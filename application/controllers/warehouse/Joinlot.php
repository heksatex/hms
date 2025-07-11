<?php defined('BASEPATH') or exit ('No Direct Script Acces Allowed');

/**
 * 
 */
class Joinlot extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->is_loggedin();//cek apakah user sudah login
        $this->load->model("_module");
        $this->load->model("m_joinLot");
        $this->load->model("m_inlet");
        $this->load->library("token");
        $this->load->library('prints');
        $this->load->library('barcode');
    }

    public function index()
    {
        $data['id_dept']   = 'JLOT';
        $data['warehouse'] = $this->_module->get_list_departement();
        $this->load->view('warehouse/v_join_lot', $data);
    }

    public function get_data()
    {
        if(isset($_POST['start']) && isset($_POST['draw'])){
            $list = $this->m_joinLot->get_datatables();
            $data = array();
            $no = $_POST['start'];
            foreach ($list as $field) {
                $kode_encrypt = encrypt_url($field->kode_join);
            
                $no++;
                $row = array();
                $row[] = $no;
                $row[] = '<a href="'.base_url('warehouse/joinlot/edit/'.$kode_encrypt).'">'.$field->kode_join.'</a>';
                $row[] = $field->tanggal_buat;
                $row[] = $field->tanggal_transaksi;
                $row[] = $field->departemen;
                $row[] = $field->jml_join;
                $row[] = $field->nama_produk;
                $row[] = $field->corak_remark;
                $row[] = $field->warna_remark;
                $row[] = $field->lot;
                $row[] = $field->note;
                $row[] = $field->nama_status;
                $row[] = $field->kode_join;
    
                $data[] = $row;
            }
    
            $output = array(
                "draw" => $_POST['draw'],
                "recordsTotal" => $this->m_joinLot->count_all(),
                "recordsFiltered" => $this->m_joinLot->count_filtered(),
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
	    $data['id_dept']       = 'JLOT';
        $data['warehouse']     = $this->_module->get_list_departement();
	    return $this->load->view('warehouse/v_join_lot_add', $data);
	}

    public function edit($id = null)
    {
        if(!isset($id)) show_404();
        $data['id_dept']      = 'JLOT';
        $kode_decrypt         = decrypt_url($id);
        $data['mms']          = $this->_module->get_data_mms_for_log_history('JLOT');// get mms by dept untuk menu yg beda-beda
        $data['join']         = $this->m_joinLot->get_data_join_lot_by_kode($kode_decrypt);
        $data['join_items']   = $this->m_joinLot->get_data_join_lot_items_by_kode($kode_decrypt);
        $this->load->view('warehouse/v_join_lot_edit', $data);
    }

    function edit_join_result_modal()
    {
        $kode               = $this->input->post('kode');
        $lot                = $this->input->post('lot');
        $data['kode']       = $kode;
        $data['data_join']         = $this->m_joinLot->get_data_join_lot_by_kode($kode);
        return $this->load->view('modal/v_join_lot_edit_result_modal',$data);
    }

    function save_join_lot()
    {
        try{
            if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
                // session habis
                throw new \Exception('Waktu Anda Telah Habis', 401);
            }else{

                $dept_id = $this->input->post('dept');
                $note    = $this->input->post('note');
                $kode    = $this->input->post('kode');// kode join lot
                $tanda_join    = $this->input->post('tanda_join');// tanda join lot

                // start transaction
                $this->_module->startTransaction();

                // lock table
                $this->_module->lock_tabel('join_lot as j WRITE, join_lot WRITE, departemen as d WRITE, mst_sales_group as msg WRITE,token_increment WRITE,user WRITE ,main_menu_sub WRITE,log_history WRITE ');

                if(empty($dept_id)){
                    throw new \Exception("Departemen Harus diisi !", 200);
                }else if(empty($note)){
                    throw new \Exception("Note Harus diisi !", 200);
                }else{
                    $sub_menu   = $this->uri->segment(2);
                    $username   = addslashes($this->session->userdata('username')); 
                    $nu         = $this->_module->get_nama_user($username)->row_array(); 
                    $nama_user  = addslashes($nu['nama']);
                    $tgl        = date('Y-m-d H:i:s');

                    // cek kode
                    if(!empty($kode)){// update 

                        $cek           = $this->m_joinLot->get_data_join_lot_by_kode($kode);

                        if($cek->status == 'done'){
                            $callback = array('status' => 'failed', 'message' => 'Data tidak bisa dirubah, Status sudah <b> Done </b> !', 'icon' =>'fa fa-warning', 'type' => 'danger');
                        }else if($cek->status == 'cancel'){
                            $callback = array('status' => 'failed', 'message' => 'Data tidak bisa dirubah, Status sudah Cancel !', 'icon' =>'fa fa-warning', 'type' => 'danger');
                        }else {

                            $data_update = array('note'=> $note, 'tanda_join'=>$tanda_join);
                            $this->m_joinLot->update_join_lot_by_kode($data_update,$kode);
                            
                            $jenis_log = "edit";
                            $note_log  = $kode." | ".$note." | ".$tanda_join;
                            $data_history = array(
                                            'datelog'   => date("Y-m-d H:i:s"),
                                            'kode'      => $kode,
                                            'jenis_log' => $jenis_log,
                                            'note'      => $note_log );
                            $this->_module->gen_history_ip($sub_menu,$username,$data_history);

                            $callback = array('status' => 'success', 'message' => 'Data Join Lot berhasil diubah !', 'icon' =>'fa fa-success', 'type' => 'success', );
                        }

                    }else{// insert

                        $kode_join = $this->token->noUrut('join_lot', date('ym'), true)->generate('JL', '%05d')->get();
                        $data_insert = array(
                                            'kode_join'=> $kode_join,
                                            'tanggal_buat'    => $tgl,
                                            'tanggal_transaksi'=> $tgl,
                                            'dept_id'         => $dept_id,
                                            'nama_user'       => $nama_user,
                                            'note'            => $note,
                                            'tanda_join'      => $tanda_join
                        );

                        $insert = $this->m_joinLot->insert_data_join_lot($data_insert);
                        if(!empty($insert)){
                            throw new \Exception("Data Gagal disimpan !", 500);
                        }

                        $jenis_log = "edit";
                        $note_log  = $kode_join." | ".$note." | ".$tanda_join;
                        $data_history = array(
                                            'datelog'   => date("Y-m-d H:i:s"),
                                            'kode'      => $kode_join,
                                            'jenis_log' => $jenis_log,
                                            'note'      => $note_log );
                        $this->_module->gen_history_ip($sub_menu,$username,$data_history);

                        $callback = array('status' => 'success', 'message' => 'Data Join Lot berhasil disimpan !', 'icon' =>'fa fa-success', 'type' => 'success', 'isi'=> encrypt_url($kode_join));
              
                    }

                }

                if(!$this->_module->finishTransaction()){
                    throw new \Exception("Data Gagal disimpan !", 500);
                }

                $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($callback));

            }
        }catch(Exception $ex){
            // finish transaction
            $this->_module->finishRollBack();
            $this->_module->rollbackTransaction();
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('status'=>'failed', 'message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }finally{
            // unlock table
            $this->_module->unlock_tabel();
        }
    }

    function save_join_lot_result()
    {

        try{
            if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
                // session habis
                throw new \Exception('Waktu Anda Telah Habis', 401);
            }else{

                $kode           = $this->input->post('kode');
                $kode_produk    = $this->input->post('kode_produk');
                $corak_remark   = $this->input->post('corak_remark');
                $warna_remark   = $this->input->post('warna_remark');
                $qty_jual       = $this->input->post('qty_jual');
                $uom_jual       = $this->input->post('uom_qty_jual');
                $qty2_jual      = $this->input->post('qty2_jual');
                $uom2_jual      = $this->input->post('uom_qty2_jual');
                $lebar_jadi     = $this->input->post('lebar_jadi');
                $uom_lebar_jadi = $this->input->post('uom_lebar_jadi');
                $lot            = $this->input->post('lot');
                $quant_id       = $this->input->post('quant_id');

                $sub_menu  = $this->uri->segment(2);
                $username = addslashes($this->session->userdata('username')); 

                // start transaction
                $this->_module->startTransaction();

                // lock table
                $this->_module->lock_tabel('join_lot as j WRITE, join_lot WRITE, departemen as d WRITE, mst_sales_group as msg WRITE,token_increment WRITE, mst_produk WRITE, user WRITE ,main_menu_sub WRITE,log_history WRITE, picklist_detail WRITE, stock_quant WRITE ');

                $join = $this->m_joinLot->get_data_join_lot_by_kode($kode);

                if(empty($join)){
                    throw new \Exception('Data Barcode tidak ditemukan !', 200);
                }else if(empty($kode_produk)){
                    $callback = array('status' => 'failed', 'message' => 'Produk Kosong !', 'icon' => 'fa fa-warrning' , 'type' => 'danger');
                }else if(empty($corak_remark)){
                    $callback = array('status' => 'failed', 'message' => 'Corak Remark Harus diisi !', 'icon' => 'fa fa-warning' , 'type' => 'danger');
                }else if(empty($warna_remark)){
                    $callback = array('status' => 'failed', 'message' => 'Warna Remark Harus diisi !', 'icon' => 'fa fa-warning' , 'type' => 'danger');
                }else if(empty($qty_jual)){
                    $callback = array('status' => 'failed', 'message' => 'Qty Jual Jadi Harus diisi !', 'icon' => 'fa fa-warning' , 'type' => 'danger');
                }else if(empty($uom_jual)){
                    $callback = array('status' => 'failed', 'message' => 'Uom Jual Jadi Harus diisi !', 'icon' => 'fa fa-warning' , 'type' => 'danger');
                }else if(empty($uom_lebar_jadi)){
                    $callback = array('status' => 'failed', 'message' => 'Uom Lebar Jadi Harus diisi !', 'icon' => 'fa fa-warning' , 'type' => 'danger');
                }else{

                    // nama_produk
                    $nm = $this->_module->cek_produk_by_kode_produk($kode_produk)->row_array();
                    $nama_produk = $nm['nama_produk'] ?? '';

                    if(empty($nama_produk)){
                        $callback = array('status' => 'failed', 'message' => 'Nama Produk tidak ditemukan !', 'icon' => 'fa fa-warning' , 'type' => 'danger');
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

                            // get_data sebelumnya
                            $note_before = $get->kode_produk.' '.$get->nama_produk." | ".$get->corak_remark." | ".$get->warna_remark. " | ".$get->qty_jual." ".$get->uom_jual. " | ".$get->qty2_jual." ".$get->uom2_jual. " | ".$get->lebar_jadi." ".$get->uom_lebar_jadi;

                            // $data_update = array('note'=> $note, 'tanda_join'=>$tanda_join);
                            $data_update = array(
                                                    'corak_remark'  => $corak_remark,
                                                    'warna_remark'  => $warna_remark,
                                                    'qty_jual'      => $qty_jual,
                                                    'uom_jual'      => $uom_jual,
                                                    'qty2_jual'     => $qty2_jual,
                                                    'uom2_jual'     => $uom2_jual,
                                                    'lebar_jadi'    => $lebar_jadi,
                                                    'uom_lebar_jadi'=> $uom_lebar_jadi,
                            );  
                            $this->m_joinLot->update_join_lot_by_kode($data_update,$kode);
                            $this->m_joinLot->update_data_stock_quant($data_update,$quant_id,$lot);

                            $jenis_log = "edit";
                            $note_after = $kode_produk.' '.$nama_produk." | ".$corak_remark." | ".$warna_remark. " | ".$qty_jual." ".$uom_jual. " | ".$qty2_jual." ".$uom2_jual. " | ".$lebar_jadi." ".$uom_lebar_jadi;
                            $note_log  = "Edit Data Join lot ".$lot."<br> ".$note_before." <b> -> </b> <br> ".$note_after;
                            $data_history = array(
                                            'datelog'   => date("Y-m-d H:i:s"),
                                            'kode'      => $kode,
                                            'jenis_log' => $jenis_log,
                                            'note'      => $note_log  );
                                    
                            // load in library
                            $this->_module->gen_history_ip($sub_menu,$username,$data_history);

                            $callback = array('status'=>'success', 'message' =>'Data Berhasil Disimpan !', 'icon'=> 'fa fa-check', 'type'=>'success');

                        }

                    }

                }

                if(!$this->_module->finishTransaction()){
                    throw new \Exception("Data Gagal disimpan !", 500);
                }

                $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($callback));

            }
        }catch(Exception $ex){
            // finish transaction
            $this->_module->finishRollBack();
            $this->_module->rollbackTransaction();
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('status'=>'failed', 'message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }finally{
            // unlock table
            $this->_module->unlock_tabel();
        }

    }

    function import_produk_join()
    {
        $data['kode_join'] = $this->input->post('kode_join');
        $data['dept_id'] = $this->input->post('dept_id');
        return $this->load->view('modal/v_import_produk_join_lot_modal',$data);
    }

    public function list_import_produk()
    {
        if(isset($_POST['start']) && isset($_POST['draw'])){
            $kode_lokasi  = "GJD/Stock";
            $list = $this->m_joinLot->get_datatables2($kode_lokasi);
            $data = array();
            $no = $_POST['start'];
            foreach ($list as $field) {
                $no++;
                $row = array();
                $row[] = $no.".";
                $row[] = $field->kode_produk;
                $row[] = $field->nama_produk;
                $row[] = $field->corak_remark;
                $row[] = $field->warna_remark;
                $row[] = $field->lot;
                $row[] = number_format($field->qty,2)." ".$field->uom;
                $row[] = number_format($field->qty2,2)." ".$field->uom2;
                $row[] = number_format($field->qty_jual,2)." ".$field->uom_jual;
                $row[] = number_format($field->qty2_jual,2)." ".$field->uom2_jual;
                $row[] = $field->nama_grade;
                $row[] = $field->lebar_jadi." ".$field->uom_lebar_jadi;
                $row[] = $field->nama_sales_group;
                $row[] = $field->lokasi_fisik;
                $row[] = $field->reserve_move;
                $row[] = $field->quant_id;
                $data[] = $row;
            }
            $output = array(
                "draw" => $_POST['draw'],
                "recordsTotal" => $this->m_joinLot->count_all2($kode_lokasi),
                "recordsFiltered" => $this->m_joinLot->count_filtered2($kode_lokasi),
                "data" => $data,
            );
            //output dalam format JSON
            echo json_encode($output);
        }else{
            die();
        }
    }


    function search_lot_join()
    {
        try{
            if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
                // session habis
                throw new \Exception('Waktu Anda Telah Habis', 401);
            }else{

                $sub_menu   = $this->uri->segment(2);
                $username   = addslashes($this->session->userdata('username')); 

                $kode_join = $this->input->post('kode_join');
                $lot       = $this->input->post('lot');
                $dept      = $this->input->post('dept');// dept id ex GJD

                // start transaction
                $this->_module->startTransaction();

                //lock tabel
                $this->_module->lock_tabel('join_lot as j WRITE, departemen as d WRITE,mst_sales_group as msg WRITE, join_lot_items WRITE, stock_quant WRITE, mrp_production_fg_hasil WRITE, picklist_detail WRITE, join_lot_items as jli WRITE,user WRITE ,main_menu_sub WRITE,log_history WRITE, mrp_inlet WRITE ');

                $cek_status  = $this->m_joinLot->get_data_join_lot_by_kode($kode_join);

                if($cek_status->status == 'done'){
                    throw new \Exception('Maaf, Data Tidak Bisa Disimpan, Status Join Lot Sudah Done !', 200);
                }else if($cek_status->status == 'cancel'){
                    throw new \Exception('Maaf, Data Tidak Bisa Disimpan, Status Join Lot Cancel  !', 200);
                }else if(empty($kode_join)){
                    throw new \Exception('Kode Join Kosong !', 200);
                }else if(empty($dept)){
                    throw new \Exception('Departemen Kosong !', 200);
                }else if(empty($lot)){
                    throw new \Exception('Barcode / Lot Kosong !', 200);
                }else{

                    $get = $this->_module->get_nama_dept_by_kode($dept)->row_array();// get lokasi stock
                    $lokasi_stock = $get['stock_location'] ?? '';
                    $get_sq = $this->m_joinLot->get_stock_quant_by_lot($lot,$lokasi_stock);
                    
                    // cek barcode di picklist 
                    $cek_pl = $this->m_joinLot->cek_picklist_by_lot($lot);
                     
                    //cek lot by kodejoin
                    $cek_lot = $this->m_joinLot->cek_lot_join_by_kode($kode_join,$lot)->num_rows();
                    $lot_tmp = "";

                    // cek_lot hph
                    $cek_lot_hph = $this->m_joinLot->cek_lot_hph($lot);
                    $inlet       = '';
                    if(!empty($cek_lot_hph)){
                        $id_inlet = $cek_lot_hph->id_inlet ?? ''; 
                        $inlet = $this->m_joinLot->cek_status_inlet_by_id($id_inlet);
                    }
                    if(empty($get_sq)){
                        $get_sq2 = $this->m_joinLot->get_stock_quant_by_lot($lot);
                        if(empty($get_sq2)){
                            throw new \Exception('Barcode / Lot tidak ditemukan !', 200);
                        }else if($get_sq2->lokasi != $lokasi_stock){
                            throw new \Exception('Lokasi Barcode / Lot tidak Valid  Lokasi Sekarang ada di <b>'.$get_sq2->lokasi.'</b> !', 200);                       
                        }else{
                            throw new \Exception('Barcode / Lot tidak valid !', 200);
                        }
                    }else if($cek_lot > 0){
                        throw new \Exception('Barcode / Lot sudah diinput !', 200);
                    // }else if(empty($cek_lot_hph)){
                    //     throw new \Exception('Barcode / Lot sudah bukan dari HPH !', 200);
                    }else if($get_sq->lokasi_fisik == "XPD" AND $dept == 'GJD'){
                        throw new \Exception('Lokasi Barcode / Lot sudah XPD !',200);
                    }else if(!empty($cek_pl) AND $dept == 'GJD'){
                        throw new \Exception('Data Barcode / Lot '.$lot.' sudah masuk PL !',200);
                    }else if(!empty($cek_lot_hph) AND $inlet->status != 'done'){
                        throw new \Exception('Status HPH / INLET Barcode / Lot '.$lot.' masih <b>'.$inlet->status.'<b> !',200);
                    }else{

                        $items_join = $this->m_joinLot->get_data_join_lot_items_by_kode($kode_join);
                        if(!empty($items_join)){
                            foreach($items_join as $ij){
                                if(($ij->kode_produk != $get_sq->kode_produk) or ($ij->nama_produk != $get_sq->nama_produk)){//produk
                                    $lot_tmp .= $get_sq->lot;
                                    throw new \Exception('Produk Barcode / Lot '.$lot_tmp.' tidak sama  !', 200);
                                }else if($ij->corak_remark != $get_sq->corak_remark){// cek corak remark
                                    $lot_tmp .= $get_sq->lot;
                                    throw new \Exception('Corak Remark Barcode / Lot '.$lot_tmp.' tidak sama  !', 200);
                                }else if($ij->warna_remark != $get_sq->warna_remark){
                                    $lot_tmp .= $get_sq->lot;
                                    throw new \Exception('Warna Remark Barcode / Lot '.$lot_tmp.' tidak sama  !', 200);
                                }else if($ij->uom_jual != $get_sq->uom_jual){
                                    $lot_tmp .= $get_sq->lot;
                                    throw new \Exception('Uom Jual Barcode / Lot '.$lot_tmp.' tidak sama  !', 200);
                                }else if($ij->uom2_jual != $get_sq->uom2_jual){
                                    $lot_tmp .= $get_sq->lot;
                                    throw new \Exception('Uom2 Jual Barcode / Lot '.$lot_tmp.' tidak sama  !', 200);
                                }else if($ij->grade != $get_sq->nama_grade){
                                    $lot_tmp .= $get_sq->lot;
                                    throw new \Exception('Grade Barcode / Lot '.$lot_tmp.' tidak sama  !', 200);
                                }else if(($ij->lebar_jadi != $get_sq->lebar_jadi) or ($ij->uom_lebar_jadi != $get_sq->uom_lebar_jadi)){
                                    $lot_tmp .= $get_sq->lot;
                                    throw new \Exception('Lebar Jadi Barcode / Lot '.$lot_tmp.' tidak sama  !', 200);
                                }else if($ij->sales_group != $get_sq->sales_group){
                                    $lot_tmp .= $get_sq->lot;
                                    throw new \Exception('Marketing Barcode / Lot '.$lot_tmp.' tidak sama  !', 200);
                                }
                            }
                        }

                        $row_order = $this->m_joinLot->get_row_order_join_lot_by_kode($kode_join);
                        // insert into join_lot_items
                        $data[] = array('kode_join'       => $kode_join,
                                        'quant_id'      => $get_sq->quant_id,
                                        'kode_produk'   => $get_sq->kode_produk,
                                        'nama_produk'   => $get_sq->nama_produk,
                                        'corak_remark'  => $get_sq->corak_remark,
                                        'warna_remark'  => $get_sq->warna_remark,
                                        'lot'           => $get_sq->lot,
                                        'qty'           => $get_sq->qty,
                                        'uom'           => $get_sq->uom,
                                        'qty2'          => $get_sq->qty2,
                                        'uom2'          => $get_sq->uom2,
                                        'qty_jual'      => $get_sq->qty_jual,
                                        'uom_jual'      => $get_sq->uom_jual,
                                        'qty2_jual'     => $get_sq->qty2_jual,
                                        'uom2_jual'     => $get_sq->uom2_jual,
                                        'grade'         => $get_sq->nama_grade,
                                        'lebar_jadi'    => $get_sq->lebar_jadi,
                                        'uom_lebar_jadi'=> $get_sq->uom_lebar_jadi,
                                        'sales_group'   => $get_sq->sales_group,
                                        'row_order'     => $row_order,
                        );

                        $this->m_joinLot->insert_data_join_lot_items($data);

                        
                        $jenis_log = "edit";
                        $data_barcode = $get_sq->kode_produk." ".$get_sq->nama_produk." ".$get_sq->lot." ".$get_sq->qty." ".$get_sq->uom." ".$get_sq->qty2." ".$get_sq->uom2;
                        $note_log  = "Tambah Data Join Items Scan ".$kode_join." <br> ".$data_barcode;
                        $data_history = array(
                                            'datelog'   => date("Y-m-d H:i:s"),
                                            'kode'      => $kode_join,
                                            'jenis_log' => $jenis_log,
                                            'note'      => $note_log );
                        $this->_module->gen_history_ip($sub_menu,$username,$data_history);

                        $callback = array('status' => 'success', 'message' => 'Data Lot Join berhasil ditambah !', 'icon' =>'fa fa-success', 'type' => 'success');
                    }

                }

                if (!$this->_module->finishTransaction()) {
                    throw new \Exception('Gagal Simpan data ', 500);
                }
                $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($callback));

            }
            // // finish transaction
            // $this->_module->finishTransaction();

        }catch(Exception $ex){
            // finish transaction
            $this->_module->finishRollBack();
            $this->_module->rollbackTransaction();
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('status'=>'failed', 'message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }finally{
            // unlock table
            $this->_module->unlock_tabel();
        }
    }


    function save_details_import_produk_joinlot_modal()
    {

        try{
            if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
                // session habis
                throw new \Exception('Waktu Anda Telah Habis', 401);
            }else{

                $kode_join  = $this->input->post('kode_join');
                $arr_data   = $this->input->post('arr_data');
                $countchek  = $this->input->post('countchek');
                $dept       = $this->input->post('dept_id');// dept id ex GJD

                $sub_menu   = $this->uri->segment(2);
                $username   = addslashes($this->session->userdata('username')); 
               

                // start transaction
                $this->_module->startTransaction();

                //lock tabel
                $this->_module->lock_tabel('join_lot as j WRITE, departemen as d WRITE,mst_sales_group as msg WRITE, join_lot_items WRITE, stock_quant WRITE, mrp_production_fg_hasil WRITE, picklist_detail WRITE, join_lot_items as jli WRITE,user WRITE ,main_menu_sub WRITE,log_history WRITE ');
                
                // cek status done / cancel
                $cek_status  = $this->m_joinLot->get_data_join_lot_by_kode($kode_join);

                if($cek_status->status == 'done'){
                    $callback = array('status' => 'failed', 'message'=>'Maaf, Data Tidak Bisa Disimpan, Status Join Lot Sudah Done !', 'icon' => 'fa fa-warning', 'type'=>'danger');
                }else if($cek_status->status == 'cancel'){
                    $callback = array('status' => 'failed', 'message'=>'Maaf, Data Tidak Bisa Disimpan, Status Join Lot Cancel !', 'icon' => 'fa fa-warning', 'type'=>'danger');
                }else if(empty($kode_join)){
                    throw new \Exception('Kode Join Kosong !', 200);
                }else if(empty($dept)){
                    throw new \Exception('Departemen Kosong !', 200);
                }else if(empty($arr_data)){
                    throw new \Exception('Data Lot yang akan di Join belum dipilih !', 200);
                }else{
                    
                    $get = $this->_module->get_nama_dept_by_kode($dept)->row_array();// get lokasi stock
                    $lokasi_stock = $get['stock_location'] ?? '';
                    $row_order  = $this->m_joinLot->get_row_order_join_lot_by_kode($kode_join);
                    $data_items = array();
                    $lot_not_found        = '';
                    $lot_not_valid_lokasi = '';
                    $lot_not_valid        = '';
                    $lot_input            = '';
                    $lot_xpd              = '';
                    $count                = 1;
                    $log_add_items        = '';
                    $row_order = $this->m_joinLot->get_row_order_join_lot_by_kode($kode_join);
                    $tmp_items = "";
                    $corak_remark_note_same = false;
                    $grade_note_same = false;
                    $lot_tmp    = "";
                    foreach($arr_data as $row){

                        $get_sq = $this->m_joinLot->get_stock_quant_by_id($row,$lokasi_stock);// GJD   
                        
                        if(empty($get_sq)){
                            $get_sq2 = $this->_module->get_stock_quant_by_id($row)->row();
                            // $lot = $get_sq['lot'];
                            if(empty($get_sq2)){
                                // $lot_not_found .= $lot.'<br> ';
                                throw new \Exception('Barcode / Lot tidak ditemukan !', 200);
                            }else if($get_sq2->lokasi != $lokasi_stock){
                                $lot = $get_sq2['lot'];
                                // $lot_not_valid_lokasi .= 'Lokasi  Barcode / Lot '.$lot.' sekarang di '.$get_sq2->lokasi.' <br>';
                                throw new \Exception('Lokasi  Barcode / Lot '.$lot.' sekarang di '.$get_sq2->lokasi.' !', 200);                       
                            }else{
                                // $lot_not_valid .= $lot;
                                throw new \Exception('Barcode / Lot tidak valid !', 200);
                            }
                            break;
                        }else if($get_sq->lokasi_fisik == 'XPD' AND $dept == 'GJD'){
                            $lot        = $get_sq->lot;
                            throw new \Exception('Lokasi Barcode / Lot '.$lot.' sudah XPD !', 200);
                        }else{

                            // cek_lot hph
                            $lot         = $get_sq->lot;
                            $cek_lot_hph = $this->m_joinLot->cek_lot_hph($lot);
                            $inlet       = '';
                            $status_inlet = '';
                            if(!empty($cek_lot_hph)){
                                $id_inlet = $cek_lot_hph->id_inlet ?? ''; 
                                $inlet = $this->m_joinLot->cek_status_inlet_by_id($id_inlet);
                                $status_inlet = $inlet->status ?? '';
                            }
                            // cek barcode di picklist 
                            $cek_pl = $this->m_joinLot->cek_picklist_by_lot($lot);

                            if(!empty($cek_lot_hph) AND $status_inlet != 'done'){
                                throw new \Exception('Status HPH / INLET Barcode / Lot '.$lot.' masih <b>'.$status_inlet.'<b> !',200);
                            }else if(!empty($cek_pl)){
                                throw new \Exception('Data Barcode / Lot '.$lot.' sudah masuk PL !',200);
                            }else{
                                $cek_lot = $this->m_joinLot->cek_lot_join_by_kode($kode_join,$lot)->num_rows();
        
                                if(!empty($cek_lot)){
                                    foreach($cek_lot as $cki){
    
                                        if($cki['grade'] != $get_sq->nama_grade){
                                            $grade_note_same = true;
                                            $lot_tmp .= $get_sq->lot;
                                            // break;
                                            throw new \Exception('Grade Barcode / Lot '.$lot_tmp.' tidak sama  !', 200);
                                        }
    
                                        if($cki['corak_remark'] != $get_sq->corak_remark){
                                            $corak_remark_note_same = true;
                                            $lot_tmp .= $get_sq->lot;
                                            throw new \Exception('Corak Remark Barcode / Lot '.$lot_tmp.' tidak sama  !', 200);
                                        }
                                    }
                                    $tmp_items = "";
                                }
    
                                if($cek_lot > 0){
                                    // $lot_input .= $lot.' <br> ';
                                    throw new \Exception('Barcode / Lot '.$lot.' sudah diinput !', 200);
                                }else{
    
                                    $items_join = $this->m_joinLot->get_data_join_lot_items_by_kode($kode_join);
                                    if(!empty($items_join)){
                                        foreach($items_join as $ij){
                                            if(($ij->kode_produk != $get_sq->kode_produk) or ($ij->nama_produk != $get_sq->nama_produk)){//produk
                                                $lot_tmp .= $get_sq->lot;
                                                throw new \Exception('Produk Barcode / Lot '.$lot_tmp.' tidak sama  !', 200);
                                            }else if($ij->corak_remark != $get_sq->corak_remark){// cek corak remark
                                                $lot_tmp .= $get_sq->lot;
                                                throw new \Exception('Corak Remark Barcode / Lot '.$lot_tmp.' tidak sama  !', 200);
                                            }else if($ij->warna_remark != $get_sq->warna_remark){
                                                $lot_tmp .= $get_sq->lot;
                                                throw new \Exception('Warna Remark Barcode / Lot '.$lot_tmp.' tidak sama  !', 200);
                                            }else if($ij->uom_jual != $get_sq->uom_jual){
                                                $lot_tmp .= $get_sq->lot;
                                                throw new \Exception('Uom Jual Barcode / Lot '.$lot_tmp.' tidak sama  !', 200);
                                            }else if($ij->uom2_jual != $get_sq->uom2_jual){
                                                $lot_tmp .= $get_sq->lot;
                                                throw new \Exception('Uom2 Jual Barcode / Lot '.$lot_tmp.' tidak sama  !', 200);
                                            }else if($ij->grade != $get_sq->nama_grade){
                                                $lot_tmp .= $get_sq->lot;
                                                throw new \Exception('Grade Barcode / Lot '.$lot_tmp.' tidak sama  !', 200);
                                            }else if(($ij->lebar_jadi != $get_sq->lebar_jadi) or ($ij->uom_lebar_jadi != $get_sq->uom_lebar_jadi)){
                                                $lot_tmp .= $get_sq->lot;
                                                throw new \Exception('Lebar Jadi Barcode / Lot '.$lot_tmp.' tidak sama  !', 200);
                                            }else if($ij->sales_group != $get_sq->sales_group){
                                                $lot_tmp .= $get_sq->lot;
                                                throw new \Exception('Marketing Barcode / Lot '.$lot_tmp.' tidak sama  !', 200);
                                            }
                                        }
                                    }
                                    
                                    // insert into join_lot_items
                                    $data_items[] = array('kode_join'       => $kode_join,
                                                    'quant_id'      => $get_sq->quant_id,
                                                    'kode_produk'   => $get_sq->kode_produk,
                                                    'nama_produk'   => $get_sq->nama_produk,
                                                    'corak_remark'  => $get_sq->corak_remark,
                                                    'warna_remark'  => $get_sq->warna_remark,
                                                    'lot'           => $get_sq->lot,
                                                    'qty'           => $get_sq->qty,
                                                    'uom'           => $get_sq->uom,
                                                    'qty2'          => $get_sq->qty2,
                                                    'uom2'          => $get_sq->uom2,
                                                    'qty_jual'      => $get_sq->qty_jual,
                                                    'uom_jual'      => $get_sq->uom_jual,
                                                    'qty2_jual'     => $get_sq->qty2_jual,
                                                    'uom2_jual'     => $get_sq->uom2_jual,
                                                    'grade'         => $get_sq->nama_grade,
                                                    'lebar_jadi'    => $get_sq->lebar_jadi,
                                                    'uom_lebar_jadi'=> $get_sq->uom_lebar_jadi,
                                                    'sales_group'   => $get_sq->sales_group,
                                                    'row_order'     => $row_order,
                                    );
    
                                    $tmp_items = $data_items;
                                    // var_dump($tmp_items);
                                    $row_order++;
    
                                    $log_add_items .= "(".$count.") ".$get_sq->kode_produk." ".$get_sq->nama_produk." ".$get_sq->lot." ".$get_sq->qty." ".$get_sq->uom." ".$get_sq->qty2." ".$get_sq->uom2." <br>";
                                    $count++;
                                }
                            }

                        }
                    }


                    if(!empty($data_items) AND $lot_tmp == ''){

                        $insert = $this->m_joinLot->insert_data_join_lot_items($data_items);
                        if(!empty($insert)){
                            throw new \Exception('Data Gagal Disimpan !', 200);
                        }

                        $jenis_log = "edit";
                        $note_log  = "Tambah Data Join Items Manual ".$kode_join." <br> ".$log_add_items;
                        $data_history = array(
                                            'datelog'   => date("Y-m-d H:i:s"),
                                            'kode'      => $kode_join,
                                            'jenis_log' => $jenis_log,
                                            'note'      => $note_log );
                        $this->_module->gen_history_ip($sub_menu,$username,$data_history);

                        $callback = array('status' => 'success', 'message' => 'Data Lot Join berhasil ditambah !', 'icon' =>'fa fa-success', 'type' => 'success');

                    }else{
                        if(!empty($lot_not_found)){
                            $callback = array('status' => 'failed', 'message'=>'Barcode / Lot tidak ditemukan ! <br>'.$lot_not_found, 'icon' => 'fa fa-warning', 'type'=>'danger');
                        }else if(!empty($lot_not_valid_lokasi)){
                            $callback = array('status' => 'failed', 'message'=>'Lokasi Barcode / Lot tidak valid ! <br>'.$lot_not_valid_lokasi, 'icon' => 'fa fa-warning', 'type'=>'danger');
                        }else if(!empty($lot_xpd)){
                            $callback = array('status' => 'failed', 'message'=>'Lokasi Barcode / Lot sudah XPD ! <br>'.$lot_not_valid_lokasi, 'icon' => 'fa fa-warning', 'type'=>'danger');
                        }else if(!empty($lot_not_valid)){
                            $callback = array('status' => 'failed', 'message'=>'Lokasi Barcode / Lot tidak valid ! <br>'.$lot_not_valid_lokasi, 'icon' => 'fa fa-warning', 'type'=>'danger');
                        }else if(!empty($lot_input)){
                            $callback = array('status' => 'failed', 'message'=>'Barcode / Lot sudah diinput ! <br>'.$lot_input, 'icon' => 'fa fa-warning', 'type'=>'danger');
                        }else{
                            $callback = array('status' => 'failed', 'message'=>'Gagal menambahkan Barcode / Lot !', 'icon' => 'fa fa-warning', 'type'=>'danger');
                        }

                    }

                }

                if (!$this->_module->finishTransaction()) {
                    throw new \Exception('Gagal Mencari Data', 500);
                }
                $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($callback));

            }

        }catch(Exception $ex){
            // finish transaction
            $this->_module->finishRollBack();
            $this->_module->rollbackTransaction();
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('status'=>'failed', 'message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }finally{
            // unlock table
            $this->_module->unlock_tabel();
        }

    }

    function delete_join_lot_items()
    {
        try{
            if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
                // session habis
                throw new \Exception('Waktu Anda Telah Habis', 401);
            }else{

                $kode_join  = $this->input->post('kode_join');
                $quant_id   = $this->input->post('quant_id');
                $row_order  = $this->input->post('row_order');

                $sub_menu   = $this->uri->segment(2);
                $username   = addslashes($this->session->userdata('username')); 
                
                // start transaction
                $this->_module->startTransaction();

                // lock table
                $this->_module->lock_tabel("join_lot as j WRITE, departemen as d WRITE, mst_sales_group as msg WRITE, join_lot_items WRITE, log_history WRITE, user WRITE ,main_menu_sub WRITE ");
                
                $cek_status  = $this->m_joinLot->get_data_join_lot_by_kode($kode_join);
                
                if($cek_status->status == 'done'){
                    $callback = array('status' => 'failed', 'message'=>'Maaf, Data Tidak Bisa dihapus, Status Join Lot Sudah Done !', 'icon' => 'fa fa-warning', 'type'=>'danger');
                }else if($cek_status->status == 'cancel'){
                    $callback = array('status' => 'failed', 'message'=>'Maaf, Data Tidak Bisa dihapus, Status Join Lot Cancel !', 'icon' => 'fa fa-warning', 'type'=>'danger');
                }else{
                    
                    // cek kp yang akan di delete
                    $cek_lot = $this->m_joinLot->cek_lot_join_by_quant($kode_join,$quant_id,$row_order)->row_array();
                    if(empty($cek_lot)){
                        throw new \Exception('Barcode / Lot tidak ditemukan !', 200);
                    }else{

                        $result_del = $this->m_joinLot->delete_join_lot_items_by_kode($kode_join,$quant_id,$row_order);
                        if(empty($result_del)){
                            throw new \Exception('Barcode / Lot gagal dihapus !', 200);
                        }

                        $log_del_items = $cek_lot['kode_produk']." ".$cek_lot['nama_produk']." ".$cek_lot['lot']." ".$cek_lot['qty']." ".$cek_lot['uom']." ".$cek_lot['qty2']." ".$cek_lot['uom2'];
                        $jenis_log = "cancel";
                        $note_log  = "Hapus Data Join Items ".$kode_join." <br> ".$log_del_items;
                        $data_history = array(
                                            'datelog'   => date("Y-m-d H:i:s"),
                                            'kode'      => $kode_join,
                                            'jenis_log' => $jenis_log,
                                            'note'      => $note_log );
                        $this->_module->gen_history_ip($sub_menu,$username,$data_history);

                        $callback = array('status' => 'success', 'message' => 'Data Lot Join berhasil dhapus !', 'icon' =>'fa fa-success', 'type' => 'success');
                    }

                }

                if (!$this->_module->finishTransaction()) {
                    throw new \Exception('Data Gagal dihapus', 500);
                }

                $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($callback));
            }
        }catch(Exception $ex){
            // finish transaction
            $this->_module->finishRollBack();
            $this->_module->rollbackTransaction();
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('status'=>'failed', 'message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        } finally {
            // unlock table
            $this->_module->unlock_tabel();

        }
    }


    function cancel_join_lot()
    {
        try{
            if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
                // session habis
                throw new \Exception('Waktu Anda Telah Habis', 401);
            }else{

                $kode_join  = $this->input->post('kode_join');

                $sub_menu   = $this->uri->segment(2);
                $username   = addslashes($this->session->userdata('username')); 

                //start transaction
                $this->_module->startTransaction();

                //lock table
                $this->_module->lock_tabel("join_lot as j WRITE, departemen as d WRITE, mst_sales_group as msg WRITE, join_lot_items WRITE, join_lot_items as jli WRITE, log_history WRITE, user WRITE ,main_menu_sub WRITE, join_lot WRITE ");

                $cek_status  = $this->m_joinLot->get_data_join_lot_by_kode($kode_join);
                
                if($cek_status->status == 'done'){
                    $callback = array('status' => 'failed', 'message'=>'Maaf, Data Tidak Bisa batalkan, Status Join Lot Sudah Done !', 'icon' => 'fa fa-warning', 'type'=>'danger');
                }else if($cek_status->status == 'cancel'){
                    $callback = array('status' => 'failed', 'message'=>'Maaf, Data Tidak Bisa batalkan, Status Join Lot Cancel !', 'icon' => 'fa fa-warning', 'type'=>'danger');
                }else{

                    $items = $this->m_joinLot->get_data_join_lot_items_by_kode($kode_join);
                    if(!empty($items)){
                        throw new \Exception('Hapus terlebih dahulu Barcode / Lot tidak yang akan di Join !', 200);
                    }else{

                        $data_update = array('status'=> 'cancel');
                        $this->m_joinLot->update_join_lot_by_kode($data_update,$kode_join);

                        $jenis_log = "cancel";
                        $note_log  = "Batal Join Lot ".$kode_join;
                        $data_history = array(
                                            'datelog'   => date("Y-m-d H:i:s"),
                                            'kode'      => $kode_join,
                                            'jenis_log' => $jenis_log,
                                            'note'      => $note_log );
                        $this->_module->gen_history_ip($sub_menu,$username,$data_history);
    
                        $callback = array('status' => 'success', 'message' => 'Data Join Lot berhasil dibatalkan !', 'icon' =>'fa fa-success', 'type' => 'success');
                    }

                }
                
                if (!$this->_module->finishTransaction()) {
                    throw new \Exception('Data Gagal dibatalkan', 500);
                }
                $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($callback));
            }
        }catch(Exception $ex){
            $this->_module->finishRollBack();
            $this->_module->rollbackTransaction();
            $this->output->set_status_header($ex->getCode() ??  500)
                    ->set_content_type('application/json','utf-8')
                    ->set_output(json_encode(array('status'=>'failed','message'=>$ex->getMessage(), 'icon'=>'fa fa-warning', 'type'=>'danger')));
        } finally {
            // unlock table
            $this->_module->unlock_tabel();

        }

    }

    function generate_join_lot()
    {
        try{
            if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
                // session habis
                throw new \Exception('Waktu Anda Telah Habis', 401);
            }else{

                $kode_join  = $this->input->post('kode_join');

                $sub_menu   = $this->uri->segment(2);
                $username   = addslashes($this->session->userdata('username')); 
                $nama_user = $this->_module->get_nama_user($username)->row_array();
                $tgl        = date("Y-m-d H:i:s");

                //start transaction
                $this->_module->startTransaction();

                // lock table
                $this->_module->lock_tabel("stock_quant WRITE, join_lot as j WRITE, departemen as d WRITE, mst_sales_group as msg WRITE, stock_move WRITE, adjustment WRITE, join_lot_items as jli WRITE, picklist_detail WRITE, mrp_inlet WRITE, token_increment WRITE, mrp_production_fg_hasil WRITE, stock_move_produk WRITE, stock_move_items WRITE, adjustment_items WRITE, join_lot WRITE, log_history WRITE, user WRITE ,main_menu_sub WRITE");


                $cek  = $this->m_joinLot->get_data_join_lot_by_kode($kode_join);
                
                if($cek->status == 'done'){
                    $callback = array('status' => 'failed', 'message'=>'Maaf, Data Tidak Bisa Disimpan, Status Join Lot Sudah Done !', 'icon' => 'fa fa-warning', 'type'=>'danger');
                }else if($cek->status == 'cancel'){
                    $callback = array('status' => 'failed', 'message'=>'Maaf, Data Tidak Bisa Disimpan, Status Join Lot Cancel !', 'icon' => 'fa fa-warning', 'type'=>'danger');
                }else{

                    $get          = $this->_module->get_nama_dept_by_kode($cek->dept_id)->row_array();// get lokasi stock
                    $lokasi_stock = $get['stock_location'] ?? '';
                    $lokasi_adj   = $get['adjustment_location'] ?? '';
                    $nama_departemen   = $get['nama'] ?? '';

                    if($cek->tanda_join == 'true'){
                        $tanda_join = '-';
                    }else{
                        $tanda_join = '';
                    }

                    // get move_id
                    $last_move   = $this->_module->get_kode_stock_move();
                    $move_id     = "SM".$last_move; //Set kode stock_move
                    // get quant_id
                    $start       = $this->_module->get_last_quant_id();

                    // get kode adj
                    $get_kode_adjustment   = $this->_module->get_kode_adj();  
                    // $kode_adjustment   = substr("0000" . $get_kode_adjustment,-4);      
                    $kode_adjustment       = substr($get_kode_adjustment, -5) + 1;
                    $kode_adjustment_tmp   = substr($get_kode_adjustment, -5);
                    $kode_adjustment      = substr("00000" . $kode_adjustment,-5);     
                    $kode_adjustment_tmp2 = $kode_adjustment;
                    $kode_adjustment   = "ADJ/".date("y") . '/' .  date("m") . '/' . $kode_adjustment;

                    $items_join     = $this->m_joinLot->get_data_join_lot_items_by_kode($kode_join);
                    $data_sm    = array();
                    $data_smp   = array();
                    $data_smi   = array();
                    $update_sq      = array();
                    $data_stock_quant = array();
                    $data_adj = array();
                    $data_adj_items = array();
                    $sum_qty        = 0;
                    $sum_qty2       = 0;
                    $sum_qty_jual   = 0;
                    $sum_qty2_jual  = 0;
                    $row_order_adj  = 1;
                    if(!empty($items_join) AND count($items_join) > 1){
                        foreach($items_join as $ij){

                            $get_sq = $this->_module->get_stock_quant_by_id($ij->quant_id)->row_array();
                            // cek barcode di picklist 
                            $cek_pl = $this->m_joinLot->cek_picklist_by_lot($ij->lot);

                            // cek_lot hph
                            $cek_lot_hph = $this->m_joinLot->cek_lot_hph($ij->lot);
                            $inlet       = '';
                            if(!empty($cek_lot_hph)){
                                $id_inlet = $cek_lot_hph->id_inlet ?? ''; 
                                $inlet = $this->m_joinLot->cek_status_inlet_by_id($id_inlet);
                            }

                            if(empty($get_sq)){
                                throw new \Exception('Data Barcode / Lot '.$ij->lot.' tidak ditemukan !',200);
                            }else if($get_sq['lokasi'] != $lokasi_stock ){
                                throw new \Exception('Lokasi Barcode / Lot '.$ij->lot.' bukan di '.$lokasi_stock.' , Lokasi Sekarang di <b>'.$get_sq['lokasi'].'</b> !', 200);
                            }else if($get_sq['lokasi_fisik'] == 'XPD' AND $cek->dept_id == 'GJD'){
                                throw new \Exception('Lokasi Barcode / Lot '.$ij->lot.' sudah XPD !',200);
                            }else if(!empty($get_sq['reserve_move'])){
                                throw new \Exception('Data Barcode / Lot '.$ij->lot.' sudah terpesan oleh dokumen lain !',200);
                            }else if(!empty($cek_pl)){
                                throw new \Exception('Data Barcode / Lot '.$ij->lot.' sudah masuk PL !',200);
                            }else if(!empty($cek_lot_hph) AND $inlet->status != 'done'){
                                throw new \Exception('Status HPH / INLET Barcode / Lot '.$ij->lot.' masih <b>'.$inlet->status.'<b> !',200);
                            }

                            $tmp_quant_id = $ij->quant_id;
                            $lot_tmp   = $ij->lot;
                            $tmp_kode_produk = $ij->kode_produk;
                            $tmp_nama_produk = $ij->nama_produk;
                            $tmp_corak_remark = $ij->corak_remark;
                            $tmp_warna_remark = $ij->warna_remark;
                            $tmp_uom_jual   = $ij->uom_jual;
                            $tmp_uom2_jual  = $ij->uom2_jual;
                            $tmp_grade      = $ij->grade;
                            $tmp_lebar_jadi = $ij->lebar_jadi;
                            $tmp_uom_lebar_jadi = $ij->uom_lebar_jadi;
                            $tmp_sales_group    = $ij->sales_group;
                            $tmp_qty            = $ij->qty;
                            $tmp_uom            = $ij->uom;
                            $tmp_qty2           = $ij->qty2;
                            $tmp_uom2           = $ij->uom2;
                            foreach($items_join as $ij2){
                                if($tmp_kode_produk != $ij2->kode_produk or ($tmp_nama_produk != $ij2->nama_produk)){//produk
                                    throw new \Exception('Produk Barcode / Lot '.$lot_tmp.' tidak sama  !', 200);
                                }else if($tmp_corak_remark != $ij2->corak_remark){// cek corak remark
                                    throw new \Exception('Corak Remark Barcode / Lot '.$lot_tmp.' tidak sama  !', 200);
                                }else if($tmp_warna_remark != $ij2->warna_remark){
                                    throw new \Exception('Warna Remark Barcode / Lot '.$lot_tmp.' tidak sama  !', 200);
                                }else if($tmp_uom_jual != $ij2->uom_jual){
                                    throw new \Exception('Uom Jual Barcode / Lot '.$lot_tmp.' tidak sama  !', 200);
                                }else if($tmp_uom2_jual != $ij2->uom2_jual){
                                    throw new \Exception('Uom2 Jual Barcode / Lot '.$lot_tmp.' tidak sama  !', 200);
                                }else if($tmp_grade != $ij2->grade){
                                    throw new \Exception('Grade Barcode / Lot '.$lot_tmp.' tidak sama  !', 200);
                                }else if(($tmp_lebar_jadi != $ij2->lebar_jadi) or ($tmp_uom_lebar_jadi != $ij2->uom_lebar_jadi)){
                                    throw new \Exception('Lebar Jadi Barcode / Lot '.$lot_tmp.' tidak sama  !', 200);
                                }else if($tmp_sales_group != $ij2->sales_group){
                                    throw new \Exception('Marketing Barcode / Lot '.$lot_tmp.' tidak sama  !', 200);
                                }
                            }
                        
                            // sm to adj
                            $origin = $kode_adjustment."|".$row_order_adj;
                            $data_sm[] = array('move_id'    => $move_id,
                                                    'create_date'=> $tgl,
                                                    'origin'     => $origin,
                                                    'source_move'=> '',
                                                    'method'     => 'GJD|ADJ',
                                                    'lokasi_dari'=> $lokasi_stock,
                                                    'lokasi_tujuan'=> $lokasi_adj,
                                                    'status'    => 'done',
                                                    'row_order' => 1
                                                );
                            $data_smp[] = array(
                                                    'move_id'       => $move_id,
                                                    'kode_produk'   => $tmp_kode_produk,
                                                    'nama_produk'   => $tmp_nama_produk,
                                                    'qty'           => $tmp_qty,
                                                    'uom'           => $tmp_uom,
                                                    'status'        => 'done',
                                                    'origin_prod'   => '',
                                                    'row_order'     => 1
                            );

                            // smi to adj
                            $data_smi[] = array('move_id'        => $move_id,
                                                    'quant_id'      => $tmp_quant_id,
                                                    'tanggal_transaksi'=> $tgl,
                                                    'kode_produk'   => $tmp_kode_produk,
                                                    'nama_produk'   => $tmp_nama_produk,
                                                    'lot'           => $lot_tmp,
                                                    'qty'           => $tmp_qty,
                                                    'uom'           => $tmp_uom,
                                                    'qty2'          => $tmp_qty2,
                                                    'uom2'          => $tmp_uom2,
                                                    'status'        => 'done',
                                                    'origin_prod'   => '',
                                                    'lebar_jadi'    => $ij->lebar_jadi,
                                                    'uom_lebar_jadi'=> $ij->uom_lebar_jadi,
                                                    'lokasi_fisik'  => '',
                                                    'row_order'     => 1
                                                );
                            // update lokasi
                            $update_sq[] = array('quant_id'     => $tmp_quant_id,
                                                'move_date'     => $tgl,
                                                'lokasi'        => $lokasi_adj,
                                                'lokasi_fisik'  => '',
                            );

                            
                            $qty1_move = 0 - $tmp_qty;
                            $qty2_move = 0 - $tmp_qty2;
                            $data_adj_items[] = array('kode_adjustment'   => $kode_adjustment,
                                                'quant_id'          => $tmp_quant_id,
                                                'kode_produk'       => $tmp_kode_produk,
                                                'lot'               => $lot_tmp,
                                                'uom'               => $tmp_uom,
                                                'qty_data'          => $tmp_qty,
                                                'qty_adjustment'    => 0,
                                                'uom2'              => $tmp_uom2,
                                                'qty_data2'         => $tmp_qty2,
                                                'qty_adjustment2'   => 0,
                                                'move_id'           => $move_id,
                                                'qty_move'          => $qty1_move,
                                                'qty2_move'         => $qty2_move,
                                                'row_order'         => $row_order_adj);

                            $sum_qty        = $sum_qty + $ij->qty;
                            $sum_qty2       = $sum_qty2 + $ij->qty2;
                            $sum_qty_jual   = $sum_qty_jual + $ij->qty_jual;
                            $sum_qty2_jual  = $sum_qty2_jual + $ij->qty2_jual;
                            $last_move      = $last_move + 1;
                            $move_id        = "SM".$last_move;
                            $row_order_adj++;

                        }

                        // create stock
                        if($data_sm > 0){

                            if($tmp_grade == 'A'){
                                $lot_baru = $this->token->noUrut('stock_quant_a', date('my'), true)->generate('', '%05d')->get();
                            }else if($tmp_grade == 'B'){
                                $lot_baru = $this->token->noUrut('stock_quant_b', date('my'), true)->generate($tmp_grade, '%05d')->get();
                            }else if($tmp_grade == 'C'){
                                $lot_baru = $this->token->noUrut('stock_quant_c', date('my'), true)->generate($tmp_grade, '%05d')->get();
                            }else{
                                throw new \Exception('Grade tidak Valid !', 200);
                            }

                            $origin   = $kode_adjustment."|".$row_order_adj;
                            // sm to adj
                            $data_sm[] = array('move_id'    => $move_id,
                                                    'create_date'=> $tgl,
                                                    'origin'     => $origin,
                                                    'source_move'=> '',
                                                    'method'     => 'GJD|ADJ',
                                                    'lokasi_dari'=> $lokasi_adj,
                                                    'lokasi_tujuan'=> $lokasi_stock,
                                                    'status'    => 'done',
                                                    'row_order' => 1
                                                );
                            $data_smp[] = array('move_id'       => $move_id,
                                                    'kode_produk'   => $tmp_kode_produk,
                                                    'nama_produk'   => $tmp_nama_produk,
                                                    'qty'           => $sum_qty,
                                                    'uom'           => $tmp_uom,
                                                    'status'        => 'done',
                                                    'origin_prod'   => '',
                                                    'row_order'     => 1
                            );

                            // smi to adj
                            $data_smi[] = array('move_id'        => $move_id,
                                                    'quant_id'      => $start,
                                                    'tanggal_transaksi'=> $tgl,
                                                    'kode_produk'   => $tmp_kode_produk,
                                                    'nama_produk'   => $tmp_nama_produk,
                                                    'lot'           => $lot_baru,
                                                    'qty'           => $sum_qty,
                                                    'uom'           => $tmp_uom,
                                                    'qty2'          => $sum_qty2,
                                                    'uom2'          => $tmp_uom2,
                                                    'status'        => 'done',
                                                    'origin_prod'   => '',
                                                    'lebar_jadi'    => $tmp_lebar_jadi,
                                                    'uom_lebar_jadi'=> $tmp_uom_lebar_jadi,
                                                    'lokasi_fisik'  => '',
                                                    'row_order'     => 1
                            );


                            $data_stock_quant[] = array('quant_id'  => $start,
                                                    'create_date'   => $tgl,
                                                    'move_date'     => $tgl,
                                                    'kode_produk'   => $tmp_kode_produk,
                                                    'nama_produk'   => $tmp_nama_produk,
                                                    'corak_remark'  => $tmp_corak_remark.''.$tanda_join,
                                                    'warna_remark'  => $tmp_warna_remark,
                                                    'lot'           => trim($lot_baru),
                                                    'nama_grade'    => $tmp_grade,
                                                    'qty'           => $sum_qty,
                                                    'uom'           => $tmp_uom,
                                                    'qty2'          => $sum_qty2,
                                                    'uom2'          => $tmp_uom2,
                                                    'qty_jual'      => $sum_qty_jual,
                                                    'uom_jual'      => $tmp_uom_jual,
                                                    'qty2_jual'     => $sum_qty2_jual,
                                                    'uom2_jual'     => $tmp_uom2_jual,
                                                    'lokasi'        => $lokasi_stock,
                                                    'lokasi_fisik'  => 'PORT',
                                                    'lebar_greige'  => '',
                                                    'lebar_jadi'      => $tmp_lebar_jadi,
                                                    'uom_lebar_jadi'  => $tmp_uom_lebar_jadi,
                                                    'sales_order'     => '',
                                                    'sales_group'     => $tmp_sales_group
                            );

                            $note_adj  = 'ADJ | Dibuat dari Fitur JOIN LOT. No.'.$kode_join;
                            $type_adjustment = 9; // JOIN
                            $data_adj[] = array('kode_adjustment'   => $kode_adjustment,
                                                'create_date'       => $tgl,
                                                'lokasi_adjustment' => $nama_departemen,
                                                'kode_lokasi'       => $lokasi_stock,
                                                'note'              => $note_adj,
                                                'status'            => "done",
                                                'nama_user'         => $nama_user['nama'],
                                                'id_type_adjustment'=> $type_adjustment);

                            $data_adj_items[] = array('kode_adjustment'   => $kode_adjustment,
                                                    'quant_id'          => $start,
                                                    'kode_produk'       => $tmp_kode_produk,
                                                    'lot'               => trim($lot_baru),
                                                    'uom'               => $tmp_uom,
                                                    'qty_data'          => 0,
                                                    'qty_adjustment'    => $sum_qty,
                                                    'uom2'              => $tmp_uom2,
                                                    'qty_data2'         => 0,
                                                    'qty_adjustment2'   => $sum_qty2,
                                                    'move_id'           => $move_id,
                                                    'qty_move'          => $sum_qty,
                                                    'qty2_move'         => $sum_qty2,
                                                    'row_order'         => $row_order_adj);

                            $update_join_lot = array(                                                  
                                                    'quant_id'  => $start,
                                                    'kode_produk'   => $tmp_kode_produk,
                                                    'nama_produk'   => $tmp_nama_produk,
                                                    'corak_remark'  => $tmp_corak_remark.''.$tanda_join,
                                                    'warna_remark'  => $tmp_warna_remark,
                                                    'lot'           => $lot_baru,
                                                    'qty'           => $sum_qty,
                                                    'uom'           => $tmp_uom,
                                                    'qty2'          => $sum_qty2,
                                                    'uom2'          => $tmp_uom2,
                                                    'qty_jual'      => $sum_qty_jual,
                                                    'uom_jual'      => $tmp_uom_jual,
                                                    'qty2_jual'     => $sum_qty2_jual,
                                                    'uom2_jual'     => $tmp_uom2_jual,
                                                    'lebar_jadi'    => $tmp_lebar_jadi,
                                                    'uom_lebar_jadi'=> $tmp_uom_lebar_jadi,
                                                    'sales_group'   => $tmp_sales_group,
                                                    'grade'         => $tmp_grade,
                                                    'status'        => "done",
                                                    'tanggal_transaksi' => $tgl
                            );

                           
                        
                            // simpan stock move
                            if(!empty($data_sm)){
                                $result = $this->_module->create_stock_move_batch_2($data_sm);
                                if($result['message'] != null){
                                    throw new \Exception('Simpan Data Gagal !', 200);                       
                                }
                                $result  = $this->_module->create_stock_move_produk_batch_2($data_smp);
                                if($result['message'] != null){
                                    throw new \Exception('Simpan Data Gagal !', 200);                       
                                }
                                $result = $this->_module->simpan_stock_move_items_batch_2($data_smi);
                                if($result['message'] != null){
                                    throw new \Exception('Simpan Data Gagal !', 200);                       
                                }
                            }else{
                                throw new \Exception('Simpan Data Gagal !', 200);                       
                            }


                            // simpan stock quant
                            if(!empty($data_stock_quant)){
                                // $sql_stock_quant_batch = rtrim($sql_stock_quant_batch, ', ');
                                $result = $this->_module->simpan_stock_quant_batch_2($data_stock_quant);
                                if($result['message'] != null){
                                    throw new \Exception('Simpan Data Gagal !', 200);                       
                                }
                            }else{
                                throw new \Exception('Simpan Data Gagal !', 200);                       
                            }


                            if(!empty($data_adj)){
                                $result1 = $this->m_joinLot->simpan_adjustment_batch($data_adj);
                                if($result1['message'] != null){
                                    throw new \Exception('Simpan Data Gagal !', 200);                       
                                }
                                if(!empty($data_adj_items)){
                                    $result2 = $this->m_joinLot->simpan_adjustment_items_batch($data_adj_items);
                                    if($result2['message'] != null){
                                        throw new \Exception('Simpan Data Gagal !', 200);                       
                                    }
                                }
                            }else{
                                throw new \Exception('Simpan Data Gagal !', 200);
                            }
                                
                            // update stockquant
                            $this->m_joinLot->update_stock_quant_by_kode($update_sq);

                            // update join lot
                            $this->m_joinLot->update_join_lot_by_kode($update_join_lot,$kode_join);


                            //create log history adjustment 
                            $ip         = $this->input->ip_address();

                            $note_log_adj = $kode_adjustment." ini dibuat dari Fitur Join Lot  <br> No. ".$kode_adjustment_tmp." => ".$kode_adjustment_tmp2;
                            $date_log        = date('Y-m-d H:i:s');
                            $insert_log_adj[] = array(
                                                    'datelog'   => $date_log,
                                                    'main_menu_sub_kode'    => 'mm72',
                                                    'kode'                  => $kode_adjustment,
                                                    'jenis_log'             => 'create',
                                                    'note'                  => $note_log_adj,
                                                    'nama_user'             => $nama_user['nama'] ?? '',
                                                    'ip_address'            => $ip);

                            $total_adj = $row_order_adj + 1;

                            $note_log_adj = "Generate Adjustment ini di generate otomatis dari Fitur Join Lot | Jumlah Adjustment  ".$total_adj;

                            $insert_log_adj[] = array(
                                                    'datelog'   => $date_log,
                                                    'main_menu_sub_kode'    => 'mm72',
                                                    'kode'                  => $kode_adjustment,
                                                    'jenis_log'             => 'generate',
                                                    'note'                  => $note_log_adj,
                                                    'nama_user'             => $nama_user['nama'] ?? '',
                                                    'ip_address'            => $ip);

                            $this->_module->simpan_log_history_batch_2($insert_log_adj);

                            $jenis_log = "generate";
                            $note_log  = "Generate Join Lot ".$kode_join;
                            $data_history = array(
                                                'datelog'   => date("Y-m-d H:i:s"),
                                                'kode'      => $kode_join,
                                                'jenis_log' => $jenis_log,
                                                'note'      => $note_log );
                            $this->_module->gen_history_ip($sub_menu,$username,$data_history);

                            $callback = array('status' => 'success', 'message' => 'Join Lot berhasil di Generate !', 'icon' =>'fa fa-success', 'type' => 'success');

                        }else{
                            throw new \Exception('Join Lot Gagal Di Simpan !', 200);
                        }


                    }else if(count($items_join) == 1){
                        throw new \Exception('Barcode / Lot yang akan di Join harus lebih dari 1 !', 200);
                    }else{
                        throw new \Exception('Barcode / Lot yang akan di Join masih Kosong !', 200);
                    }

                }
                // finish transaction
                if (!$this->_module->finishTransaction()) {
                    throw new \Exception('Gagal generate Data', 500);
                }
                $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($callback));
            }
        }catch(Exception $ex){
            $this->_module->finishRollBack();
            $this->_module->rollbackTransaction();
            $this->output->set_status_header($ex->getCode() ??  500)
                    ->set_content_type('application/json','utf-8')
                    ->set_output(json_encode(array('status'=>'failed','message'=>$ex->getMessage(), 'icon'=>'fa fa-warning', 'type'=>'danger')));
        } finally {
            // unlock table
            $this->_module->unlock_tabel();

        }
    }

    public function print_modal()
    {
    	$data['kode_join']  = $this->input->post('kode');
        $data['kode_k3l']   = $this->_module->get_list_kode_k3l();        
        $data['desain_barcode']   = $this->_module->get_list_desain_barcode_by_type('LBK');    
        return $this->load->view('modal/v_join_lot_print_modal', $data);
    }


    public function print_modal2()
    {
        $data['data_print'] = ($this->input->post('data'));
        $data['kode_k3l']   = $this->_module->get_list_kode_k3l();    
        $data['desain_barcode']   = $this->_module->get_list_desain_barcode_by_type('LBK');    
        return $this->load->view('modal/v_join_lot_print2_modal', $data);
    }
    


    function print_barcode_join()
    {
        try{
            if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
                // session habis
                throw new \Exception('Waktu Anda Telah Habis', 401);
            }else{

                $kode_join  = $this->input->post('kode_join');
                $k3l        = $this->input->post('k3l');
                $desain_barcode  = $this->input->post('desain_barcode');

                if(empty($kode_join)){
                    throw new \Exception('Data Print Lot Kosong !', 200);
                }else if(empty($desain_barcode)){
                    throw new \Exception('Desain Barcode K3l Harus dipilih !', 200);
                }else if(empty($k3l)){
                    throw new \Exception('K3L Harus dipilih  !', 200);
                }else{

                    $data = $this->m_joinLot->get_data_join_lot_by_kode($kode_join);

                    if(empty($data)){
                        throw new \Exception('Data Join Lot tidak ditemukan !', 200);
                    }else{
                        $quant_id = $data->quant_id;
                        $get = $this->_module->get_stock_quant_by_id($quant_id)->result();
                        if(empty($get)){
                            throw new \Exception('Data tidak ditemukan !', 200);
                        }
                        
                        $data_print = $this->print_barcode($desain_barcode,$get,$kode_join,$k3l);
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

    function print_barcode_join2()
    {

        try{
            if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
                // session habis
                throw new \Exception('Waktu Anda Telah Habis', 401);
            }else{

                $data       = $this->input->post('data');
                $k3l        = $this->input->post('k3l');
                $desain_barcode  = $this->input->post('desain_barcode');

                if(empty($desain_barcode)){
                    throw new \Exception('Desain Barcode Harus dipilih !', 200);                
                }else{

                    if(empty($data)){
                        throw new \Exception('Data Join Lot tidak ditemukan !', 200);
                    }else{
                        
                        $data_print = $this->print_barcode2($desain_barcode,$data,$k3l);
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

    function print_barcode2($desain_barcode,$kode,$k3l){

        $desain_barcode = strtolower($desain_barcode);
        $code = new Code\Code128New();
        $this->prints->setView('print/'.$desain_barcode);
        $data_print_array = array();
        $data_qty2_jual = array();
        for($a=0; $a<count($kode); $a++){
            $dp     = $this->m_joinLot->get_data_join_lot_by_kode($kode[$a]);
            $gen_code = $code->generate($dp->lot, "", 60, "vertical");
            $tanggal = date('Ymd', strtotime($dp->tanggal_transaksi));
            $data_print_array = array(
                        'pattern' => $dp->corak_remark,
                        'isi_color' => !empty($dp->warna_remark)? $dp->warna_remark : '&nbsp',
                        'isi_satuan_lebar' => 'WIDTH ('.$dp->uom_lebar_jadi.')',
                        'isi_lebar' => !empty($dp->lebar_jadi)? $dp->lebar_jadi : '&nbsp',
                        'isi_satuan_qty1' => 'QTY ['.$dp->uom_jual.']',
                        'isi_qty1' => round($dp->qty_jual,2),
                        'barcode_id' => $dp->lot,
                        'tanggal_buat' => $tanggal,
                        'no_pack_brc' => $kode[$a],
                        'barcode' => $gen_code,
                        'k3l' => $k3l
            );
            if(!empty((double)$dp->qty2_jual)){
                $data_qty2_jual = array('isi_satuan_qty2' => 'QTY2 ['.$dp->uom2_jual.']', 'isi_qty2' => round($dp->qty2_jual,2));
                $data_print_array = array_merge($data_print_array,$data_qty2_jual);
            }
            // break;
            $this->prints->addDatas($data_print_array);
        }
     
        return $this->prints->generate();
    }


    function print_barcode($desain_barcode,$data_stock,$kode_join,$k3l)
    {
        $kode_k3l  = $k3l;
        $desain_barcode = strtolower($desain_barcode);
        $code = new Code\Code128New();
        $this->prints->setView('print/'.$desain_barcode);
        $data_print_array = array();
        $data_qty2_jual = array();
        foreach($data_stock as $row){
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
                        'no_pack_brc' => $kode_join,
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
     
        return $this->prints->generate();
    }
}       