#!/usr/bin/env bash
set -euo pipefail

cd "$(dirname "$0")"

echo "==> ESCM Communication — production deploy"

if [[ ! -f .env ]]; then
  cp .env.example .env
  echo "Created .env from .env.example — edit APP_URL and mail settings before continuing."
  php artisan key:generate
fi

composer install --no-dev --optimize-autoloader --no-interaction

if [[ ! -f database/database.sqlite ]]; then
  touch database/database.sqlite
  echo "Created database/database.sqlite"
fi

php artisan migrate --force
php artisan storage:link --force 2>/dev/null || php artisan storage:link

if command -v npm >/dev/null 2>&1; then
  npm ci
  npm run build
else
  echo "WARNING: npm not found — run 'npm ci && npm run build' on a machine with Node, then deploy public/build/"
fi

php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache 2>/dev/null || true

echo ""
echo "Deploy OK."
echo "Create the first admin (if needed):"
echo "  php artisan escm:create-admin --name=\"Admin\" --email=\"admin@escm.mg\" --role=super_admin"
echo ""
echo "Ensure these are writable by the web server:"
echo "  database/database.sqlite  storage/  bootstrap/cache/"
echo "Document root must point to: public/"
