<?php
require "config.php";

$data      = json_decode(file_get_contents("php://input"), true);
$productID = (int)$data["productID"];

// Remove related records first to avoid foreign key errors
$conn->query("DELETE FROM order_item WHERE productID = $productID");
$conn->query("DELETE FROM review     WHERE productID = $productID");

// Then delete the product
$conn->query("DELETE FROM product WHERE productID = $productID");

echo json_encode(["message" => "Product deleted."]);
$conn->close();
?>