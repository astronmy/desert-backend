<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') — Desert Eventos</title>
    <meta name="description" content="@yield('meta_description', 'Desert Eventos — Luján de Cuyo, Mendoza')">
    <style>
        :root {
            --bg: #182828;
            --bg-elevated: #273C3B;
            --surface: #EAE4D8;
            --gold: #DCA15B;
            --gold-dark: #B58543;
            --accent: #C0B39B;
            --muted: #919496;
            --border: rgba(192, 179, 155, 0.28);
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            min-height: 100vh;
            font-family: system-ui, -apple-system, "Segoe UI", sans-serif;
            background:
                radial-gradient(ellipse 80% 50% at 50% 0%, rgba(220, 161, 91, 0.14), transparent 55%),
                linear-gradient(165deg, #1c3231 0%, var(--bg) 50%, #122020 100%);
            color: var(--surface);
            line-height: 1.6;
        }
        a { color: var(--gold); text-decoration: underline; text-underline-offset: 2px; }
        a:hover { color: var(--gold-dark); }
        .wrap {
            width: 100%;
            max-width: 760px;
            margin: 0 auto;
            padding: 28px 20px 48px;
        }
        header.site {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 28px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--border);
        }
        .brand {
            display: flex;
            align-items: center;
            gap: 14px;
            text-decoration: none;
            color: inherit;
        }
        .brand img {
            width: 56px;
            height: 56px;
            object-fit: contain;
        }
        .brand span {
            font-weight: 600;
            letter-spacing: 0.02em;
        }
        nav.legal-nav {
            display: flex;
            gap: 16px;
            font-size: 0.9rem;
        }
        nav.legal-nav a {
            text-decoration: none;
            color: var(--accent);
        }
        nav.legal-nav a:hover,
        nav.legal-nav a[aria-current="page"] {
            color: var(--gold);
        }
        article {
            border: 1px solid var(--border);
            border-radius: 12px;
            background: rgba(39, 60, 59, 0.55);
            backdrop-filter: blur(8px);
            padding: 28px 24px 36px;
        }
        @media (min-width: 640px) {
            article { padding: 36px 40px 44px; }
        }
        h1 {
            font-size: 1.65rem;
            font-weight: 700;
            letter-spacing: 0.01em;
            margin-bottom: 12px;
            color: var(--surface);
        }
        .meta {
            color: var(--accent);
            font-size: 0.95rem;
            margin-bottom: 8px;
        }
        .meta strong { color: var(--surface); font-weight: 600; }
        .vigencia {
            display: inline-block;
            margin: 12px 0 20px;
            font-size: 0.85rem;
            color: var(--muted);
            letter-spacing: 0.02em;
        }
        .lead {
            color: var(--accent);
            margin-bottom: 16px;
        }
        .note {
            font-size: 0.9rem;
            color: var(--muted);
            margin-bottom: 28px;
            padding: 12px 14px;
            border-left: 3px solid var(--gold);
            background: rgba(0, 0, 0, 0.15);
            border-radius: 0 8px 8px 0;
        }
        hr {
            border: none;
            border-top: 1px solid var(--border);
            margin: 28px 0;
        }
        h2 {
            font-size: 1.15rem;
            font-weight: 650;
            margin: 28px 0 12px;
            color: var(--gold);
        }
        h3 {
            font-size: 1rem;
            font-weight: 600;
            margin: 20px 0 10px;
            color: var(--accent);
        }
        p { margin-bottom: 12px; }
        ul, ol {
            margin: 0 0 14px 1.25rem;
        }
        li { margin-bottom: 6px; }
        .table-wrap {
            width: 100%;
            overflow-x: auto;
            margin: 14px 0 18px;
            -webkit-overflow-scrolling: touch;
        }
        table {
            width: 100%;
            min-width: 520px;
            border-collapse: collapse;
            font-size: 0.88rem;
        }
        th, td {
            border: 1px solid var(--border);
            padding: 10px 12px;
            text-align: left;
            vertical-align: top;
        }
        th {
            background: rgba(0, 0, 0, 0.25);
            color: var(--gold);
            font-weight: 600;
        }
        td { color: var(--surface); }
        footer.site {
            margin-top: 28px;
            text-align: center;
            font-size: 0.8rem;
            color: var(--muted);
            letter-spacing: 0.04em;
        }
        footer.site a { color: var(--accent); text-decoration: none; }
        footer.site a:hover { color: var(--gold); }
    </style>
</head>
<body>
    <div class="wrap">
        <header class="site">
            <a class="brand" href="{{ url('/') }}">
                <img src="{{ asset('assets/logo-desert.png') }}" alt="Desert Eventos">
                <span>Desert Eventos</span>
            </a>
            <nav class="legal-nav" aria-label="Documentos legales">
                <a href="{{ route('legal.terminos') }}" @if(request()->routeIs('legal.terminos')) aria-current="page" @endif>Términos</a>
                <a href="{{ route('legal.privacidad') }}" @if(request()->routeIs('legal.privacidad')) aria-current="page" @endif>Privacidad</a>
            </nav>
        </header>

        <article>
            @yield('content')
        </article>

        <footer class="site">
            <p>Luján de Cuyo, Mendoza · <a href="mailto:info@deserteventos.com">info@deserteventos.com</a></p>
        </footer>
    </div>
</body>
</html>
