<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>COCAINE - Dashboard Celengan Pintar</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>

    <style>
        .switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 34px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked+.slider {
            background-color: #dc3545;
        }

        /* Merah untuk UNLOCK/Security Alert */
        input:checked+.slider:before {
            transform: translateX(26px);
        }

        #status-text {
            font-weight: bold;
            margin-top: 10px;
            display: block;
        }

        .card-title-icon {
            font-size: 1.2rem;
            margin-right: 10px;
        }
    </style>
</head>

<body class="bg-light">
    <div class="container mt-5">
        <div class="text-center mb-4">
            <h2 class="fw-bold text-primary">COCAINE Dashboard</h2>
            <p class="text-muted">Coin Cash Investment - Intelligent IoT Savings System</p>
        </div>

        <div class="row mt-4">
            <div class="col-md-7">
                <div class="card p-3 shadow border-0">
                    <h5 class="fw-bold border-bottom pb-2">Real-time Monitoring</h5>
                    <div class="row mt-3">
                        <div class="col-6">
                            <p class="mb-1 text-muted small">Total Saldo Terdeteksi</p>
                            <h3 class="fw-bold">Rp <span id="val-saldo">0</span></h3>
                        </div>
                        <div class="col-6">
                            <p class="mb-1 text-muted small">Lokasi (GPS)</p>
                            <p class="fw-bold" id="val-gps">Menunggu Data...</p>
                        </div>
                    </div>
                    <div class="mt-3">
                        <p class="mb-1 text-muted small">Deteksi Sensor Terakhir</p>
                        <ul class="list-unstyled">
                            <li>🪙 Koin (IR): <span id="val-koin" class="fw-bold">0</span></li>
                            <li>💵 Kertas (TCS3200): <span id="val-kertas" class="fw-bold">0</span></li>
                        </ul>
                    </div>
                    <small class="text-muted" id="last-update"></small>
                </div>
            </div>

            <div class="col-md-5">
                <div class="card p-3 shadow border-0 text-center">
                    <h5 class="fw-bold border-bottom pb-2">Keamanan & Akses</h5>
                    <div class="py-4">
                        <p class="mb-2">Status Solenoid (Doorlock)</p>
                        <label class="switch">
                            <input type="checkbox" id="btn-solenoid">
                            <span class="slider"></span>
                        </label>
                        <span id="status-text">TERKUNCI</span>
                    </div>
                    <div class="alert alert-info py-1 small">
                        Akses fisik memerlukan verifikasi PIN pada Numpad alat.
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <div class="card p-3 shadow border-0">
                    <h5 class="fw-bold mb-3">Analisis Akumulasi Tabungan</h5>
                    <div class="row">
                        <div class="col-md-8">
                            <canvas id="chartSaldo" style="max-height: 300px;"></canvas>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded text-center">
                                <h6>Target Tabungan</h6>
                                <h4 class="text-success" id="val-target">Rp 1.000.000</h4>
                                <div class="progress mt-3">
                                    <div id="prog-bar" class="progress-bar bg-success" role="progressbar"
                                        style="width: 0%"></div>
                                </div>
                                <small id="prog-text" class="mt-2 d-block">0% Tercapai</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4 mb-5">
            <div class="col-12">
                <div class="card p-3 shadow border-0">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold mb-0">Log Aktivitas & Transaksi</h5>
                        <div class="d-flex gap-2">
                            <select class="form-select form-select-sm" id="filterType" onchange="loadTableData()">
                                <option value="">Semua Aktivitas</option>
                                <option value="Koin">Pemasukan Koin</option>
                                <option value="Kertas">Pemasukan Kertas</option>
                                <option value="Keamanan">Log Keamanan</option>
                            </select>
                            <button class="btn btn-sm btn-outline-success" onclick="exportExcel()">Excel</button>
                            <button class="btn btn-sm btn-outline-danger" onclick="exportPDF()">PDF</button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover table-sm border">
                            <thead class="table-light">
                                <tr>
                                    <th>Waktu</th>
                                    <th>Kategori</th>
                                    <th>Detail Aktivitas</th>
                                    <th>Nilai</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="tableBody">
                                <tr>
                                    <td colspan="5" class="text-center">Memuat log sistem...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Logika Integrasi Sensor & Saldo
        let currentSaldo = 0;
        const targetSaldo = 1000000; // Contoh Target

        function fetchLatestCOCAINEData() {
            fetch('/api/latest') // Sesuaikan endpoint API Laravel Anda
                .then(res => res.json())
                .then(json => {
                    const d = json.data;

                    // Update UI Sensor
                    document.getElementById('val-saldo').innerText = formatRupiah(d.total_saldo);
                    document.getElementById('val-koin').innerText = d.count_koin + " unit";
                    document.getElementById('val-kertas').innerText = d.count_kertas + " lembar";
                    document.getElementById('val-gps').innerText = d.lat + ", " + d.lng;

                    // Update Progress Bar
                    let percent = (d.total_saldo / targetSaldo) * 100;
                    document.getElementById('prog-bar').style.width = Math.min(percent, 100) + "%";
                    document.getElementById('prog-text').innerText = percent.toFixed(1) + "% Tercapai";

                    // Update Solenoid Status
                    const solenoid = document.getElementById('btn-solenoid');
                    const txt = document.getElementById('status-text');
                    solenoid.checked = (d.solenoid_status == 1);
                    txt.innerText = d.solenoid_status == 1 ? "TERBUKA" : "TERKUNCI";
                    txt.style.color = d.solenoid_status == 1 ? "#28a745" : "#dc3545";

                    document.getElementById('last-update').innerText = 'Sinkronisasi Terakhir: ' + new Date().toLocaleTimeString('id-ID');
                })
                .catch(err => console.error("API Error:", err));
        }

        function formatRupiah(angka) {
            return new Intl.NumberFormat('id-ID').format(angka);
        }

        // Grafik Akumulasi Saldo (Line Chart)
        const ctx = document.getElementById('chartSaldo').getContext('2d');
        const saldoChart = new Chart(ctx, {
            type: 'line',
            data: { labels: [], datasets: [{ label: 'Saldo (Rp)', data: [], borderColor: '#0d6efd', tension: 0.3, fill: true, backgroundColor: 'rgba(13, 110, 253, 0.1)' }] },
            options: { responsive: true, scales: { y: { beginAtZero: true } } }
        });

        // Toggle Solenoid (Manual Trigger dari Web)
        document.getElementById('btn-solenoid').onchange = function () {
            const status = this.checked ? 1 : 0;
            fetch('/api/control-solenoid', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: JSON.stringify({ open: status })
            }).then(() => alert(status ? "Solenoid Terbuka!" : "Solenoid Terkunci!"));
        };

        // Polling Data
        setInterval(fetchLatestCOCAINEData, 3000);
        fetchLatestCOCAINEData();
    </script>
</body>

</html>