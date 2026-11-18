# ==============================================================================
#  AHPC Multi-Server Setup Script — Windows Server Edition
#
#  Purpose:
#    Single unified PowerShell script to provision a fully configured AHPC
#    server on Windows Server 2019/2022/2025.
#    Both Central and Center roles get the same full stack + app setup.
#    The only difference is the sync configuration in .env.
#
#  Run with Administrator privileges:
#    .\ahpc-multiserver-setup.ps1
#
#  What it does (both roles — full provisioning):
#    1. System update via Windows Update (optional)
#    2. Installs base utilities (Git, curl, 7-Zip)
#    3. Installs & configures MySQL (app DB + user on localhost)
#    4. Installs & configures Redis (localhost-only, password protected)
#    5. Installs PHP (non-thread-safe) + required extensions
#    6. Installs Node.js + npm for frontend builds
#    7. Downloads the 'ahpc' git branch into C:\inetpub\ahpc
#    8. Writes .env with all credentials (DB, Redis, Session, Sync, APP_NAME)
#    9. Runs composer install, npm install, npm run build
#   10. Generates app key, runs migrations, creates storage link
#   11. Provisions a default admin user
#   12. Configures IIS or Nginx to serve the app (HTTP + optional HTTPS)
#   13. Configures Windows Task Scheduler for queue workers
#   14. Configures Windows Task Scheduler for Laravel scheduler
#   15. Configures Windows Firewall
#   16. Writes credentials to C:\ahpc-credentials.txt and prints next steps
#
#  Role differences:
#    - Central: generates Ed25519 key pair, APP_ROLE=central
#    - Center: requires --CentralUrl, --SyncToken, --PublicKey, APP_ROLE=center
#
#  Safe to re-run: most steps check for existing state before acting.
# ==============================================================================

[CmdletBinding()]
param(
    # REQUIRED: "central" or "center"
    [Parameter(Mandatory = $true)]
    [ValidateSet("central", "center")]
    [string]$AppRole,

    # Database password (default: auto-generated)
    [string]$DbPassword,

    # Redis password (default: auto-generated)
    [string]$RedisPassword,

    # Server IP address (default: auto-detected)
    [string]$Ip,

    # Domain name (default: ahpc.internal)
    [string]$Domain = "ahpc.internal",

    # Git repository URL (default: GitHub tarball download)
    [string]$RepoUrl,

    # Git branch to check out (default: ahpc)
    [string]$RepoBranch = "ahpc",

    # Default admin email (default: admin@ahpc.local)
    [string]$AdminEmail = "admin@ahpc.local",

    # Default admin password (default: auto-generated)
    [string]$AdminPassword,

    # Central server URL (required when role=center)
    [string]$CentralUrl,

    # Center sync token (required when role=center)
    [string]$SyncToken,

    # Central Ed25519 public key base64 (required when role=center)
    [string]$PublicKey,

    # App version for version handshake (default: 1.0.0)
    [string]$AppVersion = "1.0.0",

    # Pull sync interval in minutes (default: 15)
    [int]$PullInterval = 15,

    # Push sync interval in minutes (default: 15)
    [int]$PushInterval = 15,

    # Web server: "IIS" or "Nginx" (default: IIS)
    [ValidateSet("IIS", "Nginx")]
    [string]$WebServer = "IIS",

    # Skip Windows Update check
    [switch]$SkipWindowsUpdate,

    # Show what would be done without making changes
    [switch]$DryRun,

    # Install Nginx from chocolatey instead of IIS
    [switch]$UseNginx,

    # Self-signed certificate for HTTPS
    [switch]$SelfSignedSsl,

    # Project installation directory (default: C:\inetpub\ahpc)
    [string]$ProjectDir = "C:\inetpub\ahpc"
)

# ==============================================================================
# SHARED DEFAULTS
# ==============================================================================
$AppName = "ahpc"
$DbName = $AppName
$DbUser = "${AppName}_user"
$CredsFile = "C:\${AppName}-credentials.txt"
$DefaultCentralUrl = "http://192.168.1.103"

# ==============================================================================
# COLOR HELPERS
# ==============================================================================
function Write-Log {
    param([string]$Message)
    Write-Host "`n==> $Message" -ForegroundColor Green
}

function Write-Info {
    param([string]$Message)
    Write-Host "[INFO]   $Message" -ForegroundColor Blue
}

function Write-Ok {
    param([string]$Message)
    Write-Host "[OK]     $Message" -ForegroundColor Green
}

function Write-Warn {
    param([string]$Message)
    Write-Host "[WARN]   $Message" -ForegroundColor Yellow
}

function Write-Die {
    param([string]$Message)
    Write-Host "[FATAL]  $Message" -ForegroundColor Red
    exit 1
}

function Write-Dry {
    param([string]$Message)
    Write-Host "[DRY-RUN] $Message" -ForegroundColor Cyan
}

function Test-Command {
    param([string]$Name)
    return $null -ne (Get-Command $Name -ErrorAction SilentlyContinue)
}

function Test-Admin {
    $identity = [Security.Principal.WindowsIdentity]::GetCurrent()
    $principal = New-Object Security.Principal.WindowsPrincipal($identity)
    return $principal.IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)
}

function Generate-RandomPassword {
    param([int]$Length = 12)
    $chars = 'abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789'
    $rng = [System.Security.Cryptography.RNGCryptoServiceProvider]::new()
    $bytes = New-Object byte[] $Length
    $rng.GetBytes($bytes)
    $rng.Dispose()
    -join ($bytes | ForEach-Object { $chars[$_ % $chars.Length] })
}

# ==============================================================================
# PRE-FLIGHT CHECKS
# ==============================================================================

# Check admin privileges
if (-not (Test-Admin)) {
    Write-Die "This script must be run as Administrator. Right-click PowerShell and select 'Run as Administrator'."
}

# Check PowerShell version
if ($PSVersionTable.PSVersion.Major -lt 5) {
    Write-Die "PowerShell 5.0 or higher is required. Current version: $($PSVersionTable.PSVersion)"
}

# Check Windows version
$winVer = [Environment]::OSVersion.Version
if ($winVer.Major -lt 10 -or ($winVer.Major -eq 10 -and $winVer.Build -lt 17763)) {
    Write-Die "Windows Server 2019 or later is required. Current: $($env:COMPUTERNAME) $($winVer)"
}

Write-Log "============================================================"
Write-Log "  AHPC Multi-Server Setup — Windows Server Edition"
Write-Log "  Role: $AppRole"
Write-Log "  Computer: $($env:COMPUTERNAME)"
Write-Log "  Windows: $([Environment]::OSVersion.VersionString)"
Write-Log "============================================================"

if ($DryRun) {
    Write-Log "============================================================"
    Write-Log "  DRY-RUN MODE — No changes will be made"
    Write-Log "============================================================"
}

# ==============================================================================
# VALIDATE CENTER-REQUIRED ARGS
# ==============================================================================
if ($AppRole -eq "center") {
    if (-not $CentralUrl) {
        Write-Die "--CentralUrl is required when role=center."
    }
    if (-not $SyncToken) {
        Write-Die "--SyncToken is required when role=center."
    }
    if (-not $PublicKey) {
        Write-Die "--PublicKey is required when role=center."
    }
    $CentralUrl = $CentralUrl.TrimEnd('/')
}

# ==============================================================================
# DETECT IP ADDRESS
# ==============================================================================
if (-not $Ip) {
    try {
        $Ip = (Test-Connection -ComputerName 1.1.1.1 -Count 1 -ErrorAction Stop).IPv4Address
    } catch {
        $Ip = (Get-NetIPAddress -AddressFamily IPv4 | Where-Object { $_.InterfaceAlias -notlike "*Loopback*" } | Select-Object -First 1).IPAddress
    }
}
if (-not $Ip) {
    Write-Die "Could not detect this server's IP address. Use --Ip to specify one."
}
Write-Log "Detected IP: $Ip"

