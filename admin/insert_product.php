<?php
include('../includes/connect.php');


if (isset($_POST['insert_product'])) {
    
    $product_title = mysqli_real_escape_string($con, $_POST['product_title']);
    $product_description = mysqli_real_escape_string($con, $_POST['product_description']);
    $product_keywords = mysqli_real_escape_string($con, $_POST['product_keywords']);
    $product_category = mysqli_real_escape_string($con, $_POST['product_category']);
    $product_brand = mysqli_real_escape_string($con, $_POST['product_brand']);
    $product_price = mysqli_real_escape_string($con, $_POST['product_price']); 
    $product_status = 'true';


    $product_image_one = $_FILES['product_image_one']['name'];
    $product_image_two = $_FILES['product_image_two']['name'];
    $product_image_three = $_FILES['product_image_three']['name'];

    $temp_image_one = $_FILES['product_image_one']['tmp_name'];
    $temp_image_two = $_FILES['product_image_two']['tmp_name'];
    $temp_image_three = $_FILES['product_image_three']['tmp_name'];

    $upload_directory = "./product_images/";


    if (empty($product_title) || empty($product_description) || empty($product_keywords) ||
        empty($product_category) || empty($product_brand) || !is_numeric($product_price) || $product_price < 0 ||
        empty($product_image_one) || empty($product_image_two) || empty($product_image_three))
    {
        echo "<script>alert('All fields are required and product price must be a valid number.'); window.location.href = 'insert_product.php';</script>";
        exit();
    }

    $allowed_image_types = ['image/jpeg', 'image/png', 'image/gif'];
    $image_one_type = mime_content_type($temp_image_one);
    $image_two_type = mime_content_type($temp_image_two);
    $image_three_type = mime_content_type($temp_image_three);

    if (!in_array($image_one_type, $allowed_image_types) ||
        !in_array($image_two_type, $allowed_image_types) ||
        !in_array($image_three_type, $allowed_image_types)) {
        echo "<script>alert('Only JPG, PNG, and GIF image formats are allowed.'); window.location.href = 'insert_product.php';</script>";
        exit();
    }
    if (!is_dir($upload_directory)) {
        mkdir($upload_directory, 0755, true); 
    }

    if (!move_uploaded_file($temp_image_one, $upload_directory . $product_image_one) ||
        !move_uploaded_file($temp_image_two, $upload_directory . $product_image_two) ||
        !move_uploaded_file($temp_image_three, $upload_directory . $product_image_three)) {
        echo "<script>alert('Error uploading one or more images. Please check directory permissions.'); window.location.href = 'insert_product.php';</script>";
        exit();
    }
    $insert_query = "INSERT INTO `products` (product_title, product_description, product_keywords, category_id, brand_id, product_image_one, product_image_two, product_image_three, product_price, date, status)
                     VALUES ('$product_title', '$product_description', '$product_keywords', '$product_category', '$product_brand', '$product_image_one', '$product_image_two', '$product_image_three', '$product_price', NOW(), '$product_status')";

    $insert_result = mysqli_query($con, $insert_query);

    if ($insert_result) {
        echo "<script>alert('Product Inserted Successfully!'); window.location.href = 'insert_product.php';</script>";
    } else {
        echo "<script>alert('Error: Product could not be inserted. " . mysqli_error($con) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insert Products - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/main.css" />
    <style>
        /* Add some basic styling here or in your main.css for clarity */
        body {
            background-color: #f8f9fa;
        }
        .container {
            max-width: 800px;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .categ-header {
            border-bottom: 2px solid #007bff;
            padding-bottom: 15px;
            margin-bottom: 30px;
        }
        .categ-header h2 {
            color: #343a40;
            font-weight: bold;
        }
        .form-label {
            font-weight: 500;
            color: #495057;
        }
    </style>
</head>

<body>
    <div class="container py-4 px-2 mt-5">
        <div class="categ-header text-center">
            <h2>Insert Products</h2>
        </div>
        <form action="" method="post" enctype="multipart/form-data">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label for="product_title" class="form-label">Product Title</label>
                        <input type="text" placeholder="Enter Product Title" name="product_title" id="product_title" class="form-control" autocomplete="off" required>
                    </div>
                    <div class="mb-3">
                        <label for="product_description" class="form-label">Product Description</label>
                        <textarea placeholder="Enter Product Description" name="product_description" id="product_description" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="product_keywords" class="form-label">Product Keywords</label>
                        <input type="text" placeholder="Enter Product Keywords (comma-separated)" name="product_keywords" id="product_keywords" class="form-control" autocomplete="off" required>
                    </div>
                    <div class="mb-3">
                        <label for="product_category" class="form-label">Product Category</label>
                        <select class="form-select" name="product_category" id="product_category" required>
                            <option value="">Select a Category</option>
                            <?php
                            if (isset($con)) {
                                $select_category_query = 'SELECT * FROM `categories`';
                                $select_category_result = mysqli_query($con, $select_category_query);
                                if ($select_category_result) {
                                    while ($row = mysqli_fetch_assoc($select_category_result)) {
                                        $category_title = htmlspecialchars($row['category_title']);
                                        $category_id = htmlspecialchars($row['category_id']);
                                        echo "<option value='{$category_id}'>{$category_title}</option>";
                                    }
                                } else {
                                    echo "<option value='' disabled>Error loading categories</option>";
                                }
                            } else {
                                echo "<option value='' disabled>Database connection error</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="product_brand" class="form-label">Product Brand</label>
                        <select class="form-select" name="product_brand" id="product_brand" required>
                            <option value="">Select a Brand</option>
                            <?php
                            if (isset($con)) {
                                $select_brand_query = 'SELECT * FROM `brands`';
                                $select_brand_result = mysqli_query($con, $select_brand_query);
                                if ($select_brand_result) {
                                    while ($row = mysqli_fetch_assoc($select_brand_result)) {
                                        $brand_title = htmlspecialchars($row['brand_title']);
                                        $brand_id = htmlspecialchars($row['brand_id']);
                                        echo "<option value='{$brand_id}'>{$brand_title}</option>";
                                    }
                                } else {
                                    echo "<option value='' disabled>Error loading brands</option>";
                                }
                            } else {
                                echo "<option value='' disabled>Database connection error</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="product_image_one" class="form-label">Product Image One</label>
                        <input type="file" name="product_image_one" id="product_image_one" class="form-control" accept="image/jpeg, image/png, image/gif" required>
                    </div>
                    <div class="mb-3">
                        <label for="product_image_two" class="form-label">Product Image Two</label>
                        <input type="file" name="product_image_two" id="product_image_two" class="form-control" accept="image/jpeg, image/png, image/gif" required>
                    </div>
                    <div class="mb-3">
                        <label for="product_image_three" class="form-label">Product Image Three</label>
                        <input type="file" name="product_image_three" id="product_image_three" class="form-control" accept="image/jpeg, image/png, image/gif" required>
                    </div>
                    <div class="mb-3">
                        <label for="product_price" class="form-label">Product Price (PKR)</label>
                        <input type="number" placeholder="Enter Product Price in PKR" name="product_price" id="product_price" class="form-control" step="0.01" min="0" required>
                    </div>
                    <div class="mb-3">
                        <input type="submit" value="Insert Product" name="insert_product" class="btn btn-primary w-100">
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>