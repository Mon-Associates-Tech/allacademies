#!/usr/bin/env bash
# ==============================================================================
#  AHPC — Section 11 patch: dnsmasq + .internal DNS  (run as root)
#  Fixes: unit-not-found caused by wrong setup order in deploy-ahpc.sh
#  Run:   sudo bash fix-dnsmasq.sh
# ==============================================================================

set -uo pipefail          # Note: no -e here — we handle errors explicitly
IFS=$'\n\t'

GREEN='\033[0;32m'; YELLOW='\033[1;33m'; BLUE='\033[0;34m'
CYAN='\033[0;36m'; BOLD='\033[1m'; RED='\033[0;31m'; NC='\033[0m'

info()    { echo -e "${BLUE}[INFO]${NC}   $*"; }
ok()      { echo -e "${GREEN}[OK]${NC}     $*"; }
warn()    { echo -e "${YELLOW}[WARN]${NC}   $*"; }
die()     { echo -e "${RED}[FATAL]${NC}  $*" >&2; exit 1; }
section() { echo -e "\n${BOLD}${CYAN}┌─ $* ${NC}"; }

[[ $EUID -ne 0 ]] && die "Run as root: sudo bash $0"

APP_HOSTNAME="ahpc"

# Detect LAN IP
SERVER_IP=$(ip route get 1.1.1.1 2>/dev/null \
    | awk '{for(i=1;i<=NF;i++) if($i=="src") print $(i+1)}' | head -1)
[[ -z "$SERVER_IP" ]] && SERVER_IP=$(hostname -I | awk '{print $1}')
info "Server LAN IP: $SERVER_IP"

# ==============================================================================
#  STEP A — Free port 53 FIRST (before dnsmasq is touched)
# ==============================================================================
section "Freeing port 53"

# ── A1. systemd-resolved stub listener ────────────────────────────────────────
RESOLVED_CONF="/etc/systemd/resolved.conf"

if systemctl list-unit-files systemd-resolved.service &>/dev/null \
   && systemctl is-enabled --quiet systemd-resolved 2>/dev/null; then

    info "Disabling systemd-resolved stub listener..."

    # Patch resolved.conf
    if grep -q 'DNSStubListener' "$RESOLVED_CONF" 2>/dev/null; then
        sed -i 's/^#*DNSStubListener=.*/DNSStubListener=no/' "$RESOLVED_CONF"
    else
        # Section header may be missing on minimal installs
        if grep -q '^\[Resolve\]' "$RESOLVED_CONF" 2>/dev/null; then
            sed -i '/^\[Resolve\]/a DNSStubListener=no' "$RESOLVED_CONF"
        else
            printf '\n[Resolve]\nDNSStubListener=no\n' >> "$RESOLVED_CONF"
        fi
    fi

    systemctl daemon-reload
    systemctl restart systemd-resolved
    sleep 2
    ok "systemd-resolved stub listener disabled"

    # Point resolv.conf at the real (non-stub) resolved socket
    # so the system retains working upstream DNS after we take over :53
    ln -sf /run/systemd/resolve/resolv.conf /etc/resolv.conf
    ok "resolv.conf → /run/systemd/resolve/resolv.conf"

else
    info "systemd-resolved not active — skipping stub-listener patch"
fi

# ── A2. Kill anything else squatting on port 53 ───────────────────────────────
if command -v ss &>/dev/null; then
    PORT53_PIDS=$(ss -tlunp sport = :53 2>/dev/null \
        | awk '/LISTEN|UNCONN/{match($0,/pid=([0-9]+)/,a); if(a[1]) print a[1]}' \
        | sort -u)
    if [[ -n "$PORT53_PIDS" ]]; then
        warn "Processes still holding port 53: $PORT53_PIDS — killing..."
        for pid in $PORT53_PIDS; do
            kill "$pid" 2>/dev/null && info "Killed PID $pid" || true
        done
        sleep 1
    fi
fi

# Confirm port is free before proceeding
if ss -tlunp sport = :53 2>/dev/null | grep -q LISTEN; then
    warn "Port 53 (TCP) still in use — dnsmasq may fail to start. Check: ss -tlunp | grep 53"
else
    ok "Port 53 is free"
fi

# ==============================================================================
#  STEP B — Install dnsmasq NOW (port is free, so it can start cleanly)
# ==============================================================================
section "Installing dnsmasq"

if ! command -v dnsmasq &>/dev/null; then
    apt-get update -qq
    DEBIAN_FRONTEND=noninteractive apt-get install -y dnsmasq
    ok "dnsmasq installed"
else
    ok "dnsmasq already installed"
fi

# Stop it before writing config so we control the start
systemctl stop dnsmasq 2>/dev/null || true

# ==============================================================================
#  STEP C — Write dnsmasq config
# ==============================================================================
section "Writing dnsmasq config"

mkdir -p /etc/dnsmasq.d

