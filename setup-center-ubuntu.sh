#!/usr/bin/env bash
# ==============================================================================
#  AllAcademies — Center Server Setup (Ubuntu)
#
#  Purpose:
#    Configure an Ubuntu server that already has the AllAcademies project
#    cloned, so it can operate as an examination center connected to a
#    central server.
#
#  What this script does:
#    • Installs and enables the PHP sodium extension (required for Ed25519)
#    • Configures .env for center role (APP_ROLE, CENTRAL_URL, etc.)
#    • Runs all pending database migrations
#    • Clears Laravel caches
#    • Sets up optional auto-sync via cron (pull/push every 15 minutes)
#    • Verifies connectivity to the central server
#
#  What this script does NOT do:
#    • Clone the repository (project must already be present)
#    • Install PHP, nginx, or MariaDB (assumed already working)
#    • Configure nginx or PHP-FPM (assumed already configured)
#
#  Usage (interactive — prompts for missing values):
#    sudo bash setup-center-ubuntu.sh
#
#  Usage (non-interactive — all values via flags):
#    sudo bash setup-center-ubuntu.sh \
#      --central-url http://192.168.1.103 \
#      --sync-token abc123... \
#      --public-key <base64-key> \
#      --db-host 127.0.0.1 \
#      --db-name allacademies \
#      --db-user allacademies \
#      --db-password secret \
#      --app-dir /var/www/html/ahpc \
#      --web-user www-data \
#      --setup-cron
#
#  Options:
#    --central-url       Central server URL (default: http://192.168.1.103)
#    --sync-token        Center sync token from Central admin UI (required)
#    --public-key        Central Ed25519 public key, base64-encoded (required)
#    --app-version       App version for version handshake (default: 1.0.0)
#    --db-host           Database host (default: 127.0.0.1)
#    --db-name           Database name (default: allacademies)
#    --db-user           Database username (required)
#    --db-password       Database password (default: empty)
#    --db-port           Database port (default: 3306)
#    --app-dir           Project directory (default: /home/deploy/sites/ahpc)
#    --web-user          Web server user (default: deploy)
#    --pull-interval     Pull sync interval in minutes (default: 15)
#    --push-interval     Push sync interval in minutes (default: 15)
#    --setup-cron        Set up auto-sync cron jobs (default: prompt)
#    --no-cron           Skip cron setup entirely
#    --help              Show this help message
# ==============================================================================

set -euo pipefail
IFS=$'\n\t'

# ── Defaults ───────────────────────────────────────────────────────────────────
DEFAULT_CENTRAL_URL="http://192.168.1.103"
DEFAULT_APP_VERSION="1.0.0"
DEFAULT_DB_HOST="127.0.0.1"
DEFAULT_DB_NAME="allacademies"
DEFAULT_DB_PORT="3306"
DEFAULT_APP_DIR="/home/deploy/sites/ahpc"
DEFAULT_WEB_USER="deploy"
DEFAULT_PULL_INTERVAL=15
DEFAULT_PUSH_INTERVAL=15

# ── Parse command-line arguments ───────────────────────────────────────────────
CENTRAL_URL=""
CENTER_SYNC_TOKEN=""
CENTRAL_PUBLIC_KEY=""
APP_VERSION=""
DB_HOST=""
DB_DATABASE=""
DB_USERNAME=""
DB_PASSWORD=""
DB_PORT=""
APP_DIR=""
WEB_USER=""
PULL_INTERVAL=""
PUSH_INTERVAL=""
SETUP_CRON_FLAG=""
SHOW_HELP=0

