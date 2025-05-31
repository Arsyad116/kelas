<?php
// dbcontroller.php - Database Controller
class DBController {
    private $host = "localhost";
    private $username = "root";
    private $password = "";
    private $database = "restodor";
    private $conn;

    public function __construct() {
        $this->conn = new mysqli($this->host, $this->username, $this->password, $this->database);
        
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
        
        // Set charset to UTF-8
        $this->conn->set_charset("utf8");
    }

    public function getConnection() {
        return $this->conn;
    }

    public function query($sql) {
        $result = $this->conn->query($sql);
        if (!$result) {
            die("Query failed: " . $this->conn->error);
        }
        return $result;
    }

    public function prepare($sql) {
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            die("Prepare failed: " . $this->conn->error);
        }
        return $stmt;
    }

    public function escape($string) {
        return $this->conn->real_escape_string($string);
    }

    public function getLastInsertId() {
        return $this->conn->insert_id;
    }

    public function close() {
        $this->conn->close();
    }

    // Helper method untuk mendapatkan satu row
    public function fetchOne($sql, $params = []) {
        $stmt = $this->prepare($sql);
        if (!empty($params)) {
            $types = str_repeat('s', count($params));
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    // Helper method untuk mendapatkan multiple rows
    public function fetchAll($sql, $params = []) {
        $stmt = $this->prepare($sql);
        if (!empty($params)) {
            $types = str_repeat('s', count($params));
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }

    // Helper method untuk execute prepared statement
    public function execute($sql, $params = []) {
        $stmt = $this->prepare($sql);
        if (!empty($params)) {
            $types = str_repeat('s', count($params));
            $stmt->bind_param($types, ...$params);
        }
        return $stmt->execute();
    }
}
?>