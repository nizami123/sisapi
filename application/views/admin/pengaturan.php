<div class="card" style="max-width:720px"><div class="card-body"><?= form_open('admin/pengaturan/simpan') ?>
<?php foreach(array('nama_app'=>'Nama aplikasi','instansi'=>'Nama instansi','alamat'=>'Alamat','telepon'=>'Telepon','email'=>'Email') as $k=>$l): ?><div class="mb-3"><label class="form-label"><?= $l ?></label><input name="<?= $k ?>" class="form-control" value="<?= e(isset($app[$k])?$app[$k]:'') ?>"></div><?php endforeach; ?>
<div class="mb-3"><label class="form-label">Tentang SISAPI</label><textarea name="tentang" rows="4" class="form-control"><?= e(isset($app['tentang'])?$app['tentang']:'') ?></textarea></div>
<button class="btn btn-sisapi">Simpan</button><?= form_close() ?></div></div>
