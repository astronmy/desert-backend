<div class="mb-4 flex flex-wrap gap-2">
    <a href="{{ route('admin.access-control-logs.index') }}" wire:navigate
       @class([
           'rounded-md px-3 py-2 text-sm font-medium',
           'bg-[var(--desert-bg-elevated)] text-white' => request()->routeIs('admin.access-control-logs.*'),
           'border border-gray-300 bg-white text-gray-700 hover:bg-gray-50' => ! request()->routeIs('admin.access-control-logs.*'),
       ])>
        {{ __('system_log.tabs.access') }}
    </a>
    <a href="{{ route('admin.microservice-logs.index') }}" wire:navigate
       @class([
           'rounded-md px-3 py-2 text-sm font-medium',
           'bg-[var(--desert-bg-elevated)] text-white' => request()->routeIs('admin.microservice-logs.*'),
           'border border-gray-300 bg-white text-gray-700 hover:bg-gray-50' => ! request()->routeIs('admin.microservice-logs.*'),
       ])>
        {{ __('system_log.tabs.microservice') }}
    </a>
    <a href="{{ route('admin.api-logs.index') }}" wire:navigate
       @class([
           'rounded-md px-3 py-2 text-sm font-medium',
           'bg-[var(--desert-bg-elevated)] text-white' => request()->routeIs('admin.api-logs.*'),
           'border border-gray-300 bg-white text-gray-700 hover:bg-gray-50' => ! request()->routeIs('admin.api-logs.*'),
       ])>
        {{ __('system_log.tabs.app') }}
    </a>
</div>
