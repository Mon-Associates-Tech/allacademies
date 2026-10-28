#!/usr/bin/env bash
#
# setup-local-server.sh
# Provisions an Ubuntu 26 LTS LAN server to serve the AHPC Laravel app at
# https://ahpc.internal  (no public domain, no Let's Encrypt).
#
# Run as root:  bash setup-local-server.sh
#
# What it does:
#   1. System update + base packages
#   2. Creates a non-root 'deploy' user (sudo + www-data group)
#   3. Installs & configures MariaDB (app DB + user on BOTH localhost and 127.0.0.1)
#   4. Installs & configures Redis (localhost-only, password protected)
#   5. Ensures PHP-FPM + required extensions (uses existing PHP if present)
#   6. Moves (or clones) the project into /home/deploy/sites/ahpc
#   7. Creates a local Certificate Authority + server cert for ahpc.internal
#   8. Configures Nginx (HTTP -> HTTPS redirect, Laravel on 443)
#   9. Configures Supervisor queue workers
#  10. Configures UFW firewall
#  11. Writes credentials to /root/ahpc-credentials.txt and prints client setup steps
#
# Safe to re-run: most steps check for existing state before acting.
#
# NOTE ON SSL: Let's Encrypt/certbot cannot issue certificates for private
# names like *.internal (it must validate ownership over the public internet).
# This script creates a private CA instead. Install ca.crt once on each client
# device and browsers will trust https://ahpc.internal with no warnings.

set -euo pipefail

# ============================================================
# CONFIG — edit if needed, then run
# ============================================================
APP_NAME="ahpc"
DOMAIN="ahpc.internal"

DEPLOY_USER="deploy"
OLD_PROJECT_DIR="/root/sites/${APP_NAME}"
NEW_PROJECT_DIR="/home/${DEPLOY_USER}/sites/${APP_NAME}"

# If the project is not already on this machine, set a git URL to clone it
# (cloned as the deploy user; private repos need a deploy key or token URL).
REPO_URL=""

DB_NAME="${APP_NAME}"
DB_USER="${APP_NAME}_app"
DB_PASS="$(openssl rand -base64 24 | tr -d '/+=' | cut -c1-24)"

REDIS_PASS="$(openssl rand -base64 24 | tr -d '/+=' | cut -c1-24)"

QUEUE_WORKER_PROCESSES=2

# Optional: restrict web access to your LAN, e.g. "192.168.1.0/24". Empty = allow all.
LAN_SUBNET=""

SSL_DIR="/etc/ssl/${APP_NAME}"
CREDS_FILE="/root/${APP_NAME}-credentials.txt"

# ============================================================
# HELPERS
# ============================================================
log()  { echo -e "\n\033[1;32m==>\033[0m \033[1m$1\033[0m"; }
warn() { echo -e "\033[1;33m[warn]\033[0m $1"; }
die()  { echo -e "\033[1;31m[error]\033[0m $1"; exit 1; }

[[ $EUID -eq 0 ]] || die "This script must be run as root."

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
# 6. PROJECT FILES
# ============================================================
log "Placing project files"
mkdir -p "/home/${DEPLOY_USER}/sites"
chown "${DEPLOY_USER}:${DEPLOY_USER}" "/home/${DEPLOY_USER}/sites"

if [[ -d "$NEW_PROJECT_DIR" ]]; then
    warn "${NEW_PROJECT_DIR} already exists — skipping move, will just fix permissions."
elif [[ -d "$OLD_PROJECT_DIR" ]]; then
    rsync -a --remove-source-files "$OLD_PROJECT_DIR"/ "$NEW_PROJECT_DIR"/
    find "$OLD_PROJECT_DIR" -type d -empty -delete || true
    log "Moved project from ${OLD_PROJECT_DIR} to ${NEW_PROJECT_DIR}"
elif [[ -n "$REPO_URL" ]]; then
    sudo -u "$DEPLOY_USER" git clone "$REPO_URL" "$NEW_PROJECT_DIR"
else
    die "Project not found at ${NEW_PROJECT_DIR} or ${OLD_PROJECT_DIR}, and REPO_URL is empty. Set one and re-run."
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

# ============================================================
# 7. LOCAL CERTIFICATE AUTHORITY + SERVER CERT
# ============================================================
log "Setting up local CA and certificate for ${DOMAIN}"
mkdir -p "$SSL_DIR"
chmod 755 "$SSL_DIR"

if [[ ! -f "${SSL_DIR}/ca.key" || ! -f "${SSL_DIR}/ca.crt" ]]; then
    openssl genrsa -out "${SSL_DIR}/ca.key" 4096
    openssl req -x509 -new -nodes -sha256 -days 3650 \
        -key "${SSL_DIR}/ca.key" \
        -subj "/O=AHPC/CN=AHPC Local CA" \
        -out "${SSL_DIR}/ca.crt"
    chmod 600 "${SSL_DIR}/ca.key"
    log "Created local CA (valid 10 years)"
else
    warn "Local CA already exists, reusing it."
fi

# Regenerate the server cert if missing, expiring within 30 days, or the LAN IP changed
NEED_CERT=1
if [[ -f "${SSL_DIR}/server.crt" ]] \
   && openssl x509 -checkend 2592000 -noout -in "${SSL_DIR}/server.crt" >/dev/null \
   && openssl x509 -noout -ext subjectAltName -in "${SSL_DIR}/server.crt" | grep -q "${LAN_IP}"; then
    NEED_CERT=0
