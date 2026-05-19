<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit();
}
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1 class="text-center">Dashboard</h1>
        <p class="text-center">Welcome to the dashboard!</p>
        <div class="text-center">
            <a href="upload.php" class="btn btn-primary">Go to Upload Catgory</a>
            <a href="upload_item.php" class="btn btn-secondary">Go to Upload Item</a>
            <a href="category_edit.php" class="btn btn-primary">Go to Catgory Edit</a>
            <br>
            <br>
            <a href="item_edit.php" class="btn btn-secondary">Go to Item Edit</a>
            <a href="featured.php" class="btn btn-primary">edit Featured</a>
            <a href="arrange_order.php" class="btn btn-secondary"> Arrange Order</a>
            <br>
            <br>

            <a href="user.php" class="btn btn-info">Add New User</a>
            <a href="logout.php" class="btn btn-danger">Logout</a>
        </div>
    </div>
</body>
</html>
