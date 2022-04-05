<?php defined('BASEPATH') OR EXIT ('No Direct Script Acces Allowed');
/**
 * 
 */
class Stock extends MY_Controller
{
	
	public function __construct()
	{
		parent:: __construct();
		$this->is_loggedin();//cek apakah user sudah login
		$this->load->model('_module');
		$this->load->model('m_stock');
        $this->load->library('pagination');

	}

	public function index()
    {
    	$id_dept         = 'RSTOK';
    	$data['id_dept'] = $id_dept;
    	$type_condition     = $this->_module->get_first_type_conditon($id_dept);
        $data['mstFilter']  = $this->_module->get_list_mst_filter($id_dept);
        $data['list_grade'] = $this->_module->get_list_grade();
        $data['warehouse']  = $this->m_stock->get_list_departement_stock();
        $data['type_condition']  = $type_condition;
        $data['list_grade']      = $this->_module->get_list_grade();
		$data['mst_sales_group'] = $this->_module->get_list_sales_group();
    	$this->load->view('report/v_stock', $data);
    }

    public function loadData($record=0)
    {

        $recordPerPage = 30;
        if($record != 0){
           $record = ($record-1) * $recordPerPage;
        }

    	$search      = $this->input->post('search');
    	$cmbSearch   = $this->input->post('cmbSearch');
    	$data_filter = json_decode($this->input->post('arr_filter'),true); // tampung arr filter
    	$data_group  = json_decode($this->input->post('arr_group'),true);
    	$transit_location = $this->input->post('transit');

        if($transit_location == 'true'){
            $where_lokasi = " (sq.lokasi LIKE '%Stock%' OR sq.lokasi  LIKE '%Transit Location%') ";
        }else{
            $where_lokasi = " sq.lokasi LIKE '%Stock%'  ";
        }

    	$arr_order     = $this->input->post('arr_order'); 
        
        if(count($arr_order) > 0){
            $order_by =  '';
            foreach($arr_order as $val){
                $column = $this->declaration_name_field($val['column']);
                $order_by .= $column.' '.$val['sort'].', ';
            }

			$order_by = rtrim($order_by, ', ');
            $order_by = "ORDER BY ".$order_by;
        }else{
            $order_by = "ORDER BY sq.quant_id desc";
        }

	    $dataRecord  = [];
	    $group       = false;
        $allcount    = 0;
        $pagination = '';

    	// create where
        $where_result   = $this->create_where($data_filter);

        // cek jika ada group
        if(count($data_group) > 0 ){

        	foreach ($data_group as $gp) {
        		# code..
        		$nama_field = $gp['nama_field'];
        		break;
        	}

        	$list = $this->m_stock->get_list_stock_grouping($where_lokasi, $nama_field, $where_result);
            $tot_group = 0;
        	foreach ($list as $gp) {
        		# code...
        		$dataRecord[]  = array('group' => 'Yes',
        							   'nama_field' => $gp->nama_field,
        							   'grouping'   => $gp->grouping,
        							   'qty'        => 'Qty1 = '.number_format($gp->tot_qty,2),
        							   'qty2'       => 'Qty2= '.number_format($gp->tot_qty2,2),
                                       'by'         => $nama_field
        						);
                $tot_group++;
                

        	}

        	$group = true;
        	$name_total = "Total Group : ";
            $allcount   = $tot_group;

        }else{
            $name_total="Total Data : ";

    	    	$list = $this->m_stock->get_list_stock_by($where_lokasi,$where_result,$order_by,$record,$recordPerPage)->result();
    	    	foreach ($list as $row) {
    	    		# code...
    	    		$dataRecord[] = array(
    	    							  'lot'   		=> $row->lot,
    	    							  'tgl_dibuat'  => $row->create_date,
    	    							  'tgl_diterima'=> $row->move_date,
    	    							  'grade'       => $row->nama_grade,
    	    							  'lokasi'      => $row->lokasi,
    	    							  'lokasi_fisik'  => $row->lokasi_fisik,
    	    							  'kode_produk' => $row->kode_produk,
    	    							  'nama_produk' => $row->nama_produk,
    	    							  'qty' 		=> $row->qty.' '.$row->uom,
    	    							  'qty2'        => $row->qty2.' '.$row->uom2,
    	    							  'lebar_greige'=> $row->lebar_greige.' '.$row->uom_lebar_greige,
    	    							  'lebar_jadi'  => $row->lebar_jadi.' '.$row->uom_lebar_jadi,
    	    							  'sales_order' => $row->sales_order,
    	    							  'sales_group' => $row->nama_sales_group,
    	    							  'umur_produk' => $row->umur
    	    							);
    	    	}


                $allcount   = $this->m_stock->get_record_stock($where_lokasi,$where_result);

                $config['base_url']         = base_url().'report/stock/loadData';
                $config['use_page_numbers'] = TRUE;
                $config['total_rows']       = $allcount;
                $config['per_page']         = $recordPerPage;
                
                //$config['first_link']     = FALSE;
                //$config['last_link']      = FALSE;
                $config['num_links']        = 1;
                $config['next_link']        = '>';
                $config['prev_link']        = '<';
                $this->pagination->initialize($config);
                $pagination         = $this->pagination->create_links();
    	}

        $total_record       = $name_total.' '. number_format($allcount);

    	$callback  = array('group' => $group, 'record' => $dataRecord, 'pagination'=>$pagination, 'total_record'=>$total_record, 'sql' => $where_result.' '.$order_by);

    	echo json_encode($callback);
    	
    }

