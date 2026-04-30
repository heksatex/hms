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
        $subquery_korksi       = $this->get_saldo_koreksi(['tanggal_transaksi >= '=> $tgldari, 'tanggal_transaksi <= '=> $tglsampai, 'status'=>'done', 'tipe_currency' => 'Rp'], 'id_partner','');


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
            $tmp_where = ['tanggal_transaksi >= ' => $tgl_dari, 'tanggal_transaksi <= ' => $tgl_sampai, 'status' => 'done'];
            // if ($currency === 'valas') {
            // }
            $cr_condition = ($currency === 'valas') ? '<>' : '';
            $tmp_where = array_merge($tmp_where, ['tipe_currency ' . $cr_condition => 'Rp']);
            $subquery  = $this->get_saldo_koreksi($tmp_where, 'id_partner', $currency);
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
            IF(origin = '', '', CONCAT('RCV: ',origin)),
            IF(no_invoice_supp = '', '', CONCAT(' - ','No: ', no_invoice_supp)),
            IF(no_sj_supp = '', '', CONCAT(' - ','SJ: ', no_sj_supp)),
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
        $this->db->SELECT("aph.id as id_bukti, aph.no_pelunasan as no_bukti, aph.tanggal_transaksi as tgl, aph.partner_id as id_partner, CONCAT('Pembayaran: ', (SELECT GROUP_CONCAT(no_invoice) as group_invoice FROM acc_pelunasan_hutang_invoice WHERE pelunasan_hutang_id = aph.id),' - ', 
            
            IF('$currency' = 'valas', 
                CONCAT(GROUP_CONCAT(aphm.no_bukti), ' Curr: ', (SELECT currency FROM currency_kurs WHERE id = currency_id), '  Kurs: ', aphm.kurs, ' '), 
                GROUP_CONCAT(aphm.no_bukti)
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

    function get_saldo_koreksi_normalx(array $where = [], array $group = [],  string $currency = '') 
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
        $this->db->SELECT("aph.id as id_bukti, aph.no_pelunasan as no_bukti, aph.tanggal_transaksi as tanggal_transaksi, aph.partner_id as id_partner,  CONCAT('Koreksi : ', ack.nama_koreksi, IF('$currency' = 'valas', 
                CONCAT(' - ',' Curr: ', (SELECT currency FROM currency_kurs WHERE id = currency_id), ' - Kurs: ', aphs.kurs), 
                CONCAT(' - ',(SELECT GROUP_CONCAT(no_invoice) as group_no FROM acc_pelunasan_hutang_invoice WHERE pelunasan_hutang_id = aph.id))
            ))  as uraian, IFNULL(SUM($total),0) as total_koreksi,  
                        (CASE 
                            WHEN aphs.selisih < 0 THEN abs($total) 
                            ELSE 0 
                        END) AS debit,
                        (CASE
                            WHEN aphs.selisih > 0 THEN ($total)
                            ELSE 0 
                        END) AS credit, aph.status, 'plh' as link, aphs.tipe_currency");
        $this->db->FROM('acc_pelunasan_hutang aph');
        $this->db->jOIN("acc_pelunasan_hutang_summary aphs","aph.id = aphs.pelunasan_hutang_id", "INNER");
        $this->db->JOIN("acc_pelunasan_koreksi ack","aphs.koreksi = ack.kode","left");
        return $this->db->get_compiled_select();
    } 


    function get_saldo_koreksi_normal(array $where = [], array $group = [],  string $currency = '') 
    {
        if(count($where) > 0){
            $this->db->where($where);
        }
        if (count($group)) {
            $this->db->group_by($group);
        }

        $total  = ($currency === 'valas')? 'aphs.selisih' : 'aphs.selisih';

        $this->db->where('aphs.keterangan <>', 'Uang Muka');
        // $this->db->where('aph.status', 'done');
        // $this->db->where('aphs.tipe_currency', 'Rp');
        $this->db->where('ack.koreksi_bb', 'true');
        $this->db->SELECT("aph.id as id_bukti, aph.no_pelunasan as no_bukti, aph.tanggal_transaksi as tanggal_transaksi, aph.partner_id as id_partner,  CONCAT('Koreksi : ', ack.nama_koreksi, IF('$currency' = 'valas', 
                CONCAT(' - ',' Curr: ', (SELECT currency FROM currency_kurs WHERE id = currency_id), ' - Kurs: ', aphs.kurs), 
                CONCAT(' - ',(SELECT GROUP_CONCAT(no_invoice) as group_no FROM acc_pelunasan_hutang_invoice WHERE pelunasan_hutang_id = aph.id))
            ))  as uraian, IFNULL(SUM($total),0) as total_koreksi,  
                        (CASE 
                            WHEN aphs.selisih < 0 THEN abs($total) 
                            ELSE 0 
                        END) AS debit,
                        (CASE
                            WHEN aphs.selisih > 0 THEN ($total)
                            ELSE 0 
                        END) AS credit, aph.status, 'plh' as link, aphs.tipe_currency");
        $this->db->FROM('acc_pelunasan_hutang aph');
        $this->db->jOIN("acc_pelunasan_hutang_summary aphs","aph.id = aphs.pelunasan_hutang_id", "INNER");
        $this->db->JOIN("acc_pelunasan_koreksi ack","aphs.koreksi = ack.kode","left");
        return $this->db->get_compiled_select();
    } 


    function get_saldo_koreksi_split(array $where = [] , array $group = [], string $currency = '')
    {

        if (count($where) > 0) {
            $this->db->where($where);
        }
        if (count($group)) {
            // $this->db->group_by(implode(', ', $group));
            $this->db->group_by($group);
        }

        $total_m_koreksi  = ($currency === 'valas') ? 'IF(aphsk.koreksi_tanda = "-", (aphsk.nominal * -1), aphsk.nominal)' : 'IF(aphsk.koreksi_tanda = "-", (aphsk.nominal * -1) * aphs.kurs, aphsk.nominal*aphs.kurs)';


        $this->db->where('aphsk.koreksi_id <>', 'uang_muka');
        // $this->db->where('aphs.keterangan <>', 'Uang Muka');
        // $this->db->where('aph.status', 'done');
        // $this->db->where('aphs.tipe_currency', 'Rp');
        // $this->db->where('ack.koreksi_bb', 'true');
        $this->db->SELECT("aph.id as id_bukti, aph.no_pelunasan as no_bukti, aph.tanggal_transaksi as tanggal_transaksi, aph.partner_id as id_partner,  CONCAT('Koreksi : ', GROUP_CONCAT(ack.nama_koreksi SEPARATOR ', '), IF('$currency' = 'valas', 
                CONCAT(' - ',' Curr: ', (SELECT currency FROM currency_kurs WHERE id = currency_id), ' - Kurs: ', aphs.kurs), 
                CONCAT(' - ',(SELECT GROUP_CONCAT(no_invoice) as group_no FROM acc_pelunasan_hutang_invoice WHERE pelunasan_hutang_id = aph.id))
            ))  as uraian, 
                CASE
                    WHEN (aphs.selisih) > 0 THEN
                                /* D - C */
                                SUM(
                                    CASE WHEN aphsk.posisi = 'D'
                                        THEN   ($total_m_koreksi)
                                        ELSE 0
                                    END
                                )
                                -
                                SUM(
                                    CASE WHEN aphsk.posisi = 'C'
                                        THEN   ($total_m_koreksi)
                                        ELSE 0
                                    END
                                )

                    WHEN (aphs.selisih) < 0 THEN
                                /* C - D */
                                SUM(
                                    CASE WHEN aphsk.posisi = 'C'
                                        THEN   ($total_m_koreksi)
                                        ELSE 0
                                    END
                                )
                                -
                                SUM(
                                    CASE WHEN aphsk.posisi = 'D'
                                        THEN   ($total_m_koreksi)
                                        ELSE 0
                                    END
                                )
                    ELSE 0
                END AS total_koreksi,
                CASE
                        WHEN (
                                CASE
                                    WHEN aphs.selisih < 0 THEN
                                        SUM(CASE WHEN aphsk.posisi='D'
                                                THEN ($total_m_koreksi)
                                                ELSE 0 END)
                                        -
                                        SUM(CASE WHEN aphsk.posisi='C'
                                                THEN ($total_m_koreksi)
                                                ELSE 0 END)
                                    WHEN aphs.selisih > 0 THEN
                                        SUM(CASE WHEN aphsk.posisi='C'
                                                THEN ($total_m_koreksi)
                                                ELSE 0 END)
                                        -
                                        SUM(CASE WHEN aphsk.posisi='D'
                                                THEN ($total_m_koreksi)
                                                ELSE 0 END)
                                    ELSE 0
                                END
                            ) > 0
                            THEN ABS(
                                CASE
                                    WHEN aphs.selisih < 0 THEN
                                        SUM(CASE WHEN aphsk.posisi='D'
                                                THEN ($total_m_koreksi)
                                                ELSE 0 END)
                                        -
                                        SUM(CASE WHEN aphsk.posisi='C'
                                                THEN ($total_m_koreksi)
                                                ELSE 0 END)
                                    WHEN aphs.selisih > 0 THEN
                                        SUM(CASE WHEN aphsk.posisi='C'
                                                THEN ($total_m_koreksi)
                                                ELSE 0 END)
                                        -
                                        SUM(CASE WHEN aphsk.posisi='D'
                                                THEN ($total_m_koreksi)
                                                ELSE 0 END)
                                    ELSE 0
                                END
                            )
                        ELSE 0
                END AS debit,
                CASE
                        WHEN (
                                CASE
                                    WHEN aphs.selisih < 0 THEN
                                        SUM(CASE WHEN aphsk.posisi='D'
                                                THEN ($total_m_koreksi)
                                                ELSE 0 END)
                                        -
                                        SUM(CASE WHEN aphsk.posisi='C'
                                                THEN ($total_m_koreksi)
                                                ELSE 0 END)
                                    WHEN aphs.selisih > 0 THEN
                                        SUM(CASE WHEN aphsk.posisi='C'
                                                THEN ($total_m_koreksi)
                                                ELSE 0 END)
                                        -
                                        SUM(CASE WHEN aphsk.posisi='D'
                                                THEN ($total_m_koreksi)
                                                ELSE 0 END)
                                    ELSE 0
                                END
                            ) < 0
                            THEN ABS(
                                CASE
                                    WHEN aphs.selisih < 0 THEN
                                        SUM(CASE WHEN aphsk.posisi='D'
                                                THEN ($total_m_koreksi)
                                                ELSE 0 END)
                                        -
                                        SUM(CASE WHEN aphsk.posisi='C'
                                                THEN ($total_m_koreksi)
                                                ELSE 0 END)
                                    WHEN aphs.selisih > 0 THEN
                                        SUM(CASE WHEN aphsk.posisi='C'
                                                THEN ($total_m_koreksi)
                                                ELSE 0 END)
                                        -
                                        SUM(CASE WHEN aphsk.posisi='D'
                                                THEN ($total_m_koreksi)
                                                ELSE 0 END)
                                    ELSE 0
                                END
                            )
                        ELSE 0
                END AS credit,
              
                aph.status, 'plh' as link, aphs.tipe_currency");
        $this->db->FROM('acc_pelunasan_hutang aph');
        $this->db->jOIN("acc_pelunasan_hutang_summary aphs","aph.id = aphs.pelunasan_hutang_id", "INNER");
        $this->db->jOIN("acc_pelunasan_hutang_summary_koreksi aphsk", "aphs.id = aphsk.pelunasan_summary_id", "INNER");
        $this->db->JOIN("acc_pelunasan_koreksi ack","aphsk.koreksi_id = ack.kode","left");
        return $this->db->get_compiled_select();
    }


    function get_saldo_kas_kurs_akhir_bulan(array $where = [], array $group = [], string $currency = '')
    {
        if (count($where) > 0) {
            $this->db->where($where);
        }
        if (count($group)) {
            $this->db->group_by($group);
        }

        if ($currency === 'valas') {
            $this->db->where('akab.curr', 'IDR');
        } else {
            $this->db->where_not_in('akab.curr', 'IDR');
        }

        $this->db->select("akab.id as id_bukti,  akab.no as no_bukti, 
                            CONCAT(LAST_DAY(CONCAT(akab.bulan,'-01')), ' 00:00:00') as  tanggal_transaksi,  akk.partner_id as id_partner,
                            CONCAT('Kurs Akhir Bulan : ' , akk.no_kk , ' ',akab.curr , ' ',akab.kurs) as uraian,
                            IFNULL(sum(ab.nominal),0) as total_koreksi,
                           SUM(
                                CASE 
                                    WHEN ab.nominal < 0 THEN ABS(ab.nominal)
                                    ELSE 0
                                END
                            ) as debit,

                           SUM(
                                CASE 
                                    WHEN ab.nominal > 0 THEN ab.nominal
                                    ELSE 0
                                END
                            ) as credit,
                            CASE 
                                WHEN akab.status = 'confirm' THEN 'done'
                                ELSE akab.status
                            END AS status, 
                            'kab' as link,
                            'Rp' as tipe_currency");
        $this->db->from("acc_kas_keluar akk");
        $this->db->join("acc_kas_keluar_detail akkd", "akkd.kas_keluar_id = akk.id", "inner");
        $this->db->join("acc_kas_bank_giro_kurs_akhir_bulan ab", "akkd.id =  ab.kas_id", "INNER");
        $this->db->join("acc_kurs_akhir_bulan akab", "akab.no = ab.no_kab", "INNER");
        $sql = $this->db->get_compiled_select();
        return $sql;
    }

    function get_saldo_giro_kurs_akhir_bulan(array $where = [], array $group = [], string $currency = '')
    {
        if (count($where) > 0) {
            $this->db->where($where);
        }
        if (count($group)) {
            $this->db->group_by($group);
        }

        if ($currency === 'valas') {
            $this->db->where('akab.curr', 'IDR');
        } else {
            $this->db->where_not_in('akab.curr', 'IDR');
        }

        $this->db->select("akab.id as id_bukti,  akab.no as no_bukti, 
                            CONCAT(LAST_DAY(CONCAT(akab.bulan,'-01')), ' 00:00:00') as  tanggal_transaksi,  agk.partner_id as id_partner,
                            CONCAT('Kurs Akhir Bulan : ' , agk.no_gk , ' ',akab.curr , ' ',akab.kurs) as uraian,
                            IFNULL(sum(ab.nominal),0) as total_koreksi,
                           SUM(
                                CASE 
                                    WHEN ab.nominal < 0 THEN ABS(ab.nominal)
                                    ELSE 0
                                END
                            ) as debit,

                           SUM(
                                CASE 
                                    WHEN ab.nominal > 0 THEN ab.nominal
                                    ELSE 0
                                END
                            ) as credit,
                            CASE 
                                WHEN akab.status = 'confirm' THEN 'done'
                                ELSE akab.status
                            END AS status, 
                            'kab' as link,
                            'Rp' as tipe_currency");
        $this->db->from("acc_giro_keluar agk");
        $this->db->join("acc_giro_keluar_detail agkd", "agkd.giro_keluar_id = agk.id", "inner");
        $this->db->join("acc_kas_bank_giro_kurs_akhir_bulan ab", "agkd.id =  ab.kas_id", "INNER");
        $this->db->join("acc_kurs_akhir_bulan akab", "akab.no = ab.no_kab", "INNER");
        $sql = $this->db->get_compiled_select();
        return $sql;
    }

    
    function get_saldo_bank_kurs_akhir_bulan(array $where = [], array $group = [], string $currency = '')
    {
        if (count($where) > 0) {
            $this->db->where($where);
        }
        if (count($group)) {
            $this->db->group_by($group);
        }

        if ($currency === 'valas') {
            $this->db->where('akab.curr', 'IDR');
        } else {
            $this->db->where_not_in('akab.curr', 'IDR');
        }

        $this->db->select("akab.id as id_bukti,  akab.no as no_bukti, 
                            CONCAT(LAST_DAY(CONCAT(akab.bulan,'-01')), ' 00:00:00')as  tanggal_transaksi,  abk.partner_id as id_partner,
                            CONCAT('Kurs Akhir Bulan : ' , abk.no_bk , ' ',akab.curr , ' ',akab.kurs) as uraian,
                            IFNULL(sum(ab.nominal),0) as total_koreksi,
                            SUM(
                                CASE 
                                    WHEN ab.nominal < 0 THEN ABS(ab.nominal)
                                    ELSE 0
                                END
                            ) as debit,

                            SUM(
                                CASE 
                                    WHEN ab.nominal > 0 THEN ab.nominal
                                    ELSE 0
                                END
                            ) as credit,
                            CASE 
                                WHEN akab.status = 'confirm' THEN 'done'
                                ELSE akab.status
                            END AS status, 
                            'kab' as link,
                            'Rp' as tipe_currency");
        $this->db->from("acc_bank_keluar abk");
        $this->db->join("acc_bank_keluar_detail abkd", "abkd.bank_keluar_id = abk.id", "inner");
        $this->db->join("acc_kas_bank_giro_kurs_akhir_bulan ab", "abkd.id =  ab.kas_id", "INNER");
        $this->db->join("acc_kurs_akhir_bulan akab", "akab.no = ab.no_kab", "INNER");
        $sql = $this->db->get_compiled_select();
        return $sql;
    }

    function get_saldo_retur_kurs_akhir_bulan(array $where = [], array $group = [], string $currency = '')
    {
        if (count($where) > 0) {
            $this->db->where($where);
        }
        if (count($group)) {
            $this->db->group_by($group);
        }

        if ($currency === 'valas') {
            $this->db->where('akab.curr', 'IDR');
        } else {
            $this->db->where_not_in('akab.curr', 'IDR');
        }

        $this->db->select("akab.id as id_bukti, akab.no as no_bukti, 
                            CONCAT(LAST_DAY(CONCAT(akab.bulan,'-01')), ' 00:00:00')  as  tanggal_transaksi,  arp.id_supplier as id_partner,
                            CONCAT('Kurs Akhir Bulan : ' , akab.curr , ' '  ,akab.kurs) as uraian,
                            IFNULL(sum(arpab.nominal),0) as total_koreksi,
                           SUM(
                                CASE 
                                    WHEN arpab.nominal < 0 THEN ABS(arpab.nominal)
                                    ELSE 0
                                END
                            ) as debit,

                           SUM(
                                CASE 
                                    WHEN arpab.nominal > 0 THEN arpab.nominal
                                    ELSE 0
                                END
                            ) as credit,
                            CASE 
                                WHEN akab.status = 'confirm' THEN 'done'
                                ELSE akab.status
                            END AS status,
                            'kab' as link,
                            'Rp' as tipe_currency");
        $this->db->from("invoice_retur arp");
        $this->db->join("acc_retur_kurs_akhir_bulan arpab", "arp.id =  arpab.retur_id", "INNER");
        $this->db->join("acc_kurs_akhir_bulan akab", "akab.no = arpab.no_kab", "INNER");
        $sql = $this->db->get_compiled_select();
        return $sql;
    }


    function get_saldo_koreksi(array $where = [], string $group = '', string $currency = '')
    {

        $where_tipe_cur = ($currency === 'valas') ? 'Valas' : 'Rp';
        $where_normal   = ['aphs.mode' => 'normal', 'aph.status' => 'done', 'aphs.keterangan <> ' => '', 'aphs.tipe_currency' => $where_tipe_cur, 'ack.koreksi_bb' => 'true'];
        $sub_query_normal = $this->get_saldo_koreksi_normal($where_normal, array('aph.partner_id', 'aphs.id'), $currency);
        $where_split    = ['aphs.mode'=> 'split',  'aph.status' => 'done', 'aphs.keterangan <> ' => '', 'aphs.tipe_currency' => $where_tipe_cur,   'aphsk.head' => 'false','ack.koreksi_bb' => 'true'];
        $sub_query_split  = $this->get_saldo_koreksi_split($where_split, ['aph.partner_id', 'aphs.id'], $currency);  
        $where_kab_kas    = ['akk.status' => 'confirm', 'akab.status' => 'confirm', 'ab.tipe' => 'kas_keluar'];
        $sub_query_akhir_bulan_kas = $this->get_saldo_kas_kurs_akhir_bulan($where_kab_kas, ['akk.partner_id'], $currency);
        $where_kab_giro    = ['agk.status' => 'confirm', 'akab.status' => 'confirm', 'ab.tipe' => 'giro_keluar'];
        $sub_query_akhir_bulan_giro = $this->get_saldo_giro_kurs_akhir_bulan($where_kab_giro, ['agk.partner_id'], $currency);
        $where_kab_bank    = ['abk.status' => 'confirm', 'akab.status' => 'confirm', 'ab.tipe' => 'bank_keluar'];
        $sub_query_akhir_bulan_bank = $this->get_saldo_bank_kurs_akhir_bulan($where_kab_bank, ['abk.partner_id'], $currency);
        $where_kab_retur    = ['arp.status' => 'done', 'akab.status' => 'confirm', 'arpab.tipe' => 'retur_pembelian'];
        $sub_query_akhir_bulan_retur = $this->get_saldo_retur_kurs_akhir_bulan($where_kab_retur, ['arp.id_supplier'], $currency);

        $this->db->select("
                        (id_bukti)        AS id_bukti,
                        (no_bukti)        AS no_bukti,
                        (tanggal_transaksi)  AS tgl,
                        (id_partner)      AS id_partner,
                        (uraian)          AS uraian,

                        SUM(total_koreksi)   AS total_koreksi,

                        SUM(debit)           AS debit,
                        SUM(credit)          AS credit,
                        (status)          AS status,
                        (link)            AS link,
                        ");
        $this->db->FROM("( ({$sub_query_normal}) UNION ALL ({$sub_query_split}) UNION ALL ({$sub_query_akhir_bulan_kas}) UNION ALL ({$sub_query_akhir_bulan_giro}) UNION ALL ({$sub_query_akhir_bulan_bank}) UNION ALL ({$sub_query_akhir_bulan_retur}) ) as t");

        if(count($where) > 0) {
            $this->db->where($where);
        }

        if ($group) {
            $this->db->group_by($group);
        }
        $sql = $this->db->get_compiled_select();
        // var_dump($sql);
        // die; 
        return $sql;
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
            $where_koreksi = ['tanggal_transaksi >= '=> $tgldari, 'tanggal_transaksi <= '=> $tglsampai, 'status'=>'done', 'tipe_currency' => 'Rp'];
        } else {
           $cr_condition = ($currency === 'valas')? '<>' : '';
            
           $where_utang = ['created_at >= '=> $tgldari, 'created_at <= '=> $tglsampai, 'status'=>'done', 'matauang ' . $cr_condition => 1 ];
           $where_pelunasan = ['aph.tanggal_transaksi >= '=> $tgldari, 'aph.tanggal_transaksi  <= '=> $tglsampai, 'aphm.currency_id ' .$cr_condition => 1];
           $where_retur = ['aph.tanggal_transaksi >= '=> $tgldari, 'aph.tanggal_transaksi  <= '=> $tglsampai, 'aphm.currency_id ' .$cr_condition => 1];
           $where_um    = ['aph.tanggal_transaksi >= '=> $tgldari, 'aph.tanggal_transaksi <= '=> $tglsampai, 'status'=>'done','aphs.tipe_currency ' .$cr_condition => 'Rp'];
           $where_koreksi = ['tanggal_transaksi >= '=> $tgldari, 'tanggal_transaksi <= '=> $tglsampai, 'status'=>'done', 'tipe_currency ' .$cr_condition => 'Rp'];
        }

        // saldo berdasakran periode berjalan
        $subquery_invoice      = $this->get_saldo_utang($where_utang, 'id_supplier',$currency);
        $subquery_pelunasan    = $this->get_saldo_pelunasan($where_pelunasan, 'aph.partner_id',$currency);
        $subquery_retur        = $this->get_saldo_retur($where_retur, 'aph.partner_id',$currency);
        $subquery_um           = $this->get_saldo_um($where_um, 'aph.partner_id',$currency);
        $subquery_koreksi       = $this->get_saldo_koreksi($where_koreksi, 'id_partner',$currency);

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
            $where_koreksi = ['tanggal_transaksi >= '=> $tgldari, 'tanggal_transaksi <= '=> $tglsampai, 'status'=>'done', 'tipe_currency' => 'Rp'];
        } else {
           $cr_condition = ($currency === 'valas')? '<>' : '';
            
           $where_utang = ['created_at >= '=> $tgldari, 'created_at <= '=> $tglsampai, 'status'=>'done', 'matauang ' . $cr_condition => 1 ];
           $where_pelunasan = ['aph.tanggal_transaksi >= '=> $tgldari, 'aph.tanggal_transaksi  <= '=> $tglsampai, 'aphm.currency_id ' .$cr_condition => 1];
           $where_retur = ['aph.tanggal_transaksi >= '=> $tgldari, 'aph.tanggal_transaksi  <= '=> $tglsampai, 'aphm.currency_id ' .$cr_condition => 1];
           $where_um    = ['aph.tanggal_transaksi >= '=> $tgldari, 'aph.tanggal_transaksi <= '=> $tglsampai, 'status'=>'done','aphs.tipe_currency ' .$cr_condition => 'Rp'];
           $where_koreksi = ['tanggal_transaksi >= '=> $tgldari, 'tanggal_transaksi <= '=> $tglsampai, 'status'=>'done', 'tipe_currency ' .$cr_condition => 'Rp'];
        }

        $subquery_invoice      = $this->get_saldo_utang($where_utang, 'no_invoice',$currency);
        $subquery_pelunasan    = $this->get_saldo_pelunasan($where_pelunasan, 'aph.no_pelunasan',$currency);
        $subquery_retur        = $this->get_saldo_retur($where_retur, 'aph.no_pelunasan',$currency);
        $subquery_um           = $this->get_saldo_um($where_um,'aph.no_pelunasan',$currency);
        $subquery_koreksi       = $this->get_saldo_koreksi($where_koreksi, 'no_bukti, id_partner',$currency);

        if(count($where) > 0){
            $this->db->where($where);
        }

        $union_sql = $subquery_invoice . ' UNION ALL ' . $subquery_pelunasan . ' UNION ALL ' . $subquery_retur . ' UNION ALL ' . $subquery_um . ' UNION ALL ' . $subquery_koreksi;

        $this->db->SELECT('id_bukti, no_bukti, tgl, id_partner, uraian, debit, credit, status, link');
        $this->db->from(" ( ({$subquery_invoice}) UNION ALL ({$subquery_pelunasan}) UNION ALL ({$subquery_retur}) UNION ALL ({$subquery_um}) UNION ALL ({$subquery_koreksi}) ) as sub");
        $this->db->order_by('tgl','asc');
        $this->db->order_by('debit','desc');
        // $this->db->order_by('credit','asc');
        $query = $this->db->get();
        return $query->result();
    }



}