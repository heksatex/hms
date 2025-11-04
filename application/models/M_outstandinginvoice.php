<?php defined('BASEPATH') OR exit('No Direct Script Acces Allowed');

/**
 * 
 */
class M_outstandinginvoice extends CI_Model
{

    function get_list_invoice_group_partner($where)
    {
        if(count($where) > 0) {
            $this->db->where($where);
        }

        $this->db->select('inv.id_supplier, p.nama as nama_partner');
        $this->db->from('invoice inv');
        $this->db->join('partner p', "inv.id_supplier = p.id", "inner");
        $this->db->join("currency_kurs c","inv.matauang = c.id","left");
        $this->db->group_by("inv.id_supplier");
        $this->db->order_by('p.nama', 'asc');
        $result = $this->db->get();
        return $result->result();
    }

    function get_list_invoice_by_partner($where) 
    {
        if(count($where) > 0 ) {
            $this->db->where($where);
        }

        $this->db->select("inv.no_invoice, p.nama as nama_partner, inv.no_po,  c.currency, inv.nilai_matauang , inv.order_date, DATEDIFF(CURDATE(), inv.order_date) AS hari, inv.origin, inv.status, IFNULL(inv.total_rp,0) as hutang_rp, IFNULL(inv.hutang_rp,0) as sisa_hutang_rp, IFNULL(inv.total_valas,0) as hutang_valas, IFNULL(inv.hutang_valas,0) as sisa_hutang_valas");
        $this->db->from('invoice inv');
        $this->db->join('partner p', "inv.id_supplier = p.id", "inner");
        $this->db->join("currency_kurs c","inv.matauang = c.id","left");
        $this->db->order_by('inv.order_date', 'asc');
        $this->db->order_by('inv.no_invoice', 'asc');
        $result = $this->db->get();
        return $result->result();
    }


    function get_list_aging_utang_supplier($where)
    {
        if(count($where) > 0 ) {
            $this->db->where($where);
        }
        $this->db->select("inv.id_supplier, p.nama AS nama_partner,SUM(inv.hutang_rp) AS total_hutang,
                        SUM(
                            CASE 
                                WHEN DATE_FORMAT(inv.order_date, '%Y-%m') = DATE_FORMAT(CURDATE(), '%Y-%m')
                                THEN inv.hutang_rp ELSE 0 
                            END
                        ) AS hutang_bulan_ini,
                        SUM(
                            CASE 
                                WHEN DATE_FORMAT(inv.order_date, '%Y-%m') = DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 1 MONTH), '%Y-%m')
                                THEN inv.hutang_rp ELSE 0 
                            END
                        ) AS hutang_bulan_1,
                        SUM(
                            CASE 
                                WHEN DATE_FORMAT(inv.order_date, '%Y-%m') = DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 2 MONTH), '%Y-%m')
                                THEN inv.hutang_rp ELSE 0 
                            END
                        ) AS hutang_bulan_2,
                        SUM(
                            CASE 
                                WHEN DATE_FORMAT(inv.order_date, '%Y-%m') = DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 3 MONTH), '%Y-%m')
                                THEN inv.hutang_rp ELSE 0 
                            END
                        ) AS hutang_bulan_3,
                        SUM(
                            CASE 
                                WHEN inv.order_date < DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 3 MONTH), '%Y-%m-01')
                                THEN inv.hutang_rp ELSE 0 
                            END
                        ) AS hutang_lebih_dari_3_bulan
                        ");
        $this->db->from('invoice inv');
        $this->db->join('partner p', "inv.id_supplier = p.id", "inner");
        $this->db->group_by("inv.id_supplier");
        $this->db->order_by('p.nama', 'asc');
        $result = $this->db->get();
        return $result->result();
        
    }

}