<?php
session_start();
require_once '../config/connection.php';

// Cek apakah user sudah login
if (!isset($_SESSION['user'])) {
    header("Location: /auth/login.php");
    exit();
}

$user = $_SESSION['user'];

// Cek apakah ada ID risiko
if (!isset($_GET['id'])) {
    header("Location: /user/dashboard.php");
    exit();
}

$risk_id = $_GET['id'];

// Ambil data master
$stmt_proses = $conn->query("SELECT * FROM kategori_proses_bisnis");
$proses_bisnis = $stmt_proses->fetchAll();

$stmt_kelompok = $conn->query("SELECT * FROM kelompok_resiko");
$kelompok_resiko = $stmt_kelompok->fetchAll();

$stmt_kode = $conn->query("SELECT * FROM kode_resiko");
$kode_resiko = $stmt_kode->fetchAll();

$stmt_sumber = $conn->query("SELECT * FROM sumber_resiko");
$sumber_resiko = $stmt_sumber->fetchAll();

$stmt_pemilik = $conn->query("SELECT * FROM pemilik_resiko");
$pemilik_resiko = $stmt_pemilik->fetchAll();

$stmt_unit = $conn->query("SELECT * FROM unit_terkait");
$unit_terkait = $stmt_unit->fetchAll();

// Ambil data risiko yang akan diedit
$stmt = $conn->prepare("SELECT * FROM risks WHERE id = ? AND user_id = ?");
$stmt->execute([$risk_id, $user['id']]);
$risk = $stmt->fetch();

