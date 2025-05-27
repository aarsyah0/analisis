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
            const pie = new Chart('chart-pie', {
                type: 'pie',
                data: {
                    labels: ['Positif', 'Netral', 'Negatif'],
                    datasets: [{
                        data: ['positif', 'netral', 'negatif'].map(s => counts.all[s]),
                        backgroundColor: COLORS,
                        borderColor: BORDERS,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });

            document.getElementById('walletFilterPie').addEventListener('change', e => {
                pie.data.datasets[0].data = ['positif', 'netral', 'negatif'].map(s => counts[e.target.value]
                    [s]);
                pie.update();
            });

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
@endpush
