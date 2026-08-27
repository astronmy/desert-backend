<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Activar invitación — Desert Eventos</title>
    <style>
        :root {
            --bg: #182828;
            --surface: #EAE4D8;
            --gold: #DCA15B;
            --gold-dark: #B58543;
            --accent: #C0B39B;
            --muted: #919496;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            min-height: 100vh;
            font-family: system-ui, -apple-system, Segoe UI, sans-serif;
            background:
                radial-gradient(ellipse 80% 50% at 50% 0%, rgba(220, 161, 91, 0.14), transparent 55%),
                linear-gradient(165deg, #1c3231 0%, var(--bg) 50%, #122020 100%);
            color: var(--surface);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }
        .card {
            width: 100%;
            max-width: 420px;
            text-align: center;
            padding: 36px 28px;
            border: 1px solid rgba(192, 179, 155, 0.28);
            border-radius: 12px;
            background: rgba(39, 60, 59, 0.55);
            backdrop-filter: blur(8px);
        }
        .logo {
            width: 96px;
            height: 96px;
            object-fit: contain;
            margin: 0 auto 20px;
            display: block;
        }
        h1 {
            font-size: 1.45rem;
            font-weight: 600;
            margin-bottom: 10px;
            letter-spacing: 0.01em;
        }
        p {
            color: var(--accent);
            font-size: 0.95rem;
            line-height: 1.5;
            margin-bottom: 28px;
        }
        .actions {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        a.btn {
            display: block;
            text-decoration: none;
            border-radius: 8px;
            padding: 14px 18px;
            font-weight: 600;
            font-size: 0.95rem;
        }
        a.btn-primary {
            background: var(--gold);
            color: var(--bg);
        }
        a.btn-primary:hover { background: var(--gold-dark); }
        a.btn-secondary {
            background: transparent;
            color: var(--surface);
            border: 1px solid rgba(192, 179, 155, 0.45);
        }
        a.btn-secondary:hover { border-color: var(--gold); color: var(--gold); }
        .hint {
            margin-top: 22px;
            font-size: 0.75rem;
            color: var(--muted);
            letter-spacing: 0.04em;
        }
    </style>
</head>
<body>
    <main class="card">
        <img class="logo" src="{{ asset('assets/logo-desert.png') }}" alt="Desert Eventos">
        <h1>Abrí la app para confirmar tu invitación</h1>
        <p>
            Si tenés Desert Eventos instalada, tocá el botón para abrirla.
            Si no, descargala desde la tienda y volvé a este link.
        </p>
        <div class="actions">
            <a class="btn btn-primary" id="open-app" href="{{ $customSchemeUrl }}">Abrir Desert Eventos</a>
            <a class="btn btn-secondary" href="{{ $playStoreUrl }}" data-store="play">Descargar en Google Play</a>
            <a class="btn btn-secondary" href="{{ $appStoreUrl }}" data-store="app_store">Descargar en App Store</a>
        </div>
        <p class="hint">deserteventos.com.ar</p>
    </main>
    <script>
        (function () {
            const token = @json($token);
            const code = @json($code ?? null);
            const customSchemeUrl = @json($customSchemeUrl);
            const intentUrl = @json($intentUrl);
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            const endpoint = @json(route('activar.store-click'));
            const isAndroid = /Android/i.test(navigator.userAgent || '');

            const openBtn = document.getElementById('open-app');
            if (openBtn && isAndroid) {
                openBtn.setAttribute('href', intentUrl);
            }

            if (token) {
                setTimeout(function () {
                    window.location.href = isAndroid ? intentUrl : customSchemeUrl;
                }, 400);
            }

            function trackStoreClick(store) {
                const body = JSON.stringify({ store: store, token: token || null, code: code || null });
                fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: body,
                    keepalive: true,
                    credentials: 'same-origin',
                }).catch(function () {});
            }

            document.querySelectorAll('[data-store]').forEach(function (el) {
                el.addEventListener('click', function () {
                    trackStoreClick(el.getAttribute('data-store'));
                });
            });
        })();
    </script>
</body>
</html>
