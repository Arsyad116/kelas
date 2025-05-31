<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Banner Management - PUMA Admin Dashboard</title>
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

        /* Alert Styles */
        .alert {
            border-radius: 8px;
            margin-bottom: 25px;
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
                <a href="{{ url('/admin') }}" class="nav-link">
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
                <a href="{{ url('/admin/banners') }}" class="nav-link active">
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
    <h1 class="page-title">Banner Management</h1>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBannerModal">
        <i class="fas fa-plus me-2"></i> Add New Banner
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

<!-- Banner Preview Carousel -->
<div class="card mb-4">
    <div class="card-header py-3">
        <h5 class="mb-0">Banner Preview</h5>
    </div>
    <div class="card-body">
        <div id="bannerCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel">
            <div class="carousel-inner">
                @php
                    $activeBanners = isset($banners) ? $banners->where('is_active', 1) : collect([]);
                @endphp
                @if(count($activeBanners) > 0)
                    @foreach($activeBanners as $index => $banner)
                        <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                            <img src="{{ asset('images/' . $banner->image_path) }}" class="d-block w-100" alt="Banner {{ $index + 1 }}">
                        </div>
                    @endforeach
                @else
                    <div class="carousel-item active">
                        <div class="d-flex align-items-center justify-content-center bg-light" style="height: 300px;">
                            <div class="text-center">
                                <i class="fas fa-image fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No active banners</h5>
                                <p class="text-muted">Add banners to see them here</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            @if(count($activeBanners) > 1)
                <button class="carousel-control-prev" type="button" data-bs-target="#bannerCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#bannerCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            @endif
        </div>
    </div>
</div>

<!-- Banner Management Table -->
<div class="card">
    <div class="card-header py-3">
        <h5 class="mb-0">Manage Banners</h5>
    </div>
    <div class="card-body">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th width="10%">ID</th>
                        <th width="40%">Banner</th>
                        <th width="20%">Status</th>
                        <th width="20%">Date Added</th>
                        <th width="10%">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($banners) && count($banners) > 0)
                        @foreach($banners as $banner)
                            <tr>
                                <td>{{ $banner->id }}</td>
                                <td>
                                    <img src="{{ asset('images/' . $banner->image_path) }}" class="banner-preview" alt="Banner">
                                </td>
                                <td>
                                    @if($banner->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td>{{ date('d M Y', strtotime($banner->created_at)) }}</td>
                                <td>
                                    <form action="{{ route('admin.banners.update', $banner->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="is_active" value="{{ $banner->is_active ? 0 : 1 }}">
                                        <button type="submit" class="action-btn {{ $banner->is_active ? 'btn-toggle-on' : 'btn-toggle-off' }}" title="{{ $banner->is_active ? 'Deactivate' : 'Activate' }}">
                                            <i class="fas fa-{{ $banner->is_active ? 'toggle-on' : 'toggle-off' }}"></i>
                                        </button>
                                    </form>

                                    <form action="{{ route('admin.banners.destroy', $banner->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="action-btn btn-delete" onclick="return confirm('Are you sure you want to delete this banner? This action cannot be undone.');" title="Delete">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="5">
                                <div class="empty-state">
                                    <i class="fas fa-images"></i>
                                    <p class="empty-state-text">No banners found</p>
                                    <button type="button" class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#addBannerModal">
                                        <i class="fas fa-plus me-2"></i> Add First Banner
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

<!-- Add Banner Modal -->
<div class="modal fade" id="addBannerModal" tabindex="-1" aria-labelledby="addBannerModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addBannerModalLabel">Add New Banner</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="title" class="form-label">Banner Title</label>
                        <input type="text" class="form-control" id="title" name="title" value="Banner {{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description (Optional)</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="image" class="form-label">Upload Banner Image</label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*" required>
                        <div class="form-text">Recommended size: 1200 x 400 pixels. Max file size: 2MB.</div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" checked>
                            <label class="form-check-label" for="is_active">
                                Active
                            </label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div id="image-preview" class="mt-3 text-center d-none">
                            <img id="preview" src="#" alt="Banner Preview" class="img-fluid rounded" style="max-height: 200px;">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i> Add Banner
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
        </main>
    </div>

    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <!-- Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
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
            const bannerImageInput = document.getElementById('image');
            const imagePreview = document.getElementById('image-preview');
            const previewImg = document.getElementById('preview');

            if (bannerImageInput) {
                bannerImageInput.addEventListener('change', function() {
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

            // Initialize Bootstrap carousel
            const carousel = document.getElementById('bannerCarousel');
            if (carousel) {
                new bootstrap.Carousel(carousel, {
                    interval: 3000,
                    wrap: true
                });
            }
        });
    </script>
    <style>
        /* Banner Preview Styles */
        .banner-preview {
            width: 150px;
            height: 80px;
            object-fit: cover;
            border-radius: 6px;
            border: 1px solid #e0e0e0;
        }

        /* Carousel Styles */
        .carousel {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .carousel-item img {
            height: 300px;
            object-fit: cover;
        }

        /* Action Button Styles */
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

        .btn-delete {
            color: #e50010;
            border-color: rgba(229, 0, 16, 0.3);
        }

        .btn-delete:hover {
            background-color: rgba(229, 0, 16, 0.1);
            border-color: rgba(229, 0, 16, 0.5);
            color: #e50010;
        }

        .btn-toggle-on {
            color: #28a745;
            border-color: rgba(40, 167, 69, 0.3);
        }

        .btn-toggle-on:hover {
            background-color: rgba(40, 167, 69, 0.1);
            border-color: rgba(40, 167, 69, 0.5);
            color: #28a745;
        }

        .btn-toggle-off {
            color: #6c757d;
            border-color: rgba(108, 117, 125, 0.3);
        }

        .btn-toggle-off:hover {
            background-color: rgba(108, 117, 125, 0.1);
            border-color: rgba(108, 117, 125, 0.5);
            color: #6c757d;
        }

        /* Empty State Styles */
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
    </style>
</body>
</html>
