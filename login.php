<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require 'config.php';

    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] == 'admin') {
                header('Location: ./admin/dashboard.php');
            } else {
                header('Location: ./user/dashboard.php');
            }
            exit();
        } else {
            echo "Invalid password!";
        }
    } else {
        echo "No user found with that email!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="css/loginStyle.css">
</head>

<body id="loginPage">
    <div id="loginBox">
        <img src="assets/loginTitle.png" class="loginTitle">
        <form method="POST" action="login.php" class="loginForm">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">
                <p class="text">Login Now!</p>
            </button>
        </form>
    </div>
    <div id="noAccount">
        <button class="noAccount" onclick="window.location.href='register.php'">Don't have an account yet?</button>
    </div>
</body>



</html>