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
    }

    public function index() {
        $data['id_dept'] = 'ACCKAB';
        $this->load->view('accounting/v_kursakhirbulan', $data);
    }

    public function add() {
        $data['id_dept'] = 'ACCKAB';
        $this->load->view('accounting/v_kursakhirbulan_add', $data);
    }

    protected function _getSaldo() {
        try {
            $curr = $this->input->post("currency");
            $bulan = $this->input->post("bulan");
            $start = "{$bulan}-01";
            $akhir = date("Y-m-t", strtotime($start));
            $model = new $this->m_global;

            $saldoDebet = $model->setTables("acc_bank_masuk bm")
                    ->setJoins("acc_bank_masuk_detail bmd","bm.id = bank_masuk_id")->setGroups(["bm.kode_coa"])
                    ->setSelects(["bm.kode_coa, SUM(bm.total_rp) as total_debit","sum(bm.total_rp * bmd.kurs) as total_debit_rp"])
                    ->setWheres(['date(bm.tanggal) >= ' => $start, 'date(bm.tanggal) <= ' => $akhir, 'status' => 'confirm'])
                    ->getQuery();
            $saldoKredit = $model->setTables("acc_bank_keluar bk")
                    ->setJoins("acc_bank_keluar_detail bkd","bk.id = bank_keluar_id")->setGroups(["bk.kode_coa"])
                    ->setSelects(["bk.kode_coa, SUM(bk.total_rp) as total_credit","sum(bk.total_rp * bkd.kurs) as total_credit_rp"])
                    ->setWheres(['date(bk.tanggal) >= ' => $start, 'date(bk.tanggal) <= ' => $akhir, 'status' => 'confirm'])
                    ->getQuery();

            $model->setTables("acc_coa coa")->setJoins("({$saldoDebet}) as debit_sbl", "debit_sbl.kode_coa = coa.kode_coa", "left")
                    ->setJoins("({$saldoKredit}) as credit_sbl", "credit_sbl.kode_coa = coa.kode_coa", "left")
                    ->setOrder(["coa.kode_coa" => "asc"])->setWheres(["coa.curr" => $curr])
                    ->setSelects(["coa.kode_coa, coa.nama as nama_coa,coa.saldo_normal,coa.saldo_awal,coa.saldo_valas,COALESCE(debit_sbl.total_debit, 0) as total_debit_sbl"])
                            ->setSelects(["COALESCE(debit_sbl.total_debit_rp, 0) as total_debit_rp_sbl","COALESCE(credit_sbl.total_credit_rp, 0) as total_credit_rp_sbl"])
                    ->setSelects(["COALESCE(credit_sbl.total_credit, 0) as total_credit_sbl"])
                    ->setSelects(["CASE 
                                WHEN coa.saldo_normal = 'D' THEN 
                                    (coa.saldo_valas + COALESCE(debit_sbl.total_debit, 0) - COALESCE(credit_sbl.total_credit, 0))
                                WHEN coa.saldo_normal = 'C' THEN 
                                    (coa.saldo_valas + COALESCE(credit_sbl.total_credit, 0) - COALESCE(debit_sbl.total_debit, 0))
                                ELSE coa.saldo_valas
                            END AS saldo_valas_final"])
                            ->setSelects(["CASE 
                                WHEN coa.saldo_normal = 'D' THEN 
                                    (coa.saldo_awal + COALESCE(debit_sbl.total_debit_rp, 0) - COALESCE(credit_sbl.total_credit_rp, 0))
                                WHEN coa.saldo_normal = 'C' THEN 
                                    (coa.saldo_awal + COALESCE(credit_sbl.total_credit_rp, 0) - COALESCE(debit_sbl.total_debit_rp, 0))
                                ELSE coa.saldo_awal
                            END AS saldo_rp_final"]);
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
                    ->setOrders([null, "no", "bulan", "curr", "kurs", "no_jurnal"])
                    ->setSearch(["no", "bulan", "kurs", "curr", "no_jurnal"]);
            $no = $_POST['start'];
            foreach ($list->getData() as $field) {
                $no++;
                $link = base_url("accounting/jurnalentries/edit/" . encrypt_url($field->no_jurnal));
                $data[] = [
                    $no,
                    $field->no,
                    $field->bulan,
                    number_format($field->kurs, 2),
                    $field->curr,
                    "<a href='{$link}' target='_blank'>{$field->no_jurnal}</a>"
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
            $tanggal = date("Y-m-d H:i:s");

            if (!$no = $this->token->noUrut('kurs_bulan', date('ym', strtotime($tanggal)), true)->generate('KAB', '/%03d')->prefixAdd("/" . date("y", strtotime($tanggal)) . "/" . date('m', strtotime($tanggal)))->get()) {
                throw new \Exception("No Kurs tidak terbuat", 500);
            }
            $curr = $this->input->post("currency");
            $symcurr = $this->input->post("symcurr");
            $kurs = str_replace(",", "", $this->input->post("kurs"));
            $model = new $this->m_global;

            $coa = $model->setTables("acc_coa")->setWheres(["curr" => $curr])->getData();
            $coask = $model->setTables("setting")->setWheres(["setting_name" => "selisih_kurs"])->getDetail();

            $head = [
                "no" => $no,
                "bulan" => $this->input->post("bulan"),
                "kurs" => $kurs,
                "curr" => $curr,
                "created_at" => $tanggal
            ];

            if (!$noJurnal = $this->token->noUrut('jurnal_selisih_kurs', date('ym', strtotime($tanggal)), true)->generate('SK', '/%03d')->prefixAdd("/" . date("y", strtotime($tanggal)) . "/" . date('m', strtotime($tanggal)))->get()) {
                throw new \Exception("No Jurnal tidak terbuat", 500);
            }
            $dataJurnal = [
                "kode" => $noJurnal,
                "origin" => $no,
                "tanggal_dibuat" => $tanggal,
                "tanggal_posting" => $tanggal,
                "periode" => date("Y-m"),
                "tipe" => "SK",
                "status" => "posted"
            ];
            $head["no_jurnal"] = $noJurnal;
            $model->setTables("acc_kurs_akhir_bulan")->save($head);
            $model->setTables("acc_jurnal_entries")->save($dataJurnal);
            $noOrder = 1;
            $entriesDetail = [];
            $updateCoa = [];
            foreach ($coa as $key => $value) {
                $selisih = ($value->saldo_valas * $kurs) - $value->saldo_awal;
                $nominal = abs($selisih);
                if ($nominal === (double) 0) {
                    continue;
                }
                $nama = "Kurs Akhir Bulan (Saldo : " . number_format($value->saldo_valas, 2) . " {$curr} Kurs : " . number_format($kurs, 2) . ")";
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
                $updateCoa[] = [
                    "kode_coa" => $value->kode_coa,
                    "saldo_awal" => ($value->saldo_awal + $selisih)
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
            $this->_updatekas();
            if (count($updateCoa) > 0) {
                $model->setTables("acc_coa")->updateBatch($updateCoa, "kode_coa");
            }
            if (!$this->_module->finishTransaction()) {
                throw new \Exception('Gagal Menyimpan Data', 500);
            }
            $this->_module->gen_history_new($sub_menu, $no, 'create', "DATA -> " . logArrayToString("; ", $head), $username);
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
}
