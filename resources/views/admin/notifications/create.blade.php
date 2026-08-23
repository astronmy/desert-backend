@php
    $oldEventId = old('event_id', $lockedEventId);
    $oldEventId = $oldEventId !== null && $oldEventId !== '' ? (int) $oldEventId : null;
    $createForm = [
        'eventId' => $oldEventId,
        'type' => old('type', 'instant'),
        'scope' => old('scope', 'general'),
        'selected' => array_map('intval', old('invitation_ids', [])),
        'endpointTemplate' => route('admin.events.notifiable-invitations', ['event' => '__EVENT__']),
        'loadErrorMessage' => __('notification.form.load_error'),
    ];
@endphp
<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.notifications.index') }}" wire:navigate class="text-[var(--desert-sand)] hover:text-white">{{ __('notification.index.title') }}</a>
            <span class="text-[var(--desert-muted)]">/</span>
            <h1 class="text-xl font-semibold text-white">{{ __('notification.form.create_title') }}</h1>
        </div>
    </x-slot>

    <div class="rounded-lg bg-[var(--desert-surface)] p-4 sm:p-6">
        <div class="overflow-visible rounded-lg bg-white shadow-sm">
            <form action="{{ route('admin.notifications.store') }}" method="POST" class="space-y-6 p-6"
                  x-data="notificationCreateForm(@js($createForm))"
                @csrf

                <div @combo-event-selected="onEventSelected($event.detail)">
                    <x-input-label for="event_id" :value="__('notification.attributes.event')" />
                    @if($lockedEventId)
                        <input type="hidden" name="event_id" value="{{ $lockedEventId }}" />
                        <p class="mt-1 text-sm font-medium text-gray-900">{{ $lockedEventName }}</p>
                    @else
                        <x-event-combobox
                            name="event_id"
                            :events="$eventsForSelect"
                            :selected="$oldEventId"
                            :placeholder="__('notification.form.select_event')"
                            :allow-clear="true"
                            :required="true"
                        />
                    @endif
                    <x-input-error :messages="$errors->get('event_id')" class="mt-2" />
                </div>

                <div class="grid gap-6 sm:grid-cols-2">
                    <div>
                        <x-input-label for="type" :value="__('notification.attributes.type')" />
                        <select id="type" name="type" x-model="type" required
                                class="mt-1 block w-full rounded-md border border-gray-300 px-3.5 py-2.5 text-sm shadow-sm focus:border-[var(--desert-bg-elevated)] focus:ring-[var(--desert-bg-elevated)]">
                            <option value="instant">{{ __('notification.types.instant') }}</option>
                            <option value="scheduled">{{ __('notification.types.scheduled') }}</option>
                        </select>
                        <x-input-error :messages="$errors->get('type')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="scope" :value="__('notification.attributes.scope')" />
                        <select id="scope" name="scope" x-model="scope" required
                                class="mt-1 block w-full rounded-md border border-gray-300 px-3.5 py-2.5 text-sm shadow-sm focus:border-[var(--desert-bg-elevated)] focus:ring-[var(--desert-bg-elevated)]">
                            <option value="general">{{ __('notification.scopes.general') }}</option>
                            <option value="specific">{{ __('notification.scopes.specific') }}</option>
                        </select>
                        <x-input-error :messages="$errors->get('scope')" class="mt-2" />
                    </div>
                </div>

                <div x-show="type === 'scheduled'" x-cloak>
                    <x-input-label for="send_at" :value="__('notification.attributes.send_at')" />
                    <input id="send_at" type="datetime-local" :name="type === 'scheduled' ? 'send_at' : null"
                           value="{{ old('send_at') }}"
                           class="mt-1 block w-full rounded-md border border-gray-300 px-3.5 py-2.5 text-sm shadow-sm focus:border-[var(--desert-bg-elevated)] focus:ring-[var(--desert-bg-elevated)]" />
                    <p class="mt-1 text-xs text-gray-500">{{ __('notification.form.send_at_help') }}</p>
                    <x-input-error :messages="$errors->get('send_at')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="title" :value="__('notification.attributes.title')" />
                    <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title')" required />
                    <x-input-error :messages="$errors->get('title')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="message" :value="__('notification.attributes.message')" />
                    <textarea id="message" name="message" rows="4" required
                              class="mt-1 block w-full rounded-md border border-gray-300 px-3.5 py-2.5 text-sm shadow-sm focus:border-[var(--desert-bg-elevated)] focus:ring-[var(--desert-bg-elevated)]">{{ old('message') }}</textarea>
                    <x-input-error :messages="$errors->get('message')" class="mt-2" />
                </div>

                <div x-show="scope === 'specific'" x-cloak class="space-y-3">
                    <x-input-label :value="__('notification.attributes.invitation_ids')" />
                    <p class="text-xs text-gray-500">{{ __('notification.form.specific_help') }}</p>

                    <template x-for="id in selected" :key="'sel-'+id">
                        <input type="hidden" :name="scope === 'specific' ? 'invitation_ids[]' : null" :value="id" />
                    </template>

                    <p class="text-sm text-gray-500" x-show="!eventId">{{ __('notification.form.pick_event_first') }}</p>
                    <p class="text-sm text-gray-500" x-show="eventId && loading">{{ __('notification.form.loading_invitations') }}</p>
                    <p class="text-sm text-red-600" x-show="eventId && !loading && loadError" x-text="loadError"></p>
                    <p class="text-sm text-gray-500" x-show="eventId && !loading && !loadError && invitations.length === 0">{{ __('notification.form.no_notifiable') }}</p>

                    <div x-show="eventId && !loading && invitations.length > 0" class="space-y-2">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <input type="search" x-model="invitationQuery"
                                   placeholder="{{ __('notification.form.search_guest_placeholder') }}"
                                   class="min-w-[220px] flex-1 rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-[var(--desert-bg-elevated)] focus:ring-[var(--desert-bg-elevated)]" />
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="text-xs text-gray-500"><span x-text="selected.length"></span> {{ __('notification.form.selected_label') }}</span>
                                <button type="button" @click="selectAllVisible()"
                                        class="rounded-md border border-gray-300 px-2.5 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50">
                                    {{ __('notification.form.select_all_visible') }}
                                </button>
                                <button type="button" @click="clearSelection()" x-show="selected.length > 0"
                                        class="rounded-md border border-gray-300 px-2.5 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50">
                                    {{ __('notification.form.clear_selection') }}
                                </button>
                            </div>
                        </div>
                        <div class="max-h-80 divide-y divide-gray-100 overflow-y-auto rounded-md border border-gray-200">
                            <template x-for="inv in filteredInvitations" :key="inv.id">
                                <label class="flex cursor-pointer items-start gap-3 px-3 py-2.5 hover:bg-[var(--desert-sand)]/40">
                                    <input type="checkbox" :value="inv.id" x-model.number="selected"
                                           class="mt-1 rounded border-gray-300 text-[var(--desert-bg-elevated)] focus:ring-[var(--desert-bg-elevated)]" />
                                    <span class="min-w-0">
                                        <span class="block text-sm font-medium text-gray-900" x-text="inv.guest"></span>
                                        <span class="block text-xs text-gray-500">
                                            <span x-text="inv.document"></span>
                                            <span x-show="inv.document"> · </span>
                                            <span x-text="inv.code"></span>
                                        </span>
                                        <span class="block truncate font-mono text-xs text-gray-400" x-text="inv.uuid"></span>
                                    </span>
                                </label>
                            </template>
                            <p class="px-3 py-3 text-sm text-gray-500" x-show="filteredInvitations.length === 0">{{ __('notification.form.no_guests_found') }}</p>
                        </div>
                    </div>
                    <x-input-error :messages="$errors->get('invitation_ids')" class="mt-2" />
                </div>

                <div class="flex gap-3">
                    <button type="submit" class="rounded-md bg-[var(--desert-bg-elevated)] px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-[var(--desert-bg)]">
                        {{ __('notification.form.create_submit') }}
                    </button>
                    <a href="{{ route('admin.notifications.index') }}" wire:navigate
                       class="inline-flex items-center gap-2 rounded-md bg-[var(--desert-gold-dark)] px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-[var(--desert-gold)]">
                        {{ __('admin.actions.back') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
