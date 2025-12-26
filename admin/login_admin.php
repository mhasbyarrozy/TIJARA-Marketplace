<?php
// 1. Tambahkan ini supaya kalau ada yang salah, muncul tulisannya (tidak putih polos)
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
include '../koneksi.php';

// Jika admin sudah login, langsung lempar ke dashboard
if (isset($_SESSION['admin_loggedin']) && $_SESSION['admin_loggedin'] === TRUE) {
    header("location: index.php");
    exit;
}

$login_error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password_input = $_POST['password'];

    // Ambil data admin
    $query = "SELECT id, username, password, role FROM user WHERE username = ? AND role = 'admin'";
    $stmt = $koneksi->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        
        // PERBAIKAN UTAMA: Pakai === (teks biasa) karena di DB kamu passwordnya 'yzohr'
        if ($password_input === $user['password']) { 
            
            // Berikan semua "kunci" akses
            $_SESSION['admin_loggedin'] = TRUE;
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_username'] = $user['username'];
            
            // Kunci untuk halaman depan (beranda) supaya sinkron
            $_SESSION['id_user'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = 'admin';
            $_SESSION['status'] = "login";
            
            $stmt->close();
            header("location: index.php");
            exit;
        } else {
            $login_error = "Password salah!";
        }
    } else {
        $login_error = "Username tidak ditemukan atau Anda bukan admin.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login Admin TIJARA</title>
    <style>
        body { font-family: Arial, sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; background-color: #f4f4f4; margin: 0; }
        .login-box { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); width: 320px; }
        input { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        button { background-color: #3f51b5; color: white; padding: 10px; border: none; border-radius: 4px; cursor: pointer; width: 100%; }
        .error { color: red; font-size: 14px; text-align: center; }
    </style>
</head>
<body>
    <div class="login-box">
        <h2 style="text-align: center;">🔑 Admin Login</h2>
        <?php if ($login_error): ?>
            <p class="error"><?php echo $login_error; ?></p>
        <?php endif; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Masuk ke Dashboard</button>
        </form>
    </div>
</body>
</html>