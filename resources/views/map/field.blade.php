<div class="map-field-container">
    <label for="{{ $field['name'] }}">{{ $field['label'] ?? ucfirst(str_replace('_', ' ', $field['name'])) }}</label>
    
    <div class="map-wrapper" style="position: relative;">
        <div id="map-{{ $field['name'] }}" 
             class="map-preview" 
             style="height: 300px; width: 100%; border: 1px solid #ddd; border-radius: 4px;">
            <!-- Map will be rendered here -->
        </div>
        
        <div class="map-coordinates" style="margin-top: 10px;">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="{{ $field['name'] }}_latitude">Latitude</label>
                        <input type="text" 
                               id="{{ $field['name'] }}_latitude" 
                               name="{{ $field['name'] }}[latitude]" 
                               value="{{ old($field['name'] . '.latitude', $field['value']['latitude'] ?? '') }}" 
                               class="form-control" 
                               placeholder="Enter latitude"
                               readonly>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="{{ $field['name'] }}_longitude">Longitude</label>
                        <input type="text" 
                               id="{{ $field['name'] }}_longitude" 
                               name="{{ $field['name'] }}[longitude]" 
                               value="{{ old($field['name'] . '.longitude', $field['value']['longitude'] ?? '') }}" 
                               class="form-control" 
                               placeholder="Enter longitude"
                               readonly>
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <label for="{{ $field['name'] }}_address">Address</label>
                <div class="input-group">
                    <input type="text" 
                           id="{{ $field['name'] }}_address" 
                           name="{{ $field['name'] }}[address]" 
                           value="{{ old($field['name'] . '.address', $field['value']['address'] ?? '') }}" 
                           class="form-control" 
                           placeholder="Search for location or enter address">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-outline-secondary" id="search-address-{{ $field['name'] }}">
                            <i class="fas fa-search"></i> Search
                        </button>
                        <button type="button" class="btn btn-outline-secondary" id="clear-map-{{ $field['name'] }}">
                            <i class="fas fa-times"></i> Clear
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    @if($field['description'] ?? false)
        <small class="form-text text-muted">{{ $field['description'] }}</small>
    @endif
    
    @error($field['name'] . '.*')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize map for field: {{ $field['name'] }}
    const mapId = 'map-{{ $field['name'] }}';
    const latInput = document.getElementById('{{ $field['name'] }}_latitude');
    const lngInput = document.getElementById('{{ $field['name'] }}_longitude');
    const addressInput = document.getElementById('{{ $field['name'] }}_address');
    const searchBtn = document.getElementById('search-address-{{ $field['name'] }}');
    const clearBtn = document.getElementById('clear-map-{{ $field['name'] }}');
    
    let map;
    let marker;
    
    // Default center (can be configured via field options)
    const defaultCenter = { lat: 40.7128, lng: -74.0060 }; // New York
    
    // Initialize map
    function initMap() {
        // Check if map container exists
        const mapElement = document.getElementById(mapId);
        if (!mapElement) return;
        
        // Parse initial coordinates
        let initialLat = parseFloat(latInput.value);
        let initialLng = parseFloat(lngInput.value);
        
        // Use provided coordinates or default center
        const center = (!isNaN(initialLat) && !isNaN(initialLng)) 
            ? { lat: initialLat, lng: initialLng }
            : defaultCenter;
        
        // Create map
        map = new google.maps.Map(mapElement, {
            center: center,
            zoom: 12,
            streetViewControl: false,
            mapTypeControl: true,
            fullscreenControl: true
        });
        
        // Add marker if coordinates exist
        if (!isNaN(initialLat) && !isNaN(initialLng)) {
            marker = new google.maps.Marker({
                position: { lat: initialLat, lng: initialLng },
                map: map,
                draggable: true
            });
            
            // Update inputs when marker is dragged
            marker.addListener('dragend', function() {
                updateCoordinates(marker.getPosition());
            });
        }
        
        // Add click listener to place marker
        map.addListener('click', function(event) {
            placeMarker(event.latLng);
        });
        
        // Search button click handler
        searchBtn.addEventListener('click', function() {
            geocodeAddress(addressInput.value);
        });
        
        // Clear button click handler
        clearBtn.addEventListener('click', function() {
            clearMarker();
        });
        
        // Address input enter key handler
        addressInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                geocodeAddress(addressInput.value);
            }
        });
    }
    
    // Place marker at given position
    function placeMarker(position) {
        if (marker) {
            marker.setPosition(position);
        } else {
            marker = new google.maps.Marker({
                position: position,
                map: map,
                draggable: true
            });
            
            marker.addListener('dragend', function() {
                updateCoordinates(marker.getPosition());
            });
        }
        
        updateCoordinates(position);
        map.panTo(position);
    }
    
    // Update coordinate inputs
    function updateCoordinates(position) {
        latInput.value = position.lat().toFixed(6);
        lngInput.value = position.lng().toFixed(6);
        
        // Reverse geocode to get address
        reverseGeocode(position);
    }
    
    // Geocode address to coordinates
    function geocodeAddress(address) {
        if (!address.trim()) return;
        
        const geocoder = new google.maps.Geocoder();
        geocoder.geocode({ address: address }, function(results, status) {
            if (status === 'OK' && results[0]) {
                const location = results[0].geometry.location;
                placeMarker(location);
                addressInput.value = results[0].formatted_address;
            } else {
                alert('Address not found. Please try a different address.');
            }
        });
    }
    
    // Reverse geocode coordinates to address
    function reverseGeocode(position) {
        const geocoder = new google.maps.Geocoder();
        geocoder.geocode({ location: position }, function(results, status) {
            if (status === 'OK' && results[0]) {
                addressInput.value = results[0].formatted_address;
            }
        });
    }
    
    // Clear marker and inputs
    function clearMarker() {
        if (marker) {
            marker.setMap(null);
            marker = null;
        }
        latInput.value = '';
        lngInput.value = '';
        addressInput.value = '';
    }
    
    // Load Google Maps API if not already loaded
    if (typeof google === 'undefined') {
        const script = document.createElement('script');
        script.src = `https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}&callback=initMap&libraries=places`;
        script.async = true;
        script.defer = true;
        document.head.appendChild(script);
        
        // Define global callback
        window.initMap = initMap;
    } else {
        initMap();
    }
});
</script>
@endpush

@push('styles')
<style>
.map-field-container {
    margin-bottom: 20px;
}

.map-field-container label {
    font-weight: 600;
    margin-bottom: 8px;
    display: block;
}

.map-preview {
    min-height: 300px;
    background-color: #f8f9fa;
}

.map-coordinates .form-group {
    margin-bottom: 15px;
}

.map-coordinates label {
    font-weight: 500;
    font-size: 14px;
    margin-bottom: 5px;
}

.input-group-append .btn {
    margin-left: 5px;
}
</style>
@endpush