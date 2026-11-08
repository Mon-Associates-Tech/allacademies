#!/usr/bin/env bash
# ==============================================================================
#  AHPC — Laravel Deployment (Ubuntu Server)
#
#  What this script does:
#    • Stops Apache so Nginx can use port 80
#    • Configures Nginx + PHP-FPM for the Laravel app
#    • Sets folder permissions
#    • Sets up the Laravel environment (key, caches)
#    • Sets the machine hostname to 'ahpc'
#    • Installs Avahi so other LAN devices reach the app at http://ahpc.local
#      with zero client configuration (mDNS — same as how printers advertise)
#
#  What this script does NOT do:
#    • Touch any IP addresses, WiFi, or LAN settings
#    • Install or configure a DHCP server
#    • Modify resolv.conf or systemd-resolved
#    • Break internet access in any way
#
#  Run:  sudo bash deploy-ahpc-ubuntu.sh
# ==============================================================================

set -uo pipefail
IFS=$'\n\t'

# ── Config ─────────────────────────────────────────────────────────────────────
APP_DIR="/var/www/html/ahpc"
APP_HOSTNAME="ahpc"
WEB_USER="www-data"
NGINX_AVAILABLE="/etc/nginx/sites-available/ahpc"
NGINX_ENABLED="/etc/nginx/sites-enabled/ahpc"

# ── Colours ────────────────────────────────────────────────────────────────────
RED='\033[0;31m'; GREEN='\033[0;32m'; YELLOW='\033[1;33m'
BLUE='\033[0;34m'; CYAN='\033[0;36m'; BOLD='\033[1m'; NC='\033[0m'

info()    { echo -e "${BLUE}[INFO]${NC}   $*"; }
ok()      { echo -e "${GREEN}[OK]${NC}     $*"; }
warn()    { echo -e "${YELLOW}[WARN]${NC}   $*"; }
die()     { echo -e "${RED}[FATAL]${NC}  $*" >&2; exit 1; }
section() { echo -e "\n${BOLD}${CYAN}┌─ $* ${NC}"; }

# ==============================================================================
#  0. PRE-FLIGHT
# ==============================================================================
section "Pre-flight checks"

[[ $EUID -ne 0 ]]           && die "Run as root: sudo bash $0"
[[ -d "$APP_DIR" ]]         || die "Project not found: $APP_DIR"
[[ -f "$APP_DIR/artisan" ]] || die "Laravel artisan missing — is the repo fully cloned?"
command -v nginx &>/dev/null || die "nginx not installed: apt install nginx"
command -v php   &>/dev/null || die "php not installed"

ok "Pre-flight passed"

# ==============================================================================
#  1. PHP-FPM — detect version and socket
# ==============================================================================
section "Detecting PHP-FPM"

PHP_VER=$(php -r 'echo PHP_MAJOR_VERSION.".".PHP_MINOR_VERSION;') \
    || die "Cannot determine PHP version"
info "PHP version: $PHP_VER"

FPM_SERVICE="php${PHP_VER}-fpm"

if ! systemctl is-active --quiet "$FPM_SERVICE" 2>/dev/null; then
    info "Starting $FPM_SERVICE..."
    systemctl enable --now "$FPM_SERVICE" \
        || die "$FPM_SERVICE failed to start — check: systemctl status $FPM_SERVICE"
    sleep 1
fi

FPM_SOCK=""
for candidate in \
    "/run/php/php${PHP_VER}-fpm.sock" \
    "/var/run/php/php${PHP_VER}-fpm.sock" \
    "/run/php-fpm/www.sock"; do
    [[ -S "$candidate" ]] && FPM_SOCK="$candidate" && break
done

[[ -n "$FPM_SOCK" ]] \
    || die "PHP-FPM socket not found. Check: systemctl status $FPM_SERVICE"

ok "PHP $PHP_VER — socket: $FPM_SOCK"

# ==============================================================================
#  2. APACHE — stop and disable (frees port 80 for Nginx)
# ==============================================================================
section "Stopping Apache"

if systemctl is-active --quiet apache2 2>/dev/null; then
    systemctl stop apache2 && ok "apache2 stopped"
fi
if systemctl is-enabled --quiet apache2 2>/dev/null; then
    systemctl disable apache2 && ok "apache2 disabled on boot"
fi

# ==============================================================================
#  3. NGINX — write site configuration
# ==============================================================================
section "Writing Nginx site config → $NGINX_AVAILABLE"

