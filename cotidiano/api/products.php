<?php
require "config.php";

$result   = $conn->query("SELECT * FROM product ORDER BY productID ASC");
$products = [];

while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}

echo json_encode($products);
$conn->close();
?>