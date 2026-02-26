<?php
header("Content-Type: application/json");

require "../config/db.php";
require "../utils/response.php";
require "../utils/helpers.php";


$data = json_decode(file_get_contents("php://input"));

if (!$data || !isset($data->email, $data->password)) {
    jsonResponse("error", "Invalid input");
}

$email = $conn->real_escape_string($data->email);
$password = $data->password;

// Check if user exists
$result = $conn->query("SELECT id, username, password, role FROM users WHERE email='$email'");

if ($result->num_rows == 0) {
    jsonResponse("error", "User not found");
}

$user = $result->fetch_assoc();

// Verify password
if (!password_verify($password, $user['password'])) {
    jsonResponse("error", "Incorrect password");
}

// Success: send user info (without password)
jsonResponse("success", "Login successful", [
    "id" => $user['id'],
    "username" => $user['username'],
    "role" => $user['role']
]);
?>