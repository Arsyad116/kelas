<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dbpuma";

echo "<h2>Add order_gambar Column to Pesanan Table</h2>";

try {
    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    echo "<p>Connected to database successfully.</p>";
    
    // Check if order_gambar column exists
    $result = $conn->query("SHOW COLUMNS FROM pesanan LIKE 'order_gambar'");
    
    if ($result->num_rows == 0) {
        // Add order_gambar column
        $sql = "ALTER TABLE pesanan ADD COLUMN order_gambar VARCHAR(255) NULL AFTER status";
        
        if ($conn->query($sql)) {
            echo "<p style='color:green'>Column 'order_gambar' added successfully to pesanan table.</p>";
        } else {
            throw new Exception("Error adding column: " . $conn->error);
        }
    } else {
        echo "<p>Column 'order_gambar' already exists in pesanan table.</p>";
    }
    
    // Show current structure
    $result = $conn->query("DESCRIBE pesanan");
    echo "<h3>Current structure of 'pesanan' table:</h3>";
    echo "<table border='1'><tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        foreach ($row as $key => $value) {
            echo "<td>" . htmlspecialchars($value ?? 'NULL') . "</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
    
} catch (Exception $e) {
    echo "<p style='color:red'>Error: " . $e->getMessage() . "</p>";
} finally {
    if (isset($conn)) {
        $conn->close();
        echo "<p>Database connection closed.</p>";
    }
}
?>
