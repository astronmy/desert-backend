<x-admin-layout>
    <x-slot name="header">
        <h1 class="text-xl font-semibold text-white">{{ __('dashboard.title') }}</h1>
    </x-slot>

    <div class="rounded-lg border border-[var(--desert-sand)] bg-white p-6 shadow-sm">
        <h2 class="text-lg font-semibold text-gray-900">{{ __('dashboard.welcome', ['name' => auth()->user()->name]) }}</h2>
        <p class="mt-2 text-sm text-gray-600">{{ __('dashboard.subtitle') }}</p>
    </div>
</x-admin-layout>
