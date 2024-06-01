<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == "GET") {
    $response = array();
    
    // memastikan user_id tersedia 
    if (isset($_GET['user_id']) && is_numeric($_GET['user_id'])) {
        $user_id = intval($_GET['user_id']);
    } else {
        $response['value'] = 0;
        $response['message'] = "Invalid user ID";
        echo json_encode($response);
        exit();
    }
    
    $query = "SELECT * FROM tb_favorite WHERE user_id = ?";
    if ($stmt = $koneksi->prepare($query)) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $response['value'] = 1;
            $response['message'] = "Berhasil mendapatkan data favorit";
            $response['favorites'] = array();
            
            while ($row = $result->fetch_assoc()) {
                $favorite = array(
                    'favorite_id' => $row['favorite_id'],
                    'user_id' => $row['user_id'],
                    'product_id' => $row['product_id'],
                    'created_at' => $row['created_at'],
                    'updated' => $row['updated']
                );
                array_push($response['favorites'], $favorite);
            }
            
            echo json_encode($response);
        } else {
            $response['value'] = 0;
            $response['message'] = "Tidak ada data favorit";
            echo json_encode($response);
        }
        
        $stmt->close();
    } else {
        $response['value'] = 0;
        $response['message'] = "Query preparation failed";
        echo json_encode($response);
    }
} else {
    // Invalid request method
    $response['value'] = 0;
    $response['message'] = "Metode permintaan tidak valid";
    echo json_encode($response);
}

?>
