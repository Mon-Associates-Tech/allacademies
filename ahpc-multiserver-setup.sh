#!/usr/bin/env bash
# ==============================================================================
#  AHPC Multi-Server Setup Script
#
#  Purpose:
#    Single unified script to provision a fully configured AHPC server.
#    Both Central and Center roles get the same full LEMP stack + app setup.
#    The only difference is the sync configuration in .env.
#
#  Run with root privileges:  sudo bash ahpc-multiserver-setup.sh
#
#  What it does (both roles — full provisioning):
#    1. Connects to Wi-Fi network (nmcli) if network-manager is available
#    2. System update + base packages (including openssh-server)
#    3. Creates a non-root 'deploy' user (sudo + www-data group)
#    4. Installs & configures MariaDB (app DB + user on localhost + 127.0.0.1)
#    5. Installs & configures Redis (localhost-only, password protected)
#    6. Ensures PHP-FPM + required extensions + production tuning
#    7. Installs Node.js + npm for frontend builds
#    8. Clones the 'ahpc' git branch into /var/www/html/ahpc
#    9. Writes .env with all credentials (DB, Redis, Session, Sync, APP_NAME)
#   10. Runs composer install, npm install, npm run build
#   11. Generates app key, runs migrations, creates storage link
#   12. Provisions a default admin user
#   13. Configures Nginx to serve the app (HTTP + HTTPS with Certbot)
#   14. Configures Supervisor queue workers
#   15. Sets up log rotation (Laravel, Nginx, Supervisor, sync)
#   16. Sets up Laravel scheduler cron entry
#   17. Configures UFW firewall
#   18. Writes credentials to /root/ahpc-credentials.txt and prints next steps
#
#  Role differences:
#    - Central: generates Ed25519 key pair, APP_ROLE=central
#    - Center: requires --central-url, --sync-token, --public-key, APP_ROLE=center
#
#  Safe to re-run: most steps check for existing state before acting.
# ==============================================================================

set -Eeuo pipefail
IFS=$'\n\t'
trap 'echo -e "\n\033[0;31m[FATAL]\033[0m Aborted at line ${LINENO}: ${BASH_COMMAND}" >&2' ERR
export DEBIAN_FRONTEND=noninteractive
export NEEDRESTART_MODE=a   # don't let needrestart prompt/hang during apt upgrades

# ============================================================
# SHARED DEFAULTS
# ============================================================
APP_NAME="ahpc"
DEFAULT_DOMAIN="ahpc.internal"
NEW_PROJECT_DIR="/var/www/html/${APP_NAME}"

DB_NAME="${APP_NAME}"
DB_USER="${APP_NAME}_user"

QUEUE_WORKER_PROCESSES=2

# Optional: restrict web access to your LAN, e.g. "192.168.1.0/24". Empty = allow all.
LAN_SUBNET=""

CREDS_FILE="/root/${APP_NAME}-credentials.txt"

# Wi-Fi defaults
DEFAULT_WIFI_SSID="AHGENERAL"
DEFAULT_WIFI_PASSWORD="1L0ve@M1n3!0B.&"

# Center-specific defaults
DEFAULT_CENTRAL_URL="http://192.168.1.103"
DEFAULT_APP_VERSION="1.0.0"
DEFAULT_PULL_INTERVAL=15
DEFAULT_PUSH_INTERVAL=15

# ============================================================
# COMMAND-LINE ARGUMENTS
# ============================================================
# Usage:
#   # Central server (full provisioning):
#   sudo bash ahpc-multiserver-setup.sh --app-role central
#   sudo bash ahpc-multiserver-setup.sh --app-role central --db-password secret --ip 192.168.1.50
#
#   # Center server (full provisioning + sync config):
#   sudo bash ahpc-multiserver-setup.sh --app-role center \
#     --central-url http://192.168.1.103 \
#     --sync-token abc123 \
#     --public-key <base64-key>
#
# Options:
#   --app-role           "central" or "center" — REQUIRED
#   --db-password        Database password (default: auto-generated or read from creds)
#   --redis-password     Redis password (default: auto-generated or read from creds)
#   --ip                 Server IP address (default: auto-detected)
#   --domain             Domain name (default: ahpc.internal)
#   --repo-url           Git repository URL (default: prompt if not set)
#   --repo-branch        Git branch to check out (default: ahpc)
#   --admin-email        Default admin email (default: admin@ahpc.local)
#   --admin-password     Default admin password (default: auto-generated)
#   --central-url        Central server URL (required when role=center)
#   --sync-token         Center sync token (required when role=center)
#   --public-key         Central Ed25519 public key base64 (required when role=center)
#   --app-version        App version for version handshake (default: 1.0.0)
#   --pull-interval      Pull sync interval in minutes (default: 15)
#   --push-interval      Push sync interval in minutes (default: 15)
#   --wifi-ssid          Wi-Fi network name (default: AHGENERAL)
#   --wifi-password       Wi-Fi password (default: built-in)
#   --skip-wifi           Skip Wi-Fi connection setup
#   --php-version        Force a PHP version, e.g. 8.3 (installed from ppa:ondrej/php if needed)
#   --setup-cron         Set up auto-sync cron jobs (center role)
#   --no-cron            Skip cron setup entirely (center role)
#   --dry-run            Show what would be done without making changes
#   --help               Show this help message

# ── Parse arguments ────────────────────────────────────────────────────────────
APP_ROLE="center"
DB_PASS=""
REDIS_PASS=""
LAN_IP_OVERRIDE=""
DOMAIN=""
REPO_BRANCH="ahpc"
DEFAULT_ADMIN_EMAIL=""
DEFAULT_ADMIN_PASSWORD=""
CENTRAL_URL=""
CENTER_SYNC_TOKEN=""
CENTRAL_PUBLIC_KEY=""
APP_VERSION=""
PULL_INTERVAL=""
PUSH_INTERVAL=""
SETUP_CRON_FLAG=""
SKIP_WIFI=0
DRY_RUN=0
SHOW_HELP=0
WIFI_SSID=""
WIFI_PASSWORD=""
PHP_VERSION_FORCE=""

# Load balancer defaults
LB_MODE=""
LB_CENTRAL_URL=""
LB_LOCAL_MAX=150
LB_CENTRAL_MAX=350
LB_COOKIE_HOURS=4

while [[ $# -gt 0 ]]; do
    case "$1" in
        --app-role)         APP_ROLE="$2";           shift 2 ;;
        --db-password)      DB_PASS="$2";            shift 2 ;;
        --redis-password)   REDIS_PASS="$2";         shift 2 ;;
        --ip)               LAN_IP_OVERRIDE="$2";    shift 2 ;;
        --domain)           DOMAIN="$2";             shift 2 ;;
        --repo-url)         REPO_URL="$2";           shift 2 ;;
        --repo-branch)      REPO_BRANCH="$2";        shift 2 ;;
        --admin-email)      DEFAULT_ADMIN_EMAIL="$2"; shift 2 ;;
        --admin-password)   DEFAULT_ADMIN_PASSWORD="$2"; shift 2 ;;
        --central-url)      CENTRAL_URL="$2";        shift 2 ;;
        --sync-token)       CENTER_SYNC_TOKEN="$2";  shift 2 ;;
        --public-key)       CENTRAL_PUBLIC_KEY="$2"; shift 2 ;;
        --app-version)      APP_VERSION="$2";        shift 2 ;;
        --pull-interval)    PULL_INTERVAL="$2";      shift 2 ;;
        --push-interval)    PUSH_INTERVAL="$2";      shift 2 ;;
        --setup-cron)       SETUP_CRON_FLAG="Y";     shift   ;;
        --no-cron)          SETUP_CRON_FLAG="N";     shift   ;;
        --wifi-ssid)        WIFI_SSID="$2";           shift 2 ;;
        --wifi-password)    WIFI_PASSWORD="$2";       shift 2 ;;
        --skip-wifi)        SKIP_WIFI=1;              shift   ;;
        --php-version)      PHP_VERSION_FORCE="$2";   shift 2 ;;
        --lb-mode)          LB_MODE="$2";             shift 2 ;;
        --lb-central-url)   LB_CENTRAL_URL="$2";      shift 2 ;;
        --lb-local-max)     LB_LOCAL_MAX="$2";        shift   ;;
        --lb-central-max)   LB_CENTRAL_MAX="$2";      shift   ;;
        --lb-cookie-hours)  LB_COOKIE_HOURS="$2";     shift   ;;
        --dry-run)          DRY_RUN=1;               shift   ;;
        --help)             SHOW_HELP=1;             shift   ;;
        *)                  echo "Unknown option: $1"; echo "Run with --help for usage."; exit 1 ;;
    esac
done

# ── Show help ──────────────────────────────────────────────────────────────────
if [[ $SHOW_HELP -eq 1 ]]; then
    head -44 "$0" | tail -32
    echo ""
    echo "Options:"
    echo "  --app-role           central or center — REQUIRED"
    echo "  --db-password        Database password (default: auto-generated)"
    echo "  --redis-password     Redis password (default: auto-generated)"
    echo "  --ip                 Server IP for /etc/hosts (default: auto-detected)"
    echo "  --domain             Domain name (default: ahpc.internal)"
    echo "  --repo-url           Git repository URL"
    echo "  --repo-branch        Git branch (default: ahpc)"
    echo "  --admin-email        Default admin email (default: admin@ahpc.local)"
    echo "  --admin-password     Default admin password (default: auto-generated)"
    echo "  --central-url        Central URL (required when role=center)"
    echo "  --sync-token         Center sync token (required when role=center)"
    echo "  --public-key         Central public key base64 (required when role=center)"
    echo "  --app-version        App version (default: 1.0.0)"
    echo "  --pull-interval      Pull sync interval in minutes (default: 15)"
    echo "  --push-interval      Push sync interval in minutes (default: 15)"
    echo "  --wifi-ssid          Wi-Fi network name (default: AHGENERAL)"
    echo "  --wifi-password      Wi-Fi password"
    echo "  --skip-wifi          Skip Wi-Fi connection setup"
    echo "  --php-version        Force a PHP version, e.g. 8.3 (installs from ppa:ondrej/php if needed)"
    echo "  --setup-cron         Set up auto-sync cron jobs (center role)"
    echo "  --no-cron            Skip cron setup entirely (center role)"
    echo "  --lb-mode            Load balancer mode: local-first, central-first, off (center role)"
    echo "  --lb-central-url     Central server URL for HAProxy (default: uses --central-url)"
    echo "  --lb-local-max       Max concurrent connections to local backend (default: 150)"
    echo "  --lb-central-max     Max concurrent connections to Central backend (default: 350)"
    echo "  --lb-cookie-hours    Sticky cookie max-age in hours (default: 4)"
    echo "  --dry-run            Show what would be done without making changes"
    echo "  --help               Show this help message"
    exit 0
fi

# ── Validate role ──────────────────────────────────────────────────────────────
if [[ -z "$APP_ROLE" ]]; then
    echo "Error: --app-role is required. Use 'central' or 'center'."
    echo "Run with --help for usage."
    exit 1
fi

if [[ "$APP_ROLE" != "central" && "$APP_ROLE" != "center" ]]; then
    echo "Error: --app-role must be 'central' or 'center', got '$APP_ROLE'."
    exit 1
fi

