<?php
session_start();

// Include database connection
include 'db.php';

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php'); // Redirect to login page
    exit;
}

// Get the image ID from the URL
$image_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch the image details from the database
$sql = "SELECT title, description, image_url, amount FROM wallpapers WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $image_id);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($title, $description, $image_url, $amount);
$stmt->fetch();

if ($stmt->num_rows === 0) {
    echo "Wallpaper not found.";
    exit;
}

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title); ?> - Wallpaper Details</title>
    <link rel="stylesheet" href="styleimg.css">
</head>
<body>
    <header>
        <h1>Wallpaper Details</h1>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="about.php">About</a></li>
                <li><a href="contact.php">Contact</a></li>
            </ul>
        </nav>
        <div class="profile">
            <?php if (isset($_SESSION['username'])): ?>
                <span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                <button class="profile-btn" onclick="toggleProfileMenu()">Profile</button>
                <div class="profile-menu" id="profileMenu">
                    <a href="profile.php">View Profile</a>
                    <?php if ($_SESSION['role'] == 'admin'): ?>
                        <a href="admin_dashboard.php">Admin Dashboard</a>
                    <?php endif; ?>
                    <a href="logout.php">Logout</a>
                </div>
            <?php else: ?>
                <button class="profile-btn" onclick="toggleProfileMenu()">Profile</button>
                <div class="profile-menu" id="profileMenu">
                    <a href="login.php">Login</a>
                    <a href="register.php">Register</a>
                </div>
            <?php endif; ?>
        </div>
    </header>

    <main>
        <section class="wallpaper-container">
            <div class="wallpaper-item">
                <img src="<?php echo htmlspecialchars($image_url); ?>" alt="<?php echo htmlspecialchars($title); ?>">
                <h2><?php echo htmlspecialchars($title); ?></h2>
                <p><?php echo htmlspecialchars($description); ?></p>
                <p style="margin: 0;">Amount: Rs <strong><?php echo htmlspecialchars($amount); ?></strong></p>
                <button class="download-button" onclick="window.location.href='payment.php?id=<?php echo $image_id; ?>&amount=<?php echo htmlspecialchars($amount); ?>'">
                    Pay and Download
                </button>
                <button class="back-button" onclick="window.history.back()">Back to Store</button>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Wallpaper Store</p>
    </footer>

    <script>
        // Function to show the download button after payment
        function showDownloadButton() {
            document.getElementById('downloadButton').style.display = 'inline-block';
        }

        // Function to handle image download
        function downloadImage(imageUrl) {
            window.location.href = imageUrl; // Redirect to download the image
        }
    </script>
</body>
</html>

<?php
$conn->close();
?>
