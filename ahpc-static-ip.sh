#!/usr/bin/env bash
# ahpc-static-ip.sh  v2.0
# Persistent static IP for AHPC servers. NetworkManager is the ONLY network manager.
#
# Usage:
#   sudo bash ahpc-static-ip.sh [OPTIONS]
#
# Options:
#   --ip      ADDR       Static IP to assign           (default: current IP)
#   --prefix  NUM        Prefix length (1-32)          (default: current, else 24)
#   --gateway ADDR       Default gateway               (default: current gateway)
#   --no-gateway         Configure no default gateway (isolated LAN)
#   --dns     A[,B]      DNS servers, comma-separated  (default: current link DNS, else gateway)
#   --iface   NAME       Network interface             (default: physical NIC holding the default
#                                                       route, else first physical NIC with link)
#   --dhcp               Use DHCP instead of a static address
#   --dry-run            Print what would be done, change nothing
#   --status             Show the current network-management state and exit
#   --confirm            Cancel a pending auto-revert (see below) after you reconnected
#   --rollback-secs N    Auto-revert delay when run over SSH            (default: 120)
#   --no-rollback        Never arm the auto-revert timer
#   --help               Show this help
#
# Safety net: when run over SSH, the script arms a one-shot timer that restores the
# previous settings after --rollback-secs seconds. If you can still reach the server
# on the new address, run "sudo bash ahpc-static-ip.sh --confirm" to keep the change.
#
# Examples:
#   sudo bash ahpc-static-ip.sh --ip 192.168.1.50 --gateway 192.168.1.1
#   sudo bash ahpc-static-ip.sh --ip 10.0.0.10 --prefix 24 --dns 10.0.0.1 --iface enp3s0
#   sudo bash ahpc-static-ip.sh --ip 10.0.0.10 --no-gateway
#   sudo bash ahpc-static-ip.sh --dhcp
#   sudo bash ahpc-static-ip.sh --status
# ============================================================

set -euo pipefail

# ── constants ─────────────────────────────────────────────────
CON_NAME="ahpc-lan"
NETPLAN_DIR="/etc/netplan"
NETPLAN_FILE="${NETPLAN_DIR}/01-ahpc-nm.yaml"
NM_CONF="/etc/NetworkManager/conf.d/90-ahpc.conf"
NM_KEYFILE_DIR="/etc/NetworkManager/system-connections"
CLOUD_CFG="/etc/cloud/cloud.cfg.d/99-disable-network-config.cfg"
STATE_DIR="/var/lib/ahpc"
PREV_ENV="${STATE_DIR}/prev.env"
REVERT_HELPER="${STATE_DIR}/revert-helper.sh"
REVERT_UNIT="ahpc-revert"
LOG_FILE="/var/log/ahpc-static-ip.log"
LOCK_FILE="/run/ahpc-static-ip.lock"
SCRIPT_PATH="$(readlink -f "${BASH_SOURCE[0]}")"

# ── defaults ──────────────────────────────────────────────────
OPT_IP=""; OPT_PREFIX=""; OPT_GATEWAY=""; OPT_DNS=""; OPT_IFACE=""
OPT_DHCP=0; OPT_NOGW=0; OPT_NOROLLBACK=0
DRY_RUN=0; MODE="apply"; ROLLBACK_SECS=120
IFACE=""; IP=""; PREFIX=""; GATEWAY=""; DNS=""

# ── output helpers ────────────────────────────────────────────
if [[ -t 1 ]]; then
    RED=$'\033[0;31m'; YEL=$'\033[1;33m'; GRN=$'\033[0;32m'; CYN=$'\033[0;36m'; RST=$'\033[0m'
else
    RED=""; YEL=""; GRN=""; CYN=""; RST=""
fi

