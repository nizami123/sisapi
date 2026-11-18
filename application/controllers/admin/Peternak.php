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
    /** Input peternak oleh admin Dinas: akun aktif & LANGSUNG berstatus terverifikasi (tanpa antrean). */
    public function tambah(){
        $kec=(int)$this->input->post('kecamatan_id');
        $this->data['kec_list']=$this->Ref_model->kecamatan();
        $this->data['desa_list']=$kec?$this->Ref_model->desa($kec):array();
        if($this->input->method()==='post') $this->proses_tambah();
        $this->page('admin/peternak_tambah','Tambah Peternak');
    }
    private function proses_tambah(){
        $v=$this->form_validation;
        $v->set_rules('nama','Nama','required|max_length[150]');
        $v->set_rules('email','Email','required|valid_email|max_length[120]|is_unique[users.email]',array('is_unique'=>'Email sudah terdaftar.'));
        $v->set_rules('password','Password','required|min_length[8]|max_length[72]');
        $v->set_rules('whatsapp','WhatsApp','required|callback_cek_wa');
        $v->set_rules('kecamatan_id','Kecamatan','required|integer');
        $v->set_rules('desa_id','Desa','required|integer');
        $v->set_rules('alamat','Alamat','required|max_length[500]');
        $v->set_rules('nama_kelompok','Nama kelompok','max_length[150]');
        $v->set_rules('jenis_usaha','Jenis usaha','max_length[150]');
        if(!$v->run()){ $this->data['error']=strip_tags(validation_errors(' ','; ')); return; }

        $kec=(int)$this->input->post('kecamatan_id'); $desa=(int)$this->input->post('desa_id');
        if(!$this->db->where(array('id'=>$desa,'kecamatan_id'=>$kec,'is_active'=>1))->count_all_results('desa')){
            $this->data['error']='Desa tidak sesuai dengan kecamatan.'; return;
        }
        // Foto & dokumen opsional; bila dipilih tapi tidak valid, beri tahu
        $this->load->library('Upload_lib'); $foto=NULL; $dok=NULL;
        if(!empty($_FILES['foto']['name'])){
            $foto=$this->upload_lib->satu('foto','peternak');
            if(!$foto){ $this->data['error']='Foto tidak valid. Gunakan JPG/PNG/WEBP maksimal 2 MB.'; return; }
        }
        if(!empty($_FILES['dokumen']['name'])){
            $dok=$this->upload_lib->satu('dokumen','peternak',array('jpg','png','webp','pdf'));
            if(!$dok){ if($foto) $this->upload_lib->hapus('peternak',$foto); $this->data['error']='Dokumen tidak valid. Gunakan JPG/PNG/WEBP/PDF maksimal 2 MB.'; return; }
        }

        $now=date('Y-m-d H:i:s'); $nama=$this->input->post('nama',TRUE); $email=trim($this->input->post('email',TRUE));
        $this->db->trans_start();
        $this->db->insert('users',array('nama'=>$nama,'email'=>$email,'password'=>password_hash($this->input->post('password'),PASSWORD_DEFAULT),'role'=>'peternak','is_active'=>1,'created_at'=>$now));
        $uid=$this->db->insert_id();
        $this->db->insert('peternak',array('user_id'=>$uid,'nama'=>$nama,'nama_kelompok'=>$this->input->post('nama_kelompok',TRUE),
            'foto'=>$foto,'deskripsi'=>$this->input->post('deskripsi',TRUE),'alamat'=>$this->input->post('alamat',TRUE),
            'kecamatan_id'=>$kec,'desa_id'=>$desa,'whatsapp'=>wa_normalize($this->input->post('whatsapp')),
            'jenis_usaha'=>$this->input->post('jenis_usaha',TRUE),'dokumen'=>$dok,
            'status_verifikasi'=>'terverifikasi','catatan_verifikasi'=>'Diinput oleh admin Dinas','sumber'=>'admin','created_at'=>$now));
        $pid=$this->db->insert_id();
        $this->db->insert('verifikasi_peternak',array('peternak_id'=>$pid,'admin_id'=>$this->data['me']['id'],'status_lama'=>'belum','status_baru'=>'terverifikasi','catatan'=>'Diinput oleh admin Dinas, langsung terverifikasi','created_at'=>$now));
        $this->db->trans_complete();

        if(!$this->db->trans_status()){
            if($foto) $this->upload_lib->hapus('peternak',$foto); if($dok) $this->upload_lib->hapus('peternak',$dok);
            $this->data['error']='Data gagal disimpan. Silakan coba lagi.'; return;
        }
        $this->Log_model->add('peternak_tambah',$email);
        $this->session->set_flashdata('success','Peternak ditambahkan dan langsung Terverifikasi. Berikan email & password awal kepada peternak, lalu minta ia menggantinya setelah login.');
        redirect('admin/peternak/detail/'.$pid);
    }
    public function cek_wa($v){
        if(wa_valid($v)) return TRUE;
        $this->form_validation->set_message('cek_wa','Nomor WhatsApp tidak valid (contoh 081234567890).'); return FALSE;
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
