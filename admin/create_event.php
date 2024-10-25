<?php
session_start();
if ($_SESSION['role'] != 'admin') {
    header('Location: ../login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require '../config.php';

    $name = $_POST['name'];
    $description = $_POST['description'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $location = $_POST['location'];
    $max_participants = $_POST['max_participants'];
    $status = $_POST['status'];

    // Image upload
    $image_path = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "uploads/";
        $image_path = $target_dir . basename($_FILES["image"]["name"]);

        // Ensure the uploads directory exists
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        // Move uploaded file to target directory
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $image_path)) {
            $image_path = $target_dir . $_FILES["image"]["name"]; // Update path if needed
        } else {
            echo "Error uploading the file.";
            exit();
        }
    }

    $sql = "INSERT INTO events (name, description, date, time, location, max_participants, status, image_path)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssss", $name, $description, $date, $time, $location, $max_participants, $status, $image_path);

    if ($stmt->execute()) {
        header('Location: dashboard.php');
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/createEvent.css">
</head>

<body id="createPage">
    <div id="createBox">
        <form method="POST" action="create_event.php" enctype="multipart/form-data">
            <div id="formContainer">
                <div id="column1">
                    <div id="eventName">
                        <input type="text" name="name" placeholder="Event Name" required>
                        <textarea name="description" placeholder="Event Description"></textarea>
                    </div>
                </div>
                <div id="column2">
                    <div id="eventDetail">
                        <input type="date" name="date" required>
                        <input type="time" name="time" required>
                        <input type="text" name="location" placeholder="Event Location" required>
                    </div>
                </div>
            </div>
            <div id="otherDetails">
                <input type="number" name="max_participants" placeholder="Max Participants" required>
                <select name="status" required>
                    <option value="open">Open</option>
                    <option value="closed">Closed</option>
                    <option value="canceled">Canceled</option>
                </select>
                <!-- Add image upload input -->
                <input type="file" name="image" accept="image/*" required>
            </div>
            <button type="submit">Create Event</button>
            <button type="button" onclick="window.location.href='dashboard.php'">Cancel</button>
        </form>
    </div>
</body>

</html>
