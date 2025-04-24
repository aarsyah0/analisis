@extends('layouts/user_type/auth')

@section('content')
    @php
        if (!function_exists('getSentimentCounts')) {
            function getSentimentCounts($path)
            {
                if (!file_exists($path)) {
                    return ['positif' => 0, 'netral' => 0, 'negatif' => 0];
                }
                $rows = array_map('str_getcsv', file($path));
                array_shift($rows);
                $labels = array_column($rows, 1);
                $counts = array_count_values($labels);
                return array_merge(['positif' => 0, 'netral' => 0, 'negatif' => 0], $counts);
            }
        }

        // Hitung data sekali
        $base = resource_path('views');
        $cDana = getSentimentCounts("{$base}/terlabel.csv");
        $cGoPay = getSentimentCounts("{$base}/terlabelgopay.csv");
        $cShopee = getSentimentCounts("{$base}/terlabeshopepay.csv");

        // Total kartu (opsional)
        $totalPositif = $cDana['positif'] + $cGoPay['positif'] + $cShopee['positif'];
        $totalNetral = $cDana['netral'] + $cGoPay['netral'] + $cShopee['netral'];
        $totalNegatif = $cDana['negatif'] + $cGoPay['negatif'] + $cShopee['negatif'];
    @endphp

    <div class="row mb-4">
        <div class="col-12">
            <!-- Filter di atas, rata-kanan -->
            <div class="d-flex justify-content-end mb-3">
                <select id="walletFilter" class="form-select form-select-sm w-auto" style="min-width: 120px;">
                    <option value="all">Semua</option>
                    <option value="dana">Dana</option>
                    <option value="gopay">GoPay</option>
                    <option value="shopee">ShopeePay</option>
                </select>
            </div>

            <!-- Baris kartu -->
            <div class="row gx-3">
                <div class="col-md-4">
                    <div class="card text-white" style="background-color: #b22fa4;">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <p class="mb-1 text-sm">Total Positif</p>
                                <h4 id="positifCount" class="mb-0 text-white">
                                    {{ $cDana['positif'] + $cGoPay['positif'] + $cShopee['positif'] }}
                                </h4>
                            </div>
                            <i class="fa-solid fa-face-smile fa-4x opacity-8"></i>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card text-white" style="background-color: #a95b91;">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <p class="mb-1 text-sm">Total Netral</p>
                                <h4 id="netralCount" class="mb-0 text-white">
                                    {{ $cDana['netral'] + $cGoPay['netral'] + $cShopee['netral'] }}
                                </h4>
                            </div>
                            <i class="fa-solid fa-face-meh fa-4x opacity-8"></i>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card text-white" style="background-color: #8b2f5e;">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <p class="mb-1 text-sm">Total Negatif</p>
                                <h4 id="negatifCount" class="mb-0 text-white">
                                    {{ $cDana['negatif'] + $cGoPay['negatif'] + $cShopee['negatif'] }}
                                </h4>
                            </div>
                            <i class="fa-solid fa-face-frown fa-4x opacity-8"></i>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="row mt-4">
        <!-- Bar Chart Card -->
        <div class="col-lg-5 mb-lg-0 mb-4">
            <div class="card z-index-2">
                <div class="card-header pb-0">
                    <h6 class="mb-1">Perbandingan Sentimen E-Wallet</h6>
                    <p class="text-sm">Grafik batang menunjukkan jumlah sentimen positif, netral, dan negatif untuk Dana,
                        GoPay, dan ShopeePay.</p>
                </div>
                <div class="card-body p-3">
                    <div class="bg-gradient-light border-radius-lg py-3 pe-1 mb-3">
                        <div class="chart">
                            <canvas id="chart-bars" class="chart-canvas" height="250" style="width:100%;"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Line Chart Card -->
        <div class="col-lg-7 mb-lg-0 mb-4">
            <div class="card z-index-2">
                <div class="card-header pb-0">
                    <h6 class="mb-1">Tren Sentimen E-Wallet</h6>
                    <p class="text-sm">Garis menunjukkan perubahan proporsi sentimen (positif, netral, negatif) di setiap
                        e-wallet.</p>
                </div>
                <div class="card-body p-3">
                    <div class="chart">
                        <canvas id="chart-line" class="chart-canvas" height="250" style="width:100%;"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <!-- Pie Chart Card with filter -->
        <div class="col-lg-6 mx-auto">
            <div class="card z-index-2">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1">Distribusi Persentase Sentimen</h6>
                        <p class="text-sm">Grafik pai menampilkan persentase sentimen (positif, netral, negatif) untuk
                            e-wallet terpilih.</p>
                    </div>
                    <select id="wallet-filter" class="form-select form-select-sm w-auto">
                        <option value="all">Semua E-Wallet</option>
                        <option value="dana">Dana</option>
                        <option value="gopay">GoPay</option>
                        <option value="shopee">ShopeePay</option>
                    </select>
                </div>
                <div class="card-body p-3">
                    <div class="chart">
                        <canvas id="chart-pie" class="chart-canvas" height="250" style="width:100%;"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('dashboard')
    <!-- Load Chart.js library -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <script>
        window.onload = function() {
            // Chart contexts
            var ctxBar = document.getElementById("chart-bars").getContext("2d");
            var ctxLine = document.getElementById("chart-line").getContext("2d");
            var ctxPie = document.getElementById("chart-pie").getContext("2d");
            var filter = document.getElementById("wallet-filter");

            @php
                function getSentimentCounts($path)
                {
                    if (!file_exists($path)) {
                        return ['positif' => 0, 'netral' => 0, 'negatif' => 0];
                    }
                    $rows = array_map('str_getcsv', file($path));
                    array_shift($rows);
                    $labels = array_column($rows, 1);
                    $counts = array_count_values($labels);
                    return array_merge(['positif' => 0, 'netral' => 0, 'negatif' => 0], $counts);
                }
                $base = resource_path('views');
                $cDana = getSentimentCounts("{$base}/terlabel.csv");
                $cGoPay = getSentimentCounts("{$base}/terlabelgopay.csv");
                $cShopee = getSentimentCounts("{$base}/terlabeshopepay.csv");
            @endphp

            // Data arrays
            var labels = ['Positif', 'Netral', 'Negatif'];
            var danaData = [{{ $cDana['positif'] }}, {{ $cDana['netral'] }}, {{ $cDana['negatif'] }}];
            var goPayData = [{{ $cGoPay['positif'] }}, {{ $cGoPay['netral'] }}, {{ $cGoPay['negatif'] }}];
            var shopeeData = [{{ $cShopee['positif'] }}, {{ $cShopee['netral'] }}, {{ $cShopee['negatif'] }}];
            var allData = [
                danaData[0] + goPayData[0] + shopeeData[0],
                danaData[1] + goPayData[1] + shopeeData[1],
                danaData[2] + goPayData[2] + shopeeData[2]
            ];

            // Bar chart
            var gradBar = function(ctx, color) {
                var g = ctx.createLinearGradient(0, 0, 0, 250);
                g.addColorStop(0, color + '0.8)');
                g.addColorStop(1, color + '0.2)');
                return g;
            };
            new Chart(ctxBar, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                            label: 'Dana',
                            data: danaData,
                            backgroundColor: gradBar(ctxBar, 'rgba(75,192,192,'),
                            borderRadius: 4,
                            maxBarThickness: 20
                        },
                        {
                            label: 'GoPay',
                            data: goPayData,
                            backgroundColor: gradBar(ctxBar, 'rgba(255,159,64,'),
                            borderRadius: 4,
                            maxBarThickness: 20
                        },
                        {
                            label: 'Shopee',
                            data: shopeeData,
                            backgroundColor: gradBar(ctxBar, 'rgba(153,102,255,'),
                            borderRadius: 4,
                            maxBarThickness: 20
                        }
                    ]
                },
                options: {
                    responsive: true,
                    animation: {
                        duration: 1200,
                        easing: 'easeInOutQuad'
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top'
                        }
                    }
                }
            });

            // Line chart
            function makeLineGrad(ctx, base) {
                var g = ctx.createLinearGradient(0, 0, 0, 250);
                g.addColorStop(0, base + '0.8)');
                g.addColorStop(1, base + '0.2)');
                return g;
            }
            new Chart(ctxLine, {
                type: 'line',
                data: {
                    labels: ['Dana', 'GoPay', 'Shopee'],
                    datasets: [{
                            label: 'Positif',
                            data: [danaData[0], goPayData[0], shopeeData[0]],
                            borderColor: makeLineGrad(ctxLine, 'rgba(54,162,235,'),
                            backgroundColor: 'transparent',
                            tension: 0.4,
                            borderWidth: 3
                        },
                        {
                            label: 'Netral',
                            data: [danaData[1], goPayData[1], shopeeData[1]],
                            borderColor: 'rgba(255,206,86,0.8)',
                            backgroundColor: 'transparent',
                            tension: 0.4,
                            borderWidth: 3
                        },
                        {
                            label: 'Negatif',
                            data: [danaData[2], goPayData[2], shopeeData[2]],
                            borderColor: 'rgba(255,99,132,0.8)',
                            backgroundColor: 'transparent',
                            tension: 0.4,
                            borderWidth: 3
                        }
                    ]
                },
                options: {
                    responsive: true,
                    animation: {
                        duration: 1200,
                        easing: 'easeOutQuart'
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });

            // Pie chart with tooltip percentages
            var pieChart = new Chart(ctxPie, {
                type: 'pie',
                data: {
                    labels: labels,
                    datasets: [{
                        data: allData,
                        backgroundColor: ['#4BC0C0', '#FFCE56', '#FF6384'],
                        hoverOffset: 10
                    }]
                },
                options: {
                    responsive: true,
                    animation: {
                        animateRotate: true,
                        duration: 1000
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    var value = context.parsed;
                                    var sum = context.chart.data.datasets[0].data.reduce(function(a, b) {
                                        return a + b;
                                    }, 0);
                                    var pct = Math.round(value * 100 / sum);
                                    return context.label + ': ' + value + ' (' + pct + '%)';
                                }
                            }
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });

            // Filter event for pie chart
            filter.addEventListener('change', function() {
                var m = this.value;
                pieChart.data.datasets[0].data = (m === 'dana' ? danaData : m === 'gopay' ? goPayData : m ===
                    'shopee' ? shopeeData : allData);
                pieChart.update();
            });
        };
        document.addEventListener('DOMContentLoaded', () => {
            const filter = document.getElementById('walletFilter');
            const positifEl = document.getElementById('positifCount');
            const netralEl = document.getElementById('netralCount');
            const negatifEl = document.getElementById('negatifCount');

            const data = {
                all: {
                    positif: {{ $cDana['positif'] + $cGoPay['positif'] + $cShopee['positif'] }},
                    netral: {{ $cDana['netral'] + $cGoPay['netral'] + $cShopee['netral'] }},
                    negatif: {{ $cDana['negatif'] + $cGoPay['negatif'] + $cShopee['negatif'] }},
                },
                dana: {
                    positif: {{ $cDana['positif'] }},
                    netral: {{ $cDana['netral'] }},
                    negatif: {{ $cDana['negatif'] }},
                },
                gopay: {
                    positif: {{ $cGoPay['positif'] }},
                    netral: {{ $cGoPay['netral'] }},
                    negatif: {{ $cGoPay['negatif'] }},
                },
                shopee: {
                    positif: {{ $cShopee['positif'] }},
                    netral: {{ $cShopee['netral'] }},
                    negatif: {{ $cShopee['negatif'] }},
                },
            };

            filter.addEventListener('change', function() {
                const d = data[this.value];
                positifEl.textContent = d.positif;
                netralEl.textContent = d.netral;
                negatifEl.textContent = d.negatif;
            });
        });
    </script>
@endpush
