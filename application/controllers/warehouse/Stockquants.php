<?php 
defined('BASEPATH') OR exit('No Direct Script Acces Allowed');
/**
 * 
 */
class Stockquants extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->is_loggedin();//cek apakah user sudah login
		$this->load->model("_module");//load model global
		$this->load->model("m_stockQuants");
        $this->load->library('pagination');

	}

	public function index()
	{
		$id_dept ='SQ';
        $data['id_dept'] = $id_dept;
        $username        = $this->session->userdata('username');

        $data['user_filter'] = $this->_module->get_list_user_filter($id_dept,$username);

		//$data['tbody1'] = $this->m_stockQuants->get_list_stock_quant_grouping(); 
        $type_condition = $this->_module->get_first_type_conditon($id_dept);
        $data['mstFilter']      = $this->_module->get_list_mst_filter($id_dept);
        $data['type_condition'] = $type_condition;
        $this->load->view('warehouse/v_stock_quants',$data);
	}

    public function conditionFilter()
    {
        $kode_element = $_POST['element'];
        $id_dept      = $_POST['id_dept'];

        $type_condition = $this->_module->get_type_conditon($id_dept,$kode_element);
        $callback = array('type_condition'=>$type_condition);
        echo json_encode($callback);

    }

    public function simpan()
    {

        try{
            if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
                // session habis
                throw new \Exception('Waktu Anda Telah Habis', 401);
            }else{

                // start transaction
                $this->_module->startTransaction();

                $username    = addslashes($this->session->userdata('username'));
                $sub_menu    = $this->uri->segment(2);
                $quant_id    = $this->input->post('quant_id');
                $nama_produk = addslashes($this->input->post('nama_produk'));
                $lot         = addslashes($this->input->post('lot'));
                $qty2        = $this->input->post('qty2');
                $uom2        = $this->input->post('uom2');
                $nama_grade  = $this->input->post('nama_grade');
                $reff_note   = addslashes($this->input->post('reff_note'));
                $lebar_greige       = addslashes($this->input->post('lebar_greige'));
                $uom_lebar_greige   = addslashes($this->input->post('uom_lebar_greige'));
                $lebar_jadi         = addslashes($this->input->post('lebar_jadi'));
                $uom_lebar_jadi     = addslashes($this->input->post('uom_lebar_jadi'));
                $lokasi     = addslashes($this->input->post('lokasi'));

                if(empty($quant_id)){
                    throw new \Exception("Data Kosong !", 200);
                }else{

                    // get data stock_quant by quant_id sebelumnya
                    $sq   = $this->m_stockQuants->get_stock_quant_by_kode($quant_id);

                    if(empty($sq)){
                        throw new \Exception("Data Produk tidak ditemukan !", 200);
                    }else{
                        // cek category produk
                        $cek_cat = $this->m_stockQuants->get_kategori_produk_by_produk($sq->kode_produk);
                        if((int) $cek_cat->id_category == 21){// kain hasil gudang jadi
                            if($sq->nama_grade != $nama_grade){
                                throw new \Exception(" Nama Grade tidak Boleh dirubah !", 200);
                            }else if($sq->lebar_greige != $lebar_greige){
                                throw new \Exception(" Lebar Greige tidak Boleh dirubah !", 200);
                            }else if($sq->uom_lebar_greige != $uom_lebar_greige){
                                throw new \Exception(" Uom Lebar Greige tidak Boleh dirubah !", 200);
                            }else if($sq->lebar_jadi != $lebar_jadi){
                                throw new \Exception(" Lebar Jadi tidak Boleh dirubah !", 200);
                            }else if($sq->uom_lebar_jadi != $uom_lebar_jadi){
                                throw new \Exception(" Uom Lebar Jadi tidak Boleh dirubah !", 200);
                            }
                        }
                        $note_before = $sq->kode_produk.' '.$sq->nama_produk.' '.$sq->lot.' '.$sq->nama_grade.' '.$sq->qty.' '.$sq->uom.' '.$sq->qty2.' '.$sq->uom2.' | '.$sq->lebar_greige.' '.$sq->uom_lebar_greige.' | '.$sq->lebar_jadi.' '.$sq->uom_lebar_jadi.' | '.$sq->lokasi.' '.$sq->reff_note; 
        
                        $this->m_stockQuants->update_stockquants($quant_id,$nama_grade,$reff_note,$lebar_greige,$uom_lebar_greige,$lebar_jadi,$uom_lebar_jadi);
        
                        $jenis_log   = "edit";        
                        $note_log    = $note_before.' <b> -> </b>'. $sq->kode_produk.' '.$nama_produk.'  '.$lot.'  '.$nama_grade.' '.$qty2.' '.$uom2.' | '.$lebar_greige.' '.$uom_lebar_greige.' | '.$lebar_jadi.' '.$uom_lebar_jadi.' | '.$lokasi.' '.$reff_note;
                        $this->_module->gen_history($sub_menu, $quant_id, $jenis_log, $note_log, $username);
        
                        $callback = array('status' => 'success','message' => 'Data Berhasil Disimpan !', 'icon' =>'fa fa-check', 'type' => 'success');
                    }
    
                }
                // finish transaction
                $this->_module->finishTransaction();
                $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($callback));
            }
        
        }catch(Exception $ex){
            // finish transaction
            $this->_module->finishTransaction();
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('status'=>'failed', 'message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }


    public function simpan_user_filter()
    {

        if(empty($this->session->userdata('status'))){//cek apa session masih ada ?
            $callback = array('message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
        }else{

            $username    = $this->session->userdata('username');
            $id_dept     = $_POST['id_dept'];
            $inisial_class = $_POST['inisial_class'];
            $nama_filter = $_POST['nama_filter'];
            $use_default = $_POST['use_default'];
            $data_grouping = '';
            $data_filter = '';
            if(empty($_POST['data_filter'])){
                $data_filter = '';
            }else{
                foreach ($_POST['data_filter'] as $val) {
                    $data_filter .= $val['query'].'|^';
                }
                $data_filter = rtrim($data_filter, '|^');
            }

            if(empty($_POST['data_grouping'])){
                $data_grouping  = '';
            }else{
                foreach ($_POST['data_grouping'] as $val) {
                    $data_grouping .= $val['nama_field'].'|^';
                }
                $data_grouping = rtrim($data_grouping, '|^');
            }
                
            //cek ke tbl user filter apa sudah ada default
            $check = $this->_module->check_default_user_filter($username,$id_dept,$inisial_class,$use_default)->row_array();
            if($check['use_default'] == 'true'){
                $callback = array('status'=>'failed', 'message'=>'Maaf, favorite default sudah dipakai '.$check['nama_filter']);
            }else{
                $this->_module->save_user_filter($username,$id_dept,$inisial_class,$nama_filter,$data_filter,$data_grouping,$use_default);
                $callback = array('status'=>'success', 'message'=>'Data Berhasil Disimpan !');
            }
        }

        echo json_encode($callback);
    }


    public function hapus_user_filter()
    {
        if(empty($this->session->userdata('status'))){//cek apa session masih ada ?
            $callback = array('message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
        }else{
            $username = $this->session->userdata('username');
            $id_dept  = $_POST['id_dept'];
            $inisial_class = $_POST['inisial_class'];
            $nama_filter   = $_POST['nama_filter'];

            $this->_module->delete_user_filter($username,$id_dept,$inisial_class,$nama_filter);
            $callback  = array('status'=>'success','message'=>'Data Berhasil Dihapus !');
        }

        echo json_encode($callback);
    }


    public function edit($id = null)
    {
        if(!isset($id)) show_404();
            $quant_id = decrypt_url($id);
            $data['id_dept']    ='SQ';
            $data['list']       = $this->m_stockQuants->get_stock_quant_by_kode($quant_id);
            $data['list_uom']   = $this->_module->get_list_uom();
            $data['list_grade'] = $this->_module->get_list_grade();
            if(empty($data['list'])){
              show_404();
            }else{
              return $this->load->view('warehouse/v_stock_quants_edit',$data);
            }
    }


    public function loadData($record=0)
    {   

        $recordPerPage = 30;
        if($record != 0){
           $record = ($record-1) * $recordPerPage;
        }

        $data_filter      = $this->input->post('data_filter'); 
        $data_filter_table= $this->input->post('data_filter_table'); //data filter dari table filer advanced
        $data_grouping = $this->input->post('data_grouping');
        $kolom_order   = $this->input->post('nama_kolom');
        $order         = $this->input->post('order');
        $id_dept       = $this->input->post('id_dept');
        
        $whereAll      = "";
        $no            = 1;
        $dataRecord    = [];
        $dataArr       = [];

        if(!empty($kolom_order)){
            $kolom_order = "ORDER BY ".$kolom_order;
        }else{
            $kolom_order = "ORDER BY quant_id";
            $order       = "desc";
        }

        // create where berdasarkan data post
        $result = $this->create_where($data_filter,$data_filter_table,$data_grouping,$id_dept);

        $where  = $result[0];
        $dataArr= $result[1];
        $tmp_arr_group = [];
        $group_ke  = '';

        // data_grouping / array group
        if(!empty($data_grouping)){


            if(count($data_grouping) == 1 ){
                $grouping1  = $this->grouping_1_params($data_grouping,$where,$id_dept,$kolom_order,$order);
                $dataRecord = $grouping1[0];
                $tmp_arr_group = $grouping1[1];
                $jml_group     = $grouping1[2];
                $group_ke  = 1;

            }elseif (count($data_grouping) == 2) {
                $grouping2  = $this->grouping_1_params($data_grouping,$where,$id_dept,$kolom_order,$order);
                $dataRecord = $grouping2[0];
                $tmp_arr_group = $grouping2[1];
                $jml_group     = $grouping2[2];
                $group_ke  = 1;
            }
       
            $name_total = "Total Group : ";
            //$allcount   = count($data_grouping);
            $allcount   = $jml_group;
            $pagination = '';
            $group   = true;

        }else{
           

            if(!empty($where)){
                //$where = rtrim($where, $condition);
                $where = "where lokasi NOT IN (SELECT empty_location FROM departemen WHERE show_dept = 'FALSE') AND ".$where;
                $whereAll = $where;
            }else{
                $where = "where lokasi NOT IN (SELECT empty_location FROM departemen WHERE show_dept = 'FALSE') ";
                $whereAll = $where;
            }

            $items = $this->m_stockQuants->get_list_stock_quant_by($where,$record,$recordPerPage,$kolom_order,$order);
            foreach ($items as $row) {
                $dataRecord[]  = array( 'no' =>$no,  'quant_id'   => encrypt_url($row->quant_id),
                                                    'kode_produk' => $row->kode_produk, 
                                                    'nama_produk' => $row->nama_produk,
                                                    'create_date' => $row->create_date,
                                                    'move_date'   => $row->move_date,
                                                    'lot'         => $row->lot,
                                                    'grade'       => $row->nama_grade,
                                                    'qty'         => $row->qty,
                                                    'uom'         => $row->uom,
                                                    'qty2'        => $row->qty2,
                                                    'uom2'        => $row->uom2,
                                                    'qty_opname'  => $row->qty_opname.' '.$row->uom_opname,
                                                    'lebar_greige'=> $row->lebar_greige.' '.$row->uom_lebar_greige,
                                                    'lebar_jadi'  => $row->lebar_jadi.' '.$row->uom_lebar_jadi,
                                                    'lokasi'      => $row->lokasi,
                                                    'lokasi_fisik'=> $row->lokasi_fisik,
                                                    'reff_note'   => $row->reff_note,
                                                    'reserve_move'=> $row->reserve_move);   

                $no++;
            }

            $allcount   = $this->m_stockQuants->getRecordCount($whereAll);
            $name_total="Total Data : ";

            $config['base_url']         = base_url().'warehouse/stockquants/loadData';
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
            $group   = false;

        }

        $total_record       = $name_total.' '. number_format($allcount);

        //$data['pagination']         = '';
        $callback  = array('record' => $dataRecord, 'dataArr' => $dataArr, 'pagination'=>$pagination, 'total_record'=>$total_record, 'group'=> $group, 'tmp_arr_group'=>$tmp_arr_group, 'group_ke' => $group_ke);

        echo json_encode($callback);

    }


    function grouping_1_params($arr_grouping,$where,$id_dept,$kolom_order,$order)
    {

        $ro    = 1;
        $row   = '';
        $jml_group= count($arr_grouping);
        $by       = $arr_grouping[0]['nama_field'];
        $groupBy  = $this->declaration_name_field($by,$id_dept);

        if(!empty($where)){
            $where1 = "where lokasi NOT IN (SELECT empty_location FROM departemen WHERE show_dept = 'FALSE') AND ".$where;
        }else{
            $where1 = "where lokasi NOT IN (SELECT empty_location FROM departemen WHERE show_dept = 'FALSE') ";
        }
       
        $tmp_arr_group = [];
        $jml_group     = 0;

        $group1   = $this->m_stockQuants->get_list_stock_quant_grouping($groupBy,$where1);
        foreach ($group1 as $gp1) {

            $group = 'group-of-rows-'.$ro;
            $row  .= "<tbody id='".$group."'>";
            $row  .= "<tr >";
            $row  .= "<td class='show collapsed group1' href='#' style='cursor:pointer;'  data-content='edit' data-isi='".$gp1->nama_field."' data-group='".$by."' data-tbody='".$group."' data-root='".$group."' node-root='Yes' group-ke='1'><i class='glyphicon glyphicon-plus' ></i></td>";
            $row .= "<td colspan='5'>".$gp1->grouping."</td>";
            $row .= "<td align='right'>".$gp1->tqty."</td>";
            $row .= "<td colspan='3' class='list_pagination'></td>";
            $row .= "<td colspan='2' ></td>";
            $row .= "</tr>";
            $ro++;

            $tmp_arr_group[] = array('tbody_id' => $group, 'by'=> $by,'value' => $gp1->nama_field);

            /*
            $row .= "<tbody id='".$group."' class='collapse child'>";

            if(!empty($where)){
                $where2_add = "AND ".$where;
            }else{
                $where2_add = $where;
            }

            $where2 = "WHERE ".$groupBy." = '".$gp1->nama_field."' ".$where2_add." ";
            $group2  = $this->m_stockQuants->get_list_stock_quant_noLimit($where2,$kolom_order,$order);
            $no = 1;
                foreach ($group2 as $gp2) {
                    $quant_id = encrypt_url($gp2->quant_id);
                    $row .=  "<tr style='background-color: #f2f2f2;'>";
                    $row .= "<td>".$no++."</td>";
                    $row .= "<td>".$gp2->kode_produk."</td>";
                    $row .= "<td><a href='".base_url('warehouse/stockquants/edit/'.$quant_id)."'>".$gp2->nama_produk."</td>";
                    $row .= "<td>".$gp2->create_date."</td>";
                    $row .= "<td>".$gp2->lot."</td>";
                    $row .= "<td>".$gp2->nama_grade."</td>";
                    $row .= "<td>".$gp2->qty."</td>";
                    $row .= "<td>".$gp2->uom."</td>";
                    $row .= "<td>".$gp2->qty2."</td>";
                    $row .= "<td>".$gp2->uom2."</td>";
                    $row .= "<td>".$gp2->lokasi."</td>";
                    $row .= "<td>".$gp2->reff_note."</td>";
                    $row .= "<td>".$gp2->reserve_move."</td>";
                    $row .=  "</tr>";
                
                }

            $row .= "</tbody>";

            */
            $row .= "</tbody>";
            $jml_group++;

        }
        return array($row,$tmp_arr_group,$jml_group);
    }


    function grouping_2_params($arr_grouping,$where,$id_dept,$kolom_order,$order){

        $ro    = 1;
        $row   = '';

        $jml_group= count($arr_grouping);
        $by       = $arr_grouping[0]['nama_field'];
        $groupBy  = $this->declaration_name_field($by,$id_dept);
        $by2      = $arr_grouping[1]['nama_field'];
        $groupBy2  = $this->declaration_name_field($by2,$id_dept);

        if(!empty($where)){
            $where = "where lokasi NOT IN (SELECT empty_location FROM departemen WHERE show_dept = 'FALSE') AND ".$where;
        }else{
            $where1 = "where lokasi NOT IN (SELECT empty_location FROM departemen WHERE show_dept = 'FALSE') ";
        }

        $group1   = $this->m_stockQuants->get_list_stock_quant_grouping($groupBy,$where1);
        foreach ($group1 as $gp1) {

            $group = 'group-of-rows-'.$ro;

            $row .="<tbody>";
            $row .= "<tr >";
            $row .= "<td class='clickable' data-toggle='collapse' data-target='#".$group."' aria-expanded='false' aria-controls='".$group."' style='cursor:pointer;'><i class='glyphicon glyphicon-plus'></td>";
            $row .= "<td colspan='5'>".$gp1->grouping."</td>";
            $row .= "<td>".$gp1->tqty."</td>";
            $row .= "<td></td>";
            $row .= "<td></td>";
            $row .= "<td></td>";
            $row .= "<td></td>";
            $row .= "</tr>";
            $row .= "</tbody>";
            $ro++;

            /*

            $row .= "<tbody id='".$group."' class='collapse child'>";
            
            if(empty($where)){
                $where2 = "WHERE ".$groupBy." = '".$gp1->nama_field."' ";
            }else{
                $where2 = $where1." AND ".$groupBy." = '".$gp1->nama_field."' ";
            }

            $ro2 = 1;
            $group2   = $this->m_stockQuants->get_list_stock_quant_grouping($groupBy2,$where2);
            foreach ($group2 as $gp2) {

                $groupOf = 'group-of-rows-'.$ro.'-'.$ro2;
                $row .= "<tr >";
                $row .= "<td></td>";
                $row .= "<td class='clickable' data-toggle='collapse' data-target='.".$groupOf."' aria-expanded='false' aria-controls='".$groupOf."' style='cursor:pointer;'><i class='glyphicon glyphicon-plus'></td>";
                $row .= "<td colspan='4'>".$gp2->grouping."</td>";
                $row .= "<td>".$gp2->tqty."</td>";
                $row .= "<td></td>";
                $row .= "<td></td>";
                $row .= "<td></td>";
                $row .= "<td></td>";
                $row .= "</tr>";
                $ro2++;

                if(!empty($where)){
                    $where3_add = "AND ".$where;
                }else{
                    $where3_add = $where;
                }
                
                $where3 = "WHERE ".$groupBy." = '".$gp1->nama_field."' AND ".$groupBy2." = '".$gp2->nama_field."' ".$where3_add." ";
                $group3  = $this->m_stockQuants->get_list_stock_quant_noLimit($where3,$kolom_order,$order);
                $no = 1;
                    foreach ($group3 as $gp3) {
                        $quant_id = encrypt_url($gp3->quant_id);
                        $row .=  "<tr class='".$groupOf." collapse child'  style='background-color: #f2f2f2;' >";
                        $row .= "<td>".$no++."</td>";
                        $row .= "<td>".$gp3->kode_produk."</td>";
                        $row .= "<td><a href='".base_url('warehouse/stockquants/edit/'.$quant_id)."'>".$gp3->nama_produk."</td>";
                        $row .= "<td>".$gp3->create_date."</td>";
                        $row .= "<td>".$gp3->lot."</td>";
                        $row .= "<td>".$gp3->nama_grade."</td>";
                        $row .= "<td>".$gp3->qty."</td>";
                        $row .= "<td>".$gp3->uom."</td>";
                        $row .= "<td>".$gp3->qty2."</td>";
                        $row .= "<td>".$gp3->uom2."</td>";
                        $row .= "<td>".$gp3->lokasi."</td>";
                        $row .= "<td>".$gp3->reff_note."</td>";
                        $row .= "<td>".$gp3->reserve_move."</td>";
                        $row .=  "</tr>";
                    }
                //break;
            }
                
            $row .= "</tbody>";
            */

        }

        return $row;
    }

    function create_where($data_filter,$data_filter_table,$data_grouping,$id_dept){

        $data_filter  = $data_filter; 
        $data_filter_table= $data_filter_table; //data filter dari table filer advanced
        $data_grouping = $data_grouping;
        
        $id_dept      = $id_dept;
        //$dataRecord   = [];
        $where        = "";
        //$whereAll     = "";
        $dataArr      = [];
        $where_table  = "";
        $where_df     = "";

        //data_filter_table
        if(!empty($data_filter_table)){

            //$whereAll = "";  
            $caption  = "";
            $loop_for = 1;
            $tmp_nama_field = "";
            $tmp_caption    = "";
            $loop     = false;

            // data_filter_tabel dari table filter advanved
            foreach ($data_filter_table as $row) {
                $loop = true;
           
                $condition = 'OR';

                // operator ex and, like, =, =>, ect
                if($row['operator'] == 'LIKE' OR $row['operator'] == 'NOT LIKE' ){
                    $isi = $row['operator']." '%".addslashes($row['isi'])."%' ";
                    $operator = $row['operator'];
                   // $whereAll .= "LIKE '%".$row['isi']."%' ".$condition;
                }else if($row['nama_field'] == 'qty_opname'){
                    if($row['isi'] == 'done'){
                        $isi = " > 0 ";
                    }else{
                        $isi = " = 0 ";
                    }

                    $operator = $row['operator'];
                }else{
                    $isi = $row['operator']." '".addslashes($row['isi'])."' ";
                    $operator = $row['operator'];
                    //$whereAll .= $row['operator']." '".$row['isi']."' ".$condition;
                }
                
                $qry_where = $this->declaration_name_field($row['nama_field'],$id_dept);
                if(!empty($qry_where)){
                    $nama_field = $qry_where;
                    $tmp_nama_field .= $row['nama_field'].'^-|'.$operator.'^-|'.$row['isi'].'^-|,';
                }else{
                    break;
                }
                
                if($loop_for == 1 ){
                    $where .= $nama_field.' '.$isi.' ';
                }else{
                    $where .= $condition.' '.$nama_field.' '.$isi;
                }

                $nama_element = $this->_module->get_nama_element_by_kode($row['nama_field'],$id_dept);  
                $tmp_caption  .= $nama_element.' '.$operator.' '.$row['isi'].' '.$condition.' ';  

                $loop_for++;

            }// end foreach data_filter_table
                
            if($loop == true){

                $tmp_caption = rtrim($tmp_caption, $condition.' ');
                $tmp_nama_field = rtrim($tmp_nama_field, '^-|,');
                //buat ke arr
                $dataArr[] = array('caption'     => $tmp_caption, // ex Nama Produk LIKE FOY
                                    'nama_field' => $tmp_nama_field, // ex nama_produk LIKE '%FOY%'
                                    'operator'   => 'kosong',
                                    'isi'        => 'kosong',
                                    'condition'  => 'OR');
            }

            //untuk menambahkan tanda ( )
            if($loop_for  > 1 AND $loop == true){
                $where  = ' ( '.$where.' ) ';
            }

            $where_table .= $where;
            $where       = '';

        }// end if data_filter_table
        

        // data_filter
        if(!empty($data_filter)){
            $loop_data_ex = 1;
            $loop     = 1;
            $type_table = false;
            $type_textfield = false;
            $before   = '';
            $before2  = '';
            $after    = '';           
            $loop_text= 1;
            foreach ($data_filter as $row) {
               

                    if($row['type'] == 'table'){ //jika bukan dari favorite

                    $type_table = true;

                    $data_ex = explode("^-|,", $row['nama_field']); // ex nama_produk^-|LIKE^-|foy^-|,kode_produk^-|LIKE^-|0206^-|,

                    if($type_textfield == true){
                        $before = ' AND ';
                        $where  .= $before;
                    }

                    if($type_textfield == false AND $loop > 1){
                        $before = ' AND ';
                        $where  .= $before;
                    }
                   
                    foreach($data_ex as $row1) {
                        $data    = explode("^-|",$row1);
                        //$nama_field = $data[0];
                        $qry_where = $this->declaration_name_field($data[0],$id_dept);
                        if(!empty($qry_where)){
                            $nama_field = $qry_where;
                        }else{
                            break;
                        } 

                        $data_ke = 0;
                        foreach ($data as $data1) {
                            # code...
                            if($data_ke == 1){
                                $operator  = $data1;
                            }
                            if($data_ke == 2){
                                $isi = addslashes($data1);
                            }

                            $data_ke++;
                        }

                        if($operator == 'LIKE' OR $operator == 'NOT LIKE' ){
                            $isi_ = $operator." '%".addslashes($isi)."%' ";
                            // $operator = 'LIKE';
                        }else if($nama_field == 'qty_opname'){
                            if($isi == 'done'){
                                $isi_ = " > 0 ";
                            }else{
                                $isi_ = " = 0 ";
                            }
                        }else{
                            $isi_ = $operator." '".addslashes($isi)."' ";
                            // $operator = $row['operator'];
                        }

                        $where .= $nama_field.' '.$isi_.' '.$row['condition'].' ';// condition = OR
                        $loop_data_ex++;
                    }

                    if(!empty($where)){
                        $where = rtrim($where, $row['condition'].' ');
                        if($loop_data_ex > 2){
                            $where = ' ( '.$where.' ) ';
                        }else{
                            $where = $where;
                        }
                    }

                    $type_textfield = false;
                }


                if($row['type'] == 'textfield'){
                    
                    $type_textfield = true;

                    if($type_table == true){
                        $before = ' AND ';
                    }

                    if($type_table == false AND $loop > 1){
                        $before = ' AND ';
                    }
                    
                    $qry_where = $this->declaration_name_field($row['nama_field'],$id_dept);
                    if(!empty($qry_where)){
                        $nama_field = $qry_where;
                    }else{
                        break;
                    } 

                    if($row['operator'] == 'LIKE'){
                        $isi = "LIKE '%".addslashes($row['isi'])."%' ";
                        $operator = 'LIKE';
                    }else{
                        $isi = $row['operator']." '".addslashes($row['isi'])."' ";
                        $operator = $row['operator'];
                    }

                    if(empty($data_filter_table) AND $type_table == false AND $loop == 1 ){
                        $condition = 'AND ';
                        $where .= $nama_field.' '.$isi;
                        //$where .= $nama_field.' '.$isi.' '.$condition;
                    }else{
                        //$where .= $before.' '.$nama_field.' '.$isi.' '.$after;
                        $where .= $before.' '.$nama_field.' '.$isi;

                    }

                    $type_table = false;

                }

                $loop++;
            }// end foreach data_filter

            $where_df = $where;

        }// end if data_filter

        if(!empty($where_table) OR !empty($where_df)){
            if(!empty($where_table)){
                if(!empty($where_df)){
                    $where_table = $where_table.' AND ';
                }
            }

            $where = $where_table.' '.$where_df;

        }

        return array($where,$dataArr);
    }


    function declaration_name_field($nama_field,$id_dept)
    {
          
        $where = $nama_field;
        return $where;
    }


    function loadChild($record=0)
    {
        $kode     = $this->input->post('kode');// nama field contoh kode_produk
        $group_by = $this->declaration_name_field($this->input->post('group_by'),''); // translate nama_field
        $tbody_id = $this->input->post('tbody_id');// id child berdasarkan parents nya
        $record   = $this->input->post('record');
        $group_ke = $this->input->post('group_ke');
        $id_dept       = $this->input->post('id_dept');
        $root          = $this->input->post('root');
        $data_grouping = $this->input->post('data_grouping');
        $data_filter   = $this->input->post('data_filter');
        $tmp_arr_group = $this->input->post('tmp_arr_group');
        $jml_group     = count($data_grouping);

        // create where by data_filter
        $result     = $this->create_where($data_filter,'',$data_grouping,$id_dept);
        $filter     = $result[0];
        $where_post = '';
        $where_post2 = '';

        if(!empty($filter)){
            $where_post .= ' AND '.$filter.'  '; 
        }else{
            $where_post .= ''; 
        }

        // cek jml array di tmpp_arr_group
        $jml_tmp_arr = count($tmp_arr_group);
        $count       = 1;
        $isi_arr     = '';
        foreach ($tmp_arr_group as $row) {
            # code...
            foreach ($row['record'] as $items) {
                # code...
                if($items['tbody_id'] == $root){
                    $isi_arr = $items['by']." = '".$items['value']."' ";
                    break;
                }
            }
        }

        if(!empty($isi_arr)){
            $where_post2 .= ' AND '.$isi_arr.' AND ';
        }

        $where      = '';
        $list_group = '';
        $list_items = [];

        // informasi page sekarang
        $page_now = $record+1;

        $recordPerPage = 10;
        if($record != 0){
           $record = ($record-1) * $recordPerPage;
        }

        $where     .= "WHERE lokasi NOT IN (SELECT empty_location FROM departemen WHERE show_dept = 'FALSE')  ".$where_post.' '.$where_post2.' '.$group_by." = '".$kode."' ";
        $whereCount =  $where;
        $allcount = 0;
        if($group_ke == $jml_group){
            $where .= "ORDER BY quant_id desc ";
            $where .= "LIMIT ".$record.",".$recordPerPage;
            $item  = $this->m_stockQuants->get_list_stock_quant_limit($where);
            foreach ($item as $val) {
                 # code...
                $list_items[] = array('kode_produk'=> $val->kode_produk,
                                      'id_encr'    => encrypt_url($val->quant_id),
                                      'nama_produk'=> $val->nama_produk,
                                      'create_date'=> $val->create_date,
                                      'move_date'  => $val->move_date,
                                      'lot'        => $val->lot,
                                      'nama_grade' => $val->nama_grade,
                                      'qty'        => $val->qty,
                                      'uom'        => $val->uom,
                                      'qty2'       => $val->qty2,
                                      'uom2'       => $val->uom2,
                                      'qty_opname' => $val->qty_opname.' '.$val->uom_opname,
                                      'lebar_greige'=> $val->lebar_greige.' '.$val->uom_lebar_greige,
                                      'lebar_jadi'  => $val->lebar_jadi.' '.$val->uom_lebar_jadi,
                                      'lokasi'      => $val->lokasi,
                                      'lokasi_fisik'=> $val->lokasi_fisik,
                                      'reff_note'   => $val->reff_note,
                                      'reserve_move'=> $val->reserve_move
                                  );
            }
            $allcount = $this->m_stockQuants->getRecordCount($whereCount);// get total semua record berdasarkan WHere

        }else{

            $row   = '';
            $no    = 1;
            $group_ke_next = 0;
            $group    = $tbody_id;
            $by2      = $data_grouping[$group_ke]['nama_field'];
            $groupBy2 = $this->declaration_name_field($by2,'');
            $group2   = $this->m_stockQuants->get_list_stock_quant_grouping($groupBy2,$where);
            $group_ke_next = $group_ke + 1;
        
            foreach ($group2 as $gp2) {
                # code...
                $groupOf = $group;
                $id = $group.'-'.$no;
                $row .= "<tbody  data-root='".$groupOf."' data-parent='".$groupOf."' id='".$id."'>";
                $row .= "<tr>";
                $row .= "<td></td>";
                $row .= "<td class='show collapsed group1'  href='#' data-content='edit' data-isi='".$gp2->nama_field."' data-group='".$by2."' data-tbody='".$id."' group-ke='".$group_ke_next."'' data-root='".$groupOf."' node-root='No' style='cursor:pointer;'><i class='glyphicon glyphicon-plus'></td>";
                $row .= "<td colspan='4'>".$gp2->grouping."</td>";
                $row .= "<td align='right'>".$gp2->tqty."</td>";
                $row .= "<td colspan='3' class='list_pagination'></td>";
                $row .= "<td colspan='2' ></td>";
                $row .= "</tr>";
                $row .="</tbody>";
                $no++;

            }

            $list_group  = $row;
        }

        // informasi pagination
        $all_page = ceil($allcount/$recordPerPage);

        $callback = array('record'          => $list_items,
                         'tbody_id'         => $tbody_id, 
                          'total_record'    => $allcount,
                          'all_page'        => $all_page,
                          'page_now'        => $page_now,
                          'list_group'      => $list_group,
                          'root'            => $root,
                          'limit'           => $recordPerPage
                        );

        echo json_encode($callback);
    }


    public function mode_print_modal()
    {
    	$quant_id   = $this->input->post('quant_id');
        $data['quant_id'] =$quant_id;
        return $this->load->view('modal/v_stock_quant_print_modal', $data);
    }


    function print_knitting()
    {
       
        $id       = $this->input->get('quant_id');
        $quant_id = decrypt_url($id);

        $sq       = $this->_module->get_stock_quant_by_id($quant_id)->row_array();
        
        if($sq){
            $barcode  = $sq['lot'];
            $nama_grade  = $sq['nama_grade'];

            $this->load->library('Pdf');//load library pdf

            $pdf=new PDF_Code128('l','mm',array(177.8,101.6));

            $pdf->AddPage();

                $pdf->SetFont('Arial','B',25,'C');
                $pdf->setXY(10,8);
                $pdf->Multicell(110,10,$barcode,0,'R');// Nama LOT 1
                //$pdf->Cell(100,5,$barcode,0,0,'R');// Nama LOT 1

                $pdf->SetFont('Arial','B',40);
                $pdf->setXY(120,5);
                $pdf->Multicell(30,13,$nama_grade,0,'L'); // grade
                //$pdf->Cell(0,3,$nama_grade,0,1);//grade
                
                $pdf->Code128(30,18,$barcode,110,23,'C');//barcode 1       
                
                
                $pdf->Line(20, 47, 170, 47); // garis tengah
                //$pdf->Cell(150,30,'','B',1,'C');//garis tengah   

                $pdf->SetFont('Arial','B',25,'C');
                $pdf->setXY(10,54);
                $pdf->Multicell(110,10,$barcode,0,'R');// Nama LOT 2
                //$pdf->Cell(100,30,$barcode,0,0,'R');

                $pdf->SetFont('Arial','B',40);
                $pdf->setXY(120,51);
                $pdf->Multicell(30,13,$nama_grade,0,'L'); // grade
                //$pdf->Cell(0,27,$nama_grade,0,1);//grade

                $pdf->Code128(30,65,$barcode,110,23,'C');//barcode 2

                $pdf->Line(170,3,170,100);//vertical

            $pdf->Output();

        }else{
            print_r('Maaf, Lot Tidak ditemukan !');
        }
    }



 

}