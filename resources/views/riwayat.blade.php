<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>COCAINE - Riwayat Aktivitas</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
                            <td colspan="4" class="text-center text-muted">Memuat riwayat...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        // Fungsi aslinya tetap dipertahankan
        function loadRiwayat() {
            console.log("Memuat data riwayat...");
        }
        loadRiwayat();
    </script>
</body>
</html>