<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dbpuma";

echo "<h2>Add Timestamps to Users Table</h2>";

try {
    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    echo "<p>Connected to database successfully.</p>";
    
    // Check if users table exists
    $result = $conn->query("SHOW TABLES LIKE 'users'");
    
    if ($result->num_rows > 0) {
        echo "<p>Table 'users' exists.</p>";
        
        // Check if updated_at column exists
        $result = $conn->query("SHOW COLUMNS FROM users LIKE 'updated_at'");
        if ($result->num_rows == 0) {
            echo "<p>Column 'updated_at' does not exist. Adding it now...</p>";
            
            // Add updated_at column
            if ($conn->query("ALTER TABLE users ADD COLUMN updated_at TIMESTAMP NULL")) {
                echo "<p style='color:green'>Column 'updated_at' added successfully.</p>";
            } else {
                throw new Exception("Error adding column 'updated_at': " . $conn->error);
            }
        } else {
            echo "<p>Column 'updated_at' already exists.</p>";
        }
        
        // Show updated structure
        $result = $conn->query("DESCRIBE users");
        echo "<h3>Updated structure of 'users' table:</h3>";
        echo "<table border='1'><tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            foreach ($row as $key => $value) {
                echo "<td>" . htmlspecialchars($value ?? 'NULL') . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
        
    } else {
        echo "<p>Table 'users' does not exist.</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color:red'>Error: " . $e->getMessage() . "</p>";
} finally {
    if (isset($conn)) {
        $conn->close();
        echo "<p>Database connection closed.</p>";
    }
}
?>
