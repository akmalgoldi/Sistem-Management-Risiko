# 💼 Sistem Manajemen Risiko

Sistem Manajemen Risiko adalah aplikasi web berbasis PHP dan MySQL yang dirancang untuk membantu universitas dalam mengelola risiko yang mungkin terjadi dalam berbagai proses bisnis. Aplikasi ini memungkinkan pengguna untuk menambahkan, mengedit, dan menghapus data risiko, serta mengelola data master terkait.

## 🚀 Fitur Utama

- 🔐 **Autentikasi Pengguna** – Login, logout, dan registrasi.
- 👥 **Manajemen Pengguna** – Admin dapat mengelola user.
- ⚠️ **Manajemen Risiko** – CRUD data risiko.
- 📂 **Manajemen Data Master** – Kategori proses, kelompok risiko, kode risiko, dll.
- 📊 **Dashboard** – Statistik risiko untuk pengguna/admin.

---

## 🛠️ Teknologi yang Digunakan

![PHP](https://img.shields.io/badge/PHP-777BB4?style=flat&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-005C84?style=flat&logo=mysql&logoColor=white)
![HTML](https://img.shields.io/badge/HTML5-E34F26?style=flat&logo=html5&logoColor=white)
![CSS](https://img.shields.io/badge/CSS3-1572B6?style=flat&logo=css3&logoColor=white)

---

## 📦 Instalasi

1. **Clone repository**:
    ```bash
    git clone https://github.com/akmalgoldi/rizzman.git
    cd rizzman
    ```

2. **Buat database**:
    - Buat database baru di MySQL dengan nama `rizzman`.
    - Import file `rizzman.sql` ke dalam database.

3. **Konfigurasi koneksi database**:
    - Buka `connection.php`.
    - Ubah parameter `host`, `dbname`, `username`, dan `password` sesuai konfigurasi lokal kamu.

4. **Jalankan aplikasi**:
    - Pastikan server Apache dan MySQL berjalan (XAMPP/WAMP/Laragon).
    - Akses via browser: `http://localhost/rizzman`

---

## 👨‍💻 Penggunaan

- 🔑 **Registrasi**: `/auth/register.php`
- 🔐 **Login**: `/auth/login.php`
- 🧭 **Dashboard**: Tergantung peran (admin/user)
- ⚙️ **Manajemen Data Master**: Hanya oleh admin
- 📌 **Manajemen Risiko**: CRUD data risiko untuk semua user

---

## 📎 Catatan Tambahan

- Disarankan menggunakan PHP ≥ 7.4 dan MySQL ≥ 5.7
- Aplikasi ini masih dalam tahap pengembangan dan dapat diperluas untuk multi-organisasi


