<div class="container py-4">
 <h4><?= e($judul) ?> <small class="text-muted fs-6">(<?= (int)$total ?> produk)</small></h4>
 <form method="get" class="card card-body mb-3"><div class="row g-2">
  <div class="col-12 col-md-4"><input name="q" value="<?= e(isset($g['q'])?$g['q']:'') ?>" class="form-control" placeholder="Nama produk"></div>
  <div class="col-6 col-md-2"><select name="kategori" class="form-select" data-sub-target="#f_sub"><option value="">Kategori</option><?php foreach($kategori_menu as $k): ?><option value="<?= $k->id ?>" <?= (isset($g['kategori'])&&$g['kategori']==$k->id)?'selected':'' ?>><?= e($k->nama) ?></option><?php endforeach; ?></select></div>
  <div class="col-6 col-md-2"><select name="sub" id="f_sub" class="form-select"><option value="">Subkategori</option><?php foreach($sub_list as $s): ?><option value="<?= $s->id ?>" <?= (isset($g['sub'])&&$g['sub']==$s->id)?'selected':'' ?>><?= e($s->nama) ?></option><?php endforeach; ?></select></div>
  <div class="col-6 col-md-2"><select name="kec" class="form-select" data-desa-target="#f_desa"><option value="">Kecamatan</option><?php foreach($kec_list as $k): ?><option value="<?= $k->id ?>" <?= (isset($g['kec'])&&$g['kec']==$k->id)?'selected':'' ?>><?= e($k->nama) ?></option><?php endforeach; ?></select></div>
  <div class="col-6 col-md-2"><select name="desa" id="f_desa" class="form-select"><option value="">Semua Desa</option><?php foreach($desa_list as $d): ?><option value="<?= $d->id ?>" <?= (isset($g['desa'])&&$g['desa']==$d->id)?'selected':'' ?>><?= e($d->nama) ?></option><?php endforeach; ?></select></div>
  <div class="col-6 col-md-2"><input type="number" min="0" name="hmin" value="<?= e(isset($g['hmin'])?$g['hmin']:'') ?>" class="form-control" placeholder="Harga min"></div>
  <div class="col-6 col-md-2"><input type="number" min="0" name="hmax" value="<?= e(isset($g['hmax'])?$g['hmax']:'') ?>" class="form-control" placeholder="Harga maks"></div>
  <div class="col-6 col-md-2"><select name="status" class="form-select"><option value="">Semua status</option><option value="tersedia" <?= (isset($g['status'])&&$g['status']=='tersedia')?'selected':'' ?>>Tersedia</option><option value="habis" <?= (isset($g['status'])&&$g['status']=='habis')?'selected':'' ?>>Habis</option></select></div>
  <div class="col-6 col-md-3"><select name="urut" class="form-select"><?php foreach(array('terbaru'=>'Terbaru','termurah'=>'Harga terendah','termahal'=>'Harga tertinggi') as $v=>$l): ?><option value="<?= $v ?>" <?= (isset($g['urut'])&&$g['urut']==$v)?'selected':'' ?>><?= $l ?></option><?php endforeach; ?></select></div>
  <div class="col-12 col-md-3"><button class="btn btn-sisapi w-100"><i class="fa fa-filter"></i> Terapkan</button></div>
 </div></form>
 <div class="row g-3"><?php foreach($produk as $p) include(__DIR__.'/_card.php'); ?>
 <?php if(!$produk): ?><div class="col-12 text-center text-muted py-5">Produk tidak ditemukan.</div><?php endif; ?></div>
 <nav class="mt-4"><?= $paging ?></nav>
</div>
