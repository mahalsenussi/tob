<?php
header("Content-Type: application/json; charset=UTF-8");
include 'db_connect.php'; // Include your database connection file

// Fetch featured items
$featuredSql = "SELECT i.*, c.name as category_name FROM item i LEFT JOIN category c ON i.category_id = c.id WHERE i.is_featured = 1 ORDER BY i.display_order ASC, i.id ASC";
$featuredResult = $conn->query($featuredSql);

$featuredItems = [];
if ($featuredResult->num_rows > 0) {
    while ($row = $featuredResult->fetch_assoc()) {
        $featuredItems[] = $row;
    }
}

// Fetch categories
$categorySql = "SELECT * FROM category";
$categoryResult = $conn->query($categorySql);

$categories = [];
if ($categoryResult->num_rows > 0) {
    while ($row = $categoryResult->fetch_assoc()) {
        $categories[] = $row;
    }
}

// Return JSON response
echo json_encode([
    "featured_items" => $featuredItems,
    "categories" => $categories
]);

$conn->close();
?>