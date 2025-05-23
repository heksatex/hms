<?php defined('BASEPATH') or exit('No Direct Script Acces Allowed');

/**
 * 
 */
class M_uom extends CI_Model
{
    var $column_order = array(null, 'nama', 'short', 'jenis', 'jual', 'beli');
    var $column_search = array('nama', 'short', 'jenis', 'jual', 'beli');
    var $order        = array('id' => 'asc');
    var $table        = "uom";

    private function _get_datatables_query()
    {

        $this->db->from($this->table);
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
        $this->db->from($this->table);
        return $this->db->count_all_results();
    }

    public function get_uom_by_id($id)
    {
        $this->db->where('id', $id);
        $this->db->from('uom');
        return $this->db->get();
    }

    public function get_last_id_uom()
    {
        $last_no = $this->db->query("SELECT max(id) as nom FROM uom");

        $result = $last_no->row();
        if (empty($result->nom)) {
            $no = 1;
        } else {
            $no = (int) $result->nom + 1;
        }
        return $no;
    }

    public function save_uom($data_uom)
    {
        $this->db->insert('uom', $data_uom);
    }

    public function cek_uom_double($nama, $short, $id = '')
    {
        if ($id) {
            $this->db->where('id', $id);
        }
        $this->db->where('nama', $nama);
        $this->db->where('short', $short);
        $this->db->from('uom');
        $query = $this->db->get();
        return $query->row();
    }
}
