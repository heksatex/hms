<?php defined('BASEPATH') OR EXIT ('No Direct Script Acces Allowed');
/**
 * 
 */
class Colororder extends MY_Controller
{
	public function __construct()
	{
		parent:: __construct();
		$this->is_loggedin();//cek apakah user sudah login
    $this->load->model('m_colorOrder');
		$this->load->model('_module');
	}

	public function index()
	{	
		//$data["color"] = $this->m_colorOrder->getAll();
    $data['id_dept']='CO';
		$this->load->view('ppic/v_colorOrder', $data);
	}

	function get_data()
  {
        $sub_menu  = $this->uri->segment(2);
        $kode = $this->_module->get_kode_sub_menu($sub_menu)->row_array();
        $list = $this->m_colorOrder->get_datatables($kode['kode']);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $field) {
            $kode_encrypt = encrypt_url($field->kode_co);
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = '<a href="'.base_url('ppic/colororder/edit/'.$kode_encrypt).'">'.$field->kode_co.'</a>';
            $row[] = $field->tanggal;
            $row[] = $field->kode_sc;
            $row[] = $field->buyer_code;
            $row[] = $field->nama_status;
            $row[] = $field->notes;
 
            $data[] = $row;
        }
 
        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_colorOrder->count_all($kode['kode']),
            "recordsFiltered" => $this->m_colorOrder->count_filtered($kode['kode']),
            "data" => $data,
        );
        //output dalam format JSON
        echo json_encode($output);
  }


    public function add()
    {
        $data['id_dept']  ='CO';
        //$data['handling'] = $this->_module->get_list_handling();
        $data['route']    = $this->m_colorOrder->get_list_route_co();
    	return $this->load->view('ppic/v_colorOrder_add', $data);
    }

    public function simpan()
    {
        $sub_menu  = $this->uri->segment(2);
        $username = $this->session->userdata('username'); 

        if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
            // session habis
            $callback = array('message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
        }else{

          $kode_co    = $this->input->post('kode_co');
          $kode_sc    = $this->input->post('kode_sc');
          $buyer_code = $this->input->post('buyer_code');
          $tgl_sj     = $this->input->post('tgl_sj');
          $note       = $this->input->post('note');
          $tgl        = $this->input->post('tgl');
          $route      = $this->input->post('route');

          if(empty($kode_sc)){
              $callback = array('status' => 'failed', 'field' => 'kode_sc', 'message' => 'Sales Contract Harus Diisi !', 'icon' =>'fa fa-warning',  'type' => 'danger'  );    
          }elseif(empty($buyer_code)){
              $callback = array('status' => 'failed', 'field' => 'buyer_code', 'message' => 'Buyer Code Harus Diisi !', 'icon' =>'fa fa-warning',  'type' => 'danger' );    
          }elseif(empty($tgl_sj)){
              $callback = array('status' => 'failed', 'field' => 'tgl_sj', 'message' => 'Tanggal Kirim / Surat Jalan Harus Diisi !', 'icon' =>'fa fa-warning',  'type' => 'danger' );    
          }elseif(empty($note)){
              $callback = array('status' => 'failed', 'field' => 'note', 'message' => 'Note Harus Diisi !', 'icon' =>'fa fa-warning', 
                  'type' => 'danger' );    
          }elseif(empty($tgl)){
              $callback = array('status' => 'failed', 'field' => 'tgl', 'message' => 'Tanggal Harus Diisi !', 'icon' =>'fa fa-warning', 
                  'type' => 'danger' ); 
          /*   
          }elseif(empty($route)){
              $callback = array('status' => 'failed', 'field' => 'route', 'message' => 'Route Harus Diisi !', 'icon' =>'fa fa-warning', 
                  'type' => 'danger' );    
          */
          }else{
              if(empty($kode_co)){//jika kode co kosong, aksinya simpan data
                  $kode['kode_co'] =  $this->m_colorOrder->kode_co();
                  $kode_encrypt    = encrypt_url($kode['kode_co']);
                  $this->m_colorOrder->simpan($kode['kode_co'], $kode_sc, $buyer_code, $tgl_sj, $note, $tgl, $route);

                  $callback = array('status' => 'success', 'field' => 'kode_co' , 'message' => 'Data Berhasil Disimpan !', 'isi'=> $kode['kode_co'], 'icon' =>'fa fa-check', 'type' => 'success', 'kode_encrypt' => $kode_encrypt);

                  $jenis_log = "create";
                  $note_log  =$kode['kode_co']." | ".$route." | ".$kode_sc." | ".$buyer_code." | ".$tgl_sj." | ".$note;
                  $this->_module->gen_history($sub_menu, $kode['kode_co'], $jenis_log, $note_log, $username);

              }else{//jika kode co ada, aksinya update data
                  $this->m_colorOrder->ubah($kode_co, $kode_sc, $buyer_code, $tgl_sj, $note, $tgl, $route);
                  $callback = array('status' => 'success', 'field' => 'kode_co' , 'message' => 'Data Berhasil Disimpan !', 'isi' => $kode_co, 'icon' =>'fa fa-check', 'type' => 'success');

                  $jenis_log   = "edit";
                  $note_log    = "->".$kode_co." | ".$route." | ".$kode_sc." | ".$buyer_code." | ".$tgl_sj." | ".$note;
                  $this->_module->gen_history($sub_menu, $kode_co, $jenis_log, $note_log, $username);

              }
          }

        }

        echo json_encode($callback);
    }

    /*
    public function hapus($kode_co)
    {
        //$kode_co    = $this->input->post('kode_co');
        if(!isset($kode_co)) show_404();
        $result = $this->m_colorOrder->hapus($kode_co);
        echo json_encode($result);
    }
   */ 

    public function edit($id = null)
    {   
        if(!isset($id)) show_404();
        $kode_decrypt       = decrypt_url($id);
        $data['id_dept']    = 'CO';
        $data['mms']        = $this->_module->get_data_mms_for_log_history('CO');// get mms by dept untuk menu yg beda-beda
        $data["colororder"] = $this->m_colorOrder->get_data_by_code($kode_decrypt);
        $data['detail']     = $this->m_colorOrder->get_data_detail_by_code($kode_decrypt);

        // cek priv akses menu
        $sub_menu           = $this->uri->segment(2);
        $username           = $this->session->userdata('username'); 
        $kode               = $this->_module->get_kode_sub_menu_deptid($sub_menu,'CO')->row_array();
        $data['akses_menu'] = $this->_module->cek_priv_menu_by_user($username,$kode['kode'])->num_rows();

        return $this->load->view('ppic/v_colorOrder_edit',$data);
    }


    public function generate_detail_color_order()
    {

        if(empty($this->session->userdata('status'))) {//cek apakah session masih ada
            // session habis
            $callback = array('status' => 'failed','message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
        }else{
            $sub_menu  = $this->uri->segment(2);
            $username  = addslashes($this->session->userdata('username')); 
            $nu        = $this->_module->get_nama_user($username)->row_array();
            $nama_user = addslashes($nu['nama']);

            $kode_co   = $this->input->post('kode');
            $row       = $this->input->post('row_order');

            $last_number          = 0;
            $last_bom             = 0;
            $last_move            = 0;
            $last_pengiriman      = 0;
            $sql_insert_batch     = "";
            $sql_update_batch     = "";
            $case                 = "";
            $where                = "";
            $case2                = "";
            $where2               = "";
            $case3                = "";
            $where3               = "";
            $case4                = "";
            $where4               = "";
            $case5                = "";
            $where5               = "";
            $sql_bom_batch        ="";
            $sql_bom_items_batch  ="";
            $source_move          = "";
            $sql_stock_move_batch        = "";
            $sql_stock_move_produk_batch = "";
            $sql_out_batch       = "";
            $sql_out_items_batch = "";
            $sql_in_batch        = "";
            $sql_in_items_batch  = "";
            $sql_mrp_prod_batch  = "";
            $sql_mrp_prod_rm_batch="";
            $sql_mrp_prod_fg_batch="";
            $nama_warna           = "";
            $sql_log_history_out  = "";
            $sql_log_history_in   = "";
            $sql_log_history_mrp  = "";
            $source_move_PROD = FALSE;
            $arr_kode[]           = '';
            $kode_out[]           = '';
            $kode_prod_rm_target  = '';
            $nama_prod_rm_target  = '';
            $qty_rm_target        = '';
            $uom_rm_target        = '';
            $sql_insert_log_history = array();
            $ip                   = $this->input->ip_address();

            // lock tabel
            $this->m_colorOrder->lock_tabel('mst_produk WRITE, mrp_route WRITE, mrp_route as mr WRITE, departemen WRITE, departemen as d WRITE, color_order_detail as cod WRITE,color_order_detail WRITE, color_order co WRITE, bom WRITE, bom_items WRITE, stock_move WRITE, stock_move_produk WRITE, penerimaan_barang WRITE, penerimaan_barang_items WRITE, pengiriman_barang WRITE, pengiriman_barang_items WRITE, mrp_production WRITE, mrp_production_rm_target WRITE, mrp_production_fg_target WRITE, Warna WRITE, warna_items WRITE, color_order_detail as b WRITE, color_order as a WRITE, color_order WRITE, log_history WRITE, user WRITE, main_menu_sub WRITE, mst_category WRITE, mst_produk_sub_parent WRITE, mst_produk_parent WRITE, mst_jenis_kain WRITE');

            // cek status color_order_details
            $cek_status = $this->m_colorOrder->cek_status_color_order_details_by_row($kode_co,$row)->row_array();

            if($cek_status['status'] == 'generated'){
                $callback = array('status' => 'failed','message' => 'Maaf, Status Product Sudah Generated !', 'icon' =>'fa fa-check', 'type' => 'danger');

            }else if($cek_status['status'] == 'cancel'){
                $callback = array('status' => 'failed','message' => 'Maaf, Status Product Sudah dibatalkan !', 'icon' =>'fa fa-check', 'type' => 'danger');

            }else if(empty($cek_status['status'])){
                $callback = array('status' => 'failed','message' => 'Maaf, Data yang akan Di Generate Kosong !', 'icon' =>'fa fa-check', 'type' => 'danger');
            }else{ 

              if($cek_status['status'] != 'draft'){
                $callback = array('status' => 'failed','message' => 'Maaf, Status tidak valid !', 'icon' =>'fa fa-check', 'type' => 'danger');
              }else{

                $produk_empty       = FALSE;
                $warna_empty        = FALSE;
                $route_empty        = FALSE;
                $id_parent_empty      = FALSE;
                $id_jenis_kain_empty  = FALSE;
                $id_sub_parent_empty  = FALSE;
                $id_jenis_kain_empty  = FALSE;
                $produk_aktif = TRUE;

             
                $nama_produk_empty  = '';
                $nama_produk_parent_empty     = "";
                $nama_produk_jenis_kain_empty = "";

                $cdi  = $this->m_colorOrder->get_color_order_details_by_row($kode_co,$row);
                foreach ($cdi as $val) {
                  # code...

                  $kode_produk  = $val->kode_produk;
                  $nama_produk  = $val->nama_produk;
                  $id_warna     = $val->id_warna;
                  $route_co     = $val->route_co;

                  $cek_prod2 = $this->_module->cek_produk_by_kode_produk(addslashes($kode_produk))->row_array();
                  if(empty($cek_prod2['nama_produk'])){
                    
                    $nama_produk_empty .= $nama_produk.', ';
                    $produk_empty       = TRUE;
                    break;
                  }

                  $stat_produk = $this->_module->get_status_aktif_by_produk(addslashes($kode_produk))->row_array();// status produk aktif/tidak
                  if($stat_produk['status_produk']!= 't'){
                    $produk_aktif     = FALSE;
                    break;
                  }

                  // nama warna by id_warna
                  $cek_warna = $this->m_colorOrder->cek_warna_by_id_warna($id_warna)->row_array();
                  if(empty($cek_warna['id'])){
                    $nama_warna   = 'Kosong';
                    $warna_empty  = TRUE;
                    break;
                  }

                  // cek route color order
                  $cek_route_co  = $this->m_colorOrder->cek_route_color_order($route_co)->num_rows();
                  if(empty($route_co) OR $cek_route_co == 0){
                    $route_empty = TRUE;
                    break;
                  }

                  // cek product parent
                  $cek_pp    = $this->m_colorOrder->cek_produk_parent_sub_parent_jenis_kain_by_kode_produk(addslashes($kode_produk))->row_array();
                  if(empty($cek_pp['id_parent'])){
                    $id_parent_empty              = TRUE;
                    $nama_produk_parent_empty     = $nama_produk;
                    break;
                  }

                  // cek jenis kain
                  if(empty($cek_pp['id_jenis_kain'])){
                    $id_jenis_kain_empty           = TRUE;
                    $nama_produk_jenis_kain_empty  = $nama_produk;
                    break;
                  }


                }// end loop cek cek

                if($produk_empty == TRUE){
                  $nama_produk_empty = rtrim($nama_produk_empty,', ');
                  $callback = array('status' => 'failed', 'message' => 'Maaf, Produk '.$nama_produk_empty.' Kosong !', 'icon' =>'fa fa-check', 'type' => 'danger');
               
                }else if($produk_aktif == FALSE){
                   $callback = array('status' => 'failed', 'message' => 'Maaf, Status Produk <b>Tidak Aktif </b> !', 'icon' =>'fa fa-check', 'type' => 'danger');

                }else if($warna_empty == TRUE){
                    $callback = array('status' => 'failed', 'message' => 'Maaf, Warna tidak tersedia / Kosong !', 'icon' =>'fa fa-check', 'type' => 'danger');
 
                }else if($route_empty == TRUE){
                  $callback = array('status' => 'failed', 'message' => 'Maaf, Route Color Order Kosong !', 'icon' =>'fa fa-check', 'type' => 'danger');

                }else if($id_parent_empty == TRUE){
                  $nama_produk_parent_empty = rtrim($nama_produk_parent_empty,', ');
                  $callback = array('status' => 'failed', 'message' => 'Maaf, Produk <b>'.$nama_produk_parent_empty.'</b> Product Parentnya masih kosong, Harap isi terlebih dahulu  !', 'icon' =>'fa fa-check', 'type' => 'danger');

                }else if($id_jenis_kain_empty == TRUE){
                  $nama_produk_jenis_kain_empty = rtrim($nama_produk_jenis_kain_empty,', ');
                  $callback = array('status' => 'failed', 'message' => 'Maaf, Produk ini <b>'.$nama_produk_jenis_kain_empty.'</b> Jenis Kain masih kosong, Harap isi terlebih dahulu !', 'icon' =>'fa fa-check', 'type' => 'danger');

                }else{

                    /*----------------------------------
                      Generate Produk Setelah Greige
                    ----------------------------------*/
                    
                    $cod         = $this->m_colorOrder->get_color_order_details_by_row($kode_co,$row);
                    $last_number = $this->m_colorOrder->get_kode_product();
                    $kode_prod   = "MF".$last_number; //set kode produk
                    $last_bom    = $this->m_colorOrder->get_kode_bom();
                    $kode_bom    = "BM".$last_bom; //set kode bom
                    $last_move   = $this->m_colorOrder->get_kode_stock_move();
                    $move_id     = "SM".$last_move; //Set kode stock_move
                    $i           = 1; //set count kode in/out
                    $tgl         = date('Y-m-d H:i:s');

                    $last_mo     = $this->m_colorOrder->get_kode_mo();
                    $dgt         = substr("00000" . $last_mo,-5);            
                    $kode_mo     = "MG".date("y") .  date("m"). $dgt;

                    $head        = $this->m_colorOrder->get_data_by_code($kode_co);
                    $buyer_code  = "Buyer Code = ".$head->buyer_code ." | ";
                  
                    $id_parent     = 0;
                    $id_sub_parent = 0;
                    $id_jenis_kain = 0;

                    $produk_aktif  = TRUE;
                    $id_parent_empty      = FALSE;
                    $id_sub_parent_empty  = FALSE;
                    $id_jenis_kain_empty  = FALSE;
                    $sql_insert_mst_sub_parent  = "";
                    
                    $nama_produk_parent_empty     = "";
                    $nama_produk_jenis_kain_empty = "";

                    $nama_produk_tidak_aktif = "";
                    $sql_log_history_batch   = "";

                    $id_sub_parent_new = $this->m_colorOrder->get_last_id_mst_sub_parent();                        

                    $parent_produk_same           = TRUE;
                    $nama_produk_parent_produk_not_same = "";
                    $sub_parent_produk_same       = TRUE;
                    $nama_sub_parent_produk_same  = "";
                    $jenis_kain_same              = TRUE;
                    $nama_jenis_kain_not_same     = "";
                    $satuan_produk_new_empty      = FALSE;
                    $satuan_produk_empty          = FALSE;
                    $nama_satuan_produk_empty     = "";
                    $satuan_produk_new_empty_departemen = "";

                    foreach ($cod as $val) {

                      $kode_prod_rm = $val->kode_produk;
                      $nama_produk  = $val->nama_produk;
                      $id_warna     = $val->id_warna;
                      $row_order_cod= $val->row_order; // row order color order details
                      $qty          = $val->qty;
                      $uom          = $val->uom;
                      $reff_notes   = $buyer_code.' '.$val->reff_notes;
                      $route_co     = $val->route_co;
                      $ow           = $val->ow;
                      $kode_sc      = $val->kode_sc;
                      $id_handling  = $val->id_handling;
                      $lebar_jadi   = $val->lebar_jadi;
                      $uom_lebar_jadi = $val->uom_lebar_jadi;
                      $gramasi      = $val->gramasi;

                      // get nama warna by id_warna
                      $cek_warna = $this->m_colorOrder->cek_warna_by_id_warna($id_warna)->row_array();
                      if(empty($cek_warna['id'])){
                        $nama_warna   = 'Kosong';
                        $warna_empty  = TRUE;
                      }else{
                        $nama_warna   = $cek_warna['nama_warna'];
                      }

                      // product parent
                      $cek_mst    = $this->m_colorOrder->cek_produk_parent_sub_parent_jenis_kain_by_kode_produk(addslashes($kode_prod_rm))->row_array();
                      $parent     = $cek_mst['id_parent'];
                            
                      if(empty($parent)){
                        $id_parent_empty            = TRUE;
                        $nama_produk_parent_empty  .= $nama_produk.', ';
                        break;
                      }else{
                        $id_parent = $parent;
                      }

                      //cek sub_parent
                      $sub_parent = $cek_mst['id_sub_parent'];
                      
                      if(empty($sub_parent)){

                        $nama_sub_parent_ex   = explode('"',$nama_produk);
                        $nama_sub_parent      = trim($nama_sub_parent_ex[0]).'"';
                        // cek ke mst sub parent 
                        $cek_sp = $this->m_colorOrder->cek_sub_parent_by_nama(addslashes($nama_sub_parent))->row_array();
                        if(empty($cek_sp['id'])){

                          // create sub_parent
                          $id_sub_parent  = $id_sub_parent_new ;  // sudah + 1
                          
                          // insert into mst sub parent
                          $sql_insert_mst_sub_parent .= "('".$id_sub_parent_new."','".$tgl."','".addslashes($nama_sub_parent)."'), ";
                          $id_sub_parent_new = $id_sub_parent_new + 1;  

                        }else{
                          $id_sub_parent = $cek_sp['id'];
                          // update ke mst produk by produk color order
                          $case3  .= "when kode_produk = '".addslashes($kode_prod_rm)."' then '".$id_sub_parent."'";
                          $where3 .= "'".addslashes($kode_prod_rm)."',"; 

                          //create log history mst produk
                          $note_log = "Update Sub Parent -> ".$cek_sp['nama_sub_parent'];
                          $date_log = date('Y-m-d H:i:s');
                          // $sql_log_history_batch .= "('".$date_log."','mms56','".$kode_prod_rm."','edit','".addslashes($note_log)."','".$nama_user."'), ";

                          $sql_insert_log_history[] = array(
                                      'datelog'   => $date_log,
                                      'main_menu_sub_kode'    => 'mms56',
                                      'kode'                  => $kode_prod_rm,
                                      'jenis_log'             => 'edit',
                                      'note'                  => $note_log,
                                      'nama_user'             => $nama_user ?? '',
                                      'ip_address'            => $ip);


                        }
                                               
                      }else{
                        $id_sub_parent = $sub_parent;
                      }


                      // cek jenis kain
                      $jenis_kain = $cek_mst['id_jenis_kain'];

                      if(empty($jenis_kain)){
                        $id_jenis_kain_empty           = TRUE;
                        $nama_produk_jenis_kain_empty  .= '<br>'.$nama_produk.', ';
                        break;
                      }else{
                        $id_jenis_kain = $jenis_kain;
                      }

                      $reff_picking_in  = "";
                      $reff_picking_out = "";
                      $move_id_rm       = "";
                      $move_id_fg       = "";
                      $sm_row           = 1;
                      $nama_prod_rm     = $nama_produk;

                      //generate produk + color
                      $produk_exp      = explode('"',$nama_produk);
                      $product_warna   = $produk_exp[0].'"-'.$nama_warna; // exp TR-TN123-60"-PINKPUTIH

                      //get lebar jadi produk
                      /*
                      $exp   = explode('"', $nama_produk);
                      $exp2  = explode('-', $exp[0]);
                      $lebar = array_pop($exp2);
                      */

                      // get route color order details
                      $route_prod    = $this->m_colorOrder->get_route_product($route_co);
                      foreach ($route_prod as $rp) {

                        $mthd          = explode('|',$rp->method);
                        $method_dept   = trim($mthd[0]);
                        $method_action = trim($mthd[1]);

                        $nama_dept        = $this->m_colorOrder->get_nama_dept_by_kode($method_dept)->row_array();
                        if($method_dept == 'GJD'){
                          $product_fullname = $product_warna;
                        }else{
                          $product_dept     = $nama_dept['nama'];
                          $product_fullname = $product_warna." (".$product_dept.")";
                        }
                
                        if ($method_action == 'PROD'){
                          $cek_prod2 = $this->m_colorOrder->cek_nama_product(addslashes($product_fullname))->row_array();

                          if(empty($cek_prod2['nama_produk'])){ // jika nama produk blm ada di master produk

                            // get kategori produk by departemen
                            $cek_cat = $this->m_colorOrder->get_kategori_produk_by_dept($method_dept)->row_array();
                            $kategori_produk = $cek_cat['id'];
                            $bom_true_false  = $cek_cat['bom'];
                            $type_produk     = 'stockable';

                            // cek satuan by departement
                            $uom           = $nama_dept['uom_1'];
                            $uom_2         = $nama_dept['uom_2'];

                            if(empty($uom) or empty($uom_2)){
                              $satuan_produk_new_empty  = TRUE;
                              $satuan_produk_new_empty_departemen .= '<br>'.$method_dept.', ';
                            }

                            $status_produk = 't';
                            
                            $last_number = $last_number + 1;
                            $kode_prod    = "MF".$last_number;
                            $sql_insert_batch .= "('".$kode_prod."','".addslashes($product_fullname)."','".$tgl."', '".$lebar_jadi."','".$uom_lebar_jadi."','".$kategori_produk."','".$bom_true_false."','".$type_produk."','".$uom."','".$uom_2."','".$status_produk."', '".$id_parent."','".$id_sub_parent."','".$id_jenis_kain."'), ";

                            $cat_nm   = $this->m_colorOrder->get_nama_category_by_id($kategori_produk)->row_array();
                            $get_pp   = $this->m_colorOrder->get_mst_parent_produk_by_id($id_parent)->row_array();
                            $get_spp  = $this->m_colorOrder->cek_sub_parent_by_id($id_sub_parent)->row_array();
                            $get_jk   = $this->m_colorOrder->get_mst_jenis_kain_by_id($id_jenis_kain)->row_array();


                            $note_log = "Produk dibuat dari Generate Color Order No. ".$kode_co." -> ".$kode_prod.' '.$product_fullname.' '.$uom.' '.$uom_2.' '.$cat_nm['nama_category'].' '.$get_pp['nama'].' '.$get_spp['nama_sub_parent'].' '.$get_jk['nama_jenis_kain'];
                            $date_log = date('Y-m-d H:i:s');
                            // $sql_log_history_batch .= "('".$date_log."','mms56','".$kode_prod."','create','".addslashes($note_log)."','".$nama_user."'), ";
                            $sql_insert_log_history[] = array(
                                        'datelog'   => $date_log,
                                        'main_menu_sub_kode'    => 'mms56',
                                        'kode'                  => $kode_prod,
                                        'jenis_log'             => 'create',
                                        'note'                  => $note_log,
                                        'nama_user'             => $nama_user ?? '',
                                        'ip_address'            => $ip);

                          }else{
                            $kode_prod = $cek_prod2['kode_produk'];
                            $uom       = $cek_prod2['uom'];
                            $uom_2     = $cek_prod2['uom_2'];

                            if(empty($uom) or empty($uom_2)){
                              $satuan_produk_empty  = TRUE;
                              $nama_satuan_produk_empty    .= '<br>'.$product_fullname.", ";
                            }

                            // cek status produk route color order
                            $stat_produk = $this->_module->get_status_aktif_by_produk(addslashes($kode_prod))->row_array();// status produk aktif/tidak
                            if($stat_produk['status_produk']!= 't'){
                              $produk_aktif     = FALSE;
                              $nama_produk_tidak_aktif .= '<br>'.$product_fullname.", ";
                              // break;
                            }

                            // cek product parent
                            $cek_mst_rp   = $this->m_colorOrder->cek_produk_parent_sub_parent_jenis_kain_by_kode_produk(addslashes($kode_prod))->row_array();
                            $route_pp     = $cek_mst_rp['id_parent'];
                            
                            if(empty($route_pp)){
                              $id_parent_empty    = TRUE;
                              $nama_produk_parent_empty  .= '<br>'.$product_fullname.", ";
                              // update id_parent produk route color order
                              // $case2  .= "when kode_produk = '".addslashes($kode_prod)."' then '".$id_parent."'";
                              // $where2 .= "'".addslashes($kode_prod)."',"; 
                              break;
                            }else{

                              //cek apakah parent produk (CO) dan parent produk (grg) sama
                              if($route_pp != $id_parent ){
                                $parent_produk_same = FALSE;
                                $nama_produk_parent_produk_not_same .= '<br>'.$product_fullname.', ';
                                // break;
                              }

                            }


                            //cek sub parent produk route color order
                            $route_spp = $cek_mst_rp['id_sub_parent'];
                            if(empty($route_spp)){
                              // update sub parent
                              $case3  .= "when kode_produk = '".addslashes($kode_prod)."' then '".$id_sub_parent."'";
                              $where3 .= "'".addslashes($kode_prod)."',"; 

                              $cek_sp_nm = $this->m_colorOrder->cek_sub_parent_by_id($id_sub_parent)->row_array();
                            
                              //create log history mst produk
                              $note_log = "Update Sub Parent -> ".$cek_sp_nm['nama_sub_parent'];
                              $date_log = date('Y-m-d H:i:s');
                              // $sql_log_history_batch .= "('".$date_log."','mms56','".$kode_prod."','edit','".addslashes($note_log)."','".$nama_user."'), ";

                              $sql_insert_log_history[] = array(
                                              'datelog'   => $date_log,
                                              'main_menu_sub_kode'    => 'mms56',
                                              'kode'                  => $kode_prod,
                                              'jenis_log'             => 'edit',
                                              'note'                  => $note_log,
                                              'nama_user'             => $nama_user ?? '',
                                              'ip_address'            => $ip);
                              
                            }else{
                              if($route_spp != $id_sub_parent ){
                                $sub_parent_produk_same = FALSE;
                                $nama_sub_parent_produk_same .= '<br>'.$product_fullname.', ';
                                // break;
                              }
                            }

                            // cek jenis kain produk route color order
                            $route_jk = $cek_mst_rp['id_jenis_kain'];
                            if(empty($route_jk)){
                              // update jenis kain
                              // $case4  .= "when kode_produk = '".addslashes($kode_prod)."' then '".$id_jenis_kain."'";
                              // $where4 .= "'".addslashes($kode_prod)."',"; 
                              $id_jenis_kain_empty = TRUE;
                              $nama_produk_jenis_kain_empty .= '<br>'.$product_fullname.", ";
                              // break;
                            }else{
                              if($route_jk != $id_jenis_kain ){
                                $jenis_kain_same = FALSE;
                                $nama_jenis_kain_not_same .= '<br>'.$product_fullname.', ';
                                // break;
                              }
                            }

                          }
                           
                          /*----------------------------------
                                    Generate BOM
                          ----------------------------------*/
                          $cek_bom = $this->m_colorOrder->cek_bom($kode_prod)->row_array();

                          if(empty($cek_bom['kode_produk'])){
                            $sql_bom_batch .= "('".$kode_bom."','".$tgl."','".addslashes($product_fullname)."','".addslashes($kode_prod)."','".addslashes($product_fullname)."','1000','".addslashes($uom)."'), ";
                            $sql_bom_items_batch .= "('".$kode_bom."','".addslashes($kode_prod_rm)."','".addslashes($nama_prod_rm)."','1000','".addslashes($uom)."','1'), ";

                            $last_bom  = $last_bom + 1;

                            //create log history bom
                            $note_log = "BoM dibuat dari Generate Color Order No. ".$kode_co."  -> <br> ".$kode_bom." ".$product_fullname." 1000 ".$uom;
                            $date_log = date('Y-m-d H:i:s');
                            // $sql_log_history_batch .= "('".$date_log."','mms73','".$kode_bom."','create','".addslashes($note_log)."','".$nama_user."'), ";

                            $sql_insert_log_history[] = array(
                                        'datelog'   => $date_log,
                                        'main_menu_sub_kode'    => 'mms73',
                                        'kode'                  => $kode_bom,
                                        'jenis_log'             => 'create',
                                        'note'                  => $note_log,
                                        'nama_user'             => $nama_user ?? '',
                                        'ip_address'            => $ip);

                          }else{
                            // ambil kode bom
                            $kode_bom   = $cek_bom['kode_bom'];
                          }

                          $kode_prod_rm = $kode_prod;
                          $nama_prod_rm = $product_fullname;            

                        }//end if PROD


                        /*----------------------------------
                                Generate Stock Moves
                        ----------------------------------*/

                        if($method_action == 'CON'){
                          $origin_prod = $kode_prod_rm.'_1';
                          $source_move_PROD = FALSE;
                          if($method_dept== "GJD"){
                            $source_move      = $source_move;
                          }else{
                            $source_move      = '';
                          }
                        }else{
                          $origin_prod = '';
                        }

                        $origin = $kode_sc.'|'.$kode_co.'|'.$row_order_cod.'|'.$ow;  // exp format origin = SC|CO|ROW|OW
                        $sql_stock_move_batch .= "('".$move_id."','".$tgl."','".$origin."','".$rp->method."','".$rp->lokasi_dari."','".$rp->lokasi_tujuan."','draft','".$sm_row."','".$source_move."'), ";
                        $sql_stock_move_produk_batch .= "('".$move_id."','".addslashes($kode_prod_rm)."','".addslashes($nama_prod_rm)."','".$qty."','".addslashes($uom)."','draft','1','".$origin_prod."'), ";
                        $sm_row = $sm_row + 1; // stock_move_row

                        if($method_action == 'OUT'){//Generate Pengiriman
          
                          if($i=="1"){
                            $arr_kode[$rp->method]= $this->m_colorOrder->get_kode_pengiriman($method_dept);
                          }else{
                            $arr_kode[$rp->method]= $arr_kode[$rp->method] + 1;
                          }
                          $dgt=substr("00000" . $arr_kode[$rp->method],-5);            
                          $kode_out = $method_dept."/".$method_action."/".date("y").  date("m").$dgt;
                            
                          $sql_out_batch  .= "('".$kode_out."','".$tgl."','".$tgl."','".$tgl."','".addslashes($reff_notes)."','draft','".$method_dept."','".$origin."','".$move_id."','".$rp->lokasi_dari."','".$rp->lokasi_tujuan."'), ";
                          $sql_out_items_batch .= "('".$kode_out."','".addslashes($kode_prod_rm)."','".addslashes($nama_prod_rm)."','".$qty."','".addslashes($uom)."','draft','1',''), ";

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
                          // $sql_log_history_out .= "('".$date_log."','".$mms_kode."','".$kode_out."','create','".$note_log."','".$nama_user."'), ";

                          $sql_insert_log_history[] = array(
                                      'datelog'   => $date_log,
                                      'main_menu_sub_kode'    => $mms_kode,
                                      'kode'                  => $kode_out,
                                      'jenis_log'             => 'create',
                                      'note'                  => $note_log,
                                      'nama_user'             => $nama_user ?? '',
                                      'ip_address'            => $ip);
                         
                        }elseif($method_action == 'IN'){//Generete Penerimaan

                          if($i=="1"){
                            $arr_kode[$rp->method]= $this->m_colorOrder->get_kode_penerimaan($method_dept);
                          }else{
                            $arr_kode[$rp->method]= $arr_kode[$rp->method] + 1;
                          }
                          $dgt     =substr("00000" . $arr_kode[$rp->method],-5);            
                          $kode_in = $method_dept."/".$method_action."/".date("y").  date("m").$dgt;

                          
                          $reff_picking_in = $kode_out."|".$kode_in;
                          $sql_in_batch   .= "('".$kode_in."','".$tgl."','".$tgl."','".$tgl."','".addslashes($reff_notes)."','draft','".$method_dept."','".$origin."','".$move_id."','".$reff_picking_in."','".$rp->lokasi_dari."','".$rp->lokasi_tujuan."'), "; 
                          $sql_in_items_batch   .= "('".$kode_in."','".addslashes($kode_prod_rm)."','".addslashes($nama_prod_rm)."','".$qty."','".addslashes($uom)."','draft','1'), "; 

                          $reff_picking_out = $kode_out."|".$kode_in;
                          $case  .= "when kode = '".$kode_out."' then '".$reff_picking_out."'";
                          $where .= "'".$kode_out."',";

                          $kode_out    = "";
                          $source_move = $move_id;

                          //get mms kode berdasarkan dept_id
                          $mms = $this->_module->get_kode_sub_menu_deptid('penerimaanbarang',$method_dept)->row_array();
                          if(!empty($mms['kode'])){
                            $mms_kode = $mms['kode'];
                          }else{
                            $mms_kode = '';
                          }

                          //create log history pengiriman_barang
                          $note_log = $kode_in.' | '.$origin;
                          $date_log = date('Y-m-d H:i:s');
                          // $sql_log_history_in .= "('".$date_log."','".$mms_kode."','".$kode_in."','create','".$note_log."','".$nama_user."'), ";

                          $sql_insert_log_history[] = array(
                                        'datelog'   => $date_log,
                                        'main_menu_sub_kode'    => $mms_kode,
                                        'kode'                  => $kode_in,
                                        'jenis_log'             => 'create',
                                        'note'                  => $note_log,
                                        'nama_user'             => $nama_user ?? '',
                                        'ip_address'            => $ip);
                         
                        }elseif($method_action == 'CON'){
                          $source_move = "";
                         
                          //get move id rm target
                          $move_id_rm = $move_id;
                          $kode_prod_rm_target = $kode_prod_rm;
                          $nama_prod_rm_target = $nama_prod_rm;
                          $qty_rm_target       = $qty;
                          $uom_rm_target       = $uom;

                        }elseif($method_action == 'PROD'){// generate mo/mg   
                          $source_move      = $move_id;
                          $source_move_PROD = TRUE;

                          /*----------------------------------
                              Generate MO / MG
                          ----------------------------------*/

                          $move_id_fg = $move_id;
                          $kode_prod_fg_target = $kode_prod_rm;
                          $nama_prod_fg_target = $nama_prod_rm;
                          $qty_fg_target       = $qty;
                          $uom_fg_target       = $uom;

                          //$source_location = $method_dept."/Stock";
                          // get location stock by dept
                          $loc      = $this->_module->get_nama_dept_by_kode($method_dept)->row_array();
                          $location = $loc['stock_location'];

                          //sql simpan mrp_production
                          $sql_mrp_prod_batch .= "('".$kode_mo."','".$tgl."','".$origin."','".addslashes($kode_prod_rm)."','".addslashes($nama_prod_rm)."','".$qty."','".addslashes($uom)."','".$tgl."','".addslashes($reff_notes)."','".$kode_bom."','".$tgl."','".$tgl."','".$location."','".$location."','".$method_dept."','draft','".$id_warna."','".$nama_user."','".$id_handling."','".$lebar_jadi."','".$uom_lebar_jadi."','".$gramasi."'), ";

                          //sql simpan mrp production rm target
                          $origin_prod  = $kode_prod_rm_target.'_1';
                          $sql_mrp_prod_rm_batch .= "('".$kode_mo."','".$move_id_rm."','".addslashes($kode_prod_rm_target)."','".addslashes($nama_prod_rm_target)."','".$qty_rm_target."','".addslashes($uom_rm_target)."','1','".addslashes($origin_prod)."','draft'), "; 

                          //sql simpan mrp production fg target
                          $sql_mrp_prod_fg_batch .= "('".$kode_mo."','".$move_id_fg."','".addslashes($kode_prod_fg_target)."','".addslashes($nama_prod_fg_target)."','".$qty_fg_target."','".addslashes($uom_fg_target)."','1','draft'), "; 

                           //get mms kode berdasarkan dept_id
                          $mms = $this->_module->get_kode_sub_menu_deptid('mO',$method_dept)->row_array();
                          if(!empty($mms['kode'])){
                            $mms_kode = $mms['kode'];
                          }else{
                            $mms_kode = '';
                          }

                           //create log history mrp_prodction
                          $note_log = $kode_mo.' | '.($nama_prod_rm).' | '.$qty.' '.$uom;
                          $date_log = date('Y-m-d H:i:s');
                          // $sql_log_history_mrp .= "('".$date_log."','".$mms_kode."','".$kode_mo."','create','".$note_log."','".$nama_user."'), ";

                          $sql_insert_log_history[] = array(
                                    'datelog'   => $date_log,
                                    'main_menu_sub_kode'    => $mms_kode,
                                    'kode'                  => $kode_mo,
                                    'jenis_log'             => 'create',
                                    'note'                  => $note_log,
                                    'nama_user'             => $nama_user ?? '',
                                    'ip_address'            => $ip);
                                                   
                          $last_mo   = $last_mo + 1;

                        }

                        $kode_bom  = "BM".$last_bom;

                        $dgt       = substr("00000" . $last_mo,-5);            
                        $kode_mo   = "MG".date("y") .  date("m"). $dgt;

                        $last_move = $last_move + 1;
                        $move_id   = "SM".$last_move;

                      }// end foreach $route_prod


                    } //end foreach color order details / COD
                    
                    if($id_parent_empty == FALSE AND $id_sub_parent_empty == FALSE AND $id_jenis_kain_empty == FALSE AND $produk_aktif == TRUE AND $jenis_kain_same == TRUE AND $parent_produk_same == TRUE AND $parent_produk_same == TRUE AND $satuan_produk_new_empty == FALSE AND $satuan_produk_empty == FALSE){

                      if(!empty($sql_insert_batch)){
                        $sql_insert_batch = rtrim($sql_insert_batch, ', ');
                        $this->m_colorOrder->simpan_product_batch($sql_insert_batch);
                      }

                      if(!empty($sql_bom_batch)){
                        $sql_bom_batch = rtrim($sql_bom_batch, ', ');
                        $this->m_colorOrder->simpan_bom_batch($sql_bom_batch);

                        $sql_bom_items_batch = rtrim($sql_bom_items_batch, ', ');
                        $this->m_colorOrder->simpan_bom_items_batch($sql_bom_items_batch);
                      }

                      if(!empty($sql_stock_move_batch)){
                        $sql_stock_move_batch = rtrim($sql_stock_move_batch, ', ');
                        $this->m_colorOrder->create_stock_move_batch($sql_stock_move_batch);

                        $sql_stock_move_produk_batch = rtrim($sql_stock_move_produk_batch, ', ');
                        $this->m_colorOrder->create_stock_move_produk_batch($sql_stock_move_produk_batch);
                      }

                      // if(!empty($sql_log_history_batch)){
                      //   $sql_log_history_batch = rtrim($sql_log_history_batch, ', ');
                      //   $this->_module->simpan_log_history_batch($sql_log_history_batch);
                        
                      // }

                      if(!empty($sql_out_batch)){
                        $sql_out_batch = rtrim($sql_out_batch, ', ');
                        $this->_module->simpan_pengiriman_batch($sql_out_batch);
    
                        $sql_out_items_batch = rtrim($sql_out_items_batch, ', ');
                        $this->_module->simpan_pengiriman_items_batch($sql_out_items_batch);
    
                        // $sql_log_history_out = rtrim($sql_log_history_out, ', ');
                        // $this->_module->simpan_log_history_batch($sql_log_history_out);
                      }

                      if(!empty($sql_in_batch)){
                        $sql_in_batch = rtrim($sql_in_batch, ', ');
                        $this->m_colorOrder->simpan_penerimaan_batch($sql_in_batch);

                        $sql_in_items_batch = rtrim($sql_in_items_batch, ', ');
                        $this->m_colorOrder->simpan_penerimaan_items_batch($sql_in_items_batch);

                        $where = rtrim($where, ',');
                        $sql_update_reff_out_batch  = "UPDATE pengiriman_barang SET reff_picking =(case ".$case." end) WHERE  kode in (".$where.") ";
                        $this->m_colorOrder->update_reff_picking_pengiriman_batch($sql_update_reff_out_batch);
                        
                        // $sql_log_history_in = rtrim($sql_log_history_in, ', ');
                        // $this->_module->simpan_log_history_batch($sql_log_history_in);
                        
                      }

                      if(!empty($sql_mrp_prod_batch)){
                        $sql_mrp_prod_batch = rtrim($sql_mrp_prod_batch, ', ');
                        $this->m_colorOrder->simpan_mrp_production_batch($sql_mrp_prod_batch);

                        $sql_mrp_prod_rm_batch = rtrim($sql_mrp_prod_rm_batch, ', ');
                        $this->m_colorOrder->simpan_mrp_production_rm_target_batch($sql_mrp_prod_rm_batch);

                        $sql_mrp_prod_fg_batch = rtrim($sql_mrp_prod_fg_batch, ', ');
                        $this->m_colorOrder->simpan_mrp_production_fg_target_batch($sql_mrp_prod_fg_batch);
                        
                        // $sql_log_history_mrp = rtrim($sql_log_history_mrp, ', ');
                        // $this->_module->simpan_log_history_batch($sql_log_history_mrp);
                        
                      }

                      if(!empty($sql_insert_mst_sub_parent)){
                        $sql_insert_mst_sub_parent = rtrim($sql_insert_mst_sub_parent, ', ');
                        $this->m_colorOrder->simpan_mst_sub_parent_batch($sql_insert_mst_sub_parent);
                      }

                      if(!empty($case3) AND !empty($where3)){
                        
                        //update sub parent produk
                        $where3 = rtrim($where3, ',');
                        $sql_update_sub_parent_produk = "UPDATE mst_produk SET id_sub_parent = (case ".$case3." end) WHERE  kode_produk in (".$where3.") ";
                        $this->_module->update_reff_batch($sql_update_sub_parent_produk);

                      }


                      //create log history All
                      if(!empty($sql_insert_log_history)){
                          $this->_module->simpan_log_history_batch_2($sql_insert_log_history);
                      }

                    

                      // update reff_notes by row
                      $this->m_colorOrder->update_reff_notes_color_order_items_by_row($kode_co,$row,addslashes($reff_notes));

                      $jenis_log   = "edit";
                      $note_log    = "Update Reff Notes | ".$reff_notes;
                      $this->_module->gen_history($sub_menu, $kode_co, $jenis_log, addslashes($note_log), $username);


                      //update detail items jadi generate
                      $this->m_colorOrder->update_status_color_order_items($kode_co,$row,'generated');

                      $cek_details = $this->m_colorOrder->cek_status_color_order_items($kode_co,'')->num_rows(); 

                      $where_status       = "AND status NOT IN ('generated','cancel','ng')";
                      $cek_details_status = $this->m_colorOrder->cek_status_color_order_items($kode_co,$where_status)->num_rows();

                      if($cek_details == 0  ){
                        $this->m_colorOrder->ubah_status_color_order($kode_co,'draft');
                      }else if($cek_details > 0){
                        if($cek_details_status == 0){
                          $this->m_colorOrder->ubah_status_color_order($kode_co,'done');
                        }else{
                          $this->m_colorOrder->ubah_status_color_order($kode_co,'draft');
                        } 
                      }

                      $jenis_log   = "generate";
                      $note_log    = "Generated | ".$row;
                      $this->_module->gen_history($sub_menu, $kode_co, $jenis_log, addslashes($note_log), $username);  

                      $callback = array('status' => 'success', 'message'=>'Generate Data Berhasil ! ', 'icon' => 'fa fa-check', 'type'=>'success');

                    }else{

                      if($id_parent_empty == TRUE){
                        $nama_produk_parent_empty = rtrim($nama_produk_parent_empty,', ');
                        $callback = array('status' => 'failed','message' => 'Maaf, Product Parent Kosong ! <b>'.$nama_produk_parent_empty.'</b>.<br> Harap isi terlebih dahulu Product Parent !', 'icon' =>'fa fa-check', 'type' => 'danger');

                      }else if($produk_aktif == FALSE){
                        $nama_produk_tidak_aktif = rtrim($nama_produk_tidak_aktif,', ');
                        $callback = array('status' => 'failed', 'message' => 'Maaf, Status Produk Tidak Aktif  ! <b>'.$nama_produk_tidak_aktif.'</b>', 'icon' =>'fa fa-check', 'type' => 'danger');
                      
                      }else if($id_sub_parent_empty == TRUE){
                        $callback = array('status' => 'failed','message' => 'Maaf, Sub Product Parent Kosong, Harap isi terlebih dahulu Sub Product Parent !', 'icon' =>'fa fa-check', 'type' => 'danger');
                        
                      }else if( $id_jenis_kain_empty == TRUE){
                        $nama_produk_jenis_kain_empty = rtrim($nama_produk_jenis_kain_empty,', ');
                        $callback = array('status' => 'failed','message' => 'Maaf, Jenis Kain kosong ! <b>'.$nama_produk_jenis_kain_empty.'</b> </br>  Harap isi terlebih dahulu Jenis Kain !', 'icon' =>'fa fa-check', 'type' => 'danger');
                      
                      }else if( $parent_produk_same == FALSE){
                        $nama_produk_parent_produk_not_same = rtrim($nama_produk_parent_produk_not_same,', ');
                        $callback = array('status' => 'failed','message' => 'Maaf, Product Parent Tidak Sama ! <b>'.$nama_produk_parent_produk_not_same.'</b> </br>  Harap samakan terlebih dahulu !', 'icon' =>'fa fa-check', 'type' => 'danger');
                        
                      }else if( $sub_parent_produk_same == FALSE){
                        $nama_sub_parent_produk_same = rtrim($nama_sub_parent_produk_same,', ');
                        $callback = array('status' => 'failed','message' => 'Maaf, Sub Product Parent Tidak Sama ! <b>'.$nama_sub_parent_produk_same.'</b> </br>  Harap samakan terlebih dahulu !', 'icon' =>'fa fa-check', 'type' => 'danger');

                      }else if( $jenis_kain_same == FALSE){
                        $nama_jenis_kain_not_same = rtrim($nama_jenis_kain_not_same,', ');
                        $callback = array('status' => 'failed','message' => 'Maaf, Jenis Kain Tidak Sama ! <b>'.$nama_jenis_kain_not_same.'</b> </br>  Harap samakan terlebih dahulu !', 'icon' =>'fa fa-check', 'type' => 'danger');

                      }else if( $satuan_produk_new_empty == TRUE){
                        $satuan_produk_new_empty_departemen = rtrim($satuan_produk_new_empty_departemen,', ');
                        $callback = array('status' => 'failed','message' => 'Maaf, Master Uom atau Uom 2 di departemen tersebut Kosong ! <b>'.$satuan_produk_new_empty_departemen.'</b> </br>  Harap isi terlebih dahulu !', 'icon' =>'fa fa-check', 'type' => 'danger');
                      }else if( $satuan_produk_empty == TRUE){
                        $nama_satuan_produk_empty = rtrim($nama_satuan_produk_empty,', ');
                        $callback = array('status' => 'failed','message' => 'Maaf, Uom atau Uom2 kosong ! <b>'.$nama_satuan_produk_empty.'</b> </br>  Harap isi terlebih dahulu !', 'icon' =>'fa fa-check', 'type' => 'danger');
                        
                      }else{
                        $callback = array('status' => 'failed','message' => 'Maaf, Generate Data Gagal !', 'icon' =>'fa fa-check', 'type' => 'danger');
                      }

                    }

                }// else cek


              }// else cek != draft

            }

            // unlock tabel
            $this->m_colorOrder->unlock_tabel();

        }


        echo json_encode($callback);
    }


    public function batal_detail_color_order()
    {
        if(empty($this->session->userdata('status'))) {//cek apakah session masih ada
            // session habis
            $callback = array('status' => 'failed','message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
        }else{
            $sub_menu  = $this->uri->segment(2);
            $username  = addslashes($this->session->userdata('username')); 
            $nu        = $this->_module->get_nama_user($username)->row_array();
            $nama_user = addslashes($nu['nama']);

            $kode_co   = $this->input->post('kode');
            $row       = $this->input->post('row_order');
            $row_co    = $row;

            // lock tabel
            $this->_module->lock_tabel('color_order WRITE, color_order_detail WRITE, color_order_detail as a WRITE, route_co as b WRITE, warna as c WRITE, stock_move WRITE, stock_move_items WRITE, stock_move_produk WRITE, mrp_production WRITE, mrp_production_rm_hasil WRITE, mrp_production_rm_target WRITE, mrp_production_fg_target WRITE, mrp_production_fg_hasil WRITE, pengiriman_barang WRITE, pengiriman_barang_items WRITE, penerimaan_barang WRITE, penerimaan_barang_items WRITE, user WRITE, main_menu_sub WRITE, log_history WRITE, departemen d WRITE');

            // cek status color_order_details
            $cek_status = $this->m_colorOrder->cek_status_color_order_details_by_row($kode_co,$row)->row_array();

            if($cek_status['status'] == 'draft'){
                $callback = array('status' => 'failed','message' => 'Maaf, Status Product masih draft !', 'icon' =>'fa fa-check', 'type' => 'danger');

            }else if($cek_status['status'] == 'cancel'){
                $callback = array('status' => 'failed','message' => 'Maaf, Status Product Sudah dibatalkan !', 'icon' =>'fa fa-check', 'type' => 'danger');

            }else if(empty($cek_status['status'])){
                $callback = array('status' => 'failed','message' => 'Maaf, Data yang akan Di hapus Kosong !', 'icon' =>'fa fa-check', 'type' => 'danger');
            }else{ 

              if($cek_status['status'] != 'generated'){
                  $callback = array('status' => 'failed','message' => 'Maaf, Status tidak valid !', 'icon' =>'fa fa-check', 'type' => 'danger');
              }else{

                  $update_stock_move = false;
                  $batal_item    = false;
                  $status_cancel = "cancel";
                    //mrp_production
                  $case   = "";
                  $where  = "";
                    //pengiriman_barang
                  $case2  = "";
                  $where2 = "";
                    //penerimaan_barang
                  $case3  = "";
                  $where3 = "";
                    //stock move, stock_move_items, stock_move_produk
                  $case4  = "";
                  $where4 = "";
                  $date_log         = date('Y-m-d H:i:s');
                  $sql_log_history  = "";
                  $insert_log       = array();
                  $ip         = $this->input->ip_address();

                  // get OW by row
                  $get_ow = $this->m_colorOrder->cek_status_color_order_details_by_row($kode_co,$row)->row_array();
                  $ow     = $get_ow['ow'];

                    // get so
                  $get_so = $this->m_colorOrder->get_data_by_code($kode_co);
                  $so     = $get_so->kode_sc;

                    // origin SO|CO|ROW|OW
                  $origin           = $so.'|'.$kode_co.'|'.$row.'|'.$ow;
                  $status_in_valid  = false;
                  $dokumen          = '';
                  $list_sm          = $this->_module->get_list_stock_move_by_origin($origin);
                  $status_done_all  = true;
                  $kode_mrp_tmp     = '';
                  foreach ($list_sm as $row) {
                    # code...

                      $batal_item = true;

                      $ex_mt = explode('|',$row->method);
                      $method_dept = $ex_mt[0];
                      $method_action  = $ex_mt[1]; //ex CON/PROD/OUT/IN
                      $origin  = $row->origin;
                      $move_id = $row->move_id;

                        if( ($method_action == 'CON' OR $method_action == 'PROD') ){

                            $log_mrp = false;
                            // cek status mrp_production ?
                            $status = "AND status NOT IN ('done','cancel')";
                            $cek_mrp = $this->_module->cek_status_mrp_productin_by_origin($origin,$method_dept,$status)->result_array();
                            foreach($cek_mrp as $mrp){
                                $status_done_all = false;
                                if(!empty($mrp['kode'])){//bearti status MO = ready/draft
        
                                    //update status = cancel mrp_production, mrp_production_rm_target, mrp_production_fg_target
                                    $case  .= "when kode = '".$mrp['kode']."' then '".$status_cancel."'";
                                    $where .= "'".$mrp['kode']."',";

                                    $log_mrp = true;
                                    $update_stock_move = true;
                                    $kode_mrp = $mrp['kode'] ?? ''; 

                                    if($mrp['status'] == 'draft'){
                                      // cek mrp rm target
                                      $cek_status_rm = $this->m_colorOrder->cek_mrp_production_rm_target($kode_mrp,'ready')->num_rows();
                                      if($cek_status_rm > 0){
                                        $status_in_valid  = true;
                                        $nm_dept          = $this->_module->get_nama_dept_by_kode($mrp['dept_id'])->row_array();
                                        if($kode_mrp != $kode_mrp_tmp){
                                          $dokumen         .= $kode_mrp.' - '.$nm_dept['nama'] ?? '';
                                          $dokumen         .= '<br>';
                                        }
                                        $kode_mrp_tmp     = $kode_mrp;
                                      }
                                    }else if($mrp['status'] == 'ready'){
                                      $status_in_valid  = true;
                                      $nm_dept          = $this->_module->get_nama_dept_by_kode($mrp['dept_id'])->row_array();
                                      if($kode_mrp != $kode_mrp_tmp){
                                        $dokumen         .= $kode_mrp.' - '.$nm_dept['nama'] ?? '';
                                        $dokumen         .= '<br>';
                                      }
                                      $kode_mrp_tmp     = $kode_mrp;

                                    }
                                }
                            }
                                if($log_mrp == true){

                                  $mms = $this->_module->get_kode_sub_menu_deptid('mO',$method_dept)->row_array();
                                  if(!empty($mms['kode'])){
                                    $mms_kode = $mms['kode'];
                                  }else{
                                    $mms_kode = '';
                                  } 

                                    // create log history mrp_production
                                    $note_log         = 'Batal MO '.$method_action.' | '.$kode_mrp;
                                    // $sql_log_history .= "('".$date_log."','".$mms_kode."','".$kode_mrp."','cancel','".$note_log."','".$nama_user."'), ";

                                    $insert_log[] = array(
                                                  'datelog'   => $date_log,
                                                  'main_menu_sub_kode'    => $mms_kode,
                                                  'kode'                  => $kode_mrp,
                                                  'jenis_log'             => 'cancel',
                                                  'note'                  => $note_log,
                                                  'nama_user'             => $nama_user ?? '',
                                                  'ip_address'            => $ip);
                                }

                        }elseif($method_action == 'OUT'){

                            // cek status pengiriman barang
                            $status  = "AND status NOT IN ('done','cancel')";
                            $cek_out = $this->_module->cek_status_pengiriman_barang_by_move_id($origin,$move_id,$status)->row_array();
                            
                            if(!empty($cek_out['kode'])){//bearti pengiriman_barang = ready/draft
                                $status_done_all = false;
                                //update status = cancel pengiriman_barang, pengiriman_barang_items
                                $case2  .= " when kode = '".$cek_out['kode']."' then '".$status_cancel."'";
                                $where2 .= "'".$cek_out['kode']."',";             

                                //get mms kode berdasarkan dept_id
                                $mms = $this->_module->get_kode_sub_menu_deptid('pengirimanbarang',$method_dept)->row_array();
                                if(!empty($mms['kode'])){
                                  $mms_kode = $mms['kode'];
                                }else{
                                  $mms_kode = '';
                                }    
                                
                                // create log history pengiriman_barang
                                $note_log         = 'Batal Pengiriman Barang | '.$cek_out['kode'] ?? '';
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

                                if($cek_out['status'] == 'ready'){
                                    $kode_out       = $cek_out['kode'] ?? ' ';
                                    $status_in_valid = true;
                                    $nm_dept         = $this->_module->get_nama_dept_by_kode($cek_out['dept_id'])->row_array();
                                    $dokumen        .= $kode_out.'  - '.$nm_dept['nama'] ?? '';
                                    $dokumen        .= '<br>';
                                }
                            }

                        }elseif($method_action == 'IN'){
                            
                            // cek status penerimaan barang
                            $status  = "AND status NOT IN ('done','cancel')";
                            $cek_in  = $this->_module->cek_status_penerimaan_barang_by_move_id($origin,$move_id,$status)->row_array();
                            
                            if(!empty($cek_in['kode'])){//bearti penerimaan_barang = ready/draft
                               $status_done_all = false;
                                //update status = cancel penerimaan_barang, penerimaan_barang_items
                                $case3  .= " when kode = '".$cek_in['kode']."' then '".$status_cancel."'";
                                $where3 .= "'".$cek_in['kode']."',";     

                                //get mms kode berdasarkan dept_id
                                $mms = $this->_module->get_kode_sub_menu_deptid('penerimaanbarang',$method_dept)->row_array();
                                if(!empty($mms['kode'])){
                                  $mms_kode = $mms['kode'];
                                }else{
                                  $mms_kode = '';
                                }       
                                
                                // create log history penerimaan barang
                                $note_log         = 'Batal Penerimaan Barang | '.$cek_in['kode'] ?? ''; 
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

                                if($cek_in['status'] == 'ready'){
                                    $status_in_valid = true;
                                    $kode_in         = $cek_in['kode'] ?? '';
                                    $nm_dept         = $this->_module->get_nama_dept_by_kode($cek_in['dept_id'])->row_array();
                                    $dokumen        .= $kode_in.' - '.$nm_dept['nama'] ?? '';
                                    $dokumen        .= '<br>';
                                }
                            }
                        }

                        if($update_stock_move == true){
                                        
                            //update status = cancel stock move, stock_move_items, stock_move_produk
                            $case4  .= " when move_id = '".$move_id."' then '".$status_cancel."'";
                            $where4 .= "'".$move_id."',";
                        }

                        $update_stock_move = false;

                  }// end foreach stock_move origin


                  if($batal_item == true AND $status_in_valid == false &&  $status_done_all == false){
                   

                       //update mrp_production, mrp_production_rm_target, mrp_production_fg_target
                       if(!empty($case) AND !empty($where)){
                            
                            // update mrp_production
                            $where = rtrim($where, ',');
                            $sql_update_mrp_production = "UPDATE mrp_production SET status =(case ".$case." end) WHERE  kode in (".$where.") ";
                            $this->_module->update_reff_batch($sql_update_mrp_production);

                            // update mrp_production_rm_target
                            $sql_update_mrp_production_rm_target = "UPDATE mrp_production_rm_target SET status =(case ".$case." end) WHERE  kode in (".$where.") AND status NOT IN ('done')";
                            $this->_module->update_reff_batch($sql_update_mrp_production_rm_target);

                            // update mrp_production_fg_target 
                            $sql_update_mrp_production_fg_target = "UPDATE mrp_production_fg_target SET status =(case ".$case." end) WHERE  kode in (".$where.") AND status NOT IN ('done')";
                            $this->_module->update_reff_batch($sql_update_mrp_production_fg_target);


                       }

                       //update pengiriman_barang, pengiriman_barang_items
                       if(!empty($case2) AND !empty($where2)){
                        
                            //update pengiriman_barang
                            $where2 = rtrim($where2, ',');
                            $sql_update_pengiriman_barang = "UPDATE pengiriman_barang SET status =(case ".$case2." end) WHERE  kode in (".$where2.") ";
                            $this->_module->update_reff_batch($sql_update_pengiriman_barang);

                            // update pengiriman_barang_items
                            $sql_update_pengiriman_barang_items = "UPDATE pengiriman_barang_items SET status_barang =(case ".$case2." end) WHERE  kode in (".$where2.") ";
                            $this->_module->update_reff_batch($sql_update_pengiriman_barang_items);

                        
                       }
                       
                       //update penerimaan_barang, penerimaan_barang_items
                       if(!empty($case3) AND !empty($where3)){

                           //update penerimaan_barang
                            $where3 = rtrim($where3, ',');
                            $sql_update_penerimaan_barang = "UPDATE penerimaan_barang SET status =(case ".$case3." end) WHERE  kode in (".$where3.") ";
                            $this->_module->update_reff_batch($sql_update_penerimaan_barang);

                            // update penerimaan_barang_items
                            $sql_update_penerimaan_barang_items = "UPDATE penerimaan_barang_items SET status_barang =(case ".$case3." end) WHERE  kode in (".$where3.") ";
                            $this->_module->update_reff_batch($sql_update_penerimaan_barang_items);
                            

                       }

                       //update stock move, stock_move_items, stock_move_produk
                       if(!empty($case4) AND !empty($where4)){

                            // update stock_move
                            $where4 = rtrim($where4, ',');
                            $sql_update_stock_move = "UPDATE stock_move SET status =(case ".$case4." end) WHERE  move_id in (".$where4.") ";
                            $this->_module->update_reff_batch($sql_update_stock_move);

                            // update stock_move_items
                            $sql_update_stock_move_items = "UPDATE stock_move_items SET status =(case ".$case4." end) WHERE  move_id in (".$where4.") AND status NOT IN ('done') ";
                            $this->_module->update_reff_batch($sql_update_stock_move_items);

                            // update stock_move_produk
                            $sql_update_stock_move_produk = "UPDATE stock_move_produk SET status =(case ".$case4." end) WHERE  move_id in (".$where4.") AND status NOT IN ('done')";
                            $this->_module->update_reff_batch($sql_update_stock_move_produk);
                            

                       }

                        $jenis_log   = "cancel";
                        $note_log    = "Batal Items | ".$row_co;
                        $this->_module->gen_history($sub_menu, $kode_co, $jenis_log, addslashes($note_log), $username);

                        //create log history setiap yg batal
                        if(!empty($insert_log)){
                            // $sql_log_history = rtrim($sql_log_history, ', ');
                            $this->_module->simpan_log_history_batch_2($insert_log);
                        }

                        // update detail items jadi cancel
                        $this->m_colorOrder->update_status_color_order_items($kode_co,$row_co,$status_cancel);

                        $cek_details = $this->m_colorOrder->cek_status_color_order_items($kode_co,'')->num_rows(); 

                        $where_status       = "AND status NOT IN ('cancel')";
                        $cek_details_status = $this->m_colorOrder->cek_status_color_order_items($kode_co,$where_status)->num_rows();

                        $where_status2       = "AND status IN ('draft')";
                        $cek_details_status2 = $this->m_colorOrder->cek_status_color_order_items($kode_co,$where_status2)->num_rows();

                        if($cek_details > 0  ){
               
                          if($cek_details_status == 0){
                            $this->m_colorOrder->ubah_status_color_order($kode_co,'cancel');
                          }else if($cek_details_status2 > 0){
                            $this->m_colorOrder->ubah_status_color_order($kode_co,'draft');
                          }else{
                            $this->m_colorOrder->ubah_status_color_order($kode_co,'done');
                          }
                        }

                  }//end if batal_items == true

                  if($batal_item == false){
                        $callback = array('status' => 'failed', 'message' => 'Color Order Items Gagal Dibatalkan !', 'icon' =>'fa fa-warning', 'type' => 'danger');
                  }else if($status_done_all == true){
                        $callback = array('status' => 'failed', 'message' => 'Color Order Items Gagal Dibatalkan !<br> Semua status di Rantai Color Order sudah <b>Done</b>', 'icon' =>'fa fa-warning', 'type' => 'danger');
                  }else if($status_in_valid == true){
                        $callback = array('status' => 'failed', 'message' => 'Color Order Items Gagal Dibatalkan ! <br> Rantai Color Order Terdapat Status <b>Ready</b> <br>'.$dokumen , 'icon' =>'fa fa-warning', 'type' => 'danger');
                  }else{
                        $callback = array('status' => 'success', 'message' => 'Color Order Items Berhasil Dibatalkan !', 'icon' =>'fa fa-check', 'type' => 'success');
                  }

              } // else cek status draft

            } // else cek status

            // unlock tabel
            $this->m_colorOrder->unlock_tabel();

        }

        echo json_encode($callback);
    }

    public function list_sales_contract()
    {
        //$data['sc'] = $this->m_colorOrder->get_list_sales_contract();
        return $this->load->view('modal/v_sales_contract_modal');
    }

    public function get_data_sales_contract()
    {
        $list = $this->m_colorOrder->get_datatables2();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $field) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $field->sales_order .' <a href="#" class="pilih" sale_contract="'.$field->sales_order.'" buyer-code="'.$field->buyer_code.'"><span class="glyphicon glyphicon-check"></span></a>';
            $row[] = $field->buyer_code;
            $row[] = '<a href="#" class="pilih" sale_contract="'.$field->sales_order.'" buyer-code="'.$field->buyer_code.'">'.$field->nama_sales_group.'</a>';
 
            $data[] = $row;
        }
 
        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_colorOrder->count_all2(),
            "recordsFiltered" => $this->m_colorOrder->count_filtered2(),
            "data" => $data,
        );
        //output dalam format JSON
        echo json_encode($output);
    }


    public function color_detail_modal()
    {
        $kode_sc        = $this->input->post('kode_sc');
        $data['sc']     = $kode_sc;
        $data['co']     = $this->input->post('kode_co');
        return $this->load->view('modal/v_color_detail_modal',$data);
    }

    public function delete_color_detail()
    {

        if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
            // session habis
            $callback = array('status' => 'failed','message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
        }else{
          $sub_menu  = $this->uri->segment(2);
          $username  = addslashes($this->session->userdata('username')); 

          $kode_co    = $this->input->post('kode_co');
          $row_order  = $this->input->post('row_order');
          $this->m_colorOrder->hapus_color_detail($kode_co,$row_order);

          $cek_details = $this->m_colorOrder->cek_status_color_order_items($kode_co,'')->num_rows();

          $where_status       = "AND status IN ('generated','ng')";
          $cek_details_status = $this->m_colorOrder->cek_status_color_order_items($kode_co,$where_status)->num_rows();

          $where_status2       = "AND status NOT IN ('cancel','generated','ng')";
          $cek_details_status2 = $this->m_colorOrder->cek_status_color_order_items($kode_co,$where_status2)->num_rows();

          if($cek_details == 0  ){
              $this->m_colorOrder->ubah_status_color_order($kode_co,'draft');
          }else if($cek_details > 0){
              if($cek_details_status > 0){
                  $this->m_colorOrder->ubah_status_color_order($kode_co,'done');
              }else if($cek_details_status2 == 0){
                  $this->m_colorOrder->ubah_status_color_order($kode_co,'draft');
              }else{
                  $this->m_colorOrder->ubah_status_color_order($kode_co,'cancel');
              }
          }

          $jenis_log   = "cancel";
          $note_log    = "Hapus Data Color Order Details"." | ".$row_order;
          $this->_module->gen_history($sub_menu, $kode_co, $jenis_log, $note_log, $username);
          $callback = array('status' => 'success', 'message'=>'Data Berhasil di hapus !', 'icon' => 'fa fa-check', 'type'=>'success');

        }
        echo json_encode($callback);

    }

    public function edit_color_detail_modal()
    {
        $kode_co    = $this->input->post('kode_co');
        $status     = $this->input->post('status');
        $row_order  = $this->input->post('row_order');
        $data['handling']   = $this->_module->get_list_handling();
        $data['co']         = $kode_co;
        $data['ro']         = $row_order;
        $data['status']     = $status; 
        $data['route']      = $this->m_colorOrder->get_list_route_co();
        $data['uom']        = $this->_module->get_list_uom();

        $data['get']= $this->m_colorOrder->get_color_detail_by_id($kode_co,$row_order)->row_array();
        return $this->load->view('modal/v_color_detail_edit_modal',$data);

    }

    public function update_color_detail()
    {   

        if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
            // session habis
            $callback = array('status' => 'failed','message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
        }else{

            $sub_menu  = $this->uri->segment(2);
            $username  = addslashes($this->session->userdata('username'));

            $kode_co    = addslashes($this->input->post('kode_co'));
            $row_order  = $this->input->post('row_order');
            $route_co   = addslashes($this->input->post('route_co'));
            $qty        = $this->input->post('qty');
            $handling   = $this->input->post('handling');
            $gramasi   = $this->input->post('gramasi');
            $lebar_jadi = $this->input->post('lebar_jadi');
            $uom_lebar_jadi = $this->input->post('uom_lebar_jadi');
            $reff       = addslashes($this->input->post('reff'));

            if(empty($kode_co) OR empty($row_order)){
              $callback = array('status' => 'failed','message' => 'Maaf, Data Gagal Disimpan !', 'icon' =>'fa fa-check', 'type' => 'danger');

            }else{
              // update status head colororder = draft
              //$this->m_colorOrder->ubah_status_color_order($kode_co,'draft');

              // cek status  colo order details
              
              $cek_status = $this->m_colorOrder->cek_status_color_order_details_by_row($kode_co,$row_order)->row_array();

              if($cek_status['status'] == 'generated'){
                  $callback = array('status' => 'failed','message' => 'Maaf, Status Product Sudah Generated !', 'icon' =>'fa fa-check', 'type' => 'danger');

              }else if($cek_status['status'] == 'cancel'){
                  $callback = array('status' => 'failed','message' => 'Maaf, Status Product Sudah dibatalkan !', 'icon' =>'fa fa-check', 'type' => 'danger');
              }else if(empty($cek_status['status'])){
                  $callback = array('status' => 'failed','message' => 'Maaf, Data yang akan Di Generate Kosong !', 'icon' =>'fa fa-check', 'type' => 'danger');
              }else{
                
                if(!empty($handling)){
                  $hd = $this->_module->get_handling_by_id($handling)->row_array();
                  $nama_handling = $hd['nama_handling'];
                }else{
                  $nama_handling = '';
                }

                $nm_route = $this->m_colorOrder->get_nama_route_by_kode($route_co)->row_array();
                $nama_route = $nm_route['nama'];
                
                $this->m_colorOrder->ubah_color_detail($kode_co,$route_co,$row_order,$qty,$reff,$handling,$lebar_jadi,$uom_lebar_jadi,$gramasi);
                $jenis_log   = "edit";
                $note_log    = "Edit Color Details | ".$qty." | ".$reff." | ".$nama_route." | ".$nama_handling." | ".$gramasi." | ".$lebar_jadi." ".$uom_lebar_jadi." | ".$row_order;
                $this->_module->gen_history($sub_menu, $kode_co, $jenis_log, $note_log, $username);
                $callback = array('status' => 'success', 'message'=>'Data Berhasil diubah !', 'icon' => 'fa fa-check', 'type'=>'success');

              }

            }
            echo json_encode($callback);
        }

    }

    public function update_status_color_order_details()
    {
        if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
            // session habis
            $callback = array('status' => 'failed','message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
        }else{

            $sub_menu  = $this->uri->segment(2);
            $username  = addslashes($this->session->userdata('username'));

            $kode_co    = addslashes($this->input->post('kode_co'));
            $row_order  = $this->input->post('row_order');
            $status_cod = addslashes($this->input->post('value'));// generated / ng (not good)

            
            if(empty($kode_co) OR empty($row_order) OR empty($status_cod)){
              $callback = array('status' => 'failed','message' => 'Maaf, status Gagal Dirubah !', 'icon' =>'fa fa-check', 'type' => 'danger');
            }else{

              $cek_status = $this->m_colorOrder->cek_status_color_order_details_by_row($kode_co,$row_order)->row_array();

              if($cek_status['status'] == 'draft'){
                $callback = array('status' => 'failed','message' => 'Maaf, Status Product masih Draft !', 'icon' =>'fa fa-check', 'type' => 'danger');

              }else if($cek_status['status'] == 'cancel'){
                $callback = array('status' => 'failed','message' => 'Maaf, Status Product sudah dibatalkan !', 'icon' =>'fa fa-check', 'type' => 'danger');

              }else{

                //update detail items
                $this->m_colorOrder->update_status_color_order_items($kode_co,$row_order,$status_cod);
                
                $status_cod_lama = $this->_module->get_mst_status_by_kode($cek_status['status']);
                $status_cod_baru = $this->_module->get_mst_status_by_kode($status_cod);

                $cek_details = $this->m_colorOrder->cek_status_color_order_items($kode_co,'')->num_rows();

                $where_status       = "AND status NOT IN ('generated','ng')";
                $cek_details_status = $this->m_colorOrder->cek_status_color_order_items($kode_co,$where_status)->num_rows();
      
                $where_status2       = "AND status NOT IN ('draft','generated','ng')";
                $cek_details_status2 = $this->m_colorOrder->cek_status_color_order_items($kode_co,$where_status2)->num_rows();
      
                if($cek_details == 0  ){
                    $this->m_colorOrder->ubah_status_color_order($kode_co,'draft');
                }else if($cek_details > 0){
                    if($cek_details_status == 0){
                        $this->m_colorOrder->ubah_status_color_order($kode_co,'done');
                    }else if($cek_details_status2 == 0){
                        $this->m_colorOrder->ubah_status_color_order($kode_co,'draft');
                    }else{
                        $this->m_colorOrder->ubah_status_color_order($kode_co,'cancel');
                    }
                }

                $jenis_log   = "edit";
                $note_log    = "Edit Status Details | ".$row_order." <br>".$status_cod_lama." -> ".$status_cod_baru;
                $this->_module->gen_history($sub_menu, $kode_co, $jenis_log, $note_log, $username);
                $callback = array('status' => 'success', 'message'=>'Data Berhasil diubah !', 'icon' => 'fa fa-check', 'type'=>'success');
                
              }


            }


        }

        echo json_encode($callback);

    }

    public function list_color_detail_modal()
    {

        $sales_order = $this->input->post('sales_order');

        $list = $this->m_colorOrder->get_datatables3($sales_order);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $field) {
            $no++;
            $row = array();
            $row[] = $no.".";
            $row[] = $field->ow;
            $row[] = $field->tanggal_ow;
            $row[] = $field->status_scl;
            $row[] = $field->nama_produk;
            $row[] = $field->nama_parent;
            $row[] = $field->nama_jenis_kain;
            $row[] = $field->nama_warna;
            $row[] = $field->qty;
            $row[] = $field->uom;
            $row[] = $field->lebar_jadi.' '.$field->uom_lebar_jadi;
            $row[] = $field->nama_handling;
            $row[] = $field->gramasi;
            $row[] = $field->nama_route;
            $row[] = $field->piece_info;
            $row[] = $field->reff_notes;
            $row[] = $field->row_order;
            $row[] = $field->status;
            //$row[] = '';//buat checkbox
            //$row[] = htmlentities($field->nama_produk)."|".$field->kode_warna."|".$field->qty."|".$field->uom."|".$field->piece_info."|^";
            $data[] = $row;
        }
        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_colorOrder->count_all3($sales_order),
            "recordsFiltered" => $this->m_colorOrder->count_filtered3($sales_order),
            "data" => $data,
        );
        //output dalam format JSON
        echo json_encode($output);

    } 

    public function save_color_detail_modal()
    {

        if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
            // session habis
            $callback = array('status' => 'failed','message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
        }else{

            $sub_menu  = $this->uri->segment(2);
            $username  = addslashes($this->session->userdata('username'));

            $so       = addslashes($this->input->post('txtso'));
            $kode_co  = addslashes($this->input->post('txtco'));
            $cl_id    = $this->input->post('checkbox');// isi row_order sales color line
            $countchek= $this->input->post('countchek');
            $ro       = $this->m_colorOrder->get_row_order_color_detail($kode_co)->row_array();
            //$route    = $this->m_colorOrder->get_default_route_co_by_kode($kode_co)->row_array();
            //$route_co = $route['route'];
            $row_order  = $ro['jml'] + 1;
            $is_approve = 't';

            foreach ($cl_id as $val) {
              
              // get isi sales color line by row_order dan SO
              $items = $this->m_colorOrder->get_sales_color_line_by_kode($so,$val)->row_array();
              $ow          = addslashes($items['ow']);
              $kode_produk = addslashes($items['kode_produk']);
              $nama_produk = addslashes($items['nama_produk']);
              $id_warna    = $items['id_warna'];
              $qty         = $items['qty'];
              $uom         = addslashes($items['uom']);
              $lebar_jadi  = $items['lebar_jadi'];
              $uom_lebar_jadi  = $items['uom_lebar_jadi'];
              $id_handling = $items['id_handling'];
              $gramasi     = $items['gramasi'];
              $reff_notes  = addslashes($items['piece_info']);
              $reff_notes_mkt = addslashes($items['reff_notes']);
              $row_color_line = $items['row_order'];
              $route_co    = $items['route_co'];

              $this->m_colorOrder->simpan_color_detail($kode_co,$ow,$kode_produk,$nama_produk,$id_warna,$qty,$uom,$reff_notes,'draft', $row_order,$route_co,$id_handling,$gramasi,$lebar_jadi,$uom_lebar_jadi,$reff_notes_mkt); 
              $row_order++;
              $this->m_colorOrder->update_one_is_approve_color_lines($so,$id_warna,$is_approve,$row_color_line);
            }

            // update status head colororder = draft
            $this->m_colorOrder->ubah_status_color_order($kode_co,'draft');
           
            $jenis_log   = "edit";
            $note_log    = "Tambah ".$countchek." Data Color Order Details";
            $this->_module->gen_history($sub_menu, $kode_co, $jenis_log, $note_log, $username);

            $callback    = array('status'=>'success', 'message' => 'Color Order Details Berhasil Ditambahkan !',  'icon' =>'fa fa-check', 'type' => 'success');
        }
        echo json_encode($callback);
    }


    public function get_data_detail_color()
    {
        $list = $this->m_colorOrder->get_datatables2();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $field) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $field->nosc;
            $row[] = $field->buyer_code;
            $row[] = '<a class="pilih" sale_contract="'.$field->nosc.'" buyer-code="'.$field->buyer_code.'">'.$field->mkt.'</a>';
 
            $data[] = $row;
        }
 
        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_colorOrder->count_all2(),
            "recordsFiltered" => $this->m_colorOrder->count_filtered2(),
            "data" => $data,
        );
        //output dalam format JSON
        echo json_encode($output);
    }


    public function view_detail_items()// view detail item modal
    {
        $kode_co     = $this->input->post('kode_co');
        $sales_order = $this->input->post('sales_order');
        $kode_produk = $this->input->post('kode_produk');
        $nama_produk = $this->input->post('nama_produk');
        $row_order   = $this->input->post('row_order');
        $ow          = $this->input->post('ow');
        $route_co    = $this->input->post('route');
        $origin      = addslashes($sales_order.'|'.$kode_co.'|'.$row_order.'|'.$ow);

        $data['kode_produk'] = $kode_produk;
        $data['nama_produk'] = $nama_produk;
        $data['row_order']   = $row_order;
        $data['origin']      = $origin;
        $data['ow']          = $ow;
        $data['route_co']    = $route_co;
        $data['penerimaan']  = $this->_module->get_detail_items_penerimaan($origin);
        $data['pengiriman']  = $this->_module->get_detail_items_pengiriman($origin);
        $data['mo']          = $this->_module->get_detail_items_mo($origin);
        //$data['detail_items']= $this->m_mo->view_detail_items($origi);
        return $this->load->view('modal/v_color_order_detail_items_modal', $data);
    }


}



?>