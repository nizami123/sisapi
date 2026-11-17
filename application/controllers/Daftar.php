<?php defined('BASEPATH') OR exit('No direct script access allowed');
/** Pendaftaran mandiri peternak. Akun dibuat NONAKTIF, status 'menunggu', sampai di-acc admin Dinas. */
class Daftar extends Public_Controller {
    const MAX_PER_JAM = 5; // batas pendaftaran per IP per jam

    public function index(){
        if($this->session->userdata('uid')) redirect($this->session->userdata('role'));
        $kec = (int)$this->input->post('kecamatan_id');
        $this->data['kec_list']  = $this->Ref_model->kecamatan();
        $this->data['desa_list'] = $kec ? $this->Ref_model->desa($kec) : array();

        if($this->input->method()==='post') $this->proses();
        $this->page('public/daftar','Daftar Peternak - SISAPI');
    }

    private function proses(){
        // Honeypot: kolom tersembunyi, manusia tidak mengisinya
        if($this->input->post('website')!==NULL && trim($this->input->post('website'))!==''){ redirect('daftar'); }

        $jam = date('Y-m-d H:i:s', time()-3600);
        $n = $this->db->where(array('aksi'=>'daftar','ip'=>$this->input->ip_address(),'created_at >='=>$jam))->count_all_results('log_aktivitas');
        if($n >= self::MAX_PER_JAM){
            $this->session->set_flashdata('danger','Terlalu banyak pendaftaran dari jaringan ini. Coba lagi satu jam lagi.'); redirect('daftar');
        }

        $v = $this->form_validation;
        $v->set_rules('nama','Nama','required|max_length[150]');
        $v->set_rules('email','Email','required|valid_email|max_length[120]|is_unique[users.email]',array('is_unique'=>'Email sudah terdaftar. Silakan masuk, atau gunakan email lain.'));
        $v->set_rules('password','Password','required|min_length[8]|max_length[72]');
        $v->set_rules('password2','Ulangi password','required|matches[password]');
        $v->set_rules('whatsapp','WhatsApp','required|callback_cek_wa');
        $v->set_rules('kecamatan_id','Kecamatan','required|integer');
        $v->set_rules('desa_id','Desa','required|integer');
        $v->set_rules('alamat','Alamat','required|max_length[500]');
        $v->set_rules('nama_kelompok','Nama kelompok','max_length[150]');
        $v->set_rules('jenis_usaha','Jenis usaha','max_length[150]');
        if(!$v->run()){ $this->data['error']=strip_tags(validation_errors('<div>','</div>')); return; }

        $kec=(int)$this->input->post('kecamatan_id'); $desa=(int)$this->input->post('desa_id');
        if(!$this->db->where(array('id'=>$desa,'kecamatan_id'=>$kec,'is_active'=>1))->count_all_results('desa')){
            $this->data['error']='Desa tidak sesuai dengan kecamatan.'; return;
        }

        // Dokumen pendukung (opsional): jika dipilih tapi tidak valid, beri tahu
        $dok=NULL;
        if(!empty($_FILES['dokumen']['name'])){
            $this->load->library('Upload_lib');
            $dok=$this->upload_lib->satu('dokumen','peternak',array('jpg','png','webp','pdf'));
            if(!$dok){ $this->data['error']='Dokumen tidak valid. Gunakan JPG/PNG/WEBP/PDF maksimal 2 MB.'; return; }
        }

        $now=date('Y-m-d H:i:s'); $nama=$this->input->post('nama',TRUE); $email=trim($this->input->post('email',TRUE));
        $this->db->trans_start();
        $this->db->insert('users',array('nama'=>$nama,'email'=>$email,'password'=>password_hash($this->input->post('password'),PASSWORD_DEFAULT),
            'role'=>'peternak','is_active'=>0,'created_at'=>$now));
        $uid=$this->db->insert_id();
        $this->db->insert('peternak',array('user_id'=>$uid,'nama'=>$nama,'nama_kelompok'=>$this->input->post('nama_kelompok',TRUE),
            'whatsapp'=>wa_normalize($this->input->post('whatsapp')),'kecamatan_id'=>$kec,'desa_id'=>$desa,
            'alamat'=>$this->input->post('alamat',TRUE),'jenis_usaha'=>$this->input->post('jenis_usaha',TRUE),'dokumen'=>$dok,
            'status_verifikasi'=>'menunggu','sumber'=>'mandiri','created_at'=>$now));
        $pid=$this->db->insert_id();
        $this->db->insert('verifikasi_peternak',array('peternak_id'=>$pid,'admin_id'=>NULL,'status_lama'=>'belum','status_baru'=>'menunggu','catatan'=>'Pendaftaran mandiri','created_at'=>$now));
        $this->db->trans_complete();

        if(!$this->db->trans_status()){
            if($dok) $this->upload_lib->hapus('peternak',$dok);
            $this->data['error']='Pendaftaran gagal disimpan. Silakan coba lagi.'; return;
        }
        $this->Log_model->add('daftar',$email);
        $this->session->set_flashdata('success','Pendaftaran berhasil dikirim. Akun Anda dapat dipakai masuk setelah disetujui admin Dinas.');
        redirect('login');
    }

    public function cek_wa($v){
        if(wa_valid($v)) return TRUE;
        $this->form_validation->set_message('cek_wa','Nomor WhatsApp tidak valid (contoh 081234567890).'); return FALSE;
    }
}
