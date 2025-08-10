<?php
include('../includes/connect.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Payments</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Basic styling for the header section. Consider moving this to an external CSS file. */
        .categ-header .sub-title {
            display: flex;
            align-items: center;
            gap: 10px; /* Space between the shape and the heading */
        }

        .categ-header .sub-title .shape {
            width: 20px;
            height: 20px;
            background-color: #007bff; /* Example color for the shape */
            /* Add more styling for your shape if needed */
        }
    </style>
</head>

<body>
    <div class="container mt-4">
        <div class="categ-header mb-4">
            <div class="sub-title">
                <span class="shape"></span>
                <h2>All Payments</h2>
            </div>
        </div>

        <div class="table-data">
            <table class="table table-bordered table-hover table-striped text-center">
                <?php
                // Check if the database connection ($con) is established
                if (!isset($con)) {
                    echo "<thead><tr><td colspan='7' class='text-danger'>Database connection not established. Please check your `connect.php` file.</td></tr></thead><tbody></tbody>";
                } else {
                    // Query to fetch all payments from the database
                    $get_payment_query = "SELECT * FROM `user_payments`";
                    $get_payment_result = mysqli_query($con, $get_payment_query);

                    // Check if the query was successful
                    if (!$get_payment_result) {
                        echo "<thead><tr><td colspan='7' class='text-danger'>Error fetching payments: " . mysqli_error($con) . "</td></tr></thead><tbody></tbody>";
                    } else {
                        $row_count = mysqli_num_rows($get_payment_result);

                        // Only display table headers if there are payments
                        if ($row_count > 0) {
                            echo "
                            <thead class='table-dark'>
                                <tr>
                                    <th>Payment No.</th>
                                    <th>Order ID</th>
                                    <th>Invoice Number</th>
                                    <th>Due Amount</th>
                                    <th>Payment Method</th>
                                    <th>Payment Date</th>
                                    <th>Delete</th>
                                </tr>
                            </thead>";
                        }
                        // Start <tbody> for the results
                        echo "<tbody>";

                        if ($row_count == 0) {
                            echo "<tr><td colspan='7'><h2 class='text-center p-3'>No payments found yet.</h2></td></tr>";
                        } else {
                            $id_number = 1; // Initialize a counter for display purposes
                            while ($row_fetch_payments = mysqli_fetch_array($get_payment_result)) {
                                $payment_id = $row_fetch_payments['payment_id'];
                                // Sanitize all data for safe display in HTML to prevent XSS attacks
                                $order_id = htmlspecialchars($row_fetch_payments['order_id']);
                                $invoice_number = htmlspecialchars($row_fetch_payments['invoice_number']);
                                $amount_due = htmlspecialchars($row_fetch_payments['amount']);
                                $payment_method = htmlspecialchars($row_fetch_payments['payment_method']);
                                $payment_date = htmlspecialchars($row_fetch_payments['payment_date']);

                                // Output a table row for each payment
                                echo "
                                <tr>
                                    <td>{$id_number}</td>
                                    <td>{$order_id}</td>
                                    <td>{$invoice_number}</td>
                                    <td>{$amount_due}</td>
                                    <td>{$payment_method}</td>
                                    <td>{$payment_date}</td>
                                    <td>
                                        <a href='#' data-bs-toggle='modal' data-bs-target='#deleteModal_{$payment_id}' class='text-decoration-none text-danger'>
                                            <svg xmlns='http://www.w3.org/2000/svg' height='1em' viewBox='0 0 448 512'><path d='M135.2 17.7L128 32H32C14.3 32 0 46.3 0 64S14.3 96 32 96H416c17.7 0 32-14.3 32-32s-14.3-32-32-32H320l-7.2-14.3C307.4 6.8 296.3 0 284.2 0H163.8c-12.1 0-23.2 6.8-28.6 17.7zM416 128H32L53.2 467c1.6 25.3 22.6 45 47.9 45H346.9c25.3 0 46.3-19.7 47.9-45L416 128z'/></svg>
                                        </a>

                                        <div class='modal fade' id='deleteModal_{$payment_id}' tabindex='-1' aria-labelledby='deleteModal_{$payment_id}Label' aria-hidden='true'>
                                            <div class='modal-dialog modal-dialog-centered'>
                                                <div class='modal-content'>
                                                    <div class='modal-body text-center'>
                                                        <div class='d-flex flex-column gap-3 align-items-center'>
                                                            <span>
                                                                <svg width='50' height='50' viewBox='0 0 60 60' fill='none' xmlns='http://www.w3.org/2000/svg'>
                                                                    <circle cx='29.5' cy='30.5' r='26' stroke='#EA4335' stroke-width='3' />
                                                                    <path d='M41.2715 22.2715C42.248 21.2949 42.248 19.709 41.2715 18.7324C40.2949 17.7559 38.709 17.7559 37.7324 18.7324L29.5059 26.9668L21.2715 18.7402C20.2949 17.7637 18.709 17.7637 17.7324 18.7402C16.7559 19.7168 16.7559 21.3027 17.7324 22.2793L25.9668 30.5059L17.7402 38.7402C16.7637 39.7168 16.7637 41.3027 17.7402 42.2793C18.7168 43.2559 20.3027 43.2559 21.2793 42.2793L29.5059 34.0449L37.7402 42.2715C38.7168 43.248 40.3027 43.248 41.2793 42.2715C42.2559 41.2949 42.2559 39.709 41.2793 38.7324L33.0449 30.5059L41.2715 22.2715Z' fill='#EA4335' />
                                                                </svg>
                                                            </span>
                                                            <h2>Are you sure?</h2>
                                                            <p>
                                                                Do you really want to delete payment for Invoice #<strong>{$invoice_number}</strong> (Order ID: <strong>{$order_id}</strong>)? This process cannot be undone.
                                                            </p>
                                                            <div class='btns d-flex gap-3'>
                                                                <button type='button' class='btn px-5 btn-secondary' data-bs-dismiss='modal'>Cancel</button>
                                                                <a class='btn px-5 btn-danger' href='index.php?delete_payment={$payment_id}'>Delete</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>";
                                $id_number++; // Increment the display counter
                            }
                        }
                        echo "</tbody>"; // Close tbody
                    }
                }
                ?>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>