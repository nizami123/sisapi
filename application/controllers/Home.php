<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Home extends Public_Controller {
    public function index(){
        $this->load->model('Produk_model','pm');
        $this->publik_where(); $this->pm->query()->order_by('p.created_at','DESC')->limit(8);
        $this->data['terbaru']=$this->db->get()->result();
        $this->publik_where(); $this->pm->query()->where('p.is_unggulan',1)->order_by('p.updated_at','DESC')->limit(8);
        $this->data['unggulan']=$this->db->get()->result();
        $this->data['banner']=$this->db->order_by('urutan')->get_where('banner',array('is_active'=>1))->result();
        $this->data['peternak']=$this->db->select('pt.*,kc.nama kecamatan,(SELECT COUNT(*) FROM produk p WHERE p.peternak_id=pt.id AND p.status_verifikasi="disetujui" AND p.status_ketersediaan="tersedia" AND p.deleted_at IS NULL) jml',FALSE)
            ->from('peternak pt')->join('kecamatan kc','kc.id=pt.kecamatan_id','left')
            ->where(array('pt.status_verifikasi'=>'terverifikasi','pt.deleted_at'=>NULL))->order_by('pt.id','DESC')->limit(6)->get()->result();
        $this->data['stat']=array(
            'peternak'=>$this->db->where(array('status_verifikasi'=>'terverifikasi','deleted_at'=>NULL))->count_all_results('peternak'),
            'produk'=>$this->db->where(array('status_verifikasi'=>'disetujui','deleted_at'=>NULL))->where_in('status_ketersediaan',array('tersedia','habis'))->count_all_results('produk'),
            'kecamatan'=>$this->db->query('SELECT COUNT(DISTINCT kecamatan_id) c FROM produk WHERE status_verifikasi="disetujui" AND deleted_at IS NULL')->row()->c);
        $this->data['kecamatan']=$this->Ref_model->kecamatan();
        $this->page('public/home','SISAPI - Temukan Kebutuhan Peternakan Anda');
    }
    public function tentang(){ $this->page('public/tentang','Tentang SISAPI'); }
}
