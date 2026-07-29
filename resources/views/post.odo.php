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

    #include('partials.header')

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
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.75v5.25l3.5 2M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            [[ max(1, (int) ceil(str_word_count(strip_tags($post->body ?? '')) / 200)) ]] min read
                        </span>
                        #if ($post->view_count)
                        <span class="flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            [[ number_format($post->view_count) ]] reads
                        </span>
                        #endif

                        <div class="relative">
                            <button type="button" id="share-btn" class="flex items-center gap-1.5 hover:text-ink transition-colors" aria-label="Share this post" aria-haspopup="true" aria-expanded="false">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7.217 10.907a2.25 2.25 0 100 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186l9.566-5.314m-9.566 7.5l9.566 5.314m0 0a2.25 2.25 0 103.935 2.186 2.25 2.25 0 00-3.935-2.186zm0-12.814a2.25 2.25 0 103.933-2.185 2.25 2.25 0 00-3.933 2.185z" />
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
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244" />
                                        </svg>
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
                class="w-full max-h-[520px] object-cover rounded-2xl shadow-[0_20px_60px_-30px_rgba(26,26,26,0.4)] bg-white">
        </figure>
        #endif

        <!-- Body -->
        <div class="max-w-3xl mx-auto px-6 py-14">
            <!-- Table of Contents -->
            <div id="toc-container" class="hidden mb-8 p-6 rounded-xl border border-soft bg-white">
                <button type="button" id="toc-toggle" class="flex items-center justify-between w-full text-left">
                    <h3 class="font-display text-sm font-bold tracking-wide uppercase text-ink-soft">Table of Contents</h3>
                    <svg id="toc-icon" class="w-5 h-5 text-ink-soft transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                    </svg>
                </button>
                <nav id="toc" class="space-y-2 text-sm mt-4 hidden"></nav>
            </div>

            <div class="prose-article">
                [[! $post->body !]]
            </div>

            <!-- Like Button -->
            <div class="mt-8 pt-8 border-t border-soft">
                #php
                $viewerHasLiked = auth()->check() && $post->likes()->where('user_id', auth()->id())->exists();
                #endphp
                #if (auth()->check())
                <button type="button" id="like-btn" data-slug="[[ $post->slug ]]" data-liked="[[ $viewerHasLiked ? '1' : '0' ]]" class="inline-flex items-center gap-2 px-4 py-2 rounded-full border border-soft text-sm font-medium text-ink hover:border-primary hover:text-primary transition-colors [[ $viewerHasLiked ? 'text-primary' : '' ]]">
                    <svg id="like-icon" class="w-5 h-5" fill="[[ $viewerHasLiked ? 'currentColor' : 'none' ]]" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                    </svg>
                    <span id="like-count">[[ $post->likes_count ?? 0 ]]</span>
                    <span id="like-text">[[ $viewerHasLiked ? 'Liked' : 'Like' ]]</span>
                </button>
                #else
                <a href="[[ route('login') ]]" class="inline-flex items-center gap-2 px-4 py-2 rounded-full border border-soft text-sm font-medium text-ink hover:border-primary hover:text-primary transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                    </svg>
                    <span>[[ $post->likes_count ?? 0 ]]</span>
                    <span>Log in to like</span>
                </a>
                #endif
            </div>

            <!-- Comments Section -->
            <div class="mt-12 pt-8 border-t border-soft">
                <h3 id="comments-heading" class="font-display text-xl font-bold text-ink mb-6">Comments (<span id="comments-count">[[ $post->comments_count ?? 0 ]]</span>)</h3>

                #if (auth()->check())
                <!-- Comment Form -->
                <form id="comment-form" class="mb-8">
                    <div class="mb-4">
                        <textarea
                            name="body"
                            id="comment-body"
                            rows="4"
                            placeholder="Write a comment..."
                            class="w-full px-4 py-3 rounded-xl border border-soft bg-white text-ink placeholder-ink-soft focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all resize-none"
                            required></textarea>
                        <p id="comment-form-error" class="mt-2 text-sm text-red-500 hidden"></p>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-full bg-primary text-white font-medium hover:brightness-110 transition-all">
                            Post Comment
                        </button>
                    </div>
                </form>
                #else
                <p class="text-ink-soft mb-6">
                    <a href="[[ route('login') ]]" class="text-primary hover:underline">Log in</a> to leave a comment.
                </p>
                #endif

                <!-- Comments List -->
                <div id="comments-container" class="space-y-6">
                    #php
                    $comments = $post->comments()->where('status', true)->whereNull('parent_id')->embed(['user', 'replies.user'])->newest()->get();
                    #endphp
                    #foreach ($comments as $comment)
                    #include('partials.comment', ['comment' => $comment])
                    #endforeach
                </div>
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
        </div>

        <!-- Share -->
        <div class="max-w-3xl mx-auto px-6 py-8">
            <div class="flex items-center justify-between">
                <button type="button" id="share-btn" class="inline-flex items-center gap-2 px-4 py-2 rounded-full border border-soft text-sm font-medium text-ink hover:border-ink transition-colors" aria-label="Share this post">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.217 10.907a2.25 2.25 0 100 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186l9.566-5.314m-9.566 7.5l9.566 5.314m0 0a2.25 2.25 0 103.935 2.186 2.25 2.25 0 00-3.935-2.186zm0-12.814a2.25 2.25 0 103.933-2.185 2.25 2.25 0 00-3.933 2.185z" />
                    </svg>
                    Share
                </button>
                <div id="share-menu" class="hidden absolute right-6 top-1/2 -translate-y-1/2 bg-white rounded-xl shadow-[0_20px_50px_-20px_rgba(26,26,26,0.4)] border border-soft overflow-hidden z-50" role="menu">
                    <a id="share-facebook" href="#" target="_blank" rel="noopener noreferrer" class="flex items-center gap-3 px-4 py-3 text-sm text-ink-soft hover:bg-[rgba(26,26,26,0.03)] transition-colors">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                        </svg>
                        Facebook
                    </a>
                    <a id="share-x" href="#" target="_blank" rel="noopener noreferrer" class="flex items-center gap-3 px-4 py-3 text-sm text-ink-soft hover:bg-[rgba(26,26,26,0.03)] transition-colors">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z" />
                        </svg>
                        X (Twitter)
                    </a>
                    <a id="share-linkedin" href="#" target="_blank" rel="noopener noreferrer" class="flex items-center gap-3 px-4 py-3 text-sm text-ink-soft hover:bg-[rgba(26,26,26,0.03)] transition-colors">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z" />
                        </svg>
                        LinkedIn
                    </a>
                    <button id="share-copy" type="button" class="flex items-center gap-3 px-4 py-3 text-sm text-ink-soft hover:bg-[rgba(26,26,26,0.03)] transition-colors w-full text-left">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.666 3.888A2.25 2.25 0 0013.5 2.25h-3c-1.03 0-1.9.693-2.166 1.638m7.332 0c.055.194.084.4.084.612v0a.75.75 0 01-.75.75H9a.75.75 0 01-.75-.75v0c0-.212.03-.418.084-.612m7.332 0c.646.049 1.288.11 1.927.184 1.1.128 1.907 1.077 1.907 2.185V19.5a2.25 2.25 0 01-2.25 2.25H6.75A2.25 2.25 0 014.5 19.5V6.257c0-1.108.806-2.057 1.907-2.185a48.208 48.208 0 011.927-.184" />
                        </svg>
                        Copy link
                    </button>
                </div>
                <a href="/" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full border border-soft text-sm font-medium text-ink hover:border-ink transition-colors">
                    More stories
                </a>
            </div>
        </div>

        <div class="max-w-3xl mx-auto px-6 py-8 flex items-center justify-between">
            <a href="/" class="inline-flex items-center gap-2 text-sm text-ink-soft hover:text-ink transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                Back to all stories
            </a>
            <button type="button" onclick="window.scrollTo({top:0, behavior:'smooth'})"
                class="inline-flex items-center gap-2 text-sm text-ink-soft hover:text-ink transition-colors">
                Top
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 10.5L12 3m0 0l7.5 7.5M12 3v18" />
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
    <div id="cover_image_for_js">[[! $post->cover_image ?? '' !]]</div>
