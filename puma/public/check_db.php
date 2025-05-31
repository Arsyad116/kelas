<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dbpuma";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if database exists
$result = $conn->query("SHOW DATABASES LIKE '$dbname'");
if ($result->num_rows > 0) {
    echo "Database $dbname exists.<br>";
    
    // Connect to the database
    $conn->select_db($dbname);
    
    // Check if tables exist
    $tables = array("pesanan", "detail_pesanan");
    foreach ($tables as $table) {
        $result = $conn->query("SHOW TABLES LIKE '$table'");
        if ($result->num_rows > 0) {
            echo "Table $table exists.<br>";
        } else {
            echo "Table $table does not exist.<br>";
        }
    }
} else {
    echo "Database $dbname does not exist.<br>";
    
    // Create database
    echo "Creating database $dbname...<br>";
    if ($conn->query("CREATE DATABASE IF NOT EXISTS $dbname")) {
        echo "Database created successfully.<br>";
    } else {
        echo "Error creating database: " . $conn->error . "<br>";
    }
}

$conn->close();
?>
