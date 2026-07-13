# ESCM Communication

Application Laravel 10 de gestion du service communication ESCM.

## Stack

- Laravel 10
- PHP 8.1+
- SQLite
- Tailwind CSS
- Alpine.js
- ApexCharts
- Laravel Breeze (authentification)

## Installation (développement)

```bash
cd escm-communication
composer install
npm install
cp .env.example .env
# Mettre APP_ENV=local et APP_DEBUG=true dans .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
npm run build
php artisan serve
```

### Comptes de démonstration (local uniquement)

| Email | Mot de passe | Rôle |
|-------|--------------|------|
| matthieu@escm.mg | password | Responsable Communication |
| admin@escm.mg | password | Administrateur |

Le seeder est **désactivé en production**.

## Déploiement production (SQLite)

1. Copier le projet sur le serveur, document root = `public/`
2. Éditer `.env` (à partir de `.env.example`) :
   - `APP_ENV=production`
   - `APP_DEBUG=false`
   - `APP_URL=https://votre-domaine`
   - `DB_CONNECTION=sqlite`
   - `SESSION_SECURE_COOKIE=true`
3. Lancer le script :

```bash
# Linux / macOS
chmod +x deploy.sh && ./deploy.sh

# Windows (PowerShell)
.\deploy.ps1
```

4. Créer le premier administrateur (ne pas utiliser `db:seed`) :

```bash
php artisan escm:create-admin --name="Admin ESCM" --email="admin@escm.mg" --role=super_admin
```

5. Permissions web server : `database/database.sqlite`, `storage/`, `bootstrap/cache/`

### Sécurité prod

- Inscription publique (`/register`) désactivée
- Gestion `/users` réservée aux rôles `super_admin` et `administrateur`
- HTTPS forcé, cookies sécurisés, proxies de confiance
- Tokens API Sanctum : expiration 7 jours (configurable via `SANCTUM_TOKEN_EXPIRATION`)

## URLs principales

- `/login` — Connexion
- `/dashboard` — Tableau de bord
- `/users` — Gestion des utilisateurs (admins)

## Structure

- `app/Http/Controllers/` — Contrôleurs
- `app/Models/` — Modèles Eloquent
- `resources/views/layouts/app.blade.php` — Layout principal
- `database/seeders/DatabaseSeeder.php` — Données de démonstration (local)
- `deploy.sh` / `deploy.ps1` — Scripts de déploiement
