<?php

defined('BASEPATH') OR EXIT('No Direct Script Acces Allowed');
/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of Asettetap
 *
 * @author RONI
 */
class Asettetap extends MY_Controller {

    //put your code here

    protected $valForm = [
        [
            'field' => 'nama',
            'label' => 'Nama',
            'rules' => ['trim', 'required'],
            'errors' => [
                'required' => '{field} Harus diisi'
            ]
        ],
        [
            'field' => 'tanggal_beli',
            'label' => 'Tanggal Beli',
            'rules' => ['trim', 'required'],
            'errors' => [
                'required' => '{field} Harus dipilih'
            ]
        ],
        [
            'field' => 'tanggal_pakai',
            'label' => 'Tanggal Pakai',
            'rules' => ['trim', 'required'],
            'errors' => [
                'required' => '{field} Harus dipilih'
            ]
        ],
        [
            'field' => 'harga',
            'label' => 'Harga',
            'rules' => ['trim', 'required', 'regex_match[/^-?\d*(,\d{3})*\.?\d*$/]'],
            'errors' => [
                'required' => '{field} Pada Item harus diisi',
                "regex_match" => "{field} harus berupa number / desimal"
            ]
        ],
        [
            'field' => 'nilai_sisa',
            'label' => 'Nilai Sisa',
            'rules' => ['trim', 'required', 'regex_match[/^-?\d*(,\d{3})*\.?\d*$/]'],
            'errors' => [
                'required' => '{field} Pada Item harus diisi',
                "regex_match" => "{field} harus berupa number / desimal"
            ]
        ],
        [
            'field' => 'kategori',
            'label' => 'Kategori',
            'rules' => ['trim', 'required'],
            'errors' => [
                'required' => '{field} Harus dipilih'
            ]
        ],
        [
            'field' => 'kelompok',
            'label' => 'Kelompok',
            'rules' => ['trim', 'required'],
            'errors' => [
                'required' => '{field} Harus dipilih'
            ]
        ],
        [
            'field' => 'umur',
            'label' => 'Umur Aset',
            'rules' => ['trim', 'required'],
            'errors' => [
                'required' => '{field} Harus dipilih'
            ]
        ],
        [
            'field' => 'tarif',
            'label' => 'tarif',
            'rules' => ['trim', 'required', 'regex_match[/^\d*\.?\d*$/]'],
            'errors' => [
                'required' => '{field} Harus dipilih',
                "regex_match" => "{field} harus berupa number / desimal"
            ]
        ]
    ];

    public function __construct() {
        parent:: __construct();
        $this->is_loggedin();
        $this->load->model("m_global");
        $this->load->model('_module');
        $this->load->driver('cache', array('adapter' => 'file'));
        $this->config->load('additional');
        $this->load->library("token");
    }

    public function index($depth = "ACCASTE") {
        $data['id_dept'] = $depth;
        $data["class"] = $this->uri->segment(1);
        $this->load->view('accounting/v_aset_tetap', $data);
    }

    public function add($depth = "ACCASTE") {
        $data['id_dept'] = $depth;
        $data["class"] = $this->uri->segment(1);
        $this->load->view('accounting/v_aset_tetap_add', $data);
    }

    public function edit($id, $depth = "ACCASTE") {
        try {
            $data["id"] = $id;
            $data['id_dept'] = $depth;
            $model = new $this->m_global;
            $kode = decrypt_url($id);
            $data["datas"] = $model->setTables("acc_aset_tetap")->setWheres(["no_aset" => $kode])->getDetail();
            if (!$data['datas']) {
                throw new \Exception("", 500);
            }
            $data["jurnals"] = $model->setTables("acc_aset_tetap_jurnal")
                            ->setJoins("acc_jurnal_entries", "acc_jurnal_entries.kode = no_jurnal", "left")
                            ->setSelects(["acc_aset_tetap_jurnal.*", "acc_jurnal_entries.status as status_jurnal,reff_note"])
                            ->setWheres(["id_aset" => $data["datas"]->id])->getData();
            $this->load->view('accounting/v_aset_tetap_edit', $data);
        } catch (Exception $ex) {
            log_message("error", $ex->getMessage());
            show_404();
        }
    }

