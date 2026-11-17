<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Log extends Admin_Controller {
    public function index(){
        $this->data['list']=$this->db->select('l.*,u.nama,u.email')->from('log_aktivitas l')->join('users u','u.id=l.user_id','left')->order_by('l.id','DESC')->limit(1000)->get()->result();
        $this->page('admin/log','Log Aktivitas');
    }
}
