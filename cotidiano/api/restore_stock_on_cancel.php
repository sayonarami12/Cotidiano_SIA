<?php
require "config.php";

$data   = json_decode(file_get_contents("php://input"), true);
$orderID = intval($data["orderID"] ?? 0);

if (!$orderID) {
    http_response_code(400);
    echo json_encode(["error" => "Missing orderID"]);
    exit;
}

$conn->begin_transaction();

try {
    // Get all items in the order
    $items_result = $conn->query("
        SELECT productID, quantity
        FROM order_item
        WHERE orderID = $orderID
    ");

    if (!$items_result) {
        throw new Exception("Failed to fetch order items");
    }

    // Restore stock for each item
    while ($item = $items_result->fetch_assoc()) {
        $productID = intval($item['productID']);
        $quantity  = intval($item['quantity']);

        $update_result = $conn->query("
            UPDATE product 
            SET stockQuantity = stockQuantity + $quantity 
            WHERE productID = $productID
        ");

        if (!$update_result) {
            throw new Exception("Failed to restore stock for product ID $productID");
        }
    }

    $conn->commit();
    http_response_code(200);
    echo json_encode(["success" => true, "message" => "Stock restored successfully"]);

} catch (Exception $e) {
    $conn->rollback();
    http_response_code(500);
    echo json_encode(["error" => "Failed to restore stock: " . $e->getMessage()]);
}

$conn->close();
?>
