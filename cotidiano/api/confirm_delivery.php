<?php
require "config.php";

header("Access-Control-Allow-Methods: POST");

$data = json_decode(file_get_contents("php://input"), true);
$orderID = filter_var($data["orderID"] ?? null, FILTER_VALIDATE_INT);
$customerID = filter_var($data["customerID"] ?? null, FILTER_VALIDATE_INT);

if (!$orderID || $orderID < 1 || !$customerID || $customerID < 1) {
    http_response_code(400);
    echo json_encode(["success" => false, "error" => "A valid orderID and customerID are required."]);
    $conn->close();
    exit;
}

try {
    $conn->begin_transaction();

    $orderStmt = $conn->prepare("SELECT orderStatus, totalAmount FROM `order` WHERE orderID = ? AND customerID = ? FOR UPDATE");
    $orderStmt->bind_param("ii", $orderID, $customerID);
    if (!$orderStmt->execute()) throw new Exception("Could not verify order.");
    $order = $orderStmt->get_result()->fetch_assoc();
    $orderStmt->close();

    if (!$order) {
        $conn->rollback();
        http_response_code(404);
        echo json_encode(["success" => false, "error" => "Order not found."]);
        $conn->close();
        exit;
    }

    if (!in_array($order["orderStatus"], ["Processing", "Shipped"], true)) {
        $conn->rollback();
        http_response_code(409);
        echo json_encode(["success" => false, "error" => "Only processing or shipped orders can be confirmed."]);
        $conn->close();
        exit;
    }

    $paymentStmt = $conn->prepare("SELECT paymentID FROM payment WHERE orderID = ? FOR UPDATE");
    $paymentStmt->bind_param("i", $orderID);
    if (!$paymentStmt->execute()) throw new Exception("Could not verify payment record.");
    $payment = $paymentStmt->get_result()->fetch_assoc();
    $paymentStmt->close();
    if (!$payment) throw new Exception("Payment record not found.");

    $status = "Delivered";
    $updateOrder = $conn->prepare("UPDATE `order` SET orderStatus = ? WHERE orderID = ? AND customerID = ?");
    $updateOrder->bind_param("sii", $status, $orderID, $customerID);
    if (!$updateOrder->execute()) throw new Exception("Could not confirm delivery.");
    $updateOrder->close();

    $saleStmt = $conn->prepare("INSERT IGNORE INTO sales_transaction (orderID, saleAmount)
        SELECT orderID, totalAmount FROM `order` WHERE orderID = ?");
    $saleStmt->bind_param("i", $orderID);
    if (!$saleStmt->execute()) throw new Exception("Could not record completed sale.");
    $saleStmt->close();

    $countResult = $conn->query("SELECT COUNT(*) AS deliveredCount FROM `order` WHERE orderStatus = 'Delivered' AND isArchived = 0");
    if (!$countResult) throw new Exception("Could not update delivered-order archive.");
    $archiveCount = max(0, (int)$countResult->fetch_assoc()["deliveredCount"] - 50);
    if ($archiveCount > 0) {
        $archiveStmt = $conn->prepare("UPDATE `order` SET isArchived = 1, archivedAt = NOW()
            WHERE orderID IN (
                SELECT orderID FROM (
                    SELECT orderID FROM `order`
                    WHERE orderStatus = 'Delivered' AND isArchived = 0
                    ORDER BY orderDate ASC, orderID ASC LIMIT ?
                ) AS oldest_orders
            )");
        $archiveStmt->bind_param("i", $archiveCount);
        if (!$archiveStmt->execute()) throw new Exception("Could not archive older delivered orders.");
        $archiveStmt->close();
    }

    $conn->commit();
    echo json_encode(["success" => true, "message" => "Delivery confirmed."]);
} catch (Throwable $e) {
    $conn->rollback();
    http_response_code(500);
    echo json_encode(["success" => false, "error" => "Failed to confirm delivery."]);
}

$conn->close();
?>