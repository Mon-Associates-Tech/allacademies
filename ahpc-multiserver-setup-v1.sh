#!/usr/bin/env bash
# ==============================================================================
#  AHPC Multi-Server Setup Script  (v3)
#
#  Provisions a Central or Center AHPC server (Nginx + PHP-FPM + MariaDB + Redis +
#  Supervisor) sized for ~400 concurrent students per center on a 32 GB / 800 GB box.
#
#  Run as root:   sudo bash ahpc-multiserver-setup.sh --app-role central|center [options]
#
#  First run   -> full provisioning. At the end the script installs itself as
#                 /usr/local/sbin/ahpc-setup and saves its settings to /etc/ahpc/setup.conf,
#                 so afterwards you can run ONLY what you need:
#
#      sudo ahpc-setup --list-sections
#      sudo ahpc-setup --only deploy                 # pull latest code, deps, migrate, caches
#      sudo ahpc-setup --only tls                    # (re)issue the local certificate
#      sudo ahpc-setup --only nginx,tls              # rewrite web config + certificate
#      sudo ahpc-setup --from laravel                # resume a failed run from a section
#      sudo ahpc-setup --fix-ip                      # pin the current LAN IP (device moved)
#      sudo ahpc-setup --fix-ip 10.20.0.50/24 --gateway 10.20.0.1
#      sudo ahpc-setup --dhcp                        # back to DHCP before moving the device
#
#  Safe to re-run: every section checks existing state before acting.
#  Run with --help for all options.
# ==============================================================================

set -Eeuo pipefail
IFS=$'\n\t'
trap 'echo -e "\n\033[0;31m[FATAL]\033[0m Aborted at line ${LINENO}: ${BASH_COMMAND}" >&2' ERR
export DEBIAN_FRONTEND=noninteractive
export NEEDRESTART_MODE=a   # don't let needrestart prompt/hang during apt upgrades

# ============================================================
# CONSTANTS
# ============================================================
APP_NAME="ahpc"
DEFAULT_DOMAIN="ahpc.internal"
NEW_PROJECT_DIR="/var/www/html/${APP_NAME}"

DB_NAME="${APP_NAME}"
DB_USER="${APP_NAME}_user"

STATE_DIR="/etc/${APP_NAME}"
STATE_FILE="${STATE_DIR}/setup.conf"
CA_DIR="${STATE_DIR}/ca"            # local root CA (private key lives here, root-only)
TLS_DIR="${STATE_DIR}/tls"          # issued server certificate + key (root-only)
PUBLIC_DIR="${STATE_DIR}/public"    # world-readable copy of the CA certificate for client devices
CA_PUBLIC_NAME="${APP_NAME}-rootCA.crt"
SELF_INSTALL_PATH="/usr/local/sbin/${APP_NAME}-setup"
LOG_FILE="/var/log/${APP_NAME}-setup.log"
LOCK_FILE="/var/lock/${APP_NAME}-setup.lock"
CREDS_FILE="/root/${APP_NAME}-credentials.txt"
NETPLAN_FILE="/etc/netplan/60-${APP_NAME}-static.yaml"

# Optional: restrict web access to your LAN, e.g. "192.168.1.0/24". Empty = allow all.
LAN_SUBNET=""

# Wi-Fi defaults
DEFAULT_WIFI_SSID="AHGENERAL"
DEFAULT_WIFI_PASSWORD="1L0ve@M1n3!0B.&"

# Center-specific defaults
DEFAULT_APP_VERSION="1.0.0"
DEFAULT_PULL_INTERVAL=15
DEFAULT_PUSH_INTERVAL=15

# Section order is fixed: --only/--skip/--from always run in this order.
ALL_SECTIONS=(wifi ip packages tuning mariadb redis php node code env deps laravel admin nginx workers logrotate cron tls firewall sync finalize summary)

# Settings remembered between runs (/etc/ahpc/setup.conf, root-only). CLI flags override them.
STATE_KEYS=(APP_ROLE DOMAIN LAN_IP REPO_BRANCH CENTRAL_URL CENTER_SYNC_TOKEN CENTRAL_PUBLIC_KEY
            APP_VERSION PULL_INTERVAL PUSH_INTERVAL DEFAULT_ADMIN_EMAIL DEFAULT_ADMIN_PASSWORD
            DB_PASS REDIS_PASS CURRENT_USER PHP_VERSION_FORCE EXPECTED_STUDENTS FPM_CHILDREN
            QUEUE_WORKER_PROCESSES TLS_MODE TLS_TOOL TLS_SANS TLS_EMAIL TLS_CERT TLS_KEY
            NET_IFACE NET_GATEWAY NET_DNS FIXED_IP PROVISIONED)

section_desc() {
    case "$1" in
        wifi)      echo "Connect to Wi-Fi with nmcli (skip with --skip-wifi)" ;;
        ip)        echo "Pin a fixed IP (--fix-ip [IP[/prefix]]) or return to DHCP (--dhcp)" ;;
        packages)  echo "apt update/upgrade, base utilities, SSH, user groups" ;;
        tuning)    echo "Kernel/network limits (somaxconn, file handles) for ~400 concurrent users" ;;
        mariadb)   echo "MariaDB: install, harden, size to RAM, create app database + user" ;;
        redis)     echo "Redis: localhost-only, password, memory limit (sessions/cache/queue)" ;;
        php)       echo "PHP-FPM + extensions (sized to RAM), Composer; switches PHP version if needed" ;;
        node)      echo "Node.js (>= 20.19) + npm for frontend builds" ;;
        code)      echo "Download the branch tarball into ${NEW_PROJECT_DIR}, fix permissions" ;;
        env)       echo "Write/refresh .env (DB, Redis, session, sync settings)" ;;
        deps)      echo "composer install + npm ci + npm run build" ;;
        laravel)   echo "key:generate (once), migrate, storage:link, config/route/view caches" ;;
        admin)     echo "Create the default admin user" ;;
        nginx)     echo "Nginx: install, tune, write the site (HTTP, or HTTPS when a certificate exists)" ;;
        workers)   echo "Supervisor queue workers" ;;
        logrotate) echo "Log rotation (Laravel, Nginx, workers, sync)" ;;
        cron)      echo "Laravel scheduler cron (runs as www-data)" ;;
        tls)       echo "Local CA + signed certificate (mkcert/openssl) or Let's Encrypt for public domains" ;;
        firewall)  echo "UFW firewall" ;;
        sync)      echo "Center role: connectivity test, auto-sync cron, sync command check" ;;
        finalize)  echo "Fix storage permissions, restart PHP-FPM/workers, HTTP health check" ;;
        summary)   echo "Write credentials file and print the summary" ;;
    esac
}

expand_alias() {
    case "$1" in
        all)    printf '%s\n' "${ALL_SECTIONS[@]}" ;;
        deploy) printf '%s\n' code deps laravel finalize ;;          # ship a new version
        app)    printf '%s\n' code env deps laravel admin ;;         # application only
        web)    printf '%s\n' nginx tls ;;                           # web server + certificate
        *)      printf '%s\n' "$1" ;;
    esac
}

is_section() {
    local s
    for s in "${ALL_SECTIONS[@]}"; do
        if [[ "$s" == "$1" ]]; then return 0; fi
    done
    return 1
}

list_sections() {
    local s
    echo "Sections (always executed in this order):"
    for s in "${ALL_SECTIONS[@]}"; do printf '  %-10s %s\n' "$s" "$(section_desc "$s")"; done
    echo
    echo "Shortcuts for --only:"
    echo "  deploy     code deps laravel finalize      (ship a new version)"
    echo "  app        code env deps laravel admin"
    echo "  web        nginx tls"
    echo "  all        everything"
}

usage() {
    cat <<'USAGE'
Usage: sudo bash ahpc-multiserver-setup.sh --app-role central|center [options]
       sudo ahpc-setup [--only a,b] [--skip a,b] [--from section] [options]   (after the first run)

Choosing what runs
  --only a,b,c         run only these sections (see --list-sections; shortcuts: deploy, app, web, all)
  --skip a,b           run everything except these
  --from section       run from this section to the end (resume after a failure)
  --list-sections      show all sections and exit
  --full               with --fix-ip/--dhcp on a provisioned server: run everything, not just 'ip'
  --dry-run            show what would be done without changing anything

Server
  --app-role           central | center  (required on the first run; remembered afterwards)
  --domain             Domain name (default: ahpc.internal)
  --ip                 Use this address for /etc/hosts + certificate (does NOT change the network)
  --repo-branch        Git branch to deploy (default: ahpc)
  --admin-email / --admin-password
  --db-password / --redis-password   (default: auto-generated, kept across runs)
  --php-version X.Y    Force a PHP version (installed from ppa:ondrej/php if needed)
  --skip-upgrade       Skip 'apt-get upgrade' (limited bandwidth)

Fixed IP (device may move between locations)
  --fix-ip [IP[/N]]    Pin the LAN address. No value = keep the address it has right now.
                       With a value from another subnet also pass --gateway.
  --gateway IP         Default gateway (default: the current one)
  --dns a,b            DNS servers (default: current ones, else gateway,1.1.1.1)
  --iface NAME         Network interface (default: the one holding the default route)
  --dhcp               Go back to DHCP (run this before moving, or use --fix-ip at the new place)
  --force              Allow an address that already answers on the network

Capacity
  --students N         Expected concurrent students per center (default: 400)
  --fpm-children N     Override the computed pm.max_children
  --queue-workers N    Supervisor queue worker processes (default: 4)

HTTPS
  --tls MODE           auto (default) | local | certbot | none
                       auto = Let's Encrypt for a public domain, local CA for .internal/.local/IP
  --tls-tool TOOL      auto (default) | mkcert | openssl   (how the local CA signs certificates)
  --san a,b            Extra names/IPs to put in the local certificate
  --email ADDR         Let's Encrypt contact address
  --central-ca SRC     (center) file or URL of the Central's root CA, to trust https://central

Center role
  --central-url / --sync-token / --public-key   (required for center on the first run)
  --app-version / --pull-interval / --push-interval / --setup-cron / --no-cron

Wi-Fi
  --wifi-ssid / --wifi-password / --skip-wifi
USAGE
}

# ============================================================
# ARGUMENTS
# ============================================================
APP_ROLE=""; DB_PASS=""; REDIS_PASS=""; LAN_IP_OVERRIDE=""; DOMAIN=""; REPO_BRANCH=""
DEFAULT_ADMIN_EMAIL=""; DEFAULT_ADMIN_PASSWORD=""
CENTRAL_URL=""; CENTER_SYNC_TOKEN=""; CENTRAL_PUBLIC_KEY=""
APP_VERSION=""; PULL_INTERVAL=""; PUSH_INTERVAL=""; SETUP_CRON_FLAG=""
SKIP_WIFI=0; DRY_RUN=0; SHOW_HELP=0; LIST_SECTIONS=0; WIFI_SSID=""; WIFI_PASSWORD=""
PHP_VERSION_FORCE=""; SKIP_UPGRADE=0; REPO_URL=""
FIX_IP=0; FIX_IP_ADDR=""; DHCP_REQ=0; NET_GATEWAY=""; NET_DNS=""; NET_IFACE=""; FORCE=0; FULL_RUN=0
EXPECTED_STUDENTS=""; FPM_CHILDREN=""; QUEUE_WORKER_PROCESSES=""
TLS_MODE=""; TLS_TOOL=""; TLS_SANS=""; TLS_EMAIL=""; TLS_CERT=""; TLS_KEY=""; CENTRAL_CA=""
FROM_SECTION=""; FIXED_IP=""; PROVISIONED=""; CURRENT_USER=""; LAN_IP=""
ONLY_LIST=(); SKIP_LIST=()