cat > /etc/dnsmasq.d/ahpc.conf << DNSEOF
# ──────────────────────────────────────────────────
#  AHPC Internal DNS — /etc/dnsmasq.d/ahpc.conf
# ──────────────────────────────────────────────────

# Upstream DNS (used for everything else)
no-resolv
server=1.1.1.1
server=8.8.8.8
server=8.8.4.4

# Resolve ahpc.* to this server on the LAN
address=/${APP_HOSTNAME}.internal/${SERVER_IP}
address=/${APP_HOSTNAME}.local/${SERVER_IP}
address=/${APP_HOSTNAME}/${SERVER_IP}

# Listen on loopback and LAN interface only
listen-address=127.0.0.1,${SERVER_IP}
bind-dynamic

# Safety
domain-needed
bogus-priv

# Cache — 1 hour TTL
cache-size=1000
local-ttl=3600
DNSEOF

ok "Config written to /etc/dnsmasq.d/ahpc.conf"

# Ensure the base dnsmasq.conf loads drop-in directory
DNSMASQ_CONF="/etc/dnsmasq.conf"
if ! grep -q 'conf-dir=/etc/dnsmasq.d' "$DNSMASQ_CONF" 2>/dev/null; then
    echo 'conf-dir=/etc/dnsmasq.d/,*.conf' >> "$DNSMASQ_CONF"
    info "conf-dir added to $DNSMASQ_CONF"
fi

# ==============================================================================
#  STEP D — Enable and start dnsmasq
# ==============================================================================
section "Starting dnsmasq"

systemctl enable dnsmasq

if systemctl start dnsmasq; then
    sleep 1
    if systemctl is-active --quiet dnsmasq; then
        ok "dnsmasq is running"
    else
        warn "dnsmasq started but is not active — check: journalctl -u dnsmasq -n 30"
    fi
else
    echo ""
    warn "dnsmasq failed to start. Printing last 20 log lines:"
    journalctl -u dnsmasq -n 20 --no-pager || true
    echo ""
    warn "Check for port conflicts: ss -tlunp | grep ':53'"
    warn "Check config syntax:      dnsmasq --test"
    die "dnsmasq could not start. Fix the above, then re-run this script."
fi

# ==============================================================================
#  STEP E — Smoke test
# ==============================================================================
section "DNS smoke test"

sleep 1
if command -v dig &>/dev/null; then
    RESOLVED=$(dig +short +time=2 "${APP_HOSTNAME}.internal" @127.0.0.1 2>/dev/null | head -1)
    if [[ "$RESOLVED" == "$SERVER_IP" ]]; then
        ok "dig ${APP_HOSTNAME}.internal @127.0.0.1 → $RESOLVED ✓"
    else
        warn "dig returned '$RESOLVED' (expected $SERVER_IP) — dnsmasq may still be initialising"
    fi
elif command -v nslookup &>/dev/null; then
    nslookup "${APP_HOSTNAME}.internal" 127.0.0.1 &>/dev/null \
        && ok "nslookup ${APP_HOSTNAME}.internal resolved successfully" \
        || warn "nslookup could not resolve ${APP_HOSTNAME}.internal yet"
else
    info "Install dig (apt install dnsutils) to test DNS resolution"
fi

# ==============================================================================
#  SUMMARY
# ==============================================================================
echo ""
echo -e "${BOLD}${GREEN}╔══════════════════════════════════════════════════════════╗"
echo -e "║        dnsmasq setup complete ✓                         ║"
echo -e "╚══════════════════════════════════════════════════════════╝${NC}"
echo ""
echo -e "  ${BOLD}${CYAN}How other LAN devices use ahpc.internal${NC}"
echo -e "  ─────────────────────────────────────────────────────────"
echo -e "  Set their DNS server to: ${BOLD}${SERVER_IP}${NC}"
echo ""
echo -e "  Router (recommended — covers all devices at once):"
echo -e "    In your router admin page → DHCP settings"
echo -e "    Set 'Primary DNS' to ${BOLD}${SERVER_IP}${NC}"
echo ""
echo -e "  Per device (manual):"
echo -e "    Windows: Network adapter → IPv4 → DNS server → ${SERVER_IP}"
echo -e "    macOS:   System Settings → Wi-Fi → Details → DNS → ${SERVER_IP}"
echo -e "    Linux:   nmcli con mod <name> ipv4.dns ${SERVER_IP}"
echo ""
echo -e "  Or skip all of the above — use ${BOLD}http://ahpc.local${NC} instead"
echo -e "  (mDNS via avahi works on most devices with zero config)"
echo ""
echo -e "  ${BOLD}Useful commands:${NC}"
echo -e "    systemctl status dnsmasq"
echo -e "    journalctl -u dnsmasq -f"
echo -e "    dig ahpc.internal @${SERVER_IP}"
echo -e "    dnsmasq --test"
echo ""
