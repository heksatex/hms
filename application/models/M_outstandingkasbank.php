<?php defined('BASEPATH') OR exit('No Direct Script Acces Allowed');

/**
 * 
 */
class M_outstandingkasbank extends CI_Model
{
    var $column_order = array(null, null, 'no_bukti', 'partner_nama', 'tanggal', 'currency', 'kurs', 'total_rp', 'total_valas');
    var $column_search = array('no_bukti', 'tanggal', 'partner_nama', 'currency', 'kurs', 'total_rp', 'total_valas');
    var $order         = array('tanggal' => 'asc');

    var $coa_kas = array('2112.01', '2112.02','1192.01', '1192.02', '1192.03', '1192.99');
    // var $coa_um = array('1192.01', '1192.02', '1192.03', '1192.99');

    function query()
    {
        $this->db->where_in('b.kode_coa', $this->coa_kas);
       
        $where = ["a.status" => 'confirm', "b.lunas" => 0];
        if (count($where) > 0) {
            $this->db->where($where);
        }
        $this->db->select("b.id,(a.no_bk) as no_bukti, b.tanggal, b.currency_id, c.currency, b.kurs, IF(c.currency='IDR', b.nominal, IFNULL(b.nominal*b.kurs,0)) as total_rp, IF(c.currency != 'IDR', b.nominal, 0) as total_valas, 'bank' as tipe2, a.partner_nama");
        $this->db->from("acc_bank_keluar a");
        $this->db->join("acc_bank_keluar_detail b ", "a.id = b.bank_keluar_id", "left");
        $this->db->join("currency_kurs c ", "b.currency_id = c.id", "left");
        return $query1_sql = $this->db->get_compiled_select();
    }

    function queryB()
    {
        $this->db->where_in('e.kode_coa', $this->coa_kas);
        $where = ["h.status" => 'confirm', 'e.lunas' => 0];
        if (count($where) > 0) {
            $this->db->where($where);
        }
        $this->db->select("e.id,(h.no_kk) as no_bukti , e.tanggal, e.currency_id, i.currency, e.kurs, IF(i.currency='IDR', e.nominal, IFNULL(e.nominal*e.kurs,0) ) as total_rp, IF(i.currency != 'IDR', e.nominal, 0) as total_valas, 'kas' as tipe2, h.partner_nama");
        $this->db->from("acc_kas_keluar h");
        $this->db->join("acc_kas_keluar_detail e ", "h.id = e.kas_keluar_id", "left");
        $this->db->join("currency_kurs i ", "e.currency_id = i.id", "left");
        return  $query2_sql = $this->db->get_compiled_select();
    }

    function queryC()
    {
        $this->db->where_in('g.kode_coa', $this->coa_kas);
        $where = ["f.status" => 'confirm', 'g.lunas' => 0];
        if (count($where) > 0) {
            $this->db->where($where);
        }
        $this->db->select("g.id,(f.no_gk) as no_bukti, g.tanggal, g.currency_id, j.currency, g.kurs, IF(j.currency='IDR', g.nominal, IFNULL(g.nominal*g.kurs,0)) as total_rp, IF(j.currency != 'IDR', g.nominal, 0) as total_valas, 'giro' as tipe2, f.partner_nama");
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
}