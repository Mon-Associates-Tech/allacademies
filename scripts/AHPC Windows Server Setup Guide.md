# AHPC Windows Server Setup Guide

**Version:** 1.0  
**Date:** 13 November 2026  
**Script:** `ahpc-multiserver-setup.ps1`

---

## Overview

This guide explains how to provision AHPC servers on **Windows Server 2019/2022/2025** using the PowerShell setup script `ahpc-multiserver-setup.ps1`. It provides equivalent functionality to the Linux bash script `ahpc-multiserver-setup.sh`.

---

## Quick Start

### Central Server

```powershell
# Run PowerShell as Administrator
.\ahpc-multiserver-setup.ps1 -AppRole central
```

### Center Server

```powershell
# Run PowerShell as Administrator
.\ahpc-multiserver-setup.ps1 -AppRole center `
  -CentralUrl "http://192.168.1.103" `
  -SyncToken "your-sync-token" `
  -PublicKey "your-central-public-key-base64"
```

### Dry Run (Preview Changes)

```powershell
.\ahpc-multiserver-setup.ps1 -AppRole central -DryRun
```

---

## Prerequisites

### System Requirements
- **OS:** Windows Server 2019, 2022, or 2025
- **PowerShell:** 5.0 or higher (built into Windows Server)
- **RAM:** Minimum 4GB (8GB recommended for 400+ candidates)
- **Disk:** 20GB free space minimum
- **Network:** Internet access for initial setup (downloads packages)

### Permissions
- **Administrator privileges required.** Right-click PowerShell and select "Run as Administrator"
- Execution policy must allow script execution:
  ```powershell
  Set-ExecutionPolicy Bypass -Scope Process -Force
  ```

### Network
- The server must have internet access during setup to download:
  - Chocolatey package manager
  - Git, 7-Zip, MySQL, Redis, PHP, Node.js, Composer
  - Project code from GitHub
  - IIS URL Rewrite module (if using IIS)

---

## What the Script Does

The script performs these steps automatically:

| # | Step | Description |
|---|------|-------------|
| 1 | Windows Update | Checks update status (skipped by default for speed) |
| 2 | Base Utilities | Installs Chocolatey, Git, 7-Zip |
| 3 | MySQL | Installs MySQL, creates database and user |
| 4 | Redis | Installs Redis, configures password and localhost binding |
| 5 | PHP | Installs PHP with required extensions, production tuning |
| 6 | Node.js + npm | Installs Node.js LTS for frontend builds |
| 6b | Composer | Installs PHP package manager |
| 7 | Project Files | Downloads the `ahpc` branch from GitHub |
| 8 | .env Config | Writes all credentials and sync settings |
| 9 | Dependencies | Runs `composer install` and `npm ci && npm run build` |
| 10 | Laravel Setup | Generates app key, runs migrations, creates storage link |
| 11 | Admin User | Provisions default admin user |
| 12 | Web Server | Configures IIS (default) or Nginx |
| 13 | Queue Worker | Registers Windows Task Scheduler task |
| 14 | Scheduler | Registers Laravel scheduler task (every minute) |
| 15 | Firewall | Opens ports 80, 443, 22, 9000 |
| 16 | Sync Tasks | (Center only) Registers pull/push sync tasks |
| 17 | Health Check | Tests the application is responding |
| 18 | Credentials | Writes summary to `C:\ahpc-credentials.txt` |

---

## Command-Line Parameters

### Required

| Parameter | Description | Example |
|-----------|-------------|---------|
| `-AppRole` | Server role: `central` or `center` | `-AppRole central` |

### Center-Only Required

When `-AppRole center`, these are also required:

| Parameter | Description | Example |
|-----------|-------------|---------|
| `-CentralUrl` | Central server URL | `-CentralUrl "http://192.168.1.103"` |
| `-SyncToken` | Sync token from Central admin | `-SyncToken "abc123"` |
| `-PublicKey` | Central Ed25519 public key (base64) | `-PublicKey "xyz..."` |

### Optional

| Parameter | Default | Description |
|-----------|---------|-------------|
| `-DbPassword` | Auto-generated | Database password |
| `-RedisPassword` | Auto-generated | Redis password |
| `-Ip` | Auto-detected | Server IP address |
| `-Domain` | `ahpc.internal` | Domain name |
| `-RepoBranch` | `ahpc` | Git branch to deploy |
| `-AdminEmail` | `admin@ahpc.local` | Default admin email |
| `-AdminPassword` | Auto-generated | Default admin password |
| `-AppVersion` | `1.0.0` | App version for sync handshake |
| `-PullInterval` | `15` | Pull sync interval (minutes) |
| `-PushInterval` | `15` | Push sync interval (minutes) |
| `-WebServer` | `IIS` | Web server: `IIS` or `Nginx` |
| `-ProjectDir` | `C:\inetpub\ahpc` | Installation directory |
| `-SelfSignedSsl` | Off | Generate self-signed HTTPS cert |
| `-SkipWindowsUpdate` | Off | Skip Windows Update check |
| `-DryRun` | Off | Preview changes without applying |

---

## Key Differences from Linux Script

| Feature | Linux (bash) | Windows (PowerShell) |
|---------|-------------|---------------------|
| Package manager | `apt-get` | Chocolatey (`choco`) |
| Web server | Nginx | IIS (default) or Nginx |
| PHP mode | PHP-FPM (socket) | PHP-CGI (FastCGI via IIS) |
| Queue worker | Supervisor | Windows Task Scheduler |
| Laravel scheduler | Cron | Windows Task Scheduler |
| Sync tasks | Cron | Windows Task Scheduler |
| Firewall | UFW | Windows Firewall (`netsh advfirewall`) |
| SSL | Certbot (Let's Encrypt) | Self-signed cert (`-SelfSignedSsl`) |
| Storage link | `ln -s` (symlink) | `mklink /J` (junction) |
| File permissions | `chown`/`chmod` | NTFS ACLs (`icacls`) |
| HAProxy | Native HAProxy | **Not available** (see below) |
| Service management | `systemctl` | `Get-Service`/`Restart-Service` |
| Log rotation | `logrotate` | Manual or third-party tool |

---

## HAProxy on Windows — Important Limitation

**HAProxy does not run natively on Windows.** The load balancing feature (Central-First architecture) requires HAProxy, which is Linux-only.

### Options for Windows Center Servers

1. **Use Linux for center servers** — Run the bash script (`ahpc-multiserver-setup.sh`) on Ubuntu/Debian for centers that need HAProxy load balancing.

2. **Use IIS URL Rewrite as a simple proxy** — IIS can reverse-proxy to Central, but without the sophisticated health checks, sticky sessions, and connection limiting that HAProxy provides.

3. **Use Windows Subsystem for Linux (WSL2)** — Run HAProxy inside WSL2 on the Windows Server. This is experimental and not recommended for production.

4. **Deploy a separate Linux box for HAProxy** — Place a small Linux VM between candidates and the Windows center server, running only HAProxy.

### Recommendation

For production deployments requiring load balancing:
- **Central server:** Can be Windows or Linux
- **Center servers with LB:** Use Linux (Ubuntu/Debian) with the bash script
- **Center servers without LB:** Can use Windows with the PowerShell script

The PowerShell script is ideal for:
- Central servers in Windows environments
- Center servers that don't need HAProxy load balancing
- Environments where Windows Server is the standard platform
- Testing and development

---

## IIS Configuration Details

When using IIS (default), the script:

1. **Installs IIS role** with required features:
   - Web Server (IIS)
   - ASP.NET 4.5
   - .NET Extensibility 4.5
   - ISAPI Extensions
   - ISAPI Filters
   - Management Tools

2. **Creates a website** named `ahpc` on port 80

3. **Configures PHP FastCGI** — Registers `php-cgi.exe` with IIS

4. **Creates `web.config`** with:
   - URL Rewrite rule (Laravel front controller pattern)
   - Static file caching (30 days)
   - Request size limit (100MB)
   - Default document (index.php)

5. **Installs URL Rewrite module** if not present

### IIS Management

After setup, manage the site via:
- **IIS Manager:** `inetmgr`
- **PowerShell:**
  ```powershell
  Import-Module WebAdministration
  Get-Website -Name ahpc
  Get-WebAppPool -Name ahpc
  Restart-Website -Name ahpc
  ```

---

## Nginx Configuration Details

When using `-WebServer Nginx`, the script:

1. **Installs Nginx** via Chocolatey
2. **Writes `nginx.conf`** with Laravel configuration
3. **Starts PHP-CGI** on port 9000
4. **Starts Nginx**

### Nginx Management

```powershell
# Check status
nginx -t

