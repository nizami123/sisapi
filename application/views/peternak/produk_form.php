<?php $pp=$p; ?>
<?php if($revisi): ?><div class="alert alert-info">Ada perubahan yang menunggu persetujuan ulang admin. Tampilan publik masih memakai data sebelumnya.</div><?php endif; ?>
<?php if($pp && $pp->status_verifikasi==='ditolak'): ?><div class="alert alert-danger">Ditolak: <?= e($pp->alasan_penolakan) ?></div><?php endif; ?>
<div class="card"><div class="card-body">
<?= form_open_multipart('peternak/produk/simpan'.($pp?'/'.$pp->id:'')) ?>
<div class="row g-3">
 <div class="col-md-8"><label class="form-label">Nama produk *</label><input name="nama" class="form-control" value="<?= $pp?e($pp->nama):'' ?>" required></div>
 <div class="col-md-4"><label class="form-label">Kategori *</label><select name="kategori_id" id="kat" class="form-select" data-sub-target="#sub" required><option value="">- Pilih -</option><?php foreach($kategori as $k): ?><option value="<?= $k->id ?>" data-slug="<?= e($k->slug) ?>" <?= ($pp&&$pp->kategori_id==$k->id)?'selected':'' ?>><?= e($k->nama) ?></option><?php endforeach; ?></select></div>
 <div class="col-md-4"><label class="form-label">Subkategori</label><select name="subkategori_id" id="sub" class="form-select"><option value="">- Pilih Subkategori -</option><?php foreach($sub as $s): if($pp && $s->kategori_id==$pp->kategori_id): ?><option value="<?= $s->id ?>" <?= $pp->subkategori_id==$s->id?'selected':'' ?>><?= e($s->nama) ?></option><?php endif; endforeach; ?></select></div>
 <div class="col-md-4"><label class="form-label">Harga (Rp) *</label><input name="harga" inputmode="numeric" class="form-control" value="<?= $pp?(int)$pp->harga:'' ?>" required></div>
 <div class="col-md-2"><label class="form-label">Satuan *</label><input name="satuan" class="form-control" placeholder="ekor/kg/karung" value="<?= $pp?e($pp->satuan):'' ?>" required></div>
 <div class="col-md-2"><label class="form-label">Stok *</label><input type="number" min="0" name="stok" class="form-control" value="<?= $pp?(int)$pp->stok:1 ?>" required></div>
 <div class="col-12"><label class="form-label">Deskripsi</label><textarea name="deskripsi" class="form-control" rows="3"><?= $pp?e($pp->deskripsi):'' ?></textarea></div>
 <div class="col-12 box-ternak"><div class="row g-3 border rounded p-2 mx-0"><div class="col-12 fw-semibold">Informasi ternak</div>
  <div class="col-md-4"><label class="form-label">Jenis ternak</label><input name="jenis_ternak" class="form-control" value="<?= $pp?e($pp->jenis_ternak):'' ?>"></div>
  <div class="col-md-4"><label class="form-label">Ras / jenis</label><input name="ras" class="form-control" value="<?= $pp?e($pp->ras):'' ?>"></div>
  <div class="col-md-4"><label class="form-label">Jenis kelamin</label><select name="jenis_kelamin" class="form-select"><option value="">-</option><option value="jantan" <?= ($pp&&$pp->jenis_kelamin=='jantan')?'selected':'' ?>>Jantan</option><option value="betina" <?= ($pp&&$pp->jenis_kelamin=='betina')?'selected':'' ?>>Betina</option></select></div>
  <div class="col-md-4"><label class="form-label">Umur</label><input name="umur" class="form-control" value="<?= $pp?e($pp->umur):'' ?>"></div>
  <div class="col-md-4"><label class="form-label">Berat</label><input name="berat" class="form-control" value="<?= $pp?e($pp->berat):'' ?>"></div>
  <div class="col-md-4"><label class="form-label">Kondisi</label><input name="kondisi" class="form-control" value="<?= $pp?e($pp->kondisi):'' ?>"></div></div></div>
 <div class="col-12 box-lain"><label class="form-label">Spesifikasi (hasil peternakan / pakan)</label><textarea name="spesifikasi" class="form-control" rows="2"><?= $pp?e($pp->spesifikasi):'' ?></textarea></div>
 <div class="col-md-4"><label class="form-label">Kecamatan *</label><select name="kecamatan_id" class="form-select" data-desa-target="#desa" required><option value="">- Pilih -</option><?php $kc=$pp?$pp->kecamatan_id:$pt->kecamatan_id; foreach($kec_list as $k): ?><option value="<?= $k->id ?>" <?= $kc==$k->id?'selected':'' ?>><?= e($k->nama) ?></option><?php endforeach; ?></select></div>
 <div class="col-md-4"><label class="form-label">Desa</label><select name="desa_id" id="desa" class="form-select"><option value="">-</option><?php $dc=$pp?$pp->desa_id:$pt->desa_id; foreach($desa_list as $d): ?><option value="<?= $d->id ?>" <?= $dc==$d->id?'selected':'' ?>><?= e($d->nama) ?></option><?php endforeach; ?></select></div>
 <div class="col-md-4"><label class="form-label">Ketersediaan</label><select name="status_ketersediaan" class="form-select"><?php foreach(array('tersedia'=>'Tersedia','habis'=>'Habis','tidak_aktif'=>'Tidak Aktif') as $k=>$l): ?><option value="<?= $k ?>" <?= ($pp&&$pp->status_ketersediaan==$k)?'selected':'' ?>><?= $l ?></option><?php endforeach; ?></select></div>
 <div class="col-12"><label class="form-label">Alamat lengkap</label><input name="alamat" class="form-control" value="<?= $pp?e($pp->alamat):e($pt->alamat) ?>"></div>
 <div class="col-12"><label class="form-label">Foto (maks. total 5, JPG/PNG/WEBP, maks 2 MB/foto)</label><input type="file" name="fotos[]" multiple accept="image/*" class="form-control">
  <div class="d-flex gap-3 flex-wrap mt-2"><?php foreach($fotos as $f): ?><label class="small text-center"><img src="<?= foto_url($f->file) ?>" width="80" height="60" style="object-fit:cover" class="rounded d-block"><input type="checkbox" name="hapus_foto[]" value="<?= $f->id ?>"> hapus</label><?php endforeach; ?></div></div>
 <div class="col-12 small text-muted">Nama peternak &amp; WhatsApp diambil dari <a href="<?= site_url('peternak/profil') ?>">Profil Saya</a> (<?= e($pt->nama) ?> / <?= e($pt->whatsapp) ?>).</div>
</div>
<div class="mt-3 d-flex gap-2"><button class="btn btn-secondary" name="simpan" value="1"><?= ($pp&&$pp->status_verifikasi==='disetujui')?'Simpan Perubahan':'Simpan Draft' ?></button>
<?php if(!$pp || in_array($pp->status_verifikasi,array('draft','ditolak'))): ?><button class="btn btn-sisapi" name="ajukan" value="1">Simpan &amp; Ajukan ke Admin</button><?php endif; ?></div>
<?= form_close() ?></div></div>
<script>
document.addEventListener('DOMContentLoaded',function(){var k=document.getElementById('kat');function t(){var s=k.options[k.selectedIndex]?k.options[k.selectedIndex].getAttribute('data-slug'):'';document.querySelector('.box-ternak').style.display=(s==='ternak'||!s)?'':'none';document.querySelector('.box-lain').style.display=(s==='ternak')?'none':'';}k.addEventListener('change',t);t();});
</script>
