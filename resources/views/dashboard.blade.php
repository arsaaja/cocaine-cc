<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COCAINE - Dashboard Celengan Pintar</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>

    <style>
        :root {
            --sidebar-width: 260px;
            --primary-color: #285A48;
            --secondary-color: #408A71;
            --accent-color: #B0E4CC;
            --dark-bg: #091413;
            --bg-body: #091413;
            --card-bg: #121f1d;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-body);
            color: #ffffff;
        }

        /* Sidebar Styling */
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            background: #050a09; /* Lebih gelap dari body */
            color: white;
            padding: 20px;
            z-index: 1000;
            border-right: 1px solid rgba(255,255,255,0.05);
        }

        .main-content {
            margin-left: var(--sidebar-width);
            padding: 30px;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.6);
            padding: 12px 15px;
            border-radius: 10px;
            margin-bottom: 5px;
            transition: all 0.3s;
        }

        .nav-link:hover, .nav-link.active {
            background: var(--primary-color);
            color: white;
        }

        /* Target Box Sidebar */
        .target-box {
            cursor: pointer;
            transition: all 0.3s ease;
            border: 1px solid rgba(255,255,255,0.1);
            min-height: 80px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background-color: var(--primary-color) !important;
        }

        .target-box:hover {
            background-color: var(--secondary-color) !important;
            transform: translateY(-5px);
        }

        /* Header Components */
        #header-clock {
            background: var(--card-bg);
            padding: 10px 20px;
            border-radius: 14px;
            border: 1px solid rgba(255,255,255,0.1);
            text-align: right;
        }

        .clock-time { font-size: 20px; font-weight: 700; color: var(--accent-color); }
        .clock-date { font-size: 12px; color: rgba(255,255,255,0.6); }

        .profile-card {
            cursor: pointer;
            transition: all 0.2s;
            background-color: var(--card-bg) !important;
            border: 1px solid rgba(255,255,255,0.1);
            color: white;
        }
        .profile-card:hover { background-color: var(--primary-color) !important; }
        
        .dropdown-menu {
            background-color: var(--card-bg);
            border: 1px solid rgba(255,255,255,0.1);
        }
        .dropdown-item {
            color: white;
            font-size: 14px;
            border-radius: 8px;
            margin: 0 8px;
            width: calc(100% - 16px);
        }
        .dropdown-item:hover {
            background-color: var(--primary-color);
            color: white;
        }
        .dropdown-header { color: var(--accent-color); }
        .dropdown-divider { border-top: 1px solid rgba(255,255,255,0.1); }

        .card { 
            background-color: var(--card-bg); 
            border: 1px solid rgba(255,255,255,0.05); 
            border-radius: 16px; 
            color: white;
        }

        .stat-icon {
            width: 48px; height: 48px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem; margin-bottom: 15px;
        }

        .bg-primary-subtle { background-color: rgba(40, 90, 72, 0.3) !important; color: var(--accent-color) !important; }
        .text-muted { color: rgba(255,255,255,0.5) !important; }

        .switch { position: relative; display: inline-block; width: 45px; height: 24px; }
        .switch input { opacity: 0; width: 0; height: 0; }
        .slider {
            position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0;
            background-color: #333; transition: .4s; border-radius: 34px;
        }
        .slider:before {
            position: absolute; content: ""; height: 18px; width: 18px; left: 3px; bottom: 3px;
            background-color: white; transition: .4s; border-radius: 50%;
        }
        input:checked + .slider { background-color: var(--secondary-color); }
        input:checked + .slider:before { transform: translateX(21px); }

        .progress { background-color: rgba(255,255,255,0.1); }
        .progress-bar { background-color: var(--secondary-color); }
        .bg-light { background-color: rgba(255,255,255,0.05) !important; }
    </style>
</head>

