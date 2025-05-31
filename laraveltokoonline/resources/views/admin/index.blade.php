<?php
// Enable error reporting
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

// Debug information for $_FILES
if (isset($_FILES['banner'])) {
    echo "File information: <pre>";
    print_r($_FILES['banner']);
    echo "</pre>";
}

// Check if a file was uploaded
if (isset($_FILES['banner']) && $_FILES['banner']['error'] == 0) {
    $uploadDir = "uploads/";

    // Create uploads directory if it doesn't exist
    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0777, true)) {
            die("Failed to create uploads directory. Check folder permissions.");
        }
    }

    // Get file information
    $fileTmpPath = $_FILES['banner']['tmp_name'];
    $fileName = basename($_FILES['banner']['name']);
    $imageFileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    // Validate file type
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($imageFileType, $allowedTypes)) {
        die("Error: Only JPG, JPEG, PNG, and GIF files are allowed.");
    }

    // Check file size (max 2MB)
    if ($_FILES['banner']['size'] > 2 * 1024 * 1024) {
        die("Error: File size too large. Maximum size is 2MB.");
    }

    // Generate unique filename
    $newFileName = uniqid("banner_", true) . "." . $imageFileType;
    $targetFile = $uploadDir . $newFileName;

    // Move uploaded file
    if (move_uploaded_file($fileTmpPath, $targetFile)) {
        // Save path to database using prepared statement
        $stmt = $conn->prepare("INSERT INTO banners (image_path) VALUES (?)");
        $stmt->bind_param("s", $newFileName);

        if ($stmt->execute()) {
            echo "<p style='color: green;'>Upload successful! File saved as: " . $newFileName . "</p>";
        } else {
            echo "<p style='color: red;'>Database Error: " . $stmt->error . "</p>";
        }

        $stmt->close();
    } else {
        echo "<p style='color: red;'>Error uploading file. Check folder permissions.</p>";
    }
} else if (isset($_FILES['banner'])) {
    echo "<p style='color: red;'>Upload error code: " . $_FILES['banner']['error'] . "</p>";
}

// Fetch product details for AJAX requests
if (isset($_GET['action']) && $_GET['action'] == 'get_product' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT * FROM produk WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode($result->fetch_assoc());
    } else {
        echo json_encode(['error' => 'Product not found']);
    }
    $stmt->close();
    $conn->close();
    exit;
}

// Handle AJAX product update
if (isset($_POST['edit_product']) && isset($_POST['id']) && isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    $id = intval($_POST['id']);
    $nama = $conn->real_escape_string($_POST['nama']);
    $harga = intval($_POST['harga']);
    $response = ['success' => false];

    // Check if there's a new image
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $uploadDir = "uploads/";
        $fileName = basename($_FILES['gambar']['name']);
        $imageFileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $newFileName = uniqid("product_", true) . "." . $imageFileType;
        $targetFile = $uploadDir . $newFileName;

        if (move_uploaded_file($_FILES['gambar']['tmp_name'], $targetFile)) {
            $stmt = $conn->prepare("UPDATE produk SET nama=?, harga=?, gambar=? WHERE id=?");
            $stmt->bind_param("sisi", $nama, $harga, $newFileName, $id);
        } else {
            $response['message'] = 'Failed to upload image';
            echo json_encode($response);
            exit;
        }
    } else {
        $stmt = $conn->prepare("UPDATE produk SET nama=?, harga=? WHERE id=?");
        $stmt->bind_param("sii", $nama, $harga, $id);
    }

    if ($stmt->execute()) {
        $response['success'] = true;
    } else {
        $response['message'] = $stmt->error;
    }

    echo json_encode($response);
    $stmt->close();
    $conn->close();
    exit;
}

// Tambah produk
if (isset($_POST['add_product'])) {
    $nama = $_POST['nama'];
    $harga = $_POST['harga'];
    $gambar = $_FILES['gambar']['name'];
    $target = "uploads/" . basename($gambar);

    if (move_uploaded_file($_FILES['gambar']['tmp_name'], $target)) {
        $sql = "INSERT INTO produk (nama, harga, gambar) VALUES ('$nama', '$harga', '$gambar')";
        if ($conn->query($sql) === TRUE) {
            $message = "Produk berhasil ditambahkan!";
            $messageType = "success";
        } else {
            $message = "Error: " . $conn->error;
            $messageType = "error";
        }
    }
}

