#extends('layouts.public')

#section('title', 'Doppar Blog — Stories from a faster PHP')

#section('content')

<div class="min-h-screen flex flex-col">

    <header class="sticky top-0 z-30 backdrop-blur-md bg-[rgba(254,254,254,0.85)] border-b border-soft">
        <div class="h-1 accent-bar"></div>
        <div class="max-w-7xl mx-auto px-6 flex items-center h-16 gap-8">
            <a href="/" class="flex items-center gap-2 group">
                <span class="w-8 h-8 rounded-lg bg-ink text-[#fefefe] grid place-items-center font-display font-bold text-lg group-hover:bg-primary transition-colors">D</span>
                <span class="font-display text-xl font-bold tracking-tight text-ink">Doppar <span class="text-primary">Blog</span></span>
            </a>
            <nav class="hidden md:flex items-center gap-6 text-sm font-medium text-ink-soft ml-4">
                <a href="/" class="hover:text-ink transition-colors">Latest</a>
                <a href="/?tab=featured" class="hover:text-ink transition-colors">Featured</a>
                <a href="https://doppar.com" class="hover:text-ink transition-colors">Documentation</a>
                <a href="https://github.com/doppar/framework" class="hover:text-ink transition-colors">GitHub</a>
            </nav>
            <div class="ml-auto flex items-center gap-3">
                <a href="/login" class="hidden sm:inline-flex text-sm font-medium text-ink-soft hover:text-ink transition-colors">Sign in</a>
                <a href="https://doppar.com" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-ink text-[#fefefe] text-sm font-medium hover:bg-primary transition-colors">
                    Get started
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                </a>
            </div>
        </div>
    </header>

    <section class="hero-grad border-b border-soft">
        <div class="max-w-7xl mx-auto px-6 py-16 sm:py-20">
            <div class="max-w-3xl">
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white border border-soft text-xs font-medium text-ink-soft mb-6">
                    <span class="w-1.5 h-1.5 rounded-full bg-green-a animate-pulse"></span>
                    The official Doppar journal
                </span>
                <h1 class="font-display text-4xl sm:text-5xl lg:text-6xl font-bold leading-[1.05] tracking-tight text-ink">
                    Stories that read like <span class="text-primary italic">prose</span>,
                    <br class="hidden sm:block">
                    engineered to <span class="text-cyan-a italic">scale</span>.
                </h1>
                <p class="mt-6 text-lg text-ink-soft max-w-2xl leading-relaxed">
                    Deep dives, release notes and craftsmanship from the people building Doppar — a PHP framework
                    obsessed with both expressiveness and throughput.
                </p>
                <div class="mt-8 flex flex-wrap items-center gap-3">
                    <a href="/?tab=for-you" class="chip border-soft text-ink hover:bg-primary hover:text-[#fefefe] hover:border-transparent transition-colors">All stories</a>
                    <a href="/?tab=featured" class="chip border-soft text-ink hover:bg-amber-a hover:text-ink hover:border-transparent transition-colors">★ Featured</a>
                    <a href="/?tab=for-you" class="chip border-soft text-ink hover:bg-cyan-a hover:text-[#fefefe] hover:border-transparent transition-colors">Tutorials</a>
                    <a href="/?tab=for-you" class="chip border-soft text-ink hover:bg-green-a hover:text-[#fefefe] hover:border-transparent transition-colors">Releases</a>
                    <a href="/?tab=for-you" class="chip border-soft text-ink hover:bg-pink-a hover:text-[#fefefe] hover:border-transparent transition-colors">Community</a>
                </div>
            </div>
        </div>
    </section>

    <!-- ================= MAIN ================= -->
    <main class="flex-1">
        <div class="max-w-7xl mx-auto px-6 py-12 lg:py-16 grid lg:grid-cols-[1fr_300px] gap-12">

            <!-- LEFT: feed -->
            <div class="min-w-0">
                <!-- Section heading + tabs -->
                <div class="flex items-end justify-between mb-8 pb-4 border-b border-soft">
                    <div>
                        <h2 class="font-display text-2xl sm:text-3xl font-bold tracking-tight text-ink">
                            [[ $tab === 'featured' ? 'Featured stories' : 'Latest stories' ]]
                        </h2>
                        <p class="text-sm text-ink-soft mt-1">Hand-picked writing from the Doppar team and community.</p>
                    </div>
                    <div class="hidden sm:flex items-center gap-1 p-1 rounded-full bg-white border border-soft">
                        <a href="/?tab=for-you"
                           class="px-4 py-1.5 rounded-full text-sm font-medium transition-colors [[ $tab === 'for-you' ? 'bg-ink text-[#fefefe]' : 'text-ink-soft hover:text-ink' ]]">
                            For you
                        </a>
                        <a href="/?tab=featured"
                           class="px-4 py-1.5 rounded-full text-sm font-medium transition-colors [[ $tab === 'featured' ? 'bg-ink text-[#fefefe]' : 'text-ink-soft hover:text-ink' ]]">
                            Featured
                        </a>
                    </div>
                </div>

                <div id="post-list" class="grid sm:grid-cols-2 gap-6">
                    #include('partials.post-list-items', ['posts' => $posts['data']])
                </div>

                #if ($showEmptyState)
                <div class="py-24 text-center border border-dashed border-soft rounded-2xl">
                    <div class="w-14 h-14 mx-auto mb-4 rounded-2xl bg-white border border-soft grid place-items-center">
                        <svg class="w-6 h-6 text-muted" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                        </svg>
                    </div>
                    <p class="text-base font-medium text-ink">No stories published yet</p>
                    <p class="text-sm text-ink-soft mt-1">Check back soon — fresh writing is on the way.</p>
                </div>
                #endif

                #if ($loadMore['has_more'])
                <div class="py-12 flex items-center justify-center">
                    <button
                        type="button"
                        id="load-more-posts"
                        data-next-url="[[ $loadMore['next_url'] ]]"
                        class="group inline-flex items-center gap-2 px-6 py-3 rounded-full border border-ink text-sm font-medium text-ink hover:bg-ink hover:text-[#fefefe] transition-colors"
                    >
                        Load more stories
                        <svg class="w-4 h-4 group-hover:translate-y-0.5 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
                        </svg>
                    </button>
                </div>
                #endif
            </div>

            <!-- RIGHT: sidebar -->
            <aside class="space-y-8 lg:sticky lg:top-24 lg:self-start">
                <div class="rounded-2xl border border-soft bg-white p-6">
                    <h3 class="font-display text-lg font-bold text-ink mb-2">Subscribe</h3>
                    <p class="text-sm text-ink-soft mb-4">Get the latest Doppar stories delivered to your inbox. No spam.</p>
                    <form class="space-y-2">
                        <input type="email" placeholder="you@domain.com" class="w-full px-3 py-2 rounded-lg border border-soft text-sm focus:outline-none focus:border-primary focus:ring-2 focus:ring-[rgba(109,108,243,.15)]">
                        <button type="button" class="w-full px-3 py-2 rounded-lg bg-primary text-[#fefefe] text-sm font-medium hover:opacity-90 transition-opacity">
                            Subscribe
                        </button>
                    </form>
                </div>

                <div>
                    <h3 class="font-display text-sm font-bold tracking-wide uppercase text-ink-soft mb-3">Topics</h3>
                    <div class="flex flex-wrap gap-2">
                        <a href="/" class="chip bg-white border-soft text-ink hover:border-primary"><span class="w-1.5 h-1.5 rounded-full bg-primary"></span>Framework</a>
                        <a href="/" class="chip bg-white border-soft text-ink hover:border-cyan-a"><span class="w-1.5 h-1.5 rounded-full bg-cyan-a"></span>Performance</a>
                        <a href="/" class="chip bg-white border-soft text-ink hover:border-green-a"><span class="w-1.5 h-1.5 rounded-full bg-green-a"></span>Releases</a>
                        <a href="/" class="chip bg-white border-soft text-ink hover:border-amber-a"><span class="w-1.5 h-1.5 rounded-full bg-amber-a"></span>Tutorials</a>
                        <a href="/" class="chip bg-white border-soft text-ink hover:border-pink-a"><span class="w-1.5 h-1.5 rounded-full bg-pink-a"></span>Community</a>
                        <a href="/" class="chip bg-white border-soft text-ink"><span class="w-1.5 h-1.5 rounded-full bg-gold-a"></span>Best practices</a>
                    </div>
                </div>

                <div class="rounded-2xl p-6 text-[#fefefe] relative overflow-hidden" style="background: linear-gradient(135deg, var(--c-ink) 0%, #2a2a2a 100%);">
                    <div class="absolute -top-8 -right-8 w-32 h-32 rounded-full" style="background: radial-gradient(circle, rgba(109,108,243,.5), transparent 70%);"></div>
                    <div class="relative">
                        <div class="font-mono text-[10px] tracking-widest text-cyan-a mb-2">// PSST</div>
                        <h3 class="font-display text-lg font-bold leading-snug mb-2">Build with Doppar.</h3>
                        <p class="text-sm text-[#babcbd] leading-relaxed mb-4">Expressive PHP, engineered for throughput. Read like prose, run like a machine.</p>
                        <a href="https://doppar.com" class="inline-flex items-center gap-1 text-sm font-medium text-amber-a link-anim">
                            Read the docs →
                        </a>
                    </div>
                </div>
            </aside>

        </div>
    </main>

    <!-- ================= FOOTER ================= -->
    <footer class="border-t border-soft mt-12">
        <div class="max-w-7xl mx-auto px-6 py-12 grid sm:grid-cols-2 lg:grid-cols-4 gap-10">
            <div class="lg:col-span-2">
                <a href="/" class="flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-ink text-[#fefefe] grid place-items-center font-display font-bold text-sm">D</span>
                    <span class="font-display text-lg font-bold tracking-tight text-ink">Doppar Blog</span>
                </a>
                <p class="text-sm text-ink-soft mt-3 max-w-sm">
                    The official journal of the Doppar framework — a modern PHP stack designed
                    to feel beautiful and run fast.
                </p>
            </div>
            <div>
                <h4 class="font-display text-sm font-bold text-ink mb-3">Doppar</h4>
                <ul class="space-y-2 text-sm text-ink-soft">
                    <li><a href="https://doppar.com" class="hover:text-ink transition-colors">Documentation</a></li>
                    <li><a href="https://github.com/doppar/framework" class="hover:text-ink transition-colors">GitHub</a></li>
                    <li><a href="/" class="hover:text-ink transition-colors">Releases</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-display text-sm font-bold text-ink mb-3">Blog</h4>
                <ul class="space-y-2 text-sm text-ink-soft">
                    <li><a href="/" class="hover:text-ink transition-colors">Latest</a></li>
                    <li><a href="/?tab=featured" class="hover:text-ink transition-colors">Featured</a></li>
                    <li><a href="/login" class="hover:text-ink transition-colors">Authors</a></li>
                </ul>
            </div>
        </div>
        <div class="border-t border-soft">
            <div class="max-w-7xl mx-auto px-6 py-5 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-ink-soft">
                <p>© [[ date('Y') ]] Doppar. Crafted with care.</p>
                <p class="font-mono">v3.x • PHP ^8.3</p>
            </div>
        </div>
    </footer>

</div>

<script>
    (() => {
        const button = document.getElementById('load-more-posts');
        const postList = document.getElementById('post-list');

        if (!button || !postList) {
            return;
        }

        let isLoading = false;

        button.addEventListener('click', async () => {
            const nextUrl = button.dataset.nextUrl;

            if (!nextUrl || isLoading) {
                return;
            }

            isLoading = true;
            const originalHTML = button.innerHTML;
            button.innerHTML = 'Loading…';
            button.disabled = true;

            try {
                const response = await fetch(nextUrl, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });

                if (!response.ok) {
                    throw new Error('Unable to load more posts.');
                }

                const payload = await response.json();

                if (payload.html) {
                    postList.insertAdjacentHTML('beforeend', payload.html);
                }

                if (payload.has_more && payload.next_url) {
                    button.dataset.nextUrl = payload.next_url;
                    button.innerHTML = originalHTML;
                    button.disabled = false;
                } else {
                    button.remove();
                }
            } catch (error) {
                button.innerHTML = 'Try again';
                button.disabled = false;
            } finally {
                isLoading = false;
            }
        });
    })();
</script>

#endsection
