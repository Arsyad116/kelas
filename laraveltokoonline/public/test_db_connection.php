<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dbpuma";

echo "<h2>Database Connection Test</h2>";

try {
    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    echo "<p style='color:green'>Connected to database successfully.</p>";
    
    // Test query
    $result = $conn->query("SELECT 1 as test");
    if ($result) {
        $row = $result->fetch_assoc();
        echo "<p>Test query result: " . $row['test'] . "</p>";
    } else {
        throw new Exception("Query failed: " . $conn->error);
    }
    
    // Test insert
    echo "<h3>Testing User Registration</h3>";
    
    // Generate random username and email
    $random = rand(1000, 9999);
    $username = "testuser" . $random;
    $email = "test" . $random . "@example.com";
    $password = password_hash("password123", PASSWORD_DEFAULT);
    
    // Check if user already exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo "<p>User with email $email already exists.</p>";
    } else {
        // Insert new user
        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $password);
        
        if ($stmt->execute()) {
            echo "<p style='color:green'>Test user created successfully.</p>";
            echo "<p>Username: $username</p>";
            echo "<p>Email: $email</p>";
            echo "<p>Password: password123</p>";
        } else {
            throw new Exception("Error creating test user: " . $stmt->error);
        }
    }
    
    // Test login
    echo "<h3>Testing User Login</h3>";
    
    // Get the most recently created test user
    $result = $conn->query("SELECT * FROM users WHERE username LIKE 'testuser%' ORDER BY id DESC LIMIT 1");
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        echo "<p>Found test user:</p>";
        echo "<p>Username: " . $user['username'] . "</p>";
        echo "<p>Email: " . $user['email'] . "</p>";
        
        // Verify password
        if (password_verify("password123", $user['password'])) {
            echo "<p style='color:green'>Password verification successful.</p>";
        } else {
            echo "<p style='color:red'>Password verification failed.</p>";
        }
    } else {
        echo "<p>No test users found.</p>";
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
