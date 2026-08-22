<div
    x-show="linkOpen"
    x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
    style="display: none;"
    @keydown.escape.window="if (linkOpen) closeLinkModal()"
>
    <div class="absolute inset-0 bg-black/50" @click="closeLinkModal()"></div>
    <div class="relative w-full max-w-lg rounded-lg bg-white shadow-xl" @click.stop>
        <div class="flex items-start justify-between border-b border-gray-200 bg-[var(--desert-bg)] px-5 py-3">
            <div>
                <h2 class="text-sm font-semibold uppercase tracking-wide text-[var(--desert-sand)]">
                    {{ __('event.deeplink.title') }}
                </h2>
                <p class="mt-1 text-xs text-[var(--desert-accent)]" x-text="linkEventName"></p>
            </div>
            <button type="button" @click="closeLinkModal()" class="text-[var(--desert-muted)] hover:text-white" aria-label="{{ __('admin.actions.close') }}">&times;</button>
        </div>
        <div class="space-y-4 p-5">
            <template x-if="linkLoading">
                <p class="text-sm text-gray-500">{{ __('event.deeplink.loading') }}</p>
            </template>
            <template x-if="!linkLoading && linkError">
                <p class="text-sm text-red-600" x-text="linkError"></p>
            </template>
            <template x-if="!linkLoading && !linkError && !linkHasLink">
                <div class="space-y-3">
                    <p class="text-sm text-gray-600">{{ __('event.deeplink.help') }}</p>
                    <p class="text-sm text-gray-500">{{ __('event.deeplink.no_link') }}</p>
                    <button type="button"
                            @click="generateLink()"
                            :disabled="linkSaving"
                            class="rounded-md bg-[var(--desert-gold)] px-3 py-2 text-sm font-semibold text-[var(--desert-bg)] hover:bg-[var(--desert-gold-dark)] hover:text-white disabled:opacity-60">
                        <span x-text="linkSaving ? '{{ __('event.deeplink.saving') }}' : '{{ __('event.deeplink.generate') }}'"></span>
                    </button>
                </div>
            </template>
            <template x-if="!linkLoading && !linkError && linkHasLink">
                <div class="space-y-3">
                    <p class="text-sm text-gray-600">{{ __('event.deeplink.help') }}</p>
                    <div>
                        <p class="mb-1 text-xs font-medium uppercase tracking-wide text-gray-500">{{ __('event.deeplink.url') }}</p>
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                            <input type="text" readonly :value="linkShortUrl"
                                   class="w-full rounded-md border-gray-300 bg-gray-50 font-mono text-xs text-gray-800 shadow-sm" />
                            <button type="button"
                                    @click="copyLink()"
                                    class="shrink-0 rounded-md bg-[var(--desert-bg-elevated)] px-3 py-2 text-sm font-medium text-white hover:bg-[var(--desert-bg)]">
                                <span x-text="linkCopied ? '{{ __('event.deeplink.copied') }}' : '{{ __('event.deeplink.copy') }}'"></span>
                            </button>
                        </div>
                        <p class="mt-2 text-xs text-gray-500" x-show="linkExpiresAt">
                            {{ __('event.deeplink.expires_label') }}: <span x-text="linkExpiresAt"></span>
                        </p>
                    </div>
                    <div class="flex flex-wrap gap-2 pt-1">
                        <button type="button"
                                @click="regenerateLink()"
                                :disabled="linkSaving"
                                class="rounded-md border border-amber-300 bg-amber-50 px-3 py-2 text-sm font-medium text-amber-900 hover:bg-amber-100 disabled:opacity-60">
                            <span x-text="linkSaving ? '{{ __('event.deeplink.saving') }}' : '{{ __('event.deeplink.regenerate') }}'"></span>
                        </button>
                        <button type="button"
                                @click="closeLinkModal()"
                                class="rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                            {{ __('admin.actions.close') }}
                        </button>
                    </div>
                    <p class="text-xs text-gray-500">{{ __('event.deeplink.regenerate_hint') }}</p>
                </div>
            </template>
        </div>
    </div>
</div>
