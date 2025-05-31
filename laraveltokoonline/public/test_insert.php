<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dbpuma";

echo "<h2>Test Database Insert</h2>";

try {
    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    echo "<p>Connected to database successfully.</p>";
    
    // Create tables if they don't exist
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
    
    // Test insert into pesanan
    $total = 150000;
    $nama_barang = "Test Product (x1)";
    
    $sql_order = "INSERT INTO pesanan (total_harga, tanggal, nama_barang) VALUES (?, NOW(), ?)";
    $stmt_order = $conn->prepare($sql_order);
    $stmt_order->bind_param("ds", $total, $nama_barang);
    
    if ($stmt_order->execute()) {
        $order_id = $stmt_order->insert_id;
        echo "<p>Test order inserted successfully. Order ID: $order_id</p>";
        
        // Test insert into detail_pesanan
        $nama_produk = "Test Product";
        $harga = 150000;
        $quantity = 1;
        
        $sql_detail = "INSERT INTO detail_pesanan (id_pesanan, nama_produk, harga, quantity) VALUES (?, ?, ?, ?)";
        $stmt_detail = $conn->prepare($sql_detail);
        $stmt_detail->bind_param("isdi", $order_id, $nama_produk, $harga, $quantity);
        
        if ($stmt_detail->execute()) {
            echo "<p>Test order detail inserted successfully.</p>";
        } else {
            throw new Exception("Error inserting order detail: " . $stmt_detail->error);
        }
        
        $stmt_detail->close();
    } else {
        throw new Exception("Error inserting order: " . $stmt_order->error);
    }
    
    $stmt_order->close();
    
    // Show all orders
    $result = $conn->query("SELECT * FROM pesanan ORDER BY id DESC LIMIT 10");
    if ($result->num_rows > 0) {
        echo "<h3>Recent Orders:</h3>";
        echo "<table border='1'><tr><th>ID</th><th>Total</th><th>Date</th><th>Products</th><th>Status</th></tr>";
        while($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row["id"] . "</td>";
            echo "<td>Rp " . number_format($row["total_harga"], 0, ',', '.') . "</td>";
            echo "<td>" . $row["tanggal"] . "</td>";
            echo "<td>" . $row["nama_barang"] . "</td>";
            echo "<td>" . $row["status"] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No orders found.</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
} finally {
    if (isset($conn)) {
        $conn->close();
        echo "<p>Database connection closed.</p>";
    }
}
?>
