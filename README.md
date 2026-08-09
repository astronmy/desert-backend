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
  - Alta manual
  - Importación Excel/CSV (`Nombre`, `Apellido`, `DNI`)
  - Código único + estados: `pending` → `confirmed` / `cancelled`
- **API mobile** — consulta y confirmación de invitación (datos + selfie)

### Modelo de dominio

```
Event ──< Invitation >── Guest
```

- Un **Guest** es una persona (documento único).
- Una **Invitation** vincula guest + evento, con código, estado y selfie de confirmación.
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
| `/admin/events/{event}/invitations/import` | Importar Excel |

---

## API (mobile)

Base: `/api`  
Rate limit: 30 req/min

### Obtener invitación

```http
GET /api/invitations/{code}
```

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

### Confirmar invitación

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

El documento debe coincidir con el de la invitación. Al confirmar se actualiza el guest, se guarda la selfie y el estado pasa a `confirmed`.

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
  Models/          User, Event, Guest, Invitation
  Http/Controllers/
    Admin/         Users, Events, EventInvitations
    Api/           Invitation confirm
  Services/Invitations/
resources/views/
  admin/           dashboard, users, events, invitations
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
- Fuera de scope actual: auth Sanctum para la app, envío de códigos por WhatsApp/email, check-in en puerta.

---

## Licencia

Uso interno — Desert Eventos.
