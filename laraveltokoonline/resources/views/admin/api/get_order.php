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
    echo json_encode(['error' => 'Connection failed: ' . $conn->connect_error]);
    exit;
}

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode(['error' => 'Order ID is required']);
    exit;
}

$orderId = intval($_GET['id']);

// Get order details
$stmt = $conn->prepare("SELECT *, nama_barang FROM pesanan WHERE id = ?");
$stmt->bind_param("i", $orderId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['error' => 'Order not found']);
    exit;
}

$order = $result->fetch_assoc();
$stmt->close();

// Get order items
$stmt = $conn->prepare("SELECT pi.*, p.nama, p.gambar FROM detail_pesanan pi 
                        LEFT JOIN produk p ON pi.produk_id = p.id 
                        WHERE pi.pesanan_id = ?");
$stmt->bind_param("i", $orderId);
$stmt->execute();
$itemsResult = $stmt->get_result();

$items = [];
while ($item = $itemsResult->fetch_assoc()) {
    $items[] = $item;
}
$stmt->close();

// Format order data
$orderData = [
    'id' => $order['id'],
    'tanggal' => date('d M Y', strtotime($order['tanggal'])),
    'total' => $order['total'],
    'status' => $order['status'],
    'items' => $items
];

// Return order data as JSON
echo json_encode($orderData);

// Close connection
$conn->close();
