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

        <div class="mt-4 divide-y divide-gray-100">
            @foreach (['texto_registro', 'texto_confirmacion', 'texto_recordatorio'] as $field)
                <label for="{{ $field }}" class="flex items-center justify-between gap-4 py-4">
                    <span class="text-sm font-medium text-gray-900">{{ __('setting.attributes.'.$field) }}</span>
                    <span class="relative inline-flex shrink-0">
                        <input type="checkbox" id="{{ $field }}" name="{{ $field }}" value="1" class="peer sr-only" @checked($setting->{$field}) />
                        <span class="h-6 w-11 rounded-full bg-gray-200 transition peer-checked:bg-[var(--desert-bg-elevated)] peer-focus-visible:ring-2 peer-focus-visible:ring-[var(--desert-bg-elevated)] peer-focus-visible:ring-offset-2"></span>
                        <span class="pointer-events-none absolute left-0.5 top-0.5 h-5 w-5 rounded-full bg-white shadow transition peer-checked:translate-x-5"></span>
                    </span>
                </label>
            @endforeach
        </div>

        <button type="submit" class="mt-2 rounded-md bg-[var(--desert-bg-elevated)] px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-[var(--desert-bg)]">
            {{ __('setting.save') }}
        </button>
    </form>
</x-admin-layout>
