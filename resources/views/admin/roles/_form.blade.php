@php
    /** @var \Illuminate\Support\Collection<string, \Illuminate\Support\Collection<int, \App\Models\Permission>> $permissions */
    $selected = $selected ?? old('permissions', []);
@endphp

<div class="space-y-4">
    <div>
        <x-input-label for="name" :value="__('role.attributes.name')" />
        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $role->name ?? '')" required autofocus />
        <x-input-error :messages="$errors->get('name')" class="mt-2" />
        @isset($role)
            @if ($role->is_system)
                <p class="mt-1 text-xs text-amber-700">{{ __('role.form.system_locked') }}</p>
            @endif
        @endisset
    </div>

    <div class="flex flex-wrap gap-6">
        <label class="inline-flex items-center gap-2 text-sm text-gray-700">
            <input type="checkbox" name="requires_event" value="1" class="rounded border-gray-300 text-[var(--desert-bg-elevated)] focus:ring-[var(--desert-bg-elevated)]"
                   @checked(old('requires_event', $role->requires_event ?? false)) />
            {{ __('role.attributes.requires_event') }}
        </label>
        <label class="inline-flex items-center gap-2 text-sm text-gray-700">
            <input type="hidden" name="is_active" value="0" />
            <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300 text-[var(--desert-bg-elevated)] focus:ring-[var(--desert-bg-elevated)]"
                   @checked(old('is_active', $role->is_active ?? true)) />
            {{ __('role.attributes.is_active') }}
        </label>
    </div>
    <p class="text-xs text-gray-500">{{ __('role.form.requires_event_help') }}</p>

    <div>
        <p class="text-sm font-medium text-gray-900">{{ __('role.attributes.permissions') }}</p>
        <p class="mt-1 text-xs text-gray-500">{{ __('role.form.permissions_help') }}</p>
        <div class="mt-3 space-y-4">
            @foreach ($permissions as $module => $items)
                <div class="rounded-md border border-gray-200 p-4">
                    <p class="mb-2 text-sm font-semibold text-gray-800">{{ __('role.modules.'.$module) }}</p>
                    <div class="grid gap-2 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($items as $permission)
                            <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                                <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                                       class="rounded border-gray-300 text-[var(--desert-bg-elevated)] focus:ring-[var(--desert-bg-elevated)]"
                                       @checked(in_array($permission->id, $selected, true)) />
                                {{ $permission->label ?: $permission->action }}
                            </label>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
        <x-input-error :messages="$errors->get('permissions')" class="mt-2" />
    </div>
</div>
