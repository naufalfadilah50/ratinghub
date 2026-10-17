<?php
session_start();

// 1. Mencegah akses langsung via URL (mencegah HTTP ERROR 405)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.html");
    exit();
}

// 2. Keluar satu tingkat folder dari 'login' untuk mengambil 'koneksi.php' utama
include "../koneksi.php";

// 3. Cek koneksi ke database
if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// 4. Ambil data dari form login
$email = $_POST['email'];
$password = $_POST['password'];

// 5. Ambil data user berdasarkan email
$query = mysqli_query($koneksi, "SELECT * FROM users WHERE email='$email'");

if (mysqli_num_rows($query) > 0) {
    $data = mysqli_fetch_assoc($query);
    
    // 6. Verifikasi password hash
    if (password_verify($password, $data['password'])) {
        
        // Simpan data ke session
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