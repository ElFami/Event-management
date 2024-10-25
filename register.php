<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require 'config.php';

    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = 'user';

    $sql = "INSERT INTO users (name, email, password, role) VALUES ('$name', '$email', '$password', '$role')";

    if ($conn->query($sql) === TRUE) {
        header('Location: login.php');
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/registerStyle.css">
</head>

<body id="registerPage">
    <div id="registerBox">
        <img src="assets/registerTitle.png" class="registerTitle">
        <form method="POST" action="register.php" class="registerForm">
            <input type="text" name="name" placeholder="Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">
                <p class="text">Sign Up!</p>
            </button>
        </form>
    </div>
    <div id="yesAccount">
        <button class="yesAccount" onclick="window.location.href='login.php'">Already have an account?</button>
    </div>
</body>

</html>