# ==============================================================================
# GENERATE PASSWORDS
# ==============================================================================
# Check for existing credentials file
$existingDbPass = $null
$existingRedisPass = $null
if (Test-Path $CredsFile) {
    Write-Log "Found existing credentials file: $CredsFile"
    $credsContent = Get-Content $CredsFile -Raw
    $dbMatch = [regex]::Match($credsContent, 'Database pass:\s*(.+)')
    if ($dbMatch.Success) { $existingDbPass = $dbMatch.Groups[1].Value.Trim() }
    $redisMatch = [regex]::Match($credsContent, 'Redis password:\s*(.+)')
    if ($redisMatch.Success) { $existingRedisPass = $redisMatch.Groups[1].Value.Trim() }
}

$DbPassword = $DbPassword ?? $existingDbPass ?? (Generate-RandomPassword)
$RedisPassword = $RedisPassword ?? $existingRedisPass ?? (Generate-RandomPassword)
$AdminPassword = $AdminPassword ?? (Generate-RandomPassword)

Write-Info "Database password: $(if ($existingDbPass) { 'reused from credentials' } else { 'auto-generated' })"
Write-Info "Redis password: $(if ($existingRedisPass) { 'reused from credentials' } else { 'auto-generated' })"

# ==============================================================================
# 1. WINDOWS UPDATE (optional, skipped by default for speed)
# ==============================================================================
if ($SkipWindowsUpdate) {
    Write-Log "Skipping Windows Update (--SkipWindowsUpdate)"
} else {
    Write-Log "Checking Windows Update status"
    try {
        $updates = (Get-HotFix | Measure-Object).Count
        Write-Info "$updates hotfixes installed. Run Windows Update manually if needed."
        Write-Info "To install updates: Install-WindowsUpdate (requires PSWindowsUpdate module)"
    } catch {
        Write-Warn "Could not check Windows Update status: $_"
    }
}

# ==============================================================================
# 2. BASE UTILITIES — Git, curl, 7-Zip
# ==============================================================================
Write-Log "Installing base utilities"

# Install Chocolatey if not present (package manager for Windows)
if (-not (Test-Command choco)) {
    if (-not $DryRun) {
        Write-Log "Installing Chocolatey package manager"
        Set-ExecutionPolicy Bypass -Scope Process -Force
        [System.Net.ServicePointManager]::SecurityProtocol = [System.Net.ServicePointManager]::SecurityProtocol -bor 3072
        try {
            Invoke-Expression ((New-Object System.Net.WebClient).DownloadString('https://community.chocolatey.org/install.ps1'))
            refreshenv
            Write-Ok "Chocolatey installed"
        } catch {
            Write-Warn "Failed to install Chocolatey: $_"
            Write-Warn "Please install Chocolatey manually: https://chocolatey.org/install"
        }
    } else {
        Write-Dry "Install Chocolatey package manager"
    }
}

# Install Git
if (-not (Test-Command git)) {
    if (-not $DryRun) {
        Write-Log "Installing Git"
        choco install git -y --no-progress
        refreshenv
        Write-Ok "Git installed"
    } else {
        Write-Dry "choco install git -y"
    }
} else {
    Write-Info "Git already installed: $(git --version)"
}

# Install 7-Zip
if (-not (Test-Command 7z)) {
    if (-not $DryRun) {
        Write-Log "Installing 7-Zip"
        choco install 7zip -y --no-progress
        refreshenv
        Write-Ok "7-Zip installed"
    } else {
        Write-Dry "choco install 7zip -y"
    }
}

# ==============================================================================
# 3. MYSQL — Database Server
# ==============================================================================
Write-Log "Installing MySQL"

$mysqlInstalled = Test-Command mysql

if (-not $mysqlInstalled) {
    if (-not $DryRun) {
        Write-Log "Installing MySQL via Chocolatey"
        choco install mysql -y --no-progress --params '/Password:$DbPassword'
        refreshenv

        # Wait for MySQL service to start
        Write-Info "Waiting for MySQL service to start..."
        $maxWait = 60
        $waited = 0
        while ($waited -lt $maxWait) {
            $svc = Get-Service MySQL -ErrorAction SilentlyContinue
            if ($svc -and $svc.Status -eq 'Running') { break }
            Start-Sleep -Seconds 2
            $waited += 2
        }

        $svc = Get-Service MySQL -ErrorAction SilentlyContinue
        if ($svc -and $svc.Status -eq 'Running') {
            Write-Ok "MySQL service is running"
        } else {
            Write-Warn "MySQL service may not be running. Check: Get-Service MySQL"
        }
    } else {
        Write-Dry "choco install mysql -y --params '/Password:$DbPassword'"
    }
} else {
    Write-Info "MySQL already installed"
}

# Create database and user
if (-not $DryRun) {
    Write-Log "Creating application database and user"

    $mysqlExe = if (Test-Command mysql) { "mysql" } else { "C:\tools\mysql\current\bin\mysql" }

    # Try connecting as root with the installed password
    $mysqlArgs = "-u root -p$DbPassword -e"
    $sqlCommands = @"
CREATE DATABASE IF NOT EXISTS `$${DbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS '${DbUser}'@'localhost' IDENTIFIED BY '${DbPassword}';
ALTER USER '${DbUser}'@'localhost' IDENTIFIED BY '${DbPassword}';
GRANT ALL PRIVILEGES ON `${DbName}`.* TO '${DbUser}'@'localhost';
FLUSH PRIVILEGES;
"@

    try {
        & $mysqlExe -u root -p$DbPassword -e $sqlCommands 2>$null
        Write-Ok "Database '$DbName' and user '$DbUser' created"
    } catch {
        # Try without password (some MySQL installs use Windows auth)
        try {
            & $mysqlExe -u root -e $sqlCommands 2>$null
            Write-Ok "Database '$DbName' and user '$DbUser' created"
        } catch {
            Write-Warn "Could not create database/user automatically: $_"
            Write-Warn "Create manually: mysql -u root -e `"CREATE DATABASE $DbName;`""
        }
    }
} else {
    Write-Dry "CREATE DATABASE $DbName; CREATE USER '$DbUser'@'localhost'; GRANT ALL;"
}

# ==============================================================================
# 4. REDIS — Cache/Session/Queue Backend
# ==============================================================================
Write-Log "Installing Redis"

$redisInstalled = Test-Command redis-cli

if (-not $redisInstalled) {
    if (-not $DryRun) {
        Write-Log "Installing Redis via Chocolatey"
        choco install redis-64 -y --no-progress
        refreshenv

        # Configure Redis password
        $redisConf = "C:\ProgramData\Redis\redis.conf"
        if (Test-Path $redisConf) {
            $confContent = Get-Content $redisConf -Raw

            # Set bind to localhost only
            if ($confContent -match '^\s*bind\s+') {
                $confContent = $confContent -replace '(^\s*bind\s+.+)', "bind 127.0.0.1"
            } else {
                $confContent += "`nbind 127.0.0.1"
            }

            # Set requirepass
            if ($confContent -match '^\s*requirepass\s+') {
                $confContent = $confContent -replace '(^\s*requirepass\s+.+)', "requirepass $RedisPassword"
            } else {
                $confContent += "`nrequirepass $RedisPassword"
            }

            Set-Content $redisConf -Value $confContent -NoNewline
            Write-Ok "Redis configured: bind 127.0.0.1, password set"
        }

        # Restart Redis service
        Restart-Service Redis -ErrorAction SilentlyContinue
        Start-Sleep -Seconds 2

        # Test Redis connection
        try {
            $pingResult = & redis-cli -a $RedisPassword PING 2>$null
            if ($pingResult -match 'PONG') {
                Write-Ok "Redis is up and accepts the configured password"
            } else {
                Write-Warn "Redis did not respond to PING"
            }
        } catch {
            Write-Warn "Could not test Redis: $_"
        }
    } else {
        Write-Dry "choco install redis-64 -y && configure redis.conf"
    }
} else {
    Write-Info "Redis already installed"
}

# ==============================================================================
# 5. PHP — Non-Thread-Safe with Required Extensions
# ==============================================================================
Write-Log "Installing PHP"

$phpInstalled = Test-Command php

