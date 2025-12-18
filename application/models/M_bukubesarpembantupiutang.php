<?php defined('BASEPATH') or exit('No Direct Script Acces Allowed');

/**
 * 
 */

class M_bukubesarpembantupiutang extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library('periodesaldo');
    }



    public function get_list_bukubesar($tgldari, $tglsampai, $checkhidden, array $where = [], array $not_in = [], string $currency = '')
    {
        // saldo sebelum periode berjalan
        $subquery_kas_sblm     = $this->get_saldo_sblm($tgldari, 'kas_um', $currency);
        $subquery_faktur_sblm   = $this->get_saldo_sblm($tgldari, 'faktur', $currency);
        $subquery_diskon_sblm   = $this->get_saldo_sblm($tgldari, 'diskon', $currency);
        $subquery_pelunasan_sblm = $this->get_saldo_sblm($tgldari, 'pelunasan', $currency);
        $subquery_retur_sblm    = $this->get_saldo_sblm($tgldari, 'retur', $currency);
        $subquery_um_sblm       = $this->get_saldo_sblm($tgldari, 'um', $currency);
        $subquery_koreksi_sblm   = $this->get_saldo_sblm($tgldari, 'koreksi', $currency);
        $subquery_deposit_sblm   = $this->get_saldo_sblm($tgldari, 'deposit', $currency);
        $subquery_deposit_pel_sblm   = $this->get_saldo_sblm($tgldari, 'deposit_pel', $currency);
        $subquery_refund_sblm   = $this->get_saldo_sblm($tgldari, 'refund', $currency);

        // // saldo berdasakran periode berjalan
        // $subquery_faktur       = $this->get_saldo_piutang(['tanggal >= ' => $tgldari, 'tanggal <= ' => $tglsampai, 'status' => 'confirm'], 'partner_id', '', $currency);
        // $subquery_diskon       = $this->get_saldo_diskon(['tanggal >= ' => $tgldari, 'tanggal <= ' => $tglsampai, 'status' => 'confirm', 'sub.total_all <>' => 0], 'partner_id', '', $currency);
        // $subquery_pelunasan    = $this->get_saldo_pelunasan(['app.tanggal_transaksi >= ' => $tgldari, 'app.tanggal_transaksi  <= ' => $tglsampai], 'app.partner_id', '', $currency);
        // $subquery_retur        = $this->get_saldo_retur(['app.tanggal_transaksi >= ' => $tgldari, 'app.tanggal_transaksi  <= ' => $tglsampai], 'app.partner_id', '', $currency);
        // $subquery_um           = $this->get_saldo_um(['app.tanggal_transaksi >= ' => $tgldari, 'app.tanggal_transaksi <= ' => $tglsampai, 'status' => 'done', 'apps.tipe_currency' => 'Rp'], 'app.partner_id', '', $currency);
        // $subquery_korksi       = $this->get_saldo_koreksi(['app.tanggal_transaksi >= ' => $tgldari, 'app.tanggal_transaksi <= ' => $tglsampai, 'status' => 'done', 'apps.tipe_currency' => 'Rp'], 'app.partner_id', '', $currency);


        if ($currency === 'all' || $currency == 'rp') {
            $where_utang = ['tanggal >= ' => $tgldari, 'tanggal <= ' => $tglsampai, 'status' => 'confirm'];
            $where_diskon = ['tanggal >= ' => $tgldari, 'tanggal <= ' => $tglsampai, 'status' => 'confirm', 'sub.total_all <>' => 0];
            $where_pelunasan = ['app.tanggal_transaksi >= ' => $tgldari, 'app.tanggal_transaksi  <= ' => $tglsampai];
            $where_retur = ['app.tanggal_transaksi >= ' => $tgldari, 'app.tanggal_transaksi  <= ' => $tglsampai];
            $where_um    = ['app.tanggal_transaksi >= ' => $tgldari, 'app.tanggal_transaksi <= ' => $tglsampai, 'status' => 'done', 'apps.tipe_currency' => 'Rp'];
            $where_koreksi = ['app.tanggal_transaksi >= ' => $tgldari, 'app.tanggal_transaksi <= ' => $tglsampai, 'status' => 'done', 'apps.tipe_currency' => 'Rp'];
        } else {
            $cr_condition = ($currency === 'valas') ? '<>' : '';
            
            $where_utang = ['tanggal >= ' => $tgldari, 'tanggal <= ' => $tglsampai, 'status' => 'confirm', 'kurs ' . $cr_condition => 1];
            $where_diskon = ['tanggal >= ' => $tgldari, 'tanggal <= ' => $tglsampai, 'status' => 'confirm', 'sub.total_all <>' => 0, 'kurs ' . $cr_condition => 1];
            $where_pelunasan = ['app.tanggal_transaksi >= ' => $tgldari, 'app.tanggal_transaksi  <= ' => $tglsampai, 'appm.currency_id ' . $cr_condition => 1];
            $where_retur = ['app.tanggal_transaksi >= ' => $tgldari, 'app.tanggal_transaksi  <= ' => $tglsampai, 'appm.currency_id ' . $cr_condition => 1];
            $where_um    = ['app.tanggal_transaksi >= ' => $tgldari, 'app.tanggal_transaksi <= ' => $tglsampai, 'status' => 'done', 'apps.tipe_currency ' . $cr_condition => 'Rp'];
            $where_koreksi = ['app.tanggal_transaksi >= ' => $tgldari, 'app.tanggal_transaksi <= ' => $tglsampai, 'status' => 'done', 'apps.tipe_currency ' . $cr_condition => 'Rp'];
            // $where_deposit = ['app.tanggal_transaksi >= ' => $tgldari, 'app.tanggal_transaksi <= ' => $tglsampai, 'app.status' => 'done', 'apps.tipe_currency ' . $cr_condition => 'Rp', 'appsk.lunas' => 0];
            // $where_deposit_pel = ['app.tanggal_transaksi >= ' => $tgldari, 'app.tanggal_transaksi <= ' => $tglsampai, 'app.status' => 'done', 'apps.tipe_currency ' . $cr_condition => 'Rp', 'appsk.lunas' => 1];
        }
        
        $where_deposit = ['app.tanggal_transaksi >= ' => $tgldari, 'app.tanggal_transaksi <= ' => $tglsampai, 'app.status' => 'done',  'appsk.lunas' => 0];
        $where_deposit_pel = ['app.tanggal_transaksi >= ' => $tgldari, 'app.tanggal_transaksi <= ' => $tglsampai, 'app.status' => 'done',  'appsk.lunas' => 1];
        // saldo berdasakran periode berjalan
        $where_kas_um = ['tanggal >= ' => $tgldari, 'tanggal <= ' => $tglsampai];
        $subquery_kas           = $this->get_saldo_kas_um($where_kas_um, 'partner_id', $currency);
        $subquery_faktur        = $this->get_saldo_piutang($where_utang, 'partner_id', $currency);
        $subquery_diskon        = $this->get_saldo_diskon($where_diskon, 'partner_id', $currency);
        $subquery_pelunasan     = $this->get_saldo_pelunasan($where_pelunasan, 'app.partner_id', $currency);
        $subquery_retur         = $this->get_saldo_retur($where_retur, 'app.partner_id', $currency);
        $subquery_um            = $this->get_saldo_um($where_um, 'app.partner_id', $currency);
        $subquery_koreksi       = $this->get_saldo_koreksi($where_koreksi, 'app.partner_id', $currency);
        $subquery_deposit       = $this->get_saldo_deposit($where_deposit, 'app.partner_id', $currency);
        $subquery_deposit_pel   = $this->get_saldo_deposit($where_deposit_pel, 'app.partner_id', $currency);
        $subquery_refund        = $this->get_saldo_refund($where_kas_um, 'partner_id', $currency);


        $cr_condition = ($currency === 'valas') ? 'p.saldo_awal_piutang_valas' : 'p.saldo_awal_piutang';
        if ($checkhidden == 'true') {
            $this->db->where("(
                    ($cr_condition
                        - IFNULL(kas_um_sblm.total_kas_um, 0)
                        + IFNULL(piutang_sblm.total_piutang, 0) 
                        - IFNULL(pelunasan_sblm.total_pelunasan, 0) 
                        - IFNULL(retur_sblm.total_retur, 0) 
                        - IFNULL(um_sblm.total_uang_muka, 0) 
                        - IFNULL(diskon_sblm.total_diskon, 0)
                        - IFNULL(koreksi_sblm.total_koreksi, 0)
                        + IFNULL(refund_sblm.total_refund, 0)
                    ) <> 0
                    OR
                    (IFNULL(piutang.total_piutang, 0)
                        + IFNULL(kas_um.total_kas_um, 0)
                        + IFNULL(pelunasan.total_pelunasan, 0)
                        + IFNULL(retur.total_retur, 0)
                        + IFNULL(um.total_uang_muka, 0)
                        + IFNULL(diskon.total_diskon, 0)
                        + IFNULL(koreksi.total_koreksi, 0)
                        + IFNULL(refund.total_refund, 0)
                    ) <> 0
                )
            ", null, false);
        }


        if (count($where) > 0) {
            $this->db->where($where);
        }

        if (count($not_in) > 0) {
            foreach ($not_in as $field => $arr) {
                $this->db->where_not_in($field, $arr);
            }
        }

        // $this->db->where_in('p.id', array(4195,4435,4494,5132,4210,5166,552));
        $this->db->select("p.id, p.nama, $cr_condition as saldo_awal_piutang,
                        ($cr_condition - IFNULL(kas_um_sblm.total_kas_um,0) + IFNULL(piutang_sblm.total_piutang,0) - IFNULL(pelunasan_sblm.total_pelunasan,0) - IFNULL(retur_sblm.total_retur, 0) - IFNULL(diskon_sblm.total_diskon,0)- IFNULL(um_sblm.total_uang_muka,0) - (IFNULL(koreksi_sblm.total_koreksi,0))) + IFNULL(refund_sblm.total_refund, 0) as saldo_awal_final,
                        IFNULL(piutang.total_piutang,0) as total_piutang,
                        IFNULL(piutang.dpp_piutang,0) as dpp_piutang,
                        IFNULL(piutang.ppn_piutang,0) as ppn_piutang,
                        IFNULL(piutang.total_piutang_dpp_ppn,0) as total_piutang_dpp_ppn,
                        IFNULL(pelunasan.total_pelunasan,0) as total_pelunasan,
                        IFNULL(retur.total_retur,0) as total_retur,
                        IFNULL(retur.dpp_retur,0) as dpp_retur,
                        IFNULL(retur.ppn_retur,0) as ppn_retur,
                        IFNULL(retur.total_retur_dpp_ppn,0) as total_retur_dpp_ppn,
                        IFNULL(diskon.dpp_diskon,0) as dpp_diskon,
                        IFNULL(diskon.ppn_diskon,0) as ppn_diskon,
                        IFNULL(diskon.total_diskon_dpp_ppn,0) as total_diskon_dpp_ppn,
                        IFNULL(um.total_uang_muka,0) as total_uang_muka,
                        IFNULL(koreksi.total_koreksi,0) as total_koreksi, 
                        IFNULL(kas_um.total_kas_um, 0) as total_kas_um,
                        IFNULL(depo.total_deposit, 0) as total_deposit,
                        IFNULL(depo_pel.total_deposit, 0) as total_deposit_pel,
                        IFNULL(refund.total_refund, 0) as total_refund
                        ");
        $this->db->from('partner p');
        $this->db->join("($subquery_faktur_sblm) as piutang_sblm", "piutang_sblm.id_partner = p.id", "left");
        $this->db->join("($subquery_faktur) as piutang", "piutang.id_partner = p.id", "left");
        $this->db->join("($subquery_pelunasan_sblm) as pelunasan_sblm", "pelunasan_sblm.id_partner = p.id", "left");
        $this->db->join("($subquery_pelunasan) as pelunasan", "pelunasan.id_partner = p.id", "left");
        $this->db->join("($subquery_retur_sblm) as retur_sblm", "retur_sblm.id_partner = p.id", "left");
        $this->db->join("($subquery_retur) as retur", "retur.id_partner = p.id", "left");
        $this->db->join("($subquery_um_sblm) as um_sblm", "um_sblm.id_partner = p.id", "left");
        $this->db->join("($subquery_um) as um", "um.id_partner = p.id", "left");
        $this->db->join("($subquery_koreksi_sblm) as koreksi_sblm", "koreksi_sblm.id_partner = p.id", "left");
        $this->db->join("($subquery_koreksi) as koreksi", "koreksi.id_partner = p.id", "left");
        $this->db->join("($subquery_diskon) as diskon", "diskon.id_partner = p.id", "left");
        $this->db->join("($subquery_diskon_sblm) as diskon_sblm", "diskon_sblm.id_partner = p.id", "left");
        $this->db->JOIN("($subquery_kas_sblm) as kas_um_sblm", "kas_um_sblm.partner_id = p.id", "LEFT");
        $this->db->JOIN("($subquery_kas) as kas_um", "kas_um.partner_id = p.id", "LEFT");
        $this->db->JOIN("($subquery_deposit_sblm) as depo_sblm", "depo_sblm.partner_id = p.id", "LEFT");
        $this->db->JOIN("($subquery_deposit) as depo", "depo.partner_id = p.id", "LEFT");
        $this->db->JOIN("($subquery_deposit_pel_sblm) as depo_pel_sblm", "depo_pel_sblm.partner_id = p.id", "LEFT");
        $this->db->JOIN("($subquery_deposit_pel) as depo_pel", "depo_pel.partner_id = p.id", "LEFT");
        $this->db->JOIN("($subquery_refund_sblm) as refund_sblm", "refund_sblm.partner_id = p.id", "LEFT");
        $this->db->JOIN("($subquery_refund) as refund", "refund.partner_id = p.id", "LEFT");
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

        if($tipe == 'kas_um'){
            $tmp_where = ['tanggal >= ' => $tgl_dari, 'tanggal <= ' => $tgl_sampai];
            $subquery  = $this->get_saldo_kas_um($tmp_where, 'partner_id', $currency);

        } else if($tipe == 'refund'){
            $tmp_where = ['tanggal >= ' => $tgl_dari, 'tanggal <= ' => $tgl_sampai];
            $subquery  = $this->get_saldo_refund($tmp_where, 'partner_id', $currency);

        } else if ($tipe == 'faktur') {
            $tmp_where = ['tanggal >= ' => $tgl_dari, 'tanggal <= ' => $tgl_sampai, 'status' => 'confirm'];
            if ($currency === 'valas') {
                $cr_condition = ($currency === 'valas') ? '<>' : '';
                $tmp_where = array_merge($tmp_where, ['kurs ' . $cr_condition => 1]);
            }
            $subquery  = $this->get_saldo_piutang($tmp_where, 'partner_id', $currency);
        } else if ($tipe == 'diskon') {
            $tmp_where = ['tanggal >= ' => $tgl_dari, 'tanggal <= ' => $tgl_sampai, 'status' => 'confirm', 'sub.total_all <>' => 0];
            if ($currency === 'valas') {
                $cr_condition = ($currency === 'valas') ? '<>' : '';
                $tmp_where = array_merge($tmp_where, ['kurs ' . $cr_condition => 1]);
            }
            $subquery  = $this->get_saldo_diskon($tmp_where, 'partner_id', $currency);
        } else if ($tipe == 'pelunasan') {
            $tmp_where = ['app.tanggal_transaksi >= ' => $tgl_dari, 'app.tanggal_transaksi  <= ' => $tgl_sampai];
            if ($currency === 'valas') {
                $cr_condition = ($currency === 'valas') ? '<>' : '';
                $tmp_where = array_merge($tmp_where, ['appm.currency_id ' . $cr_condition => 1]);
            }
            $subquery  = $this->get_saldo_pelunasan($tmp_where, 'app.partner_id', $currency);
        } else if ($tipe == 'retur') {
            $tmp_where = ['app.tanggal_transaksi >= ' => $tgl_dari, 'app.tanggal_transaksi  <= ' => $tgl_sampai];
            if ($currency === 'valas') {
                $cr_condition = ($currency === 'valas') ? '<>' : '';
                $tmp_where = array_merge($tmp_where, ['appm.currency_id ' . $cr_condition => 1]);
            }
            $subquery  = $this->get_saldo_retur($tmp_where, 'app.partner_id', $currency);
        } else if ($tipe == 'um') {
            $tmp_where = ['app.tanggal_transaksi >= ' => $tgl_dari, 'app.tanggal_transaksi <= ' => $tgl_sampai, 'status' => 'done'];
            // $tmp_where = ['app.tanggal_transaksi >= ' => $tgl_dari, 'app.tanggal_transaksi <= ' => $tgl_sampai, 'status' => 'done', 'apps.tipe_currency' => 'Rp'];
            // if ($currency === 'valas') {
            // }
            $cr_condition = ($currency === 'valas') ? '<>' : '';
            $tmp_where = array_merge($tmp_where, ['apps.tipe_currency ' . $cr_condition => 'Rp']);
            $subquery  = $this->get_saldo_um($tmp_where, 'app.partner_id', $currency);
        } else if ($tipe == 'deposit') {
            // $tmp_where = ['app.tanggal_transaksi >= ' => $tgl_dari, 'app.tanggal_transaksi <= ' => $tgl_sampai, 'app.status' => 'done', 'apps.tipe_currency' => 'Rp', 'appsk.lunas' => 0];
            $tmp_where = ['app.tanggal_transaksi >= ' => $tgl_dari, 'app.tanggal_transaksi <= ' => $tgl_sampai, 'app.status' => 'done',  'appsk.lunas' => 0];
            // if ($currency === 'valas') {
            //     $cr_condition = ($currency === 'valas') ? '<>' : '';
                // $tmp_where = array_merge($tmp_where, ['apps.tipe_currency ' . $cr_condition => 'Rp']);
            // }
            $subquery  = $this->get_saldo_deposit($tmp_where, 'app.partner_id', $currency);
        } else if ($tipe == 'deposit_pel') {
            $tmp_where = ['app.tanggal_transaksi >= ' => $tgl_dari, 'app.tanggal_transaksi <= ' => $tgl_sampai, 'app.status' => 'done',  'appsk.lunas'=> 1];
            // if ($currency === 'valas') {
            //     $cr_condition = ($currency === 'valas') ? '<>' : '';
                // $tmp_where = array_merge($tmp_where, ['apps.tipe_currency ' . $cr_condition => 'Rp']);
            // }
            $subquery  = $this->get_saldo_deposit($tmp_where, 'app.partner_id', $currency);
        } else { // koreksi
            // $tmp_where = ['app.tanggal_transaksi >= ' => $tgl_dari, 'app.tanggal_transaksi <= ' => $tgl_sampai, 'status' => 'done', 'apps.tipe_currency' => 'Rp'];
            $tmp_where = ['app.tanggal_transaksi >= ' => $tgl_dari, 'app.tanggal_transaksi <= ' => $tgl_sampai, 'status' => 'done'];
            // if ($currency === 'valas') {
            // }
            $cr_condition = ($currency === 'valas') ? '<>' : '';
            $tmp_where = array_merge($tmp_where, ['apps.tipe_currency ' . $cr_condition => 'Rp']);
            $subquery  = $this->get_saldo_koreksi($tmp_where, 'app.partner_id', $currency);
        }
        return $subquery;
    }

    var $where_jenis_transaksi_um = array('um_penjualan');

    function get_list_coa_um_by_transaksi()
    {
        $this->db->select('kode_coa');
        $this->db->where_in('jenis_transaksi', $this->where_jenis_transaksi_um);
        $rows = $this->db->get('acc_coa')->result_array();

        $list_coa = array_column($rows, 'kode_coa');
        // Cegah error kalau COA kosong
        if (empty($list_coa)) {
            $list_coa = ['__EMPTY__']; 
        }
        return array_map('strval', $list_coa);
    }

    function query1() 
    {
        $this->db->where_in('abmd.kode_coa', $this->get_list_coa_um_by_transaksi());
        $this->db->where('abmd.lunas',0);
        $this->db->where('abm.status','confirm');
        $this->db->select("abmd.id,(abm.no_bm) as no_bukti, abm.partner_id,  abm.tanggal, abmd.currency_id, ck1.currency, abmd.kurs,abmd.nominal, 'bank' as tipe2, abmd.uraian, abmd.kode_coa, abm.status");
        $this->db->from("acc_bank_masuk abm");
        $this->db->join("acc_bank_masuk_detail abmd ", "abm.id = abmd.bank_masuk_id", "left");
        $this->db->join("currency_kurs ck1 ", "abmd.currency_id = ck1.id", "left");
        return $query1_sql = $this->db->get_compiled_select();
    }

    function query2()
    {
        $this->db->where_in('akmd.kode_coa', $this->get_list_coa_um_by_transaksi());
        $this->db->where('akmd.lunas',0);
        $this->db->where('akm.status','confirm');
        $this->db->select("akmd.id,(akm.no_km) as no_bukti, akm.partner_id, akm.tanggal, akmd.currency_id, ck2.currency, akmd.kurs,akmd.nominal, 'kas' as tipe2, akmd.uraian, akmd.kode_coa, akm.status");
        $this->db->from("acc_kas_masuk akm");
        $this->db->join("acc_kas_masuk_detail akmd ", "akm.id = akmd.kas_masuk_id", "left");
        $this->db->join("currency_kurs ck2 ", "akmd.currency_id = ck2.id", "left");
        return  $query2_sql = $this->db->get_compiled_select();
    }

    function query3()
    {
        $this->db->where_in('agmd.kode_coa', $this->get_list_coa_um_by_transaksi());
        $this->db->where('agmd.lunas',0);
        $this->db->where('agm.status','confirm');
        $this->db->select("agmd.id,(agm.no_gm) as no_bukti,agm.partner_id, agm.tanggal, agmd.currency_id, ck3.currency, agmd.kurs,agmd.nominal, 'giro' as tipe2, iF(agm.transinfo !='', agm.transinfo, agm.lain2 ) as uraian, agmd.kode_coa, agm.status");
        $this->db->from("acc_giro_masuk agm");
        $this->db->join("acc_giro_masuk_detail agmd ", "agm.id = agmd.giro_masuk_id", "left");
        $this->db->join("currency_kurs ck3 ", "agmd.currency_id = ck3.id", "left");
        return $query3_sql = $this->db->get_compiled_select();

    }

    function get_saldo_kas_um(array $where = [], string $group = '', string $currency = '') 
    {
        $union_sql  = $this->query1() . " UNION ALL ". $this->query2() . " UNION ALL " . $this->query3();
        if(count($where) > 0) {
            $this->db->where($where);
        }

        if($group) {
            $this->db->group_by($group);
        }

        $total_kas_um  = ($currency === 'valas') ? "sum(nominal)" : "sum(nominal*kurs)";

        $this->db->select("id as id_bukti, no_bukti as id_bukti_ecr, no_bukti,  tanggal as tgl, partner_id, IF(currency_id  != 1, CONCAT('Uang Muka (Outstanding) - ','Kurs : ', kurs) , 'Uang Muka (Outstanding)' ) as uraian, 
                        IFNULL({$total_kas_um}, 0) as total_kas_um,
                        0 as debit,
                        IFNULL({$total_kas_um}, 0) as credit,
                        status,
                        tipe2 as link,
                        0 as dpp,
                        0 as ppn,
                        0 as tppn");
        $this->db->FROM("($union_sql) as sub");
        return $query = $this->db->get_compiled_select();

    }

    function get_saldo_piutang_sub(array $where = [], string $group = '', string $currency = '')
    {
        if (count($where) > 0) {
            $this->db->where($where);
        }

        if ($group) {
            // $this->db->group_by('a.no_faktur_internal');
            $this->db->group_by($group);
        }
        // $ppn    = ($currency === 'valas') ? "ROUND(ROUND(ROUND(SUM(CAST(b.qty*b.harga  AS DECIMAL(20,4))), 2)*11/12, 2) *a.tax_value, 2)" : "ROUND(  ROUND( ROUND(ROUND(SUM(CAST(b.qty * b.harga AS DECIMAL(20,4))), 0) * 11 / 12, 0)* a.tax_value, 0)* a.kurs_nominal, 0)"; 
        $dpp    = ($currency === 'valas') ? "ROUND(SUM(CAST(b.qty*b.harga AS DECIMAL(20,4))), 2)" : "ROUND(SUM(CAST(b.qty*b.harga AS DECIMAL(20,4)))*a.kurs_nominal, 0)";
        $ppn    = ($currency === 'valas') ? "ROUND(ROUND(SUM(CAST(b.qty*b.harga  AS DECIMAL(20,4)))*11/12, 2) *a.tax_value, 2)" : "ROUND(  ROUND( ROUND(ROUND(SUM(CAST(b.qty * b.harga AS DECIMAL(20,4))), 0) * 11 / 12, 0)* a.tax_value, 0)* a.kurs_nominal, 0)"; 
        $total  = ($currency === 'valas') ? "IFNULL(ROUND(SUM(CAST(b.qty*b.harga AS DECIMAL(20,4))), 2),0) + IFNULL(ROUND(ROUND(SUM(CAST(b.qty*b.harga  AS DECIMAL(20,4)))*11/12, 2) *a.tax_value, 2),0)" : "IFNULL(ROUND(SUM(CAST(b.qty*b.harga AS DECIMAL(20,4)))*a.kurs_nominal, 0),0) + IFNULL(ROUND(  ROUND( ROUND(ROUND(SUM(CAST(b.qty * b.harga AS DECIMAL(20,4))), 0) * 11 / 12, 0)* a.tax_value, 0)* a.kurs_nominal, 0), 0)";

        $this->db->SELECT("a.id,a.no_faktur_internal,
                IFNULL($dpp,0) as dpp_piutang,
                IFNULL($ppn,0) as ppn_piutang,
                ($total) as total_all  ");
        $this->db->FROM("acc_faktur_penjualan a");
        $this->db->JOIN("acc_faktur_penjualan_detail b", "b.faktur_id = a.id", "INNER");
        // $this->where('a.id = fak.id');
        return $this->db->get_compiled_select();

    }


    function get_saldo_piutang(array $where = [], string $group = '', string $currency = '')
    {
        $sub_query = $this->get_saldo_piutang_sub($where, 'a.id', $currency);
        if (count($where) > 0) {
            $this->db->where($where);
        }


        if ($group) {
            $this->db->group_by($group);
        }


        // ROUND(CAST(nilai AS DECIMAL(20,4)), 2)

        // $total  = ($currency === 'valas') ? "" : "";

        // $total_dpp = ($currency === 'valas') ? "ROUND(CAST(fakd.qty*fakd.harga as DECIMAL(20,4)),2)" :  "ROUND(CAST(fakd.qty*fakd.harga*fak.kurs_nominal as DECIMAL(20,4)))";
        // $total_ppn = ($currency === 'valas') ? "ROUND(CAST(fakd.qty*fakd.harga * 11/12 * fak.tax_value as DECIMAL(20,4)),2))" : "ROUND(CAST(fakd.qty*fakd.harga*fak.kurs_nominal * 11/12 * fak.tax_value AS DECIMAL(20,4)))";
        // $total_all = ($currency === 'valas') ? "ROUND(CAST(fakd.qty*fakd.harga as DECIMAL(20,4)),2) +  ROUND(CAST(fakd.qty*fakd.harga * 11/12 * fak.tax_value as DECIMAL(20,4)),2))" : "ROUND(CAST(fakd.qty*fakd.harga*fak.kurs_nominal as DECIMAL(20,4))) +  (ROUND(CAST(fakd.qty*fakd.harga*fak.kurs_nominal * 11/12 * fak.tax_value AS DECIMAL(20,4))))";

        // $total = " ";

        $this->db->SELECT("fak.id as id_bukti, fak.no_faktur as id_bukti_ecr,fak.no_faktur_internal as no_bukti,tanggal as tgl, partner_id as id_partner,
        CONCAT(
            'Penjualan: ', tipe, ' - ',
            IF(no_sj = '', '', CONCAT(' No SJ: ',no_sj)),
            IF('$currency' = 'valas', 
                CONCAT(' - ',' Curr: ', (SELECT currency FROM currency_kurs WHERE id = kurs), ' - Kurs: ', kurs_nominal), 
                ''
            )
        ) as uraian,
        IFNULL(sum(sub.total_all), 0) as total_piutang,
        IFNULL(sum(sub.total_all), 0) as debit,
        0 as credit,
        status, 'fak' as link,
        sum(sub.dpp_piutang) as dpp_piutang,
        sum(sub.ppn_piutang) as ppn_piutang,
        sum(sub.total_all) as total_piutang_dpp_ppn");
        $this->db->FROM('acc_faktur_penjualan fak');
        // $this->db->JOIN("acc_faktur_penjualan_detail fakd", "fak.id = fakd.faktur_id", "INNER");
        $this->db->JOIN("($sub_query) as sub", "sub.id = fak.id", "LEFT");
        return $this->db->get_compiled_select();
    }

    function get_saldo_diskon_sub(array $where = [], string $group = '', string $currency = '')
    {
        unset($where['sub.total_all <>']);
        if (count($where) > 0) {
            $this->db->where($where);
        }

        if ($group) {
            $this->db->group_by($group);
        }

        // $dpp    = ($currency === 'valas') ? "ROUND(SUM(CAST(b.diskon AS DECIMAL(20,4))), 2)" : "ROUND(SUM(CAST(b.diskon AS DECIMAL(20,4)))*a.kurs_nominal , 0)";
        // $ppn    = ($currency === 'valas') ? "ROUND(SUM(CAST(b.diskon * 11/12 * a.tax_value AS DECIMAL(20,4))), 2)" : "ROUND(ROUND(ROUND(SUM(CAST(b.diskon AS DECIMAL(20,4))) * 11/12, 0) * a.tax_value, 0) *a.kurs_nominal , 0)";
        // $total = ($currency === 'valas') ? "IFNULL(ROUND(SUM(CAST(b.diskon AS DECIMAL(20,4))), 2),0) + IFNULL(ROUND(SUM(CAST(b.diskon * 11/12 * a.tax_value AS DECIMAL(20,4))), 2), 0)" : "IFNULL(ROUND(SUM(CAST(b.diskon AS DECIMAL(20,4)))*a.kurs_nominal , 0),0) + IFNULL(ROUND(SUM(CAST(b.diskon * 11/12 * a.tax_value AS DECIMAL(20,4))), 0), 0)";

        $dpp    = ($currency === 'valas') ? "ROUND(SUM(CAST(b.qty * b.harga AS DECIMAL(20,4))) * (a.nominal_diskon / 100),2)" : "ROUND(SUM(CAST(b.qty * b.harga AS DECIMAL(20,4))) * (a.nominal_diskon / 100) * a.kurs_nominal,0)";
        $ppn    = ($currency === 'valas') ? "ROUND(ROUND(ROUND(ROUND(SUM(CAST(b.qty * b.harga AS DECIMAL(20,4))) * (a.nominal_diskon / 100), 2) * 11/12, 2) * a.tax_value, 2),2)" : "ROUND(ROUND(ROUND(ROUND(SUM(CAST(b.qty * b.harga AS DECIMAL(20,4))) * (a.nominal_diskon / 100), 0) * 11/12, 0) * a.tax_value, 0) * a.kurs_nominal,0)";
        $total = ($currency === 'valas') ? "IFNULL(ROUND(SUM(CAST(b.qty * b.harga AS DECIMAL(20,4))) * (a.nominal_diskon / 100),2),0) + IFNULL(ROUND(ROUND(ROUND(ROUND(SUM(CAST(b.qty * b.harga AS DECIMAL(20,4))) * (a.nominal_diskon / 100), 2) * 11/12, 2) * a.tax_value, 2),2), 0)" : "IFNULL(ROUND(SUM(CAST(b.qty * b.harga AS DECIMAL(20,4))) * (a.nominal_diskon / 100) * a.kurs_nominal,0),0) + IFNULL(ROUND(ROUND(ROUND(ROUND(SUM(CAST(b.qty * b.harga AS DECIMAL(20,4))) * (a.nominal_diskon / 100), 0) * 11/12, 0) * a.tax_value, 0) * a.kurs_nominal,0),0)";

        $this->db->SELECT("a.id,a.no_faktur_internal,
                IFNULL($dpp,0) as dpp_diskon,
                IFNULL($ppn,0) as ppn_diskon,
                ($total) as total_all  ");
        $this->db->FROM("acc_faktur_penjualan a");
        $this->db->JOIN("acc_faktur_penjualan_detail b", "b.faktur_id = a.id", "INNER");
        return $this->db->get_compiled_select();

    }


    function get_saldo_diskon(array $where = [], string $group = '', string $currency = '')
    {

        $sub_query = $this->get_saldo_diskon_sub($where, 'a.id', $currency);

        if (count($where) > 0) {
            $this->db->where($where);
        }

        if ($group) {
            $this->db->group_by($group);
        }
        $this->db->having('total_diskon <> 0');

      
        $this->db->SELECT("fak.id as id_bukti, fak.no_faktur as id_bukti_ecr, fak.no_faktur_internal as no_bukti,tanggal as tgl, partner_id as id_partner,
        CONCAT(
            'Diskon: ', 
            IF(no_sj = '', '', CONCAT(' No SJ: ',no_sj)),
            IF('$currency' = 'valas', 
                CONCAT(' - ',' Curr: ', (SELECT currency FROM currency_kurs WHERE id = kurs), ' - Kurs: ', kurs_nominal), 
                ''
            )
        ) as uraian,
        IFNULL((sub.total_all), 0) as total_diskon,
        0 as debit,
        IFNULL((sub.total_all), 0) as credit,
        status, 'fak' as link,
        sum(sub.dpp_diskon) as dpp_diskon,
        sum(sub.ppn_diskon) as ppn_diskon,
        sum(sub.total_all) as total_diskon_dpp_ppn");
        $this->db->FROM('acc_faktur_penjualan fak');
        // $this->db->JOIN("acc_faktur_penjualan_detail fakd", "fak.id = fakd.faktur_id", "INNER");
        $this->db->JOIN("($sub_query) as sub", "sub.id = fak.id", "LEFT");
        return $this->db->get_compiled_select();
    }


    function get_saldo_pelunasan(array $where = [], string $group = '', string $currency = '')
    {
        if (count($where) > 0) {
            $this->db->where($where);
        }
        if ($group) {
            $this->db->group_by($group);
        }

        $total  = ($currency === 'valas') ? 'appm.total_valas' : 'appm.total_rp';

        $this->db->where('app.status', 'done');
        $this->db->where('appm.tipe <> ', 'um');
        $this->db->where('appm.tipe <> ', 'retur');
        $this->db->where('appm.tipe <> ', 'koreksi');
        $this->db->where('appm.tipe <> ', 'depo');
        $this->db->SELECT("app.id as id_bukti,app.id as id_bukti_ecr, app.no_pelunasan as no_bukti, app.tanggal_transaksi as tgl, app.partner_id as id_partner, CONCAT('Pelunasan: ', (SELECT GROUP_CONCAT(no_faktur) as group_faktur FROM acc_pelunasan_piutang_faktur WHERE pelunasan_piutang_id = app.id),' - ', 
            
            IF('$currency' = 'valas', 
                CONCAT(GROUP_CONCAT(appm.no_bukti), ' Curr: ', (SELECT currency FROM currency_kurs WHERE id = currency_id), '  Kurs: ', appm.kurs, ' '), 
                GROUP_CONCAT(appm.no_bukti)
            )) as uraian, IFNULL(SUM(CAST( abs($total) AS DECIMAL(20,2))),0) as total_pelunasan,   0 as debit , IFNULL(SUM(CAST( abs($total) AS DECIMAL(20,2))),0) as credit, app.status, 'plp' as link, 
            0 as dpp,
            0 as ppn,
            0 as total_dpp_ppn ");
        $this->db->FROM('acc_pelunasan_piutang app');
        $this->db->jOIN("acc_pelunasan_piutang_metode appm", "app.id = appm.pelunasan_piutang_id", "INNER");
        return $this->db->get_compiled_select();
    }

    function get_saldo_retur(array $where = [], string $group = '', string $currency = '')
    {
        if (count($where) > 0) {
            $this->db->where($where);
        }
        if ($group) {
            $this->db->group_by($group);
        }
        $total  = ($currency === 'valas') ? 'appm.total_valas' : 'appm.total_rp';

        $dpp_retur = ($currency === 'valas') ? "ROUND(sum(CAST(retd.qty*retd.harga AS DECIMAL(20,4))),2)" : "ROUND(sum(CAST(retd.qty*retd.harga AS DECIMAL(20,4))) *ret.kurs_nominal,0) ";
        $ppn_retur = ($currency === 'valas') ? "ROUND(ROUND(SUM(CAST(retd.qty*retd.harga  AS DECIMAL(20,4)))*11/12, 2) *ret.tax_value, 2)" : " ROUND(ROUND(ROUND(ROUND(SUM(CAST(retd.qty * retd.harga AS DECIMAL(20,4))), 0) * 11 / 12, 0)* ret.tax_value, 0)* ret.kurs_nominal, 0)";
        $total_retur = ($currency === 'valas') ?  "ROUND(sum(CAST(retd.qty*retd.harga AS DECIMAL(20,4))),2) + ROUND(ROUND(SUM(CAST(retd.qty*retd.harga  AS DECIMAL(20,4)))*11/12, 2) *ret.tax_value, 2)" : "ROUND(sum(CAST(retd.qty*retd.harga AS DECIMAL(20,4))) *ret.kurs_nominal,0) + ROUND(ROUND(ROUND(ROUND(SUM(CAST(retd.qty * retd.harga AS DECIMAL(20,4))), 0) * 11 / 12, 0)* ret.tax_value, 0)* ret.kurs_nominal, 0)";

        $this->db->where('app.status', 'done');
        $this->db->where('appm.tipe', 'retur');
        $this->db->SELECT("app.id as id_bukti,app.id as id_bukti_ecr, app.no_pelunasan as no_bukti, app.tanggal_transaksi as tgl, app.partner_id as id_partner, CONCAT('Retur: ', (SELECT GROUP_CONCAT(no_faktur) as group_faktur FROM acc_pelunasan_piutang_faktur WHERE pelunasan_piutang_id = app.id), 
            
            IF('$currency' = 'valas', 
                GROUP_CONCAT(' - ',appm.no_bukti,' Curr: ', (SELECT currency FROM currency_kurs WHERE id = currency_id), '  Kurs: ', appm.kurs,' '), 
                GROUP_CONCAT(' - ',,appm.no_bukti)
            )) as uraian, IFNULL(SUM($total),0) as total_retur,  0 as debit ,  IFNULL(SUM($total),0)  as credit, app.status, 'plp' as link,
            sum(dpp_retur) as dpp_retur,
            sum(ppn_retur) as ppn_retur,
            sum(total_retur_dpp_ppn) as total_retur_dpp_ppn");
        $this->db->FROM('acc_pelunasan_piutang app');
        $this->db->jOIN("acc_pelunasan_piutang_metode appm", "app.id = appm.pelunasan_piutang_id", "INNER");
        $this->db->JOIN("( SELECT ret.id , 
                ($dpp_retur) as dpp_retur,
                ($ppn_retur) as ppn_retur,
                ($total_retur) as total_retur_dpp_ppn                
                FROM acc_retur_penjualan ret
                INNER JOIN acc_retur_penjualan_detail retd ON ret.id = retd.retur_id
                WHERE ret.status = 'confirm' and lunas = 1
                GROUP BY ret.id
                ) arp", "arp.id = appm.id_bukti", "INNER");
        return $this->db->get_compiled_select();
    }


    function get_saldo_um(array $where = [], string $group = '', string $currency = '')
    {
        if (count($where) > 0) {
            $this->db->where($where);
        }
        if ($group) {
            $this->db->group_by($group);
        }
        $total  = ($currency === 'valas') ? 'apps.total_pelunasan' : 'apps.total_pelunasan * apps.kurs';

        $this->db->where('apps.keterangan', 'Uang Muka');
        $this->db->where('app.status', 'done');
        // $this->db->where('apps.tipe_currency', 'Rp');
        $this->db->SELECT("app.id as id_bukti,app.id as id_bukti_ecr, app.no_pelunasan as no_bukti, app.tanggal_transaksi as tgl, app.partner_id as id_partner,  
                        CONCAT('Uang Muka : ', (SELECT GROUP_CONCAT(no_faktur) as group_faktur FROM acc_pelunasan_piutang_faktur WHERE pelunasan_piutang_id = app.id), ' ',  
                        IF('$currency' = 'valas', 
                                GROUP_CONCAT(' - ',(SELECT GROUP_CONCAT(no_bukti) as group_no FROM acc_pelunasan_piutang_metode WHERE pelunasan_piutang_id = app.id),' Curr: ', (SELECT currency FROM currency_kurs WHERE id = apps.currency_id), '  Kurs: ', apps.kurs,' '), 
                                GROUP_CONCAT(' - ',(SELECT GROUP_CONCAT(no_bukti) as group_no FROM acc_pelunasan_piutang_metode WHERE pelunasan_piutang_id = app.id))
                        )) as uraian, 

                        IFNULL(SUM($total),0) as total_uang_muka, 0  as debit, IFNULL(SUM($total),0)  as credit,  app.status, 'plp' as link,
                        0 as dpp,
                        0 as ppn,
                        0 as total_dpp_ppn");
        $this->db->FROM('acc_pelunasan_piutang app');
        $this->db->jOIN("acc_pelunasan_piutang_summary apps", "app.id = apps.pelunasan_piutang_id", "INNER");
        return $this->db->get_compiled_select();
    }

    function get_saldo_koreksi(array $where = [], string $group = '',  string $currency = '')
    {
        if (count($where) > 0) {
            $this->db->where($where);
        }
        if ($group) {
            $this->db->group_by($group);
        }
        $total  = ($currency === 'valas') ? 'IF(appsk.koreksi_tanda = "-", (appsk.nominal * -1), appsk.nominal)' : 'IF(appsk.koreksi_tanda = "-", (appsk.nominal * -1) * apps.kurs, appsk.nominal*apps.kurs)';

        $this->db->where('apps.keterangan <>', 'Uang Muka');
        $this->db->where('app.status', 'done');
        $this->db->where('appsk.head', 'false');
        $this->db->where('appsk.alat_pelunasan', 'false');
        // $this->db->where('appsk.head', 'false');
        // $this->db->where('appsk.koreksi_id <>','deposit');
        // $this->db->where('apps.tipe_currency', 'Rp');
        $this->db->where('ack.koreksi_bb', 'true');
        $this->db->SELECT("app.id as id_bukti, app.id as id_bukti_ecr, app.no_pelunasan as no_bukti, app.tanggal_transaksi as tgl, app.partner_id as id_partner,  CONCAT('Koreksi : ', ack.nama_koreksi, 
            COALESCE((SELECT GROUP_CONCAT(CONCAT(' - Curr : ' ,currency,' - Kurs : ',kurs, ' - Valas : ', total_valas)) FROM acc_pelunasan_piutang_metode appm Where appm.pelunasan_piutang_id = app.id AND appm.tipe = 'koreksi') , ''),
            IF('$currency' = 'valas', 
                CONCAT(' - ',' Curr: ', (SELECT currency FROM currency_kurs WHERE id = currency_id), ' - Kurs: ', apps.kurs), 
                CONCAT(' - ',(SELECT GROUP_CONCAT(no_faktur) as group_no FROM acc_pelunasan_piutang_faktur WHERE  pelunasan_piutang_id = app.id))
            ))  as uraian, IFNULL(SUM($total),0) as total_koreksi,  
                        (CASE 
                            WHEN apps.selisih < 0 THEN  CAST( SUM(abs($total)) AS DECIMAL(20,2))
                            ELSE 0 
                        END) AS debit,
                        (CASE
                            WHEN apps.selisih > 0 THEN CAST( SUM(abs($total)) AS DECIMAL(20,2))
                            ELSE 0 
                        END) AS credit, app.status, 'plp' as link,
                        0 as dpp,
                        0 as ppn,
                        0 as total_dpp_ppn");
        $this->db->FROM('acc_pelunasan_piutang app');
        $this->db->jOIN("acc_pelunasan_piutang_summary apps", "app.id = apps.pelunasan_piutang_id", "INNER");
        $this->db->jOIN("acc_pelunasan_piutang_summary_koreksi appsk", "apps.id = appsk.pelunasan_summary_id", "INNER");
        $this->db->JOIN("acc_pelunasan_koreksi_piutang ack", "appsk.koreksi_id = ack.kode", "left");
        return $this->db->get_compiled_select();
    }


    function get_saldo_deposit(array $where = [], string $group = '',  string $currency = '')
    {
        if (count($where) > 0) {
            $this->db->where($where);
        }
        if ($group) {
            $this->db->group_by($group);
        }

        $total  = ($currency === 'valas') ? 'appsk.nominal' : 'appsk.nominal*apps.kurs';

        $this->db->where('app.status', 'done');
        $this->db->where('appsk.head', 'false');
        $this->db->where('appsk.alat_pelunasan', 'true');
        // $this->db->where('apps.tipe_currency', 'Rp');
        $this->db->SELECT("app.id as id_bukti, app.id as id_bukti_ecr, app.no_pelunasan as no_bukti, app.tanggal_transaksi as tgl, app.partner_id,  CONCAT('Koreksi : ', ack.nama_koreksi, 
            COALESCE((SELECT GROUP_CONCAT(CONCAT(' - Curr : ' ,currency,' - Kurs : ',kurs, ' - Valas : ', total_valas)) FROM acc_pelunasan_piutang_metode appm Where appm.pelunasan_piutang_id = app.id AND appm.tipe = 'koreksi') , ''),
            IF('$currency' = 'valas', 
                CONCAT(' - ',' Curr: ', (SELECT currency FROM currency_kurs WHERE id = currency_id), ' - Kurs: ', apps.kurs), 
                CONCAT(' - ',(SELECT GROUP_CONCAT(no_faktur) as group_no FROM acc_pelunasan_piutang_faktur WHERE  pelunasan_piutang_id = app.id))
            ))  as uraian, IFNULL(SUM($total),0) as total_deposit,  
                        0 AS debit,
                         CAST(SUM($total) AS DECIMAL(20,2)) AS credit, app.status, 'plp' as link,
                        0 as dpp,
                        0 as ppn,
                        0 as total_dpp_ppn");
        $this->db->FROM('acc_pelunasan_piutang app');
        $this->db->jOIN("acc_pelunasan_piutang_summary apps", "app.id = apps.pelunasan_piutang_id", "INNER");
        $this->db->jOIN("acc_pelunasan_piutang_summary_koreksi appsk", "apps.id = appsk.pelunasan_summary_id", "INNER");
        $this->db->JOIN("acc_pelunasan_koreksi_piutang ack", "appsk.koreksi_id = ack.kode", "left");
        return $this->db->get_compiled_select();
    }


    var $where_jenis_transaksi_refund = array('piutang');

    function get_list_coa_by_transaksi()
    {
        $this->db->select('kode_coa');
        $this->db->where_in('jenis_transaksi', $this->where_jenis_transaksi_refund);
        $rows = $this->db->get('acc_coa')->result_array();

        $list_coa = array_column($rows, 'kode_coa');
        // Cegah error kalau COA kosong
        if (empty($list_coa)) {
            $list_coa = ['__EMPTY__']; 
        }
        return array_map('strval', $list_coa);
    }

    function query4()
    {
       
        $this->db->where_in('b.kode_coa', $this->get_list_coa_by_transaksi());
        $where = ["a.status" => 'confirm', "b.lunas" => 0];
        if (count($where) > 0) {
            $this->db->where($where);
        }
        $this->db->select("b.id,( a.no_bk) as no_bukti, a.tanggal, a.partner_id, b.currency_id, c.currency, b.kurs, b.nominal, 'giro' as tipe2, b.uraian,  a.status");
        $this->db->from("acc_bank_keluar a");
        $this->db->join("acc_bank_keluar_detail b ", "a.id = b.bank_keluar_id", "left");
        $this->db->join("currency_kurs c ", "b.currency_id = c.id", "left");
        return $query1_sql = $this->db->get_compiled_select();
    }

    function query5()
    {
        $this->db->where_in('e.kode_coa', $this->get_list_coa_by_transaksi());
        $where = ["h.status" => 'confirm', 'e.lunas' => 0];
        if (count($where) > 0) {
            $this->db->where($where);
        }
        $this->db->select("e.id,(h.no_kk) as no_bukti, h.tanggal, h.partner_id, e.currency_id, i.currency, e.kurs, e.nominal, 'giro' as tipe2, e.uraian, h.status");
        $this->db->from("acc_kas_keluar h");
        $this->db->join("acc_kas_keluar_detail e ", "h.id = e.kas_keluar_id", "left");
        $this->db->join("currency_kurs i ", "e.currency_id = i.id", "left");
        return  $query2_sql = $this->db->get_compiled_select();
    }

    function query6()
    {
       
        $this->db->where_in('g.kode_coa', $this->get_list_coa_by_transaksi());
        $where = ["f.status" => 'confirm', 'g.lunas' => 0];
        if (count($where) > 0) {
            $this->db->where($where);
        }
        $this->db->select("g.id,(f.no_gk) as no_bukti, f.tanggal, f.partner_id, g.currency_id, j.currency, g.kurs, g.nominal, 'giro' as tipe2, '' as uraian, f.status");
        $this->db->from("acc_giro_keluar f");
        $this->db->join("acc_giro_keluar_detail g ", "f.id = g.giro_keluar_id", "left");
        $this->db->join("currency_kurs j ", "g.currency_id = j.id", "left");
        return $query3_sql = $this->db->get_compiled_select();
    }

    function get_saldo_refund(array $where = [], string $group = '', string $currency = '') 
    {
        $union_sql  = $this->query4() . " UNION ALL ". $this->query5() . " UNION ALL " . $this->query6();
        if(count($where) > 0) {
            $this->db->where($where);
        }

        if($group) {
            $this->db->group_by($group);
        }

        $total_refund  = ($currency === 'valas') ? "sum(nominal)" : "sum(nominal*kurs)";

        $this->db->select("id as id_bukti, no_bukti as id_bukti_ecr, no_bukti,  tanggal as tgl, partner_id, IF(currency_id  != 1, CONCAT('Refund  - ','Kurs : ', kurs) , 'Refund ' ) as uraian, 
                        IFNULL({$total_refund}, 0) as total_refund,
                        IFNULL({$total_refund}, 0) as debit,
                        0 as credit,
                        status,
                        '' as link,
                        0 as dpp,
                        0 as ppn,
                        0 as tppn");
        $this->db->FROM("($union_sql) as sub");
        return $query = $this->db->get_compiled_select();

    }
    

    public function get_list_bukubesar_detail($tgldari, $tglsampai, $checkhidden, array $where = [], $currency)
    {
        // saldo sebelum periode berjalan
        $subquery_kas_sblm          = $this->get_saldo_sblm($tgldari, 'kas_um', $currency);
        $subquery_faktur_sblm       = $this->get_saldo_sblm($tgldari, 'faktur', $currency);
        $subquery_diskon_sblm       = $this->get_saldo_sblm($tgldari, 'diskon', $currency);
        $subquery_pelunasan_sblm    = $this->get_saldo_sblm($tgldari, 'pelunasan', $currency);
        $subquery_retur_sblm        = $this->get_saldo_sblm($tgldari, 'retur', $currency);
        $subquery_um_sblm           = $this->get_saldo_sblm($tgldari, 'um', $currency);
        $subquery_koreksi_sblm      = $this->get_saldo_sblm($tgldari, 'koreksi', $currency);
        $subquery_refund_sblm   = $this->get_saldo_sblm($tgldari, 'refund', $currency);

        // $subquery_deposit_sblm      = $this->get_saldo_sblm($tgldari, 'deposit', $currency);
        // $subquery_deposit_pel_sblm  = $this->get_saldo_sblm($tgldari, 'deposit_pel', $currency);

        //table currecny kurs 
        // 1 = IDR
        // 1 <> USD,EUR CNY,JPY

        if ($currency === 'all' || $currency == 'rp') {
            $where_utang = ['tanggal >= ' => $tgldari, 'tanggal <= ' => $tglsampai, 'status' => 'confirm'];
            $where_diskon = ['tanggal >= ' => $tgldari, 'tanggal <= ' => $tglsampai, 'status' => 'confirm', 'sub.total_all <>' => 0];
            $where_pelunasan = ['app.tanggal_transaksi >= ' => $tgldari, 'app.tanggal_transaksi  <= ' => $tglsampai];
            $where_retur = ['app.tanggal_transaksi >= ' => $tgldari, 'app.tanggal_transaksi  <= ' => $tglsampai];
            $where_um    = ['app.tanggal_transaksi >= ' => $tgldari, 'app.tanggal_transaksi <= ' => $tglsampai, 'status' => 'done', 'apps.tipe_currency' => 'Rp'];
            $where_koreksi = ['app.tanggal_transaksi >= ' => $tgldari, 'app.tanggal_transaksi <= ' => $tglsampai, 'status' => 'done', 'apps.tipe_currency' => 'Rp'];
            $where_deposit = ['app.tanggal_transaksi >= ' => $tgldari, 'app.tanggal_transaksi <= ' => $tglsampai, 'app.status' => 'done', 'apps.tipe_currency ' => 'Rp', 'appsk.lunas'=>0];
            $where_deposit_pel = ['app.tanggal_transaksi >= ' => $tgldari, 'app.tanggal_transaksi <= ' => $tglsampai, 'app.status' => 'done', 'apps.tipe_currency ' => 'Rp', 'appsk.lunas'=>1];
        } else {
            $cr_condition = ($currency === 'valas') ? '<>' : '';

            $where_utang = ['tanggal >= ' => $tgldari, 'tanggal <= ' => $tglsampai, 'status' => 'confirm', 'kurs ' . $cr_condition => 1];
            $where_diskon = ['tanggal >= ' => $tgldari, 'tanggal <= ' => $tglsampai, 'status' => 'confirm', 'sub.total_all <>' => 0];
            $where_pelunasan = ['app.tanggal_transaksi >= ' => $tgldari, 'app.tanggal_transaksi  <= ' => $tglsampai, 'appm.currency_id ' . $cr_condition => 1];
            $where_retur = ['app.tanggal_transaksi >= ' => $tgldari, 'app.tanggal_transaksi  <= ' => $tglsampai, 'appm.currency_id ' . $cr_condition => 1];
            $where_um    = ['app.tanggal_transaksi >= ' => $tgldari, 'app.tanggal_transaksi <= ' => $tglsampai, 'status' => 'done', 'apps.tipe_currency ' . $cr_condition => 'Rp'];
            $where_koreksi = ['app.tanggal_transaksi >= ' => $tgldari, 'app.tanggal_transaksi <= ' => $tglsampai, 'status' => 'done', 'apps.tipe_currency ' . $cr_condition => 'Rp'];
            $where_deposit = ['app.tanggal_transaksi >= ' => $tgldari, 'app.tanggal_transaksi <= ' => $tglsampai, 'app.status' => 'done', 'apps.tipe_currency ' . $cr_condition => 'Rp', 'appsk.lunas'=>0];
            $where_deposit_pel = ['app.tanggal_transaksi >= ' => $tgldari, 'app.tanggal_transaksi <= ' => $tglsampai, 'app.status' => 'done', 'apps.tipe_currency ' . $cr_condition => 'Rp', 'appsk.lunas'=>1];
        }

        // saldo berdasakran periode berjalan
        $where_kas_um           = ['tanggal >= ' => $tgldari, 'tanggal <= ' => $tglsampai];
        $subquery_kas           = $this->get_saldo_kas_um($where_kas_um, 'partner_id', $currency);
        $subquery_faktur        = $this->get_saldo_piutang($where_utang, 'partner_id', $currency);
        $subquery_diskon        = $this->get_saldo_diskon($where_diskon, 'partner_id', $currency);
        $subquery_pelunasan     = $this->get_saldo_pelunasan($where_pelunasan, 'app.partner_id', $currency);
        $subquery_retur         = $this->get_saldo_retur($where_retur, 'app.partner_id', $currency);
        $subquery_um            = $this->get_saldo_um($where_um, 'app.partner_id', $currency);
        $subquery_koreksi       = $this->get_saldo_koreksi($where_koreksi, 'app.partner_id', $currency);
        $subquery_refund        = $this->get_saldo_refund($where_kas_um, 'partner_id', $currency);
        // $subquery_deposit       = $this->get_saldo_deposit($where_deposit, 'app.partner_id', $currency);
        // $subquery_deposit_pel   = $this->get_saldo_deposit($where_deposit_pel, 'app.partner_id', $currency);

        $cr_condition = ($currency === 'valas') ? 'p.saldo_awal_piutang_valas' : 'p.saldo_awal_piutang';
        if ($checkhidden == 'true') {
            $this->db->where("(
                    ($cr_condition
                        - IFNULL(kas_um_sblm.total_kas_um, 0)
                        + IFNULL(piutang_sblm.total_piutang, 0) 
                        - IFNULL(pelunasan_sblm.total_pelunasan, 0) 
                        - IFNULL(retur_sblm.total_retur, 0) 
                        - IFNULL(um_sblm.total_uang_muka, 0) 
                        - IFNULL(diskon_sblm.total_diskon, 0)
                        - IFNULL(koreksi_sblm.total_koreksi, 0)
                        + IFNULL(refund_sblm.total_refund, 0)
                    ) <> 0
                    OR
                    (IFNULL(piutang.total_piutang, 0)
                        + IFNULL(kas_um.total_kas_um, 0)
                        + IFNULL(pelunasan.total_pelunasan, 0)
                        + IFNULL(retur.total_retur, 0)
                        + IFNULL(um.total_uang_muka, 0)
                        + IFNULL(diskon.total_diskon, 0)
                        + IFNULL(koreksi.total_koreksi, 0)
                        + IFNULL(refund.total_refund, 0)
                    ) <> 0
                )
            ", null, false);
        }


        if (count($where) > 0) {
            $this->db->where($where);
        }

        $this->db->select("p.id, p.nama, $cr_condition as saldo_awal_piutang,
                        ($cr_condition - IFNULL(kas_um_sblm.total_kas_um, 0) + IFNULL(piutang_sblm.total_piutang,0) - IFNULL(pelunasan_sblm.total_pelunasan,0) - IFNULL(retur_sblm.total_retur, 0) - IFNULL(diskon_sblm.total_diskon, 0) - IFNULL(um_sblm.total_uang_muka,0) - (IFNULL(koreksi_sblm.total_koreksi,0)) + IFNULL(refund_sblm.total_refund, 0) ) as saldo_awal_final,
                        IFNULL(piutang.total_piutang,0) as total_piutang,
                        IFNULL(pelunasan.total_pelunasan,0) as total_pelunasan,
                        IFNULL(retur.total_retur,0) as total_retur,
                        IFNULL(um.total_uang_muka,0) as total_uang_muka,
                        IFNULL(koreksi.total_koreksi,0) as total_koreksi ");
        $this->db->from('partner p');
        $this->db->join("($subquery_faktur_sblm) as piutang_sblm", "piutang_sblm.id_partner = p.id", "left");
        $this->db->join("($subquery_faktur) as piutang", "piutang.id_partner = p.id", "left");
        $this->db->join("($subquery_pelunasan_sblm) as pelunasan_sblm", "pelunasan_sblm.id_partner = p.id", "left");
        $this->db->join("($subquery_pelunasan) as pelunasan", "pelunasan.id_partner = p.id", "left");
        $this->db->join("($subquery_retur_sblm) as retur_sblm", "retur_sblm.id_partner = p.id", "left");
        $this->db->join("($subquery_retur) as retur", "retur.id_partner = p.id", "left");
        $this->db->join("($subquery_um_sblm) as um_sblm", "um_sblm.id_partner = p.id", "left");
        $this->db->join("($subquery_um) as um", "um.id_partner = p.id", "left");
        $this->db->join("($subquery_koreksi_sblm) as koreksi_sblm", "koreksi_sblm.id_partner = p.id", "left");
        $this->db->join("($subquery_koreksi) as koreksi", "koreksi.id_partner = p.id", "left");
        $this->db->join("($subquery_diskon) as diskon", "diskon.id_partner = p.id", "left");
        $this->db->join("($subquery_diskon_sblm) as diskon_sblm", "diskon_sblm.id_partner = p.id", "left");
        $this->db->JOIN("($subquery_kas_sblm) as kas_um_sblm", "kas_um_sblm.partner_id = p.id", "LEFT");
        $this->db->JOIN("($subquery_kas) as kas_um", "kas_um.partner_id = p.id", "LEFT");
        $this->db->JOIN("($subquery_refund_sblm) as refund_sblm", "refund_sblm.partner_id = p.id", "LEFT");
        $this->db->JOIN("($subquery_refund) as refund", "refund.partner_id = p.id", "LEFT");
        // $this->db->JOIN("($subquery_deposit_sblm) as depo_sblm", "depo_sblm.partner_id = p.id", "LEFT");
        // $this->db->JOIN("($subquery_deposit) as depo", "depo.partner_id = p.id", "LEFT");
        // $this->db->JOIN("($subquery_deposit_pel_sblm) as depo_pel_sblm", "depo_pel_sblm.partner_id = p.id", "LEFT");
        // $this->db->JOIN("($subquery_deposit_pel) as depo_pel", "depo_pel.partner_id = p.id", "LEFT");
        $this->db->order_by('p.nama');
        $query = $this->db->get();
        return $query->result();
    }

    function get_list_bukubesar_detail_by_kode($tgldari, $tglsampai, array $where = [], $currency)
    {

        //table currecny kurs 
        // 1 = IDR
        // 1 <> USD,EUR CNY,JPY

        if ($currency === 'all' || $currency === 'rp') {
            $where_utang = ['tanggal >= ' => $tgldari, 'tanggal <= ' => $tglsampai, 'status' => 'confirm'];
            $where_diskon = ['tanggal >= ' => $tgldari, 'tanggal <= ' => $tglsampai, 'status' => 'confirm', 'sub.total_all <>' => 0];
            $where_pelunasan = ['app.tanggal_transaksi >= ' => $tgldari, 'app.tanggal_transaksi  <= ' => $tglsampai];
            $where_retur = ['app.tanggal_transaksi >= ' => $tgldari, 'app.tanggal_transaksi  <= ' => $tglsampai];
            $where_um    = ['app.tanggal_transaksi >= ' => $tgldari, 'app.tanggal_transaksi <= ' => $tglsampai, 'status' => 'done', 'apps.tipe_currency' => 'Rp'];
            $where_koreksi = ['app.tanggal_transaksi >= ' => $tgldari, 'app.tanggal_transaksi <= ' => $tglsampai, 'status' => 'done', 'apps.tipe_currency' => 'Rp'];
            $where_deposit = ['app.tanggal_transaksi >= ' => $tgldari, 'app.tanggal_transaksi <= ' => $tglsampai, 'status' => 'done', 'apps.tipe_currency' => 'Rp', 'appsk.lunas'=>0];
            $where_deposit_pel = ['app.tanggal_transaksi >= ' => $tgldari, 'app.tanggal_transaksi <= ' => $tglsampai, 'status' => 'done', 'apps.tipe_currency' => 'Rp', 'appsk.lunas'=>1];
        } else {
            $cr_condition = ($currency === 'valas') ? '<>' : '';

            $where_utang = ['tanggal >= ' => $tgldari, 'tanggal <= ' => $tglsampai, 'status' => 'confirm', 'kurs ' . $cr_condition => 1];
            $where_diskon = ['tanggal >= ' => $tgldari, 'tanggal <= ' => $tglsampai, 'status' => 'confirm', 'kurs ' . $cr_condition => 1, 'sub.total_all <>' => 0];
            $where_pelunasan = ['app.tanggal_transaksi >= ' => $tgldari, 'app.tanggal_transaksi  <= ' => $tglsampai, 'appm.currency_id ' . $cr_condition => 1];
            $where_retur = ['app.tanggal_transaksi >= ' => $tgldari, 'app.tanggal_transaksi  <= ' => $tglsampai, 'appm.currency_id ' . $cr_condition => 1];
            $where_um    = ['app.tanggal_transaksi >= ' => $tgldari, 'app.tanggal_transaksi <= ' => $tglsampai, 'status' => 'done', 'apps.tipe_currency ' . $cr_condition => 'Rp'];
            $where_koreksi = ['app.tanggal_transaksi >= ' => $tgldari, 'app.tanggal_transaksi <= ' => $tglsampai, 'status' => 'done', 'apps.tipe_currency ' . $cr_condition => 'Rp'];
            $where_deposit = ['app.tanggal_transaksi >= ' => $tgldari, 'app.tanggal_transaksi <= ' => $tglsampai, 'status' => 'done', 'apps.tipe_currency ' . $cr_condition => 'Rp', 'appsk.lunas'=>0];
            $where_deposit_pel = ['app.tanggal_transaksi >= ' => $tgldari, 'app.tanggal_transaksi <= ' => $tglsampai, 'status' => 'done', 'apps.tipe_currency ' . $cr_condition => 'Rp', 'appsk.lunas'=>1];
        }

        $where_kas_um           = ['tanggal >= ' => $tgldari, 'tanggal <= ' => $tglsampai];
        $subquery_kas          = $this->get_saldo_kas_um($where_kas_um, 'partner_id', $currency);
        $subquery_faktur       = $this->get_saldo_piutang($where_utang, 'fak.no_faktur_internal', $currency);
        $subquery_diskon       = $this->get_saldo_diskon($where_diskon, 'fak.no_faktur_internal', $currency);
        $subquery_pelunasan    = $this->get_saldo_pelunasan($where_pelunasan, 'app.no_pelunasan', $currency);
        $subquery_retur        = $this->get_saldo_retur($where_retur, 'app.no_pelunasan', $currency);
        $subquery_um           = $this->get_saldo_um($where_um, 'app.no_pelunasan', $currency);
        $subquery_koreksi      = $this->get_saldo_koreksi($where_koreksi, 'app.no_pelunasan', $currency);
        $subquery_refund       = $this->get_saldo_refund($where_kas_um, 'partner_id', $currency);
        // $subquery_deposit      = $this->get_saldo_deposit($where_deposit, 'app.no_pelunasan', $currency);
        // $subquery_deposit_pel  = $this->get_saldo_deposit($where_deposit_pel, 'app.no_pelunasan', $currency);

        if (count($where) > 0) {
            $this->db->where($where);
        }

        $union_sql = $subquery_faktur . ' UNION ALL ' . $subquery_pelunasan . ' UNION ALL ' . $subquery_retur . ' UNION ALL ' . $subquery_um . ' UNION ALL ' . $subquery_koreksi . ' UNION ALL ' . $subquery_diskon . " UNION ALL ".$subquery_kas . " UNION ALL " . $subquery_refund;

        $this->db->SELECT('id_bukti, id_bukti_ecr,  no_bukti, tgl, id_partner, uraian, debit, credit, status, link');
        $this->db->from('(' . $union_sql . ') as sub');
        $this->db->order_by('tgl', 'asc');
        $this->db->order_by('no_bukti', 'asc');
        $this->db->order_by('debit', 'desc');
        // $this->db->order_by('credit','asc');
        $query = $this->db->get();
        return $query->result();
    }

    function get_list_golongan()
    {
        $this->db->order_by('id','asc');
        $query = $this->db->get('partner_gol');
        return $query->result();
    }
}
