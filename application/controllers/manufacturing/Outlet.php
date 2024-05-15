<?php defined('BASEPATH') or exit ('No Direct Script Acces Allowed');

/**
 * 
 */
class Outlet extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->is_loggedin();//cek apakah user sudah login
        $this->load->model("_module");
        $this->load->model("m_inlet");
        $this->load->model("m_outlet");
        $this->load->model("m_mo");
        $this->load->library("token");
        $this->load->library('prints');
        $this->load->library('barcode');
	}

    public function index()
    {
        $data['id_dept']                = 'OUTLET';
        // $data['data_lot_inlet']         = $this->m_outlet->get_list_lot_inlet()->result();
        $data['data_oum_jual']          = $this->m_outlet->get_list_uom_jual();
        $data['data_oum']               = $this->_module->get_list_uom();
        $data['list_remark']            = $this->m_outlet->get_list_remark_by_grade();
        $uom_konversi                   = $this->m_outlet->get_list_uom_konversi();
        $data['uom_konversi']           = json_encode($uom_konversi);
        $this->load->view('manufacturing/v_outlet', $data);      
    }


    function get_count_lot_inlet()
    {
        try{
            if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
                // session habis
                // $callback = array('message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
                throw new \Exception('Waktu Anda Telah Habis', 401);
            }else{ 

                $jml_lot_blm_inlet      = $this->m_outlet->get_list_lot_belum_inlet()->num_rows();
                $jml_lot_inlet          = $this->m_outlet->get_list_lot_inlet()->num_rows();
                $callback               = array("jml_lot_blm_inlet" => $jml_lot_blm_inlet, "jml_lot_inlet" => $jml_lot_inlet);

                $this->output->set_status_header(200)
                        ->set_content_type('application/json', 'utf-8')->set_output(json_encode($callback));
            }

        }catch(Exception $ex){
            // $this->_module->finishTransaction();
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }

    }

    function search_data_inlet()
    {
        try{
            if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
                // session habis
                // $callback = array('message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
                throw new \Exception('Waktu Anda Telah Habis', 401);
            }else{

                $id     = $this->input->post('id');// id inlet
                $lot    = $this->input->post('lot');

                $sub_menu  = $this->uri->segment(2);
                $username  = addslashes($this->session->userdata('username'));

                $cek_lot = $this->m_outlet->cek_lot_inlet_by_kode($id);
               
                if(empty($id)){
                    $callback = array('status' => 'failed', 'message' => 'Data KP/Lot Kosong / tidak ditemukan !', 'icon' =>'fa fa-warning', 'type' => 'danger');
                }else if($cek_lot == 0){
                    $callback = array('status' => 'failed', 'message' => 'Lot <b>'.$lot.'</b> belum masuk MG GJD !'.$method, 'icon' =>'fa fa-warning', 'type' => 'danger');
                }else{

                    $inlet     = $this->m_inlet->get_data_inlet_by_id($id);

                    if(!empty($inlet)){

                        if($inlet->status =='done'){
                            $type = 'info_status_done';
                        }else if($inlet->status =='process'){
                            $type = 'info_status_process';
                        }else if($inlet->status =='cancel'){
                            $type = 'info_status_cancel';
                        }else{
                            $type = 'info_status_default';
                        }

                        $data_inlet[] = array(  
                            'id'            => $inlet->id,
                            'nama_mesin'    => $inlet->nama_mesin,
                            'lot'           => $inlet->lot,
                            'nama_marketing'=> $inlet->nama_sales_group,
                            'kode_mrp'      => $inlet->kode_mrp,
                            'corak_remark'  => $inlet->corak_remark,
                            'warna_remark'  => $inlet->warna_remark,
                            'lebar_jadi'    => $inlet->lebar_jadi,
                            'uom_lebar_jadi'=> $inlet->uom_lebar_jadi,
                            'quality'       => $inlet->nama_quality,
                            'benang'        => $inlet->benang,
                            'nama_jenis_kain'=> $inlet->nama_jenis_kain,
                            'benang'        => $inlet->benang,
                            'gramasi'       => $inlet->gramasi,
                            'berat'         => $inlet->berat,
                            'desain_barcode'=> $inlet->desain_barcode,
                            'k3l'           => $inlet->kode_k3l,
                            'nama_k3l'      => $inlet->nama_k3l,
                            'nama_status'   => "Status : ".$inlet->nama_status,
                            'tipe_alert'    => $type,
                            'status'        => $inlet->status,
                        );
                        // $dept      = $this->_module->get_nama_dept_by_kode('GJD')->row_array();

                        $hasil          = $this->m_outlet->get_mrp_production_fg_hasil_by_kode($inlet->kode_mrp,$inlet->id)->row();
                        if(!empty($hasil) ){
                            $sisa_qty       = $inlet->qty - $hasil->total_qty;
                            $sisa_qty2      = $inlet->qty2 - $hasil->total_qty2;
                            $potka_next     = $hasil->jml_lot + 1;
                        }else{
                            $sisa_qty       = $inlet->qty;
                            $sisa_qty2      = $inlet->qty2;
                            $potka_next     = 1;
                        }
                        $data_sisa   = array(
                                            'qty'   => round($sisa_qty,2),
                                            'uom'   => $inlet->uom, 
                                            'qty2'  => round($sisa_qty2,2),
                                            'uom2'  => $inlet->uom2, 
                                            'potongan_ke' => $potka_next
                        );

                        $callback = array('status' => 'success', 'message' => 'Data KP/Lot Inlet ditemukan !', 'icon' =>'fa fa-check', 'type' => 'success', 'record' => $data_inlet, 'sisa_target' =>$data_sisa);

                    }else{
                        $callback = array('status' => 'failed', 'message' => 'Data KP/Lot tidak ditemukan !', 'icon' =>'fa fa-warning', 'type' => 'danger');
                    }

                }

                $this->output->set_status_header(200)
                        ->set_content_type('application/json', 'utf-8')->set_output(json_encode($callback));


            }
        } catch(Exception $ex){
            $this->_module->finishTransaction();
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }

    function get_list_lot_hph()
    {

        try{
            if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
                // session habis
                // $callback = array('message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
                throw new \Exception('Waktu Anda Telah Habis', 401);
            }else{  

                $lot          = $this->input->post('lot');

                $data         = $this->m_outlet->get_list_lot_inlet_by_lot($lot)->result();
                $callback     = array("record" => $data);
                $this->output->set_status_header(200)
                        ->set_content_type('application/json', 'utf-8')->set_output(json_encode($callback));
            }
        
        } catch(Exception $ex){
            $this->_module->finishTransaction();
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }


    function view_detail_lot_modal()
    {

        $param = $this->input->post('param');
        if($param == 0){
            return $this->load->view('modal/v_outlet_list_lot_belum_inlet_modal');
        }
    }
    

    function get_data_lot_belum_inlet_modal()
    {

        if(isset($_POST['start']) && isset($_POST['draw'])){

            $list = $this->m_outlet->get_list_lot_belum_inlet_2();
            $data = array();
            $no = $_POST['start'];
            foreach ($list as $field) {

                $no++;
                $row = array();
                $row[] = $no;
                $row[] = $field->kode_produk;
                $row[] = $field->nama_produk;
                $row[] = $field->lot;
                $row[] = $field->qty.' '.$field->uom;
                $row[] = $field->qty2.' '.$field->uom2;
                $row[] = $field->qty_opname.' '.$field->uom_opname;
                $data[] = $row;
            }
    
            $output = array(
                "draw" => $_POST['draw'],
                "recordsTotal" => $this->m_outlet->count_all(),
                "recordsFiltered" => $this->m_outlet->count_filtered(),
                "data" => $data,
            );
            //output dalam format JSON
            echo json_encode($output);

        }else{
            die();
        }
        
    }

    function get_data_detail_hph_modal()
    {

        if(isset($_POST['start']) && isset($_POST['draw'])){
            $id   = $this->input->post('id');
            $list = $this->m_outlet->get_list_detail_hph($id);
            $data = array();
            $no = $_POST['start'];
            foreach ($list as $field) {

                $no++;
                $row = array();
                $row[] = $no;
                $row[] = $field->create_date;
                $row[] = $field->kode_produk;
                $row[] = $field->nama_produk;
                $row[] = $field->corak_remark;
                $row[] = $field->warna_remark;
                $row[] = $field->lot;
                $row[] = $field->nama_grade;
                $row[] = $field->qty.' '.$field->uom;
                $row[] = $field->qty2.' '.$field->uom2;
                $row[] = $field->qty_jual.' '.$field->uom_jual;
                $row[] = $field->qty2_jual.' '.$field->uom2_jual;
                $row[] = $field->lebar_jadi.' '.$field->uom_lebar_jadi;
                $row[] = $field->lokasi;
                $row[] = $field->lokasi_fisik;
                $row[] = $field->nama_user;
                $row[] = $field->kode_split ?? '';
                $data[] = $row;
            }
    
            $output = array(
                "draw" => $_POST['draw'],
                "recordsTotal" => $this->m_outlet->count_all_hph($id),
                "recordsFiltered" => $this->m_outlet->count_filtered_hph($id),
                "data" => $data,
            );
            //output dalam format JSON
            echo json_encode($output);

        }else{
            die();
        }
        
    }

    function view_detail_hph_modal()
    { 
        $data['id_inlet'] = $this->input->post('param');
        return $this->load->view('modal/v_outlet_list_detail_hph_modal',$data);
    }


    function save_outlet()
    {
        try{
            if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
                // session habis
                // $callback = array('message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
                throw new \Exception('Waktu Anda Telah Habis', 401);
            }else{

                $sub_menu = $this->uri->segment(2);
                $username = addslashes($this->session->userdata('username')); 
                $nama_user = $this->_module->get_nama_user($username)->row_array();
                
                $id_inlet = $this->input->post('id');
                $sisa_hph_mtr = $this->input->post('sisa_hph_mtr');// sisa mtr
                $sisa_hph_kg  = $this->input->post('sisa_hph_kg');// sisa kg
                $hph_mtr  = $this->input->post('hph_mtr'); // qty
                $hph_kg   = $this->input->post('hph_kg'); // qty2
                $grade    = $this->input->post('grade_hph');
                $qty_label              = $this->input->post('qty_label');
                $uom_label_barcode      = $this->input->post('uom_label_barcode');
                $qty2_label             = $this->input->post('qty2_label');
                $uom2_label_barcode     = $this->input->post('uom2_label_barcode');
                $lebar_jadi_label       = $this->input->post('lebar_jadi_label');
                $uom_lebar_jadi_label_barcode    = $this->input->post('uom_lebar_jadi_label_barcode');
                $arr_uom_jual = json_decode($this->input->post('arr_uom_jual'),true); 
                $remark_by_grade        = $this->input->post('remark_by_grade');
                $print        = $this->input->post('print');

                // start transaction
                $this->_module->startTransaction();

                if(empty($id_inlet)){
                    // throw new \Exception('Waktu Anda Telah Habis', 401);
                    $callback = array('status' => 'failed', 'message' => 'Id Inlet Kosong !', 'icon' =>'fa fa-warning', 'type' => 'danger');
                }else if(empty($hph_mtr) AND empty($hph_kg)){
                    $callback = array('status' => 'failed', 'field' => 'qty_mtr_hph','message' => 'Qty Mtr HPH atau Qty Kg HPH harus diisi  !', 'icon' =>'fa fa-warning', 'type' => 'danger');
                }else if(empty($grade)){
                    $callback = array('status' => 'failed', 'message' => 'Grade Harus dipilih !', 'icon' =>'fa fa-warning', 'type' => 'danger');
                }else if(empty($arr_uom_jual) AND ($grade == 'A' or $grade == 'B' or $grade == 'C') ){
                    $callback = array('status' => 'failed', 'field' => 'uom_jual', 'message' => 'Tabel Qty Uom Jual Harus diisi minimal 1 Uom jual !', 'icon' =>'fa fa-warning', 'type' => 'danger');
                }else if(empty($uom_label_barcode) AND ($grade == 'A' or $grade == 'B' or $grade == 'C')  ){
                    $callback = array('status' => 'failed','field' => 'uom_label_barcode', 'message' => 'Uom Qty1 Jual Harus diisi !', 'icon' =>'fa fa-warning', 'type' => 'danger');
                }else if(empty($qty_label) AND ($grade == 'A' or $grade == 'B' or $grade == 'C')  ){
                    $callback = array('status' => 'failed', 'field' => 'qty_label','message' => 'Qty1 Jual Harus diisi !', 'icon' =>'fa fa-warning', 'type' => 'danger');
                }else if($grade != 'A' And $grade != 'B' And $grade != 'C' And $grade != 'F'){
                    $callback = array('status' => 'failed', 'field' => 'remark_by_grade', 'message'=>'Barcode untuk Grade '.$grade.' tidak tersedia !', 'icon' => 'fa fa-warning', 'type'=>'danger');
                }else if($grade == 'C' And empty($remark_by_grade)){
                    $callback = array('status' => 'failed',  'message'=>'Barcode Grade C harus terdapat Remark C !', 'icon' => 'fa fa-warning', 'type'=>'danger');
                }else if(($grade == 'A' or $grade == 'B') And empty($lebar_jadi_label)){
                    $callback = array('status' => 'failed', 'field' => 'lebar_jadi_label', 'message'=>'Lebar jadi Harus Diisi !', 'icon' => 'fa fa-warning', 'type'=>'danger');
                }else if(($grade == 'A' or $grade == 'B') And empty($uom_lebar_jadi_label_barcode)){
                    $callback = array('status' => 'failed', 'field' => 'uom_lebar_jadi_label_barcode' ,'message'=>'Uom Lebar jadi Harus Diisi !', 'icon' => 'fa fa-warning', 'type'=>'danger');
                }else{  

                    $inlet     = $this->m_inlet->get_data_inlet_by_id($id_inlet);

                    // lock table
                    // $this->_module->lock_tabel("stock_quant WRITE, mrp_production WRITE, mrp_production_fg_hasil WRITE, mrp_production_fg_target WRITE, mrp_production_rm_target WRITE, mrp_production_rm_hasil WRITE, stock_move_items WRITE, stock_move_produk WRITE, stock_move WRITE, mrp_satuan WRITE, mrp_production_rm_target as rm WRITE, mst_produk as mp WRITE, mrp_inlet  WRITE, stock_move_items as smi WRITE, stock_quant as sq WRITE, mst_produk WRITE, token_increment WRITE,departemen as d WRITE, mrp_production_rm_target as rmt WRITE,user WRITE ,main_menu_sub WRITE,log_history WRITE,mrp_production_fg_hasil  as fg WRITE, mrp_production as mrp write, mrp_production_fg_target as tfg WRITE");


                    // get data inlet
                    if(empty($inlet->id)){
                        $callback = array('status' => 'failed', 'message' => 'Data KP/Lot Inlet tidak ditemukan !', 'icon' =>'fa fa-warning', 'type' => 'danger');
                    }else if($inlet->status == 'done'){
                        $callback = array('status' => 'failed', 'message' => 'Data KP/Lot Inlet sudah Done  !', 'icon' =>'fa fa-warning', 'type' => 'danger');
                    }else if($inlet->status == 'cancel'){
                        $callback = array('status' => 'failed', 'message' => 'Data KP/Lot Inlet Cancel / dibatalkan !', 'icon' =>'fa fa-warning', 'type' => 'danger');
                    }else{  

                        $kode_mg        = $inlet->kode_mrp;
                        $kode_produk_rm = $inlet->kode_produk;
                        $nama_produk_rm = $inlet->nama_produk;
                        
                        $quant_id_inlet = $inlet->quant_id;
                        $corak_remark   = $inlet->corak_remark;
                        $warna_remark   = $inlet->warna_remark;
                        $kode_sales_group = $inlet->sales_group; //example MKT001
                        $desain_barcode = $inlet->desain_barcode;
                        $lot_inlet      = $inlet->lot;
                        $nama_quality   = $inlet->nama_quality;
                        $k3l            = $inlet->kode_k3l;

                        $data_mrp = $this->m_outlet->get_mrp_production_by_kode($kode_mg);

                        // $move_rm  = $this->m_outlet->get_move_id_rm_target_by_produk($kode_mg,$kode_produk_rm)->row();

                        // cek apakah lot ini masih terpesan di MG GJD yg sama
                        // $cek_reserve_move = $this->m_outlet->cek_reserve_lot_to_mrp($kode_mg,$quant_id_inlet,$lot_inlet)->num_rows();

                        // $cek_smi_rm       = '';
                        // if($cek_reserve_move > 0 ){
                        // }
                        $cek_smi_rm = $this->m_outlet->cek_stock_move_items_by_kode($kode_mg,$quant_id_inlet,$lot_inlet)->row();
                        $cek_smi_rm_qty = $cek_smi_rm->qty ?? 0 ;
                        $cek_smi_rm_qty2 = $cek_smi_rm->qty2 ?? 0 ;
                        // cek lokasi quant id apakah waste 
                        $sq     = $this->_module->get_stock_quant_by_id($quant_id_inlet)->row();
                        $sq_ex  = explode("/", $sq->lokasi);
                        $waste_lokasi = $sq_ex[1] ?? '';
                        $is_waste_lokasi = false;
                        if($waste_lokasi == "Waste"){
                            $is_waste_lokasi = true;
                            $cek_smi_rm = $this->m_outlet->cek_stock_move_items_by_kode_2($kode_mg)->row();// fg jika quant inlet habis buat waste limit 1
                        }

                        if(empty($data_mrp)){
                            $callback = array('status' => 'failed', 'message'=> 'Data KP/Lot Inlet tidak ditemukan !', 'icon' =>'fa fa-warning', 'type' => 'danger');
                        }else if($data_mrp->status == 'done'){
                            $callback = array('status' => 'failed', 'message'=>'Maaf, Data Tidak Bisa Disimpan, Status MG Sudah Done !', 'icon' => 'fa fa-warning', 'type'=>'danger');
                        }else if($data_mrp->status == 'draft'){
                            $callback = array('status' => 'failed', 'message'=>'Maaf, Data Tidak Bisa Disimpan, Status MG Masih Draft !', 'icon' => 'fa fa-warning', 'type'=>'danger');
                        }else if($data_mrp->status == 'cancel'){
                            $callback = array('status' => 'failed', 'message'=>'Maaf, Data Tidak Bisa Disimpan, Status MG Batal !', 'icon' => 'fa fa-warning', 'type'=>'danger');
                        }else if($data_mrp->status == 'hold'){
                            $callback = array('status' => 'failed', 'message'=>'Maaf, Data Tidak Bisa Disimpan, Status MG di Hold !', 'icon' => 'fa fa-warning', 'type'=>'danger');
                        }else if(empty($cek_smi_rm) AND $is_waste_lokasi == false){
                            $callback = array('status' => 'failed', 'message'=>'Maaf, Stock Move Bahan Baku tidak ditemukan !', 'icon' => 'fa fa-warning', 'type'=>'danger');
                        }else if(empty($cek_smi_rm) AND $is_waste_lokasi == true){
                            $callback = array('status' => 'failed', 'message'=>'Maaf, Stock Move Bahan Baku tidak ditemukan !', 'icon' => 'fa fa-warning', 'type'=>'danger');
                        }else if(round($cek_smi_rm_qty,2) != round($sisa_hph_mtr,2) AND $sisa_hph_mtr > 0 AND $is_waste_lokasi == false){
                            $callback = array('status' => 'failed', 'message'=>'Maaf, <b>Sisa Qty Mtr</b> dan <b>Stock Qty Mtr</b> tidak sama, Harap Pilih kp/lot kembali !', 'icon' => 'fa fa-warning', 'type'=>'danger');
                        }else if(round($cek_smi_rm_qty2,2) != round($sisa_hph_kg,2) AND $sisa_hph_kg > 0 AND $is_waste_lokasi == false){
                            $callback = array('status' => 'failed', 'message'=>'Maaf, <b>Sisa Qty Kg</b> dan <b>Stock Qty Kg</b> tidak sama, Harap Pilih kp/lot kembali !', 'icon' => 'fa fa-warning', 'type'=>'danger');
                        }else{

                            $kode_produk_fg = $data_mrp->kode_produk;
                            $nama_produk_fg = $data_mrp->nama_produk;
                            $origin_mg      = $data_mrp->origin;
                            $dept_id_mg     = $data_mrp->dept_id;
                            $lebar_greige_fg= $data_mrp->lebar_greige;
                            $uom_lebar_greige_fg = $data_mrp->uom_lebar_greige;

                            $get_uom        = $this->_module->get_uom_by_kode_produk($kode_produk_fg)->row_array();//get uom 1 dan uom 2 by kode_produk
                            $uom_1_fg       = $get_uom['uom'];
                            $uom_2_fg       = $get_uom['uom_2'];

                            $origin_prod_rm = $cek_smi_rm->origin_prod;
                            $move_id_rm     = $cek_smi_rm->move_id;
                            $grade_rm       = $cek_smi_rm->nama_grade;
                            $lokasi_fisik_rm= $cek_smi_rm->lokasi_fisik;
                            $qty_1_rm       = $cek_smi_rm->qty;
                            $uom_1_rm       = $cek_smi_rm->uom;
                            $qty_2_rm       = $cek_smi_rm->qty2;
                            $uom_2_rm       = $cek_smi_rm->uom2;
                            $reff_note_rm   = $cek_smi_rm->reff_note;
                            $lebar_greige_rm= $cek_smi_rm->lebar_greige;
                            $uom_lebar_greige_rm = $cek_smi_rm->uom_lebar_greige;
                            $lebr_jadi_rm   = $cek_smi_rm->lebar_jadi;
                            $uom_lebar_jadi_rm = $cek_smi_rm->uom_lebar_jadi;
                            $qty_rm_target  = $cek_smi_rm->qty_rm_target;


                            $tgl            = date("Y-m-d H:i:s");
                            $status_done    = 'done';

                            $start    = $this->_module->get_last_quant_id();
                            $get_ro   = $this->m_mo->get_row_order_fg_hasil($kode_mg)->row_array();
                            $row_order= $get_ro['row']+1;

                            $move_fg    = $this->m_mo->get_move_id_fg_target_by_kode($kode_mg)->row_array();
                            $move_id_fg = $move_fg['move_id'];

                            // get lokasi tujuan fg
                            $lokasi_fg = $this->_module->get_location_by_move_id($move_id_fg)->row_array();

                            // get row order stock_move_items fg
                            $row_order_smi  = $this->_module->get_row_order_stock_move_items_by_kode($move_id_fg);

                            // get_row_order mrp_satuan 
                            $row_order_satuan = $this->m_outlet->get_row_order_mrp_satuan_by_kode($move_id_fg);

                            $org_mo      = explode("|", $origin_mg);
                            $org_mo_loop = 0;
                            $sales_order = "";
                            foreach($org_mo as $org_mos){
                                if($org_mo_loop == 0){
                                    $sales_order = trim($org_mos);
                                }
                                $org_mo_loop++;
                            }

                            if($grade == 'A'){
                                $barcode_id = $this->token->noUrut('stock_quant_a', date('my'), true)->generate('', '%05d')->get();
                            }else if($grade == 'B'){
                                $barcode_id = $this->token->noUrut('stock_quant_b', date('my'), true)->generate($grade, '%05d')->get();
                            }else if($grade == 'C'){
                                $barcode_id = $this->token->noUrut('stock_quant_c', date('my'), true)->generate($grade, '%05d')->get();
                            // }else{
                            //     $barcode_id = $this->token->noUrut('stock_quant_f', date('my'), true)->generate($grade, '%05d')->get();
                            }

                            // ** START HASIL PRODUKSI  **\\

                            $data_array_mrp_fg_hasil = array();
                            $data_array_stock_move_items = array();
                            $data_array_stock_quant  = array();
                            // $data_array_stock_quant_tmp  = array();
                            $warna_remark_fix   = '';  
                            $corak_remark_fix   = '';
                            $qty_not_same       = false;

                            // fg hasil
                            if($grade == 'A' or $grade == 'B' OR $grade == 'C'){

                                if($grade == 'C'){
                                    $lebar_jadi_label = '';
                                    $uom_lebar_jadi_label_barcode = '';
                                }
                                // fg hasil
                                $data_array_mrp_fg_hasil[] =  array(
                                                        'kode'          => $kode_mg,
                                                        'move_id'       => $move_id_fg,
                                                        'create_date'   => $tgl,
                                                        'quant_id'      => $start,
                                                        'kode_produk'   => $kode_produk_fg,
                                                        'nama_produk'   => $nama_produk_fg,
                                                        'lot'           => trim($barcode_id),
                                                        'nama_grade'    => $grade,
                                                        'qty'           => $hph_mtr,
                                                        'uom'           => $uom_1_fg,
                                                        'qty2'          => $hph_kg,
                                                        'uom2'          => $uom_2_fg,
                                                        'lokasi'        => $lokasi_fg['lokasi_tujuan'],
                                                        'lebar_jadi'    => $lebar_jadi_label,
                                                        'uom_lebar_jadi'=> $uom_lebar_jadi_label_barcode,
                                                        'lebar_greige'  => $lebar_greige_fg,
                                                        'uom_lebar_greige' => $uom_lebar_greige_fg,
                                                        'sales_order'   => $sales_order,
                                                        'sales_group'   => $kode_sales_group,
                                                        'consume'       => 'yes',
                                                        'nama_user'     => $nama_user['nama'],
                                                        'row_order'     => $row_order,
                                                        'id_inlet'      => $id_inlet


                                );
                                $row_order++;
                                                               
                                if($grade == 'A' or $grade == 'B'){
                                    $corak_remark_fix   = $corak_remark.' '.$nama_quality;
                                    $warna_remark_fix   = $warna_remark;
                                }else if($grade == 'C'){
                                    $corak_remark_fix   = $remark_by_grade.' '.$corak_remark.' '.$nama_quality;
                                    $warna_remark_fix   = '';
                                }

                                // stock_quant fg
                                $data_array_stock_quant[] = array(
                                                            'quant_id'      => $start,
                                                            'create_date'   => $tgl,
                                                            'move_date'     => $tgl,
                                                            'kode_produk'   => $kode_produk_fg,
                                                            'nama_produk'   => $nama_produk_fg,
                                                            'corak_remark'  => trim($corak_remark_fix),
                                                            'warna_remark'  => trim($warna_remark_fix),
                                                            'lot'           => trim($barcode_id),
                                                            'nama_grade'    => $grade,
                                                            'qty'           => $hph_mtr,
                                                            'uom'           => $uom_1_fg,
                                                            'qty2'          => $hph_kg,
                                                            'uom2'          => $uom_2_fg,
                                                            'qty_jual'      => $qty_label,
                                                            'uom_jual'      => $uom_label_barcode,
                                                            'qty2_jual'     => $qty2_label,
                                                            'uom2_jual'     => $uom2_label_barcode,
                                                            'qty_opname'    => '',
                                                            'uom_opname'=> '',
                                                            'lokasi'        => $lokasi_fg['lokasi_tujuan'],
                                                            'lokasi_fisik'  => "PORT",
                                                            'reff_note'     => '',
                                                            'lebar_greige'  => $lebar_greige_fg,
                                                            'uom_lebar_greige' => $uom_lebar_greige_fg,
                                                            'lebar_jadi'    => $lebar_jadi_label,
                                                            'uom_lebar_jadi'=> $uom_lebar_jadi_label_barcode,
                                                            'reserve_move'  => '',
                                                            'reserve_origin'=> $origin_mg,
                                                            'sales_order'   => $sales_order,
                                                            'sales_group'   => $kode_sales_group,

                                );

                                // stock move items fg
                                $data_array_stock_move_items[] = array(
                                                                'move_id'           => $move_id_fg,
                                                                'quant_id'          => $start,
                                                                'tanggal_transaksi' => $tgl,
                                                                'kode_produk'       => $kode_produk_fg,
                                                                'nama_produk'       => $nama_produk_fg,
                                                                'lot'               => trim($barcode_id),
                                                                'qty'               => $hph_mtr,
                                                                'uom'               => $uom_1_fg,
                                                                'qty2'              => $hph_kg,
                                                                'uom2'              => $uom_2_fg,
                                                                'status'            => $status_done,
                                                                'lebar_greige'      => $lebar_greige_fg,
                                                                'uom_lebar_greige'  => $uom_lebar_greige_fg,
                                                                'lebar_jadi'        => $lebar_jadi_label,
                                                                'uom_lebar_jadi'    => $uom_lebar_jadi_label_barcode,
                                                                'lokasi_fisik'      => "PORT",
                                                                'origin_prod'       => '',
                                                                'row_order'         => $row_order_smi
                                );
                                // satuan Jual
                                $qty_not_same   = false;
                                $uom_not_same   = '';
                                $data_satuan_jual = array();
                                foreach($arr_uom_jual as $uj){
                                    $data_satuan_jual[] = array(  
                                                        'kode'              => $kode_mg,
                                                        'tanggal'           => $tgl,
                                                        'quant_id'          => $start,
                                                        'kode_produk'       => $kode_produk_fg,
                                                        'nama_produk'       => $nama_produk_fg,
                                                        'lot'               => trim($barcode_id),
                                                        'qty'               => $uj['value_uom_jual'],
                                                        'uom'               => $uj['uom_jual'],
                                                        'row_order'         => $row_order_satuan,
                                    );
                                    $row_order_satuan++;

                                    // if($uj['uom_jual'] == 'Mtr'){
                                    //     if($uj['value_uom_jual'] != $hph_mtr){
                                    //         $qty_not_same = true;
                                    //         $uom_not_same .= "Mtr, ";
                                    //     }
                                    // }
                                    // if($uj['uom_jual'] == 'Kg'){
                                    //     if($uj['value_uom_jual'] != $hph_kg){
                                    //         $qty_not_same = true;
                                    //         $uom_not_same .= "Kg, ";
                                    //     }
                                    // }

                                    if(!empty($uom_label_barcode)){
                                        if($uom_label_barcode == $uj['uom_jual']){
                                            if($qty_label != $uj['value_uom_jual']){
                                                $qty_not_same = true;
                                                $uom_not_same .= $uom_label_barcode.", ";
                                            }
                                        }
                                    }

                                    if(!empty($uom2_label_barcode)){
                                        if($uom2_label_barcode == $uj['uom_jual']){
                                            if($qty2_label != $uj['value_uom_jual']){
                                                $qty_not_same = true;
                                                $uom_not_same .= $uom_label_barcode.", ";
                                            }
                                        }
                                    }
                                }

                                $start++;
                            }
                            // ** FINISH HASIL PRODUKSI  **\\

                            
                            // ** START KONSUMSI BAHAN BAKU  **\\
                            $row_order_rm_smi   = $this->_module->get_row_order_stock_move_items_by_kode($move_id_rm); // row yang sudah + 1

                            $lokasi_rm = $this->_module->get_location_by_move_id($move_id_rm)->row_array();
                            $dept      = $this->_module->get_nama_dept_by_kode($dept_id_mg)->row_array();// get dept stock
                            $get_sq    = $this->_module->get_stock_quant_by_id($quant_id_inlet)->row_array();
                            $get_ro      = $this->m_mo->get_row_order_rm_hasil($kode_mg)->row_array();
                            $row_order_rm_hasil = $get_ro['row']+1;
                            $rm_not_valid = false;
                            $waste_not_valid = false;
                            $lokasi_waste_empty = false;
                            $grade_not_valid    = false;

                            $qty_op_update = 0;
                            $qty_op_new    = 0;
                            $uom_opname_rm = '';

                            $data_update_rm_sq = array();
                            $update_rm_sq      = array();
                            $update_rm_smi     = array();
                            $update_rm_smi_where= array();
                            $data_array_mrp_rm_hasil = array();

                            if($get_sq['lokasi'] == $dept['stock_location'] ){ // cek lokasi lot == lokasi mg stock

                                if($get_sq['qty'] == $qty_1_rm AND $get_sq['qty2'] == $qty_2_rm){ // cek qty di stock quant dan di smi
                                    
                                    if($grade == 'A' or $grade == 'B' or $grade == 'C'){
                                        if((double)$hph_mtr < (double)$qty_1_rm){

                                            if($get_sq['qty_opname'] > 0 AND $qty_1_rm > 0){
                                                $qty_op_new = ($get_sq['qty_opname'] / $qty_1_rm) * $hph_mtr;
                                                $qty_op_update = $get_sq['qty_opname'] - round($qty_op_new,2);
                                                $uom_opname_rm = $get_sq['qty_opname'];
                                            }

                                            if((double)$hph_kg < (double)$qty_2_rm){
                                                $qty1_update = (double)$qty_1_rm - (double)round($hph_mtr,2);// update ke quant bahan baku yg ready
                                                $qty2_update =  (double)$qty_2_rm - (double)round($hph_kg,2);
                                               
                                            }else{ // hph_kg >= qty_2_rm

                                                $qty1_update = (double)$qty_1_rm - (double)round($hph_mtr,2);// update ke quant bahan baku yg ready
                                                $qty2_update = 0;
                                                // if($hph_kg >= $qty_2_rm){
                                                // }else{
                                                //     $qty2_update = $qty_2_rm;
                                                // }
                                                $hph_kg = $qty_2_rm;
                                            }

                                                                                    
                                            // update stock_move_items
                                            $update_rm_smi  = array(
                                                                'qty'      => $qty1_update,
                                                                'qty2'     => $qty2_update,
                                            );
                                            $update_rm_smi_where = array(
                                                                'quant_id' => $quant_id_inlet,
                                                                'lot'      => $lot_inlet,
                                                                'move_id'  => $move_id_rm,
                                                                'status'   => 'ready',
                                            );

                                            // update stock_quant
                                            $update_rm_sq = array(
                                                                'quant_id' => $quant_id_inlet,
                                                                'qty'      => $qty1_update,
                                                                'qty2'     => $qty2_update,
                                                                'qty_opname'=> $qty_op_update,
                                                                // 'move_date' => $tgl
                                            );
                                            array_push($data_update_rm_sq,$update_rm_sq);


                                            // insert stock quant
                                            $data_array_stock_quant[] = array(
                                                                    'quant_id'      => $start,
                                                                    'create_date'   => $tgl,
                                                                    'move_date'     => $tgl,
                                                                    'kode_produk'   => $kode_produk_rm,
                                                                    'nama_produk'   => $nama_produk_rm,
                                                                    'corak_remark'  => '',
                                                                    'warna_remark'  => '',
                                                                    'lot'           => $lot_inlet,
                                                                    'nama_grade'    => $grade_rm,
                                                                    'qty'           => $hph_mtr,
                                                                    'uom'           => $uom_1_rm,
                                                                    'qty2'          => $hph_kg,
                                                                    'uom2'          => $uom_2_rm,
                                                                    'qty_jual'      => '',
                                                                    'uom_jual'      => '',
                                                                    'qty2_jual'     => '',
                                                                    'uom2_jual'     => '',
                                                                    'qty_opname'    => $qty_op_new,
                                                                    'uom_opname'    => $uom_opname_rm,
                                                                    'lokasi'        => $lokasi_rm['lokasi_tujuan'],
                                                                    'lokasi_fisik'  => $lokasi_fisik_rm,
                                                                    'reff_note'     => $reff_note_rm,
                                                                    'lebar_greige'  => $lebar_greige_rm,
                                                                    'uom_lebar_greige' => $uom_lebar_greige_rm,
                                                                    'lebar_jadi'    => $lebr_jadi_rm,
                                                                    'uom_lebar_jadi'=> $uom_lebar_jadi_rm,
                                                                    'reserve_move'  => $move_id_rm,
                                                                    'reserve_origin'=> $origin_mg,
                                                                    'sales_order'   => $sales_order,
                                                                    'sales_group'   => $kode_sales_group,                                                              

                                                                
                                            );


                                            // insert stock move items
                                            $data_array_stock_move_items[] = array(
                                                                            'move_id'           => $move_id_rm,
                                                                            'quant_id'          => $start,
                                                                            'tanggal_transaksi' => $tgl,
                                                                            'kode_produk'       => $kode_produk_rm,
                                                                            'nama_produk'       => $nama_produk_rm,
                                                                            'lot'               => $lot_inlet,
                                                                            'qty'               => $hph_mtr,
                                                                            'uom'               => $uom_1_rm,
                                                                            'qty2'              => $hph_kg,
                                                                            'uom2'              => $uom_2_rm,
                                                                            'status'            => $status_done,
                                                                            'lebar_greige'      => $lebar_greige_rm,
                                                                            'uom_lebar_greige'  => $uom_lebar_greige_rm,
                                                                            'lebar_jadi'        => $lebr_jadi_rm,
                                                                            'uom_lebar_jadi'    => $uom_lebar_jadi_rm,
                                                                            'lokasi_fisik'      => $lokasi_fisik_rm,
                                                                            'origin_prod'       => $origin_prod_rm,
                                                                            'row_order'         => $row_order_rm_smi
                                                                        
                                            );

                                            // insert mrp rm hasil
                                            $data_array_mrp_rm_hasil[] = array(
                                                                        'kode'          => $kode_mg,
                                                                        'move_id'       => $move_id_rm,
                                                                        'quant_id'      => $start,
                                                                        'kode_produk'   => $kode_produk_rm,
                                                                        'nama_produk'   => $nama_produk_rm,
                                                                        'lot'           => $lot_inlet,
                                                                        'qty'           => $hph_mtr,
                                                                        'uom'           => $uom_1_rm,
                                                                        'origin_prod'   => $origin_prod_rm,
                                                                        'row_order'     => $row_order_rm_hasil
                                            );


                                        }else{ // if($hph_mtr == $qty_1_rm) { //$qty1_new <= 0
                                            
                                            if($get_sq['qty_opname'] > 0 AND $qty_1_rm > 0){
                                                $qty_op_new = ($get_sq['qty_opname'] / $qty_1_rm) * $hph_mtr;
                                                $qty_op_update = $get_sq['qty_opname'] - round($qty_op_new,2);
                                                $uom_opname_rm = $get_sq['qty_opname'];
                                            }
                                         
                                            if($hph_kg < $qty_2_rm){

                                                // if($hph_mtr == $qty_1_rm){
                                                // }else{ // hph_mtr > $qty_1_rm
                                                //     $qty1_update = $qty_1_rm; // qty = qty_1_rm
                                                // }
                                                
                                                $qty1_update = 0;
                                                $qty2_update = (double)$qty_2_rm - (double)round($hph_kg,2);
                                                $hph_mtr     = $qty_1_rm;

                                                // update stock_quant
                                                $update_rm_sq = array(
                                                                    'quant_id'  => $quant_id_inlet,
                                                                    'qty'       => $qty1_update,
                                                                    'qty2'      => $qty2_update,
                                                                    'qty_opname'=> $qty_op_update,
                                                );

                                                array_push($data_update_rm_sq,$update_rm_sq);

                                                // update stock_move_items
                                                $update_rm_smi  = array(
                                                                    'qty'      => $qty1_update,
                                                                    'qty2'     => $qty2_update,
                                                );
                                                $update_rm_smi_where = array(
                                                                    'quant_id' => $quant_id_inlet,
                                                                    'lot'      => $lot_inlet,
                                                                    'move_id'  => $move_id_rm,
                                                                    'status'   => 'ready',
                                                );
                                                
                                                // insert stock_quant
                                                $data_array_stock_quant[] = array(
                                                                        'quant_id'      => $start,
                                                                        'create_date'   => $tgl,
                                                                        'move_date'     => $tgl,
                                                                        'kode_produk'   => $kode_produk_rm,
                                                                        'nama_produk'   => $nama_produk_rm,
                                                                        'corak_remark'  => '',
                                                                        'warna_remark'  => '',
                                                                        'lot'           => $lot_inlet,
                                                                        'nama_grade'    => $grade_rm,
                                                                        'qty'           => $hph_mtr,
                                                                        'uom'           => $uom_1_rm,
                                                                        'qty2'          => $hph_kg,
                                                                        'uom2'          => $uom_2_rm,
                                                                        'qty_jual'      => '',
                                                                        'uom_jual'      => '',
                                                                        'qty2_jual'     => '',
                                                                        'uom2_jual'     => '',
                                                                        'qty_opname'    => $qty_op_new,
                                                                        'uom_opname'    => $uom_opname_rm,
                                                                        'lokasi'        => $lokasi_rm['lokasi_tujuan'],
                                                                        'lokasi_fisik'  => $lokasi_fisik_rm,
                                                                        'reff_note'     => $reff_note_rm,
                                                                        'lebar_greige'  => $lebar_greige_rm,
                                                                        'uom_lebar_greige' => $uom_lebar_greige_rm,
                                                                        'lebar_jadi'    => $lebr_jadi_rm,
                                                                        'uom_lebar_jadi'=> $uom_lebar_jadi_rm,
                                                                        'reserve_move'  => $move_id_rm,
                                                                        'reserve_origin'=> $origin_mg,
                                                                        'sales_order'   => $sales_order,
                                                                        'sales_group'   => $kode_sales_group,                                                              
                                                );

                                                // insert stock_move_items
                                                $data_array_stock_move_items[] = array(
                                                                                'move_id'           => $move_id_rm,
                                                                                'quant_id'          => $start,
                                                                                'tanggal_transaksi' => $tgl,
                                                                                'kode_produk'       => $kode_produk_rm,
                                                                                'nama_produk'       => $nama_produk_fg,
                                                                                'lot'               => $lot_inlet,
                                                                                'qty'               => $hph_mtr,
                                                                                'uom'               => $uom_1_rm,
                                                                                'qty2'              => $hph_kg,
                                                                                'uom2'              => $uom_2_rm,
                                                                                'status'            => $status_done,
                                                                                'lebar_greige'      => $lebar_greige_rm,
                                                                                'uom_lebar_greige'  => $uom_lebar_greige_rm,
                                                                                'lebar_jadi'        => $lebr_jadi_rm,
                                                                                'uom_lebar_jadi'    => $uom_lebar_jadi_rm,
                                                                                'lokasi_fisik'      => $lokasi_fisik_rm,
                                                                                'origin_prod'       => $origin_prod_rm,
                                                                                'row_order'         => $row_order_rm_smi
                                                );

                                                // simpan rm hasil
                                                $data_array_mrp_rm_hasil[] = array(
                                                                            'kode'          => $kode_mg,
                                                                            'move_id'       => $move_id_rm,
                                                                            'quant_id'      => $start,
                                                                            'kode_produk'   => $kode_produk_rm,
                                                                            'nama_produk'   => $nama_produk_rm,
                                                                            'lot'           => $lot_inlet,
                                                                            'qty'           => $hph_mtr,
                                                                            'uom'           => $uom_1_rm,
                                                                            'origin_prod'   => $origin_prod_rm,
                                                                            'row_order'     => $row_order_rm_hasil
                                                );


                                            }else{ //($hph_kg >= $qty_2_rm)

                                                // update lokasi , update reserve_origin
                                                $update_rm_sq = array(
                                                                    'quant_id'  => $quant_id_inlet,
                                                                    'move_date' => $tgl,
                                                                    'lokasi'    => $lokasi_rm['lokasi_tujuan'],
                                                                    'reserve_origin' => $origin_mg,
                                                                    'reserve_move'=>$move_id_rm,
                                                                    'sales_order'   => $sales_order,
                                                                    'sales_group'   => $kode_sales_group,
                                                );
                                                array_push($data_update_rm_sq,$update_rm_sq);
                                                
                                                // update_status done smi
                                                $update_rm_smi = array(
                                                                    'tanggal_transaksi' => $tgl,
                                                                    // 'move_id'  => $move_id_rm,
                                                                    'status'   => $status_done
                                                );
                                                $update_rm_smi_where = array(
                                                                    'quant_id' => $quant_id_inlet,
                                                                    'lot'      => $lot_inlet,
                                                                    'move_id'  => $move_id_rm,
                                                                    'status'   => 'ready',
                                                );
                                              
                                                // simpan rm hasil
                                                $data_array_mrp_rm_hasil[] = array(
                                                                            'kode'          => $kode_mg,
                                                                            'move_id'       => $move_id_rm,
                                                                            'quant_id'      => $quant_id_inlet,
                                                                            'kode_produk'   => $kode_produk_rm,
                                                                            'nama_produk'   => $nama_produk_rm,
                                                                            'lot'           => $lot_inlet,
                                                                            'qty'           => $hph_mtr,
                                                                            'uom'           => $uom_1_rm,
                                                                            'origin_prod'   => $origin_prod_rm,
                                                                            'row_order'     => $row_order_rm_hasil
                                                );
                                            }


                                        }

                                    }else if($grade == 'F'){

                                        if((double)$hph_mtr < 0){
                                            $hph_mtr = 0.00;
                                        }
                                        if((double)$hph_kg < 0){
                                            $hph_kg = 0.00;
                                        }

                                        if((double)$qty_1_rm ==(double)$hph_mtr AND (double)$qty_2_rm == (double)$hph_kg){

                                            if(!empty($dept['waste_location'])){
                                                $waste_location = $dept['waste_location'];

                                                // update status smi = done by lot/quantid
                                                $lot_waste_rm = "S|".$lot_inlet;
                                                $barcode_id    = $lot_waste_rm;
                                                $update_rm_smi  = array(
                                                                    'status'      => $status_done,
                                                                    'move_id'     => $move_id_fg,
                                                                    'lot'         => $lot_waste_rm,
                                                                    'tanggal_transaksi'     => $tgl
                                                );
                                                $update_rm_smi_where = array(
                                                                    'quant_id' => $quant_id_inlet,
                                                                    'lot'      => $lot_inlet,
                                                                    'move_id'  => $move_id_rm,
                                                                    'status'   => 'ready',
                                                );

                                                // update stock quant (lokasi,move_date,reserve_move,lokasi_fisik)
                                                $update_rm_sq = array(
                                                                    'quant_id'      => $quant_id_inlet,
                                                                    'move_date'     => $tgl,
                                                                    'reserve_move'  => $move_id_fg,
                                                                    'lokasi'        => $waste_location,
                                                                    'lot'           => $lot_waste_rm,
                                                                    'lokasi_fisik'  => '',
                                                                    'nama_grade'    => 'F'

                                                );
                                                array_push($data_update_rm_sq,$update_rm_sq);

                                                // fg hasil
                                                $data_array_mrp_fg_hasil[] =  array(
                                                                        'kode'          => $kode_mg,
                                                                        'move_id'       => $move_id_fg,
                                                                        'create_date'   => $tgl,
                                                                        'quant_id'      => $quant_id_inlet,
                                                                        'kode_produk'   => $kode_produk_rm,
                                                                        'nama_produk'   => $nama_produk_rm,
                                                                        'lot'           => $lot_waste_rm,
                                                                        'nama_grade'    => 'F',
                                                                        'qty'           => $hph_mtr,
                                                                        'uom'           => $uom_1_rm,
                                                                        'qty2'          => $hph_kg,
                                                                        'uom2'          => $uom_2_rm,
                                                                        'lokasi'        => $waste_location,
                                                                        'lebar_jadi'    => $lebr_jadi_rm,
                                                                        'uom_lebar_jadi'=> $uom_lebar_jadi_rm,
                                                                        'lebar_greige'  => $lebar_greige_rm,
                                                                        'uom_lebar_greige' => $uom_lebar_greige_rm,
                                                                        'sales_order'   => $sales_order,
                                                                        'sales_group'   => $kode_sales_group,
                                                                        'consume'       => 'yes',
                                                                        'nama_user'     => $nama_user['nama'],
                                                                        'row_order'     => $row_order,
                                                                        'id_inlet'      => $id_inlet
                                                );
                                            }else{
                                                $lokasi_waste_empty = true;
                                            }
                                        }else{
                                            $waste_not_valid = true;
                                        }

                                    }else{
                                        $rm_not_valid = true;
                                    }

                                }else{
                                    $rm_not_valid = true;
                                }
                                
                            }else{
                                if($grade == 'F' or empty($grade)){
                                    $hasil          = $this->m_outlet->get_mrp_production_fg_hasil_by_kode($inlet->kode_mrp,$inlet->id)->row();

                                    if(!empty($hasil)){
                                        $sisa_qty       = $inlet->qty - $hasil->total_qty;
                                        $sisa_qty2      = $inlet->qty2 - $hasil->total_qty2;
                                    }else{
                                        $sisa_qty       = $inlet->qty;
                                        $sisa_qty2      = $inlet->qty2;
                                    }

                                    if($sisa_qty <= 0 AND $sisa_qty2 <= 0){
                                        $grade_not_valid = true;
                                    }
                                }
                            }

                            // ** FINISH KONSUMSI BAHAN BAKU  **\\
                            

                            if($rm_not_valid == false AND $waste_not_valid == false AND $lokasi_waste_empty == false AND $grade_not_valid == false AND $qty_not_same == false){

                                // save mrp production fg_hasil
                                if(!empty($data_array_mrp_fg_hasil)){
                                    $this->m_outlet->save_mrp_production_fg_hasil_batch($data_array_mrp_fg_hasil);
                                }

                                // save stock quant
                                if(!empty($data_array_stock_quant)){
                                    $this->m_outlet->save_stock_quant_batch($data_array_stock_quant);
                                }
                                
                                // // save mrp stock_move items
                                if(!empty($data_array_stock_move_items)){
                                    $this->m_outlet->save_stock_move_items_batch($data_array_stock_move_items);
                                }

                                // // save mrp satuan jual
                                if(!empty($data_satuan_jual)){
                                    $this->m_outlet->save_mrp_satuan_batch($data_satuan_jual);
                                }

                                //update stock quant
                                if(!empty($data_update_rm_sq)){
                                    // print_r($data_update_rm_sq);
                                    $resullt1 = $this->m_outlet->update_stock_quant_by_kode($data_update_rm_sq);
                                }

                                // update stock_move_items
                                if(!empty($update_rm_smi) and $update_rm_smi_where){
                                    $this->m_outlet->update_data_in_table_by_kode('stock_move_items',$update_rm_smi,$update_rm_smi_where);
                                }

                                // save mrp rm hasil
                                if(!empty($data_array_mrp_rm_hasil)){
                                    $this->m_outlet->save_mrp_rm_hasil_batch($data_array_mrp_rm_hasil);
                                }

                               
                                // GET SISA QTY
                                // $dept      = $this->_module->get_nama_dept_by_kode('GJD')->row_array();
                                $hasil          = $this->m_outlet->get_mrp_production_fg_hasil_by_kode($inlet->kode_mrp,$inlet->id)->row();
                                if(!empty($hasil) ){
                                    $sisa_qty       = $inlet->qty - $hasil->total_qty;
                                    $sisa_qty2      = $inlet->qty2 - $hasil->total_qty2;
                                    $potka_next     = $hasil->jml_lot + 1;                                 
                                  
                                }else{
                                    $sisa_qty       = $inlet->qty;
                                    $sisa_qty2      = $inlet->qty2;
                                    $potka_next     = 1;
                                }
                                $data_sisa   = array(
                                                    'qty'   => round($sisa_qty,2), 
                                                    'uom'   => $inlet->uom, 
                                                    'qty2'  => round($sisa_qty2,2),
                                                    'uom2'  => $inlet->uom2, 
                                                    'potongan_ke' => $potka_next
                                );

                                ///cek qty sudah produksi sudah memenuhi atau belum ?
                                $qty_target = $this->m_mo->get_qty_mrp_production_fg_target($inlet->kode_mrp)->row_array();

                                $qty_hasil  = $this->m_mo->get_qty_mrp_production_fg_hasil($inlet->kode_mrp)->row_array();

                                if($qty_hasil['sum_qty'] >= $qty_target['qty']){
                                    $this->m_mo->update_status_mrp_production_fg_target($inlet->kode_mrp,'done');
                                    $this->_module->update_status_stock_move($qty_target['move_id'],'done');
                                    //update stock_move_produk fg_target
                                    $sql_update_status_stock_move_produk_fg_target = "UPDATE stock_move_produk SET status = 'done' Where move_id = '".$qty_target['move_id']."'";
                                    $this->_module->update_perbatch($sql_update_status_stock_move_produk_fg_target); 
                                }
                                
                                if($potka_next > 1){
                                    // update status = process
                                    $status = 'process';
                                    $nama_status = "Status : Process";
                                    $update_status = array('status'=>$status);
                                    $this->m_inlet->update_data_inlet($id_inlet,$update_status);
                                    $tipe_alert = 'info_status_process';
                                }
                                
                                if(!empty($hasil) ){
                                    $update_rm_target       = array();
                                    $where_update_rm_target = array();
                                    $update_rm_smp          = array();
                                    $update_rm_smp_where    = array();
                                    // cek jml stock move items yg status nya ready
                                    $cek_smi = $this->m_mo->cek_qty_stock_move_items_by_produk($move_id_rm,addslashes($origin_prod_rm),'ready')->row_array();
                                    if(empty($cek_smi['jml_qty'])){
                                        // cek yg status nya done
                                        $cek_smi2 = $this->m_mo->cek_qty_stock_move_items_by_produk($move_id_rm,addslashes($origin_prod_rm),'done')->row_array();
                                        if($cek_smi2['jml_qty'] < $qty_rm_target){
                                            //update status barang jadi draft
                                            //mrp production rm target
                                            $update_rm_target  = array('status' => 'draft' );
                                            $where_update_rm_target = array(
                                                                'origin_prod'   => $origin_prod_rm,
                                                                'move_id'       => $move_id_rm,
                                                                'kode'          => $kode_mg  );

                                            // staock_move_produk
                                            $update_rm_smp  = array('status'    => 'draft');
                                            $update_rm_smp_where = array(
                                                                'origin_prod' => $origin_prod_rm,
                                                                'move_id'  => $move_id_rm);

                                        }else if($cek_smi2['jml_qty'] >= $qty_rm_target){

                                            //mrp production rm target
                                            $update_rm_target  = array('status' => $status_done);

                                            $where_update_rm_target = array(
                                                                'origin_prod'   => $origin_prod_rm,
                                                                'move_id'       => $move_id_rm,
                                                                'kode'          => $kode_mg);

                                            // staock_move_produk
                                            $update_rm_smp  = array('status'    => $status_done);
                                            $update_rm_smp_where = array(
                                                                'origin_prod' => $origin_prod_rm,
                                                                'move_id'  => $move_id_rm);
                                        }
                                    }

                                    // update mrp rm target
                                    if(!empty($update_rm_target) and $where_update_rm_target){
                                        $this->m_outlet->update_data_in_table_by_kode('mrp_production_rm_target',$update_rm_target,$where_update_rm_target);
                                    }

                                    // update smp target
                                    if(!empty($update_rm_smp) and $update_rm_smp_where){
                                        $this->m_outlet->update_data_in_table_by_kode('stock_move_produk',$update_rm_smp,$update_rm_smp_where);
                                    }

                                }

                                if($print && ($grade == 'A' or $grade == 'B' or $grade == 'C')){
                                    $data_print = $this->print_barcode_gjd($barcode_id,$desain_barcode,$corak_remark_fix,$warna_remark_fix,$lebar_jadi_label,$uom_lebar_jadi_label_barcode,$qty_label,$uom_label_barcode,$qty2_label,$uom2_label_barcode,$tgl,$kode_mg,$k3l);
                                }else{
                                    $data_print = '';
                                }

                                $jenis_log = "edit";
                                $note_log  = "HPH OUTLET ".$kode_mg." -> ".$lot_inlet." | LOT : ".$barcode_id;
                                $data_history = array(
                                                'datelog'   => date("Y-m-d H:i:s"),
                                                'kode'      => $kode_mg,
                                                'jenis_log' => $jenis_log,
                                                'note'      => $note_log  );
                                
                                // load in library
                                $this->_module->gen_history_ip_deptid('mO',$username,$data_history,'GJD');

                                $callback = array('status' => 'success', 'message' => 'Data Behasil Disimpan !', 'icon' =>'fa fa-check', 'type' => 'success', 'sisa_target'=>$data_sisa, 'data_print' =>$data_print, 'nama_status' => $nama_status, 'tipe_alert' => $tipe_alert );

                            }else{

                                if($rm_not_valid){
                                    $callback = array('status' => 'failed', 'message'=>'Maaf, Data Gagal Disimpan, Bahan Baku tidak Valid !', 'icon' => 'fa fa-warning', 'type'=>'danger');
                                }else if($qty_not_same){
                                    $callback = array('status' => 'failed', 'message'=>'Maaf, Qty tidak sama !', 'icon' => 'fa fa-warning', 'type'=>'danger');
                                }else if($lokasi_waste_empty){
                                    $callback = array('status' => 'failed', 'message'=>'Maaf, Data Gagal Disimpan, Lokasi Waste Tidak ditemukan !', 'icon' => 'fa fa-warning', 'type'=>'danger');
                                }else if($waste_not_valid){
                                    $callback = array('status' => 'failed', 'message'=>'Maaf, Data Gagal Disimpan, Waste Tidak Valid !', 'icon' => 'fa fa-warning', 'type'=>'danger');
                                }else if($grade_not_valid){
                                    $callback = array('status' => 'failed', 'message'=>'Maaf, Qty Waste tidak boleh lebih dari target !', 'icon' => 'fa fa-warning', 'type'=>'danger');
                                }else{
                                    $callback = array('status' => 'failed', 'message'=>'Maaf, Data Gagal Disimpan !', 'icon' => 'fa fa-warning', 'type'=>'danger');
                                }


                            }
                            
    
                        }


                    }

                    // unlock table
                    // $this->_module->unlock_tabel();

                }

                if (!$this->_module->finishTransaction()) {
                    throw new \Exception('Gagal Menyimpan Data', 500);
                }
   
                $this->output->set_status_header(200)
                        ->set_content_type('application/json', 'utf-8')->set_output(json_encode($callback));
            }

        }catch(Exception $ex){
            $this->_module->rollbackTransaction();
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }


    function done_hph()
    {
        try{
            if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
                // session habis
                throw new \Exception('Waktu Anda Telah Habis', 401);
            }else{
                $username = addslashes($this->session->userdata('username')); 
                $id_inlet  = $this->input->post('id');

                $inlet     = $this->m_inlet->get_data_inlet_by_id($id_inlet);

                // start transaction
                $this->_module->startTransaction();

                // get data inlet
                if(empty($inlet->id)){
                    $callback = array('status' => 'failed', 'message' => 'Data KP/Lot Inlet tidak ditemukan !', 'icon' =>'fa fa-warning', 'type' => 'danger');
                }else if($inlet->status == 'done'){
                    $callback = array('status' => 'failed', 'message' => 'Data KP/Lot Inlet sudah Done  !', 'icon' =>'fa fa-warning', 'type' => 'danger');
                }else if($inlet->status == 'cancel'){
                    $callback = array('status' => 'failed', 'message' => 'Data KP/Lot Inlet Cancel / dibatalkan !', 'icon' =>'fa fa-warning', 'type' => 'danger');
                }else{  

                    $kode_mg    = $inlet->kode_mrp;
                    $data_mrp   = $this->m_outlet->get_mrp_production_by_kode($kode_mg);
                    $lot_inlet  = $inlet->lot;
                    $quant_id_inlet     = $inlet->quant_id;

                    $cek_smi_rm = $this->m_outlet->cek_stock_move_items_by_kode($kode_mg,$quant_id_inlet,$lot_inlet)->row();
                    $cek_smi_rm_status = $cek_smi_rm->status ?? '';
                    // cek lokasi quant id apakah waste 
                    $sq     = $this->_module->get_stock_quant_by_id($quant_id_inlet)->row();
                    $sq_ex  = explode("/", $sq->lokasi);
                    $waste_lokasi = $sq_ex[1] ?? '';
                    $is_waste_lokasi = false;
                    if($waste_lokasi == "Waste"){
                        $is_waste_lokasi = true;
                    }

                    $total_hph = $this->m_inlet->get_total_hph_by_lot($id_inlet);

                    if(empty($data_mrp)){
                        $callback = array('status' => 'failed', 'message'=> 'Data KP/Lot Inlet tidak ditemukan !', 'icon' =>'fa fa-warning', 'type' => 'danger');
                    }else if($data_mrp->status == 'done'){
                        $callback = array('status' => 'failed', 'message'=>'Maaf, Data Tidak Bisa Disimpan, Status MG Sudah Done !', 'icon' => 'fa fa-warning', 'type'=>'danger');
                    }else if($data_mrp->status == 'draft'){
                        $callback = array('status' => 'failed', 'message'=>'Maaf, Data Tidak Bisa Disimpan, Status MG Masih Draft !', 'icon' => 'fa fa-warning', 'type'=>'danger');
                    }else if($data_mrp->status == 'cancel'){
                        $callback = array('status' => 'failed', 'message'=>'Maaf, Data Tidak Bisa Disimpan, Status MG Batal !', 'icon' => 'fa fa-warning', 'type'=>'danger');
                    }else if($data_mrp->status == 'hold'){
                        $callback = array('status' => 'failed', 'message'=>'Maaf, Data Tidak Bisa Disimpan, Status MG di Hold !', 'icon' => 'fa fa-warning', 'type'=>'danger');
                    }else if(empty($cek_smi_rm) AND $is_waste_lokasi == false){
                        $callback = array('status' => 'failed', 'message'=>'Maaf, Stock Move Bahan Baku tidak ditemukan !'.$waste_lokasi, 'icon' => 'fa fa-warning', 'type'=>'danger');
                    }else if(($cek_smi_rm_status == 'ready' OR $total_hph->qty > $total_hph->hasil_qty or $total_hph->qty2 > $total_hph->hasil_qty2) AND $is_waste_lokasi == false ){
                        $callback = array('status' => 'failed', 'message'=>'Maaf, Target Bahan Baku <b>'.$lot_inlet.'</b> belum habis!', 'icon' => 'fa fa-warning', 'type'=>'danger');
                    }else if(($cek_smi_rm_status == 'draft') AND $is_waste_lokasi == false){
                        $callback = array('status' => 'failed', 'message'=>'Maaf, Bahan Baku <b>'.$lot_inlet.'</b> belum ready!', 'icon' => 'fa fa-warning', 'type'=>'danger');
                    }else if(($cek_smi_rm_status == 'cancel') AND $is_waste_lokasi == false){
                        $callback = array('status' => 'failed', 'message'=>'Maaf, Bahan Baku <b>'.$lot_inlet.'</b> di cancel!', 'icon' => 'fa fa-warning', 'type'=>'danger');
                    }else{

                        $tgl            = date("Y-m-d H:i:s");

                        $data_update = array('status' => 'done');
                        $data_where = array('id' => $id_inlet);
                        $result  = $this->m_outlet->update_data_in_table_by_kode('mrp_inlet',$data_update,$data_where);

                        if($result > 0 ){
                            $jenis_log = "edit";
                            $note_log  = "Done HPH ".$kode_mg." -> ".$lot_inlet;
                            $data_history = array(
                                            'datelog'   => date("Y-m-d H:i:s"),
                                            'kode'      => $kode_mg,
                                            'jenis_log' => $jenis_log,
                                            'note'      => $note_log  );
                            
                            $this->_module->gen_history_ip_deptid('mO',$username,$data_history,'GJD');

                            $data_history = array(
                                            'datelog'   => date("Y-m-d H:i:s"),
                                            'kode'      => $id_inlet,
                                            'jenis_log' => $jenis_log,
                                            'note'      => $note_log  );
                            $this->_module->gen_history_ip('inlet',$username,$data_history);

                            
                            $callback = array('status' => 'success', 'message' => 'Data HPH Berhasil diselesaikan !', 'icon' =>'fa fa-check', 'type' => 'success');
                        }else{
                            $callback = array('status' => 'failed', 'message'=>'Maaf, done HPH Gagal !', 'icon' => 'fa fa-warning', 'type'=>'danger');
                        }

                        
                    }
                }

                if (!$this->_module->finishTransaction()) {
                    throw new \Exception('done HPH Gagal', 500);
                }
   
                $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($callback));

            }
        }catch(Exception $ex){
            $this->_module->finishTransaction();
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }

    public function check() {
        $data['id_dept']      = 'OUTLET';
        return $this->load->view('print/a1');
    }


    function print_barcode_gjd($lot,$desain_barcode,$corak,$warna,$lebar,$uom_lebar,$qty_jual,$uom_jual,$qty2_jual,$uom2_jual,$tanggal,$kode_mg,$k3l){

        $desain_barcode = strtolower($desain_barcode);
        $tanggal = date('Ymd', strtotime($tanggal));

        $code = new Code\Code128New();
        $gen_code = $code->generate($lot, "", 60, "vertical");
        $this->prints->setView('print/'.$desain_barcode);
        $this->prints->addData('pattern', $corak);
        $this->prints->addData('isi_color', !empty($warna)? $warna : '&nbsp');
        $this->prints->addData('isi_satuan_lebar', 'WIDTH ['.$uom_lebar.']');
        $this->prints->addData('isi_lebar', !empty($lebar)? $lebar : '&nbsp');
        $this->prints->addData('isi_satuan_qty1', 'QTY ['.$uom_jual.']');
        $this->prints->addData('isi_qty1', round($qty_jual,2));
        if(!empty($qty2_jual)){
            $this->prints->addData('isi_satuan_qty2', 'QTY2 ['.$uom2_jual.']');
            $this->prints->addData('isi_qty2', round($qty2_jual,2));
        }
        $this->prints->addData('barcode_id', $lot);
        $this->prints->addData('tanggal_buat', $tanggal);
        $this->prints->addData('no_pack_brc', $kode_mg);
        $this->prints->addData('barcode', $gen_code);
        $this->prints->addData('k3l', $k3l);
        return $this->prints->generate();

    }

    public function test() {
        try {
            $code = new Code\Code128New();
            $gen_code = $code->generate("A123456789", "", 60, "vertical");
            $this->prints->setView('print/a');
            $this->prints->addData('pattern', 'Test Printed');
            $this->prints->addData('isi_color', 'warna kuning matahari');
            $this->prints->addData('isi_satuan_lebar', 'WIDTH (cm)');
            $this->prints->addData('isi_lebar', '250x128');
            $this->prints->addData('isi_satuan_qty1', 'QTY Pnl');
            $this->prints->addData('isi_qty1', 16);
            $this->prints->addData('isi_satuan_qty2', 'QTY kg');
            $this->prints->addData('isi_qty2', 85);
            $this->prints->addData('barcode_id', 12312312);
            $this->prints->addData('tanggal_buat', date('y-m-d'));
            $this->prints->addData('no_pack_brc', 'MG12345');
            $this->prints->addData('barcode', $gen_code);
            $this->prints->addData('k3l','20-D-001740');
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success', 'data' => $this->prints->generate())));
        } catch (Exception $ex) {
            
        }
    }
}