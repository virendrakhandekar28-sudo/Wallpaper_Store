<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Include database connection
include 'db.php';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize input data
    $username = $conn->real_escape_string($_POST['username']);
    $password = $conn->real_escape_string($_POST['password']);

    // Check if the user exists
    $sql = "SELECT * FROM users WHERE username = '$username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Fetch the user data
        $user = $result->fetch_assoc();

        // Verify the password
        if (password_verify($password, $user['password'])) {
            // Successful login, set session variables
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role']; // Store the user's role (admin or user)
            $_SESSION['email'] = $user['email']; 
            $_SESSION['success'] = "You are now logged in.";

            // Redirect based on role
            if ($user['role'] == 'admin') {
                header("Location: admin_dashboard.php"); // Redirect to admin dashboard
            } else {
                header("Location: index.php"); // Redirect to user homepage
            }
        } else {
            $_SESSION['error'] = "Invalid password.";
            header("Location: login.php");
        }
    } else {
        $_SESSION['error'] = "Username not found.";
        header("Location: login.php");
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wallpaper Store</title>
    <link rel="stylesheet" href="styletest.css">
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
    <h2>Login</h2>
    <form method="POST" action="login.php">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>
    <p>Don't have an account? <a href="register.php">Register here</a></p>

    <footer>
        <p>&copy; 2024 Wallpaper Store</p>
    </footer>

    <script src="script.js"></script>

</body>
</html>