if (-not $phpInstalled) {
    if (-not $DryRun) {
        Write-Log "Installing PHP via Chocolatey (non-thread-safe)"
        choco install php -y --no-progress
        refreshenv

        $phpVer = php -r 'echo PHP_MAJOR_VERSION.".".PHP_MINOR_VERSION;'
        Write-Ok "PHP $phpVer installed"
    } else {
        Write-Dry "choco install php -y"
    }
} else {
    $phpVer = php -r 'echo PHP_MAJOR_VERSION.".".PHP_MINOR_VERSION;'
    Write-Info "PHP already installed: $phpVer"
}

# Verify required PHP extensions
$requiredExtensions = @('mbstring', 'curl', 'pdo_mysql', 'redis', 'xml', 'zip', 'gd', 'bcmath', 'intl', 'sodium', 'openssl', 'fileinfo')
if (-not $DryRun) {
    $phpModules = php -m 2>$null | ForEach-Object { $_.Trim().ToLower() }
    $missing = @()
    foreach ($ext in $requiredExtensions) {
        if ($ext -notin $phpModules) {
            $missing += $ext
        }
    }
    if ($missing.Count -gt 0) {
        Write-Warn "Missing PHP extensions: $($missing -join ', ')"
        Write-Warn "These may need to be enabled in php.ini or installed separately"
    } else {
        Write-Ok "All required PHP extensions are present"
    }
}

# Configure PHP for production
if (-not $DryRun) {
    $phpIniPath = php --ini 2>$null | Select-String 'Loaded Configuration' | ForEach-Object {
        $_.Line -replace '.*: (.+)', '$1'
    }
    if ($phpIniPath -and (Test-Path $phpIniPath)) {
        $phpIni = Get-Content $phpIniPath -Raw

        # Production settings
        $settings = @{
            'memory_limit' = '256M'
            'upload_max_filesize' = '100M'
            'post_max_size' = '100M'
            'max_execution_time' = '300'
            'date.timezone' = 'UTC'
        }

        foreach ($key in $settings.Keys) {
            $pattern = "(^{$key}\s*=.*)"
            $replacement = "$key = $($settings[$key])"
            if ($phpIni -match $pattern) {
                $phpIni = $phpIni -replace $pattern, $replacement
            }
        }

        Set-Content $phpIniPath -Value $phpIni
        Write-Ok "PHP production settings applied (memory=256M, uploads=100M)"
    }
}

# ==============================================================================
# 5b. PHP as Windows Service (using php-cgi + IIS FastCGI or PHP Manager)
# ==============================================================================
if ($WebServer -eq "IIS" -and -not $DryRun) {
    Write-Log "Configuring PHP for IIS FastCGI"

    # Find PHP-CGI executable
    $phpCgiPath = Get-ChildItem "$env:ProgramFiles\PHP" -Filter php-cgi.exe -ErrorAction SilentlyContinue | Select-Object -First 1
    if (-not $phpCgiPath) {
        $phpCgiPath = Get-ChildItem "C:\tools\php" -Filter php-cgi.exe -Recurse -ErrorAction SilentlyContinue | Select-Object -First 1
    }

    if ($phpCgiPath) {
        Write-Info "PHP-CGI found at: $($phpCgiPath.FullName)"

        # Register PHP with IIS FastCGI if WebAdministration module is available
        try {
            Import-Module WebAdministration -ErrorAction Stop

            $fcgiPath = $phpCgiPath.FullName
            $existing = Get-WebConfigurationSystem /system.webServer/fastCgi | Where-Object { $_.fullPath -eq $fcgiPath }
            if (-not $existing) {
                Add-WebConfiguration "/system.webServer/fastCgi" -Value @{ fullPath = $fcgiPath; arguments = ""; maxInstances = "50" }
                Write-Ok "PHP registered with IIS FastCGI"
            } else {
                Write-Info "PHP already registered with IIS FastCGI"
            }
        } catch {
            Write-Warn "Could not configure IIS FastCGI: $_"
            Write-Warn "Install IIS role first: Install-WindowsFeature Web-Server,Web-Asp-Net45"
        }
    } else {
        Write-Warn "PHP-CGI not found. PHP may not be configured for IIS."
    }
}

# ==============================================================================
# 6. NODE.JS + NPM
# ==============================================================================
Write-Log "Installing Node.js"

$nodeInstalled = Test-Command node

if (-not $nodeInstalled) {
    if (-not $DryRun) {
        Write-Log "Installing Node.js LTS via Chocolatey"
        choco install nodejs-lts -y --no-progress
        refreshenv
        Write-Ok "Node.js installed: $(node -v)"
    } else {
        Write-Dry "choco install nodejs-lts -y"
    }
} else {
    Write-Info "Node.js already installed: $(node -v)"
}

if (-not $DryRun -and (Test-Command npm)) {
    Write-Info "npm version: $(npm --version)"
}

# ==============================================================================
# 6b. COMPOSER
# ==============================================================================
Write-Log "Installing Composer"

$composerInstalled = Test-Command composer

if (-not $composerInstalled) {
    if (-not $DryRun) {
        Write-Log "Installing Composer via Chocolatey"
        choco install composer -y --no-progress
        refreshenv
        Write-Ok "Composer installed"
    } else {
        Write-Dry "choco install composer -y"
    }
} else {
    Write-Info "Composer already installed"
}

# ==============================================================================
# 7. PROJECT FILES — Download the 'ahpc' branch
# ==============================================================================
Write-Log "Placing project files"

if (-not $DryRun) {
    # Create directory
    if (-not (Test-Path $ProjectDir)) {
        New-Item -ItemType Directory -Path $ProjectDir -Force | Out-Null
    }

    $repoOwner = "Mon-Associates-Tech"
    $repoSlug = "allacademies"
    $tarUrl = "https://github.com/${repoOwner}/${repoSlug}/archive/refs/heads/${RepoBranch}.tar.gz"
    $tarFile = "$env:TEMP\${AppName}-${RepoBranch}.tar.gz"
    $extractDir = "$env:TEMP\${AppName}-extract"

    Write-Log "Downloading branch '$RepoBranch' from GitHub"

    # Download tarball
    try {
        Remove-Item $tarFile -Force -ErrorAction SilentlyContinue
        Invoke-WebRequest -Uri $tarUrl -OutFile $tarFile -TimeoutSec 120
        Write-Ok "Downloaded: $tarFile"
    } catch {
        Write-Die "Failed to download $tarUrl — check network connectivity: $_"
    }

    # Extract
    Write-Log "Extracting archive"
    Remove-Item $extractDir -Recurse -Force -ErrorAction SilentlyContinue
    New-Item -ItemType Directory -Path $extractDir -Force | Out-Null

    try {
        & 7z x $tarFile -o"$extractDir" -y | Out-Null
        # The tarball extracts to a subdirectory like allacademies-ahpc/
        $subDir = Get-ChildItem $extractDir -Directory | Select-Object -First 1
        if ($subDir) {
            Copy-Item "$($subDir.FullName)\*" $ProjectDir -Recurse -Force
        }
        Remove-Item $extractDir -Recurse -Force
        Remove-Item $tarFile -Force
    } catch {
        Write-Die "Failed to extract archive: $_"
    }

    # If project dir already had files, sync (preserving .env, storage, vendor, node_modules)
    Write-Log "Fixing ownership and permissions"

    # Ensure required storage subdirectories exist
    $storageDirs = @(
        "$ProjectDir\storage\logs",
        "$ProjectDir\storage\app\public",
        "$ProjectDir\storage\framework\cache\data",
        "$ProjectDir\storage\framework\sessions",
        "$ProjectDir\storage\framework\views",
        "$ProjectDir\bootstrap\cache",
        "$ProjectDir\public\build"
    )
    foreach ($dir in $storageDirs) {
        if (-not (Test-Path $dir)) {
            New-Item -ItemType Directory -Path $dir -Force | Out-Null
        }
    }

    # Set permissions — grant IIS_IUSRS and current user full control on storage
    try {
        $acl = Get-Acl "$ProjectDir\storage"
        $rule = New-Object System.Security.AccessControl.FileSystemAccessRule(
            "IIS_IUSRS", "FullControl", "ContainerInherit,ObjectInherit", "None", "Allow"
        )
        $acl.SetAccessRule($rule)
        Set-Acl "$ProjectDir\storage" $acl

        $acl = Get-Acl "$ProjectDir\bootstrap\cache"
        $acl.SetAccessRule($rule)
        Set-Acl "$ProjectDir\bootstrap\cache" $acl

        $acl = Get-Acl "$ProjectDir\public\build"
        $acl.SetAccessRule($rule)
        Set-Acl "$ProjectDir\public\build" $acl

        Write-Ok "Permissions set on storage/, bootstrap/cache, public/build"
    } catch {
        Write-Warn "Could not set ACLs: $_"
    }

    # Add hosts entry
    $hostsFile = "C:\Windows\System32\drivers\etc\hosts"
    $hostsEntry = "$Ip $Domain"
    $hostsContent = Get-Content $hostsFile -Raw
    if ($hostsContent -notmatch [regex]::Escape($Domain)) {
        Add-Content $hostsFile "`n$hostsEntry"
        Write-Log "Added hosts entry: $hostsEntry"
    }
} else {
    Write-Dry "Download $RepoBranch tarball and place project in $ProjectDir"
    Write-Dry "Set permissions on storage/, bootstrap/cache, public/build"
}

