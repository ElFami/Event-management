<?php
session_start();
if ($_SESSION['role'] != 'admin') {
    header('Location: ../login.php');
    exit();
}

require '../config.php';

// Check if an event ID is provided
if (isset($_GET['id'])) {
    $event_id = intval($_GET['id']);
    
    // Delete event from the database
    $delete_sql = "DELETE FROM events WHERE id=?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param('i', $event_id);

    if ($stmt->execute()) {
        // Redirect to admin dashboard after successful deletion
        header('Location: dashboard.php');
        exit();
    } else {
        // Handle error if needed
        $error_message = "Error deleting event: " . $stmt->error;
        // You might want to redirect back with an error or show a message
    }
} else {
    // Redirect if no event ID is provided
    header('Location: dashboard.php');
    exit();
}
?>
