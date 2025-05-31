<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Management - PUMA Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #ED1B2E;
            --secondary-color: #000000;
            --light-color: #f8f9fa;
            --dark-color: #212529;
            --success-color: #28a745;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --info-color: #17a2b8;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }

        .app-container {
            display: flex;
            min-height: 100vh;
            width: 100%;
        }

        /* Sidebar Styles */
        .sidebar {
            width: 250px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background: linear-gradient(to bottom, #1a1a2e, #16213e);
            color: #fff;
            padding: 20px;
            overflow-y: auto;
            z-index: 1000;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .logo-area {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding-bottom: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .logo-img {
            width: 80px;
            height: 80px;
            object-fit: contain;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            border-radius: 8px;
            margin-bottom: 8px;
            transition: all 0.3s ease;
        }

        .nav-link i {
            width: 20px;
            margin-right: 10px;
        }

        .nav-link:hover, .nav-link.active {
            background-color: rgba(255, 255, 255, 0.1);
            color: #fff;
        }

        .nav-link.active {
            background-color: #e50010; /* PUMA red */
            color: white;
            font-weight: 500;
        }


        .main-content {
            flex: 1;
            margin-left: 250px;
            padding: 20px;
            background-color: #f5f5f5;
        }

        .content-header {
            padding-bottom: 20px;
            border-bottom: 1px solid #dee2e6;
            margin-bottom: 20px;
        }

        .page-title {
            margin: 0;
            font-weight: 600;
            color: var(--dark-color);
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
        }

        .card-header {
            background-color: white;
            border-bottom: 1px solid #edf2f7;
            padding: 15px 20px;
            font-weight: 600;
        }

        .card-body {
            padding: 20px;
        }

        .search-container {
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .search-input {
            padding: 10px 15px;
            border: 1px solid #edf2f7;
            border-radius: 8px;
            width: 300px;
            font-size: 14px;
            transition: all 0.3s;
        }

        .search-input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(237, 27, 46, 0.1);
            outline: none;
        }

        .btn {
            padding: 10px 15px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: #c01424;
            border-color: #c01424;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .table th {
            background-color: #f8f9fa;
            color: #495057;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 0.5px;
            padding: 12px 15px;
            border-bottom: 1px solid #edf2f7;
        }

        .table td {
            padding: 15px;
            vertical-align: middle;
            border-bottom: 1px solid #edf2f7;
            font-size: 14px;
        }

        .table tr:hover {
            background-color: #f8f9fa;
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            display: inline-block;
        }

        .status-active {
            background-color: rgba(46, 204, 113, 0.1);
            color: #2ecc71;
        }

        .status-inactive {
            background-color: rgba(231, 76, 60, 0.1);
            color: #e74c3c;
        }

        .action-btn {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-right: 5px;
            border: none;
            transition: all 0.2s;
            background-color: #f8f9fa;
        }

        .btn-view {
            color: var(--info-color);
        }

        .btn-view:hover {
            background-color: var(--info-color);
            color: white;
        }

        .btn-edit {
            color: var(--warning-color);
        }

        .btn-edit:hover {
            background-color: var(--warning-color);
            color: white;
        }

        .btn-delete {
            color: var(--danger-color);
        }

        .btn-delete:hover {
            background-color: var(--danger-color);
            color: white;
        }

        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .page-item {
            margin: 0 3px;
        }

        .page-link {
            padding: 8px 12px;
            border-radius: 8px;
            border: 1px solid #edf2f7;
            color: #495057;
            transition: all 0.3s;
        }

        .page-link:hover {
            background-color: #f8f9fa;
            border-color: #edf2f7;
        }

        .page-item.active .page-link {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
        }

        .modal-content {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .modal-header {
            background: linear-gradient(45deg, var(--primary-color), #ff4757);
            color: white;
            border-radius: 15px 15px 0 0;
            padding: 20px;
        }

        .modal-header .btn-close {
            background-color: rgba(255,255,255,0.5);
            border-radius: 50%;
            padding: 8px;
        }

        .modal-body {
            padding: 30px;
        }

        .form-label {
            font-weight: 500;
            color: #2d3436;
            margin-bottom: 8px;
        }

        .form-control {
            border: 2px solid #edf2f7;
            border-radius: 10px;
            padding: 12px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(237, 27, 46, 0.1);
        }

        .alert {
            border-radius: 10px;
            padding: 15px 20px;
            margin-bottom: 20px;
            border: none;
        }

        .alert-success {
            background-color: rgba(46, 204, 113, 0.1);
            color: #2ecc71;
        }

        .alert-danger {
            background-color: rgba(231, 76, 60, 0.1);
            color: #e74c3c;
        }

        .customer-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 10px;
        }

        .customer-info {
            display: flex;
            align-items: center;
        }

        .customer-name {
            font-weight: 500;
            margin-bottom: 2px;
        }

        .customer-email {
            font-size: 12px;
            color: #6c757d;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 70px;
                padding: 15px 0;
            }

            .sidebar .logo-area {
                padding: 0 10px;
            }

            .sidebar .nav-link span {
                display: none;
            }

            .sidebar .nav-link i {
                margin-right: 0;
                font-size: 1.3rem;
            }

            .main-content {
                margin-left: 70px;
            }

            .search-container {
                flex-direction: column;
                align-items: flex-start;
            }

            .search-input {
                width: 100%;
                margin-bottom: 10px;
            }

            .table-responsive {
                overflow-x: auto;
            }
        }
    </style>
</head>
<body>
    <div class="app-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="logo-area">
                <img src="{{ asset('images/logo.png') }}" alt="PUMA" class="logo-img">
                <h5 class="mt-3 text-white fw-bold">Admin Dashboard</h5>
            </div>

            <nav class="mt-4">
                <a href="{{ url('/admin') }}" class="nav-link">
                    <i class="fas fa-box-open"></i>
                    <span>Products</span>
                </a>
                <a href="{{ url('/admin/orders') }}" class="nav-link">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Orders</span>
                </a>
                <a href="{{ url('/admin/customers') }}" class="nav-link active">
                    <i class="fas fa-users"></i>
                    <span>Customers</span>
                </a>
                <a href="{{ url('/admin/banners') }}" class="nav-link">
                    <i class="fas fa-image"></i>
                    <span>Banners</span>
                </a>
                <a href="{{ url('/logout') }}" class="nav-link mt-5">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </nav>
        </aside>
        <!-- Main Content -->
        <main class="main-content">
            <div class="content-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h4 class="page-title">Customer Management</h4>
                    </div>
                    <div class="col-auto">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCustomerModal">
                            <i class="fas fa-plus me-2"></i> Add New Customer
                        </button>
                    </div>
                </div>
            </div>

            @if (isset($message))
            <div class="alert alert-{{ $messageType === 'success' ? 'success' : 'danger' }} alert-dismissible fade show" role="alert">
                {{ $message }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <div class="card">
                <div class="card-body">
                    <div class="search-container">
                        <form action="" method="GET" class="d-flex">
                            <input type="text" name="search" class="search-input" placeholder="Search customers..." value="{{ $search ?? '' }}">
                            <button type="submit" class="btn btn-primary ms-2">Search</button>
                            @if (isset($search) && !empty($search))
                                <a href="{{ url('/admin/customers') }}" class="btn btn-outline-secondary ms-2">Clear</a>
                            @endif
                        </form>
                    </div>

                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Customer</th>
                                    <th>Email</th>
                                    <th>Registered</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (isset($customers) && count($customers) > 0)
                                    @foreach ($customers as $customer)
                                        <tr>
                                            <td>#{{ $customer['id'] }}</td>
                                            <td>
                                                <div class="customer-info">
                                                    <img src="{{ asset('images/user-default.png') }}" alt="Avatar" class="customer-avatar">
                                                    <div>
                                                        <div class="customer-name">{{ $customer['name'] }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $customer['email'] }}</td>
                                            <td>{{ isset($customer['created_at']) ? date('d M Y', strtotime($customer['created_at'])) : date('d M Y') }}</td>
                                            <td>
                                                <span class="status-badge status-active">Active</span>
                                            </td>
                                            <td>
                                                <button type="button" class="action-btn btn-view" data-bs-toggle="modal" data-bs-target="#viewCustomerModal" data-id="{{ $customer['id'] }}">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button type="button" class="action-btn btn-edit" data-bs-toggle="modal" data-bs-target="#editCustomerModal" data-id="{{ $customer['id'] }}">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <a href="{{ url('/admin/customers?hapus=' . $customer['id']) }}" class="action-btn btn-delete" onclick="return confirm('Are you sure you want to delete this customer?')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="6" class="text-center py-4">No customers found</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if (isset($totalPages) && $totalPages > 1)
                    <nav aria-label="Page navigation">
                        <ul class="pagination">
                            @if ($page > 1)
                                <li class="page-item">
                                    <a class="page-link" href="{{ url('/admin/customers?page=' . ($page - 1) . (isset($search) && !empty($search) ? '&search=' . urlencode($search) : '')) }}" aria-label="Previous">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                </li>
                            @endif

                            @php
                            $startPage = max(1, $page - 2);
                            $endPage = min($totalPages, $startPage + 4);
                            @endphp

                            @for ($i = $startPage; $i <= $endPage; $i++)
                                <li class="page-item {{ $i === $page ? 'active' : '' }}">
                                    <a class="page-link" href="{{ url('/admin/customers?page=' . $i . (isset($search) && !empty($search) ? '&search=' . urlencode($search) : '')) }}">
                                        {{ $i }}
                                    </a>
                                </li>
                            @endfor

                            @if ($page < $totalPages)
                                <li class="page-item">
                                    <a class="page-link" href="{{ url('/admin/customers?page=' . ($page + 1) . (isset($search) && !empty($search) ? '&search=' . urlencode($search) : '')) }}" aria-label="Next">
                                        <span aria-hidden="true">&raquo;</span>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </nav>
                    @endif
                </div>
            </div>
        </main>
    </div>

    <!-- View Customer Modal -->
    <div class="modal fade" id="viewCustomerModal" tabindex="-1" aria-labelledby="viewCustomerModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewCustomerModalLabel">Customer Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="customerDetailContent">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Customer Modal -->
    <div class="modal fade" id="editCustomerModal" tabindex="-1" aria-labelledby="editCustomerModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editCustomerModalLabel">Edit Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="updateCustomerForm" action="{{ url('/admin/customers') }}" method="POST">
                        <input type="hidden" name="customer_id" id="editCustomerId">
                        <input type="hidden" name="update_customer" value="1">

                        <div class="mb-3">
                            <label for="editCustomerName" class="form-label">Name</label>
                            <input type="text" class="form-control" id="editCustomerName" name="name" required>
                        </div>

                        <div class="mb-3">
                            <label for="editCustomerEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="editCustomerEmail" name="email" required>
                        </div>

                        <div class="mb-3">
                            <label for="editCustomerStatus" class="form-label">Status</label>
                            <select class="form-select" id="editCustomerStatus" name="status">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>

                        <div class="text-end">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Update Customer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Customer Modal -->
    <div class="modal fade" id="addCustomerModal" tabindex="-1" aria-labelledby="addCustomerModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCustomerModalLabel">Add New Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addCustomerForm" action="{{ url('/admin/customers') }}" method="POST">
                        <input type="hidden" name="add_customer" value="1">

                        <div class="mb-3">
                            <label for="newCustomerName" class="form-label">Name</label>
                            <input type="text" class="form-control" id="newCustomerName" name="name" required>
                        </div>

                        <div class="mb-3">
                            <label for="newCustomerEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="newCustomerEmail" name="email" required>
                        </div>

                        <div class="mb-3">
                            <label for="newCustomerPassword" class="form-label">Password</label>
                            <input type="password" class="form-control" id="newCustomerPassword" name="password" required>
                        </div>

                        <div class="text-end">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Add Customer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        // View Customer Details Modal
        document.querySelectorAll('.btn-view').forEach(button => {
            button.addEventListener('click', function() {
                const customerId = this.getAttribute('data-id');
                const detailContent = document.getElementById('customerDetailContent');

                // Show loading spinner
                detailContent.innerHTML = `
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                `;

                // Fetch customer details using AJAX
                fetch(`{{ url('/api/get_customer_details.php') }}?id=${customerId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            detailContent.innerHTML = `<div class="alert alert-danger">${data.error}</div>`;
                            return;
                        }

                        // Format customer details
                        const customer = data.customer;
                        const createdDate = new Date(customer.created_at).toLocaleDateString('id-ID', {day: 'numeric', month: 'long', year: 'numeric'});

                        // Create detail content
                        detailContent.innerHTML = `
                            <div class="text-center mb-4">
                                <img src="{{ asset('images/user-default.png') }}" alt="Customer Avatar" class="rounded-circle" style="width: 100px; height: 100px; object-fit: cover;">
                                <h4 class="mt-3">${customer.name}</h4>
                                <p class="text-muted">${customer.email}</p>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Customer ID:</strong> #${customer.id}</p>
                                    <p><strong>Registered:</strong> ${createdDate}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Status:</strong> <span class="status-badge status-active">Active</span></p>
                                </div>
                            </div>
                        `;
                    })
                    .catch(error => {
                        detailContent.innerHTML = `<div class="alert alert-danger">Error fetching customer details: ${error.message}</div>`;
                    });
            });
        });

        // Edit Customer Modal
        document.querySelectorAll('.btn-edit').forEach(button => {
            button.addEventListener('click', function() {
                const customerId = this.getAttribute('data-id');
                document.getElementById('editCustomerId').value = customerId;

                // Fetch current customer data
                fetch(`{{ url('/api/get_customer_details.php') }}?id=${customerId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.customer) {
                            document.getElementById('editCustomerName').value = data.customer.name;
                            document.getElementById('editCustomerEmail').value = data.customer.email;
                            document.getElementById('editCustomerStatus').value = data.customer.status || "1";
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching customer data:', error);
                    });
            });
        });
    </script>
</body>
</html>
