@php
    $oldEventId = old('event_id', $lockedEventId);
    $oldEventId = $oldEventId !== null && $oldEventId !== '' ? (int) $oldEventId : null;
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
        <div class="overflow-hidden rounded-lg bg-white shadow-sm">
            <form action="{{ route('admin.notifications.store') }}" method="POST" class="space-y-6 p-6"
                  x-data="{
                      eventId: @js($oldEventId),
                      type: @js(old('type', 'instant')),
                      scope: @js(old('scope', 'general')),
                      invitations: [],
                      invitationQuery: '',
                      selected: @js(array_map('intval', old('invitation_ids', []))),
                      loading: false,
                      loadError: '',
                      endpointBase: @js(url('/admin/events')),
                      get filteredInvitations() {
                          const q = (this.invitationQuery || '').trim().toLowerCase();
                          if (!q) return this.invitations;
                          return this.invitations.filter(inv =>
                              (inv.guest && inv.guest.toLowerCase().includes(q)) ||
                              (inv.code && inv.code.toLowerCase().includes(q))
                          );
                      },
                      init() {
                          this.$watch('eventId', () => this.fetchInvitations());
                          this.$watch('scope', () => { if (this.scope === 'specific') this.fetchInvitations(); });
                          if (this.eventId && this.scope === 'specific') this.fetchInvitations();
                      },
                      async fetchInvitations() {
                          if (!this.eventId || this.scope !== 'specific') {
                              this.invitations = [];
                              return;
                          }
                          this.loading = true;
                          this.loadError = '';
                          try {
                              const res = await fetch(this.endpointBase + '/' + this.eventId + '/notifiable-invitations', {
                                  headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                                  credentials: 'same-origin',
                              });
                              if (!res.ok) throw new Error('HTTP ' + res.status);
                              const json = await res.json();
                              this.invitations = json.data || [];
                          } catch (e) {
                              this.loadError = @json(__('notification.form.load_error'));
                              this.invitations = [];
                          } finally {
                              this.loading = false;
                          }
                      },
                      toggle(id) {
                          const i = this.selected.indexOf(id);
                          if (i === -1) this.selected.push(id);
                          else this.selected.splice(i, 1);
                      }
                  }">
                @csrf

                <div @event-chosen="eventId = $event.detail">
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
                    <template x-if="loading">
                        <p class="text-sm text-gray-500">{{ __('notification.form.loading_invitations') }}</p>
                    </template>
                    <template x-if="!loading && loadError">
                        <p class="text-sm text-red-600" x-text="loadError"></p>
                    </template>
                    <template x-if="!loading && !loadError && eventId && invitations.length === 0">
                        <p class="text-sm text-gray-500">{{ __('notification.form.no_notifiable') }}</p>
                    </template>
                    <div x-show="!loading && invitations.length > 0" class="space-y-2">
                        <input type="search" x-model="invitationQuery"
                               placeholder="{{ __('notification.form.search_guest_placeholder') }}"
                               class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-[var(--desert-bg-elevated)] focus:ring-[var(--desert-bg-elevated)]" />
                        <div class="max-h-64 space-y-2 overflow-y-auto rounded-md border border-gray-200 p-3">
                            <template x-for="inv in filteredInvitations" :key="inv.id">
                                <label class="flex items-center gap-2 text-sm text-gray-800">
                                    <input type="checkbox" name="invitation_ids[]" :value="inv.id"
                                           :checked="selected.includes(inv.id)"
                                           @change="toggle(inv.id)"
                                           class="rounded border-gray-300 text-[var(--desert-bg-elevated)] focus:ring-[var(--desert-bg-elevated)]" />
                                    <span x-text="inv.guest + ' (' + inv.code + ')'"></span>
                                </label>
                            </template>
                            <p class="text-sm text-gray-500" x-show="filteredInvitations.length === 0">{{ __('notification.form.no_events_found') }}</p>
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
