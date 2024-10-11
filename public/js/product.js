
/**
 * DOM elements references.
 */
const productModal = $('#productModal');
const editModal = $('#editProductModal');
const deleteModal = $('#deleteProductModal');
const loginModal = $('#loginModal');
const logoutModal = $('#logoutModal');

class Product {
    constructor() {
        this.setupEventListeners();
    }
    setupEventListeners() {
        /**
         * Handles click events on the document for various buttons.
         *
         * @param {Event} event - The click event.
         */
        document.addEventListener('click', (event) => {
            if (event.target.classList.contains('create-button')) {
                this.refreshModal();
                productModal.show();
            }

            if (event.target.classList.contains('edit-button')) {
                const product = JSON.parse(event.target.dataset.product);
                this.openEditModal(product);
            }

            if (event.target.classList.contains('delete-button')) {
                const productId = event.target.dataset.id;
                this.openDeleteModal(productId);
            }

            if (event.target.classList.contains('login-button')) {
                this.openLoginModal();
            }

            if (event.target.classList.contains('logout-button')) {
                this.openLogoutModal();
            }
        });

        $(document).on('click', '.close', function() {
            // Hide all modals with the class 'modal'
            $('.modal').hide();
        });

        /**
         * Handles the submission of the product creation form.
         * 
         * @param {Event} e - The submit event triggered by the form.
         */
        $('#createProductForm').on('submit', function (e) {
            e.preventDefault();
            $.ajax({
                url: '/products/store',
                method: 'POST',
                data: $(this).serialize(),
                success: function (response) {
                    toastr.success(response);
                    productModal.hide();
                    $('#product-list-ajax-form').submit();
                },
                error: function (xhr) {
                    let errorMessage;
                    try {
                        const response = JSON.parse(xhr.responseText);
                        errorMessage = response;
                    } catch (e) {
                        errorMessage = 'Ocurrió un error inesperado.';
                    }
                    toastr.error(errorMessage);
                }
            });
        });

        /**
         * Handles the submission of the product edit form.
         * 
         * @param {Event} e - The submit event triggered by the form.
         */
        $('#editProductForm').on('submit', function (e) {
            e.preventDefault();
            $.ajax({
                url: '/products/update',
                method: 'PUT',
                data: $(this).serialize(),
                success: function (response) {
                    toastr.success(response);
                    editModal.hide();
                    $('#product-list-ajax-form').submit();
                },
                error: function (xhr) {
                    let errorMessage;
                    try {
                        const response = JSON.parse(xhr.responseText);
                        errorMessage = response;
                    } catch (e) {
                        errorMessage = 'Ocurrió un error inesperado.';
                    }
                    toastr.error(errorMessage);
                }
            });
        });

        /**
         * Handles the submission of the product delete form.
         * 
         * @param {Event} e - The submit event triggered by the form.
         */
        $('#deleteProductForm').on('submit', function (e) {
            e.preventDefault();
            const id = $('#deleteProductId').val();
            const csrfToken = $('meta[name="csrf-token"]').attr('content');

            $.ajax({
                url: '/products/delete/' + id,
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                success: function (response) {
                    toastr.success(response);
                    deleteModal.hide();
                    $('#product-list-ajax-form').submit();
                },
                error: function (xhr) {
                    let errorMessage;
                    try {
                        const response = JSON.parse(xhr.responseText);
                        errorMessage = response;
                    } catch (e) {
                        errorMessage = 'Ocurrió un error inesperado.';
                    }
                    toastr.error(errorMessage);
                }
            });
        });
    }

    /**
     * Opens the edit modal and populates it with the selected product's details.
     * 
     * @param {Object} product - The product object containing id, title, and price.
     */
    openEditModal(product) {
        $('#editProductId').val(product.id);
        $('#editTitle').val(product.title);
        $('#editPrice').val(product.price);
        editModal.show();
    }

    /**
     * Opens the delete confirmation modal for the specified product.
     * 
     * @param {number|string} productId - The ID of the product to be deleted.
     */
    openDeleteModal(productId) {
        $('#deleteProductId').val(productId);
        deleteModal.show();
    }

    /**
     * Clears the input fields in the modal for creating a new product.
     */
    refreshModal() {
        $('#newTitle').val('');
        $('#newPrice').val('');
    }

    /**
     * Opens the login modal.
     */
    openLoginModal() {
        loginModal.show();
    }

    /**
     * Opens the logout modal.
     */
    openLogoutModal() {
        logoutModal.show();
    }
}

let P = new Product();
