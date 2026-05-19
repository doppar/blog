function bootCategoryGrid() {
    const root = document.getElementById('category-grid');

    if (!root) {
        return;
    }

    const $ = window.jQuery;

    if (!$ || !$.fn?.DataTable) {
        console.error('jQuery DataTable assets are missing.');
        return;
    }

    const tableElement = $('#categories-table');
    const searchInput = $('#category-search-input');
    const refreshButton = $('#category-refresh');
    const createButton = $('#category-create');
    const editButton = $('#category-edit');
    const deleteButton = $('#category-delete');
    const summary = $('#category-table-summary');
    const selectionPill = $('#category-selection-pill');
    const toast = document.getElementById('category-toast');
    const modal = document.getElementById('category-modal');
    const form = document.getElementById('category-form');
    const formErrors = document.getElementById('category-form-errors');
    const modalTitle = document.getElementById('category-modal-title');
    const submitButton = document.getElementById('category-submit-button');
    const categoryIdField = document.getElementById('category-id');
    const nameField = document.getElementById('category-name');
    const excerptField = document.getElementById('category-excerpt');
    const statusField = document.getElementById('category-status');
    const editUrlTemplate = root.dataset.editUrlTemplate;
    const updateUrlTemplate = root.dataset.updateUrlTemplate;
    const deleteUrlTemplate = root.dataset.deleteUrlTemplate;

    let selectedId = null;
    let searchTimer = null;

    const table = tableElement.DataTable({
        ajax: {
            url: root.dataset.tableUrl,
            type: 'GET',
        },
        serverSide: true,
        processing: true,
        pageLength: 6,
        lengthMenu: [6, 10, 20, 30],
        searching: true,
        ordering: true,
        info: true,
        pagingType: 'simple_numbers',
        dom: 'rt<"category-table-footer"lip>',
        order: [[5, 'desc']],
        columns: [
            {
                data: 'id',
                name: 'selector',
                orderable: false,
                searchable: false,
                className: 'category-table__selector-cell',
                render(data, type, row) {
                    if (type !== 'display') {
                        return data;
                    }

                    const checked = selectedId === row.id ? 'checked' : '';

                    return `
                        <label class="category-row-check">
                            <input type="checkbox" class="category-row-select" value="${row.id}" ${checked}>
                            <span></span>
                        </label>
                    `;
                },
            },
            {
                data: 'status_label',
                name: 'status',
                render(data, type, row) {
                    if (type !== 'display') {
                        return row.status;
                    }

                    return `
                        <span class="category-status ${row.status_class}">
                            <span class="category-status__dot"></span>
                            ${data}
                        </span>
                    `;
                },
            },
            { data: 'name', name: 'name' },
            {
                data: 'excerpt',
                name: 'excerpt',
                render(data, type) {
                    if (type !== 'display') {
                        return data;
                    }

                    return `<span class="category-table__excerpt">${data || 'No excerpt added yet.'}</span>`;
                },
            },
            { data: 'updated_at_label', name: 'updated_at' },
            { data: 'created_at_label', name: 'created_at' },
        ],
        language: {
            info: '',
            infoEmpty: '',
            infoFiltered: '',
            emptyTable: 'No categories found.',
            processing: 'Loading categories...',
            lengthMenu: 'Show _MENU_ entries',
            paginate: {
                previous: '<',
                next: '>',
            },
        },
        drawCallback() {
            syncSelectionState();
            updateSummary();
        },
        rowCallback(row, data) {
            row.dataset.categoryId = data.id;
            row.classList.toggle('is-selected', selectedId === data.id);
        },
    });

    function replaceCategoryId(template, categoryId) {
        return template.replace('__CATEGORY_ID__', String(categoryId));
    }

    function updateSummary() {
        const info = table.page.info();
        const hasRows = info.recordsDisplay > 0;
        const from = hasRows ? info.start + 1 : 0;
        const to = hasRows ? info.end : 0;

        summary.textContent = `Showing ${from}-${to} of ${info.recordsDisplay}`;
    }

    function updateSelectionPill() {
        if (!selectedId) {
            selectionPill.textContent = 'No row selected';
            editButton.prop('disabled', true);
            deleteButton.prop('disabled', true);
            return;
        }

        selectionPill.textContent = '1 row selected';
        editButton.prop('disabled', false);
        deleteButton.prop('disabled', false);
    }

    function syncSelectionState() {
        tableElement.find('tbody tr').each(function syncRow() {
            const row = table.row(this).data();
            const isSelected = row && row.id === selectedId;

            $(this).toggleClass('is-selected', isSelected);
            $(this).find('.category-row-select').prop('checked', Boolean(isSelected));
        });

        updateSelectionPill();
    }

    function showToast(message, tone = 'success') {
        toast.hidden = false;
        toast.textContent = message;
        toast.dataset.tone = tone;

        window.clearTimeout(showToast.timer);
        showToast.timer = window.setTimeout(() => {
            toast.hidden = true;
        }, 3200);
    }

    function resetForm() {
        form.reset();
        categoryIdField.value = '';
        statusField.value = '1';
        formErrors.hidden = true;
        formErrors.innerHTML = '';
    }

    function openCreateModal() {
        resetForm();
        modalTitle.textContent = 'Create category';
        submitButton.textContent = 'Save category';
        modal.hidden = false;
        document.body.classList.add('category-modal-open');
        nameField.focus();
    }

    function openEditModal(category) {
        resetForm();
        categoryIdField.value = category.id;
        nameField.value = category.name ?? '';
        excerptField.value = category.excerpt ?? '';
        statusField.value = category.status ? '1' : '0';
        modalTitle.textContent = 'Edit category';
        submitButton.textContent = 'Update category';
        modal.hidden = false;
        document.body.classList.add('category-modal-open');
        nameField.focus();
    }

    function closeModal() {
        modal.hidden = true;
        document.body.classList.remove('category-modal-open');
        resetForm();
    }

    function renderErrors(errors = {}) {
        const messages = Object.values(errors).flat();

        if (!messages.length) {
            formErrors.hidden = true;
            formErrors.innerHTML = '';
            return;
        }

        formErrors.hidden = false;
        formErrors.innerHTML = messages.map((message) => `<p>${message}</p>`).join('');
    }

    function selectRow(id) {
        selectedId = selectedId === id ? null : id;
        syncSelectionState();
    }

    function getSelectedRowData() {
        const rows = table.rows().data().toArray();
        return rows.find((row) => row.id === selectedId) ?? null;
    }

    function reloadTable(keepPage = true) {
        table.ajax.reload(null, !keepPage);
    }

    searchInput.on('input', function handleSearch() {
        const term = this.value;

        window.clearTimeout(searchTimer);
        searchTimer = window.setTimeout(() => {
            table.search(term).draw();
        }, 250);
    });

    refreshButton.on('click', function handleRefresh() {
        reloadTable(true);
    });

    createButton.on('click', function handleCreate() {
        openCreateModal();
    });

    editButton.on('click', function handleEdit() {
        if (!selectedId) {
            return;
        }

        $.ajax({
            url: replaceCategoryId(editUrlTemplate, selectedId),
            method: 'GET',
            success(response) {
                openEditModal(response.category);
            },
            error() {
                showToast('Unable to load the selected category.', 'error');
            },
        });
    });

    deleteButton.on('click', function handleDelete() {
        if (!selectedId) {
            return;
        }

        const row = getSelectedRowData();
        const label = row?.name ? `"${row.name}"` : 'this category';

        if (!window.confirm(`Delete ${label}?`)) {
            return;
        }

        $.ajax({
            url: replaceCategoryId(deleteUrlTemplate, selectedId),
            method: 'DELETE',
            headers: window.__DOPPAR_FRONTEND__?.headers ?? {},
            success(response) {
                selectedId = null;
                reloadTable(false);
                updateSelectionPill();
                showToast(response.message ?? 'Category deleted successfully.');
            },
            error() {
                showToast('Category deletion failed.', 'error');
            },
        });
    });

    tableElement.on('click', '.category-row-select', function handleRowSelection() {
        selectRow(Number(this.value));
    });

    tableElement.on('click', 'tbody tr', function handleRowClick(event) {
        if (event.target.closest('input, button, a, label')) {
            return;
        }

        const row = table.row(this).data();

        if (row) {
            selectRow(row.id);
        }
    });

    $(modal).on('click', '[data-close-modal]', function handleModalClose() {
        closeModal();
    });

    $(document).on('keydown', function handleEscape(event) {
        if (event.key === 'Escape' && !modal.hidden) {
            closeModal();
        }
    });

    $(form).on('submit', function handleSubmit(event) {
        event.preventDefault();
        renderErrors({});

        const categoryId = categoryIdField.value;
        const payload = {
            name: nameField.value.trim(),
            excerpt: excerptField.value.trim(),
            status: statusField.value,
        };

        const requestConfig = {
            url: categoryId
                ? replaceCategoryId(updateUrlTemplate, categoryId)
                : root.dataset.storeUrl,
            method: categoryId ? 'PUT' : 'POST',
            headers: window.__DOPPAR_FRONTEND__?.headers ?? {},
            data: payload,
            success(response) {
                closeModal();
                if (!categoryId) {
                    table.page('first').draw('page');
                } else {
                    reloadTable(true);
                }
                showToast(response.message ?? 'Category saved successfully.');
            },
            error(xhr) {
                if (xhr.status === 422) {
                    renderErrors(xhr.responseJSON?.errors ?? {});
                    return;
                }

                showToast('Saving the category failed.', 'error');
            },
        };

        $.ajax(requestConfig);
    });

    updateSelectionPill();
}

document.addEventListener('DOMContentLoaded', bootCategoryGrid);
