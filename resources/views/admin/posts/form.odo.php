#extends('layouts.admin')
#section('title')
    [[ $formMode === 'create' ? 'Create Post' : 'Edit Post' ]]
#endsection
#section('page_title')
    [[ $formMode === 'create' ? 'Create post' : 'Edit post' ]]
#endsection
#section('page_description')
    Manage the article body, publishing state, category, and tags.
#endsection
#section('page_actions')
    <a class="admin-button admin-button--ghost" href="[[ route('admin.posts.index') ]]">Back to posts</a>
#endsection
#section('content')
    #php $formInput = session('input') ?? []; #endphp
    <section class="admin-form-shell">
        <form
            class="admin-form-grid admin-form-grid--post"
            method="POST"
            action="[[ $formMode === 'create' ? route('admin.posts.store') : route('admin.posts.update', ['post' => $post->slug]) ]]"
            data-slug-form
        >
            #csrf
            #if ($formMode === 'edit')
                #method('PUT')
            #endif

            <div class="admin-form-main">
                <div class="admin-panel">
                    <div class="admin-panel__head">
                        <div>
                            <p class="admin-section__eyebrow">Story details</p>
                            <h3 class="admin-panel__title">Writing surface</h3>
                        </div>
                    </div>

                    <div class="admin-form-grid__fields">
                        <div class="admin-field">
                            <label for="title">Title</label>
                            <input id="title" name="title" type="text" value="[[ $formInput['title'] ?? $post?->title ?? '' ]]" data-slug-source>
                            #error('title')
                                <p class="admin-field__error">[[ $message ]]</p>
                            #enderror
                        </div>

                        <div class="admin-field">
                            <label for="slug">Slug</label>
                            <input id="slug" name="slug" type="text" value="[[ $formInput['slug'] ?? $post?->slug ?? '' ]]" data-slug-target>
                            #error('slug')
                                <p class="admin-field__error">[[ $message ]]</p>
                            #enderror
                        </div>

                        <div class="admin-field admin-field--full">
                            <label for="excerpt">Excerpt</label>
                            <textarea id="excerpt" name="excerpt" rows="4">[[ $formInput['excerpt'] ?? $post?->excerpt ?? '' ]]</textarea>
                            #error('excerpt')
                                <p class="admin-field__error">[[ $message ]]</p>
                            #enderror
                        </div>

                        <div class="admin-field admin-field--full">
                            <label for="body">Body</label>
                            <textarea id="body" name="body" rows="16">[[ $formInput['body'] ?? $post?->body ?? '' ]]</textarea>
                            #error('body')
                                <p class="admin-field__error">[[ $message ]]</p>
                            #enderror
                        </div>
                    </div>
                </div>

                <div class="admin-panel">
                    <div class="admin-panel__head">
                        <div>
                            <p class="admin-section__eyebrow">Search metadata</p>
                            <h3 class="admin-panel__title">SEO details</h3>
                        </div>
                    </div>

                    <div class="admin-form-grid__fields">
                        <div class="admin-field">
                            <label for="seo_title">SEO title</label>
                            <input id="seo_title" name="seo_title" type="text" value="[[ $formInput['seo_title'] ?? $post?->seo_title ?? '' ]]">
                        </div>

                        <div class="admin-field">
                            <label for="seo_description">SEO description</label>
                            <textarea id="seo_description" name="seo_description" rows="4">[[ $formInput['seo_description'] ?? $post?->seo_description ?? '' ]]</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <aside class="admin-form-sidebar">
                <div class="admin-panel">
                    <div class="admin-panel__head">
                        <div>
                            <p class="admin-section__eyebrow">Publishing</p>
                            <h3 class="admin-panel__title">Visibility</h3>
                        </div>
                    </div>

                    <div class="admin-form-grid__fields">
                        <div class="admin-field">
                            <label for="status">Status</label>
                            <select id="status" name="status">
                                <option value="draft" [[ ($formInput['status'] ?? $post?->status ?? 'draft') === 'draft' ? 'selected' : '' ]]>Draft</option>
                                <option value="published" [[ ($formInput['status'] ?? $post?->status ?? 'draft') === 'published' ? 'selected' : '' ]]>Published</option>
                                <option value="archived" [[ ($formInput['status'] ?? $post?->status ?? 'draft') === 'archived' ? 'selected' : '' ]]>Archived</option>
                            </select>
                        </div>

                        <div class="admin-field">
                            <label for="published_at">Publish at</label>
                            <input id="published_at" name="published_at" type="text" value="[[ $formInput['published_at'] ?? $post?->published_at ?? '' ]]" placeholder="2026-05-20 10:00:00">
                        </div>

                        <div class="admin-field">
                            <label for="is_featured">Featured</label>
                            <select id="is_featured" name="is_featured">
                                <option value="0" [[ ($formInput['is_featured'] ?? (($post?->is_featured ?? false) ? '1' : '0')) === '0' ? 'selected' : '' ]]>No</option>
                                <option value="1" [[ ($formInput['is_featured'] ?? (($post?->is_featured ?? false) ? '1' : '0')) === '1' ? 'selected' : '' ]]>Yes</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="admin-panel">
                    <div class="admin-panel__head">
                        <div>
                            <p class="admin-section__eyebrow">Taxonomy</p>
                            <h3 class="admin-panel__title">Placement</h3>
                        </div>
                    </div>

                    <div class="admin-form-grid__fields">
                        <div class="admin-field">
                            <label for="category_id">Category</label>
                            <select id="category_id" name="category_id">
                                #foreach ($categories as $category)
                                    <option value="[[ $category->id ]]" [[ (string) ($formInput['category_id'] ?? $post?->category_id ?? '') === (string) $category->id ? 'selected' : '' ]]>[[ $category->name ]]</option>
                                #endforeach
                            </select>
                            #error('category_id')
                                <p class="admin-field__error">[[ $message ]]</p>
                            #enderror
                        </div>

                        <div class="admin-field">
                            <label for="tag_names_input">Tags</label>
                            #php
                                $tagValue = $formInput['tag_names'] ?? implode(', ', $selectedTagNames ?? []);
                            #endphp
                            <div
                                class="admin-tagify"
                                data-tagify
                                data-tagify-source="post-tag-options"
                            >
                                <div class="admin-tagify__shell" data-tagify-shell>
                                    <div class="admin-tagify__chips" data-tagify-chips></div>
                                    <input
                                        id="tag_names_input"
                                        class="admin-tagify__input"
                                        type="text"
                                        placeholder="Type a tag and press Enter"
                                        autocomplete="off"
                                        data-tagify-input
                                    >
                                </div>
                                <input type="hidden" name="tag_names" value="[[ $tagValue ]]" data-tagify-hidden>
                                <div class="admin-tagify__menu" data-tagify-menu></div>
                            </div>
                            <script id="post-tag-options" type="application/json">[[! json_encode($tagOptions) !]]</script>
                            <p class="admin-field__hint">Choose existing tags or type a new one to create it automatically.</p>
                        </div>
                    </div>
                </div>

                <div class="admin-panel">
                    <div class="admin-panel__head">
                        <div>
                            <p class="admin-section__eyebrow">Presentation</p>
                            <h3 class="admin-panel__title">Meta fields</h3>
                        </div>
                    </div>

                    <div class="admin-form-grid__fields">
                        <div class="admin-field">
                            <label for="author_name">Author label</label>
                            <input id="author_name" name="author_name" type="text" value="[[ $formInput['author_name'] ?? $post?->author_name ?? 'Editorial Team' ]]">
                        </div>

                        <div class="admin-field">
                            <label for="cover_image">Cover image URL</label>
                            <input id="cover_image" name="cover_image" type="text" value="[[ $formInput['cover_image'] ?? $post?->cover_image ?? '' ]]">
                        </div>

                        <div class="admin-field">
                            <label for="view_count">View count</label>
                            <input id="view_count" name="view_count" type="number" min="0" value="[[ $formInput['view_count'] ?? $post?->view_count ?? 0 ]]">
                        </div>
                    </div>
                </div>

                <div class="admin-panel admin-panel--actions">
                    <button class="admin-button" type="submit">[[ $formMode === 'create' ? 'Create post' : 'Save changes' ]]</button>
                    <a class="admin-text-link" href="[[ route('admin.posts.index') ]]">Cancel</a>
                </div>
            </aside>
        </form>
    </section>
#endsection
