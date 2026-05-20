#extends('layouts.admin')
#section('title')
    [[ $formMode === 'create' ? 'Create Tag' : 'Edit Tag' ]]
#endsection
#section('page_title')
    [[ $formMode === 'create' ? 'Create tag' : 'Edit tag' ]]
#endsection
#section('page_description')
    Set the label name, slug, description, and color.
#endsection
#section('page_actions')
    <a class="admin-button admin-button--ghost" href="[[ route('admin.tags.index') ]]">Back to tags</a>
#endsection
#section('content')
    <section class="admin-form-shell">
        <form
            class="admin-form-grid"
            method="POST"
            action="[[ $formMode === 'create' ? route('admin.tags.store') : route('admin.tags.update', ['tag' => $tag->slug]) ]]"
            data-slug-form
        >
            #csrf
            #if ($formMode === 'edit')
                #method('PUT')
            #endif

            <div class="admin-panel">
                <div class="admin-panel__head">
                    <div>
                        <p class="admin-section__eyebrow">Tag details</p>
                        <h3 class="admin-panel__title">Core information</h3>
                    </div>
                </div>

                <div class="admin-form-grid__fields">
                    <div class="admin-field">
                        <label for="name">Name</label>
                        <input id="name" name="name" type="text" value="[[ $formInput['name'] ?? $tag?->name ?? '' ]]" data-slug-source>
                        #error('name')
                            <p class="admin-field__error">[[ $message ]]</p>
                        #enderror
                    </div>

                    <div class="admin-field">
                        <label for="slug">Slug</label>
                        <input id="slug" name="slug" type="text" value="[[ $formInput['slug'] ?? $tag?->slug ?? '' ]]" data-slug-target>
                        #error('slug')
                            <p class="admin-field__error">[[ $message ]]</p>
                        #enderror
                    </div>

                    <div class="admin-field admin-field--full">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" rows="6">[[ $formInput['description'] ?? $tag?->description ?? '' ]]</textarea>
                    </div>
                </div>
            </div>

            <aside class="admin-form-sidebar">
                <div class="admin-panel">
                    <div class="admin-panel__head">
                        <div>
                            <p class="admin-section__eyebrow">Presentation</p>
                            <h3 class="admin-panel__title">Visual token</h3>
                        </div>
                    </div>

                    <div class="admin-form-grid__fields">
                        <div class="admin-field">
                            <label for="color">Color</label>
                            <input id="color" name="color" type="text" value="[[ $formInput['color'] ?? $tag?->color ?? '#8fa2ff' ]]" placeholder="#8fa2ff">
                        </div>
                    </div>
                </div>

                <div class="admin-panel admin-panel--actions">
                    <button class="admin-button" type="submit">[[ $formMode === 'create' ? 'Create tag' : 'Save changes' ]]</button>
                    <a class="admin-text-link" href="[[ route('admin.tags.index') ]]">Cancel</a>
                </div>
            </aside>
        </form>
    </section>
#endsection
