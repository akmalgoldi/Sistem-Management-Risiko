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
    // Hapus user (kecuali admin)
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND role != 'admin'");
    $stmt->execute([$user_id]);
    
    if ($stmt->rowCount() > 0) {
        $_SESSION['success'] = "User berhasil dihapus";
    } else {
        $_SESSION['error'] = "User tidak ditemukan";
    }
    
} catch(PDOException $e) {
    $_SESSION['error'] = "Terjadi kesalahan saat menghapus user: " . $e->getMessage();
}

header("Location: /admin/dashboard.php");
exit();
?> 