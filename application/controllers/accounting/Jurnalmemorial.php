<?php

defined('BASEPATH') or exit('No Direct Script Acces Allowed');
/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of Jurnalmemorial
 *
 * @author RONI
 */
class Jurnalmemorial extends MY_Controller {

    //put your code here
    protected $data, $tanggals, $filter, $jurnal;
    protected $jm = [
        "pen_kb" => "Penerimaan Kas Besar",
        "peng_kb" => "Pengeluaran Kas Besar",
        "pen_b" => "Penerimaan Bank",
        "peng_b" => "Pengeluaran Bank",
        "pen_g" => "Penerimaan Giro",
        "peng_g" => "Pengeluaran Giro",
        "pen_kv" => "Penerimaan Kas Valas",
        "peng_kv" => "Pengeluaran Kas Valas",
        "pel_p" => "Pelunasan Piutang",
        "pel_h" => "Pelunasan Hutang",
        "pemb" => "Pembelian",
        "penj" => "Penjualan",
    ];

    public function __construct() {
        parent:: __construct();
        $this->is_loggedin();
        $this->load->model("m_global");
        $this->load->model('_module');
        $this->load->library('excelexp/Memorialpenkasbesar', null, 'penkasbesar');
        $this->load->library('excelexp/Memorialpengkasbesar', null, 'pengkasbesar');
        $this->load->library('excelexp/Memorialpenbank', null, 'penbank');
        $this->load->library('excelexp/Memorialpengbank', null, 'pengbank');
        $this->load->library('excelexp/Memorialpengiro', null, 'pengiro');
        $this->load->library('excelexp/Memorialpenggiro', null, 'penggiro');
        $this->load->library('excelexp/Memorialpenkasvalas', null, 'penkasvalas');
        $this->load->library('excelexp/Memorialpengkasvalas', null, 'pengkasvalas');
        $this->load->library('excelexp/Memorialpelpiutang', null, 'pelpiutang');
        $this->load->library('excelexp/Memorialpelhutang', null, 'pelhutang');
        $this->load->library('excelexp/Memorialpembelian', null, 'pemb');
        $this->load->library('excelexp/Memorialpenjualan', null, 'penj');
//        $this->data = new $this->m_global;
    }

    public function check_berdasarkan($str, $periode): bool {
        if (empty($str) && empty($this->input->post($periode))) {
            $this->form_validation->set_message('check_berdasarkan', 'Pilih Salah satu Tanggal dibuat / Periode');
            return false;
        }
        return true;
    }