# Restart
nginx -s stop
Start-Sleep -Seconds 1
nginx

# View logs
Get-Content C:\nginx\logs\error.log -Tail 50
```

---

## Windows Task Scheduler

The script registers these scheduled tasks:

| Task Name | Trigger | Action |
|-----------|---------|--------|
| `ahpc-queue-worker` | At startup, restart on failure | `php artisan queue:work redis` |
| `ahpc-scheduler` | Every 1 minute | `php artisan schedule:run` |
| `ahpc-sync-pull` | Every N minutes (center only) | `php artisan sync:pull` |
| `ahpc-sync-push` | Every N minutes (center only) | `php artisan sync:push` |

### Managing Tasks

```powershell
# View all AHPC tasks
Get-ScheduledTask | Where-Object { $_.TaskName -like "ahpc*" }

# Start a task manually
Start-ScheduledTask -TaskName "ahpc-queue-worker"

# View task history
Get-ScheduledTaskInfo -TaskName "ahpc-queue-worker"

# Disable a task
Disable-ScheduledTask -TaskName "ahpc-sync-pull"

# Enable a task
Enable-ScheduledTask -TaskName "ahpc-sync-pull"
```

---

## Windows Firewall

The script opens these inbound ports:

| Port | Protocol | Purpose |
|------|----------|---------|
| 80 | TCP | HTTP |
| 443 | TCP | HTTPS |
| 22 | TCP | SSH (if OpenSSH installed) |
| 9000 | TCP | HAProxy stats (if LB used) |

### Managing Firewall Rules

```powershell
# View AHPC firewall rules
Get-NetFirewallRule | Where-Object { $_.DisplayName -like "AHPC*" }

