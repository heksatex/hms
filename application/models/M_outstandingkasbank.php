<?php defined('BASEPATH') OR exit('No Direct Script Acces Allowed');

/**
 * 
 */
class M_outstandingkasbank extends CI_Model
{
    var $column_order = array(null, null, 'no_bukti', 'partner_nama', 'coa', 'tanggal', 'uraian', 'currency', 'kurs', 'total_rp', 'total_valas');
    var $column_search = array('no_bukti', 'tanggal', 'partner_nama', 'coa', 'currency', 'uraian','kurs', 'total_rp', 'total_valas');
    var $order         = array('tanggal' => 'asc', 'no_bukti' => 'asc');

    var $coa_kas = array('2112.01', '2112.02','1192.01', '1192.02', '1192.03', '1192.99');
    // var $coa_um = array('1192.01', '1192.02', '1192.03', '1192.99');
    var $where_jenis_transaksi = array('utang','um_pembelian');

    function get_list_coa_by_transaksi()
    {
        $this->db->where_in('jenis_transaksi', $this->where_jenis_transaksi);
        $this->db->select('kode_coa');
        $this->db->from('acc_coa');
        return $this->db->get_compiled_select();
    }

    function query()
    {
        $list_coa =  $this->get_list_coa_by_transaksi();
        if (empty($list_coa)) {
            $list_coa = ['__EMPTY__']; 
        }

        $this->db->where_in('b.kode_coa', $list_coa, FALSE);
       
        $where = ["a.status" => 'confirm', "b.lunas" => 0];
        if (count($where) > 0) {
            $this->db->where($where);
        }
        $this->db->select("b.id,(a.no_bk) as no_bukti, b.tanggal,  b.kode_coa as coa, b.currency_id, c.currency, (CASE WHEN b.kurs_akhir IS NOT NULL AND b.kurs_akhir > 0 THEN b.kurs_akhir ELSE b.kurs END) as kurs, IF(c.currency='IDR', b.nominal, IFNULL(b.nominal*(CASE WHEN b.kurs_akhir IS NOT NULL AND b.kurs_akhir > 0 THEN b.kurs_akhir ELSE b.kurs END),0)) as total_rp, IF(c.currency != 'IDR', b.nominal, 0) as total_valas, 'bank' as tipe2, a.partner_nama, b.uraian");
        $this->db->from("acc_bank_keluar a");
        $this->db->join("acc_bank_keluar_detail b ", "a.id = b.bank_keluar_id", "left");
        $this->db->join("currency_kurs c ", "b.currency_id = c.id", "left");
        return $query1_sql = $this->db->get_compiled_select();
    }

    function queryB()
    {
        $list_coa =  $this->get_list_coa_by_transaksi();
        if (empty($list_coa)) {
            $list_coa = ['__EMPTY__']; 
        }

        $this->db->where_in('e.kode_coa', $list_coa, FALSE);
        $where = ["h.status" => 'confirm', 'e.lunas' => 0];
        if (count($where) > 0) {
            $this->db->where($where);
        }
        $this->db->select("e.id,(h.no_kk) as no_bukti , e.tanggal, e.kode_coa as coa, e.currency_id,  i.currency, (CASE WHEN e.kurs_akhir IS NoT NULL AND e.kurs_akhir > 0 THEN e.kurs_akhir ELSE e.kurs END) as kurs, IF(i.currency='IDR', e.nominal, IFNULL(e.nominal*(CASE WHEN e.kurs_akhir IS NOT NULL AND e.kurs_akhir > 0 THEN e.kurs_akhir ELSE e.kurs END),0) ) as total_rp, IF(i.currency != 'IDR', e.nominal, 0) as total_valas, 'kas' as tipe2, h.partner_nama, e.uraian");
        $this->db->from("acc_kas_keluar h");
        $this->db->join("acc_kas_keluar_detail e ", "h.id = e.kas_keluar_id", "left");
        $this->db->join("currency_kurs i ", "e.currency_id = i.id", "left");
        return  $query2_sql = $this->db->get_compiled_select();
    }

