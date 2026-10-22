<x-admin-layout>
    <x-slot name="header">
        <h1 class="text-xl font-semibold text-white">{{ __('tool.title') }}</h1>
    </x-slot>

    <p class="mb-6 text-sm text-gray-600">{{ __('tool.subtitle') }}</p>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <a href="{{ route('admin.tools.list-mapping') }}" wire:navigate
           class="group rounded-lg border border-gray-200 bg-white p-5 shadow-sm transition hover:border-[var(--desert-sand)] hover:shadow">
            <span class="mb-4 flex h-11 w-11 items-center justify-center rounded-lg bg-[var(--desert-bg)] text-[var(--desert-sand)]">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </span>
            <h2 class="text-base font-semibold text-gray-900 group-hover:text-[var(--desert-bg)]">{{ __('tool.list_mapping.title') }}</h2>
            <p class="mt-2 text-sm text-gray-600">{{ __('tool.list_mapping.card') }}</p>
        </a>
    </div>
</x-admin-layout>
