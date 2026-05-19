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
    <title>Edit Items</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        /* Style for the floating button */
        .floating-btn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Edit Items</h2>

        <?php
        // Enable error reporting for better debugging
        error_reporting(E_ALL);
        ini_set('display_errors', 1);

        include 'db_connect.php';

        // Handle individual update request
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] == 'update') {
            $id = intval($_POST['id']);
            $name = $conn->real_escape_string($_POST['name']);
            $description = $conn->real_escape_string($_POST['description']);
            $price = floatval($_POST['price']);
            $category_id = intval($_POST['category_id']);
            $image = '';

            // Handle image upload
            if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
                $targetDir = "img/";
                $targetFile = $targetDir . basename($_FILES["image"]["name"]);
                move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile);
                $image = $conn->real_escape_string($targetFile);
            } else {
                $image = $conn->real_escape_string($_POST['existing_image']);
            }

            $sql = "UPDATE item SET 
                    name = '$name', 
                    description = '$description', 
                    price = $price, 
                    category_id = $category_id, 
                    image = '$image' 
                    WHERE id = $id";

            if ($conn->query($sql) === TRUE) {
                echo '<div class="alert alert-success">Item updated successfully!</div>';
            } else {
                echo "<div class=\"alert alert-danger\">Error updating item: {$conn->error}</div>";
            }
        }

        // Handle bulk update request
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] == 'update_all') {
            foreach ($_POST['items'] as $item) {
                $id = intval($item['id']);
                $name = $conn->real_escape_string($item['name']);
                $description = $conn->real_escape_string($item['description']);
                $price = floatval($item['price']);
                $category_id = intval($item['category_id']);
                $image = $conn->real_escape_string($item['existing_image']);

                // Handle image upload if a new image is provided
                if (isset($_FILES['items']['tmp_name'][$id]['image']) && $_FILES['items']['tmp_name'][$id]['image'] != '') {
                    $targetDir = "img/";
                    $targetFile = $targetDir . basename($_FILES['items']['name'][$id]['image']);
                    move_uploaded_file($_FILES['items']['tmp_name'][$id]['image'], $targetFile);
                    $image = $conn->real_escape_string($targetFile);
                }

                $sql = "UPDATE item SET 
                        name = '$name', 
                        description = '$description', 
                        price = $price, 
                        category_id = $category_id, 
                        image = '$image' 
                        WHERE id = $id";

                if (!$conn->query($sql)) {
                    echo "<div class=\"alert alert-danger\">Error updating item with ID {$id}: {$conn->error}</div>";
                }
            }
            echo '<div class="alert alert-success">All items updated successfully!</div>';
        }

        // Handle delete request
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] == 'delete') {
            $id = intval($_POST['id']);
            $sql = "DELETE FROM item WHERE id = $id";
            if ($conn->query($sql) === TRUE) {
                echo '<div class="alert alert-success">Item deleted successfully!</div>';
            } else {
                echo "<div class=\"alert alert-danger\">Error deleting item: {$conn->error}</div>";
            }
        }

        // Fetch categories for the filter
        $categoriesSql = "SELECT id, name FROM category";
        $categoriesResult = $conn->query($categoriesSql);

        // Determine the selected category for filtering
        $selectedCategory = isset($_GET['category']) ? intval($_GET['category']) : 0;

        // Fetch items based on selected category
        $sql = "SELECT item.id, item.name, item.description, item.price, item.image, item.category_id, category.name AS category_name
                FROM item
                LEFT JOIN category ON item.category_id = category.id" . 
                ($selectedCategory > 0 ? " WHERE item.category_id = $selectedCategory" : "");

        $result = $conn->query($sql);
        ?>

        <form method="GET" class="mb-3">
            <label for="category" class="form-label">Filter by Category:</label>
            <select name="category" id="category" class="form-select" onchange="this.form.submit()">
                <option value="0">All Categories</option>
                <?php while ($category = $categoriesResult->fetch_assoc()): ?>
                    <option value="<?= $category['id'] ?>" <?= ($selectedCategory == $category['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($category['name']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </form>

        <?php if ($result && $result->num_rows > 0): ?>
            <table class="table table-bordered mt-3">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Price</th>
                        <th>Category</th>
                        <th>Image</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr data-id="<?= $row['id'] ?>">
                            <td><?= $row['id'] ?></td>
                            <td><input type="text" class="form-control" value="<?= htmlspecialchars($row['name']) ?>" /></td>
                            <td><textarea class="form-control" rows="2"><?= htmlspecialchars($row['description']) ?></textarea></td>
                            <td><input type="number" step="0.01" class="form-control" value="<?= htmlspecialchars($row['price']) ?>" /></td>
                            <td>
                                <select class="form-select">
                                    <?php
                                    // Reset categories query to fetch all categories
                                    $categoriesResult->data_seek(0); // Reset result pointer
                                    while ($category = $categoriesResult->fetch_assoc()): ?>
                                        <option value="<?= $category['id'] ?>" <?= ($row['category_id'] == $category['id']) ? 'selected' : '' ?>><?= htmlspecialchars($category['name']) ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </td>
                            <td>
                                <input type="file" class="form-control" accept="image/*" />
                                <input type="hidden" name="existing_image" value="<?= htmlspecialchars($row['image']) ?>" />
                            </td>
                            <td>
                                <button type="button" class="btn btn-success save-btn">Save</button>
                                <form action="" method="POST" style="display:inline;">
                                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                    <input type="hidden" name="action" value="delete">
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this item?');">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="alert alert-warning">No items found.</div>
        <?php endif; ?>

        <!-- Floating Save All Button -->
        <button type="button" class="btn btn-primary floating-btn" id="save-all-btn">Save All Changes</button>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Save All Changes Button Logic
        $('#save-all-btn').on('click', function() {
            const rows = $('tr[data-id]'); // Select all rows with data-id attribute
            const formData = new FormData();
            let hasChanges = false;

            rows.each(function(index, row) {
                const $row = $(row);
                const id = $row.data('id');
                const name = $row.find('input[type="text"]').eq(0).val();
                const description = $row.find('textarea').val();
                const price = $row.find('input[type="number"]').val();
                const category_id = $row.find('select').val();
                const imageInput = $row.find('input[type="file"]')[0];
                const existingImage = $row.find('input[name="existing_image"]').val();

                // Append data for each row
                formData.append(`items[${index}][id]`, id);
                formData.append(`items[${index}][name]`, name);
                formData.append(`items[${index}][description]`, description);
                formData.append(`items[${index}][price]`, price);
                formData.append(`items[${index}][category_id]`, category_id);
                formData.append(`items[${index}][existing_image]`, existingImage);

                if (imageInput.files.length > 0) {
                    formData.append(`items[${index}][image]`, imageInput.files[0]);
                    hasChanges = true;
                }

                // Check if any changes were made
                if (name !== $row.find('input[type="text"]').eq(0).attr('value') ||
                    description !== $row.find('textarea').attr('value') ||
                    price !== $row.find('input[type="number"]').attr('value') ||
                    category_id !== $row.find('select').find(':selected').attr('value')) {
                    hasChanges = true;
                }
            });

            if (!hasChanges) {
                alert('No changes detected.');
                return;
            }

            formData.append('action', 'update_all'); // Add a new action for bulk update

            // Send AJAX request to save all changes
            $.ajax({
                url: '', // This is the same page, item_edit.php
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    alert('All changes saved successfully!');
                    location.reload(); // Refresh the page to see updated data
                },
                error: function(xhr, status, error) {
                    alert('Error saving changes: ' + error);
                }
            });
        });

        // Existing Save Button Logic (unchanged)
        $('.save-btn').on('click', function() {
            const row = $(this).closest('tr');
            const id = row.data('id');
            const name = row.find('input[type="text"]').eq(0).val();
            const description = row.find('textarea').val();
            const price = row.find('input[type="number"]').val();
            const category_id = row.find('select').val();
            const imageInput = row.find('input[type="file"]')[0];
            const existingImage = row.find('input[name="existing_image"]').val();

            const formData = new FormData();
            formData.append('id', id);
            formData.append('name', name);
            formData.append('description', description);
            formData.append('price', price);
            formData.append('category_id', category_id);
            formData.append('existing_image', existingImage);
            if (imageInput.files.length > 0) {
                formData.append('image', imageInput.files[0]);
            }

            formData.append('action', 'update');

            $.ajax({
                url: '', // This is the same page, item_edit.php
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    alert('Item updated successfully!');
                    location.reload(); // Refresh the page to see updated data
                },
                error: function(xhr, status, error) {
                    alert('Error updating item: ' + error);
                }
            });
        });
    </script>
</body>
</html>
