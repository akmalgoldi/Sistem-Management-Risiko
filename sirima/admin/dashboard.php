<?php
session_start();
require_once '../config/connection.php';

// Cek apakah user sudah login dan role-nya admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: /auth/login.php");
    exit();
}

$user = $_SESSION['user'];

// Ambil semua data user kecuali admin, urutkan berdasarkan ID
$stmt = $conn->query("SELECT * FROM users WHERE role != 'admin' ORDER BY id DESC");
$users = $stmt->fetchAll();

// Hitung total user
$total_users = count($users);

include '../includes/header.php';
?>

<div class="container mx-auto px-4">
    <!-- Header Dashboard -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Dashboard Admin</h1>
                <p class="text-gray-600">Selamat datang, <?php echo htmlspecialchars($user['username']); ?></p>
            </div>
        </div>
    </div>

    <!-- Statistik -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Total User</h3>
            <p class="text-3xl font-bold text-blue-600"><?php echo $total_users; ?></p>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Menu Cepat</h3>
            <div class="space-x-2">
                <a href="/admin/add_user.php" 
                   class="inline-block bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                    Tambah User
                </a>
            </div>
        </div>
        
        <!-- Menu Master Data -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Master Data</h3>
            <div class="space-y-2">
                <a href="/admin/master/proses_bisnis.php" 
                   class="block bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    Proses Bisnis
                </a>
                <a href="/admin/master/kelompok_resiko.php" 
                   class="block bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    Kelompok Risiko
                </a>
                <a href="/admin/master/kode_resiko.php" 
                   class="block bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    Kode Risiko
                </a>
                <a href="/admin/master/pemilik_resiko.php" 
                   class="block bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    Pemilik Risiko
                </a>
                <a href="/admin/master/sumber_resiko.php" 
                   class="block bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    Sumber Risiko
                </a>
                <a href="/admin/master/unit_terkait.php" 
                   class="block bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    Unit Terkait
                </a>
            </div>
        </div>
        
        <!-- Tambahkan tombol lihat data risiko -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Data Risiko</h3>
            <a href="/admin/risks/index.php" 
               class="block bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                Lihat Data Risiko
            </a>
        </div>
    </div>

    <!-- Daftar User -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold text-gray-800">Daftar User</h2>
            <a href="/admin/add_user.php" 
               class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                Tambah User
            </a>
        </div>

        <?php if ($total_users > 0): ?>
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Username</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status Profil</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($users as $index => $userData): ?>
                            <tr>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <?php echo $index + 1; ?>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <?php echo htmlspecialchars($userData['username']); ?>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <?php echo ucfirst(htmlspecialchars($userData['role'])); ?>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <?php if ($userData['profile_completed']): ?>
                                        <span class="text-green-600">Lengkap</span>
                                    <?php else: ?>
                                        <span class="text-red-600">Belum Lengkap</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <a href="/admin/edit_user.php?id=<?php echo $userData['id']; ?>" 
                                       class="text-blue-500 hover:text-blue-700 mr-3">
                                        Edit
                                    </a>
                                    <a href="/admin/reset_password.php?id=<?php echo $userData['id']; ?>" 
                                       class="text-yellow-500 hover:text-yellow-700 mr-3"
                                       onclick="return confirm('Reset password user ini?')">
                                        Reset Password
                                    </a>
                                    <a href="/admin/delete_user.php?id=<?php echo $userData['id']; ?>" 
                                       class="text-red-500 hover:text-red-700"
                                       onclick="return confirm('Hapus user ini?')">
                                        Hapus
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-8 text-gray-600">
                <p>Belum ada data user.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?> 