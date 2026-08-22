@php
    $selectedRole = (int) old('role_id', isset($user) ? $user->role_id : '');
    $selectedEvent = old('event_id', isset($user) ? $user->event_id : '');
@endphp

<div
    x-data="{
        roleId: @js($selectedRole ?: null),
        rolesMeta: @js($rolesMeta),
        get requiresEvent() {
            if (!this.roleId) return false;
            return !!(this.rolesMeta[this.roleId] && this.rolesMeta[this.roleId].requires_event);
        }
    }"
    class="space-y-6"
>
    <div>
        <x-input-label for="name" :value="__('user.attributes.name')" />
        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name ?? '')" required autofocus />
        <x-input-error :messages="$errors->get('name')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="email" :value="__('user.attributes.email')" />
        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email ?? '')" required />
        <x-input-error :messages="$errors->get('email')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="role_id" :value="__('user.attributes.role_id')" />
        <x-select-input id="role_id" name="role_id" class="mt-1" x-model="roleId" required>
            <option value="">{{ __('user.form.select_role') }}</option>
            @foreach ($roles as $role)
                <option value="{{ $role->id }}" @selected((int) $selectedRole === (int) $role->id)>{{ $role->name }}</option>
            @endforeach
        </x-select-input>
        <x-input-error :messages="$errors->get('role_id')" class="mt-2" />
    </div>

    <div x-show="requiresEvent" x-cloak>
        <x-input-label for="event_id" :value="__('user.attributes.event_id')" />
        <x-select-input id="event_id" name="event_id" class="mt-1">
            <option value="">{{ __('user.form.select_event') }}</option>
            @foreach ($events as $event)
                <option value="{{ $event->id }}" @selected((string) $selectedEvent === (string) $event->id)>{{ $event->name }}</option>
            @endforeach
        </x-select-input>
        <p class="mt-1 text-xs text-gray-500">{{ __('user.form.event_help') }}</p>
        <x-input-error :messages="$errors->get('event_id')" class="mt-2" />
    </div>
</div>
