@extends('layouts.base')

@section('title', 'Edit Boundaries - ' . $farm->display_name)

@push('styles')
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<!-- Leaflet Draw CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.css" />
<style>
    #boundary-map {
        height: 600px;
        width: 100%;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }

    .map-container {
        background-color: white;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        margin-bottom: 20px;
    }

    .controls-panel {
        background-color: white;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        margin-bottom: 20px;
    }

    .coordinates-table {
        max-height: 300px;
        overflow-y: auto;
    }

    .coordinates-table table {
        font-size: 0.85rem;
    }

    .instruction-card {
        background-color: #e8f5e9;
        border-left: 4px solid #27ae60;
        padding: 15px;
        border-radius: 0 8px 8px 0;
        margin-bottom: 20px;
    }

    .instruction-card h6 {
        color: #27ae60;
        margin-bottom: 10px;
    }

    .instruction-card ul {
        margin-bottom: 0;
        padding-left: 20px;
    }

    .instruction-card li {
        margin-bottom: 5px;
    }

    .alert-info-custom {
        background-color: #e3f2fd;
        border-color: #90caf9;
        color: #1565c0;
    }

    .point-badge {
        display: inline-block;
        width: 24px;
        height: 24px;
        line-height: 24px;
        text-align: center;
        background-color: #27ae60;
        color: white;
        border-radius: 50%;
        font-size: 0.75rem;
        font-weight: bold;
    }

    .btn-action {
        margin-bottom: 10px;
    }

    .status-indicator {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px;
        border-radius: 5px;
        margin-bottom: 15px;
    }

    .status-indicator.has-boundary {
        background-color: #d4edda;
        color: #155724;
    }

    .status-indicator.no-boundary {
        background-color: #fff3cd;
        color: #856404;
    }

    .gps-input-group {
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 15px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2><i class="fas fa-draw-polygon text-info"></i> Edit Farm Boundaries</h2>
            <p class="text-muted mb-0">{{ $farm->display_name }} - {{ $farm->code }}</p>
        </div>
        <div class="btn-group">
            <a href="{{ route('farms.show', $farm) }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to Farm
            </a>
            <a href="{{ route('farms.map', $farm) }}" class="btn btn-outline-primary">
                <i class="fas fa-map"></i> View Map
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Map Column -->
        <div class="col-lg-8">
            <!-- Instructions -->
            <div class="instruction-card">
                <h6><i class="fas fa-info-circle"></i> How to Draw Boundaries</h6>
                <ul>
                    <li><strong>Draw:</strong> Click the polygon icon <i class="fas fa-draw-polygon"></i> in the toolbar, then click on the map to add points. Double-click to finish.</li>
                    <li><strong>Edit:</strong> Click the edit icon <i class="fas fa-edit"></i> to modify existing boundaries by dragging points.</li>
                    <li><strong>Delete:</strong> Click the delete icon <i class="fas fa-trash"></i> to remove boundaries.</li>
                    <li><strong>Save:</strong> Click "Save Boundaries" when done to store the coordinates.</li>
                </ul>
            </div>

            <!-- Map -->
            <div class="map-container">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0"><i class="fas fa-map"></i> Draw Boundary</h5>
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-secondary active" id="btn-street">
                            <i class="fas fa-road"></i> Street
                        </button>
                        <button type="button" class="btn btn-outline-secondary" id="btn-satellite">
                            <i class="fas fa-satellite"></i> Satellite
                        </button>
                    </div>
                </div>
                <div id="boundary-map"></div>
            </div>
        </div>

        <!-- Controls Column -->
        <div class="col-lg-4">
            <!-- Status -->
            <div class="controls-panel">
                <div class="status-indicator {{ $farm->has_boundary ? 'has-boundary' : 'no-boundary' }}">
                    @if($farm->has_boundary)
                        <i class="fas fa-check-circle fa-lg"></i>
                        <span>Boundary mapped ({{ $farm->boundaries->count() }} points)</span>
                    @else
                        <i class="fas fa-exclamation-triangle fa-lg"></i>
                        <span>No boundary mapped yet</span>
                    @endif
                </div>

                <h5 class="mb-3"><i class="fas fa-tools"></i> Actions</h5>

                <button class="btn btn-success w-100 btn-action" id="btn-save" disabled>
                    <i class="fas fa-save"></i> Save Boundaries
                </button>

                <button class="btn btn-warning w-100 btn-action" id="btn-clear">
                    <i class="fas fa-eraser"></i> Clear All Points
                </button>

                <button class="btn btn-outline-secondary w-100 btn-action" id="btn-center">
                    <i class="fas fa-crosshairs"></i> Center on Farm
                </button>

                <hr>

                <!-- Manual GPS Entry -->
                <h6 class="mb-3"><i class="fas fa-map-marker-alt"></i> Add Point Manually</h6>
                <div class="gps-input-group">
                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <input type="number" class="form-control form-control-sm" id="manual-lat" placeholder="Latitude" step="0.000001">
                        </div>
                        <div class="col-6">
                            <input type="number" class="form-control form-control-sm" id="manual-lng" placeholder="Longitude" step="0.000001">
                        </div>
                    </div>
                    <button class="btn btn-sm btn-outline-primary w-100" id="btn-add-point">
                        <i class="fas fa-plus"></i> Add Point
                    </button>
                </div>

                <hr>

                <!-- Current Location -->
                <button class="btn btn-outline-info w-100 btn-action" id="btn-my-location">
                    <i class="fas fa-location-arrow"></i> Use My Location
                </button>
            </div>

            <!-- Coordinates List -->
            <div class="controls-panel">
                <h5 class="mb-3"><i class="fas fa-list-ol"></i> Boundary Points</h5>
                <div class="coordinates-table" id="coordinates-list">
                    <p class="text-muted text-center py-3">No points added yet</p>
                </div>
                <div class="mt-3" id="total-points">
                    <small class="text-muted">Total Points: <strong id="point-count">0</strong></small>
                </div>
            </div>

            <!-- Farm Info -->
            <div class="controls-panel">
                <h6 class="mb-3"><i class="fas fa-info-circle"></i> Farm Information</h6>
                <table class="table table-sm table-borderless">
                    <tr>
                        <td class="text-muted">Farmer:</td>
                        <td>{{ $farm->farmer->full_name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Area:</td>
                        <td>{{ $farm->total_area }} ha</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Location:</td>
                        <td>{{ $farm->village->name ?? '' }}, {{ $farm->district->name ?? '' }}</td>
                    </tr>
                    @if($farm->latitude && $farm->longitude)
                    <tr>
                        <td class="text-muted">Center:</td>
                        <td><code>{{ $farm->latitude }}, {{ $farm->longitude }}</code></td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Save Confirmation Modal -->
<div class="modal fade" id="saveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-save"></i> Save Boundaries</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>You are about to save <strong id="modal-point-count">0</strong> boundary points for this farm.</p>
                <p class="text-warning"><i class="fas fa-exclamation-triangle"></i> This will replace any existing boundaries.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="btn-confirm-save">
                    <i class="fas fa-check"></i> Confirm Save
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<!-- Leaflet Draw JS -->
<script src="https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.js"></script>
<script>
    // CSRF Token
    const csrfToken = '{{ csrf_token() }}';

    // Farm data
    const farmData = {
        id: {{ $farm->id }},
        latitude: {{ $farm->latitude ?? 'null' }},
        longitude: {{ $farm->longitude ?? 'null' }},
        hasBoundary: {{ $farm->has_boundary ? 'true' : 'false' }},
        boundaries: {!! json_encode($farm->boundaries->map(function($b) { return ['lat' => $b->latitude, 'lng' => $b->longitude]; })) !!},
        boundaryCoordinates: {!! json_encode($farm->boundary_coordinates ?? []) !!}
    };

    // Default center (Tanzania)
    const defaultCenter = [-6.369028, 34.888822];
    const defaultZoom = 6;

    // Determine map center
    let mapCenter = defaultCenter;
    let mapZoom = defaultZoom;

    if (farmData.latitude && farmData.longitude) {
        mapCenter = [farmData.latitude, farmData.longitude];
        mapZoom = 16;
    } else if (farmData.boundaries.length > 0) {
        mapCenter = [farmData.boundaries[0].lat, farmData.boundaries[0].lng];
        mapZoom = 16;
    }

    // Initialize map
    const map = L.map('boundary-map').setView(mapCenter, mapZoom);

    // Tile layers
    const streetLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap'
    });

    const satelliteLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        maxZoom: 19,
        attribution: 'Tiles &copy; Esri'
    });

    streetLayer.addTo(map);

    // Layer switching
    document.getElementById('btn-street').addEventListener('click', function() {
        map.removeLayer(satelliteLayer);
        streetLayer.addTo(map);
        this.classList.add('active');
        document.getElementById('btn-satellite').classList.remove('active');
    });

    document.getElementById('btn-satellite').addEventListener('click', function() {
        map.removeLayer(streetLayer);
        satelliteLayer.addTo(map);
        this.classList.add('active');
        document.getElementById('btn-street').classList.remove('active');
    });

    // Feature group for drawn items
    const drawnItems = new L.FeatureGroup();
    map.addLayer(drawnItems);

    // Draw control
    const drawControl = new L.Control.Draw({
        position: 'topleft',
        draw: {
            polygon: {
                allowIntersection: false,
                showArea: true,
                shapeOptions: {
                    color: '#27ae60',
                    weight: 3,
                    fillColor: '#2ecc71',
                    fillOpacity: 0.3
                }
            },
            polyline: false,
            circle: false,
            rectangle: false,
            marker: false,
            circlemarker: false
        },
        edit: {
            featureGroup: drawnItems,
            remove: true
        }
    });
    map.addControl(drawControl);

    // Current polygon and points
    let currentPolygon = null;
    let boundaryPoints = [];

    // Load existing boundary
    function loadExistingBoundary() {
        if (!farmData.hasBoundary) return;

        let coords = [];

        if (farmData.boundaryCoordinates && farmData.boundaryCoordinates.length > 0) {
            coords = farmData.boundaryCoordinates.map(c => [c[1], c[0]]);
        } else if (farmData.boundaries.length > 0) {
            coords = farmData.boundaries.map(b => [b.lat, b.lng]);
        }

        if (coords.length >= 3) {
            currentPolygon = L.polygon(coords, {
                color: '#27ae60',
                weight: 3,
                fillColor: '#2ecc71',
                fillOpacity: 0.3
            });
            drawnItems.addLayer(currentPolygon);
            boundaryPoints = coords.map(c => ({ lat: c[0], lng: c[1] }));
            updateCoordinatesList();
            map.fitBounds(currentPolygon.getBounds(), { padding: [50, 50] });
        }
    }

    // Update coordinates list
    function updateCoordinatesList() {
        const container = document.getElementById('coordinates-list');
        const countEl = document.getElementById('point-count');

        if (boundaryPoints.length === 0) {
            container.innerHTML = '<p class="text-muted text-center py-3">No points added yet</p>';
            countEl.textContent = '0';
            document.getElementById('btn-save').disabled = true;
            return;
        }

        let html = '<table class="table table-sm table-striped mb-0">';
        html += '<thead><tr><th>#</th><th>Latitude</th><th>Longitude</th><th></th></tr></thead><tbody>';

        boundaryPoints.forEach((point, index) => {
            html += `
                <tr>
                    <td><span class="point-badge">${index + 1}</span></td>
                    <td>${point.lat.toFixed(6)}</td>
                    <td>${point.lng.toFixed(6)}</td>
                    <td>
                        <button class="btn btn-sm btn-outline-danger" onclick="removePoint(${index})">
                            <i class="fas fa-times"></i>
                        </button>
                    </td>
                </tr>
            `;
        });

        html += '</tbody></table>';
        container.innerHTML = html;
        countEl.textContent = boundaryPoints.length;

        // Enable save button if we have at least 3 points
        document.getElementById('btn-save').disabled = boundaryPoints.length < 3;
    }

    // Remove a point
    function removePoint(index) {
        boundaryPoints.splice(index, 1);
        redrawPolygon();
        updateCoordinatesList();
    }

    // Redraw polygon from points
    function redrawPolygon() {
        drawnItems.clearLayers();

        if (boundaryPoints.length >= 3) {
            const coords = boundaryPoints.map(p => [p.lat, p.lng]);
            currentPolygon = L.polygon(coords, {
                color: '#27ae60',
                weight: 3,
                fillColor: '#2ecc71',
                fillOpacity: 0.3
            });
            drawnItems.addLayer(currentPolygon);
        } else {
            currentPolygon = null;
        }
    }

    // Draw events
    map.on(L.Draw.Event.CREATED, function(e) {
        drawnItems.clearLayers();
        currentPolygon = e.layer;
        drawnItems.addLayer(currentPolygon);

        // Extract points
        const latLngs = currentPolygon.getLatLngs()[0];
        boundaryPoints = latLngs.map(ll => ({ lat: ll.lat, lng: ll.lng }));
        updateCoordinatesList();
    });

    map.on(L.Draw.Event.EDITED, function(e) {
        const layers = e.layers;
        layers.eachLayer(function(layer) {
            if (layer === currentPolygon) {
                const latLngs = layer.getLatLngs()[0];
                boundaryPoints = latLngs.map(ll => ({ lat: ll.lat, lng: ll.lng }));
                updateCoordinatesList();
            }
        });
    });

    map.on(L.Draw.Event.DELETED, function(e) {
        boundaryPoints = [];
        currentPolygon = null;
        updateCoordinatesList();
    });

    // Clear all points
    document.getElementById('btn-clear').addEventListener('click', function() {
        if (confirm('Are you sure you want to clear all boundary points?')) {
            drawnItems.clearLayers();
            boundaryPoints = [];
            currentPolygon = null;
            updateCoordinatesList();
        }
    });

    // Center on farm
    document.getElementById('btn-center').addEventListener('click', function() {
        if (farmData.latitude && farmData.longitude) {
            map.setView([farmData.latitude, farmData.longitude], 16);
        } else if (boundaryPoints.length > 0) {
            const bounds = L.latLngBounds(boundaryPoints.map(p => [p.lat, p.lng]));
            map.fitBounds(bounds, { padding: [50, 50] });
        }
    });

    // Add point manually
    document.getElementById('btn-add-point').addEventListener('click', function() {
        const lat = parseFloat(document.getElementById('manual-lat').value);
        const lng = parseFloat(document.getElementById('manual-lng').value);

        if (isNaN(lat) || isNaN(lng)) {
            alert('Please enter valid latitude and longitude values.');
            return;
        }

        if (lat < -90 || lat > 90 || lng < -180 || lng > 180) {
            alert('Coordinates are out of valid range.');
            return;
        }

        boundaryPoints.push({ lat, lng });
        redrawPolygon();
        updateCoordinatesList();

        // Clear inputs
        document.getElementById('manual-lat').value = '';
        document.getElementById('manual-lng').value = '';

        // Center on new point
        map.setView([lat, lng], 16);
    });

    // Use my location
    document.getElementById('btn-my-location').addEventListener('click', function() {
        if (!navigator.geolocation) {
            alert('Geolocation is not supported by your browser.');
            return;
        }

        this.disabled = true;
        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Getting location...';

        navigator.geolocation.getCurrentPosition(
            (position) => {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;

                boundaryPoints.push({ lat, lng });
                redrawPolygon();
                updateCoordinatesList();
                map.setView([lat, lng], 18);

                this.disabled = false;
                this.innerHTML = '<i class="fas fa-location-arrow"></i> Use My Location';
            },
            (error) => {
                alert('Unable to get your location: ' + error.message);
                this.disabled = false;
                this.innerHTML = '<i class="fas fa-location-arrow"></i> Use My Location';
            },
            { enableHighAccuracy: true }
        );
    });

    // Save button - show modal
    document.getElementById('btn-save').addEventListener('click', function() {
        document.getElementById('modal-point-count').textContent = boundaryPoints.length;
        new bootstrap.Modal(document.getElementById('saveModal')).show();
    });

    // Confirm save
    document.getElementById('btn-confirm-save').addEventListener('click', function() {
        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';

        const data = {
            boundaries: boundaryPoints.map(p => ({
                latitude: p.lat,
                longitude: p.lng
            }))
        };

        fetch('{{ route("farms.boundaries.store", $farm) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                alert('Boundaries saved successfully!');
                window.location.reload();
            } else {
                alert('Error: ' + (result.message || 'Failed to save boundaries'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while saving boundaries.');
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-check"></i> Confirm Save';
            bootstrap.Modal.getInstance(document.getElementById('saveModal')).hide();
        });
    });

    // Add scale control
    L.control.scale({ metric: true, imperial: false }).addTo(map);

    // Initialize
    loadExistingBoundary();
</script>
@endpush
