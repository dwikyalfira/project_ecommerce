<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include 'koneksi.php';

$id_user = $koneksi->real_escape_string($_POST['user_id']);
$sql = "SELECT c.cart_id, p.product_name ,p.product_image,p.product_description,p.product_price,p.qty, c.status
        FROM tb_cart c 
        JOIN tb_product p ON c.cart_id = p.product_id 
        WHERE c.user_id = '$id_user'";
$result = $koneksi->query($sql);

$response = array();

if ($result->num_rows > 0) {
    $response['isSuccess'] = true;
    $response['message'] = "Berhasil Menampilkan Data Cart";
    $response['data'] = array();
    while ($row = $result->fetch_assoc()) {
        $response['data'][] = $row;
    }
} else {
    $response['isSuccess'] = false;
    $response['message'] = "Gagal menampilkan Data Cart";
    $response['data'] = null;
}

echo json_encode($response);