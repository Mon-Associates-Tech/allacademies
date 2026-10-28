#!/usr/bin/env bash
#
# setup-local-server.sh
# Provisions an Ubuntu 26 LTS LAN server to serve the AHPC Laravel app at
# http://ahpc.internal  (plain HTTP — no certificates, no HTTPS).
#
# Run with root privileges:  sudo bash setup-local-server.sh
#
# What it does:
#   1. System update + base packages (including openssh-server)
#   2. Creates a non-root 'deploy' user (sudo + www-data group)
#   3. Installs & configures MariaDB (app DB + user on BOTH localhost and 127.0.0.1)
#   4. Installs & configures Redis (localhost-only, password protected)
#   5. Ensures PHP-FPM + required extensions (uses existing PHP if present)
#   6. Clones the 'ahpc' git branch into /home/deploy/sites/ahpc
#   7. Configures Nginx to serve the app over HTTP on port 80
#   8. Configures Supervisor queue workers
#   9. Configures UFW firewall
#  10. Writes credentials to /root/ahpc-credentials.txt and prints next steps
#
# Safe to re-run: most steps check for existing state before acting.

set -euo pipefail

# ============================================================
# CONFIG — edit if needed, then run
# ============================================================
APP_NAME="ahpc"
DOMAIN="ahpc.internal"

DEPLOY_USER="deploy"
NEW_PROJECT_DIR="/home/${DEPLOY_USER}/sites/${APP_NAME}"

# Git repository to clone, and the branch to check out.
# Leave REPO_URL empty to be prompted for it at run time.
# Private repos: use a token URL (https://<token>@github.com/org/repo.git)
# or set up a deploy key for the deploy user first.
REPO_URL=""
REPO_BRANCH="ahpc"

# Fallback: if the project already sits on this machine (and no REPO_URL is
# given), it is moved from here into NEW_PROJECT_DIR instead of cloned.
OLD_PROJECT_DIR="/root/sites/${APP_NAME}"

DB_NAME="${APP_NAME}"
DB_USER="${APP_NAME}_app"
DB_PASS="$(openssl rand -base64 24 | tr -d '/+=' | cut -c1-24)"

REDIS_PASS="$(openssl rand -base64 24 | tr -d '/+=' | cut -c1-24)"

QUEUE_WORKER_PROCESSES=2

# Optional: restrict web access to your LAN, e.g. "192.168.1.0/24". Empty = allow all.
LAN_SUBNET=""

CREDS_FILE="/root/${APP_NAME}-credentials.txt"

# ============================================================
# HELPERS
# ============================================================
log()  { echo -e "\n\033[1;32m==>\033[0m \033[1m$1\033[0m"; }
warn() { echo -e "\033[1;33m[warn]\033[0m $1"; }
die()  { echo -e "\033[1;31m[error]\033[0m $1"; exit 1; }

[[ $EUID -eq 0 ]] || die "This script must be run as root (use sudo)."

LAN_IP="$(ip -4 route get 1.1.1.1 2>/dev/null | awk '{for(i=1;i<=NF;i++) if($i=="src") print $(i+1)}' | head -1 || true)"
[[ -n "$LAN_IP" ]] || LAN_IP="$(hostname -I | awk '{print $1}')"
[[ -n "$LAN_IP" ]] || die "Could not detect this server's LAN IP address."
log "Detected LAN IP: ${LAN_IP}"

# ============================================================
# 1. BASE SYSTEM UPDATE
# ============================================================
log "Updating system packages"
export DEBIAN_FRONTEND=noninteractive
apt-get update -y
apt-get upgrade -y

log "Installing base utilities"
apt-get install -y \
    curl wget git unzip zip rsync openssl \
    ca-certificates gnupg lsb-release apt-transport-https \
    ufw supervisor cron openssh-server

# The UFW 'OpenSSH' profile only exists once openssh-server is installed.
# Enabling it here also keeps remote access working after the firewall is turned on.
systemctl enable --now ssh

# ============================================================
# 2. CREATE DEPLOY USER
# ============================================================
log "Setting up deploy user"
if id "$DEPLOY_USER" &>/dev/null; then
    warn "User '$DEPLOY_USER' already exists, skipping creation."
else
    adduser --disabled-password --gecos "" "$DEPLOY_USER"
    usermod -aG sudo "$DEPLOY_USER"
    echo "${DEPLOY_USER} ALL=(ALL) NOPASSWD:ALL" > "/etc/sudoers.d/90-${DEPLOY_USER}"
    chmod 440 "/etc/sudoers.d/90-${DEPLOY_USER}"
fi
usermod -aG www-data "$DEPLOY_USER"

