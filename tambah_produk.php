<?php
session_start();
// Cek pengamanan ganda: Hanya admin yang bisa akses form ini
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header("location: login.html"); // Atau dashboard_admin.php
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Produk Baru</title>
</head>
<body>
    <h1>Tambah Produk Baru</h1>
    
    <form action="tambah_produk_proses.php" method="POST" enctype="multipart/form-data">
        
        <label for="nama_produk">Nama Produk:</label><br>
        <input type="text" id="nama_produk" name="nama_produk" required><br><br>

        <label for="deskripsi">Deskripsi:</label><br>
        <textarea id="deskripsi" name="deskripsi" rows="4" required></textarea><br><br>

        <label for="harga">Harga (Rp):</label><br>
        <input type="number" id="harga" name="harga" required><br><br>

        <label for="stok">Stok:</label><br>
        <input type="number" id="stok" name="stok" required><br><br>

        <label for="gambar">Gambar Produk:</label><br>
        <input type="file" id="gambar" name="gambar"><br><br>

        <button type="submit">SIMPAN PRODUK</button>
    </form>
    
    <hr>
    <p><a href="dashboard_admin.php">Kembali ke Dashboard</a></p>
</body>
</html>