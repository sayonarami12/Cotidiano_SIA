<?php
require "config.php";

$data        = json_decode(file_get_contents("php://input"), true);
$productID   = (int)$data["productID"];
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
$image       = $conn->real_escape_string($data["image"]       ?? "");
$notes_top   = $conn->real_escape_string($data["notes_top"]   ?? "");
$notes_heart = $conn->real_escape_string($data["notes_heart"] ?? "");
$notes_base  = $conn->real_escape_string($data["notes_base"]  ?? "");

$conn->query("
    UPDATE product
    SET productName  = '$name',
        family       = '$family',
        price        = $price,
        discount_price = $discountPriceSql,
        category     = '$category',
        Image_url    = '$image',
        notes_top    = '$notes_top',
        notes_heart  = '$notes_heart',
        notes_base   = '$notes_base'
    WHERE productID  = $productID
");

echo json_encode(["message" => "Product updated."]);
$conn->close();
?>