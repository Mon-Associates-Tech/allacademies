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

set -euo pipefail
IFS=$'\n\t'

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
#   --setup-cron         Set up auto-sync cron jobs (center role)
#   --no-cron            Skip cron setup entirely (center role)
#   --dry-run            Show what would be done without making changes
#   --help               Show this help message

# ── Parse arguments ────────────────────────────────────────────────────────────
APP_ROLE=""
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
    echo "  --setup-cron         Set up auto-sync cron jobs (center role)"
    echo "  --no-cron            Skip cron setup entirely (center role)"
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
# 3. CREATE DEPLOY USER
# ============================================================
_log "Setting up deploy user"
DEPLOY_USER="deploy"
if [[ "$DRY_RUN" -eq 0 ]]; then
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
else
    dry "adduser --disabled-password --gecos '' ${DEPLOY_USER}"
    dry "usermod -aG sudo ${DEPLOY_USER}"
    dry "usermod -aG www-data ${DEPLOY_USER}"
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
else
    dry "apt-get install -y redis-server && configure redis.conf && systemctl enable redis-server"
fi

# ============================================================
# 6. PHP + COMPOSER
# ============================================================
if [[ "$DRY_RUN" -eq 0 ]]; then
    if ! command -v php >/dev/null 2>&1; then
        _log "PHP not found — installing default PHP packages"
        apt-get install -y php-cli php-fpm
    fi
    PHP_VER="$(php -r 'echo PHP_MAJOR_VERSION.".".PHP_MINOR_VERSION;')"
    _log "Using PHP ${PHP_VER}"

    PHP_FPM_SERVICE="php${PHP_VER}-fpm"
    PHP_FPM_SOCK="/run/php/php${PHP_VER}-fpm.sock"

    _log "Installing required PHP extensions"
    apt-get install -y \
        php-mbstring php-curl php-fpm php-mysql php-redis \
        "php${PHP_VER}-fpm" "php${PHP_VER}-cli" "php${PHP_VER}-common" \
        "php${PHP_VER}-redis" "php${PHP_VER}-xml" "php${PHP_VER}-zip" \
        "php${PHP_VER}-gd" "php${PHP_VER}-bcmath" "php${PHP_VER}-intl" \
        "php${PHP_VER}-sodium" \
        || warn "Some PHP packages may not exist — verify with 'php -m'."

    systemctl enable "$PHP_FPM_SERVICE" || warn "Could not enable ${PHP_FPM_SERVICE}."
    systemctl restart "$PHP_FPM_SERVICE" || true

    # ── PHP-FPM Production Tuning ─────────────────────────────────────────────
    _log "Tuning PHP-FPM for production"
    PHP_FPM_POOL_DIR="/etc/php/${PHP_VER}/fpm/pool.d"
    PHP_FPM_POOL="${PHP_FPM_POOL_DIR}/www.conf"
    if [[ -f "$PHP_FPM_POOL" ]]; then
        # Process manager tuning
        sed -i 's/^;\?pm = .*/pm = dynamic/' "$PHP_FPM_POOL"
        sed -i 's/^;\?pm.max_children = .*/pm.max_children = 50/' "$PHP_FPM_POOL"
        sed -i 's/^;\?pm.start_servers = .*/pm.start_servers = 5/' "$PHP_FPM_POOL"
        sed -i 's/^;\?pm.min_spare_servers = .*/pm.min_spare_servers = 2/' "$PHP_FPM_POOL"
        sed -i 's/^;\?pm.max_spare_servers = .*/pm.max_spare_servers = 10/' "$PHP_FPM_POOL"
        # Memory limit
        sed -i 's/^;\?php\[memory_limit\] = .*/php\[memory_limit\] = 256M/' "$PHP_FPM_POOL"
        # Upload sizes (aligned with Nginx client_max_body_size 100M)
        sed -i 's/^;\?php\[upload_max_filesize\] = .*/php\[upload_max_filesize\] = 100M/' "$PHP_FPM_POOL"
        sed -i 's/^;\?php\[post_max_size\] = .*/php\[post_max_size\] = 100M/' "$PHP_FPM_POOL"
        systemctl restart "$PHP_FPM_SERVICE" || true
        ok "PHP-FPM tuned: pm.max_children=50, memory=256M, uploads=100M"
    else
        warn "PHP-FPM pool config not found at ${PHP_FPM_POOL} — skipping tuning."
    fi

    if ! command -v composer >/dev/null 2>&1; then
        _log "Installing Composer"
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
else
    dry "Install PHP and extensions (php-fpm, php-cli, php-mysql, php-redis, php-mbstring, php-xml, php-curl, php-zip, php-gd, php-bcmath, php-intl, php-sodium)"
    dry "systemctl enable php-fpm && systemctl restart php-fpm"
    dry "Install Composer"
