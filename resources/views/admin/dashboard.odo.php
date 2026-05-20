#extends('layouts.admin')
#section('title')
    Dashboard
#endsection
#section('page_title')
    Dashboard
#endsection
#section('page_description')
    Monitor the total content inventory for posts, categories, and tags.
#endsection
#section('page_actions')
    <a class="admin-button admin-button--ghost" href="[[ route('admin.categories.create') ]]">Create Category</a>
    <a class="admin-button" href="[[ route('admin.posts.create') ]]">Create Post</a>
#endsection
#section('content')
    <section class="admin-stat-grid admin-stat-grid--three">
        <article class="admin-stat-card">
            <div class="admin-stat-card__top">
                <p class="admin-stat-card__label">Total Posts</p>
            </div>

            <div class="admin-stat-card__body">
                <span class="admin-stat-card__icon">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <rect x="4" y="11" width="4" height="8" rx="1"></rect>
                        <rect x="10" y="7" width="4" height="12" rx="1"></rect>
                        <rect x="16" y="4" width="4" height="15" rx="1"></rect>
                    </svg>
                </span>

                <div>
                    <p class="admin-stat-card__value">[[ $totalPosts ]]</p>
                    <p class="admin-stat-card__meta">Published and draft entries combined.</p>
                </div>
            </div>
        </article>

        <article class="admin-stat-card">
            <div class="admin-stat-card__top">
                <p class="admin-stat-card__label">Total Categories</p>
            </div>

            <div class="admin-stat-card__body">
                <span class="admin-stat-card__icon">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M5 7h14"></path>
                        <path d="M7 7V5.5A1.5 1.5 0 0 1 8.5 4h7A1.5 1.5 0 0 1 17 5.5V7"></path>
                        <rect x="4" y="7" width="16" height="12" rx="2"></rect>
                    </svg>
                </span>

                <div>
                    <p class="admin-stat-card__value">[[ $totalCategories ]]</p>
                    <p class="admin-stat-card__meta">All editorial content groups.</p>
                </div>
            </div>
        </article>

        <article class="admin-stat-card">
            <div class="admin-stat-card__top">
                <p class="admin-stat-card__label">Total Tags</p>
            </div>

            <div class="admin-stat-card__body">
                <span class="admin-stat-card__icon">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="m7 7 4-4h6l4 4v6l-8 8-8-8V7z"></path>
                        <circle cx="15.5" cy="8.5" r="1.2"></circle>
                    </svg>
                </span>

                <div>
                    <p class="admin-stat-card__value">[[ $totalTags ]]</p>
                    <p class="admin-stat-card__meta">Reusable labels across the CMS.</p>
                </div>
            </div>
        </article>
    </section>
#endsection
