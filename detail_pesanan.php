<?php
session_start();
include 'koneksi.php';

// Cek apakah user sudah login
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== TRUE) {
    header("location: login.html");
    exit;
}

// Cek apakah ada ID Transaksi yang dikirimkan melalui URL
if (!isset($_GET['id'])) {
    header("location: riwayat_pesanan.php");
    exit;
}

$id_transaksi = $_GET['id'];
$id_user_session = $_SESSION['id_user'];
$total_bayar = 0; // Untuk menghitung ulang total

// 1. Ambil data transaksi utama & pastikan itu milik user yang sedang login
$query_transaksi = "SELECT * FROM transaksi WHERE id_transaksi = ? AND id_user = ?";
$stmt_transaksi = $koneksi->prepare($query_transaksi);
$stmt_transaksi->bind_param("ii", $id_transaksi, $id_user_session);
$stmt_transaksi->execute();
$result_transaksi = $stmt_transaksi->get_result();

if ($result_transaksi->num_rows === 0) {
    // Jika transaksi tidak ditemukan atau bukan milik user ini
    echo "<script>alert('Akses ditolak.'); window.location.href='riwayat_pesanan.php';</script>";
    exit;
}
$transaksi = $result_transaksi->fetch_assoc();
$stmt_transaksi->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Pesanan #<?php echo $id_transaksi; ?></title>
    <style>
        body { font-family: Arial, sans-serif; width: 80%; margin: 20px auto; }
        h1 { border-bottom: 2px solid #ccc; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #f2f2f2; }
        .total-row { font-weight: bold; background-color: #e0f7fa; }
    </style>
</head>
<body>
    <h1>Detail Pesanan #<?php echo $id_transaksi; ?></h1>
    
    <p>
        **Status Pesanan:** <span style="font-size: 1.1em; color: <?php echo $transaksi['status'] == 'Menunggu Pembayaran' ? 'orange' : 'green'; ?>;"><?php echo htmlspecialchars($transaksi['status']); ?></span><br>
        **Tanggal Pesan:** <?php echo date('d M Y H:i', strtotime($transaksi['tanggal_pesan'])); ?><br>
        **Total Pembayaran Final:** Rp <?php echo number_format($transaksi['total_bayar'], 0, ',', '.'); ?>
    </p>
    
    <hr>
    
    <h2>Item yang Dipesan</h2>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Produk</th>
                <th>Harga Satuan</th>
                <th>Kuantitas</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            // 2. Ambil detail produk dari tabel detail_transaksi
            // Menggunakan JOIN untuk mengambil nama produk dari tabel produk
            $query_detail = "
                SELECT dt.*, p.nama_produk 
                FROM detail_transaksi dt 
                JOIN produk p ON dt.id_produk = p.id_produk 
                WHERE dt.id_transaksi = ?";
                
            $stmt_detail = $koneksi->prepare($query_detail);
            $stmt_detail->bind_param("i", $id_transaksi);
            $stmt_detail->execute();
            $result_detail = $stmt_detail->get_result();
            
            while($detail = $result_detail->fetch_assoc()):
                $total_bayar += $detail['subtotal'];
            ?>
                <tr>
                    <td><?php echo $no++; ?></td>
                    <td><?php echo htmlspecialchars($detail['nama_produk']); ?></td>
                    <td>Rp <?php echo number_format($detail['subtotal'] / $detail['kuantitas'], 0, ',', '.'); ?></td>
                    <td><?php echo $detail['kuantitas']; ?></td>
                    <td>Rp <?php echo number_format($detail['subtotal'], 0, ',', '.'); ?></td>
                </tr>
            <?php endwhile; ?>
            
            <tr class="total-row">
                <td colspan="4" style="text-align: right;">GRAND TOTAL:</td>
                <td>Rp <?php echo number_format($total_bayar, 0, ',', '.'); ?></td>
            </tr>
        </tbody>
    </table>
    
    <p style="margin-top: 20px;"><a href="riwayat_pesanan.php">← Kembali ke Riwayat Pembelian</a></p>

    <?php $stmt_detail->close(); mysqli_close($koneksi); ?>
</body>
</html>