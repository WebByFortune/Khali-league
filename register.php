<?php
session_start();
include('config.php'); // Include your database configuration file

$message = '';

// Show message stored in session and clear it
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $cpassword = $_POST['cpassword'];

    // --- Password Validation Rules ---
    $min_length = 8;
    $has_uppercase = preg_match('/[A-Z]/', $password);
    $has_lowercase = preg_match('/[a-z]/', $password);
    $has_digit = preg_match('/\d/', $password);
    $has_special = preg_match('/[^A-Za-z0-9]/', $password); // Non-alphanumeric

    // Check if username or email already exists
    $check_query = "SELECT * FROM users WHERE email = ? OR username = ?";
    $stmt = mysqli_prepare($conn, $check_query);
    mysqli_stmt_bind_param($stmt, "ss", $email, $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        $_SESSION['message'] = '<div class="message-box error"><p>User with this email or username already exists!</p></div>';
        header("Location: register.php");
        exit();
    } elseif ($password !== $cpassword) {
        $_SESSION['message'] = '<div class="message-box error"><p>Passwords do not match!</p></div>';
        header("Location: register.php");
        exit();
    } elseif (
        strlen($password) < $min_length ||
        !$has_uppercase ||
        !$has_lowercase ||
        !$has_digit ||
        !$has_special
    ) {
        $_SESSION['message'] = '<div class="message-box error"><p>Password must be at least ' . $min_length . ' characters long and include a mix of uppercase letters, lowercase letters, numbers, and special characters.</p></div>';
        header("Location: register.php");
        exit();
    } else {
        // Hash password securely for security 
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert into user table
        $insert_query = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
        $stmt_insert = mysqli_prepare($conn, $insert_query);
        mysqli_stmt_bind_param($stmt_insert, "sss", $username, $email, $hashed_password);

        if (mysqli_stmt_execute($stmt_insert)) {
            $_SESSION['message'] = '<div class="message-box success"><p>Registration successful! You can now <a href="login.php">log in</a>.</p></div>';
        } else {
            $_SESSION['message'] = '<div class="message-box error"><p>Error: ' . mysqli_error($conn) . '</p></div>';
        }

        mysqli_stmt_close($stmt_insert);
        header("Location: register.php");
        exit();
    }

    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link rel="stylesheet" href="assets/main.css">
</head>

<body>
    <?php
    if (!empty($message)) {
        echo $message;
    }
    ?>
    <div class="form-container">
        <h2>Register</h2>
        <form method="post" action="">
            <label>Username:</label>
            <input type="text" name="username" required placeholder="input your username here!">

            <label>Email:</label>
            <input type="email" name="email" required placeholder="input your valid email here!">

            <label>Password:</label>
            <input type="password" name="password" id="password" required placeholder="Input your password here!"
                title="Password must be at least 8 characters, include uppercase, lowercase, numbers, and special characters. ">

            <label>Confirm Password:</label>
            <input type="password" name="cpassword" id="cpassword" required placeholder="confirm password here!">

            <button type="submit" name="register" class="form-btn">Register</button>
        </form>
        <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>
</body>

</html>