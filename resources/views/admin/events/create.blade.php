<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.events.index') }}" wire:navigate class="text-[var(--desert-sand)] hover:text-white">{{ __('event.index.title') }}</a>
            <span class="text-[var(--desert-muted)]">/</span>
            <h1 class="text-xl font-semibold text-white">{{ __('event.form.create_title') }}</h1>
        </div>
    </x-slot>

    <div class="rounded-lg bg-[var(--desert-surface)] p-4 sm:p-6">
        <div class="bg-white overflow-hidden rounded-lg shadow-sm">
            <form action="{{ route('admin.events.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
                @csrf

                <div>
                    <x-input-label for="name" :value="__('event.attributes.name')" />
                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required autofocus />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="host" :value="__('event.attributes.host')" />
                    <x-text-input id="host" name="host" type="text" class="mt-1 block w-full" :value="old('host')" />
                    <x-input-error :messages="$errors->get('host')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="short_description" :value="__('event.attributes.short_description')" />
                    <textarea id="short_description" name="short_description" rows="2" maxlength="500"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[var(--desert-bg-elevated)] focus:ring-[var(--desert-bg-elevated)]">{{ old('short_description') }}</textarea>
                    <p class="mt-1 text-xs text-gray-500">{{ __('event.form.short_description_hint') }}</p>
                    <x-input-error :messages="$errors->get('short_description')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="description" :value="__('event.attributes.description')" />
                    <textarea id="description" name="description" rows="5"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[var(--desert-bg-elevated)] focus:ring-[var(--desert-bg-elevated)]">{{ old('description') }}</textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                </div>

                <div class="grid gap-6 sm:grid-cols-2">
                    <div>
                        <x-input-label for="init_date" :value="__('event.attributes.init_date')" />
                        <x-text-input id="init_date" name="init_date" type="date" class="mt-1 block w-full" :value="old('init_date')" required />
                        <x-input-error :messages="$errors->get('init_date')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="end_date" :value="__('event.attributes.end_date')" />
                        <x-text-input id="end_date" name="end_date" type="date" class="mt-1 block w-full" :value="old('end_date')" required />
                        <x-input-error :messages="$errors->get('end_date')" class="mt-2" />
                    </div>
                </div>

                <div class="grid gap-6 sm:grid-cols-2">
                    <div>
                        <x-input-label for="type" :value="__('event.attributes.type')" />
                        <x-select-input id="type" name="type" class="mt-1" required>
                            <option value="">{{ __('event.form.select_type') }}</option>
                            @foreach($types as $value => $label)
                                <option value="{{ $value }}" @selected(old('type') === $value)>{{ $label }}</option>
                            @endforeach
                        </x-select-input>
                        <x-input-error :messages="$errors->get('type')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="place" :value="__('event.attributes.place')" />
                        <x-select-input id="place" name="place" class="mt-1" required>
                            <option value="">{{ __('event.form.select_place') }}</option>
                            @foreach($places as $value => $label)
                                <option value="{{ $value }}" @selected(old('place') === $value)>{{ $label }}</option>
                            @endforeach
                        </x-select-input>
                        <x-input-error :messages="$errors->get('place')" class="mt-2" />
                    </div>
                </div>

                <div class="border-t border-gray-200 pt-6 space-y-6">
                    <h2 class="text-sm font-semibold text-gray-900">{{ __('event.form.media_section') }}</h2>

                    <div class="grid gap-6 sm:grid-cols-2">
                        <div>
                            <x-input-label for="image" :value="__('event.attributes.image')" />
                            <input id="image" name="image" type="file" accept="image/*"
                                   class="mt-1 block w-full text-sm text-gray-700 file:mr-3 file:rounded-md file:border-0 file:bg-[var(--desert-bg-elevated)] file:px-3 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-[var(--desert-bg)]" />
                            <p class="mt-1 text-xs text-gray-500">{{ __('event.form.image_hint') }}</p>
                            <x-input-error :messages="$errors->get('image')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="mobile_image" :value="__('event.attributes.mobile_image')" />
                            <input id="mobile_image" name="mobile_image" type="file" accept="image/*"
                                   class="mt-1 block w-full text-sm text-gray-700 file:mr-3 file:rounded-md file:border-0 file:bg-[var(--desert-bg-elevated)] file:px-3 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-[var(--desert-bg)]" />
                            <p class="mt-1 text-xs text-gray-500">{{ __('event.form.mobile_image_hint') }}</p>
                            <x-input-error :messages="$errors->get('mobile_image')" class="mt-2" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="gallery" :value="__('event.attributes.gallery')" />
                        <input id="gallery" name="gallery[]" type="file" accept="image/*" multiple
                               class="mt-1 block w-full text-sm text-gray-700 file:mr-3 file:rounded-md file:border-0 file:bg-[var(--desert-bg-elevated)] file:px-3 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-[var(--desert-bg)]" />
                        <p class="mt-1 text-xs text-gray-500">{{ __('event.form.gallery_hint') }}</p>
                        <x-input-error :messages="$errors->get('gallery')" class="mt-2" />
                        <x-input-error :messages="$errors->get('gallery.*')" class="mt-2" />
                    </div>
                </div>

                <div class="flex gap-3">
                    <button type="submit" class="rounded-md bg-[var(--desert-bg-elevated)] px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-[var(--desert-bg)]">
                        {{ __('event.form.create_submit') }}
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