// Edit produk (non-AJAX form submission)
if (isset($_POST['edit_product']) && !isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
    $id = $_POST['id'];
    $nama = $_POST['nama'];
    $harga = $_POST['harga'];

    // Cek apakah ada file gambar baru
    if ($_FILES['gambar']['name']) {
        $gambar = $_FILES['gambar']['name'];
        $target = "uploads/" . basename($gambar);

        // Upload gambar baru
        if (move_uploaded_file($_FILES['gambar']['tmp_name'], $target)) {
            $sql = "UPDATE produk SET nama='$nama', harga='$harga', gambar='$gambar' WHERE id='$id'";
        }
    } else {
        // Jika tidak ada gambar baru, hanya update nama dan harga
        $sql = "UPDATE produk SET nama='$nama', harga='$harga' WHERE id='$id'";
    }

    if ($conn->query($sql) === TRUE) {
        $message = "Produk berhasil diupdate!";
        $messageType = "success";
    } else {
        $message = "Error: " . $conn->error;
        $messageType = "error";
    }
}

// Hapus produk
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $sql = "DELETE FROM produk WHERE id='$id'";
    if ($conn->query($sql) === TRUE) {
        $message = "Produk berhasil dihapus!";
        $messageType = "success";
    } else {
        $message = "Error: " . $conn->error;
        $messageType = "error";
    }
}

