<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>COCAINE - Riwayat Aktivitas</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
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
            border-right: 1px solid rgba(255,255,255,0.05);
        }

        .nav-link { 
            color: rgba(255,255,255,0.7); 
            padding: 12px 15px; 
            border-radius: 10px; 
            margin-bottom: 5px; 
            text-decoration: none; 
            display: block; 
            transition: all 0.3s;
        }

        .nav-link:hover, .nav-link.active { 
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
            box-shadow: 0 4px 20px rgba(0,0,0,0.3); 
            border: 1px solid rgba(255,255,255,0.05);
        }

        /* --- Table Styling --- */
        .table {
            color: white;
        }

        .table-light {
            background-color: rgba(255,255,255,0.05) !important;
            color: var(--accent-color) !important;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .table-hover tbody tr:hover {
            background-color: rgba(255,255,255,0.02);
            color: white;
        }

        td {
            border-bottom: 1px solid rgba(255,255,255,0.05) !important;
            padding: 15px !important;
        }

        .row-archived {
            opacity: 0.6;
            background-color: rgba(255, 255, 255, 0.02);
        }
    
        .badge-archive {
            background: rgba(176, 228, 204, 0.1);
            color: var(--accent-color);
            border: 1px solid var(--accent-color);
        }

        .text-primary {
            color: var(--accent-color) !important;
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
            <a class="nav-link" href="/dashboard"><i class="bi bi-grid-1x2-fill me-2"></i> Dashboard</a>
            <a class="nav-link active" href="/riwayat"><i class="bi bi-clock-history me-2"></i> Riwayat</a>
            <a class="nav-link" href="/lokasi"><i class="bi bi-geo-alt-fill me-2"></i> Lokasi Alat</a>
        </nav>
    </div>

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-4">Riwayat Aktivitas Celengan</h4>
                <button class="btn btn-danger rounded-3 px-4" onclick="handleResetSaldo()">
                    <i class="bi bi-arrow-counterclockwise me-2"></i> Reset & Arsip Saldo
                </button>
            </div>    
        
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
                    <tbody id="riwayatTable">
                        <tr>
                            <td colspan="2" class="text-center text-muted">Memuat riwayat...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function handleResetSaldo() {
            Swal.fire({
                title: 'Konfirmasi Reset',
                text: "Semua riwayat saat ini akan diarsipkan dan saldo akan kembali ke nol. Lanjutkan?",
                icon: 'warning',
                showCancelButton: true,
                background: '#121f1d', // Warna var(--card-bg)
                color: '#ffffff',
                confirmButtonColor: '#ff4757', // Warna merah untuk aksi hapus/reset
                cancelButtonColor: '#408A71',  // Warna var(--secondary-color)
                confirmButtonText: 'Ya, Reset & Arsip!',
                cancelButtonText: 'Batal',
                borderRadius: '16px'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Tampilkan loading saat proses fetch
                    Swal.fire({
                        title: 'Memproses...',
                        allowOutsideClick: false,
                        background: '#121f1d',
                        color: '#ffffff',
                        didOpen: () => { Swal.showLoading(); }
                    });

                    fetch('/api/saldo/reset', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: 'Saldo telah di-reset dan riwayat lama berhasil diarsipkan.',
                            icon: 'success',
                            background: '#121f1d',
                            color: '#ffffff',
                            confirmButtonColor: '#285A48'
                        }).then(() => {
                            location.reload(); 
                        });
                    })
                    .catch(err => {
                        console.error("Gagal reset:", err);
                        Swal.fire({
                            title: 'Gagal!',
                            text: 'Terjadi kesalahan sistem saat mencoba mereset saldo.',
                            icon: 'error',
                            background: '#121f1d',
                            color: '#ffffff',
                            confirmButtonColor: '#285A48'
                        });
                    });
                }
            });
        }
        // Fungsi aslinya tetap dipertahankan
        function loadRiwayat() {
            const tbody = document.getElementById('riwayatTable');
        }
        loadRiwayat();
    </script>
</body>
</html>