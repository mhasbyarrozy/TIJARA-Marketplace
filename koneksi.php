<?php
// === KONFIGURASI KONEKSI DATABASE TIJARA ===

$host = "localhost";        
$user = "root";             
$password = "";             
$database = "tijara_db";    

// Mencoba membuat koneksi
$koneksi = mysqli_connect($host, $user, $password, $database);

// Cek jika koneksi gagal
if (mysqli_connect_errno()){
	echo "Koneksi database GAGAL : " . mysqli_connect_error();
    die(); 
} 
?>