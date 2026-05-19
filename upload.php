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
    <title>Upload Items and Categories</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .container {
            margin-top: 20px;
        }
        .alert {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center">Upload Items and Categories</h1>

        <!-- Category Upload Form -->
        <h2 class="mt-4">Add Category</h2>
        <form action="upload.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="categoryName">Category Name</label>
                <input type="text" class="form-control" id="categoryName" name="categoryName" required>
            </div>
            <div class="form-group">
                <label for="categoryLink">Category Link</label>
                <input type="text" class="form-control" id="categoryLink" name="categoryLink" required>
            </div>
            <div class="form-group">
                <label for="categoryImage">Category Image</label>
                <input type="file" class="form-control-file" id="categoryImage" name="categoryImage" accept="image/*" required>
            </div>
            <button type="submit" name="addCategory" class="btn btn-primary">Add Category</button>
        </form>

        <hr>

        <!-- Item Upload Form -->
        <h2 class="mt-4">Add Item</h2>
        <form action="upload.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="itemName">Item Name</label>
                <input type="text" class="form-control" id="itemName" name="itemName" required>
            </div>
            <div class="form-group">
                <label for="itemDescription">Item Description</label>
                <textarea class="form-control" id="itemDescription" name="itemDescription" required></textarea>
            </div>
            <div class="form-group">
                <label for="itemPrice">Item Price (LYD)</label>
                <input type="number" step="0.01" class="form-control" id="itemPrice" name="itemPrice" required>
            </div>
            <div class="form-group">
                <label for="itemCategory">Select Category</label>
                <select class="form-control" id="itemCategory" name="itemCategory" required> 
                    <?php
                    include 'db_connect.php';

                    // Fetch categories from the database
                    $sql = "SELECT * FROM category";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo '<option value="' . $row['id'] . '">' . $row['name'] . '</option>'; // Use the ID for the value
                        }
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="itemImage">Item Image</label>
                <input type="file" class="form-control-file" id="itemImage" name="itemImage" accept="image/*" required>
            </div>
            <button type="submit" name="addItem" class="btn btn-primary">Add Item</button>
        </form>

        <?php
        include 'db_connect.php';

        // Handle category submission
        if (isset($_POST['addCategory'])) {
            $categoryName = $_POST['categoryName'];
            $categoryLink = $_POST['categoryLink'];

            // Handle image upload
            $categoryImage = $_FILES['categoryImage']['name'];
            $targetDir = "img/";
            $targetFile = $targetDir . basename($categoryImage);
            $uploadOk = 1;
            $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

            // Check if image file is an actual image
            $check = getimagesize($_FILES['categoryImage']['tmp_name']);
            if ($check === false) {
                echo '<div class="alert alert-danger">File is not an image.</div>';
                $uploadOk = 0;
            }

            // Check file size
            if ($_FILES['categoryImage']['size'] > 500000) {
                echo '<div class="alert alert-danger">Sorry, your file is too large.</div>';
                $uploadOk = 0;
            }

            // Allow certain file formats
            if (!in_array($imageFileType, ['jpg', 'png', 'jpeg', 'gif'])) {
                echo '<div class="alert alert-danger">Sorry, only JPG, JPEG, PNG & GIF files are allowed.</div>';
                $uploadOk = 0;
            }

            // Upload the image if all checks are okay
            if ($uploadOk === 1) {
                if (move_uploaded_file($_FILES['categoryImage']['tmp_name'], $targetFile)) {
                    $stmt = $conn->prepare("INSERT INTO category (name, link, image) VALUES (?, ?, ?)");
                    $stmt->bind_param("sss", $categoryName, $categoryLink, $targetFile);
                    if ($stmt->execute()) {
                        echo '<div class="alert alert-success">Category added successfully!</div>';
                    } else {
                        echo '<div class="alert alert-danger">Error adding category: ' . $conn->error . '</div>';
                    }
                    $stmt->close();
                } else {
                    echo '<div class="alert alert-danger">Sorry, there was an error uploading your file.</div>';
                }
            }
        }

        // Handle item submission
        if (isset($_POST['addItem'])) {
            $itemName = $_POST['itemName'];
            $itemDescription = $_POST['itemDescription'];
            $itemPrice = $_POST['itemPrice'];
            $itemCategory = $_POST['itemCategory'];

            // Handle image upload
            $itemImage = $_FILES['itemImage']['name'];
            $targetFile = $targetDir . basename($itemImage);
            $uploadOk = 1;
            $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

            // Check if image file is an actual image
            $check = getimagesize($_FILES['itemImage']['tmp_name']);
            if ($check === false) {
                echo '<div class="alert alert-danger">File is not an image.</div>';
                $uploadOk = 0;
            }

            // Check file size
            if ($_FILES['itemImage']['size'] > 500000) {
                echo '<div class="alert alert-danger">Sorry, your file is too large.</div>';
                $uploadOk = 0;
            }

            // Allow certain file formats
            if (!in_array($imageFileType, ['jpg', 'png', 'jpeg', 'gif'])) {
                echo '<div class="alert alert-danger">Sorry, only JPG, JPEG, PNG & GIF files are allowed.</div>';
                $uploadOk = 0;
            }

            // Upload the image if all checks are okay
            if ($uploadOk === 1) {
                if (move_uploaded_file($_FILES['itemImage']['tmp_name'], $targetFile)) {
                    // Prepare the insert statement
                    $stmt = $conn->prepare("INSERT INTO item (name, description, price, category_id, image) VALUES (?, ?, ?, ?, ?)");
                    $stmt->bind_param("ssdiss", $itemName, $itemDescription, $itemPrice, $itemCategory, $targetFile);
                    if ($stmt->execute()) {
                        echo '<div class="alert alert-success">Item added successfully!</div>';
                    } else {
                        echo '<div class="alert alert-danger">Error adding item: ' . $conn->error . '</div>';
                    }
                    $stmt->close();
                } else {
                    echo '<div class="alert alert-danger">Sorry, there was an error uploading your file.</div>';
                }
            } else {
                // Prepare the insert statement even if the image upload failed
                $stmt = $conn->prepare("INSERT INTO item (name, description, price, category_id, image) VALUES (?, ?, ?, ?, ?)");
                $imagePlaceholder = ''; // Placeholder if image upload failed
                $stmt->bind_param("ssdiss", $itemName, $itemDescription, $itemPrice, $itemCategory, $imagePlaceholder);
                if ($stmt->execute()) {
                    echo '<div class="alert alert-success">Item added without image successfully!</div>';
                } else {
                    echo '<div class="alert alert-danger">Error adding item: ' . $conn->error . '</div>';
                }
                $stmt->close();
            }
        }

        // Close the database connection
        $conn->close();
        ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
