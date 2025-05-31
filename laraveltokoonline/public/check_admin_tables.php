<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dbpuma";

echo "<h2>Admin Tables Check</h2>";

try {
    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    echo "<p>Connected to database successfully.</p>";
    
    // Check pesanan table
    $result = $conn->query("SHOW TABLES LIKE 'pesanan'");
    if ($result->num_rows > 0) {
        echo "<p>Table 'pesanan' exists.</p>";
        
        // Show structure
        $result = $conn->query("DESCRIBE pesanan");
        echo "<h3>Structure of 'pesanan' table:</h3>";
        echo "<table border='1'><tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            foreach ($row as $key => $value) {
                echo "<td>" . htmlspecialchars($value ?? 'NULL') . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
        
        // Show data
        $result = $conn->query("SELECT * FROM pesanan ORDER BY id DESC LIMIT 10");
        if ($result->num_rows > 0) {
            echo "<h3>Recent orders in 'pesanan' table:</h3>";
            echo "<table border='1'>";
            
            // Headers
            echo "<tr>";
            $fields = $result->fetch_fields();
            foreach ($fields as $field) {
                echo "<th>" . htmlspecialchars($field->name) . "</th>";
            }
            echo "</tr>";
            
            // Data
            $result->data_seek(0);
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                foreach ($row as $value) {
                    echo "<td>" . htmlspecialchars($value ?? 'NULL') . "</td>";
                }
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No data in 'pesanan' table.</p>";
        }
    } else {
        echo "<p>Table 'pesanan' does not exist.</p>";
    }
    
    // Check pesanan_item table
    $result = $conn->query("SHOW TABLES LIKE 'pesanan_item'");
    if ($result->num_rows > 0) {
        echo "<p>Table 'pesanan_item' exists.</p>";
        
        // Show structure
        $result = $conn->query("DESCRIBE pesanan_item");
        echo "<h3>Structure of 'pesanan_item' table:</h3>";
        echo "<table border='1'><tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            foreach ($row as $key => $value) {
                echo "<td>" . htmlspecialchars($value ?? 'NULL') . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
        
        // Show data
        $result = $conn->query("SELECT * FROM pesanan_item ORDER BY id DESC LIMIT 10");
        if ($result->num_rows > 0) {
            echo "<h3>Recent items in 'pesanan_item' table:</h3>";
            echo "<table border='1'>";
            
            // Headers
            echo "<tr>";
            $fields = $result->fetch_fields();
            foreach ($fields as $field) {
                echo "<th>" . htmlspecialchars($field->name) . "</th>";
            }
            echo "</tr>";
            
            // Data
            $result->data_seek(0);
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                foreach ($row as $value) {
                    echo "<td>" . htmlspecialchars($value ?? 'NULL') . "</td>";
                }
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No data in 'pesanan_item' table.</p>";
        }
    } else {
        echo "<p>Table 'pesanan_item' does not exist.</p>";
    }
    
    // Check produk table
    $result = $conn->query("SHOW TABLES LIKE 'produk'");
    if ($result->num_rows > 0) {
        echo "<p>Table 'produk' exists.</p>";
        
        // Show structure
        $result = $conn->query("DESCRIBE produk");
        echo "<h3>Structure of 'produk' table:</h3>";
        echo "<table border='1'><tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            foreach ($row as $key => $value) {
                echo "<td>" . htmlspecialchars($value ?? 'NULL') . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
        
        // Show data
        $result = $conn->query("SELECT * FROM produk ORDER BY id DESC LIMIT 10");
        if ($result->num_rows > 0) {
            echo "<h3>Recent products in 'produk' table:</h3>";
            echo "<table border='1'>";
            
            // Headers
            echo "<tr>";
            $fields = $result->fetch_fields();
            foreach ($fields as $field) {
                echo "<th>" . htmlspecialchars($field->name) . "</th>";
            }
            echo "</tr>";
            
            // Data
            $result->data_seek(0);
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                foreach ($row as $value) {
                    echo "<td>" . htmlspecialchars($value ?? 'NULL') . "</td>";
                }
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No data in 'produk' table.</p>";
        }
    } else {
        echo "<p>Table 'produk' does not exist.</p>";
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
