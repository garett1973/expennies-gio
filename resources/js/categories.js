import {Modal} from 'bootstrap'
import {get, post, del} from './ajax'
import DataTable from 'datatables.net'

window.addEventListener('DOMContentLoaded', function () {
    const editCategoryModal = Modal.getOrCreateInstance('#editCategoryModal');

    const table = new DataTable('#categoriesTable', {
        serverSide: true,
        ajax: '/categories/load',
        orderMulti: false,
        columns: [
            {data: 'name'},
            {data: 'createdAt'},
            {data: 'updatedAt'},
            {
                sortable: false,
                data: row => `
                    <div class="d-flex flex- justify-content-center">
                        <button type="submit" class="btn btn-outline-primary delete-category-btn" data-id="${row.id}">
                            <i class="bi bi-trash"></i>
                        </button>
                        <button type="submit" class="btn btn-outline-primary edit-category-btn ms-2" data-id="${row.id}">
                            <i class="bi bi-pencil"></i>
                        </button>
                    </div>
                `
            }
        ]
    });

    document.querySelector(('#categoriesTable')).addEventListener('click', function (event) {
        const editBtn = event.target.closest('.edit-category-btn');
        const deleteBtn = event.target.closest('.delete-category-btn');

        if (editBtn) {
            const categoryId = editBtn.getAttribute('data-id');

            get(`/categories/${categoryId}`)
                .then(response => response.json())
                .then(response => openEditCategoryModal(editCategoryModal, response));
        } else {
            const categoryId = deleteBtn.getAttribute('data-id');

            if (confirm('Are you sure?')) {
                del(`/categories/${categoryId}`)
                    .then(response => {
                        if (response.ok) {
                            table.draw();
                        }
                    });
            }
        }
    });

    document.querySelector('.save-category-btn').addEventListener('click', function (event) {
        const categoryId = event.currentTarget.getAttribute('data-id')

        post(`/categories/${categoryId}`, {
            name: editCategoryModal._element.querySelector('input[name = "name"]').value,
        }, editCategoryModal._element).then(response => {
            if (response.ok) {
                table.draw();
                editCategoryModal.hide();
            }
        });
    });
})

function openEditCategoryModal(modal, {id, name}) {
    const nameInput = modal._element.querySelector('input[name = "name"]');
    nameInput.value = name;
    modal._element.querySelector('.save-category-btn').setAttribute('data-id', id);

    setTimeout(function () {
        modal.show();
    }, 200);
}