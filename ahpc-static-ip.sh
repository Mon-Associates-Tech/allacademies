#!/usr/bin/env bash
# ahpc-static-ip.sh — Persistent static IP setup for AHPC servers
# Supports both netplan and NetworkManager; survives reboots.
#
# Usage:
#   sudo bash ahpc-static-ip.sh [OPTIONS]
#
# Options:
#   --ip      ADDR       Static IP to assign          (default: current IP)
#   --prefix  NUM        Prefix length / subnet bits  (default: 24)
#   --gateway ADDR       Default gateway              (default: current gateway)
#   --dns     A[,B]      DNS servers, comma-separated (default: gateway,1.1.1.1)
#   --iface   NAME       Network interface            (default: interface holding default route)
#   --dhcp               Revert to DHCP instead of setting static
#   --dry-run            Print what would be done, make no changes
#   --help               Show this help
#
# Examples:
#   sudo bash ahpc-static-ip.sh --ip 192.168.1.50 --gateway 192.168.1.1
#   sudo bash ahpc-static-ip.sh --ip 10.0.0.10 --prefix 24 --dns 10.0.0.1,8.8.8.8 --iface enp3s0
#   sudo bash ahpc-static-ip.sh --dhcp
# ============================================================

set -euo pipefail

# ── colours ──────────────────────────────────────────────────
RED='\033[0;31m'; YEL='\033[1;33m'; GRN='\033[0;32m'; CYN='\033[0;36m'; RST='\033[0m'
_log()  { echo -e "${CYN}[INFO]${RST}  $*"; }
ok()    { echo -e "${GRN}[ OK ]${RST}  $*"; }
warn()  { echo -e "${YEL}[WARN]${RST}  $*" >&2; }
die()   { echo -e "${RED}[FAIL]${RST}  $*" >&2; exit 1; }
dry()   { echo -e "${YEL}[DRY ]${RST}  $*"; }

# ── defaults ─────────────────────────────────────────────────
OPT_IP=""
OPT_PREFIX="24"
OPT_GATEWAY=""
OPT_DNS=""
OPT_IFACE=""
OPT_DHCP=0
DRY_RUN=0
NETPLAN_FILE="/etc/netplan/60-ahpc-static.yaml"

# ── arg parsing ───────────────────────────────────────────────
usage() {
    grep '^#' "$0" | grep -v '#!/' | sed 's/^# \{0,1\}//'
    exit 0
}

while [[ $# -gt 0 ]]; do
    case "$1" in
        --ip)       OPT_IP="$2";      shift 2 ;;
        --prefix)   OPT_PREFIX="$2";  shift 2 ;;
        --gateway)  OPT_GATEWAY="$2"; shift 2 ;;
        --dns)      OPT_DNS="$2";     shift 2 ;;
        --iface)    OPT_IFACE="$2";   shift 2 ;;
        --dhcp)     OPT_DHCP=1;       shift   ;;
        --dry-run)  DRY_RUN=1;        shift   ;;
        --help|-h)  usage ;;
        *) die "Unknown argument: $1  (run with --help)" ;;
    esac
done

[[ "$EUID" -eq 0 ]] || die "Must be run as root (sudo bash $0)"

# ── helper: detect default interface ─────────────────────────
detect_iface() {
    local dev
    dev="$(ip -4 route show default 2>/dev/null | awk '{for(i=1;i<=NF;i++) if($i=="dev"){print $(i+1); exit}}')"
    [[ -z "$dev" ]] && dev="$(ip -o link show 2>/dev/null | awk -F': ' '$2 ~ /^(en|eth)/ && /state UP/ {print $2; exit}')"
    [[ -z "$dev" ]] && dev="$(ip -o link show 2>/dev/null | awk -F': ' '$2 ~ /^(en|eth|wl)/ {print $2; exit}')"
    printf '%s' "${dev%%@*}"
}

# ── helper: current IP / gateway on an interface ─────────────
current_ip() {
    ip -4 -o addr show dev "$1" scope global 2>/dev/null | awk '{print $4; exit}' | cut -d/ -f1
}
current_prefix() {
    ip -4 -o addr show dev "$1" scope global 2>/dev/null | awk '{print $4; exit}' | cut -d/ -f2
}
current_gw() {
    ip -4 route show default dev "$1" 2>/dev/null | awk '{print $3; exit}'
}
current_dns() {
    local d=""
    if command -v resolvectl >/dev/null 2>&1; then
        d="$(resolvectl dns "$1" 2>/dev/null | grep -oE '[0-9]+(\.[0-9]+){3}' | paste -sd, - || true)"
    fi
    [[ -z "$d" ]] && d="$(grep -E '^nameserver' /etc/resolv.conf 2>/dev/null | awk '{print $2}' | grep -vE '^127\.' | paste -sd, - || true)"
    printf '%s' "$d"
}

