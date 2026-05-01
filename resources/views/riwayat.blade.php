<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>COCAINE - Riwayat Aktivitas</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --sidebar-width: 260px;
            --primary-color: #285A48;
            --secondary-color: #408A71;
            --accent-color: #B0E4CC;
            --dark-bg: #091413;
            --card-bg: #121f1d;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--dark-bg);
            color: #ffffff;
        }

        /* --- Sidebar --- */
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            background: #050a09;
            color: white;
            padding: 20px;
            border-right: 1px solid rgba(255, 255, 255, 0.05);
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.7);
            padding: 12px 15px;
            border-radius: 10px;
            margin-bottom: 5px;
            text-decoration: none;
            display: block;
            transition: all 0.3s;
        }

        .nav-link:hover,
        .nav-link.active {
            background: var(--primary-color);
            color: white;
        }

        /* --- Main Content --- */
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 30px;
        }

        .card {
            border: none;
            border-radius: 16px;
            background: var(--card-bg);
            color: white;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.05);
            margin-bottom: 25px;
        }

        .table {
            color: white;
        }

        .table-dark {
            background-color: #050a09 !important;
        }

        td {
            border-bottom: 1px solid rgba(255, 255, 255, 0.05) !important;
            padding: 15px !important;
        }

        .text-primary {
            color: var(--accent-color) !important;
        }

        .badge-debit {
            background-color: var(--primary-color);
            color: white;
        }

        .badge-kredit {
            background-color: #ff4757;
            color: white;
        }
    </style>
</head>

<body>
    <div class="sidebar">
        <div class="d-flex align-items-center mb-4 px-2">
            <img src="{{ asset('images/logo/Frame 7.png') }}" alt="Logo"
                style="width: 40px; height: auto; margin-right: 10px;">
            <h4 class="fw-bold mb-0">COCAINE</h4>
        </div>
        <nav class="nav flex-column">
            <a class="nav-link" href="/dashboard"><i class="bi bi-grid-1x2-fill me-2"></i> Dashboard</a>
            <a class="nav-link active" href="/riwayat"><i class="bi bi-clock-history me-2"></i> Riwayat</a>
            <a class="nav-link" href="/lokasi"><i class="bi bi-geo-alt-fill me-2"></i> Lokasi Alat</a>
        </nav>
    </div>

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold">Riwayat Aktivitas Celengan</h4>
            <button class="btn btn-danger rounded-3 px-4" onclick="handleResetSaldo()">
                <i class="bi bi-arrow-counterclockwise me-2"></i> Reset & Arsip Saldo
            </button>
        </div>

        <!-- Tabel Log Transaksi -->
        <div class="card p-4">
            <h5 class="fw-bold mb-4">Log Transaksi</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Waktu</th>
                            <th>Aktivitas</th>
                            <th>Nominal</th>
                            <th>Saldo Akhir</th>
                        </tr>
                    </thead>
                    <tbody id="riwayatTable">
                        <tr>
                            <td colspan="4" class="text-center text-muted">Memuat riwayat...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tabel Log Keamanan -->
        <div class="card p-4">
            <h5 class="fw-bold mb-4">Log Keamanan</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Waktu</th>
                            <th>Aktivitas</th>
                        </tr>
                    </thead>
                    <tbody id="keamananTable">
                        <tr>
                            <td colspan="2" class="text-center text-muted">Memuat riwayat...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        // Fungsi untuk mengambil data dari API DashboardController@logs
        async function loadRiwayat() {
            const tbodyRiwayat = document.getElementById('riwayatTable');
            const tbodyKeamanan = document.getElementById('keamananTable');

            try {
                const response = await fetch('/api/dashboard/log');
                const res = await response.json();

                if (res.status === 'success') {
                    // 1. Render Log Transaksi
                    tbodyRiwayat.innerHTML = '';
                    if (res.data.transactions.length > 0) {
                        res.data.transactions.forEach(tx => {
                            let badgeClass = tx.aktivitas === 'DEBIT' ? 'badge-debit' : 'badge-kredit';
                            tbodyRiwayat.innerHTML += `
                                <tr>
                                    <td>${tx.waktu}</td>
                                    <td><span class="badge ${badgeClass}">${tx.aktivitas}</span></td>
                                    <td class="fw-bold">Rp ${tx.nominal}</td>
                                    <td class="text-primary fw-bold">Rp ${tx.saldo_akhir}</td>
                                </tr>`;
                        });
                    } else {
                        tbodyRiwayat.innerHTML = '<tr><td colspan="4" class="text-center text-muted">Belum ada transaksi</td></tr>';
                    }

                    // 2. Render Log Keamanan
                    tbodyKeamanan.innerHTML = '';
                    if (res.data.security.length > 0) {
                        res.data.security.forEach(log => {
                            let colorClass = log.severity === 'critical' ? 'text-danger' : 'text-warning';
                            let icon = log.severity === 'critical' ? 'bi-exclamation-octagon-fill' : 'bi-exclamation-triangle-fill';

                            tbodyKeamanan.innerHTML += `
                                <tr>
                                    <td class="text-muted small">${log.waktu}</td>
                                    <td class="${colorClass}">
                                        <i class="bi ${icon} me-2"></i>${log.aktivitas}
                                    </td>
                                </tr>`;
                        });
                    } else {
                        tbodyKeamanan.innerHTML = '<tr><td colspan="2" class="text-center text-muted">Aman, tidak ada gangguan</td></tr>';
                    }
                }
            } catch (err) {
                console.error("Gagal mengambil data:", err);
                tbodyRiwayat.innerHTML = '<tr><td colspan="4" class="text-center text-danger">Gagal memuat data dari server</td></tr>';
            }
        }

        function handleResetSaldo() {
            Swal.fire({
                title: 'Konfirmasi Reset',
                text: "Semua riwayat saat ini akan diarsipkan dan saldo akan kembali ke nol.",
                icon: 'warning',
                showCancelButton: true,
                background: '#121f1d',
                color: '#ffffff',
                confirmButtonColor: '#ff4757',
                confirmButtonText: 'Ya, Reset!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('/api/saldo/reset', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Content-Type': 'application/json'
                        }
                    })
                        .then(() => {
                            Swal.fire({ title: 'Berhasil!', icon: 'success', background: '#121f1d', color: '#ffffff' });
                            loadRiwayat();
                        });
                }
            });
        }

        // Jalankan loadRiwayat saat halaman dibuka
        document.addEventListener('DOMContentLoaded', loadRiwayat);
    </script>
</body>

</html>