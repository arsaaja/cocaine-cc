<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>COCAINE - Riwayat Aktivitas</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root { --sidebar-width: 260px; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8f9fa; }
        .sidebar { width: var(--sidebar-width); height: 100vh; position: fixed; background: #1e272e; color: white; padding: 20px; }
        .main-content { margin-left: var(--sidebar-width); padding: 30px; }
        .nav-link { color: rgba(255,255,255,0.7); padding: 12px 15px; border-radius: 10px; margin-bottom: 5px; text-decoration: none; display: block; }
        .nav-link.active { background: rgba(255,255,255,0.1); color: white; }
        .card { border: none; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="d-flex align-items-center mb-4 px-2">
            <i class="bi bi-safe2-fill text-primary fs-3 me-2"></i>
            <h4 class="fw-bold mb-0">COCAINE</h4>
        </div>
        <nav class="nav flex-column">
            <a class="nav-link" href="/"><i class="bi bi-grid-1x2-fill me-2"></i> Dashboard</a>
            <a class="nav-link active" href="/riwayat"><i class="bi bi-clock-history me-2"></i> Riwayat</a>
            <a class="nav-link" href="/lokasi"><i class="bi bi-geo-alt-fill me-2"></i> Lokasi Alat</a>
            <a class="nav-link" href="/pengaturan"><i class="bi bi-gear-fill me-2"></i> Pengaturan</a>
        </nav>
    </div>

    <div class="main-content">
        <h4 class="fw-bold mb-4">Riwayat Aktivitas Celengan</h4>
        
        <div class="card p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Waktu</th>
                            <th>Aktivitas</th>
                            <th>Nominal</th>
                            <th>Saldo Akhir</th>
                        </tr>
                    </thead>
                    <tbody id="riwayatTable">
                        <tr>
                            <td colspan="4" class="text-center">Memuat riwayat...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        // Contoh fungsi fetch riwayat (kamu bisa sesuaikan dengan API-mu nanti)
        function loadRiwayat() {
            // fetch('/api/logs').then(...)
            console.log("Memuat data riwayat...");
        }
        loadRiwayat();
    </script>
</body>
</html>