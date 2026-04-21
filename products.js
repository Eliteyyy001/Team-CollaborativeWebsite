(() => {
    const form = document.getElementById('productForm');
    if (!form) {
        return;
    }

    const heading = document.getElementById('productFormHeading');
    const actionInput = document.getElementById('formAction');
    const prodIDInput = document.getElementById('prodID');
    const prodNameInput = document.getElementById('prodName');
    const catIDInput = document.getElementById('catID');
    const prodCostInput = document.getElementById('prodCost');
    const quantityStockedInput = document.getElementById('quantityStocked');
    const prodDiscountInput = document.getElementById('prodDiscount');
    const submitButton = document.getElementById('formSubmitButton');
    const cancelButton = document.getElementById('cancelEditButton');

    const editButtons = Array.from(document.querySelectorAll('.edit-product-btn'));
    const deleteForms = Array.from(document.querySelectorAll('.delete-product-form'));

    function resetToAddMode() {
        actionInput.value = 'add';
        prodIDInput.value = '';
        heading.textContent = 'Add Product';
        submitButton.textContent = 'Add Product';
        cancelButton.style.display = 'none';
        form.reset();
        prodDiscountInput.value = '0';
    }

    function enableEditMode(payload) {
        actionInput.value = 'update';
        prodIDInput.value = payload.prodID;
        prodNameInput.value = payload.prodName;
        catIDInput.value = payload.catID;
        prodCostInput.value = payload.prodCost;
        quantityStockedInput.value = payload.quantityStocked;
        prodDiscountInput.value = payload.prodDiscount;
        heading.textContent = `Edit Product #${payload.prodID}`;
        submitButton.textContent = 'Save Changes';
        cancelButton.style.display = 'inline-block';
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    editButtons.forEach((btn) => {
        btn.addEventListener('click', () => {
            enableEditMode({
                prodID: btn.dataset.prodId || '',
                prodName: btn.dataset.prodName || '',
                catID: btn.dataset.catId || '',
                prodCost: btn.dataset.prodCost || '0.00',
                quantityStocked: btn.dataset.quantityStocked || '0',
                prodDiscount: btn.dataset.prodDiscount || '0.00'
            });
        });
    });

    deleteForms.forEach((deleteForm) => {
        deleteForm.addEventListener('submit', (event) => {
            const deleteButton = deleteForm.querySelector('button[type="submit"]');
            const productName = deleteButton ? (deleteButton.dataset.prodName || 'this product') : 'this product';
            const ok = window.confirm(`Delete ${productName}? This cannot be undone.`);
            if (!ok) {
                event.preventDefault();
            }
        });
    });

    cancelButton.addEventListener('click', () => {
        resetToAddMode();
    });
})();
