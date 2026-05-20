#extends('layouts.admin')
#section('title')
    Media Library
#endsection
#section('page_title')
    Media library
#endsection
#section('page_description')
    Upload, organize, and reuse editorial image assets across the CMS.
#endsection
#section('page_actions')
    <button class="admin-button" type="button" data-media-open-upload>Upload Images</button>
#endsection
#section('content')
    <section class="admin-stat-grid admin-stat-grid--three">
        <article class="admin-stat-card">
            <div class="admin-stat-card__top">
                <p class="admin-stat-card__label">Total Assets</p>
                <p class="admin-stat-card__meta">Image library</p>
            </div>

            <div class="admin-stat-card__body">
                <span class="admin-stat-card__icon">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <rect x="3.5" y="5" width="17" height="14" rx="2.5"></rect>
                        <circle cx="9" cy="10" r="1.5"></circle>
                        <path d="m20.5 15-4.2-4.2a1.4 1.4 0 0 0-2 0L8 17"></path>
                    </svg>
                </span>

                <div>
                    <p class="admin-stat-card__value" data-media-count-total>[[ $counts['total_items'] ]]</p>
                    <p class="admin-stat-card__meta">Ready for posts, pages, and highlights.</p>
                </div>
            </div>
        </article>

        <article class="admin-stat-card">
            <div class="admin-stat-card__top">
                <p class="admin-stat-card__label">Uploaded This Month</p>
                <p class="admin-stat-card__meta">Fresh additions</p>
            </div>

            <div class="admin-stat-card__body">
                <span class="admin-stat-card__icon">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M12 4v10"></path>
                        <path d="m8.5 10.5 3.5 3.5 3.5-3.5"></path>
                        <rect x="4" y="17" width="16" height="3" rx="1.5"></rect>
                    </svg>
                </span>

                <div>
                    <p class="admin-stat-card__value" data-media-count-month>[[ $counts['uploaded_this_month'] ]]</p>
                    <p class="admin-stat-card__meta">New uploads during the current month.</p>
                </div>
            </div>
        </article>

        <article class="admin-stat-card">
            <div class="admin-stat-card__top">
                <p class="admin-stat-card__label">Library Size</p>
                <p class="admin-stat-card__meta">Public storage</p>
            </div>

            <div class="admin-stat-card__body">
                <span class="admin-stat-card__icon">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M6 7.5V6a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v1.5"></path>
                        <rect x="4" y="7.5" width="16" height="11.5" rx="2"></rect>
                        <path d="M8 12h8"></path>
                    </svg>
                </span>

                <div>
                    <p class="admin-stat-card__value" data-media-count-storage>[[ $counts['storage_label'] ]]</p>
                    <p class="admin-stat-card__meta">Optimized image payload currently in the library.</p>
                </div>
            </div>
        </article>
    </section>

    <form class="admin-users-toolbar" method="GET" action="[[ route('admin.media.index') ]]">
        <div class="admin-users-toolbar__search">
            <svg viewBox="0 0 24 24" aria-hidden="true">
                <circle cx="11" cy="11" r="6.5"></circle>
                <path d="M16 16l4.5 4.5"></path>
            </svg>
            <input id="search" name="search" type="search" value="[[ $filters['search'] ]]" placeholder="Search media title or file name" aria-label="Search media">
        </div>

        <div class="admin-users-toolbar__actions">
            <div class="admin-users-toolbar__filters">
                <select id="month" name="month" aria-label="Filter by month">
                    <option value="">All upload dates</option>
                    #foreach ($monthOptions as $value => $label)
                        <option value="[[ $value ]]" [[ $filters['month'] === $value ? 'selected' : '' ]]>[[ $label ]]</option>
                    #endforeach
                </select>
            </div>
            <button class="admin-button admin-button--ghost admin-button--with-icon" type="submit">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M4 6h16l-6 7v4l-4 2v-6Z"></path>
                </svg>
                <span>Filter</span>
            </button>
            <a class="admin-text-link" href="[[ route('admin.media.index') ]]">Reset</a>
        </div>
    </form>

    <section
        class="admin-panel admin-media-library"
        data-media-library
        data-upload-url="[[ route('admin.media.upload') ]]"
        data-initial-focus="[[ $filters['focus'] ]]"
    >
        <div class="admin-panel__head admin-media-library__head">
            <div>
                <p class="admin-section__eyebrow">Asset management</p>
                <h3 class="admin-panel__title">Editorial image library</h3>
            </div>

            <div class="admin-media-library__head-actions">
                <div class="admin-segmented" data-media-view-toggle>
                    <button class="admin-segmented__button is-active" type="button" data-media-view="grid">Grid</button>
                    <button class="admin-segmented__button" type="button" data-media-view="list">List</button>
                </div>

                <button class="admin-button admin-button--ghost" type="button" data-media-open-upload>Choose Files</button>
            </div>
        </div>

        <div class="admin-media-upload" id="media-upload-panel" data-media-dropzone tabindex="0">
            <div class="admin-media-upload__icon">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M12 5v10"></path>
                    <path d="m8.5 8.5 3.5-3.5 3.5 3.5"></path>
                    <rect x="4" y="16" width="16" height="3.5" rx="1.75"></rect>
                </svg>
            </div>

            <div class="admin-media-upload__copy">
                <strong>Drop images here or browse from your device</strong>
                <p>Upload multiple JPG, PNG, WEBP, GIF, AVIF, or SVG files at once. Each image is stored on the public disk and becomes immediately reusable across the CMS.</p>
            </div>

            <div class="admin-media-upload__actions">
                <button class="admin-button" type="button" data-media-open-upload>Select Images</button>
                <span>Maximum file size: 8 MB per image</span>
            </div>

            <input type="file" name="files[]" accept="image/*" multiple hidden data-media-input>
        </div>

        <div class="admin-media-library__summary">
            <p><strong data-media-visible-count>[[ count($media['data']) ]]</strong> media items in this view</p>
            <span>Instant upload, preview, copy URL, and removal workflows.</span>
        </div>

        <div class="admin-media-empty [[ count($media['data']) > 0 ? 'is-hidden' : '' ]]" data-media-empty>
            <div class="admin-media-empty__icon">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <rect x="3.5" y="5" width="17" height="14" rx="2.5"></rect>
                    <circle cx="9" cy="10" r="1.5"></circle>
                    <path d="m20.5 15-4.2-4.2a1.4 1.4 0 0 0-2 0L8 17"></path>
                </svg>
            </div>
            <strong>No media items match this view yet.</strong>
            <p>Upload the first image to start building the editorial asset library.</p>
        </div>

        <div class="admin-media-collection" data-media-collection>
            <section class="admin-media-view is-active" data-media-view-panel="grid">
                <div class="admin-media-grid" data-media-grid>
                    #foreach ($media['data'] as $item)
                        <article
                            class="admin-media-card"
                            data-media-item
                            data-media-id="[[ $item['id'] ]]"
                            data-media='[[! htmlspecialchars(json_encode($item), ENT_QUOTES, "UTF-8") !]]'
                        >
                            <button class="admin-media-card__preview" type="button" data-media-open-details>
                                <img src="[[ $item['thumbnail_url'] ]]" alt="[[ $item['alt_text'] !== '' ? $item['alt_text'] : $item['title'] ]]">
                            </button>

                            <div class="admin-media-card__content">
                                <div>
                                    <p class="admin-media-card__title">[[ $item['title'] ]]</p>
                                    <p class="admin-media-card__meta">[[ $item['extension'] ]] · [[ $item['dimensions_label'] ]]</p>
                                </div>

                                <div class="admin-media-card__actions">
                                    <button class="admin-icon-button" type="button" data-media-copy-url aria-label="Copy media URL">
                                        <svg viewBox="0 0 24 24" aria-hidden="true">
                                            <rect x="9" y="9" width="10" height="10" rx="2"></rect>
                                            <rect x="5" y="5" width="10" height="10" rx="2"></rect>
                                        </svg>
                                    </button>
                                    <button class="admin-icon-button" type="button" data-media-delete aria-label="Delete media">
                                        <svg viewBox="0 0 24 24" aria-hidden="true">
                                            <path d="M5 7h14"></path>
                                            <path d="M9 7V5h6v2"></path>
                                            <rect x="7" y="7" width="10" height="12" rx="2"></rect>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </article>
                    #endforeach
                </div>
            </section>

            <section class="admin-media-view" data-media-view-panel="list">
                <div class="admin-table-wrap">
                    <table class="admin-table admin-media-table">
                        <thead>
                            <tr>
                                <th>Preview</th>
                                <th>File</th>
                                <th>Type</th>
                                <th>Size</th>
                                <th>Dimensions</th>
                                <th>Uploaded</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody data-media-list-body>
                            #foreach ($media['data'] as $item)
                                <tr
                                    data-media-item
                                    data-media-id="[[ $item['id'] ]]"
                                    data-media='[[! htmlspecialchars(json_encode($item), ENT_QUOTES, "UTF-8") !]]'
                                >
                                    <td>
                                        <button class="admin-media-table__thumb" type="button" data-media-open-details>
                                            <img src="[[ $item['thumbnail_url'] ]]" alt="[[ $item['alt_text'] !== '' ? $item['alt_text'] : $item['title'] ]]">
                                        </button>
                                    </td>
                                    <td>
                                        <a class="admin-table__title" href="[[ $item['url'] ]]" target="_blank" rel="noreferrer">[[ $item['title'] ]]</a>
                                        <p class="admin-table__meta">[[ $item['original_name'] ]]</p>
                                    </td>
                                    <td>[[ $item['mime_type'] ]]</td>
                                    <td>[[ $item['size_label'] ]]</td>
                                    <td>[[ $item['dimensions_label'] ]]</td>
                                    <td>
                                        <div>[[ $item['uploaded_by'] ]]</div>
                                        <p class="admin-table__meta">[[ $item['created_relative'] ]]</p>
                                    </td>
                                    <td class="admin-table__actions">
                                        <button class="admin-inline-link" type="button" data-media-copy-url>Copy URL</button>
                                        <button class="admin-inline-danger" type="button" data-media-delete>Delete</button>
                                    </td>
                                </tr>
                            #endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <div class="admin-pagination">
            [[! paginator($media)->links() !]]
        </div>
    </section>

    <aside class="admin-media-drawer" data-media-drawer aria-hidden="true">
        <div class="admin-media-drawer__backdrop" data-media-drawer-close></div>

        <div class="admin-media-drawer__panel">
            <div class="admin-media-drawer__head">
                <div>
                    <p class="admin-section__eyebrow">Media details</p>
                    <h3 class="admin-panel__title" data-media-drawer-title>Preview</h3>
                </div>

                <button class="admin-media-drawer__close" type="button" data-media-drawer-close aria-label="Close media details">×</button>
            </div>

            <div class="admin-media-drawer__preview">
                <img src="" alt="" data-media-drawer-image>
            </div>

            <div class="admin-media-drawer__content">
                <dl class="admin-media-drawer__meta">
                    <div>
                        <dt>Original file</dt>
                        <dd data-media-drawer-original-name></dd>
                    </div>
                    <div>
                        <dt>Format</dt>
                        <dd data-media-drawer-type></dd>
                    </div>
                    <div>
                        <dt>Dimensions</dt>
                        <dd data-media-drawer-dimensions></dd>
                    </div>
                    <div>
                        <dt>File size</dt>
                        <dd data-media-drawer-size></dd>
                    </div>
                    <div>
                        <dt>Uploaded by</dt>
                        <dd data-media-drawer-author></dd>
                    </div>
                    <div>
                        <dt>Added</dt>
                        <dd data-media-drawer-created></dd>
                    </div>
                </dl>

                <div class="admin-field">
                    <label for="media-library-url">Public URL</label>
                    <input id="media-library-url" type="text" value="" readonly data-media-drawer-url>
                </div>

                <div class="admin-media-drawer__actions">
                    <button class="admin-button admin-button--ghost" type="button" data-media-drawer-copy>Copy URL</button>
                    <button class="admin-button admin-button--danger" type="button" data-media-drawer-delete>Delete Image</button>
                </div>
            </div>
        </div>
    </aside>
#endsection
