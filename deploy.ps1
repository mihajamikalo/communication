# ESCM Communication — production deploy (PowerShell / Windows hosting)
$ErrorActionPreference = "Stop"
Set-Location $PSScriptRoot

Write-Host "==> ESCM Communication — production deploy"

if (-not (Test-Path .env)) {
    Copy-Item .env.example .env
    Write-Host "Created .env from .env.example — edit APP_URL and mail settings before continuing."
    php artisan key:generate
}

composer install --no-dev --optimize-autoloader --no-interaction

if (-not (Test-Path database\database.sqlite)) {
    New-Item -ItemType File -Path database\database.sqlite | Out-Null
    Write-Host "Created database/database.sqlite"
}

php artisan migrate --force
try { php artisan storage:link --force } catch { php artisan storage:link }

if (Get-Command npm -ErrorAction SilentlyContinue) {
    npm ci
    npm run build
} else {
    Write-Host "WARNING: npm not found — run 'npm ci && npm run build' then deploy public/build/"
}

php artisan config:cache
php artisan route:cache
php artisan view:cache
try { php artisan event:cache } catch {}

Write-Host ""
Write-Host "Deploy OK."
Write-Host "Create the first admin (if needed):"
Write-Host '  php artisan escm:create-admin --name="Admin" --email="admin@escm.mg" --role=super_admin'
Write-Host ""
Write-Host "Ensure these are writable by the web server:"
Write-Host "  database/database.sqlite  storage/  bootstrap/cache/"
Write-Host "Document root must point to: public/"
