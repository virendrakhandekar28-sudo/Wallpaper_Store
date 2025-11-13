<?php
session_start();

// Include database connection
include 'db.php';

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php'); // Redirect to login page
    exit;
}

// Get the logged-in username
$username = $_SESSION['username'];

// Fetch user's transactions from the database, ordered by transaction_date descending
$sql = "SELECT t.tsn_id, t.img_id, t.amount, t.status, t.transaction_date, w.title, w.image_url
        FROM transactions t
        JOIN wallpapers w ON t.img_id = w.id
        WHERE t.username = ?
        ORDER BY t.transaction_date DESC"; // Order by transaction date descending
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Transactions</title>
    <link rel="stylesheet" href="styletable.css">
</head>
<body>
    <div class="transactions-container">
        <h2>Your Transactions</h2>
        <table>
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Transaction Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>';

// Display each transaction
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $imageId = htmlspecialchars($row['img_id']);
        $amount = htmlspecialchars($row['amount']);
        $status = htmlspecialchars($row['status']);
        $transactionDate = htmlspecialchars($row['transaction_date']);
        $imageTitle = htmlspecialchars($row['title']);
        $imageUrl = htmlspecialchars($row['image_url']);

        echo '<tr>
                <td><img src="' . $imageUrl . '" alt="' . $imageTitle . '" style="width: 100px; height: auto;"></td>
                <td>' . $amount . '</td>
                <td>' . $status . '</td>
                <td>' . $transactionDate . '</td>
                <td><a href="payment.php?id=' . $imageId . '&amount='. $amount .'" class="go-to-download-button">Go to Download Page</a></td>
              </tr>';
    }
} else {
    echo '<tr><td colspan="5">No transactions found.</td></tr>';
}

echo '        </tbody>
        </table>
        <button class="back-button" onclick="window.location.href=\'index.php\'">Back to Store</button>
    </div>
</body>
</html>';

$stmt->close();
$conn->close();
?>
