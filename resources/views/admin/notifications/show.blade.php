<x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.notifications.index') }}" wire:navigate class="text-[var(--desert-sand)] hover:text-white">{{ __('notification.index.title') }}</a>
                <span class="text-[var(--desert-muted)]">/</span>
                <h1 class="text-xl font-semibold text-white">{{ __('notification.show.title') }}</h1>
            </div>
            <div class="flex flex-wrap gap-2">
                @can('permission', 'notificaciones.editar')
                    @if($notification->status->value === 'pending')
                        <a href="{{ route('admin.notifications.edit', $notification) }}" wire:navigate
                           class="inline-flex items-center gap-2 rounded-md bg-[var(--desert-gold)] px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-[var(--desert-gold-dark)]">
                            {{ __('admin.actions.edit') }}
                        </a>
                    @endif
                @endcan
                <a href="{{ route('admin.notifications.index') }}" wire:navigate
                   class="inline-flex items-center gap-2 rounded-md border border-white/20 bg-white/10 px-3 py-2 text-sm font-medium text-white hover:bg-white/15">
                    {{ __('admin.actions.back') }}
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

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="overflow-hidden rounded-xl border border-[var(--desert-sand)] bg-white shadow-sm">
            <div class="border-b border-[var(--desert-sand)] bg-[var(--desert-bg)] px-5 py-3">
                <h2 class="text-sm font-semibold uppercase tracking-wide text-[var(--desert-sand)]">{{ $notification->title }}</h2>
            </div>
            <dl class="divide-y divide-gray-100 px-5 py-2">
                <div class="flex justify-between gap-4 py-3">
                    <dt class="text-sm text-gray-500">{{ __('notification.attributes.event') }}</dt>
                    <dd class="text-sm font-medium text-gray-900">{{ $notification->event?->name }}</dd>
                </div>
                <div class="flex justify-between gap-4 py-3">
                    <dt class="text-sm text-gray-500">{{ __('notification.attributes.type') }}</dt>
                    <dd class="text-sm font-medium text-gray-900">{{ __('notification.types.'.$notification->type->value) }}</dd>
                </div>
                <div class="flex justify-between gap-4 py-3">
                    <dt class="text-sm text-gray-500">{{ __('notification.attributes.scope') }}</dt>
                    <dd class="text-sm font-medium text-gray-900">{{ __('notification.scopes.'.$notification->scope->value) }}</dd>
                </div>
                <div class="flex justify-between gap-4 py-3">
                    <dt class="text-sm text-gray-500">{{ __('notification.attributes.status') }}</dt>
                    <dd class="text-sm font-medium text-gray-900">{{ __('notification.statuses.'.$notification->status->value) }}</dd>
                </div>
                <div class="flex justify-between gap-4 py-3">
                    <dt class="text-sm text-gray-500">{{ __('notification.attributes.send_at') }}</dt>
                    <dd class="text-sm font-medium text-gray-900">{{ $notification->send_at?->timezone(config('app.timezone'))->format('d/m/Y H:i') ?? '—' }}</dd>
                </div>
                <div class="flex justify-between gap-4 py-3">
                    <dt class="text-sm text-gray-500">{{ __('notification.attributes.sent_at') }}</dt>
                    <dd class="text-sm font-medium text-gray-900">{{ $notification->sent_at?->timezone(config('app.timezone'))->format('d/m/Y H:i') ?? '—' }}</dd>
                </div>
                <div class="flex justify-between gap-4 py-3">
                    <dt class="text-sm text-gray-500">{{ __('notification.attributes.external_id') }}</dt>
                    <dd class="text-sm font-medium text-gray-900">{{ $notification->external_id ?? '—' }}</dd>
                </div>
                <div class="py-3">
                    <dt class="text-sm text-gray-500">{{ __('notification.attributes.message') }}</dt>
                    <dd class="mt-1 text-sm font-medium text-gray-900 whitespace-pre-wrap">{{ $notification->message }}</dd>
                </div>
            </dl>
        </div>

        <div class="overflow-hidden rounded-xl border border-[var(--desert-sand)] bg-white shadow-sm">
            <div class="border-b border-[var(--desert-sand)] bg-[var(--desert-bg)] px-5 py-3">
                <h2 class="text-sm font-semibold uppercase tracking-wide text-[var(--desert-sand)]">{{ __('notification.show.recipients') }}</h2>
            </div>
            <div class="px-5 py-4">
                @if($notification->scope->value === 'general')
                    <p class="text-sm text-gray-600">{{ __('notification.show.no_recipients') }}</p>
                @elseif($notification->invitations->isEmpty())
                    <p class="text-sm text-gray-500">{{ __('admin.table.no_records') }}</p>
                @else
                    <ul class="divide-y divide-gray-100">
                        @foreach($notification->invitations as $invitation)
                            <li class="py-2 text-sm text-gray-800">
                                {{ $invitation->guest->first_name }} {{ $invitation->guest->last_name }}
                                <span class="text-gray-500">({{ $invitation->code }})</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
</x-admin-layout>
