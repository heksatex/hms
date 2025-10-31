<?php

use Google\Service\Iam\Oidc;

 defined('BASEPATH') or exit('No Direct Script Acces Allowed');

/**
 * 
 */
class M_pelunasanhutang extends CI_Model
{

    var $column_order = array(null, 'no_pelunasan', 'tanggal_dibuat', 'partner_nama', 'nama_status');
    var $column_search = array('no_pelunasan', 'tanggal_dibuat', 'partner_nama', 'nama_status');
    var $order        = array('no_pelunasan' => 'desc');


    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    private function _get_datatables_query()
    {

        $this->db->select("pl.no_pelunasan, pl.tanggal_dibuat, pl.tanggal_transaksi, pl.status, pl.partner_id, pl.partner_nama, mmss.nama_status");
        $this->db->from("acc_pelunasan_hutang pl");
        $this->db->join("main_menu_sub_status mmss", "mmss.jenis_status=pl.status", "inner");

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

    function get_datatables($mmss)
    {
        $this->_get_datatables_query();
        $this->db->where("mmss.main_menu_sub_kode", $mmss);
        if (isset($_POST["length"]) && $_POST["length"] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }

    function count_filtered($mmss)
    {
        $this->db->where("mmss.main_menu_sub_kode", $mmss);
        $this->_get_datatables_query();
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all($mmss)
    {
        $this->db->select("pl.no_pelunasan, pl.tanggal_dibuat, pl.tanggal_transaksi, pl.status, pl.partner_id, pl.partner_nama, mmss.nama_status");
        $this->db->from("acc_pelunasan_hutang pl");
        $this->db->join("main_menu_sub_status mmss", "mmss.jenis_status=pl.status", "inner");
        $this->db->where("mmss.main_menu_sub_kode", $mmss);
        return $this->db->count_all_results();
    }



    public function get_list_partner_supplier($suppler, $name)
    {
        $this->db->order_by('nama', 'asc');
        $this->db->where('supplier', $suppler);
        $this->db->like('nama', $name);
        $this->db->limit(50);
        $query = $this->db->get('partner');
        return $query->result();
    }

    function get_partner_by_id($id)
    {
        $this->db->where('id', $id);
        $query = $this->db->get('partner');
        return $query->row();
    }

    function insert_data_pelunasan_hutang($data)
    {
        try {
            $this->db->insert('acc_pelunasan_hutang', $data);
            $db_error = $this->db->error();

            if ($db_error['code'] > 0) {
                // jika error dari database
                return [
                    'status'  => false,
                    'message' => $db_error['message']
                ];
            }

            return [
                'status'  => true,
                'message' => 'Data berhasil disimpan'
            ];
        } catch (Exception $ex) {
            return [
                'status'  => false,
                'message' => $ex->getMessage()
            ];
        }
    }

    public function update_data_pelunasan_hutang($data)
    {
        try {
            $this->db->update_batch('acc_pelunasan_hutang', $data, 'no_pelunasan');
            $db_error = $this->db->error();

            if ($db_error['code'] > 0) {
                // jika ada error SQL
                return [
                    'status'  => false,
                    'message' => $db_error['message']
                ];
            }

            // Cek apakah ada baris yang berubah
            if ($this->db->affected_rows() === 0) {
                return [
                    'status'  => false,
                    'message' => 'Tidak ada data yang diperbarui (data mungkin sama atau tidak ditemukan)'
                ];
            }

            return [
                'status'  => true,
                'message' => 'Data berhasil diperbarui'
            ];
        } catch (Exception $ex) {
            return [
                'status'  => false,
                'message' => $ex->getMessage()
            ];
        }
    }



    function get_data_by_code($kode)
    {
        $this->db->where('no_pelunasan', $kode);
        $query = $this->db->get('acc_pelunasan_hutang');
        return $query->row();
    }

    function query_get_invoice()
    {
        $this->db->order_by('row_order', 'asc');
        $this->db->select('id, pelunasan_hutang_id, no_pelunasan, no_invoice, origin, DATE(tanggal_invoice) as tanggal_invoice, currency_id, currency, kurs, total_hutang_valas, total_hutang_rp, sisa_hutang_rp, sisa_hutang_valas, pelunasan_rp, pelunasan_valas, row_order, status_bayar');
        $this->db->from('acc_pelunasan_hutang_invoice');

    }

    function get_data_invoice_by_code($kode)
    {
        $this->db->where('no_pelunasan', $kode);
        $this->query_get_invoice();
        $query = $this->db->get();
        return $query->result();
    }

    function get_data_invoice_by_code2($where)
    {
        $this->db->order_by('row_order', 'asc');
        if (count($where) > 0) {
            $this->db->where($where);
        }
        $this->query_get_invoice();
        $query = $this->db->get();
        return $query->result();
    }

    function get_invoice_by_code($where)
    {
        if (count($where) > 0) {
            $this->db->where($where);
        }
        $this->db->order_by('row_order', 'asc');
        $this->db->select('id, pelunasan_hutang_id, no_pelunasan, no_invoice, origin, DATE(tanggal_invoice) as tanggal_invoice, currency_id, currency, kurs, total_hutang_valas, total_hutang_rp, sisa_hutang_rp, sisa_hutang_valas, pelunasan_rp, pelunasan_valas, row_order, status_bayar');
        $this->db->from('acc_pelunasan_hutang_invoice');
        $query = $this->db->get();
        return $query->row();
    }

    function get_data_metode_by_code($kode)
    {
        $this->db->order_by('row_order', 'asc');
        $this->db->where('no_pelunasan', $kode);
        $this->db->select("id, pelunasan_hutang_id, no_pelunasan, no_bukti, DATE(tanggal_bukti) as tanggal_bukti, currency_id, currency, kurs, total_rp, total_valas, row_order, tipe, tipe2, id_bukti");
        $this->db->from('acc_pelunasan_hutang_metode');
        $query = $this->db->get();
        return $query->result();
    }

    function get_metode_by_code($where)
    {
        if (count($where) > 0) {
            $this->db->where($where);
        }
        $this->db->order_by('row_order', 'asc');
        $this->db->select("id, pelunasan_hutang_id, no_pelunasan, no_bukti, DATE(tanggal_bukti) as tanggal_bukti, currency_id, currency, kurs, total_rp, total_valas, row_order, tipe");
        $this->db->from('acc_pelunasan_hutang_metode');
        $query = $this->db->get();
        return $query->row();
    }

    function get_data_summary_by_code($kode)
    {
        $this->db->order_by('id', 'asc');
        $this->db->where('no_pelunasan', $kode);
        $query = $this->db->get('acc_pelunasan_hutang_summary');
        return $query->result();
    }

    function get_data_jurnal_by_code($kode)
    {
        $this->db->where('aph.no_pelunasan', $kode);
        $this->db->select('aje.kode, aje.tanggal_dibuat, aje.periode');
        $this->db->from('acc_jurnal_entries aje');
        $this->db->join('acc_pelunasan_hutang aph', 'aje.kode = aph.no_jurnal','inner');
        $query = $this->db->get();
        $result = $query->row();
        return $result ?: (object)[
            'kode' => '',
            'tanggal_dibuat' => '',
            'periode' => ''
        ];
    }
    

    var $coa_kas = array('2112.01', '2112.02');
    var $coa_um = array('1192.01', '1192.02', '1192.03', '1192.99');

    function query_kas_keluar()
    {
        $this->db->from('acc_kas_keluar a');
        $this->db->join('acc_kas_keluar_detail b', 'a.id = b.kas_keluar_id', 'inner');
    }

    function get_total_kas_keluar_by_partner($where, $type)
    {
        if ($type == 'kas') {
            $this->db->where_in('b.kode_coa', $this->coa_kas);
        } else { // uang muka
            $this->db->where_in('b.kode_coa', $this->coa_um);
        }
        if (count($where) > 0) {
            $this->db->where($where);
        }
        $this->query_kas_keluar();
        $this->db->SELECT('count(*) as total');
        $query = $this->db->get()->row();
        return $query->total ?? 0;
    }


    function query_bank_keluar()
    {
        $this->db->from('acc_bank_keluar a');
        $this->db->join('acc_bank_keluar_detail b', 'a.id = b.bank_keluar_id', 'inner');
    }

    function get_total_bank_keluar_by_partner($where, $type)
    {
        if ($type == 'kas') {
            $this->db->where_in('b.kode_coa', $this->coa_kas);
        } else { // uang muka
            $this->db->where_in('b.kode_coa', $this->coa_um);
        }
        if (count($where) > 0) {
            $this->db->where($where);
        }
        $this->query_bank_keluar();
        $this->db->SELECT('count(*) as total');
        $query = $this->db->get()->row();
        return $query->total ?? 0;
    }


    function query_giro_keluar()
    {
        $this->db->from('acc_giro_keluar a');
        $this->db->join('acc_giro_keluar_detail b', 'a.id = b.giro_keluar_id', 'inner');
    }

    function get_total_giro_keluar_by_partner($where, $type)
    {
        if ($type == 'kas') {
            $this->db->where_in('b.kode_coa', $this->coa_kas);
        } else { // uang muka
            $this->db->where_in('b.kode_coa', $this->coa_um);
        }
        if (count($where) > 0) {
            $this->db->where($where);
        }
        $this->query_giro_keluar();
        $this->db->SELECT('count(*) as total');
        $query = $this->db->get()->row();
        return $query->total ?? 0;
    }


    function query_invoice()
    {
        $this->db->from('invoice');
    }

    function get_total_invoice_by_partner($where)
    {
        if (count($where) > 0) {
            $this->db->where($where);
        }
        $this->query_invoice();
        $this->db->SELECT('count(*) as total');
        $query = $this->db->get()->row();
        return $query->total ?? 0;
    }

    function get_total_invoice_retur_by_partner($where)
    {
        if (count($where) > 0) {
            $this->db->where($where);
        }
        $this->db->from('invoice_retur');
        $this->db->SELECT('count(*) as total');
        $query = $this->db->get()->row();
        return $query->total ?? 0;
    }


    var $column_order2 = array(null, null, 'no_invoice', 'origin', 'created_at', 'c.currency', 'a.nilai_matauang', 'total_rp', 'total_valas', 'hutang_rp', 'hutang_valas');
    var $column_search2 = array('no_invoice', 'origin', 'created_at', 'c.currency', 'a.nilai_matauang', 'total_rp', 'total_valas', 'hutang_rp', 'hutang_valas');
    var $order2         = array('no_invoice' => 'desc');

    function query2()
    {
        $this->db->where('status', 'done');
        $this->db->select("a.id,a.no_invoice, a.created_at, a.order_date, a.tanggal_invoice_supp, a.no_invoice_supp, a.origin, a.status, a.matauang, c.currency, a.nilai_matauang, IFNULL(total_rp, 0) as total_hutang_rp, IFNULL(total_valas,0) as total_hutang_valas, IFNULL(hutang_rp,0) as sisa_hutang_rp, IFNULL(hutang_valas,0) as sisa_hutang_valas");
        $this->db->from("invoice a");
        $this->db->join("currency_kurs c ", "a.matauang = c.id", "left");
    }


    private function _get_datatables_query2()
    {
        $this->query2();
        $i = 0;

        foreach ($this->column_search2 as $item) // loop column 
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

                if (count($this->column_search2) - 1 == $i) //last loop
                    $this->db->group_end(); //close bracket
            }
            $i++;
        }

        if (isset($_POST['order'])) // here order processing
        {
            $this->db->order_by($this->column_order2[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order2)) {
            $order = $this->order2;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    function get_datatables2($partner)
    {
        $this->_get_datatables_query2();
        $this->db->where("a.id_supplier", $partner);
        $this->db->where("a.lunas", 0);
        if (isset($_POST["length"]) && $_POST["length"] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }

    function count_filtered2($partner)
    {
        $this->db->where("a.id_supplier", $partner);
        $this->db->where("a.lunas", 0);
        $this->_get_datatables_query2();
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all2($partner)
    {
        $this->query2();
        $this->db->where("a.lunas", 0);
        $this->db->where("a.id_supplier", $partner);
        return $this->db->count_all_results();
    }


    function get_data_invoice_by_id($where)
    {
        $this->query2();
        // $this->db->where('a.id', $id);
        if (count($where) > 0) {
            $this->db->where($where);
        }
        $query = $this->db->get();
        return $query->row();
    }


    function get_last_row_order_invoice_by_id($id)
    {
        $this->db->order_by('id', 'desc');
        $this->db->where('pelunasan_hutang_id', $id);
        $this->db->select('max(row_order) as last');
        $this->db->from('acc_pelunasan_hutang_invoice');
        $last_no = $this->db->get();

        $result = $last_no->row();
        if (empty($result->last)) {
            $no = 1;
        } else {
            $no = (int) $result->last + 1;
        }
        return $no;
    }

    function get_last_row_order_metode_by_id($id)
    {
        $this->db->order_by('id', 'desc');
        $this->db->where('pelunasan_hutang_id', $id);
        $this->db->select('max(row_order) as last');
        $this->db->from('acc_pelunasan_hutang_metode');
        $last_no = $this->db->get();

        $result = $last_no->row();
        if (empty($result->last)) {
            $no = 1;
        } else {
            $no = (int) $result->last + 1;
        }
        return $no;
    }

    function insert_data_pelunasan_hutang_invoice($data_insert)
    {
        try {
            $this->db->insert_batch('acc_pelunasan_hutang_invoice', $data_insert);
            $db_error = $this->db->error();
            if ($db_error['code'] > 0) {
                throw new Exception($db_error['message']);
            }
            return "";
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
    }

    function insert_data_pelunasan_hutang_metode($data_insert)
    {
        try {
            $this->db->insert_batch('acc_pelunasan_hutang_metode', $data_insert);
            $db_error = $this->db->error();
            if ($db_error['code'] > 0) {
                throw new Exception($db_error['message']);
            }
            return "";
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
    }


    function cek_invoice_input_by_kode($no_pelunasan, $no_invoice)
    {
        $this->db->where('no_pelunasan', $no_pelunasan);
        $this->db->where('no_invoice', $no_invoice);
        $query = $this->db->get('acc_pelunasan_hutang_invoice');
        return $query->num_rows();
    }

    function cek_metode_input_by_kode($where)
    {
        if (count($where) > 0) {
            $this->db->where($where);
        }
        $query = $this->db->get('acc_pelunasan_hutang_metode');
        return $query->num_rows();
    }


    var $column_order3 = array(null, null, 'no_bukti', 'tanggal', 'currency', 'kurs', 'total_rp', 'total_valas');
    var $column_search3 = array('no_bukti', 'tanggal', 'currency', 'kurs', 'total_rp', 'total_valas');
    var $order3         = array('tanggal' => 'asc');

    function query3($partner, $type)
    {
        if ($type == 'kas') {
            $this->db->where_in('b.kode_coa', $this->coa_kas);
        } else { // uang muka
            $this->db->where_in('b.kode_coa', $this->coa_um);
        }
        $where = ["a.partner_id" => $partner, "a.status" => 'confirm', "b.lunas" => 0];
        if (count($where) > 0) {
            $this->db->where($where);
        }
        $this->db->select("b.id,(a.no_bk) as no_bukti, b.tanggal, b.currency_id, c.currency, b.kurs, IF(c.currency='IDR', b.nominal, IFNULL(b.nominal*b.kurs,0)) as total_rp, IF(c.currency != 'IDR', b.nominal, 0) as total_valas, 'bank' as tipe2");
        $this->db->from("acc_bank_keluar a");
        $this->db->join("acc_bank_keluar_detail b ", "a.id = b.bank_keluar_id", "left");
        $this->db->join("currency_kurs c ", "b.currency_id = c.id", "left");
        return $query1_sql = $this->db->get_compiled_select();
    }

    function query3B($partner, $type)
    {
        if ($type == 'kas') {
            $this->db->where_in('e.kode_coa', $this->coa_kas);
        } else { // uang muka
            $this->db->where_in('e.kode_coa', $this->coa_um);
        }
        $where = ["h.partner_id" => $partner, "h.status" => 'confirm', 'e.lunas' => 0];
        if (count($where) > 0) {
            $this->db->where($where);
        }
        $this->db->select("e.id,(h.no_kk) as no_bukti , e.tanggal, e.currency_id, i.currency, e.kurs, IF(i.currency='IDR', e.nominal, IFNULL(e.nominal*e.kurs,0) ) as total_rp, IF(i.currency != 'IDR', e.nominal, 0) as total_valas, 'kas' as tipe2");
        $this->db->from("acc_kas_keluar h");
        $this->db->join("acc_kas_keluar_detail e ", "h.id = e.kas_keluar_id", "left");
        $this->db->join("currency_kurs i ", "e.currency_id = i.id", "left");
        return  $query2_sql = $this->db->get_compiled_select();
    }

    function query3C($partner, $type)
    {
        if ($type == 'kas') {
            $this->db->where_in('g.kode_coa', $this->coa_kas);
        } else { // uang muka
            $this->db->where_in('g.kode_coa', $this->coa_um);
        }
        $where = ["f.partner_id" => $partner, "f.status" => 'confirm', 'g.lunas' => 0];
        if (count($where) > 0) {
            $this->db->where($where);
        }
        $this->db->select("g.id,(f.no_gk) as no_bukti, g.tanggal, g.currency_id, j.currency, g.kurs, IF(j.currency='IDR', g.nominal, IFNULL(g.nominal*g.kurs,0)) as total_rp, IF(j.currency != 'IDR', g.nominal, 0) as total_valas, 'giro' as tipe2");
        $this->db->from("acc_giro_keluar f");
        $this->db->join("acc_giro_keluar_detail g ", "f.id = g.giro_keluar_id", "left");
        $this->db->join("currency_kurs j ", "g.currency_id = j.id", "left");
        return $query3_sql = $this->db->get_compiled_select();
    }


    private function _get_datatables_query3($partner, $type)
    {
        $union_sql = $this->query3($partner, $type) . ' UNION ALL ' . $this->query3B($partner, $type) . ' UNION ALL ' . $this->query3C($partner, $type);
        // $query = $this->db->query($union_sql);

        $this->db->SELECT('*');
        $this->db->from('(' . $union_sql . ') as sub');

        $i = 0;

        foreach ($this->column_search3 as $item) // loop column 
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

                if (count($this->column_search3) - 1 == $i) //last loop
                    $this->db->group_end(); //close bracket
            }
            $i++;
        }

        if (isset($_POST['order'])) // here order processing
        {
            $this->db->order_by($this->column_order3[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order3)) {
            $order = $this->order3;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    function get_datatables3($partner, $type)
    {
        $this->_get_datatables_query3($partner, $type);
        if (isset($_POST["length"]) && $_POST["length"] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }

    function count_filtered3($partner, $type)
    {
        $this->_get_datatables_query3($partner, $type);
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all3($partner, $type)
    {
        $union_sql = $this->query3($partner, $type) . ' UNION ALL ' . $this->query3B($partner, $type) . ' UNION ALL ' . $this->query3C($partner, $type);
        $this->db->SELECT('*');
        $this->db->from('(' . $union_sql . ') as sub');
        return $this->db->count_all_results();
    }


    function get_data_metode_pelunasan_by_id($partner, $type, $no_bukti)
    {
        $union_sql = $this->query3($partner, $type) . ' UNION ALL ' . $this->query3B($partner, $type) . ' UNION ALL ' . $this->query3C($partner, $type);

        $this->db->where('no_bukti', $no_bukti);
        $this->db->SELECT('*');
        $this->db->from('(' . $union_sql . ') as sub');
        $query = $this->db->get();
        return $query->row();
    }

    function get_total_metode_pelunasan_by_no($no_pelunasan)
    {
        $this->db->where('no_pelunasan', $no_pelunasan);
        $this->db->select('no_pelunasan, IFNULL(sum(total_rp),0) as sum_rp, ifnull(sum(total_valas),0) as sum_valas');
        $this->db->from('acc_pelunasan_hutang_metode');
        $query = $this->db->get();
        return $query->row();
    }



    function update_pelunasan_invoice_by_kode($data_update, $kode = null)
    {
        try {
            $this->db->where('no_pelunasan', $kode);
            $this->db->update_batch('acc_pelunasan_hutang_invoice', $data_update, 'id');
            $db_error = $this->db->error();
            if ($db_error['code'] > 0) {
                throw new Exception($db_error['message']);
            }
            return "";
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
    }


    function get_acc_pelunasan_hutang_invoice_id($id)
    {
        $this->db->where('id', $id);
        $query = $this->db->get('acc_pelunasan_hutang_invoice');
        return $query->row();
    }

    function delete_pelunasan_hutang_invoice_by_kode($where)
    {
        try {
            // $data = array('no_pelunasan' => $no_pelunasan, 'id' => $id);
            $this->db->delete('acc_pelunasan_hutang_invoice', $where);
            $db_error = $this->db->error();
            if ($db_error['code'] > 0) {
                throw new Exception($db_error['message']);
            }
            return "";
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
    }


    function delete_pelunasan_hutang_metode_by_kode($where)
    {
        try {
            // $data = array('no_pelunasan' => $no_pelunasan, 'id' => $id);
            $this->db->delete('acc_pelunasan_hutang_metode', $where);
            $db_error = $this->db->error();
            if ($db_error['code'] > 0) {
                throw new Exception($db_error['message']);
            }
            return "";
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
    }


    function get_total_hutang($where)
    {

        if (count($where) > 0) {
            $this->db->where($where);
        }
        $this->db->select("IFNULL(sum(sisa_hutang_rp),0) as total_hutang_rp, IFNULL(sum(sisa_hutang_valas),0) as total_hutang_valas, IFNULL(sum(pelunasan_valas),0) as total_pelunasan_valas, IFNULL(sum(pelunasan_rp),0) as total_pelunasan_rp");
        $this->db->from('acc_pelunasan_hutang_invoice');
        $query = $this->db->get()->row();
        return $query;
    }

    public function get_currency_by_pelunasan($where)
    {
        // Query pertama
        $this->db->select('no_pelunasan, currency_id, currency, kurs');
        $this->db->from('acc_pelunasan_hutang_invoice');
        if(count($where) > 0){
            $this->db->where($where);
        }
        $query1 = $this->db->get_compiled_select();

        // Query kedua
        $this->db->select('no_pelunasan, currency_id, currency, kurs');
        $this->db->from('acc_pelunasan_hutang_metode');
        if(count($where) > 0){
            $this->db->where($where);
        }
        $query2 = $this->db->get_compiled_select();

        // Gabungkan dengan UNION ALL
        $final_query = "
            SELECT *
            FROM (
                ($query1)
                UNION ALL
                ($query2)
            ) AS pm
            GROUP BY currency_id
        ";

        // Eksekusi dan kembalikan hasil
        return $this->db->query($final_query)->row();
    }

    function get_total_pelunasan($where)
    {
        if (count($where) > 0) {
            $this->db->where($where);
        }
        $this->db->select("pelunasan_hutang_id, IFNULL(sum( IF(currency='IDR', total_rp, kurs*total_valas)),0) as total_pelunasan_rp, IFNULL(sum(total_valas),0) as total_pelunasan_valas ");
        $this->db->from('acc_pelunasan_hutang_metode');
        $query = $this->db->get()->row();
        return $query;
    }


    function delete_pelunasan_hutang_summary_by_kode($no_pelunasan)
    {
        try {
            $data = array('no_pelunasan' => $no_pelunasan);
            $this->db->delete('acc_pelunasan_hutang_summary', $data);
            $db_error = $this->db->error();
            if ($db_error['code'] > 0) {
                throw new Exception($db_error['message']);
            }
            return "";
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
    }


    function insert_data_pelunasan_hutang_summary($data)
    {
        try {
            $this->db->insert_batch('acc_pelunasan_hutang_summary', $data);
            $db_error = $this->db->error();
            if ($db_error['code'] > 0) {
                throw new Exception($db_error['message']);
            }
            return "";
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
    }


    var $column_order4 = array(null, null, 'no_bukti', 'tanggal', 'currency', 'kurs', 'total_rp', 'total_valas');
    var $column_search4 = array('no_bukti', 'tanggal', 'currency', 'kurs', 'total_rp', 'total_valas');
    var $order4         = array('tanggal' => 'asc');

    function query4($partner)
    {

        $where = ["invr.id_supplier" => $partner, "invr.status" => 'done', "invr.lunas" => 0];
        if (count($where) > 0) {
            $this->db->where($where);
        }
        $this->db->select("invr.id, invr.no_inv_retur as no_bukti, invr.created_at as tanggal, curr.currency, invr.matauang as currency_id, invr.nilai_matauang as kurs, invr.total_rp, invr.total_valas, 'retur' as tipe2");
        $this->db->from("invoice_retur invr ");
        $this->db->join("currency_kurs curr ", "invr.matauang = curr.id", "left");
        return $query4_sql = $this->db->get_compiled_select();
    }

    private function _get_datatables_query4($partner)
    {
        $union_sql = $this->query4($partner);
        // $query = $this->db->query($union_sql);

        $this->db->SELECT('*');
        $this->db->from('(' . $union_sql . ') as sub');

        $i = 0;

        foreach ($this->column_search4 as $item) // loop column 
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

                if (count($this->column_search4) - 1 == $i) //last loop
                    $this->db->group_end(); //close bracket
            }
            $i++;
        }

        if (isset($_POST['order'])) // here order processing
        {
            $this->db->order_by($this->column_order4[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order4)) {
            $order = $this->order4;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    function get_datatables4($partner)
    {
        $this->_get_datatables_query4($partner);
        if (isset($_POST["length"]) && $_POST["length"] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }

    function count_filtered4($partner)
    {
        $this->_get_datatables_query4($partner);
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all4($partner)
    {
        $union_sql = $this->query4($partner);
        $this->db->SELECT('*');
        $this->db->from('(' . $union_sql . ') as sub');
        return $this->db->count_all_results();
    }


    function get_data_metode_pelunasan_retur_by_id($partner, $no_bukti)
    {
        $union_sql = $this->query4($partner);
        $this->db->where('no_bukti', $no_bukti);
        $this->db->SELECT('*');
        $this->db->from('(' . $union_sql . ') as sub');
        $query = $this->db->get();
        return $query->row();
    }


    function get_list_coa_by_kode($name)
    {
        // if ($where_in > 0) {
        //     $this->db->where_in('kode_coa', $where_in);
        // }
        $this->db->limit(50);
        $this->db->where('level',5);
        $this->db->like('nama', $name);
        $this->db->order_by('kode_coa', 'asc');
        $this->db->select('*');
        $this->db->from("acc_coa");
        $query = $this->db->get();
        return $query->result();
    }

    function get_coa_by_kode($where)
    {
        if ($where > 0) {
            $this->db->where($where);
        }
        $this->db->select('*');
        $this->db->from("acc_coa");
        $query = $this->db->get();
        return $query->row();
    }


    function get_data_summary_by_id($id)
    {
        $this->db->order_by('id', 'asc');
        $this->db->where('id', $id);
        $query = $this->db->get('acc_pelunasan_hutang_summary');
        return $query->row();
    }

    function update_data_pelunasan_hutang_summary($data)
    {
        try {
            $this->db->update_batch('acc_pelunasan_hutang_summary', $data, 'id');
            $db_error = $this->db->error();
            if ($db_error['code'] > 0) {
                throw new Exception($db_error['message']);
            }
            return "";
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
    }

    function delete_pelunasan_hutang_summary_koreksi_by_id($where)
    {
        try {
            $this->db->delete('acc_pelunasan_hutang_summary_koreksi', $where);
            $db_error = $this->db->error();
            if ($db_error['code'] > 0) {
                throw new Exception($db_error['message']);
            }
            return "";
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
    }

    function insert_data_pelunasan_hutang_summary_koreksi($data_insert)
    {
        try {
            $this->db->insert_batch('acc_pelunasan_hutang_summary_koreksi', $data_insert);
            $db_error = $this->db->error();
            if ($db_error['code'] > 0) {
                throw new Exception($db_error['message']);
            }
            return "";
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
    }


    function get_coa_summary_id($where)
    {
        // $this->db->where('pelunasan_summary_id', $id);
        if(count($where) > 0){
            $this->db->where($where);
        }
        $this->db->select('aphsk.posisi, aphsk.kode_coa, aphsk.nama_coa, aphs.koreksi');
        $this->db->order_by('aphsk.id');
        $this->db->from('acc_pelunasan_hutang_summary_koreksi aphsk');
        $this->db->join('acc_pelunasan_hutang_summary aphs', 'aphs.id = aphsk.pelunasan_summary_id', 'inner');
        $result = $this->db->get();
        return $result;
    }

    function cek_metode_pelunasan_tipe_by_id($id)
    {
        $this->db->where('pelunasan_hutang_id', $id);
        $this->db->select('*');
        $this->db->from('acc_pelunasan_hutang_metode');
        $result = $this->db->get();
        return $result->row();
    }


    function insert_jurnal_entries($data)
    {
        try {
            $this->db->insert('acc_jurnal_entries', $data);
            $db_error = $this->db->error();
            if ($db_error['code'] > 0) {
                throw new Exception($db_error['message']);
            }

            // Cek apakah ada data yang berhasil dimasukkan
            if ($this->db->affected_rows() === 0) {
                throw new Exception("Gagal insert: tidak ada data yang dimasukkan.");
            }

            return "";
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
    }

    function insert_jurnal_entries_items($data_insert)
    {
        try {
            $this->db->insert_batch('acc_jurnal_entries_items', $data_insert);

            // Cek error MySQL
            $db_error = $this->db->error();
            if ($db_error['code'] > 0) {
                throw new Exception($db_error['message']);
            }

            // Cek apakah ada data yang berhasil dimasukkan
            if ($this->db->affected_rows() === 0) {
                throw new Exception("Gagal insert: tidak ada data yang dimasukkan.");
            }

            return ""; // sukses
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
    }

    function get_list_koreksi($where = null)
    {
        $this->db->order_by('nama_koreksi', 'asc');
        if(($where)){
            $this->db->like('nama_koreksi', $where);
        }
        $query = $this->db->get('acc_pelunasan_koreksi');
        return $query->result();
    }


    function get_coa_default_by_kode($where)
    {
        if(count($where) > 0){
            $this->db->where($where);
        }

        $this->db->select('b.kode_coa, c.nama as nama_coa');
        $this->db->from('acc_pelunasan_koreksi a');
        $this->db->join('acc_pelunasan_koreksi_coa b','a.id = b.pelunasan_koreksi_id','inner');
        $this->db->join('acc_coa c', 'b.kode_coa = c.kode_coa', 'left');
        $result = $this->db->get();
        return $result->row();
    }

    function cek_metode_pelunasan_group($where)
    {
        if(count($where) > 0){
            $this->db->where($where);
        }
        $this->db->group_by('tipe');
        $this->db->select('tipe');
        $this->db->from('acc_pelunasan_hutang_metode');
        $result = $this->db->get();
        return $result;
    }


    function get_koreksi_by_kode($where)
    {
        $this->db->order_by('nama_koreksi', 'asc');
        if(($where)){
            $this->db->where($where);
        }
        $query = $this->db->get('acc_pelunasan_koreksi');
        return $query->row();
    }


    function get_invoice_by_kode($where) {

        if(count($where) > 0){
            $this->db->where($where);
        }
        $this->query2();
        $result = $this->db->get();
        return $result;
    }


    function update_invoice_by_kode($data_update)
    {
        try {
            $this->db->update_batch('invoice', $data_update, 'no_invoice');
            $db_error = $this->db->error();
            if ($db_error['code'] > 0) {
                throw new Exception($db_error['message']);
            }
            
            // Cek apakah ada data yang berubah
            if ($this->db->affected_rows() === 0) {
                throw new Exception("Gagal update: tidak ada data yang diperbarui.");
            }
            return "";
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
    }

    function update_kas_bank_kode($data_update, $kas_bank = null)
    {
        try {
            $this->db->update_batch($kas_bank, $data_update, 'id');

            // Cek error database
            $db_error = $this->db->error();
            if ($db_error['code'] > 0) {
                throw new Exception($db_error['message']);
            }

            // Cek apakah ada data yang berubah
            if ($this->db->affected_rows() === 0) {
                throw new Exception("Gagal update: tidak ada data yang diperbarui.");
            }

            return ""; // sukses tanpa pesan
        } catch (Exception $ex) {
            return $ex->getMessage(); // kembalikan pesan error
        }
    }


    function get_kode_jurnal_by_nama($where)
    {
        if(count($where) > 0){
            $this->db->where($where);
        }
        $result = $this->db->get('mst_jurnal');
        return $result->row();
    }

    function cek_data_metode_valid_by_code($metode, $where)
    {   
        if(count($where) > 0) {
            $this->db->where($where);
        }
        if($metode == 'bank') {
            $this->db->select('acc_bank_keluar.no_bk, acc_bank_keluar_detail.id, acc_bank_keluar_detail.nominal');
            $this->db->FROM('acc_bank_keluar');
            $this->db->join('acc_bank_keluar_detail', 'acc_bank_keluar_detail.bank_keluar_id = acc_bank_keluar.id', 'inner');
            $result = $this->db->get();
        } else if($metode =='giro') {
            $this->db->select('acc_giro_keluar.no_gk, acc_giro_keluar_detail.id, acc_giro_keluar_detail.nominal');
            $this->db->FROM('acc_giro_keluar');
            $this->db->join('acc_giro_keluar_detail', 'acc_giro_keluar_detail.giro_keluar_id = acc_giro_keluar.id', 'inner');
            $result = $this->db->get();
        } else if($metode =='retur') {
            $this->db->select('id, no_inv_retur, total, lunas');
            $this->db->FROM('invoice_retur');
            $result = $this->db->get();
        } else {
            $this->db->select('acc_kas_keluar.no_kk, acc_kas_keluar_detail.id, acc_kas_keluar_detail.nominal');
            $this->db->FROM('acc_kas_keluar');
            $this->db->join('acc_kas_keluar_detail', 'acc_kas_keluar_detail.kas_keluar_id = acc_kas_keluar.id', 'inner');
            $result = $this->db->get();
        }

        return $result->row();
    }


      function update_by_kode($table, $data_update, $where)
    {
        try {
            $this->db->update($table, $data_update, $where);
            $db_error = $this->db->error();
            if ($db_error['code'] > 0) {
                throw new Exception($db_error['message']);
            }

            // Cek apakah ada data yang berubah
            if ($this->db->affected_rows() === 0) {
                throw new Exception("Gagal update: tidak ada data yang diperbarui.");
            }

            return "";
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
    }

}
