<?php

defined('BASEPATH') or exit('No Direct Script Acces Allowed');
/**
 * 
 */
require FCPATH . 'vendor/autoload.php';

use Mpdf\Mpdf;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\DummyPrintConnector;

class Penerimaanbarang extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->is_loggedin(); //cek apakah user sudah login
        $this->load->model("_module"); //load model global
        $this->load->model("m_penerimaanBarang"); ///load model penerimaan barang
        $this->load->model("m_mo");
        $this->load->model('m_po');
        $this->config->load('additional');
        $this->load->library("token");
        $this->load->model("m_global");
    }

    public function index() {
        $kode_sub = 'mm_warehouse';
        $username = $this->session->userdata('username');
        $row = $this->_module->sub_menu_default($kode_sub, $username)->row_array();
        redirect($row['link_menu']);
    }

    public function Receiving() {
        $data['id_dept'] = 'RCV';
        $this->load->view('warehouse/v_penerimaan_barang', $data);
    }

    public function Gudangbenang() {
        $data['id_dept'] = 'GDB';
        $this->load->view('warehouse/v_penerimaan_barang', $data);
    }

    public function Twisting() {
        $data['id_dept'] = 'TWS';
        $this->load->view('warehouse/v_penerimaan_barang', $data);
    }

    public function Warpingdasar() {
        $data['id_dept'] = 'WRD';
        $this->load->view('warehouse/v_penerimaan_barang', $data);
    }

    public function Warpingpanjang() {
        $data['id_dept'] = 'WRP';
        $this->load->view('warehouse/v_penerimaan_barang', $data);
    }

    public function Tricot() {
        $data['id_dept'] = 'TRI';
        $this->load->view('warehouse/v_penerimaan_barang', $data);
    }

    public function Jacquard() {
        $data['id_dept'] = 'JAC';
        $this->load->view('warehouse/v_penerimaan_barang', $data);
    }

    public function Raschel() {
        $data['id_dept'] = 'RSC';
        $this->load->view('warehouse/v_penerimaan_barang', $data);
    }

    public function Cuttingshearing() {
        $data['id_dept'] = 'CS';
        $this->load->view('warehouse/v_penerimaan_barang', $data);
    }

    public function Inspecting() {
        $data['id_dept'] = 'INS1';
        $this->load->view('warehouse/v_penerimaan_barang', $data);
    }

    public function Greige() {
        $data['id_dept'] = 'GRG';
        $this->load->view('warehouse/v_penerimaan_barang', $data);
    }

    public function Dyeing() {
        $data['id_dept'] = 'DYE';
        $this->load->view('warehouse/v_penerimaan_barang', $data);
    }

    public function Finishing() {
        $data['id_dept'] = 'FIN';
        $this->load->view('warehouse/v_penerimaan_barang', $data);
    }

    public function Brushing() {
        $data['id_dept'] = 'BRS';
        $this->load->view('warehouse/v_penerimaan_barang', $data);
    }

    public function Finbrushing() {
        $data['id_dept'] = 'FBR';
        $this->load->view('warehouse/v_penerimaan_barang', $data);
    }

    public function Padding() {
        $data['id_dept'] = 'PAD';
        $this->load->view('warehouse/v_penerimaan_barang', $data);
    }

    public function Setting() {
        $data['id_dept'] = 'SET';
        $this->load->view('warehouse/v_penerimaan_barang', $data);
    }

    public function Inspecting2() {
        $data['id_dept'] = 'INS2';
        $this->load->view('warehouse/v_penerimaan_barang', $data);
    }

    public function Gudangjadi() {
        $data['id_dept'] = 'GJD';
        $this->load->view('warehouse/v_penerimaan_barang', $data);
    }

    public function IT() {
        $data['id_dept'] = 'GIT';
        $this->load->view('warehouse/v_penerimaan_barang', $data);
    }

    public function Sparepart() {
        $data['id_dept'] = 'GSP';
        $this->load->view('warehouse/v_penerimaan_barang', $data);
    }

    public function Packingmaterial() {
        $data['id_dept'] = 'GPM';
        $this->load->view('warehouse/v_penerimaan_barang', $data);
    }

    public function Umum() {
        $data['id_dept'] = 'GUM';
        $this->load->view('warehouse/v_penerimaan_barang', $data);
    }

    public function ATK() {
        $data['id_dept'] = 'GATK';
        $this->load->view('warehouse/v_penerimaan_barang', $data);
    }

    public function Gudangobat() {
        $data['id_dept'] = 'GOB';
        $this->load->view('warehouse/v_penerimaan_barang', $data);
    }

    function limit_words($string, $awal_start, $awal_length, $akhir_start, $akhir_length) {

        //$jml_kata = str_word_count($string);

        $words = explode(" ", $string);
        $word_awal = implode(" ", array_splice($words, $awal_start, $awal_length));
        $word_akhir = implode(" ", array_splice($words, $akhir_start, $akhir_length));
        return $word_awal . ' [...] ' . $word_akhir;
    }

    public function get_data() {

        $sub_menu = $this->uri->segment(2);
        $id_dept = $this->input->post('id_dept');
        $kode = $this->_module->get_kode_sub_menu_deptid($sub_menu, $id_dept)->row_array();
        $list = $this->m_penerimaanBarang->get_datatables($id_dept, $kode['kode']);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $field) {
            //$kode_encrypt = $this->encryption->encrypt($field->kode);
            $kode_encrypt = encrypt_url($field->kode);
            if (str_word_count($field->reff_note) > 75) {
                $reff_note = $this->limit_words($field->reff_note, 0, 3, -37, 37);
            } else {
                $reff_note = $field->reff_note;
            }

            $no++;
            $row = array();
            $row[] = $no;
            $row[] = '<a href="' . base_url('warehouse/penerimaanbarang/edit/' . $kode_encrypt) . '">' . $field->kode . '</a>';
            $row[] = $field->tanggal;
            $row[] = $field->tanggal_transaksi;
            $row[] = $field->origin;
            $row[] = $field->lokasi_tujuan;
            $row[] = $field->reff_picking;
            $row[] = $field->nama_partner;
            $row[] = $reff_note;
            $row[] = $field->nama_status;

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_penerimaanBarang->count_all($id_dept, $kode['kode']),
            "recordsFiltered" => $this->m_penerimaanBarang->count_filtered($id_dept, $kode['kode']),
            "data" => $data,
        );
        //output dalam format JSON
        echo json_encode($output);
    }

    public function edit($kode = null) {
        if (!isset($kode))
            show_404();
        $kode_decrypt = decrypt_url($kode);
        $list = $this->m_penerimaanBarang->get_data_by_code($kode_decrypt);
        $data["list"] = $list;
        $data["items"] = $this->m_penerimaanBarang->get_list_penerimaan_barang($kode_decrypt);
        $move = $this->m_penerimaanBarang->get_stock_move_by_kode($kode_decrypt)->row_array();
        $data['smove'] = $move;
        $data['smi'] = $this->m_penerimaanBarang->get_stock_move_items_by_kode($kode_decrypt);
        $data['show_lebar'] = $this->_module->cek_show_lebar_by_dept_id($list->dept_id)->row_array();

        // cek apakah benar move_id_next adalah CON
        $method = $list->dept_id . '|CON';
        $cek_move_mrp = $this->m_penerimaanBarang->cek_move_id_by_kode($list->origin, $method)->row_array();
        if (!empty($cek_move_mrp)) {
            $data['mo'] = $this->m_penerimaanBarang->get_kode_mrp_by_move_id($cek_move_mrp['move_id'])->row_array();
        } else {
            $data['mo'] = '';
        }

        // cek priv akses menu
        $sub_menu = $this->uri->segment(2);
        $username = $this->session->userdata('username');
        $kode = $this->_module->get_kode_sub_menu_deptid($sub_menu, $list->dept_id)->row_array();
        $data['akses_menu'] = $this->_module->cek_priv_menu_by_user($username, $kode['kode'])->num_rows();

        // cek level akses by user
        $level_akses = $this->_module->get_level_akses_by_user($username)->row_array();
        // cek departemen by user
        $cek_dept = $this->_module->cek_departemen_by_user($username)->row_array();

        if ($level_akses['level'] == 'Administrator' or $level_akses['level'] == 'Super Administrator') {
            $data['show_delete'] = true;
        } else if (strpos($cek_dept['dept'], 'PPIC') !== false) {
            $data['show_delete'] = true;
        } else {
            $data['show_delete'] = false;
        }

        // cek type mo
        $data['type_mo'] = $this->_module->cek_type_mo_by_dept($list->dept_id);

        if (empty($data["list"])) {
            show_404();
        } else {
            return $this->load->view('warehouse/v_penerimaan_barang_edit', $data);
        }
    }

    public function edit_barcode($kode = null) {
        if (!isset($kode))
            show_404();
        $kode_decrypt = decrypt_url($kode);
        $list = $this->m_penerimaanBarang->get_data_by_code($kode_decrypt);
        $data["list"] = $list;
        $smi = $this->m_penerimaanBarang->get_move_id_by_kode($kode_decrypt)->row_array();
        $data["move_id"] = $smi;
        $data['items'] = $this->m_penerimaanBarang->get_stock_move_items_by_kode($kode_decrypt);
        $data['count'] = $this->m_penerimaanBarang->get_count_valid_scan_by_kode($kode_decrypt);
        $data['count_all'] = $this->m_penerimaanBarang->get_count_all_scan_by_kode($smi['move_id']);

        // cek priv akses menu
        $sub_menu = $this->uri->segment(2);
        $username = $this->session->userdata('username');
        $kode = $this->_module->get_kode_sub_menu_deptid($sub_menu, $list->dept_id)->row_array();
        $data['akses_menu'] = $this->_module->cek_priv_menu_by_user($username, $kode['kode'])->num_rows();

        if (empty($data["list"])) {
            show_404();
        } else {
            return $this->load->view('warehouse/v_penerimaan_barang_edit_barcode', $data);
        }
    }

    public function simpan() {
        $kode = $this->input->post('kode');
        $tgl_transaksi = $this->input->post('tgl_transaksi');
        $reff_note = addslashes($this->input->post('reff_note'));
        $move_id = $this->input->post('move_id');
        $deptid = $this->input->post('deptid');
        $no_sj = addslashes($this->input->post('no_sj'));
        $tgl_sj = $this->input->post('tgl_sj');

        $sub_menu = $this->uri->segment(2);
        $username = addslashes($this->session->userdata('username'));

        if (empty($this->session->userdata('status'))) { //cek apakah session masih ada
            // session habis
            $callback = array('message' => 'Waktu Anda Telah Habis', 'sesi' => 'habis');
        } else {
            //cek status terkirim ?
            $cek_kirim = $this->m_penerimaanBarang->cek_status_barang($kode)->row_array();
            if ($cek_kirim['status'] == 'done') {
                if ($deptid !== 'RCV') {
                    $callback = array('status' => 'ada', 'message' => 'Maaf, Data Tidak Bisa Disimpan, Data Sudah Terkirim !', 'icon' => 'fa fa-warning', 'type' => 'danger');
                } else {
                    $model = new $this->m_global;
                    $model->setTables("penerimaan_barang")->setWheres(["kode" => $kode])->update(["no_sj" => $no_sj, "tanggal_sj" => $tgl_sj]);
                    $this->_module->gen_history_deptid($sub_menu, $kode, "edit", "-> no SJ " . $no_sj . " tanggal SJ " . $tgl_sj, $username, $deptid);

                    $inv = $model->setTables("invoice")->setWheres(["origin" => $kode, "status <>" => "cancel"], true)->getDetail();
                    if ($inv) {
                        $model->update(["no_sj_supp" => $no_sj, "tanggal_sj" => $tgl_sj]);
                        $this->_module->gen_history("invoice", $inv->id, "edit", "-> no SJ " . $no_sj . " tanggal SJ " . $tgl_sj, $username);
                    }
                    //
                    $callback = array('status' => 'success', 'message' => 'Data Berhasil Disimpan !', 'icon' => 'fa fa-check', 'type' => 'success');
                }
            } else if ($cek_kirim['status'] == 'cancel') {
                $callback = array('status' => 'ada', 'message' => 'Maaf, Data Tidak Bisa Disimpan, Data Penerimaan Sudah dibatalkan !', 'icon' => 'fa fa-warning', 'type' => 'danger');
            } else {
                if (empty($reff_note)) {
                    $callback = array('status' => 'failed', 'field' => 'reff_note', 'message' => 'Reff Note Harus Diisi !', 'icon' => 'fa fa-warning', 'type' => 'danger');
                } else {
                    $this->m_penerimaanBarang->update_penerimaan_barang($kode, $reff_note, $no_sj, $tgl_sj);
                    $callback = array('status' => 'success', 'message' => 'Data Berhasil Disimpan !', 'icon' => 'fa fa-check', 'type' => 'success');
                    $jenis_log = "edit";
                    $note_log = "-> " . $no_sj . " " . $tgl_sj . " " . $reff_note;
                    $this->_module->gen_history_deptid($sub_menu, $kode, $jenis_log, $note_log, $username, $deptid);
                }
            }
        }

        echo json_encode($callback);
    }

    public function kirim_barang() {

        try {

            if (empty($this->session->userdata('status'))) { //cek apakah session masih ada
                // session habis
                throw new \Exception('Waktu Anda Telah Habis', 401);
            } else {

                $kode = $this->input->post('kode');
                $move_id = $this->input->post('move_id');
                $deptid = $this->input->post('deptid');
                $origin = $this->input->post('origin');
                $method = $this->input->post('method');
                $mode = $this->input->post('mode'); // scan mode / list mode
                $tgl = date('Y-m-d H:i:s');
                $sql_stock_move_items_batch = "";
                $status_done = 'done';
                $case = "";
                $where = "";
                $case2 = "";
                $where2 = "";
                $case3 = "";
                $where3 = "";
                $case3x = "";
                $where3x = "";
                $case4 = "";
                $where4 = "";
                $case6 = "";
                $where6 = "";
                $case8 = "";
                $where8 = "";
                $whereMo = "";
                $whereQuant = "";
                $data_smi = [];
                $data_stock_quant = [];

                if ($deptid == 'RCV') {
                    $status_back_order = 'ready';
                } else {
                    $status_back_order = 'draft';
                }

                $sub_menu = $this->uri->segment(2);
                $username = addslashes($this->session->userdata('username'));
                $nu = $this->_module->get_nama_user($username)->row_array();
                $nama_user = $nu['nama'];

                // start transaction
                $this->_module->startTransaction();

                //lock table
                $lockTable = 'stock_move WRITE, stock_move_produk WRITE, stock_move_items WRITE, '
                        . ' stock_quant WRITE, departemen d WRITE, pengiriman_barang WRITE, log_history WRITE,'
                        . ' mrp_production WRITE, mrp_production_rm_target WRITE, main_menu_sub WRITE, penerimaan_barang_tmp WRITE,'
                        . ' stock_move_items  as smi WRITE, penerimaan_barang_tmp as tmp WRITE, mrp_production as mrp WRITE,'
                        . ' departemen as dept WRITE, departemen WRITE,  user WRITE, penerimaan_barang_tmpp_add_quant WRITE,'
                        . 'penerimaan_barang pb WRITE,penerimaan_barang_items pbi WRITE,penerimaan_barang WRITE,penerimaan_barang_items WRITE, invoice WRITE,'
                        . 'mst_produk_coa WRITE,invoice_detail WRITE,purchase_order WRITE,purchase_order_detail WRITE,token_increment WRITE, tax WRITE,setting WRITE,nilai_konversi WRITE';
                // if ($deptid === "RCV") {
                // }
                $this->_module->lock_tabel($lockTable);

                // cek jika mode scan
                $cek_tmp = $this->m_penerimaanBarang->cek_penerimaan_barang_tmp_by_kode($kode);

                // cek item penerimaan_barang by move id
                $smi_in = $this->m_penerimaanBarang->cek_stock_move_items_penerimaan_barang_by_move_id($move_id);
                // tmp add quant
                $add_quant = $this->m_penerimaanBarang->get_list_add_quant_penerimaan_barang_tmp_1($kode);

                //cek status terkirim ?
                $cek_kirim = $this->m_penerimaanBarang->cek_status_barang($kode)->row_array();
                if ($cek_kirim['status'] == 'draft') {
                    $callback = array('status' => 'failed', 'message' => 'Maaf, Product Belum ready !', 'icon' => 'fa fa-warning', 'type' => 'danger');
                } elseif ($cek_kirim['status'] == 'done') {
                    $callback = array('status' => 'failed', 'message' => 'Maaf, Data Sudah Terkirim !', 'icon' => 'fa fa-warning', 'type' => 'danger');
                } else if ($cek_kirim['status'] == 'cancel') {
                    $callback = array('status' => 'failed', 'message' => 'Maaf, Data Tidak Bisa Dikirim, Data Sudah dibatalkan !', 'icon' => 'fa fa-warning', 'type' => 'danger');
                    //}else if(($cek_tmp == 0 AND $deptid =='GRG' ) OR ($mode == 'scan' AND $cek_tmp == 0)){
                } else if (empty($smi_in) && empty($add_quant)) {
                    $callback = array('status' => 'failed', 'message' => 'Maaf, Data Tidak Bisa Dikirim, Data yang akan dikirim kosong !', 'icon' => 'fa fa-warning', 'type' => 'danger');
                } else if ($cek_tmp == 0 and $mode == 'scan') {
                    $callback = array('status' => 'failed', 'message' => 'Barcode belum di Scan, Silahkan Scan Barcode terlebih dahulu !', 'icon' => 'fa fa-warning', 'type' => 'danger');
                } else {

                    $start = $this->_module->get_last_quant_id();

                    //lokasi tujuan 
                    $lokasi = $this->m_penerimaanBarang->get_location_by_move_id($move_id)->row_array();

                    // insert stock_move_items 
                    // $row_order_add = 1;
                    $row_order_add = $this->_module->get_row_order_stock_move_items_by_kode($move_id);

                    foreach ($add_quant as $add_smi) {
                        // smi to adj
                        $data_smi[] = array(
                            'move_id' => $move_id,
                            'quant_id' => $start,
                            'tanggal_transaksi' => $tgl,
                            'kode_produk' => $add_smi->kode_produk,
                            'nama_produk' => $add_smi->nama_produk,
                            'lot' => trim($add_smi->lot),
                            'qty' => $add_smi->qty,
                            'uom' => $add_smi->uom,
                            'qty2' => $add_smi->qty2,
                            'uom2' => $add_smi->uom2,
                            'status' => 'ready',
                            'origin_prod' => $add_smi->origin_prod,
                            'lebar_jadi' => $add_smi->lebar_jadi,
                            'uom_lebar_jadi' => $add_smi->uom_lebar_jadi,
                            'lebar_greige' => $add_smi->lebar_greige,
                            'uom_lebar_greige' => $add_smi->uom_lebar_greige,
                            'lokasi_fisik' => '',
                            'row_order' => $row_order_add
                        );

                        $data_stock_quant[] = array(
                            'quant_id' => $start,
                            'create_date' => $tgl,
                            'move_date' => $tgl,
                            'kode_produk' => $add_smi->kode_produk,
                            'nama_produk' => $add_smi->nama_produk,
                            'lot' => trim($add_smi->lot),
                            'nama_grade' => $add_smi->grade,
                            'qty' => $add_smi->qty,
                            'uom' => $add_smi->uom,
                            'qty2' => $add_smi->qty2,
                            'uom2' => $add_smi->uom2,
                            'lokasi' => $lokasi['lokasi_dari'] ?? '',
                            'lokasi_fisik' => '',
                            'lebar_jadi' => $add_smi->uom_lebar_jadi,
                            'uom_lebar_jadi' => $add_smi->lebar_jadi,
                            'lebar_greige' => $add_smi->lebar_greige,
                            'uom_lebar_greige' => $add_smi->uom_lebar_greige,
                            'reff_note' => $add_smi->reff_note,
                            'reserve_move' => $move_id
                        );

                        $start++;
                        $row_order_add++;
                    }

                    if (!empty($data_smi)) {
                        $result_smi2 = $this->_module->simpan_stock_move_items_batch_2($data_smi);
                        if (!empty($result_smi2)) {
                            if ($result_smi2['message'] != null) {
                                throw new \Exception('Simpan Data Gagal !', 200);
                            }
                        }
                    }

                    // simpan stock quant
                    if (!empty($data_stock_quant)) {
                        // $sql_stock_quant_batch = rtrim($sql_stock_quant_batch, ', ');
                        $result_sq2 = $this->_module->simpan_stock_quant_batch_2($data_stock_quant);
                        if ($result_sq2['message'] != null) {
                            throw new \Exception('Simpan Data Gagal !', 200);
                        }
                    }

                    //cek qty yg akan dikirim                     
                    if ($deptid == 'RCV') {
                        $produk_lebih = '';
                        $qty_produk_lebih = false;
                        $in_items = $this->m_penerimaanBarang->get_list_penerimaan_barang_items($kode);

                        foreach ($in_items as $ins) {
                            // qty target
                            $kebutuh_qty_in = $ins->qty;
                            $origin_prod_in = $ins->origin_prod;
                            $kode_produk_in = $ins->kode_produk;
                            $nama_produk_in = $ins->nama_produk;

                            //cek_qty_smi by  origin produk;
                            $qty_smi = $this->_module->get_qty_stock_move_items_by_kode_origin($move_id, addslashes($kode_produk_in), $origin_prod_in)->row_array();

                            if ($qty_smi['sum_qty'] > $kebutuh_qty_in) {
                                $produk_lebih .= $nama_produk_in . '<br> ';
                                $qty_produk_lebih = true;
                            }
                        }
                        if ($qty_produk_lebih == true) {
                            // $produk_lebih = rtrim($produk_lebih, ', ');
                            throw new \Exception('Qty Produk Melebihi target ! <br>' . $produk_lebih, 200);
                        }
                    }

                    // cek smi 2 
                    $smi_in_2 = $this->m_penerimaanBarang->cek_stock_move_items_penerimaan_barang_by_move_id($move_id);

                    if (empty($smi_in_2)) {
                        throw new \Exception('Maaf, Data yang akan dikirim kosong / tidak terbentuk Movement', 200);
                    } else {


                        //update status tbl penerimaan brg
                        $this->m_penerimaanBarang->update_status_penerimaan_barang($kode, $status_done);

                        //update status tbl penerimaan brg items
                        // $this->m_penerimaanBarang->update_status_penerimaan_barang_items_full($kode, $status_done);
                        // //update semua status di stock_move_produk  
                        // $this->_module->update_status_stock_move_produk_full($move_id, $status_done);
                        //update status tbl stock move 
                        $this->_module->update_status_stock_move($move_id, $status_done);
                        //get move id tujuan
                        $sm_tj = $this->_module->get_stock_move_tujuan($move_id, $origin, 'done', 'cancel')->row_array();
                        // update tangal kirim = now
                        $this->m_penerimaanBarang->update_tgl_kirim_penerimaan_barang($kode, $tgl);

                        $move_id_in = $move_id; //move id asal yg ngebentuk back order
                        //get row order stock_move_items
                        $row_order = $this->_module->get_row_order_stock_move_items_by_kode($sm_tj['move_id']);

                        //loop stock_move_items
                        if ($mode == 'scan') {
                            $querysm = $this->m_penerimaanBarang->get_stock_move_items_by_move_id_partial_in($move_id);
                        } else {
                            $querysm = $this->_module->get_stock_move_items_by_move_id($move_id); // jika mode list / mode != scan
                        }

                        foreach ($querysm as $val) {
                            $loop_sm = true;
                            $sm_pasangan = true;
                            $move_id = $val->move_id;
                            $origin_prod_smi = $val->origin_prod;
                            $quant_id = $val->quant_id;

                            //sebanyak stock_move tujuanya ada
                            while ($loop_sm) {
                                if ($sm_pasangan) {
                                    $status = "ready";
                                }

                                // untuk mendapatkan origin_prod yang terdapat consume kedepannya atau consume lebih dari 1
                                $loop_sm2 = true;
                                $origin_prod_tj = "";
                                $con = false;

                                //get list stock_move by origin
                                $list_sm = $this->_module->get_list_stock_move_origin($origin)->result_array();
                                foreach ($list_sm as $row) {

                                    $mt = explode("|", $row['method']);
                                    $ex_deptid = $mt[0];
                                    $ex_mt = $mt[1];

                                    if ($loop_sm2 == true) {

                                        if ($ex_mt == 'CON' and $ex_deptid == $deptid) {

                                            if (!empty($origin_prod_smi)) {
                                                $origin_prod_tj = $origin_prod_smi;
                                            } else {
                                                //get  origin_prod by move id, kode_produk
                                                $get_origin_prod = $this->m_mo->get_origin_prod_mrp_production_by_kode($row['move_id'], addslashes($val->kode_produk))->row_array();
                                                $origin_prod_tj = $get_origin_prod['origin_prod'];
                                                $loop_sm = false;
                                            }
                                        }
                                    } elseif ($loop_sm2 == false) {
                                        break; //paksa keluar looping
                                    }
                                }


                                if (!empty($origin_prod_tj)) {
                                    $origin_prod = $origin_prod_tj; // origin prod berdasarkan 
                                } else {
                                    $origin_prod = '';
                                }


                                //query ke stock_move tujuan
                                $querysm_tujuan = $this->_module->get_stock_move_tujuan($move_id, $origin, 'done', 'cancel')->row_array();
                                $sm_tujuan = $querysm_tujuan['move_id'];
                                if (!empty($querysm_tujuan['move_id'])) {

                                    // insert stock move untuk stock move tujuan (CON MO)
                                    $sql_stock_move_items_batch .= "('" . $querysm_tujuan['move_id'] . "', '" . $val->quant_id . "', '" . addslashes($val->kode_produk) . "', '" . addslashes($val->nama_produk) . "', '" . addslashes($val->lot) . "', '" . $val->qty . "', '" . addslashes($val->uom) . "', '" . $val->qty2 . "', '" . addslashes($val->uom2) . "', '" . $status . "', '" . $row_order . "', '" . addslashes($origin_prod) . "', '" . $tgl . "','','" . addslashes($val->lebar_greige) . "','" . addslashes($val->uom_lebar_greige) . "','" . addslashes($val->lebar_jadi) . "','" . addslashes($val->uom_lebar_jadi) . "'), ";
                                    //$sm_pasangan = false;
                                    $row_order++;

                                    $move_id = $querysm_tujuan['move_id'];

                                    //update status stock move,stock move dan stock move produk  
                                    $case3 .= "when move_id = '" . $move_id . "' then '" . $status . "'";
                                    $where3 .= "'" . $move_id . "',";
                                    $whereQuant .= "'" . addslashes($val->quant_id) . "',"; //quant id

                                    /*
                                      //update tgl stock_move_items tujuan
                                      $case3x  .= "when quant_id = '".$quant_id."' then '".$tgl."'";
                                      $where3x .= "'".$quant_id."',";
                                     */

                                    //cek jika method stock move tujuan nya CON
                                    $mthd = explode("|", $querysm_tujuan['method']);
                                    $ex_mthd = $mthd[1];

                                    if ($ex_mthd == 'CON') { //update mrp_production_rm_target by kode jadi statusnya ready
                                        //get kode MO by move id 
                                        $mrp = $this->m_mo->get_kode_mrp_production_rm_target_by_move_id($move_id)->row_array();
                                        $case8 .= "when origin_prod = '" . addslashes($origin_prod) . "' then '" . $status . "'";
                                        $where8 .= "'" . addslashes($origin_prod) . "',";
                                        $whereMo = "'" . $mrp['kode'] . "',";
                                    }
                                } else {
                                    //jika sdh tidak ada stockmove ujuan maka loop_sm berhenti
                                    $loop_sm = false;
                                }
                            } //end while
                            //update stok move items asal set done
                            $case .= "when move_id = '" . $val->move_id . "' then '" . $status_done . "'";
                            $where .= "'" . $val->move_id . "',";

                            //update stock quant
                            $case2 .= "when quant_id = '" . $val->quant_id . "' then '" . $lokasi['lokasi_tujuan'] . "'";
                            $where2 .= "'" . $val->quant_id . "',";

                            //update stock quant move id
                            $case6 .= "when quant_id = '" . $val->quant_id . "' then '" . $sm_tj['move_id'] . "'";
                            $where6 .= "'" . $val->quant_id . "',";
                        } //end foreach
                        //simpan stock move item
                        if (!empty($sql_stock_move_items_batch)) {
                            $sql_stock_move_items_batch = rtrim($sql_stock_move_items_batch, ', ');
                            $this->_module->simpan_stock_move_items_batch($sql_stock_move_items_batch);
                            $sql_stock_move_items_batch = '';
                        }

                        //update status stock move items asal
                        if (!empty($where) and !empty($case)) {
                            $where = rtrim($where, ',');
                            $sql_update_stock_move_items = "UPDATE stock_move_items SET status =(case " . $case . " end), tanggal_transaksi = '" . $tgl . "' WHERE  move_id in (" . $where . ") ";
                            $this->m_penerimaanBarang->update_perbatch($sql_update_stock_move_items);
                        }

                        //update lokasi tbl stock quant
                        if (!empty($where2) and !empty($case2)) {
                            $where2 = rtrim($where2, ',');
                            $sql_update_stock_quant = "UPDATE stock_quant SET lokasi =(case " . $case2 . " end), move_date = '" . $tgl . "' WHERE  quant_id in (" . $where2 . ") ";
                            $this->m_penerimaanBarang->update_perbatch($sql_update_stock_quant);
                        }

                        if (!empty($where6) and !empty($case6)) {
                            $where6 = rtrim($where6, ',');
                            $sql_update_stock_quant_move_id = "UPDATE stock_quant SET reserve_move =(case " . $case6 . " end) WHERE  quant_id in (" . $where6 . ") ";
                            $this->m_penerimaanBarang->update_perbatch($sql_update_stock_quant_move_id);
                        }

                        if (!empty($where3) and !empty($case3)) {
                            //update stock move penerimaan barang 
                            $where3 = rtrim($where3, ',');
                            $sql_update_stock_move = "UPDATE stock_move SET status =(case " . $case3 . " end) WHERE  move_id in (" . $where3 . ") ";
                            $this->m_penerimaanBarang->update_perbatch($sql_update_stock_move);

                            //update stock move produk penerimaan barang 
                            $where3 = rtrim($where3, ',');
                            $sql_update_stock_move_produk = "UPDATE stock_move_produk SET status =(case " . $case3 . " end) WHERE  move_id in (" . $where3 . ") ";
                            $this->m_penerimaanBarang->update_perbatch($sql_update_stock_move_produk);

                            //update status = ready
                            $where3 = rtrim($where3, ',');
                            $where3x = rtrim($where3x, ',');
                            $whereQuant = rtrim($whereQuant, ',');
                            $sql_update_stock_move_items = "UPDATE stock_move_items SET status =(case " . $case3 . " end) WHERE  move_id in (" . $where3 . ") AND quant_id in (" . $whereQuant . ") ";
                            $this->m_penerimaanBarang->update_perbatch($sql_update_stock_move_items);

                            //update status=ready untuk MO tujuan
                            if (!empty($where8) and !empty($case8)) {
                                $where8 = rtrim($where8, ',');
                                $whereMo = rtrim($whereMo, ',');
                                $sql_update_mrp_rm_target = "UPDATE mrp_production_rm_target SET status =(case " . $case8 . " end) WHERE  origin_prod in (" . $where8 . ") AND kode in (" . $whereMo . ") ";
                                $this->_module->update_perbatch($sql_update_mrp_rm_target);

                                $update_status = true;
                                // cek apakah untuk MG Dyeing 
                                $cek_mrp = $this->m_penerimaanBarang->get_type_mo_dept_id_mrp_production_by_kode($whereMo);
                                if ($cek_mrp['dept_id'] == 'DYE' and $cek_mrp['type_mo'] == 'colouring') {
                                    // cek status mrp_rm yg sama dengan draft dan cancel
                                    $cek_mrp_rm = $this->m_penerimaanBarang->cek_mrp_production_rm_target_by_kode($whereMo)->num_rows();
                                    if ($cek_mrp_rm > 0) {
                                        $update_status = false;
                                    }
                                }

                                $cek_rm = $this->_module->cek_status_mrp_rm_target_additional_move_id_kosong_by_kode($whereMo)->num_rows();
                                if ($cek_rm > 0) {
                                    $update_status = false;
                                } else {
                                    $update_status = true;
                                }

                                if ($update_status == true) {
                                    $sql_update_mrp_production = "UPDATE mrp_production SET status ='ready' WHERE  kode in (" . $whereMo . ") ";
                                    $this->_module->update_perbatch($sql_update_mrp_production);
                                }
                            }
                        }


                        $warehouse = $deptid;
                        $method_dept = $warehouse;
                        $method_action = 'IN';

                        // Generate penerimaan barang
                        $kode_ = $this->_module->get_kode_penerimaan($method_dept);
                        $get_kode_in = $kode_;

                        $dgt = substr("00000" . $get_kode_in, -5);
                        $kode_in = $method_dept . "/" . $method_action . "/" . date("y") . date("m") . $dgt;
                        $in_row = 1;
                        $backorder = false;
                        $delete = false;

                        $sql_stock_move_batch = "";
                        $sql_stock_move_produk_batch = "";
                        $sql_log_history_in = "";

                        $sql_in_batch = "";
                        $sql_in_items_batch = "";
                        $qty_back = "";
                        $kode_prod_del = "";
                        $sql_in_batch_2 = [];
                        $sql_in_items_batch_2 = [];

                        $last_move = $this->_module->get_kode_stock_move();
                        $move_id = "SM" . $last_move; //Set kode stock_move

                        $row_order_tmp = 1;
                        $sql_stock_move_items_batch = '';
                        $case_tmp = '';
                        $case_tmp_2 = '';
                        $where_tmp = '';

                        if ($mode == 'scan') {

                            // get stock_move_items not penerimaan_barang_tmp
                            $smi_tmp = $this->m_penerimaanBarang->get_stock_move_items_not_penerimaan_barang_tmp($move_id_in);
                            foreach ($smi_tmp as $tmp) {
                                $sql_stock_move_items_batch .= "('" . $move_id . "', '" . $tmp->quant_id . "', '" . addslashes($tmp->kode_produk) . "', '" . addslashes($tmp->nama_produk) . "', '" . addslashes($tmp->lot) . "', '" . $tmp->qty . "', '" . addslashes($tmp->uom) . "', '" . $tmp->qty2 . "', '" . addslashes($tmp->uom2) . "', 'ready', '" . $row_order_tmp . "', '" . addslashes($tmp->origin_prod) . "', '" . $tgl . "','','" . addslashes($val->lebar_greige) . "','" . addslashes($val->uom_lebar_greige) . "','" . addslashes($val->lebar_jadi) . "','" . addslashes($val->uom_lebar_jadi) . "'), ";
                                $row_order++;

                                //get quant_id not in tmp
                                $case_tmp .= "when quant_id = '" . $tmp->quant_id . "' then '' ";
                                $where_tmp .= "'" . $tmp->quant_id . "',";
                                $case_tmp_2 .= "when quant_id = '" . $tmp->quant_id . "' then '" . $move_id . "' ";
                            }


                            if (!empty($where_tmp) and !empty($case_tmp)) {

                                // ganti reserve move ke penerimaan baru
                                $where_tmp = rtrim($where_tmp, ',');
                                $sql_update_reserve_move = "UPDATE stock_quant SET reserve_move =(case " . $case_tmp . " end) WHERE  quant_id in (" . $where_tmp . ") ";
                                $this->m_penerimaanBarang->update_perbatch($sql_update_reserve_move);

                                // hapus stock move items not in tmp    
                                $sql_delete_smi_not_tmp = "DELETE  FROM stock_move_items WHERE quant_id IN (" . $where_tmp . ") AND move_id = '" . $move_id_in . "'";
                                $this->_module->update_perbatch($sql_delete_smi_not_tmp);
                            }
                        }

                        //hapus penerimaan barang tmp
                        $sql_delete_lot_tbl_tmp = "DELETE  FROM penerimaan_barang_tmp WHERE kode = '" . $kode . "'";
                        $this->_module->update_perbatch($sql_delete_lot_tbl_tmp);

                        //foreach untuk ngebentuk back order atau tidak
                        $list = $this->m_penerimaanBarang->get_list_penerimaan_barang_items($kode);
                        foreach ($list as $row) {
                            $kode_produk = $row->kode_produk;
                            $qty = $row->qty;
                            $origin_prod = $row->origin_prod;

                            //$qty_smi = $this->_module->get_qty_stock_move_items_by_kode($move_id_in,addslashes($kode_produk))->row_array();
                            // cek apakah terdapat kode_produk yg lebih dari 1
                            $cek_jml_produk_sama = $this->m_penerimaanBarang->cek_jml_produk_sama_penerimaan_barang_by_kode($kode, $kode_produk)->num_rows();
                            if ($cek_jml_produk_sama > 0) { // where ditambah origin_prod
                                $qty_smi = $this->_module->get_qty_stock_move_items_by_kode_origin($move_id_in, addslashes($kode_produk), $origin_prod)->row_array();
                            } else {
                                //cek qty produk di stock_move_items apa masih kurang dengan target qty di pengiriman barang items
                                $qty_smi = $this->_module->get_qty_stock_move_items_by_kode($move_id_in, addslashes($kode_produk))->row_array();
                            }

                            if ($qty_smi['sum_qty'] <= $qty and !empty($qty_smi['sum_qty'])) { //jika qty di stock_move_items kurang dari qty di penerimaan barang items
                                //update status done, in items & sm produk
                                $this->m_penerimaanBarang->update_status_penerimaan_barang_items_origin_prod($kode, addslashes($kode_produk), 'done', $origin_prod);
                                $this->_module->update_status_stock_move_produk_origin_prod($move_id_in, addslashes($kode_produk), 'done', $origin_prod);

                                if ($qty_smi['sum_qty'] < $qty) {
                                    $backorder = true;
                                    $qty_back = $qty - $qty_smi['sum_qty'];
                                    //simpan ke penermaan_barang_items
                                    // $sql_in_items_batch .= "('" . $kode_in . "','" . addslashes($row->kode_produk) . "','" . addslashes($row->nama_produk) . "','" . $qty_back . "','" . addslashes($row->uom) . "','draft','" . $in_row . "','" . addslashes($origin_prod) . "'), ";

                                    $sql_in_items_batch_2[] = array(
                                        "kode" => $kode_in,
                                        "kode_produk" => $row->kode_produk,
                                        "nama_produk" => $row->nama_produk,
                                        "qty" => $qty_back,
                                        "uom" => $row->uom,
                                        "status_barang" => $status_back_order,
                                        "origin_prod" => $origin_prod,
                                        "qty_beli" => $row->qty_beli,
                                        "uom_beli" => $row->uom_beli,
                                        "kode_pp" => $row->kode_pp,
                                        "row_order" => $in_row,
                                        "id_konversiuom" => $row->id_konversiuom,
                                        "nilai_konversiuom" => $row->nilai_konversiuom,
                                        "reff_note" => $row->reff_note
                                    );

                                    //simpan ke stock move produk 
                                    $sql_stock_move_produk_batch .= "('" . $move_id . "','" . addslashes($row->kode_produk) . "','" . addslashes($row->nama_produk) . "','" . $qty_back . "','" . addslashes($row->uom) . "','" . $status_back_order . "','" . $in_row . "','" . addslashes($origin_prod) . "'), ";
                                }
                                $in_row++;
                            } else if (round($qty_smi['sum_qty'], 2) == 0.00 or empty($qty_smi['sum_qty'])) {

                                $this->m_penerimaanBarang->update_status_penerimaan_barang_items_origin_prod($kode, addslashes($kode_produk), 'draft', $origin_prod);
                                $this->_module->update_status_stock_move_produk_origin_prod($move_id_in, addslashes($kode_produk), 'draft', $origin_prod);

                                $backorder = true;
                                $qty_back = $qty;

                                $sql_in_items_batch_2[] = array(
                                    "kode" => $kode_in,
                                    "kode_produk" => $row->kode_produk,
                                    "nama_produk" => $row->nama_produk,
                                    "qty" => $qty_back,
                                    "uom" => $row->uom,
                                    "status_barang" => $status_back_order,
                                    "origin_prod" => $origin_prod,
                                    "qty_beli" => $row->qty_beli,
                                    "uom_beli" => $row->uom_beli,
                                    "kode_pp" => $row->kode_pp,
                                    "row_order" => $in_row,
                                    "id_konversiuom" => $row->id_konversiuom,
                                    "nilai_konversiuom" => $row->nilai_konversiuom,
                                    "reff_note" => $row->reff_note
                                );
                                //simpan ke stock move produk 
                                $sql_stock_move_produk_batch .= "('" . $move_id . "','" . addslashes($row->kode_produk) . "','" . addslashes($row->nama_produk) . "','" . $qty_back . "','" . addslashes($row->uom) . "','" . $status_back_order . "','" . $in_row . "','" . addslashes($origin_prod) . "'), ";
                                $in_row++;
                            }

                            // if (empty($qty_smi['sum_qty'])) {//jika qty di stock_move_items tidak ada
                            //     $delete = true;
                            //     $kode_prod_del .= "'" . addslashes($kode_produk) . "',";
                            // }
                        }

                        if ($backorder == true) {

                            //get data di pengiriman barang 
                            $head = $this->m_penerimaanBarang->get_data_by_code($kode);

                            $method = $warehouse . '|' . $method_action;
                            $lokasi_dari = $head->lokasi_dari;
                            $lokasi_tujuan = $head->lokasi_tujuan;
                            $reff_notes_back = 'Back Order ' . $kode . ' ' . $head->reff_note;
                            $schedule_date = $head->tanggal_jt;
                            $partner_id = $head->partner_id;
                            $nama_partner = $head->nama_partner;
                            $no_sj = $head->no_sj;
                            $tanggal_sj = $head->tanggal_sj;
                            $tgl = date('Y-m-d H:i:s');

                            //simpan ke stock move
                            $origin = $origin;
                            $sql_stock_move_batch .= "('" . $move_id . "','" . $tgl . "','" . $origin . "','" . $method . "','" . $lokasi_dari . "','" . $lokasi_tujuan . "','" . $status_back_order . "','1',''), ";

                            $reff_picking_in = $head->reff_picking;
                            // $sql_in_batch .= "('" . $kode_in . "','" . $tgl . "','" . $tgl . "','" . $schedule_date . "','" . addslashes($reff_notes_back) . "','draft','" . $method_dept . "','" . $origin . "','" . $move_id . "','" . $reff_picking_in . "','" . $lokasi_dari . "','" . $lokasi_tujuan . "'), ";

                            $sql_in_batch_2[] = array(
                                "kode" => $kode_in,
                                "tanggal" => $tgl,
                                "tanggal_transaksi" => $tgl,
                                "tanggal_jt" => $schedule_date,
                                "origin" => $origin,
                                "move_id" => $move_id,
                                "lokasi_dari" => $lokasi_dari,
                                "lokasi_tujuan" => $lokasi_tujuan,
                                "reff_picking" => $reff_picking_in,
                                "reff_note" => $reff_notes_back,
                                "status" => $status_back_order,
                                "dept_id" => $method_dept,
                                "partner_id" => $partner_id,
                                "nama_partner" => $nama_partner
                                    // "no_sj"        => $no_sj,
                                    // "tanggal_sj"   => $tanggal_sj
                            );

                            //get mms kode berdasarkan dept_id
                            $mms = $this->_module->get_kode_sub_menu_deptid('penerimaanbarang', $method_dept)->row_array();
                            if (!empty($mms['kode'])) {
                                $mms_kode = $mms['kode'];
                            } else {
                                $mms_kode = '';
                            }

                            //create log history penerimaan_barang
                            $note_log = $kode_in . '|' . $origin;
                            $date_log = date('Y-m-d H:i:s');
                            $sql_log_history_in .= "('" . $date_log . "','" . $mms_kode . "','" . $kode_in . "','create','" . $note_log . "','" . $nama_user . "'), ";

                            if (!empty($sql_stock_move_batch)) {
                                $sql_stock_move_batch = rtrim($sql_stock_move_batch, ', ');
                                $this->_module->create_stock_move_batch($sql_stock_move_batch);

                                $sql_stock_move_produk_batch = rtrim($sql_stock_move_produk_batch, ', ');
                                $this->_module->create_stock_move_produk_batch($sql_stock_move_produk_batch);
                            }

                            if (!empty($sql_in_batch_2)) {
                                // $sql_in_batch = rtrim($sql_in_batch, ', ');
                                // $this->_module->simpan_penerimaan_batch($sql_in_batch);
                                // $sql_in_items_batch = rtrim($sql_in_items_batch, ', ');
                                // $this->_module->simpan_penerimaan_items_batch_origin_prod($sql_in_items_batch);


                                $in_insert = $this->_module->simpan_penerimaan_batch_2($sql_in_batch_2);
                                if ($in_insert['message'] != null) {
                                    throw new \Exception('Simpan Data Gagal !', 200);
                                }

                                $in_item_insert = $this->_module->simpan_penerimaan_items_batch_2($sql_in_items_batch_2);
                                if ($in_item_insert['message'] != null) {
                                    throw new \Exception('Simpan Data Gagal !', 200);
                                }

                                $sql_log_history_in = rtrim($sql_log_history_in, ', ');
                                $this->_module->simpan_log_history_batch($sql_log_history_in);
                            }

                            if ($mode == 'scan' and !empty($sql_stock_move_items_batch)) {

                                //simpan stock move items in baru dari mode scan
                                if (!empty($sql_stock_move_items_batch)) {
                                    $sql_stock_move_items_batch = rtrim($sql_stock_move_items_batch, ', ');
                                    $this->_module->simpan_stock_move_items_batch($sql_stock_move_items_batch);

                                    // ganti reserve move ke penerimaan baru
                                    $where_tmp = rtrim($where_tmp, ',');
                                    $sql_update_reserve_move = "UPDATE stock_quant SET reserve_move =(case " . $case_tmp_2 . " end) WHERE  quant_id in (" . $where_tmp . ") ";
                                    $this->m_penerimaanBarang->update_perbatch($sql_update_reserve_move);
                                }

                                //update penerimaan barang = ready
                                $sql_update_penerimaan_barang = "UPDATE penerimaan_barang SET status ='ready' WHERE  kode in ('" . $kode_in . "') ";
                                $this->m_penerimaanBarang->update_perbatch($sql_update_penerimaan_barang);

                                //update penerimaan barang items = ready
                                $sql_update_penerimaan_barang_items = "UPDATE penerimaan_barang_items SET status_barang ='ready' WHERE  kode in ('" . $kode_in . "') ";
                                $this->m_penerimaanBarang->update_perbatch($sql_update_penerimaan_barang_items);

                                //update stock_move  == ready
                                $sql_update_stock_move = "UPDATE stock_move SET status ='ready' WHERE  move_id in ('" . $move_id . "') ";
                                $this->m_penerimaanBarang->update_perbatch($sql_update_stock_move);

                                $sql_update_stock_move_produk = "UPDATE stock_move_produk SET status ='ready' WHERE  move_id in ('" . $move_id . "') ";
                                $this->m_penerimaanBarang->update_perbatch($sql_update_stock_move_produk);

                                $sql_update_stock_move_items = "UPDATE stock_move_items SET status ='ready' WHERE  move_id in ('" . $move_id . "') ";
                                $this->m_penerimaanBarang->update_perbatch($sql_update_stock_move_items);
                            }

                            // update source_move by move_id backorder jika status nya tidak sama dengan done atau cancel
                            $sc_move = $this->_module->get_stock_move_by_move_id($move_id_in)->row_array();
                            $mvid_updt = false;
                            $case7 = "";
                            $where7 = "";

                            // cek jika ada move_id_tujuan (biasanya ini untuk jalur jacquard saja) to consumable berdasarkan move_id sebelumnya
                            $querysm_tujuan_con = $this->_module->get_stock_move_tujuan($move_id_in, $origin, 'done', 'cancel')->row_array();
                            $sm_tujuan = $querysm_tujuan_con['move_id'];
                            if (!empty($sm_tujuan)) {

                                $sc_move_con = $this->_module->get_stock_move_by_move_id($sm_tujuan)->row_array();
                                $source_move_con = $sc_move_con['source_move'] . '|' . $move_id;

                                $sql_update_source_move_con = "UPDATE stock_move set source_move = '$source_move_con' WHERE move_id = '$sm_tujuan' ";
                                $this->_module->update_perbatch($sql_update_source_move_con);
                            }

                            if (!empty($sc_move['source_move'])) {
                                $sc = explode('|', $sc_move['source_move']);
                                foreach ($sc as $key) {
                                    //cek jika status move id nya tidak done atau cancel
                                    $mvid = $this->_module->get_move_id_by_source_move($key, 'done', 'cancel')->row_array();
                                    if (!empty($mvid['move_id'])) {
                                        $mvid_updt = true;
                                        $move_id_updt = $mvid['move_id'] . '|';
                                        $kode_out = $this->_module->get_kode_pengiriman_barang_by_move_id($mvid['move_id'])->row_array();
                                        //$case7 .= "when move_id = '".$mvid['move_id']."' then '".$kode_out['kode'].'|'.$kode_in."' ";
                                        //$where7 .= "'".$mvid['move_id']."',";

                                        if (!empty($kode_out['kode'])) {
                                            $reff_picking_baru = $kode_out['kode'] . '|' . $kode_in;
                                        } else {
                                            //$reff_picking_baru = $kode_out['kode'].'|'.$kode_in;

                                            $dept_dari = $this->_module->get_kode_departemen_by_stock_location($lokasi_dari); // jika lokasi tujuan transit pasti tidak di temukan
                                            if (!empty($dept_dari)) {
                                                $reff_picking_baru = $dept_dari . '|' . $kode_in;
                                            } else {
                                                $reff_picking_baru = '|' . $kode_in;
                                            }
                                        }

                                        $move_id_out = $mvid['move_id'];
                                    }
                                }

                                if ($mvid_updt == true) {
                                    //update source_move backorder
                                    $move_id_updt = rtrim($move_id_updt, '|');
                                    $source_move = $move_id_updt;
                                    $sql_update_source_move = "UPDATE stock_move set source_move = '$source_move' WHERE move_id = '$move_id' ";
                                    $this->_module->update_perbatch($sql_update_source_move);

                                    //update reff picking baru di  pengiriman barang  dan penerimaan barang 
                                    $where7 = rtrim($where7, ',');
                                    $sql_update_reff_picking_pengiriman = "UPDATE pengiriman_barang SET reff_picking ='$reff_picking_baru' WHERE  move_id in ('" . $move_id_out . "')";
                                    $this->_module->update_perbatch($sql_update_reff_picking_pengiriman);

                                    $sql_update_reff_picking_penerimaan = "UPDATE penerimaan_barang SET reff_picking ='$reff_picking_baru' WHERE  move_id in ('" . $move_id . "')";
                                    $this->_module->update_perbatch($sql_update_reff_picking_penerimaan);
                                }
                            }
                        } //end if backorder == true
                        // if ($delete == true) {
                        //     $kode_prod_del = rtrim($kode_prod_del, ',');
                        //     $sql_delete_penerimaan_brg_items = "DELETE  FROM penerimaan_barang_items WHERE kode_produk IN (" . $kode_prod_del . ") AND kode = '" . $kode . "'";
                        //     $this->m_penerimaanBarang->update_perbatch($sql_delete_penerimaan_brg_items);
                        //     $sql_delete_stock_move_produk = "DELETE  FROM stock_move_produk WHERE kode_produk IN (" . $kode_prod_del . ") AND move_id = '" . $move_id_in . "'";
                        //     $this->m_penerimaanBarang->update_perbatch($sql_delete_stock_move_produk);
                        // }
                        //unlock table
                        // $this->_module->unlock_tabel();

                        if ($mode == 'scan') {
                            $info_partial = '( Partial )';
                        } else {
                            $info_partial = '';
                        }

                        // delete tmp add quant penerimaan barang
                        $this->m_penerimaanBarang->delete_add_quant_penerimaan_barang_2($kode);

                        $jenis_log = "done";
                        $note_log = "Kirim Data Barang " . $info_partial . " ";
                        $this->_module->gen_history_deptid($sub_menu, $kode, $jenis_log, $note_log, $username, $deptid);
                        if (!$delete) {
                            $po = new $this->m_po;
                            $rcvItem = clone $po;
                            $datarcvItem = $rcvItem->setTables("penerimaan_barang pb")->setJoins("penerimaan_barang_items pbi", "pb.kode = pbi.kode")
                                    ->setWheres(["pb.kode" => $kode])->setOrder(["tanggal" => "desc"])
                                    ->setSelects(["origin", "kode_produk"]);
                            $origin = [];
                            $kode_produk = [];
                            $readyrcvItem = clone $datarcvItem;
                            foreach ($readyrcvItem->setWheres(["status_barang" => "done"])->getData() as $key => $value) {
                                $origin[] = $value->origin;
                                $kode_produk[] = $value->kode_produk;
                            }
                            //     if($readyrcvItem->setWheres(["status_barang"=>"ready"])->getDataCountFiltered() < 1) {
                            //        $ipo = clone $po;
                            //         $ipo->setTables("purchase_order")
                            //            ->setWhereRaw("no_po in ('". implode("','", $origin)."') and status = 'purchase_confirmed'")
                            //             ->update(["status"=>"done"]);
                            //    }
                            $po->setTables("purchase_order_detail")
                                    ->setWhereRaw("po_no_po in ('" . implode("','", $origin) . "') and kode_produk in ('" . implode("','", $kode_produk) . "') and status not in ('cancel','retur')")
                                    ->update(["status" => "done"]);

                            // log_message('error', "po_no_po in ('". implode("','", $origin)."') and kode_produk in ('". implode("','", $kode_produk)."') and status <> 'cancel'");
                        }
                        if ($backorder == true) {
                            $callback = array('status' => 'success', 'message' => 'Data Berhasil Terkirim !', 'icon' => 'fa fa-check', 'type' => 'success', 'backorder' => 'yes', 'message2' => 'Akan terbentuk Backorder dengan No ' . $kode_in);
                        } else {

                            $callback = array('status' => 'success', 'message' => 'Data Berhasil Terkirim !', 'icon' => 'fa fa-check', 'type' => 'success');
                        }
                    }
                } //else cek-cek
            } //else session

            if ($deptid === 'RCV') {
                $orig = $this->input->post('origin');
                $po = new m_po;
                $dataPO = $po->setWheres(["no_po" => $orig,])->setWhereRaw("purchase_order_detail.status not in ('cancel','retur')")
                        ->setJoins("purchase_order_detail", "purchase_order_detail.po_id = purchase_order.id")
                        ->setJoins("penerimaan_barang_items", "(penerimaan_barang_items.kode = '{$kode}' and penerimaan_barang_items.status_barang='done' "
                                . "and  purchase_order_detail.kode_produk = penerimaan_barang_items.kode_produk and penerimaan_barang_items.kode_pp = purchase_order_detail.kode_pp)")
                        ->setJoins("penerimaan_barang", "penerimaan_barang_items.kode = penerimaan_barang.kode")
                        ->setJoins("mst_produk_coa", "mst_produk_coa.kode_produk = purchase_order_detail.kode_produk", "left")
                        ->setJoins("tax", "tax.id = purchase_order_detail.tax_id", "left")
                        ->setJoins("nilai_konversi", "nilai_konversi.id = purchase_order_detail.id_konversiuom", "left")
                        ->setJoins("stock_move_items as smi", "(smi.move_id = penerimaan_barang.move_id and smi.origin_prod = penerimaan_barang_items.origin_prod)", "left")
                        ->setOrder(["no_po"])
                        ->setSelects([
                            "purchase_order_detail.harga_per_uom_beli,purchase_order_detail.tax_id,purchase_order_detail.diskon,purchase_order_detail.deskripsi",
                            "purchase_order_detail.reff_note,mst_produk_coa.kode_coa,no_value", "smi.qty as qty_dtg",
                            "purchase_order.supplier,purchase_order.currency,purchase_order.nilai_currency,purchase_order.total as po_total",
                            "penerimaan_barang_items.*", "amount,tax.id as pajak_id", "dpp_lain", "tax.dpp as dpp_tax", "tax_lain_id", "nilai_konversi.nilai", "purchase_order.jenis as jenis_po",
                            "konversi_aktif", "pembilang", "penyebut"
                        ])->setGroups(["smi.quant_id"])
                        ->getData();
                if (is_null($dataPO)) {
                    throw new \Exception("No PO {$orig} tidak ditemukan.", 500);
                }
                if ($dataPO[0]->jenis_po === "RFQ") {
                    if ($dataPO[0]->no_value !== "1") {
                        $orderDate = date("Y-m-d H:i:s");
                        if (!$noinv = $this->token->noUrut('invoice_pembelian', date('y') . '/' . date('m'), true)
                                        ->generate("PBINV/", '/%05d')->get()) {
                            throw new \Exception("No Invoice tidak terbuat", 500);
                        }
                        $inserInvoice = new m_po;
                        //                $item = clone $inserInvoice;
                        $invoiceDetail = [];

                        $head = $this->m_penerimaanBarang->get_data_by_code($kode);

                        $dataInvoice = [
                            "no_invoice" => $noinv,
                            "id_supplier" => $dataPO[0]->supplier,
                            "no_po" => $orig,
                            "order_date" => $orderDate,
                            "created_at" => date("Y-m-d H:i:s"),
                            "matauang" => $dataPO[0]->currency,
                            'nilai_matauang' => $dataPO[0]->nilai_currency,
                            "journal" => "PB",
                            "total" => $dataPO[0]->po_total,
                            "dpp_lain" => $dataPO[0]->dpp_lain,
                            "origin" => $kode,
                            "no_sj_supp" => $head->no_sj,
                            "tanggal_invoice_supp" => $head->tanggal_sj,
                            "tanggal_sj" => $head->tanggal_sj
                        ];

                        $idInsert = $inserInvoice->setTables("invoice")->save($dataInvoice);
                        //                $dataRCV = $item->setTables("penerimaan_barang_items")->setWheres(["kode"=>$kode])->setOrder(["row_order"])->getData();


                        $totals = 0.00;
                        $diskons = 0.00;
                        $taxes = 0.00;
                        $nilaiDppLain = 0;
                        $models = new $this->m_global;
                        $models->setTables("tax");
                        $qty = 0;
                        foreach ($dataPO as $key => $value) {
                            $nilai_dpp = 0;
                            if ($value->konversi_aktif === "1") {
                                $qty = ($value->pembilang / $value->penyebut) * $value->qty_dtg;
                            } else {
                                $qty = $value->qty_dtg / $value->nilai;
                            }
                            $invoiceDetail[] = [
                                'invoice_id' => $idInsert,
                                'nama_produk' => $value->nama_produk,
                                'kode_produk' => $value->kode_produk,
                                'qty_beli' => $qty,
                                'uom_beli' => $value->uom_beli,
                                'deskripsi' => $value->deskripsi,
                                'reff_note' => $value->reff_note,
                                'account' => $value->kode_coa,
                                'harga_satuan' => $value->harga_per_uom_beli,
                                'tax_id' => $value->pajak_id,
                                'diskon' => $value->diskon,
                                "amount_tax" => $value->amount
                            ];
                            $total = ($qty * $value->harga_per_uom_beli);
                            $totals += $total;
                            $diskon = ($value->diskon ?? 0);
                            $diskons += $diskon;
                            $taxe = 0;

                            if ($value->dpp_lain > 0 && $value->dpp_tax === "1") {
                                $nilai_dpp = ((($total - $diskon) * 11) / 12);
                                $taxe += ((($total - $diskon) * 11) / 12) * $value->amount;
                            } else {
                                $taxe += ($total - $diskon) * $value->amount;
                            }

                            if ($value->tax_lain_id !== "0") {
                                $dataTax = $models->setWhereIn("id", explode(",", $value->tax_lain_id), true)->setSelects(["amount,dpp"])->setOrder(["id"])->getData();
                                foreach ($dataTax as $kkk => $datas) {
                                    if ($value->dpp_lain > 0 && $datas->dpp === "1") {
                                        $nilai_dpp += ((($total - $diskon) * 11) / 12);
                                        $taxe += ((($total - $diskon) * 11) / 12) * $datas->amount;
                                    } else {
                                        $taxe += ($total - $diskon) * $datas->amount;
                                    }
                                }
                            }
                            $taxes += $taxe;
                            $nilaiDppLain += $nilai_dpp;
                        }
                        $grandTotal = ($totals - $diskons) + $taxes;
                        //create Invoice_detail
                        $inserInvoice->setTables("invoice_detail")->saveBatch($invoiceDetail);
                        $inserInvoice->setTables("invoice")->setWheres(["id" => $idInsert], true)->update(["total" => $grandTotal, "dpp_lain" => $nilaiDppLain]);
                        $this->_module->gen_history('invoice', $idInsert, 'create', logArrayToString(";", $dataInvoice), $username);
                    }
                }
                //status done PO
                $model = new $this->m_global;
                $cek = $model->setTables("penerimaan_barang pb")->setWheres(["pb.origin" => $orig])->setWhereRaw("status not in ('done','cancel')")->getDetail();
                if (!$cek) {
                    $model->setTables("purchase_order")->setWheres(["no_po" => $orig], true)->update(["status" => "done"]);
//                    $this->_module->gen_history('invoice', $idInsert, 'edit', logArrayToString(";", $dataInvoice), $username);
                }
            }
            if (!$this->_module->finishTransaction()) {
                throw new \Exception('Kirim Barang Gagal', 500);
            }

            $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($callback));
        } catch (Exception $ex) {
            // $this->_module->rollbackTransaction();
            // $this->_module->unlock_tabel();
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('status' => 'failed', 'message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        } finally {
            $this->_module->rollbackTransaction();
            // unlock table
            $this->_module->unlock_tabel();
        }

        // echo json_encode($callback);
    }

    public function batal_penerimaan_barang() {

        if (empty($this->session->userdata('status'))) { //cek apakah session masih ada
            // session habis
            $callback = array('message' => 'Waktu Anda Telah Habis', 'sesi' => 'habis');
        } else {

            $sub_menu = $this->uri->segment(2);
            $username = addslashes($this->session->userdata('username'));

            $kode = $this->input->post('kode');
            $move_id = $this->input->post('move_id');
            $deptid = $this->input->post('deptid');

            $status_cancel = 'cancel';

            // cek item penerimaan_barang by move id
            $smi_out = $this->m_penerimaanBarang->cek_stock_move_items_penerimaan_barang_by_move_id($move_id);
            // tmp add quant
            $add_quant = $this->m_penerimaanBarang->get_list_add_quant_penerimaan_barang_tmp_1($kode);

            //cek status terkirim ?
            $cek_kirim = $this->m_penerimaanBarang->cek_status_barang($kode)->row_array();
            if ($cek_kirim['status'] == 'done') {
                $callback = array('status' => 'failed', 'message' => 'Maaf, Data tidak bisa dibatalkan, Data Sudah Terkirim !', 'icon' => 'fa fa-warning', 'type' => 'danger');
            } elseif ($cek_kirim['status'] == 'cancel') {
                $callback = array('status' => 'failed', 'message' => 'Maaf, Data Sudah dibatalkan !', 'icon' => 'fa fa-warning', 'type' => 'danger');
            } elseif ($smi_out > 0 or !empty($add_quant)) {
                $callback = array('status' => 'failed', 'message' => 'Maaf, Data tidak bisa dibatalkan, Harap Hapus terlebih dahulu details Produk / Lot !', 'icon' => 'fa fa-warning', 'type' => 'danger');
            } else {

                // lock table
                $this->_module->lock_tabel('penerimaan_barang WRITE, penerimaan_barang_items WRITE, stock_move WRITE, stock_move_produk WRITE');

                // batal penerimaan_barang
                $sql_update_status_penerimaan = "UPDATE penerimaan_barang SET status = '" . $status_cancel . "' WHERE kode = '" . $kode . "' ";
                $this->_module->update_perbatch($sql_update_status_penerimaan);

                // batal penerimaan_barang items
                $sql_update_status_penerimaan_items = "UPDATE penerimaan_barang_items SET status_barang = '" . $status_cancel . "' WHERE kode = '" . $kode . "' ";
                $this->_module->update_perbatch($sql_update_status_penerimaan_items);

                // batal stock_move, stock_move_produk
                $sql_update_status_stock_move = "UPDATE stock_move SET status = '" . $status_cancel . "' WHERE move_id = '" . $move_id . "' ";
                $this->_module->update_perbatch($sql_update_status_stock_move);

                $sql_update_status_stock_move_produk = "UPDATE stock_move_produk SET status = '" . $status_cancel . "' WHERE move_id = '" . $move_id . "' ";
                $this->_module->update_perbatch($sql_update_status_stock_move_produk);

                // unlock table
                $this->_module->unlock_tabel();

                $jenis_log = "cancel";
                $note_log = "Batal Penerimaan Barang ";
                $this->_module->gen_history_deptid($sub_menu, $kode, $jenis_log, $note_log, $username, $deptid);

                $callback = array('status' => 'success', 'message' => 'Data Penerimaan Barang Berhasil di batalkan !', 'icon' => 'fa fa-check', 'type' => 'success');
            }
        }

        echo json_encode($callback);
    }

    public function tambah_data_details_quant_penerimaan() {
        $kode = $this->input->post('kode');
        $kode_produk = $this->input->post('kode_produk');
        $move_id = $this->input->post('move_id');
        $deptid = $this->input->post('deptid');
        $nama_produk = $this->input->post('nama_produk');
        $origin = $this->input->post('origin');
        $origin_prod = $this->input->post('origin_prod');

        $data['kode'] = $kode;
        $data['kode_produk'] = $kode_produk;
        $data['nama_produk'] = $nama_produk;
        $data['move_id'] = $move_id;
        $data['deptid'] = $deptid;
        $data['origin'] = $origin;
        $data['origin_prod'] = $origin_prod;
        $data['list_grade'] = $this->_module->get_list_grade();

        $data_produk = $this->m_penerimaanBarang->get_produk_add_quant($kode, $kode_produk, $origin_prod);
        $data['data_produk'] = $data_produk;
        if (strpos($data_produk->nama_category, 'Kain Hasil') !== false) {
            $data['hidden_field'] = 'No';
        } else {
            $data['hidden_field'] = 'Yes';
        }

        if (strpos($data_produk->nama_category, 'Benang') !== false) {
            $data['category_benang'] = 'Yes';
        } else {
            $data['category_benang'] = 'No';
        }

        if ($deptid == 'RCV') {
            return $this->load->view('modal/v_tambah_details_quant_penerimaan_2_modal', $data);
        } else {
            return $this->load->view('modal/v_tambah_details_quant_penerimaan_modal', $data);
        }
    }

    public function tambah_data_details_quant_penerimaan_modal() {
        $kode_produk = addslashes($this->input->post('kode_produk'));
        $move_id = $this->input->post('move_id');
        $origin = $this->input->post('origin');
        $deptid = $this->input->post('deptid');
        //lokasi tujuan, lokasi dari
        $lokasi = $this->m_penerimaanBarang->get_location_by_move_id($move_id)->row_array();

        $list = $this->m_penerimaanBarang->get_datatables3($kode_produk, $lokasi['lokasi_dari'], $origin, $deptid);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $field) {
            $no++;
            $row = array();
            $row[] = $no . ".";
            $row[] = $field->kode_produk;
            $row[] = $field->nama_produk;
            $row[] = $field->lot;
            $row[] = number_format($field->qty, 2) . " " . $field->uom;
            $row[] = number_format($field->qty2, 2) . " " . $field->uom2;
            $row[] = $field->nama_grade;
            $row[] = $field->reff_note;
            $row[] = $field->quant_id;
            //$row[] = '';//buat checkbox
            //$row[] = $field->kode_produk."|".htmlentities($field->nama_produk)."|".$field->lot."|".$field->qty."|".$field->uom."|".$field->qty2."|".$field->uom2."|".$field->lokasi."|".$field->quant_id."|^";

            $data[] = $row;
        }
        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_penerimaanBarang->count_all3($kode_produk, $lokasi['lokasi_dari'], $origin, $deptid),
            "recordsFiltered" => $this->m_penerimaanBarang->count_filtered3($kode_produk, $lokasi['lokasi_dari'], $origin, $deptid),
            "data" => $data,
        );
        //output dalam format JSON
        echo json_encode($output);
    }

    public function save_details_quant_penerimaan_modal() {
        $sub_menu = $this->uri->segment(2);
        $username = addslashes($this->session->userdata('username'));
        $deptid = $this->input->post('deptid');
        $kode = $this->input->post('kode');

        $cek_kirim = $this->m_penerimaanBarang->cek_status_barang($kode)->row_array();

        if (empty($this->session->userdata('status'))) { //cek apakah session masih ada
            // session habis
            $callback = array('status' => 'failed', 'message' => 'Waktu Anda Telah Habis', 'sesi' => 'habis');
        } else if ($cek_kirim['status'] == 'done') { //cek jika status penerimaan sudah terkirim
            $callback = array('status' => 'failed', 'message' => 'Maaf, Data Tidak Bisa Disimpan, Data Sudah Terkirim !', 'icon' => 'fa fa-warning', 'type' => 'danger');
        } else if ($cek_kirim['status'] == 'cancel') { //cek jika status penerimaan batal
            $callback = array('status' => 'failed', 'message' => 'Maaf, Data Tidak Bisa Disimpan, Data Penerimaan Sudah dibatalkan !', 'icon' => 'fa fa-warning', 'type' => 'danger');
        } else {

            $kode_produk = $this->input->post('kode_produk');
            $nama_produk = $this->input->post('nama_produk');
            $move_id = $this->input->post('move_id');
            $origin_prod = $this->input->post('origin_prod');
            $origin = $this->input->post('origin');
            $check = $this->input->post('checkbox');
            $countchek = $this->input->post('countchek');
            $sql_stock_move_items_batch = "";
            $tgl = date('Y-m-d H:i:s');
            //$row        = explode("^,", $check);
            $status = "";
            $status_brg = "ready";
            $case = "";
            $where = "";
            $case2 = "";
            $where2 = "";
            $kosong = false;

            //lock tabel
            $this->_module->lock_tabel('stock_quant WRITE, stock_move_items WRITE,stock_move WRITE,stock_move_produk WRITE, penerimaan_barang WRITE, penerimaan_barang_items WRITE, mrp_production_rm_target WRITE');
            //get row order stock_move_items
            $row_order = $this->_module->get_row_order_stock_move_items_by_kode($move_id);
            //get qty  produk penerimaan barang items 
            $get_qty = $this->m_penerimaanBarang->get_qty_penerimaan_barang_items_by_kode($kode, addslashes($kode_produk))->row_array();
            //get sum qty produk stock move items
            $get_qty2 = $this->_module->get_qty_stock_move_items_by_kode($move_id, addslashes($kode_produk))->row_array();
            //get last quant id
            $start = $this->_module->get_last_quant_id();
            //get_lokasi dari by move id 
            $location = $this->_module->get_location_by_move_id($move_id)->row_array();
            $no = 1;
            $list_product = '';

            foreach ($check as $data) {

                $cek_sq = $this->_module->get_stock_quant_by_id($data)->row_array();

                $quantid = $cek_sq['quant_id'];
                $kode_produk = $cek_sq['kode_produk'];
                $nama_produk = $cek_sq['nama_produk'];
                $lot = $cek_sq['lot'];
                $qty = $cek_sq['qty'];
                $uom = $cek_sq['uom'];
                $qty2 = $cek_sq['qty2'];
                $uom2 = $cek_sq['uom2'];
                $lokasi = $cek_sq['lokasi'];
                $nama_grade = $cek_sq['nama_grade'];
                $lokasi_fisik = $cek_sq['lokasi_fisik'];
                $lebar_greige = $cek_sq['lebar_greige'];
                $uom_lebar_greige = $cek_sq['uom_lebar_greige'];
                $lebar_jadi = $cek_sq['lebar_jadi'];
                $uom_lebar_jadi = $cek_sq['uom_lebar_jadi'];

                //cek product di stock quant
                $cq = $this->_module->cek_produk_di_stock_quant($quantid, $location['lokasi_dari'])->row_array();
                if (!empty($cq['quant_id']) and empty($cq['reserve_move'])) {

                    //insert ke stock move items
                    $sql_stock_move_items_batch .= "('" . $move_id . "', '" . $quantid . "','" . addslashes($kode_produk) . "', '" . addslashes($nama_produk) . "','" . addslashes(trim($lot)) . "','" . $qty . "','" . addslashes($uom) . "','" . $qty2 . "','" . addslashes($uom2) . "','ready','" . $row_order . "','" . addslashes($origin_prod) . "', '" . $tgl . "','" . addslashes($lokasi_fisik) . "','" . addslashes($lebar_greige) . "','" . addslashes($uom_lebar_greige) . "','" . addslashes($lebar_jadi) . "','" . addslashes($uom_lebar_jadi) . "'), ";
                    $row_order++;

                    //update reserve move by quant id di stok quant                
                    $case .= "when quant_id = '" . $quantid . "' then '" . $move_id . "'";
                    $where .= "'" . $quantid . "',";

                    $list_product .= "(" . $no . ") " . $kode_produk . " " . $nama_produk . " " . $lot . " " . $qty . " " . $uom . " " . $qty2 . " " . $uom2 . " " . $nama_grade . " <br>";
                    $no++;
                } else {
                    $kosong = true;
                }
            }
            /*
              for($i=0; $i <= $countchek-1;$i++){
              $dt1  =  $row[$i];
              $row2 = explode("|", $dt1);
              $quantid     = $row2[8];

              $kode_produk = $row2[0];
              $nama_produk = $row2[1];
              $lot         = $row2[2];
              $qty         = $row2[3];
              $uom         = $row2[4];
              $qty2        = $row2[5];
              $uom2        = $row2[6];
              $lokasi      = $row2[7];
              //$break   = false;

              //cek product di stock quant
              $cq = $this->_module->cek_produk_di_stock_quant($quantid,$location['lokasi_dari'])->row_array();
              if(!empty($cq['quant_id'])){


              //untuk mendapatkan origin_prod yang terdapat consume kedepannya atau consume lebih dari 1
              $loop_sm    = true;
              $origin_prod_tj = "";
              $con        = false;

              //get list stock_move by origin
              $list_sm = $this->_module->get_list_stock_move_origin($origin)->result_array();
              foreach ($list_sm as $row) {

              $mt = explode("|", $row['method']);
              $ex_deptid = $mt[0];
              $ex_mt     = $mt[1];

              if($loop_sm == true){

              if($ex_mt == 'CON' AND $ex_deptid == $deptid){

              //get  origin_prod by move id, kode_produk
              $get_origin_prod = $this->m_mo->get_origin_prod_mrp_production_by_kode($row['move_id'],addslashes($kode_produk))->row_array();
              $origin_prod_tj = $get_origin_prod['origin_prod'];
              $loop_sm =false;

              }

              }elseif($loop_sm == false){
              break;//paksa keluar looping
              }

              }


              if(!empty($origin_prod_tj)){
              $origin_prod = $origin_prod_tj; // origin prod berdasarkan
              }else{
              $origin_prod = '';
              }


              //insert ke stock move items
              $sql_stock_move_items_batch .= "('".$move_id."', '".$quantid."','".addslashes($kode_produk)."', '".addslashes($nama_produk)."','".addslashes(trim($lot))."','".$qty."','".addslashes($uom)."','".$qty2."','".addslashes($uom2)."','draft','".$row_order."','".addslashes($origin_prod)."', '".$tgl."'), ";
              $row_order++;

              //update reserve move by quant id di stok quant
              $case   .= "when quant_id = '".$quantid."' then '".$move_id."'";
              $where  .= "'".$quantid."',";

              }else{
              $kosong = true;
              }

              }
             */

            if (!empty($sql_stock_move_items_batch) and $kosong == false) {
                $sql_stock_move_items_batch = rtrim($sql_stock_move_items_batch, ', ');
                $this->_module->simpan_stock_move_items_batch($sql_stock_move_items_batch);

                if (!empty($case)) {
                    //update qty stock quant 
                    $where = rtrim($where, ',');
                    $sql_update_qty_stock_quant = "UPDATE stock_quant SET reserve_move =(case " . $case . " end) WHERE  quant_id in (" . $where . ") ";
                    $this->m_penerimaanBarang->update_perbatch($sql_update_qty_stock_quant);
                }

                if (!empty($case2)) {
                    //update qty stock quant 
                    $where2 = rtrim($where2, ',');
                    $sql_update_qty_stock_quant2 = "UPDATE stock_quant SET qty =(case " . $case2 . " end) WHERE  quant_id in (" . $where2 . ") ";
                    $this->m_penerimaanBarang->update_perbatch($sql_update_qty_stock_quant2);
                }

                $this->m_penerimaanBarang->update_status_penerimaan_barang_items($kode, addslashes($kode_produk), $status_brg);
                $this->_module->update_status_stock_move_items($move_id, addslashes($kode_produk), $status_brg);

                $cek_status = $this->m_penerimaanBarang->cek_status_barang_penerimaan_barang_items($kode, 'ready')->row_array();

                if (!empty($cek_status['status_barang'])) {
                    $this->m_penerimaanBarang->update_status_penerimaan_barang($kode, $status_brg);
                    $this->_module->update_status_stock_move_produk($move_id, addslashes($kode_produk), $status_brg);
                    $cek_status2 = $this->m_penerimaanBarang->cek_status_penerimaan_barang($kode)->row_array();
                    if ($cek_status2['status'] == 'ready') {
                        $this->_module->update_status_stock_move($move_id, $status_brg);
                    }
                }
            }

            //unlock table
            $this->_module->unlock_tabel();
            if ($kosong == false) {
                $jenis_log = "edit";
                $note_log = "Tambah Data Details -> <br>" . $list_product;
                $this->_module->gen_history_deptid($sub_menu, $kode, $jenis_log, addslashes($note_log), $username, $deptid);
                $callback = array('status' => 'success', 'message' => 'Detail Product Berhasil Ditambahkan !', 'icon' => 'fa fa-check', 'type' => 'success');
            } else {
                $callback = array('status' => 'kosong', 'message' => 'Maaf, Product Sudah ada yang terpakai !', 'icon' => 'fa fa-check', 'type' => 'danger');
            }
        }
        echo json_encode($callback);
    }

    public function hapus_details_items() {
        $sub_menu = $this->uri->segment(2);
        $username = addslashes($this->session->userdata('username'));
        $deptid = $this->input->post('deptid');
        $kode = $this->input->post('kode');

        $cek_kirim = $this->m_penerimaanBarang->cek_status_barang($kode)->row_array();

        $kode_menu = $this->_module->get_kode_sub_menu_deptid($sub_menu, $deptid)->row_array();
        $akses_menu = $this->_module->cek_priv_menu_by_user($username, $kode_menu['kode'])->num_rows();

        // cek level akses by user
        $level_akses = $this->_module->get_level_akses_by_user($username)->row_array();
        // cek departemen by user
        $cek_dept = $this->_module->cek_departemen_by_user($username)->row_array();

        if ($level_akses['level'] == 'Administrator' or $level_akses['level'] == 'Super Administrator') {
            $delete_items = true;
        } else if ($cek_dept['dept'] == 'QC' or strpos($cek_dept['dept'], 'PPIC') !== false) {
            $delete_items = true;
        } else {
            $delete_items = false;
        }


        if (empty($this->session->userdata('status'))) { //cek apakah session masih ada
            // session habis
            $callback = array('message' => 'Waktu Anda Telah Habis', 'sesi' => 'habis');
        } else if ($cek_kirim['status'] == 'done') { //cek jika status penerimaan sudah terkii
            $callback = array('status' => 'failed', 'message' => 'Maaf, Data Tidak Bisa Dihapus, Data Sudah Terkirim !', 'icon' => 'fa fa-warning', 'type' => 'danger');
        } else if ($cek_kirim['status'] == 'cancel') { //cek jika status penerimaan batal
            $callback = array('status' => 'failed', 'message' => 'Maaf, Data Tidak Bisa Dihapus, Data Penerimaan Sudah dibatalkan !', 'icon' => 'fa fa-warning', 'type' => 'danger');
        } else if ($delete_items == false or $akses_menu == 0) {
            $callback = array('status' => 'failed', 'message' => 'Maaf, Anda tidak punya akses untuk menghapus data !', 'icon' => 'fa fa-warning', 'type' => 'danger');
        } else {

            $quant_id = $this->input->post('quant_id');
            $row_order = $this->input->post('row_order');
            $move_id = $this->input->post('move_id');
            $kode_produk = addslashes($this->input->post('kode_produk'));
            $nama_produk = addslashes($this->input->post('nama_produk'));
            $origin_prod = $this->input->post('origin_prod');
            $status_brg = 'draft';

            // cek item by row
            $get_smi = $this->_module->get_stock_move_items_by_kode($move_id, $quant_id, $kode_produk, $row_order)->row_array();
            if (empty($get_smi)) {
                $callback = array('status' => 'failed', 'message' => 'Maaf, Product/Lot Tidak ditemukan !', 'icon' => 'fa fa-warning', 'type' => 'danger');
            } else {

                //lock tabel
                $this->_module->lock_tabel('stock_quant WRITE, stock_move WRITE,stock_move_items WRITE,stock_move_produk WRITE, penerimaan_barang WRITE, penerimaan_barang_items WRITE');

                //delete stock move item dan update reserve move jadi kosong
                $this->_module->delete_details_items($move_id, $quant_id, $row_order);

                // cek apakah terdapat kode_produk yg lebih dari 1
                $cek_jml_produk_sama = $this->m_penerimaanBarang->cek_jml_produk_sama_penerimaan_barang_by_kode($kode, $kode_produk)->num_rows();
                if ($cek_jml_produk_sama > 0) { // where ditambah origin_prod
                    $qty_smi = $this->_module->get_qty_stock_move_items_by_kode_origin($move_id, addslashes($kode_produk), $origin_prod)->row_array();
                } else {
                    //cek qty produk di stock_move_items apa masih kurang dengan target qty di pengiriman barang items
                    $qty_smi = $this->_module->get_qty_stock_move_items_by_kode($move_id, addslashes($kode_produk))->row_array();
                }

                //get sum qty produk stock move items
                // $get_qty2 = $this->_module->get_qty_stock_move_items_by_kode($move_id, $kode_produk)->row_array();
                //update status draft jika qty di stock move items kosong
                if (empty($qty_smi['sum_qty'])) {

                    if ($cek_jml_produk_sama > 0) {

                        $this->m_penerimaanBarang->update_status_penerimaan_barang_items_origin_prod($kode, addslashes($kode_produk), 'done', $origin_prod);
                        $this->_module->update_status_stock_move_produk_origin_prod($move_id, addslashes($kode_produk), 'done', $origin_prod);
                    } else {
                        $this->m_penerimaanBarang->update_status_penerimaan_barang_items($kode, $kode_produk, $status_brg);
                        $this->_module->update_status_stock_move_produk($move_id, $kode_produk, $status_brg);
                    }
                }

                $cek_status = $this->m_penerimaanBarang->cek_status_barang_penerimaan_barang_items($kode, 'ready')->row_array();
                if (empty($cek_status['status_barang'])) {
                    $this->m_penerimaanBarang->update_status_penerimaan_barang($kode, $status_brg);
                    $cek_status2 = $this->m_penerimaanBarang->cek_status_penerimaan_barang($kode)->row_array();
                    if ($cek_status2['status'] == 'draft') {
                        $this->_module->update_status_stock_move($move_id, $status_brg);
                    }
                }

                if (!empty($cek_status['status_barang'])) {
                    $this->m_penerimaanBarang->update_status_penerimaan_barang($kode, 'ready');
                    $cek_status2 = $this->m_penerimaanBarang->cek_status_penerimaan_barang($kode)->row_array();
                    if ($cek_status2['status'] == 'ready') {
                        $this->_module->update_status_stock_move($move_id, 'ready');
                    }
                }

                $cek_sq = $this->_module->get_stock_quant_by_id($quant_id)->row_array();
                $nama_grade = $cek_sq['nama_grade'];

                //unlock table
                $this->_module->unlock_tabel();

                $note_log_produk = $get_smi['origin_prod'] . ' ' . $get_smi['kode_produk'] . ' ' . $get_smi['nama_produk'] . ' ' . $get_smi['lot'] . ' ' . $get_smi['qty'] . ' ' . $get_smi['uom'] . ' ' . $get_smi['qty2'] . ' ' . $get_smi['uom2'] . ' ' . $nama_grade;

                $jenis_log = "cancel";
                $note_log = "Hapus Data Details - > <br>" . $note_log_produk;
                $this->_module->gen_history_deptid($sub_menu, $kode, $jenis_log, addslashes($note_log), $username, $deptid);

                $callback = array('status' => 'success', 'message' => 'Data Berhasil di hapus !', 'icon' => 'fa fa-check', 'type' => 'success');
            }
        }
        echo json_encode($callback);
    }

    public function cek_stok() {
        $sub_menu = $this->uri->segment(2);
        $username = addslashes($this->session->userdata('username'));
        $deptid = $this->input->post('deptid');

        if (empty($this->session->userdata('status'))) { //cek apakah session masih ada
            // session habis
            $callback = array('message' => 'Waktu Anda Telah Habis', 'sesi' => 'habis');
        } else {

            $kode = $this->input->post('kode');
            $move_id = $this->input->post('move_id');
            $origin = $this->input->post('origin');
            $status_brg = 'ready';
            $tgl = date('Y-m-d H:i:s');
            $sql_stock_quant_batch = "";
            $sql_stock_move_items_batch = "";
            $case = "";
            $where = "";
            $case2 = "";
            $where2 = "";
            $case3 = "";
            $where3 = "";
            $kurang = false;
            $produk_kurang = "";
            $kosong = true;
            $produk_kosong = "";
            $cukup = false;
            $produk_terpenuhi = "";
            $history = false;
            $qty2_new = "";
            $qty2_update = "";
            $case_qty2 = "";

            //cek status terkirim ?
            $cek_kirim = $this->m_penerimaanBarang->cek_status_barang($kode)->row_array();
            if ($cek_kirim['status'] == 'done') {
                $callback = array('status' => 'failed', 'message' => 'Maaf, Tidak Bisa Cek Stok, Data Sudah Terkirim !', 'icon' => 'fa fa-warning', 'type' => 'danger');
            } else if ($cek_kirim['status'] == 'cancel') {
                $callback = array('status' => 'failed', 'message' => 'Maaf, Tidak Bisa Cek Stok, Data Penerimaan Sudah Dibatalkan !', 'icon' => 'fa fa-warning', 'type' => 'danger');
            } else {

                //lock tabel
                $this->_module->lock_tabel('stock_quant WRITE, stock_move_items WRITE,stock_move WRITE,stock_move_produk WRITE, penerimaan_barang WRITE, penerimaan_barang_items WRITE, departemen WRITE, mrp_production_rm_target WRITE');

                //get row order stock_move_items
                $row_order = $this->_module->get_row_order_stock_move_items_by_kode($move_id);
                //lokasi tujuan, lokasi dari
                $lokasi = $this->m_penerimaanBarang->get_location_by_move_id($move_id)->row_array();

                $list = $this->m_penerimaanBarang->get_list_penerimaan_barang_items($kode);
                // log_message('error',json_encode($list));
                foreach ($list as $val) {
                    $kode_produk = $val->kode_produk;
                    $nama_produk = $val->nama_produk;
                    $qty = $val->qty;
                    $uom = $val->uom;
                    $ro_items = $val->row_order;
                    $origin_prod = $val->origin_prod;

                    //get last quant id
                    $start = $this->_module->get_last_quant_id();

                    //cek qty produk di stock_move_items apa masih kurang dengan target qty di penerimaan barang items
                    //$qty_smi = $this->_module->get_qty_stock_move_items_by_kode($move_id,addslashes($kode_produk))->row_array();
                    // cek apakah terdapat kode_produk yg lebih dari 1
                    $cek_jml_produk_sama = $this->m_penerimaanBarang->cek_jml_produk_sama_penerimaan_barang_by_kode($kode, $kode_produk)->num_rows();
                    if ($cek_jml_produk_sama > 0) { // where ditambah origin_prod
                        $qty_smi = $this->_module->get_qty_stock_move_items_by_kode_origin($move_id, addslashes($kode_produk), $origin_prod)->row_array();
                    } else {
                        //cek qty produk di stock_move_items apa masih kurang dengan target qty di pengiriman barang items
                        $qty_smi = $this->_module->get_qty_stock_move_items_by_kode($move_id, addslashes($kode_produk))->row_array();
                    }

                    $kebutuhan_qty = $qty - $qty_smi['sum_qty'];

                    if ($kebutuhan_qty > 0) { //jika kebutuhan_qty > 0
                        $ceK_quant = $this->_module->get_cek_stok_quant_by_prod(addslashes($kode_produk), $lokasi['lokasi_dari'], $origin, $deptid)->result_array();
//                        log_message('error', json_encode($ceK_quant));

                        foreach ($ceK_quant as $stock) {
                            $kosong = false;
                            $history = true;

                            /*
                              //untuk mendapatkan origin_prod yang terdapat consume kedepannya atau consume lebih dari 1
                              $loop_sm    = true;
                              $origin_prod_tj = "";
                              $con        = false;

                              //get list stock_move by origin
                              $list_sm = $this->_module->get_list_stock_move_origin($origin)->result_array();
                              foreach ($list_sm as $row) {

                              $mt = explode("|", $row['method']);
                              $ex_deptid = $mt[0];
                              $ex_mt     = $mt[1];

                              if($loop_sm == true){

                              if($ex_mt == 'CON' AND $ex_deptid == $deptid){

                              //get  origin_prod by move id, kode_produk
                              $get_origin_prod = $this->m_mo->get_origin_prod_mrp_production_by_kode($row['move_id'],addslashes($kode_produk))->row_array();
                              $origin_prod_tj = $get_origin_prod['origin_prod'];
                              $loop_sm =false;

                              }

                              }elseif($loop_sm == false){
                              break;//paksa keluar looping
                              }

                              }

                              if(!empty($origin_prod_tj)){
                              $origin_prod = $origin_prod_tj; // origin prod berdasarkan
                              }else{
                              $origin_prod = '';
                              }
                             */


                            if ($kebutuhan_qty >= $stock['qty']) { //jika kebutuhan_qty lebih atau sama dengan qty di stock_quant
                                //update reserve_move dengan move_id
                                $case2 .= "when quant_id = '" . $stock['quant_id'] . "' then '" . $move_id . "'";
                                $where2 .= "'" . $stock['quant_id'] . "',";

                                //insert stock move items batch
                                $sql_stock_move_items_batch .= "('" . $move_id . "', '" . $stock['quant_id'] . "','" . addslashes($kode_produk) . "', '" . addslashes($nama_produk) . "','" . addslashes($stock['lot']) . "','" . $stock['qty'] . "','" . addslashes($uom) . "','" . $stock['qty2'] . "','" . addslashes($stock['uom2']) . "','" . $status_brg . "','" . $row_order . "','" . addslashes($origin_prod) . "', '" . $tgl . "','" . addslashes($stock['lokasi_fisik']) . "','" . addslashes($stock['lebar_greige']) . "','" . addslashes($stock['uom_lebar_greige']) . "','" . addslashes($stock['lebar_jadi']) . "','" . addslashes($stock['uom_lebar_jadi']) . "'), ";
                                $row_order++;
                                $kebutuhan_qty = $kebutuhan_qty - $stock['qty'];
                            } else if ($kebutuhan_qty < $stock['qty']) { //jika kebutuhan_qty kurang dari qty di stock_quant
                                $qty_new = $stock['qty'] - $kebutuhan_qty; //qty baru di stock quant
                                //update qty produk di stock_quant
                                $case .= "when quant_id = '" . $stock['quant_id'] . "' then '" . $qty_new . "'";
                                $where .= "'" . $stock['quant_id'] . "',";

                                $qty2_new = ($stock['qty2'] / $stock['qty']) * $kebutuhan_qty;
                                $qty2_update = $stock['qty2'] - $qty2_new;
                                $case_qty2 .= "when quant_id = '" . $stock['quant_id'] . "' then '" . $qty2_update . "'";

                                //insert qty stock_quant_batch dengan quant_id baru 
                                $sql_stock_quant_batch .= "('" . $start . "','" . $tgl . "', '" . addslashes($kode_produk) . "', '" . addslashes($nama_produk) . "','" . addslashes($stock['lot']) . "','" . addslashes($stock['nama_grade']) . "','" . $kebutuhan_qty . "','" . addslashes($uom) . "','" . $qty2_new . "','" . addslashes($stock['uom2']) . "','" . $lokasi['lokasi_dari'] . "','" . addslashes($stock['reff_note']) . "','" . $move_id . "','" . $stock['reserve_origin'] . "','" . $tgl . "','" . addslashes($stock['lebar_greige']) . "','" . addslashes($stock['uom_lebar_greige']) . "','" . addslashes($stock['lebar_jadi']) . "','" . addslashes($stock['uom_lebar_jadi']) . "','" . addslashes($stock['sales_order']) . "','" . addslashes($stock['sales_group']) . "'), ";
                                //insert stock move items batch
                                $sql_stock_move_items_batch .= "('" . $move_id . "', '" . $start . "','" . addslashes($kode_produk) . "', '" . addslashes($nama_produk) . "','" . addslashes($stock['lot']) . "','" . ($kebutuhan_qty) . "','" . addslashes($uom) . "','" . $qty2_new . "','" . addslashes($stock['uom2']) . "','" . $status_brg . "','" . $row_order . "','" . addslashes($origin_prod) . "', '" . $tgl . "','" . addslashes($stock['lokasi_fisik']) . "','" . addslashes($stock['lebar_greige']) . "','" . addslashes($stock['uom_lebar_greige']) . "','" . addslashes($stock['lebar_jadi']) . "','" . addslashes($stock['uom_lebar_jadi']) . "'), ";
                                $row_order++;
                                $start++;
                                $kebutuhan_qty = 0;
                            }

                            //update status di pengiriman_barang_items dan stock_move_produk jadi ready
                            $case3 .= "when kode_produk = '" . addslashes($kode_produk) . "' then '" . $status_brg . "'";
                            $where3 .= "'" . addslashes($kode_produk) . "',";
                            //untuk memotong proses looping ketika kebutuhan_qty == 0
                            if ($kebutuhan_qty == 0) {
                                break;
                            }
                            // log_message('error',$sql_stock_move_items_batch);
                        } //end foreach cek_quant

                        if ($kebutuhan_qty > 0) {
                            $kurang = true;
                            $produk_kurang .= $nama_produk . ', ';
                        }
                        if ($kosong == true) { //jika qty di stock_quant_kosong/blm terisi
                            $produk_kosong .= $nama_produk . ', ';
                        }
                    } else { //jik kebutuhan_qty <= 0
                        $cukup = true;
                        $produk_terpenuhi .= $nama_produk . ', ';
                    }



                    if (!empty($sql_stock_quant_batch)) {
                        $sql_stock_quant_batch = rtrim($sql_stock_quant_batch, ', ');
                        $this->_module->simpan_stock_quant_batch($sql_stock_quant_batch);

                        $sql_stock_quant_batch = "";
                    }

                    if (!empty($sql_stock_move_items_batch)) {
                        $sql_stock_move_items_batch = rtrim($sql_stock_move_items_batch, ', ');
                        $this->_module->simpan_stock_move_items_batch($sql_stock_move_items_batch);

                        $sql_stock_move_items_batch = "";
                    }

                    //update reserve_move di stock_quant
                    if (!empty($where2) and !empty($case2)) {
                        $where2 = rtrim($where2, ',');
                        $sql_update_reserve_move = "UPDATE stock_quant SET reserve_move =(case " . $case2 . " end) WHERE  quant_id in (" . $where2 . ") ";
                        $this->m_penerimaanBarang->update_perbatch($sql_update_reserve_move);

                        $sql_update_reserve_move = "";
                        $where2 = "";
                        $case2 = "";
                    }

                    //update qty baru di stock quant 
                    if (!empty($where) and !empty($case)) {
                        $where = rtrim($where, ',');
                        $sql_update_qty_stock = "UPDATE stock_quant SET qty =(case " . $case . " end), qty2 =(case " . $case_qty2 . " end)  WHERE  quant_id in (" . $where . ") ";
                        $this->m_penerimaanBarang->update_perbatch($sql_update_qty_stock);

                        $sql_update_qty_stock = "";
                        $where = "";
                        $case = "";
                    }

                    if (!empty($where3) and !empty($case3)) {
                        $where3 = rtrim($where3, ',');
                        $sql_update_status_penerimaan_items = "UPDATE penerimaan_barang_items SET status_barang =(case " . $case3 . " end) WHERE  kode_produk in (" . $where3 . ") AND kode = '" . $kode . "' ";
                        $this->m_penerimaanBarang->update_perbatch($sql_update_status_penerimaan_items);

                        $sql_update_status_stock_move_produk = "UPDATE stock_move_produk SET status =(case " . $case3 . " end) WHERE  kode_produk in (" . $where3 . ") AND move_id = '" . $move_id . "' ";
                        $this->m_penerimaanBarang->update_perbatch($sql_update_status_stock_move_produk);

                        $sql_update_penerimaan_barang_items = "";
                        $sql_update_status_stock_move_produk = "";
                        $where3 = "";
                        $case3 = "";
                    }
                } // end foreach list penerimaan barang
                //cek apa ada items yang status nya ready?
                $all_produk_items = $this->m_penerimaanBarang->cek_status_barang_penerimaan_barang_items($kode, 'ready')->row_array();

                //jika tidak kosong maka update status di penerimaan brg
                if (!empty($all_produk_items['status_barang'])) {
                    $this->m_penerimaanBarang->update_status_penerimaan_barang($kode, $status_brg);
                }

                $cek_status2 = $this->m_penerimaanBarang->cek_status_penerimaan_barang($kode)->row_array();
                if ($cek_status2['status'] == 'ready') {
                    $this->_module->update_status_stock_move($move_id, $status_brg);
                }


                //unlock table
                $this->_module->unlock_tabel();

                if (!empty($produk_kosong)) {
                    $callback = array('status' => 'failed', 'message' =>
                        'Maaf, Qty Product "<b>' . $produk_kosong . '</b>" Kosong !', 'icon' => 'fa fa-warning', 'type' => 'danger');
                } else if (!empty($produk_kurang)) {
                    $callback = array('status' => 'failed', 'message' =>
                        'Maaf, Qty Product "<b>' . $produk_kurang . '</b>" tidak mencukupi !', 'icon' => 'fa fa-warning', 'type' => 'danger', 'status_kurang' => 'yes', 'message2' => 'Detail Product Berhasil Ditambahkan !', 'icon2' => 'fa fa-check', 'type2' => 'success');
                    /*
                      }else if(!empty($produk_terpenuhi)){
                      $callback = array('status' => 'failed', 'message'=>
                      'Qty Product "'.  $produk_terpenuhi  .'" Sudah Terpenuhi !', 'icon' => 'fa fa-warning', 'type'=>'danger');
                     */
                } else {

                    if (!empty($produk_terpenuhi)) {
                        $callback = array('status' => 'success', 'message' => 'Detail Product Berhasil Ditambahkan !', 'icon' => 'fa fa-check', 'type' => 'success', 'terpenuhi' => 'yes');
                    } else {
                        $callback = array('status' => 'success', 'message' => 'Detail Product Berhasil Ditambahkan !', 'icon' => 'fa fa-check', 'type' => 'success');
                    }
                }

                if ($history == true) {
                    $jenis_log = "edit";
                    $note_log = "Cek Stok";
                    $this->_module->gen_history_deptid($sub_menu, $kode, $jenis_log, $note_log, $username, $deptid);
                }
            } //end if cek status penerimaan barang
        }

        echo json_encode($callback);
    }

    function valid_barcode_in() {

        if (empty($this->session->userdata('username'))) { //cek apakah session masih ada
            // session habis
            $callback = array('message' => 'Waktu Anda Telah Habis', 'sesi' => 'habis');
        } else {

            $sub_menu = $this->uri->segment(2);
            $username = addslashes($this->session->userdata('username'));

            $deptid = $this->input->post('deptid');
            $kode = addslashes($this->input->post('kode'));
            $txtbarcode = $this->input->post('txtbarcode');
            $tgl = date('Y-m-d H:i:s');

            // lock table
            $this->_module->lock_tabel('stock_move as sm WRITE, stock_move_items WRITE, penerimaan_barang as pb WRITE, penerimaan_barang_tmp WRITE, penerimaan_barang WRITE, log_history WRITE, main_menu_sub WRITE, user WRITE');

            //cek status terkirim ?
            $cek_kirim = $this->m_penerimaanBarang->cek_status_barang($kode)->row_array();
            if ($cek_kirim['status'] == 'draft') {
                $callback = array('status' => 'ada', 'message' => 'Maaf, Product yang akan di Scan belum ready !', 'icon' => 'fa fa-warning', 'type' => 'danger');
            } elseif ($cek_kirim['status'] == 'done') {
                $callback = array('status' => 'ada', 'message' => 'Maaf, Data Sudah Terkirim !', 'icon' => 'fa fa-warning', 'type' => 'danger');
            } else if ($cek_kirim['status'] == 'cancel') {
                $callback = array('status' => 'failed', 'message' => 'Maaf, Data Tidak Bisa Dikirim, Data Sudah dibatalkan !', 'icon' => 'fa fa-warning', 'type' => 'danger');
            } else {
                // cek lo apa sudah di scan / belum
                $ck_scan = $this->m_penerimaanBarang->cek_scan_by_lot($kode, $txtbarcode)->row_array();
                if (!empty($ck_scan['lot'])) { // jika tidak koosong
                    $callback = array('status' => 'failed', 'message' => 'Barcode ' . $txtbarcode . ' Sudah di Scan !', 'icon' => 'fa fa-warning', 'type' => 'danger');
                } else {

                    $mv = $this->m_penerimaanBarang->get_move_id_by_kode($kode)->row_array();

                    // get list tmp penerimaan barang by lot yg ready
                    $tmp = $this->m_penerimaanBarang->get_list_stock_move_items_by_lot($mv['move_id'], $txtbarcode, 'ready');
                    $empty = true;
                    foreach ($tmp as $row) {
                        $empty = false;
                        // insert topenerimaan barang tmp
                        $this->m_penerimaanBarang->simpan_penerimaan_barang_tmp($kode, $row->quant_id, $mv['move_id'], $row->kode_produk, $row->lot, 't', $tgl);
                    }

                    if ($empty == true) {
                        $callback = array('status' => 'failed', 'message' => 'Barcode ' . $txtbarcode . ' Tidak valid  !', 'icon' => 'fa fa-warning', 'type' => 'danger');
                    } else {

                        $jenis_log = "edit";
                        $note_log = "Scan Barcode " . $txtbarcode;
                        $this->_module->gen_history_deptid($sub_menu, $kode, $jenis_log, $note_log, $username, $deptid);

                        $callback = array('status' => 'success', 'message' => 'Barcode ' . $txtbarcode . ' Valid Scan !', 'icon' => 'fa fa-check', 'type' => 'success');
                    }
                }
            }

            //unlock table            
            $this->_module->unlock_tabel();
        }
        echo json_encode($callback);
    }

    function print_penerimaan_barang_rcv() {
        try {
            $users = $this->session->userdata('nama');
            $connector = new DummyPrintConnector();
            $printer = new Printer($connector);

            $printers = $this->session->userdata('printer');
            if ($printers === null) {
                throw new \exception("Printer Direct belum ditentukan, silakan pilih pada tab atas", 500);
            }
            $printers = json_decode($printers);

            $dept_id = $this->input->get('departemen');
            $kode = $this->input->get('kode');

            $origin = '';
            $tanggal = '';
            $reff_picking = '';
            $tanggal_transaksi = '';
            $tanggal_jt = '';

            //            $dept = $this->_module->get_nama_dept_by_kode($dept_id)->row_array();
            //            $head = $this->m_penerimaanBarang->get_data_by_code_print($kode, $dept_id);
            $modelHead = new $this->m_po;
            $head = $modelHead->setTables("penerimaan_barang")->setWheres(["kode" => $kode, "dept_id" => $dept_id])
                            ->setJoins('partner', "partner_id = partner.id", "left")->setOrder(["kode"])
                            ->setSelects(["penerimaan_barang.*", "concat(partner.delivery_street,' ',partner.delivery_city) as alamat"])->getDetail();

            if (!empty($head)) {
                $kode = $head->kode;
                $origin = $head->origin;
                $tanggal = $head->tanggal;
                $reff_picking = $head->reff_picking;
                $tanggal_transaksi = $head->tanggal_transaksi;
                $tanggal_jt = $head->tanggal_jt;
            }
            //            $nama_dept = strtoupper($dept['nama']);
            //            $printer->setTextSize(2, 2);
            $buff = $printer->getPrintConnector();

            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $buff->write("\x1bE" . chr(1));
            $printer->text("BUKTI TERIMA BARANG (BTB)\n");
            $buff->write("\x1bF" . chr(0));
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            //            $printer->feed();
            $buff->write("\x1bX" . chr(15));
            $printer->text(str_pad("Kode", 12));
            $printer->text(str_pad(":{$kode}", 50));
            $printer->text(str_pad("No.SJ", 12));
            $printer->text(str_pad(":{$head->no_sj}", 50));
            $printer->feed();
            $printer->text(str_pad("Tgl.Terima", 12));
            $printer->text(str_pad(":{$head->tanggal_transaksi}", 50));
            $printer->text(str_pad("Tgl.SJ", 12));
            $printer->text(str_pad(":{$head->tanggal_sj}", 50));
            $printer->feed();
            $printer->text(str_pad("Origin", 12));
            $printer->text(str_pad(":{$head->origin}", 50));
            $printer->text(str_pad("Supplier", 12));
            $printer->text(str_pad(":$head->nama_partner", 50));
            $printer->feed();
            $printer->text(str_pad("Tgl.Dibuat", 12));
            $printer->text(str_pad(":{$head->tanggal}", 50));
            $printer->text(str_pad("", 12));
            $splitAlamat = str_split($head->alamat, 50);
//            $splitAlamat = str_split("TES PRNT UNTUK ALAMT DI BANDUNG TES BANDUNG", 30);
            foreach ($splitAlamat as $key => $value) {
                $printer->text(str_pad($value, 50));
                if (count($splitAlamat) > ($key + 1)) {
                    $printer->feed();
                    $printer->text(str_pad("", 62));
                }
            }
            $printer->feed();

            $printer->text(str_pad("", 12));
            $printer->text(str_pad("", 50));
            $printer->text(str_pad("Reff Note :", 12));
            $splitNotes = str_split($head->reff_note, 50);

            foreach ($splitNotes as $key => $value) {
                $printer->text(str_pad($value, 50));
                $printer->feed();
                $printer->text(str_pad("", 50));
            }

            $printer->feed();
            $printer->setUnderline(Printer::UNDERLINE_SINGLE);
            $printer->text(str_pad("NO", 3) . str_pad("Kode Produk", 11, " ", STR_PAD_RIGHT) . str_pad("Nama Produk", 40, " ", STR_PAD_BOTH) . str_pad("LOT", 25, " ", STR_PAD_RIGHT)
                    . str_pad("Qty", 12, " ", STR_PAD_RIGHT) . str_pad("Uom", 5) . str_pad("Reff Note", 41));
            $printer->setUnderline(Printer::UNDERLINE_NONE);

            $printer->feed();
//            $kode_pp = '';
            // products
//            $items = $this->m_penerimaanBarang->get_stock_move_items_by_kode_print($kode, $dept_id);
            $modelItems = new $this->m_global;
            $items = $modelItems->setTables("stock_move_items smi")
                            ->setJoins("penerimaan_barang pb", "smi.move_id = pb.move_id")
                            ->setJoins("stock_quant sq", "smi.quant_id = sq.quant_id")
                            ->setJoins("penerimaan_barang_items pbi", "pbi.kode = pb.kode and pbi.origin_prod = smi.origin_prod")
                            ->setJoins("nilai_konversi nk", "nk.id = pbi.id_konversiuom", "left")
                            ->setWheres(["pb.kode" => $kode, "pb.dept_id" => $dept_id])
                            ->setOrder(["smi.row_order"])
                            ->setSelects([
                                "smi.quant_id, smi.move_id, smi.kode_produk, smi.nama_produk, smi.lot, smi.qty",
                                "smi.uom, smi.qty2, smi.uom2, smi.status, smi.row_order, sq.reff_note", "nilai_konversiuom as nilai,pbi.uom_beli",
                                "konversi_aktif", "pembilang", "penyebut"])
                            ->setGroups(["smi.quant_id"])->getData();
            foreach ($items as $keyss => $item) {
                $kodeProduk = str_split($item->kode_produk, 11);
                foreach ($kodeProduk as $key => $value) {
                    $value = trim($value);
                    $kodeProduk[$key] = $value;
                }

                $namaProduk = str_split(" ".$item->nama_produk, 40);
                foreach ($namaProduk as $key => $value) {
                    $value = trim($value);
                    $namaProduk[$key] = $value;
                }

                $lot = str_split(" {$item->lot}", 25);
                foreach ($lot as $key => $value) {
                    $value = trim($value);
                    $lot[$key] = $value;
                }

                if ($item->konversi_aktif === "1") {
                    $item->qty = ($item->pembilang / $item->penyebut) * $item->qty;
                } else {
                    $item->qty = $item->qty / $item->nilai;
                }
                $qty = str_split(" ".number_format($item->qty, 2), 12);
                foreach ($qty as $key => $value) {
                    $value = trim($value);
                    $qty[$key] = $value;
                }

                $uom = str_split(" ".(($item->nilai !== null || $item->nilai !== "") ? $item->uom_beli : $item->uom), 5);
                foreach ($uom as $key => $value) {
                    $value = trim($value);
                    $uom[$key] = $value;
                }

                $reff = str_split(" ".$item->reff_note, 41);
                foreach ($reff as $key => $value) {
                    $value = trim($value);
                    $reff[$key] = $value;
                }

                $noo = str_split(($keyss + 1), 3);
                foreach ($noo as $key => $value) {
                    $value = trim($value);
                    $noo[$key] = $value;
                }

                $counter = 0;
                $temp = [];
                $temp[] = count($noo);
                $temp[] = count($kodeProduk);
                $temp[] = count($namaProduk);
                $temp[] = count($lot);
                $temp[] = count($qty);
                $temp[] = count($uom);
                $temp[] = count($reff);
                $counter = max($temp);

                for ($i = 0; $i < $counter; $i++) {
                    $line = (isset($noo[$i])) ? str_pad($noo[$i], 3) : str_pad("", 3);

                    $line .= (isset($kodeProduk[$i])) ? str_pad($kodeProduk[$i], 11, " ", STR_PAD_RIGHT) : str_pad("", 11, " ", STR_PAD_RIGHT);
                    $line .= (isset($namaProduk[$i])) ? str_pad($namaProduk[$i], 40, " ", STR_PAD_RIGHT) : str_pad("", 40, " ", STR_PAD_RIGHT);
                    $line .= (isset($lot[$i])) ? str_pad($lot[$i], 25, " ", STR_PAD_RIGHT) : str_pad("", 25, " ", STR_PAD_RIGHT);
                    $line .= (isset($qty[$i])) ? str_pad($qty[$i], 12, " ", STR_PAD_RIGHT) : str_pad("", 12, " ", STR_PAD_RIGHT);
                    $line .= (isset($uom[$i])) ? str_pad($uom[$i], 5) : str_pad("", 5);
                    $line .= (isset($reff[$i])) ? str_pad($reff[$i], 41, " ", STR_PAD_RIGHT) : str_pad("", 41, " ", STR_PAD_RIGHT);

                    $printer->text($line . "\n");
                }
            }

            $modelItemsKodePP = new $this->m_global;
            $kodePP = $modelItemsKodePP->setTables("stock_move_items smi")
                            ->setJoins("penerimaan_barang pb", "smi.move_id = pb.move_id")
                            ->setJoins("penerimaan_barang_items pbi", "pbi.kode = pb.kode and pbi.origin_prod = smi.origin_prod")
                            ->setWheres(["pb.kode" => $kode, "pb.dept_id" => $dept_id])
                            ->setOrder(["smi.row_order"])->setSelects(["group_concat(DISTINCT(pbi.kode_pp)) as kode_pp"])->getDetail();
            $printer->feed();
            $printer->feed();

            // kode pp
            $printer->text(str_pad("kode PP : ", 70) . str_pad("Tgl.Cetak :" . date('Y-m-d H:i:s'), 50, " ", STR_PAD_RIGHT));
            // $printer->text("Tgl.Cetak :" . date("Y-m-d H:i:s"));
            $printer->feed();
            $splitKodePP = str_split(($kodePP->kode_pp ?? ""), 70);
            foreach ($splitKodePP as $key => $value) {
                $printer->text(str_pad($value, 70));
                $printer->feed();
//                $printer->text(str_pad("", 70));
            }
            $splitRcv = str_split($users["nama"], 30);
            $printer->setJustification(Printer::JUSTIFY_RIGHT);
            $printer->feed();
            $printer->feed();
            $printer->text(str_pad("Pembelian", 20) . " " . str_pad("Gudang", 20) . " " . str_pad("Receiveing", 30));
            $printer->feed();
            $printer->feed();
            $printer->feed();
            $printer->feed();
            $linesTtd = "";
            foreach ($splitRcv as $key => $value) {
                if ($key === 0) {
                    $linesTtd .= str_pad("(__________________)", 20) . str_pad("(__________________) ", 20) . " " . str_pad($value, 30, " ", STR_PAD_RIGHT);
                } else {
                    $linesTtd .= "\n";
                    $linesTtd .= str_pad("", 20) . str_pad("", 20) . " " . str_pad($value, 30, " ", STR_PAD_RIGHT);
                }
            }
            $printer->text($linesTtd . "\n");
            $printer->feed();
            $printer->feed();
            $datas = $connector->getData();
            $printer->close();
            $client = new GuzzleHttp\Client();
            $resp = $client->request("POST", $this->config->item('url_web_print'), [
                "form_params" => [
                    "data" => $datas,
                    "printer" => "\\\\{$printers->ip_share}\\{$printers->nama_printer_share}"
                ]
            ]);
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success')));
        } catch (\Exception $ex) {
            log_message('error', $ex->getMessage());
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        } finally {
            $printer->close();
        }
    }

    function print_penerimaan_barang() {

        $this->load->library('Pdf'); //load library pdf

        $dept_id = $this->input->get('departemen');
        $kode = $this->input->get('kode');

        $origin = '';
        $tanggal = '';
        $reff_picking = '';
        $tanggal_transaksi = '';
        $tanggal_jt = '';

        $dept = $this->_module->get_nama_dept_by_kode($dept_id)->row_array();
        $head = $this->m_penerimaanBarang->get_data_by_code_print($kode, $dept_id);

        if (!empty($head)) {
            $kode = $head->kode;
            $origin = $head->origin;
            $tanggal = $head->tanggal;
            $reff_picking = $head->reff_picking;
            $tanggal_transaksi = $head->tanggal_transaksi;
            $tanggal_jt = $head->tanggal_jt;
        }

        $nama_dept = strtoupper($dept['nama']);
        $pdf = new PDF_Code128('P', 'mm', 'A4');
        //$pdf = new PDF_Code128('l','mm',array(210,148.5));

        $pdf->SetMargins(0, 0, 0);
        $pdf->SetAutoPageBreak(False);
        $pdf->AddPage();
        $pdf->setTitle('Penerimaan Barang : ' . $nama_dept);

        $pdf->SetFont('Arial', 'B', 9, 'C');
        $pdf->Cell(0, 10, 'PENERIMAAN BARANG ' . $nama_dept, 0, 0, 'C');

        $pdf->SetFont('Arial', '', 7, 'C');

        $pdf->setXY(160, 3);
        $tgl_now = tgl_indo(date('d-m-Y H:i:s'));
        $pdf->Multicell(50, 4, 'Tgl.Cetak : ' . $tgl_now, 0, 'C');

        $pdf->SetFont('Arial', 'B', 8, 'C');

        // caption kiri
        $pdf->setXY(5, 10);
        $pdf->Multicell(15, 4, 'Kode ', 0, 'L');

        $pdf->setXY(5, 13);
        $pdf->Multicell(15, 4, 'Tgl.buat ', 0, 'L');

        $pdf->setXY(5, 16);
        $pdf->Multicell(15, 4, 'Origin ', 0, 'L');

        $pdf->setXY(19, 10);
        $pdf->Multicell(5, 4, ':', 0, 'L');
        $pdf->setXY(19, 13);
        $pdf->Multicell(5, 4, ':', 0, 'L');
        $pdf->setXY(19, 16);
        $pdf->Multicell(5, 4, ':', 0, 'L');

        // isi kiri
        $pdf->SetFont('Arial', '', 8, 'C');

        $pdf->setXY(20, 10);
        $pdf->Multicell(40, 4, $kode, 0, 'L');
        $pdf->setXY(20, 13);
        $pdf->Multicell(40, 4, $tanggal, 0, 'L');
        $pdf->setXY(20, 16);
        $pdf->Multicell(70, 4, $origin, 0, 'L');

        $pdf->SetFont('Arial', 'B', 8, 'C');
        // caption tengah
        $pdf->setXY(60, 10);
        $pdf->Multicell(25, 4, 'Reff Picking ', 0, 'L');
        $pdf->setXY(60, 13);
        $pdf->Multicell(25, 4, 'Tgl.kirim ', 0, 'L');
        $pdf->setXY(60, 16);
        $pdf->Multicell(25, 4, 'Tgl.Jatuh Tempo ', 0, 'L');

        $pdf->setXY(85, 10);
        $pdf->Multicell(5, 4, ':', 0, 'L');
        $pdf->setXY(85, 13);
        $pdf->Multicell(5, 4, ':', 0, 'L');
        $pdf->setXY(85, 16);
        $pdf->Multicell(5, 4, ':', 0, 'L');

        // isi tengah
        $pdf->SetFont('Arial', '', 8, 'C');

        $pdf->setXY(86, 10);
        $pdf->Multicell(60, 4, $reff_picking, 0, 'L');
        $pdf->setXY(86, 13);
        $pdf->Multicell(40, 4, $tanggal_transaksi, 0, 'L');
        $pdf->setXY(86, 16);
        $pdf->Multicell(70, 4, $tanggal_jt, 0, 'L');

        // header table product
        $pdf->SetFont('Arial', 'B', 8, 'C');
        $pdf->setXY(5, 23);
        $pdf->Multicell(52, 4, 'Produk', 0, 'L');

        $pdf->setXY(5, 27);
        $pdf->Cell(7, 5, 'No.', 1, 0, 'L');
        $pdf->Cell(20, 5, 'Kode Produk', 1, 0, 'C');
        $pdf->Cell(70, 5, 'Nama Produk', 1, 0, 'C');
        $pdf->Cell(25, 5, 'Qty', 1, 0, 'R');
        $pdf->Cell(10, 5, 'Uom', 1, 0, 'C');
        $pdf->Cell(18, 5, 'Tersedia', 1, 0, 'C');

        // products
        $items = $this->m_penerimaanBarang->get_list_penerimaan_barang_print($kode, $dept_id);
        $x = 5;
        $y = 32;
        $no = 1;
        foreach ($items as $row) {

            // set font tbody =
            $pdf->SetFont('Arial', '', 8, 'C');

            $pdf->setXY($x, $y);
            $pdf->Multicell(7, 5, $no, 1, 'L');
            $pdf->setXY($x + 7, $y);
            $pdf->Multicell(20, 5, $this->custom_char_in($row->kode_produk, 8), 1, 'L');
            $pdf->setXY($x + 27, $y);
            $pdf->Multicell(70, 5, $this->custom_char_in($row->nama_produk, 45), 1, 'L');
            $pdf->setXY($x + 97, $y);
            $pdf->Multicell(25, 5, number_format($row->qty, 2), 1, 'R');
            $pdf->setXY($x + 122, $y);
            $pdf->Multicell(10, 5, $this->custom_char_in($row->uom, 3), 1, 'L');
            $pdf->setXY($x + 132, $y);
            $pdf->Multicell(18, 5, number_format($row->sum_qty, 2), 1, 'R');

            $no++;
            $y = $y + 5;

            if ($y > 290) {
                $pdf->AddPage();
                $y = 7;
                $pdf->SetFont('Arial', '', 7, 'C');
                $pdf->setXY(160, 3);
                $tgl_now = tgl_indo(date('d-m-Y H:i:s'));
                $pdf->Multicell(50, 4, 'Tgl.Cetak : ' . $tgl_now, 0, 'C');
            }
        }

        $y = $y + 5;

        // header table details
        $pdf->SetFont('Arial', 'B', 8, 'C');
        $pdf->setXY(5, $y);
        $pdf->Multicell(52, 4, 'Detail Produk', 0, 'L');

        $pdf->setXY(5, $y + 5);
        $pdf->Cell(7, 5, 'No.', 1, 0, 'L');
        $pdf->Cell(20, 5, 'Kode Produk', 1, 0, 'C');
        $pdf->Cell(70, 5, 'Nama Produk', 1, 0, 'C');
        $pdf->Cell(30, 5, 'Lot', 1, 0, 'C');
        $pdf->Cell(15, 5, 'Qty', 1, 0, 'R');
        $pdf->Cell(10, 5, 'Uom', 1, 0, 'L');
        $pdf->Cell(15, 5, 'Qty2', 1, 0, 'R');
        $pdf->Cell(10, 5, 'Uom2', 1, 0, 'L');
        $pdf->Cell(20, 5, 'Reff Note', 1, 0, 'C');

        // details
        $smi = $this->m_penerimaanBarang->get_stock_move_items_by_kode_print($kode, $dept_id);
        $x = 5;
        $y = $y + 10;
        $no = 1;
        foreach ($smi as $row) {

            // set font tbody 
            $pdf->SetFont('Arial', '', 8, 'C');

            $pdf->setXY($x, $y);
            $pdf->Multicell(7, 5, $no, 1, 'L');
            $pdf->setXY($x + 7, $y);
            $pdf->Multicell(20, 5, $this->custom_char_in($row->kode_produk, 8), 1, 'L');
            $pdf->setXY($x + 27, $y);
            $pdf->Multicell(70, 5, $this->custom_char_in($row->nama_produk, 45), 1, 'L');
            $pdf->setXY($x + 97, $y);
            $pdf->Multicell(30, 5, $row->lot, 1, 'L');
            $pdf->setXY($x + 127, $y);
            $pdf->Multicell(15, 5, number_format($row->qty, 2), 1, 'R');
            $pdf->setXY($x + 142, $y);
            $pdf->Multicell(10, 5, $row->uom, 1, 'L');
            $pdf->setXY($x + 152, $y);
            $pdf->Multicell(15, 5, round($row->qty2, 2), 1, 'R');
            $pdf->setXY($x + 167, $y);
            $pdf->Multicell(10, 5, $row->uom2, 1, 'L');
            $pdf->setXY($x + 177, $y);
            $pdf->Multicell(20, 5, $this->custom_char_in($row->reff_note, 8), 1, 'L');

            $no++;
            $y = $y + 5;

            if ($y > 290) {
                $pdf->AddPage();
                $y = 7;
                $pdf->SetFont('Arial', '', 7, 'C');
                $pdf->setXY(160, 3);
                $tgl_now = tgl_indo(date('d-m-Y H:i:s'));
                $pdf->Multicell(50, 4, 'Tgl.Cetak : ' . $tgl_now, 0, 'C');
            }
        }

        $pdf->Output();
    }

    function custom_char_in($string, $length) {
        if (strlen($string) <= $length) {
            return $string;
        }
        return substr($string, 0, $length) . ' ...';
    }

    public function get_uom_select2() {
        $prod = addslashes($this->input->post('params'));
        $callback = $this->m_penerimaanBarang->get_list_uom_select2_by_kode($prod);
        echo json_encode($callback);
    }

    public function save_detail_add_quant_penerimaan_modal() {
        try {
            //code...
            $sub_menu = $this->uri->segment(2);

            if (empty($this->session->userdata('status'))) { //cek apakah session masih ada
                // session habis
                $callback = array('message' => 'Waktu Anda Telah Habis', 'sesi' => 'habis');
            } else {
                $username = addslashes($this->session->userdata('username'));
                $nama_user = $this->_module->get_nama_user($username)->row_array();

                $kode = $this->input->post('kode');
                $kode_produk = $this->input->post('kode_produk');
                $nama_produk = $this->input->post('nama_produk');
                $origin_prod = $this->input->post('origin_prod');
                $dept_id = $this->input->post('dept_id');
                $data_lot = json_decode($this->input->post('data_lot'), true);

                $this->_module->lock_tabel('penerimaan_barang WRITE, penerimaan_barang_items WRITE, penerimaan_barang_tmpp_add_quant WRITE,  log_history WRITE, main_menu_sub WRITE, user WRITE');

                $cek_kirim = $this->m_penerimaanBarang->cek_status_barang($kode)->row_array();
                if (empty($cek_kirim)) {
                    $callback = array('status' => 'failed', 'message' => 'Maaf, Data Penerimaan Barang Tidak ditemukan !', 'icon' => 'fa fa-warning', 'type' => 'danger');
                } else if ($cek_kirim['status'] == 'done') { //cek jika status penerimaan sudah terkirim
                    $callback = array('status' => 'failed', 'message' => 'Maaf, Data Tidak Bisa Disimpan, Data Sudah Terkirim !', 'icon' => 'fa fa-warning', 'type' => 'danger');
                } else if ($cek_kirim['status'] == 'cancel') { //cek jika status penerimaan batal
                    $callback = array('status' => 'failed', 'message' => 'Maaf, Data Tidak Bisa Disimpan, Data Penerimaan Sudah dibatalkan !', 'icon' => 'fa fa-warning', 'type' => 'danger');
                    // } else if(empty($data_lot)) {
                    //     $callback = array('status' => 'failed', 'message' => 'Maaf, Data items masih kosong !', 'icon' => 'fa fa-warning', 'type' => 'danger');
                } else {

                    // delete table tmp 
                    $this->m_penerimaanBarang->delete_add_quant_penerimaan_barang($kode, $kode_produk, $origin_prod);

                    $cek_target_qty = $this->m_penerimaanBarang->get_qty_produk_penerimaan_by_kode_origin($kode, $kode_produk, $origin_prod);

                    $tmp_data_insert = array();
                    $row_order = $this->m_penerimaanBarang->get_last_row_order_tmp($kode);
                    $list_product = "";
                    $tmp_jml_qty = 0;
                    $loop = 1;
                    foreach ($data_lot as $dl) {
                        $tmp_data_insert[] = array(
                            'kode' => $kode,
                            'kode_produk' => $dl['kode_produk'],
                            'nama_produk' => $dl['nama_produk'],
                            'lot' => $dl['lot'] ?? '',
                            'qty' => $dl['qty'],
                            'uom' => $dl['uom'],
                            'qty2' => $dl['qty2'],
                            'uom2' => $dl['uom2'],
                            'grade' => $dl['grade'],
                            'lebar_greige' => $dl['lebar_greige'] ?? '',
                            'uom_lebar_greige' => $dl['uom_lebar_greige'] ?? '',
                            'lebar_jadi' => $dl['lebar_jadi'] ?? '',
                            'uom_lebar_jadi' => $dl['uom_lebar_jadi'] ?? '',
                            'reff_note' => $dl['reff_note'],
                            'origin_prod' => $origin_prod,
                            'row_order' => $row_order
                        );
                        $tmp_jml_qty = $tmp_jml_qty + $dl['qty'];
                        $list_product .= "(" . $loop . ") " . $dl['nama_produk'] . " " . $dl['lot'] ?? '' . " " . $dl['qty'] . " " . $dl['uom'] . " " . $dl['qty2'] . " " . $dl['uom2'] . " " . $dl['grade'] . " " . $dl['lebar_greige'] ?? '' . " " . $dl['uom_lebar_greige'] ?? '' . " " . $dl['lebar_jadi'] ?? '' . " " . $dl['uom_lebar_jadi'] ?? '' . " " . $dl['reff_note'] . " <br>";
                        $row_order++;
                        $loop++;
                    }

                    if (floatval($tmp_jml_qty) > floatval($cek_target_qty)) {
                        throw new \Exception('Qty Melebih Target', 200);
                    }

                    if ($tmp_data_insert) {
                        $this->m_penerimaanBarang->save_add_quant_penerimaan_barang($tmp_data_insert);

                        $jenis_log = "edit";
                        $note_log = "Tambah Produk / Lot -> " . $origin_prod . " <br>" . $list_product;
                        $this->_module->gen_history_deptid($sub_menu, $kode, $jenis_log, addslashes($note_log), $username, $dept_id);
                        $callback = array('status' => 'success', 'message' => 'Data Berhasil Disimpan !', 'icon' => 'fa fa-check', 'type' => 'success');
                    } else {
                        $jenis_log = "cancel";
                        $note_log = "Hapus Semua Produk / Lot -> " . $origin_prod . " " . $nama_produk;
                        $this->_module->gen_history_deptid($sub_menu, $kode, $jenis_log, addslashes($note_log), $username, $dept_id);
                        $callback = array('status' => 'success', 'message' => 'Data Berhasil Disimpan !', 'icon' => 'fa fa-check', 'type' => 'success');
                    }
                }

                // $this->_module->unlock_tabel();
            }

            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode($callback));
        } catch (\Exception $ex) {
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('status' => 'failed', 'message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        } finally {
            // unlock table
            $this->_module->unlock_tabel();
        }
    }

    public function get_items_lot() {

        try {
            $kode = $this->input->post('kode');
            $kode_produk = $this->input->post('kode_produk');
            $origin_prod = $this->input->post('origin_prod');
            $items_lot = $this->m_penerimaanBarang->get_list_add_quant_penerimaan_barang_tmp($kode, $kode_produk, $origin_prod);

            $callback = array('status' => 'success', 'record1' => $items_lot);
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode($callback));
        } catch (\Exception $ex) {
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }

    public function get_list_grade_select2() {
        $kode = addslashes($this->input->post('params'));
        $callback = $this->m_penerimaanBarang->get_list_grade_select2_by_kode($kode);
        echo json_encode($callback);
    }

    public function print_rcv_pdf() {
        try {
            $users = $this->session->userdata('nama');

            $dept_id = $this->input->post('departemen');
            $kode = $this->input->post('id');

            $origin = '';
            $tanggal = '';
            $reff_picking = '';
            $tanggal_transaksi = '';
            $tanggal_jt = '';

            $modelHead = new $this->m_po;
            $head = $modelHead->setTables("penerimaan_barang")->setWheres(["kode" => $kode, "dept_id" => $dept_id])
                            ->setJoins('partner', "partner_id = partner.id", "left")->setOrder(["kode"])
                            ->setSelects(["penerimaan_barang.*", "concat(partner.delivery_street,' ',partner.delivery_city) as alamat"])->getDetail();

            if (!empty($head)) {
                $kode = $head->kode;
                $origin = $head->origin;
                $tanggal = $head->tanggal;
                $reff_picking = $head->reff_picking;
                $tanggal_transaksi = $head->tanggal_transaksi;
                $tanggal_jt = $head->tanggal_jt;
            }
            $items = $this->m_penerimaanBarang->get_stock_move_items_by_kode_print($kode, $dept_id);

            $modelItemsKodePP = new $this->m_global;
            $kodePP = $modelItemsKodePP->setTables("stock_move_items smi")
                            ->setJoins("penerimaan_barang pb", "smi.move_id = pb.move_id")
                            ->setJoins("penerimaan_barang_items pbi", "pbi.kode = pb.kode and pbi.origin_prod = smi.origin_prod")
                            ->setWheres(["pb.kode" => $kode, "pb.dept_id" => $dept_id])
                            ->setOrder(["smi.row_order"])->setSelects(["group_concat(DISTINCT(pbi.kode_pp)) as kode_pp"])->getDetail();

            $splitKodePP = str_split(($kodePP->kode_pp ?? ""), 30);

            $url = "dist/storages/print/rcv";
            if (!is_dir(FCPATH . $url)) {
                mkdir(FCPATH . $url, 0775, TRUE);
            }
            ini_set("pcre.backtrack_limit", "50000000");
            $html = $this->load->view("print/penerimaan_rcv", ["head" => $head, "item" => $items, 'users' => $users, 'kode_pp' => $splitKodePP], true);
            $mpdf = new Mpdf(['tempDir' => FCPATH . '/tmp']);

            $mpdf->WriteHTML($html);
            $pathFile = $url . "/" . str_replace("/", "_", $kode) . ".pdf";
            $mpdf->Output(FCPATH . $pathFile, "F");
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array("url" => base_url($pathFile))));
        } catch (Exception $ex) {
            log_message('error', $ex->getMessage());
            $this->output->set_status_header(500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        } finally {
            ini_set("pcre.backtrack_limit", "1000000");
        }
    }

