# ==============================================================================
# Script Import Database eLabora ke MySQL Lokal
# ==============================================================================

$sqlFile = Join-Path $PSScriptRoot "..\database\database-elabora.sql"
if (-not (Test-Path $sqlFile)) {
    Write-Error "File SQL tidak ditemukan di: $sqlFile"
    exit 1
}

# Cari lokasi binary mysql.exe
$mysqlExe = Get-Command mysql -ErrorAction SilentlyContinue | Select-Object -ExpandProperty Source
if (-not $mysqlExe) {
    if (Test-Path "C:\xampp\mysql\bin\mysql.exe") {
        $mysqlExe = "C:\xampp\mysql\bin\mysql.exe"
    } elseif (Test-Path "C:\laragon\bin\mysql") {
        $laragonMysql = Get-ChildItem "C:\laragon\bin\mysql\*\bin\mysql.exe" -ErrorAction SilentlyContinue | Select-Object -First 1
        if ($laragonMysql) {
            $mysqlExe = $laragonMysql.FullName
        }
    }
}

if (-not $mysqlExe) {
    Write-Error "mysql.exe tidak ditemukan di PATH, XAMPP, atau Laragon. Pastikan MySQL terpasang."
    exit 1
}

Write-Host "Menggunakan MySQL binary di: $mysqlExe" -ForegroundColor Cyan
Write-Host "Mengimpor database dari: $sqlFile ..." -ForegroundColor Yellow

$dbUser = "root"
$dbPass = ""

# Eksekusi import
Get-Content -Path $sqlFile -Encoding UTF8 | & $mysqlExe -u $dbUser

if ($LASTEXITCODE -eq 0) {
    Write-Host "Database 'elabora_db' berhasil diimpor dengan sukses!" -ForegroundColor Green
} else {
    Write-Host "Import selesai dengan kode: $LASTEXITCODE. Periksa apakah MySQL service sudah aktif." -ForegroundColor Yellow
}
