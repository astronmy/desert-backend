<x-admin-layout>
    <x-slot name="header">
        <h1 class="text-xl font-semibold text-white">{{ __('event.content.title') }}</h1>
    </x-slot>

    <div class="rounded-lg bg-[var(--desert-surface)] p-4 sm:p-6">
        @if (session('status'))
            <div class="mb-4 rounded-md bg-green-50 p-4 text-sm text-green-800">{{ session('status') }}</div>
        @endif

        <div class="mb-6 overflow-hidden rounded-lg bg-white shadow-sm">
            <div class="border-b border-gray-200 bg-[var(--desert-bg)] px-5 py-3">
                <h2 class="text-sm font-semibold uppercase tracking-wide text-[var(--desert-sand)]">{{ $event->name }}</h2>
            </div>
            <div class="space-y-1 p-5">
                <p class="text-sm text-gray-600">{{ __('event.content.help') }}</p>
                <p class="text-sm text-gray-500">
                    {{ $event->init_date->format('d/m/Y') }} – {{ $event->end_date->format('d/m/Y') }}
                    @if ($event->host)
                        · {{ $event->host }}
                    @endif
                </p>
            </div>
        </div>

        <div class="overflow-hidden rounded-lg bg-white shadow-sm">
            <form action="{{ route('admin.client-event.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6 p-6">
                @csrf
                @method('PUT')

                @include('admin.events._content_media_fields', ['event' => $event])

                <div class="flex gap-3">
                    <button type="submit" class="rounded-md bg-[var(--desert-bg-elevated)] px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-[var(--desert-bg)]">
                        {{ __('admin.actions.save_changes') }}
                    </button>
                    <a href="{{ route('admin.dashboard') }}" wire:navigate class="inline-flex items-center gap-2 rounded-md bg-[var(--desert-gold-dark)] px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-[var(--desert-gold)]">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                        {{ __('admin.actions.back') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
