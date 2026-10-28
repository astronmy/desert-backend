<x-admin-layout>
    <x-slot name="header">
        <h1 class="text-xl font-semibold text-white">{{ __('system_log.title') }}</h1>
    </x-slot>

    @include('admin.system-logs._tabs')

    <div class="mb-4 rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
        <form method="GET" action="{{ route('admin.api-logs.index') }}" class="flex flex-wrap items-end gap-4">
            <div class="min-w-[180px]">
                <label for="filter_service" class="block text-sm font-medium text-gray-700">{{ __('api_log.attributes.service') }}</label>
                <x-select-input id="filter_service" name="service" class="mt-1">
                    <option value="">{{ __('api_log.all_services') }}</option>
                    @foreach (\App\Models\ApiLog::services() as $service)
                        <option value="{{ $service }}" @selected(request('service') === $service)>{{ __('api_log.services.'.$service) }}</option>
                    @endforeach
                </x-select-input>
            </div>
            <div class="min-w-[140px]">
                <label for="filter_method" class="block text-sm font-medium text-gray-700">{{ __('api_log.attributes.method') }}</label>
                <x-select-input id="filter_method" name="method" class="mt-1">
                    <option value="">{{ __('api_log.all_methods') }}</option>
                    @foreach (['GET', 'POST', 'PATCH', 'PUT'] as $method)
                        <option value="{{ $method }}" @selected(request('method') === $method)>{{ $method }}</option>
                    @endforeach
                </x-select-input>
            </div>
            <div class="min-w-[120px]">
                <label for="filter_response_code" class="block text-sm font-medium text-gray-700">{{ __('api_log.attributes.response_code') }}</label>
                <input type="number" id="filter_response_code" name="response_code" value="{{ request('response_code') }}" min="0" max="599"
                       class="mt-1 block w-full rounded-md border border-gray-300 px-3.5 py-2.5 text-sm shadow-sm focus:border-[var(--desert-bg-elevated)] focus:ring-[var(--desert-bg-elevated)]" />
            </div>
            <div class="min-w-[150px]">
                <label for="filter_ip" class="block text-sm font-medium text-gray-700">{{ __('api_log.attributes.ip') }}</label>
                <input type="text" id="filter_ip" name="ip" value="{{ request('ip') }}"
                       class="mt-1 block w-full rounded-md border border-gray-300 px-3.5 py-2.5 text-sm shadow-sm focus:border-[var(--desert-bg-elevated)] focus:ring-[var(--desert-bg-elevated)]" />
            </div>
            <div class="min-w-[150px]">
                <label for="filter_date_from" class="block text-sm font-medium text-gray-700">{{ __('api_log.attributes.date_from') }}</label>
                <input type="date" id="filter_date_from" name="date_from" value="{{ request('date_from') }}"
                       class="mt-1 block w-full rounded-md border border-gray-300 px-3.5 py-2.5 text-sm shadow-sm focus:border-[var(--desert-bg-elevated)] focus:ring-[var(--desert-bg-elevated)]" />
            </div>
            <div class="min-w-[150px]">
                <label for="filter_date_to" class="block text-sm font-medium text-gray-700">{{ __('api_log.attributes.date_to') }}</label>
                <input type="date" id="filter_date_to" name="date_to" value="{{ request('date_to') }}"
                       class="mt-1 block w-full rounded-md border border-gray-300 px-3.5 py-2.5 text-sm shadow-sm focus:border-[var(--desert-bg-elevated)] focus:ring-[var(--desert-bg-elevated)]" />
            </div>
            <div class="flex gap-2">
                <button type="submit" class="inline-flex items-center gap-2 rounded-md bg-[var(--desert-bg-elevated)] px-3 py-2 text-sm font-medium text-white shadow-sm hover:bg-[var(--desert-bg)]">
                    {{ __('admin.actions.filter') }}
                </button>
                <a href="{{ route('admin.api-logs.index') }}" wire:navigate
                   class="inline-flex items-center gap-2 rounded-md border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    {{ __('admin.actions.clear') }}
                </a>
            </div>
        </form>
    </div>

    <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-[var(--desert-bg-elevated)]">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase text-[var(--desert-sand)]">{{ __('api_log.attributes.created_at') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase text-[var(--desert-sand)]">{{ __('api_log.attributes.service') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase text-[var(--desert-sand)]">{{ __('api_log.attributes.method') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase text-[var(--desert-sand)]">{{ __('api_log.attributes.endpoint') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase text-[var(--desert-sand)]">{{ __('api_log.attributes.ip') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-medium uppercase text-[var(--desert-sand)]">{{ __('api_log.attributes.response_code') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse ($logs as $log)
                    <tr>
                        <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-700">{{ $log->created_at?->timezone(config('app.timezone'))->format('d/m/Y H:i:s') }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-900">{{ $log->serviceLabel() }}</td>
                        <td class="whitespace-nowrap px-4 py-3 font-mono text-sm text-gray-700">{{ $log->method }}</td>
                        <td class="px-4 py-3 font-mono text-sm text-gray-700">{{ $log->endpoint }}</td>
                        <td class="whitespace-nowrap px-4 py-3 font-mono text-sm text-gray-700">{{ $log->ip ?: '—' }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-900">{{ $log->response_code }}</td>
                    </tr>
                    <tr>
                        <td colspan="6" class="bg-gray-50 px-4 py-2">
                            <details>
                                <summary class="cursor-pointer text-sm font-medium text-[var(--desert-bg-elevated)]">{{ __('api_log.bodies') }}</summary>
                                <div class="mt-3 grid gap-3 lg:grid-cols-2">
                                    <div>
                                        <p class="mb-1 text-xs font-medium uppercase tracking-wide text-gray-500">{{ __('api_log.request_body') }}</p>
                                        <pre class="max-h-48 overflow-auto whitespace-pre-wrap rounded-md bg-white p-3 text-xs text-gray-800 ring-1 ring-gray-200">{{ $log->request_body ?: __('api_log.empty_body') }}</pre>
                                    </div>
                                    <div>
                                        <p class="mb-1 text-xs font-medium uppercase tracking-wide text-gray-500">{{ __('api_log.response_body') }}</p>
                                        <pre class="max-h-48 overflow-auto whitespace-pre-wrap rounded-md bg-white p-3 text-xs text-gray-800 ring-1 ring-gray-200">{{ $log->response_body ?: __('api_log.empty_body') }}</pre>
                                    </div>
                                </div>
                            </details>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-sm text-gray-500">{{ __('api_log.empty') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="flex flex-col gap-3 border-t border-gray-200 bg-gray-50 px-4 py-3 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-sm text-gray-700">
                @if ($logs->total() > 0)
                    {{ __('admin.table.showing', ['from' => $logs->firstItem(), 'to' => $logs->lastItem(), 'total' => $logs->total()]) }}
                @else
                    {{ __('admin.table.no_records') }}
                @endif
            </p>
            <div>
                {{ $logs->links() }}
            </div>
        </div>
    </div>
</x-admin-layout>
