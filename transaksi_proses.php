<?php
session_start();
include 'koneksi.php'; // Pastikan koneksi.php sudah tersedia

// Wajib Login dan Keranjang Tidak Boleh Kosong
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== TRUE) {
    echo "<script>alert('Anda harus login untuk memproses transaksi.'); window.location.href='login.html';</script>";
    exit;
}
if (empty($_SESSION['keranjang'])) {
    echo "<script>alert('Keranjang belanja kosong. Tidak bisa memproses pesanan.'); window.location.href='keranjang.php';</script>";
    exit;
}

// Data yang dibutuhkan dari session
$id_user = $_SESSION['id_user'];
$total_bayar = $_SESSION['total_bayar'];
$keranjang = $_SESSION['keranjang'];

// --- START TRANSACTION ---
mysqli_begin_transaction($koneksi);
$success = true; // Flag untuk menandai keberhasilan transaksi

try {
    // 1. INPUT DATA KE TABEL INDUK (TRANSAKSI/PESANAN)
    $tanggal_pesan = date("Y-m-d H:i:s");
    $status = 'Menunggu Pembayaran'; // Status awal
    
    // Asumsi: Anda memiliki tabel 'transaksi' dengan kolom id_user, tanggal_pesan, total_bayar, status
    $query_transaksi = "INSERT INTO transaksi (id_user, tanggal_pesan, total_bayar, status) VALUES (?, ?, ?, ?)";
    $stmt_transaksi = $koneksi->prepare($query_transaksi);
    $stmt_transaksi->bind_param("isds", $id_user, $tanggal_pesan, $total_bayar, $status);
    
    if (!$stmt_transaksi->execute()) {
        $success = false;
        throw new Exception("Gagal menyimpan transaksi induk.");
    }
    
    // Ambil ID Transaksi yang baru saja dibuat
    $id_transaksi_baru = mysqli_insert_id($koneksi);
    $stmt_transaksi->close();
    
    // 2. INPUT DATA DETAIL ITEM KE TABEL DETAIL_TRANSAKSI
    
    // Ambil detail produk dari database (Sama seperti di keranjang.php)
    $ids = array_keys($keranjang);
    $id_string = implode(',', $ids);
    // Kita TIDAK perlu mengambil harga lagi di sini karena sudah diambil saat INSERT ke detail_transaksi
    // Query ini hanya untuk mengambil item yang akan diproses.
    $query_produk = "SELECT id_produk, harga FROM produk WHERE id_produk IN ($id_string)"; 
    $result_produk = mysqli_query($koneksi, $query_produk);
    
    if (!$result_produk) {
        throw new Exception("Gagal mengambil data produk.");
    }

    $stmt_detail = $koneksi->prepare("INSERT INTO detail_transaksi (id_transaksi, id_produk, kuantitas, subtotal) VALUES (?, ?, ?, ?)");
    
    while ($data_produk = mysqli_fetch_assoc($result_produk)) {
        $id_produk = $data_produk['id_produk'];
        $harga_satuan = $data_produk['harga'];
        $kuantitas = $keranjang[$id_produk];
        $subtotal_item = $harga_satuan * $kuantitas;

        // 2a. Bind dan Eksekusi INSERT detail_transaksi
        $stmt_detail->bind_param("iiid", $id_transaksi_baru, $id_produk, $kuantitas, $subtotal_item);
        
        if (!$stmt_detail->execute()) {
            $success = false;
            throw new Exception("Gagal menyimpan detail produk.");
        }
        
        // =======================================================
        // 2b. LOGIKA PENGURANGAN STOK DITAMBAHKAN DI SINI
        // =======================================================
        
        $query_update_stok = "UPDATE produk SET stok = stok - ? WHERE id_produk = ?";
        $stmt_stok = $koneksi->prepare($query_update_stok);
        
        if (!$stmt_stok) {
            throw new Exception("Gagal prepare query update stok: " . $koneksi->error);
        }
        
        $stmt_stok->bind_param("ii", $kuantitas, $id_produk);
        
        if (!$stmt_stok->execute()) {
            throw new Exception("Gagal mengurangi stok produk ID: " . $id_produk);
        }
        $stmt_stok->close();

        // =======================================================
        // Akhir Logika Pengurangan Stok
        // =======================================================
    }
    
    $stmt_detail->close();
    
    // 3. JIKA SEMUA BERHASIL, COMMIT TRANSACTION
    mysqli_commit($koneksi);
    
    // 4. KOSONGKAN KERANJANG
    unset($_SESSION['keranjang']);
    unset($_SESSION['total_bayar']);
    
    // Redirect ke halaman sukses
    echo "<script>alert('PESANAN BERHASIL! ID Pesanan Anda: $id_transaksi_baru. Silahkan lakukan pembayaran.'); window.location.href='index.php';</script>";

} catch (Exception $e) {
    // JIKA ADA ERROR, ROLLBACK TRANSACTION
    mysqli_rollback($koneksi);
    
    // Tampilkan error
    echo "<script>alert('PROSES PESANAN GAGAL: " . $e->getMessage() . "'); window.location.href='keranjang.php';</script>";
}

mysqli_close($koneksi);
?>