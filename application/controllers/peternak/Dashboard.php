<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Dashboard extends Peternak_Controller {
    public function index(){
        $uid=$this->data['me']['id']; $this->load->model('Stat_model');
        $c=function($w) use($uid){ return $this->db->where('user_id',$uid)->where('deleted_at IS NULL',NULL,FALSE)->where($w)->count_all_results('produk'); };
        $this->data['aktif']=$c(array('status_verifikasi'=>'disetujui','status_ketersediaan'=>'tersedia'));
        $this->data['menunggu']=$c(array('status_verifikasi'=>'menunggu'));
        $this->data['ditolak']=$c(array('status_verifikasi'=>'ditolak'));
        $r=$this->db->select('COALESCE(SUM(total_view),0) v, COALESCE(SUM(total_wa),0) w',FALSE)->get_where('produk',array('user_id'=>$uid,'deleted_at'=>NULL))->row();
        $this->data['views']=$r->v; $this->data['wa']=$r->w;
        $this->data['top_view']=$this->Stat_model->top('view',$uid); $this->data['top_wa']=$this->Stat_model->top('wa',$uid);
        $this->page('peternak/dashboard','Dashboard');
    }
}
