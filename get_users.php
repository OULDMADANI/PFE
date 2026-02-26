<?php
require "../utils/helpers.php";
require "../utils/response.php";
require "../middleware/auth_middleware.php";
require "../config/db.php";

// Get authenticated user ID
$user_id = checkAuth(); // returns user ID if authenticated, else exits with Unauthorized

// Check if user is admin
$stmt = $conn->prepare("SELECT role FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user['role'] !== 'admin') {
    sendResponse("error", "Unauthorized. Admin access required");
}
//Fetch users in a try-catch block
try {
    $stmt = $conn->prepare("
        SELECT id, username, email, role, created_at
        FROM users
        ORDER BY created_at DESC
    ");
    $stmt->execute();
    $result = $stmt->get_result();

    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }

    sendResponse("success", "Users fetched successfully", $users);

} catch (Exception $e) {
    sendResponse("error", "Server error: " . $e->getMessage());
}