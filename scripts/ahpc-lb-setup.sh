#!/usr/bin/env bash
# ==============================================================================
#  AHPC Load Balancer Setup Script
#
#  Installs and configures HAProxy in front of the center's Nginx to balance
#  traffic between the local center server and the central server.
#
#  Two modes:
#    --mode center-first   (default)  Students stay on the center; overflow to
#                                     central when the center is busy.
#    --mode central-first            Students go to central first; fallback to
#                                     the center when central is busy.
#
#  Run as root:   sudo bash scripts/ahpc-lb-setup.sh [options]
#
#  First run   -> full provisioning. At the end the script installs itself as
#                 /usr/local/sbin/ahpc-lb-setup and saves its settings to
#                 /etc/ahpc/lb-setup.conf, so afterwards you can run ONLY
#                 what you need:
#
#      sudo ahpc-lb-setup --list-sections
#      sudo ahpc-lb-setup --only haproxy-config          # rewrite HAProxy config
#      sudo ahpc-lb-setup --only tls                      # (re)merge TLS cert
#      sudo ahpc-lb-setup --only nginx,haproxy-config     # reconfigure both
#      sudo ahpc-lb-setup --from finalize                 # resume from a section
#
#  Safe to re-run: every section checks existing state before acting.
#  Run with --help for all options.
# ==============================================================================

set -Eeuo pipefail
IFS=$'\n\t'
trap 'echo -e "\n\033[0;31m[FATAL]\033[0m Aborted at line ${LINENO}: ${BASH_COMMAND}" >&2' ERR
export DEBIAN_FRONTEND=noninteractive

# ============================================================
# CONSTANTS
# ============================================================
APP_NAME="ahpc"

STATE_DIR="/etc/${APP_NAME}"
STATE_FILE="${STATE_DIR}/lb-setup.conf"
TLS_DIR="${STATE_DIR}/tls"
LOG_FILE="/var/log/${APP_NAME}-lb-setup.log"
LOCK_FILE="/var/lock/${APP_NAME}-lb-setup.lock"
CREDS_FILE="/root/${APP_NAME}-lb-summary.txt"

HAPROXY_CONF="/etc/haproxy/haproxy.cfg"
HAPROXY_TLS_PEM="${TLS_DIR}/haproxy.pem"
HAPROXY_STATS_SOCKET="/run/haproxy/admin.sock"

NGINX_CONF_DIR="/etc/nginx/sites-available"
NGINX_ENABLED_DIR="/etc/nginx/sites-enabled"

# Settings remembered between runs (/etc/ahpc/lb-setup.conf, root-only).
STATE_KEYS=(MODE CENTRAL_URL CENTRAL_IP CENTRAL_CA_FILE LOCAL_THRESHOLD \
            HAPROXY_MAXCONN HAPROXY_LOCAL_MAXCONN COOKIE_MAX_AGE \
            DOMAIN LAN_IP PROVISIONED)

# Section order is fixed: --only/--skip/--from always run in this order.
ALL_SECTIONS=(haproxy-install tls-merge haproxy-config nginx-reconfigure \
              health-endpoint logrotate systemd finalize summary)

section_desc() {
    case "$1" in
        haproxy-install)     echo "Install HAProxy and create system user" ;;
        tls-merge)           echo "Merge server.crt + server.key into haproxy.pem for TLS termination" ;;
        haproxy-config)      echo "Write HAProxy frontend/backend config with cookie-based routing" ;;
        nginx-reconfigure)   echo "Move Nginx to 127.0.0.1:8081 so HAProxy listens on 80/443" ;;
        health-endpoint)     echo "Verify /up health check endpoint exists on the app" ;;
        logrotate)           echo "Log rotation for HAProxy logs" ;;
        systemd)             echo "Systemd socket activation + override for file descriptors" ;;
        finalize)            echo "Restart services, validate config, health check" ;;
        summary)             echo "Print the load balancer summary and credentials" ;;
    esac
}

expand_alias() {
    case "$1" in
        all)    printf '%s\n' "${ALL_SECTIONS[@]}" ;;
        config) printf '%s\n' haproxy-config nginx-reconfigure ;;
        tls)    printf '%s\n' tls-merge ;;
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
    for s in "${ALL_SECTIONS[@]}"; do printf '  %-18s %s\n' "$s" "$(section_desc "$s")"; done
    echo
    echo "Shortcuts for --only:"
    echo "  config       haproxy-config nginx-reconfigure   (rewrite both configs)"
    echo "  tls          tls-merge"
    echo "  all          everything"
}

