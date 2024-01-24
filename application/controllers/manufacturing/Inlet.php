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
        $this->load->library('barcode');
        $this->load->library('prints');
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
                $row[] = '<button type="button" class="btn btn-danger btn-sm btn-delete-inlet" data-id="' . $kode_encrypt . '" data-lot ="'.$field->lot.'"  title="Batal Inlet" data-toggle="tooltip"><i class="fa fa-trash"></></button>';
    
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

    function get_data_total_hasil_hph()
    {
        try{
            if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
                // session habis
                // $callback = array('message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
                throw new \Exception('Waktu Anda Telah Habis', 401);
            }else{  

                $id_inlet = $this->input->post('id');
                $total_hph = $this->m_inlet->get_total_hph_by_lot($id_inlet);

                $table_total = array();
                $table_target = array('ket'=>'Qty Target','qty'=>$total_hph->qty,'qty2'=>$total_hph->qty2);
                array_push($table_total,$table_target);
                $table_ready  = array('ket'=>'Belum diproses','qty'=>isset($total_hph->qty_ready)? $total_hph->qty_ready : 0,'qty2'=>isset($total_hph->qty2_ready)? $total_hph->qty2_ready : 0);
                array_push($table_total,$table_ready);
                $table_hasil  = array('ket'=>'Sudah diproses','qty'=>isset($total_hph->hasil_qty)? $total_hph->hasil_qty : 0,'qty2'=>isset($total_hph->hasil_qty2)? $total_hph->hasil_qty2:0);
                array_push($table_total,$table_hasil);

                $hasil_grade         = $this->m_inlet->get_total_hph_by_grade($id_inlet);
                $callback     = array("hasil_hph" => $table_total, 'hasil_hph_grade'=>$hasil_grade);
                $this->output->set_status_header(200)
                        ->set_content_type('application/json', 'utf-8')->set_output(json_encode($callback));
            }
        
        } catch(Exception $ex){
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }


    }

    public function get_uom_select2()
    {
	    $prod = addslashes($this->input->post('prod'));
   		$callback = $this->m_inlet->get_list_uom_select2_by_prod($prod);
        echo json_encode($callback);
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
                $callback = array('status' => 'failed', 'message' => 'Data Inlet tidak bisa di batalkan, Data Inlet sudah Done !', 'icon' =>'fa fa-warning', 'type' => 'danger');
            }else if($cek_status->status == 'process'){
                $callback = array('status' => 'failed', 'message' => 'Data Inlet tidak bisa di batalkan, Data Inlet sedang di Process !', 'icon' =>'fa fa-warning', 'type' => 'danger');
            }else if($cek_status->status == 'cancel'){
                $callback = array('status' => 'failed', 'message' => 'Data Inlet tidak bisa di batalkan, Data Inlet sudah Cancel / dibatalkan !', 'icon' =>'fa fa-warning', 'type' => 'danger');
            }else{

                // cek hasil hph by lot
                $cek_hasil = $this->m_inlet->cek_mrp_rm_hasil_by_lot($cek_status->kode_mrp,$cek_status->lot)->num_rows();

                if($cek_hasil > 0 ){
                    $callback = array('status' => 'failed', 'message' => 'Data Inlet tidak bisa di batalkan, Data Inlet sudah terdapat HPH !', 'icon' =>'fa fa-warning', 'type' => 'danger');
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

    function get_data_hph_inlet()
    {
        if(isset($_POST['start']) && isset($_POST['draw'])){
            $kode = $this->input->post('kode');// id inlet
            $list = $this->m_inlet->get_datatables2($kode);
            $data = array();
            $no = $_POST['start'];
            foreach ($list as $field) {
                $no++;
                $row = array();
                $row[] = $no;
                $row[] = $field->create_date;
                $row[] = $field->kode_produk;
                if($field->nama_grade == 'F'){
                    $row[] = $field->nama_produk;
                }else{
                    $row[] = ' <a href="javascript:void(0)" class="edit_lot" title="Edit" data-toggle="tooltip" data-quant="'.$field->quant_id.'">'.$field->nama_produk.'</a>';
                }
                
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
                $row[] = $field->nama_user;
                $data[] = $row;
            }
    
            $output = array(
                "draw" => $_POST['draw'],
                "recordsTotal" => $this->m_inlet->count_all2($kode),
                "recordsFiltered" => $this->m_inlet->count_filtered2($kode),
                "data" => $data,
            );
            //output dalam format JSON
            echo json_encode($output);

        }else{
            die();
        }
    }

    function edit_lot_modal()
    {
        try{
            $kode               = $this->input->post('id');// id_inlet
            $quant_id           = $this->input->post('quant_id');
            $data['kode']       = $kode;
            $data['quant_id']   = $quant_id;
            if(empty($kode) or empty($quant_id)){
                throw new \Exception('Data Lot Kosong !', 500);
            }
            $data_hph_lot       = $this->m_inlet->get_data_lot_hph_by_kode($kode,$quant_id);
            $kode_mg            = $data_hph_lot->kode_mrp ?? '';
            // cek list satuan
            $list_uom_lot       = $this->m_inlet->get_list_uom_by_lot($kode_mg,$quant_id);
            $data['list_uom_ready'] = ($list_uom_lot);
            $data['list_uom_lot']   = json_encode($list_uom_lot);
            if(empty($data_hph_lot)){
                throw new \Exception('Data tidak Ditemukan !', 500);
            }
            $data['data_hph_lot']   = $data_hph_lot;
            $data['desain_barcode']   = $this->_module->get_list_desain_barcode_by_type('LBK');     
            // $data['kode_k3l']   = $this->_module->get_list_kode_k3l();        
            return $this->load->view('modal/v_hph_lot_edit_modal',$data);

        }catch(Exception $ex){
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }

    function save_edit_hph_lot()
    {
        try{
            if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
                // session habis
                throw new \Exception('Waktu Anda Telah Habis', 401);
            }else{

                $id_inlet   = $this->input->post('kode');
                $quant_id   = $this->input->post('quant_id');
                $lot        = $this->input->post('lot');
                $corak_remark = $this->input->post('corak_remark');
                $warna_remark = $this->input->post('warna_remark');
                $qty_jual     = (double)$this->input->post('qty_jual');
                $uom_qty_jual = $this->input->post('uom_qty_jual');
                $qty2_jual    = (double)$this->input->post('qty2_jual');
                $uom_qty2_jual= $this->input->post('uom_qty2_jual');
                $lebar_jadi = $this->input->post('lebar_jadi');
                $uom_lebar_jadi = $this->input->post('uom_lebar_jadi');

                $sub_menu           = $this->uri->segment(2);
                $username  = addslashes($this->session->userdata('username'));

                // start transaction
                $this->_module->startTransaction();

                $tgl            = date('Y-m-d H:i:s');
                $inlet = $this->m_inlet->get_data_inlet_by_id($id_inlet);

                if(empty($inlet)){
                    throw new \Exception('Data Inlet tidak ditemukan !', 200);
                }

                //lock table 
                $this->_module->lock_tabel('log_history WRITE, user WRITE, main_menu_sub WRITE, mrp_satuan WRITE,stock_quant WRITE, picklist_detail WRITE, mrp_inlet WRITE, mrp_production_fg_hasil WRITE, stock_move_items WRITE');

                //get data stock by kode
                $get = $this->_module->get_stock_quant_by_id($quant_id)->row();
                if(empty($get) or empty($quant_id)){
                    throw new \Exception('Data Lot'.$lot.' tidak ditemukan di Stock !', 200);
                }else if($get->lokasi != 'GJD/Stock'){
                    throw new \Exception('Lokasi tidak valid, Data Lot'.$lot.' berada dilokasi '.$get->lokasi ?? '' .' !', 200);
                }else{
                    if($get->nama_grade == 'F'){
                        $callback = array('status' => 'failed', 'message' => 'Grade F tidak bisa Edit HPH !', 'icon' => 'fa fa-warning' , 'type' => 'danger');
                    }else if(empty($corak_remark)){
                        $callback = array('status' => 'failed', 'message' => 'Corak Remark Harus diisi !', 'icon' => 'fa fa-warning' , 'type' => 'danger');
                    }else if(empty($warna_remark) AND $get->nama_grade != 'C'){
                        $callback = array('status' => 'failed', 'message' => 'Warna Remark Harus diisi !', 'icon' => 'fa fa-warning' , 'type' => 'danger');                    
                    }else if(empty($qty_jual)){
                        $callback = array('status' => 'failed', 'message' => 'Qty Jual Harus diisi !', 'icon' => 'fa fa-warning' , 'type' => 'danger');
                    }else if(empty($uom_qty_jual)){
                        $callback = array('status' => 'failed', 'message' => 'Uom Jual Harus diisi !', 'icon' => 'fa fa-warning' , 'type' => 'danger');
                    }else if(!empty($qty2_jual) AND empty($uom_qty2_jual)){
                        $callback = array('status' => 'failed', 'message' => 'Uom2 Jual Harus diisi !', 'icon' => 'fa fa-warning' , 'type' => 'danger');
                    }else if(empty($lebar_jadi) AND $get->nama_grade != 'C'){
                        $callback = array('status' => 'failed', 'message' => 'Lebar Jadi Harus diisi !', 'icon' => 'fa fa-warning' , 'type' => 'danger');
                    }else if(empty($uom_lebar_jadi) AND $get->nama_grade != 'C'){
                        $callback = array('status' => 'failed', 'message' => 'Uom Lebar Jadi Harus diisi !', 'icon' => 'fa fa-warning' , 'type' => 'danger');
                    }else{

                        // cek apakah barcode masuk PL
                        $cek_pl = $this->m_inlet->cek_barcode_in_picklist($quant_id,$lot)->row();
                        if(!empty($cek_pl)){
                            throw new \Exception('Tidak Bisi disimpan, Data Lot '.$lot.' Sudah Masuk PL !', 200);
                        }
                        $kode_mrp           = $inlet->kode_mrp;
                        $kode_produk_fg     = $get->kode_produk;
                        $nama_produk_fg     = $get->nama_produk;
                        $data_qty_update    = array(array('qty'=>$qty_jual,'uom'=>$uom_qty_jual),array('qty'=>$qty2_jual,'uom'=>$uom_qty2_jual));
                        $row_satuan         = $this->m_inlet->get_row_order_mrp_satuan_by_kode($kode_mrp,$quant_id);
                        $rows               = $row_satuan;
                        $data_insert_uom    = array();
                        $data_update_uom    = array();
                        $same               = false;
                        $note_log_uom_qty   = "";
                        $note_log_uom_qty2   = "";
                        $note_log_uom_qty2   = "";
                        $note_log_update_uom_jual   = "";

                        $list_uom_ready =  $this->m_inlet->get_list_uom_by_lot($kode_mrp,$quant_id);

                        // cek uom jual ada di table?                        
                        $ids = array_column($list_uom_ready, 'uom', 'uom');
                        isset($ids[$uom_qty_jual])? $qty_jual_ready = true: $qty_jual_ready = false;

                        if($get->qty_jual != $qty_jual || $get->uom_jual != $uom_qty_jual){
                            if($qty_jual_ready == false AND $qty_jual > 0){
                                // insert mrp satuan baru   
                                $data_insert_uom[] = array('kode'         => $kode_mrp,
                                                'tanggal'           => $tgl,
                                                'quant_id'          => $quant_id,
                                                'kode_produk'       => $kode_produk_fg,
                                                'nama_produk'       => $nama_produk_fg,
                                                'lot'               => trim($lot),
                                                'qty'               => $qty_jual,
                                                'uom'               => $uom_qty_jual,
                                                'row_order'         => $row_satuan,
                            );
                            $row_satuan++;
                            $note_log_uom_qty = "<br> ".$qty_jual." ".$uom_qty_jual;
                            }else{ // update qty
                                if($qty2_jual > 0){
                                    $data_update_uom[] = array('qty'=> $qty_jual, 'uom' => $uom_qty_jual);
                                    $note_log_update_uom_jual = $qty_jual." ".$uom_qty_jual;
                                }
                            }
                        }


                        $ids = array_column($list_uom_ready, 'uom', 'uom');
                        isset($ids[$uom_qty2_jual])? $qty2_jual_ready = true: $qty2_jual_ready = false;

                        if($get->qty2_jual != $qty2_jual || $get->uom2_jual != $uom_qty2_jual){
                            if($qty2_jual_ready == false AND $uom_qty_jual != $uom_qty2_jual AND $qty2_jual > 0){
                                $data_insert_uom[] = array('kode'         => $kode_mrp,
                                                        'tanggal'           => $tgl,
                                                        'quant_id'          => $quant_id,
                                                        'kode_produk'       => $kode_produk_fg,
                                                        'nama_produk'       => $nama_produk_fg,
                                                        'lot'               => trim($lot),
                                                        'qty'               => $qty2_jual,
                                                        'uom'               => $uom_qty2_jual,
                                                        'row_order'         => $row_satuan,
                                );
                                $row_satuan++;
                                $note_log_uom_qty2 = "<br> ".$qty2_jual." ".$uom_qty2_jual;
                            }else{ // update qty
                                if($qty2_jual > 0){
                                    $data_update_uom[] = array('qty'=> $qty2_jual, 'uom' => $uom_qty2_jual);
                                    $note_log_update_uom_jual = $qty2_jual." ".$uom_qty2_jual;
                                }

                            }
                        }


                        $note_log_before = $get->corak_remark." ".$get->warna_remark." ".$get->qty_jual." ".$get->uom_jual." ".$get->qty2_jual." ".$get->uom2_jual." ".$get->lebar_jadi." ".$get->uom_lebar_jadi." <b>-></b>";

                        // update stock_quant
                        $data_update = array(
                                        'corak_remark' => $corak_remark,
                                        'warna_remark' => $warna_remark,
                                        'qty_jual'     => $qty_jual,
                                        'uom_jual'     => $uom_qty_jual,
                                        'qty2_jual'    => $qty2_jual,
                                        'uom2_jual'    => $uom_qty2_jual,
                                        'lebar_jadi'   => $lebar_jadi,
                                        'uom_lebar_jadi'=> $uom_lebar_jadi
                        );

                        $update = $this->m_inlet->update_date_stock_quant($data_update,$quant_id);
                        if(!empty($update)){
                            throw new \Exception('Gagal Simpan Data Lot !', 200);
                        }

                        if($get->lebar_jadi != $lebar_jadi or $get->uom_lebar_jadi != $uom_lebar_jadi){
                            $data_update_lebar = array('lebar_jadi'=> $lebar_jadi,'uom_lebar_jadi'=> $uom_lebar_jadi);
                            $update_fg_hasil = $this->m_inlet->update_data_mrp_fg_hasil($data_update_lebar,$kode_mrp,$quant_id);
                            if(!empty($update_fg_hasil)){
                                throw new \Exception('Gagal Simpan Data Lot !', 200);
                            }

                            $update_smi = $this->m_inlet->update_data_stock_move_items($data_update_lebar,$quant_id);
                            if(!empty($update_smi)){
                                throw new \Exception('Gagal Simpan Data Lot !', 200);
                            }
                        }


                        // update data uom
                        $data_where_update = array('quant_id'=>$quant_id, 'kode'=>$kode_mrp);
                        if(!empty($data_update_uom)){
                            $update_uom = $this->m_inlet->update_data_mrp_satuan_batch($data_update_uom,$data_where_update);
                        }

                        //insert data uom
                        if(!empty($data_insert_uom)){
                            $insert_uom = $this->m_inlet->save_mrp_satuan_batch($data_insert_uom);
                            if(!empty($insert_uom)){
                                throw new \Exception('Gagal Simpan Uom baru !'.$insert_uom, 500);
                            }
                        }

                        if(!empty($note_log_uom_qty2) || !empty($note_log_uom_qty)){
                            $note_log_insert_uom = "<br> Tambah List Uom Jual ".$note_log_uom_qty." ".$note_log_uom_qty2;
                        }else{
                            $note_log_insert_uom = "";
                        }

                        if(!empty($note_log_update_uom_jual)){
                            $note_log_update_uom = "<br> Update List Uom Jual <br>".$note_log_update_uom_jual;
                        }else{
                            $note_log_update_uom = "";
                        }

                        $jenis_log = "edit";
                        $note_log  = "Edit Lot ".$lot." <br> ".$note_log_before." ".$corak_remark." ".$warna_remark." ".$qty_jual." ".$uom_qty_jual." ".$qty2_jual." ".$uom_qty2_jual." ".$lebar_jadi." ".$uom_lebar_jadi." ".$note_log_insert_uom." ".$note_log_update_uom;
                        $data_history = array(
                                        'datelog'   => date("Y-m-d H:i:s"),
                                        'kode'      => $id_inlet,
                                        'jenis_log' => $jenis_log,
                                        'note'      => $note_log );

                        $this->_module->gen_history_ip($sub_menu,$username,$data_history);

                        $callback = array('status' => 'success', 'message' => 'Data Berhasil disimpan !', 'icon' =>'fa fa-check', 'type' => 'success');

                    }
                    
                    // unlock table
                    $this->_module->unlock_tabel();
                    $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($callback));

                    if (!$this->_module->finishTransaction()) {
                        throw new \Exception('Gagal Simpan data ', 500);
                    }
                }

                // finish transaction
                $this->_module->finishTransaction();

            }
        
        }catch(Exception $ex){
            // unlock table
            $this->_module->unlock_tabel();
            // finish transaction
            $this->_module->finishTransaction();
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('status'=>'failed', 'message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }

    function reprint_barcode_hph()
    {
        try{
            if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
                // session habis
                throw new \Exception('Waktu Anda Telah Habis', 401);
            }else{

                $id_inlet   = $this->input->post('kode');
                $quant_id   = $this->input->post('quant_id');
                $lot        = $this->input->post('lot');
                $desain_barcode  = $this->input->post('desain_barcode');

                if(empty($id_inlet) AND empty($quant_id) AND empty($lot)){
                    throw new \Exception('Data Print Lot Kosong !', 200);
                }else if(empty($desain_barcode)){
                    throw new \Exception('Data Print Lot Kosong !', 200);
                }else{

                    $inlet = $this->m_inlet->get_data_inlet_by_id($id_inlet);
                    if(empty($inlet)){
                        throw new \Exception('Data Inlet tidak ditemukan !', 200);
                    }

                    //get data stock by kode
                    $get = $this->_module->get_stock_quant_by_id($quant_id)->result();
                    if(empty($get)){
                        throw new \Exception('Data '.$lot.' tidak ditemukan !', 200);
                    }
                    
                    $data_print = $this->print_barcode($desain_barcode,$get,$inlet);
                    if(empty($data_print)){
                        throw new \Exception('Data Print tidak ditemukan !', 500);
                    }
                    $callback = array('status' => 'success', 'message' => 'Print Berhasil !', 'icon' =>'fa fa-check', 'type' => 'success', 'data_print' =>$data_print);
                }
                $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($callback));
            }

        }catch(Exception $ex){
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('status'=>'failed','message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }


    function print_barcode($desain_barcode,$data_stock,$inlet)
    {
        $kode_mrp = $inlet->kode_mrp; 
        $kode_k3l = $inlet->kode_k3l;
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
                        'no_pack_brc' => $kode_mrp,
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