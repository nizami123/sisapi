// Dropdown bertingkat: <select data-desa-target="#desa"> -> isi desa via AJAX
document.addEventListener('DOMContentLoaded',function(){
  var base=document.body.getAttribute('data-base')||'';
  document.querySelectorAll('select[data-desa-target]').forEach(function(sel){
    sel.addEventListener('change',function(){
      var t=document.querySelector(sel.getAttribute('data-desa-target')); if(!t) return;
      t.innerHTML='<option value="">- Semua Desa -</option>'; if(!sel.value) return;
      fetch(base+'api/desa/'+sel.value).then(function(r){return r.json()}).then(function(rows){
        rows.forEach(function(d){var o=document.createElement('option');o.value=d.id;o.textContent=d.nama;t.appendChild(o);});
      });
    });
  });
  document.querySelectorAll('select[data-sub-target]').forEach(function(sel){
    sel.addEventListener('change',function(){
      var t=document.querySelector(sel.getAttribute('data-sub-target')); if(!t) return;
      t.innerHTML='<option value="">- Pilih Subkategori -</option>'; if(!sel.value) return;
      fetch(base+'api/subkategori/'+sel.value).then(function(r){return r.json()}).then(function(rows){
        rows.forEach(function(d){var o=document.createElement('option');o.value=d.id;o.textContent=d.nama;t.appendChild(o);});
      });
    });
  });
  document.querySelectorAll('.gal-thumb').forEach(function(i){i.addEventListener('click',function(){document.getElementById('galMain').src=i.src;});});
  if(window.jQuery && jQuery.fn.DataTable){ jQuery('table.dt').DataTable({language:{url:'https://cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'},pageLength:25}); }
});
