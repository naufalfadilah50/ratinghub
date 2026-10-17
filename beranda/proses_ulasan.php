<?php
session_start();
include "../koneksi.php";

// Pastikan yang mengakses file ini datang dari tombol "Kirim Form"
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_film = $_POST['id_film'];
    $nama = $_SESSION['nama']; // Mengambil nama nopal yang sedang login
    $rating = $_POST['rating'];
    
    // Keamanan: hindari serangan SQL Injection menggunakan mysqli_real_escape_string
    $komentar = mysqli_real_escape_string($koneksi, $_POST['komentar']);

    // Perintah memasukkan data ke tabel ulasan
    $query = "INSERT INTO ulasan (id_film, nama_user, rating, komentar) VALUES ('$id_film', '$nama', '$rating', '$komentar')";
    
    // Eksekusi query
    if (mysqli_query($koneksi, $query)) {
        // Jika berhasil, tendang balik ke halaman detail film tersebut
        header("Location: detail.php?id=" . $id_film);
        exit();
    } else {
        echo "Gagal mengirim ulasan: " . mysqli_error($koneksi);
    }
} else {
    // Jika ada yang iseng akses file ini langsung lewat URL, tendang ke beranda
    header("Location: beranda.php");
    exit();
}
?>