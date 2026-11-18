<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Produk extends Peternak_Controller {
    // field yang dianggap "penting": perubahan pada produk disetujui butuh persetujuan ulang
    private $penting=array('nama','kategori_id','subkategori_id','harga','satuan','deskripsi','spesifikasi','jenis_ternak','ras','jenis_kelamin','umur','berat','kondisi','kecamatan_id','desa_id','alamat');
    public function __construct(){ parent::__construct(); $this->load->model('Produk_model','pm'); $this->load->library('Upload_lib'); }

    public function index($filter=''){
        $map=array('menunggu'=>'menunggu','disetujui'=>'disetujui','ditolak'=>'ditolak');
        $this->pm->query()->where('p.user_id',$this->data['me']['id']);
        if(isset($map[$filter])) $this->db->where('p.status_verifikasi',$map[$filter]);
        $this->data['list']=$this->db->where('p.deleted_at IS NULL',NULL,FALSE)->order_by('p.id','DESC')->get()->result();
        $this->data['filter']=$filter;
        $this->page('peternak/produk_list','Produk Saya');
    }
    private function milik($id){
        $p=$this->db->get_where('produk',array('id'=>(int)$id,'user_id'=>$this->data['me']['id'],'deleted_at'=>NULL))->row();
        if(!$p) show_404(); return $p;
    }
    /** Hanya peternak yang sudah diverifikasi Dinas yang boleh menambah produk baru. */
    private function wajib_terverifikasi(){
        if($this->pt->status_verifikasi==='terverifikasi') return;
        $this->session->set_flashdata('warning','Produk baru hanya bisa ditambahkan setelah akun Anda terverifikasi oleh admin Dinas. Lengkapi profil lalu ajukan verifikasi.');
        redirect('peternak/profil');
    }
    public function tambah(){ $this->wajib_terverifikasi(); $this->form_data(NULL); }
    public function edit($id){ $this->form_data($this->milik($id)); }
    private function form_data($p){
        $this->data['p']=$p; $this->data['fotos']=$p?$this->pm->fotos($p->id):array();
        $this->data['kategori']=$this->Ref_model->kategori_aktif(); $this->data['sub']=$this->Ref_model->subkategori();
        $this->data['kec_list']=$this->Ref_model->kecamatan();
        $kid=$p?$p->kecamatan_id:$this->pt->kecamatan_id; $this->data['desa_list']=$kid?$this->Ref_model->desa($kid):array();
        $this->data['def']=(object)array('kecamatan_id'=>$this->pt->kecamatan_id,'desa_id'=>$this->pt->desa_id,'alamat'=>$this->pt->alamat);
        $this->data['revisi']=$p?$this->db->get_where('produk_revisi',array('produk_id'=>$p->id,'status'=>'menunggu'))->row():NULL;
        $this->page('peternak/produk_form',$p?'Edit Produk':'Tambah Produk');
    }
    public function simpan($id=NULL){
        $p=$id?$this->milik($id):NULL;
        if(!$p) $this->wajib_terverifikasi();
        $d=$this->pm->ambil_input(); $now=date('Y-m-d H:i:s');
        $err=$this->pm->valid($d);
        if($err!==TRUE){ $this->session->set_flashdata('danger',$err); redirect($p?'peternak/produk/edit/'.$p->id:'peternak/produk/tambah'); }
        $stok=(int)$this->input->post('stok'); $ajukan=(bool)$this->input->post('ajukan');
        $ket=$this->pm->ketersediaan_input();

        if(!$p){ // ---- tambah: input peternak selalu berstatus MENUNGGU persetujuan admin
            $baru=$this->upload_lib->banyak('fotos','produk',5);
            $pid=$this->pm->buat($d,$stok,$ket,$this->data['me']['id'],$this->pt->id,'menunggu',$baru,NULL,'Produk dibuat peternak, menunggu persetujuan');
            $this->Log_model->add('produk_tambah','#'.$pid);
            $this->session->set_flashdata('success','Produk dikirim, menunggu persetujuan admin.');
            redirect('peternak/produk');
        }
        // ---- edit
        $hapus=array_map('intval',(array)$this->input->post('hapus_foto'));
        $ada=$this->db->where('produk_id',$p->id)->count_all_results('produk_foto');
        $sisa=$ada-count(array_intersect($hapus,array_column($this->pm->fotos($p->id),'id')));
        $baru=$this->upload_lib->banyak('fotos','produk',max(0,5-$sisa));
        $langsung=array('stok'=>$stok,'status_ketersediaan'=>$ket,'updated_at'=>$now);

        if($p->status_verifikasi==='disetujui'){
            $beda=FALSE; foreach($this->penting as $k) if((string)$p->$k!==(string)$d[$k]) $beda=TRUE;
            if($baru||$hapus) $beda=TRUE;
            if($beda){
                $this->db->where(array('produk_id'=>$p->id,'status'=>'menunggu'))->update('produk_revisi',array('status'=>'ditolak','catatan'=>'Digantikan revisi baru','updated_at'=>$now));
                $this->db->insert('produk_revisi',array('produk_id'=>$p->id,'data_json'=>json_encode(array('data'=>$d,'foto_baru'=>$baru,'hapus_foto'=>$hapus)),'created_at'=>$now));
                $this->pm->log_history($p->id,$this->pt->id,NULL,'disetujui','disetujui','Revisi diajukan, menunggu persetujuan ulang');
                $this->session->set_flashdata('warning','Perubahan penting menunggu persetujuan admin. Tampilan publik tetap memakai data sebelumnya. Stok & ketersediaan langsung diperbarui.');
            } else $this->session->set_flashdata('success','Produk diperbarui.');
            $this->db->where('id',$p->id)->update('produk',$langsung);
        } else {
            $upd=array_merge($d,$langsung);
            if($ajukan && in_array($p->status_verifikasi,array('draft','ditolak'))){ $upd['status_verifikasi']='menunggu'; $upd['alasan_penolakan']=NULL; $this->pm->log_history($p->id,$this->pt->id,NULL,$p->status_verifikasi,'menunggu','Diajukan ulang'); }
            $this->db->where('id',$p->id)->update('produk',$upd);
            foreach($this->pm->fotos($p->id) as $f) if(in_array((int)$f->id,$hapus)){ $this->upload_lib->hapus('produk',$f->file); $this->db->delete('produk_foto',array('id'=>$f->id)); }
            foreach($baru as $n=>$f) $this->db->insert('produk_foto',array('produk_id'=>$p->id,'file'=>$f,'urutan'=>10+$n,'created_at'=>$now));
            $this->session->set_flashdata('success','Produk disimpan.');
        }
        $this->Log_model->add('produk_edit','#'.$p->id); redirect('peternak/produk');
    }
    /** Ubah ketersediaan cepat (langsung) */
    public function ketersediaan($id){
        $p=$this->milik($id); $s=$this->input->post('status');
        if($this->input->method()==='post' && in_array($s,array('tersedia','habis','tidak_aktif'))){
            $this->db->where('id',$p->id)->update('produk',array('status_ketersediaan'=>$s,'updated_at'=>date('Y-m-d H:i:s')));
            $this->Log_model->add('ketersediaan','#'.$p->id.' -> '.$s);
        }
        redirect('peternak/produk');
    }
    public function hapus($id){
        $p=$this->milik($id);
        if($this->input->method()==='post'){ $this->db->where('id',$p->id)->update('produk',array('deleted_at'=>date('Y-m-d H:i:s'),'status_ketersediaan'=>'tidak_aktif')); $this->Log_model->add('produk_hapus','#'.$p->id); $this->session->set_flashdata('success','Produk dihapus.'); }
        redirect('peternak/produk');
    }
}
