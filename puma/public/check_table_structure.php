<?php
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

// Get table structure
$result = $conn->query("DESCRIBE produk");

echo "<h1>Struktur Tabel 'produk'</h1>";
echo "<table border='1' cellpadding='10'>";
echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row["Field"] . "</td>";
        echo "<td>" . $row["Type"] . "</td>";
        echo "<td>" . $row["Null"] . "</td>";
        echo "<td>" . $row["Key"] . "</td>";
        echo "<td>" . $row["Default"] . "</td>";
        echo "<td>" . $row["Extra"] . "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='6'>No fields found in table 'produk'</td></tr>";
}

echo "</table>";

// Close connection
$conn->close();
?>
