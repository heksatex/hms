<?php defined('BASEPATH') or exit ('No Direct Script Acces Allowed');

/**
 * 
 */

class M_bukubesar extends CI_Model
{
  	public function __construct()
	{
		parent::__construct();
		$this->load->database();
        $this->load->library('periodesaldo');

	}

    function query_entries($tgldari,$tglsampai)
    {
        if(isset($tgldari) AND isset($tglsampai)){
            $tgl_dari = date("Y-m-d H:i:s", strtotime($tgldari));
            $tglsampai = date("Y-m-d 23:59:59", strtotime($tglsampai));
            $this->db->where('je.tanggal_dibuat >=',$tgl_dari);
            $this->db->where('je.tanggal_dibuat <=',$tglsampai);
        }
        $this->db->where('je.status','posted');
        $this->db->select("jei.posisi, jei.kode_coa,  IFNULL(SUM(CASE WHEN jei.posisi = 'D' THEN jei.nominal ELSE 0 END),0) AS total_debit,   IFNULL(SUM(CASE WHEN jei.posisi = 'C' THEN jei.nominal ELSE 0 END),0) AS total_credit");
        $this->db->from("acc_jurnal_entries_items jei");
        $this->db->join("acc_jurnal_entries je",'je.kode = jei.kode');
        $this->db->group_by('jei.kode_coa');
        $query = $this->db->get_compiled_select();
        return $query;
    }

    // public function get_total_saldo_akhir_posisi_by_coa1(array $where = [])
    // {
    //     if (count($where) > 0) {
    //         $this->db->where($where);
    //     }

    //     $this->db->select("sum(jei.nominal) as total_nominal");
    //     $this->db->from("acc_jurnal_entries je");
    //     $this->db->join("acc_jurnal_entries_items jei","jei.kode = je.kode","inner");
    //     $this->db->join("acc_coa coa1","coa1.kode_coa = jei.kode_coa","inner"); 
    //     $this->db->where('jei.kode_coa = coa.kode_coa');
    //     $this->db->group_by('coa1.kode_coa');
    //     $query = $this->db->get_compiled_select();
    //     // $query = $this->db->get();
    //     return $query;
    // }
    
    // public function get_list_bukubesar1($tgldari,$tglsampai,$checkhidden,array $where = [])
    // {
    //     $start      = $this->periodesaldo->get_start_periode();
    //     $tgl_dari   = date("Y-m-d 00:00:00", strtotime($start)); // example 202501-01 00:00:00 by table setting
    //     $tgl_sampai = date("Y-m-d 23:59:59", strtotime("-1 day",  strtotime($tglsampai))); // tgl sampai - 1 untuk saldo awal

    //     $entries = $this->query_entries($tgldari,$tglsampai);
    //     $tmp_where = ['je.tanggal_dibuat >= '=> $tgl_dari, 'je.tanggal_dibuat <= '=> $tgl_sampai, 'je.status'=>'posted', 'jei.posisi'=>'D'];
    //     $s_akhir_debit = $this->get_total_saldo_akhir_posisi_by_coa($tmp_where);
    //     $tmp_where =  ['je.tanggal_dibuat >= '=> $tgl_dari, 'je.tanggal_dibuat <= '=> $tgl_sampai,'je.status'=>'posted', 'jei.posisi'=>'C'];
    //     $s_akhir_credit = $this->get_total_saldo_akhir_posisi_by_coa($tmp_where);

    //     if($checkhidden == 'true'){
    //         $this->db->group_start();
    //         $this->db->where('(coa.saldo_awal + IFNULL(('.$s_akhir_debit.'),0) - IFNULL(('.$s_akhir_credit.'),0)) > 0');
    //         $this->db->or_where('jr.total_debit > 0');
    //         $this->db->or_where('jr.total_credit > 0');
    //         $this->db->group_end();
    //     }
    //     if (count($where) > 0) {
    //         $this->db->where($where);
    //     }

    //     $this->db->where('coa.level',5);
    //     $this->db->select('coa.kode_coa, coa.nama as nama_coa, coa.saldo_normal, (coa.saldo_awal + IFNULL(('.$s_akhir_debit.'),0) - IFNULL(('.$s_akhir_credit.'),0)) as saldo_awal,  IFNULL(jr.total_debit, 0) as  total_debit, IFNULL(jr.total_credit,0) as total_credit');
    //     $this->db->from('acc_coa coa');
    //     $this->db->join("({$entries}) as jr ","jr.kode_coa = coa.kode_coa","left");
    //     $this->db->order_by('coa.kode_coa');
    //     $query = $this->db->get();
    //     return $query->result();
    // }

