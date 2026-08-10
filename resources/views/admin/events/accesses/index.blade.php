<x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-xl font-semibold text-white">{{ __('access.index.title') }}</h1>
                <p class="text-sm text-[var(--desert-sand)]">{{ __('access.index.subtitle', ['name' => $event->name]) }}</p>
            </div>
            <a href="{{ route('admin.events.invitations.index', $event) }}" wire:navigate
               class="inline-flex items-center gap-2 rounded-md bg-[var(--desert-gold)] px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-[var(--desert-gold-dark)]">
                {{ __('invitation.index.title') }}
            </a>
        </div>
    </x-slot>

    <div class="mb-4">
        <a href="{{ route('admin.events.index') }}" wire:navigate class="inline-flex items-center gap-2 text-sm font-medium text-[var(--desert-bg-elevated)] hover:underline">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            {{ __('admin.actions.back') }}
        </a>
    </div>

    <div class="mb-4 rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
        <form method="GET" action="{{ route('admin.events.accesses.index', $event) }}" class="flex flex-wrap items-end gap-4">
            <div class="min-w-[160px]">
                <label for="filter_name" class="block text-sm font-medium text-gray-700">{{ __('access.attributes.guest') }}</label>
                <input type="text" id="filter_name" name="name" value="{{ request('name') }}"
                       placeholder="{{ __('access.index.search_name_placeholder') }}"
                       class="mt-1 block w-full rounded-md border border-gray-300 text-sm shadow-sm" />
            </div>
            <div class="min-w-[150px]">
                <label for="filter_document" class="block text-sm font-medium text-gray-700">{{ __('access.attributes.document_number') }}</label>
                <input type="text" id="filter_document" name="document_number" value="{{ request('document_number') }}"
                       placeholder="{{ __('access.index.search_document_placeholder') }}"
                       class="mt-1 block w-full rounded-md border border-gray-300 text-sm shadow-sm" />
            </div>
            <div class="min-w-[130px]">
                <label for="filter_code" class="block text-sm font-medium text-gray-700">{{ __('access.attributes.invitation_code') }}</label>
                <input type="text" id="filter_code" name="code" value="{{ request('code') }}"
                       placeholder="{{ __('access.index.search_code_placeholder') }}"
                       class="mt-1 block w-full rounded-md border border-gray-300 text-sm shadow-sm" />
            </div>
            <div class="min-w-[150px]">
                <label for="filter_date_from" class="block text-sm font-medium text-gray-700">{{ __('access.attributes.date_from') }}</label>
                <input type="date" id="filter_date_from" name="date_from" value="{{ request('date_from') }}"
                       class="mt-1 block w-full rounded-md border border-gray-300 text-sm shadow-sm" />
            </div>
            <div class="min-w-[150px]">
                <label for="filter_date_to" class="block text-sm font-medium text-gray-700">{{ __('access.attributes.date_to') }}</label>
                <input type="date" id="filter_date_to" name="date_to" value="{{ request('date_to') }}"
                       class="mt-1 block w-full rounded-md border border-gray-300 text-sm shadow-sm" />
            </div>
            <div class="flex gap-2">
                <button type="submit" class="inline-flex items-center gap-2 rounded-md bg-[var(--desert-bg-elevated)] px-3 py-2 text-sm font-medium text-white shadow-sm hover:bg-[var(--desert-bg)]">
                    {{ __('admin.actions.filter') }}
                </button>
                <a href="{{ route('admin.events.accesses.index', $event) }}" wire:navigate
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
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase text-[var(--desert-sand)]">{{ __('access.attributes.guest') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase text-[var(--desert-sand)]">{{ __('access.attributes.document_number') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase text-[var(--desert-sand)]">{{ __('access.attributes.invitation_code') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase text-[var(--desert-sand)]">{{ __('access.attributes.accessed_at') }}</th>
                    <th class="px-4 py-3 text-right text-xs font-medium uppercase text-[var(--desert-sand)]">{{ __('admin.table.actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse($accesses as $access)
                    <tr>
                        <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-gray-900">{{ $access->guestFullName() }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-600">{{ strtoupper($access->guest_id_type) }} {{ $access->guest_document_number }}</td>
                        <td class="whitespace-nowrap px-4 py-3 font-mono text-sm text-gray-900">{{ $access->invitation_code }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-600">{{ $access->accessed_at->format('d/m/Y H:i:s') }}</td>
                        <td class="px-4 py-3 text-right">
                            @if($access->invitation)
                                <a href="{{ route('admin.events.invitations.show', [$event, $access->invitation]) }}?from=accesses"
                                   wire:navigate
                                   class="inline-flex shrink-0 items-center gap-1.5 rounded-md bg-[var(--desert-gold)] px-2.5 py-1.5 text-xs font-medium text-white shadow-sm hover:bg-[var(--desert-gold-dark)]">
                                    {{ __('admin.actions.view') }}
                                </a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-gray-500">{{ __('access.index.empty') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="flex flex-col gap-3 border-t border-gray-200 bg-gray-50 px-4 py-3 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-sm text-gray-700">
                @if($accesses->total() > 0)
                    {{ __('admin.table.showing', ['from' => $accesses->firstItem(), 'to' => $accesses->lastItem(), 'total' => $accesses->total()]) }}
                @else
                    {{ __('admin.table.no_records') }}
                @endif
            </p>
            <div>{{ $accesses->links() }}</div>
        </div>
    </div>
</x-admin-layout>
