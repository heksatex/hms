<?php

defined('BASEPATH') OR EXIT('No Direct Script Acces Allowed');
/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of Coa
 *
 * @author RONI
 */
class Coa extends MY_Controller {

    //put your code here
    public function __construct() {
        parent:: __construct();
        $this->is_loggedin();
        $this->load->model("m_global");
        $this->load->model('_module');
        $this->load->driver('cache', array('adapter' => 'file'));
    }

    public function index() {
        $data['id_dept'] = 'ACCCOA';
        $search = $this->input->get("search");
        if (!$datas = $this->cache->get("coa_{$search}")) {
        $model = new $this->m_global;
            $datas = [];
            $model->setTables("acc_coa")->setSearch(["kode_coa", "nama"])
                    ->setSelects(["kode_coa,parent,nama,saldo_normal,saldo_valas,saldo_awal,level"]);

            if (!empty($search)) {
                $_POST['search']['value'] = $search;
            }
            $coa = $model->setOrder(["kode_coa" => "desc"])->getData();
            foreach ($coa as $key => $value) {
                $datas = $this->array_insert_assoc($datas, ["k{$value->kode_coa}" => $value], "k{$value->parent}");
            }
            $this->cache->save("coa_{$search}", $datas, 300);
        }

        $data["coa"] = $datas;
        $this->load->view('accounting/v_coa', $data);
    }

    public function add() {
        $data['id_dept'] = 'ACCCOA';
        $model = new $this->m_global;
        $data["coa"] = $model->setTables("acc_coa")->setSelects(["kode_coa,parent,nama"])
                        ->setOrder(["parent" => "asc", "kode_coa" => "asc"])->setWheres(["level" => "1"])->getData();
        $this->load->view('accounting/v_coa_add', $data);
    }

    public function edit($id) {
        try {
            $data['id_dept'] = 'ACCCOA';
            $data["id"] = $id;
            $kode = decrypt_url($id);
            $model = new $this->m_global;
            $data["detail"] = $model->setTables("acc_coa ac")->setJoins("acc_coa acc", "ac.parent = acc.kode_coa", "left")
                            ->setSelects(["ac.*", "acc.kode_coa as kc,acc.nama as nc,acc.level as acc_level"])
                            ->setWheres(["ac.kode_coa" => $kode])->getDetail();
            if (!$data['detail']) {
                throw new \Exception();
            }
            $data['mms'] = $this->_module->get_data_mms_for_log_history('ACCCOA');
            $data["curr"] = $model->setTables("currency_kurs")->setSelects(["currency"])->getData();
            $this->load->view('accounting/v_coa_edit', $data);
        } catch (Exception $ex) {
            show_404();
        }
    }

