<?php
session_start();
require_once '../config/connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = 'mahasiswa'; // Default role untuk user baru
    
    // Validasi input
    $errors = [];
    
    // Cek username minimal 3 karakter
    if (strlen($username) < 3) {
        $errors[] = "Username minimal 3 karakter";
    }
    
    // Cek password minimal 6 karakter
    if (strlen($password) < 6) {
        $errors[] = "Password minimal 6 karakter";
    }
    
    // Cek konfirmasi password
    if ($password !== $confirm_password) {
        $errors[] = "Konfirmasi password tidak cocok";
    }
    
    // Cek username sudah digunakan atau belum
    $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->fetchColumn() > 0) {
        $errors[] = "Username sudah digunakan";
    }
    
    // Jika tidak ada error, proses pendaftaran
    if (empty($errors)) {
        try {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            $stmt = $conn->prepare("INSERT INTO users (username, password, role, profile_completed) VALUES (?, ?, ?, FALSE)");
            $stmt->execute([$username, $hashed_password, $role]);
            
            $success = "Pendaftaran berhasil! Silakan login.";
            
            // Redirect ke halaman login setelah 2 detik
            header("refresh:2;url=/auth/login.php");
            
        } catch(PDOException $e) {
            $errors[] = "Terjadi kesalahan saat mendaftar. Silakan coba lagi.";
        }
    }
}

include '../includes/header.php';
?>

<div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="max-w-md w-full bg-white shadow-lg rounded-lg p-10"> <!-- Maksimalkan lebar dan ubah padding -->
        <h2 class="text-3xl font-bold mb-8 text-center text-gray-800">Daftar Akun</h2>
        
        <?php if (!empty($errors)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">
                <ul class="list-disc list-inside">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <?php if (isset($success)): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" class="space-y-6"> <!-- Ubah space-y-4 menjadi space-y-6 untuk memberi jarak lebih -->
            <div>
                <label class="block text-gray-700 font-medium">Username</label>
                <input type="text" name="username" required 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" 
                       value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                <p class="text-sm text-gray-500 mt-1">Minimal 3 karakter</p>
            </div>
            
            <div>
                <label class="block text-gray-700 font-medium">Password</label>
                <input type="password" name="password" required 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                <p class="text-sm text-gray-500 mt-1">Minimal 6 karakter</p>
            </div>
            
            <div>
                <label class="block text-gray-700 font-medium">Konfirmasi Password</label>
                <input type="password" name="confirm_password" required 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
            </div>
            
            <button type="submit" 
                    class="w-full bg-gradient-to-r from-blue-500 to-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition duration-200"> <!-- Gradient button -->
                Daftar
            </button>
            
            <div class="text-center text-sm text-gray-600">
                Sudah punya akun? 
                <a href="/auth/login.php" class="text-blue-500 hover:text-blue-600">Login di sini</a>
            </div>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
