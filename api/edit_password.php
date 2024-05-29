<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $response = array();

    if (isset($_POST['id']) && isset($_POST['password'])) {
        $id = $_POST['id'];
        $password = $_POST['password'];

        $id = mysqli_real_escape_string($koneksi, $id);
        $password = mysqli_real_escape_string($koneksi, $password);

        // Encrypt the new password using md5
        $password_md5 = md5($password);

        // Update the password
        $update = "UPDATE tb_user SET password = '$password_md5', updated = NOW() WHERE id = '$id'";
        if (mysqli_query($koneksi, $update)) {
            $response['value'] = 1;
            $response['message'] = "Password updated successfully";
        } else {
            $response['value'] = 0;
            $response['message'] = "Failed to update password";
        }
    } else {
        // Missing required POST parameters
        $response['value'] = 0;
        $response['message'] = "Missing required parameters";
    }

    echo json_encode($response);

} else {
    // Invalid request method
    $response['value'] = 0;
    $response['message'] = "Invalid request method";
    echo json_encode($response);
}

?>
