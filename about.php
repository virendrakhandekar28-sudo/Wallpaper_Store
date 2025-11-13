<?php 
session_start(); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Wallpaper Store</title>
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
        <section class="about">
            <h2>About Us</h2>
            <p>Welcome to the Wallpaper Store! We offer a wide variety of wallpapers to suit every style and preference. Our mission is to provide high-quality, beautiful wallpapers at affordable prices.</p>
            <p>With a passion for design and aesthetics, our team carefully curates our collection to ensure that you find the perfect wallpaper for your home or office.</p>
            <h3>Our Values</h3>
            <ul>
                <li>Quality</li>
                <li>Affordability</li>
                <li>Customer Satisfaction</li>
                <li>Innovative Design</li>
            </ul>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Wallpaper Store</p>
    </footer>

    <script src="script.js"></script>
</body>
</html>
