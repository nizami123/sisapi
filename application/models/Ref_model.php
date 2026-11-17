<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Ref_model extends CI_Model {
    public function kategori_aktif(){ return $this->db->get_where('kategori',array('is_active'=>1))->result(); }
    public function subkategori($kid=NULL){
        $this->db->where('is_active',1); if($kid) $this->db->where('kategori_id',(int)$kid);
        return $this->db->order_by('nama')->get('subkategori')->result();
    }
    public function kecamatan(){ return $this->db->where('is_active',1)->order_by('nama')->get('kecamatan')->result(); }
    public function desa($kid){ return $this->db->where(array('is_active'=>1,'kecamatan_id'=>(int)$kid))->order_by('nama')->get('desa')->result(); }
    public function pengaturan(){
        $o=array(); foreach($this->db->get('pengaturan')->result() as $r) $o[$r->kunci]=$r->nilai; return $o;
    }
}
