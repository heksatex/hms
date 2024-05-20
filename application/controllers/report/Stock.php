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
    	$id_dept            = 'RSTOK';
        $username           = $this->session->userdata('username');
    	$data['id_dept']    = $id_dept;
        $data['user_filter']= $this->_module->get_list_user_filter($id_dept,$username);
        $filter_default     = $this->_module->get_user_filter_default($id_dept,$username);
        $data['filter_default'] =  $filter_default['id'];
        //$data['dfi']        = $this->_module->get_user_filter_isi_by_id($filter_default['id'])->row_array();
        //$data['dfg']        = $this->_module->get_user_filter_isi_by_id($filter_default['id'])->row_array();
        //$data['filter_group']   = $this->_module->get_user_filter_grouping_by_id('1');

    	$type_condition     = $this->_module->get_first_type_conditon($id_dept);
        $data['mstFilter']  = $this->_module->get_list_mst_filter($id_dept);
        $data['list_grade'] = $this->_module->get_list_grade();
        $data['category']   = $this->_module->get_list_category();

        $stock_location    = $this->m_stock->get_list_departement_stock();
        $output_location   = $this->m_stock->get_list_departemen_outputlocation();

        $location          = [];
        foreach($stock_location as $stock){
            $location[]    = array('lokasi'=>$stock->stock_location);
        }
        foreach($output_location as $output){
            $location[]   = array('lokasi' => $output->output_location);
        }

        $data['warehouse'] = $location;
        

        $data['type_condition']  = $type_condition;
        $data['list_grade']      = $this->_module->get_list_grade();
		$data['mst_sales_group'] = $this->_module->get_list_sales_group();
    	$this->load->view('report/v_stock', $data);
    }

    public function get_default()
    {   
        $filter_id = $this->input->post('filter_id');
        $filter =  $this->_module->get_user_filter_isi_by_id($filter_id)->result_array();
        $group  =  $this->_module->get_user_filter_grouping_by_id($filter_id)->result_array();
        $sort   =  $this->_module->get_user_filter_order_by_id($filter_id)->result_array();
        echo json_encode(array('filter' => $filter, 'group' => $group, 'sort' => $sort));
    }

    public function save_filter()
    {
        if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
            // session habis
            $callback = array('message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
        }else{
            $sub_menu    = $this->uri->segment(2);
            $id_dept     = 'RSTOK';
            $username    = addslashes($this->session->userdata('username')); 
            $nama_filter = $this->input->post('nama_filter');
            $data_filter = json_decode($this->input->post('arr_filter'),true); // tampung arr filter
            $data_group  = json_decode($this->input->post('arr_group'),true);
            $data_order  = json_decode($this->input->post('arr_order'),true);
            $default     = $this->input->post('default');

            // cek apakah user terdapat save default atau tidak
            $filter_default  = $this->_module->get_user_filter_default($id_dept,$username);
            if(!empty($filter_default['id']) AND $default == 'true'){
                $callback = array('status' => 'failed', 'message'=> 'Maaf, use by default filter sudah ada, silahkan uncheck use default jika ingin menyimpan Filternya !', 'icon' => 'fa fa-warning', 'type'=>'danger');
            }else{
                // lock table
                $this->_module->lock_tabel('user_filter WRITE, user_filter_isi WRITE, user_filter_grouping WRITE, user_filter_order_by WRITE');
                $filter_id = $this->_module->get_last_user_filter_id();
                $this->_module->save_user_filter($filter_id,$username,$id_dept,$sub_menu,$nama_filter,$default);
                foreach($data_filter as $filter){
                    // insert table filter isi
                    $nama_field     = $filter['nama_field'];
                    $operator       = $filter['operator'];
                    $value          = $filter['value'];
                    $this->_module->save_user_filter_isi($filter_id,$nama_field,$operator,$value,'');
                }
                foreach($data_group as $group){
                    $index      = $group['index_group'];
                    $nama_field = $group['nama_field'];
                    $this->_module->save_user_filter_grouping($filter_id,$nama_field,$index);
                }
                foreach($data_order as $order){
                    $column    = $order['column'];
                    $sort      = $order['sort'];
                    $this->_module->save_user_filter_order($filter_id,$column,$sort);
                }
                //unlock table
                $this->_module->unlock_tabel();
                $callback = array('status' => 'success', 'message'=> 'Data Filter Berhasil Disimpan !', 'icon' => 'fa fa-check', 'type'=>'success');
            }

        }
        echo json_encode($callback);
    }

    public function delete_favorite()
    {

        if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
            // session habis
            $callback = array('message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
        }else{
            $filter_id = $this->input->post('filter_id');
            //delete favorite
            $this->_module->delete_user_filter($filter_id);
            $callback = array('status' => 'success', 'message'=> 'Favorite Berhasil Dihapus !', 'icon' => 'fa fa-check', 'type'=>'success');
        }

        echo json_encode($callback);

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
    	// $transit_location = $this->input->post('transit');

        $where_lokasi = " (sq.lokasi LIKE '%Stock%' OR sq.lokasi  LIKE '%Transit Location%') ";
        // if($transit_location == 'true'){
        // }else{
        //     $where_lokasi = " sq.lokasi LIKE '%Stock%'  ";
        // }

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

			$order_by          = rtrim($order_by, ', ');
            $order_by          = "ORDER BY ".$order_by;
            if(!empty($order_by_in_group)){
                $order_by_in_group = rtrim($order_by_in_group, ', ');
                $order_by_in_group = "ORDER BY ".$order_by_in_group;
            }
        }else{
            $order_by = "ORDER BY sq.quant_id desc";
            $order_by_in_group = "ORDER BY sq.quant_id asc";
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
        		$nama_field     = $this->declaration_name_field_group($gp['nama_field']);
        		$nama_field_real = $gp['nama_field'];
        		break;
        	}

        	$list = $this->m_stock->get_list_stock_grouping($where_lokasi, $nama_field, $where_result,$order_by_in_group,$record,$recordPerPage);
            //$tot_group = 0;
        	foreach ($list as $gp) {
        		# code...
        		$dataRecord[]  = array('group'      => 'Yes',
        							   'nama_field' => $gp->nama_field,
        							   'grouping'   => $gp->grouping,
        							   'qty'        => 'Qty1 [HPH] = '.number_format($gp->tot_qty,2),
        							   'qty2'       => 'Qty2 [HPH] = '.number_format($gp->tot_qty2,2),
                                       'by'         => $nama_field_real
        						);
                //$tot_group++;
        	}

        	$group      = true;
        	$name_total = "Total Group : ";
            $allcount   = $this->m_stock->get_record_list_stock_grouping($where_lokasi, $nama_field, $where_result);
            //$allcount   = $tot_group;

        }else{
            $name_total="Total Data : ";
                $tgl_sekarang = date('Y-m-d');
                $tgl_sebelum = date('Y-m-d', strtotime('-3 month', strtotime($tgl_sekarang)));
    	    	$list = $this->m_stock->get_list_stock_by($where_lokasi,$where_result,$order_by,$record,$recordPerPage)->result();
    	    	foreach ($list as $row) {
    	    		# code...
                    if(date('Y-m-d', strtotime($row->create_date)) < $tgl_sebelum){
                        $ket_exp = 'Expired';
                    }else{
                        $ket_exp = '';
                    }
    	    		$dataRecord[] = array(
    	    							  'lot'   		=> $row->lot,
    	    							  'tgl_dibuat'  => $row->create_date,
    	    							  'tgl_diterima'=> $row->move_date,
    	    							  'grade'       => $row->nama_grade,
    	    							  'lokasi'      => $row->lokasi,
    	    							  'lokasi_fisik'  => $row->lokasi_fisik,
    	    							  'kode_produk' => $row->kode_produk,
    	    							  'nama_produk' => $row->nama_produk,
    	    							  'corak_remark' => $row->corak_remark,
    	    							  'warna_remark' => $row->warna_remark,
    	    							  'kategori'    => $row->nama_category,
    	    							  'qty' 		=> $row->qty.' '.$row->uom,
    	    							  'qty2'        => $row->qty2.' '.$row->uom2,
    	    							  'lebar_greige'=> $row->lebar_greige.' '.$row->uom_lebar_greige,
    	    							  'lebar_jadi'  => $row->lebar_jadi.' '.$row->uom_lebar_jadi,
    	    							  'sales_order' => $row->sales_order,
    	    							  'sales_group' => $row->nama_sales_group,
                                          'qty_opname'  => $row->qty_opname.' '.$row->uom_opname,
    	    							  'umur_produk' => $row->umur,
                                          'qty_jual'    => $row->qty_jual.' '.$row->uom_jual,
                                          'qty2_jual'   => $row->qty2_jual.' '.$row->uom2_jual,
                                          'no_pl'       => $row->no_pl,
                                          'ket_exp'     => $ket_exp,
                                          'lot_asal'    => $row->lot_asal
    	    							);
    	    	}

                $allcount   = $this->m_stock->get_record_stock($where_lokasi,$where_result);
            }

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

            
        $total_record       = $name_total.' '. number_format($allcount);

    	$callback  = array('group' => $group, 'record' => $dataRecord, 'pagination'=>$pagination, 'total_record'=>$total_record, 'sql' => $where_result.' '.$order_by);

    	echo json_encode($callback);
    	
    }

    public function loadChild()
    {
        $kode        = $this->input->post('kode');
        $group_by    = $this->input->post('group_by');
        $group_ke    = $this->input->post('group_ke');
        $data_filter = json_decode($this->input->post('arr_filter'),true); // tampung arr filter
        $data_group  = json_decode($this->input->post('arr_group'),true);
        $record      = $this->input->post('record');
        $tbody_id    = $this->input->post('tbody_id');
        $root        = $this->input->post('root');
        $post_tmp_group  = json_decode($this->input->post('tmp_arr_group'),true);
        // $transit_location = $this->input->post('transit');
        $where_lokasi = " (sq.lokasi LIKE '%Stock%' OR sq.lokasi  LIKE '%Transit Location%') ";

        // if($transit_location == 'true'){
        // }else{
        //     $where_lokasi = " sq.lokasi LIKE '%Stock%'  ";
        // }

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

			$order_by          = rtrim($order_by, ', ');
            $order_by          = "ORDER BY ".$order_by;
            if(!empty($order_by_in_group)){
                $order_by_in_group = rtrim($order_by_in_group, ', ');
                $order_by_in_group = "ORDER BY ".$order_by_in_group;
            }
        }else{
            $order_by = "ORDER BY sq.quant_id desc";
            $order_by_in_group = "ORDER BY sq.quant_id desc";
        }


        $list_items  = [];
        $tmp_arr_group = [];
        $all_page    = 0;
        $allcount    = 0;
        $list_group  = [];
        $where_result= '';
        $by2         = '';
        $by2_real    = '';


        // create where
        $where         = $this->create_where($data_filter);
        if($kode == 'null' && $group_by == 'category'){
            $where_result  .= $where ." AND ".$this->declaration_name_field($group_by)." = '".$kode."'";
            $group_by       = $this->declaration_name_field($group_by);
        }else{
            $where_result  .= $where ." AND ".$this->declaration_name_field_group($group_by)." = '".$kode."'";
            $group_by       = $this->declaration_name_field_group($group_by);
        }

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
        $page_now       = $record+1;
        $recordPerPage  = 10;

        if($record != 0){
           $record = ($record-1) * $recordPerPage;
        }

        if($group_ke == count($data_group)){// loadchild list
            $tgl_sekarang = date('Y-m-d');
            $tgl_sebelum = date('Y-m-d', strtotime('-3 month', strtotime($tgl_sekarang)));

            $list = $this->m_stock->get_list_stock_by($where_lokasi,$where_result,$order_by,$record,$recordPerPage)->result();
            foreach ($list as $row) {
            	# code...
                if(date('Y-m-d', strtotime($row->create_date)) < $tgl_sebelum){
                    $ket_exp = 'Expired';
                }else{
                    $ket_exp = '';
                }
                $list_items[] = array(
                                        'lot'         => $row->lot,
                                        'tgl_dibuat'  => $row->create_date,
                                        'tgl_diterima'=> $row->move_date,
                                        'grade'       => $row->nama_grade,
                                        'lokasi'      => $row->lokasi,
                                        'lokasi_fisik'  => $row->lokasi_fisik,
                                        'kode_produk' => $row->kode_produk,
                                        'nama_produk' => $row->nama_produk,
                                        'corak_remark' => $row->corak_remark,
                                        'warna_remark' => $row->warna_remark,
                                        'kategori'    => $row->nama_category,
                                        'qty' 		=> $row->qty.' '.$row->uom,
                                        'qty2'        => $row->qty2.' '.$row->uom2,
                                        'lebar_greige'=> $row->lebar_greige.' '.$row->uom_lebar_greige,
                                        'lebar_jadi'  => $row->lebar_jadi.' '.$row->uom_lebar_jadi,
                                        'sales_order' => $row->sales_order,
                                        'sales_group' => $row->nama_sales_group,
                                        'qty_opname'  => $row->qty_opname.' '.$row->uom_opname,
                                        'umur_produk' => $row->umur,
                                        'no_pl'       => $row->no_pl,
                                        'qty_jual'    => $row->qty_jual.' '.$row->uom_jual,
                                        'qty2_jual'   => $row->qty2_jual.' '.$row->uom2_jual,
                                        'ket_exp'     => $ket_exp,
                                        'lot_asal'      => $row->lot_asal,
                                    );
            }
            $allcount  = $this->m_stock->get_record_stock($where_lokasi,$where_result);
            $all_page = ceil($allcount/$recordPerPage);


        }else{ // loadchild group

            $row   = '';
            $no    = 1;
            $group_ke_next = 0;
            $group    = $tbody_id;
            $by2      = $this->declaration_name_field_group($data_group[$group_ke]['nama_field']);
            $by2_real = ($data_group[$group_ke]['nama_field']);
            
            $list = $this->m_stock->get_list_stock_grouping($where_lokasi,$by2, $where_result,$order_by_in_group,$record,$recordPerPage);
            $group_ke_next = $group_ke + 1;
        
            /*    
            foreach ($list as $gp2) {
                # code...
                $groupOf = $group;
                $id = $group.'-'.$no;
                $row .= "<tbody  data-root='".$groupOf."' data-parent='".$groupOf."' id='".$id."'>";
                $row .= "<tr>";
                $row .= "<td></td>";
                $row .= "<td class='show collapsed group1'  href='#' data-content='edit' data-isi='".$gp2->nama_field."' data-group='".$by2."' data-tbody='".$id."' group-ke='".$group_ke_next."'' data-root='".$groupOf."' node-root='No' style='cursor:pointer;'><i class='glyphicon glyphicon-plus'></td>";
                $row .= "<td colspan='4'>".$gp2->grouping."</td>";
                $row .= "<td align='right' colspan='2'>Qty1 = ".number_format($gp2->tot_qty,2)."</td>";
                $row .= "<td align='right'colspan='2'>Qty2 = ".number_format($gp2->tot_qty2,2)."</td>";
                $row .= "<td colspan='3' class='list_pagination'></td>";
                $row .= "</tr>";
                $row .="</tbody>";
                $no++;
            } */

            foreach ($list as $gp2) {
        		$list_group[]  = array('group'      => 'Yes',
        							   'nama_field' => $gp2->nama_field,
        							   'grouping'   => $gp2->grouping,
        							   'qty'        => 'Qty1 = '.number_format($gp2->tot_qty,2),
        							   'qty2'       => 'Qty2 = '.number_format($gp2->tot_qty2,2),
                                       'by'         => $tbody_id
        						);
        	}


            $allcount = $this->m_stock->get_record_list_stock_grouping($where_lokasi, $by2, $where_result);
            $all_page = ceil($allcount/$recordPerPage);

            // create array tmpung group yang terbuka / collapsed
            $tmp_arr_group = array('tbody_id' => $tbody_id, 'by' => $group_by, 'value' => $kode);

        }

        $callback = array('record'          => $list_items,
                          'tbody_id'        => $tbody_id, 
                          'total_record'    => $allcount,
                          'all_page'        => $all_page,
                          'page_now'        => $page_now,
                          'group_ke'        => $group_ke,
                          'group_by'        => $by2_real,
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
                    
                    }else if($row['nama_field'] == 'move_date' OR $row['nama_field'] == 'create_date' ){
                        $date_format = date('Y-m-d H:i:s', strtotime($row['value']));
                        if($row['operator'] == '<'){
                            $isi        = "< '".$date_format."' ";
                        }else if($row['operator'] == '>'){
                            $isi        = "> '".$date_format."' ";
                        }else if($row['operator'] == '<='){
                            $isi        = "<= '".$date_format."' ";
                        }else{ 
                            // ($row['operator'] == '>='){
                            $isi        = ">= '".$date_format."' ";
                        }

                        $nama_field = " sq.".$row['nama_field']." ";

                    }else if($row['nama_field'] == 'ket_exp'){ 
                        $tgl_sekarang = date('Y-m-d');
                        $tgl_sebelum = date('Y-m-d', strtotime('-3 month', strtotime($tgl_sekarang)));
                        if($row['value'] == 'Yes'){
                            $isi = " <= '".$tgl_sebelum."' ";
                        }else{
                            $isi = " >= '".$tgl_sebelum."' ";
                        }
                        $nama_field = " STR_TO_DATE(sq.create_date,'%Y-%m-%d') ";

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
                        if($row['nama_field'] == 'category'){
                            $nama_field = $this->declaration_name_field_group($row['nama_field']);
                        }else{
                            $nama_field = $this->declaration_name_field($row['nama_field']);
                        }

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
        if($nama_field == 'category'){
            $where = 'mp.id_category';
        }else if($nama_field == 'no_pl'){
            $where = 'no_pl';
        }else if($nama_field == 'kp_lot'){
            $where = 'kp_lot.lot';
        }else{
            $where = 'sq.'.$nama_field;
        }
        return $where;
    }

    function declaration_name_field_group($nama_field)
    {
        if($nama_field == 'category'){
            $where = 'cat.nama_category';
        }else{
            $where = 'sq.'.$nama_field;
        }
        return $where;
    }

    function cek_column_excel($jml)
    {
        $max    = $jml; 
        $result = 'A';
        $arr_column = [];
        for ($l = 'A', $i = 1; $i < $max; $l++, $i++) {
            // if($i == $index){
            //     $result = $l;
            // }
            $arr_column[] = $l;
        }
        return $arr_column;
    }


    function export_excel_stock()
    {

    	$this->load->library('excel');
        ob_start();
        $where_result   = $this->input->post('sql');
        $checkboxes_hide= $this->input->post('checkboxes_hide');

       
        //$order_by = "ORDER BY quant_id desc";
        $where_lokasi = " (lokasi LIKE '%Stock%' OR lokasi  LIKE '%Transit Location%') ";

        // $transit_location = $this->input->post('transit[]');

        // if(count($transit_location) > 0){
        // }else{
        //     $where_lokasi = " lokasi LIKE '%Stock%'  ";
        // }

    	$object = new PHPExcel();
    	$object->setActiveSheetIndex(0);
        ini_set('memory_limit', '4096M');
    	// SET JUDUL
 		$object->getActiveSheet()->SetCellValue('A1', 'Laporan Stock');
 		$object->getActiveSheet()->getStyle('A1')->getAlignment()->setIndent(1);
		$object->getActiveSheet()->mergeCells('A1:L1');

        //bold huruf
        $object->getActiveSheet()->getStyle("A1:AF4")->getFont()->setBold(true);

        // Border 
		$styleArray = array(
            'borders' => array(
              'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN
              )
            )
        );
        
        // header table
        $table_head_columns  = array('No', 'Quant ID','Lot', 'Grade', 'Tgl diterima', 'Lokasi', 'Lokasi Fisik', 'Kode Produk', 'Nama Produk', 'Corak Remark', 'Warna Remark', 'Kategori' ,'Qty1 [HPH]','Uom1 [HPH]', 'Qty2 [HPH]', 'Uom2 [HPH]', 'Qty1 [JUAL]', 'Uom1 [JUAL]' , 'Qty2 [JUAL]', 'Uom2 [JUAL]', 'Lbr Greige', 'Uom Lbr Greige', 'Lbr Jadi', 'Uom Lbr Jadi', 'SC', 'Marketing',' Qty Opname','Uom Opname', 'Picklist (PL)', 'KP/Lot Asal', 'Ket Expired','Umur (Hari)');

        $column = 0;
        foreach ($table_head_columns as $judul) {
            $object->getActiveSheet()->setCellValueByColumnAndRow($column, 4, $judul);  
            $column++;
        }

        // set with and border
    	// $index_header = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y', 'Z', 'AA', 'AB','AC','AD','AE','AF');
        $index_header = $this->cek_column_excel(33);
    	$loop = 0;
    	foreach ($index_header as $val) {
    		
    		$object->getActiveSheet()->getStyle($val.'4')->applyFromArray($styleArray);
            $object->getActiveSheet()->getStyle($val.'5')->applyFromArray($styleArray);
        }

        $tgl_sekarang = date('Y-m-d');
        $tgl_sebelum = date('Y-m-d', strtotime('-3 month', strtotime($tgl_sekarang)));

        //body
        $num      = 1;
        $rowCount = 5;
        $list     = $this->m_stock->get_list_stock_by_noLimit($where_lokasi,$where_result)->result();
        $tmp_val  = [];
        foreach ($list as $val) {
            # code...
            if(date('Y-m-d', strtotime($val->create_date)) < $tgl_sebelum){
                $ket_exp = 'Expired';
            }else{
                $ket_exp = '';
            }
            $object->getActiveSheet()->SetCellValue('A'.$rowCount, ($num++));
            $object->getActiveSheet()->SetCellValue('B'.$rowCount, $val->quant_id);
            $object->getActiveSheet()->SetCellValue('C'.$rowCount, $val->lot);
            $object->getActiveSheet()->SetCellValue('D'.$rowCount, $val->nama_grade);
            $object->getActiveSheet()->SetCellValue('E'.$rowCount, $val->move_date);
            $object->getActiveSheet()->SetCellValue('F'.$rowCount, $val->lokasi);
            $object->getActiveSheet()->SetCellValue('G'.$rowCount, $val->lokasi_fisik);
            $object->getActiveSheet()->SetCellValue('H'.$rowCount, $val->kode_produk);
            $object->getActiveSheet()->SetCellValue('I'.$rowCount, $val->nama_produk);
            $object->getActiveSheet()->SetCellValue('J'.$rowCount, $val->corak_remark);
            $object->getActiveSheet()->SetCellValue('K'.$rowCount, $val->warna_remark);
            $object->getActiveSheet()->SetCellValue('L'.$rowCount, $val->nama_category);
            $object->getActiveSheet()->SetCellValue('M'.$rowCount, $val->qty);
            $object->getActiveSheet()->SetCellValue('N'.$rowCount, $val->uom);
            $object->getActiveSheet()->SetCellValue('O'.$rowCount, $val->qty2);
            $object->getActiveSheet()->SetCellValue('P'.$rowCount, $val->uom2);
            $object->getActiveSheet()->SetCellValue('Q'.$rowCount, $val->qty_jual);
            $object->getActiveSheet()->SetCellValue('R'.$rowCount, $val->uom_jual);
            $object->getActiveSheet()->SetCellValue('S'.$rowCount, $val->qty2_jual);
            $object->getActiveSheet()->SetCellValue('T'.$rowCount, $val->uom2_jual);
            $object->getActiveSheet()->SetCellValue('U'.$rowCount, $val->lebar_greige);
            $object->getActiveSheet()->SetCellValue('V'.$rowCount, $val->uom_lebar_greige);
            $object->getActiveSheet()->SetCellValue('W'.$rowCount, $val->lebar_jadi);
            $object->getActiveSheet()->SetCellValue('X'.$rowCount, $val->uom_lebar_jadi);
            $object->getActiveSheet()->SetCellValue('Y'.$rowCount, $val->sales_order);
            $object->getActiveSheet()->SetCellValue('Z'.$rowCount, $val->nama_sales_group);
            $object->getActiveSheet()->SetCellValue('AA'.$rowCount, $val->qty_opname);
            $object->getActiveSheet()->SetCellValue('AB'.$rowCount, $val->uom_opname);
            $object->getActiveSheet()->SetCellValue('AC'.$rowCount, $val->no_pl);
            $object->getActiveSheet()->SetCellValue('AD'.$rowCount, $val->lot_asal);
            $object->getActiveSheet()->SetCellValue('AE'.$rowCount, $ket_exp);
            $object->getActiveSheet()->SetCellValue('AF'.$rowCount, $val->umur);

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
			$object->getActiveSheet()->getStyle('U'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('V'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('W'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('X'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('Y'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('Z'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('AA'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('AB'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('AC'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('AD'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('AE'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('AF'.$rowCount)->applyFromArray($styleArray);

            $rowCount++;

        }

        if(count($checkboxes_hide)){
            //hide column
            foreach ($checkboxes_hide as $val) {
                if($val == 13 ){ // qty1 HPH
                    $object->getActiveSheet()->removeColumn('M');
                    $object->getActiveSheet()->removeColumn('M');
                }else if($val == 14){
                    if(count($checkboxes_hide) == 1){
                        $object->getActiveSheet()->removeColumn('O');
                        $object->getActiveSheet()->removeColumn('O');
                    }else{
                        $object->getActiveSheet()->removeColumn('M');
                        $object->getActiveSheet()->removeColumn('M');
                    }
                }
            }
        }

        $object = PHPExcel_IOFactory::createWriter($object, 'Excel2007');  
		$object->save('php://output');
		$xlsData = ob_get_contents();
		ob_end_clean();
		$name_file ='Stock.xlsx';
		$response =  array(
			'op'        => 'ok',
			'file'      => "data:application/vnd.ms-excel;base64,".base64_encode($xlsData),
			'filename'  => $name_file
		);
		
		die(json_encode($response));


    }


}