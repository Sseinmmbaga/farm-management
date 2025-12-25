@extends('layouts.base')

@section('title', 'All Farms Map')

@push('styles')
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<!-- Leaflet MarkerCluster CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css" />
<style>
    #all-farms-map {
        height: calc(100vh - 200px);
        min-height: 500px;
        width: 100%;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }

    .map-container {
        background-color: white;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }

    .map-filters {
        background-color: white;
        border-radius: 10px;
        padding: 15px 20px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        margin-bottom: 20px;
    }

    .map-stats {
        background-color: white;
        border-radius: 10px;
        padding: 15px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }

    .stat-card {
        text-align: center;
        padding: 10px;
    }

    .stat-card .stat-value {
        font-size: 1.5rem;
        font-weight: bold;
    }

    .stat-card .stat-label {
        font-size: 0.85rem;
        color: #666;
    }

    .leaflet-popup-content-wrapper {
        border-radius: 8px;
    }

    .farm-popup {
        min-width: 200px;
    }

    .farm-popup h5 {
        color: #27ae60;
        margin-bottom: 10px;
        border-bottom: 2px solid #27ae60;
        padding-bottom: 5px;
    }

    .farm-popup p {
        margin-bottom: 5px;
    }

    .farm-popup .badge {
        font-size: 0.75rem;
    }

    .farm-popup .btn-sm {
        padding: 2px 8px;
        font-size: 0.75rem;
    }

    /* Custom cluster styles */
    .marker-cluster-small {
        background-color: rgba(46, 204, 113, 0.6);
    }
    .marker-cluster-small div {
        background-color: rgba(39, 174, 96, 0.8);
    }

    .marker-cluster-medium {
        background-color: rgba(52, 152, 219, 0.6);
    }
    .marker-cluster-medium div {
        background-color: rgba(41, 128, 185, 0.8);
    }

    .marker-cluster-large {
        background-color: rgba(231, 76, 60, 0.6);
    }
    .marker-cluster-large div {
        background-color: rgba(192, 57, 43, 0.8);
    }

    .map-legend {
        position: absolute;
        bottom: 30px;
        right: 10px;
        background: white;
        padding: 10px 15px;
        border-radius: 8px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.2);
        z-index: 1000;
    }

    .map-legend h6 {
        margin-bottom: 10px;
        font-weight: 600;
        border-bottom: 1px solid #eee;
        padding-bottom: 5px;
    }

    .legend-item {
        display: flex;
        align-items: center;
        margin-bottom: 5px;
        font-size: 0.85rem;
    }

    .legend-color {
        width: 16px;
        height: 16px;
        margin-right: 8px;
        border-radius: 3px;
        border: 1px solid #ddd;
    }

    .search-result-item {
        padding: 8px 12px;
        cursor: pointer;
        border-bottom: 1px solid #eee;
    }

    .search-result-item:hover {
        background-color: #f5f5f5;
    }

    .search-results {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border-radius: 0 0 8px 8px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        max-height: 300px;
        overflow-y: auto;
        z-index: 1000;
        display: none;
    }

    .search-container {
        position: relative;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2><i class="fas fa-globe text-primary"></i> All Farms Map</h2>
            <p class="text-muted mb-0">View all registered farms on the map</p>
        </div>
        <div class="btn-group">
            <a href="{{ route('farms.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-list"></i> List View
            </a>
            <button class="btn btn-outline-success" onclick="exportAllGeoJSON()">
                <i class="fas fa-download"></i> Export GeoJSON
            </button>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="row mb-4">
        <div class="col">
            <div class="map-stats">
                <div class="stat-card">
                    <div class="stat-value text-primary" id="total-farms">0</div>
                    <div class="stat-label">Total Farms</div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="map-stats">
                <div class="stat-card">
                    <div class="stat-value text-success" id="organic-farms">0</div>
                    <div class="stat-label">Organic Farms</div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="map-stats">
                <div class="stat-card">
                    <div class="stat-value text-warning" id="conversion-farms">0</div>
                    <div class="stat-label">In Conversion</div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="map-stats">
                <div class="stat-card">
                    <div class="stat-value text-secondary" id="conventional-farms">0</div>
                    <div class="stat-label">Conventional</div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="map-stats">
                <div class="stat-card">
                    <div class="stat-value text-info" id="mapped-farms">0</div>
                    <div class="stat-label">With Boundaries</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="map-filters">
        <div class="row align-items-center">
            <div class="col-md-4">
                <div class="search-container">
                    <input type="text" class="form-control" id="farm-search" placeholder="Search farms by name or code...">
                    <div class="search-results" id="search-results"></div>
                </div>
            </div>
            <div class="col-md-2">
                <select class="form-select" id="filter-certification">
                    <option value="">All Certifications</option>
                    <option value="organic">Organic</option>
                    <option value="in-conversion">In Conversion</option>
                    <option value="conventional">Conventional</option>
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select" id="filter-status">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                    <option value="abandoned">Abandoned</option>
                </select>
            </div>
            <div class="col-md-2">
                <div class="btn-group w-100">
                    <button type="button" class="btn btn-outline-secondary active" id="btn-street-all">
                        <i class="fas fa-road"></i>
                    </button>
                    <button type="button" class="btn btn-outline-secondary" id="btn-satellite-all">
                        <i class="fas fa-satellite"></i>
                    </button>
                    <button type="button" class="btn btn-outline-secondary" id="btn-terrain-all">
                        <i class="fas fa-mountain"></i>
                    </button>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="show-boundaries" checked>
                    <label class="form-check-label" for="show-boundaries">Show Boundaries</label>
                </div>
            </div>
        </div>
    </div>

    <!-- Map Container -->
    <div class="map-container position-relative">
        <div id="all-farms-map"></div>

        <!-- Legend -->
        <div class="map-legend">
            <h6><i class="fas fa-info-circle"></i> Legend</h6>
            <div class="legend-item">
                <div class="legend-color" style="background-color: #27ae60;"></div>
                <span>Organic</span>
            </div>
            <div class="legend-item">
                <div class="legend-color" style="background-color: #f39c12;"></div>
                <span>In Conversion</span>
            </div>
            <div class="legend-item">
                <div class="legend-color" style="background-color: #95a5a6;"></div>
                <span>Conventional</span>
            </div>
            <div class="legend-item mt-2" style="border-top: 1px solid #eee; padding-top: 5px;">
                <div class="legend-color" style="background-color: rgba(46, 204, 113, 0.3); border: 2px solid #27ae60;"></div>
                <span>Farm Boundary</span>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<!-- Leaflet MarkerCluster JS -->
<script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>
<script>
    // GeoJSON data from Laravel
    const geoJsonData = {!! json_encode($geoJson) !!};

    // Stats counters
    let totalFarms = 0;
    let organicFarms = 0;
    let conversionFarms = 0;
    let conventionalFarms = 0;
    let mappedFarms = 0;

    // Default center (Tanzania)
    const defaultCenter = [-6.369028, 34.888822];
    const defaultZoom = 6;

    // Initialize map
    const map = L.map('all-farms-map').setView(defaultCenter, defaultZoom);

    // Tile layers
    const streetLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    });

    const satelliteLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        maxZoom: 19,
        attribution: 'Tiles &copy; Esri'
    });

    const terrainLayer = L.tileLayer('https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png', {
        maxZoom: 17,
        attribution: '&copy; <a href="https://opentopomap.org">OpenTopoMap</a>'
    });

    streetLayer.addTo(map);

    // Layer switching
    document.getElementById('btn-street-all').addEventListener('click', function() {
        switchLayer(streetLayer, 'btn-street-all');
    });

    document.getElementById('btn-satellite-all').addEventListener('click', function() {
        switchLayer(satelliteLayer, 'btn-satellite-all');
    });

    document.getElementById('btn-terrain-all').addEventListener('click', function() {
        switchLayer(terrainLayer, 'btn-terrain-all');
    });

    function switchLayer(layer, buttonId) {
        map.removeLayer(streetLayer);
        map.removeLayer(satelliteLayer);
        map.removeLayer(terrainLayer);
        layer.addTo(map);

        ['btn-street-all', 'btn-satellite-all', 'btn-terrain-all'].forEach(id => {
            document.getElementById(id).classList.remove('active');
        });
        document.getElementById(buttonId).classList.add('active');
    }

    // Marker cluster group
    const markers = L.markerClusterGroup({
        maxClusterRadius: 50,
        spiderfyOnMaxZoom: true,
        showCoverageOnHover: false
    });

    // Polygons layer group
    const polygonsLayer = L.layerGroup().addTo(map);

    // Store all features for filtering
    let allFeatures = [];

    // Get marker color based on certification status
    function getMarkerColor(status) {
        switch(status) {
            case 'organic': return '#27ae60';
            case 'in-conversion': return '#f39c12';
            default: return '#95a5a6';
        }
    }

    // Create custom marker
    function createMarker(feature) {
        const props = feature.properties;
        const color = getMarkerColor(props.status);

        const icon = L.divIcon({
            className: 'custom-marker',
            html: `<div style="background-color: ${color}; width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 12px; box-shadow: 0 2px 5px rgba(0,0,0,0.3); border: 2px solid white;"><i class="fas fa-tractor"></i></div>`,
            iconSize: [28, 28],
            iconAnchor: [14, 14],
            popupAnchor: [0, -14]
        });

        // Get center point from polygon or use first coordinate
        let lat, lng;
        if (feature.geometry.coordinates[0].length > 0) {
            const coords = feature.geometry.coordinates[0];
            const validCoords = coords.filter(c => c && c.length === 2 && c[0] && c[1]);
            if (validCoords.length > 0) {
                // Calculate centroid
                let sumLng = 0, sumLat = 0;
                validCoords.forEach(c => {
                    sumLng += c[0];
                    sumLat += c[1];
                });
                lng = sumLng / validCoords.length;
                lat = sumLat / validCoords.length;
            }
        }

        if (!lat || !lng) return null;

        const marker = L.marker([lat, lng], { icon: icon });

        const popupContent = `
            <div class="farm-popup">
                <h5><i class="fas fa-tractor"></i> ${props.name}</h5>
                <p><strong>Code:</strong> ${props.code}</p>
                <p><strong>Farmer:</strong> ${props.farmer || 'N/A'}</p>
                <p><strong>Area:</strong> ${props.area || 0} ha</p>
                <p>
                    <span class="badge" style="background-color: ${color}; color: white;">
                        ${props.status || 'N/A'}
                    </span>
                </p>
                <div class="mt-2">
                    <a href="/farms/${props.id}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-eye"></i> View
                    </a>
                    <a href="/farms/${props.id}/map" class="btn btn-sm btn-outline-success">
                        <i class="fas fa-map"></i> Map
                    </a>
                </div>
            </div>
        `;

        marker.bindPopup(popupContent);
        marker.feature = feature;

        return marker;
    }

    // Create polygon
    function createPolygon(feature) {
        const props = feature.properties;
        const color = getMarkerColor(props.status);
        const coords = feature.geometry.coordinates[0];

        if (!coords || coords.length < 3) return null;

        // Convert GeoJSON coordinates to Leaflet format
        const latLngs = coords
            .filter(c => c && c.length === 2 && c[0] && c[1])
            .map(c => [c[1], c[0]]);

        if (latLngs.length < 3) return null;

        const polygon = L.polygon(latLngs, {
            color: color,
            weight: 2,
            opacity: 0.8,
            fillColor: color,
            fillOpacity: 0.2
        });

        polygon.bindPopup(`
            <div class="farm-popup">
                <h5>${props.name}</h5>
                <p><strong>Area:</strong> ${props.area || 0} ha</p>
                <a href="/farms/${props.id}" class="btn btn-sm btn-outline-primary">View Details</a>
            </div>
        `);

        polygon.feature = feature;
        return polygon;
    }

    // Load farms
    function loadFarms() {
        markers.clearLayers();
        polygonsLayer.clearLayers();
        allFeatures = [];

        totalFarms = 0;
        organicFarms = 0;
        conversionFarms = 0;
        conventionalFarms = 0;
        mappedFarms = 0;

        if (geoJsonData.features && geoJsonData.features.length > 0) {
            geoJsonData.features.forEach(feature => {
                totalFarms++;

                if (feature.properties.status === 'organic') organicFarms++;
                else if (feature.properties.status === 'in-conversion') conversionFarms++;
                else conventionalFarms++;

                const coords = feature.geometry.coordinates[0];
                if (coords && coords.length >= 3) mappedFarms++;

                // Create marker
                const marker = createMarker(feature);
                if (marker) {
                    markers.addLayer(marker);
                    allFeatures.push({ marker, feature });
                }

                // Create polygon
                const polygon = createPolygon(feature);
                if (polygon) {
                    polygonsLayer.addLayer(polygon);
                    const existingFeature = allFeatures.find(f => f.feature.properties.id === feature.properties.id);
                    if (existingFeature) {
                        existingFeature.polygon = polygon;
                    }
                }
            });

            map.addLayer(markers);

            // Fit bounds if we have markers
            if (markers.getLayers().length > 0) {
                map.fitBounds(markers.getBounds(), { padding: [50, 50] });
            }
        }

        // Update stats
        document.getElementById('total-farms').textContent = totalFarms;
        document.getElementById('organic-farms').textContent = organicFarms;
        document.getElementById('conversion-farms').textContent = conversionFarms;
        document.getElementById('conventional-farms').textContent = conventionalFarms;
        document.getElementById('mapped-farms').textContent = mappedFarms;
    }

    // Filter farms
    function filterFarms() {
        const certFilter = document.getElementById('filter-certification').value;
        const statusFilter = document.getElementById('filter-status').value;
        const showBoundaries = document.getElementById('show-boundaries').checked;

        markers.clearLayers();
        polygonsLayer.clearLayers();

        allFeatures.forEach(({ marker, polygon, feature }) => {
            let show = true;

            if (certFilter && feature.properties.status !== certFilter) {
                show = false;
            }

            // Note: status filter would need actual status field, currently using certification_status
            // Adjust if you have separate status field

            if (show && marker) {
                markers.addLayer(marker);
            }

            if (show && showBoundaries && polygon) {
                polygonsLayer.addLayer(polygon);
            }
        });
    }

    // Event listeners for filters
    document.getElementById('filter-certification').addEventListener('change', filterFarms);
    document.getElementById('filter-status').addEventListener('change', filterFarms);
    document.getElementById('show-boundaries').addEventListener('change', filterFarms);

    // Search functionality
    const searchInput = document.getElementById('farm-search');
    const searchResults = document.getElementById('search-results');

    searchInput.addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();

        if (query.length < 2) {
            searchResults.style.display = 'none';
            return;
        }

        const matches = allFeatures.filter(({ feature }) => {
            const name = (feature.properties.name || '').toLowerCase();
            const code = (feature.properties.code || '').toLowerCase();
            return name.includes(query) || code.includes(query);
        }).slice(0, 10);

        if (matches.length === 0) {
            searchResults.innerHTML = '<div class="search-result-item text-muted">No farms found</div>';
        } else {
            searchResults.innerHTML = matches.map(({ feature }) => `
                <div class="search-result-item" data-id="${feature.properties.id}">
                    <strong>${feature.properties.name}</strong>
                    <small class="text-muted d-block">${feature.properties.code} - ${feature.properties.farmer || 'N/A'}</small>
                </div>
            `).join('');
        }

        searchResults.style.display = 'block';
    });

    searchResults.addEventListener('click', function(e) {
        const item = e.target.closest('.search-result-item');
        if (!item) return;

        const id = parseInt(item.dataset.id);
        const match = allFeatures.find(({ feature }) => feature.properties.id === id);

        if (match && match.marker) {
            map.setView(match.marker.getLatLng(), 16);
            match.marker.openPopup();
        }

        searchResults.style.display = 'none';
        searchInput.value = '';
    });

    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
            searchResults.style.display = 'none';
        }
    });

    // Export GeoJSON
    function exportAllGeoJSON() {
        const dataStr = "data:text/json;charset=utf-8," + encodeURIComponent(JSON.stringify(geoJsonData, null, 2));
        const downloadAnchor = document.createElement('a');
        downloadAnchor.setAttribute("href", dataStr);
        downloadAnchor.setAttribute("download", "all-farms.geojson");
        document.body.appendChild(downloadAnchor);
        downloadAnchor.click();
        downloadAnchor.remove();
    }

    // Add scale control
    L.control.scale({ metric: true, imperial: false }).addTo(map);

    // Initialize
    loadFarms();
</script>
@endpush
