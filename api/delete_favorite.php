<?php
include 'koneksi.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

if (isset($_POST['favorite_id'])) {
    $favorite_id = $_POST['favorite_id'];

    $query = "DELETE FROM tb_favorite WHERE favorite_id='$favorite_id'";
    $result = mysqli_query($koneksi, $query);

    if ($result && mysqli_affected_rows($koneksi) > 0) {
        $response = array(
            'status' => 'success',
            'message' => 'Favorite berhasil dihapus'
        );
    } else {
        $response = array(
            'status' => 'failed',
            'message' => 'Gagal hapus favorite atau favorite tidak ditemukan'
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
