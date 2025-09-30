<?php
session_start(); // Start the session at the very beginning
include 'config.php'; // Include database configuration file

$message = ''; // Initialize an empty message variable

// Show message stored in session and clear it
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = "SELECT id, username, password FROM users WHERE username = ?";
    $stmt = mysqli_prepare($conn, $query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true); // Optional security
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_id'] = $user['id'];

            // Redirect all users to the same page to register for game
            header("Location: Gamereg.php");
            exit();
        } else {
            $_SESSION['message'] = '<div class="message-box error"><p>Invalid username or password!</p></div>';
        }
    } else {
        $_SESSION['message'] = '<div class="message-box error"><p>Database error: Unable to prepare statement.</p></div>';
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="assets/main.css">

</head>

<body>

    <?php
    if (!empty($message)) {
        echo $message;
    }
    ?>
    <div class="form-container">
        <h2>Login</h2>
        <form method="post" action="login.php">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required placeholder="input your username here!">

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required placeholder="input password here!">

            <button type="submit" name="login" class="form-btn">Login</button>
        </form>
        <p>Don't have an account? <a href="register.php">Register here</a></p>
    </div>
</body>

</html>