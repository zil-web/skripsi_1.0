<div class="bg-white rounded-xl shadow-sm p-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Pemasukan vs Pengeluaran (6 bulan terakhir)</h3>

    <div style="height:300px;">
        <canvas id="chartBar"></canvas>
    </div>

    @once
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
    @endonce

    @once
    <script>
        (function(){
            const chartData = @json($chart_bulanan);

            const ctx = document.getElementById('chartBar');
            if (!ctx) return;

            const idr = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 });

            new Chart(ctx.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: chartData.labels,
                    datasets: [
                        {
                            label: 'Pemasukan',
                            data: chartData.pemasukan,
                            backgroundColor: '#1D9E75',
                            borderRadius: 6,
                        },
                        {
                            label: 'Pengeluaran',
                            data: chartData.pengeluaran,
                            backgroundColor: '#E74C3C',
                            borderRadius: 6,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const val = context.parsed.y ?? context.parsed;
                                    return context.dataset.label + ': ' + idr.format(val);
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return idr.format(value);
                                }
                            }
                        }
                    }
                }
            });
        })();
    </script>
    @endonce
</div>
