<?php

defined('BASEPATH') or exit('No Direct Script Acces Allowed');
/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */

class M_bulk extends CI_Model {

    protected $table = "bulk";
    protected $column_order = array(null, 'no_bulk', 'tanggal_input', 'user');
    protected $order = ['tanggal_input' => 'desc'];
    protected $fillable = "";

    public function __construct() {
        parent::__construct();
        $this->column_order = array(null, $this->table . '.no_bulk', $this->table . '.tanggal_buat', $this->table . '.user');
        $this->column_search = array($this->table . '.no_bulk', $this->table . '.tanggal_input', $this->table . '.user');
        $this->order = [$this->table . '.tanggal_input' => 'desc'];
        $this->fillable = "$this->table.no_bulk, $this->table.tanggal_input, $this->table.user";
    }

    protected function _getDataQuery() {
        $this->db->from($this->table);
        if (isset($_POST["search"])) {
            foreach ($this->column_search as $key => $value) {

                if ($_POST['search']['value']) {
                    if ($key === 0) {
                        $this->db->group_start();
                        $this->db->like($value, $_POST['search']['value']);
                    } else {
                        $this->db->or_like($value, $_POST['search']['value']);
                    }

                    if (count($this->column_search) - 1 === $key)
                        $this->db->group_end();
                }
            }
        }
        if (isset($_POST['order'])) { // here order processing
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    public function getData(array $condition = []) {
        $this->_getDataQuery();
        if (count($condition) > 0) {
            $this->db->where($condition);
        }
        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }

    public function getCountDataFiltered(array $condition = []) {
        $this->_getDataQuery();
        if (count($condition) > 0) {
            $this->db->where($condition);
        }
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function getCountAllData(array $condition = []) {
        $this->db->from($this->table);
        if (count($condition) > 0) {
            $this->db->where($condition);
        }
        return $this->db->count_all_results();
    }

    public function getDataDetail(array $condition) {
        $this->db->from($this->table);
        $this->db->where($condition);
        return $this->db->select("*")->get()->row();
    }

    public function getDatas(array $condition) {
        $this->db->from($this->table);
        $this->db->where($condition);
        $query = $this->db->get();
        return $query->result();
    }

    protected function _listBulkDetail(array $condition) {
        $subquery = $this->_listBulkDetailSub($condition);
        $this->_getDataQuery();
        $this->db->join("($subquery) pl", " bulk.no_bulk = pl.bulk_no_bulk", "left");
        $this->db->select($this->table . '.*');
        $this->db->where($condition);
        $this->db->group_by('no_bulk');
    }

    protected function _listBulkDetailSub($condition): string {
        $this->db->from("bulk_detail cbd");
        $this->db->select("cbd.bulk_no_bulk,cbd.barcode,cbd.tanggal_input,sum(pl.qty) as total_qty,count(pl.qty) as jumlah_qty,valid");
        $this->db->join("picklist_detail pl", "(pl.barcode_id = cbd.barcode and valid != 'cancel')", "left");
        $this->db->where($condition);
        $this->db->group_by('bulk_no_bulk');
        $subquery = $this->db->get_compiled_select();
        return $subquery;
    }

    public function listBulkDetail(array $condition) {
        $this->_listBulkDetail($condition);
//        $this->db->join('picklist_detail pl', 'pl.barcode_id = bd.barcode', 'left');
        $q = $this->db->select('bulk.no_bulk,bulk.tanggal_input,total_qty,jumlah_qty')->get();
        return $q->result();
    }

    public function getCountAllDataBulk($condition) {
        $this->_listBulkDetail($condition);
//        $this->db->join('picklist_detail pl', 'pl.barcode_id = bd.barcode', 'left');
        return $this->db->count_all_results();
    }

    public function getCountDataFilteredBulk(array $condition = []) {
        $this->_listBulkDetail($condition);
//        $this->db->join('picklist_detail pl', 'pl.barcode_id = bd.barcode', 'left');
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function updateNetGross(array $condition, array $data) {
        $this->db->set($data);
        $this->db->where($condition);
        $this->db->update($this->table);
    }

    public function save(array $data) {
        try {
            $this->db->insert($this->table, $data);
            $db_error = $this->db->error();
            if ($db_error['code'] > 0) {
                log_message('error', json_encode($data));
                throw new Exception($db_error['message']);
            }
            return "";
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
    }

    protected $selectPicklist = 'picklist.id,no,tanggal_input,jenis_jual,tb.name as bulk_nama,msg.nama_sales_group as sales_nama,ms.nama_status as status,keterangan,nama_user';
    protected $column_orderPicklist = array(null, 'no', 'p.nama', 'tanggal_input', 'jenis_jual', 'bulk_nama', null, 'sales_nama', 'status', 'nama_user');
    protected $orderPicklist = ['tanggal_input' => 'desc'];
    protected $column_searchPicklist = array('no', 'jenis_jual', 'msg.nama_sales_group');

    protected function _bulkPicklist() {
        $this->db->select($this->selectPicklist . ', p.nama');
        $this->db->from('picklist');
        $this->db->join('type_bulk as tb', 'tb.id = type_bulk_id', 'left');
        $this->db->join('mst_sales_group as msg', 'msg.kode_sales_group = sales_kode', 'left');
        $this->db->join('partner as p', 'p.id = customer_id', 'left');
        $this->db->join('mst_status as ms', 'ms.kode = status', 'left');
        foreach ($this->column_searchPicklist as $key => $value) {
            if ($_POST['search']['value']) {
                if ($key === 0) {
                    $this->db->group_start();
                    $this->db->like($value, $_POST['search']['value']);
                } else {
                    $this->db->or_like($value, $_POST['search']['value']);
                }

                if (count($this->column_searchPicklist) - 1 === $key)
                    $this->db->group_end();
            }
        }

        if (isset($_POST['order'])) {
            $this->db->order_by($this->column_orderPicklist[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->orderPicklist)) {
            $order = $this->orderPicklist;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    public function getCountDataFilteredPicklist(array $condition = []) {

        if (count($condition) > 0)
            $this->db->where($condition);

        $this->_bulkPicklist();
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function getDataPicklist(array $condition = []) {
        try {
            if (count($condition) > 0)
                $this->db->where($condition);

            $this->_bulkPicklist();
            if ($_POST['length'] != -1)
                $this->db->limit($_POST['length'], $_POST['start']);
            $query = $this->db->get();
            return $query->result();
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    public function getCountAllDataPicklist(array $condition = []) {
        $this->db->from('picklist');
        if (count($condition) > 0)
            $this->db->where($condition);
        return $this->db->count_all_results();
    }
    
    public function getDataByIDPicklist($condition = [], $join = "") {
        $this->db->from('picklist');
        $select = 'picklist.*, partner.id as ids,nama,delivery_street as alamat,tb.name as bulk, msg.nama_sales_group as sales';
//        $this->db->where($this->table . '.id', $id);
        switch ($join) {
            case "DO":
                $this->db->join('delivery_order do', 'do.no_picklist = ' . $this->table . '.no', 'left');
                $select .= ",do.no_sj";
                break;

            default:
                break;
        }
        $this->db->where($condition);
        $this->db->join('mst_sales_group as msg', 'msg.kode_sales_group = sales_kode', 'left');
        $this->db->join('partner', 'partner.id = customer_id', 'left');
        $this->db->join('type_bulk as tb', 'tb.id = type_bulk_id', 'left');
        return $this->db->select($select)->get()->row();
    }
}
