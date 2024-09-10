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
//            "persen" => true,
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
//            "persen" => true,
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
//            "persen" => true,
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
        $this->load->model("_module");
        $this->load->library('pagination');
        $this->load->model("m_produk");
        $this->load->model("m_analisa_kain_cacat");
    }

    public function index() {
        $data['id_dept'] = 'RACK';
        $data['date'] = date('Y-m-d', strtotime("-1 month", strtotime(date("Y-m-d")))) . ' - ' . date('Y-m-d');
        $data['jenis_kain'] = $this->m_produk->get_list_jenis_kain();
        $this->load->view('report/v_analisa_kain_cacat', $data);
    }

    public function export() {
        try {
            $periode = $this->input->post("periode");
            $jenis_kain = $this->input->post("jenis_kain");
            $period = explode(" - ", $periode);
            if (count($period) < 2) {
                throw new \Exception("Tentukan dahulu periodenya", 500);
            }
            $tanggalAwal = date("Y-m-d H:i:s", strtotime($period[0] . " 00:00:00"));
            $tanggalAkhir = date("Y-m-d H:i:s", strtotime($period[1] . " 23:59:59"));
            $wheres = ["mrpp.dept_id" => 'GJD', "mrppfghs.create_date >=" => $tanggalAwal, 'mrppfghs.create_date <=' => $tanggalAkhir, "mrppfghs.lokasi LIKE" => "%Stock"];
            $dataAwal = $this->m_analisa_kain_cacat->setWheres($wheres)
                            ->setGroup("mp.id_sub_parent")
                            ->setSelect("mpsp.nama_sub_parent as nama_produk,mrpp.kode_produk,sum(mrppfghs.qty) as total_qty,GROUP_CONCAT(DISTINCT(mrpp.kode)) as kodes")
                            ->setWhereIn(['id_jenis_kain' => $jenis_kain])->getData();

            $queryDetail = [];
            $querysDetail = [];
            $select = [];
            $totalHphGjd = 0;
            foreach ($dataAwal as $key => $value) {
                $totalHphGjd += $value->total_qty;
                foreach ($this->where as $keys => $values) {
                    $join = false;
                    $persen = "";
                    $setSelect = "sum(qty) as total_qty";
                    if (isset($values["join"])) {
                        $join = true;
                        unset($values["join"]);
                    }
                    if (isset($values["persen"])) {
                        $persen = ",ROUND(((COALESCE(tbl_{$keys}.total_qty,0)*100)/{$value->total_qty}),2)  as persen_{$keys}";
                        unset($values["persen"]);
                    }
                    if (isset($values["select"])) {
                        $setSelect = $values["select"];
                        unset($values["select"]);
                    }

                    $query = $this->m_analisa_kain_cacat->detailTableAwal(array_merge($values, [
                        "mpfh.kode" => [
                            "type" => "in",
                            "data" => explode(",", $value->kodes)
                        ]
                            ]), "tbl_{$keys}", $join, $setSelect);
                    $queryDetail[] = $query;
                    $select[] = "COALESCE(tbl_{$keys}.total_qty,0)  as qty_{$keys} {$persen}";
                }
                $queries = "select " . implode(",", $select) . " from " . implode(",", $queryDetail);
                $querysDetail[] = $queries;
                $queryDetail = [];
                $select = [];
            }
            $querydataKedua = '(' . implode(" ) UNION ALL ( ", $querysDetail) . ')';
//            log_message('error', $querydataKedua);
            $dataKedua = $this->m_analisa_kain_cacat->getQuery($querydataKedua);

//            $dataDetail = $this->load->view("report/v_analisa_kain_cacat_detail", ["data" => $dataAwal, 'data_kedua' => $dataKedua], true);
//            log_message('error', json_encode($dataKedua));
            //table2
            $datas = $this->m_analisa_kain_cacat->getDataCacat();
            $queryDetails = [];
            $querysDetails = [];
            $selectss = [];
            foreach ($datas as $key => $value) {
                foreach ($this->where2 as $keys => $values) {
                    $select = $values["select"];
                    $join = false;
                    if (isset($values["join"])) {
                        $join = true;
                        unset($values["join"]);
                    }
                    unset($values["select"]);
                    $val = array_merge($values, ["mpfh.create_date >=" => $tanggalAwal, 'mpfh.create_date <=' => $tanggalAkhir,
                        "mpc.kode_cacat" => ["type" => "in", "data" => explode(",", $value->kc)], "mp.id_jenis_kain" => ["type" => "in", "data" => $jenis_kain]]);
                    $query = $this->m_analisa_kain_cacat->getQueryTable2($val, "tbl_{$keys}", $select, $join);
                    $queryDetails[] = $query;
                    $selectss[] = "COALESCE(tbl_{$keys}.total_qty,0)  as qty_{$keys}";
                }
                $queries = "select " . implode(",", $selectss) . " from " . implode(",", $queryDetails);
                $querysDetails[] = $queries;
                $queryDetails = [];
                $selectss = [];
            }
            $querydataKeduas = '(' . implode(" ) UNION ALL ( ", $querysDetails) . ')';
            $dataKeduas = $this->m_analisa_kain_cacat->getQuery($querydataKeduas);
//            $dataDetails = $this->load->view("report/v_analisa_kain_cacat_detail_2", ["data" => $datas, 'dataKedua' => $dataKeduas, 'totalhph' => $totalHphGjd], true);
//            $pool = new ApcuCachePool();
//            $sCache = new SimpleCacheBridge($pool);
//            Settings::setCache($sCache);
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $start_row = 2;
//            $sheet->setCellValue('A' . $start_row, 'Produk');
//            $sheet->setCellValue('B' . $start_row, 'Total HPH GJD');
//            $sheet->setCellValue('C' . $start_row, 'Total Kain Grade A');
//            $sheet->setCellValue('D' . $start_row, '');
//            $sheet->setCellValue('E' . $start_row, 'Total Kain Grade Tidak ada cacat DYE dan FIN');
//            $sheet->setCellValue('F' . $start_row, 'persen :- Σ A PRODUKSI thd Σ');
//            $sheet->setCellValue('G' . $start_row, 'total :
//- grade A kain jadi
//- tidak ada cacat produksi (T)
//- tidak ada cacat finishing (F) ');
//            $sheet->setCellValue('H' . $start_row, 'persen :
//- Σ A DYEING thd Σ');
//            $sheet->setCellValue('I' . $start_row, 'total :
//- grade A kain jadi
//- tidak ada cacat produksi (T)
//- tidak ada cacat dyeing (D)');
//            $sheet->setCellValue('J' . $start_row, 'persen :
//- Σ A FINISHING thd Σ');
//
//            $sheet->setCellValue("L" . $start_row, "Master Cacat");
//            $sheet->setCellValue("M" . $start_row, "jumlah point cacat keseluruhan kain = 1 point cacat = 1 mtr, grade B");
//            $sheet->setCellValue("P" . $start_row, "persen total defect thd total hph gjd b22");
//            $sheet->setCellValue("Q" . $start_row, "jumlah point cacat keseluruhan kain = 1 point cacat = 1 mtr, grade C, BS & Potongan");
//            $sheet->setCellValue("R" . $start_row, "BS POT");
//            $sheet->setCellValue("S" . $start_row, "BS POT");
//            $sheet->setCellValue("T" . $start_row, "Tali bubuk, tali");
//            $sheet->setCellValue("U" . $start_row, "Persen Tali");

            $start_row += 1;

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
            $sheet->setCellValue("P" . $start_row, "%B");
            $sheet->setCellValue("Q" . $start_row, "Σ");
            $sheet->setCellValue("R" . $start_row, "Σ");
            $sheet->setCellValue("S" . $start_row, "Σ");
            $sheet->setCellValue("T" . $start_row, "Σ");
            $sheet->setCellValue("U" . $start_row, "%C");

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
            $sheet->setCellValue("Q" . $start_row, "PRODUKSI");
            $sheet->setCellValue("R" . $start_row, "DYEING");
            $sheet->setCellValue("S" . $start_row, "FINISHING");
            $sheet->setCellValue("T" . $start_row, "Lain2");
            $start_row += 2;

            $persenProd = 0;
            $persenDye = 0;
            $persenFin = 0;
            $persenAll = 0;
            $start_row_2 = $start_row;
            $totalGradeA = 0;
            $totalGradeADF = 0;
            $totalGradeATF = 0;
            $totalGradeADT = 0;
            foreach ($dataAwal as $key => $value) {
                $gradeADF = $dataKedua[$key]["qty_cacat_grade_a_DF"];
                $gradeATF = $dataKedua[$key]["qty_cacat_grade_a_TF"];
                $gradeATD = $dataKedua[$key]["qty_cacat_grade_a_TD"];
                $persenProd = ($gradeADF / ($value->total_qty ?? 0)) * 100;
                $persenDye = ($gradeATF / ($value->total_qty ?? 0)) * 100;
                $persenFin = ($gradeATD / ($value->total_qty ?? 0)) * 100;
                $persenAll = ($dataKedua[$key]["qty_total_grade_a_GJD"] / ($value->total_qty ?? 0)) * 100;

                $totalGradeA += $dataKedua[$key]["qty_total_grade_a_GJD"];
                $totalGradeADF += $gradeADF;
                $totalGradeATF += $gradeATF;
                $totalGradeADT += $gradeATD;

                $sheet->setCellValue("A" . $start_row, $value->nama_produk);
                $sheet->setCellValue('B' . $start_row, $value->total_qty ?? 0);
                $sheet->setCellValue('C' . $start_row, $dataKedua[$key]["qty_total_grade_a_GJD"]);
                $sheet->setCellValue('D' . $start_row, round($persenAll, 2));
                $sheet->setCellValue('E' . $start_row, $gradeADF);
                $sheet->setCellValue('F' . $start_row, round($persenProd, 2));
                $sheet->setCellValue('G' . $start_row, $gradeATF);
                $sheet->setCellValue('H' . $start_row, round($persenDye, 2));
                $sheet->setCellValue('I' . $start_row, $gradeATD);
                $sheet->setCellValue('J' . $start_row, round($persenFin, 2));
                $start_row++;
            }
            $start_row += 1;
            $sheet->setCellValue("A" . $start_row, "Σ");
            $sheet->setCellValue("B" . $start_row, $totalHphGjd);
            $sheet->setCellValue("C" . $start_row, $totalGradeA);
            $sheet->setCellValue("D" . $start_row, (($totalGradeA / $totalHphGjd) * 100));
            $sheet->setCellValue("E" . $start_row, $totalGradeADF);
            $sheet->setCellValue("F" . $start_row, (($totalGradeADF / $totalHphGjd) * 100));
            $sheet->setCellValue("G" . $start_row, $totalGradeATF);
            $sheet->setCellValue("H" . $start_row, (($totalGradeATF / $totalHphGjd) * 100));
            $sheet->setCellValue("I" . $start_row, $totalGradeADT);
            $sheet->setCellValue("J" . $start_row, (($totalGradeADT / $totalHphGjd) * 100));

            $totalBAll = 0;
            $totalCAll = 0;
            foreach ($datas as $key => $value) {
                $gradeBt = $dataKeduas[$key]["qty_produksi_b_t"] ?? 0;
                $gradeBd = $dataKeduas[$key]["qty_produksi_b_d"] ?? 0;
                $gradeBf = $dataKeduas[$key]["qty_produksi_b_f"] ?? 0;

                $gradeCt = $dataKeduas[$key]["produksi_c_t_bs_pot"] ?? 0;
                $gradeCd = $dataKeduas[$key]["produksi_c_d_bs"] ?? 0;
                $gradeCf = $dataKeduas[$key]["produksi_c_f_bs"] ?? 0;
                $gradeCtali = $dataKeduas[$key]["produksi_c_tali"] ?? 0;

                $totalB = $gradeBt + $gradeBd + $gradeBf;
                $totalC = $gradeCt + $gradeCd + $gradeCf + $gradeCtali;

                $totalBAll += $totalB;
                $totalCAll += $totalC;
                $sheet->setCellValue("L" . $start_row_2, $value->nama_cacat);
                $sheet->setCellValue("M" . $start_row_2, $gradeBt);
                $sheet->setCellValue("N" . $start_row_2, $gradeBd);
                $sheet->setCellValue("O" . $start_row_2, $gradeBf);
                $sheet->setCellValue("p" . $start_row_2, round(($totalB / $totalHphGjd) * 100, 2));
                $sheet->setCellValue("Q" . $start_row_2, $gradeCt);
                $sheet->setCellValue("R" . $start_row_2, $gradeCd);
                $sheet->setCellValue("S" . $start_row_2, $gradeCf);
                $sheet->setCellValue("T" . $start_row_2, $gradeCtali);
                $sheet->setCellValue("U" . $start_row_2, round(($totalC / $totalHphGjd) * 100, 2));
                $start_row_2++;
            }
            $start_row_2 += 1;
            $sheet->setCellValue("L" . $start_row_2, "Σ");
            $sheet->setCellValue("M" . $start_row_2, $totalBAll);
            $sheet->setCellValue("p" . $start_row_2, round(($totalBAll / $totalHphGjd) * 100, 2));
            $sheet->setCellValue("Q" . $start_row_2, $totalCAll);
            $sheet->setCellValue("U" . $start_row_2, round(($totalCAll / $totalHphGjd) * 100, 2));

            $sheet->setCellValue("A1", "Laporan Kain Cacat Periode Tanggal " . $period[0] . ' - ' . $period[1]);
            $sheet->mergeCells("A1:U1");
            $sheet->getStyle('A1:U1')->getAlignment()->setHorizontal('center');
            $writer = new Xlsx($spreadsheet);
            $filename = "Lap_kain_cacat_periode_" . $period[0] . ' - ' . $period[1];
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
        } catch (Exception $ex) {
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger', "data" => "")));
        }
    }
}
