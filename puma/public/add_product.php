<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dbpuma";

echo "<h2>Add New Product</h2>";

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Check connection
        if ($conn->connect_error) {
            throw new Exception("Connection failed: " . $conn->connect_error);
        }

        echo "<p>Connected to database successfully.</p>";

        // Get form data
        $nama = $_POST['nama'];
        $harga = $_POST['harga'];

        // Handle file upload
        if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
            $uploadDir = "../public/images/";

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

            // Move uploaded file
            if (move_uploaded_file($fileTmpPath, $targetFile)) {
                echo "<p>File uploaded successfully.</p>";

                // Insert product into database
                $stmt = $conn->prepare("INSERT INTO produk (nama, harga, gambar) VALUES (?, ?, ?)");
                $stmt->bind_param("sis", $nama, $harga, $newFileName);

                if ($stmt->execute()) {
                    echo "<p style='color:green'>Product added successfully!</p>";
                } else {
                    throw new Exception("Error adding product: " . $stmt->error);
                }

                $stmt->close();
            } else {
                throw new Exception("Error uploading file. Check folder permissions.");
            }
        } else {
            throw new Exception("No file uploaded or file upload error.");
        }

    } catch (Exception $e) {
        echo "<p style='color:red'>Error: " . $e->getMessage() . "</p>";
    } finally {
        if (isset($conn)) {
            $conn->close();
            echo "<p>Database connection closed.</p>";
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
    </style>
</head>
<body>
    <div class="container">
        <h1 class="mb-4">Add New Product</h1>

        <form method="POST" enctype="multipart/form-data">
            <!-- Bypass Laravel CSRF protection for direct PHP script -->
            <input type="hidden" name="_token" value="bypass_csrf">
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
                <button type="submit" class="btn btn-primary">Add Product</button>
                <a href="/admin" class="btn btn-secondary">Back to Admin</a>
            </div>
        </form>
    </div>

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