cat > "$NGINX_AVAILABLE" << NGINXEOF
##
##  AHPC – Laravel Application
##  /etc/nginx/sites-available/ahpc
##

server {
    listen 80;
    listen [::]:80;

    server_name ${APP_HOSTNAME}.local ${APP_HOSTNAME}.internal ${APP_HOSTNAME} _;

    root ${APP_DIR}/public;
    index index.php index.html;
    charset utf-8;

    # ── Logging ──────────────────────────────────────────────────────────────
    access_log /var/log/nginx/ahpc.access.log;
    error_log  /var/log/nginx/ahpc.error.log warn;

    # ── Upload size ───────────────────────────────────────────────────────────
    client_max_body_size 64M;

    # ── Laravel front-controller ──────────────────────────────────────────────
    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    # ── Static assets — long-lived cache ──────────────────────────────────────
    location ~* \.(css|js|jpg|jpeg|png|gif|ico|svg|webp|woff|woff2|ttf|eot|otf|map)\$ {
        expires    30d;
        access_log off;
        add_header Cache-Control "public, immutable";
        try_files  \$uri =404;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    # ── PHP-FPM ───────────────────────────────────────────────────────────────
    location ~ \.php\$ {
        try_files       \$uri =404;
        fastcgi_pass    unix:${FPM_SOCK};
        fastcgi_index   index.php;
        fastcgi_param   SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include         fastcgi_params;
        fastcgi_hide_header X-Powered-By;

        # Generous timeouts for exam sessions and PDF generation
        fastcgi_read_timeout      300;
        fastcgi_send_timeout      300;
        fastcgi_connect_timeout    60;
        fastcgi_buffer_size       128k;
        fastcgi_buffers           8 256k;
        fastcgi_busy_buffers_size 256k;
    }

    # ── Block hidden files ────────────────────────────────────────────────────
    location ~ /\.(?!well-known).* {
        deny all;
    }

    # ── Block direct access to sensitive Laravel files ─────────────────────────
    location ~* (\.env|composer\.(json|lock)|artisan|package\.json|webpack\.mix\.js)\$ {
        deny all;
        return 404;
    }

    # ── Gzip ─────────────────────────────────────────────────────────────────
    gzip            on;
    gzip_comp_level 5;
    gzip_min_length 256;
    gzip_proxied    any;
    gzip_types
        text/plain text/css text/xml text/javascript
        application/javascript application/json application/xml
        image/svg+xml font/opentype application/x-font-ttf;
}
NGINXEOF

ok "Nginx config written"

# ==============================================================================
#  4. NGINX — enable site, remove default
# ==============================================================================
section "Enabling Nginx site"

[[ -L "$NGINX_ENABLED" ]] && rm -f "$NGINX_ENABLED"
ln -s "$NGINX_AVAILABLE" "$NGINX_ENABLED"
ok "Symlinked → $NGINX_ENABLED"

if [[ -L /etc/nginx/sites-enabled/default ]]; then
    rm -f /etc/nginx/sites-enabled/default
    info "Default Nginx site removed"
fi

# ==============================================================================
#  5. FOLDER PERMISSIONS
# ==============================================================================
section "Setting folder permissions"

chown -R "${WEB_USER}:${WEB_USER}" "$APP_DIR"
find "$APP_DIR" -type f -exec chmod 644 {} \;
find "$APP_DIR" -type d -exec chmod 755 {} \;
chmod -R 775 "$APP_DIR/storage"
chmod -R 775 "$APP_DIR/bootstrap/cache"
chmod    +x  "$APP_DIR/artisan"

ok "Files 644, dirs 755, storage/ + bootstrap/cache/ 775, owner $WEB_USER"

# ==============================================================================
#  6. LARAVEL — .env
# ==============================================================================
section "Laravel .env"

if [[ ! -f "$APP_DIR/.env" ]]; then
    if [[ -f "$APP_DIR/.env.example" ]]; then
        cp "$APP_DIR/.env.example" "$APP_DIR/.env"
        chown "${WEB_USER}:${WEB_USER}" "$APP_DIR/.env"
        chmod 640 "$APP_DIR/.env"
        warn ".env created from .env.example — edit DB credentials before first use"
    else
        warn "No .env.example found — create $APP_DIR/.env manually"
    fi
else
    ok ".env already exists"
fi

if [[ -f "$APP_DIR/.env" ]]; then
    # Set APP_URL to the mDNS hostname
    if grep -qE '^APP_URL=http://localhost' "$APP_DIR/.env" 2>/dev/null \
    || grep -qE '^APP_URL=$'               "$APP_DIR/.env" 2>/dev/null; then
        sed -i "s|^APP_URL=.*|APP_URL=http://${APP_HOSTNAME}.local|" "$APP_DIR/.env"
        info "APP_URL → http://${APP_HOSTNAME}.local"
    fi

    # Generate APP_KEY if missing
    APP_KEY_VAL=$(grep '^APP_KEY=' "$APP_DIR/.env" | cut -d= -f2 | tr -d '[:space:]')
    if [[ -z "$APP_KEY_VAL" ]]; then
        sudo -u "${WEB_USER}" bash -c "cd '$APP_DIR' && php artisan key:generate --force"
        ok "APP_KEY generated"
    else
        info "APP_KEY already set"
    fi
fi

# ==============================================================================
#  7. COMPOSER
# ==============================================================================
section "Composer dependencies"

if command -v composer &>/dev/null; then
    if [[ ! -d "$APP_DIR/vendor" ]]; then
        info "Running composer install..."
        sudo -u "${WEB_USER}" composer install \
            --no-dev --optimize-autoloader --no-interaction \
            --working-dir="$APP_DIR"
        ok "composer install done"
    else
        info "vendor/ already present — skipping (run manually if needed)"
    fi
else
    warn "composer not in PATH — install dependencies manually"
fi

# ==============================================================================
#  8. LARAVEL CACHES
# ==============================================================================
section "Laravel optimisation"

cd "$APP_DIR"
sudo -u "${WEB_USER}" php artisan config:cache \
    && ok "config:cache" || warn "config:cache failed — check .env / DB settings"
sudo -u "${WEB_USER}" php artisan route:cache \
    && ok "route:cache"  || warn "route:cache failed"
sudo -u "${WEB_USER}" php artisan view:cache \
    && ok "view:cache"   || warn "view:cache failed"

# ==============================================================================
#  9. HOSTNAME
# ==============================================================================
section "Setting hostname"

CURRENT=$(hostname)
if [[ "$CURRENT" != "$APP_HOSTNAME" ]]; then
    hostnamectl set-hostname "$APP_HOSTNAME"
    ok "Hostname: $CURRENT → $APP_HOSTNAME"
else
    info "Hostname already '$APP_HOSTNAME'"
fi

# Keep /etc/hosts clean
sed -i "/[[:space:]]${APP_HOSTNAME}$/d"       /etc/hosts 2>/dev/null || true
sed -i "/[[:space:]]${APP_HOSTNAME}\./d"      /etc/hosts 2>/dev/null || true
echo "127.0.1.1   ${APP_HOSTNAME}.local ${APP_HOSTNAME}" >> /etc/hosts
ok "/etc/hosts updated"

# ==============================================================================
#  10. AVAHI — mDNS so LAN devices reach http://ahpc.local automatically
#
#  This is the only "network" change the script makes.
#  Avahi announces this machine's hostname over mDNS (port 5353/udp multicast).
#  No client configuration needed — works like printer discovery.
#  Internet access is completely unaffected.
# ==============================================================================
section "Avahi mDNS (ahpc.local)"

if ! command -v avahi-daemon &>/dev/null; then
    info "Installing avahi-daemon..."
    apt-get update -qq
    apt-get install -y avahi-daemon avahi-utils libnss-mdns
fi

# Make sure local processes also resolve .local via mDNS
if ! grep -q 'mdns4_minimal' /etc/nsswitch.conf 2>/dev/null; then
    sed -i \
        's/^hosts:.*/hosts:          files mdns4_minimal [NOTFOUND=return] dns/' \
        /etc/nsswitch.conf
    info "nsswitch.conf updated for mDNS"
fi

cat > /etc/avahi/avahi-daemon.conf << AVAHIEOF
[server]
host-name=${APP_HOSTNAME}
domain-name=local
use-ipv4=yes
use-ipv6=no
ratelimit-interval-usec=1000000
ratelimit-burst=1000

[wide-area]
enable-wide-area=no

[publish]
publish-addresses=yes
publish-hinfo=yes
publish-workstation=yes
publish-domain=yes
AVAHIEOF

systemctl enable --now avahi-daemon
systemctl restart avahi-daemon
ok "avahi-daemon running — ${APP_HOSTNAME}.local is now visible on the LAN"

# ==============================================================================
#  11. FIREWALL
# ==============================================================================
section "Firewall"

if command -v ufw &>/dev/null && ufw status | grep -q 'Status: active'; then
    ufw allow 80/tcp     comment 'AHPC HTTP'  2>/dev/null
    ufw allow 5353/udp   comment 'AHPC mDNS'  2>/dev/null
    ok "UFW: allowed HTTP (80) and mDNS (5353)"
else
    info "UFW not active — skipping"
fi

# ==============================================================================
#  12. NGINX — test and reload
# ==============================================================================
section "Testing and reloading Nginx"

nginx -t 2>&1 | sed 's/^/  /' || die "Nginx config test failed — fix $NGINX_AVAILABLE"

systemctl enable --now nginx
systemctl reload nginx
ok "Nginx reloaded"

# PHP-FPM should still be running — confirm
systemctl is-active --quiet "$FPM_SERVICE" \
    || { systemctl restart "$FPM_SERVICE" && ok "$FPM_SERVICE restarted"; }

# ==============================================================================
#  13. SMOKE TEST
# ==============================================================================
section "Smoke test"

sleep 1
HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" \
    -H "Host: ${APP_HOSTNAME}.local" http://127.0.0.1/ 2>/dev/null || echo "000")

case "$HTTP_CODE" in
    200|302) ok   "HTTP $HTTP_CODE — app is responding" ;;
    500)     warn "HTTP 500 — app reachable but erroring (check .env and DB)" ;;
    *)       warn "HTTP $HTTP_CODE — check: tail -f /var/log/nginx/ahpc.error.log" ;;
