<?php
require "config.php";

$data     = json_decode(file_get_contents("php://input"), true);
$email    = $conn->real_escape_string($data["email"]);
$password = $conn->real_escape_string($data["password"]);

$result = $conn->query(
    "SELECT customerID, firstName, lastName, email, phoneNumber 
     FROM customer WHERE email='$email' AND password='$password'"
);

if ($result->num_rows === 0) {
    http_response_code(401);
    echo json_encode(["error" => "Incorrect email or password."]);
} else {
    $customer = $result->fetch_assoc();
    echo json_encode(["user" => $customer]);
}

$conn->close();
?>