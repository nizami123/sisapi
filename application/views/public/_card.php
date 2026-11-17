<div class="col-6 col-md-4 col-lg-3">
 <div class="card card-produk">
  <a href="<?= site_url('produk/'.$p->id) ?>"><img src="<?= foto_url($p->foto) ?>" alt="<?= e($p->nama) ?>" loading="lazy"></a>
  <div class="card-body p-2 p-md-3 d-flex flex-column">
   <div class="small text-muted"><?= e($p->kategori) ?><?= $p->subkategori?' &middot; '.e($p->subkategori):'' ?></div>
   <h6 class="mb-1"><a class="text-decoration-none text-dark" href="<?= site_url('produk/'.$p->id) ?>"><?= e($p->nama) ?></a></h6>
   <div class="harga"><?= rupiah($p->harga) ?> <small class="text-muted fw-normal">/ <?= e($p->satuan) ?></small></div>
   <div class="small mt-1"><i class="fa fa-user"></i> <?= e($p->peternak) ?> <span class="badge badge-verif"><i class="fa fa-circle-check"></i> Terverifikasi</span></div>
   <div class="small text-muted"><i class="fa fa-location-dot"></i> <?= e($p->kecamatan) ?><?= $p->desa?', '.e($p->desa):'' ?></div>
   <div class="mt-2 d-flex justify-content-between align-items-center"><?= badge_ketersediaan($p->status_ketersediaan) ?>
    <a href="<?= site_url('produk/'.$p->id) ?>" class="btn btn-sm btn-sisapi">Detail Produk</a></div>
  </div>
 </div>
</div>
