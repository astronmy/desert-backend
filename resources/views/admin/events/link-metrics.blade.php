<x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-xl font-semibold text-white">{{ __('event.deeplink.metrics_title') }}</h1>
                <p class="text-sm text-[var(--desert-sand)]">{{ __('event.deeplink.metrics_subtitle', ['name' => $event->name]) }}</p>
            </div>
            <div class="flex flex-wrap gap-2" x-data="registrationLinkModalState()">
                <button type="button"
                        @click="openLinkModal({{ $event->id }}, @js($event->name))"
                        class="inline-flex items-center gap-2 rounded-md border border-white/35 bg-white/10 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-white/20">
                    {{ __('event.deeplink.open_modal') }}
                </button>
                <a href="{{ route('admin.events.invitations.index', $event) }}" wire:navigate
                   class="inline-flex items-center gap-2 rounded-md bg-[var(--desert-gold)] px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-[var(--desert-gold-dark)]">
                    {{ __('invitation.index.title') }}
                </a>
                <x-registration-link-modal />
            </div>
        </div>
    </x-slot>

    <div class="mb-4">
        <a href="{{ route('admin.events.invitations.index', $event) }}" wire:navigate class="inline-flex items-center gap-2 text-sm font-medium text-[var(--desert-bg-elevated)] hover:underline">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            {{ __('admin.actions.back') }}
        </a>
    </div>

    @if ($active_link)
        <div class="mb-4 space-y-3 rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
            <div>
                <p class="text-xs font-medium uppercase tracking-wide text-gray-500">{{ __('event.deeplink.url') }}</p>
                <p class="mt-1 break-all font-mono text-sm text-gray-900">{{ $active_link->shortUrl() }}</p>
            </div>
            <p class="text-xs text-gray-500">{{ __('event.deeplink.expires_at', ['date' => $active_link->expires_at->timezone(config('app.timezone'))->format('d/m/Y H:i')]) }}</p>
        </div>
    @else
        <div class="mb-4 rounded-md bg-amber-50 p-4 text-sm text-amber-900">
            {{ __('event.deeplink.no_link') }}
        </div>
    @endif

    <div class="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
            <p class="text-xs font-medium uppercase tracking-wide text-gray-500">{{ __('event.deeplink.stat_views') }}</p>
            <p class="mt-2 text-3xl font-semibold text-gray-900">{{ number_format($total_views) }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
            <p class="text-xs font-medium uppercase tracking-wide text-gray-500">{{ __('event.deeplink.stat_uniques') }}</p>
            <p class="mt-2 text-3xl font-semibold text-gray-900">{{ number_format($unique_visitors) }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
            <p class="text-xs font-medium uppercase tracking-wide text-gray-500">{{ __('event.deeplink.stat_stores') }}</p>
            <p class="mt-2 text-3xl font-semibold text-gray-900">{{ number_format($store_clicks['total']) }}</p>
            <p class="mt-1 text-xs text-gray-500">
                {{ __('event.deeplink.stat_play') }}: {{ $store_clicks['play'] }} ·
                {{ __('event.deeplink.stat_app_store') }}: {{ $store_clicks['app_store'] }}
            </p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
            <p class="text-xs font-medium uppercase tracking-wide text-gray-500">{{ __('event.deeplink.stat_conversion') }}</p>
            @if ($soft_conversion['ratio'] !== null)
                <p class="mt-2 text-3xl font-semibold text-gray-900">{{ $soft_conversion['ratio'] }}%</p>
                <p class="mt-1 text-xs text-gray-500">
                    {{ __('event.deeplink.stat_conversion_ratio', [
                        'ratio' => $soft_conversion['ratio'],
                        'invitations' => $soft_conversion['invitations'],
                        'views' => $soft_conversion['views'],
                    ]) }}
                </p>
                @if ($soft_conversion['since'])
                    <p class="mt-1 text-xs text-gray-400">Desde {{ $soft_conversion['since'] }}</p>
                @endif
            @else
                <p class="mt-2 text-sm text-gray-500">{{ __('event.deeplink.stat_conversion_empty') }}</p>
            @endif
            <p class="mt-2 text-xs text-gray-400">{{ __('event.deeplink.stat_conversion_help') }}</p>
        </div>
    </div>

    <div class="mb-6 grid gap-4 lg:grid-cols-2">
        <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
            <h2 class="text-sm font-semibold text-gray-900">{{ __('event.deeplink.stat_devices') }}</h2>
            <ul class="mt-3 space-y-2">
                @foreach (['mobile', 'tablet', 'desktop', 'bot', 'unknown'] as $type)
                    <li class="flex items-center justify-between text-sm">
                        <span class="text-gray-700">{{ __('event.deeplink.device_'.$type) }}</span>
                        <span class="font-medium text-gray-900">
                            {{ $devices[$type] ?? 0 }}
                            <span class="text-gray-400">({{ $device_pct[$type] ?? 0 }}%)</span>
                        </span>
                    </li>
                @endforeach
            </ul>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
            <h2 class="text-sm font-semibold text-gray-900">{{ __('event.deeplink.stat_os') }}</h2>
            <ul class="mt-3 space-y-2">
                @php
                    $osOrder = ['ios', 'android', 'windows', 'macos', 'other', 'unknown'];
                    $osShown = [];
                @endphp
                @foreach ($osOrder as $key)
                    @if (($os[$key] ?? 0) > 0 || in_array($key, ['ios', 'android'], true))
                        @php $osShown[] = $key; @endphp
                        <li class="flex items-center justify-between text-sm">
                            <span class="text-gray-700">{{ __('event.deeplink.os_'.$key) }}</span>
                            <span class="font-medium text-gray-900">{{ $os[$key] ?? 0 }}</span>
                        </li>
                    @endif
                @endforeach
                @foreach ($os as $key => $count)
                    @if (! in_array($key, $osShown, true))
                        <li class="flex items-center justify-between text-sm">
                            <span class="text-gray-700">{{ $key }}</span>
                            <span class="font-medium text-gray-900">{{ $count }}</span>
                        </li>
                    @endif
                @endforeach
            </ul>
        </div>
    </div>

    <div class="mb-6 rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
        <h2 class="text-sm font-semibold text-gray-900">{{ __('event.deeplink.stat_series') }}</h2>
        @php $maxViews = max(1, collect($daily_series)->max('views')); @endphp
        <div class="mt-4 flex h-40 items-end gap-1 overflow-x-auto">
            @foreach ($daily_series as $day)
                <div class="flex min-w-[18px] flex-1 flex-col items-center justify-end gap-1" title="{{ $day['date'] }}: {{ $day['views'] }}">
                    <span class="text-[10px] text-gray-500">{{ $day['views'] > 0 ? $day['views'] : '' }}</span>
                    <div class="w-full rounded-t bg-[var(--desert-gold)]"
                         style="height: {{ max(4, (int) round(($day['views'] / $maxViews) * 100)) }}%"></div>
                    <span class="text-[9px] text-gray-400">{{ $day['label'] }}</span>
                </div>
            @endforeach
        </div>
    </div>

    <div class="mb-4 overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
        <div class="border-b border-gray-200 px-4 py-3">
            <h2 class="text-sm font-semibold text-gray-900">{{ __('event.deeplink.stat_recent') }}</h2>
        </div>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium uppercase text-gray-500">{{ __('event.deeplink.col_visited') }}</th>
                    <th class="px-4 py-2 text-left text-xs font-medium uppercase text-gray-500">{{ __('event.deeplink.col_device') }}</th>
                    <th class="px-4 py-2 text-left text-xs font-medium uppercase text-gray-500">{{ __('event.deeplink.col_os') }}</th>
                    <th class="px-4 py-2 text-left text-xs font-medium uppercase text-gray-500">{{ __('event.deeplink.col_referrer') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse ($recent_hits as $hit)
                    <tr>
                        <td class="whitespace-nowrap px-4 py-2 text-sm text-gray-700">
                            {{ $hit->visited_at->timezone(config('app.timezone'))->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-4 py-2 text-sm text-gray-700">
                            {{ __('event.deeplink.device_'.$hit->device_type) }}
                            @if ($hit->browser)
                                <span class="text-gray-400">({{ $hit->browser }})</span>
                            @endif
                        </td>
                        <td class="px-4 py-2 text-sm text-gray-700">
                            @if ($hit->os)
                                {{ __('event.deeplink.os_'.$hit->os) }}
                            @else
                                —
                            @endif
                        </td>
                        <td class="max-w-xs truncate px-4 py-2 text-sm text-gray-500" title="{{ $hit->referrer }}">
                            {{ $hit->referrer ?: '—' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-6 text-center text-sm text-gray-500">{{ __('event.deeplink.stat_recent_empty') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <p class="text-xs text-gray-400">{{ __('event.deeplink.metrics_future') }}</p>
</x-admin-layout>
