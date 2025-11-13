<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

include 'db.php';

if (isset($_GET['id'])) {
    $id = $conn->real_escape_string($_GET['id']);

    // Fetch the wallpaper details
    $result = $conn->query("SELECT * FROM wallpapers WHERE id = '$id'");
    if ($result->num_rows > 0) {
        $wallpaper = $result->fetch_assoc();
    } else {
        $_SESSION['error'] = "Wallpaper not found.";
        header("Location: dashboard.php");
        exit();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $conn->real_escape_string($_POST['title']);
    $category = $conn->real_escape_string($_POST['category']);
    $description = $conn->real_escape_string($_POST['description']);
    $amount = floatval($_POST['amount']); // Get the new amount value
    $id = $conn->real_escape_string($_POST['id']);

    // Server-side validation for amount
    if ($amount <= 0) {
        $_SESSION['error'] = "Amount must be a positive value.";
        header("Location: edit_wallpaper.php?id=" . $id);
        exit();
    }

    // Handle image upload if a new image is uploaded
    if (!empty($_FILES["wallpaper"]["name"])) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["wallpaper"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        $check = getimagesize($_FILES["wallpaper"]["tmp_name"]);
        if ($check !== false) {
            $uploadOk = 1;
        } else {
            $uploadOk = 0;
            $_SESSION['error'] = "File is not an image.";
        }

        if ($uploadOk == 1) {
            if (move_uploaded_file($_FILES["wallpaper"]["tmp_name"], $target_file)) {
                // Update query with new image and amount
                $sql = "UPDATE wallpapers SET title = '$title', category = '$category', description = '$description', amount = '$amount', image_url = '$target_file' WHERE id = '$id'";
            }
        }
    } else {
        // Update without changing the image
        $sql = "UPDATE wallpapers SET title = '$title', category = '$category', description = '$description', amount = '$amount' WHERE id = '$id'";
    }

    if ($conn->query($sql)) {
        $_SESSION['success'] = "Wallpaper updated successfully!";
        header("Location: admin_dashboard.php");
        exit();
    } else {
        $_SESSION['error'] = "Error: " . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Wallpaper</title>
    <link rel="stylesheet" href="styletest.css">
</head>
<body>
    <h2>Edit Wallpaper</h2>
    <?php if (isset($_SESSION['error'])): ?>
        <p style="color:red;"><?php echo $_SESSION['error']; ?></p>
    <?php endif; ?>
    <form method="POST" action="edit_wallpaper.php?id=<?php echo $id; ?>" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo $id; ?>">
        <input type="text" name="title" value="<?php echo $wallpaper['title']; ?>" required>
        <input type="text" name="category" value="<?php echo $wallpaper['category']; ?>" required>
        <textarea name="description" required><?php echo $wallpaper['description']; ?></textarea>
        <input type="number" step="0.01" name="amount" min="0" value="<?php echo $wallpaper['amount']; ?>" required> <!-- New amount field -->
        <input type="file" name="wallpaper">
        <button type="submit">Update Wallpaper</button>
    </form>
    <pre>

    </pre>
    <a href="admin_dashboard.php" style="align-self:center;">Back to Dashboard</a>
</body>
</html>
