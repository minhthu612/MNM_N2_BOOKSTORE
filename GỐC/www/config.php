<?php
// Bắt đầu session TRƯỚC KHI có bất kỳ output nào
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Thiết lập thông tin kết nối
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "book";

// Tạo kết nối
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

// Sử dụng bộ mã UTF8
mysqli_set_charset($conn, 'utf8');
?>