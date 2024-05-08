<?php defined('BASEPATH') or exit ('No Direct Script Acces Allowed');

/**
 * 
 */
class Barcodemanual extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->is_loggedin();//cek apakah user sudah login
        $this->load->model("_module");
        $this->load->model("m_inlet");
        $this->load->model("m_barcodemanual");
        $this->load->model("m_adjustment");
        $this->load->library("token");
        $this->load->library('prints');
        $this->load->library('barcode');
        $this->load->model('m_accessmenu');
        $this->load->model('m_outlet');
	}

    public function index()
    {
        $sub_menu               = $this->uri->segment(2);
        $data['id_dept']        = 'BRCM';
        $data['sales_group']    = $this->_module->get_list_sales_group_by_view('1');
        $data['list_status']    = $this->m_inlet->get_list_status_by_menu($sub_menu,'BRCM');
        $this->load->view('manufacturing/v_barcode_manual', $data);
    }

    function get_data()
    {
        if(isset($_POST['start']) && isset($_POST['draw'])){
            $list = $this->m_barcodemanual->get_datatables();
            $data = array();
            $no   = $_POST['start'];
            foreach ($list as $field) {
                $kode_encrypt = encrypt_url($field->kode);
                $no++;
                $row = array();
                $row[] = $no;
                $row[] = '<a href="'.base_url('manufacturing/barcodemanual/edit/'.$kode_encrypt).'">'.$field->kode.'</a>';
                $row[] = $field->tanggal_buat;
                $row[] = $field->tanggal_transaksi;
                $row[] = $field->nama_sales_group;
                $row[] = $field->name_type;
                $row[] = $field->tot_batch;
                $row[] = $field->kode_adjustment;
                $row[] = $field->notes;
                $row[] = $field->nama_user;
                $row[] = $field->nama_status;
                $data[] = $row;
            }
    
            $output = array(
                "draw" => $_POST['draw'],
                "recordsTotal" => $this->m_barcodemanual->count_all(),
                "recordsFiltered" => $this->m_barcodemanual->count_filtered(),
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
        $data['id_dept']    = 'BRCM';       
        $data['access']     = $this->cek_akses_menu();
        $data['type']       = $this->m_adjustment->get_list_type_adjustment('view_barcode_manual','1');
        $data['sales_group']= $this->_module->get_list_sales_group_by_view('1');
        $this->load->view('manufacturing/v_barcode_manual_add', $data);
    }
    

    function get_data_batch_barcode_manual()
    {

        if(isset($_POST['start']) && isset($_POST['draw'])){
            $kode = $this->input->post('kode');
            $list = $this->m_barcodemanual->get_list_mrp_manual_batch($kode);
            $data = array();
            $no = $_POST['start'];
            foreach ($list as $field) {
                $no++;
                $row = array();
                $row[] = $no;
                $row[] = $field->no_batch;
                $row[] = '<a href="#" class="edit_batch" data-row="' . $field->row_order . '"  data-title="Edit"> ['.$field->kode_produk.'] '.$field->nama_produk.' </a>';
                $row[] = $field->corak_remark;
                $row[] = $field->warna_remark;
                $row[] = $field->nama_quality;
                $row[] = $field->grade;
                $row[] = $field->jml_pcs;
                $row[] = $field->qty.' '.$field->uom;
                $row[] = $field->qty2.' '.$field->uom2;
                $row[] = $field->qty_jual.' '.$field->uom_jual;
                $row[] = $field->qty2_jual.' '.$field->uom2_jual;
                $row[] = $field->lebar_jadi.' '.$field->uom_lebar_jadi;
                $row[] = $field->kode_k3l;
                if($field->status == 'draft' OR $field->status == 'process' ){
                    $row[] = '<button type="button" class="btn btn-danger btn-xs delete_batch" data-row="' . $field->row_order . '" data-title="Hapus"><i class="fa fa-trash"></></button>';
                }else{
                    $row[] = '';
                }
                $data[] = $row;
            }
    
            $output = array(
                "draw" => $_POST['draw'],
                "recordsTotal" => $this->m_barcodemanual->count_all2($kode),
                "recordsFiltered" => $this->m_barcodemanual->count_filtered2($kode),
                "data" => $data,
            );
            //output dalam format JSON
            echo json_encode($output);

        }else{
            die();
        }
        
    }

    function get_data_batch_items_barcode_manual()
    {

        if(isset($_POST['start']) && isset($_POST['draw'])){
            $kode = $this->input->post('kode');
            $list = $this->m_barcodemanual->get_list_mrp_manual_batch_items($kode);
            $data = array();
            $no = $_POST['start'];
            foreach ($list as $field) {

                $no++;
                $row = array();
                $row[] = $no;
                $row[] = $field->no_batch;
                $row[] = '<a href="#" class="edit_batch_items" data-lot="' . $field->lot . '"  data-title="Edit"> ['.$field->kode_produk.'] '.$field->nama_produk.' </a>';;
                $row[] = $field->corak_remark;
                $row[] = $field->warna_remark;
                $row[] = $field->grade;
                $row[] = $field->lot;
                $row[] = $field->qty.' '.$field->uom;
                $row[] = $field->qty2.' '.$field->uom2;
                $row[] = $field->qty_jual.' '.$field->uom_jual;
                $row[] = $field->qty2_jual.' '.$field->uom2_jual;
                $row[] = $field->lebar_jadi.' '.$field->uom_lebar_jadi;
                $row[] = $field->kode_k3l;
                $row[] = $field->quant_id;
                $data[] = $row;
            }
    
            $output = array(
                "draw" => $_POST['draw'],
                "recordsTotal" => $this->m_barcodemanual->count_all3($kode),
                "recordsFiltered" => $this->m_barcodemanual->count_filtered3($kode),
                "data" => $data,
            );
            //output dalam format JSON
            echo json_encode($output);

        }else{
            die();
        }
        
    }

    public function edit($id = null)
    {
        if(!isset($id)) show_404();
        $data['access']     = $this->cek_akses_menu();
        $kode_decrypt       = decrypt_url($id);
        $data['id']         = $id;
        $data['id_dept']    = 'BRCM';
        $data['mms']        = $this->_module->get_data_mms_for_log_history('BRCM');// get mms by dept untuk menu yg beda-beda
        $data['type']       = $this->m_adjustment->get_list_type_adjustment('view_barcode_manual','1');
        $data['quality']    = $this->_module->get_list_quality();        
        $data['kode_k3l']   = $this->_module->get_list_kode_k3l();        
        $data['desain_barcode']   = $this->_module->get_list_desain_barcode_by_type('LBK');     
        $data['sales_group']= $this->_module->get_list_sales_group_by_view('1');
        $data['list_grade'] = $this->_module->get_list_grade();
        $data['mrpm']       = $this->m_barcodemanual->get_data_mrp_manual_by_id($kode_decrypt);
        // $data['mrpmb']      = $this->m_barcodemanual->get_data_mrp_manual_batch_by_id($kode_decrypt);
        $data['mrpmbi']     = $this->m_barcodemanual->get_data_mrp_manual_batch_items_by_id($kode_decrypt);
        $data['desain_barcode']   = $this->_module->get_list_desain_barcode_by_type('LBK');     
        $this->load->view('manufacturing/v_barcode_manual_edit', $data);
    }

    function save_barcode_manual()
    {
        try{
            if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
                // session habis
                throw new \Exception('Waktu Anda Telah Habis', 401);
            }else{

                $kode       = $this->input->post('kode');
                $marketing  = $this->input->post('marketing');
                $type       = $this->input->post('type');
                $notes      = $this->input->post('notes');
                $tgl        = date('Y-m-d H:i:s');
                // start transaction
                $this->_module->startTransaction();

                $sub_menu  = $this->uri->segment(2);
                $username = addslashes($this->session->userdata('username')); 
                $nu        = $this->_module->get_nama_user($username)->row_array(); 
                $nama_user = addslashes($nu['nama']);

                // get nama sales_group by kode
                $nm_sales_group = $this->_module->get_nama_sales_Group_by_kode($marketing);
                $access         = $this->cek_akses_menu();
                if(empty($access->status)){
                    $callback = array('status' => 'failed', 'field'=>'', 'message' => 'PC ini tidak diizinkan membuat Barcode Manual !', 'icon' =>'fa fa-warning', 'type' => 'danger');
                }else if(empty($marketing)){
                    $callback = array('status' => 'failed', 'field'=>'marketing', 'message' => 'Marketing Harus dipilih !', 'icon' =>'fa fa-warning', 'type' => 'danger');
                }else if(empty($type)){
                    $callback = array('status' => 'failed', 'field' => 'type', 'message' => 'Alasan Harus dipilih !', 'icon' =>'fa fa-warning', 'type' => 'danger');
                }else if(empty($notes)){
                    $callback = array('status' => 'failed', 'field' => 'notes', 'message' => 'Notes Harus diisi !', 'icon' =>'fa fa-warning', 'type' => 'danger');
                }else{

                    // get nama type adjustment
                    $nm = $this->m_adjustment->get_type_adjustment_by_kode($type);
                    $nama_type_adjustment = $nm->name_type ?? '';

                    if(!empty($kode)){// update data

                        $mrpm = $this->m_barcodemanual->get_data_mrp_manual_by_id($kode);

                        if(empty($mrpm)){
                            $callback = array('status' => 'failed','message' => 'Data Barcode Manual tidak ditemukan !', 'icon' =>'fa fa-warning', 'type' => 'danger');
                        }else if($mrpm->status == 'done'){
                            $callback = array('status' => 'failed','message' => 'Maaf, Data Tidak Bisa Disimpan, Status Sudah Done !', 'icon' =>'fa fa-warning', 'type' => 'danger');
                        }else if($mrpm->status == 'cancel'){
                            $callback = array('status' => 'failed','message' => 'Maaf, Data Tidak Bisa Disimpan, Status Sudah Done !', 'icon' =>'fa fa-warning', 'type' => 'danger');
                        }else{

                            $data = array(
                                'sales_group'           => $marketing,
                                'notes'                 => $notes,
                                'id_type_adjustment'    => $type
                            );
                            $update = $this->m_barcodemanual->update_data_barcode_manual($kode,$data);
                            // if(empty($update)){
                            //     throw new \Exception('Gagal Mengubah data'.$update, 500);
                            // }

                            $jenis_log = "edit";
                            $note_log  = $kode." | ".$nm_sales_group." | ".$nama_type_adjustment." | ".$notes;
                            $data_history = array(
                                            'datelog'   => date("Y-m-d H:i:s"),
                                            'kode'      => $kode,
                                            'jenis_log' => $jenis_log,
                                            'note'      => $note_log  );
                            
                            // load in library
                            $this->_module->gen_history_ip($sub_menu,$username,$data_history);

                            if (!$this->_module->finishTransaction()) {
                                throw new \Exception('Gagal Mengubah Data2', 500);
                            }

                            $callback = array('status' => 'success', 'message' => 'Data Berhasil diubah !', 'icon' =>'fa fa-check', 'type' => 'success');
                        }

                    }else{ // create data

                        $kode = $this->token->noUrut('mrp_manual', date('ym'), true)->generate('BCM', '%04d')->get();
                        $data = array(
                            'kode'                  => $kode,
                            'tanggal_buat'          => $tgl,
                            'tanggal_transaksi'     => $tgl,
                            'sales_group'           => $marketing,
                            'notes'                 => $notes,
                            'nama_user'             => $nama_user,
                            'id_type_adjustment'    => $type
                        );
                        $insert = $this->m_barcodemanual->insert_data_barcode_manual($data);

                        if(!empty($insert)){
                            throw new \Exception('Gagal Menyimpan data', 500);
                        }

                        $jenis_log = "create";
                        $note_log  = $kode." | ".$nm_sales_group." | ".$nama_type_adjustment." | ".$notes;
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
                        $callback = array('message' => 'Data Berhasil Disimpan', 'icon' => 'fa fa-check', 'type' => 'success', 'isi'=> encrypt_url($kode) );
                    }
                }

                $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($callback));
                // finish transaction
                $this->_module->finishTransaction();
            }
        }catch(Exception $ex){
            $this->_module->finishTransaction();
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }


   
    function save_mrp_batch()
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
                $quality        = $this->input->post('quality');
                $grade          = $this->input->post('grade');
                $jml_pcs        = $this->input->post('jml_pcs');
                $qty            = $this->input->post('qty');
                $uom            = $this->input->post('uom');
                $qty2           = $this->input->post('qty2');
                $uom2           = $this->input->post('uom2');
                $qty_jual       = $this->input->post('qty_jual');
                $uom_jual       = $this->input->post('uom_qty_jual');
                $qty2_jual      = $this->input->post('qty2_jual');
                $uom2_jual      = $this->input->post('uom_qty2_jual');
                $lebar_jadi     = $this->input->post('lebar_jadi');
                $uom_lebar_jadi = $this->input->post('uom_lebar_jadi');
                $kode_k3l       = $this->input->post('kode_k3l');
                $row_order      = $this->input->post('row');

                // start transaction
                $this->_module->startTransaction();

                $sub_menu  = $this->uri->segment(2);
                $username = addslashes($this->session->userdata('username')); 

                $tgl            = date('Y-m-d H:i:s');
                $mrpm           = $this->m_barcodemanual->get_data_mrp_manual_by_id($kode);

                if(empty($mrpm)){
                    throw new \Exception('Data Barcode Manual tidak ditemukan !', 200);
                    // $callback = array('status' => 'failed','message' => 'Data Barcode Manual tidak ditemukan !', 'icon' =>'fa fa-warning', 'type' => 'danger');
                }else if($mrpm->status == 'done'){
                    throw new \Exception('Maaf, Data Tidak Bisa Disimpan, Status Sudah Done !', 200);
                    // $callback = array('status' => 'failed','message' => 'Maaf, Data Tidak Bisa Disimpan, Status Sudah Done !', 'icon' =>'fa fa-warning', 'type' => 'danger');
                }else if($mrpm->status == 'cancel'){
                    throw new \Exception('Maaf, Data Tidak Bisa Disimpan, Status Sudah Cancel !', 200);
                    // $callback = array('status' => 'failed','message' => 'Maaf, Data Tidak Bisa Disimpan, Status Sudah Done !', 'icon' =>'fa fa-warning', 'type' => 'danger');
                }else{
                    $access         = $this->cek_akses_menu();
                    if(empty($access->status)){
                        $callback = array('status' => 'failed', 'message' => 'PC ini tidak diizinkan membuat Barcode Manual !', 'icon' =>'fa fa-warning', 'type' => 'danger');
                    }else if(empty($kode_produk)){
                        $callback = array('status' => 'failed', 'message' => 'Kode Produk Harus diisi !', 'icon' => 'fa fa-warrning' , 'type' => 'danger');
                    }else if(empty($corak_remark)){
                        $callback = array('status' => 'failed', 'message' => 'Corak Remark Harus diisi !', 'icon' => 'fa fa-warning' , 'type' => 'danger');
                    }else if(empty($warna_remark)){
                        $callback = array('status' => 'failed', 'message' => 'Warna Remark Harus diisi !', 'icon' => 'fa fa-warning' , 'type' => 'danger');
                    }else if(empty($grade)){
                        $callback = array('status' => 'failed', 'message' => 'Grade Harus diisi !', 'icon' => 'fa fa-warning' , 'type' => 'danger');
                    }else if(empty($jml_pcs)){
                        $callback = array('status' => 'failed', 'message' => 'Jumlah Pcs Harus diisi !', 'icon' => 'fa fa-warning' , 'type' => 'danger');
                    }else if(empty($qty)){
                        $callback = array('status' => 'failed', 'message' => 'Qty Harus diisi !', 'icon' => 'fa fa-warning' , 'type' => 'danger');
                    }else if(empty($uom)){
                        $callback = array('status' => 'failed', 'message' => 'Uom Harus diisi !', 'icon' => 'fa fa-warning' , 'type' => 'danger');
                    }else if(empty($qty2)){
                        $callback = array('status' => 'failed', 'message' => 'Qty2 Harus diisi !', 'icon' => 'fa fa-warning' , 'type' => 'danger');
                    }else if(empty($uom2)){
                        $callback = array('status' => 'failed', 'message' => 'Uom2 Harus diisi !', 'icon' => 'fa fa-warning' , 'type' => 'danger');
                    }else if(empty($uom_lebar_jadi)){
                        $callback = array('status' => 'failed', 'message' => 'Uom Lebar Jadi Harus diisi !', 'icon' => 'fa fa-warning' , 'type' => 'danger');
                    }else{
                      
                        // nama_produk
                        $nm = $this->_module->cek_produk_by_kode_produk($kode_produk)->row_array();
                        $nama_produk = $nm['nama_produk'] ?? '';

                        if(empty($nama_produk)){
                            $callback = array('status' => 'failed', 'message' => 'Nama Produk tidak ditemukan !', 'icon' => 'fa fa-warning' , 'type' => 'danger');
                        }else{

                            if(empty($row_order)){// simpan baru
                                // get last row order + 1;
                                $last_row = $this->m_barcodemanual->get_row_order_mrp_batch_by_kode($kode);

                                $data = array('kode'          => $kode,
                                                'tanggal_buat'  => $tgl,
                                                'kode_produk'   => $kode_produk,
                                                'nama_produk'   => $nama_produk,
                                                'corak_remark'  => $corak_remark,
                                                'warna_remark'  => $warna_remark,
                                                'id_quality'    => $quality,
                                                'grade'         => $grade,
                                                'jml_pcs'       => $jml_pcs,
                                                'qty'           => $qty,
                                                'uom'           => $uom,
                                                'qty2'          => $qty2,
                                                'uom2'          => $uom2,
                                                'qty_jual'      => $qty_jual,
                                                'uom_jual'      => $uom_jual,
                                                'qty2_jual'     => $qty2_jual,
                                                'uom2_jual'     => $uom2_jual,
                                                'lebar_jadi'    => $lebar_jadi,
                                                'uom_lebar_jadi'=> $uom_lebar_jadi,
                                                'kode_k3l'      => $kode_k3l,
                                                'row_order'     => $last_row
                                );                     
        
                                $insert = $this->m_barcodemanual->insert_data_barcode_manual_batch($data);
                                if(!empty($insert)){
                                    throw new \Exception('Gagal Menyimpan data ', 500);
                                }
        
                                // update tot batch in header
                                $this->m_barcodemanual->update_total_batch($kode);
        
                                $jenis_log = "edit";
                                $note_log  = "Menambahkan Data <br> ".$kode." | ".$kode_produk.' '.$nama_produk." | ".$corak_remark." | ".$warna_remark." | Baris Ke ".$last_row;
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
                                $callback = array('status'=>'success', 'message' =>'Data Berhasil Disimpan !', 'icon'=> 'fa fa-check', 'type'=>'success');
                            }else{

                                // cek row
                                $mrpmb = $this->m_barcodemanual->get_data_mrp_manual_batch_by_row($kode,$row_order)->row();
                                if(empty($mrpm)){
                                    throw new \Exception('Data Barcode Manual tidak ditemukan !', 200);
                                }else{
                                    $data_update = array();
                                    $data = array('kode'          => $kode,
                                                    'kode_produk'   => $kode_produk,
                                                    'nama_produk'   => $nama_produk,
                                                    'corak_remark'  => $corak_remark,
                                                    'warna_remark'  => $warna_remark,
                                                    'id_quality'    => $quality,
                                                    'grade'         => $grade,
                                                    'jml_pcs'       => $jml_pcs,
                                                    'qty'           => $qty,
                                                    'uom'           => $uom,
                                                    'qty2'          => $qty2,
                                                    'uom2'          => $uom2,
                                                    'qty_jual'      => $qty_jual,
                                                    'uom_jual'      => $uom_jual,
                                                    'qty2_jual'     => $qty2_jual,
                                                    'uom2_jual'     => $uom2_jual,
                                                    'lebar_jadi'    => $lebar_jadi,
                                                    'uom_lebar_jadi'=> $uom_lebar_jadi,
                                                    'kode_k3l'      => $kode_k3l,
                                                    'row_order'     => $row_order
                                    );                     

                                    array_push($data_update,$data);
                                    $update = $this->m_barcodemanual->update_data_barcode_manual_batch($data_update,$kode);
                                    // if(empty($update)){
                                    //     throw new \Exception('Gagal Mengubah data ', 500);
                                    // }

                                    $jenis_log = "edit";
                                    $note_log  = "Edit Data Baris Ke ".$row_order."<br> ".$kode." | ".$kode_produk.' '.$nama_produk." | ".$corak_remark." | ".$warna_remark;
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

                }

                $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($callback));
                // finish transaction
                $this->_module->finishTransaction();
            }
            
        }catch(Exception $ex){
            $this->_module->finishTransaction();
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }


    function save_mrp_batch_items()
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
                $kode_k3l       = $this->input->post('kode_k3l');
                $row_order      = $this->input->post('row');
                $lot            = $this->input->post('lot');
                $quant_id            = $this->input->post('quant_id');

                // start transaction
                $this->_module->startTransaction();

                $sub_menu  = $this->uri->segment(2);
                $username = addslashes($this->session->userdata('username')); 

                $tgl            = date('Y-m-d H:i:s');
                $mrpm           = $this->m_barcodemanual->get_data_mrp_manual_by_id($kode);

                if(empty($mrpm)){
                    throw new \Exception('Data Barcode Manual tidak ditemukan !', 200);
                }else if($mrpm->status == 'process'){
                    throw new \Exception('Maaf, Data Tidak Bisa Disimpan, Status masih Process !', 200);
                }else if($mrpm->status == 'cancel'){
                    throw new \Exception('Maaf, Data Tidak Bisa Disimpan, Status Sudah Cancel !', 200);
                }else{
                    $access         = $this->cek_akses_menu();
                    if(empty($access->status)){
                        $callback = array('status' => 'failed', 'message' => 'PC ini tidak diizinkan membuat Barcode Manual !', 'icon' =>'fa fa-warning', 'type' => 'danger');
                    }else if(empty($kode_produk)){
                        $callback = array('status' => 'failed', 'message' => 'Produk Kosong !', 'icon' => 'fa fa-warrning' , 'type' => 'danger');
                    }else if(empty($corak_remark)){
                        $callback = array('status' => 'failed', 'message' => 'Corak Remark Harus diisi !', 'icon' => 'fa fa-warning' , 'type' => 'danger');
                    }else if(empty($warna_remark)){
                        $callback = array('status' => 'failed', 'message' => 'Warna Remark Harus diisi !', 'icon' => 'fa fa-warning' , 'type' => 'danger');
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
                                // cek row
                                $mrpmb = $this->m_barcodemanual->get_data_mrp_manual_batch_items_by_row($kode,$lot)->row();
                                if(empty($mrpmb)){
                                    throw new \Exception('Data Barcode Manual tidak ditemukan !', 200);
                                }else{
                                    // get data quant sebelumnya
                                    $note_before = $get->kode_produk.' '.$get->nama_produk." | ".$get->corak_remark." | ".$get->warna_remark. " | ".$get->qty_jual." ".$get->uom_jual. " | ".$get->qty2_jual." ".$get->uom2_jual. " | ".$get->lebar_jadi." ".$get->uom_lebar_jadi;

                                    $data_update = array();
                                    $data = array(
                                                    'corak_remark'  => $corak_remark,
                                                    'warna_remark'  => $warna_remark,
                                                    'qty_jual'      => $qty_jual,
                                                    'uom_jual'      => $uom_jual,
                                                    'qty2_jual'     => $qty2_jual,
                                                    'uom2_jual'     => $uom2_jual,
                                                    'lebar_jadi'    => $lebar_jadi,
                                                    'uom_lebar_jadi'=> $uom_lebar_jadi,
                                                    'kode_k3l'      => $kode_k3l,
                                                    'row_order'     => $row_order
                                    );                     

                                    array_push($data_update,$data);
                                    $update = $this->m_barcodemanual->update_data_barcode_manual_items_batch($data_update,$kode,$lot);
                                    
                                    $data_update_quant = array(
                                                    'corak_remark'  => $corak_remark,
                                                    'warna_remark'  => $warna_remark,
                                                    'qty_jual'      => $qty_jual,
                                                    'uom_jual'      => $uom_jual,
                                                    'qty2_jual'     => $qty2_jual,
                                                    'uom2_jual'     => $uom2_jual,
                                                    'lebar_jadi'    => $lebar_jadi,
                                                    'uom_lebar_jadi'=> $uom_lebar_jadi,
                                    );   

                                    $update = $this->m_barcodemanual->update_data_stock_quant($data_update_quant,$quant_id,$lot);

                                    // if(empty($update)){
                                    //     throw new \Exception('Gagal Mengubah data ', 500);
                                    // }

                                    $jenis_log = "edit";
                                    $note_after = $kode_produk.' '.$nama_produk." | ".$corak_remark." | ".$warna_remark. " | ".$qty_jual." ".$uom_jual. " | ".$qty2_jual." ".$uom2_jual. " | ".$lebar_jadi." ".$uom_lebar_jadi;
                                    $note_log  = "Edit Data Batch Items lot ".$lot."<br> ".$note_before." <b> -> </b> <br> ".$note_after;
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

                }

                $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($callback));
                // finish transaction
                $this->_module->finishTransaction();
            }
            
        }catch(Exception $ex){
            $this->_module->finishTransaction();
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }

    function delete_mrp_batch()
    {
        try{
            if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
                // session habis
                throw new \Exception('Waktu Anda Telah Habis', 401);
            }else{

                $kode   = $this->input->post('kode');
                $row    = $this->input->post('row');

                // start transaction
                $this->_module->startTransaction();

                $sub_menu  = $this->uri->segment(2);
                $username = addslashes($this->session->userdata('username')); 
 
                $tgl            = date('Y-m-d H:i:s');
                $mrpm           = $this->m_barcodemanual->get_data_mrp_manual_by_id($kode);

                // cek row
                $mrpmb          = $this->m_barcodemanual->get_data_mrp_manual_batch_by_row($kode,$row)->row();
                $access         = $this->cek_akses_menu();
                if(empty($access->status)){
                    throw new \Exception('PC ini tidak diizinkan membuat Barcode Manual ', 200);
                }else if(empty($mrpm)){
                    throw new \Exception('Data Barcode Manual tidak ditemukan !', 200);
                }else if($mrpm->status == 'done'){
                    throw new \Exception('Maaf, Data Tidak Bisa Dihapus, Status Sudah Done !', 200);
                }else if($mrpm->status == 'cancel'){
                    throw new \Exception('Maaf, Data Tidak Bisa Dihapus, Status Sudah Cancel !', 200);
                }else if(empty($mrpmb)){
                    throw new \Exception('Data yang akan dihapus tidak ditemukan !', 200);
                }else{

                    $kode_produk = $mrpmb->kode_produk;
                    $nama_produk = $mrpmb->nama_produk;
                    $corak_remark = $mrpmb->corak_remark;
                    $warna_remark = $mrpmb->warna_remark;

                    $delete = $this->m_barcodemanual->delete_data_barcode_manual_batch($kode,$row);
                    if(!empty($delete)){
                        throw new \Exception('Gagal Menghapus data ', 500);
                    }

                    // update tot batch in header
                    $this->m_barcodemanual->update_total_batch($kode);
    
                    $jenis_log = "cancel";
                    $note_log  = "Mengahapus Data <br> ".$kode." | ".$kode_produk.' '.$nama_produk." | ".$corak_remark." | ".$warna_remark;
                    $data_history = array(
                                     'datelog'   => date("Y-m-d H:i:s"),
                                     'kode'      => $kode,
                                     'jenis_log' => $jenis_log,
                                     'note'      => $note_log  );
                     
                    // load in library
                    $this->_module->gen_history_ip($sub_menu,$username,$data_history);


                    if (!$this->_module->finishTransaction()) {
                        throw new \Exception('Gagal Menghapus Data2', 500);
                    }

                    $callback = array('status'=>'success', 'message' =>'Data Berhasil dihapus !', 'icon'=> 'fa fa-check', 'type'=>'success');

                }

                $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($callback));
                // finish transaction
                $this->_module->finishTransaction();

            }

        }catch(Exception $ex){
            $this->_module->finishTransaction();
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }

    function add_batch_modal()
    {
        $kode               = $this->input->post('kode');
        $data['kode']       = $kode;
        $data['kode_k3l']   = $this->_module->get_list_kode_k3l(); 
        $uom_konversi                   = $this->m_outlet->get_list_uom_konversi();
        $data['uom_konversi']           = json_encode($uom_konversi);       
        return $this->load->view('modal/v_barcode_manual_modal',$data);
    }

    function edit_batch_modal()
    {
        $kode               = $this->input->post('kode');
        $row                = $this->input->post('row');
        $data['kode']       = $kode;
        $data['row_mb']     = $row;
        $data['data_mbb']   = $this->m_barcodemanual->get_data_mrp_manual_batch_by_row($kode,$row)->row();
        $data['kode_k3l']   = $this->_module->get_list_kode_k3l();        
        $uom_konversi                   = $this->m_outlet->get_list_uom_konversi();
        $data['uom_konversi']           = json_encode($uom_konversi);       
        return $this->load->view('modal/v_barcode_manual_edit_modal',$data);
    }

    function edit_batch_items_modal()
    {
        $kode               = $this->input->post('kode');
        $lot                = $this->input->post('lot');
        $data['kode']       = $kode;
        $data['data_mbi']   = $this->m_barcodemanual->get_data_mrp_manual_batch_items_by_row($kode,$lot)->row();
        $data['kode_k3l']   = $this->_module->get_list_kode_k3l();        
        return $this->load->view('modal/v_barcode_manual_edit_items_modal',$data);
    }


    function get_list_quality_select2()
    {
        $nama = addslashes($this->input->post('prod'));
        $callback =  $this->_module->get_list_quality($nama);
        if(!$callback){
            $callback = array();
        }
        echo json_encode($callback);
    }


    function get_produk_select_mrp_manual_batch()
    {
	    $prod = addslashes($this->input->post('prod'));
   		$callback = $this->m_barcodemanual->get_list_produk_gudang_jadi($prod);
        echo json_encode($callback);
    }
    

    function generate_barcode_manual()
    {
        try{
            if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
                // session habis
                throw new \Exception('Waktu Anda Telah Habis', 401);
            }else{
                $kode   = $this->input->post('kode');

                // start transaction
                $this->_module->startTransaction();
                
                $sub_menu  = $this->uri->segment(2);
                $username = addslashes($this->session->userdata('username')); 
                $nu        = $this->_module->get_nama_user($username)->row_array(); 
                $nama_user = addslashes($nu['nama']);

                //lock table
                $this->_module->lock_tabel("mrp_manual WRITE, mrp_manual_batch WRITE, mrp_manual_batch as mmb WRITE, mst_quality as q WRITE,  mrp_manual_batch_items WRITE, stock_quant WRITE, adjustment WRITE, adjustment_items WRITE, stock_move WRITE, departemen as d WRITE, token_increment WRITE, stock_move_produk WRITE, stock_move_items WRITE,log_history WRITE, user WRITE, main_menu_sub WRITE, mst_access_menu WRITE ");
 
                $tgl        = date('Y-m-d H:i:s');
                $mrpm       = $this->m_barcodemanual->get_data_mrp_manual_by_id($kode);
                $mrpmb      = $this->m_barcodemanual->get_data_mrp_manual_batch_by_id($kode);
                $access         = $this->cek_akses_menu();
                if(empty($access->status)){
                    throw new \Exception('PC ini tidak diizinkan membuat Barcode Manual ', 200);
                }else if(empty($mrpm)){
                    throw new \Exception('Data Barcode Manual tidak ditemukan !', 200);
                }else if($mrpm->status == 'done'){
                    $callback = array('status' => 'failed','message' => 'Maaf, Data Tidak Bisa Generate, Status Sudah Done !', 'icon' =>'fa fa-warning', 'type' => 'danger');
                }else if($mrpm->status == 'cancel'){
                    $callback = array('status' => 'failed','message' => 'Maaf, Data Tidak Bisa Generate, Status Sudah Cancel !', 'icon' =>'fa fa-warning', 'type' => 'danger');
                }else if(empty($mrpmb)){
                    throw new \Exception('Data Batch  Masih Kosong  !', 200);
                }else{

                    $type_adjustment= $mrpm->id_type_adjustment ;
                    $sales_group    = $mrpm->sales_group ?? '';

                    $kode_substr    = substr($kode,3);// example BCM23110020 => 23110020
                    $kode_reverse   = strrev($kode_substr);
                    $data_items     = array();
                    $data_update_batch = array();
                    $loop_batch     = 1;
                    $data_stock_quant   = array();
                    $data_adj_head      = array();
                    $data_adj_items     = array();
                    $data_stock_move    = array();
                    $data_stock_move_produk    = array();
                    $data_stock_move_items     = array();

                    // get quant_id
                    $quant_id_new     = $this->_module->get_last_quant_id();

                    // get kode adj
                    $get_kode_adjustment   = $this->_module->get_kode_adj();  
                    $kode_adjustment   = substr("0000" . $get_kode_adjustment,-4);                  
                    $kode_adjustment   = "ADJ/".date("y") . '/' .  date("m") . '/' . $kode_adjustment;

                    // get kode move id
    	        	$last_move   = $this->_module->get_kode_stock_move();
    	            $move_id     = "SM".$last_move; //Set kode stock_move

                    // cek lokasi sock by dept 
                    $cek_lc = $this->_module->get_nama_dept_by_kode('GJD')->row_array();
                    $nama_departemen = $cek_lc['nama'] ?? '';
                    $lokasi_adj      = $cek_lc['adjustment_location'] ?? '';
                    $lokasi_stock    = $cek_lc['stock_location'] ?? '';

                    if(empty($nama_departemen) or empty($lokasi_adj) or empty($lokasi_stock)){
                        throw new \Exception('Lokasi Adjustment '.$nama_departemen.' tidak ditemukan  !', 200);
                    }

                    // adjustment header
                    $data_adj_head[]      = array(
                                        'kode_adjustment'   => $kode_adjustment,
                                        'create_date'       => $tgl,
                                        'lokasi_adjustment' => $nama_departemen,
                                        'kode_lokasi'       => $lokasi_stock,
                                        'id_type_adjustment'=> $type_adjustment,
                                        'note'              => 'ADJ | Mengadakan. Dibuat dari Fitur Barcode Manual. No.'.$kode,
                                        'status'            => 'done',
                                        'nama_user'         => $nama_user
                    );

                    $total_pcs = 0;
                    $row_order_smi   = 1;
                    foreach($mrpmb as $mb){

                        $counter    = sprintf("%05d",$loop_batch);
                        $no_batch   = $kode_reverse.''.$counter;

                        // update no_batch
                        $data_update = array( 'no_batch'  => $no_batch, 'row_order' => $mb->row_order );
                        array_push($data_update_batch,$data_update );

                        $row_order_items = 1;
                        $jml_pcs = $mb->jml_pcs;
                        $total_pcs = $total_pcs + $mb->jml_pcs;
                        for($loop=1; $loop<=$jml_pcs; $loop++){

                            $grade      = $mb->grade;

                            if($grade == 'A'){
                                $barcode_id = $this->token->noUrut('stock_quant_a', date('my'), true)->generate('', '%05d')->get();
                            }else if($grade == 'B'){
                                $barcode_id = $this->token->noUrut('stock_quant_b', date('my'), true)->generate($grade, '%05d')->get();
                            }else if($grade == 'C'){
                                $barcode_id = $this->token->noUrut('stock_quant_c', date('my'), true)->generate($grade, '%05d')->get();
                            }else{
                                throw new \Exception('Grade tidak Valid !', 200);
                            }
                            $corak_remark = $mb->corak_remark.' '.$mb->nama_quality;

                            $data_items[] = array( 
                                        'kode'          => $kode,
                                        'tanggal_buat'  => $tgl,
                                        'no_batch'      => $no_batch,
                                        'quant_id'      => $quant_id_new,
                                        'lot'           => $barcode_id,
                                        'grade'         => $mb->grade,
                                        'kode_produk'   => $mb->kode_produk,
                                        'nama_produk'   => $mb->nama_produk,
                                        'corak_remark'  => $corak_remark,
                                        'warna_remark'  => $mb->warna_remark,
                                        'qty'           => $mb->qty,
                                        'uom'           => $mb->uom,
                                        'qty2'          => $mb->qty2,
                                        'uom2'          => $mb->uom2,
                                        'qty_jual'      => $mb->qty_jual,
                                        'uom_jual'      => $mb->uom_jual,
                                        'qty2_jual'     => $mb->qty2_jual,
                                        'uom2_jual'     => $mb->uom2_jual,
                                        'lebar_jadi'    => $mb->lebar_jadi,
                                        'uom_lebar_jadi'=> $mb->uom_lebar_jadi,
                                        'kode_k3l'      => $mb->kode_k3l,
                                        'row_order'     => $row_order_items,
                                        'nama_user'     => $nama_user
                            );

                            $data_adj_items[] = array(
                                                'kode_adjustment' => $kode_adjustment,
                                                'quant_id'        => $quant_id_new,
                                                'kode_produk'     => $mb->kode_produk,
                                                'lot'             => $barcode_id,
                                                'uom'             => $mb->uom,
                                                'qty_adjustment'  => $mb->qty,
                                                'uom2'            => $mb->uom2,
                                                'qty_adjustment2' => $mb->qty2,
                                                'move_id'         => $move_id,
                                                'qty_move'        => $mb->qty,
                                                'qty2_move'       => $mb->qty2,
                                                'row_order'       => $row_order_smi
                            );

                            $data_stock_quant[] = array(
                                                'quant_id'      => $quant_id_new,
                                                'create_date'   => $tgl,
                                                'move_date'     => $tgl,
                                                'kode_produk'   => $mb->kode_produk,
                                                'nama_produk'   => $mb->nama_produk,
                                                'corak_remark'  => $corak_remark,
                                                'warna_remark'  => $mb->warna_remark,
                                                'lot'           => $barcode_id,
                                                'nama_grade'    => $mb->grade,
                                                'qty'           => $mb->qty,
                                                'uom'           => $mb->uom,
                                                'qty2'          => $mb->qty2,
                                                'uom2'          => $mb->uom2,
                                                'qty_jual'      => $mb->qty_jual,
                                                'uom_jual'      => $mb->uom_jual,
                                                'qty2_jual'     => $mb->qty2_jual,
                                                'uom2_jual'     => $mb->uom2_jual,
                                                'lokasi'        => 'GJD/Stock',
                                                'lokasi_fisik'  => 'PORT',
                                                'lebar_jadi'    => $mb->lebar_jadi,
                                                'uom_lebar_jadi'=> $mb->uom_lebar_jadi,
                                                'sales_group'   => $sales_group,
                                                'reff_note'     => "Barcode Manual dari No. ".$kode
                            );

                            // stock_move
                            $origin_adj      = $kode_adjustment.'|'.$row_order_smi;
                            $data_stock_move[] = array(
                                                'move_id'       => $move_id,
                                                'create_date'   => $tgl,
                                                'origin'        => $origin_adj,
                                                'method'        => 'GJD|ADJ',
                                                'lokasi_dari'   => $lokasi_adj,
                                                'lokasi_tujuan' =>  $lokasi_stock,
                                                'status'        => 'done',
                                                'row_order'     => $row_order_smi
                            );

                            // stock_move_produk
                            $data_stock_move_produk[] = array(
                                            'move_id'       => $move_id,
                                            'kode_produk'   => $mb->kode_produk,
                                            'nama_produk'   => $mb->nama_produk,
                                            'qty'           => $mb->qty,
                                            'uom'           => $mb->uom,
                                            'status'        => 'done',
                                            'row_order'     => $row_order_smi,
                            );

                            // stock_move_items
                            $data_stock_move_items[] = array(
                                                'move_id'      => $move_id,
                                                'quant_id'     => $quant_id_new,
                                                'tanggal_transaksi'=> $tgl,
                                                'kode_produk'  => $mb->kode_produk,
                                                'nama_produk'  => $mb->nama_produk,
                                                'lot'          => $barcode_id,
                                                'qty'          => $mb->qty,
                                                'uom'          => $mb->uom,
                                                'qty2'         => $mb->qty2,
                                                'uom2'         => $mb->uom2,
                                                'status'       => 'done',
                                                'lokasi_fisik'  => 'PORT',
                                                'lebar_jadi'    => $mb->lebar_jadi,
                                                'uom_lebar_jadi'=> $mb->uom_lebar_jadi,
                                                'row_order'     => $row_order_smi
                            );

                            $quant_id_new++;
                            $row_order_items++;
                            $last_move = $last_move + 1;
                            $move_id   = "SM".$last_move;
                            $row_order_smi++;
                        }
                        $loop_batch++;

                    }

                    if(!empty($data_update_batch) AND !empty($data_items)){

                        if(!empty($data_update_batch)){
                            $update = $this->m_barcodemanual->update_data_barcode_manual_batch($data_update_batch,$kode);
                            $jml_row_batch = $loop_batch - 1;
                            $jml_update    = (int) $update;
                            if($jml_row_batch != $jml_update){
                                throw new \Exception('Generate data Gagal, No Batch Gagal dibuat !', 200);
                            }
                        }

                        if(!empty($data_items)){
                            $insert = $this->m_barcodemanual->insert_data_barcode_manual_batch_items($data_items);
                            if(!empty($insert)){
                                throw new \Exception('Generate data Gagal, Gagal Menyimpan data Item batch' , 200);
                            }
                        }

                        if(!empty($data_adj_head)){
                            $insert_adj = $this->m_barcodemanual->insert_data_adj_barcode_manual($data_adj_head);
                            if(!empty($insert_adj)){
                                throw new \Exception('Generate data Gagal, Gagal Menyimpan data Adjustment' , 200);
                            }
                        }

                        if(!empty($data_adj_items)){
                            $insert_adj_items = $this->m_barcodemanual->insert_data_adj_items_barcode_manual($data_adj_items);
                            if(!empty($insert_adj_items)){
                                throw new \Exception('Generate data Gagal, Gagal Menyimpan data Adjustment Items', 200);
                            }
                        }
                        
                        if(!empty($data_stock_quant)){
                            $insert_stock = $this->m_barcodemanual->insert_data_stock_quant_barcode_manual($data_stock_quant);
                            if(!empty($insert_stock)){
                                throw new \Exception('Generate data Gagal, Gagal Menyimpan data Stock' , 200);
                            }
                        }

                        if(!empty($data_stock_move)){
                            $insert_sm = $this->m_barcodemanual->insert_data_stock_move_barcode_manual('stock_move',$data_stock_move);
                            if(!empty($insert_sm)){
                                throw new \Exception('Generate data Gagal, Gagal Menyimpan data Movement' , 200);
                            }
                        }

                        if(!empty($data_stock_move_produk)){
                            $insert_smp = $this->m_barcodemanual->insert_data_stock_move_barcode_manual('stock_move_produk',$data_stock_move_produk);
                            if(!empty($insert_smp)){
                                throw new \Exception('Generate data Gagal, Gagal Menyimpan data Movement' , 200);
                            }
                        }

                        if(!empty($data_stock_move_items)){
                            $insert_smi = $this->m_barcodemanual->insert_data_stock_move_barcode_manual('stock_move_items',$data_stock_move_items);
                            if(!empty($insert_smi)){
                                throw new \Exception('Generate data Gagal, Gagal Menyimpan data Movement' , 200);
                            }
                        }

                        // update status 
                        $data_update_mm = array(
                                        'tanggal_transaksi' => $tgl,
                                        'status'            => 'done',
                                        'kode_adjustment'   => $kode_adjustment,
                        );
                        $update = $this->m_barcodemanual->update_data_barcode_manual($kode,$data_update_mm);

                        // History Barcode Manual
                        $jenis_log = "generate";
                        $note_log  = "Generate Data | Jumlah Lot. ".$total_pcs;
                        $data_history = array(
                                         'datelog'   => date("Y-m-d H:i:s"),
                                         'kode'      => $kode,
                                         'jenis_log' => $jenis_log,
                                         'note'      => $note_log  );
                         
                        // load in library
                        $this->_module->gen_history_ip($sub_menu,$username,$data_history);


                        // History Adjustment
                        $jenis_log = "create";
                        $note_log  = $kode_adjustment." ini dibuat dari Fitur Barcode Manual";
                        $data_history = array(
                                         'datelog'   => date("Y-m-d H:i:s"),
                                         'kode'      => $kode_adjustment,
                                         'jenis_log' => $jenis_log,
                                         'note'      => $note_log  );
                         
                        // load in library
                        $this->_module->gen_history_ip('adjustment',$username,$data_history);

                        $jenis_log = "generate";
                        $note_log  = "Generate Adjustment ini di generate otomatis dari Fitur Barcode Manual | Jumlah Adjustment  ".$total_pcs;
                        $data_history = array(
                                         'datelog'   => date("Y-m-d H:i:s"),
                                         'kode'      => $kode_adjustment,
                                         'jenis_log' => $jenis_log,
                                         'note'      => $note_log  );
                         
                        // load in library
                        $this->_module->gen_history_ip('adjustment',$username,$data_history);

    
                    }else{
                        throw new \Exception('Gagal Generate Data1 ', 500);
                    }

                    if (!$this->_module->finishTransaction()) {
                        throw new \Exception('Gagal Generate Data2 ', 500);
                    }

                    $callback = array('status'=>'success', 'message' =>'Data Berhasil di Generate !', 'icon'=> 'fa fa-check', 'type'=>'success');

                }

                // unlock table
                $this->_module->unlock_tabel();

                $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($callback));
                // finish transaction
                $this->_module->finishTransaction();

            }

        }catch(Exception $ex){
            // unlock table
            $this->_module->unlock_tabel();

            $this->_module->finishTransaction();
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }


    function cancel_barcode_manual()
    {
        try{
            if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
                // session habis
                throw new \Exception('Waktu Anda Telah Habis', 401);
            }else{
                $kode   = $this->input->post('kode');

                $sub_menu  = $this->uri->segment(2);
                $username = addslashes($this->session->userdata('username')); 

                $this->_module->lock_tabel("mrp_manual WRITE, mrp_manual_batch WRITE, mrp_manual_batch WRITE");
                
                // start transaction
                $this->_module->startTransaction();
                
                $mrpm       = $this->m_barcodemanual->get_data_mrp_manual_by_id($kode);

                if(empty($mrpm)){
                    throw new \Exception('Data Barcode Manual tidak ditemukan !', 200);
                }else if($mrpm->status == 'done'){
                    $callback = array('status' => 'failed','message' => 'Maaf, Data Tidak Bisa dibatalkan, Status Sudah Done !', 'icon' =>'fa fa-warning', 'type' => 'danger');
                }else if($mrpm->status == 'cancel'){
                    $callback = array('status' => 'failed','message' => 'Maaf, Data Tidak Bisa dibatalkan, Status Sudah Cancel !', 'icon' =>'fa fa-warning', 'type' => 'danger');
                }else{
                    $data = array(
                        'status'                => 'cancel',
                        'tanggal_transaksi'     => date('Y-m-d H:i:s'),
                    );
                    $update = $this->m_barcodemanual->update_data_barcode_manual($kode,$data);
                    if(empty($update)){
                        throw new \Exception('Gagal Mengubah data'.$update, 500);
                    }

                    $jenis_log = "Cancel";
                    $note_log  = "Batal Membuat Barcode Manual ";
                    $data_history = array(
                                        'datelog'   => date("Y-m-d H:i:s"),
                                        'kode'      => $kode,
                                        'jenis_log' => $jenis_log,
                                        'note'      => $note_log  );
                        
                    // load in library
                    $this->_module->gen_history_ip($sub_menu,$username,$data_history);

                    $callback = array('status'=>'success', 'message' =>'Data Berhasil dibatalkan !', 'icon'=> 'fa fa-check', 'type'=>'success');

                }
                $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($callback));
                // finish transaction
                $this->_module->finishTransaction();
                // unlock table
                $this->_module->unlock_tabel();

            }
        }catch(Exception $ex){
            $this->_module->finishTransaction();
            // unlock table
            $this->_module->unlock_tabel();
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }

    function print_barcode_manual()
    {
        try{
            if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
                // session habis
                throw new \Exception('Waktu Anda Telah Habis', 401);
            }else{

                $kode       = $this->input->post('kode');
                $quant_id_arr   = $this->input->post('quant_id_arr');
                $desain_barcode       = $this->input->post('desain_barcode');

                if(empty($quant_id_arr)){
                    throw new \Exception('Data Print tidak ditemukan !', 500);
                }else{
                    
                    $data_print = $this->print_barcode($kode,$desain_barcode,$quant_id_arr);
                    if(empty($data_print)){
                        throw new \Exception('Data Print tidak ditemukan !', 500);
                    }
                    $callback = array('status' => 'success', 'message' => 'Print Berhasil !', 'icon' =>'fa fa-check', 'type' => 'success', 'data_print' =>$data_print);
                }
                $this->output->set_status_header(200)
                        ->set_content_type('application/json', 'utf-8')
                        ->set_output(json_encode($callback));
            }

        }catch(Exception $ex){
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }


    function print_barcode($kode,$desain_barcode,$quant_id_arr){

        $desain_barcode = strtolower($desain_barcode);
        $code = new Code\Code128New();
        $this->prints->setView('print/'.$desain_barcode);
        $data_print_array = array();
        $data_qty2_jual = array();
        for($a=0; $a<count($quant_id_arr); $a++){
            $dp     = $this->m_barcodemanual->get_data_print_by_kode($kode,$quant_id_arr[$a]);
            $gen_code = $code->generate($dp->lot, "", 60, "vertical");
            $tanggal = date('Ymd', strtotime($dp->tanggal_buat));
            $data_print_array = array(
                        'pattern' => $dp->corak_remark,
                        'isi_color' => !empty($dp->warna_remark)? $dp->warna_remark : '&nbsp',
                        'isi_satuan_lebar' => 'WIDTH ('.$dp->uom_lebar_jadi.')',
                        'isi_lebar' => !empty($dp->lebar_jadi)? $dp->lebar_jadi : '&nbsp',
                        'isi_satuan_qty1' => 'QTY ['.$dp->uom_jual.']',
                        'isi_qty1' => round($dp->qty_jual,2),
                        'barcode_id' => $dp->lot,
                        'tanggal_buat' => $tanggal,
                        'no_pack_brc' => $kode,
                        'barcode' => $gen_code,
                        'k3l' => $dp->kode_k3l
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

    function cek_akses_menu()
    {
        $mms               = $this->_module->get_kode_sub_menu_deptid($this->uri->segment(2),'BRCM')->row_array();
        if(!empty($mms['kode'])){
            $mms_kode = $mms['kode'];
        }else{
            $mms_kode = '';
        }
        $access = $this->m_accessmenu->getDetailByMenu(['access_only' => $this->input->ip_address(), 'menu' => $mms_kode]);
        return $access;
    }
  
}