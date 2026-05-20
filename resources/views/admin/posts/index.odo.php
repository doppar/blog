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

    <form class="admin-users-toolbar" method="GET" action="[[ route('admin.posts.index') ]]">
        <div class="admin-users-toolbar__search">
            <svg viewBox="0 0 24 24" aria-hidden="true">
                <circle cx="11" cy="11" r="6.5"></circle>
                <path d="M16 16l4.5 4.5"></path>
            </svg>
            <input id="search" name="search" type="search" value="[[ $filters['search'] ]]" placeholder="Search posts by title or author" aria-label="Search posts">
        </div>

        <div class="admin-users-toolbar__actions">
            <div class="admin-users-toolbar__filters">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M4 6h16l-6 7v4l-4 2v-6Z"></path>
                </svg>
                <select id="status" name="status" aria-label="Filter by status">
                    <option value="">All statuses</option>
                    <option value="draft" [[ $filters['status'] === 'draft' ? 'selected' : '' ]]>Draft</option>
                    <option value="published" [[ $filters['status'] === 'published' ? 'selected' : '' ]]>Published</option>
                    <option value="archived" [[ $filters['status'] === 'archived' ? 'selected' : '' ]]>Archived</option>
                </select>
                <span class="admin-users-toolbar__sep"></span>
                <select id="category_id" name="category_id" aria-label="Filter by category">
                    <option value="">All categories</option>
                    #foreach ($categories as $category)
                        <option value="[[ $category->id ]]" [[ $filters['category_id'] === (string) $category->id ? 'selected' : '' ]]>[[ $category->name ]]</option>
                    #endforeach
                </select>
            </div>
            <button class="admin-button admin-button--ghost admin-button--with-icon" type="submit">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M4 6h16l-6 7v4l-4 2v-6Z"></path>
                </svg>
                <span>Filter</span>
            </button>
            <a class="admin-text-link" href="[[ route('admin.posts.index') ]]">Reset</a>
        </div>
    </form>

    <section class="admin-panel">
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
                            <td>
                                <span class="admin-status-indicator [[ $post->is_featured ? 'is-active' : 'is-inactive' ]]">
                                    #if ($post->is_featured)
                                        <svg viewBox="0 0 24 24" aria-hidden="true">
                                            <circle cx="12" cy="12" r="8.5"></circle>
                                            <path d="m8.8 12.2 2.2 2.2 4.4-4.6"></path>
                                        </svg>
                                    #else
                                        <svg viewBox="0 0 24 24" aria-hidden="true">
                                            <circle cx="12" cy="12" r="8.5"></circle>
                                            <path d="m9.2 9.2 5.6 5.6"></path>
                                            <path d="m14.8 9.2-5.6 5.6"></path>
                                        </svg>
                                    #endif
                                </span>
                            </td>
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
