<?php
header("Content-Type: application/json");

require "../config/db.php";
require "../utils/response.php";
require_once '../utils/helpers.php'; 

$data = json_decode(file_get_contents("php://input"));

if (!$data || !isset($data->username, $data->email, $data->password)) {
    jsonResponse("error", "Invalid input");
}

$username = $conn->real_escape_string($data->username);
$email = $conn->real_escape_string($data->email);
$password = password_hash($data->password, PASSWORD_DEFAULT);

// Check if email already exists
$check = $conn->query("SELECT id FROM users WHERE email = '$email'");

if ($check->num_rows > 0) {
    jsonResponse("error", "Email already exists");
}

// Insert user
$sql = "INSERT INTO users (username, email, password, role)
        VALUES ('$username', '$email', '$password', 'user')";

if ($conn->query($sql)) {
    jsonResponse("success", "User registered successfully");
} else {
    jsonResponse("error", "Registration failed");
}
?>