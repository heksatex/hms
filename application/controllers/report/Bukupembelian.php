<?php

defined('BASEPATH') or exit('No Direct Script Acces Allowed');


class Bukupembelian extends MY_Controller
{
    //put your code here
    public function __construct()
    {
        parent::__construct();
        $this->is_loggedin(); //cek apakah user sudah login
        $this->load->model('m_bukupembelian');
    }

    public function index()
    {
        $data['id_dept'] = 'RBP';
        $data['date'] = date('Y-m-d', strtotime("-1 month", strtotime(date("Y-m-d")))) . ' - ' . date('Y-m-d');
        $this->load->view('report/v_buku_pembelian', $data);
    }


    function loadData()
    {
        try {
            $callback  = array();
            $periode    = $this->input->post('periode');
            $period = explode(" - ", $periode);
           if (count($period) < 2) {
                throw new \Exception("Tentukan dahulu periodenya", 200);
            } else {

                $departemen  = $this->input->post('departemen');
                $partner  = $this->input->post('partner');
                $uraian= $this->input->post('uraian');
                $no_faktur= $this->input->post('no_faktur');

                $data = $this->proses_data($periode,$departemen,$partner,$uraian,$no_faktur);
                $callback = array('status' => 'success', 'message' =>'berhasil', 'icon' => 'fa fa-check', 'type' => 'success', 'record'=> $data[0], 'total_record'=>'Total Data : ' . number_format($data[1]));

            }

            $this->output->set_status_header(200)
                ->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode($callback));
        } catch (Exception $ex) {
            $this->output->set_status_header(($ex->getCode()) ?? 500)
                ->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode(array('status'=>'failed', 'message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }

    function proses_data($periode,$departemen,$partner,$uraian,$no_faktur) {

        $period = explode(" - ", $periode);
        $tanggalAwal = date("Y-m-d H:i:s", strtotime($period[0] . " 00:00:00"));
        $tanggalAkhir = date("Y-m-d H:i:s", strtotime($period[1] . " 23:59:59"));

        $where = ['inv.status' => 'done','inv.created_at >= ' => $tanggalAwal, 'inv.created_at <= ' => $tanggalAkhir];
        $where_or = array();
        if($departemen){
            $where = array_merge($where,['pod.warehouse'=>$departemen]);
        }
        if($partner){
            $where = array_merge($where, ['p.id' => $partner]);
        }
        if($uraian){
            $where_or = array($uraian);
        }

        if($no_faktur!='All'){
            if($no_faktur == 'ada'){
                $where = array_merge($where, ['inv.no_faktur_pajak <>' => '']);
            } else {
                $where = array_merge($where, ['inv.no_faktur_pajak' => '']);
            }
        }

        $data = $this->m_bukupembelian->get_list_buku_pembelian($where,$where_or);

        $tmp_data = array();
        $num        = 0;
        foreach($data as $datas){
            $num++;
            $tmp_data[]= array(
                            'no_invoice'=>$datas->no_invoice,
                            'tanggal'   =>date('Y-m-d', strtotime($datas->created_at)),
                            'rcv'       =>$datas->origin,
                            'no_po'     =>$datas->no_po,
                            'nama_partner'=> $datas->nama_partner,
                            'departemen'=>$datas->departemen,
                            'uraian'    =>$datas->uraian,
                            'qty_beli'    =>(float) $datas->qty_beli,
                            'uom_beli'    =>$datas->uom_beli,
                            'qty_beli_concat'    =>$datas->qty_beli.' '.$datas->uom_beli,
                            'harga'    =>(float)  $datas->harga_satuan,
                            'currency'    =>$datas->currency,
                            'kurs'        =>(float) $datas->nilai_matauang,
                            'dpp'         =>(float) $datas->dpp,
                            'ppn'         =>(float) $datas->ppn,
                            'no_faktur_pajak'=>$datas->no_faktur_pajak,
                            'tanggal_fk'    =>$datas->tanggal_fk,

            );
        
        }

        return array($tmp_data,$num);

    }


    
    public function export_excel()
    {
        try {
            //code...

            $this->load->library('excel');
            ob_start();

            $periode = $this->input->post("periode");
            $period = explode(" - ", $periode);

            if (count($period) < 2) {
                throw new \Exception("Tentukan dahulu periodenya", 500);
            }

            $departemen = $this->input->post('departemen');
            $partner    = $this->input->post('partner');
            $uraian     = $this->input->post('uraian');
            $no_faktur  = $this->input->post('no_faktur');
            $tanggalAwal = date("Y-m-d H:i:s", strtotime($period[0] . " 00:00:00"));
            $tanggalAkhir = date("Y-m-d H:i:s", strtotime($period[1] . " 23:59:59"));

            $object = new PHPExcel();
            $object->setActiveSheetIndex(0);

            //bold huruf
            $object->getActiveSheet()->getStyle("A1:U5")->getFont()->setBold(true);


            // SET JUDUL
            $object->getActiveSheet()->SetCellValue('A1', 'BUKU PEMBELIAN');
            $object->getActiveSheet()->getStyle('A1')->getAlignment()->setIndent(1);
            $object->getActiveSheet()->mergeCells('A1:Q1');
            $object->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $object->getActiveSheet()->SetCellValue('A3', tgl_indo(date('d-m-Y', strtotime($tanggalAwal))) . ' - ' . tgl_indo(date('d-m-Y', strtotime($tanggalAkhir))));
            $object->getActiveSheet()->mergeCells('A3' . ':Q3');
            $object->getActiveSheet()->getStyle('A3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);


            // header table
            $table_head_columns  = array('No', 'No Invoice', 'Tgl dibuat', 'RCV', 'PO', 'Uraian', 'Supplier', 'Gudang', 'Qty', 'Uom Beli', 'Currency', 'Kurs', 'Harga', 'DPP', 'PPN', 'No Faktur Pajak', 'Tgl FP');

            $column = 0;
            foreach ($table_head_columns as $field) {
                $object->getActiveSheet()->setCellValueByColumnAndRow($column, 5, $field);
                $column++;
            }


            $data = $this->proses_data($periode,$departemen,$partner,$uraian,$no_faktur);

            $rowCount = 6;
            $num      = 1;
            foreach ($data[0] as $val) {

                $object->getActiveSheet()->SetCellValue('A' . $rowCount, ($num++));
                $object->getActiveSheet()->SetCellValue('B' . $rowCount, $val['no_invoice']);
                $object->getActiveSheet()->SetCellValue('C' . $rowCount, $val['tanggal']);
                $object->getActiveSheet()->SetCellValue('D' . $rowCount, $val['rcv']);
                $object->getActiveSheet()->SetCellValue('E' . $rowCount, $val['no_po']);
                $object->getActiveSheet()->SetCellValue('F' . $rowCount, $val['uraian']);
                $object->getActiveSheet()->SetCellValue('G' . $rowCount, $val['nama_partner']);
                $object->getActiveSheet()->SetCellValue('H' . $rowCount, $val['departemen']);
                $object->getActiveSheet()->SetCellValue('I' . $rowCount, $val['qty_beli']);
                $object->getActiveSheet()->SetCellValue('J' . $rowCount, $val['uom_beli']);
                $object->getActiveSheet()->SetCellValue('K' . $rowCount, $val['currency']);
                $object->getActiveSheet()->SetCellValue('L' . $rowCount, $val['kurs']);
                $object->getActiveSheet()->SetCellValue('M' . $rowCount, $val['harga']);
                $object->getActiveSheet()->SetCellValue('N' . $rowCount, $val['dpp']);
                $object->getActiveSheet()->SetCellValue('O' . $rowCount, $val['ppn']);
                $object->getActiveSheet()->SetCellValue('P' . $rowCount, $val['no_faktur_pajak']);
                $object->getActiveSheet()->SetCellValue('Q' . $rowCount, $val['tanggal_fk']);

                $object->getActiveSheet()->getStyle('I'.$rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
                $object->getActiveSheet()->getStyle('L'.$rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
                $object->getActiveSheet()->getStyle('M'.$rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
                $object->getActiveSheet()->getStyle('N'.$rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
                $object->getActiveSheet()->getStyle('O'.$rowCount)->getNumberFormat()->setFormatCode('#,##0.00');

                $rowCount++;
            }

            $object = PHPExcel_IOFactory::createWriter($object, 'Excel2007');
            $object->save('php://output');
            $xlsData = ob_get_contents();
            ob_end_clean();
            $periode_tgl  = tgl_indo(date('d-m-Y', strtotime($tanggalAwal))) . ' - ' . tgl_indo(date('d-m-Y', strtotime($tanggalAkhir)));
            $name_file = "Buku Pembelian " . $periode_tgl . ".xlsx";
            $response =  array(
                'op'        => 'ok',
                'file'      => "data:application/vnd.ms-excel;base64," . base64_encode($xlsData),
                'filename'  => $name_file
            );

            $this->output->set_status_header(200)
                ->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode($response));
        } catch (Exception $ex) {
            $this->output->set_status_header($ex->getCode() ?? 500)
                ->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger', "data" => "")));
        }
    }


}