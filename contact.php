<?php 
session_start(); 

// Include database connection
include 'db.php';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars(trim($_POST['name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $message = htmlspecialchars(trim($_POST['message']));
    
    // File upload handling
    $attachment = $_FILES['attachment'];
    $uploadDir = './attachments/';
    $uploadFile = $uploadDir . basename($attachment['name']);
    $maxFileSize = 20 * 1024 * 1024; // 20 MB

    // Check if file size is less than 20 MB
    if ($attachment['size'] > $maxFileSize) {
        $response = "File size exceeds 20 MB limit.";
    } else {
        // Move the uploaded file to the attachments directory
        if (move_uploaded_file($attachment['tmp_name'], $uploadFile)) {
            // Insert form data into the database
            $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, message, attachment) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $email, $message, $attachment['name']);

            if ($stmt->execute()) {
                $response = "Message sent successfully with attachment.";
            } else {
                $response = "Error inserting data into the database: " . $conn->error;
            }

            $stmt->close();
        } else {
            $response = "Error uploading file.";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Wallpaper Store</title>
    <link rel="stylesheet" href="stylecontact.css">
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
        <section class="contact">
            <h2 class="formtxt">Contact Us</h2>
            <form class="formtxt" id="contactForm" method="POST" enctype="multipart/form-data">
                <label class="txt" for="name">Name:</label>
                <input type="text" id="name" name="name" required>
                
                <label class="txt" for="email">Email:</label>
                <input type="email" id="email" name="email" required>
                
                <label class="txt" for="message">Message:</label>
                <textarea id="message" name="message" required></textarea>
                
                <label class="txt" for="attachment">Attachment (max 20MB):</label>
                <input type="file" id="attachment" name="attachment" accept="*/*">
                <pre></pre>
                <button class="txt" type="submit">Send Message</button>
            </form>
            <div id="form-response">
                <?php if (isset($response)): ?>
                    <p><?php echo $response; ?></p>
                <?php endif; ?>
            </div>
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
