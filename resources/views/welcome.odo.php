#extends('layouts.app')
#section('title')
    Blog Admin
#endsection
#section('body_class')
    admin-dashboard-shell
#endsection
#section('content')
<main class="min-h-screen p-3 sm:p-4 lg:p-5">
    <div class="mx-auto flex min-h-[calc(100vh-1.5rem)] max-w-[1600px] flex-col gap-3 rounded-[30px] border border-white/70 bg-white/70 p-3 shadow-[0_25px_80px_rgba(15,23,42,0.08)] backdrop-blur md:gap-4 md:p-4 lg:flex-row lg:rounded-[34px] lg:p-5">
        <aside class="w-full rounded-[28px] bg-white px-4 py-5 shadow-[inset_0_1px_0_rgba(255,255,255,0.8)] lg:w-[270px] lg:px-5">
            <div class="flex items-center justify-between gap-3 lg:block">
                <div class="flex items-center gap-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-700">
                        <svg class="h-7 w-7" viewBox="0 0 32 32" fill="none" aria-hidden="true">
                            <path d="M8.2 12.5C8.2 8.5 11.5 5.2 15.5 5.2C17.8 5.2 19.7 6.1 21 7.7C22 6.9 23.2 6.5 24.6 6.5C27.9 6.5 30.5 9.1 30.5 12.4C30.5 15.7 27.9 18.3 24.6 18.3H11.7C9.1 18.3 7 16.2 7 13.6C7 13.2 7 12.9 7.1 12.5H8.2Z" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M11.7 18.2V18.2C11.7 21.7 14.5 24.5 18 24.5H20.1C23.2 24.5 25.7 22 25.7 18.9V18.2" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-lg font-semibold tracking-tight text-slate-900">Doppar</p>
                        <p class="text-xs text-slate-400">Blog control center</p>
                    </div>
                </div>
                <button class="inline-flex h-10 w-10 items-center justify-center rounded-2xl border border-slate-200 text-slate-500 lg:hidden" type="button" aria-label="Open menu">
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                        <path d="M3.5 5.5H16.5M3.5 10H16.5M3.5 14.5H16.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                    </svg>
                </button>
            </div>

            <nav class="mt-7 space-y-7">
                <div>
                    <p class="mb-3 px-2 text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400">Menu</p>
                    <div class="space-y-1.5">
                        <a href="#" class="flex items-center gap-3 rounded-2xl bg-emerald-50 px-3 py-3 text-sm font-semibold text-emerald-800">
                            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-600 text-white">
                                <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                    <path d="M2.5 2.5H6.5V6.5H2.5V2.5ZM9.5 2.5H13.5V9.5H9.5V2.5ZM2.5 9.5H6.5V13.5H2.5V9.5ZM9.5 12H13.5V13.5H9.5V12Z" fill="currentColor"/>
                                </svg>
                            </span>
                            Dashboard
                        </a>
                        <a href="#" class="flex items-center gap-3 rounded-2xl px-3 py-3 text-sm text-slate-500 transition hover:bg-slate-50 hover:text-slate-900">
                            <span class="flex h-9 w-9 items-center justify-center rounded-xl border border-slate-200 bg-white">
                                <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                    <path d="M5 2.5H11L13 4.5V12C13 12.83 12.33 13.5 11.5 13.5H4.5C3.67 13.5 3 12.83 3 12V4C3 3.17 3.67 2.5 4.5 2.5H5Z" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/>
                                    <path d="M5.5 6.25H10.5M5.5 8.75H10.5" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
                                </svg>
                            </span>
                            Posts
                            <span class="ml-auto rounded-full bg-emerald-600 px-2 py-0.5 text-[10px] font-semibold text-white">12+</span>
                        </a>
                        <a href="#" class="flex items-center gap-3 rounded-2xl px-3 py-3 text-sm text-slate-500 transition hover:bg-slate-50 hover:text-slate-900">
                            <span class="flex h-9 w-9 items-center justify-center rounded-xl border border-slate-200 bg-white">
                                <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                    <rect x="2.75" y="3.25" width="10.5" height="10" rx="2" stroke="currentColor" stroke-width="1.4"/>
                                    <path d="M2.75 6H13.25M5.5 2.25V4.25M10.5 2.25V4.25" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
                                </svg>
                            </span>
                            Calendar
                        </a>
                        <a href="#" class="flex items-center gap-3 rounded-2xl px-3 py-3 text-sm text-slate-500 transition hover:bg-slate-50 hover:text-slate-900">
                            <span class="flex h-9 w-9 items-center justify-center rounded-xl border border-slate-200 bg-white">
                                <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                    <path d="M3 12.75H13M4 10.75L6.5 8.25L8.5 10.25L12 6.75" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M4 12.75V6.25M8 12.75V4.75M12 12.75V3.25" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                </svg>
                            </span>
                            Analytics
                        </a>
                        <a href="#" class="flex items-center gap-3 rounded-2xl px-3 py-3 text-sm text-slate-500 transition hover:bg-slate-50 hover:text-slate-900">
                            <span class="flex h-9 w-9 items-center justify-center rounded-xl border border-slate-200 bg-white">
                                <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                    <path d="M5.25 6C5.25 4.48 6.48 3.25 8 3.25C9.52 3.25 10.75 4.48 10.75 6C10.75 7.52 9.52 8.75 8 8.75C6.48 8.75 5.25 7.52 5.25 6Z" stroke="currentColor" stroke-width="1.4"/>
                                    <path d="M3 12.75C3.76 10.96 5.63 9.75 8 9.75C10.37 9.75 12.24 10.96 13 12.75" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
                                </svg>
                            </span>
                            Team
                        </a>
                    </div>
                </div>

                <div>
                    <p class="mb-3 px-2 text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400">General</p>
                    <div class="space-y-1.5">
                        <a href="#" class="flex items-center gap-3 rounded-2xl px-3 py-3 text-sm text-slate-500 transition hover:bg-slate-50 hover:text-slate-900">
                            <span class="flex h-9 w-9 items-center justify-center rounded-xl border border-slate-200 bg-white">
                                <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                    <path d="M8 2.75V4.25M8 11.75V13.25M4.29 4.29L5.35 5.35M10.65 10.65L11.71 11.71M2.75 8H4.25M11.75 8H13.25M4.29 11.71L5.35 10.65M10.65 5.35L11.71 4.29" stroke="currentColor" stroke-width="1.35" stroke-linecap="round"/>
                                    <circle cx="8" cy="8" r="2.3" stroke="currentColor" stroke-width="1.35"/>
                                </svg>
                            </span>
                            Settings
                        </a>
                        <a href="#" class="flex items-center gap-3 rounded-2xl px-3 py-3 text-sm text-slate-500 transition hover:bg-slate-50 hover:text-slate-900">
                            <span class="flex h-9 w-9 items-center justify-center rounded-xl border border-slate-200 bg-white">
                                <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                    <circle cx="8" cy="8" r="5.25" stroke="currentColor" stroke-width="1.35"/>
                                    <path d="M6.65 6.6A1.5 1.5 0 0 1 9.4 7.4C9.4 8.55 8 8.45 8 9.65M8 11.35H8.01" stroke="currentColor" stroke-width="1.35" stroke-linecap="round"/>
                                </svg>
                            </span>
                            Help
                        </a>
                        <a href="#" class="flex items-center gap-3 rounded-2xl px-3 py-3 text-sm text-slate-500 transition hover:bg-slate-50 hover:text-slate-900">
                            <span class="flex h-9 w-9 items-center justify-center rounded-xl border border-slate-200 bg-white">
                                <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                    <path d="M6 5L3.25 8L6 11M3.5 8H9.5M9 3.5H11.5C12.6 3.5 13.5 4.4 13.5 5.5V10.5C13.5 11.6 12.6 12.5 11.5 12.5H9" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            Logout
                        </a>
                    </div>
                </div>
            </nav>
        </aside>

        <section class="flex-1 rounded-[28px] bg-[#fcfbf8] p-3 sm:p-4 lg:p-5">
            <header class="mb-4 flex flex-col gap-3 rounded-[26px] bg-white p-3 shadow-[0_8px_30px_rgba(15,23,42,0.04)] sm:p-4 xl:flex-row xl:items-center xl:justify-between">
                <div class="flex flex-1 items-center gap-3">
                    <div class="relative flex-1">
                        <svg class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                            <path d="M8.75 15.5C12.48 15.5 15.5 12.48 15.5 8.75C15.5 5.02 12.48 2 8.75 2C5.02 2 2 5.02 2 8.75C2 12.48 5.02 15.5 8.75 15.5Z" stroke="currentColor" stroke-width="1.6"/>
                            <path d="M13.5 13.5L18 18" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                        </svg>
                        <input type="search" placeholder="Search task" class="h-14 w-full rounded-2xl border border-transparent bg-[#f7f5f2] pl-12 pr-16 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-emerald-200 focus:bg-white focus:ring-2 focus:ring-emerald-100" />
                        <span class="absolute right-3 top-1/2 hidden -translate-y-1/2 rounded-xl border border-slate-200 bg-white px-2.5 py-1 text-xs font-medium text-slate-400 sm:inline-flex">⌘ F</span>
                    </div>
                </div>

                <div class="flex items-center justify-between gap-3 sm:justify-end">
                    <div class="flex items-center gap-3 rounded-[22px] border border-slate-200 bg-white px-3 py-2.5">
                        <span class="flex h-11 w-11 items-center justify-center rounded-full border border-emerald-100 bg-gradient-to-br from-orange-100 via-rose-50 to-emerald-100 text-sm font-semibold text-slate-700">TM</span>
                        <div class="hidden pr-1 sm:block">
                            <p class="text-sm font-semibold text-slate-900">Totok Michael</p>
                            <p class="text-xs text-slate-400">tmichael20@mail.com</p>
                        </div>
                    </div>
                </div>
            </header>

            <div class="rounded-[28px] bg-white p-5 shadow-[0_8px_34px_rgba(15,23,42,0.04)] sm:p-6">
                <h1 class="text-[2.2rem] font-semibold leading-none tracking-[-0.04em] text-slate-950 sm:text-[2.5rem]">Dashboard</h1>
                <p class="mt-2 text-sm text-slate-400 sm:text-base">Welcome to dashboard.</p>
            </div>
        </section>
    </div>
</main>
#endsection
