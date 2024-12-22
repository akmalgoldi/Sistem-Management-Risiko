<?php
session_start();
require_once '../../config/connection.php';

// Cek apakah user sudah login dan role-nya admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: /auth/login.php");
    exit();
}

// Ambil daftar user untuk filter
$stmt_users = $conn->query("SELECT id, username FROM users WHERE role != 'admin'");
$users = $stmt_users->fetchAll();

// Filter berdasarkan user yang dipilih
$selected_user = isset($_GET['user_id']) ? $_GET['user_id'] : null;

// Query untuk mengambil data risiko
$query = "
    SELECT r.*, 
           u.username,
           pb.nama_proses as proses_bisnis,
           kr.nama_kelompok as kelompok_resiko,
           kd.kode as kode_resiko,
           sr.nama_sumber as sumber_resiko,
           pr.nama_pemilik as pemilik_resiko,
           ut.nama_unit as unit_terkait
    FROM risks r
    LEFT JOIN users u ON r.user_id = u.id
    LEFT JOIN kategori_proses_bisnis pb ON r.proses_bisnis_id = pb.id
    LEFT JOIN kelompok_resiko kr ON r.kelompok_resiko_id = kr.id
    LEFT JOIN kode_resiko kd ON r.kode_resiko_id = kd.id
    LEFT JOIN sumber_resiko sr ON r.sumber_resiko_id = sr.id
    LEFT JOIN pemilik_resiko pr ON r.pemilik_resiko_id = pr.id
    LEFT JOIN unit_terkait ut ON r.unit_terkait_id = ut.id
";

if ($selected_user) {
    $query .= " WHERE r.user_id = :user_id";
}

$query .= " ORDER BY r.created_at DESC";

$stmt = $conn->prepare($query);
if ($selected_user) {
    $stmt->bindParam(':user_id', $selected_user);
}
$stmt->execute();
$risks = $stmt->fetchAll();

include '../../includes/header.php';
?>

<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Data Risiko</h1>
        <a href="/admin/dashboard.php" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
            Kembali ke Dashboard
        </a>
    </div>

    <!-- Filter User -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <form method="GET" class="flex items-center space-x-4">
            <div class="flex-1">
                <label class="block text-gray-700 mb-2">Filter berdasarkan User</label>
                <select name="user_id" 
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-500"
                        onchange="this.form.submit()">
                    <option value="">Semua User</option>
                    <?php foreach ($users as $user): ?>
                        <option value="<?php echo $user['id']; ?>"
                                <?php echo $selected_user == $user['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($user['username']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </form>
    </div>

    <!-- Tabel Data Risiko -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="overflow-x-auto">
            <table class="min-w-full table-auto">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tujuan</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Proses Bisnis</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode Risiko</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Uraian</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pemilik</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($risks as $index => $risk): ?>
                        <tr>
                            <td class="px-4 py-4"><?php echo $index + 1; ?></td>
                            <td class="px-4 py-4"><?php echo htmlspecialchars($risk['username']); ?></td>
                            <td class="px-4 py-4"><?php echo htmlspecialchars($risk['tujuan']); ?></td>
                            <td class="px-4 py-4"><?php echo htmlspecialchars($risk['proses_bisnis']); ?></td>
                            <td class="px-4 py-4"><?php echo htmlspecialchars($risk['kode_resiko']); ?></td>
                            <td class="px-4 py-4"><?php echo htmlspecialchars($risk['uraian_resiko']); ?></td>
                            <td class="px-4 py-4"><?php echo htmlspecialchars($risk['pemilik_resiko']); ?></td>
                            <td class="px-4 py-4"><?php echo date('d/m/Y H:i', strtotime($risk['created_at'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?> 