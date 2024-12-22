<?php
session_start();
require_once '../config/connection.php';

// Cek apakah user sudah login dan role-nya admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: /auth/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $role = $_POST['role'];
    
    $errors = [];
    
    // Validasi input
    if (strlen($username) < 3) {
        $errors[] = "Username minimal 3 karakter";
    }
    
    if (strlen($password) < 6) {
        $errors[] = "Password minimal 6 karakter";
    }
    
    // Cek username sudah digunakan atau belum
    $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->fetchColumn() > 0) {
        $errors[] = "Username sudah digunakan";
    }
    
    if (empty($errors)) {
        try {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            $stmt = $conn->prepare("INSERT INTO users (username, password, role, profile_completed) VALUES (?, ?, ?, FALSE)");
            $stmt->execute([$username, $hashed_password, $role]);
            
            $_SESSION['success'] = "User berhasil ditambahkan!";
            header("Location: /admin/dashboard.php");
            exit();
            
        } catch(PDOException $e) {
            $errors[] = "Terjadi kesalahan saat mendaftar: " . $e->getMessage();
        }
    }
}

include '../includes/header.php';
?>

<div class="container mx-auto px-4 py-6">
    <div class="max-w-lg mx-auto bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold">Tambah User Baru</h2>
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
                       value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                <p class="text-sm text-gray-500 mt-1">Minimal 3 karakter</p>
            </div>
            
            <div>
                <label class="block text-gray-700 mb-2">Password</label>
                <input type="password" name="password" required 
                       class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                <p class="text-sm text-gray-500 mt-1">Minimal 6 karakter</p>
            </div>
            
            <div>
                <label class="block text-gray-700 mb-2">Role</label>
                <select name="role" required 
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                    <option value="">Pilih Role</option>
                    <option value="mahasiswa">Mahasiswa</option>
                    <option value="dosen">Dosen</option>
                    <option value="sekaprodi">Sekretaris Prodi</option>
                    <option value="kaprodi">Kepala Prodi</option>
                    <option value="wakildekan">Wakil Dekan</option>
                    <option value="dekan">Dekan</option>
                    <option value="wakilrektor">Wakil Rektor</option>
                    <option value="rektor">Rektor</option>
                </select>
            </div>
            
            <button type="submit" 
                    class="w-full bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-600">
                Tambah User
            </button>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?> 