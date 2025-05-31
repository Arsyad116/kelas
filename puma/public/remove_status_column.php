<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dbpuma";

echo "<h2>Remove Status Column from Banners Table</h2>";

try {
    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    echo "<p>Connected to database successfully.</p>";
    
    // Check if banners table exists
    $result = $conn->query("SHOW TABLES LIKE 'banners'");
    
    if ($result->num_rows > 0) {
        echo "<p>Table 'banners' exists.</p>";
        
        // Check if status column exists
        $result = $conn->query("SHOW COLUMNS FROM banners LIKE 'status'");
        if ($result->num_rows > 0) {
            echo "<p>Column 'status' exists. Removing it now...</p>";
            
            // Remove status column
            if ($conn->query("ALTER TABLE banners DROP COLUMN status")) {
                echo "<p style='color:green'>Column 'status' removed successfully.</p>";
            } else {
                throw new Exception("Error removing column 'status': " . $conn->error);
            }
        } else {
            echo "<p>Column 'status' does not exist. No action needed.</p>";
        }
        
        // Show updated structure
        $result = $conn->query("DESCRIBE banners");
        echo "<h3>Updated structure of 'banners' table:</h3>";
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
        echo "<p>Table 'banners' does not exist.</p>";
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
