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

class Memorialpelhutang {

    //put your code here
    protected $list = ["utang_giro", "utang", "um_pembelian"];
    protected $ket = ["detail" => "Rekapan Kredit", "detail_2" => "Rekapan Debet", "global" => "Global"];

    public function _data($model, $datas) {
        $nt = implode("','", $this->list);
        $data = [];
        try {
            $model->setTables("acc_bank_keluar bk")->setJoins("acc_bank_keluar_detail bkd", "bank_keluar_id = bk.id")
                    ->setJoins("acc_coa acbk", "acbk.kode_coa = bk.kode_coa", "left")
                    ->setJoins("acc_coa acbkd", "acbkd.kode_coa = bkd.kode_coa", "left")
                    ->setWheres(["date(bk.tanggal) >=" => $datas['tanggals'][0], "date(bk.tanggal) <=" => $datas['tanggals'][1], "bk.status" => "confirm"])
                    ->setWhereRaw("bkd.kode_coa in (select kode_coa from acc_coa where jenis_transaksi in ('{$nt}'))")->setSelects(["if(partner_nama ='',lain2,partner_nama) as partner,bk.no_bk,bkd.kurs"])
                    ->setSelects(["bkd.kode_coa as kode_coa_bkd,acbkd.nama as nama_bkd,sum(bkd.nominal) as valas,sum(bkd.nominal*bkd.kurs) as nominals,acbk.nama,bk.kode_coa,date(bkd.tanggal) as tanggal"])
                    ->setSelects(['case when transinfo <> "" then CONCAT(transinfo," - ",GROUP_CONCAT(uraian)) else GROUP_CONCAT(uraian) end as uraian'])
                    ->setGroups(["bkd.kode_coa"])->setOrder(["bkd.kode_coa"]);
            $data["bank_debit"] = $model->getData();
            switch ($datas["filter"]) {
                case "detail":
                    $model->setGroups(["bk.kode_coa", "bkd.no_bk"], true)->setOrder(["bk.kode_coa", "bkd.no_bk"], true)
                        ->setSelects(['case when transinfo <> "" then CONCAT(transinfo," - ",bk.jenis_transaksi) else bk.jenis_transaksi end as uraian']);
                    $data["bank_kredit"] = $model->getData();
                    break;
                case "detail_2":
                    $model->setGroups(["bkd.no_bk"], true)->setOrder(["bkd.kode_coa"], true);
                    $data["bank_debit"] = $model->getData();
                    break;
                default:
                    $model->setGroups(["bk.kode_coa"], true)->setOrder(["bk.kode_coa"], true);
                    $data["bank_kredit"] = $model->getData();
                    break;
            }
            $model->setTables("acc_giro_keluar gk")->setJoins("acc_giro_keluar_detail gkd", "giro_keluar_id = gk.id")
                    ->setJoins("acc_coa acgk", "acgk.kode_coa = gk.kode_coa", "left")
                    ->setJoins("acc_coa acgkd", "acgkd.kode_coa = gkd.kode_coa", "left")
                    ->setWheres(["date(gk.tanggal) >=" => $datas['tanggals'][0], "date(gk.tanggal) <=" => $datas['tanggals'][1], "gk.status" => "confirm"])
                    ->setWhereRaw("gkd.kode_coa in ('{$nt}')")->setGroups(["gkd.kode_coa"])->setOrder(["gkd.kode_coa"])
                    ->setSelects(["gkd.kode_coa as kode_coa_gkd,acgkd.nama as nama_gkd,sum(gkd.nominal) as valas,sum(gkd.nominal*gkd.kurs) as nominals,acgk.nama", "gk.kode_coa"])
                    ->setSelects(["if(partner_nama ='',lain2,partner_nama) as partner,gk.no_gk,gkd.kurs", "date(gkd.tanggal) as tanggal", "transinfo as uraian"]);
            $data["giro_debit"] = $model->getData();
            switch ($datas["filter"]) {
                case "detail":
                    $model->setGroups(["gk.kode_coa", "gkd.no_gk"], true)->setOrder(["gk.kode_coa", "gkd.no_gk"], true);
                    $data["giro_kredit"] = $model->getData();
                    break;
                case "detail_2":
                    $model->setGroups(["gkd.no_gk"], true)->setOrder(["gkd.kode_coa"], true);
                    $data["giro_debit"] = $model->getData();
                    break;
                default:
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
                $sheet->setCellValue("B{$row}", "{$value->nama_bkd}");
                $sheet->setCellValue("C{$row}", "{$value->kode_coa_bkd}");
                $sheet->setCellValue("D{$row}", "{$value->nominals}");
            }
            foreach ($data["bank_kredit"] as $key => $value) {
                $totalKredit += $value->nominals;
                $grandTotalKredit += $value->nominals;
                $row += 1;
                $sheet->setCellValue("B{$row}", "{$value->nama}");
                $sheet->setCellValue("C{$row}", "{$value->kode_coa}");
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
                $sheet->setCellValue("B{$row}", "{$value->nama_gkd}");
                $sheet->setCellValue("C{$row}", "{$value->kode_coa_gkd}");
                $sheet->setCellValue("D{$row}", "{$value->nominals}");
            }
            foreach ($data["giro_kredit"] as $key => $value) {
                $totalKredit += $value->nominals;
                $grandTotalKredit += $value->nominals;
                $row += 1;
                $sheet->setCellValue("B{$row}", "{$value->nama}");
                $sheet->setCellValue("C{$row}", "{$value->kode_coa}");
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
                $sheet->setCellValue("B{$row}", $value->no_bk);
                $sheet->setCellValue("C{$row}", $value->uraian);
                $sheet->setCellValue("D{$row}", $value->partner);
                $sheet->setCellValue("E{$row}", $value->nominals);
                $sheet->setCellValue("F{$row}", $value->kode_coa_bkd);
                $sheet->setCellValue("G{$row}", $value->nama_bkd);
                $sheet->setCellValue("H{$row}", $value->nominals);

                if (isset($bank[$key + 1])) {
                    if ($value->kode_coa_bkd !== $bank[$key + 1]->kode_coa_bkd) {
                        $row += 1;
                        $sheet->setCellValue("E{$row}", $total);
                        $sheet->setCellValue("G{$row}", "{$value->nama_bkd} Total");
                        $sheet->setCellValue("H{$row}", $total);
                        $total = 0;
                        $row += 1;
                    }
                } else {
                    $row += 1;
                    $sheet->setCellValue("E{$row}", $total);
                    $sheet->setCellValue("G{$row}", "{$value->nama_bkd} Total");
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
                $sheet->setCellValue("B{$row}", $value->no_gk);
                $sheet->setCellValue("C{$row}", $value->uraian);
                $sheet->setCellValue("D{$row}", $value->partner);
                $sheet->setCellValue("E{$row}", $value->nominals);
                $sheet->setCellValue("F{$row}", $value->kode_coa_gkd);
                $sheet->setCellValue("G{$row}", $value->nama_gkd);
                $sheet->setCellValue("H{$row}", $value->nominals);

                if (isset($giro[$key + 1])) {
                    if ($value->kode_coa_gkd !== $giro[$key + 1]->kode_coa_gkd) {
                        $row += 1;
                        $sheet->setCellValue("E{$row}", $total);
                        $sheet->setCellValue("G{$row}", "{$value->nama_gkd} Total");
                        $sheet->setCellValue("H{$row}", $total);
                        $total = 0;
                        $row += 1;
                    }
                } else {
                    $row += 1;
                    $sheet->setCellValue("E{$row}", $total);
                    $sheet->setCellValue("G{$row}", "{$value->nama_gkd} Total");
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
            $sheet->getStyle("H2:H{$row}")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $sheet->getStyle("E2:E{$row}")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

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
                $sheet->setCellValue("B{$row}", $value->no_bk);
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
            $giro = $data["giro_kredit"] ?? [];
            foreach ($giro as $key => $value) {
                $grandTotal += $value->nominals;
                $total += $value->nominals;
                $row += 1;
                $sheet->setCellValue("A{$row}", $value->tanggal);
                $sheet->setCellValue("B{$row}", $value->no_gk);
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
            $sheet->getStyle("H2:H{$row}")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $sheet->getStyle("E2:E{$row}")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

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
