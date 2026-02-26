<?php
// Include your JSON response helper
require "../utils/response.php";

// Start session (in case you switch to session-based auth later)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if a session exists
if (isset($_SESSION['user_id'])) {
    // Session-based logout
    session_unset();
    session_destroy();
    sendResponse("success", "Logged out successfully (session destroyed).");
} else {
    // Stateless logout (header-based User-ID)
    sendResponse("success", "User logged out. Remove User-ID from client.");
}