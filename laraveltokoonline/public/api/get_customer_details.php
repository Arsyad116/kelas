<?php
// File ini hanya untuk kompatibilitas dengan AJAX di frontend
// Sebenarnya kita sudah punya route Laravel untuk ini

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

// Ambil ID customer
if (!isset($_GET['id'])) {
    echo json_encode(['error' => 'Customer ID is required']);
    exit;
}

$customerId = intval($_GET['id']);

// Ambil data customer
$query = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $customerId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['error' => 'Customer not found']);
    exit;
}

$customer = $result->fetch_assoc();

// Tambahkan kolom yang mungkin tidak ada
if (!isset($customer['name'])) {
    $customer['name'] = $customer['username'] ?? $customer['nama'] ?? 'Customer ' . $customer['id'];
}

if (!isset($customer['created_at'])) {
    $customer['created_at'] = date('Y-m-d H:i:s');
}

// Kirim response
echo json_encode([
    'customer' => $customer
]);

// Tutup koneksi
$conn->close();
