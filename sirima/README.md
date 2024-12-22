# Sistem Manajemen Risiko 

Sistem Manajemen Risiko  adalah aplikasi web yang dirancang untuk membantu universitas dalam mengelola risiko yang mungkin terjadi dalam berbagai proses bisnis. Aplikasi ini memungkinkan pengguna untuk menambahkan, mengedit, dan menghapus data risiko, serta mengelola data master terkait risiko.

## Fitur

- **Autentikasi Pengguna**: Login, logout, dan registrasi pengguna.
- **Manajemen Pengguna**: Tambah, edit, dan hapus pengguna (hanya untuk admin).
- **Manajemen Risiko**: Tambah, edit, dan hapus data risiko.
- **Manajemen Data Master**: Kelola data master seperti kategori proses bisnis, kelompok risiko, kode risiko, sumber risiko, pemilik risiko, dan unit terkait.
- **Dashboard**: Tampilkan statistik dan data risiko yang telah diinput oleh pengguna.


## Instalasi

1. **Clone repository**:
    ```sh
    git clone https://github.com/username/rizzman.git
    cd rizzman
    ```

2. **Buat database**:
    - Buat database baru di MySQL dengan nama `rizzman`.
    - Import file [rizzman.sql](http://_vscodecontentref_/25) ke dalam database tersebut.

3. **Konfigurasi koneksi database**:
    - Buka file [connection.php](http://_vscodecontentref_/26).
    - Sesuaikan parameter koneksi database (`host`, `dbname`, `username`, `password`) sesuai dengan konfigurasi MySQL Anda.

4. **Jalankan aplikasi**:
    - Pastikan server web (seperti Apache atau Nginx) dan server database MySQL berjalan.
    - Akses aplikasi melalui browser dengan URL yang sesuai (misalnya `http://localhost/rizzman`).

## Penggunaan

1. **Registrasi**:
    - Pengguna baru dapat mendaftar melalui halaman registrasi (`/auth/register.php`).

2. **Login**:
    - Pengguna yang sudah terdaftar dapat login melalui halaman login (`/auth/login.php`).

3. **Dashboard**:
    - Setelah login, pengguna akan diarahkan ke dashboard sesuai dengan peran mereka (admin atau user).

4. **Manajemen Risiko**:
    - Pengguna dapat menambahkan, mengedit, dan menghapus data risiko melalui form yang disediakan.

5. **Manajemen Data Master**:
    - Admin dapat mengelola data master seperti kategori proses bisnis, kelompok risiko, kode risiko, sumber risiko, pemilik risiko, dan unit terkait melalui menu yang tersedia di dashboard admin.
