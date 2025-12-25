<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $farm->display_name }} - Fields - Remei Farm OS</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f5f7fa; padding: 20px; }
        .navbar { background-color: #27ae60; margin-bottom: 20px; }
        .navbar-brand { color: white !important; font-weight: bold; }
        .card { border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); margin-bottom: 20px; border: none; }
        .card-header { background-color: #27ae60; color: white; border-radius: 10px 10px 0 0 !important; padding: 15px 20px; }
        .status-badge { font-size: 0.75rem; padding: 4px 8px; border-radius: 15px; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="/"><i class="fas fa-seedling"></i> Remei Farm OS</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="{{ route('farms.show', $farm) }}">Back to Farm</a>
                <a class="nav-link" href="{{ route('farms.index') }}">All Farms</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="mb-2"><i class="fas fa-tractor"></i> {{ $farm->display_name }}</h1>
                        <h5 class="text-muted mb-0">Fields Management</h5>
                    </div>
                    <div>
                        <a href="{{ route('fields.create', $farm) }}" class="btn btn-success">
                            <i class="fas fa-plus"></i> Add New Field
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-list"></i> Fields List</h5>
            </div>
            <div class="card-body">
                @if($fields->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Name</th>
                                    <th>Area</th>
                                    <th>Crop Type</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($fields as $field)
                                    <tr>
                                        <td><strong>{{ $field->code }}</strong></td>
                                        <td>{{ $field->name }}</td>
                                        <td>{{ $field->area_with_unit }}</td>
                                        <td>
                                            @if($field->current_crop_type)
                                                <span class="badge bg-info">{{ $field->current_crop_type }}</span>
                                            @else
                                                <span class="text-muted">No crop</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $field->status_color }} status-badge">
                                                {{ $field->status_label }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('fields.show', [$farm, $field]) }}" class="btn btn-sm btn-info" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('fields.edit', [$farm, $field]) }}" class="btn btn-sm btn-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="d-flex justify-content-center">
                        {{ $fields->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-tractor fa-3x text-muted mb-3"></i>
                        <h4>No fields found</h4>
                        <p class="text-muted">This farm doesn't have any fields yet.</p>
                        <a href="{{ route('fields.create', $farm) }}" class="btn btn-success">
                            <i class="fas fa-plus"></i> Add First Field
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
