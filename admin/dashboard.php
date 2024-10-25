<?php
session_start(); // Start the session only once

if ($_SESSION['role'] != 'admin') {
    header('Location: ../login.php');
    exit();
}

require '../config.php';

// Fetch all events
$sql = "SELECT * FROM events";
$result = $conn->query($sql);

// Fetch all users and their registered events
$sql_users = "
    SELECT u.id AS user_id, u.name AS user_name, u.email AS user_email, e.name AS event_name
    FROM users u
    LEFT JOIN registrations r ON u.id = r.user_id
    LEFT JOIN events e ON r.event_id = e.id
    ORDER BY u.name, e.name";
$users_result = $conn->query($sql_users);

// No need to call session_start() or require config.php again
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/adminDashboard.css">
</head>

<body id="adminPage">
    <button class="logout" onclick="window.location.href='../login.php'">
        <div class="sign">
            <svg viewBox="0 0 512 512">
                <path
                    d="M377.9 105.9L500.7 228.7c7.2 7.2 11.3 17.1 11.3 27.3s-4.1 20.1-11.3 27.3L377.9 406.1c-6.4 6.4-15 9.9-24 9.9c-18.7 0-33.9-15.2-33.9-33.9l0-62.1-128 0c-17.7 0-32-14.3-32-32l0-64c0-17.7 14.3-32 32-32l128 0 0-62.1c0-18.7 15.2-33.9 33.9-33.9c9 0 17.6 3.6 24 9.9zM160 96L96 96c-17.7 0-32 14.3-32 32l0 256c0 17.7 14.3 32 32 32l64 0c17.7 0 32 14.3 32 32s-14.3 32-32 32l-64 0c-53 0-96-43-96-96L0 128C0 75 43 32 96 32l64 0c17.7 0 32 14.3 32 32s-14.3 32-32 32z"></path>
            </svg>
        </div>

        <div class="text">Logout</div>
    </button>
    <div id="managing">
        <div id="export">
            <a href="export_participants.php" class="export" tabindex="0">
            
            </a>
            <a style="color: white;">Export User</a>
        </div>
    </div>

    <ul class="listUser" onclick="openUserModal()">
        <li style="--i:#ffa9c6;--j:#f434e2;">
            <span class="icon">👥</span>
            <span class="title">USER</span>
        </li>
    </ul>

    <div id="userModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeUserModal()">&times;</span>
            <h2>Registered Users</h2>
            <table>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Registered Events</th>
                    <th>Action</th>
                </tr>
                <?php while ($user = $users_result->fetch_assoc()) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['user_name']); ?></td>
                        <td><?php echo htmlspecialchars($user['user_email']); ?></td>
                        <td><?php echo htmlspecialchars($user['event_name'] ?: 'None'); ?></td>
                        <td>
                            <button onclick="confirmDeleteUser(<?php echo $user['user_id']; ?>)">Delete</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        </div>
    </div>

    <div id="eventBoard">
        <div id="tableContainer">
            <table>
                <tr>
                    <th>Event Name</th>
                    <th>Date</th>
                    <th>Location</th>
                    <th>Max Participants</th>
                    <th>Registrants</th>
                    <th>Edit</th>
                    <th>Delete</th>
                </tr>
                <?php while ($event = $result->fetch_assoc()) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($event['name']); ?></td>
                        <td><?php echo htmlspecialchars($event['date']); ?></td>
                        <td><?php echo htmlspecialchars($event['location']); ?></td>
                        <td><?php echo htmlspecialchars($event['max_participants']); ?></td>
                        <td>
                            <?php
                            $event_id = $event['id'];
                            $sql2 = "SELECT COUNT(*) AS registrant_count FROM registrations WHERE event_id = $event_id";
                            $registrants = $conn->query($sql2)->fetch_assoc();
                            echo htmlspecialchars($registrants['registrant_count']);
                            ?>
                        </td>
                        <td>
                            <div id="edit">
                                <button class="editEvent" onclick="window.location.href='edit_event.php?id=<?php echo $event_id; ?>'">
                                    <span class="bar bar1"></span>
                                    <span class="bar bar2"></span>
                                    <span class="bar bar1"></span>
                                </button>
                                <span>Edit</span>
                            </div>
                        </td>
                        <td>
                            <div id="delete">
                                <button class="deleteEvent" onclick="confirmDelete(<?php echo $event_id; ?>)">
                                    <svg viewBox="0 0 15 17.5" height="17.5" width="15" xmlns="http://www.w3.org/2000/svg" class="icon">
                                        <path transform="translate(-2.5 -1.25)" d="M15,18.75H5A1.251,1.251,0,0,1,3.75,17.5V5H2.5V3.75h15V5H16.25V17.5A1.251,1.251,0,0,1,15,18.75ZM5,5V17.5H15V5Zm7.5,10H11.25V7.5H12.5V15ZM8.75,15H7.5V7.5H8.75V15ZM12.5,2.5h-5V1.25h5V2.5Z" id="Fill"></path>
                                    </svg>
                                </button>
                                <br />
                                <span>Delete</span>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        </div>
        <div id="managing">
            <div id="add">
                <a href="create_event.php" class="addEvent" tabindex="0">
                    <svg class="plusIcon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30 30">
                        <g mask="url(#mask0_21_345)">
                            <path d="M13.75 23.75V16.25H6.25V13.75H13.75V6.25H16.25V13.75H23.75V16.25H16.25V23.75H13.75Z"></path>
                        </g>
                    </svg>
                </a>
                <br />
                <span>Add Event</span>
            </div>
        </div>
    </div>
</body>

<script>
    function confirmDelete(eventId) {
        if (confirm("Are you sure you want to delete this event?")) {
            window.location.href = 'delete_event.php?id=' + eventId;
        }
    }

    function confirmDeleteUser(userId) {
        if (confirm("Are you sure you want to delete this user account?")) {
            window.location.href = 'delete_user.php?id=' + userId;
        }
    }

    function openUserModal() {
        document.getElementById("userModal").style.display = "block";
    }

    function closeUserModal() {
        document.getElementById("userModal").style.display = "none";
    }

    // Close the modal when the user clicks anywhere outside of it
    window.onclick = function(event) {
        const modal = document.getElementById("userModal");
        if (event.target == modal) {
            closeUserModal();
        }
    }
</script>

</html>