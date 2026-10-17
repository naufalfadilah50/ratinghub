<?php
session_start();
include "../koneksi.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_SESSION['nama'])) {
        header("Location: ../login/login.html");
        exit();
    }

    $nama_user = $_SESSION['nama'];
    $pw_lama = $_POST['password_lama'];
    $pw_baru = $_POST['password_baru'];
    $konfirmasi = $_POST['konfirmasi_password'];

    // 1. Ambil data password hash dari database
    $query = mysqli_query($koneksi, "SELECT password FROM users WHERE nama_lengkap = '$nama_user'");
    $data = mysqli_fetch_assoc($query);

    if (!$data) {
        echo "<script>alert('User tidak ditemukan!'); window.location='profile.php';</script>";
        exit();
    }

    $hash_database = $data['password'];

    // 2. VALIDASI PASSWORD MENGGUNAKAN password_verify (UNTUK HASH)
    // Fungsi ini mengecek apakah password yang diketik cocok dengan hash di DB
    if (!password_verify($pw_lama, $hash_database)) {
        echo "<script>alert('Gagal! Kata sandi saat ini tidak sesuai.'); window.location='profile.php';</script>";
        exit();
    }

    // 3. Cek Konfirmasi Password Baru
    if ($pw_baru !== $konfirmasi) {
        echo "<script>alert('Gagal! Konfirmasi kata sandi baru tidak cocok.'); window.location='profile.php';</script>";
        exit();
    }

    // 4. Update Password (WAJIB DI-HASH LAGI SEBELUM DISIMPAN)
    $password_baru_di_hash = password_hash($pw_baru, PASSWORD_DEFAULT);
    $update = mysqli_query($koneksi, "UPDATE users SET password = '$password_baru_di_hash' WHERE nama_lengkap = '$nama_user'");

    if ($update) {
        echo "<script>alert('Berhasil! Keamanan akun Anda telah diperbarui.'); window.location='profile.php';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui data.'); window.location='profile.php';</script>";
    }
} else {
    header("Location: profile.php");
    exit();
}
?>