<?php
session_start();
include 'includes/header.php';
?>

<div class="flex items-center justify-center min-h-screen bg-gray-100">
    <div class="text-center bg-white rounded-lg shadow-lg p-10 max-w-md w-full"> <!-- Mengurangi bayangan untuk kesan minimalis -->
        <h1 class="text-4xl font-bold text-gray-800 mb-4">Selamat Datang</h1>
        <p class="text-lg text-gray-500 mb-8">Sistem Manajemen Risiko Universitas</p>

        <?php if (!isset($_SESSION['user'])): ?>
            <div class="flex flex-col space-y-4">
                <a href="/auth/login.php" 
                   class="py-3 px-6 bg-blue-500 text-white rounded-md shadow hover:bg-blue-600 transition duration-200 text-lg">
                   Login
                </a>
                <a href="/auth/register.php" 
                   class="py-3 px-6 bg-gray-200 text-gray-800 rounded-md border border-gray-300 hover:bg-gray-300 transition duration-200 text-lg">
                   Daftar
                </a>
            </div>
        <?php else: ?>
            <div class="flex flex-col space-y-4">
                <a href="/user/dashboard.php" 
                   class="py-3 px-6 bg-green-500 text-white rounded-md shadow hover:bg-green-600 transition duration-200 text-lg">
                   Dashboard
                </a>
                <a href="/user/risk_form.php" 
                   class="py-3 px-6 bg-blue-500 text-white rounded-md shadow hover:bg-blue-600 transition duration-200 text-lg">
                   Input Risiko
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
