<?php
require "config.php";

$data        = json_decode(file_get_contents("php://input"), true);
$firstName   = $conn->real_escape_string($data["firstName"]);
$lastName    = $conn->real_escape_string($data["lastName"]);
$email       = $conn->real_escape_string($data["email"]);
$password    = $conn->real_escape_string($data["password"]);
$phoneNumber = $conn->real_escape_string($data["phoneNumber"] ?? "");

// Check if email already exists
$check = $conn->query("SELECT customerID FROM customer WHERE email='$email'");
if ($check->num_rows > 0) {
    http_response_code(409);
    echo json_encode(["error" => "Email already registered."]);
    exit;
}

$conn->query(
    "INSERT INTO customer (firstName, lastName, email, password, phoneNumber)
     VALUES ('$firstName', '$lastName', '$email', '$password', '$phoneNumber')"
);

echo json_encode(["message" => "Account created successfully."]);
$conn->close();
?>