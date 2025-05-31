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
    // Ambil data pesanan
    $orderQuery = "SELECT * FROM pesanan WHERE id = ?";
    $stmt = $conn->prepare($orderQuery);
    $stmt->bind_param("i", $orderId);
    $stmt->execute();
    $orderResult = $stmt->get_result();

    if ($orderResult->num_rows === 0) {
        echo json_encode(['error' => 'Order not found']);
        exit;
    }

    $order = $orderResult->fetch_assoc();

    // Ambil item pesanan dari detail_pesanan
    $items = [];

    // Coba ambil dari detail_pesanan dulu
    $detailQuery = "SELECT dp.*, p.nama as product_name
                FROM detail_pesanan dp
                LEFT JOIN produk p ON dp.nama_produk = p.nama
                WHERE dp.id_pesanan = ?";
    $stmt = $conn->prepare($detailQuery);
    $stmt->bind_param("i", $orderId);
    $stmt->execute();
    $detailResult = $stmt->get_result();

    if ($detailResult->num_rows > 0) {
        while ($item = $detailResult->fetch_assoc()) {
            $items[] = [
                'product_name' => $item['nama_produk'],
                'harga' => $item['harga'],
                'quantity' => $item['quantity'] ?? 1
            ];
        }
    } else {
        // Jika tidak ada di detail_pesanan, coba ambil dari pesanan_item
        $itemsQuery = "SELECT pi.*, p.nama as product_name, p.harga
                    FROM pesanan_item pi
                    LEFT JOIN produk p ON pi.produk_id = p.id
                    WHERE pi.pesanan_id = ?";
        $stmt = $conn->prepare($itemsQuery);
        $stmt->bind_param("i", $orderId);
        $stmt->execute();
        $itemsResult = $stmt->get_result();

        while ($item = $itemsResult->fetch_assoc()) {
            $items[] = [
                'product_name' => $item['product_name'] ?? 'Unknown Product',
                'harga' => $item['harga'] ?? 0,
                'quantity' => $item['quantity'] ?? 1
            ];
        }
    }

    // Jika masih tidak ada items, coba parse dari nama_barang
    if (empty($items) && !empty($order['nama_barang'])) {
        $productNames = explode(',', $order['nama_barang']);
        foreach ($productNames as $productInfo) {
            // Format: "Product Name (x2)"
            if (preg_match('/(.+)\s*\(x(\d+)\)/', trim($productInfo), $matches)) {
                $productName = trim($matches[1]);
                $quantity = intval($matches[2]);

                // Coba cari harga produk
                $priceQuery = "SELECT harga FROM produk WHERE nama LIKE ? LIMIT 1";
                $stmt = $conn->prepare($priceQuery);
                $searchName = "%" . $productName . "%";
                $stmt->bind_param("s", $searchName);
                $stmt->execute();
                $priceResult = $stmt->get_result();
                $price = 0;

                if ($priceResult->num_rows > 0) {
                    $priceRow = $priceResult->fetch_assoc();
                    $price = $priceRow['harga'];
                }

                $items[] = [
                    'product_name' => $productName,
                    'harga' => $price,
                    'quantity' => $quantity
                ];
            }
        }
    }

    // Kirim response
    echo json_encode([
        'order' => $order,
        'items' => $items
    ]);
} catch (Exception $e) {
    echo json_encode(['error' => 'Error processing request: ' . $e->getMessage()]);
}

// Tutup koneksi
$conn->close();
