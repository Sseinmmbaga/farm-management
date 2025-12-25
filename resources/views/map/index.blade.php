<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Farm Map - Remei Farm OS</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
          crossorigin=""/>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; padding: 0; }
        .navbar { background-color: #27ae60; }
        .navbar-brand { color: white !important; font-weight: bold; }
        #map { height: calc(100vh - 56px); width: 100%; }
        .map-sidebar {
            position: absolute;
            top: 60px;
            right: 10px;
            z-index: 1000;
            background: white;
            padding: 15px;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            width: 300px;
            max-height: calc(100vh - 80px);
            overflow-y: auto;
        }
        .farm-marker {
            background-color: #27ae60;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            border: 2px solid white;
            box-shadow: 0 0 5px rgba(0,0,0,0.5);
        }
        .field-marker {
            background-color: #3498db;
            border-radius: 50%;
            width: 15px;
            height: 15px;
            border: 2px solid white;
            box-shadow: 0 0 3px rgba(0,0,0,0.3);
        }
        .legend {
            background: white;
            padding: 10px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            position: absolute;
            bottom: 20px;
            left: 10px;
            z-index: 1000;
        }
        .legend-item {
            display: flex;
            align-items: center;
            margin-bottom: 5px;
        }
        .legend-color {
            width: 20px;
            height: 20px;
            margin-right: 10px;
            border-radius: 3px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="/"><i class="fas fa-seedling"></i> Remei Farm OS</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="{{ route('farms.index') }}"><i class="fas fa-tractor"></i> Farms</a>
                <a class="nav-link" href="{{ route('dashboard') }}"><i class="fas fa-home"></i> Dashboard</a>
            </div>
        </div>
    </nav>

    <div id="map"></div>

    <div class="map-sidebar">
        <h5><i class="fas fa-map"></i> Farm Map</h5>
        <p class="text-muted">View all farms and fields on the map</p>
        
        <div class="mb-3">
            <label class="form-label">Map Type</label>
            <select id="map-type" class="form-select">
                <option value="openstreetmap">OpenStreetMap</option>
                @if($config['mapbox_token'])
                    <option value="mapbox">MapBox Satellite</option>
                @endif
            </select>
        </div>
        
        <div class="mb-3">
            <label class="form-label">Show</label>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="show-farms" checked>
                <label class="form-check-label" for="show-farms">Farms</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="show-fields" checked>
                <label class="form-check-label" for="show-fields">Fields</label>
            </div>
        </div>
        
        <div class="mb-3">
            <button class="btn btn-sm btn-outline-secondary" id="locate-me">
                <i class="fas fa-location-arrow"></i> Locate Me
            </button>
            <button class="btn btn-sm btn-outline-secondary" id="reset-view">
                <i class="fas fa-globe"></i> Reset View
            </button>
        </div>
        
        <div id="farm-list">
            <h6>Farms</h6>
            <div class="list-group" id="farms-container">
                <!-- Farms will be loaded here -->
            </div>
        </div>
    </div>

    <div class="legend">
        <div class="legend-item">
            <div class="legend-color" style="background-color: #27ae60;"></div>
            <span>Farms</span>
        </div>
        <div class="legend-item">
            <div class="legend-color" style="background-color: #3498db;"></div>
            <span>Fields</span>
        </div>
        <div class="legend-item">
            <div class="legend-color" style="background-color: #e74c3c;"></div>
            <span>Current Location</span>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
            crossorigin=""></script>
    @if($config['mapbox_token'])
        <script src="https://api.mapbox.com/mapbox.js/v3.3.1/mapbox.js"></script>
        <link href="https://api.mapbox.com/mapbox.js/v3.3.1/mapbox.css" rel="stylesheet" />
    @endif
    
    <script>
        // Map configuration
        const config = @json($config);
        const user = @json($user);
        
        // Initialize map
        const map = L.map('map').setView([config.center.lat, config.center.lng], config.zoom);
        
        // Tile layers
        const openStreetMap = L.tileLayer(config.openstreetmap_url, {
            attribution: config.attribution,
            maxZoom: 19
        });
        
        let mapboxLayer = null;
        if (config.mapbox_token) {
            L.mapbox.accessToken = config.mapbox_token;
            mapboxLayer = L.mapbox.styleLayer(config.mapbox_style);
        }
        
        // Add default layer
        openStreetMap.addTo(map);
        
        // Layer control
        const baseLayers = {
            'OpenStreetMap': openStreetMap
        };
        
        if (mapboxLayer) {
            baseLayers['MapBox Satellite'] = mapboxLayer;
        }
        
        // Feature groups for farms and fields
        const farmsLayer = L.featureGroup().addTo(map);
        const fieldsLayer = L.featureGroup().addTo(map);
        
        // Overlay control
        const overlays = {
            'Farms': farmsLayer,
            'Fields': fieldsLayer
        };
        
        L.control.layers(baseLayers, overlays).addTo(map);
        
        // Load GeoJSON data
        async function loadFarmsGeoJson() {
            try {
                const response = await fetch('/api/map/farms/geojson');
                const data = await response.json();
                
                // Clear existing layers
                farmsLayer.clearLayers();
                
                // Add farms to map
                L.geoJSON(data, {
                    pointToLayer: function(feature, latlng) {
                        return L.circleMarker(latlng, {
                            radius: 8,
                            fillColor: '#27ae60',
                            color: '#fff',
                            weight: 2,
                            opacity: 1,
                            fillOpacity: 0.8
                        });
                    },
                    onEachFeature: function(feature, layer) {
                        if (feature.properties) {
                            const props = feature.properties;
                            const popupContent = `
                                <div style="max-width: 300px;">
                                    <h6><i class="fas fa-tractor"></i> ${props.name}</h6>
                                    <p><strong>Code:</strong> ${props.code}</p>
                                    <p><strong>Farmer:</strong> ${props.farmer}</p>
                                    <p><strong>Area:</strong> ${props.area} hectares</p>
                                    <p><strong>Status:</strong> ${props.status}</p>
                                    <a href="/farms/${props.id}/map" class="btn btn-sm btn-success">
                                        <i class="fas fa-map"></i> View Farm Map
                                    </a>
                                </div>
                            `;
                            layer.bindPopup(popupContent);
                        }
                    }
                }).addTo(farmsLayer);
                
            } catch (error) {
                console.error('Error loading farms GeoJSON:', error);
            }
        }
        
        // Load farms list for sidebar
        async function loadFarmsList() {
            try {
                const response = await fetch('/api/farms');
                const farms = await response.json();
                
                const container = document.getElementById('farms-container');
                container.innerHTML = '';
                
                farms.data.forEach(farm => {
                    const farmItem = document.createElement('a');
                    farmItem.href = `#`;
                    farmItem.className = 'list-group-item list-group-item-action';
                    farmItem.innerHTML = `
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">${farm.name}</h6>
                            <small>${farm.code}</small>
                        </div>
                        <p class="mb-1">${farm.farmer?.full_name || 'No farmer'}</p>
                        <small>${farm.total_area} ${farm.measurement_unit}</small>
                    `;
                    
                    farmItem.addEventListener('click', (e) => {
                        e.preventDefault();
                        map.setView([farm.latitude || config.center.lat, farm.longitude || config.center.lng], 13);
                    });
                    
                    container.appendChild(farmItem);
                });
                
            } catch (error) {
                console.error('Error loading farms list:', error);
            }
        }
        
        // Locate user
        function locateUser() {
            if (!navigator.geolocation) {
                alert('Geolocation is not supported by your browser');
                return;
            }
            
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    
                    // Add marker for current location
                    const marker = L.marker([lat, lng], {
                        icon: L.divIcon({
                            className: 'current-location-marker',
                            html: '<i class="fas fa-location-arrow" style="color: #e74c3c; font-size: 24px;"></i>',
                            iconSize: [24, 24],
                            iconAnchor: [12, 12]
                        })
                    }).addTo(map);
                    
                    marker.bindPopup('<strong>Your Location</strong><br>Lat: ' + lat + '<br>Lng: ' + lng).openPopup();
                    
                    // Center map on location
                    map.setView([lat, lng], 15);
                },
                (error) => {
                    alert('Unable to retrieve your location: ' + error.message);
                }
            );
        }
        
        // Event listeners
        document.getElementById('locate-me').addEventListener('click', locateUser);
        document.getElementById('reset-view').addEventListener('click', () => {
            map.setView([config.center.lat, config.center.lng], config.zoom);
        });
        
        document.getElementById('map-type').addEventListener('change', (e) => {
            if (e.target.value === 'mapbox' && mapboxLayer) {
                map.removeLayer(openStreetMap);
                mapboxLayer.addTo(map);
            } else {
                if (mapboxLayer) map.removeLayer(mapboxLayer);
                openStreetMap.addTo(map);
            }
        });
        
        // Initialize
        loadFarmsGeoJson();
        loadFarmsList();
    </script>
</body>
</html>