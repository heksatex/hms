<?php

defined('BASEPATH') or exit('No Direct Script Acces Allowed');
/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of M_PicklistDetail
 *
 * @author RONI
 */
class M_PicklistDetail extends CI_Model {

    protected $db_debug;

    public function __construct() {
        $this->db_debug = $this->db->db_debug;
        $this->db->db_debug = FALSE;
    }

    //put your code here
    protected $table = "picklist_detail";
    var $column_order = array(null, 'a.barcode_id', 'a.corak_remark', 'a.warna_remark', 'a.qty', 'a.qty2', 'sq.lokasi_fisik', 'a.valid');
    var $order = ['tanggal_masuk' => 'desc'];
    var $column_search = array('a.barcode_id', 'a.quant_id', 'a.kode_produk', 'a.nama_produk', 'sq.lokasi_fisik', 'a.corak_remark', 'a.warna_remark', 'a.valid');

    public function insertItem(array $data) {
        try {
            $this->db->insert($this->table, $data);
            $db_error = $this->db->error();
            if ($db_error['code'] > 0) {
                throw new Exception($db_error['message']);
            }
            return "";
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
    }

    public function insertBatch(array $data) {
        $this->db->insert_batch($this->table, $data);
    }

    protected function _getDataItem() {
        $this->db->select("a.*,ms.nama_status as valid,sq.lokasi_fisik as lokasi_fisik, dt.bulk_no_bulk as bulk,dod.do_id as dod,dod.status as dodstatus");
        $this->db->join('mst_status as ms', 'ms.kode = a.valid', 'left');
        $this->db->from($this->table . ' a');

        $this->db->join('stock_quant as sq', 'sq.quant_id = a.quant_id');
//        $this->db->join("bulk_detail dt", "dt.barcode = a.barcode_id", "left");
        $this->db->join("bulk_detail dt", "dt.picklist_detail_id = a.id", "left");
//        $this->db->join("delivery_order_detail dod", "(dod.barcode_id = a.barcode_id and dod.status = 'done')", "left");
        $this->db->join("delivery_order_detail dod", "(dod.picklist_detail_id = a.id and dod.status = 'done')", "left");
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

    public function getData(array $condition = [], $join = [], $notin = []) {
        $this->_getDataItem();

        foreach ($notin as $key => $value) {
            $this->db->where_not_in($key, $value);
        }

        if (count($condition) > 0) {
            $this->db->where($condition);
        }
        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }

    public function getCountDataFiltered(array $condition = [], $join = [], $notin = []) {
        $this->_getDataItem();
        foreach ($notin as $key => $value) {
            $this->db->where_not_in($key, $value);
        }
        if (count($condition) > 0) {
            $this->db->where($condition);
        }
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function getCountAllData(array $condition = [], $join = [], $notin = []) {
        $this->db->from($this->table);
        if (count($condition) > 0) {
            $this->db->where($condition);
        }
        return $this->db->count_all_results();
    }

    public function detailReport($condition, array $group = ['corak_remark', 'warna_remark']) {
        $this->db->from($this->table);
        $this->db->where($condition);
        $this->db->select('no_pl,kode_produk,nama_produk,warna_remark,corak_remark,sales_order,uom,lebar_jadi,uom_lebar_jadi, count(qty) as jml_qty, sum(qty) as total_qty');
        $this->db->order_by('corak_remark', 'asc');
        $this->db->group_by($group);
        return $this->db->get()->result();
    }

    public function detailDraftReport($condition, $nopl, array $group = ['corak_remark', 'warna_remark'], array $join = []) {
        $this->db->from($this->table);
        $this->db->where($condition);
        $this->db->select('picklist_detail.no_pl,kode_produk,nama_produk,warna_remark,corak_remark,sales_order,uom,lebar_jadi,uom_lebar_jadi, count(qty) as jml_qty, sum(qty) as total_qty');
        foreach ($join as $value) {
            switch ($value) {
                case "BULK":
                    $this->db->select("bbd.no_bulk,bbd.gross_weight,bbd.net_weight");
                    $this->db->join("("
                            . "select b.no_pl,b.net_weight,b.gross_weight,bd.barcode,b.no_bulk,bd.picklist_detail_id from bulk b
                                join bulk_detail bd on bd.bulk_no_bulk = b.no_bulk where b.no_pl = '" . $nopl . "'"
                            . ") as bbd ", "bbd.picklist_detail_id = picklist_detail.id", "left");

                    $this->db->order_by('bbd.no_bulk', 'asc');
                    break;

                default:
                    break;
            }
        }

        $this->db->group_by($group);
        return $this->db->get()->result();
    }
    public function detailReportQty($condition, $select = 'qty,uom', array $join = []) {
        $this->db->from($this->table);

        foreach ($join as $key => $value) {
            switch ($value) {
                case "BULK":
                    $this->db->join("bulk_detail bd", "bd.picklist_detail_id = " . $this->table . ".id");
                    break;

                default:
                    break;
            }
        }
        $this->db->where($condition);
        $this->db->select($select);
        return $this->db->get()->result();
    }

    public function deleteItem($condition) {
        $this->db->delete($this->table, $condition);
    }

    public function detailData(array $condition, $deepCheck = false, string $column_order = 'tanggal_masuk', string $orderby = 'DESC') {
        $this->db->from($this->table);
        $this->db->order_by($column_order, $orderby);
        $this->db->where($condition);
        if ($deepCheck) {
            $this->db->join('stock_quant sq', 'sq.quant_id = ' . $this->table . '.quant_id');
        }
        return $this->db->select('*')->get()->row();
    }

    public function statusCount(array $condition) {
        $this->db->from($this->table);
        $this->db->where($condition);
        $this->db->group_by('valid');
        return $this->db->select('valid,COUNT(id) as cnt')->get()->result();
    }

    public function updateStatus(array $condition, array $data) {
        try {
            $this->db->set($data);
            $this->db->where($condition);
            $this->db->update($this->table);
            $db_error = $this->db->error();
            if ($db_error['code'] > 0) {
                throw new Exception($db_error['message']);
            }
            return "";
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
    }

    public function updateStatusWin(array $condition, array $data, array $arrayCondition = [], $in = true) {
        try {
            $this->db->set($data);
            $this->db->where($condition);
            if ($in) {
                foreach ($arrayCondition as $key => $value) {
                    $this->db->where_in($key, $value);
                }
            } else {
                foreach ($arrayCondition as $key => $value) {
                    $this->db->where_not_in($key, $value);
                }
            }
            $this->db->update($this->table);
            $db_error = $this->db->error();
            if ($db_error['code'] > 0) {
                throw new Exception($db_error['message']);
            }
            return "";
        } catch (Exception $ex) {
            log_message("error","masuk".json_encode($ex));
            return $ex->getMessage();
        }
    }

    public function getSc(array $condition, $group = null) {
        $this->db->from($this->table);
        $this->db->where($condition);
        if (!is_null($group)) {
            $this->db->group_by($group);
        }
        $result = $this->db->select('GROUP_CONCAT(DISTINCT sales_order) as sc');
        return $result->get()->row();
    }

    public function getBarcodeID($condition = [], $whereNotIn = []) {
        $this->db->from($this->table . ' a');
        $this->db->join('stock_quant as sq', 'sq.quant_id = a.quant_id');
        $this->db->where($condition);
        foreach ($whereNotIn as $key => $value) {
            $this->db->where_not_in($key, $value);
        }
        $result = $this->db->select('a.barcode_id,sq.*,a.id as picklist_detail_id');
        return $result->get()->result();
    }

    public function contoh($limit, $size) {
        $this->db->from($this->table);
        $this->db->join('stock_quant sq', '(sq.quant_id = picklist_detail.quant_id and valid <> "cancel")');
        $this->db->where_in("barcode_id", $limit);
        $rest = $this->db->select("barcode_id,sq.nama_produk,picklist_detail.corak_remark,sq.warna_remark,picklist_detail.lebar_jadi,picklist_detail.uom_lebar_jadi,picklist_detail.qty,picklist_detail.uom")->get();
        return $rest->result();
    }

    protected function doDataList_() {
        $this->db->from($this->table . ' pd');
        $columnSearch = ["barcode_id", "corak_remark", "warna_remark"];
        $columnOrder = ["corak_remark", "warna_remark"];
        foreach ($columnSearch as $key => $value) {
            if ($_POST['search']['value']) {
                if ($key === 0) {
                    $this->db->group_start();
                    $this->db->like($value, $_POST['search']['value']);
                } else {
                    $this->db->or_like($value, $_POST['search']['value']);
                }

                if (count($columnSearch) === ($key + 1)) {
                    $this->db->group_end();
                }
            }
        }

        if (isset($_POST['order'])) {
            $this->db->order_by($columnOrder[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else {
//            $this->db->order_by('tanggal_masuk', 'ASC');
        }
        $this->db->group_by('pd.warna_remark, pd.corak_remark,pd.uom');
        $this->db->select('pd.corak_remark,pd.warna_remark,sum(qty) as total_qty,count(qty) as jumlah_qty,uom');
    }

    public function getdoDataList(array $condition = [], $joinBulk = [], $notIn = []) {
        $this->doDataList_();
        if (count($condition) > 0) {
            $this->db->where($condition);
        }
        if (count($joinBulk) > 0) {
//            $this->db->join("bulk_detail bd", "pd.barcode_id = bd.barcode");
            $this->db->join("bulk_detail bd", "pd.id = bd.picklist_detail_id");
//            $this->db->join("bulk b", "b.no_pl = pd.no_pl");
            $this->db->where_in('bd.bulk_no_bulk', $joinBulk);
            $this->db->group_by('pd.warna_remark, pd.corak_remark,pd.uom,bd.bulk_no_bulk');
            $this->db->select('pd.corak_remark,pd.warna_remark,sum(qty) as total_qty,count(qty) as jumlah_qty,uom,bd.bulk_no_bulk');
            $this->db->order_by("bulk_no_bulk", "ASC");
        }
        if (count($notIn) > 0) {
            foreach ($notIn as $key => $value) {
                $this->db->where_not_in($key, $value);
            }
        }
        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }

    public function getCountdoDataListFiltered(array $condition = [], $joinBulk = [], $notIn = []) {
        $this->doDataList_();
        if (count($condition) > 0) {
            $this->db->where($condition);
        }
        if (count($joinBulk) > 0) {
            $this->db->join("bulk_detail bd", "pd.barcode_id = bd.barcode");
//            $this->db->join("bulk b", "b.no_pl = pd.no_pl");
            $this->db->group_by('pd.warna_remark, pd.corak_remark,pd.uom,bd.bulk_no_bulk');
            $this->db->where_in('bd.bulk_no_bulk', $joinBulk);
        }
        if (count($notIn) > 0) {
            foreach ($notIn as $key => $value) {
                $this->db->where_not_in($key, $value);
            }
        }
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function getCountAlldoDataList(array $condition = [], $joinBulk = [], $notIn = []) {
        $this->db->from($this->table . ' pd');
        if (count($condition) > 0) {
            $this->db->where($condition);
        }
        if (count($joinBulk) > 0) {
            $this->db->join("bulk_detail bd", "(pd.barcode_id = bd.barcode)", "right");
//            $this->db->join("bulk b", "b.no_pl = pd.no_pl");
            $this->db->group_by('pd.warna_remark, pd.corak_remark,pd.uom,bd.bulk_no_bulk');
            $this->db->where_in('bd.bulk_no_bulk', $joinBulk);
            $this->db->select('barcode_id');
        }
        if (count($notIn) > 0) {
            foreach ($notIn as $key => $value) {
                $this->db->where_not_in($key, $value);
            }
        }
        return $this->db->count_all_results();
    }

    protected function _getDataItemViewDodd() {
        $this->db->select("a.*");
        $this->db->from($this->table . ' a');
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

    public function getDataViewDodd(array $condition = [], $join = [], $notin = [], $in = []) {
        $this->_getDataItemViewDodd();
        foreach ($join as $key => $value) {
            switch ($value) {
                case "BULK":
//                    $this->db->join("bulk_detail dt", "dt.barcode = a.barcode_id");
                    $this->db->join("bulk_detail dt", "dt.picklist_detail_id = a.id");
                    $this->db->select(",dt.bulk_no_bulk");
                    $this->db->order_by("bulk_no_bulk", "ASC");
                    foreach ($in as $key => $value) {
                        $this->db->where_in($key, $value);
                    }
                    break;

                default:
                    break;
            }
        }

        foreach ($notin as $key => $value) {
            $this->db->where_not_in($key, $value);
        }

        if (count($condition) > 0) {
            $this->db->where($condition);
        }
        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }

    public function getCountDataFilteredViewDodd(array $condition = [], $join = [], $notin = [], $in = []) {
        $this->_getDataItemViewDodd();
        foreach ($join as $key => $value) {
            switch ($value) {
                case "BULK":
//                    $this->db->join("bulk_detail dt", "dt.barcode = a.barcode_id");
                    $this->db->join("bulk_detail dt", "dt.picklist_detail_id = a.id");
                    foreach ($in as $key => $value) {
                        $this->db->where_in($key, $value);
                    }
                    break;

                default:
                    break;
            }
        }
        foreach ($notin as $key => $value) {
            $this->db->where_not_in($key, $value);
        }
        if (count($condition) > 0) {
            $this->db->where($condition);
        }
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function getCountAllDataViewDodd(array $condition = [], $join = [], $notin = [], $in = []) {
        $this->db->from($this->table . ' a');
        foreach ($join as $key => $value) {
            switch ($value) {
                case "BULK":
//                    $this->db->join("bulk_detail dt", "dt.barcode = a.barcode_id");
                    $this->db->join("bulk_detail dt", "dt.picklist_detail_id = a.id");
                    
                    foreach ($in as $key => $value) {
                        $this->db->where_in($key, $value);
                    }
                    break;

                default:
                    break;
            }
        }
        if (count($condition) > 0) {
            $this->db->where($condition);
        }
        return $this->db->count_all_results();
    }

    public function reportLokasiFisikRak(array $condition) {
        $this->db->select("sq.lokasi_fisik");
        $this->db->from($this->table);
        $this->db->join("stock_quant sq", "sq.quant_id = picklist_detail.quant_id", "left");
        $this->db->where($condition);
        $this->db->group_by('sq.lokasi_fisik');
        $this->db->order_by("sq.lokasi_fisik", "asc");
        $query = $this->db->get();
        return $query->result();
    }

    public function reportLokasiFisik(array $condition, array $in = []) {
        $this->db->from($this->table);
        $this->db->join("stock_quant sq", "sq.quant_id = picklist_detail.quant_id", "left");
        $this->db->select("sq.lokasi_fisik,barcode_id,picklist_detail.corak_remark,picklist_detail.warna_remark,picklist_detail.qty");
        $this->db->where($condition);
        foreach ($in as $key => $value) {
            $this->db->where_in($key, $value);
        }
        $this->db->order_by("sq.lokasi_fisik", "asc");
        $query = $this->db->get();
        return $query->result();
    }

    public function getCountDetail(array $condition) {
        $this->db->from($this->table . " pd");
        $this->db->where($condition);
        $query = $this->db->select('count(pd.barcode_id) as total_item, sum(pd.qty) as total_qty, count(pd.qty) as jumlah_qty')->get();
        return $query->row();
    }

    public function __destruct() {
        $this->db->db_debug = $this->db_debug;
    }
}
