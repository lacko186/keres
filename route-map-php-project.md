# Route Map PHP Project Structure

## index.php
```php
<?php
// Fetch GTFS data
$gtfsData = json_decode(file_get_contents('gtfs_data.json'), true);

// Fetch Markers
$markers = json_decode(file_get_contents('marker.json'), true);
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Útvonal Térkép</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <div id="search-container">
            <input type="text" id="route-search" placeholder="Útvonal keresése...">
        </div>
        
        <div id="map"></div>
    </div>

    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script>
        // Pass PHP data to JavaScript
        const gtfsData = <?php echo json_encode($gtfsData); ?>;
        const markers = <?php echo json_encode($markers); ?>;
    </script>
    <script src="map.js"></script>
</body>
</html>
```

## map.js
```javascript
document.addEventListener('DOMContentLoaded', () => {
    // Initialize the map
    const map = L.map('map').setView([47.4979, 19.0402], 13);

    // Add OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    // Prepare route coordinates
    const routeCoordinates = gtfsData
        .sort((a, b) => a.shape_pt_sequence - b.shape_pt_sequence)
        .map(point => [point.shape_pt_lat, point.shape_pt_lon]);

    // Draw route polyline
    if (routeCoordinates.length > 0) {
        L.polyline(routeCoordinates, {
            color: 'blue',
            weight: 5,
            opacity: 0.7
        }).addTo(map);

        // Fit map to route
        map.fitBounds(routeCoordinates);
    }

    // Add markers
    markers.forEach(marker => {
        L.marker([marker.Lat, marker.Lng])
            .addTo(map)
            .bindPopup(marker.Nev);
    });

    // Search functionality
    const searchInput = document.getElementById('route-search');
    searchInput.addEventListener('input', (e) => {
        const searchTerm = e.target.value.toLowerCase();
        
        // Filter markers based on search term
        markers.forEach(marker => {
            const markerElement = document.querySelector(
                `.leaflet-marker-icon[title="${marker.Nev}"]`
            );
            
            if (markerElement) {
                if (marker.Nev.toLowerCase().includes(searchTerm)) {
                    markerElement.style.display = 'block';
                } else {
                    markerElement.style.display = 'none';
                }
            }
        });
    });
});
```

## styles.css
```css
body, html {
    margin: 0;
    padding: 0;
    height: 100%;
    font-family: Arial, sans-serif;
}

.container {
    display: flex;
    flex-direction: column;
    height: 100vh;
}

#search-container {
    padding: 10px;
    background-color: #f4f4f4;
}

#route-search {
    width: 100%;
    padding: 10px;
    font-size: 16px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

#map {
    flex-grow: 1;
    width: 100%;
}
```

## Szükséges JSON fájlok
1. `gtfs_data.json`
```json
[
  {
    "shape_id": "91_route",
    "shape_pt_lat": 47.4979,
    "shape_pt_lon": 19.0402,
    "shape_pt_sequence": 1,
    "shape_dist_traveled": 0
  }
]
```

2. `marker.json`
```json
[
  {
    "Nev": "Rómahegy",
    "Lat": 47.5034,
    "Lng": 19.0324
  }
]
```
