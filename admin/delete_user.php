<?php
session_start();
require '../config.php';

// Ensure the user is an admin
if ($_SESSION['role'] != 'admin') {
    header('Location: ../login.php');
    exit();
}

// Check if an ID is provided
if (isset($_GET['id'])) {
    $userId = intval($_GET['id']);
    
    // Prepare and execute the deletion query
    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);

    if ($stmt->execute()) {
        // Optionally add a success message
        header('Location: admin_dashboard.php?success=User deleted successfully');
    } else {
        // Handle errors
        header('Location: admin_dashboard.php?error=Failed to delete user');
    }

    $stmt->close();
} else {
    header('Location: admin_dashboard.php?error=No user ID provided');
}

$conn->close();
?>