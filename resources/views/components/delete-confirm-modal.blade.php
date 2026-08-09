{{-- Debe estar dentro de un elemento con x-data que tenga: open, confirmLabel, confirmValue, formId, openModal, close, submit --}}
<div x-show="open"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-[100] flex items-center justify-center p-4"
     style="display: none;"
     x-cloak
     role="dialog"
     aria-modal="true"
     aria-labelledby="delete-modal-title">
    <div class="fixed inset-0 bg-black/50" @click="close()"></div>
    <div class="relative w-full max-w-md rounded-lg bg-white p-6 shadow-xl"
         @click.stop>
        <h2 id="delete-modal-title" class="text-lg font-semibold text-gray-900">{{ __('admin.delete_modal.title') }}</h2>
        <p class="mt-2 text-sm text-gray-600">
            {{ __('admin.delete_modal.description_prefix') }}
            <strong x-text="confirmLabel" class="break-all"></strong>
            {{ __('admin.delete_modal.description_suffix') }}
        </p>
        <input type="text"
               x-model="confirmValue"
               @keydown.enter.prevent="if (confirmValue === confirmLabel) submit()"
               class="mt-3 block w-full rounded-md border border-gray-300 px-3.5 py-2.5 text-sm shadow-sm focus:border-red-500 focus:ring-red-500"
               placeholder="{{ __('admin.delete_modal.placeholder') }}"
               autocomplete="off" />
        <div class="mt-6 flex justify-end gap-2">
            <button type="button"
                    @click="close()"
                    class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                {{ __('admin.actions.cancel') }}
            </button>
            <button type="button"
                    @click="submit()"
                    :disabled="confirmValue !== confirmLabel"
                    class="rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-500 disabled:cursor-not-allowed disabled:opacity-50">
                {{ __('admin.actions.delete') }}
            </button>
        </div>
    </div>
</div>
