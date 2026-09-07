<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="admin-force-light" style="color-scheme: light;">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
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
             @click="closeSidebar()"
             style="display: none;"
             x-cloak>
        </div>

        <aside
            class="admin-sidebar fixed inset-y-0 left-0 z-50 flex w-64 flex-col border-r border-[var(--desert-border)] bg-[var(--desert-bg)] transition-all duration-300 ease-in-out lg:translate-x-0"
            :class="{
                'translate-x-0': sidebarOpen,
                '-translate-x-full': !sidebarOpen,
                'lg:translate-x-0 lg:w-64': !sidebarCollapsed,
                'lg:translate-x-0 lg:w-[4.5rem]': sidebarCollapsed,
            }"
        >
            <div class="flex h-16 shrink-0 items-center justify-center border-b border-[var(--desert-border)] transition-[padding] duration-300"
                 :class="showSidebarLabels ? 'px-4' : 'px-2'">
                <a href="{{ route('admin.dashboard') }}" wire:navigate @click="closeSidebar()"
                   class="flex min-w-0 w-full items-center justify-center overflow-hidden">
                    <img src="{{ asset('assets/logo-desert.png') }}" alt="{{ config('app.name') }}"
                         class="h-10 w-auto shrink-0 object-contain transition-all duration-300"
                         :class="showSidebarLabels ? 'max-w-[10rem]' : 'max-h-8 max-w-[3rem]'" />
                </a>
            </div>

            <nav class="admin-sidebar-nav flex-1 overflow-y-auto px-3 py-4">
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
                                'label' => __('admin.menu.my_event'),
                                'route' => 'admin.client-event.edit',
                                'active' => request()->routeIs('admin.client-event.*'),
                                'icon' => 'my_event',
                                'permission' => 'eventos.contenido',
                                'requires_event' => true,
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
                        ])->filter(function ($item) use ($user) {
                            if (! $user || ! $user->canPermission($item['permission'])) {
                                return false;
                            }
                            if (! empty($item['requires_event']) && ! $user->requiresEvent()) {
                                return false;
                            }

                            return true;
                        })->values();
                    @endphp
                    @foreach($menuItems as $item)
                        <li>
                            <a href="{{ route($item['route']) }}" wire:navigate @click="closeSidebar()"
                               class="admin-sidebar-link {{ $item['active'] ? 'admin-sidebar-link-active' : '' }}">
                                <span class="admin-sidebar-icon flex h-5 w-5 shrink-0 items-center justify-center">
                                    @if($item['icon'] === 'dashboard')
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0a1 1 0 01-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1h-2z" />
                                        </svg>
                                    @elseif($item['icon'] === 'events' || $item['icon'] === 'my_event')
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
                                <span x-show="showSidebarLabels" x-transition>{{ $item['label'] }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </nav>
        </aside>

        <div class="flex flex-1 flex-col lg:pl-0"
             :class="sidebarCollapsed ? 'lg:pl-[4.5rem]' : 'lg:pl-64'">
            <header class="admin-header sticky top-0 z-[60] flex min-h-16 shrink-0 items-center gap-4 border-b border-[var(--desert-border)] bg-[var(--desert-bg)] px-4 sm:px-6 lg:px-8">
                <button type="button"
                        @click="toggleSidebar()"
                        class="relative z-[60] rounded-md p-2 text-[var(--desert-accent)] hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-[var(--desert-accent)]">
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
                isDesktop() {
                    return window.matchMedia('(min-width: 1024px)').matches;
                },
                get showSidebarLabels() {
                    return ! this.isDesktop() || ! this.sidebarCollapsed;
                },
                lockScroll(lock) {
                    document.documentElement.classList.toggle('overflow-hidden', lock);
                    document.body.classList.toggle('overflow-hidden', lock);
                },
                closeSidebar() {
                    this.sidebarOpen = false;
                    this.lockScroll(false);
                },
                init() {
                    if (this.isDesktop() && typeof localStorage !== 'undefined') {
                        this.sidebarCollapsed = localStorage.getItem('adminSidebarCollapsed') === 'true';
                    } else {
                        this.sidebarCollapsed = false;
                    }

                    this.$watch('sidebarOpen', (open) => {
                        this.lockScroll(! this.isDesktop() && open);
                    });

                    document.addEventListener('livewire:navigated', () => this.closeSidebar());

                    window.matchMedia('(min-width: 1024px)').addEventListener('change', (event) => {
                        if (event.matches) {
                            this.closeSidebar();
                            if (typeof localStorage !== 'undefined') {
                                this.sidebarCollapsed = localStorage.getItem('adminSidebarCollapsed') === 'true';
                            }
                        } else {
                            this.sidebarCollapsed = false;
                            this.closeSidebar();
                        }
                    });
                },
                toggleSidebar() {
                    if (this.isDesktop()) {
                        this.sidebarCollapsed = ! this.sidebarCollapsed;
                        localStorage.setItem('adminSidebarCollapsed', this.sidebarCollapsed);
                        return;
                    }
                    this.sidebarOpen = ! this.sidebarOpen;
                },
            };
        }

        window.eventCombobox = function eventCombobox(config) {
            return {
                eventId: config.eventId ?? null,
                events: config.events || [],
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
                },
            };
        }

        window.notificationCreateForm = function notificationCreateForm(config) {
            return {
                eventId: config.eventId ?? null,
                type: config.type,
                scope: config.scope,
                selected: config.selected || [],
                endpointTemplate: config.endpointTemplate,
                loadErrorMessage: config.loadErrorMessage,
                invitations: [],
                invitationQuery: '',
                loading: false,
                loadError: '',
                get filteredInvitations() {
                    const q = (this.invitationQuery || '').trim().toLowerCase();
                    if (!q) return this.invitations;
                    return this.invitations.filter(inv =>
                        (inv.guest && inv.guest.toLowerCase().includes(q)) ||
                        (inv.code && inv.code.toLowerCase().includes(q)) ||
                        (inv.document && inv.document.toLowerCase().includes(q)) ||
                        (inv.uuid && inv.uuid.toLowerCase().includes(q))
                    );
                },
                init() {
                    this.$watch('eventId', () => this.fetchInvitations());
                    this.$watch('scope', (scope) => {
                        if (scope === 'specific') this.fetchInvitations();
                    });
                    if (this.eventId && this.scope === 'specific') this.fetchInvitations();
                },
                onEventSelected(id) {
                    const next = id ? Number(id) : null;
                    if (this.eventId !== next) this.selected = [];
                    this.invitationQuery = '';
                    this.eventId = next;
                },
                async fetchInvitations() {
                    if (!this.eventId || this.scope !== 'specific') {
                        this.invitations = [];
                        return;
                    }
                    this.loading = true;
                    this.loadError = '';
                    try {
                        const res = await fetch(this.endpointTemplate.replace('__EVENT__', this.eventId), {
                            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                            credentials: 'same-origin',
                        });
                        if (!res.ok) throw new Error('HTTP ' + res.status);
                        const json = await res.json();
                        this.invitations = json.data || [];
                    } catch (e) {
                        this.loadError = this.loadErrorMessage;
                        this.invitations = [];
                    } finally {
                        this.loading = false;
                    }
                },
                selectAllVisible() {
                    this.filteredInvitations.forEach((inv) => {
                        if (!this.selected.includes(inv.id)) this.selected.push(inv.id);
                    });
                },
                clearSelection() {
                    this.selected = [];
                },
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
                linkCopied: '',
                openLinkModal(eventId, eventName) {
                    this.linkEventId = eventId;
                    this.linkEventName = eventName || '';
                    this.linkOpen = true;
                    this.linkError = '';
                    this.linkCopied = '';
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
                        this.linkCopied = '';
                    } catch (e) {
                        this.linkError = @json(__('event.deeplink.save_error'));
                    } finally {
                        this.linkSaving = false;
                    }
                },
                async copyLink(kind) {
                    if (!this.linkShortUrl) return;
                    try {
                        await navigator.clipboard.writeText(this.linkShortUrl);
                        this.linkCopied = kind;
                        setTimeout(() => { if (this.linkCopied === kind) this.linkCopied = ''; }, 2000);
                    } catch (e) {
                        // ignore
                    }
                },
            };
        }

        function eventClientModalState() {
            return {
                clientOpen: false,
                clientLoading: false,
                clientSaving: false,
                clientError: '',
                clientEventId: null,
                clientEventName: '',
                clientExists: false,
                clientShowForm: false,
                clientEmail: '',
                clientPassword: '',
                clientCopied: false,
                openClientModal(eventId, eventName) {
                    this.clientEventId = eventId;
                    this.clientEventName = eventName || '';
                    this.clientOpen = true;
                    this.clientError = '';
                    this.clientCopied = false;
                    this.clientShowForm = false;
                    this.clientEmail = '';
                    this.clientPassword = '';
                    this.fetchClient();
                },
                closeClientModal() {
                    this.clientOpen = false;
                    this.clientSaving = false;
                    this.clientLoading = false;
                    this.clientShowForm = false;
                },
                clientUrl() {
                    return @json(url('/admin/events')) + '/' + this.clientEventId + '/client';
                },
                firstError(data, fallback) {
                    if (data && data.errors) {
                        const first = Object.values(data.errors)[0];
                        if (Array.isArray(first) && first[0]) return first[0];
                    }
                    return (data && data.message) || fallback;
                },
                autogeneratePassword() {
                    const sets = ['abcdefghijkmnopqrstuvwxyz', 'ABCDEFGHJKLMNPQRSTUVWXYZ', '23456789', '!@#$%&*'];
                    let out = '';
                    for (let i = 0; i < sets.length; i++) {
                        out += sets[i].charAt(Math.floor(Math.random() * sets[i].length));
                    }
                    const all = sets.join('');
                    while (out.length < 12) {
                        out += all.charAt(Math.floor(Math.random() * all.length));
                    }
                    this.clientPassword = out.split('').sort(() => Math.random() - 0.5).join('');
                },
                startCreateClient() {
                    this.clientShowForm = true;
                    this.clientError = '';
                    this.clientEmail = '';
                    this.clientPassword = '';
                },
                async fetchClient() {
                    this.clientLoading = true;
                    this.clientError = '';
                    try {
                        const res = await fetch(this.clientUrl(), {
                            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                            credentials: 'same-origin',
                        });
                        if (!res.ok) throw new Error('HTTP ' + res.status);
                        const data = await res.json();
                        this.clientExists = !!data.exists;
                        this.clientEmail = data.email || '';
                        this.clientPassword = '';
                    } catch (e) {
                        this.clientError = @json(__('event.client.load_error'));
                    } finally {
                        this.clientLoading = false;
                    }
                },
                async createClient() {
                    this.clientSaving = true;
                    this.clientError = '';
                    try {
                        const res = await fetch(this.clientUrl(), {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.csrfToken(),
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                            credentials: 'same-origin',
                            body: JSON.stringify({
                                email: this.clientEmail,
                                password: this.clientPassword || null,
                            }),
                        });
                        const data = await res.json().catch(() => ({}));
                        if (!res.ok && res.status !== 409) {
                            this.clientError = this.firstError(data, @json(__('event.client.save_error')));
                            return;
                        }
                        this.clientExists = true;
                        this.clientShowForm = false;
                        this.clientEmail = data.email || this.clientEmail;
                        this.clientPassword = data.password || this.clientPassword || '';
                    } catch (e) {
                        this.clientError = @json(__('event.client.save_error'));
                    } finally {
                        this.clientSaving = false;
                    }
                },
                async regenerateClientPassword() {
                    if (!window.confirm(@json(__('event.client.new_password_confirm')))) {
                        return;
                    }
                    this.clientSaving = true;
                    this.clientError = '';
                    try {
                        const res = await fetch(this.clientUrl() + '/password', {
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
                        const data = await res.json().catch(() => ({}));
                        if (!res.ok) {
                            this.clientError = this.firstError(data, @json(__('event.client.password_error')));
                            return;
                        }
                        this.clientEmail = data.email || this.clientEmail;
                        this.clientPassword = data.password || '';
                        this.clientCopied = false;
                    } catch (e) {
                        this.clientError = @json(__('event.client.password_error'));
                    } finally {
                        this.clientSaving = false;
                    }
                },
                async copyClientCredentials() {
                    const lines = ['Email: ' + (this.clientEmail || '')];
                    if (this.clientPassword) {
                        lines.push('Contraseña: ' + this.clientPassword);
                    }
                    try {
                        await navigator.clipboard.writeText(lines.join('\n'));
                        this.clientCopied = true;
                        setTimeout(() => { this.clientCopied = false; }, 2000);
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

