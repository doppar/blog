import { Editor, Node, mergeAttributes } from '@tiptap/core';
import CodeBlockLowlight from '@tiptap/extension-code-block-lowlight';
import Link from '@tiptap/extension-link';
import StarterKit from '@tiptap/starter-kit';
import Placeholder from '@tiptap/extension-placeholder';
import Underline from '@tiptap/extension-underline';
import { common, createLowlight } from 'lowlight';
import hljs from 'highlight.js/lib/core';

const SIDEBAR_STORAGE_KEY = 'editorial-desk-sidebar-groups';
const MEDIA_VIEW_STORAGE_KEY = 'editorial-desk-media-view';
const lowlight = createLowlight(common);

Object.entries(common).forEach(([name, grammar]) => hljs.registerLanguage(name, grammar));
let activeAjaxRequests = 0;

const EditorImage = Node.create({
    name: 'editorImage',
    group: 'block',
    atom: true,
    draggable: true,
    selectable: true,

    addAttributes() {
        return {
            src: {
                default: null,
            },
            alt: {
                default: '',
            },
            title: {
                default: '',
            },
            caption: {
                default: '',
            },
            align: {
                default: 'center',
            },
        };
    },

    parseHTML() {
        return [
            {
                tag: 'figure[data-editor-image]',
                getAttrs: (element) => {
                    const image = element.querySelector('img');
                    const caption = element.querySelector('figcaption');

                    return {
                        src: image?.getAttribute('src') || '',
                        alt: image?.getAttribute('alt') || '',
                        title: image?.getAttribute('title') || '',
                        caption: element.getAttribute('data-caption') || caption?.textContent || '',
                        align: element.getAttribute('data-align') || 'center',
                    };
                },
            },
            {
                tag: 'img[src]',
                getAttrs: (element) => ({
                    src: element.getAttribute('src') || '',
                    alt: element.getAttribute('alt') || '',
                    title: element.getAttribute('title') || '',
                    caption: '',
                    align: 'center',
                }),
            },
        ];
    },

    renderHTML({ HTMLAttributes }) {
        const {
            caption = '',
            align = 'center',
            src = '',
            alt = '',
            title = '',
        } = HTMLAttributes;

        const figureAttributes = mergeAttributes(
            {
                'data-editor-image': 'true',
                'data-align': align,
                'data-caption': caption,
                class: `editor-image-block align-${align}`,
            },
        );

        const imageAttributes = mergeAttributes({
            src,
            alt,
            title,
            loading: 'lazy',
            decoding: 'async',
        });

        const children = [['img', imageAttributes]];

        if (caption) {
            children.push(['figcaption', {}, caption]);
        }

        return ['figure', figureAttributes, ...children];
    },

    addCommands() {
        return {
            setEditorImage: (attributes) => ({ commands }) => commands.insertContent({
                type: this.name,
                attrs: {
                    caption: '',
                    align: 'center',
                    ...attributes,
                },
            }),
            updateEditorImage: (attributes) => ({ commands }) => commands.updateAttributes(this.name, attributes),
        };
    },
});

function slugify(value) {
    return (value || '')
        .toString()
        .trim()
        .toLowerCase()
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-+|-+$/g, '') || 'entry';
}

