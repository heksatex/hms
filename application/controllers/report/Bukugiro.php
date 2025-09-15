<?php

defined('BASEPATH') or exit('No Direct Script Acces Allowed');

require_once APPPATH . '/third_party/vendor/autoload.php';
/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of Bukugiro
 *
 * @author RONI
 */
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Bukugiro extends MY_Controller {

    //put your code here
    public function __construct() {
        parent::__construct();
        $this->is_loggedin();
        $this->load->model("_module");
        $this->load->model("m_global");
    }

    protected function _query() {
        try {
            $this->form_validation->set_rules([
                [
                    'field' => 'tanggal',
                    'label' => 'Periode',
                    'rules' => ['trim', 'required'],
                    'errors' => [
                        'required' => '{field} Harus dipilih'
                    ]
                ],
                [
                    'field' => 'kode_coa',
                    'label' => 'Bank',
                    'rules' => ['trim', 'required'],
                    'errors' => [
                        'required' => '{field} Harus dipilih'
                    ]
                ]
            ]);
            if ($this->form_validation->run() == FALSE) {
                throw new \Exception(array_values($this->form_validation->error_array())[0], 500);
            }
            $coa = $this->input->post("kode_coa");
            $tanggal = $this->input->post("tanggal");
            $tanggals = explode(" - ", $tanggal);
            $model = new $this->m_global;

            $model->setTables("acc_bank_masuk bm")->setJoins("acc_bank_masuk_detail bmd", "bm.id = bank_masuk_id")
                    ->setSelects(["bm.no_bm as no_bukti,date(bm.tanggal) as tanggal,'D' as posisi,nominal,bmd.kode_coa"])
                    ->setSelects(["if(bmd.uraian = '',transinfo,bmd.uraian) as uraian"])->setWheres(["bmd.no_bg" => "","status"=>"confirm"]);
            if (count($tanggals) > 1) {
                $model->setWheres(["date(bm.tanggal) >=" => date("Y-m-d", strtotime($tanggals[0])), "date(bm.tanggal) <=" => date("Y-m-d", strtotime($tanggals[1]))]);
            }
            if ($coa !== "") {
                $model->setWheres(["bm.kode_coa" => $coa]);
            }
            $queryBankMasuk = $model->getQuery();

            $model->setTables("acc_bank_keluar bm")->setJoins("acc_bank_keluar_detail bmd", "bm.id = bank_keluar_id")
                    ->setSelects(["bm.no_bk as no_bukti,date(bm.tanggal) as tanggal,'D' as posisi,nominal,bmd.kode_coa"])
                    ->setSelects(["if(bmd.uraian = '',transinfo,bmd.uraian) as uraian"])->setWheres(["bmd.no_bg" => "","status"=>"confirm"]);
            if (count($tanggals) > 1) {
                $model->setWheres(["date(bm.tanggal) >=" => date("Y-m-d", strtotime($tanggals[0])), "date(bm.tanggal) <=" => date("Y-m-d", strtotime($tanggals[1]))]);
            }
            if ($coa !== "") {
                $model->setWheres(["bm.kode_coa" => $coa]);
            }
            $queryBankKeluar = $model->getQuery();

            $model->setTables("acc_giro_keluar bm")->setJoins("acc_giro_keluar_detail bmd", "bm.id = giro_keluar_id")
                    ->setSelects(["bm.no_gk as no_bukti,date(bm.tanggal) as tanggal,'D' as posisi,nominal,bmd.kode_coa"])
                    ->setSelects(["transinfo as as  uraian"])->setWheres(["bmd.no_bg" => "","status"=>"confirm"]);
            if (count($tanggals) > 1) {
                $model->setWheres(["date(bm.tanggal) >=" => date("Y-m-d", strtotime($tanggals[0])), "date(bm.tanggal) <=" => date("Y-m-d", strtotime($tanggals[1]))]);
            }
            if ($coa !== "") {
                $model->setWheres(["bm.kode_coa" => $coa]);
            }
            $quergiroKeluar = $model->getQuery();

            $table = "({$queryBankMasuk} union all {$queryBankKeluar} union all {$quergiroKeluar}) as buku_giro";
            $model->setTables($table)->setJoins("acc_coa", "acc_coa.kode_coa = buku_giro.kode_coa", "left")
                    ->setSelects(["no_bukti,tanggal,uraian,posisi,nominal,concat(buku_giro.kode_coa,'-',acc_coa.nama) as coa"]);
            return $model;
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    public function index() {
        $data['id_dept'] = 'BACG';
        $model = new $this->m_global;
        $data["coa"] = $model->setTables("acc_coa")->setWheres(["jenis_transaksi" => "bank"])->getData();
        $this->load->view('report/acc/v_buku_giro', $data);
    }

    public function search() {
        try {
            $model = $this->_query();
        } catch (Exception $ex) {
            
        }
    }
}
