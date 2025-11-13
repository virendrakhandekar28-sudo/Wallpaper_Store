<?php
session_start();

// Include database connection
include 'db.php';

// Get the category from the URL
$category = isset($_GET['category']) ? $conn->real_escape_string($_GET['category']) : '';

// Fetch wallpapers for the given category
$sql = "SELECT id, title, description, image_url FROM wallpapers WHERE category = '$category'";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($category); ?> Wallpapers</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Wallpaper Store</h1>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="about.php">About</a></li>
                <li><a href="contact.php">Contact</a></li>
            </ul>
        </nav>
        <div class="profile">
            <?php if (isset($_SESSION['username'])): ?>
                <span>Welcome, <?php echo $_SESSION['username']; ?></span>
                <button class="profile-btn" onclick="toggleProfileMenu()">Profile</button>
                <div class="profile-menu" id="profileMenu">
                    <a href="profile.php">View Profile</a>
                    <?php if ($_SESSION['role'] == 'admin'): ?>
                        <a href="admin_dashboard.php">Admin Dashboard</a>
                    <?php endif; ?>
                    <a href="user_transactions.php">My Transactions</a>
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
        <h2><?php echo htmlspecialchars($category); ?></h2>
        <section class="gallery">
            <?php if ($result->num_rows > 0): ?>
                <div class="wallpaper-container">
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <div class="wallpaper-item">
                            <a href="download_image.php?id=<?php echo $row['id']; ?>">
                                <img src="<?php echo $row['image_url']; ?>" alt="<?php echo $row['title']; ?>">
                            </a>
                            <p><?php echo $row['title']; ?></p>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <p>No wallpapers available for this category.</p>
            <?php endif; ?>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Wallpaper Store</p>
    </footer>

    <script src="script.js"></script>
</body>
</html>

<?php
$conn->close();
?>
