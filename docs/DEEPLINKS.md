# Deep links — Desert Backend

Registro abierto por **evento** (caso feliz) + código manual como Plan B.

## Flujo feliz

1. Admin genera el **short link** (grilla de eventos, editar evento, o invitaciones → “Link de registro”).
2. Se comparte `https://desert.rxstudio.dev/r/{code}` (8 chars `A-Z0-9`).
3. `GET /r/{code}` registra una visita y hace **302** a `/activar?feature=event_register&token=...`
4. App: `POST /api/deeplink/redeem` → recibe `event_id`
5. App: `POST /api/events/{id}/register` (datos + selfie) → invitación `pending`
6. Admin aprueba (1 a 1 o lote) → `confirmed` + stub OneSignal
7. Puerta: solo `confirmed` puede ingresar (`GET …/entry` y `POST /accesses`)

## Short links

| Campo | Detalle |
|-------|---------|
| URL pública | `/r/{code}` |
| Persistencia | `event_registration_links` (token HMAC + `short_code` + `expires_at`) |
| Activo | **1 link activo por evento**; regenerar pone `revoked_at` al anterior |
| Admin | `GET/POST /admin/events/{event}/registration-link` (JSON para el modal) |
| Métricas | `GET /admin/events/{event}/link-metrics` (desde invitaciones del evento) |

Hits en `registration_link_hits` (vistas + clicks a stores). IP solo como `ip_hash`.

Landing `/activar`: botones de tienda disparan `POST /activar/store-click` (`token` o `code` + `store`: `play` \| `app_store`).

## Token

```
v1.<base64url(payload)>.<base64url(hmac_sha256(payload_b64, secret))>
```

```json
{ "f": "event_register", "e": 1, "exp": 1767225600, "jti": "<uuid>" }
```

- `exp` = fin del día de `event.end_date`
- El link es **reutilizable** (muchas personas); redeem no quema el `jti`

### Redeem

```
POST /api/deeplink/redeem
{ "token": "v1....", "device_id": "..." }
```

200:

```json
{
  "valid": true,
  "feature": "event_register",
  "event_id": 1,
  "jti": "...",
  "expires_at": "..."
}
```

### Auto-registro

```
POST /api/events/{event}/register
multipart: first_name, last_name, document_number, id_type, selfie
```

201 → invitación `pending` con `code` y `selfie_url`.

## Config `.env`

```
DEEPLINK_HMAC_SECRET=
DEEPLINK_BASE_URL=https://desert.rxstudio.dev
DEEPLINK_PLAY_STORE_URL=...
DEEPLINK_APP_STORE_URL=...
ONESIGNAL_APP_ID=
ONESIGNAL_API_KEY=
```

CLI: `php artisan deeplink:generate {eventId}` → imprime el **short URL** (y regenera el activo).

## Plan B

`GET/POST /api/invitations/{code}` y `…/confirm` siguen disponibles (código manual).

## App (cambio mínimo)

1. `FeatureKey` / `KNOWN_FEATURES`: `event_register`
2. Tras redeem: leer `event_id` → onboarding self-register (sin código)
3. Guardar invitación local como `pending` hasta que admin apruebe
4. Refrescar status con `GET /invitations/{code}`

La app **no cambia** por los short links: sigue abriendo `/activar?token=...` después del 302.
