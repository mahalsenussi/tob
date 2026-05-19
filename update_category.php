<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    unset($_POST['id']); // Remove the ID from the update fields

    // Validate and prepare the SQL query
    $set_clause = [];
    foreach ($_POST as $column => $value) {
        $allowed_columns = ['name', 'link', 'image', 'display_order'];
        if (in_array($column, $allowed_columns)) {
            $set_clause[] = "$column = '" . addslashes($value) . "'";
        }
    }

    if (empty($set_clause)) {
        echo 'No valid fields to update';
        exit;
    }

    $set_clause = implode(', ', $set_clause);

    // Include database connection
    include 'db_connect.php';

    // Update query
    $sql = "UPDATE category SET $set_clause WHERE id = $id";

    if ($conn->query($sql)) {
        echo 'Success';
    } else {
        echo "Error: {$conn->error}";
    }

    $conn->close();
}
?>
