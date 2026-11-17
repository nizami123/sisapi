<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Auth extends CI_Controller {
    public function login(){
        if($this->session->userdata('uid')) redirect($this->session->userdata('role'));
        $data['app']=$this->Ref_model->pengaturan();
        if($this->input->method()==='post'){
            $tries = (int)$this->session->userdata('tries');
            if($tries>=5 && time()-(int)$this->session->userdata('tries_at')<300){
                $this->session->set_flashdata('danger','Terlalu banyak percobaan. Coba lagi 5 menit lagi.');
                redirect('login');
            }
            $email=trim($this->input->post('email',TRUE)); $pw=(string)$this->input->post('password');
            $u=$this->db->get_where('users',array('email'=>$email,'is_active'=>1,'deleted_at'=>NULL))->row();
            if($u && password_verify($pw,$u->password)){
                $this->session->sess_regenerate(TRUE);
                $this->session->set_userdata(array('uid'=>$u->id,'nama'=>$u->nama,'role'=>$u->role,'tries'=>0));
                $this->db->where('id',$u->id)->update('users',array('last_login'=>date('Y-m-d H:i:s')));
                $this->Log_model->add('login',$u->email);
                redirect($u->role);
            }
            $this->session->set_userdata(array('tries'=>$tries+1,'tries_at'=>time()));
            $this->Log_model->add('login_gagal',$email);
            $this->session->set_flashdata('danger','Email atau password salah.');
            redirect('login');
        }
        $this->load->view('public/login',$data);
    }
    public function logout(){ $this->Log_model->add('logout'); $this->session->sess_destroy(); redirect('login'); }
}