while [[ $# -gt 0 ]]; do
    case "$1" in
        --central-url)   CENTRAL_URL="$2";      shift 2 ;;
        --sync-token)    CENTER_SYNC_TOKEN="$2"; shift 2 ;;
        --public-key)    CENTRAL_PUBLIC_KEY="$2"; shift 2 ;;
        --app-version)   APP_VERSION="$2";       shift 2 ;;
        --db-host)       DB_HOST="$2";           shift 2 ;;
        --db-name)       DB_DATABASE="$2";       shift 2 ;;
        --db-user)       DB_USERNAME="$2";       shift 2 ;;
        --db-password)   DB_PASSWORD="$2";       shift 2 ;;
        --db-port)       DB_PORT="$2";           shift 2 ;;
        --app-dir)       APP_DIR="$2";           shift 2 ;;
        --web-user)      WEB_USER="$2";          shift 2 ;;
        --pull-interval) PULL_INTERVAL="$2";     shift 2 ;;
        --push-interval) PUSH_INTERVAL="$2";     shift 2 ;;
        --setup-cron)    SETUP_CRON_FLAG="Y";    shift   ;;
        --no-cron)       SETUP_CRON_FLAG="N";    shift   ;;
        --dry-run)       DRY_RUN=1;              shift   ;;
        --help)          SHOW_HELP=1;            shift   ;;
        *)               echo "Unknown option: $1"; echo "Run with --help for usage."; exit 1 ;;
    esac
done
DRY_RUN="${DRY_RUN:-0}"

# Show help if requested
if [[ $SHOW_HELP -eq 1 ]]; then
    head -72 "$0" | tail -60
    echo ""
    echo "  --dry-run          Show what would be done without making changes"
    exit 0
fi

# Apply defaults for optional values
CENTRAL_URL="${CENTRAL_URL:-$DEFAULT_CENTRAL_URL}"
APP_VERSION="${APP_VERSION:-$DEFAULT_APP_VERSION}"
DB_HOST="${DB_HOST:-$DEFAULT_DB_HOST}"
DB_DATABASE="${DB_DATABASE:-$DEFAULT_DB_NAME}"
DB_PORT="${DB_PORT:-$DEFAULT_DB_PORT}"
APP_DIR="${APP_DIR:-$DEFAULT_APP_DIR}"
WEB_USER="${WEB_USER:-$DEFAULT_WEB_USER}"
PULL_INTERVAL="${PULL_INTERVAL:-$DEFAULT_PULL_INTERVAL}"
PUSH_INTERVAL="${PUSH_INTERVAL:-$DEFAULT_PUSH_INTERVAL}"

# ── Colours ────────────────────────────────────────────────────────────────────
RED='\033[0;31m'; GREEN='\033[0;32m'; YELLOW='\033[1;33m'
BLUE='\033[0;34m'; CYAN='\033[0;36m'; BOLD='\033[1m'; NC='\033[0m'

info()    { echo -e "${BLUE}[INFO]${NC}   $*"; }
ok()      { echo -e "${GREEN}[OK]${NC}     $*"; }
warn()    { echo -e "${YELLOW}[WARN]${NC}   $*"; }
die()     { echo -e "${RED}[FATAL]${NC}  $*" >&2; exit 1; }
section() { echo -e "\n${BOLD}${CYAN}┌─ $* ${NC}"; }
dry()     { echo -e "${BLUE}[DRY-RUN]${NC} $*"; }

# ── Dry-run banner ─────────────────────────────────────────────────────────────
if [[ "$DRY_RUN" -eq 1 ]]; then
    echo ""
    echo -e "${BOLD}${YELLOW}============================================================"
    echo -e "  DRY-RUN MODE — No changes will be made"
    echo -e "============================================================${NC}"
    echo ""
fi

# ==============================================================================
#  0. PRE-FLIGHT
# ==============================================================================
section "Pre-flight checks"

[[ $EUID -ne 0 ]]           && die "Run as root: sudo bash $0"
[[ -d "$APP_DIR" ]]         || die "Project not found: $APP_DIR"
[[ -f "$APP_DIR/artisan" ]] || die "Laravel artisan missing — is the repo fully cloned?"
command -v php   &>/dev/null || die "php not installed"
command -v mysql  &>/dev/null || command -v mariadb &>/dev/null \
    || die "mysql/mariadb client not installed"

ok "Pre-flight passed"

# ==============================================================================
#  1. PHP — detect version
# ==============================================================================
section "Detecting PHP"

PHP_VER=$(php -r 'echo PHP_MAJOR_VERSION.".".PHP_MINOR_VERSION;') \
    || die "Cannot determine PHP version"
