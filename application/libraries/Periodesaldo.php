<?php


class Periodesaldo {

    protected $CI;

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->model('m_bukubesar');
    }

    public function get_start_periode()
    {
        $where = ['setting_name'=>"start_periode_acc", 'status'=>1];
        $periode = $this->CI->m_bukubesar->get_setting_start_periode_acc($where);
        return $periode;
    }

    // function get_saldo_awal_by_posisi($tgl_sampai,$kode_coa,$posisi)
    // {
    //     $start = $this->get_start_periode();
    //     $tgl_dari = date("Y-m-d 00:00:00", strtotime($start)); // example 202501-01 00:00:00
    //     $tgl_sampai = date("Y-m-d 23:59:59", strtotime("-1 day",  strtotime($tgl_sampai))); // tgl smpai - 1

    //     $where = ['je.tanggal_dibuat >='=> $tgl_dari, 'je.tanggal_dibuat <=' => $tgl_sampai, 'coa.kode_coa' => $kode_coa, 'jei.posisi'=> $posisi, 'je.status'=>'posted'];
    //     $result = $this->CI->m_bukubesar->get_total_nominal_posisi_by_coa($where);
    //     return $result->total_nominal ?? 0;
        
    // }
}