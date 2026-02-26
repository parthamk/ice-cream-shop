<?php
require 'db.php';

// Get the JSON payload from the JavaScript Fetch API
$json = file_get_contents('php://input');
$data = json_decode($json, true);

if ($data && isset($data['cart'])) {
    try {
        // Begin a database transaction
        $pdo->beginTransaction();

        // 1. Insert into Orders table
        $stmtOrder = $pdo->prepare("INSERT INTO orders (total_amount, tax, platform_fee, payment_method) VALUES (?, ?, ?, ?)");
        $stmtOrder->execute([$data['total'], $data['tax'], 1.50, $data['paymentMethod']]);
        
        // Get the ID of the order we just created
        $orderId = $pdo->lastInsertId();

        // 2. Insert into Order Items table
        $stmtItem = $pdo->prepare("INSERT INTO order_items (order_id, product_name, quantity, price) VALUES (?, ?, ?, ?)");
        
        foreach ($data['cart'] as $item) {
            $stmtItem->execute([$orderId, $item['name'], $item['qty'], $item['price']]);
        }

        // Commit transaction
        $pdo->commit();
        
        // Send success response back to JavaScript
        echo json_encode(['success' => true, 'order_id' => $orderId]);

    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid data received']);
}
?>
