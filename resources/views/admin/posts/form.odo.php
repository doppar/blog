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
    <button class="admin-button admin-button--secondary admin-button--with-icon" type="button" data-ai-drawer-open>
        <svg viewBox="0 0 24 24" aria-hidden="true">
            <path d="M12 2a7 7 0 0 1 7 7c0 2.38-1.19 4.47-3 5.74V17a2 2 0 0 1-2 2H10a2 2 0 0 1-2-2v-2.26C6.19 13.47 5 11.38 5 9a7 7 0 0 1 7-7z" fill="none" stroke="currentColor" stroke-width="2"/>
            <path d="M9 21h6" fill="none" stroke="currentColor" stroke-width="2"/>
        </svg>
        AI assistant
    </button>
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
                            <div class="admin-rich-editor" data-rich-editor>
                                <div class="admin-rich-editor__toolbar" data-rich-editor-toolbar>
                                    <div class="admin-rich-editor__toolbar-group">
                                        <span class="admin-u-sr-only">Format</span>
                                        <button class="admin-rich-editor__tool admin-rich-editor__tool--icon admin-rich-editor__tool--glyph" type="button" data-editor-action="paragraph" aria-label="Paragraph" title="Paragraph">
                                            ¶
                                        </button>
                                        <button class="admin-rich-editor__tool admin-rich-editor__tool--icon admin-rich-editor__tool--badge" type="button" data-editor-action="heading" data-editor-level="2" aria-label="Heading 2" title="Heading 2">
                                            H2
                                        </button>
                                        <button class="admin-rich-editor__tool admin-rich-editor__tool--icon admin-rich-editor__tool--badge" type="button" data-editor-action="heading" data-editor-level="3" aria-label="Heading 3" title="Heading 3">
                                            H3
                                        </button>
                                    </div>

                                    <div class="admin-rich-editor__toolbar-group">
                                        <span class="admin-u-sr-only">Style</span>
                                        <button class="admin-rich-editor__tool admin-rich-editor__tool--icon admin-rich-editor__tool--glyph" type="button" data-editor-action="bold" aria-label="Bold" title="Bold">
                                            B
                                        </button>
                                        <button class="admin-rich-editor__tool admin-rich-editor__tool--icon admin-rich-editor__tool--glyph admin-rich-editor__tool--italic" type="button" data-editor-action="italic" aria-label="Italic" title="Italic">
                                            I
                                        </button>
                                        <button class="admin-rich-editor__tool admin-rich-editor__tool--icon admin-rich-editor__tool--glyph admin-rich-editor__tool--underline" type="button" data-editor-action="underline" aria-label="Underline" title="Underline">
                                            U
                                        </button>
                                        <button class="admin-rich-editor__tool admin-rich-editor__tool--icon admin-rich-editor__tool--glyph admin-rich-editor__tool--strike" type="button" data-editor-action="strike" aria-label="Strike" title="Strike">
                                            S
                                        </button>
                                        <button class="admin-rich-editor__tool admin-rich-editor__tool--icon" type="button" data-editor-action="link" aria-label="Link" title="Link">
                                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                                <path d="M10.5 13.5 13.5 10.5"></path>
                                                <path d="M8.25 15.75 6.5 17.5a3 3 0 0 1-4.25-4.25L6 9.5a3 3 0 0 1 4.25 0"></path>
                                                <path d="m15.75 8.25 1.75-1.75a3 3 0 1 1 4.25 4.25L18 14.5a3 3 0 0 1-4.25 0"></path>
                                            </svg>
                                        </button>
                                    </div>

                                    <div class="admin-rich-editor__toolbar-group">
                                        <span class="admin-u-sr-only">Blocks</span>
                                        <button class="admin-rich-editor__tool admin-rich-editor__tool--icon" type="button" data-editor-action="bulletList" aria-label="Bulleted list" title="Bulleted list">
                                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                                <circle cx="5" cy="7" r="1.4"></circle>
                                                <circle cx="5" cy="12" r="1.4"></circle>
                                                <circle cx="5" cy="17" r="1.4"></circle>
                                                <path d="M9 7h10"></path>
                                                <path d="M9 12h10"></path>
                                                <path d="M9 17h10"></path>
                                            </svg>
                                        </button>
                                        <button class="admin-rich-editor__tool admin-rich-editor__tool--icon" type="button" data-editor-action="orderedList" aria-label="Numbered list" title="Numbered list">
                                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                                <path d="M4 7h2"></path>
                                                <path d="M4 12h2"></path>
                                                <path d="M4 17h2"></path>
                                                <path d="M9 7h10"></path>
                                                <path d="M9 12h10"></path>
                                                <path d="M9 17h10"></path>
                                            </svg>
                                        </button>
                                        <button class="admin-rich-editor__tool admin-rich-editor__tool--icon" type="button" data-editor-action="blockquote" aria-label="Quote" title="Quote">
                                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                                <path d="M8 9H5.5A1.5 1.5 0 0 0 4 10.5V13a3 3 0 0 0 3 3h1"></path>
                                                <path d="M18 9h-2.5A1.5 1.5 0 0 0 14 10.5V13a3 3 0 0 0 3 3h1"></path>
                                            </svg>
                                        </button>
                                        <button class="admin-rich-editor__tool admin-rich-editor__tool--icon" type="button" data-editor-action="horizontalRule" aria-label="Divider" title="Divider">
                                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                                <path d="M4 12h16"></path>
                                            </svg>
                                        </button>
                                    </div>

                                    <div class="admin-rich-editor__toolbar-group">
                                        <span class="admin-u-sr-only">Media</span>
                                        <button class="admin-rich-editor__tool admin-rich-editor__tool--icon" type="button" data-editor-action="image" aria-label="Image" title="Image">
                                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                                <rect x="4" y="5" width="16" height="14" rx="2"></rect>
                                                <circle cx="9" cy="10" r="1.5"></circle>
                                                <path d="m20 16-4.5-4.5L8 19"></path>
                                            </svg>
                                        </button>
                                    </div>

                                    <div class="admin-rich-editor__toolbar-group">
                                        <span class="admin-u-sr-only">Code</span>
                                        <button class="admin-rich-editor__tool admin-rich-editor__tool--icon admin-rich-editor__tool--code" type="button" data-editor-action="codeBlock" aria-label="Code block" title="Code block">
                                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                                <path d="m8 8-4 4 4 4"></path>
                                                <path d="m16 8 4 4-4 4"></path>
                                                <path d="m13 6-2 12"></path>
                                            </svg>
                                        </button>
                                        <div class="admin-rich-editor__language-wrap" data-editor-code-language-wrap>
                                            <select class="admin-rich-editor__select" data-editor-code-language>
                                                <option value="">Plain text</option>
                                                <option value="javascript">JavaScript</option>
                                                <option value="typescript">TypeScript</option>
                                                <option value="php">PHP</option>
                                                <option value="html">HTML</option>
                                                <option value="css">CSS</option>
                                                <option value="json">JSON</option>
                                                <option value="bash">Bash</option>
                                                <option value="markdown">Markdown</option>
                                                <option value="sql">SQL</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="admin-rich-editor__toolbar-group admin-rich-editor__toolbar-group--end">
                                        <span class="admin-u-sr-only">History</span>
                                        <button class="admin-rich-editor__tool admin-rich-editor__tool--icon" type="button" data-editor-action="undo" aria-label="Undo" title="Undo">
                                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                                <path d="M9 7 5 11l4 4"></path>
                                                <path d="M5 11h8a6 6 0 1 1 0 12"></path>
                                            </svg>
                                        </button>
                                        <button class="admin-rich-editor__tool admin-rich-editor__tool--icon" type="button" data-editor-action="redo" aria-label="Redo" title="Redo">
                                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                                <path d="m15 7 4 4-4 4"></path>
                                                <path d="M19 11h-8a6 6 0 1 0 0 12"></path>
                                            </svg>
                                        </button>
                                    </div>

                                    <div class="admin-rich-editor__toolbar-meta">
                                        <span><strong>/</strong> quick insert</span>
                                    </div>
                                </div>

                                <div class="admin-rich-editor__popover admin-rich-editor__popover--link is-hidden" data-editor-link-popover>
                                    <div class="admin-rich-editor__popover-head">
                                        <div class="admin-rich-editor__popover-title">
                                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                                <path d="M10.5 13.5 13.5 10.5"></path>
                                                <path d="M8.25 15.75 6.5 17.5a3 3 0 0 1-4.25-4.25L6 9.5a3 3 0 0 1 4.25 0"></path>
                                                <path d="m15.75 8.25 1.75-1.75a3 3 0 1 1 4.25 4.25L18 14.5a3 3 0 0 1-4.25 0"></path>
                                            </svg>
                                            <strong>Insert link</strong>
                                        </div>
                                        <button class="admin-rich-editor__popover-close" type="button" data-editor-link-close aria-label="Close link options">
                                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M18 6 6 18M6 6l12 12"/></svg>
                                        </button>
                                    </div>
                                    <label class="admin-rich-editor__popover-label" for="editor-link-url">URL</label>
                                    <input id="editor-link-url" class="admin-rich-editor__popover-input" type="url" placeholder="https://example.com" data-editor-link-input>
                                    <div class="admin-rich-editor__popover-actions">
                                        <button class="admin-button admin-button--compact" type="button" data-editor-link-apply>Apply link</button>
                                        <button class="admin-button admin-button--ghost admin-button--compact" type="button" data-editor-link-remove>Remove link</button>
                                    </div>
                                </div>

                                <div class="admin-rich-editor__popover admin-rich-editor__popover--image is-hidden" data-editor-image-popover>
                                    <div class="admin-rich-editor__popover-head">
                                        <div class="admin-rich-editor__popover-title">
                                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                                <rect x="4" y="5" width="16" height="14" rx="2"></rect>
                                                <circle cx="9" cy="10" r="1.5"></circle>
                                                <path d="m20 16-4.5-4.5L8 19"></path>
                                            </svg>
                                            <strong>Image settings</strong>
                                        </div>
                                    </div>
                                    <label class="admin-rich-editor__popover-label" for="editor-image-caption">Caption</label>
                                    <input id="editor-image-caption" class="admin-rich-editor__popover-input" type="text" placeholder="Add a short caption" data-editor-image-caption>
                                    <div class="admin-rich-editor__image-align" data-editor-image-align>
                                        <button class="admin-rich-editor__tool" type="button" data-editor-image-align-value="left">Left</button>
                                        <button class="admin-rich-editor__tool" type="button" data-editor-image-align-value="center">Center</button>
                                        <button class="admin-rich-editor__tool" type="button" data-editor-image-align-value="wide">Wide</button>
                                    </div>
                                    <div class="admin-rich-editor__popover-actions">
                                        <button class="admin-button admin-button--compact" type="button" data-editor-image-replace>Replace image</button>
                                        <button class="admin-button admin-button--ghost admin-button--compact" type="button" data-editor-image-remove>Remove image</button>
                                    </div>
                                </div>

                                <div class="admin-rich-editor__canvas" data-rich-editor-canvas></div>

                                <div class="admin-rich-editor__slash-menu is-hidden" data-editor-slash-menu></div>

                                <textarea
                                    id="body"
                                    name="body"
                                    rows="16"
                                    hidden
                                    data-rich-editor-source
                                >[[ $formInput['body'] ?? $post?->body ?? '' ]]</textarea>
                            </div>
                            <p class="admin-field__hint">Write long-form content with structured blocks, pull quotes, and code snippets.</p>
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
                            <textarea id="seo_description" class="admin-field__textarea--compact" name="seo_description" rows="1">[[ $formInput['seo_description'] ?? $post?->seo_description ?? '' ]]</textarea>
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

    <aside class="admin-ai-drawer" data-ai-drawer aria-hidden="true">
        <div class="admin-ai-drawer__backdrop" data-ai-drawer-close></div>
        <div class="admin-ai-drawer__dialog">
            <div class="admin-ai-drawer__head">
                <div>
                    <p class="admin-section__eyebrow">AI assistant</p>
                    <h3 class="admin-ai-drawer__title">Writing assistant</h3>
                </div>
                <button class="admin-ai-drawer__close" type="button" data-ai-drawer-close aria-label="Close AI assistant">×</button>
            </div>

            <div class="admin-ai-drawer__body">
                <div class="admin-field">
                    <label for="ai_target">Generate for</label>
                    <select id="ai_target" data-ai-target>
                        <option value="title">Title</option>
                        <option value="excerpt">Excerpt</option>
                        <option value="body">Body</option>
                        <option value="seo_title">SEO title</option>
                        <option value="seo_description">SEO description</option>
                        <option value="tags">Tags</option>
                    </select>
                </div>

                <div class="admin-field">
                    <label for="ai_provider">Provider</label>
                    <select id="ai_provider" data-ai-provider>
                        <option value="openai">OpenAI</option>
                        <option value="gemini">Google Gemini</option>
                        <option value="claude">Anthropic Claude</option>
                        <option value="openrouter" selected>OpenRouter</option>
                        <option value="selfhost">Self-hosted</option>
                    </select>
                    <p class="admin-field__hint">Set the matching API key in your .env file.</p>
                </div>

                <div class="admin-field">
                    <label for="ai_model">Model</label>
                    <input id="ai_model" type="text" value="openrouter/free" data-ai-model>
                </div>

                <div class="admin-field admin-field--full">
                    <label for="ai_instructions">Instructions</label>
                    <textarea id="ai_instructions" rows="4" placeholder="Tone, audience, key points, keywords…" data-ai-instructions></textarea>
                </div>

                <div class="admin-field">
                    <label for="ai_temperature">Creativity</label>
                    <input id="ai_temperature" type="range" min="0" max="20" value="7" data-ai-temperature>
                    <p class="admin-field__hint" data-ai-temperature-label>0.7</p>
                </div>

                <div class="admin-field">
                    <label for="ai_max_tokens">Max tokens</label>
                    <input id="ai_max_tokens" type="number" min="50" max="8000" value="1200" data-ai-max-tokens>
                </div>

                <div class="admin-field admin-field--full is-hidden" data-ai-output-field>
                    <label for="ai_output">Result</label>
                    <textarea id="ai_output" rows="8" data-ai-output></textarea>
                </div>

                <div class="admin-ai-actions" data-ai-actions>
                    <button class="admin-button admin-button--secondary" type="button" data-ai-generate>
                        <span data-ai-button-label>Generate</span>
                    </button>
                    <button class="admin-button is-hidden" type="button" data-ai-apply>Apply to field</button>
                </div>

                <input type="hidden" data-ai-generate-url value="[[ route('admin.ai.generate') ]]">
            </div>
        </div>
    </aside>

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
                        <div>
                            <strong data-cover-media-title>Upload cover media</strong>
                            <span data-cover-media-subtitle>Fast image publishing for this post</span>
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
