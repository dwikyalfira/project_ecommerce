<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == "GET") {
    $response = array();
    
    $query = "SELECT * FROM tb_favorite";
    $result = mysqli_query($koneksi, $query);
    
    if (mysqli_num_rows($result) > 0) {
        $response['value'] = 1;
        $response['message'] = "Berhasil mendapatkan data favorit";
        $response['favorites'] = array();
        
        while ($row = mysqli_fetch_assoc($result)) {
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
} else {
    // Invalid request method
    $response['value'] = 0;
    $response['message'] = "Metode permintaan tidak valid";
    echo json_encode($response);
}

?>
