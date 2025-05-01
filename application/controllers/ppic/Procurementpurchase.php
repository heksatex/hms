<?php defined('BASEPATH') OR EXIT ('No Direct Script Acces Allowed');
/**
 * 
 */
class Procurementpurchase extends MY_Controller
{
	public function __construct()
	{
		parent:: __construct();
		$this->is_loggedin();//cek apakah user sudah login
        $this->load->model('m_procurementPurchase');
		$this->load->model('_module');
		$this->load->model("m_coa");
        $this->load->library("token");

	}

	public function index()
	{	
        $data['id_dept']='PP';
		$this->load->view('ppic/v_procurement_purchase', $data);
	}

	function get_data()
    {	
        $sub_menu  = $this->uri->segment(2);
    	$kode = $this->_module->get_kode_sub_menu($sub_menu)->row_array();
        $username  = addslashes($this->session->userdata('username'));
        $level = $this->session->userdata('nama')["level"];
        if($level === "Entry Data") {
            $masking = $this->m_coa->setTables("user_masking_procurement_purchase")->setSelects(["GROUP_CONCAT(departemen) as departemen"])
                    ->setOrder(["departemen"])->setWheres(["username" => $username])->getDetail();
            $maskings = explode(",", $masking->departemen);
            if(!empty($masking->departemen)){
                $list = $this->m_procurementPurchase->setWhereRaw("warehouse in ('". implode("','", $maskings)."')")->get_datatables($kode['kode']);
                $recordsTotal = $this->m_procurementPurchase->setWhereRaw("warehouse in ('". implode("','", $maskings)."')")->count_all($kode['kode']);
                $recordFilter = $this->m_procurementPurchase->setWhereRaw("warehouse in ('". implode("','", $maskings)."')")->count_filtered($kode['kode']);
            }else{
                $list = $this->m_procurementPurchase->get_datatables($kode['kode']);
                $recordsTotal = $this->m_procurementPurchase->count_all($kode['kode']);
                $recordFilter = $this->m_procurementPurchase->count_filtered($kode['kode']);
            }
            
        }else {
            $list = $this->m_procurementPurchase->get_datatables($kode['kode']);
            $recordsTotal = $this->m_procurementPurchase->count_all($kode['kode']);
            $recordFilter = $this->m_procurementPurchase->count_filtered($kode['kode']);
        }
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $field) {
            $kode_encrypt = encrypt_url($field->kode_pp);
            $no++;
            $row = array();
            if ($field->show_sales_order == 'yes') {
                $capt_sc = 'Yes';
            } else if ($field->show_sales_order == 'no') {
                $capt_sc = 'No';
            } else {
                $capt_sc = '';
            }
            $row[] = $no;
            $row[] = '<a href="'.base_url('ppic/procurementpurchase/edit/'.$kode_encrypt).'">'.$field->kode_pp.'</a>';
            $row[] = $field->create_date;
            $row[] = $field->type;
            $row[] = $capt_sc;
            $row[] = $field->schedule_date;
            $row[] = $field->sales_order;
            $row[] = $field->priority;
            $row[] = $field->nama_dept;
            $row[] = $field->notes;
            $row[] = $field->nama_status;
            $data[] = $row;
        }
 
        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $recordsTotal,
            "recordsFiltered" => $recordFilter,
            "data" => $data,
        );
        //output dalam format JSON
        echo json_encode($output);
    }

    public function add()
    {
        $data['id_dept']  ='PP';
        $level = $this->session->userdata('nama')["level"];
        if($level === "Entry Data") {
            $username  = addslashes($this->session->userdata('username'));
            $masking = $this->m_coa->setTables("user_masking_procurement_purchase")->setSelects(["GROUP_CONCAT(departemen) as departemen"])
            ->setOrder(["departemen"])->setWheres(["username" => $username])->getDetail();
            $maskings = explode(",", $masking->departemen);
            $data['warehouse'] = $this->m_procurementPurchase->setWhereRaw("kode in ('". implode("','", $maskings)."')")->get_list_departement_masking();
        } else {
            $data['warehouse'] = $this->_module->get_list_departement();
        }

        $kode_pp         = $this->input->get('kode_pp');
        $duplicate        = $this->input->get('duplicate');
        if($duplicate == 'true'){
            $procurementpurchase = $this->m_procurementPurchase->get_data_by_code($kode_pp);
            $data["procurementpurchase"] = $procurementpurchase;
            $data['details']    = $this->m_procurementPurchase->get_data_detail_by_code($kode_pp);
            $data['warehouse']  = $this->_module->get_list_departement();
            $data['uom']        = $this->_module->get_list_uom();
            $data['kode_pp']    = $kode_pp;

            if(empty($procurementpurchase)){
                show_404();
            }else{
                $data['row_order'] = 1;
                return $this->load->view('ppic/v_procurement_purchase_duplicate', $data);
            }
        }else{
            return $this->load->view('ppic/v_procurement_purchase_add', $data);
        }
    }

    public function simpan()
    {

        try {
            //code...
            if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
                // session habis
                $callback = array('message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
            }else{
                
                $sub_menu  = $this->uri->segment(2);
                $username  = addslashes($this->session->userdata('username')); 
    
    
                $kode_pp     = addslashes($this->input->post('kode_pp'));
                $kode_prod   = addslashes($this->input->post('kode_prod'));
                $tgl         = $this->input->post('tgl');
                $note        = addslashes($this->input->post('note'));
                $sales_order = addslashes($this->input->post('sales_order'));
                $priority    = addslashes($this->input->post('priority'));
                $warehouse   = addslashes($this->input->post('warehouse')); 
                $show_sc_arr = $this->input->post('show_sc'); // yes or No    
                $type_arr = $this->input->post('type');   // Makte to Order (mts), Pengiriman
    
                $type            = '';
                $show_sc         = '';
                if(!empty($show_sc_arr)){
                    foreach($show_sc_arr as $val2){
                    $show_sc = $val2;
                    break;
                    }
                }

                if (!empty($type_arr)) {
                    foreach ($type_arr as $val) {
                        $type = $val;
                        break;
                    }
                }

                // start transaction
                $this->_module->startTransaction();
    
                // $where_status       = "AND status IN ('generated')";      
                // $where_status2      = "AND status IN ('cancel')";      
                // $cek_details_status2 = $this->m_procurementPurchase->cek_status_procurement_purchase_items($kode_pp,$where_status)->num_rows();
                // $cek_details_status3 = $this->m_procurementPurchase->cek_status_procurement_purchase_items($kode_pp,$where_status2)->num_rows();
    
                $cek_head = $this->m_procurementPurchase->get_data_by_code($kode_pp);
    
                if(!empty($kode_pp) AND $cek_head->status == 'done'){
                    $callback = array('status' => 'failed','message' => 'Maaf, Procurement Purchase Sudah Generated !', 'icon' =>'fa fa-warning', 'type' => 'danger');
                }else if(!empty($kode_pp) AND $cek_head->status == 'cancel'){
                        $callback = array('status' => 'failed','message' => 'Maaf, Procurement Purchase Sudah dibatalkan !', 'icon' =>'fa fa-warning', 'type' => 'danger');
                }else{
    
                    if(empty($tgl)){
                        $callback = array('status' => 'failed', 'field' => 'tgl', 'message' => 'Create Date Harus Diisi !', 'icon' =>'fa fa-warning', 'type' => 'danger'  );    
                    }elseif(empty($note)){
                        $callback = array('status' => 'failed', 'field' => 'note', 'message' => 'Reff Notes Harus Diisi !', 'icon' =>'fa fa-warning', 
                          'type' => 'danger' ); 
                    }elseif(empty($type_arr) AND empty($kode_pp)){
                            $callback = array('status' => 'failed', 'field' => 'mto', 'message' => 'Type Procurement Harus Diisi !', 'icon' => 'fa fa-warning','type' => 'danger');
                    }elseif(empty($sales_order) AND $show_sc == 'yes' AND empty($kode_pp)){
                        $callback = array('status' => 'failed', 'field' => 'sales_order', 'message' => 'Sales Order  Harus Diisi !', 'icon' =>'fa fa-warning', 'type' => 'danger' );    
                    }elseif(empty($kode_prod) AND $show_sc == 'yes' ){
                        $callback = array('status' => 'failed', 'field' => 'kode_prod', 'message' => 'Production Order Harus Diisi !', 'icon' =>'fa fa-warning', 'type' => 'danger' );   
                    }elseif(empty($warehouse)){
                        $callback = array('status' => 'failed', 'field' => 'warehouse', 'message' => 'Departement Tujuan Harus Diisi !', 'icon' =>'fa fa-warning', 
                          'type' => 'danger' );    
                    }elseif(empty($priority)){
                        $callback = array('status' => 'failed', 'field' => 'priority', 'message' => 'Priority Harus Diisi !', 'icon' =>'fa fa-warning', 
                          'type' => 'danger' );    
                    }else{
    
                        if(empty($kode_pp)){//jika kode procurement order kosong, aksinya simpan data
                            $kode_pp_new = $this->token->noUrut('procurement_purchase', '/' . $warehouse. '/' . date('ym'), true)->generate('PP', '%04d')->get();

                            if(!$kode_pp_new) {
                                throw new \Exception('Kode Procurement Purchase tidak terbentuk !', 200);
                            }

                            // $kode['kode_pp'] =  $this->m_procurementPurchase->get_kode_pp();//get no procurement order
                            $kode_encrypt    = encrypt_url($kode_pp_new);
                            $tgl_buat        = date('Y-m-d H:i:s');
                            $this->m_procurementPurchase->simpan($kode_pp_new, $tgl_buat, $tgl, $note, $sales_order, $kode_prod, $priority, $warehouse, 'draft',$show_sc,$type);
    
                            $callback = array('status' => 'success', 'field' => 'kode_pp' , 'message' => 'Data Berhasil Disimpan !', 'isi'=> $kode_pp_new, 'icon' =>'fa fa-check', 'type' => 'success', 'kode_encrypt' => $kode_encrypt);
    
                            if($show_sc == 'yes'){
                                $capt_sc = 'Yes';
                            }else if($show_sc == 'no'){
                                $capt_sc = 'No';
                            }else{
                                $capt_sc = '';
                            }

                            if ($type == 'mto') {
                                $capt_type = 'Make to Order';
                            } else {
                                $capt_type = 'Pengiriman';
                            }
                          
                            $jenis_log = "create";
                            $note_log  =$kode_pp_new." | ".$tgl." | ".$note." | ".$capt_type." | ".$capt_sc." | ".$kode_prod." | ".$sales_order." | ".$priority." | ".$warehouse;
                            $this->_module->gen_history($sub_menu, $kode_pp_new, $jenis_log, $note_log, $username);
                        
                        }else{//jika kode procurement purchase ada, aksinya update data
    
                            //cek status detail apa sudah generate ?
                            $where_status  = "AND status IN ('generated')";
                            $cek_details_status = $this->m_procurementPurchase->cek_status_procurement_purchase_items($kode_pp,$where_status)->num_rows();
                            $detail_generate    = false;
                            $ubah_warehouse     = false;
    
                            if($cek_details_status > 0){
                                $detail_generate = true;
                                //cek warehouse by procutement purchase 
                                $cek_warehouse = $this->m_procurementPurchase->cek_warehouse_procurement_purchase_order_by_kode($kode_pp)->row_array();
                                if($warehouse != $cek_warehouse['warehouse']){
                                    $ubah_warehouse = true;
                                }else{
                                    $ubah_warehouse = false;
                                }
                            }else{
                                $detail_generate = false;
                            }
    
    
                            if($detail_generate == true AND $ubah_warehouse == true){
                                $callback = array('status' => 'failed', 'field' => 'warehouse','message' => 'Maaf, Warehouse tidak Bisa diubah !', 'icon' =>'fa fa-warning','type' => 'danger' );  
    
                            }else{
                                $this->m_procurementPurchase->ubah($kode_pp, $tgl, $note, $priority);
                                $callback = array('status' => 'success', 'message' => 'Data Berhasil Disimpan !','icon' =>'fa fa-check', 'type' => 'success');
                                
                                $jenis_log = "edit";
                                $note_log  = $kode_pp." | ".$tgl." | ".$note." | ".$priority." | ".$warehouse;
                                $this->_module->gen_history($sub_menu, $kode_pp, $jenis_log, $note_log, $username);
    
                            }
                            
                        }
                    }
    
                }
    
            }

            if (!$this->_module->finishTransaction()) {
                throw new \Exception('Simpan Gagal', 200);
            }

            $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($callback));
    
        } catch(Exception $ex){
            // $this->_module->rollbackTransaction();
            // $this->_module->unlock_tabel();
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('status'=>'failed','message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        } finally {
            $this->_module->rollbackTransaction();
            // unlock table
            $this->_module->unlock_tabel();
        }

        
    }


    public function simpan_duplicate()
    {
        try{
            if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
                // session habis
                $callback = array('message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
                throw new \Exception('Waktu Anda Telah Habis', 401);
            }else{

                $sub_menu   = $this->uri->segment(2);
                $username   = $this->session->userdata('username'); 

                $kode_pp     = addslashes($this->input->post('kode_pp'));
                $kode_pp_asal= addslashes($this->input->post('kode_pp_asal'));
                $kode_prod   = addslashes($this->input->post('kode_prod'));
                $tgl         = $this->input->post('tgl');
                $note        = addslashes($this->input->post('note'));
                $sales_order = addslashes($this->input->post('sales_order'));
                $priority    = addslashes($this->input->post('priority'));
                $warehouse   = addslashes($this->input->post('warehouse')); 
                $show_sc_arr = $this->input->post('show_sc'); // yes or No    
                $array_items    = json_decode($this->input->post('arr_items'),true); 
                $type_arr = $this->input->post('type');   // Makte to Order (mts), Pengiriman

                $type            = '';
                $show_sc         = '';
                if(!empty($show_sc_arr)){
                    foreach($show_sc_arr as $val2){
                    $show_sc = $val2;
                    break;
                    }
                }

                if (!empty($type_arr)) {
                    foreach ($type_arr as $val) {
                        $type = $val;
                        break;
                    }
                }

                if(empty($note)){
                    $callback = array('status' => 'failed', 'field' => 'note', 'message' => 'Reff Notes Harus Diisi !', 'icon' =>'fa fa-warning',  'type' => 'danger' );   
                }elseif(empty($show_sc_arr) AND empty($kode_pp)) {
                    $callback = array('status' => 'failed', 'field' => 'sc_true', 'message' => 'Pilih salah satu Sales Order Yes/No !', 'icon' => 'fa fa-warning',
                        'type' => 'danger');
                }elseif(empty($type_arr)){
                    $callback = array('status' => 'failed', 'field' => 'mto', 'message' => 'Type Procurement Harus Diisi !', 'icon' => 'fa fa-warning','type' => 'danger');
                }elseif(empty($sales_order) AND $show_sc == 'yes'){
                    $callback = array('status' => 'failed', 'field' => 'sales_order', 'message' => 'Sales Order  Harus Diisi !', 'icon' =>'fa fa-warning', 'type' => 'danger' );    
                }elseif(empty($kode_prod) AND $show_sc == 'yes' ){
                    $callback = array('status' => 'failed', 'field' => 'kode_prod', 'message' => 'Production Order Harus Diisi !', 'icon' =>'fa fa-warning', 'type' => 'danger' );   
                }elseif(empty($warehouse)){
                    $callback = array('status' => 'failed', 'field' => 'warehouse', 'message' => 'Departement Tujuan Harus Diisi !', 'icon' =>'fa fa-warning', 'type' => 'danger' );    
                }elseif(empty($priority)){
                    $callback = array('status' => 'failed', 'field' => 'priority', 'message' => 'Priority Harus Diisi !', 'icon' =>'fa fa-warning', 'type' => 'danger' );    
                // }elseif(empty($array_items)){
                //     $callback = array('status' => 'failed', 'field' => '', 'message' => 'Items Masih Kosong !', 'icon' =>'fa fa-warning', 'type' => 'danger' );    
                }else{
                    // start transaction
                    $this->_module->startTransaction();

                     $this->_module->lock_tabel('procurement_purchase WRITE,procurement_purchase_items WRITE, token_increment WRITE, log_history WRITE, main_menu_sub WRITE, user WRITE, mst_produk WRITE, nilai_konversi WRITE');

                    $arr_cek_double = [];
                    $items_double         = false;
                    foreach($array_items as $ai){
                        foreach($arr_cek_double as $cek){
                        if($ai['kode_produk'] == $cek){
                            $items_double = true;
                            break;
                        }
                        }
                        array_push($arr_cek_double,$ai['kode_produk']);
                    }


                    $kode_pp_new = $this->token->noUrut('procurement_purchase', '/' . $warehouse. '/' . date('ym'), true)->generate('PP', '%04d')->get();

                    if(!$kode_pp_new) {
                        throw new \Exception('Kode Procurement Purchase tidak terbentuk !', 200);
                    }

                    // $kode['kode_pp'] =  $this->m_procurementPurchase->get_kode_pp();//get no procurement order
                    $kode_encrypt    = encrypt_url($kode_pp_new);
                    $tgl_buat        = date('Y-m-d H:i:s');
                    $this->m_procurementPurchase->simpan($kode_pp_new, $tgl_buat, $tgl, $note, $sales_order, $kode_prod, $priority, $warehouse, 'draft',$show_sc,$type);

                    $tmp_produk = [];
                    $log_produk = "";
                    $row        = 1;
                    foreach($array_items as $it){
                            // cek mst produk 
                            $cek_mst = $this->_module->cek_produk_by_kode_produk($it['kode_produk'])->row_array();
                            if($cek_mst){

                                $tmp_produk[] = array(
                                            "kode_pp"   => $kode_pp_new,
                                            "kode_produk"=> $it['kode_produk'],
                                            "nama_produk"=> $cek_mst['nama_produk'],
                                            "schedule_date" => $it['schedule_date'],
                                            "qty"       => $it['qty'],
                                            "uom"       => $it['uom'],
                                            "qty_beli"  => $it['qty_beli'],
                                            "uom_beli"  => $it['uom_beli'],
                                            "reff_notes"=> $it['reff_note'],
                                            'status'    =>  'draft',
                                            'row_order' => $row
                                );
                                $gt_ku = $this->m_procurementPurchase->get_nilai_konversi_uom_by_id($it['uom_beli']);
                                $dari = $gt_ku->dari ?? '';
                                $nilai = $gt_ku->nilai ?? '';
                                $uom_beli = $dari."[Nilai=".$nilai."]";
                                $log_produk .= "(".$row.") ".$it['kode_produk']." ".$cek_mst['nama_produk']." ".$it['schedule_date']." ".$it['qty_beli']." ".$uom_beli." ".$it['qty']." ".$it["uom"]." ".$it["reff_note"]." <br>";
                                $row++;
                            } else {
                                throw new \Exception('Produk tidak ditemukan !', 200);
                            }
                    }

                    if(!empty($array_items)) {
                        $this->m_procurementPurchase->save_procurement_purchase_items_batch($tmp_produk);
                    }
                   
                    if($show_sc == 'yes'){
                        $capt_sc = 'Yes';
                    }else if($show_sc == 'no'){
                        $capt_sc = 'No';
                    }else{
                        $capt_sc = '';
                    }

                    if ($type == 'mto') {
                        $capt_type = 'Make to Order';
                    } else {
                        $capt_type = 'Pengiriman';
                    }
                      
                    $jenis_log = "create";
                    $note_log  = "duplicate dari kode ".$kode_pp_asal." <br> ".$kode_pp_new." | ".$tgl." | ".$note." | ".$capt_type." | ".$capt_sc." | ".$kode_prod." | ".$sales_order." | ".$priority." | ".$warehouse." <br> ".$log_produk;
                    $this->_module->gen_history($sub_menu, $kode_pp_new, $jenis_log, $note_log, $username);

                    $callback = array('status' => 'success', 'field' => 'kode_pp' , 'message' => 'Data Berhasil Disimpan !', 'isi'=> $kode_pp_new, 'icon' =>'fa fa-check', 'type' => 'success', 'kode_encrypt' => $kode_encrypt);
                }

            }
            if (!$this->_module->finishTransaction()) {
                throw new \Exception('Data gagal disimpan', 500);
            }

            $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($callback));

        } catch(Exception $ex){
            $this->output->set_status_header($ex->getCode() ?? 500)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode(array('status'=>'failed','message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        } finally {
            $this->_module->rollbackTransaction();
            // unlock table
            $this->_module->unlock_tabel();
        }
    }

    public function edit($id = null)
    {   
        if(!isset($id)) show_404();
        $kode_decrypt      = decrypt_url($id);
        $data['id_dept']   ='PP';
        $data["procurementpurchase"] = $this->m_procurementPurchase->get_data_by_code($kode_decrypt);
        $data['details']    = $this->m_procurementPurchase->get_data_detail_by_code($kode_decrypt);
        $data['warehouse']  = $this->_module->get_list_departement();
        $data['uom']        = $this->_module->get_list_uom();
        $where_status       = "AND status IN ('generated','cancel')";
        // $data['cek_status'] = $this->m_procurementPurchase->cek_status_procurement_purchase_items($kode_decrypt,$where_status)->num_rows();

        if(empty($data["procurementpurchase"])){
          show_404();
        }else{
          return $this->load->view('ppic/v_procurement_purchase_edit',$data);
        }

    }


    public function view_detail_items()
    {
        $kode        = $this->input->post('kode_pp');
        $kode_prod   = $this->input->post('kode_prod');
        $sales_order = $this->input->post('sales_order');
        
        // $cfb = $this->m_procurementPurchase->get_cfb_by_kode($kode,$kode_prod,$sales_order)->row_array();
        // $kode_cfb    = $cfb['kode_cfb'];
        if(!empty($sales_order)){
            $origin      = $sales_order.'|'.$kode;
        }else{
            $origin      = $kode;
        }

        $data['cfb']         = $this->m_procurementPurchase->get_list_cfb_by_kode($kode,$kode_prod,$sales_order);
        $data['penerimaan']  = $this->_module->get_detail_items_penerimaan($origin);
        $data['pengiriman']  = $this->_module->get_detail_items_pengiriman($origin);
        return $this->load->view('modal/v_procurement_purchase_details_modal', $data);
    }



    public function get_produk_procurement_purchase_select2()
    {
        $prod     = addslashes($this->input->post('prod'));
        $callback = $this->m_procurementPurchase->get_list_produk_procurement_purchase($prod);
        echo json_encode($callback);
    }


    public function get_prod_by_id()
    {
        $kode_produk = addslashes($this->input->post('kode_produk'));
        $result      = $this->m_procurementPurchase->get_produk_procurement_purchase_byid($kode_produk)->row_array();
        $callback    = array('kode_produk'=>$result['kode_produk'],'nama_produk'=>$result['nama_produk'],'uom'=>$result['uom']);
        echo json_encode($callback);        
    }


    public function simpan_detail_procurement_purchase()
    {

        if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
            // session habis
            $callback = array('message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
        }else{

            $sub_menu  = $this->uri->segment(2);
            $username  = addslashes($this->session->userdata('username')); 

            $kode        = addslashes($this->input->post('kode')); 
            $kode_produk = addslashes($this->input->post('kode_produk')); 
            $produk      = addslashes($this->input->post('produk')); 
            $tgl         = $this->input->post('tgl'); 
            $qty_beli    = $this->input->post('qty_beli'); 
            $uom_beli    = $this->input->post('uom_beli'); 
            $qty         = $this->input->post('qty'); 
            $uom         = addslashes($this->input->post('uom')); 
            $reff        = addslashes($this->input->post('reff')); 
            $row        = ($this->input->post('row_order')); 
            // $data        = explode("^|",$row1);
            // $row         = $data[0];
            //cek apa ada produk yang sudah diinput ?
            $cek_prod = $this->m_procurementPurchase->cek_produk_by_kode($kode,$kode_produk,$row)->row_array();

            $cek_head = $this->m_procurementPurchase->get_data_by_code($kode);

            if($cek_head->status == 'done'){
                $callback = array('status' => 'failed','message' => 'Maaf, Procurement Purchase Sudah Generated !', 'icon' =>'fa fa-warning', 'type' => 'danger');
            }else if($cek_head->status == 'cancel'){
                $callback = array('status' => 'failed','message' => 'Maaf, Procurement Purchase Sudah dibatalkan !', 'icon' =>'fa fa-warning', 'type' => 'danger');
            }else if(!empty($cek_prod['kode_produk'])){
                $callback = array('status' => 'success','message' => 'Maaf, Produk " '.$produk.' " sudah diinput !', 'icon' =>'fa fa-warning', 'type' => 'danger');
            }else{

                if(!empty($row)){//update details

                    $get  = $this->m_procurementPurchase->get_data_procurement_purchase_item_by_row($kode,$row)->row_array();
                    $kode_produk_ex_row =  $get['kode_produk'];
                    $nama_produk        =  $get['nama_produk'];
                    // $uom                =  $get['uom'];

                    //cek status produk, dan cek apa produk masih ada ?
                    $cek_status = $this->m_procurementPurchase->cek_status_procurement_purchase_items_by_row($kode,$kode_produk_ex_row,$row)->row_array(); 

                    if(empty($cek_status['kode_produk'])){
                        $callback = array('status' => 'failed','message' => 'Maaf, Produk Kosong !', 'icon' =>'fa fa-warning', 'type' => 'danger');

                    }else if($cek_status['status'] != 'draft'){
                        $callback = array('status' => 'failed','message' => 'Maaf, Data tidak bisa Diubah, Status tidak valid !', 'icon' =>'fa fa-warning', 'type' => 'danger');

                    }else{

                        $this->m_procurementPurchase->update_procurement_purchase_items($kode,$kode_produk,$produk,$uom,$tgl,$qty,$reff,$row,$qty_beli,$uom_beli);
                        if(!empty($uom_beli)){
                            $gt_ku = $this->m_procurementPurchase->get_nilai_konversi_uom_by_id($uom_beli);
                            $dari = $gt_ku->dari ?? '';
                            $nilai = $gt_ku->nilai ?? '';
                            $uom_beli = $dari."[Nilai=".$nilai."]";
                        }else {
                            $uom_beli = '';
                        }

                        $jenis_log   = "edit";
                        $note_log    = "Edit data Details | ".$kode." | ".$kode_produk."  ".$produk." | ".$tgl." | ".$qty_beli." ".$uom_beli." | ".$qty." ".$uom." | ".$reff." | ".$row;
                        $this->_module->gen_history($sub_menu, $kode, $jenis_log, $note_log, $username);
                        $callback = array('status' => 'success','message' => 'Data Berhasil Disimpan !', 'icon' =>'fa fa-check', 'type' => 'success');
                    }
                    
                }else{//simpan data baru

                    //cek Status purchase items
                    $where_status       = "AND status NOT IN ('draft')";
                    $cek_details = $this->m_procurementPurchase->cek_status_procurement_purchase_items($kode,$where_status)->num_rows(); 
                    if($cek_details > 0){
                        $callback = array('status' => 'failed','message' => 'Maaf, Data tidak bisa Disimpan, Status Product tidak valid !', 'icon' =>'fa fa-warning', 'type' => 'danger');

                    }else{

                        $ro  = $this->m_procurementPurchase->get_row_order_procurement_purchase_items($kode)->row_array();
                        $row_order = $ro['row_order']+1;
                        $status  = 'draft';
                        $this->m_procurementPurchase->save_procurement_purchase_items($kode,$kode_produk,$produk,$tgl,$qty,$uom,$reff,$status,$row_order,$qty_beli,$uom_beli);
                 
                        
                        $cek_details = $this->m_procurementPurchase->cek_status_procurement_purchase_items($kode,'')->num_rows(); 
                        
                        $where_status       = "AND status IN ('draft')";
                        $cek_details_status = $this->m_procurementPurchase->cek_status_procurement_purchase_items($kode,$where_status)->num_rows();

                        if($cek_details > 0  ){
                              $this->m_procurementPurchase->update_status_procurement_purchase($kode,'draft');
                        }else if($cek_details == 0){
                            $where_status       = "AND status IN ('cancel')";
                            $cek_details_status2 = $this->m_procurementPurchase->cek_status_procurement_purchase_items($kode,$where_status)->num_rows();

                            if($cek_details_status2 == 0){
                                $this->m_procurementPurchase->update_status_procurement_purchase($kode,'done');
                            }else{
                                $this->m_procurementPurchase->update_status_procurement_purchase($kode,'cancel');
                            }   
                        }

                        if(!empty($uom_beli)){
                            $gt_ku = $this->m_procurementPurchase->get_nilai_konversi_uom_by_id($uom_beli);
                            $dari = $gt_ku->dari ?? '';
                            $nilai = $gt_ku->nilai ?? '';
                            $uom_beli = $dari."[Nilai=".$nilai."]";
                        }else {
                            $uom_beli = '';
                        }


                        $jenis_log   = "edit";
                        $note_log    = "Tambah data Details | ".$kode." | ".$kode_produk." ".$produk." | ".$tgl." | ".$qty_beli."  ".$uom_beli." | ".$qty."  ".$uom." | ".$reff." | ".$row_order;
                        $this->_module->gen_history($sub_menu, $kode, $jenis_log, $note_log, $username);
                        
                        $callback = array('status' => 'success','message' => 'Data Berhasil Disimpan !', 'icon' =>'fa fa-check', 'type' => 'success');
                    }


                }

            }

            echo json_encode($callback);
        }
    }


    public function hapus_procurement_purchase_items()
    {

        if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
            // session habis
            $callback = array('message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
        }else{
            $sub_menu  = $this->uri->segment(2);
            $username  = addslashes($this->session->userdata('username')); 

            $kode = addslashes($this->input->post('kode'));
            $row  = $this->input->post('row_order');

            $get  = $this->m_procurementPurchase->get_data_procurement_purchase_item_by_row($kode,$row)->row_array();
            $kode_produk_ex_row =  $get['kode_produk'];
            $nama_produk        =  $get['nama_produk'];
            $qty                =  $get['qty'];
            $uom                =  $get['uom'];
            $schedule_date      =  $get['schedule_date'];
            $row_order          =  $row;
            
            $cek_head = $this->m_procurementPurchase->get_data_by_code($kode);

            $cek_status = $this->m_procurementPurchase->cek_status_procurement_purchase_items_by_row($kode,$kode_produk_ex_row,$row)->row_array(); 

            if($cek_head->status == 'done'){
                $callback = array('status' => 'failed','message' => 'Maaf, Procurement Purchase Sudah Generated !', 'icon' =>'fa fa-warning', 'type' => 'danger');
            }else if($cek_head->status == 'cancel'){
                $callback = array('status' => 'failed','message' => 'Maaf, Procurement Purchase Sudah dibatalkan !', 'icon' =>'fa fa-warning', 'type' => 'danger');

            }else if(empty($kode) && empty($row) ){
                $callback = array('status' => 'success','message' => 'Data Gagal Dihapus !', 'icon' =>'fa fa-warning', 'type' => 'danger');
           
            }else if(empty($cek_status['kode_produk'])){
                $callback = array('status' => 'failed','message' => 'Maaf, Produk Kosong  atau sudah dihapus !', 'icon' =>'fa fa-warning', 'type' => 'danger');

            }else if($cek_status['status'] != 'draft' && $cek_status['status'] != 'cancel'){
                $callback = array('status' => 'failed','message' => 'Maaf, Data tidak bisa Dihapus, Status tidak valid !', 'icon' =>'fa fa-warning', 'type' => 'danger');

            }else{
                $this->m_procurementPurchase->delete_procurement_purchase_items($kode,$row_order);
                $callback = array('status' => 'success','message' => 'Data Berhasil Dihapus !', 'icon' =>'fa fa-check', 'type' => 'success');
                
                $cek_details = $this->m_procurementPurchase->cek_status_procurement_purchase_items($kode,'')->num_rows(); 
                $where_status       = "AND status IN ('draft')";
                $cek_details_status = $this->m_procurementPurchase->cek_status_procurement_purchase_items($kode,$where_status)->num_rows();

                if($cek_details == 0  ){
                      $this->m_procurementPurchase->update_status_procurement_purchase($kode,'draft');
                }else if($cek_details > 0){
                    if($cek_details_status == 0){
                        $this->m_procurementPurchase->update_status_procurement_purchase($kode,'done');
                    }else{
                        $this->m_procurementPurchase->update_status_procurement_purchase($kode,'draft');
                    }   
                }
                
                $jenis_log   = "cancel";
                $note_log    = "Hapus data Details | ".$kode." | ".$kode_produk_ex_row." ".$nama_produk." | ".$schedule_date." | ".$qty."  ".$uom." | ".$row_order;
                $this->_module->gen_history($sub_menu, $kode, $jenis_log, $note_log, $username);
                
            }

            echo json_encode($callback);
        }
    }


    public function generate_procurement_purchase()
    {

        try {
            //code...
            if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
                // session habis
                // $callback = array('message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
                throw new \Exception('Waktu Anda Telah Habis', 401);
            }else{
    
                $sub_menu  = $this->uri->segment(2);
                $username  = addslashes($this->session->userdata('username')); 
                $nu = $this->_module->get_nama_user($username)->row_array();
                $nama_user = addslashes($nu['nama']);
                $kode_m = $this->_module->get_kode_sub_menu($sub_menu)->row_array();
                $mms_kode_cfb =  $kode_m['kode'] ?? '';
                $ip       = $this->input->ip_address();
    
                $kode     =  $this->input->post('kode');     
                // $where_status       = "AND status IN ('generated')";      
                // $where_status2      = "AND status IN ('cancel')";      
                $cek_details_status = $this->m_procurementPurchase->cek_status_procurement_purchase_items($kode,'')->num_rows();
                // $cek_details_status2 = $this->m_procurementPurchase->cek_status_procurement_purchase_items($kode,$where_status)->num_rows();
                // $cek_details_status3 = $this->m_procurementPurchase->cek_status_procurement_purchase_items($kode,$where_status2)->num_rows();
                
                $cek_head = $this->m_procurementPurchase->get_data_by_code($kode);
    
                if($cek_details_status == 0){
                    $callback = array('status' => 'failed','message' => 'Maaf, Items Masih Kosong !', 'icon' =>'fa fa-warning', 'type' => 'danger');
                }else if($cek_head->status == 'done'){
                        $callback = array('status' => 'failed','message' => 'Maaf, Procurement Purchase Sudah Generated !', 'icon' =>'fa fa-warning', 'type' => 'danger');
                }else if($cek_head->status == 'cancel'){
                        $callback = array('status' => 'failed','message' => 'Maaf, Procurement Purchase Sudah dibatalkan !', 'icon' =>'fa fa-warning', 'type' => 'danger');
                }else{  

                    // start transaction
                    $this->_module->startTransaction();
                    
                    $tgl    = date('Y-m-d H:i:s');
                    //get data di table procurement_purchase
                    $head   = $this->m_procurementPurchase->get_data_by_code($kode);
                    //get data di table procurement_purchase_items
                    $items  = $this->m_procurementPurchase->get_data_detail_by_code($kode);
    
                    //lock table
                    $this->_module->lock_tabel('procurement_purchase WRITE, procurement_purchase_items WRITE, cfb WRITE,  cfb_items WRITE, stock_move WRITE, stock_move_produk WRITE, departemen d WRITE, penerimaan_barang WRITE, penerimaan_barang_items WRITE, mrp_route mr WRITE, mrp_route WRITE, pengiriman_barang WRITE, pengiriman_barang_items WRITE, departemen WRITE, log_history WRITE, main_menu_sub WRITE, mst_produk WRITE');
                    
                    $sql_stock_move_batch        = "";
                    $sql_stock_move_produk_batch = "";
                    $sql_log_history_out = "";
                    $sql_out_batch       = "";
                    $sql_out_items_batch = "";
                    $sm_row              = 1; 
                    $source_move         = ""; 
                    $i                   = 1; //set count kode out
                    $arr_kode           = [];    
                    $status_produk_aktif = TRUE; 
                    $produk_tidak_aktif  = "";
                    $insert_log_cfb      = [];
                    $insert_cfb          = [];
                    $insert_cfb_items    = [];
                    $update_kode_cfb     = [];
                    $insert_log       = array();
    
                    $last_move   = $this->_module->get_kode_stock_move();
                    $move_id     = "SM".$last_move; //Set kode stock_move
    
                    //get_kode cfb
                    // $get_cfb  = $this->m_procurementPurchase->get_kode_cfb();
                    // $last_kode_cfb = $get_cfb;
                    // $kode_cfb = "TE".$last_kode_cfb;
    
                    if(!empty($head->sales_order)){
                        $origin   = $head->sales_order.'|'.$kode;
                    }else{
                        $origin   = $kode;
                    }
    
                   
                    $route_prod = $this->_module->get_route_product('procurement_purchase');
                    //get total leadtime by route
                    $total_ld = $this->_module->get_total_leadtime('procurement_purchase');
                    $leadtime = $total_ld;
                    $leadtime_dept =  $leadtime;
                    foreach ($route_prod as $rp) {
    
                        $mthd          = explode('|',$rp->method);
                        $method_dept   = trim($mthd[0]);
                        $method_action = trim($mthd[1]);
    
                        /*----------------------------------
                                Generate Stock Moves
                        ----------------------------------*/               
    
                        $sql_stock_move_batch .= "('".$move_id."','".$tgl."','".$origin."','".$rp->method."','".$rp->lokasi_dari."','".$rp->lokasi_tujuan."','draft','".$sm_row."','".$source_move."'), ";                    
                        $sm_row = $sm_row + 1;
    
                        if($method_action == 'OUT'){//Generate Pengiriman
                          
                            if($i=="1"){
                                $arr_kode[$rp->method]= $this->_module->get_kode_pengiriman($method_dept);
                            }else{
                                $arr_kode[$rp->method]= $arr_kode[$rp->method] + 1;
                            }
                            $dgt=substr("00000" . $arr_kode[$rp->method],-5);            
                            $kode_out = $method_dept."/".$method_action."/".date("y").  date("m").$dgt;
    
                            $tgl_jt  =  date('Y-m-d H:i:s', strtotime(-$leadtime_dept.' days', strtotime($head->schedule_date)));
                            
                            //simpan ke pengiriman barang
                            $sql_out_batch  .= "('".$kode_out."','".$tgl."','".$tgl."','".$tgl_jt."','".addslashes($head->notes)."','draft','".$method_dept."','".$origin."','".$move_id."','".$rp->lokasi_dari."','".$rp->lokasi_tujuan."'), ";
    
                            $out_row = 1;
                            $num     = 1;
                            foreach ($items as $row) {//get data in procurement_purchase_items 
    
                                $origin_prod = $row->kode_produk."_".$out_row;
    
                                $stat_produk = $this->_module->get_status_aktif_by_produk(addslashes($row->kode_produk))->row_array();// status produk aktif/tidak
                                if($stat_produk['status_produk'] != 't'){
                                    $status_produk_aktif = FALSE; 
                                    $produk_tidak_aktif  .= $num.'. '.$row->kode_produk.' '.$row->nama_produk.' <br>';
                                    $num++;
                                }else{
                                    //simpan ke pengiriman barang items
                                    $sql_out_items_batch .= "('".$kode_out."','".addslashes($row->kode_produk)."','".addslashes($row->nama_produk)."','".$row->qty."','".addslashes($row->uom)."','draft','".$out_row."','".$origin_prod."'), ";
                                    
                                    //simpan ke stock move produk 
                                    $sql_stock_move_produk_batch .= "('".$move_id."','".addslashes($row->kode_produk)."','".addslashes($row->nama_produk)."','".$row->qty."','".addslashes($row->uom)."','draft','".$out_row."','".$origin_prod."'), ";
                                    
                                      
                                    
                                    //sql insert tbl cfb head   
                                    // $sql_cfb_head = "INSERT INTO cfb (kode_cfb, create_date, schedule_date, sales_order, kode_prod, kode_pp, priority, warehouse, notes, status) values ('".$kode_cfb."','".$tgl."','".$tgl."','".$head->sales_order."','".$head->kode_prod."','".$kode."','".$head->priority."','".$head->warehouse."','".addslashes($head->notes)."','draft')";
                                    $kode_cfb     = $kode.'_'.$out_row;
                                    $insert_cfb[] = array(
                                                'kode_cfb'      => $kode_cfb,
                                                'create_date'   => $tgl,
                                                'schedule_date' => $row->schedule_date,
                                                'sales_order'   => $head->sales_order,
                                                'kode_prod'     => $head->kode_prod,
                                                'kode_pp'       => $kode,
                                                'priority'      => $head->priority,
                                                'warehouse'     => $head->warehouse,
                                                'notes'         => $head->notes,
                                                'responsible'   => $nama_user,
                                                'status'        => ($head->type == 'pengiriman')? 'done' : 'draft',
                                    );
    
                                    $insert_cfb_items[] = array(    
                                                'kode_cfb'      => $kode_cfb,
                                                'kode_produk'   => $row->kode_produk,
                                                'nama_produk'   => $row->nama_produk,
                                                'schedule_date' => $row->schedule_date,
                                                'qty'           => $row->qty,
                                                'uom'           => $row->uom,
                                                'status'        => ($head->type == 'pengiriman')? 'done' : 'draft',
                                                'reff_notes'    => $row->reff_notes,
                                                'row_order'     => 1
                                    );
    
                                    $source_move = $move_id;
                                    
                                    $out_row = $out_row + 1; 
    
                                    //create log history cfb 
                                    $note_log_cfb = $kode_cfb.' | '.$kode.' | '.$head->warehouse;
                                    $insert_log_cfb[] = array(
                                                'datelog'   => $tgl,
                                                'main_menu_sub_kode'    => $mms_kode_cfb,
                                                'kode'                  => $kode_cfb ?? '',
                                                'jenis_log'             => 'create',
                                                'note'                  => $note_log_cfb,
                                                'nama_user'             => $nama_user ?? '',
                                                'ip_address'            => $ip);
                                    }
    
                                    $update_kode_cfb[] = array('id'=>$row->id, 'kode_cfb'=>$kode_cfb);
    
                                    // $last_kode_cfb = $last_kode_cfb + 1;
                                    // $kode_cfb      = "TE".$last_kode_cfb;
                        
                            }//end foreach items procurement purchase
    
                            //get mms kode berdasarkan dept_id
                            $mms = $this->_module->get_kode_sub_menu_deptid('pengirimanbarang',$method_dept)->row_array();
                            if(!empty($mms['kode'])){
                                $mms_kode = $mms['kode'];
                            }else{
                                $mms_kode = '';
                            }
    
                            //create log history pengiriman_barang
                            $note_log = $kode_out.' | '.$origin;
                            $date_log = date('Y-m-d H:i:s');
                            // $sql_log_history_out .= "('".$date_log."','".$mms_kode."','".$kode_out."','create','".$note_log."','".$nama_user."'), ";

                            $insert_log[] = array(
                                'datelog'   => $date_log,
                                'main_menu_sub_kode'    => $mms_kode,
                                'kode'                  => $kode_out ?? '',
                                'jenis_log'             => 'create',
                                'note'                  => $note_log,
                                'nama_user'             => $nama_user ?? '',
                                'ip_address'            => $ip);
                             
                        }//end if out                
    
                    }//end foreach route
    
                    if($status_produk_aktif == FALSE){
                        $callback = array('status' => 'failed','message' => 'Maaf, Status Produk Tidak Aktif ! <br>'.$produk_tidak_aktif, 'icon' =>'fa fa-warning', 'type' => 'danger');
    
                    }else{
    
                        if(!empty($sql_stock_move_batch)){
                            $sql_stock_move_batch = rtrim($sql_stock_move_batch, ', ');
                            $this->_module->create_stock_move_batch($sql_stock_move_batch);
    
                            $sql_stock_move_produk_batch = rtrim($sql_stock_move_produk_batch, ', ');
                            $this->_module->create_stock_move_produk_batch($sql_stock_move_produk_batch);
                        }
    
                        if(!empty($sql_out_batch)){
                            $sql_out_batch = rtrim($sql_out_batch, ', ');
                            $this->_module->simpan_pengiriman_batch($sql_out_batch);
    
                            $sql_out_items_batch = rtrim($sql_out_items_batch, ', ');
                            $this->_module->simpan_pengiriman_items_batch($sql_out_items_batch);
    
                            // $sql_log_history_out = rtrim($sql_log_history_out, ', ');
                            // $this->_module->simpan_log_history_batch($sql_log_history_out);
                        }
                        
    
                        /*----  Generate IN  -------------*/
    
                        $sql_in_batch        = "";
                        $sql_in_items_batch  = "";
                        $sql_cfb_items       = "";
                        $sql_stock_move_batch        = "";
                        $sql_stock_move_produk_batch = "";
                        $sql_log_history_in = "";
                        $sql_log_history_cfb="";
                        $sql_in_items_batch_2 = [];
    
                        $last_move   = $this->_module->get_kode_stock_move();
                        $move_id     = "SM".$last_move; //Set kode stock_move           
    
                        $warehouse     = $head->warehouse;
                        $method_action = 'IN';
                        $method        = $warehouse.'|'.$method_action;
                        $output_location = $this->_module->get_output_location_by_kode('RCV')->row_array();
                        $lokasi_dari   = $output_location['output_location'];
                        $loc      = $this->_module->get_nama_dept_by_kode($warehouse)->row_array();
                        $lokasi_tujuan = $loc['stock_location'];
                        
                        //$lokasi_tujuan = $warehouse.'/Stock';
                        $method_dept   = $warehouse;            
    
                        $sql_stock_move_batch .= "('".$move_id."','".$tgl."','".$origin."','".$method."','".$lokasi_dari."','".$lokasi_tujuan."','draft','".$sm_row."','".$source_move."'), ";                  
                            
                        // Generate penerimaan barang
                        $kode_= $this->_module->get_kode_penerimaan($method_dept);
                        $get_kode_in= $kode_;
    
                        $dgt     =substr("00000" . $get_kode_in,-5);            
                        $kode_in = $method_dept."/".$method_action."/".date("y").  date("m").$dgt;
    
                        $ld_dept = $this->_module->get_leadtime_by_dept($method_dept)->row_array();
                        $leadtime_dept = $ld_dept['manf_leadtime'];
    
                        $tgl_jt  =  date('Y-m-d H:i:s', strtotime(-$leadtime_dept.' days', strtotime($head->schedule_date)));
    
                        $reff_picking_in = $kode_out."|".$kode_in;
                        $sql_in_batch   .= "('".$kode_in."','".$tgl."','".$tgl."','".$tgl."','".addslashes($head->notes)."','draft','".$method_dept."','".$origin."','".$move_id."','".$reff_picking_in."','".$lokasi_dari."','".$lokasi_tujuan."'), "; 
                        $in_row=1;
    
                        //get mms kode berdasarkan dept_id
                        $mms = $this->_module->get_kode_sub_menu_deptid('penerimaanbarang',$method_dept)->row_array();
                        if(!empty($mms['kode'])){
                            $mms_kode = $mms['kode'];
                        }else{
                            $mms_kode = '';
                        }
    
                        //create log history penerimaan_barang
                        $note_log = $kode_in.'|'.$origin;
                        $date_log = date('Y-m-d H:i:s');
                        // $sql_log_history_in .= "('".$date_log."','".$mms_kode."','".$kode_in."','create','".$note_log."','".$nama_user."'), ";
                        $insert_log[] = array(
                            'datelog'   => $date_log,
                            'main_menu_sub_kode'    => $mms_kode,
                            'kode'                  => $kode_in ?? '',
                            'jenis_log'             => 'create',
                            'note'                  => $note_log,
                            'nama_user'             => $nama_user ?? '',
                            'ip_address'            => $ip);
                        foreach ($items as $row) {
    
                            $origin_prod = $row->kode_produk."_".$in_row;
    
                            //simpan ke penermaan_barang_items
                            $sql_in_items_batch   .= "('".$kode_in."','".addslashes($row->kode_produk)."','".addslashes($row->nama_produk)."','".$row->qty."','".addslashes($row->uom)."','draft','".$in_row."'), ";
    
                            $sql_in_items_batch_2[] = array(
                                                        "kode"  => $kode_in,
                                                        "kode_produk"   => $row->kode_produk,
                                                        "nama_produk"   => $row->nama_produk,
                                                        "qty"           => $row->qty,
                                                        "uom"           => $row->uom,
                                                        "status_barang" => 'draft',
                                                        "origin_prod"   => $origin_prod,
                                                        "row_order"     => $in_row
                            );
                            //simpan ke stock move produk 
                            $sql_stock_move_produk_batch .= "('".$move_id."','".addslashes($row->kode_produk)."','".addslashes($row->nama_produk)."','".$row->qty."','".addslashes($row->uom)."','draft','".$in_row."',''), ";
                            //sql insert tbl cfb_items
                            // $sql_cfb_items .= "('".$kode_cfb."','".addslashes($row->kode_produk)."','".addslashes($row->nama_produk)."','".$row->schedule_date."','".$row->qty."','".addslashes($row->uom)."','draft','".addslashes($row->reff_notes)."','".$in_row."'), ";
                            $in_row = $in_row + 1; 
                            
                        }
                    
                        // //sql insert tbl cfb head   
                        // $sql_cfb_head = "INSERT INTO cfb (kode_cfb, create_date, schedule_date, sales_order, kode_prod, kode_pp, priority, warehouse, notes, status) values ('".$kode_cfb."','".$tgl."','".$tgl."','".$head->sales_order."','".$head->kode_prod."','".$kode."','".$head->priority."','".$head->warehouse."','".addslashes($head->notes)."','draft')";
    
                        // //create log history cfb 
                        // $note_log = $kode_cfb.' | '.$kode.' | '.$head->warehouse;
                        // $date_log = date('Y-m-d H:i:s');
                        // $sql_log_history_cfb .= "('".$date_log."','','".$kode_cfb."','create','".$note_log."','".$nama_user."'), ";        
                        
                        if(!empty($insert_cfb)){
                            $this->m_procurementPurchase->save_cfb_batch($insert_cfb);
    
                            // $sql_cfb_items = rtrim($sql_cfb_items, ', ');
                            $this->m_procurementPurchase->save_cfb_items_batch($insert_cfb_items); 
    
                            // $sql_log_history_cfb = rtrim($sql_log_history_cfb, ', ');
                            // $this->_module->simpan_log_history_batch($sql_log_history_cfb);    
                            $this->_module->simpan_log_history_batch_2($insert_log_cfb);
    
                            $this->m_procurementPurchase->update_pp_items($update_kode_cfb);
                            
                        }
    
                        if(!empty($sql_stock_move_batch)){
                            $sql_stock_move_batch = rtrim($sql_stock_move_batch, ', ');
                            $this->_module->create_stock_move_batch($sql_stock_move_batch);
    
                            $sql_stock_move_produk_batch = rtrim($sql_stock_move_produk_batch, ', ');
                            $this->_module->create_stock_move_produk_batch($sql_stock_move_produk_batch);
                        }
    
                        if(!empty($sql_in_batch)){
                            $sql_in_batch = rtrim($sql_in_batch, ', ');
                            $this->_module->simpan_penerimaan_batch($sql_in_batch);
    
                            // $sql_in_items_batch = rtrim($sql_in_items_batch, ', ');
                            // $this->_module->simpan_penerimaan_items_batch($sql_in_items_batch);  
                            $this->_module->simpan_penerimaan_items_batch_2($sql_in_items_batch_2);
                        
                            $sql_update_reff_out_batch  = "UPDATE pengiriman_barang SET reff_picking = '".$reff_picking_in."' WHERE  kode = '".$kode_out."' ";
                            $this->_module->update_reff_batch($sql_update_reff_out_batch);   
    
                            // $sql_log_history_in = rtrim($sql_log_history_in, ', ');
                            // $this->_module->simpan_log_history_batch($sql_log_history_in);        
                        }

                        //create log history
                        if(!empty($insert_log)){
                            $this->_module->simpan_log_history_batch_2($insert_log);
                        }
    
                        //update status procurement_purchase
                        $this->m_procurementPurchase->update_status_procurement_purchase($kode, 'done');
                        //update status procurement_purchase_items
                        $this->m_procurementPurchase->update_status_procurement_purchase_items($kode, 'generated');            
    
                        //unlock table
                        $this->_module->unlock_tabel();
                        
                        $jenis_log   = "generate";
                        $note_log    = "Generated | ".$kode;
                        $this->_module->gen_history($sub_menu, $kode, $jenis_log, $note_log, $username);
                                
                        $callback = array('status' => 'success','message' => 'Generate Data Berhasil !', 'icon' =>'fa fa-check', 'type' => 'success');
                    }
                }
            }
    
            if (!$this->_module->finishTransaction()) {
                throw new \Exception('Gagal Generate Data', 500);
            }

            $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($callback));

        } catch(Exception $ex){
            $this->output->set_status_header($ex->getCode() ?? 500)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode(array('status'=>'failed','message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        } finally {
            $this->_module->rollbackTransaction();
            // unlock table
            $this->_module->unlock_tabel();
        }
       
    }

    public function batal_procurement_purchase()
    {
        try {
            //code...
            if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
                // session habis
                // $callback = array('message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
                throw new \Exception('Waktu Anda Telah Habis', 500);
            }else{

                $sub_menu  = $this->uri->segment(2);
                $username  = addslashes($this->session->userdata('username')); 
                $nu = $this->_module->get_nama_user($username)->row_array();
                $nama_user = addslashes($nu['nama']);
                $insert_log = array();
                $ip         = $this->input->ip_address();

                $kode       = $this->input->post('kode');
                $kode_prod  = $this->input->post('kode_prod');
                $sales_order= $this->input->post('sales_order');

                $where_status       = "AND status NOT IN ('generated')";      
                $where_status2      = "AND status NOT IN ('draft','generated','cfb')";      
                $cek_details_status = $this->m_procurementPurchase->cek_status_procurement_purchase_items($kode,'')->num_rows();

                $cek_details_status2 = $this->m_procurementPurchase->cek_status_procurement_purchase_items($kode,$where_status2)->num_rows();
                // $cek_details_status3 = $this->m_procurementPurchase->cek_status_procurement_purchase_items($kode,$where_status2)->num_rows();

                $cek_head = $this->m_procurementPurchase->get_data_by_code($kode);

                if($cek_details_status == 0 and $cek_head->status == 'generated'){
                    $callback = array('status' => 'failed','message' => 'Maaf, Items Masih Kosong !', 'icon' =>'fa fa-warning', 'type' => 'danger');
                }else if($cek_head->status == 'cancel'){
                    $callback = array('status' => 'failed','message' => 'Maaf, Procurement Purchase Sudah dibatalkan !', 'icon' =>'fa fa-warning', 'type' => 'danger');
                }else if($cek_details_status2 > 0){
                    $callback = array('status' => 'failed','message' => 'Maaf, Status Produk tidak Valid !', 'icon' =>'fa fa-warning', 'type' => 'danger');
                }else if($cek_head->status == 'draft'){
                    //update status procurement_purchase = cancel
                    $this->m_procurementPurchase->update_status_procurement_purchase($kode, 'cancel');
                    //update status procurement_purchase_items = cancel
                    $this->m_procurementPurchase->update_status_procurement_purchase_items($kode, 'cancel'); 

                    $jenis_log   = "cancel";
                    $note_log    = "Batal Procurement Purchase | ".$kode;
                    $this->_module->gen_history($sub_menu, $kode, $jenis_log, addslashes($note_log), $username);

                    //create log history setiap yg batal
                    if(!empty($sql_log_history)){
                        $sql_log_history = rtrim($sql_log_history, ', ');
                        $this->_module->simpan_log_history_batch($sql_log_history);
                    }

                    $callback = array('status' => 'success','message' => 'Procurement Purchase Berhasil dibatalkan ', 'icon' =>'fa fa-check', 'type' => 'success');

                }else{
                    
                    // start transaction
                    $this->_module->startTransaction();

                    //lock table
                    $this->_module->lock_tabel('procurement_purchase WRITE, procurement_purchase_items WRITE, cfb WRITE, cfb_items WRITE, pengiriman_barang WRITE, pengiriman_barang_items WRITE, penerimaan_barang WRITE, penerimaan_barang_items WRITE, stock_move WRITE, stock_move_items WRITE, stock_move_produk WRITE, user WRITE, main_menu_sub WRITE, log_history WRITE');

                    //get kode_cfb
                    $cfb = $this->m_procurementPurchase->get_cfb_by_kode($kode)->result();
                    $sales_order = $cek_head->sales_order ?? '';
                    if(!empty($sales_order)){
                        $origin = $sales_order.'|'.$kode;
                    }else{
                        $origin = $kode;
                    }


                    $update_stock_move = false;
                    $batal_item    = false;
                    $status_cancel = "cancel";
                    //pengiriman_barang
                    $case  = "";
                    $where = "";
                    //penerimaaan_barang
                    $case2  = "";
                    $where2 = "";
                    //stock_move
                    $case3  = "";
                    $where3 = "";
                    $date_log = date('Y-m-d H:i:s');
                    $sql_log_history = "";

                    $status_in_valid = false;
                    $dokumen = '';

                    //get list stock_move by origin
                    $list_sm    = $this->_module->get_list_stock_move_by_origin($origin);
                    foreach ($list_sm as $row) {

                        $batal_item = true;

                        $ex_mt = explode('|',$row->method);
                        $method_dept    = $ex_mt[0];
                        $method_action  = $ex_mt[1]; //ex CON/PROD/OUT/IN
                        $origin  = $row->origin;
                        $move_id = $row->move_id;

                        
                        if($method_action == 'OUT'){//pengiriman_barang

                            // cek status pengiriman barang
                            $status  = "AND status NOT IN ('done','cancel')";
                            $cek_out = $this->_module->cek_status_pengiriman_barang_by_move_id($origin,$move_id,$status)->row_array();

                            if(!empty($cek_out['kode'])){//bearti pengiriman_barang = ready/draft

                                //update status = cancel pengiriman_barang, pengiriman_barang_items
                                $case  .= " when kode = '".$cek_out['kode']."' then '".$status_cancel."'";
                                $where .= "'".$cek_out['kode']."',";         

                                //get mms kode berdasarkan dept_id
                                $mms = $this->_module->get_kode_sub_menu_deptid('pengirimanbarang',$method_dept)->row_array();
                                if(!empty($mms['kode'])){
                                    $mms_kode = $mms['kode'];
                                }else{
                                    $mms_kode = '';
                                }    
                                    
                                // create log history pengiriman_barang
                                $note_log         = 'Batal Pengiriman Barang | '.$cek_out['kode'];
                                // $sql_log_history .= "('".$date_log."','".$mms_kode."','".$cek_out['kode']."','cancel','".$note_log."','".$nama_user."'), ";
                                $insert_log[] = array(
                                    'datelog'   => $date_log,
                                    'main_menu_sub_kode'    => $mms_kode,
                                    'kode'                  => $cek_out['kode'] ?? '',
                                    'jenis_log'             => 'cancel',
                                    'note'                  => $note_log,
                                    'nama_user'             => $nama_user ?? '',
                                    'ip_address'            => $ip);

                                $update_stock_move = true;

                                if ($cek_out['status'] == 'ready') {
                                    $kode_out = $cek_out['kode'] ?? ' ';
                                    $status_in_valid = true;
                                    $nm_dept = $this->_module->get_nama_dept_by_kode($cek_out['dept_id'])->row_array();
                                    $dokumen .= $kode_out . '  - ' . $nm_dept['nama'] ?? '';
                                    $dokumen .= '<br>';
                                }
                            }

                        }else if($method_action == 'IN'){//penerimaan_barang

                            // cek status penerimaan barang
                            $status  = "AND status NOT IN ('done','cancel')";
                            $cek_in  = $this->_module->cek_status_penerimaan_barang_by_move_id($origin,$move_id,$status)->row_array();
                                
                            if(!empty($cek_in['kode'])){//bearti penerimaan_barang = ready/draft

                                //update status = cancel penerimaan_barang, penerimaan_barang_items
                                $case2  .= " when kode = '".$cek_in['kode']."' then '".$status_cancel."'";
                                $where2 .= "'".$cek_in['kode']."',";             

                                //get mms kode berdasarkan dept_id
                                $mms = $this->_module->get_kode_sub_menu_deptid('penerimaanbarang',$method_dept)->row_array();
                                if(!empty($mms['kode'])){
                                    $mms_kode = $mms['kode'];
                                }else{
                                    $mms_kode = '';
                                }
                                    
                                // create log history penerimaan barang
                                $note_log         = 'Batal Penerimaan Barang | '.$cek_in['kode'];
                                // $sql_log_history .= "('".$date_log."','".$mms_kode."','".$cek_in['kode']."','cancel','".$note_log."','".$nama_user."'), ";
                                $insert_log[] = array(
                                    'datelog'   => $date_log,
                                    'main_menu_sub_kode'    => $mms_kode,
                                    'kode'                  => $cek_in['kode'] ?? '',
                                    'jenis_log'             => 'cancel',
                                    'note'                  => $note_log,
                                    'nama_user'             => $nama_user ?? '',
                                    'ip_address'            => $ip);


                                $update_stock_move = true;
                                if ($cek_in['status'] == 'ready') {
                                    $status_in_valid = true;
                                    $kode_in = $cek_in['kode'] ?? '';
                                    $nm_dept = $this->_module->get_nama_dept_by_kode($cek_in['dept_id'])->row_array();
                                    $dokumen .= $kode_in . ' - ' . $nm_dept['nama'] ?? '';
                                    $dokumen .= '<br>';
                                }
                            }
                        }
                        
                        if($update_stock_move == true){
                                        
                            //update status = cancel stock move, stock_move_items, stock_move_produk
                            $case3  .= " when move_id = '".$move_id."' then '".$status_cancel."'";
                            $where3 .= "'".$move_id."',";
                        }

                        $update_stock_move = false;

                    }/// end foreach stock_move

                    $batal_cfb = FALSE;
                    if($status_in_valid == false AND $batal_item == true){
                        //cek cfb jika statusnya tidak done , tidak cancel
                        foreach($cfb as $cfbi){
                            $cek_cfb = $this->m_procurementPurchase->cek_cfb_by_kode($cfbi->kode_cfb,'draft')->num_rows();                        
                            if($cek_cfb > 0 or $cek_head->type == 'pengiriman' ){
                                $batal_cfb = TRUE;
                                $sql_update_cfb = "UPDATE cfb SET status = '".$status_cancel."' WHERE kode_cfb = '".$cfbi->kode_cfb."' ";
                                $this->_module->update_reff_batch($sql_update_cfb);

                                //update cfb_items
                                $sql_update_cfb_items = "UPDATE cfb_items SET status = '".$status_cancel."' WHERE kode_cfb = '".$cfbi->kode_cfb."' ";
                                $this->_module->update_reff_batch($sql_update_cfb_items);
                            }
                        }
                    }


                    if($batal_item == true  AND $batal_cfb == TRUE AND $status_in_valid == false){

                        //update pengiriman barang
                        if(!empty($case) AND !empty($where)){

                            //update pengiriman_barang
                            $where = rtrim($where, ',');
                            $sql_update_pengiriman_barang = "UPDATE pengiriman_barang SET status =(case ".$case." end) WHERE  kode in (".$where.") ";
                            $this->_module->update_reff_batch($sql_update_pengiriman_barang);

                            // update pengiriman_barang_items
                            $sql_update_pengiriman_barang_items = "UPDATE pengiriman_barang_items SET status_barang =(case ".$case." end) WHERE  kode in (".$where.") ";
                            $this->_module->update_reff_batch($sql_update_pengiriman_barang_items);

                        }

                        //update penerimaan barang
                        if(!empty($case2) AND !empty($where2)){

                            //update penerimaan_barang
                            $where2 = rtrim($where2, ',');
                            $sql_update_penerimaan_barang = "UPDATE penerimaan_barang SET status =(case ".$case2." end) WHERE  kode in (".$where2.") ";
                            $this->_module->update_reff_batch($sql_update_penerimaan_barang);

                            // update penerimaan_barang_items
                            $sql_update_penerimaan_barang_items = "UPDATE penerimaan_barang_items SET status_barang =(case ".$case2." end) WHERE  kode in (".$where2.") ";
                            $this->_module->update_reff_batch($sql_update_penerimaan_barang_items);

                        }


                        //update stock_move, stock move items, stock_move produk
                        if(!empty($case3) AND !empty($where3)){

                            // update stock_move
                            $where3 = rtrim($where3, ',');
                            $sql_update_stock_move = "UPDATE stock_move SET status =(case ".$case3." end) WHERE  move_id in (".$where3.") ";
                            $this->_module->update_reff_batch($sql_update_stock_move);

                            // update stock_move_items
                            $sql_update_stock_move_items = "UPDATE stock_move_items SET status =(case ".$case3." end) WHERE  move_id in (".$where3.") ";
                            $this->_module->update_reff_batch($sql_update_stock_move_items);

                            // update stock_move_produk
                            $sql_update_stock_move_produk = "UPDATE stock_move_produk SET status =(case ".$case3." end) WHERE  move_id in (".$where3.") ";
                            $this->_module->update_reff_batch($sql_update_stock_move_produk);
                        }


                        $jenis_log   = "cancel";
                        $note_log    = "Batal Procurement Purchase | ".$kode;
                        $this->_module->gen_history($sub_menu, $kode, $jenis_log, addslashes($note_log), $username);

                        //create log history setiap yg batal
                        // if(!empty($sql_log_history)){
                        //     $sql_log_history = rtrim($sql_log_history, ', ');
                        //     $this->_module->simpan_log_history_batch($sql_log_history);
                        // }

                        //create log history setiap yg batal
                        if(!empty($insert_log)){
                            $this->_module->simpan_log_history_batch_2($insert_log);
                        }

                        //update status procurement_purchase = cancel
                        $this->m_procurementPurchase->update_status_procurement_purchase($kode, $status_cancel);
                        //update status procurement_purchase_items = cancel
                        $this->m_procurementPurchase->update_status_procurement_purchase_items($kode, $status_cancel); 

                        $callback = array('status' =>'success', 'message' => 'Procurement Purchase Berhasil dibatalkan', 'icon' => 'fa fa-check', 'type' => 'success');

                    }// end if batal_item = true

                    if($batal_item == false) {

                        $callback = array('status' => 'failed', 'message' => 'Procurement Purchase Gagal Dibatalkan !', 'icon' =>'fa fa-warning', 'type' => 'danger');
                    } else if($batal_cfb == FALSE) {

                        $callback = array('status' => 'failed', 'message' => 'Procurement Purchase Gagal Dibatalkan 2!', 'icon' =>'fa fa-warning', 'type' => 'danger');
                    } else if ($status_in_valid == true) {
                        $callback = array('status' => 'failed', 'message' => 'Procurement Purchase Gagal Dibatalkan. Terdapat Status yang Ready ! <br> '.$dokumen , 'icon' =>'fa fa-warning', 'type' => 'danger');
                    } else {
                    
                        $callback = array('status' =>'success', 'message' => 'Procurement Purchase Berhasil dibatalkan', 'icon' => 'fa fa-check', 'type' => 'success');
                    }

                    //unlock table
                    $this->_module->unlock_tabel();


                }//else 


            }

            if (!$this->_module->finishTransaction()) {
                throw new \Exception('Gagal Membatalkan Procurement Purchase', 500);
            }

            $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($callback));

        } catch(Exception $ex){
            $this->output->set_status_header($ex->getCode() ?? 500)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode(array('status'=>'failed','message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        } finally {
            $this->_module->rollbackTransaction();
            // unlock table
            $this->_module->unlock_tabel();
        }
    }


    public function get_list_uom_select2() 
    {

        $params     = addslashes($this->input->post('prod'));
        $kode_produk= addslashes($this->input->post('kode_produk'));
        $callback = $this->m_procurementPurchase->get_list_uom_by_kode_produk($kode_produk,$params);
        echo json_encode($callback);
    }


    public function get_list_uom_beli_select2() 
    {

        $params     = addslashes($this->input->post('prod'));
        $kode_produk= addslashes($this->input->post('kode_produk'));
        $callback = $this->m_procurementPurchase->get_list_uom_beli_by_kode_produk($kode_produk,$params);
        echo json_encode($callback);
    }

}