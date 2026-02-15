<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mapa Námrazy</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        body { margin: 0; font-family: sans-serif; }
        #map { height: 100vh; width: 100%; z-index: 1; }
        #controls-panel { position: absolute; top: 10px; left: 10px; z-index: 1000; background: white; padding: 15px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.2); width: 300px; }
        #search-box { width: 100%; padding: 8px; margin-bottom: 10px; box-sizing: border-box; }
        #search-results { background: white; border: 1px solid #ddd; display: none; max-height: 150px; overflow-y: auto; }
        .search-item { padding: 8px; cursor: pointer; border-bottom: 1px solid #eee; }
        .search-item:hover { background: #f0f0f0; }
        .btn { display: block; width: 100%; padding: 10px; margin-top: 5px; color: white; border: none; border-radius: 4px; cursor: pointer; text-align: center; text-decoration: none; box-sizing: border-box; }
        .btn-report { background: #dc3545; }
        .btn-report.active { background: #a71d2a; border: 2px solid black; }
        .btn-admin { background: #28a745; }
        #user-info { margin-top: 10px; font-size: 0.9em; border-top: 1px solid #eee; padding-top: 5px; }
        .report-form textarea { width: 100%; height: 60px; margin: 5px 0; }
    </style>
</head>
<body>

<div id="controls-panel">
    <h3>❄️ Mapa Námrazy</h3>
    <div style="position:relative;">
        <input type="text" id="search-box" placeholder="Hledat město/ulici..." autocomplete="off">
        <div id="search-results"></div>
    </div>
    <button id="btn-report-mode" class="btn btn-report" onclick="toggleReportMode()">📢 Nahlásit námrazu</button>
    <?php if($isAdmin): ?>
        <a href="index.php?page=admin" class="btn btn-admin">⚙️ Administrace</a>
    <?php endif; ?>
    <div id="user-info">
        Uživatel: <b><?php echo htmlspecialchars($_SESSION['username']); ?></b><br>
        <a href="index.php?page=logout" style="color:red;">Odhlásit se</a>
    </div>
</div>

<div id="map"></div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    var map = L.map('map').setView([49.8175, 15.4730], 7);
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '&copy; OpenStreetMap' }).addTo(map);

    var reportMode = false;
    var popup = L.popup();

    async function loadApprovedReports() {
        // ZMĚNA: Volání přes Router
        const res = await fetch('index.php?page=api_reports');
        const reports = await res.json();
        
        var reportIcon = L.icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png',
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
            iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34], shadowSize: [41, 41]
        });

        reports.forEach(r => {
            L.marker([r.lat, r.lon], {icon: reportIcon}).addTo(map)
                .bindPopup(`<b>${r.username}:</b><br>${r.description}<br><small>${r.created_at}</small>`);
        });
    }
    loadApprovedReports();

    // Vyhledávání (Nominatim) - beze změny
    const searchBox = document.getElementById('search-box');
    const resultsBox = document.getElementById('search-results');
    searchBox.addEventListener('input', async function() {
        const query = this.value;
        if (query.length < 3) { resultsBox.style.display = 'none'; return; }
        const res = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${query}&countrycodes=cz&limit=5`);
        const data = await res.json();
        resultsBox.innerHTML = '';
        if (data.length > 0) {
            resultsBox.style.display = 'block';
            data.forEach(place => {
                const div = document.createElement('div');
                div.className = 'search-item';
                div.innerText = place.display_name;
                div.onclick = () => {
                    map.setView([place.lat, place.lon], 14);
                    resultsBox.style.display = 'none';
                    searchBox.value = place.display_name;
                };
                resultsBox.appendChild(div);
            });
        }
    });

    function toggleReportMode() {
        reportMode = !reportMode;
        const btn = document.getElementById('btn-report-mode');
        if (reportMode) {
            btn.classList.add('active');
            btn.innerText = "❌ Zrušit hlášení";
            map.getContainer().style.cursor = "crosshair";
        } else {
            btn.classList.remove('active');
            btn.innerText = "📢 Nahlásit námrazu";
            map.getContainer().style.cursor = "";
        }
    }

    map.on('click', function(e) {
        if (reportMode) {
            const formHtml = `
                <div class="report-form" style="text-align:center;">
                    <h4>Nahlásit problém</h4>
                    <textarea id="report-desc" placeholder="Popis..."></textarea><br>
                    <button onclick="submitReport(${e.latlng.lat}, ${e.latlng.lng})">Odeslat</button>
                </div>`;
            popup.setLatLng(e.latlng).setContent(formHtml).openOn(map);
        } else {
            checkWeather(e.latlng.lat, e.latlng.lng);
        }
    });

    async function submitReport(lat, lon) {
        const desc = document.getElementById('report-desc').value;
        if (!desc) { alert("Napište popis."); return; }
        // ZMĚNA: Volání přes Router
        const res = await fetch('index.php?page=api_reports', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ lat: lat, lon: lon, desc: desc })
        });
        const result = await res.json();
        if (result.success) {
            alert(result.message);
            map.closePopup();
            toggleReportMode();
        }
    }

    async function checkWeather(lat, lng) {
        popup.setLatLng([lat, lng]).setContent("Načítám...").openOn(map);
        try {
            // ZMĚNA: Volání přes Router
            const response = await fetch(`index.php?page=api_weather&lat=${lat}&lon=${lng}`);
            const data = await response.json();
            let color = data.risk === "high" ? "red" : (data.risk === "medium" ? "orange" : "green");
            popup.setContent(`
                <div style="min-width:150px">
                    <h4 style="color:${color};margin:0">${data.message}</h4><hr>
                    <b>Teplota:</b> ${data.temp} °C<br><b>Počasí:</b> ${data.desc}
                </div>`);
        } catch (e) { popup.setContent("Chyba."); }
    }
</script>
</body>
</html>