fi

# ============================================================
# 5b. Node.js + npm
# ============================================================
if [[ "$DRY_RUN" -eq 0 ]]; then
    if ! command -v node >/dev/null 2>&1; then
        _log "Installing Node.js and npm"
        curl -fsSL https://deb.nodesource.com/setup_20.x -o /tmp/nodesource-setup.sh
        bash /tmp/nodesource-setup.sh
        apt-get install -y nodejs
        rm /tmp/nodesource-setup.sh
    else
        _log "Node.js already installed: $(node --version)"
    fi

    if ! command -v npm >/dev/null 2>&1; then
        warn "npm not found — install manually."
    else
        _log "npm version: $(npm --version)"
    fi
else
    dry "Install Node.js 20 LTS and npm"
fi

# ============================================================
# 7. PROJECT FILES — clone the 'ahpc' branch
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
    sudo -u www-data wget --timeout=120 --tries=3 -q -O "$TAR_FILE" "$TAR_URL" \
        || die "Failed to download ${TAR_URL} — check network connectivity."

    _log "Extracting archive"
    rm -rf "$EXTRACT_DIR"
    mkdir -p "$EXTRACT_DIR"
    tar -xzf "$TAR_FILE" -C "$EXTRACT_DIR" --strip-components=1
    rm -f "$TAR_FILE"

    if [[ -d "$NEW_PROJECT_DIR" ]]; then
        _log "${NEW_PROJECT_DIR} exists — syncing latest files (preserving .env and storage)"
        rsync -a --exclude='.env' --exclude='storage/' --exclude='vendor/' --exclude='node_modules/' \
            "${EXTRACT_DIR}/" "${NEW_PROJECT_DIR}/"
    else
        _log "Moving extracted files to ${NEW_PROJECT_DIR}"
        mv "$EXTRACT_DIR" "$NEW_PROJECT_DIR"
    fi
    rm -rf "$EXTRACT_DIR"

    _log "Fixing ownership and permissions"
    chown -R "www-data:www-data" "$NEW_PROJECT_DIR"
    find "$NEW_PROJECT_DIR" -type d -exec chmod 755 {} \;
    find "$NEW_PROJECT_DIR" -type f -exec chmod 644 {} \;
    for d in storage bootstrap/cache; do
        if [[ -d "${NEW_PROJECT_DIR}/${d}" ]]; then
            chmod -R 775 "${NEW_PROJECT_DIR}/${d}"
            find "${NEW_PROJECT_DIR}/${d}" -type d -exec chmod g+s {} \;
        fi
    done
    mkdir -p "${NEW_PROJECT_DIR}/storage/logs"
    chown -R "www-data:www-data" "${NEW_PROJECT_DIR}/storage/logs"

    grep -q "[[:space:]]${DOMAIN}\$" /etc/hosts && sed -i "s|.*[[:space:]]${DOMAIN}$|${LAN_IP} ${DOMAIN}|" /etc/hosts || echo "${LAN_IP} ${DOMAIN}" >> /etc/hosts
    _log "Added /etc/hosts entry: ${LAN_IP} ${DOMAIN}"
else
    dry "mkdir -p /var/www/html && Clone or move project to ${NEW_PROJECT_DIR}"
    dry "Set ownership www-data:www-data on ${NEW_PROJECT_DIR}"
    dry "Add /etc/hosts entry: ${LAN_IP} ${DOMAIN}"
fi

