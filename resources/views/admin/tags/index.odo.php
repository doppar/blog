#extends('layouts.admin')
#section('title')
    Tags
#endsection
#section('page_title')
    Tags
#endsection
#section('page_description')
    Maintain reusable labels for filtering and discovery.
#endsection
#section('page_actions')
    <a class="admin-button" href="[[ route('admin.tags.create') ]]">Create Tag</a>
#endsection
#section('content')
    <section class="admin-panel">
        <form class="admin-filter-bar" method="GET" action="[[ route('admin.tags.index') ]]">
            <div class="admin-field">
                <label for="search">Search</label>
                <input id="search" name="search" type="search" value="[[ $search ]]" placeholder="Search name or slug">
            </div>

            <div class="admin-filter-bar__actions">
                <button class="admin-button admin-button--ghost" type="submit">Filter</button>
                <a class="admin-text-link" href="[[ route('admin.tags.index') ]]">Reset</a>
            </div>
        </form>

        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Tag</th>
                        <th>Posts</th>
                        <th>Updated</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    #foreach ($tags['data'] as $tag)
                        <tr>
                            <td>
                                <div class="admin-table__title-row">
                                    <span class="admin-color-dot" style="background:[[ $tag->color ]];"></span>
                                    <div>
                                        <a class="admin-table__title" href="[[ route('admin.tags.edit', ['tag' => $tag->slug]) ]]">[[ $tag->name ]]</a>
                                        <p class="admin-table__meta">[[ $tag->slug ]]</p>
                                    </div>
                                </div>
                            </td>
                            <td>[[ $tag->posts_count ?? 0 ]]</td>
                            <td>[[ date('M d, Y', strtotime($tag->updated_at ?? 'now')) ]]</td>
                            <td class="admin-table__actions">
                                <a class="admin-inline-link" href="[[ route('admin.tags.edit', ['tag' => $tag->slug]) ]]">Edit</a>

                                <form method="POST" action="[[ route('admin.tags.destroy', ['tag' => $tag->slug]) ]]">
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
            [[! paginator($tags)->links() !]]
        </div>
    </section>
#endsection
