<?php defined('BASEPATH') OR exit('No direct script access allowed');
/** CRUD generik: kategori, subkategori, kecamatan, desa (whitelist) */
class Master extends Admin_Controller {
    private $cfg=array(
      'kategori'=>array('judul'=>'Kategori','kolom'=>array('nama'),'parent'=>NULL),
      'subkategori'=>array('judul'=>'Subkategori','kolom'=>array('nama'),'parent'=>'kategori_id','parent_tbl'=>'kategori'),
      'kecamatan'=>array('judul'=>'Data Kecamatan','kolom'=>array('nama'),'parent'=>NULL),
      'desa'=>array('judul'=>'Data Desa','kolom'=>array('nama'),'parent'=>'kecamatan_id','parent_tbl'=>'kecamatan'));
    private function c($t){ if(!isset($this->cfg[$t])) show_404(); return $this->cfg[$t]; }
    public function index($t){
        $c=$this->c($t); $pf=(int)$this->input->get('parent');
        if($c['parent']){ $this->db->select("m.*, pr.nama AS parent_nama",FALSE)->from("$t m")->join($c['parent_tbl'].' pr','pr.id=m.'.$c['parent']);
            if($pf) $this->db->where('m.'.$c['parent'],$pf);
            $this->db->order_by('pr.nama')->order_by('m.nama'); $this->data['list']=$this->db->limit(1500)->get()->result();
            $this->data['parents']=$this->db->order_by('nama')->get($c['parent_tbl'])->result();
        } else { $this->data['list']=$this->db->order_by('id')->get($t)->result(); $this->data['parents']=array(); }
        $this->data['t']=$t; $this->data['c']=$c; $this->data['pf']=$pf;
        $this->page('admin/master',$c['judul']);
    }
    public function simpan($t,$id=0){
        $c=$this->c($t); if($this->input->method()!=='post') show_404();
        $nama=trim($this->input->post('nama',TRUE)); if($nama===''){ $this->session->set_flashdata('danger','Nama wajib diisi.'); redirect("admin/master/$t"); }
        $d=array('nama'=>$nama,'is_active'=>$this->input->post('is_active')?1:0);
        if($t==='kategori'){ $d['slug']=url_title($nama,'-',TRUE); }
        if($c['parent']){ $pid=(int)$this->input->post('parent'); if(!$this->db->where('id',$pid)->count_all_results($c['parent_tbl'])){ $this->session->set_flashdata('danger','Induk tidak valid.'); redirect("admin/master/$t"); } $d[$c['parent']]=$pid; }
        if($id){ $d['updated_at']=date('Y-m-d H:i:s'); $this->db->where('id',(int)$id)->update($t,$d); }
        else { $d['created_at']=date('Y-m-d H:i:s'); $this->db->insert($t,$d); }
        $this->Log_model->add("master_$t",$nama); $this->session->set_flashdata('success','Data disimpan.');
        redirect("admin/master/$t".($c['parent']&&!empty($d[$c['parent']])?'?parent='.$d[$c['parent']]:''));
    }
    /** nonaktifkan / aktifkan (tidak menghapus fisik agar relasi aman) */
    public function toggle($t,$id){
        $this->c($t);
        if($this->input->method()==='post'){ $r=$this->db->get_where($t,array('id'=>(int)$id))->row(); if($r) $this->db->where('id',$r->id)->update($t,array('is_active'=>$r->is_active?0:1)); }
        redirect($this->input->server('HTTP_REFERER')?:"admin/master/$t");
    }
}
