<?php defined('BASEPATH') or exit ('No Direct Script Acces Allowed');

/**
 * 
 */

class M_bukubesarpembantuutang extends CI_Model
{
  	public function __construct()
	{
		parent::__construct();
		$this->load->database();
        $this->load->library('periodesaldo');
	}



    public function get_list_bukubesar($tgldari, $tglsampai, $checkhidden, array $where = [])
    {
        // saldo sebelum periode berjalan
        $subquery_invoice_sblm = $this->get_saldo_sblm($tgldari, 'invoice');
        $subquery_pelunasan_sblm = $this->get_saldo_sblm($tgldari, 'pelunasan');
        $subquery_retur_sblm = $this->get_saldo_sblm($tgldari, 'retur');
        $subquery_um_sblm = $this->get_saldo_sblm($tgldari, 'um');
        $subquery_korksi_sblm = $this->get_saldo_sblm($tgldari, 'koreksi');
        

        // saldo berdasakran periode berjalan
        $subquery_invoice      = $this->get_saldo_utang(['created_at >= '=> $tgldari, 'created_at <= '=> $tglsampai, 'status'=>'done'], 'id_supplier','');
        $subquery_pelunasan    = $this->get_saldo_pelunasan(['aph.tanggal_transaksi >= '=> $tgldari, 'aph.tanggal_transaksi  <= '=> $tglsampai], 'aph.partner_id','');
        $subquery_retur        = $this->get_saldo_retur(['aph.tanggal_transaksi >= '=> $tgldari, 'aph.tanggal_transaksi  <= '=> $tglsampai], 'aph.partner_id','');
        $subquery_um           = $this->get_saldo_um(['aph.tanggal_transaksi >= '=> $tgldari, 'aph.tanggal_transaksi <= '=> $tglsampai, 'status'=>'done', 'aphs.tipe_currency' => 'Rp'], 'aph.partner_id','');
        $subquery_korksi       = $this->get_saldo_koreksi(['aph.tanggal_transaksi >= '=> $tgldari, 'aph.tanggal_transaksi <= '=> $tglsampai, 'status'=>'done', 'aphs.tipe_currency' => 'Rp'], 'aph.partner_id','');


        if($checkhidden == 'true'){
            $this->db->where("(
                    (p.saldo_awal_utang 
                        + IFNULL(utang_sblm.total_utang, 0) 
                        - IFNULL(pelunasan_sblm.total_pelunasan, 0) 
                        - IFNULL(retur_sblm.total_retur, 0) 
                        - IFNULL(um_sblm.total_uang_muka, 0) 
                        + IFNULL(koreksi_sblm.total_koreksi, 0)
                    ) <> 0
                    OR
                    (IFNULL(utang.total_utang, 0)
                        + IFNULL(pelunasan.total_pelunasan, 0)
                        + IFNULL(retur.total_retur, 0)
                        + IFNULL(um.total_uang_muka, 0)
                        + IFNULL(koreksi.total_koreksi, 0)
                    ) <> 0
                )
            ", null, false);
        }


        if(count($where) > 0){
            $this->db->where($where);
        }
        // $this->db->where_in('p.id', array(4195,4435,4494,5132,4210,5166,552));
        $this->db->select("p.id, p.nama, p.saldo_awal_utang,
                        (p.saldo_awal_utang + IFNULL(utang_sblm.total_utang,0) - IFNULL(pelunasan_sblm.total_pelunasan,0) - IFNULL(retur_sblm.total_retur, 0) - IFNULL(um_sblm.total_uang_muka,0) + (IFNULL(koreksi_sblm.total_koreksi,0)) ) as saldo_awal_final,
                        IFNULL(utang.total_utang,0) as total_utang,
                        IFNULL(pelunasan.total_pelunasan,0) as total_pelunasan,
                        IFNULL(retur.total_retur,0) as total_retur,
                        IFNULL(um.total_uang_muka,0) as total_uang_muka,
                        IFNULL(koreksi.total_koreksi,0) as total_koreksi ");
        $this->db->from('partner p');
        $this->db->join("($subquery_invoice_sblm) as utang_sblm", "utang_sblm.id_partner = p.id", "left");
        $this->db->join("($subquery_invoice) as utang", "utang.id_partner = p.id", "left");
        $this->db->join("($subquery_pelunasan_sblm) as pelunasan_sblm", "pelunasan_sblm.id_partner = p.id", "left");
        $this->db->join("($subquery_pelunasan) as pelunasan", "pelunasan.id_partner = p.id", "left");
        $this->db->join("($subquery_retur_sblm) as retur_sblm", "retur_sblm.id_partner = p.id", "left");
        $this->db->join("($subquery_retur) as retur", "retur.id_partner = p.id", "left");
        $this->db->join("($subquery_um_sblm) as um_sblm", "um_sblm.id_partner = p.id", "left");
        $this->db->join("($subquery_um) as um", "um.id_partner = p.id", "left");
        $this->db->join("($subquery_korksi_sblm) as koreksi_sblm", "koreksi_sblm.id_partner = p.id", "left");
        $this->db->join("($subquery_korksi) as koreksi", "koreksi.id_partner = p.id", "left");
        $this->db->order_by('p.nama');
        $query = $this->db->get();
        return $query->result();

    }


    function get_saldo_sblm($tgldari, $tipe, string $currency = '')
    {
        $start      = $this->periodesaldo->get_start_periode();
        $tgl_dari   = date("Y-m-d 00:00:00", strtotime($start)); // example 202501-01 00:00:00 by table setting
        $tgl_sampai = date("Y-m-d 23:59:59", strtotime("-1 day",  strtotime($tgldari))); // tgl_sampai = tgldari - 1 untuk saldo awal

        $tmp_currency = [];
        
        if($tipe == 'invoice'){
            $tmp_where = ['created_at >= '=> $tgl_dari, 'created_at <= '=> $tgl_sampai, 'status'=>'done'];
            if($currency === 'valas'){
                $cr_condition = ($currency === 'valas')? '<>' : '';
                $tmp_where = array_merge($tmp_where, ['matauang ' . $cr_condition => 1 ]);
            }
            $subquery  = $this->get_saldo_utang($tmp_where, 'id_supplier',$currency);
        } else if($tipe == 'pelunasan'){
            $tmp_where = ['aph.tanggal_transaksi >= '=> $tgl_dari, 'aph.tanggal_transaksi  <= '=> $tgl_sampai];
            if($currency === 'valas'){
                $cr_condition = ($currency === 'valas')? '<>' : '';
                $tmp_where = array_merge($tmp_where, [ 'aphm.currency_id ' .$cr_condition => 1]);
            }
            $subquery  = $this->get_saldo_pelunasan($tmp_where, 'aph.partner_id',$currency);
        } else if($tipe == 'retur'){
            $tmp_where = ['aph.tanggal_transaksi >= '=> $tgl_dari, 'aph.tanggal_transaksi  <= '=> $tgl_sampai];
            if($currency === 'valas'){
                $cr_condition = ($currency === 'valas')? '<>' : '';
                $tmp_where = array_merge($tmp_where, [ 'aphm.currency_id ' .$cr_condition => 1]);
            }
            $subquery  = $this->get_saldo_retur($tmp_where, 'aph.partner_id',$currency);
        } else if($tipe == 'um'){
            $tmp_where = ['aph.tanggal_transaksi >= '=> $tgl_dari, 'aph.tanggal_transaksi <= '=> $tgl_sampai, 'status'=>'done','aphs.tipe_currency' => 'Rp'];
            if($currency === 'valas'){
                $cr_condition = ($currency === 'valas')? '<>' : '';
                $tmp_where = array_merge($tmp_where, [ 'aphs.tipe_currency ' .$cr_condition => 'Rp']);
            }
            $subquery  = $this->get_saldo_um($tmp_where, 'aph.partner_id',$currency);
        } else { // koreksi
            $tmp_where = ['aph.tanggal_transaksi >= '=> $tgl_dari, 'aph.tanggal_transaksi <= '=> $tgl_sampai, 'status'=>'done', 'aphs.tipe_currency' => 'Rp'];
            if($currency === 'valas'){
                $cr_condition = ($currency === 'valas')? '<>' : '';
                $tmp_where = array_merge($tmp_where, [ 'aphs.tipe_currency ' .$cr_condition => 'Rp']);
            }
            $subquery  = $this->get_saldo_koreksi($tmp_where, 'aph.partner_id',$currency);
        }
        return $subquery;
    }


    function get_saldo_utang(array $where = [], string $group = '', string $currency = '') 
    {
        if(count($where) > 0){
            $this->db->where($where);
        }

        if($group){
            $this->db->group_by($group);
        }

        $total  = ($currency === 'valas')? 'total_valas' : 'total_rp';

        $this->db->SELECT("id as id_bukti, no_invoice as no_bukti,created_at as tgl, id_supplier as id_partner,
        CONCAT(
            'Pembelian: ',
            IF(origin = '', '', CONCAT('RCV: ',origin, ' - ')),
            IF(no_invoice_supp = '', '', CONCAT('No: ', no_invoice_supp, ' - ')),
            IF(no_sj_supp = '', '', CONCAT('SJ: ', no_sj_supp)),
            IF('$currency' = 'valas', 
                CONCAT(' - ',' Curr: ', (SELECT currency FROM currency_kurs WHERE id = matauang), ' - Kurs: ', nilai_matauang), 
                ''
            )
        ) as uraian,
        IFNULL(SUM($total), 0) as total_utang,
        0 as debit,
        IFNULL(SUM($total), 0) as credit,
        status, 'inv' as link");
        $this->db->FROM('invoice');
        return $this->db->get_compiled_select();
    }


    function get_saldo_pelunasan( array $where = [], string $group = '', string $currency = '') 
    {
        if(count($where) > 0){
            $this->db->where($where);
        }
        if($group){
            $this->db->group_by($group);
        }

        $total  = ($currency === 'valas')? 'aphm.total_valas' : 'aphm.total_rp';

        $this->db->where('aph.status', 'done');
        $this->db->where('aphm.tipe <> ','um');
        $this->db->where('aphm.tipe <> ','retur');
        $this->db->where('aphm.tipe <> ','koreksi');
        $this->db->SELECT("aph.id as id_bukti, aph.no_pelunasan as no_bukti, aph.tanggal_transaksi as tgl, aph.partner_id as id_partner, CONCAT('Pembayaran: ', (SELECT GROUP_CONCAT(no_invoice) as group_invoice FROM acc_pelunasan_hutang_invoice WHERE pelunasan_hutang_id = aph.id), 
            
            IF('$currency' = 'valas', 
                GROUP_CONCAT(' - ',aphm.no_bukti,' Curr: ', (SELECT currency FROM currency_kurs WHERE id = currency_id), '  Kurs: ', aphm.kurs,' '), 
                GROUP_CONCAT(' - ',,aphm.no_bukti)
            )) as uraian, IFNULL(SUM($total),0) as total_pelunasan,   IFNULL(SUM($total),0) as debit , 0 as credit, aph.status, 'plh' as link ");
        $this->db->FROM('acc_pelunasan_hutang aph');
        $this->db->jOIN("acc_pelunasan_hutang_metode aphm","aph.id = aphm.pelunasan_hutang_id", "INNER");
        return $this->db->get_compiled_select();

    }


    function get_saldo_retur(array $where = [], string $group = '', string $currency = '') 
    {
        if(count($where) > 0){
            $this->db->where($where);
        }
        if($group){
            $this->db->group_by($group);
        }
        $total  = ($currency === 'valas')? 'aphm.total_valas' : 'aphm.total_rp';

        $this->db->where('aph.status', 'done');
        $this->db->where('aphm.tipe','retur');
        $this->db->SELECT("aph.id as id_bukti, aph.no_pelunasan as no_bukti, aph.tanggal_transaksi as tgl, aph.partner_id as id_partner, CONCAT('Retur: ', (SELECT GROUP_CONCAT(no_invoice) as group_invoice FROM acc_pelunasan_hutang_invoice WHERE pelunasan_hutang_id = aph.id), 
            
            IF('$currency' = 'valas', 
                GROUP_CONCAT(' - ',aphm.no_bukti,' Curr: ', (SELECT currency FROM currency_kurs WHERE id = currency_id), '  Kurs: ', aphm.kurs,' '), 
                GROUP_CONCAT(' - ',,aphm.no_bukti)
            )) as uraian, IFNULL(SUM($total),0) as total_retur,   IFNULL(SUM($total),0) as debit , 0 as credit, aph.status, 'plh' as link ");
        $this->db->FROM('acc_pelunasan_hutang aph');
        $this->db->jOIN("acc_pelunasan_hutang_metode aphm","aph.id = aphm.pelunasan_hutang_id", "INNER");
        return $this->db->get_compiled_select();

    }


    function get_saldo_um(array $where = [], string $group = '', string $currency = '') 
    {
        if(count($where) > 0){
            $this->db->where($where);
        }
        if($group){
            $this->db->group_by($group);
        }
        $total  = ($currency === 'valas')? 'aphs.total_pelunasan' : 'aphs.total_pelunasan * aphs.kurs';

        $this->db->where('aphs.keterangan', 'Uang Muka');
        $this->db->where('aph.status', 'done');
        // $this->db->where('aphs.tipe_currency', 'Rp');
        $this->db->SELECT("aph.id as id_bukti, aph.no_pelunasan as no_bukti, aph.tanggal_transaksi as tgl, aph.partner_id as id_partner,  
                        CONCAT('Uang Muka : ', (SELECT GROUP_CONCAT(no_invoice) as group_invoice FROM acc_pelunasan_hutang_invoice WHERE pelunasan_hutang_id = aph.id), ' ',  
                        IF('$currency' = 'valas', 
                                GROUP_CONCAT(' - ',(SELECT GROUP_CONCAT(no_bukti) as group_no FROM acc_pelunasan_hutang_metode WHERE pelunasan_hutang_id = aph.id),' Curr: ', (SELECT currency FROM currency_kurs WHERE id = aphs.currency_id), '  Kurs: ', aphs.kurs,' '), 
                                GROUP_CONCAT(' - ',(SELECT GROUP_CONCAT(no_bukti) as group_no FROM acc_pelunasan_hutang_metode WHERE pelunasan_hutang_id = aph.id))
                        )) as uraian, 

                        IFNULL(SUM($total),0) as total_uang_muka, IFNULL(SUM($total),0)  as debit, 0  as credit,  aph.status, 'plh' as link");
        $this->db->FROM('acc_pelunasan_hutang aph');
        $this->db->jOIN("acc_pelunasan_hutang_summary aphs","aph.id = aphs.pelunasan_hutang_id", "INNER");
        return $this->db->get_compiled_select();
    }

    function get_saldo_koreksi(array $where = [], string $group = '',  string $currency = '') 
    {
        if(count($where) > 0){
            $this->db->where($where);
        }
        if($group){
            $this->db->group_by($group);
        }

        $total  = ($currency === 'valas')? 'aphs.selisih' : 'aphs.selisih';

        $this->db->where('aphs.keterangan <>', 'Uang Muka');
        $this->db->where('aph.status', 'done');
        // $this->db->where('aphs.tipe_currency', 'Rp');
        $this->db->where('ack.koreksi_bb', 'true');
        $this->db->SELECT("aph.id as id_bukti, aph.no_pelunasan as no_bukti, aph.tanggal_transaksi as tgl, aph.partner_id as id_partner,  CONCAT('Koreksi : ', ack.nama_koreksi, IF('$currency' = 'valas', 
                CONCAT(' - ',' Curr: ', (SELECT currency FROM currency_kurs WHERE id = currency_id), ' - Kurs: ', aphs.kurs), 
                ''
            ))  as uraian, IFNULL(SUM($total),0) as total_koreksi,  
                        (CASE 
                            WHEN aphs.selisih < 0 THEN abs($total) 
                            ELSE 0 
                        END) AS debit,
                        (CASE
                            WHEN aphs.selisih > 0 THEN ($total)
                            ELSE 0 
                        END) AS credit, aph.status, 'plh' as link");
        $this->db->FROM('acc_pelunasan_hutang aph');
        $this->db->jOIN("acc_pelunasan_hutang_summary aphs","aph.id = aphs.pelunasan_hutang_id", "INNER");
        $this->db->JOIN("acc_pelunasan_koreksi ack","aphs.koreksi = ack.kode","left");
        return $this->db->get_compiled_select();
    } 


    public function get_list_bukubesar_detail($tgldari, $tglsampai, $checkhidden, array $where = [], $currency)
    {
        // saldo sebelum periode berjalan
        $subquery_invoice_sblm = $this->get_saldo_sblm($tgldari, 'invoice',$currency);
        $subquery_pelunasan_sblm = $this->get_saldo_sblm($tgldari, 'pelunasan',$currency);
        $subquery_retur_sblm = $this->get_saldo_sblm($tgldari, 'retur',$currency);
        $subquery_um_sblm = $this->get_saldo_sblm($tgldari, 'um',$currency);
        $subquery_koreksi_sblm = $this->get_saldo_sblm($tgldari, 'koreksi',$currency);

        //table currecny kurs 
        // 1 = IDR
        // 1 <> USD,EUR CNY,JPY

        if($currency === 'all' || $currency == 'rp'){
            $where_utang = ['created_at >= '=> $tgldari, 'created_at <= '=> $tglsampai, 'status'=>'done'];
            $where_pelunasan = ['aph.tanggal_transaksi >= '=> $tgldari, 'aph.tanggal_transaksi  <= '=> $tglsampai];
            $where_retur = ['aph.tanggal_transaksi >= '=> $tgldari, 'aph.tanggal_transaksi  <= '=> $tglsampai];
            $where_um    = ['aph.tanggal_transaksi >= '=> $tgldari, 'aph.tanggal_transaksi <= '=> $tglsampai, 'status'=>'done','aphs.tipe_currency' => 'Rp'];
            $where_koreksi = ['aph.tanggal_transaksi >= '=> $tgldari, 'aph.tanggal_transaksi <= '=> $tglsampai, 'status'=>'done', 'aphs.tipe_currency' => 'Rp'];
        } else {
           $cr_condition = ($currency === 'valas')? '<>' : '';
            
           $where_utang = ['created_at >= '=> $tgldari, 'created_at <= '=> $tglsampai, 'status'=>'done', 'matauang ' . $cr_condition => 1 ];
           $where_pelunasan = ['aph.tanggal_transaksi >= '=> $tgldari, 'aph.tanggal_transaksi  <= '=> $tglsampai, 'aphm.currency_id ' .$cr_condition => 1];
           $where_retur = ['aph.tanggal_transaksi >= '=> $tgldari, 'aph.tanggal_transaksi  <= '=> $tglsampai, 'aphm.currency_id ' .$cr_condition => 1];
           $where_um    = ['aph.tanggal_transaksi >= '=> $tgldari, 'aph.tanggal_transaksi <= '=> $tglsampai, 'status'=>'done','aphs.tipe_currency ' .$cr_condition => 'Rp'];
           $where_koreksi = ['aph.tanggal_transaksi >= '=> $tgldari, 'aph.tanggal_transaksi <= '=> $tglsampai, 'status'=>'done', 'aphs.tipe_currency ' .$cr_condition => 'Rp'];
        }

        // saldo berdasakran periode berjalan
        $subquery_invoice      = $this->get_saldo_utang($where_utang, 'id_supplier',$currency);
        $subquery_pelunasan    = $this->get_saldo_pelunasan($where_pelunasan, 'aph.partner_id',$currency);
        $subquery_retur        = $this->get_saldo_retur($where_retur, 'aph.partner_id',$currency);
        $subquery_um           = $this->get_saldo_um($where_um, 'aph.partner_id',$currency);
        $subquery_koreksi       = $this->get_saldo_koreksi($where_koreksi, 'aph.partner_id',$currency);

        $cr_condition = ($currency === 'valas')? 'p.saldo_awal_utang_valas' : 'p.saldo_awal_utang';
        if($checkhidden == 'true'){
            $this->db->where("(
                    ($cr_condition 
                        + IFNULL(utang_sblm.total_utang, 0) 
                        - IFNULL(pelunasan_sblm.total_pelunasan, 0) 
                        - IFNULL(retur_sblm.total_retur, 0) 
                        - IFNULL(um_sblm.total_uang_muka, 0) 
                        + IFNULL(koreksi_sblm.total_koreksi, 0)
                    ) <> 0
                    OR
                    (IFNULL(utang.total_utang, 0)
                        + IFNULL(pelunasan.total_pelunasan, 0)
                        + IFNULL(retur.total_retur, 0)
                        + IFNULL(um.total_uang_muka, 0)
                        + IFNULL(koreksi.total_koreksi, 0)
                    ) <> 0
                )
            ", null, false);
        }


        if(count($where) > 0){
            $this->db->where($where);
        }
       
        $this->db->select("p.id, p.nama, $cr_condition as saldo_awal_utang,
                        ($cr_condition + IFNULL(utang_sblm.total_utang,0) - IFNULL(pelunasan_sblm.total_pelunasan,0) - IFNULL(retur_sblm.total_retur, 0) - IFNULL(um_sblm.total_uang_muka,0) + (IFNULL(koreksi_sblm.total_koreksi,0)) ) as saldo_awal_final,
                        IFNULL(utang.total_utang,0) as total_utang,
                        IFNULL(pelunasan.total_pelunasan,0) as total_pelunasan,
                        IFNULL(retur.total_retur,0) as total_retur,
                        IFNULL(um.total_uang_muka,0) as total_uang_muka,
                        IFNULL(koreksi.total_koreksi,0) as total_koreksi ");
        $this->db->from('partner p');
        $this->db->join("($subquery_invoice_sblm) as utang_sblm", "utang_sblm.id_partner = p.id", "left");
        $this->db->join("($subquery_invoice) as utang", "utang.id_partner = p.id", "left");
        $this->db->join("($subquery_pelunasan_sblm) as pelunasan_sblm", "pelunasan_sblm.id_partner = p.id", "left");
        $this->db->join("($subquery_pelunasan) as pelunasan", "pelunasan.id_partner = p.id", "left");
        $this->db->join("($subquery_retur_sblm) as retur_sblm", "retur_sblm.id_partner = p.id", "left");
        $this->db->join("($subquery_retur) as retur", "retur.id_partner = p.id", "left");
        $this->db->join("($subquery_um_sblm) as um_sblm", "um_sblm.id_partner = p.id", "left");
        $this->db->join("($subquery_um) as um", "um.id_partner = p.id", "left");
        $this->db->join("($subquery_koreksi_sblm) as koreksi_sblm", "koreksi_sblm.id_partner = p.id", "left");
        $this->db->join("($subquery_koreksi) as koreksi", "koreksi.id_partner = p.id", "left");
        $this->db->order_by('p.nama');
        $query = $this->db->get();
        return $query->result();

    }

    function get_list_bukubesar_detail_by_kode($tgldari,$tglsampai, array $where = [], $currency)
    {

        //table currecny kurs 
        // 1 = IDR
        // 1 <> USD,EUR CNY,JPY

        if($currency === 'all' || $currency === 'rp'){
            $where_utang = ['created_at >= '=> $tgldari, 'created_at <= '=> $tglsampai, 'status'=>'done'];
            $where_pelunasan = ['aph.tanggal_transaksi >= '=> $tgldari, 'aph.tanggal_transaksi  <= '=> $tglsampai];
            $where_retur = ['aph.tanggal_transaksi >= '=> $tgldari, 'aph.tanggal_transaksi  <= '=> $tglsampai];
            $where_um    = ['aph.tanggal_transaksi >= '=> $tgldari, 'aph.tanggal_transaksi <= '=> $tglsampai, 'status'=>'done','aphs.tipe_currency' => 'Rp'];
            $where_koreksi = ['aph.tanggal_transaksi >= '=> $tgldari, 'aph.tanggal_transaksi <= '=> $tglsampai, 'status'=>'done', 'aphs.tipe_currency' => 'Rp'];
        } else {
           $cr_condition = ($currency === 'valas')? '<>' : '';
            
           $where_utang = ['created_at >= '=> $tgldari, 'created_at <= '=> $tglsampai, 'status'=>'done', 'matauang ' . $cr_condition => 1 ];
           $where_pelunasan = ['aph.tanggal_transaksi >= '=> $tgldari, 'aph.tanggal_transaksi  <= '=> $tglsampai, 'aphm.currency_id ' .$cr_condition => 1];
           $where_retur = ['aph.tanggal_transaksi >= '=> $tgldari, 'aph.tanggal_transaksi  <= '=> $tglsampai, 'aphm.currency_id ' .$cr_condition => 1];
           $where_um    = ['aph.tanggal_transaksi >= '=> $tgldari, 'aph.tanggal_transaksi <= '=> $tglsampai, 'status'=>'done','aphs.tipe_currency ' .$cr_condition => 'Rp'];
           $where_koreksi = ['aph.tanggal_transaksi >= '=> $tgldari, 'aph.tanggal_transaksi <= '=> $tglsampai, 'status'=>'done', 'aphs.tipe_currency ' .$cr_condition => 'Rp'];
        }

        $subquery_invoice      = $this->get_saldo_utang($where_utang, 'no_invoice',$currency);
        $subquery_pelunasan    = $this->get_saldo_pelunasan($where_pelunasan, 'aph.no_pelunasan',$currency);
        $subquery_retur        = $this->get_saldo_retur($where_retur, 'aph.no_pelunasan',$currency);
        $subquery_um           = $this->get_saldo_um($where_um,'aph.no_pelunasan',$currency);
        $subquery_koreksi       = $this->get_saldo_koreksi($where_koreksi, 'aph.no_pelunasan',$currency);

        if(count($where) > 0){
            $this->db->where($where);
        }

        $union_sql = $subquery_invoice . ' UNION ALL ' . $subquery_pelunasan . ' UNION ALL ' . $subquery_retur . ' UNION ALL ' . $subquery_um . ' UNION ALL ' . $subquery_koreksi;

        $this->db->SELECT('id_bukti, no_bukti, tgl, id_partner, uraian, debit, credit, status, link');
        $this->db->from('(' . $union_sql . ') as sub');
        $this->db->order_by('tgl','asc');
        $this->db->order_by('debit','desc');
        // $this->db->order_by('credit','asc');
        $query = $this->db->get();
        return $query->result();
    }



}