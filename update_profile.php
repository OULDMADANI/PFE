<?php
require "../utils/helpers.php";
require "../utils/response.php";
require "../middleware/auth_middleware.php";
require "../config/db.php";

$user_id = checkAuth(); 

// Get JSON input
$data = json_decode(file_get_contents("php://input"));

if (!$data) {
    sendResponse("error", "Invalid input");
}

// Validate inputs (optional fields: username, email, password)
$fields = [];
$params = [];
$types = "";

// Update username
if (isset($data->username) && !empty(trim($data->username))) {
    $fields[] = "username = ?";
    $params[] = trim($data->username);
    $types .= "s";
}

// Update email
if (isset($data->email) && filter_var($data->email, FILTER_VALIDATE_EMAIL)) {
    $fields[] = "email = ?";
    $params[] = trim($data->email);
    $types .= "s";
}

// Update password
if (isset($data->password) && strlen($data->password) >= 6) {
    $fields[] = "password = ?";
    $hashed = password_hash($data->password, PASSWORD_DEFAULT);
    $params[] = $hashed;
    $types .= "s";
}

// If no valid fields provided
if (empty($fields)) {
    sendResponse("error", "No valid fields to update");
}

// Build SQL dynamically
$sql = "UPDATE users SET " . implode(", ", $fields) . " WHERE id = ?";

// Add user_id to params
$params[] = $user_id;
$types .= "i";

// Prepare statement
$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);

if ($stmt->execute()) {
    sendResponse("success", "Profile updated successfully");
} else {
    sendResponse("error", "Failed to update profile: " . $stmt->error);
}