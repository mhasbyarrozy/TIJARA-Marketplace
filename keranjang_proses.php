<?php
session_start();

// Cek apakah produk ID dikirim melalui GET/POST
if (isset($_REQUEST['id'])) {
    $id_produk = $_REQUEST['id'];
    
    // Inisialisasi keranjang jika belum ada
    if (!isset($_SESSION['keranjang'])) {
        $_SESSION['keranjang'] = array();
    }

    // Cek apakah produk sudah ada di keranjang
    if (array_key_exists($id_produk, $_SESSION['keranjang'])) {
        // Jika sudah ada, tambahkan kuantitasnya
        $_SESSION['keranjang'][$id_produk] += 1;
        $pesan = "Kuantitas produk ditambahkan di keranjang!";
    } else {
        // Jika belum ada, tambahkan produk baru dengan kuantitas 1
        $_SESSION['keranjang'][$id_produk] = 1;
        $pesan = "Produk berhasil ditambahkan ke keranjang!";
    }

    // Arahkan kembali ke halaman detail produk
    echo "<script>alert('$pesan'); window.location.href='detail_produk.php?id=$id_produk';</script>";
    
} else {
    // Jika tidak ada ID produk, arahkan ke katalog
    header("location: daftar_produk.php");
}
?>