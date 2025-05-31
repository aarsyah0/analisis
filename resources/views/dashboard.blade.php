@extends('layouts/user_type/auth')

@section('content')
    {{-- Ringkasan Sentimen --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-end mb-3">
                <select id="walletFilter" class="form-select form-select-sm w-auto" style="min-width:120px;">
                    <option value="all">Semua</option>
                    <option value="dana">Dana</option>
                    <option value="gopay">GoPay</option>
                    <option value="shopeepay">ShopeePay</option>
                </select>
            </div>
            <div class="row gx-3">
                @foreach (['positif' => 'bg-success', 'netral' => 'bg-warning', 'negatif' => 'bg-danger'] as $sent => $badge)
                    <div class="col-md-4">
                        <div class="card h-100 text-white {{ $badge }} shadow-lg rounded-2">
                            <div class="card-body d-flex justify-content-between align-items-center p-4">
                                <div>
                                    <p class="mb-1 text-sm fw-bold text-uppercase">Total {{ ucfirst($sent) }}</p>
                                    <h4 id="{{ $sent }}Count" class="mb-0 fw-bolder">
                                        {{ $counts['all'][$sent] }}
                                    </h4>
                                </div>
                                <div class="rounded-circle d-flex justify-content-center align-items-center"
                                    style="width:64px; height:64px; background:rgba(255,255,255,0.15);">
                                    <i class="fa-solid fa-face-{{ $sent === 'positif' ? 'smile' : 'frown' }} fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Charts Section --}}
    <div class="row mt-4 align-items-stretch">
        <div class="col-lg-5 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h6>Perbandingan Sentimen E-Wallet</h6>
                </div>
                <div class="card-body p-0" style="height:300px;">
                    <canvas id="chart-bars" width="400" height="300"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-7 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h6>Tren Sentimen E-Wallet</h6>
                </div>
                <div class="card-body p-0" style="height:300px;">
                    <canvas id="chart-line" width="700" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Pie + Table --}}
    <div class="row mt-4 align-items-stretch">
        <div class="col-lg-5 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6>Distribusi Persentase Sentimen</h6>
                    <select id="walletFilterPie" class="form-select form-select-sm w-auto" style="min-width:120px;">
                        <option value="all">Semua</option>
                        <option value="dana">Dana</option>
                        <option value="gopay">GoPay</option>
                        <option value="shopeepay">ShopeePay</option>
                    </select>
                </div>
                <div class="card-body p-0" style="height:300px;">
                    <canvas id="chart-pie" width="400" height="300"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-7 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h6>Perbandingan Sentimen Antar Brand</h6>
                </div>
                <div class="card-body p-0" style="overflow-y:auto; max-height:300px;">
                    <table class="table align-middle mb-0">
                        <thead>
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

    {{-- WordCloud Section --}}
    <div class="row mt-5">
        <div class="col-12 mb-3 d-flex justify-content-center">
            <select id="sourceSelect" class="form-select w-auto" style="min-width:120px;">
                <option value="all">Gabungan</option>
                <option value="dana">Dana</option>
                <option value="gopay">GoPay</option>
                <option value="shopeepay">ShopeePay</option>
            </select>
        </div>

        @foreach (['positif', 'netral', 'negatif'] as $s)
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-header text-center">
                        <h6 class="mb-0">{{ ucfirst($s) }}</h6>
                    </div>
                    <div class="card-body d-flex justify-content-center align-items-center p-0" style="height:250px;">
                        <canvas id="wc-{{ $s }}" style="width:100%; height:100%;"></canvas>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    {{-- ==============================================
    SECTION: Confusion Matrix dengan Tailwind CSS
   ============================================== --}}
    <div class="mt-8 space-y-8">
        {{-- Grid tiga kolom pada layar md ke atas, satu kolom pada layar kecil --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach (['dana', 'gopay', 'shopeepay'] as $key)
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    {{-- Header Card --}}
                    <div class="px-4 py-2 bg-gray-100 border-b">
                        <h5 class="text-center text-gray-700 font-semibold capitalize">
                            {{ $key }} Confusion Matrix
                        </h5>
                    </div>

                    {{-- Body Card --}}
                    <div class="p-4">
                        @php
                            // Path ke CSV: storage/app/public/confusion_matrix_{key}.csv
                            $confusionPath = storage_path("app/public/confusion_matrix_{$key}.csv");
                            $confusionData = [];
                            if (file_exists($confusionPath)) {
                                // Parse setiap baris CSV menjadi array via str_getcsv
                                $confusionData = array_map('str_getcsv', file($confusionPath));
                            }
                        @endphp

                        @if (count($confusionData) > 1)
                            {{-- Wrapper agar tabel bisa di-scroll horizontal di layar kecil, dan center --}}
                            <div class="flex justify-center overflow-x-auto">
                                {{-- Hapus w-full, tambahkan mx-auto supaya tabel di-center --}}
                                <table
                                    class="table-fixed border-collapse border border-gray-200 text-sm text-center mx-auto">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            {{-- Pojok kiri atas: beri label “Actual\Predicted” --}}
                                            <th
                                                class="px-2 py-1 text-left text-gray-700 font-medium whitespace-nowrap border border-gray-200">
                                                Actual\Predicted
                                            </th>

                                            {{-- LOOP HANYA header Predicted (lewati elemen pertama yang kosong) --}}
                                            @foreach (array_slice($confusionData[0], 1) as $header)
                                                <th
                                                    class="px-2 py-1 border border-gray-200 text-gray-700 font-medium break-words">
                                                    {{ $header }}
                                                </th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach (array_slice($confusionData, 1) as $rowIndex => $row)
                                            {{-- Striping ganjil/genap --}}
                                            <tr class="{{ $rowIndex % 2 === 0 ? 'bg-white' : 'bg-gray-50' }}">
                                                {{-- Kolom label “Actual_…” --}}
                                                <th
                                                    class="px-2 py-1 text-left text-gray-700 font-medium whitespace-nowrap border border-gray-200">
                                                    {{ $row[0] }}
                                                </th>

                                                {{-- Loop nilai prediksi --}}
                                                @foreach (array_slice($row, 1) as $colIndex => $cell)
                                                    @php
                                                        // Perhatikan bahwa $rowIndex mulai dari 0 (baris pertama di body),
                                                        // tapi kolom diagonal-nya juga sama, karena array_slice dimulai dari row ke-1.
                                                        $isDiagonal = $rowIndex === $colIndex;
                                                    @endphp
                                                    <td
                                                        class="px-2 py-1 border border-gray-200
                                                       {{ $isDiagonal ? 'bg-gray-800 text-white font-semibold' : 'text-gray-700' }}">
                                                        {{ $cell }}
                                                    </td>
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center text-sm text-gray-500 py-4">
                                File <code
                                    class="bg-gray-100 px-1 py-0.5 rounded">confusion_matrix_{{ $key }}.csv</code>
                                tidak ditemukan atau kosong.
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>




    {{-- ================================================================
    SECTION: Metrics Chart (Perbandingan Precision, F1, Accuracy)
   ================================================================ --}}
    @php
        // Siapkan array untuk ringkasan metrik tiap e‐wallet
        $metricsSummary = [
            'dana' => ['precision' => 0, 'f1' => 0, 'accuracy' => 0],
            'gopay' => ['precision' => 0, 'f1' => 0, 'accuracy' => 0],
            'shopeepay' => ['precision' => 0, 'f1' => 0, 'accuracy' => 0],
        ];

        foreach (array_keys($metricsSummary) as $key) {
            // 1) Baca evaluation_metrics_full{key}.csv
            $metricsPath = storage_path("app/public/evaluation_metrics_full{$key}.csv");
            $metricsData = [];
            if (file_exists($metricsPath)) {
                $metricsData = array_map('str_getcsv', file($metricsPath));
            }

            // Hitung Macro‐Precision & Macro‐F1 (rata-rata precision/f1 tiap kelas)
            $sumPrecision = 0.0;
            $sumF1 = 0.0;
            $nClasses = 0;
            if (count($metricsData) > 1) {
                // Baris pertama header: [,precision,recall,f1‐score,support]
                foreach (array_slice($metricsData, 1) as $row) {
                    $sumPrecision += (float) $row[1]; // precision
                    $sumF1 += (float) $row[3]; // f1‐score
                    $nClasses++;
                }
                if ($nClasses > 0) {
                    $metricsSummary[$key]['precision'] = round($sumPrecision / $nClasses, 3);
                    $metricsSummary[$key]['f1'] = round($sumF1 / $nClasses, 3);
                }
            }

            // 2) Hitung Accuracy dari confusion_matrix_{key}.csv
            $confusionPath = storage_path("app/public/confusion_matrix_{$key}.csv");
            $confusionData = [];
            if (file_exists($confusionPath)) {
                $confusionData = array_map('str_getcsv', file($confusionPath));
            }

            if (count($confusionData) > 1) {
                $totalAll = 0;
                $sumDiagonal = 0;
                // Hitung total sampel
                foreach (array_slice($confusionData, 1) as $r) {
                    for ($c = 1; $c < count($r); $c++) {
                        $totalAll += (int) $r[$c];
                    }
                }
                // Jumlah diagonal (misal 3 kelas → sel [1][1], [2][2], [3][3])
                $sumDiagonal += (int) $confusionData[1][1];
                $sumDiagonal += (int) $confusionData[2][2];
                $sumDiagonal += (int) $confusionData[3][3];

                if ($totalAll > 0) {
                    $metricsSummary[$key]['accuracy'] = round($sumDiagonal / $totalAll, 3);
                }
            }
        }
    @endphp

    <div class="row mt-4 mb-5">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h6 class="mb-0">Perbandingan Metode Prediksi: Precision, F1‐Score, dan Akurasi</h6>
                </div>
                <div class="card-body p-3" style="height:350px;">
                    <canvas id="metricsChart" width="800" height="350"></canvas>
                </div>
            </div>
        </div>
    </div>
    {{-- ================================================================
    SECTION: Top Features (Tiap e‐wallet dalam satu card terpisah)
   ================================================================ --}}
    <div class="row mt-5">
        @foreach (['dana', 'gopay', 'shopeepay'] as $key)
            @php
                // Path ke CSV: storage/app/public/top_features_{key}.csv
                $featuresPath = storage_path("app/public/top_features_{$key}.csv");
                $featuresData = [];
                if (file_exists($featuresPath)) {
                    $featuresData = array_map('str_getcsv', file($featuresPath));
                }
            @endphp

            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card bg-white border-radius-lg shadow-lg h-100">
                    {{-- Header Kartu dengan Gradient Soft UI --}}
                    <div class="card-header bg-gradient-secondary border-radius-lg-top text-center py-2">
                        <h6 class="mb-0 text-uppercase text-white font-weight-bold">
                            {{ $key }} Top Features
                        </h6>
                    </div>

                    {{-- Body Kartu --}}
                    <div class="card-body p-0">
                        @if (count($featuresData) > 1)
                            <ul class="list-group list-group-flush">
                                @foreach (array_slice($featuresData, 1) as $row)
                                    {{-- Kolom pertama = nama fitur, kolom kedua = skor --}}
                                    <li
                                        class="list-group-item d-flex justify-content-between align-items-center px-3 py-2">
                                        <span class="font-weight-medium text-dark">
                                            {{ $row[0] }}
                                        </span>
                                        <span class="badge badge-sm bg-gradient-secondary">
                                            {{ $row[1] }}
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="alert alert-warning text-center mb-0 py-3 small rounded-bottom-lg">
                                File <code>top_features_{{ $key }}.csv</code> tidak ditemukan atau kosong.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- ======================================================================== --}}
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/wordcloud2@1.1.2/dist/wordcloud2.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const counts = @json($counts);
            // Summary
            const upd = key => ['positif', 'netral', 'negatif']
                .forEach(s => document.getElementById(s + 'Count').textContent = counts[key][s]);
            document.getElementById('walletFilter').addEventListener('change', e => upd(e.target.value));
            upd('all');

            const COLORS = ['rgba(75,192,192,0.6)', 'rgba(54,162,235,0.6)', 'rgba(255,206,86,0.6)'];
            const BORDERS = ['rgba(75,192,192,1)', 'rgba(54,162,235,1)', 'rgba(255,206,86,1)'];

            // Bar
            new Chart('chart-bars', {
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
                        yAxes: [{
                            ticks: {
                                beginAtZero: true
                            }
                        }]
                    }
                }
            });

            // Line
            new Chart('chart-line', {
                type: 'line',
                data: {
                    labels: ['Dana', 'GoPay', 'ShopeePay'],
                    datasets: ['positif', 'netral', 'negatif'].map((s, i) => ({
                        label: s.charAt(0).toUpperCase() + s.slice(1),
                        data: ['dana', 'gopay', 'shopeepay'].map(k => counts[k][s]),
                        borderColor: BORDERS[i],
                        fill: false,
                        tension: 0.4
                    }))
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });

            // Pie
            // const pie = new Chart('chart-pie', {
            //     type: 'pie',
            //     data: {
            //         labels: ['Positif', 'Netral', 'Negatif'],
            //         datasets: [{
            //             data: ['positif', 'netral', 'negatif'].map(s => counts.all[s]),
            //             backgroundColor: COLORS,
            //             borderColor: BORDERS,
            //             borderWidth: 1
            //         }]
            //     },
            //     options: {
            //         responsive: true,
            //         maintainAspectRatio: false
            //     }
            // });

            // document.getElementById('walletFilterPie').addEventListener('change', e => {
            //     pie.data.datasets[0].data = ['positif', 'netral', 'negatif'].map(s => counts[e.target.value]
            //         [s]);
            //     pie.update();
            // });

            const sourceSel = document.getElementById('sourceSelect');
            const sentiments = ['positif', 'netral', 'negatif'];

            function drawWC(sentiment) {
                const cvs = document.getElementById(`wc-${sentiment}`);
                // gunakan koordinat canvas internal
                const origin = [cvs.width / 2, cvs.height / 2];

                fetch(`/wordcloud-data?source=${sourceSel.value}&sentiment=${sentiment}`)
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
                    });
            }

            sourceSel.addEventListener('change', () => sentiments.forEach(drawWC));
            sentiments.forEach(drawWC);
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Ambil data ringkasan metrik dari PHP ke JS
            const ewMetrics = @json($metricsSummary);
            const labels = ['Dana', 'GoPay', 'ShopeePay'];

            // Siapkan data Precision, F1, dan Accuracy dalam urutan yang sama
            const precisionData = labels.map(name => {
                const key = name.toLowerCase();
                return ewMetrics[key].precision;
            });
            const f1Data = labels.map(name => {
                const key = name.toLowerCase();
                return ewMetrics[key].f1;
            });
            const accuracyData = labels.map(name => {
                const key = name.toLowerCase();
                return ewMetrics[key].accuracy;
            });

            // Buat bar chart dengan tiga kelompok dataset
            new Chart(document.getElementById('metricsChart'), {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                            label: 'Precision (Macro)',
                            data: precisionData,
                            backgroundColor: 'rgba(54, 162, 235, 0.6)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'F1‐Score (Macro)',
                            data: f1Data,
                            backgroundColor: 'rgba(255, 206, 86, 0.6)',
                            borderColor: 'rgba(255, 206, 86, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'Accuracy',
                            data: accuracyData,
                            backgroundColor: 'rgba(75, 192, 192, 0.6)',
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true,
                                callback: value => (value * 100).toFixed(0) +
                                    '%' // Menampilkan dalam persen
                            },
                            scaleLabel: {
                                display: true,
                                labelString: 'Nilai (%)'
                            }
                        }],
                        xAxes: [{
                            barPercentage: 0.6,
                            categoryPercentage: 0.6
                        }]
                    },
                    tooltips: {
                        callbacks: {
                            label: function(tooltipItem, data) {
                                let label = data.datasets[tooltipItem.datasetIndex].label || '';
                                let val = tooltipItem.yLabel;
                                return label + ': ' + (val * 100).toFixed(1) + '%';
                            }
                        }
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const counts = @json($counts);

            // Fungsi menghitung persentase
            function getPiePercent(key) {
                const d = counts[key];
                const total = (d.positif + d.netral + d.negatif) || 1;
                return [
                    ((d.positif / total) * 100).toFixed(2),
                    ((d.netral / total) * 100).toFixed(2),
                    ((d.negatif / total) * 100).toFixed(2)
                ];
            }

            const COLORS = [
                'rgba(75,192,192,0.6)',
                'rgba(54,162,235,0.6)',
                'rgba(255,206,86,0.6)'
            ];
            const BORDERS = [
                'rgba(75,192,192,1)',
                'rgba(54,162,235,1)',
                'rgba(255,206,86,1)'
            ];

            // Inisialisasi Pie Chart
            const pie = new Chart('chart-pie', {
                type: 'pie',
                data: {
                    labels: ['Positif', 'Netral', 'Negatif'],
                    datasets: [{
                        data: getPiePercent('all'),
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
                            color: '#ffffff',
                            font: {
                                weight: 'bold',
                                size: 14
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const valStr = context.formattedValue || context.raw;
                                    return `${label}: ${valStr}%`;
                                }
                            }
                        }
                    }
                },
                plugins: [ChartDataLabels]
            });

            // Update pie chart & counter ketika dropdown dipilih
            const updateAll = (key) => {
                // Update data pie
                pie.data.datasets[0].data = getPiePercent(key);
                pie.update();

                // Update counter
                ['positif', 'netral', 'negatif'].forEach(s => {
                    document.getElementById(s + 'Count').textContent = counts[key][s];
                });
            };

            // Event listener untuk filter Pie
            document.getElementById('walletFilterPie').addEventListener('change', e => {
                updateAll(e.target.value);
            });

            // Set default awal
            updateAll('all');
        });
    </script>
@endpush
