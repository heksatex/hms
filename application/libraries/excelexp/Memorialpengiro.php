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

class Memorialpengiro {

    //put your code here
    protected $list = ['1161.01', '1161.02', '1161.99', '1162.01', '1162.99'];

    public function _data($model, $datas) {
        $nt = implode("','", $this->list);
        $data = [];
        try {
            $model->setTables("acc_giro_masuk gm")->setJoins("acc_giro_masuk_detail gmd", "giro_masuk_id = gm.id")
                    ->setJoins("acc_coa acgm", "acgm.kode_coa = gm.kode_coa", "left")
                    ->setJoins("acc_coa acgmd", "acgmd.kode_coa = gmd.kode_coa", "left")
                    ->setWheres(["date(gm.tanggal) >=" => $datas['tanggals'][0], "date(gm.tanggal) <=" => $datas['tanggals'][1], "gm.status" => "confirm"])
                    ->setWhereRaw("gmd.kode_coa in ('{$nt}')")->setSelects(["if(partner_nama ='',lain2,partner_nama) as partner,gm.no_gm,gmd.kurs"])
                    ->setSelects(["gmd.kode_coa as kode_coa_gmd,sum(gmd.nominal) as valas,sum(gmd.nominal*gmd.kurs) as nominals,acgmd.nama as nama_gmd,gm.kode_coa,acgm.nama as nama,date(gmd.tanggal) as tanggal"])
                    ->setGroups(["gm.kode_coa"])->setOrder(["gm.kode_coa"]);
            $data["giro_debit"] = $model->getData();
            switch ($datas["filter"]) {
                case "detail_2":
                    $model->setSelects(["transinfo as uraian"]);
                    $model->setGroups(["gmd.kode_coa"], true)->setOrder(["gmd.kode_coa"], true);
                    $data["giro_kredit"] = $model->getData();
                    break;
                case "detail":
                    $model->setSelects(["transinfo as uraian"]);
                    $model->setGroups(["gm.no_gm"], true)->setOrder(["gm.kode_coa"], true);
                    $data["giro_debit"] = $model->getData();
                    break;
                default:
                    $model->setGroups(["gmd.kode_coa"], true)->setOrder(["gmd.kode_coa"], true);
                    $data["giro_kredit"] = $model->getData();
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
            $sheet->setCellValue("D{$row}", 'Debet');
            $sheet->setCellValue("E{$row}", 'Kredit');
            $row += 1;
            $sheet->setCellValue("B{$row}", "Jurnal {$data['jurnal']}");
            $row += 1;
            $sheet->setCellValue("B{$row}", "{$data['periode']}");
            $totalDebit = 0;
            $totalKredit = 0;
            foreach ($data["giro_debit"] as $key => $value) {
                $totalDebit += $value->nominals;
                $row += 1;
                $sheet->setCellValue("A{$row}", ($key === 0) ? "1" : "");
                $sheet->setCellValue("B{$row}", "{$value->nama}");
                $sheet->setCellValue("C{$row}", "{$value->kode_coa}");
                $sheet->setCellValue("D{$row}", "{$value->nominals}");
            }
            $row += 1;
            foreach ($data["giro_kredit"] as $key => $value) {
                $totalKredit += $value->nominals;
                $row += 1;
                $sheet->setCellValue("B{$row}", " {$value->nama_gmd}");
                $sheet->setCellValue("C{$row}", " {$value->kode_coa_gmd}");
                $sheet->setCellValue("E{$row}", " {$value->nominals}");
            }
            $row += 2;
            if (($totalDebit + $totalKredit) > 0) {
                $sheet->setCellValue("D{$row}", $totalDebit);
                $sheet->setCellValue("E{$row}", $totalKredit);
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
            $sheet->setCellValue("E{$row}", 'Nominal');
            $sheet->setCellValue("F{$row}", 'No Perkiraan');
            $sheet->setCellValue("G{$row}", 'Perkiraan Posisi Debet');
            $sheet->setCellValue("H{$row}", 'Jumlah');

            $grandTotal = 0;
            $total = 0;
            $giro = $data["giro_debit"] ?? [];
            foreach ($giro as $key => $value) {
                $grandTotal += $value->nominals;
                $total += $value->nominals;
                $row += 1;
                $sheet->setCellValue("A{$row}", $value->tanggal);
                $sheet->setCellValue("B{$row}", $value->no_gm);
                $sheet->setCellValue("C{$row}", $value->uraian);
                $sheet->setCellValue("D{$row}", $value->partner);
                $sheet->setCellValue("E{$row}", $value->nominals);
                $sheet->setCellValue("F{$row}", $value->kode_coa);
                $sheet->setCellValue("G{$row}", $value->nama);
                $sheet->setCellValue("H{$row}", $value->nominals);

                if (isset($giro[$key + 1])) {
                    if ($value->kode_coa !== $giro[$key + 1]->kode_coa) {
                        $row += 1;
                        $sheet->setCellValue("E{$row}", $total);
                        $sheet->setCellValue("G{$row}", "{$value->nama} Total");
                        $sheet->setCellValue("H{$row}", $total);
                        $total = 0;
                    }
                } else {
                    $row += 1;
                    $sheet->setCellValue("E{$row}", $total);
                    $sheet->setCellValue("G{$row}", "{$value->nama} Total");
                    $sheet->setCellValue("H{$row}", $total);
                }
            }
            if ($grandTotal > 0) {
                $row = 2;
                $sheet->setCellValue("E{$row}", $grandTotal);
                $sheet->setCellValue("G{$row}", "Grand Total");
                $sheet->setCellValue("H{$row}", $grandTotal);
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
    
    public function _detail_2($data, &$filename) {
        try {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
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
            $giro = $data["giro_kredit"] ?? [];
            
            foreach ($giro as $key => $value) {
                $grandTotal += $value->nominals;
                $total += $value->nominals;
                $row += 1;
                $sheet->setCellValue("A{$row}", $value->tanggal);
                $sheet->setCellValue("B{$row}", $value->no_gm);
                $sheet->setCellValue("C{$row}", $value->uraian);
                $sheet->setCellValue("D{$row}", $value->partner);
                $sheet->setCellValue("E{$row}", $value->nominals);
                $sheet->setCellValue("F{$row}", $value->kode_coa_gmd);
                $sheet->setCellValue("G{$row}", $value->nama_gmd);
                $sheet->setCellValue("H{$row}", $value->nominals);
                
                if (isset($giro[$key + 1])) {
                    if ($value->kode_coa_gmd !== $giro[$key + 1]->kode_coa_gmd) {
                        $row += 1;
                        $sheet->setCellValue("E{$row}", $total);
                        $sheet->setCellValue("G{$row}", "{$value->nama_gmd} Total");
                        $sheet->setCellValue("H{$row}", $total);
                        $total = 0;
                    }
                } else {
                    $row += 1;
                    $sheet->setCellValue("E{$row}", $total);
                    $sheet->setCellValue("G{$row}", "{$value->nama_gmd} Total");
                    $sheet->setCellValue("H{$row}", $total);
                }
            }
            
            if ($grandTotal > 0) {
                $row += 2;
                $sheet->setCellValue("E{$row}", $grandTotal);
                $sheet->setCellValue("G{$row}", "Grand Total");
                $sheet->setCellValue("H{$row}", $grandTotal);
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
