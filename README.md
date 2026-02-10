# Mini Notes Service (Docker + PHP API + React UI)

This test project includes:
- REST API in PHP (CRUD + validation)
- React UI (create, edit, delete, list)
- Docker Compose startup in one command
- OpenAPI spec in `openapi.yaml`
- Bitrix module package in `backend/local/modules/alex.notes`
- API smoke tests (alternative to PHPUnit): `tests/smoke.sh`, `tests/smoke.ps1`

## Stack

- `nginx:1.27-alpine`
- `php:8.2-fpm-alpine`
- `mysql:8.0`
- `node:20-alpine` + Vite + React

## Quick Start

1. Create env file from template:
```bash
cp .env.example .env
cp frontend/.env.example frontend/.env
```

2. Run:
```bash
docker compose up -d --build
```

After startup:
- API: `http://localhost:8080/api/notes`
- Frontend: `http://localhost:5173`

What you will see:
- React page with note list and create/edit/delete form.
- Working REST API with JSON responses.

## API Endpoints

- `GET /api/notes` - list notes
- `GET /api/notes/{id}` - view note
- `POST /api/notes` - create note
- `PUT /api/notes/{id}` - update note
- `DELETE /api/notes/{id}` - delete note

## Request Examples

Create:
```bash
curl -X POST http://localhost:8080/api/notes \
  -H "Content-Type: application/json" \
  -d "{\"title\":\"First note\",\"content\":\"Hello\"}"
```

List:
```bash
curl http://localhost:8080/api/notes
```

Update:
```bash
curl -X PUT http://localhost:8080/api/notes/1 \
  -H "Content-Type: application/json" \
  -d "{\"title\":\"Updated\",\"content\":\"Updated text\"}"
```

Delete:
```bash
curl -X DELETE http://localhost:8080/api/notes/1
```

## Validation Rules

- `title`: required string, max 255 chars
- `content`: required string
- `id`: positive integer

Validation errors return `422`.
Not found returns `404`.

## OpenAPI

Specification file: `openapi.yaml`

## Tests (Alternative to PHPUnit)

Linux/macOS:
```bash
sh tests/smoke.sh
```

Windows PowerShell:
```powershell
powershell -ExecutionPolicy Bypass -File .\tests\smoke.ps1
```

For Bitrix VM endpoint:
```powershell
powershell -ExecutionPolicy Bypass -File .\tests\smoke.ps1 -BaseUrl "http://your-host/bitrix/tools/alex.notes/notes_api.php/api/notes"
```

## Bitrix Module (VM/Production)

Module path:
- `backend/local/modules/alex.notes`

How to install on Bitrix VM:
1. Copy `backend/local/modules/alex.notes` into `<bitrix_root>/local/modules/alex.notes`
2. Open Bitrix admin module list and install module `alex.notes`
3. Endpoint becomes available at:
   - `/bitrix/tools/alex.notes/notes_api.php/api/notes`
   - `/bitrix/tools/alex.notes/notes_api.php/api/notes/{id}`

Notes:
- Module creates table `alex_notes` on install.
- Module drops table `alex_notes` on uninstall.
- React app can target VM endpoint via env:
  - `VITE_API_BASE=http://your-host/bitrix/tools/alex.notes/notes_api.php/api/notes`

## Two Runtime Modes

1. Docker demo mode (for test task verification):
- API endpoint: `http://localhost:8080/api/notes`
- Runs standalone entrypoint: `backend/public/index.php`
- Purpose: one-command local launch for reviewer.

2. Bitrix module mode (for real Bitrix VM):
- API endpoint: `/bitrix/tools/alex.notes/notes_api.php/api/notes`
- Runs module code from `local/modules/alex.notes`
- Purpose: native Bitrix installation and usage.
