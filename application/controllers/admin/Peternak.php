<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Peternak extends Admin_Controller {
    public function index($status=''){
        $this->db->select('pt.*,u.email,u.is_active,kc.nama kecamatan,(SELECT COUNT(*) FROM produk p WHERE p.peternak_id=pt.id AND p.deleted_at IS NULL) jml_produk',FALSE)
            ->from('peternak pt')->join('users u','u.id=pt.user_id')->join('kecamatan kc','kc.id=pt.kecamatan_id','left')->where('pt.deleted_at IS NULL',NULL,FALSE);
        if($status) $this->db->where('pt.status_verifikasi',$status);
        $this->data['list']=$this->db->order_by('pt.id','DESC')->get()->result(); $this->data['status']=$status;
        $this->page('admin/peternak_list',$status?'Verifikasi Peternak':'Data Peternak');
    }
    public function detail($id){
        $pt=$this->db->select('pt.*,u.email,kc.nama kecamatan,ds.nama desa')->from('peternak pt')->join('users u','u.id=pt.user_id')
            ->join('kecamatan kc','kc.id=pt.kecamatan_id','left')->join('desa ds','ds.id=pt.desa_id','left')->where('pt.id',(int)$id)->get()->row();
        if(!$pt) show_404();
        $this->data['pt']=$pt;
        $this->data['riwayat']=$this->db->select('v.*,u.nama admin')->from('verifikasi_peternak v')->join('users u','u.id=v.admin_id','left')->where('v.peternak_id',$pt->id)->order_by('v.id','DESC')->get()->result();
        $this->page('admin/peternak_detail','Detail Peternak');
    }
    public function tambah(){
        if($this->input->method()==='post'){
            $this->form_validation->set_rules('nama','Nama','required'); $this->form_validation->set_rules('email','Email','required|valid_email|is_unique[users.email]');
            $this->form_validation->set_rules('password','Password','required|min_length[8]');
            if($this->form_validation->run()){
                $now=date('Y-m-d H:i:s');
                $this->db->trans_start();
                $this->db->insert('users',array('nama'=>$this->input->post('nama',TRUE),'email'=>$this->input->post('email',TRUE),'password'=>password_hash($this->input->post('password'),PASSWORD_DEFAULT),'role'=>'peternak','created_at'=>$now));
                $uid=$this->db->insert_id();
                $this->db->insert('peternak',array('user_id'=>$uid,'nama'=>$this->input->post('nama',TRUE),'whatsapp'=>wa_normalize($this->input->post('whatsapp')),'created_at'=>$now));
                $this->db->trans_complete();
                $this->Log_model->add('peternak_tambah',$this->input->post('email',TRUE));
                $this->session->set_flashdata('success','Akun peternak dibuat. Minta peternak mengganti password setelah login.'); redirect('admin/peternak');
            }
            $this->session->set_flashdata('danger',strip_tags(validation_errors(' ','; ')));
        }
        $this->page('admin/peternak_tambah','Tambah Akun Peternak');
    }
    /** Ubah status verifikasi: hanya admin, hanya via POST */
    public function status($id){
        $pt=$this->db->get_where('peternak',array('id'=>(int)$id))->row(); if(!$pt) show_404();
        $baru=$this->input->post('status');
        if($this->input->method()!=='post' || !in_array($baru,array('belum','menunggu','terverifikasi','ditolak','dinonaktifkan'))) redirect('admin/peternak/detail/'.$pt->id);
        $cat=$this->input->post('catatan',TRUE);
        // Akun daftar mandiri: tetap nonaktif selama belum pernah disetujui (menunggu/ditolak). Akun buatan admin: perilaku lama.
        $aktif=1;
        if($baru==='dinonaktifkan') $aktif=0;
        elseif($pt->sumber==='mandiri' && in_array($baru,array('menunggu','ditolak'))){
            $pernah=$this->db->where(array('peternak_id'=>$pt->id,'status_baru'=>'terverifikasi'))->count_all_results('verifikasi_peternak');
            if(!$pernah) $aktif=0;
        }
        $this->db->where('id',$pt->id)->update('peternak',array('status_verifikasi'=>$baru,'catatan_verifikasi'=>$cat,'updated_at'=>date('Y-m-d H:i:s')));
        $this->db->insert('verifikasi_peternak',array('peternak_id'=>$pt->id,'admin_id'=>$this->data['me']['id'],'status_lama'=>$pt->status_verifikasi,'status_baru'=>$baru,'catatan'=>$cat,'created_at'=>date('Y-m-d H:i:s')));
        $this->db->where('id',$pt->user_id)->update('users',array('is_active'=>$aktif));
        $this->Log_model->add('verifikasi_peternak','#'.$pt->id.' '.$pt->status_verifikasi.' -> '.$baru);
        $this->session->set_flashdata('success','Status peternak diperbarui.'); redirect('admin/peternak/detail/'.$pt->id);
    }
    public function reset_password($id){
        $pt=$this->db->get_where('peternak',array('id'=>(int)$id))->row(); if(!$pt) show_404();
        if($this->input->method()==='post' && strlen($this->input->post('password'))>=8){
            $this->db->where('id',$pt->user_id)->update('users',array('password'=>password_hash($this->input->post('password'),PASSWORD_DEFAULT)));
            $this->Log_model->add('reset_password','peternak #'.$pt->id); $this->session->set_flashdata('success','Password direset.');
        } else $this->session->set_flashdata('danger','Password minimal 8 karakter.');
        redirect('admin/peternak/detail/'.$pt->id);
    }
}
