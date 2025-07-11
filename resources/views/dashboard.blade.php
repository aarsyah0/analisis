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
        // Daftar e-wallet yang ingin kita tampilkan
        $wallets = ['dana', 'gopay', 'shopeepay'];

        // Kita akan menyimpan struktur seperti:
        // $metricsDetail['dana'] = [
        //     'labels'    => ['Netral','Positif','Negatif'],
        //     'precision' => [0.812, 0.902, 0.748],
        //     'recall'    => [0.800, 0.889, 0.765],
        //     'f1'        => [0.806, 0.895, 0.756],
        // ];
        $metricsDetail = [];

        // Kelas‐kelas yang ingin kita plot urutannya: netral → positif → negatif
        $kelasUrut = ['netral', 'positif', 'negatif'];

        foreach ($wallets as $key) {
            // Nama file CSV mirip: evaluation_metrics_full{key}.csv
            // Note: untuk 'shopeepay', file Anda pakai 'shopee' bukan 'shopeepay'?
            // Di contoh awal Anda, yang di‐upload adalah evaluation_metrics_fullshopee.csv,
            // maka gunakan $fileKey='shopee' bukan 'shopeepay'.
            $fileKey = $key;
            $pathCsv = storage_path("app/public/evaluation_metrics_full{$fileKey}.csv");
            $rows = [];
            if (file_exists($pathCsv)) {
                // Baca setiap baris kemudian pecah jadi array per kolom
                $rows = array_map('str_getcsv', file($pathCsv));
            }

            // Inisialisasi struktur kosong
            $metricsDetail[$key] = [
                'labels' => [],
                'precision' => [],
                'recall' => [],
                'f1' => [],
            ];

            if (count($rows) > 1) {
                // Baris 0: header → abaikan. Mulai dari baris 1.
                // Kita ingin memastikan tiap kelas ('netral','positif','negatif') ada, dan urutannya sesuai $kelasUrut.
                // Bisa saja di CSV urutannya sudah sama, tapi kita paksa urut sesuai $kelasUrut.

                // Buat map nama kelas ke indeks baris
                $mapRowByKelas = [];
                foreach (array_slice($rows, 1) as $r) {
                    // Asumsikan kolom 0 adalah nama kelas, kolom 2 precision, kolom 3 recall, kolom 4 f1-score
                    $namaKelas = strtolower(trim($r[0]));
                    $mapRowByKelas[$namaKelas] = $r;
                }

                foreach ($kelasUrut as $kelas) {
                    if (isset($mapRowByKelas[$kelas])) {
                        $r = $mapRowByKelas[$kelas];
                        // Ambil nilai ke‐2 (precision), ke‐3 (recall), ke‐4 (f1‐score)
                        // Hati‐hati: format CSV kadang ada kolom kosong di indeks 1 → kita gunakan r[2], r[3], r[4].
                        $precision = isset($r[1]) ? (float) $r[1] : 0;
                        $recall = isset($r[2]) ? (float) $r[2] : 0;
                        $f1score = isset($r[3]) ? (float) $r[3] : 0;

                        // Push ke dalam array
                        $metricsDetail[$key]['labels'][] = ucfirst($kelas);
                        $metricsDetail[$key]['precision'][] = round($precision, 3);
                        $metricsDetail[$key]['recall'][] = round($recall, 3);
                        $metricsDetail[$key]['f1'][] = round($f1score, 3);
                    } else {
                        // Jika suatu kelas tidak ditemukan di CSV, isi 0.
                        $metricsDetail[$key]['labels'][] = ucfirst($kelas);
                        $metricsDetail[$key]['precision'][] = 0;
                        $metricsDetail[$key]['recall'][] = 0;
                        $metricsDetail[$key]['f1'][] = 0;
                    }
                }
            }
        }
    @endphp
    <div class="row mt-4 mb-5">
        @foreach ($wallets as $key)
            @php
                // Ubah key menjadi judul yang lebih enak (Dana, Gopay, Shopeepay)
                $judul = ucfirst($key);
            @endphp
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header">
                        <h6 class="mb-0">Metrik Klasifikasi: {{ $judul }}</h6>
                    </div>
                    <div class="card-body" style="height: 300px;">
                        {{-- Canvas untuk Chart.js --}}
                        <canvas id="chart-{{ $key }}" width="400" height="250"></canvas>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- ================================================================
    SECTION: Top Features (Tiap e‐wallet dalam satu card terpisah)
   ================================================================ --}}
    <div class="row mt-5">
        @foreach (['dana', 'gopay', 'shopeepay'] as $key)
            @php
                // Path ke CSV (perhatikan key: 'shopeepay' kalau memang file bernama top_features_shopeepay.csv)
                $featuresPath = storage_path("app/public/top_features_{$key}.csv");
                $featuresRaw = [];
                if (file_exists($featuresPath)) {
                    // Baca CSV (masing‐masing baris jadi array)
                    $featuresRaw = array_map('str_getcsv', file($featuresPath));
                }

                // Siapkan array untuk menampung fitur per kelas
                $classFeatures = [
                    'neutral' => [], // nanti kita label sebagai “Netral”
                    'positive' => [], // nanti jadi “Positif”
                    'negative' => [], // nanti jadi “Negatif”
                ];

                if (count($featuresRaw) > 1) {
                    // Baris pertama adalah header: [rank,neutral,positive,negative]
                    // Mulai loop dari index ke-1 (data sebenarnya)
                    foreach (array_slice($featuresRaw, 1) as $row) {
                        // $row[1] = neutral, $row[2] = positive, $row[3] = negative
                        $classFeatures['neutral'][] = $row[1] ?? '';
                        $classFeatures['positive'][] = $row[2] ?? '';
                        $classFeatures['negative'][] = $row[3] ?? '';
                    }
                }
            @endphp

            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card bg-white border-radius-lg shadow-lg h-100">
                    {{-- Header Kartu --}}
                    <div class="card-header bg-gradient-secondary border-radius-lg-top text-center py-2">
                        <h6 class="mb-0 text-uppercase text-white font-weight-bold">
                            {{ ucfirst($key) }} Top Features
                        </h6>
                    </div>

                    {{-- Body Kartu --}}
                    <div class="card-body p-3">
                        @if (count($featuresRaw) > 1)
                            <div class="row">
                                {{-- Tiga kolom kecil: Netral, Positif, Negatif --}}
                                @foreach ([
            'neutral' => 'Netral',
            'positive' => 'Positif',
            'negative' => 'Negatif',
        ] as $clsKey => $clsLabel)
                                    <div class="col-4">
                                        <h6 class="text-secondary text-uppercase text-center">{{ $clsLabel }}</h6>
                                        <ul class="list-group list-group-flush">
                                            @foreach ($classFeatures[$clsKey] as $feature)
                                                <li class="list-group-item px-2 py-1 small">
                                                    {{ $feature }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            {{-- Jika file tidak ada atau kosong --}}
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Kita ingin memanggil Chart.js untuk masing‐masing e‐wallet
            const wallets = {!! json_encode($wallets) !!};

            wallets.forEach(function(key) {
                // Ambil data PHP yang sudah kita siapkan di $metricsDetail
                const dataPHP = {!! json_encode($metricsDetail) !!};

                // Labels kelas (Netral, Positif, Negatif)
                const labels = dataPHP[key]['labels']; // contoh: ["Netral","Positif","Negatif"]
                const dataPrecision = dataPHP[key]['precision'];
                const dataRecall = dataPHP[key]['recall'];
                const dataF1 = dataPHP[key]['f1'];

                // Konfigurasi dataset untuk Chart.js
                const chartData = {
                    labels: labels,
                    datasets: [{
                            label: 'Precision',
                            data: dataPrecision,
                            backgroundColor: 'rgba(54, 162, 235, 0.5)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'Recall',
                            data: dataRecall,
                            backgroundColor: 'rgba(255, 206, 86, 0.5)',
                            borderColor: 'rgba(255, 206, 86, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'F1‐Score',
                            data: dataF1,
                            backgroundColor: 'rgba(75, 192, 192, 0.5)',
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 1
                        }
                    ]
                };

                const config = {
                    type: 'bar',
                    data: chartData,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: 1,
                                ticks: {
                                    callback: function(value) {
                                        return value.toFixed(2);
                                    }
                                },
                                title: {
                                    display: true,
                                    text: 'Nilai'
                                }
                            }
                        },
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.parsed.y !== null) {
                                            label += context.parsed.y.toFixed(3);
                                        }
                                        return label;
                                    }
                                }
                            },
                            legend: {
                                position: 'top',
                            }
                        }
                    }
                };

                // Buat chart pada canvas dengan id="chart-<key>"
                const ctx = document.getElementById('chart-' + key).getContext('2d');
                new Chart(ctx, config);
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
