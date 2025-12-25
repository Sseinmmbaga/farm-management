"<!DOCTYPE html>
<html lang=\"en\">
<head>
    <meta charset=\"UTF-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <meta http-equiv=\"X-UA-Compatible\" content=\"ie=edge\">
    <title>Add New Field - {{ $farm->display_name }} - Remei Farm OS</title>
    
    <link href=\"https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css\" rel=\"stylesheet\">
    <link rel=\"stylesheet\" href=\"https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css\">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f5f7fa; padding: 20px; }
        .navbar { background-color: #27ae60; margin-bottom: 20px; }
        .navbar-brand { color: white !important; font-weight: bold; }
        .card { border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); margin-bottom: 20px; border: none; }
        .card-header { background-color: #27ae60; color: white; border-radius: 10px 10px 0 0 !important; padding: 15px 20px; }
        .form-section { border-left: 4px solid #27ae60; padding-left: 15px; margin-bottom: 25px; }
        .form-section h5 { color: #27ae60; margin-bottom: 15px; }
    </style>
</head>
<body>
    <nav class=\"navbar navbar-expand-lg navbar-dark\">
        <div class=\"container\">
            <a class=\"navbar-brand\" href=\"/\"><i class=\"fas fa-seedling\"></i> Remei Farm OS</a>
            <div class=\"navbar-nav ms-auto\">
                <a class=\"nav-link\" href=\"{{ route('fields.index', $farm) }}\">Back to Fields</a>
                <a class=\"nav-link\" href=\"{{ route('farms.show', $farm) }}\">Back to Farm</a>
            </div>
        </div>
    </nav>

    <div class=\"container\">
        <div class=\"card\">
            <div class=\"card-header\">
                <h4 class=\"mb-0\"><i class=\"fas fa-plus\"></i> Add New Field to {{ $farm->display_name }}</h4>
            </div>
            <div class=\"card-body\">
                <form method=\"POST\" action=\"{{ route('fields.store', $farm) }}\">
                    @csrf
                    
                    <!-- Basic Information -->
                    <div class=\"form-section\">
                        <h5><i class=\"fas fa-info-circle\"></i> Basic Information</h5>
                        
                        <div class=\"row mb-3\">
                            <div class=\"col-md-6\">
                                <label for=\"name\" class=\"form-label\">Field Name *</label>
                                <input type=\"text\" class=\"form-control\" id=\"name\" name=\"name\" required 
                                       placeholder=\"e.g., North Field, Plot A\">
                            </div>
                            <div class=\"col-md-6\">
                                <label for=\"location_description\" class=\"form-label\">Location Description</label>
                                <input type=\"text\" class=\"form-control\" id=\"location_description\" name=\"location_description\" 
                                       placeholder=\"e.g., Northwest corner of the farm\">
                            </div>
                        </div>
                        
                        <div class=\"row mb-3\">
                            <div class=\"col-md-6\">
                                <label for=\"total_area\" class=\"form-label\">Total Area *</label>
                                <div class=\"input-group\">
                                    <input type=\"number\" step=\"0.01\" class=\"form-control\" id=\"total_area\" name=\"total_area\" 
                                           required min=\"0.01\" placeholder=\"e.g., 2.5\">
                                    <select class=\"form-select\" name=\"measurement_unit\" style=\"max-width: 120px;\">
                                        <option value=\"acres\" selected>Acres</option>
                                        <option value=\"hectares\">Hectares</option>
                                    </select>
                                </div>
                            </div>
                            <div class=\"col-md-6\">
                                <label for=\"status\" class=\"form-label\">Status *</label>
                                <select class=\"form-select\" id=\"status\" name=\"status\" required>
                                    <option value=\"active\" selected>Active</option>
                                    <option value=\"fallow\">Fallow</option>
                                    <option value=\"prepared\">Prepared</option>
                                    <option value=\"planted\">Planted</option>
                                    <option value=\"growing\">Growing</option>
                                    <option value=\"harvested\">Harvested</option>
                                    <option value=\"abandoned\">Abandoned</option>
                                    <option value=\"converted\">Converted</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Soil Information -->
                    <div class=\"form-section\">
                        <h5><i class=\"fas fa-mountain\"></i> Soil Information</h5>
                        
                        <div class=\"row mb-3\">
                            <div class=\"col-md-4\">
                                <label for=\"soil_type\" class=\"form-label\">Soil Type</label>
                                <input type=\"text\" class=\"form-control\" id=\"soil_type\" name=\"soil_type\" 
                                       placeholder=\"e.g., Loamy, Sandy, Clay\">
                            </div>
                            <div class=\"col-md-4\">
                                <label for=\"soil_texture\" class=\"form-label\">Soil Texture</label>
                                <select class=\"form-select\" id=\"soil_texture\" name=\"soil_texture\">
                                    <option value=\"\">Select texture</option>
                                    <option value=\"sandy\">Sandy</option>
                                    <option value=\"loamy\">Loamy</option>
                                    <option value=\"clay\">Clay</option>
                                    <option value=\"silty\">Silty</option>
                                </select>
                            </div>
                            <div class=\"col-md-4\">
                                <label for=\"soil_ph\" class=\"form-label\">Soil pH</label>
                                <input type=\"number\" step=\"0.1\" class=\"form-control\" id=\"soil_ph\" name=\"soil_ph\" 
                                       min=\"0\" max=\"14\" placeholder=\"e.g., 6.5\">
                            </div>
                        </div>
                        
                        <div class=\"row mb-3\">
                            <div class=\"col-md-4\">
                                <label for=\"slope_percentage\" class=\"form-label\">Slope Percentage</label>
                                <input type=\"number\" step=\"0.1\" class=\"form-control\" id=\"slope_percentage\" name=\"slope_percentage\" 
                                       min=\"0\" max=\"100\" placeholder=\"e.g., 5.0\">
                            </div>
                            <div class=\"col-md-4\">
                                <label for=\"drainage\" class=\"form-label\">Drainage</label>
                                <select class=\"form-select\" id=\"drainage\" name=\"drainage\">
                                    <option value=\"\">Select drainage</option>
                                    <option value=\"good\">Good</option>
                                    <option value=\"moderate\">Moderate</option>
                                    <option value=\"poor\">Poor</option>
                                </select>
                            </div>
                            <div class=\"col-md-4\">
                                <label for=\"terrain\" class=\"form-label\">Terrain</label>
                                <input type=\"text\" class=\"form-control\" id=\"terrain\" name=\"terrain\" 
                                       placeholder=\"e.g., Flat, Hilly, Valley\">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Crop Information -->
                    <div class=\"form-section\">
                        <h5><i class=\"fas fa-seedling\"></i> Crop Information</h5>
                        
                        <div class=\"row mb-3\">
                            <div class=\"col-md-6\">
                                <label for=\"current_crop_type\" class=\"form-label\">Current Crop Type</label>
                                <input type=\"text\" class=\"form-control\" id=\"current_crop_type\" name=\"current_crop_type\" 
                                       placeholder=\"e.g., Cotton, Sesame, Maize\">
                            </div>
                            <div class=\"col-md-6\">
                                <label for=\"crop_variety\" class=\"form-label\">Crop Variety</label>
                                <input type=\"text\" class=\"form-control\" id=\"crop_variety\" name=\"crop_variety\" 
                                       placeholder=\"e.g., BT Cotton, Local Sesame\">
                            </div>
                        </div>
                        
                        <div class=\"row mb-3\">
                            <div class=\"col-md-4\">
                                <label for=\"planting_date\" class=\"form-label\">Planting Date</label>
                                <input type=\"date\" class=\"form-control\" id=\"planting_date\" name=\"planting_date\">
                            </div>
                            <div class=\"col-md-4\">
                                <label for=\"expected_harvest_date\" class=\"form-label\">Expected Harvest Date</label>
                                <input type=\"date\" class=\"form-control\" id=\"expected_harvest_date\" name=\"expected_harvest_date\">
                            </div>
                            <div class=\"col-md-4\">
                                <label for=\"expected_yield\" class=\"form-label\">Expected Yield</label>
                                <div class=\"input-group\">
                                    <input type=\"number\" step=\"0.01\" class=\"form-control\" id=\"expected_yield\" name=\"expected_yield\" 
                                           min=\"0\" placeholder=\"e.g., 500\">
                                    <select class=\"form-select\" name=\"yield_unit\" style=\"max-width: 100px;\">
                                        <option value=\"kg\" selected>kg</option>
                                        <option value=\"tons\">tons</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Irrigation Information -->
                    <div class=\"form-section\">
                        <h5><i class=\"fas fa-tint\"></i> Irrigation Information</h5>
                        
                        <div class=\"row mb-3\">
                            <div class=\"col-md-6\">
                                <label for=\"irrigation_type\" class=\"form-label\">Irrigation Type</label>
                                <select class=\"form-select\" id=\"irrigation_type\" name=\"irrigation_type\">
                                    <option value=\"\">Select type</option>
                                    <option value=\"drip\">Drip Irrigation</option>
                                    <option value=\"sprinkler\">Sprinkler</option>
                                    <option value=\"flood\">Flood Irrigation</option>
                                    <option value=\"rain-fed\">Rain-fed</option>
                                </select>
                            </div>
                            <div class=\"col-md-6\">
                                <label for=\"irrigation_source\" class=\"form-label\">Water Source</label>
                                <input type=\"text\" class=\"form-control\" id=\"irrigation_source\" name=\"irrigation_source\" 
                                       placeholder=\"e.g., Well, River, Dam\">
                            </div>
                        </div>
                        
                        <div class=\"row mb-3\">
                            <div class=\"col-md-6\">
                                <label for=\"irrigation_frequency_days\" class=\"form-label\">Irrigation Frequency (days)</label>
                                <input type=\"number\" class=\"form-control\" id=\"irrigation_frequency_days\" name=\"irrigation_frequency_days\" 
                                       min=\"1\" placeholder=\"e.g., 7\">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Certification & Rotation -->
                    <div class=\"form-section\">
                        <h5><i class=\"fas fa-certificate\"></i> Certification & Rotation</h5>
                        
                        <div class=\"row mb-3\">
                            <div class=\"col-md-6\">
                                <div class=\"form-check\">
                                    <input class=\"form-check-input\" type=\"checkbox\" id=\"is_organic\" name=\"is_organic\" value=\"1\">
                                    <label class=\"form-check-label\" for=\"is_organic\">
                                        Organic Certified Field
                                    </label>
                                </div>
                            </div>
                            <div class=\"col-md-6\">
                                <label for=\"organic_certified_since\" class=\"form-label\">Organic Since</label>
                                <input type=\"date\" class=\"form-control\" id=\"organic_certified_since\" name=\"organic_certified_since\">
                            </div>
                        </div>
                        
                        <div class=\"row mb-3\">
                            <div class=\"col-md-6\">
                                <label for=\"previous_crop\" class=\"form-label\">Previous Crop</label>
                                <input type=\"text\" class=\"form-control\" id=\"previous_crop\" name=\"previous_crop\" 
                                       placeholder=\"e.g., Maize, Beans\">
                            </div>
                            <div class=\"col-md-6\">
                                <label for=\"next_planned_crop\" class=\"form-label\">Next Planned Crop</label>
                                <input type=\"text\" class=\"form-control\" id=\"next_planned_crop\" name=\"next_planned_crop\" 
                                       placeholder=\"e.g., Sesame, Sunflower\">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Additional Information -->
                    <div class=\"form-section\">
                        <h5><i class=\"fas fa-sticky-note\"></i> Additional Information</h5>
                        
                        <div class=\"row mb-3\">
                            <div class=\"col-md-6\">
                                <label for=\"establishment_date\" class=\"form-label\">Field Establishment Date</label>
                                <input type=\"date\" class=\"form-control\" id=\"establishment_date\" name=\"establishment_date\">
                            </div>
                        </div>
                        
                        <div class=\"mb-3\">
                            <label for=\"notes\" class=\"form-label\">Notes</label>
                            <textarea class=\"form-control\" id=\"notes\" name=\"notes\" rows=\"3\" 
                                      placeholder=\"Any additional notes about this field...\"></textarea>
                        </div>
                    </div>
                    
                    <!-- Form Actions -->
                    <div class=\"d-flex justify-content-between\">
                        <a href=\"{{ route('fields.index', $farm) }}\" class=\"btn btn-secondary\">
                            <i class=\"fas fa-times\"></i> Cancel
                        </a>
                        <button type=\"submit\" class=\"btn btn-success\">
                            <i class=\"fas fa-save\"></i> Create Field
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src=\"https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js\"></script>
    
    <script>
        // Set default dates
        document.getElementById('planting_date').valueAsDate = new Date();
        
        // Calculate expected harvest date (default: 90 days from planting)
        const plantingDateInput = document.getElementById('planting_date');
        const harvestDateInput = document.getElementById('expected_harvest_date');
        
        plantingDateInput.addEventListener('change', function() {
            if (this.value) {
                const plantingDate = new Date(this.value);
                const harvestDate = new Date(plantingDate);
                harvestDate.setDate(harvestDate.getDate() + 90); // 90 days default
                harvestDateInput.valueAsDate = harvestDate;
            }
        });
        
        // Enable/disable organic certified since based on checkbox
        const organicCheckbox = document.getElementById('is_organic');
        const organicSinceInput = document.getElementById('organic_certified_since');
        
        organicCheckbox.addEventListener('change', function() {
            organicSinceInput.disabled = !this.checked;
            if (!this.checked) {
                organicSinceInput.value = '';
            }
        });
        
        // Initialize
        organicSinceInput.disabled = !organicCheckbox.checked;
    </script>
</body>
</html>"