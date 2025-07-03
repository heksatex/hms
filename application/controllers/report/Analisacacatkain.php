<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */
defined('BASEPATH') or exit('No Direct Script Acces Allowed');

require_once APPPATH . '/third_party/vendor/autoload.php';

/**
 * Description of Analisacacatkain
 *
 * @author RONI
 */
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Settings;
use Cache\Adapter\Apcu\ApcuCachePool;
use Cache\Bridge\SimpleCache\SimpleCacheBridge;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Analisacacatkain extends MY_Controller {

    //put your code here
    protected $where = [
        "total_grade_a_GJD" => [
            'mpfh.nama_grade' => "A",
            'select' => "sum(qty) as total_qty",
            "mpfh.lokasi LIKE" => "%Stock"
        ],
        "cacat_grade_a_DF" => [
            "join" => true,
            'select' => "sum(qty) as total_qty",
            "mpfh.lokasi LIKE" => "%Stock",
            'raw' => [
                "type" => "raw",
                "data" => '(mpc.kode_cacat NOT LIKE "D%" and mpc.kode_cacat NOT LIKE "F%")'
            ],
            'mpfh.nama_grade' => "A",
        ],
        "cacat_grade_a_TF" => [
            "join" => true,
            'select' => "sum(qty) as total_qty",
            "mpfh.lokasi LIKE" => "%Stock",
            'raw' => [
                "type" => "raw",
                "data" => '(mpc.kode_cacat NOT LIKE "T%" and mpc.kode_cacat NOT LIKE "F%")'
            ],
            'mpfh.nama_grade' => "A",
        ],
        "cacat_grade_a_TD" => [
            "join" => true,
            'select' => "sum(qty) as total_qty",
            "mpfh.lokasi LIKE" => "%Stock",
            'raw' => [
                "type" => "raw",
                "data" => '(mpc.kode_cacat NOT LIKE "D%" and mpc.kode_cacat NOT LIKE "T%")'
            ],
            'mpfh.nama_grade' => "A",
        ]
    ];
    protected $where2 = [
        "produksi_b_t" => [
            'mpfh.nama_grade' => "B",
            'select' => "count(qty) as total_qty",
            "mpfh.lokasi LIKE" => "%Stock",
            "mpc.kode_cacat LIKE" => "T%"
        ],
        "produksi_b_d" => [
            'mpfh.nama_grade' => "B",
            'select' => "count(qty) as total_qty",
            "mpfh.lokasi LIKE" => "%Stock",
            "mpc.kode_cacat LIKE" => "D%"
        ],
        "produksi_b_f" => [
            'mpfh.nama_grade' => "B",
            'select' => "count(qty) as total_qty",
            "mpfh.lokasi LIKE" => "%Stock",
            "mpc.kode_cacat LIKE" => "F%"
        ],
        "produksi_c_t_bs_pot" => [
            'mpfh.nama_grade' => "C",
            "mpfh.lokasi LIKE" => "%Stock",
            "mpc.kode_cacat LIKE" => "T%",
            "select" => "count(mpfh.qty) as total_qty",
            "join" => true,
            "raw" => [
                "type" => "raw",
                "data" => '(sq.corak_remark LIKE "BS %" or sq.corak_remark LIKE "potongan %")'
            ]
        ],
        "produksi_c_d_bs" => [
            'mpfh.nama_grade' => "C",
            "mpfh.lokasi LIKE" => "%Stock",
            "mpc.kode_cacat LIKE" => "D%",
            "select" => "count(mpfh.qty) as total_qty",
            "join" => true,
            "raw" => [
                "type" => "raw",
                "data" => '(sq.corak_remark LIKE "BS %" or sq.corak_remark LIKE "potongan %")'
            ]
        ],
        "produksi_c_f_bs" => [
            'mpfh.nama_grade' => "C",
            "mpfh.lokasi LIKE" => "%Stock",
            "mpc.kode_cacat LIKE" => "F%",
            "select" => "count(mpfh.qty) as total_qty",
            "join" => true,
            "raw" => [
                "type" => "raw",
                "data" => '(sq.corak_remark LIKE "BS %" or sq.corak_remark LIKE "potongan %")'
            ]
        ],
        "produksi_c_tali" => [
            'mpfh.nama_grade' => "C",
            "mpfh.lokasi LIKE" => "%Stock",
            "select" => "count(mpfh.qty) as total_qty",
            "join" => true,
            "raw" => [
                "type" => "raw",
                "data" => '(sq.corak_remark LIKE "tali%")'
            ]
        ],
    ];

    public function __construct() {
        parent::__construct();
        $this->is_loggedin();
        $_SERVER['CI_ENV'] = "production";
        $this->load->model("_module");
        $this->load->library('pagination');
        $this->load->model("m_produk");
        $this->load->model("m_analisa_kain_cacat");
        $this->load->model("m_global");
    }

    public function index() {
        $data['id_dept'] = 'RACK';
        $modelSales = new $this->m_global;
        $data["sales"] = $modelSales->setTables("mst_sales_group")->setWheres(["view" => "1"])->setOrder(["nama_sales_group"])->getData();
        $data['date'] = date('Y-m-d', strtotime("-1 month", strtotime(date("Y-m-d")))) . ' - ' . date('Y-m-d');
        $data['jenis_kain'] = $this->m_produk->get_list_jenis_kain();
        $this->load->view('report/v_analisa_kain_cacat', $data);
    }

    public function export() {
        $uniqid = "";//uniqid("_copy_");
        $tbl_mrp_production = "mrp_production";
        $tbl_mrp_production_fg_hasil = "mrp_production_fg_hasil";
        $tbl_mst_produk = "mst_produk";
        $tbl_mst_produk_parent = "mst_produk_parent";
        $tbl_mrp_production_cacat = "mrp_production_cacat";
        $tbl_stock_quant = "stock_quant";
        $tbl_mrp_inlet = "mrp_inlet";

        $grade = ["b"];

        try {
            $periode = $this->input->post("periode");
            $jenis_kain = $this->input->post("jenis_kain");
            $marketing = $this->input->post("marketing");
            $detail = $this->input->post("detail");
            $period = explode(" - ", $periode);
            if (count($period) < 2) {
                throw new \Exception("Tentukan dahulu periodenya", 500);
            }
            $tanggalAwal = date("Y-m-d H:i:s", strtotime($period[0] . " 00:00:00"));
            $tanggalAkhir = date("Y-m-d H:i:s", strtotime($period[1] . " 23:59:59"));
            $model = new $this->m_global;
            $jk = "('" . implode(',', $jenis_kain) . "')";
//            $model->copyExt($tbl_mrp_production_fg_hasil, $uniqid . $tbl_mrp_production_fg_hasil);
//            $model->copyExt($tbl_mst_produk, $uniqid . $tbl_mst_produk);
//            $model->copyExt($tbl_mst_produk_parent, $uniqid . $tbl_mst_produk_parent);
//            $model->copyExt($tbl_mrp_production_cacat, $uniqid . $tbl_mrp_production_cacat);
//            $model->copyExt($tbl_mrp_inlet, $uniqid . $tbl_mrp_inlet);
//            $model->copyExt($tbl_mrp_production, $uniqid . $tbl_mrp_production);

            $this->_module->startTransaction();

            $this->_module->lock_tabel("analisa_cacat_kain WRITE,analisa_cacat_kain_defect WRITE,analisa_cacat_kain a WRITE,mst_sales_group msg READ ,mst_cacat MC READ,{$uniqid}{$tbl_mrp_inlet} mi READ,"
            . "{$uniqid}{$tbl_mst_produk} mp READ,{$uniqid}{$tbl_mst_produk_parent} mpsp READ,{$uniqid}{$tbl_mrp_production_cacat} mpc READ, mrp_production_cacat READ,"
                    . "{$uniqid}{$tbl_mrp_production} mrpp READ,{$uniqid}{$tbl_mrp_production_fg_hasil} mrppfghs READ");
                    
            $this->m_analisa_kain_cacat->getQuery("TRUNCATE TABLE analisa_cacat_kain;", false);
            $queryAwal = "INSERT INTO analisa_cacat_kain (nama_parent, kp, barcode, nama_grade, tgl, corak, warna, qty_all) ";
            $queryAwal .= "SELECT mpsp.nama, mi.lot as kp, mrppfghs.lot as barcode, mrppfghs.nama_grade, mrppfghs.create_date, mi.corak_remark, mi.warna_remark, mrppfghs.qty ";
            $queryAwal .= "FROM {$uniqid}{$tbl_mrp_production} mrpp ";
            $queryAwal .= "JOIN {$uniqid}{$tbl_mrp_production_fg_hasil} `mrppfghs` ON (`mrppfghs`.`kode` = `mrpp`.`kode` and `mrppfghs`.`lokasi` LIKE '%Stock') ";
            $queryAwal .= "INNER JOIN {$uniqid}{$tbl_mrp_inlet} mi ON mrppfghs.id_inlet = mi.id ";
            $queryAwal .= "JOIN {$uniqid}{$tbl_mst_produk} `mp` ON `mp`.`kode_produk` = `mrpp`.`kode_produk` ";
            $queryAwal .= "JOIN {$uniqid}{$tbl_mst_produk_parent} `mpsp` ON `mpsp`.`id` = `mp`.`id_parent` ";
            $queryAwal .= "WHERE `mrppfghs`.`create_date` >= '{$tanggalAwal}' AND `mrppfghs`.`create_date` <= '{$tanggalAkhir}' ";
            $queryAwal .= "AND mrppfghs.lokasi LIKE '%Stock' AND mrpp.dept_id='GJD' and mp.id_jenis_kain in {$jk}";

            if ($marketing !== "") {
                $queryAwal .= " and mi.sales_group = '{$marketing}' ";
            }
            else {
                 $queryAwal .= " and mi.view = '1' ";
            }

            $rst = $model->excQuery($queryAwal);
            if (is_array($rst)) {
                throw new \Exception("{$rst['message']}", 500);
            }

            $updateAgjd = $model->excQuery("UPDATE analisa_cacat_kain SET a_gjd=qty_all WHERE nama_grade='A'");
            if (is_array($updateAgjd)) {
                throw new \Exception("{$updateAgjd['message']}", 500);
            }

            foreach ($grade as $key => $val) {
                $query = "UPDATE analisa_cacat_kain a join ";
                $query .= "(SELECT lot, GROUP_CONCAT(kode_cacat) as kodes FROM {$uniqid}{$tbl_mrp_production_cacat} mpc ";
                $query .= "WHERE lot IN (SELECT barcode FROM analisa_cacat_kain) GROUP BY lot) b ";
                $query .= "ON a.barcode=b.lot SET ";
                $query .= "a.{$val}_prod= CASE WHEN b.kodes LIKE 'T%' THEN a.qty_all ELSE 0 END, ";
                $query .= "a.{$val}_dye= CASE WHEN b.kodes LIKE 'D%' THEN a.qty_all ELSE 0 END, ";
                $query .= "a.{$val}_fin= CASE WHEN b.kodes LIKE 'F%' OR b.kodes LIKE 'B%' THEN a.qty_all ELSE 0 END, ";
                $query .= "a.{$val}_lain= CASE WHEN b.kodes LIKE 'L%' THEN a.qty_all ELSE 0 END ";
                $query .= "WHERE a.nama_grade='{$val}';";

                $updateAlain = $model->excQuery($query);
                if (is_array($updateAlain)) {
                    throw new \Exception("{$updateAlain['message']}", 500);
                }
            }

            //#c_prod, c_dye, c_fin, c_lain
            $query = "UPDATE analisa_cacat_kain a join ";
            $query .= "(SELECT lot,SUM(kode_cacat LIKE 'T%') as sum_p,SUM(kode_cacat LIKE 'D%') as sum_d,SUM(kode_cacat LIKE 'F%') as sum_f, ";
            $query .= "SUM(kode_cacat LIKE 'L%') as sum_l FROM {$uniqid}{$tbl_mrp_production_cacat} WHERE lot IN (SELECT barcode FROM analisa_cacat_kain) GROUP BY lot) b ";
            $query .= "ON a.barcode=b.lot SET ";
            $query .= "a.c_prod=sum_p,a.c_dye=sum_d,a.c_fin=sum_f,a.c_lain=sum_l WHERE a.nama_grade='C';";

            $updateC = $model->excQuery($query);
            if (is_array($updateC)) {
                throw new \Exception("{$updateC['message']}", 500);
            }

            //#a_prod, a_dye, a_fin
            $query = "UPDATE analisa_cacat_kain SET a_fin = a_gjd + b_prod + b_dye + c_prod + c_dye,";
            $query .= "a_prod = a_gjd + b_dye + b_fin + c_dye + c_fin,a_dye = a_gjd + b_prod + b_fin + c_prod + c_fin;";

            $updateA = $model->excQuery($query);
            if (is_array($updateA)) {
                throw new \Exception("{$updateA['message']}", 500);
            }
            //analisa_cacat_kain_defect;
            $this->m_analisa_kain_cacat->getQuery("TRUNCATE TABLE analisa_cacat_kain_defect;", false);
            $query = "INSERT INTO analisa_cacat_kain_defect (nama_parent, kp, barcode, mkt, nama_grade, kode_cacat, nama_cacat, tgl, corak, warna, qty_all) ";
            $query .= "SELECT mpsp.nama, mi.lot as kp, mrppfghs.lot as barcode, msg.nama_sales_group, mrppfghs.nama_grade, mpc.kode_cacat, ";
            $query .= "mc.nama_cacat, mi.tanggal, mi.corak_remark, mi.warna_remark, mrppfghs.qty FROM {$uniqid}{$tbl_mrp_production} mrpp ";
            $query .= "JOIN {$uniqid}{$tbl_mrp_production_fg_hasil} `mrppfghs` ON (`mrppfghs`.`kode` = `mrpp`.`kode` and `mrppfghs`.`lokasi` LIKE '%Stock') ";
            $query .= "INNER JOIN {$uniqid}{$tbl_mrp_inlet} mi ON mrppfghs.id_inlet = mi.id ";
            $query .= "INNER JOIN mst_sales_group msg ON mi.sales_group = msg.kode_sales_group ";
            $query .= "LEFT JOIN {$uniqid}{$tbl_mrp_production_cacat} mpc ON mrppfghs.quant_id = mpc.quant_id ";
            $query .= "LEFT JOIN mst_cacat mc ON mpc.kode_cacat = mc.kode_cacat ";
            $query .= "JOIN {$uniqid}{$tbl_mst_produk} `mp` ON `mp`.`kode_produk` = `mrpp`.`kode_produk` ";
            $query .= "JOIN {$uniqid}{$tbl_mst_produk_parent} `mpsp` ON `mpsp`.`id` = `mp`.`id_parent` ";
            $query .= "WHERE `mrppfghs`.`create_date` >= '{$tanggalAwal}' AND `mrppfghs`.`create_date` <= '{$tanggalAkhir}' ";
            $query .= "AND mrppfghs.lokasi LIKE '%Stock' AND mrpp.dept_id='GJD' and mp.id_jenis_kain in {$jk} ";

            if ($marketing !== "") {
                $query .= " and mi.sales_group = '{$marketing}' ";
            }
            else {
                 $queryAwal .= " and msg.view = '1' ";
            }
            $query .= "AND mrppfghs.nama_grade IN ('B') GROUP by mrppfghs.lot, mpc.kode_cacat; ";

            $rst = $model->excQuery($query);
            if (is_array($rst)) {
                throw new \Exception("{$rst['message']}", 500);
            }

            //#b_prod, b_dye, b_fin, b_lain
            $query = "UPDATE analisa_cacat_kain_defect SET ";
            $query .= "b_prod = CASE WHEN kode_cacat LIKE 'T%' THEN qty_all ELSE 0 END, ";
            $query .= "b_dye= CASE WHEN kode_cacat LIKE 'D%' THEN qty_all ELSE 0 END, ";
            $query .= "b_fin= CASE WHEN kode_cacat LIKE 'F%' OR kode_cacat LIKE 'B%' THEN qty_all ELSE 0 END, ";
            $query .= "b_lain= CASE WHEN kode_cacat LIKE 'L%' THEN qty_all ELSE 0 END ";
            $query .= "WHERE nama_grade='B';";

            $rst = $model->excQuery($query);
            if (is_array($rst)) {
                throw new \Exception("{$rst['message']}", 500);
            }

            //#INSERT AWAL QTY ALL C
            $query = "INSERT INTO analisa_cacat_kain_defect (nama_parent, kp, barcode, mkt, nama_grade, kode_cacat, nama_cacat, tgl, corak, warna, qty_all) ";
            $query .= "SELECT mpsp.nama, mi.lot as kp, mrppfghs.lot as barcode, msg.nama_sales_group, mrppfghs.nama_grade, mpc.kode_cacat, mc.nama_cacat, ";
            $query .= "mi.tanggal, mi.corak_remark, mi.warna_remark, mrppfghs.qty FROM {$uniqid}{$tbl_mrp_production} mrpp ";
            $query .= "JOIN {$uniqid}{$tbl_mrp_production_fg_hasil} `mrppfghs` ON (`mrppfghs`.`kode` = `mrpp`.`kode` and `mrppfghs`.`lokasi` LIKE '%Stock') ";
            $query .= "INNER JOIN {$uniqid}{$tbl_mrp_inlet} mi ON mrppfghs.id_inlet = mi.id ";
            $query .= "INNER JOIN mst_sales_group msg ON mi.sales_group = msg.kode_sales_group ";
            $query .= "LEFT JOIN {$uniqid}{$tbl_mrp_production_cacat} mpc ON mrppfghs.quant_id = mpc.quant_id ";
            $query .= "LEFT JOIN mst_cacat mc ON mpc.kode_cacat = mc.kode_cacat ";
            $query .= "JOIN {$uniqid}{$tbl_mst_produk} `mp` ON `mp`.`kode_produk` = `mrpp`.`kode_produk` ";
            $query .= "JOIN {$uniqid}{$tbl_mst_produk_parent} `mpsp` ON `mpsp`.`id` = `mp`.`id_parent` ";
            $query .= "WHERE `mrppfghs`.`create_date` >= '{$tanggalAwal}' AND `mrppfghs`.`create_date` <= '{$tanggalAkhir}' ";
            $query .= "AND mrppfghs.lokasi LIKE '%Stock' AND mrpp.dept_id='GJD' and mp.id_jenis_kain in {$jk} ";
            if ($marketing !== "") {
                $query .= " and mi.sales_group = '{$marketing}' ";
            }
            else {
                 $query .= " and msg.view = '1' ";
            }
            $query .= "AND mrppfghs.nama_grade IN ('C') GROUP by mrppfghs.lot, mpc.kode_cacat; ";

            $rst = $model->excQuery($query);
            if (is_array($rst)) {
                throw new \Exception("{$rst['message']}", 500);
            }

            //#c_prod, c_dye, c_fin, c_lain
            $query = "UPDATE analisa_cacat_kain_defect SET ";
            $query .= "c_prod = CASE WHEN kode_cacat LIKE 'T%' THEN 1 ELSE 0 END, ";
            $query .= "c_dye= CASE WHEN kode_cacat LIKE 'D%' THEN 1 ELSE 0 END, ";
            $query .= "c_fin= CASE WHEN kode_cacat LIKE 'F%' OR kode_cacat LIKE 'B%' THEN 1 ELSE 0 END, ";
            $query .= "c_lain= CASE WHEN kode_cacat LIKE 'L%' THEN 1 ELSE 0 END ";
            $query .= "WHERE nama_grade='C';";

            $rst = $model->excQuery($query);
            if (is_array($rst)) {
                throw new \Exception("{$rst['message']}", 500);
            }

            if (!$this->_module->finishTransaction()) {
                throw new \Exception('Gagal update status', 500);
            }

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $start_row = 2;
            $start_row += 1;

            if (is_null($detail)) {
                $sheet->setCellValue('A' . $start_row, 'Corak');
                $sheet->setCellValue('B' . $start_row, 'Σ');
                $sheet->setCellValue('C' . $start_row, 'Σ A');
                $sheet->setCellValue('D' . $start_row, '%A');
                $sheet->setCellValue('E' . $start_row, 'Σ A');
                $sheet->setCellValue('F' . $start_row, '%A');
                $sheet->setCellValue('G' . $start_row, 'Σ A');
                $sheet->setCellValue('H' . $start_row, '%A');
                $sheet->setCellValue('I' . $start_row, 'Σ A');
                $sheet->setCellValue('J' . $start_row, '%A');

                $sheet->setCellValue("L" . $start_row, "Defect");
                $sheet->setCellValue("M" . $start_row, "Σ");
                $sheet->setCellValue("N" . $start_row, "Σ");
                $sheet->setCellValue("O" . $start_row, "Σ");
                $sheet->setCellValue("P" . $start_row, "Σ");
                $sheet->setCellValue("Q" . $start_row, "%B");
                $sheet->setCellValue("R" . $start_row, "Σ");
                $sheet->setCellValue("S" . $start_row, "Σ");
                $sheet->setCellValue("T" . $start_row, "Σ");
                $sheet->setCellValue("U" . $start_row, "Σ");
                $sheet->setCellValue("V" . $start_row, "%C");

                $start_row += 1;
                $sheet->setCellValue("C{$start_row}", "All");
                $sheet->mergeCells("C{$start_row}:D{$start_row}");
                $sheet->setCellValue("E{$start_row}", "Produksi");
                $sheet->mergeCells("E{$start_row}:F{$start_row}");
                $sheet->setCellValue("G{$start_row}", "DYEING");
                $sheet->mergeCells("G{$start_row}:H{$start_row}");
                $sheet->setCellValue("I{$start_row}", "FINISHING");
                $sheet->mergeCells("I{$start_row}:J{$start_row}");
                $sheet->setCellValue("M" . $start_row, "PRODUKSI");
                $sheet->setCellValue("N" . $start_row, "DYEING");
                $sheet->setCellValue("O" . $start_row, "FINISHING");
                $sheet->setCellValue("P" . $start_row, "Lain2");

                $sheet->setCellValue("R" . $start_row, "PRODUKSI");
                $sheet->setCellValue("S" . $start_row, "DYEING");
                $sheet->setCellValue("T" . $start_row, "FINISHING");
                $sheet->setCellValue("U" . $start_row, "Lain2");

                $start_row += 2;
                $start_row_2 = $start_row;
                $totalAllGjd = 0;
                $totalProd = 0;
                $totalDye = 0;
                $totalAll = 0;
                $totalFin = 0;

                $modelGlobal = new $this->m_global;
                $dataGLobal = $modelGlobal->setTables("analisa_cacat_kain")->setSelects(["nama_parent,sum(qty_all) as `all`, sum(a_gjd) as gjd, sum(a_prod) as prod,sum(a_dye) as dye, sum(a_fin) as fin"])
                                ->setGroups(["nama_parent"])->setOrder(["nama_parent"])->getData();

                foreach ($dataGLobal as $key => $value) {

                    $totalFin += $value->fin;
                    $totalAllGjd += $value->gjd;
                    $totalProd += $value->prod;
                    $totalDye += $value->dye;
                    $totalAll += $value->all;

                    $sheet->setCellValue("A" . $start_row, $value->nama_parent);
                    $sheet->setCellValue('B' . $start_row, $value->all ?? 0);
                    $sheet->setCellValue('C' . $start_row, $value->gjd);
                    $sheet->setCellValue('D' . $start_row, round(($value->gjd / $value->all) * 100, 2));
                    $sheet->setCellValue('E' . $start_row, $value->prod);
                    $sheet->setCellValue('F' . $start_row, round(($value->prod / $value->all) * 100, 2));
                    $sheet->setCellValue('G' . $start_row, $value->dye);
                    $sheet->setCellValue('H' . $start_row, round(($value->dye / $value->all) * 100, 2));
                    $sheet->setCellValue('I' . $start_row, $value->fin);
                    $sheet->setCellValue('J' . $start_row, round(($value->fin / $value->all) * 100, 2));
                    $start_row++;
                }
                $start_row += 1;
//            log_message("error","all {$totalAll} gjd {$totalAllGjd} prod {$totalProd} dye {$totalDye} fin {$totalFin}");
                $sheet->setCellValue("A" . $start_row, "Σ");
                $sheet->setCellValue("B" . $start_row, $totalAll);
                $sheet->setCellValue("C" . $start_row, $totalAllGjd);
                $sheet->setCellValue("D" . $start_row, ($totalAllGjd > 0) ? round(($totalAllGjd / $totalAll) * 100, 2) : 0);
                $sheet->setCellValue("E" . $start_row, $totalProd);
                $sheet->setCellValue("F" . $start_row, ($totalProd > 0) ? round(($totalProd / $totalAll) * 100, 2) : 0);
                $sheet->setCellValue("G" . $start_row, $totalDye);
                $sheet->setCellValue("H" . $start_row, ($totalDye > 0) ? round(($totalDye / $totalAll) * 100, 2) : 0);
                $sheet->setCellValue("I" . $start_row, $totalFin);
                $sheet->setCellValue("J" . $start_row, ($totalFin > 0) ? round(($totalFin / $totalAll) * 100, 2) : 0);

                //defectData

                $modelDefect = new $this->m_global;
                $dataDefect = $modelDefect->setTables("analisa_cacat_kain_defect")->setSelects([
                            "nama_cacat,sum(b_prod) as b_prod,sum(b_dye) as b_dye",
                            "sum(b_fin) as b_fin,sum(b_lain) as b_lain,sum(c_prod) as c_prod",
                            "sum(c_dye) as c_dye,sum(c_fin) as c_fin,sum(c_lain) as c_lain"
                        ])->setGroups(["nama_cacat"])->setOrder(["nama_cacat"])->getData();

                foreach ($dataDefect as $key => $value) {
                    $b = $value->b_prod + $value->b_dye + $value->b_fin + $value->b_lain;
                    $c = $value->c_prod + $value->c_dye + $value->c_fin + $value->c_lain;
                    $sheet->setCellValue("L" . $start_row_2, $value->nama_cacat);
                    $sheet->setCellValue("M" . $start_row_2, $value->b_prod);
                    $sheet->setCellValue("N" . $start_row_2, $value->b_dye);
                    $sheet->setCellValue("O" . $start_row_2, $value->b_fin);
                    $sheet->setCellValue("P" . $start_row_2, $value->b_lain);
                    $sheet->setCellValue("Q" . $start_row_2, ($b > 0) ? round(($b / $totalAll * 100),2) : 0);
                    $sheet->setCellValue("R" . $start_row_2, $value->c_prod);
                    $sheet->setCellValue("S" . $start_row_2, $value->c_dye);
                    $sheet->setCellValue("T" . $start_row_2, $value->c_fin);
                    $sheet->setCellValue("U" . $start_row_2, $value->c_lain);
                    $sheet->setCellValue("V" . $start_row_2, ($c > 0) ? round(($c / $totalAll * 100),2) : 0);
                    $start_row_2++;
                }
            } else {
                $detail_group = $this->input->post("detail_group");
                $ModelDetail = new $this->m_global;
                $ModelDetail->setTables("analisa_cacat_kain")->setSelects([
                    "kp, tgl, nama_parent,warna,mkt",
                    "sum(qty_all) as `all`, sum(a_gjd) as a_gjd,sum(a_prod) as a_prod",
                    "sum(a_dye) as a_dye, sum(a_fin) as a_fin, sum(b_prod) as b_prod",
                    "sum(b_dye) as b_dye, sum(b_fin) as b_fin, sum(b_lain) as b_lain",
                    "sum(c_dye) as c_dye, sum(c_fin) as c_fin, sum(c_lain) as c_lain,sum(c_prod) as c_prod"
                ]);
                $barcode = "";
                if ($detail_group === "kp") { 
                    $dataDetail = $ModelDetail->setSelects(["count(barcode) as barcode"])->setGroups([$detail_group])->setOrder(["nama_parent"=>"asc","kp"=>"asc"])->getData();
                     $barcode.="Total ";
                } else {
                    $dataDetail = $ModelDetail->setSelects(["barcode"])->setGroups([$detail_group])->setOrder(["nama_parent"=>"asc","kp"=>"asc","barcode"=>"asc"])->getData();
                }
                $sheet->setCellValue("A{$start_row}", "No");
                $sheet->mergeCells("A{$start_row}:A" . ($start_row + 2));
                $sheet->setCellValue("B{$start_row}", "KP");
                $sheet->mergeCells("B{$start_row}:B" . ($start_row + 2));
                $sheet->setCellValue("c{$start_row}", "{$barcode} Barcode");
                $sheet->mergeCells("c{$start_row}:c" . ($start_row + 2));
                $sheet->setCellValue("D{$start_row}", "MKT");
                $sheet->mergeCells("D{$start_row}:D" . ($start_row + 2));
                $sheet->setCellValue("E{$start_row}", "CORAK");
                $sheet->mergeCells("E{$start_row}:E" . ($start_row + 2));
                $sheet->setCellValue("F{$start_row}", "WARNA");
                $sheet->mergeCells("F{$start_row}:F" . ($start_row + 2));
                $sheet->setCellValue("G{$start_row}", "QTY MTR");
                $sheet->mergeCells("G{$start_row}:G" . ($start_row + 2));
                $sheet->setCellValue("H{$start_row}", "GRADE A");
                $sheet->mergeCells("H{$start_row}:I" . ($start_row));
                $sheet->setCellValue("J{$start_row}", "GRADE A");
                $sheet->mergeCells("J{$start_row}:K" . ($start_row));
                $sheet->setCellValue("L{$start_row}", "GRADE A");
                $sheet->mergeCells("L{$start_row}:M" . ($start_row));
                $sheet->setCellValue("N{$start_row}", "GRADE A");
                $sheet->mergeCells("N{$start_row}:O" . ($start_row));
                $sheet->setCellValue("P{$start_row}", "GRADE B");
                $sheet->mergeCells("P{$start_row}:Q" . ($start_row));
                $sheet->setCellValue("R{$start_row}", "GRADE B");
                $sheet->mergeCells("R{$start_row}:S" . ($start_row));
                $sheet->setCellValue("T{$start_row}", "GRADE B");
                $sheet->mergeCells("T{$start_row}:U" . ($start_row));
                $sheet->setCellValue("V{$start_row}", "GRADE B");
                $sheet->mergeCells("V{$start_row}:W" . ($start_row));
                $sheet->setCellValue("X{$start_row}", "GRADE C");
                $sheet->mergeCells("X{$start_row}:Y" . ($start_row));
                $sheet->setCellValue("Z{$start_row}", "GRADE C");
                $sheet->mergeCells("Z{$start_row}:AA" . ($start_row));
                $sheet->setCellValue("AB{$start_row}", "GRADE C");
                $sheet->mergeCells("AB{$start_row}:AC" . ($start_row));
                $sheet->setCellValue("AD{$start_row}", "GRADE C");
                $sheet->mergeCells("AD{$start_row}:AE" . ($start_row));
                $start_row += 1;
                $sheet->setCellValue("H{$start_row}", "MTR");
                $sheet->setCellValue("I{$start_row}", "%");
                $sheet->setCellValue("J{$start_row}", "MTR");
                $sheet->setCellValue("K{$start_row}", "%");
                $sheet->setCellValue("L{$start_row}", "MTR");
                $sheet->setCellValue("M{$start_row}", "%");
                $sheet->setCellValue("N{$start_row}", "MTR");
                $sheet->setCellValue("O{$start_row}", "%");
                $sheet->setCellValue("P{$start_row}", "MTR");
                $sheet->setCellValue("Q{$start_row}", "%");
                $sheet->setCellValue("R{$start_row}", "MTR");
                $sheet->setCellValue("S{$start_row}", "%");
                $sheet->setCellValue("T{$start_row}", "MTR");
                $sheet->setCellValue("U{$start_row}", "%");
                $sheet->setCellValue("V{$start_row}", "MTR");
                $sheet->setCellValue("W{$start_row}", "%");
                $sheet->setCellValue("X{$start_row}", "MTR");
                $sheet->setCellValue("Y{$start_row}", "%");
                $sheet->setCellValue("Z{$start_row}", "MTR");
                $sheet->setCellValue("AA{$start_row}", "%");
                $sheet->setCellValue("AB{$start_row}", "MTR");
                $sheet->setCellValue("AC{$start_row}", "%");
                $sheet->setCellValue("AD{$start_row}", "MTR");
                $sheet->setCellValue("AE{$start_row}", "%");
                $start_row += 1;
                $sheet->setCellValue("H{$start_row}", "ALL");
                $sheet->mergeCells("H{$start_row}:I" . ($start_row));
                $sheet->setCellValue("J{$start_row}", "PRODUKSI");
                $sheet->mergeCells("J{$start_row}:K" . ($start_row));
                $sheet->setCellValue("L{$start_row}", "DYEING");
                $sheet->mergeCells("L{$start_row}:M" . ($start_row));
                $sheet->setCellValue("N{$start_row}", "FINISHING");
                $sheet->mergeCells("N{$start_row}:O" . ($start_row));
                $sheet->setCellValue("P{$start_row}", "PRODUKSI");
                $sheet->mergeCells("P{$start_row}:Q" . ($start_row));
                $sheet->setCellValue("R{$start_row}", "DYEING");
                $sheet->mergeCells("R{$start_row}:S" . ($start_row));
                $sheet->setCellValue("T{$start_row}", "FINISHING");
                $sheet->mergeCells("T{$start_row}:U" . ($start_row));
                $sheet->setCellValue("V{$start_row}", "LAIN-LAIN");
                $sheet->mergeCells("V{$start_row}:W" . ($start_row));
                $sheet->setCellValue("X{$start_row}", "PRODUKSI");
                $sheet->mergeCells("X{$start_row}:Y" . ($start_row));
                $sheet->setCellValue("Z{$start_row}", "DYEING");
                $sheet->mergeCells("Z{$start_row}:AA" . ($start_row));
                $sheet->setCellValue("AB{$start_row}", "FINISHING");
                $sheet->mergeCells("AB{$start_row}:AC" . ($start_row));
                $sheet->setCellValue("AD{$start_row}", "LAIN-LAIN");
                $sheet->mergeCells("AD{$start_row}:AE" . ($start_row));

                
                $no = 0;
                foreach ($dataDetail as $key => $value) {
                    $no += 1;
                    $start_row += 1;
                    $sheet->setCellValue("A{$start_row}", $no);
                    $sheet->setCellValue("B{$start_row}", $value->kp);
                    $sheet->setCellValue("c{$start_row}", $value->barcode ?? "");
                    $sheet->setCellValue("D{$start_row}", $value->mkt);
                    $sheet->setCellValue("E{$start_row}", $value->nama_parent);
                    $sheet->setCellValue("F{$start_row}", $value->warna);
                    $sheet->setCellValue("G{$start_row}", $value->all);
                    $sheet->setCellValue("H{$start_row}", $value->a_gjd);
                    $sheet->setCellValue("I{$start_row}", ($value->a_gjd > 0) ? round(($value->a_gjd / $value->all * 100),2) : 0);
                    $sheet->setCellValue("J{$start_row}", $value->a_prod);
                    $sheet->setCellValue("K{$start_row}", ($value->a_prod > 0) ? round(($value->a_prod / $value->all * 100),2) : 0);
                    $sheet->setCellValue("L{$start_row}", $value->a_dye);
                    $sheet->setCellValue("M{$start_row}", ($value->a_dye > 0) ? round(($value->a_dye / $value->all * 100),2) : 0);
                    $sheet->setCellValue("N{$start_row}", $value->a_fin);
                    $sheet->setCellValue("O{$start_row}", ($value->a_fin > 0) ? round(($value->a_fin / $value->all * 100),2) : 0);
                    $sheet->setCellValue("P{$start_row}", $value->b_prod);
                    $sheet->setCellValue("Q{$start_row}", ($value->b_prod > 0) ? round(($value->b_prod / $value->all * 100),2) : 0);
                    $sheet->setCellValue("R{$start_row}", $value->b_dye);
                    $sheet->setCellValue("S{$start_row}", ($value->b_dye > 0) ? round(($value->b_dye / $value->all * 100),2) : 0);
                    $sheet->setCellValue("T{$start_row}", $value->b_fin);
                    $sheet->setCellValue("U{$start_row}", ($value->b_fin > 0) ? round(($value->b_fin / $value->all * 100),2) : 0);
                    $sheet->setCellValue("V{$start_row}", $value->b_lain);
                    $sheet->setCellValue("W{$start_row}", ($value->b_lain > 0) ? round(($value->b_lain / $value->all * 100),2) : 0);

                    $sheet->setCellValue("X{$start_row}", $value->c_prod);
                    $sheet->setCellValue("Y{$start_row}", ($value->c_prod > 0) ? round(($value->c_prod / $value->all * 100),2) : 0);
                    $sheet->setCellValue("Z{$start_row}", $value->c_dye);
                    $sheet->setCellValue("AA{$start_row}", ($value->c_dye > 0) ? round(($value->c_dye / $value->all * 100),2) : 0);
                    $sheet->setCellValue("AB{$start_row}", $value->c_fin);
                    $sheet->setCellValue("aC{$start_row}", ($value->c_fin > 0) ? round(($value->c_fin / $value->all * 100),2) : 0);
                    $sheet->setCellValue("aD{$start_row}", $value->c_lain);
                    $sheet->setCellValue("aE{$start_row}", ($value->c_lain > 0) ? round(($value->c_lain / $value->all * 100),2) : 0);
                }

//                $sheet->setCellValue("A{$start_row}",$value->);
            }

            $writer = new Xlsx($spreadsheet);
            $ttt = is_null($detail) ? "":"Detail ";
//            $mrkt = ($marketing !== "") ? "({$marketing})":"";
            $filename = "{$ttt}laporan kain cacat periode {$period[0]} - {$period[1]}" ;
            $url = "dist/storages/report/cacat kain";
            if (!is_dir(FCPATH . $url)) {
                mkdir(FCPATH . $url, 0775, TRUE);
            }
            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save(FCPATH . $url . '/' . $filename . '.xlsx');
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil Export', 'icon' => 'fa fa-check', 'text_name' => $filename,
                        'type' => 'success', "data" => base_url($url . '/' . $filename . '.xlsx'))));
        } catch (\Exception $ex) {
            $this->_module->rollbackTransaction();
            $this->output->set_status_header(($ex->getCode() ?? 500))
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        } finally {
            $this->_module->unlock_tabel();
//            $this->m_analisa_kain_cacat->getQuery("DROP TABLE IF EXISTS {$uniqid}{$tbl_mrp_production},{$uniqid}{$tbl_mrp_production_fg_hasil},"
//                    . "{$uniqid}{$tbl_mrp_production_cacat},{$uniqid}{$tbl_mst_produk},{$uniqid}{$tbl_mst_produk_parent},{$uniqid}{$tbl_stock_quant},{$uniqid}{$tbl_mrp_inlet};", false);
        }
    }


}
