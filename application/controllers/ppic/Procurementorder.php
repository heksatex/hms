<?php

defined('BASEPATH') OR EXIT('No Direct Script Acces Allowed');

/**
 * 
 */
class Procurementorder extends MY_Controller {

    public function __construct() {
        parent:: __construct();
        $this->is_loggedin(); //cek apakah user sudah login
        $this->load->model('m_procurementOrder');
        $this->load->model("m_gtp");
        $this->load->library("wa_message");
        $this->config->load('additional');
        $this->load->model('_module');
    }

    public function index() {
        $data['id_dept'] = 'PRC';
        $this->load->view('ppic/v_procurement_order', $data);
    }

    function get_data() {
        $sub_menu = $this->uri->segment(2);
        $kode = $this->_module->get_kode_sub_menu($sub_menu)->row_array();
        $list = $this->m_procurementOrder->get_datatables($kode['kode']);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $field) {
            $kode_encrypt = encrypt_url($field->kode_proc);
            $no++;
            $row = array();
            if ($field->show_sales_order == 'yes') {
                $capt_sc = 'Yes';
            } else if ($field->show_sales_order == 'no') {
                $capt_sc = 'No';
            } else {
                $capt_sc = '';
            }

            $row[] = $no;
            $row[] = '<a href="' . base_url('ppic/procurementorder/edit/' . $kode_encrypt) . '">' . $field->kode_proc . '</a>';
            $row[] = $field->create_date;
            $row[] = $field->type;
            $row[] = $capt_sc;
            $row[] = $field->schedule_date;
            $row[] = $field->sales_order;
            $row[] = $field->priority;
            $row[] = $field->nama_dept;
            $row[] = $field->notes;
            $row[] = $field->nama_status;
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_procurementOrder->count_all($kode['kode']),
            "recordsFiltered" => $this->m_procurementOrder->count_filtered($kode['kode']),
            "data" => $data,
        );
        //output dalam format JSON
        echo json_encode($output);
    }

    public function add() {
        $data['id_dept'] = 'PRC';
        $data['warehouse'] = $this->_module->get_list_departement();
        return $this->load->view('ppic/v_procurement_order_add', $data);
    }

    public function list_production_order_modal() {
        return $this->load->view('modal/v_production_order_list_modal');
    }

    public function get_list_data_production_order_modal() {
        $list = $this->m_procurementOrder->get_datatables2();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $field) {

            $no++;
            $row = array();
            $row[] = $no;
            $row[] = '<a href="#" class="pilih" sales_order="' . $field->sales_order . '"  kode_prod="' . $field->kode_prod . '">' . $field->kode_prod . '</a>';
            $row[] = $field->create_date;
            $row[] = '<a href="#" class="pilih" sales_order="' . $field->sales_order . '"  kode_prod="' . $field->kode_prod . '">' . $field->sales_order . '</a>';
            $row[] = $field->priority;

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_procurementOrder->count_all2(),
            "recordsFiltered" => $this->m_procurementOrder->count_filtered2(),
            "data" => $data,
        );
        //output dalam format JSON
        echo json_encode($output);
    }

    public function view_detail_items() {
        $kode = $this->input->post('kode');
        $kode_prod = $this->input->post('kode_prod');
        $sales_order = $this->input->post('sales_order');
        $kode_produk = $this->input->post('kode_produk');
        $nama_produk = $this->input->post('nama_produk');
        $row_order = $this->input->post('row_order');
        $origin = '';

        // cek type Procurement Order (Make to order = mto, Makte to stock = mts, Pengiriman = pengiriman)
        $type_proc = $this->m_procurementOrder->cek_type_procurement_order_by_kode($kode);
        //cek show_sc =(yes,no)
        $show_sc = $this->m_procurementOrder->cek_show_sales_order_by_kode($kode);
        if ($type_proc == 'mto') {
            $origin = $sales_order . '|' . $kode_prod . '|' . $kode . '|' . $row_order;
        } else if ($type_proc == 'mts') {
            if ($show_sc == 'yes') {
                $origin = $sales_order . '|' . $kode_prod . '|' . $kode . '|MTS|' . $row_order;
            } else {
                $origin = $kode . '|MTS|' . $row_order;
            }
        } else if ($type_proc == 'pengiriman') {
            if ($show_sc == 'yes') {
                $origin = $sales_order . '|' . $kode_prod . '|' . $kode . '|OUT|' . $row_order;
            } else {
                $origin = $kode . '|OUT|' . $row_order;
            }
        }

        $data['kode_produk'] = $kode_produk;
        $data['nama_produk'] = $nama_produk;
        $data['kode'] = $kode;
        $data['row_order'] = $row_order;
        $data['origin'] = $origin;
        $data['penerimaan'] = $this->_module->get_detail_items_penerimaan($origin);
        $data['pengiriman'] = $this->_module->get_detail_items_pengiriman($origin);
        $data['mo'] = $this->_module->get_detail_items_mo($origin);
        return $this->load->view('modal/v_procurement_order_detail_items_modal', $data);
    }

    public function simpan() {

        if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
            // session habis
            $callback = array('message' => 'Waktu Anda Telah Habis', 'sesi' => 'habis');
        } else {

            $sub_menu = $this->uri->segment(2);
            $username = addslashes($this->session->userdata('username'));

            $kode_proc = addslashes($this->input->post('kode_proc'));
            $kode_prod = addslashes($this->input->post('kode_prod'));
            $tgl = $this->input->post('tgl');
            $note = addslashes($this->input->post('note'));
            $sales_order = addslashes($this->input->post('sales_order'));
            $priority = addslashes($this->input->post('priority'));
            $warehouse = addslashes($this->input->post('warehouse'));
            $type_arr = $this->input->post('type');   // Makte to Order (mts), Make to Stock (mto), Pengiriman
            $show_sc_arr = $this->input->post('show_sc'); // yes or No    

            $type = '';
            $show_sc = '';
            if (!empty($type_arr)) {
                foreach ($type_arr as $val) {
                    $type = $val;
                    break;
                }
            }
            if (!empty($show_sc_arr)) {
                foreach ($show_sc_arr as $val2) {
                    $show_sc = $val2;
                    break;
                }
            }

            if (empty($tgl)) {
                $callback = array('status' => 'failed', 'field' => 'tgl', 'message' => 'Create Date Harus Diisi !', 'icon' => 'fa fa-warning',
                    'type' => 'danger');
            } elseif (empty($note)) {
                $callback = array('status' => 'failed', 'field' => 'note', 'message' => 'Notes Harus Diisi !', 'icon' => 'fa fa-warning',
                    'type' => 'danger');
            } elseif (empty($type_arr) AND empty($kode_proc)) {
                $callback = array('status' => 'failed', 'field' => 'mto', 'message' => 'Type Procurement Harus Diisi !', 'icon' => 'fa fa-warning',
                    'type' => 'danger');
            } elseif (empty($show_sc_arr) AND empty($kode_proc)) {
                $callback = array('status' => 'failed', 'field' => 'sc_true', 'message' => 'Pilih salah satu Sales Order Yes/No !', 'icon' => 'fa fa-warning',
                    'type' => 'danger');
            } elseif (empty($sales_order) AND $show_sc == 'yes') {
                $callback = array('status' => 'failed', 'field' => 'sales_order', 'message' => 'Sales Order  Harus Diisi !', 'icon' => 'fa fa-warning', 'type' => 'danger');
            } elseif (empty($kode_prod) AND $show_sc == 'yes') {
                $callback = array('status' => 'failed', 'field' => 'kode_prod', 'message' => 'Production Order Harus Diisi !', 'icon' => 'fa fa-warning', 'type' => 'danger');
            } elseif (empty($warehouse)) {
                $callback = array('status' => 'failed', 'field' => 'warehouse', 'message' => 'Departement Tujuan Harus Diisi !', 'icon' => 'fa fa-warning',
                    'type' => 'danger');
            } elseif (empty($priority)) {
                $callback = array('status' => 'failed', 'field' => 'priority', 'message' => 'Priority Harus Diisi !', 'icon' => 'fa fa-warning',
                    'type' => 'danger');
            } else {

                if (empty($kode_proc)) {//jika kode procurement order kosong, aksinya simpan data
                    $kode['kode_proc'] = $this->m_procurementOrder->get_kode_proc(); //get no procurement order
                    $kode_encrypt = encrypt_url($kode['kode_proc']);
                    $tgl_buat = date('Y-m-d H:i:s');

                    $this->m_procurementOrder->simpan($kode['kode_proc'], $tgl_buat, $tgl, $note, $sales_order, $kode_prod, $priority, $warehouse, 'draft', $type, $show_sc);

                    $callback = array('status' => 'success', 'field' => 'kode_proc', 'message' => 'Data Berhasil Disimpan !', 'isi' => $kode['kode_proc'], 'icon' => 'fa fa-check', 'type' => 'success', 'kode_encrypt' => $kode_encrypt);

                    if ($type == 'mts') {
                        $capt_type = 'Make to Stock';
                    } else if ($type == 'mto') {
                        $capt_type = 'Make to Order';
                    } else {
                        $capt_type = 'Pengiriman';
                    }

                    if ($show_sc == 'yes') {
                        $capt_sc = 'Yes';
                    } else if ($show_sc == 'no') {
                        $capt_sc = 'No';
                    } else {
                        $capt_sc = '';
                    }

                    $jenis_log = "create";
                    $note_log = $kode['kode_proc'] . " | " . $tgl . " | " . $note . " | " . $capt_type . " | " . $capt_sc . " | " . $kode_prod . " | " . $sales_order . " | " . $priority . " | " . $warehouse;
                    $this->_module->gen_history($sub_menu, $kode['kode_proc'], $jenis_log, $note_log, $username);
                } else {//jika kode procurement order ada, aksinya update data
                    $where_status = "AND status IN ('generated')";
                    $cek_details_status = $this->m_procurementOrder->cek_status_procurement_order_items($kode_proc, $where_status)->num_rows();

                    $detail_generate = false;
                    $ubah_warehouse = false;

                    if ($cek_details_status > 0) {
                        $detail_generate = true;
                        //cek warehouse by production order
                        $cek_warehouse = $this->m_procurementOrder->cek_warehouse_procurement_order_by_kode($kode_proc)->row_array();
                        if ($warehouse != $cek_warehouse['warehouse']) {
                            $ubah_warehouse = true;
                        } else {
                            $ubah_warehouse = false;
                        }
                    } else {
                        $detail_generate = false;
                    }

                    if ($detail_generate == true AND $ubah_warehouse == true) {
                        $callback = array('status' => 'failed', 'field' => 'warehouse', 'message' => 'Maaf, Warehouse tidak Bisa diubah !', 'icon' => 'fa fa-warning', 'type' => 'danger');
                    } else {
                        $this->m_procurementOrder->ubah($kode_proc, $tgl, $note, $priority, $warehouse);
                        $callback = array('status' => 'success', 'message' => 'Data Berhasil Disimpan !', 'icon' => 'fa fa-check', 'type' => 'success');

                        $jenis_log = "edit";
                        $note_log = $kode_proc . " | " . $tgl . " | " . $note . " | " . $priority . " | " . $warehouse;
                        $this->_module->gen_history($sub_menu, $kode_proc, $jenis_log, $note_log, $username);
                    }
                }
            }
        }

        echo json_encode($callback);
    }

    public function edit($id = null) {
        if (!isset($id))
            show_404();
        $kode_decrypt = decrypt_url($id);
        $data['id_dept'] = 'PRC';
        $data["procurementorder"] = $this->m_procurementOrder->get_data_by_code($kode_decrypt);
        $data['details'] = $this->m_procurementOrder->get_data_detail_by_code($kode_decrypt);
        $data['warehouse'] = $this->_module->get_list_departement();
        $data['uom'] = $this->_module->get_list_uom();

        //$data['cek_status'] = $this->m_procurementOrder->cek_status_procurement_order_items($kode_decrypt,'draft')->num_rows();

        if (empty($data["procurementorder"])) {
            show_404();
        } else {
            return $this->load->view('ppic/v_procurement_order_edit', $data);
        }
    }

    public function get_produk_procurement_order_select2() {
        $prod = addslashes($this->input->post('prod'));
        $callback = $this->m_procurementOrder->get_list_produk_procurement_order($prod);
        echo json_encode($callback);
    }

    public function get_prod_by_id() {
        $kode_produk = addslashes($this->input->post('kode_produk'));
        $result = $this->m_procurementOrder->get_produk_procurement_order_byid($kode_produk)->row_array();
        $callback = array('kode_produk' => $result['kode_produk'], 'nama_produk' => $result['nama_produk'], 'uom' => $result['uom']);
        echo json_encode($callback);
    }

    public function simpan_detail_procurement_order() {

        if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
            // session habis
            $callback = array('message' => 'Waktu Anda Telah Habis', 'sesi' => 'habis');
        } else {

            $sub_menu = $this->uri->segment(2);
            $username = addslashes($this->session->userdata('username'));

            $kode = addslashes($this->input->post('kode'));
            $kode_produk = addslashes($this->input->post('kode_produk'));
            $produk = addslashes($this->input->post('produk'));
            $tgl = $this->input->post('tgl');
            $qty = $this->input->post('qty');
            $uom = addslashes($this->input->post('uom'));
            $reff = addslashes($this->input->post('reff'));
            $row = $this->input->post('row_order');
            //$data        = explode("^|",$row1);
            //$row         = $data[0];

            if (!empty($row)) {//update details
                //lock table
                $this->_module->lock_tabel('procurement_order WRITE, procurement_order_items WRITE');

                // get data items by row 
                $d_items = $this->m_procurementOrder->get_data_items_by_row($kode, $row)->row_array();
                $kode_produk_ex_row = addslashes($d_items['kode_produk']);
                $row_order = $d_items['row_order'];
                $nama_produk = addslashes($d_items['nama_produk']);
                // $qty         = $d_items['qty'];
                $uom = $d_items['uom'];
                // $reff        = $d_items['reff_notes'];

                $cek_status = $this->m_procurementOrder->cek_status_procurement_order_items_by_row($kode, $kode_produk_ex_row, $row)->row_array();

                if (empty($cek_status['kode_produk'])) {
                    $callback = array('status' => 'failed', 'message' => 'Maaf, Produk Kosong !', 'icon' => 'fa fa-check', 'type' => 'danger');
                    //unlock table
                    $this->_module->unlock_tabel();
                } else if ($cek_status['status'] == 'generated') {
                    $callback = array('status' => 'failed', 'message' => 'Maaf, Data tidak bisa Diubah, Status Product Sudah Generated !', 'icon' => 'fa fa-check', 'type' => 'danger');
                    //unlock table
                    $this->_module->unlock_tabel();
                } else {
                    $this->m_procurementOrder->update_procurement_order_items($kode, $tgl, $qty, $reff, $row);

                    //unlock table
                    $this->_module->unlock_tabel();

                    $jenis_log = "edit";
                    $note_log = "Edit data Details | " . $kode . " | " . $tgl . " | " . $kode_produk_ex_row . " " . $nama_produk . " | " . $qty . " " . $uom . " | " . $reff . " | " . $row;
                    $this->_module->gen_history($sub_menu, $kode, $jenis_log, $note_log, $username);
                    $callback = array('status' => 'success', 'message' => 'Data Berhasil Disimpan !', 'icon' => 'fa fa-check', 'type' => 'success');
                }
            } else {//simpan data baru
                //lock table
                $this->_module->lock_tabel('procurement_order WRITE, procurement_order_items WRITE');

                $ro = $this->m_procurementOrder->get_row_order_procurement_order_items($kode)->row_array();
                $row_order = $ro['row_order'] + 1;
                $status = 'draft';
                $this->m_procurementOrder->save_procurement_order_items($kode, $kode_produk, $produk, $tgl, $qty, $uom, $reff, $status, $row_order);

                $cek_details = $this->m_procurementOrder->cek_status_procurement_order_items($kode, '')->num_rows();

                $where_status = "AND status NOT IN ('generated')";
                $cek_details_status = $this->m_procurementOrder->cek_status_procurement_order_items($kode, $where_status)->num_rows();

                if ($cek_details == 0) {
                    $this->m_procurementOrder->update_status_procurement_order($kode, 'draft');
                } else if ($cek_details > 0) {
                    if ($cek_details_status == 0) {
                        $this->m_procurementOrder->update_status_procurement_order($kode, 'done');
                    } else {
                        $this->m_procurementOrder->update_status_procurement_order($kode, 'draft');
                    }
                }

                //unlock table
                $this->_module->unlock_tabel();

                $jenis_log = "edit";
                $note_log = "Tambah data Details | " . $kode . " | " . $kode_produk . " " . $produk . " | " . $tgl . " | " . $qty . " " . $uom . " | " . $reff . " | " . $row_order;
                $this->_module->gen_history($sub_menu, $kode, $jenis_log, $note_log, $username);
                $callback = array('status' => 'success', 'message' => 'Data Berhasil Disimpan !', 'icon' => 'fa fa-check', 'type' => 'success');
            }


            echo json_encode($callback);
        }
    }

    public function hapus_procurement_order_items() {

        if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
            // session habis
            $callback = array('message' => 'Waktu Anda Telah Habis', 'sesi' => 'habis');
        } else {
            $sub_menu = $this->uri->segment(2);
            $username = addslashes($this->session->userdata('username'));

            $kode = addslashes($this->input->post('kode'));
            $row = $this->input->post('row_order');

            //lock table
            $this->_module->lock_tabel('procurement_order WRITE, procurement_order_items WRITE');

            // get data items by row 
            $d_items = $this->m_procurementOrder->get_data_items_by_row($kode, $row)->row_array();
            $row_order = $d_items['row_order'];
            $kode_produk = addslashes($d_items['kode_produk']);
            $nama_produk = addslashes($d_items['nama_produk']);
            $qty = $d_items['qty'];
            $uom = $d_items['uom'];
            $reff = $d_items['reff_notes'];
            $schedule_date = $d_items['schedule_date'];

            $cek_status = $this->m_procurementOrder->cek_status_procurement_order_items_by_row($kode, $kode_produk, $row_order)->row_array();

            if (empty($kode) && empty($row)) {
                $callback = array('status' => 'success', 'message' => 'Data Gagal Dihapus !', 'icon' => 'fa fa-warning', 'type' => 'danger');
                //unlock table
                $this->_module->unlock_tabel();
            } else if (empty($cek_status['kode_produk'])) {
                $callback = array('status' => 'failed', 'message' => 'Maaf, Produk Kosong  atau sudah dihapus !', 'icon' => 'fa fa-check', 'type' => 'danger');
                //unlock table
                $this->_module->unlock_tabel();
            } else if ($cek_status['status'] == 'generated') {
                $callback = array('status' => 'failed', 'message' => 'Maaf, Data tidak bisa Dihapus, Status Product Sudah Generated !', 'icon' => 'fa fa-check', 'type' => 'danger');
                //unlock table
                $this->_module->unlock_tabel();
            } else {
                $this->m_procurementOrder->delete_procurement_order_items($kode, $row_order);
                $cek_details = $this->m_procurementOrder->cek_status_procurement_order_items($kode, '')->num_rows();

                $where_status = "AND status NOT IN ('generated')";
                $cek_details_status = $this->m_procurementOrder->cek_status_procurement_order_items($kode, $where_status)->num_rows();

                $where_status2 = "AND status NOT IN ('generated','cancel')";
                $cek_details_status2 = $this->m_procurementOrder->cek_status_procurement_order_items($kode, $where_status2)->num_rows();

                if ($cek_details == 0) {
                    $this->m_procurementOrder->update_status_procurement_order($kode, 'draft');
                } else if ($cek_details > 0) {
                    if ($cek_details_status == 0) {
                        $this->m_procurementOrder->update_status_procurement_order($kode, 'done');
                    } else if ($cek_details_status2 == 0) {
                        $this->m_procurementOrder->update_status_procurement_order($kode, 'cancel');
                    } else {
                        $this->m_procurementOrder->update_status_procurement_order($kode, 'draft');
                    }
                }

                //unlock table
                $this->_module->unlock_tabel();

                $callback = array('status' => 'success', 'message' => 'Data Berhasil Dihapus !', 'icon' => 'fa fa-check', 'type' => 'success');

                $jenis_log = "cancel";
                $note_log = "Hapus data Details | " . $kode . " | " . $schedule_date . " | " . $kode_produk . " | " . $nama_produk . " | " . $qty . " | " . $uom . " | " . $row_order;
                $this->_module->gen_history($sub_menu, $kode, $jenis_log, $note_log, $username);
            }

            echo json_encode($callback);
        }
    }

    public function generate_detail_procurement_order() {

        if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
            // session habis
            $callback = array('message' => 'Waktu Anda Telah Habis', 'sesi' => 'habis');
        } else {
            $sub_menu = $this->uri->segment(2);
            $username = addslashes($this->session->userdata('username'));
            $nu = $this->_module->get_nama_user($username)->row_array();
            $nama_user = addslashes($nu['nama']);

            $kode = ($this->input->post('kode'));
            $row = $this->input->post('row_order');

            //lock table
            $this->_module->lock_tabel('procurement_order WRITE, procurement_order_items WRITE');

            // get data items by row 
            $d_items = $this->m_procurementOrder->get_data_items_by_row($kode, $row)->row_array();
            $row_order = $d_items['row_order'];
            $kode_produk = $d_items['kode_produk'];
            $nama_produk = $d_items['nama_produk']; //ex.. BD [PH-0206] POLY SDY SDC 50D-24/384
            $qty = $d_items['qty'];
            $uom = $d_items['uom'];
            $reff_notes = $d_items['reff_notes'];
            $schedule_date = $d_items['schedule_date'];

            // get data head
            $head = $this->m_procurementOrder->get_data_by_code($kode);
            $sales_order = $head->sales_order;
            $kode_production_order = $head->kode_prod;
            $warehouse = $head->warehouse; //exx WRP,JAC, WRD dll
            /*
              $data = explode("^|",$row);
              $row_order = $data[0];
              $kode_produk = ($data[1]);
              $nama_produk = ($data[2]);//ex.. BD [PH-0206] POLY SDY SDC 50D-24/384

              $qty = $data[3];
              $uom = ($data[4]);
              $reff_notes    = ($data[5]);
              $schedule_date = $data[6];
              $sales_order   = ($data[7]);
              $kode_production_order  = ($data[8]);
              $warehouse  = $data[9]; //exx  1|J-5P143SR-126" (Inspecting)
             */

            $status = "generated";

            $sm_row = 1;
            $source_move = "";
            $sql_stock_move_batch = "";
            $sql_stock_move_produk_batch = "";
            $sql_out_batch = "";
            $sql_out_items_batch = "";
            $sql_in_batch = "";
            $sql_in_items_batch = "";
            $sql_mrp_prod_batch = "";
            $sql_mrp_prod_rm_batch = "";
            $sql_mrp_prod_fg_batch = "";

            $reff_picking_in = "";
            $reff_picking_out = "";
            $move_id_rm = "";
            $move_id_fg = "";
            $case = "";
            $where = "";
            $i = 1; //set count kode in/out
            $kode_in = "";
            $kode_out = "";
            $kode_bom = "";
            $sql_log_history_mo = "";
            $sql_log_history_in = "";
            $sql_log_history_out = "";
            $arr_kode = [];
            $cek_status = $this->m_procurementOrder->cek_status_procurement_order_items_by_row($kode, addslashes($kode_produk), $row_order)->row_array();

            if ($cek_status['status'] == 'generated') {
                $callback = array('status' => 'failed', 'message' => 'Maaf, Status Product Sudah Generated !', 'icon' => 'fa fa-check', 'type' => 'danger');
                //unlock table
                $this->_module->unlock_tabel();
            } else if (empty($cek_status['status'])) {
                $callback = array('status' => 'failed', 'message' => 'Maaf, Data yang akan Di Generate Kosong !', 'icon' => 'fa fa-check', 'type' => 'danger');
                //unlock table
                $this->_module->unlock_tabel();
            } else if (($qty == 0)) {
                $callback = array('status' => 'failed', 'message' => 'Maaf, Qty tidak boleh 0 !', 'icon' => 'fa fa-check', 'type' => 'danger');
                //unlock table
                $this->_module->unlock_tabel();
            } else {
                //unlock table
                $this->_module->unlock_tabel();

                //lock table
                $this->_module->lock_tabel('wa_group WRITE,wa_template WRITE,wa_send_message WRITE,mst_category WRITE,mst_produk WRITE, mst_produk mp WRITE, mrp_route WRITE, mrp_route as mr WRITE, departemen WRITE, departemen as d WRITE,  stock_move WRITE, stock_move_produk WRITE, penerimaan_barang WRITE, penerimaan_barang_items WRITE, pengiriman_barang WRITE, pengiriman_barang_items WRITE, mrp_production WRITE, mrp_production_rm_target WRITE, mrp_production_fg_target WRITE, bom WRITE, bom_items bi WRITE, bom_items  WRITE, procurement_order WRITE, procurement_order_items WRITE, log_history WRITE, user WRITE, main_menu_sub WRITE');

                /* --Get ROUTE produk by kode_produk-- */
                $jen_route = $this->_module->get_jenis_route_product(addslashes($kode_produk))->row_array();

                $stat_produk = $this->_module->get_status_aktif_by_produk(addslashes($kode_produk))->row_array(); // status produk aktif/tidak

                $produk_route_empty = FALSE;
                $bom_empty = FALSE;
                $generate_produk = FALSE;
                $produk_bom_tidak_aktif = FALSE;
                $nama_produk_arr_bi = '';
                $produk_bom_item_tidak_aktif = FALSE;
                $nama_produk_arr_bi2 = '';
                $nama_produk_empty = '';
                $nama_bom = '';
                $origin = '';
                $arr_bi = array();
                $arr_bi2 = array();
                $bom_aktif = TRUE;

                if (empty($jen_route['route_produksi'])) {//cek route produksi apakah ada ?
                    $callback = array('status' => 'failed', 'message' => 'Maaf, Route Produksi Produk Kosong !', 'icon' => 'fa fa-warning', 'type' => 'danger');

                    //unlock table
                    $this->_module->unlock_tabel();
                } else if ($stat_produk['status_produk'] != 't') {
                    $callback = array('status' => 'failed', 'message' => 'Maaf, Status Produk Tidak Aktif !', 'icon' => 'fa fa-warning', 'type' => 'danger');

                    //unlock table
                    $this->_module->unlock_tabel();
                } else {

                    $last_move = $this->_module->get_kode_stock_move();
                    $move_id = "SM" . $last_move; //Set kode stock_move

                    $last_mo = $this->_module->get_kode_mo();
                    $dgt = substr("00000" . $last_mo, -5);
                    $kode_mo = "MO" . date("y") . date("m") . $dgt;

                    $route_prod = $this->_module->get_route_product($jen_route['route_produksi']);

                    //get total leadtime by route
                    $total_ld = $this->_module->get_total_leadtime($jen_route['route_produksi']);
                    $leadtime = $total_ld;
                    $leadtime_dept = $leadtime;

                    // cek type Procurement Order (Make to order = mto, Makte to stock = mts, Pengiriman = pengiriman)
                    $type_proc = $this->m_procurementOrder->cek_type_procurement_order_by_kode($kode);
                    //cek show_sc =(yes,no)
                    $show_sc = $this->m_procurementOrder->cek_show_sales_order_by_kode($kode);

                    if ($type_proc == 'mto') {

                        foreach ($route_prod as $rp) {

                            //get semua product
                            $tgl = date('Y-m-d H:i:s');
                            $mthd = explode('|', $rp->method);
                            $method_dept = trim($mthd[0]);
                            $method_action = trim($mthd[1]);
                            $dept_id_dari = $rp->dept_id_dari;

                            //$nama_dept        = $this->_module->get_nama_dept_by_kode($method_dept)->row_array();
                            //$product_dept     = ($nama_dept['nama']);
                            //$product_fullname = ($nama_produk);
                            //cek produk by kode_produk
                            $cek_prod2 = $this->_module->cek_produk_by_kode_produk(addslashes($kode_produk))->row_array();

                            if (!empty($cek_prod2['nama_produk'])) {
                                $kode_prod = $cek_prod2['kode_produk'];
                                $nama_prod = $cek_prod2['nama_produk'];
                                $uom = $cek_prod2['uom'];
                                /*
                                  cek bom berdasarkan kode_produk
                                  jika ada
                                  ambil produk di bom items untuk dijadikan kode_prod_rm
                                 */
                                $cek_bom = $this->_module->cek_bom($kode_prod)->row_array();
                                if (!empty($cek_bom['kode_bom'])) {

                                    // cek apa bom aktif atau tidak
                                    if ($cek_bom['status_bom'] == 't') {

                                        $qty_bom = $cek_bom['qty'];

                                        $bi = $this->_module->get_bom_items_by_kode($cek_bom['kode_bom'], $qty_bom, $qty);
                                        $arr_bi = $bi->result_array();

                                        $bi2 = $this->_module->get_bom_items_all_by_kode($cek_bom['kode_bom'], $qty_bom, $qty);
                                        $arr_bi2 = $bi2->result_array();

                                        if (empty($arr_bi) or empty($arr_bi2)) {
                                            $bom_empty = TRUE;
                                        }
                                    } else {
                                        $bom_aktif = FALSE;
                                    }
                                } else {

                                    // cek bom = 1 atau 0
                                    // cek apa produk harus ada bom atau tidak ?
                                    $bom_required = $this->_module->cek_required_bom_by_kode_produk($kode_prod)->row_array();
                                    if ($bom_required['bom'] == 1) { // cek jika bom = 1 atau harus ada bom
                                        $bom_empty = TRUE;
                                    }
                                }

                                $kode_prod_rm = $kode_prod;
                                $nama_prod_rm = $nama_prod;

                                if (!empty($arr_bi)) {

                                    foreach ($arr_bi as $arr_bis) { // cek apakah terdapat produk yang tidak aktif
                                        $stat_produk_bi = $this->_module->get_status_aktif_by_produk(addslashes($arr_bis['kode_produk']))->row_array();
                                        if ($stat_produk_bi['status_produk'] != 't') {
                                            $produk_bom_tidak_aktif = TRUE;
                                            $nama_produk_arr_bi .= $arr_bis['nama_produk'] . ', ';
                                        }
                                    }
                                }

                                if (!empty($arr_bi2)) {

                                    foreach ($arr_bi2 as $arr_bi2s) { // cek apakah terdapat produk yang tidak aktif
                                        $stat_produk_bi2 = $this->_module->get_status_aktif_by_produk(addslashes($arr_bi2s['kode_produk']))->row_array();
                                        if ($stat_produk_bi2['status_produk'] != 't') {
                                            $produk_bom_item_tidak_aktif = TRUE;
                                            $nama_produk_arr_bi2 .= $arr_bi2s['nama_produk'] . ', ';
                                        }
                                    }
                                }
                            } else {
                                $produk_route_empty = TRUE;
                                $generate_produk = FALSE;
                                $nama_produk_empty .= $nama_produk . ', ';
                                break;
                            }

                            if ($bom_empty == TRUE) {
                                $generate_produk = FALSE;
                                $nama_bom .= $nama_prod_rm . ', ';
                                break;
                            }

                            // jika produk bom / bom items  tidak aktif
                            if ($produk_bom_tidak_aktif == TRUE || $produk_bom_item_tidak_aktif == TRUE || $bom_aktif == FALSE) {
                                break;
                            }


                            //jalankan jika produk dan bom nya ada
                            if ($produk_route_empty == FALSE AND $bom_empty == FALSE AND $produk_bom_tidak_aktif == FALSE AND $produk_bom_item_tidak_aktif == FALSE AND $bom_aktif == TRUE) {

                                $generate_produk = TRUE;

                                /* ----------------------------------
                                  Generate Stock Moves
                                  ---------------------------------- */

                                $origin = $sales_order . '|' . $kode_production_order . '|' . $kode . '|' . $row_order;

                                $sql_stock_move_batch .= "('" . $move_id . "','" . $tgl . "','" . $origin . "','" . $rp->method . "','" . $rp->lokasi_dari . "','" . $rp->lokasi_tujuan . "','draft','" . $sm_row . "','" . $source_move . "'), ";

                                $sm_row = $sm_row + 1;

                                if ($method_action == 'OUT') {//Generate Pengiriman
                                    if ($i == "1") {
                                        $arr_kode[$rp->method] = $this->_module->get_kode_pengiriman($method_dept);
                                    } else {
                                        $arr_kode[$rp->method] = $arr_kode[$rp->method] + 1;
                                    }
                                    $dgt = substr("00000" . $arr_kode[$rp->method], -5);
                                    $kode_out = $method_dept . "/" . $method_action . "/" . date("y") . date("m") . $dgt;

                                    $tgl_jt = date('Y-m-d H:i:s', strtotime(-$leadtime_dept . ' days', strtotime($schedule_date)));

                                    $sql_out_batch .= "('" . $kode_out . "','" . $tgl . "','" . $tgl . "','" . $tgl_jt . "','" . addslashes($reff_notes) . "','draft','" . $method_dept . "','" . $origin . "','" . $move_id . "','" . $rp->lokasi_dari . "','" . $rp->lokasi_tujuan . "'), ";
                                    $sql_out_items_batch .= "('" . $kode_out . "','" . addslashes($kode_prod_rm) . "','" . addslashes($nama_prod_rm) . "','" . $qty . "','" . addslashes($uom) . "','draft','1',''), ";

                                    //simpan ke stock move produk 
                                    $sql_stock_move_produk_batch .= "('" . $move_id . "','" . addslashes($kode_prod_rm) . "','" . addslashes($nama_prod_rm) . "','" . $qty . "','" . addslashes($uom) . "','draft','1',''), ";

                                    $source_move = $move_id;

                                    //get mms kode berdasarkan dept_id
                                    $mms = $this->_module->get_kode_sub_menu_deptid('pengirimanbarang', $method_dept)->row_array();
                                    if (!empty($mms['kode'])) {
                                        $mms_kode = $mms['kode'];
                                    } else {
                                        $mms_kode = '';
                                    }

                                    //create log history pengiriman_barang
                                    $note_log = $kode_out . ' | ' . $origin;
                                    $date_log = date('Y-m-d H:i:s');
                                    $sql_log_history_out .= "('" . $date_log . "','" . $mms_kode . "','" . $kode_out . "','create','" . $note_log . "','" . $nama_user . "'), ";
                                } elseif ($method_action == 'IN') {//Generete Penerimaan
                                    if ($i == "1") {
                                        $arr_kode[$rp->method] = $this->_module->get_kode_penerimaan($method_dept);
                                    } else {
                                        $arr_kode[$rp->method] = $arr_kode[$rp->method] + 1;
                                    }
                                    $dgt = substr("00000" . $arr_kode[$rp->method], -5);
                                    $kode_in = $method_dept . "/" . $method_action . "/" . date("y") . date("m") . $dgt;

                                    $tgl_jt = date('Y-m-d H:i:s', strtotime(-$leadtime_dept . ' days', strtotime($schedule_date)));

                                    $reff_picking_in = $kode_out . "|" . $kode_in;
                                    $sql_in_batch .= "('" . $kode_in . "','" . $tgl . "','" . $tgl . "','" . $tgl_jt . "','" . addslashes($reff_notes) . "','draft','" . $method_dept . "','" . $origin . "','" . $move_id . "','" . $reff_picking_in . "','" . $rp->lokasi_dari . "','" . $rp->lokasi_tujuan . "'), ";

                                    $in_row = 1;
                                    foreach ($arr_bi as $in) {
                                        $sql_in_items_batch .= "('" . $kode_in . "','" . addslashes($in['kode_produk']) . "','" . addslashes($in['nama_produk']) . "','" . $in['qty_bom_items'] . "','" . addslashes($in['uom']) . "','draft','" . $in_row . "'), ";

                                        //simpan ke stock move produk 
                                        $sql_stock_move_produk_batch .= "('" . $move_id . "','" . addslashes($in['kode_produk']) . "','" . addslashes($in['nama_produk']) . "','" . $in['qty_bom_items'] . "','" . addslashes($in['uom']) . "','draft','" . $in_row . "',''), ";
                                        $in_row = $in_row + 1;
                                    }

                                    $reff_picking_out = $kode_out . "|" . $kode_in;
                                    $case .= "when kode = '" . $kode_out . "' then '" . $reff_picking_out . "'";
                                    $where .= "'" . $kode_out . "',";

                                    $kode_out = "";
                                    $source_move = $move_id;

                                    //get mms kode berdasarkan dept_id
                                    $mms = $this->_module->get_kode_sub_menu_deptid('penerimaanbarang', $method_dept)->row_array();
                                    if (!empty($mms['kode'])) {
                                        $mms_kode = $mms['kode'];
                                    } else {
                                        $mms_kode = '';
                                    }

                                    //create log history penerimaan_barang
                                    $note_log = $kode_in . ' | ' . $origin;
                                    $date_log = date('Y-m-d H:i:s');
                                    $sql_log_history_in .= "('" . $date_log . "','" . $mms_kode . "','" . $kode_in . "','create','" . addslashes($note_log) . "','" . $nama_user . "'), ";
                                } elseif ($method_action == 'CON') {
                                    $source_move = "";

                                    //get move id rm target
                                    $move_id_rm = $move_id;
                                    $kode_prod_rm_target = $kode_prod_rm;
                                    $nama_prod_rm_target = $nama_prod_rm;
                                    $qty_rm_target = $qty;
                                    $uom_rm_target = $uom;

                                    $con_row = 1;
                                    foreach ($arr_bi2 as $con) {
                                        //simpan ke stock move produk 
                                        $origin_prod = $con['kode_produk'] . '_' . $con_row;
                                        $sql_stock_move_produk_batch .= "('" . $move_id . "','" . addslashes($con['kode_produk']) . "','" . addslashes($con['nama_produk']) . "','" . $con['qty_bom_items'] . "','" . addslashes($con['uom']) . "','draft','" . $con_row . "','" . addslashes($origin_prod) . "'), ";
                                        $con_row = $con_row + 1;
                                    }
                                } elseif ($method_action == 'PROD') {// generate mo/mg
                                    $source_move = $move_id;

                                    /* ----------------------------------
                                      Generate MO / MG
                                      ---------------------------------- */

                                    $move_id_fg = $move_id;
                                    $kode_prod_fg_target = $kode_prod_rm;
                                    $nama_prod_fg_target = $nama_prod_rm;
                                    $qty_fg_target = $qty;
                                    $uom_fg_target = $uom;

                                    $cek_bom = $this->_module->cek_bom($kode_prod_rm)->row_array();
                                    $kode_bom = $cek_bom['kode_bom'];

                                    $ld_dept = $this->_module->get_leadtime_by_dept($method_dept)->row_array();
                                    $leadtime_dept = $ld_dept['manf_leadtime'];

                                    $tgl_jt = date('Y-m-d H:i:s', strtotime(-$leadtime_dept . ' days', strtotime($schedule_date)));

                                    //$source_location = $method_dept."/Stock";
                                    $loc = $this->_module->get_nama_dept_by_kode($method_dept)->row_array();
                                    $location = $loc['stock_location'];
                                    //sql simpan mrp_production
                                    $sql_mrp_prod_batch .= "('" . $kode_mo . "','" . $tgl . "','" . $origin . "','" . addslashes($kode_prod_rm) . "','" . addslashes($nama_prod_rm) . "','" . $qty . "','" . addslashes($uom) . "','" . $tgl_jt . "','" . addslashes($reff_notes) . "','" . $kode_bom . "','" . $tgl . "','" . $tgl . "','" . $location . "','" . $location . "','" . $method_dept . "','draft','','" . $nama_user . "','','','',''), ";

                                    //get mms kode berdasarkan dept_id
                                    $mms = $this->_module->get_kode_sub_menu_deptid('mO', $method_dept)->row_array();
                                    if (!empty($mms['kode'])) {
                                        $mms_kode = $mms['kode'];
                                    } else {
                                        $mms_kode = '';
                                    }

                                    //create log history MO
                                    $note_log = $kode_mo . ' | ' . $nama_prod_rm . ' | ' . $qty . ' ' . $uom;
                                    $date_log = date('Y-m-d H:i:s');
                                    $sql_log_history_mo .= "('" . $date_log . "','" . $mms_kode . "','" . $kode_mo . "','create','" . addslashes($note_log) . "','" . $nama_user . "'), ";

                                    $rm_row = 1;
                                    foreach ($arr_bi2 as $rm) {
                                        //sql simpan mrp production rm target
                                        $origin_prod = $rm['kode_produk'] . '_' . $rm_row;
                                        $sql_mrp_prod_rm_batch .= "('" . $kode_mo . "','" . $move_id_rm . "','" . addslashes($rm['kode_produk']) . "','" . addslashes($rm['nama_produk']) . "','" . $rm['qty_bom_items'] . "','" . addslashes($rm['uom']) . "','" . $rm_row . "','" . addslashes($origin_prod) . "','draft','" . addslashes($rm['note']) . "'), ";
                                        $rm_row = $rm_row + 1;
                                    }

                                    //sql simpan mrp production fg target
                                    $sql_mrp_prod_fg_batch .= "('" . $kode_mo . "','" . $move_id_fg . "','" . addslashes($kode_prod_fg_target) . "','" . addslashes($nama_prod_fg_target) . "','" . $qty_fg_target . "','" . addslashes($uom_fg_target) . "','1','draft'), ";

                                    //sql simpan stock move produk
                                    $sql_stock_move_produk_batch .= "('" . $move_id_fg . "','" . addslashes($kode_prod_fg_target) . "','" . addslashes($nama_prod_fg_target) . "','" . $qty_fg_target . "','" . addslashes($uom_fg_target) . "','draft','1',''), ";

                                    // $last_bom  = $last_bom + 1;
                                    $last_mo = $last_mo + 1;
                                }

                                $dgt = substr("00000" . $last_mo, -5);
                                $kode_mo = "MO" . date("y") . date("m") . $dgt;

                                $last_move = $last_move + 1;
                                $move_id = "SM" . $last_move;
                            }//end if produk dan bom nya ada

                            $arr_bi = array();
                            $arr_bi2 = array();
                        }//end foreach route produksi
                    } else if ($type_proc == 'mts') {

                        foreach ($route_prod as $rp) {

                            //get semua product
                            $tgl = date('Y-m-d H:i:s');
                            $mthd = explode('|', $rp->method);
                            $method_dept = trim($mthd[0]);
                            $method_action = trim($mthd[1]);
                            $dept_id_dari = $rp->dept_id_dari;

                            //$nama_dept        = $this->_module->get_nama_dept_by_kode($method_dept)->row_array();
                            //$product_dept     = ($nama_dept['nama']);
                            //$product_fullname = ($nama_produk);
                            //cek produk by kode_produk
                            $cek_prod2 = $this->_module->cek_produk_by_kode_produk(addslashes($kode_produk))->row_array();

                            if (!empty($cek_prod2['nama_produk'])) {
                                $kode_prod = $cek_prod2['kode_produk'];
                                $nama_prod = $cek_prod2['nama_produk'];
                                $uom = $cek_prod2['uom'];
                                /*
                                  cek bom berdasarkan kode_produk
                                  jika ada
                                  ambil produk di bom items untuk dijadikan kode_prod_rm
                                 */
                                $cek_bom = $this->_module->cek_bom($kode_prod)->row_array();
                                if (!empty($cek_bom['kode_bom'])) {

                                    if ($cek_bom['status_bom'] == 't') {

                                        $qty_bom = $cek_bom['qty'];

                                        $bi = $this->_module->get_bom_items_by_kode($cek_bom['kode_bom'], $qty_bom, $qty);
                                        $arr_bi = $bi->result_array();

                                        $bi2 = $this->_module->get_bom_items_all_by_kode($cek_bom['kode_bom'], $qty_bom, $qty);
                                        $arr_bi2 = $bi2->result_array();

                                        if (empty($arr_bi) or empty($arr_bi2)) {
                                            $bom_empty = TRUE;
                                        }
                                    } else {
                                        $bom_aktif = FALSE;
                                    }
                                } else {

                                    // cek bom = 1 atau 0
                                    // cek apa produk harus ada bom atau tidak ?
                                    $bom_required = $this->_module->cek_required_bom_by_kode_produk($kode_prod)->row_array();
                                    if ($bom_required['bom'] == 1) { // cek jika bom = 1 atau harus ada bom
                                        $bom_empty = TRUE;
                                    }
                                }

                                $kode_prod_rm = $kode_prod;
                                $nama_prod_rm = $nama_prod;

                                if (!empty($arr_bi)) {

                                    foreach ($arr_bi as $arr_bis) { // cek apakah terdapat produk yang tidak aktif
                                        $stat_produk_bi = $this->_module->get_status_aktif_by_produk(addslashes($arr_bis['kode_produk']))->row_array();
                                        if ($stat_produk_bi['status_produk'] != 't') {
                                            $produk_bom_tidak_aktif = TRUE;
                                            $nama_produk_arr_bi .= $arr_bis['nama_produk'] . ', ';
                                        }
                                    }
                                }

                                if (!empty($arr_bi2)) {

                                    foreach ($arr_bi2 as $arr_bi2s) { // cek apakah terdapat produk yang tidak aktif
                                        $stat_produk_bi2 = $this->_module->get_status_aktif_by_produk(addslashes($arr_bi2s['kode_produk']))->row_array();
                                        if ($stat_produk_bi2['status_produk'] != 't') {
                                            $produk_bom_item_tidak_aktif = TRUE;
                                            $nama_produk_arr_bi2 .= $arr_bi2s['nama_produk'] . ', ';
                                        }
                                    }
                                }
                            } else {
                                $produk_route_empty = TRUE;
                                $generate_produk = FALSE;
                                $nama_produk_empty .= $nama_produk . ', ';
                                break;
                            }

                            if ($bom_empty == TRUE) {
                                $generate_produk = FALSE;
                                $nama_bom .= $nama_prod_rm . ', ';
                                break;
                            }

                            // jika produk bom / bom items  tidak aktif
                            if ($produk_bom_tidak_aktif == TRUE || $produk_bom_item_tidak_aktif == TRUE || $bom_aktif == FALSE) {
                                break;
                            }


                            //jalankan jika produk dan bom nya ada
                            if ($produk_route_empty == FALSE AND $bom_empty == FALSE AND $produk_bom_tidak_aktif == FALSE AND $produk_bom_item_tidak_aktif == FALSE AND $bom_aktif == TRUE) {

                                $generate_produk = TRUE;
                                if ($show_sc == 'yes') {
                                    $origin = $sales_order . '|' . $kode_production_order . '|' . $kode . '|MTS|' . $row_order;
                                } else {
                                    $origin = $kode . '|MTS|' . $row_order;
                                }


                                if ($method_action == 'CON') {

                                    $sql_stock_move_batch .= "('" . $move_id . "','" . $tgl . "','" . $origin . "','" . $rp->method . "','" . $rp->lokasi_dari . "','" . $rp->lokasi_tujuan . "','draft','" . $sm_row . "','" . $source_move . "'), ";
                                    $sm_row = $sm_row + 1;

                                    $source_move = "";

                                    //get move id rm target
                                    $move_id_rm = $move_id;
                                    $kode_prod_rm_target = $kode_prod_rm;
                                    $nama_prod_rm_target = $nama_prod_rm;
                                    $qty_rm_target = $qty;
                                    $uom_rm_target = $uom;

                                    $con_row = 1;
                                    foreach ($arr_bi2 as $con) {
                                        //simpan ke stock move produk 
                                        $origin_prod = $con['kode_produk'] . '_' . $con_row;
                                        $sql_stock_move_produk_batch .= "('" . $move_id . "','" . addslashes($con['kode_produk']) . "','" . addslashes($con['nama_produk']) . "','" . $con['qty_bom_items'] . "','" . addslashes($con['uom']) . "','draft','" . $con_row . "','" . addslashes($origin_prod) . "'), ";
                                        $con_row = $con_row + 1;
                                    }
                                } elseif ($method_action == 'PROD') {// generate mo/mg
                                    $sql_stock_move_batch .= "('" . $move_id . "','" . $tgl . "','" . $origin . "','" . $rp->method . "','" . $rp->lokasi_dari . "','" . $rp->lokasi_tujuan . "','draft','" . $sm_row . "','" . $source_move . "'), ";
                                    $sm_row = $sm_row + 1;

                                    $source_move = $move_id;

                                    $move_id_fg = $move_id;
                                    $kode_prod_fg_target = $kode_prod_rm;
                                    $nama_prod_fg_target = $nama_prod_rm;
                                    $qty_fg_target = $qty;
                                    $uom_fg_target = $uom;

                                    $cek_bom = $this->_module->cek_bom($kode_prod_rm)->row_array();
                                    $kode_bom = $cek_bom['kode_bom'];

                                    $ld_dept = $this->_module->get_leadtime_by_dept($method_dept)->row_array();
                                    $leadtime_dept = $ld_dept['manf_leadtime'];

                                    $tgl_jt = date('Y-m-d H:i:s', strtotime(-$leadtime_dept . ' days', strtotime($schedule_date)));

                                    //$source_location = $method_dept."/Stock";
                                    $loc = $this->_module->get_nama_dept_by_kode($method_dept)->row_array();
                                    $location = $loc['stock_location'];
                                    //sql simpan mrp_production
                                    $sql_mrp_prod_batch .= "('" . $kode_mo . "','" . $tgl . "','" . $origin . "','" . addslashes($kode_prod_rm) . "','" . addslashes($nama_prod_rm) . "','" . $qty . "','" . addslashes($uom) . "','" . $tgl_jt . "','" . addslashes($reff_notes) . "','" . $kode_bom . "','" . $tgl . "','" . $tgl . "','" . $location . "','" . $location . "','" . $method_dept . "','draft','','" . $nama_user . "','','','',''), ";

                                    //get mms kode berdasarkan dept_id
                                    $mms = $this->_module->get_kode_sub_menu_deptid('mO', $method_dept)->row_array();
                                    if (!empty($mms['kode'])) {
                                        $mms_kode = $mms['kode'];
                                    } else {
                                        $mms_kode = '';
                                    }

                                    //create log history MO
                                    $note_log = $kode_mo . ' | ' . $nama_prod_rm . ' | ' . $qty . ' ' . $uom;
                                    $date_log = date('Y-m-d H:i:s');
                                    $sql_log_history_mo .= "('" . $date_log . "','" . $mms_kode . "','" . $kode_mo . "','create','" . addslashes($note_log) . "','" . $nama_user . "'), ";

                                    $rm_row = 1;
                                    foreach ($arr_bi2 as $rm) {
                                        //sql simpan mrp production rm target
                                        $origin_prod = $rm['kode_produk'] . '_' . $rm_row;
                                        $sql_mrp_prod_rm_batch .= "('" . $kode_mo . "','" . $move_id_rm . "','" . addslashes($rm['kode_produk']) . "','" . addslashes($rm['nama_produk']) . "','" . $rm['qty_bom_items'] . "','" . addslashes($rm['uom']) . "','" . $rm_row . "','" . addslashes($origin_prod) . "','draft','" . addslashes($rm['note']) . "'), ";
                                        $rm_row = $rm_row + 1;
                                    }

                                    //sql simpan mrp production fg target
                                    $sql_mrp_prod_fg_batch .= "('" . $kode_mo . "','" . $move_id_fg . "','" . addslashes($kode_prod_fg_target) . "','" . addslashes($nama_prod_fg_target) . "','" . $qty_fg_target . "','" . addslashes($uom_fg_target) . "','1','draft'), ";

                                    //sql simpan stock move produk
                                    $sql_stock_move_produk_batch .= "('" . $move_id_fg . "','" . addslashes($kode_prod_fg_target) . "','" . addslashes($nama_prod_fg_target) . "','" . $qty_fg_target . "','" . addslashes($uom_fg_target) . "','draft','1',''), ";

                                    // $last_bom  = $last_bom + 1;
                                    $last_mo = $last_mo + 1;
                                }

                                $dgt = substr("00000" . $last_mo, -5);
                                $kode_mo = "MO" . date("y") . date("m") . $dgt;

                                $last_move = $last_move + 1;
                                $move_id = "SM" . $last_move;
                            }

                            $arr_bi = array();
                            $arr_bi2 = array();
                        }
                    } else if ($type_proc == 'pengiriman') {

                        foreach ($route_prod as $rp) {

                            $tgl = date('Y-m-d H:i:s');
                            $mthd = explode('|', $rp->method);
                            $method_dept = trim($mthd[0]);
                            $method_action = trim($mthd[1]);
                            $dept_id_dari = $rp->dept_id_dari;

                            if ($method_action == 'OUT') {//Generate Pengiriman
                                $generate_produk = TRUE;

                                if ($show_sc == 'yes') {
                                    $origin = $sales_order . '|' . $kode_production_order . '|' . $kode . '|OUT|' . $row_order;
                                } else {
                                    $origin = $kode . '|OUT|' . $row_order;
                                }

                                $sql_stock_move_batch .= "('" . $move_id . "','" . $tgl . "','" . $origin . "','" . $rp->method . "','" . $rp->lokasi_dari . "','" . $rp->lokasi_tujuan . "','draft','" . $sm_row . "','" . $source_move . "'), ";
                                $sm_row = $sm_row + 1;

                                if ($i == "1") {
                                    $arr_kode[$rp->method] = $this->_module->get_kode_pengiriman($method_dept);
                                } else {
                                    $arr_kode[$rp->method] = $arr_kode[$rp->method] + 1;
                                }
                                $dgt = substr("00000" . $arr_kode[$rp->method], -5);
                                $kode_out = $method_dept . "/" . $method_action . "/" . date("y") . date("m") . $dgt;

                                $tgl_jt = date('Y-m-d H:i:s', strtotime(-$leadtime_dept . ' days', strtotime($schedule_date)));

                                $sql_out_batch .= "('" . $kode_out . "','" . $tgl . "','" . $tgl . "','" . $tgl_jt . "','" . addslashes($reff_notes) . "','draft','" . $method_dept . "','" . $origin . "','" . $move_id . "','" . $rp->lokasi_dari . "','" . $rp->lokasi_tujuan . "'), ";
                                $sql_out_items_batch .= "('" . $kode_out . "','" . addslashes($kode_produk) . "','" . addslashes($nama_produk) . "','" . $qty . "','" . addslashes($uom) . "','draft','1',''), ";

                                //simpan ke stock move produk 
                                $sql_stock_move_produk_batch .= "('" . $move_id . "','" . addslashes($kode_produk) . "','" . addslashes($nama_produk) . "','" . $qty . "','" . addslashes($uom) . "','draft','1',''), ";

                                $source_move = $move_id;

                                //get mms kode berdasarkan dept_id
                                $mms = $this->_module->get_kode_sub_menu_deptid('pengirimanbarang', $method_dept)->row_array();
                                if (!empty($mms['kode'])) {
                                    $mms_kode = $mms['kode'];
                                } else {
                                    $mms_kode = '';
                                }

                                //create log history pengiriman_barang
                                $note_log = $kode_out . ' | ' . $origin;
                                $date_log = date('Y-m-d H:i:s');
                                $sql_log_history_out .= "('" . $date_log . "','" . $mms_kode . "','" . $kode_out . "','create','" . $note_log . "','" . $nama_user . "'), ";

                                $last_move = $last_move + 1;
                                $move_id = "SM" . $last_move;
                            }
                        }// end foreach route 
                    }

                    //jika GENERATE produk TRUE 
                    if ($generate_produk == TRUE) {

                        if (!empty($sql_stock_move_batch)) {
                            $sql_stock_move_batch = rtrim($sql_stock_move_batch, ', ');
                            $this->_module->create_stock_move_batch($sql_stock_move_batch);

                            if (!empty($sql_stock_move_produk_batch)) {
                                $sql_stock_move_produk_batch = rtrim($sql_stock_move_produk_batch, ', ');
                                $this->_module->create_stock_move_produk_batch($sql_stock_move_produk_batch);
                            }
                        }

                        if (!empty($sql_out_batch)) {
                            $sql_out_batch = rtrim($sql_out_batch, ', ');
                            $this->_module->simpan_pengiriman_batch($sql_out_batch);

                            if (!empty($sql_out_items_batch)) {
                                $sql_out_items_batch = rtrim($sql_out_items_batch, ', ');
                                $this->_module->simpan_pengiriman_items_batch($sql_out_items_batch);
                            }
                            $sql_log_history_out = rtrim($sql_log_history_out, ', ');
                            $this->_module->simpan_log_history_batch($sql_log_history_out);
                        }

                        if (!empty($sql_in_batch)) {
                            $sql_in_batch = rtrim($sql_in_batch, ', ');
                            $this->_module->simpan_penerimaan_batch($sql_in_batch);

                            if (!empty($sql_in_items_batch)) {
                                $sql_in_items_batch = rtrim($sql_in_items_batch, ', ');
                                $this->_module->simpan_penerimaan_items_batch($sql_in_items_batch);
                            }

                            $where = rtrim($where, ',');
                            $sql_update_reff_out_batch = "UPDATE pengiriman_barang SET reff_picking =(case " . $case . " end) WHERE  kode in (" . $where . ") ";
                            $this->_module->update_reff_batch($sql_update_reff_out_batch);

                            $sql_log_history_in = rtrim($sql_log_history_in, ', ');
                            $this->_module->simpan_log_history_batch($sql_log_history_in);
                        }

                        if (!empty($sql_mrp_prod_batch)) {
                            $sql_mrp_prod_batch = rtrim($sql_mrp_prod_batch, ', ');
                            $this->_module->simpan_mrp_production_batch($sql_mrp_prod_batch);

                            if (!empty($sql_mrp_prod_rm_batch)) {
                                $sql_mrp_prod_rm_batch = rtrim($sql_mrp_prod_rm_batch, ', ');
                                $this->_module->simpan_mrp_production_rm_target_batch($sql_mrp_prod_rm_batch);
                            }

                            if (!empty($sql_mrp_prod_fg_batch)) {
                                $sql_mrp_prod_fg_batch = rtrim($sql_mrp_prod_fg_batch, ', ');
                                $this->_module->simpan_mrp_production_fg_target_batch($sql_mrp_prod_fg_batch);
                            }

                            $sql_log_history_mo = rtrim($sql_log_history_mo, ', ');
                            $this->_module->simpan_log_history_batch($sql_log_history_mo);
                        }

                        if (!empty($product_supp_row)) {
                            $origin2 = $sales_order . '|' . $kode_production_order . '|' . $product_supp_row;
                            //$lokasi_tujuan = $warehouse.'/Stock';
                            $loc = $this->_module->get_nama_dept_by_kode($warehouse)->row_array();
                            $lokasi_tujuan = $loc['stock_location'];
                            $kd_in = $this->_module->get_kode_in_by_origin($lokasi_tujuan, $origin2)->row_array();
                            if (!empty($kd_in['kode'])) {
                                $reff_picking = $kode_out . '|' . $kd_in['kode'];
                                $sql_update_reff_out = "UPDATE pengiriman_barang SET reff_picking ='$reff_picking' WHERE  kode = '$kode_out'";
                                $this->_module->update_reff_batch($sql_update_reff_out);
                            }
                        }

                        if ($type_proc == 'pengiriman' || $type_proc == 'mto') {

                            //Start Method IN baru setelah route produksi di atas

                            $sql_stock_move_batch = "";
                            $sql_stock_move_produk_batch = "";
                            $sql_in_batch = "";
                            $sql_in_items_batch = "";
                            $where = '';
                            $case = '';
                            $sql_log_history_in = "";

                            $last_move = $this->_module->get_kode_stock_move();
                            $move_id = "SM" . $last_move; //Set kode stock_move
                            /*
                              $method_dept = $warehouse;//WRD
                              $nama_dept        = $this->_module->get_nama_dept_by_kode($method_dept)->row_array();
                              $product_fullname = ($nama_produk);
                              $cek_prod2 = $this->_module->cek_nama_product(addslashes($product_fullname))->row_array();//get kode_produk

                              if(!empty($cek_prod2['nama_produk'])){
                              $kode_produk  = ($cek_prod2['kode_produk']);
                              $kode_prod_rm = ($kode_prod);
                              $nama_prod_rm = ($product_fullname);
                              }
                             */

                            /* ----------------------------------
                              Generate Stock Moves
                              ---------------------------------- */
                            $method_action = 'IN';
                            $method = $warehouse . '|' . $method_action;
                            //$lokasi_dari   = 'Transit Location';
                            //$lokasi_tujuan = $warehouse.'/Stock';
                            $method_dept = $warehouse;

                            $output_location = $this->_module->get_output_location_by_kode($dept_id_dari)->row_array();
                            $lokasi_dari = $output_location['output_location']; // ex : Transit Location GRG
                            $stock_location = $this->_module->get_nama_dept_by_kode($warehouse)->row_array(); // ex : warehouse/stock
                            $lokasi_tujuan = $stock_location['stock_location'];

                            //$origin = $sales_order.'|'.$kode_production_order.'|'.$kode.'|'.$row_order; 

                            $sql_stock_move_batch .= "('" . $move_id . "','" . $tgl . "','" . $origin . "','" . $method . "','" . $lokasi_dari . "','" . $lokasi_tujuan . "','draft','" . $sm_row . "','" . $source_move . "'), ";

                            if ($i == "1") {
                                $arr_kode[$rp->method] = $this->_module->get_kode_penerimaan($method_dept);
                            } else {
                                $arr_kode[$rp->method] = $arr_kode[$rp->method] + 1;
                            }
                            $dgt = substr("00000" . $arr_kode[$rp->method], -5);
                            $kode_in = $method_dept . "/" . $method_action . "/" . date("y") . date("m") . $dgt;

                            $ld_dept = $this->_module->get_leadtime_by_dept($method_dept)->row_array();
                            $leadtime = $leadtime - $ld_dept['manf_leadtime'];
                            $leadtime_dept = $leadtime;

                            $tgl_jt = date('Y-m-d H:i:s', strtotime(-$leadtime_dept . ' days', strtotime($schedule_date)));

                            if (empty($kode_out)) {// jika terdapat route out nya ga ada maka In terakhir di reff_picking ditambahkan dept_id departemen sebelumnya (MO) contoh route Tricot
                                $kode_out_asli = $dept_id_dari;
                            } else {
                                $kode_out_asli = $kode_out;
                            }

                            // $tgl_jt    = date('Y-m-d H:i:s');
                            $reff_picking_in = $kode_out_asli . "|" . $kode_in;
                            $sql_in_batch .= "('" . $kode_in . "','" . $tgl . "','" . $tgl . "','" . $tgl_jt . "','" . addslashes($reff_notes) . "','draft','" . $method_dept . "','" . $origin . "','" . $move_id . "','" . $reff_picking_in . "','" . $lokasi_dari . "','" . $lokasi_tujuan . "'), ";

                            $in_row = 1;
                            $sql_in_items_batch .= "('" . $kode_in . "','" . addslashes($kode_produk) . "','" . addslashes($nama_produk) . "','" . $qty . "','" . addslashes($uom) . "','draft','" . $in_row . "'), ";

                            //get mms kode berdasarkan dept_id
                            $mms = $this->_module->get_kode_sub_menu_deptid('penerimaanbarang', $method_dept)->row_array();
                            if (!empty($mms['kode'])) {
                                $mms_kode = $mms['kode'];
                            } else {
                                $mms_kode = '';
                            }

                            //create log history penerimaan_barang
                            $note_log = $kode_in . ' | ' . $origin;
                            $date_log = date('Y-m-d H:i:s');
                            $sql_log_history_in .= "('" . $date_log . "','" . $mms_kode . "','" . $kode_in . "','create','" . $note_log . "','" . $nama_user . "'), ";

                            //simpan ke stock move produk 
                            $sql_stock_move_produk_batch .= "('" . $move_id . "','" . addslashes($kode_produk) . "','" . addslashes($nama_produk) . "','" . $qty . "','" . addslashes($uom) . "','draft','" . $in_row . "',''), ";
                            $in_row = $in_row + 1;

                            $reff_picking_out = $kode_out . "|" . $kode_in;
                            $case .= "when kode = '" . $kode_out . "' then '" . $reff_picking_out . "'";
                            $where .= "'" . $kode_out . "',";

                            if (!empty($sql_stock_move_batch)) {
                                $sql_stock_move_batch = rtrim($sql_stock_move_batch, ', ');
                                $this->_module->create_stock_move_batch($sql_stock_move_batch);

                                $sql_stock_move_produk_batch = rtrim($sql_stock_move_produk_batch, ', ');
                                $this->_module->create_stock_move_produk_batch($sql_stock_move_produk_batch);
                            }

                            if (!empty($sql_in_batch)) {
                                $sql_in_batch = rtrim($sql_in_batch, ', ');
                                $this->_module->simpan_penerimaan_batch($sql_in_batch);

                                $sql_in_items_batch = rtrim($sql_in_items_batch, ', ');
                                $this->_module->simpan_penerimaan_items_batch($sql_in_items_batch);

                                if (!empty($case) AND !empty($where)) {
                                    $where = rtrim($where, ',');
                                    $sql_update_reff_out_batch = "UPDATE pengiriman_barang SET reff_picking =(case " . $case . " end) WHERE  kode in (" . $where . ") ";
                                    $this->_module->update_reff_batch($sql_update_reff_out_batch);
                                }

                                $sql_log_history_in = rtrim($sql_log_history_in, ', ');
                                $this->_module->simpan_log_history_batch($sql_log_history_in);
                            }

                            //finish method IN baru
                        }

                        //update detail items jadi generate
                        $this->m_procurementOrder->update_status_procurement_order_items($kode, $row_order, $status);

//                        if.sd ad as

                        $cek_details = $this->m_procurementOrder->cek_status_procurement_order_items($kode, '')->num_rows();

                        $where_status = "AND status NOT IN ('generated','cancel')";
                        $cek_details_status = $this->m_procurementOrder->cek_status_procurement_order_items($kode, $where_status)->num_rows();

                        if ($cek_details == 0) {
                            $this->m_procurementOrder->update_status_procurement_order($kode, 'draft');
                        } else if ($cek_details > 0) {
                            if ($cek_details_status == 0) {
                                $this->m_procurementOrder->update_status_procurement_order($kode, 'done');
                            } else {
                                $this->m_procurementOrder->update_status_procurement_order($kode, 'draft');
                            }
                        }


                        $jenis_log = "generate";
                        $note_log = "Generated | " . $kode . " | " . $nama_produk . " | " . $row_order;
                        $this->_module->gen_history($sub_menu, $kode, $jenis_log, addslashes($note_log), $username);
                        $callback = array('status' => 'success', 'message' => 'Generate Data Berhasil !', 'icon' => 'fa fa-check', 'type' => 'success');
                        $pesan = ["{mo}" => ($kode_mo ?? ""), "{origin}" => ($origin ?? "")];
                        $this->sendWa($kode, $row_order, $head->warehouse, $pesan);
                    }// end if cek produk generate

                    if ($produk_route_empty == TRUE OR $bom_empty == TRUE OR $generate_produk == FALSE OR $produk_bom_tidak_aktif == TRUE OR $produk_bom_item_tidak_aktif == TRUE OR $bom_aktif == FALSE) {
                        if ($produk_route_empty == TRUE) {
                            $callback = array('status' => 'failed', 'message' => 'Maaf, Produk Kosong !', 'icon' => 'fa fa-warning', 'type' => 'danger');
                        } else if ($bom_empty == TRUE) {
                            $callback = array('status' => 'failed', 'message' => 'Maaf, Bill of Materials (BOM) Kosong !', 'icon' => 'fa fa-warning', 'type' => 'danger');
                        } else if ($bom_aktif == FALSE) {
                            $callback = array('status' => 'failed', 'message' => 'Maaf, Bill of Materials (BOM) Tidak Aktif !', 'icon' => 'fa fa-warning', 'type' => 'danger');
                        } else if ($produk_bom_tidak_aktif == TRUE) {
                            $nama_produk_arr_bi = rtrim($nama_produk_arr_bi, ', ');
                            $callback = array('status' => 'failed', 'message' => 'Maaf, Produk BOM ' . $nama_produk_arr_bi . ' Tidak Aktif !', 'icon' => 'fa fa-warning', 'type' => 'danger');
                        } else if ($produk_bom_item_tidak_aktif == TRUE) {
                            $nama_produk_arr_bi2 = rtrim($nama_produk_arr_bi2, ', ');
                            $callback = array('status' => 'failed', 'message' => 'Maaf, Produk BOM Items ' . $nama_produk_arr_bi2 . ' Tidak Aktif !', 'icon' => 'fa fa-warning', 'type' => 'danger');
                        } else {
                            $callback = array('status' => 'failed', 'message' => 'Maaf, Generate Data Gagal !', 'icon' => 'fa fa-warning', 'type' => 'danger');
                        }
                    }

                    //unlock table
                    $this->_module->unlock_tabel();
                }//end if cek route produksi
            }
            echo json_encode($callback);
        }
    }

    public function batal_detail_procurement_order() {
        if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
            // session habis
            $callback = array('message' => 'Waktu Anda Telah Habis', 'sesi' => 'habis');
        } else {
            $noMo = [];
            $sub_menu = $this->uri->segment(2);
            $username = addslashes($this->session->userdata('username'));
            $nu = $this->_module->get_nama_user($username)->row_array();
            $nama_user = addslashes($nu['nama']);

            $kode = $this->input->post('kode');
            $row = $this->input->post('row_order');
            //$data   = explode("^|",$row);
            // get data items by row 
            $d_items = $this->m_procurementOrder->get_data_items_by_row($kode, $row)->row_array();
            $row_order = $d_items['row_order'];
            $kode_produk = addslashes($d_items['kode_produk']);
            $nama_produk = addslashes($d_items['nama_produk']);

            $head = $this->m_procurementOrder->get_data_by_code($kode);
            $sales_order = $head->sales_order;
            $kode_prod = $head->kode_prod;

            // cek type Procurement Order (Make to order = mto, Makte to stock = mts, Pengiriman = pengiriman)
            $type_proc = $this->m_procurementOrder->cek_type_procurement_order_by_kode($kode);
            //cek show_sc =(yes,no)
            $show_sc = $this->m_procurementOrder->cek_show_sales_order_by_kode($kode);
            if ($type_proc == 'mto') {
                $origin = $sales_order . '|' . $kode_prod . '|' . $kode . '|' . $row_order;
            } else if ($type_proc == 'mts') {
                if ($show_sc == 'yes') {
                    $origin = $sales_order . '|' . $kode_prod . '|' . $kode . '|MTS|' . $row_order;
                } else {
                    $origin = $kode . '|MTS|' . $row_order;
                }
            } else if ($type_proc == 'pengiriman') {
                if ($show_sc == 'yes') {
                    $origin = $sales_order . '|' . $kode_prod . '|' . $kode . '|OUT|' . $row_order;
                } else {
                    $origin = $kode . '|OUT|' . $row_order;
                }
            }

            $cek_status = $this->m_procurementOrder->cek_status_procurement_order_items_by_row($kode, addslashes($kode_produk), $row_order)->row_array();

            if ($cek_status['status'] == 'cancel') {
                $callback = array('status' => 'failed', 'message' => 'Maaf, Status Product Sudah Batal !', 'icon' => 'fa fa-warning', 'type' => 'danger');
            } else if (empty($cek_status['kode_produk'])) {
                $callback = array('status' => 'failed', 'message' => 'Maaf, Data yang akan Di Batalkan Kosong !', 'icon' => 'fa fa-warning', 'type' => 'danger');
            } else {

                //lock table
                $this->_module->lock_tabel('wa_group WRITE,wa_template WRITE,wa_send_message WRITE,mst_category WRITE,mst_produk WRITE,procurement_order WRITE, procurement_order_items WRITE, stock_move WRITE, stock_move_items WRITE, stock_move_produk WRITE, mrp_production WRITE, mrp_production_rm_hasil WRITE, mrp_production_rm_target WRITE, mrp_production_fg_target WRITE, mrp_production_fg_hasil WRITE, pengiriman_barang WRITE, pengiriman_barang_items WRITE, penerimaan_barang WRITE, penerimaan_barang_items WRITE, user WRITE, main_menu_sub WRITE, log_history WRITE');

                //select stock_move by origin row order move id
                //$mrp = true;
                $update_stock_move = false;
                $batal_item = false;
                $status_cancel = "cancel";
                //mrp_production
                $case = "";
                $where = "";
                //pengiriman_barang
                $case2 = "";
                $where2 = "";
                //penerimaan_barang
                $case3 = "";
                $where3 = "";
                //stock move, stock_move_items, stock_move_produk
                $case4 = "";
                $where4 = "";
                $date_log = date('Y-m-d H:i:s');
                $sql_log_history = "";

                $list_sm = $this->_module->get_list_stock_move_by_origin($origin);
                foreach ($list_sm as $row) {

                    $batal_item = true;

                    $ex_mt = explode('|', $row->method);
                    $method_dept = $ex_mt[0];
                    $method_action = $ex_mt[1]; //ex CON/PROD/OUT/IN
                    $origin = $row->origin;
                    $move_id = $row->move_id;

                    if (( $method_action == 'CON' OR $method_action == 'PROD')) {
                        $log_mrp = false;
                        // cek status mrp_production ?
                        $status = "AND status NOT IN ('done','cancel')";
                        $cek_mrp = $this->_module->cek_status_mrp_productin_by_origin($origin, $method_dept, $status)->result_array();
                        foreach ($cek_mrp as $mrp) {

                            if (!empty($mrp['kode'])) {//bearti status MO = ready/draft
                                //update status = cancel mrp_production, mrp_production_rm_target, mrp_production_fg_target
                                $case .= "when kode = '" . $mrp['kode'] . "' then '" . $status_cancel . "'";
                                $where .= "'" . $mrp['kode'] . "',";

                                $log_mrp = true;
                                $update_stock_move = true;
                                $kode_mrp = $mrp['kode'];
                                if (!in_array($mrp['kode'], $noMo)) {
                                    $noMo[] = $mrp['kode'];
                                }
                            }
                        }
                        if ($log_mrp == true) {

                            //get mms kode berdasarkan dept_id
                            $mms = $this->_module->get_kode_sub_menu_deptid('mO', $method_dept)->row_array();
                            if (!empty($mms['kode'])) {
                                $mms_kode = $mms['kode'];
                            } else {
                                $mms_kode = '';
                            }

                            // create log history mrp_production
                            $note_log = 'Batal MO ' . $method_action . ' | ' . $kode_mrp;
                            $sql_log_history .= "('" . $date_log . "','" . $mms_kode . "','" . $kode_mrp . "','cancel','" . $note_log . "','" . $nama_user . "'), ";
                        }
                    } elseif ($method_action == 'OUT') {

                        // cek status pengiriman barang
                        $status = "AND status NOT IN ('done','cancel')";
                        $cek_out = $this->_module->cek_status_pengiriman_barang_by_move_id($origin, $move_id, $status)->row_array();

                        if (!empty($cek_out['kode'])) {//bearti pengiriman_barang = ready/draft 
                            //update status = cancel pengiriman_barang, pengiriman_barang_items
                            $case2 .= " when kode = '" . $cek_out['kode'] . "' then '" . $status_cancel . "'";
                            $where2 .= "'" . $cek_out['kode'] . "',";

                            //get mms kode berdasarkan dept_id
                            $mms = $this->_module->get_kode_sub_menu_deptid('pengirimanbarang', $method_dept)->row_array();
                            if (!empty($mms['kode'])) {
                                $mms_kode = $mms['kode'];
                            } else {
                                $mms_kode = '';
                            }

                            // create log history pengiriman_barang
                            $note_log = 'Batal Pengiriman Barang | ' . $cek_out['kode'];
                            $sql_log_history .= "('" . $date_log . "','" . $mms_kode . "','" . $cek_out['kode'] . "','cancel','" . $note_log . "','" . $nama_user . "'), ";

                            $update_stock_move = true;
                        }
                    } elseif ($method_action == 'IN') {

                        // cek status penerimaan barang
                        $status = "AND status NOT IN ('done','cancel')";
                        $cek_in = $this->_module->cek_status_penerimaan_barang_by_move_id($origin, $move_id, $status)->row_array();

                        if (!empty($cek_in['kode'])) {//bearti penerimaan_barang = ready/draft
                            //update status = cancel penerimaan_barang, penerimaan_barang_items
                            $case3 .= " when kode = '" . $cek_in['kode'] . "' then '" . $status_cancel . "'";
                            $where3 .= "'" . $cek_in['kode'] . "',";

                            //get mms kode berdasarkan dept_id
                            $mms = $this->_module->get_kode_sub_menu_deptid('penerimaanbarang', $method_dept)->row_array();
                            if (!empty($mms['kode'])) {
                                $mms_kode = $mms['kode'];
                            } else {
                                $mms_kode = '';
                            }

                            // create log history penerimaan barang
                            $note_log = 'Batal Penerimaan Barang | ' . $cek_in['kode'];
                            $sql_log_history .= "('" . $date_log . "','" . $mms_kode . "','" . $cek_in['kode'] . "','cancel','" . $note_log . "','" . $nama_user . "'), ";

                            $update_stock_move = true;
                        }
                    }

                    if ($update_stock_move == true) {

                        //update status = cancel stock move, stock_move_items, stock_move_produk
                        $case4 .= " when move_id = '" . $move_id . "' then '" . $status_cancel . "'";
                        $where4 .= "'" . $move_id . "',";
                    }

                    $update_stock_move = false;
                }// end foreach stock_move

                if ($batal_item == true) {

                    //update mrp_production, mrp_production_rm_target, mrp_production_fg_target
                    if (!empty($case) AND !empty($where)) {

                        // update mrp_production
                        $where = rtrim($where, ',');
                        $sql_update_mrp_production = "UPDATE mrp_production SET status =(case " . $case . " end) WHERE  kode in (" . $where . ") ";
                        $this->_module->update_reff_batch($sql_update_mrp_production);

                        // update mrp_production_rm_target
                        $sql_update_mrp_production_rm_target = "UPDATE mrp_production_rm_target SET status =(case " . $case . " end) WHERE  kode in (" . $where . ") AND status NOT IN ('done')";
                        $this->_module->update_reff_batch($sql_update_mrp_production_rm_target);

                        // update mrp_production_fg_target 
                        $sql_update_mrp_production_fg_target = "UPDATE mrp_production_fg_target SET status =(case " . $case . " end) WHERE  kode in (" . $where . ") AND status NOT IN ('done') ";
                        $this->_module->update_reff_batch($sql_update_mrp_production_fg_target);
                    }

                    //update pengiriman_barang, pengiriman_barang_items
                    if (!empty($case2) AND !empty($where2)) {

                        //update pengiriman_barang
                        $where2 = rtrim($where2, ',');
                        $sql_update_pengiriman_barang = "UPDATE pengiriman_barang SET status =(case " . $case2 . " end) WHERE  kode in (" . $where2 . ") ";
                        $this->_module->update_reff_batch($sql_update_pengiriman_barang);

                        // update pengiriman_barang_items
                        $sql_update_pengiriman_barang_items = "UPDATE pengiriman_barang_items SET status_barang =(case " . $case2 . " end) WHERE  kode in (" . $where2 . ") ";
                        $this->_module->update_reff_batch($sql_update_pengiriman_barang_items);
                    }

                    //update penerimaan_barang, penerimaan_barang_items
                    if (!empty($case3) AND !empty($where3)) {

                        //update penerimaan_barang
                        $where3 = rtrim($where3, ',');
                        $sql_update_penerimaan_barang = "UPDATE penerimaan_barang SET status =(case " . $case3 . " end) WHERE  kode in (" . $where3 . ") ";
                        $this->_module->update_reff_batch($sql_update_penerimaan_barang);

                        // update penerimaan_barang_items
                        $sql_update_penerimaan_barang_items = "UPDATE penerimaan_barang_items SET status_barang =(case " . $case3 . " end) WHERE  kode in (" . $where3 . ") ";
                        $this->_module->update_reff_batch($sql_update_penerimaan_barang_items);
                    }

                    //update stock move, stock_move_items, stock_move_produk
                    if (!empty($case4) AND !empty($where4)) {

                        // update stock_move
                        $where4 = rtrim($where4, ',');
                        $sql_update_stock_move = "UPDATE stock_move SET status =(case " . $case4 . " end) WHERE  move_id in (" . $where4 . ") ";
                        $this->_module->update_reff_batch($sql_update_stock_move);

                        // update stock_move_items
                        $sql_update_stock_move_items = "UPDATE stock_move_items SET status =(case " . $case4 . " end) WHERE  move_id in (" . $where4 . ")  AND status NOT IN ('done') ";
                        $this->_module->update_reff_batch($sql_update_stock_move_items);

                        // update stock_move_produk
                        $sql_update_stock_move_produk = "UPDATE stock_move_produk SET status =(case " . $case4 . " end) WHERE  move_id in (" . $where4 . ") AND status NOT IN ('done') ";
                        $this->_module->update_reff_batch($sql_update_stock_move_produk);
                    }

                    $jenis_log = "cancel";
                    $note_log = "Batal Items | " . $kode . " | " . $nama_produk . " | " . $row_order;
                    $this->_module->gen_history($sub_menu, $kode, $jenis_log, addslashes($note_log), $username);

                    //create log history setiap yg batal
                    if (!empty($sql_log_history)) {
                        $sql_log_history = rtrim($sql_log_history, ', ');
                        $this->_module->simpan_log_history_batch($sql_log_history);
                    }


                    //update detail items jadi cancel
                    $this->m_procurementOrder->update_status_procurement_order_items($kode, $row_order, $status_cancel);

                    $cek_details = $this->m_procurementOrder->cek_status_procurement_order_items($kode, '')->num_rows();

                    $where_status = "AND status NOT IN ('cancel')";
                    $cek_details_status = $this->m_procurementOrder->cek_status_procurement_order_items($kode, $where_status)->num_rows();

                    $where_status2 = "AND status NOT IN ('generated','cancel')";
                    $cek_details_status2 = $this->m_procurementOrder->cek_status_procurement_order_items($kode, $where_status2)->num_rows();

                    if ($cek_details > 0) {
                        if ($cek_details_status == 0) {
                            $this->m_procurementOrder->update_status_procurement_order($kode, 'cancel');
                        } else if ($cek_details_status2 > 0) {
                            $this->m_procurementOrder->update_status_procurement_order($kode, 'draft');
                        }
                    }
                }//end if batal_items == true

                if ($batal_item == false) {
                    $callback = array('status' => 'failed', 'message' => 'Procurement Order Items Gagal Dibatalkan !', 'icon' => 'fa fa-warning', 'type' => 'danger');
                } else {
                    $callback = array('status' => 'success', 'message' => 'Procurement Order Items Berhasil Dibatalkan !', 'icon' => 'fa fa-check', 'type' => 'success');
                    if (count($noMo) > 0)
                        $this->sendWa($kode, $row_order, $head->warehouse, ["{mo}" => implode(",", $noMo), "{origin}" => ($origin ?? "")]);
                }

                //unlock table
                $this->_module->unlock_tabel();
            }
        }

        echo json_encode($callback);
    }

    public function batal_waste_procurement_order() {
        if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
            // session habis
            $callback = array('message' => 'Waktu Anda Telah Habis', 'sesi' => 'habis');
        } else {
            $sub_menu = $this->uri->segment(2);
            $username = addslashes($this->session->userdata('username'));
            $nu = $this->_module->get_nama_user($username)->row_array();
            $nama_user = addslashes($nu['nama']);

            $kode = $this->input->post('kode');
            $kode_produk = $this->input->post('kode_produk');
            $row_order = $this->input->post('row_order');
            $origin = $this->input->post('origin');

            $cek_status = $this->m_procurementOrder->cek_status_procurement_order_items_by_row($kode, addslashes($kode_produk), $row_order)->row_array();

            if ($cek_status['status'] == 'cancel') {
                $callback = array('status' => 'failed', 'message' => 'Maaf, Status Product Sudah Batal !', 'icon' => 'fa fa-warning', 'type' => 'danger');
            } else {

                // lock tabel
                $this->_module->lock_tabel('pengiriman_barang WRITE, pengiriman_barang_items WRITE, penerimaan_barang WRITE, penerimaan_barang_items WRITE, stock_move WRITE, stock_move_items WRITE, stock_move_produk WRITE, user WRITE, main_menu_sub WRITE, log_history WRITE');

                //$mrp = true;
                $update_stock_move = false;
                $batal_item = false;
                $status_cancel = "cancel";
                //pengiriman_barang
                $case = "";
                $where = "";
                //penerimaan_barang
                $case2 = "";
                $where2 = "";
                //stock move, stock_move_items, stock_move_produk
                $case3 = "";
                $where3 = "";

                $date_log = date('Y-m-d H:i:s');
                $sql_log_history = "";

                //get list stock_move_by_origin
                $list_sm = $this->_module->get_list_stock_move_by_origin($origin);
                foreach ($list_sm as $row) {


                    $ex_mt = explode('|', $row->method);
                    $method_dept = $ex_mt[0];
                    $method_action = $ex_mt[1]; // CON/PROD/OUT/IN
                    $origin = $row->origin;
                    $move_id = $row->move_id;

                    if ($method_action == 'OUT') {

                        // cek status pengiriman barang
                        $status = "AND status NOT IN ('done','cancel')";
                        $cek_out = $this->_module->cek_status_pengiriman_barang_by_move_id($origin, $move_id, $status)->row_array();

                        if (!empty($cek_out['kode'])) {

                            // cek qty_target
                            $qty_target = $this->_module->get_qty_target_pengiriman_barang_by_kode($cek_out['kode'])->row_array();

                            //cek qty_tersedia
                            $qty_tersedia = $this->_module->get_qty_tersedia_stock_move_items_by_move_id($move_id)->row_array();

                            if ($qty_target['qty_target'] > $qty_tersedia['qty_tersedia']) {

                                $batal_item = true;

                                //update status = cancel pengiriman_barang, pengiriman_barang_items
                                $case .= " when kode = '" . $cek_out['kode'] . "' then '" . $status_cancel . "'";
                                $where .= "'" . $cek_out['kode'] . "',";

                                //get mms kode berdasarkan dept_id
                                $mms = $this->_module->get_kode_sub_menu_deptid('pengirimanbarang', $method_dept)->row_array();
                                if (!empty($mms['kode'])) {
                                    $mms_kode = $mms['kode'];
                                } else {
                                    $mms_kode = '';
                                }

                                // create log history pengiriman_barang
                                $note_log = 'Batal Waste Pengiriman Barang | ' . $cek_out['kode'];
                                $sql_log_history .= "('" . $date_log . "','" . $mms_kode . "','" . $cek_out['kode'] . "','cancel','" . $note_log . "','" . $nama_user . "'), ";

                                $update_stock_move = true;
                            }
                        }
                    } elseif ($method_action == 'IN') {


                        // cek status penerimaan barang
                        $status = "AND status NOT IN ('done','cancel')";
                        $cek_in = $this->_module->cek_status_penerimaan_barang_by_move_id($origin, $move_id, $status)->row_array();

                        if (!empty($cek_in['kode'])) {

                            // cek qty_target
                            $qty_target = $this->_module->get_qty_target_penerimaan_barang_by_kode($cek_in['kode'])->row_array();

                            //cek qty_tersedia
                            $qty_tersedia = $this->_module->get_qty_tersedia_stock_move_items_by_move_id($move_id)->row_array();

                            //$qty_in = $qty_target['qty_target'].' - '.$qty_tersedia['qty_tersedia'];

                            if ($qty_target['qty_target'] > $qty_tersedia['qty_tersedia']) {

                                $batal_item = true;

                                //update status = cancel penerimaan_barang, penerimaan_barang_items
                                $case2 .= " when kode = '" . $cek_in['kode'] . "' then '" . $status_cancel . "'";
                                $where2 .= "'" . $cek_in['kode'] . "',";

                                //get mms kode berdasarkan dept_id
                                $mms = $this->_module->get_kode_sub_menu_deptid('penerimaanbarang', $method_dept)->row_array();
                                if (!empty($mms['kode'])) {
                                    $mms_kode = $mms['kode'];
                                } else {
                                    $mms_kode = '';
                                }

                                // create log history penerimaan_barang
                                $note_log = 'Batal Waste Penerimaan Barang | ' . $cek_in['kode'];
                                $sql_log_history .= "('" . $date_log . "','" . $mms_kode . "','" . $cek_in['kode'] . "','cancel','" . $note_log . "','" . $nama_user . "'), ";

                                $update_stock_move = true;
                            }
                        }
                    }


                    if ($update_stock_move == true) {
                        //update status = cancel stock move, stock_move_items, stock_move_produk
                        $case3 .= " when move_id = '" . $move_id . "' then '" . $status_cancel . "'";
                        $where3 .= "'" . $move_id . "',";
                    }

                    $update_stock_move = false;
                } // end foreach list_sm

                if ($batal_item == true) {

                    //update pengiriman_barang, pengiriman_barang_items
                    if (!empty($case) AND !empty($where)) {

                        //update pengiriman_barang
                        $where = rtrim($where, ',');
                        $sql_update_pengiriman_barang = "UPDATE pengiriman_barang SET status =(case " . $case . " end) WHERE  kode in (" . $where . ") ";
                        $this->_module->update_reff_batch($sql_update_pengiriman_barang);

                        // update pengiriman_barang_items
                        $sql_update_pengiriman_barang_items = "UPDATE pengiriman_barang_items SET status_barang =(case " . $case . " end) WHERE  kode in (" . $where . ") ";
                        $this->_module->update_reff_batch($sql_update_pengiriman_barang_items);
                    }


                    //update penerimaan_barang, penerimaan_barang_items
                    if (!empty($case2) AND !empty($where2)) {

                        //update penerimaan_barang
                        $where2 = rtrim($where2, ',');
                        $sql_update_penerimaan_barang = "UPDATE penerimaan_barang SET status =(case " . $case2 . " end) WHERE  kode in (" . $where2 . ") ";
                        $this->_module->update_reff_batch($sql_update_penerimaan_barang);

                        // update penerimaan_barang_items
                        $sql_update_penerimaan_barang_items = "UPDATE penerimaan_barang_items SET status_barang =(case " . $case2 . " end) WHERE  kode in (" . $where2 . ") ";
                        $this->_module->update_reff_batch($sql_update_penerimaan_barang_items);
                    }


                    //update stock move, stock_move_items, stock_move_produk
                    if (!empty($case3) AND !empty($where3)) {

                        // update stock_move
                        $where3 = rtrim($where3, ',');
                        $sql_update_stock_move = "UPDATE stock_move SET status =(case " . $case3 . " end) WHERE  move_id in (" . $where3 . ") ";
                        $this->_module->update_reff_batch($sql_update_stock_move);

                        // update stock_move_items
                        $sql_update_stock_move_items = "UPDATE stock_move_items SET status =(case " . $case3 . " end) WHERE  move_id in (" . $where3 . ") ";
                        $this->_module->update_reff_batch($sql_update_stock_move_items);

                        // update stock_move_produk
                        $sql_update_stock_move_produk = "UPDATE stock_move_produk SET status =(case " . $case3 . " end) WHERE  move_id in (" . $where3 . ") ";
                        $this->_module->update_reff_batch($sql_update_stock_move_produk);
                    }


                    $jenis_log = "cancel";
                    $note_log = "Batal Waste Items | " . $kode . " | " . $kode_produk . " | " . $row_order;
                    $this->_module->gen_history($sub_menu, $kode, $jenis_log, addslashes($note_log), $username);

                    //create log history setiap yg batal
                    if (!empty($sql_log_history)) {
                        $sql_log_history = rtrim($sql_log_history, ', ');
                        $this->_module->simpan_log_history_batch($sql_log_history);
                    }
                } // end if $batal_item = true;

                if ($batal_item == false) {
                    $callback = array('status' => 'failed', 'message' => 'Batal Produk Waste Gagal Dibatalkan !', 'icon' => 'fa fa-warning', 'type' => 'danger');
                } else {
                    $callback = array('status' => 'success', 'message' => 'Batal Produk Waste Berhasil Dibatalkan !', 'icon' => 'fa fa-check', 'type' => 'success');
                }
                //unlock table 
                $this->_module->unlock_tabel();
            }
        }

        echo json_encode($callback);
    }

    protected function sendWa($kode, $row_order, $tujuan, $data_pesan = []) {
        try {
            $check = new $this->m_gtp;
            $data = [];
            if (in_array(strtolower($tujuan), ["jac", "wrd", "wrp", "tws"])) {
                $data = $check->setTables("procurement_order_items")->setOrder(["procurement_order_items.row_order"])
                                ->setJoins("mst_produk", "mst_produk.kode_produk = procurement_order_items.kode_produk")
                                ->setJoins("mst_category", "mst_category.id = mst_produk.id_category")
                                ->setWheres(["kode_proc" => $kode, 'row_order' => $row_order])
                                ->setWhereRaw("mst_category.id in ('2','3','4','5','19')")->getDetail();
                $groups = $this->config->item('additional_wa_bc_jac') ?? [];
            } else if (in_array(strtolower($tujuan), ["tri"])) {
                $data = $check->setTables("procurement_order_items")->setOrder(["procurement_order_items.row_order"])
                                ->setJoins("mst_produk", "mst_produk.kode_produk = procurement_order_items.kode_produk")
                                ->setJoins("mst_category", "mst_category.id = mst_produk.id_category")
                                ->setWheres(["kode_proc" => $kode, 'row_order' => $row_order])
                                ->setWhereRaw("mst_category.id in ('3')")->getDetail();
                $groups = $this->config->item('additional_wa_bc_tri') ?? [];
            }
            if (count($data) > 0) {

                if ($data->status === "generated") {
                    $pesan = "*{$data_pesan["{mo}"]}* BARU (WARPING DASAR)";
                    $template = "proc_order_create";
                } else if ($data->status === "cancel") {
                    $pesan = "*{$data_pesan["{mo}"]}* BATAL (WARPING DASAR)";
                    $template = "proc_order_cancel";
                }
                $data_pesan = array_merge($data_pesan, ["{judul}" => $pesan, "{produk}" => $data->nama_produk, "{qty}" => (number_format($data->qty, 2, ",", ".") . " " . $data->uom), "{reff_notes}" => $data->reff_notes]);
                $wa = new $this->wa_message;
                $wa->sendMessageToGroup($template, $data_pesan, $groups)
                        ->setMentions([])->setFooter('footer_hms')->send();
            }
        } catch (Exception $ex) {
            log_message('error', $ex->getMessage());
        }
    }
}
