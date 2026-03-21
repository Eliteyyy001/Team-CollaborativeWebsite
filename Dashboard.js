let chart;

async function loadDashboard() {
  const startDate = document.getElementById("startDate").value;
  const endDate = document.getElementById("endDate").value;

  let url = "http://localhost:5000/api/dashboard";

  if (startDate || endDate) {
    url += `?startDate=${startDate}&endDate=${endDate}`;
  }

  const res = await fetch(url, {
    headers: {
      Authorization: "Bearer YOUR_TOKEN_HERE"
    }
  });

  const data = await res.json();

  const metrics = data.metrics;

  // ✅ Update metrics
  document.getElementById("revenue").textContent = metrics.totalRevenue;
  document.getElementById("orders").textContent = metrics.totalOrders;
  document.getElementById("products").textContent = metrics.totalItems;
  document.getElementById("lowStock").textContent = metrics.lowStock;
  document.getElementById("outOfStock").textContent = metrics.outOfStock;

  // ✅ Build chart data
  const labels = data.sales.map(s => s.date);
  const totals = data.sales.map(s => s.total);

  // ✅ Destroy old chart before reloading
  if (chart) {
    chart.destroy();
  }

  const ctx = document.getElementById("salesChart");

  chart = new Chart(ctx, {
    type: "line",
    data: {
      labels: labels,
      datasets: [{
        label: "Sales",
        data: totals
      }]
    }
  });
}

// Load on page start
loadDashboard();
