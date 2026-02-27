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

class Memorialpelpiutang {

    //put your code here
    protected $list = ['piutang_giro', 'piutang', "um_penjualan"];
    protected $ket = ["detail" => "Rekapan Kredit", "detail_2" => "Rekapan Debet", "global" => "Global"];

    public function _data($model, $datas) {
        $nt = implode("','", $this->list);
        $data = [];
        try {
            $model->setTables("acc_bank_masuk bm")->setJoins("acc_bank_masuk_detail bmd", "bank_masuk_id = bm.id")
                    ->setJoins("acc_coa acbm", "acbm.kode_coa = bm.kode_coa", "left")
                    ->setJoins("acc_coa acbmd", "acbmd.kode_coa = bmd.kode_coa", "left")
                    ->setWheres(["date(bm.tanggal) >=" => $datas['tanggals'][0], "date(bm.tanggal) <=" => $datas['tanggals'][1], "bm.status" => "confirm"])
                    ->setWhereRaw("bmd.kode_coa in (select kode_coa from acc_coa where jenis_transaksi in ('{$nt}'))")->setSelects(["if(partner_nama ='',lain2,partner_nama) as partner,bm.no_bm,bmd.kurs"])
                    ->setSelects(["bmd.kode_coa as kode_coa_bmd,acbmd.nama as nama_bmd,if(bmd.kurs > 1,sum(bmd.nominal),0) as valas,sum(bmd.nominal*bmd.kurs) as nominals,acbm.nama,bm.kode_coa,date(bmd.tanggal) as tanggal"])
                    ->setSelects(['case when transinfo <> "" then CONCAT(transinfo," - ",bm.jenis_transaksi) else bm.jenis_transaksi end as uraian'])
                    ->setGroups(["bm.kode_coa"])->setOrder(["bm.kode_coa"]);
            $data["bank_debit"] = $model->getData();
            switch ($datas["filter"]) {
                case "detail":
                    $model->setGroups(["bmd.kode_coa", "bm.no_bm"], true)->setOrder(["bmd.kode_coa", "bm.no_bm"], true)
                            ->setSelects(['case when transinfo <> "" then CONCAT(transinfo," - ",GROUP_CONCAT(uraian)) else GROUP_CONCAT(uraian) end as uraian']);
                    $data["bank_kredit"] = $model->getData();
                    break;
                case "detail_2":
                    $model->setGroups(["bm.no_bm"], true)->setOrder(["bm.kode_coa"], true);
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
                    ->setWhereRaw("gmd.kode_coa in ('{$nt}')")->setGroups(["gm.kode_coa"])->setOrder(["gm.kode_coa"])
                    ->setSelects(["gmd.kode_coa as kode_coa_gmd,acgmd.nama as nama_gmd,if(gmd.kurs > 1,sum(gmd.nominal),0) as valas,sum(gmd.nominal*gmd.kurs) as nominals,acgm.nama", "gm.kode_coa"])
                    ->setSelects(["if(partner_nama ='',lain2,partner_nama) as partner,gm.no_gm,gmd.kurs", "date(gmd.tanggal) as tanggal", "transinfo as uraian"]);
            $data["giro_debit"] = $model->getData();
            switch ($datas["filter"]) {
                case "detail":
                    $model->setGroups(["gmd.kode_coa", "gm.no_gm"], true)->setOrder(["gmd.kode_coa", "gm.no_gm"], true);
                    $data["giro_kredit"] = $model->getData();
                    break;
                case "detail_2":
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
            $sheet->getStyle("C")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
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
            foreach ($data["bank_debit"] as $key => $value) {
                $totalDebit += $value->nominals;
                $grandTotalDebit += $value->nominals;
                $row += 1;
                $sheet->setCellValue("A{$row}", ($key === 0) ? "1" : "");
                $sheet->setCellValue("B{$row}", "{$value->nama}");
                $sheet->setCellValue("C{$row}", "{$value->kode_coa}");
                $sheet->setCellValue("D{$row}", "{$value->nominals}");
            }
            foreach ($data["bank_kredit"] as $key => $value) {
                $totalKredit += $value->nominals;
                $grandTotalKredit += $value->nominals;
                $row += 1;
                $sheet->setCellValue("B{$row}", "{$value->nama_bmd}");
                $sheet->setCellValue("C{$row}", "{$value->kode_coa_bmd}");
                $sheet->setCellValue("E{$row}", "{$value->nominals}");
            }
            $row += 1;
            if (($totalDebit + $totalKredit) > 0) {
                $sheet->setCellValue("B{$row}", "(Jurnal Pelunasan VIA Bank)");
                $row += 1;
            }

            $totalDebit = 0;
            $totalKredit = 0;
            foreach ($data["giro_debit"] as $key => $value) {
                $totalDebit += $value->nominals;
                $grandTotalDebit += $value->nominals;
                $row += 1;
                $sheet->setCellValue("A{$row}", ($key === 0) ? "2" : "");
                $sheet->setCellValue("B{$row}", "{$value->nama}");
                $sheet->setCellValue("C{$row}", "{$value->kode_coa}");
                $sheet->setCellValue("D{$row}", "{$value->nominals}");
            }
            foreach ($data["giro_kredit"] as $key => $value) {
                $totalKredit += $value->nominals;
                $grandTotalKredit += $value->nominals;
                $row += 1;
                $sheet->setCellValue("B{$row}", "{$value->nama_gmd}");
                $sheet->setCellValue("C{$row}", "{$value->kode_coa_gmd}");
                $sheet->setCellValue("E{$row}", "{$value->nominals}");
            }
            $row += 1;
            if (($totalDebit + $totalKredit) > 0) {
                $sheet->setCellValue("B{$row}", "(Jurnal Pelengkap)");
                $row += 2;
            }
            if (($grandTotalKredit + $grandTotalDebit) > 0) {
                $sheet->setCellValue("D{$row}", $grandTotalDebit);
                $sheet->setCellValue("E{$row}", $grandTotalKredit);
            }
            $sheet->getStyle("D2:D{$row}")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $sheet->getStyle("E2:E{$row}")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

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
            $sheet->getStyle("F")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
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
            $bank = $data["bank_debit"] ?? [];
            foreach ($bank as $key => $value) {
                $grandTotal += $value->nominals;
                $total += $value->nominals;
                $row += 1;
                $sheet->setCellValue("A{$row}", $value->tanggal);
                $sheet->setCellValue("B{$row}", $value->no_bm);
                $sheet->setCellValue("C{$row}", $value->uraian);
                $sheet->setCellValue("D{$row}", $value->partner);
                $sheet->setCellValue("E{$row}", $value->nominals);
                $sheet->setCellValue("F{$row}", $value->kode_coa);
                $sheet->setCellValue("G{$row}", $value->nama);
                $sheet->setCellValue("H{$row}", $value->nominals);

                if (isset($bank[$key + 1])) {
                    if ($value->kode_coa !== $bank[$key + 1]->kode_coa) {
                        $row += 1;
                        $sheet->setCellValue("E{$row}", $total);
                        $sheet->setCellValue("G{$row}", "{$value->nama} Total");
                        $sheet->setCellValue("H{$row}", $total);
                        $total = 0;
                        $row += 1;
                    }
                } else {
                    $row += 1;
                    $sheet->setCellValue("E{$row}", $total);
                    $sheet->setCellValue("G{$row}", "{$value->nama} Total");
                    $sheet->setCellValue("H{$row}", $total);
                    $row += 1;
                }
            }

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
                        $row += 1;
                    }
                } else {
                    $row += 1;
                    $sheet->setCellValue("E{$row}", $total);
                    $sheet->setCellValue("G{$row}", "{$value->nama} Total");
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
            $sheet->getStyle("E2:E{$row}")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $sheet->getStyle("H2:H{$row}")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

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

            $grandTotal = 0;
            $total = 0;
            $bank = $data["bank_kredit"] ?? [];
            foreach ($bank as $key => $value) {
                $grandTotal += $value->nominals;
                $total += $value->nominals;
                $row += 1;
                $sheet->setCellValue("A{$row}", $value->tanggal);
                $sheet->setCellValue("B{$row}", $value->no_bm);
                $sheet->setCellValue("C{$row}", $value->uraian);
                $sheet->setCellValue("D{$row}", $value->partner);
                $sheet->setCellValue("E{$row}", $value->nominals);
                $sheet->setCellValue("F{$row}", $value->kode_coa_bmd);
                $sheet->setCellValue("G{$row}", $value->nama_bmd);
                $sheet->setCellValue("H{$row}", $value->nominals);

                if (isset($bank[$key + 1])) {
                    if ($value->kode_coa_bmd !== $bank[$key + 1]->kode_coa_bmd) {
                        $row += 1;
                        $sheet->setCellValue("E{$row}", $total);
                        $sheet->setCellValue("G{$row}", "{$value->nama_bmd} Total");
                        $sheet->setCellValue("H{$row}", $total);
                        $total = 0;
                        $row += 1;
                    }
                } else {
                    $row += 1;
                    $sheet->setCellValue("E{$row}", $total);
                    $sheet->setCellValue("G{$row}", "{$value->nama_bmd} Total");
                    $sheet->setCellValue("H{$row}", $total);
                    $row += 1;
                }
            }

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
                    if ($value->kode_coa !== $giro[$key + 1]->kode_coa) {
                        $row += 1;
                        $sheet->setCellValue("E{$row}", $total);
                        $sheet->setCellValue("G{$row}", "{$value->nama_gmd} Total");
                        $sheet->setCellValue("H{$row}", $total);
                        $total = 0;
                        $row += 1;
                    }
                } else {
                    $row += 1;
                    $sheet->setCellValue("E{$row}", $total);
                    $sheet->setCellValue("G{$row}", "{$value->nama_gmd} Total");
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
            $sheet->getStyle("E2:E{$row}")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $sheet->getStyle("H2:H{$row}")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

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
