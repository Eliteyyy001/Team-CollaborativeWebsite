

(() => {
  const cfg = (typeof window.ADMIN_METRICS === 'object' && window.ADMIN_METRICS) ? window.ADMIN_METRICS : null;
  if (!cfg) return;

  const money = (n) =>
    Number(n || 0).toLocaleString(undefined, { style: 'currency', currency: 'USD' });

  const trendLabels = Array.isArray(cfg.trendLabels) ? cfg.trendLabels : [];
  const trendTotals = Array.isArray(cfg.trendTotals) ? cfg.trendTotals : [];

  const salesTrendEl = document.getElementById('salesTrend');
  if (salesTrendEl && typeof Chart !== 'undefined') {
    new Chart(salesTrendEl, {
      type: 'line',
      data: {
        labels: trendLabels,
        datasets: [
          {
            label: 'Revenue',
            data: trendTotals,
            
          },
        ],
      },
      options: {
        plugins: {
          tooltip: { callbacks: { label: (ctx) => money(ctx.parsed.y) } },
        },
        scales: {
          y: { ticks: { callback: (v) => money(v) } },
        },
      },
    });
  }

  const inStock = Number(cfg.inStock || 0);
  const outStock = Number(cfg.outStock || 0);

  
})();

