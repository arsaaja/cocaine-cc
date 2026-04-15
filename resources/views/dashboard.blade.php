<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COCAINE - Dashboard Celengan Pintar</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

    <style>
        :root {
            --sidebar-width: 260px;
            --primary-color: #0d6efd;
            --dark-card: #ffffff;
            --bg-body: #f8f9fa;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-body);
            color: #2d3436;
        }

        /* Sidebar Navigation */
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            background: #1e272e;
            color: white;
            padding: 20px;
            z-index: 1000;
        }

        .main-content {
            margin-left: var(--sidebar-width);
            padding: 30px;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.7);
            padding: 12px 15px;
            border-radius: 10px;
            margin-bottom: 5px;
            transition: all 0.3s;
        }

        .nav-link:hover,
        .nav-link.active {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }

        /* Card Customization */
        .card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 15px;
        }

        /* Toggle Switch Premium */
        .switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 28px;
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
            background-color: #cbd5e0;
            transition: .4s;
            border-radius: 34px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 20px;
            width: 20px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked+.slider {
            background-color: #2ecc71;
        }

        input:checked+.slider:before {
            transform: translateX(22px);
        }

        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: bold;
        }

        /* Header Clock */
        #header-clock {
            font-weight: 600;
            color: #636e72;
        }

        @media (max-width: 992px) {
            .sidebar {
                display: none;
            }

            .main-content {
                margin-left: 0;
            }
        }


        /* Jam Dashboard Premium */
        #header-clock {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #ffffff, #f1f3f5);
            padding: 10px 16px;
            border-radius: 14px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(0, 0, 0, 0.05);
            min-width: 220px;
        }

        #header-clock .clock-date {
            font-size: 13px;
            color: #6c757d;
        }

        #header-clock .clock-time {
            font-size: 22px;
            font-weight: 700;
            color: #0d6efd;
            letter-spacing: 1px;
        }


        /* Efek hover jam */
        #header-clock {
            transition: all 0.3s ease;
            cursor: pointer;
        }

        #header-clock:hover {
            transform: translateY(-3px) scale(1.03);
            box-shadow: 0 10px 25px rgba(13, 110, 253, 0.25);
            border-color: rgba(13, 110, 253, 0.3);
        }
    </style>
</head>

