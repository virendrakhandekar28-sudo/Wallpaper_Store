<?php
session_start();

// Include database connection
include 'db.php';

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php'); // Redirect to login page
    exit;
}

// Get the image ID and amount from the URL
$image_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$amount = isset($_GET['amount']) ? floatval($_GET['amount']) : 0.00;

// Fetch the image details from the database
$sql = "SELECT title, image_url, amount FROM wallpapers WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $image_id);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($title, $image_url, $amount); // Bind all three fields: title, image_url, and amount
$stmt->fetch();

if ($stmt->num_rows === 0) {
    echo "Wallpaper not found.";
    exit;
}

$stmt->close();

// Check if a transaction already exists for this user and wallpaper
$username = $_SESSION['username'];
$sql = "SELECT * FROM transactions WHERE img_id = ? AND username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $image_id, $username);
$stmt->execute();
$stmt->store_result();

$hasPurchased = $stmt->num_rows > 0; // Check if the user has already purchased the wallpaper
$stmt->close();

// Process the payment (this is a placeholder for real payment logic)
// In a real application, you would integrate with a payment gateway here.
$payment_successful = true; // Assume the payment was successful for demo purposes

if ($payment_successful && !$hasPurchased) {
    // Insert transaction into the database
    $sql = "INSERT INTO transactions (tsn_id, img_id, username, amount, status) VALUES (?, ?, ?, ?, ?)";
    $status = 'completed'; // Payment status

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sisss", $tsn_id, $image_id, $username, $amount, $status);
    $stmt->execute();
    $stmt->close();
}

echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>' . ($hasPurchased ? "Wallpaper Already Purchased" : "Payment Successful") . ' - Download Your Wallpaper</title>
    <link rel="stylesheet" href="styleimg.css">
</head>
<body>
    <div class="payment-container">
        <h2>' . ($hasPurchased ? "You\'ve Already Purchased This Wallpaper!" : "Payment Successful!") . '</h2>
        <p>You have successfully paid <strong>Rs ' . htmlspecialchars($amount) . '</strong> for the wallpaper.</p>
        <h3>' . htmlspecialchars($title) . '</h3>
        <div class="wallpaper-item">
            <img src="' . htmlspecialchars($image_url) . '" alt="' . htmlspecialchars($title) . '" style="width: 100%; height: auto;">
        </div>';

if ($hasPurchased) {
    // If the user has already purchased, show the download button
    echo '<a href="' . htmlspecialchars($image_url) . '" download="wallpaper" class="download-button">Download Image</a>';
} else {
    echo '<a href="' . htmlspecialchars($image_url) . '" download="wallpaper" class="download-button">Download Image</a>';
}

echo '        <button class="back-button" onclick="window.location.href=\'index.php\'">Back to Gallery</button>
    </div>
</body>
</html>';

$conn->close();
?>
