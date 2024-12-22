<?php
session_start();
require_once '../config/connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = $user;
        header("Location: /user/dashboard.php");
        exit();
    } else {
        $error = "Username atau password salah!";
    }
}

include '../includes/header.php';
?>

<div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="max-w-md w-full bg-white shadow-lg rounded-lg p-10"> <!-- Maksimalkan lebar dan ubah padding -->
        <h2 class="text-3xl font-bold mb-8 text-center text-gray-800">Login</h2>
        
        <?php if (isset($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" class="space-y-6"> <!-- Ubah space-y-4 menjadi space-y-6 untuk memberi jarak lebih -->
            <div>
                <label class="block text-gray-700 font-medium">Username</label>
                <input type="text" name="username" required 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"> <!-- Ubah padding vertikal -->
            </div>
            
            <div>
                <label class="block text-gray-700 font-medium">Password</label>
                <input type="password" name="password" required 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"> <!-- Ubah padding vertikal -->
            </div>
            
            <button type="submit" 
                    class="w-full bg-gradient-to-r from-blue-500 to-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition duration-200"> <!-- Gradient button -->
                Login
            </button>
            
            <div class="text-center text-sm text-gray-600 mt-4">
                Belum punya akun? 
                <a href="/auth/register.php" class="text-blue-500 hover:text-blue-600">Daftar di sini</a>
            </div>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