# ── Validate center-required args ──────────────────────────────────────────────
if [[ "$APP_ROLE" == "center" ]]; then
    if [[ -z "$CENTRAL_URL" ]]; then
        if [[ -t 0 ]]; then
            read -rp "  Central server URL (required for center role): " CENTRAL_URL
        else
            echo "Error: --central-url is required when role=center."
            exit 1
        fi
    fi
    if [[ -z "$CENTER_SYNC_TOKEN" ]]; then
        if [[ -t 0 ]]; then
            read -rp "  Center sync token (from Central admin UI): " CENTER_SYNC_TOKEN
        else
            echo "Error: --sync-token is required when role=center."
            exit 1
        fi
    fi
    if [[ -z "$CENTRAL_PUBLIC_KEY" ]]; then
        if [[ -t 0 ]]; then
            read -rp "  Central public key (Ed25519, base64): " CENTRAL_PUBLIC_KEY
        else
            echo "Error: --public-key is required when role=center."
            exit 1
        fi
    fi
    CENTRAL_URL="${CENTRAL_URL%/}"
    APP_VERSION="${APP_VERSION:-$DEFAULT_APP_VERSION}"
    PULL_INTERVAL="${PULL_INTERVAL:-$DEFAULT_PULL_INTERVAL}"
    PUSH_INTERVAL="${PUSH_INTERVAL:-$DEFAULT_PUSH_INTERVAL}"
fi

# ============================================================
# COLOR HELPERS
# ============================================================
RED='\033[0;31m'; GREEN='\033[0;32m'; YELLOW='\033[1;33m'
BLUE='\033[0;34m'; CYAN='\033[0;36m'; BOLD='\033[1m'; NC='\033[0m'

_log()  { echo -e "\n\033[1;32m==>\033[0m \033[1m$1\033[0m"; }
info() { echo -e "${BLUE}[INFO]${NC}   $*"; }
ok()   { echo -e "${GREEN}[OK]${NC}     $*"; }
warn() { echo -e "${YELLOW}[WARN]${NC}   $*"; }
die()  { echo -e "${RED}[FATAL]${NC}  $*" >&2; exit 1; }
section() { echo -e "\n${BOLD}${CYAN}┌─ $* ${NC}"; }
dry()  { echo -e "${BLUE}[DRY-RUN]${NC} $*"; }

# Dry-run mode: print commands instead of executing them
if [[ "$DRY_RUN" -eq 1 ]]; then
    _log "============================================================"
    _log "  DRY-RUN MODE — No changes will be made"
    _log "============================================================"
    run() { echo -e "\033[1;34m[DRY-RUN]\033[0m $*"; }
    run_sql() { echo -e "\033[1;34m[DRY-RUN SQL]\033[0m $*"; }
    run_cmd() { echo -e "\033[1;34m[DRY-RUN]\033[0m $*"; }
    write_file() { echo -e "\033[1;34m[DRY-RUN write]\033[0m $1"; }
else
    run()    { "$@"; }
    run_sql() { mysql -u root "$@"; }
    run_cmd() { "$@"; }
    write_file() { cat > "$1"; }
fi

[[ $EUID -eq 0 ]] || die "This script must be run as root (use sudo)."

# ── Helpers ────────────────────────────────────────────────────────────────────
# www-data's real home (/var/www) is not writable, so composer/npm get a scratch HOME.
WWW_HOME="/tmp/${APP_NAME}-www-home"
if [[ "$DRY_RUN" -eq 0 ]]; then
    mkdir -p "$WWW_HOME"
    chown -R www-data:www-data "$WWW_HOME"
fi

# Run a command as www-data (the PHP-FPM user). Everything artisan/composer/npm creates
# must be owned by www-data, otherwise the web app later hits "Permission denied" -> HTTP 500.
as_www() {
    sudo -u www-data -H env HOME="$WWW_HOME" COMPOSER_HOME="${WWW_HOME}/composer" \
        npm_config_cache="${WWW_HOME}/npm" "$@"
}

# set_env FILE KEY VALUE
#   Updates KEY if present, un-comments "# KEY=" if that's how .env.example ships it
#   (Laravel 11 comments out DB_HOST/DB_DATABASE/...), otherwise appends it.
set_env() {
    local file="$1" key="$2" value="$3" esc
    esc="$(printf '%s' "$value" | sed -e 's/[\\&|]/\\&/g')"
    if grep -qE "^${key}=" "$file"; then
        sed -i -E "s|^${key}=.*|${key}=${esc}|" "$file"
    elif grep -qE "^#[[:space:]]*${key}=" "$file"; then
        sed -i -E "0,/^#[[:space:]]*${key}=/{s|^#[[:space:]]*${key}=.*|${key}=${esc}|}" "$file"
    else
        if [[ -s "$file" && -n "$(tail -c1 "$file")" ]]; then echo >> "$file"; fi
        printf '%s=%s\n' "$key" "$value" >> "$file"
    fi
}

# ============================================================
# 1. WI-FI CONNECTION (nmcli)
# ============================================================
WIFI_SSID="${WIFI_SSID:-$DEFAULT_WIFI_SSID}"
WIFI_PASSWORD="${WIFI_PASSWORD:-$DEFAULT_WIFI_PASSWORD}"

if [[ "$SKIP_WIFI" -eq 1 ]]; then
    _log "Skipping Wi-Fi setup (--skip-wifi)"
else
    _log "Checking Wi-Fi connectivity"
    if [[ "$DRY_RUN" -eq 0 ]]; then
        # Install network-manager if not present
        if ! command -v nmcli >/dev/null 2>&1; then
            _log "Installing NetworkManager"
            apt-get install -y network-manager || warn "Failed to install network-manager — Wi-Fi setup will be skipped."
        fi

        if command -v nmcli >/dev/null 2>&1; then
            # Check if already connected to the target SSID
            CURRENT_SSID="$(nmcli -t -f active,ssid dev wifi | grep '^yes:' | cut -d: -f2 || true)"
            if [[ "$CURRENT_SSID" == "$WIFI_SSID" ]]; then
                ok "Already connected to Wi-Fi: ${WIFI_SSID}"
            else
                # Check if the connection profile already exists
                if nmcli connection show | grep -q "$WIFI_SSID"; then
                    _log "Connection profile for '${WIFI_SSID}' exists — activating"
                    nmcli connection up id "$WIFI_SSID" 2>&1 || warn "Could not activate connection '${WIFI_SSID}'."
                else
                    _log "Connecting to Wi-Fi: ${WIFI_SSID}"
                    nmcli device wifi connect "$WIFI_SSID" password "$WIFI_PASSWORD" 2>&1 || warn "Could not connect to Wi-Fi '${WIFI_SSID}'."
                fi

                # Wait a moment and verify connection
                sleep 3
                NEW_SSID="$(nmcli -t -f active,ssid dev wifi | grep '^yes:' | cut -d: -f2 || true)"
                if [[ "$NEW_SSID" == "$WIFI_SSID" ]]; then
                    ok "Connected to Wi-Fi: ${WIFI_SSID}"
                else
                    warn "Wi-Fi connection to '${WIFI_SSID}' may have failed — continuing anyway."
                fi
            fi
        else
            warn "nmcli not available — skipping Wi-Fi setup."
        fi
    else
        dry "Connect to Wi-Fi: ${WIFI_SSID} (password hidden)"
    fi
fi

# ============================================================
# SHARED PRE-SETUP
# ============================================================

# Apply domain default
DOMAIN="${DOMAIN:-$DEFAULT_DOMAIN}"

# Detect or use provided IP address
if [[ -n "${LAN_IP_OVERRIDE:-}" ]]; then
    LAN_IP="$LAN_IP_OVERRIDE"
    _log "Using provided IP address: ${LAN_IP}"
else
    LAN_IP="$(ip -4 route get 1.1.1.1 2>/dev/null | awk '{for(i=1;i<=NF;i++) if($i=="src") print $(i+1)}' | head -1 || true)"
    [[ -n "$LAN_IP" ]] || LAN_IP="$(hostname -I | awk '{print $1}')"
    [[ -n "$LAN_IP" ]] || die "Could not detect this server's LAN IP address. Use --ip to specify one."
    _log "Detected LAN IP: ${LAN_IP}"
fi

# Default admin credentials
DEFAULT_ADMIN_EMAIL="${DEFAULT_ADMIN_EMAIL:-admin@ahpc.local}"
DEFAULT_ADMIN_PASSWORD="${DEFAULT_ADMIN_PASSWORD:-pass1234}"

# Generate Ed25519 key pair placeholders for central role
CENTRAL_PRIVATE_KEY="${CENTRAL_PRIVATE_KEY:-}"

# ============================================================
# READ EXISTING CREDENTIALS (if available)
# ============================================================
if [[ -f "$CREDS_FILE" ]]; then
    _log "Found existing credentials file: ${CREDS_FILE}"

    EXISTING_DB_PASS="$(grep '^Database pass:' "$CREDS_FILE" 2>/dev/null | sed 's/^Database pass:[[:space:]]*//' | xargs || true)"
    EXISTING_REDIS_PASS="$(grep '^Redis password:' "$CREDS_FILE" 2>/dev/null | sed 's/^Redis password:[[:space:]]*//' | xargs || true)"

    if [[ -n "$DB_PASS" ]]; then
        _log "Using --db-password flag to replace existing database password"
    elif [[ -n "$EXISTING_DB_PASS" ]]; then
        DB_PASS="$EXISTING_DB_PASS"
        _log "Reusing existing database password from credentials file"
    fi

    if [[ -n "$REDIS_PASS" ]]; then
        _log "Using --redis-password flag to replace existing Redis password"
    elif [[ -n "$EXISTING_REDIS_PASS" ]]; then
        REDIS_PASS="$EXISTING_REDIS_PASS"
        _log "Reusing existing Redis password from credentials file"
    fi
fi

if [[ -z "$DB_PASS" ]]; then
    DB_PASS="$(openssl rand -base64 12 | tr -d '/+=' | cut -c1-8)"
    _log "Generated new database password"
fi

if [[ -z "$REDIS_PASS" ]]; then
    REDIS_PASS="$(openssl rand -base64 12 | tr -d '/+=' | cut -c1-8)"
    _log "Generated new Redis password"
fi

# ============================================================
# 2. BASE SYSTEM UPDATE
# ============================================================
_log "Updating system packages"
if [[ "$DRY_RUN" -eq 0 ]]; then
    export DEBIAN_FRONTEND=noninteractive
    apt-get update -y
    apt-get upgrade -y
else
    dry "apt-get update -y && apt-get upgrade -y"
fi

_log "Installing base utilities"
if [[ "$DRY_RUN" -eq 0 ]]; then
    apt-get install -y \
        curl wget git unzip zip rsync openssl \
        ca-certificates gnupg lsb-release apt-transport-https \
        ufw supervisor cron openssh-server
    systemctl enable --now ssh
else
    dry "apt-get install -y curl wget git unzip zip rsync openssl ca-certificates gnupg lsb-release apt-transport-https ufw supervisor cron openssh-server"
    dry "systemctl enable --now ssh"
fi

# ============================================================
# 3. ENSURE CURRENT USER IS IN www-data GROUP
# ============================================================
_log "Configuring user groups"
if [[ "$DRY_RUN" -eq 0 ]]; then
    CURRENT_USER="${SUDO_USER:-$(logname 2>/dev/null || whoami)}"
    usermod -aG www-data "$CURRENT_USER" || true
    usermod -aG "$CURRENT_USER" www-data || true
    ok "Added ${CURRENT_USER} <-> www-data to each other's groups"