# ============================================================
# 8. ENVIRONMENT CONFIGURATION — write .env
# ============================================================
_log "Configuring .env"
if [[ "$DRY_RUN" -eq 0 ]]; then
    if [[ ! -f "${NEW_PROJECT_DIR}/.env" ]]; then
        if [[ -f "${NEW_PROJECT_DIR}/.env.example" ]]; then
            cp "${NEW_PROJECT_DIR}/.env.example" "${NEW_PROJECT_DIR}/.env"
            _log ".env created from .env.example"
        else
            warn "No .env or .env.example found — creating minimal .env"
            touch "${NEW_PROJECT_DIR}/.env"
        fi
    fi

    ENV_FILE="${NEW_PROJECT_DIR}/.env"

    sed -i "s|^APP_URL=.*|APP_URL=http://${DOMAIN}|" "$ENV_FILE"
    sed -i "s|^APP_ENV=.*|APP_ENV=production|" "$ENV_FILE"
    sed -i "s|^APP_DEBUG=.*|APP_DEBUG=false|" "$ENV_FILE"
    sed -i "s|^APP_NAME=.*|APP_NAME=\"All Academies\"|" "$ENV_FILE"
    sed -i "s|^SESSION_DOMAIN=.*|SESSION_DOMAIN=${DOMAIN}|" "$ENV_FILE"

    sed -i "s|^DB_CONNECTION=.*|DB_CONNECTION=mysql|" "$ENV_FILE"
    sed -i "s|^DB_HOST=.*|DB_HOST=127.0.0.1|" "$ENV_FILE"
    sed -i "s|^DB_PORT=.*|DB_PORT=3306|" "$ENV_FILE"
    sed -i "s|^DB_DATABASE=.*|DB_DATABASE=${DB_NAME}|" "$ENV_FILE"
    sed -i "s|^DB_USERNAME=.*|DB_USERNAME=${DB_USER}|" "$ENV_FILE"
    sed -i "s|^DB_PASSWORD=.*|DB_PASSWORD=${DB_PASS}|" "$ENV_FILE"

    sed -i "s|^REDIS_HOST=.*|REDIS_HOST=127.0.0.1|" "$ENV_FILE"
    sed -i "s|^REDIS_PASSWORD=.*|REDIS_PASSWORD=${REDIS_PASS}|" "$ENV_FILE"
    sed -i "s|^REDIS_PORT=.*|REDIS_PORT=6379|" "$ENV_FILE"

    sed -i "s|^CACHE_STORE=.*|CACHE_STORE=redis|" "$ENV_FILE"
    sed -i "s|^CACHE_DRIVER=.*|CACHE_DRIVER=redis|" "$ENV_FILE"
    sed -i "s|^SESSION_DRIVER=.*|SESSION_DRIVER=redis|" "$ENV_FILE"
    sed -i "s|^QUEUE_CONNECTION=.*|QUEUE_CONNECTION=redis|" "$ENV_FILE"

    if grep -q '^SESSION_SECURE_COOKIE' "$ENV_FILE"; then
        sed -i 's|^SESSION_SECURE_COOKIE=.*|SESSION_SECURE_COOKIE=false|' "$ENV_FILE"
    else
        echo "SESSION_SECURE_COOKIE=false" >> "$ENV_FILE"
    fi

    # Remove any existing center-related lines to avoid duplicates
    for var in APP_ROLE CENTRAL_URL CENTER_SYNC_TOKEN CENTRAL_PUBLIC_KEY CENTRAL_PRIVATE_KEY APP_VERSION CENTER_ID CENTER_PULL_INTERVAL CENTER_PUSH_INTERVAL; do
        sed -i "/^${var}=/d" "$ENV_FILE"
    done

    # ── Role-specific .env configuration ──────────────────────────────────────
    if [[ "$APP_ROLE" == "central" ]]; then
        # Generate Ed25519 key pair for central server
        if [[ -z "$CENTRAL_PRIVATE_KEY" ]]; then
            _log "Generating Ed25519 key pair for central server"
            KEY_OUTPUT="$(php -r '
