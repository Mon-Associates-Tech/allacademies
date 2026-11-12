# AHPC Central-First Load Balancing — Local Testing Guide (Mac + Arch Linux)

**Version:** 1.0  
**Date:** 12 November 2026  
**Machines:** 1 Mac (Central server) + 1 Arch Linux (Center server with HAProxy)

---

## Overview

This guide shows how to test the Central-First load balancing feature using two local machines on the same LAN, without any production servers or internet access.

- **Mac OS** → Acts as the **Central** server (serves the app on port 8000)
- **Arch Linux** → Acts as the **Center** server (runs HAProxy on ports 80/443, local app on port 8081)

Both machines run the same Laravel app from the `ahpc` branch. They share the same database or use separate databases — both approaches work for testing.

---

## Table of Contents

1. [Network Setup](#1-network-setup)
2. [Mac OS — Central Server Setup](#2-mac-os--central-server-setup)
3. [Arch Linux — Center Server Setup](#3-arch-linux--center-server-setup)
4. [Database Setup](#4-database-setup)
5. [Environment Configuration](#5-environment-configuration)
6. [HAProxy Setup on Arch Linux](#6-haproxy-setup-on-arch-linux)
7. [Testing Checklist](#7-testing-checklist)
8. [Troubleshooting](#8-troubleshooting)

---

## 1. Network Setup

### 1.1 Ensure both machines are on the same network

Both machines must be on the same Wi-Fi or Ethernet network so they can reach each other.

### 1.2 Find the Mac's LAN IP address

On the Mac, run:

```bash
ipconfig getifaddr en0
```

Note the IP (e.g., `192.168.1.100`). This is your **Central URL**.

### 1.3 Find the Arch Linux IP address

On Arch Linux, run:

```bash
ip addr show | grep "inet " | grep -v 127.0.0.1
```

Note the IP (e.g., `192.168.1.101`). This is your **Center URL**.

### 1.4 Test connectivity

From Arch Linux, ping the Mac:

```bash
ping -c 3 192.168.1.100
```

From the Mac, ping Arch Linux:

```bash
ping -c 3 192.168.1.101
```

If ping is blocked, test with curl instead:

```bash
curl -s http://192.168.1.100:8000/up
```

### 1.5 Firewall rules

**Mac OS:** macOS firewall may block incoming connections. Temporarily disable it for testing:

```bash
# System Settings → Network → Firewall → Turn Off
# Or via terminal:
sudo /usr/libexec/ApplicationFirewall/socketfilterfw --setglobalstate off
```

**Arch Linux:** If `ufw` or `firewalld` is running, open the required ports:

```bash
# If using ufw:
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw allow 8081/tcp

# If using firewalld:
sudo firewall-cmd --add-port=80/tcp --permanent
sudo firewall-cmd --add-port=443/tcp --permanent
sudo firewall-cmd --add-port=8081/tcp --permanent
sudo firewall-cmd --reload
```

---

## 2. Mac OS — Central Server Setup

### 2.1 Install PHP and dependencies

If you don't have PHP 8.4+:

```bash
# Using Homebrew
brew install php@8.4 mariadb redis

# Start services
brew services start mariadb
brew services start redis
```

### 2.2 Clone and checkout the code

```bash
cd ~/Projects
git clone <your-repo-url> allacademies
cd allacademies
git fetch origin ahpc
git checkout ahpc
git pull origin ahpc
```

### 2.3 Install dependencies

```bash
# PHP dependencies
composer install

# Node dependencies (for Vite assets)
npm ci && npm run build
```

### 2.4 Create .env file

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env`:

```env
APP_NAME="All Academies"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://192.168.1.100:8000
# Replace with your Mac's actual IP

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=allacademies
DB_USERNAME=root
DB_PASSWORD=

APP_ROLE=central
TRUSTED_PROXY_IPS=192.168.1.101
# Replace with your Arch Linux IP
```

### 2.5 Create the database

```bash
mysql -u root -e "CREATE DATABASE IF NOT EXISTS allacademies CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

### 2.6 Run migrations

```bash
php artisan migrate --force
php artisan storage:link
php artisan config:clear
php artisan route:clear
```

### 2.7 Start the development server

```bash
# Bind to 0.0.0.0 so Arch Linux can reach it
php artisan serve --host=0.0.0.0 --port=8000
```

Keep this terminal open. The Mac is now your Central server at `http://192.168.1.100:8000`.

### 2.8 Verify from Arch Linux

On Arch Linux, test:

```bash
curl -s http://192.168.1.100:8000/up | jq
# Expected: {"status":"ok"}

curl -s http://192.168.1.100:8000/lb-health | jq
# Expected: {"status":"ok", "db_connections":N, ...}
```

---

## 3. Arch Linux — Center Server Setup

### 3.1 Install PHP and dependencies

```bash
sudo pacman -S php php-fpm php-mysql php-redis php-gd php-mbstring php-intl php-zip php-xml php-sodium php-curl php-bcmath
sudo pacman -S mariadb redis nginx haproxy
sudo pacman -S nodejs npm composer
```

### 3.2 Start services

```bash
sudo systemctl enable --now mariadb
sudo systemctl enable --now redis
sudo systemctl enable --now php-fpm
```

### 3.3 Clone and checkout the code

```bash
cd ~/Projects
git clone <your-repo-url> allacademies
cd allacademies
git fetch origin ahpc
git checkout ahpc
git pull origin ahpc
```

### 3.4 Install dependencies

```bash
composer install
npm ci && npm run build
```

### 3.5 Create .env file

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env`:

```env
APP_NAME="All Academies"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://192.168.1.101
# Replace with your Arch Linux IP

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=allacademies_center
DB_USERNAME=root
DB_PASSWORD=

APP_ROLE=center
CENTER_ID=test-center-001
CENTRAL_URL=http://192.168.1.100:8000
# Replace with your Mac's actual IP
CENTER_SYNC_TOKEN=test-sync-token-123
LB_MODE=local-first
TRUSTED_PROXY_IPS=127.0.0.1
```

### 3.6 Create the database

```bash
sudo mysql -e "CREATE DATABASE IF NOT EXISTS allacademies_center CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

### 3.7 Run migrations

```bash
php artisan migrate --force
php artisan storage:link
php artisan config:clear
php artisan route:clear
```

---

## 4. Database Setup

### Option A: Separate databases (recommended for testing)

Each machine uses its own local MariaDB. This is simpler and isolates failures.

- **Mac (Central):** `allacademies` database
- **Arch (Center):** `allacademies_center` database

Sync between them will work via HTTP API calls, not shared database.

### Option B: Shared database (quickest setup)

If you want to skip sync testing and just test HAProxy routing:

Point both `.env` files to the same database on one machine:

```env
# On Arch Linux .env, point to Mac's database:
DB_HOST=192.168.1.100
DB_DATABASE=allacademies
```

This means both servers read/write the same data, so you don't need to test sync.

---

## 5. Environment Configuration

### 5.1 TrustProxies

The `TrustProxies` middleware is already configured with `$proxies = 'header'`, which trusts any request with `X-Forwarded-For` headers. This is perfect for local testing.

On the Mac (Central), ensure `.env` has the Arch Linux IP:

```env
TRUSTED_PROXY_IPS=192.168.1.101
```

### 5.2 Clear caches on both machines

```bash
# On both Mac and Arch
php artisan config:clear
php artisan route:clear
php artisan cache:clear
```

---

## 6. HAProxy Setup on Arch Linux

### 6.1 Generate a self-signed certificate

For local testing, HAProxy needs a TLS certificate. Generate a self-signed one:

```bash
sudo mkdir -p /etc/ahpc/tls
sudo openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
  -keyout /etc/ahpc/tls/key.pem \
  -out /etc/ahpc/tls/cert.pem \
  -subj "/CN=192.168.1.101" \
  -addext "subjectAltName=IP:192.168.1.101"
sudo cat /etc/ahpc/tls/cert.pem /etc/ahpc/tls/key.pem > /etc/ahpc/tls/haproxy.pem
sudo chmod 600 /etc/ahpc/tls/haproxy.pem
```

### 6.2 Create HAProxy configuration

Create `/etc/haproxy/haproxy.cfg`:

```bash
sudo tee /etc/haproxy/haproxy.cfg > /dev/null << 'EOF'
global
    log stdout format raw local0
    maxconn 4000
    stats socket /run/haproxy/admin.sock mode 660 level admin
    stats timeout 30s
    ssl-default-bind-ciphers ECDHE-ECDSA-AES128-GCM-SHA256:ECDHE-RSA-AES128-GCM-SHA256
    ssl-default-bind-options ssl-min-ver TLSv1.2

defaults
    log     global
    mode    http
    option  httplog
    option  dontlognull
    option  forwardfor
    timeout connect 3s
    timeout client  60s
    timeout server  120s
    timeout queue   10s

frontend http_front
    bind *:80
    mode http
    redirect scheme https code 301 if !{ ssl_fc }

frontend https_front
    bind *:443 ssl crt /etc/ahpc/tls/haproxy.pem
    mode http

    # Sticky session cookie
    acl has_sticky_cookie cookie(ahpc_route) -m found
    acl route_local cookie(ahpc_route) -m str local
    acl route_central cookie(ahpc_route) -m str central

    # Health check ACLs
    acl central_healthy nbsrv(central) gt 0
    acl local_healthy nbsrv(local) gt 0

    # Connection limit ACLs
    acl local_saturated sc1_conn gt 150
    acl central_saturated sc2_conn gt 350

    # Static files always go local
    acl is_static path_beg /build/ /storage/ /css/ /js/ /images/ /fonts/

    # Routing rules for LOCAL-FIRST mode (Stage A)
    # If sticky cookie exists, respect it
    use_backend local if has_sticky_cookie route_local local_healthy
    use_backend central if has_sticky_cookie route_central central_healthy

    # Static files always local
    use_backend local if is_static local_healthy

    # Default: prefer local, overflow to central
    use_backend local if !local_saturated local_healthy
    use_backend central if central_healthy

    # Fallback: if local is down, go central; if central is down, go local
    use_backend central if !local_healthy central_healthy
    use_backend local if !central_healthy local_healthy

    # Both down: return 503
    default_backend local

backend local
    mode http
    balance roundrobin
    option httpchk GET /up
    http-check expect status 200
    cookie local insert indirect nocache
    http-response add-header Set-Cookie "ahpc_route=local; Path=/; Max-Age=14400; Secure; HttpOnly; SameSite=Lax" if !has_sticky_cookie
    http-request set-header X-Forwarded-Server local
    http-request set-header X-Forwarded-Center "test-center-001"
    server app 127.0.0.1:8081 check inter 5s fall 3 rise 2 maxconn 200

backend central
    mode http
    balance roundrobin
    option httpchk GET /lb-health
    http-check expect status 200
    cookie central insert indirect nocache
    http-response add-header Set-Cookie "ahpc_route=central; Path=/; Max-Age=14400; Secure; HttpOnly; SameSite=Lax" if !has_sticky_cookie
    http-request set-header X-Forwarded-Server central
    http-request set-header X-Forwarded-Center "test-center-001"
    # For local testing, Central is HTTP (no SSL)
    server central 192.168.1.100:8000 check inter 5s fall 3 rise 2 maxconn 400

listen stats
    bind *:9000
    mode http
    stats enable
    stats uri /haproxy?stats
    stats refresh 10s
    stats admin if LOCALHOST
EOF
```

**Important:** Replace `192.168.1.100` with your Mac's actual IP in the `server central` line.

### 6.3 Create HAProxy runtime directory

```bash
sudo mkdir -p /run/haproxy
sudo touch /run/haproxy/admin.sock
sudo chmod 660 /run/haproxy/admin.sock
```

### 6.4 Validate HAProxy config

```bash
sudo haproxy -c -f /etc/haproxy/haproxy.cfg
```

Expected: `Configuration file is valid`

### 6.5 Configure Nginx on port 8081

Create `/etc/nginx/sites-available/ahpc-center`:

```bash
sudo tee /etc/nginx/sites-available/ahpc-center > /dev/null << 'EOF'
server {
    listen 127.0.0.1:8081;
    server_name localhost;

    root /home/youruser/Projects/allacademies/public;
    index index.php;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_param HTTPS on;
    }

    location ~ /\.(?!well-known/) {
        deny all;
    }

    error_page 500 502 503 504 /503.html;
    location = /503.html {
        root /usr/share/nginx/html;
        internal;
    }
}
EOF
```

Replace `/home/youruser/Projects/allacademies/public` with your actual path.

Enable the site:

```bash
sudo ln -sf /etc/nginx/sites-available/ahpc-center /etc/nginx/sites-enabled/
sudo rm -f /etc/nginx/sites-enabled/default
sudo nginx -t
sudo systemctl reload nginx
```

### 6.6 Start HAProxy

```bash
sudo systemctl enable --now haproxy
sudo systemctl status haproxy
```

### 6.7 Alternative: Use PHP artisan serve instead of Nginx

If Nginx setup is too complex for local testing, you can skip it entirely and use Laravel's built-in server:

**On Arch Linux, in a separate terminal:**

```bash
cd ~/Projects/allacademies
php artisan serve --host=127.0.0.1 --port=8081
```

Then update HAProxy config to point to this port (it already does: `server app 127.0.0.1:8081`).

This is the **quickest path** for local testing.

---

## 7. Testing Checklist

### 7.1 Verify health endpoints

**On Mac (Central):**

```bash
curl -s http://127.0.0.1:8000/up | jq
# Expected: {"status":"ok"}

curl -s http://127.0.0.1:8000/lb-health | jq
# Expected: {"status":"ok", ...}
```

**On Arch (Center) — via HAProxy:**

```bash
curl -sk https://127.0.0.1/up | jq
# Expected: {"status":"ok"} (routed to local backend)

curl -s http://127.0.0.1/up | jq
# Expected: redirects to HTTPS
```

### 7.2 Verify sticky cookie

```bash
# First request sets the cookie
curl -v -c /tmp/cookies.txt -k https://127.0.0.1/up 2>&1 | grep -i 'set-cookie'
# Expected: Set-Cookie: ahpc_route=local; ...

# Second request includes the cookie
curl -v -b /tmp/cookies.txt -k https://127.0.0.1/up 2>&1 | grep -i 'cookie:'
# Expected: Cookie: ahpc_route=local
```

### 7.3 Verify HAProxy stats

```bash
# Install socat if not present
sudo pacman -S socat

# Query backend status
echo 'show stat' | socat stdio /run/haproxy/admin.sock | head -10

# Or use the Artisan command
php artisan exam:lb:status
```

### 7.4 Test the full exam flow

1. Open a browser and navigate to `https://192.168.1.101` (Arch Linux IP)
   - Accept the self-signed certificate warning
2. Login as a test user
3. Navigate to the exam hub
4. Join an exam
5. Complete a section and save responses
6. Submit the exam

**Verify:**

```bash
# Check the submission has server_origin set
php artisan tinker --execute="
\$sub = \App\ExaminationHub\Models\GeneralExamSubmission::latest()->first();
echo 'server_origin: ' . (\$sub->server_origin ?? 'NULL') . PHP_EOL;
"
# Expected: server_origin: local
```

### 7.5 Test Central routing (Stage B)

To test central-first mode, update HAProxy config:

```bash
sudo nano /etc/haproxy/haproxy.cfg
```

Change the routing rules section to:

```
    # Routing rules for CENTRAL-FIRST mode (Stage B)
    use_backend local if has_sticky_cookie route_local local_healthy
    use_backend central if has_sticky_cookie route_central central_healthy

    use_backend local if is_static local_healthy

    # Default: prefer central, fallback to local
    use_backend central if !central_saturated central_healthy
    use_backend local if local_healthy

    use_backend local if !central_healthy local_healthy
    use_backend central if !local_healthy central_healthy
```

Then reload HAProxy:

```bash
sudo systemctl reload haproxy
```

Now test:

```bash
# Clear cookies
rm /tmp/cookies.txt

# New request should route to Central
curl -v -c /tmp/cookies.txt -k https://127.0.0.1/up 2>&1 | grep -i 'set-cookie'
# Expected: Set-Cookie: ahpc_route=central; ...
```

### 7.6 Test failure scenarios

**Simulate Central going down:**

On Mac, stop the PHP server (Ctrl+C).

On Arch, test:

```bash
# HAProxy should detect Central is down and route to local
curl -sk https://127.0.0.1/up | jq
# Expected: {"status":"ok"} (from local backend)

# Check HAProxy stats
echo 'show stat' | socat stdio /run/haproxy/admin.sock | grep central
# Should show: DOWN
```

**Simulate Central overloaded:**

On Mac, temporarily set a very low threshold:

```bash
# In .env on Mac:
LB_HEALTH_THRESHOLD=1
php artisan config:clear
```

On Arch:

```bash
curl -sk https://127.0.0.1/up | jq
# Should route to local because Central returns 503
```

### 7.7 Test rollback

```bash
# Switch back to local-first
php artisan exam:lb:rollback --mode=local-first

# Or disable HAProxy entirely
php artisan exam:lb:rollback --mode=off
```

---

## 8. Troubleshooting

### 8.1 HAProxy won't start

```bash
# Check config syntax
sudo haproxy -c -f /etc/haproxy/haproxy.cfg

# Check for port conflicts
sudo ss -tlnp | grep -E ':(80|443)\s'

# Check logs
sudo journalctl -u haproxy --no-pager -n 50
```

**Common causes:**
- Nginx or another service already on port 80/443
- TLS certificate missing or invalid
- Config syntax error

### 8.2 Health endpoints return errors

```bash
# On Mac, check the app is running
curl -s http://127.0.0.1:8000/up

# On Arch, check local backend
curl -s http://127.0.0.1:8081/up

# Check database connectivity
php artisan tinker --execute="DB::connection()->getPdo(); echo 'OK';"
```

### 8.3 HAProxy can't reach Mac (Central)

```bash
# From Arch, test connectivity
curl -s http://192.168.1.100:8000/up

# If it fails, check:
# 1. Mac firewall is off
# 2. Mac PHP server is bound to 0.0.0.0, not 127.0.0.1
# 3. IP address is correct
```

### 8.4 Sticky cookie not set

```bash
# Check HAProxy config
grep -A2 'Set-Cookie' /etc/haproxy/haproxy.cfg

# Test with verbose output
curl -v -k https://127.0.0.1/up 2>&1 | grep -i 'set-cookie'
```

**Note:** The cookie is `Secure`-only, so it's only set over HTTPS. Test with `https://` or `-k` flag.

### 8.5 Both backends show DOWN

```bash
# Check both backends independently
curl -s http://127.0.0.1:8081/up          # Local
curl -s http://192.168.1.100:8000/up      # Central

# Check HAProxy health checks
echo 'show stat' | socat stdio /run/haproxy/admin.sock | grep -E '(local|central)'
```

### 8.6 CORS or mixed content errors in browser

Since you're using a self-signed cert, the browser may block mixed content. Ensure:

1. You access the site via `https://` (HAProxy redirects HTTP to HTTPS)
2. You accept the certificate warning
3. All asset URLs use protocol-relative or HTTPS URLs

---

## Quick Start Summary

The fastest way to get testing:

### On Mac (Central):
```bash
cd ~/Projects/allacademies
git checkout ahpc && git pull origin ahpc
composer install && npm ci && npm run build
cp .env.example .env
php artisan key:generate
# Edit .env: APP_ROLE=central, APP_URL=http://YOUR_MAC_IP:8000
mysql -u root -e "CREATE DATABASE allacademies;"
php artisan migrate --force
php artisan serve --host=0.0.0.0 --port=8000
```

### On Arch Linux (Center):
```bash
sudo pacman -S php php-fpm php-mysql php-redis php-gd php-mbstring php-zip php-xml php-sodium php-curl php-bcmath mariadb redis haproxy socat nodejs npm composer
sudo systemctl enable --now mariadb redis

cd ~/Projects/allacademies
git checkout ahpc && git pull origin ahpc
composer install && npm ci && npm run build
cp .env.example .env
php artisan key:generate
# Edit .env: APP_ROLE=center, CENTRAL_URL=http://MAC_IP:8000, LB_MODE=local-first
mysql -u root -e "CREATE DATABASE allacademies_center;"
php artisan migrate --force

# Generate self-signed cert
sudo mkdir -p /etc/ahpc/tls
sudo openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout /etc/ahpc/tls/key.pem -out /etc/ahpc/tls/cert.pem -subj "/CN=localhost"
sudo cat /etc/ahpc/tls/cert.pem /etc/ahpc/tls/key.pem > /etc/ahpc/tls/haproxy.pem

# Write HAProxy config (from Section 6.2, replacing MAC_IP)
# Then:
sudo haproxy -c -f /etc/haproxy/haproxy.cfg
sudo systemctl enable --now haproxy

# Start local app
php artisan serve --host=127.0.0.1 --port=8081
```

### Test:
```bash
# From Arch Linux
curl -sk https://127.0.0.1/up | jq
# Expected: {"status":"ok"}

# Open browser at https://ARCH_LINUX_IP (accept cert warning)
# Login, join exam, submit, check server_origin
```

---

## Notes

- **No internet required:** Both machines communicate over LAN only. The Mac acts as Central; Arch acts as Center with HAProxy.
- **Self-signed certificates:** Browsers will show warnings. Accept them for testing.
- **Laravel serve:** Using `php artisan serve` instead of Nginx on the Center simplifies setup. For production-like testing, use Nginx (Section 6.5).
- **Database:** Each machine uses its own database. Sync testing works via HTTP API between them.
- **HAProxy setup script:** The `ahpc-multiserver-setup.sh` script is designed for Ubuntu/Debian. On Arch Linux, follow the manual steps in this guide instead.
