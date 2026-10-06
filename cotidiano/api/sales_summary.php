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

$query = "SELECT COUNT(*) AS totalSales, COALESCE(SUM(st.saleAmount), 0) AS totalRevenue
          FROM sales_transaction st
          JOIN `order` o ON o.orderID = st.orderID";
if ($hasStartDate) {
    // Reports follow the order date, not when the sales row was recorded.
    $query .= " WHERE o.orderDate BETWEEN ? AND ?";
    $startDateTime = $startDate . " 00:00:00";
    $endDateTime = $endDate . " 23:59:59";
}

$statement = $conn->prepare($query);
if (!$statement) {
    http_response_code(500);
    echo json_encode(["error" => "Unable to prepare sales summary query."]);
    $conn->close();
    exit;
}
if ($hasStartDate) {
    $statement->bind_param('ss', $startDateTime, $endDateTime);
}
if (!$statement->execute()) {
    http_response_code(500);
    echo json_encode(["error" => "Unable to load sales summary."]);
    $statement->close();
    $conn->close();
    exit;
}
$result = $statement->get_result();
$summary = $result ? $result->fetch_assoc() : ["totalSales" => 0, "totalRevenue" => 0];

echo json_encode([
    "totalSales" => intval($summary["totalSales"]),
    "totalRevenue" => floatval($summary["totalRevenue"])
]);
$statement->close();
$conn->close();
?>
