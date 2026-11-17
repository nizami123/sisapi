-- SISAPI database. Import via phpMyAdmin.
CREATE DATABASE IF NOT EXISTS sisapi CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sisapi;
SET FOREIGN_KEY_CHECKS=0;

CREATE TABLE users (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 nama VARCHAR(120) NOT NULL,
 email VARCHAR(120) NOT NULL UNIQUE,
 password VARCHAR(255) NOT NULL,
 role ENUM('admin','peternak') NOT NULL,
 is_active TINYINT(1) NOT NULL DEFAULT 1,
 last_login DATETIME NULL,
 created_at DATETIME NOT NULL, updated_at DATETIME NULL, deleted_at DATETIME NULL,
 INDEX(role)
) ENGINE=InnoDB;

CREATE TABLE kecamatan (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 nama VARCHAR(100) NOT NULL, is_active TINYINT(1) NOT NULL DEFAULT 1,
 created_at DATETIME NOT NULL, updated_at DATETIME NULL
) ENGINE=InnoDB;
CREATE TABLE desa (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 kecamatan_id INT UNSIGNED NOT NULL,
 nama VARCHAR(100) NOT NULL, is_active TINYINT(1) NOT NULL DEFAULT 1,
 created_at DATETIME NOT NULL, updated_at DATETIME NULL,
 INDEX(kecamatan_id), FOREIGN KEY (kecamatan_id) REFERENCES kecamatan(id)
) ENGINE=InnoDB;

CREATE TABLE peternak (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 user_id INT UNSIGNED NOT NULL UNIQUE,
 nama VARCHAR(150) NOT NULL,
 nama_kelompok VARCHAR(150) NULL,
 foto VARCHAR(255) NULL,
 deskripsi TEXT NULL,
 alamat TEXT NULL,
 kecamatan_id INT UNSIGNED NULL, desa_id INT UNSIGNED NULL,
 whatsapp VARCHAR(20) NULL,
 jenis_usaha VARCHAR(150) NULL,
 dokumen VARCHAR(255) NULL,
 status_verifikasi ENUM('belum','menunggu','terverifikasi','ditolak','dinonaktifkan') NOT NULL DEFAULT 'belum',
 catatan_verifikasi TEXT NULL,
 sumber ENUM('admin','mandiri') NOT NULL DEFAULT 'admin',
 created_at DATETIME NOT NULL, updated_at DATETIME NULL, deleted_at DATETIME NULL,
 INDEX(status_verifikasi), INDEX(kecamatan_id), INDEX(desa_id),
 FOREIGN KEY (user_id) REFERENCES users(id),
 FOREIGN KEY (kecamatan_id) REFERENCES kecamatan(id),
 FOREIGN KEY (desa_id) REFERENCES desa(id)
) ENGINE=InnoDB;

CREATE TABLE verifikasi_peternak (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 peternak_id INT UNSIGNED NOT NULL, admin_id INT UNSIGNED NULL,
 status_lama VARCHAR(20), status_baru VARCHAR(20), catatan TEXT NULL,
 created_at DATETIME NOT NULL,
 INDEX(peternak_id), FOREIGN KEY (peternak_id) REFERENCES peternak(id), FOREIGN KEY (admin_id) REFERENCES users(id)
) ENGINE=InnoDB;

CREATE TABLE kategori (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 nama VARCHAR(100) NOT NULL, slug VARCHAR(100) NOT NULL UNIQUE,
 is_active TINYINT(1) NOT NULL DEFAULT 1, created_at DATETIME NOT NULL, updated_at DATETIME NULL
) ENGINE=InnoDB;
CREATE TABLE subkategori (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 kategori_id INT UNSIGNED NOT NULL,
 nama VARCHAR(100) NOT NULL, is_active TINYINT(1) NOT NULL DEFAULT 1,
 created_at DATETIME NOT NULL, updated_at DATETIME NULL,
 INDEX(kategori_id), FOREIGN KEY (kategori_id) REFERENCES kategori(id)
) ENGINE=InnoDB;

