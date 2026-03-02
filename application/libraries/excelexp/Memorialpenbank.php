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

class Memorialpenbank {

    protected $notList = ["piutang", "um_penjualan"];
    protected $ket = ["detail" => "Rekapan Kredit", "detail_2" => "Rekapan Debet", "global" => "Global"];

    //put your code here

    public function _data($model, $datas) {
        $nt = implode("','", $this->notList);
        try {
            $data = [];
            $model->setTables("acc_bank_masuk bm")->setJoins("acc_bank_masuk_detail bmd", "bank_masuk_id = bm.id")
                    ->setJoins("acc_coa acbm", "acbm.kode_coa = bm.kode_coa", "left")
                    ->setJoins("acc_coa acbmd", "acbmd.kode_coa = bmd.kode_coa", "left")
                    ->setWheres(["date(bm.tanggal) >=" => $datas['tanggals'][0], "date(bm.tanggal) <=" => $datas['tanggals'][1], "bm.status" => "confirm"])
                    ->setWhereRaw("bmd.kode_coa not in (select kode_coa from acc_coa where jenis_transaksi in ('{$nt}'))")
                    ->setSelects(["if(partner_nama ='',lain2,partner_nama) as partner,bm.no_bm,bmd.kurs"])
                    ->setSelects(["bm.kode_coa,bmd.kode_coa as kode_coa_bmd,acbm.nama,acbmd.nama as nama_bmd,sum(if(bmd.kurs > 1,bmd.nominal,0)) as valas,sum(bmd.nominal*bmd.kurs) as nominals,date(bmd.tanggal) as tanggal"])
                    ->setSelects(['case when transinfo <> "" then CONCAT(transinfo," - ",uraian) else uraian end as uraian'])
                    ->setGroups(["bm.kode_coa"])->setOrder(["bm.kode_coa"]);
            $data["bank_debit"] = $model->getData();
            switch ($datas["filter"]) {
                case "detail":
                    $model->setGroups(["bmd.id", "bm.no_bm"], true)->setOrder(["bmd.kode_coa", "bm.no_bm"], true);
                    $data["bank_kredit"] = $model->getData();
                    break;
                case "detail_2":
                    $model->setGroups(["bmd.id"], true)->setOrder(["bm.kode_coa", "bm.no_bm"], true);
                    $data["bank_debit"] = $model->getData();
                    break;
                default:
                    $model->setGroups(["bmd.kode_coa"], true)->setOrder(["bmd.kode_coa"], true);
                    $data["bank_kredit"] = $model->getData();
                    break;
            }
            $model->setTables("acc_giro_masuk gm")->setJoins("acc_giro_masuk_detail gmd", "giro_masuk_id = gm.id")
                    ->setJoins("acc_coa acgm", "acgm.kode_coa = gm.kode_coa", "left")
                    ->setJoins("acc_coa acgmd", "acgmd.kode_coa = gmd.kode_coa", "left")
                    ->setWheres(["date(gm.tanggal) >=" => $datas['tanggals'][0], "date(gm.tanggal) <=" => $datas['tanggals'][1], "gm.status" => "confirm"])
                    ->setSelects(["gm.kode_coa,acgm.nama,gmd.kode_coa as kode_coa_gmd,acgmd.nama as nama_gmd,sum(if(gmd.kurs > 1,gmd.nominal,0)) as valas,sum(gmd.nominal*gmd.kurs) as nominals"])
                    ->setSelects(["if(partner_nama ='',lain2,partner_nama) as partner,gm.no_gm,gmd.kurs", "date(gmd.tanggal) as tanggal", "transinfo as uraian"])
                    ->setWhereRaw("gmd.kode_coa not in ('{$nt}')")->setGroups(["gm.kode_coa"])->setOrder(["gm.kode_coa"]);
            $data["giro_debit"] = $model->getData();
            switch ($datas["filter"]) {
                case "detail":
                    $model->setGroups(["gmd.id", "gm.no_gm"], true)->setOrder(["gmd.kode_coa", "gm.no_gm"], true);
                    $data["giro_kredit"] = $model->getData();
                    break;
                case "detail_2":
                    $model->setGroups(["gmd.id"], true)->setOrder(["gm.kode_coa", "gm.no_gm"], true);
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
                $sheet->setCellValue("B{$row}", "{$value->nama_bmd}");
                $sheet->setCellValue("C{$row}", "{$value->kode_coa_bmd}");
                $sheet->setCellValue("D{$row}", "{$value->valas}");
                $sheet->setCellValue("F{$row}", "{$value->nominals}");
            }
            $row += 2;
            foreach ($data["giro_debit"] as $key => $value) {
                $totalDebit += $value->nominals;
                $row += 1;
                $sheet->setCellValue("A{$row}", ($key === 0) ? "2" : "");
                $sheet->setCellValue("B{$row}", $value->nama);
                $sheet->setCellValue("C{$row}", $value->kode_coa);
                $sheet->setCellValue("D{$row}", $value->valas);
                $sheet->setCellValue("E{$row}", $value->nominals);
            }
            foreach ($data["giro_kredit"] as $key => $value) {
                $totalKredit += $value->nominals;
                $row += 1;
                $sheet->setCellValue("B{$row}", $value->nama_gmd);
                $sheet->setCellValue("C{$row}", $value->kode_coa_gmd);
                $sheet->setCellValue("D{$row}", $value->valas);
                $sheet->setCellValue("F{$row}", $value->nominals);
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
                $sheet->setCellValue("B{$row}", $value->no_bm);
                $sheet->setCellValue("C{$row}", $value->uraian);
                $sheet->setCellValue("D{$row}", $value->partner);
                $sheet->setCellValue("E{$row}", $value->valas);
                $sheet->setCellValue("F{$row}", $value->kurs);
                $sheet->setCellValue("G{$row}", $value->nominals);
                $sheet->setCellValue("H{$row}", $value->kode_coa_bmd);
                $sheet->setCellValue("I{$row}", $value->nama_bmd);
                $sheet->setCellValue("J{$row}", $value->nominals);

                if (isset($bank[$key + 1])) {
                    if ($value->kode_coa !== $bank[$key + 1]->kode_coa) {
                        $row += 1;
                        $sheet->setCellValue("E{$row}", $totalBankValas);
                        $sheet->setCellValue("G{$row}", $totalBankKredit);
                        $sheet->setCellValue("I{$row}", "{$value->nama_bmd} Total");
                        $sheet->setCellValue("J{$row}", $totalBankKredit);
                        $row += 1;
                        $totalBankKredit = 0;
                        $totalBankValas = 0;
                    }
                } else {
                    $row += 1;
                    $sheet->setCellValue("E{$row}", $totalBankValas);
                    $sheet->setCellValue("G{$row}", $totalBankKredit);
                    $sheet->setCellValue("I{$row}", "{$value->nama_bmd} Total");
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
                $sheet->setCellValue("B{$row}", $value->no_gm);
                $sheet->setCellValue("C{$row}", $value->uraian);
                $sheet->setCellValue("D{$row}", $value->partner);
                $sheet->setCellValue("E{$row}", $value->valas);
                $sheet->setCellValue("F{$row}", $value->kurs);
                $sheet->setCellValue("G{$row}", $value->nominals);
                $sheet->setCellValue("H{$row}", $value->kode_coa_gmd);
                $sheet->setCellValue("I{$row}", $value->nama_gmd);
                $sheet->setCellValue("J{$row}", $value->nominals);

                if (isset($giro[$key + 1])) {
                    if ($value->kode_coa_gmd !== $giro[$key + 1]->kode_coa_gmd) {
                        $row += 1;
                        $sheet->setCellValue("E{$row}", $totalGiroValas);
                        $sheet->setCellValue("G{$row}", $totalGiroKredit);
                        $sheet->setCellValue("I{$row}", "{$value->nama_gmd} Total");
                        $sheet->setCellValue("J{$row}", $totalGiroKredit);
                        $row += 1;
                        $totalGiroKredit = 0;
                        $totalGiroValas = 0;
                    }
                } else {
                    $row += 1;
                    $sheet->setCellValue("E{$row}", $totalGiroValas);
                    $sheet->setCellValue("G{$row}", $totalGiroKredit);
                    $sheet->setCellValue("I{$row}", "{$value->nama_gmd} Total");
                    $sheet->setCellValue("J{$row}", $totalGiroKredit);
                }
            }
            $row += 2;

            if ($grandTotal > 0) {
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

    public function _detail_2($data, &$filename) {
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
                $sheet->setCellValue("B{$row}", $value->no_bm);
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
                        $totalBankKredit = 0;
                        $totalBankValas = 0;
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
                $sheet->setCellValue("B{$row}", $value->no_gm);
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
