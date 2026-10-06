<?php
require "config.php";

$customerID = isset($_GET['customerID']) ? intval($_GET['customerID']) : 0;
$archived   = isset($_GET['archived']) && $_GET['archived'] === '1';
$archiveWhere = $archived ? "o.isArchived = 1" : "o.isArchived = 0";

function isValidReportDate($value) {
    if (!is_string($value) || !preg_match('/^\d{4}-\d{2}-\d{2}$/D', $value)) {
        return false;
    }

    [$year, $month, $day] = array_map('intval', explode('-', $value));
    return checkdate($month, $day, $year);
}

// Require a complete, valid calendar-date pair before adding a date predicate.
$hasStartDate = array_key_exists('start_date', $_GET);
$hasEndDate = array_key_exists('end_date', $_GET);
$startDate = $_GET['start_date'] ?? null;
$endDate = $_GET['end_date'] ?? null;

if ($hasStartDate !== $hasEndDate || ($hasStartDate && (!isValidReportDate($startDate) || !isValidReportDate($endDate) || $startDate > $endDate))) {
    http_response_code(400);
    echo json_encode(["error" => "Provide a valid start_date and end_date in YYYY-MM-DD format."]);
    $conn->close();
    exit;
}

$conditions = [$archiveWhere];
$parameters = [];
$types = '';
if ($customerID) {
    $conditions[] = "o.customerID = ?";
    $parameters[] = $customerID;
    $types .= 'i';
}
if ($hasStartDate) {
    // Include the full end date while keeping user input out of the SQL string.
    $conditions[] = "o.orderDate BETWEEN ? AND ?";
    $startDateTime = $startDate . " 00:00:00";
    $endDateTime = $endDate . " 23:59:59";
    $parameters[] = $startDateTime;
    $parameters[] = $endDateTime;
    $types .= 'ss';
}

$query = "
    SELECT o.orderID, o.orderDate, o.orderStatus, o.totalAmount, o.isArchived, o.archivedAt,
           c.firstName, c.lastName, c.phoneNumber,
            sa.streetAddress, sa.city, sa.province, sa.postalCode,
            p.paymentMethod, p.paymentStatus
    FROM `order` o
    JOIN customer c ON o.customerID = c.customerID
    JOIN shipping_address sa ON o.addressID = sa.addressID
        LEFT JOIN payment p ON p.orderID = o.orderID
    WHERE " . implode(' AND ', $conditions) . "
    ORDER BY o.orderDate DESC
";
$statement = $conn->prepare($query);
if (!$statement) {
    http_response_code(500);
    echo json_encode(["error" => "Unable to prepare orders query."]);
    $conn->close();
    exit;
}

if ($types === 'iss') {
    $statement->bind_param($types, $parameters[0], $parameters[1], $parameters[2]);
} elseif ($types === 'i') {
    $statement->bind_param($types, $parameters[0]);
} elseif ($types === 'ss') {
    $statement->bind_param($types, $parameters[0], $parameters[1]);
}

if (!$statement->execute()) {
    http_response_code(500);
    echo json_encode(["error" => "Unable to load orders."]);
    $statement->close();
    $conn->close();
    exit;
}
$result = $statement->get_result();

$orders = [];
while ($row = $result->fetch_assoc()) {
    $orderID = $row['orderID'];

    // Get items for this order
    $items_result = $conn->query("
        SELECT oi.quantity, oi.unitPrice, p.productName
        FROM order_item oi
        JOIN product p ON oi.productID = p.productID
        WHERE oi.orderID = $orderID
    ");

    $items = [];
    while ($item = $items_result->fetch_assoc()) {
        $items[] = [
            "productName" => $item['productName'],
            "quantity"    => intval($item['quantity']),
            "unitPrice"   => floatval($item['unitPrice']),
            // Also add camelCase versions for compatibility
            "name"        => $item['productName'],
            "price"       => floatval($item['unitPrice'])
        ];
    }

    $orders[] = [
        // Admin format (snake_case/original field names)
        "orderID"     => $orderID,
        "orderDate"   => $row['orderDate'],
        "orderStatus" => $row['orderStatus'],
        "totalAmount" => floatval($row['totalAmount']),
        "paymentMethod" => $row['paymentMethod'],
        "paymentStatus" => $row['paymentStatus'],
        "isArchived"  => intval($row['isArchived']),
        "archivedAt"  => $row['archivedAt'],
        "canCancel"   => $row['orderStatus'] === 'Pending' && intval($row['isArchived']) === 0 && (time() - strtotime($row['orderDate'])) >= 0 && (time() - strtotime($row['orderDate'])) <= 5 * 60 * 60,
        "customer"    => $row['firstName'] . " " . $row['lastName'],
        "address"     => $row['streetAddress'] . ", " . $row['city'] . ", " . $row['province'],
        "items"       => $items,
        
        // Account format (camelCase)
        "orderId"         => $orderID,
        "date"            => $row['orderDate'],
        "status"          => $row['orderStatus'],
        "total"           => floatval($row['totalAmount']),
        "paymentMethod"   => $row['paymentMethod'],
        "paymentStatus"   => $row['paymentStatus'],
        "shippingAddress" => [
            "street"   => $row['streetAddress'],
            "city"     => $row['city'],
            "province" => $row['province'],
            "zip"      => $row['postalCode'],
            "phone"    => $row['phoneNumber'] ?? ""
        ]
    ];
}

echo json_encode($orders);
$statement->close();
$conn->close();
?>