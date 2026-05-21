#extends('layouts.public')

#section('title', 'Blog – Stories Worth Reading')

#section('content')

<div class="min-h-screen bg-white" style="font-family: 'Inter', 'Helvetica Neue', Arial, sans-serif;">

    <header class="border-b border-gray-200 bg-white sticky top-0 z-20">
        <div class="max-w-[880px] mx-auto px-6 flex items-center h-14">
            <a href="/" class="text-xl font-bold tracking-tight text-gray-900" style="font-family: Georgia, serif;">Blog</a>
        </div>
    </header>

    <div class="max-w-[880px] mx-auto px-6">

        <main class="min-w-0">

            <div class="border-b border-gray-200 flex gap-6 bg-white">
                <a href="/?tab=for-you" class="py-3 text-sm font-medium border-b-2 transition-colors [[ $tab === 'for-you' ? 'border-gray-900 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700' ]]">
                    For you
                </a>
                <a href="/?tab=featured" class="py-3 text-sm font-medium border-b-2 transition-colors [[ $tab === 'featured' ? 'border-gray-900 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700' ]]">
                    Featured
                </a>
            </div>

            <div id="post-list" class="divide-y divide-gray-200">
                #include('partials.post-list-items', ['posts' => $posts['data']])
            </div>

            #if ($showEmptyState)
            <div class="py-24 text-center">
                <svg class="w-10 h-10 mx-auto mb-4 text-gray-200" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                </svg>
                <p class="text-[15px] text-gray-400">No stories published yet.</p>
            </div>
            #endif

            #if ($loadMore['has_more'])
            <div class="py-8 flex items-center justify-center">
                <button
                    type="button"
                    id="load-more-posts"
                    data-next-url="[[ $loadMore['next_url'] ]]"
                    class="px-5 py-2.5 rounded-full border border-gray-200 text-[13px] font-medium text-gray-700 hover:border-gray-400 transition-colors"
                >
                    Load more
                </button>
            </div>
            #endif

        </main>
    </div>

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
            const originalText = button.textContent.trim();
            button.textContent = 'Loading...';
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
                    button.textContent = originalText;
                    button.disabled = false;
                } else {
                    button.remove();
                }
            } catch (error) {
                button.textContent = 'Try again';
                button.disabled = false;
            } finally {
                isLoading = false;
            }
        });
    })();
</script>

#endsection
