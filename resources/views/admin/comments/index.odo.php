#extends('layouts.admin')
#section('title')
    Comments
#endsection
#section('page_title')
    Comments
#endsection
#section('page_description')
    Moderate reader comments and replies across every post.
#endsection
#section('content')
    <section class="admin-stat-grid">
        <article class="admin-stat-card">
            <div class="admin-stat-card__top">
                <p class="admin-stat-card__label">Approved</p>
                <div class="admin-stat-card__range">
                    <span>Visible</span>
                </div>
            </div>

            <div class="admin-stat-card__body">
                <span class="admin-stat-card__icon">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <circle cx="12" cy="12" r="8.5"></circle>
                        <path d="m8.8 12.2 2.2 2.2 4.4-4.6"></path>
                    </svg>
                </span>

                <div>
                    <p class="admin-stat-card__value">[[ $approvedComments ]]</p>
                    <p class="admin-stat-card__meta">visible to readers</p>
                </div>
            </div>
        </article>

        <article class="admin-stat-card">
            <div class="admin-stat-card__top">
                <p class="admin-stat-card__label">Disapproved</p>
                <div class="admin-stat-card__range">
                    <span>Hidden</span>
                </div>
            </div>

            <div class="admin-stat-card__body">
                <span class="admin-stat-card__icon">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <circle cx="12" cy="12" r="8.5"></circle>
                        <path d="m9.2 9.2 5.6 5.6"></path>
                        <path d="m14.8 9.2-5.6 5.6"></path>
                    </svg>
                </span>

                <div>
                    <p class="admin-stat-card__value">[[ $disapprovedComments ]]</p>
                    <p class="admin-stat-card__meta">hidden from readers</p>
                </div>
            </div>
        </article>
    </section>

    <form class="admin-users-toolbar" method="GET" action="[[ route('admin.comments.index') ]]">
        <div class="admin-users-toolbar__search">
            <svg viewBox="0 0 24 24" aria-hidden="true">
                <circle cx="11" cy="11" r="6.5"></circle>
                <path d="M16 16l4.5 4.5"></path>
            </svg>
            <input id="q" name="q" type="search" value="[[ request()->q ]]" placeholder="Search comment, author, or post" aria-label="Search comments">
        </div>

        <div class="admin-users-toolbar__actions">
            <div class="admin-users-toolbar__filters">
                <select id="status" name="status" aria-label="Filter by status">
                    <option value="">All statuses</option>
                    <option value="approved" [[ request()->status === 'approved' ? 'selected' : '' ]]>Approved</option>
                    <option value="disapproved" [[ request()->status === 'disapproved' ? 'selected' : '' ]]>Disapproved</option>
                </select>
            </div>
            <button class="admin-button admin-button--ghost admin-button--with-icon" type="submit">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M4 6h16l-6 7v4l-4 2v-6Z"></path>
                </svg>
                <span>Filter</span>
            </button>
            <a class="admin-text-link" href="[[ route('admin.comments.index') ]]">Reset</a>
        </div>
    </form>

    <section class="admin-panel">
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Comment</th>
                        <th>Author</th>
                        <th>Post</th>
                        <th>Likes</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    #foreach ($comments['data'] as $comment)
                        <tr>
                            <td>
                                #if ($comment->parent)
                                <p class="admin-table__meta">Reply to: [[ str()->truncate((string) $comment->parent->body, 40) ]]</p>
                                #endif
                                <p>[[ str()->truncate((string) $comment->body, 120) ]]</p>
                            </td>
                            <td>
                                <div class="admin-table__title-row">
                                    #if (($comment->user->image ?? '') !== '')
                                    <img src="[[ $comment->user->image ]]" alt="[[ $comment->user->name ]]" class="admin-avatar-sm" style="width:28px;height:28px;border-radius:9999px;object-fit:cover;">
                                    #else
                                    <span class="admin-color-dot" style="width:28px;height:28px;border-radius:9999px;background:var(--admin-accent,#6d6cf3);display:inline-flex;align-items:center;justify-content:center;color:#fff;font-size:12px;font-weight:700;">[[ strtoupper(substr($comment->user->name ?? 'A', 0, 1)) ]]</span>
                                    #endif
                                    <div>
                                        [[ $comment->user->name ?? 'Deleted user' ]]
                                    </div>
                                </div>
                            </td>
                            <td>
                                #if ($comment->post)
                                <a class="admin-table__title" href="[[ route('post.show', ['post' => $comment->post->slug]) ]]" target="_blank" rel="noopener noreferrer">[[ str()->truncate((string) $comment->post->title, 40) ]]</a>
                                #else
                                <span class="admin-table__meta">Deleted post</span>
                                #endif
                            </td>
                            <td>[[ $comment->likes_count ?? 0 ]]</td>
                            <td>
                                <span class="admin-status-indicator [[ $comment->status ? 'is-active' : 'is-inactive' ]]">
                                    #if ($comment->status)
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
                                    <span>[[ $comment->status ? 'Approved' : 'Disapproved' ]]</span>
                                </span>
                            </td>
                            <td>[[ date('M d, Y', strtotime($comment->created_at ?? 'now')) ]]</td>
                            <td class="admin-table__actions">
                                #if ($comment->post)
                                <button
                                    type="button"
                                    class="admin-inline-link"
                                    data-comment-reply-open
                                    data-reply-url="[[ route('admin.comments.reply', ['comment' => $comment->id]) ]]"
                                    data-reply-preview="[[ $comment->user->name ?? 'Deleted user' ]]: [[ str()->truncate((string) $comment->body, 100) ]]"
                                >Reply</button>
                                #endif

                                #if ($comment->status)
                                <form method="POST" action="[[ route('admin.comments.disapprove', ['comment' => $comment->id]) ]]">
                                    #csrf
                                    #method('put')
                                    <button class="admin-inline-link" type="submit">Disapprove</button>
                                </form>
                                #else
                                <form method="POST" action="[[ route('admin.comments.approve', ['comment' => $comment->id]) ]]">
                                    #csrf
                                    #method('put')
                                    <button class="admin-inline-success" type="submit">Approve</button>
                                </form>
                                #endif

                                <form method="POST" action="[[ route('admin.comments.destroy', ['comment' => $comment->id]) ]]" data-confirm-message="Delete this comment and all of its replies?">
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
            [[! paginator($comments)->links() !]]
        </div>
    </section>

    <div class="admin-comment-reply-modal" data-comment-reply-modal aria-hidden="true">
        <button class="admin-comment-reply-modal__backdrop" type="button" data-comment-reply-modal-close aria-label="Close reply form"></button>

        <div class="admin-comment-reply-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="comment-reply-modal-title">
            <div class="admin-comment-reply-modal__head">
                <div>
                    <p class="admin-section__eyebrow">Reply as admin</p>
                    <h3 id="comment-reply-modal-title">Reply to comment</h3>
                    <p data-comment-reply-preview></p>
                </div>
                <button class="admin-comment-reply-modal__close" type="button" data-comment-reply-modal-close aria-label="Close reply form">&times;</button>
            </div>

            <form class="admin-comment-reply-modal__form" method="POST" data-comment-reply-form>
                #csrf
                <div class="admin-field admin-field--full">
                    <label for="comment-reply-body">Your reply</label>
                    <textarea id="comment-reply-body" name="body" rows="4" placeholder="Write your reply..." required></textarea>
                    #error('body')
                        <p class="admin-field__error">[[ $message ]]</p>
                    #enderror
                </div>
                <div class="admin-comment-reply-modal__actions">
                    <button class="admin-button admin-button--ghost" type="button" data-comment-reply-modal-close>Cancel</button>
                    <button class="admin-button" type="submit">Post Reply</button>
                </div>
            </form>
        </div>
    </div>
#endsection
