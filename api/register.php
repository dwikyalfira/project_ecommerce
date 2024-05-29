<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $response = array();
    
    if (isset($_POST['username']) && isset($_POST['email']) && isset($_POST['password'])) {
        $username = mysqli_real_escape_string($koneksi, $_POST['username']);
        $email = mysqli_real_escape_string($koneksi, $_POST['email']);
        $password = md5(mysqli_real_escape_string($koneksi, $_POST['password']));
        
        $cek = "SELECT * FROM tb_user WHERE username = '$username' OR email = '$email'";
        $result = mysqli_query($koneksi, $cek);
        
        if (mysqli_num_rows($result) > 0) {
            $response['value'] = 2;
            $response['message'] = "Username atau email telah digunakan";
        } else {
            $insert = "INSERT INTO tb_user (username, email, password) VALUES ('$username', '$email', '$password')";
            if (mysqli_query($koneksi, $insert)) {
                $response['value'] = 1;
                $response['message'] = "Berhasil didaftarkan";
            } else {
                $response['value'] = 0;
                $response['message'] = "Gagal didaftarkan";
            }
        }
    } else {
        $response['value'] = 0;
        $response['message'] = "Parameter yang diperlukan tidak ada";
    }

    echo json_encode($response);
} else {
    $response['value'] = 0;
    $response['message'] = "Metode permintaan tidak valid";
    echo json_encode($response);
}

?>
