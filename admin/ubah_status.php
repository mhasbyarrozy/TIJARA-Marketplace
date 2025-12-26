<?php
session_start();
include '../koneksi.php'; // Sesuaikan path koneksi ke folder TIJARA

// 1. Cek Admin Login
if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== TRUE) {
    header("location: login_admin.php");
    exit;
}

// 2. Cek ID Transaksi dari URL
if (!isset($_GET['id'])) {
    header("location: index.php"); // Kembali ke dashboard jika ID tidak ada
    exit;
}

$id_transaksi = $_GET['id'];
$status_options = ['Menunggu Pembayaran', 'Diproses', 'Dikirim', 'Selesai', 'Dibatalkan'];

// =======================================================
// 3. LOGIKA UPDATE STATUS (Jika Form disubmit)
// =======================================================
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['status_baru'])) {
    $status_baru = $_POST['status_baru'];

    // Pastikan status yang dikirim valid
    if (in_array($status_baru, $status_options)) {
        
        $query_update = "UPDATE transaksi SET status = ? WHERE id_transaksi = ?";
        $stmt_update = $koneksi->prepare($query_update);
        $stmt_update->bind_param("si", $status_baru, $id_transaksi);
        
        if ($stmt_update->execute()) {
            echo "<script>alert('Status Pesanan #{$id_transaksi} berhasil diubah menjadi {$status_baru}!'); window.location.href='index.php';</script>";
        } else {
            echo "<script>alert('Gagal mengubah status: " . $stmt_update->error . "');</script>";
        }
        $stmt_update->close();
    } else {
        echo "<script>alert('Status yang dipilih tidak valid.');</script>";
    }
}

// =======================================================
// 4. AMBIL DETAIL TRANSAKSI
// =======================================================

// Ambil data transaksi utama & nama user
$query_transaksi = "SELECT t.*, u.username 
                    FROM transaksi t 
                    JOIN user u ON t.id_user = u.id 
                    WHERE t.id_transaksi = ?";
$stmt_transaksi = $koneksi->prepare($query_transaksi);
$stmt_transaksi->bind_param("i", $id_transaksi);
$stmt_transaksi->execute();
$result_transaksi = $stmt_transaksi->get_result();

if ($result_transaksi->num_rows === 0) {
    echo "<script>alert('Transaksi tidak ditemukan.'); window.location.href='index.php';</script>";
    exit;
}
$transaksi = $result_transaksi->fetch_assoc();
$stmt_transaksi->close();

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Ubah Status Pesanan #<?php echo $id_transaksi; ?></title>
    <style>
        body { font-family: Arial, sans-serif; width: 80%; margin: 20px auto; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #f2f2f2; }
        .status-form { margin-top: 30px; padding: 20px; border: 1px solid #ccc; background-color: #f9f9f9; }
    </style>
</head>
<body>
    <h1>✏️ Ubah Status Pesanan #<?php echo $id_transaksi; ?></h1>
    
    <p>
        **User:** <?php echo htmlspecialchars($transaksi['username']); ?><br>
        **Tanggal Pesan:** <?php echo date('d M Y H:i', strtotime($transaksi['tanggal_pesan'])); ?><br>
        **Total Pembayaran:** Rp <?php echo number_format($transaksi['total_bayar'], 0, ',', '.'); ?><br>
        **Status Saat Ini:** <strong style="color: blue;"><?php echo htmlspecialchars($transaksi['status']); ?></strong>
    </p>
    
    <div class="status-form">
        <h2>Ubah Status</h2>
        <form method="POST" action="ubah_status.php?id=<?php echo $id_transaksi; ?>">
            <label for="status_baru">Pilih Status Baru:</label>
            <select name="status_baru" id="status_baru" required>
                <?php foreach ($status_options as $status) : ?>
                    <option value="<?php echo $status; ?>" 
                        <?php echo ($status == $transaksi['status']) ? 'disabled' : ''; ?>
                        >
                        <?php echo $status; ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit" style="padding: 5px 15px; margin-left: 10px;">Simpan Perubahan</button>
        </form>
    </div>

    <h2 style="margin-top: 40px;">Item dalam Pesanan</h2>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Produk</th>
                <th>Kuantitas</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            // Ambil detail produk dari tabel detail_transaksi
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
            ?>
                <tr>
                    <td><?php echo $no++; ?></td>
                    <td><?php echo htmlspecialchars($detail['nama_produk']); ?></td>
                    <td><?php echo $detail['kuantitas']; ?></td>
                    <td>Rp <?php echo number_format($detail['subtotal'], 0, ',', '.'); ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    
    <p style="margin-top: 20px;"><a href="index.php">← Kembali ke Daftar Pesanan</a></p>

    <?php $stmt_detail->close(); mysqli_close($koneksi); ?>
</body>
</html>