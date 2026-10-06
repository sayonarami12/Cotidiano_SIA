<?php
require "config.php";

$data      = json_decode(file_get_contents("php://input"), true);
$productID = (int)$data["productID"];
$stock     = (int)$data["stock"];

$conn->query("UPDATE product SET stockQuantity=$stock WHERE productID=$productID");

echo json_encode(["message" => "Stock updated."]);
$conn->close();
?>