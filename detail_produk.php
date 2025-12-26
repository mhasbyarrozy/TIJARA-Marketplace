<?php
session_start();
include 'koneksi.php';

// 1. Ambil ID dari URL dan bersihkan agar tidak kena Hack (SQL Injection)
$id_produk = isset($_GET['id']) ? mysqli_real_escape_string($koneksi, $_GET['id']) : '';

if (empty($id_produk)) {
    die("ERROR: ID produk tidak disertakan dalam URL. Silakan kembali ke katalog.");
}

// 2. Query untuk mengambil data produk
// Ganti 'id' menjadi 'id_produk'
$query = "SELECT * FROM produk WHERE id_produk = '$id_produk'";
$result = mysqli_query($koneksi, $query);

// 3. CEK ERROR DATABASE
if (!$result) {
    die("ERROR DATABASE: " . mysqli_error($koneksi));
}

// 4. CEK APAKAH DATA ADA
if (mysqli_num_rows($result) == 0) {
    // Jika bagian ini muncul, berarti koneksi OK, tapi ID tersebut tidak ada di tabel
    die("ERROR: Produk dengan ID '$id_produk' tidak ditemukan di database. <br> 
         Saran: Cek lagi tabel 'produk' di phpMyAdmin, pastikan angka '$id_produk' ada di kolom 'id'.");
}

$data = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Produk: <?php echo htmlspecialchars($data['nama_produk']); ?></title>
    <style>
        .product-detail { max-width: 800px; margin: 20px auto; border: 1px solid #ddd; padding: 30px; background: #fff; border-radius: 8px; font-family: sans-serif; }
        .product-detail img { max-width: 400px; height: auto; float: left; margin-right: 30px; border-radius: 5px; }
        .price { font-size: 24px; color: #d9534f; font-weight: bold; margin: 15px 0; }
        .stok { color: green; font-weight: bold; }
        .deskripsi { clear: both; padding-top: 20px; line-height: 1.6; }
        .btn-keranjang { padding: 12px 20px; background-color: #5cb85c; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; }
    </style>
</head>
<body>
    <div class="product-detail">
        <?php 
            // Ambil nama file dan encode agar spasi/kurung terbaca browser
            $nama_file_gambar = rawurlencode($data['gambar']); 
            $path_gambar = "img/" . $nama_file_gambar; // Mengarah ke folder img/
        ?>
        
        <img src="<?php echo $path_gambar; ?>" alt="<?php echo htmlspecialchars($data['nama_produk']); ?>">
        
        <h1><?php echo htmlspecialchars($data['nama_produk']); ?></h1>
        <p class="price">Rp <?php echo number_format($data['harga'], 0, ',', '.'); ?></p>
        <p class="stok"><?php echo ($data['stok'] > 0) ? "Stok Tersedia: " . $data['stok'] : "STOK HABIS"; ?></p> 

        <a href="keranjang_proses.php?id=<?php echo $data['id_produk']; ?>" style="text-decoration: none;">
    <button class="btn-keranjang">
        🛒 Masukkan Keranjang
    </button>
</a>

        <div class="deskripsi">
            <hr>
            <h2>Deskripsi Lengkap</h2>
            <p><?php echo nl2br(htmlspecialchars($data['deskripsi'])); ?></p>
        </div>
        
        <p style="margin-top: 40px;"><a href="index.php">← Kembali ke Katalog</a></p>
    </div>
    <?php mysqli_close($koneksi); ?>
</body>
</html>