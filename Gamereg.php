<?php
session_start();
include('config.php');

$message = '';
$messageType = '';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    $_SESSION['redirect_message'] = "Please log in or register first to access the registration forms.";
    $_SESSION['redirect_message_type'] = "info";
    header('Location: login.php');
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['gaming-name'], $_POST['gamer-game'], $_POST['email'], $_POST['phone'], $_POST['location'], $_POST['physical-location'], $_POST['id-number'], $_POST['mpesa-message'])) {

        $gamingName = htmlspecialchars(trim($_POST['gaming-name']));
        $gamerGame = htmlspecialchars(trim($_POST['gamer-game']));
        $email = htmlspecialchars(trim($_POST['email']));
        $phone = htmlspecialchars(trim($_POST['phone']));
        $location = htmlspecialchars(trim($_POST['location']));
        $physicalLocation = htmlspecialchars(trim($_POST['physical-location']));
        $idNumber = htmlspecialchars(trim($_POST['id-number']));
        $mpesaMessage = htmlspecialchars(trim($_POST['mpesa-message']));

        if (empty($gamingName) || empty($gamerGame) || empty($email) || empty($phone) || empty($location) || empty($physicalLocation) || empty($idNumber) || empty($mpesaMessage)) {
            $message = "All fields are required for gamer registration.";
            $messageType = "error";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message = "Invalid email format.";
            $messageType = "error";
        } else {
            // check if transaction id is 10 char and include alphanumeric
            preg_match('/^([A-Z0-9]{10}) Confirmed/', $mpesaMessage, $matches);
            $transactionId = $matches[1] ?? null;

            // --- New Validation Logic for Transaction ID ---
            if (!$transactionId) {
                $message = "Could not find a valid M-Pesa transaction ID in the message. Please ensure it starts with a 10-character alphanumeric code followed by 'Confirmed'.";
                $messageType = "error";
            } elseif (strlen($transactionId) !== 10) { // Check if length is exactly 10
                $message = "The M-Pesa transaction ID must be exactly 10 characters long.";
                $messageType = "error";
            } elseif (!ctype_alnum($transactionId)) { // Check if it's alphanumeric
                $message = "The M-Pesa transaction ID must contain only letters and numbers.";
                $messageType = "error";
            } else {
                // If all checks pass, proceed with database operations
                try {
                    $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM gamer WHERE mpesa_transaction_id = ?");
                    $stmtCheck->execute([$transactionId]);
                    if ($stmtCheck->fetchColumn() > 0) {
                        $message = "This M-Pesa transaction ID has already been used for registration.";
                        $messageType = "error";
                    } else {
                        $stmt = $pdo->prepare("INSERT INTO gamerreg (gaming_name, game_registered, email, phone_number, location_county, physical_location, id_number, mpesa_confirmation_message, mpesa_transaction_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                        $stmt->execute([$gamingName, $gamerGame, $email, $phone, $location, $physicalLocation, $idNumber, $mpesaMessage, $transactionId]);

                        $message = "Gamer registration successful! Thank you for registering.";
                        $messageType = "success";
                    }
                } catch (PDOException $e) {
                    error_log("Gamer registration error: " . $e->getMessage());
                    $message = "There was an error processing your gamer registration. Please try again later. If the issue persists, contact support.";
                    $messageType = "error";
                }
            }
        }
    }
    // --- Shop Registration Form Handling ---
    if (isset($_POST['shop-name']) && !empty($_POST['shop-name'])) {
        // Similar validation and insertion for shop registration would go here
        $message = "Shop registration form submitted. Logic for shop registration needs to be implemented.";
        $messageType = "info";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Khali League - Where Gamers Unite</title>
    <link rel="stylesheet" href="assets/custom.css" />
    <link rel="stylesheet" href="assets/styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;display=swap" rel="stylesheet" />
    <style>
        .message-box {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            font-weight: bold;
        }

        .message-box.info {
            background-color: #e2f2ff;
            color: #0056b3;
            border: 1px solid #a8d7ff;
        }

        .message-box.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .message-box.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
    </style>
</head>

<body>
    <nav>
        <div class="container nav-content">
            <a href="#" class="nav-logo">Khali League</a>
            <div class="nav-links">
                <a href="about.php" class="nav-link">About</a>
                <a href="#games" class="nav-link">Games</a>
                <a href="#register" class="nav-link">Register</a>
                <a href="#contact" class="nav-link">Contact</a>
                <a href="logout.php" class="nav-link">Logout</a>
            </div>
        </div>
    </nav>

    <section id="register">
        <div class="container">
            <h2 class="section-title">Registration</h2>

            <?php
            if (!empty($message)) : ?>
                <div class="message-box <?php echo $messageType; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <div class="form-container">
                <form class="vertical-form" action="#register" method="POST">
                    <h3>Gamer Registration (500 KES)</h3>
                    <div class="form-group">
                        <label for="gamer-name">Gaming Name</label>
                        <input type="text" name="gaming-name" id="gamer-name" required />
                    </div>

                    <div class="form-group">
                        <label for="gamer-game">Select Game to Register</label>
                        <select name="gamer-game" id="gamer-game" required>
                            <option value="NBA2K">NBA2K</option>
                            <option value="FIFA">FIFA</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="gamer-email">Email</label>
                        <input type="email" name="email" id="gamer-email" required />
                    </div>

                    <div class="form-group">
                        <label for="gamer-phone">Phone Number</label>
                        <input type="tel" name="phone" id="gamer-phone" required />
                    </div>

                    <div class="form-group">
                        <label for="gamer-location">Location (County)</label>
                        <select name="location" id="gamer-location" required>
                            <option value="Mombasa">Mombasa</option>
                            <option value="Nakuru">Nakuru</option>
                            <option value="Eldoret">Eldoret</option>
                            <option value="Kisumu">Kisumu</option>
                            <option value="Nyeri">Nyeri</option>
                            <option value="Meru">Meru</option>
                            <option value="Kakamega">Kakamega</option>
                            <option value="Uasin Gishu">Uasin Gishu</option>
                            <option value="Machakos">Machakos</option>
                            <option value="Kericho">Kericho</option>
                            <option value="Embu">Embu</option>
                            <option value="Nandi">Nandi</option>
                            <option value="Bomet">Bomet</option>
                            <option value="Busia">Busia</option>
                            <option value="Homa Bay">Homa Bay</option>
                            <option value="Migori">Migori</option>
                            <option value="Siaya">Siaya</option>
                            <option value="Kitui">Kitui</option>
                            <option value="Nairobi City">Nairobi City</option>
                            <option value="Narok">Narok</option>
                            <option value="West Pokot">West Pokot</option>
                            <option value="Taita Taveta">Taita Taveta</option>
                            <option value="Kilifi">Kilifi</option>
                            <option value="Tana River">Tana River</option>
                            <option value="Lamu">Lamu</option>
                            <option value="Marsabit">Marsabit</option>
                            <option value="Isiolo">Isiolo</option>
                            <option value="Tharaka Nithi">Tharaka Nithi</option>
                            <option value="Samburu">Samburu</option>
                            <option value="Nyandarua">Nyandarua</option>
                            <option value="Kirinyaga">Kirinyaga</option>
                            <option value="Murang'a">Murang'a</option>
                            <option value="Nairobi">Nairobi</option>
                            <option value="Wajir">Wajir</option>
                            <option value="Mandera">Mandera</option>
                            <option value="Garissa">Garissa</option>
                            <option value="Kwale">Kwale</option>
                            <option value="Vihiga">Vihiga</option>
                            <option value="Bungoma">Bungoma</option>
                            <option value="Trans Nzoia">Trans Nzoia</option>
                            <option value="Elgeyo Marakwet">Elgeyo Marakwet</option>
                            <option value="Laikipia">Laikipia</option>
                            <option value="Makueni">Makueni</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="gamer-physical-location">Physical Location</label>
                        <input type="text" name="physical-location" id="gamer-physical-location" required />
                    </div>

                    <div class="form-group">
                        <label for="gamer-id-number">ID Number</label>
                        <input type="text" name="id-number" id="gamer-id-number" required />
                    </div>

                    <div class="form-group">
                        <label for="gamer-mpesa">M-Pesa Confirmation Message</label>
                        <input type="text" name="mpesa-message" id="gamer-mpesa" required />
                    </div>

                    <div class="payment-info">
                        <h3>Payment Details</h3>
                        <p>Business Number: 247247</p>
                        <p>Account Number: 697527</p>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        Complete Gamer Registration
                    </button>
                </form>
            </div>

            <div class="form-container">
                <form class="vertical-form" action="#register" method="POST">
                    <h3>Shop Registration (900 KES)</h3>
                    <div class="form-group">
                        <label for="shop-name">Shop Name</label>
                        <input type="text" name="shop-name" id="shop-name" required />
                    </div>

                    <div class="form-group">
                        <label for="shop-email">Email</label>
                        <input type="email" name="email" id="shop-email" required />
                    </div>

                    <div class="form-group">
                        <label for="shop-phone">Phone Number</label>
                        <input type="tel" name="phone" id="shop-phone" required />
                    </div>

                    <div class="form-group">
                        <label for="shop-location">Location (County)</label>
                        <select name="location" id="shop-location" required>
                            <option value="Mombasa">Mombasa</option>
                            <option value="Nakuru">Nakuru</option>
                            <option value="Eldoret">Eldoret</option>
                            <option value="Kisumu">Kisumu</option>
                            <option value="Nyeri">Nyeri</option>
                            <option value="Meru">Meru</option>
                            <option value="Kakamega">Kakamega</option>
                            <option value="Uasin Gishu">Uasin Gishu</option>
                            <option value="Machakos">Machakos</option>
                            <option value="Kericho">Kericho</option>
                            <option value="Embu">Embu</option>
                            <option value="Nandi">Nandi</option>
                            <option value="Bomet">Bomet</option>
                            <option value="Busia">Busia</option>
                            <option value="Homa Bay">Homa Bay</option>
                            <option value="Migori">Migori</option>
                            <option value="Siaya">Siaya</option>
                            <option value="Kitui">Kitui</option>
                            <option value="Nairobi City">Nairobi City</option>
                            <option value="Narok">Narok</option>
                            <option value="West Pokot">West Pokot</option>
                            <option value="Taita Taveta">Taita Taveta</option>
                            <option value="Kilifi">Kilifi</option>
                            <option value="Tana River">Tana River</option>
                            <option value="Lamu">Lamu</option>
                            <option value="Marsabit">Marsabit</option>
                            <option value="Isiolo">Isiolo</option>
                            <option value="Tharaka Nithi">Tharaka Nithi</option>
                            <option value="Samburu">Samburu</option>
                            <option value="Nyandarua">Nyandarua</option>
                            <option value="Kirinyaga">Kirinyaga</option>
                            <option value="Murang'a">Murang'a</option>
                            <option value="Nairobi">Nairobi</option>
                            <option value="Wajir">Wajir</option>
                            <option value="Mandera">Mandera</option>
                            <option value="Garissa">Garissa</option>
                            <option value="Kwale">Kwale</option>
                            <option value="Vihiga">Vihiga</option>
                            <option value="Bungoma">Bungoma</option>
                            <option value="Trans Nzoia">Trans Nzoia</option>
                            <option value="Elgeyo Marakwet">Elgeyo Marakwet</option>
                            <option value="Laikipia">Laikipia</option>
                            <option value="Makueni">Makueni</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="shop-physical-location">Physical Location</label>
                        <input type="text" name="physical-location" id="shop-physical-location" required />
                    </div>

                    <div class="form-group">
                        <label for="shop-id-number">ID Number</label>
                        <input type="text" name="id-number" id="shop-id-number" required />
                    </div>
                    <div class="form-group">
                        <label for="shop-mpesa">M-Pesa Confirmation Message</label>
                        <input type="text" name="shop-mpesa-message" id="shop-mpesa" required />
                    </div>

                    <div class="payment-info">
                        <h3>Payment Details</h3>
                        <p>Business Number: 247247</p>
                        <p>Account Number: 697527</p>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        Complete Shop Registration
                    </button>
                </form>
            </div>
        </div>
    </section>

    <script>
        window.onload = function() {
            const messageBox = document.querySelector(".message-box");
            if (messageBox) {
                document
                    .getElementById("register")
                    .scrollIntoView({
                        behavior: "smooth"
                    });
            }
        };
    </script>
</body>

</html>