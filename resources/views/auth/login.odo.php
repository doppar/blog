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

            <div class="auth-divider"><span>or</span></div>

            <a href="[[ route('auth.google.redirect') . '?redirect_to=' . urlencode((string) request()->redirect_to) ]]" class="auth-social-btn">
                <svg viewBox="0 0 48 48" aria-hidden="true">
                    <path fill="#FFC107" d="M43.611 20.083H42V20H24v8h11.303c-1.649 4.657-6.08 8-11.303 8-6.627 0-12-5.373-12-12s5.373-12 12-12c3.059 0 5.842 1.154 7.961 3.039l5.657-5.657C34.046 6.053 29.268 4 24 4 12.955 4 4 12.955 4 24s8.955 20 20 20 20-8.955 20-20c0-1.341-.138-2.65-.389-3.917z"></path>
                    <path fill="#FF3D00" d="M6.306 14.691l6.571 4.819C14.655 15.108 18.961 12 24 12c3.059 0 5.842 1.154 7.961 3.039l5.657-5.657C34.046 6.053 29.268 4 24 4 16.318 4 9.656 8.337 6.306 14.691z"></path>
                    <path fill="#4CAF50" d="M24 44c5.166 0 9.86-1.977 13.409-5.192l-6.19-5.238C29.211 35.091 26.715 36 24 36c-5.202 0-9.619-3.317-11.283-7.946l-6.522 5.025C9.505 39.556 16.227 44 24 44z"></path>
                    <path fill="#1976D2" d="M43.611 20.083H42V20H24v8h11.303a12.04 12.04 0 0 1-4.087 5.571l.003-.002 6.19 5.238C36.971 39.205 44 34 44 24c0-1.341-.138-2.65-.389-3.917z"></path>
                </svg>
                <span>Continue with Google</span>
            </a>

            #if ($allowRegistration ?? false)
                <p class="auth-card__foot">No account yet? <a href="[[ route('register') ]]">Create the first administrator</a></p>
            #endif
        </section>
    </main>
#endsection
