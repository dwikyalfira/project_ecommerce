<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == "GET") {
    $response = array();
    
    // Fetch all notification records from the database
    $query = "SELECT * FROM tb_notification";
    $result = mysqli_query($koneksi, $query);
    
    if (mysqli_num_rows($result) > 0) {
        $response['value'] = 1;
        $response['message'] = "Berhasil mendapatkan data notifikasi";
        $response['notifications'] = array();
        
        while ($row = mysqli_fetch_assoc($result)) {
            $notification_item = array(
                'notification_id' => $row['notification_id'],
                'title' => $row['title'],
                'description' => $row['description'],
                'time' => $row['time'],
                'is_read' => $row['is_read'],
                'created_at' => $row['created_at'],
                'updated' => $row['updated']
            );
            array_push($response['notifications'], $notification_item);
        }
        
        echo json_encode($response);
    } else {
        $response['value'] = 0;
        $response['message'] = "Tidak ada data notifikasi";
        echo json_encode($response);
    }
} else {
    // Invalid request method
    $response['value'] = 0;
    $response['message'] = "Metode permintaan tidak valid";
    echo json_encode($response);
}

?>
