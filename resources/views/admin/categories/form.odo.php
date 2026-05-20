#extends('layouts.admin')
#section('title')
    [[ $formMode === 'create' ? 'Create Category' : 'Edit Category' ]]
#endsection
#section('page_title')
    [[ $formMode === 'create' ? 'Create category' : 'Edit category' ]]
#endsection
#section('page_description')
    Set the section name, slug, color, and status.
#endsection
#section('page_actions')
    <a class="admin-button admin-button--ghost" href="[[ route('admin.categories.index') ]]">Back to categories</a>
#endsection
#section('content')
    #php $formInput = session('input') ?? []; #endphp
    <section class="admin-form-shell">
        <form
            class="admin-form-grid"
            method="POST"
            action="[[ $formMode === 'create' ? route('admin.categories.store') : route('admin.categories.update', ['category' => $category->slug]) ]]"
            data-slug-form
        >
            #csrf
            #if ($formMode === 'edit')
                #method('PUT')
            #endif

            <div class="admin-panel">
                <div class="admin-panel__head">
                    <div>
                        <p class="admin-section__eyebrow">Category details</p>
                        <h3 class="admin-panel__title">Core information</h3>
                    </div>
                </div>

                <div class="admin-form-grid__fields">
                    <div class="admin-field">
                        <label for="name">Name</label>
                        <input id="name" name="name" type="text" value="[[ $formInput['name'] ?? $category?->name ?? '' ]]" data-slug-source>
                        #error('name')
                            <p class="admin-field__error">[[ $message ]]</p>
                        #enderror
                    </div>

                    <div class="admin-field">
                        <label for="slug">Slug</label>
                        <input id="slug" name="slug" type="text" value="[[ $formInput['slug'] ?? $category?->slug ?? '' ]]" data-slug-target>
                        #error('slug')
                            <p class="admin-field__error">[[ $message ]]</p>
                        #enderror
                    </div>

                    <div class="admin-field admin-field--full">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" rows="6">[[ $formInput['description'] ?? $category?->description ?? '' ]]</textarea>
                        #error('description')
                            <p class="admin-field__error">[[ $message ]]</p>
                        #enderror
                    </div>
                </div>
            </div>

            <aside class="admin-form-sidebar">
                <div class="admin-panel">
                    <div class="admin-panel__head">
                        <div>
                            <p class="admin-section__eyebrow">Presentation</p>
                            <h3 class="admin-panel__title">Sidebar settings</h3>
                        </div>
                    </div>

                    <div class="admin-form-grid__fields">
                        <div class="admin-field">
                            <label for="accent_color">Accent color</label>
                            <input id="accent_color" name="accent_color" type="text" value="[[ $formInput['accent_color'] ?? $category?->accent_color ?? '#6f7bf7' ]]" placeholder="#6f7bf7">
                        </div>

                        <div class="admin-field">
                            <label for="status">Status</label>
                            <select id="status" name="status">
                                <option value="1" [[ ($formInput['status'] ?? (($category?->status ?? true) ? '1' : '0')) === '1' ? 'selected' : '' ]]>Active</option>
                                <option value="0" [[ ($formInput['status'] ?? (($category?->status ?? true) ? '1' : '0')) === '0' ? 'selected' : '' ]]>Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="admin-panel admin-panel--actions">
                    <button class="admin-button" type="submit">[[ $formMode === 'create' ? 'Create category' : 'Save changes' ]]</button>
                    <a class="admin-text-link" href="[[ route('admin.categories.index') ]]">Cancel</a>
                </div>
            </aside>
        </form>
    </section>
#endsection
