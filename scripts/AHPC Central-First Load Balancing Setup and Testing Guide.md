# AHPC Central-First Load Balancing — Setup & Testing Guide

**Version:** 1.0  
**Date:** 12 November 2026  
**Status:** Implementation complete. This guide covers testing and production deployment.

---

## Overview

This guide walks you through everything needed to **test** and **deploy** the Central-First load balancing architecture. The system allows examination centers to route candidate traffic through HAProxy, with the local center server as primary (Stage A: local-first) or Central as primary (Stage B: central-first).

**Key constraint:** Candidate machines do NOT have internet access during exams. They always connect to their center's LAN address. HAProxy on the center server decides whether to serve locally or forward to Central.

---

## Table of Contents

1. [Prerequisites](#1-prerequisites)
2. [Architecture Refresher](#2-architecture-refresher)
3. [Phase 1: Prepare Central Server](#3-phase-1-prepare-central-server)
4. [Phase 2: Prepare Center Server (Pilot)](#4-phase-2-prepare-center-server-pilot)
5. [Phase 3: Application Deployment](#5-phase-3-application-deployment)
6. [Phase 4: Component Testing](#6-phase-4-component-testing)
7. [Phase 5: Integration Testing](#7-phase-5-integration-testing)
8. [Phase 6: Load Testing](#8-phase-6-load-testing)
9. [Phase 7: Failure Drill Testing](#9-phase-7-failure-drill-testing)
10. [Phase 8: Go Live — Stage A (Local-First)](#10-phase-8-go-live--stage-a-local-first)
11. [Phase 9: Go Live — Stage B (Central-First)](#11-phase-9-go-live--stage-b-central-first)
12. [Phase 10: Rollout to All Centers](#12-phase-10-rollout-to-all-centers)
13. [Monitoring and Operations](#13-monitoring-and-operations)
14. [Troubleshooting](#14-troubleshooting)
15. [Rollback Procedures](#15-rollback-procedures)

---

## 1. Prerequisites

### 1.1 Server Requirements

| Server | Role | Specs |
|--------|------|-------|
| Central | Primary exam server | 64 GB RAM, 1 TB storage, serves ~2000 concurrent candidates |
| Each Center | Edge proxy + fallback | 32 GB RAM, 800 GB storage, ~400 concurrent candidates |

### 1.2 Network Requirements

- **Center ↔ Central connectivity:** Each center server must have outbound internet/WAN access to reach the Central server on port 443.
- **Candidate ↔ Center connectivity:** Candidates connect only to their center's LAN address (ports 80/443).
- **Measure bandwidth and latency** between each center and Central before proceeding:

```bash
# From each center server, run:
iperf3 -c <CENTRAL_IP>
mtr <CENTRAL_IP>
```

Record these numbers. You'll need them to tune thresholds.

### 1.3 Software Requirements

- Ubuntu 22.04 LTS (or compatible Debian-based distro)
- PHP 8.2+ with required extensions
- MariaDB 10.11+
- Redis 7+
- Nginx
- HAProxy (installed by the setup script)
- Let's Encrypt certificates (or self-signed for testing)

### 1.4 Code Requirements

All servers must run the **same branch at the same commit** of the application. Deploy from the `ahpc` branch and ensure all servers pull the same commit — Vite asset hashes must match exactly between Central and centers.

```bash
# On ALL servers, deploy the same branch:
git fetch origin ahpc
git checkout ahpc
git pull origin ahpc
composer install --no-dev --optimize-autoloader
npm ci && npm run build
php artisan migrate --force
php artisan storage:link
```

---

## 2. Architecture Refresher

```
Candidate (LAN) → Center HAProxy :80/:443 (TLS termination, routing, sticky cookie)
                         |
              +----------+----------+
              v                   v
       Local Nginx           Central (WAN, HTTPS)
       127.0.0.1:8081       Nginx + PHP-FPM
       PHP-FPM, MariaDB     MariaDB, Redis
       Redis
              |                   ^
              +-- sync, 15 min --+
```

### Two operating modes:

| Mode | Primary backend | Fallback | Use case |
|------|----------------|----------|----------|
| **Stage A: local-first** | Local server | Central | Each center serves its own ~400 candidates; overflow to Central |
| **Stage B: central-first** | Central server | Local | Central serves all ~2000 candidates; center is fallback |

### Sticky sessions:

- First request sets cookie `ahpc_route` = `local` or `central`
- Cookie lasts 4 hours (longer than any exam)
- A candidate is never moved mid-exam
- Cookie is HTTPS-only, HttpOnly, SameSite=Lax

---

## 3. Phase 1: Prepare Central Server

### 3.1 Backup Central

Before any changes, take a full backup:

```bash
# Database backup
mysqldump -u root -p --all-databases > /backup/central-db-$(date +%F).sql

# Storage backup
tar czf /backup/central-storage-$(date +%F).tar.gz /var/www/html/ahpc/storage/

# Config backup
cp /var/www/html/ahpc/.env /backup/central-env-$(date +%F).env
```

### 3.2 Deploy the latest code

```bash
cd /var/www/html/ahpc
git fetch origin ahpc
git checkout ahpc
git pull origin ahpc
composer install --no-dev --optimize-autoloader
npm ci && npm run build
php artisan migrate --force
php artisan config:cache
php artisan route:cache
```

### 3.3 Configure TrustProxies on Central

Central must trust the IP addresses of all center servers so that `X-Forwarded-For` headers are respected and real candidate IPs reach Laravel.

Edit Central's `.env`:

```env
# Add the public IPs of all center servers, comma-separated
TRUSTED_PROXY_IPS=192.168.1.10,192.168.2.10,192.168.3.10
```

Or set to `'header'` to trust all proxied requests (less secure, easier for testing):

```env
# In app/Http/Middleware/TrustProxies.php, $proxies is already set to 'header'
# This trusts any request with X-Forwarded-For headers
```

Clear the config cache:

```bash
php artisan config:clear
php artisan route:clear
```

### 3.4 Verify health endpoints

The Central server now exposes two public health check endpoints:

```bash
# Test /up — should return 200 with {"status":"ok"}
curl -s http://127.0.0.1/up | jq

# Test /lb-health — should return 200 with worker utilization data
curl -s http://127.0.0.1/lb-health | jq
```

Expected response from `/lb-health`:

```json
{
  "status": "ok",
  "db_connections": 5,
  "db_threshold": 50,
  "note": "PHP-FPM status unavailable, using DB connections as proxy"
}
```

If PHP-FPM status is configured (see below), you'll see worker-level data instead.

### 3.5 (Optional) Enable PHP-FPM status page

For more accurate load detection, expose the PHP-FPM status page on Central:

Add to Central's Nginx config:

```nginx
location /fpm-status {
    fastcgi_pass unix:/run/php/php8.2-fpm.sock;  # Adjust for your PHP version
    fastcgi_param SCRIPT_FILENAME $fastcgi_script_name;
    include fastcgi_params;
}
```

Then in `.env`:

```env
FPM_STATUS_PATH=/fpm-status
```

Reload Nginx: `sudo systemctl reload nginx`

### 3.6 Tune Central for 2000 concurrent candidates

Re-run the setup script to size Central properly:

```bash
sudo bash ahpc-multiserver-setup.sh \
    --app-role central \
    --students 2000 \
    --only php,mariadb,redis,nginx
```

This configures approximately:
- **400 PHP-FPM workers** (adjust based on your actual per-worker memory)
- **16 GB InnoDB buffer pool**
- **2 GB Redis maxmemory**
- **550 max DB connections**

### 3.7 Verify Central is ready

```bash
# Check all services
systemctl status php8.2-fpm
systemctl status mariadb
systemctl status redis
systemctl status nginx

# Check health endpoints
curl -s http://127.0.0.1/up
curl -s http://127.0.0.1/lb-health

# Check database connectivity
php artisan tinker --execute="DB::connection()->getPdo(); echo 'DB OK\n';"
```

---

## 4. Phase 2: Prepare Center Server (Pilot)

Start with **one pilot center** for testing. Once verified, roll out to other centers.

### 4.1 Backup the center

```bash
mysqldump -u root -p --all-databases > /backup/center-db-$(date +%F).sql
cp /var/www/html/ahpc/.env /backup/center-env-$(date +%F).env
```

### 4.2 Deploy the same branch as Central

```bash
cd /var/www/html/ahpc
git fetch origin ahpc
git checkout ahpc
git pull origin ahpc
composer install --no-dev --optimize-autoloader
npm ci && npm run build
php artisan migrate --force
```

### 4.3 Run the setup script with HAProxy

For **Stage A (local-first)** testing:

```bash
sudo bash ahpc-multiserver-setup.sh \
    --app-role center \
    --center-id "your-center-uuid" \
    --central-url "https://central.example.com" \
    --lb-mode local-first \
    --lb-local-max 150 \
    --lb-central-max 350 \
    --lb-cookie-hours 4
```

For **Stage B (central-first)** testing:

```bash
sudo bash ahpc-multiserver-setup.sh \
    --app-role center \
    --center-id "your-center-uuid" \
    --central-url "https://central.example.com" \
    --lb-mode central-first \
    --lb-local-max 150 \
    --lb-central-max 350 \
    --lb-cookie-hours 4
```

### 4.4 What the script does

When `--lb-mode` is set, the script:

1. **Installs HAProxy** if not already present
2. **Generates TLS certificate** at `/etc/ahpc/tls/haproxy.pem` (uses Let's Encrypt cert if available, otherwise self-signed)
3. **Moves Nginx** from ports 80/443 to `127.0.0.1:8081` (HTTP only)
4. **Writes HAProxy config** at `/etc/haproxy/haproxy.cfg` with:
   - Frontend on ports 80/443 with TLS termination
   - Sticky session cookie `ahpc_route`
   - Health checks: `/up` for local, `/lb-health` for Central
   - Connection limits based on your `--lb-*` flags
   - Routing rules for your chosen mode
5. **Creates custom 503 page** for when both backends are down
6. **Starts HAProxy** on ports 80/443
7. **Updates `.env`** with `LB_MODE` and `TRUSTED_PROXY_IPS`

### 4.5 Verify the setup

```bash
# Check HAProxy is running
systemctl status haproxy

# Check HAProxy config is valid
sudo haproxy -c -f /etc/haproxy/haproxy.cfg

# Check Nginx is on the new port
curl -s http://127.0.0.1:8081/up | jq

# Check HAProxy is serving on port 80/443
curl -s http://127.0.0.1/up | jq
curl -sk https://127.0.0.1/up | jq

# Check HAProxy stats page
curl -s http://127.0.0.1:9000/haproxy?stats | head -20
```

### 4.6 Dry run option

Before making changes, you can preview what the script will do:

```bash
sudo bash ahpc-multiserver-setup.sh \
    --app-role center \
    --center-id "your-center-uuid" \
    --central-url "https://central.example.com" \
    --lb-mode local-first \
    --dry-run
```

---

## 5. Phase 3: Application Deployment

### 5.1 Run migrations

The `server_origin` column must exist on `general_exam_submissions` on **both** Central and all centers:

```bash
# On ALL servers
php artisan migrate --force

# Verify the column exists
php artisan tinker --execute="echo Schema::hasColumn('general_exam_submissions', 'server_origin') ? 'YES' : 'NO';"
```

Expected output: `YES`

### 5.2 Configure environment variables

On each **center** server, ensure `.env` has:

```env
APP_ROLE=center
CENTER_ID=your-center-uuid
CENTRAL_URL=https://central.example.com
CENTER_SYNC_TOKEN=your-sync-token
LB_MODE=local-first  # or central-first
TRUSTED_PROXY_IPS=127.0.0.1
```

On the **Central** server:

```env
APP_ROLE=central
TRUSTED_PROXY_IPS=192.168.1.10,192.168.2.10,192.168.3.10
```

### 5.3 Clear caches

```bash
# On ALL servers after any config change
php artisan config:clear
php artisan route:clear
php artisan cache:clear
```

---

## 6. Phase 4: Component Testing

Test each component individually before integration testing.

### 6.1 Test health endpoints

**On Central:**

```bash
# /up — basic liveness
curl -s http://127.0.0.1/up | jq
# Expected: {"status":"ok"}

# /lb-health — load check
curl -s http://127.0.0.1/lb-health | jq
# Expected: {"status":"ok", "db_connections":N, ...}
```

**On Center (via HAProxy):**

```bash
# /up through HAProxy
curl -s http://127.0.0.1/up | jq

# /lb-health through HAProxy (routes to local backend)
curl -s http://127.0.0.1/lb-health | jq
```

### 6.2 Test TrustProxies

From the center server, make a request to Central with forwarded headers:

```bash
# Simulate HAProxy forwarding
curl -s -H "X-Forwarded-For: 10.0.0.50" \
     -H "X-Forwarded-Server: central" \
     https://central.example.com/up | jq
```

Verify that Central's logs show the real client IP (`10.0.0.50`), not the center's IP.

### 6.3 Test HAProxy sticky cookie

```bash
# First request — should set the cookie
curl -v -c /tmp/cookies.txt http://127.0.0.1/up 2>&1 | grep -i 'set-cookie'
# Expected: Set-Cookie: ahpc_route=local; Path=/; Max-Age=14400; ...

# Second request — should include the cookie
curl -v -b /tmp/cookies.txt http://127.0.0.1/up 2>&1 | grep -i 'cookie:'
# Expected: Cookie: ahpc_route=local
```

### 6.4 Test HAProxy stats socket

```bash
# Query backend status
echo 'show stat' | socat stdio /run/haproxy/admin.sock | head -5

# Or use the Artisan command
php artisan exam:lb:status
```

### 6.5 Test HTTP to HTTPS redirect

```bash
# HTTP request should redirect to HTTPS
curl -s -o /dev/null -w "%{http_code} %{redirect_url}" http://127.0.0.1/
# Expected: 301 https://127.0.0.1/
```

### 6.6 Test attempt ownership

Create a test submission and verify `server_origin` is set:

```bash
# Login as a test user and join an exam
# Then check the submission record
php artisan tinker --execute="
\$sub = \App\ExaminationHub\Models\GeneralExamSubmission::latest()->first();
echo 'server_origin: ' . (\$sub->server_origin ?? 'NULL') . PHP_EOL;
"
```

Expected: `server_origin: local` (when served by the local backend)

---

## 7. Phase 5: Integration Testing

### 7.1 Test candidate flow through HAProxy

1. Open a browser on a LAN machine (or use the center server directly)
2. Navigate to `http://<center-ip>/`
3. Login as a test candidate
4. Join an exam
5. Complete a section and save responses
6. Submit the exam

Verify:
- The `ahpc_route` cookie is set in your browser
- The exam loads and functions normally
- The submission has `server_origin` set correctly
- Results appear in the admin dashboard

### 7.2 Test cross-server routing

**Stage A (local-first):**

1. Verify candidates are served by the local backend by default
2. Check HAProxy stats: `php artisan exam:lb:status`
3. Confirm `local` backend shows active sessions

**Stage B (central-first):**

1. Verify new candidates are routed to Central by default
2. Check Central's access logs for requests from the center
3. Confirm `central` backend shows active sessions in HAProxy stats

### 7.3 Test submission sync

1. Create an exam attempt on Central (via center routing in Stage B)
2. Wait for the next sync cycle (15 minutes) or trigger manually:

```bash
# On the center server
php artisan sync:pull
```

3. Verify the Central-originated submission appears in the center's dashboard

### 7.4 Test static file serving

In Stage B (central-first), static files (`/build/`, `/storage/`) should always be served locally:

```bash
# Check that a build asset is served by the local backend
curl -s -I http://127.0.0.1/build/app.js | head -5
```

Check HAProxy logs to confirm the request was routed to the `local` backend, not Central.

---

## 8. Phase 6: Load Testing

### 8.1 Install k6 (load testing tool)

```bash
sudo apt-get install -y k6
```

### 8.2 Create a load test script

Create `load-test.js`:

```javascript
import http from 'k6/http';
import { check, sleep } from 'k6';

export const options = {
    vus: 100,  // Start with 100 virtual users
    duration: '5m',
    stages: [
        { duration: '1m', target: 100 },   // Ramp up
        { duration: '3m', target: 100 },   // Stay steady
        { duration: '1m', target: 0 },     // Ramp down
    ],
};

const BASE_URL = '__ENV_CENTRAL_URL__';  // Replace with your center IP

export default function () {
    // Test health endpoint
    const res = http.get(`${BASE_URL}/up`);
    check(res, {
        'health check is 200': (r) => r.status === 200,
    });
    sleep(1);
}
```

### 8.3 Run the load test

```bash
# From a machine that can reach the center server
k6 run load-test.js
```

### 8.4 Pass criteria

| Metric | Threshold |
|--------|-----------|
| Error rate | < 0.5% |
| Health check p95 latency | < 500ms |
| PHP-FPM workers | < 85% busy |
| Database connections | < 70% of max |
| No out-of-memory events | — |

### 8.5 Scale up gradually

Start with 100 VUs, then increase: 200 → 400 → 800 → 2000. At each level:

1. Run for at least 5 minutes
2. Check all metrics
3. If any threshold is exceeded, tune before proceeding
4. Record results for capacity planning

---

## 9. Phase 7: Failure Drill Testing

### 9.1 Central server down

**Setup:** Stage B (central-first) mode on the center

**Test:**
```bash
# On Central, simulate being down
sudo systemctl stop nginx
```

**Expected behavior:**
- HAProxy detects Central is down via `/lb-health` health check
- New candidates are automatically routed to the local backend
- Existing candidates on Central lose connection (documented edge case)

**Verify:**
```bash
echo 'show stat' | socat stdio /run/haproxy/admin.sock | grep central
# Should show: DOWN or MAINT

# New requests should go to local
curl -s http://127.0.0.1/up | jq
```

**Restore:**
```bash
# On Central
sudo systemctl start nginx
```

### 9.2 Central server overloaded

**Setup:** Stage B mode

**Test:** Simulate `/lb-health` returning 503 on Central

```bash
# Temporarily set a very low threshold on Central
php artisan tinker --execute="Config::set('center.lb_health_threshold', 1);"
```

Or modify `.env` and clear cache:

```env
LB_HEALTH_THRESHOLD=1
```

```bash
php artisan config:clear
```

**Expected behavior:**
- HAProxy detects Central is overloaded via `/lb-health` returning 503
- New candidates fall back to local
- When Central recovers, HAProxy resumes routing to Central

**Verify:**
```bash
curl -s http://127.0.0.1/lb-health | jq
# Should show: {"status":"overloaded", ...}
```

**Restore:**
```env
LB_HEALTH_THRESHOLD=85
```

```bash
php artisan config:clear
```

### 9.3 Local server down (Stage A)

**Setup:** Stage A (local-first) mode

**Test:**
```bash
# On the center, stop PHP-FPM
sudo systemctl stop php8.2-fpm
```

**Expected behavior:**
- HAProxy detects local backend is down via `/up` health check
- New candidates are routed to Central
- When local recovers, new candidates return to local

**Verify:**
```bash
echo 'show stat' | socat stdio /run/haproxy/admin.sock | grep local
# Should show: DOWN or MAINT
```

**Restore:**
```bash
sudo systemctl start php8.2-fpm
```

### 9.4 WAN cut mid-exam

**Setup:** Stage B mode, candidate actively taking exam on Central

**Test:**
```bash
# On the center, block traffic to Central
sudo iptables -A OUTPUT -d <CENTRAL_IP> -j DROP
```

**Expected behavior:**
- Local candidates are unaffected
- Candidates routed to Central will experience connection failures
- This is a documented edge case; the exam state on Central is preserved
- When WAN is restored, the candidate can reconnect to Central

**Restore:**
```bash
sudo iptables -D OUTPUT -d <CENTRAL_IP> -j DROP
```

### 9.5 Rollback test

**Test:** Switch from Stage B to Stage A

```bash
# Option 1: Artisan command
php artisan exam:lb:rollback --mode=local-first

# Option 2: Re-run setup script
sudo bash ahpc-multiserver-setup.sh --only lb --lb-mode local-first
```

**Expected behavior:**
- Mode changes within 5 minutes
- New candidates follow the new routing rules
- Existing candidates stay on their current server

---

## 10. Phase 8: Go Live — Stage A (Local-First)

### 10.1 Pre-launch checklist

- [ ] Central server health endpoints respond correctly
- [ ] TrustProxies configured on Central with center IPs
- [ ] Same tagged release deployed on all servers
- [ ] Migrations run on all servers (`server_origin` column exists)
- [ ] HAProxy installed and running on pilot center
- [ ] Health endpoints tested on pilot center
- [ ] Sticky cookie tested
- [ ] Component tests pass
- [ ] Integration tests pass
- [ ] Load test at 400 VUs passes
- [ ] Failure drills pass
- [ ] Rollback procedure tested
- [ ] Backup of pilot center taken

### 10.2 Enable on pilot center

```bash
sudo bash ahpc-multiserver-setup.sh \
    --app-role center \
    --center-id "your-center-uuid" \
    --central-url "https://central.example.com" \
    --lb-mode local-first \
    --lb-local-max 150 \
    --lb-central-max 350 \
    --lb-cookie-hours 4
```

### 10.3 Monitor during first exam

```bash
# Check HAProxy status
php artisan exam:lb:status

# Watch HAProxy logs
sudo tail -f /var/log/haproxy.log

# Watch application logs
tail -f /var/www/html/ahpc/storage/logs/laravel.log

# Check Central logs for overflow requests
sudo tail -f /var/log/nginx/central.example.com-access.log
```

### 10.4 Verify after exam

1. Check all submissions have `server_origin` set
2. Verify no answers were lost
3. Confirm results appear in the correct dashboards
4. Check HAProxy stats for any errors or timeouts

---

## 11. Phase 9: Go Live — Stage B (Central-First)

### 11.1 Gate criteria

Before switching to Stage B, verify:

- [ ] Stage A has run successfully for at least one full exam session
- [ ] Central passes load test at 2000 VUs:
  - Error rate < 0.5%
  - Exam page p95 < 1.5s
  - Autosave p95 < 500ms
  - PHP workers < 85% busy
  - Database load < 70%
- [ ] WAN bandwidth at each center is < 70% of link capacity
- [ ] All failure drills pass
- [ ] Sync reconciles attempts correctly

### 11.2 Switch to Stage B

```bash
sudo bash ahpc-multiserver-setup.sh \
    --only lb \
    --lb-mode central-first
```

Or use the Artisan command:

```bash
php artisan exam:lb:rollback --mode=central-first
```

Then regenerate HAProxy config:

```bash
sudo bash ahpc-multiserver-setup.sh --only lb --lb-mode central-first
```

### 11.3 Verify Stage B

```bash
# Check mode
php artisan exam:lb:status

# Verify new candidates go to Central
curl -s -b /tmp/cookies.txt -v http://127.0.0.1/up 2>&1 | grep -i 'set-cookie'
# Should set: ahpc_route=central

# Verify static files stay local
curl -s -I http://127.0.0.1/build/app.js | head -5
```

---

## 12. Phase 10: Rollout to All Centers

### 12.1 Rollout strategy

Move one center per exam session. Keep Stage A config as a one-command rollback.

### 12.2 For each center

1. **Backup:** Take database and config backup
2. **Deploy:** Same tagged release as Central and pilot
3. **Configure:** Run setup script with appropriate `--lb-mode`
4. **Test:** Run component tests (Section 6)
5. **Monitor:** Watch first exam session closely
6. **Verify:** Check submissions and results after exam

### 12.3 Rollout command template

```bash
sudo bash ahpc-multiserver-setup.sh \
    --app-role center \
    --center-id "<CENTER_UUID>" \
    --central-url "https://central.example.com" \
    --lb-mode <local-first|central-first> \
    --lb-local-max 150 \
    --lb-central-max 350 \
    --lb-cookie-hours 4
```

---

## 13. Monitoring and Operations

### 13.1 HAProxy stats page

Access the live stats dashboard at:

```
http://<center-ip>:9000/haproxy?stats
```

This shows real-time backend status, connection counts, and health check results.

### 13.2 Artisan commands

```bash
# Check load balancer status
php artisan exam:lb:status

# Rollback to a different mode
php artisan exam:lb:rollback --mode=off
php artisan exam:lb:rollback --mode=local-first
php artisan exam:lb:rollback --mode=central-first
```

### 13.3 Livewire dashboard component

The `LoadBalancerStatus` Livewire component can be embedded in the admin dashboard:

```blade
@livewire('examination-hub.load-balancer-status')
```

It shows:
- Current mode (local-first / central-first / off)
- Local backend status and session count
- Central backend status and session count
- Health endpoint status (`/up` and `/lb-health`)
- Rollback button

### 13.4 Runtime commands

```bash
# Check HAProxy status
echo 'show stat' | socat stdio /run/haproxy/admin.sock

# Force new candidates to local (disable Central backend)
echo 'disable server central/central' | socat stdio /run/haproxy/admin.sock

# Force new candidates to Central (disable local backend)
echo 'disable server local/app' | socat stdio /run/haproxy/admin.sock

# Re-enable a backend
echo 'enable server central/central' | socat stdio /run/haproxy/admin.sock
```

### 13.5 Log files

| Log | Location |
|-----|----------|
| HAProxy | `/var/log/haproxy.log` |
| Nginx access | `/var/log/nginx/<domain>-access.log` |
| Nginx error | `/var/log/nginx/<domain>-error.log` |
| Laravel app | `/var/www/html/ahpc/storage/logs/laravel.log` |
| Queue worker | `/var/www/html/ahpc/storage/logs/worker.log` |

### 13.6 Certificate management

HAProxy uses the TLS certificate at `/etc/ahpc/tls/haproxy.pem`. When Let's Encrypt renews:

```bash
# Regenerate HAProxy PEM from renewed cert
cat /etc/letsencrypt/live/<domain>/fullchain.pem \
    /etc/letsencrypt/live/<domain>/privkey.pem \
    > /etc/ahpc/tls/haproxy.pem
chmod 600 /etc/ahpc/tls/haproxy.pem
systemctl reload haproxy
```

Or re-run the setup script:

```bash
sudo bash ahpc-multiserver-setup.sh --only lb --lb-mode local-first
```

### 13.7 Weekly checks

- [ ] Verify HAProxy is running on all centers
- [ ] Check certificate expiration dates
- [ ] Review HAProxy logs for errors
- [ ] Verify sync is working (check last sync timestamps)
- [ ] Check Central load metrics

---

## 14. Troubleshooting

### 14.1 HAProxy won't start

```bash
# Check config syntax
sudo haproxy -c -f /etc/haproxy/haproxy.cfg

# Check for port conflicts
sudo ss -tlnp | grep -E ':(80|443)\s'

# Check logs
sudo journalctl -u haproxy --no-pager -n 50
```

**Common causes:**
- Nginx still listening on port 80/443 (should be on 127.0.0.1:8081)
- TLS certificate missing or invalid
- Config syntax error

### 14.2 Health endpoints return 503

```bash
# Check database connectivity
php artisan tinker --execute="DB::connection()->getPdo(); echo 'OK';"

# Check PHP-FPM
systemctl status php8.2-fpm

# Check /lb-health response
curl -s http://127.0.0.1/lb-health | jq
```

**Common causes:**
- Database connection threshold exceeded
- PHP-FPM workers all busy
- Database server unreachable

### 14.3 Candidates not getting sticky cookie

```bash
# Check if cookie is being set
curl -v http://127.0.0.1/up 2>&1 | grep -i 'set-cookie'

# Check HAProxy config for cookie settings
grep -A2 'Set-Cookie' /etc/haproxy/haproxy.cfg
```

**Common causes:**
- Request is over HTTP, not HTTPS (cookie is Secure-only)
- HAProxy config doesn't include the `http-response add-header Set-Cookie` directive

### 14.4 All traffic goes to one backend

```bash
# Check backend health
echo 'show stat' | socat stdio /run/haproxy/admin.sock | grep -E '(local|central)'

# Check health endpoint responses
curl -s http://127.0.0.1:8081/up
curl -s https://central.example.com/lb-health
```

**Common causes:**
- One backend is marked DOWN by health checks
- Connection limit (`be_conn`) is exceeded
- Routing rules in HAProxy config are incorrect

### 14.5 Real client IP not reaching Laravel

```bash
# Check TrustProxies config
php artisan tinker --execute="echo config('center.trusted_proxy_ips');"

# Check middleware
grep -r 'TrustProxies' app/Http/Kernel.php
```

**Common causes:**
- Center IP not in `TRUSTED_PROXY_IPS` on Central
- TrustProxies middleware not in the `web` middleware group
- `X-Forwarded-For` header not being set by HAProxy

### 14.6 Submission sync issues

```bash
# Check sync status
php artisan tinker --execute="
\$subs = \App\ExaminationHub\Models\GeneralExamSubmission::whereNull('server_origin')->count();
echo 'Submissions without server_origin: ' . \$subs . PHP_EOL;
"

# Manually trigger sync
php artisan sync:push
php artisan sync:pull
```

**Common causes:**
- `server_origin` column missing (migration not run)
- Sync token misconfigured
- Network connectivity issues between center and Central

### 14.7 Both backends down — 503 page

If both local and Central backends are down, HAProxy returns the custom 503 page. Check:

```bash
# Verify the 503 page exists
cat /etc/haproxy/errors/503.http

# Check both backends
echo 'show stat' | socat stdio /run/haproxy/admin.sock | grep -E '(local|central)'
```

---

## 15. Rollback Procedures

### 15.1 Quick rollback — disable HAProxy

```bash
# One command to stop HAProxy and restore Nginx
php artisan exam:lb:rollback --mode=off
```

Or re-run the setup script:

```bash
sudo bash ahpc-multiserver-setup.sh --only lb,nginx,tls --lb-mode off
```

This:
1. Stops and disables HAProxy
2. Restores Nginx to ports 80/443
3. Removes `LB_MODE` from `.env`
4. Clears Laravel caches

### 15.2 Switch between modes

```bash
# Stage A → Stage B
sudo bash ahpc-multiserver-setup.sh --only lb --lb-mode central-first

# Stage B → Stage A
sudo bash ahpc-multiserver-setup.sh --only lb --lb-mode local-first
```

### 15.3 Emergency — force backend at runtime

Without restarting anything:

```bash
# Force all new candidates to local
echo 'disable server central/central' | socat stdio /run/haproxy/admin.sock

# Force all new candidates to Central
echo 'disable server local/app' | socat stdio /run/haproxy/admin.sock

# Re-enable
echo 'enable server central/central' | socat stdio /run/haproxy/admin.sock
echo 'enable server local/app' | socat stdio /run/haproxy/admin.sock
```

### 15.4 Full rollback to pre-HAProxy state

```bash
# Remove HAProxy entirely
sudo apt-get remove -y haproxy

# Restore Nginx config manually if needed
# (The --lb-mode off command should handle this)

# Remove LB settings from .env
sed -i '/^LB_MODE=/d' /var/www/html/ahpc/.env
sed -i '/^TRUSTED_PROXY_IPS=/d' /var/www/html/ahpc/.env

# Clear caches
php artisan config:clear
php artisan route:clear
```

---

## Appendix A: HAProxy Config Reference

The generated config at `/etc/haproxy/haproxy.cfg` includes:

- **Global:** maxconn 4000, stats socket, TLS tuning
- **Defaults:** HTTP mode, forwardfor, timeouts (connect 3s, client 60s, server 120s, queue 10s)
- **Frontend:** ports 80/443, HTTP→HTTPS redirect, sticky cookie ACLs, health check ACLs, connection limit ACLs
- **Backend local:** health check on `/up`, sticky cookie `ahpc_route=local`, `X-Forwarded-Server: local` header
- **Backend central:** health check on `/lb-health`, sticky cookie `ahpc_route=central`, `X-Forwarded-Server: central` and `X-Forwarded-Center` headers, SSL with `verify none`
- **Stats:** port 9000, `/haproxy?stats` URI, 10s refresh

### Production note on SSL verification

The setup script uses `verify none` for the Central backend SSL connection. For production, install Central's CA certificate and change to:

```
server central <HOST>:443 check ssl verify required ca-file /usr/local/share/ca-certificates/ahpc-central-ca.crt verifyhost <HOST> maxconn 400
```

---

## Appendix B: Threshold Tuning Guide

| Parameter | Default | Description | When to adjust |
|-----------|---------|-------------|----------------|
| `--lb-local-max` | 150 | Max connections to local backend | If local overflows too early or too late |
| `--lb-central-max` | 350 | Max connections to Central backend | If Central overflows too early or too late |
| `--lb-cookie-hours` | 4 | Sticky cookie lifetime | If exams are longer than 4 hours |
| `LB_HEALTH_THRESHOLD` | 85 | PHP-FPM busy % threshold | If Central returns 503 too often or not often enough |
| `LB_HEALTH_DB_THRESHOLD` | 50 | DB connection fallback threshold | If DB connections are a better load indicator |

After tuning, re-run the setup script or edit `/etc/haproxy/haproxy.cfg` directly and reload:

```bash
sudo systemctl reload haproxy
```

---

## Appendix C: File Reference

| File | Purpose |
|------|---------|
| `app/ExaminationHub/Controllers/HealthCheckController.php` | `/up` and `/lb-health` endpoints |
| `app/Http/Middleware/TrustProxies.php` | Trusts proxy headers for real client IP |
| `config/center.php` | LB mode, trusted IPs, health thresholds |
| `ahpc-multiserver-setup.sh` | HAProxy installation and config generation |
| `database/migrations/2026_11_12_000001_add_server_origin_to_general_exam_submissions_table.php` | `server_origin` column migration |
| `app/Console/Commands/LoadBalancerStatus.php` | `php artisan exam:lb:status` command |
| `app/Console/Commands/LoadBalancerRollback.php` | `php artisan exam:lb:rollback` command |
| `app/Livewire/ExaminationHub/LoadBalancerStatus.php` | Admin dashboard Livewire component |
| `resources/views/livewire/examination-hub/load-balancer-status.blade.php` | Livewire component view |
| `routes/web.php` | Public health check route registrations |

---

## Appendix D: Quick Start Checklist

For a fast test on a single center:

- [ ] Deploy same tag on Central and center
- [ ] Run migrations on both servers
- [ ] Set `TRUSTED_PROXY_IPS` on Central
- [ ] On center: `sudo bash ahpc-multiserver-setup.sh --app-role center --center-id <UUID> --central-url https://central.example.com --lb-mode local-first`
- [ ] Verify: `curl -s http://127.0.0.1/up`
- [ ] Verify: `php artisan exam:lb:status`
- [ ] Test login and exam flow from a browser
- [ ] Check `server_origin` on the created submission
- [ ] Test rollback: `php artisan exam:lb:rollback --mode=off`