if [[ -f /root/.ssh/authorized_keys ]]; then
    mkdir -p "/home/${DEPLOY_USER}/.ssh"
    cp /root/.ssh/authorized_keys "/home/${DEPLOY_USER}/.ssh/authorized_keys"
    chown -R "${DEPLOY_USER}:${DEPLOY_USER}" "/home/${DEPLOY_USER}/.ssh"
    chmod 700 "/home/${DEPLOY_USER}/.ssh"
    chmod 600 "/home/${DEPLOY_USER}/.ssh/authorized_keys"
fi

# ============================================================
# 3. NGINX + MARIADB
# ============================================================
log "Installing Nginx"
apt-get install -y nginx
systemctl enable nginx

log "Installing MariaDB"
apt-get install -y mariadb-server mariadb-client
systemctl enable mariadb
systemctl start mariadb

log "Hardening MariaDB"
mysql -u root <<'SQL'
DELETE FROM mysql.global_priv WHERE User='';
DELETE FROM mysql.global_priv WHERE User='root' AND Host NOT IN ('localhost', '127.0.0.1', '::1');
DROP DATABASE IF EXISTS test;
DELETE FROM mysql.db WHERE Db='test' OR Db='test\\_%';
FLUSH PRIVILEGES;
SQL

log "Creating application database and user (localhost + 127.0.0.1)"
# MariaDB reverse-resolves 127.0.0.1 to 'localhost' but treats the two host
# patterns as separate accounts, so both get the same password and grants.
mysql -u root <<SQL
CREATE DATABASE IF NOT EXISTS \`${DB_NAME}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE USER IF NOT EXISTS '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASS}';
ALTER USER '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASS}';
GRANT ALL PRIVILEGES ON \`${DB_NAME}\`.* TO '${DB_USER}'@'localhost';

CREATE USER IF NOT EXISTS '${DB_USER}'@'127.0.0.1' IDENTIFIED BY '${DB_PASS}';
ALTER USER '${DB_USER}'@'127.0.0.1' IDENTIFIED BY '${DB_PASS}';
GRANT ALL PRIVILEGES ON \`${DB_NAME}\`.* TO '${DB_USER}'@'127.0.0.1';

FLUSH PRIVILEGES;
SQL

# ============================================================
# 4. REDIS
# ============================================================
log "Installing Redis"
apt-get install -y redis-server

REDIS_CONF="/etc/redis/redis.conf"
if [[ -f "$REDIS_CONF" ]]; then
    sed -i 's/^supervised .*/supervised systemd/' "$REDIS_CONF"
    if grep -q '^bind ' "$REDIS_CONF"; then
        sed -i 's/^bind .*/bind 127.0.0.1 -::1/' "$REDIS_CONF"
    else
        echo "bind 127.0.0.1 -::1" >> "$REDIS_CONF"
    fi
    if grep -q '^requirepass' "$REDIS_CONF"; then
        sed -i "s/^requirepass .*/requirepass ${REDIS_PASS}/" "$REDIS_CONF"
    else
        echo "requirepass ${REDIS_PASS}" >> "$REDIS_CONF"
    fi
fi
systemctl enable redis-server
systemctl restart redis-server

# ============================================================
# 5. PHP — use existing install, otherwise install from Ubuntu's repos
# ============================================================
if ! command -v php >/dev/null 2>&1; then
    warn "PHP not found — installing Ubuntu's default PHP packages."
    apt-get install -y php-cli php-fpm
fi
PHP_VER="$(php -r 'echo PHP_MAJOR_VERSION.".".PHP_MINOR_VERSION;')"
log "Detected PHP ${PHP_VER}"

PHP_FPM_SERVICE="php${PHP_VER}-fpm"
PHP_FPM_SOCK="/run/php/php${PHP_VER}-fpm.sock"

log "Installing required PHP extensions for PHP ${PHP_VER}"
apt-get install -y \
    "php${PHP_VER}-fpm" "php${PHP_VER}-cli" "php${PHP_VER}-common" \
    "php${PHP_VER}-mysql" "php${PHP_VER}-redis" "php${PHP_VER}-mbstring" \
    "php${PHP_VER}-xml" "php${PHP_VER}-curl" "php${PHP_VER}-zip" \
    "php${PHP_VER}-gd" "php${PHP_VER}-bcmath" "php${PHP_VER}-intl" \
    || warn "Some PHP packages may not exist under these names for PHP ${PHP_VER} — verify with 'php -m'."

systemctl enable "$PHP_FPM_SERVICE" || warn "Could not enable ${PHP_FPM_SERVICE}."
systemctl restart "$PHP_FPM_SERVICE" || true

if ! command -v composer >/dev/null 2>&1; then
    log "Installing Composer"
    EXPECTED_SIG="$(curl -s https://composer.github.io/installer.sig)"
    curl -sS https://getcomposer.org/installer -o /tmp/composer-setup.php
    ACTUAL_SIG="$(php -r "echo hash_file('sha384', '/tmp/composer-setup.php');")"
    if [[ "$EXPECTED_SIG" == "$ACTUAL_SIG" ]]; then
        php /tmp/composer-setup.php --install-dir=/usr/local/bin --filename=composer
        rm /tmp/composer-setup.php
    else
        warn "Composer installer signature mismatch — install manually."
    fi
fi

# ============================================================
# 6. PROJECT FILES — clone the 'ahpc' branch
# ============================================================
log "Placing project files"
mkdir -p "/home/${DEPLOY_USER}/sites"
chown "${DEPLOY_USER}:${DEPLOY_USER}" "/home/${DEPLOY_USER}/sites"

if [[ -d "$NEW_PROJECT_DIR" ]]; then
    warn "${NEW_PROJECT_DIR} already exists — not cloning again."
    if [[ -d "${NEW_PROJECT_DIR}/.git" ]]; then
        CURRENT_BRANCH="$(sudo -u "$DEPLOY_USER" git -C "$NEW_PROJECT_DIR" rev-parse --abbrev-ref HEAD 2>/dev/null || true)"
        if [[ "$CURRENT_BRANCH" != "$REPO_BRANCH" ]]; then
            warn "Existing checkout is on branch '${CURRENT_BRANCH}', not '${REPO_BRANCH}'."
            warn "Switch manually if needed: cd ${NEW_PROJECT_DIR} && git checkout ${REPO_BRANCH}"
        fi
    fi
else
    if [[ -z "$REPO_URL" && ! -d "$OLD_PROJECT_DIR" && -t 0 ]]; then
        read -rp "Git repository URL to clone (branch '${REPO_BRANCH}'): " REPO_URL
    fi

    if [[ -n "$REPO_URL" ]]; then
        log "Cloning branch '${REPO_BRANCH}' from ${REPO_URL}"
        sudo -u "$DEPLOY_USER" git clone --branch "$REPO_BRANCH" "$REPO_URL" "$NEW_PROJECT_DIR"
    elif [[ -d "$OLD_PROJECT_DIR" ]]; then
        warn "No REPO_URL given — moving existing project from ${OLD_PROJECT_DIR}."
        rsync -a --remove-source-files "$OLD_PROJECT_DIR"/ "$NEW_PROJECT_DIR"/
        find "$OLD_PROJECT_DIR" -type d -empty -delete || true
    else
        die "No project found. Set REPO_URL at the top of this script (branch '${REPO_BRANCH}') and re-run."
    fi
fi

# The permission fixes below change file modes; stop git from reporting those as edits.
if [[ -d "${NEW_PROJECT_DIR}/.git" ]]; then
    sudo -u "$DEPLOY_USER" git -C "$NEW_PROJECT_DIR" config core.fileMode false
fi

log "Fixing ownership and permissions"
# Ubuntu homes are mode 750; www-data needs traverse (x) on /home/deploy to reach the app.
chmod o+x "/home/${DEPLOY_USER}"
chown -R "${DEPLOY_USER}:www-data" "$NEW_PROJECT_DIR"
find "$NEW_PROJECT_DIR" -type d -exec chmod 755 {} \;
find "$NEW_PROJECT_DIR" -type f -exec chmod 644 {} \;
for d in storage bootstrap/cache; do
    if [[ -d "${NEW_PROJECT_DIR}/${d}" ]]; then
        chmod -R 775 "${NEW_PROJECT_DIR}/${d}"
        find "${NEW_PROJECT_DIR}/${d}" -type d -exec chmod g+s {} \;
    fi
done
mkdir -p "${NEW_PROJECT_DIR}/storage/logs"
chown -R "${DEPLOY_USER}:www-data" "${NEW_PROJECT_DIR}/storage/logs"

# Resolve the domain on this machine too
grep -q "[[:space:]]${DOMAIN}\$" /etc/hosts || echo "127.0.0.1 ${DOMAIN}" >> /etc/hosts

# ============================================================
# 7. NGINX SERVER BLOCK (HTTP only)
# ============================================================
log "Writing Nginx config for ${DOMAIN}"
NGINX_SITE="/etc/nginx/sites-available/${DOMAIN}"

cat > "$NGINX_SITE" <<NGINXCONF
server {
    listen 80 default_server;
    listen [::]:80 default_server;
    server_name ${DOMAIN} ${LAN_IP};

    root ${NEW_PROJECT_DIR}/public;
    index index.php index.html;

    client_max_body_size 25M;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location ~ \.php\$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:${PHP_FPM_SOCK};
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    location ~* \.(?:css|js|jpg|jpeg|png|gif|ico|svg|woff|woff2|ttf)\$ {
        expires 30d;
        access_log off;
        add_header Cache-Control "public";
    }

    access_log /var/log/nginx/${DOMAIN}-access.log;
    error_log  /var/log/nginx/${DOMAIN}-error.log;
}
NGINXCONF

ln -sf "$NGINX_SITE" "/etc/nginx/sites-enabled/${DOMAIN}"
rm -f /etc/nginx/sites-enabled/default

nginx -t
systemctl reload nginx

# ============================================================
# 8. SUPERVISOR — QUEUE WORKER
# ============================================================
log "Writing Supervisor config for queue worker"
SUPERVISOR_CONF="/etc/supervisor/conf.d/${APP_NAME}-worker.conf"

cat > "$SUPERVISOR_CONF" <<SUPCONF
[program:${APP_NAME}-worker]
process_name=%(program_name)s_%(process_num)02d
command=php ${NEW_PROJECT_DIR}/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
directory=${NEW_PROJECT_DIR}
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=${DEPLOY_USER}
numprocs=${QUEUE_WORKER_PROCESSES}
redirect_stderr=true
stdout_logfile=${NEW_PROJECT_DIR}/storage/logs/worker.log
stopwaitsecs=3600
SUPCONF

supervisorctl reread
supervisorctl update
# Workers started before .env/vendor exist will fail until the manual steps below are done.
supervisorctl restart "${APP_NAME}-worker:*" || true

# ============================================================
# 9. FIREWALL
# ============================================================
log "Configuring UFW firewall"
ufw allow OpenSSH
if [[ -n "$LAN_SUBNET" ]]; then
    ufw allow from "$LAN_SUBNET" to any port 80 proto tcp
else
    ufw allow 80/tcp
fi
ufw --force enable

# ============================================================
# 10. SUMMARY
# ============================================================
log "Writing credentials to ${CREDS_FILE} (root-only readable)"
cat > "$CREDS_FILE" <<EOF
AHPC local server provisioning summary
Generated: $(date)

Deploy user:      ${DEPLOY_USER} (passwordless sudo, member of www-data)
Project path:     ${NEW_PROJECT_DIR}  (branch: ${REPO_BRANCH})
LAN IP:           ${LAN_IP}
URL:              http://${DOMAIN}

Database name:    ${DB_NAME}
Database user:    ${DB_USER}
Database pass:    ${DB_PASS}

Redis password:   ${REDIS_PASS}

Add to ${NEW_PROJECT_DIR}/.env (values unquoted):
  APP_URL=http://${DOMAIN}
  SESSION_SECURE_COOKIE=false

  DB_CONNECTION=mysql
  DB_HOST=127.0.0.1
  DB_PORT=3306
  DB_DATABASE=${DB_NAME}
  DB_USERNAME=${DB_USER}
  DB_PASSWORD=${DB_PASS}

  REDIS_HOST=127.0.0.1
  REDIS_PASSWORD=${REDIS_PASS}
  REDIS_PORT=6379
  CACHE_STORE=redis
  SESSION_DRIVER=redis
  QUEUE_CONNECTION=redis
EOF
chmod 600 "$CREDS_FILE"

log "DONE"
cat <<SUMMARY

────────────────────────────────────────────────────────
  Provisioning complete for ${DOMAIN} (HTTP, branch '${REPO_BRANCH}')
────────────────────────────────────────────────────────
  Credentials saved to: ${CREDS_FILE} (root only)

  Server steps (run as ${DEPLOY_USER}, never with sudo):
    1. su - ${DEPLOY_USER}
    2. cd ${NEW_PROJECT_DIR}
    3. cp .env.example .env   (if not present), then paste values from ${CREDS_FILE}
    4. composer install --no-dev --optimize-autoloader
    5. php artisan key:generate
    6. php artisan migrate --force      (or import your SQL dump first)
    7. php artisan storage:link
    8. npm ci && npm run build          (if you have a frontend build)
    9. php artisan config:clear && php artisan config:cache
   10. sudo supervisorctl restart ${APP_NAME}-worker:*

  Because the site is served over plain HTTP, make sure the app does not
  force HTTPS (e.g. URL::forceScheme('https') in AppServiceProvider, or a
  production-only HTTPS redirect), and that SESSION_SECURE_COOKIE is not
  true — otherwise logins will fail.

  Client device step (every PC/phone that will open the site):
    Make ${DOMAIN} resolve to ${LAN_IP}, either:
      - add this line to the hosts file:  ${LAN_IP}  ${DOMAIN}
        (Windows: C:\\Windows\\System32\\drivers\\etc\\hosts, as Administrator;
         macOS/Linux: /etc/hosts)
      - or add a DNS record on your router / local DNS server

  Then open: http://${DOMAIN}   (or http://${LAN_IP})
────────────────────────────────────────────────────────
SUMMARY
