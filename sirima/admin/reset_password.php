<?php
session_start();
require_once '../config/connection.php';

// Cek apakah user sudah login dan role-nya admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: /auth/login.php");
    exit();
}

// Cek apakah ada ID user
if (!isset($_GET['id'])) {
    header("Location: /admin/dashboard.php");
    exit();
}

$user_id = $_GET['id'];

try {
    // Reset password menjadi "dsadsadsa"
    // di sini kita bisa mengganti password default nya dengan password yang kita inginkan
    $default_password = "dsadsadsa";
    $hashed_password = password_hash($default_password, PASSWORD_DEFAULT);
    
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ? AND role != 'admin'");
    $stmt->execute([$hashed_password, $user_id]);
    
    if ($stmt->rowCount() > 0) {
        $_SESSION['success'] = "Password berhasil direset menjadi: " . $default_password;
    } else {
        $_SESSION['error'] = "User tidak ditemukan";
    }
    
} catch(PDOException $e) {
    $_SESSION['error'] = "Terjadi kesalahan saat mereset password: " . $e->getMessage();
}

header("Location: /admin/dashboard.php");
exit();
?> 