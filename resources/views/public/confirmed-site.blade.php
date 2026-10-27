<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="author" content="RXStudio.dev">
    <title>{{ $event->name }} — {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[var(--desert-bg)] font-sans text-[var(--desert-sand)] antialiased">
    <header class="relative isolate min-h-[22rem] overflow-hidden sm:min-h-[28rem]">
        <img src="{{ asset('assets/confirmed-site-hero.png') }}" alt=""
             class="absolute inset-0 h-full w-full object-cover" />
        <div class="absolute inset-0 bg-[var(--desert-bg)]/55"></div>
        <div class="relative z-10 mx-auto flex max-w-4xl flex-col items-center px-6 py-12 text-center sm:py-16">
            <img src="{{ asset('assets/logo-desert.png') }}" alt="{{ config('app.name') }}"
                 class="mb-8 h-16 w-auto object-contain sm:h-20" />
            <h1 class="text-3xl font-semibold tracking-wide text-white sm:text-5xl">{{ $event->name }}</h1>
            @if ($event->host)
                <p class="mt-4 text-sm font-medium uppercase tracking-[0.2em] text-[var(--desert-gold)]">
                    {{ __('invitation.confirmed_site.host_label') }}
                </p>
                <p class="mt-1 text-xl text-[var(--desert-sand)] sm:text-2xl">{{ $event->host }}</p>
            @endif
        </div>
    </header>

    <main class="mx-auto max-w-5xl px-4 py-10 sm:px-6 sm:py-14">
        @if ($invitations->isEmpty())
            <p class="text-center text-[var(--desert-muted)]">{{ __('invitation.confirmed_site.empty') }}</p>
        @else
            <div class="mb-4">
                <label for="guest-search" class="sr-only">{{ __('invitation.confirmed_site.search_placeholder') }}</label>
                <div class="flex items-center gap-3 rounded-lg border border-[var(--desert-border)] bg-[var(--desert-surface)] px-3 shadow-sm focus-within:border-[var(--desert-gold)] focus-within:ring-2 focus-within:ring-[var(--desert-gold)]/40">
                    <svg class="h-5 w-5 shrink-0 text-[var(--desert-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M11 18a7 7 0 100-14 7 7 0 000 14z" />
                    </svg>
                    <input id="guest-search" type="text" autocomplete="off"
                           placeholder="{{ __('invitation.confirmed_site.search_placeholder') }}"
                           class="min-w-0 flex-1 border-0 bg-transparent py-3 text-sm text-[var(--desert-bg)] placeholder:text-[var(--desert-muted)] focus:outline-none focus:ring-0" />
                </div>
            </div>

            <div class="overflow-hidden rounded-xl border border-[var(--desert-border)] bg-[var(--desert-surface)] shadow-lg" style="color-scheme: light;">
                <table class="min-w-full divide-y divide-[var(--desert-border)]/20">
                    <thead class="bg-[var(--desert-bg-elevated)]">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-[var(--desert-sand)]">{{ __('invitation.confirmed_site.first_name') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-[var(--desert-sand)]">{{ __('invitation.confirmed_site.last_name') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-[var(--desert-sand)]">{{ __('invitation.confirmed_site.document') }}</th>
                        </tr>
                    </thead>
                    <tbody id="guest-rows" class="divide-y divide-black/5">
                        @foreach ($invitations as $invitation)
                            @php
                                $firstName = $invitation->guest?->displayFirstName() ?? '';
                                $lastName = $invitation->guest?->displayLastName() ?? '';
                                $document = $invitation->guest?->document_number ?? '';
                            @endphp
                            <tr class="guest-row text-[var(--desert-bg)]"
                                data-search="{{ mb_strtolower(trim($firstName.' '.$lastName.' '.$document)) }}">
                                <td class="px-4 py-3 text-sm font-medium">{{ $firstName }}</td>
                                <td class="px-4 py-3 text-sm">{{ $lastName }}</td>
                                <td class="px-4 py-3 font-mono text-sm">{{ $document }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <p id="guest-empty" class="hidden px-4 py-8 text-center text-sm text-[var(--desert-muted)]">
                    {{ __('invitation.confirmed_site.no_results') }}
                </p>
            </div>
        @endif
    </main>

    <footer class="px-4 pb-10 text-center text-xs text-[var(--desert-muted)] sm:px-6">
        Diseñado por <a href="https://rxstudio.dev" target="_blank" rel="noopener noreferrer" class="text-[var(--desert-sand)] underline decoration-[var(--desert-border)] underline-offset-2 hover:text-[var(--desert-gold)]">RxStudio.dev</a>
    </footer>

    @unless ($invitations->isEmpty())
        <script>
            (function () {
                const input = document.getElementById('guest-search');
                const rows = Array.from(document.querySelectorAll('.guest-row'));
                const empty = document.getElementById('guest-empty');
                const table = document.querySelector('#guest-rows')?.closest('table');

                if (! input || rows.length === 0) {
                    return;
                }

                input.addEventListener('input', function () {
                    const query = input.value.trim().toLowerCase();
                    let visible = 0;

                    rows.forEach(function (row) {
                        const match = query === '' || (row.dataset.search || '').includes(query);
                        row.classList.toggle('hidden', ! match);
                        if (match) {
                            visible += 1;
                        }
                    });

                    empty?.classList.toggle('hidden', visible > 0);
                    table?.classList.toggle('hidden', visible === 0);
                });
            })();
        </script>
    @endunless
</body>
</html>