else
    dry "usermod -aG www-data <current-user> && usermod -aG <current-user> www-data"
fi

# ============================================================
# 4. NGINX + MARIADB
# ============================================================
_log "Installing Nginx"
if [[ "$DRY_RUN" -eq 0 ]]; then
    apt-get install -y nginx
    systemctl enable nginx
else
    dry "apt-get install -y nginx && systemctl enable nginx"
fi

_log "Installing MariaDB"
if [[ "$DRY_RUN" -eq 0 ]]; then
    apt-get install -y mariadb-server mariadb-client
    systemctl enable mariadb
    systemctl start mariadb
else
    dry "apt-get install -y mariadb-server mariadb-client && systemctl enable mariadb"
fi

_log "Hardening MariaDB"
if [[ "$DRY_RUN" -eq 0 ]]; then
    mysql -u root <<'SQL'
DELETE FROM mysql.global_priv WHERE User='';
DELETE FROM mysql.global_priv WHERE User='root' AND Host NOT IN ('localhost', '127.0.0.1', '::1');
DROP DATABASE IF EXISTS test;
DELETE FROM mysql.db WHERE Db='test' OR Db='test\\_%';
FLUSH PRIVILEGES;
SQL
else
    dry "DELETE FROM mysql.global_priv WHERE User='';"
    dry "DROP DATABASE IF EXISTS test; FLUSH PRIVILEGES;"
fi

_log "Creating application database and user (localhost + 127.0.0.1)"
if [[ "$DRY_RUN" -eq 0 ]]; then
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
else
    dry "CREATE DATABASE \`${DB_NAME}\`; CREATE USER '${DB_USER}'@'localhost'; GRANT ALL;"
fi
_log "Database password set for ${DB_USER}@localhost and ${DB_USER}@127.0.0.1"

# ============================================================
# 5. REDIS
# ============================================================
_log "Installing Redis"
if [[ "$DRY_RUN" -eq 0 ]]; then
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
    sleep 1
    if [[ "$(REDISCLI_AUTH="$REDIS_PASS" redis-cli ping 2>/dev/null || true)" == "PONG" ]]; then
        ok "Redis is up and accepts the configured password"
    else
        die "Redis did not answer PING with the configured password (sessions/cache/queue use Redis, so the app would 500). Check requirepass in /etc/redis/redis.conf and 'systemctl status redis-server'."
    fi
else
    dry "apt-get install -y redis-server && configure redis.conf && systemctl enable redis-server"
fi

# ============================================================
# 6. PHP + COMPOSER
# ============================================================
PHP_VER=""
PHP_FPM_SERVICE=""
PHP_FPM_SOCK=""

# Modules the app needs. If one is missing the app dies at runtime with an HTTP 500,
# so we verify them right after installing instead of finding out in the browser.
REQUIRED_PHP_MODULES=(mbstring curl pdo_mysql redis xml zip gd bcmath intl sodium openssl tokenizer fileinfo ctype)

install_php_stack() {
    local ver="$1" ext pkg
    local pkgs=("php${ver}-fpm" "php${ver}-cli" "php${ver}-common")
    # NOTE: sodium is built into php-common (there is no separate php-sodium package) and
    # listing a non-existent package makes apt abort the WHOLE install, so it's not listed.
    for ext in mbstring curl mysql redis xml zip gd bcmath intl readline; do
        pkgs+=("php${ver}-${ext}")
    done

    _log "Installing PHP ${ver} and extensions"
    if ! apt-get install -y "${pkgs[@]}"; then
        warn "Bulk install failed (a package name probably doesn't exist) — retrying one by one"
        for pkg in "${pkgs[@]}"; do
            apt-get install -y "$pkg" >/dev/null 2>&1 || warn "  could not install ${pkg} (skipped)"
        done
    fi

    # Production PHP settings. Written to conf.d because the php[...] lines the old script
    # tried to sed in www.conf don't exist there (they ship as ;php_admin_value) — no-op.
    cat > "/etc/php/${ver}/fpm/conf.d/99-${APP_NAME}.ini" <<INI
; Managed by the ${APP_NAME} setup script
memory_limit = 256M
upload_max_filesize = 100M
post_max_size = 100M
INI

    local pool="/etc/php/${ver}/fpm/pool.d/www.conf"
    if [[ -f "$pool" ]]; then
        sed -i 's/^;\?pm = .*/pm = dynamic/' "$pool"
        sed -i 's/^;\?pm.max_children = .*/pm.max_children = 50/' "$pool"
        sed -i 's/^;\?pm.start_servers = .*/pm.start_servers = 5/' "$pool"
        sed -i 's/^;\?pm.min_spare_servers = .*/pm.min_spare_servers = 2/' "$pool"
        sed -i 's/^;\?pm.max_spare_servers = .*/pm.max_spare_servers = 10/' "$pool"
    else
        warn "PHP-FPM pool config not found at ${pool} — skipping pool tuning."
    fi

    systemctl enable "php${ver}-fpm" >/dev/null 2>&1 || warn "Could not enable php${ver}-fpm."
    systemctl restart "php${ver}-fpm" || warn "Could not restart php${ver}-fpm."
    ok "PHP ${ver} installed (memory=256M, uploads=100M, pm.max_children=50)"
}

