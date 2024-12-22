<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RIZZMAN - Risiko Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
<nav class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-md">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <!-- Logo atau Judul -->
            <a href="/" class="text-2xl font-extrabold hover:text-gray-200 transition">
                Sistem Risiko Management
            </a>

            <!-- Menu Navigasi -->
            <div class="flex items-center space-x-6">
                <?php if (isset($_SESSION['user'])): ?>
                    <!-- Jika Admin -->
                    <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                        <a href="/admin/dashboard.php" 
                           class="hover:text-gray-200 transition font-medium">
                           Admin Dashboard
                        </a>
                    <?php endif; ?>

                    <!-- Username dan Logout -->
                    <span class="font-semibold">
                        Selamat datang, <?php echo htmlspecialchars($_SESSION['user']['username']); ?>
                    </span>
                    <a href="/auth/logout.php" 
                       class="bg-red-500 px-4 py-2 rounded-full hover:bg-red-600 transition font-medium">
                       Logout
                    </a>
                <?php else: ?>
                    <!-- Menu Login -->
                    <a href="/auth/login.php" 
                       class="bg-blue-500 px-4 py-2 rounded-full hover:bg-blue-600 transition font-medium">
                       Login
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </nav>


    <!-- Konten Utama -->
    <main class="container mx-auto mt-8 px-4">
