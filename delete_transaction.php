<?php
session_start();
include 'db.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// Check if the transaction ID is provided
if (isset($_POST['tsn_id'])) {
    $tsn_id = $_POST['tsn_id'];

    // Delete the transaction from the database
    $sql = "DELETE FROM transactions WHERE tsn_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $tsn_id);
    
    if ($stmt->execute()) {
        // Redirect back to the transactions page with a success message
        header('Location: admin_transactions.php?message=Transaction deleted successfully.');
    } else {
        // Redirect back with an error message
        header('Location: admin_transactions.php?message=Error deleting transaction.');
    }
    
    $stmt->close();
}
$conn->close();
?>
