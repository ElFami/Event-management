<?php
session_start();
require '../config.php';

$user_id = $_SESSION['user_id'];
$event_id = $_POST['event_id'];
$action = $_POST['action'];

if ($action == 'register') {
    // Register the user for the event
    $sql = "INSERT INTO registrations (user_id, event_id) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $user_id, $event_id);
    $success = $stmt->execute();
} else if ($action == 'cancel') {
    // Cancel the user's registration
    $sql = "DELETE FROM registrations WHERE user_id = ? AND event_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $user_id, $event_id);
    $success = $stmt->execute();
}

// Return JSON response
echo json_encode(['success' => $success]);
?>
