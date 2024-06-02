<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == "GET") {
    $response = array();
    
    // Fetch all tracking records from the database
    $query = "SELECT * FROM tb_tracking";
    $result = mysqli_query($koneksi, $query);
    
    if (mysqli_num_rows($result) > 0) {
        $response['value'] = 1;
        $response['message'] = "Berhasil mendapatkan data tracking";
        $response['tracking'] = array();
        
        while ($row = mysqli_fetch_assoc($result)) {
            $tracking_item = array(
                'tracking_id' => $row['tracking_id'],
                'status' => $row['status'],
                'description' => $row['description'],
                'created_at' => $row['created_at'],
                'updated' => $row['updated']
            );
            array_push($response['tracking'], $tracking_item);
        }
        
        echo json_encode($response);
    } else {
        $response['value'] = 0;
        $response['message'] = "Tidak ada data tracking";
        echo json_encode($response);
    }
} else {
    // Invalid request method
    $response['value'] = 0;
    $response['message'] = "Metode permintaan tidak valid";
    echo json_encode($response);
}

?>
