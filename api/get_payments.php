<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include 'koneksi.php';

$id_user = $koneksi->real_escape_string($_POST['user_id']);
$sql = "SELECT *
        FROM payments 
        
        WHERE id_user = '$id_user'";
$result = $koneksi->query($sql);

$response = array();

if ($result->num_rows > 0) {
    $response['isSuccess'] = true;
    $response['message'] = "Berhasil Menampilkan Data ";
    $response['data'] = array();
    while ($row = $result->fetch_assoc()) {
        $response['data'][] = $row;
    }
} else {
    $response['isSuccess'] = false;
    $response['message'] = "Gagal menampilkan Data ";
    $response['data'] = null;
}

echo json_encode($response);
