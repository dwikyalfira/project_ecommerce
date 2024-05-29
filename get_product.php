<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == "GET") {
    $response = array();
    
    // Fetch all products from the database
    $query = "SELECT * FROM tb_product";
    $result = mysqli_query($koneksi, $query);
    
    if (mysqli_num_rows($result) > 0) {
        $response['value'] = 1;
        $response['message'] = "Products fetched successfully";
        $response['products'] = array();
        
        while ($row = mysqli_fetch_assoc($result)) {
            $product = array(
                'product_id' => $row['product_id'],
                'product_name' => $row['product_name'],
                'product_category' => $row['product_category'],
                'product_description' => $row['product_description'],
                'product_image' => $row['product_image'],
                'product_price' => $row['product_price'],
                'product_store' => $row['product_store'],
                'created' => $row['created_at'],
                'updated' => $row['updated']
            );
            array_push($response['products'], $product);
        }
        
        echo json_encode($response);
    } else {
        $response['value'] = 0;
        $response['message'] = "No products found";
        echo json_encode($response);
    }
} else {
    // Invalid request method
    $response['value'] = 0;
    $response['message'] = "Invalid request method";
    echo json_encode($response);
}

?>