# ── helper: check if NM manages the interface ────────────────
uses_nm() {
    command -v nmcli >/dev/null 2>&1 || return 1
    systemctl is-active --quiet NetworkManager 2>/dev/null || return 1
    local st
    st="$(nmcli -t -f DEVICE,STATE device 2>/dev/null | awk -F: -v d="$1" '$1==d {print $2}')"
    [[ -n "$st" && "$st" != "unmanaged" ]]
}

# ── helper: wait for IP to appear, then ping gateway ─────────
net_verify() {
    local iface="$1" want_ip="$2" gw="$3" i out=""
    for i in $(seq 1 10); do
        out="$(ip -4 -o addr show dev "$iface" scope global 2>/dev/null || true)"
        if [[ -n "$want_ip" ]]; then
            [[ "$out" == *" ${want_ip}/"* ]] && break
        elif [[ -n "$out" ]]; then
            break
        fi
        sleep 2
    done
    [[ -z "$out" ]] && return 1
    [[ -n "$want_ip" && "$out" != *" ${want_ip}/"* ]] && return 1
    [[ -z "$gw" ]] && return 0
    ping -c2 -W2 "$gw" >/dev/null 2>&1 || arping -c2 -w3 -I "$iface" "$gw" >/dev/null 2>&1
}

# ── apply via NetworkManager ──────────────────────────────────
apply_nm() {
    local mode="$1" iface="$2" ip="$3" prefix="$4" gw="$5" dns="$6"
    local con old_m old_a old_g old_d

    con="$(nmcli -g GENERAL.CONNECTION device show "$iface" 2>/dev/null || true)"
    [[ -n "$con" && "$con" != "--" ]] || die "NetworkManager has no active connection on ${iface}."

    old_m="$(nmcli -g ipv4.method     connection show "$con")"
    old_a="$(nmcli -g ipv4.addresses  connection show "$con")"
    old_g="$(nmcli -g ipv4.gateway    connection show "$con")"
    old_d="$(nmcli -g ipv4.dns        connection show "$con")"

    if [[ "$mode" == "static" ]]; then
        nmcli connection modify "$con" \
            ipv4.method manual \
            ipv4.addresses "${ip}/${prefix}" \
            ipv4.gateway   "$gw" \
            ipv4.dns       "$dns" \
            ipv4.ignore-auto-dns yes
    else
        nmcli connection modify "$con" \
            ipv4.method auto \
            ipv4.addresses "" \
            ipv4.gateway   "" \
            ipv4.dns       "" \
            ipv4.ignore-auto-dns no
    fi
    nmcli connection up "$con" >/dev/null 2>&1 || true

    local want="" want_gw=""
    [[ "$mode" == "static" ]] && want="$ip" && want_gw="$gw"
    if net_verify "$iface" "$want" "$want_gw"; then
        ok "NetworkManager: ${mode} applied on ${iface}"
        return 0
    fi

    warn "Settings did not take — restoring previous configuration"
    if [[ "$old_m" == "manual" ]]; then
        nmcli connection modify "$con" ipv4.method manual ipv4.addresses "$old_a" \
            ipv4.gateway "$old_g" ipv4.dns "$old_d" || true
    else
        nmcli connection modify "$con" ipv4.method auto ipv4.addresses "" \
            ipv4.gateway "" ipv4.dns "" || true
    fi
    nmcli connection up "$con" >/dev/null 2>&1 || true
    die "Could not apply ${mode} settings on ${iface}; previous configuration restored."
}

# ── apply via netplan ─────────────────────────────────────────
apply_netplan() {
    local mode="$1" iface="$2" ip="$3" prefix="$4" gw="$5" dns="$6" backup=""

    command -v netplan >/dev/null 2>&1 \
        || die "Neither NetworkManager nor netplan manages ${iface} — set the address manually."
    [[ -e "/sys/class/net/${iface}/wireless" ]] \
        && die "${iface} is a Wi-Fi interface not managed by NetworkManager."

    [[ -f "$NETPLAN_FILE" ]] && backup="$(mktemp)" && cp -p "$NETPLAN_FILE" "$backup"

    if [[ "$mode" == "static" ]]; then
        cat > "$NETPLAN_FILE" <<YAML
# Managed by ahpc-static-ip.sh — delete this file and run 'netplan apply' to undo.
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
# Managed by ahpc-static-ip.sh
network:
  version: 2
  ethernets:
    ${iface}:
      dhcp4: true
YAML
    fi
    chmod 600 "$NETPLAN_FILE"

    if ! netplan generate 2>&1; then
        [[ -n "$backup" ]] && cp -p "$backup" "$NETPLAN_FILE" || rm -f "$NETPLAN_FILE"
        die "netplan rejected the generated config (previous config restored)."
    fi
    netplan apply

    local want="" want_gw=""
    [[ "$mode" == "static" ]] && want="$ip" && want_gw="$gw"
    if net_verify "$iface" "$want" "$want_gw"; then
        ok "netplan: ${mode} applied on ${iface}"
        return 0
    fi

    warn "Settings did not take — restoring previous configuration"
    [[ -n "$backup" ]] && cp -p "$backup" "$NETPLAN_FILE" || rm -f "$NETPLAN_FILE"
    netplan apply || true
    die "Could not apply ${mode} settings on ${iface}; previous configuration restored."
}

