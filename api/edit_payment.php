<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $response = array();

    // Check if the required POST parameters are set
    if (isset($_POST['cart_id'])) {
        $cart_id = $_POST['cart_id'];

        // Sanitize inputs to prevent SQL injection
        $cart_id = mysqli_real_escape_string($koneksi, $cart_id);

        // Update the cart status to 'done'
        $update = "UPDATE payments SET status = 'success' WHERE cart_product_id = '$cart_id'";

        if (mysqli_query($koneksi, $update)) {
            $response['value'] = 1;
            $response['message'] = "Status berhasil diperbarui.";
        } else {
            $response['value'] = 0;
            $response['message'] = "Gagal memperbarui status.";
        }
    } else {
        // Missing required POST parameters
        $response['value'] = 0;
        $response['message'] = "Parameter 'cart_id' diperlukan.";
    }
    echo json_encode($response);
} else {
    // Invalid request method
    $response['value'] = 0;
    $response['message'] = "Metode permintaan tidak valid.";
    echo json_encode($response);
}