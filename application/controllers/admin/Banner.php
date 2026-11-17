<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Banner extends Admin_Controller {
    public function index(){ $this->data['list']=$this->db->order_by('urutan')->get('banner')->result(); $this->page('admin/banner','Banner Beranda'); }
    public function simpan($id=0){
        if($this->input->method()!=='post') show_404();
        $this->load->library('Upload_lib');
        $d=array('judul'=>$this->input->post('judul',TRUE),'deskripsi'=>$this->input->post('deskripsi',TRUE),'urutan'=>(int)$this->input->post('urutan'),'is_active'=>$this->input->post('is_active')?1:0);
        if($d['judul']===''){ $this->session->set_flashdata('danger','Judul wajib.'); redirect('admin/banner'); }
        $f=$this->upload_lib->satu('gambar','banner');
        if($id){ $old=$this->db->get_where('banner',array('id'=>(int)$id))->row(); if(!$old) show_404(); if($f){ $this->upload_lib->hapus('banner',$old->gambar); $d['gambar']=$f; } $d['updated_at']=date('Y-m-d H:i:s'); $this->db->where('id',$old->id)->update('banner',$d); }
        else { if(!$f){ $this->session->set_flashdata('danger','Gambar banner wajib (JPG/PNG/WEBP, maks 2 MB).'); redirect('admin/banner'); } $d['gambar']=$f; $d['created_at']=date('Y-m-d H:i:s'); $this->db->insert('banner',$d); }
        $this->Log_model->add('banner',$d['judul']); $this->session->set_flashdata('success','Banner disimpan.'); redirect('admin/banner');
    }
    public function hapus($id){
        if($this->input->method()==='post'){ $b=$this->db->get_where('banner',array('id'=>(int)$id))->row(); if($b){ $this->load->library('Upload_lib'); $this->upload_lib->hapus('banner',$b->gambar); $this->db->delete('banner',array('id'=>$b->id)); } }
        redirect('admin/banner');
    }
}