fi

if [[ $NEED_CERT -eq 1 ]]; then
    openssl genrsa -out "${SSL_DIR}/server.key" 2048
    openssl req -new -key "${SSL_DIR}/server.key" \
        -subj "/O=AHPC/CN=${DOMAIN}" \
        -out "${SSL_DIR}/server.csr"
    cat > "${SSL_DIR}/server.ext" <<EXT
authorityKeyIdentifier=keyid,issuer
basicConstraints=CA:FALSE
keyUsage=digitalSignature,keyEncipherment
extendedKeyUsage=serverAuth
subjectAltName=DNS:${DOMAIN},IP:${LAN_IP}
EXT
    openssl x509 -req -in "${SSL_DIR}/server.csr" \
        -CA "${SSL_DIR}/ca.crt" -CAkey "${SSL_DIR}/ca.key" -CAcreateserial \
        -out "${SSL_DIR}/server.crt" -days 825 -sha256 \
        -extfile "${SSL_DIR}/server.ext"
    chmod 600 "${SSL_DIR}/server.key"
    rm -f "${SSL_DIR}/server.csr"
    log "Issued server certificate (valid 825 days) for ${DOMAIN} and ${LAN_IP}"
else
    warn "Server certificate is still valid, keeping it."
fi

# Resolve the domain on this machine too
grep -q "[[:space:]]${DOMAIN}\$" /etc/hosts || echo "127.0.0.1 ${DOMAIN}" >> /etc/hosts

# ============================================================
# 8. NGINX SERVER BLOCKS
# ============================================================
log "Writing Nginx config for ${DOMAIN}"
NGINX_SITE="/etc/nginx/sites-available/${DOMAIN}"

cat > "$NGINX_SITE" <<NGINXCONF
# HTTP: serve the CA certificate for easy client install, redirect everything else to HTTPS
server {
    listen 80;
    listen [::]:80;
    server_name ${DOMAIN} ${LAN_IP};

    location = /${APP_NAME}-local-ca.crt {
        alias ${SSL_DIR}/ca.crt;
        default_type application/x-x509-ca-cert;
    }

    location / {
        return 301 https://\$host\$request_uri;
    }
}

server {
    listen 443 ssl;
    listen [::]:443 ssl;
    http2 on;
    server_name ${DOMAIN} ${LAN_IP};

    ssl_certificate     ${SSL_DIR}/server.crt;
    ssl_certificate_key ${SSL_DIR}/server.key;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_session_cache shared:SSL:10m;
    ssl_session_timeout 1d;

    root ${NEW_PROJECT_DIR}/public;
    index index.php index.html;

    client_max_body_size 25M;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    location = /${APP_NAME}-local-ca.crt {
        alias ${SSL_DIR}/ca.crt;
        default_type application/x-x509-ca-cert;
    }

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
# 9. SUPERVISOR — QUEUE WORKER
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
# 10. FIREWALL
# ============================================================
log "Configuring UFW firewall"
ufw allow OpenSSH
if [[ -n "$LAN_SUBNET" ]]; then
    ufw allow from "$LAN_SUBNET" to any port 80 proto tcp
    ufw allow from "$LAN_SUBNET" to any port 443 proto tcp
else
    ufw allow 'Nginx Full'
fi
ufw --force enable

# ============================================================
# 11. SUMMARY
# ============================================================
log "Writing credentials to ${CREDS_FILE} (root-only readable)"
cat > "$CREDS_FILE" <<EOF
AHPC local server provisioning summary
Generated: $(date)

Deploy user:      ${DEPLOY_USER} (passwordless sudo, member of www-data)
Project path:     ${NEW_PROJECT_DIR}
LAN IP:           ${LAN_IP}
URL:              https://${DOMAIN}

Database name:    ${DB_NAME}
Database user:    ${DB_USER}
Database pass:    ${DB_PASS}

Redis password:   ${REDIS_PASS}

CA certificate:   ${SSL_DIR}/ca.crt   (install on client devices)
CA download URL:  http://${DOMAIN}/${APP_NAME}-local-ca.crt

Add to ${NEW_PROJECT_DIR}/.env (values unquoted):
  APP_URL=https://${DOMAIN}

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
  Provisioning complete for ${DOMAIN}
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

  Client device steps (every PC/phone that will open the site):
    A. Make ${DOMAIN} resolve to ${LAN_IP}, either:
         - add this line to the hosts file:  ${LAN_IP}  ${DOMAIN}
           (Windows: C:\\Windows\\System32\\drivers\\etc\\hosts, as Administrator;
            macOS/Linux: /etc/hosts)
         - or add a DNS record on your router / local DNS server
    B. Trust the local CA so the browser shows no warning:
         download http://${DOMAIN}/${APP_NAME}-local-ca.crt and install it as a
         Trusted Root Certification Authority (Windows: double-click > Install
         Certificate > Local Machine > Trusted Root; Android/iOS: install as a
         CA certificate from settings).

  Then open: https://${DOMAIN}
────────────────────────────────────────────────────────
SUMMARY
