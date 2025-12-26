<?php
session_start();
session_unset(); // Hapus semua variabel session
session_destroy(); // Hancurkan session
header("location: login_admin.php"); // Arahkan kembali ke halaman login
exit;
?>