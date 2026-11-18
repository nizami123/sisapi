<div class="alert alert-success">Peternak yang diinput admin Dinas <b>langsung berstatus Terverifikasi</b> dan akunnya aktif (tanpa antrean verifikasi). Peternak yang mendaftar sendiri tetap berstatus Menunggu sampai Anda menyetujuinya.</div>
<?php if(!empty($error)): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
<div class="card"><div class="card-body">
<?= form_open_multipart('admin/peternak/tambah') ?>
<div class="row g-3">
 <div class="col-md-6"><label class="form-label">Nama peternak *</label><input name="nama" class="form-control" value="<?= set_value('nama') ?>" required></div>
 <div class="col-md-6"><label class="form-label">Nama kelompok ternak</label><input name="nama_kelompok" class="form-control" value="<?= set_value('nama_kelompok') ?>"></div>
 <div class="col-md-6"><label class="form-label">Email (untuk login) *</label><input type="email" name="email" class="form-control" value="<?= set_value('email') ?>" required></div>
 <div class="col-md-6"><label class="form-label">Password awal * (min. 8 karakter)</label><input name="password" class="form-control" minlength="8" required autocomplete="new-password"></div>
 <div class="col-md-6"><label class="form-label">Nomor WhatsApp * (08xx / 628xx)</label><input name="whatsapp" class="form-control" value="<?= set_value('whatsapp') ?>" required></div>
 <div class="col-md-6"><label class="form-label">Jenis usaha peternakan</label><input name="jenis_usaha" class="form-control" placeholder="mis. Sapi potong, ayam petelur" value="<?= set_value('jenis_usaha') ?>"></div>
 <div class="col-md-6"><label class="form-label">Kecamatan *</label><select name="kecamatan_id" class="form-select" data-desa-target="#desa" required><option value="">- Pilih -</option><?php foreach($kec_list as $k): ?><option value="<?= $k->id ?>" <?= set_select('kecamatan_id',$k->id) ?>><?= e($k->nama) ?></option><?php endforeach; ?></select></div>
 <div class="col-md-6"><label class="form-label">Desa *</label><select name="desa_id" id="desa" class="form-select" required><option value="">- Pilih -</option><?php foreach($desa_list as $d): ?><option value="<?= $d->id ?>" <?= set_select('desa_id',$d->id) ?>><?= e($d->nama) ?></option><?php endforeach; ?></select></div>
 <div class="col-12"><label class="form-label">Alamat usaha *</label><textarea name="alamat" class="form-control" rows="2" required><?= set_value('alamat') ?></textarea></div>
 <div class="col-12"><label class="form-label">Deskripsi singkat</label><textarea name="deskripsi" class="form-control" rows="2"><?= set_value('deskripsi') ?></textarea></div>
 <div class="col-md-6"><label class="form-label">Foto profil / logo (opsional)</label><input type="file" name="foto" accept="image/*" class="form-control"><div class="form-text">JPG/PNG/WEBP, maks 2 MB.</div></div>
 <div class="col-md-6"><label class="form-label">Dokumen pendukung (opsional)</label><input type="file" name="dokumen" accept="image/*,.pdf" class="form-control"><div class="form-text">JPG/PNG/WEBP/PDF, maks 2 MB.</div></div>
</div>
<div class="mt-3"><button class="btn btn-sisapi">Simpan &amp; Verifikasi</button> <a href="<?= site_url('admin/peternak') ?>" class="btn btn-link">Batal</a></div>
<?= form_close() ?></div></div>
