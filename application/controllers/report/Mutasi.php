<?php defined('BASEPATH') OR EXIT ('No Direct Script Acces Allowed');

/**
 * 
 */
class Mutasi extends MY_Controller
{

    public function __construct()
	{
		parent:: __construct();
		$this->is_loggedin();//cek apakah user sudah login
		$this->load->model('m_mutasi');
		$this->load->model('_module');
	}

    public function index()
	{
		$id_dept        = 'MTSI';
        $data['id_dept']= $id_dept;
		$this->load->view('report/v_mutasi', $data);
	}

    function loadData()
    {

        $tanggal    = $this->input->post('tanggal');
        $departemen = addslashes($this->input->post('departemen'));

        $tahun      = date('Y', strtotime($tanggal)); // example 2022
        $bulan      = date('n', strtotime($tanggal)); // example 8
        $table_mutasi      = [];

        //cek departemen yg ada mutasi
        $result = $this->cek_dept_mutasi($departemen);
        if($result == true){

            
            // cek tipe departemen
            $get_dept  = $this->_module->get_nama_dept_by_kode($departemen);
            $type_dept = $get_dept->row_array();
            if($type_dept['type_dept'] == 'manufaktur'){
                // rm
                $mutasi_dept_rm = $this->m_mutasi->acc_dept_mutasi_by_kode($departemen,'rm')->result();
                $table          = 'acc_mutasi_'.strtolower($departemen).'_rm';
                $result         = $this->create_header($mutasi_dept_rm,false);

                $rm_field          = $result[0];
                $rm_head_table1    = $result[1];
                $rm_head_table2    = $result[2];
                $rm_jml_in         = $result[3];
                $rm_jml_out        = $result[4];
                $acc_mutasi_rm  = $this->get_acc_mutasi_by_kode($table,$tahun,$bulan,$rm_field);

                $table_mutasi[] = array('table_1'       => 'Yes',
                                        'record'        => $acc_mutasi_rm[0], 
                                        'count_record'  => number_format($acc_mutasi_rm[1]),
                                        'head_table1'   => $rm_head_table1, 
                                        'head_table2'   => $rm_head_table2, 
                                        'count_in'      => $rm_jml_in, 
                                        'count_out'     => $rm_jml_out);

                // fg
                $mutasi_dept_fg = $this->m_mutasi->acc_dept_mutasi_by_kode($departemen,'fg')->result();
                $table2         = 'acc_mutasi_'.strtolower($departemen).'_fg';
                $result2        = $this->create_header($mutasi_dept_fg,false);
                $fg_field          = $result2[0];
                $fg_head_table1    = $result2[1];
                $fg_head_table2    = $result2[2];
                $fg_jml_in         = $result2[3];
                $fg_jml_out        = $result2[4];
                $acc_mutasi_fg  = $this->get_acc_mutasi_by_kode($table2,$tahun,$bulan,$fg_field);

                $table_mutasi[]    = array('table_2'       => 'Yes',
                                            'record'        => $acc_mutasi_fg[0],
                                            'count_record'  => number_format($acc_mutasi_fg[1]),
                                            'head_table1'   => $fg_head_table1, 
                                            'head_table2'   => $fg_head_table2, 
                                            'count_in'      => $fg_jml_in, 
                                            'count_out'     => $fg_jml_out);

            }else{
                $mutasi_dept = $this->m_mutasi->acc_dept_mutasi_by_kode($departemen,'')->result();
                $table       = 'acc_mutasi_'.strtolower($departemen);
                $result      = $this->create_header($mutasi_dept,false);

                $field       = $result[0];
                $head_table1 = $result[1];
                $head_table2 = $result[2];
                $jml_in      = $result[3];
                $jml_out     = $result[4];

                // $acc_mutasi  = $this->m_mutasi->acc_mutasi_by_kode($table,$tahun,$bulan,$field)->result();
                $acc_mutasi  = $this->get_acc_mutasi_by_kode($table,$tahun,$bulan,$field);
                $table_mutasi[]    = array('table_1'       => 'Yes',
                                            'record'        => $acc_mutasi[0], 
                                            'count_record'  => number_format($acc_mutasi[1]),
                                            'head_table1'   => $head_table1, 
                                            'head_table2'   => $head_table2, 
                                            'count_in'      => $jml_in, 
                                            'count_out'     => $jml_out);
                $table_mutasi[]    = array('table_2'       => 'No',
                                            'record'        => '', 
                                            'count_record'  => '',
                                            'head_table1'   => '', 
                                            'head_table2'   => '', 
                                            'count_in'      => '', 
                                            'count_out'     => '');

            }
            $callback = array('status'=>'success', 'result'=>$table_mutasi);
        }else{
            $get_dept  = $this->_module->get_nama_dept_by_kode($departemen)->row_array();
            $callback = array('status'=>'failed', 'message'=> "Departemen ".$get_dept['nama']." belum terdapat Laporan Mutasi", 'result'=>$table_mutasi);
        }

        echo json_encode($callback);
    }

