<x-admin-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-xl font-semibold text-white">{{ __('invitation.logs.title') }}</h1>
            <p class="text-sm text-[var(--desert-sand)]">{{ __('invitation.index.subtitle', ['name' => $event->name]) }}</p>
        </div>
    </x-slot>

    <div class="mb-4">
        <a href="{{ route('admin.events.invitations.index', $event) }}" wire:navigate class="inline-flex items-center gap-2 text-sm font-medium text-[var(--desert-bg-elevated)] hover:underline">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            {{ __('invitation.index.title') }}
        </a>
    </div>

    <div class="mb-4 rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
        <form method="GET" action="{{ route('admin.events.invitation-logs.index', $event) }}" class="flex flex-wrap items-end gap-4">
            <div class="min-w-[150px]">
                <label for="filter_date_from" class="block text-sm font-medium text-gray-700">{{ __('invitation.logs.date_from') }}</label>
                <input type="date" id="filter_date_from" name="date_from" value="{{ request('date_from') }}"
                       class="mt-1 block w-full rounded-md border border-gray-300 text-sm shadow-sm focus:border-[var(--desert-bg-elevated)] focus:ring-[var(--desert-bg-elevated)]" />
            </div>
            <div class="min-w-[150px]">
                <label for="filter_date_to" class="block text-sm font-medium text-gray-700">{{ __('invitation.logs.date_to') }}</label>
                <input type="date" id="filter_date_to" name="date_to" value="{{ request('date_to') }}"
                       class="mt-1 block w-full rounded-md border border-gray-300 text-sm shadow-sm focus:border-[var(--desert-bg-elevated)] focus:ring-[var(--desert-bg-elevated)]" />
            </div>
            <div class="min-w-[160px]">
                <label for="filter_action" class="block text-sm font-medium text-gray-700">{{ __('invitation.logs.action') }}</label>
                <x-select-input id="filter_action" name="action" class="mt-1">
                    <option value="">{{ __('invitation.logs.all_actions') }}</option>
                    @foreach($actions as $value => $label)
                        <option value="{{ $value }}" @selected(request('action') === $value)>{{ $label }}</option>
                    @endforeach
                </x-select-input>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="inline-flex items-center gap-2 rounded-md bg-[var(--desert-bg-elevated)] px-3 py-2 text-sm font-medium text-white shadow-sm hover:bg-[var(--desert-bg)]">
                    {{ __('admin.actions.filter') }}
                </button>
                <a href="{{ route('admin.events.invitation-logs.index', $event) }}" wire:navigate
                   class="inline-flex items-center gap-2 rounded-md border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    {{ __('admin.actions.clear') }}
                </a>
            </div>
        </form>
    </div>

    <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-[var(--desert-bg-elevated)]">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase text-[var(--desert-sand)]">{{ __('invitation.logs.date') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase text-[var(--desert-sand)]">{{ __('invitation.logs.user') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase text-[var(--desert-sand)]">{{ __('invitation.logs.action') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase text-[var(--desert-sand)]">{{ __('invitation.logs.status_from') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase text-[var(--desert-sand)]">{{ __('invitation.logs.status_to') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase text-[var(--desert-sand)]">{{ __('invitation.attributes.code') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase text-[var(--desert-sand)]">{{ __('guest.attributes.full_name') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse($logs as $log)
                    <tr>
                        <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-700">{{ $log->created_at?->timezone(config('app.timezone'))->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $log->user?->name ?? __('invitation.logs.system') }}</td>
                        <td class="px-4 py-3 text-sm text-gray-900">{{ $log->action->label() }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $log->status_from->label() }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $log->status_to->label() }}</td>
                        <td class="px-4 py-3 font-mono text-sm text-gray-900">{{ $log->invitation?->code }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ trim(($log->invitation?->guest?->first_name ?? '').' '.($log->invitation?->guest?->last_name ?? '')) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-sm text-gray-500">{{ __('invitation.logs.empty') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $logs->links() }}</div>
</x-admin-layout>
