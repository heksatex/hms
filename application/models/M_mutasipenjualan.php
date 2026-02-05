<?php defined('BASEPATH') or exit('No Direct Script Acces Allowed');

/**
 * 
 */
class M_mutasipenjualan extends CI_Model
{

    function get_list_faktur_group_partner($where)
    {
        if (count($where) > 0) {
            $this->db->where($where);
        }
        $this->db->select('fak.partner_id, p.nama as nama_partner');
        $this->db->from('acc_faktur_penjualan fak');
        $this->db->join('partner p', "fak.partner_id = p.id", "inner");
        $this->db->join("currency_kurs c", "fak.kurs = c.id", "left");
        $this->db->group_by("fak.partner_id");
        $this->db->order_by('p.nama', 'asc');
        $result = $this->db->get();
        return $result->result();
    }


    function get_group_partner($where, $partner, $date, $like)
    {
        $this->db->select('fak.partner_id, p.nama as nama_partner');
        $this->db->from('acc_faktur_penjualan fak');
        $this->db->join('partner p', 'fak.partner_id = p.id', 'inner');

        $this->db->where($where);

        // partner
        if (is_array($partner)) {
            $this->db->where_in('fak.partner_id', $partner);
        } elseif (!empty($partner)) {
            $this->db->where('fak.partner_id', $partner);
        }

        // tanggal
        if (!empty($date['from'])) {
            $this->db->where('fak.tanggal >=', $date['from']);
        }
        if (!empty($date['to'])) {
            $this->db->where('fak.tanggal <=', $date['to']);
        }

        // like
        foreach ($like as $field => $val) {
            $this->db->like($field, $val);
        }

        $this->db->group_by('fak.partner_id');
        $this->db->order_by('p.nama', 'asc');

        return $this->db->get()->result();
    }