    public function get_total_saldo_akhir_posisi_by_coa(array $where = [], $posisi)
    {
        if(count($where) > 0){
            $this->db->where($where);
        }

        $this->db->select('jei.kode_coa, SUM(jei.nominal) as total_'.$posisi);
        $this->db->from('acc_jurnal_entries je');
        $this->db->join('acc_jurnal_entries_items jei', 'jei.kode = je.kode');
        $this->db->group_by('jei.kode_coa');
        return $this->db->get_compiled_select();
    }

    public function get_saldo_sblm($tgldari,$posisi)
    {
        $start      = $this->periodesaldo->get_start_periode();
        $tgl_dari   = date("Y-m-d 00:00:00", strtotime($start)); // example 202501-01 00:00:00 by table setting
        $tgl_sampai = date("Y-m-d 23:59:59", strtotime("-1 day",  strtotime($tgldari))); // tgl_sampai = tgldari - 1 untuk saldo awal

        if($posisi == 'D'){
            $kata_posisi = 'debit';
        } else {
            $kata_posisi = 'credit';
        }
        $tmp_where = ['je.tanggal_dibuat >= '=> $tgl_dari, 'je.tanggal_dibuat <= '=> $tgl_sampai, 'je.status'=>'posted', 'jei.posisi'=>$posisi];
        $subquery  = $this->get_total_saldo_akhir_posisi_by_coa($tmp_where, $kata_posisi);
        return $subquery;
      
    }

