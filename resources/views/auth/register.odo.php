#extends('layouts.app')
#section('title')
    Create Administrator
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
                    <p class="auth-card__eyebrow">First-time setup</p>
                    <h1>Create the first admin</h1>
                    <p>This account becomes the initial administrator for the CMS.</p>
                </div>
            </div>

            <form class="auth-form" action="[[ route('register') ]]" method="POST">
                #csrf

                <div class="auth-field">
                    <label for="name">Full name</label>
                    <input id="name" name="name" type="text" value="[[ old('name') ]]" placeholder="Your name" autocomplete="name">
                </div>

                <div class="auth-field">
                    <label for="email">Email</label>
                    <input id="email" name="email" type="email" value="[[ old('email') ]]" placeholder="admin@example.com" autocomplete="email">
                </div>

                <div class="auth-field">
                    <label for="password">Password</label>
                    <input id="password" name="password" type="password" placeholder="Create a secure password" autocomplete="new-password">
                </div>

                <div class="auth-field">
                    <label for="password_confirmation">Confirm password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" placeholder="Repeat your password" autocomplete="new-password">
                </div>

                <button class="auth-submit" type="submit">Create Administrator</button>
            </form>
        </section>
    </main>
#endsection
