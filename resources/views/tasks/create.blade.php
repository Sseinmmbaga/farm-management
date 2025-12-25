<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Create Task - Remei Farm OS</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
            padding: 20px;
        }

        .navbar {
            background-color: #27ae60;
            margin-bottom: 20px;
        }

        .navbar-brand {
            color: white !important;
            font-weight: bold;
        }

        .form-container {
            background-color: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            max-width: 900px;
            margin: 0 auto;
        }

        .section-title {
            color: #27ae60;
            border-bottom: 2px solid #27ae60;
            padding-bottom: 10px;
            margin-bottom: 20px;
            font-size: 1.1rem;
        }

        .priority-option {
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .priority-option:hover {
            transform: scale(1.02);
        }

        .priority-option.selected {
            border: 2px solid #27ae60;
        }

        .type-option {
            display: flex;
            align-items: center;
            padding: 8px 12px;
            border-radius: 5px;
            margin-bottom: 5px;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .type-option:hover {
            background-color: #f8f9fa;
        }

        .type-option input {
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="/">
                <i class="fas fa-seedling"></i> Remei Farm OS
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="{{ route('tasks.index') }}">
                    <i class="bi bi-arrow-left"></i> Back to Tasks
                </a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="form-container">
            <h1 class="mb-4"><i class="bi bi-plus-circle"></i> Create New Task</h1>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('tasks.store') }}" id="taskForm">
                @csrf

                <!-- Basic Information -->
                <h5 class="section-title"><i class="bi bi-info-circle"></i> Basic Information</h5>

                <div class="mb-3">
                    <label for="title" class="form-label">Task Title *</label>
                    <input type="text" class="form-control" id="title" name="title"
                           value="{{ old('title') }}" required placeholder="e.g., Prepare field for planting">
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="3"
                              placeholder="Detailed description of the task...">{{ old('description') }}</textarea>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="type" class="form-label">Task Type *</label>
                        <select class="form-select" id="type" name="type" required>
                            <option value="">-- Select Task Type --</option>
                            @foreach($taskTypes as $type)
                                <option value="{{ $type->value }}" {{ old('type') == $type->value ? 'selected' : '' }}>
                                    {{ $type->label() }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Priority *</label>
                        <div class="row g-2">
                            @foreach($taskPriorities as $priority)
                                <div class="col-6 col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="priority"
                                               id="priority_{{ $priority->value }}" value="{{ $priority->value }}"
                                               {{ old('priority', 'medium') == $priority->value ? 'checked' : '' }} required>
                                        <label class="form-check-label" for="priority_{{ $priority->value }}">
                                            <span class="badge bg-{{ $priority->color() }}">
                                                <i class="{{ $priority->icon() }}"></i> {{ $priority->label() }}
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Location -->
                <h5 class="section-title mt-4"><i class="bi bi-geo-alt"></i> Location</h5>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="farm_id" class="form-label">Farm</label>
                        <select class="form-select" id="farm_id" name="farm_id" onchange="loadFields()">
                            <option value="">-- Select Farm --</option>
                            @foreach($farms as $farm)
                                <option value="{{ $farm->id }}"
                                        {{ old('farm_id', $selectedFarm?->id) == $farm->id ? 'selected' : '' }}>
                                    {{ $farm->name }} ({{ $farm->farmer->full_name ?? 'N/A' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="field_id" class="form-label">Field</label>
                        <select class="form-select" id="field_id" name="field_id">
                            <option value="">-- Select Field --</option>
                            @foreach($fields as $field)
                                <option value="{{ $field->id }}"
                                        {{ old('field_id', $selectedField?->id) == $field->id ? 'selected' : '' }}>
                                    {{ $field->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="farmer_id" class="form-label">Farmer</label>
                        <select class="form-select" id="farmer_id" name="farmer_id">
                            <option value="">-- Select Farmer --</option>
                            @foreach($farmers as $farmer)
                                <option value="{{ $farmer->id }}"
                                        {{ old('farmer_id', $selectedFarmer?->id) == $farmer->id ? 'selected' : '' }}>
                                    {{ $farmer->full_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="season_id" class="form-label">Season</label>
                    <select class="form-select" id="season_id" name="season_id">
                        <option value="">-- Select Season --</option>
                        @foreach($seasons as $season)
                            <option value="{{ $season->id }}" {{ old('season_id') == $season->id ? 'selected' : '' }}>
                                {{ $season->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Schedule -->
                <h5 class="section-title mt-4"><i class="bi bi-calendar"></i> Schedule</h5>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="planned_start_date" class="form-label">Planned Start Date</label>
                        <input type="date" class="form-control" id="planned_start_date" name="planned_start_date"
                               value="{{ old('planned_start_date') }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="planned_end_date" class="form-label">Planned End Date</label>
                        <input type="date" class="form-control" id="planned_end_date" name="planned_end_date"
                               value="{{ old('planned_end_date') }}">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="estimated_hours" class="form-label">Estimated Hours</label>
                        <input type="number" step="0.5" min="0" class="form-control" id="estimated_hours"
                               name="estimated_hours" value="{{ old('estimated_hours') }}" placeholder="e.g., 8">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="estimated_cost" class="form-label">Estimated Cost (TZS)</label>
                        <input type="number" step="100" min="0" class="form-control" id="estimated_cost"
                               name="estimated_cost" value="{{ old('estimated_cost') }}" placeholder="e.g., 50000">
                    </div>
                </div>

                <!-- Assignment -->
                <h5 class="section-title mt-4"><i class="bi bi-people"></i> Assignment</h5>

                <div class="mb-3">
                    <label for="assignees" class="form-label">Assign To</label>
                    <select class="form-select" id="assignees" name="assignees[]" multiple>
                        @foreach($assignableUsers as $user)
                            <option value="{{ $user->id }}"
                                    {{ in_array($user->id, old('assignees', [])) ? 'selected' : '' }}>
                                {{ $user->name }} ({{ ucfirst(str_replace('_', ' ', $user->role)) }})
                            </option>
                        @endforeach
                    </select>
                    <small class="text-muted">Hold Ctrl/Cmd to select multiple users</small>
                </div>

                <!-- Additional Information -->
                <h5 class="section-title mt-4"><i class="bi bi-gear"></i> Additional Information</h5>

                <div class="mb-3">
                    <label for="notes" class="form-label">Notes</label>
                    <textarea class="form-control" id="notes" name="notes" rows="2"
                              placeholder="Any additional notes...">{{ old('notes') }}</textarea>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="equipment_required" class="form-label">Equipment Required</label>
                        <input type="text" class="form-control" id="equipment_required" name="equipment_required"
                               value="{{ old('equipment_required') }}"
                               placeholder="e.g., Tractor, Plough (comma separated)">
                        <small class="text-muted">Separate items with commas</small>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="materials_required" class="form-label">Materials Required</label>
                        <input type="text" class="form-control" id="materials_required" name="materials_required"
                               value="{{ old('materials_required') }}"
                               placeholder="e.g., Seeds, Fertilizer (comma separated)">
                        <small class="text-muted">Separate items with commas</small>
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('tasks.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle"></i> Create Task
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery (needed for Select2) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize Select2 for assignees
            $('#assignees').select2({
                theme: 'bootstrap-5',
                placeholder: 'Select assignees...',
                allowClear: true
            });

            // Initialize Select2 for other selects
            $('#farm_id, #farmer_id, #season_id').select2({
                theme: 'bootstrap-5',
                allowClear: true
            });
        });

        // Load fields for selected farm
        function loadFields() {
            const farmId = document.getElementById('farm_id').value;
            const fieldSelect = document.getElementById('field_id');

            if (!farmId) {
                fieldSelect.innerHTML = '<option value="">-- Select Field --</option>';
                return;
            }

            fieldSelect.innerHTML = '<option value="">Loading...</option>';
            fieldSelect.disabled = true;

            fetch(`/tasks/fields/${farmId}`)
                .then(response => response.json())
                .then(data => {
                    let options = '<option value="">-- Select Field --</option>';
                    data.forEach(field => {
                        options += `<option value="${field.id}">${field.name} (${field.code})</option>`;
                    });
                    fieldSelect.innerHTML = options;
                    fieldSelect.disabled = false;
                })
                .catch(error => {
                    console.error('Error loading fields:', error);
                    fieldSelect.innerHTML = '<option value="">Error loading fields</option>';
                    fieldSelect.disabled = false;
                });
        }

        // Date validation
        document.getElementById('planned_start_date').addEventListener('change', function() {
            const endDate = document.getElementById('planned_end_date');
            if (endDate.value && endDate.value < this.value) {
                endDate.value = this.value;
            }
            endDate.min = this.value;
        });

        // Handle equipment/materials as arrays
        document.getElementById('taskForm').addEventListener('submit', function(e) {
            const equipmentInput = document.getElementById('equipment_required');
            const materialsInput = document.getElementById('materials_required');

            // Convert comma-separated to hidden array inputs
            if (equipmentInput.value) {
                const items = equipmentInput.value.split(',').map(i => i.trim()).filter(i => i);
                equipmentInput.name = 'equipment_required_text';
                items.forEach((item, index) => {
                    const hidden = document.createElement('input');
                    hidden.type = 'hidden';
                    hidden.name = 'equipment_required[]';
                    hidden.value = item;
                    this.appendChild(hidden);
                });
            }

            if (materialsInput.value) {
                const items = materialsInput.value.split(',').map(i => i.trim()).filter(i => i);
                materialsInput.name = 'materials_required_text';
                items.forEach((item, index) => {
                    const hidden = document.createElement('input');
                    hidden.type = 'hidden';
                    hidden.name = 'materials_required[]';
                    hidden.value = item;
                    this.appendChild(hidden);
                });
            }
        });

        // Initialize fields if farm is pre-selected
        document.addEventListener('DOMContentLoaded', function() {
            const farmId = document.getElementById('farm_id').value;
            if (farmId) {
                // Fields should already be loaded from controller
            }
        });
    </script>
</body>
</html>
