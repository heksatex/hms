<?php defined('BASEPATH') OR EXIT ('No Direct Script Acces Allowed');
/**
 * 
 */
class Stockxow extends MY_Controller
{
	
	public function __construct()
	{
		parent:: __construct();
		$this->is_loggedin();//cek apakah user sudah login
		$this->load->model('_module');
		$this->load->model('m_stockxow');
        $this->load->library('pagination');

	}

    public function index()
    {
    	$id_dept                    = 'RSTOKXOW';
    	$data['id_dept']            = $id_dept;
        $data['list_grade']         = $this->_module->get_list_grade();
        $data['warehouse']          = $this->m_stockxow->get_list_departement_stock();
		// $data['mst_sales_group']    = $this->_module->get_list_sales_group();
    	$this->load->view('report/v_stock_x_ow', $data);
    }

    /* Table 1 >> */
    public function loadData($record=0)
    {

        $recordPerPage = 30;
        if($record != 0){
           $record = ($record-1) * $recordPerPage;
        }

    	$search             = $this->input->post('search');
    	$cmbSearch          = $this->input->post('cmbSearch');
    	$data_filter        = json_decode($this->input->post('arr_filter'),true); // tampung arr filter
    	$data_group         = json_decode($this->input->post('arr_group'),true);
    	$transit_location   = $this->input->post('transit');
    	$arr_order          = $this->input->post('arr_order'); 

        $set_lokasi         = "('TRI/Stock','JAC/Stock','CS/Stock','INS/Stock','GRG/Stock')";
        
        if($transit_location == 'true'){
            $where_lokasi = " ( sq.lokasi  IN ".$set_lokasi."  OR sq.lokasi  = 'Transit Location' ) AND (sq.nama_produk LIKE '%(Tricot)%' OR sq.nama_produk LIKE '%(Jacquard)%' OR sq.nama_produk LIKE '%(Cutting Shearing)%' OR sq.nama_produk LIKE '%(Inspecting)%' ) ";
        }else{
            $where_lokasi = " sq.lokasi  IN ".$set_lokasi."  AND (sq.nama_produk LIKE '%(Tricot)%' OR sq.nama_produk LIKE '%(Jacquard)%' OR sq.nama_produk LIKE '%(Cutting Shearing)%' OR sq.nama_produk LIKE '%(Inspecting)%' ) ";
        }
        
        if(count($arr_order) > 0){
            $order_by =  '';
            $order_by_in_group = '';
            foreach($arr_order as $val){
                $column = $this->declaration_name_field($val['column']);
                $order_by .= $column.' '.$val['sort'].', ';
                if($val['column'] ==  'qty'){
                    $order_by_in_group .= ' tot_qty '.$val['sort'].', ';
                }else if($val['column'] ==  'qty2'){
                    $order_by_in_group .= ' tot_qty2 '.$val['sort'].', ';
                }else{
                    $order_by_in_group .= $column.' '.$val['sort'].', ';
                }
            }

			$order_by = rtrim($order_by, ', ');
            $order_by = "ORDER BY ".$order_by;
            if(!empty($order_by_in_group)){
                $order_by_in_group = rtrim($order_by_in_group, ', ');
                $order_by_in_group = "ORDER BY ".$order_by_in_group;
            }
        }else{
            $order_by = "ORDER BY sq.quant_id desc";
            $order_by_in_group = "";
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

                $list = $this->m_stockxow->get_list_stock_grouping($where_lokasi, $nama_field, $where_result, $order_by_in_group,$record,$recordPerPage);
                $tot_group = 0;
                foreach ($list as $gp) {
                    # code...
                    $dataRecord[]  = array('group' => 'Yes',
                                        'nama_field' => $gp->nama_field,
                                        'grouping'   => $gp->grouping,
                                        'qty'        => 'Qty1 = '.number_format($gp->tot_qty,2),
                                        'qty2'       => 'Qty2 = '.number_format($gp->tot_qty2,2),
                                        'by'         => $nama_field
                                    );
                    $tot_group++;

                }

                $group = true;
                $name_total = "Total Group : ";
                //$allcount = $tot_group;

                $allcount   = $this->m_stockxow->get_record_stock_group($where_lokasi,$where_result,$nama_field);

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

        }else{
                $name_total="Total Data : ";

    	    	$list = $this->m_stockxow->get_list_stock_by($where_lokasi,$where_result,$order_by,$record,$recordPerPage)->result();
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
                                          'qty_opname'  => $row->qty_opname.' '.$row->uom_opname,
    	    							  'umur_produk' => $row->umur,
    	    							);
    	    	}

