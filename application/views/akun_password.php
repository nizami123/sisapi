<div class="card" style="max-width:480px"><div class="card-body">
<?= form_open('akun/password') ?>
<div class="mb-3"><label class="form-label">Password lama</label><input type="password" name="lama" class="form-control" required></div>
<div class="mb-3"><label class="form-label">Password baru (min. 8 karakter)</label><input type="password" name="baru" class="form-control" minlength="8" required></div>
<div class="mb-3"><label class="form-label">Konfirmasi password baru</label><input type="password" name="konfirmasi" class="form-control" required></div>
<button class="btn btn-sisapi">Simpan</button><?= form_close() ?></div></div>
