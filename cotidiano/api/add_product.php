<?php
require "config.php";

$data        = json_decode(file_get_contents("php://input"), true);
$name        = $conn->real_escape_string($data["name"]);
$family      = $conn->real_escape_string($data["family"]);
$price       = (float)$data["price"];
$discountPrice = $data["discount_price"] ?? null;
if ($discountPrice === "" || $discountPrice === null) {
    $discountPriceSql = "NULL";
} elseif (is_numeric($discountPrice) && (float)$discountPrice >= 0) {
    $discountPriceSql = (string)(float)$discountPrice;
} else {
    http_response_code(400);
    echo json_encode(["error" => "Discount price must be a non-negative number."]);
    $conn->close();
    exit;
}
$category    = $data["category"] ?? "unisex";
$category    = ["him" => "male", "her" => "female"][$category] ?? $category;
$category    = $conn->real_escape_string($category);
$stock       = (int)$data["stock"];
$image       = $conn->real_escape_string($data["image"] ?? "");
$notes_top   = $conn->real_escape_string($data["notes_top"] ?? "");
$notes_heart = $conn->real_escape_string($data["notes_heart"] ?? "");
$notes_base  = $conn->real_escape_string($data["notes_base"] ?? "");

$conn->query("
    INSERT INTO product (productName, family, price, discount_price, category, stockQuantity, Image_url, notes_top, notes_heart, notes_base)
    VALUES ('$name', '$family', $price, $discountPriceSql, '$category', $stock, '$image', '$notes_top', '$notes_heart', '$notes_base')
");

echo json_encode(["message" => "Product added.", "id" => $conn->insert_id]);
$conn->close();
?>