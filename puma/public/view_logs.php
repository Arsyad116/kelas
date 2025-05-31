<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Checkout Logs</h2>";

$log_file = __DIR__ . '/checkout_log.txt';

if (file_exists($log_file)) {
    echo "<pre>";
    echo htmlspecialchars(file_get_contents($log_file));
    echo "</pre>";
} else {
    echo "<p>No log file found. No checkout has been attempted yet.</p>";
}
?>
