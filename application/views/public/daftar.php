<div class="container py-5" style="max-width:720px">
<h3>Daftar sebagai Peternak</h3>
<p class="text-muted">Isi data berikut. Setelah dikirim, pendaftaran Anda akan diperiksa dan disetujui oleh admin Dinas. Anda baru bisa masuk dan menayangkan produk setelah disetujui.</p>
<?= flash_html() ?>
<?php if(!empty($error)): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
<div class="card"><div class="card-body">
<?= form_open_multipart('daftar') ?>
<div style="position:absolute;left:-9999px" aria-hidden="true"><label>Website</label><input type="text" name="website" tabindex="-1" autocomplete="off"></div>
<div class="row g-3">
 <div class="col-md-6"><label class="form-label">Nama peternak *</label><input name="nama" class="form-control" value="<?= set_value('nama') ?>" required></div>
 <div class="col-md-6"><label class="form-label">Nama kelompok ternak</label><input name="nama_kelompok" class="form-control" value="<?= set_value('nama_kelompok') ?>"></div>
 <div class="col-md-6"><label class="form-label">Email (untuk masuk) *</label><input type="email" name="email" class="form-control" value="<?= set_value('email') ?>" required></div>
 <div class="col-md-6"><label class="form-label">Nomor WhatsApp * (08xx / 628xx)</label><input name="whatsapp" class="form-control" value="<?= set_value('whatsapp') ?>" required></div>
 <div class="col-md-6"><label class="form-label">Password * (min. 8 karakter)</label><input type="password" name="password" class="form-control" minlength="8" required autocomplete="new-password"></div>
 <div class="col-md-6"><label class="form-label">Ulangi password *</label><input type="password" name="password2" class="form-control" minlength="8" required autocomplete="new-password"></div>
 <div class="col-md-6"><label class="form-label">Kecamatan *</label><select name="kecamatan_id" class="form-select" data-desa-target="#desa" required><option value="">- Pilih -</option><?php foreach($kec_list as $k): ?><option value="<?= $k->id ?>" <?= set_select('kecamatan_id',$k->id) ?>><?= e($k->nama) ?></option><?php endforeach; ?></select></div>
 <div class="col-md-6"><label class="form-label">Desa *</label><select name="desa_id" id="desa" class="form-select" required><option value="">- Pilih -</option><?php foreach($desa_list as $d): ?><option value="<?= $d->id ?>" <?= set_select('desa_id',$d->id) ?>><?= e($d->nama) ?></option><?php endforeach; ?></select></div>
 <div class="col-12"><label class="form-label">Alamat usaha *</label><textarea name="alamat" class="form-control" rows="2" required><?= set_value('alamat') ?></textarea></div>
 <div class="col-md-6"><label class="form-label">Jenis usaha peternakan</label><input name="jenis_usaha" class="form-control" placeholder="mis. Sapi potong, ayam petelur" value="<?= set_value('jenis_usaha') ?>"></div>
 <div class="col-md-6"><label class="form-label">Dokumen pendukung (opsional)</label><input type="file" name="dokumen" class="form-control" accept="image/*,.pdf"><div class="form-text">Mis. KTP atau surat keterangan kelompok. JPG/PNG/WEBP/PDF, maks 2 MB. Membantu admin memverifikasi lebih cepat.</div></div>
</div>
<button class="btn btn-sisapi mt-4">Kirim Pendaftaran</button>
<a href="<?= site_url('login') ?>" class="btn btn-link mt-4">Sudah punya akun? Masuk</a>
<?= form_close() ?></div></div></div>
