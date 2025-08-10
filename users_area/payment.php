<?php
include('../includes/connect.php');
include('../functions/common_functions.php');
// session_start(); // Uncomment this if your session is not started elsewhere and you need $_SESSION['username']
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ecommerce Payment Page</title>
    <!-- Assuming these CSS files exist relative to this payment.php -->
    <link rel="stylesheet" href="../assets/css/bootstrap.css" />
    <link rel="stylesheet" href="../assets/css/main.css" />
    <style>
        /* Optional: Add some basic styling for the 'fake payment' button */
        .fake-payment-option {
            background-color: #4CAF50; /* Green */
            color: white;
            padding: 15px 30px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 24px;
            margin: 20px 0;
            cursor: pointer;
            border-radius: 8px;
            transition: background-color 0.3s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .fake-payment-option:hover {
            background-color: #45a049;
            color: white; /* Keep text color white on hover */
        }
        .fake-payment-option:active {
            background-color: #3e8e41;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transform: translateY(1px);
        }
        /* Center content vertically if landing is full height */
        html, body {
            height: 100%;
        }
        .landing {
            min-height: calc(100vh - 60px); /* Adjust based on upper-nav/footer height */
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            padding: 20px;
        }
    </style>
</head>

<body>
    <!-- upper-nav -->
    <div class="upper-nav primary-bg p-2 px-3 text-center text-break">
        <span>Summer Sale For All Swim Suits And Free Express Delivery - OFF 50%! <a href="#">Shop Now</a></span>
    </div>
    <!-- upper-nav -->

    <!-- php code to access user id -->
    <?php
        // Start session if not already started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $user_id = 0; // Default to 0
        if (isset($_SESSION['username'])) {
            $username = $_SESSION['username'];
            $get_user_query = "SELECT user_id FROM `user_table` WHERE username='$username'";
            $get_user_result = mysqli_query($con, $get_user_query);

            if ($get_user_result && mysqli_num_rows($get_user_result) > 0) {
                $fetch_user = mysqli_fetch_array($get_user_result);
                $user_id = $fetch_user['user_id'];
            } else {
                echo "<p class='text-danger text-center'>Error: User ID not found for the logged-in user.</p>";
            }
        } else {
            // Fallback for IP address if username session isn't available (less reliable for user-specific data)
            // You should ideally ensure the user is logged in before they reach the payment page.
            $user_ip = getIPAddress();
            $get_user_query_ip = "SELECT user_id FROM `user_table` WHERE user_ip='$user_ip'";
            $get_user_result_ip = mysqli_query($con, $get_user_query_ip);
            if ($get_user_result_ip && mysqli_num_rows($get_user_result_ip) > 0) {
                 $fetch_user_ip = mysqli_fetch_array($get_user_result_ip);
                 $user_id = $fetch_user_ip['user_id'];
            } else {
                 echo "<p class='text-danger text-center'>Error: User not identified. Please login or register.</p>";
            }
        }
    ?>
    <!-- php code to access user id -->

    <!-- Start Landing Section -->
    <div class="landing">
        <div class="container">
            <h1 class="text-center mt-2 mb-5">Select Payment Method</h1>
            <div class="row m-0 align-items-center justify-content-center">
                <div class="col-md-12 d-flex flex-column justify-content-center align-items-center">
                    <p class="lead mb-4">Choose your preferred payment method:</p>
                    
                    <!-- Fake Payment / Process Order Option -->
                    <?php if ($user_id > 0): // Only show if user_id is valid ?>
                        <a href="order.php?user_id=<?php echo $user_id;?>" class="fake-payment-option">
                            Process Order 
                        </a>
                        <p class="text-muted mt-3">Authentic Transactions</p>
                    <?php else: ?>
                        <p class="text-danger">Cannot process order without a valid user. Please login or register.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <!-- End Landing Section -->

    <!-- <div class="upper-nav primary-bg p-2 px-3 text-center text-break">
        <span>All CopyRight &copy;2023</span>
    </div> -->

    <script src="../assets/js/bootstrap.bundle.js"></script>
</body>

</html>
