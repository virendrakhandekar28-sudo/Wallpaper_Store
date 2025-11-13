<?php
session_start();

// Include database connection
include 'db.php';

// Initialize search variable
$search = isset($_POST['search']) ? $_POST['search'] : '';

// Build the SQL query for fetching wallpapers
$sql = "SELECT id, category, title, description, image_url FROM wallpapers WHERE 1=1"; // Start with a base query

if (!empty($search)) {
    $sql .= " AND (title LIKE '%" . $conn->real_escape_string($search) . "%' OR 
                   description LIKE '%" . $conn->real_escape_string($search) . "%' OR 
                   category LIKE '%" . $conn->real_escape_string($search) . "%')";
}

$sql .= " ORDER BY category"; // Order by category
$result = $conn->query($sql);

// Initialize an array to group wallpapers by category
$wallpapers_by_category = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $category = $row['category'];
        // Add wallpaper data to the respective category group
        $wallpapers_by_category[$category][] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wallpaper Store</title>
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
        <section class="search-section">
            <form method="POST" action="index.php" style="margin-top: 20px; padding-left:5%; padding-right:5%;" class="search-form">    
                <input type="text" name="search" style="width:92%; height:25px;" placeholder="Search by Title, Description or Category" value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit" style="width:6%; padding-left:1%; font-size:70%;">Search</button>
            </form>
        </section>

        <section class="gallery">
            <h2>Our Wallpapers</h2>

            <?php if (!empty($wallpapers_by_category)): ?>
                <?php foreach ($wallpapers_by_category as $category => $wallpapers): ?>
                    <h3><?php echo $category; ?></h3> <!-- Display Category Heading -->
                    <div class="wallpaper-container" id="category-<?php echo $category; ?>">
                        <?php 
                        // Display the first 8 wallpapers, and hide the rest
                        foreach (array_slice($wallpapers, 0, 8) as $wallpaper): ?>
                            <div class="wallpaper-item">
                                <a href="download_image.php?id=<?php echo $wallpaper['id']; ?>">
                                    <img src="<?php echo $wallpaper['image_url']; ?>" alt="<?php echo $wallpaper['title']; ?>">
                                </a>    
                                <p><?php echo $wallpaper['title']; ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <?php if (count($wallpapers) > 8): ?>
                        <button onclick="window.location.href='index_category.php?category=<?php echo urlencode($category); ?>'">View All</button>
                <?php endif; ?>

                <?php endforeach; ?>
            <?php else: ?>
                <p>No wallpapers available at the moment.</p>
            <?php endif; ?>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Wallpaper Store</p>
    </footer>

    <script>
        // Function to show all wallpapers in a category
        function viewAllWallpapers(category) {
            document.getElementById('hidden-wallpapers-' + category).style.display = 'block';
            document.getElementById('view-all-' + category).style.display = 'none'; // Hide the "View All" button
        }
    </script>
    <script src="script.js"></script>
</body>
</html>

<?php
$conn->close();
?>
