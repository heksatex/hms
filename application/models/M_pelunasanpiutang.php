
<?php


defined('BASEPATH') or exit('No Direct Script Acces Allowed');

class M_pelunasanpiutang extends CI_Model
{

    var $column_order = array(null, 'no_pelunasan', 'tanggal_transaksi', 'partner_nama', 'nama_status');
    var $column_search = array('no_pelunasan', 'tanggal_transaksi', 'partner_nama', 'nama_status');
    var $order        = array('no_pelunasan' => 'desc');


    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    private function _get_datatables_query()
    {

        $this->db->select("pl.no_pelunasan, pl.tanggal_dibuat, pl.tanggal_transaksi, pl.status, pl.partner_id, pl.partner_nama, mmss.nama_status");
        $this->db->from("acc_pelunasan_piutang pl");
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
        $this->db->from("acc_pelunasan_piutang pl");
        $this->db->join("main_menu_sub_status mmss", "mmss.jenis_status=pl.status", "inner");
        $this->db->where("mmss.main_menu_sub_kode", $mmss);
        return $this->db->count_all_results();
    }

    public function get_list_partner_customer($cust, $name)
    {
        $this->db->order_by('nama', 'asc');
        $this->db->where('customer', $cust);
        $this->db->like('nama', $name);
        $this->db->limit(50);
        $query = $this->db->get('partner');
        return $query->result();
    }


    function query_faktur()
    {
        $this->db->from('acc_faktur_penjualan');
    }

    function get_total_faktur_by_partner($where)
    {
        if (count($where) > 0) {
            $this->db->where($where);
        }
        $this->query_faktur();
        $this->db->SELECT('count(*) as total');
        $query = $this->db->get()->row();
        return $query->total ?? 0;
    }

    var $where_jenis_transaksi_kas = array('piutang');
    var $where_jenis_transaksi_um = array('um_penjualan');

    function get_list_coa_kas_by_transaksi()
    {
        $this->db->select('kode_coa');
        $this->db->where_in('jenis_transaksi', $this->where_jenis_transaksi_kas);
        $rows = $this->db->get('acc_coa')->result_array();

        $list_coa = array_column($rows, 'kode_coa');
        if (empty($list_coa)) {
            $list_coa = ['__EMPTY__']; 
        }
        return array_map('strval', $list_coa);
    }

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


    function query_kas_masuk()
    {
        $this->db->from('acc_kas_masuk akk');
        $this->db->join('acc_kas_masuk_detail akkd', 'akk.id = akkd.kas_masuk_id', 'inner');
    }

    function get_total_kas_masuk_by_partner($where, $type)
    {
        if ($type == 'kas') {
            $this->db->where_in('akkd.kode_coa', $this->get_list_coa_kas_by_transaksi());
        } else { // uang muka
            $this->db->where_in('akkd.kode_coa', $this->get_list_coa_um_by_transaksi());
        }
        if (count($where) > 0) {
            $this->db->where($where);
        }
        $this->query_kas_masuk();
        $this->db->SELECT('count(*) as total');
        $query = $this->db->get()->row();
        return $query->total ?? 0;
    }


    function query_bank_masuk()
    {
        $this->db->from('acc_bank_masuk abm');
        $this->db->join('acc_bank_masuk_detail abmd', 'abm.id = abmd.bank_masuk_id', 'inner');
    }

    function get_total_bank_masuk_by_partner($where, $type)
    {
        if ($type == 'kas') {
            $this->db->where_in('abmd.kode_coa', $this->get_list_coa_kas_by_transaksi());
        } else { // uang muka
            $this->db->where_in('abmd.kode_coa', $this->get_list_coa_um_by_transaksi());
        }
        if (count($where) > 0) {
            $this->db->where($where);
        }
        $this->query_bank_masuk();
        $this->db->SELECT('count(*) as total');
        $query = $this->db->get()->row();
        return $query->total ?? 0;
    }


    function query_giro_masuk()
    {
        $this->db->from('acc_giro_masuk agm');
        $this->db->join('acc_giro_masuk_detail agmd', 'agm.id = agmd.giro_masuk_id', 'inner');
    }

