<?php defined('BASEPATH') or exit ('No Direct Script Acces Allowed');

/**
 * 
 */
class Inlet extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->is_loggedin();//cek apakah user sudah login
        $this->load->model("_module");
        $this->load->model("m_inlet");
        $this->load->model("m_outlet");
        $this->load->model("m_produk");
        $this->load->model("m_mo");
	}

    public function index()
    {
        $sub_menu               = $this->uri->segment(2);
        $data['id_dept']        = 'INLET';
        $data['sales_group']= $this->_module->get_list_sales_group();
        $data['list_status']    = $this->m_inlet->get_list_status_by_menu($sub_menu,'INLET');
        $this->load->view('manufacturing/v_inlet', $data);
    }

    public function add()
    {
        $data['id_dept']    = 'INLET';
        $data['uom']        = $this->_module->get_list_uom();
        $data['jenis_kain'] = $this->_module->get_list_jenis_kain();        
        $data['quality']    = $this->_module->get_list_quality();        
        $data['kode_k3l']   = $this->_module->get_list_kode_k3l();        
        $data['desain_barcode']   = $this->_module->get_list_desain_barcode_by_type('LBK');     
        $data['mesin']    = $this->m_mo->get_list_mesin('GJD');
        $data['sales_group']= $this->_module->get_list_sales_group();
        $this->load->view('manufacturing/v_inlet_add', $data);
    }

    public function edit($id = null)
    {
        if(!isset($id)) show_404();
        $kode_decrypt       = decrypt_url($id);
        $data['id']         = $id;
        $data['id_dept']    = 'INLET';
        $data['mms']        = $this->_module->get_data_mms_for_log_history('INLET');// get mms by dept untuk menu yg beda-beda
        $data['uom']        = $this->_module->get_list_uom();
        $data['jenis_kain'] = $this->_module->get_list_jenis_kain();        
        $data['quality']    = $this->_module->get_list_quality();        
        $data['kode_k3l']   = $this->_module->get_list_kode_k3l();        
        $data['desain_barcode']   = $this->_module->get_list_desain_barcode_by_type('LBK');     
        $data['sales_group']= $this->_module->get_list_sales_group();
        $data['mesin']      = $this->m_mo->get_list_mesin('GJD');
        $data['inlet']      = $this->m_inlet->get_data_inlet_by_id($kode_decrypt);
        $this->load->view('manufacturing/v_inlet_edit', $data);
    }

    function get_data()
    {
        if(isset($_POST['start']) && isset($_POST['draw'])){
            $list = $this->m_inlet->get_datatables();
            $data = array();
            $no = $_POST['start'];
            foreach ($list as $field) {
                $kode_encrypt = encrypt_url($field->id);
                $no++;
                $row = array();
                $row[] = $no;
                $row[] = '<a href="'.base_url('manufacturing/inlet/edit/'.$kode_encrypt).'">'.$field->lot.'</a>';
                $row[] = $field->tanggal;
                $row[] = $field->kode_mrp;
                $row[] = $field->nama_sales_group;
                $row[] = $field->nama_produk;
                $row[] = $field->corak_remark;
                $row[] = $field->warna_remark;
                $row[] = $field->lebar_jadi.' '.$field->uom_lebar_jadi;
                $row[] = $field->desain_barcode;
                $row[] = $field->nama_status;
                $row[] = '<button type="button" class="btn btn-danger btn-sm btn-delete-inlet" data-id="' . $kode_encrypt . '" data-lot ="'.$field->lot.'" data-title="Batal Inlet"><i class="fa fa-trash"></></button>';
    
                $data[] = $row;
            }
    
            $output = array(
                "draw" => $_POST['draw'],
                "recordsTotal" => $this->m_inlet->count_all(),
                "recordsFiltered" => $this->m_inlet->count_filtered(),
                "data" => $data,
            );
            //output dalam format JSON
            echo json_encode($output);
            
        }else{
            die();
        }
    }

    function search_lot()
    {
        if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
            // session habis
            $callback = array('message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
        }else{

            $lot = addslashes($this->input->post('txtlot'));

            $count_lot = $this->m_inlet->get_count_data_data_by_lot($lot);
            if($count_lot == 0){
                $callback = array('status' => 'failed', 'message' => 'Data KP / Lot tidak ditemukan !', 'icon' =>'fa fa-warning', 'type' => 'danger');
            }else if($count_lot > 1){
                $callback = array('status' => 'failed', 'message' => 'Lot <b>'.$lot.'</b> lebih dari 1 !', 'icon' =>'fa fa-warning', 'type' => 
                'danger');

            }else{

                $method = "";
                $data = $this->m_inlet->get_data_by_lot($lot);
                if(!empty($data->method)){
                    $mth = explode("|", $data->method);
                    $loop = 0;
                    foreach($mth as $mths){
                        if($loop == 1){
                            $method = $mth[$loop];
                        }
                        $loop++;
                    }
                }

                // cek lot apa sudah diinput atau belum 
                $cek_lot_inlet = $this->m_inlet->get_data_inlet_by_lot($lot)->num_rows();
                $status = array('draft','process');
                $cek_status    = $this->m_inlet->get_data_inlet_by_lot_status($lot,$status)->num_rows();

                if(empty($method) OR $method != 'CON'){
                    $callback = array('status' => 'failed', 'message' => 'Lot <b>'.$lot.'</b> belum masuk MG GJD !', 'icon' =>'fa fa-warning', 'type' => 'danger');
                }else if($cek_lot_inlet > 0 AND $cek_status > 0 ){
                    $callback = array('status' => 'failed', 'message' => 'Lot <b>'.$lot.'</b> sudah di Inlet !', 'icon' =>'fa fa-warning', 'type' => 'danger');
                }else if($data->id_category == 21){
                    $callback = array('status' => 'failed', 'message' => 'Kategori Kain tidak Boleh Kain Hasil Gudang Jadi !', 'icon' =>'fa fa-warning', 'type' => 'danger');
                }else if($data->lokasi_fisik != ''){
                    $callback = array('status' => 'failed', 'message' => 'Lokasi Fisik / Rak harus Kosong !', 'icon' =>'fa fa-warning', 'type' => 'danger');
                }else{

                    $data_lot   = [];
                    $data_lot[] = array(  
                                'quant_id'      => $data->quant_id,
                                'lot'           => $data->lot,
                                'kd_marketing'  => $data->sales_group,
                                'nm_marketing'  => $data->nama_sales_group,
                                'kode_mrp'      => $data->kode,
                                'kode_produk'   => $data->kode_produk,
                                'nama_produk'   => $data->nama_produk,
                                'warna'         => $data->nama_warna,
                                'lebar_jadi'    => $data->lebar_jadi,
                                'uom_lebar_jadi'=> $data->uom_lebar_jadi,
                                'id_jenis_kain' => $data->id_jenis_kain,
                                'nama_jenis_kain'=> $data->nama_jenis_kain,
                                'gramasi'       => $data->gramasi,
                    );
                    $callback = array('status' => 'succes', 'message' => 'Data KP / Lot ditemukan !', 'icon' =>'fa fa-success', 'type' => 'success', 'record' => $data_lot);
                }

            }

        }

        echo json_encode($callback);

    }

    public function save_inlet()
    {

        try{
            if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
                // session habis
                $callback = array('message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
                throw new \Exception('Waktu Anda Telah Habis', 401);
            }else{
                
                $id             = ($this->input->post('id'));
                $quant_id       = ($this->input->post('quant_id'));
                $lot            = ($this->input->post('lot'));
                $kode_mrp       = ($this->input->post('kode_mrp'));
                $marketing      = ($this->input->post('marketing'));
                $kode_produk    = ($this->input->post('kode_produk'));
                $nama_produk    = ($this->input->post('nama_produk'));
                $corak_remark   = ($this->input->post('corak_remark'));
                $warna_remark   = ($this->input->post('warna_remark'));
                $lebar_jadi     = ($this->input->post('lebar_jadi'));
                $uom_lebar_jadi = ($this->input->post('uom_lebar_jadi'));
                $jenis_kain     = ($this->input->post('jenis_kain'));
                $benang         = ($this->input->post('benang'));
                $gramasi        = ($this->input->post('gramasi'));
                $berat          = ($this->input->post('berat'));
                $quality        = ($this->input->post('quality'));
                $desain_barcode = ($this->input->post('desain_barcode'));
                $k3l            = ($this->input->post('k3l'));
                $mesin          = ($this->input->post('mesin'));
                $operator       = ($this->input->post('operator'));

                // start transaction
                $this->_module->startTransaction();

                // get nama_jenis_kain_by_id, ket
                $get_njk = $this->m_produk->get_mst_jenis_kain_by_id($jenis_kain)->row();
                $nama_jenis_kain = $get_njk->nama_jenis_kain ?? '';
                $ket_kain = $get_njk->ket ?? ''; // Multibar / Non Multibar

                // get nama_quality by id
                $get_nq = $this->_module->get_mst_quality_by_id($quality)->row();
                $nama_quality = $get_nq->nama ?? '';

                // get nama mesin by mcid
                $get_mc = $this->m_mo->get_nama_mesin_by_kode($mesin)->row();
                $nama_mesin = $get_mc->nama_mesin ?? '';

                // get nama sales_group by kode
                $nm_sales_group = $this->_module->get_nama_sales_Group_by_kode($marketing);

                $sub_menu  = $this->uri->segment(2);
                $username  = addslashes($this->session->userdata('username'));
                $nu        = $this->_module->get_nama_user($username)->row_array(); 
                $nama_user = addslashes($nu['nama']);

                $count_lot = $this->m_inlet->get_count_data_data_by_lot($lot);
                if(empty($quant_id)){
                    $callback = array('status' => 'failed', 'field' => 'lot', 'message' => 'Data KP / Lot tidak ditemukan di Stock!', 'icon' =>'fa fa-warning', 'type' => 'danger');
                }else if(empty($lot)){
                    $callback = array('status' => 'failed', 'field' => 'lot', 'message' => 'Data KP / Lot tidak boleh kosong !', 'icon' =>'fa fa-warning', 'type' => 'danger');
                }else if($count_lot == 0 AND empty($id)){
                    $callback = array('status' => 'failed', 'field' => 'lot', 'message' => 'Data KP / Lot tidak ditemukan !', 'icon' =>'fa fa-warning', 'type' => 'danger');
                }else if($count_lot > 1){
                    $callback = array('status' => 'failed', 'field' => 'lot', 'message' => 'Lot <b>'.$lot.'</b> lebih dari 1 !', 'icon' =>'fa fa-warning', 'type' => 'danger');
                }else if(empty($kode_produk)){
                    $callback = array('status' => 'failed', 'field' => 'kode_produk', 'message' => 'Kode Produk tidak boleh kosong !', 'icon' =>'fa fa-warning', 'type' => 'danger');
                }else if(empty($marketing)){
                    $callback = array('status' => 'failed', 'field' => 'marketing', 'message' => 'Marketing tidak boleh kosong !', 'icon' =>'fa fa-warning', 'type' => 'danger');
                }else if(empty($corak_remark)){
                    $callback = array('status' => 'failed', 'field' => 'corak_remark', 'message' => 'Corak Remark tidak boleh kosong !', 'icon' =>'fa fa-warning', 'type' => 'danger');
                }else if(empty($warna_remark)){
                    $callback = array('status' => 'failed', 'field' => 'warna_remark', 'message' => 'Warna Remark tidak boleh kosong !', 'icon' =>'fa fa-warning', 'type' => 'danger');
                }else if(empty($jenis_kain)){
                    $callback = array('status' => 'failed', 'field' => 'jenis_kain', 'message' => 'Jenis Kain tidak boleh kosong !', 'icon' =>'fa fa-warning', 'type' => 'danger');
                }else if(empty($gramasi) AND $ket_kain == 'Non Multibar'){
                    $callback = array('status' => 'failed', 'field' => 'gramasi', 'message' => 'Gramasi tidak boleh kosong !', 'icon' =>'fa fa-warning', 'type' => 'danger');
                }else if(empty($berat) AND $ket_kain == 'Multibar'){
                    $callback = array('status' => 'failed', 'field' => 'berat', 'message' => 'Berat tidak boleh kosong !', 'icon' =>'fa fa-warning', 'type' => 'danger');
                }else if(empty($lebar_jadi)){
                    $callback = array('status' => 'failed', 'field' => 'lebar_jadi', 'message' => 'Lebar Jadi tidak boleh kosong !', 'icon' =>'fa fa-warning', 'type' => 'danger');
                }else if(empty($uom_lebar_jadi)){
                    $callback = array('status' => 'failed', 'field' => 'uom_lebar_jadi', 'message' => 'Uom Lebar Jadi tidak boleh kosong !', 'icon' =>'fa fa-warning', 'type' => 'danger');
                }else if(empty($benang)){
                    $callback = array('status' => 'failed', 'field' => 'benang', 'message' => 'Benang tidak boleh kosong !', 'icon' =>'fa fa-warning', 'type' => 'danger');
                }else if(empty($desain_barcode)){
                    $callback = array('status' => 'failed', 'field' => 'desain_barcode', 'message' => 'Desain Barcode tidak boleh kosong !', 'icon' =>'fa fa-warning', 'type' => 'danger');
                }else if(empty($k3l)){
                    $callback = array('status' => 'failed', 'field' => 'k3l', 'message' => 'Kode K3L tidak boleh kosong !', 'icon' =>'fa fa-warning', 'type' => 'danger');

                }else{

                    // lock table
                    $this->_module->lock_tabel('mrp_inlet WRITE,log_history WRITE, user WRITE, main_menu_sub WRITE, stock_quant as sq WRITE, mst_produk as mp WRITE, mst_jenis_kain as mjk WRITE, stock_move as sm WRITE, warna as w WRITE, mrp_production as mrp WRITE, mrp_production_rm_target as rmt WRITE, sales_contract as sc WRITE, mst_sales_group as msg WRITE, stock_quant WRITE, stock_move_items WRITE');

                    if(!empty($id)){ // update

                        // cek status inlet
                        $kode_decrypt  = decrypt_url($id);
                        $cek           = $this->m_inlet->cek_status_inlet_by_id($kode_decrypt);

                        if($cek->status == 'done'){
                            $callback = array('status' => 'failed', 'message' => 'Data Inlet tidak bisa dirubah, Data Inlet sudah <b> Done </b> !', 'icon' =>'fa fa-warning', 'type' => 'danger');
                            // throw new Exception('Data Inlet tidak bisa dirubah, Data Inlet sudah Done !', 500);

                        }else if($cek->status == 'cancel'){
                            // throw new Exception('Data Inlet tidak bisa dirubah, Data Inlet sudah Cancel !', 500);
                            $callback = array('status' => 'failed', 'message' => 'Data Inlet tidak bisa dirubah, Data Inlet sudah Cancel !', 'icon' =>'fa fa-warning', 'type' => 'danger');
                            
                        }else{

                            $data_inlet = array(
                                            'corak_remark'  => $corak_remark,
                                            'warna_remark'  => $warna_remark,
                                            'lebar_jadi'    => $lebar_jadi,
                                            'uom_lebar_jadi'=> $uom_lebar_jadi,
                                            'gramasi'       => $gramasi,
                                            'berat'         => $berat,
                                            'benang'        => $benang,
                                            'id_quality'    => $quality,
                                            'desain_barcode'=> $desain_barcode,
                                            'kode_k3l'      => $k3l,
                                            'mc_id'         => $mesin,
                                            'operator'      => $operator,
                            );
                            
                            $this->m_inlet->update_data_inlet($kode_decrypt,$data_inlet);

                            $jenis_log = "edit";
                            $note_log  = $kode_decrypt." | ".$lot." | ".$nm_sales_group." | ".$kode_mrp." | ".$kode_produk." | ".$nama_produk." | ".$corak_remark." | ".$warna_remark." | ".$lebar_jadi." ".$uom_lebar_jadi." | ".$nama_jenis_kain." | ".$gramasi." | ".$berat." | ".$benang." | ".$nama_quality." | ".$desain_barcode." | ".$k3l." | ".$nama_mesin." | ".$operator;
                            $data_history = array(
                                            'datelog'   => date("Y-m-d H:i:s"),
                                            'kode'      => $kode_decrypt,
                                            'jenis_log' => $jenis_log,
                                            'note'      => $note_log );

                            $this->_module->gen_history_ip($sub_menu,$username,$data_history);

                            $callback = array('status' => 'succes', 'message' => 'Data Inlet berhasil diubah !', 'icon' =>'fa fa-success', 'type' => 'success', );
                        }

                    }else{ // save new
                        $method = "";
                        $data = $this->m_inlet->get_data_by_lot($lot);
                        if(!empty($data->method)){
                            $mth = explode("|", $data->method);
                            $loop = 0;
                            foreach($mth as $mths){
                                if($loop == 1){
                                    $method = $mth[$loop];
                                }
                                $loop++;
                            }
                        }

                        // cek lot apa sudah diinput atau belum 
                        $cek_lot_inlet = $this->m_inlet->get_data_inlet_by_lot($lot)->num_rows();
                        $status  = array('draft','process','done');
                        $cek_status    = $this->m_inlet->get_data_inlet_by_lot_status($lot,$status)->num_rows();

                        if(empty($method) OR $method != 'CON'){
                            $callback = array('status' => 'failed', 'message' => 'Lot <b>'.$lot.'</b> belum masuk MG GJD !', 'icon' =>'fa fa-warning', 'type' => 'danger');
                        }else if($cek_lot_inlet > 0 AND $cek_status > 0 ){
                            $callback = array('status' => 'failed', 'message' => 'Lot <b>'.$lot.'</b> sudah di Inlet !', 'icon' =>'fa fa-warning', 'type' => 'danger');
                        }else{// insert

                            // get data last id in mrp inlet
                            $id_inlet = $this->m_inlet->get_last_id_mrp_inlet_id();

                            // get data stock_quant
                            $sq       = $this->_module->get_stock_quant_by_id($quant_id)->row();

                            $data_inlet = array(
                                            'quant_id'      => $quant_id,
                                            'id'            => $id_inlet,
                                            'lot'           => $lot,
                                            'tanggal'       => date("Y-m-d H:i:s"),
                                            'kode_mrp'      => $kode_mrp,
                                            'sales_group'   => $marketing,
                                            'kode_produk'   => $kode_produk,
                                            'nama_produk'   => $nama_produk,
                                            'corak_remark'  => $corak_remark,
                                            'warna_remark'  => $warna_remark,
                                            'id_quality'    => $quality,
                                            'desain_barcode'=> $desain_barcode,
                                            'id_jenis_kain' => $jenis_kain,
                                            'benang'        => $benang,
                                            'kode_k3l'      => $k3l,
                                            'mc_id'         => $mesin,
                                            'operator'      => $operator,
                                            'lebar_jadi'    => $lebar_jadi,
                                            'uom_lebar_jadi'=> $uom_lebar_jadi,
                                            'gramasi'       => $gramasi,
                                            'berat'         => $berat,
                                            'nama_user'     => $nama_user,
                                            'qty'           => $sq->qty,
                                            'uom'           => $sq->uom,
                                            'qty2'          => $sq->qty2,
                                            'uom2'          => $sq->uom2,
                                            'qty_opname'    => $sq->uom,
                                            'uom_opname'    => $sq->uom_opname,                                                                                     
                                            'origin_prod'   => $data->origin_prod
                            );

                            
                            $this->m_inlet->save_data_inlet($data_inlet);

                            $update_lokasi_fisik = array('lokasi_fisik' => 'INLET');
                            $where_smi           = array('move_id' => $sq->reserve_move, 'quant_id'=>$quant_id, 'lot'=> $lot);
                            $this->m_inlet->update_data_lokasi_fisik_lot($quant_id,$update_lokasi_fisik,$where_smi);

                            $jenis_log = "create";
                            $note_log  = $id_inlet." | ".$lot." | ".$nm_sales_group." | ".$kode_mrp." | ".$kode_produk." | ".$nama_produk." | ".$corak_remark." | ".$warna_remark." | ".$lebar_jadi." ".$uom_lebar_jadi." | ".$nama_jenis_kain." | ".$gramasi." | ".$berat." | ".$benang." | ".$nama_quality." | ".$desain_barcode." | ".$k3l." | ".$nama_mesin." | ".$operator;
                            $data_history = array(
                                            'datelog'   => date("Y-m-d H:i:s"),
                                            'kode'      => $id_inlet,
                                            'jenis_log' => $jenis_log,
                                            'note'      => $note_log  );
                            
                            // load in library
                            $this->_module->gen_history_ip($sub_menu,$username,$data_history);
                            
                            $callback = array('status' => 'succes', 'message' => 'Data KP / Lot berhasil di Inlet !', 'icon' =>'fa fa-success', 'type' => 'success', );

                        }
                    }
                    // unlock table
                    $this->_module->unlock_tabel();
                }

                $this->output->set_status_header(200)
                        ->set_content_type('application/json', 'utf-8')->set_output(json_encode($callback));
                // finish transaction
                $this->_module->finishTransaction();
            }
        } catch(Exception $ex){
            $this->_module->finishTransaction();
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
        // echo json_encode($callback);
        
    }

    public function delete_inlet()
    {
        if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
            // session habis
            $callback = array('message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
        }else{

            $id     = $this->input->post('id');
            $lot    = $this->input->post('lot');

            $kode_decrypt       = decrypt_url($id);
            $sub_menu           = $this->uri->segment(2);
            $username  = addslashes($this->session->userdata('username'));

            // start transaction
            $this->_module->startTransaction();


            //lock table 
            $this->_module->lock_tabel('mrp_inlet WRITE,log_history WRITE, user WRITE, main_menu_sub WRITE, mrp_production_rm_hasil WRITE,stock_quant WRITE, stock_quant as sq WRITE, mrp_production_rm_target as rm WRITE, stock_move_items as smi WRITE, stock_move_items WRITE');

            // cek status inlet
            $cek_status = $this->m_inlet->cek_status_inlet_by_id($kode_decrypt);

            if(empty($cek_status)){
                $callback = array('status' => 'failed', 'message' => 'Data Inlet KP / Lot <b>'.$lot.' tidak ditemukan!', 'icon' =>'fa fa-warning', 'type' => 'danger');
            }else if($cek_status->status == 'done'){
                $callback = array('status' => 'failed', 'message' => 'Data Inlet tidak bisa dihapus, Data Inlet sudah Done !', 'icon' =>'fa fa-warning', 'type' => 'danger');
            }else if($cek_status->status == 'process'){
                $callback = array('status' => 'failed', 'message' => 'Data Inlet tidak bisa dihapus, Data Inlet sedang di Process !', 'icon' =>'fa fa-warning', 'type' => 'danger');
            }else if($cek_status->status == 'cancel'){
                $callback = array('status' => 'failed', 'message' => 'Data Inlet tidak bisa dihapus, Data Inlet sudah Cancel / dibatalkan !', 'icon' =>'fa fa-warning', 'type' => 'danger');
            }else{

                // cek hasil hph by lot
                $cek_hasil = $this->m_inlet->cek_mrp_rm_hasil_by_lot($cek_status->kode_mrp,$cek_status->lot)->num_rows();

                if($cek_hasil > 0 ){
                    $callback = array('status' => 'failed', 'message' => 'Data Inlet tidak bisa dihapus, Data Inlet sudah terdapat HPH !', 'icon' =>'fa fa-warning', 'type' => 'danger');
                }else{

                    $update_status = array('status'=>'cancel');
                    $this->m_inlet->update_data_inlet($kode_decrypt,$update_status);

                    $cek_smi_rm = $this->m_outlet->cek_stock_move_items_by_kode($cek_status->kode_mrp,$cek_status->quant_id,$lot)->row();

                    $update_lokasi_fisik = array('lokasi_fisik' => '');
                    $where_smi           = array('move_id' => $cek_smi_rm->move_id, 'quant_id'=>$cek_status->quant_id, 'lot'=> $lot);
                    $this->m_inlet->update_data_lokasi_fisik_lot($cek_status->quant_id,$update_lokasi_fisik,$where_smi);

                    $jenis_log = "cancel";
                    $note_log  = "Data Inlet dibatalkan | ".$kode_decrypt." | ".$lot;
                            $data_history = array(
                                            'datelog'   => date("Y-m-d H:i:s"),
                                            'kode'      => $kode_decrypt,
                                            'jenis_log' => $jenis_log,
                                            'note'      => $note_log  );
                            
                    // load in library
                    $this->_module->gen_history_ip($sub_menu,$username,$data_history);
    
                    $callback = array('status' => 'succes', 'message' => 'Data KP / Lot <b>'.$lot.'</b> berhasil di batalkan !', 'icon' =>'fa fa-success', 'type' => 'success', );
                }

            }

            // unlock table
            $this->_module->unlock_tabel();

            // finish transaction
            $this->_module->finishTransaction();
            
        }

        echo json_encode($callback);

    }
}