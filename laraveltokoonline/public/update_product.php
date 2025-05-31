<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set headers for AJAX response
header('Content-Type: application/json');

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dbpuma";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => "Connection failed: " . $conn->connect_error]));
}

// Handle AJAX product update
if (isset($_POST['edit_product']) && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $nama = $_POST['nama'];
    $harga = intval($_POST['harga']);
    $response = ['success' => false];

    try {
        // Check if there's a new image
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
            
            // Move uploaded file
            if (move_uploaded_file($fileTmpPath, $targetFile)) {
                // Update product with new image
                $stmt = $conn->prepare("UPDATE produk SET nama=?, harga=?, gambar=? WHERE id=?");
                $stmt->bind_param("sisi", $nama, $harga, $newFileName, $id);
            } else {
                throw new Exception("Error uploading file. Check folder permissions.");
            }
        } else {
            // Update product without changing image
            $stmt = $conn->prepare("UPDATE produk SET nama=?, harga=? WHERE id=?");
            $stmt->bind_param("sii", $nama, $harga, $id);
        }
        
        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = "Product updated successfully!";
        } else {
            throw new Exception("Error updating product: " . $stmt->error);
        }
        
        $stmt->close();
    } catch (Exception $e) {
        $response['message'] = $e->getMessage();
    }
    
    echo json_encode($response);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request parameters']);
}

$conn->close();
?>