$pair = sodium_crypto_box_keypair();
$pub = sodium_bin2base64(sodium_crypto_box_publickey($pair), SODIUM_BASE64_VARIANT_ORIGINAL);
$priv = sodium_bin2base64(sodium_crypto_box_secretkey($pair), SODIUM_BASE64_VARIANT_ORIGINAL);
echo $priv . "\n" . $pub . "\n";
' 2>/dev/null || true)"

            if [[ -n "$KEY_OUTPUT" ]]; then
                CENTRAL_PRIVATE_KEY="$(echo "$KEY_OUTPUT" | head -1)"
                CENTRAL_PUBLIC_KEY="$(echo "$KEY_OUTPUT" | tail -1)"
                _log "Ed25519 key pair generated"
            else
                warn "Could not generate Ed25519 keys automatically (php-sodium not available)."
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
if [[ "$DRY_RUN" -eq 0 ]]; then
    cd "$NEW_PROJECT_DIR"

    _log "Running composer install"
    sudo -u www-data composer install --no-dev --optimize-autoloader --no-interaction --ignore-platform-reqs 2>&1 | tail -5 \
        && ok "composer install done" || warn "composer install failed — continuing."

    if [[ -f "package.json" ]]; then
        NPM_CACHE="/tmp/${APP_NAME}-npm-cache"
        mkdir -p "$NPM_CACHE" "${NEW_PROJECT_DIR}/node_modules"
        chown -R "www-data:www-data" "$NPM_CACHE" "${NEW_PROJECT_DIR}/node_modules"

        _log "Running npm install"
        sudo -u www-data npm ci --no-audit --no-fund --cache "$NPM_CACHE" 2>&1 | tail -3 \
            || sudo -u www-data npm install --no-audit --no-fund --cache "$NPM_CACHE" 2>&1 | tail -3 \
            || warn "npm install failed — continuing."

        _log "Running npm run build"
        sudo -u www-data npm run build 2>&1 | tail -5 \
            && ok "npm run build done" || warn "npm run build failed — continuing."

        rm -rf "$NPM_CACHE"
    else
        warn "package.json not found — skipping npm install/build"
    fi
else
    dry "composer install --no-dev --optimize-autoloader --ignore-platform-reqs"
    dry "npm ci && npm run build"
fi

# ============================================================
# 10. LARAVEL SETUP — key, migrate, storage link, caches
# ============================================================
_log "Running Laravel setup commands"
if [[ "$DRY_RUN" -eq 0 ]]; then
    cd "$NEW_PROJECT_DIR"

    _log "Generating application key"
    php artisan key:generate --force
    ok "APP_KEY generated"

    _log "Running database migrations"
    php artisan migrate --force --no-interaction 2>&1 | tail -10
    ok "Migrations complete"

    _log "Creating storage symbolic link"
    php artisan storage:link
    ok "Storage link created"

    _log "Clearing and caching config"
    php artisan config:clear
    php artisan cache:clear
    php artisan view:clear
    php artisan route:clear
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    ok "Laravel caches configured"
else
    dry "php artisan key:generate --force"
    dry "php artisan migrate --force"
    dry "php artisan storage:link"
    dry "php artisan config:cache && route:cache && view:cache"
fi

# ============================================================
# 11. DEFAULT ADMIN USER PROVISIONING
# ============================================================
_log "Provisioning default admin user"
if [[ "$DRY_RUN" -eq 0 ]]; then
    cd "$NEW_PROJECT_DIR"

    EXISTING_USER="$(php artisan tinker --execute="echo \App\Models\User::where('email', '${DEFAULT_ADMIN_EMAIL}')->exists() ? 'yes' : 'no';" 2>/dev/null || true)"

    if [[ "$EXISTING_USER" == "yes" ]]; then
        warn "Admin user ${DEFAULT_ADMIN_EMAIL} already exists — skipping creation"
    else
        _log "Creating admin user: ${DEFAULT_ADMIN_EMAIL}"
        php artisan users:create \
            "${DEFAULT_ADMIN_EMAIL}" \
            admin \
            --first_name="Admin" \
            --last_name="User" \
            --password="${DEFAULT_ADMIN_PASSWORD}" \
            --no-interaction 2>&1 || \
            warn "Failed to create admin user — create manually after setup"
        ok "Admin user provisioned"
    fi
else
    dry "php artisan users:create ${DEFAULT_ADMIN_EMAIL} admin --password=***"
fi

# ============================================================
# 12. NGINX SERVER BLOCK
# ============================================================
_log "Writing Nginx config for ${DOMAIN}"
NGINX_SITE="/etc/nginx/sites-available/${DOMAIN}"

