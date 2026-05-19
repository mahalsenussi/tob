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
    <title>Arrange Items Order</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Style for the list */
        .item-list {
            list-style-type: none;
            padding: 0;
        }
        .item-list li {
            margin: 10px 0;
            padding: 10px;
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            display: flex;
            align-items: center;
        }
        .item-list li input {
            width: 60px;
            margin-right: 10px;
        }

        /* Hide the item list and save button initially */
        #itemList, #saveOrder {
            display: none;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Arrange Items Order</h2>

        <!-- Category Filter -->
        <div class="mb-3">
            <label for="categoryFilter" class="form-label">Filter by Category:</label>
            <select id="categoryFilter" class="form-select">
                <option value="0">Select a Category</option>
                <?php
                // Include database connection
                include 'db_connect.php';

                // Fetch categories
                $sql = "SELECT id, name FROM category";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='{$row['id']}'>{$row['name']}</option>";
                    }
                }

                $conn->close();
                ?>
            </select>
        </div>

        <!-- Item List -->
        <ul id="itemList" class="item-list"></ul>

        <!-- Save Button -->
        <button id="saveOrder" class="btn btn-primary mt-3">Save Order</button>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(function() {
            // Filter items by category
            $("#categoryFilter").change(function() {
                const categoryId = $(this).val();

                if (categoryId === "0") {
                    // Hide the item list and save button if "Select a Category" is chosen
                    $("#itemList, #saveOrder").hide();
                    return;
                }

                console.log("Fetching items for category ID:", categoryId); // Debugging

                // Fetch items for the selected category
                $.ajax({
                    url: 'fetch_items.php', // Separate PHP file to fetch items
                    type: 'GET',
                    data: { 
                        category_id: categoryId 
                    },
                    success: function(response) {
                        console.log("Server response:", response); // Debugging
                        const items = JSON.parse(response);
                        $("#itemList").empty();

                        if (items.length > 0) {
                            items.forEach(item => {
                                $("#itemList").append(
                                    `<li data-id="${item.id}">
                                        <input type="number" class="form-control display-order" value="${item.display_order}" />
                                        <span>${item.name}</span>
                                    </li>`
                                );
                            });

                            // Show the item list and save button
                            $("#itemList, #saveOrder").show();
                        } else {
                            // Show a message if no items are found
                            $("#itemList").append("<li>No items found for this category.</li>");
                            $("#itemList").show();
                            $("#saveOrder").hide();
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX error:", error); // Debugging
                        alert('Error fetching items. Check the console for details.');
                    }
                });
            });

            // Save the new order
            $("#saveOrder").click(function() {
    const order = [];
    $("#itemList li").each(function() {
        const itemId = $(this).data('id');
        const displayOrder = $(this).find('.display-order').val();
        order.push({ id: itemId, display_order: displayOrder });
    });

    console.log("Sending order data:", order); // Debugging

    $.ajax({
        url: 'update_order.php', // Separate PHP file to update order
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({ 
            order: order 
        }),
        success: function(response) {
            console.log("Server response:", response); // Debugging
            try {
                const result = JSON.parse(response);
                if (result.status === 'success') {
                    alert('Order saved successfully!');
                    location.reload(); // Refresh the page to reflect the new order
                } else {
                    alert('Error saving order: ' + (result.message || 'Unknown error'));
                }
            } catch (e) {
                console.error("Error parsing server response:", e); // Debugging
                alert('Error saving order: Invalid server response');
            }
        },
        error: function(xhr, status, error) {
            console.error("AJAX error:", xhr.responseText, status, error); // Debugging
            alert('Error saving order: ' + (xhr.responseText || error));
        }
    });
});
        });
    </script>
</body>
</html>