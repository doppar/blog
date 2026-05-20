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

document.addEventListener('DOMContentLoaded', () => {
    bootDismissibleAlerts();
    bootSlugForms();
    bootSidebarToggle();
    bootSidebarGroups();
    bootDropdowns();
    bootSearchShortcut();
});
