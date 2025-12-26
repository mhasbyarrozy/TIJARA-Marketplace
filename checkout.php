<?php
session_start(); // Wajib di baris pertama
include 'koneksi.php';

/** * 1. PERBAIKAN SESSION LOGIN
 * Pastikan variabel 'pelanggan' atau 'id_user' sesuai dengan file login kamu.
 * Di sini saya gunakan 'pelanggan' sebagai standar umum.
 */
if (!isset($_SESSION['pelanggan']) && !isset($_SESSION['loggedin'])) {
    echo "<script>alert('Anda harus login untuk melanjutkan ke Checkout.'); window.location.href='login.php';</script>";
    exit;
}

// 2. Cek apakah keranjang kosong
if (!isset($_SESSION['keranjang']) || empty($_SESSION['keranjang'])) {
    echo "<script>alert('Keranjang belanja Anda kosong! Silakan pilih produk dulu.'); window.location.href='index.php';</script>";
    exit;
}

// 3. Ambil data user dari session agar tidak error
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Pelanggan';
// Hitung ulang total jika session total_bayar tidak ada
$total_bayar = isset($_SESSION['total_bayar']) ? $_SESSION['total_bayar'] : 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Konfirmasi Pesanan</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; padding: 20px; background: #f4f4f4; }
        .checkout-box { max-width: 600px; margin: auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .table-checkout { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .table-checkout th, .table-checkout td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        .btn-final { width: 100%; padding: 15px; background-color: #d9534f; color: white; border: none; font-size: 18px; cursor: pointer; border-radius: 5px; }
        .btn-final:hover { background-color: #c9302c; }
    </style>
</head>
<body>

<div class="checkout-box">
    <h1>Konfirmasi Pesanan</h1>
    <p>Selamat datang, <strong><?php echo htmlspecialchars($username); ?></strong>.</p>
    
    <table class="table-checkout">
        <thead>
            <tr>
                <th>Produk</th>
                <th>Qty</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $total_belanja = 0;
            foreach ($_SESSION['keranjang'] as $id_produk => $jumlah): 
                $ambil = $koneksi->query("SELECT * FROM produk WHERE id_produk='$id_produk'");
                $pecah = $ambil->fetch_assoc();
                $subharga = $pecah['harga'] * $jumlah;
            ?>
            <tr>
                <td><?php echo $pecah['nama_produk']; ?></td>
                <td><?php echo $jumlah; ?></td>
                <td>Rp <?php echo number_format($subharga, 0, ',', '.'); ?></td>
            </tr>
            <?php 
                $total_belanja += $subharga;
            endforeach; 
            ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="2">Total Bayar</th>
                <th>Rp <?php echo number_format($total_belanja, 0, ',', '.'); ?></th>
            </tr>
        </tfoot>
    </table>

    <div style="background: #fdf7f7; border-left: 5px solid #d9534f; padding: 10px; margin-bottom: 20px;">
        <strong>Alamat Pengiriman:</strong><br>
        <?php 
            // Opsional: Ambil alamat dari database jika sudah ada
            echo "Alamat default pelanggan (Silakan lengkapi di profil)";
        ?>
    </div>

    <form action="transaksi_proses.php" method="post">
        <input type="hidden" name="total_akhir" value="<?php echo $total_belanja; ?>">
        <button type="submit" class="btn-final">📦 FINALISASI PESANAN</button>
    </form>

    <p style="text-align: center;"><a href="keranjang.php" style="color: #666; text-decoration: none;">← Kembali ke Keranjang</a></p>
</div>

</body>
</html>