<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $farm->display_name }} - Map - Remei Farm OS</title>
    
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
            left: 10px;
            z-index: 1000;
            background: white;
            padding: 15px;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            width: 300px;
            max-height: calc(100vh - 80px);
            overflow-y: auto;
        }
        .farm-boundary {
            color: #27ae60;
            fillColor: #27ae60;
            fillOpacity: 0.2;
            weight: 3;
        }
        .field-boundary {
            color: #3498db;
            fillColor: #3498db;
            fillOpacity: 0.3;
            weight: 2;
        }
        .legend {
            background: white;
            padding: 10px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            position: absolute;
            bottom: 20px;
            right: 10px;
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
                <a class="nav-link" href="{{ route('farms.show', $farm) }}"><i class="fas fa-arrow-left"></i> Back to Farm</a>
                <a class="nav-link" href="{{ route('fields.index', $farm) }}"><i class="fas fa-tractor"></i> Fields</a>
                <a class="nav-link" href="{{ route('map.index') }}"><i class="fas fa-globe"></i> All Farms Map</a>
            </div>
        </div>
    </nav>

    <div id="map"></div>

    <div class="map-sidebar">
        <h5><i class="fas fa-tractor"></i> {{ $farm->display_name }}</h5>
        <p class="text-muted">{{ $farm->code }}</p>
        
        <div class="mb-3">
            <p><strong>Farmer:</strong> {{ $farm->farmer->full_name ?? 'N/A' }}</p>
            <p><strong>Total Area:</strong> {{ $farm->total_area }} {{ $farm->measurement_unit ?? 'hectares' }}</p>
            <p><strong>Cultivated Area:</strong> {{ $farm->cultivated_area ?? '0' }} hectares</p>
            <p><strong>Status:</strong> <span class="badge bg-{{ $farm->status_color }}">{{ $farm->status_label }}</span></p>
        </div>
        
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
                <input class="form-check-input" type="checkbox" id="show-farm-boundary" checked>
                <label class="form-check-label" for="show-farm-boundary">Farm Boundary</label>
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
        
        <div id="fields-list">
            <h6>Fields</h6>
            <div class="list-group" id="fields-container">
                <!-- Fields will be loaded here -->
            </div>
        </div>
    </div>

    <div class="legend">
        <div class="legend-item">
            <div class="legend-color" style="background-color: #27ae60;"></div>
            <span>Farm Boundary</span>
        </div>
        <div class="legend-item">
            <div class="legend-color" style="background-color: #3498db;"></div>
            <span>Field Boundaries</span>
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
        const farm = @json($farm);
        const geoJson = @json($geoJson);
        
        // Initialize map
        const map = L.map('map').setView([farm.latitude || config.center.lat, farm.longitude || config.center.lng], 13);
        
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
        
        // Feature groups for farm and fields
        const farmLayer = L.featureGroup().addTo(map);
        const fieldsLayer = L.featureGroup().addTo(map);
        
        // Add farm boundary if available
        if (geoJson.geometry && geoJson.geometry.coordinates && geoJson.geometry.coordinates[0].length > 0) {
            const farmPolygon = L.polygon(geoJson.geometry.coordinates[0], {
                className: 'farm-boundary',
                color: '#27ae60',
                fillColor: '#27ae60',
                fillOpacity: 0.2,
                weight: 3
            }).addTo(farmLayer);
            
            farmPolygon.bindPopup(`
                <div style="max-width: 300px;">
                    <h6><i class="fas fa-tractor"></i> ${farm.name}</h6>
                    <p><strong>Code:</strong> ${farm.code}</p>
                    <p><strong>Farmer:</strong> ${farm.farmer?.full_name || 'N/A'}</p>
                    <p><strong>Area:</strong> ${farm.total_area} ${farm.measurement_unit || 'hectares'}</p>
                    <p><strong>Status:</strong> ${farm.certification_status_label}</p>
                </div>
            `);
        } else {
            // Add farm marker if no boundary
            const farmMarker = L.marker([farm.latitude || config.center.lat, farm.longitude || config.center.lng], {
                icon: L.divIcon({
                    className: 'farm-marker',
                    html: '<i class="fas fa-tractor" style="color: #27ae60; font-size: 24px;"></i>',
                    iconSize: [24, 24],
                    iconAnchor: [12, 12]
                })
            }).addTo(farmLayer);
            
            farmMarker.bindPopup(`
                <div style="max-width: 300px;">
                    <h6><i class="fas fa-tractor"></i> ${farm.name}</h6>
                    <p><strong>Code:</strong> ${farm.code}</p>
                    <p><strong>Farmer:</strong> ${farm.farmer?.full_name || 'N/A'}</p>
                    <p><strong>Area:</strong> ${farm.total_area} ${farm.measurement_unit || 'hectares'}</p>
                    <p><strong>Status:</strong> ${farm.certification_status_label}</p>
                </div>
            `);
        }
        
        // Load fields GeoJSON
        async function loadFieldsGeoJson() {
            try {
                const response = await fetch(`/api/farms/${farm.id}/fields/geojson`);
                const data = await response.json();
                
                // Clear existing layers
                fieldsLayer.clearLayers();
                
                // Add fields to map
                L.geoJSON(data, {
                    style: {
                        color: '#3498db',
                        fillColor: '#3498db',
                        fillOpacity: 0.3,
                        weight: 2
                    },
                    onEachFeature: function(feature, layer) {
                        if (feature.properties) {
                            const props = feature.properties;
                            const popupContent = `
                                <div style="max-width: 300px;">
                                    <h6><i class="fas fa-map-marker-alt"></i> ${props.name}</h6>
                                    <p><strong>Code:</strong> ${props.code}</p>
                                    <p><strong>Area:</strong> ${props.area} hectares</p>
                                    <p><strong>Crop:</strong> ${props.crop || 'None'}</p>
                                    <p><strong>Status:</strong> ${props.status}</p>
                                    <a href="/farms/${farm.id}/fields/${props.id}/map" class="btn btn-sm btn-primary">
                                        <i class="fas fa-map"></i> View Field Map
                                    </a>
                                </div>
                            `;
                            layer.bindPopup(popupContent);
                        }
                    }
                }).addTo(fieldsLayer);
                
            } catch (error) {
                console.error('Error loading fields GeoJSON:', error);
            }
        }
        
        // Load fields list for sidebar
        async function loadFieldsList() {
            try {
                const response = await fetch(`/api/farms/${farm.id}/fields`);
                const fields = await response.json();
                
                const container = document.getElementById('fields-container');
                container.innerHTML = '';
                
                fields.data.forEach(field => {
                    const fieldItem = document.createElement('a');
                    fieldItem.href = `#`;
                    fieldItem.className = 'list-group-item list-group-item-action';
                    fieldItem.innerHTML = `
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">${field.name}</h6>
                            <small>${field.code}</small>
                        </div>
                        <p class="mb-1">${field.current_crop_type || 'No crop'}</p>
                        <small>${field.total_area} ${field.measurement_unit} • <span class="badge bg-${field.status_color}">${field.status_label}</span></small>
                    `;
                    
                    fieldItem.addEventListener('click', (e) => {
                        e.preventDefault();
                        if (field.latitude && field.longitude) {
                            map.setView([field.latitude, field.longitude], 15);
                        }
                    });
                    
                    container.appendChild(fieldItem);
                });
                
            } catch (error) {
                console.error('Error loading fields list:', error);
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
            map.setView([farm.latitude || config.center.lat, farm.longitude || config.center.lng], 13);
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
        
        document.getElementById('show-farm-boundary').addEventListener('change', (e) => {
            if (e.target.checked) {
                map.addLayer(farmLayer);
            } else {
                map.removeLayer(farmLayer);
            }
        });
        
        document.getElementById('show-fields').addEventListener('change', (e) => {
            if (e.target.checked) {
                map.addLayer(fieldsLayer);
            } else {
                map.removeLayer(fieldsLayer);
            }
        });
        
        // Initialize
        loadFieldsGeoJson();
        loadFieldsList();
        
        // Fit bounds to show all layers
        setTimeout(() => {
            const bounds = L.featureGroup([farmLayer, fieldsLayer]).getBounds();
            if (bounds.isValid()) {
                map.fitBounds(bounds, { padding: [50, 50] });
            }
        }, 500);
    </script>
</body>
</html>