esac

# ==============================================================================
#  14. SUMMARY
# ==============================================================================
SERVER_IP=$(ip route get 1.1.1.1 2>/dev/null \
    | awk '{for(i=1;i<=NF;i++) if($i=="src") print $(i+1)}' | head -1)
SERVER_IP="${SERVER_IP:-<server-ip>}"

echo ""
echo -e "${BOLD}${GREEN}╔════════════════════════════════════════════════════════════╗"
echo -e "║        AHPC Deployment Complete (Ubuntu) ✓                ║"
echo -e "╚════════════════════════════════════════════════════════════╝${NC}"
echo ""
echo -e "  ${BOLD}App:${NC}      $APP_DIR"
echo -e "  ${BOLD}PHP:${NC}      $PHP_VER  │  FPM socket: $FPM_SOCK"
echo ""
echo -e "  ${BOLD}${CYAN}How to access the app from any device on the same network${NC}"
echo -e "  ┌───────────────────────────────────────────────────────────┐"
echo -e "  │  http://ahpc.local        ← recommended (zero config)    │"
echo -e "  │  http://${SERVER_IP}      ← by IP (always works)         │"
echo -e "  └───────────────────────────────────────────────────────────┘"
echo ""
echo -e "  ${BOLD}ahpc.local works on:${NC}"
echo -e "    macOS / iOS      — built-in Bonjour, works immediately"
echo -e "    Windows 10/11    — built-in mDNS, works immediately"
echo -e "    Linux            — needs avahi-daemon + libnss-mdns installed"
echo -e "    Android          — use the IP address instead"
echo ""
echo -e "  ${BOLD}Internet access:${NC} unchanged — WiFi and LAN work as before"
echo -e "  ${BOLD}Git:${NC}            git fetch/pull from GitHub works normally"
echo ""
echo -e "  ${BOLD}${CYAN}Next steps${NC}"
echo -e "  ─────────────────────────────────────────────────────────────"
echo -e "  1. Edit .env:"
echo -e "     nano $APP_DIR/.env"
echo -e "     Set: DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD"
echo -e "          APP_ENV=production"
echo -e "          APP_DEBUG=false"
echo ""
echo -e "  2. Run migrations:"
echo -e "     cd $APP_DIR"
echo -e "     sudo -u www-data php artisan migrate --force"
echo ""
echo -e "  3. After every git pull:"
echo -e "     sudo -u www-data php artisan optimize"
echo ""
echo -e "  ${BOLD}Logs${NC}"
echo -e "     tail -f /var/log/nginx/ahpc.error.log"
echo -e "     tail -f /var/log/nginx/ahpc.access.log"
echo -e "     journalctl -u php${PHP_VER}-fpm -f"
echo ""
