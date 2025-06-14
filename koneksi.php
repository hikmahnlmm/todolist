<?php
$host = "localhost";
$user = "root"; // atau user lain sesuai setting kamu
$pass = "";     // sesuaikan juga password-nya
$db   = "Bismillah_lsp"; // ganti dengan nama database kamu

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>