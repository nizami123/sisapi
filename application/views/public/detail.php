<div class="container py-4"><div class="row g-4">
 <div class="col-md-6">
  <img id="galMain" class="gal-main" src="<?= foto_url($fotos?$fotos[0]->file:NULL) ?>" alt="<?= e($p->nama) ?>">
  <div class="d-flex gap-2 mt-2 flex-wrap"><?php foreach($fotos as $f): ?><img class="gal-thumb" src="<?= foto_url($f->file) ?>" alt=""><?php endforeach; ?></div>
 </div>
 <div class="col-md-6">
  <div class="text-muted small"><?= e($p->kategori) ?><?= $p->subkategori?' &rsaquo; '.e($p->subkategori):'' ?></div>
  <h3><?= e($p->nama) ?></h3>
  <div class="fs-3 harga"><?= rupiah($p->harga) ?> <small class="text-muted fs-6 fw-normal">/ <?= e($p->satuan) ?></small></div>
  <div class="my-2"><?= badge_ketersediaan($p->status_ketersediaan) ?> <span class="ms-2">Stok: <b><?= (int)$p->stok ?></b> <?= e($p->satuan) ?></span></div>
  <p class="text-muted"><i class="fa fa-location-dot"></i> <?= e($p->kecamatan) ?><?= $p->desa?', '.e($p->desa):'' ?><?= $p->alamat?' &mdash; '.e($p->alamat):'' ?></p>
  <div class="card card-body mb-3"><div class="d-flex align-items-center">
   <div><a class="fw-semibold" href="<?= site_url('profil-peternak/'.$p->peternak_id) ?>"><?= e($p->peternak) ?></a> <span class="badge badge-verif"><i class="fa fa-circle-check"></i> Peternak Terverifikasi</span></div></div></div>
  <?php if($p->status_ketersediaan==='tersedia' && wa_valid($p->whatsapp)): ?>
   <a class="btn btn-wa btn-lg w-100" href="<?= site_url('wa/'.$p->id) ?>" rel="nofollow noopener" target="_blank"><i class="fab fa-whatsapp"></i> HUBUNGI PENJUAL VIA WHATSAPP</a>
  <?php else: ?><div class="alert alert-warning mb-0">Produk ini sedang tidak tersedia.</div><?php endif; ?>
 </div>
</div>
<div class="row mt-4"><div class="col-md-7"><h5>Deskripsi</h5><p><?= nl2br(e($p->deskripsi)) ?></p></div>
 <div class="col-md-5"><h5>Spesifikasi</h5><table class="table table-sm">
  <?php foreach(array('Jenis ternak'=>$p->jenis_ternak,'Ras / jenis'=>$p->ras,'Jenis kelamin'=>$p->jenis_kelamin,'Umur'=>$p->umur,'Berat'=>$p->berat,'Kondisi'=>$p->kondisi) as $l=>$v): if($v): ?><tr><th><?= $l ?></th><td><?= e(ucfirst($v)) ?></td></tr><?php endif; endforeach; ?>
  <?php if($p->spesifikasi): ?><tr><th>Lainnya</th><td><?= nl2br(e($p->spesifikasi)) ?></td></tr><?php endif; ?></table></div></div>
<?php if($lain): ?><h5 class="mt-4">Produk lain dari penjual ini</h5><div class="row g-3"><?php foreach($lain as $p) include(__DIR__.'/_card.php'); ?></div><?php endif; ?>
</div>
