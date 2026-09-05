<x-admin-layout>
    <x-slot name="header">
        <h1 class="text-xl font-semibold text-white">{{ __('dashboard.title') }}</h1>
    </x-slot>

    @if (session('status'))
        <div class="mb-4 rounded-md bg-green-50 p-4 text-sm text-green-800">{{ session('status') }}</div>
    @endif

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
        @else
            {{-- Registration short link --}}
            <div class="mb-6 overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm" x-data="{ copied: '' }">
                <div class="border-b border-gray-200 bg-[var(--desert-bg)] px-5 py-3">
                    <h3 class="text-sm font-semibold uppercase tracking-wide text-[var(--desert-sand)]">{{ __('dashboard.link.title') }}</h3>
                </div>
                <div class="space-y-3 p-5">
                    <p class="text-sm text-gray-600">{{ __('dashboard.link.help') }}</p>
                    @if ($registrationLink)
                        <div>
                            <p class="mb-1 text-xs font-medium uppercase tracking-wide text-gray-500">{{ __('dashboard.link.url') }}</p>
                            <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                                <input id="dashboard-short-url" type="text" readonly value="{{ $registrationLink->shortUrl() }}"
                                       class="w-full rounded-md border-gray-300 bg-gray-50 font-mono text-xs text-gray-800 shadow-sm" />
                                <button type="button"
                                        @click="navigator.clipboard.writeText(document.getElementById('dashboard-short-url').value).then(() => { copied = 'short'; setTimeout(() => copied = '', 2000) })"
                                        class="shrink-0 rounded-md bg-[var(--desert-bg-elevated)] px-3 py-2 text-sm font-medium text-white hover:bg-[var(--desert-bg)]">
                                    <span x-text="copied === 'short' ? '{{ __('dashboard.link.copied') }}' : '{{ __('dashboard.link.copy') }}'"></span>
                                </button>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500">
                            {{ __('dashboard.link.expires_at', ['date' => $registrationLink->expires_at->timezone(config('app.timezone'))->format('d/m/Y H:i')]) }}
                        </p>
                        @can('permission', 'deeplink.generar')
                            <form method="POST" action="{{ route('admin.dashboard.registration-link') }}"
                                  onsubmit="return confirm(@js(__('dashboard.link.regenerate_confirm')))">
                                @csrf
                                <button type="submit" class="rounded-md border border-amber-300 bg-amber-50 px-3 py-2 text-sm font-medium text-amber-900 hover:bg-amber-100">
                                    {{ __('dashboard.link.regenerate') }}
                                </button>
                            </form>
                        @endcan
                    @else
                        <p class="text-sm text-gray-500">{{ __('dashboard.link.empty') }}</p>
                        @can('permission', 'deeplink.generar')
                            <form method="POST" action="{{ route('admin.dashboard.registration-link') }}">
                                @csrf
                                <button type="submit" class="rounded-md bg-[var(--desert-gold)] px-3 py-2 text-sm font-semibold text-[var(--desert-bg)] hover:bg-[var(--desert-gold-dark)] hover:text-white">
                                    {{ __('dashboard.link.generate') }}
                                </button>
                            </form>
                        @endcan
                    @endif
                </div>
            </div>

            @if ($invitations)
                @php $canModerate = auth()->user()->canPermission('invitaciones.moderar'); @endphp

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
                            <label for="filter_name" class="block text-sm font-medium text-gray-700">Nombre</label>
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

                <div x-data="{
                    selected: [],
                    toggleAll(checked, ids) {
                        this.selected = checked ? ids.map(Number) : [];
                    }
                }">
                    @if ($canModerate)
                        <form method="POST" action="{{ route('admin.dashboard.invitations.bulk-approve') }}"
                              class="mb-3 flex flex-wrap items-center gap-2"
                              x-show="selected.length > 0" x-cloak>
                            @csrf
                            <template x-for="id in selected" :key="id">
                                <input type="hidden" name="ids[]" :value="id" />
                            </template>
                            <span class="text-sm text-gray-700" x-text="selected.length + ' {{ __('dashboard.selected') }}'"></span>
                            <button type="submit"
                                    class="rounded-md bg-emerald-600 px-3 py-2 text-sm font-semibold text-white hover:bg-emerald-500">
                                {{ __('dashboard.confirm_selected') }}
                            </button>
                        </form>
                    @endif

                    @php $pageIds = $invitations->pluck('id')->values(); @endphp
                    <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-[var(--desert-bg-elevated)]">
                                <tr>
                                    @if ($canModerate)
                                        <th class="px-4 py-3">
                                            <input type="checkbox"
                                                   class="rounded border-gray-300 text-[var(--desert-bg-elevated)] focus:ring-[var(--desert-bg-elevated)]"
                                                   @change="toggleAll($event.target.checked, {{ $pageIds }})" />
                                        </th>
                                    @endif
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
                                        @if ($canModerate)
                                            <td class="px-4 py-3">
                                                <input type="checkbox" value="{{ $invitation->id }}"
                                                       class="rounded border-gray-300 text-[var(--desert-bg-elevated)] focus:ring-[var(--desert-bg-elevated)]"
                                                       x-model.number="selected" />
                                            </td>
                                        @endif
                                        <td class="whitespace-nowrap px-4 py-3 font-mono text-sm text-gray-900">{{ $invitation->code }}</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-900">
                                            {{ $invitation->guest?->first_name }} {{ $invitation->guest?->last_name }}
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-600">{{ $invitation->guest?->document_number }}</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-sm">
                                            <span @class([
                                                'inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium',
                                                'bg-amber-100 text-amber-800' => $invitation->status->value === 'pending',
                                                'bg-emerald-100 text-emerald-800' => $invitation->status->value === 'confirmed',
                                                'bg-red-100 text-red-800' => $invitation->status->value === 'cancelled',
                                            ])>
                                                {{ $invitation->status->label() }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="flex items-center justify-end gap-2">
                                                @if ($canModerate && $invitation->status->value === 'pending')
                                                    <form method="POST" action="{{ route('admin.dashboard.invitations.approve', $invitation) }}">
                                                        @csrf
                                                        <button type="submit"
                                                                class="inline-flex items-center rounded-md bg-emerald-600 px-2.5 py-1.5 text-xs font-medium text-white hover:bg-emerald-500">
                                                            {{ __('dashboard.confirm') }}
                                                        </button>
                                                    </form>
                                                @endif
                                                @can('permission', 'invitaciones.ver')
                                                    <a href="{{ route('admin.events.invitations.show', [$event, $invitation]) }}" wire:navigate
                                                       class="inline-flex items-center rounded-md bg-[var(--desert-bg-elevated)] px-2.5 py-1.5 text-xs font-medium text-white hover:bg-[var(--desert-bg)]">
                                                        {{ __('admin.actions.view') }}
                                                    </a>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ $canModerate ? 6 : 5 }}" class="px-4 py-8 text-center text-gray-500">{{ __('invitation.index.empty') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="border-t border-gray-200 bg-gray-50 px-4 py-3">
                            {{ $invitations->links() }}
                        </div>
                    </div>
                </div>
            @endif
        @endif
    @endif
</x-admin-layout>