CREATE TABLE produk (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 user_id INT UNSIGNED NOT NULL,
 peternak_id INT UNSIGNED NOT NULL,
 kategori_id INT UNSIGNED NOT NULL, subkategori_id INT UNSIGNED NULL,
 nama VARCHAR(200) NOT NULL,
 harga DECIMAL(15,2) NOT NULL DEFAULT 0,
 satuan VARCHAR(30) NOT NULL DEFAULT 'ekor',
 stok INT NOT NULL DEFAULT 0,
 deskripsi TEXT NULL,
 spesifikasi TEXT NULL,
 jenis_ternak VARCHAR(80) NULL, ras VARCHAR(80) NULL,
 jenis_kelamin ENUM('jantan','betina') NULL,
 umur VARCHAR(40) NULL, berat VARCHAR(40) NULL, kondisi VARCHAR(120) NULL,
 kecamatan_id INT UNSIGNED NOT NULL, desa_id INT UNSIGNED NULL,
 alamat TEXT NULL,
 status_verifikasi ENUM('draft','menunggu','disetujui','ditolak') NOT NULL DEFAULT 'draft',
 status_ketersediaan ENUM('tersedia','habis','tidak_aktif') NOT NULL DEFAULT 'tersedia',
 alasan_penolakan TEXT NULL,
 is_unggulan TINYINT(1) NOT NULL DEFAULT 0,
 total_view INT UNSIGNED NOT NULL DEFAULT 0,
 total_wa INT UNSIGNED NOT NULL DEFAULT 0,
 created_at DATETIME NOT NULL, updated_at DATETIME NULL, deleted_at DATETIME NULL,
 INDEX(user_id), INDEX(kategori_id), INDEX(subkategori_id), INDEX(kecamatan_id), INDEX(desa_id),
 INDEX(status_verifikasi), INDEX(status_ketersediaan), INDEX(harga), INDEX(nama),
 FOREIGN KEY (user_id) REFERENCES users(id),
 FOREIGN KEY (peternak_id) REFERENCES peternak(id),
 FOREIGN KEY (kategori_id) REFERENCES kategori(id),
 FOREIGN KEY (subkategori_id) REFERENCES subkategori(id),
 FOREIGN KEY (kecamatan_id) REFERENCES kecamatan(id),
 FOREIGN KEY (desa_id) REFERENCES desa(id)
) ENGINE=InnoDB;

-- Perubahan penting produk yang sudah disetujui, menunggu persetujuan ulang (JSON)
CREATE TABLE produk_revisi (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 produk_id INT UNSIGNED NOT NULL,
 data_json LONGTEXT NOT NULL,
 status ENUM('menunggu','disetujui','ditolak') NOT NULL DEFAULT 'menunggu',
 catatan TEXT NULL,
 created_at DATETIME NOT NULL, updated_at DATETIME NULL,
 INDEX(produk_id,status), FOREIGN KEY (produk_id) REFERENCES produk(id)
) ENGINE=InnoDB;

CREATE TABLE produk_foto (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 produk_id INT UNSIGNED NOT NULL,
 file VARCHAR(255) NOT NULL, urutan TINYINT NOT NULL DEFAULT 0,
 created_at DATETIME NOT NULL,
 INDEX(produk_id), FOREIGN KEY (produk_id) REFERENCES produk(id)
) ENGINE=InnoDB;

CREATE TABLE verifikasi_produk (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 produk_id INT UNSIGNED NOT NULL, peternak_id INT UNSIGNED NOT NULL, admin_id INT UNSIGNED NULL,
 status_lama VARCHAR(20), status_baru VARCHAR(20), catatan TEXT NULL,
 created_at DATETIME NOT NULL,
 INDEX(produk_id), FOREIGN KEY (produk_id) REFERENCES produk(id)
) ENGINE=InnoDB;

CREATE TABLE statistik_produk (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 produk_id INT UNSIGNED NOT NULL, user_id INT UNSIGNED NOT NULL,
 kategori_id INT UNSIGNED NOT NULL, kecamatan_id INT UNSIGNED NOT NULL,
 tipe ENUM('view','wa_click') NOT NULL,
 ip_hash CHAR(64) NOT NULL,
 created_at DATETIME NOT NULL,
 INDEX(produk_id,tipe,created_at), INDEX(user_id,tipe), INDEX(created_at)
) ENGINE=InnoDB;

