<?php
// Mulai session
session_start();

// Hapus semua variabel session
$_SESSION = array();

// Hancurkan session (secara teknis mengakhiri sesi)
session_destroy();

// Arahkan kembali ke halaman login (login.html)
header("location: login.html");
exit;
?>