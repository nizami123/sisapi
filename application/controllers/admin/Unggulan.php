<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Unggulan extends Admin_Controller {
    public function index(){
        $this->load->model('Produk_model','pm');
        $this->pm->query()->where('p.deleted_at IS NULL',NULL,FALSE)->where('p.status_verifikasi','disetujui');
        $this->data['list']=$this->db->order_by('p.is_unggulan','DESC')->order_by('p.id','DESC')->get()->result();
        $this->page('admin/unggulan','Produk Unggulan');
    }
}
