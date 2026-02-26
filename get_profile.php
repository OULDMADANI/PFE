<?php
// Include helpers and auth middleware
require "../utils/helpers.php";
require "../utils/response.php";
require "../middleware/auth_middleware.php";


$user_id = checkAuth(); 

// Connect to database
require "../config/db.php";

try {
    $stmt = $conn->prepare("SELECT id, username, email, role, created_at FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        sendResponse("error", "User not found");
    }

    $user = $result->fetch_assoc();

    sendResponse("success", "User profile fetched", $user);

} catch (Exception $e) {
    sendResponse("error", "Server error: " . $e->getMessage());
}