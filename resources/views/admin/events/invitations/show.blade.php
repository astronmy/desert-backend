@php
    $fromAccesses = request('from') === 'accesses';
    $backUrl = $fromAccesses
        ? route('admin.events.accesses.index', $event)
        : route('admin.events.invitations.index', $event);
    $backLabel = $fromAccesses
        ? __('access.index.title')
        : __('invitation.index.title');
@endphp
<x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-2">
                <a href="{{ $backUrl }}" wire:navigate class="text-[var(--desert-sand)] hover:text-white">{{ $backLabel }}</a>
                <span class="text-[var(--desert-muted)]">/</span>
                <h1 class="text-xl font-semibold text-white">{{ __('invitation.show.title') }}</h1>
            </div>
            <div class="flex flex-wrap gap-2">
                @unless($fromAccesses)
                    <a href="{{ route('admin.events.invitations.edit', [$event, $invitation]) }}" wire:navigate
                       class="inline-flex items-center gap-2 rounded-md bg-[var(--desert-gold)] px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-[var(--desert-gold-dark)]">
                        {{ __('admin.actions.edit') }}
                    </a>
                @endunless
                <a href="{{ $backUrl }}" wire:navigate
                   class="inline-flex items-center gap-2 rounded-md border border-white/20 bg-white/10 px-3 py-2 text-sm font-medium text-white hover:bg-white/15">
                    {{ __('admin.actions.back') }}
                </a>
            </div>
        </div>
    </x-slot>

    <p class="mb-4 text-sm text-gray-700">{{ __('invitation.index.subtitle', ['name' => $event->name]) }}</p>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="overflow-hidden rounded-xl border border-[var(--desert-sand)] bg-white shadow-sm">
            <div class="border-b border-[var(--desert-sand)] bg-[var(--desert-bg)] px-5 py-3">
                <h2 class="text-sm font-semibold uppercase tracking-wide text-[var(--desert-sand)]">{{ __('invitation.show.guest_data') }}</h2>
            </div>
            <dl class="divide-y divide-gray-100 px-5 py-2">
                <div class="flex justify-between gap-4 py-3">
                    <dt class="text-sm text-gray-500">{{ __('guest.attributes.first_name') }}</dt>
                    <dd class="text-sm font-medium text-gray-900">{{ $invitation->guest->first_name }}</dd>
                </div>
                <div class="flex justify-between gap-4 py-3">
                    <dt class="text-sm text-gray-500">{{ __('guest.attributes.last_name') }}</dt>
                    <dd class="text-sm font-medium text-gray-900">{{ $invitation->guest->last_name }}</dd>
                </div>
                <div class="flex justify-between gap-4 py-3">
                    <dt class="text-sm text-gray-500">{{ __('guest.attributes.id_type') }}</dt>
                    <dd class="text-sm font-medium text-gray-900">{{ $invitation->guest->id_type->label() }}</dd>
                </div>
                <div class="flex justify-between gap-4 py-3">
                    <dt class="text-sm text-gray-500">{{ __('guest.attributes.document_number') }}</dt>
                    <dd class="text-sm font-medium text-gray-900">{{ $invitation->guest->document_number }}</dd>
                </div>
            </dl>
        </div>

        <div class="overflow-hidden rounded-xl border border-[var(--desert-sand)] bg-white shadow-sm">
            <div class="border-b border-[var(--desert-sand)] bg-[var(--desert-bg)] px-5 py-3">
                <h2 class="text-sm font-semibold uppercase tracking-wide text-[var(--desert-sand)]">{{ __('invitation.show.invitation_data') }}</h2>
            </div>
            <dl class="divide-y divide-gray-100 px-5 py-2">
                <div class="flex justify-between gap-4 py-3">
                    <dt class="text-sm text-gray-500">{{ __('invitation.attributes.code') }}</dt>
                    <dd class="font-mono text-sm font-semibold text-gray-900">{{ $invitation->code }}</dd>
                </div>
                <div class="flex justify-between gap-4 py-3">
                    <dt class="text-sm text-gray-500">{{ __('invitation.attributes.status') }}</dt>
                    <dd>
                        <span class="inline-flex rounded-full bg-[var(--desert-sand)] px-2.5 py-0.5 text-xs font-medium text-[var(--desert-bg)]">
                            {{ $invitation->status->label() }}
                        </span>
                    </dd>
                </div>
                <div class="flex justify-between gap-4 py-3">
                    <dt class="text-sm text-gray-500">{{ __('invitation.attributes.confirmed_at') }}</dt>
                    <dd class="text-sm font-medium text-gray-900">{{ $invitation->confirmed_at?->format('d/m/Y H:i') ?? '—' }}</dd>
                </div>
            </dl>
        </div>

        <div class="overflow-hidden rounded-xl border border-[var(--desert-sand)] bg-white shadow-sm lg:col-span-2">
            <div class="border-b border-[var(--desert-sand)] bg-[var(--desert-bg)] px-5 py-3">
                <h2 class="text-sm font-semibold uppercase tracking-wide text-[var(--desert-sand)]">{{ __('invitation.attributes.selfie') }}</h2>
            </div>
            <div class="flex items-center justify-center p-6">
                @if($invitation->selfieUrl())
                    <img src="{{ $invitation->selfieUrl() }}"
                         alt="{{ __('invitation.attributes.selfie') }} — {{ $invitation->guest->fullName() }}"
                         class="max-h-[28rem] w-auto max-w-full rounded-lg object-contain shadow-md ring-1 ring-gray-200" />
                @else
                    <div class="flex w-full max-w-md flex-col items-center justify-center rounded-xl border-2 border-dashed border-gray-300 bg-gray-50 px-6 py-16 text-center">
                        <svg class="mb-3 h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 7a2 2 0 012-2h3l2-2h4l2 2h3a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V7z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15a3 3 0 100-6 3 3 0 000 6z" />
                        </svg>
                        <p class="text-sm font-medium text-gray-500">{{ __('invitation.show.no_selfie') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-admin-layout>
