@php
    /** @var \App\Models\Event|null $event */
    $event = $event ?? null;
@endphp

<div>
    <x-input-label for="short_description" :value="__('event.attributes.short_description')" />
    <textarea id="short_description" name="short_description" rows="2" maxlength="500"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[var(--desert-bg-elevated)] focus:ring-[var(--desert-bg-elevated)]">{{ old('short_description', $event?->short_description) }}</textarea>
    <p class="mt-1 text-xs text-gray-500">{{ __('event.form.short_description_hint') }}</p>
    <x-input-error :messages="$errors->get('short_description')" class="mt-2" />
</div>

<div>
    <x-input-label for="description" :value="__('event.attributes.description')" />
    <textarea id="description" name="description" rows="5"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[var(--desert-bg-elevated)] focus:ring-[var(--desert-bg-elevated)]">{{ old('description', $event?->description) }}</textarea>
    <x-input-error :messages="$errors->get('description')" class="mt-2" />
</div>

<div class="border-t border-gray-200 pt-6 space-y-6">
    <h2 class="text-sm font-semibold text-gray-900">{{ __('event.form.media_section') }}</h2>

    <div class="grid gap-6 sm:grid-cols-2">
        <div>
            <x-input-label for="image" :value="__('event.attributes.image')" />
            @if ($event?->imageUrl())
                <div class="mt-2 mb-3">
                    <img src="{{ $event->imageUrl() }}" alt="" class="h-28 w-auto rounded-md object-cover ring-1 ring-gray-200" />
                    <label class="mt-2 flex items-center gap-2 text-sm text-gray-700">
                        <input type="checkbox" name="remove_image" value="1" class="rounded border-gray-300 text-[var(--desert-bg-elevated)] focus:ring-[var(--desert-bg-elevated)]" />
                        {{ __('event.form.remove_image') }}
                    </label>
                </div>
            @endif
            <input id="image" name="image" type="file" accept="image/*"
                   class="mt-1 block w-full text-sm text-gray-700 file:mr-3 file:rounded-md file:border-0 file:bg-[var(--desert-bg-elevated)] file:px-3 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-[var(--desert-bg)]" />
            <p class="mt-1 text-xs text-gray-500">{{ __('event.form.image_hint') }}</p>
            <x-input-error :messages="$errors->get('image')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="mobile_image" :value="__('event.attributes.mobile_image')" />
            @if ($event?->mobileImageUrl())
                <div class="mt-2 mb-3">
                    <img src="{{ $event->mobileImageUrl() }}" alt="" class="h-28 w-auto rounded-md object-cover ring-1 ring-gray-200" />
                    <label class="mt-2 flex items-center gap-2 text-sm text-gray-700">
                        <input type="checkbox" name="remove_mobile_image" value="1" class="rounded border-gray-300 text-[var(--desert-bg-elevated)] focus:ring-[var(--desert-bg-elevated)]" />
                        {{ __('event.form.remove_mobile_image') }}
                    </label>
                </div>
            @endif
            <input id="mobile_image" name="mobile_image" type="file" accept="image/*"
                   class="mt-1 block w-full text-sm text-gray-700 file:mr-3 file:rounded-md file:border-0 file:bg-[var(--desert-bg-elevated)] file:px-3 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-[var(--desert-bg)]" />
            <p class="mt-1 text-xs text-gray-500">{{ __('event.form.mobile_image_hint') }}</p>
            <x-input-error :messages="$errors->get('mobile_image')" class="mt-2" />
        </div>
    </div>

    <div>
        <x-input-label for="gallery" :value="__('event.attributes.gallery')" />
        @if ($event && $event->images->isNotEmpty())
            <div class="mt-3 mb-4 grid grid-cols-2 gap-3 sm:grid-cols-4">
                @foreach ($event->images as $image)
                    <label class="relative block overflow-hidden rounded-md ring-1 ring-gray-200">
                        <img src="{{ $image->url() }}" alt="" class="h-28 w-full object-cover" />
                        <span class="absolute inset-x-0 bottom-0 flex items-center gap-2 bg-black/60 px-2 py-1.5 text-xs text-white">
                            <input type="checkbox" name="delete_gallery[]" value="{{ $image->id }}"
                                   class="rounded border-gray-300 text-red-600 focus:ring-red-500" />
                            {{ __('event.form.delete_gallery_item') }}
                        </span>
                    </label>
                @endforeach
            </div>
        @endif
        <input id="gallery" name="gallery[]" type="file" accept="image/*" multiple
               class="mt-1 block w-full text-sm text-gray-700 file:mr-3 file:rounded-md file:border-0 file:bg-[var(--desert-bg-elevated)] file:px-3 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-[var(--desert-bg)]" />
        <p class="mt-1 text-xs text-gray-500">{{ __('event.form.gallery_hint') }}</p>
        <x-input-error :messages="$errors->get('gallery')" class="mt-2" />
        <x-input-error :messages="$errors->get('gallery.*')" class="mt-2" />
        <x-input-error :messages="$errors->get('delete_gallery')" class="mt-2" />
    </div>
</div>
