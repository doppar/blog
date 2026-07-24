<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>#yield('title')</title>
        <meta name="csrf-token" content="[[ csrf_token() ]]" />
        <link rel="icon" type="image/x-icon" href="[[ url('/favicon.ico') ]]">
        <link rel="icon" type="image/png" sizes="16x16" href="[[ url('/favicon-16x16.png') ]]">
        <link rel="icon" type="image/png" sizes="32x32" href="[[ url('/favicon-32x32.png') ]]">
        <link rel="apple-touch-icon" sizes="180x180" href="[[ url('/apple-touch-icon.png') ]]">
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
        #yield('head')
        <script>
            window.__DOPPAR_FRONTEND__ = {
                version: "[[ 'v' . Application::VERSION ]]",
                phpVersion: "PHP [[ phpversion() ]]",
                logoUrl: "[[ enqueue('logo.png') ]]",
                csrfToken: "[[ csrf_token() ]]"
            };
        </script>
        #vite('resources/client/js/app.js')
    </head>
    <body class="#yield('body_class')">
        #if (session('success') || session('error') || session()->has('errors'))
            <div class="app-feedback-stack">
                #if (session('success'))
                    <div class="admin-alert admin-alert--success" data-dismissible>
                        <p>[[ session()->pull('success') ]]</p>
                        <button type="button" class="admin-alert__close" data-dismiss-alert aria-label="Dismiss message">×</button>
                    </div>
                #endif

                #if (session('error'))
                    <div class="admin-alert admin-alert--danger" data-dismissible>
                        <p>[[ session()->pull('error') ]]</p>
                        <button type="button" class="admin-alert__close" data-dismiss-alert aria-label="Dismiss message">×</button>
                    </div>
                #endif

                #errors
                    <div class="admin-alert admin-alert--danger admin-alert--stacked" data-dismissible>
                        <div class="admin-alert__body">
                            <p class="admin-alert__title">Please fix the following errors.</p>
                            <ul class="admin-alert__list">
                                #foreach (session()->pull('errors') as $messages)
                                    #foreach ($messages as $message)
                                        <li>[[ $message ]]</li>
                                    #endforeach
                                #endforeach
                            </ul>
                        </div>
                        <button type="button" class="admin-alert__close" data-dismiss-alert aria-label="Dismiss message">×</button>
                    </div>
                #enderrors
            </div>
        #endif

        #yield('content')
        #yield('scripts')
    </body>
</html>
