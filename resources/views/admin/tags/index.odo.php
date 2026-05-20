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
#endsection
#section('content')
    <form class="admin-users-toolbar" method="GET" action="[[ route('admin.tags.index') ]]">
        <div class="admin-users-toolbar__search-group">
            <div class="admin-users-toolbar__search">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <circle cx="11" cy="11" r="6.5"></circle>
                    <path d="M16 16l4.5 4.5"></path>
                </svg>
                <input id="search" name="search" type="search" value="[[ $search ]]" placeholder="Search name or slug" aria-label="Search tags">
            </div>
            <button class="admin-button admin-button--ghost admin-button--with-icon" type="submit">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <circle cx="11" cy="11" r="6.5"></circle>
                    <path d="M16 16l4.5 4.5"></path>
                </svg>
                <span>Search</span>
            </button>
            #if ($search)
                <a class="admin-text-link" href="[[ route('admin.tags.index') ]]">Reset</a>
            #endif
        </div>

        <a class="admin-button" href="[[ route('admin.tags.create') ]]">Create Tag</a>
    </form>

    <section class="admin-panel">
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
