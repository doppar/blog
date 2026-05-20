#extends('layouts.admin')
#section('title')
    [[ $formMode === 'create' ? 'Create User' : 'Edit User' ]]
#endsection
#section('page_title')
    [[ $formMode === 'create' ? 'Create user' : 'Edit user' ]]
#endsection
#section('page_description')
    Manage account identity, access role, profile image, and status.
#endsection
#section('page_actions')
    <a class="admin-button admin-button--ghost" href="[[ route('admin.users.index') ]]">Back to users</a>
#endsection
#section('content')
    <section class="admin-form-shell">
        <form
            class="admin-form-grid"
            method="POST"
            action="[[ $formMode === 'create' ? route('admin.users.store') : route('admin.users.update', ['user' => $user->id]) ]]"
            enctype="multipart/form-data"
        >
            #csrf

            <div class="admin-panel">
                <div class="admin-panel__head">
                    <div>
                        <p class="admin-section__eyebrow">User details</p>
                        <h3 class="admin-panel__title">Core information</h3>
                    </div>
                </div>

                <div class="admin-form-grid__fields">
                    <div class="admin-field">
                        <label for="name">Name</label>
                        <input id="name" name="name" type="text" value="[[ $formInput['name'] ?? $user?->name ?? '' ]]">
                        #error('name')
                            <p class="admin-field__error">[[ $message ]]</p>
                        #enderror
                    </div>

                    <div class="admin-field">
                        <label for="email">Email</label>
                        <input id="email" name="email" type="email" value="[[ $formInput['email'] ?? $user?->email ?? '' ]]">
                        #error('email')
                            <p class="admin-field__error">[[ $message ]]</p>
                        #enderror
                    </div>

                    <div class="admin-field admin-field--full">
                        <div class="admin-field__label-row">
                            <label for="image_file">Profile image</label>
                        </div>
                        <div class="admin-cover-preview [[ ($formInput['image'] ?? $user?->image ?? '') === '' ? 'is-hidden' : '' ]]" data-user-image-preview>
                            <img src="[[ $formInput['image'] ?? $user?->image ?? '' ]]" alt="User image preview" data-user-image-preview-image>
                        </div>
                        <input id="image_file" name="image_file" class="admin-field__file-input" type="file" accept="image/*" data-user-image-input>
                        <p class="admin-field__hint">Upload a JPG, PNG, WEBP, GIF, AVIF, or SVG profile image for this user.</p>
                        #error('image_file')
                            <p class="admin-field__error">[[ $message ]]</p>
                        #enderror
                    </div>

                    <div class="admin-field">
                        <label for="password">[[ $formMode === 'create' ? 'Password' : 'New password' ]]</label>
                        <input id="password" name="password" type="password" value="">
                        <p class="admin-field__hint">[[ $formMode === 'create' ? 'Use at least 8 characters for a secure login.' : 'Leave blank to keep the current password.' ]]</p>
                        #error('password')
                            <p class="admin-field__error">[[ $message ]]</p>
                        #enderror
                    </div>

                    <div class="admin-field">
                        <label for="password_confirmation">Confirm password</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" value="">
                        <p class="admin-field__hint">[[ $formMode === 'create' ? 'Repeat the password to confirm the new account.' : 'Repeat the new password to confirm the update.' ]]</p>
                    </div>
                </div>
            </div>

            <aside class="admin-form-sidebar">
                <div class="admin-panel">
                    <div class="admin-panel__head">
                        <div>
                            <p class="admin-section__eyebrow">Access</p>
                            <h3 class="admin-panel__title">Permissions</h3>
                        </div>
                    </div>

                    <div class="admin-form-grid__fields">
                        <div class="admin-field">
                            <label for="role">Role</label>
                            <select id="role" name="role">
                                #foreach ($roleOptions as $roleValue => $roleLabel)
                                    <option value="[[ $roleValue ]]" [[ ($formInput['role'] ?? $user?->role ?? 'editor') === $roleValue ? 'selected' : '' ]]>[[ $roleLabel ]]</option>
                                #endforeach
                            </select>
                            #error('role')
                                <p class="admin-field__error">[[ $message ]]</p>
                            #enderror
                        </div>

                        <div class="admin-field">
                            <label for="status">Status</label>
                            <select id="status" name="status">
                                <option value="1" [[ ($formInput['status'] ?? (($user?->status ?? true) ? '1' : '0')) === '1' ? 'selected' : '' ]]>Active</option>
                                <option value="0" [[ ($formInput['status'] ?? (($user?->status ?? true) ? '1' : '0')) === '0' ? 'selected' : '' ]]>Inactive</option>
                            </select>
                            #error('status')
                                <p class="admin-field__error">[[ $message ]]</p>
                            #enderror
                        </div>
                    </div>
                </div>

                <div class="admin-panel admin-panel--actions">
                    <button class="admin-button" type="submit">[[ $formMode === 'create' ? 'Create user' : 'Save changes' ]]</button>
                    <a class="admin-text-link" href="[[ route('admin.users.index') ]]">Cancel</a>
                </div>
            </aside>
        </form>
    </section>
#endsection
