<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of Recycle
 *
 * @author RONI
 */
defined('BASEPATH') or exit('No Direct Script Acces Allowed');

require_once APPPATH . '/third_party/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Settings;
use Cache\Adapter\Apcu\ApcuCachePool;
use Cache\Bridge\SimpleCache\SimpleCacheBridge;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Recycle extends MY_Controller {

    protected $submodul = [
        "TRI-HPT" => [
            "type" => "prod",
            "dept_id_tujuan" => "TRI"
        ],
        "TRI-PKG" => [
            "type" => "in",
            "dept_id_tujuan" => "GRG",
            "dept_id_mutasi" => "GRG",
            "dept_id_dari" => "TRI"
        ],
        "GRG-PDT" => [
            "type" => "in",
            "dept_id_tujuan" => "GRG",
            "dept_id_mutasi" => "GRG",
            "dept_id_dari" => "TRI"
        ],
        "GRG-PDD" => [
            "type" => "in",
            "dept_id_tujuan" => "GRG",
            "dept_id_mutasi" => "GRG",
            "dept_id_dari" => "DYE"
        ],
        "GRG-PDS" => [
            "type" => "in",
            "dept_id_tujuan" => "GRG",
            "dept_id_mutasi" => "GRG",
            "dept_id_dari" => "SET"
        ],
        "GRG-PDP" => [
            "type" => "in",
            "dept_id_tujuan" => "GRG",
            "dept_id_mutasi" => "GRG",
            "dept_id_dari" => "PAD"
        ],
        "GRG-PKD" => [
            "type" => "out",
            "dept_id_tujuan" => "DYE",
            "dept_id_mutasi" => "GRG",
            "dept_id_dari" => "GRG"
        ],
        "GRG-PKS" => [
            "type" => "out",
            "dept_id_tujuan" => "SET",
            "dept_id_mutasi" => "GRG",
            "dept_id_dari" => "GRG"
        ],
        "GRG-PKP" => [
            "type" => "out",
            "dept_id_tujuan" => "PAD",
            "dept_id_mutasi" => "GRG",
            "dept_id_dari" => "GRG"
        ],
        "GRG-PKB" => [
            "type" => "out",
            "dept_id_tujuan" => "BRS",
            "dept_id_mutasi" => "GRG",
            "dept_id_dari" => "GRG"
        ],
        "GRG-PKI" => [
            "type" => "out",
            "dept_id_tujuan" => "DYE",
            "dept_id_mutasi" => "GRG",
            "dept_id_dari" => "INS2"
        ],
        "GRG-PKG" => [
            "type" => "out",
            "dept_id_tujuan" => "GJD",
            "dept_id_mutasi" => "GRG",
            "dept_id_dari" => "GRG"
        ],
        "DF-PDG" => [
            "type" => "in",
            "dept_id_tujuan" => "DYE",
            "dept_id_mutasi" => "DYE",
            "dept_id_dari" => "GRG"
        ],
        "DF-PKI" => [
            "type" => "out",
            "dept_id_tujuan" => "INS2",
            "dept_id_mutasi" => "DYE",
            "dept_id_dari" => "DYE"
        ],
        "DF-PKG" => [
            "type" => "out",
            "dept_id_tujuan" => "GRG",
            "dept_id_mutasi" => "DYE",
            "dept_id_dari" => "DYE"
        ],
        "INS2-PDG" => [
            "type" => "in",
            "dept_id_tujuan" => "INS2",
            "dept_id_mutasi" => "INS2",
            "dept_id_dari" => "GRG"
        ],
        "INS2-PDS" => [
            "type" => "in",
            "dept_id_tujuan" => "INS2",
            "dept_id_mutasi" => "INS2",
            "dept_id_dari" => "SET"
        ],
        "INS2-PDP" => [
            "type" => "in",
            "dept_id_tujuan" => "INS2",
            "dept_id_mutasi" => "INS2",
            "dept_id_dari" => "PAD"
        ],
        "INS2-PDF" => [
            "type" => "in",
            "dept_id_tujuan" => "INS2",
            "dept_id_mutasi" => "INS2",
            "dept_id_dari" => "FIN"
        ],
        "INS2-PDFR" => [
            "type" => "in",
            "dept_id_tujuan" => "INS2",
            "dept_id_mutasi" => "INS2",
            "dept_id_dari" => "FBR"
        ],
        "INS2-PKG" => [
            "type" => "out",
            "dept_id_tujuan" => "GJD",
            "dept_id_mutasi" => "INS2",
            "dept_id_dari" => "INS2"
        ],
        "INS2-PKGG" => [
            "type" => "out",
            "dept_id_tujuan" => "GRG",
            "dept_id_mutasi" => "INS2",
            "dept_id_dari" => "INS2"
        ],
        "GJ-PDI" => [
            "type" => "out",
            "dept_id_tujuan" => "GJD",
            "dept_id_mutasi" => "INS2",
            "dept_id_dari" => "INS2"
        ]
    ];

    //put your code here
    public function __construct() {
        parent::__construct();
        $this->is_loggedin();
        $this->load->model("_module");
        $this->load->model("m_recycle");
        $this->load->library('pagination');
    }

    public function index() {
        $data['id_dept'] = 'RRC';
        $this->load->view('report/v_recycle', $data);
    }

    public function get_list_mo() {
        if (strlen(trim($this->input->get("term"))) < 4) {
            echo json_encode([]);
            return;
        }
        $data = $this->m_recycle->getListMo(trim($this->input->get("q")), "kode as id,kode as text");
        echo json_encode($data);
    }

    public function get_list_kp() {
        try {
            $mo = $this->input->get("mo");
            $corak = $this->input->get("corak");
            $condition = [
                "mph.lot LIKE" => "%" . trim($this->input->get("q")) . "%",
                "mp.dept_id" => "TRI"
            ];
            if (!(empty(trim($mo)))) {
                $condition = array_merge($condition, ["mph.kode" => $mo]);
            }
            if (!(empty(trim($corak)))) {
                $condition = array_merge($condition, ["mph.nama_produk LIKE" => "%" . $corak . "%"]);
            }

            $data = $this->m_recycle->getListKP($condition, "lot as id,lot as text");
            echo json_encode($data);
        } catch (Exception $ex) {
            echo json_encode([]);
        }
    }

    public function export() {
        try {
            $kp = $this->input->post("kp") ?? [];
//            $mo = $this->input->post("mo");
//            $corak = $this->input->post("corak");
//            $condition = [];
//            if (!(empty(trim($kp)))) {
//                $condition = array_merge($condition, ["mph.lot" => $kp]);
//            }
//            if (!(empty(trim($mo)))) {
//                $condition = array_merge($condition, ["mp.kode" => $mo]);
//            }
//            if (!(empty(trim($corak)))) {
//                $condition = array_merge($condition, ["p.nama_produk LIKE" => "%" . $corak . "%"]);
//            }
            if (is_string($kp)) {
                throw new Exception("Silahkan Pilih KP Terlebih dahulu", 500);
            }
            
            if(count($kp) < 1) {
                throw new Exception("Silahkan Pilih KP Terlebih dahulu", 500);
            }
            $condition = ['mph.lot' => $kp];
            $data = $this->m_recycle->setWhereIn($condition)
                            ->setSelect('mph.lot as kp,mph.qty,mph.uom,mph.qty2,mph.uom2,p.kode as go,nama_route,p.nama_produk,p.nama_warna,p.produk_parent,p.nama_jenis_kain')->result();
            $datas = $data;
            $queryDetail = [];
            foreach ($datas as $keys => $values) {
                foreach ($this->submodul as $key => $value) {
                    $query = $this->m_recycle->detail(array_merge($value, ["lot" => $values->kp]), true);
                    $queryDetail = array_merge($queryDetail, [$query]);
                }
                $detail = $this->m_recycle->getDetail('(' . implode(" ) UNION ALL ( ", $queryDetail) . ')');
                $data[$keys]->detail = $detail;
                $queryDetail = [];
            }

            $spreadsheet = new Spreadsheet();
            $indexJudul = 3;
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue('A' . 1, 'Report Tracking KP Recycle')->getStyle("A" . $indexJudul)->getAlignment()->setHorizontal(PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->mergeCells("A" . 1 . ":" . "D" . 1);
            $sheet->setCellValue('A' . $indexJudul, 'No');
            $sheet->setCellValue('B' . $indexJudul, 'LOT');
            $sheet->setCellValue('C' . $indexJudul, 'QTY 1 (MTR)');
            $sheet->setCellValue('D' . $indexJudul, 'UOM 1');
            $sheet->setCellValue('E' . $indexJudul, 'QTY 2');
            $sheet->setCellValue('F' . $indexJudul, 'UOM 2');
            $sheet->setCellValue('G' . $indexJudul, 'GO');
            $sheet->setCellValue('H' . $indexJudul, 'Route');
            $sheet->setCellValue('I' . $indexJudul, 'Nama Produk');
            $sheet->setCellValue('J' . $indexJudul, 'Warna');
            $sheet->setCellValue('K' . $indexJudul, 'Parent');
            $sheet->setCellValue('L' . $indexJudul, 'Jenis Kain');
            foreach (range("A", "L") as $value) {
                $spreadsheet->getActiveSheet()->mergeCells($value . $indexJudul . ":" . $value . (($indexJudul + 2)));
            }
            //tricot
            $sheet->setCellValue('M' . $indexJudul, 'Tricot')->getStyle("M" . $indexJudul)->getAlignment()->setHorizontal(PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->mergeCells("M" . $indexJudul . ":" . "AA" . $indexJudul);

            $sheet->setCellValue('M' . (($indexJudul + 2)), 'MO Tricot');
            $sheet->setCellValue('N' . (($indexJudul + 2)), 'Kode Produk Kain Tricot');
            $sheet->setCellValue('O' . (($indexJudul + 2)), 'Nama Produk Kain Tricot');
            $sheet->setCellValue('P' . (($indexJudul + 1)), 'Hasil Produksi TRI')->getStyle("P" . ($indexJudul + 1))->getAlignment()->setHorizontal(PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $spreadsheet->getActiveSheet()->mergeCells("P" . ($indexJudul + 1) . ":U" . ($indexJudul + 1));
            $sheet->setCellValue('V' . (($indexJudul + 1)), 'Pengiriman ke GRG')->getStyle("V" . ($indexJudul + 1))->getAlignment()->setHorizontal(PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->mergeCells("V" . ($indexJudul + 1) . ":AA" . ($indexJudul + 1));

            $sheet->setCellValue('P' . (($indexJudul + 2)), 'Tanggal HPH');
            $sheet->setCellValue('Q' . (($indexJudul + 2)), 'Lot');
            $sheet->setCellValue('R' . (($indexJudul + 2)), 'Qty 1');
            $sheet->setCellValue('S' . (($indexJudul + 2)), 'Uom 1');
            $sheet->setCellValue('T' . (($indexJudul + 2)), 'Qty 2');
            $sheet->setCellValue('U' . (($indexJudul + 2)), 'Uom 2');

            $sheet->setCellValue('V' . (($indexJudul + 2)), 'Tanggal HPH');
            $sheet->setCellValue('W' . (($indexJudul + 2)), 'Lot');
            $sheet->setCellValue('X' . (($indexJudul + 2)), 'Qty 1');
            $sheet->setCellValue('Y' . (($indexJudul + 2)), 'Uom 1');
            $sheet->setCellValue('Z' . (($indexJudul + 2)), 'Qty 2');
            $sheet->setCellValue('AA' . (($indexJudul + 2)), 'Uom 2');

            //GREIGE
            $sheet->setCellValue('AB' . $indexJudul, 'GREIGE')->getStyle("AB" . $indexJudul)->getAlignment()->setHorizontal(PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->mergeCells("AB" . $indexJudul . ":" . "CI" . $indexJudul);
            $sheet->setCellValue('AB' . ($indexJudul + 1), 'Penerimaan dari TRI')->getStyle("AB" . ($indexJudul + 1))->getAlignment()->setHorizontal(PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->mergeCells("AB" . ($indexJudul + 1) . ":" . "AG" . ($indexJudul + 1));
            $sheet->setCellValue('AH' . ($indexJudul + 1), 'Penerimaan dari DYE')->getStyle("AH" . ($indexJudul + 1))->getAlignment()->setHorizontal(PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->mergeCells("AH" . ($indexJudul + 1) . ":" . "AM" . ($indexJudul + 1));
            $sheet->setCellValue('AN' . ($indexJudul + 1), 'Penerimaan dari SET')->getStyle("AN" . ($indexJudul + 1))->getAlignment()->setHorizontal(PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->mergeCells("AN" . ($indexJudul + 1) . ":" . "AS" . ($indexJudul + 1));
            $sheet->setCellValue('AT' . ($indexJudul + 1), 'Penerimaan dari PAD')->getStyle("AT" . ($indexJudul + 1))->getAlignment()->setHorizontal(PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->mergeCells("AT" . ($indexJudul + 1) . ":" . "AY" . ($indexJudul + 1));
            $sheet->setCellValue('AZ' . ($indexJudul + 1), 'Pengiriman ke DYE')->getStyle("AZ" . ($indexJudul + 1))->getAlignment()->setHorizontal(PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->mergeCells("AZ" . ($indexJudul + 1) . ":" . "BE" . ($indexJudul + 1));
            $sheet->setCellValue('BF' . ($indexJudul + 1), 'Pengiriman ke SET')->getStyle("BF" . ($indexJudul + 1))->getAlignment()->setHorizontal(PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->mergeCells("BF" . ($indexJudul + 1) . ":" . "BK" . ($indexJudul + 1));
            $sheet->setCellValue('BL' . ($indexJudul + 1), 'Pengiriman ke PAD')->getStyle("BL" . ($indexJudul + 1))->getAlignment()->setHorizontal(PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->mergeCells("BL" . ($indexJudul + 1) . ":" . "BQ" . ($indexJudul + 1));
            $sheet->setCellValue('BR' . ($indexJudul + 1), 'Pengiriman ke BRS')->getStyle("BR" . ($indexJudul + 1))->getAlignment()->setHorizontal(PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->mergeCells("BR" . ($indexJudul + 1) . ":" . "BW" . ($indexJudul + 1));
            $sheet->setCellValue('BX' . ($indexJudul + 1), 'Pengiriman ke INS2')->getStyle("BX" . ($indexJudul + 1))->getAlignment()->setHorizontal(PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->mergeCells("BX" . ($indexJudul + 1) . ":" . "CC" . ($indexJudul + 1));
            $sheet->setCellValue('CD' . ($indexJudul + 1), 'Pengiriman ke GJD')->getStyle("CD" . ($indexJudul + 1))->getAlignment()->setHorizontal(PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->mergeCells("CD" . ($indexJudul + 1) . ":" . "CI" . ($indexJudul + 1));

            $sheet->setCellValue('AB' . ($indexJudul + 2), 'Tanggal HPH');
            $sheet->setCellValue('AC' . ($indexJudul + 2), 'Lot');
            $sheet->setCellValue('AD' . ($indexJudul + 2), 'Qty 1');
            $sheet->setCellValue('AE' . ($indexJudul + 2), 'Uom 1');
            $sheet->setCellValue('AF' . ($indexJudul + 2), 'Qty 2');
            $sheet->setCellValue('AG' . ($indexJudul + 2), 'Uom 2');

            $sheet->setCellValue('AH' . ($indexJudul + 2), 'Tanggal HPH');
            $sheet->setCellValue('AI' . ($indexJudul + 2), 'Lot');
            $sheet->setCellValue('AJ' . ($indexJudul + 2), 'Qty 1');
            $sheet->setCellValue('AK' . ($indexJudul + 2), 'Uom 1');
            $sheet->setCellValue('AL' . ($indexJudul + 2), 'Qty 2');
            $sheet->setCellValue('AM' . ($indexJudul + 2), 'Uom 2');

            $sheet->setCellValue('AN' . ($indexJudul + 2), 'Tanggal HPH');
            $sheet->setCellValue('AO' . ($indexJudul + 2), 'Lot');
            $sheet->setCellValue('AP' . ($indexJudul + 2), 'Qty 1');
            $sheet->setCellValue('AQ' . ($indexJudul + 2), 'Uom 1');
            $sheet->setCellValue('AR' . ($indexJudul + 2), 'Qty 2');
            $sheet->setCellValue('AS' . ($indexJudul + 2), 'Uom 2');

            $sheet->setCellValue('AT' . ($indexJudul + 2), 'Tanggal HPH');
            $sheet->setCellValue('AU' . ($indexJudul + 2), 'Lot');
            $sheet->setCellValue('AV' . ($indexJudul + 2), 'Qty 1');
            $sheet->setCellValue('AW' . ($indexJudul + 2), 'Uom 1');
            $sheet->setCellValue('AX' . ($indexJudul + 2), 'Qty 2');
            $sheet->setCellValue('AY' . ($indexJudul + 2), 'Uom 2');

            $sheet->setCellValue('AZ' . ($indexJudul + 2), 'Tanggal HPH');
            $sheet->setCellValue('BA' . ($indexJudul + 2), 'Lot');
            $sheet->setCellValue('BB' . ($indexJudul + 2), 'Qty 1');
            $sheet->setCellValue('BC' . ($indexJudul + 2), 'Uom 1');
            $sheet->setCellValue('BD' . ($indexJudul + 2), 'Qty 2');
            $sheet->setCellValue('BE' . ($indexJudul + 2), 'Uom 2');

            $sheet->setCellValue('BF' . ($indexJudul + 2), 'Tanggal HPH');
            $sheet->setCellValue('BG' . ($indexJudul + 2), 'Lot');
            $sheet->setCellValue('BH' . ($indexJudul + 2), 'Qty 1');
            $sheet->setCellValue('BI' . ($indexJudul + 2), 'Uom 1');
            $sheet->setCellValue('BJ' . ($indexJudul + 2), 'Qty 2');
            $sheet->setCellValue('BK' . ($indexJudul + 2), 'Uom 2');

            $sheet->setCellValue('BL' . ($indexJudul + 2), 'Tanggal HPH');
            $sheet->setCellValue('BM' . ($indexJudul + 2), 'Lot');
            $sheet->setCellValue('BN' . ($indexJudul + 2), 'Qty 1');
            $sheet->setCellValue('BO' . ($indexJudul + 2), 'Uom 1');
            $sheet->setCellValue('BP' . ($indexJudul + 2), 'Qty 2');
            $sheet->setCellValue('BQ' . ($indexJudul + 2), 'Uom 2');

            $sheet->setCellValue('BR' . ($indexJudul + 2), 'Tanggal HPH');
            $sheet->setCellValue('BS' . ($indexJudul + 2), 'Lot');
            $sheet->setCellValue('BT' . ($indexJudul + 2), 'Qty 1');
            $sheet->setCellValue('BU' . ($indexJudul + 2), 'Uom 1');
            $sheet->setCellValue('BV' . ($indexJudul + 2), 'Qty 2');
            $sheet->setCellValue('BW' . ($indexJudul + 2), 'Uom 2');

            $sheet->setCellValue('BX' . ($indexJudul + 2), 'Tanggal HPH');
            $sheet->setCellValue('BY' . ($indexJudul + 2), 'Lot');
            $sheet->setCellValue('BZ' . ($indexJudul + 2), 'Qty 1');
            $sheet->setCellValue('CA' . ($indexJudul + 2), 'Uom 1');
            $sheet->setCellValue('CB' . ($indexJudul + 2), 'Qty 2');
            $sheet->setCellValue('CC' . ($indexJudul + 2), 'Uom 2');

            $sheet->setCellValue('CD' . ($indexJudul + 2), 'Tanggal HPH');
            $sheet->setCellValue('CE' . ($indexJudul + 2), 'Lot');
            $sheet->setCellValue('CF' . ($indexJudul + 2), 'Qty 1');
            $sheet->setCellValue('CG' . ($indexJudul + 2), 'Uom 1');
            $sheet->setCellValue('CH' . ($indexJudul + 2), 'Qty 2');
            $sheet->setCellValue('CI' . ($indexJudul + 2), 'Uom 2');

            //DYEING FINISHING
            $sheet->setCellValue('CJ' . $indexJudul, 'DYEING FINISHING')->getStyle("CJ" . ($indexJudul))->getAlignment()->setHorizontal(PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->mergeCells("CJ" . $indexJudul . ":" . "DA" . $indexJudul);
            $sheet->setCellValue('CJ' . ($indexJudul + 1), 'Penerimaan dari GRG')->getStyle("CJ" . ($indexJudul + 1))->getAlignment()->setHorizontal(PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->mergeCells("CJ" . ($indexJudul + 1) . ":" . "CO" . ($indexJudul + 1));
            $sheet->setCellValue('CP' . ($indexJudul + 1), 'Pengiriman ke INS2')->getStyle("CP" . ($indexJudul + 1))->getAlignment()->setHorizontal(PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->mergeCells("CP" . ($indexJudul + 1) . ":" . "CU" . ($indexJudul + 1));
            $sheet->setCellValue('CV' . ($indexJudul + 1), 'Pengiriman ke GRG')->getStyle("CV" . ($indexJudul + 1))->getAlignment()->setHorizontal(PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->mergeCells("CV" . ($indexJudul + 1) . ":" . "DA" . ($indexJudul + 1));

            $sheet->setCellValue('CJ' . ($indexJudul + 2), 'Tanggal HPH');
            $sheet->setCellValue('CK' . ($indexJudul + 2), 'Lot');
            $sheet->setCellValue('CL' . ($indexJudul + 2), 'Qty 1');
            $sheet->setCellValue('CM' . ($indexJudul + 2), 'Uom 1');
            $sheet->setCellValue('CN' . ($indexJudul + 2), 'Qty 2');
            $sheet->setCellValue('CO' . ($indexJudul + 2), 'Uom 2');

            $sheet->setCellValue('CP' . ($indexJudul + 2), 'Tanggal HPH');
            $sheet->setCellValue('CQ' . ($indexJudul + 2), 'Lot');
            $sheet->setCellValue('CR' . ($indexJudul + 2), 'Qty 1');
            $sheet->setCellValue('CS' . ($indexJudul + 2), 'Uom 1');
            $sheet->setCellValue('CT' . ($indexJudul + 2), 'Qty 2');
            $sheet->setCellValue('CU' . ($indexJudul + 2), 'Uom 2');

            $sheet->setCellValue('CV' . ($indexJudul + 2), 'Tanggal HPH');
            $sheet->setCellValue('CW' . ($indexJudul + 2), 'Lot');
            $sheet->setCellValue('CX' . ($indexJudul + 2), 'Qty 1');
            $sheet->setCellValue('CY' . ($indexJudul + 2), 'Uom 1');
            $sheet->setCellValue('CZ' . ($indexJudul + 2), 'Qty 2');
            $sheet->setCellValue('DA' . ($indexJudul + 2), 'Uom 2');

            //INSPECTING2
            $sheet->setCellValue('DB' . $indexJudul, 'INSPECTING2')->getStyle("DB" . ($indexJudul))->getAlignment()->setHorizontal(PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->mergeCells("DB" . $indexJudul . ":" . "EQ" . $indexJudul);
            $sheet->setCellValue('DB' . ($indexJudul + 1), 'Penerimaan dari GRG')->getStyle("DB" . ($indexJudul + 1))->getAlignment()->setHorizontal(PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->mergeCells("DB" . ($indexJudul + 1) . ":" . "DG" . ($indexJudul + 1));

            $sheet->setCellValue('DH' . ($indexJudul + 1), 'Pengiriman ke SET')->getStyle("DH" . ($indexJudul + 1))->getAlignment()->setHorizontal(PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->mergeCells("DH" . ($indexJudul + 1) . ":" . "DM" . ($indexJudul + 1));

            $sheet->setCellValue('DN' . ($indexJudul + 1), 'Pengiriman ke PAD')->getStyle("DN" . ($indexJudul + 1))->getAlignment()->setHorizontal(PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->mergeCells("DN" . ($indexJudul + 1) . ":" . "DS" . ($indexJudul + 1));

            $sheet->setCellValue('DT' . ($indexJudul + 1), 'Pengiriman ke FIN')->getStyle("DT" . ($indexJudul + 1))->getAlignment()->setHorizontal(PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->mergeCells("DT" . ($indexJudul + 1) . ":" . "DY" . ($indexJudul + 1));

            $sheet->setCellValue('DZ' . ($indexJudul + 1), 'Pengiriman ke FBR')->getStyle("DZ" . ($indexJudul + 1))->getAlignment()->setHorizontal(PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->mergeCells("DZ" . ($indexJudul + 1) . ":" . "EE" . ($indexJudul + 1));
            $sheet->setCellValue('EF' . ($indexJudul + 1), 'Pengiriman ke GJD')->getStyle("EF" . ($indexJudul + 1))->getAlignment()->setHorizontal(PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->mergeCells("EF" . ($indexJudul + 1) . ":" . "EK" . ($indexJudul + 1));
            $sheet->setCellValue('EL' . ($indexJudul + 1), 'Pengiriman ke GRG')->getStyle("EL" . ($indexJudul + 1))->getAlignment()->setHorizontal(PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->mergeCells("EL" . ($indexJudul + 1) . ":" . "EQ" . ($indexJudul + 1));

            $sheet->setCellValue('DB' . ($indexJudul + 2), 'Tanggal HPH');
            $sheet->setCellValue('DC' . ($indexJudul + 2), 'Lot');
            $sheet->setCellValue('DD' . ($indexJudul + 2), 'Qty 1');
            $sheet->setCellValue('DE' . ($indexJudul + 2), 'Uom 1');
            $sheet->setCellValue('DF' . ($indexJudul + 2), 'Qty 2');
            $sheet->setCellValue('DG' . ($indexJudul + 2), 'Uom 2');

            $sheet->setCellValue('DH' . ($indexJudul + 2), 'Tanggal HPH');
            $sheet->setCellValue('DI' . ($indexJudul + 2), 'Lot');
            $sheet->setCellValue('DJ' . ($indexJudul + 2), 'Qty 1');
            $sheet->setCellValue('DK' . ($indexJudul + 2), 'Uom 1');
            $sheet->setCellValue('DL' . ($indexJudul + 2), 'Qty 2');
            $sheet->setCellValue('DM' . ($indexJudul + 2), 'Uom 2');

            $sheet->setCellValue('DN' . ($indexJudul + 2), 'Tanggal HPH');
            $sheet->setCellValue('DO' . ($indexJudul + 2), 'Lot');
            $sheet->setCellValue('DP' . ($indexJudul + 2), 'Qty 1');
            $sheet->setCellValue('DQ' . ($indexJudul + 2), 'Uom 1');
            $sheet->setCellValue('DR' . ($indexJudul + 2), 'Qty 2');
            $sheet->setCellValue('DS' . ($indexJudul + 2), 'Uom 2');

            $sheet->setCellValue('DT' . ($indexJudul + 2), 'Tanggal HPH');
            $sheet->setCellValue('DU' . ($indexJudul + 2), 'Lot');
            $sheet->setCellValue('DV' . ($indexJudul + 2), 'Qty 1');
            $sheet->setCellValue('DW' . ($indexJudul + 2), 'Uom 1');
            $sheet->setCellValue('DX' . ($indexJudul + 2), 'Qty 2');
            $sheet->setCellValue('DY' . ($indexJudul + 2), 'Uom 2');

            $sheet->setCellValue('DZ' . ($indexJudul + 2), 'Tanggal HPH');
            $sheet->setCellValue('EA' . ($indexJudul + 2), 'Lot');
            $sheet->setCellValue('EB' . ($indexJudul + 2), 'Qty 1');
            $sheet->setCellValue('EC' . ($indexJudul + 2), 'Uom 1');
            $sheet->setCellValue('ED' . ($indexJudul + 2), 'Qty 2');
            $sheet->setCellValue('EE' . ($indexJudul + 2), 'Uom 2');

            $sheet->setCellValue('EF' . ($indexJudul + 2), 'Tanggal HPH');
            $sheet->setCellValue('EG' . ($indexJudul + 2), 'Lot');
            $sheet->setCellValue('EH' . ($indexJudul + 2), 'Qty 1');
            $sheet->setCellValue('EI' . ($indexJudul + 2), 'Uom 1');
            $sheet->setCellValue('EJ' . ($indexJudul + 2), 'Qty 2');
            $sheet->setCellValue('EK' . ($indexJudul + 2), 'Uom 2');

            $sheet->setCellValue('EL' . ($indexJudul + 2), 'Tanggal HPH');
            $sheet->setCellValue('EM' . ($indexJudul + 2), 'Lot');
            $sheet->setCellValue('EN' . ($indexJudul + 2), 'Qty 1');
            $sheet->setCellValue('EO' . ($indexJudul + 2), 'Uom 1');
            $sheet->setCellValue('EP' . ($indexJudul + 2), 'Qty 2');
            $sheet->setCellValue('EQ' . ($indexJudul + 2), 'Uom 2');

            //GJD
            $sheet->setCellValue('ER' . $indexJudul, 'GUDANG JADI')->getStyle("ER" . ($indexJudul))->getAlignment()->setHorizontal(PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->mergeCells("ER" . $indexJudul . ":" . "EW" . $indexJudul);
            $sheet->setCellValue('ER' . ($indexJudul + 1), 'Penerimaan dari INS2')->getStyle("ER" . ($indexJudul + 1))->getAlignment()->setHorizontal(PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->mergeCells("ER" . ($indexJudul + 1) . ":" . "EW" . ($indexJudul + 1));
            $sheet->setCellValue('ER' . ($indexJudul + 2), 'Tanggal HPH');
            $sheet->setCellValue('ES' . ($indexJudul + 2), 'Lot');
            $sheet->setCellValue('ET' . ($indexJudul + 2), 'Qty 1');
            $sheet->setCellValue('EU' . ($indexJudul + 2), 'Uom 1');
            $sheet->setCellValue('EV' . ($indexJudul + 2), 'Qty 2');
            $sheet->setCellValue('EW' . ($indexJudul + 2), 'Uom 2');

            $indexData = $indexJudul + 3;
            foreach ($data as $key => $value) {
                $sheet->setCellValue('A' . $indexData, ($key + 1));
                $sheet->setCellValue('B' . $indexData, $value->kp);
                $sheet->setCellValue('C' . $indexData, $value->qty);
                $sheet->setCellValue('D' . $indexData, $value->uom);
                $sheet->setCellValue('E' . $indexData, $value->qty2);
                $sheet->setCellValue('F' . $indexData, $value->uom2);
                $sheet->setCellValue('G' . $indexData, $value->go);
                $sheet->setCellValue('H' . $indexData, $value->nama_route);
                $sheet->setCellValue('I' . $indexData, $value->nama_produk);
                $sheet->setCellValue('J' . $indexData, $value->nama_warna);
                $sheet->setCellValue('K' . $indexData, $value->produk_parent);
                $sheet->setCellValue('L' . $indexData, $value->nama_jenis_kain);
                $col = 16;
                foreach ($value->detail as $keys => $values) {
                    $values = (object) $values;
                    $details = explode("#", $values->dt);

                    if ($keys === 0) {
                        $sheet->setCellValue($this->number_to_alphabet(($col - 3)) . $indexData, $details[0] ?? "");
                        $sheet->setCellValue($this->number_to_alphabet(($col - 2)) . $indexData, $details[1] ?? "");
                        $sheet->setCellValue($this->number_to_alphabet(($col - 1)) . $indexData, isset($details[2]) ? str_replace(";", '"', $details[2]) : "");
                    }
                    $sheet->setCellValue($this->number_to_alphabet($col) . $indexData, $details[3] ?? "");
                    $col += 1;
                    $sheet->setCellValue($this->number_to_alphabet($col) . $indexData, $details[4] ?? "");
                    $col += 1;
                    $sheet->setCellValue($this->number_to_alphabet($col) . $indexData, $details[5] ?? "");
                    $col += 1;
                    $sheet->setCellValue($this->number_to_alphabet($col) . $indexData, $details[6] ?? "");
                    $col += 1;
                    $sheet->setCellValue($this->number_to_alphabet($col) . $indexData, $details[7] ?? "");
                    $col += 1;
                    $sheet->setCellValue($this->number_to_alphabet($col) . $indexData, $details[8] ?? "");
                    $col += 1;
                }

                $indexData += 1;
//                for ($i = 1; $i <= 153; $i++) {
//                    
//                }
            }

            $writer = new Xlsx($spreadsheet);
            $filename = "Report Recycle";
            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save(FCPATH . 'dist/storages/report/suratjalan/' . $filename . '.xlsx');
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil Export', 'icon' => 'fa fa-check', 'text_name' => $filename,
                        'type' => 'success', "data" => base_url('dist/storages/report/suratjalan/' . $filename . '.xlsx'))));
        } catch (Exception $ex) {
            $this->output->set_status_header(500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger', "data" => "")));
        }
    }

    public function search($p = 0) {
        try {
            $kp = $this->input->post("kp") ?? [];
            log_message("error", count($kp));
//            $mo = $this->input->post("mo");
//            $corak = $this->input->post("corak");
            $perPage = 25;
            $page = $this->input->post("page") ?? 0;
//            $condition = [];
//            if (!(empty(trim($kp)))) {
//                $condition = array_merge($condition, ["mph.lot" => $kp]);
//            }
//            if (!(empty(trim($mo)))) {
//                $condition = array_merge($condition, ["mp.kode" => $mo]);
//            }
//            if (!(empty(trim($corak)))) {
//                $condition = array_merge($condition, ["p.nama_produk LIKE" => "%" . $corak . "%"]);
//            }
            if (is_string($kp)) {
                throw new Exception("Silahkan Pilih KP Terlebih dahulu", 500);
            }
            if(count($kp) < 1) {
                throw new Exception("Silahkan Pilih KP Terlebih dahulu", 500);
            }
            $condition = ['mph.lot' => $kp];
            $base = $this->m_recycle->setWhereIn($condition)
                    ->setSelect('mph.lot as kp,mph.qty,mph.uom,mph.qty2,mph.uom2,p.kode as go,nama_route,p.nama_produk,p.nama_warna,p.produk_parent,p.nama_jenis_kain');

            $count = $base->resultCount();

            $_POST['length'] = $perPage;
            $_POST['start'] = 0;
            if ($page > 0) {
                $_POST['start'] = ($page - 1) * $perPage;
            }
            $header = $base->result();
            $queryDetail = [];
            $headers = $header;
            foreach ($headers as $keys => $values) {
                foreach ($this->submodul as $key => $value) {
                    $query = $this->m_recycle->detail(array_merge($value, ["lot" => $values->kp]), true);
                    $queryDetail = array_merge($queryDetail, [$query]);
                }
                $detail = $this->m_recycle->getDetail('(' . implode(" ) UNION ALL ( ", $queryDetail) . ')');
                $header[$keys]->detail = $detail;
                $queryDetail = [];
            }

            $data["data"] = $this->load->view('report/v_recycle_detail', ['header' => $header, 'page' => $page, 'perpage' => $perPage], true);

            $config['base_url'] = base_url() . 'report/recycle';
            $config['use_page_numbers'] = TRUE;
            $config['total_rows'] = $count;
            $config['per_page'] = $perPage;
            $config['full_tag_open'] = '<div class="pagging text-center"><nav><ul class="pagination">';
            $config['full_tag_close'] = '</ul></nav></div>';
            $config['num_tag_open'] = '<li class="page-item"><span class="page-link">';
            $config['num_tag_close'] = '</span></li>';
            $config['cur_tag_open'] = '<li class="page-item active"><span class="page-link">';
            $config['cur_tag_close'] = '<span class="sr-only">(current)</span></span></li>';
            $config['next_tag_open'] = '<li class="page-item"><span class="page-link">';
            $config['next_tag_close'] = '<span aria-hidden="true"></span></span></li>';
            $config['prev_tag_open'] = '<li class="page-item"><span class="page-link">';
            $config['prev_tag_close'] = '</span></li>';
            $config['first_tag_open'] = '<li class="page-item"><span class="page-link">';
            $config['first_tag_close'] = '</span></li>';
            $config['last_tag_open'] = '<li class="page-item"><span class="page-link">';
            $config['last_tag_close'] = '</span></li>';

            $this->pagination->initialize($config);
            $data['pagination'] = $this->pagination->create_links();
            $data['total'] = $count;
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Data ditemukan', 'icon' => 'fa fa-check', 'type' => 'success', "data" => $data)));
        } catch (Exception $ex) {
            $this->output->set_status_header(500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
            
        }
    }

    protected function number_to_alphabet($number) {
        $number = intval($number);
        if ($number <= 0) {
            return '';
        }
        $alphabet = '';
        while ($number != 0) {
            $p = ($number - 1) % 26;
            $number = intval(($number - $p) / 26);
            $alphabet = chr(65 + $p) . $alphabet;
        }
        return $alphabet;
    }
}
