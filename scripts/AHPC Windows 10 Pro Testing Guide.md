# Testing the AHPC PowerShell Setup Script on Windows 10 Pro

**Date:** 13 November 2026  
**Script:** `ahpc-multiserver-setup.ps1`  
**Environment:** Windows 10 Pro with Laragon

---

## Overview

The PowerShell setup script (`ahpc-multiserver-setup.ps1`) was designed for **Windows Server 2019/2022/2025** with IIS. On Windows 10 Pro, there are two approaches:

1. **DryRun Mode** — Preview what the script would do without making any changes. This validates the script logic safely.
2. **App-Level Testing** — Test the Central-First load balancing features (health endpoints, attempt ownership, TrustProxies) using your existing Laragon environment.

> **Note:** The full script cannot run on Windows 10 Pro as-is because it uses Windows Server-only cmdlets (`Install-WindowsFeature`, `WebAdministration`). However, all the app-level code (health endpoints, migrations, controllers) works fine on Windows 10 Pro with Laragon.

---

## Prerequisites

### What You Already Have

| Component | Status | Location |
|-----------|--------|----------|
| PHP 8.3 | ✅ Installed | `C:\laragon\bin\php\php-8.3.36-Win32-vs16-x64` |
| MySQL 8.4 | ✅ Installed | `C:\laragon\bin\mysql\mysql-8.4.3-winx64` |
| Redis | ✅ Installed | `C:\laragon\bin\redis\redis-x64-5.0.14.1` |
| Node.js 22 | ✅ Installed | `C:\laragon\bin\nodejs\node-v22` |
| Git | ✅ Installed | `C:\laragon\bin\git` |
| Composer | ✅ Installed | `C:\laragon\bin\composer` |
| Laravel 12 | ✅ Installed | `C:\laragon\www\allacademies` |

### What You Need to Start

1. **Start Laragon** — Launch Laragon and click "Start All" to start Apache/Nginx, MySQL, and Redis.
2. **Open PowerShell as Administrator** — Right-click PowerShell → "Run as Administrator".

---

## Phase 1: Start Laragon Services

### Step 1.1: Start Laragon

1. Open Laragon from the Start menu or desktop shortcut.
2. Click **Start All** at the bottom.
3. Verify the services are running:

```powershell
# Check MySQL is listening
Test-NetConnection -ComputerName 127.0.0.1 -Port 3306

# Check Redis is listening
Test-NetConnection -ComputerName 127.0.0.1 -Port 6379

# Check web server (Apache default port 80)
Test-NetConnection -ComputerName 127.0.0.1 -Port 80
```

All three should return `TcpTestSucceeded : True`.

### Step 1.2: Verify Database Connection

```powershell
cd C:\laragon\www\allacademies

# Test Laravel can connect to the database
php artisan tinker --execute="echo DB::connection()->getPdo() ? 'DB connected' : 'DB failed';"
```

If you get "DB connected", proceed. If not, check your `.env` file:

```
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_app
DB_USERNAME=me
DB_PASSWORD=
```

---

## Phase 2: Run the Script in DryRun Mode

DryRun mode shows you exactly what the script would do without making any changes. This is the safest way to test the script on Windows 10 Pro.

### Step 2.1: Central Server DryRun

```powershell
cd C:\laragon\www\allacademies

.\ahpc-multiserver-setup.ps1 -AppRole central -DryRun
```

**What to look for:**
- The script should detect your IP address.
- It should list all the steps it would take (install Chocolatey, Git, MySQL, Redis, PHP, etc.).
- Since you already have these installed, it should show "already installed" messages.
- At the IIS step, it will show `[DRY-RUN] Install-WindowsFeature Web-Server, Web-Asp-Net45, ...` — this is the Windows Server-specific part that won't work on Windows 10 Pro.

### Step 2.2: Center Server DryRun

```powershell
.\ahpc-multiserver-setup.ps1 -AppRole center `
  -CentralUrl "http://192.168.1.100" `
  -SyncToken "test-token-123" `
  -PublicKey "dGVzdC1wdWJsaWMta2V5" `
  -DryRun
```

