<?php 
session_start(); 

// Redirect to login if the user is not logged in or not an admin
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Include database connection
include 'db.php';

// Delete contact entry if ID is provided
if (isset($_GET['id'])) {
    $contactId = intval($_GET['id']);
    $sql = "DELETE FROM contact_messages WHERE id = ?"; // Updated table name
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $contactId);
    
    if ($stmt->execute()) {
        header("Location: view_contacts.php?message=Contact deleted successfully.");
    } else {
        header("Location: view_contacts.php?error=Failed to delete contact.");
    }
    $stmt->close();
}

// Fetch all contact entries
$sql = "SELECT * FROM contact_messages ORDER BY created_at DESC"; // Updated table name
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Contacts - Wallpaper Store</title>
    <link rel="stylesheet" href="styleconview.css">
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
        <section class="contact-view">
            <h2>Contact Submissions</h2>
            <?php if (isset($_GET['message'])): ?>
                <p class="success-message"><?php echo htmlspecialchars($_GET['message']); ?></p>
            <?php elseif (isset($_GET['error'])): ?>
                <p class="error-message"><?php echo htmlspecialchars($_GET['error']); ?></p>
            <?php endif; ?>

            <div class="contact-table">
                <table>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Message</th>
                        <th>Attachment</th>
                        <th>Actions</th>
                    </tr>
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['message']) . "</td>";
                            echo "<td><a href='./attachments/" . htmlspecialchars($row['attachment']) . "' download='attached_file' target='_blank'><button style='background-color:blue;'>View</button></a></td>";
                            echo "<td><button onclick=\"confirmDelete(" . $row['id'] . ")\">Delete</button></td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6'>No contacts found.</td></tr>";
                    }
                    ?>
                </table>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Wallpaper Store</p>
    </footer>

    <script>
        function confirmDelete(contactId) {
            var confirmAction = confirm("Are you sure you want to delete this contact?");
            if (confirmAction) {
                window.location.href = "view_contacts.php?id=" + contactId;
            }
        }
    </script>
    <script src="script.js"></script>
</body>
</html>

<?php
$conn->close();
?>
