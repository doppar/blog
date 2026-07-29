<div class="comment-item" data-comment-id="[[ $comment->id ]]">
    <div class="flex gap-4">
        <!-- User Avatar -->
        <div class="shrink-0">
            #if (($comment->user->image ?? '') !== '')
            <img src="[[ $comment->user->image ]]" alt="[[ $comment->user->name ]]" class="w-10 h-10 rounded-full object-cover">
            #else
            <div class="w-10 h-10 rounded-full bg-primary grid place-items-center text-sm font-bold text-white">
                [[ strtoupper(substr($comment->user->name ?? 'A', 0, 1)) ]]
            </div>
            #endif
        </div>

        <!-- Comment Content -->
        <div class="flex-1 min-w-0">
            <div class="bg-white rounded-xl border border-soft p-4">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="font-medium text-ink">[[ $comment->user->name ]]</span>
                        #if (isset($postAuthorId) && (int) $comment->user_id === (int) $postAuthorId)
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-primary/10 text-primary text-[11px] font-semibold">Author</span>
                        #endif
                        <span class="text-xs text-ink-soft">[[ $comment->created_at->diffForHumans() ]]</span>
                    </div>
                    #if (auth()->check() && auth()->id() === $comment->user_id)
                    <div class="flex items-center gap-2">
                        <button type="button" class="edit-comment-btn text-xs text-ink-soft hover:text-primary transition-colors" data-comment-id="[[ $comment->id ]]">Edit</button>
                        <button type="button" class="delete-comment-btn text-xs text-ink-soft hover:text-red-500 transition-colors" data-comment-id="[[ $comment->id ]]">Delete</button>
                    </div>
                    #endif
                </div>
                <p class="comment-body text-ink leading-relaxed">[[! nl2br($this->e($comment->body)) !]]</p>
            </div>

            <!-- Actions -->
            <div class="flex items-center gap-4 mt-2 text-sm">
                #if (auth()->check())
                <button type="button" class="comment-like-btn font-medium text-ink-soft hover:text-primary transition-colors [[ $comment->liked_by_viewer ? 'text-primary' : '' ]]" data-comment-id="[[ $comment->id ]]" data-liked="[[ $comment->liked_by_viewer ? '1' : '0' ]]">
                    [[ $comment->liked_by_viewer ? 'Liked' : 'Like' ]]
                </button>
                #endif
                #if (auth()->check())
                <button type="button" class="reply-btn font-medium text-ink-soft hover:text-primary transition-colors" data-comment-id="[[ $comment->id ]]">
                    Reply
                </button>
                #endif
                <span class="comment-like-count inline-flex items-center gap-1 text-ink-soft [[ $comment->likes_count > 0 ? '' : 'hidden' ]]">
                    <svg class="w-3.5 h-3.5 text-primary" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M2 21h2a1 1 0 001-1v-9a1 1 0 00-1-1H2v11zM22.67 12.06c.11-.24.17-.5.17-.76a2.3 2.3 0 00-2.3-2.3h-5.05l.76-3.65.02-.24c0-.34-.14-.66-.36-.89L14.83 3 8.41 9.41C8.05 9.78 7.83 10.28 7.83 10.83v8.29a2 2 0 002 2h9c.82 0 1.54-.5 1.84-1.22l3-6.99a1.8 1.8 0 00.14-.69v-.14z" />
                    </svg>
                    <span class="comment-like-count-value">[[ $comment->likes_count ]]</span>
                </span>
            </div>

            <!-- Replies -->
            #if ($comment->replies && count($comment->replies) > 0)
            <div class="mt-4 ml-4 space-y-4 border-l-2 border-soft pl-4">
                #foreach ($comment->replies as $reply)
                    #include('partials.comment', ['comment' => $reply, 'postAuthorId' => $postAuthorId ?? null])
                #endforeach
            </div>
            #endif
        </div>
    </div>
</div>
