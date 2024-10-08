<?php

defined('BASEPATH') or exit('No Direct Script Acces Allowed');
/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */

class M_Picklist extends CI_Model {

    var $column_order = array(null, 'no', 'p.nama', 'tanggal_input', 'jenis_jual', 'bulk_nama', 'sales_nama', null, 'status', 'nama_user');
    var $order = ['tanggal_input' => 'desc'];
    var $column_search = array('no', 'jenis_jual', 'msg.nama_sales_group', 'status', "tb.name", "p.nama");
    protected $table = "picklist";
    protected $level_sales_group;
    protected $select = 'picklist.id,no,tanggal_input,jenis_jual,tb.name as bulk_nama,msg.nama_sales_group as sales_nama,ms.nama_status as status,keterangan,nama_user,alamat_kirim,tot_qty,pcs_qty';
    protected $_menu = "";
    protected $user;

    public function __construct() {
        parent::__construct();
        $this->level_sales_group = $this->getLevelSales();
        $this->user = $this->getUser($this->session->userdata('username'));
    }

    protected function getDataQuery() {
        $this->db->select($this->select . ', p.nama');
        $this->db->from($this->table);
        $this->db->join('type_bulk as tb', 'tb.id = type_bulk_id', 'left');
        $this->db->join('mst_sales_group as msg', 'msg.kode_sales_group = sales_kode', 'left');
        $this->db->join('partner as p', 'p.id = customer_id', 'left');
        $this->db->join('mst_status as ms', 'ms.kode = status', 'left');
        $this->filteredSales();
        $this->getMenu();
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

        if (isset($_POST['order'])) {
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    protected function getMenu() {
        switch ($this->_menu) {
            case "count_detail":
//                $this->db->join("picklist_detail pdt", "(picklist.no = pdt.no_pl and pdt.valid <>'cancel')", "left");
//                $this->db->group_by("picklist.no");
//                $this->db->select(" sum(pdt.qty) as tot_qty,sum(pdt.qty2) as tot_qty2, count(pdt.qty) as pcs_qty,sum(pdt.qty2) as pcs_qty2");
                break;

            default:
                break;
        }
    }

    public function getTotalItem($array) {
        $this->db->select("count(id) as jumlah,sum(qty) as total_qty,sum(qty2) as total_qty2,sum(qty_hph) as total_qty_hph,sum(qty2_hph) as total_qty2_hph");
        $this->db->from("picklist_detail");
        $this->db->where($array);
        return $this->db->get()->row();
    }

    public function getCountDataFiltered(array $condition = [], array $menu = []) {

        if (count($condition) > 0)
            $this->db->where($condition);

        foreach ($menu as $value) {

            switch (strtolower($value)) {
                case 'do':
                    $this->notInDO();
                    break;
                case "realisasi":
                    $this->_menu = "realisasi";
                    break;
                case "validasi":
                    $this->_menu = "validasi";
                    break;
                case "delivery":
                    $this->_menu = "delivery";
                    break;
                case "count_detail":
                    $this->_menu = "count_detail";
                    break;
                default:
                    break;
            }
        }

        $this->getDataQuery();
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function getData(bool $realiasi = false, array $condition = [], array $menu = []) {
        try {
            if (count($condition) > 0)
                $this->db->where($condition);

            foreach ($menu as $value) {

                switch (strtolower($value)) {
                    case 'do':
                        $this->notInDO();
                        break;
                    case "realisasi":
                        $this->_menu = "realisasi";
                        break;
                    case "validasi":
                        $this->_menu = "validasi";
                        break;
                    case "delivery":
                        $this->_menu = "delivery";
                        break;
                    case "count_detail":
                        $this->_menu = "count_detail";
                        break;
                    default:
                        break;
                }
            }

            $this->getDataQuery();
            if ($realiasi)
                $this->joinDetail();
            if ($_POST['length'] != -1)
                $this->db->limit($_POST['length'], $_POST['start']);
            $query = $this->db->get();
            return $query->result();
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    public function getCountAllData(array $condition = [], array $menu = []) {
        $this->db->from($this->table);
        if (count($condition) > 0)
            $this->db->where($condition);

        foreach ($menu as $value) {

            switch (strtolower($value)) {
                case 'do':
                    $this->notInDO();
                    break;
                case "realisasi":
                    $this->_menu = "realisasi";
                    break;
                case "validasi":
                    $this->_menu = "validasi";
                    break;
                case "delivery":
                    $this->_menu = "delivery";
                    break;
                case "count_detail":
                    $this->_menu = "count_detail";
                    break;
                default:
                    break;
            }
        }

        $this->filteredSales();
        return $this->db->count_all_results();
    }

    protected function filteredSales() {
        if ($this->level_sales_group !== 'Administrator') {
            if (!in_array($this->_menu, ['realisasi', 'validasi', 'delivery'])) {
                $this->db->where('sales_kode', ($this->user->sales_group ?? ""));
            }
        }
    }

    protected function getUser($username) {
        $this->db->from("user");
        $this->db->where(["username" => $username]);
        $result = $this->db->select("*")->get();
        return $result->row();
    }

    public function getLevelSales(): string {
        $sales_group = $this->db->query('select nama_sales_group from mst_sales_group '
                        . 'join user on user.sales_group = mst_sales_group.kode_sales_group '
                        . 'where user.username = "' . $this->session->userdata('username') . '"')
                ->row_array();
        return $sales_group["nama_sales_group"] ?? "";
    }

    public function getDataPicklist(array $condition, string $select = "*", $join = "") {
        $this->db->from($this->table);
        switch ($join) {
            case "detail":
                $this->db->join("picklist_detail", "picklist.no = picklist_detail.no_pl");
                break;

            default:
                break;
        }
        $this->db->where($condition);
        return $this->db->select($select)->get()->row();
    }

    public function getDataByID($condition = [], $join = "", $menu = "") {
        $this->db->from($this->table);
        $select = $this->table . '.*, partner.id as ids,nama,delivery_street as alamat,invoice_street as alamat_invoice,tb.name as bulk, msg.nama_sales_group as sales';
//        $this->db->where($this->table . '.id', $id);
        switch ($join) {
            case "DO":
                $this->db->join('delivery_order do', '(do.no_picklist = ' . $this->table . '.no and do.status = "done")', 'left');
                $select .= ",do.no_sj,do.status as sj_status";
                break;

            default:
                break;
        }
        switch ($menu) {
            case "count_detail":
                $this->_menu = "count_detail";
                $this->getMenu();
                break;

            default:
                break;
        }
        $this->db->where($condition);
        if (!in_array($menu, ['realisasi', 'validasi', 'delivery'])) {

            $this->filteredSales();
        }
        $this->db->join('mst_sales_group as msg', 'msg.kode_sales_group = sales_kode', 'left');
        $this->db->join('partner', 'partner.id = customer_id', 'left');
        $this->db->join('type_bulk as tb', 'tb.id = type_bulk_id', 'left');
        return $this->db->select($select)->get()->row();
    }

    public function getDataReportPL($condition) {
        $this->db->from($this->table);
        $this->db->where($condition);
        $this->filteredSales();
        $this->db->join('mst_sales_group as msg', 'msg.kode_sales_group = sales_kode', 'left');
        $this->db->join('partner', 'partner.id = customer_id', 'left');
        $this->db->join('type_bulk as tb', 'tb.id = type_bulk_id', 'left');
        return $this->db->select($this->table . '.*, partner.id as ids,nama,delivery_street as alamat,tb.name as bulk,msg.nama_sales_group as sales')->get()->row();
    }

    public function getTypeBulk() {
        return $this->db->query("SELECT id,name FROM type_bulk ORDER BY name ")->result();
    }

    public function getSales() {
        return $this->db->query("SELECT kode_sales_group as kode, nama_sales_group as nama FROM mst_sales_group where `view` = '1' ")->result();
    }

    public function getCustomer($param) {
        return $this->db->query("select id,nama as text,delivery_street as alamat,invoice_street as alamat_invoice from partner where customer = 1 and "
                        . "nama LIKE '%" . $param . "%' or delivery_street LIKE '%" . $param . "%' group by id order by nama asc limit 10")->result();
    }

    public function checkExists(array $condition, array $join = []) {
        $this->db->from($this->table . ' a');
        $this->db->where($condition);
        $this->db->select("a.*");
        foreach ($join as $key => $value) {
            switch ($value) {
                case "DO":
                    $this->db->join("delivery_order do", "(do.no_picklist = a.no and do.status ='done' )", 'left');
                    $this->db->select(",do.no as doid");
                    break;
                case "BULK":
                    $this->db->join("bulk b", "b.no_pl = a.no", 'left');
                    $this->db->select(", no_bulk");
                    break;
            }
        }

        return $this->db->get()->row();
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

    public function draftSuratJalan(array $condition) {
        $this->db->from($this->table);
        $this->db->where($condition);
        $this->db->join('partner', 'partner.id = customer_id', 'left');
        $this->db->join('delivery_order do', '(do.no_picklist = picklist.no and do.status = "done")', 'left');
        return $this->db->select($this->table . '.*, partner.id as ids,nama,delivery_street as alamat,do.no_sj,alamat_kirim')->get()->row();
    }

    protected function joinDetail() {
        $this->db->select($this->select . ",GROUP_CONCAT(CONCAT(f.valid, ',', f.cnt) SEPARATOR '|' ) as st,total_item");
        $this->db->join('(select no_pl,valid,count(id) as cnt from picklist_detail where  valid <> "cancel" GROUP BY no_pl,valid ) f', 'f.no_pl = picklist.no');
        $this->db->join('(select no_pl,count(id) as total_item from picklist_detail where valid <> "cancel" GROUP BY no_pl ) e', 'e.no_pl = picklist.no', 'left');
        $this->db->group_by('picklist.id');
    }

    protected function withCountDetail() {
        $this->db->select($this->select . ", count(detail.id) as total_item");
        $this->db->join('picklist_detail detail', 'picklist.no = detail.no_pl', 'left');
    }

    protected function notInDO() {
        $this->db->where("no NOT IN (select no_picklist from delivery_order where status != 'cancel')", null, false);
    }

    public function getUserBC(array $condition) {
        $this->db->from("user");
        $this->db->select("telepon_wa");
        if (count($condition) > 0) {
            $loop = 0;
            foreach ($condition as $key => $value) {
                if ($loop === 0) {
                    $this->db->group_start();
                    $this->db->where($key, $value);
                } else {
                    $this->db->or_where($key, $value);
                }
                $loop++;
            }
            $this->db->group_end();
        }
        $query = $this->db->get();
        return $query->result();
    }
}