    public function get_list_bukubesar($tgldari, $tglsampai, $checkhidden, array $where = [])
    {
        
        $subquery_debit = $this->get_saldo_sblm($tgldari, 'D');
        $subquery_credit = $this->get_saldo_sblm($tgldari, 'C');

        // get saldo debit / credit yang berjalan
        $entries = $this->query_entries($tgldari,$tglsampai);

        if($checkhidden == 'true'){
            $this->db->group_start();
            $this->db->where("(CASE 
                WHEN coa.saldo_normal = 'D' THEN 
                    (coa.saldo_awal + COALESCE(debit_sbl.total_debit, 0) - COALESCE(credit_sbl.total_credit, 0)) <> 0
                WHEN coa.saldo_normal = 'C' THEN 
                    (coa.saldo_awal + COALESCE(credit_sbl.total_credit, 0) - COALESCE(debit_sbl.total_debit, 0)) <> 0
                ELSE 0
              END)", null, false);
            $this->db->or_where('jr.total_debit <> 0');
            $this->db->or_where('jr.total_credit <> 0');
            $this->db->group_end();
        }
        

        if(count($where) > 0){
            $this->db->where($where);
        }

        $this->db->where('coa.level',5);
        $this->db->select(" coa.kode_coa, coa.nama as nama_coa,coa.saldo_normal,coa.saldo_awal,COALESCE(debit_sbl.total_debit, 0) as total_debit_sbl,
                            COALESCE(credit_sbl.total_credit, 0) as total_credit_sbl,
                            CASE 
                                WHEN coa.saldo_normal = 'D' THEN 
                                    (coa.saldo_awal + COALESCE(debit_sbl.total_debit, 0) - COALESCE(credit_sbl.total_credit, 0))
                                WHEN coa.saldo_normal = 'C' THEN 
                                    (coa.saldo_awal + COALESCE(credit_sbl.total_credit, 0) - COALESCE(debit_sbl.total_debit, 0))
                                ELSE coa.saldo_awal
                            END AS saldo_awal_final,
                            COALESCE(jr.total_debit, 0) as total_debit,
                            COALESCE(jr.total_credit, 0) as total_credit");
        $this->db->from('acc_coa coa');
        $this->db->join("($subquery_debit) as debit_sbl", "debit_sbl.kode_coa = coa.kode_coa", "left");
        $this->db->join("($subquery_credit) as credit_sbl", "credit_sbl.kode_coa = coa.kode_coa", "left");
        $this->db->join("({$entries}) as jr ","jr.kode_coa = coa.kode_coa","left");
        $this->db->order_by('coa.kode_coa');
        $query = $this->db->get();
        return $query->result();

    }

    // public function get_bukubesar_detail($checkhidden, array $where = [])
    // {

    //     if($checkhidden == 'true'){
    //         $this->db->group_start();
    //         $this->db->where('coa.saldo_awal > 0');
    //         $this->db->or_where('jr.total_debit > 0');
    //         $this->db->or_where('jr.total_credit > 0');
    //         $this->db->group_end();
    //     }

    //     if (count($where) > 0) {
    //         $this->db->where($where);
    //     }

    //     $this->db->select("je.tanggal_dibuat as tanggal, jei.kode as kode_entries, je.origin, CONCAT('[',prt.nama,']',' ',jei.nama,' - ',IFNULL(jei.reff_note,'')) as keterangan,
    //                         jei.kode_coa, coa.nama as nama_coa, jei.posisi,  SUM(CASE WHEN jei.posisi = 'D' THEN jei.nominal ELSE 0 END) AS total_debit,   SUM(CASE WHEN jei.posisi = 'C' THEN jei.nominal ELSE 0 END) AS total_credit");
    //     $this->db->from("acc_jurnal_entries je");
    //     $this->db->join("acc_jurnal_entries_items jei","jei.kode = je.kode","inner");
    //     $this->db->join("acc_coa coa","coa.kode_coa = jei.kode_coa","inner");
    //     $this->db->join("partner prt","prt.id = jei.partner","left");
    //     $this->db->order_by('je.tanggal_dibuat asc');
    //     $query = $this->db->get();
    //     return $query->result();

    // }


    function get_coa_by_kode($kode)
    {
        $this->db->where('kode_coa',$kode);
        $this->db->select('*');
        $this->db->from('acc_coa');
        $query = $this->db->get();
        return $query->row();
    }


    public function get_list_coa_select2($nama)
	{
        $this->db->where('level',5);
        $this->db->group_start();
        $this->db->like('nama', $nama);
        $this->db->or_like('kode_coa', $nama);
        $this->db->group_end();
        $query = $this->db->get('acc_coa');
        return $query->result();
	}


    public function get_list_bukubesar_detail_coa($tgldari, $tglsampai, array $where = [],string $checkhidden = 'false')
    {
       
        $entries = $this->query_entries($tgldari,$tglsampai);

        $subquery_debit = $this->get_saldo_sblm($tgldari, 'D');
        $subquery_credit = $this->get_saldo_sblm($tgldari, 'C');


        if($checkhidden == 'true'){
            $this->db->group_start();
            $this->db->where("(CASE 
                WHEN coa.saldo_normal = 'D' THEN 
                    (coa.saldo_awal + COALESCE(debit_sbl.total_debit, 0) - COALESCE(credit_sbl.total_credit, 0)) <> 0
                WHEN coa.saldo_normal = 'C' THEN 
                    (coa.saldo_awal + COALESCE(credit_sbl.total_credit, 0) - COALESCE(debit_sbl.total_debit, 0)) <> 0
                ELSE 0
              END)", null, false);
            $this->db->or_where('jr.total_debit <> 0');
            $this->db->or_where('jr.total_credit <> 0');
            $this->db->group_end();
        }
        
        if (count($where) > 0) {
            $this->db->where($where);
        }

        $this->db->select("coa.kode_coa, coa.nama as nama_coa,coa.saldo_normal,coa.saldo_awal,COALESCE(debit_sbl.total_debit, 0) as total_debit_sbl,
                            COALESCE(credit_sbl.total_credit, 0) as total_credit_sbl,
                            CASE 
                                WHEN coa.saldo_normal = 'D' THEN 
                                    (coa.saldo_awal + COALESCE(debit_sbl.total_debit, 0) - COALESCE(credit_sbl.total_credit, 0))
                                WHEN coa.saldo_normal = 'C' THEN 
                                    (coa.saldo_awal + COALESCE(credit_sbl.total_credit, 0) - COALESCE(debit_sbl.total_debit, 0))
                                ELSE coa.saldo_awal
                            END AS saldo_awal_final,
                            COALESCE(jr.total_debit, 0) as total_debit,
                            COALESCE(jr.total_credit, 0) as total_credit");
        $this->db->from('acc_coa coa');
        $this->db->join("($subquery_debit) as debit_sbl", "debit_sbl.kode_coa = coa.kode_coa", "left");
        $this->db->join("($subquery_credit) as credit_sbl", "credit_sbl.kode_coa = coa.kode_coa", "left");
        $this->db->join("({$entries}) as jr ","jr.kode_coa = coa.kode_coa","left");
        // $this->db->select("coa.kode_coa, coa.nama as nama_coa, coa.saldo_awal, coa.saldo_normal, if(coa.saldo_normal = 'D', coa.saldo_awal,0) as saldo_awal_debit, if(coa.saldo_normal = 'C', coa.saldo_awal,0) as saldo_awal_credit");
        // $this->db->from("acc_jurnal_entries je");
        // $this->db->join("acc_jurnal_entries_items jei","jei.kode = je.kode","inner");
        // $this->db->join("acc_coa coa","coa.kode_coa = jei.kode_coa","inner");
        // $this->db->group_by('coa.kode_coa');
        $this->db->order_by('coa.kode_coa asc');
        $query = $this->db->get();
        return $query->result();
    }


    public function get_list_bukubesar_detail_by_coa(array $where = [])
    {

        
        if (count($where) > 0) {
            $this->db->where($where);
        }
       
        $this->db->select("coa.saldo_normal,je.tanggal_dibuat as tanggal, jei.kode as kode_entries,je.origin, IF(prt.nama is null or  prt.nama = '',  IF(je.reff_note ='',jei.nama,CONCAT('[',je.reff_note,']',' - ',jei.nama)), CONCAT('[',prt.nama,']',' ', jei.nama,IF(jei.reff_note IS NOT NULL AND jei.reff_note != '', CONCAT(' - ', jei.reff_note), ''))) as keterangan,,
                            jei.kode_coa, coa.nama as nama_coa, jei.posisi,  if(jei.posisi = 'D',  jei.nominal, 0) as total_debit, if(jei.posisi = 'C', jei.nominal, 0) as total_credit");
        $this->db->from("acc_jurnal_entries je");
        $this->db->join("acc_jurnal_entries_items jei","jei.kode = je.kode","inner");
        $this->db->join("acc_coa coa","coa.kode_coa = jei.kode_coa","inner");
        $this->db->join("partner prt","prt.id = jei.partner","left");
        $this->db->order_by('je.tanggal_dibuat asc');
        $query = $this->db->get();
        return $query->result();

    }

    public function get_list_bukubesar_detail_lawan_by_coa(array $where = [], $view)
    {

        if (count($where) > 0) {
            $this->db->where($where);
        }

        $this->db->where("jei.nominal <> 0");

        $this->db->select("coa.saldo_normal,je.tanggal_dibuat as tanggal, jei.kode as kode_entries,je.origin, IF(prt.nama is null or  prt.nama = '',  CONCAT('[',je.reff_note,']',' - ',jei.nama), CONCAT('[',prt.nama,']',' ',jei.nama,IF(jei.reff_note IS NOT NULL AND jei.reff_note != '', CONCAT(' - ', jei.reff_note), ''))) as keterangan,
                            jei.kode_coa, coa.nama as nama_coa, jei.posisi,  IFNULL(CASE WHEN jei.posisi = '{$view}' THEN jei.nominal ELSE 0 END,0) AS total");
        $this->db->from("acc_jurnal_entries je");
        $this->db->join("acc_jurnal_entries_items jei","jei.kode = je.kode","inner");
        $this->db->join("acc_coa coa","coa.kode_coa = jei.kode_coa","inner");
        $this->db->join("partner prt","prt.id = jei.partner","left");
        $this->db->order_by('je.tanggal_dibuat asc');
        $this->db->order_by('coa.kode_coa asc');
        $query = $this->db->get();
        return $query->result();

    }


    public function get_setting_start_periode_acc(array $where = [])
    {
        if (count($where) > 0) {
            $this->db->where($where);
        }
        $this->db->select('value');
        $this->db->from('setting');
        $this->db->order_by('id');
        $query = $this->db->get();
        return $query->row()->value ?? '';
    }


    public function get_total_nominal_posisi_by_coa(array $where = [])
    {
        if (count($where) > 0) {
            $this->db->where($where);
        }

        $this->db->select("coa.kode_coa, coa.nama as nama_coa, coa.saldo_awal, coa.saldo_normal, sum(jei.nominal) as total_nominal");
        $this->db->from("acc_jurnal_entries je");
        $this->db->join("acc_jurnal_entries_items jei","jei.kode = je.kode","inner");
        $this->db->join("acc_coa coa","coa.kode_coa = jei.kode_coa","inner");
        $this->db->group_by('coa.kode_coa');
        $query = $this->db->get();
        return $query->row();
    }



    



}