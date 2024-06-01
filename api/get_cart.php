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
        $query = "SELECT * FROM tb_cart WHERE user_id = ?";
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
                        'updated' => $row['updated']
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
