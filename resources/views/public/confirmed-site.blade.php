<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
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
            <div class="overflow-hidden rounded-xl border border-[var(--desert-border)] bg-[var(--desert-surface)] shadow-lg" style="color-scheme: light;">
                <table class="min-w-full divide-y divide-[var(--desert-border)]/20">
                    <thead class="bg-[var(--desert-bg-elevated)]">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-[var(--desert-sand)]">{{ __('invitation.confirmed_site.first_name') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-[var(--desert-sand)]">{{ __('invitation.confirmed_site.last_name') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-[var(--desert-sand)]">{{ __('invitation.confirmed_site.document') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-black/5">
                        @foreach ($invitations as $invitation)
                            <tr class="text-[var(--desert-bg)]">
                                <td class="px-4 py-3 text-sm font-medium">{{ $invitation->guest?->first_name }}</td>
                                <td class="px-4 py-3 text-sm">{{ $invitation->guest?->last_name }}</td>
                                <td class="px-4 py-3 font-mono text-sm">{{ $invitation->guest?->document_number }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </main>
</body>
</html>