    function queryC()
    {
        $list_coa =  $this->get_list_coa_by_transaksi();
        if (empty($list_coa)) {
            $list_coa = ['__EMPTY__']; 
        }

        $this->db->where_in('g.kode_coa', $list_coa, FALSE);
        $where = ["f.status" => 'confirm', 'g.lunas' => 0];
        if (count($where) > 0) {
            $this->db->where($where);
        }
        $this->db->select("g.id,(f.no_gk) as no_bukti, g.tanggal, g.kode_coa as coa, g.currency_id, j.currency, (CASE WHEN g.kurs_akhir IS NOT NULL AND g.kurs_akhir > 0 THEN g.kurs_akhir ELSE g.kurs END) as kurs, IF(j.currency='IDR', g.nominal, IFNULL(g.nominal* (CASE WHEN g.kurs_akhir IS NOT NULL AND g.kurs_akhir > 0 THEN g.kurs_akhir ELSE g.kurs END) ,0)) as total_rp, IF(j.currency != 'IDR', g.nominal, 0) as total_valas, 'giro' as tipe2, f.partner_nama, IF(f.transinfo !='', f.transinfo, f.lain2 ) as uraian");
        $this->db->from("acc_giro_keluar f");
        $this->db->join("acc_giro_keluar_detail g ", "f.id = g.giro_keluar_id", "left");
        $this->db->join("currency_kurs j ", "g.currency_id = j.id", "left");
        return $query_sql = $this->db->get_compiled_select();
    }