# ── disable conflicting renderers ────────────────────────────
# When both netplan and NM are present, netplan may hand off to NM via its
# NetworkManager renderer. Ensure only one renderer owns the interface so
# settings persist across reboots.
disable_conflicting_renderer() {
    local iface="$1"
    # If NM is managing the iface, remove any stale netplan static file so
    # netplan doesn't fight NM on next boot.
    if uses_nm "$iface" && [[ -f "$NETPLAN_FILE" ]]; then
        warn "NetworkManager is managing ${iface}; removing stale netplan file ${NETPLAN_FILE}"
        rm -f "$NETPLAN_FILE"
        netplan apply >/dev/null 2>&1 || true
    fi
}

# ── main ──────────────────────────────────────────────────────
IFACE="${OPT_IFACE:-$(detect_iface)}"
[[ -n "$IFACE" ]] || die "Could not detect a network interface. Use --iface NAME."
[[ -e "/sys/class/net/$IFACE" ]] || die "Interface '${IFACE}' does not exist."

if (( OPT_DHCP )); then
    _log "Reverting ${IFACE} to DHCP"
    if (( DRY_RUN )); then
        dry "Remove ${NETPLAN_FILE} (if exists) and set ${IFACE} to dhcp4: true  OR  nmcli ipv4.method auto"
        exit 0
    fi
    if uses_nm "$IFACE"; then
        apply_nm "dhcp" "$IFACE" "" "" "" ""
    else
        apply_netplan "dhcp" "$IFACE" "" "" "" ""
    fi
    ok "Done — ${IFACE} will obtain an address via DHCP after reboot too."
    exit 0
fi

# ── resolve defaults from current state ──────────────────────
CUR_IP="$(current_ip "$IFACE")"
CUR_PREFIX="$(current_prefix "$IFACE")"
CUR_GW="$(current_gw "$IFACE")"
CUR_DNS="$(current_dns "$IFACE")"

IP="${OPT_IP:-$CUR_IP}"
PREFIX="${OPT_PREFIX:-${CUR_PREFIX:-24}}"
GATEWAY="${OPT_GATEWAY:-$CUR_GW}"
DNS="${OPT_DNS:-}"

[[ -n "$IP"      ]] || die "Could not detect current IP. Specify one with --ip ADDR."
[[ -n "$GATEWAY" ]] || die "Could not detect current gateway. Specify one with --gateway ADDR."

# Build DNS default: gateway first, then 1.1.1.1 as fallback
if [[ -z "$DNS" ]]; then
    if [[ -n "$CUR_DNS" ]]; then
        DNS="$CUR_DNS"
    else
        DNS="${GATEWAY},1.1.1.1"
    fi
fi

# ── summary ───────────────────────────────────────────────────
echo ""
_log "Network interface : ${IFACE}"
_log "Static IP         : ${IP}/${PREFIX}"
_log "Gateway           : ${GATEWAY}"
_log "DNS               : ${DNS}"
if uses_nm "$IFACE"; then
    _log "Backend           : NetworkManager (nmcli)"
else
    _log "Backend           : netplan  →  ${NETPLAN_FILE}"
fi
echo ""

if (( DRY_RUN )); then
    dry "Would apply static IP ${IP}/${PREFIX} gw ${GATEWAY} dns ${DNS} on ${IFACE}"
    dry "Would write /etc/hosts entry: ${IP} $(hostname -f 2>/dev/null || hostname)"
    exit 0
fi

# ── apply ─────────────────────────────────────────────────────
disable_conflicting_renderer "$IFACE"

if uses_nm "$IFACE"; then
    apply_nm "static" "$IFACE" "$IP" "$PREFIX" "$GATEWAY" "$DNS"
else
    apply_netplan "static" "$IFACE" "$IP" "$PREFIX" "$GATEWAY" "$DNS"
fi

# ── update /etc/hosts with the new IP ────────────────────────
HOSTNAME="$(hostname -f 2>/dev/null || hostname)"
SHORT="$(hostname -s 2>/dev/null || hostname)"
for name in "$HOSTNAME" "$SHORT"; do
    if grep -qE "[[:space:]]${name}$" /etc/hosts; then
        sed -i "s|.*[[:space:]]${name}$|${IP} ${name}|" /etc/hosts
    else
        echo "${IP} ${name}" >> /etc/hosts
    fi
done
ok "/etc/hosts updated: ${IP} ${HOSTNAME}"

echo ""
ok "Static IP configuration complete."
_log "The address ${IP}/${PREFIX} will persist across reboots."
_log "To undo, run:  sudo bash $0 --dhcp --iface ${IFACE}"
echo ""
