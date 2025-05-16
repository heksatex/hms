<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of Partner
 *
 * @author RONI
 */
class Partner extends MY_Controller {

    //put your code here
    public function __construct() {
        parent:: __construct();
        $this->is_loggedin();
        $this->load->model('_module');
        $this->load->model('m_user');
        $this->load->model('m_global');
        $this->load->model("m_partner");
    }

    public function index() {
        $data['id_dept'] = 'PPRT';
        $this->load->view('purchase/v_partner', $data);
    }

    public function list_data() {
        try {
            $data = array();
            $list = new $this->m_global;
            $list->setTables("partner")->setWheres(["supplier" => 1])
                    ->setJoins("partner_states","partner_states.id = invoice_state","left")
                    ->setJoins("partner_country","partner_country.id = invoice_country","left")
                    ->setOrder(["id" => "desc"])->setSearch(["nama", "invoice_city", "delivery_city"])
                    ->setOrders([null, "nama","invoice_street","invoice_city","nama_state","nama_country","invoice_zip"])
                    ->setSelects(["partner.*","partner_states.name as nama_state","partner_country.name as nama_country"]);
            $no = $_POST['start'];
            foreach ($list->getData() as $field) {
                $kode_encrypt = encrypt_url($field->id);
                $no++;
                $data [] = array(
                    $no,
                    '<a href="' . base_url('purchase/partner/edit/' . $kode_encrypt) . '">' . $field->nama . '</a>',
                    $field->invoice_street,
                    $field->invoice_city,
                    $field->nama_state,
                    $field->nama_country,
                    $field->invoice_zip,
                );
            }
            echo json_encode(array("draw" => $_POST['draw'],
                "recordsTotal" => $list->getDataCountAll(),
                "recordsFiltered" => $list->getDataCountFiltered(),
                "data" => $data,
            ));
            exit();
        } catch (Exception $ex) {
            echo json_encode(array("draw" => $_POST['draw'],
                "recordsTotal" => 0,
                "recordsFiltered" => 0,
                "data" => [],
            ));
        }
    }

    public function add() {
        $data['id_dept'] = 'PPRT';
        return $this->load->view('purchase/v_partner_add', $data);
    }
    
    public function edit($ids) {
        $id = decrypt_url($ids);
        $model = new $this->m_global;
        $data['mms']      = $this->_module->get_data_mms_for_log_history('PPRT');
        $data["partner"]= $model->setTables("partner")->setWheres(["id"=>$id])->getDetail();
        if(!$data["partner"]) {
            show_404();
        }
        $data['inv_id_country'] = $data["partner"]->invoice_country;
        $nama_country           = $this->m_partner->get_name_country_by_id($data['inv_id_country'] )->row_array();
        $data['inv_nm_country'] = $nama_country['name'];
        
        $data['inv_id_state']   = $data["partner"]->invoice_state;
        $nama_state             = $this->m_partner->get_name_state_by_id($data["partner"]->invoice_state)->row_array();
        $data['inv_nm_state']   = $nama_state['name'];
        
        // >> delivery
        //get nama country by id country
        $data['dv_id_country'] = $data["partner"]->delivery_country;
        $nama_country           = $this->m_partner->get_name_country_by_id($data["partner"]->delivery_country)->row_array();
        $data['dv_nm_country'] = $nama_country['name'];

        $data['dv_id_state']   = $data["partner"]->delivery_state;
        $nama_state             = $this->m_partner->get_name_state_by_id($data["partner"]->delivery_state)->row_array();
        $data['dv_nm_state']   = $nama_state['name'];
        // << delivery 
        
        $data['id_dept'] = 'PPRT';
        $data["ids"] = $ids;
        return $this->load->view('purchase/v_partner_edit', $data);
    }

    public function save($id = "") {
        try {
            $sub_menu = $this->uri->segment(2);
            $username = addslashes($this->session->userdata('username'));
            $tanggal = date('Y-m-d H:i:s');

            $name = addslashes($this->input->post('name'));
            $invoice_street = addslashes($this->input->post('invoice_street'));
            $invoice_city = addslashes($this->input->post('invoice_city'));
            $invoice_state = addslashes($this->input->post('invoice_state'));
            $invoice_country = addslashes($this->input->post('invoice_country'));
            $invoice_zip = addslashes($this->input->post('invoice_zip'));

            $contact_person = addslashes($this->input->post('contact_person'));
            $phone = addslashes($this->input->post('phone'));
            $mobile = addslashes($this->input->post('mobile'));
            $fax = addslashes($this->input->post('fax'));
            $email = addslashes($this->input->post('email'));

            $delivery_street = addslashes($this->input->post('delivery_street'));
            $delivery_city = addslashes($this->input->post('delivery_city'));
            $delivery_state = addslashes($this->input->post('delivery_state'));
            $delivery_country = addslashes($this->input->post('delivery_country'));
            $delivery_zip = addslashes($this->input->post('delivery_zip'));

            $check_customer = $this->input->post('customer');
            $check_supplier = $this->input->post('supplier');

            $this->_module->lock_tabel('partner WRITE, log_history WRITE, main_menu_sub READ, user READ');

            $model = new $this->m_global;

            $data = [
                "nama" => $name,
                "create_date" => $tanggal,
                "invoice_street" => $invoice_street,
                "invoice_city" => $invoice_city,
                "invoice_state" => $invoice_state,
                "invoice_country" => $invoice_country,
                "invoice_zip" => $invoice_zip,
                "delivery_street" => $delivery_street,
                "delivery_city" => $delivery_city,
                "delivery_state" => $delivery_state,
                "delivery_country" => $delivery_country,
                "delivery_zip" => $delivery_zip,
                "contact_person" => $contact_person,
                "phone" => $phone,
                "mobile" => $mobile,
                "fax" => $fax,
                "email" => $email,
                "customer" => $check_customer,
                "supplier" => $check_supplier
            ];
            $jenis_log = "create";
            $id_encrypt = "";
            if ($id === "") {
                $last_id = $this->m_partner->get_last_id_partner();
                $id_encrypt = encrypt_url($last_id);
                $model->setTables("partner")->save($data);
            } else {
                $jenis_log = "edit";
                $last_id = decrypt_url($id);
                $model->setTables("partner")->setWheres(["id"=>$last_id])->update($data);
            }
            $note_log = $last_id . " | " . $name . " | " . $invoice_street . " | " . $invoice_city . " | " . $invoice_zip;
            $this->_module->gen_history($sub_menu, $last_id, $jenis_log, $note_log, $username);
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success', "id" => $id_encrypt)));
        } catch (Exception $ex) {
            
        } finally {
            $this->_module->unlock_tabel();
        }
    }
}
