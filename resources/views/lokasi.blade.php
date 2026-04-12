<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>COCAINE - Lokasi Real-time</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    
    <style>
        :root { --sidebar-width: 260px; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8f9fa; }
        .sidebar { width: var(--sidebar-width); height: 100vh; position: fixed; background: #1e272e; color: white; padding: 20px; }
        .main-content { margin-left: var(--sidebar-width); padding: 30px; }
        .nav-link { color: rgba(255,255,255,0.7); padding: 12px 15px; border-radius: 10px; margin-bottom: 5px; text-decoration: none; display: block; }
        .nav-link.active { background: rgba(255,255,255,0.1); color: white; }
        .card { border: none; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
        #map { height: 500px; border-radius: 15px; width: 100%; z-index: 1; }
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
            <a class="nav-link" href="/riwayat"><i class="bi bi-clock-history me-2"></i> Riwayat</a>
            <a class="nav-link active" href="/lokasi"><i class="bi bi-geo-alt-fill me-2"></i> Lokasi Alat</a>
            <a class="nav-link" href="/pengaturan"><i class="bi bi-gear-fill me-2"></i> Pengaturan</a>
        </nav>
    </div>

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-0">Pelacakan Real-time</h4>
                <p class="text-muted small" id="sync-status">Sinkronisasi GPS...</p>
            </div>
        </div>
        
        <div class="card p-4">
            <div class="row mb-3 text-center">
                <div class="col-6 border-end">
                    <small class="text-muted d-block">Latitude</small>
                    <span id="lat-text" class="fw-bold text-primary">-</span>
                </div>
                <div class="col-6">
                    <small class="text-muted d-block">Longitude</small>
                    <span id="lng-text" class="fw-bold text-primary">-</span>
                </div>
            </div>
            <div id="map"></div>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // Inisialisasi Peta (Default ke Jakarta)
        let map = L.map('map').setView([-6.2000, 106.8166], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
        let marker = L.marker([-6.2000, 106.8166]).addTo(map).bindPopup("Posisi Alat COCAINE");

        function fetchLocation() {
            fetch('/api/latest')
                .then(res => res.json())
                .then(json => {
                    const d = json.data;
                    if (d && d.lat && d.lng) {
                        const lat = parseFloat(d.lat);
                        const lng = parseFloat(d.lng);

                        // Update Teks UI
                        document.getElementById('lat-text').innerText = lat.toFixed(6);
                        document.getElementById('lng-text').innerText = lng.toFixed(6);
                        document.getElementById('sync-status').innerText = 'Terhubung - ' + new Date().toLocaleTimeString();

                        // Update Posisi Peta & Marker secara Real-time
                        const newLatLng = new L.LatLng(lat, lng);
                        marker.setLatLng(newLatLng);
                        map.panTo(newLatLng);
                    }
                })
                .catch(err => {
                    document.getElementById('sync-status').innerText = 'Koneksi Terputus...';
                });
        }

        // Jalankan polling setiap 3 detik
        setInterval(fetchLocation, 3000);
        fetchLocation();
    </script>
</body>
</html>