    public function jm() {
        try {
            $jm = $this->input->get("jm");
            $curr = $this->input->get("curr");
            $data["jm"] = $curr == "" ? $jm : "{$jm}_v";
            $data["filter"] = $this->input->get("filter");
            $data["header"] = "{$data['jm']}_{$data['filter']}";
            $html = $this->load->view('accounting/jm/v_header', $data, true);
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success', "data" => $html)));
        } catch (Exception $ex) {
            $this->output->set_status_header(500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success', "data" => "")));
        }
    }

    public function index() {
        $data['id_dept'] = '';
        $data['jurnal'] = $this->jm;
        $model = new $this->m_global();
        $data["bank"] = $model->setTables("acc_coa")->setWheres(["jenis_transaksi" => "bank", "level" => 5])->getData();
        $this->load->view('accounting/v_rpt_jurnal_memorial', $data);
    }

    public function search() {
        $validation = [
            [
                'field' => 'jurnal',
                'label' => 'Jurnal',
                'rules' => ['required'],
                'errors' => [
                    'required' => '{field} Harus dipilih',
                ]
            ],
            [
                'field' => 'periode',
                'label' => 'periode',
                'rules' => ['required'],
                'errors' => [
                    'required' => '{field} Harus dipilih',
                ]
            ]
        ];
        try {
            $this->form_validation->set_rules($validation);
            if ($this->form_validation->run() == FALSE) {
                throw new \Exception(array_values($this->form_validation->error_array())[0], 500);
            }
            $tanggal = $this->input->post("periode");
            $this->tanggals = explode(" - ", $tanggal);
            $this->filter = $this->input->post("filter");
            $this->jurnal = $this->input->post("jurnal");
            $fbank = $this->input->post("fbank") ?? "";
            $curr = $this->input->post("curr");
            $view = "";
            $curr == "" ? "" : "_v";
            $data["fbank"] = $fbank;
            $data["jurnal"] = $this->jm[$this->jurnal];
            $data["periode"] = $tanggal;
            $data["tanggals"] = $this->tanggals;
            $data["filter"] = $this->filter;
            $model = new $this->m_global;
            switch ($this->jurnal) {
                case "pen_kb":
                    $data["data"] = $this->penkasbesar->_data($model, $data);
                    $view = $this->load->view("accounting/jm/v_kas_besar_{$this->filter}", $data, true);
                    break;
                case "peng_kb":
                    $data["data"] = $this->pengkasbesar->_data($model, $data);
                    $view = $this->load->view("accounting/jm/v_kas_besar_keluar_{$this->filter}", $data, true);
                    break;
                case "pen_b":
                    $data["data"] = $this->penbank->_data($model, $data);
                    $view = $this->load->view("accounting/jm/v_penerimaan_bank_{$this->filter}", $data, true);
                    break;
                case "peng_b":
                    $data["data"] = $this->pengbank->_data($model, $data);
                    $view = $this->load->view("accounting/jm/v_pengeluaran_bank_{$this->filter}", $data, true);
                    break;
                case "pen_g":
                    $data["data"] = $this->pengiro->_data($model, $data);
                    $view = $this->load->view("accounting/jm/v_penerimaan_giro_{$this->filter}", $data, true);
                    break;
                case "peng_g":
                    $data["data"] = $this->penggiro->_data($model, $data);
                    $view = $this->load->view("accounting/jm/v_pengiriman_giro_{$this->filter}", $data, true);
                    break;

                case "pen_kv":
                    $data["data"] = $this->penkasvalas->_data($model, $data);
                    $view = $this->load->view("accounting/jm/v_penerimaan_kasvalas_{$this->filter}", $data, true);
                    break;
                case "peng_kv":
                    $data["data"] = $this->pengkasvalas->_data($model, $data);
                    $view = $this->load->view("accounting/jm/v_pengeluaran_kasvalas_{$this->filter}", $data, true);
                    break;
                case "pel_p":
                    $data["data"] = $this->pelpiutang->_data($model, $data);
                    if (empty($curr))
                        $view = $this->load->view("accounting/jm/v_pelunasan_piutang_{$this->filter}", $data, true);
                    else
                        $view = $this->load->view("accounting/jm/v_pelunasan_piutang_v_{$this->filter}", $data, true);
                    break;
                case "pel_h":
                    $data["data"] = $this->pelhutang->_data($model, $data);
                    if (empty($curr))
                        $view = $this->load->view("accounting/jm/v_pelunasan_hutang_{$this->filter}", $data, true);
                    else
                        $view = $this->load->view("accounting/jm/v_pelunasan_hutang_v_{$this->filter}", $data, true);
                    break;
                case "pemb":
                    $data["data"] = $this->pemb->_data($model, $data);
                    $view = $this->load->view("accounting/jm/v_pembelian_{$this->filter}", $data, true);
                    break;
                case "penj":
                    $data["data"] = $this->penj->_data($model, $data);
                    $view = $this->load->view("accounting/jm/v_penjualan_{$this->filter}", $data, true);
                    break;
                default://
                    break;
            }
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success', "data" => $view)));
        } catch (Exception $ex) {
            $this->output->set_status_header(($ex->getCode()) ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }

    public function export() {
        try {
            $validation = [
                [
                    'field' => 'jurnal',
                    'label' => 'Jurnal',
                    'rules' => ['required'],
                    'errors' => [
                        'required' => '{field} Harus dipilih',
                    ]
                ],
                [
                    'field' => 'periode',
                    'label' => 'periode',
                    'rules' => ['callback_check_berdasarkan[tanggal_dibuat]'],
                ]
            ];

            $this->form_validation->set_rules($validation);
            if ($this->form_validation->run() == FALSE) {
                throw new \Exception(array_values($this->form_validation->error_array())[0], 500);
            }

            $tanggal = $this->input->post("periode");
            $this->tanggals = explode(" - ", $tanggal);
            $this->filter = $this->input->post("filter");
            $this->jurnal = $this->input->post("jurnal");
            $curr = $this->input->post("curr");
            $view = "";
            $filename = "";
            $model = new $this->m_global;
            $data["jurnal"] = $this->jm[$this->jurnal];
            $data["periode"] = $tanggal;
            $data["tanggals"] = $this->tanggals;
            $data["filter"] = $this->filter;
            $data["curr"] = $curr;
            $data["fbank"] = $this->input->post("fbank");
            switch ($this->jurnal) {
                case "pen_kb":
                    $datas = $this->penkasbesar->_data($model, $data);
                    $data["kredit"] = $datas["kredit"];
                    $data["debit"] = $datas["debit"];
                    if ($this->filter === "global")
                        $view = $this->penkasbesar->_global($data, $filename);
                    else
                        $view = $this->penkasbesar->_detail($data, $filename);
                    break;
                case "peng_kb":
                    $datas = $this->pengkasbesar->_data($model, $data);
                    $data["kredit"] = $datas["kredit"];
                    $data["debit"] = $datas["debit"];
                    if ($this->filter === "global")
                        $view = $this->pengkasbesar->_global($data, $filename);
                    else
                        $view = $this->pengkasbesar->_detail($data, $filename);
                    break;
                case "pen_b":
                    $datas = $this->penbank->_data($model, $data);
                    $data = array_merge($data, $datas);
                    if ($this->filter === "global")
                        $view = $this->penbank->_global($data, $filename);
                    else if ($this->filter === "detail")
                        $view = $this->penbank->_detail($data, $filename);
                    else
                        $view = $this->penbank->_detail_2($data, $filename);
                    break;
                case "peng_b":
                    $datas = $this->pengbank->_data($model, $data);
                    $data = array_merge($data, $datas);
                    if ($this->filter === "global")
                        $view = $this->pengbank->_global($data, $filename);
                    else if ($this->filter === "detail")
                        $view = $this->pengbank->_detail($data, $filename);
                    else
                        $view = $this->pengbank->_detail_2($data, $filename);
                    break;

                case "pen_g":
                    $datas = $this->pengiro->_data($model, $data);
                    $data = array_merge($data, $datas);
                    if ($this->filter === "global")
                        $view = $this->pengiro->_global($data, $filename);
                    else if ($this->filter === "detail")
                        $view = $this->pengiro->_detail($data, $filename);
                    else
                        $view = $this->pengiro->_detail_2($data, $filename);
                    break;
                case "peng_g":
                    $datas = $this->penggiro->_data($model, $data);
                    $data = array_merge($data, $datas);
                    if ($this->filter === "global")
                        $view = $this->penggiro->_global($data, $filename);
                    else if ($this->filter === "detail")
                        $view = $this->penggiro->_detail($data, $filename);
                    else
                        $view = $this->penggiro->_detail_2($data, $filename);
                    break;
                case "pen_kv":
                    $datas = $this->penkasvalas->_data($model, $data);
                    $data = array_merge($data, $datas);
                    if ($this->filter === "global")
                        $view = $this->penkasvalas->_global($data, $filename);
                    else
                        $view = $this->penkasvalas->_detail($data, $filename);
                    break;
                case "peng_kv":
                    $datas = $this->pengkasvalas->_data($model, $data);
                    $data = array_merge($data, $datas);
                    if ($this->filter === "global")
                        $view = $this->pengkasvalas->_global($data, $filename);
                    else
                        $view = $this->pengkasvalas->_detail($data, $filename);
                    break;
                case "pel_p":
                    $datas = $this->pelpiutang->_data($model, $data);
                    $data = array_merge($data, $datas);
                    if ($this->filter === "global")
                        $view = $this->pelpiutang->_global($data, $filename);
                    else if ($this->filter === "detail")
                        $view = $this->pelpiutang->_detail($data, $filename);
                    else
                        $view = $this->pelpiutang->_detail_2($data, $filename);
                    break;
                case "pel_h":
                    $datas = $this->pelhutang->_data($model, $data);
                    $data = array_merge($data, $datas);
                    if ($this->filter === "global")
                        $view = $this->pelhutang->_global($data, $filename);
                    else if ($this->filter === "detail")
                        $view = $this->pelhutang->_detail($data, $filename);
                    else
                        $view = $this->pelhutang->_detail_2($data, $filename);
                    break;
                case "pemb":
                    $datas = $this->pemb->_data($model, $data);
                    $data = array_merge($data, $datas);
                    if ($this->filter === "global")
                        $view = $this->pemb->_global($data, $filename);
                    else if ($this->filter === "detail")
                        $view = $this->pemb->_detail($data, $filename);
                    else
                        $view = $this->pemb->_detail_2($data, $filename);
                    break;
                case "penj":
                    $datas = $this->penj->_data($model, $data);
                    $data = array_merge($data, $datas);
                    if ($this->filter === "global")
                        $view = $this->penj->_global($data, $filename);
                    else if ($this->filter === "detail")
                        $view = $this->penj->_detail($data, $filename);
                    else
                        $view = $this->penj->_detail_2($data, $filename);
                    break;
                default:
                    break;
            }

            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil Export', 'icon' => 'fa fa-check', 'type' => 'success', 'text_name' => $filename, "data" => $view)));
        } catch (Exception $ex) {
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger', "data" => "")));
        }
    }
}
