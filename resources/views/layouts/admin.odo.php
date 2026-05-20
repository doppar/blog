<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>#yield('title')</title>
        <meta name="csrf-token" content="[[ csrf_token() ]]" />
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        #yield('head')
        #vite('resources/client/js/app.js')
    </head>
    <body class="admin-body">
        <div class="admin-shell">
            <header class="admin-header">
                <div class="admin-header__left">
                    <button class="admin-sidebar-toggle" type="button" data-sidebar-toggle aria-label="Toggle navigation">
                        <span></span>
                        <span></span>
                        <span></span>
                    </button>

                    <a class="admin-brand" href="[[ route('admin.dashboard') ]]">
                        <span class="admin-brand__mark">
                            <svg viewBox="0 0 40 40" aria-hidden="true">
                                <circle cx="20" cy="20" r="17" fill="none" stroke="currentColor" stroke-width="3"></circle>
                                <path d="M13 21c0-3.5 2.6-6 6.5-6 2.2 0 4.4.8 6.3 2.5" fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="3"></path>
                                <path d="M27 19c0 3.5-2.6 6-6.5 6-2.2 0-4.4-.8-6.3-2.5" fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="3"></path>
                            </svg>
                        </span>
                        <span class="admin-brand__name">Doppar CMS</span>
                    </a>
                </div>

                <div class="admin-header__search-wrap">
                    <form class="admin-header__search" method="GET" action="[[ route('admin.posts.index') ]]" role="search">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <circle cx="11" cy="11" r="6.5"></circle>
                            <path d="M16 16l4.5 4.5"></path>
                        </svg>
                        <input type="search" name="search" value="[[ request('search', '') ]]" placeholder="Press / to search" data-global-search />
                    </form>
                </div>

                <div class="admin-header__right">
                    <div class="admin-dropdown" data-dropdown>
                        <button class="admin-header__icon" type="button" aria-label="Notifications" data-dropdown-trigger aria-expanded="false">
                            <span class="admin-header__icon-dot"></span>
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M12 4a4 4 0 0 0-4 4v2.2c0 1-.3 2-.9 2.8L6 15h12l-1.1-2c-.6-.8-.9-1.8-.9-2.8V8a4 4 0 0 0-4-4Z"></path>
                                <path d="M10 18a2 2 0 0 0 4 0"></path>
                            </svg>
                        </button>

                        <div class="admin-dropdown__menu admin-dropdown__menu--notifications" data-dropdown-menu>
                            <div class="admin-dropdown__head">
                                <strong>Notifications</strong>
                                <span>Editorial updates</span>
                            </div>

                            <a class="admin-dropdown__item" href="[[ route('admin.posts.index', ['status' => 'draft']) ]]">
                                <strong>Review draft queue</strong>
                                <span>Open all draft posts that still need review.</span>
                            </a>
                            <a class="admin-dropdown__item" href="[[ route('admin.posts.index', ['status' => 'published']) ]]">
                                <strong>Published content</strong>
                                <span>Check live-ready posts and final metadata.</span>
                            </a>
                            <a class="admin-dropdown__item" href="[[ route('admin.categories.index') ]]">
                                <strong>Category maintenance</strong>
                                <span>Audit category structure and clean taxonomy.</span>
                            </a>
                        </div>
                    </div>

                    <div class="admin-dropdown" data-dropdown>
                        <button class="admin-profile" type="button" data-dropdown-trigger aria-expanded="false">
                            <span class="admin-profile__avatar">ED</span>
                            <span class="admin-profile__name">Editorial Admin</span>
                            <svg class="admin-profile__chevron" viewBox="0 0 20 20" aria-hidden="true">
                                <path d="M5 7.5 10 12.5l5-5"></path>
                            </svg>
                        </button>

                        <div class="admin-dropdown__menu admin-dropdown__menu--profile" data-dropdown-menu>
                            <div class="admin-dropdown__head">
                                <strong>Editorial Admin</strong>
                                <span>Backend workspace</span>
                            </div>

                            <a class="admin-dropdown__item" href="[[ route('admin.dashboard') ]]">
                                <strong>Dashboard</strong>
                                <span>Overview, stats, and recent activity.</span>
                            </a>
                            <a class="admin-dropdown__item" href="[[ route('admin.posts.index') ]]">
                                <strong>Manage posts</strong>
                                <span>Search and maintain the publishing pipeline.</span>
                            </a>
                            <a class="admin-dropdown__item" href="[[ route('admin.media.index') ]]">
                                <strong>Media library</strong>
                                <span>Upload, browse, and reuse visual assets.</span>
                            </a>
                            <a class="admin-dropdown__item" href="[[ route('admin.users.index') ]]">
                                <strong>Manage users</strong>
                                <span>Control account access, roles, and profile details.</span>
                            </a>
                            <a class="admin-dropdown__item" href="[[ route('admin.tags.index') ]]">
                                <strong>Manage taxonomy</strong>
                                <span>Review tags and supporting content labels.</span>
                            </a>
                        </div>
                    </div>
                </div>
            </header>

            <button class="admin-sidebar-backdrop" type="button" data-sidebar-close aria-label="Close navigation"></button>

            <div class="admin-body-shell">
                <aside class="admin-sidebar" id="admin-sidebar">
                    <section class="admin-sidebar__section admin-sidebar__section--static">
                        <a class="admin-sidebar__group admin-sidebar__nav-link [[ str_contains((string) Route::currentRouteName(), 'admin.dashboard') ? 'is-active' : '' ]]" href="[[ route('admin.dashboard') ]]">
                            <span class="admin-sidebar__group-icon">
                                <svg viewBox="0 0 24 24" aria-hidden="true">
                                    <rect x="3" y="3" width="8" height="8" rx="1.5"></rect>
                                    <rect x="13" y="3" width="8" height="8" rx="1.5"></rect>
                                    <rect x="3" y="13" width="8" height="8" rx="1.5"></rect>
                                    <rect x="13" y="13" width="8" height="8" rx="1.5"></rect>
                                </svg>
                            </span>
                            <span>Dashboard</span>
                        </a>
                    </section>

                    <section class="admin-sidebar__section [[ str_contains((string) Route::currentRouteName(), 'admin.media.') ? 'is-open' : '' ]]" data-sidebar-group="media">
                        <button class="admin-sidebar__group" type="button" data-sidebar-group-trigger aria-expanded="[[ str_contains((string) Route::currentRouteName(), 'admin.media.') ? 'true' : 'false' ]]">
                            <span class="admin-sidebar__group-icon">
                                <svg viewBox="0 0 24 24" aria-hidden="true">
                                    <rect x="3.5" y="5" width="17" height="14" rx="2.5"></rect>
                                    <circle cx="9" cy="10" r="1.5"></circle>
                                    <path d="m20.5 15-4.2-4.2a1.4 1.4 0 0 0-2 0L8 17"></path>
                                </svg>
                            </span>
                            <span>Media</span>
                            <svg class="admin-sidebar__chevron" viewBox="0 0 20 20" aria-hidden="true">
                                <path d="M5 7.5 10 12.5l5-5"></path>
                            </svg>
                        </button>

                        <nav class="admin-sidebar__links" data-sidebar-group-panel>
                            <a class="admin-sidebar__link [[ request()->route('admin.media.index') && request('focus', '') !== 'upload' ? 'is-active' : '' ]]" href="[[ route('admin.media.index') ]]">Library</a>
                            <a class="admin-sidebar__link [[ str_contains((string) request('focus', ''), 'upload') ? 'is-active' : '' ]]" href="[[ route('admin.media.index') ]]?focus=upload#media-upload-panel">Upload New</a>
                        </nav>
                    </section>

                    <section class="admin-sidebar__section is-open" data-sidebar-group="resources">
                        <button class="admin-sidebar__group" type="button" data-sidebar-group-trigger aria-expanded="true">
                            <span class="admin-sidebar__group-icon">
                                <svg viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M5 8.5h14"></path>
                                    <path d="M7 8.5V7a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v1.5"></path>
                                    <rect x="4" y="8.5" width="16" height="10.5" rx="2"></rect>
                                </svg>
                            </span>
                            <span>Resources</span>
                            <svg class="admin-sidebar__chevron" viewBox="0 0 20 20" aria-hidden="true">
                                <path d="M5 7.5 10 12.5l5-5"></path>
                            </svg>
                        </button>

                        <nav class="admin-sidebar__links" data-sidebar-group-panel>
                            <a class="admin-sidebar__link [[ str_contains((string) Route::currentRouteName(), 'admin.categories.') ? 'is-active' : '' ]]" href="[[ route('admin.categories.index') ]]">Categories</a>
                            <a class="admin-sidebar__link [[ str_contains((string) Route::currentRouteName(), 'admin.posts.') ? 'is-active' : '' ]]" href="[[ route('admin.posts.index') ]]">Posts</a>
                            <a class="admin-sidebar__link [[ str_contains((string) Route::currentRouteName(), 'admin.tags.') ? 'is-active' : '' ]]" href="[[ route('admin.tags.index') ]]">Tags</a>
                            <a class="admin-sidebar__link [[ str_contains((string) Route::currentRouteName(), 'admin.users.') ? 'is-active' : '' ]]" href="[[ route('admin.users.index') ]]">Users</a>
                        </nav>
                    </section>
                </aside>

                <main class="admin-workspace">
                    #if (session('success'))
                        <div class="admin-alert admin-alert--success" data-dismissible>
                            <p>[[ session()->pull('success') ]]</p>
                            <button type="button" class="admin-alert__close" data-dismiss-alert aria-label="Dismiss message">×</button>
                        </div>
                    #endif

                    #if (session('error'))
                        <div class="admin-alert admin-alert--danger" data-dismissible>
                            <p>[[ session()->pull('error') ]]</p>
                            <button type="button" class="admin-alert__close" data-dismiss-alert aria-label="Dismiss message">×</button>
                        </div>
                    #endif

                    #errors
                        <div class="admin-alert admin-alert--danger admin-alert--stacked" data-dismissible>
                            <div class="admin-alert__body">
                                <p class="admin-alert__title">Please fix the following errors.</p>
                                <ul class="admin-alert__list">
                                    #foreach (session()->pull('errors') as $messages)
                                        #foreach ($messages as $message)
                                            <li>[[ $message ]]</li>
                                        #endforeach
                                    #endforeach
                                </ul>
                            </div>
                            <button type="button" class="admin-alert__close" data-dismiss-alert aria-label="Dismiss message">×</button>
                        </div>
                    #enderrors

                    <section class="admin-page-head">
                        <div class="admin-page-head__content">
                            <h1 class="admin-page-head__title">#yield('page_title')</h1>
                            <p class="admin-page-head__description">#yield('page_description')</p>
                        </div>

                        <div class="admin-page-head__actions">
                            #yield('page_actions')
                        </div>
                    </section>

                    <section class="admin-page-body">
                        #yield('content')
                    </section>
                </main>
            </div>
        </div>

        #yield('scripts')
    </body>
</html>