    public function loadChild()
    {
        $kode        = $this->input->post('kode');
        $group_by    = $this->declaration_name_field($this->input->post('group_by'));
        $group_ke    = $this->input->post('group_ke');
        $data_filter = json_decode($this->input->post('arr_filter'),true); // tampung arr filter
        $data_group  = json_decode($this->input->post('arr_group'),true);
        $record      = $this->input->post('record');
        $tbody_id    = $this->input->post('tbody_id');
        $root        = $this->input->post('root');
        $post_tmp_group  = json_decode($this->input->post('tmp_arr_group'),true);

        $transit_location = $this->input->post('transit');

        if($transit_location == 'true'){
            $where_lokasi = " (sq.lokasi LIKE '%Stock%' OR sq.lokasi  LIKE '%Transit Location%') ";
        }else{
            $where_lokasi = " sq.lokasi LIKE '%Stock%'  ";
        }

        $arr_order     = $this->input->post('arr_order'); 
        
        if(count($arr_order) > 0){
            $order_by =  '';
            foreach($arr_order as $val){
                $order_by .= $val['column'].' '.$val['sort'].', ';
            }

			$order_by = rtrim($order_by, ', ');
            $order_by = "ORDER BY ".$order_by;
        }else{
            $order_by = "ORDER BY quant_id desc";
        }


        $list_items  = [];
        $tmp_arr_group = [];
        $all_page    = 0;
        $allcount    = 0;
        $list_group  = '';
        $where_result= '';


        // create where
        $where         = $this->create_where($data_filter);
        $where_result  .= $where ." AND ".$group_by." = '".$kode."'";

        if($group_ke > 1){
            // looping post arr_tmp_group
            foreach ($post_tmp_group as $key) {
                    # code...
                    if($key['tbody_id'] == $root){
                        $where_result .= "AND ".$key['by']." = '".$key['value']."' ";
                        break;
                    }
            }
        }

        // informasi page sekarang
        $page_now = $record+1;
        $recordPerPage = 10;


        if($group_ke == count($data_group)){// loadchild list

            if($record != 0){
               $record = ($record-1) * $recordPerPage;
            }

            $list = $this->m_stock->get_list_stock_by($where_lokasi,$where_result,$order_by,$record,$recordPerPage)->result();
            foreach ($list as $row) {
                # code...
                $list_items[] = array(
                                      'lot'         => $row->lot,
                                      'tgl_dibuat'  => $row->create_date,
                                      'tgl_diterima'=> $row->move_date,
                                      'grade'       => $row->nama_grade,
                                      'lokasi'      => $row->lokasi,
                                      'lokasi_fisik'  => $row->lokasi_fisik,
                                      'kode_produk' => $row->kode_produk,
                                      'nama_produk' => $row->nama_produk,
                                      'qty' 		=> $row->qty.' '.$row->uom,
    	    						  'qty2'        => $row->qty2.' '.$row->uom2,
                                      'lebar_greige'=> $row->lebar_greige.' '.$row->uom_lebar_greige,
    	    						  'lebar_jadi'  => $row->lebar_jadi.' '.$row->uom_lebar_jadi,
                                      'sales_order' => $row->sales_order,
                                      'sales_group' => $row->nama_sales_group,
                                      'umur_produk'   => $row->umur
                                    );
            }
            $allcount  = $this->m_stock->get_record_stock($where_lokasi,$where_result);
            $all_page = ceil($allcount/$recordPerPage);


        }else{ // loadchild group

            $row   = '';
            $no    = 1;
            $group_ke_next = 0;
            $group    = $tbody_id;
            $by2      = $data_group[$group_ke]['nama_field'];
            
            $list = $this->m_stock->get_list_stock_grouping($where_lokasi,$by2, $where_result);
            $group_ke_next = $group_ke + 1;
        
            foreach ($list as $gp2) {
                # code...
                $groupOf = $group;
                $id = $group.'-'.$no;
                $row .= "<tbody  data-root='".$groupOf."' data-parent='".$groupOf."' id='".$id."'>";
                $row .= "<tr>";
                $row .= "<td></td>";
                $row .= "<td class='show collapsed group1'  href='#' data-content='edit' data-isi='".$gp2->nama_field."' data-group='".$by2."' data-tbody='".$id."' group-ke='".$group_ke_next."'' data-root='".$groupOf."' node-root='No' style='cursor:pointer;'><i class='glyphicon glyphicon-plus'></td>";
                $row .= "<td colspan='4'>".$gp2->grouping."</td>";
                $row .= "<td align='right'colspan='2'>Qty1 = ".number_format($gp2->tot_qty,2)."</td>";
                $row .= "<td align='right'colspan='2'>Qty2 = ".number_format($gp2->tot_qty2,2)."</td>";
                $row .= "<td colspan='3' class='list_pagination'></td>";
                $row .= "</tr>";
                $row .="</tbody>";
                $no++;

            }
            $list_group  = $row;

            // create array tmpung group yang terbuka / collapsed
            $tmp_arr_group = array('tbody_id' => $tbody_id, 'by' => $group_by, 'value' => $kode);

        }

        $callback = array('record'          => $list_items,
                          'tbody_id'        => $tbody_id, 
                          'total_record'    => $allcount,
                          'all_page'        => $all_page,
                          'page_now'        => $page_now,
                          'list_group'      => $list_group,
                          'root'            => $root,
                          'limit'           => $recordPerPage,
                          'tmp_arr_group'   => $tmp_arr_group,
                          'sql'             => $where_result.' '.$order_by,
                        );

        echo json_encode($callback);

    }


