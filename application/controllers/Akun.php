<?php defined('BASEPATH') OR exit('No direct script access allowed');
/** Ganti password sendiri (admin & peternak) */
class Akun extends Auth_Controller {
    public function password(){
        if($this->input->method()==='post'){
            $u=$this->db->get_where('users',array('id'=>$this->data['me']['id']))->row();
            $new=(string)$this->input->post('baru');
            if(!password_verify((string)$this->input->post('lama'),$u->password)) $this->session->set_flashdata('danger','Password lama salah.');
            elseif(strlen($new)<8 || $new!==$this->input->post('konfirmasi')) $this->session->set_flashdata('danger','Password baru minimal 8 karakter dan harus sama dengan konfirmasi.');
            else { $this->db->where('id',$u->id)->update('users',array('password'=>password_hash($new,PASSWORD_DEFAULT),'updated_at'=>date('Y-m-d H:i:s'))); $this->Log_model->add('ganti_password'); $this->session->set_flashdata('success','Password diganti.'); }
            redirect('akun/password');
        }
        $this->page('akun_password','Ganti Password');
    }
}
