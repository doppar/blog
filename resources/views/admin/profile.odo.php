#extends('layouts.admin')
#section('title')
    Profile
#endsection
#section('page_title')
    Profile
#endsection
#section('page_description')
    Manage your profile, password, and two-factor authentication.
#endsection
#section('page_actions')
    <a class="admin-button admin-button--ghost" href="[[ route('admin.dashboard') ]]">Back to dashboard</a>
#endsection
#section('content')
    <section class="admin-profile-page">
        <div class="admin-profile-section">
            <div class="admin-profile-section__intro">
                <h2>Profile Information</h2>
                <p>Update your account's profile information and email address.</p>
            </div>

            <div class="admin-panel">
                <form method="POST" action="[[ route('admin.profile.information') ]]" enctype="multipart/form-data" class="admin-profile-form">
                    #csrf

                    <div class="admin-form-grid__fields">
                        <div class="admin-field">
                            <label for="profile-name">Name</label>
                            <input id="profile-name" name="name" type="text" value="[[ Auth::user()->name ]]" autocomplete="name">
                            #error('name')
                                <p class="admin-field__error">[[ $message ]]</p>
                            #enderror
                        </div>

                        <div class="admin-field">
                            <label for="profile-email">Email</label>
                            <input id="profile-email" name="email" type="email" value="[[ Auth::user()->email ]]" autocomplete="email">
                            #error('email')
                                <p class="admin-field__error">[[ $message ]]</p>
                            #enderror
                        </div>

                        <div class="admin-field admin-field--full">
                            <div class="admin-field__label-row">
                                <label for="profile-image">Profile image</label>
                            </div>
                            <div class="admin-profile-upload">
                                <div class="admin-cover-preview [[ ($user->image ?? '') === '' ? 'is-hidden' : '' ]]" data-user-image-preview>
                                    <img src="[[ $user->image ?? '' ]]" alt="Profile image preview" data-user-image-preview-image>
                                </div>
                                <input id="profile-image" name="image_file" class="admin-field__file-input" type="file" accept="image/*" data-user-image-input>
                            </div>
                            <p class="admin-field__hint">Upload a JPG, PNG, WEBP, GIF, AVIF, or SVG image for your profile avatar.</p>
                            #error('image_file')
                                <p class="admin-field__error">[[ $message ]]</p>
                            #enderror
                        </div>
                    </div>

                    <button class="admin-button" type="submit">Update profile</button>
                </form>
            </div>
        </div>

        <div class="admin-profile-section">
            <div class="admin-profile-section__intro">
                <h2>Update Password</h2>
                <p>Ensure your account is using a strong password to stay secure.</p>
            </div>

            <div class="admin-panel">
                <form method="POST" action="[[ route('admin.profile.password') ]]" class="admin-profile-form">
                    #csrf

                    <div class="admin-form-grid__fields">
                        <div class="admin-field admin-field--full">
                            <label for="current-password">Current password</label>
                            <input id="current-password" name="current_password" type="password" value="" autocomplete="current-password">
                            #error('current_password')
                                <p class="admin-field__error">[[ $message ]]</p>
                            #enderror
                        </div>

                        <div class="admin-field">
                            <label for="new-password">New password</label>
                            <input id="new-password" name="password" type="password" value="" autocomplete="new-password">
                            #error('password')
                                <p class="admin-field__error">[[ $message ]]</p>
                            #enderror
                        </div>

                        <div class="admin-field">
                            <label for="new-password-confirmation">Confirm password</label>
                            <input id="new-password-confirmation" name="password_confirmation" type="password" value="" autocomplete="new-password">
                        </div>
                    </div>

                    <button class="admin-button" type="submit">Update password</button>
                </form>
            </div>
        </div>

        <div class="admin-profile-section admin-profile-section--two-factor">
            <div class="admin-profile-section__intro">
                <h2>Two-Factor Authentication</h2>
                <p>Add extra security to your account using two-factor authentication (2FA).</p>
            </div>

            <div class="admin-panel">
                <div class="admin-profile-security">
                    <div class="admin-profile-security__status [[ $twoFactorEnabled ? 'is-enabled' : 'is-disabled' ]]">
                        <span class="admin-profile-security__dot"></span>
                        <div>
                            <strong>Two-factor authentication is currently [[ $twoFactorEnabled ? 'enabled' : 'disabled' ]].</strong>
                            <p>[[ $twoFactorEnabled ? 'Your account requires a second verification step during sign-in.' : 'Protect your account with a 6-digit code from your authenticator app.' ]]</p>
                        </div>
                    </div>

                    #if ($twoFactorEnabled)
                        <div class="admin-profile-security__actions">
                            <form method="POST" action="[[ route('admin.profile.two-factor.disable') ]]">
                                #csrf
                                <button class="admin-button admin-button--ghost" type="submit">Disable 2FA</button>
                            </form>

                            <form method="POST" action="[[ route('admin.profile.two-factor.recovery-codes') ]]">
                                #csrf
                                <button class="admin-button admin-button--ghost" type="submit">Generate new recovery codes</button>
                            </form>
                        </div>
                    #else
                        <div class="admin-profile-security__actions">
                            #if ($pendingTwoFactorSetup)
                                <button class="admin-button" type="button" data-two-factor-modal-open>Resume setup</button>
                                <form method="POST" action="[[ route('admin.profile.two-factor.cancel') ]]">
                                    #csrf
                                    <button class="admin-button admin-button--ghost" type="submit">Cancel setup</button>
                                </form>
                            #else
                                <form method="POST" action="[[ route('admin.profile.two-factor.start') ]]">
                                    #csrf
                                    <button class="admin-button" type="submit">Enable 2FA</button>
                                </form>
                            #endif
                        </div>
                    #endif

                    #if (!empty($recoveryCodes))
                        <div class="admin-profile-recovery">
                            <div class="admin-profile-recovery__head">
                                <h3>Recovery codes</h3>
                                <p>Store these one-time recovery codes somewhere safe. Each code can only be used once.</p>
                            </div>
                            <div class="admin-security-codes__grid">
                                #foreach ($recoveryCodes as $code)
                                    <span>[[ $code ]]</span>
                                #endforeach
                            </div>
                        </div>
                    #endif
                </div>
            </div>
        </div>
    </section>

    #if ($pendingTwoFactorSetup)
        <form id="two-factor-cancel-form" method="POST" action="[[ route('admin.profile.two-factor.cancel') ]]" class="admin-u-sr-only">
            #csrf
        </form>

        <div
            class="admin-two-factor-modal"
            data-two-factor-modal
            data-auto-open="true"
            aria-hidden="true"
        >
            <button class="admin-two-factor-modal__backdrop" type="button" data-two-factor-modal-close aria-label="Close two-factor setup"></button>

            <div class="admin-two-factor-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="two-factor-modal-title">
                <div class="admin-two-factor-modal__head">
                    <div>
                        <p class="admin-section__eyebrow">Two-factor setup</p>
                        <h3 id="two-factor-modal-title">Scan the QR code to enable 2FA</h3>
                        <p>Use Google Authenticator, Authy, Microsoft Authenticator, or another TOTP-compatible app.</p>
                    </div>
                    <button class="admin-two-factor-modal__close" type="button" data-two-factor-modal-close aria-label="Close two-factor setup">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="admin-two-factor-modal__body">
                    <div class="admin-two-factor-modal__qr">
                        <img src="[[ $pendingTwoFactorSetup['qr_code_svg'] ]]" alt="Scan this QR code with your authenticator app" width="200" height="200">
                    </div>

                    <form method="POST" action="[[ route('admin.profile.two-factor.confirm') ]]" class="admin-two-factor-modal__form">
                        #csrf

                        <div class="admin-field">
                            <label for="two-factor-manual-secret">Manual setup key</label>
                            <input id="two-factor-manual-secret" type="text" value="[[ $pendingTwoFactorSetup['secret'] ]]" readonly>
                        </div>

                        <div class="admin-field">
                            <label for="two-factor-code">Authentication code</label>
                            <input id="two-factor-code" name="two_factor_code" type="text" value="[[ old('two_factor_code') ]]" inputmode="numeric" autocomplete="one-time-code" placeholder="123456">
                            <p class="admin-field__hint">After scanning the QR code, enter the 6-digit code from your authenticator app.</p>
                            #error('two_factor_code')
                                <p class="admin-field__error">[[ $message ]]</p>
                            #enderror
                        </div>

                        <div class="admin-two-factor-modal__actions">
                            <button class="admin-button" type="submit">Verify and enable</button>
                            <button class="admin-button admin-button--ghost" type="submit" form="two-factor-cancel-form">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    #endif
#endsection
