@props([
    'name' => 'event_id',
    'events' => [],
    'selected' => null,
    'placeholder' => null,
    'allowClear' => true,
    'required' => false,
])

@php
    $placeholder = $placeholder ?? __('notification.form.select_event');
    $selected = $selected !== null && $selected !== '' ? (int) $selected : null;
@endphp

<div
    class="relative"
    @click.outside="eventOpen = false"
    x-data="{
        eventId: @js($selected),
        events: @js($events),
        eventOpen: false,
        eventQuery: '',
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
        selectEvent(id) {
            this.eventId = id;
            this.eventOpen = false;
            this.eventQuery = '';
            this.$dispatch('combo-event-selected', id);
        },
        clearEvent() {
            this.eventId = null;
            this.eventQuery = '';
            this.$dispatch('combo-event-selected', null);
        },
        openEventPicker() {
            this.eventOpen = true;
            this.$nextTick(() => {
                const el = this.$refs.eventSearch;
                if (el) el.focus();
            });
        }
    }"
    x-init="$watch('eventId', value => $dispatch('combo-event-selected', value))"
>
    <input type="hidden" name="{{ $name }}" :value="eventId ?? ''" @if($required) required @endif />

    <button
        type="button"
        @click="openEventPicker()"
        class="mt-1 flex w-full items-center justify-between gap-3 rounded-md border border-gray-300 bg-white px-3.5 py-2.5 text-left shadow-sm focus:border-[var(--desert-bg-elevated)] focus:outline-none focus:ring-1 focus:ring-[var(--desert-bg-elevated)]"
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
                <span class="block text-sm text-gray-400">{{ $placeholder }}</span>
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
                @keydown.enter.prevent="filteredEvents.length && selectEvent(filteredEvents[0].id)"
                @keydown.escape.prevent="eventOpen = false"
                placeholder="{{ __('notification.form.search_event_placeholder') }}"
                class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-[var(--desert-bg-elevated)] focus:ring-[var(--desert-bg-elevated)]"
            />
        </div>
        <ul class="max-h-56 overflow-y-auto py-1" role="listbox">
            <template x-if="events.length === 0">
                <li class="px-3 py-3 text-sm text-gray-500">{{ __('notification.form.no_events') }}</li>
            </template>
            <template x-if="events.length > 0 && filteredEvents.length === 0">
                <li class="px-3 py-3 text-sm text-gray-500">{{ __('notification.form.no_events_found') }}</li>
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
        @if($allowClear)
            <div class="border-t border-gray-100 p-2" x-show="eventId">
                <button type="button" @click="clearEvent()" class="text-xs font-medium text-red-600 hover:text-red-700">
                    {{ __('notification.form.clear_event') }}
                </button>
            </div>
        @endif
    </div>
</div>
