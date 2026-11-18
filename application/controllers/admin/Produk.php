<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Produk extends Admin_Controller {
    public function __construct(){ parent::__construct(); $this->load->model('Produk_model','pm'); }
    public function index($status=''){
        $this->pm->query()->where('p.deleted_at IS NULL',NULL,FALSE);
        if(in_array($status,array('menunggu','disetujui','ditolak','draft'))) $this->db->where('p.status_verifikasi',$status);
        $this->data['list']=$this->db->order_by('p.id','DESC')->get()->result(); $this->data['status']=$status;
        $this->page('admin/produk_list',$status==='menunggu'?'Verifikasi Produk':'Data Produk');
    }
    /** Form input produk oleh admin Dinas untuk peternak tertentu. ?peternak=ID untuk memilih peternak di awal. */
    public function tambah(){
        $sel=(int)$this->input->get('peternak'); $def=(object)array('kecamatan_id'=>NULL,'desa_id'=>NULL,'alamat'=>'');
        $this->data['peternak_list']=$this->db->select('pt.id,pt.nama,pt.status_verifikasi,pt.kecamatan_id,pt.desa_id,pt.alamat,kc.nama kecamatan',FALSE)
            ->from('peternak pt')->join('kecamatan kc','kc.id=pt.kecamatan_id','left')
            ->where('pt.deleted_at IS NULL',NULL,FALSE)->where('pt.status_verifikasi !=','dinonaktifkan')->order_by('pt.nama')->get()->result();
        foreach($this->data['peternak_list'] as $r) if((int)$r->id===$sel){ $def=(object)array('kecamatan_id'=>$r->kecamatan_id,'desa_id'=>$r->desa_id,'alamat'=>$r->alamat); }
        $this->data['sel']=$sel; $this->data['def']=$def; $this->data['p']=NULL; $this->data['fotos']=array();
        $this->data['kategori']=$this->Ref_model->kategori_aktif(); $this->data['sub']=$this->Ref_model->subkategori();
        $this->data['kec_list']=$this->Ref_model->kecamatan();
        $this->data['desa_list']=$def->kecamatan_id?$this->Ref_model->desa($def->kecamatan_id):array();
        $this->page('admin/produk_tambah','Tambah Produk');
    }
    /** Simpan produk input admin: LANGSUNG berstatus disetujui (tanpa antrean verifikasi). */
    public function simpan(){
        if($this->input->method()!=='post') redirect('admin/produk/tambah');
        $pt=$this->db->get_where('peternak',array('id'=>(int)$this->input->post('peternak_id'),'deleted_at'=>NULL))->row();
        if(!$pt || $pt->status_verifikasi==='dinonaktifkan'){ $this->session->set_flashdata('danger','Pilih peternak (penjual) yang valid.'); redirect('admin/produk/tambah'); }
        $d=$this->pm->ambil_input(); $err=$this->pm->valid($d);
        if($err!==TRUE){ $this->session->set_flashdata('danger',$err); redirect('admin/produk/tambah?peternak='.$pt->id); }
        $this->load->library('Upload_lib'); $fotos=$this->upload_lib->banyak('fotos','produk',5);
        $pid=$this->pm->buat($d,(int)$this->input->post('stok'),$this->pm->ketersediaan_input(),$pt->user_id,$pt->id,'disetujui',$fotos,$this->data['me']['id'],'Diinput oleh admin Dinas, langsung disetujui');
        $this->Log_model->add('admin_produk_tambah','#'.$pid.' peternak #'.$pt->id);
        $this->session->set_flashdata('success','Produk ditambahkan dan langsung berstatus Disetujui.');
        if($pt->status_verifikasi!=='terverifikasi') $this->session->set_flashdata('warning','Peternak ini belum terverifikasi, jadi produk belum tayang di katalog publik sampai peternaknya diverifikasi.');
        redirect('admin/produk/detail/'.$pid);
    }
    public function detail($id){
        $p=$this->pm->find($id); if(!$p) show_404();
        $this->data['p']=$p; $this->data['fotos']=$this->pm->fotos($p->id);
        $rev=$this->db->get_where('produk_revisi',array('produk_id'=>$p->id,'status'=>'menunggu'))->row();
        $this->data['revisi']=$rev?json_decode($rev->data_json,TRUE):NULL; $this->data['rev_row']=$rev;
        $this->data['riwayat']=$this->db->select('v.*,u.nama admin')->from('verifikasi_produk v')->join('users u','u.id=v.admin_id','left')->where('v.produk_id',$p->id)->order_by('v.id','DESC')->get()->result();
        $this->page('admin/produk_detail','Verifikasi Produk');
    }
    /** keputusan: setuju | tolak (produk), setuju_revisi | tolak_revisi */
    public function keputusan($id){
        $p=$this->pm->find($id); if(!$p || $this->input->method()!=='post') show_404();
        $aksi=$this->input->post('aksi'); $cat=$this->input->post('catatan',TRUE); $now=date('Y-m-d H:i:s'); $aid=$this->data['me']['id'];
        if(in_array($aksi,array('tolak','tolak_revisi')) && trim($cat)===''){ $this->session->set_flashdata('danger','Catatan wajib diisi saat menolak.'); redirect('admin/produk/detail/'.$p->id); }
        if($aksi==='setuju' || $aksi==='tolak'){
            $baru=$aksi==='setuju'?'disetujui':'ditolak';
            $this->db->where('id',$p->id)->update('produk',array('status_verifikasi'=>$baru,'alasan_penolakan'=>$aksi==='tolak'?$cat:NULL,'updated_at'=>$now));
            $this->pm->log_history($p->id,$p->peternak_id,$aid,$p->status_verifikasi,$baru,$cat);
        } else {
            $rev=$this->db->get_where('produk_revisi',array('produk_id'=>$p->id,'status'=>'menunggu'))->row(); if(!$rev){ redirect('admin/produk/detail/'.$p->id); }
            $j=json_decode($rev->data_json,TRUE);
            if($aksi==='setuju_revisi'){
                $this->db->where('id',$p->id)->update('produk',array_merge($j['data'],array('updated_at'=>$now)));
                $this->load->library('Upload_lib');
                foreach($this->pm->fotos($p->id) as $f) if(in_array((int)$f->id,(array)$j['hapus_foto'])){ $this->upload_lib->hapus('produk',$f->file); $this->db->delete('produk_foto',array('id'=>$f->id)); }
                foreach((array)$j['foto_baru'] as $n=>$f) $this->db->insert('produk_foto',array('produk_id'=>$p->id,'file'=>$f,'urutan'=>20+$n,'created_at'=>$now));
                $this->db->where('id',$rev->id)->update('produk_revisi',array('status'=>'disetujui','catatan'=>$cat,'updated_at'=>$now));
                $this->pm->log_history($p->id,$p->peternak_id,$aid,'disetujui','disetujui','Revisi disetujui. '.$cat);
            } elseif($aksi==='tolak_revisi'){
                $this->load->library('Upload_lib'); foreach((array)$j['foto_baru'] as $f) $this->upload_lib->hapus('produk',$f);
                $this->db->where('id',$rev->id)->update('produk_revisi',array('status'=>'ditolak','catatan'=>$cat,'updated_at'=>$now));
                $this->pm->log_history($p->id,$p->peternak_id,$aid,'disetujui','disetujui','Revisi ditolak. '.$cat);
            }
        }
        $this->Log_model->add('verifikasi_produk','#'.$p->id.' '.$aksi);
        $this->session->set_flashdata('success','Keputusan disimpan.'); redirect('admin/produk/detail/'.$p->id);
    }
    public function ketersediaan($id){
        $s=$this->input->post('status');
        if($this->input->method()==='post' && in_array($s,array('tersedia','habis','tidak_aktif'))){
            $this->db->where('id',(int)$id)->update('produk',array('status_ketersediaan'=>$s,'updated_at'=>date('Y-m-d H:i:s')));
            $this->Log_model->add('admin_ketersediaan','#'.(int)$id.' -> '.$s);
        }
        redirect('admin/produk/detail/'.(int)$id);
    }
    public function unggulan($id){
        if($this->input->method()==='post'){
            $p=$this->db->get_where('produk',array('id'=>(int)$id))->row(); if($p){
                $this->db->where('id',$p->id)->update('produk',array('is_unggulan'=>$p->is_unggulan?0:1));
                $this->Log_model->add('unggulan','#'.$p->id);
            }
        }
        redirect($this->input->post('back')==='list'?'admin/unggulan':'admin/produk/detail/'.(int)$id);
    }
    public function hapus($id){
        if($this->input->method()==='post'){ $this->db->where('id',(int)$id)->update('produk',array('deleted_at'=>date('Y-m-d H:i:s'),'status_ketersediaan'=>'tidak_aktif')); $this->Log_model->add('admin_hapus_produk','#'.(int)$id); }
        redirect('admin/produk');
    }
}
