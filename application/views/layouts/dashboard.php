<?php $role=$me['role']; $seg=$this->uri->segment(2); $s1=$this->uri->segment(1);
function _mi($url,$icon,$label){ $a=(current_url()==site_url($url))?' active':''; return '<li class="nav-item"><a href="'.site_url($url).'" class="nav-link'.$a.'"><i class="nav-icon fa '.$icon.'"></i><p>'.$label.'</p></a></li>'; } ?>
<!DOCTYPE html>
<html lang="id"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= e($title) ?> - SISAPI</title>
<link href="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0-beta3/dist/css/adminlte.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<link href="<?= base_url('assets/css/sisapi.css') ?>" rel="stylesheet">
</head>
<body class="layout-fixed sidebar-expand-lg bg-body-tertiary" data-base="<?= base_url() ?>">
<div class="app-wrapper">
 <nav class="app-header navbar navbar-expand bg-body"><div class="container-fluid">
  <ul class="navbar-nav"><li class="nav-item"><a class="nav-link" data-lte-toggle="sidebar" href="#" role="button"><i class="fa fa-bars"></i></a></li></ul>
  <ul class="navbar-nav ms-auto"><li class="nav-item"><span class="nav-link"><i class="fa fa-user"></i> <?= e($me['nama']) ?> (<?= e($role) ?>)</span></li>
   <li class="nav-item"><a class="nav-link" href="<?= site_url('akun/password') ?>">Ganti Password</a></li>
   <li class="nav-item"><a class="nav-link" href="<?= site_url('logout') ?>">Logout</a></li></ul>
 </div></nav>
 <aside class="app-sidebar shadow" style="background:#14532d" data-bs-theme="dark">
  <div class="sidebar-brand"><a href="<?= site_url($role) ?>" class="brand-link"><span class="brand-text fw-bold">SISAPI</span></a></div>
  <div class="sidebar-wrapper"><nav class="mt-2"><ul class="nav sidebar-menu flex-column" role="menu">
  <?php if($role==='admin'){
    echo _mi('admin','fa-gauge','Dashboard'),_mi('admin/peternak','fa-users','Data Peternak'),_mi('admin/peternak/index/menunggu','fa-user-check','Verifikasi Peternak'),
      _mi('admin/produk','fa-box','Data Produk'),_mi('admin/produk/index/menunggu','fa-clipboard-check','Verifikasi Produk'),
      _mi('admin/master/kategori','fa-tags','Kategori'),_mi('admin/master/subkategori','fa-tag','Subkategori'),
      _mi('admin/master/kecamatan','fa-map','Data Kecamatan'),_mi('admin/master/desa','fa-location-dot','Data Desa'),
      _mi('admin/unggulan','fa-star','Produk Unggulan'),_mi('admin/banner','fa-image','Banner Beranda'),
      _mi('admin/statistik','fa-chart-line','Statistik'),_mi('admin/pengguna','fa-user-shield','Manajemen Admin'),
      _mi('admin/pengaturan','fa-gear','Pengaturan Aplikasi'),_mi('admin/log','fa-clock-rotate-left','Log Aktivitas');
  } else {
    echo _mi('peternak','fa-gauge','Dashboard'),_mi('peternak/profil','fa-id-card','Profil Saya'),_mi('peternak/produk','fa-box','Data Produk Saya'),
      _mi('peternak/produk/tambah','fa-plus','Tambah Produk'),_mi('peternak/produk/index/menunggu','fa-hourglass-half','Menunggu Persetujuan'),
      _mi('peternak/produk/index/disetujui','fa-circle-check','Produk Disetujui'),_mi('peternak/produk/index/ditolak','fa-circle-xmark','Produk Ditolak'),
      _mi('peternak/statistik','fa-chart-line','Statistik Produk');
  } echo _mi('logout','fa-right-from-bracket','Logout'); ?>
  </ul></nav></div>
 </aside>
 <main class="app-main"><div class="app-content-header"><div class="container-fluid"><h3 class="mb-0"><?= e($title) ?></h3></div></div>
  <div class="app-content"><div class="container-fluid"><?= flash_html() ?><?= $content ?></div></div></main>
</div>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0-beta3/dist/js/adminlte.min.js"></script>
<script src="<?= base_url('assets/js/sisapi.js') ?>"></script>
</body></html>
