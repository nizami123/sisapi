<div class="card"><div class="card-body">
<p>Status verifikasi: <?= badge_verif($pt->status_verifikasi) ?> <?php if($pt->catatan_verifikasi): ?><span class="text-muted">&mdash; <?= e($pt->catatan_verifikasi) ?></span><?php endif; ?></p>
<?= form_open_multipart('peternak/profil/simpan') ?>
<div class="row g-3">
 <div class="col-md-6"><label class="form-label">Nama peternak *</label><input name="nama" class="form-control" value="<?= e($pt->nama) ?>" required></div>
 <div class="col-md-6"><label class="form-label">Nama kelompok ternak</label><input name="nama_kelompok" class="form-control" value="<?= e($pt->nama_kelompok) ?>"></div>
 <div class="col-md-6"><label class="form-label">Nomor WhatsApp * (08xx / 628xx)</label><input name="whatsapp" class="form-control" value="<?= e($pt->whatsapp) ?>" required></div>
 <div class="col-md-6"><label class="form-label">Jenis usaha peternakan</label><input name="jenis_usaha" class="form-control" value="<?= e($pt->jenis_usaha) ?>"></div>
 <div class="col-md-6"><label class="form-label">Kecamatan *</label><select name="kecamatan_id" class="form-select" data-desa-target="#desa" required><option value="">- Pilih -</option><?php foreach($kec_list as $k): ?><option value="<?= $k->id ?>" <?= $pt->kecamatan_id==$k->id?'selected':'' ?>><?= e($k->nama) ?></option><?php endforeach; ?></select></div>
 <div class="col-md-6"><label class="form-label">Desa *</label><select name="desa_id" id="desa" class="form-select" required><option value="">- Pilih -</option><?php foreach($desa_list as $d): ?><option value="<?= $d->id ?>" <?= $pt->desa_id==$d->id?'selected':'' ?>><?= e($d->nama) ?></option><?php endforeach; ?></select></div>
 <div class="col-12"><label class="form-label">Alamat usaha *</label><textarea name="alamat" class="form-control" rows="2" required><?= e($pt->alamat) ?></textarea></div>
 <div class="col-12"><label class="form-label">Deskripsi singkat</label><textarea name="deskripsi" class="form-control" rows="3"><?= e($pt->deskripsi) ?></textarea></div>
 <div class="col-md-6"><label class="form-label">Foto profil / logo (JPG/PNG/WEBP, maks 2 MB)</label><input type="file" name="foto" accept="image/*" class="form-control"><?php if($pt->foto): ?><img src="<?= foto_url($pt->foto,'peternak') ?>" height="60" class="mt-2 rounded"><?php endif; ?></div>
 <div class="col-md-6"><label class="form-label">Dokumen pendukung (gambar/PDF, maks 2 MB)</label><input type="file" name="dokumen" class="form-control"><?php if($pt->dokumen): ?><small class="text-muted">Sudah ada dokumen terunggah.</small><?php endif; ?></div>
</div>
<div class="mt-3 d-flex gap-2"><button class="btn btn-sisapi">Simpan Profil</button>
<?php if(in_array($pt->status_verifikasi,array('belum','ditolak'))): ?><button name="ajukan" value="1" class="btn btn-warning">Simpan &amp; Ajukan Verifikasi</button><?php endif; ?></div>
<?= form_close() ?></div></div>
