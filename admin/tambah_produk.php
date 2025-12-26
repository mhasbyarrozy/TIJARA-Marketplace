<?php
session_start();
include '../koneksi.php';

// Cek Admin Login
if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== TRUE) {
    header("location: login_admin.php");
    exit;
}

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_produk = mysqli_real_escape_string($koneksi, $_POST['nama_produk']);
    $harga = mysqli_real_escape_string($koneksi, $_POST['harga']);
    $stok = mysqli_real_escape_string($koneksi, $_POST['stok']);
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);

    // --- BAGIAN PENTING: LOGIKA UPLOAD FOTO ---
    // 1. Ambil informasi file yang diupload
    $nama_file_asli = $_FILES['gambar']['name'];
    $tmp_file = $_FILES['gambar']['tmp_name'];
    $ukuran_file = $_FILES['gambar']['size'];
    
    // 2. Tentukan folder tujuan (pastikan folder 'img' ada di luar folder admin)
    // Kita gunakan nama file asli. Anda bisa menambahkan angka acak agar nama file tidak bentrok.
    $target_dir = "../img/";
    $target_file = $target_dir . basename($nama_file_asli);
    $uploadOk = 1;

    // 3. Cek apakah file benar-benar ada dan tidak kosong
    if ($nama_file_asli == "" || $ukuran_file == 0) {
        $message = "<div style='color: red;'>❌ Harap pilih file gambar!</div>";
        $uploadOk = 0;
    }

    // 4. Jika semua cek aman, coba pindahkan file
    if ($uploadOk == 1) {
        // Fungsi inilah yang memindahkan file dari folder sementara ke folder 'img'
        if (move_uploaded_file($tmp_file, $target_file)) {
            // 5. Jika file berhasil dipindah, BARU simpan datanya ke database
            // Ingat, yang disimpan ke database HANYA nama filenya saja (contoh: celana.jpg)
            $query = "INSERT INTO produk (nama_produk, harga, stok, deskripsi, gambar) 
                      VALUES (?, ?, ?, ?, ?)";
            
            $stmt = $koneksi->prepare($query);
            $stmt->bind_param("siiss", $nama_produk, $harga, $stok, $deskripsi, $nama_file_asli);

            if ($stmt->execute()) {
                $message = "<div style='color: green;'>✅ Produk <b>{$nama_produk}</b> berhasil ditambah dan foto terupload!</div>";
            } else {
                $message = "<div style='color: red;'>❌ Gagal menyimpan data ke database: " . $stmt->error . "</div>";
            }
            $stmt->close();
        } else {
            $message = "<div style='color: red;'>❌ Gagal mengupload gambar. Pastikan folder <b>../img/</b> ada dan bisa ditulis.</div>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Produk Baru</title>
    <style>
        body { font-family: Arial, sans-serif; width: 60%; margin: 20px auto; background-color: #f4f4f4; }
        .form-container { background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        form div { margin-bottom: 15px; }
        label { display: block; font-weight: bold; margin-bottom: 5px; }
        input[type="text"], input[type="number"], textarea, input[type="file"] { 
            width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; 
        }
        button { background-color: #4CAF50; color: white; padding: 12px 20px; border: none; border-radius: 4px; cursor: pointer; width: 100%; font-size: 16px; }
    </style>
</head>
<body>
    <div class="form-container">
        <h1>➕ Tambah Produk Baru</h1>
        <p><a href="daftar_produk_admin.php">← Kembali ke Daftar Produk</a></p>

        <?php echo $message; ?>

        <form method="POST" action="tambah_produk.php" enctype="multipart/form-data">
            <div>
                <label for="nama_produk">Nama Produk:</label>
                <input type="text" id="nama_produk" name="nama_produk" required>
            </div>
            <div>
                <label for="harga">Harga (Rp):</label>
                <input type="number" id="harga" name="harga" required min="1000">
            </div>
            <div>
                <label for="stok">Stok Awal:</label>
                <input type="number" id="stok" name="stok" required min="0">
            </div>
            <div>
                <label for="deskripsi">Deskripsi:</label>
                <textarea id="deskripsi" name="deskripsi" rows="4" required></textarea>
            </div>
            <div>
                <label for="gambar">Pilih Foto Produk:</label>
                <input type="file" id="gambar" name="gambar" accept="image/png, image/jpeg, image/jpg" required>
                <small>*Format: JPG, PNG, atau JPEG.</small>
            </div>
            <button type="submit">Simpan & Upload Produk</button>
        </form>
    </div>
</body>
</html>