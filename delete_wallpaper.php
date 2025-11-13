<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

include 'db.php';

if (isset($_GET['id'])) {
    $id = $conn->real_escape_string($_GET['id']);

    // Delete wallpaper
    $sql = "DELETE FROM wallpapers WHERE id = '$id'";
    if ($conn->query($sql)) {
        $_SESSION['success'] = "Wallpaper deleted successfully!";
    } else {
        $_SESSION['error'] = "Error deleting wallpaper: " . $conn->error;
    }
}

$conn->close();
header("Location: admin_dashboard.php");