    function cek_dept_mutasi($dept)
    {
        $list_dept_mutasi = array('GDB','WRD','TWS','WRP','TRI','JAC','CS','INS1','GRG');
        $dept_status      = false;
        foreach($list_dept_mutasi as $list){
            if($list == $dept){
                $dept_status = true;
                break;
            }
        }
        return $dept_status;
    }

    function get_acc_mutasi_by_kode($table,$tahun,$bulan,$field)
    {
        $query      = $this->m_mutasi->acc_mutasi_by_kode($table,$tahun,$bulan,$field);
        $result     = $query->result_array();
        $result2    = $query->num_rows();
        return array($result,$result2);
    }

    function create_header($mutasi_dept,$excel)
    {
        $field    = '';
        $no_in    = 1;
        $no_out   = 1;
        $head_table1        = [];
        $head_table2        = [];
        $head_table1_in     = [];
        $head_table1_out    = [];
        $head_table2_in     = [];
        $head_table2_out    = [];
        $head_table2_awal   = [];
        $head_table2_akhir  = [];
        // field saldo awal
        $field   .= 'kode_produk, nama_produk, s_awal_lot, s_awal_qty1, s_awal_qty1_uom, s_awal_qty2,  s_awal_qty2_uom, s_awal_qty_opname, s_awal_qty_opname_uom, '; 
        // field saldo akhir
        $field   .= 's_akhir_lot, s_akhir_qty1, s_akhir_qty1_uom, s_akhir_qty2, s_akhir_qty2_uom, s_akhir_qty_opname, s_akhir_qty_opname_uom, ';
        // field adj in 
        $field  .= 'adj_in_lot, adj_in_qty1, adj_in_qty1_uom, adj_in_qty2, adj_in_qty2_uom, adj_in_qty_opname, adj_in_qty_opname_uom, ';
        // field adj in 
        $field  .= 'adj_out_lot, adj_out_qty1, adj_out_qty1_uom, adj_out_qty2, adj_out_qty2_uom, adj_out_qty_opname, adj_out_qty_opname_uom, ';
        // $head_table2_awal[]  = array('S Awal Lot','S Awal Qty1','S Awal Qty2','S Awal Qty Opname');
        if($excel == true){
            $head_table2_awal[]  = array('Lot','Qty1','Uom1','Qty2','Uom2','Qty Opname','Uom Opname');
        }else{
            $head_table2_awal[]  = array('Lot','Qty1','Qty2','Qty Opname');
        }
        // $head_table2_akhir[] = array('S Akhir Lot','S Akhir Qty1','S Akhir Qty2','S Akhir Qty Opname');
        if($excel == true){
            $head_table2_adj[]   = array(' Adj Lot',' Adj Qty1','Uom1',' Adj Qty2', 'Uom2',' Adj Qty Opname','Uom Opname');
        }else{
            $head_table2_adj[]   = array(' Adj Lot',' Adj Qty1',' Adj Qty2',' Adj Qty Opname');
        }
        // $head_table2_adj[]   = array('S Adj Lot','S Adj Qty1','S Adj Qty2','S Adj Qty Opname');

        $jml_in   = 0;
        $jml_out  = 0;

        foreach($mutasi_dept as $mts){
                if (strpos($mts->seq, 'in') !== FALSE) {
                    $int_lot        = 'in'.$no_in.'_lot';
                    $in_qty1        = 'in'.$no_in.'_qty1';
                    $in_qty1_uom    = 'in'.$no_in.'_qty1_uom';
                    $in_qty2        = 'in'.$no_in.'_qty2';
                    $in_qty2_uom    = 'in'.$no_in.'_qty2_uom';
                    $in_opname      = 'in'.$no_in.'_qty_opname';
                    $in_opname_uom  = 'in'.$no_in.'_qty_opname_uom';

                    $field .=  $int_lot.', '.$in_qty1.', '.$in_qty1_uom.', '.$in_qty2.', '.$in_qty2_uom.', '.$in_opname.', '.$in_opname_uom.', ';
                    $no_in++;
                    if($excel == true){
                        $head_table2_in[]  = array('Lot','Qty1','Uom1','Qty2','Uom2','Qty Opname','Uom Opname');
                    }else{
                        $head_table2_in[] = array('Lot','Qty1','Qty2', 'Qty Opname');
                    }
                    $jml_in++;
                    if($mts->type == 'prod'){
                        $judul_column_group_in = 'Hasil Produksi '.$mts->dept_id_dari.'';
                    }else{
                        $judul_column_group_in = 'Penerimaan dari '.$mts->dept_id_dari;
                    }
                    $head_table1_in[] = array($judul_column_group_in);
                    
                }

                if (strpos($mts->seq, 'out') !== FALSE) {
                    $out_lot         = 'out'.$no_out.'_lot';
                    $out_qty1        = 'out'.$no_out.'_qty1';
                    $out_qty1_uom    = 'out'.$no_out.'_qty1_uom';
                    $out_qty2        = 'out'.$no_out.'_qty2';
                    $out_qty2_uom    = 'out'.$no_out.'_qty2_uom';
                    $out_opname      = 'out'.$no_out.'_qty_opname';
                    $out_opname_uom  = 'out'.$no_out.'_qty_opname_uom';

                    $field .=  $out_lot.', '.$out_qty1.', '.$out_qty1_uom.', '.$out_qty2.', '.$out_qty2_uom.', '.$out_opname.', '.$out_opname_uom.', ';
                    $no_out++;
                    if($excel == true){
                        $head_table2_out[]  = array('Lot','Qty1','Uom1','Qty2','Uom2','Qty Opname','Uom Opname');
                    }else{
                        $head_table2_out[] = array('Lot','Qty1','Qty2', 'Qty Opname');
                    }

                    $jml_out++;
                    if($mts->type == 'con'){
                        $judul_column_group_out = 'Bahan Baku Dikomsumsi '.$mts->dept_id_dari.'';
                    }else{
                        $judul_column_group_out = 'Pengiriman ke '.$mts->dept_id_tujuan;
                    }
                    $head_table1_out[] = array($judul_column_group_out);
                }
        }
        $field = rtrim($field,', ');
     

        $head_table1_info[]     = array('No.','Kode Produk', 'Nama Produk');
        $head_table1_awal[]     = array('Saldo Awal');
        $head_table1_akhir[]    = array('Saldo Akhir');
        $head_table1_adj_in[]   = array('Adjustment IN');
        $head_table1_adj_out[]  = array('Adjustment OUT');
        $head_table1_total_in[] = array('Total Penerimaan');
        $head_table1_total_out[]= array('Total Pengiriman');

        $head_table1[] = array('info'   => $head_table1_info,
                                'awal'  => $head_table1_awal,
                                'in'    => $head_table1_in,
                                'adj_in'=> $head_table1_adj_in,
                                'count_in'=> $head_table1_total_in,
                                'out'   => $head_table1_out,
                                'adj_out'=>  $head_table1_adj_out,
                                'count_out'=> $head_table1_total_out,
                                'akhir' => $head_table1_akhir,
                                );

        // header table
        $head_table2[] = array( 'awal'    => $head_table2_awal, 
                                'in'      => $head_table2_in,
                                'adj_in'  => $head_table2_adj, 
                                'count_in'=> $head_table2_awal, 
                                'out'     => $head_table2_out, 
                                'adj_out' => $head_table2_adj,
                                'count_out'=> $head_table2_awal, 
                                'akhir'   => $head_table2_awal);
      
       return array($field,$head_table1,$head_table2,$jml_in,$jml_out);

    }

