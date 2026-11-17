<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Log_model extends CI_Model {
    public function add($aksi,$ket=''){
        $this->db->insert('log_aktivitas',array(
            'user_id'=>$this->session->userdata('uid') ?: NULL,'aksi'=>$aksi,'keterangan'=>$ket,
            'ip'=>$this->input->ip_address(),'created_at'=>date('Y-m-d H:i:s')));
    }
}
