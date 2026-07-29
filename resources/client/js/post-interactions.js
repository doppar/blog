const SPINNER_SVG = '<svg class="inline-spinner animate-spin" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>';

function csrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
}

function jsonFetch(url, options = {}) {
    return fetch(url, {
        ...options,
        __skipAdminLoader: true,
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken(),
            ...(options.headers || {}),
        },
        body: options.body ?? '{}',
    });
}

async function parseJsonResponse(response) {
    const data = await response.json().catch(() => ({}));

    if (!response.ok) {
        const firstError = data.errors && typeof data.errors === 'object'
            ? Object.values(data.errors).flat()[0]
            : null;

        throw new Error(firstError || data.message || 'Something went wrong. Please try again.');
    }

    return data;
}

function setButtonLoading(button, isLoading, loadingLabel) {
    if (isLoading) {
        button.dataset.originalHtml = button.innerHTML;
        button.disabled = true;
        button.innerHTML = `${SPINNER_SVG}<span>${loadingLabel}</span>`;
    } else {
        button.disabled = false;
        if (button.dataset.originalHtml) {
            button.innerHTML = button.dataset.originalHtml;
            delete button.dataset.originalHtml;
        }
    }
}

function buildCommentHtml(comment) {
    return `
        <div class="comment-item" data-comment-id="${comment.id}">
            <div class="flex gap-4">
                <div class="shrink-0">
                    ${comment.user.avatar
                        ? `<img src="${comment.user.avatar}" alt="${comment.user.name}" class="w-10 h-10 rounded-full object-cover">`
                        : `<div class="w-10 h-10 rounded-full bg-primary grid place-items-center text-sm font-bold text-white">${comment.user.name.charAt(0).toUpperCase()}</div>`
                    }
                </div>
                <div class="flex-1 min-w-0">
                    <div class="bg-white rounded-xl border border-soft p-4">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-2">
                                <span class="font-medium text-ink">${comment.user.name}</span>
                                <span class="text-xs text-ink-soft">${comment.created_at}</span>
                            </div>
                        </div>
                        <p class="comment-body text-ink leading-relaxed">${comment.body.replace(/\n/g, '<br>')}</p>
                    </div>
                    <button type="button" class="reply-btn mt-2 text-sm text-ink-soft hover:text-primary transition-colors" data-comment-id="${comment.id}">
                        Reply
                    </button>
                </div>
            </div>
        </div>
    `;
}

function bumpCommentCount(delta) {
    const commentCountEl = document.getElementById('comments-count');
    if (!commentCountEl) return;

    const currentCount = parseInt(commentCountEl.textContent, 10) || 0;
    commentCountEl.textContent = Math.max(0, currentCount + delta);
}

function insertReplyIntoDom(parentId, commentHtml) {
    const parentComment = document.querySelector(`[data-comment-id="${parentId}"]`);
    let repliesContainer = parentComment.querySelector(':scope > .flex.gap-4 > .flex-1.min-w-0 > .ml-4');

    if (!repliesContainer) {
        repliesContainer = document.createElement('div');
        repliesContainer.className = 'mt-4 ml-4 space-y-4 border-l-2 border-soft pl-4';
        parentComment.querySelector(':scope > .flex.gap-4 > .flex-1.min-w-0').appendChild(repliesContainer);
    }

    repliesContainer.insertAdjacentHTML('beforeend', commentHtml);
}