    function cek_column_excel($index)
    {
        $max    = 200; 
        $result = 'A';
        for ($l = 'A', $i = 1; $i < $max; $l++, $i++) {
            if($i == $index){
                $result = $l;
                break;
            }
        }
        return $result;
    }


    function export_excel_mutasi()
    {

        $tanggal    = $this->input->post('tanggal');
        $departemen = addslashes($this->input->post('departemen'));

        $result = $this->cek_dept_mutasi($departemen);
        if($result == true){


        $this->load->library('excel');
        

        $tahun      = date('Y', strtotime($tanggal)); // example 2022
        $bulan      = date('n', strtotime($tanggal)); // example 8

        $dept    = $this->_module->get_nama_dept_by_kode($departemen)->row_array();


        $object = new PHPExcel();
    	$object->setActiveSheetIndex(0);


        // cek tipe departemen
        $get_dept  = $this->_module->get_nama_dept_by_kode($departemen);
        $type_dept = $get_dept->row_array();
        if($type_dept['type_dept'] == 'manufaktur'){
            // rm
            $mutasi_dept_rm = $this->m_mutasi->acc_dept_mutasi_by_kode($departemen,'rm')->result();
            $table          = 'acc_mutasi_'.strtolower($departemen).'_rm';
            $result         = $this->create_header($mutasi_dept_rm,true);

            $rm_field          = $result[0];
            $rm_head_table1    = $result[1];
            $rm_head_table2    = $result[2];
            $rm_jml_in         = $result[3];
            $rm_jml_out        = $result[4];
            $acc_mutasi_rm     = $this->get_acc_mutasi_by_kode($table,$tahun,$bulan,$rm_field);

            $table_mutasi[] = array('judul'         => "Laporan Mutasi Bahan Baku",
                                    'record'        => $acc_mutasi_rm[0], 
                                    'count_record'  => $acc_mutasi_rm[1],
                                    'head_table1'   => $rm_head_table1, 
                                    'head_table2'   => $rm_head_table2, 
                                    'count_in'      => $rm_jml_in, 
                                    'count_out'     => $rm_jml_out);

            // fg
            $mutasi_dept_fg = $this->m_mutasi->acc_dept_mutasi_by_kode($departemen,'fg')->result();
            $table2         = 'acc_mutasi_'.strtolower($departemen).'_fg';
            $result2        = $this->create_header($mutasi_dept_fg,true);
            $fg_field          = $result2[0];
            $fg_head_table1    = $result2[1];
            $fg_head_table2    = $result2[2];
            $fg_jml_in         = $result2[3];
            $fg_jml_out        = $result2[4];
            $acc_mutasi_fg     = $this->get_acc_mutasi_by_kode($table2,$tahun,$bulan,$fg_field);

            $table_mutasi[]    = array('judul'         => "Laporan Mutasi Barang Jadi",
                                        'record'        => $acc_mutasi_fg[0],
                                        'count_record'  => $acc_mutasi_fg[1],
                                        'head_table1'   => $fg_head_table1, 
                                        'head_table2'   => $fg_head_table2, 
                                        'count_in'      => $fg_jml_in, 
                                        'count_out'     => $fg_jml_out);

        }else{

            $mutasi_dept = $this->m_mutasi->acc_dept_mutasi_by_kode($departemen,'')->result();
            $table       = 'acc_mutasi_'.strtolower($departemen);
            $result      = $this->create_header($mutasi_dept,true);

            $field       = $result[0];
            $head_table1 = $result[1];
            $head_table2 = $result[2];
            $jml_in      = $result[3];
            $jml_out     = $result[4];

            // $acc_mutasi  = $this->m_mutasi->acc_mutasi_by_kode($table,$tahun,$bulan,$field)->result();
            $acc_mutasi  = $this->get_acc_mutasi_by_kode($table,$tahun,$bulan,$field);
            $table_mutasi[]    = array( 'judul'         => "Laporan Mutasi",
                                        'record'        => $acc_mutasi[0], 
                                        'count_record'  => $acc_mutasi[1],
                                        'head_table1'   => $head_table1, 
                                        'head_table2'   => $head_table2, 
                                        'count_in'      => $jml_in, 
                                        'count_out'     => $jml_out);

        }

        $rowCount = 1;

        foreach($table_mutasi as $tm){

        $rowCountFirst = $rowCount;

        // SET JUDUL
 		$object->getActiveSheet()->SetCellValue('A'.$rowCount, $tm['judul']);
 		$object->getActiveSheet()->getStyle('A'.$rowCount)->getAlignment()->setIndent(1);
		$object->getActiveSheet()->mergeCells('A'.$rowCount.':N'.$rowCount);

		// set Departemen
        $rowCount = $rowCount+1;
 		$object->getActiveSheet()->SetCellValue('A'.$rowCount, 'Departemen');
		$object->getActiveSheet()->mergeCells('A'.$rowCount.':B'.$rowCount);
 		$object->getActiveSheet()->SetCellValue('C'.$rowCount, ': '.$dept['nama']);
		$object->getActiveSheet()->mergeCells('C'.$rowCount.':D'.$rowCount);

		// set periode
        $rowCount = $rowCount+1;
 		$object->getActiveSheet()->SetCellValue('A'.$rowCount, 'Periode');
		$object->getActiveSheet()->mergeCells('A'.$rowCount.':B'.$rowCount);
 		$object->getActiveSheet()->SetCellValue('C'.$rowCount, ': '.bln_indo(date('d-m-Y',strtotime($tanggal))) );
		$object->getActiveSheet()->mergeCells('C'.$rowCount.':F'.$rowCount);

        // header table
    	$column   = 0; // A,B,C,D dst
    	$column_e = 1;
        $rowCount   = $rowCount + 3;
    	foreach ($tm['head_table1'] as $field) {
            foreach($field as $field2 => $field22){
                //var_dump($field);
                if($field2 == 'info'){
                    for (  $i = 0, $l = count($field22); $i < $l; $i++ ) {
                        foreach($field22[$i] as $field3 => $field33){
                            $object->getActiveSheet()->setCellValueByColumnAndRow($column, $rowCount, $field33); 
                            $column_excel  = $this->cek_column_excel($column_e);
                            $rowCountMerge = $rowCount + 1;
                            $object->getActiveSheet()->mergeCells($column_excel.''.$rowCount.':'.$column_excel.''.$rowCountMerge);
                            $object->getActiveSheet()->getStyle($column_excel.''.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY);
                            $column++;
                            $column_e++;
                        }
                    }
                }else if($field2 == 'awal' || $field2 == 'akhir' || $field2 == 'in' || $field2 == 'out' || $field2 == 'adj_in' || $field2 == 'adj_out' || $field2 == 'count_in' || $field2 == 'count_out'  ){

                    for ( $i = 0, $l = count($field22); $i < $l; $i++ ) {
                        $column_excel_start  = $this->cek_column_excel($column_e);
                        $object->getActiveSheet()->getStyle($column_excel_start.''.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        foreach($field22[$i] as $field3 => $field33){
                            
                            $object->getActiveSheet()->setCellValueByColumnAndRow($column, $rowCount, $field33); 
                            // $object->getActiveSheet()->mergeCells($column_excel_start.'7:'.$column_excel_finish.'7');
                            $column = $column + 7;
                            $column_e = $column_e + 7;
                        }
                        $column_excel_finish = $this->cek_column_excel($column_e-1);
                        $object->getActiveSheet()->mergeCells($column_excel_start.''.$rowCount.':'.$column_excel_finish.''.$rowCount);

                    }
                    
                }
               
            }
    	}

        // head table 2
        $column   = 3; // A,B,C,D dst
    	$column_e = 1;
        $rowCount = $rowCount + 1;
        foreach ($tm['head_table2'] as $field) {
            foreach($field as $field2 => $field22){
                    for (  $i = 0, $l = count($field22); $i < $l; $i++ ) {
                        foreach($field22[$i] as $field3 => $field33){
                            $object->getActiveSheet()->setCellValueByColumnAndRow($column, $rowCount, $field33); 
                            $column++;
                            $column_e++;
                        }
                    }
            }
    	}

        // bold header table
        $column_excel  = $this->cek_column_excel($column);
        $object->getActiveSheet()->getStyle("A".$rowCountFirst.":".$column_excel."".$rowCountMerge)->getFont()->setBold(true);


        //body
        $no    = 1;
        $rowCount = $rowCount + 1;
    	foreach ($tm['record'] as $row) {
            
            $in_total_lot         = 0;
            $in_total_qty1        = 0;
            $in_total_qty2        = 0;
            $in_total_qty_opname  = 0;
            $out_total_lot         = 0;
            $out_total_qty1        = 0;
            $out_total_qty2        = 0;
            $out_total_qty_opname  = 0;


            $object->getActiveSheet()->SetCellValue('A'.$rowCount, ($no++));
            $object->getActiveSheet()->SetCellValue('B'.$rowCount, $row['kode_produk']);
            $object->getActiveSheet()->SetCellValue('C'.$rowCount, $row['nama_produk']);
            $object->getActiveSheet()->SetCellValue('D'.$rowCount, $row['s_awal_lot']);
            $object->getActiveSheet()->SetCellValue('E'.$rowCount, $row['s_awal_qty1']);
            $object->getActiveSheet()->SetCellValue('F'.$rowCount, $row['s_awal_qty1_uom']);
            $object->getActiveSheet()->SetCellValue('G'.$rowCount, $row['s_awal_qty2']);
            $object->getActiveSheet()->SetCellValue('H'.$rowCount, $row['s_awal_qty2_uom']);
            $object->getActiveSheet()->SetCellValue('I'.$rowCount, $row['s_awal_qty_opname']);
            $object->getActiveSheet()->SetCellValue('J'.$rowCount, $row['s_awal_qty_opname_uom']);

            // info uom
            if($row['s_awal_qty1_uom'] != ''){// jik ada
                $qty1_uom        = $row['s_awal_qty1_uom'];
            }else{
                $qty1_uom        = '';
            }
            if($row['s_awal_qty2_uom'] != ''){
                $qty2_uom        = $row['s_awal_qty2_uom'];
            }else{
                $qty2_uom        = '';
            }
            if($row['s_awal_qty_opname_uom'] != ''){
                $qty_opname_uom  = $row['s_awal_qty_opname_uom'];
            }else{
                $qty_opname_uom  = '';
            }
            
            // IN
            $no_in  = 1;
            $n      = $tm['count_in'];
            $column = 11;
            for ( $d = 0; $d < $n; $d++) {
                $in_lot         = $row['in'.$no_in.'_lot'];
                $in_qty1        = $row['in'.$no_in.'_qty1'];
                $in_qty1_uom    = $row['in'.$no_in.'_qty1_uom'];
                $in_qty2        = $row['in'.$no_in.'_qty2'];
                $in_qty2_uom    = $row['in'.$no_in.'_qty2_uom'];
                $in_opname      = $row['in'.$no_in.'_qty_opname'];
                $in_opname_uom  = $row['in'.$no_in.'_qty_opname_uom'];
                $column_excel  = $this->cek_column_excel($column);
                $object->getActiveSheet()->SetCellValue($column_excel.''.$rowCount, $in_lot);
                $column_excel1  = $this->cek_column_excel($column+1);
                $object->getActiveSheet()->SetCellValue($column_excel1.''.$rowCount, $in_qty1);
                $column_excel2  = $this->cek_column_excel($column+2);
                $object->getActiveSheet()->SetCellValue($column_excel2.''.$rowCount, $in_qty1_uom);
                $column_excel3  = $this->cek_column_excel($column+3);
                $object->getActiveSheet()->SetCellValue($column_excel3.''.$rowCount, $in_qty2);
                $column_excel4  = $this->cek_column_excel($column+4);
                $object->getActiveSheet()->SetCellValue($column_excel4.''.$rowCount, $in_qty2_uom);
                $column_excel5  = $this->cek_column_excel($column+5);
                $object->getActiveSheet()->SetCellValue($column_excel5.''.$rowCount, $in_opname);
                $column_excel6  = $this->cek_column_excel($column+6);
                $object->getActiveSheet()->SetCellValue($column_excel6.''.$rowCount, $in_opname_uom);

                $in_total_lot         = $in_total_lot+($in_lot);
                $in_total_qty1        = $in_total_qty1+($in_qty1);
                $in_total_qty2        = $in_total_qty2+($in_qty2);
                $in_total_qty_opname  = $in_total_qty_opname+($in_opname);

                // info uom
                if($in_qty1_uom != ''){
                    $qty1_uom        = $in_qty1_uom;
                }
                if($in_qty2_uom != ''){
                    $qty2_uom        = $in_qty2_uom;
                }
                if($in_opname_uom != ''){
                    $qty_opname_uom  = $in_opname_uom;
                }

                $column = $column + 7;
                $no_in++;
            }
            
            // ADJ IN
            $column_excel_adj  = $this->cek_column_excel($column);
            $object->getActiveSheet()->SetCellValue($column_excel_adj.''.$rowCount, $row['adj_in_lot']);
            $column_excel_adj  = $this->cek_column_excel($column+1);
            $object->getActiveSheet()->SetCellValue($column_excel_adj.''.$rowCount, $row['adj_in_qty1']);
            $column_excel_adj  = $this->cek_column_excel($column+2);
            $object->getActiveSheet()->SetCellValue($column_excel_adj.''.$rowCount, $row['adj_in_qty1_uom']);
            $column_excel_adj  = $this->cek_column_excel($column+3);
            $object->getActiveSheet()->SetCellValue($column_excel_adj.''.$rowCount, $row['adj_in_qty2']);
            $column_excel_adj  = $this->cek_column_excel($column+4);
            $object->getActiveSheet()->SetCellValue($column_excel_adj.''.$rowCount, $row['adj_in_qty2_uom']);
            $column_excel_adj  = $this->cek_column_excel($column+5);
            $object->getActiveSheet()->SetCellValue($column_excel_adj.''.$rowCount, $row['adj_in_qty_opname']);
            $column_excel_adj  = $this->cek_column_excel($column+6);
            $object->getActiveSheet()->SetCellValue($column_excel_adj.''.$rowCount, $row['adj_in_qty_opname_uom']);

            $in_total_lot         = $in_total_lot+($row['adj_in_lot']);
            $in_total_qty1        = $in_total_qty1+($row['adj_in_qty1']);
            $in_total_qty2        = $in_total_qty2+($row['adj_in_qty2']);
            $in_total_qty_opname  = $in_total_qty_opname+($row['adj_in_qty_opname']);

             // info uom
            if($row['adj_in_qty1_uom'] != ''){
                $qty1_uom        = $row['adj_in_qty1_uom'];
            }
            if($row['adj_in_qty2_uom'] != ''){
                $qty2_uom        = $row['adj_in_qty2_uom'];
            }
            if($row['adj_in_qty_opname_uom'] != ''){
                $qty_opname_uom  = $row['adj_in_qty_opname_uom'];
            }

            $column = $column + 7;

            // Total IN
            $column_excel_tot_in  = $this->cek_column_excel($column);
            $object->getActiveSheet()->SetCellValue($column_excel_tot_in.''.$rowCount, $in_total_lot);
            $column_excel_tot_in  = $this->cek_column_excel($column+1);
            $object->getActiveSheet()->SetCellValue($column_excel_tot_in.''.$rowCount, $in_total_qty1);
            $column_excel_tot_in  = $this->cek_column_excel($column+2);
            $object->getActiveSheet()->SetCellValue($column_excel_tot_in.''.$rowCount, $qty1_uom);
            $column_excel_tot_in  = $this->cek_column_excel($column+3);
            $object->getActiveSheet()->SetCellValue($column_excel_tot_in.''.$rowCount, $in_total_qty2);
            $column_excel_tot_in  = $this->cek_column_excel($column+4);
            $object->getActiveSheet()->SetCellValue($column_excel_tot_in.''.$rowCount, $qty2_uom);
            $column_excel_tot_in  = $this->cek_column_excel($column+5);
            $object->getActiveSheet()->SetCellValue($column_excel_tot_in.''.$rowCount, $in_total_qty_opname);
            $column_excel_tot_in  = $this->cek_column_excel($column+6);
            $object->getActiveSheet()->SetCellValue($column_excel_tot_in.''.$rowCount, $qty_opname_uom);

            $column = $column + 7;

            // OUT
            $no_out = 1;
            $n      = $tm['count_out'];
            for ( $d = 0; $d < $n; $d++) {
              
                $out_lot         = $row['out'.$no_out.'_lot'];
                $out_qty1        = $row['out'.$no_out.'_qty1'];
                $out_qty1_uom    = $row['out'.$no_out.'_qty1_uom'];
                $out_qty2        = $row['out'.$no_out.'_qty2'];
                $out_qty2_uom    = $row['out'.$no_out.'_qty2_uom'];
                $out_opname      = $row['out'.$no_out.'_qty_opname'];
                $out_opname_uom  = $row['out'.$no_out.'_qty_opname_uom'];
                $column_excel  = $this->cek_column_excel($column);
                $object->getActiveSheet()->SetCellValue($column_excel.''.$rowCount, $out_lot);
                $column_excel1  = $this->cek_column_excel($column+1);
                $object->getActiveSheet()->SetCellValue($column_excel1.''.$rowCount, $out_qty1);
                $column_excel2  = $this->cek_column_excel($column+2);
                $object->getActiveSheet()->SetCellValue($column_excel2.''.$rowCount, $out_qty1_uom);
                $column_excel3  = $this->cek_column_excel($column+3);
                $object->getActiveSheet()->SetCellValue($column_excel3.''.$rowCount, $out_qty2);
                $column_excel4  = $this->cek_column_excel($column+4);
                $object->getActiveSheet()->SetCellValue($column_excel4.''.$rowCount, $out_qty2_uom);
                $column_excel5  = $this->cek_column_excel($column+5);
                $object->getActiveSheet()->SetCellValue($column_excel5.''.$rowCount, $out_opname);
                $column_excel6  = $this->cek_column_excel($column+6);
                $object->getActiveSheet()->SetCellValue($column_excel6.''.$rowCount, $out_opname_uom);
 
                $out_total_lot         = $out_total_lot+($out_lot);
                $out_total_qty1        = $out_total_qty1+($out_qty1);
                $out_total_qty2        = $out_total_qty2+($out_qty2);
                $out_total_qty_opname  = $out_total_qty_opname+($out_opname);
 
                // info uom
                if($out_qty1_uom != ''){
                    $qty1_uom        = $out_qty1_uom;
                }
                if($out_qty2_uom != ''){
                    $qty2_uom        = $out_qty2_uom;
                }
                if($out_opname_uom != ''){
                    $qty_opname_uom  = $out_opname_uom;
                }
 
                $column = $column + 7;
                $no_out++;
            }

            // ADJ OUT
            $column_excel_adj  = $this->cek_column_excel($column);
            $object->getActiveSheet()->SetCellValue($column_excel_adj.''.$rowCount, $row['adj_out_lot']);
            $column_excel_adj  = $this->cek_column_excel($column+1);
            $object->getActiveSheet()->SetCellValue($column_excel_adj.''.$rowCount, $row['adj_out_qty1']);
            $column_excel_adj  = $this->cek_column_excel($column+2);
            $object->getActiveSheet()->SetCellValue($column_excel_adj.''.$rowCount, $row['adj_out_qty1_uom']);
            $column_excel_adj  = $this->cek_column_excel($column+3);
            $object->getActiveSheet()->SetCellValue($column_excel_adj.''.$rowCount, $row['adj_out_qty2']);
            $column_excel_adj  = $this->cek_column_excel($column+4);
            $object->getActiveSheet()->SetCellValue($column_excel_adj.''.$rowCount, $row['adj_out_qty2_uom']);
            $column_excel_adj  = $this->cek_column_excel($column+5);
            $object->getActiveSheet()->SetCellValue($column_excel_adj.''.$rowCount, $row['adj_out_qty_opname']);
            $column_excel_adj  = $this->cek_column_excel($column+6);
            $object->getActiveSheet()->SetCellValue($column_excel_adj.''.$rowCount, $row['adj_out_qty_opname_uom']);

            $out_total_lot         = $out_total_lot+($row['adj_out_lot']);
            $out_total_qty1        = $out_total_qty1+($row['adj_out_qty1']);
            $out_total_qty2        = $out_total_qty2+($row['adj_out_qty2']);
            $out_total_qty_opname  = $out_total_qty_opname+($row['adj_out_qty_opname']);

             // info uom
            if($row['adj_out_qty1_uom'] != ''){
                $qty1_uom        = $row['adj_out_qty1_uom'];
            }
            if($row['adj_out_qty2_uom'] != ''){
                $qty2_uom        = $row['adj_out_qty2_uom'];
            }
            if($row['adj_out_qty_opname_uom'] != ''){
                $qty_opname_uom  = $row['adj_out_qty_opname_uom'];
            }

            $column = $column + 7;

            // Total OUT
            $column_excel_tot_out  = $this->cek_column_excel($column);
            $object->getActiveSheet()->SetCellValue($column_excel_tot_out.''.$rowCount, $out_total_lot);
            $column_excel_tot_out  = $this->cek_column_excel($column+1);
            $object->getActiveSheet()->SetCellValue($column_excel_tot_out.''.$rowCount, $out_total_qty1);
            $column_excel_tot_out  = $this->cek_column_excel($column+2);
            $object->getActiveSheet()->SetCellValue($column_excel_tot_out.''.$rowCount, $qty1_uom);
            $column_excel_tot_out  = $this->cek_column_excel($column+3);
            $object->getActiveSheet()->SetCellValue($column_excel_tot_out.''.$rowCount, $out_total_qty2);
            $column_excel_tot_out  = $this->cek_column_excel($column+4);
            $object->getActiveSheet()->SetCellValue($column_excel_tot_out.''.$rowCount, $qty2_uom);
            $column_excel_tot_out  = $this->cek_column_excel($column+5);
            $object->getActiveSheet()->SetCellValue($column_excel_tot_out.''.$rowCount, $in_total_qty_opname);
            $column_excel_tot_out  = $this->cek_column_excel($column+6);
            $object->getActiveSheet()->SetCellValue($column_excel_tot_out.''.$rowCount, $qty_opname_uom);

            $column = $column + 7;

            // Saldo Akhir
            $column_excel_akhir  = $this->cek_column_excel($column);
            $object->getActiveSheet()->SetCellValue($column_excel_akhir.''.$rowCount, $row['s_akhir_lot']);
            $column_excel_akhir  = $this->cek_column_excel($column+1);
            $object->getActiveSheet()->SetCellValue($column_excel_akhir.''.$rowCount, $row['s_akhir_qty1']);
            $column_excel_akhir  = $this->cek_column_excel($column+2);
            $object->getActiveSheet()->SetCellValue($column_excel_akhir.''.$rowCount, $row['s_akhir_qty1_uom']);
            $column_excel_akhir  = $this->cek_column_excel($column+3);
            $object->getActiveSheet()->SetCellValue($column_excel_akhir.''.$rowCount, $row['s_akhir_qty2']);
            $column_excel_akhir  = $this->cek_column_excel($column+4);
            $object->getActiveSheet()->SetCellValue($column_excel_akhir.''.$rowCount, $row['s_akhir_qty2_uom']);
            $column_excel_akhir  = $this->cek_column_excel($column+5);
            $object->getActiveSheet()->SetCellValue($column_excel_akhir.''.$rowCount, $row['s_akhir_qty_opname']);
            $column_excel_akhir  = $this->cek_column_excel($column+6);
            $object->getActiveSheet()->SetCellValue($column_excel_akhir.''.$rowCount, $row['s_akhir_qty_opname_uom']);

            $rowCount++;
        }

        $rowCount = $rowCount + 3;
        }


      	$object = PHPExcel_IOFactory::createWriter($object, 'Excel5');  

		$name_file ='Mutasi '.$dept['nama'].'.xls';

        header('Content-Type: application/vnd.ms-excel'); //mime type
        header('Content-Disposition: attachment;filename="'.$name_file.'"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache
        $object->save('php://output');
        
        }else{
            $get_dept  = $this->_module->get_nama_dept_by_kode($departemen)->row_array();
            echo "<script>alert('Departemen ".$get_dept['nama']." belum terdapat Laporan Mutasi');location.replace(history.back())</script>";
            
        }

    }
}