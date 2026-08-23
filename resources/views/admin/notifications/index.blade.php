<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="text-xl font-semibold text-white">{{ __('notification.index.title') }}</h1>
            @can('permission', 'notificaciones.crear')
                <a href="{{ route('admin.notifications.create') }}" wire:navigate
                   class="inline-flex items-center gap-2 rounded-md bg-[var(--desert-gold)] px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-[var(--desert-gold-dark)]">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    {{ __('notification.index.new') }}
                </a>
            @endcan
        </div>
    </x-slot>

    @if (session('status'))
        <div class="mb-4 rounded-md bg-green-50 p-4 text-sm text-green-800">{{ session('status') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-4 rounded-md bg-red-50 p-4 text-sm text-red-800">{{ session('error') }}</div>
    @endif

    <div x-data="{
        open: false,
        confirmLabel: '',
        confirmValue: '',
        formId: '',
        openModal(label, fid) { this.confirmLabel = label; this.formId = fid; this.confirmValue = ''; this.open = true; },
        close() { this.open = false; this.confirmValue = ''; },
        submit() { if (this.confirmValue === this.confirmLabel && this.formId) { var f = document.getElementById(this.formId); if (f) f.submit(); } this.close(); }
    }">
        @if($eventsForSelect !== [])
            <div class="mb-4 rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
                <form method="GET" action="{{ route('admin.notifications.index') }}" class="flex flex-wrap items-end gap-4">
                    <div class="min-w-[260px] flex-1">
                        <label class="block text-sm font-medium text-gray-700">{{ __('notification.attributes.event') }}</label>
                        <x-event-combobox
                            name="event_id"
                            :events="$eventsForSelect"
                            :selected="request('event_id')"
                            :placeholder="__('notification.index.all_events')"
                            :allow-clear="true"
                        />
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="inline-flex items-center gap-2 rounded-md bg-[var(--desert-bg-elevated)] px-3 py-2 text-sm font-medium text-white shadow-sm hover:bg-[var(--desert-bg)]">
                            {{ __('admin.actions.filter') }}
                        </button>
                        <a href="{{ route('admin.notifications.index') }}" wire:navigate
                           class="inline-flex items-center gap-2 rounded-md border border-gray-300 bg-transparent px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                            {{ __('admin.actions.clear') }}
                        </a>
                    </div>
                </form>
            </div>
        @endif

        <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-[var(--desert-bg-elevated)]">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase text-[var(--desert-sand)]">{{ __('notification.attributes.event') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase text-[var(--desert-sand)]">{{ __('notification.attributes.title') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase text-[var(--desert-sand)]">{{ __('notification.attributes.type') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase text-[var(--desert-sand)]">{{ __('notification.attributes.scope') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase text-[var(--desert-sand)]">{{ __('notification.attributes.status') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase text-[var(--desert-sand)]">{{ __('notification.attributes.send_at') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase text-[var(--desert-sand)]">{{ __('notification.attributes.sent_at') }}</th>
                        <th class="px-4 py-3 text-right text-xs font-medium uppercase text-[var(--desert-sand)]">{{ __('admin.table.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($notifications as $notification)
                        <tr>
                            <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-700">{{ $notification->event?->name }}</td>
                            <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $notification->title }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-600">{{ __('notification.types.'.$notification->type->value) }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-600">{{ __('notification.scopes.'.$notification->scope->value) }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-600">{{ __('notification.statuses.'.$notification->status->value) }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-600">{{ $notification->send_at?->timezone(config('app.timezone'))->format('d/m/Y H:i') ?? '—' }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-600">{{ $notification->sent_at?->timezone(config('app.timezone'))->format('d/m/Y H:i') ?? '—' }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.notifications.show', $notification) }}" wire:navigate
                                       class="inline-flex shrink-0 items-center gap-1.5 rounded-md bg-[var(--desert-bg-elevated)] px-2.5 py-1.5 text-xs font-medium text-white shadow-sm hover:bg-[var(--desert-bg)]">
                                        {{ __('admin.actions.view') }}
                                    </a>
                                    @can('permission', 'notificaciones.editar')
                                        @if($notification->status->value === 'pending')
                                            <a href="{{ route('admin.notifications.edit', $notification) }}" wire:navigate
                                               class="inline-flex shrink-0 items-center gap-1.5 rounded-md bg-[var(--desert-gold)] px-2.5 py-1.5 text-xs font-medium text-white shadow-sm hover:bg-[var(--desert-gold-dark)]">
                                                {{ __('admin.actions.edit') }}
                                            </a>
                                        @endif
                                    @endcan
                                    @can('permission', 'notificaciones.eliminar')
                                        <form id="form-delete-notification-{{ $notification->id }}"
                                              action="{{ route('admin.notifications.destroy', $notification) }}"
                                              method="POST" class="inline shrink-0">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button"
                                                    data-confirm-label="{{ e($notification->title) }}"
                                                    data-form-id="form-delete-notification-{{ $notification->id }}"
                                                    @click="openModal($event.currentTarget.getAttribute('data-confirm-label'), $event.currentTarget.getAttribute('data-form-id'))"
                                                    class="inline-flex shrink-0 items-center gap-1.5 rounded-md bg-red-600 px-2.5 py-1.5 text-xs font-medium text-white shadow-sm hover:bg-red-500">
                                                {{ __('admin.actions.delete') }}
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-gray-500">{{ __('notification.index.empty') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="flex flex-col gap-3 border-t border-gray-200 bg-gray-50 px-4 py-3 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-sm text-gray-700">
                    @if($notifications->total() > 0)
                        {{ __('admin.table.showing', ['from' => $notifications->firstItem(), 'to' => $notifications->lastItem(), 'total' => $notifications->total()]) }}
                    @else
                        {{ __('admin.table.no_records') }}
                    @endif
                </p>
                <div>{{ $notifications->links() }}</div>
            </div>
        </div>
        <x-delete-confirm-modal />
    </div>
</x-admin-layout>
