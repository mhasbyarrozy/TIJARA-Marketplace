<?php
// Cek jika admin belum login, redirect ke login_admin.php
session_start();
if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== TRUE) {
    header("location: login_admin.php");
    exit;
}
include '../koneksi.php'; // Sesuaikan path koneksi
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin - Daftar Pesanan</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f9f9f9; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; background: white; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background-color: #3f51b5; color: white; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        
        /* Style Status */
        .pending { color: orange; font-weight: bold; }
        .processed { color: blue; }
        .success { color: green; font-weight: bold; }
        .danger { color: red; }
        
        /* Style Navigasi */
        .nav-admin { background: #3f51b5; padding: 15px; margin-bottom: 20px; border-radius: 5px; display: flex; gap: 20px; align-items: center; }
        .nav-admin a { color: white; text-decoration: none; font-weight: bold; font-size: 14px; }
        .nav-admin a:hover { text-decoration: underline; }
        .logout-btn { background: #ffc107; color: #333 !important; padding: 5px 10px; border-radius: 4px; margin-left: auto; }
    </style>
</head>
<body>

    <h1>🏠 Dashboard Admin TIJARA</h1>

    <div class="nav-admin">
        <a href="index.php">📋 Daftar Pesanan</a>
        <a href="daftar_produk_admin.php">📦 Kelola Produk</a>
        <a href="../index.php" target="_blank">🏠 Lihat Toko</a>
        <a href="logout_admin.php" class="logout-btn">🚪 Logout Admin</a>
    </div>

    <h2>Daftar Semua Pesanan User</h2>

    <table>
        <thead>
            <tr>
                <th>ID Transaksi</th>
                <th>Nama User</th>
                <th>Tanggal Pesan</th>
                <th>Total Bayar</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Query untuk mengambil semua transaksi user (seperti rhozy)
            $query = "SELECT t.*, u.username 
                      FROM transaksi t 
                      JOIN user u ON t.id_user = u.id 
                      ORDER BY tanggal_pesan DESC";
            
            $result = mysqli_query($koneksi, $query);
            
            if (mysqli_num_rows($result) > 0):
                while($data = mysqli_fetch_assoc($result)):
                    // Menentukan warna berdasarkan status pesanan
                    $status_class = strtolower(str_replace(' ', '', $data['status']));
                    $class_map = [
                        'menunggupembayaran' => 'pending', 
                        'diproses' => 'processed', 
                        'dikirim' => 'processed',
                        'selesai' => 'success',
                        'dibatalkan' => 'danger'
                    ];
                    $current_class = $class_map[$status_class] ?? '';
            ?>
                <tr>
                    <td><?php echo $data['id_transaksi']; ?></td>
                    <td><?php echo htmlspecialchars($data['username']); ?></td>
                    <td><?php echo date('d M Y H:i', strtotime($data['tanggal_pesan'])); ?></td>
                    <td>Rp <?php echo number_format($data['total_bayar'], 0, ',', '.'); ?></td>
                    <td>
                        <span class="<?php echo $current_class; ?>">
                            <?php echo htmlspecialchars($data['status']); ?>
                        </span>
                    </td>
                    <td>
                        <a href="ubah_status.php?id=<?php echo $data['id_transaksi']; ?>" style="color: #3f51b5; font-weight: bold; text-decoration: none;">⚙️ Kelola Status</a>
                    </td>
                </tr>
            <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="text-align: center;">Belum ada pesanan masuk.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <?php mysqli_close($koneksi); ?>
</body>
</html>