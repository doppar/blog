#extends('layouts.app')
#section('title')
    Category Manager
#endsection
#section('body_class')
    category-page
#endsection
#section('head')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
#endsection
#section('content')
<main
    id="category-grid"
    class="category-shell"
    data-table-url="[[ route('categories.data') ]]"
    data-store-url="[[ route('categories.store') ]]"
    data-edit-url-template="[[ route('categories.edit', ['category' => '__CATEGORY_ID__']) ]]"
    data-update-url-template="[[ route('categories.update', ['category' => '__CATEGORY_ID__']) ]]"
    data-delete-url-template="[[ route('categories.destroy', ['category' => '__CATEGORY_ID__']) ]]"
>
    <section class="category-frame">
        <div class="category-frame__head">
            <div>
                <p class="category-eyebrow">Doppar Attribute Routes</p>
                <h1 class="category-title">Category Grid</h1>
                <p class="category-subtitle">Server-side CRUD powered by jQuery DataTables and Doppar offset queries.</p>
            </div>

            <a class="category-home-link" href="[[ route('home') ]]">Home</a>
        </div>

        <div class="category-panel">
            <div class="category-toolbar">
                <div class="category-toolbar__cluster">
                    <button class="category-icon-button" type="button" id="category-refresh" aria-label="Refresh categories">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M20 12a8 8 0 1 1-2.34-5.66M20 4v6h-6" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>

                    <button class="category-icon-button" type="button" id="category-edit" aria-label="Edit selected category" disabled>
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M4 20h4l10-10a2.12 2.12 0 1 0-3-3L5 17v3Z" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>

                    <button class="category-icon-button category-icon-button--danger" type="button" id="category-delete" aria-label="Delete selected category" disabled>
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M3 6h18M8 6V4h8v2m-1 0v14a2 2 0 0 1-2 2H11a2 2 0 0 1-2-2V6h6Z" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                </div>

                <div class="category-toolbar__meta">
                    <p class="category-toolbar__count" id="category-table-summary">Showing 0-0 of 0</p>
                    <div class="category-toolbar__selection" id="category-selection-pill">No row selected</div>
                </div>

                <div class="category-toolbar__actions">
                    <label class="category-search">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M11 19a8 8 0 1 1 5.3-2l4.2 4.2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <input id="category-search-input" type="search" placeholder="Search categories" autocomplete="off">
                    </label>

                    <button class="category-icon-button category-icon-button--accent" type="button" id="category-create" aria-label="Create category">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="category-toast" id="category-toast" hidden></div>

            <div class="category-table-wrap">
                <table id="categories-table" class="display category-table" aria-describedby="category-table-summary">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Status</th>
                            <th>Name</th>
                            <th>Excerpt</th>
                            <th>Updated</th>
                            <th>Created At</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </section>

    <div class="category-modal" id="category-modal" hidden>
        <div class="category-modal__backdrop" data-close-modal></div>

        <div class="category-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="category-modal-title">
            <div class="category-modal__head">
                <div>
                    <p class="category-eyebrow">Category CRUD</p>
                    <h2 class="category-modal__title" id="category-modal-title">Create category</h2>
                </div>

                <button class="category-icon-button" type="button" data-close-modal aria-label="Close category form">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M6 6l12 12M18 6 6 18" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"/>
                    </svg>
                </button>
            </div>

            <form id="category-form" class="category-form">
                <input type="hidden" name="category_id" id="category-id">

                <div class="category-form__errors" id="category-form-errors" hidden></div>

                <label class="category-field">
                    <span>Name</span>
                    <input id="category-name" name="name" type="text" maxlength="255" placeholder="Category name">
                </label>

                <label class="category-field">
                    <span>Status</span>
                    <select id="category-status" name="status">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </label>

                <label class="category-field">
                    <span>Excerpt</span>
                    <textarea id="category-excerpt" name="excerpt" rows="5" maxlength="1000" placeholder="Short category description"></textarea>
                </label>

                <div class="category-form__actions">
                    <button class="category-secondary-button" type="button" data-close-modal>Cancel</button>
                    <button class="category-primary-button" type="submit" id="category-submit-button">Save category</button>
                </div>
            </form>
        </div>
    </div>
</main>
#endsection
#section('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
#endsection
