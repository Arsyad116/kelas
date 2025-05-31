<?php
// order_success.php - Order Success Page
session_start();
require_once 'dbcontroller.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['order_id'])) {
    header("Location: login.php");
    exit();
}

$db = new DBController();
$order_id = $_GET['order_id'];
$user_id = $_SESSION['user_id'];

// Get order details with verification
$order = $db->fetchOne("
    SELECT o.*, p.nama_pelanggan, p.alamat, p.no_telp 
    FROM orders o 
    JOIN pelanggan p ON o.id_pelanggan = p.id_pelanggan 
    WHERE o.id_order = ? AND p.id_user = ?
", [$order_id, $user_id]);

if (!$order) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Berhasil - Restoran Nusantara</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .success-animation {
            animation: bounceIn 1s ease-in-out;
        }
        @keyframes bounceIn {
            0% { transform: scale(0); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">🍽️ Restoran Nusantara</a>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card text-center">
                    <div class="card-body py-5">
                        <div class="success-animation">
                            <i class="fas fa-check-circle fa-5x text-success mb-4"></i>
                        </div>
                        
                        <h2 class="text-success mb-3">Pesanan Berhasil Dibuat!</h2>
                        <p class="lead">Terima kasih telah memesan di Restoran Nusantara</p>
                        
                        <div class="alert alert-info">
                            <h5>Detail Pesanan</h5>
                            <p class="mb-1"><strong>No. Pesanan:</strong> #<?= $order['id_order'] ?></p>
                            <p class="mb-1"><strong>Total:</strong> Rp <?= number_format($order['total_harga'], 0, ',', '.') ?></p>
                            <p class="mb-1"><strong>Metode Pembayaran:</strong> <?= ucfirst($order['metode_pembayaran']) ?></p>
                            <p class="mb-0"><strong>Status:</strong> <span class="badge bg-warning">Pending</span></p>
                        </div>
                        
                        <div class="alert alert-warning">
                            <i class="fas fa-clock"></i> <strong>Estimasi Waktu Pengiriman: 30-45 menit</strong>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-block">
                            <a href="orderdetail.php?id=<?= $order['id_order'] ?>" class="btn btn-primary">
                                <i class="fas fa-receipt"></i> Lihat Detail Pesanan
                            </a>
                            <a href="histori.php" class="btn btn-outline-primary">
                                <i class="fas fa-history"></i> Histori Pesanan
                            </a>
                            <a href="index.php" class="btn btn-outline-secondary">
                                <i class="fas fa-home"></i> Kembali ke Menu
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// histori.php - Order History
session_start();
require_once 'dbcontroller.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$db = new DBController();
$user_id = $_SESSION['user_id'];

// Get customer orders
$orders = $db