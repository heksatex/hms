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

    public function Finishing()
    {
        $data['id_dept']='FIN';
        $this->load->view('manufacturing/v_mo', $data);
    }

    public function Brushing()
    {
        $data['id_dept']='BRS';
        $this->load->view('manufacturing/v_mo', $data);
    }

    public function Inspecting2()
    {
        $data['id_dept']='INS2';
        $this->load->view('manufacturing/v_mo', $data);
    }

    public function Packing()
    {
        $data['id_dept']='PAC';
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
    }

    public function edit($kode = null)
    {
        if(!isset($kode)) show_404();
        $username          = addslashes($this->session->userdata('username')); 
        $kode_decrypt      = decrypt_url($kode);
        $list              = $this->m_mo->get_data_by_code($kode_decrypt);
        $data["list"]      = $list;
        $lw                = $this->m_mo->get_location_waste_by_deptid($list->dept_id)->row_array();
        $data["rm"]        = $this->m_mo->get_list_bahan_baku($kode_decrypt);
        $data["hasil_rm"]  = $this->m_mo->get_list_bahan_baku_hasil_group($kode_decrypt);
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
            }else{
                $lot_prefix   = 'KP/[MY]/[MC]/[DEPT]/COUNTER';
            }
        }else{
            $lot_prefix   = $list->lot_prefix;
        }

        $data['lot_prefix'] = $lot_prefix;

        if(!empty($cek_request['origin'])){
            $data['dystuff']   = $this->m_mo->get_dyeing_stuff($kode_decrypt);
            $data['aux']       = $this->m_mo->get_aux($kode_decrypt);
            $data['disable']   = "yes";//untuk disable air dan berat
        }else{
            $data['dystuff']   = "";
            $data['aux']       = "";
            $data['disable']   = "no";
        }

        if(empty($data['list'])){
            show_404();
        }else{
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
 
    public function add_rm_modal()//blm kepake //nambah bahan baku
    {
        $mo   = $this->input->post('txtKode');
        $data['kode'] = array('mo' => $mo);
        return $this->load->view('modal/v_rm_modal', $data);
    }

    public function save_rm_modal()//blm kepake //nambah bahan baku
    {
       $kode    = $this->input->post('kode'); 
       $product = $this->input->post('txtProduct'); 
       $qty     = $this->input->post('txtQty'); 
       $uom     = $this->input->post('txtUom'); 
        if(empty($product)){
            $callback = array('status' => 'failed', 'field' => 'txtProduct',  'message' => 'Product Harus Diisi !', 'icon' =>'fa fa-warning', 'type' => 'danger');
        }elseif(empty($qty)){
            $callback = array('status' => 'failed', 'field' => 'txtQty', 'message' => 'Qty Harus Diisi !', 'icon' =>'fa fa-warning', 'type' => 'danger');
        }elseif(empty($uom)){
            $callback = array('status' => 'failed', 'field' => 'txtUom', 'message' => 'uom Harus Diisi !', 'icon' =>'fa fa-warning', 'type' => 'danger');
        }else{
            $callback= array('status' => 'success', 'message' => 'Data Berhasil Ditambahkan !', 'icon' =>'fa fa-check', 'type' => 'success');
            $this->m_mo->save_rm($kode, $product, $qty, $uom);
        }

      echo json_encode($callback) ; 
    }

    public function hapus_rm()// blm kepake
    {
        $kode     =  $this->input->post('kode');
        $row_order=  $this->input->post('row_order');
        $callback = array('status' => 'success', 'message'=>'Data Berhasil di hapus !', 'icon' => 'fa fa-check', 'type'=>'success');
        echo json_encode($callback);
        $this->m_mo->delete_rm($kode, $row_order);
    }    

    public function tambah_rm()// blm kepake
    {
        $data['kode']  = $this->input->post('kode');
        return $this->load->view('modal/v_mo_rm_modal',$data);
    }

    public function produksi_rm_batch()
    {
        $kode             = $this->input->post('kode');
        $move_id          = $this->input->post('move_id');
        $move_id_fg       = $this->input->post('move_id_fg');
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

                $lot_prefix  = 'KP/'.$tgl_bln.'/'.$no_mesin.'/'.$dept_prefix.'/';// lot prefix by default system
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
        $data['konsumsi']   = $this->m_mo->get_konsumsi_bahan($move_id,'ready');
        $sl                 = $this->_module->get_nama_dept_by_kode($deptid)->row_array();// get ,copy_bahanbaku true/false
        $data['copy_bahan_baku']  = $sl['copy_bahan_baku'];
        $data['lbr_produk'] = $this->m_mo->get_lebar_produk_by_kode($kode);
        $data['uom']        = $this->_module->get_list_uom();
        $data['show_lebar'] = $this->_module->cek_show_lebar_by_dept_id($deptid)->row_array();

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

    public function get_list_produk_waste()
    {
        $kode_mo  = $this->input->post('kode');
        $kode_produk  = $this->input->post('kode_produk');
        $nama_produk  = $this->input->post('nama_produk');

        $move_rm  = $this->m_mo->get_move_id_rm_target_by_kode($kode_mo)->row_array();
        $list     = $this->m_mo->get_list_waste_bahan_baku_by_move_id($move_rm['move_id'])->result();
        $dataRecord[] = array('kode_produk' => $kode_produk, 
                            'nama_produk' => $nama_produk);

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

        $move_rm  = $this->m_mo->get_move_id_rm_target_by_kode($kode_mo)->row_array();
        $list     = $this->m_mo->get_list_lot_waste_by_kode($move_rm['move_id'],$kode_produk)->result();
        
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
        $move_id          = $this->input->post('move_id');
        $move_id_fg       = $this->input->post('move_id_fg');
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
        $data['konsumsi']   = $this->m_mo->get_konsumsi_bahan($move_id,'ready');
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

        if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
            // session habis
            $callback = array('message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
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

            //lock table
            $this->_module->lock_tabel('mrp_production WRITE, mrp_production_rm_hasil WRITE, mrp_production_fg_hasil WRITE, mrp_production_rm_target WRITE, mrp_production_fg_target WRITE, stock_move WRITE, stock_move_items WRITE, stock_quant WRITE, stock_move_produk WRITE, departemen WRITE, pengiriman_barang WRITE, pengiriman_barang_items WRITE, penerimaan_barang WRITE, penerimaan_barang_items WRITE, sales_contract WRITE');

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

                //simpan fg hasil
                foreach ($array_fg as $row) {

                    //simpan fg hasil
                    $sql_mrp_production_fg_hasil .= "('".$row['kode']."','".$move_id_fg."','".$start."','".$tgl."','".addslashes($row['kode_produk'])."','".addslashes($row['nama_produk'])."','".addslashes(trim($row['lot']))."','".addslashes($row['grade'])."','".$row['qty']."','".addslashes($row['uom'])."','".$row['qty2']."','".addslashes($row['uom2'])."','".$lokasi_fg['lokasi_tujuan']."','".$nama_user['nama']."','".$row_order."','".addslashes($row['lbr_greige'])."','".addslashes($row['uom_lbr_greige'])."','".addslashes($row['lbr_jadi'])."','".addslashes($row['uom_lbr_jadi'])."', '".addslashes($sales_order)."','".addslashes($sales_group)."'), ";

                    //simpan stock move items produksi
                    $sql_stock_move_items_batch .= "('".$move_id_fg."', '".$start."','".addslashes($row['kode_produk'])."', '".addslashes($row['nama_produk'])."','".addslashes(trim($row['lot']))."','".$row['qty']."','".addslashes($row['uom'])."','".$row['qty2']."','".addslashes($row['uom2'])."','".$status_done."','".$row_order_smi."','', '".$tgl."','','".addslashes($row['lbr_greige'])."','".addslashes($row['uom_lbr_greige'])."','".addslashes($row['lbr_jadi'])."','".addslashes($row['uom_lbr_jadi'])."'), ";

                   
                    //simpan stock quant dengan quant_id baru              
                    $sql_stock_quant_batch .= "('".$start."','".$tgl."', '".addslashes($row['kode_produk'])."', '".addslashes($row['nama_produk'])."','".addslashes(trim($row['lot']))."','".addslashes($row['grade'])."','".$row['qty']."','".addslashes($row['uom'])."','".$row['qty2']."','".addslashes($row['uom2'])."','".$lokasi_fg['lokasi_tujuan']."','".addslashes($row['reff_note'])."','".$sm_tj['move_id']."','".$origin_mo."','".$tgl."','".addslashes($row['lbr_greige'])."','".addslashes($row['uom_lbr_greige'])."','".addslashes($row['lbr_jadi'])."','".addslashes($row['uom_lbr_jadi'])."', '".addslashes($sales_order)."','".addslashes($sales_group)."'), ";

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
                        $sql_stock_move_items_batch .= "('".$sm_tj['move_id']."', '".$start."','".addslashes($row['kode_produk'])."', '".addslashes($row['nama_produk'])."','".addslashes(trim($row['lot']))."','".$row['qty']."','".addslashes($row['uom'])."','".$row['qty2']."','".addslashes($row['uom2'])."','".$status_ready."','".$row_order_smi_tujuan."','".addslashes($origin_prod)."', '".$tgl."','','".addslashes($row['lbr_greige'])."','".addslashes($row['uom_lbr_greige'])."','".addslashes($row['lbr_jadi'])."','".addslashes($row['uom_lbr_jadi'])."'), ";

                        //update status stock move,stock move dan stock move produk  pengiriman brg = ready
                        $case7  .= "when move_id = '".$sm_tj['move_id']."' then '".$status_ready."'";
                        $where7 .= "'".$sm_tj['move_id']."',";

                    }

                    //cek lot apa pernah diinput ?
                    if($cek_dl == 'true'){
                        $cek_lot = $this->m_mo->cek_lot_stock_quant(addslashes(trim($row['lot'])))->row_array();
                        if(strtoupper($cek_lot['lot']) == strtoupper(trim($row['lot']))){
                            $lot_double .= $row['lot'].',';
                        }
                    }

                    /*
                    //cek lot apa pernah diinput ?
                    $cek_lot = $this->m_mo->cek_lot_stock_quant(addslashes(trim($row['lot'])),'ADJ')->row_array();
                    if($cek_lot['lot'] == trim($row['lot'])){
                        //ambil lot double untuk alert
                        $lot_double .= $row['lot'].',';
                    }
                    */


                    $start++;
                    $row_order++;
                    $row_order_smi++;
                    $row_order_smi_tujuan++;
                    $jml_lot_fg++;
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

            }//if jika array_fg tidak kosong

            if(!empty($array_waste)){
                $move_fg  = $this->m_mo->get_move_id_fg_target_by_kode($kode)->row_array();
                $move_id_fg = $move_fg['move_id'];
               
                //lokasi waste lot by dept id
                $lokasi_waste = $this->m_mo->get_location_waste_by_deptid($deptid)->row_array();
                $cek_dl       = $this->m_mo->cek_validasi_double_lot_by_dept($deptid);

                foreach ($array_waste as $row) {

                    //simpan fg hasil
                    $sql_mrp_production_fg_hasil .= "('".$row['kode']."','".$move_id_fg."','".$start."','".$tgl."','".addslashes($row['kode_produk'])."','".addslashes($row['nama_produk'])."','".addslashes(trim($row['lot']))."','','".$row['qty']."','".addslashes($row['uom'])."','".$row['qty2']."','".addslashes($row['uom2'])."','".$lokasi_waste['waste_location']."','".$nama_user['nama']."','".$row_order."','','','','', '".addslashes($sales_order)."','".addslashes($sales_group)."'), ";

                    //simpan stock quant dengan quant_id baru              
                    $sql_stock_quant_batch .= "('".$start."','".$tgl."', '".addslashes($row['kode_produk'])."', '".addslashes($row['nama_produk'])."','".addslashes(trim($row['lot']))."','','".$row['qty']."','".$row['uom']."','".$row['qty2']."','".addslashes($row['uom2'])."','".$lokasi_waste['waste_location']."','".addslashes($row['reff_note'])."','".$move_id_fg."','".$origin_mo."','".$tgl."','','','','', '".addslashes($sales_order)."','".addslashes($sales_group)."'), ";

                    //simpan stock move items produksi
                    $sql_stock_move_items_batch .= "('".$move_id_fg."', '".$start."','".addslashes($row['kode_produk'])."', '".addslashes($row['nama_produk'])."','".addslashes(trim($row['lot']))."','".$row['qty']."','".addslashes($row['uom'])."','".$row['qty2']."','".addslashes($row['uom2'])."','".$status_done."','".$row_order_smi."','','".$tgl."','','','','',''), ";

                    //cek lot apa pernah diinput ?
                    if($cek_dl == 'true'){
                        $cek_lot = $this->m_mo->cek_lot_stock_quant_waste(addslashes(trim($row['lot'])),$lokasi_waste['waste_location'])->row_array();
                         if(strtoupper($cek_lot['lot']) == strtoupper(trim($row['lot']))){
                             $lot_double_Waste .= $row['lot'].',';
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
                }//foreach array_waste

            }//jika array_waste tidak kosong

            
            $move_rm = $this->m_mo->get_move_id_rm_target_by_kode($kode)->row_array();
            $move_id_rm = $move_rm['move_id'];

            if(!empty($array_rm)){
                //simpan rm hasil
                $row_order = $this->_module->get_row_order_stock_move_items_by_kode($move_id_rm);

                //lokasi tujuan rm
                $lokasi_rm = $this->_module->get_location_by_move_id($move_id_rm)->row_array();

                $get_ro = $this->m_mo->get_row_order_rm_hasil($kode)->row_array();
                $row_order_rm= $get_ro['row']+1;
                foreach ($array_rm as $row) {

                     if($row['qty_konsum'] > 0 AND $row['qty_konsum'] != ''){                       
                        
                        if($row['qty_konsum']<$row['qty_smi']){//jika qty_konsum kurang dari qty stock_move_items

                            //update qty stock_quant dan stock move items by quant_id
                            $qty_new = $row['qty_smi'] - $row['qty_konsum'];
                            $case   .= "when quant_id = '".$row['quant_id']."' then '".$qty_new."'";
                            $where  .= "'".$row['quant_id']."',";

                            $qty2_new = ($row['qty2']/$row['qty_smi'])*$row['qty_konsum'];
                            $qty2_update = $row['qty2'] - $qty2_new;
                            $case_qty2 .= "when quant_id = '".$row['quant_id']."' then '".$qty2_update."'";
                            $where_move_items .= "'".$row['move_id']."',";

                            //simpan qty_konsum di stock_quant dan stock_move_items dengan quant_id baru
                            $sql_stock_quant_batch .= "('".$start."','".$tgl."', '".addslashes($row['kode_produk'])."', '".addslashes($row['nama_produk'])."','".addslashes(trim($row['lot']))."','".addslashes($row['grade'])."','".$row['qty_konsum']."','".addslashes($row['uom'])."','".$qty2_new."','".addslashes($row['uom2'])."','".$lokasi_rm['lokasi_tujuan']."','".addslashes($row['reff_note'])."','".$row['move_id']."','".$origin_mo."','".$tgl."','".addslashes($row['lbr_greige'])."','".addslashes($row['uom_lbr_greige'])."','".addslashes($row['lbr_jadi'])."','".addslashes($row['uom_lbr_jadi'])."', '".addslashes($row['sales_order'])."','".addslashes($row['sales_group'])."'), ";
                                            
                            $sql_stock_move_items_batch .= "('".$row['move_id']."', '".$start."','".addslashes($row['kode_produk'])."', '".addslashes($row['nama_produk'])."','".addslashes(trim($row['lot']))."','".$row['qty_konsum']."','".addslashes($row['uom'])."','".$qty2_new."','".addslashes($row['uom2'])."','".$status_brg."','".$row_order."','".addslashes($row['origin_prod'])."','".$tgl."','','".addslashes($row['lbr_greige'])."','".addslashes($row['uom_lbr_greige'])."','".addslashes($row['lbr_jadi'])."','".addslashes($row['uom_lbr_jadi'])."'), ";

                            $sql_mrp_production_rm_hasil .= "('".$row['kode']."','".$row['move_id']."','".addslashes($row['kode_produk'])."','".addslashes($row['nama_produk'])."','".addslashes($row['lot'])."','".$row['qty_konsum']."','".addslashes($row['uom'])."','".addslashes($row['origin_prod'])."','".$row_order_rm."','".$start."'), ";
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

                            $case5   .= "when quant_id = '".$row['quant_id']."' then '".$status_brg."'"; //update status done move items
                            $where5  .= "'".$row['quant_id']."',";
                            $where5_move_id  .= "'".$row['move_id']."',";


                            $sql_mrp_production_rm_hasil .= "('".$row['kode']."','".$row['move_id']."','".addslashes($row['kode_produk'])."','".addslashes($row['nama_produk'])."','".addslashes($row['lot'])."','".$row['qty_konsum']."','".addslashes($row['uom'])."','".addslashes($row['origin_prod'])."','".$row_order_rm."','".$row['quant_id']."'), ";

                        }
                        $row_order_rm++;

                    }
                  
                }//foreach array_rm
            }

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
                $callback = array('status' => 'success', 'message'=>'Data Berhasil Disimpan !', 'icon' => 'fa fa-check', 'type'=>'success', 'sql' => $array_fg);
            }
               
            if(!empty($array_fg) OR !empty($array_waste)){ 

                if(!empty($array_fg) AND !empty($array_waste)){
                    $note_log    = "Produksi Batch ". $kode.' | LOT : '.$jml_lot_fg.' & Waste :'.$jml_lot_waste;
                }else if(!empty($array_fg)){
                    $note_log    = "Produksi Batch ". $kode.' | LOT : '.$jml_lot_fg;
                }else{
                    $note_log    = "Produksi Batch ". $kode.' | Waste : '.$jml_lot_waste;
                }

                $jenis_log   = "edit";
                $note_log    = $note_log;
                $this->_module->gen_history_deptid($sub_menu, $kode, $jenis_log, $note_log, $username,$deptid);          
            }

            echo json_encode($callback);
        }
    }

    public function save_produksi_modal()
    {
        if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
            // session habis
            $callback = array('message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
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
            $lot         = $this->input->post('lot');
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

            //lock table
            $this->_module->lock_tabel('mrp_production WRITE, mrp_production_rm_hasil WRITE, mrp_production_fg_hasil WRITE, mrp_production_rm_target WRITE, mrp_production_fg_target WRITE, stock_move WRITE, stock_move_items WRITE, stock_quant WRITE, stock_move_produk WRITE, departemen WRITE, pengiriman_barang WRITE, pengiriman_barang_items WRITE, penerimaan_barang WRITE, penerimaan_barang_items WRITE, sales_contract WRITE');


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
                $sql_mrp_production_fg_hasil .= "('".$kode."','".$move_id_fg."','".$start."','".$tgl."','".addslashes($kode_produk)."','".addslashes($nama_produk)."','".addslashes(trim($lot))."','".addslashes($grade)."','".$qty."','".addslashes($uom)."','".$qty2."','".addslashes($uom2)."','".$lokasi_fg['lokasi_tujuan']."','".$nama_user['nama']."','".$row_order."','".addslashes($lebar_greige)."','".addslashes($uom_lebar_greige)."','".addslashes($lebar_jadi)."','".addslashes($uom_lebar_jadi)."', '".addslashes($sales_order)."','".addslashes($sales_group)."'), ";
                       
                //simpan stock quant dengan quant_id baru              
                $sql_stock_quant_batch .= "('".$start."','".$tgl."', '".addslashes($kode_produk)."', '".addslashes($nama_produk)."','".addslashes(trim($lot))."','".addslashes($grade)."','".$qty."','".addslashes($uom)."','".$qty2."','".addslashes($uom2)."','".$lokasi_fg['lokasi_tujuan']."','".addslashes($reff_note)."','".$sm_tj['move_id']."','".$origin_mo."','".$tgl."','".addslashes($lebar_greige)."','".addslashes($uom_lebar_greige)."','".addslashes($lebar_jadi)."','".addslashes($uom_lebar_jadi)."', '".addslashes($sales_order)."','".addslashes($sales_group)."'), ";
            
                //simpan stock move items  produksi
                $sql_stock_move_items_batch .= "('".$move_id_fg."', '".$start."','".addslashes($kode_produk)."', '".addslashes($nama_produk)."','".addslashes(trim($lot))."','".$qty."','".addslashes($uom)."','".$qty2."','".addslashes($uom2)."','".$status_done."','".$row_order_smi."','', '".$tgl."','','".addslashes($lebar_greige)."','".addslashes($uom_lebar_greige)."','".addslashes($lebar_jadi)."','".addslashes($uom_lebar_jadi)."'), ";

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
                    $sql_stock_move_items_batch .= "('".$sm_tj['move_id']."', '".$start."','".addslashes($kode_produk)."', '".addslashes($nama_produk)."','".addslashes(trim($lot))."','".$qty."','".addslashes($uom)."','".$qty2."','".addslashes($uom2)."','".$status_ready."','".$row_order_smi_tujuan."','".addslashes($origin_prod)."', '".$tgl."','','".addslashes($lebar_greige)."','".addslashes($uom_lebar_greige)."','".addslashes($lebar_jadi)."','".addslashes($uom_lebar_jadi)."'), ";

                        
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

            $move_rm = $this->m_mo->get_move_id_rm_target_by_kode($kode)->row_array();
            $move_id_rm = $move_rm['move_id'];

            if(!empty($array_rm)){
                //simpan rm hasil
                $row_order = $this->_module->get_row_order_stock_move_items_by_kode($move_id_rm);

                //lokasi tujuan rm
                $lokasi_rm = $this->_module->get_location_by_move_id($move_id_rm)->row_array();

                $get_ro = $this->m_mo->get_row_order_rm_hasil($kode)->row_array();
                $row_order_rm= $get_ro['row']+1;
                foreach ($array_rm as $row) {
                 
                     if($row['qty_konsum'] > 0 AND $row['qty_konsum'] != ''){                       
                        
                        if($row['qty_konsum']<$row['qty_smi']){//jika qty_konsum kurang dari qty stock_move_items

                            //update qty stock_quant dan stock move items by quant_id
                            $qty_new = $row['qty_smi'] - $row['qty_konsum'];
                            $case   .= "when quant_id = '".$row['quant_id']."' then '".$qty_new."'";
                            $where  .= "'".$row['quant_id']."',";

                            $qty2_new    = ($row['qty2']/$row['qty_smi'])*$row['qty_konsum'];
                            $qty2_update = $row['qty2'] - $qty2_new;
                            $case_qty2  .= "when quant_id = '".$row['quant_id']."' then '".$qty2_update."'";
                            $where_move_items .= "'".$row['move_id']."',";

                            //simpan qty_konsum di stock_quant dan stock_move_items dengan quant_id baru
                            $sql_stock_quant_batch .= "('".$start."','".$tgl."', '".addslashes($row['kode_produk'])."', '".addslashes($row['nama_produk'])."','".addslashes(trim($row['lot']))."','".addslashes($row['grade'])."','".$row['qty_konsum']."','".addslashes($row['uom'])."','".$qty2_new."','".addslashes($row['uom2'])."','".$lokasi_rm['lokasi_tujuan']."','".addslashes($row['reff_note'])."','".$row['move_id']."','".$origin_mo."','".$tgl."','".addslashes($row['lbr_greige'])."','".addslashes($row['uom_lbr_greige'])."','".addslashes($row['lbr_jadi'])."','".addslashes($row['uom_lbr_jadi'])."', '".addslashes($row['sales_order'])."','".addslashes($row['sales_group'])."'), ";
                                            
                            $sql_stock_move_items_batch .= "('".$row['move_id']."', '".$start."','".addslashes($row['kode_produk'])."', '".addslashes($row['nama_produk'])."','".addslashes(trim($row['lot']))."','".$row['qty_konsum']."','".addslashes($row['uom'])."','".$qty2_new."','".addslashes($row['uom2'])."','".$status_done."','".$row_order."','".addslashes($row['origin_prod'])."', '".$tgl."','','".addslashes($row['lbr_greige'])."','".addslashes($row['uom_lbr_greige'])."','".addslashes($row['lbr_jadi'])."','".addslashes($row['uom_lbr_jadi'])."'), ";

                            $sql_mrp_production_rm_hasil .= "('".$row['kode']."','".$row['move_id']."','".addslashes($row['kode_produk'])."','".addslashes($row['nama_produk'])."','".addslashes($row['lot'])."','".$row['qty_konsum']."','".addslashes($row['uom'])."','".addslashes($row['origin_prod'])."','".$row_order_rm."','".$start."'), ";

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

                            $sql_mrp_production_rm_hasil .= "('".$row['kode']."','".$row['move_id']."','".addslashes($row['kode_produk'])."','".addslashes($row['nama_produk'])."','".addslashes($row['lot'])."','".$row['qty_konsum']."','".addslashes($row['uom'])."','".addslashes($row['origin_prod'])."','".$row_order_rm."','".$row['quant_id']."'), ";

                        }
                        $row_order_rm++;

                    }
                  
                }//foreach array_rm
            }

            //** END konsumsi Bahan **\\


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

        }

        echo json_encode($callback);
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

            $kode       = $this->input->post('kode');
            $qty_target = $this->input->post('qty_target');
            $move_id    = $this->input->post('move_id');
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

            if(!empty($cek3['status'])){
                $callback = array('status' => 'failed', 'message'=>'Maaf, Status MO Sudah Done !', 'icon' => 'fa fa-warning', 'type'=>'danger');
            }else if(!empty($cek4['status'])){
                $callback = array('status' => 'failed', 'message'=>'Maaf, Status MO Batal !', 'icon' => 'fa fa-warning', 'type'=>'danger');

            }else if(empty($cek_no_mesin['mc_id'])){
                //$done = false;
                $callback = array('status' => 'failed', 'message'=>'Maaf, No Mesin Harus Diisi !', 'icon' => 'fa fa-warning', 'type'=>'danger');

            }else if(!empty($cek['status'])){
                //$done = false;
                $callback = array('status' => 'failed', 'message'=>'Maaf, Bahan baku belum habis !', 'icon' => 'fa fa-warning', 'type'=>'danger');

            }else if($cek2['sum_qty'] < $qty_target){
                //$done = false;
                $callback = array('status' => 'failed', 'message'=>'Maaf, Qty target belum Terpenuhi !', 'icon' => 'fa fa-warning', 'type'=>'danger');
            }else{
                
                $this->m_mo->update_status_mrp_production($kode,$status);
                $this->_module->update_status_stock_move($move_id,$status);
                $jenis_log   = "edit";
                $note_log    = "Done ". $kode;
                $this->_module->gen_history_deptid($sub_menu, $kode, $jenis_log, $note_log, $username, $deptid);   
                $callback = array('status' => 'success', 'message'=>'Status Berhasil di Ubah !', 'icon' => 'fa fa-check', 'type'=>'success');
                
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

            if(!empty($cek1['status'])){
                $callback = array('status' => 'failed', 'message'=>'Maaf, Data Tidak Bisa Disimpan, Status MO Sudah Done !', 'icon' => 'fa fa-warning', 'type'=>'danger');
            }else if(!empty($cek2['status'])){
                $callback = array('status' => 'failed', 'message'=>'Maaf, Data Tidak Bisa Disimpan, Status MO Batal !', 'icon' => 'fa fa-warning', 'type'=>'danger');
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

            $orgn     = $origin_mo."|".$kode; // ex SO18|CO7|2|OW210300001|MG210300004
            
            $status_kain = $this->m_mo->cek_status_produk_kain($kode)->row_array();
            if(!empty($status_kain['status'])){

                $cek_request  = $this->m_mo->cek_origin_di_stock_move($orgn)->row_array();//cek apa sudah request obat ?
                if(empty($cek_request['origin'])){

                    $cek_ba = $this->m_mo->cek_berat_air($kode)->row_array();
                    if($cek_ba['berat'] > 0 AND $cek_ba['air'] > 0 ){

                        //lock table
                        $this->_module->lock_tabel('warna WRITE, warna_items WRITE, mrp_route WRITE, stock_move WRITE, stock_move_produk WRITE, penerimaan_barang WRITE, penerimaan_barang_items WRITE, pengiriman_barang WRITE, pengiriman_barang_items WRITE, mrp_production_rm_target WRITE, mrp_production WRITE');
                        
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
                            $sql_rm_target_batch        = "";
                            $arr_kode[]                 = "";
                            $kode_out[]                 = "";

                            $route = $this->m_mo->get_route_warna('obat_dyeing');
                            $kode_warna  = $this->m_mo->get_warna_items_by_warna($warna);
                            $get_row = $this->m_mo->get_row_order_rm_target($kode)->row_array();//get last_order di mrp rm target
                            $rm_row  = $get_row['row']+1;
                            $ba      = $this->m_mo->get_berat_air_by_kode($kode)->row_array();
                            $sm_row  = 1;///stock move row_order
                            $empty_item = TRUE;

                            foreach($route as $val){
                          
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
                                }

                                $last_num_origin = 1;

                                foreach($kode_warna as $row){
                                    $empty_item = FALSE;

                                    $kode_prod  = $row->kode_produk;
                                    $nama_prod  = $row->nama_produk;
                                    $qty        = $row->qty;
                                    $uom        = $row->uom;
                                    $reff_notes = $row->reff_note;
                                    $type_obat  = $row->type_obat;

                                    if($type_obat =='DYE'){
                                        $qty_asli  = $qty*$ba['berat']*10;
                                    }else if($type_obat == 'AUX'){
                                        $qty_asli  = $qty*$ba['air'];
                                    }

                                    if($method_action =='CON'){
                                        $origin_prod = $kode_prod.'_'.$last_num_origin;
                                    }else{
                                        $origin_prod = '';
                                    }

                                    //stock move produk
                                    $sql_stock_move_produk_batch .= "('".$move_id."','".$kode_prod."','".$nama_prod."','".$qty_asli."','".$uom."','draft','".$smp_row."','".$origin_prod."'), ";

                                    if($method_action == 'OUT'){//pengiriman barang

                                        $sql_out_items_batch .= "('".$kode_out."','".$kode_prod."','".$nama_prod."','".$qty_asli."','".$uom."','draft','".$smp_row."',''), ";

                                        //update reff notes pengiriman 
                                        $case2  .= "when kode = '".$kode_out."' then '".$reff_notes."'";
                                        $where2 .= "'".$kode_out."',";
                                    
                                    }else if($method_action =='IN'){//penerimaan barang
                                       
                                        $sql_in_items_batch   .= "('".$kode_in."','".$kode_prod."','".$nama_prod."','".$qty_asli."','".$uom."','draft','".$smp_row."'), "; 
                                        //update reff notes penerimaan
                                        $case3 .= "when kode = '".$kode_in."' then '".$reff_notes."'";
                                        $where3 .= "'".$kode_in."',";

                                    }else if($method_action =='CON'){

                                        $sql_rm_target_batch  .= "('".$kode."','".$move_id."','".$kode_prod."','".$nama_prod."','".$qty_asli."','".$uom."','".$rm_row."','".$origin_prod."','draft'), ";
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

                            if($empty_item == TRUE){
                                //action sql query
                                $callback = array('message' => 'Maaf, Resep Obat Dyeing Stuff atau AUX masih belum tersedia ! ',  'status' => 'failed' );

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

                                }

                                if(!empty($sql_rm_target_batch)){
                                    $sql_rm_target_batch = rtrim($sql_rm_target_batch, ', ');
                                   $this->m_mo->save_obat($sql_rm_target_batch);
                                }

                                $this->m_lab->update_status_warna($warna,'requested');

                                // get nama warna by id
                                $nama_warna  = $this->m_mo->get_nama_warna_by_id($warna);

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

            }else{
                $callback = array('message' => 'Maaf, Produk kain belum Ready !',  'status' => 'failed' );
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
            $lot_prefix         = addslashes($this->input->post('lot_prefix'));
            $lot_prefix_waste   = $this->input->post('lot_prefix_waste');
            $lebar_greige       = addslashes($this->input->post('lebar_greige'));
            $uom_lebar_greige   = addslashes($this->input->post('uom_lebar_greige'));
            $lebar_jadi         = addslashes($this->input->post('lebar_jadi'));
            $uom_lebar_jadi     = addslashes($this->input->post('uom_lebar_jadi'));

            $show_lebar = $this->_module->cek_show_lebar_by_dept_id($deptid)->row_array();

            //cek status mrp_production = done
            $cek1  = $this->m_mo->cek_status_mrp_production($kode,'done')->row_array();
            //cek status mrp_production = cancel
            $cek2  = $this->m_mo->cek_status_mrp_production($kode,'cancel')->row_array();

            if(!empty($cek1['status'])){
                $callback = array('status' => 'failed', 'message'=>'Maaf, Data Tidak Bisa Diubah, Status MO Sudah Done !', 'icon' => 'fa fa-warning', 'type'=>'danger');
            }else if(!empty($cek2['status'])){
                $callback = array('status' => 'failed', 'message'=>'Maaf, Data Tidak Bisa Diubah, Status MO Batal !', 'icon' => 'fa fa-warning', 'type'=>'danger');
            }else{

                if($berat == '0'  AND $type_mo == 'colouring' ){
                     $callback = array('status' => 'failed', 'field' => 'berat', 'message' => 'Berat Harus Diisi !', 'icon' =>'fa fa-warning',    'type' => 'danger' );    
                $air    = $this->input->post('air');
                }else if($air == '0' AND $type_mo == 'colouring'){
                     $callback = array('status' => 'failed', 'field' => 'air', 'message' => 'Air Harus Diisi !', 'icon' =>'fa fa-warning',    'type' => 'danger' );    
                }else if(empty($start)){
                     $callback = array('status' => 'failed', 'field' => 'start', 'message' => 'Start Time Harus Diisi !', 'icon' =>'fa fa-warning',    'type' => 'danger' );    
                }else if(empty($finish)){
                     $callback = array('status' => 'failed', 'field' => 'finish', 'message' => 'Finish Time Harus Diisi !', 'icon' =>'fa fa-warning',    'type' => 'danger' );    
                }else if(empty($reff_note)){
                     $callback = array('status' => 'failed', 'field' => 'note', 'message' => 'Reff Note Harus Diisi !', 'icon' =>'fa fa-warning',    'type' => 'danger' );    
                }else if(empty($mesin)){
                     $callback = array('status' => 'failed', 'field' => 'mc', 'message' => 'No Mesin Harus Diisi !', 'icon' =>'fa fa-warning',    'type' => 'danger' );    
                }else if(empty($type_production)){
                    $callback = array('status' => 'failed', 'field' => 'type_production', 'message' => 'Type Production Harus Diisi !', 'icon' =>'fa fa-warning',    'type' => 'danger' );    
               }else{

                    if($deptid == 'TRI' OR $deptid == 'JAC'){
                        $lot_prefix = '';
                    }

                    $this->m_mo->update_mo($kode,$berat,$air,$start,$finish,$reff_note,$mesin,$qty1_std,$qty2_std,$lot_prefix,$lot_prefix_waste,$target_efisiensi,$lebar_greige,$uom_lebar_greige,$lebar_jadi,$uom_lebar_jadi,$type_production);
                    
                    if($show_lebar['show_lebar'] == 'true'){
                        $lebar = $lebar_greige."  ".$uom_lebar_greige." | ".$lebar_jadi."  ".$uom_lebar_jadi." | ";
                    }else{
                        $lebar = '';
                    }

                    if($deptid == 'TRI' OR $deptid == 'JAC'){
                        $lot_prefix = 'Format Lot Prefix Default System';
                    }
                    
                    $mc = $this->m_mo->get_nama_mesin_by_kode($mesin)->row_array();
                    $nama_mesin = $mc['nama_mesin'];
                    
                    $jenis_log   = "edit";
                    if($type_mo == 'colouring'){                    
                        $note_log    = "-> ".$lebar." ".$berat." | ".$air." | ".$finish." | ".$start." | ".$reff_note." | ".$nama_mesin." | ".$target_efisiensi." | ".$qty1_std." | ".$qty2_std." | ".$type_production." | ".$lot_prefix." | ".$lot_prefix_waste ;
                    }else{
                        $note_log    = "-> ".$lebar." ".$finish." | ".$start." | ".$reff_note." | ".$nama_mesin." | ".$target_efisiensi." | ".$qty1_std." | ".$qty2_std." | ".$type_production." | ".$lot_prefix." | ".$lot_prefix_waste ; 
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
            $move_id    = $this->input->post('move_id');//move_id rm_target
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
            if(!empty($cek1['status'])){
                $callback = array('status' => 'failed', 'message'=>'Maaf, Tidak Bisa Cek Stok, Status MO Sudah Done !', 'icon' => 'fa fa-warning', 'type'=>'danger');

            }else if(!empty($cek2['status'])){
                $callback = array('status' => 'failed', 'message'=>'Maaf, Tidak Bisa Cek Stok, Status MO Batal !', 'icon' => 'fa fa-warning', 'type'=>'danger');
            }else{
                    //lock tabel
                    $this->_module->lock_tabel('stock_quant WRITE, stock_move_items WRITE,stock_move WRITE,stock_move_produk WRITE, mrp_production WRITE, mrp_production_rm_target WRITE, mrp_production_rm_target rm WRITE, mst_produk mp WRITE, stock_move_items smi WRITE' );

                    //get row order stock_move_items
                    $row_order  = $this->_module->get_row_order_stock_move_items_by_kode($move_id);
                    //lokasi tujuan, lokasi dari
                    $lokasi = $this->m_mo->get_location_by_mo($kode)->row_array();

                    $list  = $this->m_mo->get_list_bahan_baku_stok($kode);//get list bahan baku yang type stockable
                    foreach ($list as $val) {
                        $bahan_baku  = true; 
                        $kode_produk = $val->kode_produk;
                        $nama_produk = $val->nama_produk;
                        $qty         = $val->qty;
                        $uom         = $val->uom;
                        $origin_prod = $val->origin_prod;

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
                                    $sql_stock_move_items_batch .= "('".$move_id."', '".$start."','".addslashes($kode_produk)."', '".addslashes($nama_produk)."','".addslashes(trim($stock['lot']))."','".$kebutuhan_qty."','".addslashes($uom)."','".$stock['qty2']."','".addslashes($stock['uom2'])."','".$status_brg."','".$row_order."','".addslashes($origin_prod)."', '".$tgl."','".addslashes($stock['lokasi_fisik'])."','".addslashes($stock['lebar_greige'])."','".addslashes($stock['uom_lebar_greige'])."','".addslashes($stock['lebar_jadi'])."','".addslashes($stock['uom_lebar_jadi'])."'), ";
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
                                        // jika qty smi sama atau kurang dari qty_lebih
                                        $qty_lebih = round($qty_lebih,2) - round($val['qty'],2);

                                        // reserve_move jadi kosong di tbl stock_quant
                                        $case6  .= "when quant_id = '".$val['quant_id']."' then '' ";
                                        $where6 .= "'".$val['quant_id']."',";

                                        // hapus stock_move_items by move_id, quant_id
                                        $where_del1 .= "'".$val['move_id']."',"; // move_id
                                        $where_del2 .= "'".$val['quant_id']."',"; // quant_id


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
                          $sql_update_status_rm_target = "UPDATE mrp_production_rm_target SET status =(case ".$case3." end) WHERE  origin_prod in (".$where3.") AND kode = '".$kode."' ";
                          $this->_module->update_perbatch($sql_update_status_rm_target);

                          $sql_update_status_stock_move_produk = "UPDATE stock_move_produk SET status =(case ".$case3." end) WHERE  origin_prod in (".$where3.") AND move_id = '".$move_id."' ";
                          $this->_module->update_perbatch($sql_update_status_stock_move_produk);
                          $where3 = "";
                          $case3  = "";
                          $sql_update_status_stock_move_produk ="";
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

                   
                    if($type_mo == 'colouring' AND $deptid != 'DYE'){
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
                                'Maaf, Qty Product "'.  $produk_kurang  .'" tidak mencukupi ! <br> Maaf, Qty Product "'.  $produk_kosong  .'" Kosong !', 'icon' => 'fa fa-warning', 'type'=>'danger');
                        }else if(!empty($produk_kosong)){
                            $callback = array('status' => 'failed', 'message'=> 
                                'Maaf, Qty Product "'.  $produk_kosong  .'" Kosong !', 'icon' => 'fa fa-warning', 'type'=>'danger');

                        }else if(!empty($produk_kurang) ){                       
                            $callback = array('status' => 'failed', 'message'=> 
                                'Maaf, Qty Product "'.  $produk_kurang  .'" tidak mencukupi !', 'icon' => 'fa fa-warning', 'type'=>'danger', 'status_kurang' => 'yes',  'message2'=>'Detail Product Berhasil Ditambahkan !', 'icon2' => 'fa fa-check', 'type2'=>'success');
                                                                          
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
                        $note_log    = "Cek Stok";
                      }
                      if($history_split == true){
                        $note_log    = "Cek Stok Split ";
                      }
                       if($history == true AND $history_split == true){
                        $note_log  = "Cek Stok dan Cek Stok Split";
                      }
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
        $kode       = $this->input->post('kode'); //kode MO untu log history
        $kode_produk= $this->input->post('kode_produk');
        $nama_produk= $this->input->post('nama_produk');

        $data['kode']        = $kode;
        $data['deptid']      = $deptid;
        $data['kode_produk'] = $kode_produk;
        $data['nama_produk'] = $nama_produk;
        $data['quant']       = $this->m_mo->get_view_quant_by_kode($move_id,addslashes($origin_prod));
        return $this->load->view('modal/v_mo_quant_modal', $data);
    }


    public function view_mo_rm_hasil()
    {
        $kode        = $this->input->post('kode');
        $kode_produk = $this->input->post('kode_produk');
        $nama_produk = $this->input->post('nama_produk');
        $data['kode_produk'] = $kode_produk;
        $data['nama_produk'] = $nama_produk;
        $data['list_rm_hasil'] = $this->m_mo->get_list_bahan_baku_hasil($kode,addslashes($kode_produk));
        return $this->load->view('modal/v_mo_rm_hasil_modal', $data);
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
            $kode       = $this->input->post('kode');
            $status_brg = 'draft';
            
            //lock tabel
            $this->_module->lock_tabel('stock_quant WRITE, stock_move WRITE,stock_move_items WRITE,stock_move_produk WRITE, mrp_production_rm_target WRITE, mst_produk mp WRITE, mrp_production_rm_target rm WRITE, mrp_production WRITE' );
            
               //cek status mrp_production = done
            $cek1  = $this->m_mo->cek_status_mrp_production($kode,'done')->row_array();
            //cek status mrp_production = cancel
            $cek2  = $this->m_mo->cek_status_mrp_production($kode,'cancel')->row_array();

            if(!empty($cek1['status'])){
                $callback = array('status' => 'failed', 'message'=>'Maaf, Data Tidak Bisa Dihapus, Status MO Sudah Done !', 'icon' => 'fa fa-warning', 'type'=>'danger');
            }else if(!empty($cek2['status'])){
                $callback = array('status' => 'failed', 'message'=>'Maaf, Data Tidak Bisa Dihapus, Status MO Batal !', 'icon' => 'fa fa-warning', 'type'=>'danger');
            }else{

                //delete stock move item dan update reserve move jadi kosong
                $this->_module->delete_details_items($move_id,$quant_id,$row_order);

                //get sum qty produk stock move items yg statusnya ready
                $get_qty2  = $this->_module->get_qty_stock_move_items_mo_by_kode($move_id,addslashes($origin_prod),'ready')->row_array();

                //update status draft jika qty di stock move items kosong
                if(empty($get_qty2['sum_qty'])){
                  $this->m_mo->update_status_mrp_production_rm_target($kode,addslashes($origin_prod),$status_brg);
                  $this->m_mo->update_status_stock_move_produk_mo($move_id,addslashes($origin_prod),$status_brg);
                }

                //cek apa ada ada produk yang statusnya ready atau done?
                $cek_status = $this->m_mo->cek_status_barang_mrp_production_rm_target($kode,'ready', 'done')->row_array();
                if(empty($cek_status['status'])){//jika kosong maka update status mrp_production jadi draft
                    $this->m_mo->update_status_mrp_production($kode,$status_brg);
                    $cek_status2 = $this->m_mo->cek_status_mrp_production($kode,'')->row_array();
                    if($cek_status2['status']=='draft'){
                        $this->_module->update_status_stock_move($move_id,$status_brg);
                    }
                }
                
                //unlock table
                $this->_module->unlock_tabel();
                
                $jenis_log   = "cancel";
                $note_log    = "Hapus Data Details ". $quant_id.'|'.$origin_prod;
                $this->_module->gen_history_deptid($sub_menu, $kode, $jenis_log, $note_log, $username,$deptid);
                
                $callback = array('status' => 'success', 'message'=>'Data Berhasil di hapus !', 'icon' => 'fa fa-check', 'type'=>'success');
            }
        }
        echo  json_encode($callback);
    }

    public function tambah_data_details_quant_mo()
    {
        $kode_produk  = $this->input->post('kode_produk');
        $move_id      = $this->input->post('move_id');
        $deptid       = $this->input->post('deptid');
        $origin_prod  = $this->input->post('origin_prod');

        $data['kode_produk'] = $kode_produk;
        $data['move_id'] = $move_id;
        $data['deptid']  = $deptid;
        $data['origin_prod']  = $origin_prod;
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
          $this->_module->lock_tabel('stock_quant WRITE, stock_move_items WRITE,stock_move WRITE,stock_move_produk WRITE, mrp_production WRITE, mrp_production_rm_target WRITE,  mrp_production_rm_target rm WRITE, mst_produk mp WRITE, departemen WRITE' );
          
          //cek status mrp_production = done
          $cek1  = $this->m_mo->cek_status_mrp_production($kode,'done')->row_array();
          //cek status mrp_production = cancel
          $cek2  = $this->m_mo->cek_status_mrp_production($kode,'cancel')->row_array();

          if(!empty($cek1['status'])){
            $callback = array('status' => 'failed', 'message'=>'Maaf, Data Tidak Bisa Disimpan, Status MO Sudah Done !', 'icon' => 'fa fa-warning', 'type'=>'danger');
          }else if(!empty($cek2['status'])){
                $callback = array('status' => 'failed', 'message'=>'Maaf, Data Tidak Bisa Disimpan, Status MO Batal !', 'icon' => 'fa fa-warning', 'type'=>'danger');
          }else{
          
              //get row order stock_move_items
              $row_order  = $this->_module->get_row_order_stock_move_items_by_kode($move_id);
              //get_lokasi dari by move id 
              $location = $this->_module->get_location_by_move_id($move_id)->row_array();

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
                    if(!empty($cq['quant_id'])){
                          //insert ke stock move items
                        $sql_stock_move_items_batch .= "('".$move_id."', '".$quantid."','".addslashes($kode_produk)."', '".addslashes($nama_produk)."','".addslashes(trim($lot))."','".$qty."','".addslashes($uom)."','".$qty2."','".addslashes($uom2)."','ready','".$row_order."','".addslashes($origin_prod)."', '".$tgl."','".$lokasi_fisik."','".$lebar_greige."','".$uom_lebar_greige."','".$lebar_jadi."','".$uom_lebar_jadi."'), ";     
                        $row_order++;           

                        //update reserve move by quant id di stok quant                
                        $case       .= "when quant_id = '".$quantid."' then '".$move_id."'";
                        $where      .= "'".$quantid."',";

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
                
                  if(!empty($case)){
                    //update stock quant 
                    $where = rtrim($where, ',');
                    $sql_update_stock_quant  = "UPDATE stock_quant SET reserve_move =(case ".$case." end) WHERE  quant_id in (".$where.") ";
                    $this->_module->update_perbatch($sql_update_stock_quant);
                  }

                  $this->m_mo->update_status_mrp_production_rm_target($kode,addslashes($origin_prod),$status_brg);  
                  // cek type mo
                  $to    = $this->m_mo->cek_type_mo_by_dept_id($deptid)->row_array();
                  if($to['type_mo'] != 'colouring' AND $deptid != 'DYE') {

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
                $note_log    = "Tambah Data Details ".$origin_prod;
                $this->_module->gen_history_deptid($sub_menu, $kode, $jenis_log, addslashes($note_log), $username, $deptid);
                $callback    = array('status'=>'success',  'message' => 'Detail Product Berhasil Ditambahkan !',  'icon' =>'fa fa-check', 'type' => 'success'); 
              }else{
                $callback    = array('status'=>'kosong',  'message' => 'Maaf, Product Sudah ada yang terpakai !',  'icon' =>'fa fa-check', 'type' => 'danger');  
              }
          }           
            
        }
        echo json_encode($callback);
    }

  
    public function print_barcode()
    {
        $data_arr  = $this->input->get('checkboxBarcode');  
        $count     = $this->input->get('countchek'); 
        $kode      = $this->input->get('kode');
        $dept_id   = $this->input->get('dept_id');

        if($dept_id == 'TWS'){
            $this->barcode_tws($kode,$data_arr,$dept_id);

        }else if($dept_id == 'WRD'){
            $this->barcode_wrd($kode,$data_arr,$dept_id);

        }else if($dept_id == 'WRP'){
            $this->barcode_wrp($kode,$data_arr,$dept_id);
        
        }else if($dept_id == 'TRI'){
            $this->barcode_tri($data_arr,$count);

        }else if($dept_id == 'INS1'){
            $this->barcode_ins1($data_arr);

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

        $data_arr2   = rtrim($data_arr,'|^,');//empty |^
        $row         = explode("|^,", $data_arr2);
        $loop          = 1;

        // get mesin by kode
        $get_mc = $this->m_mo->get_mesin_by_mo($kode)->row_array();
        $mesin  = $get_mc['nama_mesin'];
        
        //get origin_mo
        $origin_mo  = $this->m_mo->get_origin_mo_by_kode($kode);
        $method= $dept_id.'|OUT';
        
        foreach ($row as $val ) {
      
            if($loop == 2){
                $pdf->AddPage();
                $loop = 1;
            }

            $items    = explode("^^",$val);
            $barcode  = $items[0];

            // get reff picking by kode
            $reff_picking  = $this->m_mo->get_reff_picking_pengiriman_by_kode($barcode, $method, $origin_mo);

            //get produk,qty by kode
            $get = $this->m_mo->get_data_fg_hasil_by_kode($kode,$barcode)->row_array();

            $nama_produk = $get['nama_produk'];
            $qty         = $get['qty'];
            $uom         = $get['uom'];
            $tgl         = $get['create_date'];
            $reff_note   = $get['reff_note'];
            $note_head   = $get['note_head'];

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

            $pdf->SetFont('Arial','',12,'C'); // set font

            $pdf->setXY(3,12);
            $pdf->Multicell(74,5,"Qty : ".$qty." ".$uom,0,'L'); // qty

            $pdf->setXY(3,17);
            $pdf->Multicell(74,5,"MC : ".$mesin,0,'L');// MC TWS

            $pdf->setXY(3,22);
            $pdf->Multicell(30,5,"Tgl.HPH   :",0,'L');// Tgl buat/hph

            $pdf->setXY(24,22);
            $pdf->Multicell(60,5," ".$tgl,0,'L');// isi Tgl buat/hph

            $pdf->setXY(3,27);
            $pdf->Multicell(74,5,"Reff Note : ".$reff_note,0,'L');// reff note

            $pdf->setXY(3,33);
            if($nh_mc != ''){
                $nh_mc = ' - '.$nh_mc;
            }

            $pdf->SetFont('Arial','B',12,'C'); // set font
            $pdf->Multicell(74,5,"Dept Tujuan : ".$nh_dept.''.$nh_mc,0,'L');// Departemen Tujuan
            
            $pdf->setXY(3,38);
            $pdf->Multicell(74,5,"MO Tujuan   : ".$nh_mo,0,'L');// MO Tujuan
            
            $pdf->SetFont('Arial','B',8,'C'); // set font
            $pdf->setXY(3,42);
            $pdf->Multicell(75,5,"Reff Picking : ".$reff_picking,0,'L');// reff picking pengiriman barang
            
            $pdf->Code128(5,47,$barcode,70,8,'C',0,1); // barcode

            $pdf->SetFont('Arial','B',8,'C'); // set font
            $pdf->setXY(0,54);
            $pdf->Multicell(80,5,$barcode.' - Barcode Twisting',0,'C');// barcode departement

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

        $data_arr2   = rtrim($data_arr,'|^,');//empty |^
        $row         = explode("|^,", $data_arr2);
        $loop        = 1;

        //get origin_mo
        $origin_mo  = $this->m_mo->get_origin_mo_by_kode($kode);
        $method= $dept_id.'|OUT';

        // get mesin by kode
        //$get_mc = $this->m_mo->get_mesin_by_mo($kode)->row_array();
        //$mesin  = $get_mc['nama_mesin'];

        foreach ($row as $val ) {

            if($loop == 2){
                $pdf->AddPage();
                $loop = 1;
            }

            $items    = explode("^^",$val);
            $barcode  = $items[0];

            // get reff picking by kode
            $reff_picking  = $this->m_mo->get_reff_picking_pengiriman_by_kode($barcode, $method, $origin_mo);

            //get produk,qty by kode
            $get = $this->m_mo->get_data_fg_hasil_by_kode($kode,$barcode)->row_array();

            $nama_produk = $get['nama_produk'];
            $qty         = $get['qty'];
            $uom         = $get['uom'];
            $qty2        = $get['qty2'];
            $uom2        = $get['uom2'];
            $tgl         = $get['create_date'];
            $reff_note   = $get['reff_note'];
            /*
                Format reff note dari PPIC
                1. SC
                2. MO Jac
                3. MC JAC
                4. Corak JAC
                5. Jenis Benang
                Contoh Penulisan Reff NOte 
                SC1896 | MO211000406 | MC222 | 7P1514 | NYLON 70/6 TEXT
            */
            $note_head   = $get['note_head']; 

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

            $pdf->SetFont('Arial','',12,'C');

            $pdf->setXY(3,14);
            $pdf->Multicell(74,5,"Qty : ".round($qty,2)." ".$uom.", Qty2 : ".round($qty2,2)." ".$uom2,0,'L'); // qty

            $pdf->setXY(3,19);
            $pdf->Multicell(74,5,"Tgl.HPH : ",0,'L');// Tgl buat/hph

            $pdf->setXY(24,19);
            $pdf->Multicell(60,5," ".$tgl,0,'L');// isi Tgl buat/hph

            $pdf->SetFont('Arial','B',12,'C'); // set font

            $pdf->setXY(3,27);
            $pdf->Multicell(74,5,"SC : ".$nh_sc,0,'L');// reff note

            $pdf->setXY(3,32);
            if($nh_mc != ''){
                $nh_mc = ' - '.$nh_mc;
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

            $pdf->Multicell(74,5,"Dept Tujuan : JAC".$nh_mc."".$reff_note,0,'L');// Departemen Tujuan

            $pdf->setXY(3,37);
            $pdf->Multicell(74,5,"MO Tujuan   : ".$nh_mo,0,'L');// MO Tujuan

            $pdf->SetFont('Arial','B',8,'C'); // set font
            $pdf->setXY(3,43);
            $pdf->Multicell(75,5,"Reff Picking : ".$reff_picking,0,'L');// reff picking pengiriman barang

            $pdf->Code128(5,47,$barcode,70,8,'C',0,1); // barcode
            
            $pdf->SetFont('Arial','B',8,'C'); // set font
            $pdf->setXY(0,54);
            $pdf->Multicell(80,5,$barcode.' - Barcode Warping Panjang',0,'C');// barcode
       
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


        $data_arr2   = rtrim($data_arr,'|^,');//empty |^
        $row   = explode("|^,", $data_arr2);
        // $pdf->setXY(1,2);
        //$cellWidth     = 60;
        //$offset_length = FALSE;

        //get origin_mo
        $origin_mo  = $this->m_mo->get_origin_mo_by_kode($kode);
        $method= $dept_id.'|OUT';

        $loop = 1;
        $heightNama = 0; 
        $enter         = 1;
        $enter_barcode = 13;
        //$pdf->setXY(1,2);
        //$cellWidth     = 60;

        foreach ($row as $val ) {
            //$pdf->Cell($width,$height,$val,0,0,'R');
            //$pdf->Cell($width,$height,'tes',0,0,'R');

            if($loop == 3){
                $pdf->AddPage();
                $loop = 1;
                $heightNama = 0; 
                $enter         = 1;
                $enter_barcode = 13;

            }

            $items    = explode("^^",$val);
            $barcode  = $items[0];

            // get reff picking by kode
            $reff_picking  = $this->m_mo->get_reff_picking_pengiriman_by_kode($barcode, $method, $origin_mo);

            $pdf->setXY(0,3+$heightNama);
            $pdf->Multicell(60,5,$barcode,0,'C');
            
            $pdf->SetFont('Arial','B',8,'C');
            $pdf->setXY(0,5+$heightNama+3);
            $pdf->Multicell(60,3,'Reff Picking : '.$reff_picking,0,'C');

            $pdf->Code128(5,$enter+$enter_barcode,$barcode,50,10,'C',0,1);//barcode

            $pdf->SetFont('Arial','B',15,'C');

            $pdf->setXY(0,5+$heightNama+20);
            $pdf->Multicell(60,5,$barcode,0,'C');
            $heightNama    = $heightNama + 40;
            $enter_barcode = $enter_barcode + 40;
            
        $loop++;
        }

        $pdf->output();

    }


    function barcode_tri($data_arr,$count)
    {
       
        $pdf=new PDF_Code128('l','mm',array(177.8,101.6));

        $pdf->AddPage();

        $data_arr2   = rtrim($data_arr,'|^,');//empty |^
        $row   = explode("|^,", $data_arr2);
        $loop  = 1;


        foreach ($row as $val) {

            if($loop == 2){
                $pdf->AddPage();
                $loop = 1;
            }
            
            $items    = explode("^^",$val);
            $no_itm   = 0;
            $barcode  = '';
            $nama_grade = '';
            foreach($items as $itm){
                if($no_itm == 0 ){
                    $barcode  = $itm;
                }
                if($no_itm == 1){
                    $nama_grade = $itm;
                }
                $no_itm++;
            }


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

            $loop++;
        }


        $pdf->Output();
    }


    function barcode_ins1($data_arr)
    {
       
        $pdf=new PDF_Code128('l','mm',array(177.8,101.6));

        $pdf->AddPage();

        $data_arr2   = rtrim($data_arr,'|^,');//empty |^
        $row   = explode("|^,", $data_arr2);
        $loop  = 1;


        foreach ($row as $val) {

            if($loop == 2){
                $pdf->AddPage();
                $loop = 1;
            }
            
            $items    = explode("^^",$val);
            $no_itm   = 0;
            $barcode  = '';
            $nama_grade = '';
            foreach($items as $itm){
                if($no_itm == 0 ){
                    $barcode  = $itm;
                }
                if($no_itm == 1){
                    $nama_grade = $itm;
                }
                $no_itm++;
            }


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

            $loop++;
        }


        $pdf->Output();
    }


}