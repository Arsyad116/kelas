<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dbpuma";

echo "<h2>Fix Banners Table</h2>";

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
        
        // Check if title column exists
        $result = $conn->query("SHOW COLUMNS FROM banners LIKE 'title'");
        if ($result->num_rows == 0) {
            echo "<p>Column 'title' does not exist. Adding it now...</p>";
            
            // Add title column
            if ($conn->query("ALTER TABLE banners ADD COLUMN title VARCHAR(255) NULL AFTER image_path")) {
                echo "<p style='color:green'>Column 'title' added successfully.</p>";
            } else {
                throw new Exception("Error adding column 'title': " . $conn->error);
            }
        } else {
            echo "<p>Column 'title' already exists.</p>";
        }
        
        // Check if description column exists
        $result = $conn->query("SHOW COLUMNS FROM banners LIKE 'description'");
        if ($result->num_rows == 0) {
            echo "<p>Column 'description' does not exist. Adding it now...</p>";
            
            // Add description column
            if ($conn->query("ALTER TABLE banners ADD COLUMN description TEXT NULL AFTER title")) {
                echo "<p style='color:green'>Column 'description' added successfully.</p>";
            } else {
                throw new Exception("Error adding column 'description': " . $conn->error);
            }
        } else {
            echo "<p>Column 'description' already exists.</p>";
        }
        
        // Check if is_active column exists
        $result = $conn->query("SHOW COLUMNS FROM banners LIKE 'is_active'");
        if ($result->num_rows == 0) {
            echo "<p>Column 'is_active' does not exist. Adding it now...</p>";
            
            // Add is_active column
            if ($conn->query("ALTER TABLE banners ADD COLUMN is_active TINYINT(1) DEFAULT 1 AFTER description")) {
                echo "<p style='color:green'>Column 'is_active' added successfully.</p>";
            } else {
                throw new Exception("Error adding column 'is_active': " . $conn->error);
            }
        } else {
            echo "<p>Column 'is_active' already exists.</p>";
        }
        
        // Check if status column exists and rename it to is_active if needed
        $result = $conn->query("SHOW COLUMNS FROM banners LIKE 'status'");
        if ($result->num_rows > 0) {
            // Check if is_active already exists
            $result2 = $conn->query("SHOW COLUMNS FROM banners LIKE 'is_active'");
            if ($result2->num_rows > 0) {
                echo "<p>Both 'status' and 'is_active' columns exist. Copying data from 'status' to 'is_active'...</p>";
                
                // Copy data from status to is_active
                if ($conn->query("UPDATE banners SET is_active = status")) {
                    echo "<p style='color:green'>Data copied successfully.</p>";
                } else {
                    throw new Exception("Error copying data: " . $conn->error);
                }
            } else {
                echo "<p>Column 'status' exists but 'is_active' does not. Renaming 'status' to 'is_active'...</p>";
                
                // Rename status to is_active
                if ($conn->query("ALTER TABLE banners CHANGE COLUMN status is_active TINYINT(1) DEFAULT 1")) {
                    echo "<p style='color:green'>Column renamed successfully.</p>";
                } else {
                    throw new Exception("Error renaming column: " . $conn->error);
                }
            }
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
        echo "<p>Table 'banners' does not exist. Creating it now...</p>";
        
        // Create banners table
        $sql = "CREATE TABLE banners (
            id INT(11) AUTO_INCREMENT PRIMARY KEY,
            image_path VARCHAR(255) NOT NULL,
            title VARCHAR(255) NULL,
            description TEXT NULL,
            is_active TINYINT(1) DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        
        if ($conn->query($sql)) {
            echo "<p style='color:green'>Table 'banners' created successfully.</p>";
        } else {
            throw new Exception("Error creating table: " . $conn->error);
        }
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