//    public function create_invoice(){
//        try {
//            $sub_menu = $this->uri->segment(2);
//                $username = addslashes($this->session->userdata('username'));
//                $orig = $this->input->post('origin');
//                $kode = $this->input->post('id');
//                $po = new m_po;
//                $dataPO = $po->setWheres(["no_po" => $orig])
//                        ->setJoins("purchase_order_detail", "purchase_order_detail.po_id = purchase_order.id")
//                        ->setJoins("penerimaan_barang_items", "(penerimaan_barang_items.kode = '{$kode}' and penerimaan_barang_items.status_barang='done' "
//                                . "and  purchase_order_detail.kode_produk = penerimaan_barang_items.kode_produk)")
//                        ->setJoins("penerimaan_barang", "penerimaan_barang_items.kode = penerimaan_barang.kode")
//                        ->setJoins("mst_produk_coa", "mst_produk_coa.kode_produk = purchase_order_detail.kode_produk", "left")
//                        ->setJoins("tax", "tax.id = purchase_order_detail.tax_id", "left")
//                        ->setJoins("nilai_konversi", "nilai_konversi.id = purchase_order_detail.id_konversiuom", "left")
//                        ->setJoins("stock_move_items as smi", "(smi.move_id = penerimaan_barang.move_id and smi.origin_prod = penerimaan_barang_items.origin_prod)", "left")
//                        ->setOrder(["no_po"])
//                        ->setSelects([
//                            "purchase_order_detail.harga_per_uom_beli,purchase_order_detail.tax_id,purchase_order_detail.diskon,purchase_order_detail.deskripsi",
//                            "purchase_order_detail.reff_note,mst_produk_coa.kode_coa,no_value", "smi.qty as qty_dtg",
//                            "purchase_order.supplier,purchase_order.currency,purchase_order.nilai_currency",
//                            "penerimaan_barang_items.*", "amount,tax.id as pajak_id", "dpp_lain", "nilai_konversi.nilai", "purchase_order.jenis as jenis_po"
//                        ])->setGroups(["smi.quant_id"])
//                        ->getData();
//                if (is_null($dataPO)) {
//                    throw new \Exception("No PO {$orig} tidak ditemukan.", 500);
//                }
//                if ($dataPO[0]->jenis_po === "RFQ") {
//                    if ($dataPO[0]->no_value !== "1") {
//                        $orderDate = date("Y-m-d H:i:s");
//                        if (!$noinv = $this->token->noUrut('invoice_pembelian', date('y') . '/' . date('m'), true)
//                                        ->generate("PBINV/", '/%05d')->get()) {
//                            throw new \Exception("No Invoice tidak terbuat", 500);
//                        }
//                        $inserInvoice = new m_po;
//                        //                $item = clone $inserInvoice;
//                        $invoiceDetail = [];
//
//                        $head = $this->m_penerimaanBarang->get_data_by_code($kode);
//
//                        $dataInvoice = [
//                            "no_invoice" => $noinv,
//                            "id_supplier" => $dataPO[0]->supplier,
//                            "no_po" => $orig,
//                            "order_date" => $orderDate,
//                            "created_at" => date("Y-m-d H:i:s"),
//                            "matauang" => $dataPO[0]->currency,
//                            'nilai_matauang' => $dataPO[0]->nilai_currency,
//                            "journal" => "PB",
//                            "total" => 0,
//                            "dpp_lain" => 0,
//                            "origin" => $kode,
//                            "no_sj_supp" => $head->no_sj,
//                            "tanggal_invoice_supp" => $head->tanggal_sj,
//                            "tanggal_sj" => $head->tanggal_sj
//                        ];
//
//                        $idInsert = $inserInvoice->setTables("invoice")->save($dataInvoice);
//
//                        $totals = 0.00;
//                        $diskons = 0.00;
//                        $taxes = 0.00;
//                        $nilaiDppLain = 0;
////                    $modelDpp = new $this->m_global;
//                        $dpp = 0;
//                        $qty = 0;
//                        foreach ($dataPO as $key => $value) {
//                            $qty = $value->qty_dtg / $value->nilai;
//                            $invoiceDetail[] = [
//                                'invoice_id' => $idInsert,
//                                'nama_produk' => $value->nama_produk,
//                                'kode_produk' => $value->kode_produk,
//                                'qty_beli' => $qty,
//                                'uom_beli' => $value->uom_beli,
//                                'deskripsi' => $value->deskripsi,
//                                'reff_note' => $value->reff_note,
//                                'account' => $value->kode_coa,
//                                'harga_satuan' => $value->harga_per_uom_beli,
//                                'tax_id' => $value->pajak_id,
//                                'diskon' => $value->diskon,
//                                "amount_tax" => $value->amount
//                            ];
//                            $total = ($qty * $value->harga_per_uom_beli);
//                            $totals += $total;
//                            $diskon = ($value->diskon ?? 0);
//                            $diskons += $diskon;
//
//                            if ($value->dpp_lain > 0) {
//                                $dpp = $value->dpp_lain;
//                                $taxes += ((($total - $diskon) * 11) / 12) * $value->amount;
//                            } else {
//                                $taxes += ($total - $diskon) * $value->amount;
//                            }
//                        }
//                        $grandTotal = ($totals - $diskons) + $taxes;
//                        //create Invoice_detail
//                        $inserInvoice->setTables("invoice_detail")->saveBatch($invoiceDetail);
//                        $inserInvoice->setTables("invoice")->setWheres(["id" => $idInsert])->update(["total" => $grandTotal, "dpp_lain" => $dpp]);
//                        $this->_module->gen_history('invoice', $idInsert, 'create', logArrayToString(";", $dataInvoice), "msuciati");
//                    }
//                }
//        } catch (Exception $ex) {
//            
//        }
//    }
}
