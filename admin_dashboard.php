<?php
session_start();

// Redirect to login if the user is not logged in or not an admin
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Include database connection
include 'db.php';

// Initialize search variables
$title = isset($_POST['title']) ? $_POST['title'] : '';
$category = isset($_POST['category']) ? $_POST['category'] : '';
$description = isset($_POST['description']) ? $_POST['description'] : '';

// Build the SQL query based on search inputs
$sql = "SELECT * FROM wallpapers WHERE 1=1"; // Always true to simplify appending conditions

if (!empty($title)) {
    $title = $conn->real_escape_string($title);
    $sql .= " AND title LIKE '%$title%'";
}

if (!empty($category)) {
    $category = $conn->real_escape_string($category);
    $sql .= " AND category LIKE '%$category%'";
}

if (!empty($description)) {
    $description = $conn->real_escape_string($description);
    $sql .= " AND description LIKE '%$description%'";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <script>
        function confirmDelete(wallpaperId) {
            var confirmAction = confirm("Are you sure you want to delete this wallpaper?");
            if (confirmAction) {
                window.location.href = "delete_wallpaper.php?id=" + wallpaperId;
            }
        }
    </script>
</head>
<body>
    <header>
        <h1>Admin Dashboard</h1>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="admin_dashboard.php">Dashboard</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section style="text-align: center;">
            <h2>Add Wallpapers</h2>
            <button onclick="window.location.href='add_wallpaper.php'" style="margin-bottom: 10px;">Add New Wallpaper</button>

            <!-- New button to navigate to admin transactions -->
            <h2>Manage Transactions</h2>
            <button onclick="window.location.href='admin_transactions.php'" style="margin-bottom: 10px;">View Transactions</button>

            <!-- New button to navigate to view contact messages -->
            <h2>View Contact Messages</h2>
            <button onclick="window.location.href='view_contacts.php'" style="margin-bottom: 10px;">View Contacts</button>

            <h2>Manage Wallpapers</h2>
            <form method="POST" action="admin_dashboard.php" style="margin-bottom: 20px;" class="search-form">
                <input type="text" name="title" placeholder="Search by Title" value="<?php echo htmlspecialchars($title); ?>">
                <input type="text" name="category" placeholder="Search by Category" value="<?php echo htmlspecialchars($category); ?>">
                <input type="text" name="description" placeholder="Search by Description" value="<?php echo htmlspecialchars($description); ?>">
                <button type="submit">Search</button>
            </form>

            <div class="wallpaper-container">
                <?php
                // Fetch wallpapers based on search
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<div class='wallpaper-item'>";
                        echo "<img src='" . $row['image_url'] . "' alt='" . $row['title'] . "'>";
                        echo "<p>" . $row['title'] . "</p>";
                        echo "<button onclick=\"confirmDelete(" . $row['id'] . ")\">Delete</button>";
                        echo "      ";
                        echo "<button class=\"edit-button\" onclick=\"window.location.href='edit_wallpaper.php?id=" . $row['id'] . "'\">Edit</button>";
                        echo "</div>";
                    }
                } else {
                    echo "<p>No wallpapers found.</p>";
                }
                ?>
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
