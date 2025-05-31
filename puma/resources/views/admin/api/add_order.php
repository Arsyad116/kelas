<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set content type to JSON
header('Content-Type: application/json');

// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dbpuma";

// Create database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Connection failed: ' . $conn->connect_error]);
    exit;
}

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Validate required fields
if (!isset($_POST['tanggal']) || !isset($_POST['total']) || !isset($_POST['status'])) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

// Sanitize input
$tanggal = $conn->real_escape_string($_POST['tanggal']);
$total = floatval($_POST['total']);
$status = $conn->real_escape_string($_POST['status']);

// Handle image upload
$product_images = '';
if (isset($_FILES['order_gambar']) && $_FILES['order_gambar']['error'] === 0) {
    $uploadDir = '../uploads/';
    $uploadFile = $uploadDir . basename($_FILES['order_gambar']['name']);
    $imageFileType = strtolower(pathinfo($uploadFile, PATHINFO_EXTENSION));

    // Check if image file is a actual image or fake image
    $check = getimagesize($_FILES['order_gambar']['tmp_name']);
    if($check === false) {
        echo json_encode(['success' => false, 'message' => 'File is not an image.']);
        exit;
    }

    // Allow certain file formats
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
        echo json_encode(['success' => false, 'message' => 'Sorry, only JPG, JPEG, PNG & GIF files are allowed.']);
        exit;
    }

    // Generate unique filename
    $filename = uniqid() . '.' . $imageFileType;
    $uploadFile = $uploadDir . $filename;

    // Move uploaded file
    if (move_uploaded_file($_FILES['order_gambar']['tmp_name'], $uploadFile)) {
        $product_images = $filename;
    } else {
        $error = error_get_last();
        echo json_encode(['success' => false, 'message' => 'Sorry, there was an error uploading your file: ' . $error['message']]);
        exit;
    }
}

// Get nama_barang and product_images from products
$nama_barang = '';
$order_gambar_from_products = '';
if (isset($_POST['products']) && is_array($_POST['products'])) {
    $product_ids = array_map('intval', $_POST['products']);
    $product_names = [];
    $product_images_arr = [];
    $placeholders = implode(',', array_fill(0, count($product_ids), '?'));
    $sql = "SELECT nama, gambar FROM produk WHERE id IN ($placeholders)";
    $stmt_products = $conn->prepare($sql);
    $stmt_products->bind_param(str_repeat('i', count($product_ids)), ...$product_ids);
    $stmt_products->execute();
    $result_products = $stmt_products->get_result();

    while ($row_product = $result_products->fetch_assoc()) {
        $product_names[] = $row_product['nama'];
        if (!empty($row_product['gambar'])) {
            $product_images_arr[] = $row_product['gambar'];
        }
    }
    $nama_barang = implode(', ', $product_names);
    $order_gambar_from_products = implode(',', $product_images_arr);
}

// Insert new order
$stmt = $conn->prepare("INSERT INTO pesanan (tanggal, total_harga, status, nama_barang, order_gambar) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sdsss", $tanggal, $total, $status, $nama_barang, $order_gambar_from_products);

if ($stmt->execute()) {
    $orderId = $stmt->insert_id;
    echo json_encode([
        'success' => true,
        'message' => 'Order added successfully',
        'order_id' => $orderId
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to add order: ' . $stmt->error . " - SQL: " . $stmt->sqlstate
    ]);
}

$stmt->close();
$conn->close();
?>
