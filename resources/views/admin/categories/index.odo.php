#extends('layouts.admin')
#section('title')
    Categories
#endsection
#section('page_title')
    Categories
#endsection
#section('page_description')
    Organize the main blog sections and their visibility.
#endsection
#section('page_actions')
    <a class="admin-button" href="[[ route('admin.categories.create') ]]">Create Category</a>
#endsection
#section('content')
    <form class="admin-users-toolbar" method="GET" action="[[ route('admin.categories.index') ]]">
        <div class="admin-users-toolbar__search">
            <svg viewBox="0 0 24 24" aria-hidden="true">
                <circle cx="11" cy="11" r="6.5"></circle>
                <path d="M16 16l4.5 4.5"></path>
            </svg>
            <input id="search" name="q" type="search" value="[[ request()->q ]]" placeholder="Search name or slug" aria-label="Search categories">
        </div>

        <div class="admin-users-toolbar__actions">
            <div class="admin-users-toolbar__filters">
                <select id="status" name="status" aria-label="Filter by status">
                    <option value="">All statuses</option>
                    <option value="active" [[ request()->status === 'active' ? 'selected' : '' ]]>Active</option>
                    <option value="inactive" [[ request()->status === 'inactive' ? 'selected' : '' ]]>Inactive</option>
                </select>
            </div>
            <button class="admin-button admin-button--ghost admin-button--with-icon" type="submit">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M4 6h16l-6 7v4l-4 2v-6Z"></path>
                </svg>
                <span>Filter</span>
            </button>
            <a class="admin-text-link" href="[[ route('admin.categories.index') ]]">Reset</a>
        </div>
    </form>

    <section class="admin-panel">
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Status</th>
                        <th>Posts</th>
                        <th>Updated</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    #foreach ($categories['data'] as $category)
                        <tr>
                            <td>
                                <div class="admin-table__title-row">
                                    <span class="admin-color-dot" style="background:[[ $category->accent_color ]];"></span>
                                    <div>
                                        <a class="admin-table__title" href="[[ route('admin.categories.edit', ['category' => $category->slug]) ]]">[[ $category->name ]]</a>
                                        <p class="admin-table__meta">[[ $category->slug ]]</p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="admin-status-indicator [[ $category->status ? 'is-active' : 'is-inactive' ]]">
                                    #if ($category->status)
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
                                    <span>[[ $category->status ? 'Active' : 'Inactive' ]]</span>
                                </span>
                            </td>
                            <td>[[ $category->posts_count ?? 0 ]]</td>
                            <td>[[ date('M d, Y', strtotime($category->updated_at ?? 'now')) ]]</td>
                            <td class="admin-table__actions">
                                <a class="admin-inline-link" href="[[ route('admin.categories.edit', ['category' => $category->slug]) ]]">Edit</a>

                                <form method="POST" action="[[ route('admin.categories.destroy', ['category' => $category->slug]) ]]">
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
            [[! paginator($categories)->links() !]]
        </div>
    </section>
#endsection
