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
                    <div class="card text-white shadow-lg border-radius-xl"
                        style="background-color: #b22fa4; color: #fff; box-shadow: inset 2px 2px 6px rgba(255, 255, 255, 0.1), 6px 6px 20px rgba(178, 47, 164, 0.5);">
                        <div class="card-body d-flex justify-content-between align-items-center p-4">
                            <div>
                                <p class="mb-1 text-sm fw-bold opacity-85">Total Positif</p>
                                <h4 id="positifCount" class="mb-0 text-white fw-bolder">
                                    {{ $cDana['positif'] + $cGoPay['positif'] + $cShopee['positif'] }}
                                </h4>
                            </div>
                            <div class="d-flex align-items-center justify-content-center rounded-circle"
                                style="width: 64px; height: 64px; background-color: rgba(255, 255, 255, 0.15); box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);">
                                <i class="fa-solid fa-face-smile fa-2x opacity-9"
                                    style="transition: transform 0.3s ease, opacity 0.3s ease;"
                                    onmouseover="this.style.transform='scale(1.2)'; this.style.opacity='1';"
                                    onmouseout="this.style.transform='scale(1)'; this.style.opacity='0.9';"></i>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-white"
                        style="background-color: #a95b91; color: #fff; box-shadow: inset 2px 2px 6px rgba(255, 255, 255, 0.1), 6px 6px 20px rgba(178, 47, 164, 0.5);">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <p class="mb-1 text-sm">Total Netral</p>
                                <h4 id="netralCount" class="mb-0 text-white">
                                    {{ $cDana['netral'] + $cGoPay['netral'] + $cShopee['netral'] }}
                                </h4>
                            </div>
                            <div class="d-flex align-items-center justify-content-center rounded-circle"
                                style="width: 64px; height: 64px; background-color: rgba(255, 255, 255, 0.15); box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);">
                                <i class="fa-solid fa-face-meh fa-2x opacity-9"
                                    style="transition: transform 0.3s ease, opacity 0.3s ease;"
                                    onmouseover="this.style.transform='scale(1.2)'; this.style.opacity='1';"
                                    onmouseout="this.style.transform='scale(1)'; this.style.opacity='0.9';"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card text-white"
                        style="background-color: #8b2f5e; color: #fff; box-shadow: inset 2px 2px 6px rgba(255, 255, 255, 0.1), 6px 6px 20px rgba(178, 47, 164, 0.5);">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <p class="mb-1 text-sm">Total Negatif</p>
                                <h4 id="negatifCount" class="mb-0 text-white">
                                    {{ $cDana['negatif'] + $cGoPay['negatif'] + $cShopee['negatif'] }}
                                </h4>
                            </div>
                            <div class="d-flex align-items-center justify-content-center rounded-circle"
                                style="width: 64px; height: 64px; background-color: rgba(255, 255, 255, 0.15); box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);">
                                <i class="fa-solid fa-face-frown fa-2x opacity-9"
                                    style="transition: transform 0.3s ease, opacity 0.3s ease;"
                                    onmouseover="this.style.transform='scale(1.2)'; this.style.opacity='1';"
                                    onmouseout="this.style.transform='scale(1)'; this.style.opacity='0.9';"></i>
                            </div>
                        </div>
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
                        <canvas id="chart-line" class="chart-canvas" height="318" style="width:100%;"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <!-- Pie Chart Card with filter -->
        <div class="col-lg-5 mb-lg-0 mb-4 d-flex">
            <div class="card z-index-2 flex-grow-1">
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
        <div class="col-lg-7 d-flex">
            <div class="card z-index-2 flex-grow-1">
                <div class="card-header pb-0">
                    <h6>Perbandingan Sentimen Antar Brand</h6>
                    <p class="text-sm">Tabel ini menunjukkan jumlah dan proporsi sentimen untuk masing-masing e-wallet.</p>
                </div>
                <div class="card-body px-3 pt-0 pb-3">
                    <div class="table-responsive">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                        style="background-color: #f8f9fa;">E-Wallet</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2"
                                        style="background-color: #f8f9fa;">Positif</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2"
                                        style="background-color: #f8f9fa;">Netral</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2"
                                        style="background-color: #f8f9fa;">Negatif</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2"
                                        style="background-color: #f8f9fa;">Net Score</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>DANA</strong></td>
                                    <td>{{ $cDana['positif'] }}</td>
                                    <td>{{ $cDana['netral'] }}</td>
                                    <td>{{ $cDana['negatif'] }}</td>
                                    <td>
                                        <span
                                            class="badge {{ $cDana['positif'] - $cDana['negatif'] > 0 ? 'bg-success' : 'bg-danger' }}">
                                            {{ number_format((($cDana['positif'] - $cDana['negatif']) / max(1, $cDana['positif'] + $cDana['netral'] + $cDana['negatif'])) * 100, 1) }}%
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>GoPay</strong></td>
                                    <td>{{ $cGoPay['positif'] }}</td>
                                    <td>{{ $cGoPay['netral'] }}</td>
                                    <td>{{ $cGoPay['negatif'] }}</td>
                                    <td>
                                        <span
                                            class="badge {{ $cGoPay['positif'] - $cGoPay['negatif'] > 0 ? 'bg-success' : 'bg-danger' }}">
                                            {{ number_format((($cGoPay['positif'] - $cGoPay['negatif']) / max(1, $cGoPay['positif'] + $cGoPay['netral'] + $cGoPay['negatif'])) * 100, 1) }}%
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>ShopeePay</strong></td>
                                    <td>{{ $cShopee['positif'] }}</td>
                                    <td>{{ $cShopee['netral'] }}</td>
                                    <td>{{ $cShopee['negatif'] }}</td>
                                    <td>
                                        <span
                                            class="badge {{ $cShopee['positif'] - $cShopee['negatif'] > 0 ? 'bg-success' : 'bg-danger' }}">
                                            {{ number_format((($cShopee['positif'] - $cShopee['negatif']) / max(1, $cShopee['positif'] + $cShopee['netral'] + $cShopee['negatif'])) * 100, 1) }}%
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
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
                    maintainAspectRatio: false, // Disable maintain aspect ratio
                    aspectRatio: 2, // Aspect ratio of 1 will give a square chart, you can adjust to your needs
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
        // Word Cloud
        // var danaData = [{{ $cDana['positif'] }}, {{ $cDana['netral'] }}, {{ $cDana['negatif'] }}];
        // var goPayData = [{{ $cGoPay['positif'] }}, {{ $cGoPay['netral'] }}, {{ $cGoPay['negatif'] }}];
        // var shopeeData = [{{ $cShopee['positif'] }}, {{ $cShopee['netral'] }}, {{ $cShopee['negatif'] }}];

        // var danaData = [{{ $cDana['positif'] }}, {{ $cDana['netral'] }}, {{ $cDana['negatif'] }}];
        // var goPayData = [{{ $cGoPay['positif'] }}, {{ $cGoPay['netral'] }}, {{ $cGoPay['negatif'] }}];
        // var shopeeData = [{{ $cShopee['positif'] }}, {{ $cShopee['netral'] }}, {{ $cShopee['negatif'] }}];

        // var wordData = [{
        //         text: 'Positif Dana',
        //         weight: danaData[0]
        //     },
        //     {
        //         text: 'Netral Dana',
        //         weight: danaData[1]
        //     },
        //     {
        //         text: 'Negatif Dana',
        //         weight: danaData[2]
        //     },
        //     {
        //         text: 'Positif GoPay',
        //         weight: goPayData[0]
        //     },
        //     {
        //         text: 'Netral GoPay',
        //         weight: goPayData[1]
        //     },
        //     {
        //         text: 'Negatif GoPay',
        //         weight: goPayData[2]
        //     },
        //     {
        //         text: 'Positif Shopee',
        //         weight: shopeeData[0]
        //     },
        //     {
        //         text: 'Netral Shopee',
        //         weight: shopeeData[1]
        //     },
        //     {
        //         text: 'Negatif Shopee',
        //         weight: shopeeData[2]
        //     }
        // ];

        // // Menggunakan WordCloud.js untuk membuat visualisasi
        // WordCloud(document.getElementById('wordCloud'), {
        //     list: wordData.map(function(item) {
        //         return [item.text, item.weight]; // Format: [kata, ukuran]
        //     }),
        //     gridSize: 10,
        //     weightFactor: 10,
        //     fontFamily: 'Arial',
        //     color: 'random-light',
        //     backgroundColor: '#f4f6f9',
        //     rotateRatio: 0.5
        // });
        // var wordData = [{
        //         text: 'Positif Dana',
        //         weight: danaData[0]
        //     },
        //     {
        //         text: 'Netral Dana',
        //         weight: danaData[1]
        //     },
        //     {
        //         text: 'Negatif Dana',
        //         weight: danaData[2]
        //     },
        //     {
        //         text: 'Positif GoPay',
        //         weight: goPayData[0]
        //     },
        //     {
        //         text: 'Netral GoPay',
        //         weight: goPayData[1]
        //     },
        //     {
        //         text: 'Negatif GoPay',
        //         weight: goPayData[2]
        //     },
        //     {
        //         text: 'Positif Shopee',
        //         weight: shopeeData[0]
        //     },
        //     {
        //         text: 'Netral Shopee',
        //         weight: shopeeData[1]
        //     },
        //     {
        //         text: 'Negatif Shopee',
        //         weight: shopeeData[2]
        //     }
        // ];

        // Pastikan canvas sudah siap
        // setTimeout(() => {
        //     WordCloud(document.getElementById('wordCloud'), {
        //         list: wordData.map(item => [item.text, item.weight]),
        //         gridSize: 8,
        //         weightFactor: 2,
        //         fontFamily: 'Arial',
        //         color: 'random-dark',
        //         backgroundColor: '#f4f6f9',
        //         rotateRatio: 0.5,
        //         minSize: 12
        //     });
        // }, 100);
        var danaTotal = {{ $cDana['positif'] + $cDana['netral'] + $cDana['negatif'] }};
        var goPayTotal = {{ $cGoPay['positif'] + $cGoPay['netral'] + $cGoPay['negatif'] }};
        var shopeeTotal = {{ $cShopee['positif'] + $cShopee['netral'] + $cShopee['negatif'] }};

        var wordData = [{
                text: 'Dana',
                weight: danaTotal
            },
            {
                text: 'GoPay',
                weight: goPayTotal
            },
            {
                text: 'Shopee',
                weight: shopeeTotal
            }
        ];

        // Render Word Cloud
        setTimeout(() => {
            WordCloud(document.getElementById('wordCloud'), {
                list: wordData.map(item => [item.text, item.weight]),
                gridSize: 6,
                weightFactor: 4, // semakin besar, semakin beda ukurannya
                fontFamily: 'Arial',
                color: 'random-dark',
                backgroundColor: '#f4f6f9',
                rotateRatio: 0,
                minSize: 12
            });
        }, 100);
    </script>
@endpush
