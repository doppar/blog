<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>#yield('title')</title>
        <meta name="csrf-token" content="[[ csrf_token() ]]" />
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
        #errors
            <div class="app-feedback-stack">
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
            </div>
        #enderrors

        #yield('content')
        #yield('scripts')
    </body>
</html>
