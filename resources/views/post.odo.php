#extends('layouts.public')

#section('title')[[ $post->seo_title ?? $post->title ]]#endsection

#section('og_title')[[ $post->seo_title ?? $post->title ]]#endsection

#section('meta_description')[[ $post->seo_description ?? $post->excerpt ?? 'Read the latest on the Doppar PHP framework.' ]]#endsection

#section('og_type')[[ 'article' ]]#endsection

#if ($post->cover_image)
#section('og_image')[[! $post->cover_image !]]#endsection
#endif

#section('content')

<div class="min-h-screen flex flex-col">

    <!-- Header -->
    <header id="site-header" class="sticky top-0 z-40 backdrop-blur-md bg-[rgba(254,254,254,0.85)] border-b border-soft">
        <div class="max-w-7xl mx-auto px-6 flex items-center h-16 gap-6">
            <a href="/" class="flex items-center gap-2 group shrink-0">
                <img src="/logo-icon.png" alt="Doppar" class="h-8 w-auto">
                <span class="font-display text-xl font-bold tracking-tight text-ink"> <span class="text-primary">Blog</span></span>
            </a>

            <div class="ml-auto flex items-center gap-4">
                <a href="/" class="hidden sm:inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-sm font-medium text-ink-soft hover:text-ink hover:bg-[rgba(26,26,26,0.05)] transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                    All stories
                </a>

                <button type="button" id="mobile-menu-btn" class="md:hidden inline-flex items-center justify-center w-9 h-9 rounded-full text-ink-soft hover:text-ink hover:bg-[rgba(26,26,26,0.05)] transition-colors" aria-label="Toggle menu" aria-expanded="false" aria-controls="mobile-menu">
                    <svg id="menu-icon-open" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5M3.75 17.25h16.5"/></svg>
                    <svg id="menu-icon-close" class="w-5 h-5 hidden" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        <!-- Mobile menu -->
        <div id="mobile-menu" class="md:hidden hidden px-6 pb-4">
            <nav class="flex flex-col gap-1 text-sm font-medium text-ink-soft">
                <a href="/?tab=featured" class="px-3 py-2.5 rounded-lg hover:text-ink hover:bg-[rgba(26,26,26,0.05)] transition-colors">Featured</a>
                <a href="https://doppar.com" class="px-3 py-2.5 rounded-lg hover:text-ink hover:bg-[rgba(26,26,26,0.05)] transition-colors">Documentation</a>
                <a href="/" class="sm:hidden px-3 py-2.5 rounded-lg hover:text-ink hover:bg-[rgba(26,26,26,0.05)] transition-colors">All stories</a>
            </nav>
        </div>
    </header>

    <!-- Article -->
    <article class="flex-1">

        <!-- Title block -->
        <div class="hero-grad">
            <div class="max-w-3xl mx-auto px-6 py-14 sm:py-20">

                #if ($post->category)
                <a href="/" class="chip mb-6 bg-white text-primary">
                    <span class="w-1.5 h-1.5 rounded-full bg-primary"></span>
                    [[ $post->category->name ]]
                </a>
                #endif

                <h1 class="font-display text-4xl sm:text-5xl lg:text-[56px] font-bold leading-[1.08] tracking-tight text-ink">
                    [[ $post->title ]]
                </h1>

                <div class="mt-8 flex flex-wrap items-center gap-x-5 gap-y-3">
                    <div class="flex items-center gap-3">
                        #if (($post->user->image ?? '') !== '')
                        <img src="[[ $post->user->image ]]" alt="[[ $post->user->name ]]" class="w-11 h-11 rounded-full object-cover">
                        #else
                        <div class="w-11 h-11 rounded-full bg-primary grid place-items-center text-sm font-bold text-[#fefefe]">
                            [[ strtoupper(substr($post->user->name ?? 'A', 0, 1)) ]]
                        </div>
                        #endif
                        <div>
                            <p class="text-sm font-semibold text-ink">[[ $post->user->name ?? 'Admin' ]]</p>
                            <p class="text-[12px] text-ink-soft">
                                [[ $post->published_at ? date('F j, Y', strtotime($post->published_at)) : date('F j, Y', strtotime($post->created_at)) ]]
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 ml-auto text-[13px] text-ink-soft">
                        <span class="flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.75v5.25l3.5 2M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            [[ max(1, (int) ceil(str_word_count(strip_tags($post->body ?? '')) / 200)) ]] min read
                        </span>
                        #if ($post->view_count)
                        <span class="flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            [[ number_format($post->view_count) ]] reads
                        </span>
                        #endif

                        <div class="relative">
                            <button type="button" id="share-btn" class="flex items-center gap-1.5 hover:text-ink transition-colors" aria-label="Share this post" aria-haspopup="true" aria-expanded="false">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7.217 10.907a2.25 2.25 0 100 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186l9.566-5.314m-9.566 7.5l9.566 5.314m0 0a2.25 2.25 0 103.935 2.186 2.25 2.25 0 00-3.935-2.186zm0-12.814a2.25 2.25 0 103.933-2.185 2.25 2.25 0 00-3.933 2.185z"/>
                                </svg>
                                Share
                            </button>

                            <div id="share-menu" class="hidden absolute right-0 top-full mt-2 w-56 rounded-2xl bg-white shadow-[0_20px_50px_-20px_rgba(26,26,26,0.4)] py-2 text-left z-50">
                                <a id="share-facebook" href="#" target="_blank" rel="noopener noreferrer" class="flex items-center gap-3 px-4 py-2.5 text-sm text-ink hover:bg-[rgba(26,26,26,0.05)] transition-colors">
                                    <span class="w-6 h-6 rounded-full grid place-items-center text-[11px] font-bold text-white shrink-0" style="background:#1877F2">f</span>
                                    Facebook
                                </a>
                                <a id="share-x" href="#" target="_blank" rel="noopener noreferrer" class="flex items-center gap-3 px-4 py-2.5 text-sm text-ink hover:bg-[rgba(26,26,26,0.05)] transition-colors">
                                    <span class="w-6 h-6 rounded-full grid place-items-center text-[11px] font-bold text-white shrink-0" style="background:#000000">X</span>
                                    X
                                </a>
                                <a id="share-linkedin" href="#" target="_blank" rel="noopener noreferrer" class="flex items-center gap-3 px-4 py-2.5 text-sm text-ink hover:bg-[rgba(26,26,26,0.05)] transition-colors">
                                    <span class="w-6 h-6 rounded-full grid place-items-center text-[11px] font-bold text-white shrink-0" style="background:#0A66C2">in</span>
                                    LinkedIn
                                </a>
                                <a id="share-pinterest" href="#" target="_blank" rel="noopener noreferrer" class="flex items-center gap-3 px-4 py-2.5 text-sm text-ink hover:bg-[rgba(26,26,26,0.05)] transition-colors">
                                    <span class="w-6 h-6 rounded-full grid place-items-center text-[11px] font-bold text-white shrink-0" style="background:#E60023">P</span>
                                    Pinterest
                                </a>
                                <a id="share-tumblr" href="#" target="_blank" rel="noopener noreferrer" class="flex items-center gap-3 px-4 py-2.5 text-sm text-ink hover:bg-[rgba(26,26,26,0.05)] transition-colors">
                                    <span class="w-6 h-6 rounded-full grid place-items-center text-[11px] font-bold text-white shrink-0" style="background:#34526f">t</span>
                                    Tumblr
                                </a>
                                <div class="my-1.5 h-px bg-[rgba(26,26,26,0.08)]"></div>
                                <button type="button" id="share-copy" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-ink hover:bg-[rgba(26,26,26,0.05)] transition-colors">
                                    <span class="w-6 h-6 rounded-full grid place-items-center bg-[rgba(26,26,26,0.06)] text-ink-soft shrink-0">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244"/></svg>
                                    </span>
                                    Copy link
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cover -->
        #if ($post->cover_image)
        <figure class="max-w-3xl mx-auto px-6 -mt-6 sm:-mt-10">
            <img
                src="[[ $post->cover_image ]]"
                alt="[[ $post->title ]]"
                class="w-full max-h-[520px] object-cover rounded-2xl shadow-[0_20px_60px_-30px_rgba(26,26,26,0.4)] bg-white"
            >
        </figure>
        #endif

        <!-- Body -->
        <div class="max-w-3xl mx-auto px-6 py-14">
            <div class="prose-article">
                [[! $post->body !]]
            </div>

            #if ($post->tags && count($post->tags) > 0)
            <div class="mt-14 pt-8">
                <h3 class="font-display text-sm font-bold tracking-wide uppercase text-ink-soft mb-3">Tagged with</h3>
                <div class="flex flex-wrap gap-2">
                    #foreach ($post->tags as $tag)
                    <span class="tag-pill">[[ $tag->name ]]</span>
                    #endforeach
                </div>
            </div>
            #endif

            <!-- Author card -->
            <div class="mt-14 p-6 sm:p-8 rounded-2xl border border-soft bg-white flex flex-col sm:flex-row items-start gap-5">
                #if (($post->user->image ?? '') !== '')
                <img src="[[ $post->user->image ]]" alt="[[ $post->user->name ]]" class="w-14 h-14 rounded-full object-cover flex-shrink-0">
                #else
                <div class="w-14 h-14 rounded-full bg-primary grid place-items-center text-lg font-bold text-[#fefefe] flex-shrink-0">
                    [[ strtoupper(substr($post->user->name ?? 'A', 0, 1)) ]]
                </div>
                #endif
                <div class="flex-1 min-w-0">
                    <p class="font-mono text-[10px] tracking-widest uppercase text-cyan-a mb-1">Written by</p>
                    <p class="font-display text-xl font-bold text-ink">[[ $post->user->name ?? 'Unknown' ]]</p>
                    <p class="text-sm text-ink-soft mt-1">Building things with Doppar — sharing notes, tips and deep dives along the way.</p>
                </div>
                <a href="/" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full border border-soft text-sm font-medium text-ink hover:border-ink transition-colors">
                    More stories
                </a>
            </div>

            <div class="mt-12 flex items-center justify-between">
                <a href="/" class="inline-flex items-center gap-2 text-sm text-ink-soft hover:text-ink transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
                    </svg>
                    Back to all stories
                </a>
                <button type="button" onclick="window.scrollTo({top:0, behavior:'smooth'})"
                        class="inline-flex items-center gap-2 text-sm text-ink-soft hover:text-ink transition-colors">
                    Top
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 10.5L12 3m0 0l7.5 7.5M12 3v18"/>
                    </svg>
                </button>
            </div>
        </div>

    </article>

    <!-- Footer -->
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

    // Share dropdown
    (() => {
        const btn = document.getElementById('share-btn');
        const menu = document.getElementById('share-menu');
        const copyBtn = document.getElementById('share-copy');
        if (!btn || !menu) return;

        const url = window.location.href;
        const title = document.title;
        const image = [[! json_encode($post->cover_image ?? '') !]];

        const targets = {
            'share-facebook': `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`,
            'share-x': `https://twitter.com/intent/tweet?url=${encodeURIComponent(url)}&text=${encodeURIComponent(title)}`,
            'share-linkedin': `https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(url)}`,
            'share-pinterest': `https://pinterest.com/pin/create/button/?url=${encodeURIComponent(url)}&media=${encodeURIComponent(image)}&description=${encodeURIComponent(title)}`,
            'share-tumblr': `https://www.tumblr.com/widgets/share/tool?canonicalUrl=${encodeURIComponent(url)}&title=${encodeURIComponent(title)}`,
        };
        Object.entries(targets).forEach(([id, href]) => {
            const el = document.getElementById(id);
            if (el) el.href = href;
        });

        const closeMenu = () => {
            menu.classList.add('hidden');
            btn.setAttribute('aria-expanded', 'false');
        };
        const openMenu = () => {
            menu.classList.remove('hidden');
            btn.setAttribute('aria-expanded', 'true');
        };

        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            menu.classList.contains('hidden') ? openMenu() : closeMenu();
        });
        document.addEventListener('click', (e) => {
            if (!menu.contains(e.target) && e.target !== btn) closeMenu();
        });
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeMenu();
        });

        if (copyBtn) {
            copyBtn.addEventListener('click', async () => {
                try {
                    await navigator.clipboard.writeText(url);
                    const original = copyBtn.innerHTML;
                    copyBtn.innerHTML = '<span class="px-1">Link copied ✓</span>';
                    setTimeout(() => { copyBtn.innerHTML = original; closeMenu(); }, 1200);
                } catch (_) { /* clipboard unavailable */ }
            });
        }
    })();
</script>

#endsection
