#extends('layouts.public')

#section('title', '[[ $post->seo_title ?? $post->title ]]')

#section('head')
    #if ($post->seo_description)
    <meta name="description" content="[[ $post->seo_description ]]">
    #elseif ($post->excerpt)
    <meta name="description" content="[[ $post->excerpt ]]">
    #endif
    #if ($post->cover_image)
    <meta property="og:image" content="[[ $post->cover_image ]]">
    #endif
#endsection

#section('content')

<div class="min-h-screen flex flex-col">

    <div class="reading-progress" id="reading-progress"></div>

    <!-- Header -->
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
            </nav>
            <div class="ml-auto flex items-center gap-3">
                <a href="/" class="inline-flex items-center gap-1.5 text-sm font-medium text-ink-soft hover:text-ink transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                    All stories
                </a>
            </div>
        </div>
    </header>

    <!-- Article -->
    <article class="flex-1">

        <!-- Title block -->
        <div class="hero-grad border-b border-soft">
            <div class="max-w-3xl mx-auto px-6 py-14 sm:py-20">

                #if ($post->category)
                <a href="/" class="chip mb-6 bg-white border-soft text-primary hover:border-primary">
                    <span class="w-1.5 h-1.5 rounded-full bg-primary"></span>
                    [[ $post->category->name ]]
                </a>
                #endif

                <h1 class="font-display text-4xl sm:text-5xl lg:text-[56px] font-bold leading-[1.08] tracking-tight text-ink">
                    [[ $post->title ]]
                </h1>

                #if ($post->excerpt)
                <p class="mt-6 text-xl text-ink-soft leading-relaxed font-display font-normal">
                    [[ $post->excerpt ]]
                </p>
                #endif

                <div class="mt-8 flex flex-wrap items-center gap-x-5 gap-y-3">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-full bg-primary grid place-items-center text-sm font-bold text-[#fefefe]">
                            [[ strtoupper(substr($post->author_name ?? 'A', 0, 1)) ]]
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-ink">[[ $post->author_name ?? 'Unknown' ]]</p>
                            <p class="text-[12px] text-ink-soft">
                                [[ $post->published_at ? date('F j, Y', strtotime($post->published_at)) : 'Draft' ]]
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 ml-auto text-[13px] text-ink-soft">
                        #if ($post->view_count)
                        <span class="flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            [[ number_format($post->view_count) ]] reads
                        </span>
                        #endif
                        <button type="button" id="share-btn" class="flex items-center gap-1.5 hover:text-ink transition-colors" aria-label="Share">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7.217 10.907a2.25 2.25 0 100 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186l9.566-5.314m-9.566 7.5l9.566 5.314m0 0a2.25 2.25 0 103.935 2.186 2.25 2.25 0 00-3.935-2.186zm0-12.814a2.25 2.25 0 103.933-2.185 2.25 2.25 0 00-3.933 2.185z"/>
                            </svg>
                            Share
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cover -->
        #if ($post->cover_image)
        <figure class="max-w-5xl mx-auto px-6 -mt-6 sm:-mt-10">
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
            <div class="mt-14 pt-8 border-t border-soft">
                <h3 class="font-display text-sm font-bold tracking-wide uppercase text-ink-soft mb-3">Tagged with</h3>
                <div class="flex flex-wrap gap-2">
                    #foreach ($post->tags as $tag)
                    <span class="tag-pill">#[[ $tag->name ]]</span>
                    #endforeach
                </div>
            </div>
            #endif

            <!-- Author card -->
            <div class="mt-14 p-6 sm:p-8 rounded-2xl border border-soft bg-white flex flex-col sm:flex-row items-start gap-5">
                <div class="w-14 h-14 rounded-full bg-primary grid place-items-center text-lg font-bold text-[#fefefe] flex-shrink-0">
                    [[ strtoupper(substr($post->author_name ?? 'A', 0, 1)) ]]
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-mono text-[10px] tracking-widest uppercase text-cyan-a mb-1">Written by</p>
                    <p class="font-display text-xl font-bold text-ink">[[ $post->author_name ?? 'Unknown' ]]</p>
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
    <footer class="border-t border-soft mt-12">
        <div class="max-w-7xl mx-auto px-6 py-8 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-ink-soft">
            <p>© [[ date('Y') ]] Doppar. Crafted with care.</p>
            <p class="font-mono">v3.x • PHP ^8.3</p>
        </div>
    </footer>

</div>

<script>
    // Reading progress bar
    (() => {
        const bar = document.getElementById('reading-progress');
        if (!bar) return;
        const update = () => {
            const h = document.documentElement;
            const scrolled = h.scrollTop;
            const max = h.scrollHeight - h.clientHeight;
            const pct = max > 0 ? (scrolled / max) * 100 : 0;
            bar.style.width = pct + '%';
        };
        document.addEventListener('scroll', update, { passive: true });
        update();
    })();

    // Share button
    (() => {
        const btn = document.getElementById('share-btn');
        if (!btn) return;
        btn.addEventListener('click', async () => {
            const data = { title: document.title, url: window.location.href };
            try {
                if (navigator.share) {
                    await navigator.share(data);
                } else {
                    await navigator.clipboard.writeText(data.url);
                    const original = btn.innerHTML;
                    btn.innerHTML = '<span>Link copied ✓</span>';
                    setTimeout(() => { btn.innerHTML = original; }, 1800);
                }
            } catch (_) { /* user cancelled */ }
        });
    })();
</script>

#endsection
