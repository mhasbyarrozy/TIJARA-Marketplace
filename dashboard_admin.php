<?php
session_start();

// 1. Cek apakah pengguna sudah login
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== TRUE) {
    // Jika belum login, arahkan ke halaman login
    header("location: login.html");
    exit;
}

// 2. Cek apakah role pengguna BUKAN 'admin'
if ($_SESSION['role'] !== 'admin') {
    // Jika bukan admin, arahkan ke beranda biasa atau halaman akses ditolak
    header("location: index.php");
    exit;
}

// JIKA LULUS CEK: Pengguna adalah Admin
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin TIJARA</title>
</head>
<body>
    <h1>Selamat Datang, <?php echo htmlspecialchars($_SESSION['username']); ?> di Dashboard Admin!</h1>
    <p>Ini adalah pusat kontrol Anda untuk mengelola toko.</p>

    <h2>Manajemen Produk</h2>
    <ul>
        <li><a href="tambah_produk.php">Tambah Produk Baru</a></li>
        <li><a href="daftar_produk_admin.php">Lihat/Edit Semua Produk</a></li>
    </ul>

    <hr>
    <p><a href="logout.php">Logout</a></p>
</body>
</html>