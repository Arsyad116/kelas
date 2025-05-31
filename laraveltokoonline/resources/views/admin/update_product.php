<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dbpuma";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Connection failed: ' . $conn->connect_error]));
}

try {
    if (isset($_POST['edit_product']) && isset($_POST['id'])) {
        $id = $conn->real_escape_string($_POST['id']);
        $nama = $conn->real_escape_string($_POST['nama']);
        $harga = $conn->real_escape_string($_POST['harga']);
        
        // Check if image is uploaded
        if (!empty($_FILES['gambar']['name'])) {
            $uploadDir = "uploads/";
            $fileName = basename($_FILES['gambar']['name']);
            $targetFile = $uploadDir . $fileName;
            
            // Move uploaded file
            if (move_uploaded_file($_FILES['gambar']['tmp_name'], $targetFile)) {
                $sql = "UPDATE produk SET nama=?, harga=?, gambar=? WHERE id=?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sssi", $nama, $harga, $fileName, $id);
            } else {
                throw new Exception('Failed to upload image');
            }
        } else {
            // Update without image
            $sql = "UPDATE produk SET nama=?, harga=? WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssi", $nama, $harga, $id);
        }
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            throw new Exception('Database error: ' . $stmt->error);
        }
        
        $stmt->close();
    } else {
        throw new Exception('Missing required data');
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

$conn->close();
?>
