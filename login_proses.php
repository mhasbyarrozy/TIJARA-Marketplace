<?php
session_start();
include 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $identifier = $_POST['identifier']; 
    $password_input = $_POST['password']; 
    
    // Ambil data user
    $stmt = $koneksi->prepare("SELECT id, username, password, role FROM user WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $identifier, $identifier);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Perbandingan teks biasa (===) karena password di DB belum di-hash
        if ($password_input === $user['password']) {
            
            // --- BAGIAN SINKRONISASI SESSION (KUNCI UTAMA) ---
            $_SESSION['loggedin'] = TRUE; // HARUS TRUE, agar terbaca di checkout.php
            $_SESSION['id_user'] = $user['id']; 
            $_SESSION['username'] = $user['username']; 
            $_SESSION['role'] = $user['role'];
            $_SESSION['pelanggan'] = $user; // Menyimpan data user lengkap untuk cadangan
            
            if ($user['role'] === 'admin') {
                $_SESSION['admin_loggedin'] = TRUE;
                header("location: admin/index.php"); 
            } else {
                header("location: index.php"); 
            }
            exit;
            
        } else {
            // Ganti ke login.php bukan .html
            echo "<script>alert('Login GAGAL. Password salah.'); window.location.href='login.php';</script>";
        }
    } else {
        echo "<script>alert('Login GAGAL. Akun tidak ditemukan.'); window.location.href='login.php';</script>";
    }
    $stmt->close();
} else {
    header("location: login.php");
    exit;
}
mysqli_close($koneksi);
?>