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
    <style>
    .prose-article {
        font-family: Georgia, serif;
        font-size: 18px;
        line-height: 1.8;
        color: #1a1a1a;
    }
    .prose-article p { margin-bottom: 1.5em; }
    .prose-article h2 { font-size: 28px; font-weight: 700; margin: 2em 0 0.75em; line-height: 1.3; }
    .prose-article h3 { font-size: 22px; font-weight: 700; margin: 1.75em 0 0.5em; line-height: 1.3; }
    .prose-article h4 { font-size: 18px; font-weight: 700; margin: 1.5em 0 0.5em; }
    .prose-article ul, .prose-article ol { margin: 1em 0 1.5em 1.5em; }
    .prose-article li { margin-bottom: 0.4em; }
    .prose-article blockquote { border-left: 3px solid #e5e7eb; padding-left: 1.5em; margin: 1.5em 0; color: #6b7280; font-style: italic; }
    .prose-article a { color: #111827; text-decoration: underline; }
    .prose-article img { max-width: 100%; border-radius: 4px; margin: 1.5em 0; }
    .prose-article pre { background: #f3f4f6; padding: 1.25em; border-radius: 6px; overflow-x: auto; font-size: 14px; font-family: 'Courier New', monospace; margin: 1.5em 0; }
    .prose-article code { background: #f3f4f6; padding: 0.2em 0.4em; border-radius: 3px; font-size: 0.85em; font-family: 'Courier New', monospace; }
    .prose-article pre code { background: none; padding: 0; }
    .prose-article strong { font-weight: 700; }
    .prose-article em { font-style: italic; }
    .prose-article hr { border: none; border-top: 1px solid #e5e7eb; margin: 2.5em 0; }
    </style>
#endsection

#section('content')

<div class="min-h-screen bg-white" style="font-family: 'Inter', 'Helvetica Neue', Arial, sans-serif;">
    <header class="border-b border-gray-200 bg-white sticky top-0 z-20">
        <div class="max-w-[1192px] mx-auto px-6 flex items-center justify-between h-14">
            <a href="/" class="text-xl font-bold tracking-tight text-gray-900" style="font-family: Georgia, serif;">Blog</a>
        </div>
    </header>

    <main class="max-w-[740px] mx-auto px-6 py-12">

        #if ($post->category)
        <div class="mb-4">
            <a href="/" class="text-[13px] font-medium text-gray-500 hover:text-gray-800 transition-colors uppercase tracking-wide">
                [[ $post->category->name ]]
            </a>
        </div>
        #endif

        <h1 class="text-[36px] sm:text-[42px] font-bold leading-tight text-gray-900 mb-4" style="font-family: Georgia, serif;">
            [[ $post->title ]]
        </h1>

        #if ($post->excerpt)
        <p class="text-[20px] text-gray-500 leading-relaxed mb-6" style="font-family: Georgia, serif;">
            [[ $post->excerpt ]]
        </p>
        #endif

        <div class="flex items-center gap-3 py-4 border-t border-b border-gray-200 mb-8">
            <div class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center text-sm font-bold text-white flex-shrink-0">
                [[ strtoupper(substr($post->author_name ?? 'A', 0, 1)) ]]
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-900">[[ $post->author_name ?? 'Unknown' ]]</p>
                <div class="flex items-center gap-2 text-[13px] text-gray-500">
                    <span>[[ $post->published_at ? date('F j, Y', strtotime($post->published_at)) : '' ]]</span>
                    #if ($post->view_count)
                    <span class="text-gray-300">·</span>
                    <span class="flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        [[ number_format($post->view_count) ]] reads
                    </span>
                    #endif
                </div>
            </div>
        </div>

        #if ($post->cover_image)
        <figure class="mb-10 -mx-6 sm:mx-0">
            <img
                src="[[ $post->cover_image ]]"
                alt="[[ $post->title ]]"
                class="w-full max-h-[480px] object-cover sm:rounded-lg bg-gray-100"
            >
        </figure>
        #endif

        <div class="prose-article">
            [[! $post->body !]]
        </div>

        #if ($post->tags && count($post->tags) > 0)
        <div class="mt-12 pt-8 border-t border-gray-200">
            <div class="flex flex-wrap gap-2">
                #foreach ($post->tags as $tag)
                <span class="px-4 py-2 bg-gray-100 rounded-full text-[13px] text-gray-700 font-medium">[[ $tag ]]</span>
                #endforeach
            </div>
        </div>
        #endif

        <div class="mt-12 pt-8 border-t border-gray-200">
            <a href="/" class="inline-flex items-center gap-2 text-[14px] text-gray-500 hover:text-gray-900 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
                </svg>
                Back to stories
            </a>
        </div>

    </main>

</div>

#endsection
