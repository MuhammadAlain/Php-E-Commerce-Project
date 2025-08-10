<?php
function remove_cart_item()
{
    global $con;
    if (isset($_POST['remove_cart'])) {
        if (isset($_POST['removeitem']) && is_array($_POST['removeitem'])) {
            foreach ($_POST['removeitem'] as $remove_id) {
                $delete_query = "DELETE FROM `card_details` WHERE product_id=$remove_id";
                $delete_run_result = mysqli_query($con, $delete_query);
                if ($delete_run_result) {
                    echo "<script>window.open('cart.php','_self');</script>";
                }
            }
        } else {
            echo "<script>alert('Please select at least one item to remove');</script>";
        }
    }
}
?>
