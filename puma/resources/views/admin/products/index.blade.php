<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Management - PUMA Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Global Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        
        body {
            background-color: #f8f9fa;
            color: #333;
        }
        
        .app-container {
            display: flex;
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
        
        /* Main Content Styles */
        .main-content {
            flex: 1;
            margin-left: 250px;
            padding: 30px;
        }
        
        .content-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }
        
        .page-title {
            font-size: 1.8rem;
            font-weight: 600;
            color: #333;
        }
        
        .card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            border: none;
            margin-bottom: 30px;
        }
        
        .card-body {
            padding: 25px;
        }
        
        /* Product Table Styles */
        .product-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 6px;
        }
        
        .product-name {
            font-weight: 500;
            color: #333;
        }
        
        .product-price {
            font-weight: 600;
            color: #e50010;
        }
        
        .product-category {
            display: inline-block;
            padding: 4px 8px;
            background-color: #f0f0f0;
            border-radius: 4px;
            font-size: 0.8rem;
        }
        
        .action-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: 8px;
            transition: all 0.3s ease;
            margin: 0 5px;
            background-color: #fff;
            border: 1px solid #e0e0e0;
            color: #555;
        }
        
        .btn-edit {
            color: #2196f3;
            border-color: rgba(33, 150, 243, 0.3);
        }
        
        .btn-edit:hover {
            background-color: rgba(33, 150, 243, 0.1);
            border-color: rgba(33, 150, 243, 0.5);
            color: #2196f3;
        }
        
        .btn-delete {
            color: #e50010;
            border-color: rgba(229, 0, 16, 0.3);
        }
        
        .btn-delete:hover {
            background-color: rgba(229, 0, 16, 0.1);
            border-color: rgba(229, 0, 16, 0.5);
            color: #e50010;
        }
        
        /* Status Badge */
        .badge-active {
            background-color: #4caf50;
            color: white;
        }
        
        .badge-inactive {
            background-color: #9e9e9e;
            color: white;
        }
        
        /* Empty State */
        .empty-state {
            padding: 40px;
            text-align: center;
        }
        
        .empty-state i {
            font-size: 3rem;
            color: #ccc;
            margin-bottom: 15px;
        }
        
        .empty-state-text {
            color: #888;
            font-size: 1.1rem;
        }
        
        /* Responsive Styles */
        @media (max-width: 991.98px) {
            .sidebar {
                width: 200px;
            }
            
            .main-content {
                margin-left: 200px;
                padding: 20px;
            }
        }
        
        @media (max-width: 767.98px) {
            .sidebar {
                width: 0;
                padding: 0;
                overflow: hidden;
            }
            
            .main-content {
                margin-left: 0;
                padding: 15px;
            }
            
            .content-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .content-header .btn {
                margin-top: 15px;
                width: 100%;
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
                <a href="{{ url('/admin') }}" class="nav-link active">
                    <i class="fas fa-box-open"></i>
                    <span>Products</span>
                </a>
                <a href="{{ url('/admin/orders') }}" class="nav-link">
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
                <h1 class="page-title">Product Management</h1>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
                    <i class="fas fa-plus me-2"></i> Add New Product
                </button>
            </div>
            
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-check-circle me-2"></i>
                        {{ session('success') }}
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        {{ session('error') }}
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>Price</th>
                                    <th>Stock</th>
                                    <th>Category</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($products) && count($products) > 0)
                                    @foreach($products as $product)
                                        <tr>
                                            <td>
                                                <img src="{{ asset('images/' . $product->image) }}" alt="{{ $product->name }}" class="product-image">
                                            </td>
                                            <td class="product-name">{{ $product->name }}</td>
                                            <td class="product-price">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                                            <td>{{ $product->stock ?? 0 }}</td>
                                            <td>
                                                <span class="product-category">{{ $product->category ?? 'Uncategorized' }}</span>
                                            </td>
                                            <td>
                                                @if($product->is_active)
                                                    <span class="badge badge-active">Active</span>
                                                @else
                                                    <span class="badge badge-inactive">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.products.edit', $product->id) }}" class="action-btn btn-edit" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                
                                                <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="action-btn btn-delete" onclick="return confirm('Are you sure you want to delete this product?');" title="Delete">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="7">
                                            <div class="empty-state">
                                                <i class="fas fa-box-open"></i>
                                                <p class="empty-state-text">No products found</p>
                                                <button type="button" class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#addProductModal">
                                                    <i class="fas fa-plus me-2"></i> Add First Product
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <!-- Add Product Modal -->
    <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addProductModalLabel">Add New Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Product Name</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="price" class="form-label">Price (Rp)</label>
                                    <input type="number" class="form-control @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price') }}" required>
                                    @error('price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="stock" class="form-label">Stock</label>
                                    <input type="number" class="form-control @error('stock') is-invalid @enderror" id="stock" name="stock" value="{{ old('stock', 0) }}">
                                    @error('stock')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="category" class="form-label">Category</label>
                                    <input type="text" class="form-control @error('category') is-invalid @enderror" id="category" name="category" value="{{ old('category', 'Uncategorized') }}">
                                    @error('category')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="image" class="form-label">Product Image</label>
                                    <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image" accept="image/*" required>
                                    <div class="form-text">Recommended size: 500 x 500 pixels. Max file size: 2MB.</div>
                                    @error('image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" checked>
                                        <label class="form-check-label" for="is_active">
                                            Active
                                        </label>
                                    </div>
                                </div>
                                
                                <div id="image-preview" class="mt-3 text-center d-none">
                                    <img id="preview" src="#" alt="Product Preview" class="img-fluid rounded" style="max-height: 200px;">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i> Add Product
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Add responsive sidebar toggle for mobile
        document.addEventListener('DOMContentLoaded', function() {
            // Create toggle button for mobile
            const toggleBtn = document.createElement('button');
            toggleBtn.className = 'navbar-toggler position-fixed top-0 start-0 m-3 p-2 rounded-circle shadow bg-white d-md-none';
            toggleBtn.innerHTML = '<i class="fas fa-bars"></i>';
            toggleBtn.style.zIndex = '1050';
            toggleBtn.style.width = '40px';
            toggleBtn.style.height = '40px';
            document.body.appendChild(toggleBtn);
            
            // Toggle sidebar on click
            toggleBtn.addEventListener('click', function() {
                const sidebar = document.querySelector('.sidebar');
                if (sidebar.style.width === '200px') {
                    sidebar.style.width = '0';
                    sidebar.style.padding = '0';
                } else {
                    sidebar.style.width = '200px';
                    sidebar.style.padding = '20px';
                }
            });
            
            // Close sidebar when clicking outside
            document.addEventListener('click', function(event) {
                const sidebar = document.querySelector('.sidebar');
                const toggleBtn = document.querySelector('.navbar-toggler');
                
                if (window.innerWidth < 768 && 
                    !sidebar.contains(event.target) && 
                    !toggleBtn.contains(event.target) && 
                    sidebar.style.width === '200px') {
                    sidebar.style.width = '0';
                    sidebar.style.padding = '0';
                }
            });
            
            // Image preview functionality
            const imageInput = document.getElementById('image');
            const imagePreview = document.getElementById('image-preview');
            const previewImg = document.getElementById('preview');
            
            if (imageInput) {
                imageInput.addEventListener('change', function() {
                    if (this.files && this.files[0]) {
                        const reader = new FileReader();
                        
                        reader.onload = function(e) {
                            previewImg.src = e.target.result;
                            imagePreview.classList.remove('d-none');
                        };
                        
                        reader.readAsDataURL(this.files[0]);
                    } else {
                        imagePreview.classList.add('d-none');
                    }
                });
            }
        });
    </script>
</body>
</html>
