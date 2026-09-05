<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="text-xl font-semibold text-white">{{ __('event.index.title') }}</h1>
            <a href="{{ route('admin.events.create') }}" wire:navigate
               class="inline-flex items-center gap-2 rounded-md bg-[var(--desert-gold)] px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-[var(--desert-gold-dark)]">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                {{ __('event.index.new') }}
            </a>
        </div>
    </x-slot>

    @if (session('status'))
        <div class="mb-4 rounded-md bg-green-50 p-4 text-sm text-green-800">
            {{ session('status') }}
        </div>
    @endif
    @if (session('error'))
        <div class="mb-4 rounded-md bg-red-50 p-4 text-sm text-red-800">
            {{ session('error') }}
        </div>
    @endif

    <div x-data="{
        open: false,
        confirmLabel: '',
        confirmValue: '',
        formId: '',
        openModal(label, fid) { this.confirmLabel = label; this.formId = fid; this.confirmValue = ''; this.open = true; },
        close() { this.open = false; this.confirmValue = ''; },
        submit() { if (this.confirmValue === this.confirmLabel && this.formId) { var f = document.getElementById(this.formId); if (f) f.submit(); } this.close(); },
        ...registrationLinkModalState()
    }">
    <div class="mb-4 rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
        <form method="GET" action="{{ route('admin.events.index') }}" class="flex flex-wrap items-end gap-4">
            <div class="min-w-[180px]">
                <label for="filter_name" class="block text-sm font-medium text-gray-700">{{ __('event.attributes.name') }}</label>
                <input type="text" id="filter_name" name="name" value="{{ request('name') }}"
                       placeholder="{{ __('event.index.search_name_placeholder') }}"
                       class="mt-1 block w-full rounded-md border border-gray-300 px-3.5 py-2.5 shadow-sm focus:border-[var(--desert-bg-elevated)] focus:ring-[var(--desert-bg-elevated)] text-sm" />
            </div>
            <div class="min-w-[180px]">
                <label for="filter_host" class="block text-sm font-medium text-gray-700">{{ __('event.attributes.host') }}</label>
                <input type="text" id="filter_host" name="host" value="{{ request('host') }}"
                       placeholder="{{ __('event.index.search_host_placeholder') }}"
                       class="mt-1 block w-full rounded-md border border-gray-300 px-3.5 py-2.5 shadow-sm focus:border-[var(--desert-bg-elevated)] focus:ring-[var(--desert-bg-elevated)] text-sm" />
            </div>
            <div class="min-w-[180px]">
                <label for="filter_type" class="block text-sm font-medium text-gray-700">{{ __('event.attributes.type') }}</label>
                <x-select-input id="filter_type" name="type" class="mt-1">
                    <option value="">{{ __('event.index.all_types') }}</option>
                    @foreach($types as $value => $label)
                        <option value="{{ $value }}" @selected(request('type') === $value)>{{ $label }}</option>
                    @endforeach
                </x-select-input>
            </div>
            <div class="min-w-[180px]">
                <label for="filter_place" class="block text-sm font-medium text-gray-700">{{ __('event.attributes.place') }}</label>
                <x-select-input id="filter_place" name="place" class="mt-1">
                    <option value="">{{ __('event.index.all_places') }}</option>
                    @foreach($places as $value => $label)
                        <option value="{{ $value }}" @selected(request('place') === $value)>{{ $label }}</option>
                    @endforeach
                </x-select-input>
            </div>
            <div class="min-w-[160px]">
                <label for="filter_date_from" class="block text-sm font-medium text-gray-700">{{ __('event.attributes.date_from') }}</label>
                <input type="date" id="filter_date_from" name="date_from" value="{{ request('date_from') }}"
                       class="mt-1 block w-full rounded-md border border-gray-300 px-3.5 py-2.5 shadow-sm focus:border-[var(--desert-bg-elevated)] focus:ring-[var(--desert-bg-elevated)] text-sm" />
            </div>
            <div class="min-w-[160px]">
                <label for="filter_date_to" class="block text-sm font-medium text-gray-700">{{ __('event.attributes.date_to') }}</label>
                <input type="date" id="filter_date_to" name="date_to" value="{{ request('date_to') }}"
                       class="mt-1 block w-full rounded-md border border-gray-300 px-3.5 py-2.5 shadow-sm focus:border-[var(--desert-bg-elevated)] focus:ring-[var(--desert-bg-elevated)] text-sm" />
            </div>
            <div class="flex gap-2">
                <button type="submit" class="inline-flex items-center gap-2 rounded-md bg-[var(--desert-bg-elevated)] px-3 py-2 text-sm font-medium text-white shadow-sm hover:bg-[var(--desert-bg)]">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    {{ __('admin.actions.filter') }}
                </button>
                <a href="{{ route('admin.events.index') }}" wire:navigate
                   class="inline-flex items-center gap-2 rounded-md border border-gray-300 bg-transparent px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    {{ __('admin.actions.clear') }}
                </a>
            </div>
        </form>
    </div>

    <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-[var(--desert-bg-elevated)]">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase text-[var(--desert-sand)]">{{ __('event.attributes.name') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase text-[var(--desert-sand)]">{{ __('event.attributes.host') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase text-[var(--desert-sand)]">{{ __('event.attributes.init_date') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase text-[var(--desert-sand)]">{{ __('event.attributes.end_date') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase text-[var(--desert-sand)]">{{ __('event.attributes.type') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase text-[var(--desert-sand)]">{{ __('event.attributes.place') }}</th>
                    <th class="px-4 py-3 text-right text-xs font-medium uppercase text-[var(--desert-sand)]">{{ __('admin.table.actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse($events as $event)
                    <tr>
                        <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-gray-900">{{ $event->name }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-600">{{ $event->host ?: '—' }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-600">{{ $event->init_date->format('d/m/Y') }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-600">{{ $event->end_date->format('d/m/Y') }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-sm">
                            <span class="inline-flex rounded-full bg-[var(--desert-sand)] px-2.5 py-0.5 text-xs font-medium text-[var(--desert-bg)]">
                                {{ $event->type->label() }}
                            </span>
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-sm">
                            <span class="inline-flex rounded-full bg-[var(--desert-sand)] px-2.5 py-0.5 text-xs font-medium text-[var(--desert-bg)]">
                                {{ $event->place->label() }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex flex-wrap items-center justify-end gap-2">
                                <button type="button"
                                        @click="openLinkModal({{ $event->id }}, @js($event->name))"
                                        class="inline-flex shrink-0 items-center gap-1.5 rounded-md bg-white px-2.5 py-1.5 text-xs font-medium text-[var(--desert-bg-elevated)] shadow-sm ring-1 ring-[var(--desert-gold)] hover:bg-[var(--desert-sand)]">
                                    {{ __('event.deeplink.open_modal') }}
                                </button>
                                <a href="{{ route('admin.events.invitations.index', $event) }}" wire:navigate
                                   class="inline-flex shrink-0 items-center gap-1.5 rounded-md bg-[var(--desert-gold)] px-2.5 py-1.5 text-xs font-medium text-white shadow-sm hover:bg-[var(--desert-gold-dark)]">
                                    {{ __('invitation.index.title') }}
                                </a>
                                <a href="{{ route('admin.events.accesses.index', $event) }}" wire:navigate
                                   class="inline-flex shrink-0 items-center gap-1.5 rounded-md bg-white px-2.5 py-1.5 text-xs font-medium text-[var(--desert-bg-elevated)] shadow-sm ring-1 ring-[var(--desert-bg-elevated)] hover:bg-[var(--desert-sand)]">
                                    {{ __('access.index.title') }}
                                </a>
                                <a href="{{ route('admin.events.edit', $event) }}" wire:navigate
                                   class="inline-flex shrink-0 items-center gap-1.5 rounded-md bg-[var(--desert-bg-elevated)] px-2.5 py-1.5 text-xs font-medium text-white shadow-sm hover:bg-[var(--desert-bg)]">
                                    <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    {{ __('admin.actions.edit') }}
                                </a>
                                <form id="form-delete-event-{{ $event->id }}" action="{{ route('admin.events.destroy', $event) }}" method="POST" class="inline shrink-0">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button"
                                            data-confirm-label="{{ e($event->name) }}"
                                            data-form-id="form-delete-event-{{ $event->id }}"
                                            @click="openModal($event.currentTarget.getAttribute('data-confirm-label'), $event.currentTarget.getAttribute('data-form-id'))"
                                            class="inline-flex shrink-0 items-center gap-1.5 rounded-md bg-red-600 px-2.5 py-1.5 text-xs font-medium text-white shadow-sm hover:bg-red-500">
                                        <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        {{ __('admin.actions.delete') }}
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">{{ __('event.index.empty') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="flex flex-col gap-3 border-t border-gray-200 bg-gray-50 px-4 py-3 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-sm text-gray-700">
                @if($events->total() > 0)
                    {{ __('admin.table.showing', ['from' => $events->firstItem(), 'to' => $events->lastItem(), 'total' => $events->total()]) }}
                @else
                    {{ __('admin.table.no_records') }}
                @endif
            </p>
            <div>
                {{ $events->links() }}
            </div>
        </div>
    </div>
    <x-delete-confirm-modal />
    <x-registration-link-modal />
    </div>
</x-admin-layout>
