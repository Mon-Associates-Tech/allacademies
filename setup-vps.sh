#!/usr/bin/env bash
#
# setup-vps.sh
# Provisions a fresh Ubuntu 26 LTS VPS to serve the AllAcademies Laravel app.
#
# Run as root:  bash setup-vps.sh
#
# What it does:
#   1. System update + base packages
#   2. Creates a non-root 'deploy' user (sudo + www-data group)
#   3. Installs & configures MariaDB (creates app DB + user)
#   4. Installs & configures Redis (localhost-only, password protected)
#   5. Installs missing PHP extensions for the already-installed PHP version
#   6. Moves the project from /root/sites/allacademies -> /home/deploy/sites/allacademies
#   7. Configures Nginx server block for the domain
#   8. Installs Supervisor config for the Laravel queue worker
#   9. Issues a Let's Encrypt SSL cert via certbot (nginx plugin)
#  10. Configures UFW firewall
#  11. Prints a summary + writes credentials to /root/allacademies-credentials.txt
#
# Safe to re-run: most steps check for existing state before acting.

set -euo pipefail

# ============================================================
# CONFIG — edit if needed, then run
# ============================================================
DOMAIN="allacademies.com"
WWW_DOMAIN="www.allacademies.com"
CERTBOT_EMAIL="promisepaps@gmail.com"

DEPLOY_USER="deploy"
OLD_PROJECT_DIR="/root/sites/allacademies"
NEW_PROJECT_DIR="/home/${DEPLOY_USER}/sites/allacademies"

DB_NAME="allacademies"
DB_USER="allacademies_app"
DB_PASS="$(openssl rand -base64 24 | tr -d '/+=' | cut -c1-24)"

REDIS_PASS="$(openssl rand -base64 24 | tr -d '/+=' | cut -c1-24)"

QUEUE_WORKER_PROCESSES=2

CREDS_FILE="/root/allacademies-credentials.txt"

# ============================================================
# HELPERS
# ============================================================
log()  { echo -e "\n\033[1;32m==>\033[0m \033[1m$1\033[0m"; }
warn() { echo -e "\033[1;33m[warn]\033[0m $1"; }
die()  { echo -e "\033[1;31m[error]\033[0m $1"; exit 1; }

[[ $EUID -eq 0 ]] || die "This script must be run as root."

# ============================================================
# 1. BASE SYSTEM UPDATE
# ============================================================
log "Updating system packages"
export DEBIAN_FRONTEND=noninteractive
apt-get update -y
apt-get upgrade -y

log "Installing base utilities"
apt-get install -y \
    curl wget git unzip zip software-properties-common \
    ca-certificates gnupg lsb-release apt-transport-https \
    ufw supervisor cron

# ============================================================
# 2. CREATE DEPLOY USER
# ============================================================
log "Setting up deploy user"
if id "$DEPLOY_USER" &>/dev/null; then
    warn "User '$DEPLOY_USER' already exists, skipping creation."
else
    adduser --disabled-password --gecos "" "$DEPLOY_USER"
    usermod -aG sudo "$DEPLOY_USER"
    # Allow the deploy user to sudo without a password for convenience (optional — remove if undesired)
    echo "${DEPLOY_USER} ALL=(ALL) NOPASSWD:ALL" > "/etc/sudoers.d/90-${DEPLOY_USER}"
    chmod 440 "/etc/sudoers.d/90-${DEPLOY_USER}"
    log "Created user '$DEPLOY_USER' with passwordless sudo."
fi

# Copy root's SSH keys to the new user so you can still log in the same way
if [[ -f /root/.ssh/authorized_keys ]]; then
    mkdir -p "/home/${DEPLOY_USER}/.ssh"
    cp /root/.ssh/authorized_keys "/home/${DEPLOY_USER}/.ssh/authorized_keys"
    chown -R "${DEPLOY_USER}:${DEPLOY_USER}" "/home/${DEPLOY_USER}/.ssh"
    chmod 700 "/home/${DEPLOY_USER}/.ssh"
    chmod 600 "/home/${DEPLOY_USER}/.ssh/authorized_keys"
    log "Copied root's SSH authorized_keys to ${DEPLOY_USER}."
fi

# ============================================================
# 3. NGINX
# ============================================================
log "Installing Nginx"
apt-get install -y nginx
systemctl enable nginx

# ============================================================
# 4. MARIADB
# ============================================================
log "Installing MariaDB"
apt-get install -y mariadb-server mariadb-client
systemctl enable mariadb
systemctl start mariadb

