
/* Reads window.CHART_DISPLAY_DATA from display_charts.php and builds both charts. */

(function () {
  var statusEl = document.getElementById('chartStatus');
  var payload = typeof window.CHART_DISPLAY_DATA === 'object' && window.CHART_DISPLAY_DATA
    ? window.CHART_DISPLAY_DATA
    : null;

  function money(n) {
    return Number(n || 0).toLocaleString(undefined, { style: 'currency', currency: 'USD' });
  }

  if (typeof Chart === 'undefined') {
    if (statusEl) {
      statusEl.textContent = 'Chart library did not load. Check your network or try again.';
    }
    return;
  }

  if (!payload) {
    if (statusEl) {
      statusEl.textContent = 'No chart data was provided.';
    }
    return;
  }

  var trendLabels = Array.isArray(payload.trendLabels) ? payload.trendLabels : [];
  var trendTotals = Array.isArray(payload.trendTotals) ? payload.trendTotals : [];
  var inStock = Number(payload.inStock || 0);
  var outStock = Number(payload.outStock || 0);

  var salesTrendEl = document.getElementById('salesTrend');
  if (salesTrendEl) {
    new Chart(salesTrendEl, {
      type: 'line',
      data: {
        labels: trendLabels,
        datasets: [
          {
            label: 'Revenue',
            data: trendTotals,
            borderColor: '#8b5a2b',
            backgroundColor: 'rgba(212, 165, 116, 0.25)',
            fill: true,
            tension: 0.2,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          tooltip: { callbacks: { label: function (ctx) { return money(ctx.parsed.y); } } },
        },
        scales: {
          y: { ticks: { callback: function (v) { return money(v); } } },
        },
      },
    });
  }

  var stockPieEl = document.getElementById('stockPie');
  if (stockPieEl) {
    new Chart(stockPieEl, {
      type: 'doughnut',
      data: {
        labels: ['In stock', 'Out of stock'],
        datasets: [
          {
            data: [inStock, outStock],
            backgroundColor: ['#6b9e78', '#c0392b'],
            borderColor: '#fdf6e3',
            borderWidth: 2,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { position: 'bottom' },
          tooltip: {
            callbacks: {
              label: function (ctx) {
                var label = ctx.label || '';
                var v = ctx.raw;
                var sum = inStock + outStock;
                var pct = sum > 0 ? Math.round((v / sum) * 100) : 0;
                return label + ': ' + v + ' (' + pct + '%)';
              },
            },
          },
        },
      },
    });
  }

  if (statusEl) {
    statusEl.textContent = 'Charts loaded. ' + trendLabels.length + ' day(s) of sales data.';
  }
})();
