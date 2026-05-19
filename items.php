<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Items</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* CSS styles for the background image and menu */
        .background-animation {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 30vh;
            z-index: -1;
            background-image: url('img/web.jpg'); /* Change to your desired background image */
            background-repeat: no-repeat;
            background-size: cover;
            background-position: center;
        }

        .pizza-menu {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 70vh;
            overflow-y: scroll;
            background-color: #fff;
            padding: 20px;
        }

        .menu-item {
            cursor: pointer;
            margin-bottom: 20px;
        }

        .menu-item img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 4px;
        }

        .menu-item .menu-item-text {
            text-align: center;
        }

        /* Modal styles */
        .modal-content {
            padding: 20px;
        }

        /* Fixed button styles */
        .fixed-button {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
        }
    </style>
</head>
<body>
    <div class="background-animation"></div>
    <div class="container pizza-menu">
        <h2 class="text-center" id="cake"> Tobactos  </h2>

        <?php
        // Include database connection
        include 'db_connect.php';

        // Get the category ID from the URL
        if (isset($_GET['category_id'])) {
            $category_id = intval($_GET['category_id']);

            // Fetch items from the database for the selected category, ordered by display_order and then by id
            $sql = "SELECT * FROM item WHERE category_id = ? ORDER BY display_order ASC, id ASC";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $category_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="col-md-4 menu-item" onclick="showItemDetails(' . $row['id'] . ')">';
                    echo '<div class="card">';
                    echo '<img src="' . htmlspecialchars($row['image']) . '" class="card-img-top" alt="' . htmlspecialchars($row['name']) . '">';
                    echo '<div class="card-body">';
                    echo '<h5 class="card-title">' . htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8') . '</h5>';
                    echo '<p class="card-text">' . number_format($row['price'], 2) . ' LYD</p>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo '<p>لا توجد أطباق في هذه الفئة.</p>';
            }
        }

        $stmt->close();
        $conn->close();
        ?>

        <button class="btn btn-primary mt-3 fixed-button" onclick="window.location.href = 'index.php'">
            عودة</button>
    </div>

    <!-- Modal for item details -->
    <div id="item-modal" class="modal fade" tabindex="-1" aria-labelledby="itemModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="itemModalLabel">تفاصيل الطبق</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="modal-item-details">
                        <!-- Item details will be dynamically loaded here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function showItemDetails(itemId) {
            // Fetch item details by AJAX request
            $.ajax({
                url: 'fetch_item_details.php',
                type: 'GET',
                data: { id: itemId },
                success: function(data) {
                    const item = JSON.parse(data);

                    // Set the item details in the modal
                    let modalContent = `
                        <div class="text-center">
                            <img src="${item.image}" class="img-fluid" alt="${item.name}">
                            <h3 class="mt-3">${item.name}</h3>
                            <p><strong>${item.price} LYD</strong></p>
                            <p>${item.description}</p>
                        </div>
                    `;
                    $('#modal-item-details').html(modalContent);

                    // Show the modal
                    var myModal = new bootstrap.Modal(document.getElementById('item-modal'));
                    myModal.show();
                }
            });
        }
    </script>
</body>
</html>