<?php
session_start();
require_once '../config/connection.php';

// Cek apakah user sudah login
if (!isset($_SESSION['user'])) {
    header("Location: /auth/login.php");
    exit();
}

$user = $_SESSION['user'];

// Cek apakah ada ID risiko
if (!isset($_GET['id'])) {
    header("Location: /user/dashboard.php");
    exit();
}

$risk_id = $_GET['id'];

try {
    // Cek apakah risiko milik user yang sedang login
    $stmt = $conn->prepare("SELECT id FROM risks WHERE id = ? AND user_id = ?");
    $stmt->execute([$risk_id, $user['id']]);
    $risk = $stmt->fetch();
    
    if (!$risk) {
        $_SESSION['error'] = "Data risiko tidak ditemukan atau Anda tidak memiliki akses untuk menghapusnya.";
        header("Location: /user/dashboard.php");
        exit();
    }
    
    // Hapus risiko
    $stmt = $conn->prepare("DELETE FROM risks WHERE id = ? AND user_id = ?");
    $stmt->execute([$risk_id, $user['id']]);
    
    $_SESSION['success'] = "Data risiko berhasil dihapus!";
    
} catch(PDOException $e) {
    $_SESSION['error'] = "Terjadi kesalahan saat menghapus data: " . $e->getMessage();
}

// Kembali ke halaman dashboard
header("Location: /user/dashboard.php");
exit();
?> 