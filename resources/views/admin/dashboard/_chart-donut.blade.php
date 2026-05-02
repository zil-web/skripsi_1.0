<div class="bg-white rounded-xl shadow-sm p-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Status Transaksi</h3>

    <div class="flex items-center gap-6">
        <div style="width:260px; height:260px;">
            <canvas id="chartDonut"></canvas>
        </div>

        <div class="flex-1">
            {{-- Legend will also render via Chart.js on the right, but include textual legend fallback --}}
            <ul class="space-y-3 text-sm text-gray-700">
                <li class="flex items-center gap-3"><span class="w-3 h-3 rounded-full" style="background:#1D9E75"></span> Approved</li>
                <li class="flex items-center gap-3"><span class="w-3 h-3 rounded-full" style="background:#F39C12"></span> Pending</li>
                <li class="flex items-center gap-3"><span class="w-3 h-3 rounded-full" style="background:#E74C3C"></span> Rejected</li>
            </ul>
        </div>
    </div>

    @once
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
    @endonce

    @once
    <script>
        (function(){
            const raw = @json($status_transaksi);
            const dataValues = [raw.approved ?? 0, raw.pending ?? 0, raw.rejected ?? 0];

            const ctx = document.getElementById('chartDonut');
            if (!ctx) return;

            // Plugin to draw total in center
            const centerTextPlugin = {
                id: 'centerText',
                beforeDraw(chart) {
                    const {ctx, chartArea: area} = chart;
                    const total = chart.data.datasets[0].data.reduce((s,v)=>s+ (Number(v)||0), 0);
                    ctx.save();
                    const centerX = (chart.chartArea.left + chart.chartArea.right) / 2;
                    const centerY = (chart.chartArea.top + chart.chartArea.bottom) / 2;
                    ctx.fillStyle = '#111827';
                    ctx.font = '600 18px Inter, ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial';
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    ctx.fillText(new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(total), centerX, centerY);
                    ctx.restore();
                }
            };

            new Chart(ctx.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: ['Approved','Pending','Rejected'],
                    datasets: [{
                        data: dataValues,
                        backgroundColor: ['#1D9E75', '#F39C12', '#E74C3C'],
                        hoverOffset: 8,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {usePointStyle: true}
                        },
                        tooltip: {
                            callbacks: {
                                label: function(ctx) {
                                    const val = ctx.parsed || 0;
                                    return ctx.label + ': ' + new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val);
                                }
                            }
                        }
                    }
                },
                plugins: [centerTextPlugin]
            });
        })();
    </script>
    @endonce
</div>
