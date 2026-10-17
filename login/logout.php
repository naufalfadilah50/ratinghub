<?php
session_start();

// Menghapus semua variabel session
session_unset();

// Menghancurkan session
session_destroy();

// Mengarahkan kembali ke halaman login
header("Location: login.html");
exit();
?>