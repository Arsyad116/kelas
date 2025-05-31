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

echo "<h1>Database Fix Tool - Quantity Column</h1>";

// Function to check if table exists
function tableExists($conn, $tableName) {
    $result = $conn->query("SHOW TABLES LIKE '$tableName'");
    return $result->num_rows > 0;
}

// Function to check if column exists
function columnExists($conn, $tableName, $columnName) {
    $result = $conn->query("SHOW COLUMNS FROM $tableName LIKE '$columnName'");
    return $result->num_rows > 0;
}

// Function to add column if it doesn't exist
function addColumnIfNotExists($conn, $tableName, $columnName, $columnDefinition) {
    if (!columnExists($conn, $tableName, $columnName)) {
        $sql = "ALTER TABLE $tableName ADD COLUMN $columnName $columnDefinition";
        if ($conn->query($sql)) {
            echo "<p style='color: green;'>Column '$columnName' added to table '$tableName' successfully!</p>";
        } else {
            echo "<p style='color: red;'>Error adding column '$columnName' to table '$tableName': " . $conn->error . "</p>";
        }
    } else {
        echo "<p>Column '$columnName' already exists in table '$tableName'.</p>";
    }
}

// Function to create table if it doesn't exist
function createTableIfNotExists($conn, $tableName, $tableDefinition) {
    if (!tableExists($conn, $tableName)) {
        if ($conn->query($tableDefinition)) {
            echo "<p style='color: green;'>Table '$tableName' created successfully!</p>";
        } else {
            echo "<p style='color: red;'>Error creating table '$tableName': " . $conn->error . "</p>";
        }
    } else {
        echo "<p>Table '$tableName' already exists.</p>";
    }
}

// Create detail_pesanan table if it doesn't exist
$detailPesananDefinition = "CREATE TABLE detail_pesanan (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    id_pesanan INT(11) NOT NULL,
    nama_produk VARCHAR(255) NOT NULL,
    harga DECIMAL(10,2) NOT NULL,
    quantity INT(11) NOT NULL DEFAULT 1
)";
createTableIfNotExists($conn, "detail_pesanan", $detailPesananDefinition);

// Add quantity column to detail_pesanan if it doesn't exist
addColumnIfNotExists($conn, "detail_pesanan", "quantity", "INT(11) NOT NULL DEFAULT 1");

// Create pesanan_item table if it doesn't exist
$pesananItemDefinition = "CREATE TABLE pesanan_item (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    pesanan_id INT(11) NOT NULL,
    produk_id INT(11) NOT NULL,
    quantity INT(11) NOT NULL DEFAULT 1,
    harga DECIMAL(10,2) NOT NULL
)";
createTableIfNotExists($conn, "pesanan_item", $pesananItemDefinition);

// Add quantity column to pesanan_item if it doesn't exist
addColumnIfNotExists($conn, "pesanan_item", "quantity", "INT(11) NOT NULL DEFAULT 1");

// Show all tables in database
$result = $conn->query("SHOW TABLES");
echo "<h2>All tables in database:</h2>";
echo "<ul>";
while ($row = $result->fetch_row()) {
    echo "<li>" . $row[0] . "</li>";
}
echo "</ul>";

// Show structure of detail_pesanan table
if (tableExists($conn, "detail_pesanan")) {
    $result = $conn->query("DESCRIBE detail_pesanan");
    echo "<h2>Structure of 'detail_pesanan' table:</h2>";
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
}

// Show structure of pesanan_item table
if (tableExists($conn, "pesanan_item")) {
    $result = $conn->query("DESCRIBE pesanan_item");
    echo "<h2>Structure of 'pesanan_item' table:</h2>";
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
}

// Close connection
$conn->close();

echo "<p style='margin-top: 20px;'><a href='cart.php' style='display: inline-block; padding: 10px 20px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 5px;'>Back to Cart</a></p>";
?>
