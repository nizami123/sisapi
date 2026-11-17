<p class="text-muted">Pilih produk (yang sudah disetujui) untuk tampil di beranda.</p>
<div class="card"><div class="card-body table-responsive"><table class="table dt"><thead><tr><th>Produk</th><th>Penjual</th><th>Status</th><th>Unggulan</th></tr></thead><tbody>
<?php foreach($list as $p): ?><tr><td><?= e($p->nama) ?></td><td><?= e($p->peternak) ?></td><td><?= badge_ketersediaan($p->status_ketersediaan) ?></td>
<td><?= form_open('admin/produk/unggulan/'.$p->id) ?><input type="hidden" name="back" value="list"><button class="btn btn-sm <?= $p->is_unggulan?'btn-warning':'btn-outline-secondary' ?>"><i class="fa fa-star"></i> <?= $p->is_unggulan?'Unggulan':'Jadikan' ?></button><?= form_close() ?></td></tr><?php endforeach; ?></tbody></table></div></div>
