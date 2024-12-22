<?php
session_start();
require_once '../config/connection.php';

// Cek apakah user sudah login
if (!isset($_SESSION['user'])) {
    header("Location: /auth/login.php");
    exit();
}

$user = $_SESSION['user'];

// Ambil data risiko yang telah diinput oleh user dengan join ke tabel-tabel terkait
$stmt = $conn->prepare("
    SELECT r.*, 
           pb.nama_proses as proses_bisnis,
           kr.nama_kelompok as kelompok_resiko,
           kd.kode as kode_resiko,
           sr.nama_sumber as sumber_resiko,
           pr.nama_pemilik as pemilik_resiko,
           ut.nama_unit as unit_terkait
    FROM risks r
    LEFT JOIN kategori_proses_bisnis pb ON r.proses_bisnis_id = pb.id
    LEFT JOIN kelompok_resiko kr ON r.kelompok_resiko_id = kr.id
    LEFT JOIN kode_resiko kd ON r.kode_resiko_id = kd.id
    LEFT JOIN sumber_resiko sr ON r.sumber_resiko_id = sr.id
    LEFT JOIN pemilik_resiko pr ON r.pemilik_resiko_id = pr.id
    LEFT JOIN unit_terkait ut ON r.unit_terkait_id = ut.id
    WHERE r.user_id = ?
    ORDER BY r.created_at DESC
");
$stmt->execute([$user['id']]);
$risks = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Hitung total risiko
$total_risks = count($risks);

include '../includes/header.php';
?>

<!-- Tambahkan di bawah header dashboard -->
<?php if (isset($_SESSION['success'])): ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        <?php 
        echo $_SESSION['success'];
        unset($_SESSION['success']);
        ?>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        <?php 
        echo $_SESSION['error'];
        unset($_SESSION['error']);
        ?>
    </div>
<?php endif; ?>

<div class="flex flex-col min-h-screen container mx-auto px-4">
    <!-- Header Dashboard -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Dashboard</h1>
                <p class="text-gray-600">Selamat datang, <?php echo htmlspecialchars($user['username']); ?></p>
            </div>
            <div>
                <span class="bg-blue-100 text-blue-800 text-sm font-medium px-3 py-1 rounded">
                    Role: <?php echo ucfirst(htmlspecialchars($user['role'])); ?>
                </span>
            </div>
        </div>
    </div>

    <!-- Statistik -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Total Risiko</h3>
            <p class="text-3xl font-bold text-blue-600"><?php echo $total_risks; ?></p>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Status Profil</h3>
            <p class="text-sm">
                <?php if ($user['profile_completed']): ?>
                    <span class="text-green-600 font-semibold">Lengkap</span>
                <?php else: ?>
                    <span class="text-red-600 font-semibold">Belum Lengkap</span>
                    <a href="/user/profile.php" class="text-blue-500 hover:text-blue-600 ml-2">
                        Lengkapi Sekarang
                    </a>
                <?php endif; ?>
            </p>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Menu Cepat</h3>
            <div class="space-x-2">
                <a href="/user/risk_form.php" 
                   class="inline-block bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    Input Risiko
                </a>
            </div>
        </div>
    </div>

    <!-- Daftar Risiko -->
    <div class="bg-white rounded-lg shadow-md p-6 flex-grow">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold text-gray-800">Daftar Risiko</h2>
            <a href="/user/risk_form.php" 
               class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                Tambah Risiko
            </a>
        </div>

        <?php if ($total_risks > 0): ?>
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tujuan</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Proses Bisnis</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode Risiko</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Uraian Risiko</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pemilik Risiko</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Input</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($risks as $index => $risk): ?>
                            <tr>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <?php echo $index + 1; ?>
                                </td>
                                <td class="px-4 py-4">
                                    <?php echo htmlspecialchars($risk['tujuan']); ?>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <?php echo htmlspecialchars($risk['proses_bisnis']); ?>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <?php echo htmlspecialchars($risk['kode_resiko']); ?>
                                </td>
                                <td class="px-4 py-4">
                                    <?php echo htmlspecialchars($risk['uraian_resiko']); ?>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <?php echo htmlspecialchars($risk['pemilik_resiko']); ?>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <?php echo date('d/m/Y H:i', strtotime($risk['created_at'])); ?>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <a href="/user/edit_risk.php?id=<?php echo $risk['id']; ?>" 
                                       class="text-blue-500 hover:text-blue-700 mr-3">
                                        Edit
                                    </a>
                                    <a href="/user/delete_risk.php?id=<?php echo $risk['id']; ?>" 
                                       class="text-red-500 hover:text-red-700"
                                       onclick="return confirm('Apakah Anda yakin ingin menghapus risiko ini?')">
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
                <p>Belum ada data risiko. Silakan tambahkan risiko baru.</p>
            </div>
        <?php endif; ?>
    </div>

    <?php include '../includes/footer.php'; ?>
</div>
