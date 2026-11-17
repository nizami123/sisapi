<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Pengaturan extends Admin_Controller {
    private $kunci=array('nama_app','instansi','alamat','telepon','email','tentang');
    public function index(){ $this->page('admin/pengaturan','Pengaturan Aplikasi'); }
    public function simpan(){
        if($this->input->method()!=='post') show_404();
        foreach($this->kunci as $k) $this->db->replace('pengaturan',array('kunci'=>$k,'nilai'=>$this->input->post($k,TRUE)));
        $this->Log_model->add('pengaturan'); $this->session->set_flashdata('success','Pengaturan disimpan.'); redirect('admin/pengaturan');
    }
}
