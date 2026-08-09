<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.events.invitations.index', $event) }}" wire:navigate class="text-[var(--desert-sand)] hover:text-white">{{ __('invitation.index.title') }}</a>
            <span class="text-[var(--desert-muted)]">/</span>
            <h1 class="text-xl font-semibold text-white">{{ __('invitation.form.create_title') }}</h1>
        </div>
    </x-slot>

    <p class="mb-4 text-sm text-gray-700">{{ __('invitation.index.subtitle', ['name' => $event->name]) }}</p>

    <div class="rounded-lg bg-[var(--desert-surface)] p-4 sm:p-6">
        <div class="overflow-hidden rounded-lg bg-white shadow-sm">
            <form action="{{ route('admin.events.invitations.store', $event) }}" method="POST" class="space-y-6 p-6">
                @csrf

                <div class="grid gap-6 sm:grid-cols-2">
                    <div>
                        <x-input-label for="first_name" :value="__('guest.attributes.first_name')" />
                        <x-text-input id="first_name" name="first_name" type="text" class="mt-1 block w-full" :value="old('first_name')" required autofocus />
                        <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="last_name" :value="__('guest.attributes.last_name')" />
                        <x-text-input id="last_name" name="last_name" type="text" class="mt-1 block w-full" :value="old('last_name')" required />
                        <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
                    </div>
                </div>

                <div class="grid gap-6 sm:grid-cols-2">
                    <div>
                        <x-input-label for="id_type" :value="__('guest.attributes.id_type')" />
                        <x-select-input id="id_type" name="id_type" class="mt-1" required>
                            <option value="">{{ __('invitation.form.select_id_type') }}</option>
                            @foreach($documentTypes as $value => $label)
                                <option value="{{ $value }}" @selected(old('id_type', 'dni') === $value)>{{ $label }}</option>
                            @endforeach
                        </x-select-input>
                        <x-input-error :messages="$errors->get('id_type')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="document_number" :value="__('guest.attributes.document_number')" />
                        <x-text-input id="document_number" name="document_number" type="text" class="mt-1 block w-full" :value="old('document_number')" required />
                        <x-input-error :messages="$errors->get('document_number')" class="mt-2" />
                    </div>
                </div>

                <div class="flex gap-3">
                    <button type="submit" class="rounded-md bg-[var(--desert-bg-elevated)] px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-[var(--desert-bg)]">
                        {{ __('invitation.form.create_submit') }}
                    </button>
                    <a href="{{ route('admin.events.invitations.index', $event) }}" wire:navigate
                       class="inline-flex items-center gap-2 rounded-md bg-[var(--desert-gold-dark)] px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-[var(--desert-gold)]">
                        {{ __('admin.actions.back') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
