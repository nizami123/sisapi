<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Dashboard extends Admin_Controller {
    public function index(){
        $this->load->model('Stat_model'); $n=function($t,$w=array(),$wi=NULL){ $this->db->where($w); if($wi) $this->db->where($wi,NULL,FALSE); return $this->db->count_all_results($t); };
        $nd='deleted_at IS NULL';
        $this->data['s']=array(
          'peternak'=>$n('peternak',array(),$nd),'terverifikasi'=>$n('peternak',array('status_verifikasi'=>'terverifikasi'),$nd),
          'produk'=>$n('produk',array(),$nd),'aktif'=>$n('produk',array('status_verifikasi'=>'disetujui','status_ketersediaan'=>'tersedia'),$nd),
          'menunggu'=>$n('produk',array('status_verifikasi'=>'menunggu'),$nd),'ditolak'=>$n('produk',array('status_verifikasi'=>'ditolak'),$nd),
          'ternak'=>$n('produk',array('kategori_id'=>1),$nd),'hasil'=>$n('produk',array('kategori_id'=>2),$nd),'pakan'=>$n('produk',array('kategori_id'=>3),$nd),
          'pt_menunggu'=>$n('peternak',array('status_verifikasi'=>'menunggu'),$nd));
        $this->data['per_kec']=$this->db->query('SELECT kc.nama, COUNT(p.id) jml FROM kecamatan kc JOIN produk p ON p.kecamatan_id=kc.id AND p.deleted_at IS NULL GROUP BY kc.id ORDER BY jml DESC LIMIT 15')->result();
        $this->data['top_view']=$this->Stat_model->top('view'); $this->data['top_wa']=$this->Stat_model->top('wa');
        $this->data['harian']=$this->Stat_model->harian(); $this->data['periode']=$this->Stat_model->ringkas_periode();
        $this->page('admin/dashboard','Dashboard');
    }
}
