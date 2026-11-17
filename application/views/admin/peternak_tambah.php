<div class="card" style="max-width:560px"><div class="card-body"><?= form_open('admin/peternak/tambah') ?>
<div class="mb-3"><label class="form-label">Nama peternak *</label><input name="nama" class="form-control" required></div>
<div class="mb-3"><label class="form-label">Email (untuk login) *</label><input type="email" name="email" class="form-control" required></div>
<div class="mb-3"><label class="form-label">Nomor WhatsApp</label><input name="whatsapp" class="form-control"></div>
<div class="mb-3"><label class="form-label">Password awal (min. 8) *</label><input name="password" class="form-control" minlength="8" required></div>
<button class="btn btn-sisapi">Buat Akun</button><?= form_close() ?></div></div>
