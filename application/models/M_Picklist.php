<?php

defined('BASEPATH') or exit('No Direct Script Acces Allowed');
/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */

class M_Picklist extends CI_Model {

    var $column_order = array(null, 'no', 'tanggal_input', 'jenis_jual', 'bulk_nama', null, 'sales_nama', 'status', 'nama_user');
    var $order = ['tanggal_input' => 'desc'];
    var $column_search = array('jenis_jual', 'bulk_nama', 'sales_nama');
    protected $table = "picklist";
    protected $level_sales_group;

    public function __construct() {
        parent::__construct();
        $this->level_sales_group = $this->getLevelSales();
    }

    protected function getDataQuery() {
        $this->db->select('picklist.id,no,tanggal_input,jenis_jual,tb.name as bulk_nama,msg.nama_sales_group as sales_nama,ms.nama_status as status,keterangan,nama_user');
        $this->db->from($this->table);
        $this->db->join('type_bulk as tb', 'tb.id = type_bulk_id', 'left');
        $this->db->join('mst_sales_group as msg', 'msg.kode_sales_group = sales_kode', 'left');
        $this->db->join('partner as p', 'p.id = customer_id', 'left');
        $this->db->join('mst_status as ms', 'ms.kode = status', 'left');
        $this->filteredSales();
        foreach ($this->column_search as $key => $value) {
            if ($_POST["search"]["value"]) {
                $this->db->or_like($value, $_POST["search"]["value"]);
            }
        }

        if (isset($_POST['order'])) {
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    public function getCountDataFiltered() {
        $this->getDataQuery();
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function getData() {
        try {
            $this->getDataQuery();
            if ($_POST['length'] != -1)
                $this->db->limit($_POST['length'], $_POST['start']);
            $query = $this->db->get();
            return $query->result();
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    public function getCountAllData() {
        $this->db->from($this->table);
        $this->filteredSales();
        return $this->db->count_all_results();
    }

    protected function filteredSales() {
        if ($this->level_sales_group !== 'Administrator') {
            $this->db->where('sales_kode', $this->session->userdata('nama')['sales_group']);
        }
    }

    protected function getLevelSales(): string {
        $sales_group = $this->db->query('select nama_sales_group from mst_sales_group '
                        . 'join user on user.sales_group = mst_sales_group.kode_sales_group '
                        . 'where user.username = "' . $this->session->userdata('username') . '"')
                ->row_array();
        return $sales_group["nama_sales_group"] ?? "";
    }

    public function getDataByID($id) {
        $this->db->from($this->table);
        $this->db->where($this->table . '.id', $id);
        $this->filteredSales();
        $this->db->join('partner', 'partner.id = customer_id', 'left');
        $this->db->join('type_bulk as tb', 'tb.id = type_bulk_id', 'left');
        return $this->db->select($this->table . '.*, partner.id as ids,nama,delivery_street as alamat,tb.name as bulk')->get()->row();
    }

    public function getDataReportPL($condition) {
        $this->db->from($this->table);
        $this->db->where($condition);
        $this->filteredSales();
        $this->db->join('partner', 'partner.id = customer_id', 'left');
        $this->db->join('type_bulk as tb', 'tb.id = type_bulk_id', 'left');
        return $this->db->select($this->table . '.*, partner.id as ids,nama,delivery_street as alamat,tb.name as bulk')->get()->row();
    }

    public function getTypeBulk() {
        return $this->db->query("SELECT id,name FROM type_bulk ORDER BY name ")->result();
    }

    public function getSales() {
        return $this->db->query("SELECT kode_sales_group as kode, nama_sales_group as nama FROM mst_sales_group where `view` = '1' ")->result();
    }

    public function getCustomer($param) {
        return $this->db->query("select id,nama as text,delivery_street as alamat from partner where customer = 1 and "
                        . "nama LIKE '%" . $param . "%' or delivery_street LIKE '%" . $param . "%' group by id order by nama asc limit 10")->result();
    }

    public function save(array $data) {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id() ?? null;
    }

    public function update(array $data, array $condition) {
        $this->db->set($data);
        $this->db->where($condition);
        $this->db->update($this->table);
    }
}