info "PHP version: $PHP_VER"

# ==============================================================================
#  2. PHP SODIUM EXTENSION
# ==============================================================================
section "PHP sodium extension"

if [[ "$DRY_RUN" -eq 0 ]]; then
    if php -m 2>/dev/null | grep -qi '^sodium$'; then
        ok "Sodium extension already loaded"
    else
        info "Installing php${PHP_VER}-sodium..."
        apt-get update -qq
        apt-get install -y "php${PHP_VER}-sodium" \
            || {
                apt-get install -y php-sodium \
                    || die "Failed to install sodium extension. Try: apt install php${PHP_VER}-sodium"
            }

        PHP_CONF_DIR=$(php --ini 2>/dev/null | grep 'Scan for additional .ini files' | awk '{print $NF}')
        if [[ -n "$PHP_CONF_DIR" ]] && [[ -d "$PHP_CONF_DIR" ]]; then
            if ! ls "$PHP_CONF_DIR"/*sodium*.ini &>/dev/null; then
                echo "extension=sodium" | tee "$PHP_CONF_DIR/30-sodium.ini" >/dev/null
                info "Created $PHP_CONF_DIR/30-sodium.ini"
            fi
        fi

        FPM_SERVICE="php${PHP_VER}-fpm"
        if systemctl is-active --quiet "$FPM_SERVICE" 2>/dev/null; then
            systemctl restart "$FPM_SERVICE"
            ok "$FPM_SERVICE restarted"
        fi

        if php -m 2>/dev/null | grep -qi '^sodium$'; then
            ok "Sodium extension loaded"
        else
            warn "Sodium extension may not be loaded. Check: php -m | grep sodium"
            warn "You may need to restart PHP-FPM manually: systemctl restart $FPM_SERVICE"
        fi
    fi
else
    dry "Install php${PHP_VER}-sodium extension"
    dry "Restart PHP-FPM service"
fi

# ==============================================================================
#  3. PROMPT FOR SYNC VALUES (only if not provided via flags)
# ==============================================================================
section "Sync configuration"

# Central URL — prompt if not provided via --central-url
if [[ -z "$CENTRAL_URL" ]]; then
    read -rp "  Central server URL (default: $DEFAULT_CENTRAL_URL): " CENTRAL_URL
    CENTRAL_URL="${CENTRAL_URL:-$DEFAULT_CENTRAL_URL}"
fi
# Remove trailing slash
CENTRAL_URL="${CENTRAL_URL%/}"
info "CENTRAL_URL: $CENTRAL_URL"

# Sync token — required, prompt if not provided via --sync-token
if [[ -z "$CENTER_SYNC_TOKEN" ]]; then
    read -rp "  Center sync token (from Central admin UI): " CENTER_SYNC_TOKEN
fi
[[ -n "$CENTER_SYNC_TOKEN" ]] || die "Sync token cannot be empty (use --sync-token or enter it above)"
info "CENTER_SYNC_TOKEN: ${CENTER_SYNC_TOKEN:0:10}..."

# Central public key — required, prompt if not provided via --public-key
if [[ -z "$CENTRAL_PUBLIC_KEY" ]]; then
    read -rp "  Central public key (Ed25519, base64): " CENTRAL_PUBLIC_KEY
fi
[[ -n "$CENTRAL_PUBLIC_KEY" ]] || die "Central public key cannot be empty (use --public-key or enter it above)"
info "CENTRAL_PUBLIC_KEY: ${CENTRAL_PUBLIC_KEY:0:20}..."

# App version — prompt if not provided via --app-version
if [[ -z "$APP_VERSION" ]]; then
    read -rp "  App version (default: $DEFAULT_APP_VERSION): " APP_VERSION
    APP_VERSION="${APP_VERSION:-$DEFAULT_APP_VERSION}"
fi
info "APP_VERSION: $APP_VERSION"

# Sync intervals — use values from flags or defaults
info "PULL_INTERVAL: ${PULL_INTERVAL} minutes"
info "PUSH_INTERVAL: ${PUSH_INTERVAL} minutes"

# ==============================================================================
#  4. DATABASE CREDENTIALS (only prompt if not provided via flags)
# ==============================================================================
section "Database credentials"

# Database host — prompt if not provided via --db-host
if [[ -z "$DB_HOST" ]]; then
    read -rp "  Database host (default: $DEFAULT_DB_HOST): " DB_HOST
    DB_HOST="${DB_HOST:-$DEFAULT_DB_HOST}"
fi

# Database name — prompt if not provided via --db-name
if [[ -z "$DB_DATABASE" ]]; then
    read -rp "  Database name (default: $DEFAULT_DB_NAME): " DB_DATABASE
    DB_DATABASE="${DB_DATABASE:-$DEFAULT_DB_NAME}"
fi

# Database port — prompt if not provided via --db-port
if [[ -z "$DB_PORT" ]]; then
    read -rp "  Database port (default: $DEFAULT_DB_PORT): " DB_PORT
    DB_PORT="${DB_PORT:-$DEFAULT_DB_PORT}"
fi

# Database username — required, prompt if not provided via --db-user
if [[ -z "$DB_USERNAME" ]]; then
    read -rp "  Database username: " DB_USERNAME
fi
[[ -n "$DB_USERNAME" ]] || die "Database username cannot be empty (use --db-user or enter it above)"

# Database password — optional, prompt if not provided via --db-password
if [[ -z "$DB_PASSWORD" ]]; then
    read -rp "  Database password (leave empty if none): " DB_PASSWORD
fi

info "DB_HOST: $DB_HOST, DB_DATABASE: $DB_DATABASE, DB_PORT: $DB_PORT, DB_USERNAME: $DB_USERNAME"

# ==============================================================================
#  5. LARAVEL — .env
# ==============================================================================
section "Configuring .env"

if [[ "$DRY_RUN" -eq 0 ]]; then
    if [[ ! -f "$APP_DIR/.env" ]]; then
        if [[ -f "$APP_DIR/.env.example" ]]; then
            cp "$APP_DIR/.env.example" "$APP_DIR/.env"
            info ".env created from .env.example"
        else
            die "No .env or .env.example found in $APP_DIR"
        fi
    fi

    # ── Database settings ──────────────────────────────────────────────────────────
    sed -i "s|^DB_HOST=.*|DB_HOST=${DB_HOST}|" "$APP_DIR/.env"
    sed -i "s|^DB_DATABASE=.*|DB_DATABASE=${DB_DATABASE}|" "$APP_DIR/.env"
    sed -i "s|^DB_USERNAME=.*|DB_USERNAME=${DB_USERNAME}|" "$APP_DIR/.env"
    sed -i "s|^DB_PASSWORD=.*|DB_PASSWORD=${DB_PASSWORD}|" "$APP_DIR/.env"
    sed -i "s|^DB_PORT=.*|DB_PORT=${DB_PORT}|" "$APP_DIR/.env"
    ok "Database settings updated"

    # ── Center sync settings ──────────────────────────────────────────────────────
    sed -i '/^APP_ROLE=/d' "$APP_DIR/.env"
    sed -i '/^CENTRAL_URL=/d' "$APP_DIR/.env"
    sed -i '/^CENTER_SYNC_TOKEN=/d' "$APP_DIR/.env"
    sed -i '/^CENTRAL_PUBLIC_KEY=/d' "$APP_DIR/.env"
    sed -i '/^APP_VERSION=/d' "$APP_DIR/.env"
    sed -i '/^CENTER_ID=/d' "$APP_DIR/.env"
    sed -i '/^CENTER_PULL_INTERVAL=/d' "$APP_DIR/.env"
    sed -i '/^CENTER_PUSH_INTERVAL=/d' "$APP_DIR/.env"

    cat >> "$APP_DIR/.env" << ENVEOF

# ── Multi-Center Sync (Center Role) ──────────────────────────────────────────
APP_ROLE=center
CENTRAL_URL=${CENTRAL_URL}
CENTER_SYNC_TOKEN=${CENTER_SYNC_TOKEN}
CENTRAL_PUBLIC_KEY=${CENTRAL_PUBLIC_KEY}
APP_VERSION=${APP_VERSION}
CENTER_ID=null
CENTER_PULL_INTERVAL=${PULL_INTERVAL}
CENTER_PUSH_INTERVAL=${PUSH_INTERVAL}
ENVEOF

    ok "Center sync settings added to .env"

    # ── Production settings ───────────────────────────────────────────────────────
    sed -i "s|^APP_ENV=.*|APP_ENV=production|" "$APP_DIR/.env"
    sed -i "s|^APP_DEBUG=.*|APP_DEBUG=false|" "$APP_DIR/.env"

    APP_KEY_VAL=$(grep '^APP_KEY=' "$APP_DIR/.env" | cut -d= -f2 | tr -d '[:space:]')
    if [[ -z "$APP_KEY_VAL" ]]; then
        sudo -u "${WEB_USER}" bash -c "cd '$APP_DIR' && php artisan key:generate --force" 2>/dev/null
        ok "APP_KEY generated"
    else
        info "APP_KEY already set"
    fi

    chown "${WEB_USER}:${WEB_USER}" "$APP_DIR/.env"
    chmod 640 "$APP_DIR/.env"
else
    dry "Create/update .env with database and center sync settings"
    dry "Set APP_ENV=production, APP_DEBUG=false"
    dry "Generate APP_KEY if missing"
    dry "Set .env ownership to ${WEB_USER}:${WEB_USER}"
fi

# ==============================================================================
#  6. COMPOSER DEPENDENCIES
# ==============================================================================
section "Composer dependencies"

if [[ "$DRY_RUN" -eq 0 ]]; then
    if command -v composer &>/dev/null; then
        info "Running composer install..."
        sudo -u "${WEB_USER}" composer install \
            --no-dev --optimize-autoloader --no-interaction \
            --working-dir="$APP_DIR" 2>&1 | tail -5
        ok "composer install done"
    else
        warn "composer not in PATH — install dependencies manually"
    fi
else
    dry "Run composer install --no-dev --optimize-autoloader"
fi

# ==============================================================================
#  7. DATABASE MIGRATIONS
# ==============================================================================
section "Running database migrations"

if [[ "$DRY_RUN" -eq 0 ]]; then
    cd "$APP_DIR"
    DB_TEST=$(mysql -h "$DB_HOST" -u "$DB_USERNAME" -p"$DB_PASSWORD" -e "SELECT 1" 2>&1 || true)
    if echo "$DB_TEST" | grep -qi "access denied\|error"; then
        die "Database connection failed. Check your credentials."
    fi
    ok "Database connection successful"
    sudo -u "${WEB_USER}" php artisan migrate --force 2>&1 | tail -20
    ok "Migrations complete"
else
    dry "Test database connection (${DB_USERNAME}@${DB_HOST}/${DB_DATABASE})"
    dry "Run php artisan migrate --force"
fi

# ==============================================================================
#  8. LARAVEL CACHES
# ==============================================================================
section "Laravel caches"

if [[ "$DRY_RUN" -eq 0 ]]; then
    cd "$APP_DIR"
    sudo -u "${WEB_USER}" php artisan config:clear && ok "config:clear"
    sudo -u "${WEB_USER}" php artisan cache:clear && ok "cache:clear"
    sudo -u "${WEB_USER}" php artisan view:clear && ok "view:clear"
    sudo -u "${WEB_USER}" php artisan route:clear && ok "route:clear"
    sudo -u "${WEB_USER}" php artisan config:cache && ok "config:cache"
    sudo -u "${WEB_USER}" php artisan route:cache && ok "route:cache"
    sudo -u "${WEB_USER}" php artisan view:cache && ok "view:cache"
else
    dry "Clear and cache Laravel config, routes, and views"
fi

# ==============================================================================
#  9. FOLDER PERMISSIONS
# ==============================================================================
section "Setting folder permissions"

if [[ "$DRY_RUN" -eq 0 ]]; then
    chown -R "${WEB_USER}:${WEB_USER}" "$APP_DIR"
    find "$APP_DIR" -type f -exec chmod 644 {} \;
    find "$APP_DIR" -type d -exec chmod 755 {} \;
    chmod -R 775 "$APP_DIR/storage"
    chmod -R 775 "$APP_DIR/bootstrap/cache"
    chmod    +x  "$APP_DIR/artisan"
    ok "Permissions set (owner: $WEB_USER)"
else
    dry "Set ownership ${WEB_USER}:${WEB_USER} on $APP_DIR"
    dry "Set storage and bootstrap/cache to 775"
fi

# ==============================================================================
#  10. VERIFY SYNC COMMANDS
# ==============================================================================
section "Verifying sync commands"

if [[ "$DRY_RUN" -eq 0 ]]; then
    cd "$APP_DIR"
    if sudo -u "${WEB_USER}" php artisan list sync &>/dev/null; then
        ok "Sync commands registered"
        sudo -u "${WEB_USER}" php artisan list sync 2>&1 | grep -E 'sync:|center:' | sed 's/^/  /'
    else
        warn "Sync commands not found. Check: app/Console/Kernel.php"
    fi
else
    dry "Verify sync commands are registered"
fi

# ==============================================================================
#  11. TEST CONNECTION TO CENTRAL
# ==============================================================================
section "Testing connection to Central"

if [[ "$DRY_RUN" -eq 0 ]]; then
    echo ""
    info "Testing HTTP connection to $CENTRAL_URL..."
    HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" --connect-timeout 5 "$CENTRAL_URL" 2>/dev/null || echo "000")
    case "$HTTP_CODE" in
        200|302) ok "HTTP $HTTP_CODE — Central server is reachable" ;;
        000)     warn "Cannot reach Central server at $CENTRAL_URL"
                 warn "Check: firewall, nginx, and that the Central server is running" ;;
        *)       warn "HTTP $HTTP_CODE from Central — check nginx logs on the Central server" ;;
    esac
else
    dry "Test HTTP connection to $CENTRAL_URL"
fi

# ==============================================================================
#  12. OPTIONAL: AUTO-SYNC VIA CRON
# ==============================================================================
section "Auto-sync setup"

if [[ "$DRY_RUN" -eq 0 ]]; then
    if [[ -z "$SETUP_CRON_FLAG" ]]; then
        echo ""
        read -rp "  Set up auto-sync via cron? (pull/push every ${PULL_INTERVAL} minutes) [y/N]: " SETUP_CRON_FLAG
        SETUP_CRON_FLAG="${SETUP_CRON_FLAG:-N}"
    fi

    if [[ "$SETUP_CRON_FLAG" =~ ^[Yy]$ ]]; then
        CRON_PULL="*/${PULL_INTERVAL} * * * * cd $APP_DIR && sudo -u ${WEB_USER} php artisan sync:pull >> /var/log/allacademies-sync.log 2>&1"
        CRON_PUSH="*/${PUSH_INTERVAL} * * * * cd $APP_DIR && sudo -u ${WEB_USER} php artisan sync:push >> /var/log/allacademies-sync.log 2>&1"

        if ! crontab -u root -l 2>/dev/null | grep -q "sync:pull"; then
            (crontab -u root -l 2>/dev/null; echo "$CRON_PULL"; echo "$CRON_PUSH") | crontab -u root -
            ok "Cron jobs added: pull every ${PULL_INTERVAL} min, push every ${PUSH_INTERVAL} min"
            info "Log file: /var/log/allacademies-sync.log"
        else
            info "Cron jobs already exist — skipping"
        fi
    else
        info "Auto-sync skipped. Use manual commands:"
        info "  php artisan sync:pull --force"
        info "  php artisan sync:push --force"
    fi
