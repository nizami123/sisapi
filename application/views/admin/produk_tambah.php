<div class="alert alert-success">Produk yang diinput admin Dinas <b>langsung berstatus Disetujui</b> (tanpa antrean verifikasi). Produk baru tayang di katalog bila peternaknya berstatus Terverifikasi.</div>
<div class="card"><div class="card-body">
<?= form_open_multipart('admin/produk/simpan') ?>
<div class="row g-3">
 <div class="col-12"><label class="form-label">Peternak (penjual) *</label>
  <select name="peternak_id" class="form-select" required><option value="">- Pilih peternak -</option>
  <?php $lbl=array('belum'=>'Belum diverifikasi','menunggu'=>'Menunggu verifikasi','terverifikasi'=>'Terverifikasi','ditolak'=>'Ditolak'); foreach($peternak_list as $r): ?>
   <option value="<?= $r->id ?>" <?= (int)$sel===(int)$r->id?'selected':'' ?>><?= e($r->nama) ?><?= $r->kecamatan?' - '.e($r->kecamatan):'' ?> (<?= e(isset($lbl[$r->status_verifikasi])?$lbl[$r->status_verifikasi]:$r->status_verifikasi) ?>)</option>
  <?php endforeach; ?></select>
  <div class="form-text">Belum ada peternaknya? <a href="<?= site_url('admin/peternak/tambah') ?>">Tambah peternak dulu</a>.</div></div>
<?= $this->load->view('partials/produk_fields',NULL,TRUE) ?>
</div>
<div class="mt-3"><button class="btn btn-sisapi">Simpan &amp; Setujui</button> <a href="<?= site_url('admin/produk') ?>" class="btn btn-link">Batal</a></div>
<?= form_close() ?></div></div>