verify_php_modules() {
    local mods m missing=()
    mods="$(php -m 2>/dev/null | tr 'A-Z' 'a-z')"
    for m in "${REQUIRED_PHP_MODULES[@]}"; do
        grep -qx "$m" <<<"$mods" || missing+=("$m")
    done
    if (( ${#missing[@]} > 0 )); then
        die "PHP ${PHP_VER} is missing required modules: $(printf '%s ' "${missing[@]}")— install them (apt-get install php${PHP_VER}-<name>) and re-run."
    fi
    ok "All required PHP modules are present"
}

# If composer.json needs a newer PHP than the OS ships (e.g. Ubuntu 22.04 has 8.1, Laravel 11/12 needs 8.2+),
# install it from ppa:ondrej/php. The old script passed --ignore-platform-reqs, which hid this
# and produced a half-working site (HTTP 500) instead of an error.
ensure_php_version() {
    local constraint="" want=""
    if [[ -n "$PHP_VERSION_FORCE" ]]; then
        want="$PHP_VERSION_FORCE"
    elif [[ -f "${NEW_PROJECT_DIR}/composer.json" ]]; then
        constraint="$(php -r '$j = json_decode(file_get_contents($argv[1]), true); echo $j["require"]["php"] ?? "";' "${NEW_PROJECT_DIR}/composer.json" 2>/dev/null || true)"
        [[ -n "$constraint" ]] && want="$(grep -oE '[0-9]+\.[0-9]+' <<<"$constraint" | sort -V | head -1 || true)"
    fi
    [[ -n "$want" ]] || return 0

    if [[ -n "$PHP_VERSION_FORCE" ]]; then
        [[ "$PHP_VER" == "$want" ]] && { ok "PHP ${PHP_VER} (forced via --php-version)"; return 0; }
    elif dpkg --compare-versions "$PHP_VER" ge "$want"; then
        ok "PHP ${PHP_VER} satisfies the project requirement (${constraint})"
        return 0
    fi

    warn "Switching PHP ${PHP_VER} -> ${want} (project requires '${constraint:-forced}'); using ppa:ondrej/php"
    apt-get install -y software-properties-common
    add-apt-repository -y ppa:ondrej/php
    apt-get update -y

    local old_fpm="$PHP_FPM_SERVICE"
    PHP_VER="$want"
    PHP_FPM_SERVICE="php${PHP_VER}-fpm"
    PHP_FPM_SOCK="/run/php/php${PHP_VER}-fpm.sock"
    install_php_stack "$PHP_VER"
    update-alternatives --set php "/usr/bin/php${PHP_VER}" || warn "Could not switch the 'php' CLI to ${PHP_VER}"
    if [[ -n "$old_fpm" && "$old_fpm" != "$PHP_FPM_SERVICE" ]]; then
        systemctl disable --now "$old_fpm" >/dev/null 2>&1 || true
    fi
    [[ "$(php -r 'echo PHP_MAJOR_VERSION.".".PHP_MINOR_VERSION;')" == "$PHP_VER" ]] \
        || die "The php CLI is still not ${PHP_VER} after switching — run: update-alternatives --config php"
    verify_php_modules
}

if [[ "$DRY_RUN" -eq 0 ]]; then
    if ! command -v php >/dev/null 2>&1; then
        _log "PHP not found — installing default PHP packages"
        apt-get install -y php-cli php-fpm
    fi
    PHP_VER="$(php -r 'echo PHP_MAJOR_VERSION.".".PHP_MINOR_VERSION;')"
    _log "Using PHP ${PHP_VER}"

    PHP_FPM_SERVICE="php${PHP_VER}-fpm"
    PHP_FPM_SOCK="/run/php/php${PHP_VER}-fpm.sock"

    install_php_stack "$PHP_VER"
    verify_php_modules
    sleep 1
    [[ -S "$PHP_FPM_SOCK" ]] || warn "PHP-FPM socket ${PHP_FPM_SOCK} not found — Nginx would return 502. Present sockets: $(ls /run/php 2>/dev/null | tr '\n' ' ')"

    if ! command -v composer >/dev/null 2>&1; then
        _log "Installing Composer"
        EXPECTED_SIG="$(curl -s https://composer.github.io/installer.sig)"
        curl -sS https://getcomposer.org/installer -o /tmp/composer-setup.php
        ACTUAL_SIG="$(php -r "echo hash_file('sha384', '/tmp/composer-setup.php');")"
        if [[ -n "$EXPECTED_SIG" && "$EXPECTED_SIG" == "$ACTUAL_SIG" ]]; then
            php /tmp/composer-setup.php --install-dir=/usr/local/bin --filename=composer
            rm -f /tmp/composer-setup.php
        else
            warn "Composer installer signature mismatch — install Composer manually."
        fi
    fi
else
    dry "Install PHP and extensions (php-fpm, php-cli, php-mysql, php-redis, php-mbstring, php-xml, php-curl, php-zip, php-gd, php-bcmath, php-intl)"
    dry "Verify required PHP modules; switch PHP via ppa:ondrej/php if composer.json needs a newer version"
    dry "Install Composer"
fi

# ============================================================
# 6b. Node.js + npm
# ============================================================
if [[ "$DRY_RUN" -eq 0 ]]; then
    NODE_VER=""
    NODE_OK=0
    if command -v node >/dev/null 2>&1; then
        NODE_VER="$(node -v 2>/dev/null | tr -d 'v' || true)"
        # Vite 5-7 needs Node >= 20.19 (or 22.12+). An old distro Node would silently break `npm run build`.
        if [[ -n "$NODE_VER" ]] && dpkg --compare-versions "$NODE_VER" ge 20.19.0; then NODE_OK=1; fi
    fi

    if [[ "$NODE_OK" -eq 1 ]]; then
        _log "Node.js already installed: v${NODE_VER}"
    else
        _log "Installing Node.js 22 LTS (found: ${NODE_VER:-none})"
        curl -fsSL https://deb.nodesource.com/setup_22.x -o /tmp/nodesource-setup.sh
        bash /tmp/nodesource-setup.sh
        apt-get install -y nodejs
        rm -f /tmp/nodesource-setup.sh
    fi

    if ! command -v npm >/dev/null 2>&1; then
        warn "npm not found — install manually."
    else
        _log "npm version: $(npm --version)"
    fi
else
    dry "Install Node.js 22 LTS and npm (if missing or older than 20.19)"
fi

# ============================================================
# 7. PROJECT FILES — download the 'ahpc' branch tarball
# ============================================================
_log "Placing project files"
if [[ "$DRY_RUN" -eq 0 ]]; then
    mkdir -p "/var/www/html"
    chown "www-data:www-data" "/var/www/html"

    REPO_OWNER="Mon-Associates-Tech"
    REPO_SLUG="allacademies"
    TAR_URL="https://github.com/${REPO_OWNER}/${REPO_SLUG}/archive/refs/heads/${REPO_BRANCH}.tar.gz"
    TAR_FILE="/tmp/${APP_NAME}-${REPO_BRANCH}.tar.gz"
    EXTRACT_DIR="/tmp/${APP_NAME}-extract"

    _log "Downloading branch '${REPO_BRANCH}' from GitHub"
    rm -f "$TAR_FILE"
    sudo -u www-data wget --timeout=120 --tries=3 -q -O "$TAR_FILE" "$TAR_URL" \
        || die "Failed to download ${TAR_URL} — check network connectivity (and that the repo/branch is public)."

    _log "Extracting archive"
    rm -rf "$EXTRACT_DIR"
    mkdir -p "$EXTRACT_DIR"
    tar -xzf "$TAR_FILE" -C "$EXTRACT_DIR" --strip-components=1
    rm -f "$TAR_FILE"

    if [[ -d "$NEW_PROJECT_DIR" ]]; then
        _log "${NEW_PROJECT_DIR} exists — syncing latest files (preserving .env, storage, vendor, node_modules)"
        # Excludes are anchored (/vendor/): un-anchored 'vendor/' also skipped resources/views/vendor,
        # public/vendor, lang/vendor... so published package views/assets never updated on re-runs.
        rsync -a --exclude='/.env' --exclude='/storage/' --exclude='/vendor/' --exclude='/node_modules/' \
            "${EXTRACT_DIR}/" "${NEW_PROJECT_DIR}/"
    else
        _log "Moving extracted files to ${NEW_PROJECT_DIR}"
        mv "$EXTRACT_DIR" "$NEW_PROJECT_DIR"
    fi
    rm -rf "$EXTRACT_DIR"

    _log "Fixing ownership and permissions"
    chown -R "${CURRENT_USER}:www-data" "$NEW_PROJECT_DIR"
    find "$NEW_PROJECT_DIR" \( -path "${NEW_PROJECT_DIR}/vendor" -o -path "${NEW_PROJECT_DIR}/node_modules" -o -path "${NEW_PROJECT_DIR}/storage" \) -prune -o -type d -exec chmod 755 {} +
    find "$NEW_PROJECT_DIR" \( -path "${NEW_PROJECT_DIR}/vendor" -o -path "${NEW_PROJECT_DIR}/node_modules" -o -path "${NEW_PROJECT_DIR}/storage" \) -prune -o -type f -exec chmod 644 {} +
    chmod 755 "${NEW_PROJECT_DIR}/artisan" 2>/dev/null || true

    # Ensure required storage subdirectories exist
    mkdir -p "${NEW_PROJECT_DIR}/storage/logs"
    mkdir -p "${NEW_PROJECT_DIR}/storage/app/public"
    mkdir -p "${NEW_PROJECT_DIR}/storage/framework/cache/data"
    mkdir -p "${NEW_PROJECT_DIR}/storage/framework/sessions"
    mkdir -p "${NEW_PROJECT_DIR}/storage/framework/views"
    mkdir -p "${NEW_PROJECT_DIR}/bootstrap/cache"

    # storage and bootstrap/cache must be writable by www-data
    chown -R "www-data:www-data" "${NEW_PROJECT_DIR}/storage" "${NEW_PROJECT_DIR}/bootstrap/cache"
    chmod -R 775 "${NEW_PROJECT_DIR}/storage" "${NEW_PROJECT_DIR}/bootstrap/cache"

    # Vite writes the build here as www-data. public/ itself is not writable by www-data, so without this
    # `npm run build` fails, there is no manifest.json, and every page using @vite throws -> HTTP 500.
    mkdir -p "${NEW_PROJECT_DIR}/public/build"
    chown -R "www-data:www-data" "${NEW_PROJECT_DIR}/public/build"

    # Default ACLs: anything created later in storage/ (even by root, e.g. a manual `php artisan` run)
    # stays writable by www-data. This is the Laravel-docs-recommended fix for root-owned log/cache files.
    apt-get install -y acl >/dev/null 2>&1 || true
    if command -v setfacl >/dev/null 2>&1; then
        setfacl -R  -m "u:www-data:rwX" -m "u:${CURRENT_USER}:rwX" "${NEW_PROJECT_DIR}/storage" "${NEW_PROJECT_DIR}/bootstrap/cache" 2>/dev/null \
            && setfacl -dR -m "u:www-data:rwX" -m "u:${CURRENT_USER}:rwX" "${NEW_PROJECT_DIR}/storage" "${NEW_PROJECT_DIR}/bootstrap/cache" 2>/dev/null \
            && ok "ACLs set on storage/ and bootstrap/cache" \
            || warn "Could not set ACLs (filesystem without ACL support?) — run artisan via 'sudo -u www-data php artisan ...'"
    fi

    grep -q "[[:space:]]${DOMAIN}\$" /etc/hosts && sed -i "s|.*[[:space:]]${DOMAIN}$|${LAN_IP} ${DOMAIN}|" /etc/hosts || echo "${LAN_IP} ${DOMAIN}" >> /etc/hosts
    _log "Added /etc/hosts entry: ${LAN_IP} ${DOMAIN}"

    ensure_php_version
else
    dry "Download ${REPO_BRANCH} tarball and place project in ${NEW_PROJECT_DIR}"
    dry "Set ownership, ACLs, public/build + storage writable by www-data"
    dry "Add /etc/hosts entry: ${LAN_IP} ${DOMAIN}"
fi

# ============================================================
# 8. ENVIRONMENT CONFIGURATION — write .env
# ============================================================
_log "Configuring .env"
if [[ "$DRY_RUN" -eq 0 ]]; then
    ENV_FILE="${NEW_PROJECT_DIR}/.env"

    if [[ ! -f "$ENV_FILE" ]]; then
        if [[ -f "${ENV_FILE}.example" ]]; then
            cp "${ENV_FILE}.example" "$ENV_FILE"
            _log ".env created from .env.example"
        else
            warn "No .env or .env.example found — creating minimal .env"
            touch "$ENV_FILE"
        fi
    fi

    # Remember the central signing keys before the sync block below is stripped and rewritten,
    # otherwise every re-run would silently rotate them and break all registered centers.
    EXISTING_PRIV="$(grep -E '^CENTRAL_PRIVATE_KEY=' "$ENV_FILE" | head -1 | cut -d= -f2- || true)"
    EXISTING_PUB="$(grep -E '^CENTRAL_PUBLIC_KEY=' "$ENV_FILE" | head -1 | cut -d= -f2- || true)"

    # set_env also handles keys that .env.example ships commented out (Laravel 11: "# DB_HOST=127.0.0.1")
    # or doesn't ship at all. The old plain `sed s|^KEY=.*|` silently skipped those, leaving the app on
    # default DB credentials / no Redis password -> exceptions -> HTTP 500.
    set_env "$ENV_FILE" APP_URL   "http://${DOMAIN}"
    set_env "$ENV_FILE" APP_ENV   "production"
    set_env "$ENV_FILE" APP_DEBUG "false"
    set_env "$ENV_FILE" APP_NAME  '"AHPC"'
    # A cookie Domain of ahpc.internal is rejected by browsers when the site is opened via the IP address,
    # which breaks login/CSRF. null = cookie is scoped to whatever host was used.
    set_env "$ENV_FILE" SESSION_DOMAIN "null"
    grep -q '^APP_KEY=' "$ENV_FILE" || set_env "$ENV_FILE" APP_KEY ""

    set_env "$ENV_FILE" DB_CONNECTION "mysql"
    set_env "$ENV_FILE" DB_HOST       "127.0.0.1"
    set_env "$ENV_FILE" DB_PORT       "3306"
    set_env "$ENV_FILE" DB_DATABASE   "${DB_NAME}"
    set_env "$ENV_FILE" DB_USERNAME   "${DB_USER}"
    set_env "$ENV_FILE" DB_PASSWORD   "${DB_PASS}"

    set_env "$ENV_FILE" REDIS_HOST     "127.0.0.1"
    set_env "$ENV_FILE" REDIS_PASSWORD "${REDIS_PASS}"
    set_env "$ENV_FILE" REDIS_PORT     "6379"

    set_env "$ENV_FILE" CACHE_STORE      "redis"
    set_env "$ENV_FILE" CACHE_DRIVER     "redis"
    set_env "$ENV_FILE" SESSION_DRIVER   "redis"
    set_env "$ENV_FILE" QUEUE_CONNECTION "redis"
    set_env "$ENV_FILE" SESSION_SECURE_COOKIE "false"

    # Remove any existing center-related lines to avoid duplicates
    for var in APP_ROLE CENTRAL_URL CENTER_SYNC_TOKEN CENTRAL_PUBLIC_KEY CENTRAL_PRIVATE_KEY APP_VERSION CENTER_ID CENTER_PULL_INTERVAL CENTER_PUSH_INTERVAL; do
        sed -i "/^${var}=/d" "$ENV_FILE"
    done

    # ── Role-specific .env configuration ──────────────────────────────────────
    if [[ "$APP_ROLE" == "central" ]]; then
        if [[ -z "$CENTRAL_PRIVATE_KEY" && -n "$EXISTING_PRIV" && "$EXISTING_PRIV" != CHANGE_ME* ]]; then
            CENTRAL_PRIVATE_KEY="$EXISTING_PRIV"
            CENTRAL_PUBLIC_KEY="${EXISTING_PUB:-$CENTRAL_PUBLIC_KEY}"
            info "Reusing the existing central key pair from .env (not rotating it)"
        fi

        # Generate key pair for central server
        if [[ -z "$CENTRAL_PRIVATE_KEY" ]]; then
            _log "Generating key pair for central server"
            KEY_OUTPUT="$(php -r '
$keyPair = sodium_crypto_sign_keypair();
$pub = sodium_bin2base64(sodium_crypto_sign_publickey($keyPair), SODIUM_BASE64_VARIANT_ORIGINAL);
$priv = sodium_bin2base64(sodium_crypto_sign_secretkey($keyPair), SODIUM_BASE64_VARIANT_ORIGINAL);
echo $priv . "\n" . $pub . "\n";
' 2>/dev/null || true)"

            if [[ -n "$KEY_OUTPUT" ]]; then
                CENTRAL_PRIVATE_KEY="$(echo "$KEY_OUTPUT" | head -1)"
                CENTRAL_PUBLIC_KEY="$(echo "$KEY_OUTPUT" | tail -1)"
                _log "Key pair generated"
            else
                warn "Could not generate keys automatically (php-sodium not available)."
                warn "Generate them manually and set CENTRAL_PRIVATE_KEY / CENTRAL_PUBLIC_KEY in .env"
                CENTRAL_PRIVATE_KEY="CHANGE_ME_PRIVATE_KEY_BASE64"
                CENTRAL_PUBLIC_KEY="CHANGE_ME_PUBLIC_KEY_BASE64"
            fi
        fi

        cat >> "$ENV_FILE" << ENVEOF

# ── Multi-Center Sync (Central Role) ─────────────────────────────────────────
APP_ROLE=central
CENTRAL_PRIVATE_KEY=${CENTRAL_PRIVATE_KEY}
CENTRAL_PUBLIC_KEY=${CENTRAL_PUBLIC_KEY}
APP_VERSION=1.0.0
ENVEOF
        ok ".env configured for central role"

    elif [[ "$APP_ROLE" == "center" ]]; then
        cat >> "$ENV_FILE" << ENVEOF

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
        ok ".env configured for center role"
    fi

    chown "www-data:www-data" "$ENV_FILE"
    chmod 600 "$ENV_FILE"
    ok ".env configured with database, redis, session, and center sync settings"
else
    dry "Create/update .env with all credentials and center sync settings"
fi

# ============================================================
# 9. COMPOSER + NPM DEPENDENCIES
# ============================================================
_log "Installing dependencies"
FRONTEND_WARNING=""
if [[ "$DRY_RUN" -eq 0 ]]; then
    cd "$NEW_PROJECT_DIR"
    command -v composer >/dev/null 2>&1 || die "Composer is not installed (see the earlier warning). Install it manually and re-run."

    _log "Running composer install (production; platform requirements enforced)"
    mkdir -p "${NEW_PROJECT_DIR}/vendor"
    chown -R www-data:www-data "${NEW_PROJECT_DIR}/vendor"
    COMPOSER_LOG="/tmp/${APP_NAME}-composer.log"
    #  --ignore-platform-reqs: [KEEP IT]
    if as_www composer install --no-dev --optimize-autoloader --no-interaction --ignore-platform-reqs >"$COMPOSER_LOG" 2>&1; then
        ok "composer install done"
    else
        tail -n 30 "$COMPOSER_LOG" >&2
        die "composer install failed (full log: ${COMPOSER_LOG}). A PHP version/extension mismatch is the usual cause — re-run with --php-version <X.Y> if the project needs a newer PHP."
    fi

    if [[ -f "package.json" ]]; then
        NPM_LOG="/tmp/${APP_NAME}-npm.log"
        : > "$NPM_LOG"
        mkdir -p "${NEW_PROJECT_DIR}/node_modules" "${NEW_PROJECT_DIR}/public/build"
        chown -R www-data:www-data "${NEW_PROJECT_DIR}/node_modules" "${NEW_PROJECT_DIR}/public/build"

        _log "Running npm install"
        NPM_OK=0
        if [[ -f package-lock.json ]] && as_www npm ci --no-audit --no-fund >>"$NPM_LOG" 2>&1; then
            NPM_OK=1
        elif as_www npm install --no-audit --no-fund >>"$NPM_LOG" 2>&1; then
            NPM_OK=1
        fi

        if [[ "$NPM_OK" -eq 1 ]]; then
            ok "npm dependencies installed"
            if grep -q '"build"' package.json; then
                _log "Running npm run build"
                if as_www npm run build >>"$NPM_LOG" 2>&1; then
                    ok "npm run build done"
                else
                    tail -n 25 "$NPM_LOG" >&2
                    FRONTEND_WARNING="npm run build FAILED (log: ${NPM_LOG}) — pages that use @vite will return HTTP 500."
                    warn "$FRONTEND_WARNING"
                fi
            fi
        else
            tail -n 25 "$NPM_LOG" >&2
            FRONTEND_WARNING="npm install FAILED (log: ${NPM_LOG}) — frontend assets were not built; pages that use @vite will return HTTP 500."
            warn "$FRONTEND_WARNING"
        fi

        if [[ -z "$FRONTEND_WARNING" ]] && grep -qE '"(vite|laravel-mix)"' package.json \
           && [[ ! -f public/build/manifest.json && ! -f public/mix-manifest.json ]]; then
            FRONTEND_WARNING="Build reported success but no public/build/manifest.json exists — check vite.config.js (outDir) / ${NPM_LOG}."
            warn "$FRONTEND_WARNING"
        fi
    else
        warn "package.json not found — skipping npm install/build"
    fi
else
    dry "composer install --no-dev --optimize-autoloader (platform reqs enforced)"
    dry "npm ci && npm run build (as www-data, output to public/build)"
fi

# ============================================================
# 10. LARAVEL SETUP — key, migrate, storage link, caches
# ============================================================
_log "Running Laravel setup commands"
if [[ "$DRY_RUN" -eq 0 ]]; then
    cd "$NEW_PROJECT_DIR"

    # Only generate a key when there isn't one. Regenerating on every re-run invalidates all sessions,
    # signed URLs and any encrypted database columns.
    if grep -qE '^APP_KEY=.+' .env; then
        info "APP_KEY already set — keeping it"
    else
        _log "Generating application key"
        if KEY_OUT="$(as_www php artisan key:generate --force 2>&1)"; then
            ok "APP_KEY generated"
        else
            echo "$KEY_OUT" >&2
            die "artisan could not boot the application. With 'composer install --no-dev', the usual cause is a dev-only package still referenced by a service provider (Pail, Collision, Debugbar, Telescope, ...). Fix the provider registration or install without --no-dev."
        fi
    fi

    _log "Running database migrations"
    if MIGRATE_OUT="$(as_www php artisan migrate --force --no-interaction 2>&1)"; then
        echo "$MIGRATE_OUT" | tail -10
        ok "Migrations complete"
    else
        echo "$MIGRATE_OUT" >&2
        die "Migrations failed — check the DB_* values in ${NEW_PROJECT_DIR}/.env and that MariaDB is running."
    fi

    _log "Creating storage symbolic link"
    rm -f "${NEW_PROJECT_DIR}/public/storage"
    ln -s "${NEW_PROJECT_DIR}/storage/app/public" "${NEW_PROJECT_DIR}/public/storage"
    chown -h www-data:www-data "${NEW_PROJECT_DIR}/public/storage"
    ok "Storage link created"

    _log "Clearing and caching config"
    as_www php artisan config:clear || true
    as_www php artisan cache:clear  || warn "cache:clear failed (is Redis reachable with the configured password?)"
    as_www php artisan view:clear   || true
    as_www php artisan route:clear  || true
    as_www php artisan config:cache || warn "config:cache failed — continuing without a config cache"
    # route:cache fails on closure routes; under `set -e` that used to abort the entire script half-way.
    as_www php artisan route:cache  || warn "route:cache failed (closure-based routes?) — continuing without a route cache"
    as_www php artisan view:cache   || warn "view:cache failed (Blade error?) — continuing without a view cache"
    ok "Laravel caches configured"
else
    dry "php artisan key:generate (only if APP_KEY is empty)"
    dry "php artisan migrate --force"
    dry "ln -s storage/app/public public/storage"
    dry "php artisan config:cache && route:cache && view:cache"
fi

# ============================================================
# 11. DEFAULT ADMIN USER PROVISIONING
# ============================================================
_log "Provisioning default admin user"
if [[ "$DRY_RUN" -eq 0 ]]; then
    cd "$NEW_PROJECT_DIR"

    # Run as www-data! Running artisan/tinker as root created root-owned files in storage/ (laravel.log,
    # compiled views, cache) that PHP-FPM could not write later -> "Permission denied" -> HTTP 500.
    EXISTING_USER="$(as_www php artisan tinker --execute="echo \App\Models\User::where('email', '${DEFAULT_ADMIN_EMAIL}')->exists() ? 'yes' : 'no';" 2>/dev/null || true)"

    if [[ "$EXISTING_USER" == *yes* ]]; then
        warn "Admin user ${DEFAULT_ADMIN_EMAIL} already exists — skipping creation"
    else
        _log "Creating admin user: ${DEFAULT_ADMIN_EMAIL}"
        if as_www php artisan users:create \
            "${DEFAULT_ADMIN_EMAIL}" \
            admin \
            --first_name="Admin" \
            --last_name="User" \
            --password="${DEFAULT_ADMIN_PASSWORD}" \
            --no-interaction 2>&1; then
            ok "Admin user provisioned"
        else
            warn "Failed to create admin user — create manually after setup"
        fi
    fi
else
    dry "php artisan users:create ${DEFAULT_ADMIN_EMAIL} admin --password=*** (as www-data)"
fi

# ============================================================
# 12. NGINX SERVER BLOCK
# ============================================================
_log "Writing Nginx config for ${DOMAIN}"
NGINX_SITE="/etc/nginx/sites-available/${DOMAIN}"

# Determine if HAProxy will be enabled (center role with lb-mode set)
LB_ENABLED=0
if [[ "$APP_ROLE" == "center" && -n "$LB_MODE" && "$LB_MODE" != "off" ]]; then
    LB_ENABLED=1
fi

if [[ "$DRY_RUN" -eq 0 ]]; then
    if [[ "$LB_ENABLED" -eq 1 ]]; then
        # When HAProxy is enabled, Nginx listens on 127.0.0.1:8081 (HTTP only)
        # HAProxy handles TLS termination on ports 80/443
        cat > "$NGINX_SITE" <<NGINXCONF
server {
    listen 127.0.0.1:8081;
    server_name ${DOMAIN} ${LAN_IP} 127.0.0.1;

    root ${NEW_PROJECT_DIR}/public;
    index index.php index.html;

    client_max_body_size 100M;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    # Tell Laravel the original request was HTTPS (HAProxy terminates TLS)
    fastcgi_param HTTPS on;

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
    else
        cat > "$NGINX_SITE" <<NGINXCONF
server {
    listen 80 default_server;
    listen [::]:80 default_server;
    server_name ${DOMAIN} ${LAN_IP};

    root ${NEW_PROJECT_DIR}/public;
    index index.php index.html;

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
    fi

    ln -sf "$NGINX_SITE" "/etc/nginx/sites-enabled/${DOMAIN}"
    rm -f /etc/nginx/sites-enabled/default
    nginx -t
    systemctl reload nginx
else
    dry "Write Nginx config to ${NGINX_SITE}"
    dry "ln -sf ${NGINX_SITE} /etc/nginx/sites-enabled/${DOMAIN}"
    dry "nginx -t && systemctl reload nginx"
fi

# ============================================================
# 12b. HAProxy LOAD BALANCER (Center role only)
# ============================================================
# Installs HAProxy on center servers to route candidate traffic between
# the local app (127.0.0.1:8081) and the Central server.
# Modes: local-first (Stage A) or central-first (Stage B).

if [[ "$LB_ENABLED" -eq 1 ]]; then

    # Resolve Central URL for HAProxy — use --lb-central-url or fall back to --central-url
    HAPROXY_CENTRAL_URL="${LB_CENTRAL_URL:-$CENTRAL_URL}"
    HAPROXY_CENTRAL_URL="${HAPROXY_CENTRAL_URL%/}"

    # Extract host and port from Central URL
    HAPROXY_CENTRAL_HOST="$(echo "$HAPROXY_CENTRAL_URL" | sed -E 's|https?://||; s|/.*||')"
    HAPROXY_CENTRAL_PORT="443"

    # Validate lb-mode
    if [[ "$LB_MODE" != "local-first" && "$LB_MODE" != "central-first" ]]; then
        warn "Invalid --lb-mode: '$LB_MODE'. Must be 'local-first' or 'central-first'."
        warn "Disabling load balancer."
        LB_ENABLED=0
    fi

    if [[ "$LB_ENABLED" -eq 1 ]]; then
        section "Installing HAProxy load balancer (mode: ${LB_MODE})"

        if [[ "$DRY_RUN" -eq 0 ]]; then

            # ── Install HAProxy ────────────────────────────────────────────────
            if ! command -v haproxy >/dev/null 2>&1; then
                _log "Installing HAProxy"
                apt-get install -y haproxy
                systemctl enable haproxy
                ok "HAProxy installed"
            else
                _log "HAProxy already installed: $(haproxy -v 2>&1 | head -1)"
            fi

            # ── Create HAProxy TLS certificate ─────────────────────────────────
            HAPROXY_TLS_DIR="/etc/ahpc/tls"
            mkdir -p "$HAPROXY_TLS_DIR"

            HAPROXY_PEM="$HAPROXY_TLS_DIR/haproxy.pem"
            if [[ "$SSL_ENABLED" -eq 1 ]]; then
                # Use existing Let's Encrypt certificate
                CERT_PATH="/etc/letsencrypt/live/${DOMAIN}/fullchain.pem"
                KEY_PATH="/etc/letsencrypt/live/${DOMAIN}/privkey.pem"
                if [[ -f "$CERT_PATH" && -f "$KEY_PATH" ]]; then
                    cat "$CERT_PATH" "$KEY_PATH" > "$HAPROXY_PEM"
                    chmod 600 "$HAPROXY_PEM"
                    ok "HAProxy TLS certificate created from Let's Encrypt"
                else
                    warn "Let's Encrypt certificate not found. Generating self-signed certificate."
                    openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
                        -keyout /tmp/haproxy.key \
                        -out /tmp/haproxy.crt \
                        -subj "/CN=${DOMAIN}" \
                        -addext "subjectAltName=DNS:${DOMAIN},IP:${LAN_IP}" 2>/dev/null
                    cat /tmp/haproxy.crt /tmp/haproxy.key > "$HAPROXY_PEM"
                    chmod 600 "$HAPROXY_PEM"
                    rm -f /tmp/haproxy.crt /tmp/haproxy.key
                    ok "Self-signed HAProxy TLS certificate created"
                fi
            else
                # Generate self-signed certificate for HAProxy
                _log "Generating self-signed TLS certificate for HAProxy"
                openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
                    -keyout /tmp/haproxy.key \
                    -out /tmp/haproxy.crt \
                    -subj "/CN=${DOMAIN}" \
                    -addext "subjectAltName=DNS:${DOMAIN},IP:${LAN_IP}" 2>/dev/null
                cat /tmp/haproxy.crt /tmp/haproxy.key > "$HAPROXY_PEM"
                chmod 600 "$HAPROXY_PEM"
                rm -f /tmp/haproxy.crt /tmp/haproxy.key
                ok "Self-signed HAProxy TLS certificate created"
            fi

            # ── Create HAProxy stats socket directory ──────────────────────────
            mkdir -p /run/haproxy
            chown haproxy:haproxy /run/haproxy

            # ── Compute cookie max-age in seconds ──────────────────────────────
            COOKIE_MAX_AGE=$((LB_COOKIE_HOURS * 3600))

            # ── Generate HAProxy configuration ─────────────────────────────────
            HAPROXY_CONF="/etc/haproxy/haproxy.cfg"

            # Build routing rules based on mode
            if [[ "$LB_MODE" == "local-first" ]]; then
                ROUTING_RULES="    use_backend central if route_central !central_down
    use_backend local   if route_local !local_down
    use_backend central if local_down !central_down
    use_backend central if local_full !central_down
    default_backend local"
            else
                # central-first
                ROUTING_RULES="    use_backend local   if { path_beg /build/ } !local_down
    use_backend local   if route_local !local_down
    use_backend central if route_central !central_down
    use_backend local   if central_down !local_down
    use_backend local   if central_full !local_down
    default_backend central"
            fi

            cat > "$HAPROXY_CONF" <<HAPROXYCONF
#---------------------------------------------------------------------
# Global settings
#---------------------------------------------------------------------
global
    log         127.0.0.1 local2
    chroot      /var/lib/haproxy
    pidfile     /run/haproxy.pid
    maxconn     4000
    user        haproxy
    group       haproxy
    daemon
    stats socket /run/haproxy/admin.sock mode 660 level admin

    # TLS tuning
    ssl-default-bind-ciphers ECDHE-ECDSA-AES128-GCM-SHA256:ECDHE-RSA-AES128-GCM-SHA256:ECDHE-ECDSA-AES256-GCM-SHA384:ECDHE-RSA-AES256-GCM-SHA384
    ssl-default-bind-ciphersuites TLS_AES_128_GCM_SHA256:TLS_AES_256_GCM_SHA384:TLS_CHACHA20_POLY1305_SHA256
    ssl-default-bind-options ssl-min-ver TLSv1.2 no-tls-tickets

#---------------------------------------------------------------------
# Defaults
#---------------------------------------------------------------------
defaults
    mode                        http
    log                         global
    option                      httplog
    option                      dontlognull
    option                      forwardfor
    option                      http-server-close
    timeout connect             3s
    timeout client              60s
    timeout server              120s
    timeout queue               10s
    errorfile 503               /etc/haproxy/errors/503.http

#---------------------------------------------------------------------
# Frontend — handles all incoming traffic on ports 80 and 443
#---------------------------------------------------------------------
frontend web
    bind :80
    bind :443 ssl crt ${HAPROXY_PEM}

    # Redirect HTTP to HTTPS
    http-request redirect scheme https unless { ssl_fc }

    # Sticky session cookies
    acl route_local   req.cook(ahpc_route) -m str local
    acl route_central req.cook(ahpc_route) -m str central

    # Backend health checks
    acl local_down    nbsrv(local) lt 1
    acl central_down  nbsrv(central) lt 1

    # Connection limits
    acl local_full    be_conn(local) ge ${LB_LOCAL_MAX}
    acl central_full  be_conn(central) ge ${LB_CENTRAL_MAX}

    # Routing rules (${LB_MODE})
${ROUTING_RULES}

#---------------------------------------------------------------------
# Backend — Local application (Nginx on 127.0.0.1:8081)
#---------------------------------------------------------------------
backend local
    option httpchk GET /up
    http-check expect status 200
    http-response add-header Set-Cookie 'ahpc_route=local; Path=/; Max-Age=${COOKIE_MAX_AGE}; HttpOnly; Secure; SameSite=Lax' unless { req.cook(ahpc_route) -m found }
    http-request set-header X-Forwarded-Server local
    server app 127.0.0.1:8081 check maxconn 180

#---------------------------------------------------------------------
# Backend — Central server (WAN, HTTPS)
#---------------------------------------------------------------------
backend central
    option httpchk GET /lb-health
    http-check expect status 200
    http-response add-header Set-Cookie 'ahpc_route=central; Path=/; Max-Age=${COOKIE_MAX_AGE}; HttpOnly; Secure; SameSite=Lax' unless { req.cook(ahpc_route) -m found }
    http-request set-header X-Forwarded-Server central
    http-request set-header X-Forwarded-Center ${CENTER_ID:-unknown}
    server central ${HAPROXY_CENTRAL_HOST}:${HAPROXY_CENTRAL_PORT} check ssl verify none maxconn 400

#---------------------------------------------------------------------
# Stats page (access via http://<ip>:9000/haproxy?stats)
#---------------------------------------------------------------------
listen stats
    bind :9000
    stats enable
    stats uri /haproxy?stats
    stats refresh 10s
    stats admin if LOCALHOST
HAPROXYCONF

            ok "HAProxy configuration written to ${HAPROXY_CONF}"

            # ── Create custom 503 error page ───────────────────────────────────
            mkdir -p /etc/haproxy/errors
            cat > /etc/haproxy/errors/503.http <<'ERROR503'
HTTP/1.1 503 Service Unavailable
Content-Type: text/html; charset=utf-8
Connection: close

<html>
<head><title>503 Service Unavailable</title></head>
<body style="font-family: sans-serif; text-align: center; padding: 50px;">
<h1>Service Temporarily Unavailable</h1>
<p>All examination servers are currently at capacity or undergoing maintenance.</p>
<p>Please try again in a few minutes.</p>
</body>
</html>
ERROR503
            ok "Custom 503 error page created"

            # ── Start/restart HAProxy ──────────────────────────────────────────
            haproxy -c -f "$HAPROXY_CONF" && {
                systemctl restart haproxy
                ok "HAProxy started and configured"
            } || {
                warn "HAProxy config validation failed. Check ${HAPROXY_CONF}"
                warn "HAProxy not started. Fix config and run: systemctl restart haproxy"
            }

            # ── Update .env with LB settings ───────────────────────────────────
            _log "Updating .env with load balancer settings"
            if [[ -f "${NEW_PROJECT_DIR}/.env" ]]; then
                # Remove existing LB entries
                sed -i '/^LB_MODE=/d' "${NEW_PROJECT_DIR}/.env"
                sed -i '/^TRUSTED_PROXY_IPS=/d' "${NEW_PROJECT_DIR}/.env"
                echo "LB_MODE=${LB_MODE}" >> "${NEW_PROJECT_DIR}/.env"
                echo "TRUSTED_PROXY_IPS=127.0.0.1" >> "${NEW_PROJECT_DIR}/.env"
                ok ".env updated with LB settings"
            fi

            # ── Clear cached config ────────────────────────────────────────────
            cd "$NEW_PROJECT_DIR"
            as_www php artisan config:clear 2>/dev/null || true
            as_www php artisan route:clear 2>/dev/null || true

            info "HAProxy is now handling ports 80/443"
            info "Local Nginx is on 127.0.0.1:8081"
            info "Stats available at http://${LAN_IP}:9000/haproxy?stats"
            info "Mode: ${LB_MODE}"
            info "Local max connections: ${LB_LOCAL_MAX}"
            info "Central max connections: ${LB_CENTRAL_MAX}"
            info "Sticky cookie expires in ${LB_COOKIE_HOURS} hours"

        else
            dry "Install HAProxy and configure for ${LB_MODE} mode"
            dry "Generate TLS certificate at ${HAPROXY_PEM}"
            dry "Write HAProxy config to /etc/haproxy/haproxy.cfg"
            dry "Move Nginx to 127.0.0.1:8081"
            dry "Start HAProxy on ports 80/443"
        fi
    fi

    # ── Handle --lb-mode off (rollback) ──────────────────────────────────────
    if [[ "$LB_MODE" == "off" && "$APP_ROLE" == "center" ]]; then
        section "Disabling HAProxy load balancer (rollback)"

        if [[ "$DRY_RUN" -eq 0 ]]; then
            if command -v haproxy >/dev/null 2>&1; then
                systemctl stop haproxy 2>/dev/null || true
                systemctl disable haproxy 2>/dev/null || true
                ok "HAProxy stopped and disabled"
            fi

            # Restore Nginx to ports 80/443
            cat > "$NGINX_SITE" <<NGINXCONF
server {
    listen 80 default_server;
    listen [::]:80 default_server;
    server_name ${DOMAIN} ${LAN_IP};

    root ${NEW_PROJECT_DIR}/public;
    index index.php index.html;

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
            nginx -t && systemctl reload nginx
            ok "Nginx restored to ports 80/443"

            # Remove LB settings from .env
            if [[ -f "${NEW_PROJECT_DIR}/.env" ]]; then
                sed -i '/^LB_MODE=/d' "${NEW_PROJECT_DIR}/.env"
                sed -i '/^TRUSTED_PROXY_IPS=/d' "${NEW_PROJECT_DIR}/.env"
                echo "LB_MODE=off" >> "${NEW_PROJECT_DIR}/.env"
            fi

            cd "$NEW_PROJECT_DIR"
            as_www php artisan config:clear 2>/dev/null || true
            as_www php artisan route:clear 2>/dev/null || true

            ok "Load balancer rollback complete"
        else
            dry "Stop HAProxy, restore Nginx to ports 80/443, clear LB settings"
        fi
    fi

fi

# ============================================================
# 13. SUPERVISOR — QUEUE WORKER
# ============================================================
_log "Writing Supervisor config for queue worker"
SUPERVISOR_CONF="/etc/supervisor/conf.d/${APP_NAME}-worker.conf"

if [[ "$DRY_RUN" -eq 0 ]]; then
    cat > "$SUPERVISOR_CONF" <<SUPCONF
[program:${APP_NAME}-worker]
process_name=%(program_name)s_%(process_num)02d
command=php ${NEW_PROJECT_DIR}/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
directory=${NEW_PROJECT_DIR}
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=${QUEUE_WORKER_PROCESSES}
redirect_stderr=true
stdout_logfile=${NEW_PROJECT_DIR}/storage/logs/worker.log
stopwaitsecs=3600
SUPCONF

    supervisorctl reread
    supervisorctl update
    supervisorctl restart "${APP_NAME}-worker:*" || true
else
    dry "Write Supervisor config to ${SUPERVISOR_CONF}"
    dry "supervisorctl reread && supervisorctl update"
fi

# ============================================================
# 14. LOG ROTATION
# ============================================================
_log "Setting up log rotation"
if [[ "$DRY_RUN" -eq 0 ]]; then

    # ── Laravel application logs ──────────────────────────────────────────────
    cat > "/etc/logrotate.d/${APP_NAME}-laravel" <<LOGROTATE
/var/www/html/ahpc/storage/logs/laravel.log {
    daily
    missingok
    rotate 14
    compress
    delaycompress
    notifempty
    create 0640 www-data www-data
    sharedscripts
    postrotate
        systemctl reload php${PHP_VER}-fpm 2>/dev/null || true
    endscript
}
LOGROTATE
    ok "Laravel log rotation configured (14 days, daily)"

    # ── Nginx access & error logs ─────────────────────────────────────────────
    cat > "/etc/logrotate.d/${APP_NAME}-nginx" <<LOGROTATE
/var/log/nginx/${DOMAIN}-access.log
/var/log/nginx/${DOMAIN}-error.log {
    daily
    missingok
    rotate 14
    compress
    delaycompress
    notifempty
    create 0640 www-data adm
    sharedscripts
    postrotate
        [ -f /var/run/nginx.pid ] && kill -USR1 \$(cat /var/run/nginx.pid) 2>/dev/null || true
    endscript
}
LOGROTATE
    ok "Nginx log rotation configured (14 days, daily)"

    # ── Supervisor worker log ─────────────────────────────────────────────────
    cat > "/etc/logrotate.d/${APP_NAME}-worker" <<'LOGROTATE'
/var/www/html/ahpc/storage/logs/worker.log {
    daily
    missingok
    rotate 7
    compress
    delaycompress
    notifempty
    create 0640 www-data www-data
}
LOGROTATE
    ok "Supervisor worker log rotation configured (7 days, daily)"

    # ── Sync log (center role) ────────────────────────────────────────────────
    cat > "/etc/logrotate.d/${APP_NAME}-sync" <<'LOGROTATE'
/var/log/ahpc-sync.log {
    daily
    missingok
    rotate 7
    compress
    delaycompress
    notifempty
    create 0640 www-data www-data
}
LOGROTATE
    ok "Sync log rotation configured (7 days, daily)"

else
    dry "Create logrotate configs for Laravel, Nginx, Supervisor worker, and sync logs"
fi

# ============================================================
# 15. LARAVEL SCHEDULER — cron entry (runs as www-data, NOT root)
# ============================================================
_log "Setting up Laravel scheduler"
if [[ "$DRY_RUN" -eq 0 ]]; then
    # A root cron job creates root-owned log/cache files in storage/ every minute, which later break the
    # web app. Move any such entries from root's crontab to www-data's.
    if crontab -u root -l 2>/dev/null | grep -qE 'artisan (schedule:run|sync:)'; then
        warn "Removing artisan cron entries from root's crontab (they now run as www-data)"
        crontab -u root -l 2>/dev/null | grep -vE 'artisan (schedule:run|sync:)' | crontab -u root - || true
    fi

    SCHEDULER_CRON="* * * * * cd ${NEW_PROJECT_DIR} && php artisan schedule:run >> /dev/null 2>&1"
    if ! crontab -u www-data -l 2>/dev/null | grep -q "schedule:run"; then
        (crontab -u www-data -l 2>/dev/null || true; echo "$SCHEDULER_CRON") | crontab -u www-data -
        ok "Laravel scheduler cron entry added (user: www-data)"
        info "Scheduled commands: SendScheduledExamReminders, BackfillQuestionSourcesCommand, RegradeNormalizeCommand"
    else
        info "Laravel scheduler cron already exists — skipping"
    fi
else
    dry "Add www-data cron entry: * * * * * cd ${NEW_PROJECT_DIR} && php artisan schedule:run"
fi

# ============================================================
# 16. SSL / HTTPS — Certbot + Let's Encrypt
# ============================================================
SSL_ENABLED=0
_log "Setting up SSL"
LOCAL_DOMAIN_RE='\.(internal|local|lan|home|localdomain|test|localhost)$'
if [[ "$DRY_RUN" -eq 0 ]]; then
    if [[ "$DOMAIN" =~ $LOCAL_DOMAIN_RE || "$DOMAIN" =~ ^[0-9.]+$ ]]; then
        # Let's Encrypt can never issue for these, so don't waste time/bandwidth on a guaranteed failure.
        info "Skipping Certbot: '${DOMAIN}' is not a publicly resolvable domain."
        info "The site is served over HTTP. For HTTPS on a LAN use a self-signed or local-CA certificate."
    else
        if ! command -v certbot >/dev/null 2>&1; then
            apt-get install -y certbot python3-certbot-nginx
            ok "Certbot installed"
        else
            _log "Certbot already installed: $(certbot --version 2>&1)"
        fi

        info "Attempting to obtain SSL certificate for ${DOMAIN}..."
        if certbot --nginx \
            --non-interactive \
            --agree-tos \
            --register-unsafely-without-email \
            -d "$DOMAIN" \
            --redirect \
            --expand \
            2>&1 | tee /var/log/certbot-setup.log; then
            SSL_ENABLED=1
            ok "SSL certificate obtained and Nginx auto-configured for HTTPS"
        else
            warn "Could not obtain SSL certificate for ${DOMAIN}. The site will continue to work over HTTP."
            warn "Check /var/log/certbot-setup.log (the domain must resolve publicly to this server, port 80 reachable)."
            SSL_ENABLED=0
        fi

        if [[ "$SSL_ENABLED" -eq 1 ]]; then
            ufw allow 443/tcp 2>/dev/null || true
            ok "UFW updated to allow HTTPS (port 443)"
        fi
    fi
else
    dry "Attempt SSL certificate for ${DOMAIN} (skipped automatically for .internal/.local/IP hosts)"
fi

# ============================================================
# 17. FIREWALL
# ============================================================
_log "Configuring UFW firewall"
if [[ "$DRY_RUN" -eq 0 ]]; then
    ufw allow OpenSSH
    if [[ -n "$LAN_SUBNET" ]]; then
        ufw allow from "$LAN_SUBNET" to any port 80 proto tcp
        ufw allow from "$LAN_SUBNET" to any port 443 proto tcp
    else
        ufw allow 80/tcp
        ufw allow 443/tcp
    fi
    ufw --force enable
else
    dry "ufw allow OpenSSH && ufw allow 80/tcp && ufw allow 443/tcp && ufw --force enable"
fi

# ============================================================
# 18. CENTER-ROLE POST-SETUP (auto-sync cron, central connectivity)
# ============================================================
if [[ "$APP_ROLE" == "center" ]]; then

    # ── Test connection to central ───────────────────────────────────────────
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

    # ── Auto-sync via cron ───────────────────────────────────────────────────
    section "Auto-sync setup"

    if [[ "$DRY_RUN" -eq 0 ]]; then
        if [[ -z "$SETUP_CRON_FLAG" ]]; then
            echo ""
            if [[ -t 0 ]]; then
                read -rp "  Set up auto-sync via cron? (pull/push every ${PULL_INTERVAL} minutes) [y/N]: " SETUP_CRON_FLAG
                SETUP_CRON_FLAG="${SETUP_CRON_FLAG:-N}"
            else
                SETUP_CRON_FLAG="N"
            fi
        fi

        if [[ "$SETUP_CRON_FLAG" =~ ^[Yy]$ ]]; then
            touch /var/log/ahpc-sync.log
            chown www-data:www-data /var/log/ahpc-sync.log
            CRON_PULL="*/${PULL_INTERVAL} * * * * cd ${NEW_PROJECT_DIR} && php artisan sync:pull >> /var/log/ahpc-sync.log 2>&1"
            CRON_PUSH="*/${PUSH_INTERVAL} * * * * cd ${NEW_PROJECT_DIR} && php artisan sync:push >> /var/log/ahpc-sync.log 2>&1"

            if ! crontab -u www-data -l 2>/dev/null | grep -q "sync:pull"; then
                (crontab -u www-data -l 2>/dev/null || true; echo "$CRON_PULL"; echo "$CRON_PUSH") | crontab -u www-data -
                ok "Cron jobs added: pull every ${PULL_INTERVAL} min, push every ${PUSH_INTERVAL} min"
                info "Log file: /var/log/ahpc-sync.log"
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

    # ── Verify sync commands ─────────────────────────────────────────────────
    section "Verifying sync commands"

    if [[ "$DRY_RUN" -eq 0 ]]; then
        cd "$NEW_PROJECT_DIR"
        if as_www php artisan list sync &>/dev/null; then
            ok "Sync commands registered"
            as_www php artisan list sync 2>&1 | grep -E 'sync:|center:' | sed 's/^/  /' || true
        else
            warn "Sync commands not found. Check: app/Console/Kernel.php"
        fi
    else
        dry "Verify sync commands are registered"
    fi
fi

# ============================================================
# 18b. FINAL PERMISSION PASS + HEALTH CHECK
# ============================================================
_log "Final permission pass and health check"
if [[ "$DRY_RUN" -eq 0 ]]; then
    # Anything created by root during provisioning must not lock www-data out of storage/.
    chown -R www-data:www-data "${NEW_PROJECT_DIR}/storage" "${NEW_PROJECT_DIR}/bootstrap/cache"
    chmod -R ug+rwX "${NEW_PROJECT_DIR}/storage" "${NEW_PROJECT_DIR}/bootstrap/cache"

    # Reload PHP (clears opcache) and restart queue workers so they pick up the new code/.env
    if [[ -n "${PHP_FPM_SERVICE:-}" ]]; then systemctl restart "$PHP_FPM_SERVICE" || true; fi
    supervisorctl restart "${APP_NAME}-worker:*" >/dev/null 2>&1 || true
    sleep 2

    HTTP_STATUS="$(curl -s -o /dev/null -w '%{http_code}' --max-time 30 -H "Host: ${DOMAIN}" http://127.0.0.1/ || true)"
    if [[ "$HTTP_STATUS" =~ ^5 || "$HTTP_STATUS" == "000" ]]; then
        warn "Health check FAILED: HTTP ${HTTP_STATUS} from http://127.0.0.1/ (Host: ${DOMAIN})"
        LATEST_LOG="$(ls -t "${NEW_PROJECT_DIR}"/storage/logs/laravel*.log 2>/dev/null | head -1 || true)"
        if [[ -n "$LATEST_LOG" ]]; then
            warn "Last lines of ${LATEST_LOG}:"
            tail -n 30 "$LATEST_LOG" | cut -c1-300 || true
        else
            warn "No Laravel log was written — likely a permissions problem on storage/ or PHP-FPM crashed before Laravel booted."
        fi
        warn "Nginx error log (last lines):"
        tail -n 15 "/var/log/nginx/${DOMAIN}-error.log" 2>/dev/null || true
    else
        ok "Health check passed (HTTP ${HTTP_STATUS})"
    fi
    [[ -z "${FRONTEND_WARNING:-}" ]] || warn "$FRONTEND_WARNING"
else
    dry "Fix storage ownership, restart php-fpm + workers, curl http://127.0.0.1/ and dump logs on 5xx"
fi

# ============================================================
# 19. SUMMARY & CREDENTIALS FILE
# ============================================================
_log "Writing credentials to ${CREDS_FILE} (root-only readable)"
if [[ "$DRY_RUN" -eq 0 ]]; then

    if [[ "$APP_ROLE" == "central" ]]; then
        SSL_STATUS="No (HTTP only)"
        [[ "$SSL_ENABLED" -eq 1 ]] && SSL_STATUS="Yes (HTTPS enabled)"
        PROTOCOL="http"
        [[ "$SSL_ENABLED" -eq 1 ]] && PROTOCOL="https"
        cat > "$CREDS_FILE" <<EOF
AHPC Central Server — Provisioning Summary
Generated: $(date)

Project path:     ${NEW_PROJECT_DIR}  (branch: ${REPO_BRANCH})
Server IP:        ${LAN_IP}
Domain:           ${DOMAIN}
URL:              ${PROTOCOL}://${DOMAIN}
SSL:              ${SSL_STATUS}
App Role:         central

Database name:    ${DB_NAME}
Database user:    ${DB_USER}
Database pass:    ${DB_PASS}

Redis password:   ${REDIS_PASS}

Default Admin:
  Email:          ${DEFAULT_ADMIN_EMAIL}
  Password:       ${DEFAULT_ADMIN_PASSWORD}

Ed25519 Keys:
  Private key:    ${CENTRAL_PRIVATE_KEY}
  Public key:     ${CENTRAL_PUBLIC_KEY}

.env is already configured with all of the above values.
EOF
    elif [[ "$APP_ROLE" == "center" ]]; then
        SSL_STATUS="No (HTTP only)"
        [[ "$SSL_ENABLED" -eq 1 ]] && SSL_STATUS="Yes (HTTPS enabled)"
        PROTOCOL="http"
        [[ "$SSL_ENABLED" -eq 1 ]] && PROTOCOL="https"
        cat > "$CREDS_FILE" <<EOF
AHPC Center Server — Provisioning Summary
Generated: $(date)

Project path:     ${NEW_PROJECT_DIR}  (branch: ${REPO_BRANCH})
Server IP:        ${LAN_IP}
Domain:           ${DOMAIN}
URL:              ${PROTOCOL}://${DOMAIN}
SSL:              ${SSL_STATUS}
App Role:         center

Central server:   ${CENTRAL_URL}
Sync token:       ${CENTER_SYNC_TOKEN:0:10}...
Central public key: ${CENTRAL_PUBLIC_KEY:0:20}...
App version:      ${APP_VERSION}
Pull interval:    ${PULL_INTERVAL} minutes
Push interval:    ${PUSH_INTERVAL} minutes

Database name:    ${DB_NAME}
Database user:    ${DB_USER}
Database pass:    ${DB_PASS}

Redis password:   ${REDIS_PASS}

Default Admin:
  Email:          ${DEFAULT_ADMIN_EMAIL}
  Password:       ${DEFAULT_ADMIN_PASSWORD}

.env is already configured with all of the above values.
EOF
    fi
    chmod 600 "$CREDS_FILE"
else
    dry "Write credentials to ${CREDS_FILE}"
fi

# ============================================================
# FINAL SUMMARY
# ============================================================
if [[ "$APP_ROLE" == "central" ]]; then
    _log "DONE — Central server provisioning complete"
    SSL_STATUS="No (HTTP only)"
    [[ "$SSL_ENABLED" -eq 1 ]] && SSL_STATUS="Yes (HTTPS enabled)"
    PROTOCOL="http"
    [[ "$SSL_ENABLED" -eq 1 ]] && PROTOCOL="https"
    cat <<SUMMARY

────────────────────────────────────────────────────────
  Central server provisioning complete for ${DOMAIN}
────────────────────────────────────────────────────────

  Server
    IP:         ${LAN_IP}
    Domain:     ${DOMAIN}
    URL:        ${PROTOCOL}://${DOMAIN}
    SSL:        ${SSL_STATUS}
    Role:       central

  Project
    Path:       ${NEW_PROJECT_DIR}  (branch: ${REPO_BRANCH})

  Database
    Name:       ${DB_NAME}
    User:       ${DB_USER}
    Password:   ${DB_PASS}
    Host:       127.0.0.1
    Port:       3306

  Redis
    Password:   ${REDIS_PASS}
    Host:       127.0.0.1
    Port:       6379

  Default Admin
    Email:      ${DEFAULT_ADMIN_EMAIL}
    Password:   ${DEFAULT_ADMIN_PASSWORD}

  Ed25519 Keys
    Private:    ${CENTRAL_PRIVATE_KEY}
    Public:     ${CENTRAL_PUBLIC_KEY}

  Credentials file: ${CREDS_FILE} (root only, mode 600)

  To set up a Center server, run:
    sudo bash ahpc-multiserver-setup.sh --app-role center \
      --central-url ${PROTOCOL}://${DOMAIN} \
      --sync-token <token-from-admin-ui> \
      --public-key ${CENTRAL_PUBLIC_KEY}

  The following were done automatically:
    - .env configured (DB, Redis, Session, Queue, Center sync, APP_NAME, SESSION_DOMAIN)
    - PHP-FPM tuned (pm.max_children=50, memory=256M, uploads=100M)
    - composer install --no-dev --optimize-autoloader --ignore-platform-reqs
    - npm ci && npm run build
    - php artisan key:generate
    - php artisan migrate --force
    - php artisan storage:link
    - php artisan config:cache + route:cache + view:cache
    - Default admin user provisioned
    - Supervisor queue workers started
    - Log rotation configured (Laravel, Nginx, Supervisor, sync)
    - Laravel scheduler cron entry added
    - SSL attempted via Certbot (Let's Encrypt)

  Client device step (every PC/phone that will open the site):
    Make ${DOMAIN} resolve to ${LAN_IP}, either:
      - add this line to the hosts file:  ${LAN_IP}  ${DOMAIN}
        (Windows: C:\\Windows\\System32\\drivers\\etc\\hosts, as Administrator;
         macOS/Linux: /etc/hosts)
      - or add a DNS record on your router / local DNS server

  Then open: ${PROTOCOL}://${DOMAIN}   (or http://${LAN_IP})
────────────────────────────────────────────────────────
SUMMARY

elif [[ "$APP_ROLE" == "center" ]]; then
    _log "DONE — Center server provisioning complete"
    SSL_STATUS="No (HTTP only)"
    [[ "$SSL_ENABLED" -eq 1 ]] && SSL_STATUS="Yes (HTTPS enabled)"
    PROTOCOL="http"
    [[ "$SSL_ENABLED" -eq 1 ]] && PROTOCOL="https"
    cat <<SUMMARY

────────────────────────────────────────────────────────
  Center server provisioning complete for ${DOMAIN}
────────────────────────────────────────────────────────

  Server
    IP:         ${LAN_IP}
    Domain:     ${DOMAIN}
    URL:        ${PROTOCOL}://${DOMAIN}
    SSL:        ${SSL_STATUS}
    Role:       center

  Project
    Path:       ${NEW_PROJECT_DIR}  (branch: ${REPO_BRANCH})

  Sync Configuration
    Central:    ${CENTRAL_URL}
    Token:      ${CENTER_SYNC_TOKEN:0:10}...
    Public key: ${CENTRAL_PUBLIC_KEY:0:20}...
    Version:    ${APP_VERSION}
    Pull:       Every ${PULL_INTERVAL} minutes
    Push:       Every ${PUSH_INTERVAL} minutes

  Database
    Name:       ${DB_NAME}
    User:       ${DB_USER}
    Password:   ${DB_PASS}
    Host:       127.0.0.1
    Port:       3306

  Redis
    Password:   ${REDIS_PASS}
    Host:       127.0.0.1
    Port:       6379

  Default Admin
    Email:      ${DEFAULT_ADMIN_EMAIL}
    Password:   ${DEFAULT_ADMIN_PASSWORD}

  Credentials file: ${CREDS_FILE} (root only, mode 600)

  The following were done automatically:
    - .env configured (DB, Redis, Session, Queue, Center sync, APP_NAME, SESSION_DOMAIN)
    - PHP-FPM tuned (pm.max_children=50, memory=256M, uploads=100M)
    - composer install --no-dev --optimize-autoloader --ignore-platform-reqs
    - npm ci && npm run build
    - php artisan key:generate
    - php artisan migrate --force
    - php artisan storage:link
    - php artisan config:cache + route:cache + view:cache
    - Default admin user provisioned
    - Supervisor queue workers started
    - Log rotation configured (Laravel, Nginx, Supervisor, sync)
    - Laravel scheduler cron entry added
    - SSL attempted via Certbot (Let's Encrypt)
    - Central server connectivity tested

  Next steps:
    1. Test the pull:
       cd ${NEW_PROJECT_DIR}
       php artisan sync:pull --force

    2. Verify exams appeared:
       php artisan tinker
       >>> \App\ExaminationHub\Models\GeneralExam::count()

    3. Push results (after an exam):
       php artisan sync:push --force

    4. Check sync status:
       Visit: ${PROTOCOL}://${DOMAIN}/center-sync

  Logs
     tail -f /var/log/ahpc-sync.log
     tail -f ${NEW_PROJECT_DIR}/storage/logs/laravel.log

  Client device step (every PC/phone that will open the site):
    Make ${DOMAIN} resolve to ${LAN_IP}, either:
      - add this line to the hosts file:  ${LAN_IP}  ${DOMAIN}
        (Windows: C:\\Windows\\System32\\drivers\\etc\\hosts, as Administrator;
         macOS/Linux: /etc/hosts)
      - or add a DNS record on your router / local DNS server

  Then open: ${PROTOCOL}://${DOMAIN}   (or http://${LAN_IP})
────────────────────────────────────────────────────────
SUMMARY
fi

if [[ "$DRY_RUN" -eq 1 ]]; then
    echo ""
    echo -e "${BOLD}${YELLOW}============================================================"
    echo -e "  DRY-RUN COMPLETE — No changes were made to the system"
    echo -e "  Run without --dry-run to apply these changes"
    echo -e "============================================================${NC}"
    echo ""
fi
