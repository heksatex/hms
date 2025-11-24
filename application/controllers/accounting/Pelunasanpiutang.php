<?php

defined('BASEPATH') or exit('No Direct Script Acces Allowed');


class Pelunasanpiutang extends MY_Controller
{

    //put your code here
    public function __construct()
    {
        parent::__construct();
        $this->is_loggedin(); //cek apakah user sudah login
        $this->load->model("_module"); //load modul global
        $this->load->model("m_pelunasanpiutang");
        $this->load->model("m_pelunasanhutang");
        $this->load->model("m_deliveryorder");
        $this->load->model("m_Picklist");
        $this->load->library("token");
    }

    public function index()
    {
        $data['id_dept'] = 'ACCPP';
        $this->load->view('accounting/v_pelunasan_piutang', $data);
    }

    function get_data()
    {
        if (isset($_POST['start']) && isset($_POST['draw'])) {
            $sub_menu = $this->uri->segment(2);
            $id_dept  = 'ACCPP';

            $kode     = $this->_module->get_kode_sub_menu_deptid($sub_menu, $id_dept)->row_array();

            $list = $this->m_pelunasanpiutang->get_datatables($kode['kode']);
            $data = array();
            $no = $_POST['start'];
            foreach ($list as $field) {
                $kode_encrypt = encrypt_url($field->no_pelunasan);
                $no++;
                $row = array();
                $row[] = $no;
                $row[] = '<a href="' . base_url('accounting/pelunasanpiutang/edit/' . $kode_encrypt) . '">' . $field->no_pelunasan . '</a>';
                $row[] = date("Y-m-d", strtotime($field->tanggal_transaksi));
                $row[] = $field->partner_nama;
                $row[] = $field->nama_status;

                $data[] = $row;
            }

            $output = array(
                "draw" => $_POST['draw'],
                "recordsTotal" => $this->m_pelunasanpiutang->count_all($kode['kode']),
                "recordsFiltered" => $this->m_pelunasanpiutang->count_filtered($kode['kode']),
                "data" => $data,
            );
            //output dalam format JSON
            echo json_encode($output);
        } else {
            die();
        }
    }

    public function add()
    {
        $data['id_dept'] = 'ACCPP';
        $this->load->view('accounting/v_pelunasan_piutang_add', $data);
    }


    public function edit($kode = null)
    {
        if (!isset($kode)) show_404();
        $kode_decrypt      = decrypt_url($kode);
        $list  = $this->m_pelunasanpiutang->get_data_by_code($kode_decrypt);
        $list_jurnal = $this->m_pelunasanpiutang->get_data_jurnal_by_code($kode_decrypt);
        if (empty($list)) {
            show_404();
        } else {
            $data['id_dept'] = 'ACCPP';
            $data['list']    = $list;
            $data['list_jurnal'] = $list_jurnal;
            return $this->load->view('accounting/v_pelunasan_piutang_edit', $data);
        }
    }

    public function get_list_customer()
    {
        $name     = $this->input->post('name');
        $customer =  1;
        $callback = $this->m_pelunasanpiutang->get_list_partner_customer($customer, $name);
        echo json_encode($callback);
    }

