-- Jalankan sekali pada database sisapi yang SUDAH ada (instalasi baru cukup import sisapi.sql).
USE sisapi;
ALTER TABLE peternak
  ADD COLUMN sumber ENUM('admin','mandiri') NOT NULL DEFAULT 'admin' AFTER catatan_verifikasi;
