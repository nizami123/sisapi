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
    public function log_history($pid,$peternak_id,$admin_id,$lama,$baru,$cat=''){
        $this->db->insert('verifikasi_produk',array('produk_id'=>$pid,'peternak_id'=>$peternak_id,'admin_id'=>$admin_id,
            'status_lama'=>$lama,'status_baru'=>$baru,'catatan'=>$cat,'created_at'=>date('Y-m-d H:i:s')));
    }
}
