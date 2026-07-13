# Mobile API (NativePHP client)

The Android/iOS app lives in the sibling repo `../escm-mobile`.

## Endpoints (`auth:sanctum` except login)

| Method | Path | Description |
|--------|------|-------------|
| POST | `/api/login` | `{ email, password, device_name? }` → `{ token, user }` |
| POST | `/api/logout` | Revoke current token |
| GET | `/api/user` | Current user |
| GET | `/api/dashboard` | KPIs + alertes |
| GET | `/api/projet/board` | Kanban listes + cartes |
| POST | `/api/projet/cartes` | Create card |
| GET/PATCH | `/api/projet/cartes/{id}` | Card detail / update |
| POST | `/api/projet/move` | Drag-and-drop reorder |
| POST | `/api/projet/cartes/{id}/commentaires` | Add comment |

Use `Authorization: Bearer {token}`.

CORS already allows `api/*`. Serve with `php artisan serve --host=0.0.0.0 --port=8000` for emulator/LAN access.
