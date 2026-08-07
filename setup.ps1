# Suppress progress bar for faster downloads
$ProgressPreference = 'SilentlyContinue'

$PSScriptRoot = Get-Location

Write-Host "=============================================" -ForegroundColor Green
Write-Host "      Inisialisasi POS Lapaknita Portable    " -ForegroundColor Green
Write-Host "=============================================" -ForegroundColor Green
Write-Host ""

# 1. Download & Setup Portable PHP
$phpDir = Join-Path $PSScriptRoot "php-bin"
if (-not (Test-Path $phpDir)) {
    Write-Host "Mengunduh PHP 8.2 Portable..." -ForegroundColor Cyan
    $phpZipUrl = "https://windows.php.net/downloads/releases/archives/php-8.2.12-nts-Win32-vs16-x64.zip"
    $phpZipPath = Join-Path $PSScriptRoot "php.zip"
    
    try {
        Invoke-WebRequest -Uri $phpZipUrl -OutFile $phpZipPath -TimeoutSec 300
        Write-Host "Mengekstrak PHP..." -ForegroundColor Cyan
        Expand-Archive -Path $phpZipPath -DestinationPath $phpDir -Force
        Remove-Item -Path $phpZipPath -Force
    } catch {
        Write-Host "Gagal mengunduh PHP. Pastikan koneksi internet aktif." -ForegroundColor Red
        Write-Host $_.Exception.Message -ForegroundColor Red
        Exit
    }

    # Setup php.ini
    Write-Host "Mengonfigurasi php.ini..." -ForegroundColor Cyan
    $iniDev = Join-Path $phpDir "php.ini-development"
    $iniPath = Join-Path $phpDir "php.ini"
    if (Test-Path $iniDev) {
        Copy-Item -Path $iniDev -Destination $iniPath -Force
        # Append required extensions to the end of php.ini
        $extConfig = @"

extension_dir = "ext"
extension=curl
extension=fileinfo
extension=mbstring
extension=openssl
extension=pdo_sqlite
extension=sqlite3
extension=zip
"@
        Add-Content -Path $iniPath -Value $extConfig
    }
} else {
    Write-Host "PHP Portable sudah terpasang." -ForegroundColor Green
}

# 2. Download Composer
$composerPath = Join-Path $PSScriptRoot "composer.phar"
if (-not (Test-Path $composerPath)) {
    Write-Host "Mengunduh Composer..." -ForegroundColor Cyan
    $composerUrl = "https://getcomposer.org/composer.phar"
    try {
        Invoke-WebRequest -Uri $composerUrl -OutFile $composerPath -TimeoutSec 120
    } catch {
        Write-Host "Gagal mengunduh Composer." -ForegroundColor Red
        Write-Host $_.Exception.Message -ForegroundColor Red
        Exit
    }
} else {
    Write-Host "Composer sudah siap." -ForegroundColor Green
}

# 3. Environment & Configuration (.env)
$envPath = Join-Path $PSScriptRoot ".env"
$envExPath = Join-Path $PSScriptRoot ".env.example"
if (-not (Test-Path $envPath)) {
    if (Test-Path $envExPath) {
        Write-Host "Membuat file konfigurasi .env..." -ForegroundColor Cyan
        Copy-Item -Path $envExPath -Destination $envPath -Force
    }
}

# 4. Database Setup
$dbPath = Join-Path $PSScriptRoot "database\database.sqlite"
if (-not (Test-Path $dbPath)) {
    Write-Host "Membuat database SQLite..." -ForegroundColor Cyan
    $null = New-Item -Path $dbPath -ItemType File -Force
}

# 5. Organisasi Direktori Gambar Produk
$publicStoragePath = Join-Path $PSScriptRoot "public\storage"
$targetStoragePath = Join-Path $PSScriptRoot "storage\app\public"
$productsSource = Join-Path $publicStoragePath "products"
$productsDest = Join-Path $targetStoragePath "products"

if (Test-Path $publicStoragePath) {
    $item = Get-Item $publicStoragePath
    $isLink = $item.Attributes -match "ReparsePoint"
    if (-not $isLink) {
        Write-Host "Mengatur ulang direktori gambar produk..." -ForegroundColor Cyan
        if (-not (Test-Path $productsDest)) {
            $null = New-Item -ItemType Directory -Force -Path $productsDest
        }
        if (Test-Path $productsSource) {
            Copy-Item -Path "$productsSource\*" -Destination $productsDest -Recurse -Force -ErrorAction SilentlyContinue
        }
        Remove-Item -Path $publicStoragePath -Recurse -Force -ErrorAction SilentlyContinue
    }
}

# 6. Jalankan Instalasi Depedensi Laravel
Write-Host "Menginstal dependensi PHP (ini membutuhkan waktu beberapa menit)..." -ForegroundColor Cyan
& ".\php-bin\php.exe" "composer.phar" install --no-interaction --prefer-dist

# 7. Generate App Key
Write-Host "Mengenerate application key..." -ForegroundColor Cyan
& ".\php-bin\php.exe" artisan key:generate

# 8. Storage Link
$isLinkNow = $false
if (Test-Path $publicStoragePath) {
    $item = Get-Item $publicStoragePath
    $isLinkNow = $item.Attributes -match "ReparsePoint"
}
if (-not $isLinkNow) {
    Write-Host "Membuat storage link..." -ForegroundColor Cyan
    & ".\php-bin\php.exe" artisan storage:link
}

# 9. Database Migration & Seeding
Write-Host "Menjalankan migrasi dan seeding database..." -ForegroundColor Cyan
& ".\php-bin\php.exe" artisan migrate:fresh --seed --force

Write-Host ""
Write-Host "=============================================" -ForegroundColor Green
Write-Host "   Instalasi Selesai! Menjalankan Server...  " -ForegroundColor Green
Write-Host "=============================================" -ForegroundColor Green
Write-Host "Silakan akses POS di browser: http://127.0.0.1:8000" -ForegroundColor Yellow
Write-Host "Tekan Ctrl+C di jendela ini jika ingin mematikan program." -ForegroundColor Red
Write-Host ""

# Buka browser otomatis
Start-Process "http://127.0.0.1:8000"

# Jalankan server
& ".\php-bin\php.exe" artisan serve --port=8000
