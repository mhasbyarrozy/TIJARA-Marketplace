<?php
session_start(); // Wajib untuk sistem login dan session
include 'koneksi.php';

// 1. Ambil data produk, urutkan dari yang terbaru
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog TIJARA</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background-color: #f9f9f9; margin: 0; padding: 20px; }
        h1 { text-align: center; color: #333; }
        
        /* Mengatur agar produk berjejer rapi ke samping (Grid) */
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 25px;
            max-width: 1200px;
            margin: 30px auto;
        }

        .product-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: transform 0.2s;
        }

        .product-card:hover { transform: translateY(-5px); }

        /* KUNCI: Menyeragamkan ukuran gambar agar TIDAK RAKSASA */
        .product-card img {
            width: 100%;
            height: 230px; /* Tinggi seragam */
            object-fit: cover; /* Foto dipotong rapi, tidak gepeng */
        }

        .product-info { padding: 15px; text-align: center; flex-grow: 1; }
        .product-info h3 { margin: 10px 0; font-size: 18px; color: #2c3e50; }
        .price { font-size: 20px; color: #e74c3c; font-weight: bold; margin-bottom: 10px; }
        
        .btn-detail {
            display: block;
            width: 100%;
            padding: 12px;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 0 0 12px 12px;
            font-weight: bold;
            border: none;
            cursor: pointer;
        }
        .btn-detail:hover { background-color: #2980b9; }
    </style>
</head>
<body>

    <h1>Katalog Produk Terbaru</h1>
    <p style="text-align: center;">Temukan produk terbaik dari kami di sini.</p>

    <div class="product-grid">
        <?php
        if (mysqli_num_rows($result) > 0) {
            while($row = mysqli_fetch_assoc($result)) {
                // Logika Gambar: Pastikan folder benar
                $nama_file = $row['gambar'];
                $path_fisik = "img/" . $nama_file; // Path untuk dicek sistem
                
                // Gunakan placeholder jika file tidak ada di folder img/
                if (empty($nama_file) || !file_exists($path_fisik)) {
                    $url_gambar = "https://via.placeholder.com/300x230?text=Gambar+Kosong";
                } else {
                    // Gunakan rawurlencode agar spasi pada "1 (9).png" terbaca
                    $url_gambar = "img/" . rawurlencode($nama_file);
                }
        ?>
            <div class="product-card">
                <img src="<?php echo $url_gambar; ?>" alt="<?php echo htmlspecialchars($row['nama_produk']); ?>">
                
                <div class="product-info">
                    <h3><?php echo htmlspecialchars($row['nama_produk']); ?></h3>
                    <p class="price">Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></p>
                    <p style="color: #7f8c8d; font-size: 14px;">Stok: <?php echo $row['stok']; ?></p>
                </div>

                <a href="detail_produk.php?id=<?php echo $row['id_produk']; ?>" class="btn-detail">
                    Lihat Detail
                </a>
            </div>
        <?php
            }
        } else {
            echo "<p style='text-align:center;'>Belum ada produk.</p>";
        }
        ?>
    </div>

    <?php mysqli_close($koneksi); ?>
</body>
</html>