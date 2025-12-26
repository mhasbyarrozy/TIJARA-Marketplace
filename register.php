<?php
session_start(); // Memulai session
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TIJARA - Daftar Akun Baru</title>
    <link rel="stylesheet" href="assets/css/style.css"> 
    <style>
        /* Tambahan style agar pesan error terlihat jelas */
        .alert {
            padding: 10px;
            background-color: #f44336;
            color: white;
            margin-bottom: 15px;
            border-radius: 5px;
            text-align: center;
        }
    </style>
</head>
<body>

    <main class="container">
        <div class="login-box"> <h1>Buat Akun TIJARA Baru</h1>
            <p>Silakan isi detail akun Anda untuk mulai belanja.</p>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert">
                    <?php 
                        if($_GET['error'] == 'username_ada') echo "Username sudah digunakan!";
                        elseif($_GET['error'] == 'password_pendek') echo "Password minimal 6 karakter!";
                        else echo "Terjadi kesalahan, silakan coba lagi.";
                    ?>
                </div>
            <?php endif; ?>

            <form action="register_proses.php" method="POST" class="login-form">
                
                <div class="input-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" placeholder="Minimal 5 karakter" required minlength="5">
                </div>
                
                <div class="input-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="contoh@domain.com" required>
                </div>

                <div class="input-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Minimal 6 karakter" required minlength="6">
                </div>
                
                <button type="submit" class="btn-login">DAFTAR SEKARANG</button>
                
                <div class="footer-links" style="margin-top: 20px;">
                    Sudah punya akun? <a href="login.php">Login di sini</a> </div>
            </form>
        </div>
    </main>
</body>
</html>