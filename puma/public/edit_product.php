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
$product = null;

// Check if ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: view_products.php");
    exit;
}

$id = $_GET['id'];

try {
    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    // Check if form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
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
            
            // Get current product image
            $stmt = $conn->prepare("SELECT gambar FROM produk WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($row = $result->fetch_assoc()) {
                $oldImage = $row['gambar'];
                
                // Generate unique filename
                $newFileName = uniqid("product_", true) . "." . $imageFileType;
                $targetFile = $uploadDir . $newFileName;
                
                // Move uploaded file
                if (move_uploaded_file($fileTmpPath, $targetFile)) {
                    // Delete old image if it exists
                    if ($oldImage != 'default.jpg' && file_exists($uploadDir . $oldImage)) {
                        unlink($uploadDir . $oldImage);
                    }
                    
                    // Update product with new image
                    $stmt = $conn->prepare("UPDATE produk SET nama = ?, harga = ?, gambar = ? WHERE id = ?");
                    $stmt->bind_param("sisi", $nama, $harga, $newFileName, $id);
                } else {
                    throw new Exception("Error uploading file. Check folder permissions.");
                }
            } else {
                throw new Exception("Product not found.");
            }
        } else {
            // Update product without changing image
            $stmt = $conn->prepare("UPDATE produk SET nama = ?, harga = ? WHERE id = ?");
            $stmt->bind_param("sii", $nama, $harga, $id);
        }
        
        // Execute update
        if ($stmt->execute()) {
            $message = "Product updated successfully!";
            $messageType = "success";
        } else {
            throw new Exception("Error updating product: " . $stmt->error);
        }
    }
    
    // Get product data
    $stmt = $conn->prepare("SELECT * FROM produk WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
    } else {
        throw new Exception("Product not found.");
    }
    
} catch (Exception $e) {
    $message = "Error: " . $e->getMessage();
    $messageType = "danger";
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }
        .form-label {
            font-weight: 500;
        }
        .preview-container {
            margin-top: 20px;
            text-align: center;
        }
        #imagePreview {
            max-width: 100%;
            max-height: 300px;
            border-radius: 5px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .current-image-container {
            text-align: center;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Edit Product</h1>
            <a href="view_products.php" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to Products
            </a>
        </div>
        
        <?php if (!empty($message)): ?>
            <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                <?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <?php if ($product): ?>
            <form method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="nama" class="form-label">Product Name</label>
                            <input type="text" class="form-control" id="nama" name="nama" value="<?php echo htmlspecialchars($product['nama']); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="harga" class="form-label">Price (Rp)</label>
                            <input type="number" class="form-control" id="harga" name="harga" value="<?php echo $product['harga']; ?>" required>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Current Image</label>
                            <div class="current-image-container">
                                <img src="images/<?php echo $product['gambar']; ?>" alt="<?php echo $product['nama']; ?>" id="currentImage" class="img-fluid" style="max-height: 200px; border-radius: 5px;">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="gambar" class="form-label">Change Image (Optional)</label>
                            <input type="file" class="form-control" id="gambar" name="gambar" accept="image/*" onchange="previewImage()">
                            <div class="form-text">Leave empty to keep current image</div>
                        </div>
                        
                        <div class="preview-container" style="display: none;">
                            <label class="form-label">New Image Preview</label>
                            <img id="imagePreview" src="#" alt="New Image Preview">
                        </div>
                    </div>
                </div>
                
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                    <a href="view_products.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        <?php else: ?>
            <div class="alert alert-danger">
                Product not found. <a href="view_products.php">Return to product list</a>.
            </div>
        <?php endif; ?>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function previewImage() {
            const file = document.getElementById('gambar').files[0];
            const preview = document.getElementById('imagePreview');
            const previewContainer = document.querySelector('.preview-container');
            
            if (file) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    previewContainer.style.display = 'block';
                }
                
                reader.readAsDataURL(file);
            } else {
                previewContainer.style.display = 'none';
            }
        }
    </script>
</body>
</html>
