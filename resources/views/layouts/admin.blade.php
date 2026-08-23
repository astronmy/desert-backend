<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="admin-force-light" style="color-scheme: light;">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Desert') }} - {{ __('admin.title') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased bg-[var(--desert-bg)]" x-data="adminLayout()">
    @if (session('status'))
        <div class="pointer-events-none fixed left-1/2 top-4 z-[100] w-full max-w-2xl -translate-x-1/2 px-4"
             x-data="{ show: true }"
             x-show="show"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             x-init="setTimeout(() => show = false, 5000)">
            <div class="pointer-events-auto flex items-center gap-3 rounded-lg bg-[var(--desert-bg-elevated)] px-4 py-3 text-sm text-white shadow-lg ring-1 ring-[var(--desert-border)]">
                <p class="min-w-0 flex-1">{{ session('status') }}</p>
                <button type="button" @click="show = false" class="shrink-0 rounded p-1 text-[var(--desert-muted)] hover:bg-white/10 hover:text-white" aria-label="{{ __('admin.actions.close') }}">&times;</button>
            </div>
        </div>
    @endif
    <div class="min-h-screen flex">
        <div x-show="sidebarOpen"
             x-transition:enter="transition-opacity ease-linear duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-40 bg-black/50 lg:hidden"
             @click="sidebarOpen = false"
             style="display: none;"
             x-cloak>
        </div>

        <aside :class="{
                'w-64': !sidebarCollapsed,
                'w-[4.5rem]': sidebarCollapsed,
                '-translate-x-full lg:translate-x-0': true
            }"
            class="fixed inset-y-0 left-0 z-50 flex flex-col bg-[var(--desert-bg)] border-r border-[var(--desert-border)] transition-all duration-300 ease-in-out lg:translate-x-0"
            :class="{ 'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen }">
            <div class="flex h-16 shrink-0 items-center justify-center border-b border-[var(--desert-border)] transition-[padding] duration-300"
                 :class="sidebarCollapsed ? 'px-2' : 'px-4'">
                <a href="{{ route('admin.dashboard') }}" wire:navigate class="flex items-center justify-center overflow-hidden w-full min-w-0">
                    <img src="{{ asset('assets/logo-desert.png') }}" alt="{{ config('app.name') }}"
                         class="h-10 w-auto shrink-0 object-contain transition-all duration-300"
                         :class="sidebarCollapsed ? 'max-h-8 max-w-[3rem]' : 'max-w-[10rem]'" />
                </a>
            </div>

            <nav class="flex-1 overflow-y-auto py-4 px-3">
                <ul class="space-y-1">
                    @php
                        $user = auth()->user();
                        $menuItems = collect([
                            [
                                'label' => __('admin.menu.dashboard'),
                                'route' => 'admin.dashboard',
                                'active' => request()->routeIs('admin.dashboard'),
                                'icon' => 'dashboard',
                                'permission' => 'dashboard.ver',
                            ],
                            [
                                'label' => __('admin.menu.events'),
                                'route' => 'admin.events.index',
                                'active' => request()->routeIs('admin.events.index')
                                    || request()->routeIs('admin.events.create')
                                    || request()->routeIs('admin.events.edit')
                                    || request()->routeIs('admin.events.store')
                                    || request()->routeIs('admin.events.update'),
                                'icon' => 'events',
                                'permission' => 'eventos.ver',
                            ],
                            [
                                'label' => __('admin.menu.notifications'),
                                'route' => 'admin.notifications.index',
                                'active' => request()->routeIs('admin.notifications.*'),
                                'icon' => 'notifications',
                                'permission' => 'notificaciones.ver',
                            ],
                            [
                                'label' => __('admin.menu.users'),
                                'route' => 'admin.users.index',
                                'active' => request()->routeIs('admin.users.*'),
                                'icon' => 'users',
                                'permission' => 'usuarios.ver',
                            ],
                            [
                                'label' => __('admin.menu.roles'),
                                'route' => 'admin.roles.index',
                                'active' => request()->routeIs('admin.roles.*'),
                                'icon' => 'roles',
                                'permission' => 'roles.ver',
                            ],
                        ])->filter(fn ($item) => $user && $user->canPermission($item['permission']))->values();
                    @endphp
                    @foreach($menuItems as $item)
                        <li>
                            <a href="{{ route($item['route']) }}" wire:navigate
                               class="admin-sidebar-link {{ $item['active'] ? 'admin-sidebar-link-active' : '' }}">
                                <span class="admin-sidebar-icon flex h-5 w-5 shrink-0 items-center justify-center">
                                    @if($item['icon'] === 'dashboard')
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0a1 1 0 01-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1h-2z" />
                                        </svg>
                                    @elseif($item['icon'] === 'events')
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    @elseif($item['icon'] === 'notifications')
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                        </svg>
                                    @elseif($item['icon'] === 'roles')
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                        </svg>
                                    @else
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                    @endif
                                </span>
                                <span x-show="!sidebarCollapsed" x-transition>{{ $item['label'] }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </nav>
        </aside>

        <div class="flex flex-1 flex-col lg:pl-0"
             :class="sidebarCollapsed ? 'lg:pl-[4.5rem]' : 'lg:pl-64'">
            <header class="sticky top-0 z-30 flex h-16 shrink-0 items-center gap-4 border-b border-[var(--desert-border)] bg-[var(--desert-bg)] px-4 sm:px-6 lg:px-8">
                <button type="button"
                        @click="toggleSidebar()"
                        class="rounded-md p-2 text-[var(--desert-accent)] hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-[var(--desert-accent)]">
                    <span class="sr-only">{{ __('admin.sidebar.toggle') }}</span>
                    <svg x-show="!sidebarOpen" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg x-show="sidebarOpen" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                @isset($header)
                    <div class="min-w-0 flex-1 text-white">
                        {{ $header }}
                    </div>
                @else
                    <div class="min-w-0 flex-1" aria-hidden="true"></div>
                @endisset

                <div class="relative shrink-0" x-data="{ userOpen: false }" @click.outside="userOpen = false">
                    <button type="button" @click="userOpen = ! userOpen"
                            class="flex items-center gap-2 rounded-md py-2 pl-2 pr-3 text-sm text-white hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-[var(--desert-accent)]">
                        <span class="hidden sm:block">{{ auth()->user()->name }}</span>
                        <svg class="h-5 w-5 text-[var(--desert-accent)]" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div x-show="userOpen"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 top-full z-50 mt-2 w-48 origin-top-right rounded-md bg-[var(--desert-bg-elevated)] py-1 shadow-lg ring-1 ring-[var(--desert-border)]"
                         style="display: none;">
                        <form method="POST" action="{{ route('logout') }}" class="block">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2 text-left text-sm text-white hover:bg-white/10">
                                {{ __('admin.user_menu.logout') }}
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            <main class="flex-1 p-4 sm:p-6 lg:p-8 bg-[var(--desert-surface)] admin-content-light" style="color-scheme: light;">
                {{ $slot }}
            </main>
        </div>
    </div>

    <script>
        function adminLayout() {
            return {
                sidebarOpen: false,
                sidebarCollapsed: false,
                init() {
                    if (typeof localStorage !== 'undefined') {
                        this.sidebarCollapsed = localStorage.getItem('adminSidebarCollapsed') === 'true';
                    }
                    document.addEventListener('livewire:navigated', () => { this.sidebarOpen = false; });
                },
                toggleSidebar() {
                    if (window.innerWidth >= 1024) {
                        this.sidebarCollapsed = !this.sidebarCollapsed;
                        localStorage.setItem('adminSidebarCollapsed', this.sidebarCollapsed);
                    } else {
                        this.sidebarOpen = !this.sidebarOpen;
                    }
                }
            };
        }

        function registrationLinkModalState() {
            return {
                linkOpen: false,
                linkLoading: false,
                linkSaving: false,
                linkError: '',
                linkEventId: null,
                linkEventName: '',
                linkHasLink: false,
                linkShortUrl: '',
                linkExpiresAt: '',
                linkCopied: false,
                openLinkModal(eventId, eventName) {
                    this.linkEventId = eventId;
                    this.linkEventName = eventName || '';
                    this.linkOpen = true;
                    this.linkError = '';
                    this.linkCopied = false;
                    this.fetchLink();
                },
                closeLinkModal() {
                    this.linkOpen = false;
                    this.linkSaving = false;
                    this.linkLoading = false;
                },
                csrfToken() {
                    const meta = document.querySelector('meta[name="csrf-token"]');
                    return meta ? meta.getAttribute('content') : '';
                },
                linkShowUrl() {
                    return @json(url('/admin/events')) + '/' + this.linkEventId + '/registration-link';
                },
                applyLinkPayload(data) {
                    this.linkHasLink = !!data.has_link;
                    this.linkShortUrl = data.short_url || '';
                    this.linkExpiresAt = data.expires_at || '';
                },
                async fetchLink() {
                    this.linkLoading = true;
                    this.linkError = '';
                    try {
                        const res = await fetch(this.linkShowUrl(), {
                            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                            credentials: 'same-origin',
                        });
                        if (!res.ok) throw new Error('HTTP ' + res.status);
                        this.applyLinkPayload(await res.json());
                    } catch (e) {
                        this.linkError = @json(__('event.deeplink.load_error'));
                    } finally {
                        this.linkLoading = false;
                    }
                },
                async generateLink() {
                    await this.postLink();
                },
                async regenerateLink() {
                    if (!window.confirm(@json(__('event.deeplink.regenerate_confirm')))) {
                        return;
                    }
                    await this.postLink();
                },
                async postLink() {
                    this.linkSaving = true;
                    this.linkError = '';
                    try {
                        const res = await fetch(this.linkShowUrl(), {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.csrfToken(),
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                            credentials: 'same-origin',
                            body: JSON.stringify({}),
                        });
                        if (!res.ok) throw new Error('HTTP ' + res.status);
                        this.applyLinkPayload(await res.json());
                        this.linkCopied = false;
                    } catch (e) {
                        this.linkError = @json(__('event.deeplink.save_error'));
                    } finally {
                        this.linkSaving = false;
                    }
                },
                async copyLink() {
                    if (!this.linkShortUrl) return;
                    try {
                        await navigator.clipboard.writeText(this.linkShortUrl);
                        this.linkCopied = true;
                        setTimeout(() => { this.linkCopied = false; }, 2000);
                    } catch (e) {
                        // ignore
                    }
                },
            };
        }
    </script>
    @livewireScripts
</body>
</html>