                $allcount   = $this->m_stockxow->get_record_stock($where_lokasi,$where_result);

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

        // get total all
        $tot = $this->m_stockxow->get_record_stock_all($where_lokasi,$where_result);
        $total_lot          =  "Total Lot  : ".number_format($tot['tot_lot']);
        $total_qty          =  "Total Qty1 : ".number_format($tot['tot_all_qty'],2);
        $total_qty2         =  "Total Qty2 : ".number_format($tot['tot_all_qty2'],2);

        $total_record       = $name_total.' '. number_format($allcount);

    	$callback  = array('group'          => $group, 
                            'record'        => $dataRecord, 
                            'pagination'    => $pagination, 
                            'total_record'  => $total_record, 
                            'total_lot'     => $total_lot, 
                            'total_qty'     => $total_qty, 
                            'total_qty2'    => $total_qty2, 
                            'sql'           => $where_result.' '.$order_by);

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

        $set_lokasi         = "('TRI/Stock','JAC/Stock','CS/Stock','INS/Stock','GRG/Stock')";
        
        if($transit_location == 'true'){
            $where_lokasi = " ( sq.lokasi  IN ".$set_lokasi."  OR sq.lokasi  = 'Transit Location' ) AND (sq.nama_produk LIKE '%(Tricot)%' OR sq.nama_produk LIKE '%(Jacquard)%' OR sq.nama_produk LIKE '%(Cutting Shearing)%' OR sq.nama_produk LIKE '%(Inspecting)%' ) ";
        }else{
            $where_lokasi = " sq.lokasi  IN ".$set_lokasi."  AND (sq.nama_produk LIKE '%(Tricot)%' OR sq.nama_produk LIKE '%(Jacquard)%' OR sq.nama_produk LIKE '%(Cutting Shearing)%' OR sq.nama_produk LIKE '%(Inspecting)%' ) ";
        }

        $arr_order     = $this->input->post('arr_order'); 
         
        if(count($arr_order) > 0){
            $order_by =  '';
            $order_by_in_group = '';
            foreach($arr_order as $val){
                $column = $this->declaration_name_field($val['column']);
                $order_by .= $column.' '.$val['sort'].', ';
                if($val['column'] ==  'qty'){
                    $order_by_in_group .= ' tot_qty '.$val['sort'].', ';
                }else if($val['column'] ==  'qty2'){
                    $order_by_in_group .= ' tot_qty2 '.$val['sort'].', ';
                }else{
                    $order_by_in_group .= $column.' '.$val['sort'].', ';
                }
            }

			$order_by = rtrim($order_by, ', ');
            $order_by = "ORDER BY ".$order_by;
            if(!empty($order_by_in_group)){
                $order_by_in_group = rtrim($order_by_in_group, ', ');
                $order_by_in_group = "ORDER BY ".$order_by_in_group;
            }
        }else{
            $order_by = "ORDER BY sq.quant_id desc";
            $order_by_in_group = "";
        }


        $list_items     = [];
        $tmp_arr_group  = [];
        $list_group     = [];
        $all_page       = 0;
        $allcount       = 0;
        $where_result   = '';
        $by2            = '';


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
        $page_now      = $record+1;
        $recordPerPage = 10;
        if($record != 0){
           $record = ($record-1) * $recordPerPage;
        }

