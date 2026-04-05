<?php
date_default_timezone_set('America/New_York');
session_start();
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header("Location: login.php");
    exit;
}
$cashier_name = $_SESSION['cashier_name'] ?? 'Jane Smith';
$products = [
    ['name' => 'Box Legend Folding Board', 'price' => 11.99, 'stock' => 2],
    ['name' => 'Downey Fabric Softner', 'price' => 7.99, 'stock' => 199],
    ['name' => 'Downey Sensitive Detergent', 'price' => 8.89, 'stock' => 119],
    ['name' => 'FlipFold', 'price' => 12.99, 'stock' => 79],
    ['name' => 'Tide Dryer Sheets', 'price' => 4.99, 'stock' => 298],
    ['name' => 'Tide Stain Fighting Detergent', 'price' => 6.69, 'stock' => 100],
    ['name' => 'Wool Dryer Balls', 'price' => 7.99, 'stock' => 149],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>FreshFold POS</title>
  <link rel="stylesheet" href="pos.css">
  <style>
    #sale-cart-body tr { border-bottom: 1px solid #ddd; }
    #sale-cart-body td { padding: 8px; }
    .remove-btn {
      background: #cc0000;
      color: white;
      border: none;
      padding: 4px 8px;
      border-radius: 4px;
      cursor: pointer;
    }
    /* Status Banners - Made clearly different */
    .stock-banner {
      padding: 6px 12px;
      border-radius: 4px;
      font-size: 0.85rem;
      font-weight: bold;
      display: inline-block;
      text-align: center;
      min-width: 110px;
    }
    .stock-banner.out {
      background: #f8d7da;
      color: #721c24;
      border: 1px solid #f5c6cb;
    }
    .stock-banner.low {
      background: #fff3cd;
      color: #856404;
      border: 1px solid #ffeaa7;
    }
    .stock-banner.in {
      background: #d4edda;
      color: #155724;
      border: 1px solid #c3e6cb;
    }
  </style>
</head>
<body style="background:white;">
<div style="background:white; color:#000; padding:6px 12px; font-size:0.9rem; font-weight:bold; border-bottom:1px solid #e0e0e0; text-align:left;">
  Connected successfully — <span id="live-time"></span>
</div>
<div class="top-nav" style="background:#3a3a3a; color:white; padding:8px 16px; display:flex; justify-content:space-between; align-items:center;">
  <div>FreshFold POS</div>
  <div class="menu" style="display:flex; gap:10px;">
    <a href="#" style="color:white; padding:6px 12px; background:#555; text-decoration:none;">Dashboard</a>
    <a href="#" class="active" style="color:white; padding:6px 12px; background:#ffa500; font-weight:bold; text-decoration:none;">Make Sale</a>
    <a href="#" style="color:white; padding:6px 12px; background:#555; text-decoration:none;">Products</a>
    <a href="#" style="color:white; padding:6px 12px; background:#555; text-decoration:none;">Reports</a>
    <a href="#" style="color:white; padding:6px 12px; background:#555; text-decoration:none;">Sales</a>
    <a href="audit_logs.php" style="color:white; padding:6px 12px; background:#555; text-decoration:none;">Audit Logs</a>
  </div>
  <div style="display:flex; gap:15px; align-items:center;">
    User: <?= htmlspecialchars($cashier_name) ?>
    <a href="logout.php" style="color:white;">Logout</a>
  </div>
</div>
<div class="container" style="display:flex; gap:10px; padding:12px; height:calc(100vh - 55px);">
  <!-- Products Panel -->
  <div class="panel" style="flex:1; background:#faefdd; border:1px solid:#c0b070; border-radius:6px; padding:12px;">
    <div style="background:#d6b98a; padding:8px; font-weight:bold; border:1px solid:#c0b070; margin-bottom:10px;">
      Products
    </div>
    <input type="text" placeholder="Search POS"
           style="width:100%; padding:6px; margin-bottom:8px; border:1px solid:#aaa; border-radius:4px;background:#daecff;">
    <div style="display:flex; align-items:center; gap:30px; margin-bottom:10px; height:55px;">
      <div style="display:flex; align-items:center; gap:6px;">
        <label style="font-weight:bold; position:relative; top:-3px;">Filter:</label>
        <select style="padding:6px; border:1px solid:#aaa; border-radius:4px; background:#daecff; width:150px; font-weight:bold; font-size:0.95rem;">
          <option>All Categories</option>
        </select>
      </div>
      <div style="display:flex; align-items:center; gap:6px;">
        <label style="font-weight:bold; position:relative; top:-3px;">Sort:</label>
        <select style="padding:6px; border:1px solid:#aaa; border-radius:4px; background:#daecff; width:80px; font-weight:bold; font-size:0.95rem;">
          <option>Price</option>
        </select>
      </div>
    </div>
    <table style="width:100%; border-collapse:collapse; font-size:0.92rem;">
      <thead>
        <tr style="background:#d6b98a;">
          <th style="padding:8px; text-align:left;">Product Name</th>
          <th style="padding:8px; text-align:left;">Price</th>
          <th style="padding:8px; text-align:left;">Stock</th>
          <th style="padding:8px; text-align:left;">Status</th>
          <th></th>
          <th></th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($products as $p): ?>
        <tr style="background:#fffaf2; border-bottom:1px solid:#e0d0a0;">
          <td style="padding:6px 8px; font-weight:bold; border-right:1px solid:#e0d0a0;">
              <?= htmlspecialchars($p['name']) ?>
          </td>
          <td style="padding:6px 8px; font-weight:bold; border-right:1px solid:#e0d0a0; border-left:1px solid:#e0d0a0;">
              $<?= number_format($p['price'], 2) ?>
          </td>
          <td style="padding:6px 8px; font-weight:bold; border-right:1px solid:#e0d0a0;">
              <?= $p['stock'] ?>
          </td>
          <td style="padding:6px 8px; font-weight:bold; border-right:1px solid:#e0d0a0;">
              <?php 
              if ((int)$p['stock'] <= 0): ?>
                  <div class="stock-banner out">OUT OF STOCK</div>
              <?php elseif ((int)$p['stock'] < 5): ?>
                  <div class="stock-banner low">LOW STOCK</div>
              <?php else: ?>
                  <div class="stock-banner in">IN STOCK</div>
              <?php endif; ?>
          </td>
          <td style="padding:6px 8px;">
              <button onclick="addToCart('<?= htmlspecialchars($p['name']) ?>', <?= $p['price'] ?>, <?= $p['stock'] ?>, 1)"
                  style="background:#6B8E23; color:white; border:none; padding:4px 10px; border-radius:4px; cursor:pointer;">
                  Add
              </button>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  
  <!-- Make Sale Panel -->
  <div class="panel" style="flex:1; background:#faefdd; border:1px solid:#c0b070; border-radius:6px; padding:12px;">
    <div style="background:#d6b98a; padding:8px; font-weight:bold; border:1px solid:#c0b070; margin-bottom:10px;">Make Sale</div>
    <label style="display:block; margin:10px 0 5px; font-weight:bold;">Product:</label>
    <select id="product-select" style="width:100%; max-width:595px; padding:6px; border:1px solid:#999; border-radius:4px; font-weight:bold; background:#fef9f3;">
      <option value="">Select</option>
      <?php foreach ($products as $p): ?>
        <option value="<?= htmlspecialchars($p['name']) ?>" data-stock="<?= $p['stock'] ?>" data-price="<?= $p['price'] ?>">
          <?= htmlspecialchars($p['name']) ?>
        </option>
      <?php endforeach; ?>
    </select>
    <label style="display:block; margin:10px 0 4px; font-weight:bold;">Quantity:</label>
    <input id="qty-box" type="number" value="1" min="1" style="width:100%; max-width:595px; padding:6px; border:1px solid:#999; border-radius:4px; font-weight:bold; background:#fef9f3;">
    <label style="display:block; margin:10px 0 4px; font-weight:bold;">Price:</label>
    <input id="price-box" type="text" value="$0.00" readonly style="width:100%; max-width:595px; padding:6px; border:1px solid:#999; border-radius:4px; background:#fef9f3; font-weight:bold;">
    <p style="color:#555; font-size:0.88rem; margin:4px 0 12px; font-style:italic;">
      Helper: "Enter quantity ≥ 1"
    </p>
    
    
    
    
    
    
    <!-- Add to Cart and Complete Sale Buttons with proper spacing -->
    <div style="display: flex; gap: 12px; margin-top: 10px;">
        <button onclick="addToCartFromInputs()" 
                style="background:#6B8E23; color:white; padding:12px; border:none; border-radius:6px; cursor:pointer; flex: 1; font-weight:bold;">
            Add to Cart
        </button>
        
        <button onclick="completeSale()" 
                style="background:#2e7d32; color:white; padding:12px; border:none; border-radius:6px; cursor:pointer; flex: 1; font-weight:bold;">
            Complete Sale
        </button>
    </div>



    
    
    
    
    <div style="margin-top:16px;">
      <strong>Cart items</strong>
      <table style="width:100%; border-collapse:collapse; margin:12px 0; font-size:0.9rem;">
        <thead>
          <tr style="background:#d6b98a;">
            <th style="padding:8px; text-align:left;">Item</th>
            <th style="padding:8px; text-align:center;">Qty</th>
            <th style="padding:8px; text-align:right;">Price</th>
            <th style="padding:8px; text-align:center;"></th>
          </tr>
        </thead>
        <tbody id="sale-cart-body">
          <tr class="empty-row">
            <td colspan="4" style="text-align:center; color:#777; padding:20px;">
              Your cart is empty.
            </td>
          </tr>
        </tbody>
      </table>
      <div style="background:#d9ecff; padding:8px; font-weight:bold; border:1px solid:#c0b070; margin-top:10px; margin-bottom:10px; display:flex; justify-content:space-between;">
        <span>Total Price</span>
        <span id="cart-total" style="color:#2e7d32;">$0.00</span>
      </div>
      <button id="cancel-btn" style="background:#e53935; color:white; border:none; padding:10px; width:25%; border-radius:4px; cursor:pointer; font-size:1rem; margin-top:14px;" onclick="clearCart()">
        Cancel
      </button>
    </div>
  </div>

  <!-- Receipt Panel -->
  <div class="panel" style="flex:1; background:#faefdd; border:1px solid:#c0b070; border-radius:6px; padding:12px;">
    <div style="background:#d6b98a; padding:8px; font-weight:bold; border:1px solid:#c0b070; margin-bottom:10px; text-align:left;">
      Receipt
    </div>
    <div style="background:#fffaf2; border:1px solid:#c0b070; padding:24px 20px; font-size:1rem; border-radius:4px; color:#333; min-height:520px;">
      <div style="margin-bottom:18px;">
        <strong>Product:</strong><br>
        <div style="border-bottom:1px solid #888; min-height:10px; padding:4px 0;"></div>
      </div>
      <div style="margin-bottom:18px;">
        <strong>Quantity:</strong><br>
        <div style="border-bottom:1px solid #888; min-height:10px; padding:4px 0;"></div>
      </div>
      <div style="margin-bottom:18px;">
        <strong>Price:</strong><br>
        <div style="border-bottom:1px solid #888; min-height:10px; padding:4px 0;"></div>
      </div>
      <div style="margin-bottom:18px;">
        <strong>Total:</strong><br>
        <div style="border-bottom:1px solid #888; min-height:10px; padding:4px 0;"></div>
      </div>
      <div style="margin-bottom:18px; display:flex; justify-content:space-between; align-items:baseline;">
        <strong>Date:</strong>
        <div style="border-bottom:1px solid #888; padding:4px 0; text-align:right; flex-grow:1; min-height:30px;">
          <strong><?= date('m/d/Y') ?></strong>
        </div>
      </div>
      <div style="display:flex; gap:12px; margin:30px 0 20px 0;">
        <button style="flex:1; background:#6B8E23; color:white; padding:10px; border:none; border-radius:6px; cursor:pointer; font-weight:bold;">
          Print
        </button>
        <button style="flex:1; background:#f57c00; color:white; padding:10px; border:none; border-radius:6px; cursor:pointer; font-weight:bold;">
          Download
        </button>
      </div>
      <a href="#" style="display:block; text-align:center; color:black; font-weight:bold; text-decoration:none; margin-top:12px;">
        Back to Dashboard
      </a>
    </div>
  </div>
</div>





<script>
// LIVE CLOCK
function updateClock() {
    const now = new Date();
    document.getElementById("live-time").textContent = now.toLocaleString();
}
setInterval(updateClock, 1000);
updateClock();

// UPDATE PRICE 
document.getElementById("product-select").addEventListener("change", function () {
    let price = this.options[this.selectedIndex].getAttribute("data-price");
    if (!price) {
        document.getElementById("price-box").value = "$0.00";
        return;
    }
    document.getElementById("price-box").value = "$" + parseFloat(price).toFixed(2);
});

// ADD ITEM FROM PRODUCT LIST
function addToCart(name, price, stock, qty = 1)
{
    if (qty > stock) {
        alert("Only " + stock + " left in stock.");
        return;
    }
    let cartBody = document.getElementById("sale-cart-body");
    let emptyRow = cartBody.querySelector(".empty-row");
    if (emptyRow) emptyRow.remove();
    let existing = cartBody.querySelector(`tr[data-name="${name}"]`);
    if (existing) {
        let qtyCell = existing.querySelector(".qty");
        let totalCell = existing.querySelector(".total");
        let newQty = parseInt(qtyCell.textContent) + qty;
        if (newQty > stock) {
            alert("Only " + stock + " left in stock.");
            return;
        }
        qtyCell.textContent = newQty;
        totalCell.textContent = "$" + (newQty * price).toFixed(2);
        updateCartTotal();
        return;
    }
    let row = document.createElement("tr");
    row.setAttribute("data-name", name);
    row.innerHTML = `
        <td>${name}</td>
        <td class="qty" style="text-align:center;">${qty}</td>
        <td class="total" style="text-align:right;">$${(qty * price).toFixed(2)}</td>
        <td style="text-align:center;">
            <button class="remove-btn" onclick="removeItem('${name}')">X</button>
        </td>
    `;
    cartBody.appendChild(row);
    updateCartTotal();
}

// Submit Sale with Warning + Override Prompt (from previous version)
// UPDATED Submit Sale - Step 1 (Preparation for backend)
function addToCartFromInputs() {
    const select = document.getElementById("product-select");
    const qtyInput = document.getElementById("qty-box");
    const priceInput = document.getElementById("price-box");

    if (select.selectedIndex === 0) {
        alert("Please select a product.");
        return;
    }

    const name = select.options[select.selectedIndex].text.trim();
    const qty = parseInt(qtyInput.value) || 1;
    const price = parseFloat(priceInput.value.replace("$", "")) || 0;
    const stock = parseInt(select.options[select.selectedIndex].getAttribute("data-stock"));

    if (qty < 1) {
        alert("Quantity must be at least 1.");
        return;
    }

    // Warning + Override Prompt
    let message = `Add ${qty} × "${name}" to cart?`;
    if (stock <= 0) {
        message = `⚠️ WARNING: "${name}" is OUT OF STOCK (only ${stock} left).\n\nDo you want to override?`;
    } else if (qty > stock) {
        message = `⚠️ WARNING: Only ${stock} "${name}" left in stock.\n\nYou requested ${qty}.\n\nOverride?`;
    } else if (stock < 5) {
        message = `⚠️ Low Stock Alert: Only ${stock} "${name}" remaining.\n\nAdd ${qty} anyway?`;
    }

    if (confirm(message)) {
        addToCart(name, price, stock, qty);   // Add to visual cart first
        // TODO: Later we will send this to backend to save sale
    }

    // Reset inputs
    select.selectedIndex = 0;
    qtyInput.value = "1";
    priceInput.value = "$0.00";
}

function removeItem(name) {
    const cartBody = document.getElementById("sale-cart-body");
    const row = cartBody.querySelector(`tr[data-name="${name}"]`);
    if (row) row.remove();
    if (!cartBody.querySelector("tr")) {
        cartBody.innerHTML = `
            <tr class="empty-row">
                <td colspan="4" style="text-align:center; color:#777; padding:20px;">
                    Your cart is empty.
                </td>
            </tr>`;
    }
    updateCartTotal();
}

function clearCart() {
    const cartBody = document.getElementById("sale-cart-body");
    cartBody.innerHTML = `
        <tr class="empty-row">
            <td colspan="4" style="text-align:center; color:#777; padding:20px;">
                Your cart is empty.
            </td>
        </tr>`;
    updateCartTotal();
}

function updateCartTotal() {
    let total = 0;
    document.querySelectorAll("#sale-cart-body .total").forEach(cell => {
        total += parseFloat(cell.textContent.replace("$", ""));
    });
    document.getElementById("cart-total").textContent = "$" + total.toFixed(2);
}












// STEP 2: Complete Sale - Final Confirmation
function completeSale() {
    const cartBody = document.getElementById("sale-cart-body");
    const rows = cartBody.querySelectorAll("tr:not(.empty-row)");

    if (rows.length === 0) {
        alert("Your cart is empty. Please add items first.");
        return;
    }

    let total = parseFloat(document.getElementById("cart-total").textContent.replace("$", "")) || 0;

    const confirmMsg = `Complete this sale?\n\nTotal Amount: $${total.toFixed(2)}\nNumber of items: ${rows.length}\n\nThis will save the sale and update stock.`;

    if (confirm(confirmMsg)) {
        // For now, just show success (we will connect to backend in Step 3)
        alert("✅ Sale Completed Successfully!\n\nTotal: $" + total.toFixed(2));

        // Clear the cart after sale
        clearCart();

        // Optional: Refresh the products table to show updated stock (later)
        // location.reload();
    }
}















function completeSale() {
    const cartBody = document.getElementById("sale-cart-body");
    const rows = cartBody.querySelectorAll("tr:not(.empty-row)");

    if (rows.length === 0) {
        alert("Your cart is empty. Please add items first.");
        return;
    }

    let items = [];
    let total = 0;

    rows.forEach(row => {
        const name = row.getAttribute("data-name");
        const qty = parseInt(row.querySelector(".qty").textContent);
        const price = parseFloat(row.querySelector(".total").textContent.replace("$", ""));

        items.push({ name: name, qty: qty, price: price });
        total += price;
    });

    // 🔥 UPDATE RECEIPT IMMEDIATELY (THIS IS WHAT YOU ASKED FOR)
    showReceipt(items, total);

    // 🔥 DO NOT WAIT UNTIL AFTER SALE TO SHOW RECEIPT
    // Now ask to complete the sale
    if (confirm(`Complete this sale?\n\nTotal: $${total.toFixed(2)}\nItems: ${items.length}`)) {

        fetch('complete_sale.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ items: items })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(`✅ Sale Completed Successfully!\nTotal: $${data.total.toFixed(2)}`);

                // Clear cart AFTER sale
                clearCart();
            } else {
                alert("❌ Error: " + data.message);
            }
        })
        .catch(error => {
            console.error(error);
            alert("Connection error. Please make sure complete_sale.php exists and has no errors.");
        });
    }
}





