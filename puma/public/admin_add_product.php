<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dbpuma";

$message = "";
$messageType = "";

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Check connection
        if ($conn->connect_error) {
            throw new Exception("Connection failed: " . $conn->connect_error);
        }

        // Get form data
        $nama = $_POST['nama'];
        $harga = $_POST['harga'];
        $stok = $_POST['stok'] ?? 0;
        $kategori = $_POST['kategori'] ?? 'Uncategorized';
        $is_active = isset($_POST['is_active']) ? 1 : 0;

        // Handle file upload
        if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
            $uploadDir = "images/";

            // Create uploads directory if it doesn't exist
            if (!is_dir($uploadDir)) {
                if (!mkdir($uploadDir, 0777, true)) {
                    throw new Exception("Failed to create uploads directory. Check folder permissions.");
                }
            }

            // Get file information
            $fileTmpPath = $_FILES['gambar']['tmp_name'];
            $fileName = basename($_FILES['gambar']['name']);
            $imageFileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            // Validate file type
            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
            if (!in_array($imageFileType, $allowedTypes)) {
                throw new Exception("Error: Only JPG, JPEG, PNG, and GIF files are allowed.");
            }

            // Check file size (max 2MB)
            if ($_FILES['gambar']['size'] > 2 * 1024 * 1024) {
                throw new Exception("Error: File size too large. Maximum size is 2MB.");
            }

            // Generate unique filename
            $newFileName = time() . '_product_' . str_replace(' ', '_', $nama) . '.' . $imageFileType;
            $targetFile = $uploadDir . $newFileName;

            // Move uploaded file
            if (move_uploaded_file($fileTmpPath, $targetFile)) {
                // Insert product into database
                $sql = "INSERT INTO produk (nama, harga, gambar, stok, kategori, is_active)
                        VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sisssi", $nama, $harga, $newFileName, $stok, $kategori, $is_active);

                if ($stmt->execute()) {
                    $message = "Product added successfully!";
                    $messageType = "success";
                } else {
                    throw new Exception("Error adding product: " . $stmt->error);
                }

                $stmt->close();
            } else {
                throw new Exception("Error uploading file. Check folder permissions.");
            }
        } else {
            // Insert product without image
            $defaultImage = "default.jpg";

            $sql = "INSERT INTO produk (nama, harga, gambar, stok, kategori, is_active)
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sisssi", $nama, $harga, $defaultImage, $stok, $kategori, $is_active);

            if ($stmt->execute()) {
                $message = "Product added successfully (with default image)!";
                $messageType = "success";
            } else {
                throw new Exception("Error adding product: " . $stmt->error);
            }

            $stmt->close();
        }

        $conn->close();
    } catch (Exception $e) {
        $message = "Error: " . $e->getMessage();
        $messageType = "danger";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Product - PUMA Admin</title>
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

        /* Form Styles */
        .form-label {
            font-weight: 500;
            margin-bottom: 8px;
        }

        .form-control {
            border-radius: 8px;
            padding: 10px 15px;
            border: 1px solid #e0e0e0;
        }

        .form-control:focus {
            border-color: #e50010;
            box-shadow: 0 0 0 0.25rem rgba(229, 0, 16, 0.25);
        }

        /* Image Preview */
        .preview-container {
            margin-top: 20px;
            text-align: center;
        }

        #imagePreview {
            max-width: 100%;
            max-height: 200px;
            border-radius: 8px;
            display: none;
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
                <img src="images/logo.png" alt="PUMA" class="logo-img">
                <h5 class="mt-3 text-white fw-bold">Admin Dashboard</h5>
            </div>

            <nav class="mt-4">
                <a href="/admin" class="nav-link active">
                    <i class="fas fa-box-open"></i>
                    <span>Products</span>
                </a>
                <a href="/admin/orders" class="nav-link">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Orders</span>
                </a>
                <a href="/admin/customers" class="nav-link">
                    <i class="fas fa-users"></i>
                    <span>Customers</span>
                </a>
                <a href="/admin/banners" class="nav-link">
                    <i class="fas fa-image"></i>
                    <span>Banners</span>
                </a>
                <a href="/logout" class="nav-link mt-5">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <div class="content-header">
                <h1 class="page-title">Add New Product</h1>
                <a href="/admin" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i> Back to Products
                </a>
            </div>

            <?php if (!empty($message)): ?>
                <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="fas <?php echo $messageType == 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'; ?> me-2"></i>
                        <?php echo $message; ?>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nama" class="form-label">Product Name</label>
                                    <input type="text" class="form-control" id="nama" name="nama" required>
                                </div>

                                <div class="mb-3">
                                    <label for="harga" class="form-label">Price (Rp)</label>
                                    <input type="number" class="form-control" id="harga" name="harga" required>
                                </div>

                                <div class="mb-3">
                                    <label for="stok" class="form-label">Stock</label>
                                    <input type="number" class="form-control" id="stok" name="stok" value="0">
                                </div>

                                <div class="mb-3">
                                    <label for="kategori" class="form-label">Category</label>
                                    <input type="text" class="form-control" id="kategori" name="kategori" value="Uncategorized">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="gambar" class="form-label">Product Image</label>
                                    <input type="file" class="form-control" id="gambar" name="gambar" accept="image/*" onchange="previewImage()">
                                    <div class="form-text">Recommended size: 500 x 500 pixels. Max file size: 2MB.</div>
                                </div>

                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" checked>
                                        <label class="form-check-label" for="is_active">
                                            Active
                                        </label>
                                    </div>
                                </div>

                                <div id="image-preview" class="mt-3 text-center">
                                    <img id="imagePreview" src="#" alt="Image Preview" class="img-fluid rounded">
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <a href="/admin" class="btn btn-outline-secondary me-2">
                                <i class="fas fa-times me-2"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-plus-circle me-2"></i> Add Product
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
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
        });

        // Image preview functionality
        function previewImage() {
            const file = document.getElementById('gambar').files[0];
            const preview = document.getElementById('imagePreview');
            const previewContainer = document.getElementById('image-preview');

            if (file) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }

                reader.readAsDataURL(file);
            } else {
                preview.style.display = 'none';
            }
        }
    </script>
</body>
</html>
