<?php 
session_start();  

// Redirect to login if the user is not logged in
if (!isset($_SESSION['username'])) {     
    header("Location: login.php");     
    exit(); 
} 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="style.css"> <!-- Link to your CSS file if needed -->
</head>
<body>
    <header>
        <h1>Welcome to Your Dashboard</h1>
        <div class="profile">
            <span>Hello, <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
            <a href="logout.php">Logout</a>
        </div>
    </header>

    <main>
        <h2>Your Profile</h2>
        <p>You are now logged in. You can return to the homepage.</p>
        <p>Your current email: <strong><?php echo htmlspecialchars($_SESSION['email']); ?></strong></p>

        <!-- Redirecting to index.php -->
        <h3>Profile Options</h3>
        <ul>
            <li><a href="index.php">Return to Homepage</a></li>
            <!-- Add more options as needed -->
        </ul>
    </main>

    <footer>
        <p>&copy; 2024 Wallpaper Store</p>
    </footer>
</body>
</html>
