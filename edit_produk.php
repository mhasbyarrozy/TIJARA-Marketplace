<?php
session_start();
include 'koneksi.php';

// Cek Pengamanan: Hanya Admin yang bisa mengakses
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header("location: login.html");
    exit;
}

// 1. Ambil ID dari URL
$id_produk = isset($_GET['id']) ? $_GET['id'] : die("ERROR: ID produk tidak ditemukan.");

// 2. Query untuk mengambil data produk berdasarkan ID
$query = "SELECT * FROM produk WHERE id_produk = '$id_produk'";
$result = mysqli_query($koneksi, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    die("ERROR: Data produk tidak ditemukan.");
}

$data = mysqli_fetch_assoc($result);

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Produk: <?php echo htmlspecialchars($data['nama_produk']); ?></title>
</head>
<body>
    <h1>Edit Produk: <?php echo htmlspecialchars($data['nama_produk']); ?></h1>
    
    <form action="edit_produk_proses.php" method="POST" enctype="multipart/form-data">
        
        <input type="hidden" name="id_produk" value="<?php echo $data['id_produk']; ?>">
        <input type="hidden" name="gambar_lama" value="<?php echo $data['gambar']; ?>">

        <label for="nama_produk">Nama Produk:</label><br>
        <input type="text" id="nama_produk" name="nama_produk" value="<?php echo htmlspecialchars($data['nama_produk']); ?>" required><br><br>

        <label for="deskripsi">Deskripsi:</label><br>
        <textarea id="deskripsi" name="deskripsi" rows="4" required><?php echo htmlspecialchars($data['deskripsi']); ?></textarea><br><br>

        <label for="harga">Harga (Rp):</label><br>
        <input type="number" id="harga" name="harga" value="<?php echo $data['harga']; ?>" required><br><br>

        <label for="stok">Stok:</label><br>
        <input type="number" id="stok" name="stok" value="<?php echo $data['stok']; ?>" required><br><br>

        <label>Gambar Saat Ini:</label><br>
        <?php if (!empty($data['gambar'])): ?>
            <img src="assets/img/produk/<?php echo htmlspecialchars($data['gambar']); ?>" style="max-width: 150px;"><br>
            <small>Kosongkan kolom di bawah jika tidak ingin mengganti gambar.</small><br>
        <?php else: ?>
            <small>Belum ada gambar.</small><br>
        <?php endif; ?>
        
        <label for="gambar">Ganti Gambar Produk:</label><br>
        <input type="file" id="gambar" name="gambar"><br><br>

        <button type="submit">SIMPAN PERUBAHAN</button>
    </form>
    
    <hr>
    <p><a href="daftar_produk_admin.php">Kembali ke Daftar Produk</a></p>
    <?php mysqli_close($koneksi); ?>
</body>
</html>