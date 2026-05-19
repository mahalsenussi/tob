<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit();
}

// Cache-busting headers
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Category</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css?v=<?= time() ?>" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js?v=<?= time() ?>"></script>
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
        <h2 class="text-center">Update Category</h2>

        <?php
        // Database connection
        require_once 'db_connect.php';

        // Handle individual update request
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
            if ($_POST['action'] == 'update') {
                $id = intval($_POST['id']);
                $name = trim($conn->real_escape_string($_POST['name']));
                $link = trim($conn->real_escape_string($_POST['link']));
                $display_order = intval($_POST['display_order']);
                
                // Validation: prevent empty values
                if (empty($name)) {
                    echo '<div class="alert alert-danger">Category name cannot be empty!</div>';
                } elseif (empty($link)) {
                    echo '<div class="alert alert-danger">Category link cannot be empty!</div>';
                } else {
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

                    $sql = "UPDATE category SET 
                            name = '$name', 
                            link = '$link', 
                            image = '$image', 
                            display_order = $display_order 
                            WHERE id = $id";

                    if ($conn->query($sql) === TRUE) {
                        echo '<div class="alert alert-success">Category updated successfully!</div>';
                    } else {
                        echo "<div class='alert alert-danger'>Error updating category: {$conn->error}</div>";
                    }
                }
            }

            // Handle bulk update request
            if ($_POST['action'] == 'update_all') {
                foreach ($_POST['items'] as $id => $item) {
                    $id = intval($id);
                    $name = trim($conn->real_escape_string($item['name']));
                    $link = trim($conn->real_escape_string($item['link']));
                    $display_order = intval($item['display_order']);
                    $image = $conn->real_escape_string($item['existing_image']);

                    // Validation: skip empty values
                    if (empty($name) || empty($link)) {
                        echo "<div class='alert alert-warning'>Skipping category ID {$id} - name or link is empty</div>";
                        continue;
                    }

                    // Handle image upload if a new image is provided
                    if (isset($_FILES['items']['tmp_name'][$id]['image']) && $_FILES['items']['tmp_name'][$id]['image'] != '') {
                        $targetDir = "img/";
                        $targetFile = $targetDir . basename($_FILES['items']['name'][$id]['image']);
                        move_uploaded_file($_FILES['items']['tmp_name'][$id]['image'], $targetFile);
                        $image = $conn->real_escape_string($targetFile);
                    }

                    $sql = "UPDATE category SET 
                            name = '$name', 
                            link = '$link', 
                            image = '$image', 
                            display_order = $display_order 
                            WHERE id = $id";

                    if (!$conn->query($sql)) {
                        echo "<div class='alert alert-danger'>Error updating category with ID {$id}: {$conn->error}</div>";
                    }
                }
                echo '<div class="alert alert-success">All categories updated successfully!</div>';
            }
        }

        // Fetch all categories
        $sql = "SELECT id, name, link, image, display_order FROM category ORDER BY display_order ASC";
        $result = $conn->query($sql);

        if ($result->num_rows > 0): ?>
            <form id="updateForm" method="post" enctype="multipart/form-data">
                <table class="table table-bordered mt-3">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Link</th>
                            <th>Image</th>
                            <th>Display Order</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['id']) ?></td>
                                <td><input type="text" class="form-control" name="items[<?= $row['id'] ?>][name]" value="<?= htmlspecialchars($row['name']) ?>" required></td>
                                <td><input type="text" class="form-control" name="items[<?= $row['id'] ?>][link]" value="<?= htmlspecialchars($row['link']) ?>" required></td>
                                <td>
                                    <input type="file" class="form-control" name="items[<?= $row['id'] ?>][image]" accept="image/*">
                                    <input type="hidden" name="items[<?= $row['id'] ?>][existing_image]" value="<?= htmlspecialchars($row['image']) ?>">
                                    <?php if ($row['image']): ?>
                                        <img src="<?= htmlspecialchars($row['image']) ?>" alt="Category Image" style="max-width: 100px; margin-top: 10px;">
                                    <?php endif; ?>
                                </td>
                                <td><input type="number" class="form-control" name="items[<?= $row['id'] ?>][display_order]" value="<?= htmlspecialchars($row['display_order']) ?>"></td>
                                <td><button type="button" class="btn btn-sm btn-primary update-btn" data-id="<?= $row['id'] ?>">Update</button></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <div class="text-center mt-3">
                    <input type="hidden" name="action" value="update_all">
                    <button type="submit" class="btn btn-success">Save All Changes</button>
                    <button type="button" class="btn btn-info" onclick="debugValidation()">Debug Validation</button>
                </div>
            </form>
        <?php else: ?>
            <div class="alert alert-warning">No categories found.</div>
        <?php endif;

        $conn->close();
        ?>
    </div>

    <script>
        function debugValidation() {
            console.log('=== DEBUG VALIDATION ===');
            let nameFields = $('input[name^="items"][name$="[name]"]');
            let linkFields = $('input[name^="items"][name$="[link]"]');
            
            console.log('Found ' + nameFields.length + ' name fields');
            nameFields.each(function() {
                console.log('Name field:', $(this).attr('name'), 'Value:', $(this).val(), 'Length:', $(this).val().length);
            });
            
            console.log('Found ' + linkFields.length + ' link fields');
            linkFields.each(function() {
                console.log('Link field:', $(this).attr('name'), 'Value:', $(this).val(), 'Length:', $(this).val().length);
            });
            
            console.log('=== END DEBUG ===');
        }
        
        function validateForm() {
            let isValid = true;
            
            $('input[name^="items"][name$="[name]"]').each(function() {
                const value = $(this).val();
                if (!value || value.trim() === '') {
                    alert('Category name cannot be empty!');
                    $(this).focus();
                    isValid = false;
                    return false;
                }
            });
            
            if (isValid) {
                $('input[name^="items"][name$="[link]"]').each(function() {
                    const value = $(this).val();
                    if (!value || value.trim() === '') {
                        alert('Category link cannot be empty!');
                        $(this).focus();
                        isValid = false;
                        return false;
                    }
                });
            }
            
            return isValid;
        }
        
        // Individual Update Button Logic
        $(document).on('click', '.update-btn', function () {
            const id = $(this).data('id');
            const row = $(this).closest('tr');
            const name = row.find('input[name$="[name]"]').val();
            const link = row.find('input[name$="[link]"]').val();
            const display_order = row.find('input[name$="[display_order]"]').val();
            const existing_image = row.find('input[name$="[existing_image]"]').val();
            
            // Validation: prevent empty submissions
            if (!name || name.trim() === '') {
                alert('Category name cannot be empty!');
                return;
            }
            if (!link || link.trim() === '') {
                alert('Category link cannot be empty!');
                return;
            }
            
            const formData = new FormData();
            formData.append('id', id);
            formData.append('name', name);
            formData.append('link', link);
            formData.append('display_order', display_order);
            formData.append('existing_image', existing_image);
            formData.append('action', 'update');

            // Append image file if a new one is selected
            const imageInput = row.find('input[type="file"]')[0];
            if (imageInput.files.length > 0) {
                formData.append('image', imageInput.files[0]);
            }

            $.ajax({
                url: '', // This is the same page
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: (response) => {
                    alert('Category updated successfully!');
                    location.reload(); // Refresh the page to see updated data
                },
                error: (xhr, status, error) => {
                    alert('Failed to update the category. Error: ' + error);
                }
            });
        });
    </script>
</body>
</html>
