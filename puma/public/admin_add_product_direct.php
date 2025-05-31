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
                $sql = "INSERT INTO produk (nama, harga, gambar)
                        VALUES (?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sis", $nama, $harga, $newFileName);

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

            $sql = "INSERT INTO produk (nama, harga, gambar)
                    VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sis", $nama, $harga, $defaultImage);

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
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            position: relative;
            background-image: linear-gradient(135deg, rgba(245, 245, 245, 0.9) 0%, rgba(255, 255, 255, 0.9) 100%);
        }

        .app-container {
            display: flex;
            min-height: 100vh;
            position: relative;
        }

        /* Sidebar Styles */
        .sidebar {
            width: 250px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background: linear-gradient(135deg, #1a1a1a, #2d2d2d, #1a1a1a);
            border-right: 4px solid #e50010;
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
            padding: 12px 18px;
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            border-radius: 10px;
            margin-bottom: 10px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            z-index: 1;
        }

        .nav-link i {
            width: 20px;
            margin-right: 12px;
            font-size: 1.1rem;
            transition: all 0.3s ease;
        }

        .nav-link:hover {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.1);
            transform: translateX(5px);
        }

        .nav-link:hover i {
            transform: scale(1.2);
        }

        .nav-link.active {
            background: linear-gradient(45deg, #e50010, #ff3547);
            color: white;
            font-weight: 500;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .nav-link.active::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 5px;
            height: 100%;
            background-color: #fff;
            z-index: -1;
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
            margin-bottom: 30px;
            background-color: #ffffff;
            padding: 20px 25px;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            position: relative;
            overflow: hidden;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .content-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 5px;
            height: 100%;
            background: linear-gradient(to bottom, #e50010, #ff3547);
        }

        .page-title {
            font-size: 1.8rem;
            font-weight: 600;
            color: #333;
        }

        .card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border: none;
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #e50010, #ff3547, #e50010);
            background-size: 200% 100%;
            animation: gradientMove 3s ease infinite;
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
            padding: 12px 15px;
            border: 1px solid #e0e0e0;
            transition: all 0.3s ease;
            background-color: #f9f9f9;
        }

        .form-control:focus {
            border-color: #e50010;
            box-shadow: 0 5px 15px rgba(229, 0, 16, 0.1);
            background-color: #fff;
            transform: translateY(-2px);
        }

        .form-control::placeholder {
            color: #aaa;
            font-size: 0.9rem;
        }

        /* Image Preview */
        .preview-container {
            margin-top: 20px;
            text-align: center;
            transition: all 0.3s ease;
        }

        #imagePreview {
            max-width: 100%;
            max-height: 200px;
            border-radius: 12px;
            display: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border: 2px solid #fff;
            transition: all 0.3s ease;
        }

        #imagePreview:hover {
            transform: scale(1.02);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
        }

        /* Modern Red-Black Theme Styles */
        .btn-outline-secondary {
            border: 1px solid rgba(0, 0, 0, 0.2);
            color: #333;
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-outline-secondary:hover {
            background-color: #f8f9fa;
            color: #e50010;
            border-color: #e50010;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        .btn-modern-red {
            background: linear-gradient(45deg, #e50010, #ff3547);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 10px 25px;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(229, 0, 16, 0.3);
        }

        .btn-modern-red:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(229, 0, 16, 0.4);
            background: linear-gradient(45deg, #ff3547, #e50010);
            color: white;
        }

        .page-title {
            color: #1a1a1a;
            font-weight: 700;
            position: relative;
            display: inline-block;
            padding-bottom: 5px;
        }

        .page-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 40px;
            height: 3px;
            background: #e50010;
            border-radius: 3px;
        }

        .form-label {
            color: #333;
            font-weight: 500;
            margin-bottom: 10px;
            font-size: 0.95rem;
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

        /* Animations */
        @keyframes gradientMove {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Apply animations to elements */
        .card, .form-control, .btn {
            animation: fadeIn 0.5s ease-out forwards;
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

                                <!-- Stok dan kategori dihapus karena tidak ada di tabel -->
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="gambar" class="form-label">Product Image</label>
                                    <input type="file" class="form-control" id="gambar" name="gambar" accept="image/*" onchange="previewImage()">
                                    <div class="form-text">Recommended size: 500 x 500 pixels. Max file size: 2MB.</div>
                                </div>

                                <!-- is_active dihapus karena tidak ada di tabel -->

                                <div id="image-preview" class="mt-3 text-center">
                                    <img id="imagePreview" src="#" alt="Image Preview" class="img-fluid rounded">
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <a href="/admin" class="btn btn-outline-secondary me-2">
                                <i class="fas fa-times me-2"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-modern-red">
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
