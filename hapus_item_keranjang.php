<?php
session_start();

if (!isset($_SESSION['keranjang'])) {
    // Jika tidak ada keranjang, arahkan saja
    header("location: keranjang.php");
    exit;
}

if (isset($_GET['id'])) {
    $id_produk = $_GET['id'];
    
    // Cek apakah produk tersebut ada di dalam session keranjang
    if (array_key_exists($id_produk, $_SESSION['keranjang'])) {
        
        // Hapus kunci (key) ID produk dari array keranjang
        unset($_SESSION['keranjang'][$id_produk]);
        
        echo "<script>alert('Produk berhasil dihapus dari keranjang.'); window.location.href='keranjang.php';</script>";
    } else {
        echo "<script>alert('Error: Produk tidak ditemukan di keranjang.'); window.location.href='keranjang.php';</script>";
    }
} else {
    // Jika tidak ada ID, arahkan kembali ke keranjang
    header("location: keranjang.php");
}
?>