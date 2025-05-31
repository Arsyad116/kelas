<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Management - PUMA Admin Dashboard</title>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders Management - PUMA Admin Dashboard</title>
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

        .status-new {
            background-color: rgba(52, 152, 219, 0.1);
            color: #3498db;
        }

        .status-processing {
            background-color: rgba(243, 156, 18, 0.1);
            color: #f39c12;
        }

        .status-shipped {
            background-color: rgba(155, 89, 182, 0.1);
            color: #9b59b6;
        }

        .status-completed {
            background-color: rgba(46, 204, 113, 0.1);
            color: #2ecc71;
        }

        .status-cancelled {
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

        .order-detail-row {
            display: flex;
            margin-bottom: 10px;
        }

        .order-detail-label {
            font-weight: 500;
            width: 150px;
        }

        .order-items-table {
            width: 100%;
            margin-top: 15px;
            border-collapse: collapse;
        }

        .order-items-table th, .order-items-table td {
            padding: 10px;
            border-bottom: 1px solid #edf2f7;
        }

        .order-items-table th {
            text-align: left;
            font-weight: 500;
            color: #6c757d;
            font-size: 12px;
        }

        /* Product image styles */
        .product-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 5px;
            margin-right: 5px;
            border: 1px solid #eee;
        }

        .product-image-container {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
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
                <a href="{{ url('/admin/orders') }}" class="nav-link active">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Orders</span>
                </a>
                <a href="{{ url('/admin/customers') }}" class="nav-link">
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
                        <h4 class="page-title">Order Management</h4>
                    </div>
                    <div class="col-auto">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addOrderModal">
                            <i class="fas fa-plus me-2"></i> Add New Order
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
                        <form action="{{ url('/admin/orders') }}" method="GET" class="d-flex">
                            <input type="text" name="search" class="search-input" placeholder="Search orders..." value="{{ $search ?? '' }}">
                            <button type="submit" class="btn btn-primary ms-2">Search</button>
                            @if (isset($search) && !empty($search))
                                <a href="{{ url('/admin/orders') }}" class="btn btn-outline-secondary ms-2">Clear</a>
                            @endif
                        </form>
                    </div>

                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Date</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Nama Barang</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (isset($result) && count($result) > 0)
                                    @foreach ($result as $row)
                                        <tr>
                                            <td>#{{ $row['id'] }}</td>
                                            <td>{{ date('d M Y', strtotime($row['tanggal'])) }}</td>
                                            <td>Rp {{ number_format($row['total_harga'], 0, ',', '.') }}</td>
                                            <td>
                                                @php
                                                $statusClass = '';
                                                switch($row['status']) {
                                                    case 'Baru':
                                                        $statusClass = 'status-new';
                                                        break;
                                                    case 'Diproses':
                                                        $statusClass = 'status-processing';
                                                        break;
                                                    case 'Dikirim':
                                                        $statusClass = 'status-shipped';
                                                        break;
                                                    case 'Selesai':
                                                        $statusClass = 'status-completed';
                                                        break;
                                                    case 'Dibatalkan':
                                                        $statusClass = 'status-cancelled';
                                                        break;
                                                }
                                                @endphp
                                                <span class="status-badge {{ $statusClass }}">
                                                    {{ $row['status'] }}
                                                </span>
                                            </td>
                                            <td>{{ htmlspecialchars((string)$row['nama_barang']) }}</td>
                                            <td>
                                                <button type="button" class="action-btn btn-view" data-bs-toggle="modal" data-bs-target="#viewOrderModal" data-id="{{ $row['id'] }}">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button type="button" class="action-btn btn-edit" data-bs-toggle="modal" data-bs-target="#editOrderModal" data-id="{{ $row['id'] }}">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <a href="{{ url('/admin/orders?hapus=' . $row['id']) }}" class="action-btn btn-delete" onclick="return confirm('Are you sure you want to delete this order?')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="7" class="text-center py-4">No orders found</td>
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
                                    <a class="page-link" href="{{ url('/admin/orders?page=' . ($page - 1) . (isset($search) && !empty($search) ? '&search=' . urlencode($search) : '')) }}" aria-label="Previous">
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
                                    <a class="page-link" href="{{ url('/admin/orders?page=' . $i . (isset($search) && !empty($search) ? '&search=' . urlencode($search) : '')) }}">
                                        {{ $i }}
                                    </a>
                                </li>
                            @endfor

                            @if ($page < $totalPages)
                                <li class="page-item">
                                    <a class="page-link" href="{{ url('/admin/orders?page=' . ($page + 1) . (isset($search) && !empty($search) ? '&search=' . urlencode($search) : '')) }}" aria-label="Next">
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

    <!-- View Order Modal -->
    <div class="modal fade" id="viewOrderModal" tabindex="-1" aria-labelledby="viewOrderModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewOrderModalLabel">Order Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="orderDetailContent">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Order Modal -->
    <div class="modal fade" id="editOrderModal" tabindex="-1" aria-labelledby="editOrderModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editOrderModalLabel">Update Order Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="updateOrderForm" action="/api/update_order_status.php" method="POST">
                        <input type="hidden" name="order_id" id="editOrderId">
                        <input type="hidden" name="update_status" value="1">

                        <div class="mb-3">
                            <label for="orderStatus" class="form-label">Order Status</label>
                            <select class="form-select" id="orderStatus" name="status" required>
                                <option value="Baru">Baru</option>
                                <option value="Diproses">Diproses</option>
                                <option value="Dikirim">Dikirim</option>
                                <option value="Selesai">Selesai</option>
                                <option value="Dibatalkan">Dibatalkan</option>
                            </select>
                        </div>

                        <div class="text-end">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Update Status</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Order Modal -->
    <div class="modal fade" id="addOrderModal" tabindex="-1" aria-labelledby="addOrderModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addOrderModalLabel">Add New Order</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addOrderForm" action="api/add_order.php" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="newOrderDate" class="form-label">Order Date</label>
                            <input type="date" class="form-control" id="newOrderDate" name="tanggal" required value="<?php echo date('Y-m-d'); ?>">
                        </div>

                        <div class="mb-3">
                            <label for="newOrderTotal" class="form-label">Order Total (Rp)</label>
                            <input type="number" class="form-control" id="newOrderTotal" name="total" required>
                        </div>

                        <div class="mb-3">
                            <label for="newOrderStatus" class="form-label">Order Status</label>
                            <select class="form-select" id="newOrderStatus" name="status" required>
                                <option value="Baru">Baru</option>
                                <option value="Diproses">Diproses</option>
                                <option value="Dikirim">Dikirim</option>
                                <option value="Selesai">Selesai</option>
                                <option value="Dibatalkan">Dibatalkan</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="newOrderImage" class="form-label">Order Image</label>
                            <input type="file" class="form-control" id="newOrderImage" name="order_gambar">
                        </div>

                        <div class="mb-3">
                            <label for="productSelection" class="form-label">Select Products</label>
                            <select class="form-select" id="productSelection" name="products[]" multiple>
                                @php
                                $products = DB::table('produk')->orderBy('nama')->get();
                                @endphp

                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" data-price="{{ $product->harga }}">
                                        {{ htmlspecialchars($product->nama) }} - Rp {{ number_format($product->harga, 0, ',', '.') }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Hold Ctrl (or Cmd) to select multiple products</small>
                        </div>

                        <div class="text-end">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Add Order</button>
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
        // View Order Details Modal
        document.querySelectorAll('.btn-view').forEach(button => {
            button.addEventListener('click', function() {
                const orderId = this.getAttribute('data-id');
                const detailContent = document.getElementById('orderDetailContent');

                // Show loading spinner
                detailContent.innerHTML = `
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                `;

                // Fetch order details using AJAX
                fetch(`/api/get_order_details.php?id=${orderId}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! Status: ${response.status}`);
                        }
                        return response.text().then(text => {
                            try {
                                return JSON.parse(text);
                            } catch (e) {
                                console.error('Invalid JSON:', text);
                                throw new Error(`Invalid JSON response: ${text.substring(0, 100)}...`);
                            }
                        });
                    })
                    .then(data => {
                        if (data.error) {
                            detailContent.innerHTML = `<div class="alert alert-danger">${data.error}</div>`;
                            return;
                        }

                        // Format order details
                        let itemsHtml = '';
                        if (data.items && data.items.length > 0) {
                            itemsHtml = `
                                <h6 class="mt-4 mb-3">Order Items</h6>
                                <table class="order-items-table">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Price</th>
                                            <th>Quantity</th>
                                            <th>Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                            `;

                            data.items.forEach(item => {
                                itemsHtml += `
                                    <tr>
                                        <td>${item.product_name}</td>
                                        <td>Rp ${new Intl.NumberFormat('id-ID').format(item.harga)}</td>
                                        <td>${item.quantity}</td>
                                        <td>Rp ${new Intl.NumberFormat('id-ID').format(item.harga * item.quantity)}</td>
                                    </tr>
                                `;
                            });

                            itemsHtml += `
                                    </tbody>
                                </table>
                            `;
                        }

                        // Create detail content
                        let orderImage = '';
                        if (data.order.order_gambar) {
                            orderImage = `
                                <div class="text-center mb-4">
                                    <img src="{{ asset('images') }}/${data.order.order_gambar}" alt="Order Image" style="max-width: 100%; max-height: 300px; border-radius: 10px;">
                                </div>
                            `;
                        }

                        detailContent.innerHTML = `
                            ${orderImage}
                            <div class="order-detail-row">
                                <div class="order-detail-label">Order ID:</div>
                                <div>#${data.order.id}</div>
                            </div>
                            <div class="order-detail-row">
                                <div class="order-detail-label">Date:</div>
                                <div>${new Date(data.order.tanggal).toLocaleDateString('id-ID', {day: 'numeric', month: 'long', year: 'numeric'})}</div>
                            </div>
                            <div class="order-detail-row">
                                <div class="order-detail-label">Customer:</div>
                                <div>${data.order.pelanggan}</div>
                            </div>
                            <div class="order-detail-row">
                                <div class="order-detail-label">Status:</div>
                                <div>
                                    <span class="status-badge status-${data.order.status.toLowerCase()}">${data.order.status}</span>
                                </div>
                            </div>
                            <div class="order-detail-row">
                                <div class="order-detail-label">Total:</div>
                                <div>Rp ${new Intl.NumberFormat('id-ID').format(data.order.total_harga)}</div>
                            </div>
                            <div class="order-detail-row">
                                <div class="order-detail-label">Shipping Address:</div>
                                <div>${data.order.alamat || 'Not provided'}</div>
                            </div>
                            <div class="order-detail-row">
                                <div class="order-detail-label">Phone:</div>
                                <div>${data.order.telepon || 'Not provided'}</div>
                            </div>
                            <div class="order-detail-row">
                                <div class="order-detail-label">Notes:</div>
                                <div>${data.order.catatan || 'No notes'}</div>
                            </div>
                            ${itemsHtml}
                        `;
                    })
                    .catch(error => {
                        console.error('Error fetching order details:', error);
                        detailContent.innerHTML = `
                            <div class="alert alert-danger">
                                <h5>Error fetching order details:</h5>
                                <p>${error.message}</p>
                                <hr>
                                <p class="mb-0">Please check the browser console for more details.</p>
                                <p class="mb-0">You may need to run <code>php artisan serve</code> to start the Laravel server.</p>
                                <p class="mt-2"><a href="/test_api.php?endpoint=get_order_details.php&id=${orderId}" target="_blank" class="btn btn-sm btn-outline-secondary">Test API Endpoint</a></p>
                            </div>
                        `;
                    });
            });
        });

        // Edit Order Modal
        document.querySelectorAll('.btn-edit').forEach(button => {
            button.addEventListener('click', function() {
                const orderId = this.getAttribute('data-id');
                document.getElementById('editOrderId').value = orderId;

                // Fetch current order status
                fetch(`/api/get_order_status.php?id=${orderId}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! Status: ${response.status}`);
                        }
                        return response.text().then(text => {
                            try {
                                return JSON.parse(text);
                            } catch (e) {
                                console.error('Invalid JSON:', text);
                                throw new Error(`Invalid JSON response: ${text.substring(0, 100)}...`);
                            }
                        });
                    })
                    .then(data => {
                        if (data.status) {
                            document.getElementById('orderStatus').value = data.status;
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching order status:', error);
                        // Set default status
                        document.getElementById('orderStatus').value = 'Baru';
                        // Show error message
                        const statusError = document.createElement('div');
                        statusError.className = 'alert alert-warning mt-2';
                        statusError.innerHTML = `
                            Could not fetch current status: ${error.message}
                            <p class="mt-2 mb-0"><a href="/test_api.php?endpoint=get_order_status.php&id=${orderId}" target="_blank" class="btn btn-sm btn-outline-secondary">Test API Endpoint</a></p>
                        `;
                        document.getElementById('orderStatus').parentNode.appendChild(statusError);
                    });
            });
        });

        // Handle update order status form submission
        document.getElementById('updateOrderForm').addEventListener('submit', function(e) {
            e.preventDefault();

            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Updating...';

            // Get form data
            const formData = new FormData(this);

            // Send AJAX request
            fetch(this.action, {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                // Show success message
                const alertDiv = document.createElement('div');
                alertDiv.className = data.success ? 'alert alert-success' : 'alert alert-danger';
                alertDiv.innerHTML = data.message;

                // Insert alert before form
                this.parentNode.insertBefore(alertDiv, this);

                // Hide alert after 3 seconds
                setTimeout(() => {
                    alertDiv.remove();

                    // Close modal and refresh page if success
                    if (data.success) {
                        bootstrap.Modal.getInstance(document.getElementById('editOrderModal')).hide();
                        location.reload();
                    }
                }, 3000);
            })
            .catch(error => {
                // Show error message
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-danger';
                alertDiv.innerHTML = `Error: ${error.message}`;

                // Insert alert before form
                this.parentNode.insertBefore(alertDiv, this);
            })
            .finally(() => {
                // Reset button state
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
            });
        });

        // Auto calculate total price based on selected products
        document.getElementById('productSelection').addEventListener('change', function() {
            let total = 0;
            Array.from(this.selectedOptions).forEach(option => {
                total += parseFloat(option.getAttribute('data-price'));
            });
            document.getElementById('newOrderTotal').value = total;
        });
    </script>
</body>
</html>
