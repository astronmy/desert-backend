<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.events.index') }}" wire:navigate class="text-[var(--desert-sand)] hover:text-white">{{ __('event.index.title') }}</a>
            <span class="text-[var(--desert-muted)]">/</span>
            <h1 class="text-xl font-semibold text-white">{{ __('event.form.edit_title', ['name' => $event->name]) }}</h1>
        </div>
    </x-slot>

    <div class="rounded-lg bg-[var(--desert-surface)] p-4 sm:p-6">
        <div class="bg-white overflow-hidden rounded-lg shadow-sm">
            <form action="{{ route('admin.events.update', $event) }}" method="POST" class="p-6 space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <x-input-label for="name" :value="__('event.attributes.name')" />
                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $event->name)" required autofocus />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div class="grid gap-6 sm:grid-cols-2">
                    <div>
                        <x-input-label for="init_date" :value="__('event.attributes.init_date')" />
                        <x-text-input id="init_date" name="init_date" type="date" class="mt-1 block w-full" :value="old('init_date', $event->init_date->format('Y-m-d'))" required />
                        <x-input-error :messages="$errors->get('init_date')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="end_date" :value="__('event.attributes.end_date')" />
                        <x-text-input id="end_date" name="end_date" type="date" class="mt-1 block w-full" :value="old('end_date', $event->end_date->format('Y-m-d'))" required />
                        <x-input-error :messages="$errors->get('end_date')" class="mt-2" />
                    </div>
                </div>

                <div>
                    <x-input-label for="type" :value="__('event.attributes.type')" />
                    <x-select-input id="type" name="type" class="mt-1" required>
                        <option value="">{{ __('event.form.select_type') }}</option>
                        @foreach($types as $value => $label)
                            <option value="{{ $value }}" @selected(old('type', $event->type->value) === $value)>{{ $label }}</option>
                        @endforeach
                    </x-select-input>
                    <x-input-error :messages="$errors->get('type')" class="mt-2" />
                </div>

                <div class="flex gap-3">
                    <button type="submit" class="rounded-md bg-[var(--desert-bg-elevated)] px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-[var(--desert-bg)]">
                        {{ __('admin.actions.save_changes') }}
                    </button>
                    <a href="{{ route('admin.events.index') }}" wire:navigate class="inline-flex items-center gap-2 rounded-md bg-[var(--desert-gold-dark)] px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-[var(--desert-gold)]">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                        {{ __('admin.actions.back') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
