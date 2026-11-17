<!DOCTYPE html>
<html lang="id"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= e($title) ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
<link href="<?= base_url('assets/css/sisapi.css') ?>" rel="stylesheet">
</head>
<body data-base="<?= base_url() ?>">
<nav class="navbar navbar-expand-lg navbar-dark navbar-sisapi sticky-top">
 <div class="container">
  <a class="navbar-brand fw-bold" href="<?= site_url() ?>"><img src="<?= base_url('assets/img/logo.png') ?>" alt="SISAPI" height="34" class="bg-white rounded me-2 p-1">SISAPI</a>
  <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#nav"><span class="navbar-toggler-icon"></span></button>
  <div class="collapse navbar-collapse" id="nav">
   <ul class="navbar-nav me-auto">
    <li class="nav-item"><a class="nav-link" href="<?= site_url() ?>">Beranda</a></li>
    <li class="nav-item"><a class="nav-link" href="<?= site_url('ternak') ?>">Ternak</a></li>
    <li class="nav-item"><a class="nav-link" href="<?= site_url('hasil-peternakan') ?>">Hasil Peternakan</a></li>
    <li class="nav-item"><a class="nav-link" href="<?= site_url('pakan-ternak') ?>">Pakan Ternak</a></li>
    <li class="nav-item"><a class="nav-link" href="<?= site_url('daftar-peternak') ?>">Peternak</a></li>
    <li class="nav-item"><a class="nav-link" href="<?= site_url('tentang') ?>">Tentang SISAPI</a></li>
   </ul>
   <a class="btn btn-light btn-sm me-2" href="<?= site_url('daftar') ?>"><i class="fa fa-user-plus"></i> Daftar Peternak</a>
   <a class="btn btn-outline-light btn-sm" href="<?= site_url('login') ?>"><i class="fa fa-user"></i> Masuk Peternak/Admin</a>
  </div>
 </div>
</nav>
<?= $content ?>
<footer class="footer-sisapi mt-5 py-4"><div class="container small">
 <div class="row"><div class="col-md-6"><b><?= e($app['nama_app']) ?></b><br><?= e($app['instansi']) ?><br><?= e($app['alamat']) ?></div>
 <div class="col-md-6 text-md-end mt-2 mt-md-0"><?php if(!empty($app['telepon'])): ?><i class="fa fa-phone"></i> <?= e($app['telepon']) ?><br><?php endif; ?><?php if(!empty($app['email'])): ?><i class="fa fa-envelope"></i> <?= e($app['email']) ?><br><?php endif; ?>&copy; <?= date('Y') ?> SISAPI</div></div>
</div></footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= base_url('assets/js/sisapi.js') ?>"></script>
</body></html>
