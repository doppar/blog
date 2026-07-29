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
                    <div class="flex items-center gap-2">
                        <span class="font-medium text-ink">[[ $comment->user->name ]]</span>
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

            <!-- Reply Button -->
            #if (auth()->check())
            <button type="button" class="reply-btn mt-2 text-sm text-ink-soft hover:text-primary transition-colors" data-comment-id="[[ $comment->id ]]">
                Reply
            </button>
            #endif

            <!-- Replies -->
            #if ($comment->replies && count($comment->replies) > 0)
            <div class="mt-4 ml-4 space-y-4 border-l-2 border-soft pl-4">
                #foreach ($comment->replies as $reply)
                    #include('partials.comment', ['comment' => $reply])
                #endforeach
            </div>
            #endif
        </div>
    </div>
</div>
