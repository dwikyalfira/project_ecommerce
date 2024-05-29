<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $response = array();

    // Sanitize inputs to prevent SQL injection
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $password = mysqli_real_escape_string($koneksi, md5($_POST['password']));

    $cek = "SELECT * FROM tb_user WHERE email = '$email' AND password = '$password'";
    $result = mysqli_query($koneksi, $cek);

    if ($result) {
        $row = mysqli_fetch_array($result);

        if (isset($row)) {
            $response['value'] = 1;
            $response['message'] = "Berhasil login";
            $response['email'] = $row['email'];
            $response['id'] = $row['id'];
        } else {
            $response['value'] = 0;
            $response['message'] = "Gagal login";
        }
    } else {
        $response['value'] = 0;
        $response['message'] = "Kesalahan dalam eksekusi kueri";
    }

    echo json_encode($response);
}

?>