    public function list_data() {
        try {
            $model = new $this->m_global;
            $tanggal = $this->input->post("tanggal");
            $noAset = $this->input->post("no_aset");
            $class = $this->uri->segment(1);
            $model->setTables("acc_aset_tetap")->setJoins("mst_status", "mst_status.kode = acc_aset_tetap.status", "left")
                    ->setOrder(["tanggal_beli" => "desc"])->setOrders([null, "no_aset", "nama", "tanggal_beli", "tanggal_pakai", "harga", "kategori", "kelompok"])
                    ->setSearch(["no_aset", "nama", "kategori", "kelompok"]);
            if (!empty($tanggal)) {
                $tanggals = explode(" - ", $tanggal);
                $model->setWheres(["date(tanggal_pakai) >=" => $tanggals[0], "date(tanggal_pakai) <=" => $tanggals[1]]);
            }
            if ($noAset !== "") {
                $model->setWheres(["no_aset LIKE" => "%{$noAset}%"]);
            }
            $data = array();
            $no = $_POST['start'];
            foreach ($model->getData() as $key => $value) {
                $no += 1;
                $kode_encrypt = encrypt_url($value->no_aset);
                $data [] = [
                    $no,
                    "<a href='" . base_url("{$class}/asettetap/edit/{$kode_encrypt}") . "'>{$value->no_aset}</a>",
                    $value->nama,
                    $value->tanggal_beli,
                    $value->tanggal_pakai,
                    number_format($value->harga, 2),
                    $value->kategori,
                    $value->kelompok,
                    $value->nama_status
                ];
            }
            echo json_encode(array("draw" => $_POST['draw'],
                "recordsTotal" => $model->getDataCountAll(),
                "recordsFiltered" => $model->getDataCountFiltered(),
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

    public function kategori_kelompok() {
        try {
            $where = $this->input->get("param");
            $search = $this->input->get("search");
            $where = urldecode($where);
            $model = new $this->m_global;
            $model->setTables("acc_aset_kategori");
            if (empty($where)) {
                $model->setSelects(["kategori"])->setGroups(["kategori"]);
            } else {
                $model->setWhereRaw($where);
            }
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array("data" => $model->getData())));
        } catch (Exception $ex) {
            $this->output->set_status_header(500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger', "data" => [])));
        }
    }

    public function simpan() {
        try {
            $this->form_validation->set_rules($this->valForm);
            if ($this->form_validation->run() == FALSE) {
                throw new \Exception(array_values($this->form_validation->error_array())[0], 500);
            }
            $class = $this->uri->segment(1);
            $sub_menu = $this->uri->segment(2);
            $username = $this->session->userdata('username');
            $nama = $this->input->post("nama");
            $tglBeli = $this->input->post("tanggal_beli");
            $tglPakai = $this->input->post("tanggal_pakai");
            $harga = $this->input->post("harga");
            $nilaiSisa = $this->input->post("nilai_sisa");
            $kategori = $this->input->post("kategori");
            $kelompok = $this->input->post("kelompok");
            $metode = $this->input->post("m_penyusutan");
            $umur = $this->input->post("umur");
            $tarif = $this->input->post("tarif");
            $akunAset = $this->input->post("akun_aset");
            $akunAkum = $this->input->post("akun_akum");
            $akunPenyu = $this->input->post("akun_penyusutan");
            if (!$noAset = $this->token->noUrut('aset_tetap', date('y', strtotime($tglBeli)) . '/' . getRomawi(date('m', strtotime($tglBeli))), true)->generate('PA/', '/%04d')->get())
                throw new \Exception("No Aset tidak terbuat", 500);

            $harga = str_replace(",", "", $harga);
            $nilaiSisa = str_replace(",", "", $nilaiSisa);
            $data = [
                "no_aset" => $noAset,
                "nama" => $nama,
                "tanggal_beli" => $tglBeli,
                "tanggal_pakai" => $tglPakai,
                "harga" => $harga,
                "kategori" => $kategori,
                "kelompok" => $kelompok,
                "metode" => $metode,
                "umur_aset" => $umur,
                "tarif_penyusutan" => $tarif,
                "nilai_sisa" => $nilaiSisa,
                "akun_asset" => $akunAset,
                "akun_akum_penyusutan" => $akunAkum,
                "akun_bbn_penyusutan" => $akunPenyu,
                "created_at" => date("Y-m-d H:i:s")
            ];
            $model = new $this->m_global;
            $model->setTables("acc_aset_tetap")->save($data);
            $log = "DATA -> " . logArrayToString("; ", $data);
            $this->_module->gen_history_new($sub_menu, $noAset, "create", $log, $username);
            $url = site_url("{$class}/asettetap/edit/" . encrypt_url($noAset));
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success', 'url' => $url)));
        } catch (Exception $ex) {
            $this->output->set_status_header(500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }

    public function update($id) {
        try {
            $this->form_validation->set_rules($this->valForm);
            if ($this->form_validation->run() == FALSE) {
                throw new \Exception(array_values($this->form_validation->error_array())[0], 500);
            }
            $kode = decrypt_url($id);
            $sub_menu = $this->uri->segment(2);
            $username = $this->session->userdata('username');
            $nama = $this->input->post("nama");
            $tglBeli = $this->input->post("tanggal_beli");
            $tglPakai = $this->input->post("tanggal_pakai");
            $harga = $this->input->post("harga");
            $nilaiSisa = $this->input->post("nilai_sisa");
            $kategori = $this->input->post("kategori");
            $kelompok = $this->input->post("kelompok");
            $metode = $this->input->post("m_penyusutan");
            $umur = $this->input->post("umur");
            $tarif = $this->input->post("tarif");
            $akunAset = $this->input->post("akun_aset");
            $akunAkum = $this->input->post("akun_akum");
            $akunPenyu = $this->input->post("akun_penyusutan");
            $harga = str_replace(",", "", $harga);
            $nilaiSisa = str_replace(",", "", $nilaiSisa);
            $data = [
                "nama" => $nama,
                "tanggal_beli" => $tglBeli,
                "tanggal_pakai" => $tglPakai,
                "harga" => $harga,
                "kategori" => $kategori,
                "kelompok" => $kelompok,
                "metode" => $metode,
                "umur_aset" => $umur,
                "tarif_penyusutan" => $tarif,
                "nilai_sisa" => $nilaiSisa,
                "akun_asset" => $akunAset,
                "akun_akum_penyusutan" => $akunAkum,
                "akun_bbn_penyusutan" => $akunPenyu
            ];
            $model = new $this->m_global;
            $model->setTables("acc_aset_tetap")->setWheres(["no_aset" => $kode])->update($data);
            $log = "DATA -> " . logArrayToString("; ", $data);
            $this->_module->gen_history_new($sub_menu, $kode, "edit", $log, $username);
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success')));
        } catch (Exception $ex) {
            $this->output->set_status_header(500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }

    public function generate() {
        try {
            $metode = $this->input->post("metode");
            $data = $this->_generate();
            if ($metode === "tarif_garis_lurus") {
                $html = $this->load->view('accounting/v_aset_tetap_penyusutan_lurus', ["data" => $data], true);
            } else {
                $html = $this->load->view('accounting/v_aset_tetap_penyusutan_menurun', ["data" => $data["data"], "umur" => $data["umur"], "list" => $data["list"]], true);
            }
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success', 'data' => $html)));
        } catch (Exception $ex) {
            $this->output->set_status_header(500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }

    public function show_jurnal($id) {
        try {
            $data["id"] = $id;
            $model = new $this->m_global;
            $noJurnal = $this->input->post("jurnal");
            $data["jurnal"] = $model->setTables("acc_jurnal_entries")
                            ->setJoins("mst_jurnal", "mst_jurnal.kode = acc_jurnal_entries.tipe", "left")
                            ->setWheres(["acc_jurnal_entries.kode" => $noJurnal])
                            ->setSelects(["mst_jurnal.nama as nama_jurnal", "acc_jurnal_entries.*,date(tanggal_dibuat) as tanggal_dibuat"])->getDetail();
            $data["detail"] = $model->setTables("acc_jurnal_entries_items jei")
                            ->setJoins("partner", "partner.id = jei.partner", "left")
                            ->setJoins("acc_coa", "acc_coa.kode_coa = jei.kode_coa", "left")
                            ->setJoins("acc_jurnal_entries je", "je.kode = jei.kode")
                            ->setOrder(["jei.posisi" => "desc", "jei.kode_coa" => "asc"])
                            ->setSelects(["jei.*", "partner.nama as supplier,partner.id as supplier_id", "acc_coa.nama as account", "je.tipe"])
                            ->setWheres(["je.kode" => $noJurnal])->getData();
            $data["coas"] = $model->setTables("acc_coa")->setSelects(["kode_coa", "nama"])
                            ->setWheres(["level" => 5, "status" => "aktif"])->setOrder(["kode_coa" => "asc"])->getData();
            $html = $this->load->view('accounting/modal/v_jurnal_aset', $data, true);
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success', "data" => $html)));
        } catch (Exception $ex) {
            $this->output->set_status_header(500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger', "data" => "")));
        }
    }

    public function update_jurnal($id) {
        $totalDebet = 0;
        $totalKredit = 0;
        try {
            $sub_menu = $this->uri->segment(2);
            $username = addslashes($this->session->userdata('username'));

            $kode_decrypt = decrypt_url($id);
            $account = $this->input->post("kode_coa");
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
                ]);
                if ($this->form_validation->run() == FALSE) {
                    throw new \Exception(array_values($this->form_validation->error_array())[0], 500);
                }
            }
            $refnote = $this->input->post("reff_note");
            $origin = $this->input->post("origin");
            $periode = $this->input->post("periode");
            $model = new $this->m_global;
            $headUpdate = ["reff_note" => $refnote,
                "origin" => $origin ?? "",
                "periode" => $periode
            ];

            $partner = $this->input->post("partner");
            $nama = $this->input->post("nama");
            $noteItem = $this->input->post("reffnote_item");
            $debit = $this->input->post("debet");
            $kredit = $this->input->post("kredit");
            $jurnal = $this->input->post("jurnal");
            $itemUpdate = [];
            $no = 0;
            foreach ($account as $key => $value) {
                $no++;
                $db = str_replace(",", "", $debit[$key]);
                $kr = str_replace(",", "", $kredit[$key]);
                $posisi = "D";
                if ($db > 0) {
                    $nominalCurr = $db;
                    $totalDebet += $db;
                } else {
                    $nominalCurr = $kr;
                    $posisi = "C";
                    $totalKredit += $kr;
                }
                $itemUpdate[] = [
                    "kode_coa" => $value,
                    "kode" => $jurnal,
                    "nama" => $nama[$key] ?? "",
                    "reff_note" => $noteItem[$key] ?? "",
                    "partner" => $partner[$key] ?? 0,
                    "kurs" => 1,
                    "kode_mua" => "IDR",
                    "nominal" => $nominalCurr * 1,
                    "row_order" => $no,
                    "posisi" => $posisi,
                    "nominal_curr" => $nominalCurr
                ];
            }
            if ($totalDebet !== $totalKredit) {
                throw new \Exception('Debet Dan Kredit Balance', 500);
            }
            $this->_module->startTransaction();
            $model->setTables("acc_aset_tetap_jurnal")->setWheres(["no_jurnal" => $jurnal])->update(["penyusutan_bulan"=>$totalDebet]);
            $model->setTables("acc_jurnal_entries")->setWheres(["kode" => $jurnal])->update($headUpdate);
            $model->setTables("acc_jurnal_entries_items")->setWheres(["kode" => $jurnal])->delete();
            $model->saveBatch($itemUpdate);
            if (!$this->_module->finishTransaction()) {
                throw new \Exception('Gagal Menyimpan Data', 500);
            }
            $log = " DATA -> " . logArrayToString("; ", $headUpdate);
            $log .= "\nDetail -> " . logArrayToString("; ", $itemUpdate);
            $this->_module->gen_history_new($sub_menu, $kode_decrypt, "edit", $log, $username);
            $this->_module->gen_history_new("jurnalentries", $jurnal, "edit", $log, $username);
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success',
                        "kredit" => number_format($totalKredit, 2), "debet" => number_format($totalDebet, 2))));
        } catch (Exception $ex) {
            $this->_module->rollbackTransaction();
            log_message("error", json_encode($ex));
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger',
                        "kredit" => number_format($totalKredit, 2), "debet" => number_format($totalDebet, 2))));
        }
    }

    public function update_status_jurnal($id) {
        try {
            $status = $this->input->post("status");
            $jurnal = $this->input->post("jurnal");
            $sub_menu = $this->uri->segment(2);
            $username = $this->session->userdata('username');

            $kode = decrypt_url($id);
            $model = new $this->m_global;
            $model->setTables("acc_jurnal_entries")->setWheres(["kode" => $jurnal])->update(["status" => $status]);

            $this->_module->gen_history_new($sub_menu, $kode, "edit", "Update Status ({$status}) Dengan No Jurnal {$jurnal}", $username);
            $this->_module->gen_history_new("jurnalentries", $jurnal, "edit", "Update Status ({$status}) Dari Menu Aset", $username);
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success')));
        } catch (Exception $ex) {
            $this->output->set_status_header(500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger', "data" => "")));
        }
    }

    public function update_status_jurnals($id) {
        try {
            $status = $this->input->post("status");
            $sub_menu = $this->uri->segment(2);
            $username = $this->session->userdata('username');

            $kode = decrypt_url($id);
            $model = new $this->m_global;
            $model->setTables("acc_jurnal_entries")->setWheres(["origin" => $kode, "status <>" => "cancel"])->update(["status" => $status]);

            $this->_module->gen_history_new($sub_menu, $kode, "edit", "Update Jurnal Ke Status ({$status})", $username);
//            $this->_module->gen_history_new("jurnalentries", $jurnal, "edit", "Update Status ({$status}) Dari Menu Aset", $username);
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success')));
        } catch (Exception $ex) {
            $this->output->set_status_header(500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger', "data" => "")));
        }
    }

    public function cancel($id) {
        try {
            $sub_menu = $this->uri->segment(2);
            $username = $this->session->userdata('username');
            $kode = decrypt_url($id);

            $lock = "main_menu_sub READ, log_history WRITE,delivery_order do WRITE,acc_aset_tetap WRITE,"
                    . "token_increment WRITE,acc_jurnal_entries WRITE";
            $this->_module->lock_tabel($lock);
            $model = new $this->m_global;

            $model->setTables("acc_jurnal_entries")->setWheres(["origin" => $kode])->update(["status" => "cancel"]);
            $getJurnal = $model->getData();
            $model->setTables("acc_aset_tetap")->setWheres(["no_aset" => $kode])->update(["status" => "cancel"]);
            if (!$this->_module->finishTransaction()) {
                throw new \Exception('Gagal Menyimpan Data', 500);
            }
            foreach ($getJurnal as $key => $value) {
                $this->_module->gen_history_new("jurnal_entries", $value->kode, "edit", "Update Status (cancel) Dari Menu Aset", $username);
            }
            $this->_module->gen_history_new($sub_menu, $kode, "edit", "Update status ke cancel", $username);
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

            $kode = decrypt_url($id);
            $metode = $this->input->post("metode");
            $data = $this->_generate();
            $jurnalData = [];
            $jurnalDataDetail = [];
            $lock = "main_menu_sub READ, log_history WRITE,delivery_order do WRITE,acc_aset_tetap WRITE,"
                    . "token_increment WRITE,acc_jurnal_entries_items WRITE,acc_jurnal_entries WRITE";
            $this->_module->lock_tabel($lock);

            $model = new $this->m_global;
            $check = $model->setTables("acc_aset_tetap")->setWheres(["no_aset" => $kode])->getDetail();
            if (empty($check->akun_asset))
                throw new \Exception("Akun Aset Belum Dipilih", 500);
            if (empty($check->akun_akum_penyusutan))
                throw new \Exception("Akun Akumulasi Belum Dipilih", 500);
            if (empty($check->akun_bbn_penyusutan))
                throw new \Exception("Akun Penyusutan Belum Dipilih", 500);
            $this->_module->startTransaction();
            $logH = [];
            $logD = [];
            $jurnals = [];
            $textGen = "";
            $asetJurnal = [];
            if ($metode === "tarif_garis_lurus") {
                foreach ($data as $key => $value) {
                    if (!$jurnal = $this->token->noUrut("penyusutan_at", date('y', strtotime($value->tanggal)) . '/' . date('m', strtotime($value->tanggal)), true)->generate("PA/", '/%05d')->get())
                        throw new \Exception("No jurnal tidak terbuat", 500);

                    $jurnals[] = $jurnal;
                    $jd = ["kode" => $jurnal, "periode" => date("Y/m", strtotime($value->tanggal)),
                        "origin" => "{$kode}", "status" => "unposted", "tanggal_dibuat" => $value->tanggal, "tipe" => "PA",
                        "tanggal_posting" => date("Y-m-d H:i:s"), "reff_note" => ($key + 1) . " Dari " . count($data)];

                    $asetJurnal[] = [
                        "id_aset" => $check->id,
                        "aset_tetap_no" => $kode,
                        "penyusutan_thn" => $value->tahun_penyu,
                        "penyusutan_bulan" => $value->bulan_penyu,
                        "penyusutan_tgl" => $value->tanggal,
                        "no_jurnal" => $jurnal
                    ];

                    $jurnalData[] = $jd;
                    $jdt = [];
                    $jdt[] = [
                        "kode" => $jurnal,
                        "nama" => "{$check->nama}",
                        "reff_note" => "Beban Penyusutan",
                        "partner" => 0,
                        "kode_coa" => $check->akun_bbn_penyusutan,
                        "posisi" => "D",
                        "nominal_curr" => round($value->bulan_penyu, 2),
                        "kurs" => 1,
                        "kode_mua" => "IDR",
                        "nominal" => round($value->bulan_penyu, 2),
                        "row_order" => (count($jurnalDataDetail) + 1)
                    ];
                    $jdt[] = [
                        "kode" => $jurnal,
                        "nama" => "{$check->nama}",
                        "reff_note" => "Akumulasi Penyusutan",
                        "partner" => 0,
                        "kode_coa" => $check->akun_akum_penyusutan,
                        "posisi" => "C",
                        "nominal_curr" => round($value->bulan_penyu, 2),
                        "kurs" => 1,
                        "kode_mua" => "IDR",
                        "nominal" => round($value->bulan_penyu, 2),
                        "row_order" => (count($jurnalDataDetail) + 2)
                    ];
                    $logH[] = "Header -> " . logArrayToString("; ", $jd);
                    $logD [] = "\nDETAIL -> " . logArrayToString("; ", $jdt);
                    $jurnalDataDetail = array_merge($jurnalDataDetail, $jdt);
                }
            } else {
                $textGen = $data["data"];
                foreach ($data["list"] as $key => $value) {
                    if (!$jurnal = $this->token->noUrut("penyusutan_at", date('y', strtotime($value->tanggal)) . '/' . date('m', strtotime($value->tanggal)), true)
                                    ->generate("PA/", '/%05d')->get()) {
                        throw new \Exception("No jurnal tidak terbuat", 500);
                    }
                    $jurnals[] = $jurnal;
                    $jd = ["kode" => $jurnal, "periode" => date("Y/m", strtotime($value->tanggal)),
                        "origin" => "{$kode}", "status" => "unposted", "tanggal_dibuat" => $value->tanggal, "tipe" => "PA",
                        "tanggal_posting" => date("Y-m-d H:i:s"), "reff_note" => ($key + 1) . " Dari " . count($data["list"])];

                    $asetJurnal[] = [
                        "id_aset" => $check->id,
                        "aset_tetap_no" => $kode,
                        "penyusutan_thn" => $value->tahunan,
                        "penyusutan_bulan" => $value->bulanan,
                        "penyusutan_tgl" => $value->tanggal,
                        "no_jurnal" => $jurnal
                    ];

                    $jurnalData[] = $jd;
                    $jdt = [];
                    $jdt[] = [
                        "kode" => $jurnal,
                        "nama" => "{$check->nama}",
                        "reff_note" => "Beban Penyusutan",
                        "partner" => 0,
                        "kode_coa" => $check->akun_bbn_penyusutan,
                        "posisi" => "D",
                        "nominal_curr" => round($value->bulanan, 2),
                        "kurs" => 1,
                        "kode_mua" => "IDR",
                        "nominal" => round($value->bulanan, 2),
                        "row_order" => (count($jurnalDataDetail) + 1)
                    ];
                    $jdt[] = [
                        "kode" => $jurnal,
                        "nama" => "{$check->nama}",
                        "reff_note" => "Akumulasi Penyusutan",
                        "partner" => 0,
                        "kode_coa" => $check->akun_akum_penyusutan,
                        "posisi" => "C",
                        "nominal_curr" => round($value->bulanan, 2),
                        "kurs" => 1,
                        "kode_mua" => "IDR",
                        "nominal" => round($value->bulanan, 2),
                        "row_order" => (count($jurnalDataDetail) + 2)
                    ];
                    $logH[] = "Header -> " . logArrayToString("; ", $jd);
                    $logD [] = "\nDETAIL -> " . logArrayToString("; ", $jdt);
                    $jurnalDataDetail = array_merge($jurnalDataDetail, $jdt);
                }
            }
            $model->update(["status" => "done", "text_generate" => json_encode($textGen)]);
            $model->setTables("acc_jurnal_entries")->saveBatch($jurnalData);
            $model->setTables("acc_jurnal_entries_items")->saveBatch($jurnalDataDetail);
            $model->setTables("acc_aset_tetap_jurnal")->saveBatch($asetJurnal);
            if (!$this->_module->finishTransaction()) {
                throw new \Exception('Gagal Menyimpan Data', 500);
            }
            $this->_module->gen_history_new($sub_menu, $kode, "edit", "Update status ke done", $username);
            foreach ($jurnals as $key => $jurnal) {
                $this->_module->gen_history_new("jurnal_entries", $jurnal, "create", $logH[$key] . $logD[$key], $username);
            }
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

    protected function _generate(): array {
        try {
            $metode = $this->input->post("metode");
            $tglPakai = $this->input->post("tgl_pakai");
            $harga = str_replace(",", "", $this->input->post("harga"));
            $sisa = str_replace(",", "", $this->input->post("sisa"));
            $tarif = $this->input->post("tarif");
            $umur = $this->input->post("umur");
            $nominalTahunan = ($harga - $sisa) * ($tarif / 100);
            $nominalPerbulan = $nominalTahunan / 12;
            $data = [];
            $days = date("d", strtotime($tglPakai));
            $start = date("Y-m-01", strtotime($tglPakai));
            $no = 0;
            if ($metode === "tarif_garis_lurus") {
                for ($i = 1; $i <= $umur; $i++) {
                    $finish = strtotime('+1 year', strtotime($start));
                    while ($start < date("Y-m-d", $finish)) {
                        $no++;
                        $data[] = (object) [
                                    "no" => $no,
                                    "tahun" => $i,
                                    "tahun_penyu" => $nominalTahunan,
                                    "tanggal" => date("Y-m-{$days}", strtotime($start)),
                                    "bulan_penyu" => $nominalPerbulan
                        ];
                        $start = date("Y-m-d", strtotime('+1 month', strtotime($start)));
                    }
                }
                return $data;
            } else {
                $awal = ($harga - $sisa);
                $datas = [];
                for ($i = 1; $i <= $umur; $i++) {
                    $finish = strtotime('+1 year', strtotime($start));
                    $awalFormat = number_format($awal, 2, ",", ".");
                    $textAwal = "Nilai Buku Awal : {$awalFormat}";
                    $penyusutan = $awal * ($tarif / 100);
                    $penyusutanFormat = number_format($penyusutan, 2, ",", ".");
                    $textPenyusutan = "Penyusutan : {$awalFormat} x {$tarif}% = {$penyusutanFormat}";
                    $hasil = $awal - $penyusutan;
                    $hrgBulanan = $penyusutan / 12;
                    $textNilaiAkhir = "Nilai Buku Akhir : {$awalFormat} - {$penyusutanFormat} = " . number_format($hasil, 2, ",", ".");
                    $text = "<div class='list-item-div'>{$textAwal}</div><div class='list-item-div'>$textPenyusutan</div><div class='list-item-div'>$textNilaiAkhir</div>";
                    if ($i == $umur) {
                        $text = "<div class='list-item-div'>Penyusutan : {$awalFormat} (Disusutkan seluruhnya)</div>";
                        $hasil = 0;
                        $penyusutan = $awal;
                        $hrgBulanan = $penyusutan / 12;
                    }
                    $data[] = (object) [
                                "text" => $text,
                    ];
                    while ($start < date("Y-m-d", $finish)) {
                        $datas[] = (object) [
                                    "awal" => $awal,
                                    "tahunan" => $penyusutan,
                                    "akhir" => $hasil,
                                    "tanggal" => date("Y-m-{$days}", strtotime($start)),
                                    "bulanan" => $hrgBulanan
                        ];
                        $start = date("Y-m-d", strtotime('+1 month', strtotime($start)));
                    }
                    $awal = $hasil;
                }
//                $html = $this->load->view('accounting/v_aset_tetap_penyusutan_menurun', ["data" => $data, "umur" => $umur, "list" => $datas], true);
                return ["data" => $data, "umur" => $umur, "list" => $datas];
            }
            return [];
        } catch (Exception $ex) {
            throw $ex;
        }
    }
}
