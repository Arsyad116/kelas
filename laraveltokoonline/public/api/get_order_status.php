<?php
// File ini hanya untuk kompatibilitas dengan AJAX di frontend
// Sebenarnya kita sudah punya route Laravel untuk ini

// Set header untuk JSON
header('Content-Type: application/json');

// Koneksi ke database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dbpuma";

// Buat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Periksa koneksi
if ($conn->connect_error) {
    die(json_encode(['error' => "Connection failed: " . $conn->connect_error]));
}

// Ambil ID pesanan
if (!isset($_GET['id'])) {
    echo json_encode(['error' => 'Order ID is required']);
    exit;
}

$orderId = intval($_GET['id']);

try {
    // Ambil status pesanan
    $query = "SELECT status FROM pesanan WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $orderId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(['error' => 'Order not found']);
        exit;
    }

    $row = $result->fetch_assoc();
    echo json_encode(['status' => $row['status'] ?? 'Baru']);
} catch (Exception $e) {
    echo json_encode(['error' => 'Error processing request: ' . $e->getMessage()]);
}

// Tutup koneksi
$conn->close();
