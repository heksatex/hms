<?php 
defined('BASEPATH') OR exit('No Direct Script Acces Allowed');
/**
 * 
 */
class Stockmoves extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->is_loggedin();//cek apakah user sudah login
		$this->load->model("_module");//load model global
		$this->load->model("m_stockMoves");
        $this->load->library('pagination');
	}

	public function index()
	{

        $id_dept         ='SM';
        $data['id_dept'] = $id_dept;
        $username        = $this->session->userdata('username');

        $data['user_filter'] = $this->_module->get_list_user_filter($id_dept,$username);

        //$data['tbody1'] = $this->m_stockQuants->get_list_stock_quant_grouping(); 
        $type_condition         = $this->_module->get_first_type_conditon($id_dept);
        $data['mstFilter']      = $this->_module->get_list_mst_filter($id_dept);
        $data['type_condition'] = $type_condition;
        $this->load->view('warehouse/v_stock_moves',$data);

	}

    public function conditionFilter()
    {
        $kode_element = $_POST['element'];
        $id_dept      = $_POST['id_dept'];

        $type_condition = $this->_module->get_type_conditon($id_dept,$kode_element);
        $callback = array('type_condition'=>$type_condition);
        echo json_encode($callback);

    }


    public function simpan_user_filter_sm(){

        if(empty($this->session->userdata('status'))){//cek apa session masih ada ?
            $callback = array('message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
        }else{

            $username      = addslashes($this->session->userdata('username'));
            $id_dept       = $this->input->post('id_dept');
            $inisial_class = $this->input->post('inisial_class');
            $nama_filter   = addslashes($this->input->post('nama_filter'));
            $use_default   = $this->input->post('use_default');
            $data_filter   = $this->input->post('data_filter');
            $data_grouping = $this->input->post('data_grouping');

            $dataFilter   = '';
            $dataGrouping = '';

            // arr_filter.push({caption:val.caption, nama_field : val.nama_field, operator:val.operator, isi : val.isi, type:'table'});
            // arr_grouping.push({favorite:'No', nama_field:groupBy, caption:caption, data_index:dataIndex});//add array grouping
            
            // bentuk data filter = caption|^nama_field|^operator|^isi|^condition|^textfield

            if(empty($data_filter)){
                $dataFilter = '';
            }else{
                foreach ($data_filter as $val){
                    
                    if($val['type'] == 'table'){// dari filter table


                        $data_ex = explode("^-|,", $val['nama_field']); // ex nama_produk^-|LIKE^-|foy^-|,kode_produk^-|LIKE^-|0206^-|,
                           
                        foreach($data_ex as $row1) {
                            $data    = explode("^-|",$row1);
                            $nama_field = $data[0];
                            $operator   = $data[1];
                            $isi        = $data[2];

                            $dataFilter .= $nama_field.'|^'.$operator.'|^'.addslashes($isi).'|^OR|^textfield|^,';
                        }

                    }

                    if($val['type'] == 'textfield'){
                        $dataFilter .= $val['nama_field'].'|^'.$val['operator'].'|^'.addslashes($val['isi']).'|^OR|^textfield|^,';
                    }
                }

                $dataFilter = rtrim($dataFilter, '|^,');
            }

            if(empty($data_grouping)){
                $dataGrouping  = '';
            }else{
                foreach ($data_grouping as $val) {
                    $dataGrouping .= $val['nama_field'].'|^,';
                }
                $dataGrouping = rtrim($dataGrouping, '|^,');
            }
                
            //cek ke tbl user filter apa sudah ada default
            $check = $this->_module->check_default_user_filter($username,$id_dept,$inisial_class,$use_default)->row_array();
            // cek ke tbl user filter apa nama filter sudah pernah diinput
            $check2 = $this->_module->check_nama_filter_user($nama_filter,$username,$id_dept,$inisial_class)->row_array();
            if($check['use_default'] == 'true'){
                $callback = array('status'=>'failed', 'message'=>'Maaf, Favorite default sudah dipakai '.$check['nama_filter']);
            }elseif(!empty(trim($check2['nama_filter']))){
                $callback = array('status'=>'failed', 'message'=>'Maaf, Nama Filter Sudah Pernah Diinput');
            }else{
                $this->_module->save_user_filter($username,$id_dept,$inisial_class,trim($nama_filter),$dataFilter,$dataGrouping,$use_default);
                $callback = array('status'=>'success', 'message'=>'Data Berhasil Disimpan !');
            }
        }

        echo json_encode($callback);
    }


    public function hapus_user_filter_sm(){
        
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


    public function loadData_sm($record=0)
    {
      
        $recordPerPage = 30;
        if($record != 0){
           $record = ($record-1) * $recordPerPage;
        }

        $data_filter  = $this->input->post('data_filter'); 
        $data_filter_table= $this->input->post('data_filter_table'); //data filter dari table filer advanced
        $data_grouping = $this->input->post('data_grouping');
        $type_filter   = $this->input->post('type_filter');
        $id_dept       = $this->input->post('id_dept');
        $kolom_order   = $this->input->post('nama_kolom');
        $order         = $this->input->post('order');

        $no           = 1;
        $whereAll     = "";
        $dataRecord   = [];
        $where        = "";
        $dataArr      = [];

        if(!empty($kolom_order)){
            $nama_field = $this->declaration_name_field($kolom_order,$id_dept);
            $kolom_order = "ORDER BY ".$nama_field;
        }else{
            $kolom_order = "ORDER BY sm.create_date";
            $order       = "desc";
        }

      
        // create where berdasarkan data post
        $result = $this->create_where_sm($data_filter,$data_filter_table,$data_grouping,$id_dept);

        $where  = $result[0];
        $dataArr= $result[1];
        $tmp_arr_group = [];
        $group_ke  = '';
       

        // data_grouping / array group
        if(!empty($data_grouping)){

            if(count($data_grouping) == 1){
                $grouping1  = $this->grouping_sm_1_params($data_grouping,$where,$id_dept,$kolom_order,$order);
                $dataRecord = $grouping1[0];
                $tmp_arr_group = $grouping1[1];
                $jml_group     = $grouping1[2];
                $group_ke  = 1;

            }elseif (count($data_grouping) == 2) {
                $grouping2  = $this->grouping_sm_1_params($data_grouping,$where,$id_dept,$kolom_order,$order);
                $dataRecord = $grouping2;
                $dataRecord = $grouping2[0];
                $tmp_arr_group = $grouping2[1];
                $jml_group     = $grouping2[2];
                $group_ke  = 1;
            }
       
            $name_total = "Total Group : ";
            //$allcount   = count($data_grouping);
            $allcount   = $jml_group;
            $pagination = '';
            $group     = true;

        }else{

            if(!empty($where)){
                $where ="where ".$where;
                $whereAll = $where;
            }

            $items = $this->m_stockMoves->get_list_stock_moves_by($where,$record,$recordPerPage,$kolom_order,$order);
            foreach ($items as $row) {
                $dataRecord[]  = array( 'no' =>$no,
                                                    'tgl_sm' => $row->tgl_sm, 
                                                    'move_id' => $row->move_id,
                                                    'origin'  => $row->origin,
                                                    'lokasi_dari' => $row->lokasi_dari,
                                                    'lokasi_tujuan'=> $row->lokasi_tujuan,
                                                    'picking' => $row->kode,
                                                    'tanggal_transaksi' => $row->tanggal_transaksi, 
                                                    'kode_produk' => $row->kode_produk,
                                                    'nama_produk' => $row->nama_produk,
                                                    'lot'         => $row->lot,
                                                    'qty'         => $row->qty,
                                                    'uom'         => $row->uom,
                                                    'qty2'        => $row->qty2,
                                                    'uom2'        => $row->uom2,
                                                    'status'      => $row->status);   

                $no++;
            }

            $allcount   = $this->m_stockMoves->getRecordCount_sm($whereAll);
            $name_total = "Total Data : ";

            $config['base_url']         = base_url().'warehouse/stockmoves/loadData_sm';
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

        $callback  = array('record' => $dataRecord, 'dataArr' => $dataArr, 'pagination'=>$pagination, 'total_record'=>$total_record, 'tmp_arr_group'=>$tmp_arr_group, 'group_ke' => $group_ke);

        echo json_encode($callback);

    }


    function declaration_name_field($nama_field,$id_dept){
          
        $where = '';

        // jika deklarasi join nya smp
        if($nama_field == 'kode_produk' OR $nama_field == 'nama_produk'){
            $where .= 'smp.'.$nama_field.' ';
            //$whereAll .= 'smp.'.$nama_field. ';
        }            

        // jika deklarasi join nya smi.
        if($nama_field == 'lot' or $nama_field == 'tanggal_transaksi' OR $nama_field == 'status' OR $nama_field =='qty' OR $nama_field =='uom' OR $nama_field =='qty2' OR $nama_field =='uom2' ){
            $where .= 'smi.'.$nama_field.' ';
        }

        // jika deklarasi join nya sm
        if($nama_field == 'move_id'  OR $nama_field == 'lokasi_dari' OR $nama_field == 'lokasi_tujuan' OR $nama_field == 'origin' ){
            $where .= 'sm.'.$nama_field.' ';
        } 
 
        if($nama_field == 'picking'){
            $where .=   'picking.kode ';
        }

        return $where;
    }

    function create_where_sm($data_filter,$data_filter_table,$data_grouping,$id_dept){

        $data_filter  = $data_filter; 
        $data_filter_table= $data_filter_table; //data filter dari table filer advanced
        $data_grouping = $data_grouping;

        //$no           = 1;
        $dataRecord   = [];
        $where        = "";
        //$whereAll     = "";
        $dataArr      = [];
        $where_table  = "";
        $where_df     = "";

        //data_filter_table
        if(!empty($data_filter_table)){

            $whereAll = "";  
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
                if($row['operator'] == 'LIKE'){
                    $isi = "LIKE '%".addslashes($row['isi'])."%' ";
                    $operator = 'LIKE';
                  
                }else{
                    $isi = $row['operator']." '".addslashes($row['isi'])."' ";
                    $operator = $row['operator'];                  
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
                # code... 
              
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

                        if($operator == 'LIKE'){
                            $isi_ = "LIKE '%".addslashes($isi)."%' ";
                            // $operator = 'LIKE';
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
                    }else{                       
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


    function grouping_sm_1_params($arr_grouping,$where,$id_dept,$kolom_order,$order){

        $ro    = 1;
        $row   = '';
        $jml_group= count($arr_grouping);
        $by       = $arr_grouping[0]['nama_field'];
        $groupBy  = $this->declaration_name_field($by,$id_dept);

        if(!empty($where)){
            $where1 = "where ".$where;
        }else{
            $where1 = $where;
        }

        $tmp_arr_group = [];
        $jml_group     = 0;

        $group1   = $this->m_stockMoves->get_list_stock_moves_grouping($groupBy,$where1);
        foreach ($group1 as $gp1) {
             
            $group = 'group-of-rows-'.$ro;
            $row  .= "<tbody id='".$group."'>";
            $row  .= "<tr >";
            $row .= "<td class='show collapsed group1' href='#' style='cursor:pointer;'  data-content='edit' data-isi='".$gp1->nama_field."' data-group='".$by."' data-tbody='".$group."' data-root='".$group."' node-root='Yes' group-ke='1'><i class='glyphicon glyphicon-plus' ></td>";
            $row .= "<td colspan='10'>".$gp1->grouping."</td>";
            $row .= "<td align='right'>".$gp1->tqty."</td>";
            $row .= "<td colspan='2' class='list_pagination'></td>";
            $row .= "<td colspan='2' ></td>";
            $row .= "</tr>";
            $ro++;
            $row .= "</tbody>";
            $jml_group++;
            $tmp_arr_group[] = array('tbody_id' => $group, 'by'=> $by,'value' => $gp1->nama_field);
            /*
            $row .= "<tbody id='".$group."' class='collapse child'>";

            if(!empty($where)){
                $where2_add = "AND ".$where;
            }else{
                $where2_add = $where;
            }

            $where2 = "WHERE ".$groupBy." = '".$gp1->nama_field."' ".$where2_add." ";
            $group2 = $this->m_stockMoves->get_list_stock_moves_by_noLimit($where2,$kolom_order,$order);
            $no = 1;
                foreach ($group2 as $gp2) {
                    $row .=  "<tr style='background-color: #f2f2f2;'>";
                    $row .= "<td>".$no++."</td>";
                    $row .= "<td>".$gp2->tgl_sm."</td>";
                    $row .= "<td>".$gp2->move_id."</td>";
                    $row .= "<td>".$gp2->origin."</td>";
                    $row .= "<td>".$gp2->lokasi_dari."</td>";
                    $row .= "<td>".$gp2->lokasi_tujuan."</td>";
                    $row .= "<td>".$gp2->kode."</td>";
                    $row .= "<td>".$gp2->tanggal_transaksi."</td>";
                    $row .= "<td>".$gp2->kode_produk."</td>";
                    $row .= "<td>".$gp2->nama_produk."</td>";
                    $row .= "<td>".$gp2->lot."</td>";
                    $row .= "<td>".$gp2->qty."</td>";
                    $row .= "<td>".$gp2->uom."</td>";
                    $row .= "<td>".$gp2->qty2."</td>";
                    $row .= "<td>".$gp2->uom."</td>";
                    $row .= "<td>".$gp2->status."</td>";
                    $row .=  "</tr>";
                
                }

            $row .= "</tbody>";

           */ 

        }
        return array($row,$tmp_arr_group,$jml_group);
    }

    function grouping_sm_2_params($arr_grouping,$where,$id_dept,$kolom_order,$order){

        $ro    = 1;
        $row   = '';

        $jml_group= count($arr_grouping);
        $by       = $arr_grouping[0]['nama_field'];
        $groupBy  = $this->declaration_name_field($by,$id_dept);
        $by2      = $arr_grouping[1]['nama_field'];
        $groupBy2  = $this->declaration_name_field($by2,$id_dept);

        if(!empty($where)){
            $where1 = "where ".$where;
        }else{
            $where1 = $where;
        }

        $group1   = $this->m_stockMoves->get_list_stock_moves_grouping($groupBy,$where1);
        foreach ($group1 as $gp1) {

            $group = 'group-of-rows-'.$ro;

            $row .="<tbody>";
            $row .= "<tr class='clickable' data-toggle='collapse' data-target='#".$group."' aria-expanded='false' aria-controls='".$group."' style='cursor:pointer;'>";
            $row .= "<td><i class='glyphicon glyphicon-plus'></td>";
            $row .= "<td colspan='10'>".$gp1->grouping."</td>";
            $row .= "<td>".$gp1->tqty."</td>";
            $row .= "<td></td>";
            $row .= "<td></td>";
            $row .= "<td></td>";
            $row .= "<td></td>";
            $row .= "</tr>";
            $row .= "</tbody>";
            $ro++;


            $row .= "<tbody id='".$group."' class='collapse child'>";
            
            if(empty($where)){
                $where2 = "WHERE ".$groupBy." = '".$gp1->nama_field."' ";
            }else{
                $where2 = $where1." AND ".$groupBy." = '".$gp1->nama_field."' ";
            }

            $ro2 = 1;
            $group2   = $this->m_stockMoves->get_list_stock_moves_grouping($groupBy2,$where2);
            foreach ($group2 as $gp2) {

                $groupOf = 'group-of-rows-'.$ro.'-'.$ro2;
                $row .= "<tr class='clickable' data-toggle='collapse' data-target='.".$groupOf."' aria-expanded='false' aria-controls='".$groupOf."' style='cursor:pointer;'>";
                $row .= "<td></td>";
                $row .= "<td><i class='glyphicon glyphicon-plus'></td>";
                $row .= "<td colspan='9'>".$gp2->grouping."</td>";
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

                $where3 = "WHERE ".$groupBy." = '".$gp1->nama_field."' AND ".$groupBy2." = '".$gp2->nama_field."'  ".$where3_add." ";
                $group3  = $this->m_stockMoves->get_list_stock_moves_by_noLimit($where3,$kolom_order,$order);
                $no = 1;
                    foreach ($group3 as $gp3) {
                        $row .=  "<tr class='".$groupOf." collapse child'  style='background-color: #f2f2f2;' >";
                        //$row .= "<td></td>";
                        $row .= "<td>".$no++."</td>";
                        $row .= "<td>".$gp3->tgl_sm."</td>";
                        $row .= "<td>".$gp3->move_id."</td>";
                        $row .= "<td>".$gp3->origin."</td>";
                        $row .= "<td>".$gp3->lokasi_dari."</td>";
                        $row .= "<td>".$gp3->lokasi_tujuan."</td>";
                        $row .= "<td>".$gp3->kode."</td>";
                        $row .= "<td>".$gp3->tanggal_transaksi."</td>";
                        $row .= "<td>".$gp3->kode_produk."</td>";
                        $row .= "<td>".$gp3->nama_produk."</td>";
                        $row .= "<td>".$gp3->lot."</td>";
                        $row .= "<td>".$gp3->qty."</td>";
                        $row .= "<td>".$gp3->uom."</td>";
                        $row .= "<td>".$gp3->qty2."</td>";
                        $row .= "<td>".$gp3->uom."</td>";
                        $row .= "<td>".$gp3->status."</td>";
                        $row .=  "</tr>";
                    }
                //break;
            }
                
            $row .= "</tbody>";
        }

        return $row;
    }



    function loadChild()
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
        $result     = $this->create_where_sm($data_filter,'',$data_grouping,$id_dept);
        $filter     = $result[0];
        $where_post = '';

        if(!empty($filter)){
            $where_post .= $filter.' AND '; 
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
                    $by = $this->declaration_name_field($items['by'],'');
                    $isi_arr = $by." = '".$items['value']."' ";
                    break;
                }
            }
        }

        if(!empty($isi_arr)){
            $where_post .= $isi_arr.' AND ';
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

        $where     .= "WHERE ".$where_post.' '.$group_by." = '".$kode."' ";
        $whereCount =  $where;
        $allcount = 0;
        if($group_ke == $jml_group){
            $where .= "ORDER BY sm.create_date desc,  LENGTH(sm.move_id) desc ";
            $where .= "LIMIT ".$record.",".$recordPerPage;
            $list_items  = $this->m_stockMoves->get_list_stock_moves_by_Limit($where);
            $allcount = $this->m_stockMoves->getRecordCount_sm($whereCount);// get total semua record berdasarkan WHere

        }else{

            $row   = '';
            $no    = 1;
            $group_ke_next = 0;
            $group    = $tbody_id;
            $by2      = $data_grouping[$group_ke]['nama_field'];
            $groupBy2 = $this->declaration_name_field($by2,'');
            $group2   = $this->m_stockMoves->get_list_stock_moves_grouping($groupBy2,$where);
            $group_ke_next = $group_ke + 1;
        
            foreach ($group2 as $gp2) {
                # code...
                $groupOf = $group;
                $id = $group.'-'.$no;
                $row .= "<tbody  data-root='".$groupOf."' data-parent='".$groupOf."' id='".$id."'>";
                $row .= "<tr>";
                $row .= "<td></td>";
                $row .= "<td class='show collapsed group1'  href='#' data-content='edit' data-isi='".$gp2->nama_field."' data-group='".$by2."' data-tbody='".$id."' group-ke='".$group_ke_next."'' data-root='".$groupOf."' node-root='No' style='cursor:pointer;'><i class='glyphicon glyphicon-plus'></td>";
                $row .= "<td colspan='9'>".$gp2->grouping."</td>";
                $row .= "<td align='right'>".$gp2->tqty."</td>";
                $row .= "<td colspan='2' class='list_pagination'></td>";
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
                          'limit'           => $recordPerPage,
                          'tes'             => $where
                        );

        echo json_encode($callback);
    }

   
    function export_excel()
    {
        
        $data_filter  = $this->input->post('data_filter'); 
        $kolom_order = "ORDER BY sm.create_date";
        $order       = "desc";

        // create where berdasarkan data post
        $result = $this->create_where_sm($data_filter,'','','SM');
        $where  = $result[0];
        $dataArr= $result[1];

        if(!empty($where)){
            $where    = "where ".$where;
            $whereAll = $where;
        }

    
        $this->load->library('excel');
        ob_start();
        
        $object = new PHPExcel();
        $object->setActiveSheetIndex(0);

        // SET JUDUL
 		$object->getActiveSheet()->SetCellValue('A1', 'Stock Moves');
 		$object->getActiveSheet()->getStyle('A1')->getAlignment()->setIndent(1);
		$object->getActiveSheet()->mergeCells('A1:O1');

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
		$table_head_columns  = array('No', 'Tgl Stock move','Stock Move', 'Origin', 'Lokasi dari', 'Lokasi Tujuan','Picking','Tgl Transaksi','Kode Produk','Nama Produk','Lot','Uom','Qty2','Uom2','Status');

		$column = 0;
		foreach ($table_head_columns as $judul) {
			# code...
			$object->getActiveSheet()->setCellValueByColumnAndRow($column, 4, $judul);  
			$column++;
		}

        // set width and border
		$index_header = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O');
		$loop = 0;
		foreach ($index_header as $val) {
			$object->getActiveSheet()->getStyle($val.'4')->applyFromArray($styleArray);
		}

        //body
		$num      = 1;
		$rowCount = 5;
		$list  	  = $this->m_stockMoves->get_list_stock_moves_by_noLimit($where,$kolom_order,$order);
		foreach ($list as $val) {
			# code...
			$object->getActiveSheet()->SetCellValue('A'.$rowCount, ($num++));
			$object->getActiveSheet()->SetCellValue('B'.$rowCount, $val->tgl_sm);
			$object->getActiveSheet()->SetCellValue('C'.$rowCount, $val->move_id);
			$object->getActiveSheet()->SetCellValue('D'.$rowCount, $val->origin);
			$object->getActiveSheet()->SetCellValue('E'.$rowCount, $val->lokasi_dari);
			$object->getActiveSheet()->SetCellValue('F'.$rowCount, ($val->lokasi_tujuan));
			$object->getActiveSheet()->SetCellValue('G'.$rowCount, $val->kode);
			$object->getActiveSheet()->SetCellValue('H'.$rowCount, $val->tanggal_transaksi);
			$object->getActiveSheet()->SetCellValue('I'.$rowCount, $val->kode_produk);
			$object->getActiveSheet()->SetCellValue('J'.$rowCount, $val->lot);
			$object->getActiveSheet()->SetCellValue('K'.$rowCount, $val->qty);
			$object->getActiveSheet()->SetCellValue('L'.$rowCount, $val->uom);
			$object->getActiveSheet()->SetCellValue('M'.$rowCount, $val->qty2);
			$object->getActiveSheet()->SetCellValue('N'.$rowCount, $val->uom2);
			$object->getActiveSheet()->SetCellValue('O'.$rowCount, $val->status);

            //set border true
			$object->getActiveSheet()->getStyle('A'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('B'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('C'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('D'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('E'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('F'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('G'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('H'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('I'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('J'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('K'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('L'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('M'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('N'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('O'.$rowCount)->applyFromArray($styleArray);


			$rowCount++;
		}


        $object = PHPExcel_IOFactory::createWriter($object, 'Excel2007');  
		$object->save('php://output');

		$xlsData = ob_get_contents();
		ob_end_clean();

		$response =  array(
				'op'        => 'ok',
				'file'      => "data:application/vnd.ms-excel;base64,".base64_encode($xlsData),
				'filename'  => "Stock Moves.xlsx"
		);

		die(json_encode($response));

    }



}