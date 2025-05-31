<?php
// checkout.php - Order Processing System
session_start();
require_once 'dbcontroller.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$db = new DBController();
$user_id = $_SESSION['user_id'];

// Get customer info
$customer = $db->fetchOne("SELECT * FROM pelanggan WHERE id_user = ?", [$user_id]);

// Get cart items
$cart_items = $db->fetchAll("
    SELECT c.*, m.nama_menu, m.harga, m.gambar 
    FROM cart c 
    JOIN menu m ON c.id_menu = m.id_menu 
    WHERE c.id_user = ?
", [$user_id]);

if (empty($cart_items)) {
    header("Location: cart.php");
    exit();
}

// Calculate total
$total = 0;
foreach ($cart_items as $item) {
    $total += $item['harga'] * $item['jumlah'];
}

$success = '';
$error = '';

// Process order
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    $metode_pembayaran = $_POST['metode_pembayaran'];
    $catatan = trim($_POST['catatan']);
    
    // Start transaction
    $db->getConnection()->begin_transaction();
    
    try {
        // Insert order
        $stmt = $db->prepare("INSERT INTO orders (id_pelanggan, total_harga, metode_pembayaran, catatan) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("idss", $customer['id_pelanggan'], $total, $metode_pembayaran, $catatan);
        $stmt->execute();
        
        $order_id = $db->getLastInsertId();
        
        // Insert order details
        foreach ($cart_items as $item) {
            $subtotal = $item['harga'] * $item['jumlah'];
            $stmt = $db->prepare("INSERT INTO order_detail (id_order, id_menu, harga_satuan, jumlah, subtotal) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("iidid", $order_id, $item['id_menu'], $item['harga'], $item['jumlah'], $subtotal);
            $stmt->execute();
        }
        
        // Clear cart
        $db->execute("DELETE FROM cart WHERE id_user = ?", [$user_id]);
        
        $db->getConnection()->commit();
        
        // Redirect to success page
        header("Location: order_success.php?order_id=" . $order_id);
        exit();
        
    } catch (Exception $e) {
        $db->getConnection()->rollback();
        $error = "Terjadi kesalahan saat memproses pesanan. Silakan coba lagi.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Restoran Nusantara</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .order-item {
            border-bottom: 1px solid #e0e0e0;
            padding: 10px 0;
        }
        .order-item:last-child {
            border-bottom: none;
        }
        .payment-method {
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: all 0.3s;
        }
        .payment-method:hover {
            border-color: #0d6efd;
        }
        .payment-method.active {
            border-color: #0d6efd;
            background-color: #f8f9ff;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">🍽️ Restoran Nusantara</a>
            
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="cart.php">← Kembali ke Keranjang</a>
                <a class="nav-link" href="login.php?logout=1">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        
        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-shipping-fast"></i> Informasi Pengiriman</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Nama Penerima</h6>
                                <p><?= htmlspecialchars($customer['nama_pelanggan']) ?></p>
                            </div>
                            <div class="col-md-6">
                                <h6>No. Telepon</h6>
                                <p><?= htmlspecialchars($customer['no_telp']) ?></p>
                            </div>
                        </div>
                        <h6>Alamat Pengiriman</h6>
                        <p><?= htmlspecialchars($customer['alamat']) ?></p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-credit-card"></i> Metode Pembayaran</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" id="checkoutForm">
                            <div class="payment-methods">
                                <div class="payment-method" onclick="selectPayment('cash')">
                                    <input type="radio" name="metode_pembayaran" value="cash" id="cash" required>
                                    <label for="cash" class="d-flex align-items-center">
                                        <i class="fas fa-money-bill-wave fa-2x text-success me-3"></i>
                                        <div>
                                            <h6 class="mb-1">Bayar Tunai</h6>
                                            <small class="text-muted">Bayar saat makanan diantar</small>
                                        </div>
                                    </label>
                                </div>

                                <div class="payment-method" onclick="selectPayment('transfer')">
                                    <input type="radio" name="metode_pembayaran" value="transfer" id="transfer" required>
                                    <label for="transfer" class="d-flex align-items-center">
                                        <i class="fas fa-university fa-2x text-primary me-3"></i>
                                        <div>
                                            <h6 class="mb-1">Transfer Bank</h6>
                                            <small class="text-muted">Transfer ke rekening restoran</small>
                                        </div>
                                    </label>
                                </div>

                                <div class="payment-method" onclick="selectPayment('gopay')">
                                    <input type="radio" name="metode_pembayaran" value="gopay" id="gopay" required>
                                    <label for="gopay" class="d-flex align-items-center">
                                        <i class="fas fa-mobile-alt fa-2x text-info me-3"></i>
                                        <div>
                                            <h6 class="mb-1">GoPay</h6>
                                            <small class="text-muted">Bayar dengan GoPay</small>
                                        </div>
                                    </label>
                                </div>

                                <div class="payment-method" onclick="selectPayment('ovo')">
                                    <input type="radio" name="metode_pembayaran" value="ovo" id="ovo" required>
                                    <label for="ovo" class="d-flex align-items-center">
                                        <i class="fas fa-wallet fa-2x text-warning me-3"></i>
                                        <div>
                                            <h6 class="mb-1">OVO</h6>
                                            <small class="text-muted">Bayar dengan OVO</small>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <div class="mt-4">
                                <label for="catatan" class="form-label">Catatan Pesanan (Opsional)</label>
                                <textarea class="form-control" id="catatan" name="catatan" rows="3" placeholder="Tambahkan catatan khusus untuk pesanan Anda..."></textarea>
                            </div>

                            <div class="mt-4">
                                <button type="submit" name="place_order" class="btn btn-success btn-lg w-100">
                                    <i class="fas fa-check-circle"></i> Buat Pesanan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-receipt"></i> Detail Pesanan</h5>
                    </div>
                    <div class="card-body">
                        <?php foreach ($cart_items as $item): ?>
                            <div class="order-item">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="mb-1"><?= htmlspecialchars($item['nama_menu']) ?></h6>
                                        <small class="text-muted"><?= $item['jumlah'] ?>x Rp <?= number_format($item['harga'], 0, ',', '.') ?></small>
                                    </div>
                                    <strong>Rp <?= number_format($item['harga'] * $item['jumlah'], 0, ',', '.') ?></strong>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        
                        <hr>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal</span>
                            <span>Rp <?= number_format($total, 0, ',', '.') ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Biaya Pengiriman</span>
                            <span>Gratis</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <strong>Total</strong>
                            <strong class="text-primary">Rp <?= number_format($total, 0, ',', '.') ?></strong>
                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-body">
                        <h6><i class="fas fa-info-circle"></i> Informasi Penting</h6>
                        <ul class="list-unstyled small">
                            <li><i class="fas fa-clock text-primary"></i> Estimasi pengiriman: 30-45 menit</li>
                            <li><i class="fas fa-shield-alt text-success"></i> Pesanan dijamin aman</li>
                            <li><i class="fas fa-phone text-info"></i> Hubungi kami jika ada kendala</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function selectPayment(method) {
            // Remove active class from all payment methods
            document.querySelectorAll('.payment-method').forEach(el => {
                el.classList.remove('active');
            });
            
            // Add active class to selected method
            document.querySelector(`input[value="${method}"]`).closest('.payment-method').classList.add('active');
            
            // Check the radio button
            document.getElementById(method).checked = true;
        }
    </script>
</body>
</html>