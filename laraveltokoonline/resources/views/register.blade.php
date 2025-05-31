<?php
require_once "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["register"])) {
    // Enable error reporting for debugging
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    
    // Validate inputs
    if(empty($username) || empty($email) || empty($password)) {
        echo json_encode(["success" => false, "error" => "All fields are required"]);
        exit;
    }
    
    // Check if email already exists
    $checkEmail = "SELECT id FROM users WHERE email = ?";
    if($stmt = mysqli_prepare($conn, $checkEmail)) {
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        
        if(mysqli_stmt_num_rows($stmt) > 0) {
            echo json_encode(["success" => false, "error" => "Email already exists"]);
            exit;
        }
        mysqli_stmt_close($stmt);
    }
    
    // Insert new user
    $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
    
    if($stmt = mysqli_prepare($conn, $sql)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        mysqli_stmt_bind_param($stmt, "sss", $username, $email, $hashed_password);
        
        if(mysqli_stmt_execute($stmt)) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "error" => "Registration failed: " . mysqli_error($conn)]);
        }
        mysqli_stmt_close($stmt);
    } else {
        echo json_encode(["success" => false, "error" => "Database error: " . mysqli_error($conn)]);
    }
}
?>