    function create_where($data_filter)
    {

    	$where = '';
        $where_tb = '';
    	$loop  = 1;
    	$loop_2= 1;
    	$where_condition   = '';
    	$where_condition_2 = '';
        $filter_table = FALSE;

    	if(!empty($data_filter)){

            // filter atas
	    	foreach ($data_filter as $row) {
	    		# code...
	    		if($loop > 0){
	    			$where_condition  = ' AND ';
	    		}

	    		if($row['type'] == 'search'){// search biasa
	    			
                    if($row['nama_field'] == 'umur'){
                        
                        if($row['operator'] == '<'){
                            $isi        = "< '".addslashes($row['value'])."' ";
                        }else if($row['operator'] == '>'){
                            $isi        = "> '".addslashes($row['value'])."' ";
                        }

                        $nama_field = " (datediff(now(), sq.move_date) ) ";
                        
                    }else{
                        $isi        = "LIKE '%".addslashes($row['value'])."%' ";
                        $nama_field = $this->declaration_name_field($row['nama_field']);
                    }
                    
                    $where     .= $where_condition.' '.$nama_field.' '.$isi;
                    $loop++;
	    		}
	    	}

            $where_condition   = '';
            $loop = 1;

            // filter table
            foreach ($data_filter as $row) {
                # code...
                if($row['type'] == 'table'){// search table
                    $filter_table = TRUE;

                    foreach ($row['data'] as $tbl) {
                        if($loop_2 > 1){
                            $where_condition_2 = ' OR ';
                            $where_condition   = ' AND ';
                        }

                        # code...
                        if($tbl['operator'] == 'LIKE'){
                            $isi = $tbl['operator']." '%".addslashes($tbl['value'])."%' ";
                        }else{
                            $isi = $tbl['operator']." '".addslashes($tbl['value'])."' ";
                        }

                        //$isi        = $tbl['operator']." '%".addslashes($tbl['value'])."%' ";
                        $nama_field = $this->declaration_name_field($tbl['nama_field']);
                        $where_tb   .= $where_condition_2.' '.$nama_field.' '.$isi;

                        $loop_2++;
                    }
                    $where_tb = $where_condition.' ( '.$where_tb.' )';
                    $loop_2 = 0;
                    if($loop == 2){
                        //break;
                    }
                    $loop++;
                }
            }

            if($filter_table == true){
                $where = $where.' AND '.$where_tb;
            }else{
                $where = $where.' '.$where_tb;
            }

    	}

    	return $where;
    }

    function get_sales_group_by_kode()
    {
        $kode = $this->input->post('kode_sales_group');
        $nama = $this->_module->get_nama_sales_Group_by_kode($kode);
        $callback = array('status'=> 'success', 'nama'=>$nama);
        echo json_encode($callback);
    }


    function declaration_name_field($nama_field)
    {
        
        $where = 'sq.'.$nama_field;
        return $where;

    }