_logfile() {
    if (( DRY_RUN )); then return 0; fi
    { printf '%s %s\n' "$(date '+%F %T')" "$*" >> "$LOG_FILE"; } 2>/dev/null || true
}
_log() { echo "${CYN}[INFO]${RST}  $*"; _logfile "INFO  $*"; }
ok()   { echo "${GRN}[ OK ]${RST}  $*"; _logfile "OK    $*"; }
warn() { echo "${YEL}[WARN]${RST}  $*" >&2; _logfile "WARN  $*"; }
die()  { echo "${RED}[FAIL]${RST}  $*" >&2; _logfile "FAIL  $*"; exit 1; }
dry()  { echo "${YEL}[DRY ]${RST}  $*"; }

# run: execute, or just print under --dry-run.  runq: same, but silences output.
run()  { if (( DRY_RUN )); then dry "$*"; else "$@"; fi; }
runq() { if (( DRY_RUN )); then dry "$*"; else "$@" >/dev/null 2>&1; fi; }

# put_file PATH MODE  (content on stdin)
put_file() {
    local path="$1" mode="$2"
    if (( DRY_RUN )); then
        dry "write ${path} (mode ${mode})"
        cat >/dev/null
        return 0
    fi
    install -d -m 755 "$(dirname "$path")"
    cat > "$path"
    chmod "$mode" "$path"
}

usage() {
    sed -n '2,/^# =====/p' "$SCRIPT_PATH" | sed 's/^# \{0,1\}//' | grep -v '^=====' || true
    exit 0
}

# ── argument parsing ──────────────────────────────────────────
need_val() { [[ $# -ge 2 && -n "${2:-}" ]] || die "Option $1 requires a value"; }

while [[ $# -gt 0 ]]; do
    case "$1" in
        --ip)            need_val "$@"; OPT_IP="$2";        shift 2 ;;
        --prefix)        need_val "$@"; OPT_PREFIX="$2";    shift 2 ;;
        --gateway)       need_val "$@"; OPT_GATEWAY="$2";   shift 2 ;;
        --no-gateway)    OPT_NOGW=1;                        shift   ;;
        --dns)           need_val "$@"; OPT_DNS="$2";       shift 2 ;;
        --iface)         need_val "$@"; OPT_IFACE="$2";     shift 2 ;;
        --dhcp)          OPT_DHCP=1;                        shift   ;;
        --dry-run)       DRY_RUN=1;                         shift   ;;
        --status)        MODE="status";                     shift   ;;
        --confirm)       MODE="confirm";                    shift   ;;
        --revert)        MODE="revert";                     shift   ;;   # internal: used by the timer
        --rollback-secs) need_val "$@"; ROLLBACK_SECS="$2"; shift 2 ;;
        --no-rollback)   OPT_NOROLLBACK=1;                  shift   ;;
        --help|-h)       MODE="help";                       shift   ;;
        *) die "Unknown argument: $1  (run with --help)" ;;
    esac
done

[[ "$ROLLBACK_SECS" =~ ^[0-9]+$ ]] || die "--rollback-secs must be a number"

need_root() { [[ "$EUID" -eq 0 ]] || die "Must be run as root (sudo bash $0)"; }

# ── validation helpers ────────────────────────────────────────
valid_ipv4() {
    local ip="$1" o
    [[ "$ip" =~ ^[0-9]{1,3}(\.[0-9]{1,3}){3}$ ]] || return 1
    local IFS=.
    for o in $ip; do (( 10#$o <= 255 )) || return 1; done
    return 0
}

# ── interface / address discovery ─────────────────────────────
# Physical wired NICs only: has a backing device, is not wireless. This skips
# VPN/mesh (tailscale0, nordlynx, wg0), docker0, veth*, bridges, etc.
is_physical() { [[ -e "/sys/class/net/$1/device" && ! -d "/sys/class/net/$1/wireless" ]]; }

detect_iface() {
    local dev d
    dev="$(ip -4 route show default 2>/dev/null | awk '{for(i=1;i<=NF;i++) if($i=="dev"){print $(i+1); exit}}')"
    if [[ -n "$dev" ]] && is_physical "$dev"; then printf '%s' "$dev"; return 0; fi
    for d in /sys/class/net/*; do
        d="${d##*/}"
        if is_physical "$d" && [[ "$(cat "/sys/class/net/$d/carrier" 2>/dev/null || echo 0)" == "1" ]]; then
            printf '%s' "$d"; return 0
        fi
    done
    for d in /sys/class/net/*; do
        d="${d##*/}"
        if is_physical "$d"; then printf '%s' "$d"; return 0; fi
    done
    return 0
}

