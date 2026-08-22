<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="text-xl font-semibold text-white">{{ __('role.index.title') }}</h1>
            @can('permission', 'roles.crear')
                <a href="{{ route('admin.roles.create') }}" wire:navigate
                   class="inline-flex items-center gap-2 rounded-md bg-[var(--desert-gold)] px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-[var(--desert-gold-dark)]">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    {{ __('role.index.new') }}
                </a>
            @endcan
        </div>
    </x-slot>

    @if (session('status'))
        <div class="mb-4 rounded-md bg-green-50 p-4 text-sm text-green-800">{{ session('status') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-4 rounded-md bg-red-50 p-4 text-sm text-red-800">{{ session('error') }}</div>
    @endif

    <div x-data="{
        open: false,
        confirmLabel: '',
        confirmValue: '',
        formId: '',
        openModal(label, fid) { this.confirmLabel = label; this.formId = fid; this.confirmValue = ''; this.open = true; },
        close() { this.open = false; this.confirmValue = ''; },
        submit() { if (this.confirmValue === this.confirmLabel && this.formId) { var f = document.getElementById(this.formId); if (f) f.submit(); } this.close(); }
    }">
    <div class="mb-4 rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
        <form method="GET" action="{{ route('admin.roles.index') }}" class="flex flex-wrap items-end gap-4">
            <div class="min-w-[180px]">
                <label for="filter_name" class="block text-sm font-medium text-gray-700">{{ __('role.attributes.name') }}</label>
                <input type="text" id="filter_name" name="name" value="{{ request('name') }}"
                       placeholder="{{ __('role.index.search_name_placeholder') }}"
                       class="mt-1 block w-full rounded-md border border-gray-300 px-3.5 py-2.5 shadow-sm focus:border-[var(--desert-bg-elevated)] focus:ring-[var(--desert-bg-elevated)] text-sm" />
            </div>
            <div class="flex gap-2">
                <button type="submit" class="inline-flex items-center gap-2 rounded-md bg-[var(--desert-bg-elevated)] px-3 py-2 text-sm font-medium text-white shadow-sm hover:bg-[var(--desert-bg)]">
                    {{ __('admin.actions.filter') }}
                </button>
                <a href="{{ route('admin.roles.index') }}" wire:navigate
                   class="inline-flex items-center gap-2 rounded-md border border-gray-300 bg-transparent px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    {{ __('admin.actions.clear') }}
                </a>
            </div>
        </form>
    </div>

    <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-[var(--desert-bg-elevated)]">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase text-[var(--desert-sand)]">{{ __('role.attributes.name') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase text-[var(--desert-sand)]">{{ __('role.attributes.requires_event') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase text-[var(--desert-sand)]">{{ __('role.attributes.users_count') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase text-[var(--desert-sand)]">{{ __('role.attributes.permissions_count') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase text-[var(--desert-sand)]">{{ __('role.attributes.is_active') }}</th>
                    <th class="px-4 py-3 text-right text-xs font-medium uppercase text-[var(--desert-sand)]">{{ __('admin.table.actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse($roles as $role)
                    <tr>
                        <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-gray-900">
                            {{ $role->name }}
                            @if ($role->is_system)
                                <span class="ml-1 inline-flex rounded-full bg-[var(--desert-sand)] px-2 py-0.5 text-xs font-medium text-[var(--desert-bg)]">{{ __('role.index.system') }}</span>
                            @endif
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-600">
                            {{ $role->requires_event ? __('role.index.requires_event_yes') : __('role.index.requires_event_no') }}
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-600">{{ $role->users_count }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-600">{{ $role->permissions_count }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-sm">
                            @if ($role->is_active)
                                <span class="text-emerald-700">{{ __('role.attributes.is_active') }}</span>
                            @else
                                <span class="text-red-600">{{ __('role.index.inactive') }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-2">
                                @can('permission', 'roles.editar')
                                    <a href="{{ route('admin.roles.edit', $role) }}" wire:navigate
                                       class="inline-flex shrink-0 items-center gap-1.5 rounded-md bg-[var(--desert-bg-elevated)] px-2.5 py-1.5 text-xs font-medium text-white shadow-sm hover:bg-[var(--desert-bg)]">
                                        {{ __('admin.actions.edit') }}
                                    </a>
                                @endcan
                                @can('permission', 'roles.eliminar')
                                    <form id="form-delete-role-{{ $role->id }}" action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="inline shrink-0">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button"
                                                data-confirm-label="{{ e($role->name) }}"
                                                data-form-id="form-delete-role-{{ $role->id }}"
                                                @click="openModal($event.currentTarget.getAttribute('data-confirm-label'), $event.currentTarget.getAttribute('data-form-id'))"
                                                class="inline-flex shrink-0 items-center gap-1.5 rounded-md bg-red-600 px-2.5 py-1.5 text-xs font-medium text-white shadow-sm hover:bg-red-500">
                                            {{ __('admin.actions.delete') }}
                                        </button>
                                    </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">{{ __('role.index.empty') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="flex flex-col gap-3 border-t border-gray-200 bg-gray-50 px-4 py-3 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-sm text-gray-700">
                @if($roles->total() > 0)
                    {{ __('admin.table.showing', ['from' => $roles->firstItem(), 'to' => $roles->lastItem(), 'total' => $roles->total()]) }}
                @else
                    {{ __('admin.table.no_records') }}
                @endif
            </p>
            <div>{{ $roles->links() }}</div>
        </div>
    </div>
    <x-delete-confirm-modal />
    </div>
</x-admin-layout>
