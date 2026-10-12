<x-admin-layout>
    <x-slot name="header">
        <h1 class="text-xl font-semibold text-white">{{ __('setting.title') }}</h1>
    </x-slot>

    @if (session('status'))
        <div class="mb-4 rounded-md bg-green-50 p-4 text-sm text-green-800">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('admin.settings.update') }}" class="max-w-xl rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
        @csrf
        @method('PUT')

        <h2 class="text-base font-semibold text-gray-900">{{ __('setting.texts_title') }}</h2>
        <p class="mt-1 text-xs text-gray-500">{{ __('setting.placeholder_help') }}</p>

        <div class="mt-2 divide-y divide-gray-100">
            @foreach ([
                'texto_registro' => 'mensaje_registro',
                'texto_confirmacion' => 'mensaje_confirmacion',
                'texto_recordatorio' => 'mensaje_recordatorio',
            ] as $toggle => $message)
                <div class="py-4">
                    <div class="flex items-center justify-between gap-4">
                        <span class="text-sm font-medium text-gray-900">{{ __('setting.attributes.'.$toggle) }}</span>
                        <span class="relative inline-flex h-6 w-11 shrink-0">
                            <input type="checkbox" id="{{ $toggle }}" name="{{ $toggle }}" value="1" aria-label="{{ __('setting.attributes.'.$toggle) }}" class="peer absolute inset-0 z-10 h-full w-full cursor-pointer opacity-0" @checked(filter_var(old($toggle, $setting->{$toggle}), FILTER_VALIDATE_BOOLEAN)) />
                            <span class="h-6 w-11 rounded-full bg-gray-200 transition peer-checked:bg-[var(--desert-bg-elevated)] peer-focus-visible:ring-2 peer-focus-visible:ring-[var(--desert-bg-elevated)] peer-focus-visible:ring-offset-2"></span>
                            <span class="pointer-events-none absolute left-0.5 top-0.5 h-5 w-5 rounded-full bg-white shadow transition peer-checked:translate-x-5"></span>
                        </span>
                    </div>
                    <textarea id="{{ $message }}" name="{{ $message }}" rows="3"
                              class="mt-3 block w-full rounded-md border border-gray-300 px-3.5 py-2.5 text-sm shadow-sm focus:border-[var(--desert-bg-elevated)] focus:ring-[var(--desert-bg-elevated)]">{{ old($message, $setting->{$message}) }}</textarea>
                    <x-input-error :messages="$errors->get($message)" class="mt-2" />
                </div>
            @endforeach
        </div>

        <button type="submit" class="mt-2 rounded-md bg-[var(--desert-bg-elevated)] px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-[var(--desert-bg)]">
            {{ __('setting.save') }}
        </button>
    </form>
</x-admin-layout>