</footer>

</div>

<script>
    // Share dropdown
    (() => {
        const btn = document.getElementById('share-btn');
        const menu = document.getElementById('share-menu');
        const copyBtn = document.getElementById('share-copy');
        if (!btn || !menu) return;

        const url = window.location.href;
        const title = document.title;
        const image = document.getElementById('cover_image_for_js').textContent;

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
                    setTimeout(() => {
                        copyBtn.innerHTML = original;
                        closeMenu();
                    }, 1200);
                } catch (_) {
                    /* clipboard unavailable */
                }
            });
        }
    })();

    // Code block copy buttons
    document.addEventListener('DOMContentLoaded', () => {
        const prose = document.querySelector('.prose-article');
        if (!prose) {
            console.log('Prose article not found, searching entire document');
            const codeBlocks = document.querySelectorAll('pre');
            addCopyButtons(codeBlocks);
            return;
        }

        const codeBlocks = prose.querySelectorAll('pre');
        addCopyButtons(codeBlocks);
    });

    function addCopyButtons(codeBlocks) {
        codeBlocks.forEach((pre, index) => {
            // Skip if already wrapped
            if (pre.parentElement.classList.contains('code-block-wrapper')) {
                return;
            }

            // Create wrapper
            const wrapper = document.createElement('div');
            wrapper.className = 'code-block-wrapper';

            // Insert wrapper before pre and move pre inside
            pre.parentNode.insertBefore(wrapper, pre);
            wrapper.appendChild(pre);

            // Create copy button
            const copyBtn = document.createElement('button');
            copyBtn.type = 'button';
            copyBtn.className = 'code-copy-btn';
            copyBtn.innerHTML = `
<svg class="copy-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
    <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
    <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
</svg>
<svg class="copied-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: none;">
    <polyline points="20 6 9 17 4 12"></polyline>
</svg>
`;
            copyBtn.setAttribute('aria-label', 'Copy code to clipboard');
            wrapper.appendChild(copyBtn);

            copyBtn.addEventListener('click', async () => {
                const code = pre.querySelector('code');
                const text = code ? code.textContent : pre.textContent;
                try {
                    await navigator.clipboard.writeText(text);
                    copyBtn.querySelector('.copy-icon').style.display = 'none';
                    copyBtn.querySelector('.copied-icon').style.display = 'block';
                    copyBtn.classList.add('copied');
                    setTimeout(() => {
                        copyBtn.querySelector('.copy-icon').style.display = 'block';
                        copyBtn.querySelector('.copied-icon').style.display = 'none';
                        copyBtn.classList.remove('copied');
                    }, 2000);
                } catch (err) {
                    console.error('Copy failed:', err);
                }
            });
        });
    }

    // Table of Contents
    (() => {
        const prose = document.querySelector('.prose-article');
        const tocContainer = document.getElementById('toc-container');
        const toc = document.getElementById('toc');
        const tocToggle = document.getElementById('toc-toggle');
        const tocIcon = document.getElementById('toc-icon');

        if (!prose || !tocContainer || !toc || !tocToggle || !tocIcon) return;

        const headings = prose.querySelectorAll('h2, h3');

        if (headings.length < 3) {
            return; // Only show TOC if there are 3+ headings
        }

        tocContainer.classList.remove('hidden');

        headings.forEach((heading, index) => {
            const id = `heading-${index}`;
            heading.id = id;

            const link = document.createElement('a');
            link.href = `#${id}`;
            link.textContent = heading.textContent;
            link.className = 'block text-ink-soft hover:text-primary transition-colors py-1';

            if (heading.tagName === 'H3') {
                link.classList.add('pl-4', 'text-xs');
            } else {
                link.classList.add('font-medium');
            }

            link.addEventListener('click', (e) => {
                e.preventDefault();
                heading.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            });

            toc.appendChild(link);
        });

        // Toggle functionality
        let isCollapsed = true;
        tocToggle.addEventListener('click', () => {
            isCollapsed = !isCollapsed;
            toc.classList.toggle('hidden', isCollapsed);
            // Rotate icon: 0deg when collapsed, 90deg when expanded
            tocIcon.style.transform = isCollapsed ? 'rotate(0deg)' : 'rotate(90deg)';
        });
    })();

    // Reading Progress Indicator
    (() => {
        const progressBar = document.createElement('div');
        progressBar.className = 'reading-progress-bar';
        progressBar.style.cssText = 'position: fixed; top: 0; left: 0; height: 3px; background: var(--c-primary); width: 0%; z-index: 1000; transition: width 0.1s ease;';
        document.body.appendChild(progressBar);

        const article = document.querySelector('article');
        if (!article) return;

        window.addEventListener('scroll', () => {
            const scrollTop = window.scrollY;
            const docHeight = article.offsetHeight;
            const winHeight = window.innerHeight;
            const scrollPercent = (scrollTop / (docHeight - winHeight)) * 100;

            const clampedPercent = Math.min(100, Math.max(0, scrollPercent));
            progressBar.style.width = `${clampedPercent}%`;
        });
    })();
</script>

#endsection