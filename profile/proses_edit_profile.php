<?php
session_start();
include "../koneksi.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_lama = $_SESSION['nama'];
    $nama_baru = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $email_baru = mysqli_real_escape_string($koneksi, $_POST['email']);
    $bio_baru = mysqli_real_escape_string($koneksi, $_POST['bio']);

    // Cek apakah ada upload foto
    if ($_FILES['foto']['name'] != "") {
        $ekstensi_diperbolehkan = array('png', 'jpg', 'jpeg');
        $nama_file = $_FILES['foto']['name'];
        $x = explode('.', $nama_file);
        $ekstensi = strtolower(end($x));
        $file_tmp = $_FILES['foto']['tmp_name'];

        if (in_array($ekstensi, $ekstensi_diperbolehkan) === true) {
            // Beri nama unik agar tidak tertukar
            $nama_foto_baru = time() . "_" . $nama_file;
            move_uploaded_file($file_tmp, '../beranda/img/' . $nama_foto_baru);
            
            // Query Update dengan Foto
            $query = "UPDATE users SET nama_lengkap='$nama_baru', email='$email_baru', bio='$bio_baru', foto='$nama_foto_baru' WHERE nama_lengkap='$nama_lama'";
        }
    } else {
        // Query Update tanpa ganti foto
        $query = "UPDATE users SET nama_lengkap='$nama_baru', email='$email_baru', bio='$bio_baru' WHERE nama_lengkap='$nama_lama'";
    }

    if (mysqli_query($koneksi, $query)) {
        // Update Session nama agar sinkron di semua halaman
        $_SESSION['nama'] = $nama_baru;
        header("Location: profile.php?status=sukses");
    } else {
        header("Location: profile.php?status=gagal");
    }
}
?>