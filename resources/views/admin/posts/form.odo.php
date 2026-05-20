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

                    <div class="admin-form-grid__fields admin-form-grid__fields--stacked">
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
                                <input
                                    type="hidden"
                                    name="tag_names"
                                    value="[[ array_key_exists('tag_names', $formInput) ? $formInput['tag_names'] : ($tagFieldValue ?? '') ]]"
                                    data-tagify-hidden
                                >
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

                    <div class="admin-form-grid__fields admin-form-grid__fields--stacked">
                        <div class="admin-field admin-field--full">
                            <div class="admin-field__label-row">
                                <label for="cover_image">Cover image URL</label>
                                <button class="admin-link-button admin-inline-link" type="button" data-cover-media-open>Upload Media</button>
                            </div>
                            <input id="cover_image" name="cover_image" type="text" value="[[ $formInput['cover_image'] ?? $post?->cover_image ?? '' ]]" data-cover-image-input>
                            <div class="admin-cover-preview [[ ($formInput['cover_image'] ?? $post?->cover_image ?? '') === '' ? 'is-hidden' : '' ]]" data-cover-preview>
                                <img src="[[ $formInput['cover_image'] ?? $post?->cover_image ?? '' ]]" alt="Cover image preview" data-cover-preview-image>
                            </div>
                            <p class="admin-field__hint">Open the quick media modal to upload an image and place its public URL here instantly.</p>
                        </div>

                        <div class="admin-field">
                            <label for="author_name">Author label</label>
                            <select id="author_name" name="author_name">
                                #foreach ($authorOptions as $authorOption)
                                    <option value="[[ $authorOption ]]" [[ ($formInput['author_name'] ?? $post?->author_name ?? 'Editorial Team') === $authorOption ? 'selected' : '' ]]>[[ $authorOption ]]</option>
                                #endforeach
                            </select>
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

    <aside
        class="admin-cover-modal"
        data-cover-media-modal
        data-upload-url="[[ route('admin.media.upload') ]]"
        aria-hidden="true"
    >
        <div class="admin-cover-modal__backdrop" data-cover-media-close></div>

        <div class="admin-cover-modal__dialog">
            <div class="admin-cover-modal__head">
                <div class="admin-cover-modal__identity">
                    <span class="admin-cover-modal__avatar">ED</span>
                    <div>
                        <strong>Upload cover media</strong>
                        <span>Fast image publishing for this post</span>
                    </div>
                </div>

                <button class="admin-cover-modal__close" type="button" data-cover-media-close aria-label="Close cover media modal">×</button>
            </div>

            <div class="admin-cover-modal__body">
                <div class="admin-cover-modal__composer">
                    <div class="admin-cover-modal__dropzone" data-cover-media-dropzone tabindex="0">
                        <div class="admin-cover-modal__dropzone-icon">
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M12 5v10"></path>
                                <path d="m8.5 8.5 3.5-3.5 3.5 3.5"></path>
                                <rect x="4" y="16" width="16" height="3.5" rx="1.75"></rect>
                            </svg>
                        </div>

                        <div>
                            <strong>Drag images here or browse from your device</strong>
                            <p>Upload directly from the post editor and use the public image URL immediately.</p>
                        </div>

                        <div class="admin-cover-modal__dropzone-actions">
                            <button class="admin-button" type="button" data-cover-media-browse>Choose image</button>
                            <span>JPG, PNG, WEBP, GIF, AVIF, SVG · up to 8 MB</span>
                        </div>

                        <input type="file" accept="image/*" hidden data-cover-media-input>
                    </div>

                    <div class="admin-cover-modal__preview is-hidden" data-cover-media-uploaded>
                        <div class="admin-cover-modal__preview-image">
                            <img src="" alt="Uploaded cover image" data-cover-media-uploaded-image>
                        </div>
                        <div class="admin-cover-modal__preview-copy">
                            <strong data-cover-media-uploaded-title>Ready to use</strong>
                            <p>The uploaded file is available now. Use it as the post cover image or copy the URL for later.</p>
                            <div class="admin-field">
                                <label for="cover-media-uploaded-url">Public URL</label>
                                <input id="cover-media-uploaded-url" type="text" value="" readonly data-cover-media-uploaded-url>
                            </div>
                            <div class="admin-cover-modal__preview-actions">
                                <button class="admin-button" type="button" data-cover-media-use-uploaded>Use this image</button>
                                <button class="admin-button admin-button--ghost" type="button" data-cover-media-copy-uploaded>Copy URL</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="admin-cover-modal__library">
                    <div class="admin-cover-modal__library-head">
                        <h3>Recent media</h3>
                        <a class="admin-inline-link" href="[[ route('admin.media.index') ]]" target="_blank" rel="noreferrer">Open library</a>
                    </div>

                    <div class="admin-cover-modal__library-grid" data-cover-media-library>
                        #foreach ($coverMediaItems as $mediaItem)
                            <article
                                class="admin-cover-modal__media-card"
                                data-cover-media-item
                                data-media='[[! htmlspecialchars(json_encode($mediaItem), ENT_QUOTES, "UTF-8") !]]'
                            >
                                <button class="admin-cover-modal__media-preview" type="button" data-cover-media-select>
                                    <img src="[[ $mediaItem['thumbnail_url'] ]]" alt="[[ $mediaItem['alt_text'] ]]">
                                </button>

                                <div class="admin-cover-modal__media-meta">
                                    <strong>[[ $mediaItem['title'] ]]</strong>
                                    <span>[[ $mediaItem['dimensions_label'] ]] · [[ $mediaItem['size_label'] ]]</span>
                                </div>

                                <div class="admin-cover-modal__media-actions">
                                    <button class="admin-button admin-button--ghost admin-button--compact" type="button" data-cover-media-copy-existing>Copy URL</button>
                                    <button class="admin-button admin-button--compact" type="button" data-cover-media-use-existing>Use image</button>
                                </div>
                            </article>
                        #endforeach
                    </div>
                </div>
            </div>
        </div>
    </aside>
#endsection