else
    dry "Set up cron jobs: pull every ${PULL_INTERVAL} min, push every ${PUSH_INTERVAL} min"
fi

# ==============================================================================
#  13. QUEUE WORKER SETUP
# ==============================================================================
section "Queue worker setup"

if [[ "$DRY_RUN" -eq 0 ]]; then
    echo ""
    info "The sync push/pull buttons dispatch background jobs that require a queue worker."
    read -rp "  Set up queue worker via systemd? [y/N]: " SETUP_QUEUE
    SETUP_QUEUE="${SETUP_QUEUE:-N}"

    if [[ "$SETUP_QUEUE" =~ ^[Yy]$ ]]; then
        QUEUE_SERVICE_NAME="allacademies-queue"
        QUEUE_SERVICE_FILE="/etc/systemd/system/${QUEUE_SERVICE_NAME}.service"

        cat > "$QUEUE_SERVICE_FILE" << SVCEOF
[Unit]
Description=AllAcademies Queue Worker
After=network.target

[Service]
Type=simple
User=${WEB_USER}
WorkingDirectory=${APP_DIR}
ExecStart=/usr/bin/php ${APP_DIR}/artisan queue:work --timeout=3600 --tries=3
Restart=always
RestartSec=5
StandardOutput=append:/var/log/allacademies-queue.log
StandardError=append:/var/log/allacademies-queue.log