need_val() { [[ $# -ge 2 && -n "$2" ]] || { echo "Option $1 requires a value." >&2; exit 1; }; }

split_csv() {   # split_csv ARRAY_NAME "a,b,c"  (appends)
    local -n _target="$1"
    local -a _parts=()
    local item
    IFS=',' read -ra _parts <<<"$2"
    for item in "${_parts[@]}"; do
        if [[ -n "$item" ]]; then _target+=("$item"); fi
    done
}

parse_args() {
    while [[ $# -gt 0 ]]; do
        case "$1" in
            --app-role)         need_val "$@"; APP_ROLE="$2";              shift 2 ;;
            --db-password)      need_val "$@"; DB_PASS="$2";               shift 2 ;;
            --redis-password)   need_val "$@"; REDIS_PASS="$2";            shift 2 ;;
            --ip)               need_val "$@"; LAN_IP_OVERRIDE="$2";       shift 2 ;;
            --domain)           need_val "$@"; DOMAIN="$2";                shift 2 ;;
            --repo-url)         need_val "$@"; REPO_URL="$2";              shift 2 ;;
            --repo-branch)      need_val "$@"; REPO_BRANCH="$2";           shift 2 ;;
            --admin-email)      need_val "$@"; DEFAULT_ADMIN_EMAIL="$2";   shift 2 ;;
            --admin-password)   need_val "$@"; DEFAULT_ADMIN_PASSWORD="$2"; shift 2 ;;
            --central-url)      need_val "$@"; CENTRAL_URL="$2";           shift 2 ;;
            --sync-token)       need_val "$@"; CENTER_SYNC_TOKEN="$2";     shift 2 ;;
            --public-key)       need_val "$@"; CENTRAL_PUBLIC_KEY="$2";    shift 2 ;;
            --app-version)      need_val "$@"; APP_VERSION="$2";           shift 2 ;;
            --pull-interval)    need_val "$@"; PULL_INTERVAL="$2";         shift 2 ;;
            --push-interval)    need_val "$@"; PUSH_INTERVAL="$2";         shift 2 ;;
            --setup-cron)       SETUP_CRON_FLAG="Y";                       shift ;;
            --no-cron)          SETUP_CRON_FLAG="N";                       shift ;;
            --wifi-ssid)        need_val "$@"; WIFI_SSID="$2";             shift 2 ;;
            --wifi-password)    need_val "$@"; WIFI_PASSWORD="$2";         shift 2 ;;
            --skip-wifi)        SKIP_WIFI=1;                               shift ;;
            --php-version)      need_val "$@"; PHP_VERSION_FORCE="$2";     shift 2 ;;
            --skip-upgrade)     SKIP_UPGRADE=1;                            shift ;;
            --fix-ip)
                FIX_IP=1
                if [[ $# -gt 1 && "$2" =~ ^[0-9]+(\.[0-9]+){3}(/[0-9]+)?$ ]]; then FIX_IP_ADDR="$2"; shift 2; else shift; fi ;;
            --dhcp)             DHCP_REQ=1;                                shift ;;
            --gateway)          need_val "$@"; NET_GATEWAY="$2";           shift 2 ;;
            --dns)              need_val "$@"; NET_DNS="$2";               shift 2 ;;
            --iface)            need_val "$@"; NET_IFACE="$2";             shift 2 ;;
            --force)            FORCE=1;                                   shift ;;
            --full)             FULL_RUN=1;                                shift ;;
            --only|--section)   need_val "$@"; split_csv ONLY_LIST "$2";   shift 2 ;;
            --skip)             need_val "$@"; split_csv SKIP_LIST "$2";   shift 2 ;;
            --from)             need_val "$@"; FROM_SECTION="$2";          shift 2 ;;
            --list-sections)    LIST_SECTIONS=1;                           shift ;;
            --students)         need_val "$@"; EXPECTED_STUDENTS="$2";     shift 2 ;;
            --fpm-children)     need_val "$@"; FPM_CHILDREN="$2";          shift 2 ;;
            --queue-workers)    need_val "$@"; QUEUE_WORKER_PROCESSES="$2"; shift 2 ;;
            --tls)              need_val "$@"; TLS_MODE="$2";              shift 2 ;;
            --tls-tool)         need_val "$@"; TLS_TOOL="$2";              shift 2 ;;
            --san)              need_val "$@"; TLS_SANS="$2";              shift 2 ;;
            --email)            need_val "$@"; TLS_EMAIL="$2";             shift 2 ;;
            --central-ca)       need_val "$@"; CENTRAL_CA="$2";            shift 2 ;;
            --dry-run)          DRY_RUN=1;                                 shift ;;
            --help|-h)          SHOW_HELP=1;                               shift ;;
            *)                  echo "Unknown option: $1"; echo "Run with --help for usage."; exit 1 ;;
        esac
    done
}

# ============================================================
# OUTPUT HELPERS
# ============================================================
RED='\033[0;31m'; GREEN='\033[0;32m'; YELLOW='\033[1;33m'
BLUE='\033[0;34m'; CYAN='\033[0;36m'; BOLD='\033[1m'; NC='\033[0m'

_log()    { echo -e "\n\033[1;32m==>\033[0m \033[1m$1\033[0m"; }
info()    { echo -e "${BLUE}[INFO]${NC}   $*"; }
ok()      { echo -e "${GREEN}[OK]${NC}     $*"; }
warn()    { echo -e "${YELLOW}[WARN]${NC}   $*"; }
die()     { echo -e "${RED}[FATAL]${NC}  $*" >&2; exit 1; }
dry()     { echo -e "${BLUE}[DRY-RUN]${NC} $*"; }
banner()  { echo -e "\n${BOLD}${CYAN}┌─ $* ${NC}"; }

# ============================================================
# GENERIC HELPERS
# ============================================================
# Run a command as www-data (the PHP-FPM user). Everything artisan/composer/npm creates must be
# owned by www-data, otherwise the web app later hits "Permission denied" -> HTTP 500.
WWW_HOME="/tmp/${APP_NAME}-www-home"
as_www() {
    sudo -u www-data -H env HOME="$WWW_HOME" COMPOSER_HOME="${WWW_HOME}/composer" \
        npm_config_cache="${WWW_HOME}/npm" "$@"
}

# set_env FILE KEY VALUE — update KEY, un-comment "# KEY=" (Laravel 11 ships DB_* commented out), or append.
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

# conf_set FILE KEY VALUE — "key = value" style files (php-fpm pool, php.ini); un-comments ;key / #key.
conf_set() {
    local file="$1" key="$2" value="$3" kre esc
    kre="${key//./\\.}"
    esc="$(printf '%s' "$value" | sed -e 's/[\\&|]/\\&/g')"
    if grep -qE "^[;#]?[[:space:]]*${kre}[[:space:]]*=" "$file"; then
        sed -i -E "0,/^[;#]?[[:space:]]*${kre}[[:space:]]*=/{s|^[;#]?[[:space:]]*${kre}[[:space:]]*=.*|${key} = ${esc}|}" "$file"
    else
        if [[ -s "$file" && -n "$(tail -c1 "$file")" ]]; then echo >> "$file"; fi
        printf '%s = %s\n' "$key" "$value" >> "$file"
    fi
}

# redis_set FILE KEY VALUE — "key value" style (redis.conf)
redis_set() {
    local file="$1" key="$2" value="$3"
    if grep -qE "^${key}[[:space:]]" "$file"; then
        sed -i -E "s|^${key}[[:space:]].*|${key} ${value}|" "$file"
    else
        if [[ -s "$file" && -n "$(tail -c1 "$file")" ]]; then echo >> "$file"; fi
        printf '%s %s\n' "$key" "$value" >> "$file"
    fi
}

clamp() { local v="$1" lo="$2" hi="$3"; if (( v < lo )); then v="$lo"; fi; if (( v > hi )); then v="$hi"; fi; echo "$v"; }

need_project() {
    [[ -f "${NEW_PROJECT_DIR}/artisan" ]] \
        || die "Project not found in ${NEW_PROJECT_DIR} — run the 'code' section first (e.g. --only app)."
}

app_scheme() { if [[ -n "${TLS_CERT:-}" ]]; then echo https; else echo http; fi; }

detect_php() {
    if ! command -v php >/dev/null 2>&1; then return 0; fi
    PHP_VER="$(php -r 'echo PHP_MAJOR_VERSION.".".PHP_MINOR_VERSION;')"
    PHP_FPM_SERVICE="php${PHP_VER}-fpm"
    PHP_FPM_SOCK="/run/php/php${PHP_VER}-fpm.sock"
}
PHP_VER=""; PHP_FPM_SERVICE=""; PHP_FPM_SOCK=""

update_hosts() {
    if grep -q "[[:space:]]${DOMAIN}\$" /etc/hosts; then
        sed -i "s|.*[[:space:]]${DOMAIN}\$|${LAN_IP} ${DOMAIN}|" /etc/hosts
    else
        echo "${LAN_IP} ${DOMAIN}" >> /etc/hosts
    fi
    info "/etc/hosts: ${LAN_IP} ${DOMAIN}"
}

# ============================================================
# STATE (remembered between runs)
# ============================================================
save_state() {
    if (( DRY_RUN )); then return 0; fi
    local k tmp
    mkdir -p "$STATE_DIR"
    chmod 755 "$STATE_DIR"
    tmp="$(mktemp "${STATE_DIR}/.setup.XXXXXX")"
    {
        echo "# ${APP_NAME} setup settings — written $(date -Is). Used as defaults on later runs; CLI flags override."
        for k in "${STATE_KEYS[@]}"; do printf '%s=%s\n' "$k" "${!k:-}"; done
    } > "$tmp"
    chmod 600 "$tmp"
    mv "$tmp" "$STATE_FILE"
}

load_state() {
    if [[ ! -f "$STATE_FILE" ]]; then return 0; fi
    local line k v key cur
    while IFS= read -r line || [[ -n "$line" ]]; do
        [[ "$line" =~ ^[A-Z_]+= ]] || continue
        k="${line%%=*}"; v="${line#*=}"
        for key in "${STATE_KEYS[@]}"; do
            if [[ "$key" == "$k" ]]; then
                cur="${!key:-}"
                if [[ -z "$cur" ]]; then printf -v "$key" '%s' "$v"; fi
            fi
        done
    done < "$STATE_FILE"
}

# Older runs kept DB/Redis passwords only in the credentials file.
load_credentials_fallback() {
    if [[ ! -f "$CREDS_FILE" ]]; then return 0; fi
    if [[ -z "$DB_PASS" ]]; then
        DB_PASS="$(grep '^Database pass:' "$CREDS_FILE" 2>/dev/null | sed 's/^Database pass:[[:space:]]*//' | xargs || true)"
    fi
    if [[ -z "$REDIS_PASS" ]]; then
        REDIS_PASS="$(grep '^Redis password:' "$CREDS_FILE" 2>/dev/null | sed 's/^Redis password:[[:space:]]*//' | xargs || true)"
    fi
}

install_self() {
    if (( DRY_RUN )); then return 0; fi
    local src
    src="$(readlink -f "$0" 2>/dev/null || echo "$0")"
    [[ -f "$src" ]] || return 0
    if [[ "$src" != "$SELF_INSTALL_PATH" ]] && ! cmp -s "$src" "$SELF_INSTALL_PATH" 2>/dev/null; then
        install -m 750 "$src" "$SELF_INSTALL_PATH"
        ok "Installed as ${SELF_INSTALL_PATH} — later, run only what you need:  sudo ${APP_NAME}-setup --only <sections>"
    fi
}

# ============================================================
# SIZING — derived from RAM and the expected number of students
# ============================================================
compute_sizing() {
    local ram_mb reserved by_ram by_load
    ram_mb="${RAM_MB_OVERRIDE:-$(awk '/^MemTotal:/ {printf "%d", $2/1024}' /proc/meminfo)}"

    INNODB_POOL_MB="$(clamp $(( ram_mb / 4 )) 256 16384)"          # 25% of RAM  (32 GB -> ~8 GB)
    REDIS_MAXMEM_MB="$(clamp $(( ram_mb / 16 )) 128 2048)"         # 6%          (32 GB -> 2 GB)
    reserved=$(( INNODB_POOL_MB + REDIS_MAXMEM_MB + 3072 ))        # + 3 GB for OS, Nginx, Supervisor, headroom
    by_ram=$(( (ram_mb - reserved) / 100 ))                        # ~100 MB per PHP-FPM worker (Livewire app)
    by_load=$(( EXPECTED_STUDENTS / 2 ))                           # every other student mid-request is a worst case
    if [[ -n "$FPM_CHILDREN" ]]; then
        PHP_MAX_CHILDREN="$FPM_CHILDREN"
    else
        PHP_MAX_CHILDREN="$(clamp $(( by_ram < by_load ? by_ram : by_load )) 10 400)"
    fi
    PHP_MIN_SPARE="$(clamp $(( PHP_MAX_CHILDREN / 10 )) 2 1000)"
    PHP_MAX_SPARE="$(clamp $(( PHP_MAX_CHILDREN / 3 )) $(( PHP_MIN_SPARE + 2 )) 1000)"
    PHP_START_SERVERS="$(clamp $(( PHP_MAX_CHILDREN / 5 )) "$PHP_MIN_SPARE" "$PHP_MAX_SPARE")"
    DB_MAX_CONN="$(clamp $(( PHP_MAX_CHILDREN + 150 )) 300 2000)"
    SIZING_RAM_MB="$ram_mb"
}
INNODB_POOL_MB=0; REDIS_MAXMEM_MB=0; PHP_MAX_CHILDREN=0; PHP_MIN_SPARE=0; PHP_MAX_SPARE=0
PHP_START_SERVERS=0; DB_MAX_CONN=0; SIZING_RAM_MB=0

