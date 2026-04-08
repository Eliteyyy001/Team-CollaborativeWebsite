/*Javascript logic for searching products, updating price fields, and adding items to cart.php page */
(function() {
    'use strict';

    var productSearch = document.getElementById('productSearch');
    var categoryFilter = document.getElementById('categoryFilter');
    var sortBy = document.getElementById('sortBy');
    var selectedProduct = document.getElementById('selectedProduct');
    var quantityInput = document.getElementById('quantity');
    var priceInput = document.getElementById('price');

    //update total price field
    function updatePrice() {
        if (!selectedProduct || !quantityInput || !priceInput) return;
        var opt = selectedProduct.options[selectedProduct.selectedIndex];
        var unitPrice = (opt && opt.getAttribute('data-price')) ? parseFloat(opt.getAttribute('data-price')) : 0;
        var qty = parseInt(quantityInput.value, 10) || 1;
        if (qty < 1) {
            qty = 1;
            quantityInput.value = 1;
        }
        priceInput.value = '$' + (unitPrice * qty).toFixed(2);
    }
    if (selectedProduct) selectedProduct.addEventListener('change', updatePrice);
    if (quantityInput) quantityInput.addEventListener('input', updatePrice);
    updatePrice();

    // sort and filter rows
    function filterAndSortRows() {
        var term = (productSearch && productSearch.value) ? productSearch.value.toLowerCase().trim() : '';
        var cat = (categoryFilter && categoryFilter.value) ? categoryFilter.value : '';
        var sort = (sortBy && sortBy.value) ? sortBy.value : 'price';

        var rows = document.querySelectorAll('.product-table tbody tr[data-product-id]');
        var rowArray = [];
        for (var r = 0; r < rows.length; r++) {
            var row = rows[r];
            var name = (row.getAttribute('data-name') || '').toLowerCase();
            var category = row.getAttribute('data-category') || '';
            var show = (term === '' || name.indexOf(term) !== -1) && (cat === '' || category === cat);
            row.style.display = show ? '' : 'none';
            if (show) rowArray.push(row);
        }

        rowArray.sort(function(a, b) {
            if (sort === 'name') return (a.getAttribute('data-name') || '').localeCompare(b.getAttribute('data-name') || '');
            if (sort === 'price') return parseFloat(a.getAttribute('data-price') || 0) - parseFloat(b.getAttribute('data-price') || 0);
            if (sort === 'stock') return parseInt(a.getAttribute('data-stock') || 0, 10) - parseInt(b.getAttribute('data-stock') || 0, 10);
            return 0;
        });

        var tbody = document.querySelector('.product-table tbody');
        if (tbody) {
            for (var s = 0; s < rowArray.length; s++) tbody.appendChild(rowArray[s]);
        }
    }
    if (productSearch) productSearch.addEventListener('input', filterAndSortRows);
    if (categoryFilter) categoryFilter.addEventListener('change', filterAndSortRows);
    if (sortBy) sortBy.addEventListener('change', filterAndSortRows);
})();