log "Hardening MariaDB (equivalent of mysql_secure_installation)"
mysql -u root <<'SQL'
DELETE FROM mysql.global_priv WHERE User='';
DELETE FROM mysql.global_priv WHERE User='root' AND Host NOT IN ('localhost', '127.0.0.1', '::1');
DROP DATABASE IF EXISTS test;
DELETE FROM mysql.db WHERE Db='test' OR Db='test\\_%';
FLUSH PRIVILEGES;
SQL

log "Creating application database and user"
mysql -u root <<SQL
CREATE DATABASE IF NOT EXISTS \`${DB_NAME}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASS}';
ALTER USER '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASS}';
GRANT ALL PRIVILEGES ON \`${DB_NAME}\`.* TO '${DB_USER}'@'localhost';
FLUSH PRIVILEGES;
SQL

# ============================================================
# 5. REDIS
# ============================================================
log "Installing Redis"
apt-get install -y redis-server

REDIS_CONF="/etc/redis/redis.conf"
if [[ -f "$REDIS_CONF" ]]; then
    sed -i 's/^supervised .*/supervised systemd/' "$REDIS_CONF"
    sed -i 's/^# *bind .*/bind 127.0.0.1 -::1/' "$REDIS_CONF"
    sed -i 's/^bind .*/bind 127.0.0.1 -::1/' "$REDIS_CONF"
    if grep -q '^requirepass' "$REDIS_CONF"; then
        sed -i "s/^requirepass .*/requirepass ${REDIS_PASS}/" "$REDIS_CONF"
    else
        echo "requirepass ${REDIS_PASS}" >> "$REDIS_CONF"
    fi
fi

systemctl enable redis-server
systemctl restart redis-server

# ============================================================
# 6. PHP — detect installed version, add missing extensions
# ============================================================
log "Detecting installed PHP version"
command -v php >/dev/null 2>&1 || die "PHP is not installed or not on PATH. Install PHP first."
PHP_VER="$(php -r 'echo PHP_MAJOR_VERSION.".".PHP_MINOR_VERSION;')"
log "Detected PHP ${PHP_VER}"

PHP_FPM_SERVICE="php${PHP_VER}-fpm"
PHP_FPM_SOCK="/run/php/php${PHP_VER}-fpm.sock"

log "Installing required PHP extensions for PHP ${PHP_VER}"
apt-get install -y \
    "php${PHP_VER}-fpm" \
    "php${PHP_VER}-mysql" \
    "php${PHP_VER}-redis" \
    "php${PHP_VER}-mbstring" \
    "php${PHP_VER}-xml" \
    "php${PHP_VER}-curl" \
    "php${PHP_VER}-zip" \
    "php${PHP_VER}-gd" \
    "php${PHP_VER}-bcmath" \
    "php${PHP_VER}-intl" \
    "php${PHP_VER}-cli" \
    "php${PHP_VER}-common" \
    || warn "Some PHP extension packages may not exist under this name for PHP ${PHP_VER} — verify manually with 'php -m'."

systemctl enable "$PHP_FPM_SERVICE" || warn "Could not enable ${PHP_FPM_SERVICE}, check the correct service name with 'systemctl list-units | grep php'."

# Set PUPPETEER_CACHE_DIR in PHP-FPM environment so BrowserShot can find Chromium
# PHP-FPM runs as www-data, which has a different home dir than the user who installed Puppeteer
PHP_FPM_CONF="/etc/php/${PHP_VER}/fpm/php-fpm.conf"
PHP_FPM_POOL_CONF="/etc/php/${PHP_VER}/fpm/pool.d/www.conf"
if [[ -f "$PHP_FPM_POOL_CONF" ]]; then
    # Remove any existing PUPPETEER_CACHE_DIR setting
    sed -i '/^env\[PUPPETEER_CACHE_DIR\]/d' "$PHP_FPM_POOL_CONF"
    echo 'env[PUPPETEER_CACHE_DIR] = /usr/local/share/puppeteer' >> "$PHP_FPM_POOL_CONF"
    log "PUPPETEER_CACHE_DIR set in PHP-FPM pool config"
    
    # Increase PHP upload limits for large file uploads (PDF books, audio, video)
    sed -i 's/^upload_max_filesize = .*/upload_max_filesize = 100M/' "$PHP_FPM_CONF"
    sed -i 's/^post_max_size = .*/post_max_size = 110M/' "$PHP_FPM_CONF"
    sed -i 's/^max_execution_time = .*/max_execution_time = 300/' "$PHP_FPM_CONF"
    sed -i 's/^max_input_time = .*/max_input_time = 300/' "$PHP_FPM_CONF"
    log "PHP upload limits increased to 100MB"
fi

systemctl restart "$PHP_FPM_SERVICE" || true

# Composer
if ! command -v composer >/dev/null 2>&1; then
    log "Installing Composer"
    EXPECTED_SIG="$(curl -s https://composer.github.io/installer.sig)"
    curl -sS https://getcomposer.org/installer -o /tmp/composer-setup.php
    ACTUAL_SIG="$(php -r "echo hash_file('sha384', '/tmp/composer-setup.php');")"
    if [[ "$EXPECTED_SIG" == "$ACTUAL_SIG" ]]; then
        php /tmp/composer-setup.php --install-dir=/usr/local/bin --filename=composer
        rm /tmp/composer-setup.php
    else
        warn "Composer installer signature mismatch — skipped install, install manually."
    fi
else
    warn "Composer already installed, skipping."
fi

# ============================================================
# 7. NODE.JS & PUPPETEER — required by Spatie Browsershot for PDF generation
# ============================================================
log "Installing Node.js, Puppeteer, and Chromium dependencies"
apt-get install -y nodejs npm \
    libatk1.0-0 \
    libatk-bridge2.0-0 \
    libcups2 \
    libdrm2 \
    libgbm1 \
    libgtk-3-0 \
    libnspr4 \
    libnss3 \
    libx11-xcb1 \
    libxcomposite1 \
    libxdamage1 \
    libxrandr2 \
    libxshmfence1 \
    libpango-1.0-0 \
    libcairo2 \
    libasound2t64 \
    libxss1 \
    libglib2.0-0 \
    libdbus-1-3

# Set a system-wide Puppeteer cache directory so all users (root, deploy, www-data)
# share the same Chromium installation. PHP-FPM runs as www-data, so without this,
# Puppeteer would look in /var/www/.cache/puppeteer and not find the browser.
export PUPPETEER_CACHE_DIR=/usr/local/share/puppeteer
mkdir -p "$PUPPETEER_CACHE_DIR"

# Install Puppeteer globally (downloads Chromium to PUPPETEER_CACHE_DIR)
npm install -g puppeteer || warn "Puppeteer install failed — PDF generation may not work"

# Install chrome-headless-shell explicitly to the shared cache directory
npx puppeteer browsers install chrome-headless-shell || warn "chrome-headless-shell install failed"

# Make the cache directory world-readable so www-data can access it
chmod -R a+rX "$PUPPETEER_CACHE_DIR"

log "Node.js: $(node --version), npm: $(npm --version)"
log "Puppeteer cache: $PUPPETEER_CACHE_DIR"

# ============================================================
# 8. MOVE PROJECT OUT OF /root INTO DEPLOY USER'S HOME
# ============================================================
log "Moving project directory"
if [[ -d "$NEW_PROJECT_DIR" ]]; then
    warn "Target directory ${NEW_PROJECT_DIR} already exists — skipping move, will just fix permissions."
elif [[ -d "$OLD_PROJECT_DIR" ]]; then
    mkdir -p "/home/${DEPLOY_USER}/sites"
    rsync -a --remove-source-files "$OLD_PROJECT_DIR"/ "$NEW_PROJECT_DIR"/
    find "$OLD_PROJECT_DIR" -type d -empty -delete || true
    log "Moved project from ${OLD_PROJECT_DIR} to ${NEW_PROJECT_DIR}"
else
    die "Neither ${NEW_PROJECT_DIR} nor ${OLD_PROJECT_DIR} exist. Nothing to serve — check the path and re-run."
fi

log "Fixing ownership and permissions"
chown -R "${DEPLOY_USER}:www-data" "$NEW_PROJECT_DIR"
find "$NEW_PROJECT_DIR" -type d -exec chmod 755 {} \;
find "$NEW_PROJECT_DIR" -type f -exec chmod 644 {} \;
if [[ -d "${NEW_PROJECT_DIR}/storage" ]]; then
    chmod -R 775 "${NEW_PROJECT_DIR}/storage"
    find "${NEW_PROJECT_DIR}/storage" -type d -exec chmod g+s {} \;
    # Ensure log files are group-writable so both the queue worker (deploy) and
    # PHP-FPM (www-data) can write to the same log files
    find "${NEW_PROJECT_DIR}/storage/logs" -type f -exec chmod 664 {} \; 2>/dev/null || true
fi
if [[ -d "${NEW_PROJECT_DIR}/bootstrap/cache" ]]; then
    chmod -R 775 "${NEW_PROJECT_DIR}/bootstrap/cache"
    find "${NEW_PROJECT_DIR}/bootstrap/cache" -type d -exec chmod g+s {} \;
fi

# ============================================================
# 9. NGINX SERVER BLOCK
# ============================================================
log "Writing Nginx server block for ${DOMAIN}"
NGINX_SITE="/etc/nginx/sites-available/${DOMAIN}"

cat > "$NGINX_SITE" <<NGINXCONF
server {
    listen 80;
    listen [::]:80;
    server_name ${DOMAIN} ${WWW_DOMAIN};

    root ${NEW_PROJECT_DIR}/public;
    index index.php index.html;

    # Allow uploads up to 100MB (for PDF books, audio, video)
    client_max_body_size 100M;

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
log "Nginx configured and reloaded"

# ============================================================
# 10. SUPERVISOR — QUEUE WORKER
# ============================================================
log "Writing Supervisor config for queue worker"
SUPERVISOR_CONF="/etc/supervisor/conf.d/allacademies-worker.conf"

cat > "$SUPERVISOR_CONF" <<SUPCONF
[program:allacademies-worker]
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
environment=PUPPETEER_CACHE_DIR="/usr/local/share/puppeteer"
SUPCONF

mkdir -p "${NEW_PROJECT_DIR}/storage/logs"
chown -R "${DEPLOY_USER}:www-data" "${NEW_PROJECT_DIR}/storage/logs"
chmod -R 775 "${NEW_PROJECT_DIR}/storage/logs"
find "${NEW_PROJECT_DIR}/storage/logs" -type f -exec chmod 664 {} \; 2>/dev/null || true

supervisorctl reread
supervisorctl update
supervisorctl restart "allacademies-worker:*" || true

# ============================================================
# 11. FIREWALL
# ============================================================
log "Configuring UFW firewall"
ufw allow OpenSSH
ufw allow 'Nginx Full'
ufw --force enable

# ============================================================
# 12. SSL — LET'S ENCRYPT VIA CERTBOT
# ============================================================
log "Installing certbot and requesting SSL certificate"
apt-get install -y certbot python3-certbot-nginx

if certbot certificates 2>/dev/null | grep -q "$DOMAIN"; then
    warn "Certificate for ${DOMAIN} already exists, skipping issuance."
else
    certbot --nginx \
        -d "$DOMAIN" -d "$WWW_DOMAIN" \
        --non-interactive --agree-tos \
        -m "$CERTBOT_EMAIL" \
        --redirect \
        || warn "Certbot failed — make sure ${DOMAIN} and ${WWW_DOMAIN} DNS A records already point at this server's IP, then run: certbot --nginx -d ${DOMAIN} -d ${WWW_DOMAIN}"
fi

systemctl enable certbot.timer 2>/dev/null || true

# ============================================================
# 13. SUMMARY
# ============================================================
log "Writing credentials to ${CREDS_FILE} (root-only readable)"
cat > "$CREDS_FILE" <<EOF
AllAcademies VPS provisioning summary
Generated: $(date)

Deploy user:      ${DEPLOY_USER} (passwordless sudo, member of www-data)
Project path:     ${NEW_PROJECT_DIR}

Database name:    ${DB_NAME}
Database user:    ${DB_USER}
Database pass:    ${DB_PASS}
DB connection:    host=127.0.0.1 port=3306

Redis host:       127.0.0.1
Redis port:       6379
Redis password:   ${REDIS_PASS}

PHP-FPM socket:   ${PHP_FPM_SOCK}
Nginx site conf:  ${NGINX_SITE}
Supervisor conf:  ${SUPERVISOR_CONF}

Add these to ${NEW_PROJECT_DIR}/.env :
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
  Project now lives at: ${NEW_PROJECT_DIR}
  Credentials saved to: ${CREDS_FILE} (root only)

  Remaining manual steps:
    1. su - ${DEPLOY_USER}
    2. cd ${NEW_PROJECT_DIR}
    3. cp .env.example .env   (if not already present)
    4. Paste in the DB/Redis values from ${CREDS_FILE}
    5. composer install --no-dev --optimize-autoloader
    6. php artisan key:generate
    7. php artisan migrate --force
    8. php artisan storage:link
    9. npm ci && npm run build   (if you have a frontend build step)
   10. php artisan config:cache && php artisan route:cache && php artisan view:cache
   11. node --version && npx puppeteer browsers install chrome-headless-shell   (if not already installed)
   12. ls -la /usr/local/share/puppeteer/   (verify Chromium is in shared cache)
   13. supervisorctl status   (confirm the queue worker is running)

  Site should now be reachable at:
    https://${DOMAIN}
    https://${WWW_DOMAIN}
────────────────────────────────────────────────────────
SUMMARY
