<?php
session_start();
if ($_SESSION['role'] != 'user') {
    header('Location: ../login.php');
    exit();
}

require '../config.php';

// Fetch user data from the database using session user_id
$user_id = $_SESSION['user_id'];
$sql = "SELECT name, email FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $user_name = $user['name'];
    $user_email = $user['email'];
} else {
    echo "User not found!";
    exit();
}

// Fetch events data (your existing event-fetching code)
$sql_events = "
    SELECT e.*, 
        CASE 
            WHEN r.user_id IS NOT NULL THEN 1 
            ELSE 0 
        END AS is_registered
    FROM events e
    LEFT JOIN registrations r 
        ON e.id = r.event_id AND r.user_id = ?
    WHERE e.status = 'open'";

$stmt = $conn->prepare($sql_events);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Fetch registered events (your existing registration-fetching code)
$sql_registered_events = "
    SELECT e.* 
    FROM events e
    JOIN registrations r 
        ON e.id = r.event_id 
    WHERE r.user_id = ?";

$stmt_registered = $conn->prepare($sql_registered_events);
$stmt_registered->bind_param("i", $user_id);
$stmt_registered->execute();
$registered_result = $stmt_registered->get_result();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/userDashboard.css">
</head>

<body id="userPage">
    <div id="menu">
        <button class="logout" onclick="window.location.href='../login.php'">
            <div class="sign">
                <svg viewBox="0 0 512 512">
                    <path d="M377.9 105.9L500.7 228.7c7.2 7.2 11.3 17.1 11.3 27.3s-4.1 20.1-11.3 27.3L377.9 406.1c-6.4 6.4-15 9.9-24 9.9c-18.7 0-33.9-15.2-33.9-33.9l0-62.1-128 0c-17.7 0-32-14.3-32-32l0-64c0-17.7 14.3-32 32-32l128 0 0-62.1c0-18.7 15.2-33.9 33.9-33.9c9 0 17.6 3.6 24 9.9zM160 96L96 96c-17.7 0-32 14.3-32 32l0 256c0 17.7 14.3 32 32 32l64 0c17.7 0 32 14.3 32 32s-14.3 32-32 32l-64 0c-53 0-96-43-96-96L0 128C0 75 43 32 96 32l64 0c17.7 0 32 14.3 32 32s-14.3 32-32 32z"></path>
                </svg>
            </div>
            <div class="text">Logout</div>
        </button>
        <button class="regs" id="showRegisteredEvents">
            <svg class="icon" width="30" height="30" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M12,21.35L10.55,20.03C5.4,15.36 2,12.27 2,8.5C2,5.41 4.42,3 7.5,3C9.24,3 10.91,3.81 12,5.08C13.09,3.81 14.76,3 16.5,3C19.58,3 22,5.41 22,8.5C22,12.27 18.6,15.36 13.45,20.03L12,21.35Z"></path>
            </svg>
        </button>
        <button class="profile">
            <span class="span">👤</span>
        </button>
    </div>

    <!-- Event Divs -->
    <div id="eventBoard">
        <?php while ($event = $result->fetch_assoc()) : ?>
            <div class="event-card"
                data-event-id="<?php echo $event['id']; ?>"
                data-name="<?php echo $event['name']; ?>"
                data-date="<?php echo $event['date']; ?>"
                data-location="<?php echo $event['location']; ?>"
                data-description="<?php echo $event['description']; ?>"
                data-image-path="<?php echo '../admin/' . $event['image_path']; ?>"
                data-registered="<?php echo $event['is_registered']; ?>">


                <h3><?php echo $event['name']; ?></h3>
                <div class="event-details">
                    <p><strong>Date:</strong> <?php echo $event['date']; ?></p>
                    <p><strong>Location:</strong> <?php echo $event['location']; ?></p>
                </div>
                <a href="#" class="register-btn" id="registerBtn_<?php echo $event['id']; ?>">
                    <?php echo $event['is_registered'] ? 'Cancel Registration' : 'Register'; ?>
                </a>
            </div>
        <?php endwhile; ?>
    </div>

    <!-- Modal structure for event details -->
    <!-- Modal structure for event details -->
    <div id="eventModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h3 id="modalName"></h3>
            <img id="modalImage" src="" alt="Event Image"    />
            <p><strong>Date:</strong> <span id="modalDate"></span></p>
            <p><strong>Location:</strong> <span id="modalLocation"></span></p>
            <p><strong>Description:</strong> <span id="modalDescription"></span></p>
        </div>
    </div>


    <!-- Modal for showing registered events -->
    <div id="registeredEventsModal" class="modal">
        <div class="modal-content">
            <span class="close" id="closeRegisteredModal">&times;</span>
            <h3>Registered Events</h3>
            <div id="registeredEventsList">
                <?php while ($registered_event = $registered_result->fetch_assoc()) : ?>
                    <div class="registered-event">
                        <h4><?php echo $registered_event['name']; ?></h4>
                        <p><strong>Date:</strong> <?php echo $registered_event['date']; ?></p>
                        <p><strong>Location:</strong> <?php echo $registered_event['location']; ?></p>
                        <p><strong>Description:</strong> <?php echo $registered_event['description']; ?></p>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
    <!-- Modal for showing user profile details -->
    <!-- Modal for showing user profile details -->
    <div id="profileModal" class="modal">
        <div class="modal-content">
            <span class="close" id="closeProfileModal">&times;</span>
            <h3>User Profile</h3>
            <p><strong>Name:</strong> <span id="profileName"><?php echo htmlspecialchars($user_name); ?></span></p>
            <p><strong>Email:</strong> <span id="profileEmail"><?php echo htmlspecialchars($user_email); ?></span></p>
            <p><strong>Password:</strong> <span id="profilePassword">********</span></p> <!-- Hidden password -->
        </div>
    </div>


    <script>
        // Get modal elements
        const modal = document.getElementById('eventModal');
        const closeBtn = document.querySelector('.close');
        const registeredEventsModal = document.getElementById('registeredEventsModal');
        const closeEventModalBtn = eventModal.querySelector('.close');
        const closeRegisteredModalBtn = registeredEventsModal.querySelector('.close');
        // Get modal content elements
        const modalName = document.getElementById('modalName');
        const modalDate = document.getElementById('modalDate');
        const modalLocation = document.getElementById('modalLocation');
        const modalDescription = document.getElementById('modalDescription');


        // Event listener for event details to show modal
        document.querySelectorAll('.event-details').forEach(details => {
            details.addEventListener('click', function() {
                const card = this.parentElement;
                modalName.innerText = card.getAttribute('data-name');
                modalDate.innerText = card.getAttribute('data-date');
                modalLocation.innerText = card.getAttribute('data-location');
                modalDescription.innerText = card.getAttribute('data-description');

                // Show the modal
                modal.style.display = 'block';
            });
        });

        // Event listener for the regs button to show registered events
        document.getElementById('showRegisteredEvents').addEventListener('click', function() {
            document.getElementById('registeredEventsModal').style.display = 'block';
        });

        // Close registered events modal
        document.getElementById('closeRegisteredModal').onclick = function() {
            document.getElementById('registeredEventsModal').style.display = 'none';
        }

        closeEventModalBtn.onclick = function() {
            eventModal.style.display = 'none';
        };
        window.onclick = function(event) {
            if (event.target == eventModal) {
                eventModal.style.display = 'none';
            } else if (event.target == registeredEventsModal) {
                registeredEventsModal.style.display = 'none';
            }
        };
        // Event listener for register/cancel buttons
        // Event listener for register/cancel buttons
        document.querySelectorAll('.register-btn').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault(); // Prevent the default anchor behavior

                const card = this.parentElement;
                const eventId = card.getAttribute('data-event-id');
                const isRegistered = card.getAttribute('data-registered') == '1';
                const action = isRegistered ? 'cancel' : 'register';

                // AJAX call to register or cancel registration
                fetch(`register_event.php`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: `event_id=${eventId}&action=${action}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Reload the page to reflect updated registration status
                            location.reload();
                        } else {
                            alert('Something went wrong. Please try again.');
                        }
                    });
            });
        });
        document.addEventListener("DOMContentLoaded", function() {
            const profileModal = document.getElementById('profileModal');
            const closeProfileModalBtn = document.getElementById('closeProfileModal');
            const profileButton = document.querySelector('.profile');

            // Event listener to open profile modal
            profileButton.addEventListener('click', function() {
                profileModal.style.display = 'block';
            });

            // Event listener to close profile modal when 'x' is clicked
            closeProfileModalBtn.onclick = function() {
                profileModal.style.display = 'none';
            };

            // Close modal if clicking outside of modal content
            window.onclick = function(event) {
                if (event.target == profileModal) {
                    profileModal.style.display = 'none';
                }
            };
        });
        // Event listener for event details to show modal
        document.querySelectorAll('.event-card').forEach(card => {
    card.addEventListener('click', function() {
        modalName.innerText = card.getAttribute('data-name');
        modalDate.innerText = card.getAttribute('data-date');
        modalLocation.innerText = card.getAttribute('data-location');
        modalDescription.innerText = card.getAttribute('data-description');

        // Set the image source
        const imagePath = card.getAttribute('data-image-path');
        document.getElementById('modalImage').src = imagePath; // Set the image source for the modal

        // Show the modal
        modal.style.display = 'block';
    });
});

    </script>
</body>

</html>