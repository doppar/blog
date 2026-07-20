#extends('layouts.public')

#section('title')
[[ 'Doppar Blog — Stories from a faster PHP' ]]
#endsection

#section('content')

<div class="min-h-screen flex flex-col">

    <header class="sticky top-0 z-30 backdrop-blur-md bg-[rgba(254,254,254,0.85)] border-b border-soft">
        <div class="max-w-7xl mx-auto px-6 flex items-center h-16 gap-8">
            <a href="/" class="flex items-center gap-2 group">
                <span class="w-8 h-8 rounded-lg bg-ink text-[#fefefe] grid place-items-center font-display font-bold text-lg group-hover:bg-primary transition-colors">D</span>
                <span class="font-display text-xl font-bold tracking-tight text-ink">Doppar <span class="text-primary">Blog</span></span>
            </a>
            <nav class="hidden md:flex items-center gap-6 text-sm font-medium text-ink-soft ml-4">
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
    <footer class="relative overflow-hidden border-t border-soft mt-12">
        <div class="absolute top-1/2 left-1/4 -translate-x-1/2 -translate-y-1/2 w-72 h-72 rounded-full blur-[100px] pointer-events-none" style="background:rgba(109,108,243,.10)"></div>
        <div class="absolute top-1/2 right-1/4 translate-x-1/2 -translate-y-1/2 w-72 h-72 rounded-full blur-[100px] pointer-events-none" style="background:rgba(28,176,238,.10)"></div>

        <div class="relative max-w-2xl mx-auto px-6 py-14">
            <!-- Brand -->
            <div class="text-center mb-8">
                <img src="/logo.png" alt="Doppar" class="h-8 mx-auto mb-3">
                <p class="text-xs text-ink-soft">© [[ date('Y') ]] Doppar. All Rights Reserved.</p>
            </div>

            <!-- Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-8 sm:gap-4 text-sm">
                <div>
                    <h4 class="text-primary font-semibold mb-2">Links</h4>
                    <ul class="space-y-1.5 text-ink-soft">
                        <li><a href="https://doppar.com/versions/3.x/credits" target="_blank" rel="noopener noreferrer" class="hover:text-ink transition-colors">Credits</a></li>
                        <li><a href="https://doppar.com/versions/3.x/getting-started" target="_blank" rel="noopener noreferrer" class="hover:text-ink transition-colors">Getting started</a></li>
                        <li><a href="https://doppar.com/versions/3.x/releases" target="_blank" rel="noopener noreferrer" class="hover:text-ink transition-colors">Release notes</a></li>
                        <li><a href="https://doppar.com/versions/3.x/contributions" target="_blank" rel="noopener noreferrer" class="hover:text-ink transition-colors">Contributions</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-primary font-semibold mb-2">Get Involved</h4>
                    <ul class="space-y-1.5 text-ink-soft">
                        <li><a href="https://join.slack.com/t/zuno-global/shared_invite/zt-3xg3sl8vq-giLmT9rxCdDzQ7yEK53NvA" target="_blank" rel="noopener noreferrer" class="hover:text-ink transition-colors">Join community</a></li>
                        <li><a href="https://github.com/doppar/framework/issues" target="_blank" rel="noopener noreferrer" class="hover:text-ink transition-colors">Request features</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-primary font-semibold mb-2">Ecosystem</h4>
                    <ul class="space-y-1.5 text-ink-soft">
                        <li><a href="https://doppar.com/versions/3.x/doppar-ai" target="_blank" rel="noopener noreferrer" class="hover:text-ink transition-colors">AI</a></li>
                        <li><a href="https://doppar.com/versions/3.x/doppar-queue" target="_blank" rel="noopener noreferrer" class="hover:text-ink transition-colors">Queue</a></li>
                        <li><a href="https://doppar.com/versions/3.x/doppar-airbend" target="_blank" rel="noopener noreferrer" class="hover:text-ink transition-colors">Airbend</a></li>
                        <li><a href="https://doppar.com/versions/3.x/doppar-flarion" target="_blank" rel="noopener noreferrer" class="hover:text-ink transition-colors">Flarion</a></li>
                        <li><a href="https://doppar.com/versions/3.x/doppar-notifier" target="_blank" rel="noopener noreferrer" class="hover:text-ink transition-colors">Notifier</a></li>
                        <li><a href="https://doppar.com/versions/3.x/doppar-orion" target="_blank" rel="noopener noreferrer" class="hover:text-ink transition-colors">Orion</a></li>
                        <li><a href="https://doppar.com/versions/3.x/doppar-guard" target="_blank" rel="noopener noreferrer" class="hover:text-ink transition-colors">Guard</a></li>
                        <li><a href="https://doppar.com/versions/3.x/doppar-axios" target="_blank" rel="noopener noreferrer" class="hover:text-ink transition-colors">Axios</a></li>
                        <li><a href="https://doppar.com/versions/3.x/doppar-oauthic" target="_blank" rel="noopener noreferrer" class="hover:text-ink transition-colors">OAuthic</a></li>
                        <li><a href="https://doppar.com/versions/3.x/doppar-bloom" target="_blank" rel="noopener noreferrer" class="hover:text-ink transition-colors">Bloom</a></li>
                        <li><a href="https://doppar.com/versions/3.x/doppar-insight" target="_blank" rel="noopener noreferrer" class="hover:text-ink transition-colors">Insight</a></li>
                        <li><a href="https://doppar.com/versions/3.x/doppar-twig-bridge" target="_blank" rel="noopener noreferrer" class="hover:text-ink transition-colors">Twig Bridge</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-primary font-semibold mb-2">Connect</h4>
                    <div class="flex flex-col items-start gap-1.5 text-ink-soft">
                        <a href="https://x.com/dopparframework" target="_blank" rel="noopener noreferrer" class="hover:text-ink transition-colors">X (Twitter)</a>
                        <a href="https://www.linkedin.com/company/doppar/posts/?feedView=all" target="_blank" rel="noopener noreferrer" class="hover:text-ink transition-colors">LinkedIn</a>
                        <a href="https://www.buymeacoffee.com/mahedisulaa" target="_blank" rel="noopener noreferrer" class="mt-1.5 inline-flex items-center gap-1.5 bg-amber-a hover:brightness-95 text-ink text-xs px-3 py-1.5 rounded-md font-semibold transition-all w-fit">
                            ☕ Buy me a coffee
                        </a>
                    </div>
                </div>
            </div>

            <div class="mt-10 flex justify-center">
                <p class="text-sm font-medium text-ink-soft">Made with Doppar</p>
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
