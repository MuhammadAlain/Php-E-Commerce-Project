<?php

include('../includes/connect.php');

if (!isset($con)) {
    $con = mysqli_connect("localhost", "root", "", "ecommerce_1"); 
    if (!$con) {
        die("Connection failed: " . mysqli_connect_error());
    }
}

if(isset($_GET['delete_product'])){
    $delete_id = (int)$_GET['delete_product']; 

    
    $get_image_query = "SELECT product_image_one FROM `products` WHERE product_id = $delete_id";
    $image_result = mysqli_query($con, $get_image_query);
    $image_row = mysqli_fetch_assoc($image_result);
    $image_to_delete = $image_row['product_image_one'] ?? null;

    $delete_query = "DELETE FROM `products` WHERE product_id = $delete_id";
    $delete_result = mysqli_query($con, $delete_query);

    if($delete_result){
        $image_upload_dir = './product_images/';
        if (!empty($image_to_delete) && file_exists($image_upload_dir . $image_to_delete)) {
            unlink($image_upload_dir . $image_to_delete); 
        }

        echo "<script>alert('Product deleted successfully!');</script>";
        echo "<script>window.open('index.php?view_products','_self');</script>";
    } else {
        echo "<script>alert('Error deleting product: " . mysqli_error($con) . "');</script>";
        echo "<script>window.open('index.php?view_products','_self');</script>";
    }
}
?>