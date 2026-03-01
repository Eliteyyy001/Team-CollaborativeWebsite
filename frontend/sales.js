const sales = [
  {
    id: 1,
    date: "2026-03-01",
    customer: "John Doe",
    items: [
      { name: "Laptop", price: 800, qty: 1 },
      { name: "Mouse", price: 20, qty: 2 }
    ]
  },
  {
    id: 2,
    date: "2026-03-02",
    customer: "Sarah Smith",
    items: [
      { name: "Phone", price: 600, qty: 1 }
    ]
  }
];

function calculateTotal(items) {
  return items.reduce((sum, item) => sum + item.price * item.qty, 0);
}

function loadSales() {
  const tbody = document.querySelector("#salesTable tbody");
  if (!tbody) return;

  let totalRevenue = 0;

  sales.forEach(sale => {
    const total = calculateTotal(sale.items);
    totalRevenue += total;

    const row = `
      <tr>
        <td>${sale.id}</td>
        <td>${sale.date}</td>
        <td>${sale.customer}</td>
        <td>$${total}</td>
        <td>
          <button onclick="viewReceipt(${sale.id})">View</button>
        </td>
      </tr>
    `;

    tbody.innerHTML += row;
  });

  document.getElementById("totalSales").textContent = totalRevenue;
  document.getElementById("totalOrders").textContent = sales.length;
}

function viewReceipt(id) {
  localStorage.setItem("receiptId", id);
  window.location.href = "receipt.html";
}

function loadReceipt() {
  const receiptId = localStorage.getItem("receiptId");
  if (!receiptId) return;

  const sale = sales.find(s => s.id == receiptId);
  const container = document.getElementById("receiptContent");

  if (!sale || !container) return;

  let html = `
    <p><strong>Order:</strong> ${sale.id}</p>
    <p><strong>Date:</strong> ${sale.date}</p>
    <p><strong>Customer:</strong> ${sale.customer}</p>
    <hr/>
  `;

  sale.items.forEach(item => {
    html += `
      <p>${item.name} x${item.qty} - $${item.price * item.qty}</p>
    `;
  });

  html += `<hr/><p><strong>Total: $${calculateTotal(sale.items)}</strong></p>`;

  container.innerHTML = html;
}

loadSales();
loadReceipt();
