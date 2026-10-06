<?php
require "config.php";

function respond($status, $payload) {
    http_response_code($status);
    echo json_encode($payload);
    exit;
}

$method = $_SERVER["REQUEST_METHOD"] ?? "GET";

if ($method === "GET") {
    $customerID = filter_input(INPUT_GET, "customerID", FILTER_VALIDATE_INT);
    if (!$customerID || $customerID < 1) {
        respond(400, ["error" => "A valid customerID is required."]);
    }

    $stmt = $conn->prepare("SELECT addressLabel AS label, streetAddress AS street, city, province, postalCode AS zip, phoneNumber
        FROM shipping_address
        WHERE customerID = ? AND isDefault = 1
        ORDER BY addressID DESC
        LIMIT 1");
    $stmt->bind_param("i", $customerID);
    if (!$stmt->execute()) {
        respond(500, ["error" => "Could not fetch shipping address."]);
    }

    $address = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    echo json_encode($address ?: (object)[]);
    $conn->close();
    exit;
}

if ($method !== "POST") {
    header("Allow: GET, POST");
    respond(405, ["error" => "Method not allowed."]);
}

$data = json_decode(file_get_contents("php://input"), true);
if (!is_array($data)) {
    respond(400, ["error" => "A valid JSON request body is required."]);
}

$customerID = filter_var($data["customerID"] ?? null, FILTER_VALIDATE_INT);
$street = trim((string)($data["street"] ?? ""));
$city = trim((string)($data["city"] ?? ""));
$province = trim((string)($data["province"] ?? ""));
$zip = trim((string)($data["zip"] ?? ""));
$label = trim((string)($data["label"] ?? ""));
$phone = trim((string)($data["phone"] ?? ""));
$phone = $phone === "" ? null : $phone;

if (!$customerID || $customerID < 1 || $street === "" || $city === "" || $province === "" || $zip === "") {
    respond(400, ["error" => "Customer and complete shipping address details are required."]);
}

$conn->begin_transaction();

$findDefault = $conn->prepare("SELECT addressID
    FROM shipping_address
    WHERE customerID = ? AND isDefault = 1
    ORDER BY addressID DESC
    LIMIT 1
    FOR UPDATE");
$findDefault->bind_param("i", $customerID);
if (!$findDefault->execute()) {
    $conn->rollback();
    respond(500, ["error" => "Could not save shipping address."]);
}
$defaultAddress = $findDefault->get_result()->fetch_assoc();
$findDefault->close();

if ($defaultAddress) {
    $addressID = (int)$defaultAddress["addressID"];
    $stmt = $conn->prepare("UPDATE shipping_address
        SET addressLabel = ?, streetAddress = ?, city = ?, province = ?, postalCode = ?, phoneNumber = ?, isDefault = 1
        WHERE addressID = ? AND customerID = ?");
    $stmt->bind_param("ssssssii", $label, $street, $city, $province, $zip, $phone, $addressID, $customerID);
} else {
    $lastAddress = $conn->query("SELECT addressID FROM shipping_address ORDER BY addressID DESC LIMIT 1 FOR UPDATE");
    if (!$lastAddress) {
        $conn->rollback();
        respond(500, ["error" => "Could not save shipping address."]);
    }
    $lastRow = $lastAddress->fetch_assoc();
    $addressID = (int)($lastRow["addressID"] ?? 0) + 1;

    $stmt = $conn->prepare("INSERT INTO shipping_address
        (addressID, customerID, addressLabel, streetAddress, city, province, postalCode, phoneNumber, isDefault)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)");
    $stmt->bind_param("iissssss", $addressID, $customerID, $label, $street, $city, $province, $zip, $phone);
}

if (!$stmt->execute()) {
    $stmt->close();
    $conn->rollback();
    respond(500, ["error" => "Could not save shipping address."]);
}

$stmt->close();
if (!$conn->commit()) {
    $conn->rollback();
    respond(500, ["error" => "Could not save shipping address."]);
}

echo json_encode(["success" => true, "message" => "Shipping address saved."]);
$conn->close();
?>