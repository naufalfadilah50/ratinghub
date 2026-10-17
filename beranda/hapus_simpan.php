<?php
session_start();
include "../koneksi.php";

if (!isset($_SESSION['nama']) || !isset($_GET['id'])) {
    header("Location: saved.php");
    exit();
}

$nama_user = $_SESSION['nama'];
$id_film = $_GET['id'];
$from = isset($_GET['from']) ? $_GET['from'] : 'saved';

// Perintah Hapus
mysqli_query($koneksi, "DELETE FROM simpan_film WHERE nama_user = '$nama_user' AND id_film = '$id_film'");

// Balikkan ke halaman asal
if ($from == 'detail') {
    header("Location: detail.php?id=$id_film");
} else {
    header("Location: saved.php");
}
?>