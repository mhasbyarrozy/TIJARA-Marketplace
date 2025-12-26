<?php
session_start();
include 'koneksi.php';

// Pastikan hanya admin yang bisa memproses
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header("location: login.html");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. Ambil data teks dari form
    $nama_produk = mysqli_real_escape_string($koneksi, $_POST['nama_produk']);
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];
    
    // --- 2. Proses Upload Gambar ---
    $target_dir = "assets/img/produk/"; // Tentukan folder tempat gambar disimpan
    $nama_file = basename($_FILES["gambar"]["name"]);
    $target_file = $target_dir . $nama_file;
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Pindahkan file yang di-upload ke folder tujuan
    if (move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file)) {
        // Jika upload sukses, nama_file akan disimpan di database
    } else {
        // Jika gagal upload (misalnya folder tidak ada)
        echo "<script>alert('Error saat mengupload gambar.'); window.location.href='dashboard_admin.php';</script>";
        exit;
    }
    
    // --- 3. Tulis Query SQL untuk menyimpan data ke tabel 'produk' ---
    $query = "INSERT INTO produk (nama_produk, deskripsi, harga, stok, gambar) 
              VALUES ('$nama_produk', '$deskripsi', '$harga', '$stok', '$nama_file')";
    
    // 4. Eksekusi Query
    if (mysqli_query($koneksi, $query)) {
        // Sukses
        echo "<script>alert('Produk berhasil ditambahkan!'); window.location.href='dashboard_admin.php';</script>";
    } else {
        // Gagal
        echo "<script>alert('Gagal menambahkan produk. Error SQL: " . mysqli_error($koneksi) . "'); window.location.href='tambah_produk.php';</script>";
    }

    mysqli_close($koneksi);

} else {
    // Jika diakses tanpa form submit
    header("location: dashboard_admin.php");
}
?>