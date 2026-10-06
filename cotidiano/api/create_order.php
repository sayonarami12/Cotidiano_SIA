<?php
require "config.php";

function nextID($conn, $table, $column) {
    $result = $conn->query("SELECT $column FROM $table ORDER BY $column DESC LIMIT 1 FOR UPDATE");
    if (!$result) throw new Exception("Could not allocate record ID.");
    $row = $result->fetch_assoc();
    return (int)($row[$column] ?? 0) + 1;
}

$data = json_decode(file_get_contents("php://input"), true);
$customerID = filter_var($data["customerID"] ?? null, FILTER_VALIDATE_INT);
$items = $data["items"] ?? [];
$total = $data["total"] ?? null;
$amountPaid = $data["amountPaid"] ?? null;
$address = $data["shippingAddress"] ?? [];
$paymentMethod = $data["paymentMethod"] ?? "Credit Card";
$allowedPaymentMethods = ["Credit Card", "GCash"];

if (!$customerID || $customerID < 1 || !is_array($items) || empty($items) || !is_numeric($total) || (float)$total <= 0 || !is_array($address)) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid order data"]);
    $conn->close();
    exit;
}

$amountPaidCents = is_numeric($amountPaid) && is_finite((float)$amountPaid)
    ? (int)round((float)$amountPaid * 100)
    : null;
$orderTotalCents = (int)round((float)$total * 100);
if ($amountPaidCents === null || $amountPaidCents !== $orderTotalCents) {
    http_response_code(400);
    echo json_encode(["error" => "Amount paid must match the order total exactly."]);
    $conn->close();
    exit;
}

if (!in_array($paymentMethod, $allowedPaymentMethods, true)) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid payment method."]);
    $conn->close();
    exit;
}

try {
    $conn->begin_transaction();

    $street = trim((string)($address["street"] ?? ""));
    $city = trim((string)($address["city"] ?? ""));
    $province = trim((string)($address["province"] ?? ""));
    $zip = trim((string)($address["zip"] ?? ""));
    if ($street === "" || $city === "" || $province === "" || $zip === "") {
        throw new Exception("Shipping address is incomplete.");
    }

    $addressID = nextID($conn, "shipping_address", "addressID");
    $addressStmt = $conn->prepare("INSERT INTO shipping_address (addressID, customerID, streetAddress, city, province, postalCode)
        VALUES (?, ?, ?, ?, ?, ?)");
    $addressStmt->bind_param("iissss", $addressID, $customerID, $street, $city, $province, $zip);
    if (!$addressStmt->execute()) throw new Exception("Could not save shipping address.");
    $addressStmt->close();

    $orderID = nextID($conn, "`order`", "orderID");
    $orderStatus = "Pending";
    $orderTotal = $orderTotalCents / 100;
    $orderStmt = $conn->prepare("INSERT INTO `order` (orderID, customerID, orderStatus, addressID, totalAmount)
        VALUES (?, ?, ?, ?, ?)");
    $orderStmt->bind_param("iisid", $orderID, $customerID, $orderStatus, $addressID, $orderTotal);
    if (!$orderStmt->execute()) throw new Exception("Could not create order.");
    $orderStmt->close();

    $itemIDCounter = nextID($conn, "order_item", "orderItemID");
    $stockStmt = $conn->prepare("SELECT stockQuantity FROM product WHERE productID = ? FOR UPDATE");
    $itemStmt = $conn->prepare("INSERT INTO order_item (orderItemID, orderID, productID, quantity, unitPrice)
        VALUES (?, ?, ?, ?, ?)");
    $stockUpdate = $conn->prepare("UPDATE product SET stockQuantity = stockQuantity - ? WHERE productID = ?");

    foreach ($items as $item) {
        $productID = filter_var($item["id"] ?? null, FILTER_VALIDATE_INT);
        $quantity = filter_var($item["quantity"] ?? null, FILTER_VALIDATE_INT);
        $unitPrice = $item["price"] ?? null;
        if (!$productID || $productID < 1 || !$quantity || $quantity < 1 || !is_numeric($unitPrice) || (float)$unitPrice < 0) {
            throw new Exception("Invalid order item.");
        }
        $unitPrice = (float)$unitPrice;

        $stockStmt->bind_param("i", $productID);
        if (!$stockStmt->execute()) throw new Exception("Could not check product stock.");
        $stockRow = $stockStmt->get_result()->fetch_assoc();
        if (!$stockRow || $stockRow["stockQuantity"] < $quantity) {
            throw new Exception("Insufficient stock for product ID $productID");
        }

        $itemStmt->bind_param("iiiid", $itemIDCounter, $orderID, $productID, $quantity, $unitPrice);
        if (!$itemStmt->execute()) throw new Exception("Could not save order item.");
        $stockUpdate->bind_param("ii", $quantity, $productID);
        if (!$stockUpdate->execute()) throw new Exception("Could not update product stock.");

        $itemIDCounter++;
    }

    $stockStmt->close();
    $itemStmt->close();
    $stockUpdate->close();

    $paymentID = nextID($conn, "payment", "paymentID");
    $paymentStatus = "Completed";
    $paymentStmt = $conn->prepare("INSERT INTO payment (paymentID, orderID, paymentMethod, paymentAmount, paymentStatus)
        VALUES (?, ?, ?, ?, ?)");
    $paymentStmt->bind_param("iisds", $paymentID, $orderID, $paymentMethod, $orderTotal, $paymentStatus);
    if (!$paymentStmt->execute()) throw new Exception("Could not save payment record.");
    $paymentStmt->close();

    $conn->commit();
    http_response_code(201);
    echo json_encode(["success" => true, "orderID" => $orderID, "message" => "Order placed successfully"]);

} catch (Throwable $e) {
    $conn->rollback();
    http_response_code(500);
    echo json_encode(["error" => "Failed to create order: " . $e->getMessage()]);
}

$conn->close();
?>