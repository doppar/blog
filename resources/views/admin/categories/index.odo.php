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
    <section class="admin-panel">
        <form class="admin-filter-bar" method="GET" action="[[ route('admin.categories.index') ]]">
            <div class="admin-field">
                <label for="search">Search</label>
                <input id="search" name="search" type="search" value="[[ $filters['search'] ]]" placeholder="Search name or slug">
            </div>

            <div class="admin-field">
                <label for="status">Status</label>
                <select id="status" name="status">
                    <option value="">All statuses</option>
                    <option value="1" [[ $filters['status'] === '1' ? 'selected' : '' ]]>Active</option>
                    <option value="0" [[ $filters['status'] === '0' ? 'selected' : '' ]]>Inactive</option>
                </select>
            </div>

            <div class="admin-filter-bar__actions">
                <button class="admin-button admin-button--ghost" type="submit">Filter</button>
                <a class="admin-text-link" href="[[ route('admin.categories.index') ]]">Reset</a>
            </div>
        </form>

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
                                <span class="admin-badge [[ $category->status ? 'admin-badge--published' : 'admin-badge--archived' ]]">
                                    [[ $category->status ? 'Active' : 'Inactive' ]]
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
