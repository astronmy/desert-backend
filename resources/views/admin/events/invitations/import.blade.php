<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.events.invitations.index', $event) }}" wire:navigate class="text-[var(--desert-sand)] hover:text-white">{{ __('invitation.index.title') }}</a>
            <span class="text-[var(--desert-muted)]">/</span>
            <h1 class="text-xl font-semibold text-white">{{ __('invitation.import.title') }}</h1>
        </div>
    </x-slot>

    <div class="mx-auto max-w-3xl space-y-4">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <p class="text-xs font-medium uppercase tracking-wide text-gray-500">{{ __('event.index.title') }}</p>
                <h2 class="text-lg font-semibold text-[var(--desert-bg)]">{{ $event->name }}</h2>
            </div>
            <a href="{{ route('admin.events.invitations.import.template', $event) }}"
               class="inline-flex items-center gap-2 rounded-md border border-[var(--desert-bg-elevated)] bg-white px-3 py-2 text-sm font-medium text-[var(--desert-bg-elevated)] shadow-sm hover:bg-[var(--desert-sand)]">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5m0 0l5-5m-5 5V4" />
                </svg>
                {{ __('invitation.import.download_template') }}
            </a>
        </div>

        <div class="overflow-hidden rounded-xl border border-[var(--desert-sand)] bg-white shadow-sm">
            <div class="border-b border-[var(--desert-sand)] bg-[var(--desert-bg)] px-5 py-4 text-white">
                <p class="text-sm font-medium">{{ __('invitation.import.help') }}</p>
                <div class="mt-3">
                    <p class="mb-2 text-xs uppercase tracking-wide text-[var(--desert-sand)]">{{ __('invitation.import.columns_title') }}</p>
                    <div class="flex flex-wrap gap-2">
                        <span class="rounded-full bg-white/10 px-3 py-1 text-xs font-semibold text-[var(--desert-accent)] ring-1 ring-white/15">Nombre</span>
                        <span class="rounded-full bg-white/10 px-3 py-1 text-xs font-semibold text-[var(--desert-accent)] ring-1 ring-white/15">Apellido</span>
                        <span class="rounded-full bg-white/10 px-3 py-1 text-xs font-semibold text-[var(--desert-accent)] ring-1 ring-white/15">DNI</span>
                    </div>
                </div>
            </div>

            <form action="{{ route('admin.events.invitations.import.store', $event) }}" method="POST" enctype="multipart/form-data"
                  class="space-y-5 p-5 sm:p-6"
                  x-data="{
                      fileName: '',
                      dragging: false,
                      onFiles(files) {
                          if (!files || !files.length) return;
                          const input = this.$refs.fileInput;
                          const dt = new DataTransfer();
                          dt.items.add(files[0]);
                          input.files = dt.files;
                          this.fileName = files[0].name;
                      }
                  }">
                @csrf

                <div>
                    <input x-ref="fileInput" id="file" name="file" type="file" accept=".xlsx,.xls,.csv" required class="sr-only"
                           @change="fileName = $event.target.files[0]?.name || ''" />

                    <label for="file"
                           @dragover.prevent="dragging = true"
                           @dragleave.prevent="dragging = false"
                           @drop.prevent="dragging = false; onFiles($event.dataTransfer.files)"
                           class="flex cursor-pointer flex-col items-center justify-center rounded-xl border-2 border-dashed px-6 py-10 text-center transition"
                           :class="dragging
                               ? 'border-[var(--desert-gold)] bg-[var(--desert-sand)]/60'
                               : (fileName ? 'border-[var(--desert-bg-elevated)] bg-[var(--desert-sand)]/30' : 'border-gray-300 bg-gray-50 hover:border-[var(--desert-bg-elevated)] hover:bg-[var(--desert-sand)]/20')">
                        <span class="mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-[var(--desert-bg-elevated)] text-white">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                        </span>

                        <template x-if="!fileName">
                            <span>
                                <span class="block text-sm font-semibold text-[var(--desert-bg)]">{{ __('invitation.import.drop_title') }}</span>
                                <span class="mt-1 block text-sm text-gray-500">{{ __('invitation.import.drop_subtitle') }}</span>
                                <span class="mt-2 block text-xs text-gray-400">{{ __('invitation.import.formats') }}</span>
                            </span>
                        </template>

                        <template x-if="fileName">
                            <span>
                                <span class="block text-xs font-medium uppercase tracking-wide text-gray-500">{{ __('invitation.import.selected') }}</span>
                                <span class="mt-1 block text-sm font-semibold text-[var(--desert-bg)]" x-text="fileName"></span>
                                <span class="mt-2 inline-flex rounded-md bg-white px-3 py-1 text-xs font-medium text-[var(--desert-bg-elevated)] ring-1 ring-gray-200">
                                    {{ __('invitation.import.change_file') }}
                                </span>
                            </span>
                        </template>
                    </label>

                    <x-input-error :messages="$errors->get('file')" class="mt-2" />
                </div>

                <div class="rounded-lg border border-[var(--desert-sand)] bg-[var(--desert-surface)]/70 px-4 py-3">
                    <p class="text-xs font-semibold uppercase tracking-wide text-[var(--desert-bg-elevated)]">{{ __('invitation.import.notes_title') }}</p>
                    <ul class="mt-2 space-y-1.5 text-sm text-gray-600">
                        <li class="flex gap-2">
                            <span class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-[var(--desert-gold)]"></span>
                            <span>{{ __('invitation.import.note_create') }}</span>
                        </li>
                        <li class="flex gap-2">
                            <span class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-[var(--desert-gold)]"></span>
                            <span>{{ __('invitation.import.note_reuse') }}</span>
                        </li>
                        <li class="flex gap-2">
                            <span class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-[var(--desert-gold)]"></span>
                            <span>{{ __('invitation.import.note_skip') }}</span>
                        </li>
                    </ul>
                </div>

                <div class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <a href="{{ route('admin.events.invitations.index', $event) }}" wire:navigate
                       class="inline-flex items-center justify-center gap-2 rounded-md border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        {{ __('admin.actions.back') }}
                    </a>
                    <button type="submit"
                            class="inline-flex items-center justify-center gap-2 rounded-md bg-[var(--desert-bg-elevated)] px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-[var(--desert-bg)]">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                        {{ __('invitation.import.submit') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
