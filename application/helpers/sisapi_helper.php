<?php defined('BASEPATH') OR exit('No direct script access allowed');

function e($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function rupiah($n){ return 'Rp '.number_format((float)$n,0,',','.'); }
function tgl($d){ return $d ? date('d M Y H:i', strtotime($d)) : '-'; }

/** Normalisasi nomor HP Indonesia ke format 62xxxxxxxx */
function wa_normalize($no){
    $no = preg_replace('/[^0-9+]/','',(string)$no);
    $no = ltrim($no,'+');
    if (strpos($no,'0')===0) $no = '62'.substr($no,1);
    elseif (strpos($no,'8')===0) $no = '62'.$no;
    return $no;
}
function wa_valid($no){ return (bool)preg_match('/^628[0-9]{8,12}$/', wa_normalize($no)); }
function wa_link($no,$produk){
    $msg = "Assalamu'alaikum, saya tertarik dengan produk {$produk} yang saya lihat di SISAPI. Apakah produk tersebut masih tersedia?";
    return 'https://wa.me/'.wa_normalize($no).'?text='.rawurlencode($msg);
}
function foto_url($file,$dir='produk'){
    return $file ? base_url("assets/uploads/$dir/".$file) : base_url('assets/img/noimage.svg');
}
function badge_ketersediaan($s){
    $m=array('tersedia'=>array('success','Tersedia'),'habis'=>array('warning','Habis'),'tidak_aktif'=>array('secondary','Tidak Aktif'));
    $x=isset($m[$s])?$m[$s]:array('secondary',$s);
    return '<span class="badge bg-'.$x[0].'">'.$x[1].'</span>';
}
function badge_verif($s){
    $m=array('draft'=>array('secondary','Draft'),'menunggu'=>array('warning','Menunggu'),'disetujui'=>array('success','Disetujui'),'ditolak'=>array('danger','Ditolak'),
      'belum'=>array('secondary','Belum Diverifikasi'),'terverifikasi'=>array('success','Terverifikasi'),'dinonaktifkan'=>array('dark','Dinonaktifkan'));
    $x=isset($m[$s])?$m[$s]:array('secondary',$s);
    return '<span class="badge bg-'.$x[0].'">'.$x[1].'</span>';
}
function flash_html(){
    $CI=&get_instance(); $o='';
    foreach(array('success','danger','warning') as $t){
        if($m=$CI->session->flashdata($t)) $o.='<div class="alert alert-'.$t.' alert-dismissible fade show">'.e($m).'<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
    }
    return $o;
}

function csrf_field(){
    $CI=&get_instance();
    return '<input type="hidden" name="'.$CI->security->get_csrf_token_name().'" value="'.$CI->security->get_csrf_hash().'">';
}
