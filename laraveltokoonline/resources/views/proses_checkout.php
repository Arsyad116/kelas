<?php
// proses_checkout.php
header('Content-Type: application/json');

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dbpuma";

// Create connection
try {
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    // Get and decode the JSON data
    $input = json_decode(file_get_contents('php://input'), true);

    if (!$input || !isset($input['items']) || !isset($input['total'])) {
        throw new Exception("Invalid input data");
    }

    // Log the received data for debugging
    error_log("Received checkout data: " . json_encode($input));

    // Start transaction
    $conn->begin_transaction();

    try {
        // Check if tables exist, create them if they don't
        $conn->query("CREATE TABLE IF NOT EXISTS pesanan (
            id INT(11) AUTO_INCREMENT PRIMARY KEY,
            total_harga DECIMAL(10,2) NOT NULL,
            tanggal DATETIME NOT NULL,
            nama_barang TEXT NOT NULL,
            status VARCHAR(50) DEFAULT 'pending'
        )");

        $conn->query("CREATE TABLE IF NOT EXISTS detail_pesanan (
            id INT(11) AUTO_INCREMENT PRIMARY KEY,
            id_pesanan INT(11) NOT NULL,
            nama_produk VARCHAR(255) NOT NULL,
            harga DECIMAL(10,2) NOT NULL,
            quantity INT(11) NOT NULL,
            FOREIGN KEY (id_pesanan) REFERENCES pesanan(id) ON DELETE CASCADE
        )");

        // Extract nama_barang from items
        $nama_barang = '';
        foreach ($input['items'] as $item) {
            $nama_barang .= $item['nama_produk'] . ' (x' . $item['quantity'] . '), ';
        }
        $nama_barang = rtrim($nama_barang, ', ');

        // Insert into pesanan table
        $sql_order = "INSERT INTO pesanan (total_harga, tanggal, nama_barang) VALUES (?, NOW(), ?)";
        $stmt_order = $conn->prepare($sql_order);
        $stmt_order->bind_param("ds", $input['total'], $nama_barang);

        if (!$stmt_order->execute()) {
            throw new Exception("Error inserting order: " . $stmt_order->error);
        }

        $order_id = $stmt_order->insert_id;

        // Insert into detail_pesanan table
        $sql_detail = "INSERT INTO detail_pesanan (id_pesanan, nama_produk, harga, quantity) VALUES (?, ?, ?, ?)";
        $stmt_detail = $conn->prepare($sql_detail);

        foreach ($input['items'] as $item) {
            $stmt_detail->bind_param("isdi",
                $order_id,
                $item['nama_produk'],
                $item['harga'],
                $item['quantity']
            );

            if (!$stmt_detail->execute()) {
                throw new Exception("Error inserting order detail: " . $stmt_detail->error);
            }
        }

        // If we get here, commit the transaction
        $conn->commit();

        echo json_encode([
            'success' => true,
            'message' => 'Pesanan berhasil disimpan ke database',
            'order_id' => $order_id
        ]);

    } catch (Exception $e) {
        // Something went wrong, rollback!
        $conn->rollback();
        throw $e;
    }

} catch (Exception $e) {
    error_log("Checkout error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

// Close connections
if (isset($stmt_order)) $stmt_order->close();
if (isset($stmt_detail)) $stmt_detail->close();
if (isset($conn)) $conn->close();
?>
