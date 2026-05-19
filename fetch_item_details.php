<?php
include 'db_connect.php';

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4"); 

if (isset($_GET['id'])) {
    $item_id = intval($_GET['id']);

    // Fetch item details from the database
    $sql = "SELECT * FROM item WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $item_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Return the item data as JSON
        echo json_encode($result->fetch_assoc());
    } else {
        echo json_encode(['error' => 'Item not found']);
    }

    $stmt->close();
}

$conn->close();
?>
