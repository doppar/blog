#extends('layouts.app')
#section('title')
    Sign In
#endsection
#section('body_class')
    auth-body
#endsection
#section('content')
    <main class="auth-shell">
        <section class="auth-card">
            <div class="auth-card__brand">
                <span class="auth-card__mark">
                    <svg viewBox="0 0 40 40" aria-hidden="true">
                        <circle cx="20" cy="20" r="17" fill="none" stroke="currentColor" stroke-width="3"></circle>
                        <path d="M13 21c0-3.5 2.6-6 6.5-6 2.2 0 4.4.8 6.3 2.5" fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="3"></path>
                        <path d="M27 19c0 3.5-2.6 6-6.5 6-2.2 0-4.4-.8-6.3-2.5" fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="3"></path>
                    </svg>
                </span>
                <div>
                    <p class="auth-card__eyebrow">Doppar Blog</p>
                    <h1>Welcome back</h1>
                    <p>Sign in to continue to the editorial admin panel.</p>
                </div>
            </div>

            <form class="auth-form" action="[[ route('login') ]]" method="POST">
                #csrf

                <div class="auth-field">
                    <label for="email">Email</label>
                    <input id="email" name="email" type="email" value="[[ old('email') ]]" placeholder="you@example.com" autocomplete="email">
                </div>

                <div class="auth-field">
                    <label for="password">Password</label>
                    <input id="password" name="password" type="password" placeholder="Enter your password" autocomplete="current-password">
                </div>

                <label class="auth-check">
                    <input type="checkbox" name="remember" value="1" [[ old('remember', '') === '1' ? 'checked' : '' ]]>
                    <span>Remember this device</span>
                </label>

                <button class="auth-submit" type="submit">Sign In</button>
            </form>

            #if ($allowRegistration ?? false)
                <p class="auth-card__foot">No account yet? <a href="[[ route('register') ]]">Create the first administrator</a></p>
            #endif
        </section>
    </main>
#endsection
