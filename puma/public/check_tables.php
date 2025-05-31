<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dbpuma";

echo "<h2>Database Check</h2>";

try {
    // Create connection
    $conn = new mysqli($servername, $username, $password);
    
    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    echo "<p>Connected to MySQL server successfully.</p>";
    
    // Check if database exists
    $result = $conn->query("SHOW DATABASES LIKE '$dbname'");
    if ($result->num_rows > 0) {
        echo "<p>Database '$dbname' exists.</p>";
    } else {
        echo "<p>Database '$dbname' does not exist. Creating it now...</p>";
        if ($conn->query("CREATE DATABASE IF NOT EXISTS $dbname")) {
            echo "<p>Database created successfully.</p>";
        } else {
            throw new Exception("Error creating database: " . $conn->error);
        }
    }
    
    // Select the database
    $conn->select_db($dbname);
    echo "<p>Selected database: $dbname</p>";
    
    // Create tables if they don't exist
    $tables = [
        "pesanan" => "CREATE TABLE IF NOT EXISTS pesanan (
            id INT(11) AUTO_INCREMENT PRIMARY KEY,
            total_harga DECIMAL(10,2) NOT NULL,
            tanggal DATETIME NOT NULL,
            nama_barang TEXT NOT NULL,
            status VARCHAR(50) DEFAULT 'pending'
        )",
        "detail_pesanan" => "CREATE TABLE IF NOT EXISTS detail_pesanan (
            id INT(11) AUTO_INCREMENT PRIMARY KEY,
            id_pesanan INT(11) NOT NULL,
            nama_produk VARCHAR(255) NOT NULL,
            harga DECIMAL(10,2) NOT NULL,
            quantity INT(11) NOT NULL,
            FOREIGN KEY (id_pesanan) REFERENCES pesanan(id) ON DELETE CASCADE
        )"
    ];
    
    foreach ($tables as $table => $sql) {
        // Check if table exists
        $result = $conn->query("SHOW TABLES LIKE '$table'");
        if ($result->num_rows > 0) {
            echo "<p>Table '$table' exists.</p>";
        } else {
            echo "<p>Table '$table' does not exist. Creating it now...</p>";
            if ($conn->query($sql)) {
                echo "<p>Table created successfully.</p>";
            } else {
                throw new Exception("Error creating table $table: " . $conn->error);
            }
        }
    }
    
    // Check for existing orders
    $result = $conn->query("SELECT COUNT(*) as count FROM pesanan");
    if ($result) {
        $row = $result->fetch_assoc();
        echo "<p>Number of orders in database: " . $row['count'] . "</p>";
    } else {
        echo "<p>Error checking orders: " . $conn->error . "</p>";
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
