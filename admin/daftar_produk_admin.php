<?php
session_start();
include '../koneksi.php';

// Cek Admin Login
if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== TRUE) {
    header("location: login_admin.php");
    exit;
}

// =======================================================
// LOGIKA HAPUS PRODUK
// =======================================================
if (isset($_GET['action']) && $_GET['action'] == 'hapus' && isset($_GET['id'])) {
    $id_produk_hapus = $_GET['id'];
    
    // Hapus menggunakan prepared statement untuk keamanan
    $query_hapus = "DELETE FROM produk WHERE id_produk = ?";
    $stmt_hapus = $koneksi->prepare($query_hapus);
    $stmt_hapus->bind_param("i", $id_produk_hapus);
    
    if ($stmt_hapus->execute()) {
        echo "<script>alert('Produk berhasil dihapus!'); window.location.href='daftar_produk_admin.php';</script>";
    } else {
        // Error sering terjadi jika produk sudah ada di detail_transaksi (Foreign Key Constraint)
        echo "<script>alert('Gagal menghapus produk: Mungkin produk ini masih ada di riwayat pesanan (Error: " . $stmt_hapus->error . ")'); window.location.href='daftar_produk_admin.php';</script>";
    }
    $stmt_hapus->close();
}

// =======================================================
// AMBIL SEMUA DATA PRODUK
// =======================================================
$query_produk = "SELECT * FROM produk ORDER BY id_produk DESC";
$result_produk = mysqli_query($koneksi, $query_produk);

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Produk Admin</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #3f51b5; color: white; }
        .action-link { margin-right: 10px; text-decoration: none; }
    </style>
</head>
<body>
    <h1>📦 Manajemen Produk TIJARA</h1>
    <p style="margin-top: 20px;">
        <a href="index.php">← Kembali ke Dashboard Pesanan</a> | 
        <a href="tambah_produk.php" style="font-weight: bold; color: green;">➕ Tambah Produk Baru</a>
    </p>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama Produk</th>
                <th>Harga</th>
                <th>Stok</th>
                <th>Deskripsi Singkat</th>
                <th>Gambar (URL)</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (mysqli_num_rows($result_produk) > 0):
                while($data = mysqli_fetch_assoc($result_produk)):
            ?>
                <tr>
                    <td><?php echo $data['id_produk']; ?></td>
                    <td><?php echo htmlspecialchars($data['nama_produk']); ?></td>
                    <td>Rp <?php echo number_format($data['harga'], 0, ',', '.'); ?></td>
                    <td><strong style="color: <?php echo ($data['stok'] < 5) ? 'red' : 'green'; ?>;"><?php echo $data['stok']; ?></strong></td>
                    <td><?php echo substr(htmlspecialchars($data['deskripsi']), 0, 50) . '...'; ?></td>
                    <td><?php echo htmlspecialchars($data['gambar']); ?></td>
                    <td>
                        <a href="edit_produk.php?id=<?php echo $data['id_produk']; ?>" class="action-link">✏️ Edit</a> | 
                        
                        <a href="daftar_produk_admin.php?action=hapus&id=<?php echo $data['id_produk']; ?>" 
                           onclick="return confirm('Yakin ingin menghapus produk <?php echo $data['nama_produk']; ?>?');" 
                           class="action-link" style="color: red;">🗑️ Hapus</a>
                    </td>
                </tr>
            <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" style="text-align: center;">Belum ada produk yang ditambahkan.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <?php mysqli_close($koneksi); ?>
</body>
</html>