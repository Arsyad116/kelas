<?php
// Set header untuk JSON
header('Content-Type: application/json');

// Kirim response error
echo json_encode([
    'error' => 'Invalid API endpoint',
    'message' => 'Please specify a valid API endpoint',
    'available_endpoints' => [
        'get_order_details.php',
        'get_order_status.php',
        'get_customer_details.php'
    ]
]);
?>
