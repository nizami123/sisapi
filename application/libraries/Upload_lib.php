<?php defined('BASEPATH') OR exit('No direct script access allowed');
/** Upload gambar aman: validasi MIME asli, ukuran, nama unik. */
class Upload_lib {
    protected $CI;
    const MAX_KB = 2048;
    public function __construct(){ $this->CI =& get_instance(); }

    /** Upload satu file dari $_FILES[$field][$i]; return nama file atau FALSE */
    public function simpan($tmp,$size,$dir,$exts=array('jpg','png','webp'),$maxKb=self::MAX_KB){
        if(!is_uploaded_file($tmp) || $size > $maxKb*1024) return FALSE;
        $mime = function_exists('finfo_open') ? finfo_file(finfo_open(FILEINFO_MIME_TYPE),$tmp) : '';
        $map = array('image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp','application/pdf'=>'pdf');
        if(!isset($map[$mime]) || !in_array($map[$mime],$exts)) return FALSE;
        if(strpos($mime,'image/')===0 && !@getimagesize($tmp)) return FALSE;
        $path = FCPATH.'assets/uploads/'.$dir.'/';
        if(!is_dir($path)) @mkdir($path,0755,TRUE);
        $name = bin2hex(random_bytes(12)).'.'.$map[$mime];
        return move_uploaded_file($tmp,$path.$name) ? $name : FALSE;
    }
    /** Multi-file; return array nama file berhasil */
    public function banyak($field,$dir,$limit){
        $out=array();
        if(empty($_FILES[$field]['name'][0])) return $out;
        foreach($_FILES[$field]['name'] as $i=>$n){
            if(count($out)>=$limit) break;
            if($_FILES[$field]['error'][$i]!==UPLOAD_ERR_OK) continue;
            $f=$this->simpan($_FILES[$field]['tmp_name'][$i],$_FILES[$field]['size'][$i],$dir);
            if($f) $out[]=$f;
        }
        return $out;
    }
    public function satu($field,$dir,$exts=array('jpg','png','webp')){
        if(empty($_FILES[$field]['name']) || $_FILES[$field]['error']!==UPLOAD_ERR_OK) return FALSE;
        return $this->simpan($_FILES[$field]['tmp_name'],$_FILES[$field]['size'],$dir,$exts);
    }
    public function hapus($dir,$file){ if($file){ $p=FCPATH.'assets/uploads/'.$dir.'/'.basename($file); if(is_file($p)) @unlink($p);} }
}
