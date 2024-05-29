<?php

header("Access-Control-Allow-Origin: *");
include 'koneksi.php';

if($_SERVER['REQUEST_METHOD'] == "POST") {

    $response = array();
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = md5($_POST['password']);
    
    $cek = "SELECT * FROM tb_user WHERE username = '$username' OR email = '$email'";
    $result = mysqli_fetch_array(mysqli_query($koneksi, $cek));

    if(isset($result)){
        $response['value'] = 2;
        $response['message'] = "Username atau email telah digunakan";
        echo json_encode($response);
    } else {
        // Assuming tb_user has columns: id, username, email, password, created_at
        $insert = "INSERT INTO tb_user (id, username, email, password, created_at) VALUES (NULL, '$username', '$email', '$password', NOW())";
        if(mysqli_query($koneksi, $insert)){
            $response['value'] = 1;
            $response['message'] = "Berhasil didaftarkan";
            echo json_encode($response);
        } else {
            $response['value'] = 0;
            $response['message'] = "Gagal didaftarkan";
            echo json_encode($response);
        }
    }
}

?>
