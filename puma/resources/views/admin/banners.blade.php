<?php
session_start(); // Pastikan session sudah dimulai di baris paling atas

// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dbpuma";

// Create database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize message variables
$message = "";
$messageType = "";

// Handle banner upload
if (isset($_POST['add_banner'])) {
    // Check if file was uploaded without errors
    if (isset($_FILES['banner_image']) && $_FILES['banner_image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['banner_image']['name'];
        $filetype = pathinfo($filename, PATHINFO_EXTENSION);
        
        // Verify file extension
        if (in_array(strtolower($filetype), $allowed)) {
            // Create unique filename
            $newFilename = 'banner_' . time() . '.' . $filetype;
            $uploadPath = '../images/' . $newFilename;
            
            // Upload file
            if (move_uploaded_file($_FILES['banner_image']['tmp_name'], $uploadPath)) {
                // Insert into database
                $stmt = $conn->prepare("INSERT INTO banners (image_path, status, created_at) VALUES (?, 1, NOW())");
                $stmt->bind_param("s", $newFilename);
                
                if ($stmt->execute()) {
                    $_SESSION['toast'] = [
                        'message' => 'Banner berhasil ditambahkan!',
                        'type' => 'success'
                    ];
                } else {
                    $_SESSION['toast'] = [
                        'message' => "Error: " . $stmt->error,
                        'type' => 'error'
                    ];
                }
                
    $stmt->close();
    header("Location: banners.php");
    exit();
}
        } else {
            $_SESSION['toast'] = [
                'message' => "Invalid file type. Only JPG, JPEG, PNG and GIF are allowed.",
                'type' => 'error'
            ];
        }
    } else {
        $_SESSION['toast'] = [
            'message' => "Please select an image to upload.",
            'type' => 'error'
        ];
    }
     header("Location: banners.php");
     exit();
}

// Handle banner deletion
if (isset($_GET['delete'])) {
    $bannerId = intval($_GET['delete']);
    
    // Get file name before deletion to remove the file
    $stmt = $conn->prepare("SELECT image_path FROM banners WHERE id = ?");
    $stmt->bind_param("i", $bannerId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $imagePath = '../images/' . $row['image_path'];
        if (file_exists($imagePath)) {
            unlink($imagePath); // Delete the file
        }
    }
    
    $stmt->close();
    
    // Delete from database
    $stmt = $conn->prepare("DELETE FROM banners WHERE id = ?");
    $stmt->bind_param("i", $bannerId);
    
    if ($stmt->execute()) {
        $_SESSION['toast'] = [
            'message' => 'Banner berhasil dihapus!',
            'type' => 'success'
        ];
    } else {
        $_SESSION['toast'] = [
            'message' => "Error: " . $stmt->error,
            'type' => 'error'
        ];
    }
    
    $stmt->close();
     $_SESSION['toast'] = [
            'message' => 'Banner berhasil dihapus!',
            'type' => 'success'
        ];
    header("Location: banners.php");
    exit();
}

// Handle banner status toggle (active/inactive)
if (isset($_GET['toggle'])) {
    $bannerId = intval($_GET['toggle']);
    
    // Get current status
    $stmt = $conn->prepare("SELECT status FROM banners WHERE id = ?");
    $stmt->bind_param("i", $bannerId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $newStatus = $row['status'] == 1 ? 0 : 1;
        
        // Update status
        $updateStmt = $conn->prepare("UPDATE banners SET status = ? WHERE id = ?");
        $updateStmt->bind_param("ii", $newStatus, $bannerId);
        
        if ($updateStmt->execute()) {
            $_SESSION['toast'] = [
                'message' => 'Banner status berhasil diubah!',
                'type' => 'success'
            ];
        } else {
            $_SESSION['toast'] = [
                'message' => "Error: " . $updateStmt->error,
                'type' => 'error'
            ];
        }
        
        $updateStmt->close();
    }
    
    $stmt->close();
    header("Location: banners.php");
    exit();
}

// Get banners with pagination and search
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = 5; // Show fewer banners per page since they include images
$offset = ($page - 1) * $limit;
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

// Build the WHERE clause for search
$whereClause = '';
if (!empty($search)) {
    $whereClause = " WHERE image_path LIKE '%$search%'";
}

// Count total banners for pagination
$totalQuery = "SELECT COUNT(*) as total FROM banners" . $whereClause;
$totalResult = $conn->query($totalQuery);

// Initialize $totalBanners to avoid undefined variable error
$totalBanners = 0;
if ($totalResult && $totalResult->num_rows > 0) {
    $totalRow = $totalResult->fetch_assoc();
    $totalBanners = $totalRow['total'];
}

$totalPages = ceil($totalBanners / $limit);

// Get banners for current page
$query = "SELECT * FROM banners" . $whereClause . " ORDER BY id DESC LIMIT $limit OFFSET $offset";
$result = $conn->query($query);

// Initialize $result if query failed
if (!$result) {
    $result = new stdClass();
    $result->num_rows = 0;
}

// Get all active banners for preview carousel
$carouselQuery = "SELECT image_path FROM banners WHERE status = 1 ORDER BY id DESC";
$carouselResult = $conn->query($carouselQuery);
$activeBanners = [];

if ($carouselResult && $carouselResult->num_rows > 0) {
    while ($row = $carouselResult->fetch_assoc()) {
        $activeBanners[] = $row['image_path'];
    }
}
?>

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
        
        /* Form Styles */
        .form-label {
            font-weight: 500;
            color: #555;
        }
        
        .form-control {
            border-radius: 8px;
            padding: 10px 15px;
            border: 1px solid #e0e0e0;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #e50010;
            box-shadow: 0 0 0 0.2rem rgba(229, 0, 16, 0.25);
        }
        
        /* Button Styles */
        .btn {
            border-radius: 8px;
            font-weight: 500;
            padding: 10px 20px;
            transition: all 0.3s ease;
        }
        
        .btn-primary {
            background-color: #e50010; /* PUMA red */
            border-color: #e50010;
        }
        
        .btn-primary:hover {
            background-color: #c2000e;
            border-color: #c2000e;
        }
        
        /* Table Styles */
        .table-container {
            overflow-x: auto;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
        }
        
        .table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-bottom: 0;
        }
        
        .table th {
            background-color: #f8f9fa;
            color: #555;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.5px;
            padding: 15px;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .table td {
            padding: 15px;
            vertical-align: middle;
            border-top: none;
            border-bottom: 1px solid #e0e0e0;
            color: #555;
        }
        
        .table tbody tr:hover {
            background-color: #f9f9f9;
        }
        
        .table tbody tr:last-child td {
            border-bottom: none;
        }
        
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
        
        /* Pagination Styles */
        .pagination {
            margin-top: 25px;
            justify-content: center;
        }
        
        .page-item {
            margin: 0 3px;
        }
        
        .page-link {
            border: none;
            border-radius: 8px;
            color: #555;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }
        
        .page-item.active .page-link {
            background-color: #e50010; /* PUMA red */
            color: white;
            box-shadow: 0 2px 5px rgba(229, 0, 16, 0.3);
        }
        
        .page-link:hover {
            background-color: #f5f5f5;
            color: #333;
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
            
            .carousel-item img {
                height: 200px;
            }
        }
    </style>
</head>
<body>
        
    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <!-- Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <div class="app-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="logo-area">
                <img src="../images/logo.png" alt="PUMA" class="logo-img">
                <h5 class="mt-3 text-white fw-bold">Admin Dashboard</h5>
            </div>
            
            <nav class="mt-4">
                <a href="index.php" class="nav-link">
                    <i class="fas fa-box-open"></i>
                    <span>Products</span>
                </a>
                <a href="orders.php" class="nav-link">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Orders</span>
                </a>
                <a href="customers.php" class="nav-link">
                    <i class="fas fa-users"></i>
                    <span>Customers</span>
                </a>
                <a href="banners.php" class="nav-link active">
                    <i class="fas fa-image"></i>
                    <span>Banners</span>
                </a>
                <a href="../logout.php" class="nav-link mt-5">
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

            <?php if (!empty($message)): ?>
            <div class="alert alert-<?php echo $messageType === 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
                <div class="d-flex align-items-center">
                    <i class="fas fa-<?php echo $messageType === 'success' ? 'check-circle' : 'exclamation-circle'; ?> me-2"></i>
                    <?php echo $message; ?>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php endif; ?>

            <!-- Banner Preview Carousel -->
            <div class="card mb-4">
                <div class="card-header py-3">
                    <h5 class="mb-0">Banner Preview</h5>
                </div>
                <div class="card-body">
                    <div id="bannerCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            <?php if (!empty($activeBanners)): ?>
                                <?php foreach ($activeBanners as $index => $banner): ?>
                                    <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                                        <img src="../images/<?php echo htmlspecialchars($banner); ?>" class="d-block w-100" alt="Banner <?php echo $index + 1; ?>">
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="carousel-item active">
                                    <div class="d-flex align-items-center justify-content-center bg-light" style="height: 300px;">
                                        <div class="text-center">
                                            <i class="fas fa-image fa-3x text-muted mb-3"></i>
                                            <h5 class="text-muted">No active banners</h5>
                                            <p class="text-muted">Add banners to see them here</p>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                        <?php if (count($activeBanners) > 1): ?>
                            <button class="carousel-control-prev" type="button" data-bs-target="#bannerCarousel" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Previous</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#bannerCarousel" data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Next</span>
                            </button>
                        <?php endif; ?>
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
                                <?php if ($result->num_rows > 0): ?>
                                    <?php while ($row = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo $row['id']; ?></td>
                                            <td>
                                                <img src="../images/<?php echo htmlspecialchars($row['image_path']); ?>" class="banner-preview" alt="Banner">
                                            </td>
                                            <td>
                                                <?php if ($row['status'] == 1): ?>
                                                    <span class="badge bg-success">Active</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Inactive</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo date('d M Y', strtotime($row['created_at'])); ?></td>
                                            <td>
                                                <a href="banners.php?toggle=<?php echo $row['id']; ?>" class="action-btn <?php echo $row['status'] == 1 ? 'btn-toggle-on' : 'btn-toggle-off'; ?>" title="<?php echo $row['status'] == 1 ? 'Deactivate' : 'Activate'; ?>">
                                                    <i class="fas fa-<?php echo $row['status'] == 1 ? 'toggle-on' : 'toggle-off'; ?>"></i>
                                                </a>
                                                <a href="banners.php?delete=<?php echo $row['id']; ?>" class="action-btn btn-delete" onclick="return confirm('Are you sure you want to delete this banner? This action cannot be undone.');" title="Delete">
                                                    <i class="fas fa-trash-alt"></i>
                                                </a>
<?php if (isset($_SESSION['toast'])): ?>
                                                    <script>
                                                        // Konfigurasi Toastr
                                                        toastr.options = {
                                                            "closeButton": true,
                                                            "progressBar": true,
                                                            "positionClass": "toast-top-right",
                                                            "preventDuplicates": true,
                                                            "showDuration": "300",
                                                            "hideDuration": "1000",
                                                            "timeOut": "5000",
                                                            "extendedTimeOut": "1000"
                                                        };

                                                        // Tampilkan notifikasi
                                                        <?php if (isset($_SESSION['toast']['type']) && isset($_SESSION['toast']['message'])): ?>
                                                            toastr.<?php echo $_SESSION['toast']['type']; ?>(
                                                                "<?php echo $_SESSION['toast']['message']; ?>"
                                                            );
                                                        <?php endif; ?>

                                                        // Hapus session setelah ditampilkan
                                                        <?php unset($_SESSION['toast']); ?>
                                                    </script>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
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
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                    <nav aria-label="Page navigation">
                        <ul class="pagination">
                            <?php if ($page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $page - 1; ?>" aria-label="Previous">
                                        <i class="fas fa-chevron-left"></i>
                                    </a>
                                </li>
                            <?php endif; ?>
                            
                            <?php
                            $startPage = max(1, $page - 2);
                            $endPage = min($totalPages, $startPage + 4);
                            
                            for ($i = $startPage; $i <= $endPage; $i++):
                            ?>
                                <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?>">
                                        <?php echo $i; ?>
                                    </a>
                                </li>
                            <?php endfor; ?>
                            
                            <?php if ($page < $totalPages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $page + 1; ?>" aria-label="Next">
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                    <?php endif; ?>
                    
                    <?php if ($totalBanners > 0): ?>
                    <div class="mt-4 text-center text-muted small">
                        Showing <?php echo $offset + 1; ?> to <?php echo min($offset + $limit, $totalBanners); ?> of <?php echo $totalBanners; ?> banners
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <!-- Add Banner Modal -->
    <div class="modal fade" id="addBannerModal" tabindex="-1" aria-labelledby="addBannerModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addBannerModalLabel">Add New Banner</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="banner_image" class="form-label">Upload Banner Image</label>
                            <input type="file" class="form-control" id="banner_image" name="banner_image" accept="image/*" required>
                            <div class="form-text">Recommended size: 1200 x 400 pixels. Max file size: 2MB.</div>
                        </div>
                        <div class="mb-3">
                            <div id="image-preview" class="mt-3 text-center d-none">
                                <img id="preview" src="#" alt="Banner Preview" class="img-fluid rounded" style="max-height: 200px;">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="add_banner" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i> Add Banner
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

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
        const bannerImageInput = document.getElementById('banner_image');
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
    </script>
</body>
</html>

<?php
// Close database connection
$conn->close();
?>