# ============================================================
# NETWORK HELPERS (fixed IP)
# ============================================================
valid_ipv4() {
    local ip="$1" o
    [[ "$ip" =~ ^[0-9]{1,3}(\.[0-9]{1,3}){3}$ ]] || return 1
    local IFS=.
    for o in $ip; do
        (( 10#$o <= 255 )) || return 1
    done
    return 0
}

ip2int() {
    local IFS=. a b c d
    read -r a b c d <<<"$1"
    echo $(( (10#$a << 24) + (10#$b << 16) + (10#$c << 8) + 10#$d ))
}

in_subnet() {   # in_subnet IP REFERENCE_IP PREFIX
    local mask=$(( (0xFFFFFFFF << (32 - $3)) & 0xFFFFFFFF ))
    (( ( $(ip2int "$1") & mask ) == ( $(ip2int "$2") & mask ) ))
}

net_detect_iface() {
    local dev
    dev="$(ip -4 route show default 2>/dev/null | awk '{for(i=1;i<=NF;i++) if($i=="dev"){print $(i+1); exit}}')"
    if [[ -z "$dev" ]]; then
        dev="$(ip -o link show 2>/dev/null | awk -F': ' '$2 ~ /^(en|eth)/ && /state UP/ {print $2; exit}')"
    fi
    if [[ -z "$dev" ]]; then
        dev="$(ip -o link show 2>/dev/null | awk -F': ' '$2 ~ /^(en|eth|wl)/ {print $2; exit}')"
    fi
    printf '%s' "${dev%%@*}"
}

# Sets CUR_IP CUR_PREFIX CUR_GW for an interface (empty when unset)
net_current() {
    local cidr
    cidr="$(ip -4 -o addr show dev "$1" scope global 2>/dev/null | awk '{print $4; exit}')"
    CUR_IP="${cidr%/*}"
    CUR_PREFIX=""
    if [[ -n "$cidr" ]]; then CUR_PREFIX="${cidr#*/}"; fi
    CUR_GW="$(ip -4 route show default dev "$1" 2>/dev/null | awk '{print $3; exit}')"
}

net_current_dns() {
    local d=""
    if command -v resolvectl >/dev/null 2>&1; then
        d="$(resolvectl dns "$1" 2>/dev/null | grep -oE '[0-9]+(\.[0-9]+){3}' | paste -sd, - || true)"
    fi
    if [[ -z "$d" ]]; then
        d="$(grep -E '^nameserver' /etc/resolv.conf 2>/dev/null | awk '{print $2}' | grep -vE '^127\.' | paste -sd, - || true)"
    fi
    printf '%s' "$d"
}

# 0 = another host already answers for this address
net_ip_in_use() {
    local iface="$1" ip="$2" rc=0
    if command -v arping >/dev/null 2>&1; then
        arping -D -q -c 2 -w 3 -I "$iface" "$ip" >/dev/null 2>&1 || rc=$?
        case "$rc" in
            0) return 1 ;;     # no reply  -> free
            1) return 0 ;;     # duplicate -> in use
        esac                   # anything else: arping could not run, fall back to ping
    fi
    if ping -c1 -W1 "$ip" >/dev/null 2>&1; then return 0; fi
    return 1
}

net_uses_nm() {
    command -v nmcli >/dev/null 2>&1 || return 1
    systemctl is-active --quiet NetworkManager 2>/dev/null || return 1
    local st
    st="$(nmcli -t -f DEVICE,STATE device 2>/dev/null | awk -F: -v d="$1" '$1==d {print $2}')"
    [[ -n "$st" && "$st" != "unmanaged" ]]
}

# net_verify IFACE EXPECTED_IP|"" GATEWAY|""  — address present and gateway reachable
net_verify() {
    local iface="$1" want_ip="$2" gw="$3" i out="" route_gw=""
    for i in 1 2 3 4 5 6 7 8 9 10; do
        out="$(ip -4 -o addr show dev "$iface" scope global 2>/dev/null || true)"
        if [[ -n "$want_ip" ]]; then
            if [[ "$out" == *" ${want_ip}/"* ]]; then break; fi
        elif [[ -n "$out" ]]; then
            break
        fi
        sleep 2
    done
    if [[ -z "$out" ]]; then return 1; fi
    if [[ -n "$want_ip" && "$out" != *" ${want_ip}/"* ]]; then return 1; fi
    if [[ -z "$gw" ]]; then
        for i in 1 2 3 4 5; do
            route_gw="$(ip -4 route show default dev "$iface" 2>/dev/null | awk '{print $3; exit}')"
            if [[ -n "$route_gw" ]]; then break; fi
            sleep 2
        done
        gw="$route_gw"
    fi
    if [[ -z "$gw" ]]; then return 1; fi
    # Some routers drop ICMP, so ARP counts as reachable too.
    ping -c2 -W2 "$gw" >/dev/null 2>&1 || arping -c2 -w3 -I "$iface" "$gw" >/dev/null 2>&1
}

# net_apply_nm MODE IFACE IP PREFIX GW DNS   (MODE = static|dhcp)
net_apply_nm() {
    local mode="$1" iface="$2" ip="$3" prefix="$4" gw="$5" dns="$6" con
    local old_m old_a old_g old_d
    con="$(nmcli -g GENERAL.CONNECTION device show "$iface" 2>/dev/null || true)"
    [[ -n "$con" && "$con" != "--" ]] || die "NetworkManager has no active connection on ${iface} — connect first (e.g. the Wi-Fi section)."
    old_m="$(nmcli -g ipv4.method connection show "$con")"
    old_a="$(nmcli -g ipv4.addresses connection show "$con")"
    old_g="$(nmcli -g ipv4.gateway connection show "$con")"
    old_d="$(nmcli -g ipv4.dns connection show "$con")"

    if [[ "$mode" == "static" ]]; then
        nmcli connection modify "$con" ipv4.method manual ipv4.addresses "${ip}/${prefix}" \
            ipv4.gateway "$gw" ipv4.dns "$dns" ipv4.ignore-auto-dns yes
    else
        nmcli connection modify "$con" ipv4.method auto ipv4.addresses "" ipv4.gateway "" \
            ipv4.dns "" ipv4.ignore-auto-dns no
    fi
    nmcli connection up "$con" >/dev/null 2>&1 || true

    if [[ "$mode" == "static" ]] && net_verify "$iface" "$ip" "$gw"; then return 0; fi
    if [[ "$mode" == "dhcp" ]] && net_verify "$iface" "" ""; then return 0; fi

    warn "New network settings did not work — restoring the previous ones"
    if [[ "$old_m" == "manual" ]]; then
        nmcli connection modify "$con" ipv4.method manual ipv4.addresses "$old_a" ipv4.gateway "$old_g" ipv4.dns "$old_d" || true
    else
        nmcli connection modify "$con" ipv4.method auto ipv4.addresses "" ipv4.gateway "" ipv4.dns "" || true
    fi
    nmcli connection up "$con" >/dev/null 2>&1 || true
    die "Could not apply ${mode} IPv4 settings on ${iface}; previous configuration restored."
}

# net_apply_netplan MODE IFACE IP PREFIX GW DNS
net_apply_netplan() {
    local mode="$1" iface="$2" ip="$3" prefix="$4" gw="$5" dns="$6" backup=""
    command -v netplan >/dev/null 2>&1 \
        || die "Neither NetworkManager nor netplan manages ${iface} — set the static address manually."
    if [[ -e "/sys/class/net/${iface}/wireless" ]]; then
        die "${iface} is a Wi-Fi interface not managed by NetworkManager — connect it with the 'wifi' section first (NetworkManager), then re-run."
    fi
    if [[ -f "$NETPLAN_FILE" ]]; then backup="$(mktemp)"; cp -p "$NETPLAN_FILE" "$backup"; fi

    if [[ "$mode" == "static" ]]; then
        cat > "$NETPLAN_FILE" <<YAML
# Managed by the ${APP_NAME} setup script (--fix-ip). Delete this file and run 'netplan apply' to undo.
network:
  version: 2
  ethernets:
    ${iface}:
      dhcp4: false
      addresses: [${ip}/${prefix}]
      routes:
        - to: 0.0.0.0/0
          via: ${gw}
      nameservers:
        addresses: [${dns//,/, }]
YAML
    else
        cat > "$NETPLAN_FILE" <<YAML
# Managed by the ${APP_NAME} setup script (--dhcp).
network:
  version: 2
  ethernets:
    ${iface}:
      dhcp4: true
YAML
        if grep -lE 'addresses:[[:space:]]*\[?[0-9]' /etc/netplan/*.yaml 2>/dev/null | grep -qv "$NETPLAN_FILE"; then
            warn "Another file in /etc/netplan also defines a static address — edit it if DHCP does not take effect."
        fi
    fi
    chmod 600 "$NETPLAN_FILE"

    if ! netplan generate 2>&1; then
        if [[ -n "$backup" ]]; then cp -p "$backup" "$NETPLAN_FILE"; else rm -f "$NETPLAN_FILE"; fi
        die "netplan rejected the generated configuration (restored the previous one)."
    fi
    netplan apply

    if [[ "$mode" == "static" ]] && net_verify "$iface" "$ip" "$gw"; then return 0; fi
    if [[ "$mode" == "dhcp" ]] && net_verify "$iface" "" ""; then return 0; fi

    warn "New network settings did not work — restoring the previous ones"
    if [[ -n "$backup" ]]; then cp -p "$backup" "$NETPLAN_FILE"; else rm -f "$NETPLAN_FILE"; fi
    netplan apply || true
    die "Could not apply ${mode} IPv4 settings on ${iface}; previous configuration restored."
}

net_apply() {
    if net_uses_nm "$2"; then net_apply_nm "$@"; else net_apply_netplan "$@"; fi
}

# Things that embed the server address: /etc/hosts, the local certificate, the Nginx site.
refresh_ip_dependents() {
    update_hosts
    if [[ -f "/etc/nginx/sites-available/${DOMAIN}" ]]; then
        detect_php
        if [[ -n "${TLS_CERT:-}" ]]; then
            sec_tls            # re-issues the local certificate for the new IP and rewrites Nginx
        else
            write_nginx_conf
        fi
    fi
    if [[ "$APP_ROLE" == "central" ]]; then
        warn "Centers that sync with this server use its address (CENTRAL_URL). On each center run:"
        warn "  sudo ${APP_NAME}-setup --only env,sync --central-url $(app_scheme)://${LAN_IP}"
    fi
}

# ============================================================
# SECTION: ip
# ============================================================
sec_ip() {
    if (( ! FIX_IP && ! DHCP_REQ )); then
        info "No --fix-ip / --dhcp requested — leaving the network configuration alone"
        return 0
    fi
    if (( DRY_RUN )); then
        if (( DHCP_REQ )); then dry "Switch the LAN interface back to DHCP (netplan/NetworkManager, with rollback)"
        else dry "Pin the LAN IP (${FIX_IP_ADDR:-current address}) via netplan/NetworkManager, with duplicate-IP check and rollback"; fi
        return 0
    fi
    command -v ip >/dev/null 2>&1 || die "'ip' command not found (install iproute2)."
    apt-get install -y iputils-arping >/dev/null 2>&1 || true

    local iface="${NET_IFACE:-$(net_detect_iface)}"
    [[ -n "$iface" ]] || die "Could not find a network interface — pass --iface NAME."
    NET_IFACE="$iface"
    net_current "$iface"
    info "Interface ${iface}: current address ${CUR_IP:-none}/${CUR_PREFIX:-?}, gateway ${CUR_GW:-none}"

    if (( DHCP_REQ )); then
        _log "Switching ${iface} back to DHCP"
        warn "The address will probably change; SSH sessions will drop. Progress is logged to ${LOG_FILE}."
        trap '' HUP
        exec >>"$LOG_FILE" 2>&1 </dev/null
        net_apply dhcp "$iface" "" "" "" ""
        net_current "$iface"
        LAN_IP="$CUR_IP"; FIXED_IP=0; NET_GATEWAY=""; NET_DNS=""
        ok "DHCP active on ${iface}: ${LAN_IP} (gateway ${CUR_GW:-?})"
        save_state
        refresh_ip_dependents
        return 0
    fi

    # ── Decide the target ────────────────────────────────────────────────────
    local target="$FIX_IP_ADDR" prefix="" gw="$NET_GATEWAY" dns="$NET_DNS"
    if [[ -z "$target" ]]; then
        [[ -n "$CUR_IP" ]] || die "No IPv4 address on ${iface} to pin. Pass one: --fix-ip 192.168.1.50/24 --gateway 192.168.1.1"
        target="$CUR_IP"; prefix="$CUR_PREFIX"
    fi
    if [[ "$target" == */* ]]; then prefix="${target#*/}"; target="${target%/*}"; fi
    if [[ -z "$prefix" ]]; then prefix="${CUR_PREFIX:-24}"; fi
    valid_ipv4 "$target" || die "'${target}' is not a valid IPv4 address."
    (( prefix >= 8 && prefix <= 30 )) || die "Prefix length /${prefix} is out of range (8-30)."

    if [[ -z "$gw" ]]; then
        if [[ -n "$CUR_GW" ]] && in_subnet "$target" "$CUR_GW" "$prefix"; then
            gw="$CUR_GW"
        else
            die "Need a gateway for ${target}/${prefix}: pass --gateway (it must be inside the same subnet)."
        fi
    fi
    valid_ipv4 "$gw" || die "'${gw}' is not a valid gateway address."
    in_subnet "$target" "$gw" "$prefix" || die "Gateway ${gw} is not inside ${target}/${prefix}."
    if [[ -z "$dns" ]]; then dns="$(net_current_dns "$iface")"; fi
    if [[ -z "$dns" ]]; then dns="${gw},1.1.1.1"; fi

    if [[ "${FIXED_IP:-}" == "1" && "$CUR_IP" == "$target" && "$CUR_GW" == "$gw" ]] && (( ! FORCE )); then
        ok "${target}/${prefix} via ${gw} is already pinned on ${iface}"
        LAN_IP="$target"; NET_GATEWAY="$gw"; NET_DNS="$dns"
        save_state
        refresh_ip_dependents
        return 0
    fi

    # ── Safety checks ────────────────────────────────────────────────────────
    if [[ "$target" != "$CUR_IP" ]]; then
        if net_ip_in_use "$iface" "$target"; then
            if (( FORCE )); then warn "${target} already answers on the network — continuing because of --force"
            else die "${target} already answers on this network. Choose a free address (or use --force)."; fi
        fi
        warn "IP changes ${CUR_IP:-none} -> ${target}. Over SSH the session will drop;"
        warn "the script keeps running — follow it with:  tail -f ${LOG_FILE}   (reconnect to ${target})"
        trap '' HUP
        exec >>"$LOG_FILE" 2>&1 </dev/null
    else
        info "Pinning the address this server already has (${target}) so DHCP can no longer change it."
        warn "Reserve ${target} on the router (or pick one outside the DHCP pool) so it is never handed to another device."
    fi

    _log "Fixing IP ${target}/${prefix} via ${gw} on ${iface} (DNS: ${dns})"
    net_apply static "$iface" "$target" "$prefix" "$gw" "$dns"
    ok "Fixed IP active: ${target}/${prefix}, gateway ${gw}"

    LAN_IP="$target"; FIXED_IP=1; NET_GATEWAY="$gw"; NET_DNS="$dns"
    save_state
    refresh_ip_dependents
}

# ============================================================
# SECTION: wifi
# ============================================================
sec_wifi() {
    if (( SKIP_WIFI )); then info "Skipping Wi-Fi setup (--skip-wifi)"; return 0; fi
    if (( DRY_RUN )); then dry "Connect to Wi-Fi: ${WIFI_SSID:-$DEFAULT_WIFI_SSID} (password hidden)"; return 0; fi
    local ssid="${WIFI_SSID:-$DEFAULT_WIFI_SSID}" pass="${WIFI_PASSWORD:-$DEFAULT_WIFI_PASSWORD}" cur new

    if ! command -v nmcli >/dev/null 2>&1; then
        _log "Installing NetworkManager"
        apt-get install -y network-manager || warn "Failed to install network-manager — Wi-Fi setup will be skipped."
    fi
    if ! command -v nmcli >/dev/null 2>&1; then warn "nmcli not available — skipping Wi-Fi setup."; return 0; fi

    cur="$(nmcli -t -f active,ssid dev wifi 2>/dev/null | grep '^yes:' | cut -d: -f2 || true)"
    if [[ "$cur" == "$ssid" ]]; then ok "Already connected to Wi-Fi: ${ssid}"; return 0; fi

    if nmcli connection show | grep -q "$ssid"; then
        _log "Connection profile for '${ssid}' exists — activating"
        nmcli connection up id "$ssid" 2>&1 || warn "Could not activate connection '${ssid}'."
    else
        _log "Connecting to Wi-Fi: ${ssid}"
        nmcli device wifi connect "$ssid" password "$pass" 2>&1 || warn "Could not connect to Wi-Fi '${ssid}'."
    fi
    sleep 3
    new="$(nmcli -t -f active,ssid dev wifi 2>/dev/null | grep '^yes:' | cut -d: -f2 || true)"
    if [[ "$new" == "$ssid" ]]; then ok "Connected to Wi-Fi: ${ssid}"
    else warn "Wi-Fi connection to '${ssid}' may have failed — continuing anyway."; fi
}

# ============================================================
# SECTION: packages
# ============================================================
sec_packages() {
    if (( DRY_RUN )); then dry "apt-get update (+ upgrade), base utilities, SSH, www-data group membership"; return 0; fi
    _log "Updating system packages"
    apt-get update -y
    if (( ! SKIP_UPGRADE )); then apt-get upgrade -y; else info "Skipping apt-get upgrade (--skip-upgrade)"; fi

    _log "Installing base utilities"
    apt-get install -y \
        curl wget git unzip zip rsync openssl acl iputils-arping \
        ca-certificates gnupg lsb-release apt-transport-https \
        ufw supervisor cron openssh-server
    systemctl enable --now ssh

    _log "Installing minimal desktop (XFCE + XRDP for remote admin)"
    apt-get install -y xfce4 xfce4-goodies lightdm xrdp
    echo "xfce4-session" > /root/.xsession
    [[ -n "${SUDO_USER:-}" ]] && echo "xfce4-session" > "/home/${SUDO_USER}/.xsession"
    systemctl enable xrdp

    _log "Configuring user groups"
    if [[ "$CURRENT_USER" == "root" ]]; then
        info "Running as plain root (no sudo user) — skipping group membership changes"
    else
        usermod -aG www-data "$CURRENT_USER" || true
        usermod -aG "$CURRENT_USER" www-data || true
        ok "Added ${CURRENT_USER} <-> www-data to each other's groups"
    fi
}

# ============================================================
# SECTION: tuning  (kernel / network limits for ~400 concurrent users)
# ============================================================
sec_tuning() {
    if (( DRY_RUN )); then dry "Write /etc/sysctl.d/99-${APP_NAME}.conf (somaxconn, backlog, file handles, swappiness)"; return 0; fi
    cat > "/etc/sysctl.d/99-${APP_NAME}.conf" <<SYSCTL
# Managed by the ${APP_NAME} setup script — exam-day bursts: hundreds of browsers connecting at once
net.core.somaxconn = 4096
net.ipv4.tcp_max_syn_backlog = 4096
net.ipv4.ip_local_port_range = 10240 65535
net.ipv4.tcp_fin_timeout = 20
fs.file-max = 500000
vm.swappiness = 10
SYSCTL
    sysctl -q -p "/etc/sysctl.d/99-${APP_NAME}.conf" || warn "Could not apply some sysctl settings (they take effect on reboot)."
    ok "Kernel limits tuned (somaxconn=4096, file-max=500000)"
}

# ============================================================
# SECTION: mariadb
# ============================================================
sec_mariadb() {
    if (( DRY_RUN )); then
        dry "Install MariaDB; innodb_buffer_pool=${INNODB_POOL_MB}M, max_connections=${DB_MAX_CONN}; create ${DB_NAME} + ${DB_USER}"
        return 0
    fi
    _log "Installing MariaDB"
    apt-get install -y mariadb-server mariadb-client
    systemctl enable mariadb
    systemctl start mariadb

    _log "Tuning MariaDB for ~${EXPECTED_STUDENTS} concurrent students (RAM ${SIZING_RAM_MB} MB)"
    local cnf="/etc/mysql/mariadb.conf.d/99-${APP_NAME}.cnf"
    cat > "$cnf" <<CNF
# Managed by the ${APP_NAME} setup script
[mysqld]
bind-address                   = 127.0.0.1
max_connections                = ${DB_MAX_CONN}
thread_cache_size              = 64
table_open_cache               = 4096
innodb_buffer_pool_size        = ${INNODB_POOL_MB}M
innodb_log_file_size           = 1G
innodb_flush_method            = O_DIRECT
# Fully durable commits: a portable LAN server can lose power mid-exam, and answers must survive that.
innodb_flush_log_at_trx_commit = 1
tmp_table_size                 = 128M
max_heap_table_size            = 128M
max_allowed_packet             = 64M
slow_query_log                 = 1
slow_query_log_file            = /var/log/mysql/mariadb-slow.log
long_query_time                = 1
character-set-server           = utf8mb4
collation-server               = utf8mb4_unicode_ci
CNF
    mkdir -p /var/log/mysql && chown mysql:adm /var/log/mysql 2>/dev/null || true
    if ! systemctl restart mariadb; then
        warn "MariaDB refused the tuned settings — removing them and starting with defaults"
        journalctl -u mariadb -n 15 --no-pager 2>/dev/null || true
        rm -f "$cnf"
        systemctl restart mariadb
        warn "MariaDB is running on defaults; fix ${cnf} and re-run:  --only mariadb"
    else
        ok "MariaDB tuned: buffer pool ${INNODB_POOL_MB}M, max_connections ${DB_MAX_CONN}"
    fi

    _log "Hardening MariaDB"
    mysql -u root <<'SQL'
DELETE FROM mysql.global_priv WHERE User='';
DELETE FROM mysql.global_priv WHERE User='root' AND Host NOT IN ('localhost', '127.0.0.1', '::1');
DROP DATABASE IF EXISTS test;
DELETE FROM mysql.db WHERE Db='test' OR Db='test\\_%';
FLUSH PRIVILEGES;
SQL

    _log "Creating application database and user (localhost + 127.0.0.1)"
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
    ok "Database password set for ${DB_USER}@localhost and ${DB_USER}@127.0.0.1"
}

# ============================================================
# SECTION: redis
# ============================================================
sec_redis() {
    if (( DRY_RUN )); then dry "Install Redis; localhost only, requirepass, maxmemory ${REDIS_MAXMEM_MB}mb (volatile-lru)"; return 0; fi
    _log "Installing Redis"
    apt-get install -y redis-server
    local conf="/etc/redis/redis.conf"
    if [[ -f "$conf" ]]; then
        redis_set "$conf" supervised systemd
        redis_set "$conf" bind "127.0.0.1 -::1"
        redis_set "$conf" requirepass "$REDIS_PASS"
        redis_set "$conf" maxmemory "${REDIS_MAXMEM_MB}mb"
        # volatile-lru only evicts keys that have a TTL (sessions, cache) and never touches queue lists.
        redis_set "$conf" maxmemory-policy volatile-lru
        redis_set "$conf" tcp-backlog 1024
    fi
    systemctl enable redis-server
    systemctl restart redis-server
    sleep 1
    if [[ "$(REDISCLI_AUTH="$REDIS_PASS" redis-cli ping 2>/dev/null || true)" == "PONG" ]]; then
        ok "Redis is up (maxmemory ${REDIS_MAXMEM_MB}mb, accepts the configured password)"
    else
        die "Redis did not answer PING with the configured password (sessions/cache/queue use Redis, so the app would 500). Check requirepass in ${conf} and 'systemctl status redis-server'."
    fi
}

# ============================================================
# SECTION: php  (+ Composer)
# ============================================================
# Modules the app needs. A missing one = HTTP 500 at runtime, so they are verified right after install.
REQUIRED_PHP_MODULES=(mbstring curl pdo_mysql redis xml dom zip gd bcmath intl sodium openssl tokenizer fileinfo ctype)

install_php_stack() {
    local ver="$1" ext pkg
    local pkgs=("php${ver}-fpm" "php${ver}-cli" "php${ver}-common")
    # sodium is built into php-common; there is no php-sodium package, and one bad name aborts the WHOLE apt install.
    for ext in mbstring curl mysql redis xml dom zip gd bcmath intl opcache readline; do
        pkgs+=("php${ver}-${ext}")
    done

    _log "Installing PHP ${ver} and extensions"
    if ! apt-get install -y "${pkgs[@]}"; then
        warn "Bulk install failed (a package name probably doesn't exist) — retrying one by one"
        for pkg in "${pkgs[@]}"; do
            apt-get install -y "$pkg" >/dev/null 2>&1 || warn "  could not install ${pkg} (skipped)"
        done
    fi

    cat > "/etc/php/${ver}/fpm/conf.d/99-${APP_NAME}.ini" <<INI
; Managed by the ${APP_NAME} setup script
memory_limit = 256M
upload_max_filesize = 100M
post_max_size = 100M
max_execution_time = 120
max_input_vars = 5000
opcache.enable = 1
opcache.memory_consumption = 256
opcache.interned_strings_buffer = 32
opcache.max_accelerated_files = 30000
opcache.validate_timestamps = 1
opcache.revalidate_freq = 30
INI

    local pool="/etc/php/${ver}/fpm/pool.d/www.conf"
    if [[ -f "$pool" ]]; then
        conf_set "$pool" pm "dynamic"
        conf_set "$pool" pm.max_children "$PHP_MAX_CHILDREN"
        conf_set "$pool" pm.start_servers "$PHP_START_SERVERS"
        conf_set "$pool" pm.min_spare_servers "$PHP_MIN_SPARE"
        conf_set "$pool" pm.max_spare_servers "$PHP_MAX_SPARE"
        conf_set "$pool" pm.max_requests "500"
        conf_set "$pool" request_terminate_timeout "120s"
        conf_set "$pool" listen.backlog "4096"
    else
        warn "PHP-FPM pool config not found at ${pool} — skipping pool tuning."
    fi

    systemctl enable "php${ver}-fpm" >/dev/null 2>&1 || warn "Could not enable php${ver}-fpm."
    systemctl restart "php${ver}-fpm" || warn "Could not restart php${ver}-fpm."
    ok "PHP ${ver}: pm.max_children=${PHP_MAX_CHILDREN} (start ${PHP_START_SERVERS}, spare ${PHP_MIN_SPARE}-${PHP_MAX_SPARE}), memory_limit=256M, uploads=100M"
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

# If composer.json needs a newer PHP than the OS ships (Ubuntu 22.04 = 8.1; Laravel 11/12 need 8.2+), install
# it from ppa:ondrej/php instead of installing with --ignore-platform-reqs and serving HTTP 500s.
ensure_php_version() {
    local constraint="" want=""
    detect_php
    if [[ -n "$PHP_VERSION_FORCE" ]]; then
        want="$PHP_VERSION_FORCE"
    elif [[ -f "${NEW_PROJECT_DIR}/composer.json" ]]; then
        constraint="$(php -r '$j = json_decode(file_get_contents($argv[1]), true); echo $j["require"]["php"] ?? "";' "${NEW_PROJECT_DIR}/composer.json" 2>/dev/null || true)"
        if [[ -n "$constraint" ]]; then want="$(grep -oE '[0-9]+\.[0-9]+' <<<"$constraint" | sort -V | head -1 || true)"; fi
    fi
    if [[ -z "$want" ]]; then return 0; fi

    if [[ -n "$PHP_VERSION_FORCE" ]]; then
        if [[ "$PHP_VER" == "$want" ]]; then ok "PHP ${PHP_VER} (forced via --php-version)"; return 0; fi
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
    # The Nginx site points at the PHP-FPM socket: rewrite it if it already exists.
    if [[ -f "/etc/nginx/sites-available/${DOMAIN}" ]]; then write_nginx_conf; fi
}

sec_php() {
    if (( DRY_RUN )); then
        dry "Install PHP + extensions, pm.max_children=${PHP_MAX_CHILDREN}; verify modules; install Composer"
        return 0
    fi
    if ! command -v php >/dev/null 2>&1; then
        _log "PHP not found — installing default PHP packages"
        apt-get install -y php-cli php-fpm
    fi
    detect_php
    _log "Using PHP ${PHP_VER}"
    install_php_stack "$PHP_VER"
    verify_php_modules
    sleep 1
    [[ -S "$PHP_FPM_SOCK" ]] || warn "PHP-FPM socket ${PHP_FPM_SOCK} not found — Nginx would return 502. Present sockets: $(ls /run/php 2>/dev/null | tr '\n' ' ')"

    if ! command -v composer >/dev/null 2>&1; then
        _log "Installing Composer"
        local expected actual
        expected="$(curl -s https://composer.github.io/installer.sig)"
        curl -sS https://getcomposer.org/installer -o /tmp/composer-setup.php
        actual="$(php -r "echo hash_file('sha384', '/tmp/composer-setup.php');")"
        if [[ -n "$expected" && "$expected" == "$actual" ]]; then
            php /tmp/composer-setup.php --install-dir=/usr/local/bin --filename=composer
            rm -f /tmp/composer-setup.php
        else
            warn "Composer installer signature mismatch — install Composer manually."
        fi
    fi
}

# ============================================================
# SECTION: node
# ============================================================
sec_node() {
    if (( DRY_RUN )); then dry "Install Node.js 22 LTS + npm (if missing or older than 20.19)"; return 0; fi
    local node_ver="" node_ok=0
    if command -v node >/dev/null 2>&1; then
        node_ver="$(node -v 2>/dev/null | tr -d 'v' || true)"
        # Vite needs Node >= 20.19 (or 22.12+); an old distro Node silently breaks `npm run build`.
        if [[ -n "$node_ver" ]] && dpkg --compare-versions "$node_ver" ge 20.19.0; then node_ok=1; fi
    fi
    if (( node_ok )); then
        _log "Node.js already installed: v${node_ver}"
    else
        _log "Installing Node.js 22 LTS (found: ${node_ver:-none})"
        curl -fsSL https://deb.nodesource.com/setup_22.x -o /tmp/nodesource-setup.sh
        bash /tmp/nodesource-setup.sh
        apt-get install -y nodejs
        rm -f /tmp/nodesource-setup.sh
    fi
    if command -v npm >/dev/null 2>&1; then _log "npm version: $(npm --version)"; else warn "npm not found — install manually."; fi
}

# ============================================================
# SECTION: code
# ============================================================
sec_code() {
    if (( DRY_RUN )); then dry "Download ${REPO_BRANCH} tarball into ${NEW_PROJECT_DIR}; set ownership, ACLs, writable storage + public/build"; return 0; fi
    local repo_owner="Mon-Associates-Tech" repo_slug="allacademies"
    local tar_url="https://github.com/${repo_owner}/${repo_slug}/archive/refs/heads/${REPO_BRANCH}.tar.gz"
    local tar_file="/tmp/${APP_NAME}-${REPO_BRANCH}.tar.gz" extract_dir="/tmp/${APP_NAME}-extract"

    mkdir -p /var/www/html
    chown www-data:www-data /var/www/html

    _log "Downloading branch '${REPO_BRANCH}' from GitHub"
    rm -f "$tar_file"
    sudo -u www-data wget --timeout=120 --tries=3 -q -O "$tar_file" "$tar_url" \
        || die "Failed to download ${tar_url} — check network connectivity (and that the repo/branch is public)."

    _log "Extracting archive"
    rm -rf "$extract_dir"
    mkdir -p "$extract_dir"
    tar -xzf "$tar_file" -C "$extract_dir" --strip-components=1
    rm -f "$tar_file"

    if [[ -d "$NEW_PROJECT_DIR" ]]; then
        _log "${NEW_PROJECT_DIR} exists — syncing latest files (preserving .env, storage, vendor, node_modules)"
        # Anchored excludes: an un-anchored 'vendor/' also skipped resources/views/vendor, public/vendor, lang/vendor.
        rsync -a --exclude='/.env' --exclude='/storage/' --exclude='/vendor/' --exclude='/node_modules/' \
            "${extract_dir}/" "${NEW_PROJECT_DIR}/"
    else
        _log "Moving extracted files to ${NEW_PROJECT_DIR}"
        mv "$extract_dir" "$NEW_PROJECT_DIR"
    fi
    rm -rf "$extract_dir"

    _log "Fixing ownership and permissions"
    chown -R "${CURRENT_USER}:www-data" "$NEW_PROJECT_DIR"
    local skip=( \( -path "${NEW_PROJECT_DIR}/vendor" -o -path "${NEW_PROJECT_DIR}/node_modules" -o -path "${NEW_PROJECT_DIR}/storage" \) -prune -o )
    find "$NEW_PROJECT_DIR" "${skip[@]}" -type d -exec chmod 755 {} +
    find "$NEW_PROJECT_DIR" "${skip[@]}" -type f -exec chmod 644 {} +
    chmod 755 "${NEW_PROJECT_DIR}/artisan" 2>/dev/null || true

    mkdir -p "${NEW_PROJECT_DIR}/storage/logs" "${NEW_PROJECT_DIR}/storage/app/public" \
             "${NEW_PROJECT_DIR}/storage/framework/cache/data" "${NEW_PROJECT_DIR}/storage/framework/sessions" \
             "${NEW_PROJECT_DIR}/storage/framework/views" "${NEW_PROJECT_DIR}/bootstrap/cache"
    chown -R www-data:www-data "${NEW_PROJECT_DIR}/storage" "${NEW_PROJECT_DIR}/bootstrap/cache"
    chmod -R 775 "${NEW_PROJECT_DIR}/storage" "${NEW_PROJECT_DIR}/bootstrap/cache"

    # Vite writes the build here as www-data. Without a writable public/build, `npm run build` fails, there is no
    # manifest.json, and every page using @vite throws -> HTTP 500.
    mkdir -p "${NEW_PROJECT_DIR}/public/build"
    chown -R www-data:www-data "${NEW_PROJECT_DIR}/public/build"

    # Default ACLs: anything created later in storage/ (even by root via a manual `php artisan`) stays writable by www-data.
    if command -v setfacl >/dev/null 2>&1; then
        if setfacl -R  -m "u:www-data:rwX" -m "u:${CURRENT_USER}:rwX" "${NEW_PROJECT_DIR}/storage" "${NEW_PROJECT_DIR}/bootstrap/cache" 2>/dev/null \
           && setfacl -dR -m "u:www-data:rwX" -m "u:${CURRENT_USER}:rwX" "${NEW_PROJECT_DIR}/storage" "${NEW_PROJECT_DIR}/bootstrap/cache" 2>/dev/null; then
            ok "ACLs set on storage/ and bootstrap/cache"
        else
            warn "Could not set ACLs — run artisan via 'sudo -u www-data php artisan ...'"
        fi
    fi

    update_hosts
    ensure_php_version
}

# ============================================================
# SECTION: env
# ============================================================
sec_env() {
    if (( DRY_RUN )); then dry "Create/update .env (DB, Redis, session, sync settings for role ${APP_ROLE})"; return 0; fi
    need_project
    local env_file="${NEW_PROJECT_DIR}/.env" existing_priv existing_pub

    if [[ ! -f "$env_file" ]]; then
        if [[ -f "${env_file}.example" ]]; then cp "${env_file}.example" "$env_file"; _log ".env created from .env.example"
        else warn "No .env or .env.example found — creating minimal .env"; touch "$env_file"; fi
    fi

    # Keep the central signing keys across re-runs; the sync block below is stripped and rewritten.
    existing_priv="$(grep -E '^CENTRAL_PRIVATE_KEY=' "$env_file" | head -1 | cut -d= -f2- || true)"
    existing_pub="$(grep -E '^CENTRAL_PUBLIC_KEY=' "$env_file" | head -1 | cut -d= -f2- || true)"

    set_env "$env_file" APP_URL   "$(app_scheme)://${DOMAIN}"
    set_env "$env_file" APP_ENV   "production"
    set_env "$env_file" APP_DEBUG "false"
    set_env "$env_file" APP_NAME  '"All Academies"'
    # A cookie Domain of ahpc.internal is rejected by browsers when the site is opened by IP address.
    set_env "$env_file" SESSION_DOMAIN "null"
    if ! grep -q '^APP_KEY=' "$env_file"; then set_env "$env_file" APP_KEY ""; fi

    set_env "$env_file" DB_CONNECTION "mysql"
    set_env "$env_file" DB_HOST       "127.0.0.1"
    set_env "$env_file" DB_PORT       "3306"
    set_env "$env_file" DB_DATABASE   "${DB_NAME}"
    set_env "$env_file" DB_USERNAME   "${DB_USER}"
    set_env "$env_file" DB_PASSWORD   "${DB_PASS}"

    set_env "$env_file" REDIS_HOST     "127.0.0.1"
    set_env "$env_file" REDIS_PASSWORD "${REDIS_PASS}"
    set_env "$env_file" REDIS_PORT     "6379"

    set_env "$env_file" CACHE_STORE      "redis"
    set_env "$env_file" CACHE_DRIVER     "redis"
    set_env "$env_file" SESSION_DRIVER   "redis"
    set_env "$env_file" QUEUE_CONNECTION "redis"
    if [[ -n "${TLS_CERT:-}" ]]; then set_env "$env_file" SESSION_SECURE_COOKIE "true"
    else set_env "$env_file" SESSION_SECURE_COOKIE "false"; fi

    local var
    for var in APP_ROLE CENTRAL_URL CENTER_SYNC_TOKEN CENTRAL_PUBLIC_KEY CENTRAL_PRIVATE_KEY APP_VERSION CENTER_ID CENTER_PULL_INTERVAL CENTER_PUSH_INTERVAL; do
        sed -i "/^${var}=/d" "$env_file"
    done

    if [[ "$APP_ROLE" == "central" ]]; then
        local priv="${CENTRAL_PRIVATE_KEY:-}"
        if [[ -z "$priv" && -n "$existing_priv" && "$existing_priv" != CHANGE_ME* ]]; then
            priv="$existing_priv"
            CENTRAL_PUBLIC_KEY="${existing_pub:-$CENTRAL_PUBLIC_KEY}"
            info "Reusing the existing central key pair from .env (not rotating it)"
        fi
        if [[ -z "$priv" ]]; then
            _log "Generating key pair for central server"
            local key_output
            key_output="$(php -r '
$keyPair = sodium_crypto_sign_keypair();
$pub = sodium_bin2base64(sodium_crypto_sign_publickey($keyPair), SODIUM_BASE64_VARIANT_ORIGINAL);
$priv = sodium_bin2base64(sodium_crypto_sign_secretkey($keyPair), SODIUM_BASE64_VARIANT_ORIGINAL);
echo $priv . "\n" . $pub . "\n";
' 2>/dev/null || true)"
            if [[ -n "$key_output" ]]; then
                priv="$(echo "$key_output" | head -1)"
                CENTRAL_PUBLIC_KEY="$(echo "$key_output" | tail -1)"
                _log "Key pair generated"
            else
                warn "Could not generate keys automatically (php-sodium not available)."
                warn "Generate them manually and set CENTRAL_PRIVATE_KEY / CENTRAL_PUBLIC_KEY in .env"
                priv="CHANGE_ME_PRIVATE_KEY_BASE64"
                CENTRAL_PUBLIC_KEY="CHANGE_ME_PUBLIC_KEY_BASE64"
            fi
        fi
        CENTRAL_PRIVATE_KEY="$priv"
        cat >> "$env_file" <<ENVEOF

# ── Multi-Center Sync (Central Role) ─────────────────────────────────────────
APP_ROLE=central
CENTRAL_PRIVATE_KEY=${CENTRAL_PRIVATE_KEY}
CENTRAL_PUBLIC_KEY=${CENTRAL_PUBLIC_KEY}
APP_VERSION=1.0.0
ENVEOF
        ok ".env configured for central role"
    elif [[ "$APP_ROLE" == "center" ]]; then
        cat >> "$env_file" <<ENVEOF

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

    chown www-data:www-data "$env_file"
    chmod 600 "$env_file"
    ok ".env configured with database, redis, session, and center sync settings"
}

# ============================================================
# SECTION: deps
# ============================================================
FRONTEND_WARNING=""
sec_deps() {
    if (( DRY_RUN )); then dry "composer install --no-dev --optimize-autoloader --ignore-platform-reqs; npm ci && npm run build as www-data"; return 0; fi
    need_project
    detect_php
    cd "$NEW_PROJECT_DIR"
    command -v composer >/dev/null 2>&1 || die "Composer is not installed (see the earlier warning). Install it manually and re-run."

    _log "Running composer install (production; --ignore-platform-reqs by design)"
    mkdir -p "${NEW_PROJECT_DIR}/vendor"
    chown -R www-data:www-data "${NEW_PROJECT_DIR}/vendor"
    local composer_log="/tmp/${APP_NAME}-composer.log"
    # No --ignore-platform-reqs: it hid wrong PHP versions / missing extensions and produced a "successful" deploy that 500s.
    if as_www composer install --no-dev --optimize-autoloader --no-interaction --ignore-platform-reqs >"$composer_log" 2>&1; then
        ok "composer install done"
    else
        tail -n 30 "$composer_log" >&2
        die "composer install failed (full log: ${composer_log}). Check the log for missing packages or syntax errors."
    fi

    if [[ ! -f package.json ]]; then warn "package.json not found — skipping npm install/build"; return 0; fi
    local npm_log="/tmp/${APP_NAME}-npm.log" npm_ok=0
    : > "$npm_log"
    mkdir -p "${NEW_PROJECT_DIR}/node_modules" "${NEW_PROJECT_DIR}/public/build"
    chown -R www-data:www-data "${NEW_PROJECT_DIR}/node_modules" "${NEW_PROJECT_DIR}/public/build"

    _log "Running npm install"
    if [[ -f package-lock.json ]] && as_www npm ci --no-audit --no-fund >>"$npm_log" 2>&1; then npm_ok=1
    elif as_www npm install --no-audit --no-fund >>"$npm_log" 2>&1; then npm_ok=1; fi

    FRONTEND_WARNING=""
    if (( npm_ok )); then
        ok "npm dependencies installed"
        if grep -q '"build"' package.json; then
            _log "Running npm run build"
            if as_www npm run build >>"$npm_log" 2>&1; then
                ok "npm run build done"
            else
                tail -n 25 "$npm_log" >&2
                FRONTEND_WARNING="npm run build FAILED (log: ${npm_log}) — pages that use @vite will return HTTP 500."
                warn "$FRONTEND_WARNING"
            fi
        fi
    else
        tail -n 25 "$npm_log" >&2
        FRONTEND_WARNING="npm install FAILED (log: ${npm_log}) — assets were not built; pages that use @vite will return HTTP 500."
        warn "$FRONTEND_WARNING"
    fi
    if [[ -z "$FRONTEND_WARNING" ]] && grep -qE '"(vite|laravel-mix)"' package.json \
       && [[ ! -f public/build/manifest.json && ! -f public/mix-manifest.json ]]; then
        FRONTEND_WARNING="Build reported success but no public/build/manifest.json exists — check vite.config.js (outDir) / ${npm_log}."
        warn "$FRONTEND_WARNING"
    fi
}

# ============================================================
# SECTION: laravel
# ============================================================
sec_laravel() {
    if (( DRY_RUN )); then dry "artisan key:generate (only if empty), migrate --force, storage link, config/route/view cache"; return 0; fi
    need_project
    detect_php
    cd "$NEW_PROJECT_DIR"
    local out

    # Only generate a key when there isn't one: regenerating invalidates sessions, signed URLs and encrypted columns.
    if grep -qE '^APP_KEY=.+' .env; then
        info "APP_KEY already set — keeping it"
    else
        _log "Generating application key"
        if out="$(as_www php artisan key:generate --force 2>&1)"; then ok "APP_KEY generated"
        else
            echo "$out" >&2
            die "artisan could not boot the application. With 'composer install --no-dev' the usual cause is a dev-only package still registered by a service provider (Pail, Collision, Debugbar, Telescope, ...)."
        fi
    fi

    _log "Running database migrations"
    if out="$(as_www php artisan migrate --force --no-interaction 2>&1)"; then
        echo "$out" | tail -10
        ok "Migrations complete"
    else
        echo "$out" >&2
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
    as_www php artisan route:cache  || warn "route:cache failed (closure-based routes?) — continuing without a route cache"
    as_www php artisan view:cache   || warn "view:cache failed (Blade error?) — continuing without a view cache"
    ok "Laravel caches configured"
}

# ============================================================
# SECTION: admin
# ============================================================
sec_admin() {
    if (( DRY_RUN )); then dry "php artisan users:create ${DEFAULT_ADMIN_EMAIL} admin --password=*** (as www-data)"; return 0; fi
    need_project
    cd "$NEW_PROJECT_DIR"
    _log "Provisioning default admin user"
    # Run as www-data: artisan/tinker as root created root-owned files in storage/ that PHP-FPM could not write later.
    local existing
    existing="$(as_www php artisan tinker --execute="echo \App\Models\User::where('email', '${DEFAULT_ADMIN_EMAIL}')->exists() ? 'yes' : 'no';" 2>/dev/null || true)"
    if [[ "$existing" == *yes* ]]; then
        warn "Admin user ${DEFAULT_ADMIN_EMAIL} already exists — skipping creation"
    else
        _log "Creating admin user: ${DEFAULT_ADMIN_EMAIL}"
        if as_www php artisan users:create "${DEFAULT_ADMIN_EMAIL}" admin \
            --first_name="Admin" --last_name="User" --password="${DEFAULT_ADMIN_PASSWORD}" --no-interaction 2>&1; then
            ok "Admin user provisioned"
        else
            warn "Failed to create admin user — create manually after setup"
        fi
    fi
}

# ============================================================
# NGINX SITE (shared by the nginx, tls, ip and php sections)
# ============================================================
nginx_app_body() {
    cat <<NGX
    root ${NEW_PROJECT_DIR}/public;
    index index.php index.html;

    client_max_body_size 100M;
    keepalive_timeout 30s;

    gzip on;
    gzip_comp_level 5;
    gzip_min_length 1024;
    gzip_vary on;
    gzip_types text/plain text/css text/xml application/json application/javascript text/javascript application/xml image/svg+xml;

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
        fastcgi_read_timeout 120s;
        fastcgi_buffer_size 32k;
        fastcgi_buffers 16 16k;
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
NGX
}

write_nginx_conf() {
    local site="/etc/nginx/sites-available/${DOMAIN}" backup="" f use_tls=0
    detect_php
    [[ -n "$PHP_FPM_SOCK" ]] || die "PHP is not installed — run the 'php' section first."
    if [[ -n "${TLS_CERT:-}" ]]; then
        if [[ -f "$TLS_CERT" && -f "${TLS_KEY:-}" ]]; then use_tls=1
        else warn "TLS certificate files are missing (${TLS_CERT}) — writing an HTTP-only site. Run --only tls."; fi
    fi
    if [[ -f "$site" ]]; then backup="$(mktemp)"; cp -p "$site" "$backup"; fi

    {
        echo "# Managed by ${APP_NAME} setup script — regenerated by the 'nginx' / 'tls' / 'ip' sections."
        if (( use_tls )); then
            cat <<NGX
server {
    listen 80 default_server;
    listen [::]:80 default_server;
    server_name ${DOMAIN} ${LAN_IP};

    # Let's Encrypt HTTP-01 renewals
    location ^~ /.well-known/acme-challenge/ {
        root ${NEW_PROJECT_DIR}/public;
    }
NGX
            if [[ -f "${PUBLIC_DIR}/${CA_PUBLIC_NAME}" ]]; then
                cat <<NGX

    # Root CA for client devices — kept on plain HTTP so it can be fetched before it is trusted
    location = /${CA_PUBLIC_NAME} {
        alias ${PUBLIC_DIR}/${CA_PUBLIC_NAME};
        default_type application/x-x509-ca-cert;
        add_header Content-Disposition 'attachment; filename="${CA_PUBLIC_NAME}"';
    }
NGX
            fi
            cat <<NGX

    location / {
        return 301 https://\$host\$request_uri;
    }
}

server {
    listen 443 ssl http2 default_server;
    listen [::]:443 ssl http2 default_server;
    server_name ${DOMAIN} ${LAN_IP};

    ssl_certificate     ${TLS_CERT};
    ssl_certificate_key ${TLS_KEY};
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_session_cache shared:SSL:20m;    # hundreds of browsers connecting at once: resume instead of re-handshaking
    ssl_session_timeout 1d;
    ssl_session_tickets off;

NGX
            nginx_app_body
            echo "}"
        else
            cat <<NGX
server {
    listen 80 default_server;
    listen [::]:80 default_server;
    server_name ${DOMAIN} ${LAN_IP};

NGX
            nginx_app_body
            echo "}"
        fi
    } > "$site"

    ln -sf "$site" "/etc/nginx/sites-enabled/${DOMAIN}"
    rm -f /etc/nginx/sites-enabled/default
    # Remove sites this script wrote for an old domain (they would fight over default_server).
    for f in /etc/nginx/sites-enabled/*; do
        if [[ -L "$f" && "$f" != "/etc/nginx/sites-enabled/${DOMAIN}" ]] \
           && grep -q "Managed by ${APP_NAME} setup" "$(readlink -f "$f")" 2>/dev/null; then
            rm -f "$f"
        fi
    done

    if ! nginx -t 2>&1; then
        if [[ -n "$backup" ]]; then cp -p "$backup" "$site"; else rm -f "$site" "/etc/nginx/sites-enabled/${DOMAIN}"; fi
        die "Nginx rejected the generated config (previous config restored)."
    fi
    if systemctl is-active --quiet nginx; then systemctl reload nginx; else systemctl start nginx; fi
    ok "Nginx site written for ${DOMAIN} ($( (( use_tls )) && echo "HTTPS" || echo "HTTP" ))"
}

# ============================================================
# SECTION: nginx
# ============================================================
sec_nginx() {
    if (( DRY_RUN )); then dry "Install + tune Nginx (worker_connections 4096, nofile 65535) and write the site for ${DOMAIN}"; return 0; fi
    need_project
    detect_php
    _log "Installing Nginx"
    apt-get install -y nginx
    systemctl enable nginx

    local main="/etc/nginx/nginx.conf"
    sed -i -E 's/^([[:space:]]*)worker_connections[[:space:]]+[0-9]+;/\1worker_connections 4096;/' "$main"
    if ! grep -q '^worker_rlimit_nofile' "$main"; then sed -i '1i worker_rlimit_nofile 65535;' "$main"; fi
    sed -i -E 's/^[[:space:]]*#[[:space:]]*server_tokens off;/\tserver_tokens off;/' "$main"
    mkdir -p /etc/systemd/system/nginx.service.d
    printf '[Service]\nLimitNOFILE=65535\n' > "/etc/systemd/system/nginx.service.d/${APP_NAME}-limits.conf"
    systemctl daemon-reload

    write_nginx_conf
    systemctl restart nginx      # a restart (not reload) is what applies the new file-handle limit
    ok "Nginx tuned for ~${EXPECTED_STUDENTS} concurrent students"
}

# ============================================================
# SECTION: workers (Supervisor)
# ============================================================
sec_workers() {
    if (( DRY_RUN )); then dry "Supervisor config: ${QUEUE_WORKER_PROCESSES} queue workers"; return 0; fi
    need_project
    _log "Writing Supervisor config for ${QUEUE_WORKER_PROCESSES} queue workers"
    cat > "/etc/supervisor/conf.d/${APP_NAME}-worker.conf" <<SUPCONF
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
}

# ============================================================
# SECTION: logrotate
# ============================================================
sec_logrotate() {
    if (( DRY_RUN )); then dry "Logrotate configs for Laravel, Nginx, worker and sync logs"; return 0; fi
    detect_php
    _log "Setting up log rotation"
    cat > "/etc/logrotate.d/${APP_NAME}-laravel" <<LOGROTATE
${NEW_PROJECT_DIR}/storage/logs/laravel.log {
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
    cat > "/etc/logrotate.d/${APP_NAME}-worker" <<LOGROTATE
${NEW_PROJECT_DIR}/storage/logs/worker.log {
    daily
    missingok
    rotate 7
    compress
    delaycompress
    notifempty
    create 0640 www-data www-data
}
LOGROTATE
    cat > "/etc/logrotate.d/${APP_NAME}-sync" <<LOGROTATE
/var/log/${APP_NAME}-sync.log {
    daily
    missingok
    rotate 7
    compress
    delaycompress
    notifempty
    create 0640 www-data www-data
}
LOGROTATE
    ok "Log rotation configured"
}

# ============================================================
# SECTION: cron  (Laravel scheduler — runs as www-data, NOT root)
# ============================================================
sec_cron() {
    if (( DRY_RUN )); then dry "Add www-data cron entry: * * * * * php artisan schedule:run"; return 0; fi
    need_project
    _log "Setting up Laravel scheduler"
    # A root cron job creates root-owned log/cache files in storage/ every minute that later break the web app.
    if crontab -u root -l 2>/dev/null | grep -qE 'artisan (schedule:run|sync:)'; then
        warn "Moving artisan cron entries out of root's crontab (they now run as www-data)"
        crontab -u root -l 2>/dev/null | grep -vE 'artisan (schedule:run|sync:)' | crontab -u root - || true
    fi
    local entry="* * * * * cd ${NEW_PROJECT_DIR} && php artisan schedule:run >> /dev/null 2>&1"
    if ! crontab -u www-data -l 2>/dev/null | grep -q "schedule:run"; then
        (crontab -u www-data -l 2>/dev/null || true; echo "$entry") | crontab -u www-data -
        ok "Laravel scheduler cron entry added (user: www-data)"
    else
        info "Laravel scheduler cron already exists — skipping"
    fi
}

# ============================================================
# SECTION: tls  (local CA + signed certificate, or Let's Encrypt)
# ============================================================
LOCAL_DOMAIN_RE='\.(internal|local|lan|home|localdomain|test|localhost)$'

is_public_domain() {
    [[ "$1" == *.* && ! "$1" =~ $LOCAL_DOMAIN_RE && ! "$1" =~ ^[0-9.]+$ ]]
}

# The names the local certificate must cover (one per line, unique)
tls_names() {
    local -a extra=()
    if [[ -n "${TLS_SANS:-}" ]]; then split_csv extra "$TLS_SANS"; fi
    printf '%s\n' "$DOMAIN" "$LAN_IP" localhost 127.0.0.1 "${extra[@]}" | sed '/^$/d' | sort -u
}

# 0 when the current local cert is valid for >30 days, chains to our CA and covers exactly these names
local_cert_ok() {
    local crt="${TLS_DIR}/server.crt" have want
    [[ -f "$crt" && -f "${TLS_DIR}/server.key" && -f "${CA_DIR}/rootCA.pem" ]] || return 1
    openssl x509 -in "$crt" -noout -checkend $(( 30 * 86400 )) >/dev/null 2>&1 || return 1
    openssl verify -CAfile "${CA_DIR}/rootCA.pem" "$crt" >/dev/null 2>&1 || return 1
    have="$(openssl x509 -in "$crt" -noout -ext subjectAltName 2>/dev/null | tail -n +2 | tr ',' '\n' \
            | sed -E 's/^[[:space:]]*(DNS:|IP Address:)//; s/[[:space:]]+$//' | sed '/^$/d' | sort -u)"
    want="$(printf '%s\n' "$@" | sort -u)"
    [[ "$have" == "$want" ]]
}

tls_issue_openssl() {
    local -a names=("$@")
    local tmp n alt="" n_dns=0 n_ip=0
    tmp="$(mktemp -d)"

    if [[ ! -f "${CA_DIR}/rootCA.pem" || ! -f "${CA_DIR}/rootCA-key.pem" ]]; then
        info "Creating the local root CA (valid 10 years) in ${CA_DIR}"
        cat > "${tmp}/ca.cnf" <<CNF
[req]
distinguished_name = dn
prompt = no
x509_extensions = v3_ca
[dn]
O = ${APP_NAME^^} Local CA
CN = ${APP_NAME^^} Local Root CA
[v3_ca]
basicConstraints = critical,CA:TRUE,pathlen:0
keyUsage = critical,keyCertSign,cRLSign
subjectKeyIdentifier = hash
CNF
        openssl ecparam -name prime256v1 -genkey -noout -out "${CA_DIR}/rootCA-key.pem"
        openssl req -x509 -new -key "${CA_DIR}/rootCA-key.pem" -sha256 -days 3650 -config "${tmp}/ca.cnf" -out "${CA_DIR}/rootCA.pem"
        chmod 600 "${CA_DIR}/rootCA-key.pem"
    fi

    for n in "${names[@]}"; do
        if valid_ipv4 "$n"; then n_ip=$(( n_ip + 1 )); alt+="IP.${n_ip} = ${n}"$'\n'
        else n_dns=$(( n_dns + 1 )); alt+="DNS.${n_dns} = ${n}"$'\n'; fi
    done
    cat > "${tmp}/leaf.cnf" <<CNF
[req]
distinguished_name = dn
prompt = no
[dn]
O = ${APP_NAME^^}
CN = ${DOMAIN}
CNF
    cat > "${tmp}/ext.cnf" <<CNF
[v3_leaf]
basicConstraints = CA:FALSE
keyUsage = critical,digitalSignature
extendedKeyUsage = serverAuth
subjectKeyIdentifier = hash
authorityKeyIdentifier = keyid
subjectAltName = @alt
[alt]
${alt}
CNF
    openssl ecparam -name prime256v1 -genkey -noout -out "${TLS_DIR}/server.key"
    openssl req -new -key "${TLS_DIR}/server.key" -config "${tmp}/leaf.cnf" -out "${tmp}/server.csr"
    openssl x509 -req -in "${tmp}/server.csr" -CA "${CA_DIR}/rootCA.pem" -CAkey "${CA_DIR}/rootCA-key.pem" \
        -CAserial "${CA_DIR}/rootCA.srl" -CAcreateserial -days 825 -sha256 \
        -extfile "${tmp}/ext.cnf" -extensions v3_leaf -out "${TLS_DIR}/server.crt" 2>/dev/null
    rm -rf "$tmp"
}

tls_issue_local() {
    local -a names=("$@")
    local tool="${TLS_TOOL:-auto}"
    mkdir -p "$CA_DIR" "$TLS_DIR" "$PUBLIC_DIR"
    chmod 755 "$STATE_DIR" "$PUBLIC_DIR"
    chmod 700 "$CA_DIR" "$TLS_DIR"

    if [[ "$tool" == "auto" || "$tool" == "mkcert" ]]; then
        if ! command -v mkcert >/dev/null 2>&1; then apt-get install -y mkcert libnss3-tools >/dev/null 2>&1 || true; fi
        if command -v mkcert >/dev/null 2>&1; then tool="mkcert"
        elif [[ "$tool" == "mkcert" ]]; then die "mkcert is not available from apt on this system — use --tls-tool openssl."
        else info "mkcert not available — using openssl to run the local CA"; tool="openssl"; fi
    fi

    if [[ "$tool" == "mkcert" ]]; then
        CAROOT="$CA_DIR" mkcert -install || warn "mkcert could not add the CA to every trust store (non-fatal)"
        CAROOT="$CA_DIR" mkcert -cert-file "${TLS_DIR}/server.crt" -key-file "${TLS_DIR}/server.key" "${names[@]}"
    else
        tls_issue_openssl "${names[@]}"
    fi
    chmod 600 "${TLS_DIR}/server.key"
    chmod 644 "${TLS_DIR}/server.crt"
    openssl verify -CAfile "${CA_DIR}/rootCA.pem" "${TLS_DIR}/server.crt" >/dev/null \
        || die "The freshly issued certificate does not verify against the local CA."
    cp -f "${CA_DIR}/rootCA.pem" "${PUBLIC_DIR}/${CA_PUBLIC_NAME}"
    chmod 644 "${PUBLIC_DIR}/${CA_PUBLIC_NAME}"
    ok "Certificate issued by ${tool} for: $(printf '%s ' "${names[@]}")"
}

# Trust our own CA on this server (so it can call itself / other centers over https)
trust_ca_system() {
    cp -f "${CA_DIR}/rootCA.pem" "/usr/local/share/ca-certificates/${APP_NAME}-rootCA.crt"
    update-ca-certificates >/dev/null 2>&1 || warn "update-ca-certificates failed"
}

install_extra_ca() {
    local src="$1" tmp
    tmp="$(mktemp)"
    if [[ "$src" =~ ^https?:// ]]; then
        curl -fsSk --max-time 30 -o "$tmp" "$src" || die "Could not download the Central CA from ${src}"
    else
        [[ -f "$src" ]] || die "CA file not found: ${src}"
        cp "$src" "$tmp"
    fi
    openssl x509 -in "$tmp" -noout >/dev/null 2>&1 || die "${src} is not a PEM certificate."
    install -m 644 "$tmp" "/usr/local/share/ca-certificates/${APP_NAME}-central-ca.crt"
    update-ca-certificates >/dev/null 2>&1 || warn "update-ca-certificates failed"
    ok "Now trusting the Central CA. Compare this fingerprint with the one shown on the Central server:"
    info "  $(openssl x509 -in "$tmp" -noout -fingerprint -sha256)"
    rm -f "$tmp"
}

tls_certbot() {
    if ! apt-get install -y certbot >/dev/null 2>&1; then warn "Could not install certbot"; return 1; fi
    local -a args=(certonly --webroot -w "${NEW_PROJECT_DIR}/public" -d "$DOMAIN"
                   --non-interactive --agree-tos --deploy-hook "systemctl reload nginx")
    if [[ -n "${TLS_EMAIL:-}" ]]; then args+=(-m "$TLS_EMAIL"); else args+=(--register-unsafely-without-email); fi
    info "Requesting a Let's Encrypt certificate for ${DOMAIN}"
    if certbot "${args[@]}" 2>&1 | tee /var/log/certbot-setup.log \
       && [[ -f "/etc/letsencrypt/live/${DOMAIN}/fullchain.pem" ]]; then
        TLS_CERT="/etc/letsencrypt/live/${DOMAIN}/fullchain.pem"
        TLS_KEY="/etc/letsencrypt/live/${DOMAIN}/privkey.pem"
        return 0
    fi
    return 1
}

tls_apply_env() {
    local env_file="${NEW_PROJECT_DIR}/.env"
    if [[ ! -f "$env_file" ]]; then return 0; fi
    set_env "$env_file" APP_URL "$(app_scheme)://${DOMAIN}"
    if [[ -n "${TLS_CERT:-}" ]]; then set_env "$env_file" SESSION_SECURE_COOKIE "true"
    else set_env "$env_file" SESSION_SECURE_COOKIE "false"; fi
    chown www-data:www-data "$env_file"
    chmod 600 "$env_file"
    ( cd "$NEW_PROJECT_DIR" && as_www php artisan config:cache >/dev/null 2>&1 ) || warn "config:cache failed after updating APP_URL"
}

sec_tls() {
    if (( DRY_RUN )); then
        dry "TLS (${TLS_MODE:-auto}): local CA + signed certificate for ${DOMAIN}/${LAN_IP} (mkcert or openssl), or Let's Encrypt for a public domain"
        return 0
    fi
    need_project
    detect_php
    local mode="${TLS_MODE:-auto}"
    case "$mode" in auto|local|certbot|none) ;; *) die "--tls must be auto, local, certbot or none (got '${mode}')." ;; esac

    if [[ "$mode" == "none" ]]; then
        TLS_CERT=""; TLS_KEY=""
        rm -f "/etc/cron.weekly/${APP_NAME}-cert-renew"
        write_nginx_conf
        tls_apply_env
        save_state
        ok "HTTPS disabled — serving over HTTP"
        return 0
    fi
    if [[ "$mode" == "auto" ]]; then
        if is_public_domain "$DOMAIN"; then mode="certbot"; else mode="local"; fi
    fi

    if [[ "$mode" == "certbot" ]]; then
        TLS_CERT=""; TLS_KEY=""
        write_nginx_conf          # HTTP-only site that serves /.well-known/acme-challenge
        if tls_certbot; then
            ok "Let's Encrypt certificate installed (auto-renewed by certbot's timer)"
            rm -f "/etc/cron.weekly/${APP_NAME}-cert-renew"
        else
            warn "Let's Encrypt failed for ${DOMAIN} (must resolve publicly to this server, port 80 reachable) — falling back to the local CA"
            mode="local"
        fi
    fi

    if [[ "$mode" == "local" ]]; then
        local -a names=()
        mapfile -t names < <(tls_names)
        if local_cert_ok "${names[@]}" && (( ! FORCE )); then
            info "Local certificate is still valid and covers: $(printf '%s ' "${names[@]}")"
        else
            _log "Issuing the local certificate"
            tls_issue_local "${names[@]}"
        fi
        TLS_CERT="${TLS_DIR}/server.crt"
        TLS_KEY="${TLS_DIR}/server.key"
        trust_ca_system

        cat > "/etc/cron.weekly/${APP_NAME}-cert-renew" <<CRON
#!/bin/sh
# Managed by the ${APP_NAME} setup script: re-issues the local certificate when it nears expiry or the IP changed.
[ -x ${SELF_INSTALL_PATH} ] && ${SELF_INSTALL_PATH} --only tls >> ${LOG_FILE} 2>&1
exit 0
CRON
        chmod 755 "/etc/cron.weekly/${APP_NAME}-cert-renew"

        info "Root CA for client devices: http://${LAN_IP}/${CA_PUBLIC_NAME}"
        info "  SHA-256: $(openssl x509 -in "${CA_DIR}/rootCA.pem" -noout -fingerprint -sha256 | cut -d= -f2)"
    fi

    if [[ -n "${CENTRAL_CA:-}" ]]; then install_extra_ca "$CENTRAL_CA"; fi
    write_nginx_conf
    tls_apply_env
    if ufw status 2>/dev/null | grep -q "Status: active"; then ufw allow 443/tcp >/dev/null 2>&1 || true; fi
    save_state
}

# ============================================================
# SECTION: firewall
# ============================================================
sec_firewall() {
    if (( DRY_RUN )); then dry "ufw allow OpenSSH, 80, 443; ufw --force enable"; return 0; fi
    _log "Configuring UFW firewall"
    ufw allow OpenSSH
    if [[ -n "$LAN_SUBNET" ]]; then
        ufw allow from "$LAN_SUBNET" to any port 80 proto tcp
        ufw allow from "$LAN_SUBNET" to any port 443 proto tcp
    else
        ufw allow 80/tcp
        ufw allow 443/tcp
    fi
    ufw --force enable
}

# ============================================================
# SECTION: sync  (center role)
# ============================================================
sec_sync() {
    if [[ "$APP_ROLE" != "center" ]]; then info "Sync section only applies to the center role — skipping"; return 0; fi
    if (( DRY_RUN )); then dry "Test sync API connectivity to ${CENTRAL_URL}/api/sync/v1/ping; set up sync cron (pull/push every ${PULL_INTERVAL}/${PUSH_INTERVAL} min); verify sync commands"; return 0; fi
    need_project
    # NOTE: CENTRAL_URL is ONLY for center<->central sync. Exam devices connect to this server's LAN IP directly.
    banner "Testing connection to Central sync API"
    local ping_response
    ping_response="$(curl -sk --connect-timeout 5 "${CENTRAL_URL}/api/sync/v1/ping" 2>/dev/null || true)"
    if echo "$ping_response" | grep -q '"status":"ok"'; then
        ok "Central sync API is reachable at ${CENTRAL_URL}"
    else
        local code
        code="$(curl -sk -o /dev/null -w '%{http_code}' --connect-timeout 5 "${CENTRAL_URL}/api/sync/v1/ping" 2>/dev/null || true)"
        warn "Cannot reach Central sync API at ${CENTRAL_URL}/api/sync/v1/ping (HTTP ${code:-000})"
        warn "Check: firewall, nginx, and that the Central server is running"
        warn "Note: exam devices connect to this server's LAN IP directly, not via CENTRAL_URL"
    fi

    banner "Auto-sync setup"
    if [[ -z "$SETUP_CRON_FLAG" ]]; then
        if [[ -t 0 ]]; then
            read -rp "  Set up auto-sync via cron? (pull/push every ${PULL_INTERVAL} minutes) [y/N]: " SETUP_CRON_FLAG
            SETUP_CRON_FLAG="${SETUP_CRON_FLAG:-N}"
        else
            SETUP_CRON_FLAG="N"
        fi
    fi
    if [[ "$SETUP_CRON_FLAG" =~ ^[Yy]$ ]]; then
        touch "/var/log/${APP_NAME}-sync.log"
        chown www-data:www-data "/var/log/${APP_NAME}-sync.log"
        local pull="*/${PULL_INTERVAL} * * * * cd ${NEW_PROJECT_DIR} && php artisan sync:pull >> /var/log/${APP_NAME}-sync.log 2>&1"
        local push="*/${PUSH_INTERVAL} * * * * cd ${NEW_PROJECT_DIR} && php artisan sync:push >> /var/log/${APP_NAME}-sync.log 2>&1"
        if ! crontab -u www-data -l 2>/dev/null | grep -q "sync:pull"; then
            (crontab -u www-data -l 2>/dev/null || true; echo "$pull"; echo "$push") | crontab -u www-data -
            ok "Cron jobs added (www-data): pull every ${PULL_INTERVAL} min, push every ${PUSH_INTERVAL} min"
        else
            info "Cron jobs already exist — skipping"
        fi
    else
        info "Auto-sync skipped. Manual commands: php artisan sync:pull --force / sync:push --force"
    fi

    banner "Verifying sync commands"
    cd "$NEW_PROJECT_DIR"
    if as_www php artisan list sync &>/dev/null; then
        ok "Sync commands registered"
        as_www php artisan list sync 2>&1 | grep -E 'sync:|center:' | sed 's/^/  /' || true
    else
        warn "Sync commands not found. Check: app/Console/Kernel.php"
    fi
}

# ============================================================
# SECTION: finalize
# ============================================================
sec_finalize() {
    if (( DRY_RUN )); then dry "Fix storage ownership, restart php-fpm + workers, curl the site and dump logs on 5xx"; return 0; fi
    need_project
    detect_php
    _log "Final permission pass and health check"
    chown -R www-data:www-data "${NEW_PROJECT_DIR}/storage" "${NEW_PROJECT_DIR}/bootstrap/cache"
    chmod -R ug+rwX "${NEW_PROJECT_DIR}/storage" "${NEW_PROJECT_DIR}/bootstrap/cache"
    sudo -u www-data php "${NEW_PROJECT_DIR}/artisan" optimize:clear

    if [[ -n "$PHP_FPM_SERVICE" ]]; then systemctl restart "$PHP_FPM_SERVICE" || true; fi    # also clears opcache
    as_www php "${NEW_PROJECT_DIR}/artisan" queue:restart >/dev/null 2>&1 || true
    supervisorctl restart "${APP_NAME}-worker:*" >/dev/null 2>&1 || true
    sleep 2

    local url="http://127.0.0.1/" curl_opts=(-s -o /dev/null -w '%{http_code}' --max-time 30 -H "Host: ${DOMAIN}") status
    if [[ -n "${TLS_CERT:-}" ]]; then url="https://127.0.0.1/"; curl_opts+=(-k); fi
    status="$(curl "${curl_opts[@]}" "$url" || true)"
    if [[ "$status" =~ ^5 || "$status" == "000" ]]; then
        warn "Health check FAILED: HTTP ${status} from ${url} (Host: ${DOMAIN})"
        local latest
        latest="$(ls -t "${NEW_PROJECT_DIR}"/storage/logs/laravel*.log 2>/dev/null | head -1 || true)"
        if [[ -n "$latest" ]]; then
            warn "Last lines of ${latest}:"
            tail -n 30 "$latest" | cut -c1-300 || true
        else
            warn "No Laravel log was written — likely a permissions problem on storage/ or PHP-FPM crashed before Laravel booted."
        fi
        warn "Nginx error log (last lines):"
        tail -n 15 "/var/log/nginx/${DOMAIN}-error.log" 2>/dev/null || true
    else
        ok "Health check passed (HTTP ${status})"
    fi
    if [[ -n "${FRONTEND_WARNING:-}" ]]; then warn "$FRONTEND_WARNING"; fi
}

# ============================================================
# SECTION: summary
# ============================================================
sec_summary() {
    if (( DRY_RUN )); then dry "Write ${CREDS_FILE} and print the summary"; return 0; fi
    local proto ca_url="" ca_fp=""
    proto="$(app_scheme)"
    if [[ -n "${TLS_CERT:-}" && "$TLS_CERT" == "${TLS_DIR}"/* && -f "${CA_DIR}/rootCA.pem" ]]; then
        ca_url="http://${LAN_IP}/${CA_PUBLIC_NAME}"
        ca_fp="$(openssl x509 -in "${CA_DIR}/rootCA.pem" -noout -fingerprint -sha256 | cut -d= -f2)"
    fi

    {
        echo "AHPC ${APP_ROLE^} Server — Provisioning Summary"
        echo "Generated: $(date)"
        echo
        echo "Project path:     ${NEW_PROJECT_DIR}  (branch: ${REPO_BRANCH})"
        echo "Server IP:        ${LAN_IP}$([[ "${FIXED_IP:-}" == "1" ]] && echo "  (fixed)" || echo "  (DHCP)")"
        echo "Domain:           ${DOMAIN}"
        echo "URL:              ${proto}://${DOMAIN}   (or ${proto}://${LAN_IP})"
        echo "HTTPS:            $([[ -n "${TLS_CERT:-}" ]] && echo "yes (${TLS_CERT})" || echo "no (HTTP only)")"
        echo "App Role:         ${APP_ROLE}"
        echo "Sized for:        ${EXPECTED_STUDENTS} concurrent students (pm.max_children=${PHP_MAX_CHILDREN}, InnoDB ${INNODB_POOL_MB}M, Redis ${REDIS_MAXMEM_MB}M, ${QUEUE_WORKER_PROCESSES} queue workers)"
        if [[ "$APP_ROLE" == "center" ]]; then
            echo
            echo "Central server:   ${CENTRAL_URL}"
            echo "Sync token:       ${CENTER_SYNC_TOKEN:0:10}..."
            echo "Central public key: ${CENTRAL_PUBLIC_KEY:0:20}..."
            echo "App version:      ${APP_VERSION}"
            echo "Pull / push:      every ${PULL_INTERVAL} / ${PUSH_INTERVAL} minutes"
        fi
        echo
        echo "Database name:    ${DB_NAME}"
        echo "Database user:    ${DB_USER}"
        echo "Database pass:    ${DB_PASS}"
        echo
        echo "Redis password:   ${REDIS_PASS}"
        echo
        echo "Default Admin:"
        echo "  Email:          ${DEFAULT_ADMIN_EMAIL}"
        echo "  Password:       ${DEFAULT_ADMIN_PASSWORD}"
        if [[ "$APP_ROLE" == "central" ]]; then
            echo
            echo "Sync keys:"
            echo "  Private key:    ${CENTRAL_PRIVATE_KEY:-see .env}"
            echo "  Public key:     ${CENTRAL_PUBLIC_KEY}"
        fi
        if [[ -n "$ca_url" ]]; then
            echo
            echo "Local root CA (install on every student/teacher device that opens the site):"
            echo "  Download:       ${ca_url}"
            echo "  SHA-256:        ${ca_fp}"
        fi
        echo
        echo ".env is already configured with the values above."
    } > "$CREDS_FILE"
    chmod 600 "$CREDS_FILE"

    _log "DONE — ${APP_ROLE} server provisioning complete"
    echo
    echo "────────────────────────────────────────────────────────"
    cat "$CREDS_FILE"
    echo "────────────────────────────────────────────────────────"
    echo "  Credentials file: ${CREDS_FILE} (root only)   Log: ${LOG_FILE}"
    echo
    if [[ "$APP_ROLE" == "central" ]]; then
        echo "  To set up a Center server:"
        echo "    sudo bash ahpc-multiserver-setup.sh --app-role center --central-url ${proto}://${LAN_IP} \\"
        echo "      --sync-token <token-from-admin-ui> --public-key '${CENTRAL_PUBLIC_KEY}'$([[ -n "$ca_url" ]] && echo " \\" || true)"
        if [[ -n "$ca_url" ]]; then echo "      --central-ca ${ca_url}"; fi
        echo
    fi
    echo "  Later, run only what you need:"
    echo "    sudo ${APP_NAME}-setup --list-sections"
    echo "    sudo ${APP_NAME}-setup --only deploy          # new version of the code"
    echo "    sudo ${APP_NAME}-setup --only tls             # re-issue the certificate"
    echo "  Moving the server to another network:"
    echo "    sudo ${APP_NAME}-setup --fix-ip               # pin the address it gets there (DHCP)"
    echo "    sudo ${APP_NAME}-setup --fix-ip 10.20.0.50/24 --gateway 10.20.0.1   # or set it explicitly"
    echo
    echo "  Client devices: open ${proto}://${LAN_IP}, or map ${DOMAIN} -> ${LAN_IP} in their hosts file / router DNS."
}

# ============================================================
# MAIN
# ============================================================
declare -A SEL=()

build_selection() {
    local s a seen
    local -a expanded=()
    if (( ${#ONLY_LIST[@]} > 0 )); then
        for s in "${ONLY_LIST[@]}"; do
            while IFS= read -r a; do expanded+=("$a"); done < <(expand_alias "$s")
        done
        for a in "${expanded[@]}"; do
            is_section "$a" || die "Unknown section '${a}'. Run with --list-sections."
            SEL["$a"]=1
        done
    else
        for s in "${ALL_SECTIONS[@]}"; do SEL["$s"]=1; done
    fi
    if [[ -n "$FROM_SECTION" ]]; then
        is_section "$FROM_SECTION" || die "Unknown section '${FROM_SECTION}' for --from. Run with --list-sections."
        seen=0
        for s in "${ALL_SECTIONS[@]}"; do
            if [[ "$s" == "$FROM_SECTION" ]]; then seen=1; fi
            if (( ! seen )); then unset "SEL[$s]"; fi
        done
    fi
    expanded=()
    for s in "${SKIP_LIST[@]}"; do
        while IFS= read -r a; do expanded+=("$a"); done < <(expand_alias "$s")
    done
    for a in "${expanded[@]}"; do
        is_section "$a" || die "Unknown section '${a}' in --skip. Run with --list-sections."
        unset "SEL[$a]"
    done
}

sel() { [[ -n "${SEL[$1]:-}" ]]; }

detect_lan_ip() {
    local found=""
    if [[ -n "$LAN_IP_OVERRIDE" ]]; then LAN_IP="$LAN_IP_OVERRIDE"; return 0; fi
    if (( FIX_IP )) && [[ -n "$FIX_IP_ADDR" ]]; then LAN_IP="${FIX_IP_ADDR%/*}"; return 0; fi
    found="$(ip -4 route get 1.1.1.1 2>/dev/null | awk '{for(i=1;i<=NF;i++) if($i=="src") print $(i+1)}' | head -1 || true)"
    if [[ -z "$found" ]]; then found="$(hostname -I 2>/dev/null | awk '{print $1}' || true)"; fi
    if [[ -n "$found" ]]; then LAN_IP="$found"; fi
    [[ -n "$LAN_IP" ]] || die "Could not detect this server's LAN IP address. Use --ip (or --fix-ip IP) to specify one."
}

validate_role_args() {
    if [[ "$APP_ROLE" != "center" ]]; then return 0; fi
    local what var flag prompt rest
    for what in "CENTRAL_URL|--central-url|Central server URL (required for center role)" \
                "CENTER_SYNC_TOKEN|--sync-token|Center sync token (from Central admin UI)" \
                "CENTRAL_PUBLIC_KEY|--public-key|Central public key (base64)"; do
        var="${what%%|*}"; rest="${what#*|}"; flag="${rest%%|*}"; prompt="${rest#*|}"
        if [[ -z "${!var}" ]]; then
            if [[ -t 0 ]]; then read -rp "  ${prompt}: " "$var"
            else die "${flag} is required when role=center."; fi
        fi
    done
    CENTRAL_URL="${CENTRAL_URL%/}"
    : "${APP_VERSION:=$DEFAULT_APP_VERSION}"
    : "${PULL_INTERVAL:=$DEFAULT_PULL_INTERVAL}"
    : "${PUSH_INTERVAL:=$DEFAULT_PUSH_INTERVAL}"
}

main() {
    parse_args "$@"
    if (( SHOW_HELP )); then usage; exit 0; fi
    if (( LIST_SECTIONS )); then list_sections; exit 0; fi
    [[ $EUID -eq 0 ]] || die "This script must be run as root (use sudo)."

    if (( ! DRY_RUN )); then
        mkdir -p "$(dirname "$LOG_FILE")" "$WWW_HOME"
        chown -R www-data:www-data "$WWW_HOME"
        touch "$LOG_FILE"
        chmod 600 "$LOG_FILE"      # the log includes the credentials summary
        exec 9>"$LOCK_FILE"
        flock -n 9 || die "Another ${APP_NAME}-setup run is in progress (lock: ${LOCK_FILE})."
        exec > >(tee -a "$LOG_FILE") 2>&1
    else
        _log "DRY-RUN MODE — no changes will be made"
    fi

    # ── Settings: CLI flags > saved state > defaults ──────────────────────────
    load_state
    load_credentials_fallback
    : "${DOMAIN:=$DEFAULT_DOMAIN}"
    : "${REPO_BRANCH:=ahpc}"
    : "${EXPECTED_STUDENTS:=400}"
    : "${QUEUE_WORKER_PROCESSES:=4}"
    : "${TLS_MODE:=auto}"
    : "${TLS_TOOL:=auto}"
    : "${DEFAULT_ADMIN_EMAIL:=admin@ahpc.local}"
    : "${DEFAULT_ADMIN_PASSWORD:=pass1234}"
    if [[ -z "$CURRENT_USER" ]]; then CURRENT_USER="${SUDO_USER:-$(logname 2>/dev/null || whoami)}"; fi
    case "$TLS_TOOL" in auto|mkcert|openssl) ;; *) die "--tls-tool must be auto, mkcert or openssl." ;; esac
    [[ "$EXPECTED_STUDENTS" =~ ^[0-9]+$ ]] || die "--students must be a number."

    # ── Which sections run ────────────────────────────────────────────────────
    local explicit=0 s has_ip=0
    if (( ${#ONLY_LIST[@]} > 0 )); then
        explicit=1
        for s in "${ONLY_LIST[@]}"; do
            if [[ "$s" == "ip" ]]; then has_ip=1; fi
        done
        # Asking for the 'ip' section means "pin the current IP"; asking for --fix-ip/--dhcp means "include the ip section".
        if (( has_ip && ! FIX_IP && ! DHCP_REQ )); then FIX_IP=1; fi
        if (( ! has_ip && (FIX_IP || DHCP_REQ) )); then ONLY_LIST+=(ip); fi
    fi
    if (( (FIX_IP || DHCP_REQ) && ! explicit && ! FULL_RUN )) && [[ -z "$FROM_SECTION" && "${PROVISIONED:-}" == "1" ]]; then
        ONLY_LIST=(ip)
        info "Server already provisioned: running only the 'ip' section (add --full to run everything)."
    fi
    build_selection

    if sel env || sel sync || sel summary; then
        [[ -n "$APP_ROLE" ]] || die "--app-role central|center is required (no saved settings found)."
        [[ "$APP_ROLE" == "central" || "$APP_ROLE" == "center" ]] || die "--app-role must be 'central' or 'center', got '${APP_ROLE}'."
    fi
    if [[ -n "$APP_ROLE" ]]; then validate_role_args; fi

    detect_lan_ip
    if [[ -z "$DB_PASS" ]]; then DB_PASS="$(openssl rand -base64 12 | tr -d '/+=' | cut -c1-8)"; info "Generated a new database password"; fi
    if [[ -z "$REDIS_PASS" ]]; then REDIS_PASS="$(openssl rand -base64 12 | tr -d '/+=' | cut -c1-8)"; info "Generated a new Redis password"; fi
    compute_sizing
    detect_php

    local plan=""
    for s in "${ALL_SECTIONS[@]}"; do if sel "$s"; then plan+="${s} "; fi; done
    info "Role: ${APP_ROLE:-n/a} | Domain: ${DOMAIN} | LAN IP: ${LAN_IP} | Sections: ${plan}"
    info "Sizing for ${EXPECTED_STUDENTS} students on ${SIZING_RAM_MB} MB RAM: pm.max_children=${PHP_MAX_CHILDREN}, InnoDB pool=${INNODB_POOL_MB}M, Redis=${REDIS_MAXMEM_MB}M, DB max_connections=${DB_MAX_CONN}"

    # ── Run ───────────────────────────────────────────────────────────────────
    for s in "${ALL_SECTIONS[@]}"; do
        if sel "$s"; then
            banner "Section: ${s} — $(section_desc "$s")"
            "sec_${s}"
            detect_php
            save_state
        fi
    done

    if sel finalize && ! (( DRY_RUN )); then PROVISIONED=1; fi
    save_state
    install_self

    if (( DRY_RUN )); then
        echo
        echo -e "${BOLD}${YELLOW}DRY-RUN COMPLETE — no changes were made. Run without --dry-run to apply.${NC}"
    fi
}

# Run main only when executed, so the functions can be sourced for testing.
if [[ "${BASH_SOURCE[0]}" == "$0" ]]; then main "$@"; fi
