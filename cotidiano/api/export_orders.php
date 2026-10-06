<?php
require "config.php";

$hasStartDate = array_key_exists('start_date', $_GET);
$hasEndDate = array_key_exists('end_date', $_GET);
$startDate = $_GET['start_date'] ?? null;
$endDate = $_GET['end_date'] ?? null;
$validDate = function ($value) {
    if (!is_string($value) || !preg_match('/^\d{4}-\d{2}-\d{2}$/D', $value)) {
        return false;
    }
    [$year, $month, $day] = array_map('intval', explode('-', $value));
    return checkdate($month, $day, $year);
};

if ($hasStartDate !== $hasEndDate || ($hasStartDate && (!$validDate($startDate) || !$validDate($endDate) || $startDate > $endDate))) {
    http_response_code(400);
    echo json_encode(["error" => "Provide a valid start_date and end_date in YYYY-MM-DD format."]);
    $conn->close();
    exit;
}

// Match the active Orders report and include contact and shipping details.
$query = "SELECT o.orderID, o.orderDate, o.orderStatus, o.totalAmount,
                 c.firstName, c.lastName,
                 sa.streetAddress, sa.city, sa.province, sa.postalCode
          FROM `order` o
          JOIN customer c ON c.customerID = o.customerID
          JOIN shipping_address sa ON sa.addressID = o.addressID
          WHERE o.isArchived = 0";
if ($hasStartDate) {
    $query .= " AND o.orderDate BETWEEN ? AND ?";
    $startDateTime = $startDate . " 00:00:00";
    $endDateTime = $endDate . " 23:59:59";
}

$statement = $conn->prepare($query . " ORDER BY o.orderDate DESC");
if (!$statement) {
    http_response_code(500);
    echo json_encode(["error" => "Unable to prepare orders export query."]);
    $conn->close();
    exit;
}
if ($hasStartDate) {
    $statement->bind_param('ss', $startDateTime, $endDateTime);
}
if (!$statement->execute()) {
    http_response_code(500);
    echo json_encode(["error" => "Unable to export orders."]);
    $statement->close();
    $conn->close();
    exit;
}

$result = $statement->get_result();
header("Content-Type: text/csv; charset=utf-8");
header('Content-Disposition: attachment; filename="orders_report.csv"');

$output = fopen('php://output', 'w');
fputcsv($output, ["Order ID", "Date", "Customer Name", "Customer Address", "Total Amount", "Status"]);
while ($row = $result->fetch_assoc()) {
    $address = implode(', ', array_filter([
        $row['streetAddress'],
        $row['city'],
        $row['province'],
        $row['postalCode']
    ]));
    fputcsv($output, [
        $row['orderID'],
        $row['orderDate'],
        $row['firstName'] . ' ' . $row['lastName'],
        $address,
        $row['totalAmount'],
        $row['orderStatus']
    ]);
}

fclose($output);
$statement->close();
$conn->close();
?>