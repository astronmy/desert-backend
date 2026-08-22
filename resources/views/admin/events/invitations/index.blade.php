<x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-xl font-semibold text-white">{{ __('invitation.index.title') }}</h1>
                <p class="text-sm text-[var(--desert-sand)]">{{ __('invitation.index.subtitle', ['name' => $event->name]) }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.events.accesses.index', $event) }}" wire:navigate
                   class="inline-flex items-center gap-2 rounded-md border border-[var(--desert-bg-elevated)] bg-white px-3 py-2 text-sm font-semibold text-[var(--desert-bg-elevated)] shadow-sm hover:bg-[var(--desert-sand)]">
                    {{ __('access.index.title') }}
                </a>
                <a href="{{ route('admin.events.invitations.import', $event) }}" wire:navigate
                   class="inline-flex items-center gap-2 rounded-md bg-[var(--desert-bg-elevated)] px-3 py-2 text-sm font-semibold text-white shadow-sm ring-1 ring-[var(--desert-accent)] hover:bg-[var(--desert-bg)]">
                    {{ __('invitation.index.import') }}
                </a>
                <a href="{{ route('admin.events.invitations.create', $event) }}" wire:navigate
                   class="inline-flex items-center gap-2 rounded-md bg-[var(--desert-gold)] px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-[var(--desert-gold-dark)]">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    {{ __('invitation.index.new') }}
                </a>
            </div>
        </div>
    </x-slot>

    @if (session('status'))
        <div class="mb-4 rounded-md bg-green-50 p-4 text-sm text-green-800">{{ session('status') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-4 rounded-md bg-red-50 p-4 text-sm text-red-800">{{ session('error') }}</div>
    @endif

    <div class="mb-4">
        <a href="{{ route('admin.events.index') }}" wire:navigate class="inline-flex items-center gap-2 text-sm font-medium text-[var(--desert-bg-elevated)] hover:underline">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            {{ __('admin.actions.back') }}
        </a>
    </div>

    <div x-data="{
        open: false,
        confirmLabel: '',
        confirmValue: '',
        formId: '',
        selected: [],
        toggleAll(checked, ids) { this.selected = checked ? ids : []; },
        openModal(label, fid) { this.confirmLabel = label; this.formId = fid; this.confirmValue = ''; this.open = true; },
        close() { this.open = false; this.confirmValue = ''; },
        submit() { if (this.confirmValue === this.confirmLabel && this.formId) { var f = document.getElementById(this.formId); if (f) f.submit(); } this.close(); }
    }">
    <div class="mb-4 rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
        <form method="GET" action="{{ route('admin.events.invitations.index', $event) }}" class="flex flex-wrap items-end gap-4">
            <div class="min-w-[160px]">
                <label for="filter_name" class="block text-sm font-medium text-gray-700">{{ __('guest.attributes.full_name') }}</label>
                <input type="text" id="filter_name" name="name" value="{{ request('name') }}"
                       placeholder="{{ __('invitation.index.search_name_placeholder') }}"
                       class="mt-1 block w-full rounded-md border border-gray-300 text-sm shadow-sm focus:border-[var(--desert-bg-elevated)] focus:ring-[var(--desert-bg-elevated)]" />
            </div>
            <div class="min-w-[150px]">
                <label for="filter_document" class="block text-sm font-medium text-gray-700">{{ __('guest.attributes.document_number') }}</label>
                <input type="text" id="filter_document" name="document_number" value="{{ request('document_number') }}"
                       placeholder="{{ __('invitation.index.search_document_placeholder') }}"
                       class="mt-1 block w-full rounded-md border border-gray-300 text-sm shadow-sm focus:border-[var(--desert-bg-elevated)] focus:ring-[var(--desert-bg-elevated)]" />
            </div>
            <div class="min-w-[130px]">
                <label for="filter_code" class="block text-sm font-medium text-gray-700">{{ __('invitation.attributes.code') }}</label>
                <input type="text" id="filter_code" name="code" value="{{ request('code') }}"
                       placeholder="{{ __('invitation.index.search_code_placeholder') }}"
                       class="mt-1 block w-full rounded-md border border-gray-300 text-sm shadow-sm focus:border-[var(--desert-bg-elevated)] focus:ring-[var(--desert-bg-elevated)]" />
            </div>
            <div class="min-w-[150px]">
                <label for="filter_status" class="block text-sm font-medium text-gray-700">{{ __('invitation.attributes.status') }}</label>
                <x-select-input id="filter_status" name="status" class="mt-1">
                    <option value="">{{ __('invitation.index.all_statuses') }}</option>
                    @foreach($statuses as $value => $label)
                        <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                    @endforeach
                </x-select-input>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="inline-flex items-center gap-2 rounded-md bg-[var(--desert-bg-elevated)] px-3 py-2 text-sm font-medium text-white shadow-sm hover:bg-[var(--desert-bg)]">
                    {{ __('admin.actions.filter') }}
                </button>
                <a href="{{ route('admin.events.invitations.index', $event) }}" wire:navigate
                   class="inline-flex items-center gap-2 rounded-md border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    {{ __('admin.actions.clear') }}
                </a>
            </div>
        </form>
    </div>

    <form method="POST" action="{{ route('admin.events.invitations.bulk', $event) }}" class="mb-3 flex flex-wrap items-center gap-2"
          x-show="selected.length > 0" x-cloak>
        @csrf
        <template x-for="id in selected" :key="id">
            <input type="hidden" name="ids[]" :value="id" />
        </template>
        <span class="text-sm text-gray-700" x-text="selected.length + ' {{ __('invitation.moderation.selected') }}'"></span>
        <button type="submit" name="action" value="approve"
                class="rounded-md bg-emerald-600 px-3 py-2 text-sm font-semibold text-white hover:bg-emerald-500">
            {{ __('invitation.moderation.approve_selected') }}
        </button>
        <button type="submit" name="action" value="reject"
                class="rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white hover:bg-red-500"
                onclick="return confirm(@js(__('invitation.moderation.confirm_reject')))">
            {{ __('invitation.moderation.reject_selected') }}
        </button>
    </form>

    @php $pageIds = $invitations->pluck('id')->values(); @endphp
    <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-[var(--desert-bg-elevated)]">
                <tr>
                    <th class="px-4 py-3">
                        <input type="checkbox"
                               class="rounded border-gray-300 text-[var(--desert-bg-elevated)] focus:ring-[var(--desert-bg-elevated)]"
                               @change="toggleAll($event.target.checked, {{ $pageIds }})" />
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase text-[var(--desert-sand)]">{{ __('guest.attributes.full_name') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase text-[var(--desert-sand)]">{{ __('guest.attributes.document_number') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase text-[var(--desert-sand)]">{{ __('invitation.attributes.code') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase text-[var(--desert-sand)]">{{ __('invitation.attributes.status') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase text-[var(--desert-sand)]">{{ __('invitation.attributes.confirmed_at') }}</th>
                    <th class="px-4 py-3 text-right text-xs font-medium uppercase text-[var(--desert-sand)]">{{ __('admin.table.actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse($invitations as $invitation)
                    <tr>
                        <td class="px-4 py-3">
                            <input type="checkbox" value="{{ $invitation->id }}"
                                   class="rounded border-gray-300 text-[var(--desert-bg-elevated)] focus:ring-[var(--desert-bg-elevated)]"
                                   x-model.number="selected" />
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-gray-900">{{ $invitation->guest->fullName() }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-600">
                            {{ $invitation->guest->id_type->label() }} {{ $invitation->guest->document_number }}
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-sm font-mono text-gray-900">{{ $invitation->code }}</td>
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
                        <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-600">
                            {{ $invitation->confirmed_at?->format('d/m/Y H:i') ?? '—' }}
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.events.invitations.show', [$event, $invitation]) }}" wire:navigate
                                   class="inline-flex shrink-0 items-center gap-1.5 rounded-md bg-[var(--desert-gold)] px-2.5 py-1.5 text-xs font-medium text-white shadow-sm hover:bg-[var(--desert-gold-dark)]">
                                    {{ __('admin.actions.view') }}
                                </a>
                                <a href="{{ route('admin.events.invitations.edit', [$event, $invitation]) }}" wire:navigate
                                   class="inline-flex shrink-0 items-center gap-1.5 rounded-md bg-[var(--desert-bg-elevated)] px-2.5 py-1.5 text-xs font-medium text-white shadow-sm hover:bg-[var(--desert-bg)]">
                                    {{ __('admin.actions.edit') }}
                                </a>
                                <form id="form-delete-invitation-{{ $invitation->id }}"
                                      action="{{ route('admin.events.invitations.destroy', [$event, $invitation]) }}"
                                      method="POST" class="inline shrink-0">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button"
                                            data-confirm-label="{{ e($invitation->guest->fullName()) }}"
                                            data-form-id="form-delete-invitation-{{ $invitation->id }}"
                                            @click="openModal($event.currentTarget.getAttribute('data-confirm-label'), $event.currentTarget.getAttribute('data-form-id'))"
                                            class="inline-flex shrink-0 items-center gap-1.5 rounded-md bg-red-600 px-2.5 py-1.5 text-xs font-medium text-white shadow-sm hover:bg-red-500">
                                        {{ __('admin.actions.delete') }}
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">{{ __('invitation.index.empty') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="flex flex-col gap-3 border-t border-gray-200 bg-gray-50 px-4 py-3 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-sm text-gray-700">
                @if($invitations->total() > 0)
                    {{ __('admin.table.showing', ['from' => $invitations->firstItem(), 'to' => $invitations->lastItem(), 'total' => $invitations->total()]) }}
                @else
                    {{ __('admin.table.no_records') }}
                @endif
            </p>
            <div>{{ $invitations->links() }}</div>
        </div>
    </div>
    <x-delete-confirm-modal />
    </div>
</x-admin-layout>
