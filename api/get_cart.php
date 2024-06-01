<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == "GET") {
    $response = array();

    // Check if user_id is provided in the URL
    if (isset($_GET['user_id']) && is_numeric($_GET['user_id'])) {
        $user_id = intval($_GET['user_id']);

        // Use prepared statements to prevent SQL injection
        $query = "SELECT tb_cart.cart_id, tb_cart.user_id, tb_cart.product_id, tb_cart.created_at, tb_cart.updated, 
                         tb_product.product_name, tb_product.product_category, tb_product.product_description, 
                         tb_product.product_image, tb_product.product_price, tb_product.product_store 
                  FROM tb_cart 
                  JOIN tb_product ON tb_cart.product_id = tb_product.product_id 
                  WHERE tb_cart.user_id = ?";
        if ($stmt = $koneksi->prepare($query)) {
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $response['value'] = 1;
                $response['message'] = "Berhasil mendapatkan data keranjang";
                $response['cart'] = array();
                
                while ($row = $result->fetch_assoc()) {
                    $cart_item = array(
                        'cart_id' => $row['cart_id'],
                        'user_id' => $row['user_id'],
                        'product_id' => $row['product_id'],
                        'created_at' => $row['created_at'],
                        'updated' => $row['updated'],
                        'product_name' => $row['product_name'],
                        'product_category' => $row['product_category'],
                        'product_description' => $row['product_description'],
                        'product_image' => $row['product_image'],
                        'product_price' => $row['product_price'],
                        'product_store' => $row['product_store']
                    );
                    array_push($response['cart'], $cart_item);
                }
                
                echo json_encode($response);
            } else {
                $response['value'] = 0;
                $response['message'] = "Tidak ada data keranjang untuk user ini";
                echo json_encode($response);
            }
            
            $stmt->close();
        } else {
            $response['value'] = 0;
            $response['message'] = "Query preparation failed";
            echo json_encode($response);
        }
    } else {
        $response['value'] = 0;
        $response['message'] = "Invalid or missing user ID";
        echo json_encode($response);
    }
} else {
    // Invalid request method
    $response['value'] = 0;
    $response['message'] = "Metode permintaan tidak valid";
    echo json_encode($response);
}

?>
