#extends('layouts.admin')
#section('title')
    Users
#endsection
#section('page_title')
    Users
#endsection
#section('page_description')
    Manage editor access, profile images, roles, and account status.
#endsection
#section('page_actions')
    <a class="admin-button" href="[[ route('admin.users.create') ]]">Create User</a>
#endsection
#section('content')
    <section class="admin-stat-grid admin-stat-grid--three">
        <article class="admin-stat-card">
            <div class="admin-stat-card__top">
                <p class="admin-stat-card__label">Total Users</p>
                <p class="admin-stat-card__meta">[[ $activeUsers ]] active</p>
            </div>

            <div class="admin-stat-card__body">
                <span class="admin-stat-card__icon">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <circle cx="9" cy="8" r="3"></circle>
                        <circle cx="17" cy="10" r="2.5"></circle>
                        <path d="M4 19c0-3 2.5-5 5.5-5S15 16 15 19"></path>
                        <path d="M14.5 18c.4-1.8 2-3.2 4-3.2 1.5 0 2.9.8 3.5 2.2"></path>
                    </svg>
                </span>

                <div>
                    <p class="admin-stat-card__value">[[ $totalUsers ]]</p>
                    <p class="admin-stat-card__meta">All admin accounts in the CMS.</p>
                </div>
            </div>
        </article>

        <article class="admin-stat-card">
            <div class="admin-stat-card__top">
                <p class="admin-stat-card__label">Editorial Team</p>
                <p class="admin-stat-card__meta">[[ $editorUsers ]] editors</p>
            </div>

            <div class="admin-stat-card__body">
                <span class="admin-stat-card__icon">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M6 19h12"></path>
                        <path d="M8 17V7a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v10"></path>
                        <path d="M9 9h6"></path>
                        <path d="M9 12h6"></path>
                    </svg>
                </span>

                <div>
                    <p class="admin-stat-card__value">[[ $editorUsers + $authorUsers ]]</p>
                    <p class="admin-stat-card__meta">[[ $authorUsers ]] authors supporting the workflow.</p>
                </div>
            </div>
        </article>

        <article class="admin-stat-card">
            <div class="admin-stat-card__top">
                <p class="admin-stat-card__label">Administrators</p>
                <p class="admin-stat-card__meta">Control access</p>
            </div>

            <div class="admin-stat-card__body">
                <span class="admin-stat-card__icon">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M12 3 5 6v5c0 4.6 3 8.7 7 10 4-1.3 7-5.4 7-10V6l-7-3Z"></path>
                        <path d="m9.5 12 1.8 1.8 3.7-3.7"></path>
                    </svg>
                </span>

                <div>
                    <p class="admin-stat-card__value">[[ $adminUsers ]]</p>
                    <p class="admin-stat-card__meta">Users with full publishing permissions.</p>
                </div>
            </div>
        </article>
    </section>

    <form class="admin-users-toolbar" method="GET" action="[[ route('admin.users.index') ]]">
        <div class="admin-users-toolbar__search">
            <svg viewBox="0 0 24 24" aria-hidden="true">
                <circle cx="11" cy="11" r="6.5"></circle>
                <path d="M16 16l4.5 4.5"></path>
            </svg>
            <input id="search" name="search" type="search" value="[[ request('search', '') ]]" placeholder="Search name, email, or role" aria-label="Search users">
        </div>

        <div class="admin-users-toolbar__actions">
            <div class="admin-users-toolbar__filters">
                <label class="sr-only" for="role">Filter by role</label>
                <select id="role" name="role" aria-label="Filter by role">
                    <option value="">Filter by role</option>
                    #foreach ($roleOptions as $roleValue => $roleLabel)
                        <option value="[[ $roleValue ]]" [[ strtolower((string) request('role', '')) === $roleValue ? 'selected' : '' ]]>[[ $roleLabel ]]</option>
                    #endforeach
                </select>
            </div>
            <button class="admin-button admin-button--ghost admin-button--with-icon" type="submit">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M4 6h16l-6 7v4l-4 2v-6Z"></path>
                </svg>
                <span>Filter</span>
            </button>
            <a class="admin-text-link" href="[[ route('admin.users.index') ]]">Reset</a>
        </div>
    </form>

    <section class="admin-panel">
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>2FA</th>
                        <th>Posts</th>
                        <th>Created</th>
                        <th>Updated</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    #foreach ($users['data'] as $user)
                        <tr>
                            <td>
                                <div class="admin-table__title-row">
                                    <span class="admin-user-thumb [[ ($user->image ?? '') !== '' ? 'has-image' : '' ]]">
                                        #if (($user->image ?? '') !== '')
                                            <img src="[[ $user->image ]]" alt="[[ $user->name ]]">
                                        #else
                                            [[ strtoupper(substr((string) $user->name, 0, 1)) ]]
                                        #endif
                                    </span>
                                    <div>
                                        <a class="admin-table__title" href="[[ route('admin.users.edit', ['user' => $user->id]) ]]">[[ $user->name ]]</a>
                                        <p class="admin-table__meta">[[ $user->email ]]</p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="admin-badge admin-badge--[[ strtolower((string) $user->role) ]]">[[ $roleOptions[strtolower((string) $user->role)] ?? ucfirst((string) $user->role) ]]</span>
                            </td>
                            <td>
                                <span class="admin-status-indicator [[ ($user->status ?? false) ? 'is-active' : 'is-inactive' ]]">
                                    #if ($user->status ?? false)
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
                            <td>
                                <span class="admin-status-indicator [[ Auth::hasTwoFactorEnabled($user) ? 'is-active' : 'is-inactive' ]]">
                                    #if (Auth::hasTwoFactorEnabled($user))
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
                            <td>[[ $user->posts_count ?? 0 ]]</td>
                            <td>[[ date('M d, Y', strtotime($user->created_at ?? 'now')) ]]</td>
                            <td>[[ date('M d, Y', strtotime($user->updated_at ?? 'now')) ]]</td>
                            <td class="admin-table__actions">
                                <a class="admin-inline-link" href="[[ route('admin.users.edit', ['user' => $user->id]) ]]">Edit</a>
                                <form method="POST" action="[[ route('admin.users.destroy', ['user' => $user->id]) ]]">
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
            [[! paginator($users)->links() !]]
        </div>
    </section>
#endsection
