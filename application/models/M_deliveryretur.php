<?php

defined('BASEPATH') or exit('No Direct Script Acces Allowed');
/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of D_deliveryretur
 *
 * @author RONI
 */
class M_deliveryretur extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    protected function _getDataReport() {
        $this->db->from('delivery_order ddo');
        $this->db->join("delivery_order_detail dod", 'dod.do_id = ddo.id ');
        $this->db->join("picklist_detail pd", "(pd.barcode_id = dod.barcode_id)");
        $this->db->select("ddo.no,ddo.no_sj,ddo.tanggal_buat,ddo.tanggal_dokumen,ddo.tanggal_batal,dod.tanggal_retur,p.jenis_jual,ddo.no_picklist,pr.nama,concat(pr.delivery_street,' , ',pr.delivery_city) as alamat,"
                . "alamat_kirim,pd.corak_remark,pd.warna_remark,pd.uom as uom_jual,pd.uom2 as uom2_jual,pd.uom_hph as uom,pd.uom2_hph as uom2,pd.lebar_jadi,pd.uom_lebar_jadi,"
                . "SUM(pd.qty_hph) as total_qty,SUM(pd.qty2_hph) as total_qty2,SUM(pd.qty) as total_qty_jual,SUM(pd.qty2) as total_qty2_jual,msg.nama_sales_group as marketing,ddo.user,ddo.note,dod.status");
//        $this->db->join("stock_quant sq", "sq.quant_id = pd.quant_id", "left");
//        $this->db->join("(select pd.barcode_id,pd.quant_id,pd.corak_remark,pd.warna_remark,sq.uom,sq.qty,sq.qty2,sq.qty_jual,sq.qty2_jual,"
//                . "sq.uom2,sq.uom_jual,sq.uom2_jual,sq.lebar_jadi,sq.uom_lebar_jadi,pd.id"
//                . " from picklist_detail pd join stock_quant sq on sq.quant_id = pd.quant_id GROUP BY pd.quant_id) pd", "(pd.id = dod.picklist_detail_id)");
//         $this->db->select("ddo.no,ddo.no_sj,ddo.tanggal_buat,ddo.tanggal_dokumen,ddo.tanggal_batal,dod.tanggal_retur,p.jenis_jual,ddo.no_picklist,pr.nama,concat(pr.delivery_street,' , ',pr.delivery_city) as alamat,"
//                . "alamat_kirim,pd.corak_remark,pd.warna_remark,pd.uom,pd.uom2,pd.uom_jual,pd.uom2_jual,pd.lebar_jadi,pd.uom_lebar_jadi,"
//                . "SUM(pd.qty) as total_qty,SUM(pd.qty2) as total_qty2,SUM(pd.qty_jual) as total_qty_jual,SUM(pd.qty2_jual) as total_qty2_jual,msg.nama_sales_group as marketing,ddo.user,ddo.note,dod.status");
        $this->db->join("picklist p", 'p.no = ddo.no_picklist');
        $this->db->join('partner pr', 'pr.id = p.customer_id', 'left');
        $this->db->join('mst_sales_group as msg', 'msg.kode_sales_group = p.sales_kode', 'left');
    }

    public function getDataReport(array $condition, $order = "", $rekap = "global", $raw = false) {
        $this->_getDataReport();
        if ($rekap === 'global') {
            $this->db->select(",COUNT(pd.barcode_id) as total_lot");
            $this->db->group_by("ddo.no,dod.status");
        } else if ($rekap === 'detail') {
            $this->db->select(",COUNT(pd.barcode_id) as total_lot");
            $this->db->group_by("ddo.no,pd.corak_remark,pd.warna_remark,pd.lebar_jadi,uom_jual,dod.status,no_picklist");
        } else {
            $this->db->select(",pd.barcode_id as total_lot");
            $this->db->group_by("pd.barcode_id,dod.status,no_picklist");
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

    public function getDataReportTotal(array $condition, $order = "", $rekap = "global", $raw = false) {
        $this->_getDataReport();
        if ($rekap === 'global') {
            $this->db->select(",COUNT(pd.barcode_id) as total_lot");
            $this->db->group_by("ddo.no,dod.status");
        } else if ($rekap === 'detail') {
            $this->db->select(",COUNT(pd.barcode_id) as total_lot");
            $this->db->group_by("ddo.no,pd.corak_remark,pd.warna_remark,pd.lebar_jadi,uom_jual,dod.status,no_picklist");
        } else {
            $this->db->select(",pd.barcode_id as total_lot");
            $this->db->group_by("ddo.no,pd.barcode_id,dod.status,no_picklist");
        }
        if (count($condition) > 0)
            $this->db->where($condition);

        if (!$raw) {
            $query = $this->db->get();
            return $query->num_rows();
        }
        return $this->db->get_compiled_select();
    }

    public function getDataReportUnion(array $query, $order = "", $rekap = "global") {
        $this->db->SELECT('*');
        $this->db->FROM('((' . implode(" ) UNION ALL ( ", $query) . ') ) as unionTable');
//        if ($rekap === 'global') {
//            $this->db->group_by("no,status");
//        } else if ($rekap === 'detail') {
//            $this->db->group_by("no,corak_remark,warna_remark,lebar_jadi,uom,status,no_picklist");
//        } else {
//            $this->db->group_by("total_lot,status,no_picklist");
//        }
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

    public function getDataReportTotalUnion(array $query, $rekap = "global") {
        $this->db->FROM('((' . implode(" ) UNION ALL ( ", $query) . ') ) as unionTable');
        $querys = $this->db->get();
        return $querys->num_rows();
    }
}
