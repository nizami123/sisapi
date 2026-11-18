<?php $pp=$p; ?>
<?php if(!$pp): ?><div class="alert alert-info">Produk yang Anda tambahkan akan berstatus <b>Menunggu</b> sampai disetujui admin Dinas, baru kemudian tayang di katalog.</div><?php endif; ?>
<?php if($revisi): ?><div class="alert alert-info">Ada perubahan yang menunggu persetujuan ulang admin. Tampilan publik masih memakai data sebelumnya.</div><?php endif; ?>
<?php if($pp && $pp->status_verifikasi==='ditolak'): ?><div class="alert alert-danger">Ditolak: <?= e($pp->alasan_penolakan) ?></div><?php endif; ?>
<div class="card"><div class="card-body">
<?= form_open_multipart('peternak/produk/simpan'.($pp?'/'.$pp->id:'')) ?>
<div class="row g-3">
<?= $this->load->view('partials/produk_fields',NULL,TRUE) ?>
 <div class="col-12 small text-muted">Nama peternak &amp; WhatsApp diambil dari <a href="<?= site_url('peternak/profil') ?>">Profil Saya</a> (<?= e($pt->nama) ?> / <?= e($pt->whatsapp) ?>).</div>
</div>
<div class="mt-3 d-flex gap-2">
<?php if(!$pp): ?><button class="btn btn-sisapi" name="ajukan" value="1">Simpan &amp; Kirim ke Admin</button>
<?php else: ?><button class="btn btn-secondary" name="simpan" value="1"><?= $pp->status_verifikasi==='disetujui'?'Simpan Perubahan':'Simpan Draft' ?></button>
<?php if(in_array($pp->status_verifikasi,array('draft','ditolak'))): ?><button class="btn btn-sisapi" name="ajukan" value="1">Simpan &amp; Ajukan ke Admin</button><?php endif; ?>
<?php endif; ?></div>
<?= form_close() ?></div></div>
