<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Stat_model extends CI_Model {
    /** Catat view/klik WA; abaikan duplikat dari IP+produk dalam 30 menit (view) / 5 menit (wa) */
    public function catat($produk,$tipe){
        $hash = hash('sha256', $this->input->ip_address().'|'.$this->input->user_agent().'|'.config_item('encryption_key'));
        $win  = $tipe=='view' ? 1800 : 300;
        $dup = $this->db->where(array('produk_id'=>$produk->id,'tipe'=>$tipe,'ip_hash'=>$hash))
            ->where('created_at >',date('Y-m-d H:i:s',time()-$win))->count_all_results('statistik_produk');
        if($dup) return FALSE;
        $this->db->insert('statistik_produk',array('produk_id'=>$produk->id,'user_id'=>$produk->user_id,
            'kategori_id'=>$produk->kategori_id,'kecamatan_id'=>$produk->kecamatan_id,'tipe'=>$tipe,'ip_hash'=>$hash,'created_at'=>date('Y-m-d H:i:s')));
        $col = $tipe=='view' ? 'total_view' : 'total_wa';
        $this->db->set($col,$col.'+1',FALSE)->where('id',$produk->id)->update('produk');
        return TRUE;
    }
    /** Kunjungan per hari 30 hari terakhir; $uid opsional (peternak) */
    public function harian($uid=NULL,$hari=30){
        $this->db->select("DATE(created_at) d, SUM(tipe='view') v, SUM(tipe='wa_click') w",FALSE)
            ->where('created_at >=',date('Y-m-d',strtotime("-$hari days")));
        if($uid) $this->db->where('user_id',(int)$uid);
        return $this->db->group_by('DATE(created_at)')->order_by('d')->get('statistik_produk')->result();
    }
    public function ringkas_periode($uid=NULL){
        $out=array();
        foreach(array('harian'=>'1 day','mingguan'=>'7 days','bulanan'=>'30 days') as $k=>$iv){
            $this->db->where('tipe','view')->where('created_at >=',date('Y-m-d H:i:s',strtotime("-$iv")));
            if($uid) $this->db->where('user_id',(int)$uid);
            $out[$k]=$this->db->count_all_results('statistik_produk');
        }
        return $out;
    }
    public function top($tipe,$uid=NULL,$limit=5){
        $col = $tipe=='view'?'total_view':'total_wa';
        $this->db->select('id,nama,total_view,total_wa')->where('deleted_at IS NULL',NULL,FALSE)->where($col.' >',0);
        if($uid) $this->db->where('user_id',(int)$uid);
        return $this->db->order_by($col,'DESC')->limit($limit)->get('produk')->result();
    }
}
