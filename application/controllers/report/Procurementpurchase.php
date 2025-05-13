<?php
defined('BASEPATH') or exit('No Direct Script Acces Allowed');
/**
 * 
 */
class Procurementpurchase extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->is_loggedin(); //cek apakah user sudah login
        $this->load->model("_module"); //load model
        $this->load->model("m_global"); //load model
    }

    public function index()
    {
        $id_dept        = 'RPP';
        $data['id_dept'] = $id_dept;
        $data['date'] = date('Y-m-d', strtotime("-1 month", strtotime(date("Y-m-d")))) . ' - ' . date('Y-m-d');
        $this->load->view('report/v_procurement_purchase', $data);
    }


    protected function getdata()
    {

        $periode = $this->input->post("periode");
        $kode_pp     = $this->input->post("kode_pp");
        $nama_produk = $this->input->post("nama_produk");
        $departemen = $this->input->post("departemen");
        $type = $this->input->post("type");
        $status = $this->input->post("status");
        $period = explode(" - ", $periode);

        if (count($period) < 2) {
            throw new \Exception("Tentukan dahulu periodenya", 500);
        }

        $tanggalAwal = date("Y-m-d H:i:s", strtotime($period[0] . " 00:00:00"));
        $tanggalAkhir = date("Y-m-d H:i:s", strtotime($period[1] . " 23:59:59"));

        $model = new $this->m_global;
        $model->setTables("procurement_purchase pp")
            ->setJoins("procurement_purchase_items ppi", "pp.kode_pp = ppi.kode_pp")
            ->setJoins("departemen d", "pp.warehouse = d.kode", "INNER")
            ->setJoins("mst_status ms", "ppi.status = ms.kode")
            ->setSelects([
                "pp.kode_pp",
                "pp.create_date",
                "pp.type",
                "pp.sales_order",
                "pp.kode_prod",
                "d.nama as departemen",
                "pp.priority",
                "ppi.kode_produk",
                "ppi.nama_produk",
                "ppi.schedule_date",
                "ppi.qty_beli",
                "ppi.uom_beli",
                "ppi.qty",
                "ppi.uom",
                "ppi.reff_notes",
                "pp.status",
                "ms.nama_status",
                "ppi.kode_cfb",
            ])->setOrder(["pp.create_date" => "asc", "ppi.row_order" => "asc"])
            ->setWheres(["pp.create_date >=" => $tanggalAwal, "pp.create_date <=" => $tanggalAkhir]);
        if ($kode_pp !== "") {
            // $condition = array_merge($condition, ['pr.nama LIKE' => '%' . $customer . '%']);

            $model->setWheres(["ppi.kode_pp LIKE" => '%' . $kode_pp . '%']);
        }
        if ($status !== "All") {
            $model->setWheres(["ppi.status =" => $status]);
        }

        if ($departemen !== "") {
            $model->setWheres(["pp.warehouse = " => $departemen]);
        }

        if ($type !== "") {
            $model->setWheres(["pp.type = " => $type]);
        }

        if ($nama_produk !== "") {
            $model->setWheres(["ppi.nama_produk LIKE" =>  '%' . $nama_produk . '%']);
        }


        return $model;
    }


    public function loadData()
    {
        try {

            $dataRecord = [];
            $num        = 0;
            $get = $this->getdata();
            $model = $get->getData();
            foreach ($model as $gd) {
                if ($gd->type == 'mto') {
                    $type = "Make to Order";
                } else if ($gd->type == 'pengiriman') {
                    $type = "Pengiriman";
                } else {
                    $type = '';
                }
                $num++;
                $dataRecord[] = array(
                    'kode_pp' => $gd->kode_pp,
                    'tgl_buat' => $gd->create_date,
                    'type'    => $type,
                    'sales_order'    => $gd->sales_order,
                    'departemen'    => $gd->departemen,
                    'kode_prod'    => $gd->kode_prod,
                    'priority'    => $gd->priority,
                    'kode_produk'      => $gd->kode_produk,
                    'nama_produk'      => $gd->nama_produk,
                    'schedule_date'    => $gd->schedule_date,
                    'qty_beli'         => number_format($gd->qty_beli, 2) . " " . $gd->uom_beli,
                    'qty'              => number_format($gd->qty, 2) . ' ' . $gd->uom,
                    'notes'            => $gd->reff_notes,
                    'status'           => $gd->nama_status,
                    'kode_cfb'         => $gd->kode_cfb
                );
            }

            $this->output->set_status_header(200)
                ->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode(array('message' => 'Data ditemukan', 'icon' => 'fa fa-check', 'type' => 'success', "total_record" => 'Total Data : ' . number_format($num), "record" => $dataRecord)));
        } catch (Exception $ex) {
            $this->output->set_status_header($ex->getCode() ?? 500)
                ->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger', "data" => "")));
        }
    }

    public function export_excel()
    {
        try {
            //code...

            $this->load->library('excel');
            ob_start();

            $periode = $this->input->post("periode");
            $departemen = $this->input->post("departemen");
            $period = explode(" - ", $periode);

            if (count($period) < 2) {
                throw new \Exception("Tentukan dahulu periodenya", 500);
            }

            $tanggalAwal = date("Y-m-d H:i:s", strtotime($period[0] . " 00:00:00"));
            $tanggalAkhir = date("Y-m-d H:i:s", strtotime($period[1] . " 23:59:59"));



            $object = new PHPExcel();
            $object->setActiveSheetIndex(0);

            //bold huruf
            $object->getActiveSheet()->getStyle("A1:U6")->getFont()->setBold(true);


            // SET JUDUL
            $object->getActiveSheet()->SetCellValue('A1', 'Report Procurement Purchase');
            $object->getActiveSheet()->getStyle('A1')->getAlignment()->setIndent(1);
            $object->getActiveSheet()->mergeCells('A1:L1');
            //$object->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $get_dept = $this->_module->get_nama_dept_by_kode($departemen)->row();

            // set Departemen
            $object->getActiveSheet()->SetCellValue('A2', 'Departemen');
            $object->getActiveSheet()->mergeCells('A2:B2');
            $object->getActiveSheet()->SetCellValue('C2', ': ' . $get_dept->nama ?? '');
            $object->getActiveSheet()->mergeCells('C2:D2');


            // set periode
            $object->getActiveSheet()->SetCellValue('A3', 'Periode');
            $object->getActiveSheet()->mergeCells('A3:B3');
            $object->getActiveSheet()->SetCellValue('C3', ': ' . tgl_indo(date('d-m-Y', strtotime($tanggalAwal))) . ' - ' . tgl_indo(date('d-m-Y', strtotime($tanggalAkhir))));
            $object->getActiveSheet()->mergeCells('C3:F3');



            // header table
            $table_head_columns  = array('No', 'Kode PP', 'Tgl dibuat', 'Type', 'Sales Order', 'Production Order', 'Departemen', 'Priority', 'Kode Produk', 'Nama Produk', 'Schedule Date',  'Qty Beli', 'Uom Beli', 'Qty', 'Uom', 'Notes', 'Kode CFB');

            $column = 0;
            $merge  = TRUE;
            $columns = '';
            $count_merge = 0; // untuk jml yg di merge
            foreach ($table_head_columns as $field) {
                $object->getActiveSheet()->setCellValueByColumnAndRow($column, 6, $field);
                $column++;
            }


            $get = $this->getdata();
            $model = $get->getData();
            $rowCount = 7;
            $num      = 1;
            foreach ($model as $val) {

                if ($val->type == 'mto') {
                    $type = "Make to Order";
                } else if ($val->type == 'pengiriman') {
                    $type = "Pengiriman";
                } else {
                    $type = '';
                }

                $object->getActiveSheet()->SetCellValue('A' . $rowCount, ($num++));
                $object->getActiveSheet()->SetCellValue('B' . $rowCount, $val->kode_pp);
                $object->getActiveSheet()->SetCellValue('C' . $rowCount, $val->create_date);
                $object->getActiveSheet()->SetCellValue('D' . $rowCount, $type);
                $object->getActiveSheet()->SetCellValue('E' . $rowCount, $val->sales_order);
                $object->getActiveSheet()->SetCellValue('F' . $rowCount, $val->kode_prod);
                $object->getActiveSheet()->SetCellValue('G' . $rowCount, $val->departemen);
                $object->getActiveSheet()->SetCellValue('H' . $rowCount, $val->priority);
                $object->getActiveSheet()->SetCellValue('I' . $rowCount, $val->kode_produk);
                $object->getActiveSheet()->SetCellValue('J' . $rowCount, $val->nama_produk);
                $object->getActiveSheet()->SetCellValue('K' . $rowCount, $val->schedule_date);
                $object->getActiveSheet()->SetCellValue('L' . $rowCount, $val->qty_beli);
                $object->getActiveSheet()->SetCellValue('M' . $rowCount, $val->uom_beli);
                $object->getActiveSheet()->SetCellValue('N' . $rowCount, $val->qty);
                $object->getActiveSheet()->SetCellValue('O' . $rowCount, $val->uom);
                $object->getActiveSheet()->SetCellValue('P' . $rowCount, $val->reff_notes);
                $object->getActiveSheet()->SetCellValue('Q' . $rowCount, $val->kode_cfb);
                $rowCount++;
            }

            $object = PHPExcel_IOFactory::createWriter($object, 'Excel2007');
            $object->save('php://output');
            $xlsData = ob_get_contents();
            ob_end_clean();
            $nama_dept  = $get_dept->nama;
            $name_file = "Report Procurement Purchase " . $nama_dept . ".xlsx";
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
