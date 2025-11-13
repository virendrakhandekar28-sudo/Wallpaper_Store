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
    <title>User Profile</title>
</head>
<body>
    <h1>User Profile</h1>
    <p>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
    <p>Email: <strong><?php echo htmlspecialchars($_SESSION['email']); ?></strong></p>
    <a href="logout.php">Logout</a>

    <footer>
        <p>&copy; 2024 Wallpaper Store</p>
    </footer>

</body>
</html>