if [[ "$DRY_RUN" -eq 0 ]]; then
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
        [ -f /var/run/nginx.pid ] && kill -USR1 $(cat /var/run/nginx.pid) 2>/dev/null || true
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
    create 0640 root root
}
LOGROTATE
    ok "Sync log rotation configured (7 days, daily)"

else
    dry "Create logrotate configs for Laravel, Nginx, Supervisor worker, and sync logs"
fi

# ============================================================
# 15. LARAVEL SCHEDULER — cron entry
# ============================================================
_log "Setting up Laravel scheduler"
if [[ "$DRY_RUN" -eq 0 ]]; then
    SCHEDULER_CRON="* * * * * cd ${NEW_PROJECT_DIR} && php artisan schedule:run >> /dev/null 2>&1"
    # Add to root's crontab if not already present
    if ! crontab -u root -l 2>/dev/null | grep -q "schedule:run"; then
        (crontab -u root -l 2>/dev/null; echo "$SCHEDULER_CRON") | crontab -u root -
        ok "Laravel scheduler cron entry added"
        info "Scheduled commands: SendScheduledExamReminders, BackfillQuestionSourcesCommand, RegradeNormalizeCommand"
    else
        info "Laravel scheduler cron already exists — skipping"
    fi
else
    dry "Add cron entry: * * * * * cd ${NEW_PROJECT_DIR} && php artisan schedule:run"
fi

# ============================================================
# 16. SSL / HTTPS — Certbot + Let's Encrypt
# ============================================================
SSL_ENABLED=0
_log "Setting up SSL with Certbot"
if [[ "$DRY_RUN" -eq 0 ]]; then
    # Install Certbot and Nginx plugin
    if ! command -v certbot >/dev/null 2>&1; then
        apt-get install -y certbot python3-certbot-nginx
        ok "Certbot installed"
    else
        _log "Certbot already installed: $(certbot --version 2>&1)"
    fi

    # Try to obtain a certificate
    # Note: For .internal domains, Let's Encrypt cannot issue certificates
    # because .internal is a reserved TLD (RFC 8375). We attempt it anyway
    # and fall back gracefully if it fails.
    echo ""
    info "Attempting to obtain SSL certificate for ${DOMAIN}..."
    info "Note: If ${DOMAIN} uses a reserved TLD (e.g., .internal), this will fail."
    info "In that case, consider using a public domain or a self-signed certificate."
    echo ""

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
        warn "Could not obtain SSL certificate for ${DOMAIN}."
        warn "This is expected for .internal domains (reserved TLD)."
        warn "The site will continue to work over HTTP."
        warn "To use HTTPS, either:"
        warn "  1. Use a public domain (e.g., yourdomain.com)"
        warn "  2. Generate a self-signed certificate:"
        warn "     openssl req -x509 -nodes -days 365 -newkey rsa:2048 \\"
        warn "       -keyout /etc/ssl/private/${DOMAIN}.key \\"
        warn "       -out /etc/ssl/certs/${DOMAIN}.crt \\"
        warn "       -subj '/CN=${DOMAIN}'"
        SSL_ENABLED=0
    fi

    # Ensure UFW allows HTTPS if SSL was enabled
    if [[ "$SSL_ENABLED" -eq 1 ]]; then
        ufw allow 443/tcp 2>/dev/null || true
        ok "UFW updated to allow HTTPS (port 443)"
    fi
else
    dry "Install certbot and attempt SSL certificate for ${DOMAIN}"
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
            CRON_PULL="*/${PULL_INTERVAL} * * * * cd ${NEW_PROJECT_DIR} && php artisan sync:pull >> /var/log/ahpc-sync.log 2>&1"
            CRON_PUSH="*/${PUSH_INTERVAL} * * * * cd ${NEW_PROJECT_DIR} && php artisan sync:push >> /var/log/ahpc-sync.log 2>&1"

            if ! crontab -u root -l 2>/dev/null | grep -q "sync:pull"; then
                (crontab -u root -l 2>/dev/null; echo "$CRON_PULL"; echo "$CRON_PUSH") | crontab -u root -
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
        if php artisan list sync &>/dev/null; then
            ok "Sync commands registered"
            php artisan list sync 2>&1 | grep -E 'sync:|center:' | sed 's/^/  /'
        else
            warn "Sync commands not found. Check: app/Console/Kernel.php"
        fi
    else
        dry "Verify sync commands are registered"
    fi
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
