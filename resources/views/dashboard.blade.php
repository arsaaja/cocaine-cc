<!DOCTYPE html>
<html>
<head>
    <title>Monitoring & Control</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
    <!-- SheetJS (export Excel) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <!-- jsPDF + AutoTable (export PDF) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>
    <style>
        /* Desain Switch */
        .switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }
        .switch input { opacity: 0; width: 0; height: 0; }
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0; left: 0; right: 0; bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 34px;
        }
        .slider:before {
            position: absolute;
            content: "";
            height: 26px; width: 26px;
            left: 4px; bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }
        input:checked + .slider { background-color: #28a745; }
        input:checked + .slider:before { transform: translateX(26px); }
        
        #status-text { font-weight: bold; margin-top: 10px; display: block; }
    </style>
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h2 class="text-center">Sistem Kontrol Motor & Sensor</h2>
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card p-3 shadow">
                    <h4>Monitoring Sensor</h4>
                    <p>Suhu: <span id="val-suhu">0</span> °C</p>
                    <p>Kelembapan: <span id="val-hum">0</span> %</p>
                    <p>Jarak: <span id="val-jarak">0</span> cm</p>
                    <small class="text-muted" id="last-update"></small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card p-3 shadow text-center">
                    <h4>Kontrol Motor DC</h4>
                    <div class="py-3">
                        <label class="switch">
                            <input type="checkbox" id="btn-relay">
                            <span class="slider"></span>
                        </label>
                        <span id="status-text">OFF</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== TUGAS 2C: Grafik Real-time ===== -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card p-3 shadow">
                    <h4>Grafik Real-time Sensor</h4>
                    <div class="row mt-3">
                        <div class="col-md-4">
                            <h6 class="text-center text-danger">Suhu (°C)</h6>
                            <canvas id="chartSuhu"></canvas>
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-center text-primary">Kelembapan (%)</h6>
                            <canvas id="chartKelembapan"></canvas>
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-center text-success">Jarak (cm)</h6>
                            <canvas id="chartJarak"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== TUGAS 2C: Tabel Data & Export ===== -->
        <div class="row mt-4 mb-5">
            <div class="col-12">
                <div class="card p-3 shadow">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="mb-0">Tabel Data Sensor</h4>
                        <div class="d-flex gap-2">
                            <select class="form-select form-select-sm" id="filterSensor" onchange="loadTableData()">
                                <option value="">Semua Sensor</option>
                                <option value="Suhu">Suhu</option>
                                <option value="Kelembapan">Kelembapan</option>
                                <option value="Jarak">Jarak</option>
                            </select>
                            <select class="form-select form-select-sm" id="limitSelect" onchange="loadTableData()">
                                <option value="50">50 data</option>
                                <option value="100" selected>100 data</option>
                                <option value="200">200 data</option>
                            </select>
                            <button class="btn btn-sm btn-success" onclick="exportExcel()">⬇ Excel</button>
                            <button class="btn btn-sm btn-danger" onclick="exportPDF()">⬇ PDF</button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-sm">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Device ID</th>
                                    <th>Sensor</th>
                                    <th>Nilai</th>
                                    <th>Satuan</th>
                                    <th>Waktu</th>
                                </tr>
                            </thead>
                            <tbody id="tableBody">
                                <tr><td colspan="6" class="text-center text-muted">Memuat data...</td></tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <small class="text-muted" id="tableInfo">–</small>
                        <div id="pageBtns" class="d-flex gap-1"></div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script>
        let relayState = 0;

        // Fungsi Update UI (Script asli kamu)
        function updateData() {
            fetch('/get-data')
                .then(res => res.json())
                .then(data => {
                    if(data.sensor) {
                        const suhu = data.sensor.data.find(d => d.sensor_name === "Suhu").value;
                        const hum = data.sensor.data.find(d => d.sensor_name === "Kelembapan").value;
                        const jarak = data.sensor.data.find(d => d.sensor_name === "Jarak").value;
                        
                        document.getElementById('val-suhu').innerText = suhu;
                        document.getElementById('val-hum').innerText = hum;
                        document.getElementById('val-jarak').innerText = jarak;
                    }
                    
                    relayState = data.relay;
                    const btn = document.getElementById('btn-relay');
                    const txt = document.getElementById('status-text');
                    
                    // Update status checkbox & teks
                    btn.checked = (relayState == 1);
                    txt.innerText = relayState == 1 ? "ON" : "OFF";
                    txt.style.color = relayState == 1 ? "#28a745" : "#dc3545";
                });
        }

        // Tambahan tugas 2b (Script asli kamu)
        function fetchLatestSensor() {
            fetch('/api/latest')
                .then(res => res.json())
                .then(json => {
                    const d = json.data;
                    if (d['Suhu'])       document.getElementById('val-suhu').innerText   = parseFloat(d['Suhu'].value).toFixed(1);
                    if (d['Kelembapan']) document.getElementById('val-hum').innerText    = parseFloat(d['Kelembapan'].value).toFixed(1);
                    if (d['Jarak'])      document.getElementById('val-jarak').innerText  = parseFloat(d['Jarak'].value).toFixed(1);
                    document.getElementById('last-update').innerText = 'Update: ' + new Date().toLocaleTimeString('id-ID');
                })
                .catch(() => {});
        }

        // Tombol Klik (Script asli kamu dengan penyesuaian event onchange)
        document.getElementById('btn-relay').onchange = function() {
            relayState = this.checked ? 1 : 0; // Mengikuti status check sakelar
            fetch('/toggle-relay', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ status: relayState })
            });
            // Update teks langsung tanpa nunggu polling
            document.getElementById('status-text').innerText = relayState == 1 ? "ON" : "OFF";
        };

        setInterval(updateData, 2000); 
        setInterval(fetchLatestSensor, 3000); 
        fetchLatestSensor(); 

        // ===== TUGAS 2C: Grafik Real-time =====
        const chartConfig = (color) => ({
            type: 'line',
            data: { labels: [], datasets: [{ data: [], borderColor: color, backgroundColor: color + '22', borderWidth: 2, pointRadius: 2, fill: true, tension: 0.4 }] },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    x: { ticks: { maxTicksLimit: 6, font: { size: 9 } }, grid: { color: '#eee' } },
                    y: { ticks: { font: { size: 9 } }, grid: { color: '#eee' } }
                },
                animation: { duration: 300 }
            }
        });

        const charts = {
            suhu:       new Chart(document.getElementById('chartSuhu'),       chartConfig('#dc3545')),
            kelembapan: new Chart(document.getElementById('chartKelembapan'), chartConfig('#0d6efd')),
            jarak:      new Chart(document.getElementById('chartJarak'),      chartConfig('#198754')),
        };

        function updateCharts(data) {
            const buckets = { Suhu: [], Kelembapan: [], Jarak: [] };
            [...data].reverse().forEach(row => {
                if (buckets[row.sensor_name]) {
                    buckets[row.sensor_name].push({
                        x: new Date(row.created_at).toLocaleTimeString('id-ID'),
                        y: parseFloat(row.value)
                    });
                }
            });

            const push = (chart, points) => {
                const s = points.slice(-50);
                chart.data.labels = s.map(p => p.x);
                chart.data.datasets[0].data = s.map(p => p.y);
                chart.update('none');
            };

            push(charts.suhu,       buckets.Suhu);
            push(charts.kelembapan, buckets.Kelembapan);
            push(charts.jarak,      buckets.Jarak);
        }

        // ===== TUGAS 2C: Tabel Data =====
        let tableData = [];
        let currentPage = 1;
        const rowsPerPage = 15;
        const units = { Suhu: '°C', Kelembapan: '%', Jarak: 'cm' };

        function loadTableData() {
            const limit  = document.getElementById('limitSelect').value;
            const sensor = document.getElementById('filterSensor').value;
            const url    = `/api/dashboard/data?limit=${limit}` + (sensor ? `&sensor=${sensor}` : '');

            fetch(url)
                .then(res => res.json())
                .then(json => {
                    tableData = json.data || [];
                    updateCharts(tableData);
                    currentPage = 1;
                    renderTable();
                })
                .catch(() => {});
        }

        function renderTable() {
            const tbody = document.getElementById('tableBody');
            if (tableData.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">Tidak ada data.</td></tr>';
                document.getElementById('tableInfo').textContent = '–';
                document.getElementById('pageBtns').innerHTML = '';
                return;
            }

            const start    = (currentPage - 1) * rowsPerPage;
            const pageData = tableData.slice(start, start + rowsPerPage);

            const badgeColor = { Suhu: 'danger', Kelembapan: 'primary', Jarak: 'success' };

            tbody.innerHTML = pageData.map((row, i) => `
                <tr>
                    <td>${start + i + 1}</td>
                    <td>${row.device_id}</td>
                    <td><span class="badge bg-${badgeColor[row.sensor_name] || 'secondary'}">${row.sensor_name}</span></td>
                    <td>${parseFloat(row.value).toFixed(2)}</td>
                    <td>${units[row.sensor_name] || ''}</td>
                    <td>${new Date(row.created_at).toLocaleString('id-ID')}</td>
                </tr>
            `).join('');

            const total = tableData.length;
            const end   = Math.min(start + rowsPerPage, total);
            document.getElementById('tableInfo').textContent = `Menampilkan ${start + 1}–${end} dari ${total} data`;

            // Pagination
            const pages  = Math.ceil(total / rowsPerPage);
            const btnsEl = document.getElementById('pageBtns');
            let html = `<button class="btn btn-sm btn-outline-secondary" onclick="goPage(${currentPage - 1})" ${currentPage === 1 ? 'disabled' : ''}>‹</button>`;
            for (let p = 1; p <= pages; p++) {
                if (p === 1 || p === pages || (p >= currentPage - 1 && p <= currentPage + 1)) {
                    html += `<button class="btn btn-sm ${p === currentPage ? 'btn-dark' : 'btn-outline-secondary'}" onclick="goPage(${p})">${p}</button>`;
                } else if (p === currentPage - 2 || p === currentPage + 2) {
                    html += `<span class="btn btn-sm disabled">…</span>`;
                }
            }
            html += `<button class="btn btn-sm btn-outline-secondary" onclick="goPage(${currentPage + 1})" ${currentPage === pages ? 'disabled' : ''}>›</button>`;
            btnsEl.innerHTML = html;
        }

        function goPage(p) {
            const pages = Math.ceil(tableData.length / rowsPerPage);
            if (p < 1 || p > pages) return;
            currentPage = p;
            renderTable();
        }

        // ===== TUGAS 2C: Export Excel =====
        function exportExcel() {
            if (tableData.length === 0) return alert('Tidak ada data!');
            const rows = [['No','Device ID','Sensor','Nilai','Satuan','Waktu']].concat(
                tableData.map((r, i) => [
                    i + 1, r.device_id, r.sensor_name,
                    parseFloat(r.value).toFixed(2),
                    units[r.sensor_name] || '',
                    new Date(r.created_at).toLocaleString('id-ID')
                ])
            );
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, XLSX.utils.aoa_to_sheet(rows), 'Data Sensor');
            XLSX.writeFile(wb, `SensorData_${Date.now()}.xlsx`);
        }

        // ===== TUGAS 2C: Export PDF =====
        function exportPDF() {
            if (tableData.length === 0) return alert('Tidak ada data!');
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF({ orientation: 'landscape' });
            doc.setFontSize(14);
            doc.text('Laporan Data Sensor IoT', 14, 15);
            doc.setFontSize(9);
            doc.setTextColor(100);
            doc.text('Dicetak: ' + new Date().toLocaleString('id-ID'), 14, 22);
            doc.autoTable({
                startY: 27,
                head: [['No','Device ID','Sensor','Nilai','Satuan','Waktu']],
                body: tableData.map((r, i) => [
                    i + 1, r.device_id, r.sensor_name,
                    parseFloat(r.value).toFixed(2),
                    units[r.sensor_name] || '',
                    new Date(r.created_at).toLocaleString('id-ID')
                ]),
                headStyles: { fillColor: [33, 37, 41] },
                styles: { fontSize: 8 },
            });
            doc.save(`SensorData_${Date.now()}.pdf`);
        }

        // Load tabel & grafik pertama kali & tiap 5 detik
        loadTableData();
        setInterval(loadTableData, 5000);
    </script>
</body>
</html>