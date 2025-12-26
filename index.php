<?php
// 1. Mulai Session
session_start();
include 'koneksi.php'; 

// Cek status login
$is_loggedin = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === TRUE;
$username = $is_loggedin ? htmlspecialchars($_SESSION['username']) : '';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TIJARA - Marketplace Halal Pilihan</title>
    <link rel="stylesheet" href="assets/css/style.css"> 
    <style>
        /* Pengaturan Tata Letak (Grid) agar produk berjejer ke samping */
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); /* Membuat kolom otomatis */
            gap: 20px;
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .user-nav { display: flex; align-items: center; justify-content: flex-end; gap: 15px; }
        .user-greeting { font-weight: bold; color: #007bff; }
        
        /* Pengaturan Kartu Produk agar Seragam */
        .product-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            background: #fff;
            transition: 0.3s;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        
        .product-card:hover { box-shadow: 0 4px 8px rgba(0,0,0,0.1); }

        /* KUNCI: Agar gambar tidak raksasa dan memenuhi layar */
        .product-card img {
            width: 100%;
            height: 250px; /* Tinggi gambar dikunci di sini */
            object-fit: cover; /* Memastikan foto tidak gepeng */
            display: block;
        }

        .product-info { padding: 15px; text-align: center; }
        .price { color: #d9534f; font-weight: bold; font-size: 1.1em; margin: 10px 0; }
        
        .btn-detail {
            width: 100%; 
            padding: 10px; 
            background: #007bff; 
            color: white; 
            border: none; 
            border-radius: 4px; 
            cursor: pointer;
            font-weight: bold;
        }
        .btn-detail:hover { background: #0056b3; }
    </style>
</head>
<body>

    <header class="main-header" style="background: #f8f9fa; padding: 10px 0; border-bottom: 1px solid #ddd;">
        <div class="header-container" style="display: flex; justify-content: space-between; align-items: center; max-width: 1200px; margin: 0 auto; padding: 0 20px;">
            <div class="logo" style="font-size: 24px; font-weight: bold; color: #333;">TIJARA</div>
            
            <div class="search-bar">
                <input type="text" placeholder="Cari produk Halal..." style="padding: 8px; width: 300px; border: 1px solid #ccc; border-radius: 4px;">
                <button type="submit" style="padding: 8px; cursor: pointer;">🔍</button>
            </div>
            
            <nav class="user-nav">
                <?php if ($is_loggedin): ?>
                    <span class="user-greeting">Halo, <?php echo $username; ?>!</span>
                    <a href="keranjang.php" class="nav-link">🛒 Keranjang</a>
                    <a href="logout.php" class="nav-link">Keluar</a>
                <?php else: ?>
                    <a href="login.html" class="login-button" style="text-decoration: none; background: #28a745; color: white; padding: 8px 15px; border-radius: 4px;">Masuk / Daftar</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <main class="container">
        <section class="product-showcase">
            <h3 style="text-align: center; margin: 40px 0 20px;">🛍️ Produk Terbaru & Terlaris</h3>
            <div class="product-grid">
                
                <?php
                // Mengambil produk terbaru berdasarkan id_produk
                $query = "SELECT * FROM produk ORDER BY id_produk DESC LIMIT 8";
                $result = mysqli_query($koneksi, $query);

                if ($result && mysqli_num_rows($result) > 0) {
                    while($row = mysqli_fetch_assoc($result)) {
                        
                        $nama_file = $row['gambar'];
                        
                        // Menangani spasi atau kurung pada nama file gambar
                        $link_gambar = "img/" . rawurlencode($nama_file);

                        // Cek apakah file fisik benar-benar ada di folder img/
                        if (empty($nama_file) || !file_exists("img/" . $nama_file)) {
                            $link_gambar = "assets/img/no-image.jpg"; 
                        }
                ?>
                    <div class="product-card">
    <?php 
        $nama_file = trim($row['gambar']); // Menghapus spasi gaib di depan/belakang
        $link_gambar = "img/" . $nama_file; 
        
        // Cek apakah file benar-benar ada di folder img
        if (!file_exists($link_gambar) || empty($nama_file)) {
            $link_gambar = "assets/img/no-image.jpg"; 
        }
    ?>
    <img src="img/<?php echo rawurlencode($nama_file); ?>" alt="Gambar Produk">
    
    <div class="product-info">
        <h4><?php echo htmlspecialchars($row['nama_produk']); ?></h4>
        <p class="price">Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></p>
        <a href="detail_produk.php?id=<?php echo $row['id_produk']; ?>">
            <button class="btn-detail">Lihat Detail</button>
        </a>
    </div>
</div>
                <?php 
                    }
                } else {
                    echo "<p style='grid-column: span 4; text-align:center;'>Belum ada produk atau terjadi kesalahan database.</p>";
                }
                ?>

            </div>
        </section>
    </main>

    <footer class="main-footer" style="margin-top: 50px; text-align: center; padding: 30px; background: #333; color: white;">
        <p>&copy; 2024 TIJARA | Marketplace Produk Halal & Berkah.</p>
    </footer>

</body>
</html>