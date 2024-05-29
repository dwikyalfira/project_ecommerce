<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $response = array();
    $id = $_POST['id'];
    $username = $_POST['username'];
    $email = $_POST['email'];

    // Sanitize inputs to prevent SQL injection
    $id = mysqli_real_escape_string($koneksi, $id);
    $username = mysqli_real_escape_string($koneksi, $username);
    $email = mysqli_real_escape_string($koneksi, $email);

    // Check if the username or email is already taken by another user (excluding the current user)
    $cek = "SELECT * FROM tb_user WHERE (username = '$username' OR email = '$email') AND id != '$id'";
    $result = mysqli_query($koneksi, $cek);

    if (mysqli_num_rows($result) > 0) {
        $response['value'] = 2;
        $response['message'] = "Username or email is already in use";
        echo json_encode($response);
    } else {
        // Update the user details and the updated field
        $update = "UPDATE tb_user SET username = '$username', email = '$email', updated = NOW() WHERE id = '$id'";
        if (mysqli_query($koneksi, $update)) {
            $response['value'] = 1;
            $response['message'] = "Profil berhasil di perbarui";
            echo json_encode($response);
        } else {
            $response['value'] = 0;
            $response['message'] = "Profil gagal di perbarui";
            echo json_encode($response);
        }
    }
} else {
    // Invalid request method
    $response['value'] = 0;
    $response['message'] = "Invalid request method";
    echo json_encode($response);
}

?>