// Jika risiko tidak ditemukan atau bukan milik user ini
if (!$risk) {
    header("Location: /user/dashboard.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tujuan = trim($_POST['tujuan']);
    $proses_bisnis_id = $_POST['proses_bisnis_id'];
    $kelompok_resiko_id = $_POST['kelompok_resiko_id'];
    $kode_resiko_id = $_POST['kode_resiko_id'];
    $uraian_resiko = trim($_POST['uraian_resiko']);
    $penyebab_resiko = trim($_POST['penyebab_resiko']);
    $sumber_resiko_id = $_POST['sumber_resiko_id'];
    $potensi_kerugian = trim($_POST['potensi_kerugian']);
    $pemilik_resiko_id = $_POST['pemilik_resiko_id'];
    $unit_terkait_id = $_POST['unit_terkait_id'];
    
    try {
        $stmt = $conn->prepare("UPDATE risks SET 
            tujuan = ?,
            proses_bisnis_id = ?,
            kelompok_resiko_id = ?,
            kode_resiko_id = ?,
            uraian_resiko = ?,
            penyebab_resiko = ?,
            sumber_resiko_id = ?,
            potensi_kerugian = ?,
            pemilik_resiko_id = ?,
            unit_terkait_id = ?
            WHERE id = ? AND user_id = ?");
        
        $stmt->execute([
            $tujuan,
            $proses_bisnis_id,
            $kelompok_resiko_id,
            $kode_resiko_id,
            $uraian_resiko,
            $penyebab_resiko,
            $sumber_resiko_id,
            $potensi_kerugian,
            $pemilik_resiko_id,
            $unit_terkait_id,
            $risk_id,
            $user['id']
        ]);
        
        $success = "Risiko berhasil diperbarui!";
        
        // Refresh data risiko
        $stmt = $conn->prepare("SELECT * FROM risks WHERE id = ?");
        $stmt->execute([$risk_id]);
        $risk = $stmt->fetch();
        
    } catch(PDOException $e) {
        $error = "Terjadi kesalahan saat memperbarui data: " . $e->getMessage();
    }
}

include '../includes/header.php';
?>

<div class="container mx-auto px-4 py-6">
    <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold">Edit Risiko</h2>
            <a href="/user/dashboard.php" 
               class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                Kembali
            </a>
        </div>
        
        <?php if (isset($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($success)): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Tujuan -->
                <div class="col-span-2">
                    <label class="block text-gray-700 mb-2">Tujuan</label>
                    <textarea name="tujuan" required 
                              class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-500"
                              rows="3"><?php echo htmlspecialchars($risk['tujuan']); ?></textarea>
                </div>
                
                <!-- Proses Bisnis -->
                <div>
                    <label class="block text-gray-700 mb-2">Proses Bisnis</label>
                    <select name="proses_bisnis_id" required 
                            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                        <option value="">Pilih Proses Bisnis</option>
                        <?php foreach ($proses_bisnis as $proses): ?>
                            <option value="<?php echo $proses['id']; ?>" 
                                    <?php echo $proses['id'] == $risk['proses_bisnis_id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($proses['nama_proses']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- Kelompok Risiko -->
                <div>
                    <label class="block text-gray-700 mb-2">Kelompok Risiko</label>
                    <select name="kelompok_resiko_id" required 
                            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                        <option value="">Pilih Kelompok Risiko</option>
                        <?php foreach ($kelompok_resiko as $kelompok): ?>
                            <option value="<?php echo $kelompok['id']; ?>"
                                    <?php echo $kelompok['id'] == $risk['kelompok_resiko_id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($kelompok['nama_kelompok']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- Kode Risiko -->
                <div>
                    <label class="block text-gray-700 mb-2">Kode Risiko</label>
                    <select name="kode_resiko_id" required 
                            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                        <option value="">Pilih Kode Risiko</option>
                        <?php foreach ($kode_resiko as $kode): ?>
                            <option value="<?php echo $kode['id']; ?>"
                                    <?php echo $kode['id'] == $risk['kode_resiko_id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($kode['kode'] . ' - ' . $kode['deskripsi']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- Uraian Risiko -->
                <div class="col-span-2">
                    <label class="block text-gray-700 mb-2">Uraian Peristiwa Risiko</label>
                    <textarea name="uraian_resiko" required 
                              class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-500"
                              rows="3"><?php echo htmlspecialchars($risk['uraian_resiko']); ?></textarea>
                </div>
                
                <!-- Penyebab Risiko -->
                <div class="col-span-2">
                    <label class="block text-gray-700 mb-2">Penyebab Risiko</label>
                    <textarea name="penyebab_resiko" required 
                              class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-500"
                              rows="3"><?php echo htmlspecialchars($risk['penyebab_resiko']); ?></textarea>
                </div>
                
                <!-- Sumber Risiko -->
                <div>
                    <label class="block text-gray-700 mb-2">Sumber Risiko</label>
                    <select name="sumber_resiko_id" required 
                            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                        <option value="">Pilih Sumber Risiko</option>
                        <?php foreach ($sumber_resiko as $sumber): ?>
                            <option value="<?php echo $sumber['id']; ?>"
                                    <?php echo $sumber['id'] == $risk['sumber_resiko_id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($sumber['nama_sumber']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- Potensi Kerugian -->
                <div class="col-span-2">
                    <label class="block text-gray-700 mb-2">Potensi Kerugian</label>
                    <textarea name="potensi_kerugian" required 
                              class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-500"
                              rows="3"><?php echo htmlspecialchars($risk['potensi_kerugian']); ?></textarea>
                </div>
                
                <!-- Pemilik Risiko -->
                <div>
                    <label class="block text-gray-700 mb-2">Pemilik Risiko</label>
                    <select name="pemilik_resiko_id" required 
                            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                        <option value="">Pilih Pemilik Risiko</option>
                        <?php foreach ($pemilik_resiko as $pemilik): ?>
                            <option value="<?php echo $pemilik['id']; ?>"
                                    <?php echo $pemilik['id'] == $risk['pemilik_resiko_id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($pemilik['nama_pemilik']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- Unit Terkait -->
                <div>
                    <label class="block text-gray-700 mb-2">Unit Terkait</label>
                    <select name="unit_terkait_id" required 
                            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                        <option value="">Pilih Unit Terkait</option>
                        <?php foreach ($unit_terkait as $unit): ?>
                            <option value="<?php echo $unit['id']; ?>"
                                    <?php echo $unit['id'] == $risk['unit_terkait_id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($unit['nama_unit']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="flex justify-end pt-4">
                <button type="submit" 
                        class="bg-blue-500 text-white px-6 py-2 rounded hover:bg-blue-600">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?> 