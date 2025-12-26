<?php
session_start();
include '../koneksi.php';

// Cek Admin Login
if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== TRUE) {
    header("location: login_admin.php");
    exit;
}

// 1. Cek ID Produk dari URL
if (!isset($_GET['id'])) {
    header("location: daftar_produk_admin.php");
    exit;
}

$id_produk = $_GET['id'];
$message = '';

// =======================================================
// 2. LOGIKA UPDATE DATA (Jika Form disubmit)
// =======================================================
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form
    $nama_produk = mysqli_real_escape_string($koneksi, $_POST['nama_produk']);
    $harga = mysqli_real_escape_string($koneksi, $_POST['harga']);
    $stok = mysqli_real_escape_string($koneksi, $_POST['stok']);
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    $gambar = mysqli_real_escape_string($koneksi, $_POST['gambar']);

    // Query UPDATE data produk
    $query = "UPDATE produk SET 
                nama_produk = ?, 
                harga = ?, 
                stok = ?, 
                deskripsi = ?, 
                gambar = ? 
              WHERE id_produk = ?";
    
    $stmt = $koneksi->prepare($query);
    // Tipe data: s=string, i=integer, d=double/float (Harga biasanya disimpan sebagai integer atau float)
    $stmt->bind_param("siissi", $nama_produk, $harga, $stok, $deskripsi, $gambar, $id_produk);

    if ($stmt->execute()) {
        $message = "<div style='color: green;'>✅ Produk **{$nama_produk}** berhasil diupdate!</div>";
        // Agar data yang ditampilkan di form langsung terupdate tanpa refresh
        // Kita tidak perlu memanggil ulang $stmt_get_data di sini, cukup biarkan form menampilkan data terbaru.
    } else {
        $message = "<div style='color: red;'>❌ Gagal mengupdate produk: " . $stmt->error . "</div>";
    }
    $stmt->close();
}

// =======================================================
// 3. AMBIL DATA PRODUK SAAT INI (Untuk mengisi form)
// =======================================================

$query_get_data = "SELECT * FROM produk WHERE id_produk = ?";
$stmt_get_data = $koneksi->prepare($query_get_data);
$stmt_get_data->bind_param("i", $id_produk);
$stmt_get_data->execute();
$result_get_data = $stmt_get_data->get_result();

if ($result_get_data->num_rows === 0) {
    echo "<script>alert('Produk tidak ditemukan.'); window.location.href='daftar_produk_admin.php';</script>";
    exit;
}
$produk = $result_get_data->fetch_assoc();
$stmt_get_data->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Produk: <?php echo htmlspecialchars($produk['nama_produk']); ?></title>
    <style>
        body { font-family: Arial, sans-serif; width: 60%; margin: 20px auto; }
        form div { margin-bottom: 15px; }
        label { display: block; font-weight: bold; margin-bottom: 5px; }
        input[type="text"], input[type="number"], textarea { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        button { background-color: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
    </style>
</head>
<body>
    <h1>✏️ Edit Produk: <?php echo htmlspecialchars($produk['nama_produk']); ?></h1>
    <p><a href="daftar_produk_admin.php">← Kembali ke Daftar Produk</a></p>

    <?php echo $message; ?>

    <form method="POST" action="edit_produk.php?id=<?php echo $id_produk; ?>">
        <div>
            <label for="nama_produk">Nama Produk:</label>
            <input type="text" id="nama_produk" name="nama_produk" value="<?php echo htmlspecialchars($produk['nama_produk']); ?>" required>
        </div>
        <div>
            <label for="harga">Harga (Rp):</label>
            <input type="number" id="harga" name="harga" value="<?php echo htmlspecialchars($produk['harga']); ?>" required min="1000">
        </div>
        <div>
            <label for="stok">Stok:</label>
            <input type="number" id="stok" name="stok" value="<?php echo htmlspecialchars($produk['stok']); ?>" required min="0">
        </div>
        <div>
            <label for="deskripsi">Deskripsi:</label>
            <textarea id="deskripsi" name="deskripsi" rows="4" required><?php echo htmlspecialchars($produk['deskripsi']); ?></textarea>
        </div>
        <div>
            <label for="gambar">Path/URL Gambar:</label>
            <input type="text" id="gambar" name="gambar" value="<?php echo htmlspecialchars($produk['gambar']); ?>" required>
        </div>
        <button type="submit">Simpan Perubahan Produk</button>
    </form>

    <?php mysqli_close($koneksi); ?>
</body>
</html>