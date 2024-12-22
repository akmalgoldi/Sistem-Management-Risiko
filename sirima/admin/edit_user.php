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

// Ambil data user yang akan diedit
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ? AND role != 'admin'");
$stmt->execute([$user_id]);
$userData = $stmt->fetch();

if (!$userData) {
    $_SESSION['error'] = "User tidak ditemukan";
    header("Location: /admin/dashboard.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $role = $_POST['role'];
    
    $errors = [];
    
    // Validasi input
    if (strlen($username) < 3) {
        $errors[] = "Username minimal 3 karakter";
    }
    
    // Cek username sudah digunakan atau belum (kecuali username saat ini)
    $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE username = ? AND id != ?");
    $stmt->execute([$username, $user_id]);
    if ($stmt->fetchColumn() > 0) {
        $errors[] = "Username sudah digunakan";
    }
    
    if (empty($errors)) {
        try {
            $stmt = $conn->prepare("UPDATE users SET username = ?, role = ? WHERE id = ?");
            $stmt->execute([$username, $role, $user_id]);
            
            $_SESSION['success'] = "Data user berhasil diperbarui!";
            header("Location: /admin/dashboard.php");
            exit();
            
        } catch(PDOException $e) {
            $errors[] = "Terjadi kesalahan saat memperbarui data: " . $e->getMessage();
        }
    }
}

include '../includes/header.php';
?>

<div class="container mx-auto px-4 py-6">
    <div class="max-w-lg mx-auto bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold">Edit User</h2>
            <a href="/admin/dashboard.php" 
               class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                Kembali
            </a>
        </div>
        
        <?php if (!empty($errors)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul class="list-disc list-inside">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-gray-700 mb-2">Username</label>
                <input type="text" name="username" required 
                       class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-500"
                       value="<?php echo htmlspecialchars($userData['username']); ?>">
                <p class="text-sm text-gray-500 mt-1">Minimal 3 karakter</p>
            </div>
            
            <div>
                <label class="block text-gray-700 mb-2">Role</label>
                <select name="role" required 
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                    <option value="">Pilih Role</option>
                    <option value="mahasiswa" <?php echo $userData['role'] == 'mahasiswa' ? 'selected' : ''; ?>>Mahasiswa</option>
                    <option value="dosen" <?php echo $userData['role'] == 'dosen' ? 'selected' : ''; ?>>Dosen</option>
                    <option value="sekaprodi" <?php echo $userData['role'] == 'sekaprodi' ? 'selected' : ''; ?>>Sekretaris Prodi</option>
                    <option value="kaprodi" <?php echo $userData['role'] == 'kaprodi' ? 'selected' : ''; ?>>Kepala Prodi</option>
                    <option value="wakildekan" <?php echo $userData['role'] == 'wakildekan' ? 'selected' : ''; ?>>Wakil Dekan</option>
                    <option value="dekan" <?php echo $userData['role'] == 'dekan' ? 'selected' : ''; ?>>Dekan</option>
                    <option value="wakilrektor" <?php echo $userData['role'] == 'wakilrektor' ? 'selected' : ''; ?>>Wakil Rektor</option>
                    <option value="rektor" <?php echo $userData['role'] == 'rektor' ? 'selected' : ''; ?>>Rektor</option>
                </select>
            </div>
            
            <button type="submit" 
                    class="w-full bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-600">
                Simpan Perubahan
            </button>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?> 