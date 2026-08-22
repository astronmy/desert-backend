# Deep links — Desert Backend

Hosting y canje de links de registro de invitaciones para la app Desert Eventos (`desert.rxstudio.dev`).

## Qué hace

1. Sirve App Links / Universal Links (`.well-known`).
2. Landing pública en `/activar` (sin redirect a la store).
3. `POST /api/deeplink/redeem` valida el token HMAC y lo ata a un `Invitation.code`.
4. El admin genera el link desde la ficha de la invitación.

## Configuración

En `.env`:

```
DEEPLINK_HMAC_SECRET=...   # obligatorio, secreto solo en servidor
DEEPLINK_BASE_URL=https://desert.rxstudio.dev
DEEPLINK_PLAY_STORE_URL=...
DEEPLINK_APP_STORE_URL=...
```

## Contrato del token

```
v1.<base64url(payload)>.<base64url(hmac_sha256(payload_b64, secret))>
```

Payload:

```json
{ "f": "invite", "c": "L6QXJO5F", "exp": 1767225600, "jti": "<uuid>" }
```

- `f` — feature (`invite`)
- `c` — código de invitación
- `exp` — vencimiento unix
- `jti` — id único (single-use; mismo `device_id` puede re-canjear)

### Redeem

```
POST /api/deeplink/redeem
{ "token": "v1....", "device_id": "..." }
```

200:

```json
{
  "valid": true,
  "feature": "invite",
  "invitation_code": "L6QXJO5F",
  "jti": "...",
  "expires_at": "2026-12-31T23:59:59+00:00"
}
```

422 `reason`: `expired` | `already_used` | `invalid_signature` | `unknown_feature`

## Probar en local (app no publicada)

1. Este Laravel debe servir `https://desert.rxstudio.dev` (o tunnel/DNS apuntando acá).
2. Verificar archivos:

```bash
curl -I https://desert.rxstudio.dev/.well-known/assetlinks.json
curl -I https://desert.rxstudio.dev/.well-known/apple-app-site-association
curl -I "https://desert.rxstudio.dev/activar?feature=invite&token=test"
```

Deben ser **HTTP 200**, HTTPS válido, **sin redirects**, `Content-Type: application/json` en los well-known.

3. Generar link:

```bash
php artisan deeplink:generate CODIGO --days=30
```

O desde Admin → Evento → Invitación → Detalle → **Generar link**.

4. Mientras App Links no verifique, usá el custom scheme:

```bash
adb shell am start -a android.intent.action.VIEW \
  -d "deserteventos://activar?feature=invite&token=TOKEN"
```

O forzá verificación:

```bash
adb shell pm set-app-links --package ar.com.deserteventos.app 1 all
```

5. Cuando publiquen en Play, agregá el SHA-256 de **Play App Signing** a `public/.well-known/assetlinks.json`.

6. iOS: reemplazá `REEMPLAZAR_TEAM_ID` en `resources/well-known/apple-app-site-association` cuando exista Cap iOS.

## Cambio mínimo en la app (desert-eventos-app)

Hoy la app solo conoce `feature: "premium"` y no abre el onboarding. Para cerrar el flujo:

1. En `FeatureKey` / `KNOWN_FEATURES`, agregar `'invite'`.
2. En `RedeemResponse` / `DeepLinkValidation`, leer `invitation_code`.
3. Tras redeem OK: misma lógica que `EnterCodePage.continue` — `validateCode(invitation_code)` y navegar a `/onboarding/datos`.

Sin ese cambio, el backend ya responde bien pero la app marcará `unknown_feature`.
