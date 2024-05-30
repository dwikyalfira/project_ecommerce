<?php
include 'koneksi.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

if (isset($_POST['cart_id'])) {
    $cart_id = $_POST['cart_id'];

    $query = "DELETE FROM tb_cart WHERE cart_id='$cart_id'";
    $result = mysqli_query($koneksi, $query);

    if ($result && mysqli_affected_rows($koneksi) > 0) {
        $response = array(
            'status' => 'success',
            'message' => 'Item berhasil dihapus'
        );
    } else {
        $response = array(
            'status' => 'failed',
            'message' => 'Gagal hapus Item atau Item tidak ditemukan'
        );
    }
} else {
    $response = array(
        'status' => 'failed',
        'message' => 'Parameter tidak ditemukan'
    );
}

echo json_encode($response);

mysqli_close($koneksi);
?>