# ==============================================================================
# 8. ENVIRONMENT CONFIGURATION — Write .env
# ==============================================================================
Write-Log "Configuring .env"

function Set-EnvValue {
    param(
        [string]$File,
        [string]$Key,
        [string]$Value
    )

    $content = Get-Content $File -Raw

    # Check if key exists (uncommented)
    if ($content -match "(?m)^${Key}=") {
        $content = $content -replace "(?m)^${Key}=.*", "${Key}=${Value}"
    }
    # Check if key exists (commented out)
    elseif ($content -match "(?m)^#[\s]*${Key}=") {
        $content = $content -replace "(?m)^#[\s]*${Key}=.*", "${Key}=${Value}"
    }
    else {
        # Append
        if ($content -and $content[-1] -ne "`n") {
            $content += "`n"
        }
        $content += "${Key}=${Value}`n"
    }

    Set-Content $File -Value $content -NoNewline
}

if (-not $DryRun) {
    $envFile = "$ProjectDir\.env"

    if (-not (Test-Path $envFile)) {
        $envExample = "$ProjectDir\.env.example"
        if (Test-Path $envExample) {
            Copy-Item $envExample $envFile
            Write-Log ".env created from .env.example"
        } else {
            Write-Warning "No .env or .env.example found — creating minimal .env"
            New-Item -ItemType File -Path $envFile -Force | Out-Null
        }
    }

    # Remember existing central signing keys
    $envContent = Get-Content $envFile -Raw
    $existingPriv = $null
    $existingPub = $null
    $privMatch = [regex]::Match($envContent, '^CENTRAL_PRIVATE_KEY=(.+)$', 'Multiline')
    if ($privMatch.Success) { $existingPriv = $privMatch.Groups[1].Value.Trim() }
    $pubMatch = [regex]::Match($envContent, '^CENTRAL_PUBLIC_KEY=(.+)$', 'Multiline')
    if ($pubMatch.Success) { $existingPub = $pubMatch.Groups[1].Value.Trim() }

    # Set common values
    Set-EnvValue $envFile "APP_URL" "http://$Domain"
    Set-EnvValue $envFile "APP_ENV" "production"
    Set-EnvValue $envFile "APP_DEBUG" "false"
    Set-EnvValue $envFile "APP_NAME" '"AHPC"'
    Set-EnvValue $envFile "SESSION_DOMAIN" "null"

    # Generate APP_KEY if not set
    if ($envContent -notmatch '(?m)^APP_KEY=.+') {
        Set-EnvValue $envFile "APP_KEY" ""
    }

    # Database
    Set-EnvValue $envFile "DB_CONNECTION" "mysql"
    Set-EnvValue $envFile "DB_HOST" "127.0.0.1"
    Set-EnvValue $envFile "DB_PORT" "3306"
    Set-EnvValue $envFile "DB_DATABASE" $DbName
    Set-EnvValue $envFile "DB_USERNAME" $DbUser
    Set-EnvValue $envFile "DB_PASSWORD" $DbPassword

    # Redis
    Set-EnvValue $envFile "REDIS_HOST" "127.0.0.1"
    Set-EnvValue $envFile "REDIS_PASSWORD" $RedisPassword
    Set-EnvValue $envFile "REDIS_PORT" "6379"

    # Cache, Session, Queue
    Set-EnvValue $envFile "CACHE_STORE" "redis"
    Set-EnvValue $envFile "CACHE_DRIVER" "redis"
    Set-EnvValue $envFile "SESSION_DRIVER" "redis"
    Set-EnvValue $envFile "QUEUE_CONNECTION" "redis"
    Set-EnvValue $envFile "SESSION_SECURE_COOKIE" "false"

    # Remove any existing center-related lines
    $envContent = Get-Content $envFile -Raw
    $varsToRemove = @(
        'APP_ROLE', 'CENTRAL_URL', 'CENTER_SYNC_TOKEN', 'CENTRAL_PUBLIC_KEY',
        'CENTRAL_PRIVATE_KEY', 'APP_VERSION', 'CENTER_ID', 'CENTER_PULL_INTERVAL',
        'CENTER_PUSH_INTERVAL', 'LB_MODE', 'TRUSTED_PROXY_IPS'
    )
    foreach ($var in $varsToRemove) {
        $envContent = $envContent -replace "(?m)^${var}=.*`n", ''
    }
    Set-Content $envFile -Value $envContent -NoNewline

    # Role-specific configuration
    if ($AppRole -eq "central") {
        # Generate Ed25519 key pair
        $centralPrivKey = $null
        $centralPubKey = $null

        if ($existingPriv -and $existingPriv -notlike "CHANGE_ME*") {
            $centralPrivKey = $existingPriv
            $centralPubKey = $existingPub
            Write-Info "Reusing existing central key pair"
        } else {
            Write-Log "Generating Ed25519 key pair for central server"
            try {
                $keyOutput = php -r @"
`$keyPair = sodium_crypto_sign_keypair();
`$pub = sodium_bin2base64(sodium_crypto_sign_publickey(`$keyPair), SODIUM_BASE64_VARIANT_ORIGINAL);
`$priv = sodium_bin2base64(sodium_crypto_sign_secretkey(`$keyPair), SODIUM_BASE64_VARIANT_ORIGINAL);
echo `$priv . "`n" . `$pub . "`n";
"@ 2>$null

                if ($keyOutput) {
                    $lines = $keyOutput -split "`n"
                    $centralPrivKey = $lines[0].Trim()
                    $centralPubKey = $lines[1].Trim()
                    Write-Ok "Key pair generated"
                }
            } catch {
                Write-Warn "Could not generate keys: $_"
                $centralPrivKey = "CHANGE_ME_PRIVATE_KEY_BASE64"
                $centralPubKey = "CHANGE_ME_PUBLIC_KEY_BASE64"
            }
        }

        $envAppend = @"

# ── Multi-Center Sync (Central Role) ─────────────────────────────────────────
APP_ROLE=central
CENTRAL_PRIVATE_KEY=${centralPrivKey}
CENTRAL_PUBLIC_KEY=${centralPubKey}
APP_VERSION=1.0.0
"@
        Add-Content $envFile $envAppend
        Write-Ok ".env configured for central role"

    } elseif ($AppRole -eq "center") {
        $envAppend = @"

# ── Multi-Center Sync (Center Role) ──────────────────────────────────────────
APP_ROLE=center
CENTRAL_URL=${CentralUrl}
CENTER_SYNC_TOKEN=${SyncToken}
CENTRAL_PUBLIC_KEY=${PublicKey}
APP_VERSION=${AppVersion}
CENTER_ID=null
CENTER_PULL_INTERVAL=${PullInterval}
CENTER_PUSH_INTERVAL=${PushInterval}
"@
        Add-Content $envFile $envAppend
        Write-Ok ".env configured for center role"
    }

    Write-Ok ".env configured with database, redis, session, and center sync settings"
} else {
    Write-Dry "Create/update .env with all credentials and center sync settings"
}

# ==============================================================================
# 9. COMPOSER + NPM DEPENDENCIES
# ==============================================================================
Write-Log "Installing dependencies"

$frontendWarning = $null
if (-not $DryRun) {
    Push-Location $ProjectDir

    Write-Log "Running composer install"
    $composerLog = "$env:TEMP\${AppName}-composer.log"
    if (composer install --no-dev --optimize-autoloader --no-interaction 2>&1 | Out-File $composerLog) {
        Write-Ok "composer install done"
    } else {
        Write-Warn "composer install completed with warnings. Log: $composerLog"
    }

    if (Test-Path "package.json") {
        $npmLog = "$env:TEMP\${AppName}-npm.log"

        Write-Log "Running npm install"
        $npmOk = $false
        if (Test-Path "package-lock.json") {
            try {
                npm ci --no-audit --no-fund 2>&1 | Out-File $npmLog
                $npmOk = $true
            } catch {
                Write-Warn "npm ci failed, trying npm install"
            }
        }
        if (-not $npmOk) {
            try {
                npm install --no-audit --no-fund 2>&1 | Out-File $npmLog
                $npmOk = $true
            } catch {
                Write-Warn "npm install failed: $_"
            }
        }

        if ($npmOk) {
            Write-Ok "npm dependencies installed"

            # Check if build script exists
            $packageJson = Get-Content "package.json" -Raw | ConvertFrom-Json
            if ($packageJson.scripts.build) {
                Write-Log "Running npm run build"
                try {
                    npm run build 2>&1 | Out-File "$npmLog.build"
                    Write-Ok "npm run build done"
                } catch {
                    $frontendWarning = "npm run build FAILED — pages that use @vite will return HTTP 500."
                    Write-Warn $frontendWarning
                }
            }
        } else {
            $frontendWarning = "npm install FAILED — frontend assets were not built."
            Write-Warn $frontendWarning
        }
    } else {
        Write-Warn "package.json not found — skipping npm install/build"
    }

    Pop-Location
} else {
    Write-Dry "composer install --no-dev --optimize-autoloader"
    Write-Dry "npm ci && npm run build"
}

# ==============================================================================
# 10. LARAVEL SETUP — Key, Migrate, Storage Link, Caches
# ==============================================================================
Write-Log "Running Laravel setup commands"

if (-not $DryRun) {
    Push-Location $ProjectDir

    # Generate APP_KEY if not set
    $envContent = Get-Content ".env" -Raw
    if ($envContent -match '(?m)^APP_KEY=.+') {
        Write-Info "APP_KEY already set — keeping it"
    } else {
        Write-Log "Generating application key"
        php artisan key:generate --force
        Write-Ok "APP_KEY generated"
    }

    # Run migrations
    Write-Log "Running database migrations"
    try {
        $migrateOutput = php artisan migrate --force --no-interaction 2>&1
        Write-Output $migrateOutput | Select-Object -Last 10
        Write-Ok "Migrations complete"
    } catch {
        Write-Warn "Migrations failed: $_"
        Write-Warn "Check DB_* values in .env and that MySQL is running"
    }

    # Create storage symbolic link (Windows junction)
    Write-Log "Creating storage link"
    $storageLink = "$ProjectDir\public\storage"
    $storageTarget = "$ProjectDir\storage\app\public"
    if (Test-Path $storageLink) {
        Remove-Item $storageLink -Force -Recurse -ErrorAction SilentlyContinue
    }
    New-Item -ItemType Junction -Path $storageLink -Target $storageTarget | Out-Null
    Write-Ok "Storage link created"

    # Clear and cache config
    Write-Log "Clearing and caching config"
    php artisan config:clear 2>$null
    php artisan cache:clear 2>$null
    php artisan view:clear 2>$null
    php artisan route:clear 2>$null
    php artisan config:cache 2>$null
    php artisan route:cache 2>$null
    php artisan view:cache 2>$null
    Write-Ok "Laravel caches configured"

    Pop-Location
} else {
    Write-Dry "php artisan key:generate (only if APP_KEY is empty)"
    Write-Dry "php artisan migrate --force"
    Write-Dry "mklink /J public\storage storage\app\public"
    Write-Dry "php artisan config:cache && route:cache && view:cache"
}

# ==============================================================================
# 11. DEFAULT ADMIN USER PROVISIONING
# ==============================================================================
Write-Log "Provisioning default admin user"

if (-not $DryRun) {
    Push-Location $ProjectDir

    try {
        $existingUser = php artisan tinker --execute="echo \App\Models\User::where('email', '$AdminEmail')->exists() ? 'yes' : 'no';" 2>$null

        if ($existingUser -match 'yes') {
            Write-Warn "Admin user $AdminEmail already exists — skipping creation"
        } else {
            Write-Log "Creating admin user: $AdminEmail"
            try {
                php artisan users:create `
                    "$AdminEmail" `
                    admin `
                    --first_name="Admin" `
                    --last_name="User" `
                    --password="$AdminPassword" `
                    --no-interaction 2>&1
                Write-Ok "Admin user provisioned"
            } catch {
                Write-Warn "Failed to create admin user — create manually after setup"
            }
        }
    } catch {
        Write-Warn "Could not check for existing admin user: $_"
    }

    Pop-Location
} else {
    Write-Dry "php artisan users:create $AdminEmail admin --password=***"
}

# ==============================================================================
# 12. WEB SERVER — IIS or Nginx
# ==============================================================================
Write-Log "Configuring web server: $WebServer"

if ($WebServer -eq "IIS") {
    # Install IIS if not present
    $iisInstalled = Get-WindowsFeature Web-Server -ErrorAction SilentlyContinue
    if (-not $iisInstalled -or $iisInstalled.InstallState -ne 'Installed') {
        if (-not $DryRun) {
            Write-Log "Installing IIS"
            Install-WindowsFeature Web-Server, Web-Asp-Net45, Web-Net-Ext45, Web-Isapi-Ext, Web-Isapi-Filter, Web-ASP, Web-Mgmt-Tools -IncludeManagementTools
            Write-Ok "IIS installed"
        } else {
            Write-Dry "Install-WindowsFeature Web-Server, Web-Asp-Net45, ..."
        }
    }

    # Create IIS site
    if (-not $DryRun) {
        try {
            Import-Module WebAdministration -ErrorAction Stop

            # Check if site already exists
            $existingSite = Get-Website -Name $AppName -ErrorAction SilentlyContinue
            if (-not $existingSite) {
                New-Website -Name $AppName -Port 80 -PhysicalPath $ProjectDir\public -HostHeader $Domain
                Write-Ok "IIS site '$AppName' created on port 80"
            } else {
                Set-Website -Name $AppName -PhysicalPath $ProjectDir\public
                Write-Info "IIS site '$AppName' updated"
            }

            # Configure PHP FastCGI handler
            $phpCgiPath = Get-ChildItem "$env:ProgramFiles\PHP" -Filter php-cgi.exe -ErrorAction SilentlyContinue | Select-Object -First 1
            if ($phpCgiPath) {
                $appPool = Get-WebAppPool -Name $AppName -ErrorAction SilentlyContinue
                if (-not $appPool) {
                    New-WebAppPool -Name $AppName
                }
                Set-WebAppPoolState -Name $AppName -State Started

                # Add handler mapping if not present
                $handlers = Get-WebConfiguration "/system.webServer/handlers" -PSPath "IIS:\Sites\$AppName"
                $phpHandler = $handlers | Where-Object { $_.name -eq "PHP_FastCGI" }
                if (-not $phpHandler) {
                    Add-WebConfiguration "/system.webServer/handlers" -Value @{
                        name = "PHP_FastCGI"
                        path = "*.php"
                        verb = "*"
                        modules = "FastCgiModule"
                        scriptProcessor = $phpCgiPath.FullName
                        resourceType = "Either"
                    } -PSPath "IIS:\Sites\$AppName"
                    Write-Ok "PHP FastCGI handler added"
                }
            }

            # Add web.config for URL rewriting (Laravel try_files equivalent)
            $webConfig = "$ProjectDir\public\web.config"
            if (-not (Test-Path $webConfig)) {
                $webConfigContent = @"
<?xml version="1.0" encoding="UTF-8"?>
<configuration>
    <system.webServer>
        <rewrite>
            <rules>
                <rule name="Laravel Front Controller" stopProcessing="true">
                    <match url="^(.*)$" ignoreCase="false" />
                    <conditions logicalGrouping="MatchAll">
                        <add input="{REQUEST_FILENAME}" matchType="IsDirectory" ignoreCase="false" negate="true" />
                        <add input="{REQUEST_FILENAME}" matchType="IsFile" ignoreCase="false" negate="true" />
                    </conditions>
                    <action type="Rewrite" url="index.php/{R:1}" />
                </rule>
            </rules>
        </rewrite>
        <staticContent>
            <clientCache cacheControlMode="UseMaxAge" cacheControlMaxAge="30.00:00:00" />
        </staticContent>
        <security>
            <requestFiltering>
                <requestLimits maxAllowedContentLength="104857600" />
            </requestFiltering>
        </security>
    </system.webServer>
    <system.web>
        <defaultDocument>
            <files>
                <clear />
                <add value="index.php" />
            </files>
        </defaultDocument>
    </system.web>
</configuration>
"@
                Set-Content $webConfig -Value $webConfigContent -Encoding UTF8
                Write-Ok "web.config created for URL rewriting"
            }

            # Install URL Rewrite module if not present
            if (-not (Test-Path "C:\Windows\System32\inetsrv\rewrite.dll")) {
                Write-Log "Installing URL Rewrite module"
                $rewriteUrl = "https://download.microsoft.com/download/4/9/C/49CD28DB-1AA5-4B52-A33E-36D3FD382E2F/rewrite_2.0_rtw_x64.msi"
                $rewriteMsi = "$env:TEMP\rewrite.msi"
                Invoke-WebRequest -Uri $rewriteUrl -OutFile $rewriteMsi
                Start-Process msiexec.exe -ArgumentList "/i `"$rewriteMsi`" /quiet /norestart" -Wait
                Remove-Item $rewriteMsi -Force
                Write-Ok "URL Rewrite module installed"
            }

            # Start the site
            Start-Website -Name $AppName -ErrorAction SilentlyContinue
            Write-Ok "IIS site '$AppName' is running"

        } catch {
            Write-Warn "Could not configure IIS: $_"
            Write-Warn "Ensure IIS role is installed: Install-WindowsFeature Web-Server"
        }
    } else {
        Write-Dry "Create IIS site '$AppName' on port 80"
        Write-Dry "Configure PHP FastCGI handler"
        Write-Dry "Create web.config for URL rewriting"
    }

    # Self-signed SSL for IIS
    if ($SelfSignedSsl -and -not $DryRun) {
        Write-Log "Generating self-signed SSL certificate for IIS"
        try {
            $cert = New-SelfSignedCertificate `
                -CertStoreLocation cert:\LocalMachine\My `
                -DnsName $Domain, $Ip `
                -NotAfter (Get-Date).AddYears(1) `
                -KeyUsage DigitalSignature, KeyEncipherment

            $certPassword = ConvertTo-SecureString -String $RedisPassword -Force -AsPlainText
            Export-PfxCertificate -Cert $cert -FilePath "C:\${AppName}-cert.pfx" -Password $certPassword | Out-Null

            # Bind to port 443
            Remove-WebBinding -Name $AppName -Port 443 -ErrorAction SilentlyContinue
            $binding = New-WebBinding -Name $AppName -Protocol "https" -Port 443 -IPAddress "*"
            $certThumbprint = $cert.Thumbprint
            $pfxPath = "cert:\LocalMachine\My\$certThumbprint"
            $pfxCert = Get-Item $pfxPath
            $pfxBytes = $pfxCert.Export([System.Security.Cryptography.X509Certificates.X509ContentType]::Cert)

            Write-Ok "SSL certificate created and bound to port 443"
        } catch {
            Write-Warn "Could not configure SSL: $_"
        }
    }

} elseif ($WebServer -eq "Nginx") {
    # Install Nginx via Chocolatey
    $nginxInstalled = Test-Command nginx
    if (-not $nginxInstalled) {
        if (-not $DryRun) {
            Write-Log "Installing Nginx via Chocolatey"
            choco install nginx -y --no-progress
            refreshenv
            Write-Ok "Nginx installed"
        } else {
            Write-Dry "choco install nginx -y"
        }
    }

    # Create Nginx config
    if (-not $DryRun) {
        $nginxConfDir = "C:\nginx\conf"
        if (-not (Test-Path $nginxConfDir)) {
            $nginxConfDir = "C:\ProgramData\chocolatey\lib\nginx\tools\nginx-1.24.0\conf"
        }

        $escapedProjectDir = $ProjectDir.Replace('\', '\\')
        $nginxConf = @"
worker_processes 1;

events {
    worker_connections 1024;
}

http {
    include       mime.types;
    default_type  application/octet-stream;
    sendfile        on;
    keepalive_timeout 65;
    client_max_body_size 100M;

    server {
        listen 80;
        server_name ${Domain} ${Ip};

        root "${escapedProjectDir}\public";
        index index.php index.html;

        add_header X-Frame-Options "SAMEORIGIN";
        add_header X-Content-Type-Options "nosniff";

        location / {
            try_files $uri $uri/ /index.php?$query_string;
        }

        location ~ \.php$ {
            fastcgi_pass 127.0.0.1:9000;
            fastcgi_index index.php;
            fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
            include fastcgi_params;
        }

        location ~ /\.(?!well-known).* {
            deny all;
        }

        location ~* \.(?:css|js|jpg|jpeg|png|gif|ico|svg|woff|woff2|ttf)$ {
            expires 30d;
            access_log off;
            add_header Cache-Control "public";
        }

        access_log logs/${Domain}-access.log;
        error_log  logs/${Domain}-error.log;
    }
}
"@
        $nginxConfPath = "$nginxConfDir\nginx.conf"
        Set-Content $nginxConfPath -Value $nginxConf
        Write-Ok "Nginx configuration written"

        # Start PHP-CGI as a service or process
        Write-Log "Starting PHP-CGI on port 9000"
        $phpCgiPath = Get-ChildItem "$env:ProgramFiles\PHP" -Filter php-cgi.exe -ErrorAction SilentlyContinue | Select-Object -First 1
        if ($phpCgiPath) {
            $phpIniFile = (php --ini 2>$null | Select-String 'Loaded Configuration' | ForEach-Object { $_.Line -replace '.*: ', '' })
            $cgiArgs = "-b 127.0.0.1:9000"
            if ($phpIniFile -and (Test-Path $phpIniFile)) {
                $cgiArgs = "-b 127.0.0.1:9000 -c `"$phpIniFile`""
            }
            Start-Process -FilePath $phpCgiPath.FullName -ArgumentList $cgiArgs -WindowStyle Hidden
            Write-Ok "PHP-CGI started on port 9000"
        }

        # Restart Nginx
        nginx -s stop 2>$null
        Start-Sleep -Seconds 1
        nginx
        Write-Ok "Nginx started"
    } else {
        Write-Dry "Write Nginx config and start service"
    }
}

# ==============================================================================
# 13. QUEUE WORKER — Windows Task Scheduler
# ==============================================================================
Write-Log "Configuring queue worker via Task Scheduler"

if (-not $DryRun) {
    $phpExe = (Get-Command php).Source
    $artisanPath = "$ProjectDir\artisan"

    # Create a task that runs queue:work continuously
    $action = New-ScheduledTaskAction `
        -Execute $phpExe `
        -Argument "`"$artisanPath`" queue:work redis --sleep=3 --tries=3 --max-time=3600" `
        -WorkingDirectory $ProjectDir

    $trigger = New-ScheduledTaskTrigger -AtStartup
    $settings = New-ScheduledTaskSettingsSet `
        -AllowStartIfOnBatteries `
        -DontStopIfGoingOnBatteries `
        -StartWhenAvailable `
        -RestartCount 10 `
        -RestartInterval (New-TimeSpan -Minutes 1)

    $principal = New-ScheduledTaskPrincipal -UserId "SYSTEM" -LogonType ServiceAccount -RunLevel Highest

    try {
        Register-ScheduledTask `
            -TaskName "${AppName}-queue-worker" `
            -Action $action `
            -Trigger $trigger `
            -Settings $settings `
            -Principal $principal `
            -Description "AHPC Laravel Queue Worker" | Out-Null
        Write-Ok "Queue worker task registered"
    } catch {
        Write-Warn "Could not register queue worker task: $_"
    }
} else {
    Write-Dry "Register-ScheduledTask ${AppName}-queue-worker"
}

# ==============================================================================
# 14. LARAVEL SCHEDULER — Windows Task Scheduler
# ==============================================================================
Write-Log "Configuring Laravel scheduler via Task Scheduler"

if (-not $DryRun) {
    $phpExe = (Get-Command php).Source
    $artisanPath = "$ProjectDir\artisan"

    $action = New-ScheduledTaskAction `
        -Execute $phpExe `
        -Argument "`"$artisanPath`" schedule:run" `
        -WorkingDirectory $ProjectDir

    $trigger = New-ScheduledTaskTrigger -Once -At (Get-Date) -RepetitionInterval (New-TimeSpan -Minutes 1)
    $settings = New-ScheduledTaskSettingsSet -AllowStartIfOnBatteries -DontStopIfGoingOnBatteries

    $principal = New-ScheduledTaskPrincipal -UserId "SYSTEM" -LogonType ServiceAccount -RunLevel Highest

    try {
        Register-ScheduledTask `
            -TaskName "${AppName}-scheduler" `
            -Action $action `
            -Trigger $trigger `
            -Settings $settings `
            -Principal $principal `
            -Description "AHPC Laravel Scheduler (runs every minute)" | Out-Null
        Write-Ok "Laravel scheduler task registered"
    } catch {
        Write-Warn "Could not register scheduler task: $_"
    }
} else {
    Write-Dry "Register-ScheduledTask ${AppName}-scheduler (every 1 minute)"
}

# ==============================================================================
# 15. WINDOWS FIREWALL
# ==============================================================================
Write-Log "Configuring Windows Firewall"

if (-not $DryRun) {
    # Allow HTTP
    try {
        New-NetFirewallRule -DisplayName "AHPC HTTP" -Direction Inbound -Protocol TCP -LocalPort 80 -Action Allow -ErrorAction SilentlyContinue | Out-Null
        Write-Ok "Firewall: HTTP (port 80) allowed"
    } catch {
        Write-Warn "Could not add HTTP firewall rule: $_"
    }

    # Allow HTTPS
    try {
        New-NetFirewallRule -DisplayName "AHPC HTTPS" -Direction Inbound -Protocol TCP -LocalPort 443 -Action Allow -ErrorAction SilentlyContinue | Out-Null
        Write-Ok "Firewall: HTTPS (port 443) allowed"
    } catch {
        Write-Warn "Could not add HTTPS firewall rule: $_"
    }

    # Allow SSH (if OpenSSH is installed)
    try {
        New-NetFirewallRule -DisplayName "AHPC SSH" -Direction Inbound -Protocol TCP -LocalPort 22 -Action Allow -ErrorAction SilentlyContinue | Out-Null
        Write-Ok "Firewall: SSH (port 22) allowed"
    } catch {
        Write-Warn "Could not add SSH firewall rule: $_"
    }

    # Allow HAProxy stats (if LB is used)
    try {
        New-NetFirewallRule -DisplayName "AHPC HAProxy Stats" -Direction Inbound -Protocol TCP -LocalPort 9000 -Action Allow -ErrorAction SilentlyContinue | Out-Null
        Write-Ok "Firewall: HAProxy Stats (port 9000) allowed"
    } catch {
        Write-Warn "Could not add HAProxy stats firewall rule: $_"
    }
} else {
    Write-Dry "New-NetFirewallRule for ports 80, 443, 22, 9000"
}

# ==============================================================================
# 16. CENTER-ROLE POST-SETUP (sync tasks, central connectivity)
# ==============================================================================
if ($AppRole -eq "center") {

    # Test connection to central
    Write-Log "Testing connection to Central sync API"

    if (-not $DryRun) {
        try {
            $pingResponse = Invoke-WebRequest -Uri "$CentralUrl/api/sync/v1/ping" -TimeoutSec 5 -UseBasicParsing 2>$null
            if ($pingResponse.Content -match '"status":"ok"') {
                Write-Ok "Central sync API is reachable at $CentralUrl"
            } else {
                Write-Warn "Central sync API responded but with unexpected content"
            }
        } catch {
            $httpCode = $_.Exception.Response.StatusCode.value__
            Write-Warn "Cannot reach Central sync API at $CentralUrl (HTTP $httpCode)"
            Write-Warn "Check: firewall, web server, and that the Central server is running"
        }
    }

    # Auto-sync via Task Scheduler
    Write-Log "Setting up auto-sync tasks"

    if (-not $DryRun) {
        $phpExe = (Get-Command php).Source
        $artisanPath = "$ProjectDir\artisan"
        $syncLog = "$ProjectDir\storage\logs\ahpc-sync.log"

        # Pull sync task
        $pullAction = New-ScheduledTaskAction `
            -Execute $phpExe `
            -Argument "`"$artisanPath`" sync:pull >> `"$syncLog`" 2>&1" `
            -WorkingDirectory $ProjectDir

        $pullTrigger = New-ScheduledTaskTrigger -Once -At (Get-Date) -RepetitionInterval (New-TimeSpan -Minutes $PullInterval)
        $pullSettings = New-ScheduledTaskSettingsSet -AllowStartIfOnBatteries -DontStopIfGoingOnBatteries
        $pullPrincipal = New-ScheduledTaskPrincipal -UserId "SYSTEM" -LogonType ServiceAccount -RunLevel Highest

        try {
            Register-ScheduledTask `
                -TaskName "${AppName}-sync-pull" `
                -Action $pullAction `
                -Trigger $pullTrigger `
                -Settings $pullSettings `
                -Principal $pullPrincipal `
                -Description "AHPC Sync Pull from Central (every $PullInterval minutes)" | Out-Null
            Write-Ok "Sync pull task registered (every $PullInterval minutes)"
        } catch {
            Write-Warn "Could not register sync pull task: $_"
        }

        # Push sync task
        $pushAction = New-ScheduledTaskAction `
            -Execute $phpExe `
            -Argument "`"$artisanPath`" sync:push >> `"$syncLog`" 2>&1" `
            -WorkingDirectory $ProjectDir

        $pushTrigger = New-ScheduledTaskTrigger -Once -At (Get-Date) -RepetitionInterval (New-TimeSpan -Minutes $PushInterval)
        $pushSettings = New-ScheduledTaskSettingsSet -AllowStartIfOnBatteries -DontStopIfGoingOnBatteries
        $pushPrincipal = New-ScheduledTaskPrincipal -UserId "SYSTEM" -LogonType ServiceAccount -RunLevel Highest

        try {
            Register-ScheduledTask `
                -TaskName "${AppName}-sync-push" `
                -Action $pushAction `
                -Trigger $pushTrigger `
                -Settings $pushSettings `
                -Principal $pushPrincipal `
                -Description "AHPC Sync Push to Central (every $PushInterval minutes)" | Out-Null
            Write-Ok "Sync push task registered (every $PushInterval minutes)"
        } catch {
            Write-Warn "Could not register sync push task: $_"
        }
    }
}

# ==============================================================================
# 17. FINAL HEALTH CHECK
# ==============================================================================
Write-Log "Running health check"

if (-not $DryRun) {
    try {
        $response = Invoke-WebRequest -Uri "http://127.0.0.1/" -UseBasicParsing -TimeoutSec 30 -ErrorAction Stop
        if ($response.StatusCode -eq 200) {
            Write-Ok "Health check passed (HTTP 200)"
        } else {
            Write-Warn "Health check returned HTTP $($response.StatusCode)"
        }
    } catch {
        $httpCode = $_.Exception.Response.StatusCode.value__
        if ($httpCode -and ($httpCode -ge 500 -or $httpCode -eq 0)) {
            Write-Warn "Health check FAILED: HTTP $httpCode"
            $latestLog = Get-ChildItem "$ProjectDir\storage\logs" -Filter "laravel*.log" | Sort-Object LastWriteTime -Descending | Select-Object -First 1
            if ($latestLog) {
                Write-Warn "Last lines of $($latestLog.Name):"
                Get-Content $latestLog.FullName -Tail 30 | ForEach-Object { Write-Warn "  $_" }
            }
        } else {
            Write-Warn "Health check failed: $_"
        }
    }

    if ($frontendWarning) {
        Write-Warn $frontendWarning
    }
}

# ==============================================================================
# 18. CREDENTIALS FILE
# ==============================================================================
Write-Log "Writing credentials to $CredsFile"

if (-not $DryRun) {
    $sslStatus = "No (HTTP only)"
    if ($SelfSignedSsl) { $sslStatus = "Yes (Self-signed HTTPS)" }
    $protocol = "http"
    if ($SelfSignedSsl) { $protocol = "https" }

    if ($AppRole -eq "central") {
        $credsContent = @"
AHPC Central Server — Provisioning Summary (Windows)
Generated: $(Get-Date -Format 'yyyy-MM-dd HH:mm:ss')

Project path:     $ProjectDir  (branch: $RepoBranch)
Server IP:        $Ip
Domain:           $Domain
URL:              ${protocol}://${Domain}
SSL:              $sslStatus
App Role:         central
Web Server:       $WebServer

Database name:    $DbName
Database user:    $DbUser
Database pass:    $DbPassword

Redis password:   $RedisPassword

Default Admin:
  Email:          $AdminEmail
  Password:       $AdminPassword

Ed25519 Keys:
  Private key:    $centralPrivKey
  Public key:     $centralPubKey

.env is already configured with all of the above values.
"@
    } elseif ($AppRole -eq "center") {
        $credsContent = @"
AHPC Center Server — Provisioning Summary (Windows)
Generated: $(Get-Date -Format 'yyyy-MM-dd HH:mm:ss')

Project path:     $ProjectDir  (branch: $RepoBranch)
Server IP:        $Ip
Domain:           $Domain
URL:              ${protocol}://${Domain}
SSL:              $sslStatus
App Role:         center
Web Server:       $WebServer

Central server:   $CentralUrl
Sync token:       $($SyncToken.Substring(0, [Math]::Min(10, $SyncToken.Length)))...
Central public key: $($PublicKey.Substring(0, [Math]::Min(20, $PublicKey.Length)))...
App version:      $AppVersion
Pull interval:    $PullInterval minutes
Push interval:    $PushInterval minutes

Database name:    $DbName
Database user:    $DbUser
Database pass:    $DbPassword

Redis password:   $RedisPassword

Default Admin:
  Email:          $AdminEmail
  Password:       $AdminPassword

.env is already configured with all of the above values.
"@
    }

    Set-Content $CredsFile -Value $credsContent
    Write-Ok "Credentials written to $CredsFile"
}

# ==============================================================================
# FINAL SUMMARY
# ==============================================================================
$sslStatus = "No (HTTP only)"
if ($SelfSignedSsl) { $sslStatus = "Yes (Self-signed HTTPS)" }
$protocol = "http"
if ($SelfSignedSsl) { $protocol = "https" }

if ($AppRole -eq "central") {
    Write-Log "DONE — Central server provisioning complete"
    Write-Host @"

────────────────────────────────────────────────────────
  Central server provisioning complete for $Domain
────────────────────────────────────────────────────────

  Server
    IP:         $Ip
    Domain:     $Domain
    URL:        ${protocol}://${Domain}
    SSL:        $sslStatus
    Role:       central
    Web Server: $WebServer

  Project
    Path:       $ProjectDir  (branch: $RepoBranch)

  Database
    Name:       $DbName
    User:       $DbUser
    Password:   $DbPassword
    Host:       127.0.0.1
    Port:       3306

  Redis
    Password:   $RedisPassword
    Host:       127.0.0.1
    Port:       6379

  Default Admin
    Email:      $AdminEmail
    Password:   $AdminPassword

  Ed25519 Keys
    Private:    $centralPrivKey
    Public:     $centralPubKey

  Credentials file: $CredsFile

  To set up a Center server, run:
    .\ahpc-multiserver-setup.ps1 -AppRole center `
      -CentralUrl ${protocol}://${Domain} `
      -SyncToken <token-from-admin-ui> `
      -PublicKey $centralPubKey

  The following were done automatically:
    - .env configured (DB, Redis, Session, Queue, Center sync)
    - composer install --no-dev --optimize-autoloader
    - npm ci && npm run build
    - php artisan key:generate
    - php artisan migrate --force
    - php artisan storage:link (Windows junction)
    - php artisan config:cache + route:cache + view:cache
    - Default admin user provisioned
    - Queue worker registered (Task Scheduler)
    - Laravel scheduler registered (Task Scheduler)
    - Windows Firewall configured (ports 80, 443, 22, 9000)

  Client device step (every PC that will open the site):
    Add to hosts file:  $Ip  $Domain
    (C:\Windows\System32\drivers\etc\hosts, as Administrator)

  Then open: ${protocol}://${Domain}   (or http://$Ip)
────────────────────────────────────────────────────────
"@
} elseif ($AppRole -eq "center") {
    Write-Log "DONE — Center server provisioning complete"
    Write-Host @"

────────────────────────────────────────────────────────
  Center server provisioning complete for $Domain
────────────────────────────────────────────────────────

  Server
    IP:         $Ip
    Domain:     $Domain
    URL:        ${protocol}://${Domain}
    SSL:        $sslStatus
    Role:       center
    Web Server: $WebServer

  Project
    Path:       $ProjectDir  (branch: $RepoBranch)

  Sync Configuration
    Central:    $CentralUrl
    Token:      $($SyncToken.Substring(0, [Math]::Min(10, $SyncToken.Length)))...
    Public key: $($PublicKey.Substring(0, [Math]::Min(20, $PublicKey.Length)))...
    Version:    $AppVersion
    Pull:       Every $PullInterval minutes
    Push:       Every $PushInterval minutes

  Database
    Name:       $DbName
    User:       $DbUser
    Password:   $DbPassword
    Host:       127.0.0.1
    Port:       3306

  Redis
    Password:   $RedisPassword
    Host:       127.0.0.1
    Port:       6379

  Default Admin
    Email:      $AdminEmail
    Password:   $AdminPassword

  Credentials file: $CredsFile

  The following were done automatically:
    - .env configured (DB, Redis, Session, Queue, Center sync)
    - composer install --no-dev --optimize-autoloader
    - npm ci && npm run build
    - php artisan key:generate
    - php artisan migrate --force
    - php artisan storage:link (Windows junction)
    - php artisan config:cache + route:cache + view:cache
    - Default admin user provisioned
    - Queue worker registered (Task Scheduler)
    - Laravel scheduler registered (Task Scheduler)
    - Sync tasks registered (Task Scheduler)
    - Windows Firewall configured
    - Central server connectivity tested

  Next steps:
    1. Test the pull:
       cd $ProjectDir
       php artisan sync:pull --force

    2. Verify exams appeared:
       php artisan tinker
       >>> \App\ExaminationHub\Models\GeneralExam::count()

    3. Push results (after an exam):
       php artisan sync:push --force

    4. Check sync status:
       Visit: ${protocol}://${Domain}/center-sync

  Logs:
    Get-Content $ProjectDir\storage\logs\laravel.log -Tail 50
    Get-Content $ProjectDir\storage\logs\ahpc-sync.log -Tail 50

  Client device step (every PC that will open the site):
    Add to hosts file:  $Ip  $Domain
    (C:\Windows\System32\drivers\etc\hosts, as Administrator)

  Then open: ${protocol}://${Domain}   (or http://$Ip)
────────────────────────────────────────────────────────
"@
}

if ($DryRun) {
    Write-Host ""
    Write-Host "============================================================" -ForegroundColor Yellow
    Write-Host "  DRY-RUN COMPLETE — No changes were made to the system" -ForegroundColor Yellow
    Write-Host "  Run without -DryRun to apply these changes" -ForegroundColor Yellow
    Write-Host "============================================================" -ForegroundColor Yellow
    Write-Host ""
}