**What to look for:**
- Same as above, plus center-specific sync configuration.
- The `.env` section should show the sync-related variables it would write.

### Step 2.3: Expected Output

You should see output like:

```
==> ============================================================
  AHPC Multi-Server Setup — Windows Server Edition
  Role: central
  Computer: YOUR-PC-NAME
  Windows: Microsoft Windows 10 Pro
  ============================================================

==> DRY-RUN MODE — No changes will be made
==> ============================================================

[INFO]   Git already installed: git version 2.xx.x.windows.1
[INFO]   MySQL already installed
[DRY-RUN] CREATE DATABASE ahpc; CREATE USER 'ahpc_user'@'localhost'; GRANT ALL;
...
[DRY-RUN] Install-WindowsFeature Web-Server, Web-Asp-Net45, ...
```

---

## Phase 3: Test the App-Level Features

This is the most important phase. The Central-First load balancing features are all app-level code that works on any platform, including Windows 10 Pro with Laragon.

### Step 3.1: Run Migrations

The `server_origin` column needs to be added to the `general_exam_submissions` table.

```powershell
cd C:\laragon\www\allacademies

# Run pending migrations
php artisan migrate
```

Look for:
```
Migration table created successfully.
Migrating: 2026_11_12_000001_add_server_origin_to_general_exam_submissions_table
Migrated:  2026_11_12_000001_add_server_origin_to_general_exam_submissions_table (XX ms)
```

If you see "Nothing to migrate", the migration has already been run.

### Step 3.2: Start the Laravel Development Server

```powershell
# Start Laravel's built-in server (listens on all interfaces)
php artisan serve --host=0.0.0.0 --port=8000
```

Leave this running. Open a new PowerShell window for the next steps.

### Step 3.3: Test Health Endpoints

In a new PowerShell window:

```powershell
# Test the /up endpoint
curl http://localhost:8000/up

# Expected: HTTP 200 with JSON like {"status":"ok","database":"connected"}

# Test the /lb-health endpoint
curl http://localhost:8000/lb-health

# Expected: HTTP 200 with JSON like:
# {
#   "status": "ok",
#   "php_fpm_workers": null,
#   "php_fpm_busy": null,
#   "utilization_percent": 0,
#   "threshold_percent": 85,
#   "database_connected": true
# }
```

> **Note:** On Windows with `php artisan serve`, PHP-FPM metrics will be `null` because there's no PHP-FPM process. The endpoint still works — it just can't read PHP-FPM status. The database check will work.

### Step 3.4: Test TrustProxies Configuration

Check that TrustProxies is configured:

```powershell
# In the project directory
php artisan tinker
```

Then in Tinker:

```php
// Check the TrustProxies middleware
$config = config('center.trusted_proxy_ips');
dd($config);

// Check lb_mode
$lbMode = config('center.lb_mode');
dd($lbMode);
```

Expected output:
- `trusted_proxy_ips` → `[]` (empty array by default; populated when centers are registered)
- `lb_mode` → `'off'` (default)

Exit Tinker with `exit`.

### Step 3.5: Test Server Origin Column

```powershell
php artisan tinker
```

```php
// Check the server_origin column exists
use App\ExaminationHub\Models\GeneralExamSubmission;

// Check the table has the column
$schema = DB::getSchemaBuilder();
$columns = $schema->getColumnListing('general_exam_submissions');
dd(in_array('server_origin', $columns));
```

Expected: `true` (the column exists).

### Step 3.6: Test the ExamTakingController Server Origin Logic

The controller reads `X-Forwarded-Server` header to determine server origin. Test this:

```powershell
# Simulate a request from a "central" server (HAProxy would set this header)
curl -H "X-Forwarded-Server: central" http://localhost:8000/up

# Simulate a request from a "local" server
curl -H "X-Forwarded-Server: local" http://localhost:8000/up
```

The `/up` endpoint doesn't use the header, but this verifies the header passes through. The actual server origin tagging happens in `ExamTakingController@authenticate` when a new exam submission is created.

### Step 3.7: Test Artisan Commands

