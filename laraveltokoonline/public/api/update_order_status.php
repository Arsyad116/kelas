<?php
// Set header untuk JSON
header('Content-Type: application/json');

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Koneksi ke database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dbpuma";

// Buat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Periksa koneksi
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => "Connection failed: " . $conn->connect_error]));
}

// Log request untuk debugging
$log_file = __DIR__ . '/update_status_log.txt';
file_put_contents($log_file, date('Y-m-d H:i:s') . " - Request received\n", FILE_APPEND);
file_put_contents($log_file, date('Y-m-d H:i:s') . " - POST data: " . print_r($_POST, true) . "\n", FILE_APPEND);

// Periksa apakah request adalah POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Only POST method is allowed']);
    exit;
}

// Periksa apakah parameter yang diperlukan ada
if (!isset($_POST['order_id']) || !isset($_POST['status'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters: order_id and status']);
    exit;
}

// Ambil data dari request
$orderId = intval($_POST['order_id']);
$status = $_POST['status'];

// Validasi status
$validStatuses = ['Baru', 'Diproses', 'Dikirim', 'Selesai', 'Dibatalkan'];
if (!in_array($status, $validStatuses)) {
    echo json_encode(['success' => false, 'message' => 'Invalid status value']);
    exit;
}

try {
    // Update status pesanan
    $stmt = $conn->prepare("UPDATE pesanan SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $orderId);
    
    if ($stmt->execute()) {
        // Berhasil update
        echo json_encode([
            'success' => true, 
            'message' => 'Order status updated successfully',
            'order_id' => $orderId,
            'new_status' => $status
        ]);
        
        // Log success
        file_put_contents($log_file, date('Y-m-d H:i:s') . " - Status updated successfully for order #$orderId to '$status'\n", FILE_APPEND);
    } else {
        // Gagal update
        throw new Exception("Failed to update order status: " . $stmt->error);
    }
    
    $stmt->close();
} catch (Exception $e) {
    // Log error
    file_put_contents($log_file, date('Y-m-d H:i:s') . " - Error: " . $e->getMessage() . "\n", FILE_APPEND);
    
    // Kirim response error
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

// Tutup koneksi
$conn->close();
?>
