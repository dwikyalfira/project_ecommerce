<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $response = array();

    // Check if the required POST parameters are set
    if (isset($_POST['user_id']) && isset($_POST['product_id']) && isset($_POST['total'])) {
        $user_id = $_POST['user_id'];
        $product_id = $_POST['product_id'];
        $total = $_POST['total'];

        // Sanitize inputs to prevent SQL injection
        $user_id = mysqli_real_escape_string($koneksi, $user_id);
        $product_id = mysqli_real_escape_string($koneksi, $product_id);
        $total = mysqli_real_escape_string($koneksi, $total);

        // Insert the new transaction
        $insert = "INSERT INTO tb_transaction (user_id, product_id, total, created_at) VALUES ('$user_id', '$product_id', '$total', NOW())";
        if (mysqli_query($koneksi, $insert)) {
            $response['value'] = 1;
            $response['message'] = "Transaksi berhasil ditambahkan";
        } else {
            $response['value'] = 0;
            $response['message'] = "Gagal menambahkan transaksi";
        }
    } else {
        // Missing required POST parameters
        $response['value'] = 0;
        $response['message'] = "Parameter yang diperlukan tidak ada";
    }

    echo json_encode($response);

} else {
    // Invalid request method
    $response['value'] = 0;
    $response['message'] = "Metode permintaan tidak valid";
    echo json_encode($response);
}

?>
