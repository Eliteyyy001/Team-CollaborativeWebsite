
/* Builds sales trend + stock charts from window.PRINT_REPORT_CHART_DATA */

(function () {
  var payload =
    typeof window.PRINT_REPORT_CHART_DATA === 'object' && window.PRINT_REPORT_CHART_DATA
      ? window.PRINT_REPORT_CHART_DATA
      : null;

  function money(n) {
    return Number(n || 0).toLocaleString(undefined, { style: 'currency', currency: 'USD' });
  }

  if (typeof Chart === 'undefined') {
    return;
  }

  if (!payload) {
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
        animation: false,
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
        animation: false,
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

  var btn = document.getElementById('btnPrintReport');
  var autoPrint = /(?:\?|&)autoprint=1(?:&|$)/.test(window.location.search);
  var hasTriggeredAutoPrint = false;

  function printReport() {
    window.print();
  }

  if (btn) {
    btn.addEventListener('click', function () {
      printReport();
    });
  }

  if (autoPrint && !hasTriggeredAutoPrint) {
    hasTriggeredAutoPrint = true;
    window.setTimeout(printReport, 350);
  }
})();
