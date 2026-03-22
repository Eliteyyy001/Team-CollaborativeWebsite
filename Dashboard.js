let chart;

const API_BASE = "http://localhost:5000/api/dashboard";

// Format helpers
function formatCurrency(value) {
  return Number(value).toLocaleString("en-US", {
    style: "currency",
    currency: "USD"
  });
}

function formatNumber(value) {
  return Number(value).toLocaleString();
}

// Build URL cleanly
function buildUrl(startDate, endDate) {
  const url = new URL(API_BASE);

  if (startDate) url.searchParams.append("startDate", startDate);
  if (endDate) url.searchParams.append("endDate", endDate);

  return url;
}

// Update UI metrics
function updateMetrics(metrics) {
  document.getElementById("revenue").textContent = formatCurrency(metrics.totalRevenue);
  document.getElementById("orders").textContent = formatNumber(metrics.totalOrders);
  document.getElementById("products").textContent = formatNumber(metrics.totalItems);
  document.getElementById("lowStock").textContent = formatNumber(metrics.lowStock);
  document.getElementById("outOfStock").textContent = formatNumber(metrics.outOfStock);
}

// Render chart
function renderChart(labels, totals) {
  const ctx = document.getElementById("salesChart");

  if (chart) chart.destroy();

  chart = new Chart(ctx, {
    type: "line",
    data: {
      labels,
      datasets: [{
        label: "Sales",
        data: totals,
        tension: 0.3 // smoother line
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { display: true }
      }
    }
  });
}

// Main function
async function loadDashboard() {
  try {
    document.body.style.cursor = "wait";

    const startDate = document.getElementById("startDate").value;
    const endDate = document.getElementById("endDate").value;

    const url = buildUrl(startDate, endDate);

    const res = await fetch(url, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem("token") || ""}`
      }
    });

    if (!res.ok) {
      throw new Error(`Request failed: ${res.status}`);
    }

    const data = await res.json();

    updateMetrics(data.metrics);

    const labels = data.sales.map(s => s.date);
    const totals = data.sales.map(s => s.total);

    renderChart(labels, totals);

  } catch (err) {
    console.error("Dashboard error:", err);
    alert("Failed to load dashboard data.");
  } finally {
    document.body.style.cursor = "default";
  }
}

// Load on page start
document.addEventListener("DOMContentLoaded", loadDashboard);
