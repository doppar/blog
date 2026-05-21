<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>#yield('title', 'Blog')</title>
        <meta name="csrf-token" content="[[ csrf_token() ]]" />
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Georgia&display=swap" rel="stylesheet">
        #yield('head')
        #vite('resources/client/js/app.js')
    </head>
    <body class="bg-white text-gray-900 antialiased">
        #yield('content')
    </body>
</html>
