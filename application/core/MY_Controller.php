<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Public_Controller extends CI_Controller {
    protected $data = array();
    public function __construct(){
        parent::__construct();
        $this->data['kategori_menu'] = $this->Ref_model->kategori_aktif();
        $this->data['app'] = $this->Ref_model->pengaturan();
    }
    protected function page($view,$title=''){
        $this->data['title'] = $title ?: 'SISAPI';
        $this->data['content'] = $this->load->view($view,$this->data,TRUE);
        $this->load->view('layouts/public',$this->data);
    }
    /** Kondisi produk yang boleh tampil di publik (alias: p = produk, pt = peternak) */
    protected function publik_where(){
        $this->db->where('p.deleted_at IS NULL',NULL,FALSE)
                 ->where('p.status_verifikasi','disetujui')
                 ->where_in('p.status_ketersediaan',array('tersedia','habis'))
                 ->where('pt.status_verifikasi','terverifikasi')
                 ->where('pt.deleted_at IS NULL',NULL,FALSE);
    }
}

class Auth_Controller extends CI_Controller {
    protected $data = array();
    protected $role = '';
    public function __construct(){
        parent::__construct();
        if(!$this->session->userdata('uid')) redirect('login');
        if($this->role && $this->session->userdata('role') !== $this->role){
            show_error('Anda tidak memiliki akses ke halaman ini.',403,'Akses Ditolak');
        }
        $this->data['me'] = array('id'=>$this->session->userdata('uid'),'nama'=>$this->session->userdata('nama'),'role'=>$this->session->userdata('role'));
        $this->data['app'] = $this->Ref_model->pengaturan();
    }
    protected function page($view,$title=''){
        $this->data['title']=$title;
        $this->data['content']=$this->load->view($view,$this->data,TRUE);
        $this->load->view('layouts/dashboard',$this->data);
    }
}
class Admin_Controller extends Auth_Controller { protected $role='admin'; }
class Peternak_Controller extends Auth_Controller {
    protected $role='peternak';
    protected $pt;
    public function __construct(){
        parent::__construct();
        $this->pt = $this->db->get_where('peternak',array('user_id'=>$this->data['me']['id'],'deleted_at'=>NULL))->row();
        if(!$this->pt){ show_error('Profil peternak belum dibuat. Hubungi admin Dinas.',403); }
        $this->data['pt']=$this->pt;
    }
}
