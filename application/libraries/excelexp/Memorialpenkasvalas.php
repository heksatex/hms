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
class Memorialpenkasvalas {
    protected $ket = ["detail" => "Rekapan Kredit", "detail_2" => "Rekapan Debet", "global" => "Global"];
    public function _data($model, $datas) {
        try {
            $data = [];
            $model->setTables("acc_kas_masuk km")->setJoins("acc_kas_masuk_detail kmd", "kas_masuk_id = km.id")
                    ->setJoins("acc_coa ackm", "ackm.kode_coa = km.kode_coa", "left")
                    ->setJoins("acc_coa ackmd", "ackmd.kode_coa = kmd.kode_coa", "left")
                    ->setWheres(["date(km.tanggal) >=" => $datas['tanggals'][0], "date(km.tanggal) <=" => $datas['tanggals'][1]])
                    ->setWheres(["km.kode_coa" => "1112.01", "kmd.kurs <>" => 1, "km.status" => "confirm"])
                    ->setSelects(["if(partner_nama ='',lain2,partner_nama) as partner,km.no_km,kmd.kurs"])
                    ->setSelects(["kmd.kode_coa as kode_coa_kmd,if(kmd.kurs > 1,sum(kmd.nominal),0) as valas,sum(kmd.nominal*kmd.kurs) as nominals,ackmd.nama as nama_kmd,km.kode_coa,ackm.nama as nama,date(kmd.tanggal) as tanggal"])
                    ->setSelects(['case when transinfo <> "" then CONCAT(transinfo," - ",GROUP_CONCAT(uraian)) else GROUP_CONCAT(uraian) end as uraian'])
                    ->setGroups(["km.kode_coa"])->setOrder(["km.kode_coa"]);
            $data["kas_debit"] = $model->getData();
            switch ($datas["filter"]) {
                case "detail":
                    $model->setGroups(["kmd.id"], true)->setOrder(["kmd.kode_coa","kmd.no_km","kmd.id"], true);
                    $data["kas_kredit"] = $model->getData();
                    break;
                default:
                    $model->setGroups(["kmd.kode_coa"], true)->setOrder(["kmd.kode_coa"], true);
                    $data["kas_kredit"] = $model->getData();
                    break;
            }
            return $data;
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    public function _global($data, &$filename) {
        try {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->getStyle("C")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
            $row = 1;
            $sheet->setCellValue("A{$row}", 'No');
            $sheet->setCellValue("B{$row}", 'Nama Perkiraan');
            $sheet->setCellValue("C{$row}", 'No Perkiraan');
            $sheet->setCellValue("D{$row}", 'Valas');
            $sheet->setCellValue("E{$row}", 'Debet');
            $sheet->setCellValue("F{$row}", 'Kredit');
            $row += 1;
            $sheet->setCellValue("B{$row}", "Jurnal {$data['jurnal']}");
            $row += 1;
            $sheet->setCellValue("B{$row}", "{$data['periode']}");
            $row += 1;
            $totalDebit = 0;
            foreach ($data["kas_debit"] as $key => $value) {
                $totalDebit += $value->nominals;
                $row += 1;
                $sheet->setCellValue("A{$row}", ($key === 0) ? "1" : "");
                $sheet->setCellValue("B{$row}", "{$value->nama}");
                $sheet->setCellValue("C{$row}", "{$value->kode_coa}");
                $sheet->setCellValue("D{$row}", "{$value->valas}");
                $sheet->setCellValue("E{$row}", "{$value->nominals}");
            }
            $totalKredit = 0;
            foreach ($data["kas_kredit"] as $key => $value) {
                $totalKredit += $value->nominals;
                $row += 1;
                $sheet->setCellValue("B{$row}", "{$value->nama_kmd}");
                $sheet->setCellValue("C{$row}", "{$value->kode_coa_kmd}");
                $sheet->setCellValue("D{$row}", "{$value->valas}");
                $sheet->setCellValue("F{$row}", "{$value->nominals}");
            }
            $row += 2;
            if (($totalDebit + $totalKredit) > 0) {
                $sheet->setCellValue("E{$row}", $totalDebit);
                $sheet->setCellValue("F{$row}", $totalKredit);
            }
            
            $sheet->getStyle("D2:D{$row}")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $sheet->getStyle("E2:E{$row}")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $sheet->getStyle("F2:F{$row}")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            
            $nm = str_replace("/", "_", $data["periode"]);
            $filename = "jurnal {$data['jurnal']} {$nm} {$data["filter"]}";
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

    public function _detail($data, &$filename) {
        try {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->getStyle("H")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
            $row = 1;
            $sheet->setCellValue("A{$row}", 'Tanggal');
            $sheet->setCellValue("B{$row}", 'No Bukti');
            $sheet->setCellValue("C{$row}", 'Uraian');
            $sheet->setCellValue("D{$row}", 'Dari');
            $sheet->setCellValue("E{$row}", 'Valas');
            $sheet->setCellValue("F{$row}", 'Kurs');
            $sheet->setCellValue("G{$row}", 'Nominal');
            $sheet->setCellValue("H{$row}", 'No Perkiraan');
            $sheet->setCellValue("I{$row}", 'Perkiraan Posisi Kredit');
            $sheet->setCellValue("J{$row}", 'Jumlah');

            $totalKasKredit = 0;
            $totalKasValas = 0;
            $grandTotal = 0;
            $grandTotalValas = 0;
            $kas = $data["kas_kredit"];

            foreach ($kas as $key => $value) {
                $row += 1;
                $totalKasKredit += $value->nominals;
                $totalKasValas += $value->valas;
                $grandTotal += $value->nominals;
                $grandTotalValas += $value->valas;

                $sheet->setCellValue("A{$row}", $value->tanggal);
                $sheet->setCellValue("B{$row}", $value->no_km);
                $sheet->setCellValue("C{$row}", $value->uraian);
                $sheet->setCellValue("D{$row}", $value->partner);
                $sheet->setCellValue("E{$row}", $value->valas);
                $sheet->setCellValue("F{$row}", $value->kurs);
                $sheet->setCellValue("G{$row}", $value->nominals);
                $sheet->setCellValue("H{$row}", $value->kode_coa_kmd);
                $sheet->setCellValue("I{$row}", $value->nama_kmd);
                $sheet->setCellValue("J{$row}", $value->nominals);

                if (isset($kas[$key + 1])) {
                    if ($value->kode_coa_kmd !== $kas[$key + 1]->kode_coa_kmd) {
                        $row += 1;
                        $sheet->setCellValue("E{$row}", $totalKasValas);
                        $sheet->setCellValue("G{$row}", $totalKasKredit);
                        $sheet->setCellValue("I{$row}", "{$value->nama_kmd} Total");
                        $sheet->setCellValue("J{$row}", $totalKasKredit);
                        $row += 1;
                        $totalKasKredit = 0;
                        $totalKasValas = 0;
                    }
                } else {
                    $row += 1;
                    $sheet->setCellValue("E{$row}", $totalKasValas);
                    $sheet->setCellValue("G{$row}", $totalKasKredit);
                    $sheet->setCellValue("I{$row}", "{$value->nama_kmd} Total");
                    $sheet->setCellValue("J{$row}", $totalKasKredit);
                }
            }
            if ($grandTotal > 0) {
                $row += 2;
                $sheet->setCellValue("E{$row}", $grandTotalValas);
                $sheet->setCellValue("G{$row}", $grandTotal);
                $sheet->setCellValue("I{$row}", "Grand Total");
                $sheet->setCellValue("J{$row}", $grandTotal);
            }
            
            $sheet->getStyle("E2:E{$row}")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $sheet->getStyle("F2:F{$row}")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $sheet->getStyle("G2:G{$row}")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $sheet->getStyle("J2:J{$row}")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

            $nm = str_replace("/", "_", $data["periode"]);
            $filename = "jurnal {$data['jurnal']} {$nm} {$this->ket[$data["filter"]]}";
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