<body>

    <div class="sidebar d-flex flex-column">
        <div class="d-flex align-items-center mb-4 px-2">
            <i class="bi bi-safe2-fill fs-3 me-2" style="color: var(--accent-color);"></i>
            <h4 class="fw-bold mb-0">COCAINE</h4>
        </div>
        <nav class="nav flex-column mb-auto">
            <a class="nav-link active" href="/dashboard"><i class="bi bi-grid-1x2-fill me-2"></i> Dashboard</a>
            <a class="nav-link" href="/riwayat"><i class="bi bi-clock-history me-2"></i> Riwayat</a>
            <a class="nav-link" href="/lokasi"><i class="bi bi-geo-alt-fill me-2"></i> Lokasi Alat</a>
        </nav>

        <div class="px-2 pb-4">
            <div class="p-3 rounded-4 text-center target-box shadow-sm" onclick="openTargetModal()">
                <div id="target-display-active" style="display: none;">
                    <small class="d-block mb-2 text-white-50" id="sidebar-target-title">Target Tabungan</small>
                    <h6 class="fw-bold mb-1 text-white" id="sidebar-target-text">Rp 0</h6>
                </div>
                <div id="target-display-empty">
                    <h6 class="fw-bold mb-0 text-white">Tambahkan Target Anda!</h6>
                    <small class="text-white-50" style="font-size: 10px;">Klik untuk mengatur</small>
                </div>
            </div>
        </div>
    </div>

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-0">Halo, {{ Auth::user()->username }}!</h4>
                <p class="text-muted small mb-0">Pantau perkembangan tabunganmu di sini.</p>
            </div>
            
            <div class="d-flex align-items-center gap-3">
                <div class="dropdown">
                    <div class="d-flex align-items-center gap-2 p-2 rounded-4 shadow-sm profile-card" data-bs-toggle="dropdown">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background-color: var(--primary-color) !important;">
                            <i class="bi bi-person-fill"></i>
                        </div>
                        <div class="me-2 d-none d-md-block">
                            <div class="fw-bold mb-0 small text-capitalize">{{ Auth::user()->username }}</div>
                            <div class="text-muted" style="font-size: 10px;">User Terverifikasi</div>
                        </div>
                        <i class="bi bi-chevron-down small text-muted me-1"></i>
                    </div>
                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-4 mt-2">
                        <li><h6 class="dropdown-header small">Akun: {{ Auth::user()->email }}</h6></li>
                        <li><a class="dropdown-item py-2" href="#"><i class="bi bi-person me-2"></i> Profil</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item py-2 text-danger">
                                    <i class="bi bi-box-arrow-right me-2"></i> Keluar
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>

                <div id="header-clock">
                    <div class="clock-date" id="txt-date">...</div>
                    <div class="clock-time" id="txt-time">00:00:00</div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card p-3">
                    <div class="stat-icon bg-primary-subtle"><i class="bi bi-wallet2"></i></div>
                    <small class="text-muted">Total Saldo</small>
                    <h4 class="fw-bold mb-0">Rp <span id="val-saldo">0</span></h4>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card p-3">
                    <div class="stat-icon" style="background-color: var(--accent-color); color: var(--primary-color);"><i class="bi bi-coin"></i></div>
                    <small class="text-muted">Koin</small>
                    <h4 class="fw-bold mb-0">Rp <span id="val-koin">0</span></h4>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card p-3">
                    <div class="stat-icon" style="background-color: rgba(255,255,255,0.1); color: white;"><i class="bi bi-cash-stack"></i></div>
                    <small class="text-muted">Uang Kertas</small>
                    <h4 class="fw-bold mb-0">Rp <span id="val-kertas">0</span></h4>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card p-3">
                    <div class="stat-icon" style="background-color: var(--accent-color); color: var(--primary-color);"><i class="bi bi-shield-lock"></i></div>
                    <small class="text-muted">Keamanan</small>
                    <div class="d-flex align-items-center gap-2 mt-1">
                        <label class="switch">
                            <input type="checkbox" id="check-solenoid" checked>
                            <span class="slider"></span>
                        </label>
                        <span id="txt-status-alat" class="small fw-bold" style="color: var(--accent-color);">LOCKED</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-lg-8">
                <div class="card p-4 h-100">
                    <h5 class="fw-bold mb-4">Grafik Pertumbuhan Saldo</h5>
                    <canvas id="chartSaldo" style="max-height: 300px;"></canvas>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card p-4 h-100">
                    <h5 class="fw-bold mb-3">Monitoring Target</h5>
                    <div id="monitor-target-active" style="display: none;">
                        <div class="p-3 bg-light rounded-4 mb-3 border border-secondary">
                            <div class="d-flex justify-content-between mb-2">
                                <small class="fw-bold" id="prog-label">Progres Capaian</small>
                                <small class="fw-bold" id="prog-percent" style="color: var(--accent-color);">0%</small>
                            </div>
                            <div class="progress" style="height: 12px; border-radius: 10px;">
                                <div id="prog-bar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"></div>
                            </div>
                        </div>
                    </div>
                    <div id="monitor-target-empty" class="text-center py-4 border border-secondary rounded-4 bg-light mb-3">
                        <i class="bi bi-plus-circle text-muted fs-2"></i>
                        <p class="text-muted small mt-2">Belum ada target yang aktif</p>
                    </div>
                    <div class="p-3 border border-secondary rounded-4 bg-light text-center shadow-sm">
                        <small class="text-muted d-block mb-1">Status GPS</small>
                        <p class="fw-bold mb-0 small" style="color: var(--accent-color);"><i class="bi bi-geo-alt-fill"></i> Aktif & Terpantau</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="targetModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow-lg text-dark">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold">Rencana Tabungan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small text-muted">Nama Rencana</label>
                        <input type="text" id="in-target-title" class="form-control rounded-3" placeholder="Contoh: Beli Sepatu">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small text-muted">Nominal Target (Rp)</label>
                        <input type="number" id="in-target-amount" class="form-control form-control-lg rounded-3" placeholder="Masukkan angka">
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-danger rounded-3 me-auto" onclick="handleClearTarget()" id="btn-clear-target" style="display: none;">Hapus Target</button>
                    <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary rounded-3 px-4" onclick="handleSaveTarget()" style="background-color: var(--primary-color); border:none;">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let config = { targetAmount: null, targetTitle: "", currentBalance: 0 };

        function runClock() {
            const now = new Date();
            document.getElementById('txt-date').innerText = now.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
            document.getElementById('txt-time').innerText = now.toLocaleTimeString('id-ID');
        }
        setInterval(runClock, 1000);

        function updateUIProgress() {
            const emptyDisplaySidebar = document.getElementById('target-display-empty');
            const activeDisplaySidebar = document.getElementById('target-display-active');
            const emptyDisplayMonitor = document.getElementById('monitor-target-empty');
            const activeDisplayMonitor = document.getElementById('monitor-target-active');
            const btnClear = document.getElementById('btn-clear-target');

            if (config.targetAmount === null || config.targetAmount <= 0) {
                emptyDisplaySidebar.style.display = 'block';
                activeDisplaySidebar.style.display = 'none';
                emptyDisplayMonitor.style.display = 'block';
                activeDisplayMonitor.style.display = 'none';
                btnClear.style.display = 'none';
            } else {
                emptyDisplaySidebar.style.display = 'none';
                activeDisplaySidebar.style.display = 'block';
                emptyDisplayMonitor.style.display = 'none';
                activeDisplayMonitor.style.display = 'block';
                btnClear.style.display = 'block';

                const percent = Math.min((config.currentBalance / config.targetAmount) * 100, 100).toFixed(1);
                document.getElementById('prog-bar').style.width = percent + "%";
                document.getElementById('prog-percent').innerText = percent + "%";
                document.getElementById('prog-label').innerText = "Progres: " + config.targetTitle;
                document.getElementById('sidebar-target-text').innerText = "Rp " + new Intl.NumberFormat('id-ID').format(config.targetAmount);
                document.getElementById('sidebar-target-title').innerText = config.targetTitle;
            }
        }

        function openTargetModal() {
            const modal = new bootstrap.Modal(document.getElementById('targetModal'));
            document.getElementById('in-target-amount').value = config.targetAmount;
            document.getElementById('in-target-title').value = config.targetTitle;
            modal.show();
        }

        function handleSaveTarget() {
            const amountInput = parseInt(document.getElementById('in-target-amount').value);
            const titleInput = document.getElementById('in-target-title').value;

            if (amountInput > 0) {
                config.targetAmount = amountInput;
                config.targetTitle = titleInput || "Rencana Saya";
                updateUIProgress();
                bootstrap.Modal.getInstance(document.getElementById('targetModal')).hide();
            } else {
                alert("Masukkan nominal target yang valid!");
            }
        }

        function handleClearTarget() {
            if (confirm("Apakah Anda yakin ingin menghapus target tabungan?")) {
                config.targetAmount = null;
                config.targetTitle = "";
                updateUIProgress();
                bootstrap.Modal.getInstance(document.getElementById('targetModal')).hide();
            }
        }

        const ctx = document.getElementById('chartSaldo').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
                datasets: [{ 
                    label: 'Saldo', 
                    data: [0, 50000, 120000, 200000, 450000, 600000, 750000], 
                    borderColor: '#408A71', 
                    backgroundColor: 'rgba(176, 228, 204, 0.1)',
                    fill: true,
                    tension: 0.4 
                }]
            },
            options: { 
                responsive: true, 
                maintainAspectRatio: false,
                plugins: {
                    legend: { labels: { color: 'white' } }
                },
                scales: {
                    y: { ticks: { color: 'rgba(255,255,255,0.5)' }, grid: { color: 'rgba(255,255,255,0.05)' } },
                    x: { ticks: { color: 'rgba(255,255,255,0.5)' }, grid: { display: false } }
                }
            }
        });

        setInterval(() => {
            config.currentBalance = 750000; 
            document.getElementById('val-saldo').innerText = new Intl.NumberFormat('id-ID').format(config.currentBalance);
            updateUIProgress();
        }, 3000);

        updateUIProgress();
    </script>
</body>
</html>