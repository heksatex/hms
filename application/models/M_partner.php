<?php defined('BASEPATH') or exit('No Direct Script Acces Allowed');

/**
 * 
 */
class M_partner extends CI_Model
{

	//var $table        = 'partner';
	var $column_order = array(null, 'nama', 'buyer_code', 'invoice_street', 'invoice_city', 'invoice_state', 'invoice_country', 'invoice_zip', null);
	var $column_search = array('nama', 'buyer_code', 'invoice_street', 'invoice_city', 'invoice_state', 'invoice_country', 'invoice_zip');
	var $order  	  = array('create_date' => 'desc');

	public function __construct()
	{
		parent::__construct();
		$this->load->database('default', TRUE);
	}

	private function _get_datatables_query()
	{

		$nama_partner = $this->input->post("partner");
		$type = $this->input->post("type");

		if ($type != "all") {
			if ($type == 'customer')
				$this->db->where('customer', 1);
			if ($type == 'supplier')
				$this->db->where('supplier', 1);
		}

		$i = 0;
		//$this->db->from($this->table);
		$this->db->SELECT("p.id, p.nama, p.buyer_code, p.invoice_street, p.invoice_city, p.invoice_zip, ps.name as invoice_state, pc.name as invoice_country, CASE
                        WHEN customer = 1 AND supplier = 1 THEN 'Customer dan Supplier'
                        WHEN customer = 1 THEN 'Customer'
                        WHEN supplier = 1 THEN 'Supplier'
                        ELSE '-'
                    END AS partner_type");
		$this->db->FROM("partner p ");
		$this->db->JOIN("partner_country pc", "pc.id = p.invoice_country", "left");
		$this->db->JOIN("partner_states ps", "ps.id = p.invoice_state", "left");

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

	function get_datatables($mmss)
	{
		$this->_get_datatables_query();
		if ($_POST['length'] != -1)
			$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	function count_filtered($mmss)
	{
		$this->_get_datatables_query();
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all($mmss)
	{
		//$this->db->from($this->table);
		$this->db->SELECT("p.id, p.nama, p.buyer_code, p.invoice_street, p.invoice_city, p.invoice_zip, ps.name as invoice_state, pc.name as invoice_country");
		$this->db->FROM("partner p ");
		$this->db->JOIN("partner_country pc", "pc.id = p.invoice_country", "left");
		$this->db->JOIN("partner_states ps", "ps.id = p.invoice_state", "left");
		return $this->db->count_all_results();
	}


	public function save_partner($name, $tanggal, $invoice_street, $invoice_city, $invoice_state, $invoice_country, $invoice_zip, $buyer_code, $website, $tax_name, $tax_address, $tax_city, $npwp, $contact_person, $phone, $mobile, $fax, $email, $delivery_street, $delivery_city, $delivery_state, $delivery_country, $delivery_zip, $check_customer, $check_supplier, $golongan)
	{
		$this->db->query("INSERT INTO partner (nama,create_date,invoice_street,invoice_city,invoice_state,invoice_country,invoice_zip,buyer_code,website,tax_nama,tax_address,tax_city,npwp,contact_person,phone,mobile,fax,email,delivery_street,delivery_city,delivery_state,delivery_country,delivery_zip,customer,supplier, gol) values ('$name', '$tanggal', '$invoice_street', '$invoice_city', '$invoice_state', '$invoice_country', '$invoice_zip', '$buyer_code', '$website', '$tax_name', '$tax_address', '$tax_city', '$npwp', '$contact_person', '$phone', '$mobile', '$fax', '$email', '$delivery_street', '$delivery_city', '$delivery_state', '$delivery_country', '$delivery_zip', '$check_customer', '$check_supplier','$golongan') ");
	}

	public function get_last_id_partner()
	{
		$last = $this->db->query("SELECT max(id) as no FROM partner ")->row();

		return $last->no;
	}

	public function update_partner($name, $invoice_street, $invoice_city, $invoice_state, $invoice_country, $invoice_zip, $buyer_code, $website, $tax_name, $tax_address, $tax_city, $npwp, $contact_person, $phone, $mobile, $fax, $email, $delivery_street, $delivery_city, $delivery_state, $delivery_country, $delivery_zip, $check_customer, $check_supplier, $id, $golongan)
	{
		$this->db->query("UPDATE partner SET nama = '$name', invoice_street = '$invoice_street', invoice_city = '$invoice_city',
											 invoice_state = '$invoice_state', invoice_country = '$invoice_country',
											 invoice_zip  = '$invoice_zip', buyer_code = '$buyer_code',	 website = '$website', 
											 tax_nama = '$tax_name', tax_address = '$tax_address', tax_city = '$tax_city', 
											 npwp = '$npwp', contact_person = '$contact_person', phone = '$phone', 
											 mobile = '$mobile',fax = '$fax', email = '$email', delivery_street = '$delivery_street',
											 delivery_city = '$delivery_city', delivery_country = '$delivery_country',
											  delivery_state = '$delivery_state', delivery_zip = '$delivery_zip',
											  customer = '$check_customer', supplier = '$check_supplier', gol = '$golongan'
										Where id = '$id' ");
	}

	public function get_partner_by_kode($id)
	{
		return $this->db->query("SELECT * FROM partner Where id  = '$id'")->row();
	}


	public function get_list_states_select2_by_country($id, $name)
	{
		return $this->db->query("SELECT * FROM partner_states WHERE  country_id = '$id' AND name LIKE '%$name%' ORDER BY name asc LIMIT 100 ")->result_array();
	}


	public function get_list_country_select2($name)
	{
		return $this->db->query("SELECT * FROM partner_country WHERE name LIKE '%$name%' ORDER BY name asc LIMIT 100 ")->result_array();
	}


	public function get_name_country_by_id($id)
	{
		return $this->db->query("SELECT name FROM partner_country WHERE id = '$id' ");
	}

	public function get_name_state_by_id($id)
	{
		return $this->db->query("SELECT name FROM partner_states WHERE id = '$id' ");
	}


	public function cek_partner_by_nama($nama)
	{
		return $this->db->query("SELECT id,nama FROM partner where nama='$nama'");
	}


	public function get_list_golongan()
	{
		return $this->db->query("SELECT * FROM partner_gol order by golnama")->result();
	}
}
