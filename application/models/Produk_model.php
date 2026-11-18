<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Produk_model extends CI_Model {
    const BASE = 'p.*, k.nama AS kategori, sk.nama AS subkategori, kc.nama AS kecamatan, ds.nama AS desa,
        pt.nama AS peternak, pt.whatsapp, pt.status_verifikasi AS pt_status,
        (SELECT file FROM produk_foto f WHERE f.produk_id=p.id ORDER BY f.urutan,f.id LIMIT 1) AS foto';

    public function query(){
        $this->db->select(self::BASE,FALSE)->from('produk p')
            ->join('kategori k','k.id=p.kategori_id')
            ->join('subkategori sk','sk.id=p.subkategori_id','left')
            ->join('kecamatan kc','kc.id=p.kecamatan_id')
            ->join('desa ds','ds.id=p.desa_id','left')
            ->join('peternak pt','pt.id=p.peternak_id');
        return $this->db;
    }
    public function find($id){ return $this->query()->where('p.id',(int)$id)->where('p.deleted_at IS NULL',NULL,FALSE)->get()->row(); }
    public function fotos($pid){ return $this->db->order_by('urutan,id')->get_where('produk_foto',array('produk_id'=>(int)$pid))->result(); }

    /** Terapkan filter katalog dari GET */
    public function filter($g){
        if(!empty($g['q'])) $this->db->like('p.nama',$g['q']);
        if(!empty($g['kategori'])) $this->db->where('p.kategori_id',(int)$g['kategori']);
        if(!empty($g['sub'])) $this->db->where('p.subkategori_id',(int)$g['sub']);
        if(!empty($g['kec'])) $this->db->where('p.kecamatan_id',(int)$g['kec']);
        if(!empty($g['desa'])) $this->db->where('p.desa_id',(int)$g['desa']);
        if(isset($g['hmin']) && $g['hmin']!=='') $this->db->where('p.harga >=',(float)$g['hmin']);
        if(isset($g['hmax']) && $g['hmax']!=='') $this->db->where('p.harga <=',(float)$g['hmax']);
        if(!empty($g['status']) && in_array($g['status'],array('tersedia','habis'))) $this->db->where('p.status_ketersediaan',$g['status']);
        $urut = isset($g['urut'])?$g['urut']:'terbaru';
        if($urut=='termurah') $this->db->order_by('p.harga','ASC');
        elseif($urut=='termahal') $this->db->order_by('p.harga','DESC');
        else $this->db->order_by('p.created_at','DESC');
    }
    /** Ambil & bersihkan input form produk (dipakai form admin dan form peternak) */
    public function ambil_input(){
        $i=$this->input; $kat=(int)$i->post('kategori_id');
        return array('nama'=>$i->post('nama',TRUE),'kategori_id'=>$kat,'subkategori_id'=>(int)$i->post('subkategori_id')?:NULL,
          'harga'=>(float)preg_replace('/[^0-9.]/','',$i->post('harga')),'satuan'=>$i->post('satuan',TRUE),
          'deskripsi'=>$i->post('deskripsi',TRUE),'spesifikasi'=>$i->post('spesifikasi',TRUE),
          'jenis_ternak'=>$i->post('jenis_ternak',TRUE),'ras'=>$i->post('ras',TRUE),
          'jenis_kelamin'=>in_array($i->post('jenis_kelamin'),array('jantan','betina'))?$i->post('jenis_kelamin'):NULL,
          'umur'=>$i->post('umur',TRUE),'berat'=>$i->post('berat',TRUE),'kondisi'=>$i->post('kondisi',TRUE),
          'kecamatan_id'=>(int)$i->post('kecamatan_id'),'desa_id'=>(int)$i->post('desa_id')?:NULL,'alamat'=>$i->post('alamat',TRUE));
    }
    /** Validasi form produk. Return TRUE bila lolos, selain itu string pesan error. */
    public function valid($d){
        $this->form_validation->set_rules('nama','Nama produk','required|max_length[200]');
        $this->form_validation->set_rules('kategori_id','Kategori','required|integer');
        $this->form_validation->set_rules('harga','Harga','required');
        $this->form_validation->set_rules('satuan','Satuan','required|max_length[30]');
        $this->form_validation->set_rules('stok','Stok','required|integer|greater_than_equal_to[0]');
        $this->form_validation->set_rules('kecamatan_id','Kecamatan','required|integer');
        if(!$this->form_validation->run()) return strip_tags(validation_errors(' ','; '));
        if(!$this->db->where(array('id'=>$d['kategori_id'],'is_active'=>1))->count_all_results('kategori')) return 'Kategori tidak valid.';
        if($d['subkategori_id'] && !$this->db->where(array('id'=>$d['subkategori_id'],'kategori_id'=>$d['kategori_id']))->count_all_results('subkategori')) return 'Subkategori tidak sesuai kategori.';
        if($d['desa_id'] && !$this->db->where(array('id'=>$d['desa_id'],'kecamatan_id'=>$d['kecamatan_id']))->count_all_results('desa')) return 'Desa tidak sesuai kecamatan.';
        return TRUE;
    }
    /** Nilai ketersediaan dari POST (default tersedia) */
    public function ketersediaan_input(){
        $k=$this->input->post('status_ketersediaan');
        return in_array($k,array('tersedia','habis','tidak_aktif'))?$k:'tersedia';
    }
    /**
     * Simpan produk BARU beserta foto & riwayat verifikasi.
     * $status: 'menunggu' bila diinput peternak, 'disetujui' bila diinput admin Dinas.
     * Return id produk.
     */
    public function buat($d,$stok,$ket,$user_id,$peternak_id,$status,$fotos,$admin_id,$catatan){
        $now=date('Y-m-d H:i:s');
        $d+=array('user_id'=>$user_id,'peternak_id'=>$peternak_id,'stok'=>$stok,'status_ketersediaan'=>$ket,'status_verifikasi'=>$status,'created_at'=>$now);
        $this->db->insert('produk',$d); $pid=$this->db->insert_id();
        foreach((array)$fotos as $n=>$f) $this->db->insert('produk_foto',array('produk_id'=>$pid,'file'=>$f,'urutan'=>$n,'created_at'=>$now));
        $this->log_history($pid,$peternak_id,$admin_id,NULL,$status,$catatan);
        return $pid;
    }
    public function log_history($pid,$peternak_id,$admin_id,$lama,$baru,$cat=''){
        $this->db->insert('verifikasi_produk',array('produk_id'=>$pid,'peternak_id'=>$peternak_id,'admin_id'=>$admin_id,
            'status_lama'=>$lama,'status_baru'=>$baru,'catatan'=>$cat,'created_at'=>date('Y-m-d H:i:s')));
    }
}
