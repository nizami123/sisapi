<?php if($banner): ?>
<div id="bn" class="carousel slide" data-bs-ride="carousel"><div class="carousel-inner">
<?php foreach($banner as $i=>$b): ?><div class="carousel-item <?= $i?'':'active' ?>"><img src="<?= foto_url($b->gambar,'banner') ?>" class="d-block w-100" style="max-height:360px;object-fit:cover" alt="<?= e($b->judul) ?>">
<div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50 rounded"><h5><?= e($b->judul) ?></h5><p><?= e($b->deskripsi) ?></p></div></div><?php endforeach; ?>
</div></div>
<?php endif; ?>
<section class="hero"><div class="container text-center">
 <h1 class="fw-bold">Temukan Kebutuhan Peternakan Anda</h1>
 <p class="lead">Platform informasi dan pemasaran ternak, hasil peternakan, serta pakan ternak Kabupaten Lamongan.</p>
 <form action="<?= site_url('produk') ?>" method="get" class="row g-2 justify-content-center mt-3">
  <div class="col-12 col-md-5"><input name="q" class="form-control form-control-lg" placeholder="Cari produk, misal: sapi limousin"></div>
  <div class="col-8 col-md-3"><select name="kec" class="form-select form-select-lg"><option value="">Semua Kecamatan</option><?php foreach($kecamatan as $k): ?><option value="<?= $k->id ?>"><?= e($k->nama) ?></option><?php endforeach; ?></select></div>
  <div class="col-4 col-md-2"><button class="btn btn-light btn-lg w-100"><i class="fa fa-search"></i></button></div>
 </form>
</div></section>
<div class="container">
 <div class="row g-3 mt-n4 mb-4" style="margin-top:-30px">
  <?php foreach($kategori_menu as $k): $ic=array('ternak'=>'fa-cow','hasil-peternakan'=>'fa-egg','pakan-ternak'=>'fa-wheat-awn'); ?>
  <div class="col-4"><a class="kat-pill" href="<?= site_url($k->slug) ?>"><i class="fa <?= isset($ic[$k->slug])?$ic[$k->slug]:'fa-tag' ?> fa-2x d-block mb-1"></i><?= e($k->nama) ?></a></div><?php endforeach; ?>
 </div>
 <?php if($unggulan): ?><h4 class="mt-4 mb-3">Produk Unggulan</h4><div class="row g-3"><?php foreach($unggulan as $p) include(__DIR__.'/_card.php'); ?></div><?php endif; ?>
 <div class="d-flex justify-content-between align-items-center mt-4 mb-3"><h4 class="mb-0">Produk Terbaru</h4><a href="<?= site_url('produk') ?>">Lihat semua &raquo;</a></div>
 <div class="row g-3"><?php foreach($terbaru as $p) include(__DIR__.'/_card.php'); ?>
 <?php if(!$terbaru): ?><div class="col-12 text-center text-muted py-5">Belum ada produk yang dipublikasikan.</div><?php endif; ?></div>
 <h4 class="mt-5 mb-3">Peternak Terverifikasi</h4>
 <div class="row g-3"><?php foreach($peternak as $pt): ?>
  <div class="col-6 col-md-4 col-lg-2"><a href="<?= site_url('profil-peternak/'.$pt->id) ?>" class="text-decoration-none text-dark"><div class="stat-box h-100">
   <img src="<?= foto_url($pt->foto,'peternak') ?>" class="rounded-circle mb-2" width="64" height="64" style="object-fit:cover"><div class="fw-semibold small"><?= e($pt->nama) ?></div>
   <div class="small text-muted"><?= e($pt->kecamatan) ?></div><div class="small"><?= (int)$pt->jml ?> produk</div></div></a></div><?php endforeach; ?></div>
 <div class="row g-3 mt-4 text-center">
  <div class="col-4"><div class="stat-box"><b><?= $stat['peternak'] ?></b><div>Peternak Terverifikasi</div></div></div>
  <div class="col-4"><div class="stat-box"><b><?= $stat['produk'] ?></b><div>Produk Tayang</div></div></div>
  <div class="col-4"><div class="stat-box"><b><?= $stat['kecamatan'] ?></b><div>Kecamatan Terjangkau</div></div></div>
 </div>
</div>
