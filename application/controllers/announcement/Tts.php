<?php

defined('BASEPATH') OR EXIT('No Direct Script Acces Allowed');
/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */

class Tts extends MY_Controller {

    public function __construct() {
        parent:: __construct();
        $this->is_loggedin();
        $this->load->model('_module');
        $this->load->model("m_global");
        $this->load->library("upload");
        $this->load->library("ssmlaudio");
        $this->load->helper('file');
        $this->load->driver('cache', array('adapter' => 'file'));
    }

    protected $ipESP = "10.10.0.188:8080";
    protected $tokenESP = "AA123";

    public function index() {
        $data['id_dept'] = 'ANNTTS';
        $data['user'] = (object) $this->session->userdata('nama');
        $this->load->view('announcement/v_tts', $data);
    }

    public function add() {
//        $ip = $_SERVER['REMOTE_ADDR']; // Mengambil IP pengunjung
//        
//        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) || $ip == "10.10.0.7") {
//            $data["ip_esp"] = "157.20.244.218:8841";
//        } else {
//            $data["ip_esp"] = $this->ipESP;
//        }
        $model = new $this->m_global;
        $wsIP = $model->setTables("setting")->setWheres(["setting_name" => "ws_sound_public", "status" => "1"])->setSelects(["value"])->getDetail();
        $wsToken = $model->setWheres(["setting_name" => "ws_sound_token", "status" => "1"], true)->setSelects(["value"])->getDetail();

//        $data["ip_esp"] = $this->ipESP;
        $data["ip_esp"] = $wsIP->value;
        $data['id_dept'] = 'ANNTTS';
        $data["token_esp"] = $wsToken->value;
        $data['user'] = (object) $this->session->userdata('nama');
        $this->load->view('announcement/v_tts_add_1', $data);
    }

    public function save() {
        try {
            $this->form_validation->set_rules([
                [
                    'field' => 'text_pesan',
                    'label' => 'Pesan',
                    'rules' => ['trim', 'required'],
                    'errors' => [
                        'required' => '{field} Harus dipilih'
                    ]
                ]
            ]);
            if ($this->form_validation->run() == FALSE) {
                throw new \Exception(array_values($this->form_validation->error_array())[0], 500);
            }
            $text = $this->input->post("text_pesan");
            $perulangan = $this->input->post("perulangan");
            $display = $this->input->post("display");
            $jenisSuara = $this->input->post("jenis_suara");
            $speedSuara = $this->input->post("speed_suara");
            $efek = $this->input->post("efek");
            $insertData = [
                "text" => $text,
                "pengulangan" => $perulangan,
                "display" => $display,
                "jenis_suara" => $jenisSuara,
                "speed_suara" => $speedSuara,
                "efek" => $efek,
                "status" => "done",
                "created_at" => date("Y-m-d H:i:s")
            ];
            $model = new $this->m_global;
            $model->setTables("text_to_speech")->save($insertData);
            $txt = strip_tags($text);

            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'On Progress', 'icon' => 'fa fa-check', 'type' => 'success', "text" => $txt, "timeDisplay" => $display)));
        } catch (Exception $ex) {
            $this->_module->rollbackTransaction();
            $this->output->set_status_header(500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }

    public function list() {
        try {
            $model = new $this->m_global;
            $model->setTables("text_to_speech")
                    ->setSearch(["text"])
                    ->setOrder(["created_at"=>"desc"])
                    ->setOrders([null, "text", "pengulangan", "display", "jenis_suara", "created_at", null])
                    ->setJoins("mst_status", "mst_status.kode = text_to_speech.status", "left");
            $no = $_POST['start'];
            $data = array();
            foreach ($model->getData() as $field) {
                $no++;
                $data [] = [
                    $no,
                    $field->text,
                    "{$field->pengulangan}x",
                    "{$field->display}s",
                    $field->jenis_suara,
                    date("Y-m-d H:i", strtotime($field->created_at)),
                    $field->nama_status
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

    public function save_file() {
        try {
            if ($_FILES['fl']['size'] !== 0) {
                $url = "dist/storages/test/sound";
                if (!is_dir(FCPATH . $url)) {
                    mkdir(FCPATH . $url, 0775, TRUE);
                }

                $config["upload_path"] = "./{$url}";
                $config["allowed_types"] = "*";
                $config["file_name"] = "test";
                $this->upload->initialize($config);

                if ($this->upload->do_upload("fl")) {
                    $this->output->set_status_header(200)
                            ->set_content_type('application/json', 'utf-8')
                            ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success')));
                }
                if (!$this->upload->do_upload("fl")) {
                    $error = $this->upload->display_errors();
                      throw new \Exception(json_encode($error), 500);
                }
            }
            throw new \Exception("failed", 500);
        } catch (Exception $ex) {
            $this->_module->rollbackTransaction();
            $this->output->set_status_header(500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }

//    $teksHtml = '<say-as interpret-as="characters">adasd</say-as>';
//$teksBersih = strip_tags($teksHtml);
}
