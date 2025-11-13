<?php
session_start();

// Check if the user is an admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// Include database connection
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $conn->real_escape_string($_POST['title']);
    $category = $conn->real_escape_string($_POST['category']);
    $description = $conn->real_escape_string($_POST['description']);
    $amount = $conn->real_escape_string($_POST['amount']); // New amount field for the price

    // File upload
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["wallpaper"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if image file is a valid image
    $check = getimagesize($_FILES["wallpaper"]["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        $uploadOk = 0;
        $_SESSION['error'] = "File is not an image.";
    }

    // Allow only certain file formats
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
        $uploadOk = 0;
        $_SESSION['error'] = "Only JPG, JPEG, and PNG files are allowed.";
    }

    if ($uploadOk == 1) {
        if (move_uploaded_file($_FILES["wallpaper"]["tmp_name"], $target_file)) {
            // Insert into database with amount field
            $sql = "INSERT INTO wallpapers (title, category, description, image_url, amount) 
                    VALUES ('$title', '$category', '$description', '$target_file', '$amount')";
            if ($conn->query($sql)) {
                $_SESSION['success'] = "Wallpaper added successfully!";
                header("Location: add_wallpaper.php"); // Redirect to prevent form resubmission
                exit();
            } else {
                $_SESSION['error'] = "Error: " . $conn->error;
            }
        } else {
            $_SESSION['error'] = "Error uploading the image.";
        }
    }
}

// Clear session messages
if (isset($_SESSION['success'])) {
    $successMessage = $_SESSION['success'];
    unset($_SESSION['success']);
}
if (isset($_SESSION['error'])) {
    $errorMessage = $_SESSION['error'];
    unset($_SESSION['error']);
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Wallpaper</title>
    <link rel="stylesheet" href="styletest.css">
</head>
<body>
    <h2>Add New Wallpaper</h2>
    <?php if (isset($successMessage)): ?>
        <p style="color:green;"><?php echo $successMessage; ?></p>
    <?php endif; ?>
    <?php if (isset($errorMessage)): ?>
        <p style="color:red;"><?php echo $errorMessage; ?></p>
    <?php endif; ?>
    <form method="POST" action="add_wallpaper.php" enctype="multipart/form-data">
        <input type="text" name="title" placeholder="Title" required>
        <input type="text" name="category" placeholder="Category" required>
        <textarea name="description" placeholder="Description" required></textarea>
        <input type="number" step="0.01" name="amount" placeholder="Amount (Price In Rs)" min="0" required> <!-- New amount field -->
        <input type="file" name="wallpaper" required>
        <button type="submit">Add Wallpaper</button>
    </form>
    <pre>

    </pre>
    <a href="admin_dashboard.php" style="align-self:center;">Back to Dashboard</a>
</body>
</html>
