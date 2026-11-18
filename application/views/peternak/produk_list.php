<?php if($pt->status_verifikasi==='terverifikasi'): ?><a href="<?= site_url('peternak/produk/tambah') ?>" class="btn btn-sisapi mb-3"><i class="fa fa-plus"></i> Tambah Produk</a>
<?php else: ?><div class="alert alert-warning">Anda baru bisa menambah produk setelah akun <b>terverifikasi</b> oleh admin Dinas. Lengkapi <a href="<?= site_url('peternak/profil') ?>">Profil Saya</a> lalu ajukan verifikasi.</div><?php endif; ?>
<div class="card"><div class="card-body table-responsive"><table class="table table-striped dt">
<thead><tr><th>Foto</th><th>Produk</th><th>Harga</th><th>Stok</th><th>Verifikasi</th><th>Ketersediaan</th><th>Dilihat</th><th>Klik WA</th><th></th></tr></thead><tbody>
<?php foreach($list as $p): ?><tr>
<td><img src="<?= foto_url($p->foto) ?>" width="56" height="42" style="object-fit:cover" class="rounded"></td>
<td><?= e($p->nama) ?><br><small class="text-muted"><?= e($p->kategori) ?></small><?php if($p->status_verifikasi==='ditolak'): ?><div class="small text-danger">Ditolak: <?= e($p->alasan_penolakan) ?></div><?php endif; ?></td>
<td><?= rupiah($p->harga) ?>/<?= e($p->satuan) ?></td><td><?= (int)$p->stok ?></td><td><?= badge_verif($p->status_verifikasi) ?></td>
<td><?= form_open('peternak/produk/ketersediaan/'.$p->id,array('class'=>'d-flex gap-1')) ?><select name="status" class="form-select form-select-sm" onchange="this.form.submit()"><?php foreach(array('tersedia'=>'Tersedia','habis'=>'Habis','tidak_aktif'=>'Tidak Aktif') as $v=>$l): ?><option value="<?= $v ?>" <?= $p->status_ketersediaan==$v?'selected':'' ?>><?= $l ?></option><?php endforeach; ?></select><?= form_close() ?></td>
<td><?= (int)$p->total_view ?></td><td><?= (int)$p->total_wa ?></td>
<td class="text-nowrap"><a class="btn btn-sm btn-outline-primary" href="<?= site_url('peternak/produk/edit/'.$p->id) ?>">Edit</a>
<?= form_open('peternak/produk/hapus/'.$p->id,array('class'=>'d-inline','onsubmit'=>"return confirm('Hapus produk ini?')")) ?><button class="btn btn-sm btn-outline-danger">Hapus</button><?= form_close() ?></td></tr><?php endforeach; ?>
</tbody></table></div></div>
