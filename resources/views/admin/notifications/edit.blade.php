<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.notifications.show', $notification) }}" wire:navigate class="text-[var(--desert-sand)] hover:text-white">{{ $notification->title }}</a>
            <span class="text-[var(--desert-muted)]">/</span>
            <h1 class="text-xl font-semibold text-white">{{ __('notification.form.edit_title') }}</h1>
        </div>
    </x-slot>

    <div class="rounded-lg bg-[var(--desert-surface)] p-4 sm:p-6">
        <div class="overflow-hidden rounded-lg bg-white shadow-sm">
            <form action="{{ route('admin.notifications.update', $notification) }}" method="POST" class="space-y-6 p-6">
                @csrf
                @method('PUT')

                <div>
                    <x-input-label for="title" :value="__('notification.attributes.title')" />
                    <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title', $notification->title)" required autofocus />
                    <x-input-error :messages="$errors->get('title')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="message" :value="__('notification.attributes.message')" />
                    <textarea id="message" name="message" rows="4" required
                              class="mt-1 block w-full rounded-md border border-gray-300 px-3.5 py-2.5 text-sm shadow-sm focus:border-[var(--desert-bg-elevated)] focus:ring-[var(--desert-bg-elevated)]">{{ old('message', $notification->message) }}</textarea>
                    <x-input-error :messages="$errors->get('message')" class="mt-2" />
                </div>

                <div class="flex gap-3">
                    <button type="submit" class="rounded-md bg-[var(--desert-bg-elevated)] px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-[var(--desert-bg)]">
                        {{ __('admin.actions.save_changes') }}
                    </button>
                    <a href="{{ route('admin.notifications.show', $notification) }}" wire:navigate
                       class="inline-flex items-center gap-2 rounded-md bg-[var(--desert-gold-dark)] px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-[var(--desert-gold)]">
                        {{ __('admin.actions.back') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
