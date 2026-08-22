@php
    $selectedRole = old('role_id', isset($user) ? $user->role_id : null);
    $selectedRole = $selectedRole !== null && $selectedRole !== '' ? (int) $selectedRole : null;
    $selectedEvent = old('event_id', isset($user) ? $user->event_id : null);
    $selectedEvent = $selectedEvent !== null && $selectedEvent !== '' ? (int) $selectedEvent : null;
@endphp

<div
    x-data="{
        roleId: @js($selectedRole),
        eventId: @js($selectedEvent),
        rolesMeta: @js($rolesMeta),
        events: @js($eventsForSelect),
        eventOpen: false,
        eventQuery: '',
        get requiresEvent() {
            if (!this.roleId) return false;
            const meta = this.rolesMeta[this.roleId] || this.rolesMeta[String(this.roleId)];
            return !!(meta && meta.requires_event);
        },
        get selectedEvent() {
            if (!this.eventId) return null;
            return this.events.find(e => e.id === this.eventId || e.id === Number(this.eventId)) || null;
        },
        get filteredEvents() {
            const q = (this.eventQuery || '').trim().toLowerCase();
            if (!q) return this.events;
            return this.events.filter(e =>
                e.name.toLowerCase().includes(q) ||
                (e.type_label && e.type_label.toLowerCase().includes(q)) ||
                (e.dates && e.dates.toLowerCase().includes(q))
            );
        },
        selectRole(id) {
            this.roleId = id;
            if (!this.requiresEvent) {
                this.eventId = null;
                this.eventOpen = false;
                this.eventQuery = '';
            }
        },
        selectEvent(id) {
            this.eventId = id;
            this.eventOpen = false;
            this.eventQuery = '';
        },
        clearEvent() {
            this.eventId = null;
            this.eventQuery = '';
        },
        openEventPicker() {
            this.eventOpen = true;
            this.$nextTick(() => {
                const el = this.$refs.eventSearch;
                if (el) el.focus();
            });
        }
    }"
    class="space-y-6"
    @keydown.escape.window="eventOpen = false"
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

    {{-- Role cards --}}
    <div>
        <x-input-label :value="__('user.attributes.role_id')" />
        <p class="mt-1 text-xs text-gray-500">{{ __('user.form.role_help') }}</p>
        <input type="hidden" name="role_id" :value="roleId ?? ''" />
        <div class="mt-3 grid gap-3 sm:grid-cols-2" role="radiogroup" aria-label="{{ __('user.attributes.role_id') }}">
            @foreach ($roles as $role)
                <button
                    type="button"
                    role="radio"
                    :aria-checked="roleId === {{ $role->id }}"
                    @click="selectRole({{ $role->id }})"
                    :class="roleId === {{ $role->id }}
                        ? 'border-[var(--desert-bg-elevated)] bg-[var(--desert-sand)]/40 ring-2 ring-[var(--desert-bg-elevated)]'
                        : 'border-gray-200 bg-white hover:border-[var(--desert-accent)] hover:bg-gray-50'"
                    class="flex w-full flex-col items-start gap-2 rounded-lg border px-4 py-3 text-left shadow-sm transition"
                >
                    <span class="text-sm font-semibold text-gray-900">{{ $role->name }}</span>
                    @if ($role->requires_event)
                        <span class="inline-flex rounded-full bg-amber-100 px-2 py-0.5 text-xs font-medium text-amber-900">
                            {{ __('user.form.requires_event_badge') }}
                        </span>
                    @else
                        <span class="text-xs text-gray-500">{{ __('user.form.full_access_hint') }}</span>
                    @endif
                </button>
            @endforeach
        </div>
        <x-input-error :messages="$errors->get('role_id')" class="mt-2" />
    </div>

    {{-- Event combobox --}}
    <div
        x-show="requiresEvent"
        x-cloak
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 -translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="relative"
        @click.outside="eventOpen = false"
    >
        <x-input-label :value="__('user.attributes.event_id')" />
        <p class="mt-1 text-xs text-gray-500">{{ __('user.form.event_help') }}</p>
        <input type="hidden" name="event_id" :value="eventId ?? ''" />

        <button
            type="button"
            @click="openEventPicker()"
            class="mt-2 flex w-full items-center justify-between gap-3 rounded-md border border-gray-300 bg-white px-3.5 py-2.5 text-left shadow-sm focus:border-[var(--desert-bg-elevated)] focus:outline-none focus:ring-1 focus:ring-[var(--desert-bg-elevated)]"
            :class="eventOpen ? 'border-[var(--desert-bg-elevated)] ring-1 ring-[var(--desert-bg-elevated)]' : ''"
        >
            <span class="min-w-0 flex-1">
                <template x-if="selectedEvent">
                    <span class="block">
                        <span class="block truncate text-sm font-medium text-gray-900" x-text="selectedEvent.name"></span>
                        <span class="block truncate text-xs text-gray-500">
                            <span x-text="selectedEvent.dates"></span>
                            <span x-show="selectedEvent.type_label"> · </span>
                            <span x-text="selectedEvent.type_label"></span>
                        </span>
                    </span>
                </template>
                <template x-if="!selectedEvent">
                    <span class="block text-sm text-gray-400">{{ __('user.form.select_event') }}</span>
                </template>
            </span>
            <svg class="h-5 w-5 shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>

        <div
            x-show="eventOpen"
            x-cloak
            x-transition
            class="absolute z-20 mt-1 w-full overflow-hidden rounded-md border border-gray-200 bg-white shadow-lg"
        >
            <div class="border-b border-gray-100 p-2">
                <input
                    type="text"
                    x-ref="eventSearch"
                    x-model="eventQuery"
                    placeholder="{{ __('user.form.search_event_placeholder') }}"
                    class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-[var(--desert-bg-elevated)] focus:ring-[var(--desert-bg-elevated)]"
                />
            </div>
            <ul class="max-h-56 overflow-y-auto py-1" role="listbox">
                <template x-if="events.length === 0">
                    <li class="px-3 py-3 text-sm text-gray-500">{{ __('user.form.no_events') }}</li>
                </template>
                <template x-if="events.length > 0 && filteredEvents.length === 0">
                    <li class="px-3 py-3 text-sm text-gray-500">{{ __('user.form.no_events_found') }}</li>
                </template>
                <template x-for="event in filteredEvents" :key="event.id">
                    <li>
                        <button
                            type="button"
                            role="option"
                            @click="selectEvent(event.id)"
                            class="flex w-full flex-col items-start px-3 py-2.5 text-left hover:bg-[var(--desert-sand)]/50"
                            :class="eventId === event.id ? 'bg-[var(--desert-sand)]/40' : ''"
                        >
                            <span class="text-sm font-medium text-gray-900" x-text="event.name"></span>
                            <span class="text-xs text-gray-500">
                                <span x-text="event.dates"></span>
                                <span x-show="event.type_label"> · </span>
                                <span x-text="event.type_label"></span>
                            </span>
                        </button>
                    </li>
                </template>
            </ul>
            <div class="border-t border-gray-100 p-2" x-show="eventId">
                <button type="button" @click="clearEvent()" class="text-xs font-medium text-red-600 hover:text-red-700">
                    {{ __('user.form.clear_event') }}
                </button>
            </div>
        </div>
        <x-input-error :messages="$errors->get('event_id')" class="mt-2" />
    </div>
</div>
