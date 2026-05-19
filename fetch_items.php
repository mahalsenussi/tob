<?php
include 'db_connect.php';

$category_id = intval($_GET['category_id']);

$sql = "SELECT id, name, display_order FROM item " .
       ($category_id > 0 ? "WHERE category_id = $category_id " : "") .
       "ORDER BY display_order ASC, id ASC";
$result = $conn->query($sql);

$items = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
    }
}

echo json_encode($items);

$conn->close();
?>