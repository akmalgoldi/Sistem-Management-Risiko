-- Membuat database
CREATE DATABASE rizzman;

-- Gunakan database
USE rizzman;

-- Membuat tabel users
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('mahasiswa', 'dosen', 'sekaprodi', 'kaprodi', 'wakildekan', 'dekan', 'wakilrektor', 'rektor', 'admin') NOT NULL,
    full_name VARCHAR(100),
    email VARCHAR(100),
    phone VARCHAR(20),
    department VARCHAR(100),
    profile_completed BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Membuat tabel kategori_proses_bisnis
CREATE TABLE kategori_proses_bisnis (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_proses VARCHAR(100) NOT NULL
);

-- Membuat tabel kelompok_resiko
CREATE TABLE kelompok_resiko (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_kelompok VARCHAR(100) NOT NULL
);

-- Membuat tabel kode_resiko
CREATE TABLE kode_resiko (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode VARCHAR(20) NOT NULL,
    deskripsi VARCHAR(255)
);

-- Membuat tabel sumber_resiko
CREATE TABLE sumber_resiko (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_sumber VARCHAR(100) NOT NULL
);

-- Membuat tabel pemilik_resiko
CREATE TABLE pemilik_resiko (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_pemilik VARCHAR(100) NOT NULL
);

-- Membuat tabel unit_terkait
CREATE TABLE unit_terkait (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_unit VARCHAR(100) NOT NULL
);

-- Membuat tabel risks (diperbarui)
CREATE TABLE risks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    tujuan TEXT NOT NULL,
    proses_bisnis_id INT NOT NULL,
    kelompok_resiko_id INT NOT NULL,
    kode_resiko_id INT NOT NULL,
    uraian_resiko TEXT NOT NULL,
    penyebab_resiko TEXT NOT NULL,
    sumber_resiko_id INT NOT NULL,
    potensi_kerugian TEXT NOT NULL,
    pemilik_resiko_id INT NOT NULL,
    unit_terkait_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (proses_bisnis_id) REFERENCES kategori_proses_bisnis(id),
    FOREIGN KEY (kelompok_resiko_id) REFERENCES kelompok_resiko(id),
    FOREIGN KEY (kode_resiko_id) REFERENCES kode_resiko(id),
    FOREIGN KEY (sumber_resiko_id) REFERENCES sumber_resiko(id),
    FOREIGN KEY (pemilik_resiko_id) REFERENCES pemilik_resiko(id),
    FOREIGN KEY (unit_terkait_id) REFERENCES unit_terkait(id)
);

-- Insert data master untuk kategori proses bisnis
INSERT INTO kategori_proses_bisnis (nama_proses) VALUES 
('Akademik'),
('Penelitian'),
('Pengabdian Masyarakat'),
('Keuangan'),
('SDM');

-- Insert data master untuk kelompok resiko
INSERT INTO kelompok_resiko (nama_kelompok) VALUES 
('Strategis'),
('Operasional'),
('Finansial'),
('Kepatuhan');

-- Insert data master untuk kode resiko
INSERT INTO kode_resiko (kode, deskripsi) VALUES 
('R001', 'Risiko Akademik'),
('R002', 'Risiko Keuangan'),
('R003', 'Risiko SDM');

-- Insert data master untuk sumber resiko
INSERT INTO sumber_resiko (nama_sumber) VALUES 
('Internal'),
('Eksternal'),
('Force Majeure');

-- Insert data master untuk pemilik resiko
INSERT INTO pemilik_resiko (nama_pemilik) VALUES 
('Fakultas'),
('Program Studi'),
('Unit'),
('Rektorat');

-- Insert data master untuk unit terkait
INSERT INTO unit_terkait (nama_unit) VALUES 
('Akademik'),
('Keuangan'),
('SDM'),
('Kemahasiswaan');

-- Insert user admin sebagai contoh
INSERT INTO users (username, password, role, profile_completed) VALUES 
('admin', 'admin123', 'admin', TRUE);
