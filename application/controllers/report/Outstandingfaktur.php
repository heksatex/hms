<?php
defined('BASEPATH') or exit('No Direct Script Acces Allowed');

require FCPATH . 'vendor/autoload.php';
require_once APPPATH . '/third_party/vendor/autoload.php';
/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of Outstandingfaktur
 *
 * @author RONI
 */
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Mpdf\Mpdf;

class Outstandingfaktur extends MY_Controller {

    //put your code here
    public function __construct() {
        parent::__construct();
        $this->is_loggedin();
        $this->load->model("_module");
        $this->load->model("m_global");
    }
    
    public function index()
    {
        $id_dept        = 'ROUTSFP';
        $data['id_dept'] = $id_dept;
        $this->load->view('report/acc/v_outstanding_faktur', $data);
    }
}
