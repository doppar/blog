#extends('layouts.public')

#section('title')[[ 'Saved stories — Doppar News' ]]#endsection

#section('content')

<div class="min-h-screen flex flex-col">

    #include('partials.header')

    <section class="hero-grad border-b border-soft">
        <div class="max-w-7xl mx-auto px-6 py-16 sm:py-20">
            <div class="max-w-3xl">
                <h1 class="font-display text-4xl sm:text-5xl font-bold leading-[1.05] tracking-tight text-ink">
                    Your saved stories
                </h1>
                <p class="mt-6 text-lg text-ink-soft max-w-2xl leading-relaxed">
                    Posts you have bookmarked to read later, all in one place.
                </p>
            </div>
        </div>
    </section>

    <main class="flex-1">
        <div class="max-w-7xl mx-auto px-6 py-12 lg:py-16">

            <div id="post-list" class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                #include('partials.post-list-items', ['posts' => $posts])
            </div>

            #if ($showEmptyState)
            <div class="py-24 text-center border border-dashed border-soft rounded-2xl">
                <div class="w-14 h-14 mx-auto mb-4 rounded-2xl bg-white border border-soft grid place-items-center">
                    <svg class="w-6 h-6 text-muted" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0z" />
                    </svg>
                </div>
                <p class="text-base font-medium text-ink">No saved stories yet</p>
                <p class="text-sm text-ink-soft mt-1">Tap the bookmark icon on any post to save it for later.</p>
            </div>
            #endif

            #if ($loadMore['has_more'])
            <div class="py-12 flex items-center justify-center">
                <button
                    type="button"
                    id="load-more-posts"
                    data-next-url="[[ $loadMore['next_url'] ]]"
                    class="group inline-flex items-center gap-2 px-6 py-3 rounded-full border border-ink text-sm font-medium text-ink hover:bg-ink hover:text-[#fefefe] transition-colors">
                    Load more stories
                    <svg class="w-4 h-4 group-hover:translate-y-0.5 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                    </svg>
                </button>
            </div>
            #endif
        </div>
    </main>

    <footer class="border-t border-soft mt-12">
        <div class="max-w-2xl mx-auto px-6 py-14 text-center">
            <img src="/logo.png" alt="Doppar" class="h-8 mx-auto mb-3">
            <p class="text-xs text-ink-soft">© [[ date('Y') ]] Doppar. All Rights Reserved.</p>
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
        const spinnerSvg = '<svg class="animate-spin" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>';

        button.addEventListener('click', async () => {
            const nextUrl = button.dataset.nextUrl;

            if (!nextUrl || isLoading) {
                return;
            }

            isLoading = true;
            const originalHTML = button.innerHTML;
            button.innerHTML = `${spinnerSvg}<span>Loading…</span>`;
            button.disabled = true;

            try {
                const response = await fetch(nextUrl, {
                    __skipAdminLoader: true,
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
