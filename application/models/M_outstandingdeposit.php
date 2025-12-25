<?php defined('BASEPATH') OR exit('No Direct Script Acces Allowed');

/**
 * 
 */
class M_outstandingdeposit extends CI_Model
{
    var $column_order = array(null, 'acc_pelunasan_piutang.no_pelunasan', 'tanggal_transaksi',  'partner.nama',  'currency', 'kurs', 'total_rp', 'total_valas');
    var $column_search = array('acc_pelunasan_piutang.no_pelunasan', 'tanggal_transaksi', 'partner.nama',  'currency', 'kurs');
    var $order         = array('tanggal_transaksi' => 'asc');


    function get_query()
    {
        $this->db->where('acc_pelunasan_piutang.status', "done");
        $this->db->where('acc_pelunasan_piutang_summary_koreksi.alat_pelunasan', "true");
        $this->db->where('acc_pelunasan_piutang_summary_koreksi.lunas', 0);
        $this->db->select("acc_pelunasan_piutang_summary_koreksi.id, acc_pelunasan_piutang.no_pelunasan, partner.nama as partner_nama, acc_pelunasan_piutang.tanggal_transaksi,'Deposit' as uraian, acc_pelunasan_piutang_summary.currency, acc_pelunasan_piutang_summary.kurs,
				 IF(acc_pelunasan_piutang_summary.currency = 'IDR', acc_pelunasan_piutang_summary_koreksi.nominal, acc_pelunasan_piutang_summary_koreksi.nominal * acc_pelunasan_piutang_summary.kurs)  as total_rp, 
				 IF(acc_pelunasan_piutang_summary.currency != 'IDR', acc_pelunasan_piutang_summary_koreksi.nominal, 0) as total_valas ");
        $this->db->from("acc_pelunasan_piutang ");
        $this->db->join("partner ", "partner.id = acc_pelunasan_piutang.partner_id", "INNER");
        $this->db->join("acc_pelunasan_piutang_summary ", "acc_pelunasan_piutang_summary.pelunasan_piutang_id = acc_pelunasan_piutang.id", "INNER");
        $this->db->join("acc_pelunasan_piutang_summary_koreksi ", "acc_pelunasan_piutang_summary.id = acc_pelunasan_piutang_summary_koreksi.pelunasan_summary_id", "INNER");
        
    }

    private function _get_datatables_query()
    {
        $this->get_query();

        $i = 0;

        foreach ($this->column_search as $item) // loop column 
        {
            if ($_POST['search']['value']) // if datatable send POST for search
            {

                if ($i === 0) // first loop
                {
                    $this->db->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
                    $this->db->like($item, $_POST['search']['value']);
                } else {
                    $this->db->or_like($item, $_POST['search']['value']);
                }

                if (count($this->column_search) - 1 == $i) //last loop
                    $this->db->group_end(); //close bracket
            }
            $i++;
        }

        if (isset($_POST['order'])) // here order processing
        {
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    function get_datatables()
    {
        $this->_get_datatables_query();
        if (isset($_POST["length"]) && $_POST["length"] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }

    function count_filtered()
    {
        $this->_get_datatables_query();
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all()
    {
        $this->get_query();
        return $this->db->count_all_results();
    }


 
    
}