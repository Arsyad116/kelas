<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dbpuma";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "<h1>Database Fix Tool</h1>";

// Check if detail_pesanan table exists
$result = $conn->query("SHOW TABLES LIKE 'detail_pesanan'");
if ($result->num_rows > 0) {
    echo "<p>Table 'detail_pesanan' exists.</p>";
    
    // Check if quantity column exists
    $result = $conn->query("SHOW COLUMNS FROM detail_pesanan LIKE 'quantity'");
    if ($result->num_rows == 0) {
        echo "<p>Column 'quantity' does not exist in 'detail_pesanan' table. Adding it now...</p>";
        
        // Add quantity column
        if ($conn->query("ALTER TABLE detail_pesanan ADD COLUMN quantity INT(11) NOT NULL DEFAULT 1")) {
            echo "<p style='color: green;'>Column 'quantity' added successfully!</p>";
        } else {
            echo "<p style='color: red;'>Error adding column 'quantity': " . $conn->error . "</p>";
        }
    } else {
        echo "<p>Column 'quantity' already exists in 'detail_pesanan' table.</p>";
    }
} else {
    echo "<p>Table 'detail_pesanan' does not exist. Creating it now...</p>";
    
    // Create detail_pesanan table
    $sql = "CREATE TABLE detail_pesanan (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        id_pesanan INT(11) NOT NULL,
        nama_produk VARCHAR(255) NOT NULL,
        harga DECIMAL(10,2) NOT NULL,
        quantity INT(11) NOT NULL,
        FOREIGN KEY (id_pesanan) REFERENCES pesanan(id) ON DELETE CASCADE
    )";
    
    if ($conn->query($sql)) {
        echo "<p style='color: green;'>Table 'detail_pesanan' created successfully!</p>";
    } else {
        echo "<p style='color: red;'>Error creating table 'detail_pesanan': " . $conn->error . "</p>";
    }
}

// Check if pesanan_item table exists
$result = $conn->query("SHOW TABLES LIKE 'pesanan_item'");
if ($result->num_rows > 0) {
    echo "<p>Table 'pesanan_item' exists.</p>";
    
    // Check if quantity column exists
    $result = $conn->query("SHOW COLUMNS FROM pesanan_item LIKE 'quantity'");
    if ($result->num_rows == 0) {
        echo "<p>Column 'quantity' does not exist in 'pesanan_item' table. Adding it now...</p>";
        
        // Add quantity column
        if ($conn->query("ALTER TABLE pesanan_item ADD COLUMN quantity INT(11) NOT NULL DEFAULT 1")) {
            echo "<p style='color: green;'>Column 'quantity' added successfully!</p>";
        } else {
            echo "<p style='color: red;'>Error adding column 'quantity': " . $conn->error . "</p>";
        }
    } else {
        echo "<p>Column 'quantity' already exists in 'pesanan_item' table.</p>";
    }
} else {
    echo "<p>Table 'pesanan_item' does not exist. Creating it now...</p>";
    
    // Create pesanan_item table
    $sql = "CREATE TABLE pesanan_item (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        pesanan_id INT(11) NOT NULL,
        produk_id INT(11) NOT NULL,
        quantity INT(11) NOT NULL,
        harga DECIMAL(10,2) NOT NULL,
        FOREIGN KEY (pesanan_id) REFERENCES pesanan(id) ON DELETE CASCADE
    )";
    
    if ($conn->query($sql)) {
        echo "<p style='color: green;'>Table 'pesanan_item' created successfully!</p>";
    } else {
        echo "<p style='color: red;'>Error creating table 'pesanan_item': " . $conn->error . "</p>";
    }
}

// Check if produk table exists
$result = $conn->query("SHOW TABLES LIKE 'produk'");
if ($result->num_rows > 0) {
    echo "<p>Table 'produk' exists.</p>";
    
    // Show structure of produk table
    $result = $conn->query("DESCRIBE produk");
    echo "<h2>Structure of 'produk' table:</h2>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['Field'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . $row['Default'] . "</td>";
        echo "<td>" . $row['Extra'] . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
} else {
    echo "<p>Table 'produk' does not exist.</p>";
}

// Show all tables in database
$result = $conn->query("SHOW TABLES");
echo "<h2>All tables in database:</h2>";
echo "<ul>";

while ($row = $result->fetch_row()) {
    echo "<li>" . $row[0] . "</li>";
}

echo "</ul>";

// Close connection
$conn->close();

echo "<p><a href='cart.php' class='btn btn-primary'>Back to Cart</a></p>";
?>