    function get_total_giro_masuk_by_partner($where, $type)
    {
        if ($type == 'kas') {
            $this->db->where_in('agmd.kode_coa', $this->get_list_coa_kas_by_transaksi());
        } else { // uang muka
            $this->db->where_in('agmd.kode_coa', $this->get_list_coa_um_by_transaksi());
        }
        if (count($where) > 0) {
            $this->db->where($where);
        }
        $this->query_giro_masuk();
        $this->db->SELECT('count(*) as total');
        $query = $this->db->get()->row();
        return $query->total ?? 0;
    }


    function get_total_faktur_retur_by_partner($where)
    {
        if (count($where) > 0) {
            $this->db->where($where);
        }
        $this->db->from('acc_retur_penjualan');
        $this->db->SELECT('count(*) as total');
        $query = $this->db->get()->row();
        return $query->total ?? 0;
    }

    function get_data_by_code($kode)
    {
        $this->db->where('no_pelunasan', $kode);
        $query = $this->db->get('acc_pelunasan_piutang');
        return $query->row();
    }

    function insert_data_pelunasan_piutang($data)
    {
        try {
            $this->db->insert('acc_pelunasan_piutang', $data);
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

    public function update_data_pelunasan_piutang($data)
    {
        try {
            $this->db->update_batch('acc_pelunasan_piutang', $data, 'no_pelunasan');
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



    function delete_pelunasan_piutang_faktur_by_kode($where)
    {
        try {
            // $data = array('no_pelunasan' => $no_pelunasan, 'id' => $id);
            $this->db->delete('acc_pelunasan_piutang_faktur', $where);
            $db_error = $this->db->error();
            if ($db_error['code'] > 0) {
                throw new Exception($db_error['message']);
            }
            return "";
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
    }


    function delete_pelunasan_piutang_summary_koreksi_by_id($where)
    {
        try {
            $this->db->delete('acc_pelunasan_piutang_summary_koreksi', $where);
            $db_error = $this->db->error();
            if ($db_error['code'] > 0) {
                throw new Exception($db_error['message']);
            }
            return "";
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
    }


    function delete_pelunasan_piutang_metode_by_kode($where)
    {
        try {
            // $data = array('no_pelunasan' => $no_pelunasan, 'id' => $id);
            $this->db->delete('acc_pelunasan_piutang_metode', $where);
            $db_error = $this->db->error();
            if ($db_error['code'] > 0) {
                throw new Exception($db_error['message']);
            }
            return "";
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
    }

    function delete_pelunasan_piutang_summary_by_kode($no_pelunasan)
    {
        try {
            $data = array('no_pelunasan' => $no_pelunasan);
            $this->db->delete('acc_pelunasan_piutang_summary', $data);
            $db_error = $this->db->error();
            if ($db_error['code'] > 0) {
                throw new Exception($db_error['message']);
            }
            return "";
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
    }


    function get_data_summary_by_code($kode)
    {
        $this->db->order_by('id', 'asc');
        $this->db->where('no_pelunasan', $kode);
        $query = $this->db->get('acc_pelunasan_piutang_summary');
        return $query->result();
    }


    function query_get_faktur()
    {
        $this->db->order_by('row_order', 'asc');
        $this->db->select('id, pelunasan_piutang_id, no_pelunasan, no_faktur, no_sj, DATE(tanggal_faktur) as tanggal_faktur, currency_id, currency, kurs, total_piutang_valas, total_piutang_rp, sisa_piutang_rp, sisa_piutang_valas, pelunasan_rp, pelunasan_valas, row_order, status_bayar');
        $this->db->from('acc_pelunasan_piutang_faktur');

    }

    function get_data_faktur_by_code($kode)
    {
        $this->db->where('no_pelunasan', $kode);
        $this->query_get_faktur();
        $query = $this->db->get();
        return $query->result();
    }


    function get_data_metode_by_code($kode)
    {
        $this->db->order_by('row_order', 'asc');
        $this->db->where('no_pelunasan', $kode);
        $this->db->select("id, pelunasan_piutang_id, no_pelunasan, no_bukti, DATE(tanggal_bukti) as tanggal_bukti, uraian, currency_id, currency, kurs, total_rp, total_valas, row_order, tipe, tipe2, id_bukti");
        $this->db->from('acc_pelunasan_piutang_metode');
        $query = $this->db->get();
        return $query->result();
    }


    var $column_order2 = array(null, null, 'no_faktur', 'no_sj', 'tanggal', 'currency', 'faktur.kurs_nominal', 'total_piutang_rp', 'total_piutang_valas', 'piutang_rp', 'piutang_valas');
    var $column_search2 = array('no_faktur', 'no_sj', 'tanggal', 'currency', 'faktur.kurs_nominal', 'total_piutang_rp', 'total_piutang_valas', 'piutang_rp', 'piutang_valas');
    var $order2         = array('tanggal' => 'asc');

    function query2()
    {
        $this->db->where('status', 'confirm');
        $this->db->select("faktur.id,faktur.no_faktur, faktur.tanggal, faktur.no_sj, faktur.tipe, faktur.status, faktur.kurs, currency_kurs.currency, faktur.kurs_nominal, IFNULL(total_piutang_rp, 0) as total_piutang_rp, IFNULL(total_piutang_valas,0) as total_piutang_valas, IFNULL(piutang_rp,0) as sisa_piutang_rp, IFNULL(piutang_valas,0) as sisa_piutang_valas");
        $this->db->from("acc_faktur_penjualan faktur");
        $this->db->join("currency_kurs ", "faktur.kurs = currency_kurs.id", "left");
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
        $this->db->where("faktur.partner_id", $partner);
        $this->db->where("faktur.lunas", 0);
        if (isset($_POST["length"]) && $_POST["length"] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }

    function count_filtered2($partner)
    {
        $this->db->where("faktur.partner_id", $partner);
        $this->db->where("faktur.lunas", 0);
        $this->_get_datatables_query2();
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all2($partner)
    {
        $this->query2();
        $this->db->where("faktur.lunas", 0);
        $this->db->where("faktur.partner_id", $partner);
        return $this->db->count_all_results();
    }

    function get_data_faktur_by_id($where)
    {
        $this->query2();
        // $this->db->where('a.id', $id);
        if (count($where) > 0) {
            $this->db->where($where);
        }
        $query = $this->db->get();
        return $query->row();
    }


    function get_last_row_order_faktur_by_id($id)
    {
        $this->db->order_by('id', 'desc');
        $this->db->where('pelunasan_piutang_id', $id);
        $this->db->select('max(row_order) as last');
        $this->db->from('acc_pelunasan_piutang_faktur');
        $last_no = $this->db->get();

        $result = $last_no->row();
        if (empty($result->last)) {
            $no = 1;
        } else {
            $no = (int) $result->last + 1;
        }
        return $no;
    }


    function cek_faktur_input_by_kode(array $where = [])
    {
        if (count($where) > 0) {
            $this->db->where($where);
        }
        $query = $this->db->get('acc_pelunasan_piutang_faktur');
        return $query;
    }

    function insert_data_pelunasan_piutang_faktur($data_insert)
    {
        try {
            $this->db->insert_batch('acc_pelunasan_piutang_faktur', $data_insert);
            $db_error = $this->db->error();
            if ($db_error['code'] > 0) {
                throw new Exception($db_error['message']);
            }
            return "";
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
    }


    function get_data_jurnal_by_code($kode)
    {
        $this->db->where('aph.no_pelunasan', $kode);
        $this->db->select('aje.kode, aje.tanggal_dibuat, aje.periode');
        $this->db->from('acc_jurnal_entries aje');
        $this->db->join('acc_pelunasan_piutang aph', 'aje.kode = aph.no_jurnal','inner');
        $query = $this->db->get();
        $result = $query->row();
        return $result ?: (object)[
            'kode' => '',
            'tanggal_dibuat' => '',
            'periode' => ''
        ];
    }

    var $column_order3 = array(null, null, 'no_bukti', 'tanggal','uraian','currency', 'kurs', 'total_rp', 'total_valas');
    var $column_search3 = array('no_bukti', 'tanggal', 'currency','uraian', 'kurs', 'total_rp', 'total_valas');
    var $order3         = array('tanggal' => 'asc');

    function query3($partner, $type)
    {
        if ($type == 'kas') {
            $this->db->where_in('abmd.kode_coa', $this->get_list_coa_kas_by_transaksi());
        } else { // uang muka
            $this->db->where_in('abmd.kode_coa', $this->get_list_coa_um_by_transaksi());
        }
        $where = ["abm.partner_id" => $partner, "abm.status" => 'confirm', "abmd.lunas" => 0];
        if (count($where) > 0) {
            $this->db->where($where);
        }
        $this->db->select("abmd.id,(abm.no_bm) as no_bukti, abmd.tanggal, abmd.currency_id, ck1.currency, abmd.kurs, IF(ck1.currency='IDR', abmd.nominal, IFNULL(abmd.nominal*abmd.kurs,0)) as total_rp, IF(ck1.currency != 'IDR', abmd.nominal, 0) as total_valas, 'bank' as tipe2, abmd.uraian, abmd.kode_coa");
        $this->db->from("acc_bank_masuk abm");
        $this->db->join("acc_bank_masuk_detail abmd ", "abm.id = abmd.bank_masuk_id", "left");
        $this->db->join("currency_kurs ck1 ", "abmd.currency_id = ck1.id", "left");
        return $query1_sql = $this->db->get_compiled_select();
    }

    function query3B($partner, $type)
    {
        if ($type == 'kas') {
            $this->db->where_in('akmd.kode_coa', $this->get_list_coa_kas_by_transaksi());
        } else { // uang muka
            $this->db->where_in('akmd.kode_coa', $this->get_list_coa_um_by_transaksi());
        }
        $where = ["akm.partner_id" => $partner, "akm.status" => 'confirm', 'akmd.lunas' => 0];
        if (count($where) > 0) {
            $this->db->where($where);
        }
        $this->db->select("akmd.id,(akm.no_km) as no_bukti , akmd.tanggal, akmd.currency_id, ck2.currency, akmd.kurs, IF(ck2.currency='IDR', akmd.nominal, IFNULL(akmd.nominal*akmd.kurs,0) ) as total_rp, IF(ck2.currency != 'IDR', akmd.nominal, 0) as total_valas, 'kas' as tipe2, akmd.uraian, akmd.kode_coa");
        $this->db->from("acc_kas_masuk akm");
        $this->db->join("acc_kas_masuk_detail akmd ", "akm.id = akmd.kas_masuk_id", "left");
        $this->db->join("currency_kurs ck2 ", "akmd.currency_id = ck2.id", "left");
        return  $query2_sql = $this->db->get_compiled_select();
    }

    function query3C($partner, $type)
    {
        if ($type == 'kas') {
            $this->db->where_in('agmd.kode_coa', $this->get_list_coa_kas_by_transaksi());
        } else { // uang muka
            $this->db->where_in('agmd.kode_coa', $this->get_list_coa_um_by_transaksi());
        }
        $where = ["agm.partner_id" => $partner, "agm.status" => 'confirm', 'agmd.lunas' => 0];
        if (count($where) > 0) {
            $this->db->where($where);
        }
        $this->db->select("agmd.id,(agm.no_gm) as no_bukti, agmd.tanggal, agmd.currency_id, ck3.currency, agmd.kurs, IF(ck3.currency='IDR', agmd.nominal, IFNULL(agmd.nominal*agmd.kurs,0)) as total_rp, IF(ck3.currency != 'IDR', agmd.nominal, 0) as total_valas, 'giro' as tipe2, iF(agm.transinfo !='', agm.transinfo, agm.lain2 ) as uraian, agmd.kode_coa");
        $this->db->from("acc_giro_masuk agm");
        $this->db->join("acc_giro_masuk_detail agmd ", "agm.id = agmd.giro_masuk_id", "left");
        $this->db->join("currency_kurs ck3 ", "agmd.currency_id = ck3.id", "left");
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


    var $column_order4 = array(null, null, 'no_bukti', 'tanggal', 'currency', 'kurs', 'total_rp', 'total_valas');
    var $column_search4 = array('no_bukti', 'tanggal', 'currency', 'kurs', 'total_rp', 'total_valas');
    var $order4         = array('tanggal' => 'asc');

    function query4($partner)
    {

        $where = ["arp.partner_id" => $partner, "arp.status" => 'confirm', "arp.lunas" => 0];
        if (count($where) > 0) {
            $this->db->where($where);
        }
        $this->db->select("arp.id, arp.no_retur as no_bukti, arp.create_date as tanggal, ck.currency, arp.kurs as currency_id, arp.kurs_nominal as kurs, (arp.total_piutang_rp ) as total_rp, (arp.total_piutang_valas) as total_valas, 'retur' as tipe2");
        $this->db->from("acc_retur_penjualan arp ");
        $this->db->join("currency_kurs ck ", "arp.kurs = ck.id", "left");
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


    function get_last_row_order_metode_by_id($id)
    {
        $this->db->order_by('id', 'desc');
        $this->db->where('pelunasan_piutang_id', $id);
        $this->db->select('max(row_order) as last');
        $this->db->from('acc_pelunasan_piutang_metode');
        $last_no = $this->db->get();

        $result = $last_no->row();
        if (empty($result->last)) {
            $no = 1;
        } else {
            $no = (int) $result->last + 1;
        }
        return $no;
    }


    function cek_metode_pelunasan_tipe_by_id($id)
    {
        $this->db->where('pelunasan_piutang_id', $id);
        $this->db->select('*');
        $this->db->from('acc_pelunasan_piutang_metode');
        $result = $this->db->get();
        return $result->row();
    }

     function get_data_metode_pelunasan_retur_by_id($partner, array $where = [])
    {
        $union_sql = $this->query4($partner);
        if (count($where) > 0) {
            $this->db->where($where);
        }
        $this->db->SELECT('*');
        $this->db->from('(' . $union_sql . ') as sub');
        $query = $this->db->get();
        return $query->row();
    }


    function get_data_metode_pelunasan_by_id($partner, $type, array $where = [])
    {
        $union_sql = $this->query3($partner, $type) . ' UNION ALL ' . $this->query3B($partner, $type) . ' UNION ALL ' . $this->query3C($partner, $type);

        if (count($where) > 0) {
            $this->db->where($where);
        }
        $this->db->SELECT('*');
        $this->db->from('(' . $union_sql . ') as sub');
        $query = $this->db->get();
        return $query->row();
    }

    function cek_metode_input_by_kode($where)
    {
        if (count($where) > 0) {
            $this->db->where($where);
        }
        $query = $this->db->get('acc_pelunasan_piutang_metode');
        return $query->num_rows();
    }


    function insert_data_pelunasan_piutang_metode($data_insert)
    {
        try {
            $this->db->insert_batch('acc_pelunasan_piutang_metode', $data_insert);
            $db_error = $this->db->error();
            if ($db_error['code'] > 0) {
                throw new Exception($db_error['message']);
            }
            return "";
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
    }


    function get_total_metode_pelunasan_by_no(array $where = [])
    {
        // $this->db->where('no_pelunasan', $no_pelunasan);
        if (count($where) > 0) {
            $this->db->where($where);
        }
        $this->db->select('no_pelunasan, IFNULL(sum(total_rp),0) as sum_rp, ifnull(sum(total_valas),0) as sum_valas');
        $this->db->from('acc_pelunasan_piutang_metode');
        $query = $this->db->get();
        return $query->row();
    }

    function update_pelunasan_faktur_by_kode($data_update, $kode = null)
    {
        try {
            $this->db->where('no_pelunasan', $kode);
            $this->db->update_batch('acc_pelunasan_piutang_faktur', $data_update, 'id');
            $db_error = $this->db->error();
            if ($db_error['code'] > 0) {
                throw new Exception($db_error['message']);
            }
            return "";
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
    }


    function get_total_pelunasan($where)
    {
        if (count($where) > 0) {
            $this->db->where($where);
        }
        $this->db->select("pelunasan_piutang_id, IFNULL(sum( IF(currency='IDR', total_rp, kurs*total_valas)),0) as total_pelunasan_rp, IFNULL(sum(total_valas),0) as total_pelunasan_valas ");
        $this->db->from('acc_pelunasan_piutang_metode');
        $query = $this->db->get()->row();
        return $query;
    }


    public function get_currency_by_pelunasan($where)
    {
        // Query pertama
        $this->db->select('no_pelunasan, currency_id, currency, kurs');
        $this->db->from('acc_pelunasan_piutang_faktur');
        if(count($where) > 0){
            $this->db->where($where);
        }
        $query1 = $this->db->get_compiled_select();

        // Query kedua
        $this->db->select('no_pelunasan, currency_id, currency, kurs');
        $this->db->from('acc_pelunasan_piutang_metode');
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


    function get_total_piutang($where)
    {

        if (count($where) > 0) {
            $this->db->where($where);
        }
        $this->db->select("IFNULL(sum(sisa_piutang_rp),0) as total_piutang_rp, IFNULL(sum(sisa_piutang_valas),0) as total_piutang_valas, IFNULL(sum(pelunasan_valas),0) as total_pelunasan_valas, IFNULL(sum(pelunasan_rp),0) as total_pelunasan_rp");
        $this->db->from('acc_pelunasan_piutang_faktur');
        $query = $this->db->get()->row();
        return $query;
    }

    function get_metode_by_code($where)
    {
        if (count($where) > 0) {
            $this->db->where($where);
        }
        $this->db->order_by('row_order', 'asc');
        $this->db->select("id, pelunasan_piutang_id, no_pelunasan, no_bukti, DATE(tanggal_bukti) as tanggal_bukti, currency_id, currency, kurs, total_rp, total_valas, row_order, tipe");
        $this->db->from('acc_pelunasan_piutang_metode');
        $query = $this->db->get();
        return $query->row();
    }


    function insert_data_pelunasan_piutang_summary($data)
    {
        try {
            $this->db->insert_batch('acc_pelunasan_piutang_summary', $data);
            $db_error = $this->db->error();
            if ($db_error['code'] > 0) {
                throw new Exception($db_error['message']);
            }
            return "";
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
    }

    
    function get_list_koreksi($tipe_currency = null, $where = null, $tipe = null)
    {
        $this->db->order_by('nama_koreksi', 'asc');
        if(($where)){
            $this->db->like('nama_koreksi', $where);
        }
        if($tipe_currency == 'Rp') { // Rp, VALAS
            $this->db->WHERE('show_idr', 'true'); // I
        }
        if($tipe_currency == 'Valas') { // Rp, VALAS
            $this->db->WHERE('show_valas', 'true'); // I
        }
        if($tipe){
            $this->db->WHERE('tipe', $tipe); // um / koreksi
        }
        $query = $this->db->get('acc_pelunasan_koreksi_piutang');
        return $query->result();
    }


    function get_data_summary_by_id($id)
    {
        $this->db->order_by('id', 'asc');
        $this->db->where('id', $id);
        $query = $this->db->get('acc_pelunasan_piutang_summary');
        return $query->row();
    }

    function get_coa_default_by_kode($where)
    {
        if(count($where) > 0){
            $this->db->where($where);
        }

        $this->db->select('b.kode_coa, c.nama as nama_coa');
        $this->db->from('acc_pelunasan_koreksi_piutang a');
        $this->db->join('acc_pelunasan_koreksi_piutang_coa b','a.id = b.pelunasan_koreksi_id','inner');
        $this->db->join('acc_coa c', 'b.kode_coa = c.kode_coa', 'left');
        $result = $this->db->get();
        return $result->row();
    }

    function get_coa_summary_id($where)
    {
        // $this->db->where('pelunasan_summary_id', $id);
        if(count($where) > 0){
            $this->db->where($where);
        }
        $this->db->select('appsk.posisi, appsk.kode_coa, appsk.nama_coa, apps.koreksi');
        $this->db->order_by('appsk.id');
        $this->db->from('acc_pelunasan_piutang_summary_koreksi appsk');
        $this->db->join('acc_pelunasan_piutang_summary apps', 'apps.id = appsk.pelunasan_summary_id', 'inner');
        $result = $this->db->get();
        return $result;
    }


    function update_data_pelunasan_piutang_summary($data)
    {
        try {
            $this->db->update_batch('acc_pelunasan_piutang_summary', $data, 'id');
            $db_error = $this->db->error();
            if ($db_error['code'] > 0) {
                throw new Exception($db_error['message']);
            }
            return "";
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
    }

    function insert_data_pelunasan_piutang_summary_koreksi($data_insert)
    {
        try {
            $this->db->insert_batch('acc_pelunasan_piutang_summary_koreksi', $data_insert);
            $db_error = $this->db->error();
            if ($db_error['code'] > 0) {
                throw new Exception($db_error['message']);
            }
            return "";
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
    }

    function get_koreksi_by_kode($where)
    {
        $this->db->order_by('nama_koreksi', 'asc');
        if(($where)){
            $this->db->where($where);
        }
        $query = $this->db->get('acc_pelunasan_koreksi_piutang');
        return $query->row();
    }


    function get_acc_pelunasan_piutang_faktur_id($id)
    {
        $this->db->where('id', $id);
        $query = $this->db->get('acc_pelunasan_piutang_faktur');
        return $query->row();
    }


    function cek_metode_pelunasan_group($where)
    {
        if(count($where) > 0){
            $this->db->where($where);
        }
        $this->db->group_by('tipe');
        $this->db->select('tipe');
        $this->db->from('acc_pelunasan_piutang_metode');
        $result = $this->db->get();
        return $result;
    }

    function get_data_faktur_by_code2($where)
    {
        $this->db->order_by('row_order', 'asc');
        if (count($where) > 0) {
            $this->db->where($where);
        }
        $this->query_get_faktur();
        $query = $this->db->get();
        return $query->result();
    }


    function update_faktur_by_kode($data_update)
    {
        try {
            $this->db->update_batch('acc_faktur_penjualan', $data_update, 'no_faktur');
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


    function cek_data_metode_valid_by_code($metode, $where)
    {   
        if(count($where) > 0) {
            $this->db->where($where);
        }
        if($metode == 'bank') {
            $this->db->select('acc_bank_masuk.no_bm, acc_bank_masuk_detail.id, acc_bank_masuk_detail.nominal');
            $this->db->FROM('acc_bank_masuk');
            $this->db->join('acc_bank_masuk_detail', 'acc_bank_masuk_detail.bank_masuk_id = acc_bank_masuk.id', 'inner');
            $result = $this->db->get();
        } else if($metode =='giro') {
            $this->db->select('acc_giro_masuk.no_gm, acc_giro_masuk_detail.id, acc_giro_masuk_detail.nominal');
            $this->db->FROM('acc_giro_masuk');
            $this->db->join('acc_giro_masuk_detail', 'acc_giro_masuk_detail.giro_masuk_id = acc_giro_masuk.id', 'inner');
            $result = $this->db->get();
        } else if($metode =='retur') {
            $this->db->select('id, no_retur, total_piutang_rp as total, lunas');
            $this->db->FROM('acc_retur_penjualan');
            $result = $this->db->get();
        } else {
            $this->db->select('acc_kas_masuk.no_km, acc_kas_masuk_detail.id, acc_kas_masuk_detail.nominal');
            $this->db->FROM('acc_kas_masuk');
            $this->db->join('acc_kas_masuk_detail', 'acc_kas_masuk_detail.kas_masuk_id = acc_kas_masuk.id', 'inner');
            $result = $this->db->get();
        }

        return $result->row();
    }
    

    function get_faktur_by_kode($where) {

        if(count($where) > 0){
            $this->db->where($where);
        }
        $this->query2();
        $result = $this->db->get();
        return $result;
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