    public function get_detail_by_partner($date, $partner, array $where = [], array $like = [])
    {
        // saldo sebelum periode berjalan
        $where_utang     = [ 'fak.status' => 'confirm', 'partner_id'=>$partner];
        if (!empty($date['from'])) {
            $where_utang =  array_merge($where_utang, ['fak.tanggal >=' => $date['from'] ] );
        }

        if (!empty($date['to'])) {
            $where_utang = array_merge($where_utang, ['fak.tanggal <=' => $date['to'] ] );
        }

       if (count($where) > 0) {
            $where_utang = array_merge($where_utang, $where);
        }

        
        // $where_pelunasan = ['app.status'=> 'done', "appm.tipe <> "=> "retur", "appm.tipe <> "=>"koreksi" ];
        $where_pelunasan = [];
        $where_retur     = [];
        $where_diskon    = [];

        $subquery_faktur      = $this->get_saldo_piutang($where_utang, 'fak.id');
        $subquery_pelunasan   = $this->get_saldo_pelunasan($where_pelunasan, 'appf.faktur_id');
        $subquery_retur       = $this->get_saldo_retur($where_retur,'appf.faktur_id');
        $subquery_diskon      = $this->get_saldo_diskon($where_diskon, 'a.id');

        // if (count($where) > 0) {
        //     $this->db->where($where);
        // }

        $this->db->select("p.id, p.nama,
                        piutang.tanggal, piutang.no_faktur_internal as no_faktur, piutang.no_sj, piutang.tipe,
                        IFNULL(piutang.total_piutang,0) as total_piutang,
                        IFNULL(piutang.dpp_piutang,0) as dpp_piutang,
                        IFNULL(piutang.ppn_piutang,0) as ppn_piutang,
                        pelunasan.no_pelunasan as no_pelunasan,
                        pelunasan.tgl as tanggal_pelunasan,
                        pelunasan.no_bukti as no_bukti_pelunasan,
                        IFNULL(pelunasan.total_pelunasan_rp, 0) as total_pelunasan, 
                        retur.tgl as tanggal_retur,
                        retur.no_bukti as no_bukti_retur,
                        IFNULL(retur.dpp_retur,0) as dpp_retur,
                        IFNULL(retur.ppn_retur,0) as ppn_retur,
                        IFNULL(retur.total_retur,0) as total_retur,
                        IFNULL(diskon.dpp_diskon,0) as dpp_diskon,
                        IFNULL(diskon.ppn_diskon,0) as ppn_diskon,
                        IFNULL(diskon.total_diskon,0) as total_diskon,
                        piutang.lunas 
                        ");
        $this->db->from('partner p');
        $this->db->join("($subquery_faktur) as piutang", "piutang.partner_id = p.id", "INNER");
        $this->db->JOIN("($subquery_pelunasan) as pelunasan", "pelunasan.faktur_id = piutang.id", "LEFT");
        $this->db->JOIN("($subquery_retur) as retur", "retur.faktur_id = piutang.id", "LEFT");
        $this->db->JOIN("($subquery_diskon) as diskon","diskon.id = piutang.id","LEFT");
        $this->db->order_by('p.nama');
        $query = $this->db->get();
        return $query->result();
    }



    function get_saldo_piutang(array $where = [], string $group = '', string $currency = '')
    {
        if (count($where) > 0) {
            $this->db->where($where);
        }

        if ($group) {
            $this->db->group_by($group);
        }
        $dpp    = ($currency === 'valas') ? "SUM(ROUND(CAST(b.qty as DECIMAL(20, 4)) * CAST(b.harga as DECIMAL(20, 4)),2))" : "ROUND(ROUND(SUM(ROUND(CAST(b.qty as DECIMAL(20, 4)) * CAST(b.harga as DECIMAL(20, 4)),2)),IF(fak.kurs=1, 0, 2)) * CAST( fak.kurs_nominal as DECIMAL(20, 4)), 0) ";
        $ppn    = ($currency === 'valas') ? "ROUND(ROUND(SUM(ROUND(CAST(b.qty as DECIMAL(20, 4)) * CAST(b.harga as DECIMAL(20, 4)),2)) * 11/12,2) * CAST(fak.tax_value as DECIMAL(20,4)),2) " : "ROUND(ROUND(ROUND(ROUND(SUM(ROUND(CAST(b.qty as DECIMAL(20, 4)) * CAST(b.harga as DECIMAL(20, 4)),2)),IF(fak.kurs=1, 0, 2)) * 11/12,IF(fak.kurs=1, 0, 2)) * CAST(fak.tax_value as DECIMAL(20,4)),IF(fak.kurs=1, 0, 2)) * CAST( fak.kurs_nominal as DECIMAL(20, 4)) , 0)"; 
        
        $total  = ($currency === 'valas') ? "IFNULL(SUM(ROUND(CAST(b.qty as DECIMAL(20, 4)) * CAST(b.harga as DECIMAL(20, 4)),2)),0) + IFNULL(ROUND(ROUND(SUM(ROUND(CAST(b.qty as DECIMAL(20, 4)) * CAST(b.harga as DECIMAL(20, 4)),2)) * 11/12,2) * CAST(fak.tax_value as DECIMAL(20,4)),2),0)" : "IFNULL(ROUND(ROUND(SUM(ROUND(CAST(b.qty as DECIMAL(20, 4)) * CAST(b.harga as DECIMAL(20, 4)),2)),IF(fak.kurs=1, 0, 2)) * CAST( fak.kurs_nominal as DECIMAL(20, 4)), 0),0) + IFNULL(ROUND(ROUND(ROUND(ROUND(SUM(ROUND(CAST(b.qty as DECIMAL(20, 4)) * CAST(b.harga as DECIMAL(20, 4)),2)),IF(fak.kurs=1, 0, 2)) * 11/12,IF(fak.kurs=1, 0, 2)) * CAST(fak.tax_value as DECIMAL(20,4)),IF(fak.kurs=1, 0, 2)) * CAST( fak.kurs_nominal as DECIMAL(20, 4)) , 0), 0)";

        $this->db->SELECT("fak.id, fak.tanggal, fak.no_faktur_internal, fak.no_sj, fak.tipe, fak.partner_id, fak.lunas,
                IFNULL($dpp,0) as dpp_piutang,
                IFNULL($ppn,0) as ppn_piutang,
                ($total) as total_piutang  ");
        $this->db->FROM("acc_faktur_penjualan fak");
        $this->db->JOIN("acc_faktur_penjualan_detail b", "b.faktur_id = fak.id", "INNER");
        return $this->db->get_compiled_select();

    }


    function get_saldo_diskon(array $where = [], string $group = '', string $currency = '')
    {
      
        if (count($where) > 0) {
            $this->db->where($where);
        }

        if ($group) {
            $this->db->group_by($group);
        }
       
        $dpp    = ($currency === 'valas') ? "" : "ROUND(ROUND(ROUND(SUM(ROUND(CAST(b.qty as DECIMAL(20, 4)) * CAST(b.harga as DECIMAL(20, 4)),2)),IF(a.kurs=1, 0, 2))    * (CAST(a.nominal_diskon AS DECIMAL(20,4)) / 100), IF(a.kurs=1, 0, 2)) * CAST( a.kurs_nominal as DECIMAL(20, 4)),0)";
        $ppn    = ($currency === 'valas') ? "" : "ROUND(ROUND(ROUND(ROUND(ROUND(SUM(ROUND(CAST(b.qty as DECIMAL(20, 4)) * CAST(b.harga as DECIMAL(20, 4)),2)),IF(a.kurs=1, 0, 2)) * (CAST(a.nominal_diskon AS DECIMAL(20,4)) / 100), IF(a.kurs=1, 0, 2)) * 11/12,IF(a.kurs=1, 0, 2)) * CAST(a.tax_value as DECIMAL(20,4)),IF(a.kurs=1, 0, 2)) * CAST( a.kurs_nominal as DECIMAL(20, 4)) , 0)";
        $total = ($currency === 'valas') ? "" : "IFNULL(ROUND(ROUND(ROUND(SUM(ROUND(CAST(b.qty as DECIMAL(20, 4)) * CAST(b.harga as DECIMAL(20, 4)),2)),IF(a.kurs=1, 0, 2))   * (CAST(a.nominal_diskon AS DECIMAL(20,4)) / 100), IF(a.kurs=1, 0, 2)) * CAST( a.kurs_nominal as DECIMAL(20, 4)), 2),0) + IFNULL(ROUND(ROUND(ROUND(ROUND(ROUND(SUM(ROUND(CAST(b.qty as DECIMAL(20, 4)) * CAST(b.harga as DECIMAL(20, 4)),2)),IF(a.kurs=1, 0, 2)) * (CAST(a.nominal_diskon AS DECIMAL(20,4)) / 100), IF(a.kurs=1, 0, 2)) * 11/12,IF(a.kurs=1, 0, 2)) * CAST(a.tax_value as DECIMAL(20,4)),IF(a.kurs=1, 0, 2)) * CAST( a.kurs_nominal as DECIMAL(20, 4)) , 0),0)";

        $this->db->SELECT("a.id, a.tanggal, a.no_faktur_internal, a.no_sj, a.tipe,
                IFNULL($dpp,0) as dpp_diskon,
                IFNULL($ppn,0) as ppn_diskon,
                ($total) as total_diskon  ");
        $this->db->FROM("acc_faktur_penjualan a");
        $this->db->JOIN("acc_faktur_penjualan_detail b", "b.faktur_id = a.id", "INNER");
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
        
        $this->db->where('app.status', 'done');
        // $this->db->where('appm.tipe <> ', 'um');
        $this->db->where('appm.tipe <> ', 'retur');
        $this->db->where('appm.tipe <> ', 'koreksi');
        // $this->db->where('appm.tipe <> ', 'depo');
        $this->db->SELECT("GROUP_CONCAT(app.no_pelunasan) as no_pelunasan,  GROUP_CONCAT(DATE_FORMAT(app.tanggal_transaksi, '%Y-%m-%d')) as tgl,app.partner_id as id_partner, (appm.no_bukti) as no_bukti,
                            appf.faktur_id, appf.no_faktur,   sum(appf.pelunasan_rp) as total_pelunasan_rp, (appf.pelunasan_valas) as total_pelunasan_valas,app.status  ");
        $this->db->FROM('acc_pelunasan_piutang app');
        $this->db->jOIN("(SELECT GROUP_CONCAT(no_bukti) as no_bukti, pelunasan_piutang_id, tipe FROM acc_pelunasan_piutang_metode GROUP BY pelunasan_piutang_id) as appm", "app.id = appm.pelunasan_piutang_id", "INNER");
        $this->db->jOIN("acc_pelunasan_piutang_faktur appf", "app.id = appf.pelunasan_piutang_id", "INNER");
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

        $dpp_retur = ($currency === 'valas') ? "" : "sum(ROUND(CAST(retd.qty as DECIMAL(20,4)) * CAST(retd.harga AS DECIMAL(20,4)),2)) ";
        // $ppn_retur = ($currency === 'valas') ? "" : "ROUND(ROUND(ROUND(sum(ROUND(CAST(retd.qty as DECIMAL(20,4)) * CAST(retd.harga AS DECIMAL(20,4)),2)) * 11 / 12,0) *  CAST(ret.tax_value as DECIMAL(20,4)), 0) * CAST(ret.kurs_nominal as DECIMAL(20,4)) ,0)" ;
        $ppn_retur = ($currency === 'valas') ? "" : "ROUND(ROUND(sum(ROUND(CAST(retd.qty as DECIMAL(20,4)) * CAST(retd.harga AS DECIMAL(20,4)),2)) * 11 / 12,2) * CAST(ret.tax_value as DECIMAL(20,4)), 2)" ;
        $total_retur = ($currency === 'valas') ?  "" : "IFNULL(sum(ROUND(CAST(retd.qty as DECIMAL(20,4)) * CAST(retd.harga AS DECIMAL(20,4)),2)), 0) + IFNULL(ROUND(ROUND(sum(ROUND(CAST(retd.qty as DECIMAL(20,4)) * CAST(retd.harga AS DECIMAL(20,4)),2)) * 11 / 12,2) * CAST(ret.tax_value as DECIMAL(20,4)), 2), 0)";


        $dpp_retur_diskon = ($currency === 'valas') ? "" : "ROUND(sum(ROUND(CAST(retd.qty as DECIMAL(20,4)) * CAST(retd.harga AS DECIMAL(20,4)),2)) * (CAST(ret.nominal_diskon AS DECIMAL(20,4)) / 100),2) ";
        // $ppn_retur_diskon = ($currency === 'valas') ? "" : "ROUND(ROUND(ROUND(ROUND(sum(ROUND(CAST(retd.qty as DECIMAL(20,4)) * CAST(retd.harga AS DECIMAL(20,4)),2)) * (CAST(ret.nominal_diskon AS DECIMAL(20,4)) / 100),0) * 11 / 12,0) *  CAST(ret.tax_value as DECIMAL(20,4)), 0) * CAST(ret.kurs_nominal as DECIMAL(20,4)) ,0)" ;
        $ppn_retur_diskon = ($currency === 'valas') ? "" : "ROUND(ROUND(ROUND(sum(ROUND(CAST(retd.qty as DECIMAL(20,4)) * CAST(retd.harga AS DECIMAL(20,4)),2)) * (CAST(ret.nominal_diskon AS DECIMAL(20,4)) / 100),2) * 11 / 12,2) *  CAST(ret.tax_value as DECIMAL(20,4)), 2) " ;
        $total_retur_diskon = ($currency === 'valas') ? "" : "IFNULL(ROUND(sum(ROUND(CAST(retd.qty as DECIMAL(20,4)) * CAST(retd.harga AS DECIMAL(20,4)),2)) * (CAST(ret.nominal_diskon AS DECIMAL(20,4)) / 100),2) ,0) + IFNULL(ROUND(ROUND(ROUND(sum(ROUND(CAST(retd.qty as DECIMAL(20,4)) * CAST(retd.harga AS DECIMAL(20,4)),2)) * (CAST(ret.nominal_diskon AS DECIMAL(20,4)) / 100),2) * 11 / 12,2) *  CAST(ret.tax_value as DECIMAL(20,4)), 2)  ,0)";

        
        $total_dpp_retur = ($currency === 'valas') ? "ROUND(arp.dpp_retur  - arp.dpp_retur_diskon,2)" : "ROUND(arp.dpp_retur  - arp.dpp_retur_diskon,0)";
        $total_ppn_retur = ($currency === 'valas') ? "ROUND(arp.ppn_retur  - arp.ppn_retur_diskon,2)" : "ROUND(arp.ppn_retur  - arp.ppn_retur_diskon,0)";
        $total_all       = ($currency === 'valas') ? "ROUND(ROUND(arp.dpp_retur  - arp.dpp_retur_diskon,2) + ROUND(arp.ppn_retur  - arp.ppn_retur_diskon,2),2)" : "ROUND(ROUND(arp.dpp_retur  - arp.dpp_retur_diskon,0) + ROUND(arp.ppn_retur  - arp.ppn_retur_diskon,0),0) ";

        $this->db->where('app.status', 'done');
        $this->db->where('appm.tipe', 'retur');
        $this->db->SELECT("GROUP_CONCAT(app.no_pelunasan) as no_pelunasan,  GROUP_CONCAT(DATE_FORMAT(app.tanggal_transaksi, '%Y-%m-%d')) as tgl,app.partner_id as id_partner, GROUP_CONCAT(appm.no_bukti) as no_bukti,appf.faktur_id, appf.no_faktur, ($total_dpp_retur) as dpp_retur, ($total_ppn_retur) as ppn_retur, ($total_all) as total_retur,app.status ");
        $this->db->FROM('acc_pelunasan_piutang app');
        $this->db->jOIN("acc_pelunasan_piutang_metode appm", "app.id = appm.pelunasan_piutang_id", "INNER");
        $this->db->jOIN("acc_pelunasan_piutang_faktur appf", "app.id = appf.pelunasan_piutang_id", "INNER");
        $this->db->JOIN("( SELECT ret.id , 
                ($dpp_retur) as dpp_retur,
                ($ppn_retur) as ppn_retur,
                ($total_retur) as total_retur,
                ($dpp_retur_diskon) as dpp_retur_diskon,
                ($ppn_retur_diskon) as ppn_retur_diskon,
                ($total_retur_diskon) as total_retur_diskon
                FROM acc_retur_penjualan ret
                INNER JOIN acc_retur_penjualan_detail retd ON ret.id = retd.retur_id
                WHERE ret.status = 'confirm' and lunas = 1
                GROUP BY ret.id
                ) arp", "arp.id = appm.id_bukti", "INNER");
        return $this->db->get_compiled_select();
    }


}
