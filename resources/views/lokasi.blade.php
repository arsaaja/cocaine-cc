<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COCAINE - Real-time Location</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    
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

        /* --- Sidebar --- */
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
            border-right: 1px solid rgba(255,255,255,0.05);
        }

        .brand-logo {
            font-size: 1.5rem;
            font-weight: 700;
            color: white;
            text-decoration: none;
            margin-bottom: 40px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .nav-link { 
            color: rgba(255, 255, 255, 0.7); 
            padding: 14px 18px; 
            border-radius: 12px; 
            margin-bottom: 8px; 
            text-decoration: none; 
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .nav-link.active { 
            background: var(--primary-color); 
            color: white; 
            box-shadow: 0 4px 12px rgba(40, 90, 72, 0.3);
        }

        /* --- Main Content --- */
        .main-content { 
            margin-left: var(--sidebar-width); 
            height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .top-bar {
            background: var(--card-bg);
            padding: 15px 30px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 1001;
            color: white;
        }

        .text-muted { color: rgba(255,255,255,0.5) !important; }

        /* --- Full Map Area --- */
        #map-wrapper {
            flex: 1;
            position: relative;
        }

        #map-canvas { 
            height: 100%; 
            width: 100%; 
        }

        .map-floating-controls {
            position: absolute;
            top: 20px;
            right: 20px;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .btn-map-control {
            background: var(--card-bg);
            border: 1px solid rgba(255,255,255,0.1);
            width: 45px;
            height: 45px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            color: #ffffff;
            transition: all 0.2s;
        }

        .btn-map-control:hover {
            background: var(--primary-color);
            color: white;
            transform: scale(1.05);
        }

        /* Pulsing Pin */
        .leaflet-pulsing-marker {
            border-radius: 50%;
            background: var(--secondary-color);
            box-shadow: 0 0 0 rgba(64, 138, 113, 0.4);
            animation: pulse 2s infinite;
            border: 2px solid white;
        }

        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(64, 138, 113, 0.7); }
            70% { box-shadow: 0 0 0 15px rgba(64, 138, 113, 0); }
            100% { box-shadow: 0 0 0 0 rgba(64, 138, 113, 0); }
        }

        .status-indicator {
            padding: 6px 14px;
            border-radius: 30px;
            font-size: 0.85rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .bg-success-subtle { background-color: rgba(176, 228, 204, 0.2) !important; color: var(--accent-color) !important; }
        .bg-light { background-color: rgba(255,255,255,0.05) !important; color: rgba(255,255,255,0.5) !important; }
    </style>
</head>
<body>

    <div class="sidebar">
        <a href="/" class="brand-logo">
            <i class="bi bi-safe2-fill" style="color: var(--accent-color);"></i>
            COCAINE
        </a>
        <nav class="nav flex-column">
            <a class="nav-link" href="/dashboard"><i class="bi bi-grid-1x2"></i> Dashboard</a>
            <a class="nav-link" href="/riwayat"><i class="bi bi-clock-history"></i> Riwayat</a>
            <a class="nav-link active" href="/lokasi"><i class="bi bi-geo-alt-fill"></i> Lokasi Alat</a>
        </nav>
    </div>

    <div class="main-content">
        <div class="top-bar">
            <div>
                <h5 class="fw-bold mb-0">Lokasi Alat Terkini</h5>
                <p class="text-muted small mb-0" id="last-update">Menghubungkan ke GPS...</p>
            </div>
            <div class="d-flex align-items-center gap-3">
                <div id="sync-status" class="status-indicator bg-light text-muted">
                    <i class="bi bi-circle-fill" style="font-size: 8px;"></i> Standby
                </div>
            </div>
        </div>

        <div id="map-wrapper">
            <div class="map-floating-controls">
                <button class="btn-map-control" title="Fokus ke Alat" onclick="recenterMap()">
                    <i class="bi bi-crosshair"></i>
                </button>
                <button class="btn-map-control" title="Ganti Tampilan Peta" onclick="toggleMapLayer()">
                    <i class="bi bi-layers"></i>
                </button>
            </div>
            <div id="map-canvas"></div>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // Logika JS tetap sama persis seperti file asli Anda
        const streetLayer = L.tileLayer('https://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
            maxZoom: 20, subdomains:['mt0','mt1','mt2','mt3'], attribution: 'Google Maps'
        });
        const satelliteLayer = L.tileLayer('https://{s}.google.com/vt/lyrs=s,h&x={x}&y={y}&z={z}', {
            maxZoom: 20, subdomains:['mt0','mt1','mt2','mt3'], attribution: 'Google Maps Imagery'
        });

        let map = L.map('map-canvas', {
            center: [-6.2000, 106.8166],
            zoom: 15,
            layers: [streetLayer],
            zoomControl: false
        });

        L.control.zoom({ position: 'bottomright' }).addTo(map);

        const pulsingIcon = L.divIcon({
            className: 'leaflet-pulsing-marker',
            iconSize: [14, 14],
            iconAnchor: [7, 7]
        });

        let marker = L.marker([-6.2000, 106.8166], {icon: pulsingIcon}).addTo(map)
            .bindPopup("<div class='text-center fw-bold'>CCNE-ESP32-01</div>");

        let currentCoords = null;
        let isSatellite = false;

        function toggleMapLayer() {
            if (isSatellite) {
                map.removeLayer(satelliteLayer); map.addLayer(streetLayer);
                isSatellite = false;
            } else {
                map.removeLayer(streetLayer); map.addLayer(satelliteLayer);
                isSatellite = true;
            }
        }

        function recenterMap() {
            if (currentCoords) map.flyTo(currentCoords, 17);
        }

        function fetchLocation() {
            const statusDiv = document.getElementById('sync-status');
            fetch('/api/latest')
                .then(res => res.json())
                .then(json => {
                    const d = json.data;
                    if (d && d.lat && d.lng) {
                        currentCoords = new L.LatLng(parseFloat(d.lat), parseFloat(d.lng));
                        
                        document.getElementById('last-update').innerText = 'Sinyal Diterima: ' + new Date().toLocaleTimeString('id-ID');
                        statusDiv.className = 'status-indicator bg-success-subtle text-success';
                        statusDiv.innerHTML = '<i class="bi bi-broadcast"></i> Live Tracking';

                        marker.setLatLng(currentCoords);
                        if (!map.getBounds().contains(currentCoords)) {
                            map.panTo(currentCoords);
                        }
                    }
                })
                .catch(err => {
                    statusDiv.className = 'status-indicator bg-danger-subtle text-danger';
                    statusDiv.innerHTML = '<i class="bi bi-exclamation-triangle"></i> No Signal';
                });
        }

        setInterval(fetchLocation, 3000);
        fetchLocation();
    </script>
</body>
</html>