```powershell
# Test the load balancer status command
php artisan exam:lb:status

# Expected: Shows current lb_mode, health check results, etc.
# On Windows without HAProxy, it will show "HAProxy not detected" for stats

# Test the load balancer rollback command (dry run)
php artisan exam:lb:rollback --dry-run
```

---

## Phase 4: Test the Full Exam Flow

To test the complete Central-First flow, you need to simulate a candidate joining an exam.

### Step 4.1: Create a Test Exam

1. Log in to the admin panel at `http://localhost:8000`.
2. Create a test exam with at least one question.
3. Note the exam's public code or URL.

### Step 4.2: Join the Exam

1. Open an incognito/private browser window.
2. Navigate to the exam join URL.
3. Complete the authentication flow.
4. Check the database for the created submission:

```powershell
php artisan tinker
```

```php
use App\ExaminationHub\Models\GeneralExamSubmission;

$latest = GeneralExamSubmission::latest()->first();
dd($latest->server_origin, $latest->center_id);
```

Expected:
- `server_origin` → `'local'` (default when no `X-Forwarded-Server` header)
- `center_id` → The center ID from your `.env` or `null` if running as central

### Step 4.3: Simulate Central-Routed Request

To test what happens when HAProxy routes through Central:

```powershell
# If you have a tool that can set custom headers (Postman, curl, etc.)
# Make the authenticate request with X-Forwarded-Server: central

curl -X POST http://localhost:8000/exam/authenticate `
  -H "X-Forwarded-Server: central" `
  -H "Content-Type: application/json" `
  -d '{"exam_code":"YOUR_EXAM_CODE","candidate_id":"YOUR_CANDIDATE_ID"}'
```

Then check:

```php
$latest = GeneralExamSubmission::latest()->first();
dd($latest->server_origin);
```

Expected: `server_origin` → `'central'`

---

## Phase 5: Windows 10 Pro Script Adaptation

If you want to actually run the script (not just DryRun) on Windows 10 Pro, here are the changes needed:

### Issue 1: Windows Version Check

The script checks for Windows Server 2019+ (build 17763). Windows 10 Pro 1809+ also has build 17763+, so this check will pass. ✅ No change needed.

### Issue 2: IIS Installation

`Install-WindowsFeature` is a Windows Server-only cmdlet. On Windows 10 Pro, IIS is enabled via DISM:

```powershell
# Windows 10 Pro equivalent
Enable-WindowsOptionalFeature -Online -FeatureName IIS-WebServerRole -All
Enable-WindowsOptionalFeature -Online -FeatureName IIS-ASP -All
Enable-WindowsOptionalFeature -Online -FeatureName IIS-ASPNET -All
```

### Issue 3: WebAdministration Module

The `WebAdministration` PowerShell module is only available on Windows Server. On Windows 10 Pro with IIS enabled, you can use `appcmd.exe` instead:

```powershell
# Instead of New-Website
& "$env:SystemRoot\system32\inetsrv\appcmd.exe" add site /name:"ahpc" /bindings:"http/*:80:" /physicalPath:"C:\inetpub\ahpc\public"

# Instead of Add-WebConfiguration
& "$env:SystemRoot\system32\inetsrv\appcmd.exe" set config /section:handlers /+[name='PHP_FastCGI',path='*.php',verb='*',modules='FastCgiModule',scriptProcessor='C:\php\php-cgi.exe',resourceType='Either']
```

### Issue 4: Alternative — Use `php artisan serve`

The simplest approach for Windows 10 Pro testing is to skip IIS entirely and use Laravel's built-in server:

```powershell
php artisan serve --host=0.0.0.0 --port=80
```

This bypasses all the IIS/Nginx configuration steps.

### Recommended Approach for Windows 10 Pro

**Don't run the full script.** Instead:

1. Use **DryRun mode** to validate the script logic. ✅
2. Use your **existing Laragon setup** for the app. ✅
3. Test the **app-level features** (health endpoints, migrations, server origin) directly. ✅
4. For production Windows Server deployments, use the script as-is on the target Windows Server machines.

---

## Phase 6: Quick Start Checklist

Here's the fastest path to verify everything works:

```powershell
# 1. Start Laragon (GUI: click "Start All")