<body>

    <div class="sidebar">
        <div class="d-flex align-items-center mb-4 px-2">
            <i class="bi bi-safe2-fill text-primary fs-3 me-2"></i>
            <h4 class="fw-bold mb-0">COCAINE</h4>
        </div>
        <nav class="nav flex-column">
            <a class="nav-link active" href="/"><i class="bi bi-grid-1x2-fill me-2"></i> Dashboard</a>
            <a class="nav-link" href="/riwayat"><i class="bi bi-clock-history me-2"></i> Riwayat</a>
            <a class="nav-link" href="/lokasi"><i class="bi bi-geo-alt-fill me-2"></i> Lokasi Alat</a>
            <a class="nav-link" href="/pengaturan"><i class="bi bi-gear-fill me-2"></i> Pengaturan</a>
        </nav>
        <div class="mt-auto pt-5 px-2">
            <div class="p-3 bg-primary rounded-4 text-center">
                <small class="d-block mb-2">Target Tabungan</small>
                <h6 class="fw-bold mb-0">Rp 1.000.000</h6>
            </div>
        </div>
    </div>

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-0">Ringkasan Sistem</h4>
                <p class="text-muted small mb-0" id="last-update">Menghubungkan ke alat...</p>
            </div>
            <div id="header-clock" class="d-none d-md-block"></div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card p-3">
                    <div class="stat-icon bg-primary-subtle text-primary">
                        <i class="bi bi-wallet2"></i>
                    </div>
                    <small class="text-muted d-block">Total Saldo</small>
                    <h4 class="fw-bold mb-0">Rp <span id="val-saldo">0</span></h4>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card p-3">
                    <div class="stat-icon bg-success-subtle text-success">
                        <i class="bi bi-coin"></i>
                    </div>
                    <small class="text-muted d-block">Koin Terdeteksi</small>
                    <h4 class="fw-bold mb-0"><span id="val-koin">0</span> <small class="fs-6 fw-normal">unit</small>
                    </h4>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card p-3">
                    <div class="stat-icon bg-warning-subtle text-warning">
                        <i class="bi bi-cash-stack"></i>
                    </div>
                    <small class="text-muted d-block">Uang Kertas</small>
                    <h4 class="fw-bold mb-0"><span id="val-kertas">0</span> <small class="fs-6 fw-normal">lembar</small>
                    </h4>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card p-3">
                    <div class="stat-icon bg-info-subtle text-info">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <small class="text-muted d-block">Status Keamanan</small>
                    <div class="d-flex align-items-center gap-2 mt-1">
                        <label class="switch">
                            <input type="checkbox" id="btn-solenoid">
                            <span class="slider"></span>
                        </label>
                        <span id="status-text" class="small fw-bold">LOCKED</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-lg-8">
                <div class="card p-4 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold mb-0">Grafik Akumulasi</h5>
                        <div class="badge bg-light text-dark border">7 Hari Terakhir</div>
                    </div>
                    <canvas id="chartSaldo" style="max-height: 320px;"></canvas>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card p-4 h-100">
                    <h5 class="fw-bold mb-3">Target & Lokasi</h5>
                    <div class="p-3 bg-light rounded-4 mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <small class="fw-bold">Progress Target</small>
                            <small id="prog-text" class="text-primary fw-bold">0%</small>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div id="prog-bar" class="progress-bar progress-bar-striped progress-bar-animated"
                                role="progressbar" style="width: 0%"></div>
                        </div>
                    </div>
                    <div class="p-3 border rounded-4">
                        <small class="text-muted d-block mb-1"><i class="bi bi-geo-alt"></i> Koordinat GPS</small>
                        <p class="fw-bold mb-0 small" id="val-gps">Mencari Sinyal Satelit...</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card p-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
                <h5 class="fw-bold mb-0">Log Aktivitas Terbaru</h5>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-light border" onclick="exportExcel()"><i
                            class="bi bi-file-earmark-excel me-1"></i> Excel</button>
                    <button class="btn btn-sm btn-dark" onclick="window.print()"><i class="bi bi-printer me-1"></i>
                        Cetak</button>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="border-0">Waktu</th>
                            <th class="border-0">Kategori</th>
                            <th class="border-0">Aktivitas</th>
                            <th class="border-0">Nilai</th>
                            <th class="border-0 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <tr>
                            <td colspan="5" class="text-center py-4">Memuat data transaksi...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        // 1. Logic Jam Digital Header
        function updateClock() {
            const now = new Date();
            const hari = now.toLocaleDateString('id-ID', { weekday: 'long' });
            const tanggal = now.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
            const jam = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });

            document.getElementById('header-clock').innerHTML = `
                <div style="line-height: 1.2;">
                    <div class="clock-date">${hari}, ${tanggal}</div>
                    <div class="clock-time">${jam}</div>
                </div>`;
        }
        setInterval(updateClock, 1000);

        // 2. Inisialisasi Chart Kosong (Data menyusul dari database)
        const ctx = document.getElementById('chartSaldo').getContext('2d');
        const saldoChart = new Chart(ctx, {
            type: 'line',
            data: { labels: [], datasets: [{ label: 'Tabungan Harian', data: [], borderColor: '#0d6efd', backgroundColor: 'rgba(13, 110, 253, 0.1)', fill: true, tension: 0.4, borderWidth: 3, pointRadius: 4 }] },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { grid: { display: false }, ticks: { callback: v => 'Rp ' + v.toLocaleString('id-ID') } }, x: { grid: { display: false } } } }
        });

        // 3. Logic Tarik Data dari Controller Baru Temen Lu
        function fetchDashboardData() {
            // A. Narik Data Ringkasan Atas
            fetch('/api/dashboard/data')
                .then(res => res.json())
                .then(json => {
                    const d = json.data;
                    document.getElementById('val-saldo').innerText = new Intl.NumberFormat('id-ID').format(d.total_balance);
                    
                    // Temen lu ngirim total nominal, bukan satuan fisik
                    document.getElementById('val-koin').innerText = "Rp " + new Intl.NumberFormat('id-ID').format(d.breakdown.koin);
                    document.getElementById('val-kertas').innerText = "Rp " + new Intl.NumberFormat('id-ID').format(d.breakdown.kertas);
                    
                    // Status Keamanan (Solenoid)
                    document.getElementById('status-text').innerText = d.active_devices > 0 ? "ONLINE" : "OFFLINE";
                    document.getElementById('status-text').className = d.active_devices > 0 ? "small fw-bold text-success" : "small fw-bold text-danger";
                    document.getElementById('val-gps').innerText = d.active_devices > 0 ? "Terkoneksi" : "Terputus";

                    // Update Progress Bar
                    let percent = (d.total_balance / 1000000) * 100;
                    document.getElementById('prog-bar').style.width = Math.min(percent, 100) + "%";
                    document.getElementById('prog-text').innerText = percent.toFixed(1) + "%";
                })
                .catch(err => console.error("Gagal ngambil ringkasan data:", err));

            // B. Narik Data Tabel Log Aktivitas Bawah
            fetch('/api/dashboard/log')
                .then(res => res.json())
                .then(json => {
                    const tableBody = document.getElementById('tableBody');
                    tableBody.innerHTML = '';
                    
                    if (json.data.length === 0) {
                        tableBody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-muted">Belum ada log aktivitas</td></tr>';
                    } else {
                        json.data.forEach(log => {
                            let isDanger = log.action.includes('WRONG') || log.action.includes('BAHAYA');
                            let badgeType = isDanger ? 'bg-danger' : 'bg-success';
                            let statusIcon = isDanger ? '<i class="bi bi-x-circle-fill text-danger"></i>' : '<i class="bi bi-check-circle-fill text-success"></i>';
                            
                            tableBody.innerHTML += `
                                <tr>
                                    <td><span class="text-muted small">${log.time}</span></td>
                                    <td><span class="badge ${badgeType}">${log.action}</span></td>
                                    <td class="fw-bold">${log.description}</td>
                                    <td class="text-secondary small">${log.device}</td>
                                    <td class="text-center">${statusIcon}</td>
                                </tr>
                            `;
                        });
                    }
                })
                .catch(err => console.error("Gagal ngambil log aktivitas:", err));

            // C. Narik Data Grafik Tabungan
            fetch('/api/dashboard/chart/1/all') // Default nyari device ID 1
                .then(res => res.json())
                .then(json => {
                    if(json.data.length > 0) {
                        let labels = [];
                        let totals = [];
                        json.data.forEach(item => {
                            // Potong tulisan tanggal biar nggak kepanjangan di grafik
                            let formatTanggal = new Date(item.date).toLocaleDateString('id-ID', { month: 'short', day: 'numeric' });
                            labels.push(formatTanggal);
                            totals.push(item.total);
                        });
                        saldoChart.data.labels = labels;
                        saldoChart.data.datasets[0].data = totals;
                        saldoChart.update();
                    }
                })
                .catch(err => console.error("Gagal ngambil data grafik:", err));

            // Update keterangan waktu sinkronisasi
            document.getElementById('last-update').innerText = 'Sinkronisasi: ' + new Date().toLocaleTimeString('id-ID');
        }

        // Tembak fetch saat web dibuka, lalu ulang tiap 3 detik
        fetchDashboardData();
        setInterval(fetchDashboardData, 3000);

        // Fitur cetak Excel Dummy (kalau lu ditanya dosen)
        function exportExcel() {
            alert('Fitur Export Excel akan siap di tahap selanjutnya!');
        }
    </script>
</body>

</html>