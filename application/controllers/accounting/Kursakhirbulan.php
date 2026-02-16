<?php

defined('BASEPATH') or exit('No Direct Script Acces Allowed');
/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of Kursakhirbulan
 *
 * @author RONI
 */
class Kursakhirbulan extends MY_Controller {

    //put your code here
    public function __construct() {
        parent::__construct();
        $this->is_loggedin();
        $this->load->model("_module");
        $this->load->model("m_global");
        $this->load->library("token");
        $this->load->driver('cache', array('adapter' => 'file'));
        $this->load->library('periodesaldo');
    }

    public function index() {
        $data['id_dept'] = 'ACCKAB';
        $this->load->view('accounting/v_kursakhirbulan', $data);
    }

    public function add() {
        $data['id_dept'] = 'ACCKAB';
        $this->load->view('accounting/v_kursakhirbulan_add', $data);
    }

    public function edit($id) {
        try {
            $data["user"] = (object) $this->session->userdata('nama');
            $data["id"] = $id;
            $data['id_dept'] = 'ACCKAB';
            $kode = decrypt_url($id);
            $model = new $this->m_global;
            $data["datas"] = $model->setTables("acc_kurs_akhir_bulan")->setWheres(["no" => $kode])->getDetail();
            if (!$data["datas"]) {
                throw new \Exception();
            }
            $data["detail"] = $model->setTables("acc_kurs_akhir_bulan_detail")->setWheres(["kab_id" => $data["datas"]->id])->getData();
            $data["jurnal"] = $model->setTables("acc_jurnal_entries_items")->setWheres(["kode" => $data["datas"]->no_jurnal])
                            ->setOrder(["posisi" => "desc", "kode_coa" => "asc"])->getData();
            $this->load->view('accounting/v_kursakhirbulan_edit', $data);
        } catch (Exception $ex) {
            show_404();
        }
    }

