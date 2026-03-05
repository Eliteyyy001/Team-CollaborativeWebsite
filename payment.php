<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment - FreshFold</title>
    <style>
        body { 
            font-family: Arial; 
            background: #f0f4f8; 
            margin:0; 
            padding:30px; 
            display:flex; 
            justify-content:center; 
            align-items:center; 
            min-height:100vh; 
        }
        .container { 
            background:white; 
            padding:30px; 
            border-radius:12px; 
            box-shadow:0 4px 20px rgba(0,0,0,0.15); 
            max-width:500px; 
            width:100%; 
        }
        h1 { text-align:center; color:#2c3e50; }
        .summary { background:#f8f9fa; padding:20px; border-radius:8px; margin:20px 0; }
        .line { display:flex; justify-content:space-between; margin:10px 0; }
        .total { font-weight:bold; font-size:1.3em; border-top:2px solid #34495e; padding-top:15px; }
        label { display:block; margin:15px 0 6px; font-weight:bold; }
        input { width:100%; padding:12px; box-sizing:border-box; border:1px solid #ccc; border-radius:6px; }
        button { 
            width:100%; 
            padding:16px; 
            background:#27ae60; 
            color:white; 
            border:none; 
            border-radius:8px; 
            font-size:18px; 
            cursor:pointer; 
            margin-top:20px; 
        }
        button:hover { background:#219653; }
    </style>
</head>
<body>

<div class="container">
    <h1>Checkout</h1>
    <p style="text-align:center; color:#666;">Member: Cristiano Ronaldo</p>

    <div class="summary">
        <div class="line"><span>Subtotal</span><span id="sub">$0.00</span></div>
        <div class="line"><span>Tax (6%)</span><span id="tax">$0.00</span></div>
        <div class="line total"><span>Grand Total</span><span id="grand">$0.00</span></div>
    </div>

    <label>Card Number</label>
    <input placeholder="1234 5678 9012 3456">

    <div style="display:flex; gap:15px;">
        <div style="flex:1;">
            <label>Expiry</label>
            <input placeholder="MM/YY">
        </div>
        <div style="flex:1;">
            <label>CVV</label>
            <input placeholder="123" maxlength="4">
        </div>
    </div>

    <label>Cardholder Name</label>
    <input placeholder="Cristiano Ronaldo">

    <button onclick="alert('Payment successful! Thank you.')">Pay Now</button>
</div>

<script>
    window.onload = () => {
        const total = parseFloat(localStorage.getItem('cartTotal') || '0');
        const tax = total * 0.06;
        document.getElementById('sub').textContent = '$' + total.toFixed(2);
        document.getElementById('tax').textContent = '$' + tax.toFixed(2);
        document.getElementById('grand').textContent = '$' + (total + tax).toFixed(2);
    };
</script>

</body>
</html>