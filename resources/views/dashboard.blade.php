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
            background: #050a09;
            color: white;
            padding: 20px;
            z-index: 1000;
            border-right: 1px solid rgba(255, 255, 255, 0.05);
        }

        .sidebar img {
            border-radius: 8px;
            object-fit: contain;
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

        .nav-link:hover,
        .nav-link.active {
            background: var(--primary-color);
            color: white;
        }

        /* Target Box Sidebar */
        .target-box {
            cursor: pointer;
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.1);
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
            border: 1px solid rgba(255, 255, 255, 0.1);
            text-align: right;
        }

        .clock-time {
            font-size: 20px;
            font-weight: 700;
            color: var(--accent-color);
        }

        .clock-date {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.6);
        }

        .profile-card {
            cursor: pointer;
            transition: all 0.2s;
            background-color: var(--card-bg) !important;
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
        }

        .profile-card:hover {
            background-color: var(--primary-color) !important;
        }

        .dropdown-menu {
            background-color: var(--card-bg);
            border: 1px solid rgba(255, 255, 255, 0.1);
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

        .dropdown-header {
            color: var(--accent-color);
        }

        .dropdown-divider {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        #profileModal .input-group-text {
            border-radius: 12px 0 0 12px;
        }

        #profileModal .form-control {
            border-radius: 0 12px 12px 0;
        }

        #profileModal .form-control::placeholder {
            color: rgba(255, 255, 255, 0.3);
        }

        #profileModal .form-control:focus {
            background-color: #050a09 !important;
        }

        .card {
            background-color: var(--card-bg);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 16px;
            color: white;
        }

        .form-control:focus {
            background-color: rgba(255, 255, 255, 0.05);
            color: white;
            border-color: var(--accent-color, #B0E4CC) !important;
            box-shadow: 0 0 0 0.25rem rgba(176, 228, 204, 0.25);
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.3) !important;
        }

        .dashboard-card {
            transition: 0.3s;
        }

        .dashboard-card:hover {
            transform: translateY(-10px);
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

        .bg-primary-subtle {
            background-color: rgba(40, 90, 72, 0.3) !important;
            color: var(--accent-color) !important;
        }

        .text-muted {
            color: rgba(255, 255, 255, 0.5) !important;
        }

        #txt-status-alat {
            transition: color 0.3s ease;
        }

        .switch {
            position: relative;
            display: inline-block;
            width: 45px;
            height: 24px;
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
            background-color: #333;
            transition: .4s;
            border-radius: 34px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked+.slider {
            background-color: var(--secondary-color);
        }

        input:checked+.slider:before {
            transform: translateX(21px);
        }

        .progress {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .progress-bar {
            background-color: var(--secondary-color);
        }

        .bg-light {
            background-color: rgba(255, 255, 255, 0.05) !important;
        }
    </style>
</head>

<body>

    <div class="sidebar d-flex flex-column">
        <div class="d-flex align-items-center mb-4 px-2">
            <img src="{{ asset('images/logo/Frame 7.png') }}" alt="Logo COCAINE"
                style="width: 40px; height: auto; margin-right: 10px;">
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
                    <div class="d-flex align-items-center gap-2 p-3 rounded-4 shadow-sm profile-card"
                        data-bs-toggle="dropdown">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                            style="width: 38px; height: 38px; background-color: var(--primary-color) !important;">
                            <i class="bi bi-person-fill"></i>
                        </div>
                        <div class="me-2 d-none d-md-block">
                            <div class="fw-bold mb-0 small text-capitalize">{{ Auth::user()->username }}</div>
                            <div class="text-muted" style="font-size: 10px;">User Terverifikasi</div>
                        </div>
                        <i class="bi bi-chevron-down small text-muted me-1"></i>
                    </div>
                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-4 mt-2">
                        <li>
                            <h6 class="dropdown-header small">Akun: {{ Auth::user()->email }}</h6>
                        </li>
                        <li><a class="dropdown-item d-flex align-items-center gap-2" href="#" data-bs-toggle="modal"
                                data-bs-target="#profileModal">
                                <i class="bi bi-person-fill"></i> Edit Profil</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
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
                <div class="card p-3 dashboard-card">
                    <div class="stat-icon bg-primary-subtle"><i class="bi bi-wallet2"></i></div>
                    <small class="text-muted">Total Saldo</small>
                    <h4 class="fw-bold mb-0">Rp <span id="val-saldo"
                            data-saldo="{{ $totalSaldo ?? 0 }}">{{ number_format($totalSaldo ?? 0, 0, ',', '.') }}</span>
                    </h4>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card p-3 dashboard-card">
                    <div class="stat-icon" style="background-color: var(--accent-color); color: var(--primary-color);">
                        <i class="bi bi-coin"></i>
                    </div>
                    <small class="text-muted">Koin</small>
                    <h4 class="fw-bold mb-0">Rp <span
                            id="val-koin">{{ number_format($totalKoin ?? 0, 0, ',', '.') }}</span></h4>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card p-3 dashboard-card">
                    <div class="stat-icon" style="background-color: rgba(255,255,255,0.1); color: white;"><i
                            class="bi bi-cash-stack"></i></div>
                    <small class="text-muted">Uang Kertas</small>
                    <h4 class="fw-bold mb-0">Rp <span
                            id="val-kertas">{{ number_format($totalKertas ?? 0, 0, ',', '.') }}</span></h4>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card p-3 dashboard-card">
                    <div class="stat-icon" style="background-color: var(--accent-color); color: var(--primary-color);">
                        <i class="bi bi-shield-lock"></i>
                    </div>
                    <small class="text-muted">Keamanan</small>
                    <div class="d-flex align-items-center gap-2 mt-1">
                        <label class="switch">
                            <input type="checkbox" id="check-solenoid" checked>
                            <span class="slider"></span>
                        </label>
                        <span id="txt-status-alat" class="small fw-bold"
                            style="color: var(--accent-color);">LOCKED</span>
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
                            <div class="progress mb-2" style="height: 12px; border-radius: 10px;">
                                <div id="prog-bar" class="progress-bar progress-bar-striped progress-bar-animated"
                                    role="progressbar"></div>
                            </div>
                            <small class="d-block text-muted text-center pt-1" id="prog-remaining"
                                style="font-size: 11px;"></small>
                        </div>
                    </div>
                    <div id="monitor-target-empty"
                        class="text-center py-4 border border-secondary rounded-4 bg-light mb-3">
                        <i class="bi bi-plus-circle text-muted fs-2"></i>
                        <p class="text-muted small mt-2">Belum ada target yang aktif</p>
                    </div>
                    <div class="p-3 border border-secondary rounded-4 bg-light text-center shadow-sm">
                        <small class="text-muted d-block mb-1">Status GPS</small>
                        <p class="fw-bold mb-0 small" style="color: var(--accent-color);"><i
                                class="bi bi-geo-alt-fill"></i> Aktif & Terpantau</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Target Modal -->
    <div class="modal fade" id="targetModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border border-secondary shadow-lg bg-dark text-white">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold">Rencana Tabungan</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small text-secondary">Nama Rencana</label>
                        <input type="text" id="in-target-title" class="form-control rounded-3"
                            placeholder="Contoh: Beli Sepatu" style="border-width: 1px;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small text-secondary">Nominal Target (Rp)</label>
                        <input type="number" id="in-target-amount"
                            class="form-control form-control-lg bg-transparent text-white rounded-3 border-secondary"
                            placeholder="Masukkan angka" style="border-width: 1px;">
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-danger rounded-3 me-auto" onclick="handleClearTarget()"
                        id="btn-clear-target" style="display: none;">Hapus Target</button>
                    <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary rounded-3 px-4" onclick="handleSaveTarget()"
                        style="background-color: var(--primary-color); border:none;">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Profile Modal -->
    <div class="modal fade" id="profileModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0" style="background-color: var(--card-bg); border-radius: 20px;">
                <div class="modal-header border-bottom border-secondary border-opacity-10">
                    <h5 class="modal-title fw-bold text-white">Pengaturan Profil & Alat</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="formUpdateProfile">
                        <div class="mb-4">
                            <label class="form-label small text-muted">PIN Celengan Baru (6 Digit)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-dark border-0 text-primary"><i
                                        class="bi bi-shield-lock"></i></span>
                                <input type="password" id="edit-pin" maxlength="6" inputmode="numeric"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                    class="form-control bg-dark border-0 text-white shadow-none"
                                    placeholder="Masukkan PIN baru">
                            </div>
                        </div>

                        <hr class="border-secondary border-opacity-10 my-4">

                        <div class="mb-3">
                            <label class="form-label small text-muted">Username Baru</label>
                            <div class="input-group">
                                <span class="input-group-text bg-dark border-0 text-primary"><i
                                        class="bi bi-person"></i></span>
                                <input type="text" id="edit-username"
                                    class="form-control bg-dark border-0 text-white shadow-none"
                                    value="{{ Auth::user()->username }}">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small text-muted">Password Akun Baru</label>
                            <div class="input-group">
                                <span class="input-group-text bg-dark border-0 text-primary"><i
                                        class="bi bi-key"></i></span>
                                <input type="password" id="edit-password"
                                    class="form-control bg-dark border-0 text-white shadow-none"
                                    placeholder="Password baru">
                            </div>
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary fw-bold py-2 rounded-3">Simpan
                                Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let config = {
            targetAmount: Number("{{ $targetAmount ?? 0 }}") || null,
            targetTitle: "{{ $targetTitle ?? '' }}",
            currentBalance: parseInt(document.getElementById('val-saldo').getAttribute('data-saldo')) || 0
        };

        // Form Update Profile
        document.getElementById('formUpdateProfile').addEventListener('submit', function (e) {
            e.preventDefault();

            const username = document.getElementById('edit-username').value;
            const password = document.getElementById('edit-password').value;
            const pin = document.getElementById('edit-pin').value;

            fetch("{{ route('profile.update') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    username: username,
                    password: password,
                    pin: pin
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert(data.message);
                        document.querySelectorAll('.text-capitalize').forEach(el => el.innerText = data.username);
                        document.querySelector('h4.fw-bold.mb-0').innerText = "Halo, " + data.username + "!";
                        bootstrap.Modal.getInstance(document.getElementById('profileModal')).hide();
                    } else {
                        alert('Gagal menyimpan: ' + (data.message || 'Terjadi kesalahan'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Gagal terhubung ke server');
                });
        });

        // Solenoid Control Switch
        document.getElementById('check-solenoid').addEventListener('change', function () {
            const statusText = document.getElementById('txt-status-alat');
            const isChecked = this.checked;

            if (isChecked) {
                statusText.innerText = 'LOCKED';
                statusText.style.color = 'var(--accent-color)';
            } else {
                statusText.innerText = 'UNLOCKED';
                statusText.style.color = '#ff4757';
            }

            // Sync dengan backend API celengan
            fetch("/api/device/toggle-lock", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ locked: isChecked })
            })
                .then(res => res.json())
                .then(data => {
                    if (data.status !== 'success') console.warn("Sinkronisasi alat gagal");
                })
                .catch(err => console.error("Error toggle device:", err));
        });

        // Realtime Clock Function
        function runClock() {
            const now = new Date();
            document.getElementById('txt-date').innerText = now.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
            document.getElementById('txt-time').innerText = now.toLocaleTimeString('id-ID');
        }
        setInterval(runClock, 1000);

        // UI Progress Target Update
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
                const remainingAmount = Math.max(config.targetAmount - config.currentBalance, 0);
                let remainingText = "";

                if (remainingAmount > 0) {
                    remainingText = `Sisa kekurangan: <strong>Rp ${new Intl.NumberFormat('id-ID').format(remainingAmount)}</strong> lagi.`;
                } else {
                    remainingText = `<span class="text-success fw-bold"><i class="bi bi-check-circle-fill"></i> Target Telah Tercapai!</span>`;
                }

                document.getElementById('prog-bar').style.width = percent + "%";
                document.getElementById('prog-percent').innerText = percent + "%";
                document.getElementById('prog-label').innerText = "Progres: " + config.targetTitle;
                document.getElementById('sidebar-target-text').innerText = "Rp " + new Intl.NumberFormat('id-ID').format(config.targetAmount);
                document.getElementById('sidebar-target-title').innerText = config.targetTitle;
                document.getElementById('prog-remaining').innerHTML = remainingText;
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
                fetch("/dashboard/save-target", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        target_amount: amountInput,
                        target_title: titleInput || "Rencana Saya"
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            config.targetAmount = amountInput;
                            config.targetTitle = titleInput || "Rencana Saya";
                            updateUIProgress();
                            bootstrap.Modal.getInstance(document.getElementById('targetModal')).hide();
                        } else {
                            alert("Gagal menyimpan ke server.");
                        }
                    })
                    .catch(error => console.error('Error:', error));
            } else {
                alert("Masukkan nominal target yang valid!");
            }
        }

        function handleClearTarget() {
            if (confirm("Apakah Anda yakin ingin menghapus target tabungan?")) {
                fetch("/dashboard/clear-target", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            config.targetAmount = null;
                            config.targetTitle = "";
                            updateUIProgress();
                            bootstrap.Modal.getInstance(document.getElementById('targetModal')).hide();
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }
        }

        // Initialize Chart.js
        const ctx = document.getElementById('chartSaldo').getContext('2d');
        let myChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'Saldo (Rp)',
                    data: [],
                    borderColor: '#408A71',
                    backgroundColor: 'rgba(176, 228, 204, 0.1)',
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: '#B0E4CC'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        ticks: {
                            color: 'rgba(255,255,255,0.5)',
                            callback: function (value) { return 'Rp ' + value.toLocaleString('id-ID'); }
                        },
                        grid: { color: 'rgba(255,255,255,0.05)' }
                    },
                    x: {
                        ticks: { color: 'rgba(255,255,255,0.5)' },
                        grid: { display: false }
                    }
                }
            }
        });

        // Realtime Stats Updates from API
        function updateStats() {
            fetch('/api/dashboard/data')
                .then(response => response.json())
                .then(res => {
                    const data = res.data;
                    document.getElementById('val-saldo').innerText = data.total_balance.toLocaleString('id-ID');
                    document.getElementById('val-koin').innerText = data.breakdown.koin.toLocaleString('id-ID');
                    document.getElementById('val-kertas').innerText = data.breakdown.kertas.toLocaleString('id-ID');

                    config.currentBalance = data.total_balance;
                    updateUIProgress();

                    if (data.chart_data && data.chart_labels) {
                        myChart.data.labels = data.chart_labels;
                        myChart.data.datasets[0].data = data.chart_data;
                        myChart.update(); // FIXED: Perbaikan dari sintaks terpotong 'myChar'
                    }
                })
                .catch(error => console.error('Error fetching stats:', error));
        }

        // Run functions immediately on load & pool every 5 seconds
        window.addEventListener('DOMContentLoaded', () => {
            runClock();
            updateUIProgress();
            updateStats();
            setInterval(updateStats, 5000);
        });
    </script>
</body>

</html>