usage() {
    cat <<'USAGE'
Usage: sudo bash scripts/ahpc-lb-setup.sh [options]
       sudo ahpc-lb-setup [--only a,b] [--skip a,b] [--from section] [options]

Choosing what runs
  --only a,b,c         run only these sections (see --list-sections; shortcuts: config, tls, all)
  --skip a,b           run everything except these
  --from section       run from this section to the end (resume after a failure)
  --list-sections      show all sections and exit
  --dry-run            show what would be done without changing anything

Load Balancer Mode
  --mode MODE          center-first (default) | central-first
                       center-first = students stay on center, overflow to central
                       central-first = students go to central, fallback to center

Central Server
  --central-url URL    Central server URL (e.g. https://central.ahpc.internal)
  --central-ip IP      Central server IP address (for HAProxy backend)
  --central-ca FILE    Path or URL of the Central server's CA certificate
                       (required for SSL verification on the central backend)

Thresholds
  --local-threshold N  Connection threshold before overflow (default: 150)
                       When local backend connections >= N, new students go to central.
  --maxconn N          Global HAProxy maxconn (default: 4000)
  --local-maxconn N    Per-server maxconn for the local backend (default: 180)

Cookie
  --cookie-age SECS    Sticky cookie max age in seconds (default: 14400 = 4 hours)

Server
  --domain             Domain name (default: ahpc.internal)
  --ip                 LAN IP address (for /etc/hosts + certificate)

HTTPS
  --tls-cert FILE      Path to the server certificate (PEM)
  --tls-key FILE       Path to the server private key (PEM)
                       If not provided, the script looks for existing certs in /etc/ahpc/tls/
USAGE
}

# ============================================================
# ARGUMENTS
# ============================================================
MODE=""; CENTRAL_URL=""; CENTRAL_IP=""; CENTRAL_CA_FILE=""
LOCAL_THRESHOLD=""; HAPROXY_MAXCONN=""; HAPROXY_LOCAL_MAXCONN=""
COOKIE_MAX_AGE=""
DOMAIN=""; LAN_IP=""
TLS_CERT=""; TLS_KEY=""
DRY_RUN=0; SHOW_HELP=0; LIST_SECTIONS=0
FROM_SECTION=""
ONLY_LIST=(); SKIP_LIST=()
PROVISIONED=""

need_val() { [[ $# -ge 2 && -n "$2" ]] || { echo "Option $1 requires a value." >&2; exit 1; }; }

split_csv() {
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
            --mode)             need_val "$@"; MODE="$2";                    shift 2 ;;
            --central-url)      need_val "$@"; CENTRAL_URL="$2";            shift 2 ;;
            --central-ip)       need_val "$@"; CENTRAL_IP="$2";             shift 2 ;;
            --central-ca)       need_val "$@"; CENTRAL_CA_FILE="$2";        shift 2 ;;
            --local-threshold)  need_val "$@"; LOCAL_THRESHOLD="$2";        shift 2 ;;
            --maxconn)          need_val "$@"; HAPROXY_MAXCONN="$2";       shift 2 ;;
            --local-maxconn)    need_val "$@"; HAPROXY_LOCAL_MAXCONN="$2"; shift 2 ;;
            --cookie-age)       need_val "$@"; COOKIE_MAX_AGE="$2";        shift 2 ;;
            --domain)           need_val "$@"; DOMAIN="$2";                 shift 2 ;;
            --ip)               need_val "$@"; LAN_IP="$2";                 shift 2 ;;
            --tls-cert)         need_val "$@"; TLS_CERT="$2";               shift 2 ;;
            --tls-key)          need_val "$@"; TLS_KEY="$2";                shift 2 ;;
            --only|--section)   need_val "$@"; split_csv ONLY_LIST "$2";    shift 2 ;;
            --skip)             need_val "$@"; split_csv SKIP_LIST "$2";    shift 2 ;;
            --from)             need_val "$@"; FROM_SECTION="$2";           shift 2 ;;
            --list-sections)    LIST_SECTIONS=1;                            shift ;;
            --dry-run)          DRY_RUN=1;                                  shift ;;
            --help|-h)          SHOW_HELP=1;                                shift ;;
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
# STATE (remembered between runs)
# ============================================================
save_state() {
    if (( DRY_RUN )); then return 0; fi
    local k tmp
    mkdir -p "$STATE_DIR"
    chmod 755 "$STATE_DIR"
    tmp="$(mktemp "${STATE_DIR}/.lb-setup.XXXXXX")"
    {
        echo "# ${APP_NAME} LB setup settings — written $(date -Is). Used as defaults on later runs; CLI flags override."
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

install_self() {
    if (( DRY_RUN )); then return 0; fi
    local src
    src="$(readlink -f "$0" 2>/dev/null || echo "$0")"
    [[ -f "$src" ]] || return 0
    local SELF_INSTALL_PATH="/usr/local/sbin/${APP_NAME}-lb-setup"
    if [[ "$src" != "$SELF_INSTALL_PATH" ]] && ! cmp -s "$src" "$SELF_INSTALL_PATH" 2>/dev/null; then
        install -m 750 "$src" "$SELF_INSTALL_PATH"
        ok "Installed as ${SELF_INSTALL_PATH} — later, run only what you need:  sudo ${APP_NAME}-lb-setup --only <sections>"
    fi
}

# ============================================================
# SECTION SELECTION
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

# ============================================================
# GENERIC HELPERS
# ============================================================
detect_lan_ip() {
    if [[ -n "$LAN_IP" ]]; then return 0; fi
    local found=""
    found="$(ip -4 route get 1.1.1.1 2>/dev/null | awk '{for(i=1;i<=NF;i++) if($i=="src") print $(i+1)}' | head -1 || true)"
    if [[ -z "$found" ]]; then found="$(hostname -I 2>/dev/null | awk '{print $1}' || true)"; fi
    if [[ -n "$found" ]]; then LAN_IP="$found"; fi
    [[ -n "$LAN_IP" ]] || die "Could not detect this server's LAN IP address. Use --ip to specify one."
}

detect_php() {
    if ! command -v php >/dev/null 2>&1; then return 0; fi
    PHP_VER="$(php -r 'echo PHP_MAJOR_VERSION.".".PHP_MINOR_VERSION;')"
    PHP_FPM_SERVICE="php${PHP_VER}-fpm"
    PHP_FPM_SOCK="/run/php/php${PHP_VER}-fpm.sock"
}
PHP_VER=""; PHP_FPM_SERVICE=""; PHP_FPM_SOCK=""

# ============================================================
# SECTION: haproxy-install
# ============================================================
sec_haproxy_install() {
    if (( DRY_RUN )); then dry "Install HAProxy, create admin socket directory"; return 0; fi
    _log "Installing HAProxy"
    apt-get install -y haproxy
    systemctl enable haproxy

    # Create socket directory for stats
    mkdir -p "$(dirname "$HAPROXY_STATS_SOCKET")"
    chown haproxy:haproxy "$(dirname "$HAPROXY_STATS_SOCKET")"

    # Create TLS directory
    mkdir -p "$TLS_DIR"
    chmod 700 "$TLS_DIR"

    ok "HAProxy installed and enabled"
}

# ============================================================
# SECTION: tls-merge
# ============================================================
sec_tls_merge() {
    if (( DRY_RUN )); then dry "Merge TLS certificate and key into ${HAPROXY_TLS_PEM}"; return 0; fi

    # Find the certificate and key
    local cert="" key=""
    if [[ -n "$TLS_CERT" && -f "$TLS_CERT" ]]; then
        cert="$TLS_CERT"
    elif [[ -f "${TLS_DIR}/server.crt" ]]; then
        cert="${TLS_DIR}/server.crt"
    elif [[ -f "${TLS_DIR}/fullchain.pem" ]]; then
        cert="${TLS_DIR}/fullchain.pem"
    elif [[ -f "${TLS_DIR}/cert.pem" ]]; then
        cert="${TLS_DIR}/cert.pem"
    else
        warn "No TLS certificate found. HAProxy will listen on HTTP only."
        warn "Place your certificate in ${TLS_DIR}/server.crt and re-run --only tls."
        return 0
    fi

    if [[ -n "$TLS_KEY" && -f "$TLS_KEY" ]]; then
        key="$TLS_KEY"
    elif [[ -f "${TLS_DIR}/server.key" ]]; then
        key="${TLS_DIR}/server.key"
    elif [[ -f "${TLS_DIR}/privkey.pem" ]]; then
        key="${TLS_DIR}/privkey.pem"
    else
        die "TLS key not found. Provide --tls-key or place it in ${TLS_DIR}/server.key."
    fi

    _log "Merging TLS certificate for HAProxy"
    cat "$cert" "$key" > "$HAPROXY_TLS_PEM"
    chmod 600 "$HAPROXY_TLS_PEM"
    chown root:haproxy "$HAPROXY_TLS_PEM" 2>/dev/null || chmod 600 "$HAPROXY_TLS_PEM"

    ok "TLS merged at ${HAPROXY_TLS_PEM} (cert + key)"
}

# ============================================================
# SECTION: haproxy-config
# ============================================================
sec_haproxy_config() {
    if (( DRY_RUN )); then
        dry "Write HAProxy config: mode=${MODE}, threshold=${LOCAL_THRESHOLD}, maxconn=${HAPROXY_MAXCONN}"
        return 0
    fi

    _log "Writing HAProxy configuration (mode: ${MODE})"

    local has_tls=0
    if [[ -f "$HAPROXY_TLS_PEM" ]]; then has_tls=1; fi

    local central_ca_line=""
    if [[ -n "$CENTRAL_CA_FILE" ]]; then
        # If it's a URL, download it
        if [[ "$CENTRAL_CA_FILE" == http* ]]; then
            _log "Downloading Central CA from ${CENTRAL_CA_FILE}"
            local ca_dest="/usr/local/share/ca-certificates/${APP_NAME}-central-ca.crt"
            mkdir -p "$(dirname "$ca_dest")"
            curl -fsSL "$CENTRAL_CA_FILE" -o "$ca_dest"
            update-ca-certificates 2>/dev/null || true
            CENTRAL_CA_FILE="$ca_dest"
        fi
        if [[ -f "$CENTRAL_CA_FILE" ]]; then
            central_ca_line="ca-file ${CENTRAL_CA_FILE}"
        else
            warn "Central CA file not found at ${CENTRAL_CA_FILE} — skipping SSL verification"
        fi
    fi

    # Determine central server address
    local central_server_addr="${CENTRAL_IP:-central.${DOMAIN:-ahpc.internal}}"
    local central_port="443"
    if [[ "$CENTRAL_URL" == http://* ]]; then central_port="80"; fi

    local backup=""
    if [[ -f "$HAPROXY_CONF" ]]; then
        backup="$(mktemp)"
        cp -p "$HAPROXY_CONF" "$backup"
    fi

    # Write the config
    cat > "$HAPROXY_CONF" <<HACFG
# Managed by ${APP_NAME} LB setup script — do not edit manually.
# Regenerate with: sudo ${APP_NAME}-lb-setup --only haproxy-config

global
    log /dev/log local0
    log /dev/log local1 notice
    chroot /var/lib/haproxy
    stats socket ${HAPROXY_STATS_SOCKET} mode 660 level admin
    stats timeout 30s
    user haproxy
    group haproxy
    daemon
    maxconn ${HAPROXY_MAXCONN}

    # TLS tuning
    ssl-default-bind-ciphers ECDHE-ECDSA-AES128-GCM-SHA256:ECDHE-RSA-AES128-GCM-SHA256:ECDHE-ECDSA-AES256-GCM-SHA384:ECDHE-RSA-AES256-GCM-SHA384
    ssl-default-bind-ciphersuites TLS_AES_128_GCM_SHA256:TLS_AES_256_GCM_SHA384:TLS_CHACHA20_POLY1305_SHA256
    ssl-default-bind-options ssl-min-ver TLSv1.2 no-tls-tickets

defaults
    mode http
    log global
    option httplog
    option dontlognull
    option forwardfor
    timeout connect 3s
    timeout client 60s
    timeout server 120s
    timeout queue 10s
    timeout http-keep-alive 15s
    timeout http-request 15s
    errorfile 400 /etc/haproxy/errors/400.http
    errorfile 403 /etc/haproxy/errors/403.http
    errorfile 408 /etc/haproxy/errors/408.http
    errorfile 500 /etc/haproxy/errors/500.http
    errorfile 502 /etc/haproxy/errors/502.http
    errorfile 503 /etc/haproxy/errors/503.http
    errorfile 504 /etc/haproxy/errors/504.http

# ── Stats page (access via http://${LAN_IP}:9000/haproxy?stats) ──

listen stats
    bind :9000
    mode http
    stats enable
    stats uri /haproxy?stats
    stats refresh 10s
    stats admin if LOCALHOST

# ── Frontend: terminates TLS, routes by cookie + load ──

frontend web
    bind :80
HACFG

    # Add TLS bind if certificate exists
    if (( has_tls )); then
        cat >> "$HAPROXY_CONF" <<HACFG
    bind :443 ssl crt ${HAPROXY_TLS_PEM}
HACFG
    fi

    cat >> "$HAPROXY_CONF" <<HACFG
    http-request redirect scheme https unless { ssl_fc }

    # Cookie-based routing — student sticks to the server that started their attempt
    acl route_local   req.cook(ahpc_route) -m str local
    acl route_central req.cook(ahpc_route) -m str central

    # Health and capacity checks
    acl local_down    nbsrv(local) lt 1
    acl central_down  nbsrv(central) lt 1
    acl local_full    be_conn(local) ge ${LOCAL_THRESHOLD}
    acl central_full  be_conn(central) ge $(( LOCAL_THRESHOLD * 2 / 3 ))

HACFG

    if [[ "$MODE" == "central-first" ]]; then
        # Central-first: students go to central, fallback to center
        cat >> "$HAPROXY_CONF" <<HACFG
    # Central-first routing
    use_backend local   if route_local
    use_backend central if route_central !central_down
    use_backend local   if central_down || central_full
    default_backend central
HACFG
    else
        # Center-first (default): students stay on center, overflow to central
        cat >> "$HAPROXY_CONF" <<HACFG
    # Center-first routing (default)
    use_backend central if route_central
    use_backend local   if route_local !local_down
    use_backend central if local_down
    use_backend central if local_full
    default_backend local
HACFG
    fi

    cat >> "$HAPROXY_CONF" <<'HACFG'

# ── Backend: local app server (Nginx on 127.0.0.1:8081) ──

backend local
    option httpchk GET /up
    http-response add-header Set-Cookie "ahpc_route=local; Path=/; Max-Age=COOKIE_AGE_PLACEHOLDER; HttpOnly; Secure; SameSite=Lax" unless { req.cook(ahpc_route) -m found }
    server app 127.0.0.1:8081 check maxconn LOCAL_MAXCONN_PLACEHOLDER

# ── Backend: central server ──

backend central
    http-response add-header Set-Cookie "ahpc_route=central; Path=/; Max-Age=COOKIE_AGE_PLACEHOLDER; HttpOnly; Secure; SameSite=Lax" unless { req.cook(ahpc_route) -m found }
HACFG

    # Replace placeholders
    sed -i "s/COOKIE_AGE_PLACEHOLDER/${COOKIE_MAX_AGE}/g" "$HAPROXY_CONF"
    sed -i "s/LOCAL_MAXCONN_PLACEHOLDER/${HAPROXY_LOCAL_MAXCONN}/g" "$HAPROXY_CONF"

    # Add central server line with or without SSL verification
    if [[ -n "$central_ca_line" && "$CENTRAL_URL" == https://* ]]; then
        cat >> "$HAPROXY_CONF" <<HACFG
    server central ${central_server_addr}:${central_port} check ssl verify required ${central_ca_line} verifyhost ${central_server_addr}
HACFG
    else
        cat >> "$HAPROXY_CONF" <<HACFG
    server central ${central_server_addr}:${central_port} check
HACFG
    fi

    # Validate config
    if ! haproxy -c -f "$HAPROXY_CONF" 2>&1; then
        if [[ -n "$backup" ]]; then
            cp -p "$backup" "$HAPROXY_CONF"
            ok "Restored previous HAProxy config (new config failed validation)"
        else
            rm -f "$HAPROXY_CONF"
        fi
        die "HAProxy rejected the generated config."
    fi

    ok "HAProxy config written (mode: ${MODE}, threshold: ${LOCAL_THRESHOLD}, maxconn: ${HAPROXY_MAXCONN})"
}

# ============================================================
# SECTION: nginx-reconfigure
# ============================================================
sec_nginx_reconfigure() {
    if (( DRY_RUN )); then dry "Move Nginx to 127.0.0.1:8081, add HTTPS header for Laravel"; return 0; fi

    detect_php
    _log "Reconfiguring Nginx for HAProxy"

    local site=""
    # Find the site config
    for f in "${NGINX_ENABLED_DIR}"/*; do
        if [[ -L "$f" ]] && grep -q "Managed by ${APP_NAME}" "$(readlink -f "$f")" 2>/dev/null; then
            site="$(readlink -f "$f")"
            break
        fi
    done

    if [[ -z "$site" ]]; then
        # Try domain-based name
        site="${NGINX_CONF_DIR}/${DOMAIN}"
        if [[ ! -f "$site" ]]; then
            warn "No Nginx site config found for ${DOMAIN} — skipping reconfigure."
            warn "Run the main setup script first, then re-run this section."
            return 0
        fi
    fi

    local backup="$(mktemp)"
    cp -p "$site" "$backup"

    _log "Moving Nginx to 127.0.0.1:8081"

    # Replace listen directives
    # Change "listen 80 default_server;" to "listen 127.0.0.1:8081;"
    # Change "listen [::]:80 default_server;" to remove IPv6
    # Change "listen 443 ssl http2 default_server;" to "listen 127.0.0.1:8081;"
    # Change "listen [::]:443 ssl http2 default_server;" to remove IPv6

    sed -i -E 's|^[[:space:]]*listen 80 default_server;|    listen 127.0.0.1:8081;|' "$site"
    sed -i -E '/^[[:space:]]*listen \[::\]:80 default_server;/d' "$site"
    sed -i -E 's|^[[:space:]]*listen 443 ssl http2 default_server;|    listen 127.0.0.1:8081;|' "$site"
    sed -i -E '/^[[:space:]]*listen \[::\]:443 ssl http2 default_server;/d' "$site"

    # Remove SSL directives from Nginx (HAProxy terminates TLS)
    sed -i -E '/^[[:space:]]*ssl_certificate/d' "$site"
    sed -i -E '/^[[:space:]]*ssl_certificate_key/d' "$site"
    sed -i -E '/^[[:space:]]*ssl_protocols/d' "$site"
    sed -i -E '/^[[:space:]]*ssl_session_cache/d' "$site"
    sed -i -E '/^[[:space:]]*ssl_session_timeout/d' "$site"
    sed -i -E '/^[[:space:]]*ssl_session_tickets/d' "$site"

    # Add fastcgi_param HTTPS on; so Laravel sees HTTPS through HAProxy
    if ! grep -q 'fastcgi_param HTTPS' "$site"; then
        sed -i '/fastcgi_pass/a\        fastcgi_param HTTPS on;' "$site"
    fi

    # Add TrustProxies headers so Laravel knows the real client IP
    if ! grep -q 'X-Forwarded-For' "$site"; then
        # Add proxy headers after server_name block
        sed -i '/server_name/a\
\
    # HAProxy forwards these headers — trust them in Laravel (TrustProxies)\
    set_real_ip_from 127.0.0.1;\
    real_ip_header X-Forwarded-For;\
    real_ip_recursive on;' "$site"
    fi

    # Remove the HTTP-to-HTTPS redirect server block if present (HAProxy handles it)
    # We need to remove the entire first server block that only does redirects
    # This is tricky with sed; we'll use a perl one-liner for multi-line removal
    if grep -q 'return 301 https' "$site"; then
        _log "Removing HTTP-to-HTTPS redirect block (HAProxy handles this)"
        perl -i -0pe 's/server \{\n(?:.*?return 301 https:\/\/.*?\n)*.*?\n\}\n\n//s' "$site" 2>/dev/null || true
    fi

    # Remove Let's Encrypt challenge location (HAProxy doesn't need it on 8081)
    perl -i -0pe 's/.*location \^~ \/.well-known\/acme-challenge\/.*?\n.*?\n.*?\}//s' "$site" 2>/dev/null || true

    # Validate
    if ! nginx -t 2>&1; then
        cp -p "$backup" "$site"
        ok "Restored previous Nginx config (new config failed validation)"
        die "Nginx rejected the reconfigured site."
    fi

    if systemctl is-active --quiet nginx; then
        systemctl reload nginx
    else
        systemctl start nginx
    fi

    ok "Nginx moved to 127.0.0.1:8081 (HAProxy terminates TLS on 80/443)"
}

# ============================================================
# SECTION: health-endpoint
# ============================================================
sec_health_endpoint() {
    if (( DRY_RUN )); then dry "Check that /up route exists on the Laravel app"; return 0; fi

    _log "Checking /up health endpoint"

    # Check if the app has a /up route
    local project_dir="/var/www/html/${APP_NAME}"
    if [[ ! -f "${project_dir}/artisan" ]]; then
        warn "Laravel project not found at ${project_dir} — skipping health check verification."
        return 0
    fi

    # Check routes
    local has_up=0
    if grep -rq "Route.*up\|up.*route\|health.*check" "${project_dir}/routes/" 2>/dev/null; then
        has_up=1
    fi

    # Also check if Fortify or a health package provides it
    if grep -rq "'/up'\|\"/up\"" "${project_dir}/routes/" 2>/dev/null; then
        has_up=1
    fi

    # Laravel 11 ships /up by default via the health route
    if (( has_up )); then
        ok "/up health endpoint found in routes"
    else
        warn "/up route not explicitly found — Laravel 11 may provide it via default health check."
        warn "If HAProxy health checks fail, add: Route::get('/up', fn() => response('ok', 200));"
        warn "in routes/web.php"
    fi

    # Test the endpoint via Nginx (on 8081)
    local status
    status="$(curl -s -o /dev/null -w '%{http_code}' --max-time 5 http://127.0.0.1:8081/up -H "Host: ${DOMAIN}" 2>/dev/null || echo "000")"
    if [[ "$status" == "200" ]]; then
        ok "/up endpoint responds with HTTP 200"
    else
        warn "/up endpoint returned HTTP ${status} — HAProxy health checks may mark the backend as down"
    fi
}

# ============================================================
# SECTION: logrotate
# ============================================================
sec_logrotate() {
    if (( DRY_RUN )); then dry "Logrotate config for HAProxy logs"; return 0; fi

    _log "Setting up HAProxy log rotation"

    cat > "/etc/logrotate.d/${APP_NAME}-haproxy" <<'LOGROTATE'
/var/log/haproxy.log {
    daily
    missingok
    rotate 14
    compress
    delaycompress
    notifempty
    create 0640 haproxy adm
    sharedscripts
    postrotate
        /usr/lib/haproxy/haproxy-systemd-wrapper -f /etc/haproxy/haproxy.cfg -p /run/haproxy.pid -sf $(cat /run/haproxy.old.pid 2>/dev/null) &
        kill -USR1 $(cat /run/haproxy.pid 2>/dev/null) 2>/dev/null || true
    endscript
}
LOGROTATE

    ok "HAProxy log rotation configured (14 days)"
}

# ============================================================
# SECTION: systemd
# ============================================================
sec_systemd() {
    if (( DRY_RUN )); then dry "Systemd override for HAProxy (file descriptors, socket activation)"; return 0; fi

    _log "Configuring systemd for HAProxy"

    # Increase file descriptors for HAProxy
    mkdir -p /etc/systemd/system/haproxy.service.d
    cat > "/etc/systemd/system/haproxy.service.d/${APP_NAME}-limits.conf" <<'SYSTEMD'
[Service]
LimitNOFILE=65535
LimitMEMLOCK=infinity
SYSTEMD

    # Configure rsyslog for HAProxy (if rsyslog is used)
    if command -v rsyslog >/dev/null 2>&1 && systemctl is-active --quiet rsyslog; then
        if ! grep -q "haproxy" /etc/rsyslog.d/*.conf 2>/dev/null; then
            cat > "/etc/rsyslog.d/${APP_NAME}-haproxy.conf" <<'RSYSLOG'
# HAProxy logging
$AddUnixListenSocket /var/lib/haproxy/dev/log

local0.*    /var/log/haproxy.log
local1.*    /var/log/haproxy.log
RSYSLOG
            systemctl restart rsyslog 2>/dev/null || true
            ok "rsyslog configured for HAProxy"
        fi
    fi

    # Create socket directory with proper permissions
    mkdir -p /var/lib/haproxy/dev
    chown haproxy:haproxy /var/lib/haproxy/dev 2>/dev/null || true

    systemctl daemon-reload
    ok "Systemd configured for HAProxy"
}

# ============================================================
# SECTION: finalize
# ============================================================
sec_finalize() {
    if (( DRY_RUN )); then dry "Restart HAProxy, validate config, test routing"; return 0; fi

    _log "Final validation and restart"

    # Validate HAProxy config one more time
    if ! haproxy -c -f "$HAPROXY_CONF" 2>&1; then
        die "HAProxy config validation failed. Check ${HAPROXY_CONF}"
    fi

    # Restart HAProxy
    if systemctl is-active --quiet haproxy; then
        systemctl restart haproxy
    else
        systemctl start haproxy
    fi

    # Wait for HAProxy to start
    sleep 2

    # Check HAProxy is running
    if systemctl is-active --quiet haproxy; then
        ok "HAProxy is running"
    else
        warn "HAProxy failed to start. Check: journalctl -u haproxy"
        return 1
    fi

    # Test HTTP on port 80
    local status
    status="$(curl -s -o /dev/null -w '%{http_code}' --max-time 10 http://127.0.0.1/ -H "Host: ${DOMAIN}" 2>/dev/null || echo "000")"
    if [[ "$status" =~ ^[23] ]]; then
        ok "HAProxy routing works (HTTP ${status})"
    else
        warn "HAProxy returned HTTP ${status} — check backend connectivity"
    fi

    # Test stats socket
    if [[ -S "$HAPROXY_STATS_SOCKET" ]]; then
        local show_stat
        show_stat="$(echo "show stat" | socat unix-connect:"$HAPROXY_STATS_SOCKET" stdio 2>/dev/null | head -5 || true)"
        if [[ -n "$show_stat" ]]; then
            ok "HAProxy stats socket is accessible"
        fi
    fi

}

# ============================================================
# SECTION: summary
# ============================================================
sec_summary() {
    if (( DRY_RUN )); then dry "Print the load balancer summary"; return 0; fi

    local proto="http"
    if [[ -f "$HAPROXY_TLS_PEM" ]]; then proto="https"; fi

    {
        echo "AHPC Load Balancer — Configuration Summary"
        echo "Generated: $(date)"
        echo
        echo "HAProxy Mode:       ${MODE}"
        echo "  - ${MODE} routing"
        echo "  - Local threshold: ${LOCAL_THRESHOLD} connections"
        echo "  - Global maxconn:  ${HAPROXY_MAXCONN}"
        echo "  - Local maxconn:   ${HAPROXY_LOCAL_MAXCONN}"
        echo "  - Cookie age:      ${COOKIE_MAX_AGE}s ($(( COOKIE_MAX_AGE / 3600 ))h)"
        echo
        echo "Frontend:           ${proto}://${DOMAIN} (or ${proto}://${LAN_IP})"
        echo "  - Port 80:         HTTP (redirects to HTTPS)"
        if [[ -f "$HAPROXY_TLS_PEM" ]]; then
            echo "  - Port 443:        HTTPS (TLS terminated by HAProxy)"
        else
            echo "  - Port 443:        not configured (no TLS cert)"
        fi
        echo "  - Stats:           http://${LAN_IP}:9000/haproxy?stats"
        echo
        echo "Backends:"
        echo "  - Local (center):  127.0.0.1:8081 (Nginx)"
        if [[ -n "$CENTRAL_IP" ]]; then
            echo "  - Central:         ${CENTRAL_IP} (${CENTRAL_URL:-n/a})"
        else
            echo "  - Central:         ${CENTRAL_URL:-not configured}"
        fi
        echo
        echo "Nginx:              Moved to 127.0.0.1:8081 (HAProxy on 80/443)"
        echo "Health check:       GET /up"
        echo
        echo "Configuration files:"
        echo "  HAProxy:          ${HAPROXY_CONF}"
        if [[ -f "$HAPROXY_TLS_PEM" ]]; then
            echo "  TLS PEM:          ${HAPROXY_TLS_PEM}"
        fi
        echo "  State:            ${STATE_FILE}"
        echo
        echo "Important notes:"
        echo "  1. Students stick to their assigned server via ahpc_route cookie."
        echo "  2. Cookie lasts ${COOKIE_MAX_AGE}s — set this to your longest exam + margin."
        echo "  3. Ensure Laravel TrustProxies trusts 127.0.0.1 for X-Forwarded-For."
        echo "  4. Test with: echo 'show stat' | socat unix-connect:${HAPROXY_STATS_SOCKET} stdio"
        echo "  5. Monitor: http://${LAN_IP}:9000/haproxy?stats"
        echo
        echo "  To reconfigure later:"
        echo "    sudo ${APP_NAME}-lb-setup --list-sections"
        echo "    sudo ${APP_NAME}-lb-setup --only config          # rewrite both configs"
        echo "    sudo ${APP_NAME}-lb-setup --mode central-first   # flip routing mode"
    } | tee "$CREDS_FILE"

    chmod 600 "$CREDS_FILE"

    _log "DONE — Load balancer setup complete"
}

# ============================================================
# MAIN
# ============================================================
main() {
    parse_args "$@"
    if (( SHOW_HELP )); then usage; exit 0; fi
    if (( LIST_SECTIONS )); then list_sections; exit 0; fi
    [[ $EUID -eq 0 ]] || die "This script must be run as root (use sudo)."

    if (( ! DRY_RUN )); then
        mkdir -p "$(dirname "$LOG_FILE")"
        touch "$LOG_FILE"
        chmod 600 "$LOG_FILE"
        exec 9>"$LOCK_FILE"
        flock -n 9 || die "Another ${APP_NAME}-lb-setup run is in progress (lock: ${LOCK_FILE})."
        exec > >(tee -a "$LOG_FILE") 2>&1
    else
        _log "DRY-RUN MODE — no changes will be made"
    fi

    # ── Settings: CLI flags > saved state > defaults ──
    load_state
    : "${MODE:=center-first}"
    : "${DOMAIN:=ahpc.internal}"
    : "${LOCAL_THRESHOLD:=150}"
    : "${HAPROXY_MAXCONN:=4000}"
    : "${HAPROXY_LOCAL_MAXCONN:=180}"
    : "${COOKIE_MAX_AGE:=14400}"

    # Validate mode
    case "$MODE" in
        center-first|central-first) ;;
        *) die "--mode must be 'center-first' or 'central-first', got '${MODE}'." ;;
    esac

    # Validate numeric params
    [[ "$LOCAL_THRESHOLD" =~ ^[0-9]+$ ]] || die "--local-threshold must be a number."
    [[ "$HAPROXY_MAXCONN" =~ ^[0-9]+$ ]] || die "--maxconn must be a number."
    [[ "$HAPROXY_LOCAL_MAXCONN" =~ ^[0-9]+$ ]] || die "--local-maxconn must be a number."
    [[ "$COOKIE_MAX_AGE" =~ ^[0-9]+$ ]] || die "--cookie-age must be a number."

    # ── Which sections run ──
    build_selection

    detect_lan_ip
    detect_php

    local plan=""
    for s in "${ALL_SECTIONS[@]}"; do if sel "$s"; then plan+="${s} "; fi; done
    info "Mode: ${MODE} | Domain: ${DOMAIN} | LAN IP: ${LAN_IP} | Sections: ${plan}"
    info "Threshold: ${LOCAL_THRESHOLD} | Maxconn: ${HAPROXY_MAXCONN} | Local maxconn: ${HAPROXY_LOCAL_MAXCONN}"

    # ── Run ──
    for s in "${ALL_SECTIONS[@]}"; do
        if sel "$s"; then
            banner "Section: ${s} — $(section_desc "$s")"
            "sec_${s//-/_}"
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
