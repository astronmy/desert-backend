# Desert Backend

Panel de administración y API para **[Desert Eventos](https://deserteventos.com.ar/)** — gestión de eventos, invitados e invitaciones con confirmación desde la app mobile.

---

## Stack

| Capa | Tecnología |
|------|------------|
| Framework | Laravel 13 (PHP 8.3+) |
| UI admin | Blade + Livewire + Alpine + Tailwind CSS 4 |
| Excel | Maatwebsite Excel |
| Base de datos | MySQL |
| Locale | Español (`es`) |

---

## Funcionalidades

- **Auth** — login / logout (sin registro público)
- **Dashboard** — inicio post-login
- **Usuarios** — CRUD de administradores
- **Eventos** — CRUD con filtros (nombre, tipo, fechas)
- **Invitaciones** (por evento)
  - Alta manual, ver detalle (con selfie) y editar
  - Importación Excel/CSV (`Nombre`, `Apellido`, `DNI`)
  - Código único + estados: `pending` → `confirmed` / `cancelled`
- **Accesos** — registro de ingreso por QR (un solo acceso por invitación)
- **API mobile / scanner**
  - Consulta y confirmación de invitación (datos + selfie)
  - Consulta de entrada (`/entry`): foto + si ingresó y cuándo
  - Check-in de acceso por código (`POST /accesses`)

### Modelo de dominio

```
Event ──< Invitation >── Guest
              │
           Access (0..1)
```

- Un **Guest** es una persona (documento único).
- Una **Invitation** vincula guest + evento, con código, estado y selfie de confirmación.
- Un **Access** registra el ingreso (QR) de una invitación confirmada; máximo uno por invitación.
- El mismo guest puede estar invitado a varios eventos.

Tipos de evento: `wedding`, `birthday`, `graduation`, `corporate`, `private`.

---

## Requisitos

- PHP 8.3+
- Composer
- Node.js 20+ / npm
- MySQL

---

## Instalación

```bash
# 1. Dependencias
composer install
npm install

# 2. Entorno
cp .env.example .env
php artisan key:generate

# 3. Configurar DB en .env
# DB_DATABASE=desert_backend
# DB_USERNAME=root
# DB_PASSWORD=

# 4. Migrar, seed y storage
php artisan migrate --seed
php artisan storage:link

# 5. Assets
npm run build
```

### Desarrollo

```bash
composer run dev
```

Esto levanta servidor, queue, logs y Vite.  
También podés usar:

```bash
php artisan serve
npm run dev
```

App local: [http://localhost:8000](http://localhost:8000)

---

## Acceso admin

Tras el seed:

| Campo | Valor |
|-------|--------|
| Email | `admin@deserteventos.com.ar` |
| Password | `password` |

> Cambiá la contraseña en el entorno real.

Rutas principales:

| Ruta | Descripción |
|------|-------------|
| `/login` | Ingreso |
| `/admin/dashboard` | Dashboard |
| `/admin/users` | Usuarios |
| `/admin/events` | Eventos |
| `/admin/events/{event}/invitations` | Invitaciones del evento |
| `/admin/events/{event}/invitations/{invitation}` | Ver detalle (selfie) |
| `/admin/events/{event}/invitations/import` | Importar Excel |
| `/admin/events/{event}/accesses` | Accesos / ingresos del evento |

---

## API

Base: `/api`  
Sin autenticación por token (acceso por código de invitación).

| Método | Endpoint | Uso | Throttle |
|--------|----------|-----|----------|
| `GET` | `/api/invitations/{code}` | App: datos básicos de la invitación | 30/min |
| `POST` | `/api/invitations/{code}/confirm` | App: confirmar + selfie | 30/min |
| `GET` | `/api/invitations/{code}/entry` | Scanner: datos + foto + si ingresó | 60/min |
| `POST` | `/api/accesses` | Scanner: registrar ingreso (QR) | 60/min |

### 1. Obtener invitación

```http
GET /api/invitations/{code}
```

| Status | Significado |
|--------|-------------|
| `200` | OK |
| `404` | Código inexistente |
| `410` | Invitación cancelada |

Respuesta (ejemplo):

```json
{
  "code": "L6QXJO5F",
  "status": "pending",
  "event": {
    "id": 1,
    "name": "Casamiento Pérez",
    "init_date": "2026-09-12",
    "end_date": "2026-09-12",
    "type": "wedding"
  },
  "guest": {
    "first_name": "Juan",
    "last_name": "Pérez",
    "document_number": "30111222",
    "id_type": "dni"
  },
  "confirmed_at": null,
  "selfie_url": null
}
```

### 2. Confirmar invitación

Usado por la app mobile: el invitado carga/confirma datos y selfie.

```http
POST /api/invitations/{code}/confirm
Content-Type: multipart/form-data
```

| Campo | Tipo |
|-------|------|
| `first_name` | string |
| `last_name` | string |
| `document_number` | string |
| `id_type` | `dni` \| `passport` |
| `selfie` | image (max 5 MB) |

| Status | Significado |
|--------|-------------|
| `200` | Confirmada |
| `404` | Código inexistente |
| `409` | Ya estaba confirmada |
| `410` | Cancelada |
| `422` | Validación / documento no coincide |

El documento debe coincidir con el de la invitación. Al confirmar se actualiza el guest, se guarda la selfie y el estado pasa a `confirmed`.

### 3. Consultar invitación + acceso (scanner)

Para la puerta/scanner: datos del invitado, selfie y si ya ingresó (y cuándo).

```http
GET /api/invitations/{code}/entry
```

| Status | Significado |
|--------|-------------|
| `200` | OK |
| `404` | Código inexistente |
| `410` | Invitación cancelada |

Respuesta (ejemplo):

```json
{
  "code": "L6QXJO5F",
  "status": "confirmed",
  "event": {
    "id": 1,
    "name": "Casamiento Pérez",
    "init_date": "2026-09-12",
    "end_date": "2026-09-12",
    "type": "wedding"
  },
  "guest": {
    "first_name": "Juan",
    "last_name": "Pérez",
    "document_number": "30111222",
    "id_type": "dni"
  },
  "confirmed_at": "2026-08-09T18:00:00+00:00",
  "selfie_url": "http://localhost/storage/invitations/1/selfie.jpg",
  "access": {
    "has_entered": true,
    "accessed_at": "2026-08-09T21:00:00+00:00"
  }
}
```

Si aún no ingresó: `"has_entered": false` y `"accessed_at": null`.

### 4. Registrar acceso (check-in QR)

Usado por el scanner en puerta: lee el QR (código) y notifica el ingreso.

```http
POST /api/accesses
Content-Type: application/json
```

```json
{
  "code": "L6QXJO5F"
}
```

**Reglas**

- Solo invitaciones `confirmed`
- Un solo acceso por invitación (unique en DB)
- `pending` → rechazado; `cancelled` → rechazado

| Status | Significado |
|--------|-------------|
| `201` | Acceso registrado |
| `404` | Invitación no encontrada |
| `409` | Ya ingresó (`accessed_at` del primer acceso) |
| `410` | Invitación cancelada |
| `422` | Aún no confirmada |

Respuesta OK (ejemplo):

```json
{
  "message": "Acceso registrado correctamente.",
  "access": {
    "id": 1,
    "invitation_code": "L6QXJO5F",
    "accessed_at": "2026-08-09T21:00:00+00:00",
    "event": {
      "id": 1,
      "name": "Casamiento Pérez"
    },
    "guest": {
      "first_name": "Juan",
      "last_name": "Pérez",
      "document_number": "30111222",
      "id_type": "dni"
    }
  }
}
```

Flujo típico app + puerta:

```
App mobile          Backend              Scanner puerta
   |                   |                      |
   |-- GET invitation->|                      |
   |-- POST confirm -->|  (selfie + datos)     |
   |                   |                      |
   |                   |<-- GET .../entry ----|
   |                   |   (foto + ¿ingresó?) |
   |                   |<-- POST /accesses ---|
   |                   |   (código del QR)    |
```

---

## Import Excel

Columnas esperadas (headers en español o inglés):

| Nombre | Apellido | DNI |
|--------|----------|-----|
| Juan   | Pérez    | 30111222 |

Desde el admin podés **descargar una plantilla CSV**.  
Formatos: `.xlsx`, `.xls`, `.csv`.

Comportamiento:

1. Si el DNI no existe → crea Guest + Invitation (`pending`)
2. Si el DNI existe → reutiliza Guest y crea Invitation si falta
3. Si ya tiene invitación en ese evento → omite la fila

---

## Estructura relevante

```
app/
  Enums/           EventType, DocumentType, InvitationStatus
  Models/          User, Event, Guest, Invitation, Access
  Http/Controllers/
    Admin/         Users, Events, EventInvitations, EventAccesses
    Api/           InvitationController, AccessController
  Services/
    Invitations/   códigos + import Excel
    Accesses/      registro de ingreso QR
resources/views/
  admin/           dashboard, users, events, invitations, accesses
  layouts/         admin + guest (login)
routes/
  web.php, auth.php, admin.php, api.php
```

---

## Scripts útiles

```bash
php artisan migrate:fresh --seed   # reset DB + datos demo
php artisan db:seed                # solo seeders
npm run build                      # assets producción
./vendor/bin/pint                  # formato PHP
php artisan test                   # tests
```

---

## Notas

- Las selfies se guardan en `storage/app/public/invitations/...` (vía `storage:link`).
- Branding alineado a [deserteventos.com.ar](https://deserteventos.com.ar/) (teal / arena / dorado).
- Fuera de scope actual: auth Sanctum para la app, envío de códigos por WhatsApp/email.

---

## Licencia

Uso interno — Desert Eventos.
