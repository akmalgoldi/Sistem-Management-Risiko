<?php
session_start();
require_once '../config/connection.php';

// Cek apakah user sudah login
if (!isset($_SESSION['user'])) {
    header("Location: /auth/login.php");
    exit();
}

$user = $_SESSION['user'];

// Tambahkan kolom-kolom yang diperlukan ke tabel users jika belum ada
try {
    $conn->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS full_name VARCHAR(100)");
    $conn->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS email VARCHAR(100)");
    $conn->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS phone VARCHAR(20)");
    $conn->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS department VARCHAR(100)");
} catch(PDOException $e) {
    // Handle error jika diperlukan
}

// Ambil data profil user
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user['id']]);
$profile = $stmt->fetch(PDO::FETCH_ASSOC);

// Proses update profil
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $department = trim($_POST['department']);
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    $errors = [];
    
    // Validasi input
    if (empty($full_name)) {
        $errors[] = "Nama lengkap harus diisi";
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email tidak valid";
    }
    
    // Jika user ingin mengubah password
    if (!empty($current_password)) {
        if (!password_verify($current_password, $profile['password'])) {
            $errors[] = "Password saat ini tidak sesuai";
        }
        
        if (strlen($new_password) < 6) {
            $errors[] = "Password baru minimal 6 karakter";
        }
        
        if ($new_password !== $confirm_password) {
            $errors[] = "Konfirmasi password baru tidak cocok";
        }
    }
    
    // Jika tidak ada error, update profil
    if (empty($errors)) {
        try {
            // Update data profil
            $sql = "UPDATE users SET 
                    full_name = ?, 
                    email = ?, 
                    phone = ?, 
                    department = ?, 
                    profile_completed = TRUE";
            $params = [$full_name, $email, $phone, $department];
            
            // Jika ada perubahan password
            if (!empty($new_password)) {
                $sql .= ", password = ?";
                $params[] = password_hash($new_password, PASSWORD_DEFAULT);
            }
            
            $sql .= " WHERE id = ?";
            $params[] = $user['id'];
            
            $stmt = $conn->prepare($sql);
            $stmt->execute($params);
            
            // Update session
            $_SESSION['user']['profile_completed'] = true;
            
            $success = "Profil berhasil diperbarui!";
            
            // Refresh data profil
            $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$user['id']]);
            $profile = $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch(PDOException $e) {
            $errors[] = "Terjadi kesalahan saat memperbarui profil";
        }
    }
}

include '../includes/header.php';
?>

<div class="container mx-auto px-4 py-6">
    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-bold mb-6">Profil Pengguna</h2>
        
        <?php if (!empty($errors)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul class="list-disc list-inside">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <?php if (isset($success)): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-gray-700 mb-2">Username</label>
                <input type="text" value="<?php echo htmlspecialchars($profile['username']); ?>" 
                       class="w-full px-4 py-2 border rounded-lg bg-gray-100" disabled>
            </div>
            
            <div>
                <label class="block text-gray-700 mb-2">Nama Lengkap *</label>
                <input type="text" name="full_name" required 
                       class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-500"
                       value="<?php echo htmlspecialchars($profile['full_name'] ?? ''); ?>">
            </div>
            
            <div>
                <label class="block text-gray-700 mb-2">Email *</label>
                <input type="email" name="email" required 
                       class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-500"
                       value="<?php echo htmlspecialchars($profile['email'] ?? ''); ?>">
            </div>
            
            <div>
                <label class="block text-gray-700 mb-2">Nomor Telepon</label>
                <input type="tel" name="phone" 
                       class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-500"
                       value="<?php echo htmlspecialchars($profile['phone'] ?? ''); ?>">
            </div>
            
            <div>
                <label class="block text-gray-700 mb-2">Departemen/Jurusan</label>
                <input type="text" name="department" 
                       class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-500"
                       value="<?php echo htmlspecialchars($profile['department'] ?? ''); ?>">
            </div>
            
            <div class="border-t pt-4 mt-6">
                <h3 class="text-lg font-semibold mb-4">Ubah Password</h3>
                <p class="text-sm text-gray-600 mb-4">Kosongkan bagian ini jika tidak ingin mengubah password</p>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-gray-700 mb-2">Password Saat Ini</label>
                        <input type="password" name="current_password" 
                               class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 mb-2">Password Baru</label>
                        <input type="password" name="new_password" 
                               class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 mb-2">Konfirmasi Password Baru</label>
                        <input type="password" name="confirm_password" 
                               class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                    </div>
                </div>
            </div>
            
            <div class="flex items-center justify-between pt-4">
                <a href="/user/dashboard.php" 
                   class="bg-gray-500 text-white px-6 py-2 rounded hover:bg-gray-600">
                    Kembali
                </a>
                <button type="submit" 
                        class="bg-blue-500 text-white px-6 py-2 rounded hover:bg-blue-600">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?> 