(function () {
    var data = window.TOP_SELLING_DATA;
    var statusEl = document.getElementById('reportStatus');

    // no data check
    if (!data || !data.labels || data.labels.length === 0) {
        return;
    }

    // chart.js check
    if (typeof Chart === 'undefined') {
        if (statusEl) statusEl.textContent = 'Chart failed to load.';
        return;
    }

    var ctx = document.getElementById('topSellingChart');
    if (!ctx) return;

    new Chart(ctx.getContext('2d'), {
        type: 'bar',
        data: {
            labels: data.labels,
            datasets: [{
                label: 'Units Sold',
                data: data.values,
                backgroundColor: 'rgba(107, 158, 120, 0.8)',
                borderColor: '#5a8a66',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        // show units in tooltip
                        label: function (ctx) {
                            return ' ' + ctx.parsed.y + ' units sold';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 }
                },
                x: {
                    ticks: {
                        // truncate long names
                        callback: function (val, idx) {
                            var lbl = this.getLabelForValue(idx);
                            return lbl.length > 16 ? lbl.slice(0, 14) + '…' : lbl;
                        }
                    }
                }
            }
        }
    });
}());