        if($group_ke == count($data_group)){// loadchild list

            $list = $this->m_stockxow->get_list_stock_by($where_lokasi,$where_result,$order_by,$record,$recordPerPage)->result();
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
                                      'qty_opname'  => $row->qty_opname.' '.$row->uom_opname,
                                      'umur_produk' => $row->umur
                                    );
            }
            $allcount  = $this->m_stockxow->get_record_stock($where_lokasi,$where_result);
            $all_page = ceil($allcount/$recordPerPage);


        }else{ // loadchild group

            $row   = '';
            $no    = 1;
            $group_ke_next = 0;
            $group    = $tbody_id;
            $by2      = $data_group[$group_ke]['nama_field'];
            
            $list = $this->m_stockxow->get_list_stock_grouping($where_lokasi,$by2, $where_result,$order_by_in_group,$record,$recordPerPage);
            /*
            $group_ke_next = $group_ke + 1;
            foreach ($list as $gp2) {
                # code...
                $groupOf = $group;
                $id = $group.'-'.$no;
                $row .= "<tbody  data-root='".$groupOf."' data-parent='".$groupOf."' id='".$id."'>";
                $row .= "<tr>";
                $row .= "<td></td>";
                $row .= "<td class='show collapsed group1'  href='#' data-content='edit' data-isi='".$gp2->nama_field."' data-group='".$by2."' data-tbody='".$id."' group-ke='".$group_ke_next."'' data-root='".$groupOf."' node-root='No' style='cursor:pointer;'><i class='glyphicon glyphicon-plus'></td>";
                $row .= "<td colspan='2'>".$gp2->grouping."</td>";
                $row .= "<td align='right' colspan='2'>Qty1 = ".number_format($gp2->tot_qty,2)."</td>";
                $row .= "<td align='right'colspan='2'>Qty2 = ".number_format($gp2->tot_qty2,2)."</td>";
                $row .= "<td colspan='2' class='list_pagination'></td>";
                $row .= "</tr>";
                $row .="</tbody>";
                $no++;

            }
            $list_group  = $row;
            */
            foreach ($list as $gp) {
                # code...
                $list_group[]  = array('group'   => 'Yes',
                                    'nama_field' => $gp->nama_field,
                                    'grouping'   => $gp->grouping,
                                    'qty'        => 'Qty1 = '.number_format($gp->tot_qty,2),
                                    'qty2'       => 'Qty2 = '.number_format($gp->tot_qty2,2),
                                    'by'         => $by2,
                                    'tot_items'  => $gp->total_items
                                );
            }
            
            $allcount   = $this->m_stockxow->get_record_stock_group($where_lokasi,$where_result,$by2);
            $all_page = ceil($allcount/$recordPerPage);

            // create array tmpung group yang terbuka / collapsed
            $tmp_arr_group = array('tbody_id' => $tbody_id, 'by' => $group_by, 'value' => $kode);

        }

        $callback = array('record'          => $list_items,
                          'tbody_id'        => $tbody_id, 
                          'group_ke'        => $group_ke,
                          'group_by'        => $by2,
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
                        
                    }else if($row['nama_field'] == 'opname'){ 
                        if($row['value'] == 'done'){
                            $isi = " > 0";
                        }else{
                            $isi = " <= 0";
                        }
                        $nama_field = "sq.qty_opname";

                    }else{

                        if($row['operator'] == 'LIKE'){
                            $isi        = "LIKE '%".addslashes($row['value'])."%' ";
                        }else if($row['operator'] == 'NOT LIKE'){
                            $isi        = "NOT LIKE '%".addslashes($row['value'])."%' ";
                        }else if($row['operator'] == '!='){
                            $isi        = "!= '".addslashes($row['value'])."' ";
                        }else{
                            $isi        = "= '".addslashes($row['value'])."' ";
                        }

                        //$isi        = "LIKE '%".addslashes($row['value'])."%' ";
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
    
    /* Tabel 1 << */
    
    function declaration_name_field($nama_field)
    {
        
        $where = 'sq.'.$nama_field;
        return $where;

    }

    /* Tabel 2 >> */
    public function loadData2($record=0)
    {
        $recordPerPage = 30;
        if($record != 0){
           $record = ($record-1) * $recordPerPage;
        }

    	$data_filter        = json_decode($this->input->post('arr_filter'),true); // tampung arr filter
    	$arr_order          = $this->input->post('arr_order'); 
        
        if(count($arr_order) > 0){
            $order_by =  '';
            foreach($arr_order as $val){
                $column    = $this->declaration_name_field2($val['column']);
                $order_by .= $column.' '.$val['sort'].', ';
            }

			$order_by = rtrim($order_by, ', ');
            $order_by = "ORDER BY ".$order_by;
          
        }else{
            $order_by = "ORDER BY pbi.nama_produk ASC";
        }

	    $dataRecord  = [];
	    $group       = true;
        $allcount    = 0;
        $pagination  = '';
        $where_result= '';
        $group1_field = 'move_id';
        $groupBy      = $this->declaration_name_field2($group1_field);
        $group2_field = 'nama_produk';
        $groupBy2     = $group2_field;// tidak di deklarasikan krna group yg ke 2

    	// create where
        $where_data_filter   = $this->create_where2($data_filter);
        $where_result   .= " WHERE pb.dept_id ='GRG' AND pb.status IN ('draft','ready')  ";
        $where_result   .= $where_data_filter;

                $name_total="Total Group : ";

    	    	$list = $this->m_stockxow->get_list_greige_out($where_result,$order_by,$record,$recordPerPage, $groupBy, $groupBy2)->result();
    	    	foreach ($list as $row) {
    	
                    $dataRecord[]  = array( 'group'          => 'Yes',
                                            'nama_field'     => $row->nama_field,
                                            'grouping'       => $row->grouping,
                                            'qty_planing'    => 'Qty1 Planning = '.number_format($row->tot_qty_planning,2),
                                            'qty'            => 'Qty1 Terpesan = '.number_format($row->tot_qty,2),
                                            'qty2'           => 'Qty2 = '.number_format($row->tot_qty2,2),
                                            'by'             => $group2_field
        						);
    	    	}

                $allcount   = $this->m_stockxow->get_record_list_greige_out($where_result, $groupBy2);

                $config['base_url']         = base_url().'report/stock/loadData2';
                $config['use_page_numbers'] = TRUE;
                $config['total_rows']       = $allcount;
                $config['per_page']         = $recordPerPage;
                
                $config['num_links']        = 1;
                $config['next_link']        = '>';
                $config['prev_link']        = '<';
                $this->pagination->initialize($config);
                $pagination         = $this->pagination->create_links();
    	

        $total_record       = $name_total.' '. number_format($allcount);
        
        // get tot all 
        $tot = $this->m_stockxow->get_record_list_greige_out_all($where_result);
        $total_lot          =  "Total Lot  : ".number_format($tot['tot_all_lot']);
        $total_planning     =  "Total Qty1 Planning  : ".number_format($tot['tot_all_plan'],2);
        $total_qty          =  "Total Qty1 Terpesan  : ".number_format($tot['tot_all_qty'],2);
        $total_qty2         =  "Total Qty2 : ".number_format($tot['tot_all_qty2'],2);

        $callback  = array('group'          => $group, 
                            'record'        => $dataRecord, 
                            'pagination'    => $pagination, 
                            'total_record'  => $total_record, 
                            'total_lot'     => $total_lot, 
                            'total_planning'=> $total_planning, 
                            'total_qty'     => $total_qty, 
                            'total_qty2'    => $total_qty2, 
                            'sql'           => $where_result.' '.$order_by);

    	echo json_encode($callback);
    	
    }

    public function loadChild2()
    {
        $kode        = $this->input->post('kode');
        $group_by    = $this->input->post('group_by');
        $group_ke    = $this->input->post('group_ke');
        $data_filter = json_decode($this->input->post('arr_filter'),true); // tampung arr filter
        $data_group  = json_decode($this->input->post('arr_group2'),true);
        $record      = $this->input->post('record');
        $tbody_id    = $this->input->post('tbody_id');
        $root        = $this->input->post('root');
        $post_tmp_group  = json_decode($this->input->post('tmp_arr_group2'),true);
        $arr_order     = $this->input->post('arr_order'); 

        $list_items    = [];
        $tmp_arr_group = [];
        $all_page      = 0;
        $allcount      = 0;
        $list_group    = [];
        $where_result  = '';
        $by2           = '';
        
        // create where
        $where_default  = " WHERE pb.dept_id = 'GRG' AND pb.status IN ('draft','ready')  ";
        $where_filter   = $this->create_where2($data_filter);
        $group_by_dec   = $this->declaration_name_field2($group_by);
        $where_result  .= $where_default." ".$where_filter." AND ".$group_by_dec." = '".$kode."'";

        if($group_ke > 1){
            // looping post arr_tmp_group
            foreach ($post_tmp_group as $key) {
                    if($key['tbody_id'] == $root){
                        $key_by        = $this->declaration_name_field2($key['by']);
                        $where_result .= " AND ".$key_by." = '".$key['value']."' ";
                        break;
                    }
            }
        }

        // informasi page sekarang
        $page_now      = $record+1;
        $recordPerPage = 10;
        if($record != 0){
            $record = ($record-1) * $recordPerPage;
        }

        if($group_ke == count($data_group)){// loadchild list

            if(count($arr_order) > 0){ // $arr_order terdapat field yang akan di urutkan beda nama 
                $order_by =  '';
                foreach($arr_order as $val){
                    $column    = $this->declaration_name_field22($val['column']);
                    $order_by .= $column.' '.$val['sort'].', ';
                }
    
                $order_by = rtrim($order_by, ', ');
                $order_by = "ORDER BY ".$order_by;
            }else{
                $order_by = "ORDER BY pb.tanggal desc";
            }

            $list = $this->m_stockxow->get_list_items_smi_greige_out($where_result,$order_by,$record,$recordPerPage);
            foreach ($list as $row) {
                # code...
                $list_items[] = array(
                                      'kode'        => $row->kode,
                                      'origin'      => $row->origin,
                                      'kode_produk' => $row->kode_produk,
                                      'nama_produk' => $row->nama_produk,
                                      'lot'         => $row->lot,
                                      'grade'       => $row->nama_grade,
                                      'qty_plan'	=> $row->qty_plan.' '.$row->uom_plan,
                                      'qty' 		=> $row->qty.' '.$row->uom,
    	    						  'qty2'        => $row->qty2.' '.$row->uom2,
                                    );
            }
            $allcount  = $this->m_stockxow->get_record_list_items_smi_greige_out($where_result);
            $all_page = ceil($allcount/$recordPerPage);


        }else{ // loadchild group

            if(count($arr_order) > 0){ // $arr_order terdapat field yang akan di urutkan beda nama 
                $order_by =  '';
                foreach($arr_order as $val){
                    $column    = $this->declaration_name_field2($val['column']);
                    $order_by .= $column.' '.$val['sort'].', ';
                }
    
                $order_by = rtrim($order_by, ', ');
                $order_by = "ORDER BY ".$order_by;
            }else{
                $order_by = "ORDER BY pb.tanggal desc";
            }

            $row   = '';
            $no    = 1;
            $group_ke_next = 0;
            $group    = $tbody_id;
            $by2      = $data_group[$group_ke]['nama_field'];
            $by2_dec  = $this->declaration_name_field2($by2);
            $group_ke_next = $group_ke + 1;
            
    	    $list = $this->m_stockxow->get_list_greige_out($where_result,$order_by,$record,$recordPerPage, '', $by2_dec)->result();
    	    foreach ($list as $row) {
    	
                $list_group[]  = array( 'group'          => 'Yes',
                                        'nama_field'     => $row->nama_field,
                                        'grouping'       => $row->grouping,
                                        'origin'         => $row->origin,
                                        'qty_planing'    => 'Qty1 Planning = '.number_format($row->tot_qty_planning,2),
                                        'qty'            => 'Qty1 Terpesan = '.number_format($row->tot_qty,2),
                                        'qty2'           => 'Qty2 = '.number_format($row->tot_qty2,2),
                                        'by'             => $by2,
                                        'tot_items'      => $row->tot_items
        						);
    	    }

            $allcount   = $this->m_stockxow->get_record_list_greige_out($where_result, $by2_dec);
            $all_page = ceil($allcount/$recordPerPage);
            // create array tmpung group yang terbuka / collapsed
            $tmp_arr_group = array('tbody_id' => $tbody_id, 'by' => $group_by, 'value' => $kode);

        }

        $callback = array('record'          => $list_items,
                          'group_ke'        => $group_ke,
                          'group_by'        => $by2,
                          'tbody_id'        => $tbody_id, 
                          'total_record'    => $allcount,
                          'all_page'        => $all_page,
                          'page_now'        => $page_now,
                          'list_group'      => $list_group,
                          'root'            => $root,
                          'limit'           => $recordPerPage,
                          'tmp_arr_group2'  => $tmp_arr_group,
                          'sql'             => $where_result.' '.$order_by,
                        );

        echo json_encode($callback);

    }

    function declaration_name_field2($nama_field)
    {
        if($nama_field == 'nama_produk' || $nama_field =='kode_produk' || $nama_field =='kode'){
            $where = 'pbi.'.$nama_field;
        }else if($nama_field == 'qty_plan'){
            $where = 'tot_qty_planning';
        }else if($nama_field == 'qty'){
            $where = 'tot_qty';
        }else if($nama_field == 'qty2'){
            $where = 'tot_qty2';
        }else{
            $where = 'pb.'.$nama_field;
        }
        return $where;

    }

    function declaration_name_field22($nama_field)
    {
        if($nama_field == 'nama_produk' || $nama_field =='kode_produk' || $nama_field =='kode'){
            $where = 'pbi.'.$nama_field;
        }else if($nama_field == 'qty_plan'){
            $where = 'qty_plan';
        }else if($nama_field == 'qty'){
            $where = 'smi.qty';
        }else if($nama_field == 'qty2'){
            $where = 'smi.qty2';
        }else{
            $where = 'pb.'.$nama_field;
        }
        return $where;

    }

    function create_where2($data_filter)
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
	    		if($row['type'] == 'search'){// search biasa
	    			
                    if($row['nama_field'] == 'kode_produk' or $row['nama_field'] == 'nama_produk'){ 

                        if($loop > 0){
                            $where_condition  = ' AND ';
                        }

                        if($row['operator'] == 'LIKE'){
                            $isi        = "LIKE '%".addslashes($row['value'])."%' ";
                        }else if($row['operator'] == 'NOT LIKE'){
                            $isi        = "NOT LIKE '%".addslashes($row['value'])."%' ";
                        }else if($row['operator'] == '!='){
                            $isi        = "!= '".addslashes($row['value'])."' ";
                        }else{
                            $isi        = "= '".addslashes($row['value'])."' ";
                        }

                        $nama_field = $this->declaration_name_field2($row['nama_field']);
                        $loop++;
                        $where     .= $where_condition.' '.$nama_field.' '.$isi;
                    }else{
                        $nama_field = '';
                        $isi        = '';
                    }
                    
	    		}
	    	}

            $where_condition   = '';
            $loop = 1;

    	}

    	return $where;
    }

    /* Tabel 2  << */


}