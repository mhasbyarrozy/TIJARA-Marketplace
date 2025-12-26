<?php
session_start();
include 'koneksi.php';

// Cek apakah user sudah login
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== TRUE) {
    header("location: login.html");
    exit;
}

$id_user = $_SESSION['id_user'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Riwayat Pembelian</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 80%; border-collapse: collapse; margin: 20px auto; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #f2f2f2; }
        .success { color: green; font-weight: bold; }
        .pending { color: orange; font-weight: bold; }
    </style>
</head>
<body>
    <h1>🧾 Riwayat Pembelian Anda</h1>
    <p style="text-align: center;"><a href="index.php">← Kembali ke Beranda</a></p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>ID Transaksi</th>
                <th>Tanggal Pesan</th>
                <th>Total Bayar</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            // Ambil semua transaksi milik user ini (id_user = id_user di session)
            $query = "SELECT id_transaksi, tanggal_pesan, total_bayar, status FROM transaksi WHERE id_user = ? ORDER BY tanggal_pesan DESC";
            
            $stmt = $koneksi->prepare($query);
            $stmt->bind_param("i", $id_user);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0):
                while($data = $result->fetch_assoc()):
            ?>
                <tr>
                    <td><?php echo $no++; ?></td>
                    <td><?php echo $data['id_transaksi']; ?></td>
                    <td><?php echo date('d M Y H:i', strtotime($data['tanggal_pesan'])); ?></td>
                    <td>Rp <?php echo number_format($data['total_bayar'], 0, ',', '.'); ?></td>
                    <td>
                        <span class="<?php echo strtolower(str_replace(' ', '', $data['status'])) == 'menunggupembayaran' ? 'pending' : 'success'; ?>">
                            <?php echo htmlspecialchars($data['status']); ?>
                        </span>
                    </td>
                    <td>
                        <a href="detail_pesanan.php?id=<?php echo $data['id_transaksi']; ?>">Lihat Detail</a>
                    </td>
                </tr>
            <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="text-align: center;">Anda belum memiliki riwayat pembelian.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <?php $stmt->close(); mysqli_close($koneksi); ?>
</body>
</html>