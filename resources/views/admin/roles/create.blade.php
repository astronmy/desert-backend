<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.roles.index') }}" wire:navigate class="text-[var(--desert-sand)] hover:text-white">{{ __('role.index.title') }}</a>
            <span class="text-[var(--desert-muted)]">/</span>
            <h1 class="text-xl font-semibold text-white">{{ __('role.form.create_title') }}</h1>
        </div>
    </x-slot>

    <div class="rounded-lg bg-[var(--desert-surface)] p-4 sm:p-6">
        <div class="overflow-hidden rounded-lg bg-white shadow-sm">
            <form action="{{ route('admin.roles.store') }}" method="POST" class="space-y-6 p-6">
                @csrf
                @include('admin.roles._form', ['permissions' => $permissions, 'selected' => old('permissions', [])])
                <div class="flex gap-3">
                    <button type="submit" class="rounded-md bg-[var(--desert-bg-elevated)] px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-[var(--desert-bg)]">
                        {{ __('role.form.create_submit') }}
                    </button>
                    <a href="{{ route('admin.roles.index') }}" wire:navigate class="inline-flex items-center gap-2 rounded-md bg-[var(--desert-gold-dark)] px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-[var(--desert-gold)]">
                        {{ __('admin.actions.back') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
