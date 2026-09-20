/**
 * Dashboard monthly revenue chart — loaded only when #monthlyChart exists.
 */
export function initMonthlyChart() {
    const canvas = document.getElementById('monthlyChart');
    const dataEl = document.getElementById('monthly-chart-data');
    if (!canvas || !dataEl) {
        return;
    }

    let data = [];
    try {
        data = JSON.parse(dataEl.textContent || '[]');
    } catch {
        return;
    }

    import('chart.js/auto').then(({ default: Chart }) => {
        new Chart(canvas, {
            type: 'bar',
            data: {
                labels: data.map((d) => d.month),
                datasets: [
                    {
                        label: 'الإيرادات',
                        data: data.map((d) => d.contracts),
                        backgroundColor: 'rgba(16, 185, 129, 0.7)',
                        borderColor: '#10b981',
                        borderWidth: 1.5,
                        borderRadius: 6,
                    },
                    {
                        label: 'المحصل',
                        data: data.map((d) => d.payments),
                        backgroundColor: 'rgba(59, 130, 246, 0.7)',
                        borderColor: '#3b82f6',
                        borderWidth: 1.5,
                        borderRadius: 6,
                    },
                    {
                        label: 'المصروفات',
                        data: data.map((d) => d.expenses),
                        backgroundColor: 'rgba(239, 68, 68, 0.7)',
                        borderColor: '#ef4444',
                        borderWidth: 1.5,
                        borderRadius: 6,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        rtl: true,
                        backgroundColor: '#0f172a',
                        borderColor: '#334155',
                        borderWidth: 1,
                        titleFont: { family: 'IBM Plex Sans Arabic', size: 12 },
                        bodyFont: { family: 'IBM Plex Sans Arabic', size: 11 },
                        callbacks: {
                            label(ctx) {
                                return (
                                    ctx.dataset.label +
                                    ': ' +
                                    Number(ctx.raw).toLocaleString('en-US', { minimumFractionDigits: 2 }) +
                                    ' د.ب'
                                );
                            },
                        },
                    },
                },
                scales: {
                    x: { ticks: { color: '#64748b', font: { size: 10 } }, grid: { display: false } },
                    y: { ticks: { color: '#64748b', font: { size: 10 } }, grid: { color: 'rgba(51,65,85,0.25)' } },
                },
            },
        });
    });
}