function escapeEditorHtml(value) {
    return String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

function normalizeEditorContent(value) {
    const raw = String(value ?? '').trim();

    if (!raw) {
        return '<p></p>';
    }

    if (/<[a-z][\s\S]*>/i.test(raw)) {
        return raw;
    }

    return raw
        .split(/\n{2,}/)
        .map((block) => `<p>${escapeEditorHtml(block).replace(/\n/g, '<br>')}</p>`)
        .join('');
}

function bootDismissibleAlerts() {
    document.querySelectorAll('[data-dismiss-alert]').forEach((button) => {
        button.addEventListener('click', () => {
            button.closest('[data-dismissible]')?.remove();
        });
    });
}

function bootDeleteConfirmations() {
    document.querySelectorAll('form').forEach((form) => {
        const methodInput = form.querySelector('input[name="_method"]');

        if (!methodInput || String(methodInput.value).toUpperCase() !== 'DELETE') {
            return;
        }

        form.addEventListener('submit', (event) => {
            const message = form.getAttribute('data-confirm-message') || 'Are you sure you want to delete this item?';

            if (!window.confirm(message)) {
                event.preventDefault();
            }
        });
    });
}

function pushAdminAlert(type, message) {
    const workspace = document.querySelector('.admin-workspace');

    if (!workspace || !message) {
        return;
    }

    const alert = document.createElement('div');
    alert.className = `admin-alert ${type === 'danger' ? 'admin-alert--danger' : 'admin-alert--success'}`;
    alert.setAttribute('data-dismissible', '');
    alert.innerHTML = `
        <p>${message}</p>
        <button type="button" class="admin-alert__close" data-dismiss-alert aria-label="Dismiss message">×</button>
    `;

    workspace.prepend(alert);
    alert.querySelector('[data-dismiss-alert]')?.addEventListener('click', () => alert.remove());
}

function bootSlugForms() {
    document.querySelectorAll('[data-slug-form]').forEach((form) => {
        const source = form.querySelector('[data-slug-source]');
        const target = form.querySelector('[data-slug-target]');

        if (!source || !target) {
            return;
        }

        let slugTouched = target.value.trim() !== '';

        target.addEventListener('input', () => {
            slugTouched = true;
        });

        source.addEventListener('input', () => {
            if (!slugTouched) {
                target.value = slugify(source.value);
            }
        });
    });
}

function closeSidebar() {
    document.body.classList.remove('admin-sidebar-open');
}

function bootSidebarToggle() {
    const toggle = document.querySelector('[data-sidebar-toggle]');
    const backdrop = document.querySelector('[data-sidebar-close]');

    if (toggle) {
        toggle.addEventListener('click', () => {
            document.body.classList.toggle('admin-sidebar-open');
        });
    }

    if (backdrop) {
        backdrop.addEventListener('click', closeSidebar);
    }

    document.querySelectorAll('.admin-sidebar__link').forEach((link) => {
        link.addEventListener('click', closeSidebar);
    });
}

function readSidebarState() {
    try {
        const state = window.localStorage.getItem(SIDEBAR_STORAGE_KEY);

        return state ? JSON.parse(state) : {};
    } catch {
        return {};
    }
}

function writeSidebarState(state) {
    try {
        window.localStorage.setItem(SIDEBAR_STORAGE_KEY, JSON.stringify(state));
    } catch {
        // Ignore storage failures.
    }
}

function setSidebarSectionState(section, isOpen) {
    const trigger = section.querySelector('[data-sidebar-group-trigger]');

    section.classList.toggle('is-open', isOpen);
    section.classList.toggle('is-collapsed', !isOpen);
    trigger?.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
}

function bootSidebarGroups() {
    const state = readSidebarState();

    document.querySelectorAll('[data-sidebar-group]').forEach((section) => {
        const name = section.getAttribute('data-sidebar-group');
        const trigger = section.querySelector('[data-sidebar-group-trigger]');

        if (!name || !trigger) {
            return;
        }

        const hasSavedState = Object.prototype.hasOwnProperty.call(state, name);
        const defaultOpen = section.classList.contains('is-open');

        setSidebarSectionState(section, hasSavedState ? Boolean(state[name]) : defaultOpen);

        trigger.addEventListener('click', () => {
            const nextState = section.classList.contains('is-collapsed');

            setSidebarSectionState(section, nextState);
            state[name] = nextState;
            writeSidebarState(state);
        });
    });
}

function closeAllDropdowns() {
    document.querySelectorAll('[data-dropdown].is-open').forEach((dropdown) => {
        dropdown.classList.remove('is-open');
        dropdown.querySelector('[data-dropdown-trigger]')?.setAttribute('aria-expanded', 'false');
    });
}

function bootDropdowns() {
    document.querySelectorAll('[data-dropdown]').forEach((dropdown) => {
        const trigger = dropdown.querySelector('[data-dropdown-trigger]');

        if (!trigger) {
            return;
        }

        trigger.addEventListener('click', (event) => {
            event.preventDefault();
            event.stopPropagation();

            const isOpen = dropdown.classList.contains('is-open');

            closeAllDropdowns();

            if (!isOpen) {
                dropdown.classList.add('is-open');
                trigger.setAttribute('aria-expanded', 'true');
            }
        });

        dropdown.querySelectorAll('[data-dropdown-menu] a, [data-dropdown-menu] button').forEach((item) => {
            item.addEventListener('click', () => {
                closeAllDropdowns();
            });
        });
    });

    document.addEventListener('click', (event) => {
        if (!event.target.closest('[data-dropdown]')) {
            closeAllDropdowns();
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeAllDropdowns();
            closeSidebar();
        }
    });
}

function bootSearchShortcut() {
    const input = document.querySelector('[data-global-search]');

    if (!input) {
        return;
    }

    document.addEventListener('keydown', (event) => {
        const target = event.target;
        const isTypingContext = target instanceof HTMLElement && (
            target.tagName === 'INPUT' ||
            target.tagName === 'TEXTAREA' ||
            target.isContentEditable
        );

        if (event.key === '/' && !isTypingContext) {
            event.preventDefault();
            input.focus();
            input.select();
        }
    });
}

function escapeHtml(value) {
    return String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

function formatBytes(bytes) {
    if (!Number.isFinite(bytes) || bytes <= 0) {
        return '0 B';
    }

    if (bytes < 1024) {
        return `${bytes} B`;
    }

    const units = ['KB', 'MB', 'GB', 'TB'];
    let value = bytes / 1024;

    for (const unit of units) {
        if (value < 1024 || unit === 'TB') {
            return `${value >= 100 ? Math.round(value) : value.toFixed(1).replace(/\.0$/, '')} ${unit}`;
        }

        value /= 1024;
    }

    return `${value.toFixed(1)} TB`;
}

function getAjaxLoader() {
    let loader = document.querySelector('[data-admin-ajax-loader]');

    if (loader) {
        return loader;
    }

    loader = document.createElement('div');
    loader.className = 'admin-ajax-loader';
    loader.setAttribute('data-admin-ajax-loader', '');
    loader.setAttribute('aria-hidden', 'true');
    loader.innerHTML = `
        <div class="admin-ajax-loader__panel" role="status" aria-live="polite">
            <span class="admin-ajax-loader__spinner"></span>
            <strong data-admin-ajax-loader-title>Working…</strong>
            <span data-admin-ajax-loader-copy>Processing your request.</span>
        </div>
    `;

    document.body.appendChild(loader);

    return loader;
}

function showAjaxLoader(message = 'Working…') {
    const loader = getAjaxLoader();

    activeAjaxRequests += 1;
    loader.classList.add('is-visible');
    loader.setAttribute('aria-hidden', 'false');
    loader.querySelector('[data-admin-ajax-loader-title]')?.replaceChildren(message);
}

function hideAjaxLoader() {
    const loader = document.querySelector('[data-admin-ajax-loader]');

    activeAjaxRequests = Math.max(activeAjaxRequests - 1, 0);

    if (!loader || activeAjaxRequests > 0) {
        return;
    }

    loader.classList.remove('is-visible');
    loader.setAttribute('aria-hidden', 'true');
}

function bootAjaxLoader() {
    if (typeof window.fetch !== 'function' || window.__ADMIN_FETCH_PATCHED__) {
        return;
    }

    const nativeFetch = window.fetch.bind(window);

    window.fetch = async (input, init = {}) => {
        const options = init && typeof init === 'object' ? { ...init } : {};
        const skipLoader = Boolean(options.__skipAdminLoader);
        const loaderMessage = typeof options.__loaderMessage === 'string' && options.__loaderMessage
            ? options.__loaderMessage
            : 'Working…';

        delete options.__skipAdminLoader;
        delete options.__loaderMessage;

        if (!skipLoader) {
            showAjaxLoader(loaderMessage);
        }

        try {
            return await nativeFetch(input, options);
        } finally {
            if (!skipLoader) {
                hideAjaxLoader();
            }
        }
    };

    window.__ADMIN_FETCH_PATCHED__ = true;
}

function parseTagList(value) {
    if (!value) {
        return [];
    }

    return value
        .split(',')
        .map((item) => item.trim())
        .filter(Boolean);
}

function parseMediaPayload(node) {
    const item = node?.closest?.('[data-media-item], [data-cover-media-item]');
    const raw = item?.getAttribute('data-media');

    if (!item || !raw) {
        return null;
    }

    try {
        return JSON.parse(raw);
    } catch {
        return null;
    }
}

function copyText(value, successMessage = 'Copied to clipboard.') {
    const text = String(value || '').trim();

    if (!text) {
        return;
    }

    navigator.clipboard.writeText(text)
        .then(() => pushAdminAlert('success', successMessage))
        .catch(() => pushAdminAlert('danger', 'Could not copy to clipboard.'));
}

function mediaItemMarkup(item) {
    const payload = escapeHtml(JSON.stringify(item));
    const title = escapeHtml(item.title);
    const alt = escapeHtml(item.alt_text || item.title);
    const extension = escapeHtml(item.extension);
    const dimensions = escapeHtml(item.dimensions_label);
    const originalName = escapeHtml(item.original_name);
    const mimeType = escapeHtml(item.mime_type);
    const sizeLabel = escapeHtml(item.size_label);
    const uploadedBy = escapeHtml(item.uploaded_by);
    const relative = escapeHtml(item.created_relative);
    const url = escapeHtml(item.url);
    const thumbnailUrl = escapeHtml(item.thumbnail_url);

    return {
        card: `
            <article class="admin-media-card" data-media-item data-media-id="${item.id}" data-media="${payload}">
                <button class="admin-media-card__preview" type="button" data-media-open-details>
                    <img src="${thumbnailUrl}" alt="${alt}">
                </button>

                <div class="admin-media-card__content">
                    <div>
                        <p class="admin-media-card__title">${title}</p>
                        <p class="admin-media-card__meta">${extension} · ${dimensions}</p>
                    </div>

                    <div class="admin-media-card__actions">
                        <button class="admin-icon-button" type="button" data-media-copy-url aria-label="Copy media URL">
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                <rect x="9" y="9" width="10" height="10" rx="2"></rect>
                                <rect x="5" y="5" width="10" height="10" rx="2"></rect>
                            </svg>
                        </button>
                        <button class="admin-icon-button" type="button" data-media-delete aria-label="Delete media">
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M5 7h14"></path>
                                <path d="M9 7V5h6v2"></path>
                                <rect x="7" y="7" width="10" height="12" rx="2"></rect>
                            </svg>
                        </button>
                    </div>
                </div>
            </article>
        `,
        row: `
            <tr data-media-item data-media-id="${item.id}" data-media="${payload}">
                <td>
                    <button class="admin-media-table__thumb" type="button" data-media-open-details>
                        <img src="${thumbnailUrl}" alt="${alt}">
                    </button>
                </td>
                <td>
                    <a class="admin-table__title" href="${url}" target="_blank" rel="noreferrer">${title}</a>
                    <p class="admin-table__meta">${originalName}</p>
                </td>
                <td>${mimeType}</td>
                <td>${sizeLabel}</td>
                <td>${dimensions}</td>
                <td>
                    <div>${uploadedBy}</div>
                    <p class="admin-table__meta">${relative}</p>
                </td>
                <td class="admin-table__actions">
                    <button class="admin-inline-link" type="button" data-media-copy-url>Copy URL</button>
                    <button class="admin-inline-danger" type="button" data-media-delete>Delete</button>
                </td>
            </tr>
        `,
    };
}

function coverMediaCardMarkup(item) {
    const payload = escapeHtml(JSON.stringify(item));
    const title = escapeHtml(item.title);
    const alt = escapeHtml(item.alt_text || item.title);
    const dimensions = escapeHtml(item.dimensions_label || 'Flexible');
    const sizeLabel = escapeHtml(item.size_label || '');
    const thumbnailUrl = escapeHtml(item.thumbnail_url || item.url);

    return `
        <article class="admin-cover-modal__media-card" data-cover-media-item data-media="${payload}">
            <button class="admin-cover-modal__media-preview" type="button" data-cover-media-select>
                <img src="${thumbnailUrl}" alt="${alt}">
            </button>

            <div class="admin-cover-modal__media-meta">
                <strong>${title}</strong>
                <span>${dimensions} · ${sizeLabel}</span>
            </div>

            <div class="admin-cover-modal__media-actions">
                <button class="admin-button admin-button--ghost admin-button--compact" type="button" data-cover-media-copy-existing>Copy URL</button>
                <button class="admin-button admin-button--compact" type="button" data-cover-media-use-existing>Use image</button>
            </div>
        </article>
    `;
}

function bootMediaLibrary() {
    const root = document.querySelector('[data-media-library]');

    if (!root) {
        return;
    }

    const uploadInput = root.querySelector('[data-media-input]');
    const dropzone = root.querySelector('[data-media-dropzone]');
    const grid = root.querySelector('[data-media-grid]');
    const listBody = root.querySelector('[data-media-list-body]');
    const emptyState = root.querySelector('[data-media-empty]');
    const visibleCount = root.querySelector('[data-media-visible-count]');
    const drawer = document.querySelector('[data-media-drawer]');
    const drawerImage = drawer?.querySelector('[data-media-drawer-image]');
    const drawerTitle = drawer?.querySelector('[data-media-drawer-title]');
    const drawerOriginal = drawer?.querySelector('[data-media-drawer-original-name]');
    const drawerType = drawer?.querySelector('[data-media-drawer-type]');
    const drawerDimensions = drawer?.querySelector('[data-media-drawer-dimensions]');
    const drawerSize = drawer?.querySelector('[data-media-drawer-size]');
    const drawerAuthor = drawer?.querySelector('[data-media-drawer-author]');
    const drawerCreated = drawer?.querySelector('[data-media-drawer-created]');
    const drawerUrl = drawer?.querySelector('[data-media-drawer-url]');
    const drawerDelete = drawer?.querySelector('[data-media-drawer-delete]');
    const viewButtons = Array.from(root.querySelectorAll('[data-media-view]'));
    const viewPanels = Array.from(root.querySelectorAll('[data-media-view-panel]'));
    const globalUploadButtons = Array.from(document.querySelectorAll('[data-media-open-upload]'));
    const baseHeaders = {
        ...(window.__DOPPAR_FRONTEND__?.headers || {}),
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json',
    };

    const state = {
        activeItem: null,
        currentView: window.localStorage.getItem(MEDIA_VIEW_STORAGE_KEY) || 'grid',
    };

    function syncVisibleCount() {
        const count = root.querySelectorAll('[data-media-grid] [data-media-item]').length;

        if (visibleCount) {
            visibleCount.textContent = String(count);
        }

        emptyState?.classList.toggle('is-hidden', count > 0);
    }

    function updateCounts(counts) {
        if (!counts) {
            return;
        }

        const total = document.querySelector('[data-media-count-total]');
        const month = document.querySelector('[data-media-count-month]');
        const storage = document.querySelector('[data-media-count-storage]');

        if (total && Number.isFinite(counts.total_items)) {
            total.textContent = String(counts.total_items);
        }

        if (month && Number.isFinite(counts.uploaded_this_month)) {
            month.textContent = String(counts.uploaded_this_month);
        }

        if (storage && Number.isFinite(counts.storage_bytes)) {
            storage.textContent = counts.storage_label || formatBytes(counts.storage_bytes);
        }
    }

    function setView(nextView) {
        state.currentView = nextView === 'list' ? 'list' : 'grid';
        window.localStorage.setItem(MEDIA_VIEW_STORAGE_KEY, state.currentView);

        viewButtons.forEach((button) => {
            button.classList.toggle('is-active', button.getAttribute('data-media-view') === state.currentView);
        });

        viewPanels.forEach((panel) => {
            panel.classList.toggle('is-active', panel.getAttribute('data-media-view-panel') === state.currentView);
        });
    }

    function openDrawer(item) {
        if (!drawer || !item) {
            return;
        }

        state.activeItem = item;

        if (drawerImage) {
            drawerImage.src = item.url;
            drawerImage.alt = item.alt_text || item.title;
        }

        if (drawerTitle) drawerTitle.textContent = item.title;
        if (drawerOriginal) drawerOriginal.textContent = item.original_name;
        if (drawerType) drawerType.textContent = `${item.mime_type} · ${item.extension}`;
        if (drawerDimensions) drawerDimensions.textContent = item.dimensions_label;
        if (drawerSize) drawerSize.textContent = item.size_label;
        if (drawerAuthor) drawerAuthor.textContent = item.uploaded_by;
        if (drawerCreated) drawerCreated.textContent = item.created_label;
        if (drawerUrl) drawerUrl.value = item.url;

        drawer.classList.add('is-open');
        drawer.setAttribute('aria-hidden', 'false');
    }

    function closeDrawer() {
        if (!drawer) {
            return;
        }

        drawer.classList.remove('is-open');
        drawer.setAttribute('aria-hidden', 'true');
        state.activeItem = null;
    }

    async function deleteMedia(item) {
        if (!item || !item.destroy_url) {
            return;
        }

        const confirmed = window.confirm(`Delete "${item.title}" from the media library?`);

        if (!confirmed) {
            return;
        }

        const response = await fetch(item.destroy_url, {
            method: 'DELETE',
            headers: baseHeaders,
            __loaderMessage: 'Removing image…',
        });

        const payload = await response.json().catch(() => null);

        if (!response.ok) {
            throw new Error(payload?.message || 'The media file could not be deleted.');
        }

        root.querySelectorAll(`[data-media-id="${item.id}"]`).forEach((node) => node.remove());
        syncVisibleCount();
        updateCounts(payload?.counts);
        closeDrawer();
        pushAdminAlert('success', payload?.message || 'Media file deleted successfully.');
    }

    async function uploadFiles(files) {
        const selectedFiles = Array.from(files || []).filter(Boolean);

        if (!selectedFiles.length) {
            return;
        }

        const formData = new FormData();
        selectedFiles.forEach((file) => formData.append('files[]', file));

        const response = await fetch(root.getAttribute('data-upload-url') || '', {
            method: 'POST',
            headers: baseHeaders,
            body: formData,
            __loaderMessage: selectedFiles.length > 1
                ? `Uploading ${selectedFiles.length} images…`
                : 'Uploading image…',
        });

        const payload = await response.json().catch(() => null);

        if (!response.ok) {
            throw new Error(payload?.message || 'The selected images could not be uploaded.');
        }

        (payload?.items || []).slice().reverse().forEach((item) => {
            const markup = mediaItemMarkup(item);
            grid?.insertAdjacentHTML('afterbegin', markup.card);
            listBody?.insertAdjacentHTML('afterbegin', markup.row);
        });

        syncVisibleCount();
        updateCounts(payload?.counts);
        pushAdminAlert('success', payload?.message || 'Media uploaded successfully.');
    }

    globalUploadButtons.forEach((button) => {
        button.addEventListener('click', () => {
            uploadInput?.click();
            if (root.getAttribute('data-initial-focus') === 'upload') {
                dropzone?.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });

    uploadInput?.addEventListener('change', async () => {
        try {
            await uploadFiles(uploadInput.files);
        } catch (error) {
            pushAdminAlert('danger', error instanceof Error ? error.message : 'Upload failed.');
        } finally {
            uploadInput.value = '';
        }
    });

    dropzone?.addEventListener('dragover', (event) => {
        event.preventDefault();
        dropzone.classList.add('is-dragover');
    });

    dropzone?.addEventListener('dragleave', () => {
        dropzone.classList.remove('is-dragover');
    });

    dropzone?.addEventListener('drop', async (event) => {
        event.preventDefault();
        dropzone.classList.remove('is-dragover');

        try {
            await uploadFiles(event.dataTransfer?.files);
        } catch (error) {
            pushAdminAlert('danger', error instanceof Error ? error.message : 'Upload failed.');
        }
    });

    root.addEventListener('click', async (event) => {
        const target = event.target instanceof Element ? event.target : null;

        if (!target) {
            return;
        }

        if (target.closest('[data-media-open-details]')) {
            const item = parseMediaPayload(target);

            if (item) {
                openDrawer(item);
            }

            return;
        }

        if (target.closest('[data-media-copy-url]')) {
            const item = parseMediaPayload(target);
            const btn = target.closest('[data-media-copy-url]');

            if (item && btn) {
                const originalSvg = btn.innerHTML;
                btn.innerHTML = `<svg viewBox="0 0 24 24" aria-hidden="true"><path d="m5 12 5 5 9-9"/></svg>`;
                btn.classList.add('is-copied');

                copyText(item.url, 'Media URL copied successfully.');

                window.setTimeout(() => {
                    btn.innerHTML = originalSvg;
                    btn.classList.remove('is-copied');
                }, 2000);
            }

            return;
        }

        if (target.closest('[data-media-delete]')) {
            const item = parseMediaPayload(target);

            if (item) {
                try {
                    await deleteMedia(item);
                } catch (error) {
                    pushAdminAlert('danger', error instanceof Error ? error.message : 'Delete failed.');
                }
            }
        }
    });

    drawer?.querySelectorAll('[data-media-drawer-close]').forEach((button) => {
        button.addEventListener('click', closeDrawer);
    });

    drawer?.querySelector('[data-media-drawer-copy]')?.addEventListener('click', () => {
        if (state.activeItem) {
            copyText(state.activeItem.url, 'Media URL copied successfully.');
        }
    });

    drawerDelete?.addEventListener('click', async () => {
        if (!state.activeItem) {
            return;
        }

        try {
            await deleteMedia(state.activeItem);
        } catch (error) {
            pushAdminAlert('danger', error instanceof Error ? error.message : 'Delete failed.');
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeDrawer();
        }
    });

    viewButtons.forEach((button) => {
        button.addEventListener('click', () => {
            setView(button.getAttribute('data-media-view') || 'grid');
        });
    });

    setView(state.currentView);
    syncVisibleCount();

    if ((root.getAttribute('data-initial-focus') || '') === 'upload') {
        window.setTimeout(() => {
            dropzone?.scrollIntoView({ behavior: 'smooth', block: 'start' });
            dropzone?.classList.add('is-pulsing');
            window.setTimeout(() => dropzone?.classList.remove('is-pulsing'), 1600);
        }, 160);
    }
}

function bootCoverMediaModal() {
    const modal = document.querySelector('[data-cover-media-modal]');

    if (!modal) {
        return;
    }

    const coverInput = document.querySelector('[data-cover-image-input]');
    const preview = document.querySelector('[data-cover-preview]');
    const previewImage = document.querySelector('[data-cover-preview-image]');
    const uploadInput = modal.querySelector('[data-cover-media-input]');
    const dropzone = modal.querySelector('[data-cover-media-dropzone]');
    const library = modal.querySelector('[data-cover-media-library]');
    const uploadedPanel = modal.querySelector('[data-cover-media-uploaded]');
    const uploadedImage = modal.querySelector('[data-cover-media-uploaded-image]');
    const uploadedUrl = modal.querySelector('[data-cover-media-uploaded-url]');
    const uploadedTitle = modal.querySelector('[data-cover-media-uploaded-title]');
    const modalTitle = modal.querySelector('[data-cover-media-title]');
    const modalSubtitle = modal.querySelector('[data-cover-media-subtitle]');
    const openButtons = document.querySelectorAll('[data-cover-media-open]');
    const closeButtons = modal.querySelectorAll('[data-cover-media-close]');
    const browseButton = modal.querySelector('[data-cover-media-browse]');
    const useUploadedButton = modal.querySelector('[data-cover-media-use-uploaded]');
    const copyUploadedButton = modal.querySelector('[data-cover-media-copy-uploaded]');
    const baseHeaders = {
        ...(window.__DOPPAR_FRONTEND__?.headers || {}),
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json',
    };

    const state = {
        uploadedItem: null,
        context: {
            mode: 'cover',
            editor: null,
            selection: null,
            replaceImage: false,
        },
    };

    function syncModalContext() {
        const isEditor = state.context.mode === 'editor';
        const useLabel = isEditor ? 'Insert image' : 'Use image';

        if (modalTitle) {
            modalTitle.textContent = isEditor ? 'Insert article image' : 'Upload cover media';
        }

        if (modalSubtitle) {
            modalSubtitle.textContent = isEditor
                ? 'Upload or reuse media directly inside your story body'
                : 'Fast image publishing for this post';
        }

        if (useUploadedButton instanceof HTMLButtonElement) {
            useUploadedButton.textContent = isEditor ? 'Insert into article' : 'Use this image';
        }

        modal.querySelectorAll('[data-cover-media-use-existing]').forEach((button) => {
            if (button instanceof HTMLButtonElement) {
                button.textContent = useLabel;
            }
        });
    }

    function syncCoverPreview(url) {
        const value = String(url || '').trim();

        if (!coverInput) {
            return;
        }

        coverInput.value = value;

        if (!preview || !previewImage) {
            return;
        }

        if (value) {
            preview.classList.remove('is-hidden');
            previewImage.src = value;
            previewImage.alt = 'Cover image preview';
        } else {
            preview.classList.add('is-hidden');
            previewImage.removeAttribute('src');
        }
    }

    function openModal(context = {}) {
        state.context = {
            mode: context.mode === 'editor' ? 'editor' : 'cover',
            editor: context.editor || null,
            selection: context.selection || null,
            replaceImage: Boolean(context.replaceImage),
        };
        syncModalContext();
        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('admin-modal-open');
    }

    function closeModal() {
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('admin-modal-open');
    }

    function insertImageIntoEditor(item) {
        const editor = state.context.editor;

        if (!editor) {
            return false;
        }

        const selection = state.context.selection;
        const image = {
            src: item.url,
            alt: item.alt_text || item.title || 'Post image',
            title: item.title || '',
            caption: item.title || '',
        };

        try {
            let chain = editor.chain().focus();

            if (selection && Number.isInteger(selection.from) && Number.isInteger(selection.to)) {
                chain = chain.setTextSelection(selection);
            }

            if (state.context.replaceImage && editor.isActive('editorImage')) {
                return chain.updateEditorImage(image).run();
            }

            return chain.setEditorImage(image).run();
        } catch {
            if (state.context.replaceImage && editor.isActive('editorImage')) {
                return editor.chain().focus().updateEditorImage(image).run();
            }

            return editor.chain().focus().setEditorImage(image).run();
        }
    }

    function useItem(item) {
        if (!item) {
            return;
        }

        if (state.context.mode === 'editor') {
            const inserted = insertImageIntoEditor(item);

            if (inserted) {
                pushAdminAlert('success', 'Image inserted into the article body successfully.');
                closeModal();
            } else {
                pushAdminAlert('danger', 'The selected image could not be inserted into the editor.');
            }

            return;
        }

        if (!coverInput) {
            return;
        }

        syncCoverPreview(item.url);
        pushAdminAlert('success', 'Cover image URL inserted successfully.');
        closeModal();
    }

    function showUploadedItem(item) {
        state.uploadedItem = item;

        if (uploadedPanel) {
            uploadedPanel.classList.remove('is-hidden');
        }

        if (uploadedImage) {
            uploadedImage.src = item.url;
            uploadedImage.alt = item.alt_text || item.title;
        }

        if (uploadedUrl) {
            uploadedUrl.value = item.url;
        }

        if (uploadedTitle) {
            uploadedTitle.textContent = item.title || 'Ready to use';
        }
    }

    async function uploadFile(file) {
        if (!file) {
            return;
        }

        const formData = new FormData();
        formData.append('files[]', file);

        const response = await fetch(modal.getAttribute('data-upload-url') || '', {
            method: 'POST',
            headers: baseHeaders,
            body: formData,
            __loaderMessage: 'Uploading cover image…',
        });

        const payload = await response.json().catch(() => null);

        if (!response.ok) {
            throw new Error(payload?.message || 'The cover image could not be uploaded.');
        }

        const item = payload?.items?.[0];

        if (!item) {
            throw new Error('The upload succeeded but no media item was returned.');
        }

        showUploadedItem(item);
        library?.insertAdjacentHTML('afterbegin', coverMediaCardMarkup(item));
        syncModalContext();
        if (state.context.mode === 'cover') {
            syncCoverPreview(item.url);
        }
        pushAdminAlert('success', payload?.message || 'Cover image uploaded successfully.');
    }

    openButtons.forEach((button) => {
        button.addEventListener('click', () => {
            const editorRoot = button.closest('[data-rich-editor]');
            const editor = editorRoot?.__adminEditor || null;
            const selection = editor
                ? {
                    from: editor.state.selection.from,
                    to: editor.state.selection.to,
                }
                : null;

            openModal({
                mode: button.getAttribute('data-cover-media-target') === 'editor' ? 'editor' : 'cover',
                editor,
                selection,
            });
        });
    });

    closeButtons.forEach((button) => {
        button.addEventListener('click', closeModal);
    });

    browseButton?.addEventListener('click', () => {
        uploadInput?.click();
    });

    uploadInput?.addEventListener('change', async () => {
        try {
            await uploadFile(uploadInput.files?.[0]);
        } catch (error) {
            pushAdminAlert('danger', error instanceof Error ? error.message : 'Upload failed.');
        } finally {
            uploadInput.value = '';
        }
    });

    dropzone?.addEventListener('dragover', (event) => {
        event.preventDefault();
        dropzone.classList.add('is-dragover');
    });

    dropzone?.addEventListener('dragleave', () => {
        dropzone.classList.remove('is-dragover');
    });

    dropzone?.addEventListener('drop', async (event) => {
        event.preventDefault();
        dropzone.classList.remove('is-dragover');

        try {
            await uploadFile(event.dataTransfer?.files?.[0]);
        } catch (error) {
            pushAdminAlert('danger', error instanceof Error ? error.message : 'Upload failed.');
        }
    });

    useUploadedButton?.addEventListener('click', () => {
        if (state.uploadedItem) {
            useItem(state.uploadedItem);
        }
    });

    copyUploadedButton?.addEventListener('click', () => {
        if (state.uploadedItem) {
            copyText(state.uploadedItem.url, 'Cover image URL copied successfully.');
        }
    });

    coverInput?.addEventListener('input', () => {
        syncCoverPreview(coverInput.value);
    });

    library?.addEventListener('click', (event) => {
        const target = event.target instanceof Element ? event.target : null;
        const item = parseMediaPayload(target);

        if (!target || !item) {
            return;
        }

        if (target.closest('[data-cover-media-copy-existing]')) {
            copyText(item.url, 'Cover image URL copied successfully.');
            return;
        }

        if (target.closest('[data-cover-media-use-existing]') || target.closest('[data-cover-media-select]')) {
            useItem(item);
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && modal.classList.contains('is-open')) {
            closeModal();
        }
    });

    window.__adminOpenMediaPicker = (context = {}) => {
        openModal(context);
    };
}

function bootUserImagePreview() {
    const input = document.querySelector('[data-user-image-input]');
    const preview = document.querySelector('[data-user-image-preview]');
    const previewImage = document.querySelector('[data-user-image-preview-image]');

    if (!(input instanceof HTMLInputElement) || !(preview instanceof HTMLElement) || !(previewImage instanceof HTMLImageElement)) {
        return;
    }

    const initialSrc = previewImage.getAttribute('src') || '';
    let objectUrl = null;

    const syncPreview = () => {
        const file = input.files?.[0] ?? null;

        if (objectUrl) {
            URL.revokeObjectURL(objectUrl);
            objectUrl = null;
        }

        if (file instanceof File) {
            objectUrl = URL.createObjectURL(file);
            previewImage.src = objectUrl;
            preview.classList.remove('is-hidden');
            return;
        }

        if (initialSrc.trim() !== '') {
            previewImage.src = initialSrc;
            preview.classList.remove('is-hidden');
            return;
        }

        previewImage.removeAttribute('src');
        preview.classList.add('is-hidden');
    };

    input.addEventListener('change', syncPreview);
    syncPreview();
}

function bootTwoFactorProfileModal() {
    const modal = document.querySelector('[data-two-factor-modal]');

    if (!modal) {
        return;
    }

    const openButtons = document.querySelectorAll('[data-two-factor-modal-open]');
    const closeButtons = modal.querySelectorAll('[data-two-factor-modal-close]');
    const codeInput = modal.querySelector('input[name="two_factor_code"]');

    const openModal = () => {
        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('admin-modal-open');
        window.setTimeout(() => codeInput?.focus(), 30);
    };

    const closeModal = () => {
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('admin-modal-open');
    };

    openButtons.forEach((button) => {
        button.addEventListener('click', openModal);
    });

    closeButtons.forEach((button) => {
        button.addEventListener('click', closeModal);
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && modal.classList.contains('is-open')) {
            closeModal();
        }
    });

    if (modal.getAttribute('data-auto-open') === 'true') {
        openModal();
    }
}

function bootCommentReplyModal() {
    const modal = document.querySelector('[data-comment-reply-modal]');

    if (!modal) {
        return;
    }

    const form = modal.querySelector('[data-comment-reply-form]');
    const preview = modal.querySelector('[data-comment-reply-preview]');
    const textarea = modal.querySelector('textarea[name="body"]');
    const closeButtons = modal.querySelectorAll('[data-comment-reply-modal-close]');

    const openModal = (url, previewText) => {
        form.setAttribute('action', url);
        preview.textContent = previewText || '';
        textarea.value = '';
        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('admin-modal-open');
        window.setTimeout(() => textarea?.focus(), 30);
    };

    const closeModal = () => {
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('admin-modal-open');
    };

    document.querySelectorAll('[data-comment-reply-open]').forEach((button) => {
        button.addEventListener('click', () => {
            openModal(button.dataset.replyUrl, button.dataset.replyPreview);
        });
    });

    closeButtons.forEach((button) => {
        button.addEventListener('click', closeModal);
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && modal.classList.contains('is-open')) {
            closeModal();
        }
    });
}

function bootRichEditors() {
    document.querySelectorAll('[data-rich-editor]').forEach((root) => {
        const canvas = root.querySelector('[data-rich-editor-canvas]');
        const source = root.querySelector('[data-rich-editor-source]');
        const toolbar = root.querySelector('[data-rich-editor-toolbar]');
        const codeLanguage = root.querySelector('[data-editor-code-language]');
        const codeLanguageWrap = root.querySelector('[data-editor-code-language-wrap]');
        const linkPopover = root.querySelector('[data-editor-link-popover]');
        const linkInput = root.querySelector('[data-editor-link-input]');
        const linkApply = root.querySelector('[data-editor-link-apply]');
        const linkRemove = root.querySelector('[data-editor-link-remove]');
        const linkClose = root.querySelector('[data-editor-link-close]');
        const imagePopover = root.querySelector('[data-editor-image-popover]');
        const imageCaption = root.querySelector('[data-editor-image-caption]');
        const imageReplace = root.querySelector('[data-editor-image-replace]');
        const imageRemove = root.querySelector('[data-editor-image-remove]');
        const imageAlignButtons = Array.from(root.querySelectorAll('[data-editor-image-align-value]'));
        const slashMenu = root.querySelector('[data-editor-slash-menu]');

        if (!(canvas instanceof HTMLElement) || !(source instanceof HTMLTextAreaElement) || !(toolbar instanceof HTMLElement)) {
            return;
        }

        const buttons = Array.from(toolbar.querySelectorAll('[data-editor-action]'));
        const slashCommands = [
            {
                key: 'text',
                label: 'Text',
                description: 'Start a regular paragraph',
                keywords: ['paragraph', 'text'],
                run: (instance) => instance.chain().focus().setParagraph().run(),
            },
            {
                key: 'heading-2',
                label: 'Heading 2',
                description: 'Add a strong section heading',
                keywords: ['heading', 'title', 'h2'],
                run: (instance) => instance.chain().focus().toggleHeading({ level: 2 }).run(),
            },
            {
                key: 'heading-3',
                label: 'Heading 3',
                description: 'Add a smaller subsection heading',
                keywords: ['heading', 'subtitle', 'h3'],
                run: (instance) => instance.chain().focus().toggleHeading({ level: 3 }).run(),
            },
            {
                key: 'bullets',
                label: 'Bulleted list',
                description: 'Create a simple bullet list',
                keywords: ['list', 'bullets'],
                run: (instance) => instance.chain().focus().toggleBulletList().run(),
            },
            {
                key: 'numbers',
                label: 'Numbered list',
                description: 'Create an ordered list',
                keywords: ['list', 'numbered', 'ordered'],
                run: (instance) => instance.chain().focus().toggleOrderedList().run(),
            },
            {
                key: 'quote',
                label: 'Quote',
                description: 'Highlight a pull quote',
                keywords: ['quote', 'blockquote'],
                run: (instance) => instance.chain().focus().toggleBlockquote().run(),
            },
            {
                key: 'image',
                label: 'Image',
                description: 'Upload or insert media',
                keywords: ['image', 'media', 'photo'],
                run: (instance) => window.__adminOpenMediaPicker?.({
                    mode: 'editor',
                    editor: instance,
                    selection: {
                        from: instance.state.selection.from,
                        to: instance.state.selection.to,
                    },
                    replaceImage: false,
                }),
            },
            {
                key: 'code',
                label: 'Code block',
                description: 'Insert formatted code',
                keywords: ['code', 'snippet'],
                run: (instance) => instance.chain().focus().toggleCodeBlock().run(),
            },
            {
                key: 'divider',
                label: 'Divider',
                description: 'Break the story into sections',
                keywords: ['divider', 'rule'],
                run: (instance) => instance.chain().focus().setHorizontalRule().run(),
            },
        ];
        const slashState = {
            items: [],
            activeIndex: 0,
            range: null,
        };
        let isApplyingImageCaption = false;

        const editor = new Editor({
            element: canvas,
            extensions: [
                StarterKit.configure({
                    heading: {
                        levels: [2, 3],
                    },
                    codeBlock: false,
                }),
                Placeholder.configure({
                    placeholder: ({ node }) => {
                        if (node.type.name === 'heading') {
                            return 'Give this section a strong heading';
                        }

                        if (node.type.name === 'codeBlock') {
                            return 'Write or paste code here...';
                        }

                        return 'Write your story like a polished editorial piece...';
                    },
                }),
                Underline,
                Link.configure({
                    openOnClick: false,
                    autolink: true,
                    defaultProtocol: 'https',
                    HTMLAttributes: {
                        target: '_blank',
                        rel: 'noopener noreferrer',
                    },
                }),
                CodeBlockLowlight.configure({
                    lowlight,
                    defaultLanguage: 'plaintext',
                    HTMLAttributes: {
                        class: 'admin-code-block',
                    },
                }),
                EditorImage,
            ],
            content: normalizeEditorContent(source.value),
            editorProps: {
                attributes: {
                    class: 'admin-prose',
                },
                handleKeyDown: (_view, event) => {
                    if (event.key === 'Escape') {
                        closeLinkPopover();
                        closeSlashMenu();
                        return false;
                    }

                    if ((event.metaKey || event.ctrlKey) && event.key.toLowerCase() === 'k') {
                        event.preventDefault();
                        openLinkPopover();
                        return true;
                    }

                    if (!(slashMenu instanceof HTMLElement) || slashMenu.classList.contains('is-hidden')) {
                        return false;
                    }

                    if (event.key === 'ArrowDown') {
                        event.preventDefault();
                        moveSlashSelection(1);
                        return true;
                    }

                    if (event.key === 'ArrowUp') {
                        event.preventDefault();
                        moveSlashSelection(-1);
                        return true;
                    }

                    if (event.key === 'Enter') {
                        event.preventDefault();
                        runSlashCommand(slashState.activeIndex);
                        return true;
                    }

                    return false;
                },
            },
            onCreate: ({ editor: instance }) => {
                source.value = instance.getHTML();
                refreshEditorUi();
            },
            onUpdate: ({ editor: instance }) => {
                source.value = instance.getHTML();
                refreshEditorUi();
            },
            onSelectionUpdate: refreshEditorUi,
            onFocus: refreshEditorUi,
            onBlur: () => {
                window.setTimeout(() => {
                    if (!root.contains(document.activeElement)) {
                        closeLinkPopover();
                        closeSlashMenu();
                    }

                    refreshEditorUi();
                }, 0);
            },
        });

        root.__adminEditor = editor;

        function setVisibility(element, visible) {
            if (!(element instanceof HTMLElement)) {
                return;
            }

            element.classList.toggle('is-hidden', !visible);
        }

        function getCurrentImageAttributes() {
            return editor.isActive('editorImage') ? editor.getAttributes('editorImage') : null;
        }

        function closeLinkPopover() {
            setVisibility(linkPopover, false);
        }

        function openLinkPopover() {
            if (!(linkPopover instanceof HTMLElement) || !(linkInput instanceof HTMLInputElement)) {
                return;
            }

            linkInput.value = String(editor.getAttributes('link').href || '');
            setVisibility(linkPopover, true);
            window.setTimeout(() => {
                linkInput.focus();
                linkInput.select();
            }, 20);
        }

        function applyLink() {
            if (!(linkInput instanceof HTMLInputElement)) {
                return;
            }

            const href = linkInput.value.trim();

            if (!href) {
                editor.chain().focus().extendMarkRange('link').unsetLink().run();
                closeLinkPopover();
                refreshEditorUi();
                return;
            }

            if (editor.state.selection.empty && !editor.isActive('link')) {
                editor.chain().focus().insertContent(`<a href="${escapeHtml(href)}" target="_blank" rel="noopener noreferrer">${escapeHtml(href)}</a>`).run();
            } else {
                editor.chain().focus().extendMarkRange('link').setLink({
                    href,
                    target: '_blank',
                    rel: 'noopener noreferrer',
                }).run();
            }

            closeLinkPopover();
            refreshEditorUi();
        }

        function closeSlashMenu() {
            slashState.items = [];
            slashState.activeIndex = 0;
            slashState.range = null;
            setVisibility(slashMenu, false);
        }

        function getSlashQueryInfo() {
            const selection = editor.state.selection;

            if (!selection.empty) {
                return null;
            }

            const { $from, from } = selection;

            if (!$from.parent.isTextblock) {
                return null;
            }

            const textBefore = $from.parent.textContent.slice(0, $from.parentOffset);
            const match = textBefore.match(/^\/([a-z0-9-]*)$/i);

            if (!match) {
                return null;
            }

            return {
                query: (match[1] || '').toLowerCase(),
                range: {
                    from: from - match[0].length,
                    to: from,
                },
                coords: editor.view.coordsAtPos(from),
            };
        }

        function renderSlashMenu() {
            if (!(slashMenu instanceof HTMLElement)) {
                return;
            }

            const queryInfo = getSlashQueryInfo();

            if (!queryInfo) {
                closeSlashMenu();
                return;
            }

            const items = slashCommands.filter((command) => {
                if (!queryInfo.query) {
                    return true;
                }

                const haystack = [command.label, command.description, ...(command.keywords || [])]
                    .join(' ')
                    .toLowerCase();

                return haystack.includes(queryInfo.query);
            });

            if (!items.length) {
                closeSlashMenu();
                return;
            }

            slashState.items = items;
            slashState.activeIndex = Math.min(slashState.activeIndex, items.length - 1);
            slashState.range = queryInfo.range;

            const rootRect = root.getBoundingClientRect();
            const menuWidth = 280;
            const left = Math.max(16, Math.min(queryInfo.coords.left - rootRect.left, rootRect.width - menuWidth - 16));
            const top = Math.max(74, queryInfo.coords.bottom - rootRect.top + 10);

            slashMenu.style.left = `${left}px`;
            slashMenu.style.top = `${top}px`;
            slashMenu.innerHTML = items.map((command, index) => `
                <button
                    class="admin-rich-editor__slash-item ${index === slashState.activeIndex ? 'is-active' : ''}"
                    type="button"
                    data-editor-slash-command="${command.key}"
                >
                    <strong>${escapeHtml(command.label)}</strong>
                    <span>${escapeHtml(command.description)}</span>
                </button>
            `).join('');

            setVisibility(slashMenu, true);
        }

        function moveSlashSelection(direction) {
            if (!slashState.items.length) {
                return;
            }

            const count = slashState.items.length;
            slashState.activeIndex = (slashState.activeIndex + direction + count) % count;
            renderSlashMenu();
        }

        function runSlashCommand(index) {
            const command = slashState.items[index];

            if (!command || !slashState.range) {
                closeSlashMenu();
                return;
            }

            editor.chain().focus().deleteRange(slashState.range).run();
            closeSlashMenu();
            command.run(editor);
            refreshEditorUi();
        }

        function syncCodeLanguage() {
            if (!(codeLanguage instanceof HTMLSelectElement)) {
                return;
            }

            const active = editor.isActive('codeBlock');
            codeLanguage.disabled = !active;
            codeLanguage.value = active ? String(editor.getAttributes('codeBlock').language || '') : '';

            if (codeLanguageWrap instanceof HTMLElement) {
                codeLanguageWrap.classList.toggle('is-visible', active);
            }
        }

        function syncImagePopover() {
            const attrs = getCurrentImageAttributes();

            if (!attrs) {
                setVisibility(imagePopover, false);
                return;
            }

            setVisibility(imagePopover, true);

            if (imageCaption instanceof HTMLInputElement && !isApplyingImageCaption && document.activeElement !== imageCaption) {
                imageCaption.value = String(attrs.caption || '');
            }

            imageAlignButtons.forEach((button) => {
                if (!(button instanceof HTMLButtonElement)) {
                    return;
                }

                button.classList.toggle('is-active', button.getAttribute('data-editor-image-align-value') === String(attrs.align || 'center'));
            });
        }

        function refreshEditorUi() {
            updateToolbar();
            syncCodeLanguage();
            syncImagePopover();
            renderSlashMenu();
        }

        function updateToolbar() {
            buttons.forEach((button) => {
                if (!(button instanceof HTMLButtonElement)) {
                    return;
                }

                const action = button.getAttribute('data-editor-action') || '';
                const level = Number(button.getAttribute('data-editor-level') || 0);
                let isActive = false;
                let canRun = true;

                switch (action) {
                    case 'paragraph':
                        isActive = editor.isActive('paragraph');
                        canRun = editor.can().chain().focus().setParagraph().run();
                        break;
                    case 'heading':
                        isActive = level > 0 ? editor.isActive('heading', { level }) : false;
                        canRun = level > 0 ? editor.can().chain().focus().toggleHeading({ level }).run() : false;
                        break;
                    case 'bold':
                        isActive = editor.isActive('bold');
                        canRun = editor.can().chain().focus().toggleBold().run();
                        break;
                    case 'italic':
                        isActive = editor.isActive('italic');
                        canRun = editor.can().chain().focus().toggleItalic().run();
                        break;
                    case 'underline':
                        isActive = editor.isActive('underline');
                        canRun = editor.can().chain().focus().toggleUnderline().run();
                        break;
                    case 'strike':
                        isActive = editor.isActive('strike');
                        canRun = editor.can().chain().focus().toggleStrike().run();
                        break;
                    case 'link':
                        isActive = editor.isActive('link');
                        canRun = true;
                        break;
                    case 'bulletList':
                        isActive = editor.isActive('bulletList');
                        canRun = editor.can().chain().focus().toggleBulletList().run();
                        break;
                    case 'orderedList':
                        isActive = editor.isActive('orderedList');
                        canRun = editor.can().chain().focus().toggleOrderedList().run();
                        break;
                    case 'blockquote':
                        isActive = editor.isActive('blockquote');
                        canRun = editor.can().chain().focus().toggleBlockquote().run();
                        break;
                    case 'codeBlock':
                        isActive = editor.isActive('codeBlock');
                        canRun = editor.can().chain().focus().toggleCodeBlock().run();
                        break;
                    case 'image':
                        isActive = editor.isActive('editorImage');
                        canRun = true;
                        break;
                    case 'horizontalRule':
                        canRun = editor.can().chain().focus().setHorizontalRule().run();
                        break;
                    case 'undo':
                        canRun = editor.can().chain().focus().undo().run();
                        break;
                    case 'redo':
                        canRun = editor.can().chain().focus().redo().run();
                        break;
                    default:
                        canRun = true;
                }

                button.classList.toggle('is-active', isActive);
                button.disabled = !canRun;
            });
        }

        toolbar.addEventListener('click', (event) => {
            const button = event.target instanceof Element
                ? event.target.closest('[data-editor-action]')
                : null;

            if (!(button instanceof HTMLButtonElement)) {
                return;
            }

            const action = button.getAttribute('data-editor-action') || '';
            const level = Number(button.getAttribute('data-editor-level') || 0);
            const chain = editor.chain().focus();

            switch (action) {
                case 'paragraph':
                    chain.setParagraph().run();
                    break;
                case 'heading':
                    if (level > 0) {
                        chain.toggleHeading({ level }).run();
                    }
                    break;
                case 'bold':
                    chain.toggleBold().run();
                    break;
                case 'italic':
                    chain.toggleItalic().run();
                    break;
                case 'underline':
                    chain.toggleUnderline().run();
                    break;
                case 'strike':
                    chain.toggleStrike().run();
                    break;
                case 'link':
                    openLinkPopover();
                    break;
                case 'bulletList':
                    chain.toggleBulletList().run();
                    break;
                case 'orderedList':
                    chain.toggleOrderedList().run();
                    break;
                case 'blockquote':
                    chain.toggleBlockquote().run();
                    break;
                case 'codeBlock':
                    chain.toggleCodeBlock().run();
                    break;
                case 'image':
                    window.__adminOpenMediaPicker?.({
                        mode: 'editor',
                        editor,
                        selection: {
                            from: editor.state.selection.from,
                            to: editor.state.selection.to,
                        },
                        replaceImage: false,
                    });
                    break;
                case 'horizontalRule':
                    chain.setHorizontalRule().run();
                    break;
                case 'undo':
                    chain.undo().run();
                    break;
                case 'redo':
                    chain.redo().run();
                    break;
                default:
                    break;
            }

            refreshEditorUi();
        });

        codeLanguage?.addEventListener('change', () => {
            if (!(codeLanguage instanceof HTMLSelectElement) || !editor.isActive('codeBlock')) {
                return;
            }

            editor.chain().focus().updateAttributes('codeBlock', {
                language: codeLanguage.value || null,
            }).run();
            refreshEditorUi();
        });

        linkApply?.addEventListener('click', applyLink);

        linkRemove?.addEventListener('click', () => {
            editor.chain().focus().extendMarkRange('link').unsetLink().run();
            closeLinkPopover();
            refreshEditorUi();
        });

        linkClose?.addEventListener('click', closeLinkPopover);

        linkInput?.addEventListener('keydown', (event) => {
            if (event.key === 'Enter') {
                event.preventDefault();
                applyLink();
            }

            if (event.key === 'Escape') {
                event.preventDefault();
                closeLinkPopover();
            }
        });

        imageCaption?.addEventListener('input', () => {
            if (!(imageCaption instanceof HTMLInputElement) || !editor.isActive('editorImage')) {
                return;
            }

            isApplyingImageCaption = true;
            editor.chain().focus().updateEditorImage({
                caption: imageCaption.value,
            }).run();
            isApplyingImageCaption = false;
            refreshEditorUi();
        });

        imageAlignButtons.forEach((button) => {
            if (!(button instanceof HTMLButtonElement)) {
                return;
            }

            button.addEventListener('click', () => {
                if (!editor.isActive('editorImage')) {
                    return;
                }

                editor.chain().focus().updateEditorImage({
                    align: button.getAttribute('data-editor-image-align-value') || 'center',
                }).run();
                refreshEditorUi();
            });
        });

        imageReplace?.addEventListener('click', () => {
            if (!editor.isActive('editorImage')) {
                return;
            }

            window.__adminOpenMediaPicker?.({
                mode: 'editor',
                editor,
                selection: {
                    from: editor.state.selection.from,
                    to: editor.state.selection.to,
                },
                replaceImage: true,
            });
        });

        imageRemove?.addEventListener('click', () => {
            if (!editor.isActive('editorImage')) {
                return;
            }

            editor.chain().focus().deleteSelection().run();
            refreshEditorUi();
        });

        slashMenu?.addEventListener('click', (event) => {
            const button = event.target instanceof Element
                ? event.target.closest('[data-editor-slash-command]')
                : null;

            if (!(button instanceof HTMLButtonElement)) {
                return;
            }

            const key = button.getAttribute('data-editor-slash-command') || '';
            const index = slashState.items.findIndex((item) => item.key === key);
            runSlashCommand(index);
        });

        document.addEventListener('click', (event) => {
            const target = event.target;

            if (!(target instanceof window.Node)) {
                return;
            }

            if (!root.contains(target)) {
                closeLinkPopover();
                closeSlashMenu();
            }
        });

        root.closest('form')?.addEventListener('submit', () => {
            source.value = editor.getHTML();
        });
    });
}

function bootTagify() {
    document.querySelectorAll('[data-tagify]').forEach((root) => {
        const input = root.querySelector('[data-tagify-input]');
        const hidden = root.querySelector('[data-tagify-hidden]');
        const chips = root.querySelector('[data-tagify-chips]');
        const menu = root.querySelector('[data-tagify-menu]');
        const sourceId = root.getAttribute('data-tagify-source');
        const source = sourceId ? document.getElementById(sourceId) : null;

        if (!input || !hidden || !chips || !menu) {
            return;
        }

        let whitelist = [];

        if (source?.textContent) {
            try {
                whitelist = JSON.parse(source.textContent);
            } catch {
                whitelist = [];
            }
        }

        const normalizedMap = new Map();

        const state = {
            tags: [],
        };

        function syncWhitelist() {
            normalizedMap.clear();

            whitelist.forEach((tag) => {
                const label = String(tag || '').trim();

                if (!label) {
                    return;
                }

                normalizedMap.set(slugify(label), label);
            });
        }

        function syncHidden() {
            hidden.value = state.tags.join(', ');
        }

        function selectedSlugs() {
            return new Set(state.tags.map((tag) => slugify(tag)));
        }

        function renderChips() {
            chips.innerHTML = '';

            state.tags.forEach((tag) => {
                const chip = document.createElement('button');
                chip.type = 'button';
                chip.className = 'admin-tagify__chip';
                chip.innerHTML = `<span>${tag}</span><strong>&times;</strong>`;
                chip.addEventListener('click', () => {
                    state.tags = state.tags.filter((value) => slugify(value) !== slugify(tag));
                    syncHidden();
                    renderChips();
                    renderMenu();
                });
                chips.appendChild(chip);
            });
        }

        function renderMenu() {
            const query = input.value.trim().toLowerCase();
            const used = selectedSlugs();

            const results = whitelist
                .filter((tag) => !used.has(slugify(tag)))
                .filter((tag) => !query || tag.toLowerCase().includes(query))
                .slice(0, 8);

            menu.innerHTML = '';

            if (!results.length || !query) {
                menu.classList.remove('is-open');
                return;
            }

            results.forEach((tag) => {
                const option = document.createElement('button');
                option.type = 'button';
                option.className = 'admin-tagify__option';
                option.textContent = tag;
                option.addEventListener('click', () => {
                    addTag(tag);
                });
                menu.appendChild(option);
            });

            menu.classList.add('is-open');
        }

        function addTag(rawValue) {
            const value = String(rawValue || '').trim().replace(/\s+/g, ' ');

            if (!value) {
                return;
            }

            const slug = slugify(value);

            if (!slug || state.tags.some((tag) => slugify(tag) === slug)) {
                input.value = '';
                renderMenu();
                return;
            }

            const label = normalizedMap.get(slug) || value;

            state.tags.push(label);

            if (!normalizedMap.has(slug)) {
                whitelist.push(label);
                syncWhitelist();
            }

            input.value = '';
            syncHidden();
            renderChips();
            renderMenu();
        }

        function addInputValue() {
            const raw = input.value.trim();

            if (!raw) {
                return;
            }

            raw.split(',').forEach(addTag);
        }

        syncWhitelist();
        state.tags = parseTagList(hidden.value)
            .map((tag) => normalizedMap.get(slugify(tag)) || tag)
            .filter((tag, index, values) => values.findIndex((entry) => slugify(entry) === slugify(tag)) === index);

        syncHidden();
        renderChips();

        input.addEventListener('keydown', (event) => {
            if (event.key === 'Enter' || event.key === ',' || event.key === 'Tab') {
                if (input.value.trim()) {
                    event.preventDefault();
                    addInputValue();
                }
            }

            if (event.key === 'Backspace' && !input.value && state.tags.length) {
                state.tags.pop();
                syncHidden();
                renderChips();
                renderMenu();
            }
        });

        input.addEventListener('input', () => {
            if (input.value.includes(',')) {
                addInputValue();
                return;
            }

            renderMenu();
        });

        input.addEventListener('blur', () => {
            window.setTimeout(() => {
                addInputValue();
                renderMenu();
            }, 120);
        });

        document.addEventListener('click', (event) => {
            if (!root.contains(event.target)) {
                menu.classList.remove('is-open');
            }
        });

        root.__adminTagifyApi = {
            add(value) {
                input.value = String(value || '');
                addInputValue();
            },
        };
    });
}

function bootAIAssistant() {
    const root = document.querySelector('[data-ai-drawer]');
    const openButton = document.querySelector('[data-ai-drawer-open]');

    if (!root) {
        return;
    }

    const targetSelect = root.querySelector('[data-ai-target]');
    const providerSelect = root.querySelector('[data-ai-provider]');
    const modelInput = root.querySelector('[data-ai-model]');
    const instructionsInput = root.querySelector('[data-ai-instructions]');
    const temperatureInput = root.querySelector('[data-ai-temperature]');
    const temperatureLabel = root.querySelector('[data-ai-temperature-label]');
    const maxTokensInput = root.querySelector('[data-ai-max-tokens]');
    const generateButton = root.querySelector('[data-ai-generate]');
    const applyButton = root.querySelector('[data-ai-apply]');
    const outputField = root.querySelector('[data-ai-output-field]');
    const outputTextarea = root.querySelector('[data-ai-output]');
    const generateUrlInput = root.querySelector('[data-ai-generate-url]');
    const buttonLabel = root.querySelector('[data-ai-button-label]');
    const closeButtons = root.querySelectorAll('[data-ai-drawer-close]');

    const modelDefaults = {
        openai: 'gpt-3.5-turbo',
        gemini: 'gemini-2.0-flash',
        claude: 'claude-sonnet-4-5-20250929',
        openrouter: 'openrouter/free',
        selfhost: 'local-model-name',
    };

    function openDrawer() {
        root.classList.add('is-open');
        root.setAttribute('aria-hidden', 'false');
        document.body.classList.add('admin-modal-open');
    }

    function closeDrawer() {
        root.classList.remove('is-open');
        root.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('admin-modal-open');
    }

    openButton?.addEventListener('click', openDrawer);
    closeButtons.forEach((button) => button.addEventListener('click', closeDrawer));

    root?.addEventListener('click', (event) => {
        if (event.target === root) {
            closeDrawer();
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && root.classList.contains('is-open')) {
            closeDrawer();
        }
    });

    function updateModelDefault() {
        if (!modelInput) {
            return;
        }

        const provider = providerSelect?.value || 'openai';

        if (modelDefaults[provider] && (!modelInput.dataset.touched || modelInput.value === '')) {
            modelInput.value = modelDefaults[provider];
        }
    }

    function getCsrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    }

    function getRichEditor() {
        return document.querySelector('[data-rich-editor]')?.__adminEditor || null;
    }

    function getTargetElement(target) {
        const field = document.querySelector(`#${target}`);

        if (target === 'body') {
            return getRichEditor();
        }

        return field;
    }

    function getContext() {
        const context = {};
        const title = document.querySelector('#title')?.value || '';
        const excerpt = document.querySelector('#excerpt')?.value || '';
        const body = getRichEditor()?.getHTML() || document.querySelector('#body')?.value || '';
        const category = document.querySelector('#category_id option:checked')?.textContent || '';
        const tags = document.querySelector('[data-tagify-hidden]')?.value || '';
        const seoTitle = document.querySelector('#seo_title')?.value || '';
        const seoDescription = document.querySelector('#seo_description')?.value || '';

        context.title = title;
        context.excerpt = excerpt;
        context.body = body.replace(/<[^>]+>/g, ' ').replace(/\s+/g, ' ').trim();
        context.category = category;
        context.tags = tags;
        context.seo_title = seoTitle;
        context.seo_description = seoDescription;

        return context;
    }

    function setGenerating(isGenerating) {
        if (!generateButton || !buttonLabel) {
            return;
        }

        generateButton.disabled = isGenerating;
        buttonLabel.textContent = isGenerating ? 'Generating…' : 'Generate';
    }

    async function generate() {
        const target = targetSelect?.value || 'body';
        const result = outputTextarea?.value?.trim() || '';

        if (target === 'body' && getRichEditor()) {
            getRichEditor().chain().focus().setContent(result).run();
            pushAdminAlert('success', 'The generated content has been applied to the body.');
            return;
        }

        if (target === 'tags') {
            const tagify = document.querySelector('[data-tagify]')?.__adminTagifyApi;

            if (tagify) {
                tagify.add(result);
                pushAdminAlert('success', 'The generated tags have been added.');
                return;
            }
        }

        const targetField = document.querySelector(`#${target}`);

        if (targetField) {
            targetField.value = result;
            targetField.dispatchEvent(new Event('input', { bubbles: true }));
            pushAdminAlert('success', 'The generated content has been applied.');
        }
    }

    async function performGeneration() {
        if (!generateUrlInput) {
            return;
        }

        const url = generateUrlInput.value;
        const target = targetSelect?.value || 'body';

        setGenerating(true);

        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken(),
                },
                body: JSON.stringify({
                    target,
                    provider: providerSelect?.value || 'openai',
                    model: modelInput?.value || '',
                    temperature: Number(temperatureInput?.value || 7) / 10,
                    max_tokens: Number(maxTokensInput?.value || 1200),
                    instructions: instructionsInput?.value || '',
                    context: getContext(),
                }),
            });

            const payload = await response.json().catch(() => ({}));

            if (!response.ok) {
                throw new Error(payload.error || payload.message || 'AI generation failed.');
            }

            const generated = String(payload.content ?? '').trim();

            if (outputTextarea) {
                outputTextarea.value = generated;
            }

            if (outputField) {
                outputField.classList.remove('is-hidden');
            }

            if (applyButton) {
                applyButton.classList.remove('is-hidden');
            }

            if (generated === '') {
                const debugInfo = payload.debug ? JSON.stringify(payload.debug, null, 2) : 'No debug data.';
                console.error('[AI Assistant] Empty response', payload);
                pushAdminAlert('danger', 'The AI returned an empty response. Debug data has been logged to the console.');
            }
        } catch (error) {
            pushAdminAlert('danger', error instanceof Error ? error.message : 'AI generation failed.');
        } finally {
            setGenerating(false);
        }
    }

    providerSelect?.addEventListener('change', updateModelDefault);

    modelInput?.addEventListener('input', () => {
        modelInput.dataset.touched = '1';
    });

    temperatureInput?.addEventListener('input', () => {
        if (temperatureLabel) {
            temperatureLabel.textContent = (Number(temperatureInput.value) / 10).toFixed(1);
        }
    });

    generateButton?.addEventListener('click', performGeneration);

    applyButton?.addEventListener('click', generate);

    updateModelDefault();
}

function bootArticleCodeHighlighting() {
    document.querySelectorAll('.prose-article pre code').forEach((block) => {
        hljs.highlightElement(block);
    });
}

document.addEventListener('DOMContentLoaded', () => {
    bootAjaxLoader();
    bootDismissibleAlerts();
    bootDeleteConfirmations();
    bootSlugForms();
    bootTagify();
    bootRichEditors();
    bootMediaLibrary();
    bootCoverMediaModal();
    bootTwoFactorProfileModal();
    bootCommentReplyModal();
    bootUserImagePreview();
    bootSidebarToggle();
    bootSidebarGroups();
    bootDropdowns();
    bootSearchShortcut();
    bootAIAssistant();
    bootArticleCodeHighlighting();
});
