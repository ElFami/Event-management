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

    // Fetch event details from the database
    $sql = "SELECT * FROM events WHERE id = $event_id";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $event = $result->fetch_assoc();
    } else {
        // Redirect if the event is not found
        header('Location: admin_dashboard.php');
        exit();
    }
} else {
    // Redirect if no event ID is provided
    header('Location: admin_dashboard.php');
    exit();
}

// Process the form submission
// Process the form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $date = $_POST['date'];
    $location = $_POST['location'];
    $max_participants = intval($_POST['max_participants']);

    // Update event in the database
    $update_sql = "UPDATE events SET name=?, date=?, location=?, max_participants=? WHERE id=?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param('ssssi', $name, $date, $location, $max_participants, $event_id);

    if ($stmt->execute()) {
        // Redirect to admin dashboard after successful update
        header('Location: dashboard.php');
        exit(); // Make sure to call exit after header redirect
    } else {
        $error_message = "Error updating event: " . $stmt->error;
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/editEvent.css">
</head>

<body id="editPage">

    <div id="editBox">
        <?php if (isset($error_message)): ?>
            <p style="color: red;"><?php echo $error_message; ?></p>
        <?php endif; ?>
        <form method="POST" action="edit_event.php?id=<?php echo $event_id; ?>">
            <div id="column1">
                <div id="eventName">
                    <label for="name">Event Name:</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($event['name']); ?>" required>
                </div>
            </div>
            <div id="column2">
                <div id="eventDetail">
                    <label for="date">Date:</label>
                    <input type="date" id="date" name="date" value="<?php echo htmlspecialchars($event['date']); ?>" required>
                </div>
            </div>
            <div id="otherDetails">
                <label for="location">Location:</label>
                <input type="text" id="location" name="location" value="<?php echo htmlspecialchars($event['location']); ?>" required>

                <label for="max_participants">Max Participants:</label>
                <input type="number" id="max_participants" name="max_participants" value="<?php echo htmlspecialchars($event['max_participants']); ?>" required>
            </div>
            <button type="submit">Update Event</button>
            <button onclick="window.location.href='dashboard.php'">Cancel</button>
        </form>



    </div>
</body>

</html>