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

class Memorialpembelian {

    //put your code here
    protected $ket = ["detail" => "Rekapan Kredit", "detail_2" => "Rekapan Debet", "global" => "Global"];

    public function _data($model, $datas) {
        try {
            $tanggalAwal = date("Y-m-d H:i:s", strtotime($datas['tanggals'][0] . " 00:00:00"));
            $tanggalAkhir = date("Y-m-d H:i:s", strtotime($datas['tanggals'][1] . " 23:59:59"));

            $data = [];
            $model->setTables("acc_jurnal_entries je")->setJoins("acc_jurnal_entries_items jei", "je.kode = jei.kode")
                    ->setJoins("acc_coa ac", "ac.kode_coa = jei.kode_coa", "left")
                    ->setWheres(["je.tanggal_dibuat >=" => $tanggalAwal, "je.tanggal_dibuat <=" => $tanggalAkhir, "je.status" => "posted", "posisi" => "D", "tipe" => "PB"])
                    ->setGroups(["jei.kode_coa"])->setOrder(["jei.kode_coa"])
                    ->setSelects(["jei.kode_coa,ac.nama as nama_coa,je.reff_note as partner,kurs,if(kurs > 1,sum(jei.nominal_curr),0) as valas,sum(nominal) as nominals,je.tanggal_dibuat as tanggal,jei.nama as uraian,je.kode"]);
            $data["debit"] = $model->getData();
            switch ($datas["filter"]) {
                case "detail":
                    $model->setWheres(["date(je.tanggal_dibuat) >=" => $datas['tanggals'][0], "date(je.tanggal_dibuat) <=" => $datas['tanggals'][1], "je.status" => "posted", "posisi" => "C", "tipe" => "PB"], true);
                    $model->setGroups(["jei.kode_coa", "jei.kode"], true)->setOrder(["jei.kode_coa"], true);
                    $data["kredit"] = $model->getData();
                    break;
                case "detail_2":
                    $model->setGroups(["jei.kode", "jei.kode_coa"], true)->setOrder(["jei.kode_coa"], true);
                    $data["debit"] = $model->getData();
                    break;
                default:
                    $model->setWheres(["date(je.tanggal_dibuat) >=" => $datas['tanggals'][0], "date(je.tanggal_dibuat) <=" => $datas['tanggals'][1], "je.status" => "posted", "posisi" => "C", "tipe" => "PB"], true);
                    $data["kredit"] = $model->getData();
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
            $sheet->getStyle("D")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
            $sheet->getStyle("E")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
            $row = 1;
            $sheet->setCellValue("A{$row}", 'No');
            $sheet->setCellValue("B{$row}", 'Nama Perkiraan');
            $sheet->setCellValue("C{$row}", 'No Perkiraan');
            $sheet->setCellValue("D{$row}", 'Debet');
            $sheet->setCellValue("E{$row}", 'Kredit');
            $row += 1;
            $sheet->setCellValue("B{$row}", "Jurnal {$data['jurnal']}");
            $row += 1;
            $sheet->setCellValue("B{$row}", "{$data['periode']}");
            $row += 1;
            $totalDebit = 0;
            $totalKredit = 0;
            $grandTotalDebit = 0;
            $grandTotalKredit = 0;
            foreach ($data["debit"] as $key => $value) {
                $totalDebit += $value->nominals;
                $grandTotalDebit += $value->nominals;
                $row += 1;
                $sheet->setCellValue("A{$row}", ($key === 0) ? "1" : "");
                $sheet->setCellValue("B{$row}", "{$value->nama_coa}");
                $sheet->setCellValue("C{$row}", "{$value->kode_coa}");
                $sheet->setCellValue("D{$row}", "{$value->nominals}");
            }
            foreach ($data["kredit"] as $key => $value) {
                $totalKredit += $value->nominals;
                $grandTotalKredit += $value->nominals;
                $row += 1;
                $sheet->setCellValue("B{$row}", "{$value->nama_coa}");
                $sheet->setCellValue("C{$row}", "{$value->kode_coa}");
                $sheet->setCellValue("E{$row}", "{$value->nominals}");
            }
            $row += 1;

            if (($grandTotalKredit + $grandTotalDebit) > 0) {
                $sheet->setCellValue("D{$row}", $grandTotalDebit);
                $sheet->setCellValue("E{$row}", $grandTotalKredit);
            }

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
            $sheet->getStyle("F")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
            $sheet->getStyle("E")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
            $sheet->getStyle("H")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
            $row = 1;
            $sheet->setCellValue("A{$row}", 'Tanggal');
            $sheet->setCellValue("B{$row}", 'No Bukti');
            $sheet->setCellValue("C{$row}", 'Uraian');
            $sheet->setCellValue("D{$row}", 'Dari');
            $sheet->setCellValue("E{$row}", 'Nominal');
            $sheet->setCellValue("F{$row}", 'No Perkiraan');
            $sheet->setCellValue("G{$row}", 'Perkiraan Posisi Kredit');
            $sheet->setCellValue("H{$row}", 'Jumlah');

            $grandTotal = 0;
            $total = 0;
            $bank = $data["kredit"] ?? [];
            foreach ($bank as $key => $value) {
                $grandTotal += $value->nominals;
                $total += $value->nominals;
                $row += 1;
                $sheet->setCellValue("A{$row}", $value->tanggal);
                $sheet->setCellValue("B{$row}", $value->kode);
                $sheet->setCellValue("C{$row}", $value->uraian);
                $sheet->setCellValue("D{$row}", $value->partner);
                $sheet->setCellValue("E{$row}", $value->nominals);
                $sheet->setCellValue("F{$row}", $value->kode_coa);
                $sheet->setCellValue("G{$row}", $value->nama_coa);
                $sheet->setCellValue("H{$row}", $value->nominals);

                if (isset($bank[$key + 1])) {
                    if ($value->kode_coa !== $bank[$key + 1]->kode_coa) {
                        $row += 1;
                        $sheet->setCellValue("E{$row}", $total);
                        $sheet->setCellValue("G{$row}", "{$value->nama_coa} Total");
                        $sheet->setCellValue("H{$row}", $total);
                        $total = 0;
                        $row += 1;
                    }
                } else {
                    $row += 1;
                    $sheet->setCellValue("E{$row}", $total);
                    $sheet->setCellValue("G{$row}", "{$value->nama_coa} Total");
                    $sheet->setCellValue("H{$row}", $total);
                    $row += 1;
                }
            }

            if ($grandTotal > 0) {
                $row += 2;
                $sheet->setCellValue("E{$row}", $grandTotal);
                $sheet->setCellValue("G{$row}", "Grand Total");
                $sheet->setCellValue("H{$row}", $grandTotal);
            }
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

    public function _detail_2($data, &$filename) {
        try {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->getStyle("F")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
            $sheet->getStyle("E")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
            $sheet->getStyle("H")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
            $row = 1;
            $sheet->setCellValue("A{$row}", 'Tanggal');
            $sheet->setCellValue("B{$row}", 'No Bukti');
            $sheet->setCellValue("C{$row}", 'Uraian');
            $sheet->setCellValue("D{$row}", 'Dari');
            $sheet->setCellValue("E{$row}", 'Nominal');
            $sheet->setCellValue("F{$row}", 'No Perkiraan');
            $sheet->setCellValue("G{$row}", 'Perkiraan Posisi Debet');
            $sheet->setCellValue("H{$row}", 'Jumlah');

            $grandTotal = 0;
            $total = 0;
            $bank = $data["debit"] ?? [];
            foreach ($bank as $key => $value) {
                $grandTotal += $value->nominals;
                $total += $value->nominals;
                $row += 1;
                $sheet->setCellValue("A{$row}", $value->tanggal);
                $sheet->setCellValue("B{$row}", $value->kode);
                $sheet->setCellValue("C{$row}", $value->uraian);
                $sheet->setCellValue("D{$row}", $value->partner);
                $sheet->setCellValue("E{$row}", $value->nominals);
                $sheet->setCellValue("F{$row}", $value->kode_coa);
                $sheet->setCellValue("G{$row}", $value->nama_coa);
                $sheet->setCellValue("H{$row}", $value->nominals);

                if (isset($bank[$key + 1])) {
                    if ($value->kode_coa !== $bank[$key + 1]->kode_coa) {
                        $row += 1;
                        $sheet->setCellValue("E{$row}", $total);
                        $sheet->setCellValue("G{$row}", "{$value->nama_coa} Total");
                        $sheet->setCellValue("H{$row}", $total);
                        $total = 0;
                        $row += 1;
                    }
                } else {
                    $row += 1;
                    $sheet->setCellValue("E{$row}", $total);
                    $sheet->setCellValue("G{$row}", "{$value->nama_coa} Total");
                    $sheet->setCellValue("H{$row}", $total);
                    $row += 1;
                }
            }

            if ($grandTotal > 0) {
                $row += 2;
                $sheet->setCellValue("E{$row}", $grandTotal);
                $sheet->setCellValue("G{$row}", "Grand Total");
                $sheet->setCellValue("H{$row}", $grandTotal);
            }
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
