<?php
require_once "config.php";
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["login"])) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    if (empty($email) || empty($password)) {
        echo json_encode(["success" => false, "error" => "Please enter both email and password"]);
        exit;
    }

    $sql = "SELECT id, username, password FROM users WHERE email = ?";

    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $email);

        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($result) == 1) {
                $row = mysqli_fetch_array($result);

                if (password_verify($password, $row["password"])) {
                    $_SESSION["loggedin"] = true;
                    $_SESSION["id"] = $row["id"];
                    $_SESSION["username"] = $row["username"];

                    echo json_encode(["success" => true]);
                } else {
                    echo json_encode(["success" => false, "error" => "Invalid password"]);
                }
            } else {
                echo json_encode(["success" => false, "error" => "No account found with that email"]);
            }
        } else {
            echo json_encode(["success" => false, "error" => "Database error: " . mysqli_error($conn)]);
        }
        mysqli_stmt_close($stmt);
    }
}
?>