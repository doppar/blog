<header id="site-header" class="sticky top-0 z-40 backdrop-blur-md bg-[rgba(254,254,254,0.85)] border-b border-soft">
    <div class="max-w-7xl mx-auto px-6 flex items-center h-16 gap-6">
        <a href="/" class="flex items-center gap-2 group shrink-0">
            <img src="/logo-icon.png" alt="Doppar" class="h-8 w-auto">
            <span class="font-display text-xl font-bold tracking-tight text-ink"> <span class="text-primary">Blog</span></span>
        </a>

        <div class="ml-auto flex items-center gap-4">
            <!-- Search -->
            <div class="relative hidden sm:block">
                <input type="text" id="search-input" placeholder="Search posts..." class="w-64 pl-10 pr-4 py-2 rounded-full text-sm border border-soft bg-white/50 hover:bg-white focus:bg-white focus:border-primary focus:outline-none transition-all text-ink placeholder:text-ink-soft">
                <svg class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-ink-soft pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <div id="search-results" class="absolute top-full left-0 right-0 mt-2 bg-white rounded-xl shadow-[0_20px_50px_-20px_rgba(26,26,26,0.4)] border border-soft overflow-hidden hidden z-50"></div>
            </div>

            <!-- Navigation -->
            <nav class="hidden md:flex items-center gap-4 text-sm font-medium text-ink-soft">
                <a href="https://doppar.com" class="hover:text-ink transition-colors">Docs</a>
                <a href="https://github.com/doppar/framework" class="hover:text-ink transition-colors">GitHub</a>
            </nav>

            <!-- Auth buttons -->
            #if (auth()->check())
            <div class="hidden sm:flex items-center">
                <div class="admin-dropdown" data-dropdown>
                    <button class="admin-profile" type="button" data-dropdown-trigger aria-expanded="false">
                        <span class="admin-profile__avatar">
                            #if ((auth()->user()->image ?? '') !== '')
                                <img src="[[ auth()->user()->image ]]" alt="[[ auth()->user()->name ]]">
                            #else
                                [[ strtoupper(substr((string) auth()->user()->name, 0, 2)) ]]
                            #endif
                        </span>
                        <span class="admin-profile__name">[[ auth()->user()->name ]]</span>
                        <svg class="admin-profile__chevron" viewBox="0 0 20 20" aria-hidden="true">
                            <path d="M5 7.5 10 12.5l5-5"></path>
                        </svg>
                    </button>

                    <div class="admin-dropdown__menu admin-dropdown__menu--profile" data-dropdown-menu>
                        <div class="admin-dropdown__head admin-dropdown__head--profile">
                            <span class="admin-dropdown__head-avatar">
                                #if ((auth()->user()->image ?? '') !== '')
                                    <img src="[[ auth()->user()->image ]]" alt="[[ auth()->user()->name ]]">
                                #else
                                    [[ strtoupper(substr((string) auth()->user()->name, 0, 2)) ]]
                                #endif
                            </span>
                            <span class="admin-dropdown__head-text">
                                <strong>[[ auth()->user()->name ]]</strong>
                                <span>[[ auth()->user()->email ]]</span>
                            </span>
                        </div>

                        <div class="admin-dropdown__divider"></div>

                        #if (auth()->user()->role === \App\Models\User::ROLE_ADMIN)
                        <a class="admin-dropdown__row" href="[[ route('admin.profile.index') ]]">
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                <circle cx="12" cy="12" r="3"></circle>
                                <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 1 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 1 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.6a1.65 1.65 0 0 0 1-1.51V3a2 2 0 1 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 1 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                            </svg>
                            <span>Account settings</span>
                        </a>

                        <div class="admin-dropdown__divider"></div>
                        #endif

                        <button class="admin-dropdown__row admin-dropdown__row--danger" type="submit" form="site-logout-form">
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                <path d="M16 17l5-5-5-5"></path>
                                <path d="M21 12H9"></path>
                            </svg>
                            <span>Log out</span>
                        </button>
                    </div>
                </div>
            </div>
            #else
            <div class="hidden sm:flex items-center gap-3">
                <a href="/login" class="text-sm font-medium text-ink-soft hover:text-ink transition-colors">Sign in</a>
                <a href="https://doppar.com" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-ink text-[#fefefe] text-sm font-medium hover:bg-primary transition-colors">
                    Get started
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                    </svg>
                </a>
            </div>
            #endif

            <!-- Mobile menu button -->
            <button type="button" id="mobile-menu-btn" class="md:hidden inline-flex items-center justify-center w-9 h-9 rounded-full text-ink-soft hover:text-ink hover:bg-[rgba(26,26,26,0.05)] transition-colors" aria-label="Toggle menu" aria-expanded="false" aria-controls="mobile-menu">
                <svg id="menu-icon-open" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5M3.75 17.25h16.5" />
                </svg>
                <svg id="menu-icon-close" class="w-5 h-5 hidden" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

    <!-- Mobile menu -->
    <div id="mobile-menu" class="md:hidden hidden px-6 pb-4">
        <nav class="flex flex-col gap-1 text-sm font-medium text-ink-soft">
            <a href="https://doppar.com" class="px-3 py-2.5 rounded-lg hover:text-ink hover:bg-[rgba(26,26,26,0.05)] transition-colors">Documentation</a>
            <a href="https://github.com/doppar/framework" class="px-3 py-2.5 rounded-lg hover:text-ink hover:bg-[rgba(26,26,26,0.05)] transition-colors">GitHub</a>
            <a href="/?tab=featured" class="px-3 py-2.5 rounded-lg hover:text-ink hover:bg-[rgba(26,26,26,0.05)] transition-colors">Featured</a>
            <a href="/" class="px-3 py-2.5 rounded-lg hover:text-ink hover:bg-[rgba(26,26,26,0.05)] transition-colors">All stories</a>
            #if (auth()->check())
            <button type="submit" form="site-logout-form" class="text-left px-3 py-2.5 rounded-lg hover:text-ink hover:bg-[rgba(26,26,26,0.05)] transition-colors">Log out</button>
            #else
            <a href="/login" class="px-3 py-2.5 rounded-lg hover:text-ink hover:bg-[rgba(26,26,26,0.05)] transition-colors">Sign in</a>
            #endif
        </nav>
    </div>

    #if (auth()->check())
    <form id="site-logout-form" method="POST" action="[[ route('logout') ]]" class="hidden">
        #csrf
    </form>
    #endif
</header>

<script>
    // Mobile nav menu
    (() => {
        const btn = document.getElementById('mobile-menu-btn');
        const menu = document.getElementById('mobile-menu');
        const iconOpen = document.getElementById('menu-icon-open');
        const iconClose = document.getElementById('menu-icon-close');
        if (!btn || !menu) return;
        btn.addEventListener('click', () => {
            const willOpen = menu.classList.contains('hidden');
            menu.classList.toggle('hidden', !willOpen);
            iconOpen.classList.toggle('hidden', willOpen);
            iconClose.classList.toggle('hidden', !willOpen);
            btn.setAttribute('aria-expanded', String(willOpen));
        });
    })();
</script>