#extends('layouts.admin')
#section('title')
    Posts
#endsection
#section('page_title')
    Posts
#endsection
#section('page_description')
    Search, filter, and maintain the full article inventory.
#endsection
#section('page_actions')
    <a class="admin-button" href="[[ route('admin.posts.create') ]]">Create Post</a>
#endsection
#section('content')
    <section class="admin-stat-grid">
        <article class="admin-stat-card">
            <div class="admin-stat-card__top">
                <p class="admin-stat-card__label">Current Posts</p>
                <div class="admin-stat-card__range">
                    <span>All Time</span>
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
                    <p class="admin-stat-card__meta">[[ $draftPosts ]] drafts</p>
                </div>
            </div>
        </article>

        <article class="admin-stat-card">
            <div class="admin-stat-card__top">
                <p class="admin-stat-card__label">Published Posts</p>
                <p class="admin-stat-card__meta">[[ $featuredPosts ]] featured</p>
            </div>

            <p class="admin-stat-card__value admin-stat-card__value--progress">[[ $publishedRatio ]]%</p>
            <div class="admin-stat-card__progress">
                <span style="width:[[ $publishedRatio ]]%;"></span>
            </div>
        </article>
    </section>

    <section class="admin-panel">
        <form class="admin-filter-bar" method="GET" action="[[ route('admin.posts.index') ]]">
            <div class="admin-field">
                <label for="search">Search</label>
                <input id="search" name="search" type="search" value="[[ $filters['search'] ]]" placeholder="Search">
            </div>

            <div class="admin-field">
                <label for="status">Status</label>
                <select id="status" name="status">
                    <option value="">All status</option>
                    <option value="draft" [[ $filters['status'] === 'draft' ? 'selected' : '' ]]>Draft</option>
                    <option value="published" [[ $filters['status'] === 'published' ? 'selected' : '' ]]>Published</option>
                    <option value="archived" [[ $filters['status'] === 'archived' ? 'selected' : '' ]]>Archived</option>
                </select>
            </div>

            <div class="admin-field">
                <label for="category_id">Category</label>
                <select id="category_id" name="category_id">
                    <option value="">All categories</option>
                    #foreach ($categories as $category)
                        <option value="[[ $category->id ]]" [[ $filters['category_id'] === (string) $category->id ? 'selected' : '' ]]>[[ $category->name ]]</option>
                    #endforeach
                </select>
            </div>

            <div class="admin-filter-bar__actions">
                <button class="admin-button admin-button--ghost" type="submit">Filter</button>
                <a class="admin-text-link" href="[[ route('admin.posts.index') ]]">Reset</a>
            </div>
        </form>

        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Featured</th>
                        <th>Views</th>
                        <th>Updated</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    #foreach ($posts['data'] as $post)
                        <tr>
                            <td>[[ $post->id ]]</td>
                            <td>
                                <a class="admin-table__title" href="[[ route('admin.posts.edit', ['post' => $post->slug]) ]]">[[ $post->title ]]</a>
                                <p class="admin-table__meta">[[ $post->author_name ]] • [[ $post->tags_count ?? 0 ]] tags</p>
                            </td>
                            <td>[[ $post->category?->name ?? 'Uncategorized' ]]</td>
                            <td>
                                <span class="admin-badge admin-badge--[[ $post->status ]]">[[ ucfirst($post->status) ]]</span>
                            </td>
                            <td>[[ $post->is_featured ? 'Yes' : 'No' ]]</td>
                            <td>[[ $post->view_count ]]</td>
                            <td>[[ date('M d, Y', strtotime($post->updated_at ?? 'now')) ]]</td>
                            <td class="admin-table__actions">
                                <a class="admin-inline-link" href="[[ route('admin.posts.edit', ['post' => $post->slug]) ]]">Edit</a>
                                <form method="POST" action="[[ route('admin.posts.destroy', ['post' => $post->slug]) ]]">
                                    #csrf
                                    #method('DELETE')
                                    <button class="admin-inline-danger" type="submit">Delete</button>
                                </form>
                            </td>
                        </tr>
                    #endforeach
                </tbody>
            </table>
        </div>

        <div class="admin-pagination">
            [[! paginator($posts)->links() !]]
        </div>
    </section>
#endsection
