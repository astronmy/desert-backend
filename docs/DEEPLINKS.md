# Deep links — Desert Backend

Registro abierto por **evento** (caso feliz) + código manual como Plan B.

## Flujo feliz

1. Admin genera el link en **Editar evento**.
2. El invitado abre `https://desert.rxstudio.dev/activar?feature=event_register&token=...`
3. App: `POST /api/deeplink/redeem` → recibe `event_id`
4. App: `POST /api/events/{id}/register` (datos + selfie) → invitación `pending`
5. Admin aprueba (1 a 1 o lote) → `confirmed` + stub OneSignal
6. Puerta: solo `confirmed` puede ingresar (`GET …/entry` y `POST /accesses`)

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

CLI: `php artisan deeplink:generate {eventId}`

## Plan B

`GET/POST /api/invitations/{code}` y `…/confirm` siguen disponibles (código manual).

## App (cambio mínimo)

1. `FeatureKey` / `KNOWN_FEATURES`: `event_register`
2. Tras redeem: leer `event_id` → onboarding self-register (sin código)
3. Guardar invitación local como `pending` hasta que admin apruebe
4. Refrescar status con `GET /invitations/{code}`
