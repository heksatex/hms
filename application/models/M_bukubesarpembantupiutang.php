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



    public function get_list_bukubesar($tgldari, $tglsampai, $checkhidden, array $where = [], array $not_in = [])
    {
        // saldo sebelum periode berjalan
        $subquery_faktur_sblm   = $this->get_saldo_sblm($tgldari, 'faktur');
        $subquery_diskon_sblm   = $this->get_saldo_sblm($tgldari, 'diskon');
        $subquery_pelunasan_sblm = $this->get_saldo_sblm($tgldari, 'pelunasan');
        $subquery_retur_sblm    = $this->get_saldo_sblm($tgldari, 'retur');
        $subquery_um_sblm       = $this->get_saldo_sblm($tgldari, 'um');
        $subquery_korksi_sblm   = $this->get_saldo_sblm($tgldari, 'koreksi');


        // saldo berdasakran periode berjalan
        $subquery_faktur       = $this->get_saldo_piutang(['tanggal >= ' => $tgldari, 'tanggal <= ' => $tglsampai, 'status' => 'confirm'], 'partner_id', '');
        $subquery_diskon       = $this->get_saldo_diskon(['tanggal >= ' => $tgldari, 'tanggal <= ' => $tglsampai, 'status' => 'confirm', 'sub.total_all <>' => 0], 'partner_id', '');
        $subquery_pelunasan    = $this->get_saldo_pelunasan(['app.tanggal_transaksi >= ' => $tgldari, 'app.tanggal_transaksi  <= ' => $tglsampai], 'app.partner_id', '');
        $subquery_retur        = $this->get_saldo_retur(['app.tanggal_transaksi >= ' => $tgldari, 'app.tanggal_transaksi  <= ' => $tglsampai], 'app.partner_id', '');
        $subquery_um           = $this->get_saldo_um(['app.tanggal_transaksi >= ' => $tgldari, 'app.tanggal_transaksi <= ' => $tglsampai, 'status' => 'done', 'apps.tipe_currency' => 'Rp'], 'app.partner_id', '');
        $subquery_korksi       = $this->get_saldo_koreksi(['app.tanggal_transaksi >= ' => $tgldari, 'app.tanggal_transaksi <= ' => $tglsampai, 'status' => 'done', 'apps.tipe_currency' => 'Rp'], 'app.partner_id', '');


        if ($checkhidden == 'true') {
            $this->db->where("(
                    (p.saldo_awal_piutang 
                        + IFNULL(piutang_sblm.total_piutang, 0) 
                        - IFNULL(pelunasan_sblm.total_pelunasan, 0) 
                        - IFNULL(retur_sblm.total_retur, 0) 
                        - IFNULL(um_sblm.total_uang_muka, 0) 
                        - IFNULL(diskon_sblm.total_diskon, 0)
                        - IFNULL(koreksi_sblm.total_koreksi, 0)
                    ) <> 0
                    OR
                    (IFNULL(piutang.total_piutang, 0)
                        + IFNULL(pelunasan.total_pelunasan, 0)
                        + IFNULL(retur.total_retur, 0)
                        + IFNULL(um.total_uang_muka, 0)
                        + IFNULL(diskon.total_diskon, 0)
                        + IFNULL(koreksi.total_koreksi, 0)
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
        $this->db->select("p.id, p.nama, p.saldo_awal_piutang,
                        (p.saldo_awal_piutang + IFNULL(piutang_sblm.total_piutang,0) - IFNULL(pelunasan_sblm.total_pelunasan,0) - IFNULL(retur_sblm.total_retur, 0) - IFNULL(diskon_sblm.total_diskon,0)- IFNULL(um_sblm.total_uang_muka,0) - (IFNULL(koreksi_sblm.total_koreksi,0)) ) as saldo_awal_final,
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
        $this->db->join("($subquery_korksi_sblm) as koreksi_sblm", "koreksi_sblm.id_partner = p.id", "left");
        $this->db->join("($subquery_korksi) as koreksi", "koreksi.id_partner = p.id", "left");
        $this->db->join("($subquery_diskon) as diskon", "diskon.id_partner = p.id", "left");
        $this->db->join("($subquery_diskon_sblm) as diskon_sblm", "diskon_sblm.id_partner = p.id", "left");
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

        if ($tipe == 'faktur') {
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
            $tmp_where = ['app.tanggal_transaksi >= ' => $tgl_dari, 'app.tanggal_transaksi <= ' => $tgl_sampai, 'status' => 'done', 'apps.tipe_currency' => 'Rp'];
            if ($currency === 'valas') {
                $cr_condition = ($currency === 'valas') ? '<>' : '';
                $tmp_where = array_merge($tmp_where, ['apps.tipe_currency ' . $cr_condition => 'Rp']);
            }
            $subquery  = $this->get_saldo_um($tmp_where, 'app.partner_id', $currency);
        } else { // koreksi
            $tmp_where = ['app.tanggal_transaksi >= ' => $tgl_dari, 'app.tanggal_transaksi <= ' => $tgl_sampai, 'status' => 'done', 'apps.tipe_currency' => 'Rp'];
            if ($currency === 'valas') {
                $cr_condition = ($currency === 'valas') ? '<>' : '';
                $tmp_where = array_merge($tmp_where, ['apps.tipe_currency ' . $cr_condition => 'Rp']);
            }
            $subquery  = $this->get_saldo_koreksi($tmp_where, 'app.partner_id', $currency);
        }
        return $subquery;
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

        $dpp    = ($currency === 'valas') ? "ROUND(SUM(CAST(b.qty*b.harga AS DECIMAL(20,4))), 2)" : "ROUND(SUM(CAST(b.qty*b.harga*a.kurs_nominal AS DECIMAL(20,4))), 0)";
        $ppn    = ($currency === 'valas') ? "ROUND(SUM(CAST(b.qty*b.harga*11/12*a.tax_value AS DECIMAL(20,4))), 2)" : "ROUND(SUM(CAST(b.qty*b.harga*11/12*a.tax_value*a.kurs_nominal AS DECIMAL(20,4))), 0)";
        $total = ($currency === 'valas') ? "ROUND(SUM(CAST(b.qty*b.harga AS DECIMAL(20,4))), 2) + ROUND(SUM(CAST(b.qty*b.harga*11/12*a.tax_value AS DECIMAL(20,4))), 2)" : "ROUND(SUM(CAST(b.qty*b.harga*a.kurs_nominal AS DECIMAL(20,4))), 0) + ROUND(SUM(CAST(b.qty*b.harga*11/12*a.tax_value AS DECIMAL(20,4))), 0)";

        $this->db->SELECT("a.id,a.no_faktur_internal,
                IFNULL($dpp,0) as dpp_piutang,
                IFNULL($ppn,0) as ppn_piutang,
                IFNULL($total,0) as total_all  ");
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

        $dpp    = ($currency === 'valas') ? "ROUND(SUM(CAST(b.diskon AS DECIMAL(20,4))), 2)" : "ROUND(SUM(CAST(b.diskon*a.kurs_nominal AS DECIMAL(20,4))), 0)";
        $ppn    = ($currency === 'valas') ? "ROUND(SUM(CAST(b.diskon * 11/12 * a.tax_value AS DECIMAL(20,4))), 2)" : "ROUND(SUM(CAST(b.diskon * 11/12 * a.tax_value*a.kurs_nominal AS DECIMAL(20,4))), 0)";
        $total = ($currency === 'valas') ? "ROUND(SUM(CAST(b.diskon AS DECIMAL(20,4))), 2) + ROUND(SUM(CAST(b.diskon * 11/12 * a.tax_value AS DECIMAL(20,4))), 2)" : "ROUND(SUM(CAST(b.diskon*a.kurs_nominal AS DECIMAL(20,4))), 0) + ROUND(SUM(CAST(b.diskon * 11/12 * a.tax_value AS DECIMAL(20,4))), 0)";

        $this->db->SELECT("a.id,a.no_faktur_internal,
                IFNULL($dpp,0) as dpp_diskon,
                IFNULL($ppn,0) as ppn_diskon,
                IFNULL($total,0) as total_all  ");
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
        $this->db->SELECT("app.id as id_bukti,app.id as id_bukti_ecr, app.no_pelunasan as no_bukti, app.tanggal_transaksi as tgl, app.partner_id as id_partner, CONCAT('Pelunasan: ', (SELECT GROUP_CONCAT(no_faktur) as group_faktur FROM acc_pelunasan_piutang_faktur WHERE pelunasan_piutang_id = app.id),' - ', 
            
            IF('$currency' = 'valas', 
                CONCAT(GROUP_CONCAT(appm.no_bukti), ' Curr: ', (SELECT currency FROM currency_kurs WHERE id = currency_id), '  Kurs: ', appm.kurs, ' '), 
                GROUP_CONCAT(appm.no_bukti)
            )) as uraian, IFNULL(SUM($total),0) as total_pelunasan,   0 as debit , IFNULL(SUM($total),0) as credit, app.status, 'plp' as link, 
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

        $dpp_retur = ($currency === 'valas') ? "ROUND(sum(CAST(retd.qty*retd.harga AS DECIMAL(20,4))),2)" : "ROUND(sum(CAST(retd.qty*retd.harga*ret.kurs_nominal AS DECIMAL(20,4))),0) ";
        $ppn_retur = ($currency === 'valas') ? "ROUND(sum(CAST((retd.qty*retd.harga) * 11/12 * ret.tax_value AS DECIMAL(20,4))),2)" : " ROUND(sum(CAST((retd.qty*retd.harga*ret.kurs_nominal) * 11/12 * ret.tax_value AS DECIMAL(20,4))),0)";
        $total_retur = ($currency === 'valas') ?  "ROUND(sum(CAST(retd.qty*retd.harga AS DECIMAL(20,4))),2) + ROUND(sum(CAST((retd.qty*retd.harga) * 11/12 * ret.tax_value AS DECIMAL(20,4))),2)" : "ROUND(sum(CAST(retd.qty*retd.harga*ret.kurs_nominal AS DECIMAL(20,4))),0) + ROUND(sum(CAST((retd.qty*retd.harga*ret.kurs_nominal) * 11/12 * ret.tax_value AS DECIMAL(20,4))),0)";

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

        $total  = ($currency === 'valas') ? 'apps.selisih' : 'apps.selisih';

        $this->db->where('apps.keterangan <>', 'Uang Muka');
        $this->db->where('app.status', 'done');
        // $this->db->where('apps.tipe_currency', 'Rp');
        $this->db->where('ack.koreksi_bb', 'true');
        $this->db->SELECT("app.id as id_bukti, app.id as id_bukti_ecr, app.no_pelunasan as no_bukti, app.tanggal_transaksi as tgl, app.partner_id as id_partner,  CONCAT('Koreksi : ', ack.nama_koreksi, IF('$currency' = 'valas', 
                CONCAT(' - ',' Curr: ', (SELECT currency FROM currency_kurs WHERE id = currency_id), ' - Kurs: ', apps.kurs), 
                CONCAT(' - ',(SELECT GROUP_CONCAT(no_faktur) as group_no FROM acc_pelunasan_piutang_faktur WHERE pelunasan_piutang_id = app.id))
            ))  as uraian, IFNULL(SUM($total),0) as total_koreksi,  
                        (CASE 
                            WHEN apps.selisih < 0 THEN abs($total) 
                            ELSE 0 
                        END) AS debit,
                        (CASE
                            WHEN apps.selisih > 0 THEN ($total)
                            ELSE 0 
                        END) AS credit, app.status, 'plp' as link,
                        0 as dpp,
                        0 as ppn,
                        0 as total_dpp_ppn");
        $this->db->FROM('acc_pelunasan_piutang app');
        $this->db->jOIN("acc_pelunasan_piutang_summary apps", "app.id = apps.pelunasan_piutang_id", "INNER");
        $this->db->JOIN("acc_pelunasan_koreksi_piutang ack", "apps.koreksi = ack.kode", "left");
        return $this->db->get_compiled_select();
    }


    public function get_list_bukubesar_detail($tgldari, $tglsampai, $checkhidden, array $where = [], $currency)
    {
        // saldo sebelum periode berjalan
        $subquery_faktur_sblm       = $this->get_saldo_sblm($tgldari, 'faktur', $currency);
        $subquery_diskon_sblm       = $this->get_saldo_sblm($tgldari, 'diskon', $currency);
        $subquery_pelunasan_sblm    = $this->get_saldo_sblm($tgldari, 'pelunasan', $currency);
        $subquery_retur_sblm        = $this->get_saldo_sblm($tgldari, 'retur', $currency);
        $subquery_um_sblm           = $this->get_saldo_sblm($tgldari, 'um', $currency);
        $subquery_koreksi_sblm      = $this->get_saldo_sblm($tgldari, 'koreksi', $currency);

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
        } else {
            $cr_condition = ($currency === 'valas') ? '<>' : '';

            $where_utang = ['tanggal >= ' => $tgldari, 'tanggal <= ' => $tglsampai, 'status' => 'confirm', 'kurs ' . $cr_condition => 1];
            $where_diskon = ['tanggal >= ' => $tgldari, 'tanggal <= ' => $tglsampai, 'status' => 'confirm', 'sub.total_all <>' => 0];
            $where_pelunasan = ['app.tanggal_transaksi >= ' => $tgldari, 'app.tanggal_transaksi  <= ' => $tglsampai, 'appm.currency_id ' . $cr_condition => 1];
            $where_retur = ['app.tanggal_transaksi >= ' => $tgldari, 'app.tanggal_transaksi  <= ' => $tglsampai, 'appm.currency_id ' . $cr_condition => 1];
            $where_um    = ['app.tanggal_transaksi >= ' => $tgldari, 'app.tanggal_transaksi <= ' => $tglsampai, 'status' => 'done', 'apps.tipe_currency ' . $cr_condition => 'Rp'];
            $where_koreksi = ['app.tanggal_transaksi >= ' => $tgldari, 'app.tanggal_transaksi <= ' => $tglsampai, 'status' => 'done', 'apps.tipe_currency ' . $cr_condition => 'Rp'];
        }

        // saldo berdasakran periode berjalan
        $subquery_faktur        = $this->get_saldo_piutang($where_utang, 'partner_id', $currency);
        $subquery_diskon        = $this->get_saldo_diskon($where_diskon, 'partner_id', $currency);
        $subquery_pelunasan     = $this->get_saldo_pelunasan($where_pelunasan, 'app.partner_id', $currency);
        $subquery_retur         = $this->get_saldo_retur($where_retur, 'app.partner_id', $currency);
        $subquery_um            = $this->get_saldo_um($where_um, 'app.partner_id', $currency);
        $subquery_koreksi       = $this->get_saldo_koreksi($where_koreksi, 'app.partner_id', $currency);

        $cr_condition = ($currency === 'valas') ? 'p.saldo_awal_piutang_valas' : 'p.saldo_awal_piutang';
        if ($checkhidden == 'true') {
            $this->db->where("(
                    ($cr_condition 
                        + IFNULL(piutang_sblm.total_piutang, 0) 
                        - IFNULL(pelunasan_sblm.total_pelunasan, 0) 
                        - IFNULL(retur_sblm.total_retur, 0) 
                        - IFNULL(um_sblm.total_uang_muka, 0) 
                        - IFNULL(diskon_sblm.total_diskon, 0)
                        - IFNULL(koreksi_sblm.total_koreksi, 0)
                    ) <> 0
                    OR
                    (IFNULL(piutang.total_piutang, 0)
                        + IFNULL(pelunasan.total_pelunasan, 0)
                        + IFNULL(retur.total_retur, 0)
                        + IFNULL(um.total_uang_muka, 0)
                        + IFNULL(diskon.total_diskon, 0)
                        + IFNULL(koreksi.total_koreksi, 0)
                    ) <> 0
                )
            ", null, false);
        }


        if (count($where) > 0) {
            $this->db->where($where);
        }

        $this->db->select("p.id, p.nama, $cr_condition as saldo_awal_piutang,
                        ($cr_condition + IFNULL(piutang_sblm.total_piutang,0) - IFNULL(pelunasan_sblm.total_pelunasan,0) - IFNULL(retur_sblm.total_retur, 0) - IFNULL(diskon_sblm.total_diskon, 0) - IFNULL(um_sblm.total_uang_muka,0) + (IFNULL(koreksi_sblm.total_koreksi,0)) ) as saldo_awal_final,
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
        } else {
            $cr_condition = ($currency === 'valas') ? '<>' : '';

            $where_utang = ['tanggal >= ' => $tgldari, 'tanggal <= ' => $tglsampai, 'status' => 'confirm', 'kurs ' . $cr_condition => 1];
            $where_diskon = ['tanggal >= ' => $tgldari, 'tanggal <= ' => $tglsampai, 'status' => 'confirm', 'kurs ' . $cr_condition => 1, 'sub.total_all <>' => 0];
            $where_pelunasan = ['app.tanggal_transaksi >= ' => $tgldari, 'app.tanggal_transaksi  <= ' => $tglsampai, 'appm.currency_id ' . $cr_condition => 1];
            $where_retur = ['app.tanggal_transaksi >= ' => $tgldari, 'app.tanggal_transaksi  <= ' => $tglsampai, 'appm.currency_id ' . $cr_condition => 1];
            $where_um    = ['app.tanggal_transaksi >= ' => $tgldari, 'app.tanggal_transaksi <= ' => $tglsampai, 'status' => 'done', 'apps.tipe_currency ' . $cr_condition => 'Rp'];
            $where_koreksi = ['app.tanggal_transaksi >= ' => $tgldari, 'app.tanggal_transaksi <= ' => $tglsampai, 'status' => 'done', 'apps.tipe_currency ' . $cr_condition => 'Rp'];
        }

        $subquery_faktur       = $this->get_saldo_piutang($where_utang, 'fak.no_faktur_internal', $currency);
        $subquery_diskon       = $this->get_saldo_diskon($where_diskon, 'fak.no_faktur_internal', $currency);
        $subquery_pelunasan    = $this->get_saldo_pelunasan($where_pelunasan, 'app.no_pelunasan', $currency);
        $subquery_retur        = $this->get_saldo_retur($where_retur, 'app.no_pelunasan', $currency);
        $subquery_um           = $this->get_saldo_um($where_um, 'app.no_pelunasan', $currency);
        $subquery_koreksi      = $this->get_saldo_koreksi($where_koreksi, 'app.no_pelunasan', $currency);

        if (count($where) > 0) {
            $this->db->where($where);
        }

        $union_sql = $subquery_faktur . ' UNION ALL ' . $subquery_pelunasan . ' UNION ALL ' . $subquery_retur . ' UNION ALL ' . $subquery_um . ' UNION ALL ' . $subquery_koreksi . ' UNION ALL ' . $subquery_diskon;

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
