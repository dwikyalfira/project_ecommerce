<?php

// Database connection
include 'koneksi.php';

// Headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Check request method
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $response = array();

    // Check if email and otp are set
    if (isset($_POST['email']) && isset($_POST['otp'])) {
        $email = $_POST['email'];
        $otp = $_POST['otp'];

        // Sanitize inputs
        $email = mysqli_real_escape_string($koneksi, $email);
        $otp = mysqli_real_escape_string($koneksi, $otp);

        // Verify OTP
        $query = "SELECT * FROM tb_user WHERE email='$email' AND code_verification='$otp'";
        $result = mysqli_query($koneksi, $query);

        if (mysqli_num_rows($result) > 0) {
            $response['value'] = 1;
            $response['message'] = 'OTP berhasil diverifikasi';
            
            // Update user_status to 'verified' if the email and OTP are correct
            $updateQuery = "UPDATE tb_user SET user_status = 'verified' WHERE email='$email'";
            if (mysqli_query($koneksi, $updateQuery)) {
                $response['message'] = 'Status user berhasil diperbarui';
            } else {
                $response['message'] = 'Gagal memperbarui status user';
            }
        } else {
            $response['value'] = 0;
            $response['message'] = 'OTP tidak valid atau telah kedaluwarsa';
        }
    } else {
        $response['value'] = 0;
        $response['message'] = 'Parameter yang diperlukan tidak ada';
    }

    echo json_encode($response);
} else {
    $response['value'] = 0;
    $response['message'] = 'Metode permintaan tidak valid';
    echo json_encode($response);
}

mysqli_close($koneksi);
?>
