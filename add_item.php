<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Item</title>
    <style>
        body { font-family: sans-serif; }
        label { display: block; margin-bottom: 5px; }
        input[type="text"], input[type="number"], select, textarea {
            width: 100%; padding: 8px; margin-bottom: 10px; box-sizing: border-box;
        }
        button { padding: 10px 20px; background-color: #4CAF50; color: white; border: none; cursor: pointer; }
        .error { color: red; }
        .success { color: green; }
    </style>
</head>
<body>

<h1>Add New Item</h1>

<form action="add_item.php" method="post" enctype="multipart/form-data">
    <label for="name">Item Name:</label>
    <input type="text" name="name" id="name" required>

    <label for="description">Description:</label>
    <textarea name="description" id="description" required></textarea>

    <label for="price">Price (LYD):</label>
    <input type="number" name="price" id="price" step="0.01" required>

    <label for="category">Category:</label>
    <select name="category" id="category" required>
        <?php
        // Establish database connection
        include 'db_connect.php';
        // Fetch categories from the database
        $sql = "SELECT id, name FROM category";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<option value='" . $row["id"] . "'>" . $row["name"] . "</option>";
            }
        } else {
            echo "<option value=''>No categories found</option>";
        }
        ?>
    </select>

    <label for="image">Image:</label>
    <input type="file" name="image" id="image" accept="image/*" required>

    <button type="submit">Add Item</button>
</form>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Re-establish database connection for form handling
    include 'db_connect.php';

    // Gather form data
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category_id = $_POST['category'];
    $image = $_FILES['image'];

    $targetDir = "img/";
    $targetFile = $targetDir . basename($image["name"]);
    $uploadOk = 1;

    // Check if image is an actual image
    $check = getimagesize($image["tmp_name"]);
    if ($check === false) {
        echo "<div class='error'>File is not an image.</div>";
        $uploadOk = 0;
    }

    // Check file size
    if ($image["size"] > 5000000) {
        echo "<div class='error'>Sorry, your file is too large.</div>";
        $uploadOk = 0;
    }

    // Allow certain file formats
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    if (!in_array($imageFileType, ["jpg", "jpeg", "png", "gif"])) {
        echo "<div class='error'>Sorry, only JPG, JPEG, PNG & GIF files are allowed.</div>";
        $uploadOk = 0;
    }

    // Attempt to upload file and insert into database
    if ($uploadOk == 1) {
        if (move_uploaded_file($image["tmp_name"], $targetFile)) {
            // Debugging: Check variable values
            echo "<div class='debug'>Name: $name</div>";
            echo "<div class='debug'>Description: $description</div>";
            echo "<div class='debug'>Price: $price</div>";
            echo "<div class='debug'>Category ID: $category_id</div>";
            echo "<div class='debug'>Target File: $targetFile</div>";

            // Prepare and bind the SQL statement

        // Prepare and bind the SQL statement
        $stmt = $conn->prepare("INSERT INTO item (name, description, price, category_id, image) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdiss", $name, $description, $price, $category_id, $targetFile);

        // Debugging: Check SQL statement
        if ($stmt === false) {
            echo "<div class='error'>Error preparing statement: " . $conn->error . "</div>";
        }

        // Execute the statement and check for errors
        if ($stmt->execute()) {
            echo "<div class='success'>Item added successfully!</div>";
        } else {
            echo "<div class='error'>Error executing query: " . $stmt->error . "</div>";
        }
        $stmt->close();
    } else {
        echo "<div class='error'>Sorry, there was an error uploading your file.</div>";
    }
} else {
    echo "<div class='error'>File upload failed due to previous errors.</div>";
}

// Close the database connection
$conn->close();
}
?>

</body>
</html>

</body>
</html>