CREATE TABLE banner (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 judul VARCHAR(150) NOT NULL, deskripsi VARCHAR(255) NULL,
 gambar VARCHAR(255) NOT NULL, urutan INT NOT NULL DEFAULT 0,
 is_active TINYINT(1) NOT NULL DEFAULT 1, created_at DATETIME NOT NULL, updated_at DATETIME NULL
) ENGINE=InnoDB;

CREATE TABLE pengaturan (
 kunci VARCHAR(60) PRIMARY KEY, nilai TEXT NULL
) ENGINE=InnoDB;

CREATE TABLE log_aktivitas (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 user_id INT UNSIGNED NULL, aksi VARCHAR(80) NOT NULL, keterangan TEXT NULL,
 ip VARCHAR(45) NULL, created_at DATETIME NOT NULL,
 INDEX(user_id), INDEX(created_at)
) ENGINE=InnoDB;

SET FOREIGN_KEY_CHECKS=1;

-- ================= SEED =================
-- Admin awal: admin@sisapi.local / Sisapi#2026!Ganti  (WAJIB diganti setelah login pertama)
INSERT INTO users (nama,email,password,role,created_at) VALUES
('Administrator Dinas','admin@sisapi.local','$2b$12$ZIAvPbplwSSeJ1BHX2h6F.zs/4GC3KawLNZWi4RZdNMI3HHAY6IP2','admin',NOW());

INSERT INTO kategori (nama,slug,created_at) VALUES
('Ternak','ternak',NOW()),('Hasil Peternakan','hasil-peternakan',NOW()),('Pakan Ternak','pakan-ternak',NOW());
INSERT INTO subkategori (kategori_id,nama,created_at) VALUES
(1,'Sapi',NOW()),(1,'Kambing',NOW()),(1,'Domba',NOW()),(1,'Ayam',NOW()),(1,'Bebek/Itik',NOW()),(1,'Burung',NOW()),(1,'Unggas lainnya',NOW()),(1,'Ternak lainnya',NOW()),
(2,'Telur',NOW()),(2,'Susu',NOW()),(2,'Daging',NOW()),(2,'Kulit',NOW()),(2,'Pupuk kandang',NOW()),(2,'Hasil peternakan lainnya',NOW()),
(3,'Konsentrat',NOW()),(3,'Hijauan',NOW()),(3,'Silase',NOW()),(3,'Fermentasi pakan',NOW()),(3,'Dedak',NOW()),(3,'Jagung',NOW()),(3,'Pakan ternak lainnya',NOW());

INSERT INTO kecamatan (nama,created_at) VALUES
('Babat',NOW()),('Bluluk',NOW()),('Brondong',NOW()),('Deket',NOW()),('Glagah',NOW()),('Kalitengah',NOW()),('Karanggeneng',NOW()),
('Karangbinangun',NOW()),('Kedungpring',NOW()),('Kembangbahu',NOW()),('Lamongan',NOW()),('Laren',NOW()),('Maduran',NOW()),('Mantup',NOW()),
('Modo',NOW()),('Ngimbang',NOW()),('Paciran',NOW()),('Pucuk',NOW()),('Sambeng',NOW()),('Sarirejo',NOW()),('Sekaran',NOW()),
('Solokuro',NOW()),('Sugio',NOW()),('Sukodadi',NOW()),('Sukorame',NOW()),('Tikung',NOW()),('Turi',NOW());
-- Data desa: isi melalui menu Admin > Data Desa (atau import data resmi BPS).

INSERT INTO pengaturan (kunci,nilai) VALUES
('nama_app','SISAPI'),('instansi','Dinas Peternakan dan Kesehatan Hewan Kabupaten Lamongan'),
('alamat','Kabupaten Lamongan, Jawa Timur'),('telepon',''),('email',''),
('tentang','SISAPI adalah platform informasi dan pemasaran ternak, hasil peternakan, serta pakan ternak Kabupaten Lamongan.');