    public function get_level() {
        try {
            $model = new $this->m_global;
            $level = $this->input->get("level");
//            $parent = $this->input->get("parent");
            $model->setTables("acc_coa")->setSelects(["kode_coa", "nama"])->setSearch(["kode_coa", "nama"])->setWheres(["level" => $level])->setOrder(["kode_coa" => "asc"]);
            $_POST['length'] = 50;
            $_POST['start'] = 0;
            if ($this->input->get('search') !== "") {
                $_POST['search']['value'] = $this->input->get('search');
            }
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array("data" => $model->getData())));
        } catch (Exception $ex) {
            
        }
    }

    public function get_coa() {
        try {
            $model = new $this->m_global;
            $parent = $this->input->get("parent");
            $model->setTables("acc_coa")->setSelects(["kode_coa", "nama"])->setSearch(["kode_coa", "nama"])->setWheres(["parent" => $parent])->setOrder(["kode_coa" => "asc"]);
            $_POST['length'] = 50;
            $_POST['start'] = 0;
            if ($this->input->get('search') !== "") {
                $_POST['search']['value'] = $this->input->get('search');
            }
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array("data" => $model->getData())));
        } catch (Exception $ex) {
            
        }
    }

    public function update($id) {
        try {
            $kode = decrypt_url($id);
            $sub_menu = $this->uri->segment(2);
            $username = $this->session->userdata('username');
            $this->form_validation->set_rules([
                [
                    'field' => 'kode_coa',
                    'label' => 'Kode Coa',
                    'rules' => ['trim', 'required'],
                    'errors' => [
                        'required' => '{field} Pada Item harus diisi'
                    ]
                ],
                [
                    'field' => 'nama_coa',
                    'label' => 'Nama COA',
                    'rules' => ['trim', 'required'],
                    'errors' => [
                        'required' => '{field} Pada Item harus diisi'
                    ]
                ],
                [
                    'field' => 'saldo_normal',
                    'label' => 'Saldo Normal',
                    'rules' => ['trim', 'required'],
                    'errors' => [
                        'required' => '{field} Haris dipilih'
                    ]
                ],
                [
                    'field' => 'saldo_valas',
                    'label' => 'Saldo Valas',
                    'rules' => ['trim', 'required', 'regex_match[/^-?\d*(,\d{3})*\.?\d*$/]'],
                    'errors' => [
                        'required' => '{field} Pada Item harus diisi',
                        "regex_match" => "{field} harus berupa number / desimal"
                    ]
                ],
                [
                    'field' => 'saldo_awal',
                    'label' => 'Sado Awal',
                    'rules' => ['trim', 'required', 'regex_match[/^-?\d*(,\d{3})*\.?\d*$/]'],
                    'errors' => [
                        'required' => '{field} Pada Item harus diisi',
                        "regex_match" => "{field} harus berupa number / desimal"
                    ]
                ]
            ]);
            if ($this->form_validation->run() == FALSE) {
                throw new \Exception(array_values($this->form_validation->error_array())[0], 500);
            }
            $namacoa = $this->input->post("nama_coa");
            $saldonormal = $this->input->post("saldo_normal");
            $saldovalas = $this->input->post("saldo_valas");
            $saldoawal = $this->input->post("saldo_awal");
            $curr = $this->input->post("curr");
            $jt = $this->input->post("jenis_trans");
            $parent = $this->input->post("parent");
            $update = [
                "parent" => $parent,
                "nama" => $namacoa,
                "saldo_normal" => $saldonormal,
                "saldo_valas" => str_replace(",", "", $saldovalas),
                "saldo_awal" => str_replace(",", "", $saldoawal),
                "curr" => $curr,
                "jenis_transaksi" => $jt,
                "status" => $this->input->post("status")
            ];
            $model = new $this->m_global;
            $model->setTables("acc_coa")->setWheres(["kode_coa" => $kode])->update($update);
            $this->cache->delete("coa_");
            $this->_module->gen_history_new($sub_menu, $kode, 'edit', "DATA -> " . logArrayToString("; ", $update), $username);
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success')));
        } catch (Exception $ex) {
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }

    public function simpan() {
        try {
            $this->form_validation->set_rules([
                [
                    'field' => 'kode_coa',
                    'label' => 'Kode Coa',
                    'rules' => ['trim', 'required', 'is_unique[acc_coa.kode_coa]'],
                    'errors' => [
                        'required' => '{field} Pada Item harus diisi',
                        'is_unique' => '%s. sudah ada'
                    ]
                ],
                [
                    'field' => 'nama_coa',
                    'label' => 'Nama COA',
                    'rules' => ['trim', 'required'],
                    'errors' => [
                        'required' => '{field} Pada Item harus diisi'
                    ]
                ],
                [
                    'field' => 'saldo_normal',
                    'label' => 'Saldo Normal',
                    'rules' => ['trim', 'required'],
                    'errors' => [
                        'required' => '{field} Haris dipilih'
                    ]
                ]
            ]);
            if ($this->form_validation->run() == FALSE) {
                throw new \Exception(array_values($this->form_validation->error_array())[0], 500);
            }
            $sub_menu = $this->uri->segment(2);
            $username = $this->session->userdata('username');
            $kodecoa = $this->input->post("kode_coa");
            $namacoa = $this->input->post("nama_coa");
            $saldonormal = $this->input->post("saldo_normal");
            $level1 = $this->input->post("level_1");
            $level2 = $this->input->post("level_2");
            $level3 = $this->input->post("level_3");
            $level4 = $this->input->post("level_4");
            $parent = 0;
            $level = 0;
            switch (false) {
                case empty($level4):
                    $level += 5;
                    $parent = $level4;
                    break;
                case empty($level3):
                    $level += 4;
                    $parent = $level3;
                    break;
                case empty($level2):
                    $level += 3;
                    $parent = $level2;
                    break;
                case empty($level1):
                    $level += 2;
                    $parent = $level1;
                    break;

                default:
                    $level += 1;
                    break;
            }
            $model = new $this->m_global;
            $insert = [
                "kode_coa" => $kodecoa,
                "parent" => $parent,
                "level" => $level,
                "nama" => $namacoa,
                "saldo_normal" => $saldonormal,
                "status" => "naktif"
            ];
            $model->setTables("acc_coa")->save($insert);
            $url = site_url("accounting/coa/edit/" . encrypt_url($kodecoa));
            $this->cache->delete("coa_");
            $this->_module->gen_history_new($sub_menu, $kodecoa, 'create', "DATA -> " . logArrayToString("; ", $insert), $username);
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success', 'url' => $url)));
        } catch (Exception $ex) {
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }

    protected function array_insert_assoc($array, $new_item, $after_key = null) {
        if ($after_key === null) {
            return array_merge($new_item, $array); // Insert at the beginning
        } else {
            $keys = array_keys($array);
            $index = array_search($after_key, $keys);
            if ($index !== FALSE) {
                // Slice and merge around the key index
                $before_slice = array_slice($array, 0, $index + 1, TRUE);
                $after_slice = array_slice($array, $index + 1, NULL, TRUE);
                return array_merge($before_slice, $new_item, $after_slice);
            } else {
                return array_merge($new_item, $array);
            }
        }
    }
}
