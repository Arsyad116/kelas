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

// Cek apakah ada request POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form
    $tanggal = isset($_POST['tanggal']) ? $_POST['tanggal'] : date('Y-m-d');
    $total = isset($_POST['total']) ? intval($_POST['total']) : 0;
    $status = isset($_POST['status']) ? $_POST['status'] : 'Baru';
    
    // Upload gambar jika ada
    $gambar = null;
    if (isset($_FILES['order_gambar']) && $_FILES['order_gambar']['error'] === 0) {
        $uploadDir = "../uploads/";
        
        // Buat direktori jika belum ada
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $fileName = basename($_FILES['order_gambar']['name']);
        $newFileName = uniqid('order_', true) . '_' . $fileName;
        $targetFile = $uploadDir . $newFileName;
        
        if (move_uploaded_file($_FILES['order_gambar']['tmp_name'], $targetFile)) {
            $gambar = $newFileName;
        }
    }
    
    // Simpan pesanan ke database
    $stmt = $conn->prepare("INSERT INTO pesanan (tanggal, total_harga, status, order_gambar) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("siss", $tanggal, $total, $status, $gambar);
    
    if ($stmt->execute()) {
        $orderId = $conn->insert_id;
        
        // Simpan item pesanan jika ada
        if (isset($_POST['products']) && is_array($_POST['products'])) {
            foreach ($_POST['products'] as $productId) {
                $productId = intval($productId);
                
                // Ambil harga produk
                $productQuery = "SELECT harga FROM produk WHERE id = ?";
                $productStmt = $conn->prepare($productQuery);
                $productStmt->bind_param("i", $productId);
                $productStmt->execute();
                $productResult = $productStmt->get_result();
                
                if ($productResult->num_rows > 0) {
                    $product = $productResult->fetch_assoc();
                    $harga = $product['harga'];
                    
                    // Simpan item pesanan
                    $itemStmt = $conn->prepare("INSERT INTO pesanan_item (pesanan_id, produk_id, quantity, harga) VALUES (?, ?, 1, ?)");
                    $itemStmt->bind_param("iii", $orderId, $productId, $harga);
                    $itemStmt->execute();
                }
            }
        }
        
        // Redirect ke halaman orders
        header("Location: ../orders.php?success=1");
        exit;
    } else {
        // Redirect dengan pesan error
        header("Location: ../orders.php?error=" . urlencode($stmt->error));
        exit;
    }
}

// Jika bukan POST request, redirect ke halaman orders
header("Location: ../orders.php");
exit;
