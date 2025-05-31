<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get API endpoint from query parameter
$endpoint = $_GET['endpoint'] ?? 'get_order_details.php';
$id = $_GET['id'] ?? 1;

// Base URL for API
$baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
$apiUrl = "$baseUrl/api/$endpoint?id=$id";

echo "<h1>API Test Tool</h1>";
echo "<p>Testing API endpoint: <code>$apiUrl</code></p>";

// Make API request
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

// Display results
echo "<h2>HTTP Status Code</h2>";
echo "<p>$httpCode</p>";

if ($error) {
    echo "<h2>cURL Error</h2>";
    echo "<p>$error</p>";
}

echo "<h2>Raw Response</h2>";
echo "<pre style='background-color: #f5f5f5; padding: 10px; border-radius: 5px; overflow: auto;'>";
echo htmlspecialchars($response);
echo "</pre>";

// Try to parse JSON
echo "<h2>Parsed JSON</h2>";
try {
    $json = json_decode($response, true);
    if ($json === null && json_last_error() !== JSON_ERROR_NONE) {
        echo "<p style='color: red;'>Error parsing JSON: " . json_last_error_msg() . "</p>";
    } else {
        echo "<pre style='background-color: #f5f5f5; padding: 10px; border-radius: 5px; overflow: auto;'>";
        print_r($json);
        echo "</pre>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>Exception: " . $e->getMessage() . "</p>";
}

// Display available endpoints
echo "<h2>Available Endpoints</h2>";
echo "<ul>";
echo "<li><a href='?endpoint=get_order_details.php&id=1'>get_order_details.php</a></li>";
echo "<li><a href='?endpoint=get_order_status.php&id=1'>get_order_status.php</a></li>";
echo "<li><a href='?endpoint=get_customer_details.php&id=1'>get_customer_details.php</a></li>";
echo "</ul>";

// Display server information
echo "<h2>Server Information</h2>";
echo "<pre style='background-color: #f5f5f5; padding: 10px; border-radius: 5px; overflow: auto;'>";
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "\n";
echo "Script Filename: " . $_SERVER['SCRIPT_FILENAME'] . "\n";
echo "Request URI: " . $_SERVER['REQUEST_URI'] . "\n";
echo "PHP Version: " . phpversion() . "\n";
echo "</pre>";
?>