    protected function _getSaldo() {
        try {
            $curr = $this->input->post("currency");
            $bulan = $this->input->post("bulan");
            $start = "{$bulan}-01";
            $akhir = date("Y-m-t", strtotime($start));
            $model = new $this->m_global;
            $model->setTables("acc_jurnal_entries_items")->setJoins("acc_jurnal_entries", 'acc_jurnal_entries_items.kode = acc_jurnal_entries.kode')
                    ->setWheres(["date(acc_jurnal_entries.tanggal_dibuat) >=" => date("Y-m-d", strtotime($start)), "date(acc_jurnal_entries.tanggal_dibuat) <=" => date("Y-m-d", strtotime($akhir)),
                        'status' => 'posted'])
                    ->setSelects(["posisi, acc_jurnal_entries_items.kode_coa,  IFNULL(SUM(CASE WHEN posisi = 'D' THEN nominal ELSE 0 END),0) AS total_debit,   IFNULL(SUM(CASE WHEN posisi = 'C' THEN nominal ELSE 0 END),0) AS total_credit"])
                    ->setSelects(["IFNULL(SUM(CASE WHEN posisi = 'D' THEN nominal_curr ELSE 0 END),0) AS total_debit_valas,IFNULL(SUM(CASE WHEN posisi = 'C' THEN nominal_curr ELSE 0 END),0) AS total_credit_valas"])
                    ->setGroups(["acc_jurnal_entries_items.kode_coa"]);
            $entriesRp = $model->getQuery();
            $entries = $model->setWheres(["kode_mua" => $curr])->getQuery();
            $starts = $this->periodesaldo->get_start_periode();
            $tgl_dari = date("Y-m-d 00:00:00", strtotime($starts));
            $tgl_sampai = date("Y-m-d 23:59:59", strtotime("-1 day", strtotime($start)));
            //saldodebet
            $model->setTables("acc_jurnal_entries")->setJoins('acc_jurnal_entries_items', 'acc_jurnal_entries.kode = acc_jurnal_entries_items.kode')->setGroups(["acc_jurnal_entries_items.kode_coa"])
                    ->setSelects(["acc_jurnal_entries_items.kode_coa, SUM(nominal) as total_debit"])
                    ->setSelects(["SUM(nominal_curr) as total_debit_valas"])
                    ->setWheres(['tanggal_dibuat >= ' => $tgl_dari, 'tanggal_dibuat <= ' => $tgl_sampai, 'status' => 'posted', 'posisi' => "D",]);
            $saldoDebetRp = $model->getQuery();
            $saldoDebet = $model->setWheres(["kode_mua" => $curr])->getQuery();

            //Kredit
            $model->setTables("acc_jurnal_entries")->setJoins('acc_jurnal_entries_items', 'acc_jurnal_entries_items.kode = acc_jurnal_entries.kode')->setGroups(["acc_jurnal_entries_items.kode_coa"])
                    ->setSelects(["acc_jurnal_entries_items.kode_coa, SUM(nominal) as total_credit"])
                    ->setSelects(["SUM(nominal_curr) as total_credit_valas"])
                    ->setWheres(['tanggal_dibuat >= ' => $tgl_dari, 'tanggal_dibuat <= ' => $tgl_sampai, 'status' => 'posted', 'posisi' => "C"]);
            $saldoKreditRp = $model->getQuery();
            $saldoKredit = $model->setWheres(["kode_mua" => $curr])->getQuery();

            $model->setTables("acc_coa coa")
                    ->setJoins("({$saldoDebet}) as debit_sbl", "debit_sbl.kode_coa = coa.kode_coa", "left")
                    ->setJoins("({$saldoKredit}) as credit_sbl", "credit_sbl.kode_coa = coa.kode_coa", "left")
                    ->setJoins("({$entries}) as jr ", "jr.kode_coa = coa.kode_coa", "left")
                    ->setJoins("({$saldoDebetRp}) as debit_sbl_rp", "debit_sbl_rp.kode_coa = coa.kode_coa", "left")
                    ->setJoins("({$saldoKreditRp}) as credit_sbl_rp", "credit_sbl.kode_coa = coa.kode_coa", "left")
                    ->setJoins("({$entriesRp}) as jr_rp ", "jr_rp.kode_coa = coa.kode_coa", "left")
                    ->setOrder(["coa.kode_coa" => "asc"])
                    ->setWheres(["coa.curr" => $curr])
                    ->setSelects(["coa.kode_coa, coa.nama as nama_coa,coa.saldo_normal,coa.saldo_awal,COALESCE(debit_sbl_rp.total_debit, 0) as total_debit_sbl,COALESCE(debit_sbl.total_debit_valas, 0) as total_debit_valas_sbl",
                        "COALESCE(credit_sbl_rp.total_credit, 0) as total_credit_sbl,COALESCE(credit_sbl.total_credit_valas, 0) as total_credit_valas_sbl",
                        "COALESCE(jr_rp.total_debit, 0) as total_debit,COALESCE(jr.total_debit_valas, 0) as total_debit_valas",
                        "COALESCE(jr_rp.total_credit, 0) as total_credit,COALESCE(jr.total_credit_valas, 0) as total_credit_valas",
                        "CASE 
                                WHEN coa.saldo_normal = 'D' THEN 
                                    (coa.saldo_awal + COALESCE(debit_sbl_rp.total_debit, 0) - COALESCE(credit_sbl_rp.total_credit, 0))
                                WHEN coa.saldo_normal = 'C' THEN 
                                    (coa.saldo_awal + COALESCE(credit_sbl_rp.total_credit, 0) - COALESCE(debit_sbl_rp.total_debit, 0))
                                ELSE coa.saldo_awal
                            END AS saldo_awal_final"])
                    ->setSelects(["CASE 
                                WHEN coa.saldo_normal = 'D' THEN 
                                    (coa.saldo_valas + COALESCE(debit_sbl.total_debit_valas, 0) - COALESCE(credit_sbl.total_credit_valas, 0))
                                WHEN coa.saldo_normal = 'C' THEN 
                                    (coa.saldo_valas + COALESCE(credit_sbl.total_credit_valas, 0) - COALESCE(debit_sbl.total_debit_valas, 0))
                                ELSE coa.saldo_valas
                            END AS saldo_valas_final", "coa.curr as coa_curr"]);

            return $model->getData();
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    public function list() {
        try {
            $data = array();
            $list = new $this->m_global;
            $list->setTables("acc_kurs_akhir_bulan")->setOrder(["created_at" => "desc"])
                    ->setJoins("mst_status ms", "ms.kode = status", "left")
                    ->setOrders([null, "no", "bulan", "curr", "kurs", "no_jurnal"])
                    ->setSearch(["no", "bulan", "kurs", "curr", "no_jurnal"])
                    ->setSelects(["acc_kurs_akhir_bulan.*", "nama_status"]);
            $no = $_POST['start'];
            foreach ($list->getData() as $field) {
                $no++;
                $kode_encrypt = encrypt_url($field->no);
                $link = base_url("accounting/jurnalentries/edit/" . encrypt_url($field->no_jurnal));
                $data[] = [
                    $no,
                    "<a href='" . base_url("accounting/kursakhirbulan/edit/{$kode_encrypt}") . "'>{$field->no}</a>",
                    $field->bulan,
                    number_format($field->kurs, 2),
                    $field->curr,
                    "<a href='{$link}' target='_blank'>{$field->no_jurnal}</a>",
                    $field->nama_status
                ];
            }
            echo json_encode(array("draw" => $_POST['draw'],
                "recordsTotal" => $list->getDataCountAll("acc_kas_keluar.id"),
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

    public function get_currency() {
        try {
            $model = new $this->m_global;
            $model->setTables("currency")->setSelects(["nama", "symbol"])->setOrder(["nama" => "asc"]);
            if ($this->input->get('search') !== "") {
                $model->setWheres(["nama LIKE" => "%{$this->input->get('search')}%"]);
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

    public function generate() {
        try {
            $val = [
                [
                    'field' => 'currency',
                    'label' => 'Currency',
                    'rules' => ['trim', 'required'],
                    'errors' => [
                        'required' => '{field} Pada Item harus diisi'
                    ]
                ],
                [
                    'field' => 'bulan',
                    'label' => 'Bulan',
                    'rules' => ['trim', 'required'],
                    'errors' => [
                        'required' => '{field} Pada Item harus diisi'
                    ]
                ],
                [
                    'field' => 'kurs',
                    'label' => 'Kurs',
                    'rules' => ['required', 'regex_match[/^-?\d*(,\d{3})*\.?\d*$/]'],
                    'errors' => [
                        'required' => '{field} Pada Item harus diisi',
                        "regex_match" => "{field} harus berupa number / desimal"
                    ]
                ]
            ];
            $this->form_validation->set_rules($val);
            if ($this->form_validation->run() == FALSE) {
                throw new \Exception(array_values($this->form_validation->error_array())[0], 500);
            }
//            log_message("error",json_encode($this->_getSaldo()));
            $bulans = explode("-", $this->input->post("bulan"));
            $curr = $this->input->post("currency");
            $symcurr = $this->input->post("symcurr");
            $data["kurs"] = str_replace(",", "", $this->input->post("kurs"));
            $model = new $this->m_global;
            $data["coa"] = $this->_getSaldo();
            $data["coa_sk"] = $model->setTables("setting")->setWheres(["setting_name" => "selisih_kurs"])->getDetail();
            $data["curr"] = $curr;
            $data["kas"] = $this->_updatekasView();
            $data["deposit"] = $this->_updateDepositView();
            $html = $this->load->view("accounting/v_kursakhirbulan_gen_kas", $data, true);
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success', "data" => $html, "count" => count($data['coa']))));
        } catch (Exception $ex) {
            $this->output->set_status_header(($ex->getCode()) ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }

    public function update($id) {
        try {
            $sub_menu = $this->uri->segment(2);
            $username = $this->session->userdata('username');
            $val = [
                [
                    'field' => 'currency',
                    'label' => 'Currency',
                    'rules' => ['trim', 'required'],
                    'errors' => [
                        'required' => '{field} Pada Item harus diisi'
                    ]
                ],
                [
                    'field' => 'bulan',
                    'label' => 'Bulan',
                    'rules' => ['trim', 'required'],
                    'errors' => [
                        'required' => '{field} Pada Item harus diisi'
                    ]
                ],
                [
                    'field' => 'kurs',
                    'label' => 'Kurs',
                    'rules' => ['required', 'regex_match[/^-?\d*(,\d{3})*\.?\d*$/]'],
                    'errors' => [
                        'required' => '{field} Pada Item harus diisi',
                        "regex_match" => "{field} harus berupa number / desimal"
                    ]
                ]
            ];
            $this->form_validation->set_rules($val);
            if ($this->form_validation->run() == FALSE) {
                throw new \Exception(array_values($this->form_validation->error_array())[0], 500);
            }
            $kode = decrypt_url($id);
            $curr = $this->input->post("currency");
            $bulan = $this->input->post("bulan");
            $kurs = $this->input->post("kurs");
            $model = new $this->m_global;
            $check = $model->setTables("acc_kurs_akhir_bulan")->setWheres(["no" => $kode])->getDetail();
            if (!$check) {
                throw new \Exception("No Kurs Akhir Bulan {$kode} tidak ditemukan", 500);
            }
            if ($check->status === "confirm") {
                throw new \Exception("harus dalam status draft", 500);
            }
            $updates = ["bulan" => $bulan, "kurs" => str_replace(",", "", $kurs), "curr" => $curr];
            $model->update($updates);
            if (!$this->_module->finishTransaction()) {
                throw new \Exception('Gagal Menyimpan Data', 500);
            }
            $this->_module->gen_history_new($sub_menu, $kode, 'edit', "DATA -> " . logArrayToString("; ", $updates), $username);
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success')));
        } catch (Exception $ex) {
            $this->_module->rollbackTransaction();
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        } finally {
            $this->_module->unlock_tabel();
        }
    }

    public function confirm($id) {
        try {
            $sub_menu = $this->uri->segment(2);
            $username = $this->session->userdata('username');
            $this->_module->startTransaction();
            $model = new $this->m_global;
            $kode = decrypt_url($id);
            $check = $model->setTables("acc_kurs_akhir_bulan")->setWheres(["no" => $kode])->getDetail();
            if (!$check) {
                throw new \Exception("No Kurs Akhir Bulan {$kode} tidak ditemukan", 500);
            }
            if ($check->status === "confirm") {
                throw new \Exception("harus dalam status draft", 500);
            }
            $_POST["currency"] = $check->curr;
            $_POST["bulan"] = $check->bulan;
            $_POST["kurs"] = $check->kurs;
            $tanggal = date("Y-m-t H:i:s", strtotime("{$check->bulan}-01"));
            $lock = "token_increment WRITE,main_menu_sub READ, log_history WRITE,acc_kurs_akhir_bulan WRITE,picklist_detail WRITE,acc_jurnal_entries_items WRITE,"
                    . "acc_coa READ, acc_kas_masuk_detail WRITE, acc_kas_keluar_detail WRITE, acc_bank_masuk_detail WRITE, acc_bank_keluar_detail WRITE,acc_giro_masuk_detail WRITE,acc_giro_keluar_detail WRITE,"
                    . "acc_bank_masuk bm READ, acc_bank_masuk_detail bmd READ, acc_bank_keluar bk READ,acc_bank_keluar_detail bkd READ, acc_coa coa READ, setting READ,currency_kurs READ,"
                    . "acc_pelunasan_piutang app READ,acc_jurnal_entries WRITE,acc_kurs_akhir_bulan_detail WRITE,acc_jurnal_entries READ,"
                    . "acc_pelunasan_piutang_summary apps WRITE,acc_pelunasan_piutang_summary_koreksi appsk READ";
            $this->_module->lock_tabel($lock);
            $coa = $this->_getSaldo();
            $coask = $model->setTables("setting")->setWheres(["setting_name" => "selisih_kurs"])->getDetail();

            if (!$noJurnal = $this->token->noUrut('jurnal_selisih_kurs', date('ym', strtotime($tanggal)), true)->generate('SK', '/%03d')->prefixAdd("/" . date("y", strtotime($tanggal)) . "/" . date('m', strtotime($tanggal)))->get()) {
                throw new \Exception("No Jurnal tidak terbuat", 500);
            }
            $dataJurnal = [
                "kode" => $noJurnal,
                "origin" => $kode,
                "tanggal_dibuat" => $tanggal,
                "tanggal_posting" => $tanggal,
                "periode" => date("Y-m"),
                "tipe" => "SK",
                "status" => "posted",
                "reff_note" => ""
            ];

            $model->setTables("acc_kurs_akhir_bulan")->setWheres(["no" => $kode])->update(["no_jurnal" => $noJurnal, "status" => "confirm"]);
            $model->setTables("acc_jurnal_entries")->save($dataJurnal);
            $noOrder = 1;
            $entriesDetail = [];
            $kursAkhirDetail = [];
            foreach ($coa as $key => $value) {
                $saldoAwalValas = floatval($value->saldo_valas_final);
                $totalDebitValas = floatval($value->total_debit_valas);
                $totalCreditValas = floatval($value->total_credit_valas);

                $saldoAwal = floatval($value->saldo_awal_final);
                $totalDebit = floatval($value->total_debit);
                $totalCredit = floatval($value->total_credit);
                if ($value->saldo_normal == 'D') {
                    $saldoAkhirValas = $saldoAwalValas + $totalDebitValas - $totalCreditValas;
                    $saldoAkhir = $saldoAwal + $totalDebit - $totalCredit;
                } else {
                    $saldoAkhirValas = $saldoAwalValas + $totalCreditValas - $totalDebitValas;
                    $saldoAkhir = $saldoAwal + $totalCredit - $totalDebit;
                }

                $selisih = ($saldoAwalValas * $check->kurs) - $saldoAkhir;
                $nominal = abs($selisih);

                if ($saldoAwalValas <= 0 || $nominal === (double) 0) {
                    continue;
                }
                $kursAkhirDetail[] = [
                    "selisih" => $selisih,
                    "saldo" => $saldoAkhirValas,
                    "saldo_rp" => $saldoAkhir,
                    "kurs" => $check->kurs,
                    "curr" => $check->curr,
                    "kab_id" => $check->id,
                    "no_kab" => $kode,
                    "kode_coa" => $value->kode_coa,
                    "_segment" => "jurnal"
                ];
                //"Kurs Akhir Bulan (Saldo Valas : " . number_format($saldoAkhirValas, 2) . " {$check->curr}, Saldo Rp ".number_format($saldoAkhir, 2)." Kurs : " . number_format($check->kurs, 2) . ")"
                $nama = "Kurs Akhir Bulan (Saldo Valas : " . number_format($saldoAkhirValas, 2) . " {$check->curr}, Saldo Rp " . number_format($saldoAkhir, 2) . " Kurs : " . number_format($check->kurs, 2) . ")";
                $entriesDetail[] = [
                    "nama" => $nama,
                    "kode" => $noJurnal,
                    "reff_note" => "",
                    "partner" => "",
                    "kode_coa" => $value->kode_coa,
                    "kurs" => 1,
                    "kode_mua" => "IDR",
                    "posisi" => ($selisih > 0) ? "D" : "C",
                    "nominal_curr" => $nominal,
                    "nominal" => $nominal,
                    "row_order" => $noOrder
                ];
                $noOrder += 1;
                $entriesDetail[] = [
                    "nama" => $nama,
                    "kode" => $noJurnal,
                    "reff_note" => "",
                    "partner" => "",
                    "kode_coa" => $coask->value,
                    "kurs" => 1,
                    "kode_mua" => "IDR",
                    "posisi" => ($selisih < 0) ? "D" : "C",
                    "nominal_curr" => $nominal,
                    "nominal" => $nominal,
                    "row_order" => $noOrder
                ];
                $noOrder += 1;
            }
            $model->setTables("acc_jurnal_entries_items")->saveBatch($entriesDetail);
//           
            $updatekasView = $this->_updateKasView();
            foreach ($updatekasView as $key => $value) {
                $oldKurs = ($value->kurs_akhir > 0) ? $value->kurs_akhir : $value->kurs;
                $selisih = ($value->nominal * $check->kurs) - ($value->nominal * $oldKurs);
                $kursAkhirDetail [] = [
                    "kurs" => $check->kurs,
                    "curr" => $check->curr,
                    "kab_id" => $check->id,
                    "no_kab" => $kode,
                    "saldo" => $value->nominal,
                    "saldo_rp" => $value->nominal * $check->kurs,
                    "selisih" => $selisih,
                    "kode_coa" => $value->kode_coa,
                    "_segment" => "kasbank"
                ];
            }
            $this->_updatekas();
            $updatePelView = $this->_updateDepositView();
            foreach ($updatePelView as $key => $value) {
                $oldKurs = ($value->kurs_akhir > 0) ? $value->kurs_akhir : $value->kurs;
                $saldo = $value->total_piutang - $value->total_pelunasan;
                $selisih = ($saldo * $check->kurs) - ($saldo * $oldKurs);
                $kursAkhirDetail [] = [
                    "kurs" => $check->kurs,
                    "curr" => $check->curr,
                    "kab_id" => $check->id,
                    "no_kab" => $kode,
                    "saldo" => $saldo,
                    "saldo_rp" => $saldo * $check->kurs,
                    "selisih" => $selisih,
                    "kode_coa" => $value->kode_coa,
                    "_segment" => "pelunasan"
                ];
            }
            $this->_updateDeposit();
            $model->setTables("acc_kurs_akhir_bulan_detail")->saveBatch($kursAkhirDetail);
            if (!$this->_module->finishTransaction()) {
                throw new \Exception('Gagal Menyimpan Data', 500);
            }
            $this->_module->gen_history_new($sub_menu, $kode, 'edit', "Confirm Data \n no Jurnal {$noJurnal}", $username);
            $this->_module->gen_history_new('jurnalentries', $noJurnal, 'create', "DATA -> " . logArrayToString("; ", $dataJurnal) . "\n Detail -> " . logArrayToString("; ", $entriesDetail), $username);
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success', "url" => base_url("accounting/kursakhirbulan"))));
        } catch (Exception $ex) {
            $this->_module->rollbackTransaction();
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        } finally {
            $this->_module->unlock_tabel();
        }
    }

    public function simpan() {
        try {
            $val = [
                [
                    'field' => 'currency',
                    'label' => 'Currency',
                    'rules' => ['trim', 'required'],
                    'errors' => [
                        'required' => '{field} Pada Item harus diisi'
                    ]
                ],
                [
                    'field' => 'bulan',
                    'label' => 'Bulan',
                    'rules' => ['trim', 'required'],
                    'errors' => [
                        'required' => '{field} Pada Item harus diisi'
                    ]
                ],
                [
                    'field' => 'kurs',
                    'label' => 'Kurs',
                    'rules' => ['required', 'regex_match[/^-?\d*(,\d{3})*\.?\d*$/]'],
                    'errors' => [
                        'required' => '{field} Pada Item harus diisi',
                        "regex_match" => "{field} harus berupa number / desimal"
                    ]
                ]
            ];
            $this->form_validation->set_rules($val);
            if ($this->form_validation->run() == FALSE) {
                throw new \Exception(array_values($this->form_validation->error_array())[0], 500);
            }
            $sub_menu = $this->uri->segment(2);
            $username = $this->session->userdata('username');
            $this->_module->startTransaction();
            $bulan = $this->input->post("bulan");
            $start = "{$bulan}-01";
            $tanggal = date("Y-m-t H:i:s", strtotime($start));

            if (!$no = $this->token->noUrut('kurs_bulan', date('ym', strtotime($tanggal)), true)->generate('KAB', '/%03d')->prefixAdd("/" . date("y", strtotime($tanggal)) . "/" . date('m', strtotime($tanggal)))->get()) {
                throw new \Exception("No Kurs tidak terbuat", 500);
            }
            $curr = $this->input->post("currency");
            $kurs = str_replace(",", "", $this->input->post("kurs"));
            $model = new $this->m_global;

            $head = [
                "no" => $no,
                "bulan" => $this->input->post("bulan"),
                "kurs" => $kurs,
                "curr" => $curr,
                "created_at" => date("Y-m-d H:i:s")
            ];
            $model->setTables("acc_kurs_akhir_bulan")->save($head);

            if (!$this->_module->finishTransaction()) {
                throw new \Exception('Gagal Menyimpan Data', 500);
            }
            $this->_module->gen_history_new($sub_menu, $no, 'create', "DATA -> " . logArrayToString("; ", $head), $username);
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success', "url" => base_url("accounting/kursakhirbulan"))));
        } catch (Exception $ex) {
            $this->_module->rollbackTransaction();
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        } finally {
            $this->_module->unlock_tabel();
        }
    }

    protected function _updateDepositView() {
        try {
            $bulans = explode("-", $this->input->post("bulan"));
            $kurs = str_replace(",", "", $this->input->post("kurs"));
            $model = new $this->m_global;
            return $model->setTables("acc_pelunasan_piutang app")->setJoins("acc_pelunasan_piutang_summary apps", "app.id = apps.pelunasan_piutang_id")
                            ->setJoins("acc_pelunasan_piutang_summary_koreksi appsk", "apps.id = appsk.pelunasan_summary_id")
                            ->setWheres([
                                "YEAR(app.tanggal_transaksi)" => $bulans[0],
                                "MONTH(app.tanggal_transaksi)" => $bulans[1],
                                "appsk.lunas" => 0,
                                "appsk.alat_pelunasan" => "true",
                                "appsk.koreksi_id" => "deposit",
                                "app.status" => "done",
                                "apps.tipe_currency" => "Valas"
                            ])->setSelects(["apps.id,apps.no_pelunasan,apps.total_piutang,apps.total_pelunasan,apps.kurs,apps.kurs_akhir,appsk.kode_coa"])->getData();
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    protected function _updateDeposit() {
        try {
            $bulans = explode("-", $this->input->post("bulan"));
            $kurs = str_replace(",", "", $this->input->post("kurs"));
            $model = new $this->m_global;
            $update = [];
            $data = $model->setTables("acc_pelunasan_piutang app")->setJoins("acc_pelunasan_piutang_summary apps", "app.id = apps.pelunasan_piutang_id")
                            ->setJoins("acc_pelunasan_piutang_summary_koreksi appsk", "apps.id = appsk.pelunasan_summary_id")
                            ->setWheres([
                                "YEAR(app.tanggal_transaksi)" => $bulans[0],
                                "MONTH(app.tanggal_transaksi)" => $bulans[1],
                                "appsk.lunas" => 0,
                                "appsk.alat_pelunasan" => "true",
                                "appsk.koreksi_id" => "deposit",
                                "app.status" => "done",
                                "apps.tipe_currency" => "Valas"
                            ])->setSelects(["apps.id"])->getData();
            foreach ($data as $key => $value) {
                $update[] = ["kurs_akhir" => $kurs, "id" => $value->id];
            }

            if (count($update) > 0) {
                $model->setTables("acc_pelunasan_piutang_summary apps")->updateBatch($update, "id");
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    protected function _updatekasView() {
        try {
            $bulans = explode("-", $this->input->post("bulan"));
            $curr = $this->input->post("currency");
            $kurs = str_replace(",", "", $this->input->post("kurs"));
            //kas masuk
            $model = new $this->m_global;
            $kasMasuk = $model->setTables("acc_kas_masuk_detail")
                            ->setSelects(["no_km as no,'Kas Masuk' as nama_menu,kurs,nominal,kurs_akhir,kode_coa"])
                            ->setWheres([
                                "YEAR(tanggal)" => $bulans[0],
                                "MONTH(tanggal)" => $bulans[1],
                                "lunas" => 0,
                            ])->setWhereRaw("currency_id = (select id from currency_kurs where currency = '{$curr}')")
                            ->setWhereRaw("kode_coa in (select kode_coa from acc_coa where jenis_transaksi REGEXP 'piutang')")->getQuery();

            //kas keluar
            $kasKeluar = $model->setTables("acc_kas_keluar_detail")
                            ->setSelects(["no_kk as no,'Kas Keluar' as nama_menu,kurs,nominal,kurs_akhir,kode_coa"])
                            ->setWheres([
                                "YEAR(tanggal)" => $bulans[0],
                                "MONTH(tanggal)" => $bulans[1],
                                "lunas" => 0,
                            ])->setWhereRaw("currency_id = (select id from currency_kurs where currency = '{$curr}')")
                            ->setWhereRaw("kode_coa in (select kode_coa from acc_coa where jenis_transaksi REGEXP 'utang')")->getQuery();
            //bank masuk
            $bankMasuk = $model->setTables("acc_bank_masuk_detail")
                            ->setSelects(["no_bm as no,'Bank Masuk' as nama_menu,kurs,nominal,kurs_akhir,kode_coa"])
                            ->setWheres([
                                "YEAR(tanggal)" => $bulans[0],
                                "MONTH(tanggal)" => $bulans[1],
                                "lunas" => 0,
                            ])->setWhereRaw("currency_id = (select id from currency_kurs where currency = '{$curr}')")
                            ->setWhereRaw("kode_coa in (select kode_coa from acc_coa where jenis_transaksi REGEXP 'piutang')")->getQuery();

            //bank keluar
            $bankKeluar = $model->setTables("acc_bank_keluar_detail")
                            ->setSelects(["no_bk as no,'Bank Keluar' as nama_menu,kurs,nominal,kurs_akhir,kode_coa"])
                            ->setWheres([
                                "YEAR(tanggal)" => $bulans[0],
                                "MONTH(tanggal)" => $bulans[1],
                                "lunas" => 0,
                            ])->setWhereRaw("currency_id = (select id from currency_kurs where currency = '{$curr}')")
                            ->setWhereRaw("kode_coa in (select kode_coa from acc_coa where jenis_transaksi REGEXP 'utang')")->getQuery();

            //giro masuk
            $giroMasuk = $model->setTables("acc_giro_masuk_detail")
                            ->setSelects(["no_gm as no,'Giro Masuk' as nama_menu,kurs,nominal,kurs_akhir,kode_coa"])
                            ->setWheres([
                                "YEAR(tanggal)" => $bulans[0],
                                "MONTH(tanggal)" => $bulans[1],
                                "lunas" => 0
                            ])->setWhereRaw("currency_id = (select id from currency_kurs where currency = '{$curr}')")
                            ->setWhereRaw("kode_coa in (select kode_coa from acc_coa where jenis_transaksi REGEXP 'piutang')")->getQuery();

            //giro keluar
            $giroKeluar = $model->setTables("acc_giro_keluar_detail")
                            ->setSelects(["no_gk as no,'Giro Keluar' as nama_menu,kurs,nominal,kurs_akhir,kode_coa"])
                            ->setWheres([
                                "YEAR(tanggal)" => $bulans[0],
                                "MONTH(tanggal)" => $bulans[1],
                                "lunas" => 0,
                            ])->setWhereRaw("currency_id = (select id from currency_kurs where currency = '{$curr}')")
                            ->setWhereRaw("kode_coa in (select kode_coa from acc_coa where jenis_transaksi REGEXP 'utang')")->getQuery();

            return $model->setTables("({$kasMasuk} union all {$kasKeluar} union all {$bankMasuk} union all {$bankKeluar} union all {$giroMasuk} union all {$giroKeluar}) as tbl")->getData();
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    protected function _updatekas() {
        try {
            $bulans = explode("-", $this->input->post("bulan"));
            $curr = $this->input->post("currency");
            $kurs = str_replace(",", "", $this->input->post("kurs"));
            //kas masuk
            $model = new $this->m_global;
            $model->setTables("acc_kas_masuk_detail")
                    ->setWheres([
                        "YEAR(tanggal)" => $bulans[0],
                        "MONTH(tanggal)" => $bulans[1],
                        "lunas" => 0,
                    ])->setWhereRaw("currency_id = (select id from currency_kurs where currency = '{$curr}')")
                    ->setWhereRaw("kode_coa in (select kode_coa from acc_coa where jenis_transaksi REGEXP 'piutang')")
                    ->update(["kurs_akhir" => $kurs]);

            //kas keluar
            $model->setTables("acc_kas_keluar_detail")
                    ->setWheres([
                        "YEAR(tanggal)" => $bulans[0],
                        "MONTH(tanggal)" => $bulans[1],
                        "lunas" => 0,
                    ])->setWhereRaw("currency_id = (select id from currency_kurs where currency = '{$curr}')")
                    ->setWhereRaw("kode_coa in (select kode_coa from acc_coa where jenis_transaksi REGEXP 'utang')")->update(["kurs_akhir" => $kurs]);
            //bank masuk
            $model->setTables("acc_bank_masuk_detail")
                    ->setWheres([
                        "YEAR(tanggal)" => $bulans[0],
                        "MONTH(tanggal)" => $bulans[1],
                        "lunas" => 0,
                    ])->setWhereRaw("currency_id = (select id from currency_kurs where currency = '{$curr}')")
                    ->setWhereRaw("kode_coa in (select kode_coa from acc_coa where jenis_transaksi REGEXP 'piutang')")->update(["kurs_akhir" => $kurs]);

            //bank keluar
            $model->setTables("acc_bank_keluar_detail")
                    ->setWheres([
                        "YEAR(tanggal)" => $bulans[0],
                        "MONTH(tanggal)" => $bulans[1],
                        "lunas" => 0,
                    ])->setWhereRaw("currency_id = (select id from currency_kurs where currency = '{$curr}')")
                    ->setWhereRaw("kode_coa in (select kode_coa from acc_coa where jenis_transaksi REGEXP 'utang')")->update(["kurs_akhir" => $kurs]);

            //giro masuk
            $model->setTables("acc_giro_masuk_detail")
                    ->setWheres([
                        "YEAR(tanggal)" => $bulans[0],
                        "MONTH(tanggal)" => $bulans[1],
                        "lunas" => 0
                    ])->setWhereRaw("currency_id = (select id from currency_kurs where currency = '{$curr}')")
                    ->setWhereRaw("kode_coa in (select kode_coa from acc_coa where jenis_transaksi REGEXP 'piutang')")->update(["kurs_akhir" => $kurs]);

            //giro keluar
            $model->setTables("acc_giro_keluar_detail")
                    ->setWheres([
                        "YEAR(tanggal)" => $bulans[0],
                        "MONTH(tanggal)" => $bulans[1],
                        "lunas" => 0,
                    ])->setWhereRaw("currency_id = (select id from currency_kurs where currency = '{$curr}')")
                    ->setWhereRaw("kode_coa in (select kode_coa from acc_coa where jenis_transaksi REGEXP 'utang')")->update(["kurs_akhir" => $kurs]);
        } catch (Exception $ex) {
            throw $ex;
        }
    }
}
