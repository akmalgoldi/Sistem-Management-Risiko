<?php
class Database {
    private $host;
    private $dbname;
    private $username;
    private $password;
    private $conn;

    // Constructor untuk inisialisasi parameter
    public function __construct($host, $dbname, $username, $password) {
        $this->host = $host;
        $this->dbname = $dbname;
        $this->username = $username;
        $this->password = $password;
    }

    // Method connect() untuk menghubungkan ke database
    public function connect() {
        try {
            $this->conn = new PDO("mysql:host={$this->host};dbname={$this->dbname}", $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            echo "Koneksi berhasil!\n";
            return $this->conn;
        } catch (PDOException $e) {
            echo "Koneksi gagal: " . $e->getMessage();
            return null;
        }
    }

    // Destructor untuk menutup koneksi database
    public function __destruct() {
        // Pastikan koneksi database ditutup dengan benar
        $this->conn = null;
        echo "Koneksi database ditutup.\n";
    }
}

// Contoh penggunaan
$db = new Database('localhost', 'rizzman', 'root', ''); // Membuat objek Database
$conn = $db->connect(); // Menghubungkan ke database

// Di akhir script, otomatis __destruct() akan dipanggil untuk menutup koneksi
?>
