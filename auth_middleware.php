<?php
header("Content-Type: application/json");

// Simple token/session check
// For now, we can use a user_id sent in headers (later replace with JWT)

if (!isset(getallheaders()['User-ID'])) {
    echo json_encode([
        "status" => "error",
        "message" => "Unauthorized"
    ]);
    exit;
}

$user_id = intval(getallheaders()['User-ID']);

if ($user_id <= 0) {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid User"
    ]);
    exit;
}
?>