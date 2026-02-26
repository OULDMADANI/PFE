<?php
require "../utils/helpers.php";
require "../utils/response.php";
require "../middleware/auth_middleware.php";
require "../config/db.php";

// Get authenticated user ID
$admin_id = checkAuth(); // returns user ID if authenticated, else exits with Unauthorized

// Check if user is admin
$stmt = $conn->prepare("SELECT role FROM users WHERE id = ?");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user['role'] !== 'admin') {
    sendResponse("error", "Unauthorized. Admin access required");
}
//Fetch statistics in try-catch
try {
    // Total users
    $result = $conn->query("SELECT COUNT(*) AS total_users FROM users");
    $total_users = $result->fetch_assoc()['total_users'];

    // Total devices
    $result = $conn->query("SELECT COUNT(*) AS total_devices FROM iot_device");
    $total_devices = $result->fetch_assoc()['total_devices'];

    // Total sensor readings
    $result = $conn->query("SELECT COUNT(*) AS total_sensor_data FROM sensor_data");
    $total_sensor_data = $result->fetch_assoc()['total_sensor_data'];

    // Total notifications
    $result = $conn->query("SELECT COUNT(*) AS total_notifications FROM notifications");
    $total_notifications = $result->fetch_assoc()['total_notifications'];

    $stats = [
        "total_users" => (int)$total_users,
        "total_devices" => (int)$total_devices,
        "total_sensor_data" => (int)$total_sensor_data,
        "total_notifications" => (int)$total_notifications
    ];

    sendResponse("success", "System statistics fetched", $stats);

} catch (Exception $e) {
    sendResponse("error", "Server error: " . $e->getMessage());
}