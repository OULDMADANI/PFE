<?php
require "../utils/helpers.php";
require "../utils/response.php";
require "../middleware/auth_middleware.php";
require "../config/db.php";

$admin_id = checkAuth(); 

// Check if user is admin
$stmt = $conn->prepare("SELECT role FROM users WHERE id = ?");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user['role'] !== 'admin') {
    sendResponse("error", "Unauthorized. Admin access required");
}

// Get JSON input
$data = json_decode(file_get_contents("php://input"));
if (!$data || !isset($data->user_id)) {
    sendResponse("error", "Invalid input. Required: user_id");
}

$delete_user_id = (int)$data->user_id;

// Prevent admin from deleting themselves
if ($delete_user_id === $admin_id) {
    sendResponse("error", "You cannot delete your own admin account");
}

// Delete user
$stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
$stmt->bind_param("i", $delete_user_id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        sendResponse("success", "User deleted successfully");
    } else {
        sendResponse("error", "User not found");
    }
} else {
    sendResponse("error", "Failed to delete user: " . $stmt->error);
}