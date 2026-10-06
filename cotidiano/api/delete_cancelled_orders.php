<?php
require "config.php";

$data     = json_decode(file_get_contents("php://input"), true);
$orderIDs = array_values(array_unique(array_filter(array_map("intval", $data["orderIDs"] ?? []))));

if (empty($orderIDs)) {
    http_response_code(400);
    echo json_encode(["error" => "No orders selected."]);
    exit;
}

$idList = implode(",", $orderIDs);
$conn->begin_transaction();

try {
    $check = $conn->query("SELECT orderID, orderStatus FROM `order` WHERE orderID IN ($idList) FOR UPDATE");
    if (!$check || $check->num_rows !== count($orderIDs)) {
        throw new Exception("One or more orders were not found.");
    }

    while ($order = $check->fetch_assoc()) {
        if ($order["orderStatus"] !== "Cancelled") {
            throw new Exception("Only cancelled orders can be deleted.");
        }
    }

    if (!$conn->query("DELETE FROM payment WHERE orderID IN ($idList)")) {
        throw new Exception("Failed to delete payment records.");
    }

    if (!$conn->query("DELETE FROM order_item WHERE orderID IN ($idList)")) {
        throw new Exception("Failed to delete order items.");
    }

    if (!$conn->query("DELETE FROM `order` WHERE orderID IN ($idList) AND orderStatus = 'Cancelled'")) {
        throw new Exception("Failed to delete cancelled orders.");
    }

    $conn->commit();
    echo json_encode(["success" => true, "deleted" => count($orderIDs)]);
} catch (Exception $e) {
    $conn->rollback();
    http_response_code(400);
    echo json_encode(["error" => $e->getMessage()]);
}

$conn->close();
?>
