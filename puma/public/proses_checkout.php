<?php
// proses_checkout.php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

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
    $log_file = __DIR__ . '/checkout_log.txt';
    file_put_contents($log_file, date('Y-m-d H:i:s') . " - Received checkout data: " . json_encode($input) . "\n", FILE_APPEND);
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
            status VARCHAR(50) DEFAULT 'pending',
            order_gambar VARCHAR(255) NULL
        )");

        $conn->query("CREATE TABLE IF NOT EXISTS detail_pesanan (
            id INT(11) AUTO_INCREMENT PRIMARY KEY,
            id_pesanan INT(11) NOT NULL,
            nama_produk VARCHAR(255) NOT NULL,
            harga DECIMAL(10,2) NOT NULL,
            quantity INT(11) NOT NULL,
            FOREIGN KEY (id_pesanan) REFERENCES pesanan(id) ON DELETE CASCADE
        )");

        // Create pesanan_item table for admin panel compatibility
        $conn->query("CREATE TABLE IF NOT EXISTS pesanan_item (
            id INT(11) AUTO_INCREMENT PRIMARY KEY,
            pesanan_id INT(11) NOT NULL,
            produk_id INT(11) NOT NULL,
            quantity INT(11) NOT NULL,
            harga DECIMAL(10,2) NOT NULL,
            FOREIGN KEY (pesanan_id) REFERENCES pesanan(id) ON DELETE CASCADE
        )");

        // Create produk table if it doesn't exist
        $conn->query("CREATE TABLE IF NOT EXISTS produk (
            id INT(11) AUTO_INCREMENT PRIMARY KEY,
            nama VARCHAR(255) NOT NULL,
            harga DECIMAL(10,2) NOT NULL,
            gambar VARCHAR(255) NULL,
            description TEXT NULL,
            stock INT(11) DEFAULT 0,
            category VARCHAR(100) NULL,
            is_active TINYINT(1) DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )");

        // Extract nama_barang from items
        $nama_barang = '';
        foreach ($input['items'] as $item) {
            $nama_barang .= $item['nama_produk'] . ' (x' . $item['quantity'] . '), ';
        }
        $nama_barang = rtrim($nama_barang, ', ');

        // Get product images
        $product_images = [];
        foreach ($input['items'] as $item) {
            if (!empty($item['image'])) {
                // Extract filename from image URL
                $image_url = $item['image'];
                $image_filename = '';

                // Check if it's a full URL or just a filename
                if (filter_var($image_url, FILTER_VALIDATE_URL)) {
                    $path_parts = pathinfo(parse_url($image_url, PHP_URL_PATH));
                    $image_filename = $path_parts['basename'];
                } else if (strpos($image_url, '/') !== false) {
                    $path_parts = pathinfo($image_url);
                    $image_filename = $path_parts['basename'];
                } else {
                    $image_filename = $image_url;
                }

                if (!empty($image_filename)) {
                    $product_images[] = $image_filename;
                }
            }
        }

        // Join product images with comma
        $order_gambar = !empty($product_images) ? implode(',', $product_images) : null;

        // Insert into pesanan table
        $sql_order = "INSERT INTO pesanan (total_harga, tanggal, nama_barang, status, order_gambar) VALUES (?, NOW(), ?, ?, ?)";
        $stmt_order = $conn->prepare($sql_order);
        $status = 'Baru'; // Set status to 'Baru' (New)
        $stmt_order->bind_param("dsss", $input['total'], $nama_barang, $status, $order_gambar);

        if (!$stmt_order->execute()) {
            throw new Exception("Error inserting order: " . $stmt_order->error);
        }

        $order_id = $stmt_order->insert_id;

        // Try to add quantity column if it doesn't exist
        try {
            $conn->query("ALTER TABLE detail_pesanan ADD COLUMN IF NOT EXISTS quantity INT(11) NOT NULL DEFAULT 1");
        } catch (Exception $e) {
            // Log the error but continue
            error_log("Warning: Could not add quantity column: " . $e->getMessage());
        }

        // Always use the full query with quantity
        $sql_detail = "INSERT INTO detail_pesanan (id_pesanan, nama_produk, harga, quantity) VALUES (?, ?, ?, ?)";
        $stmt_detail = $conn->prepare($sql_detail);

        // Try to add quantity column to pesanan_item if it doesn't exist
        try {
            $conn->query("ALTER TABLE pesanan_item ADD COLUMN IF NOT EXISTS quantity INT(11) NOT NULL DEFAULT 1");
        } catch (Exception $e) {
            // Log the error but continue
            error_log("Warning: Could not add quantity column to pesanan_item: " . $e->getMessage());
        }

        // Prepare statement for pesanan_item table
        $sql_item = "INSERT INTO pesanan_item (pesanan_id, produk_id, quantity, harga) VALUES (?, ?, ?, ?)";
        $stmt_item = $conn->prepare($sql_item);

        foreach ($input['items'] as $item) {
            // Insert into detail_pesanan
            $stmt_detail->bind_param("isdi",
                $order_id,
                $item['nama_produk'],
                $item['harga'],
                $item['quantity']
            );

            if (!$stmt_detail->execute()) {
                throw new Exception("Error inserting order detail: " . $stmt_detail->error);
            }

            // Try to find product ID by name
            $product_name = $item['nama_produk'];
            $find_product = $conn->prepare("SELECT id FROM produk WHERE nama LIKE ? LIMIT 1");
            $search_name = "%$product_name%";
            $find_product->bind_param("s", $search_name);
            $find_product->execute();
            $find_product->store_result();

            if ($find_product->num_rows > 0) {
                $find_product->bind_result($produk_id);
                $find_product->fetch();

                // Insert into pesanan_item with real product ID
                $stmt_item->bind_param("iiid",
                    $order_id,
                    $produk_id,
                    $item['quantity'],
                    $item['harga']
                );

                if (!$stmt_item->execute()) {
                    // Log error but continue - this is for admin panel compatibility
                    error_log("Warning: Could not insert into pesanan_item: " . $stmt_item->error);
                }
            } else {
                // If product not found, create a temporary one
                $insert_product = $conn->prepare("INSERT INTO produk (nama, harga, gambar) VALUES (?, ?, ?)");
                $insert_product->bind_param("sds", $item['nama_produk'], $item['harga'], $item['image']);

                if ($insert_product->execute()) {
                    $produk_id = $insert_product->insert_id;

                    // Insert into pesanan_item with new product ID
                    $stmt_item->bind_param("iiid",
                        $order_id,
                        $produk_id,
                        $item['quantity'],
                        $item['harga']
                    );

                    if (!$stmt_item->execute()) {
                        // Log error but continue - this is for admin panel compatibility
                        error_log("Warning: Could not insert into pesanan_item: " . $stmt_item->error);
                    }
                }

                $insert_product->close();
            }

            $find_product->close();
        }

        // If we get here, commit the transaction
        $conn->commit();

        // Double-check that the order was saved
        $check_order = $conn->query("SELECT * FROM pesanan WHERE id = $order_id");
        $order_exists = $check_order && $check_order->num_rows > 0;

        // Double-check that the order details were saved
        $check_details = $conn->query("SELECT COUNT(*) as count FROM detail_pesanan WHERE id_pesanan = $order_id");
        $details_count = 0;
        if ($check_details) {
            $details_row = $check_details->fetch_assoc();
            $details_count = $details_row['count'];
        }

        // Double-check that the pesanan_item records were saved
        $check_items = $conn->query("SELECT COUNT(*) as count FROM pesanan_item WHERE pesanan_id = $order_id");
        $items_count = 0;
        if ($check_items) {
            $items_row = $check_items->fetch_assoc();
            $items_count = $items_row['count'];
        }

        // Log the verification results
        $verification_log = "Order verification: order_exists=$order_exists, details_count=$details_count, items_count=$items_count";
        file_put_contents($log_file, date('Y-m-d H:i:s') . " - $verification_log\n", FILE_APPEND);

        echo json_encode([
            'success' => true,
            'message' => 'Pesanan berhasil disimpan ke database',
            'order_id' => $order_id,
            'verification' => [
                'order_exists' => $order_exists,
                'details_count' => $details_count,
                'items_count' => $items_count
            ]
        ]);

    } catch (Exception $e) {
        // Something went wrong, rollback!
        $conn->rollback();

        // Log inner exception
        $log_file = __DIR__ . '/checkout_log.txt';
        file_put_contents($log_file, date('Y-m-d H:i:s') . " - INNER ERROR: " . $e->getMessage() . "\n", FILE_APPEND);

        throw $e;
    }

} catch (Exception $e) {
    $error_message = "Checkout error: " . $e->getMessage();
    error_log($error_message);

    // Log to file
    $log_file = __DIR__ . '/checkout_log.txt';
    file_put_contents($log_file, date('Y-m-d H:i:s') . " - ERROR: " . $error_message . "\n", FILE_APPEND);

    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

// Close connections
if (isset($stmt_order)) $stmt_order->close();
if (isset($stmt_detail)) $stmt_detail->close();
if (isset($stmt_item)) $stmt_item->close();
if (isset($conn)) $conn->close();
?>
