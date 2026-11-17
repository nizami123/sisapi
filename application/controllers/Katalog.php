<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Katalog extends Public_Controller {
    const PER_PAGE = 12;
    public function __construct(){ parent::__construct(); $this->load->model('Produk_model','pm'); $this->load->library('pagination'); }

    private function listing($fixed=array(),$judul='Katalog Produk'){
        $g = array_merge($this->input->get(NULL,TRUE) ?: array(), $fixed);
        $off = max(0,(int)$this->input->get('per_page'));
        // total baris (tanpa select berat / order)
        $this->publik_where(); $this->db->from('produk p')->join('peternak pt','pt.id=p.peternak_id');
        $this->pm_count_filter($g);
        $total = $this->db->count_all_results();
        // data halaman ini
        $this->publik_where(); $this->pm->query(); $this->pm->filter($g);
        $this->db->limit(self::PER_PAGE,$off);
        $this->data['produk'] = $this->db->get()->result();
        $this->pagination->initialize(array(
            'base_url'=>current_url().'?', 'total_rows'=>$total,'per_page'=>self::PER_PAGE,
            'page_query_string'=>TRUE,'query_string_segment'=>'per_page','reuse_query_string'=>TRUE,
            'full_tag_open'=>'<ul class="pagination justify-content-center">','full_tag_close'=>'</ul>',
            'num_tag_open'=>'<li class="page-item">','num_tag_close'=>'</li>','cur_tag_open'=>'<li class="page-item active"><a class="page-link">','cur_tag_close'=>'</a></li>',
            'next_tag_open'=>'<li class="page-item">','next_tag_close'=>'</li>','prev_tag_open'=>'<li class="page-item">','prev_tag_close'=>'</li>',
            'first_tag_open'=>'<li class="page-item">','first_tag_close'=>'</li>','last_tag_open'=>'<li class="page-item">','last_tag_close'=>'</li>',
            'attributes'=>array('class'=>'page-link'),'next_link'=>'&raquo;','prev_link'=>'&laquo;'));
        $this->data['paging']=$this->pagination->create_links();
        $this->data['total']=$total; $this->data['g']=$g; $this->data['judul']=$judul;
        $this->data['sub_list']=!empty($g['kategori'])?$this->Ref_model->subkategori($g['kategori']):array();
        $this->data['kec_list']=$this->Ref_model->kecamatan();
        $this->data['desa_list']=!empty($g['kec'])?$this->Ref_model->desa($g['kec']):array();
        $this->page('public/katalog',$judul.' - SISAPI');
    }
    /** filter-only (tanpa order/select) untuk count */
    private function pm_count_filter($g){
        if(!empty($g['q'])) $this->db->like('p.nama',$g['q']);
        foreach(array('kategori'=>'p.kategori_id','sub'=>'p.subkategori_id','kec'=>'p.kecamatan_id','desa'=>'p.desa_id') as $k=>$c) if(!empty($g[$k])) $this->db->where($c,(int)$g[$k]);
        if(isset($g['hmin']) && $g['hmin']!=='') $this->db->where('p.harga >=',(float)$g['hmin']);
        if(isset($g['hmax']) && $g['hmax']!=='') $this->db->where('p.harga <=',(float)$g['hmax']);
        if(!empty($g['status']) && in_array($g['status'],array('tersedia','habis'))) $this->db->where('p.status_ketersediaan',$g['status']);
    }
    public function index(){ $this->listing(); }
    public function kategori($slug){
        $k=$this->db->get_where('kategori',array('slug'=>$slug,'is_active'=>1))->row(); if(!$k) show_404();
        $this->listing(array('kategori'=>$k->id),$k->nama);
    }
    public function kecamatan($id){
        $k=$this->db->get_where('kecamatan',array('id'=>(int)$id))->row(); if(!$k) show_404();
        $this->listing(array('kec'=>$k->id),'Produk di Kecamatan '.$k->nama);
    }
    public function desa($id){
        $d=$this->db->select('d.*,k.nama kec')->join('kecamatan k','k.id=d.kecamatan_id')->get_where('desa d',array('d.id'=>(int)$id))->row(); if(!$d) show_404();
        $this->listing(array('kec'=>$d->kecamatan_id,'desa'=>$d->id),'Produk di Desa '.$d->nama.', '.$d->kec);
    }
    public function detail($id){
        $this->publik_where(); $p=$this->pm->query()->where('p.id',(int)$id)->get()->row();
        if(!$p) show_404();
        $this->load->model('Stat_model'); $this->Stat_model->catat($p,'view');
        $this->data['p']=$p; $this->data['fotos']=$this->pm->fotos($p->id);
        $this->publik_where(); $this->pm->query()->where('p.peternak_id',$p->peternak_id)->where('p.id !=',$p->id)->limit(4);
        $this->data['lain']=$this->db->get()->result();
        $this->page('public/detail',$p->nama.' - SISAPI');
    }
    public function peternak(){
        $kec=(int)$this->input->get('kec');
        $this->db->select('pt.*,kc.nama kecamatan,ds.nama desa',FALSE)->from('peternak pt')
            ->join('kecamatan kc','kc.id=pt.kecamatan_id','left')->join('desa ds','ds.id=pt.desa_id','left')
            ->where(array('pt.status_verifikasi'=>'terverifikasi','pt.deleted_at'=>NULL));
        if($kec) $this->db->where('pt.kecamatan_id',$kec);
        $this->data['list']=$this->db->order_by('pt.nama')->get()->result();
        $this->data['kec_list']=$this->Ref_model->kecamatan(); $this->data['kec']=$kec;
        $this->page('public/peternak_list','Peternak Terverifikasi - SISAPI');
    }
    public function profil_peternak($id){
        $pt=$this->db->select('pt.*,kc.nama kecamatan,ds.nama desa')->from('peternak pt')
            ->join('kecamatan kc','kc.id=pt.kecamatan_id','left')->join('desa ds','ds.id=pt.desa_id','left')
            ->where(array('pt.id'=>(int)$id,'pt.deleted_at'=>NULL))->where_in('pt.status_verifikasi',array('terverifikasi'))->get()->row();
        if(!$pt) show_404();
        $this->publik_where(); $this->pm->query()->where('p.peternak_id',$pt->id)->order_by('p.created_at','DESC');
        $this->data['produk']=$this->db->get()->result(); $this->data['pt']=$pt;
        $this->page('public/peternak_profil',$pt->nama.' - SISAPI');
    }
    public function api_desa($kid){ $this->output->set_content_type('application/json')->set_output(json_encode($this->Ref_model->desa($kid))); }
    public function api_sub($kid){ $this->output->set_content_type('application/json')->set_output(json_encode($this->Ref_model->subkategori($kid))); }
}
