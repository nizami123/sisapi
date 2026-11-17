<div class="container py-4"><h4>Peternak Terverifikasi</h4>
<form class="row g-2 mb-3"><div class="col-8 col-md-4"><select name="kec" class="form-select"><option value="">Semua Kecamatan</option><?php foreach($kec_list as $k): ?><option value="<?= $k->id ?>" <?= $kec==$k->id?'selected':'' ?>><?= e($k->nama) ?></option><?php endforeach; ?></select></div><div class="col-4 col-md-2"><button class="btn btn-sisapi w-100">Filter</button></div></form>
<div class="row g-3"><?php foreach($list as $pt): ?>
 <div class="col-6 col-md-4 col-lg-3"><a href="<?= site_url('profil-peternak/'.$pt->id) ?>" class="text-decoration-none text-dark"><div class="stat-box h-100">
  <img src="<?= foto_url($pt->foto,'peternak') ?>" class="rounded-circle mb-2" width="72" height="72" style="object-fit:cover">
  <div class="fw-semibold"><?= e($pt->nama) ?></div><div class="small text-muted"><?= e($pt->kecamatan) ?><?= $pt->desa?', '.e($pt->desa):'' ?></div>
  <span class="badge badge-verif"><i class="fa fa-circle-check"></i> Terverifikasi</span></div></a></div><?php endforeach; ?>
 <?php if(!$list): ?><div class="col-12 text-center text-muted py-5">Belum ada peternak terverifikasi.</div><?php endif; ?></div></div>
