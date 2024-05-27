<?php

$koneksi = mysqli_connect("localhost", "root", "", "project_ecommerce");

if($koneksi){

	// echo "Database berhasil konek";
	
} else {
	echo "gagal Connect";
}

?>