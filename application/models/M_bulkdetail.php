<?php

defined('BASEPATH') or exit('No Direct Script Acces Allowed');
/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */

class M_bulkdetail extends CI_Model {

    protected $table = "bulk_detail";
    protected $column_order = array(null, 'bulk_no_bulk', 'barcode', 'tanggal_buat', 'user');
    protected $order = ['tanggal_buat' => 'desc'];
    protected $fillable = "bulk_detail.id,bulk_no_bulk,bulk_detail.barcode,bulk_detail.tanggal_input,bulk_detail.user";

    public function insert(array $data) {
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

    public function getDataDetail(array $condition, $joinBulk = false) {
        $this->db->from($this->table);
        if ($joinBulk)
            $this->joinBulk();
        $this->db->where($condition);
        return $this->db->select($this->fillable)->get()->row();
    }

    protected function joinBulk() {
        $this->db->join('bulk', 'no_bulk = bulk_no_bulk');
    }

    public function getTotalItem($condition = []) {
        $this->db->from($this->table);
        $this->db->join('bulk b', 'b.no_bulk = bulk_no_bulk');
        if (count($condition) > 0) {
            $this->db->where($condition);
        }

        return $this->db->count_all_results();
    }

    public function getDataListBulk(array $condition, $detail = false) {
        $this->db->from($this->table . ' bd');
        $this->db->where($condition);
        $this->db->join('bulk b', 'b.no_bulk = bd.bulk_no_bulk', 'right');
        $this->db->join('picklist_detail pl', '(pl.id = bd.picklist_detail_id and pl.valid != "cancel")');
        if (!$detail) {
            $this->db->group_by('pl.warna_remark, pl.corak_remark,pl.uom,b.no_bulk');
            $this->db->select("sum(qty) as total_qty,count(qty) as jumlah_qty");
        }
        $this->db->order_by("no_bulk asc,pl.corak_remark asc,pl.warna_remark asc,pl.uom asc");
        $query = $this->db->select('b.no_bulk,pl.corak_remark,pl.warna_remark,qty,pl.barcode_id,pl.uom,pl.lebar_jadi,pl.uom_lebar_jadi')->get();
        return $query->result();
    }

    public function delete(array $condition) {
        $this->db->delete($this->table, $condition);
    }

    public function updateBulkDetail(array $condition, array $data) {
        $this->db->set($data);
        $this->db->where($condition);
        $this->db->update($this->table);
    }

    public function getDataListBulks($condition = [], $whereIn = [], $whereNotIn = []) {
        $this->db->from($this->table . ' bd');
        $this->db->join('bulk b', 'b.no_bulk = bd.bulk_no_bulk');
        $this->db->join('picklist_detail a', '(a.id = bd.picklist_detail_id and a.valid <> "cancel")');
        $this->db->join('stock_quant as sq', 'sq.quant_id = a.quant_id');
        $this->db->where($condition);
        if (count($whereIn) > 0) {
            $this->db->where_in('b.no_bulk', $whereIn);
        }
        foreach ($whereNotIn as $key => $value) {
            $this->db->where_not_in($key, $value);
        }
        $query = $this->db->select('b.no_bulk,barcode as barcode_id,sq.*,picklist_detail_id')->get();
        return $query->result();
    }

    public function getTotalItemBulk(array $condition, $in = []) {
        $this->db->from($this->table . ' bd');
        $this->db->where($condition);
        foreach ($in as $key => $value) {
            $this->db->where_in($key, $value);
        }
        $this->db->join('bulk b', 'b.no_bulk = bd.bulk_no_bulk', 'right');
        $this->db->join('picklist_detail pl', '(pl.id = bd.picklist_detail_id and pl.valid != "cancel")');
        $query = $this->db->select('count(DISTINCT(b.no_bulk)) as total_bulk, sum(qty) as total_qty, count(qty) as jumlah_qty')->get();
        return $query->row();
    }

    protected function _barcodeOnBulk() {
        $columnSearch = ["bulk_no_bulk", "pd.barcode_id", "pd.corak_remark", "pd.warna_remark", "qty"];
        $columnOrder = [null, "bulk_no_bulk", "pd.barcode_id", "pd.corak_remark", "pd.warna_remar", "qty"];
        $order = ['bulk_no_bulk' => 'asc'];

        $this->db->from($this->table . ' bd');
        $this->db->join("bulk b", "b.no_bulk = bd.bulk_no_bulk");
        $this->db->join("picklist_detail pd", "pd.id = bd.picklist_detail_id", "right");
        $this->db->select("bulk_no_bulk,barcode_id,corak_remark,warna_remark,qty");
        foreach ($columnSearch as $key => $value) {
            if ($_POST['search']['value']) {
                if ($key === 0) {
                    $this->db->group_start();
                    $this->db->like($value, $_POST['search']['value']);
                } else {
                    $this->db->or_like($value, $_POST['search']['value']);
                }

                if (count($columnSearch) - 1 === $key)
                    $this->db->group_end();
            }
        }
        if (isset($_POST['order'])) { // here order processing
            $this->db->order_by($columnOrder[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($order)) {
            $order = $order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    public function getDataBulk(array $condition = []) {
        $this->_barcodeOnBulk();
        if (count($condition) > 0) {
            $this->db->where($condition);
        }
        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }

    public function getCountDataFilteredBulk(array $condition = []) {
        $this->_barcodeOnBulk();
        if (count($condition) > 0) {
            $this->db->where($condition);
        }
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function getCountAllDataBulk(array $condition = []) {
        $this->db->from($this->table . ' bd');
        $this->db->join("bulk b", "b.no_bulk = bd.bulk_no_bulk");
        $this->db->join("picklist_detail pd", "pd.id = bd.picklist_detail_id", "right");
        if (count($condition) > 0) {
            $this->db->where($condition);
        }
        return $this->db->count_all_results();
    }
}
