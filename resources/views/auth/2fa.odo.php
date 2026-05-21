#extends('layouts.app')
#section('title')
    Verify Two-Factor Authentication
#endsection
#section('body_class')
    auth-body
#endsection
#section('content')
    <main class="auth-shell">
        <section class="auth-card auth-card--narrow">
            <div class="auth-card__brand">
                <span class="auth-card__mark auth-card__mark--secure">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M12 3 5 6v5c0 4.6 3 8.7 7 10 4-1.3 7-5.4 7-10V6l-7-3Z"></path>
                        <path d="m9.5 12 1.8 1.8 3.7-3.7"></path>
                    </svg>
                </span>
                <div>
                    <p class="auth-card__eyebrow">Two-factor verification</p>
                    <h1>Confirm your sign-in</h1>
                    <p>Enter the 6-digit authenticator code or one of your recovery codes.</p>
                </div>
            </div>

            <form class="auth-form" action="[[ route('verify.2fa') ]]" method="POST">
                #csrf

                <div class="auth-field">
                    <label for="token">Authentication code</label>
                    <input id="token" name="token" type="text" value="[[ old('token') ]]" placeholder="123456 or recovery code" autocomplete="one-time-code">
                </div>

                <button class="auth-submit" type="submit">Verify and continue</button>
            </form>
        </section>
    </main>
#endsection
