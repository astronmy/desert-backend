<div
    x-show="clientOpen"
    x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
    style="display: none;"
    @keydown.escape.window="if (clientOpen) closeClientModal()"
>
    <div class="absolute inset-0 bg-black/50" @click="closeClientModal()"></div>
    <div class="relative w-full max-w-lg rounded-lg bg-white shadow-xl" @click.stop>
        <div class="flex items-start justify-between border-b border-gray-200 bg-[var(--desert-bg)] px-5 py-3">
            <div>
                <h2 class="text-sm font-semibold uppercase tracking-wide text-[var(--desert-sand)]">
                    {{ __('event.client.title') }}
                </h2>
                <p class="mt-1 text-xs text-[var(--desert-accent)]" x-text="clientEventName"></p>
            </div>
            <button type="button" @click="closeClientModal()" class="text-[var(--desert-muted)] hover:text-white" aria-label="{{ __('admin.actions.close') }}">&times;</button>
        </div>
        <div class="space-y-4 p-5">
            <template x-if="clientLoading">
                <p class="text-sm text-gray-500">{{ __('event.deeplink.loading') }}</p>
            </template>
            <template x-if="clientError">
                <p class="text-sm text-red-600" x-text="clientError"></p>
            </template>

            <template x-if="!clientLoading && !clientExists && !clientShowForm">
                <div class="space-y-3">
                    <p class="text-sm text-gray-600">{{ __('event.client.not_created') }}</p>
                    @can('permission', 'usuarios.crear')
                        <button type="button"
                                @click="startCreateClient()"
                                class="rounded-md bg-[var(--desert-gold)] px-3 py-2 text-sm font-semibold text-[var(--desert-bg)] hover:bg-[var(--desert-gold-dark)] hover:text-white">
                            {{ __('event.client.create') }}
                        </button>
                    @endcan
                </div>
            </template>

            <template x-if="!clientLoading && !clientExists && clientShowForm">
                <form class="space-y-3" @submit.prevent="createClient()">
                    <div>
                        <label class="mb-1 block text-xs font-medium uppercase tracking-wide text-gray-500">{{ __('event.client.email') }}</label>
                        <input type="email" required x-model="clientEmail"
                               class="w-full rounded-md border-gray-300 font-mono text-sm text-gray-800 shadow-sm" />
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-medium uppercase tracking-wide text-gray-500">{{ __('event.client.password') }}</label>
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                            <input type="text" x-model="clientPassword" autocomplete="new-password"
                                   class="w-full rounded-md border-gray-300 font-mono text-sm text-gray-800 shadow-sm" />
                            <button type="button"
                                    @click="autogeneratePassword()"
                                    class="shrink-0 rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                                {{ __('event.client.generate') }}
                            </button>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">{{ __('event.client.password_help') }}</p>
                    </div>
                    <div class="flex flex-wrap gap-2 pt-1">
                        <button type="submit"
                                :disabled="clientSaving"
                                class="rounded-md bg-[var(--desert-gold)] px-3 py-2 text-sm font-semibold text-[var(--desert-bg)] hover:bg-[var(--desert-gold-dark)] hover:text-white disabled:opacity-60">
                            <span x-text="clientSaving ? '{{ __('event.client.creating') }}' : '{{ __('event.client.create') }}'"></span>
                        </button>
                        <button type="button"
                                @click="closeClientModal()"
                                class="rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                            {{ __('admin.actions.close') }}
                        </button>
                    </div>
                </form>
            </template>

            <template x-if="!clientLoading && clientExists">
                <div class="space-y-3">
                    <div>
                        <p class="mb-1 text-xs font-medium uppercase tracking-wide text-gray-500">{{ __('event.client.email') }}</p>
                        <input type="text" readonly :value="clientEmail"
                               class="w-full rounded-md border-gray-300 bg-gray-50 font-mono text-sm text-gray-800 shadow-sm" />
                    </div>
                    <div>
                        <p class="mb-1 text-xs font-medium uppercase tracking-wide text-gray-500">{{ __('event.client.password') }}</p>
                        <input type="text" readonly
                               :value="clientPassword"
                               placeholder="{{ __('event.client.password_placeholder') }}"
                               class="w-full rounded-md border-gray-300 bg-gray-50 font-mono text-sm text-gray-800 shadow-sm placeholder:tracking-widest" />
                        <p class="mt-1 text-xs text-gray-500" x-show="!clientPassword">{{ __('event.client.password_hint') }}</p>
                    </div>
                    <div class="flex flex-wrap gap-2 pt-1">
                        <button type="button"
                                @click="copyClientCredentials()"
                                class="rounded-md bg-[var(--desert-bg-elevated)] px-3 py-2 text-sm font-medium text-white hover:bg-[var(--desert-bg)]">
                            <span x-text="clientCopied ? '{{ __('event.client.copied') }}' : '{{ __('event.client.copy') }}'"></span>
                        </button>
                        @can('permission', 'usuarios.editar')
                            <button type="button"
                                    @click="regenerateClientPassword()"
                                    :disabled="clientSaving"
                                    class="rounded-md border border-amber-300 bg-amber-50 px-3 py-2 text-sm font-medium text-amber-900 hover:bg-amber-100 disabled:opacity-60">
                                <span x-text="clientSaving ? '{{ __('event.deeplink.saving') }}' : '{{ __('event.client.new_password') }}'"></span>
                            </button>
                        @endcan
                        <button type="button"
                                @click="closeClientModal()"
                                class="rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                            {{ __('admin.actions.close') }}
                        </button>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>
