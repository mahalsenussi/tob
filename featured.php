<?php
// Database connection
require_once 'db_connect.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['save_changes'])) {
        // Loop through all submitted items and update their featured status
        foreach ($_POST['items'] as $item_id => $is_featured) {
            $is_featured = $is_featured ? 1 : 0;
            $sql = "UPDATE item SET is_featured = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $is_featured, $item_id);
            $stmt->execute();
            $stmt->close();
        }
        echo '<div class="alert alert-success text-center">Changes saved successfully!</div>';
    }
}

// Fetch all categories for the filter
$categories = [];
$categorySql = "SELECT * FROM category";
$categoryResult = $conn->query($categorySql);
if ($categoryResult->num_rows > 0) {
    while ($row = $categoryResult->fetch_assoc()) {
        $categories[] = $row;
    }
}

// Fetch items based on filter (if applied)
$filter_category = isset($_GET['category']) ? intval($_GET['category']) : null;
$sql = "SELECT * FROM item";
if ($filter_category) {
    $sql .= " WHERE category_id = $filter_category";
}
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Featured Items</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            margin-top: 50px;
        }
        table {
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        th, td {
            vertical-align: middle;
        }
        .filter-form {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="text-center mb-4">Manage Featured Items</h2>

        <!-- Filter Form -->
        <form method="GET" action="" class="filter-form">
            <div class="form-row align-items-center">
                <div class="col-auto">
                    <label for="category" class="mr-2">Filter by Category:</label>
                    <select name="category" id="category" class="form-control">
                        <option value="">All Categories</option>
                        <?php
                        foreach ($categories as $category) {
                            $selected = ($filter_category == $category['id']) ? 'selected' : '';
                            echo '<option value="' . $category['id'] . '" ' . $selected . '>' . htmlspecialchars($category['name']) . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary">Apply Filter</button>
                </div>
            </div>
        </form>

        <!-- Items Table -->
        <form method="POST" action="">
            <table class="table table-bordered">
                <thead class="thead-light">
                    <tr>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Featured</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo '<tr>';
                            echo '<td>' . htmlspecialchars($row['name']) . '</td>';
                            echo '<td>$' . number_format($row['price'], 2) . '</td>';
                            echo '<td>';
                            echo '<input type="hidden" name="items[' . $row['id'] . ']" value="0">'; // Default value if unchecked
                            echo '<input type="checkbox" name="items[' . $row['id'] . ']" value="1" ' . ($row['is_featured'] ? 'checked' : '') . '>';
                            echo '</td>';
                            echo '</tr>';
                        }
                    } else {
                        echo '<tr><td colspan="3" class="text-center">No items found.</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
            <div class="text-center">
                <button type="submit" name="save_changes" class="btn btn-success">Save Changes</button>
            </div>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>