# SISAPI - Sistem Informasi Penjualan Hasil Peternakan
Dinas Peternakan dan Kesehatan Hewan Kabupaten Lamongan. CodeIgniter 3 + MySQL + Bootstrap 5 + AdminLTE 4 + DataTables + Chart.js.

## PENTING: folder `system/` CodeIgniter TIDAK disertakan
Paket ini berisi kode aplikasi (application/, assets/, database/). Core framework CI3 harus ditambahkan:
1. Unduh CodeIgniter 3.1.13 dari https://codeigniter.com/userguide3/installation/downloads.html dan ekstrak.
2. Salin isi zip SISAPI ini ke folder hasil ekstrak, **timpa** file yang sama (application/config/*, dst).
   Hasil akhir: `system/`, `index.php` (dari CI3) + `application/`, `assets/`, `database/`, `.htaccess` (dari SISAPI).
3. Buka `index.php`; untuk produksi set `define('ENVIRONMENT','production');`.

## Instalasi
1. phpMyAdmin: import `database/sisapi.sql` (membuat database `sisapi`).
2. Edit `application/config/database.php` (username/password MySQL).
3. Edit `application/config/config.php`: ganti `encryption_key` dengan string acak. `base_url` terdeteksi otomatis; set manual bila perlu.
4. Pastikan `assets/uploads/*` dapat ditulis web server (chmod 755/775) dan mod_rewrite aktif (.htaccess).
5. PHP 7.3+ dengan ekstensi mysqli, fileinfo, mbstring.
6. Buka situs. Login: `/login`

**Akun admin awal:** `admin@sisapi.local` / `Sisapi#2026!Ganti`  -> WAJIB ganti lewat menu "Ganti Password" setelah login pertama
(dan ganti email admin lewat tambah admin baru lalu nonaktifkan akun seed).

## Alur
- Admin: buat akun peternak (Data Peternak > Tambah) -> peternak login, lengkapi profil, "Ajukan Verifikasi" -> admin menyetujui (label Terverifikasi).
- Peternak: Tambah Produk (draft atau ajukan) -> admin Verifikasi Produk -> tayang di katalog.
- Produk tayang publik hanya jika: peternak Terverifikasi + produk Disetujui + ketersediaan Tersedia/Habis.
- Perubahan penting pada produk yang sudah disetujui disimpan di `produk_revisi` dan menunggu persetujuan ulang; stok dan ketersediaan langsung berlaku.
- Tombol WhatsApp memakai `/wa/{id}`: klik dicatat lalu redirect ke wa.me (nomor dinormalisasi ke 62...).

## Asumsi teknis
- View produk dihitung 1x per IP+browser per 30 menit; klik WA 1x per 5 menit (anti-refresh).
- Data desa belum di-seed (hanya 27 kecamatan). Isi lewat Admin > Data Desa atau import data BPS.
- AdminLTE 4 (beta3), Bootstrap 5, Chart.js, DataTables, Font Awesome dimuat dari CDN. Untuk server tanpa internet, unduh dan ganti link di `views/layouts/*.php`.
- Upload gambar maks 2 MB/foto (JPG/PNG/WEBP), dokumen verifikasi juga boleh PDF. Maks 5 foto per produk.
- Hapus produk = soft delete. Kategori/wilayah hanya dinonaktifkan (tidak dihapus) agar relasi aman.

## Belum ada / catatan
- Admin belum bisa mengedit isi produk (hanya menyetujui/menolak, ubah ketersediaan, unggulkan, hapus).
- Kode belum diuji di server (dibuat tanpa PHP/CI3 di lingkungan pembuatan): lakukan uji alur per role setelah instalasi.
