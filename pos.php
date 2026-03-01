<?php
// pos.php - FreshFold POS - Make Sale issue #55
?>

<!DOCTYPE html>
<html>
<head>
    <title>FreshFold POS - Make Sale</title>
    <style>
        html, body {
            height: 100vh;
            margin: 0;
            overflow: hidden;
            font-family: Arial;
            background-image: url('image.png');
            background-size: cover;
            background-position: center;
        }
        .top-nav {
            background: #2c3e50;
            color: white;
            padding: 12px 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 16px;
            font-weight: bold;
            border-bottom: 2px solid #34495e;
        }
        .top-nav .logo { font-size: 22px; }
        .top-nav .menu { display: flex; gap: 30px; }
        .top-nav .menu a {
            color: #ecf0f1;
            text-decoration: none;
            padding: 8px 14px;
            border-radius: 4px;
        }
        .top-nav .menu a:hover,
        .top-nav .menu a.active { background: #34495e; }
        .user-section {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .basket-btn {
            background: #27ae60;
            color: white;
            padding: 10px 20px;
            border-radius: 30px;
            cursor: pointer;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.2s;
        }
        .basket-btn:hover { background: #219653; transform: scale(1.05); }
        .basket-icon { font-size: 20px; }
        .basket-count {
            background: #e74c3c;
            color: white;
            font-size: 14px;
            padding: 5px 10px;
            border-radius: 15px;
            min-width: 24px;
            text-align: center;
            transition: all 0.3s;
        }
        .basket-count.updated {
            animation: pulse 0.6s ease-in-out;
            transform: scale(1.15);
        }
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.3); }
            100% { transform: scale(1); }
        }
        .logout-btn {
            background: #e74c3c;
            color: white;
            padding: 10px 20px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.2s;
        }
        .logout-btn:hover { background: #c0392b; transform: scale(1.05); }
        .container {
            display: flex;
            height: calc(100vh - 60px);
            padding: 15px;
            gap: 20px;
        }
        .left-side { width: 55%; height: 100%; }
        .right-side {
            width: 40%;
            height: 100%;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .box {
            background: rgba(255,255,255,0.25);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            border: 1px solid rgba(255,255,255,0.3);
            padding: 15px;
        }
        .left-side .box { height: 100%; overflow-y: auto; }
        h2 { text-align: center; color: black; margin-top: 0; }
        table { width: 100%; border-collapse: collapse; }
        th, td {
            padding: 10px;
            border-bottom: 1px solid rgba(0,0,0,0.3);
            color: black;
            font-size: 16px;
            text-align: left;
        }
        .input, .value-box {
            width: 100%;
            height: 45px;
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #ccc;
            margin-top: 5px;
            font-size: 16px;
            box-sizing: border-box;
        }
        .qty-input { width: 60px; text-align: center; padding: 5px; }
        .delete-btn {
            background: #e74c3c;
            color: white;
            border: none;
            padding: 6px 10px;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            color: white;
            font-size: 18px;
            margin-top: 10px;
        }
        .submit-btn { background: #2ecc71; }
        .cancel-btn { background: #e74c3c; }
        .cart-box {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            position: relative;
        }
        .cart-table-wrapper {
            flex: 1;
            overflow-y: auto;
            margin-bottom: 0;
        }
        .cart-total {
            background: rgba(46, 204, 113, 0.85);
            color: white;
            font-size: 22px;
            font-weight: bold;
            padding: 16px 20px;
            text-align: right;
            border-top: 3px solid #27ae60;
            box-shadow: 0 -4px 10px rgba(0,0,0,0.2);
            position: sticky;
            bottom: 0;
            z-index: 10;
            margin: 0 -15px -15px -15px;
        }
        .cart-total span { font-size: 24px; }
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.6);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }
        .modal-content {
            background: white;
            padding: 30px;
            border-radius: 12px;
            width: 90%;
            max-width: 700px;
            max-height: 85vh;
            overflow-y: auto;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            position: relative;
        }
        .close-btn {
            position: absolute;
            top: 15px; right: 20px;
            font-size: 32px;
            cursor: pointer;
            color: #aaa;
        }
        .close-btn:hover { color: #333; }
        .cart-modal-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .cart-modal-table th, .cart-modal-table td {
            padding: 12px;
            border-bottom: 1px solid #eee;
            text-align: left;
        }
        .cart-modal-table th { background: #f8f9fa; }
        .summary {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
        }
        .summary-line {
            display: flex;
            justify-content: space-between;
            margin: 10px 0;
            font-size: 16px;
        }
        .summary-total {
            font-weight: bold;
            font-size: 20px;
            border-top: 2px solid #27ae60;
            padding-top: 15px;
            margin-top: 15px;
        }
        .pay-btn {
            background: #27ae60;
            color: white;
            border: none;
            padding: 16px;
            font-size: 18px;
            width: 100%;
            border-radius: 8px;
            cursor: pointer;
            margin-top: 20px;
        }
        .pay-btn:hover { background: #219653; }
        /* Payment Options */
        .payment-options {
            margin: 20px 0;
        }
        .payment-group {
            margin-bottom: 25px;
        }
        .payment-group h3 {
            margin: 0 0 12px 0;
            color: #2c3e50;
            font-size: 18px;
            border-bottom: 1px solid #eee;
            padding-bottom: 8px;
        }
        .payment-option {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            cursor: pointer;
            border: 2px solid transparent;
            transition: all 0.2s;
            margin-bottom: 10px;
        }
        .payment-option:hover,
        .payment-option.active {
            border-color: #27ae60;
            background: #e8f5e9;
        }
        .card-form {
            display: none;
            margin-top: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        .card-form.show { display: block; }
        /* Thank You Modal */
        .thank-you-modal {
            text-align: center;
            padding: 40px 20px;
        }
        .thank-you-icon {
            font-size: 80px;
            color: #27ae60;
            margin-bottom: 20px;
        }
        .thank-you-text {
            font-size: 24px;
            color: #2c3e50;
            margin-bottom: 15px;
        }
        .thank-you-subtext {
            font-size: 18px;
            color: #555;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
<div class="top-nav">
    <div class="logo">FreshFold POS</div>
    <div class="menu">
        <a href="#">Dashboard</a>
        <a href="#" class="active">Make Sale</a>
        <a href="#">Products</a>
        <a href="#">Reports</a>
    </div>
    <div class="user-section">
        <span>Member: Hamza Yalouli</span>
        <div class="basket-btn" onclick="showBasketModal()">
            <span class="basket-icon">🛒</span> Basket
            <span class="basket-count" id="cartItemCount">0</span>
        </div>
        <a href="index.php" class="logout-btn" onclick="logout()">Logout</a>
    </div>
</div>

<div class="container">
    <div class="left-side">
        <div class="box">
            <h2>All Products</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Product</th>
                        <th>Unit Size</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Status</th>
                        <th>Add</th>
                    </tr>
                </thead>
                <tbody id="product-body">
                    <tr>
                        <td>91001</td>
                        <td>Luxury Surface Cleaner</td>
                        <td>24 oz</td>
                        <td>Premium</td>
                        <td>$9.99</td>
                        <td>18</td>
                        <td class="text-center font-medium">
                            <span class="px-3 py-1 rounded-full text-sm font-semibold bg-green-100 text-green-800 border border-green-300">
                                In Stock (18)
                            </span>
                        </td>
                        <td><button onclick="addFromProducts('Luxury Surface Cleaner',9.99)">Add</button></td>
                    </tr>
                    <tr>
                        <td>91002</td>
                        <td>Premium Scented Cleaner</td>
                        <td>20 oz</td>
                        <td>Premium</td>
                        <td>$8.49</td>
                        <td>12</td>
                        <td class="text-center font-medium">
                            <span class="px-3 py-1 rounded-full text-sm font-semibold bg-green-100 text-green-800 border border-green-300">
                                In Stock (12)
                            </span>
                        </td>
                        <td><button onclick="addFromProducts('Premium Scented Cleaner',8.49)">Add</button></td>
                    </tr>
                    <tr>
                        <td>91003</td>
                        <td>Deep Cleaning Solution</td>
                        <td>32 oz</td>
                        <td>Deep Clean</td>
                        <td>$10.99</td>
                        <td>7</td>
                        <td class="text-center font-medium">
                            <span class="px-3 py-1 rounded-full text-sm font-semibold bg-yellow-100 text-yellow-800 border border-yellow-300">
                                Low Stock (7 left)
                            </span>
                        </td>
                        <td><button onclick="addFromProducts('Deep Cleaning Solution',10.99)">Add</button></td>
                    </tr>
                    <tr>
                        <td>91004</td>
                        <td>Anti-Bacterial Home Cleaner</td>
                        <td>28 oz</td>
                        <td>Disinfectant</td>
                        <td>$7.99</td>
                        <td>0</td>
                        <td class="text-center font-medium">
                            <span class="px-3 py-1 rounded-full text-sm font-semibold bg-red-100 text-red-800 border border-red-300">
                                Out of Stock
                            </span>
                        </td>
                        <td><button disabled>Add</button></td>
                    </tr>
                    <tr>
                        <td>91005</td>
                        <td>Long-Lasting Disinfectant</td>
                        <td>30 oz</td>
                        <td>Disinfectant</td>
                        <td>$11.49</td>
                        <td>9</td>
                        <td class="text-center font-medium">
                            <span class="px-3 py-1 rounded-full text-sm font-semibold bg-green-100 text-green-800 border border-green-300">
                                In Stock (9)
                            </span>
                        </td>
                        <td><button onclick="addFromProducts('Long-Lasting Disinfectant',11.49)">Add</button></td>
                    </tr>
                    <tr>
                        <td>91006</td>
                        <td>All-in-One Multi-Surface Cleaner</td>
                        <td>22 oz</td>
                        <td>Multi-Surface</td>
                        <td>$6.99</td>
                        <td>20</td>
                        <td class="text-center font-medium">
                            <span class="px-3 py-1 rounded-full text-sm font-semibold bg-green-100 text-green-800 border border-green-300">
                                In Stock (20)
                            </span>
                        </td>
                        <td><button onclick="addFromProducts('All-in-One Multi-Surface Cleaner',6.99)">Add</button></td>
                    </tr>
                    <tr>
                        <td>91007</td>
                        <td>Stain Removal Specialist</td>
                        <td>18 oz</td>
                        <td>Laundry</td>
                        <td>$5.49</td>
                        <td>14</td>
                        <td class="text-center font-medium">
                            <span class="px-3 py-1 rounded-full text-sm font-semibold bg-green-100 text-green-800 border border-green-300">
                                In Stock (14)
                            </span>
                        </td>
                        <td><button onclick="addFromProducts('Stain Removal Specialist',5.49)">Add</button></td>
                    </tr>
                    <tr>
                        <td>91008</td>
                        <td>Odor Eliminator Product</td>
                        <td>16 oz</td>
                        <td>Air Care</td>
                        <td>$4.99</td>
                        <td>11</td>
                        <td class="text-center font-medium">
                            <span class="px-3 py-1 rounded-full text-sm font-semibold bg-green-100 text-green-800 border border-green-300">
                                In Stock (11)
                            </span>
                        </td>
                        <td><button onclick="addFromProducts('Odor Eliminator Product',4.99)">Add</button></td>
                    </tr>
                    <tr>
                        <td>91009</td>
                        <td>Hypoallergenic Cleaning Product</td>
                        <td>20 oz</td>
                        <td>Allergy-Safe</td>
                        <td>$7.49</td>
                        <td>6</td>
                        <td class="text-center font-medium">
                            <span class="px-3 py-1 rounded-full text-sm font-semibold bg-yellow-100 text-yellow-800 border border-yellow-300">
                                Low Stock (6 left)
                            </span>
                        </td>
                        <td><button onclick="addFromProducts('Hypoallergenic Cleaning Product',7.49)">Add</button></td>
                    </tr>
                    <tr>
                        <td>91010</td>
                        <td>Baby-Safe Home Cleaner</td>
                        <td>24 oz</td>
                        <td>Baby-Safe</td>
                        <td>$8.99</td>
                        <td>3</td>
                        <td class="text-center font-medium">
                            <span class="px-3 py-1 rounded-full text-sm font-semibold bg-yellow-100 text-yellow-800 border border-yellow-300">
                                Low Stock (3 left)
                            </span>
                        </td>
                        <td><button onclick="addFromProducts('Baby-Safe Home Cleaner',8.99)">Add</button></td>
                    </tr>
                    <tr>
                        <td>91011</td>
                        <td>Pet-Safe Cleaning Product</td>
                        <td>22 oz</td>
                        <td>Pet-Safe</td>
                        <td>$7.99</td>
                        <td>0</td>
                        <td class="text-center font-medium">
                            <span class="px-3 py-1 rounded-full text-sm font-semibold bg-red-100 text-red-800 border border-red-300">
                                Out of Stock
                            </span>
                        </td>
                        <td><button disabled>Add</button></td>
                    </tr>
                    <tr>
                        <td>91012</td>
                        <td>Eco-Friendly Home Cleaner</td>
                        <td>26 oz</td>
                        <td>Eco-Friendly</td>
                        <td>$9.49</td>
                        <td>16</td>
                        <td class="text-center font-medium">
                            <span class="px-3 py-1 rounded-full text-sm font-semibold bg-green-100 text-green-800 border border-green-300">
                                In Stock (16)
                            </span>
                        </td>
                        <td><button onclick="addFromProducts('Eco-Friendly Home Cleaner',9.49)">Add</button></td>
                    </tr>
                    <tr>
                        <td>91013</td>
                        <td>Natural / Plant-Based Cleaner</td>
                        <td>28 oz</td>
                        <td>Eco-Friendly</td>
                        <td>$8.99</td>
                        <td>10</td>
                        <td class="text-center font-medium">
                            <span class="px-3 py-1 rounded-full text-sm font-semibold bg-green-100 text-green-800 border border-green-300">
                                In Stock (10)
                            </span>
                        </td>
                        <td><button onclick="addFromProducts('Natural / Plant-Based Cleaner',8.99)">Add</button></td>
                    </tr>
                    <tr>
                        <td>91014</td>
                        <td>Floor Deep-Care Product</td>
                        <td>1 Gallon</td>
                        <td>Floor Care</td>
                        <td>$12.99</td>
                        <td>5</td>
                        <td class="text-center font-medium">
                            <span class="px-3 py-1 rounded-full text-sm font-semibold bg-yellow-100 text-yellow-800 border border-yellow-300">
                                Low Stock (5 left)
                            </span>
                        </td>
                        <td><button onclick="addFromProducts('Floor Deep-Care Product',12.99)">Add</button></td>
                    </tr>
                    <tr>
                        <td>91015</td>
                        <td>Kitchen Degreasing Specialist</td>
                        <td>18 oz</td>
                        <td>Kitchen</td>
                        <td>$6.49</td>
                        <td>13</td>
                        <td class="text-center font-medium">
                            <span class="px-3 py-1 rounded-full text-sm font-semibold bg-green-100 text-green-800 border border-green-300">
                                In Stock (13)
                            </span>
                        </td>
                        <td><button onclick="addFromProducts('Kitchen Degreasing Specialist',6.49)">Add</button></td>
                    </tr>
                    <tr>
                        <td>91016</td>
                        <td>Bathroom Power Cleaner</td>
                        <td>32 oz</td>
                        <td>Bathroom</td>
                        <td>$7.49</td>
                        <td>4</td>
                        <td class="text-center font-medium">
                            <span class="px-3 py-1 rounded-full text-sm font-semibold bg-yellow-100 text-yellow-800 border border-yellow-300">
                                Low Stock (4 left)
                            </span>
                        </td>
                        <td><button onclick="addFromProducts('Bathroom Power Cleaner',7.49)">Add</button></td>
                    </tr>
                    <tr>
                        <td>91017</td>
                        <td>Glass & Mirror Shine Product</td>
                        <td>20 oz</td>
                        <td>Glass Care</td>
                        <td>$5.99</td>
                        <td>17</td>
                        <td class="text-center font-medium">
                            <span class="px-3 py-1 rounded-full text-sm font-semibold bg-green-100 text-green-800 border border-green-300">
                                In Stock (17)
                            </span>
                        </td>
                        <td><button onclick="addFromProducts('Glass & Mirror Shine Product',5.99)">Add</button></td>
                    </tr>
                    <tr>
                        <td>91018</td>
                        <td>Furniture Protection & Polish</td>
                        <td>16 oz</td>
                        <td>Furniture Care</td>
                        <td>$8.49</td>
                        <td>9</td>
                        <td class="text-center font-medium">
                            <span class="px-3 py-1 rounded-full text-sm font-semibold bg-green-100 text-green-800 border border-green-300">
                                In Stock (9)
                            </span>
                        </td>
                        <td><button onclick="addFromProducts('Furniture Protection & Polish',8.49)">Add</button></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="right-side">
        <div class="box">
            <h2>Make Sale</h2>
            <label>Product:</label>
            <select id="productSelect" class="input" onchange="updatePrice()"></select>
            <label>Quantity:</label>
            <input type="number" id="qtyInput" value="1" min="1" class="input">
            <label>Price:</label>
            <input type="text" id="priceDisplay" class="value-box" value="$0.00" readonly>
            <button class="btn submit-btn" onclick="addToCart()">Add to Cart</button>
            <button class="btn cancel-btn">Cancel</button>
            <button class="btn exit-btn">Exit</button>
        </div>
        <div class="box cart-box">
            <h2>Cart</h2>
            <div class="cart-table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Qty</th>
                            <th>Unit Price</th>
                            <th>Total</th>
                            <th>Delete</th>
                        </tr>
                    </thead>
                    <tbody id="cart-summary"></tbody>
                </table>
            </div>
            <div class="cart-total">
                Total: <span id="cartTotal">$0.00</span>
            </div>
        </div>
    </div>
</div>

<!-- Basket Modal -->
<div id="basketModal" class="modal">
    <div class="modal-content">
        <span class="close-btn" onclick="closeBasketModal()">×</span>
        <h2>Your Basket</h2>
        <table class="cart-modal-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Qty</th>
                    <th>Unit Price</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody id="modal-cart-body"></tbody>
        </table>
        <div class="summary">
            <div class="summary-line">
                <span>Subtotal:</span>
                <span id="modal-subtotal">$0.00</span>
            </div>
            <div class="summary-line">
                <span>Tax (7%):</span>
                <span id="modal-tax">$0.00</span>
            </div>
            <div class="summary-line summary-total">
                <span>Total Amount:</span>
                <span id="modal-grand">$0.00</span>
            </div>
        </div>
        <button class="pay-btn" onclick="closeBasketModal(); showPaymentOptions();">
            Proceed to Pay
        </button>
    </div>
</div>

<!-- Payment Options Modal -->
<div id="paymentModal" class="modal">
    <div class="modal-content">
        <span class="close-btn" onclick="closePaymentModal()">×</span>
        <h2>Select Payment Method</h2>
        <div class="payment-options">
            <div class="payment-group">
                <h3>Credit / Debit Cards</h3>
                <div class="payment-option" onclick="selectPayment('visa')">Visa</div>
                <div class="payment-option" onclick="selectPayment('mastercard')">Mastercard</div>
                <div class="payment-option" onclick="selectPayment('amex')">American Express</div>
                <div class="payment-option" onclick="selectPayment('discover')">Discover</div>
            </div>
            <div class="payment-group">
                <h3>Digital Wallets</h3>
                <div class="payment-option" onclick="selectPayment('paypal')">PayPal</div>
                <div class="payment-option" onclick="selectPayment('applepay')">Apple Pay</div>
                <div class="payment-option" onclick="selectPayment('googlepay')">Google Pay</div>
                <div class="payment-option" onclick="selectPayment('samsungpay')">Samsung Pay</div>
            </div>
        </div>
        <div id="cardForm" class="card-form">
            <label>Card Number (16 digits)</label>
            <input type="text" id="cardNumber" placeholder="1234567890123456" maxlength="16">
            <div style="display:flex; gap:15px; margin-top:15px;">
                <div style="flex:1;">
                    <label>Expiry Date</label>
                    <input type="text" placeholder="MM/YY">
                </div>
                <div style="flex:1;">
                    <label>CVV</label>
                    <input type="text" placeholder="123" maxlength="4">
                </div>
            </div>
            <label style="margin-top:15px;">Cardholder Name</label>
            <input type="text" id="cardName" placeholder="Name on Card">
        </div>
        <button class="pay-btn" onclick="processPayment()">Pay Now</button>
    </div>
</div>

<!-- Thank You Modal -->
<div id="thankYouModal" class="modal">
    <div class="modal-content thank-you-modal">
        <span class="close-btn" onclick="closeThankYouModal()">×</span>
        <div class="thank-you-icon">🎉</div>
        <div class="thank-you-text">Thank you for your purchase!</div>
        <div class="thank-you-subtext">You will receive your order in 1-2 days.</div>
        <button class="pay-btn" onclick="closeThankYouModal()">Close</button>
    </div>
</div>

<script>
let cart = [];
let selectedPayment = '';
const taxRate = 0.06; // 6% tax

function loadProductsIntoDropdown() {
    const dropdown = document.getElementById("productSelect");
    dropdown.innerHTML = "<option value=''>Select Product</option>";
    document.querySelectorAll("#product-body tr").forEach(row => {
        const cells = row.cells;
        const name = cells[1].textContent;
        const price = cells[4].textContent.replace("$", "");
        dropdown.innerHTML += `<option value="${name}" data-price="${price}">${name}</option>`;
    });
}

window.onload = function() {
    loadProductsIntoDropdown();
    updateBasketCount();
};

function updatePrice() {
    const select = document.getElementById("productSelect");
    if (select.selectedIndex <= 0) {
        document.getElementById("priceDisplay").value = "$0.00";
        return;
    }
    const price = parseFloat(select.options[select.selectedIndex].dataset.price);
    document.getElementById("priceDisplay").value = "$" + price.toFixed(2);
}

function addToCart() {
    const select = document.getElementById("productSelect");
    const name = select.value;
    if (!name) { alert("Select product"); return; }
    const price = parseFloat(select.options[select.selectedIndex].dataset.price);
    const qty = parseInt(document.getElementById("qtyInput").value) || 1;
    let item = cart.find(i => i.name === name);
    if (item) item.qty += qty;
    else cart.push({ name, qty, price });
    renderCart();
    updateBasketCount();
}

function addFromProducts(name, price) {
    // Find stock from the table row
    let stockQty = 0;
    document.querySelectorAll("#product-body tr").forEach(row => {
        if (row.cells[1].textContent === name) {
            const statusText = row.cells[6].textContent.trim();  // status column
            const match = statusText.match(/\d+/);
            stockQty = match ? parseInt(match[0]) : 0;
            if (statusText.includes("Out of Stock")) stockQty = 0;
        }
    });

    const lowThreshold = 10;

    if (stockQty <= lowThreshold) {
        let message = (stockQty <= 0)
            ? "OUT OF STOCK! Override and add to cart anyway?"
            : `Low stock (${stockQty} left). Override and add anyway?`;

        if (!confirm(message)) {
            alert("Cancelled - not added.");
            return;
        }
    }

    // Proceed
    const select = document.getElementById("productSelect");
    select.value = name;
    document.getElementById("qtyInput").value = 1;
    addToCart();
}

function updateQty(index, value) {
    const qty = parseInt(value);
    if (qty > 0) {
        cart[index].qty = qty;
        renderCart();
        updateBasketCount();
    }
}

function deleteItem(index) {
    cart.splice(index, 1);
    renderCart();
    updateBasketCount();
}

function renderCart() {
    const tbody = document.getElementById("cart-summary");
    tbody.innerHTML = "";
    cart.forEach((item, i) => {
        const total = (item.qty * item.price).toFixed(2);
        tbody.innerHTML += `
            <tr>
                <td>${item.name}</td>
                <td><input type="number" class="qty-input" value="${item.qty}" min="1" onchange="updateQty(${i}, this.value)"></td>
                <td>$${item.price.toFixed(2)}</td>
                <td>$${total}</td>
                <td><button class="delete-btn" onclick="deleteItem(${i})">Delete</button></td>
            </tr>
        `;
    });
    const grandTotal = cart.reduce((sum, item) => sum + item.qty * item.price, 0);
    document.getElementById("cartTotal").innerText = "$" + grandTotal.toFixed(2);
    localStorage.setItem('cartTotal', grandTotal.toFixed(2));
}

function updateBasketCount() {
    const countElement = document.getElementById("cartItemCount");
    const totalItems = cart.reduce((sum, item) => sum + item.qty, 0);
    countElement.textContent = totalItems;
    countElement.classList.remove("updated");
    void countElement.offsetWidth;
    countElement.classList.add("updated");
}

function showBasketModal() {
    if (cart.length === 0) {
        alert("Your basket is empty.");
        return;
    }
    const tbody = document.getElementById("modal-cart-body");
    tbody.innerHTML = "";
    cart.forEach(item => {
        const subtotal = (item.qty * item.price).toFixed(2);
        tbody.innerHTML += `
            <tr>
                <td>${item.name}</td>
                <td>${item.qty}</td>
                <td>$${item.price.toFixed(2)}</td>
                <td>$${subtotal}</td>
            </tr>
        `;
    });
    const subtotal = cart.reduce((sum, item) => sum + item.qty * item.price, 0);
    const tax = subtotal * taxRate;
    const grand = subtotal + tax;
    document.getElementById("modal-subtotal").textContent = "$" + subtotal.toFixed(2);
    document.getElementById("modal-tax").textContent = "$" + tax.toFixed(2);
    document.getElementById("modal-grand").textContent = "$" + grand.toFixed(2);
    document.getElementById("basketModal").style.display = "flex";
}

function closeBasketModal() {
    document.getElementById("basketModal").style.display = "none";
}

function showPaymentOptions() {
    closeBasketModal();
    if (cart.length === 0) {
        alert("Your cart is empty.");
        return;
    }
    document.getElementById("paymentModal").style.display = "flex";
    selectedPayment = '';
    document.getElementById("cardForm").classList.remove("show");
}

function closePaymentModal() {
    document.getElementById("paymentModal").style.display = "none";
}

function selectPayment(method) {
    selectedPayment = method;
    document.querySelectorAll('.payment-option').forEach(opt => opt.classList.remove('active'));
    event.target.classList.add('active');
    if (['visa', 'mastercard', 'amex', 'discover'].includes(method)) {
        document.getElementById("cardForm").classList.add("show");
    } else {
        document.getElementById("cardForm").classList.remove("show");
    }
}

function processPayment() {
    if (!selectedPayment) {
        alert("Please select a payment method first.");
        return;
    }
    if (['visa', 'mastercard', 'amex', 'discover'].includes(selectedPayment)) {
        const cardNum = document.querySelector("#cardNumber").value.trim();
        const cardName = document.querySelector("#cardName").value.trim();
        if (!cardNum || cardNum.length !== 16 || isNaN(cardNum)) {
            alert("You missed one or two digits! Please enter a full 16-digit card number (no spaces).");
            return;
        }
        if (!cardName) {
            alert("Please enter the cardholder name.");
            return;
        }
    }
    // Success → show thank you
    document.getElementById("paymentModal").style.display = "none";
    document.getElementById("thankYouModal").style.display = "flex";
    // Clear cart
    cart = [];
    renderCart();
    updateBasketCount();
}

function closeThankYouModal() {
    document.getElementById("thankYouModal").style.display = "none";
}

function logout() {
    if (confirm("Are you sure you want to logout?")) {
        cart = [];
        localStorage.removeItem('cartTotal');
        window.location.href = "index.php";
    }
}
</script>
</body>
</html>
