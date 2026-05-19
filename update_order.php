<?php
include 'db_connect.php';

// Get the raw POST data
$input = json_decode(file_get_contents('php://input'), true);

header('Content-Type: application/json'); // Ensure the response is JSON

$success = true;
$message = '';

if (isset($input['order']) && is_array($input['order'])) {
    foreach ($input['order'] as $item) {
        $id = intval($item['id']);
        $display_order = intval($item['display_order']);
        $sql = "UPDATE item SET display_order = $display_order WHERE id = $id";
        if (!$conn->query($sql)) {
            $success = false;
            $message = $conn->error;
            break;
        }
    }
} else {
    $success = false;
    $message = 'Invalid input data';
}

if ($success) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => $message]);
}

$conn->close();
?>