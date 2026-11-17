<?php defined('BASEPATH') OR exit('No direct script access allowed');
/** /wa/{id}: catat klik lalu arahkan ke wa.me */
class Wa extends CI_Controller {
    public function go($id){
        $this->load->model('Produk_model','pm');
        $p=$this->pm->find($id);
        if(!$p || $p->status_verifikasi!=='disetujui' || $p->pt_status!=='terverifikasi' || $p->status_ketersediaan!=='tersedia' || !wa_valid($p->whatsapp)) show_404();
        $this->load->model('Stat_model');
        $this->Stat_model->catat($p,'wa_click');
        redirect(wa_link($p->whatsapp,$p->nama),'location',302);
    }
}
