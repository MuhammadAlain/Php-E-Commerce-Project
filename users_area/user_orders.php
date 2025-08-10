<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Orders Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php
    // Assume $con is already established (e.g., from an included 'connect.php' file)
    // For demonstration, simulating a connection if not already present
    if (!isset($con)) {
        $con = mysqli_connect("localhost", "root", "", "your_database_name"); // Replace with your actual database details
        if (!$con) {
            die("Connection failed: " . mysqli_connect_error());
        }
    }

    // Start a session if not already started, as you're using $_SESSION
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    // access user id
    $username = '';
    if (isset($_SESSION['username'])) {
        $username = $_SESSION['username'];
        $get_user_query = "SELECT * FROM `user_table` WHERE username='$username'";
        $get_user_result = mysqli_query($con, $get_user_query);

        if ($get_user_result && mysqli_num_rows($get_user_result) > 0) {
            $row_user_data = mysqli_fetch_array($get_user_result);
            $user_id = $row_user_data['user_id'];
        } else {
            // Handle case where username not found in user_table
            $user_id = 0; // Or handle as an error
            echo "<p class='text-danger text-center'>User details not found.</p>";
        }
    } else {
        // Handle case where username is not in session (user not logged in)
        $user_id = 0;
        echo "<p class='text-danger text-center'>Please log in to view your orders.</p>";
        // You might want to redirect to login page here
        // header("Location: login.php");
        // exit();
    }
    ?>
    <div class="container">
        <h3 class="text-center text-success mb-5 mt-4">
            All my orders
        </h3>
        <table class="table table-bordered table-hover table-striped table-group-divider text-center">
            <thead>
                <tr>
                    <th>Serial NO.</th>
                    <th>Order Number</th>
                    <th>Amount due</th>
                    <th>Total Products</th>
                    <th>Invoice Number</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Confirm</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($user_id > 0) { // Only attempt to fetch orders if a valid user_id is found
                    $get_order_details_query = "SELECT * FROM `user_orders` WHERE user_id='$user_id'";
                    $get_order_details_result = mysqli_query($con, $get_order_details_query);

                    if ($get_order_details_result && mysqli_num_rows($get_order_details_result) > 0) {
                        $serial_number = 1;
                        while ($row_fetch_order_details = mysqli_fetch_array($get_order_details_result)) {
                            $order_id = $row_fetch_order_details['order_id'];
                            $amount_due = $row_fetch_order_details['amount_due'];
                            $invoice_number = $row_fetch_order_details['invoice_number'];
                            $total_products = $row_fetch_order_details['total_products'];
                            $order_date = $row_fetch_order_details['order_date'];
                            $order_status = $row_fetch_order_details['order_status'];

                            if ($order_status == 'pending') {
                                echo "
                                <tr>
                                    <td>$serial_number</td>
                                    <td>$order_id</td>
                                    <td>$amount_due</td>
                                    <td>$total_products</td>
                                    <td>$invoice_number</td>
                                    <td>$order_date</td>
                                    <td>$order_status</td>
                                    <td>
                                        <a href='confirm_payment.php?order_id=$order_id' class='text-decoration-underline'>Confirm</a>
                                    </td>
                                </tr>
                                ";
                            } else {
                                echo "
                                <tr>
                                    <td>$serial_number</td>
                                    <td>$order_id</td>
                                    <td>$amount_due</td>
                                    <td>$total_products</td>
                                    <td>$invoice_number</td>
                                    <td>$order_date</td>
                                    <td>$order_status</td>
                                    <td>
                                        Confirmed
                                    </td>
                                </tr>
                                ";
                            }
                            $serial_number++;
                        }
                    } else {
                        echo "<tr><td colspan='8'><h4 class='text-center text-secondary mt-3'>No orders found for this user.</h4></td></tr>";
                    }
                } else {
                    echo "<tr><td colspan='8'><h4 class='text-center text-danger mt-3'>Unable to retrieve user orders. Please ensure you are logged in.</h4></td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>