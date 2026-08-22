<x-admin-layout>
    <x-slot name="header">
        <h1 class="text-xl font-semibold text-white">{{ __('dashboard.title') }}</h1>
    </x-slot>

    <div class="mb-6 rounded-lg border border-[var(--desert-sand)] bg-white p-6 shadow-sm">
        <h2 class="text-lg font-semibold text-gray-900">{{ __('dashboard.welcome', ['name' => auth()->user()->name]) }}</h2>
        @if ($event)
            <p class="mt-2 text-sm text-gray-600">{{ __('dashboard.client_subtitle', ['name' => $event->name]) }}</p>
        @else
            <p class="mt-2 text-sm text-gray-600">{{ __('dashboard.subtitle') }}</p>
        @endif
    </div>

    @if (auth()->user()->requiresEvent())
        @if (! $event)
            <div class="rounded-md bg-amber-50 p-4 text-sm text-amber-900">{{ __('dashboard.no_event') }}</div>
        @elseif ($invitations)
            <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <h3 class="text-base font-semibold text-gray-900">{{ __('dashboard.invitations_section') }}</h3>
                <div class="flex flex-wrap gap-2">
                    @can('permission', 'invitaciones.exportar')
                        <a href="{{ route('admin.events.invitations.export', $event) }}"
                           class="inline-flex items-center gap-2 rounded-md border border-emerald-700 bg-white px-3 py-2 text-sm font-semibold text-emerald-800 shadow-sm hover:bg-emerald-50">
                            {{ __('invitation.index.export') }}
                        </a>
                    @endcan
                    @can('permission', 'invitaciones.crear')
                        <a href="{{ route('admin.events.invitations.create', $event) }}" wire:navigate
                           class="inline-flex items-center gap-2 rounded-md bg-[var(--desert-gold)] px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-[var(--desert-gold-dark)]">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            {{ __('invitation.index.new') }}
                        </a>
                    @endcan
                </div>
            </div>

            <div class="mb-4 rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
                <form method="GET" action="{{ route('admin.dashboard') }}" class="flex flex-wrap items-end gap-4">
                    <div class="min-w-[180px]">
                        <label for="filter_name" class="block text-sm font-medium text-gray-700">{{ __('invitation.attributes.code') }} / nombre</label>
                        <input type="text" id="filter_name" name="name" value="{{ request('name') }}"
                               placeholder="{{ __('invitation.index.search_name_placeholder') }}"
                               class="mt-1 block w-full rounded-md border border-gray-300 px-3.5 py-2.5 text-sm shadow-sm" />
                    </div>
                    <div class="min-w-[160px]">
                        <label for="filter_status" class="block text-sm font-medium text-gray-700">{{ __('invitation.attributes.status') }}</label>
                        <x-select-input id="filter_status" name="status" class="mt-1">
                            <option value="">{{ __('invitation.index.all_statuses') }}</option>
                            @foreach ($statuses as $value => $label)
                                <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                            @endforeach
                        </x-select-input>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="inline-flex items-center gap-2 rounded-md bg-[var(--desert-bg-elevated)] px-3 py-2 text-sm font-medium text-white shadow-sm hover:bg-[var(--desert-bg)]">
                            {{ __('admin.actions.filter') }}
                        </button>
                        <a href="{{ route('admin.dashboard') }}" wire:navigate
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
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase text-[var(--desert-sand)]">{{ __('invitation.attributes.code') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase text-[var(--desert-sand)]">Nombre</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase text-[var(--desert-sand)]">Documento</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase text-[var(--desert-sand)]">{{ __('invitation.attributes.status') }}</th>
                            <th class="px-4 py-3 text-right text-xs font-medium uppercase text-[var(--desert-sand)]">{{ __('admin.table.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse ($invitations as $invitation)
                            <tr>
                                <td class="whitespace-nowrap px-4 py-3 font-mono text-sm text-gray-900">{{ $invitation->code }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-900">
                                    {{ $invitation->guest?->first_name }} {{ $invitation->guest?->last_name }}
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-600">{{ $invitation->guest?->document_number }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-sm">
                                    <span class="inline-flex rounded-full bg-[var(--desert-sand)] px-2.5 py-0.5 text-xs font-medium text-[var(--desert-bg)]">
                                        {{ $invitation->status->label() }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    @can('permission', 'invitaciones.ver')
                                        <a href="{{ route('admin.events.invitations.show', [$event, $invitation]) }}" wire:navigate
                                           class="inline-flex items-center rounded-md bg-[var(--desert-bg-elevated)] px-2.5 py-1.5 text-xs font-medium text-white hover:bg-[var(--desert-bg)]">
                                            {{ __('admin.actions.view') }}
                                        </a>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-gray-500">{{ __('invitation.index.empty') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="border-t border-gray-200 bg-gray-50 px-4 py-3">
                    {{ $invitations->links() }}
                </div>
            </div>
        @endif
    @endif
</x-admin-layout>
