const SIDEBAR_STORAGE_KEY = 'editorial-desk-sidebar-groups';

function slugify(value) {
    return (value || '')
        .toString()
        .trim()
        .toLowerCase()
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-+|-+$/g, '') || 'entry';
}

function bootDismissibleAlerts() {
    document.querySelectorAll('[data-dismiss-alert]').forEach((button) => {
        button.addEventListener('click', () => {
            button.closest('[data-dismissible]')?.remove();
        });
    });
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

function parseTagList(value) {
    if (!value) {
        return [];
    }

    return value
        .split(',')
        .map((item) => item.trim())
        .filter(Boolean);
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
    });
}

document.addEventListener('DOMContentLoaded', () => {
    bootDismissibleAlerts();
    bootSlugForms();
    bootTagify();
    bootSidebarToggle();
    bootSidebarGroups();
    bootDropdowns();
    bootSearchShortcut();
});
