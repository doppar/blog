<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>#yield('title', 'Doppar Blog')</title>
        <meta name="csrf-token" content="[[ csrf_token() ]]" />
        <meta name="google-site-verification" content="4ECeitrIJ7pSXTqNq5LCAYycS3EdBRmY24dr9mebrH8" />
        <meta name="description" content="#yield('meta_description', 'News, releases, tutorials and community updates from the Doppar PHP framework ecosystem.')">
        <link rel="canonical" href="[[ url()->current() ]]">
        <link rel="icon" type="image/x-icon" href="[[ url('/favicon.ico') ]]">
        <link rel="icon" type="image/png" sizes="16x16" href="[[ url('/favicon-16x16.png') ]]">
        <link rel="icon" type="image/png" sizes="32x32" href="[[ url('/favicon-32x32.png') ]]">
        <link rel="apple-touch-icon" sizes="180x180" href="[[ url('/apple-touch-icon.png') ]]">
        <meta property="og:site_name" content="Doppar News">
        <meta property="og:type" content="#yield('og_type', 'website')">
        <meta property="og:title" content="#yield('og_title', 'Doppar Blog')">
        <meta property="og:description" content="#yield('meta_description', 'News, releases, tutorials and community updates from the Doppar PHP framework ecosystem.')">
        <meta property="og:url" content="[[ url()->current() ]]">
        <meta property="og:image" content="#yield('og_image', url('/logo.png'))">
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="#yield('og_title', 'Doppar Blog')">
        <meta name="twitter:description" content="#yield('meta_description', 'News, releases, tutorials and community updates from the Doppar PHP framework ecosystem.')">
        <meta name="twitter:image" content="#yield('og_image', url('/logo.png'))">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600;9..144,700;9..144,800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
        #yield('head')
        #vite('resources/client/js/app.js')
    </head>
    <body class="public antialiased">
        #yield('content')
    </body>
    <!-- Cloudflare Web Analytics -->
    <script type='module' src='https://static.cloudflareinsights.com/beacon.min.js' data-cf-beacon='{"token": "c79e2cf32a374e6488657d0b281e7496"}'></script>
    <!-- End Cloudflare Web Analytics -->
</html>
