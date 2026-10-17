<?php
session_start();
// Gunakan ../ untuk keluar dari folder login dan mencari koneksi.php di folder utama
include "../koneksi.php"; 

$email = $_POST['email'];
$password = $_POST['password'];

// Cek apakah koneksi berhasil (opsional untuk debugging)
if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// 1. Ambil data user berdasarkan email
$query = mysqli_query($koneksi, "SELECT * FROM users WHERE email='$email'");
$data = mysqli_fetch_assoc($query);

// 2. Cek apakah user ditemukan
if (mysqli_num_rows($query) > 0) {
    
    // 3. Verifikasi password hash (menggunakan data dari kolom password di DB)
    if (password_verify($password, $data['password'])) {
        
        // Simpan nama_lengkap ke session agar muncul di beranda
        $_SESSION['nama'] = $data['nama_lengkap']; 
        $_SESSION['status'] = "login";

        echo "<script>
                alert('Login Berhasil! Selamat Datang " . $data['nama_lengkap'] . "');
                window.location.href = '../beranda/beranda.php';
              </script>";
    } else {
        echo "<script>
                alert('Login Gagal! Password salah.');
                window.location.href = 'login.html';
              </script>";
    }
} else {
    echo "<script>
            alert('Login Gagal! Email tidak terdaftar.');
            window.location.href = 'login.html';
          </script>";
}
?>