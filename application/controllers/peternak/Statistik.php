<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Statistik extends Peternak_Controller {
    public function index(){
        $this->load->model('Stat_model'); $uid=$this->data['me']['id'];
        $this->data['harian']=$this->Stat_model->harian($uid); $this->data['periode']=$this->Stat_model->ringkas_periode($uid);
        $this->data['top_view']=$this->Stat_model->top('view',$uid,10); $this->data['top_wa']=$this->Stat_model->top('wa',$uid,10);
        $this->page('peternak/statistik','Statistik Produk');
    }
}
