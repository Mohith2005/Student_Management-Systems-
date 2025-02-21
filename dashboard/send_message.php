<?php
session_start();
require_once "../config.php";

// Check if user is logged in
if (!isset($_SESSION['student_id']) && !isset($_SESSION['faculty_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Get POST data
$receiver_id = isset($_POST['receiver_id']) ? (int)$_POST['receiver_id'] : 0;
$content = isset($_POST['content']) ? trim($_POST['content']) : '';
$receiver_type = isset($_POST['receiver_type']) ? $_POST['receiver_type'] : '';

// Validate input
if (!$receiver_id || !$content || !$receiver_type) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

// Set sender details based on session
if (isset($_SESSION['student_id'])) {
    $sender_id = $_SESSION['student_id'];
    $sender_type = 'student';
} else {
    $sender_id = $_SESSION['faculty_id'];
    $sender_type = 'faculty';
}

// Insert message
$sql = "INSERT INTO messages (sender_id, receiver_id, sender_type, receiver_type, content, sent_time) 
        VALUES (?, ?, ?, ?, ?, NOW())";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "iisss", $sender_id, $receiver_id, $sender_type, $receiver_type, $content);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode(['success' => true, 'message' => 'Message sent successfully']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to send message']);
}
?>
