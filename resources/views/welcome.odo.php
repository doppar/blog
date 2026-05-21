#extends('layouts.public')

#section('title', 'Blog – Stories Worth Reading')

#section('content')

<div class="min-h-screen bg-white" style="font-family: 'Inter', 'Helvetica Neue', Arial, sans-serif;">

    <header class="border-b border-gray-200 bg-white sticky top-0 z-20">
        <div class="max-w-[1192px] mx-auto px-6 flex items-center justify-between h-14">
            <a href="/" class="text-xl font-bold tracking-tight text-gray-900" style="font-family: Georgia, serif;">Blog</a>
            <nav class="flex items-center gap-4">
                <a href="[[ route('login') ]]" class="text-sm text-gray-600 hover:text-gray-900 transition-colors">Sign in</a>
                <a href="[[ route('login') ]]" class="text-sm bg-gray-900 text-white px-4 py-2 rounded-full hover:bg-gray-700 transition-colors">Get started</a>
            </nav>
        </div>
    </header>

    <div class="max-w-[1192px] mx-auto px-6">
        <div class="flex gap-12 xl:gap-16">

            <main class="flex-1 min-w-0 max-w-[740px]">

                <div class="border-b border-gray-200 flex gap-6 bg-white">
                    <a href="/?tab=for-you" class="py-3 text-sm font-medium border-b-2 transition-colors [[ $tab === 'for-you' ? 'border-gray-900 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700' ]]">
                        For you
                    </a>
                    <a href="/?tab=featured" class="py-3 text-sm font-medium border-b-2 transition-colors [[ $tab === 'featured' ? 'border-gray-900 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700' ]]">
                        Featured
                    </a>
                </div>

                <div class="divide-y divide-gray-200">
                    #foreach ($posts['data'] as $post)
                    <article class="py-8">


                        <div class="flex items-center gap-2 mb-3">
                            <div class="w-5 h-5 rounded-full bg-gray-300 flex items-center justify-center text-[10px] font-bold text-white flex-shrink-0">
                                [[ strtoupper(substr($post->author_name ?? 'A', 0, 1)) ]]
                            </div>
                            <span class="text-[13px] text-gray-600">[[ $post->author_name ?? 'Unknown' ]]</span>
                            #if ($post->category)
                            <span class="text-[13px] text-gray-400">in</span>
                            <span class="text-[13px] font-medium text-gray-700">[[ $post->category->name ]]</span>
                            #endif
                            <span class="text-gray-300">·</span>
                            <span class="text-[13px] text-gray-400">[[ $post->published_at ? date('M j', strtotime($post->published_at)) : '' ]]</span>
                        </div>

                        <div class="flex items-start gap-6">
                            <div class="flex-1 min-w-0">
                                <a href="/posts/[[ $post->slug ]]" class="group block">
                                    <h2 class="text-[20px] font-bold leading-snug text-gray-900 mb-2 group-hover:text-gray-600 transition-colors" style="font-family: Georgia, serif;">
                                        [[ $post->title ]]
                                    </h2>
                                    #if ($post->excerpt)
                                    <p class="text-[15px] text-gray-500 leading-relaxed line-clamp-2">
                                        [[ $post->excerpt ]]
                                    </p>
                                    #endif
                                </a>

                                <div class="flex items-center gap-4 mt-4 text-[13px] text-gray-400">
                                    #if ($post->is_featured)
                                    <svg class="w-4 h-4 text-yellow-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                    #endif
                                    <span class="flex items-center gap-1.5">
                                        <svg class="w-[17px] h-[17px]" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        [[ number_format($post->view_count ?? 0) ]]
                                    </span>
                                    <div class="ml-auto flex items-center gap-3">
                                        <button class="hover:text-gray-600 transition-colors" aria-label="Save">
                                            <svg class="w-[17px] h-[17px]" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0z"/>
                                            </svg>
                                        </button>
                                        <button class="hover:text-gray-600 transition-colors" aria-label="More">
                                            <svg class="w-[17px] h-[17px]" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 12a.75.75 0 11-1.5 0 .75.75 0 011.5 0zM12.75 12a.75.75 0 11-1.5 0 .75.75 0 011.5 0zM18.75 12a.75.75 0 11-1.5 0 .75.75 0 011.5 0z"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <a href="/posts/[[ $post->slug ]]" class="flex-shrink-0 block">
                                #if ($post->cover_image)
                                <img src="[[ $post->cover_image ]]" alt="[[ $post->title ]]" class="w-[112px] h-[84px] object-cover rounded bg-gray-100" loading="lazy">
                                #else
                                <div class="w-[112px] h-[84px] rounded bg-gray-100 flex items-center justify-center">
                                    <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/>
                                    </svg>
                                </div>
                                #endif
                            </a>
                        </div>

                    </article>
                    #endforeach
                </div>

                #if (empty($posts['data']))
                <div class="py-24 text-center">
                    <svg class="w-10 h-10 mx-auto mb-4 text-gray-200" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                    </svg>
                    <p class="text-[15px] text-gray-400">No stories published yet.</p>
                </div>
                #endif

                #if (paginator($posts)->hasPages())
                <div class="py-8 flex items-center justify-center gap-2">
                    #if (!paginator($posts)->onFirstPage())
                    <a href="[[ paginator($posts)->previousPageUrl() ]]&tab=[[ $tab ]]" class="px-4 py-2 rounded-full border border-gray-200 text-[13px] text-gray-600 hover:border-gray-400 transition-colors">
                        ← Previous
                    </a>
                    #endif
                    #if (paginator($posts)->hasMorePages())
                    <a href="[[ paginator($posts)->nextPageUrl() ]]&tab=[[ $tab ]]" class="px-4 py-2 rounded-full border border-gray-200 text-[13px] text-gray-600 hover:border-gray-400 transition-colors">
                        Next →
                    </a>
                    #endif
                </div>
                #endif

            </main>

            <aside class="hidden lg:block w-[300px] flex-shrink-0 pt-10 pb-8">

                <div class="mb-8">
                    <h3 class="text-[13px] font-semibold text-gray-900 mb-4">Recommended topics</h3>
                    <div class="flex flex-wrap gap-2">
                        <a href="/?tab=for-you" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-full text-[13px] text-gray-700 font-medium transition-colors">All stories</a>
                        <a href="/?tab=featured" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-full text-[13px] text-gray-700 font-medium transition-colors">Featured</a>
                    </div>
                </div>

                <p class="text-[12px] text-gray-400 leading-relaxed">
                    <a href="#" class="hover:text-gray-600 mr-2">Help</a>
                    <a href="#" class="hover:text-gray-600 mr-2">About</a>
                    <a href="[[ route('login') ]]" class="hover:text-gray-600">Sign in</a>
                </p>

            </aside>

        </div>
    </div>

</div>

#endsection
