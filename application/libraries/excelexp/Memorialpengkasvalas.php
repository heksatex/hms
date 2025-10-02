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

class Memorialpengkasvalas {

    public function _data($model, $datas) {
        try {
            $data = [];
            $model->setTables("acc_kas_keluar kk")->setJoins("acc_kas_keluar_detail kkd", "kas_keluar_id = kk.id")
                    ->setJoins("acc_coa ackk", "ackk.kode_coa = kk.kode_coa", "left")
                    ->setJoins("acc_coa ackkd", "ackkd.kode_coa = kkd.kode_coa", "left")
                    ->setWheres(["date(kk.tanggal) >=" => $datas['tanggals'][0], "date(kk.tanggal) <=" => $datas['tanggals'][1]])
                    ->setWheres(["kk.kode_coa" => "1112.01", "kkd.kurs <>" => 1, "kk.status" => "confirm"])
                    ->setSelects(["if(partner_nama ='',lain2,partner_nama) as partner,kk.no_kk,kkd.kurs"])
                    ->setSelects(["kkd.kode_coa as kode_coa_kkd,sum(kkd.nominal) as valas,sum(kkd.nominal*kkd.kurs) as nominals,ackkd.nama as nama_kkd,kk.kode_coa,ackk.nama as nama,date(kkd.tanggal) as tanggal"])
                    ->setGroups(["kkd.kode_coa"])->setOrder(["kkd.kode_coa"]);
            $data["kas_debit"] = $model->getData();
            switch ($datas["filter"]) {
                case "detail":
                    $model->setSelects(["uraian"]);
                    $model->setGroups(["kkd.kode_coa"], true)->setOrder(["kkd.kode_coa"], true);
                    $data["kas_kredit"] = $model->getData();
                    break;
                default:
                    $model->setGroups(["kk.kode_coa"], true)->setOrder(["kk.kode_coa"], true);
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
            $totalDebit = 0;
            foreach ($data["kas_debit"] as $key => $value) {
                $totalDebit += $value->nominals;
                $row += 1;
                $sheet->setCellValue("A{$row}", ($key === 0) ? "1" : "");
                $sheet->setCellValue("B{$row}", " {$value->nama_kkd}");
                $sheet->setCellValue("C{$row}", " {$value->kode_coa_kkd}");
                $sheet->setCellValue("D{$row}", " {$value->valas}");
                $sheet->setCellValue("E{$row}", " {$value->nominals}");
            }
            $totalKredit = 0;
            foreach ($data["kas_kredit"] as $key => $value) {
                $totalKredit += $value->nominals;
                $row += 1;
                $sheet->setCellValue("B{$row}", " {$value->nama}");
                $sheet->setCellValue("C{$row}", " {$value->kode_coa}");
                $sheet->setCellValue("D{$row}", " {$value->valas}");
                $sheet->setCellValue("F{$row}", " {$value->nominals}");
            }
            $row += 2;
            if (($totalDebit + $totalKredit) > 0) {
                $sheet->setCellValue("E{$row}", $totalDebit);
                $sheet->setCellValue("F{$row}", $totalKredit);
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
            $row = 1;
            $sheet->setCellValue("A{$row}", 'Tanggal');
            $sheet->setCellValue("B{$row}", 'No Bukti');
            $sheet->setCellValue("C{$row}", 'Uraian');
            $sheet->setCellValue("D{$row}", 'Dari');
            $sheet->setCellValue("E{$row}", 'Valas');
            $sheet->setCellValue("F{$row}", 'Kurs');
            $sheet->setCellValue("G{$row}", 'Nominal');
            $sheet->setCellValue("H{$row}", 'No Perkiraan');
            $sheet->setCellValue("I{$row}", 'Perkiraan Posisi Debet');
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
                $sheet->setCellValue("B{$row}", $value->no_kk);
                $sheet->setCellValue("C{$row}", $value->uraian);
                $sheet->setCellValue("D{$row}", $value->partner);
                $sheet->setCellValue("E{$row}", $value->valas);
                $sheet->setCellValue("F{$row}", $value->kurs);
                $sheet->setCellValue("G{$row}", $value->nominals);
                $sheet->setCellValue("H{$row}", $value->kode_coa_kkd);
                $sheet->setCellValue("I{$row}", $value->nama_kkd);
                $sheet->setCellValue("J{$row}", $value->nominals);

                if (isset($kas[$key + 1])) {
                    if ($value->kode_coa_kkd !== $kas[$key + 1]->kode_coa_kkd) {
                        $row += 1;
                        $sheet->setCellValue("E{$row}", $totalKasValas);
                        $sheet->setCellValue("G{$row}", $totalKasKredit);
                        $sheet->setCellValue("I{$row}", "{$value->nama_kkd} Total");
                        $sheet->setCellValue("J{$row}", $totalKasKredit);
                        $row += 1;
                        $totalKasKredit = 0;
                        $totalKasValas = 0;
                    }
                } else {
                    $row += 1;
                    $sheet->setCellValue("E{$row}", $totalKasValas);
                    $sheet->setCellValue("G{$row}", $totalKasKredit);
                    $sheet->setCellValue("I{$row}", "{$value->nama_kkd} Total");
                    $sheet->setCellValue("J{$row}", $totalKasKredit);
                }
            }
            if ($grandTotal > 0) {
                $row += 1;
                $sheet->setCellValue("E{$row}", $grandTotalValas);
                $sheet->setCellValue("G{$row}", $grandTotal);
                $sheet->setCellValue("I{$row}", "Grand Total");
                $sheet->setCellValue("J{$row}", $grandTotal);
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
}
