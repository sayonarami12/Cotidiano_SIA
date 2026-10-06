<?php
require "config.php";

$data       = json_decode(file_get_contents("php://input"), true);
$orderID    = intval($data["orderID"] ?? 0);
$customerID = intval($data["customerID"] ?? 0);

if (!$orderID || !$customerID) {
    http_response_code(400);
    echo json_encode(["error" => "Missing order or customer ID"]);
    exit;
}

$conn->begin_transaction();

try {
    $orderResult = $conn->query("SELECT orderStatus, orderDate FROM `order` WHERE orderID = $orderID AND customerID = $customerID FOR UPDATE");
    $order = $orderResult ? $orderResult->fetch_assoc() : null;

    if (!$order) {
        throw new Exception("Order not found");
    }

    $secondsSinceOrder = time() - strtotime($order["orderDate"]);
    if ($order["orderStatus"] !== "Pending" || $secondsSinceOrder < 0 || $secondsSinceOrder > 5 * 60 * 60) {
        throw new Exception("Only pending orders within five hours can be cancelled");
    }

    $itemsResult = $conn->query("SELECT productID, quantity FROM order_item WHERE orderID = $orderID");
    while ($item = $itemsResult->fetch_assoc()) {
        $productID = intval($item["productID"]);
        $quantity  = intval($item["quantity"]);
        $conn->query("UPDATE product SET stockQuantity = stockQuantity + $quantity WHERE productID = $productID");
    }

    if (!$conn->query("UPDATE `order` SET orderStatus = 'Cancelled' WHERE orderID = $orderID AND customerID = $customerID")) {
        throw new Exception("Failed to cancel order");
    }

    $conn->commit();
    echo json_encode(["success" => true, "message" => "Order cancelled successfully"]);
} catch (Exception $e) {
    $conn->rollback();
    http_response_code(400);
    echo json_encode(["error" => $e->getMessage()]);
}

$conn->close();
?>
