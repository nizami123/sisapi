<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Profil extends Peternak_Controller {
    public function index(){
        $this->data['kec_list']=$this->Ref_model->kecamatan();
        $this->data['desa_list']=$this->pt->kecamatan_id?$this->Ref_model->desa($this->pt->kecamatan_id):array();
        $this->page('peternak/profil','Profil Saya');
    }
    public function simpan(){
        $this->form_validation->set_rules('nama','Nama','required|max_length[150]');
        $this->form_validation->set_rules('whatsapp','WhatsApp','required|callback_cek_wa');
        $this->form_validation->set_rules('kecamatan_id','Kecamatan','required|integer');
        $this->form_validation->set_rules('desa_id','Desa','required|integer');
        $this->form_validation->set_rules('alamat','Alamat','required');
        if(!$this->form_validation->run()){ $this->session->set_flashdata('danger',strip_tags(validation_errors(' ',' '))); redirect('peternak/profil'); }
        $kec=(int)$this->input->post('kecamatan_id'); $desa=(int)$this->input->post('desa_id');
        if(!$this->db->where(array('id'=>$desa,'kecamatan_id'=>$kec))->count_all_results('desa')){ $this->session->set_flashdata('danger','Desa tidak sesuai kecamatan.'); redirect('peternak/profil'); }
        $this->load->library('Upload_lib');
        $d=array('nama'=>$this->input->post('nama',TRUE),'nama_kelompok'=>$this->input->post('nama_kelompok',TRUE),
            'deskripsi'=>$this->input->post('deskripsi',TRUE),'alamat'=>$this->input->post('alamat',TRUE),
            'kecamatan_id'=>$kec,'desa_id'=>$desa,'whatsapp'=>wa_normalize($this->input->post('whatsapp')),
            'jenis_usaha'=>$this->input->post('jenis_usaha',TRUE),'updated_at'=>date('Y-m-d H:i:s'));
        if($f=$this->upload_lib->satu('foto','peternak')){ $this->upload_lib->hapus('peternak',$this->pt->foto); $d['foto']=$f; }
        if($f=$this->upload_lib->satu('dokumen','peternak',array('jpg','png','webp','pdf'))){ $this->upload_lib->hapus('peternak',$this->pt->dokumen); $d['dokumen']=$f; }
        // status verifikasi tidak boleh diubah dari sini selain mengajukan
        if($this->input->post('ajukan') && in_array($this->pt->status_verifikasi,array('belum','ditolak'))){
            $d['status_verifikasi']='menunggu';
            $this->db->insert('verifikasi_peternak',array('peternak_id'=>$this->pt->id,'admin_id'=>NULL,'status_lama'=>$this->pt->status_verifikasi,'status_baru'=>'menunggu','catatan'=>'Diajukan oleh peternak','created_at'=>date('Y-m-d H:i:s')));
        }
        $this->db->where('id',$this->pt->id)->update('peternak',$d);
        $this->Log_model->add('profil_update','peternak #'.$this->pt->id);
        $this->session->set_flashdata('success','Profil disimpan.'); redirect('peternak/profil');
    }
    public function cek_wa($v){ if(wa_valid($v)) return TRUE; $this->form_validation->set_message('cek_wa','Nomor WhatsApp tidak valid (contoh 081234567890).'); return FALSE; }
}
