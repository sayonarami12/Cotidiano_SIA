<?php
require "config.php";

$data    = json_decode(file_get_contents("php://input"), true);
$orderID = (int)$data["orderID"];
$status  = $conn->real_escape_string($data["status"]);

$allowedStatuses = ["Pending", "Processing", "Shipped", "Delivered", "Cancelled"];
if (!in_array($status, $allowedStatuses, true)) {
	http_response_code(400);
	echo json_encode(["error" => "Invalid order status."]);
	$conn->close();
	exit;
}

$conn->begin_transaction();

$currentResult = $conn->query("SELECT orderStatus FROM `order` WHERE orderID=$orderID");
$currentOrder  = $currentResult ? $currentResult->fetch_assoc() : null;

if (!$currentOrder) {
	http_response_code(404);
	echo json_encode(["error" => "Order not found."]);
	$conn->rollback();
	$conn->close();
	exit;
}

if ($currentOrder["orderStatus"] === "Cancelled") {
	http_response_code(409);
	echo json_encode(["error" => "Cancelled orders cannot be changed."]);
	$conn->rollback();
	$conn->close();
	exit;
}

if ($currentOrder["orderStatus"] === "Delivered") {
	http_response_code(409);
	echo json_encode(["error" => "Delivered orders cannot be changed."]);
	$conn->rollback();
	$conn->close();
	exit;
}

if (!$conn->query("UPDATE `order` SET orderStatus='$status' WHERE orderID=$orderID AND orderStatus NOT IN ('Cancelled', 'Delivered')")) {
	http_response_code(500);
	echo json_encode(["error" => "Failed to update order status."]);
	$conn->rollback();
	$conn->close();
	exit;
}

if ($status === "Delivered") {
	if (!$conn->query("INSERT IGNORE INTO sales_transaction (orderID, saleAmount) SELECT orderID, totalAmount FROM `order` WHERE orderID=$orderID")) {
		$conn->rollback();
		http_response_code(500);
		echo json_encode(["error" => "Failed to record completed sale."]);
		$conn->close();
		exit;
	}

	$countResult = $conn->query("SELECT COUNT(*) AS deliveredCount FROM `order` WHERE orderStatus='Delivered' AND isArchived=0");
	$deliveredCount = intval($countResult->fetch_assoc()["deliveredCount"] ?? 0);
	$archiveCount = max(0, $deliveredCount - 50);
	if ($archiveCount > 0) {
		$oldest = $conn->query("SELECT orderID FROM `order` WHERE orderStatus='Delivered' AND isArchived=0 ORDER BY orderDate ASC, orderID ASC LIMIT $archiveCount");
		while ($row = $oldest->fetch_assoc()) {
			$archiveID = intval($row["orderID"]);
			$conn->query("UPDATE `order` SET isArchived=1, archivedAt=NOW() WHERE orderID=$archiveID");
		}
	}
}

$conn->commit();
echo json_encode(["message" => "Status updated."]);
$conn->close();
?>