    private function _get_datatables_query()
    {
        $union_sql = $this->query() . ' UNION ALL ' . $this->queryB() . ' UNION ALL ' . $this->queryC();
        // $query = $this->db->query($union_sql);

        $this->db->SELECT('*');
        $this->db->from('(' . $union_sql . ') as sub');

        $i = 0;

        foreach ($this->column_search as $item) // loop column 
        {
            if ($_POST['search']['value']) // if datatable send POST for search
            {

                if ($i === 0) // first loop
                {
                    $this->db->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
                    $this->db->like($item, $_POST['search']['value']);
                } else {
                    $this->db->or_like($item, $_POST['search']['value']);
                }

                if (count($this->column_search) - 1 == $i) //last loop
                    $this->db->group_end(); //close bracket
            }
            $i++;
        }

        if (isset($_POST['order'])) // here order processing
        {
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    function get_datatables()
    {
        $this->_get_datatables_query();
        if (isset($_POST["length"]) && $_POST["length"] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }

    function count_filtered()
    {
        $this->_get_datatables_query();
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all()
    {
        $union_sql = $this->query() . ' UNION ALL ' . $this->queryB() . ' UNION ALL ' . $this->queryC();
        $this->db->SELECT('*');
        $this->db->from('(' . $union_sql . ') as sub');
        return $this->db->count_all_results();
    }


    var $where_jenis_transaksi_piutang = array('piutang','um_penjualan');

    function get_list_coa_by_transaksi_2()
    {
        $this->db->where_in('jenis_transaksi', $this->where_jenis_transaksi_piutang);
        $this->db->select('kode_coa');
        $this->db->from('acc_coa');
        return $this->db->get_compiled_select();
    }

    function query_2()
    {
        $list_coa =  $this->get_list_coa_by_transaksi_2();
        if (empty($list_coa)) {
            $list_coa = ['__EMPTY__']; 
        }
        
        $this->db->where_in('abmd.kode_coa', $list_coa, FALSE);
       
        $where = ["abm.status" => 'confirm', "abmd.lunas" => 0];
        if (count($where) > 0) {
            $this->db->where($where);
        }
        $this->db->select("abmd.id,(abm.no_bm) as no_bukti, abmd.tanggal,  abmd.kode_coa as coa, abmd.currency_id, c.currency, (CASE WHEN abmd.kurs_akhir IS NOT NULL AND abmd.kurs_akhir > 0  THEN abmd.kurs_akhir ELSE abmd.kurs END) as kurs, IF(c.currency='IDR', abmd.nominal, IFNULL(abmd.nominal*(CASE WHEN abmd.kurs_akhir IS NOT NULL AND abmd.kurs_akhir > 0  THEN abmd.kurs_akhir ELSE abmd.kurs END) ,0)) as total_rp, IF(c.currency != 'IDR', abmd.nominal, 0) as total_valas, 'bank' as tipe2, abm.partner_nama, abmd.uraian");
        $this->db->from("acc_bank_masuk abm");
        $this->db->join("acc_bank_masuk_detail abmd ", "abm.id = abmd.bank_masuk_id", "left");
        $this->db->join("currency_kurs c ", "abmd.currency_id = c.id", "left");
        return $query1_sql = $this->db->get_compiled_select();
    }

    function queryB_2()
    {
        $list_coa =  $this->get_list_coa_by_transaksi_2();
        if (empty($list_coa)) {
            $list_coa = ['__EMPTY__']; 
        }
        
        $this->db->where_in('akmd.kode_coa', $list_coa , FALSE);
        $where = ["akm.status" => 'confirm', 'akmd.lunas' => 0];
        if (count($where) > 0) {
            $this->db->where($where);
        }
        $this->db->select("akmd.id,(akm.no_km) as no_bukti , akmd.tanggal, akmd.kode_coa as coa, akmd.currency_id,  i.currency, (CASE WHEN akmd.kurs_akhir IS NOT NULL AND akmd.kurs_akhir > 0  THEN akmd.kurs_akhir ELSE akmd.kurs END) as kurs, IF(i.currency='IDR', akmd.nominal, IFNULL(akmd.nominal*(CASE WHEN akmd.kurs_akhir IS NOT NULL AND akmd.kurs_akhir > 0  THEN akmd.kurs_akhir ELSE akmd.kurs END),0) ) as total_rp, IF(i.currency != 'IDR', akmd.nominal, 0) as total_valas, 'kas' as tipe2, akm.partner_nama, akmd.uraian");
        $this->db->from("acc_kas_masuk akm");
        $this->db->join("acc_kas_masuk_detail akmd ", "akm.id = akmd.kas_masuk_id", "left");
        $this->db->join("currency_kurs i ", "akmd.currency_id = i.id", "left");
        return  $query2_sql = $this->db->get_compiled_select();
    }

    function queryC_2()
    {
        $list_coa =  $this->get_list_coa_by_transaksi_2();
        if (empty($list_coa)) {
            $list_coa = ['__EMPTY__']; 
        }

        $this->db->where_in('agmd.kode_coa', $list_coa , FALSE);
        $where = ["agm.status" => 'confirm', 'agmd.lunas' => 0];
        if (count($where) > 0) {
            $this->db->where($where);
        }
        $this->db->select("agmd.id,(agm.no_gm) as no_bukti, agmd.tanggal, agmd.kode_coa as coa, agmd.currency_id, j.currency, (CASE WHEN agmd.kurs_akhir IS NOT NULL AND agmd.kurs_akhir > 0  THEN agmd.kurs_akhir ELSE agmd.kurs END) as kurs, IF(j.currency='IDR', agmd.nominal, IFNULL(agmd.nominal*(CASE WHEN agmd.kurs_akhir IS NOT NULL AND agmd.kurs_akhir > 0  THEN agmd.kurs_akhir ELSE agmd.kurs END),0)) as total_rp, IF(j.currency != 'IDR', agmd.nominal, 0) as total_valas, 'giro' as tipe2, agm.partner_nama, IF(agm.transinfo !='', agm.transinfo, agm.lain2 ) as uraian");
        $this->db->from("acc_giro_masuk agm");
        $this->db->join("acc_giro_masuk_detail agmd ", "agm.id = agmd.giro_masuk_id", "left");
        $this->db->join("currency_kurs j ", "agmd.currency_id = j.id", "left");
        return $query_sql = $this->db->get_compiled_select();
    }


    private function _get_datatables_query_2()
    {
        $union_sql = $this->query_2() . ' UNION ALL ' . $this->queryB_2() . ' UNION ALL ' . $this->queryC_2();
        // $query = $this->db->query($union_sql);

        $this->db->SELECT('*');
        $this->db->from('(' . $union_sql . ') as sub');

        $i = 0;

        foreach ($this->column_search as $item) // loop column 
        {
            if ($_POST['search']['value']) // if datatable send POST for search
            {

                if ($i === 0) // first loop
                {
                    $this->db->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
                    $this->db->like($item, $_POST['search']['value']);
                } else {
                    $this->db->or_like($item, $_POST['search']['value']);
                }

                if (count($this->column_search) - 1 == $i) //last loop
                    $this->db->group_end(); //close bracket
            }
            $i++;
        }

        if (isset($_POST['order'])) // here order processing
        {
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    function get_datatables_2()
    {
        $this->_get_datatables_query_2();
        if (isset($_POST["length"]) && $_POST["length"] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }

    function count_filtered_2()
    {
        $this->_get_datatables_query_2();
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all_2()
    {
        $union_sql = $this->query_2() . ' UNION ALL ' . $this->queryB_2() . ' UNION ALL ' . $this->queryC_2();
        $this->db->SELECT('*');
        $this->db->from('(' . $union_sql . ') as sub');
        return $this->db->count_all_results();
    }

    
}