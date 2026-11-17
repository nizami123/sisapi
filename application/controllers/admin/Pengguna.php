<?php defined('BASEPATH') OR exit('No direct script access allowed');
/** Manajemen akun administrator + ganti password sendiri */
class Pengguna extends Admin_Controller {
    public function index(){ $this->data['list']=$this->db->order_by('id')->get_where('users',array('role'=>'admin','deleted_at'=>NULL))->result(); $this->page('admin/pengguna','Manajemen Admin'); }
    public function tambah(){
        if($this->input->method()!=='post') show_404();
        $this->form_validation->set_rules('nama','Nama','required'); $this->form_validation->set_rules('email','Email','required|valid_email|is_unique[users.email]'); $this->form_validation->set_rules('password','Password','required|min_length[8]');
        if($this->form_validation->run()){
            $this->db->insert('users',array('nama'=>$this->input->post('nama',TRUE),'email'=>$this->input->post('email',TRUE),'password'=>password_hash($this->input->post('password'),PASSWORD_DEFAULT),'role'=>'admin','created_at'=>date('Y-m-d H:i:s')));
            $this->Log_model->add('admin_tambah',$this->input->post('email',TRUE)); $this->session->set_flashdata('success','Admin ditambahkan.');
        } else $this->session->set_flashdata('danger',strip_tags(validation_errors(' ','; ')));
        redirect('admin/pengguna');
    }
    public function toggle($id){
        if($this->input->method()==='post' && (int)$id!==(int)$this->data['me']['id']){ $u=$this->db->get_where('users',array('id'=>(int)$id,'role'=>'admin'))->row(); if($u) $this->db->where('id',$u->id)->update('users',array('is_active'=>$u->is_active?0:1)); }
        redirect('admin/pengguna');
    }
}
