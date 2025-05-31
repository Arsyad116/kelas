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

// Debug information
echo "<div style='background-color: #f8f9fa; padding: 10px; margin-bottom: 20px; border-radius: 5px;'>";
echo "<h3>Debug Information</h3>";
echo "<p>Request Method: " . $_SERVER["REQUEST_METHOD"] . "</p>";

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Debug POST data
        echo "<h4>POST Data:</h4>";
        echo "<pre>";
        print_r($_POST);
        echo "</pre>";

        // Debug FILES data
        echo "<h4>FILES Data:</h4>";
        echo "<pre>";
        print_r($_FILES);
        echo "</pre>";
        echo "</div>";
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
            $newFileName = uniqid("product_", true) . "." . $imageFileType;
            $targetFile = $uploadDir . $newFileName;

            // Check if directory is writable
            if (!is_writable($uploadDir)) {
                throw new Exception("Directory is not writable. Current permissions: " . substr(sprintf('%o', fileperms($uploadDir)), -4));
            }

            // Try to move the file
            echo "<p>Attempting to move file from {$fileTmpPath} to {$targetFile}</p>";

            if (move_uploaded_file($fileTmpPath, $targetFile)) {
                echo "<p style='color:green'>File moved successfully!</p>";

                // Insert product into database
                $stmt = $conn->prepare("INSERT INTO produk (nama, harga, gambar) VALUES (?, ?, ?)");
                $stmt->bind_param("sis", $nama, $harga, $newFileName);

                if ($stmt->execute()) {
                    $message = "Product added successfully!";
                    $messageType = "success";
                    echo "<p style='color:green'>Product added to database!</p>";
                } else {
                    throw new Exception("Error adding product: " . $stmt->error);
                }

                $stmt->close();
            } else {
                $error = error_get_last();
                throw new Exception("Error uploading file: " . ($error ? $error['message'] : 'Unknown error') . ". Check folder permissions.");
            }
        } else {
            throw new Exception("No file uploaded or file upload error: " . $_FILES['gambar']['error']);
        }

    } catch (Exception $e) {
        $message = "Error: " . $e->getMessage();
        $messageType = "danger";
    } finally {
        if (isset($conn)) {
            $conn->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Product</title>
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
            display: none;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Add New Product</h1>
            <a href="/" class="btn btn-outline-secondary">
                <i class="fas fa-home"></i> Back to Home
            </a>
        </div>

        <?php if (!empty($message)): ?>
            <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                <?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

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
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="gambar" class="form-label">Product Image</label>
                        <input type="file" class="form-control" id="gambar" name="gambar" accept="image/*" required onchange="previewImage()">
                    </div>

                    <div class="preview-container">
                        <img id="imagePreview" src="#" alt="Image Preview">
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-plus-circle"></i> Add Product
                </button>
                <a href="/admin" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Admin
                </a>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function previewImage() {
            const file = document.getElementById('gambar').files[0];
            const preview = document.getElementById('imagePreview');

            if (file) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }

                reader.readAsDataURL(file);
            }
        }
    </script>
</body>
</html>
