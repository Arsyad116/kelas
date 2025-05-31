<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dbpuma";

echo "<h2>Test Checkout to Database</h2>";

try {
    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    echo "<p>Connected to database successfully.</p>";
    
    // Check if tables exist, create them if they don't
    $conn->query("CREATE TABLE IF NOT EXISTS pesanan (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        total_harga DECIMAL(10,2) NOT NULL,
        tanggal DATETIME NOT NULL,
        nama_barang TEXT NOT NULL,
        status VARCHAR(50) DEFAULT 'pending'
    )");
    
    echo "<p>Pesanan table checked/created.</p>";
    
    // Create a test order
    $total_harga = 150000;
    $nama_barang = "Test Product (x1)";
    $status = "Baru";
    
    // Insert into pesanan table
    $sql_order = "INSERT INTO pesanan (total_harga, tanggal, nama_barang, status) VALUES (?, NOW(), ?, ?)";
    $stmt_order = $conn->prepare($sql_order);
    $stmt_order->bind_param("dss", $total_harga, $nama_barang, $status);
    
    if ($stmt_order->execute()) {
        $order_id = $stmt_order->insert_id;
        echo "<p style='color:green'>Test order inserted successfully! Order ID: $order_id</p>";
        
        // Show all orders in the database
        $result = $conn->query("SELECT * FROM pesanan ORDER BY id DESC");
        
        if ($result->num_rows > 0) {
            echo "<h3>All Orders in Database:</h3>";
            echo "<table border='1'>";
            echo "<tr><th>ID</th><th>Total Harga</th><th>Tanggal</th><th>Nama Barang</th><th>Status</th></tr>";
            
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                echo "<td>Rp " . number_format($row['total_harga'], 0, ',', '.') . "</td>";
                echo "<td>" . htmlspecialchars($row['tanggal']) . "</td>";
                echo "<td>" . htmlspecialchars($row['nama_barang']) . "</td>";
                echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                echo "</tr>";
            }
            
            echo "</table>";
        } else {
            echo "<p>No orders found in the database.</p>";
        }
    } else {
        throw new Exception("Error inserting test order: " . $stmt_order->error);
    }
    
} catch (Exception $e) {
    echo "<p style='color:red'>Error: " . $e->getMessage() . "</p>";
} finally {
    if (isset($stmt_order)) $stmt_order->close();
    if (isset($conn)) $conn->close();
    echo "<p>Database connection closed.</p>";
}
?>
