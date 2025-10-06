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
class Memorialpengbank {

    //put your code here
    protected $notList = ['utang_giro', 'utang', 'um_pembelian'];
    protected $ket = ["detail" => "Rekapan Kredit", "detail_2" => "Rekapan Debet", "global" => "Global"];
    
    public function _data($model, $datas) {
        $nt = implode("','", $this->notList);
        try {
            $data = [];
            $model->setTables("acc_bank_keluar bk")->setJoins("acc_bank_keluar_detail bkd", "bank_keluar_id = bk.id")
                    ->setJoins("acc_coa acbk", "acbk.kode_coa = bk.kode_coa", "left")
                    ->setJoins("acc_coa acbkd", "acbkd.kode_coa = bkd.kode_coa", "left")
                    ->setWheres(["date(bk.tanggal) >=" => $datas['tanggals'][0], "date(bk.tanggal) <=" => $datas['tanggals'][1], "bk.status" => "confirm"])
                    ->setWhereRaw("bkd.kode_coa not in (select kode_coa from acc_coa where jenis_transaksi in ('{$nt}'))")->setGroups(["bkd.kode_coa"])->setOrder(["bkd.kode_coa"])
                    ->setSelects(["bk.kode_coa,bkd.kode_coa as kode_coa_bkd,if(bkd.kurs > 1,sum(bkd.nominal),0) as valas,sum(bkd.nominal*bkd.kurs) as nominals",
                        "acbk.nama as nama,acbkd.nama as nama_bkd", "if(partner_nama ='',lain2,partner_nama) as partner"]);
            $data["bank_debit"] = $model->getData();
            switch ($datas["filter"]) {
                case "detail":
                    $model->setSelects(["bkd.uraian,bkd.no_bk,bkd.tanggal,kurs"]);
                    $model->setGroups(["bk.kode_coa"], true)->setOrder(["bk.kode_coa"], true);
                    $data["bank_kredit"] = $model->getData();
                    break;
                case "detail_2":
                    $model->setSelects(["transinfo as uraian,bkd.no_bk,bkd.tanggal,kurs"]);
                    $model->setGroups(["bkd.no_bk"], true)->setOrder(["bkd.kode_coa"], true);
                    $data["bank_debit"] = $model->getData();
                    break;
                default :
                    $model->setGroups(["bk.kode_coa"], true)->setOrder(["bk.kode_coa"], true);
                    $data["bank_kredit"] = $model->getData();
                    break;
            }

            $model->setTables("acc_giro_keluar gk")->setJoins("acc_giro_keluar_detail gkd", "giro_keluar_id = gk.id")
                    ->setJoins("acc_coa acgk", "acgk.kode_coa = gk.kode_coa", "left")
                    ->setJoins("acc_coa acgkd", "acgkd.kode_coa = gkd.kode_coa", "left")
                    ->setWhereRaw("gkd.kode_coa not in ('{$nt}')")->setGroups(["gkd.kode_coa"])->setOrder(["gkd.kode_coa"])
                    ->setWheres(["date(gk.tanggal) >=" => $datas['tanggals'][0], "date(gk.tanggal) <=" => $datas['tanggals'][1], "gk.status" => "confirm"])
                    ->setSelects(["gk.kode_coa,gkd.kode_coa as kode_coa_gkd,if(gkd.kurs > 1,sum(gkd.nominal),0) as valas,sum(gkd.nominal*gkd.kurs) as nominals",
                        "acgk.nama as nama,acgkd.nama as nama_gkd", "if(partner_nama ='',lain2,partner_nama) as partner"]);
            $data["giro_debit"] = $model->getData();
            switch ($datas["filter"]) {
                case "detail":
                    $model->setSelects(["transinfo as uraian,gkd.no_gk,gkd.tanggal,kurs"]);
                    $model->setGroups(["gk.kode_coa"], true)->setOrder(["gk.kode_coa"], true);
                    $data["giro_kredit"] = $model->getData();
                    break;
                case "detail_2":
                    $model->setSelects(["transinfo as uraian,gkd.no_gk,gkd.tanggal,kurs"]);
                    $model->setGroups(["gkd.no_gk"], true)->setOrder(["gkd.kode_coa"], true);
                    $data["giro_debit"] = $model->getData();
                    break;
                default :
                    $model->setGroups(["gk.kode_coa"], true)->setOrder(["gk.kode_coa"], true);
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
            $sheet->getStyle("C")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
            $sheet->getStyle("D")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
            $sheet->getStyle("E")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
            $sheet->getStyle("F")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
            
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
            foreach ($data["bank_debit"] as $key => $value) {
                $totalDebit += $value->nominals;
                $row += 1;
                $sheet->setCellValue("A{$row}", ($key === 0) ? "1" : "");
                $sheet->setCellValue("B{$row}", "{$value->nama}");
                $sheet->setCellValue("C{$row}", "{$value->kode_coa}");
                $sheet->setCellValue("D{$row}", "{$value->valas}");
                $sheet->setCellValue("E{$row}", "{$value->nominals}");
            }
            $totalKredit = 0;
            foreach ($data["bank_kredit"] as $key => $value) {
                $totalKredit += $value->nominals;
                $row += 1;
                $sheet->setCellValue("B{$row}", "{$value->nama}");
                $sheet->setCellValue("C{$row}", "{$value->kode_coa}");
                $sheet->setCellValue("D{$row}", "{$value->valas}");
                $sheet->setCellValue("F{$row}", "{$value->nominals}");
            }
            //giro
            $row += 1;
            foreach ($data["giro_debit"] as $key => $value) {
                $totalDebit += $value->nominals;
                $row += 1;
                $sheet->setCellValue("A{$row}", ($key === 0) ? "2" : "");
                $sheet->setCellValue("B{$row}", "{$value->nama}");
                $sheet->setCellValue("C{$row}", "{$value->kode_coa}");
                $sheet->setCellValue("D{$row}", "{$value->valas}");
                $sheet->setCellValue("E{$row}", "{$value->nominals}");
            }
            foreach ($data["giro_kredit"] as $key => $value) {
                $totalKredit += $value->nominals;
                $row += 1;
                $sheet->setCellValue("B{$row}", "{$value->nama}");
                $sheet->setCellValue("C{$row}", "{$value->kode_coa}");
                $sheet->setCellValue("D{$row}", "{$value->valas}");
                $sheet->setCellValue("F{$row}", "{$value->nominals}");
            }
            $row += 2;
            if ($totalDebit > 0) {
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
            
        }
    }
    
    public function _detail($data, &$filename) {
        try {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->getStyle("H")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
            $sheet->getStyle("E")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
            $sheet->getStyle("F")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
            $sheet->getStyle("G")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
            $sheet->getStyle("J")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
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

            $totalBankKredit = 0;
            $totalBankValas = 0;
            $grandTotal = 0;
            $grandTotalValas = 0;
            $bank = $data["bank_kredit"];

            foreach ($bank as $key => $value) {
                $row += 1;
                $totalBankKredit += $value->nominals;
                $totalBankValas += $value->valas;
                $grandTotal += $value->nominals;
                $grandTotalValas += $value->valas;

                $sheet->setCellValue("A{$row}", $value->tanggal);
                $sheet->setCellValue("B{$row}", $value->no_bk);
                $sheet->setCellValue("C{$row}", $value->uraian);
                $sheet->setCellValue("D{$row}", $value->partner);
                $sheet->setCellValue("E{$row}", $value->valas);
                $sheet->setCellValue("F{$row}", $value->kurs);
                $sheet->setCellValue("G{$row}", $value->nominals);
                $sheet->setCellValue("H{$row}", $value->kode_coa_bkd);
                $sheet->setCellValue("I{$row}", $value->nama_bkd);
                $sheet->setCellValue("J{$row}", $value->nominals);

                if (isset($bank[$key + 1])) {
                    if ($value->kode_coa_bkd !== $bank[$key + 1]->kode_coa_bkd) {
                        $row += 1;
                        $sheet->setCellValue("E{$row}", $totalBankValas);
                        $sheet->setCellValue("G{$row}", $totalBankKredit);
                        $sheet->setCellValue("I{$row}", "{$value->nama_bkd} Total");
                        $sheet->setCellValue("J{$row}", $totalBankKredit);
                        $row += 1;
                    }
                } else {
                    $row += 1;
                    $sheet->setCellValue("E{$row}", $totalBankValas);
                    $sheet->setCellValue("G{$row}", $totalBankKredit);
                    $sheet->setCellValue("I{$row}", "{$value->nama_bkd} Total");
                    $sheet->setCellValue("J{$row}", $totalBankKredit);
                }
            }
            $row += 1;
            $totalGiroKredit = 0;
            $totalGiroValas = 0;
            $giro = $data["giro_kredit"];

            foreach ($giro as $key => $value) {
                $row += 1;
                $totalGiroKredit += $value->nominals;
                $totalGiroValas += $value->valas;
                $grandTotal += $value->nominals;
                $grandTotalValas += $value->valas;
                $sheet->setCellValue("A{$row}", $value->tanggal);
                $sheet->setCellValue("B{$row}", $value->no_gk);
                $sheet->setCellValue("C{$row}", $value->uraian);
                $sheet->setCellValue("D{$row}", $value->partner);
                $sheet->setCellValue("E{$row}", $value->valas);
                $sheet->setCellValue("F{$row}", $value->kurs);
                $sheet->setCellValue("G{$row}", $value->nominals);
                $sheet->setCellValue("H{$row}", $value->kode_coa_gkd);
                $sheet->setCellValue("I{$row}", $value->nama_gkd);
                $sheet->setCellValue("J{$row}", $value->nominals);

                if (isset($giro[$key + 1])) {
                    if ($value->kode_coa_gkd !== $giro[$key + 1]->kode_coa_gkd) {
                        $row += 1;
                        $sheet->setCellValue("E{$row}", $totalGiroValas);
                        $sheet->setCellValue("G{$row}", $totalGiroKredit);
                        $sheet->setCellValue("I{$row}", "{$value->nama_gkd} Total");
                        $sheet->setCellValue("J{$row}", $totalGiroKredit);
                        $row += 1;
                        $totalGiroKredit = 0;
                        $totalGiroValas = 0;
                    }
                } else {
                    $row += 1;
                    $sheet->setCellValue("E{$row}", $totalGiroValas);
                    $sheet->setCellValue("G{$row}", $totalGiroKredit);
                    $sheet->setCellValue("I{$row}", "{$value->nama_gkd} Total");
                    $sheet->setCellValue("J{$row}", $totalGiroKredit);
                }
            }

            if ($grandTotal > 0) {
                $row += 2;
                $sheet->setCellValue("E{$row}", $grandTotalValas);
                $sheet->setCellValue("G{$row}", $grandTotal);
                $sheet->setCellValue("I{$row}", "Grand Total");
                $sheet->setCellValue("J{$row}", $grandTotal);
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
             $sheet->getStyle("H")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
            $sheet->getStyle("E")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
            $sheet->getStyle("F")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
            $sheet->getStyle("G")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
            $sheet->getStyle("J")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
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

            $totalBankKredit = 0;
            $totalBankValas = 0;
            $grandTotal = 0;
            $grandTotalValas = 0;
            $bank = $data["bank_debit"];

            foreach ($bank as $key => $value) {
                $row += 1;
                $totalBankKredit += $value->nominals;
                $totalBankValas += $value->valas;
                $grandTotal += $value->nominals;
                $grandTotalValas += $value->valas;

                $sheet->setCellValue("A{$row}", $value->tanggal);
                $sheet->setCellValue("B{$row}", $value->no_bk);
                $sheet->setCellValue("C{$row}", $value->uraian);
                $sheet->setCellValue("D{$row}", $value->partner);
                $sheet->setCellValue("E{$row}", $value->valas);
                $sheet->setCellValue("F{$row}", $value->kurs);
                $sheet->setCellValue("G{$row}", $value->nominals);
                $sheet->setCellValue("H{$row}", $value->kode_coa);
                $sheet->setCellValue("I{$row}", $value->nama);
                $sheet->setCellValue("J{$row}", $value->nominals);

                if (isset($bank[$key + 1])) {
                    if ($value->kode_coa !== $bank[$key + 1]->kode_coa) {
                        $row += 1;
                        $sheet->setCellValue("E{$row}", $totalBankValas);
                        $sheet->setCellValue("G{$row}", $totalBankKredit);
                        $sheet->setCellValue("I{$row}", "{$value->nama} Total");
                        $sheet->setCellValue("J{$row}", $totalBankKredit);
                        $row += 1;
                    }
                } else {
                    $row += 1;
                    $sheet->setCellValue("E{$row}", $totalBankValas);
                    $sheet->setCellValue("G{$row}", $totalBankKredit);
                    $sheet->setCellValue("I{$row}", "{$value->nama} Total");
                    $sheet->setCellValue("J{$row}", $totalBankKredit);
                }
            }
            $row += 1;
            $totalGiroKredit = 0;
            $totalGiroValas = 0;
            $giro = $data["giro_debit"];

            foreach ($giro as $key => $value) {
                $row += 1;
                $totalGiroKredit += $value->nominals;
                $totalGiroValas += $value->valas;
                $grandTotal += $value->nominals;
                $grandTotalValas += $value->valas;
                $sheet->setCellValue("A{$row}", $value->tanggal);
                $sheet->setCellValue("B{$row}", $value->no_gk);
                $sheet->setCellValue("C{$row}", $value->uraian);
                $sheet->setCellValue("D{$row}", $value->partner);
                $sheet->setCellValue("E{$row}", $value->valas);
                $sheet->setCellValue("F{$row}", $value->kurs);
                $sheet->setCellValue("G{$row}", $value->nominals);
                $sheet->setCellValue("H{$row}", $value->kode_coa);
                $sheet->setCellValue("I{$row}", $value->nama);
                $sheet->setCellValue("J{$row}", $value->nominals);

                if (isset($giro[$key + 1])) {
                    if ($value->kode_coa !== $giro[$key + 1]->kode_coa) {
                        $row += 1;
                        $sheet->setCellValue("E{$row}", $totalGiroValas);
                        $sheet->setCellValue("G{$row}", $totalGiroKredit);
                        $sheet->setCellValue("I{$row}", "{$value->nama} Total");
                        $sheet->setCellValue("J{$row}", $totalGiroKredit);
                        $row += 1;
                        $totalGiroKredit = 0;
                        $totalGiroValas = 0;
                    }
                } else {
                    $row += 1;
                    $sheet->setCellValue("E{$row}", $totalGiroValas);
                    $sheet->setCellValue("G{$row}", $totalGiroKredit);
                    $sheet->setCellValue("I{$row}", "{$value->nama} Total");
                    $sheet->setCellValue("J{$row}", $totalGiroKredit);
                }
            }

            if ($grandTotal > 0) {
                $row += 2;
                $sheet->setCellValue("E{$row}", $grandTotalValas);
                $sheet->setCellValue("G{$row}", $grandTotal);
                $sheet->setCellValue("I{$row}", "Grand Total");
                $sheet->setCellValue("J{$row}", $grandTotal);
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
