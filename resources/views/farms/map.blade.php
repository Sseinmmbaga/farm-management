@extends('layouts.base')

@section('title', $farm->display_name . ' - Map View')

@push('styles')
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<style>
    #farm-map {
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

    .farm-info-panel {
        background-color: white;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        margin-bottom: 20px;
    }

    .info-item {
        padding: 10px 0;
        border-bottom: 1px solid #eee;
    }

    .info-item:last-child {
        border-bottom: none;
    }

    .info-label {
        font-weight: 600;
        color: #666;
    }

    .leaflet-popup-content-wrapper {
        border-radius: 8px;
    }

    .farm-popup h5 {
        color: #27ae60;
        margin-bottom: 10px;
    }

    .farm-popup .badge {
        font-size: 0.75rem;
    }

    .map-legend {
        background: white;
        padding: 10px 15px;
        border-radius: 5px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }

    .map-legend h6 {
        margin-bottom: 10px;
        font-weight: 600;
    }

    .legend-item {
        display: flex;
        align-items: center;
        margin-bottom: 5px;
    }

    .legend-color {
        width: 20px;
        height: 20px;
        margin-right: 8px;
        border-radius: 3px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2><i class="fas fa-map-marked-alt text-success"></i> {{ $farm->display_name }}</h2>
            <p class="text-muted mb-0">Farm Map View - Code: {{ $farm->code }}</p>
        </div>
        <div class="btn-group">
            <a href="{{ route('farms.show', $farm) }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to Details
            </a>
            <a href="{{ route('farms.boundaries', $farm) }}" class="btn btn-outline-info">
                <i class="fas fa-draw-polygon"></i> Edit Boundaries
            </a>
            <a href="{{ route('farms.map.all') }}" class="btn btn-outline-primary">
                <i class="fas fa-globe"></i> All Farms Map
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Map Column -->
        <div class="col-lg-8">
            <div class="map-container">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0"><i class="fas fa-map"></i> Farm Location</h5>
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-secondary" id="btn-satellite">
                            <i class="fas fa-satellite"></i> Satellite
                        </button>
                        <button type="button" class="btn btn-outline-secondary active" id="btn-street">
                            <i class="fas fa-road"></i> Street
                        </button>
                        <button type="button" class="btn btn-outline-secondary" id="btn-terrain">
                            <i class="fas fa-mountain"></i> Terrain
                        </button>
                    </div>
                </div>
                <div id="farm-map"></div>
            </div>

            <!-- Coordinates Info -->
            @if($farm->has_boundary)
            <div class="map-container">
                <h5 class="mb-3"><i class="fas fa-vector-square"></i> Boundary Coordinates</h5>
                <div class="table-responsive">
                    <table class="table table-sm table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Latitude</th>
                                <th>Longitude</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($farm->boundaries as $index => $boundary)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ number_format($boundary->latitude, 6) }}</td>
                                <td>{{ number_format($boundary->longitude, 6) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>

        <!-- Info Panel Column -->
        <div class="col-lg-4">
            <div class="farm-info-panel">
                <h5 class="mb-3"><i class="fas fa-info-circle text-primary"></i> Farm Information</h5>

                <div class="info-item">
                    <div class="info-label">Farmer</div>
                    <div>{{ $farm->farmer->full_name ?? 'N/A' }}</div>
                </div>

                <div class="info-item">
                    <div class="info-label">Status</div>
                    <div>
                        <span class="badge bg-{{ $farm->status_color }}">{{ $farm->status_label }}</span>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-label">Certification</div>
                    <div>
                        <span class="badge bg-info">{{ $farm->certification_status_label }}</span>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-label">Total Area</div>
                    <div><strong>{{ $farm->total_area }}</strong> hectares</div>
                </div>

                <div class="info-item">
                    <div class="info-label">Cultivated Area</div>
                    <div><strong>{{ $farm->cultivated_area }}</strong> hectares</div>
                </div>

                <div class="info-item">
                    <div class="info-label">Location</div>
                    <div>
                        {{ $farm->village->name ?? '' }},
                        {{ $farm->district->name ?? '' }},
                        {{ $farm->region->name ?? '' }}
                    </div>
                </div>

                @if($farm->latitude && $farm->longitude)
                <div class="info-item">
                    <div class="info-label">GPS Coordinates</div>
                    <div>
                        <code>{{ $farm->latitude }}, {{ $farm->longitude }}</code>
                        <button class="btn btn-sm btn-outline-secondary ms-2" onclick="copyCoordinates()">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                </div>
                @endif

                <div class="info-item">
                    <div class="info-label">Boundary Status</div>
                    <div>
                        @if($farm->has_boundary)
                            <span class="text-success"><i class="fas fa-check-circle"></i> Mapped ({{ $farm->boundaries->count() }} points)</span>
                        @else
                            <span class="text-warning"><i class="fas fa-exclamation-circle"></i> Not mapped</span>
                        @endif
                    </div>
                </div>

                @if($farm->soil_type)
                <div class="info-item">
                    <div class="info-label">Soil Type</div>
                    <div>{{ $farm->soil_type }}</div>
                </div>
                @endif

                @if($farm->water_source)
                <div class="info-item">
                    <div class="info-label">Water Source</div>
                    <div>{{ $farm->water_source }}</div>
                </div>
                @endif

                @if($farm->terrain)
                <div class="info-item">
                    <div class="info-label">Terrain</div>
                    <div>{{ $farm->terrain }}</div>
                </div>
                @endif
            </div>

            <!-- Quick Actions -->
            <div class="farm-info-panel">
                <h5 class="mb-3"><i class="fas fa-bolt text-warning"></i> Quick Actions</h5>
                <div class="d-grid gap-2">
                    <a href="{{ route('farms.boundaries', $farm) }}" class="btn btn-info">
                        <i class="fas fa-draw-polygon"></i> Edit Farm Boundaries
                    </a>
                    <a href="{{ route('farms.edit', $farm) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Edit Farm Details
                    </a>
                    <button class="btn btn-outline-secondary" onclick="downloadGeoJSON()">
                        <i class="fas fa-download"></i> Download GeoJSON
                    </button>
                    <button class="btn btn-outline-secondary" onclick="printMap()">
                        <i class="fas fa-print"></i> Print Map
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    // Farm data from Laravel
    const farmData = {
        id: {{ $farm->id }},
        name: "{{ $farm->display_name }}",
        code: "{{ $farm->code }}",
        latitude: {{ $farm->latitude ?? 'null' }},
        longitude: {{ $farm->longitude ?? 'null' }},
        totalArea: {{ $farm->total_area }},
        cultivatedArea: {{ $farm->cultivated_area }},
        status: "{{ $farm->status_label }}",
        certification: "{{ $farm->certification_status_label }}",
        farmer: "{{ $farm->farmer->full_name ?? 'N/A' }}",
        hasBoundary: {{ $farm->has_boundary ? 'true' : 'false' }},
        boundaryCoordinates: {!! json_encode($farm->boundary_coordinates ?? []) !!},
        boundaries: {!! json_encode($farm->boundaries->map(function($b) { return ['lat' => $b->latitude, 'lng' => $b->longitude]; })) !!}
    };

    // Default center (Tanzania)
    const defaultCenter = [-6.369028, 34.888822];
    const defaultZoom = 6;

    // Determine map center
    let mapCenter = defaultCenter;
    let mapZoom = defaultZoom;

    if (farmData.latitude && farmData.longitude) {
        mapCenter = [farmData.latitude, farmData.longitude];
        mapZoom = 15;
    } else if (farmData.boundaries.length > 0) {
        mapCenter = [farmData.boundaries[0].lat, farmData.boundaries[0].lng];
        mapZoom = 15;
    }

    // Initialize map
    const map = L.map('farm-map').setView(mapCenter, mapZoom);

    // Tile layers
    const streetLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    });

    const satelliteLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        maxZoom: 19,
        attribution: 'Tiles &copy; Esri'
    });

    const terrainLayer = L.tileLayer('https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png', {
        maxZoom: 17,
        attribution: '&copy; <a href="https://opentopomap.org">OpenTopoMap</a>'
    });

    // Add default layer
    streetLayer.addTo(map);

    // Layer switching
    document.getElementById('btn-street').addEventListener('click', function() {
        map.removeLayer(satelliteLayer);
        map.removeLayer(terrainLayer);
        streetLayer.addTo(map);
        updateLayerButtons('btn-street');
    });

    document.getElementById('btn-satellite').addEventListener('click', function() {
        map.removeLayer(streetLayer);
        map.removeLayer(terrainLayer);
        satelliteLayer.addTo(map);
        updateLayerButtons('btn-satellite');
    });

    document.getElementById('btn-terrain').addEventListener('click', function() {
        map.removeLayer(streetLayer);
        map.removeLayer(satelliteLayer);
        terrainLayer.addTo(map);
        updateLayerButtons('btn-terrain');
    });

    function updateLayerButtons(activeId) {
        ['btn-street', 'btn-satellite', 'btn-terrain'].forEach(id => {
            document.getElementById(id).classList.remove('active');
        });
        document.getElementById(activeId).classList.add('active');
    }

    // Custom marker icon
    const farmIcon = L.divIcon({
        className: 'farm-marker',
        html: '<div style="background-color: #27ae60; width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 14px; box-shadow: 0 2px 5px rgba(0,0,0,0.3);"><i class="fas fa-tractor"></i></div>',
        iconSize: [30, 30],
        iconAnchor: [15, 15],
        popupAnchor: [0, -15]
    });

    // Add farm marker if coordinates exist
    if (farmData.latitude && farmData.longitude) {
        const marker = L.marker([farmData.latitude, farmData.longitude], { icon: farmIcon }).addTo(map);

        const popupContent = `
            <div class="farm-popup">
                <h5><i class="fas fa-tractor"></i> ${farmData.name}</h5>
                <p><strong>Code:</strong> ${farmData.code}</p>
                <p><strong>Farmer:</strong> ${farmData.farmer}</p>
                <p><strong>Area:</strong> ${farmData.totalArea} ha</p>
                <p>
                    <span class="badge bg-success">${farmData.status}</span>
                    <span class="badge bg-info">${farmData.certification}</span>
                </p>
            </div>
        `;

        marker.bindPopup(popupContent);
    }

    // Draw farm boundary polygon
    let farmPolygon = null;

    if (farmData.hasBoundary) {
        let polygonCoords = [];

        // Use boundary_coordinates if available (GeoJSON format: [lng, lat])
        if (farmData.boundaryCoordinates && farmData.boundaryCoordinates.length > 0) {
            polygonCoords = farmData.boundaryCoordinates.map(coord => [coord[1], coord[0]]);
        }
        // Otherwise use boundaries relationship
        else if (farmData.boundaries.length > 0) {
            polygonCoords = farmData.boundaries.map(b => [b.lat, b.lng]);
        }

        if (polygonCoords.length >= 3) {
            farmPolygon = L.polygon(polygonCoords, {
                color: '#27ae60',
                weight: 3,
                opacity: 0.8,
                fillColor: '#2ecc71',
                fillOpacity: 0.3
            }).addTo(map);

            // Add popup to polygon
            farmPolygon.bindPopup(`
                <div class="farm-popup">
                    <h5><i class="fas fa-draw-polygon"></i> ${farmData.name}</h5>
                    <p><strong>Area:</strong> ${farmData.totalArea} hectares</p>
                    <p><strong>Boundary Points:</strong> ${polygonCoords.length}</p>
                </div>
            `);

            // Fit map to polygon bounds
            map.fitBounds(farmPolygon.getBounds(), { padding: [50, 50] });
        }
    }

    // Add scale control
    L.control.scale({ metric: true, imperial: false }).addTo(map);

    // Helper functions
    function copyCoordinates() {
        const coords = `${farmData.latitude}, ${farmData.longitude}`;
        navigator.clipboard.writeText(coords).then(() => {
            alert('Coordinates copied to clipboard!');
        });
    }

    function downloadGeoJSON() {
        const geoJSON = {
            type: 'Feature',
            properties: {
                id: farmData.id,
                name: farmData.name,
                code: farmData.code,
                farmer: farmData.farmer,
                area: farmData.totalArea,
                status: farmData.status,
                certification: farmData.certification
            },
            geometry: {
                type: 'Polygon',
                coordinates: [farmData.boundaryCoordinates.length > 0 ? farmData.boundaryCoordinates :
                    farmData.boundaries.map(b => [b.lng, b.lat])]
            }
        };

        const dataStr = "data:text/json;charset=utf-8," + encodeURIComponent(JSON.stringify(geoJSON, null, 2));
        const downloadAnchor = document.createElement('a');
        downloadAnchor.setAttribute("href", dataStr);
        downloadAnchor.setAttribute("download", `farm-${farmData.code}.geojson`);
        document.body.appendChild(downloadAnchor);
        downloadAnchor.click();
        downloadAnchor.remove();
    }

    function printMap() {
        window.print();
    }
</script>
@endpush
