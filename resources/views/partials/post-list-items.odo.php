#foreach ($posts as $post)
<article class="post-card group relative flex flex-col rounded-2xl border border-soft bg-white overflow-hidden">

    <!-- Cover -->
    <a href="/posts/[[ $post->slug ]]" class="block relative aspect-[16/9] overflow-hidden" style="background: [[ $post->accent_soft ]];">
        #if ($post->cover_image)
        <img
            src="[[ $post->cover_image ]]"
            alt="[[ $post->title ]]"
            class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-[1.03]"
            loading="lazy"
        >
        #else
        <div class="absolute inset-0 grid place-items-center">
            <div class="font-display text-6xl font-bold opacity-40" style="color: [[ $post->accent_solid ]];">
                [[ strtoupper(substr($post->title ?? 'D', 0, 1)) ]]
            </div>
            <div class="absolute inset-0 opacity-60" style="background: radial-gradient(120% 120% at 0% 0%, [[ $post->accent_soft ]] 0%, transparent 60%);"></div>
        </div>
        #endif

        <!-- Featured ribbon -->
        #if ($post->is_featured)
        <span class="absolute top-3 left-3 inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold tracking-wide uppercase bg-amber-a text-ink">
            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
            Featured
        </span>
        #endif
    </a>

    <!-- Body -->
    <div class="flex flex-col flex-1 p-5">

        <div class="flex items-center gap-2 mb-3 text-[12px]">
            #if ($post->category)
            <span class="chip" style="background: [[ $post->accent_soft ]]; color: [[ $post->accent_solid ]];">
                [[ $post->category->name ]]
            </span>
            #endif
            <span class="text-muted">·</span>
            <span class="text-ink-soft">[[ $post->published_at ? date('M j, Y', strtotime($post->published_at)) : 'Draft' ]]</span>
        </div>

        <a href="/posts/[[ $post->slug ]]" class="block">
            <h2 class="font-display text-xl font-bold leading-snug text-ink mb-2 transition-colors group-hover:text-primary">
                [[ $post->title ]]
            </h2>
            #if ($post->excerpt)
            <p class="text-[14px] text-ink-soft leading-relaxed line-clamp-3">
                [[ $post->excerpt ]]
            </p>
            #endif
        </a>

        <!-- Footer -->
        <div class="mt-auto pt-5 flex items-center justify-between">
            <div class="flex items-center gap-2 min-w-0">
                <span class="w-7 h-7 rounded-full grid place-items-center text-[11px] font-bold text-[#fefefe] flex-shrink-0"
                      style="background: [[ $post->accent_solid ]];">
                    [[ $post->author_initial ]]
                </span>
                <span class="text-[13px] font-medium text-ink truncate">[[ $post->author_name ?? 'Unknown' ]]</span>
            </div>
            <div class="flex items-center gap-3 text-[12px] text-ink-soft">
                <span class="flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    [[ number_format($post->view_count ?? 0) ]]
                </span>
                <a href="/posts/[[ $post->slug ]]" class="text-primary font-medium hover:underline">
                    Read →
                </a>
            </div>
        </div>
    </div>

</article>
#endforeach
