<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
include 'koneksi.php';

$response = array();

try {
    if ($_SERVER['REQUEST_METHOD'] == "POST") {

        if (isset($_POST['user_id']) && isset($_POST['product_id'])) {
            $user_id = $_POST['user_id'];
            $product_id = $_POST['product_id'];

            $user_id = mysqli_real_escape_string($koneksi, $user_id);
            $product_id = mysqli_real_escape_string($koneksi, $product_id);

            $insert = "INSERT INTO tb_cart (user_id, product_id, created_at, updated) VALUES ('$user_id', '$product_id', NOW(), NOW())";
            if (mysqli_query($koneksi, $insert)) {
                $response['value'] = 1;
                $response['message'] = "Item berhasil ditambahkan ke keranjang";
            } else {
                throw new Exception("Gagal menambahkan item ke keranjang");
            }
        } else {
            $response['value'] = 0;
            $response['message'] = "Parameter yang diperlukan tidak ada";
        }
    } else {

        $response['value'] = 0;
        $response['message'] = "Metode permintaan tidak valid";
    }
} catch (mysqli_sql_exception $e) {
    $response['value'] = 0;
    $response['message'] = "Error: " . $e->getMessage();
} catch (Exception $e) {
    $response['value'] = 0;
    $response['message'] = "Error: " . $e->getMessage();
}

echo json_encode($response);

mysqli_close($koneksi);

?>
