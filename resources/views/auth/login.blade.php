<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Remei Farm Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #27ae60;
            --accent-color: #3498db;
            --light-bg: #ecf0f1;
            --text-dark: #2c3e50;
            --text-light: #7f8c8d;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .container {
            padding: 0 !important;
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }

        .card-body {
            background: #ffffff;
            padding: 2.5rem 2rem !important;
        }

        .text-center h1 {
            color: var(--primary-color);
            font-weight: 700;
            margin-bottom: 0.5rem;
            font-size: 1.8rem;
        }

        .text-center p {
            color: var(--text-light);
            font-size: 0.95rem;
            margin: 0;
        }

        .form-label {
            color: var(--primary-color);
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .form-control {
            border: 1px solid #e0e6ed;
            border-radius: 8px;
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }

        .form-control::placeholder {
            color: #bdc3c7;
        }

        .form-check {
            margin-bottom: 1.5rem;
        }

        .form-check-input {
            border: 1px solid #ddd;
            border-radius: 4px;
            width: 1.1em;
            height: 1.1em;
            cursor: pointer;
        }

        .form-check-input:checked {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }

        .form-check-label {
            color: var(--text-dark);
            font-size: 0.9rem;
            cursor: pointer;
            margin-left: 0.5rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--secondary-color), #229954);
            border: none;
            padding: 0.75rem 1rem;
            font-weight: 600;
            font-size: 0.95rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #229954, #1e8449);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(39, 174, 96, 0.3);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .btn-primary:disabled {
            background: linear-gradient(135deg, #bdc3c7, #95a5a6);
            cursor: not-allowed;
            transform: none;
        }

        .mb-3 {
            margin-bottom: 1.2rem !important;
        }

        .text-center.mt-3 {
            padding-top: 1rem;
            border-top: 1px solid #ecf0f1;
            margin-top: 1.5rem !important;
        }

        .card-link {
            color: var(--accent-color);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .card-link:hover {
            color: var(--secondary-color);
            text-decoration: underline;
        }

        .mx-2 {
            color: #bdc3c7;
        }

        #alertContainer {
            margin-bottom: 1.5rem;
        }

        .alert {
            border: none;
            border-radius: 8px;
            border-left: 4px solid;
            font-size: 0.9rem;
        }

        .alert-warning {
            border-left-color: #f39c12;
            background-color: #fef5e7;
            color: #7d6608;
        }

        .alert-success {
            border-left-color: var(--secondary-color);
            background-color: #d5f4e6;
            color: #145a32;
        }

        .alert-danger {
            border-left-color: #e74c3c;
            background-color: #fadbd8;
            color: #78281f;
        }

        .spinner-border-sm {
            margin-right: 0.5rem;
        }

        .btn-secondary {
            background-color: #95a5a6;
            border: none;
            padding: 0.5rem 0.8rem;
            font-weight: 500;
            font-size: 0.85rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            background-color: #7f8c8d;
            transform: translateY(-1px);
        }

        .credentials-table {
            font-size: 0.85rem;
        }

        .credentials-table th {
            background-color: var(--primary-color);
            color: white;
            font-weight: 600;
            border-radius: 8px 8px 0 0;
        }

        .credentials-table td {
            padding: 0.75rem;
            border-bottom: 1px solid #ecf0f1;
        }

        .credentials-table tr:last-child td {
            border-bottom: none;
        }

        .modal-content {
            border: none;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
        }

        .modal-header {
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 12px 12px 0 0;
        }

        .btn-close-white {
            filter: brightness(0) invert(1);
        }

        @media (max-width: 576px) {
            .card-body {
                padding: 2rem 1.5rem !important;
            }

            .text-center h1 {
                font-size: 1.5rem;
            }

            .col-md-6 {
                padding: 1rem !important;
            }
        }
    </style>
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <h1 class="h3">Welcome Back</h1>
                            <p class="text-muted">Sign in to your account</p>
                        </div>
                        
                        <!-- Display validation errors -->
                        @if ($errors->any())
                            <div id="alertContainer">
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @endif

                        @if (session('status'))
                            <div id="alertContainer">
                                <div class="alert alert-success">
                                    {{ session('status') }}
                                </div>
                            </div>
                        @endif
                        
                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="email" class="form-label">Email address</label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" placeholder="name@example.com" required autofocus>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                <label class="form-check-label" for="remember">Remember me</label>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Sign In</button>
                            </div>
                        </form>
                        <div class="text-center mt-3">
                            <!-- Password reset and registration features are not yet implemented -->
                            {{-- <a href="{{ route('password.request') }}" class="card-link">Forgot password?</a>
                            <span class="mx-2">|</span>
                            <a href="{{ route('register') }}" class="card-link">Create account</a> --}}
                            <span class="text-muted">Use test credentials from the button below</span>
                        </div>
                        <div class="text-center mt-3">
                            <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#credentialsModal">Show Test Credentials</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Credentials Modal -->
    <div class="modal fade" id="credentialsModal" tabindex="-1" aria-labelledby="credentialsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="credentialsModalLabel">Test Login Credentials</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted mb-3"><strong>Note:</strong> These credentials are for testing purposes only.</p>
                    <div class="table-responsive">
                        <table class="table table-striped credentials-table">
                            <thead>
                                <tr>
                                    <th>Role</th>
                                    <th>Email</th>
                                    <th>Password</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Admin</strong></td>
                                    <td>admin@remei.com</td>
                                    <td>password123</td>
                                </tr>
                                <tr>
                                    <td><strong>Accountant</strong></td>
                                    <td>accountant@remei.com</td>
                                    <td>password123</td>
                                </tr>
                                <tr>
                                    <td><strong>Production Manager</strong></td>
                                    <td>production@remei.com</td>
                                    <td>password123</td>
                                </tr>
                                <tr>
                                    <td><strong>Supervisor</strong></td>
                                    <td>supervisor@remei.com</td>
                                    <td>password123</td>
                                </tr>
                                <tr>
                                    <td><strong>Field Extension Officer</strong></td>
                                    <td>extension@remei.com</td>
                                    <td>password123</td>
                                </tr>
                                <tr>
                                    <td><strong>ICS Inspector</strong></td>
                                    <td>ics@remei.com</td>
                                    <td>password123</td>
                                </tr>
                                <tr>
                                    <td><strong>Training Coordinator</strong></td>
                                    <td>training@remei.com</td>
                                    <td>password123</td>
                                </tr>
                                <tr>
                                    <td><strong>Stock Manager</strong></td>
                                    <td>stock@remei.com</td>
                                    <td>password123</td>
                                </tr>
                                <tr>
                                    <td><strong>Farmer</strong></td>
                                    <td>farmer@remei.com</td>
                                    <td>password123</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Simple client-side validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            
            if (!email || !password) {
                e.preventDefault();
                alert('Please fill in all required fields.');
                return false;
            }
            
            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Signing in...';
        });
    </script>
</body>
</html>