# 2. Verify services
Test-NetConnection -ComputerName 127.0.0.1 -Port 3306   # MySQL
Test-NetConnection -ComputerName 127.0.0.1 -Port 6379   # Redis

# 3. Run migrations
cd C:\laragon\www\allacademies
php artisan migrate

# 4. DryRun the script
.\ahpc-multiserver-setup.ps1 -AppRole central -DryRun

# 5. Start Laravel server
php artisan serve --host=0.0.0.0 --port=8000

# 6. Test health endpoints (new window)
curl http://localhost:8000/up
curl http://localhost:8000/lb-health

# 7. Test Artisan commands
php artisan exam:lb:status

# 8. Check server_origin column
php artisan tinker
>>> DB::select("SHOW COLUMNS FROM general_exam_submissions LIKE 'server_origin'")
```

---

## Phase 7: What You Can't Test on Windows 10 Pro

| Feature | Why It Can't Be Tested | Where to Test |
|---------|----------------------|---------------|
| HAProxy installation | HAProxy doesn't run on Windows | Linux center server |
| IIS configuration via script | `Install-WindowsFeature` is Server-only | Windows Server 2019+ |
| WebAdministration module | Server-only PowerShell module | Windows Server 2019+ |
| Windows Task Scheduler for queue | Works on Win10 but script uses Server patterns | Windows Server or manually |
| Full end-to-end Central-First flow | Requires HAProxy on center | Linux center + Windows/Linux Central |

---

## Troubleshooting

### MySQL Connection Refused

```powershell
# Check if Laragon MySQL is running
Get-Process -Name mysqld -ErrorAction SilentlyContinue

# If not running, start Laragon and click "Start All"
# Or start MySQL manually:
& "C:\laragon\bin\mysql\mysql-8.4.3-winx64\bin\mysqld.exe" --defaults-file="C:\laragon\bin\mysql\mysql-8.4.3-winx64\my.ini"
```

### Redis Connection Refused

```powershell
# Check if Redis is running
Get-Process -Name redis-server -ErrorAction SilentlyContinue

# Start Redis manually:
& "C:\laragon\bin\redis\redis-x64-5.0.14.1\redis-server.exe" "C:\laragon\bin\redis\redis-x64-5.0.14.1\redis.windows.conf"
```

### Script Execution Policy Error

```powershell
# Allow script execution for this session
Set-ExecutionPolicy Bypass -Scope Process -Force

# Then run the script
.\ahpc-multiserver-setup.ps1 -AppRole central -DryRun
```

### "Install-WindowsFeature" Not Recognized

This is expected on Windows 10 Pro. The cmdlet only exists on Windows Server. Use DryRun mode or skip to Phase 3 (app-level testing).

### Health Endpoints Return 500 Error

```powershell
# Check Laravel logs
Get-Content storage\logs\laravel.log -Tail 50

# Common cause: database not connected
php artisan config:clear
php artisan cache:clear
```

---

## Summary

| What to Test | How | Works on Win10 Pro? |
|-------------|-----|---------------------|
| Script logic validation | `-DryRun` mode | ✅ Yes |
| Health endpoints (`/up`, `/lb-health`) | `php artisan serve` + `curl` | ✅ Yes |
| `server_origin` migration | `php artisan migrate` | ✅ Yes |
| TrustProxies config | `php artisan tinker` | ✅ Yes |
| ExamTakingController server origin | Manual exam flow + DB check | ✅ Yes |
| Artisan commands (`exam:lb:status`) | `php artisan` | ✅ Yes |
| HAProxy provisioning | N/A | ❌ Linux only |
| IIS auto-configuration | N/A | ❌ Windows Server only |
| Full Central-First flow | Requires HAProxy | ❌ Needs Linux center |

**Bottom line:** Use DryRun mode + Laragon to test everything except the infrastructure provisioning (HAProxy, IIS). For production, deploy the script on actual Windows Server machines.
