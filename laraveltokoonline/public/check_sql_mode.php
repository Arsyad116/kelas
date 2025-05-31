<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dbpuma";

echo "<h2>MySQL SQL Mode Check</h2>";

try {
    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    echo "<p>Connected to database successfully.</p>";
    
    // Check SQL mode
    $result = $conn->query("SELECT @@sql_mode as sql_mode");
    if ($result) {
        $row = $result->fetch_assoc();
        echo "<p>Current SQL Mode: <strong>" . htmlspecialchars($row['sql_mode']) . "</strong></p>";
        
        // Check if ONLY_FULL_GROUP_BY is enabled
        if (strpos($row['sql_mode'], 'ONLY_FULL_GROUP_BY') !== false) {
            echo "<p style='color:red'>ONLY_FULL_GROUP_BY is enabled, which may cause issues with GROUP BY queries.</p>";
            
            // Suggest solution
            echo "<h3>Suggested Solution:</h3>";
            echo "<p>You can temporarily disable ONLY_FULL_GROUP_BY for this session:</p>";
            echo "<pre>SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));</pre>";
            
            // Try to disable it for this session
            $conn->query("SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");
            
            // Check if it worked
            $result = $conn->query("SELECT @@sql_mode as sql_mode");
            if ($result) {
                $row = $result->fetch_assoc();
                echo "<p>Updated SQL Mode: <strong>" . htmlspecialchars($row['sql_mode']) . "</strong></p>";
            }
        } else {
            echo "<p style='color:green'>ONLY_FULL_GROUP_BY is not enabled, which is good for the current query.</p>";
        }
    } else {
        echo "<p style='color:red'>Error checking SQL mode: " . $conn->error . "</p>";
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