// Ambil data produk
$result = $conn->query("SELECT * FROM produk");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PUMA Admin Dashboard</title>
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
            height: 100vh;
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
            overflow-y: auto;
        }

        .content-header {
            padding-bottom: 20px;
            border-bottom: 1px solid #dee2e6;
        }

        .page-title {
            margin: 0;
            font-weight: 600;
            color: var(--dark-color);
        }

        .stat-card {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            display: flex;
            align-items: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .stat-card i {
            font-size: 2.5rem;
            margin-right: 15px;
            color: var(--primary-color);
        }

        .stat-info h3 {
            margin: 0;
            font-weight: 600;
        }

        .stat-info p {
            margin: 5px 0 0;
            color: #6c757d;
        }

        .products-grid {
            margin-top: 30px;
        }

        .product-img-cell img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 5px;
            border: 1px solid #dee2e6;
        }

        .product-name {
            font-weight: 500;
        }

        .price-tag {
            font-weight: 600;
            color: var(--primary-color);
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .status-badge.active {
            background-color: rgba(40, 167, 69, 0.2);
            color: var(--success-color);
        }

        .btn-icon {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 5px;
            border: none;
            transition: all 0.2s;
        }

        .btn-edit {
            background-color: rgba(255, 193, 7, 0.2);
            color: var(--warning-color);
        }

        .btn-edit:hover {
            background-color: var(--warning-color);
            color: white;
        }

        .btn-delete {
            background-color: rgba(220, 53, 69, 0.2);
            color: var(--danger-color);
        }

        .btn-delete:hover {
            background-color: var(--danger-color);
            color: white;
        }

        .action-buttons {
            display: flex;
        }

        .add-product-btn {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .add-product-btn:hover {
            background-color: #c01424;
            border-color: #c01424;
        }

        .product-form {
            position: relative;
        }

        .image-preview-container {
            border: 2px dashed #dee2e6;
            border-radius: 5px;
            min-height: 150px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
            transition: all 0.3s;
        }

        .image-preview-container.has-image {
            border-style: solid;
        }

        .product-preview {
            max-width: 100%;
            max-height: 150px;
            object-fit: contain;
        }

        /* Toast Notification Styles */
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 10px;
            max-width: 350px;
        }

        .toast-notification {
            display: flex;
            align-items: center;
            padding: 15px;
            border-radius: 8px;
            color: white;
            font-weight: 500;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            animation: slideIn 0.3s ease forwards, fadeOut 0.5s ease 2.5s forwards;
        }

        .toast-notification.success {
            background-color: var(--success-color);
        }

        .toast-notification.error {
            background-color: var(--danger-color);
        }

        .toast-notification i {
            margin-right: 10px;
            font-size: 1.2rem;
        }

        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        @keyframes fadeOut {
            from { opacity: 1; }
            to { opacity: 0; }
        }

        /* Floating Message Window */
        .floating-message {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.2);
            padding: 0;
            width: 350px;
            overflow: hidden;
            z-index: 9999;
            transition: all 0.3s ease;
            opacity: 0;
            transform: translateY(20px);
            pointer-events: none;
        }

        .floating-message.show {
            opacity: 1;
            transform: translateY(0);
            pointer-events: all;
        }

        .floating-message-header {
            padding: 15px;
            background-color: var(--primary-color);
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .floating-message-header h5 {
            margin: 0;
            font-weight: 600;
        }

        .floating-message-close {
            background: none;
            border: none;
            color: white;
            cursor: pointer;
            font-size: 1.2rem;
            padding: 0;
            line-height: 1;
        }

        .floating-message-body {
            padding: 15px;
            max-height: 300px;
            overflow-y: auto;
        }

        .floating-message.success .floating-message-header {
            background-color: var(--success-color);
        }

        .floating-message.error .floating-message-header {
            background-color: var(--danger-color);
        }

        .floating-message.warning .floating-message-header {
            background-color: var(--warning-color);
        }

        .floating-message.info .floating-message-header {
            background-color: var(--info-color);
        }

        /* For draggable functionality */
        .floating-message.dragging {
            cursor: grabbing;
            user-select: none;
        }

        /* Enhanced Modal Styles */
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

        /* Form Styling */
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

        /* Image Preview Enhancement */
        .image-preview-container {
            background-color: #f8f9fa;
            border: 2px dashed #dee2e6;
            border-radius: 15px;
            min-height: 200px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .image-preview-container.has-image {
            border: none;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .image-preview-container img {
            max-height: 200px;
            object-fit: contain;
            border-radius: 10px;
        }

        /* Loading Spinner Enhancement */
        #image-loading {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        /* File Input Enhancement */
        .file-upload-wrapper {
            position: relative;
            margin-top: 15px;
        }

        .file-upload-label {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            background-color: #f8f9fa;
            border: 2px solid #edf2f7;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .file-upload-label:hover {
            background-color: #edf2f7;
        }

        .file-upload-label i {
            margin-right: 10px;
            color: var(--primary-color);
        }

        /* Button Enhancement */
        .modal .btn-primary {
            background: linear-gradient(45deg, var(--primary-color), #ff4757);
            border: none;
            padding: 12px 24px;
            border-radius: 10px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .modal .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(237, 27, 46, 0.3);
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
                <a href="index.php" class="nav-link active">
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
                <a href="banners.php" class="nav-link">
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
            <header class="content-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h1 class="page-title">Product Management</h1>
                    <div>
                        <a href="/admin_add_product_direct.php" class="btn me-2" style="background: linear-gradient(45deg, #e50010, #ff3547); color: white; border: none; border-radius: 8px; padding: 10px 20px; box-shadow: 0 4px 15px rgba(229, 0, 16, 0.3); transition: all 0.3s ease;">
                            <i class="fas fa-plus-circle me-2"></i>Add Product (Direct)
                        </a>
                    </div>
                </div>
                <div class="stats-cards mt-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="stat-card">
                                <i class="fas fa-box-open"></i>
                                <div class="stat-info">
                                    <h3><?php echo $result->num_rows; ?></h3>
                                    <p>Total Products</p>
                                </div>
                            </div>
                        </div>
                        <!-- Add more stat cards as needed -->
                    </div>
                </div>
            </header>

            <!-- Products Grid -->
            <div class="products-grid mt-4">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Name</th>
                                        <th>Price</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Reset result pointer
                                    if ($result->num_rows > 0) {
                                        $result->data_seek(0);
                                    }
                                    while ($row = $result->fetch_assoc()):
                                    ?>
                                    <tr class="product-row">
                                        <td>
                                            <div class="product-img-cell">
                                                <img src="{{ asset('images/' . $row['gambar']) }}" alt="{{ $row['nama'] }}">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="product-name"><?= $row['nama']; ?></div>
                                            <small class="text-muted">ID: <?= $row['id']; ?></small>
                                        </td>
                                        <td>
                                            <div class="price-tag">
                                                Rp <?= number_format($row['harga'], 0, ',', '.'); ?>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="status-badge active">Active</span>
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <button class="btn btn-icon btn-edit" onclick="openEditModal(<?= $row['id']; ?>)" title="Edit Product">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-icon btn-delete" onclick="confirmDelete(<?= $row['id']; ?>)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Add Product Modal -->
    <div class="modal fade" id="addProductModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" enctype="multipart/form-data" class="product-form">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Product Name</label>
                                    <input type="text" name="nama" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Price</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" name="harga" class="form-control" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Product Image</label>
                                    <input type="file" name="gambar" class="form-control" accept="image/*" required onchange="previewImage(this, 'new-product-preview')">
                                </div>
                                <div class="image-preview-container mb-3">
                                    <img id="new-product-preview" class="product-preview" style="display: none;">
                                </div>
                            </div>
                        </div>
                        <button type="submit" name="add_product" class="btn btn-primary w-100">
                            <i class="fas fa-plus-circle me-2"></i>Add Product
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Product Modal -->
    <div class="modal fade" id="editProductModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-edit me-2"></i>
                    Edit Product
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editProductForm" method="POST" enctype="multipart/form-data" class="product-form">
                    <input type="hidden" name="id" id="edit_id">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label">
                                    <i class="fas fa-box me-2"></i>
                                    Product Name
                                </label>
                                <input type="text" name="nama" id="edit_nama" class="form-control" required>
                            </div>
                            <div class="mb-4">
                                <label class="form-label">
                                    <i class="fas fa-tag me-2"></i>
                                    Price
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="harga" id="edit_harga" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label">
                                    <i class="fas fa-image me-2"></i>
                                    Product Image
                                </label>
                                <div class="image-preview-container mb-3">
                                    <div id="image-loading" class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <img id="current_image" class="product-preview" style="display: none;">
                                </div>
                                <div class="file-upload-wrapper">
                                    <label class="file-upload-label w-100">
                                        <i class="fas fa-cloud-upload-alt"></i>
                                        <span>Choose new image</span>
                                        <input type="file" name="gambar" class="form-control" accept="image/*"
                                               onchange="previewImage(this, 'current_image')" style="display: none;">
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="submit" name="edit_product" class="btn btn-primary w-100 mt-4">
                        <i class="fas fa-save me-2"></i>
                        Save Changes
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteConfirmModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Confirm Deletion</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this product? This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <a href="#" id="confirmDeleteBtn" class="btn btn-danger">Delete Product</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Floating Message Window (Starts Hidden) -->
    <div id="floatingMessage" class="floating-message">
        <div class="floating-message-header">
            <h5 id="floatingMessageTitle">Notification</h5>
            <button type="button" class="floating-message-close" onclick="closeFloatingMessage()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="floating-message-body">
            <p id="floatingMessageContent">This is a notification message.</p>
        </div>
    </div>

    <!-- Add toast container -->
    <div class="toast-container"></div>

    <!-- Required Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Show floating message window
    function showFloatingMessage(title, message, type = 'info', duration = 5000) {
        const floatingMessage = document.getElementById('floatingMessage');
        const floatingMessageTitle = document.getElementById('floatingMessageTitle');
        const floatingMessageContent = document.getElementById('floatingMessageContent');

        // Set content
        floatingMessageTitle.textContent = title;
        floatingMessageContent.textContent = message;

        // Set type class
        floatingMessage.className = 'floating-message';
        floatingMessage.classList.add(type);

        // Show message
        floatingMessage.classList.add('show');

        // Auto-hide after duration (if not 0)
        if (duration > 0) {
            setTimeout(() => {
                closeFloatingMessage();
            }, duration);
        }
    }

    // Close floating message window
    function closeFloatingMessage() {
        const floatingMessage = document.getElementById('floatingMessage');
        floatingMessage.classList.remove('show');
    }

    // Make floating message draggable
const floatingMessage = document.getElementById('floatingMessage');
const floatingMessageHeader = document.querySelector('.floating-message-header');

let isDragging = false;
let dragOffsetX = 0;
let dragOffsetY = 0;

floatingMessageHeader.addEventListener('mousedown', (e) => {
    isDragging = true;
    floatingMessage.classList.add('dragging');
    dragOffsetX = e.clientX - floatingMessage.getBoundingClientRect().left;
    dragOffsetY = e.clientY - floatingMessage.getBoundingClientRect().top;
});

document.addEventListener('mousemove', (e) => {
    if (isDragging) {
        const x = e.clientX - dragOffsetX;
        const y = e.clientY - dragOffsetY;
        floatingMessage.style.left = `${x}px`;
        floatingMessage.style.bottom = 'auto';
        floatingMessage.style.top = `${y}px`;
        floatingMessage.style.right = 'auto';
    }
});

document.addEventListener('mouseup', () => {
    isDragging = false;
    floatingMessage.classList.remove('dragging');
});

// Function to preview image before upload
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    const container = preview.closest('.image-preview-container');

    if (input.files && input.files[0]) {
        const reader = new FileReader();

        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
            container.classList.add('has-image');
        }

        reader.readAsDataURL(input.files[0]);
    } else {
        preview.style.display = 'none';
        container.classList.remove('has-image');
    }
}

// Function to confirm deletion
function confirmDelete(id) {
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
    const confirmBtn = document.getElementById('confirmDeleteBtn');
    confirmBtn.href = `?hapus=${id}`;
    deleteModal.show();
}

// Function to open edit modal with product data
function openEditModal(id) {
    // Reset form dan tampilkan loading
    $('#editProductForm')[0].reset();
    $('#image-loading').show();
    $('#current_image').hide();

    // Show loading message
    showFloatingMessage('Loading', 'Fetching product data...', 'info', 0);

    // Ambil data produk
    $.ajax({
        url: 'get_product.php', // Keep using this file for now
        type: 'GET',
        data: { id: id, action: 'get_product' },
        success: function(response) {
            console.log('Response:', response); // Debug

            if (typeof response === 'string') {
                try {
                    response = JSON.parse(response);
                } catch(e) {
                    console.error('Parse error:', e);
                    showFloatingMessage('Error', 'Error parsing response: ' + e.message, 'error');
                    return;
                }
            }

            if (response.error) {
                showFloatingMessage('Error', response.error, 'error');
                return;
            }

            // Close loading message
            closeFloatingMessage();

            // Isi form dengan data produk
            $('#edit_id').val(response.id);
            $('#edit_nama').val(response.nama);
            $('#edit_harga').val(response.harga);

            // Tampilkan gambar produk
            if (response.gambar) {
                $('#current_image')
                    .attr('src', '{{ asset('images') }}/' + response.gambar)
                    .on('load', function() {
                        $('#image-loading').hide();
                        $(this).show();
                        $('.image-preview-container').addClass('has-image');
                    })
                    .on('error', function() {
                        $('#image-loading').hide();
                        showFloatingMessage('Warning', 'Failed to load image', 'warning');
                    });
            }

            // Tampilkan modal
            new bootstrap.Modal($('#editProductModal')).show();
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', error);
            console.error('Status:', status);
            console.error('Response:', xhr.responseText);
            showFloatingMessage('Error', 'Failed to load product data. Please try again.', 'error');
        }
    });
}

// Handle form submission
$('#editProductForm').on('submit', function(e) {
    e.preventDefault();

    let formData = new FormData(this);
    formData.append('edit_product', '1');

    // Show loading message
    showFloatingMessage('Processing', 'Updating product...', 'info', 0);

    $.ajax({
        url: 'update_product.php', // Use dedicated file for updates
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            'X-Requested-With': 'XMLHttpRequest' // Add this header to identify AJAX requests
        },
        success: function(response) {
            try {
                console.log('Raw response:', response); // Debug

                if (typeof response === 'string') {
                    response = JSON.parse(response);
                }

                if (response.success) {
                    // Show success message
                    showFloatingMessage('Success', 'Product updated successfully!', 'success');

                    // Close modal
                    $('#editProductModal').modal('hide');

                    // Reload page after a short delay
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    // Show error message
                    showFloatingMessage('Error', response.message || 'Failed to update product', 'error');
                }
            } catch(e) {
                console.error('Parse error:', e);
                showFloatingMessage('Error', 'Failed to process server response: ' + e.message, 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', error);
            console.error('Status:', status);
            console.error('Response:', xhr.responseText);
            showFloatingMessage('Error', 'Failed to connect to server. Please try again.', 'error');
        }
    });
});

// Add this helper function to show floating messages
function showToast(message, type) {
    showFloatingMessage('Notification', message, type);
}

// Show message if exists (PHP generated)
<?php if (isset($message)): ?>
showToast('Notification', '<?php echo $message; ?>', '<?php echo $messageType; ?>');
<?php endif; ?>
</script>
</body>
</html>