cur_cidr()   { ip -4 -o addr show dev "$1" scope global 2>/dev/null | awk '{print $4; exit}'; }
cur_ip()     { local c; c="$(cur_cidr "$1")"; printf '%s' "${c%/*}"; }
cur_prefix() { local c; c="$(cur_cidr "$1")"; [[ "$c" == */* ]] && printf '%s' "${c#*/}"; return 0; }
cur_gw()     { ip -4 route show default dev "$1" 2>/dev/null | awk '{print $3; exit}'; }
cur_dynamic() {
    local out; out="$(ip -4 -o addr show dev "$1" scope global 2>/dev/null || true)"
    [[ "$out" == *" dynamic "* ]]
}
cur_dns() {
    command -v resolvectl >/dev/null 2>&1 || return 0
    resolvectl dns "$1" 2>/dev/null | grep -oE '[0-9]+(\.[0-9]+){3}' | paste -sd, - || true
}

# ── NetworkManager profile helpers ────────────────────────────
con_exists() {
    command -v nmcli >/dev/null 2>&1 || return 1
    local names; names="$(nmcli -g NAME connection show 2>/dev/null || true)"
    grep -Fxq "$CON_NAME" <<<"$names"
}
con_get() {
    local v; v="$(nmcli -g "$1" connection show "$CON_NAME" 2>/dev/null || true)"
    [[ "$v" == "--" ]] && v=""
    printf '%s' "$v"
}

# ── previous-state capture / restore (used by rollback + timer) ──
capture_prev_state() {
    local method addr gw dns c
    if con_exists; then
        method="$(con_get ipv4.method)"; addr="$(con_get ipv4.addresses)"
        gw="$(con_get ipv4.gateway)";    dns="$(con_get ipv4.dns)"
    elif cur_dynamic "$IFACE"; then
        method="auto"; addr=""; gw=""; dns=""
    else
        c="$(cur_cidr "$IFACE")"
        if [[ -n "$c" ]]; then
            method="manual"; addr="$c"; gw="$(cur_gw "$IFACE")"; dns="$(cur_dns "$IFACE")"
        else
            method="auto"; addr=""; gw=""; dns=""
        fi
    fi
    if (( DRY_RUN )); then dry "save previous state (${method:-auto} ${addr}) to ${PREV_ENV}"; return 0; fi
    install -d -m 700 "$STATE_DIR"
    {
        printf 'PREV_METHOD=%q\n' "${method:-auto}"
        printf 'PREV_ADDR=%q\n'   "$addr"
        printf 'PREV_GW=%q\n'     "$gw"
        printf 'PREV_DNS=%q\n'    "$dns"
    } > "$PREV_ENV"
    chmod 600 "$PREV_ENV"
}

restore_prev() {
    [[ -r "$PREV_ENV" ]] || { warn "No saved previous state to restore"; return 1; }
    # shellcheck disable=SC1090
    source "$PREV_ENV"
    con_exists || { warn "Profile ${CON_NAME} does not exist; nothing to restore"; return 1; }
    if [[ "$PREV_METHOD" == "manual" && -n "$PREV_ADDR" ]]; then
        nmcli connection modify "$CON_NAME" ipv4.method manual ipv4.addresses "$PREV_ADDR" \
            ipv4.gateway "$PREV_GW" ipv4.dns "$PREV_DNS" ipv4.ignore-auto-dns yes
    else
        nmcli connection modify "$CON_NAME" ipv4.method auto ipv4.addresses "" \
            ipv4.gateway "" ipv4.dns "" ipv4.ignore-auto-dns no
    fi
    nmcli -w 30 connection up "$CON_NAME" >/dev/null 2>&1 || true
}

# ── SSH auto-revert safety net ────────────────────────────────
over_ssh() {
    [[ -n "${SSH_CONNECTION:-}${SSH_CLIENT:-}" ]] && return 0
    local out; out="$(ss -Htn state established '( sport = :22 )' 2>/dev/null || true)"
    [[ -n "$out" ]]
}

disarm_revert() {
    systemctl stop "${REVERT_UNIT}.timer" "${REVERT_UNIT}.service" >/dev/null 2>&1 || true
    systemctl reset-failed "${REVERT_UNIT}.service" "${REVERT_UNIT}.timer" >/dev/null 2>&1 || true
}

arm_revert() {
    if (( OPT_NOROLLBACK )); then return 0; fi
    if ! over_ssh; then return 0; fi
    if (( DRY_RUN )); then
        dry "arm auto-revert in ${ROLLBACK_SECS}s (SSH session detected)"
        return 0
    fi
    install -d -m 700 "$STATE_DIR"
    install -m 700 "$SCRIPT_PATH" "$REVERT_HELPER"
    disarm_revert
    systemd-run --quiet --unit="$REVERT_UNIT" --on-active="${ROLLBACK_SECS}s" \
        bash "$REVERT_HELPER" --revert
    warn "SSH session detected: auto-revert armed (${ROLLBACK_SECS}s)."
    warn "After you can reach the server again, run:  sudo bash $0 --confirm"
}

confirm_revert() {
    if systemctl is-active --quiet "${REVERT_UNIT}.timer" 2>/dev/null; then
        disarm_revert
        ok "Auto-revert cancelled. The new configuration is now permanent."
    else
        _log "No auto-revert is pending."
    fi
}

do_revert() {
    _log "Auto-revert triggered: restoring previous network settings"
    if restore_prev; then ok "Previous settings restored"; else warn "Could not restore previous settings"; fi
}

# ── status ────────────────────────────────────────────────────
show_status() {
    local iface f
    iface="${OPT_IFACE:-$(detect_iface)}"
    echo "== AHPC network status =="
    echo "Interface        : ${iface:-<none detected>}"
    echo "NetworkManager   : $(systemctl is-active NetworkManager 2>/dev/null || true) / $(systemctl is-enabled NetworkManager 2>/dev/null || true)"
    echo "systemd-networkd : $(systemctl is-active systemd-networkd 2>/dev/null || true) / $(systemctl is-enabled systemd-networkd 2>/dev/null || true)  (want: inactive / disabled)"
    echo "Netplan files    :"
    if compgen -G "${NETPLAN_DIR}/*.yaml" >/dev/null; then
        for f in "$NETPLAN_DIR"/*.yaml; do echo "   $f"; done
    else
        echo "   (none)"
    fi
    echo "                   (want: only ${NETPLAN_FILE})"
    if compgen -G "${NM_KEYFILE_DIR}/*.nmconnection" >/dev/null && grep -ls "^id=${CON_NAME}$" "${NM_KEYFILE_DIR}"/*.nmconnection >/dev/null 2>&1; then
        echo "Profile file     : persistent (${NM_KEYFILE_DIR})"
    else
        echo "Profile file     : NOT FOUND in ${NM_KEYFILE_DIR}  (settings may not survive reboot)"
    fi
    if [[ -n "$iface" ]]; then
        echo "Address          : $(ip -4 -br addr show dev "$iface" 2>/dev/null | awk '{$1="";$2=""; print}' | xargs || true)"
        echo "Default gateway  : $(cur_gw "$iface")"
    fi
    if command -v nmcli >/dev/null 2>&1; then
        echo "NM devices       :"; nmcli -t -f DEVICE,TYPE,STATE,CONNECTION device status 2>/dev/null | sed 's/^/   /' || true
    fi
    if systemctl is-active --quiet "${REVERT_UNIT}.timer" 2>/dev/null; then
        echo "Auto-revert      : PENDING  (run --confirm to keep current settings)"
    fi
}

# ── mode dispatch for non-apply modes ─────────────────────────
case "$MODE" in
    help)    usage ;;
    status)  show_status; exit 0 ;;
    confirm) need_root; confirm_revert; exit 0 ;;
    revert)  need_root; do_revert; exit 0 ;;
esac

need_root

# ── single instance ───────────────────────────────────────────
exec 9>"$LOCK_FILE"
flock -n 9 || die "Another ahpc-static-ip.sh run is already in progress"

# ── install / start NetworkManager ────────────────────────────
ensure_nm_installed() {
    if command -v nmcli >/dev/null 2>&1; then return 0; fi
    _log "NetworkManager is not installed — trying apt"
    if (( DRY_RUN )); then dry "apt-get install -y network-manager"; return 0; fi
    if ! DEBIAN_FRONTEND=noninteractive timeout 300 apt-get install -y network-manager >/dev/null 2>&1; then
        die "Could not install NetworkManager (no internet?). Pre-install it while online:  apt install network-manager"
    fi
}

# ── netplan: one passthrough file, no device definitions ──────
take_over_netplan() {
    if ! command -v netplan >/dev/null 2>&1; then return 0; fi
    local f moved=0 bak
    bak="${NETPLAN_DIR}/ahpc-backup-$(date +%Y%m%d-%H%M%S)"
    for f in "$NETPLAN_DIR"/*.yaml; do
        [[ -e "$f" ]] || continue
        [[ "$f" == "$NETPLAN_FILE" ]] && continue
        if (( ! moved )); then run install -d -m 700 "$bak"; moved=1; fi
        _log "Moving ${f} -> ${bak}/"
        run mv "$f" "$bak/"
    done
    # Only the renderer is declared here. Interfaces are NOT defined, so NetworkManager
    # keeps its own profiles in /etc/NetworkManager/system-connections/ (persistent),
    # instead of netplan regenerating a temporary one in /run at every boot.
    put_file "$NETPLAN_FILE" 600 <<'YAML'
# Managed by ahpc-static-ip.sh — NetworkManager owns all networking
network:
  version: 2
  renderer: NetworkManager
YAML
    if ! run netplan generate; then warn "netplan generate reported a problem; check: netplan generate"; fi
}

# cloud-init would otherwise rewrite /etc/netplan/50-cloud-init.yaml on boot
disable_cloud_init_net() {
    if [[ ! -d /etc/cloud ]]; then return 0; fi
    put_file "$CLOUD_CFG" 644 <<'EOF'
network: {config: disabled}
EOF
}

# ── NetworkManager configuration ──────────────────────────────
write_nm_conf() {
    # managed=1       : make NM manage this NIC (Ubuntu leaves ethernet unmanaged by default)
    # ignore-carrier  : keep the static IP when the cable/switch is down at boot, so services
    #                   bound to the address (nginx etc.) still start
    # no-auto-default : never auto-create surprise DHCP "Wired connection N" profiles
    put_file "$NM_CONF" 644 <<EOF
# Managed by ahpc-static-ip.sh — do not edit by hand
[main]
ignore-carrier=interface-name:${IFACE}
no-auto-default=*

[device-ahpc-lan]
match-device=interface-name:${IFACE}
managed=1
EOF
}

start_nm() {
    run systemctl enable NetworkManager
    runq systemctl enable NetworkManager-wait-online.service || true
    run systemctl start NetworkManager
    if (( DRY_RUN )); then dry "reload NM config; nmcli device set ${IFACE} managed yes"; return 0; fi
    for _ in $(seq 1 15); do nmcli -t general status >/dev/null 2>&1 && break; sleep 1; done
    nmcli general reload conf >/dev/null 2>&1 || systemctl reload NetworkManager >/dev/null 2>&1 || true
    nmcli connection reload >/dev/null 2>&1 || true
    nmcli device set "$IFACE" managed yes >/dev/null 2>&1 || true
}

stop_networkd_runtime() {
    if systemctl is-active --quiet systemd-networkd 2>/dev/null; then
        _log "Stopping systemd-networkd (addresses stay in place until NetworkManager takes over)"
        run systemctl stop systemd-networkd.socket systemd-networkd.service
    fi
}

disable_networkd() {
    # Done only after NetworkManager is verified, so a failed run never leaves the box unmanaged.
    runq systemctl disable systemd-networkd.service systemd-networkd.socket systemd-networkd-wait-online.service || true
    runq systemctl mask systemd-networkd-wait-online.service || true
}

# ── the AHPC connection profile ───────────────────────────────
apply_profile() {
    local mode="$1"
    local -a props
    props=(connection.interface-name "$IFACE"
           connection.autoconnect yes
           connection.autoconnect-priority 100
           connection.autoconnect-retries 0
           connection.mdns no
           ipv6.method link-local)
    if [[ "$mode" == "static" ]]; then
        props+=(ipv4.method manual ipv4.addresses "${IP}/${PREFIX}"
                ipv4.gateway "$GATEWAY" ipv4.dns "$DNS" ipv4.ignore-auto-dns yes)
    else
        props+=(ipv4.method auto ipv4.addresses "" ipv4.gateway "" ipv4.dns "" ipv4.ignore-auto-dns no)
    fi

    if (( DRY_RUN )); then
        dry "nmcli connection add/modify ${CON_NAME}: ${props[*]}"
        return 0
    fi
    if ! con_exists; then
        _log "Creating NetworkManager profile ${CON_NAME} on ${IFACE}"
        nmcli connection add type ethernet ifname "$IFACE" con-name "$CON_NAME" \
            connection.autoconnect no ipv4.method disabled >/dev/null
    fi
    nmcli connection modify "$CON_NAME" "${props[@]}"
}

# Any other ethernet profile for this NIC (or unbound) must not compete at boot
demote_other_profiles() {
    if (( DRY_RUN )); then dry "disable autoconnect on other ethernet profiles for ${IFACE}"; return 0; fi
    local uuid type id ifn
    while IFS=: read -r uuid type; do
        [[ "$type" == "802-3-ethernet" || "$type" == "ethernet" ]] || continue
        id="$(nmcli -g connection.id connection show "$uuid" 2>/dev/null || true)"
        [[ "$id" == "$CON_NAME" ]] && continue
        ifn="$(nmcli -g connection.interface-name connection show "$uuid" 2>/dev/null || true)"
        if [[ -z "$ifn" || "$ifn" == "$IFACE" ]]; then
            _log "Disabling autoconnect on competing profile: ${id:-$uuid}"
            nmcli connection modify "$uuid" connection.autoconnect no >/dev/null 2>&1 \
                || warn "Could not modify profile ${id:-$uuid} (it may be temporary and vanish on next boot)"
        fi
    done < <(nmcli -t -f UUID,TYPE connection show 2>/dev/null || true)
}

# ── verification ──────────────────────────────────────────────
net_verify() {
    local want_ip="$1" out=""
    for _ in $(seq 1 15); do
        out="$(ip -4 -o addr show dev "$IFACE" scope global 2>/dev/null || true)"
        if [[ -n "$want_ip" ]]; then
            [[ "$out" == *" ${want_ip}/"* ]] && return 0
        elif [[ -n "$out" ]]; then
            return 0
        fi
        sleep 2
    done
    return 1
}

link_up() { [[ "$(cat "/sys/class/net/${IFACE}/carrier" 2>/dev/null || echo 0)" == "1" ]]; }

# Warning only: an unreachable gateway must never roll back a correct address
check_gateway() {
    local gw="$1"
    [[ -n "$gw" ]] || return 0
    if ping -c2 -W2 "$gw" >/dev/null 2>&1; then ok "Gateway ${gw} reachable"; return 0; fi
    if command -v arping >/dev/null 2>&1 && arping -c2 -w3 -I "$IFACE" "$gw" >/dev/null 2>&1; then
        ok "Gateway ${gw} answers ARP (ICMP probably blocked)"; return 0
    fi
    warn "Gateway ${gw} not reachable right now (switch/router down, or ICMP blocked). Address is assigned and persists; not rolling back."
}

check_persistence() {
    if compgen -G "${NM_KEYFILE_DIR}/*.nmconnection" >/dev/null \
       && grep -ls "^id=${CON_NAME}$" "${NM_KEYFILE_DIR}"/*.nmconnection >/dev/null 2>&1; then
        ok "Profile is stored persistently in ${NM_KEYFILE_DIR}"
    else
        warn "Profile file not found in ${NM_KEYFILE_DIR}; settings may not survive a reboot. Check: ls ${NM_KEYFILE_DIR}"
    fi
    if systemctl is-enabled --quiet systemd-networkd 2>/dev/null; then
        warn "systemd-networkd is still enabled (potential conflict)"
    fi
    local extra
    extra="$(compgen -G "${NETPLAN_DIR}/*.yaml" | grep -v "^${NETPLAN_FILE}$" || true)"
    [[ -z "$extra" ]] || warn "Extra netplan files present: ${extra//$'\n'/ }"
}

# ── /etc/hosts: never pin the hostname to a LAN IP ────────────
# A LAN IP in /etc/hosts goes stale when the server is moved to another site.
fix_hosts() {
    local short plain fqdn tmp
    short="$(hostname -s 2>/dev/null || hostname)"
    plain="$(hostname 2>/dev/null || echo "$short")"
    fqdn="$(timeout 3 hostname -f 2>/dev/null || true)"
    if (( DRY_RUN )); then
        dry "/etc/hosts: drop '<LAN-IP> ${plain}' lines written by v1, keep '127.0.1.1 ${short}'"
        return 0
    fi
    tmp="$(mktemp)"
    awk -v a="$short" -v b="$plain" -v c="$fqdn" '
        NF==2 && ($2==a || $2==b || (c!="" && $2==c)) \
          && $1 ~ /^[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+$/ && $1 !~ /^127\./ {next}
        {print}' /etc/hosts > "$tmp"
    if ! cmp -s "$tmp" /etc/hosts; then
        [[ -e /etc/hosts.ahpc.bak ]] || cp -a /etc/hosts /etc/hosts.ahpc.bak
        cat "$tmp" > /etc/hosts
        _log "Removed stale LAN-IP hostname mapping from /etc/hosts (backup: /etc/hosts.ahpc.bak)"
    fi
    rm -f "$tmp"
    if ! awk -v s="$short" '$1 ~ /^127\./ { for (i=2;i<=NF;i++) if ($i==s) f=1 } END{exit !f}' /etc/hosts; then
        echo "127.0.1.1 ${short}" >> /etc/hosts
        _log "Added '127.0.1.1 ${short}' to /etc/hosts"
    fi
}

# ── main ──────────────────────────────────────────────────────
IFACE="${OPT_IFACE:-$(detect_iface)}"
[[ -n "$IFACE" ]]                || die "Could not detect a physical NIC. Use --iface NAME."
[[ -e "/sys/class/net/$IFACE" ]] || die "Interface '${IFACE}' does not exist."
is_physical "$IFACE" || warn "'${IFACE}' does not look like a physical wired NIC (VPN/virtual/wireless?). Continuing because you asked for it."

if (( OPT_DHCP )); then
    MODE_NET="dhcp"
else
    MODE_NET="static"
    IP="${OPT_IP:-$(cur_ip "$IFACE")}"
    PREFIX="${OPT_PREFIX:-$(cur_prefix "$IFACE")}"; PREFIX="${PREFIX:-24}"
    if (( OPT_NOGW )); then GATEWAY=""; else GATEWAY="${OPT_GATEWAY:-$(cur_gw "$IFACE")}"; fi
    DNS="${OPT_DNS:-$(cur_dns "$IFACE")}"
    [[ -n "$DNS" ]] || DNS="$GATEWAY"

    [[ -n "$IP" ]]               || die "Could not detect the current IP. Specify one with --ip ADDR."
    valid_ipv4 "$IP"             || die "Invalid IP address: ${IP}"
    [[ "$PREFIX" =~ ^[0-9]+$ ]] && (( PREFIX >= 1 && PREFIX <= 32 )) || die "Invalid prefix length: ${PREFIX}"
    if [[ -n "$GATEWAY" ]]; then valid_ipv4 "$GATEWAY" || die "Invalid gateway: ${GATEWAY}"; fi
    if [[ -n "$DNS" ]]; then
        IFS=, read -ra _dns_list <<<"$DNS"
        for _d in "${_dns_list[@]}"; do valid_ipv4 "$_d" || die "Invalid DNS server: ${_d}"; done
    fi
    [[ -n "$GATEWAY" ]] || warn "No default gateway configured (isolated LAN). Use --gateway ADDR if the server must reach other networks."
    [[ -n "$DNS" ]]     || warn "No DNS server configured."
fi

echo ""
_log "Interface : ${IFACE}"
if [[ "$MODE_NET" == "static" ]]; then
    _log "Static IP : ${IP}/${PREFIX}"
    _log "Gateway   : ${GATEWAY:-<none>}"
    _log "DNS       : ${DNS:-<none>}"
else
    _log "Mode      : DHCP"
fi
_log "Backend   : NetworkManager (profile: ${CON_NAME})"
(( DRY_RUN )) && _log "DRY RUN — no changes will be made"
echo ""

ensure_nm_installed
capture_prev_state
take_over_netplan
disable_cloud_init_net
write_nm_conf
arm_revert
stop_networkd_runtime
start_nm
apply_profile "$MODE_NET"
demote_other_profiles

if (( DRY_RUN )); then
    dry "nmcli -w 30 connection up ${CON_NAME}; verify address; disable systemd-networkd; fix /etc/hosts"
    fix_hosts
    echo ""
    ok "Dry run complete — nothing was changed."
    exit 0
fi

if ! nmcli -w 30 connection up "$CON_NAME" >/dev/null 2>&1; then
    warn "nmcli could not activate ${CON_NAME} right now (will be checked below)"
fi

WANT_IP=""; [[ "$MODE_NET" == "static" ]] && WANT_IP="$IP"
if net_verify "$WANT_IP"; then
    ok "NetworkManager: ${MODE_NET} configuration applied on ${IFACE}"
    [[ "$MODE_NET" == "static" ]] && check_gateway "$GATEWAY"
elif ! link_up; then
    warn "No link detected on ${IFACE} (cable unplugged / switch off). The profile is saved and will activate automatically when the link comes up."
else
    warn "Settings did not take effect — restoring previous configuration"
    if restore_prev; then
        disarm_revert
        die "Could not apply ${MODE_NET} settings on ${IFACE}; previous configuration restored."
    fi
    die "Could not apply ${MODE_NET} settings on ${IFACE}, and the previous configuration could not be restored. Check: nmcli connection show ${CON_NAME}"
fi

disable_networkd
fix_hosts
check_persistence

echo ""
ok "Configuration complete and persistent across reboots (NetworkManager only)."
if systemctl is-active --quiet "${REVERT_UNIT}.timer" 2>/dev/null; then
    warn "Auto-revert is still armed. Confirm with:  sudo bash $0 --confirm"
fi
_log "Check state any time : sudo bash $0 --status"
_log "Undo (back to DHCP)  : sudo bash $0 --dhcp --iface ${IFACE}"
echo ""
