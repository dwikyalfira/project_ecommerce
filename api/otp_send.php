<?php
require '../vendor/autoload.php'; // Ensure this path is correct and points to the Composer autoload file
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Database connection
include 'koneksi.php';

// Headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Check request method
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $response = array();

    // Check if email is set
    if (isset($_POST['email'])) {
        $email = $_POST['email'];

        // Sanitize email input
        $email = mysqli_real_escape_string($koneksi, $email);

        // Check if email exists
        $checkEmailQuery = "SELECT * FROM tb_user WHERE email='$email'";
        $emailResult = mysqli_query($koneksi, $checkEmailQuery);

        if (mysqli_num_rows($emailResult) > 0) {
            // Generate OTP
            $otp = rand(100000, 999999);

            // Store OTP in the database
            $updateQuery = "UPDATE tb_user SET code_verification='$otp' WHERE email='$email'";
            if (mysqli_query($koneksi, $updateQuery)) {
                // Send OTP via email
                $mail = new PHPMailer(true);

                try {
                    //Server settings
                    $mail->isSMTP();
                    $mail->Host       = 'smtp.gmail.com'; // Set the SMTP server
                    $mail->SMTPAuth   = true;
                    $mail->Username   = 'gamelyfe05@gmail.com'; // SMTP username
                    $mail->Password   = 'lrlz shbs rhom ahow'; // SMTP password
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port       = 587;

                    //Recipients
                    $mail->setFrom('otp@verif.com', 'Project E-Commerce');
                    $mail->addAddress($email);

                    // Content
                    $mail->isHTML(true);
                    $mail->Subject = 'Project E-Commerce [' . $otp . ']';
                    $mail->Body    = 'Masukkan kode OTP ini untuk memverifikasi akun Anda: ' . $otp;

                    $mail->send();
                    $response['value'] = 1;
                    $response['message'] = 'OTP berhasil dikirim';
                } catch (Exception $e) {
                    $response['value'] = 0;
                    $response['message'] = 'Gagal mengirim OTP: ' . $mail->ErrorInfo;
                }
            } else {
                $response['value'] = 0;
                $response['message'] = 'Gagal menyimpan OTP: ' . mysqli_error($koneksi);
            }
        } else {
            $response['value'] = 0;
            $response['message'] = 'Email tidak ditemukan';
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
