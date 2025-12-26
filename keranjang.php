<?php 
session_start();
include 'koneksi.php';

$keranjang = isset($_SESSION['keranjang']) ? $_SESSION['keranjang'] : array();
$total_bayar = 0; // Inisialisasi total pembayaran

// Pastikan session total_bayar dihapus jika keranjang kosong
if (empty($keranjang)) {
    unset($_SESSION['total_bayar']);
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Keranjang Belanja Anda</title>
    <style>
        table { width: 80%; border-collapse: collapse; margin: 20px auto; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
        th { background-color: #f2f2f2; }
        .img-thumb { max-width: 80px; height: auto; }
        .total-row { font-weight: bold; background-color: #e0f7fa; }
    </style>
</head>
<body>
    <h1>🛒 Keranjang Belanja</h1>
    <p style="text-align: center;"><a href="daftar_produk.php">← Lanjutkan Belanja</a></p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Gambar</th>
                <th>Nama Produk</th>
                <th>Harga Satuan</th>
                <th>Kuantitas</th>
                <th>Subtotal</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            
            // --- IF UTAMA: Cek Keranjang Kosong atau Tidak ---
            if (!empty($keranjang)):
                
                // Ambil semua ID produk dari session untuk query
                $ids = array_keys($keranjang);
                $id_string = implode(',', $ids);

                // Query database untuk mendapatkan detail produk yang ada di keranjang
                // PENTING: Jika $id_string kosong (meskipun di cek di atas), ini bisa error.
                if (!empty($id_string)) {
                    $query = "SELECT id_produk, nama_produk, harga, gambar FROM produk WHERE id_produk IN ($id_string)";
                    $result = mysqli_query($koneksi, $query);

                    while($data = mysqli_fetch_assoc($result)):
                        $id_produk = $data['id_produk'];
                        $kuantitas = $keranjang[$id_produk]; // Kuantitas dari Session
                        $subtotal = $data['harga'] * $kuantitas;
                        $total_bayar += $subtotal;
            ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><img src="assets/img/produk/<?php echo htmlspecialchars($data['gambar']); ?>" class="img-thumb"></td>
                        <td><?php echo htmlspecialchars($data['nama_produk']); ?></td>
                        <td>Rp <?php echo number_format($data['harga'], 0, ',', '.'); ?></td>
                        <td><?php echo $kuantitas; ?></td>
                        <td><?php echo number_format($subtotal, 0, ',', '.'); ?></td>
                        <td>
                            <a href="hapus_item_keranjang.php?id=<?php echo $id_produk; ?>" onclick="return confirm('Hapus item ini?');">Hapus</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
                
                <?php } ?>

                <tr class="total-row">
                    <td colspan="5" style="text-align: right;">Total Pembayaran:</td>
                    <td colspan="2">Rp <?php echo number_format($total_bayar, 0, ',', '.'); ?></td>
                </tr>

                <?php 
                // KODE BARU: Simpan Total Bayar ke Session agar Checkout bisa membacanya
                $_SESSION['total_bayar'] = $total_bayar; 
                ?>

            <?php else: ?>
                <tr>
                    <td colspan="7" style="text-align: center;">Keranjang belanja Anda masih kosong.</td>
                </tr>
            <?php endif; ?>
            </tbody>
    </table>

    <div style="text-align: center; margin-top: 30px;">
        <a href="checkout.php">
            <button style="padding: 15px 30px; background-color: #007bff; color: white; border: none; font-size: 18px;">
                Lanjut ke Checkout
            </button>
        </a>
    </div>

    <?php mysqli_close($koneksi); ?>
</body>
</html>