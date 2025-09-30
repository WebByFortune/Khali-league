<?php
// Start the PHP session. This must be at the very top of the script.
session_start();

// Check if the admin is NOT logged in.
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    // If not logged in, redirect to the login page.
    header("Location: login.php");
    exit(); // Stop script execution.
}

// --- CACHING CONFIGURATION ---
$cache_file = __DIR__ . '/cache/dashboard_cache.html'; // Path to your cache file
$cache_lifetime = 300; // Cache lifetime in seconds (e.g., 300 seconds = 5 minutes)

// --- CACHE LOGIC ---
// Check if cache file exists and is still fresh
if (file_exists($cache_file) && (time() - filemtime($cache_file) < $cache_lifetime)) {
    // Serve cached content
    readfile($cache_file);
    exit(); // Stop execution after serving cached content
}

// If cache is stale or doesn't exist, proceed to generate new content and cache it

// Start output buffering to capture all HTML output
ob_start();

// Database connection parameters
$servername = "localhost"; // e.g., 'localhost' or your database host
$username = "";
$password = "";
$dbname = "usersdata_db";

// Create connection to the database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check if connection was successful
if ($conn->connect_error) {
    // If connection fails, output error and stop
    ob_end_clean(); // Discard any buffered output
    die("Database Connection failed: " . $conn->connect_error);
}

// Fetch data from the 'users' table
$sql = "SELECT id, registration_type, gaming_name, selected_game, shop_name, email, phone_number, location, physical_location, id_number, mpesa_message, registration_date FROM usersdata ORDER BY registration_date DESC";
$result = $conn->query($sql);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Khali League - Admin Dashboard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

</head>

<body>
    <div class="container">
        <div class="clearfix">
            <a href="admin_logout.php" class="logout-link">Logout</a>
        </div>
        <h1>Admin Dashboard - Khali League Registrations</h1>
        <p class="welcome-message">Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?>! Here's an overview of all gamer and shop registrations.</p>

        <div class="table-container">
            <?php if ($result->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Type</th>
                            <th>Gaming Name</th>
                            <th>Selected Game</th>
                            <th>Shop Name</th>
                            <th>Email</th>
                            <th>Phone Number</th>
                            <th>Location</th>
                            <th>Physical Location</th>
                            <th>ID Number</th>
                            <th>M-Pesa Msg</th>
                            <th>Reg. Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['id']); ?></td>
                                <td><?php echo htmlspecialchars($row['registration_type']); ?></td>
                                <td><?php echo htmlspecialchars($row['gaming_name'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($row['selected_game'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($row['shop_name'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                <td><?php echo htmlspecialchars($row['phone_number']); ?></td>
                                <td><?php echo htmlspecialchars($row['location']); ?></td>
                                <td><?php echo htmlspecialchars($row['physical_location']); ?></td>
                                <td><?php echo htmlspecialchars($row['id_number']); ?></td>
                                <td><?php htmlspecialchars($row['mpesa_message']); ?></td>
                                <td><?php htmlspecialchars($row['registration_date']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="no-data-message">
                    <p>No registrations found yet.</p>
                    <a href="index.php#register" class="btn btn-primary">Go to Registration Page</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php
    // Close the database connection
    $conn->close();

    // --- CACHING LOGIC (CONTINUED) ---
    // Get the content from the output buffer
    $cached_content = ob_get_contents();

    // End buffering and send content to browser
    ob_end_flush();

    // Write the content to the cache file
    if (!file_put_contents($cache_file, $cached_content)) {
        error_log("Failed to write cache file: " . $cache_file);
    }

    ?>
</body>

</html>