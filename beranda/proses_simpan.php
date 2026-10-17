<?php
session_start();
include "../koneksi.php";

if (!isset($_SESSION['nama']) || !isset($_GET['id'])) {
    header("Location: beranda.php");
    exit();
}

$nama_user = $_SESSION['nama'];
$id_film = $_GET['id'];

// Cek apakah film sudah pernah disimpan sebelumnya
$cek = mysqli_query($koneksi, "SELECT * FROM simpan_film WHERE nama_user = '$nama_user' AND id_film = '$id_film'");

// Jika film belum ada di database user ini, kita simpan!
if (mysqli_num_rows($cek) == 0) {
    mysqli_query($koneksi, "INSERT INTO simpan_film (nama_user, id_film) VALUES ('$nama_user', '$id_film')");
}

// Kembalikan ke halaman detail secara kilat (user tidak akan merasa halamannya loading)
header("Location: detail.php?id=$id_film");
?>