[Install]
WantedBy=multi-user.target
SVCEOF

        systemctl daemon-reload
        systemctl enable "$QUEUE_SERVICE_NAME"
        systemctl start "$QUEUE_SERVICE_NAME"
        ok "Queue worker service created and started: ${QUEUE_SERVICE_NAME}"
        info "Log file: /var/log/allacademies-queue.log"
    else
        info "Queue worker not set up. Start it manually:"
        info "  cd $APP_DIR && php artisan queue:work --timeout=3600 --tries=3"
    fi
else
    dry "Create systemd service for queue worker"
    dry "Enable and start allacademies-queue service"
fi

# ==============================================================================
#  14. SUMMARY
# ==============================================================================
SERVER_IP=$(ip route get 1.1.1.1 2>/dev/null \
    | awk '{for(i=1;i<=NF;i++) if($i=="src") print $(i+1)}' | head -1)
SERVER_IP="${SERVER_IP:-<server-ip>}"

echo ""
echo -e "${BOLD}${GREEN}╔════════════════════════════════════════════════════════════╗"
echo -e "║     AllAcademies Center Setup Complete ✓                  ║"
echo -e "╚════════════════════════════════════════════════════════════╝${NC}"
echo ""
echo -e "  ${BOLD}Server:${NC}    $SERVER_IP"
echo -e "  ${BOLD}App:${NC}       $APP_DIR"
echo -e "  ${BOLD}PHP:${NC}       $PHP_VER"
echo -e "  ${BOLD}Role:${NC}      Center"
echo -e "  ${BOLD}Central:${NC}   $CENTRAL_URL"
echo -e "  ${BOLD}Pull:${NC}      Every ${PULL_INTERVAL} minutes"
echo -e "  ${BOLD}Push:${NC}      Every ${PUSH_INTERVAL} minutes"
echo ""
echo -e "  ${BOLD}${CYAN}Next steps${NC}"
echo -e "  ─────────────────────────────────────────────────────────────"
echo ""
echo -e "  1. ${BOLD}Test the pull:${NC}"
echo -e "     cd $APP_DIR"
echo -e "     sudo -u ${WEB_USER} php artisan sync:pull --force"
echo ""
echo -e "  2. ${BOLD}Verify exams appeared:${NC}"
echo -e "     sudo -u ${WEB_USER} php artisan tinker"
echo -e "     >>> \\App\\ExaminationHub\\Models\\GeneralExam::count()"
echo ""
echo -e "  3. ${BOLD}Push results (after an exam):${NC}"
echo -e "     sudo -u ${WEB_USER} php artisan sync:push --force"
echo ""
echo -e "  4. ${BOLD}Check sync status:${NC}"
echo -e "     Visit: http://$SERVER_IP/center-sync"
echo ""
echo -e "  ${BOLD}Logs${NC}"
echo -e "     tail -f /var/log/allacademies-sync.log"
echo -e "     tail -f $APP_DIR/storage/logs/laravel.log"
echo ""

if [[ "$DRY_RUN" -eq 1 ]]; then
    echo -e "${BOLD}${YELLOW}============================================================"
    echo -e "  DRY-RUN COMPLETE — No changes were made to the system"
    echo -e "  Run without --dry-run to apply these changes"
    echo -e "============================================================${NC}"
    echo ""
fi
