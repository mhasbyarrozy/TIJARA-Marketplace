<?php
session_start(); // Memulai session agar bisa cek status login
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TIJARA - Halaman Login</title>
    <link rel="stylesheet" href="assets/css/style.css"> 
    <style>
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
        <div class="login-box">
            <h1>Masuk ke Akun TIJARA</h1>
            <p>Silakan masukkan detail akun Anda.</p>

            <?php if (isset($_GET['pesan']) && $_GET['pesan'] == 'gagal'): ?>
                <div class="alert">
                    Username atau Password salah!
                </div>
            <?php endif; ?>

            <form action="login_proses.php" method="POST" class="login-form">
                
                <div class="input-group">
                    <label for="username">Username / Email</label>
                    <input type="text" id="username" name="identifier" placeholder="Username atau Email Anda" required>
                </div>
                
                <div class="input-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Password Akun" required>
                </div>
                
                <button type="submit" class="btn-login">LOGIN SEKARANG</button>
                
                <div class="footer-links" style="margin-top: 20px;">
                    Belum punya akun? <a href="register.php">Daftar di sini</a>
                </div>
            </form>
        </div>
    </main>
</body>
</html>