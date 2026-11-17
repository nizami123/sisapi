<!DOCTYPE html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Masuk - SISAPI</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"><link href="<?= base_url('assets/css/sisapi.css') ?>" rel="stylesheet"></head>
<body class="d-flex align-items-center" style="min-height:100vh"><div class="container" style="max-width:420px">
<div class="card card-body shadow-sm p-4"><div class="text-center mb-3"><img src="<?= base_url('assets/img/logo.png') ?>" height="90" alt="SISAPI"><div class="text-muted small mt-1">Masuk Peternak / Admin Dinas</div></div>
<?= flash_html() ?>
<?= form_open('login') ?>
<div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" required autofocus></div>
<div class="mb-3"><label class="form-label">Password</label><input type="password" name="password" class="form-control" required></div>
<button class="btn btn-sisapi w-100">Masuk</button><?= form_close() ?>
<a href="<?= site_url() ?>" class="d-block text-center mt-3 small">&laquo; Kembali ke beranda</a></div></div></body></html>
