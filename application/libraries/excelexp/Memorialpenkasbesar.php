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
require_once APPPATH . '/third_party/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Memorialpenkasbesar {

    //put your code here
    protected $data, $format = [];

    public function _data($model, $datas) {
        try {
            $data = [];
            $model->setTables("acc_kas_masuk km")->setJoins("acc_kas_masuk_detail kmd", "kmd.kas_masuk_id = km.id")
                    ->setJoins("acc_coa", "acc_coa.kode_coa = kmd.kode_coa", "left")
                    ->setWheres(["date(km.tanggal) >=" => $datas['tanggals'][0], "date(km.tanggal) <=" => $datas['tanggals'][1]])
                    ->setWheres(["km.kode_coa" => "1111.01", "kmd.kurs" => 1, "km.status" => "confirm"])
                    ->setSelects(["sum(nominal) as nominals,km.kode_coa as km_kode_coa", "acc_coa.kode_coa,acc_coa.nama"])
                        ->setOrder(["kmd.kode_coa" => "asc"]);
            if ($datas['filter'] === "detail") {
                $model->setSelects(["transinfo", "uraian", "date(km.tanggal) as tanggal", "km.no_km as no_bukti", "if(partner_nama ='',lain2,partner_nama) as partner"]);
                $model->setGroups(["kmd.kode_coa", "kmd.no_km"], true);
                $data["kredit"] = $model->getData();
                $model->setGroups(["km.kode_coa"]);
                $data["debit"] = $model->getData();
            } else {
                $model->setGroups(["km.kode_coa"]);
                 $data["debit"] = $model->getData();
                 $model->setGroups(["kmd.kode_coa"]);
                 $data["kredit"] = $model->getData();
            }
            return $data;
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    public function _global($data, &$filename) {
        $this->data = $data;
        try {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->getStyle("C")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
            $row = 1;
            $sheet->setCellValue("A{$row}", 'No');
            $sheet->setCellValue("B{$row}", 'Nama Perkiraan');
            $sheet->setCellValue("C{$row}", 'No Perkiraan');
            $sheet->setCellValue("D{$row}", 'Debet');
            $sheet->setCellValue("E{$row}", 'Kredit');
            $row += 1;
            $sheet->setCellValue("B{$row}", "Jurnal {$this->data['jurnal']}");
            $row += 1;
            $sheet->setCellValue("B{$row}", "{$this->data['periode']}");
            $row += 2;
            $sheet->setCellValue("A{$row}", '1');
            $sheet->setCellValue("B{$row}", 'KAS BESAR');
            $sheet->setCellValue("C{$row}", ($this->data["debit"][0]->km_kode_coa ?? ""));
            $sheet->setCellValue("D{$row}", ($this->data["debit"][0]->nominals ?? 0));
            $totalKredit = 0;
            foreach ($this->data["kredit"] as $key => $value) {
                $totalKredit += $value->nominals;
                $row += 1;
                $sheet->setCellValue("B{$row}", "{$value->nama}");
                $sheet->setCellValue("C{$row}", "{$value->kode_coa}");
                $sheet->setCellValue("E{$row}", "{$value->nominals}");
            }
            if ($totalKredit > 0) {
                $row += 2;
                $sheet->setCellValue("D{$row}", ($this->data["debit"][0]->nominals ?? 0));
                $sheet->setCellValue("E{$row}", "{$totalKredit}");
            }
            
            $sheet->getStyle("D2:D{$row}")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $sheet->getStyle("E2:E{$row}")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
//            $sheet->getStyle("F2:F{$row}")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            
            $nm = str_replace("/", "_", $this->data["periode"]);
            $filename = "jurnal {$this->data['jurnal']} {$nm} global";
            $url = "dist/storages/report/jurnal_memorial";
            if (!is_dir(FCPATH . $url)) {
                mkdir(FCPATH . $url, 0775, TRUE);
            }
//            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save(FCPATH . $url . '/' . $filename . '.xlsx');
            return base_url($url . '/' . $filename . '.xlsx');
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    public function _detail($data, &$filename) {
        try {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->getStyle("F")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
            $row = 1;
            $sheet->setCellValue("A{$row}", 'Tanggal');
            $sheet->setCellValue("B{$row}", 'No Bukti');
            $sheet->setCellValue("C{$row}", 'Uraian');
            $sheet->setCellValue("D{$row}", 'Dari');
            $sheet->setCellValue("E{$row}", 'Nominal');
            $sheet->setCellValue("F{$row}", 'No Perkiraan');
            $sheet->setCellValue("G{$row}", 'Perkiraan Posisi Kredit');
            $sheet->setCellValue("H{$row}", 'Jumlah');
            $total = 0;

            $kredits = $data["kredit"];
            foreach ($kredits as $key => $value) {
                $row += 1;
                $total += $value->nominals;
                $sheet->setCellValue("A{$row}", $value->tanggal);
                $sheet->setCellValue("B{$row}", $value->no_bukti);
                $sheet->setCellValue("C{$row}", $value->uraian);
                $sheet->setCellValue("D{$row}", $value->partner);
                $sheet->setCellValue("E{$row}", $value->nominals);
                $sheet->setCellValue("F{$row}", $value->kode_coa);
                $sheet->setCellValue("G{$row}", $value->nama);
                $sheet->setCellValue("H{$row}", $value->nominals);
                if (isset($kredits[$key + 1])) {
                    if ($value->kode_coa !== $kredits[$key + 1]->kode_coa) {
                        $row += 1;
                        $sheet->setCellValue("E{$row}", $total);
                        $sheet->setCellValue("G{$row}", "{$value->nama} Total");
                        $sheet->setCellValue("H{$row}", $total);
                        $row += 1;
                        $total = 0;
                    }
                } else {
                    $row += 1;
                    $sheet->setCellValue("E{$row}", $total);
                    $sheet->setCellValue("G{$row}", "{$value->nama} Total");
                    $sheet->setCellValue("H{$row}", $total);
                }
            }
            
            $sheet->getStyle("H2:H{$row}")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $sheet->getStyle("E2:E{$row}")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            
            $nm = str_replace("/", "_", $data["periode"]);
            $filename = "jurnal {$data['jurnal']} {$nm} detail";
            $url = "dist/storages/report/jurnal_memorial";
            if (!is_dir(FCPATH . $url)) {
                mkdir(FCPATH . $url, 0775, TRUE);
            }
            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save(FCPATH . $url . '/' . $filename . '.xlsx');
            return base_url($url . '/' . $filename . '.xlsx');
        } catch (Exception $ex) {
            throw $ex;
        }
    }
}
