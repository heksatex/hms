<?php defined('BASEPATH') or exit ('No Direct Script Acces Allowed');

/**
 * 
 */
class MO extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->is_loggedin();//cek apakah user sudah login
		$this->load->model("m_mo");//load query" di model m_mo
        $this->load->model("_module");
		$this->load->model("m_lab");
        $this->load->library('Pdf');//load library pdf

	}

	public function index()
	{
		$kode_sub   = 'mm_manufacturing';
		$username	= $this->session->userdata('username');
		$row 		= $this->_module->sub_menu_default($kode_sub,$username)->row_array();
		redirect($row['link_menu']);

	}

	public function Twisting()
	{
		$data['id_dept']='TWS';
		$this->load->view('manufacturing/v_mo', $data);
	}

    public function Warpingdasar()
    {
        $data['id_dept']='WRD';
        $this->load->view('manufacturing/v_mo', $data);
    }

    public function Warpingpanjang()
    {
        $data['id_dept']='WRP';
        $this->load->view('manufacturing/v_mo', $data);
    }


    public function Jacquard()
    {
        $data['id_dept']='JAC';
        $this->load->view('manufacturing/v_mo', $data);
    }

	public function Tricot()
	{
		$data['id_dept']='TRI';
		$this->load->view('manufacturing/v_mo', $data);
	}

    public function Raschel()
    {
        $data['id_dept']='RSC';
        $this->load->view('manufacturing/v_mo', $data);
    }

    public function Cuttingshearing()
    {
        $data['id_dept']='CS';
        $this->load->view('manufacturing/v_mo', $data);
    }

    public function Inspecting1()
    {
        $data['id_dept']='INS1';
        $this->load->view('manufacturing/v_mo', $data);
    }

    public function Dyeing()
    {
        $data['id_dept']='DYE';
        $this->load->view('manufacturing/v_mo', $data);
    }

    public function Dyeingreproses()
    {
        $data['id_dept']='DYE-R';
        $this->load->view('manufacturing/v_mo', $data);
    }

    public function Finishing()
    {
        $data['id_dept']='FIN';
        $this->load->view('manufacturing/v_mo', $data);
    }

    public function Finishingreproses()
    {
        $data['id_dept']='FIN-R';
        $this->load->view('manufacturing/v_mo', $data);
    }

    public function Brushing()
    {
        $data['id_dept']='BRS';
        $this->load->view('manufacturing/v_mo', $data);
    }

    public function Brushingreproses()
    {
        $data['id_dept']='BRS-R';
        $this->load->view('manufacturing/v_mo', $data);
    }

    public function Finbrushing()
    {
        $data['id_dept']='FBR';
        $this->load->view('manufacturing/v_mo', $data);
    }

    public function Finbrushingreproses()
    {
        $data['id_dept']='FBR-R';
        $this->load->view('manufacturing/v_mo', $data);
    }

    public function Padding()
    {
        $data['id_dept']='PAD';
        $this->load->view('manufacturing/v_mo', $data);
    }

    public function Paddingreproses()
    {
        $data['id_dept']='PAD-R';
        $this->load->view('manufacturing/v_mo', $data);
    }

    public function Setting()
    {
        $data['id_dept']='SET';
        $this->load->view('manufacturing/v_mo', $data);
    }

    public function Settingreproses()
    {
        $data['id_dept']='SET-R';
        $this->load->view('manufacturing/v_mo', $data);
    }

    public function Inspecting2()
    {
        $data['id_dept']='INS2';
        $this->load->view('manufacturing/v_mo', $data);
    }

    public function Inspecting2reproses()
    {
        $data['id_dept']='INS2-R';
        $this->load->view('manufacturing/v_mo', $data);
    }

    public function Gudangjadi()
    {
        $data['id_dept']='GJD';
        $this->load->view('manufacturing/v_mo', $data);
    }

    public function jadwal_Tricot()
    {
            
        $data['id_dept']='Tricot';
        //$data['mesin']  = $this->m_mo->get_jml_mesin();
        $data['data_mesin']  = $this->m_mo->get_mesin();

        $multi=array();
        foreach ($this->m_mo->get_mesin() as $key){
            $multi[$key->mc_id]=$this->m_mo->get_data_by_mesin($key->mc_id,'','','','TRI');
        }
          $data['arr_multi'] = $multi;

        $this->load->view('manufacturing/v_mo_jadwal',$data);

    }

    public function jadwal_Dyeing()
    {
            
        $data['id_dept']='Dyeing';
        //$data['mesin']  = $this->m_mo->get_jml_mesin();
        $data['data_mesin']  = $this->m_mo->get_mesin();

        $multi=array();
        foreach ($this->m_mo->get_mesin() as $key){
            $multi[$key->mc_id]=$this->m_mo->get_data_by_mesin($key->mc_id,'','','','DYE');
        }
          $data['arr_multi'] = $multi;

        $this->load->view('manufacturing/v_mo_jadwal',$data);

    }

    public function search()
    {

        $prod =  $this->input->POST('product');
        $dari = $this->input->post('dari');
        $sampai = $this->input->post('sampai');

        //$data['id_dept']='TRI';
        //$data['mesin']  = $this->m_mo->get_jml_mesin();
        $data_mesin  = $this->m_mo->get_mesin();

        $multi=array();
        foreach ($this->m_mo->get_mesin() as $key){
          $multi[$key->mc_id]=$this->m_mo->get_data_by_mesin($key->mc_id,$dari,$sampai,$prod);
        }
          $arr_multi = $multi;


        $hasil = $this->load->view('manufacturing/v_mo_jadwal_view',array('data_mesin' => $data_mesin, 'arr_multi'=>$arr_multi), TRUE);
        
        $callback = array( 'hasil' => $hasil );

        echo json_encode($callback); 
        
    }


	function get_data()
    {   
        $sub_menu = $this->uri->segment(2);
        $id_dept  = $this->input->post('id_dept');
        if(isset($_POST['start']) && isset($_POST['draw'])){

            $kode     = $this->_module->get_kode_sub_menu_deptid($sub_menu,$id_dept)->row_array();
            $list = $this->m_mo->get_datatables($id_dept,$kode['kode']);
            $data = array();
            $no = $_POST['start'];
            foreach ($list as $field) {
                $kode_encrypt = encrypt_url($field->kode);
                $no++;
                $row = array();
                $row[] = $no;
                $row[] = '<a href="'.base_url('manufacturing/mO/edit/'.$kode_encrypt).'">'.$field->kode.'</a>';
                $row[] = $field->tanggal;
                $row[] = $field->origin;
                $row[] = $field->nama_produk;
                $row[] = $field->qty;
                $row[] = $field->uom;
                $row[] = $field->reff_note;
                $row[] = $field->responsible;
                $row[] = $field->nama_status;
    
                $data[] = $row;
            }
    
            $output = array(
                "draw" => $_POST['draw'],
                "recordsTotal" => $this->m_mo->count_all($id_dept,$kode['kode']),
                "recordsFiltered" => $this->m_mo->count_filtered($id_dept,$kode['kode']),
                "data" => $data,
            );
            //output dalam format JSON
            echo json_encode($output);
            
        }else{
            die();
        }
    }

    public function edit($kode = null)
    {
        if(!isset($kode)) show_404();
        $kode_decrypt      = decrypt_url($kode);
        $list              = $this->m_mo->get_data_by_code($kode_decrypt);
        if(empty($list)){
            show_404();
        }else{
            
            $username          = addslashes($this->session->userdata('username')); 
            $data["list"]      = $list;
            $lw                = $this->m_mo->get_location_waste_by_deptid($list->dept_id)->row_array();
            $data["rm"]        = $this->m_mo->get_list_bahan_baku($kode_decrypt);
            $data["hasil_rm"]  = $this->m_mo->get_list_bahan_baku_hasil_group($kode_decrypt,'f');
            $data["hasil_rm_add"]  = $this->m_mo->get_list_bahan_baku_hasil_group($kode_decrypt,'t');
            $data["fg"]        = $this->m_mo->get_list_barang_jadi($kode_decrypt);
            $data["hasil_fg"]  = $this->m_mo->get_list_barang_jadi_hasil($kode_decrypt,$lw['waste_location']);
            $data["hasil_waste"]  = $this->m_mo->get_list_barang_jadi_hasil_waste($kode_decrypt,$lw['waste_location']);
            $data["total_fg"]  = $this->m_mo->get_total_fg($kode_decrypt);
            $data['berat']     = $this->m_mo->get_berat_by_kode($kode_decrypt)->row_array();
            $warna             = $this->m_mo->get_warna_by_kode($kode_decrypt)->row_array();
            $orgn              = $list->origin."|".$kode_decrypt;
            $cek_request       = $this->m_mo->cek_origin_di_stock_move($orgn)->row_array();//cek udh request color ?
            $data['handling']  = $this->_module->get_list_handling();
            // akses menu 
            $mms = $this->_module->get_kode_sub_menu_deptid_user('mO',$list->dept_id,$username)->row_array();
            if(!empty($mms['kode'])){
                $mms_kode = $mms['kode'];
            }else{
                $mms_kode = '';
            }
            $data['menu'] = $mms_kode;

            if($list->dept_id == 'TRI' OR $list->dept_id == 'JAC'){
                if($list->type_production =='Proofing'){
                    $lot_prefix   = 'PF/[MY]/[MC]/[DEPT]/COUNTER';
                    $lot_prefix_waste   = 'PF/[MY]/[MC]/[DEPT]/COUNTER';
                }else{
                    $lot_prefix   = 'KP/[MY]/[MC]/[DEPT]/COUNTER';
                    $lot_prefix_waste   = 'KP/[MY]/[MC]/[DEPT]/COUNTER';
                }
            }else{
                $lot_prefix   = $list->lot_prefix;
                $lot_prefix_waste   = $list->lot_prefix_waste;
            }
            

            $data['lot_prefix']       = $lot_prefix;
            $data['lot_prefix_waste'] = $lot_prefix_waste;

            if(!empty($cek_request['origin'])){
                $data['dystuff']   = $this->m_mo->get_dyeing_stuff($kode_decrypt);
                $data['aux']       = $this->m_mo->get_aux($kode_decrypt);
                $data['disable']   = "yes";//untuk disable air dan berat
            }else{
                $data['dystuff']   = "";
                $data['aux']       = "";
                $data['disable']   = "no";
            }

            $data['rm_add']        = $this->m_mo->get_list_bahan_baku_additional($kode_decrypt);
            $data['dystuff_add']   = $this->m_mo->get_dyeing_stuff_additional($kode_decrypt);
            $data['aux_add']       = $this->m_mo->get_aux_additional($kode_decrypt);

            // cek level akses by user
            $level_akses = $this->_module->get_level_akses_by_user($username)->row_array();
            $data['level']       = $level_akses['level'];

            // cek departemen by user
            $cek_dept               = $this->_module->cek_departemen_by_user($username)->row_array();
            $data['cek_dept']       = $cek_dept['dept'];
    
            // cek priv akses menu
            $sub_menu           = $this->uri->segment(2);
            $username           = $this->session->userdata('username'); 
            $kode               = $this->_module->get_kode_sub_menu_deptid($sub_menu,$list->dept_id)->row_array();
            $data['akses_menu'] = $this->_module->cek_priv_menu_by_user($username,$kode['kode'])->num_rows();
       
            $data['mesin']    = $this->m_mo->get_list_mesin($list->dept_id);
            $data['uom']      = $this->_module->get_list_uom();
            $data['type_mo']  = $this->m_mo->cek_type_mo_by_dept_id($list->dept_id)->row_array();
            $data['show_lebar'] = $this->_module->cek_show_lebar_by_dept_id($list->dept_id)->row_array();
            $data['bom']      = $this->m_mo->get_nama_bom_by_kode($list->kode_bom)->row_array();
            $data['move_id_rm'] = $this->m_mo->get_move_id_rm_target_by_kode($kode_decrypt)->row_array();
            $data['move_id_fg'] = $this->m_mo->get_move_id_fg_target_by_kode($kode_decrypt)->row_array();
            return $this->load->view('manufacturing/v_mo_edit',$data);
        }
    }

    public function get_product()
    {
        $id = addslashes($this->input->post('txtProduct'));
        $data['prod'] = $this->_module->get_prod($id)->row_array();
    	return $this->load->view('modal/v_mo_product_modal', $data);
    }

    public function get_bom()
    {
        $kode_bom     = addslashes($this->input->post('kode'));
        $data['bom'] = $this->m_mo->get_data_bom($kode_bom)->row_array();
    	return $this->load->view('modal/v_mo_bom_modal', $data);
    }
 
    public function simpan_rm_additional()
    {

        if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
            // session habis
            $callback = array('message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
        }else{
            $sub_menu   = $this->uri->segment(2);
            $username   = addslashes($this->session->userdata('username')); 

            $kode       = $this->input->post('kode');
            $kode_produk= addslashes($this->input->post('kode_produk'));
            $produk     = addslashes($this->input->post('produk'));
            $qty        = $this->input->post('qty');
            $uom        = addslashes($this->input->post('uom'));
            $reff       = addslashes($this->input->post('reff'));
            $type_obat  = addslashes($this->input->post('type_obat'));
            $origin_prod= addslashes($this->input->post('origin_prod'));
            $row_order  = $this->input->post('row_order');

            //cek status done ?
            $cek1  = $this->m_mo->cek_status_mrp_production($kode,'done')->row_array();
            //cek status cancel ?
            $cek2  = $this->m_mo->cek_status_mrp_production($kode,'cancel')->row_array();
            //cek status hold ?
            $cek3  = $this->m_mo->cek_status_mrp_production($kode,'hold')->row_array();
            // cek status kain
            $status_kain = $this->m_mo->cek_status_produk_kain($kode)->row_array();
            // cek apakah terdapat HPH
            $cek_fg_hasil = $this->m_mo->cek_mrp_production_fg_hasil($kode)->num_rows();

            $level_akses    = $this->_module->get_level_akses_by_user($username)->row_array();
            $level          = $level_akses['level'];

            // cek departemen by user
            $cek_dept = $this->_module->cek_departemen_by_user($username)->row_array();

            if(empty($kode)){
                $callback = array('status' => 'failed', 'message' => 'Kode MO/MG Kosong !', 'icon' =>'fa fa-warning', 'type' => 'danger');
            }else if(!empty($cek1['status'])){
                $callback = array('status' => 'failed', 'message'=>'Maaf, Proses Produksi telah Selesai !', 'icon' => 'fa fa-warning', 'type'=>'danger');
            }else if(!empty($cek2['status'])){
               $callback = array('status' => 'failed', 'message'=>'Maaf, Proses Produksi telah dibatalkan !', 'icon' => 'fa fa-warning', 'type'=>'danger');
            }else if(!empty($cek3['status'])){
                $callback = array('status' => 'failed', 'message'=>'Maaf, Proses Produksi ditunda / Hold !', 'icon' => 'fa fa-warning', 'type'=>'danger');

            }else if(($level != "Super Administrator" AND $level  != "Administrator" AND  strpos($cek_dept['dept'], 'PPIC') === false) ){
                // AND   $cek_dept['dept'] != 'PPIC'
                $callback = array('status' => 'failed', 'message'=>'Maaf, Anda tidak bisa menyimpan Addtional !', 'icon' => 'fa fa-warning', 'type'=>'danger');

            }else if(empty($status_kain['status']) AND $type_obat != 'rm'){ // hanya DYE, AUX
                $callback = array('message' => 'Maaf, Produk (kain) belum Ready !', 'icon' => 'fa fa-warning', 'type'=>'danger', 'status' => 'failed' );
            }else if($cek_fg_hasil > 0 AND $type_obat != 'rm'){ // hanya DYE, AUX
                $callback = array('status' => 'failed', 'message'=>'Maaf, Anda tidak bisa menyimpan Product Additional karena '.$kode.' ini sudah terdapat HPH  !', 'icon' => 'fa fa-warning', 'type'=>'danger');

            }else if(empty($kode_produk) OR empty($produk)){
                $callback = array('status' => 'failed2',   'message' => 'Product Harus Diisi !', 'icon' =>'fa fa-warning', 'type' => 'danger');
            }elseif(empty($qty)){
                $callback = array('status' => 'failed', 'message' => 'Qty Harus Diisi !', 'icon' =>'fa fa-warning', 'type' => 'danger');
            }elseif(empty($uom)){
                $callback = array('status' => 'failed2','message' => 'Uom Harus Diisi !', 'icon' =>'fa fa-warning', 'type' => 'danger');
            }else{
                
                //lock table
                $this->_module->lock_tabel('mrp_production WRITE, mrp_production_rm_target WRITE, mrp_production_rm_target as rm WRITE, mst_produk as mp WRITE');

                if(!empty($row_order)){
                    
                    if($type_obat == 'DYE'){
                        $capt_edit = 'Edit Data Additional Dyeing Stuff ';
                    }else  if($type_obat == 'AUX'){
                        $capt_edit = 'Edit Data Additional Auxiliary';
                    }else{
                        $capt_edit = 'Edit Data Additional ';
                    }

                    // get produk
                    $get = $this->m_mo->get_data_rm_target_additional_by_kode($kode,$origin_prod,$row_order)->row_array();

                    // cek move_id apakah sudah ada atau kosong
                    $cek_move_rm = $this->m_mo->cek_move_id_rm_additional_by_kode($kode,$origin_prod,$row_order)->row_array();
                    if(!empty($cek_move_rm['move_id'])){
                        $callback = array('message' => 'Maaf, Product tidak bisa diubah, karena sudah proses Request',  'status' => 'failed2' );
                        // unlock table
                        $this->_module->unlock_tabel();
                    }else if(empty($get['kode_produk'])){
                        $callback = array('message' => 'Maaf, Product yang akan diubah tidak ditemukan',  'status' => 'failed2' );
                        // unlock table
                        $this->_module->unlock_tabel();
                    }else{

                        $produk_before = $get['kode_produk'].' '.$get['nama_produk'].' '.$get['qty'].' '.$get['uom'].' '.$get['reff_note'];

                        $origin_prod = $kode_produk.'_'.$row_order;
    
                        $this->m_mo->update_rm($kode,$kode_produk,$produk,$qty,$uom,$reff,$origin_prod,'t',$row_order);
    
                        // unlock table
                        $this->_module->unlock_tabel();
       
                        $jenis_log   = "edit";
                        $note_log    = $capt_edit." | ".$produk_before." -> ".$kode_produk."  ".$produk."  ".$qty."  ".$uom."  ".$reff;
                        $this->_module->gen_history($sub_menu, $kode, $jenis_log, $note_log, $username);
                        $callback= array('status' => 'success', 'message' => 'Data Berhasil Disimpan !', 'icon' =>'fa fa-check', 'type' => 'success');
                    }
                    
                }else{

                    if($type_obat == 'DYE'){
                        $capt_tambah = 'Tambah Data Additional Dyeing Stuff ';

                    }else  if($type_obat == 'AUX'){
                        $capt_tambah = 'Tambah Data Additional Auxiliary';

                    }else{
                        $capt_tambah = 'Tambah Data Additional';
                    }

                    // get row order rm target 
                    $row         = $this->m_mo->get_row_order_rm_add($kode);
                    $origin_prod = $kode_produk.'_'.$row;
                    $status      = 'draft';
                    $this->m_mo->save_rm($kode,$kode_produk,$produk,$qty,$uom,$reff,$status,$origin_prod,'t',$row);

                     // unlock table
                     $this->_module->unlock_tabel();

                    $jenis_log   = "edit";
                    $note_log    = $capt_tambah." | ".$kode_produk."  ".$produk."  ".$qty."  ".$uom."  ".$reff;
                    $this->_module->gen_history($sub_menu, $kode, $jenis_log, $note_log, $username);
                    $callback= array('status' => 'success', 'message' => 'Data Berhasil Ditambahkan !', 'icon' =>'fa fa-check', 'type' => 'success');
                }
            }


        }

      echo json_encode($callback) ; 
    }

    public function hapus_rm()
    {
        if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
            // session habis
            $callback = array('message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
        }else{
            $sub_menu   = $this->uri->segment(2);
            $username   = addslashes($this->session->userdata('username'));

            $kode            =  $this->input->post('kode');
            $origin_prod     =  $this->input->post('origin_prod');
            $row_order       =  $this->input->post('row_order');

            //lock table
            $this->_module->lock_tabel('mrp_production WRITE, mrp_production_rm_target WRITE, mrp_production_rm_target as rm WRITE, mst_produk as mp WRITE, user WRITE');

            //cek status done ?
            $cek1  = $this->m_mo->cek_status_mrp_production($kode,'done')->row_array();
            //cek status cancel ?
            $cek2  = $this->m_mo->cek_status_mrp_production($kode,'cancel')->row_array();
            //cek status hold ?
            $cek3  = $this->m_mo->cek_status_mrp_production($kode,'hold')->row_array();

            // get data by row order
            $get = $this->m_mo->get_data_rm_target_additional_by_kode($kode,$origin_prod,$row_order)->row_array();

            $level_akses    = $this->_module->get_level_akses_by_user($username)->row_array();
            $level          = $level_akses['level'];

            // cek departemen by user
            $cek_dept = $this->_module->cek_departemen_by_user($username)->row_array();
            
            if(!empty($cek1['status'])){
                $callback = array('status' => 'failed', 'message'=>'Maaf, Proses Produksi telah Selesai !', 'icon' => 'fa fa-warning', 'type'=>'danger');
                $this->_module->unlock_tabel();
            }else if(!empty($cek2['status'])){
               $callback = array('status' => 'failed', 'message'=>'Maaf, Proses Produksi telah dibatalkan !', 'icon' => 'fa fa-warning', 'type'=>'danger');
               $this->_module->unlock_tabel();
            }else if(!empty($cek3['status'])){
                $callback = array('status' => 'failed', 'message'=>'Maaf, Proses Produksi ditunda / Hold !', 'icon' => 'fa fa-warning', 'type'=>'danger');
                $this->_module->unlock_tabel();
            }else if(($level != "Super Administrator" AND $level  != "Administrator" AND  strpos($cek_dept['dept'], 'PPIC') === false) ){
                $callback = array('status' => 'failed', 'message'=>'Maaf, Anda tidak bisa mengahapus Addtional !', 'icon' => 'fa fa-warning', 'type'=>'danger');
                $this->_module->unlock_tabel();
            }else if(empty($get['kode_produk'])){
                $callback = array('message' => 'Maaf, Product yang akan diubah tidak ditemukan',  'status' => 'failed' );
                // unlock table
                $this->_module->unlock_tabel();
            }else{
                $produk_del = $get['kode_produk'].' '.$get['nama_produk'].' '.$get['qty'].' '.$get['uom'].' '.$get['reff_note'];

                $this->m_mo->delete_rm($kode, $origin_prod,$row_order);

                // unlock table
                $this->_module->unlock_tabel();

                $jenis_log   = "cancel";
                $note_log    = "Hapus Additional | ".$produk_del;
                $this->_module->gen_history($sub_menu, $kode, $jenis_log, $note_log, $username);

                $callback = array('status' => 'success', 'message'=>'Data Berhasil di hapus !', 'icon' => 'fa fa-check', 'type'=>'success');
            }
        }

        echo json_encode($callback);
    }   

    public function request_additional()
    {
        if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
            // session habis
            $callback = array('message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
        }else{

            $sub_menu   = $this->uri->segment(2);
            $username   = addslashes($this->session->userdata('username'));
            $nu       = $this->_module->get_nama_user($username)->row_array();
            $nama_user= addslashes($nu['nama']);

            $kode       =  $this->input->post('kode');
            $origin_mo  =  $this->input->post('origin_mo');
            $deptid     =  $this->input->post('deptid');


            //cek status done ?
            $cek1  = $this->m_mo->cek_status_mrp_production($kode,'done')->row_array();
            //cek status cancel ?
            $cek2  = $this->m_mo->cek_status_mrp_production($kode,'cancel')->row_array();
            //cek status hold ?
            $cek3  = $this->m_mo->cek_status_mrp_production($kode,'hold')->row_array();
            // cek status kain
            $status_kain = $this->m_mo->cek_status_produk_kain($kode)->row_array();
            // cek apakah terdapat HPH
            $cek_fg_hasil = $this->m_mo->cek_mrp_production_fg_hasil($kode)->num_rows();
            // cek type MO/MG by dept
            $tp  = $this->m_mo->cek_type_mo_by_dept_id($deptid)->row_array();

            $level_akses    = $this->_module->get_level_akses_by_user($username)->row_array();
            $level          = $level_akses['level'];

            // cek departemen by user
            $cek_dept = $this->_module->cek_departemen_by_user($username)->row_array();
  
            if(!empty($cek1['status'])){
                $callback = array('status' => 'failed', 'message'=>'Maaf, Proses Produksi telah Selesai !', 'icon' => 'fa fa-warning', 'type'=>'danger');
            }else if(!empty($cek2['status'])){
                $callback = array('status' => 'failed', 'message'=>'Maaf, Proses Produksi telah dibatalkan !', 'icon' => 'fa fa-warning', 'type'=>'danger');
            }else if(!empty($cek3['status'])){
                $callback = array('status' => 'failed', 'message'=>'Maaf, Proses Produksi ditunda / Hold !', 'icon' => 'fa fa-warning', 'type'=>'danger');
            }else if($level != "Super Administrator" AND $level  != "Administrator" AND  strpos($cek_dept['dept'], 'PPIC') === false ){
                $callback = array('status' => 'failed', 'message'=>'Maaf, Anda tidak bisa melakukan Request Addtional !', 'icon' => 'fa fa-warning', 'type'=>'danger');
            }else if(empty($status_kain['status']) AND $tp['type_mo'] == 'colouring'){
                $callback = array('message' => 'Maaf, Produk (kain) belum Ready !',  'status' => 'failed', 'icon' => 'fa fa-warning', 'type'=>'danger');
            }else if($cek_fg_hasil > 0 AND $tp['type_mo'] == 'colouring'){
                $callback = array('status' => 'failed', 'message'=>'Maaf, Anda tidak bisa Request Additional karena '.$kode.' ini sudah terdapat HPH  !', 'icon' => 'fa fa-warning', 'type'=>'danger');
            }else{

                // lock table
                $this->_module->lock_tabel('mrp_production WRITE, mrp_production_rm_target WRITE, mrp_production_fg_hasil WRITE, stock_move WRITE, mrp_route WRITE, pengiriman_barang WRITE, penerimaan_barang WRITE, main_menu_sub WRITE, log_history WRITE, mrp_production_rm_target as rm WRITE, mst_produk as mp WRITE, mrp_production as m WRITE, stock_move_produk WRITE, pengiriman_barang_items WRITE, penerimaan_barang_items WRITE, departemen WRITE, departemen as d WRITE');
                
                // cek rm target additonal yg move id nya kosong
                $cek_add    = $this->m_mo->cek_rm_target_additional($kode)->num_rows();

                if($cek_add == 0){
                    $callback = array('status' => 'failed', 'message'=>'Maaf, Product yang akan di Request tidak ada !', 'icon' => 'fa fa-warning', 'type'=>'danger');
                    //unlock table
                    $this->_module->unlock_tabel();
                }else{

                    if($tp['type_mo'] == 'knitting' || $deptid == 'GJD'){
                        $orgn_set   = $origin_mo."|".$kode."|ADD"; // ex ORIGIN MO|MO|ADD

                        $last_move   = $this->_module->get_kode_stock_move();
                        $move_id     = "SM".$last_move; //Set kode stock_move
                        $source_move = "";
                        $tgl         = date("Y-m-d H:i:s");
                        $i           = 1;
                        $sql_stock_move_batch       = "";
                        $sql_stock_move_produk_batch= "";
                        $case                       = "";
                        $where                      = "";
                       
                        $sm_row = 1;///stock move row_order
                        $empty_item = TRUE;

                        //cek origin additional
                        $count_orgn = $this->m_mo->get_list_move_id_rm_by_kode($kode,'t')->num_rows();
                        $recount    = $count_orgn + 1;
                        $orgn       = $orgn_set."|".$recount;

                        $cek_request  = $this->m_mo->cek_origin_di_stock_move($orgn)->row_array();//cek apa sudah request obat ?

                        if(!empty($cek_request['origin'])){
                            $callback = array('message' => 'Maaf, Anda sudah melakukan Request Additional '.$recount.' !',  'status' => 'failed' );
                            //unlock table
                            $this->_module->unlock_tabel();
                        }else{
                            // get lokasi production_location, stock_location
                            $dept          = $this->_module->get_nama_dept_by_kode($deptid)->row_array();// get ,copy_bahanbaku true/false
                            $lokasi_dari   = $dept['stock_location'];//stock location
                            $lokasi_tujuan = $dept['production_location'];//production location
                            $method        = $deptid.'|CON';
                            //stock move 
                            $origin = $orgn;
                            $sql_stock_move_batch .= "('".$move_id."','".$tgl."','".$origin."','".$method."','".$lokasi_dari."','".$lokasi_tujuan."','draft','".$sm_row."',''), ";
                            $sm_row = $sm_row + 1;

                            // list rm target additional 
                            $where_category  = " mp.id_category NOT IN ('11','12') ";
                            $items_rm = $this->m_mo->get_data_rm_target_additional_by_kode_all($kode,$where_category);
                            $smp_row  = 1;
                            foreach($items_rm as $smp){
                                $empty_item = FALSE;
                                //simpan ke stock move produk 
                                $sql_stock_move_produk_batch .= "('".$move_id."','".addslashes($smp->kode_produk)."','".addslashes($smp->nama_produk)."','".$smp->qty."','".addslashes($smp->uom)."','draft','".$smp_row."','".addslashes($smp->origin_prod)."'), ";
                                $smp_row++;
                            }

                            if($empty_item == TRUE){
                                $callback = array('message' => 'Maaf, Request Additional belum bisa dilakukan ! ',  'status' => 'failed' );
                                //unlock table
                                $this->_module->unlock_tabel();
                            }else{

                                 //action sql query
                                 if(!empty($sql_stock_move_batch)){
                                    $sql_stock_move_batch = rtrim($sql_stock_move_batch, ', ');
                                    $this->_module->create_stock_move_batch($sql_stock_move_batch);

                                    if(!empty($sql_stock_move_produk_batch)){
                                        $sql_stock_move_produk_batch = rtrim($sql_stock_move_produk_batch, ', ');
                                        $this->_module->create_stock_move_produk_batch($sql_stock_move_produk_batch);
                                    }
                                }

                                $sql_update_rm_move_id  = "UPDATE mrp_production_rm_target SET move_id = '".$move_id."' WHERE  kode in ('".$kode."') AND move_id = '' AND additional ='t'";
                                $this->_module->update_reff_batch($sql_update_rm_move_id);

                                //unlock table
                                $this->_module->unlock_tabel();
                                
                                $jenis_log   = "edit";
                                $note_log    = "Request Additional -> ".$orgn;
                                $this->_module->gen_history_deptid($sub_menu, $kode, $jenis_log, $note_log, $username, $deptid);
                                
                                $callback = array('status'=>'success', 'message' => 'Request Additioanl Berhasil !',  'icon' =>'fa fa-check', 'type' => 'success');

                            }
                        
                        }
                        
                    }else if($deptid == 'DYE' || $deptid == 'DYE-R'){

                        $orgn_set   = $origin_mo."|".$kode."|ADD|OBAT"; // ex SO18|CO7|2|OW210300001|MG210300004|ADD|OBAT

                        $last_move   = $this->_module->get_kode_stock_move();
                        $move_id     = "SM".$last_move; //Set kode stock_move
                        $source_move = "";
                        $tgl         = date("Y-m-d H:i:s");
                        $i           = 1;
                        $reff_notes_additional      = "Request Additional ".$kode;
                        $sql_stock_move_batch       = "";
                        $sql_stock_move_produk_batch= "";
                        $sql_out_batch              = "";
                        $sql_out_items_batch        = ""; 
                        $sql_in_batch               = "";
                        $sql_in_items_batch         = "";
                        $case                       = "";
                        $where                      = "";
                        $case2                      = "";
                        $where2                     = "";
                        $case3                      = "";
                        //$where3                     = "";
                        //$case4                      = "";
                        //$where4                     = "";
                        $arr_kode[]                 = "";
                        $kode_out[]                 = "";
                        $sql_log_history_in         = "";
                        $sql_log_history_out        = "";

                        if($deptid == 'DYE-R'){
                            $route  = $this->m_mo->get_route_warna('obat_dyeing_reproses');
                        } else {
                            $route  = $this->m_mo->get_route_warna('obat_dyeing');
                        }
                        $sm_row = 1;///stock move row_order
                        $empty_item = TRUE;

                        //cek origin additional
                        $count_orgn = $this->m_mo->get_list_move_id_rm_obat_by_kode($kode,'t')->num_rows();
                        $recount    = $count_orgn + 1;
                        $orgn       = $orgn_set."|".$recount; // example SC3263|CO21|1|OW22060048|MG220600131|ADD|1

                        $cek_request  = $this->m_mo->cek_origin_di_stock_move($orgn)->row_array();//cek apa sudah request obat ?

                        if(!empty($cek_request['origin'])){
                            $callback = array('message' => 'Maaf, Anda sudah melakukan Request Additional Obat '.$recount.' !',  'status' => 'failed' );
                            //unlock table
                            $this->_module->unlock_tabel();
                        }else{

                            foreach($route as $val){

                                $empty_item = FALSE;
                        
                                $mthd          = explode("|",$val->method);
                                $method_dept   = trim($mthd[0]);
                                $method_action = trim($mthd[1]);

                                //stock move 
                                $origin = $orgn;
                                $sql_stock_move_batch .= "('".$move_id."','".$tgl."','".$origin."','".$val->method."','".$val->lokasi_dari."','".$val->lokasi_tujuan."','draft','".$sm_row."','".$source_move."'), ";
                                $sm_row = $sm_row + 1;

                                // list rm target additional 
                                $where_category  = " mp.id_category IN ('11','12') ";
                                $items_rm = $this->m_mo->get_data_rm_target_additional_by_kode_all($kode,$where_category);

                                if($method_action == 'OUT'){//pengiriman barang

                                    if($i=="1"){
                                    $arr_kode[$val->method]= $this->_module->get_kode_pengiriman($method_dept);
                                    }else{
                                    $arr_kode[$val->method]= $arr_kode[$val->method] + 1;
                                    }
                                    $dgt=substr("00000" . $arr_kode[$val->method],-5);            
                                    $kode_out = $method_dept."/".$method_action."/".date("y").  date("m").$dgt;
                                
                                    //pengiriman barang
                                    $sql_out_batch  .= "('".$kode_out."','".$tgl."','".$tgl."','".$tgl."','".$reff_notes_additional."','draft','".$method_dept."','".$origin."','".$move_id."','".$val->lokasi_dari."','".$val->lokasi_tujuan."'), ";
                                    
                                    //pengiriman barang
                                    $out_row = 1;
                                    foreach($items_rm as $outs){
                                        $sql_out_items_batch .= "('".$kode_out."','".addslashes($outs->kode_produk)."','".addslashes($outs->nama_produk)."','".$outs->qty."','".addslashes($outs->uom)."','draft','".$out_row."','".addslashes($outs->origin_prod)."'), ";

                                        //simpan ke stock move produk 
                                        $sql_stock_move_produk_batch .= "('".$move_id."','".addslashes($outs->kode_produk)."','".addslashes($outs->nama_produk)."','".$outs->qty."','".addslashes($outs->uom)."','draft','".$out_row."','".addslashes($outs->origin_prod)."'), ";
                                        $out_row = $out_row + 1; 
                                    }
                                
                                    //source move 
                                    $source_move = $move_id;

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
                                    $sql_log_history_out .= "('".$date_log."','".$mms_kode."','".$kode_out."','create','".$note_log."','".$nama_user."'), ";

                                    //upddate pengiriman reff picking
                                    $reff_picking_out = $kode_out."|".$deptid;
                                    $case2  .= "when kode = '".$kode_out."' then '".$reff_picking_out."'";
                                    $where2 .= "'".$kode_out."',";
                        

                                }else if($method_action =='IN'){//penerimaan barang

                                    if($i=="1"){
                                    $arr_kode[$val->method]= $this->_module->get_kode_penerimaan($method_dept);
                                    }else{
                                    $arr_kode[$val->method]= $arr_kode[$val->method] + 1;
                                    }
                                    $dgt     = substr("00000" . $arr_kode[$val->method],-5);            
                                    $kode_in = $method_dept."/".$method_action."/".date("y").  date("m").$dgt;
                                
                                    //penerimaan barang 
                                    $reff_picking_in = $kode_out."|".$kode_in;
                                    $sql_in_batch   .= "('".$kode_in."','".$tgl."','".$tgl."','".$tgl."','".$reff_notes_additional."','draft','".$method_dept."','".$origin."','".$move_id."','".$reff_picking_in."','".$val->lokasi_dari."','".$val->lokasi_tujuan."'), "; 

                                    //penerimaan barang
                                    $in_row = 1;
                                    foreach($items_rm as $ins){
                                        $sql_in_items_batch .= "('".$kode_in."','".addslashes($ins->kode_produk)."','".addslashes($ins->nama_produk)."','".$ins->qty."','".addslashes($ins->uom)."','draft','".$in_row."','".addslashes($ins->origin_prod)."'), ";

                                        //simpan ke stock move produk 
                                        $sql_stock_move_produk_batch .= "('".$move_id."','".addslashes($ins->kode_produk)."','".addslashes($ins->nama_produk)."','".$ins->qty."','".addslashes($ins->uom)."','draft','".$in_row."','".addslashes($ins->origin_prod)."'), ";
                                        $in_row = $in_row + 1; 
                                    }

                                    //update pengiriman
                                    $reff_picking_out = $kode_out."|".$kode_in;
                                    $case  .= "when kode = '".$kode_out."' then '".$reff_picking_out."'";
                                    $where .= "'".$kode_out."',";
                                    $kode_out    = "";

                                    //source move 
                                    $source_move = $move_id;

                                    //get mms kode berdasarkan dept_id
                                    $mms = $this->_module->get_kode_sub_menu_deptid('penerimaanbarang',$method_dept)->row_array();
                                    if(!empty($mms['kode'])){
                                        $mms_kode = $mms['kode'];
                                    }else{
                                        $mms_kode = '';
                                    }

                                    //create log history penerimaan_barang
                                    $note_log = $kode_in.' | '.$origin;
                                    $date_log = date('Y-m-d H:i:s');
                                    $sql_log_history_in .= "('".$date_log."','".$mms_kode."','".$kode_in."','create','".addslashes($note_log)."','".$nama_user."'), ";

                                }else if($method_action =='CON'){
                                    // update yg move id nya kosong
                                    $case3  .= "when kode = '".$kode."' then '".$move_id."'";
                                    $smp_row = 1;
                                    foreach($items_rm as $smp){
                                        //simpan ke stock move produk 
                                        $sql_stock_move_produk_batch .= "('".$move_id."','".addslashes($smp->kode_produk)."','".addslashes($smp->nama_produk)."','".$smp->qty."','".addslashes($smp->uom)."','draft','".$smp_row."','".addslashes($smp->origin_prod)."'), ";
                                        $smp_row = $smp_row + 1; 
                                    }

                                }

                                //move id + 1
                                $last_move = $last_move + 1;
                                $move_id   = "SM".$last_move;

                            } // end foreach route

                            if($empty_item == TRUE){
                                $callback = array('message' => 'Maaf, Request Additional belum bisa dilakukan ! ',  'status' => 'failed' );
                                //unlock table
                                $this->_module->unlock_tabel();
                            }else{

                                //action sql query
                                if(!empty($sql_stock_move_batch)){
                                    $sql_stock_move_batch = rtrim($sql_stock_move_batch, ', ');
                                    $this->_module->create_stock_move_batch($sql_stock_move_batch);

                                    if(!empty($sql_stock_move_produk_batch)){
                                        $sql_stock_move_produk_batch = rtrim($sql_stock_move_produk_batch, ', ');
                                        $this->_module->create_stock_move_produk_batch($sql_stock_move_produk_batch);
                                    }
                                }

                                if(!empty($sql_out_batch)){
                                    $sql_out_batch = rtrim($sql_out_batch, ', ');
                                    $this->_module->simpan_pengiriman_batch($sql_out_batch);

                                    $sql_out_items_batch = rtrim($sql_out_items_batch, ', ');
                                    $this->_module->simpan_pengiriman_items_batch($sql_out_items_batch);
                                    
                                    $sql_log_history_out = rtrim($sql_log_history_out, ', ');
                                    $this->_module->simpan_log_history_batch($sql_log_history_out);

                                    $where2 = rtrim($where2, ',');
                                    $sql_update_reff_picking_out_batch  = "UPDATE pengiriman_barang SET reff_picking =(case ".$case2." end) WHERE  kode in (".$where2.") ";
                                    $this->_module->update_reff_batch($sql_update_reff_picking_out_batch);
                                    $sql_update_reff_picking_out_batch = "";

                                }

                                if(!empty($sql_in_batch)){
                                    $sql_in_batch = rtrim($sql_in_batch, ', ');
                                    $this->_module->simpan_penerimaan_batch($sql_in_batch);

                                    $sql_in_items_batch = rtrim($sql_in_items_batch, ', ');
                                    $this->_module->simpan_penerimaan_items_batch($sql_in_items_batch);
                                    
                                    $where = rtrim($where, ',');
                                    $sql_update_reff_out_batch  = "UPDATE pengiriman_barang SET reff_picking =(case ".$case." end) WHERE  kode in (".$where.") ";
                                    $this->_module->update_reff_batch($sql_update_reff_out_batch);

                                    $sql_log_history_in = rtrim($sql_log_history_in, ', ');
                                    $this->_module->simpan_log_history_batch($sql_log_history_in);
                                }

                                if(!empty($case3)){
                                    $sql_update_rm_move_id  = "UPDATE mrp_production_rm_target SET move_id =(case ".$case3." end) WHERE  kode in ('".$kode."') AND move_id = '' AND additional ='t'";
                                    $this->_module->update_reff_batch($sql_update_rm_move_id);
                                }

                                $tp  = $this->m_mo->cek_type_mo_by_dept_id($deptid)->row_array();
                                if($tp['type_mo'] == 'colouring'){
                                    //update status mrp_production
                                    $sql_update_mrp_production  = "UPDATE mrp_production SET status ='draft' WHERE  kode = '$kode' "; 
                                    $this->_module->update_perbatch($sql_update_mrp_production);
                                }
                                    

                                //unlock table
                                $this->_module->unlock_tabel();
                                
                                $jenis_log   = "edit";
                                $note_log    = "Request Additional Obat -> ".$orgn;
                                $this->_module->gen_history_deptid($sub_menu, $kode, $jenis_log, $note_log, $username, $deptid);
                                
                                $callback = array('status'=>'success', 'message' => 'Request Additioanl Berhasil !',  'icon' =>'fa fa-check', 'type' => 'success');
                            }

                        }
                    }else{
                        $callback = array('status' => 'failed', 'message'=>'Maaf, Request Additional belum tersedia !', 'icon' => 'fa fa-warning', 'type'=>'danger');
                        //unlock table
                        $this->_module->unlock_tabel();
                    }

                }

            }

        }
        echo json_encode($callback);
    }

    public function print_mo_modal()
    {
    	$kode   = $this->input->post('kode');
    	$deptid = $this->input->post('deptid');
        $nm_dept= $this->_module->get_nama_dept_by_kode($deptid)->row_array();
        $data['departemen'] = $nm_dept['nama'];
        $data['sm_obat'] = $this->m_mo->get_list_move_id_rm_obat_by_kode($kode,'')->result();
        $data['kode']    = $kode;
        return $this->load->view('modal/v_mo_print_modal', $data);
    }

    function print_mo()
    {
       
        $kode     = $this->input->get('kode');
        $move_id  = $this->input->get('move_id');

        // get list rm obat
        $list = $this->m_mo->get_list_rm_target_obat_by_move($kode,$move_id,'');
        
        if(!empty($list)){
            
            $head = $this->m_mo->get_data_by_code($kode);

            // get move rm target kain
            $get      = $this->m_mo->get_move_id_rm_target_by_kode($kode)->row_array();
            $info_qty = $this->m_mo->get_sum_smi_rm_target_by_kode($get['move_id'])->row_array();

            // get_no_greige out by origin 
            $get_go = $this->m_mo->get_no_greige_out_by_origin($head->origin);
            
            $ex     = explode('"',$head->nama_produk);
            $loop   = 0;
            $nama_produk = '';
            foreach($ex as $val){
                if($loop == 0){
                    $nama_produk = $val;
                }
                $loop++;
            }

            $this->load->library('Pdf');//load library pdf

            //$pdf = new PDF_Code128('L','mm',array(139,215));
            $pdf = new PDF_Code128('P','mm','A4');
      
            $pdf->SetMargins(0,0,0);
            $pdf->SetAutoPageBreak(False);
            $pdf->AddPage();

            $add_num = 0;
            $caption = '';
            $sm_obat = $this->m_mo->get_list_move_id_rm_obat_by_kode($kode,'')->result();
            foreach($sm_obat as $rmo){
                if($rmo->additional == 't'){
                    $add_num++;
                }
                if($rmo->additional == 'f' AND $rmo->move_id == $move_id){
                    $caption = '';
                    break;
                }else if($rmo->additional == 't' AND $rmo->move_id == $move_id){ 
                    $caption = 'ADD'.$add_num;
                    break;
                }
            }

            $pdf->setTitle('Print '.$caption);

            $pdf->Cell(0,5,'',0,1,'C');

            // judul
            $pdf->SetFont('Arial','BU',15,'C');
            $pdf->Cell(0,10,$head->kode.''.$caption,0,1,'C');

            // tgl cetak
            $pdf->SetFont('Arial','',7,'C');
            $pdf->setXY(150,10);
            $tgl_now = tgl_indo(date('d-m-Y H:i:s'));
            $pdf->Multicell(50,4, 'Tgl.Cetak : '.$tgl_now, 0,'C');
      
            // Info Warna
            $pdf->SetFont('Arial','B',15,'C');
            $pdf->setXY(15,15);
            $pdf->Multicell(100,4,'',0,'L');
      
            $pdf->SetFont('Arial','B',10,'C');
            
            $pdf->setXY(10,25);
            $pdf->Multicell(22,5,'No. GO ',0,'L');
            $pdf->setXY(10,30);
            $pdf->Multicell(27,5,'Produk/Corak ',0,'L');
            $pdf->setXY(10,35);
            $pdf->Multicell(27,5,'Warna ',0,'L');
            $pdf->setXY(10,40);
            $pdf->Multicell(27,5,'Qty ',0,'L');
            $pdf->setXY(10,45);
            $pdf->Multicell(27,5,'Notes PPIC ',0,'L');
           
            $pdf->setXY(35, 25);
            $pdf->Multicell(5, 5, ':', 0, 'L');
            $pdf->setXY(35, 30);
            $pdf->Multicell(5, 5, ':', 0, 'L');
            $pdf->setXY(35, 35);
            $pdf->Multicell(5, 5, ':', 0, 'L');
            $pdf->setXY(35, 40);
            $pdf->Multicell(5, 5, ':', 0, 'L');
            $pdf->setXY(35, 45);
            $pdf->Multicell(5, 5, ':', 0, 'L');

        
            $pdf->SetFont('Arial','',9,'C');
            $pdf->setXY(37,25);
            $pdf->Multicell(40,5,$get_go,0,'L');
            $pdf->setXY(37,30);
            $pdf->Multicell(70,5,$nama_produk.'"',0,'L');
            $pdf->setXY(37,35);
            $pdf->Multicell(80,5,$head->nama_warna,0,'L');

            $pdf->SetFont('Arial','B',25,'C');
            $pdf->setXY(117,34);
            $pdf->Multicell(15,5,$head->nama_varian,0,'L');

            $pdf->SetFont('Arial','',9,'C');
            $pdf->setXY(37,40);
            $pdf->Multicell(70,5,$info_qty['tot_gl'].'Glg  '.$info_qty['tot_qty'].''.$info_qty['uom'].'  '.$info_qty['tot_qty2'].''.$info_qty['uom2'].' ',0,'L');

            $pdf->setXY(37,45);
            $pdf->Multicell(80,5,$head->reff_note,0,'L');
      
            $yPos_kiri=$pdf->GetY();
            
            // info Mesin
            $mc = $this->m_mo->get_nama_mesin_by_kode($head->mc_id)->row_array();
            $nama_mesin = $mc['nama_mesin'];
            $pdf->SetFont('Arial','B',10,'C');
            $pdf->setXY(130,25);
            $pdf->Multicell(25,5,'Mesin ',0,'L');
            $pdf->setXY(152, 25);
            $pdf->Multicell(5, 5, ': ', 0, 'L');
            $pdf->SetFont('Arial','',10,'C');
            $pdf->setXY(154,25);
            $pdf->Multicell(40,5,$nama_mesin,0,'L');
      
            $pdf->SetFont('Arial','B',10,'C');
            $pdf->setXY(130,30);
            $pdf->Multicell(25,5,'Finishing ',0,'L');
            $pdf->setXY(152, 30);
            $pdf->Multicell(5, 5, ': ', 0, 'L');
            $pdf->SetFont('Arial','',10,'C');
            $pdf->setXY(154,30);
            $pdf->Multicell(40,5,$head->nama_handling,0,'L');
      
            // info Program
            $pdf->SetFont('Arial','B',10,'C');
            $pdf->setXY(130,35);
            $pdf->Multicell(25,5,'Program ',0,'L');
            $pdf->setXY(152, 35);
            $pdf->Multicell(5, 5, ': ', 0, 'L');
            $pdf->SetFont('Arial','',10,'C');
            $pdf->setXY(154,35);
            $pdf->Multicell(40,5,$head->program,0,'L');

           
             // info Berat
            $pdf->SetFont('Arial','B',10,'C');
            $pdf->setXY(130,40);
            $pdf->Multicell(25,5,'Berat (Kg) ',0,'L');
            $pdf->setXY(152, 40);
            $pdf->Multicell(5, 5, ': ', 0, 'L');
            $pdf->SetFont('Arial','',10,'C');
            $pdf->setXY(154,40);
            $pdf->Multicell(40,5,number_format($head->berat,2).'',0,'L');


            // info Air
            $pdf->SetFont('Arial','B',10,'C');
            $pdf->setXY(130,45);
            $pdf->Multicell(25,5,'Air (Ltr) ',0,'L');
            $pdf->setXY(152, 45);
            $pdf->Multicell(5, 5, ': ', 0, 'L');
            $pdf->SetFont('Arial','',10,'C');
            $pdf->setXY(154,45);
            $pdf->Multicell(40,5,number_format($head->air,2),0,'L');
      
      
            $yPos_kanan=$pdf->GetY();
      
            if($yPos_kiri >= $yPos_kanan){
              $yPos = $yPos_kiri;
            }else{
              $yPos = $yPos_kanan;
            }
            $xPos=$pdf->GetX();
            $pdf->SetFont('Arial','B',10,'C');
      
            $pdf->setXY($xPos + 10, $yPos + 4);
            $pdf->Cell(10, 7, 'No. ', 1, 0, 'C');
            $pdf->Cell(20, 7, 'Kode ', 1, 0, 'C');
            $pdf->Cell(75, 7, 'Nama Produk', 1, 0, 'C');
            $pdf->Cell(20, 7, '%', 1, 0, 'C');
            $pdf->Cell(25, 7, 'Qty', 1, 0, 'C');
            $pdf->Cell(40, 7, 'Reff Notes', 1, 0, 'C');
      
            $y      = $pdf->GetY() + 7;
            $x      = 10;
            $no_dye = 1;
            $type   = '12';
            $items_dye   = $this->m_mo->get_list_rm_target_obat_by_move($kode,$move_id,$type);
      
            foreach($items_dye as $dye){
      
              $pdf->setXY($x, $y);
              if($no_dye == 1){
                $pdf->SetFont('Arial','B',10,'C');
                $pdf->Multicell(190, 7, 'Dyeing Stuff (%)', 1,'C');
                $y = $y + 7;
                $x = 10;
              }
              $pdf->setXY($x, $y);
      
              $pdf->SetFont('Arial','',10,'C');
              $pdf->Multicell(10, 7, $no_dye.'.', 1,'C');
              $pdf->setXY($x+10, $y);
              $pdf->Multicell(20, 7, $dye->kode_produk, 1,'C');
              $pdf->setXY($x+30, $y);
              $pdf->Multicell(75, 7, $dye->nama_produk, 1,'L');
              $pdf->setXY($x+105, $y);
              $pdf->Multicell(20, 7, $dye->persen, 1,'R');
              $pdf->setXY($x+125, $y);
              $pdf->Multicell(25, 7, $dye->qty.' '.$dye->uom, 1,'R');
              $pdf->setXY($x+150, $y);
              $pdf->Multicell(40, 7, $dye->reff_note, 1,'L');
      
              $no_dye++;
              $y = $y + 7;
      
            }
      
            $no_aux = 1;
            $type   = '11';
            $items_aux   = $this->m_mo->get_list_rm_target_obat_by_move($kode,$move_id,$type);
      
            foreach($items_aux as $aux){
      
              $pdf->setXY($x, $y);
              if($no_aux == 1){
                $pdf->SetFont('Arial','B',10,'C');
                $pdf->Multicell(190, 7, 'Auxiliary (g/L)', 1,'C');
                $y = $y + 7;
                $x = 10;
              }
              $pdf->setXY($x, $y);
      
              $pdf->SetFont('Arial','',10,'C');
              $pdf->Multicell(10, 7, $no_aux.'.', 1,'C');
              $pdf->setXY($x+10, $y);
              $pdf->Multicell(20, 7, $aux->kode_produk, 1,'C');
              $pdf->setXY($x+30, $y);
              $pdf->Multicell(75, 7, $aux->nama_produk, 1,'L');
              $pdf->setXY($x+105, $y);
              $pdf->Multicell(20, 7, $aux->persen, 1,'R');
              $pdf->setXY($x+125, $y);
              $pdf->Multicell(25, 7, $aux->qty.' '.$aux->uom, 1,'R');
              $pdf->setXY($x+150, $y);
              $pdf->Multicell(40, 7, $aux->reff_note, 1,'L');
      
              $no_aux++;
              $y = $y + 7;
      
            }
      
            // ttd box
            $xPos=$pdf->GetX();
            $yPos=$pdf->GetY();
            $pdf->SetFont('Arial','B',10,'C');
      
            $pdf->setXY($x+10, $y+8);
            $pdf->Multicell(20, 5, 'Penerima,', 0,'C');

            $pdf->setXY($x+8, $y+23);
            $pdf->Multicell(30, 5, '(..........................)', 0,'L');

            $pdf->setXY($x+150, $y+8);
            $pdf->Multicell(40, 5, 'Hormat Kami,', 0,'L');

            $pdf->setXY($x+148, $y+23);
            $pdf->Multicell(30, 5, '(..........................)', 0,'C');
          
            $pdf->Output();

    
        }else{
            print_r('Maaf, Data Tidak ditemukan !');
        }
    }

    function export_txt()
    {
        $kode     = $this->input->get('kode');
        $move_id  = $this->input->get('move_id');

        // get list rm obat
        $list = $this->m_mo->get_list_rm_target_obat_by_move($kode,$move_id,'');
        
        if(!empty($list)){
                
            $head = $this->m_mo->get_data_by_code($kode);
            $mc         = $this->m_mo->get_nama_mesin_by_kode($head->mc_id)->row_array();
            $nama_mesin = $mc['nama_mesin'];

            $add_num = 0;
            $caption = '';
            $sm_obat = $this->m_mo->get_list_move_id_rm_obat_by_kode($kode,'')->result();
            foreach($sm_obat as $rmo){
                if($rmo->additional == 't'){
                    $add_num++;
                }
                if($rmo->additional == 'f' AND $rmo->move_id == $move_id){
                    $caption = '';
                    break;
                }else if($rmo->additional == 't' AND $rmo->move_id == $move_id){ 
                    $caption = 'ADD'.$add_num;
                    break;
                }
            }

            $type   = '12';
            $items_dye   = $this->m_mo->get_list_rm_target_obat_by_move($kode,$move_id,$type);
            $isi         = '';
            foreach($items_dye as $dye){
                $isi .= '01';// step number dye 01
                $isi .= str_pad($dye->kode_produk,8); // materila code (space 10)
                $isi .= str_pad($dye->qty,10," ",STR_PAD_LEFT);// target (space 8 ) LEFT
                $isi .= substr($nama_mesin,0,6);// machine (space 6)
                $isi .= 0; // type number
                $isi .= "\r\n";
                
            }

            $type   = '11';
            $items_aux   = $this->m_mo->get_list_rm_target_obat_by_move($kode,$move_id,$type);
            foreach($items_aux as $aux){
                $isi .= '02';// step numer aux 02
                $isi .= str_pad($aux->kode_produk,8);
                $isi .= str_pad($aux->qty,10," ",STR_PAD_LEFT);
                $isi .= substr($nama_mesin,0,6);
                $isi .= 0;
                $isi .= "\r\n";
            }
            
            $this->load->helper('download');  

            $dataFile       = $head->kode.''.$caption.'.txt';
            force_download($dataFile,$isi);

        }else{
            print_r('Maaf, Data Tidak ditemukan !');
        }
    }

    public function tambah_rm()// blm kepake
    {
        $data['kode']  = $this->input->post('kode');
        return $this->load->view('modal/v_mo_rm_modal',$data);
    }

    public function produksi_rm_batch()
    {
        $kode             = $this->input->post('kode');
        //$move_id          = $this->input->post('move_id');
        //$move_id_fg       = $this->input->post('move_id_fg');
        $deptid           = $this->input->post('deptid');
        $lot_prefix_waste = $this->input->post('lot_prefix_waste');
        $kode_produk      = $this->input->post('kode_produk');

        if($deptid == 'TRI' OR $deptid == 'JAC'){
            //cek MC by dept_id
            $list   = $this->m_mo->get_data_by_code($kode);
            if(empty($list->mc_id)){
                $lot_prefix = '';
            }else{// setting lot prefix by defualt KP/my/MC/DEPT/
                // get no mesin by mc_id 
                $no_mesin = $this->m_mo->no_mesin_by_mc_id($list->mc_id);
                $tgl_bln   = date('m').''.date('y');// ex 0122
                if($deptid == 'TRI'){
                    $dept_prefix = 'TR';
                }else{
                    $dept_prefix = $deptid;
                }
                if($list->type_production == 'Proofing'){
                    $awal = 'PF';
                }else{
                    $awal = 'KP';
                }
                $lot_prefix  = $awal.'/'.$tgl_bln.'/'.$no_mesin.'/'.$dept_prefix.'/';// lot prefix by default system
            }
        }else{
            $lot_prefix  = $this->input->post('lot_prefix');       
        };
        
        $get_uom          = $this->_module->get_uom_by_kode_produk($kode_produk)->row_array();//get uom 1 dan uom 2 by kode_produk
        $data['deptid']   = $deptid;
        $data['uom_1']    = $get_uom['uom'];
        $data['uom_2']    = $get_uom['uom_2'];
        $data['kode']     = $kode;
        $data['kode_produk']= $kode_produk;
        $data['product']    = $this->input->post('nama_produk');
        $data['sisa_qty']   = $this->input->post('sisa_qty');
        $data['uom_qty_sisa']= $this->input->post('uom_qty_sisa');
        $data['kode']       = $this->input->post('kode');
        $data['qty_prod']   = $this->input->post('qty');
        $data['origin_mo']  = $this->input->post('origin');
        $qty1_std           = $this->input->post('qty1_std');
        if($qty1_std > 0){
            $qty1_std = $qty1_std;
        }else{
            $qty1_std = '';
        }
        $data['qty1_std']   = $qty1_std;
        $qty2_std           = $this->input->post('qty2_std');
        if($qty2_std > 0){
            $qty2_std = $qty2_std;
        }else{
            $qty2_std = '';
        }
        $data['qty2_std']   = $qty2_std;
        $data['list_grade'] = $this->_module->get_list_grade();
        $data['lot_prefix'] = $lot_prefix;
        $data['konsumsi']   = $this->m_mo->get_konsumsi_bahan($kode,'ready');
        $sl                 = $this->_module->get_nama_dept_by_kode($deptid)->row_array();// get ,copy_bahanbaku true/false
        $data['copy_bahan_baku']  = $sl['copy_bahan_baku'];
        $data['lbr_produk'] = $this->m_mo->get_lebar_produk_by_kode($kode);
        $data['uom']        = $this->_module->get_list_uom();
        $data['show_lebar'] = $this->_module->cek_show_lebar_by_dept_id($deptid)->row_array();
        
        $username = $this->session->userdata('username');
        $cek_dept = $this->_module->cek_departemen_by_user($username)->row_array();
        $data['cek_dept']    = $cek_dept['dept'];
        $level_akses         = $this->_module->get_level_akses_by_user($username)->row_array();
        $data['level']       = $level_akses['level'];
        $data['type_mo']  = $this->m_mo->cek_type_mo_by_dept_id($deptid)->row_array();
        if(!empty($lot_prefix)){
            $count              = $this->m_mo->get_counter_by_lot_prefix(addslashes($lot_prefix),$deptid);
            //$data['row_lot']  = $count['jml_lot'] + 1;
            $data['row_lot']    = $count;
            $get_length         = $this->m_mo->cek_length_counter_lot_by_dept_id($deptid);
            $data['dgt_nol_jv'] = $get_length['dgt_nol_jv'];
            $data['length']     = -$get_length['length'];
        }else{
            $data['row_lot']    = "";
            $data['dgt_nol_jv'] = "";
            $data['length']     = "";
        }
        $data['lot_prefix_waste'] = $lot_prefix_waste;
        if(!empty($lot_prefix_waste)){
            $lw                   = $this->m_mo->get_location_waste_by_deptid($deptid)->row_array();
            $count_waste          = $this->m_mo->get_counter_by_lot_prefix_waste(addslashes($lot_prefix_waste),$lw['waste_location'])->row_array();
            $data['row_lot_waste']= $count_waste['jml_lot'] + 1;
        }else{
            $data['row_lot_waste']    = "";
        }

        return $this->load->view('modal/v_mo_produksi_batch_modal',$data);
    }

    public function consume_mo()
    {

        if(empty($this->session->userdata('username'))){
            return $this->load->view('v_login');
        }else{
            // cek departemen by user
            $username = $this->session->userdata('username');
            $cek_dept = $this->_module->cek_departemen_by_user($username)->row_array();
            $data['cek_dept'] = $cek_dept['dept'];

            $level_akses         = $this->_module->get_level_akses_by_user($username)->row_array();
            $data['level']       = $level_akses['level'];

            $kode             = $this->input->post('kode');
            $deptid           = $this->input->post('deptid');
            $kode_produk      = $this->input->post('kode_produk');

            $data['deptid']      = $deptid;
            $data['kode']        = $kode;
            $data['sisa_qty']    = $this->input->post('sisa_qty');
            $data['uom_qty_sisa']= $this->input->post('uom_qty_sisa');
            $data['qty_prod']    = $this->input->post('qty');
            $data['kode_produk'] = $kode_produk;
            $data['origin_mo']   = $this->input->post('origin');
            
            $data['konsumsi']   = $this->m_mo->get_konsumsi_bahan($kode,'ready');
            $data['list_fg']    = $this->m_mo->get_list_mrp_production_fg_hasil_cons_no_by_kode($kode);

            $data['rm_done']    = $this->m_mo->get_sum_qty_rm_done($kode,'done')->row();
            $data['rm_ready']   = $this->m_mo->get_sum_qty_rm_ready($kode,'ready')->row();
            $data['fg_no']      = $this->m_mo->get_sum_qty_fg($kode,'no')->row();
            $data['fg_yes']     = $this->m_mo->get_sum_qty_fg($kode,'yes')->row();

            return $this->load->view('modal/v_mo_consume_modal',$data);
        }

    }

    public function produksi_waste()
    {

        $kode             = $this->input->post('kode');
        $deptid           = $this->input->post('deptid');
        // $lot_prefix_waste = $this->input->post('lot_prefix_waste');
        $kode_produk      = $this->input->post('kode_produk');

        $get_uom          = $this->_module->get_uom_by_kode_produk($kode_produk)->row_array();//get uom 1 dan uom 2 by kode_produk
        $data['deptid']   = $deptid;
        $data['uom_1']    = $get_uom['uom'];
        $data['uom_2']    = $get_uom['uom_2'];
        $data['kode']     = $kode;
        $data['kode_produk']= $kode_produk;
        $data['product']    = $this->input->post('nama_produk');
        $data['sisa_qty']   = $this->input->post('sisa_qty');
        $data['uom_qty_sisa']= $this->input->post('uom_qty_sisa');
        $data['kode']       = $this->input->post('kode');
        $data['qty_prod']   = $this->input->post('qty');
        $data['origin_mo']  = $this->input->post('origin');
        // $qty1_std           = $this->input->post('qty1_std');

        $data['list_grade'] = $this->_module->get_list_grade();
        $data['konsumsi']   = $this->m_mo->get_konsumsi_bahan($kode,'ready');
        $sl                 = $this->_module->get_nama_dept_by_kode($deptid)->row_array();// get ,copy_bahanbaku true/false
        $data['copy_bahan_baku']  = $sl['copy_bahan_baku'];
        $data['lbr_produk'] = $this->m_mo->get_lebar_produk_by_kode($kode);
        $data['uom']        = $this->_module->get_list_uom();
        $data['show_lebar'] = $this->_module->cek_show_lebar_by_dept_id($deptid)->row_array();


        if($deptid == 'TRI' OR $deptid == 'JAC'){
            //cek MC by dept_id
            $list   = $this->m_mo->get_data_by_code($kode);
            if(empty($list->mc_id)){
                $lot_prefix_waste = '';
            }else{// setting lot prefix by defualt KP/my/MC/DEPT/
                // get no mesin by mc_id 
                $no_mesin = $this->m_mo->no_mesin_by_mc_id($list->mc_id);
                $tgl_bln   = date('m').''.date('y');// ex 0122
                if($deptid == 'TRI'){
                    $dept_prefix = 'TR';
                }else{
                    $dept_prefix = $deptid;
                }
                if($list->type_production == 'Proofing'){
                    $awal = 'PF';
                }else{
                    $awal = 'KP';
                }
                $lot_prefix_waste  = $awal.'/'.$tgl_bln.'/'.$no_mesin.'/'.$dept_prefix.'/';// lot prefix by default system
            }
        }else{
            $lot_prefix_waste  = $this->input->post('lot_prefix_waste');       
        };

        $data['lot_prefix_waste'] = $lot_prefix_waste;

        return $this->load->view('modal/v_mo_produksi_waste_modal',$data);
    }

    public function get_list_produk_waste()
    {
        $kode_mo  = $this->input->post('kode');
        $params   = $this->input->post('prod');
        // $kode_produk  = $this->input->post('kode_produk');
        // $nama_produk  = $this->input->post('nama_produk');

        //$move_rm  = $this->m_mo->get_move_id_rm_target_by_kode($kode_mo)->row_array();
        $list     = $this->m_mo->get_list_waste_bahan_baku_by_move_id($kode_mo,$params)->result();
        //$dataRecord[] = array('kode_produk' => $kode_produk,    'nama_produk' => $nama_produk);
        $dataRecord[] = '';
        foreach ($list as $row) {
            $dataRecord[] = array( 'kode_produk' => $row->kode_produk, 
                                   'nama_produk' => $row->nama_produk);

        }

        echo json_encode($dataRecord);
    }

    public function get_last_lot_prefix_waste_by_lot()
    {

        $lot_prefix  = addslashes($this->input->post('lot_prefix'));
        $deptid      = $this->input->post('deptid');
        if(!empty($lot_prefix)){
            $lw                   = $this->m_mo->get_location_waste_by_deptid($deptid)->row_array();
            $count_waste          = $this->m_mo->get_counter_by_lot_prefix_waste(addslashes($lot_prefix),$lw['waste_location'])->row_array();
            $data                 = $count_waste['jml_lot'] + 1;
        }else{
            $data                 = "";
        }

        echo json_encode(array( 'counter' =>  $data ) );

    }

    public function get_list_produk_waste_fg()
    {
        $kode_mo  = $this->input->post('kode');
        $params   = $this->input->post('prod');

        $list     = $this->m_mo->get_list_waste_barang_jadi($kode_mo,$params)->result();
        $dataRecord[] = '';
        foreach ($list as $row) {
            $dataRecord[] = array( 'kode_produk' => $row->kode_produk, 
                                   'nama_produk' => $row->nama_produk);
        }

        echo json_encode($dataRecord);
    }


    public function get_list_lot_waste_by_produk()
    {
        $kode_produk = addslashes($this->input->post('kode_produk'));
        $kode_mo     = $this->input->post('kode');
        $params      = $this->input->post('prod');

        //$move_rm  = $this->m_mo->get_move_id_rm_target_by_kode($kode_mo)->row_array();
        $list     = $this->m_mo->get_list_lot_waste_by_kode($kode_mo,$kode_produk,$params)->result();
        $dataRecord[] = '';
        foreach ($list as $row) {
            $dataRecord[] = array( 'lot' => $row->lot);
        }
        
        echo json_encode($dataRecord);
    }

    public function get_nama_produk_waste()
    {
        $kode_produk = addslashes($this->input->post('kode_produk'));
        $get = $this->m_mo->get_nama_produk_waste_by_kode($kode_produk)->row_array();

        echo json_encode(array('nama_produk'=>$get['nama_produk'], 'uom_1' => $get['uom'], 'uom_2'=>$get['uom_2']));
    }

    public function produksi_rm()
    {
        $kode             = $this->input->post('kode');
        //$move_id          = $this->input->post('move_id');
        //$move_id_fg       = $this->input->post('move_id_fg');
        $deptid           = $this->input->post('deptid');
        $kode_produk      = $this->input->post('kode_produk');

        if($deptid == 'TRI' OR $deptid == 'JAC'){
            //cek MC by dept_id
            $list   = $this->m_mo->get_data_by_code($kode);
            if(empty($list->mc_id)){
                $lot_prefix = '';
            }else{// setting lot prefix by defualt KP/my/MC/DEPT/
                // get no mesin by mc_id 
                $no_mesin = $this->m_mo->no_mesin_by_mc_id($list->mc_id);
                $tgl_bln   = date('m').''.date('y');// ex 0122
                if($deptid == 'TRI'){
                    $dept_prefix = 'TR';
                }else{
                    $dept_prefix = $deptid;
                }
                if($list->type_production == 'Proofing'){
                    $awal = 'PF';
                }else{
                    $awal = 'KP';
                }
                $lot_prefix  = $awal.'/'.$tgl_bln.'/'.$no_mesin.'/'.$dept_prefix.'/';// lot prefix by default system
            }
        }else{
            $lot_prefix  = $this->input->post('lot_prefix');       
        }

        $get_uom          = $this->_module->get_uom_by_kode_produk($kode_produk)->row_array();//get uom 1 dan uom 2 by kode_produk
        $data['deptid']   = $deptid;
        $data['uom_1']    = $get_uom['uom'];
        $data['uom_2']    = $get_uom['uom_2'];
        $data['kode']     = $kode;
        $data['kode_produk']= $this->input->post('kode_produk');
        $data['product']    = $this->input->post('nama_produk');
        $data['sisa_qty']   = $this->input->post('sisa_qty');
        $data['uom_qty_sisa']= $this->input->post('uom_qty_sisa');
        $data['kode']       = $this->input->post('kode');
        $data['qty_prod']   = $this->input->post('qty');
        $data['origin_mo']  = $this->input->post('origin');
        $qty1_std           = $this->input->post('qty1_std');
        if($qty1_std > 0){
            $qty1_std = $qty1_std;
        }else{
            $qty1_std = '';
        }
        $data['qty1_std']   = $qty1_std;
        $qty2_std           = $this->input->post('qty2_std');
        if($qty2_std > 0){
            $qty2_std = $qty2_std;
        }else{
            $qty2_std = '';
        }
        $data['qty2_std']   = $qty2_std;
        $data['lot_prefix'] = $lot_prefix;
        $data['konsumsi']   = $this->m_mo->get_konsumsi_bahan($kode,'ready');
        $data['list_grade'] = $this->_module->get_list_grade();
        $data['lbr_produk'] = $this->m_mo->get_lebar_produk_by_kode($kode);
        $data['uom']        = $this->_module->get_list_uom();
        $data['show_lebar'] = $this->_module->cek_show_lebar_by_dept_id($deptid)->row_array();

        if(!empty($lot_prefix)){
            $count              = $this->m_mo->get_counter_by_lot_prefix(addslashes($lot_prefix),$deptid);
            //$data['row_lot']    = $count['jml_lot'] + 1;
            $data['row_lot']    = $count;
        }else{
            $data['row_lot']    = "";
        
        }     

        return $this->load->view('modal/v_mo_produksi_modal',$data);
    }

    public function save_produksi_batch_modal()
    {
        try{
            if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
                // session habis
                // $callback = array('message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
                throw new \Exception('Waktu Anda Telah Habis ', 401);
            }else{

                $sub_menu = $this->uri->segment(2);
                $username = addslashes($this->session->userdata('username')); 
                $nama_user = $this->_module->get_nama_user($username)->row_array();

                $deptid   = $this->input->post('deptid');

                $array_fg    = json_decode($this->input->post('data_fg'),true); 
                $array_rm    = json_decode($this->input->post('data_rm'),true); 
                $array_waste = json_decode($this->input->post('data_waste'),true); 
                $kode        = $this->input->post('kode');
                $kode_produk = $this->input->post('kode_produk');
                $origin_mo   = $this->input->post('origin_mo');
                $tgl         = date('Y-m-d H:i:s');
                $status_brg  = 'done';
                $sql_mrp_production_fg_hasil = "";
                $sql_mrp_production_rm_hasil = "";
                $sql_stock_quant_batch       = "";
                $sql_stock_move_items_batch  = "";
                $case  = "";
                $where = "";
                $case2 = "";
                $where2= "";
                $case3 = "";
                $where3= "";
                $case4 = "";
                $where4= "";
                $case5 = "";
                $where5= "";
                $case6 = "";
                $where6= "";
                $case7 = "";
                $where7= "";
                $case8 = "";
                $where8= "";
                $case9 = "";
                $where9= "";
                $case10= "";
                $where10="";
                $where10x="";
                $lot_double = "";
                $lot_double_Waste = "";
                $case_qty2= "";
                $qty2_update = "";
                $where_move_items= "";
                $where5_move_id  = "";
                $qty2_new = "";
                $jml_lot_fg    = 0;
                $jml_lot_waste = 0;
                $case11     = "";
                $where11    = "";
                $consume    = "yes";              
                

                // start transaction
                // $this->_module->startTransaction();

                //lock table
                $this->_module->lock_tabel('mrp_production WRITE, mrp_production_rm_hasil WRITE, mrp_production_fg_hasil WRITE, mrp_production_rm_target WRITE, mrp_production_fg_target WRITE, stock_move WRITE, stock_move_items WRITE, stock_quant WRITE, stock_move_produk WRITE, departemen WRITE, pengiriman_barang WRITE, pengiriman_barang_items WRITE, penerimaan_barang WRITE, penerimaan_barang_items WRITE, sales_contract WRITE,mrp_production_rm_target as rm WRITE, mst_produk as mp WRITE, stock_move_items as smi WRITE, mrp_production as mrp WRITE, mrp_production_fg_hasil as fg WRITE, departemen as d WRITE');

                //cek status mrp_production = done
                $cek1  = $this->m_mo->cek_status_mrp_production($kode,'done')->row_array();
                //cek status mrp_production = cancel
                $cek2  = $this->m_mo->cek_status_mrp_production($kode,'cancel')->row_array();
                //cek status mrp_production = hold
                $cek3  = $this->m_mo->cek_status_mrp_production($kode,'hold')->row_array();
        
                $sl    = $this->_module->get_nama_dept_by_kode($deptid)->row_array();// get ,copy_bahanbaku true/false
                $copy_bahan_baku = $sl['copy_bahan_baku'] ?? '';

                $get_type_mo    = $this->m_mo->cek_type_mo_by_dept_id($deptid)->row_array();
                $type_mo = $get_type_mo['type_mo'] ?? '';

                if(!empty($cek1['status'])){
                    $callback = array('status' => 'failed', 'message'=>'Maaf, Data Tidak Bisa Disimpan, Status MO Sudah Done !', 'icon' => 'fa fa-warning', 'type'=>'danger');
                }else if(!empty($cek2['status'])){
                    $callback = array('status' => 'failed', 'message'=>'Maaf, Data Tidak Bisa Disimpan, Status MO Batal !', 'icon' => 'fa fa-warning', 'type'=>'danger');
                }else if(!empty($cek3['status'])){
                    $callback = array('status' => 'failed', 'message'=>'Maaf, Data Tidak Bisa Disimpan, Status MO di Hold !', 'icon' => 'fa fa-warning', 'type'=>'danger');
                }else if($copy_bahan_baku == 'true' AND $type_mo == 'colouring' AND  !empty($array_fg) AND empty($array_rm)){
                    $callback = array('status' => 'failed', 'message'=>'Maaf, Data Tidak Bisa Disimpan, Bahan Baku Kosong !', 'icon' => 'fa fa-warning', 'type'=>'danger');
                }else{

                    //get last quant id
                    $start = $this->_module->get_last_quant_id();
                    $get_ro   = $this->m_mo->get_row_order_fg_hasil($kode)->row_array();
                    $row_order= $get_ro['row']+1;
                    $status_ready = 'ready';
                    $status_done  = 'done';
                    $move_fg  = $this->m_mo->get_move_id_fg_target_by_kode($kode)->row_array();
                    $move_id_fg = $move_fg['move_id'];

                    // get sales_group / mkt by sales_contract 
                    $org_mo      = explode("|", $origin_mo);
                    $org_mo_loop = 0;
                    $sales_order = "";
                    foreach($org_mo as $org_mos){
                        if($org_mo_loop == 0){
                            $sales_order = trim($org_mos);
                        }
                        $org_mo_loop++;
                    }

                    $sales_group = $this->_module->get_sales_group_by_sales_order($sales_order);

                    //get row order stock_move_items produksi
                    $row_order_smi  = $this->_module->get_row_order_stock_move_items_by_kode($move_id_fg);
                    $list_lot_fg    = '';
                    $list_lot_waste = '';
                    $tmp_lot_fg     = '';
                    $list_lot_cons  = '';

                    if(!empty($array_fg) ){

                        //lokasi tujuan fg
                        $lokasi_fg = $this->_module->get_location_by_move_id($move_id_fg)->row_array();

                        //get move id tujuan
                        //$method= $deptid.'|OUT';
                        //$method= $deptid.'|OUT';
                        $sm_tj = $this->_module->get_stock_move_tujuan_mo($move_id_fg,$origin_mo,'done','cancel')->row_array();
                
                        //get row order stock_move_items tujuan
                        $row_order_smi_tujuan  = $this->_module->get_row_order_stock_move_items_by_kode($sm_tj['move_id']);


                        /*
                        //untuk mendapatkan origin_prod yang terdapat consume kedepannya atau consume lebih dari 1
                        $loop_sm = true;
                        $loop_count = 1;
                        $origin_prod_tj = "";
                        $next = false;
                        $con_next = false;
                        $con = false;
                        //$tes = '';
                        //$lp='';

                        //get list stock_move by origin
                        $list_sm = $this->_module->get_list_stock_move_origin($origin_mo)->result_array();
                        foreach ($list_sm as $row) {
                            
                            $mt = explode("|", $row['method']);
                            $ex_deptid = $mt[0];
                            $ex_mt     = $mt[1];

                            if($loop_sm == true){

                                if($ex_mt == 'CON' AND $con_next == true){

                                    //get  origin_prod by move id, kode_produk
                                    $get_origin_prod = $this->m_mo->get_origin_prod_mrp_production_by_kode($row['move_id'],addslashes($kode_produk))->row_array();
                                    $origin_prod_tj = $get_origin_prod['origin_prod'];
                                    $loop_sm =false;
                                    /*
                                    foreach ($list_rm_target as $row2) {
                                        # code...
                                        //get origin_prod by move_id, mo, kode_produk(dari MO)
                                        $this->m_mo->get_origin_prod_mrp_production_by_kode()
                                    }
                                    
                                }

                                if($ex_deptid == $deptid AND $ex_mt == 'CON'){
                                    $con_next = true;
                                }
                            }elseif($loop_sm == false){
                                break;//paksa keluar looping
                            }

                            //$loop_count = $loop_count + 1;
                        }
                            

                        if(!empty($origin_prod_tj)){
                            $origin_prod = $origin_prod_tj;
                        }else{
                            $origin_prod = '';
                        }
                        */

                        $cek_dl     = $this->m_mo->cek_validasi_double_lot_by_dept($deptid);

                        $type_mo    = $this->m_mo->cek_type_mo_by_dept_id($deptid)->row_array();

                        //simpan fg hasil
                        foreach ($array_fg as $row) {

                            $rowLot = preg_replace('/\s/', '', $row['lot']);

                            // cek lot yg sama di mrp fg hasil
                            $cek_lot_input = $this->m_mo->get_data_lot_mrp_fg_hasil_by_lot($kode, trim($rowLot));
                            if(!empty($cek_lot_input) && $type_mo['type_mo'] == 'colouring') {
                                $tmp_lot_fg .= $rowLot.', ';
                            }

                            //simpan fg hasil
                            $sql_mrp_production_fg_hasil .= "('".$row['kode']."','".$move_id_fg."','".$start."','".$tgl."','".addslashes($row['kode_produk'])."','".addslashes($row['nama_produk'])."','".addslashes(trim($rowLot))."','".addslashes($row['grade'])."','".round($row['qty'],2)."','".addslashes($row['uom'])."','".round($row['qty2'],2)."','".addslashes($row['uom2'])."','".$lokasi_fg['lokasi_tujuan']."','".$nama_user['nama']."','".$row_order."','".addslashes($row['lbr_greige'])."','".addslashes($row['uom_lbr_greige'])."','".addslashes($row['lbr_jadi'])."','".addslashes($row['uom_lbr_jadi'])."', '".addslashes($sales_order)."','".addslashes($sales_group)."'), ";

                            //simpan stock move items produksi
                            $sql_stock_move_items_batch .= "('".$move_id_fg."', '".$start."','".addslashes($row['kode_produk'])."', '".addslashes($row['nama_produk'])."','".addslashes(trim($rowLot))."','".round($row['qty'],2)."','".addslashes($row['uom'])."','".round($row['qty2'],2)."','".addslashes($row['uom2'])."','".$status_done."','".$row_order_smi."','', '".$tgl."','','".addslashes($row['lbr_greige'])."','".addslashes($row['uom_lbr_greige'])."','".addslashes($row['lbr_jadi'])."','".addslashes($row['uom_lbr_jadi'])."'), ";

                        
                            //simpan stock quant dengan quant_id baru              
                            $sql_stock_quant_batch .= "('".$start."','".$tgl."', '".addslashes($row['kode_produk'])."', '".addslashes($row['nama_produk'])."','".addslashes(trim($rowLot))."','".addslashes($row['grade'])."','".round($row['qty'],2)."','".addslashes($row['uom'])."','".round($row['qty2'],2)."','".addslashes($row['uom2'])."','".$lokasi_fg['lokasi_tujuan']."','".addslashes($row['reff_note'])."','".$sm_tj['move_id']."','".$origin_mo."','".$tgl."','".addslashes($row['lbr_greige'])."','".addslashes($row['uom_lbr_greige'])."','".addslashes($row['lbr_jadi'])."','".addslashes($row['uom_lbr_jadi'])."', '".addslashes($sales_order)."','".addslashes($sales_group)."'), ";
                            
                            $case11   .= "when quant_id = '".$start."' then '".$consume."'";
                            $where11  .= "'".$start."',";

                            if($sm_tj['move_id'] != ''){ // jika stock_move tujuan nya tidak kosong maka insert ke smi

                                // cek method apakakah OUT,IN,CON
                                $mthd          = explode('|',$sm_tj['method']);
                                //$method_dept   = trim($mthd[0]);
                                $method_action = trim($mthd[1]);//OUT,IN,CON
                                if($method_action == 'OUT'){
                                    // stock_move_tujuan = pengiriman barang
                                    $sm_tj['move_id'];
                                    $kode_out = $this->_module->get_kode_pengiriman_by_move_id($sm_tj['move_id'])->row_array();
                                    
                                    // get origin_prod by kode
                                    $op = $this->m_mo->get_origin_prod_pengiriman_barang_by_kode($kode_out['kode'],addslashes($kode_produk))->row_array();
                                    $origin_prod = $op['origin_prod'];

                                    //update status pengiriman barang
                                    //$get_kode_out = $this->_module->get_kode_pengiriman_by_move_id($sm_tj['move_id'])->row_array();
                                    if(!empty($kode_out['kode'])){
                                        //update pengiriman barang items = ready
                                        $case8  .= "when kode = '".$kode_out['kode']."' then '".$status_ready."'";
                                        $where8 .= "'".$kode_out['kode']."',"; 
                                    }

                                }else if($method_action == 'IN'){
                                    // get kode penerimaan barang by move_id
                                    $kode_in = $this->_module->get_kode_penerimaan_by_move_id($sm_tj['move_id'])->row_array();
                                    
                                    // get origin_prod by kode
                                    $op = $this->m_mo->get_origin_prod_penerimaan_barang_by_kode($kode_in['kode'],addslashes($kode_produk))->row_array();
                                    $origin_prod = $op['origin_prod'];

                                    //update status penerimaan barang
                                    if(!empty($kode_in['kode'])){
                                        //update penerimaan barang items = ready
                                        $case9  .= "when kode = '".$kode_in['kode']."' then '".$status_ready."'";
                                        $where9 .= "'".$kode_in['kode']."',"; 
                                    }
                                }else if($method_action == 'CON'){
                                    // get origin prod by kode 
                                    $op = $this->m_mo->get_origin_prod_mrp_production_by_kode_mrp($row['kode'],addslashes($kode_produk))->row_array();
                                    $origin_prod = $op['origin_prod'];

                                    // update status mrp_production 
                                    if(!empty($row['kode'])){
                                        // update mrp_production dan rm target
                                        $case10  .= "when kode = '".$row['kode']."' then '".$status_ready."'";
                                        $where10 .= "'".$row['kode']."',"; 
                                        $where10x = $kode_produk;
                                    }
                                }
                        
                                //simpan stock move item tujuan
                                $sql_stock_move_items_batch .= "('".$sm_tj['move_id']."', '".$start."','".addslashes($row['kode_produk'])."', '".addslashes($row['nama_produk'])."','".addslashes(trim($rowLot))."','".round($row['qty'],2)."','".addslashes($row['uom'])."','".round($row['qty2'],2)."','".addslashes($row['uom2'])."','".$status_ready."','".$row_order_smi_tujuan."','".addslashes($origin_prod)."', '".$tgl."','','".addslashes($row['lbr_greige'])."','".addslashes($row['uom_lbr_greige'])."','".addslashes($row['lbr_jadi'])."','".addslashes($row['uom_lbr_jadi'])."'), ";

                                //update status stock move,stock move dan stock move produk  pengiriman brg = ready
                                $case7  .= "when move_id = '".$sm_tj['move_id']."' then '".$status_ready."'";
                                $where7 .= "'".$sm_tj['move_id']."',";

                            }

                            //cek lot apa pernah diinput ?
                            if($cek_dl == 'true'){
                                $cek_lot = $this->m_mo->cek_lot_stock_quant(addslashes(trim($rowLot)))->row_array();
                                if(strtoupper($cek_lot['lot']) == strtoupper(trim($rowLot))){
                                    $lot_double .= $rowLot.',';
                                }
                            }

                            /*
                            //cek lot apa pernah diinput ?
                            $cek_lot = $this->m_mo->cek_lot_stock_quant(addslashes(trim($rowLot)),'ADJ')->row_array();
                            if($cek_lot['lot'] == trim($rowLot)){
                                //ambil lot double untuk alert
                                $lot_double .= $rowLot.',';
                            }
                            */


                            $start++;
                            $row_order++;
                            $row_order_smi++;
                            $row_order_smi_tujuan++;
                            $jml_lot_fg++;
                            $list_lot_fg .= trim($rowLot).' <br> ';
                        }//foreach array_fg
                        

                        if($sm_tj['move_id'] != ''){ // jika stock_move tujuan nya tidak kosong maka update pengiriman barang

                            //update status pengiriman barang
                            $get_kode_out = $this->_module->get_kode_pengiriman_by_move_id($sm_tj['move_id'])->row_array();
                            if(!empty($get_kode_out['kode'])){
                                //update pengiriman barang items = ready
                                $case8  .= "when kode = '".$get_kode_out['kode']."' then '".$status_ready."'";
                                $where8 .= "'".$get_kode_out['kode']."',"; 
                            }

                        }

                    } else { //if jika array_fg tidak kosong
                        throw new \Exception('Maaf, Produk Lot tidak boleh Kosong ! ', 200);
                    }

                    if(!empty($tmp_lot_fg)) {
                        $tmp_lot_fg = rtrim($tmp_lot_fg, ', ');
                        throw new \Exception('Lot sudah diinput ! <br> '.$tmp_lot_fg, 200);
                    }

                    if(!empty($array_waste)){
                        $move_fg    = $this->m_mo->get_move_id_fg_target_by_kode($kode)->row_array();
                        $move_id_fg = $move_fg['move_id'];
                    
                        //lokasi waste lot by dept id
                        $lokasi_waste = $this->m_mo->get_location_waste_by_deptid($deptid)->row_array();
                        $cek_dl       = $this->m_mo->cek_validasi_double_lot_by_dept($deptid);

                        foreach ($array_waste as $row) {

                            if($row['waste'] == 'D'){
                                $lot_remark = 'D|'.$row['lot'];
                            }else if($row['waste'] == 'F'){
                                $lot_remark  = 'F|'.$row['lot'];
                            }else{
                                $lot_remark  = $row['lot'];
                            }

                            //simpan fg hasil
                            $sql_mrp_production_fg_hasil .= "('".$row['kode']."','".$move_id_fg."','".$start."','".$tgl."','".addslashes($row['kode_produk'])."','".addslashes($row['nama_produk'])."','".addslashes(trim($lot_remark))."','','".round($row['qty'],2)."','".addslashes($row['uom'])."','".round($row['qty2'],2)."','".addslashes($row['uom2'])."','".$lokasi_waste['waste_location']."','".$nama_user['nama']."','".$row_order."','','','','', '".addslashes($sales_order)."','".addslashes($sales_group)."'), ";

                            //simpan stock quant dengan quant_id baru              
                            $sql_stock_quant_batch .= "('".$start."','".$tgl."', '".addslashes($row['kode_produk'])."', '".addslashes($row['nama_produk'])."','".addslashes(trim($lot_remark))."','','".round($row['qty'],2)."','".$row['uom']."','".round($row['qty2'],2)."','".addslashes($row['uom2'])."','".$lokasi_waste['waste_location']."','".addslashes($row['reff_note'])."','".$move_id_fg."','".$origin_mo."','".$tgl."','','','','', '".addslashes($sales_order)."','".addslashes($sales_group)."'), ";

                            //simpan stock move items produksi
                            $sql_stock_move_items_batch .= "('".$move_id_fg."', '".$start."','".addslashes($row['kode_produk'])."', '".addslashes($row['nama_produk'])."','".addslashes(trim($lot_remark))."','".round($row['qty'],2)."','".addslashes($row['uom'])."','".round($row['qty2'],2)."','".addslashes($row['uom2'])."','".$status_done."','".$row_order_smi."','','".$tgl."','','','','',''), ";

                            //cek lot apa pernah diinput ?
                            if($cek_dl == 'true'){
                                $cek_lot = $this->m_mo->cek_lot_stock_quant_waste(addslashes(trim($lot_remark)),$lokasi_waste['waste_location'])->row_array();
                                if(strtoupper($cek_lot['lot']) == strtoupper(trim($lot_remark))){
                                    $lot_double_Waste .= $lot_remark.',';
                                }
                            }

                            /*
                            //cek lot apa pernah diinput ?
                            $cek_lot = $this->m_mo->cek_lot_stock_quant_waste(addslashes(trim($row['lot'])),$lokasi_waste['waste_location'])->row_array();
                            if($cek_lot['lot'] == trim($row['lot'])){
                                //ambil lot double untuk alert
                                $lot_double_Waste .= $row['lot'].',';
                            }
                            */

                            $start++;
                            $row_order++;
                            $row_order_smi++;
                            $jml_lot_waste++;
                            $list_lot_waste .= trim($lot_remark).' <br> ';

                        }//foreach array_waste

                    }//jika array_waste tidak kosong

                    
                    $list_sm_rm = $this->m_mo->get_move_id_rm_target_by_kode($kode)->result();
                    //$move_id_rm = $move_rm['move_id'];
                    $rm_not_valid = false;

                    if(!empty($array_rm)){
                        //simpan rm hasil
                        $move_arr     = [];
                        $move_id_rm   = '';
                        // get list row order by move_id;
                        foreach($list_sm_rm as $listsm){
                            $move_id_rm  = $listsm->move_id; // get salah satu move_id
                            $row_order   = $this->_module->get_row_order_stock_move_items_by_kode($listsm->move_id); // row yang sudah + 1
                            $move_arr[]  = array('move_id' => $listsm->move_id, 'row_order' => $row_order);
                        }

                        //lokasi tujuan rm
                        $lokasi_rm = $this->_module->get_location_by_move_id($move_id_rm)->row_array();

                        $dept  = $this->_module->get_nama_dept_by_kode($deptid)->row_array();// get dept stock 
                        
                        $get_ro      = $this->m_mo->get_row_order_rm_hasil($kode)->row_array();
                        $row_order_rm= $get_ro['row']+1;
                        $row_rm_cons = 1;
                        $cek_qty_konsum = 0;
                        $tmp_qty_smi    = 0;
                        foreach ($array_rm as $row) {

                            $get_sq = $this->_module->get_stock_quant_by_id($row['quant_id'])->row_array();

                            if($get_sq['lokasi'] == $dept['stock_location']){// cek lokasi rm

                                if($get_sq['qty'] == $row['qty_smi'] AND $get_sq['qty2'] == $row['qty2']){
                                    
                                    $tmp_qty_smi = $tmp_qty_smi + $row['qty_smi'];
                                    if($row['qty_konsum'] > 0 AND $row['qty_konsum'] != ''){        
                                        
                                        
                                        if($row['qty_konsum']<$row['qty_smi']){//jika qty_konsum kurang dari qty stock_move_items

                                            $loop  = 0;
                                            $row_order_push = 0 ;
                                            foreach($move_arr as $mv_row){
                                                if(isset($mv_row['move_id']) == $row['move_id']){
                                                $row_order      = $mv_row['row_order'];
                                                $row_order_push = $mv_row['row_order'] + 1; // row order + 1 untuk di masukan ke array lagi
                                                array_splice($move_arr,$loop,1);
                                                array_push($move_arr,array('move_id'=>$row['move_id'],'row_order'=>$row_order_push));
                                                break;
                                                }
                                            $loop++;
                                            }

                                            //update qty stock_quant dan stock move items by quant_id
                                            $qty_new = round($row['qty_smi'],2) - round($row['qty_konsum'],2);
                                            $case   .= "when quant_id = '".$row['quant_id']."' then '".$qty_new."'";
                                            $where  .= "'".$row['quant_id']."',";

                                            $qty2_new = round(($row['qty2']/$row['qty_smi'])*$row['qty_konsum'],2);
                                            $qty2_update = round($row['qty2'],2) - round($qty2_new,2);
                                            $case_qty2 .= "when quant_id = '".$row['quant_id']."' then '".$qty2_update."'";
                                            $where_move_items .= "'".$row['move_id']."',";

                                            //simpan qty_konsum di stock_quant dan stock_move_items dengan quant_id baru
                                            $sql_stock_quant_batch .= "('".$start."','".$tgl."', '".addslashes($row['kode_produk'])."', '".addslashes($row['nama_produk'])."','".addslashes(trim($row['lot']))."','".addslashes($row['grade'])."','".round($row['qty_konsum'],2)."','".addslashes($row['uom'])."','".round($qty2_new,2)."','".addslashes($row['uom2'])."','".$lokasi_rm['lokasi_tujuan']."','".addslashes($row['reff_note'])."','".$row['move_id']."','".$origin_mo."','".$tgl."','".addslashes($row['lbr_greige'])."','".addslashes($row['uom_lbr_greige'])."','".addslashes($row['lbr_jadi'])."','".addslashes($row['uom_lbr_jadi'])."', '".addslashes($row['sales_order'])."','".addslashes($row['sales_group'])."'), ";
                                                            
                                            $sql_stock_move_items_batch .= "('".$row['move_id']."', '".$start."','".addslashes($row['kode_produk'])."', '".addslashes($row['nama_produk'])."','".addslashes(trim($row['lot']))."','".round($row['qty_konsum'],2)."','".addslashes($row['uom'])."','".round($qty2_new,2)."','".addslashes($row['uom2'])."','".$status_brg."','".$row_order."','".addslashes($row['origin_prod'])."','".$tgl."','','".addslashes($row['lbr_greige'])."','".addslashes($row['uom_lbr_greige'])."','".addslashes($row['lbr_jadi'])."','".addslashes($row['uom_lbr_jadi'])."'), ";

                                            $sql_mrp_production_rm_hasil .= "('".$row['kode']."','".$row['move_id']."','".addslashes($row['kode_produk'])."','".addslashes($row['nama_produk'])."','".addslashes($row['lot'])."','".round($row['qty_konsum'],2)."','".addslashes($row['uom'])."','".addslashes($row['origin_prod'])."','".$row_order_rm."','".$start."','".$row['additional']."'), ";
                                            //$row_order++;
                                            $start++;

                                        }elseif($row['qty_konsum'] == $row['qty_smi']){//jika qty_konsum sama dengan qty stock_move_items
                                            //update  reserve move di stock_quant by quant_id
                                            /*
                                            $case2   .= "when quant_id = '".$row['quant_id']."' then ''";//move id jadi kosong
                                            $where2  .= "'".$row['quant_id']."',";
                                            */
                                            $case3   .= "when quant_id = '".$row['quant_id']."' then '".$lokasi_rm['lokasi_tujuan']."'"; //update lokasi
                                            $where3  .= "'".$row['quant_id']."',";

                                            $case4   .= "when quant_id = '".$row['quant_id']."' then '".$origin_mo."'"; //update reserve_origin
                                            $where4  .= "'".$row['quant_id']."',";

                                            $case5   .= "when quant_id = '".$row['quant_id']."' then '".$status_brg."'"; //update status done move items
                                            $where5  .= "'".$row['quant_id']."',";
                                            $where5_move_id  .= "'".$row['move_id']."',";


                                            $sql_mrp_production_rm_hasil .= "('".$row['kode']."','".$row['move_id']."','".addslashes($row['kode_produk'])."','".addslashes($row['nama_produk'])."','".addslashes($row['lot'])."','".round($row['qty_konsum'],2)."','".addslashes($row['uom'])."','".addslashes($row['origin_prod'])."','".$row_order_rm."','".$row['quant_id']."','".$row['additional']."'), ";

                                        }
                                        $list_lot_cons .= '('.$row_rm_cons.') '.$row['nama_produk'].' '.$row['lot']." <br>";
                                        $row_rm_cons++;
                                        $row_order_rm++;

                                    } 
                                    $cek_qty_konsum = $cek_qty_konsum + $row['qty_konsum'];

                                }else{
                                    $rm_not_valid = true;
                                }

                            }else{
                                $rm_not_valid = true;
                            }
                        
                        }//foreach array_rm

                        if(!empty($tmp_qty_smi) AND ((double)$cek_qty_konsum <= 0 || empty($cek_qty_konsum))){
                            throw new \Exception('Qty Konsumsi Bahan Baku harus terisi !', 200);
                        }
                        
                    }

                    if($rm_not_valid == false){

                        if(!empty($sql_mrp_production_fg_hasil)){
                            $sql_mrp_production_fg_hasil = rtrim($sql_mrp_production_fg_hasil, ', ');
                            $this->m_mo->simpan_mrp_production_fg_hasil_batch($sql_mrp_production_fg_hasil);               
                        }

                        if(!empty($sql_mrp_production_rm_hasil)){
                            $sql_mrp_production_rm_hasil = rtrim($sql_mrp_production_rm_hasil, ', ');
                            $this->m_mo->simpan_mrp_production_rm_hasil_batch($sql_mrp_production_rm_hasil);
                        }

                        if(!empty($sql_stock_quant_batch) ){
                            $sql_stock_quant_batch = rtrim($sql_stock_quant_batch, ', ');
                            $this->_module->simpan_stock_quant_batch($sql_stock_quant_batch);
                        }

                        if(!empty($sql_stock_move_items_batch)){
                            $sql_stock_move_items_batch = rtrim($sql_stock_move_items_batch, ', ');
                            $this->_module->simpan_stock_move_items_batch($sql_stock_move_items_batch);
                        }
            
                        //update qty di stock_quant dan stock move items
                        if(!empty($where) AND !empty($case)){
                            $where = rtrim($where, ',');
                            $where_move_items = rtrim($where_move_items, ',');
                            $sql_update_qty_stock_quant  = "UPDATE stock_quant SET qty =(case ".$case." end), qty2=( case ".$case_qty2
                            ." end) WHERE  quant_id in (".$where.") ";
                            $this->_module->update_perbatch($sql_update_qty_stock_quant);

                            $sql_update_qty_stock_move_items = "UPDATE stock_move_items SET qty =(case ".$case." end), qty2=( case ".$case_qty2
                            ." end) WHERE  quant_id in (".$where.") AND move_id in (".$where_move_items.") ";
                            $this->_module->update_perbatch($sql_update_qty_stock_move_items);

                        }

                        /* ga dipakai
                        //update move id jadi kosong di stock_quant
                        if(!empty($where2) AND !empty($case2)){
                            $where2 = rtrim($where2, ',');
                            $sql_update_reserve_move  = "UPDATE stock_quant SET reserve_move =(case ".$case2." end) WHERE  quant_id in (".$where2.") ";
                            $this->_module->update_perbatch($sql_update_reserve_move);
                        }
                        */

                        //update lokasi di stock_quant
                        if(!empty($where3) AND !empty($case3)){
                            $where3 = rtrim($where3, ',');
                            $sql_update_lokasi  = "UPDATE stock_quant SET lokasi =(case ".$case3." end), move_date = '".$tgl."' WHERE  quant_id in (".$where3.") ";
                            $this->_module->update_perbatch($sql_update_lokasi);
                        }

                        //update reserve_origin di stock_quant
                        if(!empty($where4) AND !empty($case4)){
                            $where4 = rtrim($where4, ',');
                            $sql_update_reserve_move  = "UPDATE stock_quant SET reserve_origin =(case ".$case4." end) WHERE  quant_id in (".$where4.") ";
                            $this->_module->update_perbatch($sql_update_reserve_move);
                        }

                        //update status done di stock_move_items
                        if(!empty($where5) AND !empty($case5)){
                            $where5 = rtrim($where5, ',');
                            $where5_move_id = rtrim($where5_move_id, ',');
                            $sql_update_status_stock_move_items  = "UPDATE stock_move_items SET status =(case ".$case5." end),tanggal_transaksi ='".$tgl."' WHERE  quant_id in (".$where5.") AND move_id in (".$where5_move_id.") ";
                            $this->_module->update_perbatch($sql_update_status_stock_move_items);
                        }

                        if(!empty($where7) AND !empty($case7)){
                            //update stock move pengiriman barang 
                            $where7 = rtrim($where7, ',');               
                            $sql_update_stock_move  = "UPDATE stock_move SET status =(case ".$case7." end) WHERE  move_id in (".$where7.") ";
                            $this->_module->update_perbatch($sql_update_stock_move);

                            //update stock move produk pengiriman barang               
                            $sql_update_stock_move_produk  = "UPDATE stock_move_produk SET status =(case ".$case7." end) WHERE  move_id in (".$where7.") ";
                            $this->_module->update_perbatch($sql_update_stock_move_produk);
                        }

                        if(!empty($where8) AND !empty($case8)){
                            //update pengiriman barang  
                            $where8 = rtrim($where8, ',');
                            $sql_update_pengiriman_barang  = "UPDATE pengiriman_barang SET status =(case ".$case8." end) WHERE  kode in (".$where8.") ";
                            $this->_module->update_perbatch($sql_update_pengiriman_barang);

                            //update pengiriman barang  items               
                            $sql_update_pengiriman_barang_items  = "UPDATE pengiriman_barang_items SET status_barang =(case ".$case8." end) WHERE  kode in (".$where8.") ";
                            $this->_module->update_perbatch($sql_update_pengiriman_barang_items); 
                        }
                        
                        if(!empty($where9) AND !empty($case9)){
                            //update penerimaan barang
                            $where9 = rtrim($where9, ',');
                            $sql_update_penerimaan_barang  = "UPDATE penerimaan_barang SET status =(case ".$case9." end) WHERE  kode in (".$where9.") ";
                            $this->_module->update_perbatch($sql_update_penerimaan_barang);

                            //update penerimaan barang  items               
                            $sql_update_penerimaan_barang_items  = "UPDATE penerimaan_barang_items SET status_barang =(case ".$case9." end) WHERE  kode in (".$where9.") ";
                            $this->_module->update_perbatch($sql_update_penerimaan_barang_items); 
                        }

                        if(!empty($where10) AND !empty($case10)){
                            // update mrp_production_rm_target
                            $where10 = rtrim($where10, ',');
                            $sql_update_mrp_rm_target  = "UPDATE mrp_production_rm_target SET status =(case ".$case10." end) WHERE  kode in (".$where10.") AND kode_produk = '".addslashes($where10x)."' ";
                            $this->_module->update_perbatch($sql_update_mrp_rm_target); 
                        }

                        if(!empty($array_rm)){
                            $where6_move_id = '';
                            foreach ($array_rm as $row) {

                                if($row['qty_konsum'] > 0 AND $row['qty_konsum'] != ''){                        
                                    //untuk update status
                                    //cek jml_qty di stock_move_items yg status nya ready
                                    $cek_smi=$this->m_mo->cek_qty_stock_move_items_by_produk($row['move_id'],addslashes($row['origin_prod']),'ready')->row_array();
                                    if(empty($cek_smi['jml_qty']) or $cek_smi['jml_qty'] == '0'){
                                        //cek yg status nya done
                                        $cek_smi2=$this->m_mo->cek_qty_stock_move_items_by_produk($row['move_id'],addslashes($row['origin_prod']),'done')->row_array();
                                        if($cek_smi2['jml_qty'] < $row['qty_rm']){
                                            //update status barang jadi draft
                                            $case6   .= "when origin_prod = '".addslashes($row['origin_prod'])."' then 'draft' ";
                                            $where6  .= "'".addslashes($row['origin_prod'])."',";
                                            $where6_move_id .= "'".addslashes($row['move_id'])."',";
                                        }else if($cek_smi2['jml_qty'] >= $row['qty_rm']){
                                            //update status barang jadi done
                                            $case6   .= "when origin_prod = '".addslashes($row['origin_prod'])."' then 'done' "; 
                                            $where6  .= "'".addslashes($row['origin_prod'])."',";
                                            $where6_move_id .= "'".addslashes($row['move_id'])."',";
                                        }
                                    }  
                                }
                            }
                        }       

                        //update status barang di rm target dan stock_move_produk
                        if(!empty($where6) AND !empty($case6)){
                            $where6 = rtrim($where6, ',');
                            $where6_move_id = rtrim($where6_move_id, ',');
                            $sql_update_status_rm_target ="UPDATE mrp_production_rm_target SET status =(case ".$case6." end) WHERE  origin_prod in (".$where6.") AND kode = '".$kode."' AND move_id in (".$where6_move_id.")  ";
                            $this->_module->update_perbatch($sql_update_status_rm_target);

                            $sql_update_status_stock_move_produk ="UPDATE stock_move_produk SET status =(case ".$case6." end) WHERE  origin_prod in (".$where6.") AND move_id in (".$where6_move_id.") ";
                            $this->_module->update_perbatch($sql_update_status_stock_move_produk);

                        }

                        $sl   = $this->_module->get_nama_dept_by_kode($deptid)->row_array();// get ,copy_bahanbaku true/false

                        //update consume == yes
                        if(!empty($where11) AND !empty($case11) AND $sl['copy_bahan_baku']=='true' ){
                            $where11 = rtrim($where11, ',');
                            $sql_update_status_consume ="UPDATE mrp_production_fg_hasil SET consume =(case ".$case11." end) WHERE  quant_id in (".$where11.") AND kode = '".$kode."' AND move_id  ='".$move_id_fg."'  ";
                            $this->_module->update_perbatch($sql_update_status_consume);
                        }

                        ///cek qty sudah produksi sudah memenuhi atau belum ?
                        $qty_target = $this->m_mo->get_qty_mrp_production_fg_target($kode)->row_array();

                        $qty_hasil  = $this->m_mo->get_qty_mrp_production_fg_hasil($kode)->row_array();

                        if($qty_hasil['sum_qty'] >= $qty_target['qty']){
                            $this->m_mo->update_status_mrp_production_fg_target($kode,'done');
                            $this->_module->update_status_stock_move($qty_target['move_id'],'done');
                            //update stock_move_produk fg_target
                            $sql_update_status_stock_move_produk_fg_target = "UPDATE stock_move_produk SET status = 'done' Where move_id = '".$qty_target['move_id']."'";
                            $this->_module->update_perbatch($sql_update_status_stock_move_produk_fg_target); 
                        }

                        if(!empty($array_fg) ){ 
                            // jika dept id nya DYE, FIN
                            if($deptid == 'DYE' OR $deptid == 'FIN' OR $deptid == 'FIN-R' OR $deptid == 'DYE-R'){
                                // update mrp_production, stock_move_produk, stock_move, stock_move_items = done
                                //$move_rm_obat = $this->m_mo->get_move_id_rm_target_obat_by_kode($kode)->row_array();s
                                //$move_rm_obat = $move_rm_obat['move_id'];
                                $sql_mrp_production_rm_hasil_obat = '';
                                // get move id resep by kode MG
                                $list_move_rm_obat                = $this->m_mo->get_list_move_id_rm_obat_by_kode($kode,'')->result();
                                if(!empty($list_move_rm_obat)){
                                    $where_sq_obat = "";
                                    $where_move_id = "";
                                    foreach($list_move_rm_obat as $smo){
                                        
                                        //lokasi tujuan rm obat setelah poduksi
                                        $lokasi_tujuan_rm_obat = $this->_module->get_location_by_move_id($smo->move_id)->row_array();

                                        $get_smi_obat    = $this->_module->get_stock_move_items_by_move_id($smo->move_id,'ready');
                                        $where_move_id  .= "'".$smo->move_id."',";

                                        foreach($get_smi_obat as $sobat){
                                            $where_sq_obat  .= "'".$sobat->quant_id."',";
                                            
                                            $sql_mrp_production_rm_hasil_obat .= "('".$row['kode']."','".$smo->move_id."','".addslashes($sobat->kode_produk)."','".addslashes($sobat->nama_produk)."','".addslashes($sobat->lot)."','".$sobat->qty."','".addslashes($sobat->uom)."','".addslashes($sobat->origin_prod)."','".$sobat->row_order."','".$sobat->quant_id."','".$smo->additional."'), ";
                                        }

                                    }


                                    if(!empty($sql_mrp_production_rm_hasil_obat)){
                                        $sql_mrp_production_rm_hasil_obat = rtrim($sql_mrp_production_rm_hasil_obat, ', ');
                                        $this->m_mo->simpan_mrp_production_rm_hasil_batch($sql_mrp_production_rm_hasil_obat);
                                    
                                        // update lokasi rm obat di stock_quant by id
                                        if(!empty($where_sq_obat)){
                                        
                                            $where_sq_obat = rtrim($where_sq_obat, ',');
                                            $sql_update_lokasi_rm_obat  = "UPDATE stock_quant SET lokasi = '".$lokasi_tujuan_rm_obat['lokasi_tujuan']."', move_date = '".$tgl."' WHERE  quant_id in (".$where_sq_obat.") ";
                                            $this->_module->update_perbatch($sql_update_lokasi_rm_obat);
                                        }

                                        if(!empty($where_move_id)){

                                            $where_move_id = rtrim($where_move_id, ',');

                                            $sql_update_status_stock_move_obat = "UPDATE stock_move SET status = 'done' Where move_id in (".$where_move_id.") ";
                                            $this->_module->update_perbatch($sql_update_status_stock_move_obat); 
                    
                                            $sql_update_status_stock_move_items_obat = "UPDATE stock_move_items SET status = 'done', tanggal_transaksi = '".$tgl."' Where move_id in (".$where_move_id.") ";
                                            $this->_module->update_perbatch($sql_update_status_stock_move_items_obat); 
                    
                                            $sql_update_status_stock_move_produk_obat = "UPDATE stock_move_produk SET status = 'done' Where move_id in (".$where_move_id.") ";
                                            $this->_module->update_perbatch($sql_update_status_stock_move_produk_obat);
                                            
                                            $sql_update_status_mrp_production_rm_target_obat = "UPDATE mrp_production_rm_target SET status = 'done' Where move_id in (".$where_move_id.")  AND kode = '".$kode."' ";
                                            $this->_module->update_perbatch($sql_update_status_mrp_production_rm_target_obat);
                                        }
                                    }

                                }
                            }
                        }
                    
                                    
                        //unlock table
                        $this->_module->unlock_tabel();                  
                    
                        $lot_double = rtrim($lot_double,',');

                        /*
                        if(empty($array_rm) AND !empty($array_fg)){
                            $callback = array('status' => 'failed', 'message'=>'Maaf, Konsumsi Bahan Kosong !', 'icon' => 'fa fa-check', 'type'=>'danger');

                        }else 
                        */
                        if(empty($array_fg) AND empty($array_waste)){
                            $callback = array('status' => 'failed', 'message'=>'Maaf, Produk Lot/ Waste tidak boleh Kosong !', 'icon' => 'fa fa-check', 'type'=>'danger');

                        }else if(!empty($lot_double) or !empty($lot_double_Waste)){
                            if(!empty($lot_double)){                    
                                $callback = array('status' => 'success', 'message'=>'Data Berhasil Disimpan !', 'icon' => 'fa fa-check', 'type'=>'success', 'double'=> 'yes', 'message2' => 'Lot " '.$lot_double.' " sudah pernah diinput !');
                            }

                            if(!empty($lot_double_Waste)){                    
                                $callback = array('status' => 'success', 'message'=>'Data Berhasil Disimpan !', 'icon' => 'fa fa-check', 'type'=>'success', 'double'=> 'yes', 'message2' => 'Lot Waste " '.$lot_double_Waste.' " sudah pernah diinput !');
                            }

                            if(!empty($lot_double_Waste) AND !empty($lot_double)){                    
                                $callback = array('status' => 'success', 'message'=>'Data Berhasil Disimpan !', 'icon' => 'fa fa-check', 'type'=>'success', 'double'=> 'yes', 'message2' => 'Lot " '.$lot_double.' " sudah pernah diinput ! <br> Lot Waste " '.$lot_double_Waste.' " sudah pernah diinput !');
                            }

                        }else{
                            $callback = array('status' => 'success', 'message'=>'Data Berhasil Disimpan !', 'icon' => 'fa fa-check', 'type'=>'success');
                        }
                        
                        if(!empty($array_fg) OR !empty($array_waste)){ 

                            if(!empty($list_lot_fg)){
                                $lot_fg = " <br> List Lot : <br> ".$list_lot_fg;
                            }else{
                                $lot_fg = '';
                            }

                            if(!empty($list_lot_waste)){
                                $lot_waste = "<br> List Waste : <br> ".$list_lot_waste;
                            }else{
                                $lot_waste = '';
                            }

                            if(!empty($list_lot_cons)){
                                $list_cons = " <br> List Lot Bahan Baku : <br> ".$list_lot_cons;
                            }else{
                                $list_cons = "";
                            }

                            if(!empty($array_fg) AND !empty($array_waste)){
                                $note_log    = "Produksi Batch ". $kode.' | Jumlah LOT : '.$jml_lot_fg.' & Jumlah Waste :'.$jml_lot_waste.' '.$lot_fg.' '.$lot_waste. ' ' . $list_cons;
                            }else if(!empty($array_fg)){
                                $note_log    = "Produksi Batch ". $kode.' | Jumlah LOT : '.$jml_lot_fg.' '.$lot_fg . ' ' . $list_cons;
                            }else{
                                $note_log    = "Produksi Batch ". $kode.' | Jumlah Waste : '.$jml_lot_waste.' '.$lot_waste;;
                            }

                            $jenis_log   = "edit";
                            $note_log    = $note_log;
                            $this->_module->gen_history_deptid($sub_menu, $kode, $jenis_log, addslashes($note_log), $username,$deptid);          
                        }

                    }else{
                        $callback = array('status' => 'failed', 'message'=>'Data Gagal Disimpan, Bahan Baku tidak Valid !', 'icon' => 'fa fa-check', 'type'=>'danger');
                    }
                }
            }

            if (!$this->_module->finishTransaction()) {
                throw new \Exception('Gagal Simpan Produksi Batch ', 200);
            }

            // unlock table
            // $this->_module->unlock_tabel();
            $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($callback));

            // echo json_encode($callback);
        } catch(Exception $ex){
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'status'=>'failed', 'icon' => 'fa fa-warning', 'type' => 'danger')));
        } finally {
            // $this->_module->rollbackTransaction();
            // unlock table
            $this->_module->unlock_tabel();
        }
    }

    public function save_produksi_modal()
    {
        try{
            if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
                // session habis
                throw new \Exception('Waktu Anda Telah Habis ', 401);
            }else{

                $sub_menu = $this->uri->segment(2);
                $username = addslashes($this->session->userdata('username')); 
                $nama_user = $this->_module->get_nama_user($username)->row_array();
                $deptid   = $this->input->post('deptid');

                $array_rm    = json_decode($this->input->post('data_rm'),true);     
                $kode        = $this->input->post('kode');
                $origin_mo   = $this->input->post('origin_mo');
                $kode_produk = $this->input->post('kode_produk');    
                $nama_produk = $this->input->post('nama_produk');    
                $lot         = preg_replace('/\s/', '', $this->input->post('lot'));
                $qty         = $this->input->post('qty');
                $uom         = $this->input->post('uom');
                $qty2        = $this->input->post('qty2');
                $uom2        = $this->input->post('uom2');
                $reff_note   = $this->input->post('reff_note');
                $grade       = $this->input->post('grade');
                $lebar_greige     = $this->input->post('lebar_greige');
                $uom_lebar_greige = $this->input->post('uom_lebar_greige');
                $lebar_jadi       = $this->input->post('lebar_jadi');
                $uom_lebar_jadi   = $this->input->post('uom_lebar_jadi');
                $tgl         = date('Y-m-d H:i:s');
                $status_done  = 'done';
                $sql_mrp_production_fg_hasil = "";
                $sql_mrp_production_rm_hasil = "";
                $sql_stock_quant_batch       = "";
                $sql_stock_move_items_batch  = "";
                $case  = "";
                $where = "";
                $case2 = "";
                $where2= "";
                $case3 = "";
                $where3= "";
                $case4 = "";
                $where4= "";
                $case5 = "";
                $where5= "";
                $case6 = "";
                $where6= "";
                $case7 = "";
                $where7= "";
                $case8 = "";
                $where8= "";
                $case9 = "";
                $where9= "";
                $case10= "";
                $where10="";
                $where10x="";
                $lot_double = "";
                $case_qty2= "";
                $qty2_update = "";
                $where_move_items= "";
                $where5_move_id  = "";
                $qty2_new = "";
                $hasil_produksi = FALSE;
                $case11     = "";
                $where11    = "";
                $consume    = "yes";

                // start transaction
                // $this->_module->startTransaction();

                //lock table
                $this->_module->lock_tabel('mrp_production WRITE, mrp_production_rm_hasil WRITE, mrp_production_fg_hasil WRITE, mrp_production_rm_target WRITE, mrp_production_fg_target WRITE, stock_move WRITE, stock_move_items WRITE, stock_quant WRITE, stock_move_produk WRITE, departemen WRITE, pengiriman_barang WRITE, pengiriman_barang_items WRITE, penerimaan_barang WRITE, penerimaan_barang_items WRITE, sales_contract WRITE,mrp_production_rm_target as rm WRITE, mst_produk as mp WRITE, stock_move_items as smi WRITE, mrp_production as mrp WRITE, mrp_production_fg_hasil as fg WRITE, departemen as d WRITE');

                //cek status mrp_production = done
                $cek1  = $this->m_mo->cek_status_mrp_production($kode,'done')->row_array();
                //cek status mrp_production = cancel
                $cek2  = $this->m_mo->cek_status_mrp_production($kode,'cancel')->row_array();
                //cek status mrp_production = hold
                $cek3  = $this->m_mo->cek_status_mrp_production($kode,'hold')->row_array();
        
                if(!empty($cek1['status'])){
                    $callback = array('status' => 'failed', 'message'=>'Maaf, Data Tidak Bisa Disimpan, Status MO Sudah Done !', 'icon' => 'fa fa-warning', 'type'=>'danger');
                }else if(!empty($cek2['status'])){
                    $callback = array('status' => 'failed', 'message'=>'Maaf, Data Tidak Bisa Disimpan, Status MO Batal !', 'icon' => 'fa fa-warning', 'type'=>'danger');
                }else if(!empty($cek3['status'])){
                    $callback = array('status' => 'failed', 'message'=>'Maaf, Data Tidak Bisa Disimpan, Status MO di Hold !', 'icon' => 'fa fa-warning', 'type'=>'danger');
                }else{

                    //get last quant id
                    $start = $this->_module->get_last_quant_id();
                    $get_ro   = $this->m_mo->get_row_order_fg_hasil($kode)->row_array();
                    $row_order= $get_ro['row']+1;
                    $status_ready = 'ready';
            
                    $move_fg  = $this->m_mo->get_move_id_fg_target_by_kode($kode)->row_array();
                    $move_id_fg = $move_fg['move_id'];
                    
                    //lokasi tujuan fg
                    $lokasi_fg = $this->_module->get_location_by_move_id($move_id_fg)->row_array();

                    //get move id tujuan
                    //$method= $deptid.'|OUT';
                    //$method= $deptid.'|OUT'; dihilangkan
                    $sm_tj = $this->_module->get_stock_move_tujuan_mo($move_id_fg,$origin_mo,'done','cancel')->row_array();
                
                    //get row order stock_move_items tujuan
                    $row_order_smi_tujuan  = $this->_module->get_row_order_stock_move_items_by_kode($sm_tj['move_id']);

                    //get row order stock_move_items produksi
                    $row_order_smi  = $this->_module->get_row_order_stock_move_items_by_kode($move_id_fg);

                    // get sales_group / mkt by sales_contract 
                    $org_mo      = explode("|", $origin_mo);
                    $org_mo_loop = 0;
                    $sales_order = "";
                    foreach($org_mo as $org_mos){
                        if($org_mo_loop == 0){
                            $sales_order = trim($org_mos);
                        }
                        $org_mo_loop++;
                    }
        
                    $sales_group = $this->_module->get_sales_group_by_sales_order($sales_order);

                    
                    //** START Hasil Produksi **\\
                    
                    //cek jika kode produk/nama produk tidak kosong
                    if(!empty($kode_produk) AND !empty($nama_produk) AND !empty($lot) ){
                        $hasil_produksi = TRUE;

                        //untuk mendapatkan origin_prod yang terdapat consume kedepannya atau consume lebih dari 1
                        $loop_sm    = true;
                        $loop_count = 1;
                        $origin_prod_tj = "";
                        $next       = false;
                        $con_next   = false;
                        $con        = false;

                        /*
                        //get list stock_move by origin
                        $list_sm = $this->_module->get_list_stock_move_origin($origin_mo)->result_array();
                        foreach ($list_sm as $row) {
                            
                            $mt = explode("|", $row['method']);
                            $ex_deptid = $mt[0];
                            $ex_mt     = $mt[1];

                            if($loop_sm == true){

                                if($ex_mt == 'CON' AND $con_next == true){

                                    //get  origin_prod by move id, kode_produk
                                    $get_origin_prod = $this->m_mo->get_origin_prod_mrp_production_by_kode($row['move_id'],addslashes($kode_produk))->row_array();
                                    $origin_prod_tj = $get_origin_prod['origin_prod'];
                                    $loop_sm =false;
                                    
                                }

                                if($ex_deptid == $deptid AND $ex_mt == 'CON'){
                                    $con_next = true;
                                }
                            }elseif($loop_sm == false){
                                break;//paksa keluar looping
                            }

                                //$loop_count = $loop_count + 1;
                        }
                                

                        if(!empty($origin_prod_tj)){
                            $origin_prod = $origin_prod_tj;
                        }else{
                            $origin_prod = '';
                        }
                        */

                    
                        //simpan fg hasil
                        $sql_mrp_production_fg_hasil .= "('".$kode."','".$move_id_fg."','".$start."','".$tgl."','".addslashes($kode_produk)."','".addslashes($nama_produk)."','".addslashes(trim($lot))."','".addslashes($grade)."','".round($qty,2)."','".addslashes($uom)."','".round($qty2,2)."','".addslashes($uom2)."','".$lokasi_fg['lokasi_tujuan']."','".$nama_user['nama']."','".$row_order."','".addslashes($lebar_greige)."','".addslashes($uom_lebar_greige)."','".addslashes($lebar_jadi)."','".addslashes($uom_lebar_jadi)."', '".addslashes($sales_order)."','".addslashes($sales_group)."'), ";
                            
                        //simpan stock quant dengan quant_id baru              
                        $sql_stock_quant_batch .= "('".$start."','".$tgl."', '".addslashes($kode_produk)."', '".addslashes($nama_produk)."','".addslashes(trim($lot))."','".addslashes($grade)."','".round($qty,2)."','".addslashes($uom)."','".round($qty2,2)."','".addslashes($uom2)."','".$lokasi_fg['lokasi_tujuan']."','".addslashes($reff_note)."','".$sm_tj['move_id']."','".$origin_mo."','".$tgl."','".addslashes($lebar_greige)."','".addslashes($uom_lebar_greige)."','".addslashes($lebar_jadi)."','".addslashes($uom_lebar_jadi)."', '".addslashes($sales_order)."','".addslashes($sales_group)."'), ";
                    
                        //simpan stock move items  produksi
                        $sql_stock_move_items_batch .= "('".$move_id_fg."', '".$start."','".addslashes($kode_produk)."', '".addslashes($nama_produk)."','".addslashes(trim($lot))."','".round($qty,2)."','".addslashes($uom)."','".round($qty2,2)."','".addslashes($uom2)."','".$status_done."','".$row_order_smi."','', '".$tgl."','','".addslashes($lebar_greige)."','".addslashes($uom_lebar_greige)."','".addslashes($lebar_jadi)."','".addslashes($uom_lebar_jadi)."'), ";

                        $case11   .= "when quant_id = '".$start."' then '".$consume."'";
                        $where11  .= "'".$start."',";

                        if($sm_tj['move_id'] != ''){ // jika stock_move tujuan nya tidak kosong maka insert ke smi

                            // cek method apakakah OUT,IN,CON
                            $mthd          = explode('|',$sm_tj['method']);
                            //$method_dept   = trim($mthd[0]);
                            $method_action = trim($mthd[1]);//OUT,IN,CON
                            if($method_action == 'OUT'){
                                // stock_move_tujuan = pengiriman barang
                                $sm_tj['move_id'];
                                $kode_out = $this->_module->get_kode_pengiriman_by_move_id($sm_tj['move_id'])->row_array();
                                
                                // get origin_prod by kode
                                $op = $this->m_mo->get_origin_prod_pengiriman_barang_by_kode($kode_out['kode'],addslashes($kode_produk))->row_array();
                                $origin_prod = $op['origin_prod'];

                                //update status pengiriman barang
                                //$get_kode_out = $this->_module->get_kode_pengiriman_by_move_id($sm_tj['move_id'])->row_array();
                                if(!empty($kode_out['kode'])){
                                    //update pengiriman barang items = ready
                                    $case8  .= "when kode = '".$kode_out['kode']."' then '".$status_ready."'";
                                    $where8 .= "'".$kode_out['kode']."',"; 
                                }

                            }else if($method_action == 'IN'){
                                // get kode penerimaan barang by move_id
                                $kode_in = $this->_module->get_kode_penerimaan_by_move_id($sm_tj['move_id'])->row_array();
                                
                                // get origin_prod by kode
                                $op = $this->m_mo->get_origin_prod_penerimaan_barang_by_kode($kode_in['kode'],addslashes($kode_produk))->row_array();
                                $origin_prod = $op['origin_prod'];

                                //update status penerimaan barang
                                if(!empty($kode_in['kode'])){
                                    //update penerimaan barang items = ready
                                    $case9  .= "when kode = '".$kode_in['kode']."' then '".$status_ready."'";
                                    $where9 .= "'".$kode_in['kode']."',"; 
                                }
                            }else if($method_action == 'CON'){
                                // get origin prod by kode 
                                $op = $this->m_mo->get_origin_prod_mrp_production_by_kode_mrp($kode,addslashes($kode_produk))->row_array();
                                $origin_prod = $op['origin_prod'];

                                // update status mrp_production 
                                if(!empty($kode)){
                                    // update mrp_production dan rm target
                                    $case10  .= "when kode = '".$kode."' then '".$status_ready."'";
                                    $where10 .= "'".$kode."',"; 
                                    $where10x = $kode_produk;
                                }
                            }

                            //stock move items tujuan
                            $sql_stock_move_items_batch .= "('".$sm_tj['move_id']."', '".$start."','".addslashes($kode_produk)."', '".addslashes($nama_produk)."','".addslashes(trim($lot))."','".round($qty,2)."','".addslashes($uom)."','".round($qty2,2)."','".addslashes($uom2)."','".$status_ready."','".$row_order_smi_tujuan."','".addslashes($origin_prod)."', '".$tgl."','','".addslashes($lebar_greige)."','".addslashes($uom_lebar_greige)."','".addslashes($lebar_jadi)."','".addslashes($uom_lebar_jadi)."'), ";

                                
                            //update status stock move,stock move dan stock move produk  pengiriman brg, penerimaanbarang, mrp_production_rm_target == ready
                            $case7  .= "when move_id = '".$sm_tj['move_id']."' then '".$status_ready."'";
                            $where7 .= "'".$sm_tj['move_id']."',";

                        }

                        $cek_dl     = $this->m_mo->cek_validasi_double_lot_by_dept($deptid);

                        //cek lot apa pernah diinput ?
                        if($cek_dl == 'true'){
                            $cek_lot = $this->m_mo->cek_lot_stock_quant(addslashes(trim($lot)))->row_array();
                            if(strtoupper($cek_lot['lot']) == strtoupper(trim($lot))){
                                $lot_double .= $lot.',';
                            }
                        }
                        /*
                        //cek lot apa pernah diinput ?
                        $cek_lot = $this->m_mo->cek_lot_stock_quant(addslashes(trim($lot)),$lokasi_fg['lokasi_tujuan'])->row_array();
                        if($cek_lot['lot'] == trim($lot)){
                            //ambil lot double untuk alert
                            $lot_double .= $lot.',';
                        }
                        */
                        $start++;              

                    }//end if cek jika kode produk/nama produk tidak kosong

                    //** end Hasil Produksi **\\

                
                    //** START konsumsi Bahan **\\

                    $list_sm_rm = $this->m_mo->get_move_id_rm_target_by_kode($kode)->result();
                    //$move_id_rm = $move_rm['move_id'];
                    $rm_not_valid        = false;

                    if(!empty($array_rm)){

                        //simpan rm hasil
                        $move_arr     = [];
                        $move_id_rm   = '';
                        // get list row order by move_id;
                        foreach($list_sm_rm as $listsm){
                            $move_id_rm  = $listsm->move_id; // get salah satu move_id
                            $row_order   = $this->_module->get_row_order_stock_move_items_by_kode($listsm->move_id); // row yang sudah + 1
                            $move_arr[]  = array('move_id' => $listsm->move_id, 'row_order' => $row_order);
                        }

                        //lokasi tujuan rm
                        $lokasi_rm = $this->_module->get_location_by_move_id($move_id_rm)->row_array();
                        $dept      = $this->_module->get_nama_dept_by_kode($deptid)->row_array();// get dept stock

                        $get_ro = $this->m_mo->get_row_order_rm_hasil($kode)->row_array();
                        $row_order_rm= $get_ro['row']+1;
                        foreach ($array_rm as $row) {

                            $get_sq = $this->_module->get_stock_quant_by_id($row['quant_id'])->row_array();

                            if($get_sq['lokasi'] == $dept['stock_location']){// cek lokasi rm

                                if($get_sq['qty'] == $row['qty_smi'] AND $get_sq['qty2'] == $row['qty2']){
                            
                                    if($row['qty_konsum'] > 0 AND $row['qty_konsum'] != ''){                       
                                        
                                        if($row['qty_konsum']<$row['qty_smi']){//jika qty_konsum kurang dari qty stock_move_items

                                            $loop  = 0;
                                            $row_order_push = 0 ;
                                            foreach($move_arr as $mv_row){
                                                if(isset($mv_row['move_id']) == $row['move_id']){
                                                $row_order      = $mv_row['row_order'];
                                                $row_order_push = $mv_row['row_order'] + 1; // row order + 1 untuk di masukan ke array lagi
                                                array_splice($move_arr,$loop,1);
                                                array_push($move_arr,array('move_id'=>$row['move_id'],'row_order'=>$row_order_push));
                                                break;
                                                }
                                            $loop++;
                                            }

                                            //update qty stock_quant dan stock move items by quant_id
                                            $qty_new = round($row['qty_smi'],2) - round($row['qty_konsum'],2);
                                            $case   .= "when quant_id = '".$row['quant_id']."' then '".$qty_new."'";
                                            $where  .= "'".$row['quant_id']."',";

                                            $qty2_new    = round(($row['qty2']/$row['qty_smi'])*$row['qty_konsum'],2);
                                            $qty2_update = $row['qty2'] - $qty2_new;
                                            $case_qty2  .= "when quant_id = '".$row['quant_id']."' then '".$qty2_update."'";
                                            $where_move_items .= "'".$row['move_id']."',";

                                            //simpan qty_konsum di stock_quant dan stock_move_items dengan quant_id baru
                                            $sql_stock_quant_batch .= "('".$start."','".$tgl."', '".addslashes($row['kode_produk'])."', '".addslashes($row['nama_produk'])."','".addslashes(trim($row['lot']))."','".addslashes($row['grade'])."','".round($row['qty_konsum'],2)."','".addslashes($row['uom'])."','".round($qty2_new,2)."','".addslashes($row['uom2'])."','".$lokasi_rm['lokasi_tujuan']."','".addslashes($row['reff_note'])."','".$row['move_id']."','".$origin_mo."','".$tgl."','".addslashes($row['lbr_greige'])."','".addslashes($row['uom_lbr_greige'])."','".addslashes($row['lbr_jadi'])."','".addslashes($row['uom_lbr_jadi'])."', '".addslashes($row['sales_order'])."','".addslashes($row['sales_group'])."'), ";
                                                            
                                            $sql_stock_move_items_batch .= "('".$row['move_id']."', '".$start."','".addslashes($row['kode_produk'])."', '".addslashes($row['nama_produk'])."','".addslashes(trim($row['lot']))."','".round($row['qty_konsum'],2)."','".addslashes($row['uom'])."','".round($qty2_new,2)."','".addslashes($row['uom2'])."','".$status_done."','".$row_order."','".addslashes($row['origin_prod'])."', '".$tgl."','','".addslashes($row['lbr_greige'])."','".addslashes($row['uom_lbr_greige'])."','".addslashes($row['lbr_jadi'])."','".addslashes($row['uom_lbr_jadi'])."'), ";

                                            $sql_mrp_production_rm_hasil .= "('".$row['kode']."','".$row['move_id']."','".addslashes($row['kode_produk'])."','".addslashes($row['nama_produk'])."','".addslashes($row['lot'])."','".round($row['qty_konsum'],2)."','".addslashes($row['uom'])."','".addslashes($row['origin_prod'])."','".$row_order_rm."','".$start."','".$row['additional']."'), ";

                                            $row_order++;
                                            $start++;

                                        }elseif($row['qty_konsum'] == $row['qty_smi']){//jika qty_konsum sama dengan qty stock_move_items
                                            //update  reserve move di stock_quant by quant_id
                                            /*
                                            $case2   .= "when quant_id = '".$row['quant_id']."' then ''";//move id jadi kosong
                                            $where2  .= "'".$row['quant_id']."',";
                                            */
                                            $case3   .= "when quant_id = '".$row['quant_id']."' then '".$lokasi_rm['lokasi_tujuan']."'"; //update lokasi
                                            $where3  .= "'".$row['quant_id']."',";

                                            $case4   .= "when quant_id = '".$row['quant_id']."' then '".$origin_mo."'"; //update reserve_origin
                                            $where4  .= "'".$row['quant_id']."',";

                                            $case5   .= "when quant_id = '".$row['quant_id']."' then '".$status_done."'"; //update status done move items
                                            $where5  .= "'".$row['quant_id']."',";
                                            $where5_move_id .= "'".$row['move_id']."',";

                                            $sql_mrp_production_rm_hasil .= "('".$row['kode']."','".$row['move_id']."','".addslashes($row['kode_produk'])."','".addslashes($row['nama_produk'])."','".addslashes($row['lot'])."','".round($row['qty_konsum'],2)."','".addslashes($row['uom'])."','".addslashes($row['origin_prod'])."','".$row_order_rm."','".$row['quant_id']."','".$row['additional']."'), ";

                                        }
                                        $row_order_rm++;

                                    }
                                }else{
                                    $rm_not_valid = true;
                                }

                            }else{
                                $rm_not_valid = true;
                            }
                        
                        }//foreach array_rm
                    }

                    //** END konsumsi Bahan **\\

                    if($rm_not_valid == false){

                        if(!empty($sql_mrp_production_fg_hasil)){
                            $sql_mrp_production_fg_hasil = rtrim($sql_mrp_production_fg_hasil, ', ');
                            $this->m_mo->simpan_mrp_production_fg_hasil_batch($sql_mrp_production_fg_hasil);               
                        }

                        if(!empty($sql_mrp_production_rm_hasil)){
                            $sql_mrp_production_rm_hasil = rtrim($sql_mrp_production_rm_hasil, ', ');
                            $this->m_mo->simpan_mrp_production_rm_hasil_batch($sql_mrp_production_rm_hasil);
                        }

                        if(!empty($sql_stock_quant_batch) ){
                            $sql_stock_quant_batch = rtrim($sql_stock_quant_batch, ', ');
                            $this->_module->simpan_stock_quant_batch($sql_stock_quant_batch);
                        }

                        if(!empty($sql_stock_move_items_batch)){
                            $sql_stock_move_items_batch = rtrim($sql_stock_move_items_batch, ', ');
                            $this->_module->simpan_stock_move_items_batch($sql_stock_move_items_batch);
                        }
            
                        //update qty di stock_quant dan stock move items
                        if(!empty($where) AND !empty($case)){
                            $where = rtrim($where, ',');
                            $where_move_items = rtrim($where_move_items, ',');
                            $sql_update_qty_stock_quant  = "UPDATE stock_quant SET qty =(case ".$case." end), qty2=( case ".$case_qty2
                            ." end) WHERE  quant_id in (".$where.") ";
                            $this->_module->update_perbatch($sql_update_qty_stock_quant);

                            $sql_update_qty_stock_move_items = "UPDATE stock_move_items SET qty =(case ".$case." end), qty2=( case ".$case_qty2
                            ." end) WHERE  quant_id in (".$where.") AND move_id in (".$where_move_items.") ";
                            $this->_module->update_perbatch($sql_update_qty_stock_move_items);

                        }

                        /*
                        //update move id jadi kosong di stock_quant
                        if(!empty($where2) AND !empty($case2)){
                            $where2 = rtrim($where2, ',');
                            $sql_update_reserve_move  = "UPDATE stock_quant SET reserve_move =(case ".$case2." end) WHERE  quant_id in (".$where2.") ";
                            $this->_module->update_perbatch($sql_update_reserve_move);
                        }
                        */

                        //update lokasi di stock_quant
                        if(!empty($where3) AND !empty($case3)){
                            $where3 = rtrim($where3, ',');
                            $sql_update_lokasi  = "UPDATE stock_quant SET lokasi =(case ".$case3." end), move_date = '".$tgl."' WHERE  quant_id in (".$where3.") ";
                            $this->_module->update_perbatch($sql_update_lokasi);
                        }

                        //update reserve_origin di stock_quant
                        if(!empty($where4) AND !empty($case4)){
                            $where4 = rtrim($where4, ',');
                            $sql_update_reserve_move  = "UPDATE stock_quant SET reserve_origin =(case ".$case4." end) WHERE  quant_id in (".$where4.") ";
                            $this->_module->update_perbatch($sql_update_reserve_move);
                        }

                        //update status done di stock_move_items
                        if(!empty($where5) AND !empty($case5)){
                            $where5 = rtrim($where5, ',');
                            $where5_move_id = rtrim($where5_move_id, ',');
                            $sql_update_status_stock_move_items  = "UPDATE stock_move_items SET status =(case ".$case5." end),tanggal_transaksi ='".$tgl."' WHERE  quant_id in (".$where5.") AND move_id in (".$where5_move_id.") ";
                            $this->_module->update_perbatch($sql_update_status_stock_move_items);
                        }

                        if(!empty($where7) AND !empty($case7)){
                            //update stock move pengiriman barang 
                            $where7 = rtrim($where7, ',');               
                            $sql_update_stock_move  = "UPDATE stock_move SET status =(case ".$case7." end) WHERE  move_id in (".$where7.") ";
                            $this->_module->update_perbatch($sql_update_stock_move);

                            //update stock move produk pengiriman barang               
                            $sql_update_stock_move_produk  = "UPDATE stock_move_produk SET status =(case ".$case7." end) WHERE  move_id in (".$where7.") ";
                            $this->_module->update_perbatch($sql_update_stock_move_produk);
                        }

                        if(!empty($where8) AND !empty($case8)){
                            //update pengiriman barang  
                            $where8 = rtrim($where8, ',');
                            $sql_update_pengiriman_barang  = "UPDATE pengiriman_barang SET status =(case ".$case8." end) WHERE  kode in (".$where8.") ";
                            $this->_module->update_perbatch($sql_update_pengiriman_barang);

                            //update pengiriman barang  items               
                            $sql_update_pengiriman_barang_items  = "UPDATE pengiriman_barang_items SET status_barang =(case ".$case8." end) WHERE  kode in (".$where8.") ";
                            $this->_module->update_perbatch($sql_update_pengiriman_barang_items); 
                        }

                        if(!empty($where9) AND !empty($case9)){
                            //update penerimaan barang
                            $where9 = rtrim($where9, ',');
                            $sql_update_penerimaan_barang  = "UPDATE penerimaan_barang SET status =(case ".$case9." end) WHERE  kode in (".$where9.") ";
                            $this->_module->update_perbatch($sql_update_penerimaan_barang);

                            //update penerimaan barang  items               
                            $sql_update_penerimaan_barang_items  = "UPDATE penerimaan_barang_items SET status_barang =(case ".$case9." end) WHERE  kode in (".$where9.") ";
                            $this->_module->update_perbatch($sql_update_penerimaan_barang_items); 
                        }

                        if(!empty($where10) AND !empty($case10)){
                            // update mrp_production_rm_target
                            $where10 = rtrim($where10, ',');
                            $sql_update_mrp_rm_target  = "UPDATE mrp_production_rm_target SET status =(case ".$case10." end) WHERE  kode in (".$where10.") AND kode_produk = '".addslashes($where10x)."' ";
                            $this->_module->update_perbatch($sql_update_mrp_rm_target); 
                        }

                        if(!empty($array_rm)){
                            foreach ($array_rm as $row) {

                                if($row['qty_konsum'] > 0 AND $row['qty_konsum'] != ''){                        
                                    //untuk update status
                                    //cek jml_qty di stock_move_items yg status nya ready
                                    $cek_smi=$this->m_mo->cek_qty_stock_move_items_by_produk($row['move_id'],addslashes($row['origin_prod']),'ready')->row_array();
                                    if(empty($cek_smi['jml_qty']) or $cek_smi['jml_qty'] == '0'){
                                        //cek yg status nya done
                                        $cek_smi2=$this->m_mo->cek_qty_stock_move_items_by_produk($row['move_id'],addslashes($row['origin_prod']),'done')->row_array();
                                        if($cek_smi2['jml_qty'] < $row['qty_rm']){
                                            //update status barang jadi draft
                                            $case6   .= "when origin_prod = '".addslashes($row['origin_prod'])."' then 'draft' ";
                                            $where6  .= "'".addslashes($row['origin_prod'])."',";

                                        }else if($cek_smi2['jml_qty'] >= $row['qty_rm']){
                                            //update status barang jadi done
                                            $case6   .= "when origin_prod = '".addslashes($row['origin_prod'])."' then 'done' "; 
                                            $where6  .= "'".addslashes($row['origin_prod'])."',";
                                        }
                                    }  
                                }
                            }
                        }   


                        //update status barang di rm target dan stock_move_produk
                        if(!empty($where6) AND !empty($case6)){
                            $where6 = rtrim($where6, ',');
                            $sql_update_status_rm_target ="UPDATE mrp_production_rm_target SET status =(case ".$case6." end) WHERE  origin_prod in (".$where6.") AND kode = '".$kode."' ";
                            $this->_module->update_perbatch($sql_update_status_rm_target);

                            $sql_update_status_stock_move_produk ="UPDATE stock_move_produk SET status =(case ".$case6." end) WHERE  origin_prod in (".$where6.") AND move_id = '".$move_id_rm."' ";
                            $this->_module->update_perbatch($sql_update_status_stock_move_produk);

                        }

                        $sl   = $this->_module->get_nama_dept_by_kode($deptid)->row_array();// get ,copy_bahanbaku true/false


                        //update consume == yes
                        if(!empty($where11) AND !empty($case11) AND $sl['copy_bahan_baku']=='true' ){
                            $where11 = rtrim($where11, ',');
                            $sql_update_status_consume ="UPDATE mrp_production_fg_hasil SET consume =(case ".$case11." end) WHERE  quant_id in (".$where11.") AND kode = '".$kode."' AND move_id  ='".$move_id_fg."'  ";
                            $this->_module->update_perbatch($sql_update_status_consume);
                        }

                        ///cek qty sudah produksi sudah memenuhi atau belum ?
                        $qty_target = $this->m_mo->get_qty_mrp_production_fg_target($kode)->row_array();

                        $qty_hasil  = $this->m_mo->get_qty_mrp_production_fg_hasil($kode)->row_array();

                        if($qty_hasil['sum_qty'] >= $qty_target['qty']){
                            $this->m_mo->update_status_mrp_production_fg_target($kode,'done');
                            $this->_module->update_status_stock_move($qty_target['move_id'],'done');
                            //update stock_move_produk fg_target
                            $sql_update_status_stock_move_produk_fg_target = "UPDATE stock_move_produk SET status = 'done' Where move_id = '".$qty_target['move_id']."'";
                            $this->_module->update_perbatch($sql_update_status_stock_move_produk_fg_target); 
                        }

                        //unlock table
                        $this->_module->unlock_tabel();

                        if($hasil_produksi == TRUE){
                            $jenis_log   = "edit";
                            $note_log    = "Produksi ". $kode.' | LOT : '.$lot;
                            $this->_module->gen_history_deptid($sub_menu, $kode, $jenis_log, $note_log, $username, $deptid);          
                        }
                    
                        $lot_double = rtrim($lot_double,',');
                        /*
                        if(empty($array_rm)){
                            $callback = array('status' => 'failed', 'message'=>'Maaf, Konsumsi Bahan Kosong !', 'icon' => 'fa fa-check', 'type'=>'danger');

                        }else 
                        */
                        if($hasil_produksi == FALSE ){
                            $callback = array('status' => 'failed', 'message'=>'Maaf, Produk Lot / Hasil Produksi Masih Kosong !', 'icon' => 'fa fa-check', 'type'=>'danger');

                        }else   if(!empty($lot_double)){                              
                            $callback = array('status' => 'success', 'message'=>'Data Berhasil Disimpan !', 'icon' => 'fa fa-check', 'type'=>'success', 'double'=> 'yes', 'message2' => 'Lot " '.$lot_double.' " sudah pernah diinput !');
                                    
                        }else{
                            $callback = array('status' => 'success', 'message'=>'Data Berhasil Disimpan !', 'icon' => 'fa fa-check', 'type'=>'success');
                        }
                    
                    }else{
                        $callback = array('status' => 'failed', 'message'=>'Data Gagal Disimpan, Bahan Baku tidak Valid !', 'icon' => 'fa fa-check', 'type'=>'danger');
                    }

                   
                }

            }

            if (!$this->_module->finishTransaction()) {
                throw new \Exception('Gagal Simpan Produksi', 500);
            }

            // unlock table
            // $this->_module->unlock_tabel();
            $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($callback));

        }catch(Exception $ex){

            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        } finally {
            // $this->_module->rollbackTransaction();
            // unlock table
            $this->_module->unlock_tabel();
        }
    }

    public function get_qty_by_produk()
    {
        $kode_produk = addslashes($this->input->post('kode_produk'));
        $qty2        = $this->input->post('qty2');
        $result      = $this->m_mo->get_bom_by_kode_produk($kode_produk)->row_array();
        if(empty($kode_produk)){
            $callback    = array('kode_produk'=>$kode_produk,'qty'=>'');
        }else{
            if(!empty($result)){
                $qty_new     = round(($qty2/floatval($result['qty_item']))*floatval($result['qty']),2);
            }else{
                $qty_new     = 0;
            }
            $callback    = array('kode_produk'=>$kode_produk,'qty'=>$qty_new);
        }

        echo json_encode($callback);
    }

    public function save_produksi_waste_modal()
    {
        try{

            if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
                // session habis
                throw new \Exception('Waktu Anda Telah Habis ', 401);
            }else{

                $sub_menu = $this->uri->segment(2);
                $username = addslashes($this->session->userdata('username')); 
                $nama_user = $this->_module->get_nama_user($username)->row_array();

                $deptid   = $this->input->post('deptid');
                $array_rm    = json_decode($this->input->post('data_rm'),true); 
                $array_waste = json_decode($this->input->post('data_waste'),true); 
                $kode        = $this->input->post('kode');
                $kode_produk = $this->input->post('kode_produk');
                $origin_mo   = $this->input->post('origin_mo');
                $waste_apa   = $this->input->post('waste[]'); // barang jadi (fg) atau baha baku(rm)
                $jenis_waste = $this->input->post('jenis_waste[]'); // fisik(f) atau data(d)

                $tgl         = date('Y-m-d H:i:s');
                $status_brg  = 'done';

                $sql_mrp_production_fg_hasil = "";
                $sql_mrp_production_rm_hasil = "";
                $sql_stock_quant_batch       = "";
                $sql_stock_move_items_batch  = "";
                $lot_double_Waste = "";
                $jml_lot_waste    = 0;
                $case  = "";
                $where = "";
                $case2 = "";
                $where2= "";
                $case3 = "";
                $where3= "";
                $case4 = "";
                $where4= "";
                $case5 = "";
                $where5= "";
                $case6 = "";
                $where6 = "";
                $case_qty2= "";
                $qty2_update = "";
                $where_move_items= "";
                $where5_move_id  = "";
                $where7 = "";
                $case7  = "";
                $consume = "yes";

                //lock table
                $this->_module->lock_tabel('mrp_production WRITE, mrp_production_rm_hasil WRITE, mrp_production_fg_hasil WRITE, mrp_production_rm_target WRITE, mrp_production_fg_target WRITE, stock_move WRITE, stock_move_items WRITE, stock_quant WRITE, stock_move_produk WRITE, departemen WRITE, pengiriman_barang WRITE, pengiriman_barang_items WRITE, penerimaan_barang WRITE, penerimaan_barang_items WRITE, sales_contract WRITE,mrp_production_rm_target as rm WRITE, mst_produk as mp WRITE, stock_move_items as smi WRITE, mrp_production as mrp WRITE, mrp_production_fg_hasil as fg WRITE, departemen as d WRITE');

                //cek status mrp_production = done
                $cek1  = $this->m_mo->cek_status_mrp_production($kode,'done')->row_array();
                //cek status mrp_production = cancel
                $cek2  = $this->m_mo->cek_status_mrp_production($kode,'cancel')->row_array();
                //cek status mrp_production = hold
                $cek3  = $this->m_mo->cek_status_mrp_production($kode,'hold')->row_array();
        
                if(!empty($cek1['status'])){
                    $callback = array('status' => 'failed', 'message'=>'Maaf, Data Tidak Bisa Disimpan, Status MO Sudah Done !', 'icon' => 'fa fa-warning', 'type'=>'danger');
                }else if(!empty($cek2['status'])){
                    $callback = array('status' => 'failed', 'message'=>'Maaf, Data Tidak Bisa Disimpan, Status MO Batal !', 'icon' => 'fa fa-warning', 'type'=>'danger');
                }else if(!empty($cek3['status'])){
                    $callback = array('status' => 'failed', 'message'=>'Maaf, Data Tidak Bisa Disimpan, Status MO di Hold !', 'icon' => 'fa fa-warning', 'type'=>'danger');
                }else{

                    //get last quant id
                    $start          = $this->_module->get_last_quant_id();
                    $get_ro         = $this->m_mo->get_row_order_fg_hasil($kode)->row_array();
                    $row_order      = $get_ro['row']+1;
                    $status_ready   = 'ready';
                    $status_done    = 'done';
                    $move_fg        = $this->m_mo->get_move_id_fg_target_by_kode($kode)->row_array();
                    $move_id_fg     = $move_fg['move_id'];
        
                    // get sales_group / mkt by sales_contract 
                    $org_mo      = explode("|", $origin_mo);
                    $org_mo_loop = 0;
                    $sales_order = "";
                    foreach($org_mo as $org_mos){
                        if($org_mo_loop == 0){
                            $sales_order = trim($org_mos);
                        }
                        $org_mo_loop++;
                    }

                    $rm_not_valid        = false;
                    $pilihan_waste_empty = false;
                    $r_waste_apa         = '';
                    $r_jenis_waste       = '';
                    if(!empty($waste_apa) AND !empty($jenis_waste)){
                        foreach($waste_apa as $wa){
                            $r_waste_apa = $wa;
                            break;
                        }

                        foreach($jenis_waste as $jw){
                            $r_jenis_waste = $jw;
                            break;
                        }

                        if($r_waste_apa == ''){
                            $pilihan_waste_empty = true;
                        }

                        if($r_jenis_waste == ''){
                            $pilihan_waste_empty = true;
                        }
                    }else{

                        $pilihan_waste_empty = true;

                    }


                    $sales_group = $this->_module->get_sales_group_by_sales_order($sales_order);

                    //get row order stock_move_items produksi
                    $row_order_smi  = $this->_module->get_row_order_stock_move_items_by_kode($move_id_fg);

                    if(!empty($array_waste)){
                        $move_fg    = $this->m_mo->get_move_id_fg_target_by_kode($kode)->row_array();
                        $move_id_fg = $move_fg['move_id'];
                    
                        //lokasi waste lot by dept id
                        $lokasi_waste = $this->m_mo->get_location_waste_by_deptid($deptid)->row_array();
                        $cek_dl       = $this->m_mo->cek_validasi_double_lot_by_dept($deptid);

                        foreach ($array_waste as $row) {

                            if($r_waste_apa == 'rm'){
                                if($r_jenis_waste == 'd'){
                                    $lot_remark = 'D|'.$row['lot'];
                                }else{// F
                                    $lot_remark  = 'F|'.$row['lot'];
                                }
                            }else{
                                $lot_remark  = $row['lot'];
                            }

                            //simpan fg hasil
                            $sql_mrp_production_fg_hasil .= "('".$row['kode']."','".$move_id_fg."','".$start."','".$tgl."','".addslashes($row['kode_produk'])."','".addslashes($row['nama_produk'])."','".addslashes(trim($lot_remark))."','','".round($row['qty'],2)."','".addslashes($row['uom'])."','".round($row['qty2'],2)."','".addslashes($row['uom2'])."','".$lokasi_waste['waste_location']."','".$nama_user['nama']."','".$row_order."','','','','', '".addslashes($sales_order)."','".addslashes($sales_group)."'), ";

                            //simpan stock quant dengan quant_id baru              
                            $sql_stock_quant_batch .= "('".$start."','".$tgl."', '".addslashes($row['kode_produk'])."', '".addslashes($row['nama_produk'])."','".addslashes(trim($lot_remark))."','','".round($row['qty'],2)."','".$row['uom']."','".round($row['qty2'],2)."','".addslashes($row['uom2'])."','".$lokasi_waste['waste_location']."','".addslashes($row['reff_note'])."','".$move_id_fg."','".addslashes($origin_mo)."','".$tgl."','','','','', '".addslashes($sales_order)."','".addslashes($sales_group)."'), ";

                            //simpan stock move items produksi
                            $sql_stock_move_items_batch .= "('".$move_id_fg."', '".$start."','".addslashes($row['kode_produk'])."', '".addslashes($row['nama_produk'])."','".addslashes(trim($lot_remark))."','".round($row['qty'],2)."','".addslashes($row['uom'])."','".round($row['qty2'],2)."','".addslashes($row['uom2'])."','".$status_done."','".$row_order_smi."','','".$tgl."','','','','',''), ";

                            //cek lot apa pernah diinput ?
                            if($cek_dl == 'true'){
                                $cek_lot = $this->m_mo->cek_lot_stock_quant_waste(addslashes(trim($lot_remark)),$lokasi_waste['waste_location'])->row_array();
                                if(strtoupper($cek_lot['lot']) == strtoupper(trim($lot_remark))){
                                    $lot_double_Waste .= $lot_remark.',';
                                }
                            }

                            $case7   .= "when quant_id = '".$start."' then '".$consume."'";
                            $where7  .= "'".$start."',";

                            /*
                            //cek lot apa pernah diinput ?
                            $cek_lot = $this->m_mo->cek_lot_stock_quant_waste(addslashes(trim($row['lot'])),$lokasi_waste['waste_location'])->row_array();
                            if($cek_lot['lot'] == trim($row['lot'])){
                                //ambil lot double untuk alert
                                $lot_double_Waste .= $row['lot'].',';
                            }
                            */

                            $start++;
                            $row_order++;
                            $row_order_smi++;
                            $jml_lot_waste++;
                        }//foreach array_waste

                    }//jika array_waste tidak kosong

                    $list_sm_rm = $this->m_mo->get_move_id_rm_target_by_kode($kode)->result();

                    // menentukan konsum rm iya atau tidak
                    $konsum_rm  = false;
                    if($r_waste_apa == 'fg'){
                        if($r_jenis_waste == "f"){
                            $konsum_rm = true;
                        }
                    }else{// rm
                        if($r_jenis_waste == 'f'){
                            $konsum_rm = true;
                        }
                    }


                    if(!empty($array_rm) AND $konsum_rm == true){
                        //simpan rm hasil
                        $move_arr     = [];
                        $move_id_rm   = '';
                        // get list row order by move_id;
                        foreach($list_sm_rm as $listsm){
                            $move_id_rm  = $listsm->move_id; // get salah satu move_id
                            $row_order   = $this->_module->get_row_order_stock_move_items_by_kode($listsm->move_id); // row yang sudah + 1
                            $move_arr[]  = array('move_id' => $listsm->move_id, 'row_order' => $row_order);
                        }

                        //lokasi tujuan rm
                        $lokasi_rm = $this->_module->get_location_by_move_id($move_id_rm)->row_array();

                        $dept      = $this->_module->get_nama_dept_by_kode($deptid)->row_array();// get dept stock
                        
                        $get_ro      = $this->m_mo->get_row_order_rm_hasil($kode)->row_array();
                        $row_order_rm= $get_ro['row']+1;
                        foreach ($array_rm as $row) {
                            $get_sq = $this->_module->get_stock_quant_by_id($row['quant_id'])->row_array();

                            if($get_sq['lokasi'] == $dept['stock_location']){// cek lokasi rm

                                if($get_sq['qty'] == $row['qty_smi'] AND $get_sq['qty2'] == $row['qty2']){
                                
                                    if($r_waste_apa == 'fg' OR $r_waste_apa == 'rm'){ // jika waste baarang jadi, bahan baku 

                                        if($r_jenis_waste == 'f'){// jenis yang akan di wastenya fisik

                                            if($row['qty_konsum'] > 0 AND $row['qty_konsum'] != ''){                     
                                        
                                                if($row['qty_konsum']<$row['qty_smi']){//jika qty_konsum kurang dari qty stock_move_items
                        
                                                    $loop  = 0;
                                                    $row_order_push = 0 ;
                                                    foreach($move_arr as $mv_row){
                                                        if(isset($mv_row['move_id']) == $row['move_id']){
                                                        $row_order      = $mv_row['row_order'];
                                                        $row_order_push = $mv_row['row_order'] + 1; // row order + 1 untuk di masukan ke array lagi
                                                        array_splice($move_arr,$loop,1);
                                                        array_push($move_arr,array('move_id'=>$row['move_id'],'row_order'=>$row_order_push));
                                                        break;
                                                        }
                                                    $loop++;
                                                    }
                        
                                                    //update qty stock_quant dan stock move items by quant_id
                                                    $qty_new = round($row['qty_smi'],2) - round($row['qty_konsum'],2);
                                                    $case   .= "when quant_id = '".$row['quant_id']."' then '".$qty_new."'";
                                                    $where  .= "'".$row['quant_id']."',";
                        
                                                    $qty2_new       = round($row['qty2'],2) - round($row['qty2_konsum'],2);
                                                    $case_qty2     .= "when quant_id = '".$row['quant_id']."' then '".$qty2_new."'";
                                                    $where_move_items .= "'".$row['move_id']."',";

                                                    if($r_waste_apa == 'fg'){
                        
                                                        //simpan qty_konsum di stock_quant dan stock_move_items dengan quant_id baru
                                                        $sql_stock_quant_batch .= "('".$start."','".$tgl."', '".addslashes($row['kode_produk'])."', '".addslashes($row['nama_produk'])."','".addslashes(trim($row['lot']))."','".addslashes($row['grade'])."','".round($row['qty_konsum'],2)."','".addslashes($row['uom'])."','".round($row['qty2_konsum'],2)."','".addslashes($row['uom2'])."','".$lokasi_rm['lokasi_tujuan']."','".addslashes($row['reff_note'])."','".$row['move_id']."','".addslashes($origin_mo)."','".$tgl."','".addslashes($row['lbr_greige'])."','".addslashes($row['uom_lbr_greige'])."','".addslashes($row['lbr_jadi'])."','".addslashes($row['uom_lbr_jadi'])."', '".addslashes($row['sales_order'])."','".addslashes($row['sales_group'])."'), ";
                                                                        
                                                        $sql_stock_move_items_batch .= "('".$row['move_id']."', '".$start."','".addslashes($row['kode_produk'])."', '".addslashes($row['nama_produk'])."','".addslashes(trim($row['lot']))."','".round($row['qty_konsum'],2)."','".addslashes($row['uom'])."','".round($row['qty2_konsum'],2)."','".addslashes($row['uom2'])."','".$status_brg."','".$row_order."','".addslashes($row['origin_prod'])."','".$tgl."','','".addslashes($row['lbr_greige'])."','".addslashes($row['uom_lbr_greige'])."','".addslashes($row['lbr_jadi'])."','".addslashes($row['uom_lbr_jadi'])."'), ";
                            
                                                        $sql_mrp_production_rm_hasil .= "('".$row['kode']."','".$row['move_id']."','".addslashes($row['kode_produk'])."','".addslashes($row['nama_produk'])."','".addslashes($row['lot'])."','".round($row['qty_konsum'],2)."','".addslashes($row['uom'])."','".addslashes($row['origin_prod'])."','".$row_order_rm."','".$start."','".$row['additional']."'), ";
                                                        //$row_order++;
                                                        $start++;
                                                    }

                        
                                                }elseif($row['qty_konsum'] == $row['qty_smi']){//jika qty_konsum sama dengan qty stock_move_items
                                                    //update  reserve move di stock_quant by quant_id
                                                    /*
                                                    $case2   .= "when quant_id = '".$row['quant_id']."' then ''";//move id jadi kosong
                                                    $where2  .= "'".$row['quant_id']."',";
                                                    */

                                                    if($row['qty2_konsum']<$row['qty2']){

                                                        $qty_new     = round($row['qty_smi'],2) - round($row['qty_konsum'],2);
                                                        $case       .= "when quant_id = '".$row['quant_id']."' then '".$qty_new."'";// update qty1 smi sbelumnya
                                                        
                                                        $qty2_new    = round($row['qty2'],2) - round($row['qty2_konsum'],2);
                                                        $case_qty2  .= "when quant_id = '".$row['quant_id']."' then '".$qty2_new."'"; // update qty2 smi sblumnya
                                                        
                                                        $where              .= "'".$row['quant_id']."',";
                                                        $where_move_items   .= "'".$row['move_id']."',";

                                                        if($r_waste_apa == 'fg'){

                                                            //simpan qty_konsum di stock_quant dan stock_move_items dengan quant_id baru
                                                            $sql_stock_quant_batch .= "('".$start."','".$tgl."', '".addslashes($row['kode_produk'])."', '".addslashes($row['nama_produk'])."','".addslashes(trim($row['lot']))."','".addslashes($row['grade'])."','".round($row['qty_konsum'],2)."','".addslashes($row['uom'])."','".round($row['qty2_konsum'],2)."','".addslashes($row['uom2'])."','".$lokasi_rm['lokasi_tujuan']."','".addslashes($row['reff_note'])."','".$row['move_id']."','".addslashes($origin_mo)."','".$tgl."','".addslashes($row['lbr_greige'])."','".addslashes($row['uom_lbr_greige'])."','".addslashes($row['lbr_jadi'])."','".addslashes($row['uom_lbr_jadi'])."', '".addslashes($row['sales_order'])."','".addslashes($row['sales_group'])."'), ";
                                                                            
                                                            $sql_stock_move_items_batch .= "('".$row['move_id']."', '".$start."','".addslashes($row['kode_produk'])."', '".addslashes($row['nama_produk'])."','".addslashes(trim($row['lot']))."','".round($row['qty_konsum'],2)."','".addslashes($row['uom'])."','".round($row['qty2_konsum'],2)."','".addslashes($row['uom2'])."','".$status_brg."','".$row_order."','".addslashes($row['origin_prod'])."','".$tgl."','','".addslashes($row['lbr_greige'])."','".addslashes($row['uom_lbr_greige'])."','".addslashes($row['lbr_jadi'])."','".addslashes($row['uom_lbr_jadi'])."'), ";
                                
                                                            $sql_mrp_production_rm_hasil .= "('".$row['kode']."','".$row['move_id']."','".addslashes($row['kode_produk'])."','".addslashes($row['nama_produk'])."','".addslashes($row['lot'])."','".round($row['qty_konsum'],2)."','".addslashes($row['uom'])."','".addslashes($row['origin_prod'])."','".$row_order_rm."','".$start."','".$row['additional']."'), ";
                                                            $start++;

                                                        }

                                                    }else if($row['qty2_konsum'] == $row['qty2']){
                                                    
                                                        $case3   .= "when quant_id = '".$row['quant_id']."' then '".$lokasi_rm['lokasi_tujuan']."'"; //update lokasi
                                                        $where3  .= "'".$row['quant_id']."',";
                            
                                                        $case4   .= "when quant_id = '".$row['quant_id']."' then '".addslashes($origin_mo)."'"; //update reserve_origin
                                                        $where4  .= "'".$row['quant_id']."',";
                            
                                                        $case5   .= "when quant_id = '".$row['quant_id']."' then '".$status_brg."'"; //update status done move items
                                                        $where5  .= "'".$row['quant_id']."',";
                                                        $where5_move_id  .= "'".$row['move_id']."',";

                                                        if($r_waste_apa == 'fg'){
                                                            $sql_mrp_production_rm_hasil .= "('".$row['kode']."','".$row['move_id']."','".addslashes($row['kode_produk'])."','".addslashes($row['nama_produk'])."','".addslashes($row['lot'])."','".round($row['qty_konsum'],2)."','".addslashes($row['uom'])."','".addslashes($row['origin_prod'])."','".$row_order_rm."','".$row['quant_id']."','".$row['additional']."'), ";
                                                        }
                                                    }

                        
                                                }
                                                $row_order_rm++;
                        
                                            }

                                            if($row['qty_konsum'] == 0 OR $row['qty_konsum'] == '' OR $row['qty_konsum'] == 0.00){  

                                                if($row['qty2_konsum']<$row['qty2']){ //jika qty2_konsum kurang dari qty2 stock_move_items
                                                    
                                                    $loop  = 0;
                                                    $row_order_push = 0 ;
                                                    foreach($move_arr as $mv_row){
                                                        if(isset($mv_row['move_id']) == $row['move_id']){
                                                        $row_order      = $mv_row['row_order'];
                                                        $row_order_push = $mv_row['row_order'] + 1; // row order + 1 untuk di masukan ke array lagi
                                                        array_splice($move_arr,$loop,1);
                                                        array_push($move_arr,array('move_id'=>$row['move_id'],'row_order'=>$row_order_push));
                                                        break;
                                                        }
                                                    $loop++;
                                                    }

                                                    
                                                    $qty_new     = round($row['qty_smi'],2) - round($row['qty_konsum'],2);
                                                    $case       .= "when quant_id = '".$row['quant_id']."' then '".$qty_new."'";// update qty1 smi sbelumnya
                                                    
                                                    $qty2_new    = round($row['qty2'],2) - round($row['qty2_konsum'],2);
                                                    $case_qty2  .= "when quant_id = '".$row['quant_id']."' then '".$qty2_new."'"; // update qty2 smi sblumnya
                                                    
                                                    $where              .= "'".$row['quant_id']."',";
                                                    $where_move_items   .= "'".$row['move_id']."',";

                                                    if($r_waste_apa == 'fg'){

                                                        //simpan qty_konsum di stock_quant dan stock_move_items dengan quant_id baru
                                                        $sql_stock_quant_batch .= "('".$start."','".$tgl."', '".addslashes($row['kode_produk'])."', '".addslashes($row['nama_produk'])."','".addslashes(trim($row['lot']))."','".addslashes($row['grade'])."','".round($row['qty_konsum'],2)."','".addslashes($row['uom'])."','".round($row['qty2_konsum'],2)."','".addslashes($row['uom2'])."','".$lokasi_rm['lokasi_tujuan']."','".addslashes($row['reff_note'])."','".$row['move_id']."','".addslashes($origin_mo)."','".$tgl."','".addslashes($row['lbr_greige'])."','".addslashes($row['uom_lbr_greige'])."','".addslashes($row['lbr_jadi'])."','".addslashes($row['uom_lbr_jadi'])."', '".addslashes($row['sales_order'])."','".addslashes($row['sales_group'])."'), ";
                                                                        
                                                        $sql_stock_move_items_batch .= "('".$row['move_id']."', '".$start."','".addslashes($row['kode_produk'])."', '".addslashes($row['nama_produk'])."','".addslashes(trim($row['lot']))."','".round($row['qty_konsum'],2)."','".addslashes($row['uom'])."','".round($row['qty2_konsum'],2)."','".addslashes($row['uom2'])."','".$status_brg."','".$row_order."','".addslashes($row['origin_prod'])."','".$tgl."','','".addslashes($row['lbr_greige'])."','".addslashes($row['uom_lbr_greige'])."','".addslashes($row['lbr_jadi'])."','".addslashes($row['uom_lbr_jadi'])."'), ";
                            
                                                        $sql_mrp_production_rm_hasil .= "('".$row['kode']."','".$row['move_id']."','".addslashes($row['kode_produk'])."','".addslashes($row['nama_produk'])."','".addslashes($row['lot'])."','".round($row['qty_konsum'],2)."','".addslashes($row['uom'])."','".addslashes($row['origin_prod'])."','".$row_order_rm."','".$start."','".$row['additional']."'), ";
                                                        $start++;
                                                    }
                                                    
                                                }elseif($row['qty2_konsum'] == $row['qty2']){
                                                    
                                                    if($row['qty_konsum']<$row['qty_smi']){

                                                        $qty_new     = round($row['qty_smi'],2) - round($row['qty_konsum'],2);
                                                        $case       .= "when quant_id = '".$row['quant_id']."' then '".$qty_new."'";// update qty1 smi sbelumnya
                                                        
                                                        $qty2_new    = round($row['qty2'],2) - round($row['qty2_konsum'],2);
                                                        $case_qty2  .= "when quant_id = '".$row['quant_id']."' then '".$qty2_new."'"; // update qty2 smi sblumnya
                                                        
                                                        $where              .= "'".$row['quant_id']."',";
                                                        $where_move_items   .= "'".$row['move_id']."',";

                                                        if($r_waste_apa == 'fg'){

                                                            //simpan qty_konsum di stock_quant dan stock_move_items dengan quant_id baru
                                                            $sql_stock_quant_batch .= "('".$start."','".$tgl."', '".addslashes($row['kode_produk'])."', '".addslashes($row['nama_produk'])."','".addslashes(trim($row['lot']))."','".addslashes($row['grade'])."','".round($row['qty_konsum'],2)."','".addslashes($row['uom'])."','".round($row['qty2_konsum'],2)."','".addslashes($row['uom2'])."','".$lokasi_rm['lokasi_tujuan']."','".addslashes($row['reff_note'])."','".$row['move_id']."','".addslashes($origin_mo)."','".$tgl."','".addslashes($row['lbr_greige'])."','".addslashes($row['uom_lbr_greige'])."','".addslashes($row['lbr_jadi'])."','".addslashes($row['uom_lbr_jadi'])."', '".addslashes($row['sales_order'])."','".addslashes($row['sales_group'])."'), ";
                                                                            
                                                            $sql_stock_move_items_batch .= "('".$row['move_id']."', '".$start."','".addslashes($row['kode_produk'])."', '".addslashes($row['nama_produk'])."','".addslashes(trim($row['lot']))."','".round($row['qty_konsum'],2)."','".addslashes($row['uom'])."','".round($row['qty2_konsum'],2)."','".addslashes($row['uom2'])."','".$status_brg."','".$row_order."','".addslashes($row['origin_prod'])."','".$tgl."','','".addslashes($row['lbr_greige'])."','".addslashes($row['uom_lbr_greige'])."','".addslashes($row['lbr_jadi'])."','".addslashes($row['uom_lbr_jadi'])."'), ";
                                
                                                            $sql_mrp_production_rm_hasil .= "('".$row['kode']."','".$row['move_id']."','".addslashes($row['kode_produk'])."','".addslashes($row['nama_produk'])."','".addslashes($row['lot'])."','".round($row['qty_konsum'],2)."','".addslashes($row['uom'])."','".addslashes($row['origin_prod'])."','".$row_order_rm."','".$start."','".$row['additional']."'), ";
                                                            $start++;
                                                        }

                                                    }else if($row['qty_konsum'] == $row['qty_smi']){
                                                    
                                                        $case3   .= "when quant_id = '".$row['quant_id']."' then '".$lokasi_rm['lokasi_tujuan']."'"; //update lokasi
                                                        $where3  .= "'".$row['quant_id']."',";
                            
                                                        $case4   .= "when quant_id = '".$row['quant_id']."' then '".addslashes($origin_mo)."'"; //update reserve_origin
                                                        $where4  .= "'".$row['quant_id']."',";
                            
                                                        $case5   .= "when quant_id = '".$row['quant_id']."' then '".$status_brg."'"; //update status done move items
                                                        $where5  .= "'".$row['quant_id']."',";
                                                        $where5_move_id  .= "'".$row['move_id']."',";

                                                        if($r_waste_apa == 'fg'){
                                                            $sql_mrp_production_rm_hasil .= "('".$row['kode']."','".$row['move_id']."','".addslashes($row['kode_produk'])."','".addslashes($row['nama_produk'])."','".addslashes($row['lot'])."','".round($row['qty_konsum'],2)."','".addslashes($row['uom'])."','".addslashes($row['origin_prod'])."','".$row_order_rm."','".$row['quant_id']."','".$row['additional']."'), ";
                                                        }
                                                    }

                                                }

                                                $row_order_rm++;
                        
                                            }

                                        }
                                    }

                                }else{
                                    $rm_not_valid = true;
                                }

                            }else{
                                $rm_not_valid = true;
                            }

                            // if($row['qty_konsum'] > 0 AND $row['qty_konsum'] != ''){                     
                                
                            //     if($row['qty_konsum']<$row['qty_smi']){//jika qty_konsum kurang dari qty stock_move_items

                            //         $loop  = 0;
                            //         $row_order_push = 0 ;
                            //         foreach($move_arr as $mv_row){
                            //             if(isset($mv_row['move_id']) == $row['move_id']){
                            //               $row_order      = $mv_row['row_order'];
                            //               $row_order_push = $mv_row['row_order'] + 1; // row order + 1 untuk di masukan ke array lagi
                            //               array_splice($move_arr,$loop,1);
                            //               array_push($move_arr,array('move_id'=>$row['move_id'],'row_order'=>$row_order_push));
                            //               break;
                            //             }
                            //           $loop++;
                            //         }

                            //         //update qty stock_quant dan stock move items by quant_id
                            //         $qty_new = $row['qty_smi'] - $row['qty_konsum'];
                            //         $case   .= "when quant_id = '".$row['quant_id']."' then '".$qty_new."'";
                            //         $where  .= "'".$row['quant_id']."',";

                            //         $qty2_new = ($row['qty2']/$row['qty_smi'])*$row['qty_konsum'];
                            //         $qty2_update = $row['qty2'] - $qty2_new;
                            //         $case_qty2 .= "when quant_id = '".$row['quant_id']."' then '".$qty2_update."'";
                            //         $where_move_items .= "'".$row['move_id']."',";

                            //         //simpan qty_konsum di stock_quant dan stock_move_items dengan quant_id baru
                            //         $sql_stock_quant_batch .= "('".$start."','".$tgl."', '".addslashes($row['kode_produk'])."', '".addslashes($row['nama_produk'])."','".addslashes(trim($row['lot']))."','".addslashes($row['grade'])."','".$row['qty_konsum']."','".addslashes($row['uom'])."','".$qty2_new."','".addslashes($row['uom2'])."','".$lokasi_rm['lokasi_tujuan']."','".addslashes($row['reff_note'])."','".$row['move_id']."','".addslashes($origin_mo)."','".$tgl."','".addslashes($row['lbr_greige'])."','".addslashes($row['uom_lbr_greige'])."','".addslashes($row['lbr_jadi'])."','".addslashes($row['uom_lbr_jadi'])."', '".addslashes($row['sales_order'])."','".addslashes($row['sales_group'])."'), ";
                                                    
                            //         $sql_stock_move_items_batch .= "('".$row['move_id']."', '".$start."','".addslashes($row['kode_produk'])."', '".addslashes($row['nama_produk'])."','".addslashes(trim($row['lot']))."','".$row['qty_konsum']."','".addslashes($row['uom'])."','".$qty2_new."','".addslashes($row['uom2'])."','".$status_brg."','".$row_order."','".addslashes($row['origin_prod'])."','".$tgl."','','".addslashes($row['lbr_greige'])."','".addslashes($row['uom_lbr_greige'])."','".addslashes($row['lbr_jadi'])."','".addslashes($row['uom_lbr_jadi'])."'), ";

                            //         $sql_mrp_production_rm_hasil .= "('".$row['kode']."','".$row['move_id']."','".addslashes($row['kode_produk'])."','".addslashes($row['nama_produk'])."','".addslashes($row['lot'])."','".$row['qty_konsum']."','".addslashes($row['uom'])."','".addslashes($row['origin_prod'])."','".$row_order_rm."','".$start."','".$row['additional']."'), ";
                            //         //$row_order++;
                            //         $start++;

                            //     }elseif($row['qty_konsum'] == $row['qty_smi']){//jika qty_konsum sama dengan qty stock_move_items
                            //         //update  reserve move di stock_quant by quant_id
                            //         /*
                            //         $case2   .= "when quant_id = '".$row['quant_id']."' then ''";//move id jadi kosong
                            //         $where2  .= "'".$row['quant_id']."',";
                            //         */
                            //         $case3   .= "when quant_id = '".$row['quant_id']."' then '".$lokasi_rm['lokasi_tujuan']."'"; //update lokasi
                            //         $where3  .= "'".$row['quant_id']."',";

                            //         $case4   .= "when quant_id = '".$row['quant_id']."' then '".addslashes($origin_mo)."'"; //update reserve_origin
                            //         $where4  .= "'".$row['quant_id']."',";

                            //         $case5   .= "when quant_id = '".$row['quant_id']."' then '".$status_brg."'"; //update status done move items
                            //         $where5  .= "'".$row['quant_id']."',";
                            //         $where5_move_id  .= "'".$row['move_id']."',";


                            //         $sql_mrp_production_rm_hasil .= "('".$row['kode']."','".$row['move_id']."','".addslashes($row['kode_produk'])."','".addslashes($row['nama_produk'])."','".addslashes($row['lot'])."','".$row['qty_konsum']."','".addslashes($row['uom'])."','".addslashes($row['origin_prod'])."','".$row_order_rm."','".$row['quant_id']."','".$row['additional']."'), ";

                            //     }
                            //     $row_order_rm++;

                            // }

                        
                        }//foreach array_rm
                        
                    }

                    if($pilihan_waste_empty == false AND $rm_not_valid == false){

                        if(!empty($sql_mrp_production_fg_hasil)){
                            $sql_mrp_production_fg_hasil = rtrim($sql_mrp_production_fg_hasil, ', ');
                            $this->m_mo->simpan_mrp_production_fg_hasil_batch($sql_mrp_production_fg_hasil);               
                        }

                        if(!empty($sql_mrp_production_rm_hasil)){
                            $sql_mrp_production_rm_hasil = rtrim($sql_mrp_production_rm_hasil, ', ');
                            $this->m_mo->simpan_mrp_production_rm_hasil_batch($sql_mrp_production_rm_hasil);
                        }

                        if(!empty($sql_stock_quant_batch) ){
                            $sql_stock_quant_batch = rtrim($sql_stock_quant_batch, ', ');
                            $this->_module->simpan_stock_quant_batch($sql_stock_quant_batch);
                        }

                        if(!empty($sql_stock_move_items_batch)){
                            $sql_stock_move_items_batch = rtrim($sql_stock_move_items_batch, ', ');
                            $this->_module->simpan_stock_move_items_batch($sql_stock_move_items_batch);
                        }
            
                        //update qty di stock_quant dan stock move items
                        if(!empty($where) AND !empty($case)){
                            $where = rtrim($where, ',');
                            $where_move_items = rtrim($where_move_items, ',');
                            $sql_update_qty_stock_quant  = "UPDATE stock_quant SET qty =(case ".$case." end), qty2=( case ".$case_qty2
                            ." end) WHERE  quant_id in (".$where.") ";
                            $this->_module->update_perbatch($sql_update_qty_stock_quant);

                            $sql_update_qty_stock_move_items = "UPDATE stock_move_items SET qty =(case ".$case." end), qty2=( case ".$case_qty2
                            ." end) WHERE  quant_id in (".$where.") AND move_id in (".$where_move_items.") ";
                            $this->_module->update_perbatch($sql_update_qty_stock_move_items);

                        }


                        //update lokasi di stock_quant
                        if(!empty($where3) AND !empty($case3)){
                            $where3 = rtrim($where3, ',');
                            $sql_update_lokasi  = "UPDATE stock_quant SET lokasi =(case ".$case3." end), move_date = '".$tgl."' WHERE  quant_id in (".$where3.") ";
                            $this->_module->update_perbatch($sql_update_lokasi);
                        }

                        //update reserve_origin di stock_quant
                        if(!empty($where4) AND !empty($case4)){
                            $where4 = rtrim($where4, ',');
                            $sql_update_reserve_move  = "UPDATE stock_quant SET reserve_origin =(case ".$case4." end) WHERE  quant_id in (".$where4.") ";
                            $this->_module->update_perbatch($sql_update_reserve_move);
                        }

                        //update status done di stock_move_items
                        if(!empty($where5) AND !empty($case5)){
                            $where5 = rtrim($where5, ',');
                            $where5_move_id = rtrim($where5_move_id, ',');
                            $sql_update_status_stock_move_items  = "UPDATE stock_move_items SET status =(case ".$case5." end),tanggal_transaksi ='".$tgl."' WHERE  quant_id in (".$where5.") AND move_id in (".$where5_move_id.") ";
                            $this->_module->update_perbatch($sql_update_status_stock_move_items);
                        }

                        if(!empty($array_rm)){
                            $where6_move_id = '';
                            foreach ($array_rm as $row) {
            
                                if(($row['qty_konsum'] > 0 AND $row['qty_konsum'] != '') OR ($row['qty2_konsum'] > 0 AND $row['qty2_konsum'] != '')){                       
                                    //untuk update status
                                    //cek jml_qty di stock_move_items yg status nya ready
                                    $cek_smi=$this->m_mo->cek_qty_stock_move_items_by_produk($row['move_id'],addslashes($row['origin_prod']),'ready')->row_array();
                                    if(empty($cek_smi['jml_qty']) or $cek_smi['jml_qty'] == '0'){
                                        //cek yg status nya done
                                        $cek_smi2=$this->m_mo->cek_qty_stock_move_items_by_produk($row['move_id'],addslashes($row['origin_prod']),'done')->row_array();
                                        if($cek_smi2['jml_qty'] < $row['qty_rm']){
                                            //update status barang jadi draft
                                            $case6   .= "when origin_prod = '".addslashes($row['origin_prod'])."' then 'draft' ";
                                            $where6  .= "'".addslashes($row['origin_prod'])."',";
                                            $where6_move_id .= "'".addslashes($row['move_id'])."',";
                                        }else if($cek_smi2['jml_qty'] >= $row['qty_rm']){
                                            //update status barang jadi done
                                            $case6   .= "when origin_prod = '".addslashes($row['origin_prod'])."' then 'done' "; 
                                            $where6  .= "'".addslashes($row['origin_prod'])."',";
                                            $where6_move_id .= "'".addslashes($row['move_id'])."',";
                                        }
                                    }  
                                }

                            }
                        }       
            
                        //update status barang di rm target dan stock_move_produk
                        if(!empty($where6) AND !empty($case6)){
                            $where6 = rtrim($where6, ',');
                            $where6_move_id = rtrim($where6_move_id, ',');
                            $sql_update_status_rm_target ="UPDATE mrp_production_rm_target SET status =(case ".$case6." end) WHERE  origin_prod in (".$where6.") AND kode = '".$kode."' AND move_id in (".$where6_move_id.")  ";
                            $this->_module->update_perbatch($sql_update_status_rm_target);
            
                            $sql_update_status_stock_move_produk ="UPDATE stock_move_produk SET status =(case ".$case6." end) WHERE  origin_prod in (".$where6.") AND move_id in (".$where6_move_id.") ";
                            $this->_module->update_perbatch($sql_update_status_stock_move_produk);
            
                        }

                        //update consume == yes
                        if(!empty($where7) AND !empty($case7)){
                            $where7 = rtrim($where7, ',');
                            $sql_update_status_consume ="UPDATE mrp_production_fg_hasil SET consume =(case ".$case7." end) WHERE  quant_id in (".$where7.") AND kode = '".$kode."' AND move_id  ='".$move_id_fg."'  ";
                            $this->_module->update_perbatch($sql_update_status_consume);
                        }


                        ///cek qty sudah produksi sudah memenuhi atau belum ?
                        $qty_target = $this->m_mo->get_qty_mrp_production_fg_target($kode)->row_array();

                        $qty_hasil  = $this->m_mo->get_qty_mrp_production_fg_hasil($kode)->row_array();

                        if($qty_hasil['sum_qty'] >= $qty_target['qty']){
                            $this->m_mo->update_status_mrp_production_fg_target($kode,'done');
                            $this->_module->update_status_stock_move($qty_target['move_id'],'done');
                            //update stock_move_produk fg_target
                            $sql_update_status_stock_move_produk_fg_target = "UPDATE stock_move_produk SET status = 'done' Where move_id = '".$qty_target['move_id']."'";
                            $this->_module->update_perbatch($sql_update_status_stock_move_produk_fg_target); 
                        }
                        
                                
                        //unlock table
                        $this->_module->unlock_tabel();


                        if(!empty($array_waste)){ 

                            if($r_waste_apa == 'fg'){
                                if($r_jenis_waste == 'd'){
                                    $note_log = " Produksi Waste ".$kode." | Waste Data Barang Jadi, Jumlah Waste : ".$jml_lot_waste;
                                }else if($r_jenis_waste == 'f'){
                                    $note_log = " Produksi Waste ".$kode." | Waste Fisik Barang Jadi, Jumlah Waste : ".$jml_lot_waste;
                                }else{
                                    $note_log = " Produksi Waste ".$kode." | Jumlah Waste : ".$jml_lot_waste;
                                }
                            }else{ // rm
                                if($r_jenis_waste == 'd'){
                                    $note_log = " Produksi Waste ".$kode." | Waste Data Bahan Baku, Jumlah Waste : ".$jml_lot_waste;
                                }else if($r_jenis_waste == 'f'){
                                    $note_log = " Produksi Waste ".$kode." | Waste Fisik Bahan Baku, Jumlah Waste : ".$jml_lot_waste;
                                }else{
                                    $note_log = " Produksi Waste ".$kode." | Jumlah Waste : ".$jml_lot_waste;
                                }
                            }

                            $jenis_log   = "edit";
                            $note_log    = $note_log;
                            $this->_module->gen_history_deptid($sub_menu, $kode, $jenis_log, $note_log, $username,$deptid);     
                        }else{
                            $note_log = "Waste Kosong";
                        }

                        if(empty($array_waste)){
                            $callback = array('status' => 'failed', 'message'=>'Maaf, Produk yang akan di Waste masih Kosong !', 'icon' => 'fa fa-check', 'type'=>'danger');

                        }else if(!empty($lot_double_Waste) AND $r_waste_apa == 'fg'){

                            if(!empty($lot_double_Waste)){                    
                                $callback = array('status' => 'success', 'message'=>'Data Berhasil Disimpan !', 'icon' => 'fa fa-check', 'type'=>'success', 'double'=> 'yes', 'message2' => 'Lot Waste " '.$lot_double_Waste.' " sudah pernah diinput !');
                            }

                        }else{
                            $callback = array('status' => 'success', 'message'=>'Data Berhasil Disimpan !', 'icon' => 'fa fa-check', 'type'=>'success');
                        }

                    }else{
                        if($rm_not_valid == true){
                            $callback = array('status' => 'failed', 'close' => 'yes', 'message'=>'Data Gagal Disimpan, Bahan Baku tidak Valid !', 'icon' => 'fa fa-check', 'type'=>'danger');
                        }else{
                            $callback = array('status' => 'failed', 'close' => 'yes', 'message'=>'Data Gagal Disimpan, Isi terlebih dahulu Mau Waste Apa ? atau Isi terlebih dahulu Jenis Waste Apa ?', 'icon' => 'fa fa-check', 'type'=>'danger');
                        }
                    }

                    // if (!$this->_module->finishTransaction()) {
                    //     throw new \Exception('Gagal Simpan Produksi Batch ', 500);
                    // }
                }
            
            }

            // unlock table
            $this->_module->unlock_tabel();
            
            $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($callback));
            // finish transaction
            // $this->_module->finishTransaction();
            // echo json_encode($callback);

        }catch(Exception $ex){
            // unlock table
            $this->_module->unlock_tabel();

            // $this->_module->finishTransaction();
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
        

    }


    public function save_consume_modal()
    {
        try{

            if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
                // session habis
                throw new \Exception('Waktu Anda Telah Habis ', 401);
            }else{

                $sub_menu = $this->uri->segment(2);
                $username = addslashes($this->session->userdata('username')); 
                $nama_user = $this->_module->get_nama_user($username)->row_array();

                $deptid        = $this->input->post('deptid');
                $kode          = $this->input->post('kode');
                $origin_mo     = $this->input->post('origin_mo');
                $kode_produk   = $this->input->post('kode_produk');
                $array_rm      = json_decode($this->input->post('data_rm'),true);     
                $array_fg      = json_decode($this->input->post('data_fg'),true);    

                $tgl         = date('Y-m-d H:i:s');
                $sql_mrp_production_fg_hasil = "";
                $sql_mrp_production_rm_hasil = "";
                $sql_stock_quant_batch       = "";
                $sql_stock_move_items_batch  = "";
                $konsumsi_bahan  = TRUE;
                $status_brg  = 'done';
                $case_fg   = "";
                $where_fg  = "";
                $case  = "";
                $where = "";
                $case2 = "";
                $where2= "";
                $case3 = "";
                $where3= "";
                $case4 = "";
                $where4= "";
                $case5 = "";
                $where5= "";
                $case6 = "";
                $where6 = "";
                $case_qty2= "";
                $qty2_update = "";
                $where_move_items= "";
                $where5_move_id  = "";

                //lock table
                $this->_module->lock_tabel('mrp_production WRITE, mrp_production_rm_hasil WRITE, mrp_production_fg_hasil WRITE, mrp_production_rm_target WRITE, mrp_production_fg_target WRITE, stock_move WRITE, stock_move_items WRITE, stock_quant WRITE, stock_move_produk WRITE, departemen WRITE, pengiriman_barang WRITE, pengiriman_barang_items WRITE, penerimaan_barang WRITE, penerimaan_barang_items WRITE, sales_contract WRITE,mrp_production_rm_target as rm WRITE, mst_produk as mp WRITE, stock_move_items as smi WRITE, mrp_production as mrp WRITE, mrp_production_fg_hasil as fg WRITE');

                //cek status mrp_production = done
                $cek1  = $this->m_mo->cek_status_mrp_production($kode,'done')->row_array();
                //cek status mrp_production = cancel
                $cek2  = $this->m_mo->cek_status_mrp_production($kode,'cancel')->row_array();
                //cek status mrp_production = hold
                $cek3  = $this->m_mo->cek_status_mrp_production($kode,'hold')->row_array();
    
                if(!empty($cek1['status'])){
                    $callback = array('status' => 'failed', 'message'=>'Maaf, Data Tidak Bisa Disimpan, Status MO Sudah Done !', 'icon' => 'fa fa-warning', 'type'=>'danger');
                }else if(!empty($cek2['status'])){
                    $callback = array('status' => 'failed', 'message'=>'Maaf, Data Tidak Bisa Disimpan, Status MO Batal !', 'icon' => 'fa fa-warning', 'type'=>'danger');
                }else if(!empty($cek3['status'])){
                    $callback = array('status' => 'failed', 'message'=>'Maaf, Data Tidak Bisa Disimpan, Status MO di Hold !', 'icon' => 'fa fa-warning', 'type'=>'danger');
                }else{

                    //get last quant id
                    $start          = $this->_module->get_last_quant_id();
                    $status_ready   = 'ready';
                    $status_done    = 'done';
                    $move_fg        = $this->m_mo->get_move_id_fg_target_by_kode($kode)->row_array();
                    $move_id_fg     = $move_fg['move_id'];
                    $status_consume = "yes";
                    $list_kp_consume = '';
                    $data_consume   = true;
                    $consume_done   = false;
                    $qty_smi_same   = TRUE;
                    $qty_smi_empty  = FALSE;
                    $list_product_smi = "";

                    if(!empty($array_fg)){
                        // looping array fg
                        foreach($array_fg as $row){
                            $cek_cons = $this->m_mo->get_data_mrp_fg_hasil_by_quant($kode,$row['quant_id']);

                            $list_kp_consume .= $row['lot'].", ";
                            
                            if(empty($cek_cons)){
                                $data_consume = false;
                                break;
                            }else if ($cek_cons->consume == 'yes'){
                                $consume_done = true;
                                break;
                            }

                            $case_fg  .= "when quant_id = '".$row['quant_id']."' then '".$status_consume."'";
                            $where_fg .= "'".$row['quant_id']."',"; 


                            
                        }
                    }

                    $list_sm_rm = $this->m_mo->get_move_id_rm_target_by_kode($kode)->result();

                    if(!empty($array_rm) AND $consume_done == false){

                        $konsumsi_bahan = TRUE;
                       

                        //simpan rm hasil
                        $move_arr     = [];
                        $move_id_rm   = '';
                        // get list row order by move_id;list_product_smi 
                        foreach($list_sm_rm as $listsm){
                            $move_id_rm  = $listsm->move_id; // get salah satu move_id
                            $row_order   = $this->_module->get_row_order_stock_move_items_by_kode($listsm->move_id); // row yang sudah + 1
                            $move_arr[]  = array('move_id' => $listsm->move_id, 'row_order' => $row_order);
                        }

                        //lokasi tujuan rm
                        $lokasi_rm = $this->_module->get_location_by_move_id($move_id_rm)->row_array();
                        
                        $get_ro      = $this->m_mo->get_row_order_rm_hasil($kode)->row_array();
                        $row_order_rm= $get_ro['row']+1;
                        foreach ($array_rm as $row) {

                            $cek_qty_smi = $this->m_mo->cek_qty_smi_by_kode($row['move_id'],$row['quant_id'],$row['lot']);
                            $list_product_smi .= $row['nama_produk']." ".$row['lot']." <br> ";
                            if(empty($cek_qty_smi)) {
                                $qty_smi_empty  = TRUE;
                                break;
                            } else if(!empty($cek_qty_smi)) {
                                if((round($cek_qty_smi->qty,2) !=  round($row['qty_smi'],2)) or (round($cek_qty_smi->qty2,2) !=  round($row['qty2'],2)) ) {
                                    $qty_smi_same   = FALSE;
                                    break;
                                }
                            }

                            if($row['qty_konsum'] > 0 AND $row['qty_konsum'] != ''){

                                if($row['qty_konsum'] < $row['qty_smi']){//jika qty_konsum kurang dari qty stock_move_items

                                    $loop  = 0;
                                    $row_order_push = 0 ;
                                    foreach($move_arr as $mv_row){
                                        if(isset($mv_row['move_id']) == $row['move_id']){
                                        $row_order      = $mv_row['row_order'];
                                        $row_order_push = $mv_row['row_order'] + 1; // row order + 1 untuk di masukan ke array lagi
                                        array_splice($move_arr,$loop,1);
                                        array_push($move_arr,array('move_id'=>$row['move_id'],'row_order'=>$row_order_push));
                                        break;
                                        }
                                    $loop++;
                                    }

                                    //update qty stock_quant dan stock move items by quant_id
                                    $qty_new = $row['qty_smi'] - $row['qty_konsum'];
                                    $case   .= "when quant_id = '".$row['quant_id']."' then '".$qty_new."'";
                                    $where  .= "'".$row['quant_id']."',";

                                    $qty2_new       = $row['qty2'] - $row['qty2_konsum'];
                                    $case_qty2     .= "when quant_id = '".$row['quant_id']."' then '".$qty2_new."'";
                                    $where_move_items .= "'".$row['move_id']."',";

                                    //simpan qty_konsum di stock_quant dan stock_move_items dengan quant_id baru
                                    $sql_stock_quant_batch .= "('".$start."','".$tgl."', '".addslashes($row['kode_produk'])."', '".addslashes($row['nama_produk'])."','".addslashes(trim($row['lot']))."','".addslashes($row['grade'])."','".round($row['qty_konsum'],2)."','".addslashes($row['uom'])."','".round($row['qty2_konsum'],2)."','".addslashes($row['uom2'])."','".$lokasi_rm['lokasi_tujuan']."','".addslashes($row['reff_note'])."','".$row['move_id']."','".addslashes($origin_mo)."','".$tgl."','".addslashes($row['lbr_greige'])."','".addslashes($row['uom_lbr_greige'])."','".addslashes($row['lbr_jadi'])."','".addslashes($row['uom_lbr_jadi'])."', '".addslashes($row['sales_order'])."','".addslashes($row['sales_group'])."'), ";
                                                                
                                    $sql_stock_move_items_batch .= "('".$row['move_id']."', '".$start."','".addslashes($row['kode_produk'])."', '".addslashes($row['nama_produk'])."','".addslashes(trim($row['lot']))."','".round($row['qty_konsum'],2)."','".addslashes($row['uom'])."','".round($row['qty2_konsum'],2)."','".addslashes($row['uom2'])."','".$status_brg."','".$row_order."','".addslashes($row['origin_prod'])."','".$tgl."','','".addslashes($row['lbr_greige'])."','".addslashes($row['uom_lbr_greige'])."','".addslashes($row['lbr_jadi'])."','".addslashes($row['uom_lbr_jadi'])."'), ";

                                    $sql_mrp_production_rm_hasil .= "('".$row['kode']."','".$row['move_id']."','".addslashes($row['kode_produk'])."','".addslashes($row['nama_produk'])."','".addslashes($row['lot'])."','".round($row['qty_konsum'],2)."','".addslashes($row['uom'])."','".addslashes($row['origin_prod'])."','".$row_order_rm."','".$start."','".$row['additional']."'), ";
                                    //$row_order++;
                                    $start++;

                                }elseif($row['qty_konsum'] == $row['qty_smi']){//jika qty_konsum sama dengan qty stock_move_items

                                    if($row['qty2_konsum']<$row['qty2']){
                                        $qty_new     = $row['qty_smi'] - $row['qty_konsum'];
                                        $case       .= "when quant_id = '".$row['quant_id']."' then '".$qty_new."'";// update qty1 smi sbelumnya
                                                
                                        $qty2_new    = $row['qty2'] - $row['qty2_konsum'];
                                        $case_qty2  .= "when quant_id = '".$row['quant_id']."' then '".$qty2_new."'"; // update qty2 smi sblumnya
                                                
                                        $where              .= "'".$row['quant_id']."',";
                                        $where_move_items   .= "'".$row['move_id']."',";

                                        //simpan qty_konsum di stock_quant dan stock_move_items dengan quant_id baru
                                        $sql_stock_quant_batch .= "('".$start."','".$tgl."', '".addslashes($row['kode_produk'])."', '".addslashes($row['nama_produk'])."','".addslashes(trim($row['lot']))."','".addslashes($row['grade'])."','".round($row['qty_konsum'],2)."','".addslashes($row['uom'])."','".round($row['qty2_konsum'],2)."','".addslashes($row['uom2'])."','".$lokasi_rm['lokasi_tujuan']."','".addslashes($row['reff_note'])."','".$row['move_id']."','".addslashes($origin_mo)."','".$tgl."','".addslashes($row['lbr_greige'])."','".addslashes($row['uom_lbr_greige'])."','".addslashes($row['lbr_jadi'])."','".addslashes($row['uom_lbr_jadi'])."', '".addslashes($row['sales_order'])."','".addslashes($row['sales_group'])."'), ";
                                                                    
                                        $sql_stock_move_items_batch .= "('".$row['move_id']."', '".$start."','".addslashes($row['kode_produk'])."', '".addslashes($row['nama_produk'])."','".addslashes(trim($row['lot']))."','".round($row['qty_konsum'],2)."','".addslashes($row['uom'])."','".round($row['qty2_konsum'],2)."','".addslashes($row['uom2'])."','".$status_brg."','".$row_order."','".addslashes($row['origin_prod'])."','".$tgl."','','".addslashes($row['lbr_greige'])."','".addslashes($row['uom_lbr_greige'])."','".addslashes($row['lbr_jadi'])."','".addslashes($row['uom_lbr_jadi'])."'), ";
            
                                        $sql_mrp_production_rm_hasil .= "('".$row['kode']."','".$row['move_id']."','".addslashes($row['kode_produk'])."','".addslashes($row['nama_produk'])."','".addslashes($row['lot'])."','".round($row['qty_konsum'],2)."','".addslashes($row['uom'])."','".addslashes($row['origin_prod'])."','".$row_order_rm."','".$start."','".$row['additional']."'), ";
                                        $start++;

                                    }else if($row['qty2_konsum'] == $row['qty2']){

                                        $case3   .= "when quant_id = '".$row['quant_id']."' then '".$lokasi_rm['lokasi_tujuan']."'"; //update lokasi
                                        $where3  .= "'".$row['quant_id']."',";
            
                                        $case4   .= "when quant_id = '".$row['quant_id']."' then '".addslashes($origin_mo)."'"; //update reserve_origin
                                        $where4  .= "'".$row['quant_id']."',";
            
                                        $case5   .= "when quant_id = '".$row['quant_id']."' then '".$status_brg."'"; //update status done move items
                                        $where5  .= "'".$row['quant_id']."',";
                                        $where5_move_id  .= "'".$row['move_id']."',";

                                        $sql_mrp_production_rm_hasil .= "('".$row['kode']."','".$row['move_id']."','".addslashes($row['kode_produk'])."','".addslashes($row['nama_produk'])."','".addslashes($row['lot'])."','".round($row['qty_konsum'],2)."','".addslashes($row['uom'])."','".addslashes($row['origin_prod'])."','".$row_order_rm."','".$row['quant_id']."','".$row['additional']."'), ";


                                    }

                                }
                                $row_order_rm++;

                            }// if qty konsum > 0

                            if($row['qty_konsum'] == 0 OR $row['qty_konsum'] == '' OR $row['qty_konsum'] == 0.00){  
                                
                                if($row['qty2_konsum']<$row['qty2']){ //jika qty2_konsum kurang dari qty2 stock_move_items

                                    $loop  = 0;
                                    $row_order_push = 0 ;
                                    foreach($move_arr as $mv_row){
                                        if(isset($mv_row['move_id']) == $row['move_id']){
                                            $row_order      = $mv_row['row_order'];
                                            $row_order_push = $mv_row['row_order'] + 1; // row order + 1 untuk di masukan ke array lagi
                                            array_splice($move_arr,$loop,1);
                                            array_push($move_arr,array('move_id'=>$row['move_id'],'row_order'=>$row_order_push));
                                            break;
                                        }
                                        $loop++;
                                    }

                                    $qty_new     = $row['qty_smi'] - $row['qty_konsum'];
                                    $case       .= "when quant_id = '".$row['quant_id']."' then '".$qty_new."'";// update qty1 smi sbelumnya
                                    
                                    $qty2_new    = $row['qty2'] - $row['qty2_konsum'];
                                    $case_qty2  .= "when quant_id = '".$row['quant_id']."' then '".$qty2_new."'"; // update qty2 smi sblumnya
                                    
                                    $where              .= "'".$row['quant_id']."',";
                                    $where_move_items   .= "'".$row['move_id']."',";

                                    //simpan qty_konsum di stock_quant dan stock_move_items dengan quant_id baru
                                    $sql_stock_quant_batch .= "('".$start."','".$tgl."', '".addslashes($row['kode_produk'])."', '".addslashes($row['nama_produk'])."','".addslashes(trim($row['lot']))."','".addslashes($row['grade'])."','".round($row['qty_konsum'],2)."','".addslashes($row['uom'])."','".round($row['qty2_konsum'],2)."','".addslashes($row['uom2'])."','".$lokasi_rm['lokasi_tujuan']."','".addslashes($row['reff_note'])."','".$row['move_id']."','".addslashes($origin_mo)."','".$tgl."','".addslashes($row['lbr_greige'])."','".addslashes($row['uom_lbr_greige'])."','".addslashes($row['lbr_jadi'])."','".addslashes($row['uom_lbr_jadi'])."', '".addslashes($row['sales_order'])."','".addslashes($row['sales_group'])."'), ";
                                                                
                                    $sql_stock_move_items_batch .= "('".$row['move_id']."', '".$start."','".addslashes($row['kode_produk'])."', '".addslashes($row['nama_produk'])."','".addslashes(trim($row['lot']))."','".round($row['qty_konsum'],2)."','".addslashes($row['uom'])."','".round($row['qty2_konsum'],2)."','".addslashes($row['uom2'])."','".$status_brg."','".$row_order."','".addslashes($row['origin_prod'])."','".$tgl."','','".addslashes($row['lbr_greige'])."','".addslashes($row['uom_lbr_greige'])."','".addslashes($row['lbr_jadi'])."','".addslashes($row['uom_lbr_jadi'])."'), ";
        
                                    $sql_mrp_production_rm_hasil .= "('".$row['kode']."','".$row['move_id']."','".addslashes($row['kode_produk'])."','".addslashes($row['nama_produk'])."','".addslashes($row['lot'])."','".round($row['qty_konsum'],2)."','".addslashes($row['uom'])."','".addslashes($row['origin_prod'])."','".$row_order_rm."','".$start."','".$row['additional']."'), ";
                                    $start++;

                                }elseif($row['qty2_konsum'] == $row['qty2']){

                                    if($row['qty_konsum']<$row['qty_smi']){

                                        $qty_new     = $row['qty_smi'] - $row['qty_konsum'];
                                        $case       .= "when quant_id = '".$row['quant_id']."' then '".$qty_new."'";// update qty1 smi sbelumnya
                                        
                                        $qty2_new    = $row['qty2'] - $row['qty2_konsum'];
                                        $case_qty2  .= "when quant_id = '".$row['quant_id']."' then '".$qty2_new."'"; // update qty2 smi sblumnya
                                        
                                        $where              .= "'".$row['quant_id']."',";
                                        $where_move_items   .= "'".$row['move_id']."',";

                                        //simpan qty_konsum di stock_quant dan stock_move_items dengan quant_id baru
                                        $sql_stock_quant_batch .= "('".$start."','".$tgl."', '".addslashes($row['kode_produk'])."', '".addslashes($row['nama_produk'])."','".addslashes(trim($row['lot']))."','".addslashes($row['grade'])."','".round($row['qty_konsum'],2)."','".addslashes($row['uom'])."','".round($row['qty2_konsum'],2)."','".addslashes($row['uom2'])."','".$lokasi_rm['lokasi_tujuan']."','".addslashes($row['reff_note'])."','".$row['move_id']."','".addslashes($origin_mo)."','".$tgl."','".addslashes($row['lbr_greige'])."','".addslashes($row['uom_lbr_greige'])."','".addslashes($row['lbr_jadi'])."','".addslashes($row['uom_lbr_jadi'])."', '".addslashes($row['sales_order'])."','".addslashes($row['sales_group'])."'), ";
                                                                    
                                        $sql_stock_move_items_batch .= "('".$row['move_id']."', '".$start."','".addslashes($row['kode_produk'])."', '".addslashes($row['nama_produk'])."','".addslashes(trim($row['lot']))."','".round($row['qty_konsum'],2)."','".addslashes($row['uom'])."','".round($row['qty2_konsum'],2)."','".addslashes($row['uom2'])."','".$status_brg."','".$row_order."','".addslashes($row['origin_prod'])."','".$tgl."','','".addslashes($row['lbr_greige'])."','".addslashes($row['uom_lbr_greige'])."','".addslashes($row['lbr_jadi'])."','".addslashes($row['uom_lbr_jadi'])."'), ";
            
                                        $sql_mrp_production_rm_hasil .= "('".$row['kode']."','".$row['move_id']."','".addslashes($row['kode_produk'])."','".addslashes($row['nama_produk'])."','".addslashes($row['lot'])."','".round($row['qty_konsum'],2)."','".addslashes($row['uom'])."','".addslashes($row['origin_prod'])."','".$row_order_rm."','".$start."','".$row['additional']."'), ";
                                        $start++;

                                    }else if($row['qty_konsum'] == $row['qty_smi']){
                                            
                                        $case3   .= "when quant_id = '".$row['quant_id']."' then '".$lokasi_rm['lokasi_tujuan']."'"; //update lokasi
                                        $where3  .= "'".$row['quant_id']."',";
            
                                        $case4   .= "when quant_id = '".$row['quant_id']."' then '".addslashes($origin_mo)."'"; //update reserve_origin
                                        $where4  .= "'".$row['quant_id']."',";
            
                                        $case5   .= "when quant_id = '".$row['quant_id']."' then '".$status_brg."'"; //update status done move items
                                        $where5  .= "'".$row['quant_id']."',";
                                        $where5_move_id  .= "'".$row['move_id']."',";

                                        $sql_mrp_production_rm_hasil .= "('".$row['kode']."','".$row['move_id']."','".addslashes($row['kode_produk'])."','".addslashes($row['nama_produk'])."','".addslashes($row['lot'])."','".round($row['qty_konsum'],2)."','".addslashes($row['uom'])."','".addslashes($row['origin_prod'])."','".$row_order_rm."','".$row['quant_id']."','".$row['additional']."'), ";
                                    }

                                }

                                $row_order_rm++;
                                
                            }

                        }// end foreach array rm

                    }// !empty array_rm

                    if($konsumsi_bahan == TRUE AND $consume_done == false AND  $qty_smi_same == TRUE AND $qty_smi_empty == FALSE ){
                        
                        //update consume = yes
                        if(!empty($where_fg) AND !empty($case_fg)){
                            $where_fg = rtrim($where_fg, ',');
                            $sql_update_consume_fg_hasil  = "UPDATE mrp_production_fg_hasil SET consume =(case ".$case_fg." end) WHERE  quant_id in (".$where_fg.") AND kode in ('".$kode."') ";
                            $this->_module->update_perbatch($sql_update_consume_fg_hasil);
                        }

                        if(!empty($sql_mrp_production_rm_hasil)){
                            $sql_mrp_production_rm_hasil = rtrim($sql_mrp_production_rm_hasil, ', ');
                            $this->m_mo->simpan_mrp_production_rm_hasil_batch($sql_mrp_production_rm_hasil);
                        }

                        if(!empty($sql_stock_quant_batch) ){
                            $sql_stock_quant_batch = rtrim($sql_stock_quant_batch, ', ');
                            $this->_module->simpan_stock_quant_batch($sql_stock_quant_batch);
                        }

                        if(!empty($sql_stock_move_items_batch)){
                            $sql_stock_move_items_batch = rtrim($sql_stock_move_items_batch, ', ');
                            $this->_module->simpan_stock_move_items_batch($sql_stock_move_items_batch);
                        }

                        //update qty di stock_quant dan stock move items
                        if(!empty($where) AND !empty($case)){
                            $where = rtrim($where, ',');
                            $where_move_items = rtrim($where_move_items, ',');
                            $sql_update_qty_stock_quant  = "UPDATE stock_quant SET qty =(case ".$case." end), qty2=( case ".$case_qty2
                            ." end) WHERE  quant_id in (".$where.") ";
                            $this->_module->update_perbatch($sql_update_qty_stock_quant);

                            $sql_update_qty_stock_move_items = "UPDATE stock_move_items SET qty =(case ".$case." end), qty2=( case ".$case_qty2
                            ." end) WHERE  quant_id in (".$where.") AND move_id in (".$where_move_items.") ";
                            $this->_module->update_perbatch($sql_update_qty_stock_move_items);

                        }

                        //update lokasi di stock_quant
                        if(!empty($where3) AND !empty($case3)){
                            $where3 = rtrim($where3, ',');
                            $sql_update_lokasi  = "UPDATE stock_quant SET lokasi =(case ".$case3." end), move_date = '".$tgl."' WHERE  quant_id in (".$where3.") ";
                            $this->_module->update_perbatch($sql_update_lokasi);
                        }

                        //update reserve_origin di stock_quant
                        if(!empty($where4) AND !empty($case4)){
                            $where4 = rtrim($where4, ',');
                            $sql_update_reserve_move  = "UPDATE stock_quant SET reserve_origin =(case ".$case4." end) WHERE  quant_id in (".$where4.") ";
                            $this->_module->update_perbatch($sql_update_reserve_move);
                        }

                        //update status done di stock_move_items
                        if(!empty($where5) AND !empty($case5)){
                            $where5 = rtrim($where5, ',');
                            $where5_move_id = rtrim($where5_move_id, ',');
                            $sql_update_status_stock_move_items  = "UPDATE stock_move_items SET status =(case ".$case5." end),tanggal_transaksi ='".$tgl."' WHERE  quant_id in (".$where5.") AND move_id in (".$where5_move_id.") ";
                            $this->_module->update_perbatch($sql_update_status_stock_move_items);
                        }

                        if(!empty($array_rm)){
                            $where6_move_id = '';
                            foreach ($array_rm as $row) {
            
                                if(($row['qty_konsum'] > 0 AND $row['qty_konsum'] != '') OR ($row['qty2_konsum'] > 0 AND $row['qty2_konsum'] != '')){                       
                                    //untuk update status
                                    //cek jml_qty di stock_move_items yg status nya ready
                                    $cek_smi=$this->m_mo->cek_qty_stock_move_items_by_produk($row['move_id'],addslashes($row['origin_prod']),'ready')->row_array();
                                    if(empty($cek_smi['jml_qty']) or $cek_smi['jml_qty'] == '0'){
                                        //cek yg status nya done
                                        $cek_smi2=$this->m_mo->cek_qty_stock_move_items_by_produk($row['move_id'],addslashes($row['origin_prod']),'done')->row_array();
                                        if($cek_smi2['jml_qty'] < $row['qty_rm']){
                                            //update status barang jadi draft
                                            $case6   .= "when origin_prod = '".addslashes($row['origin_prod'])."' then 'draft' ";
                                            $where6  .= "'".addslashes($row['origin_prod'])."',";
                                            $where6_move_id .= "'".addslashes($row['move_id'])."',";
                                        }else if($cek_smi2['jml_qty'] >= $row['qty_rm']){
                                            //update status barang jadi done
                                            $case6   .= "when origin_prod = '".addslashes($row['origin_prod'])."' then 'done' "; 
                                            $where6  .= "'".addslashes($row['origin_prod'])."',";
                                            $where6_move_id .= "'".addslashes($row['move_id'])."',";
                                        }
                                    }  
                                }

                            }
                        }   

                        //update status barang di rm target dan stock_move_produk
                        if(!empty($where6) AND !empty($case6)){
                            $where6 = rtrim($where6, ',');
                            $where6_move_id = rtrim($where6_move_id, ',');
                            $sql_update_status_rm_target ="UPDATE mrp_production_rm_target SET status =(case ".$case6." end) WHERE  origin_prod in (".$where6.") AND kode = '".$kode."' AND move_id in (".$where6_move_id.")  ";
                            $this->_module->update_perbatch($sql_update_status_rm_target);
            
                            $sql_update_status_stock_move_produk ="UPDATE stock_move_produk SET status =(case ".$case6." end) WHERE  origin_prod in (".$where6.") AND move_id in (".$where6_move_id.") ";
                            $this->_module->update_perbatch($sql_update_status_stock_move_produk);
            
                        }

                        ///cek qty sudah produksi sudah memenuhi atau belum ?
                        $qty_target = $this->m_mo->get_qty_mrp_production_fg_target($kode)->row_array();

                        $qty_hasil  = $this->m_mo->get_qty_mrp_production_fg_hasil($kode)->row_array();
        
                        if($qty_hasil['sum_qty'] >= $qty_target['qty']){
                            $this->m_mo->update_status_mrp_production_fg_target($kode,'done');
                            $this->_module->update_status_stock_move($qty_target['move_id'],'done');
                            //update stock_move_produk fg_target
                            $sql_update_status_stock_move_produk_fg_target = "UPDATE stock_move_produk SET status = 'done' Where move_id = '".$qty_target['move_id']."'";
                            $this->_module->update_perbatch($sql_update_status_stock_move_produk_fg_target); 
                        }

                        //unlock table
                        $this->_module->unlock_tabel();   
                        
                        if($list_kp_consume == ''){
                            $note_log    = "Konsumsi Bahan tanpa KP/Lot ";
                        }else{
                            $list_kp_consume = rtrim($list_kp_consume, ', ');
                            $note_log    = "Konsumsi Bahan untuk menghasilkan KP/Lot : ".$list_kp_consume;
                        }
                        

                        $jenis_log   = "edit";
                        $note_log    = $note_log;
                        $this->_module->gen_history_deptid($sub_menu, $kode, $jenis_log, $note_log, $username,$deptid);   

                        $callback = array('status' => 'success', 'message'=>'Data Berhasil Disimpan !', 'icon' => 'fa fa-check', 'type'=>'success');

                    }else if($consume_done == true){
                        $callback = array('status' => 'failed', 'message'=>'Data Gagal Disimpan, Lot sudah di consume ! <br>' .$list_kp_consume, 'icon' => 'fa fa-check', 'type'=>'danger');
                    }else if($qty_smi_same == FALSE){
                        $callback = array('status' => 'failed', 'message'=>'Data Gagal Disimpan, Qty Bahan Baku tidak Valid ! <br>' .$list_product_smi, 'icon' => 'fa fa-check', 'type'=>'danger');
                    }else if($qty_smi_empty == true){
                        $callback = array('status' => 'failed', 'message'=>'Data Gagal Disimpan, Qty Bahan Baku tidak ditemukan / Kosong ! <br>' .$list_product_smi, 'icon' => 'fa fa-check', 'type'=>'danger');
                    }else{
                        $callback = array('status' => 'failed', 'message'=>'Data Gagal Disimpan !', 'icon' => 'fa fa-check', 'type'=>'danger');
                    }
                    
                    if (!$this->_module->finishTransaction()) {
                        throw new \Exception('Gagal Simpan Produksi Batch ', 500);
                    }
                }
            }

            // unlock table
            $this->_module->unlock_tabel();
            $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($callback));
            // finish transaction
            // $this->_module->finishTransaction();
            // echo json_encode($callback);

        }catch(Exception $ex){
            // unlock table
            $this->_module->unlock_tabel();

            // $this->_module->finishTransaction();
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }

    }

    public function mo_done_modal()
    {

        $deptid             = $this->input->post('deptid');
        $kode               = $this->input->post('kode');
        $data['kode_mo']    = $kode;
        $data['deptid']     = $deptid;

        $cek_status         = $this->m_mo->cek_status_mrp_production($kode,'')->row_array();
        $data['status']     = $cek_status['status'];
        
        $rm_done            = $this->m_mo->get_sum_qty_rm_done($kode,'done')->row();
        $rm_waste           = $this->m_mo->get_sum_qty_rm_waste($kode,'done')->row();
        $data['rm_done']    = $rm_done;
        $data['rm_waste']   = $rm_waste;

        $fg_adj             = $this->m_mo->get_sum_qty_fg_adj($kode)->row();
        $data['fg_adj']     = $fg_adj;
        
        $fg_prod            = $this->m_mo->get_sum_qty_fg_produce($kode)->row();
        $fg_waste           = $this->m_mo->get_sum_qty_fg_waste($kode)->row();
        $data['fg_prod']    = $fg_prod;
        $data['fg_waste']   = $fg_waste;


        $total_kg_rm        = number_format($rm_done->kg,2);
        $total_kg_fg        = number_format(($fg_prod->kg + $fg_waste->kg - $fg_adj->kg),2);
       
        if($total_kg_fg == $total_kg_rm){
            $data['show_btn'] = true;
        }else{
            $data['show_btn'] = false;
        }

        $data['total_kg_rm'] = $total_kg_rm;
        $data['total_kg_fg'] = $total_kg_fg;

        return $this->load->view('modal/v_mo_done_modal', $data); 

    }

    public function mo_done()
    {

        if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
            // session habis
            $callback = array('message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
        }else{

            $sub_menu = $this->uri->segment(2);
            $username = addslashes($this->session->userdata('username')); 
            $deptid   = $this->input->post('deptid');
            $kode     = $this->input->post('kode');
            // $qty_target = $this->input->post('qty_target');

            // get qty target mrp_production
            $mrp_qty    = $this->m_mo->get_qty_target_mrp($kode)->row_array();
            $qty_target =  $mrp_qty['qty'];


            //$move_id    = $this->input->post('move_id');
            //$done       = true;
            $status     = 'done';
            $status2    = 'draft';

            //cek no mesin apakah terisi ?
            $cek_no_mesin = $this->m_mo->cek_no_mesin_mrp_production_by_kode($kode)->row_array();

             //cek status rm_target apa ada status selain done ?
            $cek = $this->m_mo->cek_status_barang_mrp_production_rm_target_done($kode,$status,$status2)->row_array();

            //cek qty yg sudah di produksi
            $cek2 = $this->m_mo->get_qty_mrp_production_fg_hasil($kode)->row_array();

            //cek status mrp_production
            $cek3  = $this->m_mo->cek_status_mrp_production($kode,'done')->row_array();

            //cek status mrp_production
            $cek4  = $this->m_mo->cek_status_mrp_production($kode,'cancel')->row_array();

            // cek rm target additional yg move id nya kosong / blm request
            $cek5 = $this->m_mo->get_data_rm_target_additional_by_kode_all($kode,'');

            //cek status mrp_production
            $cek6  = $this->m_mo->cek_status_mrp_production($kode,'hold')->row_array();
            
          
            if(!empty($cek3['status'])){
                $callback = array('status' => 'failed', 'message'=>'Maaf, Status MO Sudah Done !', 'icon' => 'fa fa-warning', 'type'=>'danger');
            }else if(!empty($cek4['status'])){
                $callback = array('status' => 'failed', 'message'=>'Maaf, Status MO Batal !', 'icon' => 'fa fa-warning', 'type'=>'danger');
            }else if(!empty($cek6['status'])){
                $callback = array('status' => 'failed', 'message'=>'Maaf, Status MO di Hold !', 'icon' => 'fa fa-warning', 'type'=>'danger');

            }else if(empty($cek_no_mesin['mc_id'])){
                //$done = false;
                $callback = array('status' => 'failed', 'message'=>'Maaf, No Mesin Harus Diisi !', 'icon' => 'fa fa-warning', 'type'=>'danger');

            }else if(!empty($cek['status'])){
                //$done = false;
                $callback = array('status' => 'failed', 'message'=>'Maaf, Bahan baku belum habis !', 'icon' => 'fa fa-warning', 'type'=>'danger');

            }else if($cek2['sum_qty'] < $qty_target){
                //$done = false;
                $callback = array('status' => 'failed', 'message'=>'Maaf, Qty target belum Terpenuhi !', 'icon' => 'fa fa-warning', 'type'=>'danger');
            }else if(!empty($cek5)){
                $callback = array('status' => 'failed', 'message'=>'Maaf, Hapus terlebih dahulu data Additional yg belum di Request!', 'icon' => 'fa fa-warning', 'type'=>'danger');
            }else{

                // cek kg 
                $rm_done            = $this->m_mo->get_sum_qty_rm_done($kode,'done')->row();
                $rm_waste           = $this->m_mo->get_sum_qty_rm_waste($kode,'done')->row();
                $fg_prod            = $this->m_mo->get_sum_qty_fg_produce($kode)->row();
                $fg_waste           = $this->m_mo->get_sum_qty_fg_waste($kode)->row();
                $fg_adj             = $this->m_mo->get_sum_qty_fg_adj($kode)->row();

                $total_kg_rm        = number_format($rm_done->kg,2);
                $total_kg_fg        = number_format(($fg_prod->kg + $fg_waste->kg - $fg_adj->kg),2);

                if($total_kg_rm != $total_kg_fg){
                    $callback = array('status' => 'failed', 'message'=>'<b>KG</b> Bahan Baku dan <b>KG</b> Barang jadi Harus Sama  !!', 'icon' => 'fa fa-warning', 'type'=>'danger');
                }else{

                    $this->m_mo->update_status_mrp_production($kode,$status);
                    // get list move id rm
                    $list_sm_rm = $this->m_mo->get_move_id_rm_target_by_kode($kode)->result();
                    foreach($list_sm_rm as $sm){
                        $this->_module->update_status_stock_move($sm->move_id,$status);
                    }

                    // insert table mo done
                    $this->m_mo->simpan_done_mo($kode,$deptid,$rm_done->mtr,$rm_done->kg,$fg_prod->mtr,$fg_prod->kg,$fg_waste->mtr,$fg_waste->kg,$fg_adj->mtr,$fg_adj->kg,'done');

                    $jenis_log   = "edit";
                    $note_log    = "Done ". $kode;
                    $this->_module->gen_history_deptid($sub_menu, $kode, $jenis_log, $note_log, $username, $deptid);   
                    $callback = array('status' => 'success', 'message'=>'Status Berhasil di Ubah !', 'icon' => 'fa fa-check', 'type'=>'success');
                }
            }

        }

        echo json_encode($callback);
    }

    public function cek_input_lot_double()
    {

        $kode  = $this->input->post('kode');
        $lot   = $this->input->post('txtlot');
        $head  = $this->m_mo->get_data_by_code($kode);

        // cek validasi double lot
        $cek_dl = $this->m_mo->cek_validasi_double_lot_by_dept($head->dept_id);
        $lot_double = FALSE;
        if($cek_dl == 'true'){
            $cek_lot = $this->m_mo->cek_lot_stock_quant(addslashes(trim($lot)))->row_array();
            if((strtoupper($cek_lot['lot']) == strtoupper(trim($lot))) AND $lot !=''){
                $lot_double = TRUE;
            }
        }

        /*
        $move_fg  = $this->m_mo->get_move_id_fg_target_by_kode($kode)->row_array();
        $move_id_fg = $move_fg['move_id'];
        
        //lokasi tujuan fg
        $lokasi_fg = $this->_module->get_location_by_move_id($move_id_fg)->row_array();
        */

        $callback = array('double' => $lot_double, 'message' => 'Lot '.$lot.' sudah pernah diinput ! ');

        echo json_encode($callback);

    }

    public function rekam_cacat_modal()
    {
        $deptid  = $this->input->post('deptid');
        $lot     = $this->input->post('lot');
        $quant_id= $this->input->post('quant_id');
        $kode    = $this->input->post('kode');
        $status  = $this->input->post('status');

        $data['deptid']   = $deptid;
        $data['lot']      = $lot;
        $data['quant_id'] = $quant_id;
        $data['kode']     = $kode;
        $data['status_mo']  = $status;
        $data['list_cacat'] = $this->m_mo->get_list_cacat($deptid);
        $data['rekam_cacat']= $this->m_mo->get_list_rekam_cacat($kode,addslashes($lot),$quant_id);

        return $this->load->view('modal/v_mo_rekam_cacat_modal', $data); 
    }
    
    public function get_body_rekam_cacat()
    {
        $lot     = $this->input->post('lot');
        $quant_id= $this->input->post('quant_id');
        $kode    = $this->input->post('kode');
        $dataRecord= $this->m_mo->get_list_rekam_cacat($kode,addslashes($lot),$quant_id);
        $callback = array('status' => 'success', 'message'=>'Data Berhasil di hapus !', 'icon' => 'fa fa-check', 'type'=>'success', 'items'=>$dataRecord );
        echo json_encode($callback);

    }

    public function save_rekam_cacat_lot_modal()
    {

        if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
            // session habis
            $callback = array('message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
        }else{

            $sub_menu = $this->uri->segment(2);
            $username = $this->session->userdata('username'); 
            $nu       = $this->_module->get_nama_user($username)->row_array();
            $nama_user= addslashes($nu['nama']);

            $deptid   = $this->input->post('deptid');

            $kode        = $this->input->post('kode');
            $array_cacat = $this->input->post('rekam_cacat');
            $quant_id    = $this->input->post('quant_id');
            $lot         = $this->input->post('lot');
            $tgl         = date('Y-m-d H:i:s');
            $sql_mrp_production_cacat = "";
            $case        = "";  
            $case2       = "";
            $where       = "";

            //lock table
            $this->_module->lock_tabel('mrp_production_cacat WRITE, mrp_production WRITE ');

            //cek status mrp_production = done
            $cek1  = $this->m_mo->cek_status_mrp_production($kode,'done')->row_array();
            //cek status mrp_production = cancel
            $cek2  = $this->m_mo->cek_status_mrp_production($kode,'cancel')->row_array();
            //cek status mrp_production = hold
            $cek3  = $this->m_mo->cek_status_mrp_production($kode,'hold')->row_array();

            if(!empty($cek1['status'])){
                $callback = array('status' => 'failed', 'message'=>'Maaf, Data Tidak Bisa Disimpan, Status MO Sudah Done !', 'icon' => 'fa fa-warning', 'type'=>'danger');
            }else if(!empty($cek2['status'])){
                $callback = array('status' => 'failed', 'message'=>'Maaf, Data Tidak Bisa Disimpan, Status MO Batal !', 'icon' => 'fa fa-warning', 'type'=>'danger');
            }else if(!empty($cek3['status'])){
                $callback = array('status' => 'failed', 'message'=>'Maaf, Data Tidak Bisa Disimpan, Status MO di Hold !', 'icon' => 'fa fa-warning', 'type'=>'danger');
            }else{

                $ro        = $this->m_mo->get_row_order_rekam_cacat($kode,addslashes($lot))->row_array();
                $row_order = $ro['row'] + 1;

                foreach ($array_cacat as $row) {
                    if(!empty($row['row_order'])){//update rekam cacat
                        $case   .= "when row_order = '".$row['row_order']."' then '".addslashes($row['point_cacat'])."'"; 
                        $case2  .= "when row_order = '".$row['row_order']."' then '".addslashes($row['kode_cacat'])."'";
                        $where  .= "'".$row['row_order']."',";
                    }else{
                        $sql_mrp_production_cacat .= "('".$kode."','".$quant_id."','".$tgl."','".addslashes(trim($lot))."','".$deptid."','".addslashes($row['point_cacat'])."','".addslashes($row['kode_cacat'])."','".$row_order."','".$nama_user."'), ";
                        $row_order++;
                    }
                }

                if(!empty($sql_mrp_production_cacat)){
                    $sql_mrp_production_cacat = rtrim($sql_mrp_production_cacat, ', ');
                    $this->m_mo->simpan_rekam_cacat_lot($sql_mrp_production_cacat);
                }

                if(!empty($case) AND !empty($where)){
                    $where = rtrim($where, ',');
                    $sql_update_point_cacat="UPDATE mrp_production_cacat SET point_cacat =(case ".$case." end) WHERE  row_order in (".$where.") AND kode = '".$kode."' AND lot = '".$lot."' AND quant_id = '".$quant_id."' ";
                    $this->_module->update_perbatch($sql_update_point_cacat);

                    $sql_update_kode_cacat="UPDATE mrp_production_cacat SET kode_cacat =(case ".$case2." end) WHERE  row_order in (".$where.") AND kode = '".$kode."' AND lot = '".$lot."' AND quant_id = '".$quant_id."' ";
                    $this->_module->update_perbatch($sql_update_kode_cacat);
                }

                //unlock table
                $this->_module->unlock_tabel();

                $kode_encrypt = encrypt_url($kode);

                $jenis_log   = "edit";
                $note_log    = "Rekam Cacat Lot ". $lot;
                $this->_module->gen_history_deptid($sub_menu, $kode, $jenis_log, $note_log, $username, $deptid);   
                $callback = array('status' => 'success', 'message'=>'Data Berhasil Disimpan !', 'icon' => 'fa fa-check', 'type'=>'success', 'kode' => $kode_encrypt);
            }
        }
    
        echo json_encode($callback);
    }

    public function delete_rekam_cacat_lot_modal()
    {

        if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
            // session habis
            $callback = array('message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
        }else{
            $sub_menu = $this->uri->segment(2);
            $username = addslashes($this->session->userdata('username')); 
            $deptid   = $this->input->post('deptid');

            $kode        = $this->input->post('kode');           
            $lot         = addslashes($this->input->post('lot'));
            $quant_id    = $this->input->post('quant_id');
            $row_order   = $this->input->post('row_order');

             //lock table
            $this->_module->lock_tabel('mrp_production_cacat WRITE, mrp_production WRITE');

            //cek status mrp_production = done
            $cek1  = $this->m_mo->cek_status_mrp_production($kode,'done')->row_array();
            //cek status mrp_production = cancel
            $cek2  = $this->m_mo->cek_status_mrp_production($kode,'cancel')->row_array();

            if(!empty($cek1['status'])){
                $callback = array('status' => 'failed', 'message'=>'Maaf, Data Tidak Bisa Disimpan, Status MO Sudah Done !', 'icon' => 'fa fa-warning', 'type'=>'danger');
            }else if(!empty($cek2['status'])){
                $callback = array('status' => 'failed', 'message'=>'Maaf, Data Tidak Bisa Disimpan, Status MO Batal !', 'icon' => 'fa fa-warning', 'type'=>'danger');
            }else{

                //hapus rekam cacat lot 
                $this->m_mo->hapus_rekam_cacat_lot($kode,$quant_id,$lot,$row_order);

                //unlock table
                $this->_module->unlock_tabel();
                
                $jenis_log   = "cancel";
                $note_log    = "Hapus Cacat Lot ". $lot;
                $this->_module->gen_history_deptid($sub_menu, $kode, $jenis_log, $note_log, $username, $deptid);   
                
                $callback = array('status' => 'success', 'message'=>'Data Berhasil Dihapus !', 'icon' => 'fa fa-check', 'type'=>'success');

            }
        }

        echo json_encode($callback);
    }

    public function request_obat_modal()
    {

        $deptid     = $this->input->post('deptid');
        $id_warna   = $this->input->post('id_warna');
        $kode       = $this->input->post('kode');
        $origin     = $this->input->post('origin');

        $data['deptid']      = $deptid;
        $data['id_warna']    = $id_warna;
        $data['kode']        = $kode;
        $data['origin']      = $origin;

        // get nama warna by id
        $data['nama_warna']  = $this->m_mo->get_nama_warna_by_id($id_warna);
        // get_list varian by id_warna
        $data['list_varian'] = $this->m_mo->get_list_varian_by_id($id_warna);

        return $this->load->view('modal/v_mo_request_modal', $data);
    }

    public function request_obat()
    {

        if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
            // session habis
            $callback = array('message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
        }else{
            $kode     = $this->input->post('kode');
            $warna    = $this->input->post('id_warna');
            $origin_mo= $this->input->post('origin');

            $sub_menu = $this->uri->segment(2);
            $username = $this->session->userdata('username'); 
            $nu       = $this->_module->get_nama_user($username)->row_array();
            $nama_user= addslashes($nu['nama']);
            $deptid   = $this->input->post('deptid');
            $varian   = $this->input->post('varian');

            $orgn     = $origin_mo."|".$kode; // ex SO18|CO7|2|OW210300001|MG210300004
            
            //cek status done ?
            $cek1  = $this->m_mo->cek_status_mrp_production($kode,'done')->row_array();
            //cek status cancel ?
            $cek2  = $this->m_mo->cek_status_mrp_production($kode,'cancel')->row_array();
            // cek status kain
            $status_kain = $this->m_mo->cek_status_produk_kain($kode)->row_array();
            //cek status hold ?
            $cek3  = $this->m_mo->cek_status_mrp_production($kode,'hold')->row_array();

            if(!empty($cek1['status'])){
                $callback = array('status' => 'failed', 'message'=>'Maaf, Proses Produksi telah Selesai !', 'icon' => 'fa fa-warning', 'type'=>'danger');
            }else if(!empty($cek2['status'])){
                $callback = array('status' => 'failed', 'message'=>'Maaf, Proses Produksi telah dibatalkan !', 'icon' => 'fa fa-warning', 'type'=>'danger');
            }else if(!empty($cek3['status'])){
                $callback = array('status' => 'failed', 'message'=>'Maaf, MO ini sedang di Hold !', 'icon' => 'fa fa-warning', 'type'=>'danger');
            }else if(empty($status_kain['status'])){
                $callback = array('message' => 'Maaf, Produk (kain) belum Ready !', 'icon' => 'fa fa-warning', 'type'=>'danger', 'status' => 'failed' );
            }else if(empty($varian)){
                $callback = array('message' => 'Varian Harus diisi !',  'status' => 'failed' );
            }else{
                
                $cek_request  = $this->m_mo->cek_origin_di_stock_move($orgn)->row_array();//cek apa sudah request obat ?
                if(empty($cek_request['origin'])){

                    $cek_ba = $this->m_mo->cek_berat_air($kode)->row_array();
                    if($cek_ba['berat'] > 0 AND $cek_ba['air'] > 0 ){

                        //lock table
                        $this->_module->lock_tabel('warna WRITE, warna_items WRITE, mrp_route WRITE, stock_move WRITE, stock_move_produk WRITE, penerimaan_barang WRITE, penerimaan_barang_items WRITE, pengiriman_barang WRITE, pengiriman_barang_items WRITE, mrp_production_rm_target WRITE, mrp_production WRITE, log_history WRITE, user WRITE, main_menu_sub WRITE, mst_produk WRITE');
                        
                        //cek_status= cek_status_warna;status=ready,request,done
                        $cek_status = $this->m_mo->cek_status_warna($warna)->row_array();
                
                        if(!empty($cek_status['status'])){
                            $last_move   = $this->_module->get_kode_stock_move();
                            $move_id     = "SM".$last_move; //Set kode stock_move
                            $source_move = "";
                            $tgl         = date("Y-m-d H:i:s");
                            $i           = 1;
                            $sql_stock_move_batch       = "";
                            $sql_stock_move_produk_batch= "";
                            $sql_out_batch              = "";
                            $sql_out_items_batch        = ""; 
                            $sql_in_batch               = "";
                            $sql_in_items_batch         = "";
                            $case                       = "";
                            $where                      = "";
                            $case2                      = "";
                            $where2                     = "";
                            $case3                      = "";
                            $where3                     = "";
                            $case4                      = "";
                            $where4                     = "";
                            $sql_rm_target_batch        = "";
                            $arr_kode[]                 = "";
                            $kode_out[]                 = "";
                            $sql_log_history_in         = "";
                            $sql_log_history_out        = "";

                            if($deptid == 'DYE-R'){
                                $route = $this->m_mo->get_route_warna('obat_dyeing_reproses');
                            } else {
                                $route = $this->m_mo->get_route_warna('obat_dyeing');
                            }
                            $kode_warna  = $this->m_mo->get_warna_items_by_warna($warna,$varian);
                            $get_row = $this->m_mo->get_row_order_rm_target($kode)->row_array();//get last_order di mrp rm target
                            //$rm_row  = $get_row['row']+1;
                            $rm_row   = 1;
                            $ba      = $this->m_mo->get_berat_air_by_kode($kode)->row_array();
                            $sm_row  = 1;///stock move row_order
                            $empty_item       = TRUE;
                            $produk_aktif     = TRUE;
                            $route_request    = FALSE;

                            // cek produk dti aktif/tidak 
                            foreach($kode_warna as $row){
                                $stat_produk = $this->_module->get_status_aktif_by_produk(addslashes($row->kode_produk))->row_array();// status produk aktif/tidak
                                $empty_item = FALSE;
                                if($stat_produk['status_produk']!= 't'){
                                    $produk_aktif     = FALSE;
                                    break;
                                }
                            }
                            
                            if($produk_aktif == TRUE AND $empty_item == FALSE){
                                
                                foreach($route as $val){
                                    $route_request = TRUE;
                                    $mthd          = explode("|",$val->method);
                                    $method_dept   = trim($mthd[0]);
                                    $method_action = trim($mthd[1]);
                                    $smp_row    = 1;//stock move produk row_order

                                    //stock move 
                                    $origin = $orgn;
                                    $sql_stock_move_batch .= "('".$move_id."','".$tgl."','".$origin."','".$val->method."','".$val->lokasi_dari."','".$val->lokasi_tujuan."','draft','".$sm_row."','".$source_move."'), ";
                                    $sm_row = $sm_row + 1;
                                    

                                    if($method_action == 'OUT'){//pengiriman barang

                                        if($i=="1"){
                                        $arr_kode[$val->method]= $this->_module->get_kode_pengiriman($method_dept);
                                        }else{
                                        $arr_kode[$val->method]= $arr_kode[$val->method] + 1;
                                        }
                                        $dgt=substr("00000" . $arr_kode[$val->method],-5);            
                                        $kode_out = $method_dept."/".$method_action."/".date("y").  date("m").$dgt;
                                    
                                        //pengiriman barang
                                        $sql_out_batch  .= "('".$kode_out."','".$tgl."','".$tgl."','".$tgl."','','draft','".$method_dept."','".$origin."','".$move_id."','".$val->lokasi_dari."','".$val->lokasi_tujuan."'), ";
                                    
                                        //source move 
                                        $source_move = $move_id;

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
                                        $sql_log_history_out .= "('".$date_log."','".$mms_kode."','".$kode_out."','create','".$note_log."','".$nama_user."'), ";

                                        //upddate pengiriman reff picking
                                        $reff_picking_out = $kode_out."|".$deptid;
                                        $case4  .= "when kode = '".$kode_out."' then '".$reff_picking_out."'";
                                        $where4 .= "'".$kode_out."',";
                            

                                    }else if($method_action =='IN'){//penerimaan barang

                                        if($i=="1"){
                                        $arr_kode[$val->method]= $this->_module->get_kode_penerimaan($method_dept);
                                        }else{
                                        $arr_kode[$val->method]= $arr_kode[$val->method] + 1;
                                        }
                                        $dgt     = substr("00000" . $arr_kode[$val->method],-5);            
                                        $kode_in = $method_dept."/".$method_action."/".date("y").  date("m").$dgt;
                                    
                                        //penerimaan barang 
                                        $reff_picking_in = $kode_out."|".$kode_in;
                                        $sql_in_batch   .= "('".$kode_in."','".$tgl."','".$tgl."','".$tgl."','','draft','".$method_dept."','".$origin."','".$move_id."','".$reff_picking_in."','".$val->lokasi_dari."','".$val->lokasi_tujuan."'), "; 

                                        //upddate pengiriman
                                        $reff_picking_out = $kode_out."|".$kode_in;
                                        $case  .= "when kode = '".$kode_out."' then '".$reff_picking_out."'";
                                        $where .= "'".$kode_out."',";
                                        $kode_out    = "";

                                        //source move 
                                        $source_move = $move_id;

                                        //get mms kode berdasarkan dept_id
                                        $mms = $this->_module->get_kode_sub_menu_deptid('penerimaanbarang',$method_dept)->row_array();
                                        if(!empty($mms['kode'])){
                                            $mms_kode = $mms['kode'];
                                        }else{
                                            $mms_kode = '';
                                        }

                                        //create log history penerimaan_barang
                                        $note_log = $kode_in.' | '.$origin;
                                        $date_log = date('Y-m-d H:i:s');
                                        $sql_log_history_in .= "('".$date_log."','".$mms_kode."','".$kode_in."','create','".addslashes($note_log)."','".$nama_user."'), ";
                                    }

                                    $last_num_origin = 1;

                                    foreach($kode_warna as $row){
                                        $empty_item = FALSE;

                                        $kode_prod  = addslashes($row->kode_produk);
                                        $nama_prod  = addslashes($row->nama_produk);
                                        $qty        = $row->qty;
                                        $uom        = $row->uom;
                                        $reff_notes = addslashes($row->reff_note);
                                        $type_obat  = $row->type_obat;

                                        if($type_obat =='DYE'){
                                            $qty_asli  = $qty*$ba['berat']*10;
                                        }else if($type_obat == 'AUX'){
                                            $qty_asli  = $qty*$ba['air'];
                                        }

                                        if($method_action =='CON'){
                                            $origin_prod = $kode_prod.'_'.$last_num_origin;
                                        }else{
                                            $origin_prod = $kode_prod.'_'.$last_num_origin;
                                            //$origin_prod = '';
                                        }

                                        //stock move produk
                                        $sql_stock_move_produk_batch .= "('".$move_id."','".$kode_prod."','".$nama_prod."','".$qty_asli."','".$uom."','draft','".$smp_row."','".$origin_prod."'), ";

                                        if($method_action == 'OUT'){//pengiriman barang

                                            $sql_out_items_batch .= "('".$kode_out."','".$kode_prod."','".$nama_prod."','".$qty_asli."','".$uom."','draft','".$smp_row."','".$origin_prod."'), ";
                                            $last_num_origin = $last_num_origin + 1;

                                            //update reff notes pengiriman 
                                            $case2  .= "when kode = '".$kode_out."' then '".$reff_notes."'";
                                            $where2 .= "'".$kode_out."',";
                                        
                                        }else if($method_action =='IN'){//penerimaan barang
                                        
                                            $sql_in_items_batch   .= "('".$kode_in."','".$kode_prod."','".$nama_prod."','".$qty_asli."','".$uom."','draft','".$smp_row."'), "; 
                                            //update reff notes penerimaan
                                            $case3 .= "when kode = '".$kode_in."' then '".$reff_notes."'";
                                            $where3 .= "'".$kode_in."',";

                                        }else if($method_action =='CON'){

                                            $sql_rm_target_batch  .= "('".$kode."','".$move_id."','".$kode_prod."','".$nama_prod."','".$qty_asli."','".$uom."','".$rm_row."','".$origin_prod."','draft','".$qty."','".$reff_notes."'), ";
                                            //rm + 1
                                            $rm_row =  $rm_row  + 1;
                                            $last_num_origin = $last_num_origin + 1;
                                        }

                                        //smp row_order + 1
                                        $smp_row = $smp_row + 1;

                                    }
                                    //move id + 1
                                    $last_move = $last_move + 1;
                                    $move_id   = "SM".$last_move;
                                    //$i=$i+1;

                                } // end foreach route
                            }

                            if($route_request == FALSE){
                                //action sql query
                                $callback = array('message' => 'Maaf, Route Request Obat Dyeing tidak ditemukan   ! ',  'status' => 'failed' );
                            }else if($produk_aktif == FALSE){
                                //action sql query
                                $callback = array('message' => 'Maaf, Obat Dyeing Stuff atau Auxiliary tidak atkif  ! ',  'status' => 'failed' );
                            }else if($empty_item == TRUE){
                                //action sql query
                                $callback = array('message' => 'Maaf, Resep Obat Dyeing Stuff atau Auxiliary masih belum tersedia ! ',  'status' => 'failed' );

                            }else{

                                //action sql query
                                if(!empty($sql_stock_move_batch)){
                                  $sql_stock_move_batch = rtrim($sql_stock_move_batch, ', ');
                                  $this->_module->create_stock_move_batch($sql_stock_move_batch);

                                  if(!empty($sql_stock_move_produk_batch)){
                                      $sql_stock_move_produk_batch = rtrim($sql_stock_move_produk_batch, ', ');
                                      $this->_module->create_stock_move_produk_batch($sql_stock_move_produk_batch);
                                  }
                                }

                                if(!empty($sql_out_batch)){
                                  $sql_out_batch = rtrim($sql_out_batch, ', ');
                                  $this->_module->simpan_pengiriman_batch($sql_out_batch);

                                  $sql_out_items_batch = rtrim($sql_out_items_batch, ', ');
                                  $this->_module->simpan_pengiriman_items_batch($sql_out_items_batch);
                                  
                                  $sql_log_history_out = rtrim($sql_log_history_out, ', ');
                                  $this->_module->simpan_log_history_batch($sql_log_history_out);

                                  $where4 = rtrim($where4, ',');
                                  $sql_update_reff_picking_out_batch  = "UPDATE pengiriman_barang SET reff_picking =(case ".$case4." end) WHERE  kode in (".$where4.") ";
                                  $this->_module->update_reff_batch($sql_update_reff_picking_out_batch);
                                  $sql_update_reff_picking_out_batch = "";
                                }
                                
                                if(!empty($sql_in_batch)){
                                  $sql_in_batch = rtrim($sql_in_batch, ', ');
                                  $this->_module->simpan_penerimaan_batch($sql_in_batch);

                                  $sql_in_items_batch = rtrim($sql_in_items_batch, ', ');
                                  $this->_module->simpan_penerimaan_items_batch($sql_in_items_batch);
                                    
                                  $where = rtrim($where, ',');
                                  $sql_update_reff_out_batch  = "UPDATE pengiriman_barang SET reff_picking =(case ".$case." end) WHERE  kode in (".$where.") ";
                                  $this->_module->update_reff_batch($sql_update_reff_out_batch);

                                  $where2 = rtrim($where2, ',');
                                  $sql_update_reff_notes_out_batch  = "UPDATE pengiriman_barang SET reff_note =(case ".$case2." end) WHERE  kode in (".$where2.") ";
                                  $this->_module->update_reff_batch($sql_update_reff_notes_out_batch);

                                  $where3 = rtrim($where3, ',');
                                  $sql_update_reff_notes_in_batch  = "UPDATE penerimaan_barang SET reff_note =(case ".$case3." end) WHERE  kode in (".$where3.") ";
                                  $this->_module->update_reff_batch($sql_update_reff_notes_in_batch);

                                  $sql_log_history_in = rtrim($sql_log_history_in, ', ');
                                  $this->_module->simpan_log_history_batch($sql_log_history_in);

                                }

                                if(!empty($sql_rm_target_batch)){
                                    $sql_rm_target_batch = rtrim($sql_rm_target_batch, ', ');
                                   $this->m_mo->save_obat($sql_rm_target_batch);
                                }

                                $this->m_lab->update_status_warna($warna,'requested');

                                // get nama warna by id
                                $nama_warna  = $this->m_mo->get_nama_warna_by_id($warna);

                                $sql_update_mrp_warna_varian  = "UPDATE mrp_production SET id_warna_varian ='$varian' WHERE  kode = '$kode' ";
                                $this->_module->update_reff_batch($sql_update_mrp_warna_varian);

                                //unlock table
                                $this->_module->unlock_tabel();
                                
                                $jenis_log   = "edit";
                                $note_log    = "Request Resep Obat -> ".$kode." | ".$nama_warna ;
                                $this->_module->gen_history_deptid($sub_menu, $kode, $jenis_log, $note_log, $username, $deptid);
                                
                                $callback    = array('status'=>'success', 'message' => 'Request Resep Obat Berhasil !',  'icon' =>'fa fa-check', 'type' => 'success');
                            }

                        }else{
                            $callback = array('message' => 'Maaf, Resep Obat Warna Belum ready !'.$cek_status['status'],  'status' => 'failed' );
                        }

                    }else{
                        if($cek_ba['berat'] <= 0){
                          $callback = array('message' => 'Maaf, Berat Harus Diisi !',  'status' => 'failed' );
                        }else if($cek_ba['air'] <= 0){
                          $callback = array('message' => 'Maaf, Air Harus Diisi !',  'status' => 'failed' );
                        }else{
                          $callback = array('message' => 'Maaf, Air Harus Diisi !1'.$cek_ba['berat'].' '.$cek_ba['air'],  'status' => 'failed' );
                        }
                    }


                }else{
                    $callback = array('message' => 'Maaf, Anda sudah melakukan Request Resep Obat !',  'status' => 'failed' );
                }

            
            }

        }

        echo json_encode($callback);
    }
    

    public function simpan()
    {

        if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
            // session habis
            $callback = array('message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
        }else{

            $sub_menu = $this->uri->segment(2);
            $username = addslashes($this->session->userdata('username')); 
            $deptid   = $this->input->post('deptid');

            $kode   = $this->input->post('kode');
            $berat  = $this->input->post('berat');
            $air    = $this->input->post('air');
            $start  = $this->input->post('start');
            $finish = $this->input->post('finish');
            $reff_note   = addslashes($this->input->post('reff_note'));
            $mesin       = addslashes($this->input->post('mesin'));
            $type_mo     = addslashes($this->input->post('type_mo'));
            $target_efisiensi   = $this->input->post('target_efisiensi');
            $qty1_std           = $this->input->post('qty1_std');
            $qty2_std           = $this->input->post('qty2_std');
            $type_production    = $this->input->post('type_production');
            $lot_prefix         = preg_replace('/\s/', '', addslashes($this->input->post('lot_prefix')));
            $lot_prefix_waste   = preg_replace('/\s/', '', $this->input->post('lot_prefix_waste'));
            $lebar_greige       = addslashes($this->input->post('lebar_greige'));
            $uom_lebar_greige   = addslashes($this->input->post('uom_lebar_greige'));
            $lebar_jadi         = addslashes($this->input->post('lebar_jadi'));
            $uom_lebar_jadi     = addslashes($this->input->post('uom_lebar_jadi'));
            $handling           = addslashes($this->input->post('handling'));
            $gramasi            = addslashes($this->input->post('gramasi'));
            $program            = addslashes($this->input->post('program'));
            $origin_mo          = addslashes($this->input->post('origin'));
            $alasan             = addslashes($this->input->post('alasan'));
            $show_lebar = $this->_module->cek_show_lebar_by_dept_id($deptid)->row_array();

            //cek status mrp_production = done
            $cek1  = $this->m_mo->cek_status_mrp_production($kode,'done')->row_array();
            //cek status mrp_production = cancel
            $cek2  = $this->m_mo->cek_status_mrp_production($kode,'cancel')->row_array();
            //cek status mrp_production = hold
            $cek3  = $this->m_mo->cek_status_mrp_production($kode,'hold')->row_array();

            $orgn_request    = $origin_mo."|".$kode; // ex SO18|CO7|2|OW210300001|MG210300004 origin request obat

            $cek_request  = $this->m_mo->cek_origin_di_stock_move($orgn_request)->row_array();//cek apa sudah request obat ?

            // cek qty berat dan air by
            $list            = $this->m_mo->get_data_by_code($kode);
            $berat_not_same  = 'false';
            $air_not_same    = 'false';

            if(!empty($cek_request['origin'])){
                if(number_format($list->berat,2) != number_format($berat,2)){
                    $berat_not_same  = 'true';
                }
                if(number_format($list->air,2) != number_format($air,2)){
                    $air_not_same  = 'true';
                }
            }

            if(!empty($cek1['status'])){
                $callback = array('status' => 'failed', 'message'=>'Maaf, Data Tidak Bisa Diubah, Status MO Sudah Done !', 'icon' => 'fa fa-warning', 'type'=>'danger');
            }else if(!empty($cek2['status'])){
                $callback = array('status' => 'failed', 'message'=>'Maaf, Data Tidak Bisa Diubah, Status MO Batal !', 'icon' => 'fa fa-warning', 'type'=>'danger');
            }else if(!empty($cek3['status'])){
                $callback = array('status' => 'failed', 'message'=>'Maaf, Data Tidak Bisa Diubah, Status MO di Hold !', 'icon' => 'fa fa-warning', 'type'=>'danger');
            }else{

                if(($air == '0' OR empty($air))  AND $type_mo == 'colouring' AND ($deptid == 'FIN' OR $deptid =='DYE' OR $deptid == 'FIN-R' OR $deptid == 'DYE-R')){
                    if($air == '0'){
                        $callback = array('status' => 'failed', 'field' => 'air', 'message' => 'Air Harus Lebih dari 0 !', 'icon' =>'fa fa-warning',   'type' => 'danger' );    
                    }else{
                        $callback = array('status' => 'failed', 'field' => 'air', 'message' => 'Air Harus Diisi !', 'icon' =>'fa fa-warning',    'type' => 'danger' );    
                    }
                }else if(($berat == '0' OR empty($berat)) AND $type_mo == 'colouring' AND ($deptid == 'FIN' OR $deptid =='DYE' OR $deptid == 'FIN-R' OR $deptid == 'DYE-R')){
                    if($berat == '0'){
                        $callback = array('status' => 'failed', 'field' => 'berat', 'message' => 'Berat Harus Lebih dari 0 !', 'icon'=>'fa fa-warning','type' => 'danger' );    
                    }else{
                        $callback = array('status' => 'failed', 'field' => 'berat', 'message' => 'Berat Harus Diisi !', 'icon' =>'fa fa-warning',    'type' => 'danger' );    
                    }

                }else if(empty($start)){
                    $callback = array('status' => 'failed', 'field' => 'start', 'message' => 'Start Time Harus Diisi !', 'icon' =>'fa fa-warning',    'type' => 'danger' );    
                }else if(empty($finish)){
                    $callback = array('status' => 'failed', 'field' => 'finish', 'message' => 'Finish Time Harus Diisi !', 'icon' =>'fa fa-warning',    'type' => 'danger' );    
                }else if(empty($reff_note)){
                    $callback = array('status' => 'failed', 'field' => 'note', 'message' => 'Reff Note Harus Diisi !', 'icon' =>'fa fa-warning',    'type' => 'danger' );    
                }else if(empty($mesin)){
                     $callback = array('status' => 'failed', 'field' => 'mc', 'message' => 'No Mesin Harus Diisi !', 'icon' =>'fa fa-warning',    'type' => 'danger' );    
                }else if(empty($type_production) AND $type_mo == 'manufaktur'){
                    $callback = array('status' => 'failed', 'field' => 'type_production', 'message' => 'Type Production Harus Diisi !', 'icon' =>'fa fa-warning',    'type' => 'danger' );    
                }else if(!empty($cek_request['origin'])  AND $type_mo == 'colouring' AND $air_not_same == 'true'){
                    $callback = array('status' => 'failed', 'field' => 'berat', 'message' => 'Air  tidak bisa dirubah, karena sudah Request Resep !', 'icon' =>'fa fa-warning',    'type' => 'danger' );    
                }else if(!empty($cek_request['origin'])  AND $type_mo == 'colouring' AND $berat_not_same == 'true'){
                    $callback = array('status' => 'failed', 'field' => 'berat', 'message' => 'Berat  tidak bisa dirubah, karena sudah Request Resep !', 'icon' =>'fa fa-warning',    'type' => 'danger' );    
                }else{

                    if($deptid == 'TRI' OR $deptid == 'JAC'){
                        $lot_prefix      = '';
                        $lot_prefix_waste = '';
                    }

                    $this->m_mo->update_mo($kode,$berat,$air,$start,$finish,$reff_note,$mesin,$qty1_std,$qty2_std,$lot_prefix,$lot_prefix_waste,$target_efisiensi,$lebar_greige,$uom_lebar_greige,$lebar_jadi,$uom_lebar_jadi,$type_production,$handling,$gramasi,$program,$alasan);
                    
                    if($show_lebar['show_lebar'] == 'true'){
                        $lebar = $lebar_greige."  ".$uom_lebar_greige." | ".$lebar_jadi."  ".$uom_lebar_jadi." | ";
                    }else{
                        $lebar = '';
                    }

                    if($deptid == 'TRI' OR $deptid == 'JAC'){
                        $lot_prefix = 'Format Lot Prefix Default System';
                        $lot_prefix_waste = 'Format Lot Prefix Waste Default System';
                    }
                    
                    $mc = $this->m_mo->get_nama_mesin_by_kode($mesin)->row_array();
                    $nama_mesin = $mc['nama_mesin'];
                    
                    $jenis_log   = "edit";
                    if($type_mo == 'colouring'){                    
                        $note_log    = "-> ".$lebar." | ".$berat." | ".$air." | ".$handling." | ".$gramasi." | ".$program." | ".$finish." | ".$start." | ".$reff_note." | ".$nama_mesin." | ".$target_efisiensi." | ".$qty1_std." | ".$qty2_std." | ".$type_production." | ".$lot_prefix." | ".$lot_prefix_waste." | ".$alasan ; 
                    }else{
                        $note_log    = "-> ".$lebar." ".$finish." | ".$start." | ".$reff_note." | ".$nama_mesin." | ".$target_efisiensi." | ".$qty1_std." | ".$qty2_std." | ".$type_production." | ".$lot_prefix." | ".$lot_prefix_waste." | ".$alasan ; 
                    }


                    $this->_module->gen_history_deptid($sub_menu, $kode, $jenis_log, $note_log, $username, $deptid);
                         
                    $callback    = array('status'=>'success', 'message' => 'Data Berhasil Disimpan !',  'icon' =>'fa fa-check', 'type' => 'success');
                }

            }// else cek cek


        }
        echo json_encode($callback);
    }


    public function cek_stok()
    {

        if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
            // session habis
            $callback = array('message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
        }else{
           
            $sub_menu = $this->uri->segment(2);
            $username = addslashes($this->session->userdata('username')); 
            $deptid   = $this->input->post('deptid');

            $kode       = $this->input->post('kode');
            //$move_id    = $this->input->post('move_id');//move_id rm_target
            $origin_mo  = $this->input->post('origin');
            $type_mo    = $this->input->post('type_mo');
            $lokasi_quant = $this->input->post('lokasi');//lokasi untuk stock_quant produk consumable
            $ex_orgn    = explode("|", $origin_mo);

            if($type_mo == 'colouring'){
                $origin  = $origin_mo;
            }else{
                $origin  = $ex_orgn[0].'|'.$ex_orgn[1].'|';
            }

            $status_brg = 'ready';
            $tgl        = date('Y-m-d H:i:s');
            $sql_stock_quant_batch      = "";
            $sql_stock_move_items_batch = "";
            $case   ="";
            $case_qty2 ="";
            $where  ="";
            $case2  ="";
            $where2 ="";
            $case3  ="";
            $where3 ="";
            $updt_consum = false;
            $case4  ="";
            $where4 ="";
            $case5  ="";
            $where5 ="";
            $where5_2 ="";
            $case6  ="";
            $where6 ="";
            $case7  ="";
            $where7 ="";

            $where_del1 ="";
            $where_del2 ="";

            $kurang        = false;
            $produk_kurang    = "";
            $kosong        = true;
            $produk_kosong    = "";
            $cukup         = false;          
            $produk_terpenuhi = "";
            $history       = false;     
            $bahan_baku    = false; 
            $history_split = false;


            //cek status done ?
            $cek1  = $this->m_mo->cek_status_mrp_production($kode,'done')->row_array();
            //cek status cancel ?
            $cek2  = $this->m_mo->cek_status_mrp_production($kode,'cancel')->row_array();
            //cek status hold ?
            $cek3  = $this->m_mo->cek_status_mrp_production($kode,'hold')->row_array();
            
            if(!empty($cek1['status'])){
                $callback = array('status' => 'failed', 'message'=>'Maaf, Tidak Bisa Cek Stok, Status MO Sudah Done !', 'icon' => 'fa fa-warning', 'type'=>'danger');
            }else if(!empty($cek2['status'])){
                $callback = array('status' => 'failed', 'message'=>'Maaf, Tidak Bisa Cek Stok, Status MO Batal !', 'icon' => 'fa fa-warning', 'type'=>'danger');
            }else if(!empty($cek3['status'])){
                $callback = array('status' => 'failed', 'message'=>'Maaf, Tidak Bisa Cek Stok, Status MO di Hold !', 'icon' => 'fa fa-warning', 'type'=>'danger');
            }else{
                    //lock tabel
                    $this->_module->lock_tabel('stock_quant WRITE, stock_move_items WRITE,stock_move WRITE,stock_move_produk WRITE, mrp_production WRITE, mrp_production_rm_target WRITE, mrp_production_rm_target rm WRITE, stock_move_items as smi WRITE, mst_produk as mp WRITE, mst_category as mc WRITE, mesin  WRITE'  );

                    //lokasi tujuan, lokasi dari
                    $lokasi = $this->m_mo->get_location_by_mo($kode)->row_array();

                    // looping move_id_rm yg stockable bukan Obat
                    $list_sm_rm = $this->m_mo->get_move_id_rm_target_by_kode($kode)->result();
                    foreach($list_sm_rm as $sm){
                    
                        $move_id    = $sm->move_id;
                        
                        //get row order stock_move_items
                        $row_order  = $this->_module->get_row_order_stock_move_items_by_kode($move_id);

                        $list  = $this->m_mo->get_list_bahan_baku_stok($kode,$move_id);//get list bahan baku yang type stockable
                        foreach ($list as $val) {
                            $bahan_baku  = true; 
                            $kode_produk = $val->kode_produk;
                            $nama_produk = $val->nama_produk;
                            $qty         = $val->qty;
                            $uom         = $val->uom;
                            $origin_prod = $val->origin_prod;
                            $move_id     = $val->move_id;

                            //get last quant id
                            $start = $this->_module->get_last_quant_id();
                        
                            //cek qty produk di stock_move_items apa masih kurang dengan target qty 
                            $qty_smi = $this->_module->get_qty_stock_move_items_mo_by_kode($move_id,addslashes($origin_prod),'')->row_array();
                            $kebutuhan_qty = $qty - $qty_smi['sum_qty'];

                            if($kebutuhan_qty > 0){//jika kebutuhan_qty > 0

                                $ceK_quant = $this->_module->get_cek_stok_quant_mo_by_prod(addslashes($kode_produk),$lokasi['source_location'],$origin)->result_array();
                                foreach ($ceK_quant as $stock) {
                                    $kosong = false;
                                    $history = true; 

                                    if(round($kebutuhan_qty,2) >= round($stock['qty'],2)){
                                        //jika kebutuhan_qty lebih atau sama dengan qty di stock_quant
                                        
                                        //update reserve_move dengan move_id
                                        $case2  .= "when quant_id = '".$stock['quant_id']."' then '".$move_id."'";
                                        $where2 .= "'".$stock['quant_id']."',"; 

                                        //insert stock move items batch
                                        $sql_stock_move_items_batch .= "('".$move_id."', '".$stock['quant_id']."','".addslashes($kode_produk)."', '".addslashes($nama_produk)."','".addslashes(trim($stock['lot']))."','".$stock['qty']."','".addslashes($uom)."','".$stock['qty2']."','".addslashes($stock['uom2'])."','".$status_brg."','".$row_order."','".addslashes($origin_prod)."', '".$tgl."','".addslashes($stock['lokasi_fisik'])."','".addslashes($stock['lebar_greige'])."','".addslashes($stock['uom_lebar_greige'])."','".addslashes($stock['lebar_jadi'])."','".addslashes($stock['uom_lebar_jadi'])."'), ";                  
                                        $row_order++;                                 
                                        $kebutuhan_qty = round($kebutuhan_qty,2) - round($stock['qty'],2);

                                    }else if(round($kebutuhan_qty,2) < round($stock['qty'],2)){

                                        //jika kebutuhan_qty kurang dari qty di stock_quant
                                    
                                        $qty_new = round($stock['qty'],2) - round($kebutuhan_qty,2);//qty baru di stock quant

                                        //update qty produk di stock_quant
                                        $case  .= "when quant_id = '".$stock['quant_id']."' then '".$qty_new."'";
                                        $where .= "'".$stock['quant_id']."',";

                                        $qty2_new = ($stock['qty2']/$stock['qty'])*$kebutuhan_qty;
                                        $qty2_update  = $stock['qty2'] - $qty2_new;
                                        $case_qty2 .= "when quant_id = '".$stock['quant_id']."' then '".$qty2_update."'";

                                        //insert qty stock_quant_batch dengan quant_id baru 
                                        $sql_stock_quant_batch .= "('".$start."','".$tgl."', '".addslashes($kode_produk)."', '".addslashes($nama_produk)."','".addslashes(trim($stock['lot']))."','".addslashes($stock['nama_grade'])."','".$kebutuhan_qty."','".addslashes($uom)."','".$qty2_new."','".addslashes($stock['uom2'])."','".$lokasi['source_location']."','".addslashes($stock['reff_note'])."','".$move_id."','".$origin_mo."','".$tgl."','".addslashes($stock['lebar_greige'])."','".addslashes($stock['uom_lebar_greige'])."','".addslashes($stock['lebar_jadi'])."','".addslashes($stock['uom_lebar_jadi'])."','".addslashes($stock['sales_order'])."','".addslashes($stock['sales_group'])."'), ";
                                        //insert stock move items batch
                                        $sql_stock_move_items_batch .= "('".$move_id."', '".$start."','".addslashes($kode_produk)."', '".addslashes($nama_produk)."','".addslashes(trim($stock['lot']))."','".$kebutuhan_qty."','".addslashes($uom)."','".$qty2_new."','".addslashes($stock['uom2'])."','".$status_brg."','".$row_order."','".addslashes($origin_prod)."', '".$tgl."','".addslashes($stock['lokasi_fisik'])."','".addslashes($stock['lebar_greige'])."','".addslashes($stock['uom_lebar_greige'])."','".addslashes($stock['lebar_jadi'])."','".addslashes($stock['uom_lebar_jadi'])."'), ";
                                        $row_order++;
                                        $start++;
                                        $kebutuhan_qty = 0;
                                    }

                                    //update status di mrp_production_rm_target dan stock_move_produk jadi ready
                                    $case3  .= "when origin_prod = '".addslashes($origin_prod)."' then '".$status_brg."'";
                                    $where3 .= "'".addslashes($origin_prod)."',";
                                    
                                    //untuk memotong proses looping ketika kebutuhan_qty == 0
                                    if($kebutuhan_qty == 0){
                                        break;
                                    } 

                                }//end foreach cek_quant

                                if($kebutuhan_qty > 0 AND $kosong == false){
                                    $kurang    = true;
                                    $produk_kurang .= $nama_produk.', ';
                                }
                                if($kosong == true){//jika qty di stock_quant_kosong/blm terisi
                                    $produk_kosong .= $nama_produk.', ';
                                }

                            }else{//jik kebutuhan_qty <= 0
                                    
                                if($kebutuhan_qty < 0){

                                    // get quant id by origin_prod , move_id, status = ready
                                    $sq = $this->m_mo->get_smi_produk_by_kode($move_id, $origin_prod, 'ready')->result_array();
                                    $qty_lebih = $qty_smi['sum_qty'] - $qty; // qty lebih dari yg dibutuhkan
                                    $ro = 1;
                                    $varbaru = "";
                                    $varbaru2 = "";
                                    foreach ($sq as $val) {
                                        $history_split = true;

                                        if(round($val['qty'],2) <= round($qty_lebih,2)){ 
                                            
                                            // reserve_move jadi kosong di tbl stock_quant
                                            $case6  .= "when quant_id = '".$val['quant_id']."' then '' ";
                                            $where6 .= "'".$val['quant_id']."',";

                                            // hapus stock_move_items by move_id, quant_id
                                            $where_del1 .= "'".$val['move_id']."',"; // move_id
                                            $where_del2 .= "'".$val['quant_id']."',"; // quant_id

                                            //update status di mrp_production_rm_target dan stock_move_produk 
                                            if(round($val['qty'],2) == round($qty_lebih,2)){ // jika sama maka status nya done
                                                $case7  .= "when origin_prod = '".addslashes($origin_prod)."' then 'done'";
                                            }else if(round($val['qty'],2) < round($qty_lebih,2)){// jika kurang maka status draft
                                                $case7  .= "when origin_prod = '".addslashes($origin_prod)."' then 'draft'";
                                            }
                                            $where7 .= "'".addslashes($origin_prod)."',";
                                            
                                            // jika qty smi sama atau kurang dari qty_lebih
                                            $qty_lebih = round($qty_lebih,2) - round($val['qty'],2);

                                        }else if(round($val['qty'],2) > round($qty_lebih,2)){ 
                                            // jika qty di smi lebih dari qty_lebih 

                                            $qty_new   = round($val['qty'],2) - round($qty_lebih,2); // untuk qty baru di smi dan stock_quant

                                            // update qty  stock_move item by move_id, quant_id
                                            $case5  .= "when quant_id = '".$val['quant_id']."' then '".$qty_new."'";
                                            $where5 .= "'".$val['quant_id']."',";
                                            $where5_2 .= "'".$move_id."',";

                                            //update qty produk di stock_quant
                                            $case  .= "when quant_id = '".$val['quant_id']."' then '".$qty_new."'";
                                            $where .= "'".$val['quant_id']."',";

                                            $cek_sq  = $this->_module->get_stock_quant_by_id($val['quant_id'])->row_array();
                                            $qty2_new = ($val['qty2']/$val['qty'])*$qty_lebih;

                                            //update qty2 di stock_quant lama dan stock_move_items
                                            $qty2_update = $val['qty2'] - $qty2_new;
                                            $case_qty2 = "when quant_id = '".$val['quant_id']."' then '".$qty2_update."'";

                                            //insert qty stock_quant_batch dengan quant_id baru 
                                            $sql_stock_quant_batch .= "('".$start."','".$tgl."', '".addslashes($cek_sq['kode_produk'])."', '".addslashes($cek_sq['nama_produk'])."','".addslashes(trim($cek_sq['lot']))."','".addslashes($cek_sq['nama_grade'])."','".$qty_lebih."','".addslashes($cek_sq['uom'])."','".$qty2_new."','".addslashes($cek_sq['uom2'])."','".$cek_sq['lokasi']."','".addslashes($cek_sq['reff_note'])."','','".$cek_sq['reserve_origin']."','".$tgl."','".addslashes($cek_sq['lebar_greige'])."','".addslashes($cek_sq['uom_lebar_greige'])."','".addslashes($cek_sq['lebar_jadi'])."','".addslashes($cek_sq['uom_lebar_jadi'])."','".addslashes($cek_sq['sales_order'])."','".addslashes($cek_sq['sales_group'])."'), ";
                                            $start++;

                                            $qty_lebih = 0;

                                        }

                                        if($qty_lebih == 0){
                                            break; // keluar looping
                                        }

                                        $ro++;

                                    }

                                }else{ // kebutuhan_qty == 0
                                    $cukup = true;
                                    $produk_terpenuhi .= $nama_produk.', ';
                                }

                            }


                            if(!empty($sql_stock_quant_batch) ){
                                $sql_stock_quant_batch = rtrim($sql_stock_quant_batch, ', ');
                                $this->_module->simpan_stock_quant_batch($sql_stock_quant_batch);
                                $sql_stock_quant_batch = "";
                            }

                            if(!empty($sql_stock_move_items_batch)){
                                $sql_stock_move_items_batch = rtrim($sql_stock_move_items_batch, ', ');
                                $this->_module->simpan_stock_move_items_batch($sql_stock_move_items_batch);
                                $sql_stock_move_items_batch="";
                            }
    
                            //update reserve_move di stock_quant
                            if(!empty($where2) AND !empty($case2)){
                                $where2 = rtrim($where2, ',');
                                $sql_update_reserve_move  = "UPDATE stock_quant SET reserve_move =(case ".$case2." end) WHERE  quant_id in (".$where2.") ";
                                $this->_module->update_perbatch($sql_update_reserve_move);
                                $where2 = "";
                                $case2  = "";
                            }
                        
                            //update qty baru di stock quant 
                            if(!empty($where) AND !empty($case)){
                                $where = rtrim($where, ',');
                                $sql_update_qty_stock  = "UPDATE stock_quant SET qty =(case ".$case." end), qty2 =(case ".$case_qty2." end) WHERE  quant_id in (".$where.") ";
                                $this->_module->update_perbatch($sql_update_qty_stock);
                                $where = "";
                                $case  = "";
                            }

                            if(!empty($where3) AND !empty($case3)){
                                $where3 = rtrim($where3, ',');
                                $sql_update_status_rm_target = "UPDATE mrp_production_rm_target SET status =(case ".$case3." end) WHERE  origin_prod in (".$where3.") AND kode = '".$kode."' AND move_id = '".$move_id."' ";
                                $this->_module->update_perbatch($sql_update_status_rm_target);

                                $sql_update_status_stock_move_produk = "UPDATE stock_move_produk SET status =(case ".$case3." end) WHERE  origin_prod in (".$where3.") AND move_id = '".$move_id."' ";
                                $this->_module->update_perbatch($sql_update_status_stock_move_produk);
                                $where3 = "";
                                $case3  = "";
                                $sql_update_status_rm_target         = "";
                                $sql_update_status_stock_move_produk = "";
                                $case_qty2 = "";
                            }


                            //update qty dan qty2 di stock_move_items
                            if(!empty($where5) AND !empty($case5)){
                                $where5 = rtrim($where5, ',');
                                $where5_2 = rtrim($where5_2, ',');
                                $sql_update_qty_smi = "UPDATE stock_move_items set qty = (case ".$case5." end), qty2 = (case ".$case_qty2." end) WHERE quant_id IN (".$where5.") AND move_id IN (".$where5_2.") ";
                                $this->_module->update_perbatch($sql_update_qty_smi);
                                $case   = "";
                                $where5 = "";
                                $where5_2 = "";
                            }

                            // update reserve_move stock_quant
                            if(!empty($where6) AND !empty($case6)){
                                $where6  = rtrim($where6, ', ');
                                $sql_update_reserve_move  = "UPDATE stock_quant SET reserve_move =(case ".$case6." end) WHERE  quant_id in (".$where6.") ";
                                $this->_module->update_perbatch($sql_update_reserve_move);
                                $case6 = "";
                                $where6 = "";
                            }

                            // update status done/draft
                            if(!empty($where7) AND !empty($case7)){
                                $where7 = rtrim($where7, ',');
                                // $sql_update_status_rm_target = "UPDATE mrp_production_rm_target SET status =(case ".$case7." end) WHERE  origin_prod in (".$where7.") AND kode = '".$kode."' AND move_id = '".$move_id."' ";
                                // $this->_module->update_perbatch($sql_update_status_rm_target);

                                // $sql_update_status_stock_move_produk = "UPDATE stock_move_produk SET status =(case ".$case7." end) WHERE  origin_prod in (".$where7.") AND move_id = '".$move_id."' ";
                                // $this->_module->update_perbatch($sql_update_status_stock_move_produk);
                                $case7 = "";
                                $case7  = "";
                                $sql_update_status_rm_target         = "";
                                $sql_update_status_stock_move_produk ="";
                            }

                            // delete stock_move_items
                            if(!empty($where_del1) AND !empty($where_del2)){
                                $where_del1 = rtrim($where_del1, ',');
                                $where_del2 = rtrim($where_del2, ',');

                                $sql_delete_smi = "DELETE FROM stock_move_items WHERE move_id IN (".$where_del1.") AND quant_id IN (".$where_del2.") ";
                                $this->_module->update_perbatch($sql_delete_smi);
                                $where_del1 = "";
                                $where_del2 = "";
                            }

                            $kosong = true;

                        }// end foreach list mrp_production_rm_target

                    }

                    $note_update_mc = '';
                    // jika mo Dyeing maka update field berat
                    if($type_mo == 'colouring'){
                        $qty2   = $this->m_mo->get_qty2_smi_kain_by_kode($move_id)->row_array();
                        
                        //update berat di mrp production
                        $sql_update_berat = "UPDATE mrp_production set berat = '".$qty2['jml_qty2']."' WHERE kode = '".$kode."' ";
                        $this->_module->update_perbatch($sql_update_berat);

                        // update ketika mesin MG berjumlah 1
                        $jml_mc    = $this->m_mo->cek_mesin_by_dept_id($deptid)->num_rows();
                        $cek_mc_mg = $this->m_mo->cek_mesin_by_mrp($kode)->row_array();
                        if($jml_mc == 1 AND empty($cek_mc_mg['mc_id'])){
                            //update mrp
                            $get_mc = $this->m_mo->cek_mesin_by_dept_id($deptid)->row_array();
                            $sql_update_mesin_mg = "UPDATE mrp_production set mc_id = '".$get_mc['mc_id']."' WHERE kode = '".$kode."' ";
                            $this->_module->update_perbatch($sql_update_mesin_mg);
                            $note_update_mc = ' No Mesin Update -> '.$get_mc['nama_mesin'];
                        }
                    }

                    $sql_stock_quant_batch = "";
                    $sql_stock_move_items_batch="";

                    //get last quant id
                    $start = $this->_module->get_last_quant_id();
                    //get row order by mode id
                    $row_order  = $this->_module->get_row_order_stock_move_items_by_kode($move_id);

                    //update barang consumable jadi ready
                    //get list bahan baku yang type consumable yg status nya draft
                    $consum  = $this->m_mo->get_list_bahan_baku_cons($kode,'draft');
                    foreach ($consum as $val) {
                        $kode_produk = $val->kode_produk;
                        $nama_produk = $val->nama_produk;
                        $qty         = $val->qty;
                        $uom         = $val->uom;
                        $move_id     = $val->move_id;
                        $origin_prod = $val->origin_prod;

                        $updt_consum = true;
                        //update status produk consumable di mrp_production_rm_target dan stock_move_produk jadi ready
                        $case4  .= "when origin_prod = '".addslashes($origin_prod)."' then '".$status_brg."'";
                        $where4 .= "'".$origin_prod."',";

                        //insert stock_quant
                        $sql_stock_quant_batch .= "('".$start."','".$tgl."','".addslashes($kode_produk)."','".addslashes($nama_produk)."','','','".$qty."','".addslashes($uom)."','','','".$lokasi_quant."','','".$move_id."','".$origin_mo."','".$tgl."','','','','','',''), ";

                        //insert stock move items batch
                        $sql_stock_move_items_batch .= "('".$move_id."','".$start."','".addslashes($kode_produk)."','".addslashes($nama_produk)."','','".$qty."','".addslashes($uom)."','','','".$status_brg."','".$row_order."','".addslashes($origin_prod)."', '".$tgl."','','','','',''), ";
                        $start++;
                        $row_order++;
                    }


                    if(!empty($sql_stock_quant_batch) ){
                        $sql_stock_quant_batch = rtrim($sql_stock_quant_batch, ', ');
                        $this->_module->simpan_stock_quant_batch($sql_stock_quant_batch);
                    }

                    if(!empty($sql_stock_move_items_batch)){
                        $sql_stock_move_items_batch = rtrim($sql_stock_move_items_batch, ', ');
                        $this->_module->simpan_stock_move_items_batch($sql_stock_move_items_batch);
                    }

                    if(!empty($case4) AND !empty($where4)){
                        $where4 = rtrim($where4, ',');
                        $sql_update_status_rm_target = "UPDATE mrp_production_rm_target SET status =(case ".$case4." end) WHERE  origin_prod in (".$where4.") AND kode = '".$kode."' ";
                          $this->_module->update_perbatch($sql_update_status_rm_target);

                        $sql_update_status_stock_move_produk = "UPDATE stock_move_produk SET status =(case ".$case4." end) WHERE  origin_prod in (".$where4.") AND move_id = '".$move_id."' ";
                          $this->_module->update_perbatch($sql_update_status_stock_move_produk);
                    }

                   
                    if($type_mo == 'colouring' AND $deptid != 'DYE' AND $deptid != 'DYE-R'){
                        //cek apa ada product yang statusnya ready atau done ?
                        $all_produk_rm = $this->m_mo->cek_status_barang_mrp_production_rm_target($kode,'ready','done')->row_array();
                        //jika tidak ada maka update status  mrp_production = ready
                        if(!empty($all_produk_rm['status'])){
                            $this->m_mo->update_status_mrp_production($kode,$status_brg);
                            
                            $cek_status2 = $this->m_mo->cek_status_mrp_production($kode,'')->row_array();
                            if($cek_status2['status']=='ready'){
                                $this->_module->update_status_stock_move($move_id,$status_brg);
                            }
                        }
                    }
                    
                    //unlock table
                    $this->_module->unlock_tabel();

                    if($bahan_baku == false){
                        $callback = array('status' => 'failed', 'message'=>'Maaf, Konsumsi Bahan Kosong !', 'icon' => 'fa fa-warning', 'type'=>'danger');  

                    }else{

                        if(!empty($produk_kurang) AND !empty($produk_kosong)){
                            $callback = array('status' => 'failed', 'message'=> 
                                'Maaf, Qty Product "<b>'.  $produk_kurang  .'</b>" tidak mencukupi ! <br> Maaf, Qty Product "<b>'.  $produk_kosong  .'</b>" Kosong !', 'icon' => 'fa fa-warning', 'type'=>'danger');
                        }else if(!empty($produk_kosong)){
                            $callback = array('status' => 'failed', 'message'=> 
                                'Maaf, Qty Product "<b>'.  $produk_kosong  .'</b>" Kosong !', 'icon' => 'fa fa-warning', 'type'=>'danger');

                        }else if(!empty($produk_kurang) ){                       
                            $callback = array('status' => 'failed', 'message'=> 
                                'Maaf, Qty Product "<b>'.  $produk_kurang  .'</b>" tidak mencukupi !', 'icon' => 'fa fa-warning', 'type'=>'danger', 'status_kurang' => 'yes',  'message2'=>'Detail Product Berhasil Ditambahkan !', 'icon2' => 'fa fa-check', 'type2'=>'success');
                                                                          
                        /*
                        }else if(!empty($produk_terpenuhi)){
                            $callback = array('status' => 'failed', 'message'=> 
                                'Qty Product "'.  $produk_terpenuhi  .'" Sudah Terpenuhi !', 'icon' => 'fa fa-warning', 'type'=>'danger');
                        */

                        }else{

                            if(!empty($produk_terpenuhi)){
                                $callback = array('status' => 'success', 'message'=>'Details Product Berhasil Ditambahkan !', 'icon' => 'fa fa-check', 'type'=>'success', 'terpenuhi'=>"yes");   
                            }else{
                                $callback = array('status' => 'success', 'message'=>'Details Product Berhasil Ditambahkan !', 'icon' => 'fa fa-check', 'type'=>'success');   
                                                  
                            }
                        }
                    }

                    if($history == true or $updt_consum == true OR $history_split == true ){
                      $jenis_log   = "edit";
                      $note_log    = '';
                      if($history == true or $updt_consum == true ){
                        $note_log    = "Cek Stok ".$note_update_mc;
                      }
                      if($history_split == true){
                        $note_log    = "Cek Stok Split ".$note_update_mc;
                      }
                      if($history == true AND $history_split == true){
                        $note_log  = "Cek Stok dan Cek Stok Split ".$note_update_mc;
                      }
                      $this->_module->gen_history_deptid($sub_menu, $kode, $jenis_log, $note_log, $username,$deptid);
                    }else if(!empty($note_update_mc)){
                      $jenis_log   = "edit";
                      $note_log    = $note_update_mc;
                      $this->_module->gen_history_deptid($sub_menu, $kode, $jenis_log, $note_log, $username,$deptid);
                    }
               
            }//end if cek status mrp_production
        }
        echo json_encode($callback);
    }

    public function view_mo_quant_modal()
    {
        $move_id    = $this->input->post('move_id');
        $deptid     = $this->input->post('deptid');
        $origin_prod= $this->input->post('origin_prod');
        $kode_mo       = $this->input->post('kode'); //kode MO untu log history
        $kode_produk= $this->input->post('kode_produk');
        $nama_produk= $this->input->post('nama_produk');

        // cek priv akses menu
        $sub_menu           = $this->uri->segment(2);
        $username           = $this->session->userdata('username'); 
        $kode               = $this->_module->get_kode_sub_menu_deptid($sub_menu,$deptid)->row_array();
        $data['akses_menu'] = $this->_module->cek_priv_menu_by_user($username,$kode['kode'])->num_rows();

        // cek level akses by user
        $level_akses = $this->_module->get_level_akses_by_user($username)->row_array();
        $data['level']       = $level_akses['level'];
        
        $data['kode']        = $kode_mo;
        $data['deptid']      = $deptid;
        $data['kode_produk'] = $kode_produk;
        $data['nama_produk'] = $nama_produk;
        $data['origin_prod'] = $origin_prod;
        $data['move_id']     = $move_id;
        $data['quant']       = $this->m_mo->get_view_quant_by_kode($move_id,addslashes($origin_prod));
        $data['type_mo']  = $this->m_mo->cek_type_mo_by_dept_id($deptid)->row_array();
        return $this->load->view('modal/v_mo_quant_modal', $data);
    }

    public function get_body_view_quant_mo()
    {
        $origin_prod    = $this->input->post('origin_prod');
        $move_id        = $this->input->post('move_id');
        $quant          = $this->m_mo->get_view_quant_by_kode($move_id,addslashes($origin_prod));
        $dataRecord     = [];
        $total_qty       = 0;
        $total_qty2      = 0;
        foreach($quant as $val){
            $dataRecord[]  = array(
                                    "move_id"       => $val->move_id,
                                    "quant_id"      => $val->quant_id,
                                    "kode_produk"   => $val->kode_produk,
                                    "nama_produk"   => $val->nama_produk,
                                    "lot"           => $val->lot,
                                    "qty"           => number_format($val->qty,2),
                                    "uom"           => $val->uom,
                                    "qty2"           => number_format($val->qty2,2),
                                    "uom2"          => $val->uom2,
                                    "status"        => $val->status,
                                    "origin_prod"   => $val->origin_prod,
                                    "row_order"     => $val->row_order,
                                    "reff_note"     => $val->reff_note,

            );
            $total_qty = $total_qty + $val->qty;
            $total_qty2 = $total_qty2 + $val->qty2;
        }
        $callback = array('status' => 'success', 'message'=>'Data Berhasil di hapus !', 'icon' => 'fa fa-check', 'type'=>'success', 'items'=>$dataRecord, 'total_qty' => number_format($total_qty,2), 'total_qty2' => number_format($total_qty2,2));
        echo json_encode($callback);


    }


    public function view_mo_rm_hasil()
    {
        $kode        = $this->input->post('kode');
        $kode_produk = $this->input->post('kode_produk');
        $nama_produk = $this->input->post('nama_produk');
        $tipe        = $this->input->post('tipe'); // example add, rm
        $data['kode_produk'] = $kode_produk;
        $data['nama_produk'] = $nama_produk;
        if($tipe == 'add'){
            $data['list_rm_hasil'] = $this->m_mo->get_list_bahan_baku_hasil($kode,addslashes($kode_produk),'t');
        }else{
            $data['list_rm_hasil'] = $this->m_mo->get_list_bahan_baku_hasil($kode,addslashes($kode_produk),'f');
        }
        return $this->load->view('modal/v_mo_rm_hasil_modal', $data);
    }

    public function hapus_details_items_mo_batch()
    {

        if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
            // session habis
            $callback = array('status' => 'failed','message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
        }else{

            $sub_menu       = $this->uri->segment(2);
            $username       = addslashes($this->session->userdata('username')); 
            $nu             = $this->_module->get_nama_user($username)->row_array();
            $nama_user      = addslashes($nu['nama']);
            
            $deptid         = addslashes($this->input->post('deptid'));
            $kode           = addslashes($this->input->post('kode'));
            $items_arr      = json_decode($this->input->post('checkbox'),true);// quant_id,row_order, lot
            $move_id        = addslashes($this->input->post('move_id'));
            $kode_produk    = addslashes($this->input->post('kode_produk'));
            $origin_prod    = addslashes($this->input->post('origin_prod'));
            $status_brg     = 'draft';
            
            //lock tabel
            $this->_module->lock_tabel('stock_quant WRITE, stock_move WRITE,stock_move_items WRITE,stock_move_produk WRITE, mrp_production_rm_target WRITE,  mrp_production_rm_target rm WRITE, mrp_production WRITE, stock_move_items as smi WRITE, mst_produk as mp WRITE, mst_category as mc WRITE, mrp_production_fg_hasil WRITE, mrp_inlet WRITE' );


            //cek status mrp_production = done
            $cek1  = $this->m_mo->cek_status_mrp_production($kode,'done')->row_array();
            //cek status mrp_production = cancel
            $cek2  = $this->m_mo->cek_status_mrp_production($kode,'cancel')->row_array();
            //cek status mrp_production = hold
            $cek3  = $this->m_mo->cek_status_mrp_production($kode,'hold')->row_array();

            if(!empty($cek1['status'])){
                $callback = array('status' => 'failed', 'message'=>'Maaf, Data Tidak Bisa Dihapus, Status MO Sudah Done !', 'icon' => 'fa fa-warning', 'type'=>'danger');
                $this->_module->unlock_tabel();
            }else if(!empty($cek2['status'])){
                $callback = array('status' => 'failed', 'message'=>'Maaf, Data Tidak Bisa Dihapus, Status MO Batal !', 'icon' => 'fa fa-warning', 'type'=>'danger');
                $this->_module->unlock_tabel();
            }else if(!empty($cek3['status'])){
                $callback = array('status' => 'failed', 'message'=>'Maaf, Data Tidak Bisa Dihapus, Status MO di Hold !', 'icon' => 'fa fa-warning', 'type'=>'danger');
                $this->_module->unlock_tabel();
            }else{

                $delete_lot      = true;
                $list_lot_failed = '';
                $where           = '';
                $where2          = '';
                $note_log_produk = '';
                $no              = 1;
                $inlet           = false;
                $lot_inlet       = '';
                foreach($items_arr as $item){

                    $quant_id   = $item['quant_id'];
                    $row_order  = $item['row_order'];
                    $lot        = $item['lot'];
                    if($deptid == 'GJD'){
                        // cek quant_id di table mrp_inlet
                        $status = array('status' => 'cancel'); // status not i
                        $cek_inlet = $this->m_mo->cek_mrp_inlet_by_quant_id($quant_id,$status);

                        if($cek_inlet > 0){
                            $inlet = true;
                            $lot_inlet .= $lot.'<br> ';
                        }
                    }

                    // cek item row
                    $get_smi = $this->_module->get_stock_move_items_by_kode($move_id,$quant_id,$kode_produk,$row_order)->row_array();
                    if(empty($get_smi)){
                        $delete_lot      = false;
                        $list_lot_failed = $lot.'<br> ';

                        break;
                    }else{
                        $where   .= "'".$quant_id."',";
                        $where2  .= "'".$row_order."',";

                        $note_log_produk .=  '('.$no.') '.$get_smi['quant_id'].'|'.$get_smi['origin_prod'].' '.$get_smi['nama_produk'].' '.$get_smi['lot'].' '.$get_smi['qty'].' '.$get_smi['uom'].' '.$get_smi['qty2'].' '.$get_smi['uom2']." <br>";
                        $no++;
                    }

                }


                if($delete_lot == true AND $inlet == false){
                    
                    //delete stock move item dan update reserve move jadi kosong
                    if(!empty($where) AND !empty($where2)){

                        $where  = rtrim($where, ',');
                        $where2 = rtrim($where2, ',');
                        $sql_delete_smi  = "DELETE FROM stock_move_items WHERE  move_id = '$move_id' AND quant_id IN (".$where.") AND row_order IN (".$where2.") ";
                        $this->_module->update_perbatch($sql_delete_smi);

                        $sql_update_stock_quant = "UPDATE stock_quant set reserve_move = '' WHERE quant_id IN (".$where.") ";
                        $this->_module->update_perbatch($sql_update_stock_quant);

                        //get sum qty produk stock move items yg statusnya ready
                        $get_qty2  = $this->_module->get_qty_stock_move_items_mo_by_kode($move_id,$origin_prod,'ready')->row_array();

                         //update status draft jika qty di stock move items kosong
                        if(empty($get_qty2['sum_qty'])){
                            $this->m_mo->update_status_mrp_production_rm_target($kode,$origin_prod,$status_brg,$move_id);
                            $this->m_mo->update_status_stock_move_produk_mo($move_id,$origin_prod,$status_brg);
                        }

                        // jika mo Dyeing maka update field berat
                        if($deptid == 'DYE' || $deptid =='DYE-R'){
                            $qty2   = $this->m_mo->get_qty2_smi_kain_by_kode($move_id)->row_array();
                            
                            //update berat di mrp production
                            $sql_update_berat = "UPDATE mrp_production set berat = '".$qty2['jml_qty2']."' WHERE kode = '".$kode."' ";
                            $this->_module->update_perbatch($sql_update_berat);
                        }
                        
                        //cek apa ada ada produk yang statusnya ready atau done?
                        $cek_status = $this->m_mo->cek_status_barang_mrp_production_rm_target($kode,'ready', 'done')->row_array();
                        
                        //cek fg hasil
                        $cek_fg_hasil = $this->m_mo->cek_mrp_production_fg_hasil($kode)->num_rows();
                        
                        if(empty($cek_status['status']) AND $cek_fg_hasil == 0){
                            $this->m_mo->update_status_mrp_production($kode,$status_brg);
                         $cek_status2 = $this->m_mo->cek_status_mrp_production($kode,'')->row_array();
                         if($cek_status2['status']=='draft'){
                             $this->_module->update_status_stock_move($move_id,$status_brg);
                            }
                        }
                        
                        //unlock table
                        $this->_module->unlock_tabel();
                        
                        $jenis_log   = "cancel";
                        $note_log    = "Hapus Data Details -> <br> ".$note_log_produk;
                        $this->_module->gen_history_deptid($sub_menu, $kode, $jenis_log, addslashes($note_log), $username,$deptid);
                        
                        $callback = array('status' => 'success', 'message'=>'Data Berhasil di Hapus !', 'icon' => 'fa fa-check', 'type'=>'success');
                    }else{
                        //unlock table
                        $this->_module->unlock_tabel();
                        $callback = array('status' => 'failed', 'message'=>'Maaf, Product/Lot Tidak ditemukan !', 'icon' => 'fa fa-warning', 'type'=>'danger');
                    }
                    
                }else{

                    if($inlet){
                        $callback = array('status' => 'failed', 'message'=>'Maaf, Product/Lot tidak bisa dihapus karena sudah INLET ! <br> '.$lot_inlet, 'icon' => 'fa fa-warning', 'type'=>'danger');
                    }else{
                        $callback = array('status' => 'failed', 'message'=>'Maaf, Product/Lot Tidak ditemukan ! <br> '.$list_lot_failed, 'icon' => 'fa fa-warning', 'type'=>'danger');
                    }
                    //unlock table
                    $this->_module->unlock_tabel();
                    

                }

            }

            
        }

        echo  json_encode($callback);
    }

    public function hapus_details_items_mo()
    {

        if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
            // session habis
            $callback = array('status' => 'failed','message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
        }else{

            $sub_menu  = $this->uri->segment(2);
            $username  = addslashes($this->session->userdata('username')); 

            $deptid     = $this->input->post('deptid');
            $quant_id   = $this->input->post('quant_id');
            $row_order  = $this->input->post('row_order');
            $move_id    = $this->input->post('move_id');
            $origin_prod= $this->input->post('origin_prod');
            $kode_produk= $this->input->post('kode_produk');
            $kode       = $this->input->post('kode');
            $status_brg = 'draft';
            
            //lock tabel
            $this->_module->lock_tabel('stock_quant WRITE, stock_move WRITE,stock_move_items WRITE,stock_move_produk WRITE, mrp_production_rm_target WRITE,  mrp_production_rm_target rm WRITE, mrp_production WRITE, stock_move_items as smi WRITE, mst_produk as mp WRITE, mst_category as mc WRITE, mrp_production_fg_hasil WRITE' );
            
            //cek status mrp_production = done
            $cek1  = $this->m_mo->cek_status_mrp_production($kode,'done')->row_array();
            //cek status mrp_production = cancel
            $cek2  = $this->m_mo->cek_status_mrp_production($kode,'cancel')->row_array();
            //cek status mrp_production = hold
            $cek3  = $this->m_mo->cek_status_mrp_production($kode,'hold')->row_array();

            if(!empty($cek1['status'])){
                $callback = array('status' => 'failed', 'message'=>'Maaf, Data Tidak Bisa Dihapus, Status MO Sudah Done !', 'icon' => 'fa fa-warning', 'type'=>'danger');
                $this->_module->unlock_tabel();
            }else if(!empty($cek2['status'])){
                $callback = array('status' => 'failed', 'message'=>'Maaf, Data Tidak Bisa Dihapus, Status MO Batal !', 'icon' => 'fa fa-warning', 'type'=>'danger');
                $this->_module->unlock_tabel();
            }else if(!empty($cek3['status'])){
                $callback = array('status' => 'failed', 'message'=>'Maaf, Data Tidak Bisa Dihapus, Status MO di Hold !', 'icon' => 'fa fa-warning', 'type'=>'danger');
                $this->_module->unlock_tabel();
            }else{

                // cek item by row
                $get_smi = $this->_module->get_stock_move_items_by_kode($move_id,$quant_id,$kode_produk,$row_order)->row_array();
                if(empty($get_smi)){
                    $callback = array('status' => 'failed', 'message'=>'Maaf, Product/Lot Tidak ditemukan !', 'icon' => 'fa fa-warning', 'type'=>'danger');
                    //unlock table
                    $this->_module->unlock_tabel();
                }else{

                    //delete stock move item dan update reserve move jadi kosong
                    $this->_module->delete_details_items($move_id,$quant_id,$row_order);

                    //get sum qty produk stock move items yg statusnya ready
                    $get_qty2  = $this->_module->get_qty_stock_move_items_mo_by_kode($move_id,addslashes($origin_prod),'ready')->row_array();

                    //update status draft jika qty di stock move items kosong
                    if(empty($get_qty2['sum_qty'])){
                    $this->m_mo->update_status_mrp_production_rm_target($kode,addslashes($origin_prod),$status_brg,$move_id);
                    $this->m_mo->update_status_stock_move_produk_mo($move_id,addslashes($origin_prod),$status_brg);
                    }

                    // jika mo Dyeing maka update field berat
                    if($deptid == 'DYE' || $deptid == 'DYE-R'){
                        $qty2   = $this->m_mo->get_qty2_smi_kain_by_kode($move_id)->row_array();
                        
                        //update berat di mrp production
                        $sql_update_berat = "UPDATE mrp_production set berat = '".$qty2['jml_qty2']."' WHERE kode = '".$kode."' ";
                        $this->_module->update_perbatch($sql_update_berat);
                    }

                    //cek apa ada ada produk yang statusnya ready atau done?
                    $cek_status = $this->m_mo->cek_status_barang_mrp_production_rm_target($kode,'ready', 'done')->row_array();

                    //cek fg hasil
                    $cek_fg_hasil = $this->m_mo->cek_mrp_production_fg_hasil($kode)->num_rows();

                    if(empty($cek_status['status']) AND $cek_fg_hasil == 0){
                        $this->m_mo->update_status_mrp_production($kode,$status_brg);
                        $cek_status2 = $this->m_mo->cek_status_mrp_production($kode,'')->row_array();
                        if($cek_status2['status']=='draft'){
                            $this->_module->update_status_stock_move($move_id,$status_brg);
                        }
                    }
                    
                    //unlock table
                    $this->_module->unlock_tabel();

                    $note_log_produk  =  $get_smi['origin_prod'].' '.$get_smi['nama_produk'].' '.$get_smi['lot'].' '.$get_smi['qty'].' '.$get_smi['uom'].' '.$get_smi['qty2'].' '.$get_smi['uom2'];
                    
                    $jenis_log   = "cancel";
                    $note_log    = "Hapus Data Details -> <br> ". $quant_id.'|'.$note_log_produk;
                    $this->_module->gen_history_deptid($sub_menu, $kode, $jenis_log, addslashes($note_log), $username,$deptid);
                    
                    $callback = array('status' => 'success', 'message'=>'Data Berhasil di hapus !', 'icon' => 'fa fa-check', 'type'=>'success');
                }
            }
        }
        echo  json_encode($callback);
    }

    // fitur habis diproduksi di tutup
    // public function waste_details_items()
    // {

        //cehckbox view
        /* <input type="checkbox" class="checkItem" value="<?php echo $row->quant_id?>" data-valuetwo="<?php echo $row->row_order?>" data-valuetree="<?php echo $row->lot?>" data-toggle="tooltip" title="Pilih Waste Data"> */


        /* // fungsi  ajax simpan 
        $("#btn-waste-data").off("click").on("click",function(e) {
            //$("#btn-waste").unbind("click");
            e.preventDefault();
            var message      = 'Silahkan pilih Product/Lot terlebih dahulu !';
            var myCheckboxes = new Array();
            var deptid 		 = "<?php echo $deptid; ?>";//parsing data id dept untuk log history
            var kode   		 = "<?php echo $kode; ?>";//kode MO untuk log history
            var move_id   	 = "<?php echo $move_id; ?>";
            var origin_prod  = "<?php echo $origin_prod; ?>";
            var kode_produk  = "<?php echo $kode_produk; ?>";

            $(".checkItem:checked").each(function() {
                value2  = $(this).attr('data-valuetwo');
                value3  = $(this).attr('data-valuetree');
                myCheckboxes.push({
                                "quant_id" : $(this).val(),
                                "row_order": value2,
                                "lot"      : value3
                            });
            });
            
            countchek = myCheckboxes.length;
            if(countchek == 0){
                alert_modal_warning(message);
            }else{
                bootbox.confirm({
                message: "Apakah Anda yakin bahan baku ini Habis diproduksi ?",
                title: "<i class='glyphicon glyphicon-trash'></i> Habis Diproduksi !",
                buttons: {
                        confirm: {
                            label: 'Yes',
                            className: 'btn-primary btn-sm'
                        },
                        cancel: {
                            label: 'No',
                            className: 'btn-default btn-sm'
                        },
                },callback: function (result) {
                        if(result == true){
                            please_wait(function(){});
                            $('#btn-waste-data').button('loading');
                            $.ajax({
                                type: "POST",
                                url :'<?php echo base_url('manufacturing/mO/waste_details_items')?>',
                                dataType: 'JSON',
                                data    : { kode 		: kode, 
                                            deptid      : deptid,
                                            checkbox    : JSON.stringify(myCheckboxes),
                                            origin_prod : origin_prod,
                                            move_id 	: move_id,
                                            kode_produk : kode_produk,
                                        },
                                success: function(data){
                                    if(data.sesi=='habis'){
                                        //alert jika session habis
                                        alert_modal_warning(data.message);
                                        window.location.replace('../index');
                                        $('#btn-waste-data').button('reset');
                                        unblockUI( function(){});
                                    }else if(data.status == 'failed'){
                                        //var pesan = "Lot "+data.lot+ " Sudah diinput !"       
                                        alert_modal_warning(data.message);
                                        $('#btn-waste-data').button('reset');
                                        unblockUI( function(){});
                                    }else{
                                        $("#tab_1").load(location.href + " #tab_1");
                                        $("#tab_2").load(location.href + " #tab_2");
                                        $("#tab_2").load(location.href + " #tab_2");
                                        $("#status_bar").load(location.href + " #status_bar");
                                        $("#foot").load(location.href + " #foot");
                                        $('#view_data').modal('hide');
                                        $('#btn-tambah').button('reset');
                                        unblockUI( function(){
                                            setTimeout(function() { alert_notify(data.icon,data.message,data.type,function(){});}, 1000);
                                        });
                                    }
                                },error: function (xhr, ajaxOptions, thrownError) {
                                    alert(xhr.responseText);
                                    $('#btn-waste-data').button('reset');
                                    unblockUI( function(){});
                                }
                            });
                        }else{
                        }
                }
            });
            
            }
        }); */

       /*  if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
            // session habis
            $callback = array('status' => 'failed','message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
        }else{

            $sub_menu       = $this->uri->segment(2);
            $username       = addslashes($this->session->userdata('username')); 
            $nu             = $this->_module->get_nama_user($username)->row_array();
            $nama_user      = addslashes($nu['nama']);

            $deptid         = $this->input->post('deptid');
            $kode           = $this->input->post('kode');
            $items_arr      = json_decode($this->input->post('checkbox'),true);// quant_id,row_order
            $move_id        = $this->input->post('move_id');
            $kode_produk    = addslashes($this->input->post('kode_produk'));
            $origin_prod    = addslashes($this->input->post('origin_prod'));

            //lock tabel
            $this->_module->lock_tabel('stock_quant WRITE, stock_move WRITE,stock_move_items WRITE,stock_move_produk WRITE, mrp_production_rm_target WRITE,  mrp_production_rm_target rm WRITE, mrp_production_fg_target WRITE, mrp_production WRITE, stock_move_items as smi WRITE, mst_produk as mp WRITE, mrp_production_rm_hasil  WRITE, mrp_production_fg_hasil WRITE, log_history WRITE, user WRITE, main_menu_sub WRITE, sales_contract WRITE, departemen WRITE' );
            
            //cek status mrp_production = done
            $cek1  = $this->m_mo->cek_status_mrp_production($kode,'done')->row_array();
            //cek status mrp_production = cancel
            $cek2  = $this->m_mo->cek_status_mrp_production($kode,'cancel')->row_array();

            if(!empty($cek1['status'])){
                $callback = array('status' => 'failed', 'message'=>'Maaf, Data Tidak Bisa Dihapus, Status MO Sudah Done !', 'icon' => 'fa fa-warning', 'type'=>'danger');
                $this->_module->unlock_tabel();
            }else if(!empty($cek2['status'])){
                $callback = array('status' => 'failed', 'message'=>'Maaf, Data Tidak Bisa Dihapus, Status MO Batal !', 'icon' => 'fa fa-warning', 'type'=>'danger');
                $this->_module->unlock_tabel();
            }else if(empty($items_arr)){
                $callback = array('status' => 'failed', 'message'=>'Maaf, Product/Lot yang akan di Waste belum di pilih !', 'icon' => 'fa fa-warning', 'type'=>'danger');
                $this->_module->unlock_tabel();
            }else{

                $tgl                          = date("Y-m-d H:i:s");
                $status_done                  = 'done';
                $sql_mrp_production_fg_hasil  = "";
                $sql_stock_quant_batch        = "";
                $sql_stock_move_items_batch   = "";
                $sql_mrp_production_rm_hasil  = "";
                $case                         = "";
                $where                        = "";
                $case2                        = "";
                $where2                       = "";
                $case3                        = "";
                $where3                       = "";
                $where3_move_id               = "";
                $case4                        = "";
                $where4                       = "";
                $items_empty                  = FALSE;


                // get last quant i
                $start = $this->_module->get_last_quant_id();

                // lokasi tujuan rm 
                $lokasi_rm = $this->_module->get_location_by_move_id($move_id)->row_array();

                // get origin mo
                $origin_mo  = $this->m_mo->get_origin_mo_by_kode($kode);

                // get sales_group / mkt by sales_contract 
                $org_mo      = explode("|", $origin_mo);
                $org_mo_loop = 0;
                $sales_order = "";
                foreach($org_mo as $org_mos){
                    if($org_mo_loop == 0){
                        $sales_order = trim($org_mos);
                    }
                    $org_mo_loop++;
                }
                
                if(!empty($sales_order)){
                    $sales_group = $this->_module->get_sales_group_by_sales_order($sales_order);
                }else{
                    $sales_group = '';
                }


                // get move id fg 
                $move_fg  = $this->m_mo->get_move_id_fg_target_by_kode($kode)->row_array();
                $move_id_fg = $move_fg['move_id'];

                // get lokasi waste 
                $lokasi_waste = $this->m_mo->get_location_waste_by_deptid($deptid)->row_array();

                // get row_order fg hasil
                $row_order_smi  = $this->_module->get_row_order_stock_move_items_by_kode($move_id_fg);

                $get_ro         = $this->m_mo->get_row_order_fg_hasil($kode)->row_array();
                $row_order_fg   = $get_ro['row']+1;

                $get_ro = $this->m_mo->get_row_order_rm_hasil($kode)->row_array();
                $row_order_rm= $get_ro['row']+1;

                // get additional true false by kode
                $additional = $this->m_mo->get_additional_true_false_by_kode($kode,$move_id,$kode_produk,$origin_prod);

                // get qty rm
                $qty_rm    = $this->m_mo->get_qty_rm_by_kode($kode,$move_id,$kode_produk,$origin_prod);
                $jml_lot   = 0;
                $lot_empty = '';
                foreach($items_arr as $item){
                    $quant_id   = $item['quant_id'];
                    $row_order  = $item['row_order'];
                    $lot        = $item['lot'];

                    // get stock move items by kode
                    $get_smi = $this->_module->get_stock_move_items_by_kode($move_id,$quant_id,$kode_produk,$row_order)->row_array();

                    if(!empty($get_smi)){

                        $quant_id       = $get_smi['quant_id'];
                        $move_id_smi    = $get_smi['move_id'];
                        $kode_produk    = $get_smi['kode_produk'];
                        $nama_produk    = $get_smi['nama_produk'];
                        $lot            = $get_smi['lot'];
                        $qty            = $get_smi['qty'];
                        $uom            = $get_smi['uom'];
                        $qty2           = $get_smi['qty2'];
                        $uom2           = $get_smi['uom2'];
                        $origin_prod_smi= $get_smi['origin_prod'];
                        $reff_note      = '';
                        $lot_remark     = 'D|'.$lot;

                        // **** simpan Produk Waste **** \\

                        //simpan fg hasil
                        $sql_mrp_production_fg_hasil .= "('".$kode."','".$move_id_fg."','".$start."','".$tgl."','".addslashes($kode_produk)."','".addslashes($nama_produk)."','".addslashes(trim($lot_remark))."','','".$qty."','".addslashes($uom)."','".$qty2."','".addslashes($uom2)."','".$lokasi_waste['waste_location']."','".$nama_user."','".$row_order_fg."','','','','', '".addslashes($sales_order)."','".addslashes($sales_group)."'), ";

                        //simpan stock quant dengan quant_id baru              
                        $sql_stock_quant_batch .= "('".$start."','".$tgl."', '".addslashes($kode_produk)."', '".addslashes($nama_produk)."','".addslashes(trim($lot_remark))."','','".$qty."','".$uom."','".$qty2."','".addslashes($uom2)."','".$lokasi_waste['waste_location']."','".addslashes($reff_note)."','".$move_id_fg."','".$origin_mo."','".$tgl."','','','','', '".addslashes($sales_order)."','".addslashes($sales_group)."'), ";

                        //simpan stock move items produksi
                        $sql_stock_move_items_batch .= "('".$move_id_fg."', '".$start."','".addslashes($kode_produk)."', '".addslashes($nama_produk)."','".addslashes(trim($lot_remark))."','".$qty."','".addslashes($uom)."','".$qty2."','".addslashes($uom2)."','".$status_done."','".$row_order_smi."','','".$tgl."','','','','',''), ";

                        $start++;
                        $row_order_fg++;
                        $row_order_smi++;


                        // *** Simpan Waste yng dikonsumsi *** \\

                        $case   .= "when quant_id = '".$quant_id."' then '".$lokasi_rm['lokasi_tujuan']."'"; //update lokasi
                        $where  .= "'".$quant_id."',";

                        $case2   .= "when quant_id = '".$quant_id."' then '".$origin_mo."'"; //update reserve_origin
                        $where2  .= "'".$quant_id."',";

                        $case3   .= "when quant_id = '".$quant_id."' then '".$status_done."'"; //update status done move items
                        $where3  .= "'".$quant_id."',";
                        $where3_move_id  .= "'".$move_id_smi."',";


                        $sql_mrp_production_rm_hasil .= "('".$kode."','".$move_id_smi."','".addslashes($kode_produk)."','".addslashes($nama_produk)."','".addslashes($lot)."','".$qty."','".addslashes($uom)."','".addslashes($origin_prod_smi)."','".$row_order_rm."','".$quant_id."','".$additional."'), ";
                        $row_order_rm++;
                        
                        $jml_lot++;
                    }else{// end if get_smi
                        $lot_empty      .= $lot.',';
                        $items_empty    = TRUE;
                    }
                }// end foreach 

                if($items_empty == FALSE){

                    if(!empty($sql_mrp_production_fg_hasil)){
                        $sql_mrp_production_fg_hasil = rtrim($sql_mrp_production_fg_hasil, ', ');
                        $this->m_mo->simpan_mrp_production_fg_hasil_batch($sql_mrp_production_fg_hasil);               
                    }
        
                    if(!empty($sql_mrp_production_rm_hasil)){
                        $sql_mrp_production_rm_hasil = rtrim($sql_mrp_production_rm_hasil, ', ');
                        $this->m_mo->simpan_mrp_production_rm_hasil_batch($sql_mrp_production_rm_hasil);
                    }
        
                    if(!empty($sql_stock_quant_batch) ){
                        $sql_stock_quant_batch = rtrim($sql_stock_quant_batch, ', ');
                        $this->_module->simpan_stock_quant_batch($sql_stock_quant_batch);
                    }
        
                    if(!empty($sql_stock_move_items_batch)){
                        $sql_stock_move_items_batch = rtrim($sql_stock_move_items_batch, ', ');
                        $this->_module->simpan_stock_move_items_batch($sql_stock_move_items_batch);
                    }

                    //update lokasi di stock_quant
                    if(!empty($where) AND !empty($case)){
                        $where = rtrim($where, ',');
                        $sql_update_lokasi  = "UPDATE stock_quant SET lokasi =(case ".$case." end), move_date = '".$tgl."' WHERE  quant_id in (".$where.") ";
                        $this->_module->update_perbatch($sql_update_lokasi);
                    }

                    //update reserve_origin di stock_quant
                    if(!empty($where2) AND !empty($case2)){
                        $where2 = rtrim($where2, ',');
                        $sql_update_reserve_move  = "UPDATE stock_quant SET reserve_origin =(case ".$case2." end) WHERE  quant_id in (".$where2.") ";
                        $this->_module->update_perbatch($sql_update_reserve_move);
                    }

                    //update status done di stock_move_items
                    if(!empty($where3) AND !empty($case3)){
                        $where3 = rtrim($where3, ',');
                        $where3_move_id = rtrim($where3_move_id, ',');
                        $sql_update_status_stock_move_items  = "UPDATE stock_move_items SET status =(case ".$case3." end),tanggal_transaksi ='".$tgl."' WHERE  quant_id in (".$where3.") AND move_id in (".$where3_move_id.") ";
                        $this->_module->update_perbatch($sql_update_status_stock_move_items);
                    }

                    if($items_empty == FALSE){
                        $where4_move_id   = '';
                        foreach($items_arr as $item){
                            $quant_id   = $item['quant_id'];
                            $row_order  = $item['row_order'];
        
                            // get stock move items by kode
                            $get_smi = $this->_module->get_stock_move_items_by_kode($move_id,$quant_id,$kode_produk,$row_order)->row_array();
        
                            if(!empty($get_smi)){
                                $quant_id       = $get_smi['quant_id'];
                                $move_id_smi    = $get_smi['move_id'];
                                $qty            = $get_smi['qty'];
                                $origin_prod_smi= $get_smi['origin_prod'];
                                //$kode_produk    = $get_smi['kode_produk'];
                                //$nama_produk    = $get_smi['nama_produk'];
                                //$lot            = $get_smi['lot'];
                                //$uom            = $get_smi['uom'];
                                //$qty2           = $get_smi['qty2'];
                                //$uom2           = $get_smi['uom2'];
                          
                                if($qty > 0 AND $qty != ''){                        
                                    //untuk update status
                                    //cek jml_qty di stock_move_items yg status nya ready
                                    $cek_smi=$this->m_mo->cek_qty_stock_move_items_by_produk($move_id_smi,addslashes($origin_prod_smi),'ready')->row_array();
                                    if(empty($cek_smi['jml_qty']) or $cek_smi['jml_qty'] == '0'){
                                        //cek yg status nya done
                                        $cek_smi2=$this->m_mo->cek_qty_stock_move_items_by_produk($move_id_smi,addslashes($origin_prod_smi),'done')->row_array();
                                        if($cek_smi2['jml_qty'] < $qty_rm){
                                            //update status barang jadi draft
                                            $case4   .= "when origin_prod = '".addslashes($origin_prod_smi)."' then 'draft' ";
                                            $where4  .= "'".addslashes($origin_prod_smi)."',";
                                            $where4_move_id .= "'".addslashes($move_id_smi)."',";
            
                                        }else if($cek_smi2['jml_qty'] >= $qty_rm){
                                            //update status barang jadi done
                                            $case4   .= "when origin_prod = '".addslashes($origin_prod_smi)."' then 'done' "; 
                                            $where4  .= "'".addslashes($origin_prod_smi)."',";
                                            $where4_move_id .= "'".addslashes($move_id_smi)."',";

                                        }
                                    }  
                                }

                            }
                        }// end foreach $item_arr
                        

                        //update status barang di rm target dan stock_move_produk
                        if(!empty($where4) AND !empty($case4)){
                            $where4 = rtrim($where4, ',');
                            $where4_move_id = rtrim($where4_move_id, ',');
                            $sql_update_status_rm_target ="UPDATE mrp_production_rm_target SET status =(case ".$case4." end) WHERE  origin_prod in (".$where4.") AND kode = '".$kode."' AND move_id in (".$where4_move_id.") ";
                            $this->_module->update_perbatch($sql_update_status_rm_target);

                            $sql_update_status_stock_move_produk ="UPDATE stock_move_produk SET status =(case ".$case4." end) WHERE  origin_prod in (".$where4.") AND move_id in (".$where4_move_id.")  ";
                            $this->_module->update_perbatch($sql_update_status_stock_move_produk);

                        }
                    } // end if items empty false

                    $jenis_log   = "edit";
                    $note_log    = "Habis Diproduksi | Jumlah : ".$jml_lot;
                    $this->_module->gen_history_deptid($sub_menu, $kode, $jenis_log, $note_log, $username, $deptid); 

                    $callback = array('status' => 'success', 'message'=>'Data Habis diproduksi Berhasil Disimpan !', 'icon' => 'fa fa-check', 'type'=>'success');

                }else{
                    $lot_empty = rtrim($lot_empty, ',');
                    $callback = array('status' => 'failed', 'message'=>'Data Habis diproduksi  Gagal Disimpan / Lot '.$lot_empty.' yang akan di habiskan tidak ditemukan !', 'icon' => 'fa fa-warning', 'type'=>'danger');
                }
                 
                //unlock table
                $this->_module->unlock_tabel();

            }


        } */

    //     echo json_encode($callback);
    // }

    public function tambah_data_details_quant_mo()
    {
        $kode_produk  = $this->input->post('kode_produk');
        $move_id      = $this->input->post('move_id');
        $deptid       = $this->input->post('deptid');
        $origin_prod  = $this->input->post('origin_prod');

        $data['kode_produk'] = $kode_produk;
        $data['move_id']     = $move_id;
        $data['deptid']      = $deptid;
        $data['origin_prod'] = $origin_prod;
        return $this->load->view('modal/v_mo_quant_tambah_details_modal',$data);
    }

    public function tambah_data_details_quant_mo_modal()
    {
        $kode_produk  = addslashes($this->input->post('kode_produk'));
        $move_id      = $this->input->post('move_id');

        //lokasi tujuan, lokasi dari
        $lokasi = $this->_module->get_location_by_move_id($move_id)->row_array();

        $list = $this->m_mo->get_datatables2($kode_produk,$lokasi['lokasi_dari']);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $field) {
            $no++;
            $row = array();
            $row[] = $no.".";
            $row[] = $field->nama_produk;
            $row[] = $field->lot;
            $row[] = number_format($field->qty,2)." ".$field->uom;
            $row[] = number_format($field->qty2,2)." ".$field->uom2;
            $row[] = $field->reff_note;
            $row[] = $field->quant_id;
            //$row[] = '';//buat checkbox
            //$row[] = $field->kode_produk."|".htmlentities($field->nama_produk)."|".$field->lot."|".$field->qty."|".$field->uom."|".$field->qty2."|".$field->uom2."|".$field->lokasi."|".$field->quant_id."|^";
          
            $data[] = $row;
        }
        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_mo->count_all2($kode_produk,$lokasi['lokasi_dari']),
            "recordsFiltered" => $this->m_mo->count_filtered2($kode_produk,$lokasi['lokasi_dari']),
            "data" => $data,
        );
        //output dalam format JSON
        echo json_encode($output);
    }

    public function save_details_quant_mo_modal()
    {
        $sub_menu  = $this->uri->segment(2);
        $username  = $this->session->userdata('username'); 

        if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
            // session habis
            $callback = array('status' => 'failed','message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
        }else{

            $deptid     = $this->input->post('deptid');
            $kode       = $this->input->post('kode');
            $kode_produk= $this->input->post('kode_produk');
            $move_id    = $this->input->post('move_id');
            $origin_prod= $this->input->post('origin_prod');
            $check      = $this->input->post('checkbox');
            $countchek  = $this->input->post('countchek');
            $sql_stock_move_items_batch = "";
            $tgl        = date('Y-m-d H:i:s');
            //$row        = explode("^,", $check);
            $status     = "";
            $status_brg = "ready";
            $case       = "";
            $where      = "";       
            $qty_tmp    = "";
            $kosong     = false;

            //lock tabel
            $this->_module->lock_tabel('stock_quant WRITE, stock_move_items WRITE,stock_move WRITE,stock_move_produk WRITE, mrp_production WRITE, mrp_production_rm_target WRITE,  mrp_production_rm_target rm WRITE, departemen WRITE,  stock_move_items as smi WRITE, mst_produk as mp WRITE, mst_category as mc WRITE'  );
          
            //cek status mrp_production = done
            $cek1  = $this->m_mo->cek_status_mrp_production($kode,'done')->row_array();
            //cek status mrp_production = cancel
            $cek2  = $this->m_mo->cek_status_mrp_production($kode,'cancel')->row_array();
            //cek status mrp_production = hold
            $cek3  = $this->m_mo->cek_status_mrp_production($kode,'hold')->row_array();

            if(!empty($cek1['status'])){
                $callback = array('status' => 'failed', 'message'=>'Maaf, Data Tidak Bisa Disimpan, Status MO Sudah Done !', 'icon' => 'fa fa-warning', 'type'=>'danger');
            }else if(!empty($cek2['status'])){
                $callback = array('status' => 'failed', 'message'=>'Maaf, Data Tidak Bisa Disimpan, Status MO Batal !', 'icon' => 'fa fa-warning', 'type'=>'danger');
            }else if(!empty($cek3['status'])){
                $callback = array('status' => 'failed', 'message'=>'Maaf, Data Tidak Bisa Disimpan, Status MO di Hold !', 'icon' => 'fa fa-warning', 'type'=>'danger');
            }else{
          
                //get row order stock_move_items
                $row_order  = $this->_module->get_row_order_stock_move_items_by_kode($move_id);
                //get_lokasi dari by move id 
                $location = $this->_module->get_location_by_move_id($move_id)->row_array();
                // list product yang akan ditambah
                $list_product = "";
                $no           = 1;
                foreach ($check as $data) {
                    # code...
                    $cek_sq  = $this->_module->get_stock_quant_by_id($data)->row_array();

                    $quantid     = $cek_sq['quant_id'];     
                    $kode_produk = $cek_sq['kode_produk'];
                    $nama_produk = $cek_sq['nama_produk'];
                    $lot         = $cek_sq['lot'];
                    $qty         = $cek_sq['qty'];
                    $uom         = $cek_sq['uom'];
                    $qty2        = $cek_sq['qty2'];
                    $uom2        = $cek_sq['uom2'];
                    $lokasi      = $cek_sq['lokasi'];
                    $lokasi_fisik = $cek_sq['lokasi_fisik'];
                    $lebar_greige     = $cek_sq['lebar_greige'];
                    $uom_lebar_greige = $cek_sq['uom_lebar_greige'];
                    $lebar_jadi       = $cek_sq['lebar_jadi'];
                    $uom_lebar_jadi   = $cek_sq['uom_lebar_jadi'];

                    //cek product di stock quant
                    $cq = $this->_module->cek_produk_di_stock_quant($quantid,$location['lokasi_dari'])->row_array();
                    if(!empty($cq['quant_id']) AND empty($cq['reserve_move'])){
                          //insert ke stock move items
                        $sql_stock_move_items_batch .= "('".$move_id."', '".$quantid."','".addslashes($kode_produk)."', '".addslashes($nama_produk)."','".addslashes(trim($lot))."','".$qty."','".addslashes($uom)."','".$qty2."','".addslashes($uom2)."','ready','".$row_order."','".addslashes($origin_prod)."', '".$tgl."','".$lokasi_fisik."','".$lebar_greige."','".$uom_lebar_greige."','".$lebar_jadi."','".$uom_lebar_jadi."'), ";     
                        $row_order++;           

                        //update reserve move by quant id di stok quant                
                        $case       .= "when quant_id = '".$quantid."' then '".$move_id."'";
                        $where      .= "'".$quantid."',";

                        $list_product .= "(".$no.") ".$quantid."|".$kode_produk." ".$nama_produk." ".$lot." ".$qty." ".$uom." ".$qty2." ".$uom2." <br>";
                        $no++;
                    }else{
                        $kosong = true;
                    } 

                }
             /*
                for($i=0; $i <= $countchek-1;$i++){
                    $dt1  =  $row[$i];
                    $row2 = explode("|", $dt1);
                    $quantid     = $row2[8];
            
                    $kode_produk = $row2[0];
                    $nama_produk = $row2[1];
                    $lot         = $row2[2];
                    $qty         = $row2[3];
                    $uom         = $row2[4];
                    $qty2        = $row2[5];
                    $uom2        = $row2[6];
                    $lokasi      = $row2[7];

                    //cek product di stock quant
                    $cq = $this->_module->cek_produk_di_stock_quant($quantid,$location['lokasi_dari'])->row_array();
                    if(!empty($cq['quant_id'])){

                        //insert ke stock move items
                        $sql_stock_move_items_batch .= "('".$move_id."', '".$quantid."','".addslashes($kode_produk)."', '".addslashes($nama_produk)."','".addslashes(trim($lot))."','".$qty."','".addslashes($uom)."','".$qty2."','".addslashes($uom2)."','ready','".$row_order."','".addslashes($origin_prod)."'), ";   

                        //update reserve move by quant id di stok quant                
                        $case   .= "when quant_id = '".$quantid."' then '".$move_id."'";
                        $where  .= "'".$quantid."',";

                        $row_order++;            

                    }else{
                        $kosong = true;
                    }
                }
             */ 
            
                if(!empty($sql_stock_move_items_batch) AND $kosong == false){
                    $sql_stock_move_items_batch = rtrim($sql_stock_move_items_batch, ', ');
                    $this->_module->simpan_stock_move_items_batch($sql_stock_move_items_batch);

                    // jika mo Dyeing maka update field berat
                    if($deptid == 'DYE' || $deptid == 'DYE-R'){
                        $qty2   = $this->m_mo->get_qty2_smi_kain_by_kode($move_id)->row_array();
                        
                        //update berat di mrp production
                        $sql_update_berat = "UPDATE mrp_production set berat = '".$qty2['jml_qty2']."' WHERE kode = '".$kode."' ";
                        $this->_module->update_perbatch($sql_update_berat);
                    }
                    
                    if(!empty($case)){
                        //update stock quant 
                        $where = rtrim($where, ',');
                        $sql_update_stock_quant  = "UPDATE stock_quant SET reserve_move =(case ".$case." end) WHERE  quant_id in (".$where.") ";
                        $this->_module->update_perbatch($sql_update_stock_quant);
                    }

                    $this->m_mo->update_status_mrp_production_rm_target($kode,addslashes($origin_prod),$status_brg,$move_id);  
                    // cek type mo
                    $to    = $this->m_mo->cek_type_mo_by_dept_id($deptid)->row_array();
                    if($to['type_mo'] != 'colouring' AND $deptid != 'DYE' AND $deptid != 'DYE-R') {

                            //cek apa produk yang status nya ready atau done ?
                            $cek_status = $this->m_mo->cek_status_barang_mrp_production_rm_target($kode,'ready', 'done')->row_array();
                            if(!empty($cek_status['status'])){
                            $this->m_mo->update_status_mrp_production($kode,$status_brg);
                            $this->m_mo->update_status_stock_move_produk_mo($move_id,addslashes($origin_prod),$status_brg);
                            $cek_status2 = $this->m_mo->cek_status_mrp_production($kode,'')->row_array();
                            if($cek_status2['status']=='ready'){
                                $this->_module->update_status_stock_move($move_id,$status_brg);
                                }
                            }
                    }
                  
                }

                //unlock table
                $this->_module->unlock_tabel();        
                if($kosong == false){

                    $jenis_log   = "edit";
                    $note_log    = "Tambah Data Details -> <br> ".$list_product;
                    $this->_module->gen_history_deptid($sub_menu, $kode, $jenis_log, addslashes($note_log), $username, $deptid);
                    $callback    = array('status'=>'success',  'message' => 'Detail Product Berhasil Ditambahkan !',  'icon' =>'fa fa-check', 'type' => 'success'); 
                }else{
                    $callback    = array('status'=>'kosong',  'message' => 'Maaf, Product Sudah ada yang terpakai !',  'icon' =>'fa fa-check', 'type' => 'danger');  
                }
            }           
            
        }
        echo json_encode($callback);
    }

    public function hold_mo()
    {
        if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
            // session habis
            $callback = array('status' => 'failed','message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
        }else{

            $sub_menu  = $this->uri->segment(2);
            $username  = $this->session->userdata('username'); 

            $kode      = addslashes($this->input->post('kode'));
            $deptid    = addslashes($this->input->post('deptid'));
            $alasan    = addslashes($this->input->post('alasan'));

            //cek status mrp_production = done
            $cek1  = $this->m_mo->cek_status_mrp_production($kode,'done')->row_array();
            //cek status mrp_production = cancel
            $cek2  = $this->m_mo->cek_status_mrp_production($kode,'cancel')->row_array();
            // cek status mrp_productio = hold
            $cek2  = $this->m_mo->cek_status_mrp_production($kode,'hold')->row_array();
            
            if(!empty($cek1['status'])){
                $callback = array('status' => 'failed', 'message'=>'Maaf, MO tidak bisa di Hold Status MO Sudah Done !', 'icon' => 'fa fa-warning', 'type'=>'danger');
            }else if(!empty($cek2['status'])){
                $callback = array('status' => 'failed', 'message'=>'Maaf, MO tidak bisa di Hold, Status MO Batal !', 'icon' => 'fa fa-warning', 'type'=>'danger');
            }else if(!empty($cek3['status'])){
                $callback = array('status' => 'failed', 'message'=>'Maaf, MO tidak bisa di Hold, Status MO di Hold !', 'icon' => 'fa fa-warning', 'type'=>'danger');
            }else{

                $cek_status  = $this->m_mo->cek_status_mrp_production($kode,'')->row_array();

                $status = 'hold';
                $this->m_mo->update_status_mrp_production($kode,$status);

                $this->m_mo->update_alasan_hold_mrp_production($kode,$alasan,$cek_status['status']);

                $jenis_log   = "edit";
                $note_log    = $kode. " di Hold";
                $this->_module->gen_history_deptid($sub_menu, $kode, $jenis_log, addslashes($note_log), $username, $deptid);
                $callback    = array('status'=>'success', 'message' => 'Status MO Berhasil diubah menjadi HOLD !',  'icon' =>'fa fa-check', 'type' => 'success'); 
            }


        }
        echo json_encode($callback);
    }

    public function unhold_mo()
    {

        if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
            // session habis
            $callback = array('status' => 'failed','message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
        }else{

            $sub_menu  = $this->uri->segment(2);
            $username  = $this->session->userdata('username'); 

            $kode      = addslashes($this->input->post('kode'));
            $deptid    = addslashes($this->input->post('deptid'));

            //cek status mrp_production = done
            $cek1  = $this->m_mo->cek_status_mrp_production($kode,'done')->row_array();
            //cek status mrp_production = cancel
            $cek2  = $this->m_mo->cek_status_mrp_production($kode,'cancel')->row_array();
            
            if(!empty($cek1['status'])){
                $callback = array('status' => 'failed', 'message'=>'Maaf, MO tidak bisa di unHold Status MO Sudah Done !', 'icon' => 'fa fa-warning', 'type'=>'danger');
            }else if(!empty($cek2['status'])){
                $callback = array('status' => 'failed', 'message'=>'Maaf, MO tidak bisa di unHold, Status MO Batal !', 'icon' => 'fa fa-warning', 'type'=>'danger');
            }else{

                $get = $this->m_mo->get_status_before_hold($kode)->row_array();
                $status = $get['status_before_hold'];

                $this->m_mo->update_status_mrp_production($kode,$status);

                $jenis_log   = "edit";
                $note_log    = $kode. " di unHold";
                $this->_module->gen_history_deptid($sub_menu, $kode, $jenis_log, addslashes($note_log), $username, $deptid);
                $callback    = array('status'=>'success', 'message' => 'Status MO Berhasil diubah jadi unHOLD !',  'icon' =>'fa fa-check', 'type' => 'success'); 
            }


        }
        echo json_encode($callback);

    }

    public function get_list_produk_rm_select2()
    {
        $prod     = addslashes($this->input->post('prod'));
        $callback = $this->m_mo->get_list_produk_rm($prod);
        echo json_encode($callback);
    }

    public function get_produk_rm_by_id()
    {
        $kode_produk = addslashes($this->input->post('kode_produk'));
        $result      = $this->m_mo->get_produk_additonal_by_id($kode_produk)->row_array();
        $callback    = array('kode_produk'=>$result['kode_produk'],'nama_produk'=>$result['nama_produk'],'uom'=>$result['uom'],);
        echo json_encode($callback);
    }

    public function batal_hph()
    {
       

        if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
            // session habis
            $callback = array('status' => 'failed','message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
        }else{

            $this->load->model("m_pengirimanBarang");
            $this->load->model("m_penerimaanBarang");

            $sub_menu  = $this->uri->segment(2);
            $username  = $this->session->userdata('username'); 

            $kode      = addslashes($this->input->post('kode'));
            $quant_id  = addslashes($this->input->post('quant_id'));
            $deptid    = addslashes($this->input->post('deptid'));
            $lot_post  = addslashes($this->input->post('lot'));
            $tanggal   = date("Y-m-d H:i:s");


            // lock table
            $this->_module->lock_tabel('mrp_production WRITE, mrp_production_fg_hasil WRITE, stock_quant WRITE, acc_stock_move_items WRITE, stock_move WRITE, stock_move_items WRITE, stock_move_produk WRITE, pengiriman_barang WRITE, pengiriman_barang_items WRITE, penerimaan_barang WRITE, penerimaan_barang_items WRITE, mrp_production_fg_target WRITE, log_history WRITE, user WRITE, main_menu_sub WRITE, adjustment as adj WRITE, adjustment_items as adji WRITE, mrp_production_cacat WRITE');


            //cek status mrp_production = done
            $cek1  = $this->m_mo->cek_status_mrp_production($kode,'done')->row_array();
            //cek status mrp_production = cancel
            $cek2  = $this->m_mo->cek_status_mrp_production($kode,'cancel')->row_array();
            //cek status mrp_production = hold
            $cek3  = $this->m_mo->cek_status_mrp_production($kode,'hold')->row_array();

            if(!empty($cek1['status'])){
                $callback = array('status' => 'failed', 'message'=>'Maaf, Data Tidak Bisa Dihapus, Status MO Sudah Done !', 'icon' => 'fa fa-warning', 'type'=>'danger');
            }else if(!empty($cek2['status'])){
                $callback = array('status' => 'failed', 'message'=>'Maaf, Data Tidak Bisa Dihapus, Status MO Batal !', 'icon' => 'fa fa-warning', 'type'=>'danger');
            }else if(!empty($cek3['status'])){
                $callback = array('status' => 'failed', 'message'=>'Maaf, Data Tidak Bisa Dihapus, Status MO di Hold !', 'icon' => 'fa fa-warning', 'type'=>'danger');
            }else{

                // get data by quant_id
                $data       = $this->m_mo->get_data_mrp_fg_hasil_by_quant($kode,$quant_id);
                if(empty($data)){
                    $callback = array('status' => 'failed', 'message'=>'Maaf, Data yang akan dihapus tidak ditemukan !', 'icon' => 'fa fa-warning', 'type'=>'danger');
                }else{
                
                    $consume    = $data->consume;
                    $lokasi_hph = $data->lokasi;
                    $kode_produk= $data->kode_produk;
                    $nama_produk= $data->nama_produk;
                    $lot        = $data->lot;
                    $qty        = $data->qty." ".$data->uom;
                    $qty2       = $data->qty2." ".$data->uom2;
                    $nama_grade = $data->nama_grade;

                    $tgl_hph = date("Y-m", strtotime($data->create_date));
                    $tgl_now = date("Y-m", strtotime($tanggal));

                    $tgl_hph2 = date("Y-m-d", strtotime($data->create_date));
                    $tgl_now2 = date("Y-m-d", strtotime($tanggal));

                    // cek lokasi 
                    $cek_lokasi = $this->m_mo->cek_lokasi_by_quant($quant_id);

                    // cek lot apa di adj atau tidak 
                    $cek_adj_lot = $this->m_mo->cek_lot_adj_by_quant($quant_id);

                    // cek KP / lot sudah ada cacat atau belum 
                    $cek_cacat = $this->m_mo->cek_mrp_cacat_by_quant($kode,$quant_id);

                    if($cek_adj_lot > 0 ){
                        $callback = array('status' => 'failed', 'message'=>'Maaf, KP/Lot <b>'.$lot.'</b> ini Tidak Bisa Dihapus, karena sudah di Adjustment !', 'icon' => 'fa fa-warning', 'type'=>'danger');
                    }else if($lokasi_hph != $cek_lokasi->lokasi){
                        $callback = array('status' => 'failed', 'message'=>'Maaf, KP/Lot <b>'.$lot.'</b> ini Tidak Bisa Dihapus, karena lokasinya sudah tidak di  <b>'.$lokasi_hph.' </b> !', 'icon' => 'fa fa-warning', 'type'=>'danger');
                    }else if($consume == "yes"){
                        $callback = array('status' => 'failed', 'message'=>'Maaf, KP/Lot  <b>'.$lot.'</b> ini Tidak Bisa Dihapus, karena sudah terdapat Konsumsi Bahan !', 'icon' => 'fa fa-warning', 'type'=>'danger');
                    }else  if($tgl_hph != $tgl_now){// cek apa tgl hph == tgl batal
                        $callback = array('status' => 'failed', 'message'=>'Maaf, KP/Lot <b>'.$lot.'</b> ini  Tidak Bisa Dihapus, karena sudah Berbeda Bulan !', 'icon' => 'fa fa-warning', 'type'=>'danger');
                    }else if($cek_cacat > 0){
                        $callback = array('status' => 'failed', 'message'=>'Maaf, KP/Lot  <b>'.$lot.'</b> ini Tidak Bisa Dihapus, karena sudah terdapat inputan Rekam Cacat Lot !', 'icon' => 'fa fa-warning', 'type'=>'danger');
                    }else{

                        $thn = date("Y", strtotime($tanggal));
                        $bln = date("n", strtotime($tanggal));
                        $type= "prod";
                        $where_del1   ="";
                        $status_brg_draft = "draft";
                        
                        // cek quant id di acc stock move items
                        if($tgl_hph2 != $tgl_now2){
                            $cek = $this->m_mo->cek_quant_acc_stock_move_items_by_kode($thn,$bln,$deptid,$type,$kode,$quant_id)->num_rows();
                        }else{
                            $cek = 1;
                        }
                        
                        if($cek == 0 ){
                            $callback = array('status' => 'failed', 'message'=>'Maaf, KP/Lot  <b>'.$lot.'</b> ini Tidak Bisa Dihapus !', 'icon' => 'fa fa-warning', 'type'=>'danger');
                        }else{

                            $origin_mo  = $this->m_mo->get_origin_mo_by_kode($kode);
                            $move_fg    = $this->m_mo->get_move_id_fg_target_by_kode($kode)->row_array();
                            
                            // cek route after produce
                            $cek_route = $this->m_mo->cek_route_after_produce_by_origin($origin_mo,$move_fg['move_id']);;

                            if($cek_route->num_rows() > 0 ){// cek move id kedepannya yg status nya ready (OUT/IN)
                                $result = $cek_route->row_array();

                                $mthd          = explode("|",$result['method']);
                                $method_dept   = trim($mthd[0]);
                                $method_action = trim($mthd[1]);
                                $move_id       =  $result['move_id'];

                                if($method_action == "OUT"){

                                    // hapus smi out
                                    $sql_delete_smi = "DELETE FROM stock_move_items WHERE move_id IN ('".$move_id."') AND quant_id IN ('".$quant_id."') ";
                                    $this->_module->update_perbatch($sql_delete_smi);

                                    // cek qty stock move item out 
                                    $get_qty = $this->_module->get_qty_stock_move_items_by_kode($move_id,addslashes($kode_produk))->row_array();

                                    $kode_out = $this->m_mo->get_kode_pengiriman_barang_by_move_id($move_id);

                                    if(!empty($kode_out)){
                                        if(empty($get_qty['sum_qty'])){// update status barang jadi draft
                                            $this->m_pengirimanBarang->update_status_pengiriman_barang_items($kode_out,addslashes($kode_produk),$status_brg_draft);
                                            $this->_module->update_status_stock_move_produk($move_id,addslashes($kode_produk),$status_brg_draft);
                                        }

                                        $cek_status = $this->m_pengirimanBarang->cek_status_barang_pengiriman_barang_items($kode_out,'draft')->row_array();
                                        if(!empty($cek_status['status_barang'])){
                                            $this->m_pengirimanBarang->update_status_pengiriman_barang($kode_out,$status_brg_draft);
                                            $cek_status2 = $this->m_pengirimanBarang->cek_status_pengiriman_barang($kode_out)->row_array();
                                            if($cek_status2['status']=='draft'){
                                                $this->_module->update_status_stock_move($move_id,$status_brg_draft);
                                            }
                                        }
                                    }

                                }else if($method_action == "IN"){

                                    // hapus smi out
                                    $sql_delete_smi = "DELETE FROM stock_move_items WHERE move_id IN ('".$move_id."') AND quant_id IN ('".$quant_id."') ";
                                    $this->_module->update_perbatch($sql_delete_smi);

                                    // cek qty stock move item out 
                                    $get_qty = $this->_module->get_qty_stock_move_items_by_kode($move_id,addslashes($kode_produk))->row_array();

                                    $kode_in = $this->m_mo->get_kode_penerimaan_barang_by_move_id($move_id);

                                    if(!empty($kode_in)){

                                        if(empty($get_qty['sum_qty'])){
                                            $this->m_penerimaanBarang->update_status_penerimaan_barang_items($kode_in,addslashes($kode_produk),$status_brg_draft);
                                            $this->_module->update_status_stock_move_produk($move_id,addslashes($kode_produk),$status_brg_draft);
                                        }
                        
                                        $cek_status = $this->m_penerimaanBarang->cek_status_barang_penerimaan_barang_items($kode_in,'ready')->row_array();
                                        if(empty($cek_status['status_barang'])){
                                            $this->m_penerimaanBarang->update_status_penerimaan_barang($kode_in,$status_brg_draft);
                                            $cek_status2 = $this->m_penerimaanBarang->cek_status_penerimaan_barang($kode_in)->row_array();
                                            if($cek_status2['status']=='draft'){
                                                $this->_module->update_status_stock_move($move_id,$status_brg_draft);
                                            }
                                        }
                                    }
                                }
                            }   

                            // hapus lot di mrp_fg_hasil
                            $sql_delete_fg_hasil = "DELETE FROM mrp_production_fg_hasil WHERE kode IN ('".$kode."') AND quant_id IN ('".$quant_id."') ";
                            $this->_module->update_perbatch($sql_delete_fg_hasil);

                            // hapus lot di smi by move fg hasil
                            $sql_delete_smi = "DELETE FROM stock_move_items WHERE move_id IN ('".$move_fg['move_id']."') AND quant_id IN ('".$quant_id."') ";
                            $this->_module->update_perbatch($sql_delete_smi);

                            // hapus stock quant
                            $sql_delete_stock = "DELETE FROM stock_quant WHERE quant_id IN ('".$quant_id."') ";
                            $this->_module->update_perbatch($sql_delete_stock);

                            // hapus acc smi
                            $sql_delete_acc_smi = "DELETE FROM acc_stock_move_items WHERE periode_th = '".$thn."' AND periode_bln = '".$bln."' AND dept_id_mutasi = '".$deptid."' AND kode_transaksi = '".$kode."' AND quant_id = '".$quant_id."' ";
                            $this->_module->update_perbatch($sql_delete_acc_smi);


                            $jenis_log   = "cancel";
                            $note_log    = " Hapus KP/Lot -> ".$quant_id." ".$kode_produk." ".$nama_produk." ".$lot." ".$qty."  ".$qty2." ".$nama_grade;
                            $this->_module->gen_history($sub_menu, $kode, $jenis_log, addslashes($note_log), $username);
                            
                            $callback    = array('status'=>'success',  'message' => 'KP/Lot <b>'.$lot.'</b> Berhasil Dihapus !',  'icon' =>'fa fa-check', 'type' => 'success'); 

                        }

                        
                    }
                }

            }

            //unlock table
            $this->_module->unlock_tabel();  

        }
        
        echo json_encode($callback);

    }

  
    public function print_barcode()
    {
        $data_arr  = json_decode($this->input->get('checkboxBarcode'),true);  
        $kode      = $this->input->get('kode');
        $dept_id   = $this->input->get('dept_id');

        if($dept_id == 'TWS'){
            $this->barcode_tws($kode,$data_arr,$dept_id);

        }else if($dept_id == 'WRD'){
            $this->barcode_wrd($kode,$data_arr,$dept_id);

        }else if($dept_id == 'WRP'){
            $this->barcode_wrp($kode,$data_arr,$dept_id);
        
        }else if($dept_id == 'TRI'){
            $this->barcode_tri($kode,$data_arr);

        }else if($dept_id == 'INS1'){
            $this->barcode_ins1($kode,$data_arr);

        }else{// belum ada barcode
            $this->barcode_empty();
        }
    }


    function barcode_empty()
    {
        echo 'Design Barcode Belum dibuat untuk Departemen tersebut :)';
    }


    function barcode_tws($kode,$data_arr,$dept_id)
    {
        $pdf = new PDF_Code128('L','mm',array(80,60));

        $pdf->SetMargins(0,0,0);
        $pdf->SetAutoPageBreak(False);
        $pdf->AddPage();
        $pdf->SetFont('Arial','B',15,'C');

        $loop          = 1;

        // get mesin by kode
        $get_mc = $this->m_mo->get_mesin_by_mo($kode)->row_array();
        $mesin  = $get_mc['nama_mesin'];
        
        //get origin_mo
        $origin_mo  = $this->m_mo->get_origin_mo_by_kode($kode);
        $method= $dept_id.'|OUT';
        
        foreach ($data_arr as $val ) {
      
            if($loop == 2){
                $pdf->AddPage();
                $loop = 1;
            }
            
            //get produk,qty by kode etc
            $get         = $this->m_mo->get_data_fg_hasil_by_kode($kode,$val)->row_array();
            if(isset($get)){
                $nama_produk = $get['nama_produk'];
                $barcode     = $get['lot'];
                $qty         = $get['qty'];
                $uom         = $get['uom'];
                $tgl         = $get['create_date'];
                $reff_note   = $get['reff_note'];
                $note_head   = $get['note_head'];
            }else{
                $nama_produk = "Not Found";
                $barcode     = "Not Found";
                $qty         = "";
                $uom         = "";
                $tgl         = "";
                $reff_note   = "";
                $note_head   = "";
            }
           

            // get reff picking by kode
            $reff_picking  = $this->m_mo->get_reff_picking_pengiriman_by_kode($barcode, $method, $origin_mo);

            $nh = explode('|', $note_head);
            $loop1 = 0;
            $nh_mo = '';
            $nh_dept = '';
            $nh_mc   = '';
            foreach($nh as $nhx){
                if($loop1 == 2){
                    $nh_mo = trim($nhx);
                }

                if($loop1 == 3){
                    $nh_dept = trim($nhx);
                }

                if($loop1 == 4){
                    $nh_mc = trim($nhx);
                }

                $loop1++;
            }

            $pdf->SetFont('Arial','B',15,'C'); // set font

            $pdf->setXY(3,1);
            $pdf->Multicell(74,5,$nama_produk,0,'L'); // nama produk

            $pdf->SetFont('Arial','B',12,'C'); // set font
            
            $pdf->setXY(3,12);
            $pdf->Multicell(74,5,"Lot : ".$barcode,0,'L');// Lot

            $pdf->SetFont('Arial','',12,'C'); // set font

            $pdf->setXY(3,22);
            $pdf->Multicell(74,5,"Qty : ".$qty." ".$uom,0,'L'); // qty

            $pdf->setXY(3,22);
            $pdf->Multicell(74,5,"MC : ".$mesin,0,'R');// MC TWS

            $pdf->setXY(3,27);
            $pdf->Multicell(30,5,"Tgl.HPH   :",0,'L');// Tgl buat/hph

            $pdf->setXY(24,27);
            $pdf->Multicell(60,5," ".$tgl,0,'L');// isi Tgl buat/hph

            $pdf->setXY(3,32);
            $pdf->Multicell(74,5,"Reff Note : ".$reff_note,0,'L');// reff note

            $pdf->setXY(3,38);
            if($nh_mc != ''){
                $nh_mc = ' - '.$nh_mc;
            }

            $pdf->SetFont('Arial','B',12,'C'); // set font
            $pdf->Multicell(30,5,"Dept Tujuan : ",0,'L');// Departemen Tujuan

            $pdf->setXY(32,38);
            $pdf->Multicell(42,5,$nh_dept.''.$nh_mc,0,'L');// Departemen Tujuan
            
            $pdf->setXY(3,47);
            $pdf->Multicell(74,5,"MO Tujuan   : ".$nh_mo,0,'L');// MO Tujuan
            
            $pdf->SetFont('Arial','B',8,'C'); // set font
            $pdf->setXY(3,50);
            $pdf->Multicell(75,5,"Reff Picking : ".$reff_picking,0,'L');// reff picking pengiriman barang
            
            // $pdf->Code128(5,47,$barcode,70,8,'C',0,1); // barcode

            $pdf->SetFont('Arial','B',8,'C'); // set font
            $pdf->setXY(0,54);
            $pdf->Multicell(80,5,'Barcode Twisting',0,'C');// barcode departement

            $loop++;
        }

        $pdf->output();

    }  

    function barcode_wrp($kode,$data_arr,$dept_id)
    {
    
        $pdf = new PDF_Code128('L','mm',array(80,60));

        $pdf->SetMargins(0,0,0);
        $pdf->SetAutoPageBreak(False);
        $pdf->AddPage();
        $pdf->SetFont('Arial','B',15,'C');

        $loop        = 1;

        //get origin_mo
        $origin_mo  = $this->m_mo->get_origin_mo_by_kode($kode);
        $method= $dept_id.'|OUT';

        // get mesin by kode
        //$get_mc = $this->m_mo->get_mesin_by_mo($kode)->row_array();
        //$mesin  = $get_mc['nama_mesin'];

        foreach ($data_arr as $val ) {

            if($loop == 2){
                $pdf->AddPage();
                $loop = 1;
            }

            //get produk,qty by kode etc
            $get         = $this->m_mo->get_data_fg_hasil_by_kode($kode,$val)->row_array();
            if(isset($get)){
                $nama_produk = $get['nama_produk'];
                $barcode     = $get['lot'];
                $qty         = $get['qty'];
                $uom         = $get['uom'];
                $qty2        = $get['qty2'];
                $uom2        = $get['uom2'];
                $tgl         = $get['create_date'];
                $reff_note   = $get['reff_note'];
                $note_head   = $get['note_head'];
            }else{
                $nama_produk = "Not Found";
                $barcode     = "Not Found";
                $qty         = "";
                $uom         = "";
                $qty2        = "";
                $uom2        = "";
                $tgl         = "";
                $reff_note   = "";
                $note_head   = "";
                
            }

            // get reff picking by kode
            $reff_picking  = $this->m_mo->get_reff_picking_pengiriman_by_kode($barcode, $method, $origin_mo);
            /*
                Format reff note dari PPIC
                1. SC
                2. MO 
                3. Dept Tujuan - MC
                4. Corak JAC
                5. Jenis Benang
                Contoh Penulisan Reff NOte 
                SC1896 | MO211000406 | MC222 | 7P1514 | NYLON 70/6 TEXT
            */

            $nh = explode('|', $note_head);
            $loop1 = 0;
            $nh_mo = '';
            $nh_mc   = '';
            $nh_sc = '';
            foreach($nh as $nhx){
                if($loop1 == 0){
                    $nh_sc = trim($nhx);
                }
                if($loop1 == 1){
                    $nh_mo = trim($nhx);
                }
                if($loop1 == 2){
                    $nh_mc = trim($nhx);
                }

                $loop1++;
            }

            $pdf->SetFont('Arial','B',15,'C'); // set font

            $pdf->setXY(3,2);
            $pdf->Multicell(74,5,$nama_produk,0,'L'); // nama produk

            $pdf->SetFont('Arial','B',12,'C');

            $pdf->setXY(3,14);
            $pdf->Multicell(74,5,"Lot : ".$barcode,0,'L');// Lot
            
            $pdf->SetFont('Arial','',12,'C');
            $pdf->setXY(3,25);
            $pdf->Multicell(74,5,"Qty : ".round($qty,2)." ".$uom.", Qty2 : ".round($qty2,2)." ".$uom2,0,'L'); // qty

            $pdf->setXY(3,30);
            $pdf->Multicell(74,5,"Tgl.HPH : ",0,'L');// Tgl buat/hph

            $pdf->setXY(24,30);
            $pdf->Multicell(60,5," ".$tgl,0,'L');// isi Tgl buat/hph

            $pdf->SetFont('Arial','B',12,'C'); // set font

            $pdf->setXY(3,35);
            $pdf->Multicell(74,5,"SC : ".$nh_sc,0,'L');// reff note

            $pdf->setXY(3,40);
            if($nh_mc != ''){
                $nh_mc = $nh_mc;
            }
           
            if($reff_note != ''){
                
                $tn = explode('|',$reff_note);
                $loopbr = 0;
                $GB = '';
                foreach($tn as $tns){
                    if($loopbr == 0){
                        $GB =trim($tns);
                    }
                    $loopbr++;
                }

                $reff_note = ' - '.$GB;
            }

            $pdf->Multicell(30,4,"Dept Tujuan : ",0,'L');// Caption Departemen Tujuan 

            $pdf->setXY(32,40);
            $pdf->Multicell(42,4,$nh_mc."".$reff_note,0,'L'); // Departemen Tujuan - MC - Reff note Lot

            $pdf->setXY(3,48);
            $pdf->Multicell(74,5,"MO Tujuan   : ".$nh_mo,0,'L');// MO Tujuan

            $pdf->SetFont('Arial','B',8,'C'); // set font
            $pdf->setXY(3,51);
            $pdf->Multicell(77,5,"Reff Picking : ".$reff_picking,0,'L');// reff picking pengiriman barang

            // $pdf->Code128(5,47,$barcode,70,8,'C',0,1); // barcode
            $tgl_now = (date('Y/m/d H:i:s'));
            $pdf->SetFont('Arial','B',8,'C'); // set font
            $pdf->setXY(0,54);
            $pdf->Multicell(80,5,'Barcode WRP - '.$tgl_now,0,'C');// barcode
       
            $loop++;
        }

        $pdf->output();

    }



    function barcode_wrd($kode,$data_arr,$dept_id)
    {
        
        $pdf = new PDF_Code128('p','mm',array(60,80));


        $pdf->SetMargins(0,0,0);
        $pdf->SetAutoPageBreak(False);
        $pdf->AddPage();
        $pdf->SetFont('Arial','B',15,'C');

        //get origin_mo
        $origin_mo  = $this->m_mo->get_origin_mo_by_kode($kode);
        $method= $dept_id.'|OUT';

        $loop = 1;
        $heightNama = 0; 
        $enter         = 1;
        $enter_barcode = 18;

        foreach ($data_arr as $val ) {

            if($loop == 3){
                $pdf->AddPage();
                $loop = 1;
                $heightNama = 0; 
                $enter         = 1;
                $enter_barcode = 18;

            }
           
            $get    = $this->m_mo->get_data_fg_hasil_by_kode($kode,$val)->row_array();
            if(isset($get)){
                $barcode     = $get['lot'];
                $nama_produk = $get['nama_produk'];
                $reff_note   = $get['reff_note'];
            }else{
                $barcode     = "Not Found";
                $nama_produk = "Not Found";
                $reff_note   = "Not Found";
            }

            // get reff picking by kode
            $reff_picking  = $this->m_mo->get_reff_picking_pengiriman_by_kode($barcode, $method, $origin_mo);

            $pdf->setXY(0,3+$heightNama);
            $pdf->Multicell(60,5,$barcode,0,'C');
            
            $pdf->SetFont('Arial','B',8,'C');
            $pdf->setXY(0,5+$heightNama+13);
            $pdf->Multicell(60,3,'Reff Picking : '.$reff_picking,0,'C');
         

            // $pdf->Code128(5,$enter+$enter_barcode,$barcode,50,6,'C',0,1);//barcode

            $pdf->SetFont('Arial','B',8,'C');

            $pdf->setXY(0,5+$heightNama+20);
            $pdf->Multicell(60,3,$nama_produk,0,'C');
            $pdf->setXY(0,5+$heightNama+27);
            $pdf->Multicell(60,3,$reff_note,0,'C');

            $pdf->SetFont('Arial','B',13,'C');

            $heightNama    = $heightNama + 40;
            $enter_barcode = $enter_barcode + 40;
            
        $loop++;
        }

        $pdf->output();

    }


    function barcode_tri($kode,$data_arr)
    {
       
        // $pdf=new PDF_Code128('p','mm',array(50,70));
        $pdf=new PDF_Code128('l','mm',array(76.2,101.6));
        // $pdf = new PDF_Code128('p','mm',array(80,101));
        // // $pdf=new PDF_Code128('p','mm',array(88.9,50.8));
        // // $pdf=new PDF_Code128('p','mm',array(89,101.6));


        $pdf->AddPage();
        $loop  = 1;
        foreach ($data_arr as $val) {

            if($loop == 2){
                $pdf->AddPage();
                $loop = 1;
            }

            $get    = $this->m_mo->get_data_fg_hasil_by_kode($kode,$val)->row_array();
            if(isset($get)){
                $barcode     = $get['lot'];
                $nama_grade  = $get['nama_grade'];
            }else{
                $barcode     = "Not Found";
                $nama_grade  = "";
            }
            $pdf->Line(5, 10, 95, 10); // garis atas gunting

            $pdf->SetDash(5,5); //5mm on, 5mm off
            $pdf->Line(5, 18, 95, 18); // garis atas jahit
            $pdf->SetDash(); //off


            $pdf->SetFont('Arial','B',20,'C');
            $pdf->setXY(0,8);
            $pdf->Multicell(82,48,$barcode,0,'R');// Nama LOT 1
            //$pdf->Cell(100,5,$barcode,0,0,'R');// Nama LOT 1

            $pdf->SetFont('Arial','B',30);
            $pdf->setXY(82,6);
            $pdf->Multicell(20,48,$nama_grade,0,'L'); // grade
            //$pdf->Cell(0,3,$nama_grade,0,1);//grade
            
            $pdf->Code128(5,40,$barcode,90,15,'C');//barcode 1       
            
            // $pdf->Line(5, 60, 95, 60); // garis bawah
            //$pdf->Cell(150,30,'','B',1,'C');//garis tengah   

            // $pdf->SetFont('Arial','B',25,'C');
            // $pdf->setXY(10,54);
            // $pdf->Multicell(110,10,$barcode,0,'R');// Nama LOT 2
            // //$pdf->Cell(100,30,$barcode,0,0,'R');

            // $pdf->SetFont('Arial','B',40);
            // $pdf->setXY(120,51);
            // $pdf->Multicell(30,13,$nama_grade,0,'L'); // grade
            // //$pdf->Cell(0,27,$nama_grade,0,1);//grade

            // $pdf->Code128(30,65,$barcode,110,23,'C');//barcode 2

            // $pdf->Line(170,3,170,100);//vertical

            $loop++;
        }


        $pdf->Output();
    }



    function barcode_ins1($kode,$data_arr)
    {
       
        $pdf=new PDF_Code128('l','mm',array(76.2,101.6));
        
 
         $pdf->AddPage();
         $loop  = 1;
         foreach ($data_arr as $val) {
 
             if($loop == 2){
                 $pdf->AddPage();
                 $loop = 1;
             }
 
             $get    = $this->m_mo->get_data_fg_hasil_by_kode($kode,$val)->row_array();
             if(isset($get)){
                 $barcode     = $get['lot'];
                 $nama_grade  = $get['nama_grade'];
             }else{
                 $barcode     = "Not Found";
                 $nama_grade  = "";
             }
             $pdf->Line(5, 10, 95, 10); // garis atas gunting
 
             $pdf->SetDash(5,5); //5mm on, 5mm off
             $pdf->Line(5, 18, 95, 18); // garis atas jahit
             $pdf->SetDash(); //off
 
 
             $pdf->SetFont('Arial','B',20,'C');
             $pdf->setXY(0,8);
             $pdf->Multicell(82,48,$barcode,0,'R');// Nama LOT 1
             //$pdf->Cell(100,5,$barcode,0,0,'R');// Nama LOT 1
 
             $pdf->SetFont('Arial','B',30);
             $pdf->setXY(82,6);
             $pdf->Multicell(20,48,$nama_grade,0,'L'); // grade
             //$pdf->Cell(0,3,$nama_grade,0,1);//grade
             
             $pdf->Code128(5,40,$barcode,90,15,'C');//barcode 1       
             
             // $pdf->Line(5, 60, 95, 60); // garis bawah
             //$pdf->Cell(150,30,'','B',1,'C');//garis tengah   
 
             // $pdf->SetFont('Arial','B',25,'C');
             // $pdf->setXY(10,54);
             // $pdf->Multicell(110,10,$barcode,0,'R');// Nama LOT 2
             // //$pdf->Cell(100,30,$barcode,0,0,'R');
 
             // $pdf->SetFont('Arial','B',40);
             // $pdf->setXY(120,51);
             // $pdf->Multicell(30,13,$nama_grade,0,'L'); // grade
             // //$pdf->Cell(0,27,$nama_grade,0,1);//grade
 
             // $pdf->Code128(30,65,$barcode,110,23,'C');//barcode 2
 
             // $pdf->Line(170,3,170,100);//vertical
 
             $loop++;
         }
 
 


        $pdf->Output();
    }


}