    function export_excel_stock()
    {

    	$this->load->library('excel');

        $where_result= $this->input->post('sql');
        //$order_by = "ORDER BY quant_id desc";

        $transit_location = $this->input->post('transit[]');

        if(count($transit_location) > 0){
            $where_lokasi = " (lokasi LIKE '%Stock%' OR lokasi  LIKE '%Transit Location%') ";
        }else{
            $where_lokasi = " lokasi LIKE '%Stock%'  ";
        }

    	$object = new PHPExcel();
    	$object->setActiveSheetIndex(0);
    	// SET JUDUL
 		$object->getActiveSheet()->SetCellValue('A1', 'Laporan Stock');
 		$object->getActiveSheet()->getStyle('A1')->getAlignment()->setIndent(1);
		$object->getActiveSheet()->mergeCells('A1:L1');

        //bold huruf
        $object->getActiveSheet()->getStyle("A1:T4")->getFont()->setBold(true);

        // Border 
		$styleArray = array(
            'borders' => array(
              'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN
              )
            )
        );


        // header table
        $table_head_columns  = array('No', 'Quant ID','Lot', 'Grade', 'Tgl diterima', 'Lokasi', 'Lokasi Fisik', 'Kode Produk', 'Nama Produk', 'Qty1','Uom1', 'Qty2','Uom2','Lbr Greige', 'Uom Lbr Greige', 'Lbr Jadi', 'Uom Lbr Jadi', 'SC', 'Marketing', 'Umur (Hari)');

        $column = 0;
        foreach ($table_head_columns as $judul) {
            # code...
            $object->getActiveSheet()->setCellValueByColumnAndRow($column, 4, $judul);  
            $column++;
        }

        // set with and border
    	$index_header = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T');
    	$loop = 0;
    	foreach ($index_header as $val) {
    		
    		$object->getActiveSheet()->getStyle($val.'4')->applyFromArray($styleArray);
            $object->getActiveSheet()->getStyle($val.'5')->applyFromArray($styleArray);
        }


        //body
        $num      = 1;
        $rowCount = 5;
        $list     = $this->m_stock->get_list_stock_by_noLimit($where_lokasi,$where_result)->result();
        foreach ($list as $val) {
            # code...
            $object->getActiveSheet()->SetCellValue('A'.$rowCount, ($num++));
            $object->getActiveSheet()->SetCellValue('B'.$rowCount, $val->quant_id);
            $object->getActiveSheet()->SetCellValue('C'.$rowCount, $val->lot);
            $object->getActiveSheet()->SetCellValue('D'.$rowCount, $val->nama_grade);
            $object->getActiveSheet()->SetCellValue('E'.$rowCount, $val->move_date);
            $object->getActiveSheet()->SetCellValue('F'.$rowCount, $val->lokasi);
            $object->getActiveSheet()->SetCellValue('G'.$rowCount, $val->lokasi_fisik);
            $object->getActiveSheet()->SetCellValue('H'.$rowCount, $val->kode_produk);
            $object->getActiveSheet()->SetCellValue('I'.$rowCount, $val->nama_produk);
            $object->getActiveSheet()->SetCellValue('J'.$rowCount, $val->qty);
            $object->getActiveSheet()->SetCellValue('K'.$rowCount, $val->uom);
            $object->getActiveSheet()->SetCellValue('L'.$rowCount, $val->qty2);
            $object->getActiveSheet()->SetCellValue('M'.$rowCount, $val->uom2);
            $object->getActiveSheet()->SetCellValue('N'.$rowCount, $val->lebar_greige);
            $object->getActiveSheet()->SetCellValue('O'.$rowCount, $val->uom_lebar_greige);
            $object->getActiveSheet()->SetCellValue('P'.$rowCount, $val->lebar_jadi);
            $object->getActiveSheet()->SetCellValue('Q'.$rowCount, $val->uom_lebar_jadi);
            $object->getActiveSheet()->SetCellValue('R'.$rowCount, $val->sales_order);
            $object->getActiveSheet()->SetCellValue('S'.$rowCount, $val->nama_sales_group);
            $object->getActiveSheet()->SetCellValue('T'.$rowCount, $val->umur);

             //set border true
			$object->getActiveSheet()->getStyle('A'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('B'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('C'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('D'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('E'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('F'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('G'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('H'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('H'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('I'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('J'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('K'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('L'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('M'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('N'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('O'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('P'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('Q'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('R'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('S'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('T'.$rowCount)->applyFromArray($styleArray);

            $rowCount++;


        }

    	$object = PHPExcel_IOFactory::createWriter($object, 'Excel5');  

        header('Content-Type: application/vnd.ms-excel'); //mime type
        header('Content-Disposition: attachment;filename="Stock.xls"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache
        $object->save('php://output');


    }


}