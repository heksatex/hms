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
        $this->db->join('bulk b','b.no_bulk = bulk_no_bulk');
        if (count($condition) > 0) {
            $this->db->where($condition);
        }

        return $this->db->count_all_results();
    }

    public function getDataListBulk(array $condition) {
        $this->db->from($this->table . ' bd');
        $this->db->where($condition);
        $this->db->join('bulk b', 'b.no_bulk = bd.bulk_no_bulk', 'right');
        $this->db->join('picklist_detail pl', '(pl.barcode_id = bd.barcode and pl.valid != "cancel")');
        $this->db->group_by('pl.warna_remark, pl.corak_remark,pl.uom,b.no_bulk');
        $this->db->order_by("no_bulk asc,pl.corak_remark asc");
        $query = $this->db->select('b.no_bulk,pl.corak_remark,pl.warna_remark,sum(qty) as total_qty,count(qty) as jumlah_qty')->get();
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
        $this->db->join('picklist_detail a', 'a.barcode_id = bd.barcode');
        $this->db->join('stock_quant as sq', 'sq.quant_id = a.quant_id');
        $this->db->where($condition);
        if (count($whereIn) > 0) {
            $this->db->where_in('b.no_bulk', $whereIn);
        }
        foreach ($whereNotIn as $key => $value) {
            $this->db->where_not_in($key, $value);
        }
        $query = $this->db->select('b.no_bulk,barcode as barcode_id,sq.*')->get();
        return $query->result();
    }
}
