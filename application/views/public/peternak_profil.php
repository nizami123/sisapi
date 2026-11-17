<div class="container py-4">
 <div class="card card-body mb-4"><div class="row align-items-center">
  <div class="col-auto"><img src="<?= foto_url($pt->foto,'peternak') ?>" class="rounded-circle" width="96" height="96" style="object-fit:cover"></div>
  <div class="col"><h4 class="mb-1"><?= e($pt->nama) ?> <span class="badge badge-verif"><i class="fa fa-circle-check"></i> Peternak Terverifikasi</span></h4>
   <?php if($pt->nama_kelompok): ?><div class="text-muted"><?= e($pt->nama_kelompok) ?></div><?php endif; ?>
   <div class="small"><i class="fa fa-location-dot"></i> <?= e($pt->alamat) ?>, <?= e($pt->desa) ?>, <?= e($pt->kecamatan) ?></div>
   <div class="small"><i class="fab fa-whatsapp"></i> <?= e($pt->whatsapp) ?> &middot; <?= count($produk) ?> produk aktif</div></div></div>
  <?php if($pt->deskripsi): ?><p class="mt-3 mb-0"><?= nl2br(e($pt->deskripsi)) ?></p><?php endif; ?></div>
 <h5>Produk yang dijual</h5><div class="row g-3"><?php foreach($produk as $p) include(__DIR__.'/_card.php'); ?>
 <?php if(!$produk): ?><div class="col-12 text-muted">Belum ada produk.</div><?php endif; ?></div></div>
