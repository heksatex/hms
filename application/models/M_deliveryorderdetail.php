<?php

defined('BASEPATH') or exit('No Direct Script Acces Allowed');
/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of M_deliveryorderdetail
 *
 * @author RONI
 */
class M_deliveryorderdetail extends CI_Model {

    //put your code here
    protected $table = "delivery_order_detail";
    protected $column_search = ["a.barcode_id"];
    protected $column_order = [null];
    protected $order = ["a.id"];
    protected $join = [];

    public function insert($data) {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id() ?? null;
    }

    public function getListDataItem(array $condition) {
        $this->db->from('picklist_detail pl');
        $this->db->where($condition);
        $this->db->group_by('pl.warna_remark, pl.corak_remark,pl.uom');
        $this->db->order_by("pl.corak_remark asc");
        $query = $this->db->select('pl.corak_remark,pl.warna_remark,sum(qty) as total_qty,count(qty) as jumlah_qty,uom')->get();
        return $query->result();
    }

    protected function _getData() {
        $this->column_search = array_merge($this->column_search, ["pd.warna_remark", "pd.corak_remark"]);
        $this->db->from($this->table . ' a');
        $this->db->join("delivery_order do", 'do.id = a.do_id');
        $this->db->join("picklist_detail pd", "(pd.barcode_id = a.barcode_id and pd.valid <> 'cancel')");
//        $this->db->join("(select * from picklist_detail group by barcode_id) pd", "pd.barcode_id = a.barcode_id");
        foreach ($this->join as $value) {
            switch ($value) {
                case 'BULK':
                    $this->column_search = array_merge($this->column_search, ["bulk_no_bulk"]);
                    $this->db->select("bulk_no_bulk");
                    $this->column_order = [null, "bulk_no_bulk", "corak_remark", "warna_remark", "total_qty", "jumlah_qty", "uom"];
                    $this->db->join('bulk_detail bd', '(bd.barcode = pd.barcode_id and pd.valid <> "cancel")', 'left');
                    $this->db->group_by('bulk_no_bulk');
//                    $this->db->order_by("bulk_no_bulk");
                    break;

                default:
                    break;
            }
        }
        $this->db->group_by('pd.warna_remark, pd.corak_remark,pd.uom');
        $this->db->select('pd.corak_remark,pd.warna_remark,sum(pd.qty) as total_qty,count(pd.qty) as jumlah_qty,pd.uom');
//        $this->db->join('stock_quant as sq', 'sq.lot = pd.barcode_id');
        foreach ($this->column_search as $key => $value) {
            if ($_POST['search']['value']) {
                if ($key === 0) {
                    $this->db->group_start();
                    $this->db->like($value, $_POST['search']['value']);
                } else {
                    $this->db->or_like($value, $_POST['search']['value']);
                }

                if (count($this->column_search) === ($key + 1)) {
                    $this->db->group_end();
                }
            }
        }
        if (isset($_POST['order'])) {
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    protected function _getDataDetail() {
        $this->column_search = ["pd.barcode_id", "pd.nama_produk", "pd.warna_remark", "pd.corak_remark", "pd.no_pl", "pd.kode_produk"];
        $this->column_order = [null, "pd.barcode_id", "pd.nama_produk", "pd.warna_remark", "pd.corak_remark", "pd.no_pl", "pd.kode_produk", null];
        $this->order = ["nodo" => "DESC"];
        $this->db->from($this->table . ' dod');
        $this->db->join("picklist_detail pd", "pd.barcode_id = dod.barcode_id");
        $this->db->select("pd.*,dod.do_id as nodo");
        $this->db->group_by("dod.barcode_id");
        foreach ($this->column_search as $key => $value) {
            if ($_POST['search']['value']) {
                if ($key === 0) {
                    $this->db->group_start();
                    $this->db->like($value, $_POST['search']['value']);
                } else {
                    $this->db->or_like($value, $_POST['search']['value']);
                }

                if (count($this->column_search) === ($key + 1)) {
                    $this->db->group_end();
                }
            }
        }
        if (isset($_POST['order'])) {
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    public function getDataDetail(array $condition, array $join = []) {
        $this->_getDataDetail();
        $this->db->where($condition);
        foreach ($join as $value) {
            switch ($value) {
                case "BULK":
                    $this->db->join('bulk_detail bd', 'pd.barcode_id = bd.barcode', 'left');
                    $this->db->select("bd.bulk_no_bulk");
                    $this->db->order_by("bd.bulk_no_bulk", "ASC");
                    break;

                default:
                    break;
            }
        }
        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }

    public function getDataDetailCountFiltered($condition = [], array $join = []) {
        $this->_getDataDetail();
        foreach ($join as $value) {
            switch ($value) {
                case "BULK":
                    $this->db->join('bulk_detail bd', 'pd.barcode_id = bd.barcode', 'left');
                    $this->db->select("bd.bulk_no_bulk");

                    break;

                default:
                    break;
            }
        }
        $this->db->where($condition);
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function getDataDetailCountAll(array $condition = [], array $join = []) {
        $this->db->from($this->table . ' dod');
        $this->db->join("picklist_detail pd", "pd.barcode_id = dod.barcode_id");
        $this->db->group_by("dod.barcode_id");
        foreach ($join as $value) {
            switch ($value) {
                case "BULK":
                    $this->db->join('bulk_detail bd', 'pd.barcode_id = bd.barcode', 'left');
                    break;

                default:
                    break;
            }
        }
        $this->db->select("dod.id");
        $this->db->where($condition);
        return $this->db->count_all_results();
    }

    public function getData($condition = [], $join = []) {
        $this->join = $join;
        $this->_getData();
        if (count($condition) > 0) {
            $this->db->where($condition);
        }
        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }

    public function getDataCountFiltered($condition = [], $join = []) {
        $this->join = $join;
        $this->_getData();
        if (count($condition) > 0)
            $this->db->where($condition);
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function getDataCountAll(array $condition = [], $join = []) {
        $this->join = $join;
        $this->_getData();
        $this->db->select("a.id");
        if (count($condition) > 0)
            $this->db->where($condition);
        return $this->db->count_all_results();
    }

    public function getDataWGroup($condition = [], $type_bulk = 1, $type = "") {
        $select = "pd.corak_remark,pd.warna_remark,sum(pd.qty) as total_qty,count(pd.qty) as jumlah_qty,pd.uom,pd.lebar_jadi,pd.uom_lebar_jadi";
        $group = "pd.warna_remark, pd.corak_remark,pd.uom,pd.lebar_jadi,pd.uom_lebar_jadi";
        $this->db->from($this->table . ' dod');
        $this->db->join('delivery_order do', 'do.id = dod.do_id');
        $this->db->where($condition);
        if ($type_bulk === 1) {
            if ($type !== "sje") {
                $group .= ",bulk_no_bulk";
            }
            $select .= ",bulk_no_bulk";
//            $this->db->join("bulk_detail bd", "bd.picklist_detail_id = dod.picklist_detail_id");
//            $this->db->join("picklist_detail pd", "pd.id = bd.picklist_detail_id");
            $this->db->join("(select pd.*,bulk_no_bulk from picklist_detail pd join bulk_detail bd on bd.barcode = pd.barcode_id) as pd","pd.barcode_id = dod.barcode_id");
            $this->db->order_by("bulk_no_bulk", "asc");
        } else {
            $this->db->join("picklist_detail pd", "pd.barcode_id = dod.barcode_id");
        }
        $this->db->group_by($group);
        $this->db->select($select);
        $query = $this->db->get();
        return $query->result();
//        $this->db->join("picklist_detail pd", 'pd.barcode_id = dod.barcode_id');
//        $this->db->join('stock_quant as sq', 'sq.lot = dod.barcode_id');
    }

    public function getCountBulk($condition = []) {
        $this->db->from($this->table . ' dod');
        $this->db->join('delivery_order do', 'do.id = dod.do_id');
        $this->db->where($condition);
        $this->db->join("bulk_detail bd", "bd.barcode = dod.barcode_id");
        $this->db->join("picklist_detail pd", "pd.barcode_id = bd.barcode");
        $this->db->group_by('bd.bulk_no_bulk');
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function detailReportQty($condition, $joinbulk = false) {
        $this->db->from("picklist_detail pd");
        $this->db->where($condition);
        if ($joinbulk) {
            $this->db->join("bulk_detail bd", "bd.barcode = pd.barcode_id");
        }
        $this->db->select('qty,uom');
        return $this->db->get()->result();
    }

    public function insertBatch(array $data) {
        $this->db->insert_batch($this->table, $data);
    }

    public function getDataAll($condition, array $join = [], array $whereIn = []) {
        $this->db->from($this->table . ' dod');
        $this->db->join("delivery_order do", 'do.id = dod.do_id');
        $this->db->where($condition);
        foreach ($whereIn as $key => $value) {
            $this->db->where_in($key, $value);
        }
        foreach ($join as $value) {
            switch ($value) {
                case "PD":
//                    $this->db->join('pickist_detail pd','pd.no_pl = do.no_picklist');
                    $this->db->join("picklist_detail pd", "(pd.barcode_id = dod.barcode_id) and (pd.no_pl = do.no_picklist)");
                    $this->db->join("stock_quant sq", "sq.quant_id = pd.quant_id");
//                    $this->db->select("pd.quant_id,pd.kode_produk,no_pl,pd.nama_produk,pd.warna_remark,pd.corak_remark,pd.sales_order,sq.lebar_jadi,sq.uom_lebar_jadi,"
//                            . "sq.qty_jual,sq.uom_jual,sq.qty2_jual,sq.uom2_jual,sq.qty,sq.qty2,sq.uom,sq.uom2");
                    $this->db->select("pd.quant_id,pd.kode_produk,no_pl,pd.nama_produk,pd.warna_remark,pd.corak_remark,pd.sales_order,pd.lebar_jadi,pd.uom_lebar_jadi,"
                            . "pd.qty as qty_jual,pd.uom as uom_jual,pd.qty2 as qty2_jual,pd.uom2 as uom2_jual,pd.qty_hph as qty,pd.qty2_hph as qty2,pd.uom_hph as uom,pd.uom2_hph as uom2");
                    break;
                case "SQ":
                    $this->db->select("sq.lokasi_fisik,sq.lebar_greige,sq.uom_lebar_greige");
                    break;
                default:
                    break;
            }
        }
        $this->db->select('dod.*');
        $query = $this->db->get();
        return $query->result();
    }

    public function update(array $data, array $condition = [], array $whereIn = []) {
        $success = false;
        if ((count($whereIn) + count($condition)) < 1) {
            return $success;
        }
        if (count($condition) > 0) {
            $this->db->where($condition);
        }
        foreach ($whereIn as $key => $value) {
            $this->db->where_in($key, $value);
        }
        $this->db->set($data);
        $this->db->update($this->table);
        return true;
    }

    public function getDetail(array $condition) {
        $this->db->from($this->table . ' dod');
        $this->db->where($condition);
        $this->db->join("delivery_order do", "do.id = dod.do_id");
        $this->db->join("picklist_detail pd", "pd.barcode_id = dod.barcode_id");
        return $this->db->select("dod.*,pd.*")->get()->row();
    }

    public function countData(array $condition, $whereIn = []) {
        $this->db->from($this->table . ' dod');
        $this->db->where($condition);
        foreach ($whereIn as $key => $value) {
            $this->db->where_in($key, $value);
        }
        return $this->db->count_all_results();
    }

    public function countDetail(array $condition) {
        $this->db->from($this->table . ' dod');
        $this->db->join("delivery_order do", "do.id = dod.do_id");
        $this->db->join('stock_quant sq', '(sq.lot = dod.barcode_id)');
        $this->db->where($condition);
//        $this->db->group_by("pd.barcode_id");
        $query = $this->db->select('count(sq.lot) as total_item, sum(sq.qty_jual) as total_qty, count(sq.qty_jual) as jumlah_qty')->get();
        return $query->row();
    }
}
