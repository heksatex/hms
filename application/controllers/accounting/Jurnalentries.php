<?php

defined('BASEPATH') or exit('No Direct Script Acces Allowed');
/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of Jurnal
 *
 * @author RONI
 */
require FCPATH . 'vendor/autoload.php';
require APPPATH . '/third_party/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use Mpdf\Mpdf;

class Jurnalentries extends MY_Controller
{

    //put your code here
    public function __construct()
    {
        parent::__construct();
        $this->is_loggedin();
        $this->load->model("m_global");
        $this->load->model('_module');
        $this->config->load('additional');
        $this->load->library("token");
    }

    public function index()
    {
        $data['id_dept'] = 'JNE';
        $model = new $this->m_global;
        $data["jurnal"] = $model->setTables("mst_jurnal")->setSelects(["kode","nama"])->setOrder(["nama"=>"asc"])->getData();
        $this->load->view('accounting/v_jurnal_entries', $data);
    }

    public function get_periode()
    {
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

    public function add()
    {
        $data['id_dept'] = 'JNE';
        $model = new $this->m_global;

        $data["jurnal"] = $model->setTables("mst_jurnal")->setOrder(["kode"])->setSelects(["nama,kode"])->getData();
        $this->load->view('accounting/v_jurnal_entries_add', $data);
    }

    public function data()
    {
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
            if ($this->input->post("kode") !== "") {
                $list->setWheres(["acc_jurnal_entries.kode LIKE" => "%" . $this->input->post('kode') . "%"]);
            }
            if ($this->input->post("jurnal") !== "") {
                $list->setWheres(["acc_jurnal_entries.tipe" => $this->input->post('jurnal')]);
            }
            if ($this->input->post("status") !== "") {
                $list->setWheres(["acc_jurnal_entries.status" => $this->input->post("status")]);
            }
            foreach ($list->getData() as $key => $field) {
                $kode_encrypt = encrypt_url($field->kode);
                $no++;
                $data[] = array(
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
            echo json_encode(array(
                "draw" => $_POST['draw'],
                "recordsTotal" => $list->getDataCountAll(),
                "recordsFiltered" => $list->getDataCountFiltered(),
                "data" => $data,
            ));
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

    public function simpan()
    {
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
                //                "periode" => $periode,
                "origin" => "",
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
            $url = site_url("accounting/jurnalentries/edit/" . encrypt_url($kode));
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

    public function edit($id)
    {
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
            $details = $detail->setTables("acc_jurnal_entries_items jei")
                ->setJoins("partner", "partner.id = jei.partner", "left")
                ->setJoins("acc_coa", "acc_coa.kode_coa = jei.kode_coa", "left")
                ->setJoins("acc_jurnal_entries je", "je.kode = jei.kode")
                //->setOrder(["jei.posisi" => "desc", "jei.kode_coa" => "asc"])
                ->setSelects(["jei.*", "partner.nama as supplier,partner.id as supplier_id", "acc_coa.nama as account", "je.tipe"])
                ->setWheres(["je.kode" => $kode_decrypt]);
            if ($data["jurnal"]->origin !== "") {
                $details->setOrder(["jei.posisi" => "desc", "jei.kode_coa" => "asc"]);
            }
            $data["detail"] = $details->getData();
            $data["coas"] = $detail->setTables("acc_coa")->setSelects(["kode_coa", "nama"])
                ->setWheres(["level" => 5])->setOrder(["kode_coa" => "asc"])->getData();
            $this->load->view('accounting/v_jurnal_entries_edit', $data);
        } catch (Exception $ex) {
            return show_404();
        }
    }

    public function update($id)
    {
        try {
            $sub_menu = $this->uri->segment(2);
            $username = addslashes($this->session->userdata('username'));

            $kode_decrypt = decrypt_url($id);
            $refnote = $this->input->post("reff_note");
            $origin = $this->input->post("origin");
            $periode = $this->input->post("periode");
            $model = new $this->m_global;
            $headUpdate = [
                "reff_note" => $refnote,
                "origin" => $origin ?? "",
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
                        'rules' => ['trim', 'required', 'regex_match[/^-?\d*(,\d{3})*\.?\d*$/]'],
                        'errors' => [
                            'required' => '{field} Pada Item harus diisi',
                            "regex_match" => "{field} harus berupa number / desimal"
                        ]
                    ],
                    [
                        'field' => 'kredit[]',
                        'label' => 'Credit',
                        'rules' => ['trim', 'required', 'regex_match[/^-?\d*(,\d{3})*\.?\d*$/]'],
                        'errors' => [
                            'required' => '{field} Pada Item harus diisi',
                            "regex_match" => "{field} harus berupa number / desimal"
                        ]
                    ],
                    //                    [
                    //                        'field' => 'kode_coa[]',
                    //                        'label' => 'Account',
                    //                        'rules' => ['trim', 'required'],
                    //                        'errors' => [
                    //                            'required' => '{field} Pada Item harus diisi'
                    //                        ]
                    //                    ]
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
                    $db = str_replace(",", "", $debit[$key]);
                    $kr = str_replace(",", "", $kredit[$key]);
                    $posisi = "D";
                    if ($db > 0) {
                        $nominalCurr = $db;
                    } else {
                        $nominalCurr = $kr;
                        $posisi = "C";
                    }
                    $itemUpdate[] = [
                        "kode_coa" => $value,
                        "kode" => $kode_decrypt,
                        "nama" => $nama[$key] ?? "",
                        "reff_note" => $noteItem[$key] ?? "",
                        "partner" => $partner[$key] ?? 0,
                        "kurs" => $kurs[$key],
                        "kode_mua" => $curr[$key],
                        "nominal" => $nominalCurr * $kurs[$key],
                        "row_order" => $no,
                        "posisi" => $posisi,
                        "nominal_curr" => $nominalCurr
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

    public function update_status()
    {
        try {
            $sub_menu = $this->uri->segment(2);
            $username = addslashes($this->session->userdata('username'));

            $id = $this->input->post("ids");
            $status = $this->input->post("status");

            $kode_decrypt = decrypt_url($id);
            $model = new $this->m_global;
            $update = ["status" => $status];
            //            $kredit = str_replace(",", "", $this->input->post("kredit"));
            //            $debit = str_replace(",", "", $this->input->post("debit"));
            if ($status === "posted") {
                $getDataNominal = $model->setTables("acc_jurnal_entries_items")->setSelects(["sum(nominal) as total,posisi"])
                    ->setWheres(["kode" => $kode_decrypt])->setGroups(["posisi"])->getData();
                if (round(($getDataNominal[0]->total ?? 0), 2) !== round(($getDataNominal[1]->total ?? 0), 2)) {
                    throw new \Exception('Total Kredit dan Debit belum balance', 500);
                }
                $update = array_merge($update, ["tanggal_posting" => date("Y-m-d H:i:s")]);
            }
            $model->setTables("acc_jurnal_entries")->setWheres(["kode" => $kode_decrypt])->update($update);

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

    public function getcoa()
    {
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

    public function print()
    {
        try {
            $id = $this->input->post("ids");
            $kode = decrypt_url($id);
            $model = new $this->m_global;
            $users = $this->session->userdata('nama');
            $data["jurnal"] = $model->setTables("acc_jurnal_entries je")->setJoins("mst_jurnal mj", "mj.kode = je.tipe")
                ->setWheres(["je.kode" => $kode])->setSelects(["je.*", "mj.nama as jurnal_nama", "date(tanggal_dibuat) as tanggal_dibuat"])->getDetail();
            if (!$data["jurnal"]) {
                throw new \exception("Data Jurnal Entries {$kode} tidak ditemukan", 500);
            }
            $details = $model->setTables("acc_jurnal_entries_items jei")
                ->setJoins("partner", "partner.id = jei.partner", "left")
                ->setJoins("acc_coa", "acc_coa.kode_coa = jei.kode_coa", "left")
                ->setJoins("acc_jurnal_entries je", "je.kode = jei.kode")
                ->setSelects(["jei.*", "partner.nama as supplier,partner.id as supplier_id", "acc_coa.nama as account", "je.tipe"])
                ->setWheres(["je.kode" => $kode]);
            if ($data["jurnal"]->origin !== "") {
                $details->setOrder(["jei.posisi" => "desc", "jei.kode_coa" => "asc"]);
            }

            $data["detail"] = $details->getData();
            $data["user"] = $users;

            $html = $this->load->view("accounting/v_jurnal_entries_print", $data, true);
            $url = "dist/storages/print/jurnalentries";
            if (!is_dir(FCPATH . $url)) {
                mkdir(FCPATH . $url, 0775, TRUE);
            }
            $mpdf = new Mpdf(['tempDir' => FCPATH . 'tmp']);
            $mpdf->WriteHTML($html);
            $filename = str_replace("/", "-", $data["jurnal"]->kode);
            $pathFile = "{$url}/{$filename}.pdf";
            $mpdf->Output(FCPATH . $pathFile, "F");
            $this->output->set_status_header(200)
                ->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode(array("url" => base_url($pathFile))));
        } catch (Exception $ex) {
            $this->output->set_status_header(500)
                ->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }

    public function download_template($id)
    {
        try {
            $kode_decrypt = decrypt_url($id);
            $nm = str_replace("/", "_", $kode_decrypt);
            $spreadsheet = new Spreadsheet();
            $spreadsheet->removeSheetByIndex(0);
            $FWorksheet = new Worksheet($spreadsheet, $nm);
            $ExWorksheet = new Worksheet($spreadsheet, "Example");
            $spreadsheet->addSheet($FWorksheet);
            $spreadsheet->addSheet($ExWorksheet);
            $sheetEx = $spreadsheet->setActiveSheetIndex(1);
            $sheetF = $spreadsheet->setActiveSheetIndex(0);
            $rowF = 1;
            $sheetF->setCellValue("A{$rowF}", "No");
            $sheetF->setCellValue("b{$rowF}", "Nama");
            $sheetF->setCellValue("c{$rowF}", "Reff Note");
            $sheetF->setCellValue("d{$rowF}", "kode coa");
            $sheetF->setCellValue("e{$rowF}", "Debet");
            $sheetF->setCellValue("f{$rowF}", "Kredit");
            $sheetF->setCellValue("g{$rowF}", "Kurs");
            $sheetF->setCellValue("h{$rowF}", "Mata Uang");

            $rowEx = 1;
            $sheetEx->setCellValue("A{$rowEx}", "No");
            $sheetEx->setCellValue("b{$rowEx}", "Nama");
            $sheetEx->setCellValue("c{$rowEx}", "Reff Note");
            $sheetEx->setCellValue("d{$rowEx}", "kode coa");
            $sheetEx->setCellValue("e{$rowEx}", "Debet");
            $sheetEx->setCellValue("f{$rowEx}", "Kredit");
            $sheetEx->setCellValue("g{$rowEx}", "Kurs");
            $sheetEx->setCellValue("h{$rowEx}", "Mata Uang");

            $rowEx += 1;
            $sheetEx->setCellValue("A{$rowEx}", "1");
            $sheetEx->setCellValue("b{$rowEx}", "Example 1");
            $sheetEx->setCellValue("c{$rowEx}", "Example 1");
            $sheetEx->setCellValue("d{$rowEx}", "00001.1");
            $sheetEx->setCellValue("e{$rowEx}", "1000000");
            $sheetEx->setCellValue("f{$rowEx}", "0");
            $sheetEx->setCellValue("g{$rowEx}", "1");
            $sheetEx->setCellValue("h{$rowEx}", "IDR");

            $rowEx += 1;
            $sheetEx->setCellValue("A{$rowEx}", "2");
            $sheetEx->setCellValue("b{$rowEx}", "Example 2");
            $sheetEx->setCellValue("c{$rowEx}", "Example 2");
            $sheetEx->setCellValue("d{$rowEx}", "00001.1");
            $sheetEx->setCellValue("e{$rowEx}", "56.25");
            $sheetEx->setCellValue("f{$rowEx}", "0");
            $sheetEx->setCellValue("g{$rowEx}", "15400");
            $sheetEx->setCellValue("h{$rowEx}", "USD");

            $filename = "template_{$nm}";
            $url = "dist/storages/report/jurnalentries";
            if (!is_dir(FCPATH . $url)) {
                mkdir(FCPATH . $url, 0775, TRUE);
            }
            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save(FCPATH . $url . '/' . $filename . '.xlsx');
            $this->output->set_status_header(200)
                ->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode(array(
                    'message' => 'Berhasil Export',
                    'icon' => 'fa fa-check',
                    'text_name' => "{$filename}",
                    'type' => 'success',
                    "url" => base_url($url . '/' . $filename . '.xlsx')
                )));
        } catch (Exception $ex) {
            $this->output->set_status_header($ex->getCode() ?? 500)
                ->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger', "data" => "")));
        }
    }

    public function upload($id)
    {
        try {
            $sub_menu = $this->uri->segment(2);
            $username = addslashes($this->session->userdata('username'));
            if (0 < $_FILES['file']['error']) {
                throw new \exception($_FILES['file']['error'], 500);
            }
            if (!file_exists($_FILES['file']['tmp_name'][0])) {
                throw new exception("File yang diimport gagal", 500);
            }
            $location = FCPATH . "dist/storages/report/jurnalentries/{$_FILES['file']['name']}";
            move_uploaded_file($_FILES['file']['tmp_name'], $location);
            $reader = new Xlsx();
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($location);

            $kode_decrypt = decrypt_url($id);
            $nm = str_replace("/", "_", $kode_decrypt);
            $activeWorksheet = $spreadsheet->getSheetByName($nm);

            if ($activeWorksheet === null) {
                throw new exception("File tidak sesuai template", 500);
            }
            $highestRow = $activeWorksheet->getHighestRow();
            if ($highestRow < 2) {
                throw new exception("Data File masih kosong", 500);
            }
            $data = $activeWorksheet->toArray();
            $jurnalItem = [];
            unset($data[0]);
            foreach ($data as $key => $value) {
                $nominal = $value[5];
                $posisi = "C";
                if ($value[4] > 0) {
                    $nominal = $value[4];
                    $posisi = "D";
                }
                $kurs = $value[6];
                $jurnalItem[] = [
                    "kode_coa" => $value[3],
                    "kode" => $kode_decrypt,
                    "nama" => $value[1],
                    "reff_note" => $value[2],
                    "partner" => 0,
                    "kurs" => $kurs,
                    "kode_mua" => $value[7],
                    "nominal" => $nominal * $kurs,
                    "nominal_curr" => $nominal,
                    "row_order" => $key++,
                    "posisi" => $posisi
                ];
            }
            $model = new $this->m_global;
            $model->setTables("acc_jurnal_entries_items")->setWheres(["kode" => $kode_decrypt])->delete();
            $model->saveBatch($jurnalItem);
            $log = "data -> " . logArrayToString("; ", $jurnalItem);
            $this->_module->gen_history_new($sub_menu, $kode_decrypt, "edit", $log, $username);
            $this->output->set_status_header(200)
                ->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success')));
        } catch (Exception $ex) {
            $this->output->set_status_header(500)
                ->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger', "data" => "")));
        }
    }
}
