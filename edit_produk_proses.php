<?php
session_start();
include 'koneksi.php';

// Cek Pengamanan: Pastikan hanya Admin yang bisa memproses
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header("location: login.html");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. Ambil semua data dari form (termasuk hidden field)
    $id_produk = $_POST['id_produk']; // ID Produk yang akan diupdate
    $gambar_lama = $_POST['gambar_lama']; // Nama gambar yang sudah ada di database
    
    $nama_produk = mysqli_real_escape_string($koneksi, $_POST['nama_produk']);
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];
    
    // Inisialisasi variabel gambar baru
    $nama_file_baru = $gambar_lama;
    $ganti_gambar = false;

    // --- 2. Proses Ganti Gambar (Jika ada file baru diupload) ---
    if (!empty($_FILES["gambar"]["name"])) {
        
        $target_dir = "assets/img/produk/";
        $nama_file = basename($_FILES["gambar"]["name"]);
        $target_file = $target_dir . $nama_file;
        $ganti_gambar = true;

        // Pindahkan file baru yang di-upload
        if (move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file)) {
            $nama_file_baru = $nama_file;
            
            // Hapus gambar lama jika ada, untuk menghemat ruang server
            if (!empty($gambar_lama) && file_exists($target_dir . $gambar_lama)) {
                unlink($target_dir . $gambar_lama);
            }
        } else {
            // Jika gagal upload gambar baru
            echo "<script>alert('Error: Gagal mengupload gambar baru.'); window.location.href='edit_produk.php?id=$id_produk';</script>";
            exit;
        }
    }
    
    // --- 3. Tulis Query SQL UPDATE ---
    $query = "UPDATE produk SET 
                nama_produk = '$nama_produk',
                deskripsi = '$deskripsi',
                harga = '$harga',
                stok = '$stok',
                gambar = '$nama_file_baru' 
              WHERE id_produk = '$id_produk'";
    
    // 4. Eksekusi Query
    if (mysqli_query($koneksi, $query)) {
        // Sukses
        echo "<script>alert('Produk berhasil diubah!'); window.location.href='daftar_produk_admin.php';</script>";
    } else {
        // Gagal
        echo "<script>alert('Gagal mengubah produk. Error SQL: " . mysqli_error($koneksi) . "'); window.location.href='edit_produk.php?id=$id_produk';</script>";
    }

    mysqli_close($koneksi);

} else {
    // Jika diakses tanpa form submit
    header("location: dashboard_admin.php");
}
?>