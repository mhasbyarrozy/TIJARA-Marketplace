<?php
session_start();
include 'koneksi.php';

// Cek Pengamanan: Hanya Admin yang bisa mengakses
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header("location: login.html");
    exit;
}

// 1. Ambil ID dari URL (menggunakan GET)
if (isset($_GET['id'])) {
    $id_produk = $_GET['id'];
    $target_dir = "assets/img/produk/";

    // --- 2. Ambil Nama File Gambar Lama ---
    $query_gambar = "SELECT gambar FROM produk WHERE id_produk = '$id_produk'";
    $result_gambar = mysqli_query($koneksi, $query_gambar);
    
    if ($result_gambar && mysqli_num_rows($result_gambar) > 0) {
        $data_gambar = mysqli_fetch_assoc($result_gambar);
        $gambar_lama = $data_gambar['gambar'];

        // --- 3. Hapus File Gambar dari Server ---
        if (!empty($gambar_lama) && file_exists($target_dir . $gambar_lama)) {
            unlink($target_dir . $gambar_lama);
        }
    }

    // --- 4. Tulis Query SQL DELETE ---
    $query_delete = "DELETE FROM produk WHERE id_produk = '$id_produk'";

    // --- 5. Eksekusi Query ---
    if (mysqli_query($koneksi, $query_delete)) {
        // Sukses
        echo "<script>alert('Produk berhasil dihapus!'); window.location.href='daftar_produk_admin.php';</script>";
    } else {
        // Gagal
        echo "<script>alert('Gagal menghapus produk. Error SQL: " . mysqli_error($koneksi) . "'); window.location.href='daftar_produk_admin.php';</script>";
    }

} else {
    // Jika tidak ada ID
    echo "<script>alert('ID Produk tidak ditemukan.'); window.location.href='daftar_produk_admin.php';</script>";
}

mysqli_close($koneksi);
?>