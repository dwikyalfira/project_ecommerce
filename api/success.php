<?php

header("Access-Control-Allow-Origin: *");

include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Pastikan bahwa semua parameter yang diperlukan tersedia
    if (isset($_POST['id_user'])) {
        $order_id = $_POST['order_id'];
        $id_user = $_POST['id_user'];

        // Cek apakah id_user sudah ada dalam tabel cart
        $check_query = "SELECT * FROM payments WHERE order_id = '$order_id'";
        $check_result = $koneksi->query($check_query);

        if ($check_result->num_rows > 0) {
            // id_user sudah ada dalam tabel cart
            $sql_delete_cart = "DELETE FROM tb_cart WHERE id_user='$id_user'";
            if ($koneksi->query($sql_delete_cart) === TRUE) {
                // Hapus data cart berhasil, sekarang perbarui status pembayaran
                $sql_update_payment = "UPDATE payments SET status='success' WHERE order_id='$order_id'";
                if ($koneksi->query($sql_update_payment) === TRUE) {
                    $response['isSuccess'] = true;
                    $response['message'] = "Berhasil membayar pesanan dan menghapus cart";
                } else {
                    $response['isSuccess'] = false;
                    $response['message'] = "Gagal mengupdate status pembayaran: " . $koneksi->error;
                }
            } else {
                $response['isSuccess'] = false;
                $response['message'] = "Gagal menghapus cart: " . $koneksi->error;
            }
        } else {
            // id_user belum ada dalam tabel cart, tangani sesuai kebutuhan
            $response['isSuccess'] = false;
            $response['message'] = "Produk sudah ada di like";
        }
    } else {
        $response['isSuccess'] = false;
        $response['message'] = "Parameter tidak lengkap";
    }
} else {
    $response['isSuccess'] = false;
    $response['message'] = "Metode yang diperbolehkan hanya POST";
}

echo json_encode($response);
