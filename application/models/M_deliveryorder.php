<?php

defined('BASEPATH') or exit('No Direct Script Acces Allowed');
/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of M_Deliveryorder
 *
 * @author RONI
 */
class M_deliveryorder extends CI_Model {

    protected $table = "delivery_order";
    protected $tableSJ = "delivery_order_sj";
    protected $column_order = [null, 'delivery_order.no', 'no_sj', 'no_picklist', 'tb.name', 'tanggal_dokumen', null, null, 'delivery_order.status'];
    protected $order = ['tanggal_buat' => 'desc'];
    protected $column_search = array('no_sj', 'delivery_order.no', 'no_picklist', 'pr.nama', "tanggal_buat", "tanggal_dokumen", "delivery_order.status", "tb.name", "msg.nama_sales_group");
    protected $retur_batal = "0";

    public function __construct() {
        parent::__construct();
    }

    protected function getDataQuery() {
        $this->db->from($this->table);
        $this->db->join("picklist p", 'p.no = delivery_order.no_picklist');
        $this->db->join('mst_sales_group as msg', 'msg.kode_sales_group = p.sales_kode', 'left');
        $this->db->join("type_bulk tb", "tb.id = p.type_bulk_id", "left");
        $this->db->join('partner pr', 'pr.id = p.customer_id', 'left');
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

    public function getData($conditon = []) {
        $this->getDataQuery();
        $this->db->select("delivery_order.*,pr.nama as buyer,tb.name as bulk,msg.nama_sales_group as sales_nama");
        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        if (count($conditon) > 0)
            $this->db->where($conditon);

        $query = $this->db->get();
        return $query->result();
    }

    public function getDataCountFiltered($condition = []) {
        $this->getDataQuery();
        if (count($condition) > 0)
            $this->db->where($condition);
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function getDataCountAll(array $condition = []) {
        $this->db->from($this->table);
        if (count($condition) > 0)
            $this->db->where($condition);
        return $this->db->count_all_results();
    }

    public function checkNoSJ(array $condition) {
        $this->db->from($this->tableSJ);
        $this->db->where($condition);
        $this->db->limit(1);
        return $this->db->select("*")->get()->row();
    }

    public function deleteNoSJ($condition) {
        $this->db->delete($this->tableSJ, $condition);
    }

    public function insertNoSJ(array $value) {
        $this->db->insert($this->tableSJ, $value);
        return $this->db->insert_id() ?? null;
    }

    public function insert(array $value) {
        $this->db->insert($this->table, $value);
        return $this->db->insert_id() ?? null;
    }

    public function update(array $data, array $condition) {
        $this->db->set($data);
        $this->db->where($condition);
        $this->db->update($this->table);
    }

    public function getDataDetail(array $condition, $join = false, $select = "a.*") {
        $this->db->from($this->table . " a");
        if ($join) {
            $this->db->join("picklist p", "p.no = a.no_picklist");
            $this->db->join('partner as pn', 'pn.id = p.customer_id', 'left');
            $this->db->join('mst_sales_group as msg', 'msg.kode_sales_group = p.sales_kode', 'left');
        }
        $this->db->where($condition);
        $result = $this->db->select($select)->get();
        return $result->row();
    }

    public function insertDoMove(array $value) {
        $this->db->insert('deliveryorder_stock_move', $value);
        return $this->db->insert_id() ?? null;
    }

    protected function _getDataReport() {
        $ip  = $this->input->ip_address();
        $this->db->from($this->table . ' ddo');
        $this->db->join("delivery_order_detail dod", 'dod.do_id = ddo.id');
        if ($this->retur_batal === "0") {
            $this->db->join("picklist_detail pd", "(pd.barcode_id = dod.barcode_id and pd.no_pl = ddo.no_picklist)");
        } else {
            $this->db->join("(SELECT * from picklist_detail GROUP BY barcode_id,no_pl) pd", "(pd.barcode_id = dod.barcode_id and pd.no_pl = ddo.no_picklist)");
        }

//        $this->db->join("stock_quant sq", "sq.quant_id = pd.quant_id", "left");
        $this->db->join("picklist p", 'p.no = ddo.no_picklist');
        $this->db->join('partner pr', 'pr.id = p.customer_id', 'left');
        $this->db->join('mst_sales_group as msg', 'msg.kode_sales_group = p.sales_kode', 'left');
//        $this->db->select("ddo.`no`,ddo.no_sj,ddo.tanggal_buat,ddo.tanggal_dokumen,p.jenis_jual,ddo.no_picklist,pr.nama,concat(pr.delivery_street,' , ',pr.delivery_city) as alamat,alamat_kirim,"
//                . "pd.corak_remark,pd.warna_remark,sq.uom,sq.uom2,sq.uom_jual,sq.uom2_jual,sq.lebar_jadi,sq.uom_lebar_jadi,"
//                . "SUM(sq.qty) as total_qty,SUM(sq.qty2) as total_qty2,SUM(sq.qty_jual) as total_qty_jual,SUM(sq.qty2_jual) as total_qty2_jual,msg.nama_sales_group as marketing,ddo.user,ddo.note");

        $this->db->select("'{$ip}' as ip,ddo.`no`,ddo.no_sj,ddo.tanggal_buat,ddo.tanggal_dokumen,p.jenis_jual,ddo.no_picklist,pr.nama,concat(pr.delivery_street,' , ',pr.delivery_city) as alamat,alamat_kirim,"
                . "pd.corak_remark,pd.warna_remark,pd.uom_hph as uom,pd.uom2_hph as uom2,pd.uom as uom_jual,pd.uom2 as uom2_jual,pd.lebar_jadi,pd.uom_lebar_jadi,pd.nama_produk,dod.status as dod_status,"
                . "msg.nama_sales_group as marketing,ddo.user,ddo.note,,ddo.tanggal_batal,dod.tanggal_retur");
    }

    public function getDataReport(array $condition, $order = "", $rekap = "global", $returbatal = "0", $raw = false) {
        $this->retur_batal = $returbatal;
        $this->_getDataReport();
        if ($rekap === 'global') {
            $this->db->select(",SUM(pd.qty_hph) as total_qty,SUM(pd.qty2_hph) as total_qty2,SUM(pd.qty) as total_qty_jual,SUM(pd.qty2) as total_qty2_jual,COUNT(dod.barcode_id) as total_lot");
            $this->db->group_by("ddo.no");
            if ($returbatal === "1") {
                $this->db->group_by("dod.status");
            }
        } else if ($rekap === 'detail') {
            $this->db->select(",SUM(pd.qty_hph) as total_qty,SUM(pd.qty2_hph) as total_qty2,SUM(pd.qty) as total_qty_jual,SUM(pd.qty2) as total_qty2_jual,COUNT(dod.barcode_id) as total_lot");
            $this->db->group_by("ddo.no,pd.corak_remark,pd.warna_remark,pd.lebar_jadi,uom");
            if ($returbatal === "1") {
                $this->db->group_by("dod.status");
            }
        } else {
            $this->db->select(",pd.qty_hph as total_qty,pd.qty2_hph as total_qty2,pd.qty as total_qty_jual,pd.qty2 as total_qty2_jual,dod.barcode_id as total_lot");
//            $this->db->group_by("dod.barcode_id");
        }
        if (count($condition) > 0) {
            $this->db->where($condition);
        }

        switch ($order) {
            case"nama":
                $this->db->order_by("nama asc, no_sj asc");
                break;
            case "jenis_jual":
                $this->db->order_by("jenis_jual asc, no_sj asc");
                break;
            default:
                $this->db->order_by("no_sj asc");
                break;
        }
        if (isset($_POST['length'])) {
            if ($_POST['length'] != -1)
                $this->db->limit($_POST['length'], $_POST['start']);
        }
        if (!$raw) {

            $query = $this->db->get();
            return $query->result();
        }
        return $this->db->get_compiled_select();
    }

    public function getDataReportTotal(array $condition, $order = "", $rekap = "global", $returbatal = "0", $raw = false) {
        $this->retur_batal = $returbatal;
        $this->_getDataReport();
        if ($rekap === 'global') {
            $this->db->select(",COUNT(dod.barcode_id) as total_lot");
            $this->db->group_by("ddo.no");
            if ($returbatal === "1") {
                $this->db->group_by("dod.status");
            }
        } else if ($rekap === 'detail') {
            $this->db->select(",COUNT(dod.barcode_id) as total_lot");
            $this->db->group_by("ddo.no,pd.corak_remark,pd.warna_remark,pd.lebar_jadi,uom");
            if ($returbatal === "1") {
                $this->db->group_by("dod.status");
            }
        } else {
            $this->db->select(",dod.barcode_id as total_lot");
//            $this->db->group_by("dod.barcode_id");
        }

        if (count($condition) > 0)
            $this->db->where($condition);

        if (!$raw) {
            $query = $this->db->get();
            return $query->num_rows();
        }
        return $this->db->get_compiled_select();
    }

    public function getDataReportTotalAll(array $condition, $order = "", $rekap = "global", $returbatal = "0") {
        $this->retur_batal = $returbatal;
        $this->_getDataReport();
        if ($rekap === 'global') {
            $this->db->select(",COUNT(dod.barcode_id) as total_lot");
            $this->db->group_by("ddo.no");
            if ($returbatal === "1") {
                $this->db->group_by("dod.status");
            }
        } else if ($rekap === 'detail') {
            $this->db->select(",COUNT(dod.barcode_id) as total_lot");
            $this->db->group_by("ddo.no,pd.corak_remark,pd.warna_remark,pd.lebar_jadi,uom");
            if ($returbatal === "1") {
                $this->db->group_by("dod.status");
            }
        } else {
            $this->db->select(",dod.barcode_id as total_lot");
//            $this->db->group_by("dod.barcode_id");
        }

        if (count($condition) > 0)
            $this->db->where($condition);

        return $this->db->count_all_results();
    }

    public function getDataReportUnion(array $query, $order = "") {
        $this->db->SELECT('*');
        $this->db->FROM('((' . implode(" ) UNION ALL ( ", $query) . ') ) as unionTable');

        switch ($order) {
            case"nama":
                $this->db->order_by("nama asc, no_sj asc");
                break;
            case "jenis_jual":
                $this->db->order_by("jenis_jual asc, no_sj asc");
                break;
            default:
                $this->db->order_by("no_sj asc");
                break;
        }
        $querys = $this->db->get();
        return $querys->result();
    }

    public function getDataReportTotalUnion(array $query) {
        $this->db->FROM('((' . implode(" ) UNION ALL ( ", $query) . ') ) as unionTable');
        $querys = $this->db->get();
        return $querys->num_rows();
    }

    public function userBC(array $condition) {
        $this->db->select("u.telepon_wa");
        $this->db->from("user as u");
        $loop = 0;
        if (count($condition) > 0) {
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

    public function getTotalBarcode(array $condition) {
        $this->db->from("picklist_detail pd");
        $this->db->select("count(pd.barcode_id) as total,pd.valid");
        $this->db->where($condition);
        $this->db->group_by("pd.valid");
        $query = $this->db->get();
        return $query->result();
    }

    public function listNoSJ(array $condition) {
        $this->db->from("delivery_order_sj");
        $this->db->where($condition);
        $query = $this->db->get();
        return $query->result();
    }
}