// Exact Receipt Format You Requested - Fixed

function showReceipt(items, total) {
    const receiptContent = document.querySelector(".panel:last-child div[style*='background:#fffaf2']");
    
    if (!receiptContent) return;

    let productHTML = '';
    let quantityHTML = '';
    let priceHTML = '';

    items.forEach(item => {
        const line = `${item.qty} × ${item.name}`;
        
        productHTML += line + '<br>';
        quantityHTML += line + '<br>';
        priceHTML += `$${item.price.toFixed(2)}<br>`;
    });

    receiptContent.innerHTML = `
        <div style="margin-bottom:18px;">
            <strong>Product:</strong><br>
            <div style="border-bottom:1px solid #888; min-height:40px; padding:4px 0; text-align:right; width:100%; display:block;">
                ${productHTML}
            </div>
        </div>

        <div style="margin-bottom:18px;">
            <strong>Quantity:</strong><br>
            <div style="border-bottom:1px solid #888; min-height:40px; padding:4px 0; text-align:right; width:100%; display:block;">
                ${quantityHTML}
            </div>
        </div>

        <div style="margin-bottom:18px;">
            <strong>Price:</strong><br>
            <div style="border-bottom:1px solid #888; min-height:40px; padding:4px 0; text-align:right; width:100%; display:block;">
                ${priceHTML}
            </div>
        </div>

        <div style="margin-bottom:18px;">
            <strong>Total:</strong><br>
            <div style="border-bottom:1px solid #888; min-height:40px; padding:4px 0; font-weight:bold; color:#2e7d32; text-align:right; width:100%; display:block;">
                $${total.toFixed(2)}
            </div>
        </div>

        <div style="margin-bottom:25px;">
            <strong>Date:</strong><br>
            <div style="border-bottom:1px solid #888; padding:4px 0; text-align:right; width:100%; display:block; min-height:30px;">
                <strong>4/5/2026</strong>
            </div>
        </div>

        <div style="display:flex; gap:12px; margin:30px 0 20px 0;">
            <button onclick="window.print()" 
                    style="flex:1; background:#6B8E23; color:white; padding:10px; border:none; border-radius:6px; cursor:pointer; font-weight:bold;">
                Print
            </button>
            <button onclick="alert('Download feature coming soon!')" 
                    style="flex:1; background:#f57c00; color:white; padding:10px; border:none; border-radius:6px; cursor:pointer; font-weight:bold;">
                Download
            </button>
        </div>

        <a href="#" style="display:block; text-align:center; color:black; font-weight:bold; text-decoration:none; margin-top:12px;">
            Back to Dashboard
        </a>
    `;
}








</script>
</body>
</html>
