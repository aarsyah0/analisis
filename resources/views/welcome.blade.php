<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Sentimen E-Wallet</title>

    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="{{ asset('assets/css/nucleo-icons.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/nucleo-svg.css') }}" rel="stylesheet" />
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
    <link href="{{ asset('assets/css/soft-ui-dashboard.css?v=1.0.3') }}" rel="stylesheet" />

    <!-- WordCloud2.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/wordcloud2.js/1.0.2/wordcloud2.min.js"></script>

    <style>
        body {
            font-family: 'Nunito', sans-serif;
            background-color: #f8f9fa;
        }

        .card {
            border: none;
        }

        .card-header h6 {
            font-weight: 700;
            font-size: 1rem;
        }

        .counts .card {
            cursor: default;
        }

        /* Pastikan canvas wordcloud tidak overflow */
        .wc-canvas {
            width: 100% !important;
            height: 100% !important;
        }
    </style>
</head>

<body class="g-sidenav-show bg-gray-100">
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow rounded py-3">
        <div class="container">
            {{-- Brand dengan Logo dan Judul --}}
            <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
                <img src="{{ asset('assets/pj.png') }}" alt="Logo" style="height:40px; width:40px;" class="me-2">
                <div class="d-flex flex-column">
                    <span class="h5 mb-0 text-primary">Sentiment Dashboard</span>
                    <small class="text-muted">Manajemen Informatika</small>
                </div>
            </a>

            {{-- Login/Logout selalu terlihat, tanpa collapse --}}
            <div class="ms-auto d-flex align-items-center">
                @guest
                    <a class="btn btn-sm btn-primary px-3 text-white d-flex align-items-center" href="{{ route('login') }}">
                        <i class="fa fa-sign-in-alt me-1"></i>
                        <span class="fw-medium">Login</span>
                    </a>
                @endguest

                @auth
                    <a class="btn btn-sm btn-outline-secondary px-3 d-flex align-items-center" href="{{ route('logout') }}"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fa fa-sign-out-alt me-1"></i>
                        <span class="fw-medium">Logout</span>
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="GET" class="d-none"></form>
                @endauth
            </div>
        </div>
    </nav>

    <div class="container py-4">

        <div class="row mb-4">
            <div class="col-12 d-flex justify-content-end mb-3">
                <select id="walletFilter" class="form-select form-select-sm w-auto" style="min-width: 140px;">
                    <option value="all">Semua</option>
                    <option value="dana">Dana</option>
                    <option value="gopay">GoPay</option>
                    <option value="shopeepay">ShopeePay</option>
                </select>
            </div>
            <div class="row gx-3 counts">
                @foreach (['positif' => 'bg-gradient-success', 'netral' => 'bg-gradient-warning', 'negatif' => 'bg-gradient-danger'] as $sent => $badge)
                    <div class="col-md-4 mb-3">
                        <div class="card {{ $badge }} shadow">
                            <div class="card-body d-flex justify-content-between align-items-center p-4">
                                <div>
                                    <p class="mb-1 text-sm text-uppercase opacity-7">{{ 'Total ' . ucfirst($sent) }}
                                    </p>
                                    <h4 id="{{ $sent }}Count" class="mb-0 fw-bold text-white">
                                        {{ $counts['all'][$sent] }}
                                    </h4>
                                </div>
                                <div class="icon icon-lg d-flex justify-content-center align-items-center">
                                    <i
                                        class="fa-solid fa-face-{{ $sent === 'positif' ? 'smile' : 'frown' }} fa-2x text-black"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ====================== --}}
        {{-- Section: Charts Row   --}}
        {{-- ====================== --}}
        <div class="row gx-3 mb-4">
            <div class="col-lg-5 mb-3">
                <div class="card h-100 shadow-sm">
                    <div class="card-header">
                        <h6 class="mb-0">Perbandingan Sentimen E-Wallet</h6>
                    </div>
                    <div class="card-body p-3" style="height: 300px;">
                        <canvas id="chart-bars" class="w-100 h-100"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-7 mb-3">
                <div class="card h-100 shadow-sm">
                    <div class="card-header">
                        <h6 class="mb-0">Tren Sentimen E-Wallet</h6>
                    </div>
                    <div class="card-body p-3" style="height: 300px;">
                        <canvas id="chart-line" class="w-100 h-100"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- ====================== --}}
        {{-- Section: Pie + Table   --}}
        {{-- ====================== --}}
        <div class="row gx-3 mb-4">
            <div class="col-lg-5 mb-3">
                <div class="card h-100 shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Distribusi Persentase Sentimen</h6>
                        <select id="walletFilterPie" class="form-select form-select-sm w-auto"
                            style="min-width: 140px;">
                            <option value="all">Semua</option>
                            <option value="dana">Dana</option>
                            <option value="gopay">GoPay</option>
                            <option value="shopeepay">ShopeePay</option>
                        </select>
                    </div>
                    <div class="card-body p-3" style="height: 300px;">
                        <canvas id="chart-pie" class="w-100 h-100"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-7 mb-3">
                <div class="card h-100 shadow-sm">
                    <div class="card-header">
                        <h6 class="mb-0">Perbandingan Sentimen Antar Brand</h6>
                    </div>
                    <div class="card-body p-3 overflow-auto" style="max-height: 300px;">
                        <table class="table table-striped align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>E-Wallet</th>
                                    <th>Positif</th>
                                    <th>Netral</th>
                                    <th>Negatif</th>
                                    <th>Net Score</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach (['Dana' => 'dana', 'GoPay' => 'gopay', 'ShopeePay' => 'shopeepay'] as $label => $key)
                                    @php
                                        $d = $counts[$key];
                                        $net = (($d['positif'] - $d['negatif']) / max(1, array_sum($d))) * 100;
                                    @endphp
                                    <tr>
                                        <td><strong>{{ $label }}</strong></td>
                                        <td>{{ $d['positif'] }}</td>
                                        <td>{{ $d['netral'] }}</td>
                                        <td>{{ $d['negatif'] }}</td>
                                        <td>
                                            <span class="badge {{ $net > 0 ? 'bg-success' : 'bg-danger' }}">
                                                {{ number_format($net, 1) }}%
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- ====================== --}}
        {{-- Section: WordClouds    --}}
        {{-- ====================== --}}
        <div class="row gx-3 mb-4">
            <div class="col-12 mb-2 d-flex justify-content-center">
                <select id="sourceSelect" class="form-select w-auto" style="min-width: 140px;">
                    <option value="all">Gabungan</option>
                    <option value="dana">Dana</option>
                    <option value="gopay">GoPay</option>
                    <option value="shopeepay">ShopeePay</option>
                </select>
            </div>
            @foreach (['positif', 'netral', 'negatif'] as $s)
                <div class="col-md-4 mb-3">
                    <div class="card h-100 shadow-sm">
                        <div class="card-header text-center">
                            <h6 class="mb-0">{{ ucfirst($s) }}</h6>
                        </div>
                        <div class="card-body p-0 d-flex justify-content-center align-items-center"
                            style="height: 250px;">
                            <canvas id="wc-{{ $s }}" class="wc-canvas"></canvas>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- ====================== --}}
    {{-- SCRIPTS                --}}
    {{-- ====================== --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const counts = @json($counts);
            // Update summary cards
            const upd = key => ['positif', 'netral', 'negatif']
                .forEach(s => document.getElementById(s + 'Count').textContent = counts[key][s]);
            document.getElementById('walletFilter').addEventListener('change', e => upd(e.target.value));
            upd('all');

            // ======================
            // Chart.js v3+ Configs
            // ======================
            const COLORS = ['rgba(75, 192, 192, 0.6)', 'rgba(54, 162, 235, 0.6)', 'rgba(255, 206, 86, 0.6)'];
            const BORDERS = ['rgba(75, 192, 192, 1)', 'rgba(54, 162, 235, 1)', 'rgba(255, 206, 86, 1)'];

            // Bar Chart: Perbandingan Sentimen
            new Chart(document.getElementById('chart-bars').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: ['Positif', 'Netral', 'Negatif'],
                    datasets: ['dana', 'gopay', 'shopeepay'].map((k, i) => ({
                        label: k.charAt(0).toUpperCase() + k.slice(1),
                        data: ['positif', 'netral', 'negatif'].map(s => counts[k][s]),
                        backgroundColor: COLORS[i],
                        borderColor: BORDERS[i],
                        borderWidth: 1
                    }))
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                display: true
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top'
                        }
                    }
                }
            });

            // Line Chart: Tren Sentimen
            new Chart(document.getElementById('chart-line').getContext('2d'), {
                type: 'line',
                data: {
                    labels: ['Dana', 'GoPay', 'ShopeePay'],
                    datasets: ['positif', 'netral', 'negatif'].map((s, i) => ({
                        label: s.charAt(0).toUpperCase() + s.slice(1),
                        data: ['dana', 'gopay', 'shopeepay'].map(k => counts[k][s]),
                        borderColor: BORDERS[i],
                        fill: false,
                        tension: 0.4,
                        pointBackgroundColor: BORDERS[i],
                        pointRadius: 4
                    }))
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                display: true
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top'
                        }
                    }
                }
            });

            // Pie Chart: Distribusi Persentase
            const pieCtx = document.getElementById('chart-pie').getContext('2d');
            const pie = new Chart(pieCtx, {
                type: 'pie',
                data: {
                    labels: ['Positif', 'Netral', 'Negatif'],
                    datasets: [{
                        data: (() => {
                            const d = counts['all'];
                            const total = (d.positif + d.netral + d.negatif) || 1;
                            return [
                                ((d.positif / total) * 100).toFixed(2),
                                ((d.netral / total) * 100).toFixed(2),
                                ((d.negatif / total) * 100).toFixed(2)
                            ];
                        })(),
                        backgroundColor: COLORS,
                        borderColor: BORDERS,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        datalabels: {
                            formatter: (value) => value + '%',
                            color: '#fff',
                            font: {
                                weight: 'bold',
                                size: 12
                            }
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                },
                plugins: [ChartDataLabels]
            });

            document.getElementById('walletFilterPie').addEventListener('change', e => {
                const key = e.target.value;
                const d = counts[key];
                const total = (d.positif + d.netral + d.negatif) || 1;
                pie.data.datasets[0].data = [
                    ((d.positif / total) * 100).toFixed(2),
                    ((d.netral / total) * 100).toFixed(2),
                    ((d.negatif / total) * 100).toFixed(2)
                ];
                pie.update();
                ['positif', 'netral', 'negatif'].forEach(s => {
                    document.getElementById(s + 'Count').textContent = counts[key][s];
                });
            });

            // ======================
            // WordClouds
            // ======================
            const sourceSel = document.getElementById('sourceSelect');
            const sentiments = ['positif', 'netral', 'negatif'];

            function drawWC(sentiment) {
                const cvs = document.getElementById(`wc-${sentiment}`);
                const origin = [cvs.width / 2, cvs.height / 2];
                fetch(`{{ url('/wordcloud-data') }}?source=${sourceSel.value}&sentiment=${sentiment}`)
                    .then(r => r.json())
                    .then(list => {
                        WordCloud(cvs, {
                            list,
                            clearCanvas: true,
                            weightFactor: 1,
                            gridSize: 2,
                            rotateRatio: 0.3,
                            backgroundColor: window.getComputedStyle(cvs).backgroundColor,
                            origin
                        });
                    })
                    .catch(err => {
                        cvs.getContext('2d').fillText('Gagal memuat wordcloud', cvs.width / 2 - 50, cvs.height /
                            2);
                        console.error(err);
                    });
            }
            sourceSel.addEventListener('change', () => sentiments.forEach(drawWC));
            sentiments.forEach(drawWC);

        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