document.addEventListener("DOMContentLoaded", function () {
    // Like functionality
    const likeBtn = document.getElementById('like-btn');
    if (likeBtn) {
        likeBtn.addEventListener('click', async function () {
            if (this.disabled) return;

            const slug = this.dataset.slug;
            const likeIcon = document.getElementById('like-icon');

            this.disabled = true;
            likeIcon.style.display = 'none';
            likeIcon.insertAdjacentHTML('beforebegin', SPINNER_SVG);

            try {
                const response = await jsonFetch(`/posts/${slug}/like`, { method: 'POST' });
                const data = await parseJsonResponse(response);

                const likeCount = document.getElementById('like-count');
                const likeText = document.getElementById('like-text');

                likeCount.textContent = data.like_count;
                likeText.textContent = data.liked ? 'Liked' : 'Like';
                likeIcon.setAttribute('fill', data.liked ? 'currentColor' : 'none');
                this.classList.toggle('text-primary', data.liked);
                this.dataset.liked = data.liked ? '1' : '0';
            } catch (error) {
                console.error('Like error:', error);
            } finally {
                this.querySelector('.inline-spinner')?.remove();
                likeIcon.style.display = '';
                this.disabled = false;
            }
        });
    }

    const postSlug = likeBtn?.dataset.slug;

    // Top-level comment functionality
    const commentForm = document.getElementById('comment-form');
    const commentsContainer = document.getElementById('comments-container');
    const commentBody = document.getElementById('comment-body');
    const commentFormError = document.getElementById('comment-form-error');

    function showCommentFormError(message) {
        if (!commentFormError) return;
        commentFormError.textContent = message;
        commentFormError.classList.remove('hidden');
    }

    function clearCommentFormError() {
        if (!commentFormError) return;
        commentFormError.textContent = '';
        commentFormError.classList.add('hidden');
    }

    if (commentForm) {
        commentForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            const body = commentBody.value.trim();

            if (!body) return;

            clearCommentFormError();

            const submitBtn = commentForm.querySelector('button[type="submit"]');
            setButtonLoading(submitBtn, true, 'Posting…');

            try {
                const response = await jsonFetch(`/posts/${postSlug}/comments`, {
                    method: 'POST',
                    body: JSON.stringify({ body, parent_id: null })
                });

                const data = await parseJsonResponse(response);

                commentsContainer.insertAdjacentHTML('afterbegin', buildCommentHtml(data.comment));
                commentBody.value = '';
                bumpCommentCount(1);
            } catch (error) {
                console.error('Comment error:', error);
                showCommentFormError(error.message);
            } finally {
                setButtonLoading(submitBtn, false);
            }
        });
    }

    // Inline reply form functionality — clicking "Reply" opens a small reply
    // box directly under that comment, instead of jumping up to the main
    // comment form at the top of the thread.
    document.addEventListener('click', function (e) {
        if (!e.target.classList.contains('reply-btn')) return;

        const replyBtn = e.target;
        const commentId = replyBtn.dataset.commentId;

        // Only one reply box open at a time.
        document.querySelectorAll('.reply-form-wrapper').forEach((el) => el.remove());

        const wrapperHtml = `
            <div class="reply-form-wrapper mt-3" data-reply-form-for="${commentId}">
                <textarea class="reply-form-textarea w-full px-3 py-2 rounded-xl border border-soft bg-white text-ink placeholder-ink-soft text-sm focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all resize-none" rows="3" placeholder="Write a reply..."></textarea>
                <p class="reply-form-error mt-1 text-xs text-red-500 hidden"></p>
                <div class="flex justify-end gap-2 mt-2">
                    <button type="button" class="cancel-reply-btn px-4 py-1.5 rounded-full border border-soft text-ink text-sm font-medium hover:border-ink transition-all">Cancel</button>
                    <button type="button" class="submit-reply-btn inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-primary text-white text-sm font-medium hover:brightness-110 transition-all">Reply</button>
                </div>
            </div>
        `;

        replyBtn.insertAdjacentHTML('afterend', wrapperHtml);

        const wrapper = replyBtn.nextElementSibling;
        const textarea = wrapper.querySelector('.reply-form-textarea');
        const errorEl = wrapper.querySelector('.reply-form-error');
        const cancelBtn = wrapper.querySelector('.cancel-reply-btn');
        const submitReplyBtn = wrapper.querySelector('.submit-reply-btn');

        textarea.focus();

        cancelBtn.addEventListener('click', function () {
            wrapper.remove();
        });

        submitReplyBtn.addEventListener('click', async function () {
            const body = textarea.value.trim();

            if (!body) return;

            errorEl.classList.add('hidden');
            setButtonLoading(submitReplyBtn, true, 'Posting…');

            try {
                const response = await jsonFetch(`/posts/${postSlug}/comments`, {
                    method: 'POST',
                    body: JSON.stringify({ body, parent_id: commentId })
                });

                const data = await parseJsonResponse(response);

                insertReplyIntoDom(commentId, buildCommentHtml(data.comment));
                bumpCommentCount(1);
                wrapper.remove();
            } catch (error) {
                console.error('Reply error:', error);
                errorEl.textContent = error.message;
                errorEl.classList.remove('hidden');
                setButtonLoading(submitReplyBtn, false);
            }
        });
    });

    // Delete comment functionality
    document.addEventListener('click', async function (e) {
        if (e.target.classList.contains('delete-comment-btn')) {
            const deleteBtn = e.target;
            const commentId = deleteBtn.dataset.commentId;

            if (!confirm('Are you sure you want to delete this comment?')) return;

            setButtonLoading(deleteBtn, true, 'Deleting…');

            try {
                const response = await jsonFetch(`/comments/${commentId}`, { method: 'DELETE' });
                await parseJsonResponse(response);

                const commentItem = document.querySelector(`[data-comment-id="${commentId}"]`);
                commentItem.remove();
                bumpCommentCount(-1);
            } catch (error) {
                console.error('Delete error:', error);
                alert(error.message);
                setButtonLoading(deleteBtn, false);
            }
        }
    });

    // Edit comment functionality
    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('edit-comment-btn')) {
            const commentId = e.target.dataset.commentId;
            const commentItem = document.querySelector(`[data-comment-id="${commentId}"]`);
            const commentBodyEl = commentItem.querySelector('.comment-body');
            const currentBody = commentBodyEl.textContent;

            const textarea = document.createElement('textarea');
            textarea.value = currentBody;
            textarea.className = 'w-full px-4 py-3 rounded-xl border border-soft bg-white text-ink focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all resize-none';
            textarea.rows = 4;

            commentBodyEl.replaceWith(textarea);
            textarea.focus();

            const saveBtn = document.createElement('button');
            saveBtn.type = 'button';
            saveBtn.textContent = 'Save';
            saveBtn.className = 'inline-flex items-center gap-2 mt-2 px-4 py-2 rounded-full bg-primary text-white font-medium hover:brightness-110 transition-all';

            const cancelBtn = document.createElement('button');
            cancelBtn.type = 'button';
            cancelBtn.textContent = 'Cancel';
            cancelBtn.className = 'mt-2 px-4 py-2 rounded-full border border-soft text-ink font-medium hover:border-ink transition-all ml-2';

            const errorEl = document.createElement('p');
            errorEl.className = 'mt-2 text-sm text-red-500 hidden';

            const buttonContainer = document.createElement('div');
            buttonContainer.className = 'flex gap-2';
            buttonContainer.appendChild(saveBtn);
            buttonContainer.appendChild(cancelBtn);

            textarea.after(buttonContainer);
            buttonContainer.after(errorEl);

            cancelBtn.addEventListener('click', function () {
                const newBodyEl = document.createElement('p');
                newBodyEl.className = 'comment-body text-ink leading-relaxed';
                newBodyEl.textContent = currentBody.replace(/\n/g, '<br>');
                textarea.replaceWith(newBodyEl);
                buttonContainer.remove();
                errorEl.remove();
            });

            saveBtn.addEventListener('click', async function () {
                const newBody = textarea.value.trim();

                if (!newBody) return;

                errorEl.classList.add('hidden');
                setButtonLoading(saveBtn, true, 'Saving…');

                try {
                    const response = await jsonFetch(`/comments/${commentId}`, {
                        method: 'PUT',
                        body: JSON.stringify({ body: newBody })
                    });

                    const data = await parseJsonResponse(response);

                    const newBodyEl = document.createElement('p');
                    newBodyEl.className = 'comment-body text-ink leading-relaxed';
                    newBodyEl.textContent = data.comment.body.replace(/\n/g, '<br>');
                    textarea.replaceWith(newBodyEl);
                    buttonContainer.remove();
                    errorEl.remove();
                } catch (error) {
                    console.error('Edit error:', error);
                    errorEl.textContent = error.message;
                    errorEl.classList.remove('hidden');
                    setButtonLoading(saveBtn, false);
                }
            });
        }
    });
});
