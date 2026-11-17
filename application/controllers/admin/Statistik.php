<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Statistik extends Admin_Controller {
    public function index(){
        $this->load->model('Stat_model');
        $this->data['harian']=$this->Stat_model->harian(NULL,60); $this->data['periode']=$this->Stat_model->ringkas_periode();
        $this->data['top_view']=$this->Stat_model->top('view',NULL,10); $this->data['top_wa']=$this->Stat_model->top('wa',NULL,10);
        $this->data['per_kat']=$this->db->query("SELECT k.nama, SUM(s.tipe='view') v, SUM(s.tipe='wa_click') w FROM statistik_produk s JOIN kategori k ON k.id=s.kategori_id GROUP BY k.id")->result();
        $this->data['per_kec']=$this->db->query("SELECT kc.nama, SUM(s.tipe='view') v, SUM(s.tipe='wa_click') w FROM statistik_produk s JOIN kecamatan kc ON kc.id=s.kecamatan_id GROUP BY kc.id ORDER BY v DESC LIMIT 15")->result();
        $this->page('admin/statistik','Statistik Pengunjung & Produk');
    }
}