# Add a custom rule
New-NetFirewallRule -DisplayName "AHPC Custom" -Direction Inbound -Protocol TCP -LocalPort 8081 -Action Allow

# Remove a rule
Remove-NetFirewallRule -DisplayName "AHPC SSH"
```

---

## Credentials File

After setup, all credentials are written to `C:\ahpc-credentials.txt`:

```
AHPC Central Server — Provisioning Summary (Windows)
Generated: 2026-11-13 23:30:00

Project path:     C:\inetpub\ahpc  (branch: ahpc)
Server IP:        192.168.1.100
Domain:           ahpc.internal
URL:              http://ahpc.internal
SSL:              No (HTTP only)
App Role:         central
Web Server:       IIS

Database name:    ahpc
Database user:    ahpc_user
Database pass:    xK9mP2nQ

Redis password:   rT7wL4jH

Default Admin:
  Email:          admin@ahpc.local
  Password:       aB3cD5eF

Ed25519 Keys:
  Private key:    <base64-private-key>
  Public key:     <base64-public-key>
```

---

## Post-Setup Verification

### 1. Check Services

```powershell
# MySQL
Get-Service MySQL

# Redis
Get-Service Redis

# IIS
Get-Service W3SVC
```

### 2. Check Scheduled Tasks

```powershell
Get-ScheduledTask | Where-Object { $_.TaskName -like "ahpc*" } | Format-Table TaskName, State
```

### 3. Test the Application

```powershell
# Health check
Invoke-WebRequest -Uri "http://127.0.0.1/" -UseBasicParsing

# Health endpoint
Invoke-WebRequest -Uri "http://127.0.0.1/up" -UseBasicParsing

# Load balancer health (Central only)
Invoke-WebRequest -Uri "http://127.0.0.1/lb-health" -UseBasicParsing
```

### 4. Check Logs

```powershell
# Laravel log
Get-Content "C:\inetpub\ahpc\storage\logs\laravel.log" -Tail 50

# Sync log (center only)
Get-Content "C:\inetpub\ahpc\storage\logs\ahpc-sync.log" -Tail 50

# IIS logs
Get-ChildItem "C:\inetpub\logs\LogFiles"

# Nginx logs (if using Nginx)
Get-Content "C:\nginx\logs\error.log" -Tail 50
```

### 5. Test Sync (Center Only)

```powershell
cd C:\inetpub\ahpc
php artisan sync:pull --force
php artisan sync:push --force
```

---

## Client Device Configuration

Every candidate PC that will access the site needs the domain to resolve to the server IP:

### Windows Clients
Edit `C:\Windows\System32\drivers\etc\hosts` (as Administrator):
```
192.168.1.100  ahpc.internal
```

### Alternative: DNS
Add a DNS A record on your network's DNS server:
```
ahpc.internal  →  192.168.1.100
```

---

## Troubleshooting

### "This script must be run as Administrator"
Right-click PowerShell → "Run as Administrator"

### "Chocolatey not found"
Install manually:
```powershell
Set-ExecutionPolicy Bypass -Scope Process -Force
[System.Net.ServicePointManager]::SecurityProtocol = 3072
Invoke-Expression ((New-Object System.Net.WebClient).DownloadString('https://community.chocolatey.org/install.ps1'))
refreshenv
```

### "MySQL service not running"
```powershell
Start-Service MySQL
Set-Service MySQL -StartupType Automatic
```

### "PHP extensions missing"
Edit `php.ini` (find path with `php --ini`):
- Uncomment the extension lines: `extension=mbstring`, `extension=redis`, etc.
- Restart IIS: `Restart-Service W3SVC`

### "IIS returns 500 Internal Server Error"
```powershell
# Check PHP FastCGI
Get-WebConfiguration "/system.webServer/fastCgi"

