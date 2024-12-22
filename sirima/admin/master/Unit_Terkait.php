<?php
session_start();
require_once '../../config/connection.php';

// Cek apakah user sudah login dan role-nya admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: /auth/login.php");
    exit();
}

// Proses tambah data
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add'])) {
    $nama_unit = trim($_POST['nama_unit']);
    
    if (!empty($nama_unit)) {
        try {
            $stmt = $conn->prepare("INSERT INTO unit_terkait (nama_unit) VALUES (?)");
            $stmt->execute([$nama_unit]);
            $_SESSION['success'] = "Data berhasil ditambahkan";
        } catch(PDOException $e) {
            $_SESSION['error'] = "Error: " . $e->getMessage();
        }
    }
    header("Location: unit_terkait.php");
    exit();
}

// Proses hapus data
if (isset($_GET['delete'])) {
    try {
        $stmt = $conn->prepare("DELETE FROM unit_terkait WHERE id = ?");
        $stmt->execute([$_GET['delete']]);
        $_SESSION['success'] = "Data berhasil dihapus";
    } catch(PDOException $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
    }
    header("Location: unit_terkait.php");
    exit();
}

// Proses edit data
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit'])) {
    $id = $_POST['id'];
    $nama_unit = trim($_POST['nama_unit']);
    
    if (!empty($nama_unit)) {
        try {
            $stmt = $conn->prepare("UPDATE unit_terkait SET nama_unit = ? WHERE id = ?");
            $stmt->execute([$nama_unit, $id]);
            $_SESSION['success'] = "Data berhasil diupdate";
        } catch(PDOException $e) {
            $_SESSION['error'] = "Error: " . $e->getMessage();
        }
    }
    header("Location: unit_terkait.php");
    exit();
}

// Ambil semua data
$stmt = $conn->query("SELECT * FROM unit_terkait ORDER BY id DESC");
$data = $stmt->fetchAll();

include '../../includes/header.php';
?>

<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Manajemen Unit Terkait</h1>
        <a href="/admin/dashboard.php" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
            Kembali ke Dashboard
        </a>
    </div>

    <!-- Form Tambah Data -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4">Tambah Unit Terkait</h2>
        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-gray-700 mb-2">Nama Unit</label>
                <input type="text" name="nama_unit" required 
                       class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
            </div>
            <button type="submit" name="add" 
                    class="bg-blue-500 text-white px-6 py-2 rounded hover:bg-blue-600">
                Tambah
            </button>
        </form>
    </div>

    <!-- Tabel Data -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-semibold mb-4">Daftar Unit Terkait</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full table-auto">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Unit</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($data as $index => $row): ?>
                        <tr>
                            <td class="px-4 py-4"><?php echo $index + 1; ?></td>
                            <td class="px-4 py-4"><?php echo htmlspecialchars($row['nama_unit']); ?></td>
                            <td class="px-4 py-4">
                                <button onclick="editData(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars($row['nama_unit']); ?>')"
                                        class="text-blue-500 hover:text-blue-700 mr-3">
                                    Edit
                                </button>
                                <a href="?delete=<?php echo $row['id']; ?>" 
                                   onclick="return confirm('Yakin ingin menghapus data ini?')"
                                   class="text-red-500 hover:text-red-700">
                                    Hapus
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Edit -->
<div id="editModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Edit Unit Terkait</h3>
            <form method="POST" class="mt-4">
                <input type="hidden" name="id" id="edit_id">
                <div class="mt-2">
                    <input type="text" name="nama_unit" id="edit_nama_unit" required
                           class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                </div>
                <div class="mt-4">
                    <button type="submit" name="edit"
                            class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                        Simpan
                    </button>
                    <button type="button" onclick="closeModal()"
                            class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 ml-2">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editData(id, nama) {
    document.getElementById('editModal').classList.remove('hidden');
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_nama_unit').value = nama;
}

function closeModal() {
    document.getElementById('editModal').classList.add('hidden');
}
</script>

<?php include '../../includes/footer.php'; ?>