    function get_total_by_partner()
    {
        try {
            //code...
            $partner  = $this->input->post('partner');

            if (empty($partner)) {
                throw new \Exception('Customer kosong', 500);
            }

            $get_tinv = $this->m_pelunasanpiutang->get_total_faktur_by_partner(['partner_id' => $partner, 'status' => 'confirm', 'lunas' => 0]);

            $where = ['partner_id' => $partner, 'status' => 'confirm'];
            //get total 
            $gt_kk = $this->m_pelunasanpiutang->get_total_kas_masuk_by_partner($where, 'kas');
            $gt_bk = $this->m_pelunasanpiutang->get_total_bank_masuk_by_partner($where, 'kas');
            $gt_gk = $this->m_pelunasanpiutang->get_total_giro_masuk_by_partner($where, 'kas');

            $total_kas_bank = $gt_kk + $gt_bk + $gt_gk;

            //get total uang muka
            $gt_kk_um = $this->m_pelunasanpiutang->get_total_kas_masuk_by_partner($where, 'um');
            $gt_bk_um = $this->m_pelunasanpiutang->get_total_bank_masuk_by_partner($where, 'um');
            $gt_gk_um = $this->m_pelunasanpiutang->get_total_giro_masuk_by_partner($where, 'um');

            $total_um = $gt_kk_um + $gt_bk_um + $gt_gk_um;

            $get_tinv_re = $this->m_pelunasanpiutang->get_total_faktur_retur_by_partner(['partner_id' => $partner, 'status' => 'confirm', 'lunas' => 0]);

            $total = array(
                'total_invoice' => $get_tinv,
                'total_kas_bank' => $total_kas_bank,
                'total_uang_muka' => $total_um,
                'total_retur' => $get_tinv_re
            );

            $callback = array("message" => "berhasil", 'total'   => $total);

            $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($callback));
        } catch (Exception $ex) {
            $this->output->set_status_header(($ex->getCode()) ?? 500)
                ->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode(array('status' => 'failed', 'message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }

    function get_list_koreksi_select2()
    {
        $nama  = addslashes($this->input->post('name'));
        $tipe_currency  = addslashes($this->input->post('tipe_currency'));
        $tipe  =  $this->input->post('tipe');
        $callback = $this->m_pelunasanpiutang->get_list_koreksi($tipe_currency, $nama, $tipe);
        echo json_encode($callback);
    }


    public function simpan()
    {
        try {
            $sub_menu = $this->uri->segment(2);
            $username = addslashes($this->session->userdata('username'));

            if (empty($this->session->userdata('status'))) {
                throw new \Exception('Waktu Anda Telah Habis', 401); // Unauthorized / session habis
            }

            $this->_module->startTransaction();
            $this->_module->lock_tabel("acc_pelunasan_piutang WRITE, main_menu_sub READ, log_history WRITE, token_increment WRITE, partner WRITE, user READ, acc_pelunasan_piutang_faktur WRITE, acc_pelunasan_piutang_metode WRITE, acc_pelunasan_piutang_summary WRITE, acc_pelunasan_piutang_summary_koreksi WRITE");

            $kode = $this->input->post('kode');
            $partner = $this->input->post('partner');
            $tgl_transaksi = $this->input->post('tgl_transaksi');
            $tgl_transaksi_new = $tgl_transaksi . " " . date("H:i:s");
            $tgl = date('Y-m-d H:i:s');
            $retry = $this->input->post('retry');

            if (empty($partner)) {
                throw new \Exception('Customer harus diisi', 422); // Validation error
            }

            $get_p = $this->m_pelunasanhutang->get_partner_by_id($partner);
            if (empty($get_p->id)) {
                throw new \Exception('Customer tidak ditemukan', 404);
            }

            if (empty($kode)) {
                $kode = $this->token->noUrut('pelunasan_piutang', date('ym', strtotime($tgl_transaksi)), true)
                    ->generate('PLP', '%04d')
                    ->get();

                $data = [
                    'no_pelunasan'      => $kode,
                    'tanggal_dibuat'    => $tgl,
                    'tanggal_transaksi' => $tgl_transaksi_new,
                    'partner_id'        => $get_p->id,
                    'partner_nama'      => $get_p->nama ?? '',
                    'status'            => 'draft'
                ];

                $insert = $this->m_pelunasanpiutang->insert_data_pelunasan_piutang($data);
                if (!$insert['status']) {
                    throw new \Exception('Gagal menyimpan data baru: ' . $insert['message'], 500);
                }

                $jenis_log = "create";
            } else {
                $cek = $this->m_pelunasanpiutang->get_data_by_code($kode);

                if (empty($cek)) {
                    throw new \Exception('Data pelunasan tidak ditemukan', 404);
                }
                if ($cek->status == 'done') {
                    throw new \Exception('Data tidak bisa disimpan, status sudah Done', 409);
                }
                if ($cek->status == 'cancel') {
                    throw new \Exception('Data tidak bisa disimpan, status Cancel', 409);
                }

                if ($retry == 'false' && $cek->partner_id != $partner) {
                    throw new \Exception('Data Customer berbeda dari sebelumnya ', 200);
                }

                $data_update = [
                    'no_pelunasan'      => $kode,
                    // 'tanggal_transaksi' => $tgl_transaksi_new,
                    'partner_id'        => $get_p->id,
                    'partner_nama'      => $get_p->nama ?? '',
                ];


                if ($cek->partner_id != $partner) { // hapus faktur, metode pelunasan, summary, summary_koreksi

                    // hapus faktur
                    $this->m_pelunasanpiutang->delete_pelunasan_piutang_faktur_by_kode(['no_pelunasan' => $kode]);
                    // hapus metode pelunasan
                    $this->m_pelunasanpiutang->delete_pelunasan_piutang_metode_by_kode(['no_pelunasan' => $kode]);

                    $this->m_pelunasanpiutang->delete_pelunasan_piutang_summary_by_kode($kode);

                    // hapus summary_koreksi
                    $this->m_pelunasanpiutang->delete_pelunasan_piutang_summary_koreksi_by_id(['no_pelunasan' => $kode]);
                }


                $update = $this->m_pelunasanpiutang->update_data_pelunasan_piutang([$data_update]);
                if (!$update['status']) {
                    throw new \Exception('Gagal memperbarui data: ' . $update['message'], 500);
                }

                $jenis_log = "edit";
            }

            // Log History
            $note_log = "$kode | Tgl Transaksi: $tgl_transaksi | Customer: " . ($get_p->nama ?? '');
            $data_history = [
                'datelog'   => date("Y-m-d H:i:s"),
                'kode'      => $kode,
                'jenis_log' => $jenis_log,
                'note'      => $note_log
            ];
            $this->_module->gen_history_ip($sub_menu, $username, $data_history);

            // Commit
            if (!$this->_module->finishTransaction()) {
                throw new \Exception('Gagal menyimpan', 500);
            }

            $callback = [
                'status'  => 'success',
                'message' => 'Data Berhasil Disimpan',
                'icon'    => 'fa fa-check',
                'type'    => 'success',
                'isi'     => encrypt_url($kode)
            ];

            $this->output
                ->set_status_header(200)
                ->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode($callback));
        } catch (\Exception $ex) {
            $this->_module->rollbackTransaction();
            $this->_module->finishRollBack();

            $code = $ex->getCode() ?: 500;
            $this->output
                ->set_status_header($code)
                ->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode([
                    'status'  => 'failed',
                    'message' => $ex->getMessage(),
                    'icon'    => 'fa fa-warning',
                    'type'    => 'danger'
                ]));
        } finally {
            $this->_module->unlock_tabel();
        }
    }


    private  $statusBayar = array(
        array(
            "id"   => 'belum_bayar',
            "text" =>  'Belum Bayar',
            "color" => 'label label-danger'
        ),
        array(
            "id"   => 'partial',
            "text" =>  'Partial',
            "color" => 'label label-warning text-dark'
        ),
        array(
            "id"   => 'lunas',
            "text" =>  'Lunas',
            "color" => 'label label-success'
        ),
    );

    private $metodePelunasan = array(
        array(
            "id" => 'um',
            "text" => 'Uang Muka'
        ),
        array(
            "id" => 'kas',
            'text' => "Kas Bank"
        ),
        array(
            "id" => 'retur',
            "text" => "Retur"
        ),
        array(
            "id" => 'koreksi',
            "text" => 'Koreksi Kurs Bulan'
        )
    );

    private $metodePelunasan2 = array(
        array(
            "id" => 'giro',
            "text" => '(Giro)',
            'table_detail' => 'acc_giro_masuk_detail',
            'status' => 'acc_giro_masuk.status',
            'status_value' => 'confirm',
            'no_bukti' => 'acc_giro_masuk_detail.no_gm',
            'id_detail' => 'acc_giro_masuk_detail.id',
            "check" => 'true',
        ),
        array(
            "id" => 'kas',
            "text" => '(Kas)',
            'table_detail' => 'acc_kas_masuk_detail',
            'status' => 'acc_kas_masuk.status',
            'status_value' => 'confirm',
            'no_bukti' => 'acc_kas_masuk_detail.no_km',
            'id_detail' => 'acc_kas_masuk_detail.id',
            "check" => 'true',
        ),
        array(
            "id" => 'bank',
            "text" => '(Bank)',
            "table_detail" => 'acc_bank_masuk_detail',
            'status' => 'acc_bank_masuk.status',
            'status_value' => 'confirm',
            'no_bukti' => 'acc_bank_masuk_detail.no_bm',
            'id_detail' => 'acc_bank_masuk_detail.id',
            "check" => 'true',
        ),
        array(
            "id" => "retur",
            "text" => '',
            'status' => 'status',
            'status_value' => 'done',
            "no_bukti" => "no_retur",
            'id_detail' => "id",
            "table_detail" => 'acc_retur_penjualan',
            "check" => 'true',
        ),
        array(
            "id" => "koreksi",
            "text" => '',
            "check" => 'false'
        )

    );


    function loadData()
    {
        try {
            //code...
            $load         = $this->input->post('load');
            $no_pelunasan = $this->input->post('no_pelunasan');
            $list         = $this->m_pelunasanpiutang->get_data_by_code($no_pelunasan);
            if ($load == 'resume') {
                $data = $this->m_pelunasanpiutang->get_data_summary_by_code($no_pelunasan);
                foreach ($data as &$row) {
                    $koreksi = strtolower(trim($row->koreksi ?? ''));
                    $row->koreksi_get_coa = '';
                    $row->koreksi_text    = '';
                    foreach ($this->m_pelunasanpiutang->get_list_koreksi() as $gk) {
                        if ($gk->kode === $koreksi) {
                            $row->koreksi_get_coa = $gk->get_coa;
                            $row->koreksi_text = $gk->nama_koreksi;
                        }
                    }
                }
                unset($datas);
            } else if ($load == 'invoice') {
                $data = $this->m_pelunasanpiutang->get_data_faktur_by_code($no_pelunasan);
                foreach ($data as &$row) {
                    $status_bayar = strtolower(trim($row->status_bayar ?? ''));
                    $row->status_text  = '-';
                    $row->status_color = 'label label-default';

                    // cari di array statusBayar yang id-nya sama
                    foreach ($this->statusBayar as $statusItem) {
                        if ($statusItem['id'] === $status_bayar) {
                            $row->status_text  = $statusItem['text'];
                            $row->status_color = $statusItem['color'];
                            break; // stop loop jika sudah ketemu
                        }
                    }
                }
                unset($row);
            } else if ($load == 'pelunasan') {
                $data = $this->m_pelunasanpiutang->get_data_metode_by_code($no_pelunasan);
                foreach ($data as &$row) {
                    $metode = strtolower(trim($row->tipe ?? ''));
                    $row->metode_text = '-';

                    foreach ($this->metodePelunasan as $metodeItems) {
                        if ($metodeItems['id'] == $metode) {
                            $row->metode_text = $metodeItems['text'];
                            $metode2 = strtolower(trim($row->tipe2 ?? ''));
                            foreach ($this->metodePelunasan2 as $metodeItems2) {
                                if ($metodeItems2['id'] == $metode2) {
                                    $row->metode_text = $metodeItems['text'] . ' ' . $metodeItems2['text'];
                                    break;
                                }
                            }
                        }
                    }
                }
                unset($row);
            } else {
                throw new \Exception('Data Gagal di Load ', 200);
            }

            $callback = array('status' => 'success', 'message' => 'berhasil', 'icon' => 'fa fa-check', 'type' => 'success', 'record' => $data, 'head' => $list);
            $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($callback));
        } catch (Exception $ex) {
            //throw $th;
            $this->output->set_status_header($ex->getCode() ?? 500)
                ->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode(array('status' => 'failed', 'message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }


    public function get_view_faktur()
    {
        $no_pelunasan = $this->input->post("no_pelunasan");
        $partner      = $this->input->post("partner"); // partner_id
        $view = $this->load->view('modal/v_pelunasan_piutang_list_faktur_modal', ["partner" => $partner, "no_pelunasan" => $no_pelunasan], true);
        $this->output->set_status_header(200)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode(['data' => $view]));
    }


    public function list_data_faktur()
    {
        try {
            $partner = $this->input->post("partner"); // partner_id

            if (isset($_POST['start']) && isset($_POST['draw'])) {
                $list = $this->m_pelunasanpiutang->get_datatables2($partner);
                $data = array();
                $no = $_POST['start'];
                foreach ($list as $field) {
                    $no++;
                    $row = array();
                    $row[] = $field->id;
                    $row[] = $no;
                    $row[] = $field->no_faktur;
                    $row[] = $field->no_sj;
                    $row[] = date('Y-m-d', strtotime($field->tanggal));
                    $row[] = $field->currency;
                    $row[] = $field->kurs_nominal;
                    $row[] = number_format($field->total_piutang_rp, 2);
                    $row[] = number_format($field->total_piutang_valas, 2);
                    $row[] = number_format($field->sisa_piutang_rp, 2);
                    $row[] = number_format($field->sisa_piutang_valas, 2);
                    $data[] = $row;
                }

                $output = array(
                    "draw" => $_POST['draw'],
                    "recordsTotal" => $this->m_pelunasanpiutang->count_all2($partner),
                    "recordsFiltered" => $this->m_pelunasanpiutang->count_filtered2($partner),
                    "data" => $data,
                );
                //output dalam format JSON
                echo json_encode($output);
            } else {
                die();
            }

            exit();
        } catch (Exception $ex) {
            echo json_encode(array(
                "draw" => $_POST['draw'],
                "recordsTotal" => 0,
                "recordsFiltered" => 0,
                "data" => [],
            ));
        }
    }


    function save_detail_faktur()
    {
        try {
            if (empty($this->session->userdata('status'))) { //cek apakah session masih ada
                // session habis
                throw new \Exception('Waktu Anda Telah Habis', 401);
            } else {

                $no_pelunasan  = $this->input->post('no_pelunasan');
                $arr_data   = $this->input->post('arr_data');

                $sub_menu   = $this->uri->segment(2);
                $username   = addslashes($this->session->userdata('username'));

                // start transaction
                $this->_module->startTransaction();

                //lock tabel
                $this->_module->lock_tabel('acc_pelunasan_piutang WRITE, acc_pelunasan_piutang_faktur WRITE, departemen as d READ, user READ, acc_faktur_penjualan as faktur WRITE, currency_kurs READ, main_menu_sub READ,log_history WRITE, acc_pelunasan_piutang_summary WRITE, acc_pelunasan_piutang_metode WRITE, acc_pelunasan_piutang_summary_koreksi WRITE');

                if (empty($no_pelunasan)) {
                    throw new \Exception('No Pelunasan Kosong !', 200);
                }

                // cek status done / cancel
                $cek  = $this->m_pelunasanpiutang->get_data_by_code($no_pelunasan);

                if (empty($cek)) {
                    throw new \Exception('Data Pelunasan tidak ditemukan !', 200);
                } else if ($cek->status == 'done') {
                    $callback = array('status' => 'failed', 'message' => 'Maaf, Data Tidak Bisa Disimpan, Status Sudah Done !', 'icon' => 'fa fa-warning', 'type' => 'danger');
                } else if ($cek->status == 'cancel') {
                    $callback = array('status' => 'failed', 'message' => 'Maaf, Data Tidak Bisa Disimpan, Status Cancel !', 'icon' => 'fa fa-warning', 'type' => 'danger');
                } else if (empty($arr_data)) {
                    throw new \Exception('Data belum dipilih !', 200);
                } else {

                    $data_items = array();
                    $log_add_items        = '';
                    $lot_tmp    = "";
                    $get_row    = $this->m_pelunasanpiutang->get_last_row_order_faktur_by_id($cek->id);
                    $row        = $get_row;
                    $num        = 1;

                    foreach ($arr_data as $ad) {

                        // get data invoice 
                        $dt = $this->m_pelunasanpiutang->get_data_faktur_by_id(['faktur.id' => $ad]);
                        $cek_inv = $this->m_pelunasanpiutang->cek_faktur_input_by_kode(['no_pelunasan' => $no_pelunasan, 'no_faktur' => $dt->no_faktur])->num_rows();

                        if (($cek_inv) > 0) {
                            throw new \Exception('Data Faktur <b>' . $dt->no_faktur . '</b> sudah diinput  !', 200);
                        }

                        if(empty($dt->no_faktur) || !isset($dt->no_faktur)){
                            throw new \Exception('No Faktur tidak boleh kosong  !', 200);
                        }

                        $data_items[] = array(
                            'pelunasan_piutang_id'   => $cek->id,
                            'no_pelunasan'           => $no_pelunasan,
                            'faktur_id'              => $dt->id,
                            'no_faktur'              => $dt->no_faktur,
                            'no_sj'                  => $dt->no_sj,
                            'tanggal_faktur'         => $dt->tanggal,
                            'currency_id'            => $dt->kurs,
                            'currency'               => $dt->currency,
                            'kurs'                   => $dt->kurs_nominal,
                            'total_piutang_rp'        => $dt->total_piutang_rp,
                            'total_piutang_valas'     => $dt->total_piutang_valas,
                            'sisa_piutang_rp'         => $dt->sisa_piutang_rp,
                            'sisa_piutang_valas'      => $dt->sisa_piutang_valas,
                            'row_order'              => $row
                        );

                        $row++;
                        $log_add_items .= "(" . $num . ") " . $dt->no_faktur . " " . $dt->no_sj . " " . $dt->tanggal . " " . $dt->currency . " " . $dt->kurs_nominal . " " . $dt->total_piutang_rp . " " . $dt->total_piutang_valas . " " . $dt->sisa_piutang_rp . " " . $dt->sisa_piutang_valas . "  <br> ";
                        $num++;
                    }

                    if (!empty($data_items)) {

                        $insert = $this->m_pelunasanpiutang->insert_data_pelunasan_piutang_faktur($data_items);
                        if (!empty($insert)) {
                            throw new \Exception('Data Gagal Disimpan !', 200);
                        }

                        $jenis_log = "edit";
                        $note_log  = "Tambah Data Faktur di " . $no_pelunasan . " <br> " . $log_add_items;
                        $data_history = array(
                            'datelog'   => date("Y-m-d H:i:s"),
                            'kode'      => $no_pelunasan,
                            'jenis_log' => $jenis_log,
                            'note'      => $note_log
                        );
                        $this->_module->gen_history_ip($sub_menu, $username, $data_history);

                        $result = $this->distribusi_pelunasan_otomatis($no_pelunasan);
                        if (!empty($result)) {
                            throw new \Exception('Distribusi gagal di update !', 200);
                        }

                        $result2 = $this->hitung_summary($no_pelunasan);
                        if (!empty($result2)) {
                            throw new \Exception('Summary Gagal di update !', 200);
                        }

                        $callback = array('status' => 'success', 'message' => 'Data Faktur berhasil ditambah !', 'icon' => 'fa fa-success', 'type' => 'success');
                    } else {

                        $callback = array('status' => 'failed', 'message' => 'Gagal menambahkan Faktur, Data List Faktur tidak ada !', 'icon' => 'fa fa-warning', 'type' => 'danger');
                    }
                }

                if (!$this->_module->finishTransaction()) {
                    throw new \Exception('Gagal Mencari Data', 500);
                }
                $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($callback));
            }
        } catch (Exception $ex) {
            // finish transaction
            $this->_module->finishRollBack();
            $this->_module->rollbackTransaction();
            $this->output->set_status_header($ex->getCode() ?? 500)
                ->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode(array('status' => 'failed', 'message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        } finally {
            // unlock table
            $this->_module->unlock_tabel();
        }
    }

    function getCoaByKoreksi()
    {
        $id_summary    = $this->input->post('summary_id');
        $get_list_coa  = $this->m_pelunasanpiutang->get_coa_summary_id(['pelunasan_summary_id' => $id_summary])->result();

        $result = ['coa_debit' => null, 'coa_credit' => null];

        foreach ($get_list_coa as $r) {
            if ($r->posisi == 'D') {
                $result['coa_debit'] = $r->kode_coa . ' - ' . $r->nama_coa;
            } else if ($r->posisi == 'C') {
                $result['coa_credit'] = $r->kode_coa . ' - ' . $r->nama_coa;
            }
        }

        echo json_encode($result);
    }



    function delete_pelunasan_piutang_faktur()
    {
        try {
            //code...
            $validation = [
                [
                    'field' => 'no_pelunasan',
                    'label' => 'No Peluasnaan',
                    'rules' => ['required'],
                    'errors' => [
                        'required' => '{field} Kosong !',
                    ]
                ],
                [
                    'field' => 'id',
                    'label' => 'Faktur',
                    'rules' => ['required'],
                    'errors' => [
                        'required' => '{field} Kosong !',
                    ]
                ],
            ];

            // start transaction
            $this->_module->startTransaction();

            //lock tabel
            $this->_module->lock_tabel('acc_pelunasan_piutang WRITE, acc_pelunasan_piutang_faktur WRITE, acc_pelunasan_piutang_metode WRITE, acc_pelunasan_piutang_summary WRITE, main_menu_sub READ,log_history WRITE, user READ, acc_pelunasan_piutang_summary_koreksi WRITE');

            $callback  = array();
            if (empty($this->session->userdata('status'))) { //cek apakah session masih adag
                // session habis
                throw new \Exception('Waktu Anda Telah Habis', 401);
            } else {

                $sub_menu   = $this->uri->segment(2);
                $username   = addslashes($this->session->userdata('username'));

                $this->form_validation->set_rules($validation);
                if ($this->form_validation->run() == FALSE) {
                    // throw new \Exception(array_values($this->form_validation->error_array())[0], 500);
                    $callback = array('status' => 'failed', 'field' => '', 'message' => array_values($this->form_validation->error_array())[0], 'icon' => 'fa fa-warning', 'type' => 'danger');
                } else {

                    $no_pelunasan  = $this->input->post('no_pelunasan');
                    $no_fak        = $this->input->post('no_faktur');
                    $id            = $this->input->post('id');

                    // cek status done / cancel
                    $cek  = $this->m_pelunasanpiutang->get_data_by_code($no_pelunasan);

                    if (empty($cek)) {
                        throw new \Exception('Data Pelunasan tidak ditemukan !', 200);
                    } else if ($cek->status == 'done') {
                        $callback = array('status' => 'failed', 'message' => 'Maaf, Data Tidak Bisa Dihapus, Status Sudah Done !', 'icon' => 'fa fa-warning', 'type' => 'danger');
                    } else if ($cek->status == 'cancel') {
                        $callback = array('status' => 'failed', 'message' => 'Maaf, Data Tidak Bisa Dihapus, Status Cancel !', 'icon' => 'fa fa-warning', 'type' => 'danger');
                    } else {


                        $delete = $this->m_pelunasanpiutang->delete_pelunasan_piutang_faktur_by_kode(['no_pelunasan' => $no_pelunasan, 'id' => $id]);
                        if (!empty($delete)) {
                            throw new \Exception('Gagal Menghapus data, Data tidak ditemukan', 200);
                        }

                        $jenis_log = "cancel";
                        $note_log  = "Menghapus Data Faktur No. " . $no_fak;
                        $data_history = array(
                            'datelog'   => date("Y-m-d H:i:s"),
                            'kode'      => $no_pelunasan,
                            'jenis_log' => $jenis_log,
                            'note'      => $note_log
                        );

                        // load in library
                        $this->_module->gen_history_ip($sub_menu, $username, $data_history);


                        $result2 = $this->hitung_summary($no_pelunasan);
                        if (!empty($result2)) {
                            throw new \Exception('Summary Gagal di update !', 200);
                        }

                        $callback = array('status' => 'success', 'message' => 'Data Berhasil dihapus', 'icon' => 'fa fa-check', 'type' => 'success');
                    }
                }

                if (!$this->_module->finishTransaction()) {
                    throw new \Exception('Gagal menghapus Data', 500);
                }
                $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($callback));
            }
        } catch (Exception $ex) {
            // finish transaction
            $this->_module->finishRollBack();
            $this->_module->rollbackTransaction();
            $this->output->set_status_header($ex->getCode() ?? 500)
                ->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode(array('status' => 'failed', 'message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        } finally {
            // unlock table
            $this->_module->unlock_tabel();
        }
    }

    public function get_view_sj()
    {
        $no_pelunasan = $this->input->post("no_pelunasan", true); // XSS filtering aktif
        $no_sj       = $this->input->post("sj", true);

        // validasi awal
        if (empty($no_sj)) {
            return $this->output
                ->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode(['error' => 'Parameter No SJ tidak boleh kosong.']));
        }

        // ambil data dari model
        $picklist = '';
        $do       = $this->m_deliveryorder->getDataDetail(['a.no_sj' => $no_sj, 'a.status' => 'done']);
        if ($do) {
            $picklist = $this->m_Picklist->getDataByID(['picklist.no' => $do->no_picklist], '', 'delivery');
        }

        // handle jika data kosong
        if (!$do && !$picklist) {
            return $this->output
                ->set_status_header(500)
                ->set_content_type('application/json')
                ->set_output(json_encode(['error' => 'Data No SJ tidak ditemukan.']));
        }

        // render view menjadi HTML string
        $view = $this->load->view(
            'modal/v_pelunasan_piutang_view_sj_modal',
            [
                "do"       => $do,
                "picklist" => $picklist,
                "no_pelunasan" => $no_pelunasan,
            ],
            true
        );

        // output JSON
        return $this->output
            ->set_status_header(200)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode(['data' => $view]));
    }


    public function get_view_kas_bank()
    {
        $no_pelunasan = $this->input->post("no_pelunasan");
        $partner      = $this->input->post("partner"); // partner_id
        $type         = $this->input->post('type'); // kas / uang muka
        $view = $this->load->view('modal/v_pelunasan_piutang_list_kas_modal', ["partner" => $partner, "no_pelunasan" => $no_pelunasan, "type" => $type], true);
        $this->output->set_status_header(200)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode(['data' => $view]));
    }

    public function list_data_kas_bank()
    {
        try {
            $partner = $this->input->post("partner"); // partner_id
            $type    = $this->input->post("type"); // kas,um, retur=''

            if (isset($_POST['start']) && isset($_POST['draw'])) {
                $list = $this->m_pelunasanpiutang->get_datatables3($partner, $type);
                $data = array();
                $no = $_POST['start'];
                foreach ($list as $field) {
                    $no++;
                    $row = array();
                    $row[] = $field->no_bukti . '|^' . $field->id;
                    $row[] = $no;
                    $row[] = $field->no_bukti;
                    $row[] = date('Y-m-d', strtotime($field->tanggal));
                    $row[] = $field->uraian;
                    $row[] = $field->currency;
                    $row[] = $field->kurs;
                    $row[] = number_format($field->total_rp, 2);
                    $row[] = number_format($field->total_valas, 2);
                    $data[] = $row;
                }

                $output = array(
                    "draw" => $_POST['draw'],
                    "recordsTotal" => $this->m_pelunasanpiutang->count_all3($partner, $type),
                    "recordsFiltered" => $this->m_pelunasanpiutang->count_filtered3($partner, $type),
                    "data" => $data,
                );
                //output dalam format JSON
                echo json_encode($output);
            } else {
                die();
            }

            exit();
        } catch (Exception $ex) {
            echo json_encode(array(
                "draw" => $_POST['draw'],
                "recordsTotal" => 0,
                "recordsFiltered" => 0,
                "data" => [],
            ));
        }
    }


    public function get_view_retur()
    {
        $no_pelunasan = $this->input->post("no_pelunasan");
        $partner      = $this->input->post("partner"); // partner_id
        $view = $this->load->view('modal/v_pelunasan_piutang_list_retur_modal', ["partner" => $partner, "no_pelunasan" => $no_pelunasan], true);
        $this->output->set_status_header(200)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode(['data' => $view]));
    }


    public function list_data_retur()
    {
        try {
            $partner = $this->input->post("partner"); // partner_id

            if (isset($_POST['start']) && isset($_POST['draw'])) {
                $list = $this->m_pelunasanpiutang->get_datatables4($partner);
                $data = array();
                $no = $_POST['start'];
                foreach ($list as $field) {
                    $no++;
                    $row = array();
                    $row[] = $field->no_bukti . '|^' . $field->id;
                    $row[] = $no;
                    $row[] = $field->no_bukti;
                    $row[] = date('Y-m-d', strtotime($field->tanggal));
                    $row[] = $field->currency;
                    $row[] = $field->kurs;
                    $row[] = number_format($field->total_rp, 2);
                    $row[] = number_format($field->total_valas, 2);
                    $data[] = $row;
                }

                $output = array(
                    "draw" => $_POST['draw'],
                    "recordsTotal" => $this->m_pelunasanpiutang->count_all4($partner),
                    "recordsFiltered" => $this->m_pelunasanpiutang->count_filtered4($partner),
                    "data" => $data,
                );
                //output dalam format JSON
                echo json_encode($output);
            } else {
                die();
            }

            exit();
        } catch (Exception $ex) {
            echo json_encode(array(
                "draw" => $_POST['draw'],
                "recordsTotal" => 0,
                "recordsFiltered" => 0,
                "data" => [],
            ));
        }
    }


    function save_detail_kas_bank()
    {
        try {
            if (empty($this->session->userdata('status'))) { //cek apakah session masih ada
                // session habis
                throw new \Exception('Waktu Anda Telah Habis', 401);
            } else {

                $no_pelunasan  = $this->input->post('no_pelunasan');
                $arr_data   = $this->input->post('arr_data');
                $type       = $this->input->post('type'); // kas / uang muka / retur

                $sub_menu   = $this->uri->segment(2);
                $username   = addslashes($this->session->userdata('username'));

                // start transaction
                $this->_module->startTransaction();

                //lock tabel
                $this->_module->lock_tabel('acc_pelunasan_piutang WRITE, acc_pelunasan_piutang_metode WRITE, departemen as d READ, user READ, main_menu_sub READ,log_history WRITE, acc_bank_masuk as abm WRITE, acc_bank_masuk_detail as abmd WRITE, acc_kas_masuk as akm WRITE, acc_kas_masuk_detail as akmd WRITE, acc_giro_masuk as agm WRITE, acc_giro_masuk_detail as  agmd WRITE, acc_pelunasan_piutang_faktur WRITE, acc_pelunasan_piutang_summary WRITE, acc_retur_penjualan as arj WRITE,   acc_pelunasan_piutang_summary_koreksi WRITE, acc_coa READ, currency_kurs as ck1 READ,currency_kurs as ck2 READ,currency_kurs as ck3 READ');

                if (empty($no_pelunasan)) {
                    throw new \Exception('No Pelunasan Kosong !', 200);
                }

                // cek status done / cancel
                $cek  = $this->m_pelunasanpiutang->get_data_by_code($no_pelunasan);

                if (empty($cek)) {
                    throw new \Exception('Data Pelunasan tidak ditemukan !', 200);
                } else if ($cek->status == 'done') {
                    $callback = array('status' => 'failed', 'message' => 'Maaf, Data Tidak Bisa Disimpan, Status Sudah Done !', 'icon' => 'fa fa-warning', 'type' => 'danger');
                } else if ($cek->status == 'cancel') {
                    $callback = array('status' => 'failed', 'message' => 'Maaf, Data Tidak Bisa Disimpan, Status Cancel !', 'icon' => 'fa fa-warning', 'type' => 'danger');
                } else if (empty($arr_data)) {
                    throw new \Exception('Data belum dipilih !', 200);
                } else {

                    $data_items = array();
                    $log_add_items        = '';
                    $lot_tmp    = "";
                    $get_row    = $this->m_pelunasanpiutang->get_last_row_order_metode_by_id($cek->id);
                    $row        = $get_row;
                    $num        = 1;

                    // cek metode pelunasan tipe 
                    $gettipe = $this->m_pelunasanpiutang->cek_metode_pelunasan_tipe_by_id($cek->id);
                    if ($gettipe) {
                        if ($gettipe->tipe != $type) {
                            throw new \Exception('Metode Pelunasan Harus sama dengan yang sudah diiinput !', 200);
                        }
                    }

                    foreach ($arr_data as $ad) {

                        $ex = explode("|^", $ad);
                        $id_bukti_ex = $ex[1];
                        $no_bukti_ex = $ex[0];

                        // get data metode pelunasan 
                        if ($type == 'retur') {
                            $dt = $this->m_pelunasanpiutang->get_data_metode_pelunasan_retur_by_id($cek->partner_id, ['no_bukti' => $no_bukti_ex]);
                        } else {
                            $dt = $this->m_pelunasanpiutang->get_data_metode_pelunasan_by_id($cek->partner_id, $type, ['no_bukti' => $no_bukti_ex, 'id' => $id_bukti_ex]);
                        }

                        if (empty($dt)) {
                            throw new \Exception('Data Metode Pelunasan tidak ditemukan !', 200);
                        }

                        $cek_inv = $this->m_pelunasanpiutang->cek_metode_input_by_kode(['no_pelunasan' => $no_pelunasan, 'no_bukti' => $dt->no_bukti, 'id_bukti' => $id_bukti_ex]);

                        if (($cek_inv) > 0) {
                            throw new \Exception('Data Metode Pelunasan <b>' . $dt->no_bukti . '</b> sudah diinput  !', 200);
                        }
                        $uraian = ($type != 'retur') ? $dt->uraian : '';
                        $data_items[] = array(
                            'pelunasan_piutang_id'  => $cek->id,
                            'no_pelunasan'         => $no_pelunasan,
                            'no_bukti'             => $dt->no_bukti,
                            'tanggal_bukti'        => $dt->tanggal,
                            'currency_id'          => $dt->currency_id,
                            'currency'             => $dt->currency,
                            'kurs'                 => $dt->kurs,
                            'total_rp'             => $dt->total_rp,
                            'total_valas'          => $dt->total_valas,
                            'id_bukti'             => $dt->id, //id bukti detail
                            'tipe'                 => $type,
                            'tipe2'                 => $dt->tipe2, // kas, bank, giro
                            'row_order'            => $row,
                            'uraian'               => $uraian
                        );

                        $row++;
                        $log_add_items .= "(" . $num . ") " . $dt->no_bukti . " " . $dt->tanggal . " " . $uraian . " " . $dt->currency . " " . $dt->kurs . " " . $dt->total_rp . " " . $dt->total_valas;
                        $num++;
                    }

                    if (!empty($data_items)) {

                        $insert = $this->m_pelunasanpiutang->insert_data_pelunasan_piutang_metode($data_items);
                        if (!empty($insert)) {
                            throw new \Exception('Data Gagal Disimpan !', 200);
                        }


                        if ($type == 'um') {
                            $type_ket = 'Uang Muka';
                        } else if ($type == 'kas') {
                            $type_ket = 'Kas Bank';
                        } else {
                            $type_ket = 'Retur';
                        }

                        $jenis_log = "edit";
                        $note_log  = "Tambah Data Metode Pelunasan " . $type_ket . " di " . $no_pelunasan . " <br> " . $log_add_items;
                        $data_history = array(
                            'datelog'   => date("Y-m-d H:i:s"),
                            'kode'      => $no_pelunasan,
                            'jenis_log' => $jenis_log,
                            'note'      => $note_log
                        );
                        $this->_module->gen_history_ip($sub_menu, $username, $data_history);

                        $result = $this->distribusi_pelunasan_otomatis($no_pelunasan);
                        if (!empty($result)) {
                            throw new \Exception('Data Gagal Disimpan2 !', 200);
                        }

                        $result2 = $this->hitung_summary($no_pelunasan);
                        if (!empty($result2)) {
                            throw new \Exception('Summary Gagal di update !', 200);
                        }

                        $callback = array('status' => 'success', 'message' => 'Data Metode Pelunasan berhasil ditambah !', 'icon' => 'fa fa-success', 'type' => 'success');
                    } else {

                        $callback = array('status' => 'failed', 'message' => 'Gagal menambahkan Metode Pelunasan, Data List Metode Pelunasan tidak ada !', 'icon' => 'fa fa-warning', 'type' => 'danger');
                    }
                }

                if (!$this->_module->finishTransaction()) {
                    throw new \Exception('Gagal Mencari Data', 500);
                }
                $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($callback));
            }
        } catch (Exception $ex) {
            // finish transaction
            $this->_module->finishRollBack();
            $this->_module->rollbackTransaction();
            $this->output->set_status_header($ex->getCode() ?? 500)
                ->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode(array('status' => 'failed', 'message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        } finally {
            // unlock table
            $this->_module->unlock_tabel();
        }
    }



    function delete_pelunasan_piutang_metode()
    {
        try {
            //code...
            $validation = [
                [
                    'field' => 'no_pelunasan',
                    'label' => 'No Peluasnaan',
                    'rules' => ['required'],
                    'errors' => [
                        'required' => '{field} Kosong !',
                    ]
                ],
                [
                    'field' => 'id',
                    'label' => 'No Bukti',
                    'rules' => ['required'],
                    'errors' => [
                        'required' => '{field} Kosong !',
                    ]
                ],
            ];

            // start transaction
            $this->_module->startTransaction();

            //lock tabel
            $this->_module->lock_tabel('acc_pelunasan_piutang WRITE, acc_pelunasan_piutang_metode WRITE, main_menu_sub READ,log_history WRITE, user READ, acc_pelunasan_piutang_faktur WRITE, acc_pelunasan_piutang_summary WRITE, acc_pelunasan_piutang_summary_koreksi WRITE');

            $callback  = array();
            if (empty($this->session->userdata('status'))) { //cek apakah session masih adag
                // session habis
                throw new \Exception('Waktu Anda Telah Habis', 401);
            } else {

                $sub_menu   = $this->uri->segment(2);
                $username   = addslashes($this->session->userdata('username'));

                $this->form_validation->set_rules($validation);
                if ($this->form_validation->run() == FALSE) {
                    // throw new \Exception(array_values($this->form_validation->error_array())[0], 500);
                    $callback = array('status' => 'failed', 'field' => '', 'message' => array_values($this->form_validation->error_array())[0], 'icon' => 'fa fa-warning', 'type' => 'danger');
                } else {

                    $no_pelunasan  = $this->input->post('no_pelunasan');
                    $no_bukti      = $this->input->post('no_bukti');
                    $id            = $this->input->post('id');

                    // cek status done / cancel
                    $cek  = $this->m_pelunasanpiutang->get_data_by_code($no_pelunasan);

                    if (empty($cek)) {
                        throw new \Exception('Data Pelunasan tidak ditemukan !', 200);
                    } else if ($cek->status == 'done') {
                        $callback = array('status' => 'failed', 'message' => 'Maaf, Data Tidak Bisa Dihapus, Status Sudah Done !', 'icon' => 'fa fa-warning', 'type' => 'danger');
                    } else if ($cek->status == 'cancel') {
                        $callback = array('status' => 'failed', 'message' => 'Maaf, Data Tidak Bisa Dihapus, Status Cancel !', 'icon' => 'fa fa-warning', 'type' => 'danger');
                    } else {


                        $delete = $this->m_pelunasanpiutang->delete_pelunasan_piutang_metode_by_kode(['no_pelunasan' => $no_pelunasan, 'id' => $id]);
                        if (!empty($delete)) {
                            throw new \Exception('Gagal Menghapus data, Data tidak ditemukan', 200);
                        }

                        $jenis_log = "cancel";
                        $note_log  = "Mengahapus Data Bukti No. " . $no_bukti;
                        $data_history = array(
                            'datelog'   => date("Y-m-d H:i:s"),
                            'kode'      => $no_pelunasan,
                            'jenis_log' => $jenis_log,
                            'note'      => $note_log
                        );

                        // load in library
                        $this->_module->gen_history_ip($sub_menu, $username, $data_history);


                        $result2 = $this->hitung_summary($no_pelunasan);
                        if (!empty($result2)) {
                            throw new \Exception('Summary Gagal di update !', 200);
                        }


                        $result = $this->distribusi_pelunasan_otomatis($no_pelunasan);
                        if (!empty($result)) {
                            throw new \Exception('Distribusi Pelunasan Gagal !', 200);
                        }


                        $callback = array('status' => 'success', 'message' => 'Data Berhasil dihapus', 'icon' => 'fa fa-check', 'type' => 'success');
                    }
                }

                if (!$this->_module->finishTransaction()) {
                    throw new \Exception('Gagal menghapus Data', 500);
                }
                $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($callback));
            }
        } catch (Exception $ex) {
            // finish transaction
            $this->_module->finishRollBack();
            $this->_module->rollbackTransaction();
            $this->output->set_status_header($ex->getCode() ?? 500)
                ->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode(array('status' => 'failed', 'message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        } finally {
            // unlock table
            $this->_module->unlock_tabel();
        }
    }


    function distribusi_pelunasan_otomatis($no_pelunasan)
    {
        try {
            //code...

            // get total value pelunasan 
            $get_tot = $this->m_pelunasanpiutang->get_total_metode_pelunasan_by_no(['no_pelunasan' => $no_pelunasan, 'tipe <> ' => 'koreksi']);
            $rupiah = $get_tot->sum_rp;
            $valas  = $get_tot->sum_valas;

            $list_inv = $this->m_pelunasanpiutang->get_data_faktur_by_code($no_pelunasan);
            $tmp_update = array();
            $status_bayar = '';
            $pelunasan_rp_update = 0;
            $pelunasan_valas_update = 0;
            foreach ($list_inv as $li) {

                $sisa_piutang_rp = floatval($li->sisa_piutang_rp);
                $sisa_piutang_valas = floatval($li->sisa_piutang_valas);
                $sisa_rupiah = $rupiah;
                $sisa_valas = $valas;

                if ($sisa_piutang_rp > 0 or $sisa_piutang_valas > 0) {

                    if ($li->total_piutang_rp > 0) {
                        if ($sisa_rupiah >= $sisa_piutang_rp) {
                            $pelunasan_rp_update = $sisa_piutang_rp;
                            $rupiah  = $sisa_rupiah - $sisa_piutang_rp;
                            $status_bayar = 'lunas';
                        } else { // rupiah < $sisa_piutang_rp
                            $pelunasan_rp_update = $sisa_rupiah;
                            $rupiah  = 0;
                            ($pelunasan_rp_update <= 0) ? $status_bayar = 'belum_bayar' : $status_bayar = 'partial';
                        }
                    }

                    if ($li->total_piutang_valas > 0) {
                        if ($sisa_valas >= $sisa_piutang_valas) {
                            $pelunasan_valas_update = $sisa_piutang_valas;
                            $valas = $sisa_valas - $sisa_piutang_valas;
                            $status_bayar = 'lunas';
                        } else { // valas < $sisa_piutang_vlas
                            $pelunasan_valas_update = $sisa_valas;
                            $valas = 0;
                            // ($pelunasan_valas_update <= 0) ? $status_bayar = 'belum_bayar' : $status_bayar = 'partial';
                        }
                    }

                    $data_update = array(
                        'id'  => $li->id,
                        'pelunasan_rp'  => $pelunasan_rp_update,
                        'pelunasan_valas' => $pelunasan_valas_update,
                        'status_bayar'  => $status_bayar
                    );

                    array_push($tmp_update, $data_update);
                    $status_bayar = '';
                }
            }

            // var_dump($tmp_update);
            if (!empty($tmp_update)) {
                $update = $this->m_pelunasanpiutang->update_pelunasan_faktur_by_kode($tmp_update, $no_pelunasan);
                if (!empty($update)) {
                    throw new \Exception('Data Gagal Disimpan !', 200);
                }
            }

            return;
        } catch (Exception $ex) {
            return 1;
            // return $tmp_update;
        }
    }


    function distribusi_pelunasan_otomatis_koreksi($no_pelunasan)
    {
        try {
            //code...
            $list_inv = $this->m_pelunasanpiutang->get_data_faktur_by_code($no_pelunasan);
            $tmp_update = array();
            $get_tot2 = $this->m_pelunasanpiutang->get_total_metode_pelunasan_by_no(['no_pelunasan' => $no_pelunasan, 'tipe' => 'koreksi']);
            $rupiah = $get_tot2->sum_rp;
            $valas  = $get_tot2->sum_valas;
            $pelunasan_valas_update = 0;
            $pelunasan_rp_update    = 0;
            foreach ($list_inv as $li) {
                $sisa_piutang_rp = floatval($li->sisa_piutang_rp);
                $sisa_piutang_valas = floatval($li->sisa_piutang_valas);
                $sisa_rupiah = $rupiah;
                $sisa_valas = $valas;

                if ($sisa_piutang_rp > 0 or $sisa_piutang_valas > 0) {

                    if ($li->total_piutang_rp > 0) {
                        $pelunasan_rp_update = $sisa_piutang_rp - $sisa_rupiah;
                        $sisa_rupiah  = 0;
                    }

                    if ($li->total_piutang_valas > 0) {
                        $pelunasan_valas_update = $sisa_piutang_valas - $sisa_valas;
                        $sisa_valas  = 0;
                    }

                    $status_bayar = 'partial';

                    $data_update = array(
                        'id'  => $li->id,
                        'pelunasan_rp'  => $pelunasan_rp_update,
                        'pelunasan_valas' => $pelunasan_valas_update,
                        'status_bayar'  => $status_bayar
                    );

                    array_push($tmp_update, $data_update);
                    $status_bayar = '';
                }
            }

            // var_dump($tmp_update);
            if (!empty($tmp_update)) {
                $update = $this->m_pelunasanpiutang->update_pelunasan_faktur_by_kode($tmp_update, $no_pelunasan);
                if (!empty($update)) {
                    throw new \Exception('Data Gagal Disimpan !', 200);
                }
            }

            return;
        } catch (Exception $ex) {
            return 1;
            // return $tmp_update;
        }
    }




    function hitung_summary($no_pelunasan)
    {
        try {
            //code...

            $cek  = $this->m_pelunasanpiutang->get_data_by_code($no_pelunasan);
            if (empty($cek)) {
                throw new \Exception('Gagal, Data tidak ditemukan', 200);
            }

            // get total_piutang
            $where_idr = ["no_pelunasan" => $no_pelunasan, "tipe <> " => "koreksi"];
            $get_total_idr = $this->m_pelunasanpiutang->get_total_pelunasan($where_idr);

            $where_valas = ["no_pelunasan" => $no_pelunasan, "tipe <> " => "koreksi"];
            $get_total_valas = $this->m_pelunasanpiutang->get_total_pelunasan($where_valas);

            $get_curr   = $this->m_pelunasanpiutang->get_currency_by_pelunasan(['no_pelunasan' => $no_pelunasan, 'currency' => 'IDR']);

            $get_curr_valas   = $this->m_pelunasanpiutang->get_currency_by_pelunasan(['no_pelunasan' => $no_pelunasan, 'currency <>' => 'IDR']);

            $get_piutang_idr   = $this->m_pelunasanpiutang->get_total_piutang(["no_pelunasan" => $no_pelunasan]);
            $get_piutang_valas = $this->m_pelunasanpiutang->get_total_piutang(["no_pelunasan" => $no_pelunasan]);

            $where_idr_koreksi = ["no_pelunasan" => $no_pelunasan, "tipe" => "koreksi"];
            $get_total_idr_koreksi = $this->m_pelunasanpiutang->get_total_pelunasan($where_idr_koreksi);
            $total_koreksi_rp = $get_total_idr_koreksi->total_pelunasan_rp ?? 0;

            $where_valas_koreksi = ["no_pelunasan" => $no_pelunasan, "tipe" => "koreksi"];
            $get_total_valas_koreksi = $this->m_pelunasanpiutang->get_total_pelunasan($where_valas_koreksi);
            $total_koreksi_valas = $get_total_valas_koreksi->total_pelunasan_valas ?? 0;

            $total_piutang_rp = $get_piutang_idr->total_piutang_rp ?? 0;
            $total_pelunasan_rp = $get_total_idr->total_pelunasan_rp ?? 0;
            $selisih_rp =  round($total_piutang_rp, 2) - round($total_pelunasan_rp, 2) + round($total_koreksi_rp, 2);



            if ($selisih_rp < 0) {
                $keterangan_rp = "Lebih Bayar";
            } else if ($selisih_rp > 0) {

                // cek invoice yng status nya bukan lunas ada ga 
                $keterangan_rp = "Kurang Bayar";
                // $ceksb = $this->m_pelunasanhutang->get_invoice_by_code(['no_pelunasan' => $no_pelunasan, 'status_bayar <>' => 'lunas']);
                // if (!empty($ceksb)) { // tidak kosong
                // } else {
                //     $keterangan_rp = '';
                //     $selisih_rp    = 0;
                // }
            } else {
                $keterangan_rp = '';
            }

            // cek metode plunasan pakai uang muka atau kas bank retur
            $cek_mt = $this->m_pelunasanpiutang->get_metode_by_code(['no_pelunasan' => $no_pelunasan, 'tipe' => 'um']);
            if ($cek_mt) {
                $keterangan_rp = 'Uang Muka';
            }

            $cek_mt2 = $this->m_pelunasanpiutang->get_metode_by_code(['no_pelunasan' => $no_pelunasan, 'tipe' => 'koreksi']);
            if ($cek_mt2) {
                $keterangan_rp = 'Koreksi Kurs';
            }

            $insert_summary[] = array(
                'tipe_currency' => 'Rp',
                'currency_id'   => $get_curr->currency_id ?? 1,
                'currency'      => $get_curr->currency ?? 'IDR',
                'kurs'          => $get_curr->kurs ?? 1,
                'no_pelunasan'  => $no_pelunasan,
                'pelunasan_piutang_id' => $cek->id,
                'total_piutang' => $total_piutang_rp,
                'total_koreksi' => $total_koreksi_rp,
                'total_pelunasan' => $total_pelunasan_rp,
                'keterangan' => $keterangan_rp,
                'selisih'   => $selisih_rp,
                'koreksi'   => '',
            );


            $total_piutang_valas = (float) $get_piutang_valas->total_piutang_valas ?? 0;
            $total_pelunasan_valas = (float) $get_total_valas->total_pelunasan_valas ?? 0;
            $selisih_valas = round($total_piutang_valas, 2) - round($total_pelunasan_valas, 2) + round($total_koreksi_valas, 2);

            if ($selisih_valas < 0) {
                $keterangan_valas = "Lebih Bayar";
            } else if ($selisih_valas > 0) {
                // cek invoice yng status nya bukan lunas ada ga 
                // $ceksb = $this->m_pelunasanhutang->get_invoice_by_code(['no_pelunasan' => $no_pelunasan, 'status_bayar <>' => 'lunas']);
                $keterangan_valas = "Kurang Bayar";

                // if (!empty($ceksb)) { // tidak kosong
                // } else {
                //     $keterangan_valas = '';
                //     $selisih_valas    = 0;
                // }
            } else {
                $keterangan_valas = '';
            }

            if ($cek_mt && $selisih_valas != 0) {
                $keterangan_valas = 'Uang Muka';
            }

            if ($cek_mt2 && $selisih_valas != 0) {
                $keterangan_valas = 'Koreksi Kurs';
            }

            $insert_summary[] = array(
                'tipe_currency' => 'Valas',
                'currency_id'   => $get_curr_valas->currency_id ?? 0,
                'currency'      => $get_curr_valas->currency ?? '',
                'kurs'          => $get_curr_valas->kurs ?? 0,
                'no_pelunasan'  => $no_pelunasan,
                'pelunasan_piutang_id' => $cek->id,
                'total_piutang' => $total_piutang_valas,
                'total_koreksi' => $total_koreksi_valas,
                'total_pelunasan' => $total_pelunasan_valas,
                'keterangan' => $keterangan_valas,
                'selisih'   => $selisih_valas,
                'koreksi'   => '',
            );


            //delete info by no pelunasan
            $delete = $this->m_pelunasanpiutang->delete_pelunasan_piutang_summary_by_kode($no_pelunasan);
            if (!empty($delete)) {
                throw new \Exception('Gagal, Data tidak ditemukan', 200);
            }


            //insert summary by no pelunasan
            $insert = $this->m_pelunasanpiutang->insert_data_pelunasan_piutang_summary($insert_summary);
            if (!empty($insert)) {
                throw new \Exception('Summary Gagal di update !', 200);
            }

            $this->m_pelunasanpiutang->delete_pelunasan_piutang_summary_koreksi_by_id(['no_pelunasan' => $no_pelunasan]);


            return;
        } catch (Exception $ex) {
            return 1;
        }
    }


    function get_coa_default($jenis_koreksi, $get_sum, $posisi, $no_pelunasan)
    {

        // $get_sum = $this->m_pelunasanpiutang->get_data_summary_by_id($id_summary);
        $coa     = '';
        $kode_coa = '';
        $nama_coa = '';
        if ($jenis_koreksi == 'uang_muka') {
            if ($posisi == 'D') {
                $cek  = $this->m_pelunasanpiutang->get_data_by_code($no_pelunasan);
                $data_mt = $this->m_pelunasanpiutang->get_data_metode_by_code($no_pelunasan);
                if($data_mt){
                    foreach ($data_mt as $mp){
                        $dt = $this->m_pelunasanpiutang->get_data_metode_pelunasan_by_id($cek->partner_id, 'um', ['no_bukti' => $mp->no_bukti, 'id' => $mp->id_bukti]);
                        if($dt){
                            $kode_coa = $dt->kode_coa;
                            $get_nm   = $this->m_pelunasanhutang->get_coa_by_kode(['kode_coa'=>$kode_coa]);
                            $nama_coa = isset($get_nm->nama) ? $get_nm->nama : '';
                            // $this->coa_um[] =  array('posisi' => 'C', 'kode_coa' => $dt->kode_coa);
                            break;
                        }
                    }
                }
            } else {
                $get_coa = $this->m_pelunasanpiutang->get_coa_default_by_kode(['a.kode' => $jenis_koreksi, 'b.posisi' => $posisi]);
                $kode_coa = isset($get_coa->kode_coa) ? $get_coa->kode_coa : '';
                $nama_coa = isset($get_coa->nama_coa) ? $get_coa->nama_coa : '';
            }
        } else {
            if ((float) $get_sum->selisih > 0) {
                $get_coa = $this->m_pelunasanpiutang->get_coa_default_by_kode(['a.kode' => $jenis_koreksi, 'b.posisi' => $posisi, 'selisih' => 'rugi']);
            } else if ((float) $get_sum->selisih < 0) {
                $get_coa = $this->m_pelunasanpiutang->get_coa_default_by_kode(['a.kode' => $jenis_koreksi, 'b.posisi' => $posisi, 'selisih' => 'laba']);
            }
            $kode_coa = isset($get_coa->kode_coa) ? $get_coa->kode_coa : '';
            $nama_coa = isset($get_coa->nama_coa) ? $get_coa->nama_coa : '';
        }

        return array('kode_coa' => $kode_coa,  'nama_coa' => $nama_coa);
    }


    public function get_view_koreksi()
    {
        $no_pelunasan = $this->input->post("no_pelunasan");
        $id      = $this->input->post("id"); // id_summary
        $jenis_koreksi = $this->input->post('jenis_koreksi');
        $get_sum = $this->m_pelunasanpiutang->get_data_summary_by_id($id);
        $coa_default_debit = $this->get_coa_default($jenis_koreksi, $get_sum, 'D', $no_pelunasan);
        $coa_default_credit = $this->get_coa_default($jenis_koreksi, $get_sum, 'C', $no_pelunasan);
        $get_coa_debit  = $this->m_pelunasanpiutang->get_coa_summary_id(['pelunasan_summary_id' => $id, 'posisi' => 'D', 'koreksi' => $jenis_koreksi])->row();
        $get_coa_credit  = $this->m_pelunasanpiutang->get_coa_summary_id(['pelunasan_summary_id' => $id, 'posisi' => 'C',  'koreksi' => $jenis_koreksi])->row();
        $view = $this->load->view('modal/v_pelunasan_piutang_koreksi_modal', ["id_summary" => $id, "no_pelunasan" => $no_pelunasan, 'jenis_koreksi' => $jenis_koreksi, 'get_sum' => $get_sum, 'get_coa_debit' => $get_coa_debit, 'get_coa_credit' => $get_coa_credit, 'coa_default_debit' => $coa_default_debit, 'coa_default_credit' => $coa_default_credit], true);
        $this->output->set_status_header(200)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode(['data' => $view]));
    }


    function save_koreksi()
    {
        try {
            //code...
            $validation = [
                [
                    'field' => 'no_pelunasan',
                    'label' => 'No Peluasnaan',
                    'rules' => ['required'],
                    'errors' => [
                        'required' => '{field} Kosong !',
                    ]
                ],
                [
                    'field' => 'id_summary',
                    'label' => 'Koreksi Kosong',
                    'rules' => ['required'],
                    'errors' => [
                        'required' => '{field} Kosong !',
                    ]
                ],
            ];

            // start transaction
            $this->_module->startTransaction();

            //lock tabel
            $this->_module->lock_tabel('acc_pelunasan_piutang WRITE, acc_pelunasan_piutang_metode WRITE, main_menu_sub READ,log_history WRITE, user READ, acc_pelunasan_piutang_faktur WRITE, acc_pelunasan_piutang_summary WRITE, acc_pelunasan_piutang_summary_koreksi WRITE, acc_coa READ, acc_pelunasan_koreksi_piutang WRITE');

            $callback  = array();
            if (empty($this->session->userdata('status'))) { //cek apakah session masih adag
                // session habis
                throw new \Exception('Waktu Anda Telah Habis', 401);
            } else {

                $sub_menu   = $this->uri->segment(2);
                $username   = addslashes($this->session->userdata('username'));

                $this->form_validation->set_rules($validation);
                if ($this->form_validation->run() == FALSE) {
                    // throw new \Exception(array_values($this->form_validation->error_array())[0], 500);
                    $callback = array('status' => 'failed', 'field' => '', 'message' => array_values($this->form_validation->error_array())[0], 'icon' => 'fa fa-warning', 'type' => 'danger');
                } else {

                    $no_pelunasan  = $this->input->post('no_pelunasan');
                    $id_summary    = $this->input->post('id_summary');
                    $jenis_koreksi  = $this->input->post('jenis_koreksi');
                    $debit          = $this->input->post('debit');
                    $credit        = $this->input->post('credit');

                    // cek status done / cancel
                    $cek  = $this->m_pelunasanpiutang->get_data_by_code($no_pelunasan);

                    if (empty($cek)) {
                        throw new \Exception('Data Pelunasan tidak ditemukan !', 200);
                    } else if ($cek->status == 'done') {
                        $callback = array('status' => 'failed', 'message' => 'Maaf, Data Tidak Bisa Disimpan, Status Sudah Done !', 'icon' => 'fa fa-warning', 'type' => 'danger');
                    } else if ($cek->status == 'cancel') {
                        $callback = array('status' => 'failed', 'message' => 'Maaf, Data Tidak Bisa Disimpan, Status Cancel !', 'icon' => 'fa fa-warning', 'type' => 'danger');
                    } else {

                        $get_sum = $this->m_pelunasanpiutang->get_data_summary_by_id($id_summary);

                        if (empty($get_sum)) {
                            throw new \Exception('Data Info Summary tidak ditemukan ', 200);
                        }


                        // update summary by id  
                        $tmp_update = array();
                        $data_update = array(
                            'id'                    => $id_summary,
                            'koreksi'               => $jenis_koreksi ?? '',
                        );
                        array_push($tmp_update, $data_update);
                        $update = $this->m_pelunasanpiutang->update_data_pelunasan_piutang_summary($tmp_update);
                        if (!empty($update)) {
                            throw new \Exception('Gagal Update Info', 500);
                        }

                        // delete summary coa by id lalu insert
                        $this->m_pelunasanpiutang->delete_pelunasan_piutang_summary_koreksi_by_id(['no_pelunasan' => $no_pelunasan, 'pelunasan_summary_id' => $id_summary]);

                        $get_coa = $this->m_pelunasanhutang->get_coa_by_kode(['kode_coa' => $debit]);
                        $data[] = array(
                            'pelunasan_piutang_id'   => $cek->id,
                            'no_pelunasan'          => $no_pelunasan,
                            'pelunasan_summary_id'  => $id_summary,
                            'posisi'                => 'D',
                            'kode_coa'              => $debit,
                            'nama_coa'              => $get_coa->nama ?? ''
                        );
                        $get_coa = $this->m_pelunasanhutang->get_coa_by_kode(['kode_coa' => $credit]);
                        $data[] = array(
                            'pelunasan_piutang_id'   => $cek->id,
                            'no_pelunasan'          => $no_pelunasan,
                            'pelunasan_summary_id'  => $id_summary,
                            'posisi'                => 'C',
                            'kode_coa'              => $credit,
                            'nama_coa'              => $get_coa->nama ?? ''
                        );

                        $insert = $this->m_pelunasanpiutang->insert_data_pelunasan_piutang_summary_koreksi($data);
                        if (!empty($insert)) {
                            throw new \Exception('Gagal Menyimpan Koreksi', 500);
                        }

                        $cek_koreksi = $this->m_pelunasanpiutang->get_koreksi_by_kode(['kode' => $jenis_koreksi]);

                        $log_edit_items  = 'Kokresi Info ' . $get_sum->tipe_currency . " = " . ($cek_koreksi->nama_koreksi ?? '');

                        $jenis_log = "edit";
                        $note_log  = "Edit Koreksi Info " . $no_pelunasan . " <br> " . $log_edit_items;
                        $data_history = array(
                            'datelog'   => date("Y-m-d H:i:s"),
                            'kode'      => $no_pelunasan,
                            'jenis_log' => $jenis_log,
                            'note'      => $note_log
                        );
                        $this->_module->gen_history_ip($sub_menu, $username, $data_history);


                        $callback = array('status' => 'success', 'message' => 'Data Koreksi Info Berhasil diubah !', 'icon' => 'fa fa-success', 'type' => 'success');
                    }
                }
            }
            if (!$this->_module->finishTransaction()) {
                throw new \Exception('Gagal menghapus Data', 500);
            }
            $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($callback));
        } catch (Exception $ex) {
            // finish transaction
            $this->_module->finishRollBack();
            $this->_module->rollbackTransaction();
            $this->output->set_status_header($ex->getCode() ?? 500)
                ->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode(array('status' => 'failed', 'message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        } finally {
            // unlock table
            $this->_module->unlock_tabel();
        }
    }

    function delete_koreksi()
    {
        try {
            //code...
            $callback = array();
            $sub_menu = $this->uri->segment(2);
            $username = addslashes($this->session->userdata('username'));

            if (empty($this->session->userdata('status'))) {
                throw new \Exception('Waktu Anda Telah Habis', 401); // Unauthorized / session habis
            }

            $this->_module->startTransaction();
            $this->_module->lock_tabel("acc_pelunasan_piutang WRITE, main_menu_sub READ, log_history WRITE, token_increment WRITE, partner WRITE, user READ, acc_pelunasan_piutang_faktur WRITE, acc_pelunasan_piutang_metode WRITE, acc_pelunasan_piutang_summary WRITE, acc_pelunasan_piutang_summary_koreksi WRITE");

            $no_pelunasan  = $this->input->post('no_pelunasan');
            $id_summary    = $this->input->post('id_summary');
            // $jenis_koreksi  = $this->input->post('jenis_koreksi');

            $get_sum = $this->m_pelunasanpiutang->get_data_summary_by_id($id_summary);

            if (empty($get_sum)) {
                throw new \Exception('Data Info Summary tidak ditemukan ', 200);
            }

            $this->m_pelunasanpiutang->delete_pelunasan_piutang_summary_koreksi_by_id(['no_pelunasan' => $no_pelunasan, 'pelunasan_summary_id' => $id_summary]);

            $tmp_update = array();
            $data_update = array('id' => $id_summary, 'koreksi' => '');
            array_push($tmp_update, $data_update);
            $update = $this->m_pelunasanpiutang->update_data_pelunasan_piutang_summary($tmp_update);

            if (!$this->_module->finishTransaction()) {
                throw new \Exception('Gagal menghapus Data', 500);
            }

            $log_edit_items  = 'Kokresi Info ' . $get_sum->tipe_currency;


            $jenis_log = "cancel";
            $note_log  = "Hapus Koreksi Info " . $no_pelunasan . " <br> " . $log_edit_items;
            $data_history = array(
                'datelog'   => date("Y-m-d H:i:s"),
                'kode'      => $no_pelunasan,
                'jenis_log' => $jenis_log,
                'note'      => $note_log
            );
            $this->_module->gen_history_ip($sub_menu, $username, $data_history);

            $callback = array('status' => 'success', 'message' => 'Data berhasil dihapus !', 'icon' => 'fa fa-success', 'type' => 'success');
            $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($callback));
        } catch (Exception $ex) {
            // finish transaction
            $this->_module->finishRollBack();
            $this->_module->rollbackTransaction();
            $this->output->set_status_header($ex->getCode() ?? 500)
                ->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode(array('status' => 'failed', 'message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        } finally {
            // unlock table
            $this->_module->unlock_tabel();
        }
    }

    function save_koreksi2()
    {
        try {
            //code...
            $validation = [
                [
                    'field' => 'no_pelunasan',
                    'label' => 'No Peluasnaan',
                    'rules' => ['required'],
                    'errors' => [
                        'required' => '{field} Kosong !',
                    ]
                ],
                [
                    'field' => 'id_summary',
                    'label' => 'Koreksi Kosong',
                    'rules' => ['required'],
                    'errors' => [
                        'required' => '{field} Kosong !',
                    ]
                ],
            ];

            // start transaction
            $this->_module->startTransaction();

            //lock tabel
            $this->_module->lock_tabel('acc_pelunasan_piutang WRITE, acc_pelunasan_piutang_metode WRITE, main_menu_sub READ,log_history WRITE, user READ, acc_pelunasan_piutang_faktur WRITE, acc_pelunasan_piutang_summary WRITE, acc_pelunasan_piutang_summary_koreksi WRITE, acc_coa READ, acc_pelunasan_koreksi_piutang WRITE');

            $callback  = array();
            if (empty($this->session->userdata('status'))) { //cek apakah session masih adag
                // session habis
                throw new \Exception('Waktu Anda Telah Habis', 401);
            } else {

                $sub_menu   = $this->uri->segment(2);
                $username   = addslashes($this->session->userdata('username'));

                $this->form_validation->set_rules($validation);
                if ($this->form_validation->run() == FALSE) {
                    // throw new \Exception(array_values($this->form_validation->error_array())[0], 500);
                    $callback = array('status' => 'failed', 'field' => '', 'message' => array_values($this->form_validation->error_array())[0], 'icon' => 'fa fa-warning', 'type' => 'danger');
                } else {

                    $no_pelunasan  = $this->input->post('no_pelunasan');
                    $id_summary    = $this->input->post('id_summary');
                    $jenis_koreksi  = $this->input->post('jenis_koreksi');

                    // cek status done / cancel
                    $cek  = $this->m_pelunasanpiutang->get_data_by_code($no_pelunasan);

                    if (empty($cek)) {
                        throw new \Exception('Data Pelunasan tidak ditemukan !', 200);
                    } else if ($cek->status == 'done') {
                        $callback = array('status' => 'failed', 'message' => 'Maaf, Data Tidak Bisa Disimpan, Status Sudah Done !', 'icon' => 'fa fa-warning', 'type' => 'danger');
                    } else if ($cek->status == 'cancel') {
                        $callback = array('status' => 'failed', 'message' => 'Maaf, Data Tidak Bisa Disimpan, Status Cancel !', 'icon' => 'fa fa-warning', 'type' => 'danger');
                    } else {

                        $get_sum = $this->m_pelunasanpiutang->get_data_summary_by_id($id_summary);

                        if (empty($get_sum)) {
                            throw new \Exception('Data Info Summary tidak ditemukan ', 200);
                        }


                        // update summary by id  
                        $tmp_update = array();
                        $data_update = array(
                            'id'                    => $id_summary,
                            'koreksi'               => $jenis_koreksi ?? '',
                        );
                        array_push($tmp_update, $data_update);
                        $update = $this->m_pelunasanpiutang->update_data_pelunasan_piutang_summary($tmp_update);
                        if (!empty($update)) {
                            throw new \Exception('Gagal Update Info', 500);
                        }

                        // delete summary coa by id lalu insert
                        $this->m_pelunasanpiutang->delete_pelunasan_piutang_summary_koreksi_by_id(['no_pelunasan' => $no_pelunasan, 'pelunasan_summary_id' => $id_summary]);

                        $cek_koreksi = $this->m_pelunasanpiutang->get_koreksi_by_kode(['kode' => $jenis_koreksi]);

                        $log_edit_items  = 'Kokresi Info ' . $get_sum->tipe_currency . " = " . ($cek_koreksi->nama_koreksi ?? '');

                        $jenis_log = "edit";
                        $note_log  = "Edit Koreksi Info " . $no_pelunasan . " <br> " . $log_edit_items;
                        $data_history = array(
                            'datelog'   => date("Y-m-d H:i:s"),
                            'kode'      => $no_pelunasan,
                            'jenis_log' => $jenis_log,
                            'note'      => $note_log
                        );
                        $this->_module->gen_history_ip($sub_menu, $username, $data_history);


                        $callback = array('status' => 'success', 'message' => 'Data Koreksi Info Berhasil diubah !', 'icon' => 'fa fa-success', 'type' => 'success');
                    }
                }
            }
            if (!$this->_module->finishTransaction()) {
                throw new \Exception('Gagal menghapus Data', 500);
            }
            $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($callback));
        } catch (Exception $ex) {
            // finish transaction
            $this->_module->finishRollBack();
            $this->_module->rollbackTransaction();
            $this->output->set_status_header($ex->getCode() ?? 500)
                ->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode(array('status' => 'failed', 'message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        } finally {
            // unlock table
            $this->_module->unlock_tabel();
        }
    }

    public function get_view_edit_distribusi()
    {
        $no_pelunasan = $this->input->post("no_pelunasan");
        $partner      = $this->input->post("partner"); // partner_id
        $id           = $this->input->post('id'); // id pelunasan_piutang_faktur
        $get_data     = $this->m_pelunasanpiutang->get_acc_pelunasan_piutang_faktur_id($id);
        $list_status_bayar = $this->statusBayar;
        $view = $this->load->view('modal/v_pelunasan_piutang_faktur_edit_modal', ["partner" => $partner, "no_pelunasan" => $no_pelunasan, "id_phi" => $id, "get_data" => $get_data, 'statusBayar' => $list_status_bayar], true);
        $this->output->set_status_header(200)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode(['data' => $view]));
    }

    function update_pelunasan_faktur()
    {
        try {
            //code...
            if (empty($this->session->userdata('status'))) { //cek apakah session masih adag
                // session habis
                throw new \Exception('Waktu Anda Telah Habis', 401);
            } else {

                $validation = [
                    [
                        'field' => 'no_pelunasan',
                        'label' => 'No Peluasnaan',
                        'rules' => ['required'],
                        'errors' => [
                            'required' => '{field} Kosong !',
                        ]
                    ],
                    [
                        'field' => 'id',
                        'label' => 'Invoice',
                        'rules' => ['required'],
                        'errors' => [
                            'required' => '{field} Kosong !',
                        ]
                    ],
                    [
                        'field' => 'pelunasan_rp',
                        'label' => 'Pelunasan (Rp)',
                        'rules' => ['trim', 'required', 'regex_match[/^-?\d*\.?\d*$/]'],
                        'errors' => [
                            'required' => '{field} Jika kosong maka isi angka 0',
                            "regex_match" => "{field} harus berupa number / desimal"
                        ]
                    ],
                    [
                        'field' => 'pelunasan_valas',
                        'label' => 'Pelunasan (Valas)',
                        'rules' => ['trim', 'required', 'regex_match[/^-?\d*\.?\d*$/]'],
                        'errors' => [
                            'required' => '{field} Jika kosong maka isi angka 0',
                            "regex_match" => "{field} harus berupa number / desimal"
                        ]
                    ],
                    [
                        'field' => 'status_bayar',
                        'label' => 'Status Bayar',
                        'rules' => ['required'],
                        'errors' => [
                            'required' => '{field} tidak boleh Kosong !',
                        ]
                    ],

                ];

                $no_pelunasan  = $this->input->post('no_pelunasan');
                $no_faktur     = $this->input->post('no_faktur');
                $status_bayar  = $this->input->post('status_bayar');
                if (empty($no_pelunasan)) {
                    throw new \Exception('No Pelunasan Kosong !', 200);
                }

                // cek status done / cancel
                $cek  = $this->m_pelunasanpiutang->get_data_by_code($no_pelunasan);

                if (empty($cek)) {
                    throw new \Exception('Data Pelunasan tidak ditemukan !', 200);
                } else if ($cek->status == 'done') {
                    $callback = array('status' => 'failed', 'message' => 'Maaf, Data Tidak Bisa Disimpan, Status Sudah Done !', 'icon' => 'fa fa-warning', 'type' => 'danger');
                } else if ($cek->status == 'cancel') {
                    $callback = array('status' => 'failed', 'message' => 'Maaf, Data Tidak Bisa Disimpan, Status Cancel !', 'icon' => 'fa fa-warning', 'type' => 'danger');
                } else {
                    $sub_menu   = $this->uri->segment(2);
                    $username   = addslashes($this->session->userdata('username'));
                    $callback  = array();
                    $tmp_update = array();
                    $this->form_validation->set_rules($validation);
                    if ($this->form_validation->run() == FALSE) {
                        // throw new \Exception(array_values($this->form_validation->error_array())[0], 500);
                        $callback = array('status' => 'failed', 'field' => '', 'message' => array_values($this->form_validation->error_array())[0], 'icon' => 'fa fa-warning', 'type' => 'danger');
                    } else {

                        $id            = $this->input->post('id');
                        $pelunasan_rp         = $this->input->post('pelunasan_rp');
                        $pelunasan_valas      = $this->input->post('pelunasan_valas');


                        $data_update = array(
                            'id' => $id,
                            'pelunasan_rp' => $pelunasan_rp,
                            'pelunasan_valas' => $pelunasan_valas,
                            'status_bayar' => $status_bayar
                        );

                        array_push($tmp_update, $data_update);

                        $get_piutang_fak1   = $this->m_pelunasanpiutang->get_total_piutang(['no_pelunasan' => $no_pelunasan]);

                        if ((float) round($pelunasan_rp, 2) > (float) round($get_piutang_fak1->total_piutang_rp, 2)) {
                            throw new \Exception('Distribusi Pelunasan (Rp) tidak boleh melebihi Sisa piutang (Rp) ' . $pelunasan_rp, 200);
                        }

                        if ((float) round($pelunasan_valas, 2) > (float) round($get_piutang_fak1->total_piutang_valas, 2)) {
                            throw new \Exception('Distribusi Pelunasan (Valas) tidak boleh melebihi Sisa piutang (Valas) ', 200);
                        }

                        $get_piutang_fak   = $this->m_pelunasanpiutang->get_total_piutang(['no_pelunasan' => $no_pelunasan, 'id <>' => $id]);

                        $get_tot = $this->m_pelunasanpiutang->get_total_metode_pelunasan_by_no(['no_pelunasan' => $no_pelunasan]);

                        if (isset($get_tot->no_pelunasan)) {

                            // cek metode pelunasan tipe 
                            $gettipe = $this->m_pelunasanpiutang->cek_metode_pelunasan_tipe_by_id($cek->id);
                            if ($gettipe) {
                                if ($gettipe->tipe == 'koreksi') {
                                    throw new \Exception('Data Distribusi Pelunasan tidak bisa disimpan, karena Metode Pelunasan menggunakan Koreksi Kurs Bulan !', 200);
                                }
                            }

                            $rupiah = $get_tot->sum_rp ?? 0;
                            $valas  = $get_tot->sum_valas ?? 0;

                            if (((float) $get_piutang_fak->total_pelunasan_rp + (float) $pelunasan_rp) > (float) $rupiah) {
                                throw new \Exception('Distribusi Pelunasan (Rp) tidak boleh melebihi Total Pelunasan (Rp) ', 200);
                            }

                            if (((float) $get_piutang_fak->total_pelunasan_valas + (float) $pelunasan_valas)  > (float)  $valas) {
                                throw new \Exception('Distribusi Pelunasan (Valas) tidak boleh melebihi Total Pelunasan (Valas) ', 200);
                            }

                            if ($status_bayar == 'belum_bayar' && (($pelunasan_rp) > 0 || ($pelunasan_valas) > 0)) {
                                throw new \Exception('Status Bayar tidak bisa diubah ke <b>Belum Bayar</b>, Karena sudah ada pelunasan !', 200);
                            }


                            if ($status_bayar == 'partial' && ((round((float) $pelunasan_rp, 2) == round($get_piutang_fak1->total_piutang_rp, 2) && $get_piutang_fak1->total_piutang_rp) || (round((float)  $pelunasan_valas, 2) ==  round($get_piutang_fak1->total_piutang_valas, 2) && $get_piutang_fak1->total_piutang_valas))) {
                                throw new \Exception('Status Bayar tidak bisa diubah ke <b>Partial</b>, Karena Total Pelunasan sama dengan Sisa Piutang !', 200);
                            }

                            if ($status_bayar == 'lunas') {
                                $cek = $this->m_pelunasanpiutang->get_data_summary_by_code($no_pelunasan);
                                foreach($cek as $ck){
                                    if($ck->keterangan == 'Uang Muka') {
                                        throw new \Exception('Status Bayar tidak bisa diubah ke <b>Lunas</b>, Karena Pelunasan memakai <b>Uang Muka</b> !', 200);
                                    }
                                }
                            }

                            $update = $this->m_pelunasanpiutang->update_pelunasan_faktur_by_kode($tmp_update, $no_pelunasan);
                        } else {
                            throw new \Exception('Metode Pelunasan Masih Kosong !', 200);
                        }


                        $jenis_log = "edit";
                        $note_log  = "Ubah Data Faktur No. " . $no_faktur;
                        $data_history = array(
                            'datelog'   => date("Y-m-d H:i:s"),
                            'kode'      => $no_pelunasan,
                            'jenis_log' => $jenis_log,
                            'note'      => $note_log
                        );

                        // load in library
                        $this->_module->gen_history_ip($sub_menu, $username, $data_history);

                        $result2 = $this->hitung_summary($no_pelunasan);
                        if (!empty($result2)) {
                            throw new \Exception('Summary Gagal di update !', 200);
                        }

                        $callback = array('status' => 'success', 'message' => 'Data Berhasil diubah', 'icon' => 'fa fa-check', 'type' => 'success');
                    }
                }

                $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($callback));
            }
        } catch (Exception $ex) {
            // finish transaction
            $this->_module->finishRollBack();
            $this->_module->rollbackTransaction();
            $this->output->set_status_header($ex->getCode() ?? 500)
                ->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode(array('status' => 'failed', 'message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        } finally {
            // unlock table
            $this->_module->unlock_tabel();
        }
    }


    public function get_view_koreksi_kurs()
    {
        $no_pelunasan = $this->input->post("no_pelunasan");
        $get_head     = $this->m_pelunasanpiutang->get_data_by_code($no_pelunasan);
        $view = $this->load->view('modal/v_pelunasan_piutang_koreksi_kurs', ["get_head" => $get_head, "no_pelunasan" => $no_pelunasan], true);
        $this->output->set_status_header(200)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode(['data' => $view]));
    }

    function save_koreksi_kurs()
    {
        try {
            //code...
            if (empty($this->session->userdata('status'))) { //cek apakah session masih adag
                // session habis
                throw new \Exception('Waktu Anda Telah Habis', 401);
            } else {

                $validation = [
                    [
                        'field' => 'no_pelunasan',
                        'label' => 'No Pelunasan',
                        'rules' => ['required'],
                        'errors' => [
                            'required' => '{field} Kosong !',
                        ]
                    ],
                    [
                        'field' => 'kurs',
                        'label' => 'Kurs',
                        'rules' => ['trim', 'required', 'regex_match[/^-?\d*\.?\d*$/]'],
                        'errors' => [
                            'required' => '{field} Jika kosong maka isi angka 0',
                            "regex_match" => "{field} harus berupa number / desimal"
                        ]
                    ],
                    // [
                    //     'field' => 'value_valas',
                    //     'label' => 'Total Valas',
                    //     'rules' => ['trim', 'required', 'regex_match[/^-?\d*\.?\d*$/]'],
                    //     'errors' => [
                    //         'required' => '{field} Jika kosong maka isi angka 0',
                    //         "regex_match" => "{field} harus berupa number / desimal"
                    //     ]
                    // ],

                ];

                $no_pelunasan   = $this->input->post('no_pelunasan');
                $tanggal        = $this->input->post('tanggal');
                $uraian         = $this->input->post('uraian');
                // $currency       = $this->input->post('currency');
                $kurs           = $this->input->post('kurs');
                // $value_valas    = $this->input->post('value_valas');

                if (empty($no_pelunasan)) {
                    throw new \Exception('No Pelunasan Kosong !', 200);
                }

                // cek status done / cancel
                $cek  = $this->m_pelunasanpiutang->get_data_by_code($no_pelunasan);

                if (empty($cek)) {
                    throw new \Exception('Data Pelunasan tidak ditemukan !', 200);
                } else if ($cek->status == 'done') {
                    $callback = array('status' => 'failed', 'message' => 'Maaf, Data Tidak Bisa Disimpan, Status Sudah Done !', 'icon' => 'fa fa-warning', 'type' => 'danger');
                } else if ($cek->status == 'cancel') {
                    $callback = array('status' => 'failed', 'message' => 'Maaf, Data Tidak Bisa Disimpan, Status Cancel !', 'icon' => 'fa fa-warning', 'type' => 'danger');
                } else {
                    $sub_menu   = $this->uri->segment(2);
                    $username   = addslashes($this->session->userdata('username'));
                    $callback  = array();
                    $tmp_update = array();
                    $this->form_validation->set_rules($validation);
                    if ($this->form_validation->run() == FALSE) {
                        // throw new \Exception(array_values($this->form_validation->error_array())[0], 500);
                        $callback = array('status' => 'failed', 'field' => '', 'message' => array_values($this->form_validation->error_array())[0], 'icon' => 'fa fa-warning', 'type' => 'danger');
                    } else {

                        $value_valas = 0;

                        $cek_inv = $this->m_pelunasanpiutang->cek_faktur_input_by_kode(['no_pelunasan' => $no_pelunasan]);
                        if ($cek_inv->num_rows()) {
                            if ($cek_inv->num_rows() > 1) {
                                throw new \Exception('faktur harus diplih 1 !', 200);
                            } else {
                                $data_inv = $cek_inv->row();
                                if ($data_inv->currency_id == 1) { // IDR
                                    throw new \Exception('Currency Faktur tidak boleh IDR !', 200);
                                }
                                $currency_id = $data_inv->currency_id;
                                $currency_name = $data_inv->currency;
                                $value_valas  = $data_inv->sisa_piutang_valas;
                            }
                        } else {
                            throw new \Exception('Faktur harus diplih dulu !', 200);
                        }

                        // cek metode pelunasan tipe 
                        $gettipe = $this->m_pelunasanpiutang->cek_metode_pelunasan_tipe_by_id($cek->id);
                        if ($gettipe) {
                            if ($gettipe->tipe != 'koreksi') {
                                throw new \Exception('Metode Pelunasan Harus sama dengan yang sudah diiinput !', 200);
                            } else {
                                throw new \Exception('Koreksi Kurs Bulan hanya boleh diinput 1 saja  !', 200);
                            }
                        }

                        $get_row    = $this->m_pelunasanpiutang->get_last_row_order_metode_by_id($cek->id);

                        $ex_plh     = explode("PLH", $no_pelunasan);

                        $id_bukti_ex = isset($ex_plh[1]) ? $get_row . '' . $ex_plh[1] : ''; // antisipasi kalau gak ada "PLH"
                        $no_bukti_ex = $no_pelunasan . '_' . $get_row;

                        $data_items[] = array(
                            'pelunasan_piutang_id'   => $cek->id,
                            'no_pelunasan'          => $no_pelunasan,
                            'id_bukti'              => $id_bukti_ex,
                            'no_bukti'              => $no_bukti_ex,
                            'uraian'                => $uraian,
                            'tanggal_bukti'         => $tanggal,
                            'currency_id'           => $currency_id,
                            'currency'              => $currency_name,
                            'kurs'                  => $kurs,
                            'total_rp'              => $kurs * $value_valas,
                            'total_valas'           => $value_valas,
                            'tipe'                  => 'koreksi',
                            'tipe2'                 => 'koreksi',
                            'row_order'             => $get_row
                        );

                        $insert = $this->m_pelunasanpiutang->insert_data_pelunasan_piutang_metode($data_items);
                        if (!empty($insert)) {
                            throw new \Exception('Data Gagal Disimpan !', 200);
                        }

                        $jenis_log = "edit";
                        $note_log  = "Tambah Data Koreksi Kurs Bulan";
                        $data_history = array(
                            'datelog'   => date("Y-m-d H:i:s"),
                            'kode'      => $no_pelunasan,
                            'jenis_log' => $jenis_log,
                            'note'      => $note_log
                        );

                        // load in library
                        $this->_module->gen_history_ip($sub_menu, $username, $data_history);

                        $result = $this->distribusi_pelunasan_otomatis_koreksi($no_pelunasan);
                        if (!empty($result)) {
                            throw new \Exception('Distribusi Pelunasan Gagal !', 200);
                        }

                        $result2 = $this->hitung_summary($no_pelunasan);
                        if (!empty($result2)) {
                            throw new \Exception('Summary Gagal di update !', 200);
                        }

                        $callback = array('status' => 'success', 'message' => 'Data Berhasil disimpan', 'icon' => 'fa fa-check', 'type' => 'success');
                    }
                }

                $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($callback));
            }
        } catch (Exception $ex) {
            // finish transaction
            $this->_module->finishRollBack();
            $this->_module->rollbackTransaction();
            $this->output->set_status_header($ex->getCode() ?? 500)
                ->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode(array('status' => 'failed', 'message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        } finally {
            // unlock table
            $this->_module->unlock_tabel();
        }
    }


    private $coa_um = array(
        array(
            'posisi' => 'D',
            'kode_coa' => '1163.01'
        ),
    );


    function confirm_pelunasan_piutang()
    {
        try {
            //code...

            $callback = array();
            $sub_menu = $this->uri->segment(2);
            $username = addslashes($this->session->userdata('username'));

            if (empty($this->session->userdata('status'))) {
                throw new \Exception('Waktu Anda Telah Habis', 401); // Unauthorized / session habis
            }

            $this->_module->startTransaction();

            $this->_module->lock_tabel("main_menu_sub READ, log_history WRITE, token_increment WRITE, partner WRITE, user READ, acc_pelunasan_piutang WRITE, acc_pelunasan_piutang_faktur WRITE, acc_pelunasan_piutang_metode WRITE, acc_pelunasan_piutang_summary WRITE, acc_pelunasan_piutang_summary_koreksi WRITE, acc_pelunasan_piutang_summary_koreksi as apphsk WRITE, acc_pelunasan_piutang_summary as apps WRITE,  mst_jurnal WRITE, acc_faktur_penjualan WRITE, acc_bank_masuk_detail WRITE, acc_giro_masuk_detail WRITE, acc_kas_masuk_detail WRITE,  acc_bank_masuk WRITE, acc_giro_masuk WRITE,  acc_kas_masuk WRITE, acc_pelunasan_koreksi_piutang WRITE, acc_pelunasan_piutang_summary_koreksi as appsk WRITE, acc_faktur_penjualan as faktur WRITE, currency_kurs WRITE, acc_jurnal_entries WRITE, acc_jurnal_entries_items WRITE, acc_coa READ, acc_bank_masuk as abm WRITE, acc_bank_masuk_detail as abmd WRITE, currency_kurs as ck1 READ, acc_kas_masuk as akm WRITE, acc_kas_masuk_detail as akmd WRITE, currency_kurs as ck2 READ, acc_giro_masuk as agm WRITE, acc_giro_masuk_detail as agmd WRITE, currency_kurs as ck3 READ");


            $no_pelunasan = $this->input->post('no_pelunasan');
            $tgl = date('Y-m-d H:i:s');

            if (empty($no_pelunasan)) {
                throw new \Exception('No Pelunasan Kosong', 422); // Validation error
            }

            $cek = $this->m_pelunasanpiutang->get_data_by_code($no_pelunasan);

            if (empty($cek)) {
                throw new \Exception('Data pelunasan tidak ditemukan', 404);
            }
            if ($cek->status == 'done') {
                throw new \Exception('Data tidak bisa di Confirm, status sudah Done', 409);
            }
            if ($cek->status == 'cancel') {
                throw new \Exception('Data tidak bisa di Confirm, status Cancel', 409);
            }
            if (empty($cek->partner_id)) {
                throw new \Exception('Data Supplier Kosonsg', 409);
            }

            // cek data faktur
            $result = $this->m_pelunasanpiutang->get_data_faktur_by_code($no_pelunasan);
            if (!$result) {
                throw new \Exception('Data Faktur yang akan dilunasi masih Kosong ', 409);
            }

            $currency_faktur = null;

            // cek data pelunasan
            $result2 = $this->m_pelunasanpiutang->get_data_metode_by_code($no_pelunasan);
            if (!$result2) {
                throw new \Exception('Data Metode Pelunasan masih Kosong ', 409);
            }


            // Ambil mata uang faktur
            foreach ($result as $inv) {
                if ($currency_faktur === null) {
                    $currency_faktur = strtoupper(trim($inv->currency));
                } else {
                    if ($currency_faktur !== strtoupper(trim($inv->currency))) {
                        throw new \Exception('Terdapat lebih dari satu currency pada faktur, tidak diperbolehkan !', 422);
                    }
                }
            }

            // Validasi currency metode pelunasan
            foreach ($result2 as $mt) {
                $currency_metode = strtoupper(trim($mt->currency));

                // Jika faktur valas (bukan IDR) dan metode juga valas tapi beda → tidak boleh
                if ($currency_faktur !== 'IDR' && $currency_metode !== 'IDR' && $currency_faktur !== $currency_metode) {
                    throw new \Exception(
                        "Invoice dengan mata uang {$currency_faktur} tidak boleh dibayar dengan mata uang {$currency_metode} !",
                        422
                    );
                }
            }


            // cek metode pelunasan 
            $cek_mt = $this->m_pelunasanpiutang->cek_metode_pelunasan_group(['no_pelunasan' => $no_pelunasan]);
            if ($cek_mt->num_rows() > 1) { // > 1
                throw new \Exception('Metode Pelunasan hanya bisa dipilih salah satu ', 409);
            }

            $getKodeJurnal = $this->m_pelunasanhutang->get_kode_jurnal_by_nama(['nama' => 'Koreksi Penjualan']);
            $kodeJurnal    = isset($getKodeJurnal) ? $getKodeJurnal->kode : '';

            if ($kodeJurnal === "") {
                throw new \Exception('Jurnal Tidak Ada', 500);
            }

            $tmp_update =  array();
            $tmp_update2 =  array();
            $tmp_update3 =  array();
            $tmp_update4 =  array();
            $head_entries =  array();
            $items_entries =  array();
            // $data_update3 =  array();
            $metode_pl = $cek_mt->row();
            $jurnal   = '';
            $data_summary = 'invalid';
            $create_jurnal = false;

            if ($metode_pl->tipe == 'um') { // kebentuk jurnal

                // cek selisih rupiah
                $get_selisih = $this->m_pelunasanpiutang->get_data_summary_by_code($no_pelunasan);
                foreach ($get_selisih as $gs) {
                    if ($gs->total_piutang > 0 && $gs->total_pelunasan > 0) {
                        if ($gs->selisih < 0) { // < 0 atau > 0
                            // if (!empty($gs->koreksi)) {
                            //     throw new \Exception('Koreksi Untuk Uang Muka tidak harus dipilih !', 422);
                            // }
                            if ($gs->tipe_currency == 'Rp') {
                                $create_jurnal = true;
                            }
                        } else { // selisih == 0 atau selisih > 0
                            throw new \Exception('Nominal tidak Valid !', 200);
                        }
                    }
                }

                // if ($data_summary == 'invalid') {
                //     throw new \Exception('Data Summary / Info  tidak Valid !', 200);
                // }
                $tgl_transaksi = $cek->tanggal_transaksi;
                if ($create_jurnal == true) {
                    if (!$jurnal = $this->token->noUrut("jurnal_{$kodeJurnal}", date('y', strtotime($tgl_transaksi)) . '/' . date('m', strtotime($tgl_transaksi)), true)
                        ->generate("{$kodeJurnal}/", '/%05d')->get()) {
                        throw new \Exception("No jurnal tidak terbuat", 500);
                    }

                    // $items_entries = array();
                    $row_items     = 1;
                    $data_mt = $this->m_pelunasanpiutang->get_data_metode_by_code($no_pelunasan);
                    if ($data_mt) {
                        $head_entries = array(
                            'kode' => $jurnal,
                            'tanggal_dibuat' => $tgl_transaksi,
                            'tanggal_posting' => $tgl,
                            'periode'       => date("y/m", strtotime($tgl_transaksi)),
                            'origin'        => $no_pelunasan,
                            'status'        => 'posted',
                            'tipe'          => $kodeJurnal,
                            'reff_note'     => $cek->partner_nama ?? ''
                        );
                        $total_curr    = 0;
                        $total_nominal = 0;
                        $kurs          = 0;
                        $currency = '';
                        $tmp_bukti     = '';
                        foreach ($data_mt as $mp) { // looping pelunasan metode
                            // hitung total_curr
                            $total_curr += ($mp->currency === 'IDR')
                                ? abs($mp->total_rp)
                                : abs($mp->total_valas);

                            // hitung total nominal dengan kurs
                            $total_nominal += ($mp->currency === 'IDR')
                                ? abs($mp->total_rp) * $mp->kurs
                                : abs($mp->total_valas) * $mp->kurs;

                            $currency = $mp->currency;
                            $kurs     = $mp->kurs;
                            $tmp_bukti .= $mp->no_bukti;

                            //get_coa_um
                            // $dt = $this->m_pelunasanpiutang->get_data_metode_pelunasan_by_id($cek->partner_id, 'um', ['no_bukti' => $mp->no_bukti, 'id' => $mp->id_bukti]);
                            // if (empty($dt)) {
                            //     throw new \Exception('Data Pelunasan Uang Muka tidak ditemukan !', 200);
                            // }
                            // $this->coa_um[] =  array('posisi' => 'C', 'kode_coa' => $dt->kode_coa);
                        }

                        // var_dump($this->coa_um);

                        $data_koreksi_coa =  $this->m_pelunasanpiutang->get_coa_summary_id(['appsk.no_pelunasan' => $no_pelunasan, 'apps.tipe_currency' => 'Rp'])->result();
                        if (empty($data_koreksi_coa)) {
                            throw new \Exception('CoA Uang Muka Tidak di temukan !', 422);
                        }
                        // looping coa 
                        foreach ($data_koreksi_coa as $cok) {
                            if (empty($cok->kode_coa)) {
                                throw new \Exception("CoA " . (($cok->posisi == 'D') ? 'Debit' : 'Credit') . " Kosong !", 422);
                            }
                            $items_entries[] = array(
                                'kode'          => $jurnal,
                                'nama'          => $tmp_bukti,
                                'reff_note'     => 'Uang Muka',
                                'partner'       => $cek->partner_id, // partner_id
                                'kode_coa'      => $cok->kode_coa,
                                'posisi'        => $cok->posisi,
                                'nominal_curr'  => $total_curr,
                                'kurs'          => $kurs,
                                'kode_mua'      => $currency,
                                'nominal'       => $total_nominal,
                                'row_order'     => $row_items
                            );
                            $row_items++;
                        }
                    } else {
                        throw new \Exception('Data Metode Pelunasan masih Kosong ', 409);
                    }
                }
            } else { // kas bank, retur

                // cek selisih rupiah
                $list_coa_koreksi = "";
                $get_selisih = $this->m_pelunasanpiutang->get_data_summary_by_code($no_pelunasan);
                $tgl_transaksi = $cek->tanggal_transaksi;
                foreach ($get_selisih as $gs) {

                    if ($gs->selisih > 0 || $gs->selisih < 0) { // rugi / laba
                        if (empty($gs->koreksi)) {
                            throw new \Exception('Koreksi ' . $gs->tipe_currency . ' belum dipilih', 422);
                        }

                        $cek_koreksi = $this->m_pelunasanpiutang->get_koreksi_by_kode(['kode' => $gs->koreksi]);
                        if (isset($cek_koreksi)) {
                            if ($cek_koreksi->get_coa == 'true') {
                                // cek coa koreksi sudah diisi atau belum
                                $coa_debit  = $this->m_pelunasanpiutang->get_coa_summary_id(['pelunasan_summary_id' => $gs->id, 'posisi' => 'D', 'koreksi' => $gs->koreksi])->row();
                                if (empty($coa_debit)) {
                                    throw new \Exception('CoA Debit belum dipilih', 422);
                                }
                                $get_coa_credit  = $this->m_pelunasanpiutang->get_coa_summary_id(['pelunasan_summary_id' => $gs->id, 'posisi' => 'C',  'koreksi' => $gs->koreksi])->row();
                                if (empty($get_coa_credit)) {
                                    throw new \Exception('CoA Credits belum dipilih', 422);
                                }
                            }
                        }

                        // cek total_piutang  di summary
                        $result_selisih = ((float) $gs->total_pelunasan + (float) $gs->total_koreksi) - (float) $gs->total_piutang;
                        if (round($result_selisih, 2) != round((float) $gs->selisih, 2)) {
                            throw new \Exception('perhitungan Selisih ' . $gs->tipe_currency . ' tidak Valid !', 422);
                        }

                        if (isset($cek_koreksi)) {
                            if ($cek_koreksi->get_coa == 'true') { // kebentuk jurnal entries berdasarkan coa yang dipilih 

                                if ($gs->tipe_currency == 'Rp') {

                                    if (empty($jurnal)) {

                                        if (!$jurnal = $this->token->noUrut("jurnal_{$kodeJurnal}", date('y', strtotime($tgl_transaksi)) . '/' . date('m', strtotime($tgl_transaksi)), true)
                                            ->generate("{$kodeJurnal}/", '/%05d')->get()) {
                                            throw new \Exception("No jurnal tidak terbuat", 500);
                                        }
                                        // $items_entries = array();
                                        $row_items     = 1;
                                        $head_entries = array(
                                            'kode' => $jurnal,
                                            'tanggal_dibuat' => $tgl_transaksi,
                                            'tanggal_posting' => $tgl,
                                            'periode'       => date("y/m", strtotime($tgl_transaksi)),
                                            'origin'        => $no_pelunasan,
                                            'status'        => 'posted',
                                            'tipe'          => $kodeJurnal,
                                            'reff_note'     => $cek->partner_nama ?? ''
                                        );
                                    }

                                    $data_koreksi_coa =  $this->m_pelunasanpiutang->get_coa_summary_id(['appsk.no_pelunasan' => $no_pelunasan, 'appsk.pelunasan_summary_id' => $gs->id])->result();
                                    if (empty($data_koreksi_coa)) {
                                        throw new \Exception('CoA Koreksi ' . $gs->tipe_currency . ' Tidak di temukan !', 422);
                                    }
                                    // looping coa 
                                    foreach ($data_koreksi_coa as $cok) {
                                        if (empty($cok->kode_coa)) {
                                            throw new \Exception("CoA " . (($cok->posisi == 'D') ? 'Debit' : 'Credit') . " Kosong !", 422);
                                        }
                                        $items_entries[] = array(
                                            'kode'          => $jurnal,
                                            'nama'          => 'Koreksi ' . $cek_koreksi->nama_koreksi ?? '',
                                            'reff_note'     => ($cok->koreksi <> 'selisih_kurs_akhir_bulan') ? 'Pelunasan Piutang' : '',
                                            'partner'       => $cek->partner_id, // partner_id
                                            'kode_coa'      => $cok->kode_coa,
                                            'posisi'        => $cok->posisi,
                                            'nominal_curr'  => abs($gs->selisih),
                                            'kurs'          => $gs->kurs,
                                            'kode_mua'      => $gs->currency,
                                            'nominal'       => abs($gs->selisih * $gs->kurs),
                                            'row_order'     => $row_items
                                        );
                                        $row_items++;
                                    }
                                }
                            }
                        }
                    }
                }
            }

            // update langsung ke faktur piutang_rp = piutang_rp - pelunasan_rp 
            // get_list_faktur
            $get_inv = $this->m_pelunasanpiutang->get_data_faktur_by_code2(['no_pelunasan' => $no_pelunasan]);
            foreach ($get_inv as $gi) {
                $getInv = $this->m_pelunasanpiutang->get_faktur_by_kode(['no_faktur_internal' => $gi->no_faktur, 'lunas' => 0, 'faktur.id' => $gi->faktur_id])->row();
                if ($getInv) {

                    if ((float) $getInv->sisa_piutang_rp != (float) $gi->sisa_piutang_rp) {
                        throw new \Exception('Data faktur ' . $gi->no_faktur . ' Tidak Valid <br> Sisa Piutang Rp di Pelunasan dan di faktur Berbeda  !', 200);
                    }
                    if ((float) $getInv->sisa_piutang_valas != (float) $gi->sisa_piutang_valas) {
                        throw new \Exception('Data faktur ' . $gi->no_faktur . ' Tidak Valid <br> Sisa Piutang Valas di Pelunasan dan di faktur Berbeda  !', 200);
                    }

                    $sisa_piutang_valas = (float) $getInv->sisa_piutang_valas -  (float) $gi->pelunasan_valas;
                    $sisa_piutang_rp = (float) $getInv->sisa_piutang_rp -  (float) $gi->pelunasan_rp;
                    $data_update = array(
                        'id'  => $gi->faktur_id,
                        'piutang_rp'   => $sisa_piutang_rp,
                        'piutang_valas'   => $sisa_piutang_valas
                    );
                    array_push($tmp_update, $data_update);
                    $data_summary = 'valid';
                } else {
                    throw new \Exception('Data faktur ' . $gi->no_faktur . ' tidak Valid / Sudah Lunas!', 200);
                }
            }


            if ($data_summary == 'invalid') {
                throw new \Exception('Data Summary / Info  tidak Valid !', 200);
            }


            // var_dump($data_update);
            if (!empty($tmp_update)) {
                $update = $this->m_pelunasanpiutang->update_faktur_by_kode($tmp_update);
                if ($update !== "") {
                    throw new \Exception('Gagal Update Faktur Nominal, Tidak ada data yang di perbaharui !', 200);
                }

                $data_update = [];
                $tmp_update  = [];

                $get_inv = $this->m_pelunasanpiutang->get_data_faktur_by_code($no_pelunasan);
                foreach ($get_inv as $gi) {

                    $getInv = $this->m_pelunasanpiutang->get_faktur_by_kode(['no_faktur_internal' => $gi->no_faktur, 'lunas' => 0, 'faktur.id' => $gi->faktur_id])->row();
                    if (isset($getInv)) {
                        if ((float)$getInv->sisa_piutang_rp == 0 and (float) $getInv->sisa_piutang_valas == 0) {
                            $data_update = array(
                                'id'  => $gi->faktur_id,
                                'lunas'   => 1
                            );
                            array_push($tmp_update, $data_update);
                        } else {
                            if ($gi->status_bayar == 'lunas') {
                                $data_update = array(
                                    'id'  => $gi->faktur_id,
                                    'lunas'   => 1
                                );
                                array_push($tmp_update, $data_update);
                            }
                            if ($gi->status_bayar == 'belum_bayar' || empty($gi->status_bayar)) {
                                throw new \Exception('Data Faktur ' . $gi->no_faktur . ' belum ada pelunasan !', 200);
                            }
                        }
                    } else {
                        throw new \Exception('Data Faktur ' . $gi->no_faktur . ' tidak Valid / Sudah Lunas!', 200);
                    }
                }

                if ($tmp_update) {
                    $update_inv = $this->m_pelunasanpiutang->update_faktur_by_kode($tmp_update);
                    if ($update_inv !== "") {
                        throw new \Exception('Gagal Update Faktur Status Lunas, Tidak ada data yang di perbaharui !' . json_encode($tmp_update), 200);
                    }
                }

                $data_update = [];
                $tmp_update = [];

                $list_mt  = $this->m_pelunasanpiutang->get_data_metode_by_code($no_pelunasan);
                $total    = 0;
                foreach ($list_mt as $mt) {
                    ($mt->currency === 'IDR') ? $total = $mt->total_rp : $total = $mt->total_valas;
                    if ($mt->tipe2 == 'bank') { // bank masuk detail
                        $cek_mt = $this->m_pelunasanpiutang->cek_data_metode_valid_by_code('bank', ['acc_bank_masuk.status' => 'confirm', 'acc_bank_masuk.no_bm' => $mt->no_bukti, 'acc_bank_masuk_detail.id' => $mt->id_bukti, 'acc_bank_masuk_detail.lunas' => 0]);
                        if (isset($cek_mt)) {
                            if ((float) $cek_mt->nominal == (float) $total) {
                                $data_update = array(
                                    'id'  => $mt->id_bukti,
                                    'lunas'   => 1
                                );
                                array_push($tmp_update, $data_update);
                                // $tmp_update =  array();
                            } else {
                                throw new \Exception('Nominal Metode Pelunasan Bank Tidak Valid <br> No. ' . $mt->no_bukti, 200);
                            }
                        } else {
                            throw new \Exception('Metode Pelunasan Bank Tidak Valid <br> No. ' . $mt->no_bukti, 200);
                        }
                    } else if ($mt->tipe2 == 'giro') { // giro masuk detail
                        $cek_mt = $this->m_pelunasanpiutang->cek_data_metode_valid_by_code('giro', ['acc_giro_masuk.status' => 'confirm', 'acc_giro_masuk.no_gm' => $mt->no_bukti, 'acc_giro_masuk_detail.id' => $mt->id_bukti, 'acc_giro_masuk_detail.lunas' => 0]);
                        if (isset($cek_mt)) {
                            if ((float) $cek_mt->nominal == (float) $total) {
                                $data_update2 = array(
                                    'id'  => $mt->id_bukti,
                                    'lunas'   => 1
                                );
                                array_push($tmp_update2, $data_update2);
                                // $tmp_update2 =  array();
                            } else {
                                throw new \Exception('Nominal Metode Pelunasan Giro  Tidak Valid <br> No. ' . $mt->no_bukti, 200);
                            }
                        } else {
                            throw new \Exception('Metode Pelunasan Giro Tidak Valid <br> No. ' . $mt->no_bukti, 200);
                        }
                    } else if ($mt->tipe2 == 'kas') { // kas masuk detail'
                        $cek_mt = $this->m_pelunasanpiutang->cek_data_metode_valid_by_code('kas', ['acc_kas_masuk.status' => 'confirm', 'acc_kas_masuk.no_km' => $mt->no_bukti, 'acc_kas_masuk_detail.id' => $mt->id_bukti, 'acc_kas_masuk_detail.lunas' => 0]);
                        if (isset($cek_mt)) {
                            if ((float) $cek_mt->nominal == (float) $total) {
                                $data_update3 = array(
                                    'id'  => $mt->id_bukti,
                                    'lunas'   => 1
                                );
                                $tmp_update3 =  array();
                                array_push($tmp_update3, $data_update3);
                            } else {
                                throw new \Exception('Nominal Metode Pelunasan Kas  Tidak Valid <br> No. ' . $mt->no_bukti, 200);
                            }
                        } else {
                            throw new \Exception('Metode Pelunasan Kas Tidak Valid <br> No. ' . $mt->no_bukti, 200);
                        }
                    } else if ($mt->tipe2 == 'retur') { //invoice retur
                        $cek_mt = $this->m_pelunasanpiutang->cek_data_metode_valid_by_code('retur', ['status' => 'done', 'no_retur' => $mt->no_bukti, 'id' => $mt->id_bukti, 'lunas' => 0]);
                        if (isset($cek_mt)) {
                            if ((float) $cek_mt->total == (float) $total) {
                                $data_update4 = array(
                                    'id'  => $mt->id_bukti,
                                    'lunas'   => 1
                                );
                                $tmp_update4 =  array();
                                array_push($tmp_update4, $data_update4);
                            } else {
                                throw new \Exception('Nominal Metode Pelunasan Retur  Tidak Valid <br> No. ' . $mt->no_bukti, 200);
                            }
                        } else {
                            throw new \Exception('Metode Pelunasan Retur Tidak Valid <br> No. ' . $mt->no_bukti, 200);
                        }
                    } else if ($mt->tipe2 == 'koreksi') { //kurs bulan
                        $cek_mt_koreksi = $this->m_pelunasanpiutang->cek_metode_input_by_kode(['no_pelunasan' => $no_pelunasan, 'no_bukti' => $mt->no_bukti, 'id_bukti' => $mt->id_bukti]);
                        if (!isset($cek_mt_koreksi)) {
                            throw new \Exception('Koreksi Kurs Bulan tidak ditemukan !', 200);
                        }
                    } else {
                        throw new \Exception('Confirm Gagal, Metode Pelunasan Selain dari Giro/Bank/Kas/Retur  !', 200);
                    }
                }
            } else {
                throw new \Exception('Tidak ada Data Faktur  di Pelunasan ini !', 200);
            }


            if ($head_entries) {
                $result = $this->m_pelunasanhutang->insert_jurnal_entries($head_entries);
                if ($result !== "") {
                    throw new \Exception('Gagal Update Insert Jurnal Entries Items !', 200);
                }
            }
            if ($items_entries) {
                $result = $this->m_pelunasanhutang->insert_jurnal_entries_items($items_entries);
                if ($result !== "") {
                    throw new \Exception('Gagal Update Insert Jurnal Entries Items !', 200);
                }
            }

            if ($tmp_update) { // update bank
                $result = $this->m_pelunasanhutang->update_kas_bank_kode($tmp_update, 'acc_bank_masuk_detail');
                if ($result !== "") {
                    throw new \Exception('Gagal Update Bank masuk, Tidak ada data yang diperbaharui  !', 200);
                }
            }
            if ($tmp_update2) { // update giro
                $result = $this->m_pelunasanhutang->update_kas_bank_kode($tmp_update2, 'acc_giro_masuk_detail');
                if ($result !== "") {
                    throw new \Exception('Gagal Update Giro masuk, Tidak ada data yang diperbaharui  !', 200);
                }
            }
            if ($tmp_update3) { // update kas
                $result = $this->m_pelunasanhutang->update_kas_bank_kode($tmp_update3, 'acc_kas_masuk_detail');
                if ($result !== "") {
                    throw new \Exception('Gagal Update Kas masuk, Tidak ada data yang diperbaharui  !', 200);
                }
            }

            if ($tmp_update4) { // update invoice retur
                $result = $this->m_pelunasanhutang->update_kas_bank_kode($tmp_update4, 'acc_retur_penjualan');
                if ($result !== "") {
                    throw new \Exception('Gagal Update Retur, Tidak ada data yang diperbaharui  !', 200);
                }
            }

            if ($head_entries && $items_entries) {
                $log = "Header -> " . logArrayToString("; ", $head_entries) . "<br>";
                $log .= "\nDETAIL -> " . logArrayToString("; ", $items_entries);
                $this->_module->gen_history_new("jurnal_entries", $jurnal, "create", $log, $username);
            }

            $data_update = [
                'no_pelunasan'      => $no_pelunasan,
                'status' => 'done',
                'no_jurnal' => $jurnal
            ];


            $update = $this->m_pelunasanpiutang->update_data_pelunasan_piutang([$data_update]);
            if (!$update['status']) {
                throw new \Exception('Gagal memperbarui data: ' . $update['message'], 500);
            }


            $jenis_log = "edit";
            $note_log  = "Pelunasan No. " . $no_pelunasan . " di Confirm";
            $data_history = array(
                'datelog'   => date("Y-m-d H:i:s"),
                'kode'      => $no_pelunasan,
                'jenis_log' => $jenis_log,
                'note'      => $note_log
            );
            $this->_module->gen_history_ip($sub_menu, $username, $data_history);


            $callback = array('status' => 'success', 'message' => 'Data Pelunasan Piutang Berhasil di Confirm !', 'icon' => 'fa fa-success', 'type' => 'success');

            if (!$this->_module->finishTransaction()) {
                throw new \Exception('Gagal mengkonfirmasi Data Pelunasan', 500);
            }
            $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($callback));
        } catch (Exception $ex) {
            // finish transaction
            $this->_module->finishRollBack();
            $this->_module->rollbackTransaction();
            $this->output->set_status_header($ex->getCode() ?? 500)
                ->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode(array('status' => 'failed', 'message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        } finally {
            // unlock table
            $this->_module->unlock_tabel();
        }
    }



    function cancel_pelunasan_piutang()
    {
        try {
            //code...
            $validation = [
                [
                    'field' => 'no_pelunasan',
                    'label' => 'No Peluasnaan',
                    'rules' => ['required'],
                    'errors' => [
                        'required' => '{field} Kosong !',
                    ]
                ],
            ];

            // start transaction
            $this->_module->startTransaction();

            //lock tabel
            $this->_module->lock_tabel('acc_pelunasan_piutang WRITE, acc_pelunasan_piutang_faktur WRITE, acc_pelunasan_piutang_metode WRITE, acc_pelunasan_piutang_summary WRITE, main_menu_sub READ,log_history WRITE, user READ, acc_pelunasan_piutang_summary_koreksi WRITE, acc_jurnal_entries WRITE, acc_bank_masuk WRITE, acc_bank_masuk_detail WRITE, acc_kas_masuk WRITE, acc_kas_masuk_detail WRITE, acc_giro_masuk WRITE, acc_giro_masuk_detail WRITE, acc_faktur_penjualan WRITE, currency_kurs READ, acc_retur_penjualan WRITE, acc_faktur_penjualan as faktur WRITE');

            $callback  = array();
            if (empty($this->session->userdata('status'))) { //cek apakah session masih adag
                // session habis
                throw new \Exception('Waktu Anda Telah Habis', 401);
            } else {

                $sub_menu   = $this->uri->segment(2);
                $username   = addslashes($this->session->userdata('username'));

                $this->form_validation->set_rules($validation);
                if ($this->form_validation->run() == FALSE) {
                    // throw new \Exception(array_values($this->form_validation->error_array())[0], 500);
                    $callback = array('status' => 'failed', 'field' => '', 'message' => array_values($this->form_validation->error_array())[0], 'icon' => 'fa fa-warning', 'type' => 'danger');
                } else {

                    $no_pelunasan  = $this->input->post('no_pelunasan');

                    // cek status done / cancel
                    $cek  = $this->m_pelunasanpiutang->get_data_by_code($no_pelunasan);

                    if (empty($cek)) {
                        throw new \Exception('Data Pelunasan tidak ditemukan !', 200);
                    } else if (!in_array($cek->status, ['done', 'draft', 'cancel'])) {
                        $callback = array('status' => 'failed', 'message' => 'Maaf, Data Tidak Bisa dibatalkan, Status tidak valid !', 'icon' => 'fa fa-warning', 'type' => 'danger');
                    } else if ($cek->status == 'cancel') {
                        $callback = array('status' => 'failed', 'message' => 'Maaf, Data Tidak Bisa dibatalkan, Status Cancel !', 'icon' => 'fa fa-warning', 'type' => 'danger');
                    } else {

                        if ($cek->status == 'done' || $cek->status == 'draft') {

                            $tmp_update = array();
                            $data_update = array(
                                'no_pelunasan'          => $no_pelunasan,
                                'status'                => 'cancel',
                            );
                            array_push($tmp_update, $data_update);
                            $update = $this->m_pelunasanpiutang->update_data_pelunasan_piutang($tmp_update);

                            if (!$update['status']) {
                                throw new \Exception('Gagal Membatalkan data: ' . $update['message'], 500);
                            }
                        }

                        if ($cek->status == 'done') {


                            //update jurnal entries by 
                            if (!empty($cek->no_jurnal)) {
                                $this->m_pelunasanpiutang->update_by_kode('acc_jurnal_entries', ['status' => 'cancel'], ['kode' => $cek->no_jurnal]);

                                $log = "Merubah Status ke Batal dari Pelunasan Piutang";
                                $this->_module->gen_history_new("jurnal_entries", $cek->no_jurnal, "cancel", $log, $username);
                            }

                            $list_inv = $this->m_pelunasanpiutang->get_data_faktur_by_code($no_pelunasan);
                            foreach ($list_inv as $li) {
                                $pelunasan_rp = $li->pelunasan_rp;
                                $pelunasan_valas = $li->pelunasan_valas;

                                $dt = $this->m_pelunasanpiutang->get_data_faktur_by_id(['faktur.no_faktur_internal' =>  $li->no_faktur, 'faktur.id' => $li->faktur_id]);
                                if (isset($dt)) {
                                    $piutang_inv = (float) $dt->sisa_piutang_rp + (float) $pelunasan_rp;
                                    $piutang_inv_valas = (float) $dt->sisa_piutang_valas + (float) $pelunasan_valas;

                                    //update to faktur
                                    $update_inv = $this->m_pelunasanpiutang->update_by_kode('acc_faktur_penjualan', ['lunas' => 0, 'piutang_rp' => $piutang_inv, 'piutang_valas' => $piutang_inv_valas], ['id' => $li->faktur_id]);
                                    if ($update_inv !== "") {
                                        throw new \Exception('Gagal Update Faktur ' . $li->no_faktur . ', Tidak ada data yang diperbaharui  !', 200);
                                    }
                                } else {
                                    throw new \Exception('Data Faktur Tidak ditemukan <br> No. ' . $li->no_faktur, 200);
                                }
                            }

                            $list_mt  = $this->m_pelunasanpiutang->get_data_metode_by_code($no_pelunasan);

                            foreach ($list_mt as $mt) {

                                foreach ($this->metodePelunasan2 as $metodeItems2) {
                                    if ($metodeItems2['id'] == $mt->tipe2 && $metodeItems2['check'] == 'true') {
                                        $cek_mt = $this->m_pelunasanpiutang->cek_data_metode_valid_by_code($mt->tipe2, [$metodeItems2['status'] => $metodeItems2['status_value'], $metodeItems2['no_bukti'] => $mt->no_bukti, $metodeItems2['id_detail'] =>  $mt->id_bukti]);
                                        if (isset($cek_mt)) {
                                            $update_metode = $this->m_pelunasanpiutang->update_by_kode($metodeItems2['table_detail'], ['lunas' => 0], [$metodeItems2['no_bukti'] => $mt->no_bukti, $metodeItems2['id_detail'] => $mt->id_bukti]);
                                            if ($update_metode !== "") {
                                                throw new \Exception('Gagal Update Metode Pelunasan  ' . $mt->no_bukti . ', Tidak ada data yang diperbaharui  !', 200);
                                            }
                                        } else {
                                            throw new \Exception('Metode Pelunasan ' . $metodeItems2['text'] . '  Tidak Valid / Tidak ditemukan <br> No. ' . $mt->no_bukti, 200);
                                        }
                                        break;
                                    }
                                }
                            }
                        }

                        $jenis_log = "cancel";
                        $note_log  = "Pelunasan No. " . $no_pelunasan . " di Batalkan.";
                        $data_history = array(
                            'datelog'   => date("Y-m-d H:i:s"),
                            'kode'      => $no_pelunasan,
                            'jenis_log' => $jenis_log,
                            'note'      => $note_log
                        );

                        // load in library
                        $this->_module->gen_history_ip($sub_menu, $username, $data_history);


                        $result2 = $this->hitung_summary($no_pelunasan);
                        if (!empty($result2)) {
                            throw new \Exception('Summary Gagal di update !', 200);
                        }

                        $callback = array('status' => 'success', 'message' => 'Pelunasan Piutang Berhasil dibatalkan', 'icon' => 'fa fa-check', 'type' => 'success');
                    }
                }

                if (!$this->_module->finishTransaction()) {
                    throw new \Exception('Gagal menghapus Data', 500);
                }
                $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($callback));
            }
        } catch (Exception $ex) {
            // finish transaction
            $this->_module->finishRollBack();
            $this->_module->rollbackTransaction();
            $this->output->set_status_header($ex->getCode() ?? 500)
                ->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode(array('status' => 'failed', 'message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        } finally {
            // unlock table
            $this->_module->unlock_tabel();
        }
    }
}
