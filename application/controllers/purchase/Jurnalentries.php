<?php

defined('BASEPATH') OR EXIT('No Direct Script Acces Allowed');
/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of Jurnal
 *
 * @author RONI
 */
class Jurnalentries extends MY_Controller {

    //put your code here
    public function __construct() {
        parent:: __construct();
        $this->is_loggedin();
        $this->load->model("m_global");
        $this->load->model('_module');
        $this->config->load('additional');
        $this->load->library("token");
    }

    public function index() {
        $data['id_dept'] = 'JNE';
        $this->load->view('purchase/v_jurnal_entries', $data);
    }

    public function get_periode() {
        try {
            $model = new $this->m_global;
            $model->setTables("acc_periode")->setSelects(["periode"]);
            if ($this->input->get('search') !== "") {
                $model->setWheres(["periode LIKE" => "%{$this->input->get('search')}%"]);
            }
            $_POST['length'] = 50;
            $_POST['start'] = 0;
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array("data" => $model->getData())));
        } catch (Exception $ex) {
            $this->output->set_status_header(500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array("message" => $ex->getMessage())));
        }
    }

    public function add() {
        $data['id_dept'] = 'JNE';
        $model = new $this->m_global;

        $data["jurnal"] = $model->setTables("mst_jurnal")->setOrder(["kode"])->setSelects(["nama,kode"])->getData();
        $this->load->view('purchase/v_jurnal_entries_add', $data);
    }

    public function data() {
        try {
            $data = array();
            $list = new $this->m_global;
            $no = $_POST['start'];

            $list->setTables("acc_jurnal_entries")->setOrder(["tanggal_dibuat" => "desc"])
                    ->setJoins("mst_jurnal", "mst_jurnal.kode = acc_jurnal_entries.tipe", "left")
                    ->setJoins("mst_status", "mst_status.kode = acc_jurnal_entries.status", "left")
                    ->setSearch(["acc_jurnal_entries.kode", "periode", "origin", "reff_note", "mst_jurnal.nama"])
                    ->setOrders([null, "acc_jurnal_entries.kode", "mst_jurnal.nama", "tanggal_dibuat", "periode", "origin", "reff_note", "status"])
                    ->setSelects(["acc_jurnal_entries.*,date(tanggal_dibuat) as tanggal_dibuat", "nama_status", "mst_jurnal.nama as nama_jurnal"]);
            foreach ($list->getData() as $key => $field) {
                $kode_encrypt = encrypt_url($field->kode);
                $no++;
                $data [] = array(
                    $no,
                    '<a href="' . base_url('purchase/jurnalentries/edit/' . $kode_encrypt) . '">' . $field->kode . '</a>',
                    $field->nama_jurnal,
                    $field->tanggal_dibuat,
//                    $field->tanggal_posting,
                    $field->periode,
                    $field->origin,
                    $field->reff_note,
                    $field->nama_status ?? $field->status,
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

    public function simpan() {
        try {
            $sub_menu = $this->uri->segment(2);
            $username = addslashes($this->session->userdata('username'));
            $refnote = $this->input->post("reff_note");
            $origin = $this->input->post("origin");
            $periode = $this->input->post("periode");
            $tanggal = $this->input->post("tanggal");
            $jurnal = $this->input->post("jurnal");
            $this->form_validation->set_rules([
                [
                    'field' => 'jurnal',
                    'label' => 'Jurnal',
                    'rules' => ['trim', 'required'],
                    'errors' => [
                        'required' => '{field} Harus dipilih dahulu'
                    ]
                ],
                [
                    'field' => 'tanggal',
                    'label' => 'Tanggal',
                    'rules' => ['trim', 'required'],
                    'errors' => [
                        'required' => '{field} Harus ditentukan dahulu'
                    ]
                ]
            ]);
            if ($this->form_validation->run() == FALSE) {
                throw new \Exception(array_values($this->form_validation->error_array())[0], 500);
            }

            $this->_module->startTransaction();
            if (!$kode = $this->token->noUrut("jurnal_acc_{$jurnal}", date('ym', strtotime($tanggal)), true)->generate(strtoupper($jurnal), '/%03d')
                            ->prefixAdd("/" . date("y", strtotime($tanggal)) . "/" . getRomawi(date('m', strtotime($tanggal)) . "/"))->get()) {
                throw new \Exception("No Jurnal tidak terbuat", 500);
            }
            $input = [
                "tanggal_dibuat" => $tanggal,
                "periode" => $periode,
                "origin" => $origin,
                "reff_note" => $refnote,
                "tipe" => $jurnal,
                "status" => "unposted",
                "kode" => $kode
            ];
            $model = new $this->m_global;
            $model->setTables("acc_jurnal_entries")->save($input);

            if (!$this->_module->finishTransaction()) {
                throw new \Exception('Gagal Menyimpan Data', 500);
            }
            $this->_module->gen_history_new($sub_menu, $kode, 'create', "DATA -> " . logArrayToString("; ", $input), $username);
            $url = site_url("purchase/jurnalentries/edit/" . encrypt_url($kode));
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success', 'url' => $url)));
        } catch (Exception $ex) {
            $this->_module->rollbackTransaction();
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }

    public function edit($id) {
        try {
            $kode_decrypt = decrypt_url($id);
            $data['id_dept'] = 'JNE';
            $data["id"] = $id;
            $head = new $this->m_global;
            $detail = clone $head;
            $data["curr"] = $head->setTables("currency_kurs")->setSelects(["id", "currency"])->getData();
            $data["jurnal"] = $head->setTables("acc_jurnal_entries")
                            ->setJoins("mst_jurnal", "mst_jurnal.kode = acc_jurnal_entries.tipe", "left")
                            ->setWheres(["acc_jurnal_entries.kode" => $kode_decrypt])
                            ->setSelects(["mst_jurnal.nama as nama_jurnal", "acc_jurnal_entries.*,date(tanggal_dibuat) as tanggal_dibuat"])->getDetail();
            if ($data["jurnal"] === null) {
                throw new \Exception();
            }
            $data["detail"] = $detail->setTables("acc_jurnal_entries_items jei")->setOrder(["jei.posisi" => "desc", "jei.kode_coa" => "asc"])
                            ->setJoins("partner", "partner.id = jei.partner", "left")
                            ->setJoins("acc_coa", "acc_coa.kode_coa = jei.kode_coa", "left")
                            ->setJoins("acc_jurnal_entries je", "je.kode = jei.kode")
                            ->setSelects(["jei.*", "partner.nama as supplier,partner.id as supplier_id", "acc_coa.nama as account", "je.tipe"])
                            ->setWheres(["je.kode" => $kode_decrypt])->getData();
            $data["coas"] = $detail->setTables("acc_coa")->setSelects(["kode_coa", "nama"])
                            ->setWheres(["level" => 5])->setOrder(["kode_coa" => "asc"])->getData();
            $this->load->view('purchase/v_jurnal_entries_edit', $data);
        } catch (Exception $ex) {
            return show_404();
        }
    }

    public function update($id) {
        try {
            $sub_menu = $this->uri->segment(2);
            $username = addslashes($this->session->userdata('username'));

            $kode_decrypt = decrypt_url($id);
            $refnote = $this->input->post("reff_note");
            $origin = $this->input->post("origin");
            $periode = $this->input->post("periode");
            $model = new $this->m_global;
            $headUpdate = ["reff_note" => $refnote,
                "origin" => $origin,
                "periode" => $periode
            ];
            $this->_module->startTransaction();
            $model->setTables("acc_jurnal_entries")->setWheres(["kode" => $kode_decrypt])->update($headUpdate);
            $model->setTables("acc_jurnal_entries_items")->setWheres(["kode" => $kode_decrypt])->delete();
            $account = $this->input->post("kode_coa");
            $itemUpdate = [];
            $no = 0;
            if (count($account) > 0) {
                $this->form_validation->set_rules([
                    [
                        'field' => 'debet[]',
                        'label' => 'Debit',
                        'rules' => ['trim', 'required', 'regex_match[/^-?\d*\.?\d*$/]'],
                        'errors' => [
                            'required' => '{field} Pada Item harus diisi',
                            "regex_match" => "{field} harus berupa number / desimal"
                        ]
                    ],
                    [
                        'field' => 'kredit[]',
                        'label' => 'Credit',
                        'rules' => ['trim', 'required', 'regex_match[/^-?\d*\.?\d*$/]'],
                        'errors' => [
                            'required' => '{field} Pada Item harus diisi',
                            "regex_match" => "{field} harus berupa number / desimal"
                        ]
                    ],
                    [
                        'field' => 'kode_coa[]',
                        'label' => 'Account',
                        'rules' => ['trim', 'required'],
                        'errors' => [
                            'required' => '{field} Pada Item harus diisi'
                        ]
                    ]
                ]);
                if ($this->form_validation->run() == FALSE) {
                    throw new \Exception(array_values($this->form_validation->error_array())[0], 500);
                }
                $partner = $this->input->post("partner");
                $nama = $this->input->post("nama");
                $noteItem = $this->input->post("reffnote_item");
                $kurs = $this->input->post("kurs");
                $curr = $this->input->post("curr");
                $debit = $this->input->post("debet");
                $kredit = $this->input->post("kredit");

                foreach ($account as $key => $value) {
                    $no++;
                    $itemUpdate[] = [
                        "kode_coa" => $value,
                        "kode" => $kode_decrypt,
                        "nama" => $nama[$key],
                        "reff_note" => $noteItem[$key],
                        "partner" => $partner[$key],
                        "kurs" => $kurs[$key],
                        "kode_mua" => $curr[$key],
                        "nominal" => ($debit[$key] > 0) ? $debit[$key] : $kredit[$key],
                        "row_order" => $no,
                        "posisi" => ($debit[$key] > 0) ? "D" : "C"
                    ];
                }
                $model->setTables("acc_jurnal_entries_items")->saveBatch($itemUpdate);
            }
            if (!$this->_module->finishTransaction()) {
                throw new \Exception('Gagal Menyimpan Data', 500);
            }
            $log = " DATA -> " . logArrayToString("; ", $headUpdate);
            $log .= "\nDetail -> " . logArrayToString("; ", $itemUpdate);
            $this->_module->gen_history_new($sub_menu, $kode_decrypt, "edit", $log, $username);
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success')));
        } catch (Exception $ex) {
            $this->_module->rollbackTransaction();
            log_message("error", json_encode($ex));
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }

    public function update_status() {
        try {
            $sub_menu = $this->uri->segment(2);
            $username = addslashes($this->session->userdata('username'));

            $id = $this->input->post("ids");
            $status = $this->input->post("status");

            $kode_decrypt = decrypt_url($id);
            $jurnal = new $this->m_global;
            $update = ["status" => $status];
            if ($status === "posted") {
                if($this->input->post("kredit") !== $this->input->post("debit")) {
                    throw new \Exception('Total Kredit dan Debit belum balance', 500);
                }
                $update = array_merge($update, ["tanggal_posting" => date("Y-m-d H:i:s")]);
            }
            $jurnal->setTables("acc_jurnal_entries")->setWheres(["kode" => $kode_decrypt])->update($update);

            $this->_module->gen_history($sub_menu, $kode_decrypt, 'update', "update status ke {$status}", $username);
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success')));
        } catch (Exception $ex) {
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger', "data" => [])));
        }
    }

    public function getcoa() {
        try {
            $search = $this->input->post("search");
            $coa = new $this->m_global;
            $_POST['search'] = array(
                'value' => $search
            );
            $_POST['length'] = 50;
            $_POST['start'] = 0;

            $data = $coa->setTables("acc_coa")->setSearch(["kode_coa", "nama"])->setWheres(["level" => 5])->setOrder(['kode_coa' => "asc"])->setSelects(['kode_coa', 'nama'])->getData();
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'success', 'icon' => 'fa fa-warning', 'type' => 'danger', 'data' => $data)));
        } catch (Exception $ex) {
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger', "data" => [])));
        }
    }
}