# Check Laravel log
Get-Content "C:\inetpub\ahpc\storage\logs\laravel.log" -Tail 30

# Clear Laravel caches
cd C:\inetpub\ahpc
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

### "Permission denied on storage/"
```powershell
# Grant IIS_IUSRS full control
icacls "C:\inetpub\ahpc\storage" /grant "IIS_IUSRS:(OI)(CI)F" /T
icacls "C:\inetpub\ahpc\bootstrap\cache" /grant "IIS_IUSRS:(OI)(CI)F" /T
icacls "C:\inetpub\ahpc\public\build" /grant "IIS_IUSRS:(OI)(CI)F" /T
```

### "Queue worker not running"
```powershell
# Check task status
Get-ScheduledTaskInfo -TaskName "ahpc-queue-worker"

# Start manually
Start-ScheduledTask -TaskName "ahpc-queue-worker"

# Run manually to see errors
cd C:\inetpub\ahpc
php artisan queue:work redis --tries=3
```

### "Sync not working (center)"
```powershell
# Test Central connectivity
Invoke-WebRequest -Uri "http://192.168.1.103/api/sync/v1/ping" -UseBasicParsing

# Check sync tasks
Get-ScheduledTaskInfo -TaskName "ahpc-sync-pull"
Get-ScheduledTaskInfo -TaskName "ahpc-sync-push"

# Run manually
cd C:\inetpub\ahpc
php artisan sync:pull --force
```

---

## Re-running the Script

The script is safe to re-run. It checks for existing state before acting:
- Existing passwords are reused from `C:\ahpc-credentials.txt`
- Existing database is not dropped
- Existing .env values are updated, not recreated
- Existing IIS site is updated, not recreated
- Existing scheduled tasks are updated

To re-run:
```powershell
.\ahpc-multiserver-setup.ps1 -AppRole central
```

---

## Uninstall / Cleanup

To remove the AHPC setup:

```powershell
# Stop and remove scheduled tasks
Unregister-ScheduledTask -TaskName "ahpc-queue-worker" -Confirm:$false
Unregister-ScheduledTask -TaskName "ahpc-scheduler" -Confirm:$false
Unregister-ScheduledTask -TaskName "ahpc-sync-pull" -Confirm:$false
Unregister-ScheduledTask -TaskName "ahpc-sync-push" -Confirm:$false

# Remove IIS site
Remove-Website -Name ahpc -Confirm:$false
Remove-WebAppPool -Name ahpc -Confirm:$false

# Remove firewall rules
Get-NetFirewallRule | Where-Object { $_.DisplayName -like "AHPC*" } | Remove-NetFirewallRule

# Remove project directory
Remove-Item "C:\inetpub\ahpc" -Recurse -Force

# Remove credentials file
Remove-Item "C:\ahpc-credentials.txt" -Force

# Uninstall packages (optional)
choco uninstall mysql redis-64 php nodejs-lts composer git 7zip -y
```

---

## Comparison: When to Use Which Script

| Scenario | Use |
|----------|-----|
| Ubuntu/Debian server, with HAProxy | `ahpc-multiserver-setup.sh` (bash) |
| Windows Server, no HAProxy needed | `ahpc-multiserver-setup.ps1` (PowerShell) |
| Windows Server Central, Linux centers with HAProxy | Both: `.ps1` for Central, `.sh` for centers |
| All-Windows environment, no load balancing | `ahpc-multiserver-setup.ps1` for all |
| Testing/development on Windows | `ahpc-multiserver-setup.ps1` |
| Production with Central-First load balancing | Linux centers with `.sh` for HAProxy support |

---

## Appendix: Full Example Commands

### Central Server (IIS, Self-Signed SSL)

```powershell
.\ahpc-multiserver-setup.ps1 `
  -AppRole central `
  -Domain "ahpc-central.company.local" `
  -Ip "192.168.1.100" `
  -WebServer IIS `
  -SelfSignedSsl `
  -AdminEmail "it@company.com" `
  -AdminPassword "SecurePass123!"
```

### Center Server (Nginx, Custom Intervals)

```powershell
.\ahpc-multiserver-setup.ps1 `
  -AppRole center `
  -Domain "ahpc-center1.company.local" `
  -Ip "192.168.1.101" `
  -CentralUrl "https://192.168.1.100" `
  -SyncToken "my-sync-token-abc123" `
  -PublicKey "base64publickeyhere..." `
  -WebServer Nginx `
  -PullInterval 10 `
  -PushInterval 10 `
  -AdminEmail "center1@company.com"
```

### Dry Run First

```powershell
# Preview what will happen
.\ahpc-multiserver-setup.ps1 -AppRole central -DryRun

# Then apply
.\ahpc-multiserver-setup.ps1 -AppRole central
```
