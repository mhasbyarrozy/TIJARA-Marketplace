<?php
// Panggil file koneksi yang sudah berhasil (koneksi.php)
include 'koneksi.php';

// Cek apakah permintaan datang dari form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Ambil data yang diinput pengguna
    $username = $_POST['username'];
    $email    = $_POST['email'];
    $password = $_POST['password'];
    
    // LANGKAH KEAMANAN: HASHING PASSWORD
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Atur role default
    $role = 'user';

    // Tulis Query SQL: Masukkan data ke tabel 'user'
    // PASTIKAN NAMA TABEL ADALAH 'user' (tunggal)
    $query = "INSERT INTO user (username, password, email, role) 
              VALUES ('$username', '$hashed_password', '$email', '$role')";
    
    // Eksekusi Query
    if (mysqli_query($koneksi, $query)) {
        // Sukses
        echo "<script>alert('Pendaftaran berhasil! Silakan login.'); window.location.href='index.php';</script>";
    } else {
        // Gagal (misalnya username/email sudah dipakai, karena UNIQUE)
        echo "<script>alert('Pendaftaran GAGAL. Kemungkinan Username atau Email sudah terdaftar.'); window.location.href='register.html';</script>";
    }

    // Tutup koneksi
    mysqli_close($koneksi);
} else {
    // Jika diakses langsung
    header("Location: register.html");
    exit();
}
?>