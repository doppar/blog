#extends('layouts.admin')
#section('title')
    Dashboard
#endsection
#section('page_title')
    Dashboard
#endsection
#section('page_description')
    Monitor posts, categories, tags, and recent editorial activity.
#endsection
#section('page_actions')
    <a class="admin-button admin-button--ghost" href="[[ route('admin.categories.create') ]]">Create Category</a>
    <a class="admin-button" href="[[ route('admin.posts.create') ]]">Create Post</a>
#endsection
#section('content')
    <section class="admin-stat-grid">
        <article class="admin-stat-card">
            <div class="admin-stat-card__top">
                <p class="admin-stat-card__label">Current Posts</p>
                <div class="admin-stat-card__range">
                    <span>30 Days</span>
                    <svg viewBox="0 0 20 20" aria-hidden="true">
                        <path d="M5 7.5 10 12.5l5-5"></path>
                    </svg>
                </div>
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
                    <p class="admin-stat-card__meta">[[ $draftPosts ]] drafts waiting review</p>
                </div>
            </div>
        </article>

        <article class="admin-stat-card">
            <div class="admin-stat-card__top">
                <p class="admin-stat-card__label">Published Share</p>
                <p class="admin-stat-card__meta">[[ $newThisMonth ]] this month</p>
            </div>

            <p class="admin-stat-card__value admin-stat-card__value--progress">[[ $publishedRatio ]]%</p>
            <div class="admin-stat-card__progress">
                <span style="width:[[ $publishedRatio ]]%;"></span>
            </div>
        </article>
    </section>

    <section class="admin-grid admin-grid--two" id="insights">
        <article class="admin-panel">
            <div class="admin-panel__head">
                <h3 class="admin-panel__title">Recent Posts</h3>
                <a class="admin-inline-link" href="[[ route('admin.posts.index') ]]">View all</a>
            </div>

            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th>Updated</th>
                        </tr>
                    </thead>
                    <tbody>
                        #foreach ($recentPosts as $post)
                            <tr>
                                <td>
                                    <a class="admin-table__title" href="[[ route('admin.posts.edit', ['post' => $post->slug]) ]]">[[ $post->title ]]</a>
                                    <p class="admin-table__meta">[[ $post->author_name ]]</p>
                                </td>
                                <td>[[ $post->category?->name ?? 'Uncategorized' ]]</td>
                                <td>
                                    <span class="admin-badge admin-badge--[[ $post->status ]]">[[ ucfirst($post->status) ]]</span>
                                </td>
                                <td>[[ date('M d, Y', strtotime($post->updated_at ?? 'now')) ]]</td>
                            </tr>
                        #endforeach
                    </tbody>
                </table>
            </div>
        </article>

        <article class="admin-panel">
            <div class="admin-panel__head">
                <h3 class="admin-panel__title">Category Summary</h3>
                <a class="admin-inline-link" href="[[ route('admin.categories.index') ]]">Manage</a>
            </div>

            <div class="admin-stack-list">
                #foreach ($categorySummary as $category)
                    <div class="admin-stack-item">
                        <div class="admin-stack-item__title">
                            <span class="admin-color-dot" style="background:[[ $category->accent_color ]];"></span>
                            <div>
                                <strong>[[ $category->name ]]</strong>
                                <p>[[ $category->slug ]]</p>
                            </div>
                        </div>
                        <span class="admin-stack-item__count">[[ $category->posts_count ?? 0 ]] posts</span>
                    </div>
                #endforeach
            </div>
        </article>
    </section>

    <section class="admin-grid admin-grid--two">
        <article class="admin-panel">
            <div class="admin-panel__head">
                <h3 class="admin-panel__title">Content Overview</h3>
            </div>

            <div class="admin-copy-list">
                <div>
                    <strong>Published Posts</strong>
                    <p>[[ $publishedPosts ]] posts are currently marked published.</p>
                </div>
                <div>
                    <strong>Featured Stories</strong>
                    <p>[[ $featuredPosts ]] posts are marked as featured for future homepage placement.</p>
                </div>
                <div>
                    <strong>Taxonomy</strong>
                    <p>[[ $totalCategories ]] categories and [[ $totalTags ]] tags are available for content organization.</p>
                </div>
            </div>
        </article>

        <article class="admin-panel">
            <div class="admin-panel__head">
                <h3 class="admin-panel__title">Top Tags</h3>
                <a class="admin-inline-link" href="[[ route('admin.tags.index') ]]">Browse</a>
            </div>

            <div class="admin-tag-cloud">
                #foreach ($tagSummary as $tag)
                    <a class="admin-tag-chip" href="[[ route('admin.tags.edit', ['tag' => $tag->slug]) ]]" style="--chip-color:[[ $tag->color ]];">
                        [[ $tag->name ]]
                        <span>[[ $tag->posts_count ?? 0 ]]</span>
                    </a>
                #endforeach
            </div>
        </article>
    </section>
#endsection
