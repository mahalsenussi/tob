<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>توباكتس</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* Ensure the menu covers the entire screen */
        #menu {
            z-index: 1050;
            transition: opacity 0.3s ease, visibility 0.3s ease;
        }

        /* Hidden state for the menu */
        #menu.d-none {
            visibility: hidden;
            opacity: 0;
        }

        /* Visible state for the menu */
        #menu:not(.d-none) {
            visibility: visible;
            opacity: 1;
        }

        /* Close button styling */
        .close-menu-btn {
            position: absolute;
            top: 10px;
            right: 20px;
            font-size: 1.5rem;
            cursor: pointer;
        }
    </style>
</head>
<body class="bg-light">
    <!-- Header Tab -->
    <div class="w-100" style="background: url('img/web3.jpg') no-repeat center/cover; height: 200px;">
        <div class="container d-flex align-items-center h-200">
            <h1 class="text-white"> </h1>
        </div>
    </div>

    <button class="btn btn-primary position-fixed top-0 start-0 m-3" onclick="toggleMenu()">
        <i class="fa fa-bars"></i>
    </button>

    <!-- Full-Screen Menu -->
    <div id="menu" class="d-none position-fixed top-0 start-0 w-100 h-100 bg-dark text-white">
        <span class="close-menu-btn text-white" onclick="toggleMenu()">&times;</span>
        <div class="d-flex flex-column justify-content-center align-items-center h-100">
            <a href="https://www.facebook.com/profile.php?id=61553829497950" class="mb-3 text-white h5" target="_blank">Facebook</a>
            <a href="https://www.instagram.com/tobactos__restaurant" class="mb-3 text-white h5" target="_blank">Instagram</a>
            <a href="about.php" class="text-white h5">About</a>
        </div>
    </div>

    <!-- Pizza Menu -->
    <div class="container mt-5 pt-5">
        <div class="mb-5 mt-5">
            <h3 class="text-center">Featured Items 
                <br>
    أصناف مميزة</h3>
            <div id="featuredCarousel" class="carousel slide mt-3" data-ride="carousel">
            <div class="carousel-inner">
                    <?php
                    include 'db_connect.php';

                    $randomSql = "SELECT i.*, c.name as category_name FROM item i LEFT JOIN category c ON i.category_id = c.id WHERE i.is_featured = 1 ORDER BY i.display_order ASC, i.id ASC";
                    $randomResult = $conn->query($randomSql);

                    $active = true;
        if ($randomResult->num_rows > 0) {
            while ($randomRow = $randomResult->fetch_assoc()) {
                $item_id = $randomRow['id']; // Get item ID for modal triggering
                echo '<div class="carousel-item ' . ($active ? 'active' : '') . '">';
                echo '<div class="card mx-auto" style="max-width: 400px; padding: 15px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);" data-toggle="modal" data-target="#itemModal' . $item_id . '">';
                echo '<div class="d-flex align-items-center">';
                echo '<img src="' . $randomRow['image'] . '" class="rounded mr-3" alt="' . $randomRow['name'] . '" style="height: 100px; width: 100px; object-fit: cover;">';
                echo '<div>';
                echo '<h5>' . $randomRow['name'] . '</h5>';
                echo '<p class="mb-0">LYD ' . number_format($randomRow['price'], 2) . '</p>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
                
                // Modal for displaying detailed information
                echo '<div class="modal fade" id="itemModal' . $item_id . '" tabindex="-1" role="dialog" aria-labelledby="itemModalLabel' . $item_id . '" aria-hidden="true">';
                echo '<div class="modal-dialog modal-lg" role="document">';
                echo '<div class="modal-content">';
                echo '<div class="modal-header">';
                echo '<h5 class="modal-title" id="itemModalLabel' . $item_id . '">' . $randomRow['name'] . '</h5>';
                echo '<button type="button" class="close" data-dismiss="modal" aria-label="Close">';
                echo '<span aria-hidden="true">&times;</span>';
                echo '</button>';
                echo '</div>';
                echo '<div class="modal-body">';
                echo '<img src="' . $randomRow['image'] . '" class="img-fluid mb-3" alt="' . $randomRow['name'] . '">';
                echo '<p><strong>Description:</strong> ' . $randomRow['description'] . '</p>';
                echo '<p><strong>Price:</strong> LYD ' . number_format($randomRow['price'], 2) . '</p>';
                echo '<p><strong>Category:</strong> ' . ($randomRow['category_name'] ?? 'Unknown') . '</p>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
                
                $active = false;
            }
        } else {
            echo '<p>No items found.</p>';
        }
                    ?>
                </div>
                <!-- Carousel Controls -->
                <a class="carousel-control-prev" href="#featuredCarousel" role="button" data-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="sr-only">Previous</span>
                </a>
                <a class="carousel-control-next" href="#featuredCarousel" role="button" data-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="sr-only">Next</span>
                </a>
            </div>
        </div>

        <!-- Categories Section -->
        <div class="text-center">
            <h3>Categories</h3>
            <div class="d-flex flex-wrap justify-content-center">
                <?php
                $sql = "SELECT * FROM category ORDER BY display_order ASC, id ASC";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo '<div class="card m-3 text-center" style="width: 300px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">';
                        echo '<img src="' . $row['image'] . '" alt="' . $row['name'] . '" class="card-img-top" style="height: 200px; object-fit: cover;">';
                        echo '<div class="card-body">';
                        echo '<h5 class="card-title">' . $row['name'] . '</h5>';
                        echo '<button class="btn btn-primary" onclick="window.location.href=\'items.php?category_id=' . $row['id'] . '\'">View Items <br> 
                        عرض القائمة
                        </button>';
                        echo '</div>';
                        echo '</div>';
                    }
                } else {
                    echo '<p>No categories found.</p>';
                }
                ?>
            </div>
        </div>

        <!-- Map Section -->
        <h3 class="mt-5 text-center">Map</h3>
        <div class="text-center">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d251.13842592561934!2d20.08955021554052!3d32.076171270314596!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x13831d0032dcfb5d%3A0xd848c61138838f35!2z2YXYt9i52YUg2KrZiNio2KfZg9iq2LM!5e0!3m2!1sen!2sly!4v1736155527384!5m2!1sen!2sly" style="border:0;" allowfullscreen="" loading="lazy" class="w-100" height="300"></iframe>
        
            </div>
    </div>

    <script>
        function toggleMenu() {
            const menu = document.getElementById('menu');
            menu.classList.toggle('d-none');
        }
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
