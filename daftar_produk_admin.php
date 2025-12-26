<?php
session_start();
include 'koneksi.php';

// Cek Pengamanan: Pastikan hanya Admin yang bisa mengakses
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header("location: login.html");
    exit;
}

// Query untuk mengambil semua data produk
$query = "SELECT * FROM produk ORDER BY id_produk DESC";
$result = mysqli_query($koneksi, $query);

if (!$result) {
    die("Query GAGAL: " . mysqli_error($koneksi));
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Produk Admin</title>
    <style>
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
        th { background-color: #f2f2f2; }
        .img-thumb { max-width: 100px; height: auto; }
    </style>
</head>
<body>
    <h1>Manajemen Produk</h1>
    <p><a href="dashboard_admin.php">Kembali ke Dashboard</a> | <a href="tambah_produk.php">Tambah Produk Baru</a></p>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Gambar</th>
                <th>Nama Produk</th>
                <th>Harga</th>
                <th>Stok</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo $row['id_produk']; ?></td>
                        <td>
                            <?php if (!empty($row['gambar'])): ?>
                                <img src="assets/img/produk/<?php echo htmlspecialchars($row['gambar']); ?>" class="img-thumb" alt="Gambar Produk">
                            <?php else: ?>
                                [Tanpa Gambar]
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($row['nama_produk']); ?></td>
                        <td>Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></td>
                        <td><?php echo $row['stok']; ?></td>
                        <td>
                            <a href="edit_produk.php?id=<?php echo $row['id_produk']; ?>">Edit</a> | 
                            <a href="hapus_produk_proses.php?id=<?php echo $row['id_produk']; ?>" onclick="return confirm('Yakin ingin menghapus produk ini?');">Hapus</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">Belum ada produk yang ditambahkan.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <?php mysqli_close($koneksi); ?>
</body>
</html>