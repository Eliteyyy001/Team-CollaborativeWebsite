 //Javascript logic for searching products, updating price fields, and adding items to cart.php page
    <script>
    document.addEventListener('DOMContentLoaded', function () {

      //product search
        var searchInput = document.getElementById('productSearch');
        var productRows = document.querySelectorAll('.product-table tbody tr');

        if (searchInput) {
            searchInput.addEventListener('input', function () {
                var term = searchInput.value.toLowerCase().trim();

                productRows.forEach(function (row) {
                    var nameCell = row.querySelector('td');
                    if (!nameCell) return;

                    var nameText = nameCell.textContent.toLowerCase();

                    // Show row if product name contains the search term
                    if (nameText.indexOf(term) !== -1) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        }

      //update total price field
      
        var productSelect = document.getElementById('selectedProduct');
        var quantityInput = document.getElementById('quantity');
        var priceInput = document.getElementById('price');

        function updatePrice() {
            if (!productSelect || !quantityInput || !priceInput) {
                return;
            }

            // Get selected option from dropdown
            var selectedOption = productSelect.options[productSelect.selectedIndex];

            // Get unit price from data-price attribute
            var unitPrice = 0;
            if (selectedOption && selectedOption.dataset.price) {
                unitPrice = parseFloat(selectedOption.dataset.price);
            }

            // Get quantity selected
            var qty = parseInt(quantityInput.value, 10);
            if (isNaN(qty) || qty < 1) {
                qty = 1;
                quantityInput.value = 1;
            }

            var total = unitPrice * qty;

            // Display price in proper format below
            priceInput.value = '$' + total.toFixed(2);
        }

        if (productSelect) {
            productSelect.addEventListener('change', updatePrice);
        }
        if (quantityInput) {
            quantityInput.addEventListener('input', updatePrice);
        }

        //add items to php cart that are selected from product list
        function addToCart(productId, quantity) {
            // Make sure productId is valid
            if (!productId) return;

            quantity = parseInt(quantity, 10);
            if (isNaN(quantity) || quantity < 1) {
                quantity = 1;
            }
          //fetch more than one time if quantity selected is less than 1
            var count = 0;

            function sendOneRequest() {
              
                if (count >= quantity) {
                    return;
                }

                var formData = new FormData();
                formData.append('add', productId);

                fetch('cart.php', {
                    method: 'POST',
                    body: formData,
                    credentials: 'include'
                }).then(function () {
                    count++;
                    // Send a request until a quantity that is greater than or equal to one is reached
                    sendOneRequest();
                }).catch(function (error) {
                    console.error('Error adding item to cart:', error);
                });
            }

            sendOneRequest();
        }

        // Handle add buttons in the product selection list
        var addButtons = document.querySelectorAll('.btn-add[data-product-id]');
        addButtons.forEach(function (btn) {
            btn.addEventListener('click', function () {
                var productId = btn.getAttribute('data-product-id');

                addToCart(productId, 1);
                alert('Item added to cart. View your cart!');
            });
        });

        // Handle submit sale button to add selected product and quanitity
        var submitSaleBtn = document.querySelector('.btn-submit');

        if (submitSaleBtn && productSelect && quantityInput) {
            submitSaleBtn.addEventListener('click', function () {
                var productId = productSelect.value;
                var qty = quantityInput.value;

                if (!productId) {
                    alert('Please pick a product first.');
                    return;
                }

                addToCart(productId, qty);
                updatePrice();

                alert('Sale added to cart. View in your cart!');
            });
        }

    });
    </script>
