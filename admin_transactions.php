<?php
session_start();

// Include database connection
include 'db.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php'); // Redirect to login page
    exit;
}

// Fetch all transactions, including deleted images
$sql = "SELECT t.tsn_id, t.img_id, t.username, t.amount, t.status, t.transaction_date, w.title, w.image_url 
        FROM transactions t
        LEFT JOIN wallpapers w ON t.img_id = w.id
        ORDER BY t.transaction_date DESC"; // Show recent transactions first
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Transactions</title>
    <link rel="stylesheet" href="styletableadm.css">
</head>
<body>
    <header>
        <h1>Admin Transactions</h1>
        <nav>
            <a href="admin_dashboard.php" class="download-button">Dashboard</a>
            <a href="logout.php" class="download-button">Logout</a></li>
        </nav>
    </header>

    <main>
        <section class="transactions-container">
            <h2>All Transactions</h2>
            <table>
                <thead>
                    <tr>
                        <th>Image</th> <!-- Changed to display image -->
                        <th>Transaction ID</th>
                        <th>Username</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Transaction Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <?php if (isset($row['image_url'])): ?>
                                <img src="<?php echo htmlspecialchars($row['image_url']); ?>" alt="<?php echo htmlspecialchars($row['title'] ?? 'Deleted Image'); ?>" style="width: 50px; height: auto;">
                            <?php else: ?>
                                <span>Deleted Image</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($row['tsn_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td>Rs <?php echo htmlspecialchars($row['amount']); ?></td>
                        <td><?php echo htmlspecialchars($row['status']); ?></td>
                        <td><?php echo htmlspecialchars($row['transaction_date']); ?></td>
                        <td>
                            <form action="delete_transaction.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this transaction?');">
                                <input type="hidden" name="tsn_id" value="<?php echo htmlspecialchars($row['tsn_id']); ?>">
                                <button type="submit" class="delete-button">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <button class="back-button" onclick="window.location.href='admin_dashboard.php'">Back to Dashboard</button>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Wallpaper Store</p>
    </footer>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
