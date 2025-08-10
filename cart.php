<?php
session_start();
include('./includes/connect.php');
include('./functions/common_functions.php');

// === Handle Remove Action at the top ===
function remove_cart_item()
{
    global $con;
    if (isset($_POST['remove_cart'])) {
        if (isset($_POST['removeitem']) && is_array($_POST['removeitem'])) {
            foreach ($_POST['removeitem'] as $remove_id) {
                $delete_query = "DELETE FROM `card_details` WHERE product_id=$remove_id";
                mysqli_query($con, $delete_query);
            }
            echo "<script>window.open('cart.php','_self');</script>";
        } else {
            echo "<script>alert('Please select at least one item to remove');</script>";
        }
    }
}
remove_cart_item();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ecommerce Cart Details Page</title>
    <link rel="stylesheet" href="./assets/css/bootstrap.css" />
    <link rel="stylesheet" href="./assets/css/main.css" />
</head>

<body>
    <!-- upper-nav -->
    <div class="upper-nav primary-bg p-2 px-3 text-center text-break">
        <span>Summer Sale For All Swim Suits And Free Express Delivery - OFF 50%! <a>Shop Now</a></span>
    </div>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">EzShop</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link active" href="./index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="./products.php">Products</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">About</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Contact</a></li>
                    <?php
                    echo isset($_SESSION['username']) ?
                        "<li class='nav-item'><a class='nav-link' href='./users_area/profile.php'>My Account</a></li>" :
                        "<li class='nav-item'><a class='nav-link' href='./users_area/user_registration.php'>Register</a></li>";
                    ?>
                </ul>
                <form class="d-flex">
                    <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                    <button class="btn btn-outline-primary" type="submit">Search</button>
                </form>
                <ul class="navbar-nav mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="./cart.php">
                            <!-- Cart SVG -->
                            <sup><?php cart_item(); ?></sup>
                            <span class="d-none">Total Price is: <?php total_cart_price(); ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <!-- User Icon -->
                            <?php
                            echo "<span>Welcome " . ($_SESSION['username'] ?? "guest") . "</span>";
                            ?>
                        </a>
                    </li>
                    <?php
                    echo !isset($_SESSION['username']) ?
                        "<li class='nav-item'><a class='nav-link' href='./users_area/user_login.php'>Login</a></li>" :
                        "<li class='nav-item'><a class='nav-link' href='./users_area/logout.php'>Logout</a></li>";
                    ?>
                </ul>
            </div>
        </div>
    </nav>
    <!-- End NavBar -->

    <!-- Cart Table -->
    <div class="landing">
        <div class="container">
            <div class="row py-5 m-0">
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                    <table class="table table-bordered table-hover table-striped table-group-divider text-center">

                        <?php
                        $getIpAddress = getIPAddress();
                        $total_price = 0;
                        $cart_query = "SELECT * FROM `card_details` WHERE ip_address='$getIpAddress'";
                        $cart_result = mysqli_query($con, $cart_query);
                        $result_count = mysqli_num_rows($cart_result);
                        if ($result_count > 0) {
                            echo "
                                <thead>
                                    <tr class='d-flex flex-column d-md-table-row'>
                                        <th>Product Title</th>
                                        <th>Product Image</th>
                                        <th>Quantity</th>
                                        <th>Total Price</th>
                                        <th>Remove</th>
                                        <th colspan='2'>Operations</th>
                                    </tr>
                                </thead>
                                <tbody>";

                            while ($row = mysqli_fetch_array($cart_result)) {
                                $product_id = $row['product_id'];
                                $product_quantity = $row['quantity'];

                                $select_product_query = "SELECT * FROM `products` WHERE product_id=$product_id";
                                $select_product_result = mysqli_query($con, $select_product_query);
                                while ($product_row = mysqli_fetch_array($select_product_result)) {
                                    $product_title = $product_row['product_title'];
                                    $product_price = $product_row['product_price'];
                                    $product_image = $product_row['product_image_one'];

                                    $subtotal = $product_price * $product_quantity;
                                    $total_price += $subtotal;

                                    // Handle quantity update
                                    if (isset($_POST['update_cart'])) {
                                        $field_name = 'qty_' . $product_id;
                                        if (!empty($_POST[$field_name])) {
                                            $new_qty = intval($_POST[$field_name]);
                                            mysqli_query($con, "UPDATE `card_details` SET quantity = $new_qty WHERE ip_address='$getIpAddress' AND product_id=$product_id");
                                            echo "<script>window.open('cart.php','_self');</script>";
                                        }
                                    }
                                    ?>

                                    <tr class="d-flex flex-column d-md-table-row">
                                        <td><?php echo $product_title; ?></td>
                                        <td><img src="./admin/product_images/<?php echo $product_image; ?>" class="img-thumbnail" alt="<?php echo $product_title; ?>"></td>
                                        <td>
                                            <input type="number" class="form-control w-50 mx-auto" min="1" name="qty_<?php echo $product_id; ?>" value="<?php echo $product_quantity; ?>">
                                        </td>
                                        <td><?php echo $subtotal; ?></td>
                                        <td><input type="checkbox" name="removeitem[]" value="<?php echo $product_id; ?>"></td>
                                        <td><input type="submit" value="Update" class="btn btn-dark" name="update_cart"></td>
                                        <td><input type="submit" value="Remove" class="btn btn-primary" name="remove_cart"></td>
                                    </tr>

                                <?php }
                            }
                            echo "</tbody>";
                        } else {
                            echo "<h2 class='text-center text-danger'>Cart is empty</h2>";
                        }
                        ?>
                    </table>

                    <!-- Subtotal and Buttons -->
                    <div class="d-flex align-items-center gap-4 flex-wrap">
                        <?php if ($result_count > 0): ?>
                            <h4>Sub-Total: <strong class="text-2">Rs. <?php echo $total_price; ?></strong></h4>
                            <a class="btn btn-dark" href="./index.php">Continue Shopping</a>
                            <a class="btn btn-dark" href="./users_area/checkout.php">Checkout</a>
                        <?php else: ?>
                            <a class="btn btn-dark" href="./index.php">Continue Shopping</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="./assets/js/bootstrap.bundle.js"></script>
</body>
</html>
