<?php defined('BASEPATH') or exit ('No Direct Script Acces Allowed');

/**
 * 
 */
class Splitlot extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->is_loggedin();//cek apakah user sudah login
        $this->load->model("_module");
        $this->load->library("token");
        $this->load->model("m_splitLot");
        $this->load->model("m_inlet");
        $this->load->model("m_outlet");
        $this->load->library('prints');
        $this->load->library('barcode');
    }

    public function index()
    {
        $data['id_dept']   = 'SPLIT';
        $data['warehouse'] = $this->_module->get_list_departement();
        $this->load->view('warehouse/v_split_lot', $data);
    }

    public function get_data()
    {	
        if(isset($_POST['start']) && isset($_POST['draw'])){
            $list = $this->m_splitLot->get_datatables();
            $data = array();
            $no = $_POST['start'];
            foreach ($list as $field) {
                $kode_encrypt = encrypt_url($field->kode_split);
            
                $no++;
                $row = array();
                $row[] = $no;
                $row[] = '<a href="'.base_url('warehouse/splitlot/edit/'.$kode_encrypt).'">'.$field->kode_split.'</a>';
                $row[] = $field->tanggal;
                $row[] = $field->departemen;
                $row[] = $field->kode_produk;
                $row[] = $field->nama_produk;
                $row[] = $field->lot;
                $row[] = $field->qty.' '.$field->uom;
                $row[] = $field->qty2.' '.$field->uom2;
                $row[] = $field->jml_split;
                $row[] = $field->note;
    
                $data[] = $row;
            }
    
            $output = array(
                "draw" => $_POST['draw'],
                "recordsTotal" => $this->m_splitLot->count_all(),
                "recordsFiltered" => $this->m_splitLot->count_filtered(),
                "data" => $data,
            );
            //output dalam format JSON
            echo json_encode($output);

        }else{
            die();
        }
    }

    public function add()
	{ 
	    $data['id_dept']       = 'SPLIT';
        $data['kode_split']    = $this->m_splitLot->get_kode_split();
        $data['warehouse']     = $this->_module->get_list_departement();
        $data['list_uom']      = $this->_module->get_list_uom();
        $uom_konversi                   = $this->m_outlet->get_list_uom_konversi();
        $data['uom_konversi']           = json_encode($uom_konversi);   
	    return $this->load->view('warehouse/v_split_lot_add', $data);
	}

    public function edit($id = null)
    {
        if(!isset($id)) show_404();
            $kode_decrypt     = decrypt_url($id);
            $id_dept   		  = 'SPLIT';
            $data['id_dept']  = $id_dept;
            $data['mms']      = $this->_module->get_data_mms_for_log_history($id_dept);// get mms by dept untuk menu yg beda-beda
            $data["split"]          = $this->m_splitLot->get_data_split_by_kode($kode_decrypt);
            $data['split_items']    = $this->m_splitLot->get_data_split_items_by_kode($kode_decrypt);
        if(empty($data["split"])){
            show_404();
        }else{
            return $this->load->view('warehouse/v_split_lot_edit',$data);
        }

    }


    public function import_produk()
    {
        $departemen            = $this->input->post('departemen');
        $data['departemen']    = $departemen;
        return $this->load->view('modal/v_split_lot_import_modal',$data);
    }


    public function list_import_produk()
    {
        $departemen  = addslashes($this->input->post('departemen'));
        // get lokasi by dept id
        $get_lokasi = $this->_module->get_nama_dept_by_kode($departemen)->row_array();
        $lokasi     = $get_lokasi['stock_location'];
        if(isset($_POST['start']) && isset($_POST['draw'])){
            $list = $this->m_splitLot->get_datatables2($lokasi,$departemen);
            $data = array();
            $no = $_POST['start'];
            foreach ($list as $field) {
                $no++;
                $row = array();
                $row[] = $no.".";
                $row[] = $field->kode_produk;
                $row[] = $field->nama_produk;
                if($departemen == 'GJD'){
                    $row[] = $field->corak_remark;
                    $row[] = $field->warna_remark;
                }
                $row[] = $field->lot;
                $row[] = number_format($field->qty,2)." ".$field->uom;
                $row[] = number_format($field->qty2,2)." ".$field->uom2;
                if($departemen == 'GJD'){
                    $row[] = number_format($field->qty_jual,2)." ".$field->uom_jual;
                    $row[] = number_format($field->qty2_jual,2)." ".$field->uom2_jual;
                }
                $row[] = $field->nama_grade;
                $row[] = $field->lokasi_fisik;
                $row[] = $field->reff_note;
                $row[] = $field->reserve_move;
                if($departemen == 'GJD'){
                    $row[] = $field->nama_sales_group;
                }
                if($departemen == 'GJD'){
                    $row[] = '<a href="#" class="btn btn-primary btn-xs pilih" quant_id="'.$field->quant_id.'" kode_produk="'.$field->kode_produk.'"  nama_produk="'.htmlentities($field->nama_produk).'" corak_remark="'.htmlentities($field->corak_remark).'" warna_remark="'.htmlentities($field->warna_remark).'" lot ="'.$field->lot.'" qty="'.$field->qty.'" uom="'.$field->uom.'" qty2="'.$field->qty2.'" uom2="'.$field->uom2.'"  qty_jual="'.$field->qty_jual.'" uom_jual="'.$field->uom_jual.'"  qty2_jual="'.$field->qty2_jual.'" uom2_jual="'.$field->uom2_jual.'" lebar_jadi="'.$field->lebar_jadi.'"  uom_lebar_jadi="'.$field->uom_lebar_jadi.'" kode_sales_group="'.$field->sales_group.'" nama_sales_group="'.$field->nama_sales_group.'" data-togle="tooltip" title="Pilih Produk"><i  class="fa fa-check"></i> Pilih</a>';
                }else{
                    $row[] = '<a href="#" class="btn btn-primary btn-xs pilih" quant_id="'.$field->quant_id.'" kode_produk="'.$field->kode_produk.'"  nama_produk="'.htmlentities($field->nama_produk).'" lot ="'.$field->lot.'" qty="'.$field->qty.'" uom="'.$field->uom.'" qty2="'.$field->qty2.'" uom2="'.$field->uom2.'" data-togle="tooltip" title="Pilih Produk"><i  class="fa fa-check"></i> Pilih</a>';
                }
                $data[] = $row;
            }
            $output = array(
                "draw" => $_POST['draw'],
                "recordsTotal" => $this->m_splitLot->count_all2($lokasi,$departemen),
                "recordsFiltered" => $this->m_splitLot->count_filtered2($lokasi,$departemen),
                "data" => $data,
            );
            //output dalam format JSON
            echo json_encode($output);
        }else{
            die();
        }
    }

    public function generate_split()
    {
     
        try{
            if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
                // session habis
                throw new \Exception('Waktu Anda Telah Habis', 401);
            }else{
                
                $username  = addslashes($this->session->userdata('username')); 
                $nama_user = $this->_module->get_nama_user($username)->row_array();
                $sub_menu  = $this->uri->segment(2);

                $dept_id        = addslashes($this->input->post('departemen'));
                $kode_sales_group       = ($this->input->post('kode_sales_group'));
                $nama_sales_group       = ($this->input->post('nama_sales_group'));
                $quant_id       = ($this->input->post('quant_id'));
                $kode_produk    = ($this->input->post('kode_produk'));
                $nama_produk    = ($this->input->post('nama_produk'));
                $lot            = ($this->input->post('lot'));
                $qty            = ($this->input->post('qty'));
                $uom_qty        = ($this->input->post('uom_qty'));
                $qty2           = ($this->input->post('qty2'));
                $uom_qty2       = ($this->input->post('uom_qty2'));
                $qty_jual       = ($this->input->post('qty_jual'));
                $uom_qty_jual   = ($this->input->post('uom_qty'));
                $qty2_jual      = ($this->input->post('qty2_jual'));
                $uom_qty2_jual  = ($this->input->post('uom_qty2'));
                $departemen     = ($this->input->post('departemen'));
                $note           = addslashes($this->input->post('note'));
                $array_split    = json_decode($this->input->post('data_split'),true); 
                $tgl            = date('Y-m-d H:i:s');
                $corak_remark     = ($this->input->post('corak_remark'));
                $warna_remark     = ($this->input->post('warna_remark'));
                $lebar_jadi       = ($this->input->post('lebar_jadi'));
                $uom_lebar_jadi   = ($this->input->post('uom_lebar_jadi'));


                //start transaction
                $this->_module->startTransaction();

                // lock table
                $this->_module->lock_tabel('stock_quant WRITE, stock_move WRITE, stock_move_produk WRITE, stock_move_items WRITE, adjustment WRITE, adjustment_items WRITE, split WRITE, split_items WRITE, departemen as d WRITE, picklist_detail WRITE, token_increment WRITE, user WRITE, log_history WRITE, main_menu_sub WRITE');
                
                if(empty($dept_id)){
                    $callback = array('status' => 'failed', 'field' => 'departemen', 'message' => 'Departemen Harus dipilih !', 'icon' =>'fa fa-warning',   'type' => 'danger' );
                }else if(empty($kode_produk)){
                    $callback = array('status' => 'failed', 'field' => 'kode_produk', 'message' => 'Kode Produk tidak boleh kosong !', 'icon' =>'fa fa-warning',   'type' => 'danger' );
                }else if(empty($nama_produk)){
                    $callback = array('status' => 'failed', 'field' => 'nama_produk', 'message' => 'Nama Produk tidak boleh kosong !', 'icon' =>'fa fa-warning',   'type' => 'danger' );
                }else if(empty($lot)){
                    $callback = array('status' => 'failed', 'field' => 'lot', 'message' => 'Lot tidak boleh kosong !', 'icon' =>'fa fa-warning',   'type' => 'danger' );
                }else if(empty($qty)){
                    $callback = array('status' => 'failed', 'field' => 'qty', 'message' => 'Qty1 tidak boleh kosong !', 'icon' =>'fa fa-warning',   'type' => 'danger' );
                }else if(empty($uom_qty)){
                    $callback = array('status' => 'failed', 'field' => 'uom_qty', 'message' => 'Uom Qty1 tidak boleh kosong !', 'icon' =>'fa fa-warning',   'type' => 'danger' );
                }else if(empty($array_split)){
                    $callback = array('status' => 'failed', 'field' => 'kode_produk', 'message' => 'Split Items masih Kosong !', 'icon' =>'fa fa-warning',   'type' => 'danger' );
                }else if(count($array_split) == 1){
                    $callback = array('status' => 'failed', 'field' => 'kode_produk', 'message' => 'Barcode / Lot yang akan di Split harus lebih dari 1 items !', 'icon' =>'fa fa-warning',   'type' => 'danger' );
                }else{

                    // cek stock quant
                    $sq = $this->_module->get_stock_quant_by_id($quant_id)->row_array();

                    // cek lokasi sock by dept 
                    $cek_lc = $this->_module->get_nama_dept_by_kode($departemen)->row_array();

                    // cek barcode di picklist 
                    $cek_pl = $this->m_splitLot->cek_picklist_by_lot($quant_id,$lot);

                    if(empty($sq['quant_id'])){
                        $callback = array('status' => 'failed', 'field' => 'kode_produk', 'message' => 'Data yang akan di Split tidak ditemukan !', 'icon' =>'fa fa-warning',   'type' => 'danger' );
                    }else if($sq['lokasi'] != $cek_lc['stock_location']){/// cek lokasi
                        $callback = array('status' => 'failed', 'field' => 'kode_produk',  'message' => 'Lokasi Produk tidak sama / sudah tidak lagi di Lokasi '.$cek_lc['stock_location'].' !', 'icon' =>'fa fa-warning',   'type' => 'danger' );
                    }else if(!empty($sq['reserve_move'])){/// cek apa terpesan oleh dokumen lain atau tidak
                        $callback = array('status' => 'failed', 'field' => 'kode_produk',  'message' => 'Produk sudah terpesan oleh dokumen lain !', 'icon' =>'fa fa-warning',   'type' => 'danger' );
                    }else if($sq['qty'] != $qty){// cek qty yg di post dan yg ter baru
                        $callback = array('status' => 'failed',  'field' => 'qty', 'message' => 'Qty Produk tidak sama dengan Stock yang sekarang !', 'icon' =>'fa fa-warning',   'type' => 'danger' );
                    }else if($sq['qty2'] != 0 AND $sq['qty2'] != $qty2){// cek qty2 yg di post dan yg ter baru
                        $callback = array('status' => 'failed', 'field' => 'qty2',  'message' => 'Qty2 Produk tidak sama Stock yang sekarang !', 'icon' =>'fa fa-warning',   'type' => 'danger' );
                    }else if($sq['lokasi_fisik'] == "XPD" AND $departemen == "GJD"){
                        $callback = array('status' => 'failed', 'field' =>'',  'message' => 'Lokasi Barcode/Lot Sudah XPD !', 'icon' =>'fa fa-warning',   'type' => 'danger' );
                    }else if(!empty($cek_pl)){
                        $callback = array('status' => 'failed', 'field' =>'',  'message' => 'Barcode/Lot Sudah Masuk PL !', 'icon' =>'fa fa-warning',   'type' => 'danger' );
                    }else{

                        $sql_insert_items   = "";
                        $sql_adjustment     = "";
                        $sql_adjustment_items = "";
                        $sql_stock_move_batch = "";
                        $sql_stock_move_produk_batch = "";
                        $sql_stock_move_items_batch  = "";
                        $sql_stock_quant_batch = "";
                        $sql_log_history_batch = "";
                        $row_order          = 1;
                        $lot_baru           = $lot;
                        $items_empty        = true;
                        $jumlah_split       = 0;
                        $status_done        = "done";

                        // get kode split
                        $kode_split = $this->m_splitLot->get_kode_split();
                        // get move_id
                        $last_move   = $this->_module->get_kode_stock_move();
                        $move_id     = "SM".$last_move; //Set kode stock_move
                        // get quant_id
                        $start       = $this->_module->get_last_quant_id();
                        // get kode adj
                        $get_kode_adjustment   = $this->_module->get_kode_adj();  


                        $nama_departemen = $cek_lc['nama'];
                        $lokasi_adj      = $cek_lc['adjustment_location'];
                        $lokasi_stock    = $cek_lc['stock_location'];

                        // ADJ OUT
                        $kode_adjustment   = substr("0000" . $get_kode_adjustment,-4);                  
                        $kode_adjustment   = "ADJ/".date("y") . '/' .  date("m") . '/' . $kode_adjustment;

                        $note_adj_in  = 'ADJ | Dibuat dari Fitur Split. No.'.$kode_split;

                        // insert into adj 
                        $type_adjustment = 6; // 6 = split
                        
                        // $sql_adjustment .= "('".$kode_adjustment."', '".$tgl."','".$nama_departemen."','".$lokasi_stock."','".$note_adj_in."','".$status_done."','".$nama_user['nama']."', '".$type_adjustment."'), ";

                        // $method         = $departemen.'|ADJ';
                        // $lokasi_dari    = $lokasi_stock;
                        // $lokasi_tujuan  = $lokasi_adj;

                        // $qty1_move = 0 - $qty;
                        // $qty2_move = 0 - $qty2;

                        // // ADJ OUT
                        // // insert to adj items
                        // $sql_adjustment_items .= "('".$kode_adjustment."','".$quant_id."','".$kode_produk."','".$lot."','".$uom_qty."','".$qty."',0,'".$uom_qty2."','".$qty2."',0,'".$move_id."','".$qty1_move."','".$qty2_move."',$row_order), ";

                        // $origin_out      = $kode_adjustment.'|'.$row_order;

                        // // stock_move ADJ OUT
                        // $sql_stock_move_batch .= "('".$move_id."','".$tgl."','".$origin_out."','".$method."','".$lokasi_dari."','".$lokasi_tujuan."','".$status_done."','1',''), ";

                        // // insert stock move produk
                        // $sql_stock_move_produk_batch .= "('".$move_id."','".($kode_produk)."','".($nama_produk)."','".$qty."','".$uom_qty."','".$status_done."','1',''), ";
                        
                        // insert stock_move_items
                        // $sql_stock_move_items_batch .= "('".$move_id."', '".$quant_id."','".($kode_produk)."', '".($nama_produk)."','".$lot."','".$qty."','".($uom_qty)."','".$qty2."','".$uom_qty2."','".$status_done."','1','','".$tgl."','','".addslashes($sq['lebar_greige'])."','".addslashes($sq['uom_lebar_greige'])."','".addslashes($sq['lebar_jadi'])."','".addslashes($sq['uom_lebar_jadi'])."'), ";
                        
                        // $last_move = $last_move + 1;
                        // $move_id   = "SM".$last_move;
                        // $row_order++;

                        

                        // cek total split qty 1 dan qty2 terhadapt qty1 dan qty2 sebelum split
                        $sum_tbl_qty1 = 0;
                        $sum_tbl_qty2 = 0;
                        $sum_tbl_qty1_jual = 0;
                        $sum_tbl_qty2_jual = 0;
                        foreach($array_split as $row){

                            $sum_tbl_qty1 = $sum_tbl_qty1 + (double) $row['qty1'];
                            $sum_tbl_qty2 = $sum_tbl_qty2 + (double) $row['qty2'];
                            // break;
                            // $sum_tbl_qty1_jual = $sum_tbl_qty1_jual + $row['qty1_jual'];
                            // $sum_tbl_qty2_jual = $sum_tbl_qty2_jual + $row['qty2_jual'];
                            
                        }
                        
                        if(round($sum_tbl_qty1,2) != round($qty,2)){
                            $callback = array('status' => 'failed', 'field' => 'qty',  'message' => 'Qty1 Produk tidak sama dengan Qty1 yang akan di Split !', 'icon' =>'fa fa-warning',   'type' => 'danger' );
                        }else if(round($sum_tbl_qty2,2) != round($qty2,2)){
                            $callback = array('status' => 'failed', 'field' => 'qty2',  'message' => 'Qty2 Produk tidak sama dengan Qty2 yang akan di Split !', 'icon' =>'fa fa-warning',   'type' => 'danger' );
                        }else{
                            $sql_insert_items   = "";
                            $sql_adjustment     = "";
                            $sql_adjustment_items = "";
                            $sql_stock_move_batch = "";
                            $sql_stock_move_produk_batch = "";
                            $sql_stock_move_items_batch  = "";
                            $sql_stock_quant_batch = "";
                            $sql_log_history_batch = "";
                            $row_order          = 1;
                           
                            $items_empty        = true;
                            $jumlah_split       = 0;
                            $status_done        = "done";

                            $data_insert_items = array();// split items
                            $data_adj          = array();
                            $data_adj_items    = array();
                            $data_stock_quant  = array();

                            // get kode split
                            $kode_split = $this->m_splitLot->get_kode_split();
                            // get move_id
                            $last_move   = $this->_module->get_kode_stock_move();
                            $move_id     = "SM".$last_move; //Set kode stock_move
                            // get quant_id
                            $start       = $this->_module->get_last_quant_id();
                            // get kode adj
                            $get_kode_adjustment   = $this->_module->get_kode_adj();  

                            $nama_departemen = $cek_lc['nama'];
                            $lokasi_adj      = $cek_lc['adjustment_location'];
                            $lokasi_stock    = $cek_lc['stock_location'];

                            // ADJ OUT
                            $kode_adjustment   = substr("0000" . $get_kode_adjustment,-4);                  
                            $kode_adjustment   = "ADJ/".date("y") . '/' .  date("m") . '/' . $kode_adjustment;

                            $note_adj_in  = 'ADJ | Dibuat dari Fitur Split. No.'.$kode_split;

                            // insert into adj 
                            $type_adjustment = 6; // 6 = split
                            // $sql_adjustment .= "('".$kode_adjustment."', '".$tgl."','".$nama_departemen."','".$lokasi_stock."','".$note_adj_in."','".$status_done."','".$nama_user['nama']."', '".$type_adjustment."'), ";

                            $data_adj[] = array('kode_adjustment' => $kode_adjustment,
                                                'create_date'       => $tgl,
                                                'lokasi_adjustment' => $nama_departemen,
                                                'kode_lokasi'       => $lokasi_stock,
                                                'note'              => $note_adj_in,
                                                'status'            => $status_done,
                                                'nama_user'         => $nama_user['nama'],
                                                'id_type_adjustment'=> $type_adjustment);

                            $method         = $departemen.'|ADJ';
                            $lokasi_dari    = $lokasi_stock;
                            $lokasi_tujuan  = $lokasi_adj;

                            $qty1_move = 0 - $qty;
                            $qty2_move = 0 - $qty2;

                            // ADJ OUT
                            // insert to adj items
                            // $sql_adjustment_items .= "('".$kode_adjustment."','".$quant_id."','".$kode_produk."','".$lot."','".$uom_qty."','".$qty."',0,'".$uom_qty2."','".$qty2."',0,'".$move_id."','".$qty1_move."','".$qty2_move."',$row_order), ";
                            $data_adj_items[] = array('kode_adjustment'   => $kode_adjustment,
                                                        'quant_id'          => $start,
                                                        'kode_produk'       => $kode_produk,
                                                        'lot'               => $lot,
                                                        'uom'               => $uom_qty,
                                                        'qty_data'          => $qty,
                                                        'qty_adjustment'    => 0,
                                                        'uom2'              => $uom_qty2,
                                                        'qty_data2'         => $qty2,
                                                        'qty_adjustment2'   => 0,
                                                        'move_id'           => $move_id,
                                                        'qty_move'          => $qty1_move,
                                                        'qty2_move'         => $qty2_move,
                                                        'row_order'         => $row_order);

                            $origin_out      = $kode_adjustment.'|'.$row_order;

                            // stock_move ADJ OUT
                            $sql_stock_move_batch .= "('".$move_id."','".$tgl."','".$origin_out."','".$method."','".$lokasi_dari."','".$lokasi_tujuan."','".$status_done."','1',''), ";

                            // insert stock move produk
                            $sql_stock_move_produk_batch .= "('".$move_id."','".addslashes($kode_produk)."','".addslashes($nama_produk)."','".$qty."','".$uom_qty."','".$status_done."','1',''), ";
                            
                            // insert stock_move_items
                            $sql_stock_move_items_batch .= "('".$move_id."', '".$quant_id."','".addslashes($kode_produk)."', '".addslashes($nama_produk)."','".$lot."','".$qty."','".($uom_qty)."','".$qty2."','".$uom_qty2."','".$status_done."','1','','".$tgl."','','".addslashes($sq['lebar_greige'])."','".addslashes($sq['uom_lebar_greige'])."','".addslashes($sq['lebar_jadi'])."','".addslashes($sq['uom_lebar_jadi'])."'), ";
                            
                            $last_move = $last_move + 1;
                            $move_id   = "SM".$last_move;
                            $row_order++;

                            $lokasi_dari    = $lokasi_adj;
                            $lokasi_tujuan  = $lokasi_stock;

                            $lokasi_fisik      = '';
                            if($dept_id == 'GJD'){
                                $lokasi_fisik = "PORT";
                            }
                            
                            // insert to split items
                            foreach($array_split as $row){
                                $items_empty = false;
                                //$sql_insert_items .= "('".$kode_split."','".$start."','".$row['qty1']."','".$row['uom_qty1']."','".$row['qty2']."','".$row['uom_qty2']."','".$lot_baru."','".$row_order."' ), ";
                                if($dept_id == 'GJD'){
                                    $lot_baru    = $this->token->noUrut('split_gjd', date('my'), true)->generate('S', '%05d')->get();
                                }else{
                                    $lot_baru    = $lot;
                                }

                                $data_insert_items[] = array(
                                                        'kode_split'        => $kode_split,
                                                        'quant_id_baru'     => $start,
                                                        'corak_remark'      => trim($row['corak_remark'] ?? ''),
                                                        'warna_remark'      => trim($row['warna_remark'] ?? ''),
                                                        'qty'               => $row['qty1'],
                                                        'uom'               => $row['uom_qty1'],
                                                        'qty2'              => $row['qty2'],
                                                        'uom2'              => $row['uom_qty2'],
                                                        'qty_jual'          => $row['qty1_jual'] ?? 0,
                                                        'uom_jual'          => $row['uom_qty1_jual'] ?? '',
                                                        'qty2_jual'         => $row['qty2_jual'] ?? 0,
                                                        'uom2_jual'         => $row['uom_qty2_jual'] ?? '',
                                                        'lot_baru'          => $lot_baru,
                                                        'lebar_jadi'        => $row['lebar_jadi'] ?? '',
                                                        'uom_lebar_jadi'    => $row['uom_lebar_jadi'] ?? '',
                                                        'row_order'         => $row_order);
                                // ADJ IN
                                // $sql_adjustment_items .= "('".$kode_adjustment."','".$start."','".$kode_produk."','".$lot_baru."','".$row['uom_qty1']."',0,'".$row['qty1']."','".$row['uom_qty2']."',0,'".$row['qty2']."','".$move_id."','".$row['qty1']."','".$row['qty2']."',$row_order), ";
                                $data_adj_items[] = array('kode_adjustment'   => $kode_adjustment,
                                                        'quant_id'          => $start,
                                                        'kode_produk'       => $kode_produk,
                                                        'lot'               => $lot_baru,
                                                        'uom'               => $row['uom_qty1'],
                                                        'qty_data'          => 0,
                                                        'qty_adjustment'    => $row['qty1'],
                                                        'uom2'              => $row['uom_qty2'],
                                                        'qty_data2'         => 0,
                                                        'qty_adjustment2'   => $row['qty2'],
                                                        'move_id'           => $move_id,
                                                        'qty_move'          => $row['qty1'],
                                                        'qty2_move'         => $row['qty2'],
                                                        'row_order'         => $row_order);
                                

                                // move
                                $origin_in      = $kode_adjustment.'|'.$row_order;

                                // insert stock_move
                                $sql_stock_move_batch .= "('".$move_id."','".$tgl."','".$origin_in."','".$method."','".$lokasi_dari."','".$lokasi_tujuan."','".$status_done."','1',''), ";

                                // insert stock_move_produk
                                $sql_stock_move_produk_batch .= "('".$move_id."','".addslashes($kode_produk)."','".addslashes($nama_produk)."','".$row['qty1']."','".$row['uom_qty1']."','".$status_done."','1',''), ";

                                // insert stock_move_items
                                $sql_stock_move_items_batch .= "('".$move_id."', '".$start."','".addslashes($kode_produk)."', '".addslashes($nama_produk)."','".addslashes(trim($lot_baru))."','".$row['qty1']."','".($row['uom_qty1'])."','".$row['qty2']."','".$row['uom_qty2']."','".$status_done."','1','','".$tgl."','','".addslashes($sq['lebar_greige'])."','".addslashes($sq['uom_lebar_greige'])."','".addslashes($row['lebar_jadi'] ?? '')."','".addslashes($row['uom_lebar_jadi'] ?? '')."'), ";

                                // insert stock_quant
                                // $sql_stock_quant_batch .= "('".$start."','".$tgl."','".($kode_produk)."','".($nama_produk)."','".addslashes(trim($lot_baru))."','".addslashes($sq['nama_grade'])."','".$row['qty1']."','".$row['uom_qty1']."','".$row['qty2']."','".$row['uom_qty2']."','".$lokasi_stock."','".addslashes($sq['reff_note'])."','','','".$tgl."','".addslashes($sq['lebar_greige'])."','".addslashes($sq['uom_lebar_greige'])."','".addslashes($sq['lebar_jadi'])."','".addslashes($sq['uom_lebar_jadi'])."','".addslashes($sq['sales_order'])."','".addslashes($sq['sales_group'])."'), ";

                                $data_stock_quant[] = array('quant_id'  => $start,
                                                        'create_date'   => $tgl,
                                                        'move_date'     => $tgl,
                                                        'kode_produk'   => $kode_produk,
                                                        'nama_produk'   => $nama_produk,
                                                        'corak_remark'  => trim($row['corak_remark'] ?? ''),
                                                        'warna_remark'  => trim($row['warna_remark'] ?? ''),
                                                        'lot'           => trim($lot_baru),
                                                        'nama_grade'    => $sq['nama_grade'],
                                                        'qty'           => $row['qty1'],
                                                        'uom'           => $row['uom_qty1'],
                                                        'qty2'          => $row['qty2'],
                                                        'uom2'          => $row['uom_qty2'],
                                                        'qty_jual'      => $row['qty1_jual'] ?? 0,
                                                        'uom_jual'      => $row['uom_qty1_jual'] ?? '',
                                                        'qty2_jual'     => $row['qty2_jual'] ?? 0,
                                                        'uom2_jual'     => $row['uom_qty2_jual'] ?? '',
                                                        'lokasi'        => $lokasi_stock,
                                                        'lokasi_fisik'  => $lokasi_fisik,
                                                        'lebar_greige'  => ($sq['lebar_greige']),
                                                        'uom_lebar_greige'=> ($sq['uom_lebar_greige']),
                                                        'lebar_jadi'      => $row['lebar_jadi'] ?? '',
                                                        'uom_lebar_jadi'  => $row['uom_lebar_jadi'] ?? '',
                                                        'sales_order'     => ($sq['sales_order']),
                                                        'sales_group'     => ($sq['sales_group'])
                                );

                                $last_move = $last_move + 1;
                                $move_id   = "SM".$last_move;

                                $row_order++;
                                $jumlah_split++;
                                $start++;
                            }
                            

                            if($items_empty == false){

                                if(!empty($data_insert_items)){
                                    // insert to split
                                    $result1 = $this->m_splitLot->save_splitlot($kode_split,$tgl,$departemen,$quant_id,$kode_produk,addslashes($nama_produk),$lot,$qty,$uom_qty,$qty2,$uom_qty2,$qty_jual,$uom_qty_jual,$qty2_jual,$uom_qty2_jual,$note,addslashes($nama_user['nama']),addslashes($corak_remark),addslashes($warna_remark),$lebar_jadi,$uom_lebar_jadi,$kode_sales_group);
                                    if($result1['message'] != null){
                                        throw new \Exception('Simpan Data Split Gagal !', 200);                       
                                    }
                                    // $sql_insert_items = rtrim($sql_insert_items,', ');
                                    $result2 = $this->m_splitLot->save_split_items_batch($data_insert_items);
                                    if($result2['message'] != null){
                                        throw new \Exception('Simpan Data Split Gagal !', 200);                       
                                    }
                                }

                                if(!empty($data_adj)){
                                    // $sql_adjustment = rtrim($sql_adjustment, ', ');
                                    $result1 = $this->m_splitLot->simpan_adjustment_batch($data_adj);
                                    if($result1['message'] != null){
                                        throw new \Exception('Simpan Data Gagal !', 200);                       
                                    }
                                    if(!empty($data_adj_items)){
                                        // $sql_adjustment_items = rtrim($sql_adjustment_items, ', ');
                                        $result2 = $this->m_splitLot->simpan_adjustment_items_batch($data_adj_items);
                                        if($result2['message'] != null){
                                            throw new \Exception('Simpan Data Gagal !', 200);                       
                                        }
                                    }
                                }

                                // simpan stock move
                                if(!empty($sql_stock_move_batch)){
                                    $sql_stock_move_batch = rtrim($sql_stock_move_batch, ', ');
                                    $this->_module->create_stock_move_batch($sql_stock_move_batch);
                                }
                    
                                // simpan stock move items
                                if(!empty($sql_stock_move_items_batch)){
                                    $sql_stock_move_items_batch = rtrim($sql_stock_move_items_batch, ', ');
                                    $this->_module->simpan_stock_move_items_batch($sql_stock_move_items_batch);
                                }
                    
                                // simpan stock move produk
                                if(!empty($sql_stock_move_produk_batch)){
                                    $sql_stock_move_produk_batch = rtrim($sql_stock_move_produk_batch, ', ');
                                    $this->_module->create_stock_move_produk_batch($sql_stock_move_produk_batch);
                    
                                }
                    
                                // simpan stock quant
                                if(!empty($data_stock_quant)){
                                    // $sql_stock_quant_batch = rtrim($sql_stock_quant_batch, ', ');
                                    $result = $this->_module->simpan_stock_quant_batch_2($data_stock_quant);
                                    if($result['message'] != null){
                                        throw new \Exception('Simpan Data Gagal !', 200);                       
                                    }
                                    
                                }
                                

                                // update lokasi stock to adj qty lama 
                                $sql_update_lokasi_stock_quant = "UPDATE stock_quant SET lokasi = '$lokasi_adj', lokasi_fisik = '', move_date = '".$tgl."'  WHERE quant_id = '$quant_id' ";
                                $this->_module->update_reff_batch($sql_update_lokasi_stock_quant);

                                $kode_split_encr = encrypt_url($kode_split);

                                $jenis_log   = "create";
                                $note_log    = $kode_split." | ".$kode_produk."  ".$nama_produk." ".$lot." ".$qty." ".$uom_qty." ".$qty2." ".$uom_qty2." <br> Jumlah Split ".$jumlah_split;
                                $this->_module->gen_history($sub_menu, $kode_split, $jenis_log, $note_log, $username);

                                //create log history adjustment 
                                $note_log_adj_in = $kode_adjustment." ini dibuat dari Fitur Split Lot";
                                $date_log        = date('Y-m-d H:i:s');
                                $sql_log_history_batch .= "('".$date_log."','mms72','".$kode_adjustment."','create','".addslashes($note_log_adj_in)."','".$nama_user['nama']."'), ";

                                $total_adj = $jumlah_split + 1;

                                $note_log_adj_out_2 = "Generate Adjustment ini di generate otomatis dari Fitur Split Lot | Jumlah Adjustment  ".$total_adj;
                                $sql_log_history_batch .= "('".$date_log."','mms72','".$kode_adjustment."','generate','".addslashes($note_log_adj_out_2)."','".$nama_user['nama']."'), ";

                                if(!empty($sql_log_history_batch)){
                                    $sql_log_history_batch = rtrim($sql_log_history_batch, ', ');
                                    $this->_module->simpan_log_history_batch($sql_log_history_batch);
                                }
                                
                                $callback = array('status' => 'success','message' => 'Data Berhasil di Generate !', 'icon' =>'fa fa-check', 'type' => 'success', 'isi'=> $kode_split_encr) ;
                            }else{

                                $callback = array('status' => 'failed', 'field' => 'kode_produk',  'message' => 'Split Gagal di generate', 'icon' =>'fa fa-warning',   'type' => 'danger' );
                            }

                        }
        
                        // unlock table
                        // $this->_module->unlock_tabel();
                        
                    }

                }

                if (!$this->_module->finishTransaction()) {
                    throw new \Exception('Gagal Simpan data ', 500);
                }

                $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($callback));
            }

        }catch(Exception $ex){
            // finish transaction
            $this->_module->finishRollBack();
            $this->_module->rollbackTransaction();
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('status'=>'failed', 'field'=> '','message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }finally {
            // unlock table
            $this->_module->unlock_tabel();

        }
    }

    function edit_items_modal()
    {
        $kode               = $this->input->post('kode');
        $lot                = $this->input->post('lot');
        $data['kode']       = $kode;
        $data['data_items'] = $this->m_splitLot->get_data_split_items_by_lot($kode,$lot);
        return $this->load->view('modal/v_split_items_edit_modal',$data);
    }

    function save_split_items()
    {
        try{
            if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
                // session habis
                throw new \Exception('Waktu Anda Telah Habis', 401);
            }else{
                $kode           = $this->input->post('kode');
                $corak_remark   = $this->input->post('corak_remark');
                $warna_remark   = $this->input->post('warna_remark');
                $qty_jual       = $this->input->post('qty_jual');
                $uom_jual       = $this->input->post('uom_qty_jual');
                $qty2_jual      = $this->input->post('qty2_jual');
                $uom2_jual      = $this->input->post('uom_qty2_jual');
                $lebar_jadi     = $this->input->post('lebar_jadi');
                $uom_lebar_jadi = $this->input->post('uom_lebar_jadi');
                $lot            = $this->input->post('lot_new');
                $quant_id            = $this->input->post('quant_id');

                // start transaction
                $this->_module->startTransaction();

                // lock table
                $this->_module->lock_tabel('stock_quant WRITE, split WRITE, split_items WRITE, departemen as d WRITE, picklist_detail WRITE, token_increment WRITE, user WRITE, log_history WRITE, main_menu_sub WRITE, split as s WRITE, user_priv WRITE, mst_sales_group as msg WRITE');

                $sub_menu  = $this->uri->segment(2);
                $username = addslashes($this->session->userdata('username')); 

                $tgl            = date('Y-m-d H:i:s');
                $split           = $this->m_splitLot->get_data_split_by_kode($kode);

                if(empty($split)){
                    throw new \Exception('Data Split tidak ditemukan !', 200);
                }else{
                    $kode_menu       = $this->_module->get_kode_sub_menu_deptid($sub_menu,$split->dept_id)->row_array();
                    $akses_menu = $this->_module->cek_priv_menu_by_user($username,$kode_menu['kode'])->num_rows();
                    if($akses_menu > 0){
                        $callback = array('status' => 'failed', 'message' => 'Anda tidak mempunyai Akses untuk Menu ini !', 'icon' =>'fa fa-warning', 'type' => 'danger');
                    }else if(empty($corak_remark)){
                        $callback = array('status' => 'failed', 'message' => 'Corak Remark Harus diisi !', 'icon' => 'fa fa-warning' , 'type' => 'danger');
                    }else if(empty($qty_jual)){
                        $callback = array('status' => 'failed', 'message' => 'Qty Jual Harus diisi !', 'icon' => 'fa fa-warning' , 'type' => 'danger');
                    }else if(!empty($qty_jual) AND empty($uom_jual)){
                        $callback = array('status' => 'failed', 'message' => 'Uom Qty Jual Harus diisi !', 'icon' => 'fa fa-warning' , 'type' => 'danger');
                    }else{
                      
                            $cek_pl = $this->m_inlet->cek_barcode_in_picklist($quant_id,$lot)->row();
                            //get data stock by kode
                            $get = $this->_module->get_stock_quant_by_id($quant_id)->row();
                            if(empty($get) or empty($quant_id)){
                                $callback = array('status' => 'failed', 'message' => 'Data Lot'.$lot.' tidak ditemukan di Stock !', 'icon' => 'fa fa-warning' , 'type' => 'danger');
                            }else if($get->lokasi != 'GJD/Stock'){
                                $callback = array('status' => 'failed', 'message' => 'Lokasi tidak valid, Data Lot'.$lot.' berada dilokasi '.$get->lokasi ?? '' .' !', 'icon' => 'fa fa-warning' , 'type' => 'danger');
                            }else if($get->lokasi_fisik == 'XPD'){
                                $callback = array('status' => 'failed', 'message' => 'Lokasi Fisik sudah <b> XPD </b> ! ', 'icon' => 'fa fa-warning' , 'type' => 'danger');
                            }else if(!empty($cek_pl)){
                                $callback = array('status' => 'failed', 'message' => 'Data Lot '.$lot.' Sudah Masuk PL ! ', 'icon' => 'fa fa-warning' , 'type' => 'danger');
                            }else{
                                // cek row
                                $spli = $this->m_splitLot->get_data_split_items_by_lot($kode,$lot);
                                if(empty($spli)){
                                    throw new \Exception('Data Split Items tidak ditemukan !', 200);
                                }else{
                                    // get data quant sebelumnya
                                    $note_before = $get->corak_remark." | ".$get->warna_remark. " | ".$get->qty_jual." ".$get->uom_jual. " | ".$get->qty2_jual." ".$get->uom2_jual. " | ".$get->lebar_jadi." ".$get->uom_lebar_jadi;

                                    $data_update_items = array(
                                                    'corak_remark'  => trim($corak_remark),
                                                    'warna_remark'  => trim($warna_remark),
                                                    'qty_jual'      => $qty_jual,
                                                    'uom_jual'      => $uom_jual,
                                                    'qty2_jual'     => $qty2_jual,
                                                    'uom2_jual'     => $uom2_jual,
                                                    'lebar_jadi'    => $lebar_jadi,
                                                    'uom_lebar_jadi'=> $uom_lebar_jadi,
                                    );                     

                                    $update = $this->m_splitLot->update_data_split_items($data_update_items,$kode,$lot);
                                    
                                    $data_update_quant = array(
                                                    'corak_remark'  => trim($corak_remark),
                                                    'warna_remark'  => trim($warna_remark),
                                                    'qty_jual'      => $qty_jual,
                                                    'uom_jual'      => $uom_jual,
                                                    'qty2_jual'     => $qty2_jual,
                                                    'uom2_jual'     => $uom2_jual,
                                                    'lebar_jadi'    => $lebar_jadi,
                                                    'uom_lebar_jadi'=> $uom_lebar_jadi,
                                    );   

                                    $update = $this->m_splitLot->update_data_stock_quant($data_update_quant,$quant_id,$lot);

                                    // if(empty($update)){
                                    //     throw new \Exception('Gagal Mengubah data ', 500);
                                    // }

                                    $jenis_log = "edit";
                                    $note_after = $corak_remark." | ".$warna_remark. " | ".$qty_jual." ".$uom_jual. " | ".$qty2_jual." ".$uom2_jual. " | ".$lebar_jadi." ".$uom_lebar_jadi;
                                    $note_log  = "Edit Data Items lot ".$lot."<br> ".$note_before." <b> -> </b> <br> ".$note_after;
                                    $data_history = array(
                                                    'datelog'   => date("Y-m-d H:i:s"),
                                                    'kode'      => $kode,
                                                    'jenis_log' => $jenis_log,
                                                    'note'      => $note_log  );
                                    
                                    // load in library
                                    $this->_module->gen_history_ip($sub_menu,$username,$data_history);

                                    if (!$this->_module->finishTransaction()) {
                                        throw new \Exception('Gagal Menyimpan Data2', 500);
                                    }

                                    $callback = array('status'=>'success', 'message' =>'Data Berhasil Diubah !', 'icon'=> 'fa fa-check', 'type'=>'success');

                                }
                            }
                    }

                }

                if (!$this->_module->finishTransaction()) {
                    throw new \Exception('Data  Gagal diubah', 500);
                }

                $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($callback));
            }
            
        }catch(Exception $ex){
            $this->_module->finishRollBack();
            $this->_module->rollbackTransaction();
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger', 'status' => 'failed')));
        }finally {
            // unlock table
            $this->_module->unlock_tabel();

        }
    }

    public function print_modal()
    {
    	$data['kode_split']  = $this->input->post('kode_split');
    	$data['data_print']  = ($this->input->post('data_arr'));
        $data['kode_k3l']    = $this->_module->get_list_kode_k3l();        
        $data['desain_barcode']   = $this->_module->get_list_desain_barcode_by_type('LBK');    
        return $this->load->view('modal/v_split_lot_print_modal', $data);
    }


    function print_barcode_split()
    {
        try{
            if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
                // session habis
                throw new \Exception('Waktu Anda Telah Habis', 401);
            }else{

                $kode_split  = $this->input->post('kode_split');
                $data_print  = $this->input->post('data_print');
                $k3l        = $this->input->post('k3l');
                $desain_barcode  = $this->input->post('desain_barcode');

                if(empty($kode_split) || empty($data_print)){
                    throw new \Exception('Data Print Lot Kosong !', 200);
                }else if(empty($desain_barcode)){
                    throw new \Exception('Desain Barcode K3l Harus dipilih !', 200);
                }else if(empty($k3l)){
                    throw new \Exception('K3L Harus dipilih  !', 200);
                }else{
                    $data = $this->m_splitLot->get_data_split_by_kode($kode_split);

                    if(empty($data)){
                        throw new \Exception('Data Join Lot tidak ditemukan !', 200);
                    }else{
                        
                        $data_print = $this->print_barcode($desain_barcode,$data_print,$kode_split,$k3l);
                        if(empty($data_print)){
                            throw new \Exception('Data Print tidak ditemukan !', 500);
                        }
                        $callback = array('status' => 'success', 'message' => 'Print Berhasil !', 'icon' =>'fa fa-check', 'type' => 'success', 'data_print' =>$data_print);
                    }
                }
                $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($callback));
            }

        }catch(Exception $ex){
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('status'=>'failed','message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }


    function print_barcode($desain_barcode,$data_stock,$kode_split,$k3l)
    {
        $kode_k3l  = $k3l;
        $desain_barcode = strtolower($desain_barcode);
        $code = new Code\Code128New();
        $this->prints->setView('print/'.$desain_barcode);
        $data_print_array = array();
        $data_qty2_jual = array();
        foreach($data_stock as $sq){
            $get = $this->_module->get_stock_quant_by_id($sq)->result();
            foreach($get as $row){
                $gen_code = $code->generate($row->lot, "", 60, "vertical");
                $tanggal = date('Ymd', strtotime($row->create_date));
                $data_print_array = array(
                            'pattern' => $row->corak_remark,
                            'isi_color' => !empty($row->warna_remark)? $row->warna_remark : '&nbsp' ,
                            'isi_satuan_lebar' => 'WIDTH ('.$row->uom_lebar_jadi.')',
                            'isi_lebar' => !empty($row->lebar_jadi)? $row->lebar_jadi : '&nbsp',
                            'isi_satuan_qty1' => 'QTY ['.$row->uom_jual.']',
                            'isi_qty1' => round($row->qty_jual,2),
                            'barcode_id' => $row->lot,
                            'tanggal_buat' => $tanggal,
                            'no_pack_brc' => $kode_split,
                            'barcode' => $gen_code,
                            'k3l' => $kode_k3l
                );
                if(!empty((double)$row->qty2_jual)){
                    $data_qty2_jual = array('isi_satuan_qty2' => 'QTY2 ['.$row->uom2_jual.']', 'isi_qty2' => round($row->qty2_jual,2));
                    $data_print_array = array_merge($data_print_array,$data_qty2_jual);
                }
                // break;
                $this->prints->addDatas($data_print_array);
            }
        }
     
        return $this->prints->generate();
    }


}