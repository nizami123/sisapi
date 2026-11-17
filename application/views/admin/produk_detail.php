<?php $rv=$revisi?$revisi['data']:NULL; ?>
<div class="row"><div class="col-lg-7">
<div class="card mb-3"><div class="card-body"><h5><?= e($p->nama) ?> <?= badge_verif($p->status_verifikasi) ?> <?= badge_ketersediaan($p->status_ketersediaan) ?></h5>
<div class="d-flex gap-2 flex-wrap mb-2"><?php foreach($fotos as $f): ?><a href="<?= foto_url($f->file) ?>" target="_blank"><img src="<?= foto_url($f->file) ?>" height="90" class="rounded"></a><?php endforeach; ?></div>
<table class="table table-sm"><tr><th>Penjual</th><td><?= e($p->peternak) ?> <?= badge_verif($p->pt_status) ?> &middot; <?= e($p->whatsapp) ?></td></tr><tr><th>Kategori</th><td><?= e($p->kategori) ?> / <?= e($p->subkategori) ?></td></tr>
<tr><th>Harga</th><td><?= rupiah($p->harga) ?> / <?= e($p->satuan) ?> &middot; stok <?= (int)$p->stok ?></td></tr><tr><th>Lokasi</th><td><?= e($p->kecamatan) ?>, <?= e($p->desa) ?> &mdash; <?= e($p->alamat) ?></td></tr>
<tr><th>Ternak</th><td><?= e($p->jenis_ternak) ?> <?= e($p->ras) ?> <?= e($p->jenis_kelamin) ?> <?= e($p->umur) ?> <?= e($p->berat) ?> <?= e($p->kondisi) ?></td></tr>
<tr><th>Spesifikasi</th><td><?= nl2br(e($p->spesifikasi)) ?></td></tr><tr><th>Deskripsi</th><td><?= nl2br(e($p->deskripsi)) ?></td></tr><tr><th>Statistik</th><td><?= (int)$p->total_view ?> dilihat &middot; <?= (int)$p->total_wa ?> klik WA</td></tr></table></div></div>
<?php if($rv): ?><div class="card border-warning mb-3"><div class="card-header bg-warning">Revisi menunggu persetujuan ulang</div><div class="card-body"><table class="table table-sm">
<?php foreach($rv as $k=>$val): if((string)$p->$k!==(string)$val): ?><tr><th><?= e($k) ?></th><td class="text-danger"><del><?= e($p->$k) ?></del></td><td class="text-success"><?= e($val) ?></td></tr><?php endif; endforeach; ?></table>
<?php if(!empty($revisi['foto_baru'])): ?><div class="small">Foto baru: <?= count($revisi['foto_baru']) ?> &middot; <?php foreach($revisi['foto_baru'] as $f): ?><a target="_blank" href="<?= foto_url($f) ?>">lihat</a> <?php endforeach; ?></div><?php endif; ?>
<?php if(!empty($revisi['hapus_foto'])): ?><div class="small">Foto dihapus: <?= count($revisi['hapus_foto']) ?></div><?php endif; ?>
<?= form_open('admin/produk/keputusan/'.$p->id) ?><input name="catatan" class="form-control my-2" placeholder="Catatan (wajib jika menolak)"><button name="aksi" value="setuju_revisi" class="btn btn-success">Setujui revisi</button> <button name="aksi" value="tolak_revisi" class="btn btn-danger">Tolak revisi</button><?= form_close() ?></div></div><?php endif; ?>
</div>
<div class="col-lg-5">
<div class="card mb-3"><div class="card-header">Keputusan verifikasi produk</div><div class="card-body"><?= form_open('admin/produk/keputusan/'.$p->id) ?><textarea name="catatan" class="form-control mb-2" rows="2" placeholder="Catatan (wajib jika menolak)"></textarea>
<button name="aksi" value="setuju" class="btn btn-success">Setujui</button> <button name="aksi" value="tolak" class="btn btn-danger">Tolak</button><?= form_close() ?>
<?php if($p->pt_status!=='terverifikasi'): ?><div class="small text-warning mt-2">Peternak belum terverifikasi: produk belum akan tayang publik.</div><?php endif; ?></div></div>
<div class="card mb-3"><div class="card-body"><?= form_open('admin/produk/ketersediaan/'.$p->id,array('class'=>'d-flex gap-2 mb-2')) ?><select name="status" class="form-select"><?php foreach(array('tersedia'=>'Tersedia','habis'=>'Habis','tidak_aktif'=>'Tidak Aktif') as $k=>$l): ?><option value="<?= $k ?>" <?= $p->status_ketersediaan==$k?'selected':'' ?>><?= $l ?></option><?php endforeach; ?></select><button class="btn btn-outline-dark">Ubah</button><?= form_close() ?>
<?= form_open('admin/produk/unggulan/'.$p->id,array('class'=>'d-inline')) ?><button class="btn btn-outline-warning"><i class="fa fa-star"></i> <?= $p->is_unggulan?'Hapus dari unggulan':'Jadikan unggulan' ?></button><?= form_close() ?>
<?= form_open('admin/produk/hapus/'.$p->id,array('class'=>'d-inline','onsubmit'=>"return confirm('Hapus produk?')")) ?><button class="btn btn-outline-danger">Hapus</button><?= form_close() ?></div></div>
<div class="card"><div class="card-header">Riwayat verifikasi</div><ul class="list-group list-group-flush"><?php foreach($riwayat as $r): ?><li class="list-group-item small"><?= tgl($r->created_at) ?>: <?= e($r->status_lama) ?> &rarr; <b><?= e($r->status_baru) ?></b> <?= $r->admin?'oleh '.e($r->admin):'' ?><br><?= e($r->catatan) ?></li><?php endforeach; ?></ul></div>
</div></div>
