<?php
// Memanggil koneksi database
include 'koneksi.php';

// Memeriksa apakah ada data yang dikirim dari form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $nama = $_POST['nama_lengkap'];
    $email = $_POST['email'];
    $pass = $_POST['password'];

    // Mengenkripsi password demi keamanan
    $hashed_password = password_hash($pass, PASSWORD_DEFAULT);
    
    // Perintah SQL untuk menyimpan data
    $sql = "INSERT INTO users (nama_lengkap, email, password) VALUES ('$nama', '$email', '$hashed_password')";

    // Eksekusi perintah
    if (mysqli_query($conn, $sql)) {
        // Jika berhasil, munculkan pop-up dan pindah ke halaman login
        echo "<script>
                alert('Akun RatingHub berhasil dibuat! Silakan login.');
                window.location.href = '../login.html'; 
              </script>";
    } else {
        // Jika email sudah pernah dipakai atau error lain
        echo "<script>
                alert('Gagal mendaftar. Email mungkin sudah digunakan.');
                window.history.back();
              </script>";
    }
}
?>