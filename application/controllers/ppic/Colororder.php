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
        $data['handling'] = $this->m_colorOrder->get_list_handling();
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
          $lbr_jadi   = $this->input->post('lbr_jadi');
          $handling   = $this->input->post('handling');


          if(empty($kode_sc)){
              $callback = array('status' => 'failed', 'field' => 'kode_sc', 'message' => 'Sales Contract Harus Diisi !', 'icon' =>'fa fa-warning', 
                  'type' => 'danger'  );    
          }elseif(empty($buyer_code)){
              $callback = array('status' => 'failed', 'field' => 'buyer_code', 'message' => 'Buyer Code Harus Diisi !', 'icon' =>'fa fa-warning', 
                  'type' => 'danger' );    
          }elseif(empty($tgl_sj)){
              $callback = array('status' => 'failed', 'field' => 'tgl_sj', 'message' => 'Tanggal Kirim / Surat Jalan Harus Diisi !', 'icon' =>'fa fa-warning', 
                  'type' => 'danger' );    
          }elseif(empty($note)){
              $callback = array('status' => 'failed', 'field' => 'note', 'message' => 'Note Harus Diisi !', 'icon' =>'fa fa-warning', 
                  'type' => 'danger' );    
          }elseif(empty($tgl)){
              $callback = array('status' => 'failed', 'field' => 'tgl', 'message' => 'Tanggal Harus Diisi !', 'icon' =>'fa fa-warning', 
                  'type' => 'danger' );    
          }elseif(empty($route)){
              $callback = array('status' => 'failed', 'field' => 'route', 'message' => 'Route Harus Diisi !', 'icon' =>'fa fa-warning', 
                  'type' => 'danger' );    
          }elseif(empty($lbr_jadi)){
              $callback = array('status' => 'failed', 'field' => 'lbr_jadi', 'message' => 'Lebar Jadi Harus Diisi !', 'icon' =>'fa fa-warning', 
                  'type' => 'danger' );    
          }elseif(empty($handling)){
              $callback = array('status' => 'failed', 'field' => 'handling', 'message' => 'Handling Harus Diisi !', 'icon' =>'fa fa-warning', 
                  'type' => 'danger' );    
          }else{
              if(empty($kode_co)){//jika kode co kosong, aksinya simpan data
                  $kode['kode_co'] =  $this->m_colorOrder->kode_co();
                  $kode_encrypt    = encrypt_url($kode['kode_co']);
                  $this->m_colorOrder->simpan($kode['kode_co'], $kode_sc, $buyer_code, $tgl_sj, $note, $tgl, $route, $lbr_jadi, $handling);

                  $callback = array('status' => 'success', 'field' => 'kode_co' , 'message' => 'Data Berhasil Disimpan !', 'isi'=> $kode['kode_co'], 'icon' =>'fa fa-check', 'type' => 'success', 'kode_encrypt' => $kode_encrypt);

                  $jenis_log = "create";
                  $note_log  =$kode['kode_co']."|".$route."|".$kode_sc."|".$buyer_code."|".$handling."|".$lbr_jadi."|".$tgl_sj."|".$note;
                  $this->_module->gen_history($sub_menu, $kode['kode_co'], $jenis_log, $note_log, $username);

              }else{//jika kode co ada, aksinya update data
                  $this->m_colorOrder->ubah($kode_co, $kode_sc, $buyer_code, $tgl_sj, $note, $tgl, $route, $lbr_jadi, $handling);
                  $callback = array('status' => 'success', 'field' => 'kode_co' , 'message' => 'Data Berhasil Disimpan !', 'isi' => $kode_co, 'icon' =>'fa fa-check', 'type' => 'success');

                  $jenis_log   = "edit";
                  $note_log    = "->".$kode_co."|".$route."|".$kode_sc."|".$buyer_code."|".$handling."|".$lbr_jadi."|".$tgl_sj."|".$note;
                  $this->_module->gen_history($sub_menu, $kode_co, $jenis_log, $note_log, $username);

              }
          }

        }

        echo json_encode($callback);
    }

    public function hapus($kode_co)
    {
        //$kode_co    = $this->input->post('kode_co');
        if(!isset($kode_co)) show_404();
        $result = $this->m_colorOrder->hapus($kode_co);
        echo json_encode($result);

    }

    public function edit($id = null)
    {   
        if(!isset($id)) show_404();
        $kode_decrypt  = decrypt_url($id);
        $data['id_dept']   ='CO';
        $data['detail']    = $this->m_colorOrder->get_data_detail_by_code($kode_decrypt);
        $data["colororder"] = $this->m_colorOrder->get_data_by_code($kode_decrypt);
        return $this->load->view('ppic/v_colorOrder_edit',$data);
    }

    public function generate()
    {
      $sub_menu  = $this->uri->segment(2);
      $username = $this->session->userdata('username'); 

      if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
        // session habis
        $callback = array('message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
      }else{
        $kode_co    = $this->input->post('kode_co');
        $cek_detail = $this->m_colorOrder->cek_color_details_by_kode($kode_co)->row_array();

        if(empty($cek_detail['kode_co'])){
          $callback = array('status' => 'kosong', 'message' => 'Color Details Masih Kosong', 'icon' =>'fa fa-warning', 'type' => 'danger');
        }else{
          $last_number    = 0;
          $last_bom       = 0;
          $last_move      = 0;
          $last_pengiriman= 0;
          $sql_insert_batch = "";
          $sql_update_batch = "";
          $case          = "";
          $where         = "";
          $sql_bom_batch      ="";
          $sql_bom_items_batch="";
          $source_move        = "";
          $sql_stock_move_batch       = "";
          $sql_stock_move_produk_batch = "";
          $sql_out_batch       = "";
          $sql_out_items_batch = "";
          $sql_in_batch        = "";
          $sql_in_items_batch  = "";
          $sql_mrp_prod_batch  = "";
          $sql_mrp_prod_rm_batch="";
          $sql_mrp_prod_fg_batch="";
          $sql_warna_batch      ="";

          if(!isset($kode_co)) show_404();

          //get info color order
          $cd  = $this->m_colorOrder->get_color_order($kode_co);

          //lock table
          $this->m_colorOrder->lock_tabel('mst_produk WRITE, mrp_route WRITE, mrp_route as mr WRITE, departemen WRITE, departemen as d WRITE, color_order_detail as cod WRITE,color_order_detail WRITE, color_order co WRITE, bom WRITE, bom_items WRITE, stock_move WRITE, stock_move_produk WRITE, penerimaan_barang WRITE, penerimaan_barang_items WRITE, pengiriman_barang WRITE, pengiriman_barang_items WRITE, mrp_production WRITE, mrp_production_rm_target WRITE, mrp_production_fg_target WRITE, Warna WRITE');
             
          /*----------------------------------
            Generate Produk Greige
          ----------------------------------*/
          $last_number= $this->m_colorOrder->get_kode_product();
          $kode_prod   = "MF".$last_number;
          foreach ($cd as $val) {
            $prod       = $val->nama_produk;
            $color      = $val->kode_warna;
            $qty        = $val->qty;
            $reff_notes = $val->reff_notes;
        
            $cek_prod1 = $this->m_colorOrder->cek_nama_product($prod)->row_array();
            if(empty($cek_prod1['nama_produk'])){
              //get lebar produk
              $exp = explode('"', $prod);
              $exp2= explode('-', $exp[0]);
              $lebar = array_pop($exp2);

              //generate product
              $tgl = date('Y-m-d H:i:s');
              $sql_insert_batch .= "('".$kode_prod."', '".$prod."', '".$tgl."', '".$lebar."'), ";
              $case  .= "when nama_produk = '".$prod."' then '".$kode_prod."'";
              $where .= "'".$prod."',";
              $last_number = $last_number + 1;
              $kode_prod   = "MF".$last_number;
            }else{
              $kode_prod = $cek_prod1['kode_produk'];
              $case .= "when nama_produk = '".$cek_prod1['nama_produk']."' then '".$cek_prod1['kode_produk']."'";
              $where .= "'".$cek_prod1['nama_produk']."',";
            }

          }//end foreach 1 (create produk greige)


          $where = rtrim($where, ',');
          $sql_update_batch  = "UPDATE color_order_detail SET kode_produk =(case ".$case." end) WHERE  nama_produk in (".$where.") AND kode_co = '".$kode_co."'";
          if(!empty($sql_insert_batch)){
            $sql_insert_batch = rtrim($sql_insert_batch, ', ');
            $this->m_colorOrder->simpan_product_batch($sql_insert_batch);
          }
          if(!empty($case)){
           $this->m_colorOrder->update_kode_product_color_detail_batch($sql_update_batch);
          }
          $sql_insert_batch = "";
          $sql_update_batch = "";
          $case             = "";
          $where            = "";

           /*--- Generate Warna -----*/
          $col  = $this->m_colorOrder->get_color_order_group_by_warna($kode_co);
          foreach ($col as  $val) {
            $color = $val->kode_warna;
            $cek_warna = $this->m_colorOrder->cek_kode_warna($color)->row_array();
            if(empty($cek_warna['kode_warna'])){
                $tgl           = date('Y-m-d H:i:s');
                $sql_warna_batch .= "('".$color."','draft','".$tgl."'), ";
            }
          }

          if(!empty($sql_warna_batch)){
            $sql_warna_batch = rtrim($sql_warna_batch, ', ');
            $this->m_colorOrder->simpan_warna_batch($sql_warna_batch);
          }


          /*----------------------------------
            Generate Produk Setelah Greige
          ----------------------------------*/
          $cod = $this->m_colorOrder->get_detail_color_order($kode_co);
          $last_number = $this->m_colorOrder->get_kode_product();
          $kode_prod   = "MF".$last_number; //set kode produk
          $last_bom    = $this->m_colorOrder->get_kode_bom();
          $kode_bom    = "BM".$last_bom; //set kode bom
          $last_move   = $this->m_colorOrder->get_kode_stock_move();
          $move_id     = "SM".$last_move; //Set kode stock_move
          $i           = 1; //set count kode in/out

          $last_mo     = $this->m_colorOrder->get_kode_mo();
          $dgt         = substr("00000" . $last_mo,-5);            
          $kode_mo     = "MO".date("y") .  date("m"). $dgt;

          foreach ($cod as $val) {

            if($val->status=='draft'){

              $kode_prod_rm = $val->kode_produk;
              $prod         = $val->nama_produk;
              $color        = $val->kode_warna;
              $ro_prod      = $val->row_order;
              $qty          = $val->qty;
              $uom          = $val->uom;
              $route        = $val->route;
              $reff_notes   = $val->reff_notes;

              //generate produk sesuai route
              $prod_exp      = explode('"',$prod);
              $product_warna = $prod_exp[0].'"-'.$color;
              $route_prod    = $this->m_colorOrder->get_route_product($route);

              //get lebar produk
              $exp = explode('"', $prod);
              $exp2= explode('-', $exp[0]);
              $lebar = array_pop($exp2);

              $reff_picking_in  = "";
              $reff_picking_out = "";
              $move_id_rm       = "";
              $move_id_fg       = "";
              $sm_row           = 1;
              $nama_prod_rm     = $prod;


              foreach ($route_prod as $rp) {

                //get semua product
                $tgl           = date('Y-m-d H:i:s');
                $mthd          = explode('|',$rp->method);
                $method_dept   = trim($mthd[0]);
                $method_action = trim($mthd[1]);

                $nama_dept        = $this->m_colorOrder->get_nama_dept_by_kode($method_dept)->row_array();
                $product_dept     = $nama_dept['nama'];
                $product_fullname = $product_warna." (".$product_dept.")";

                if ($method_action == 'PROD'){
                  $cek_prod2 = $this->m_colorOrder->cek_nama_product($product_fullname)->row_array();

                  if(empty($cek_prod2['nama_produk'])){
                    $sql_insert_batch .= "('".$kode_prod."','".$product_fullname."','".$tgl."', '".$lebar."'), ";
                    $last_number = $last_number + 1;
                  }else{
                    $kode_prod = $cek_prod2['kode_produk'];
                  }
                   
                  /*----------------------------------
                    Generate BOM
                  ----------------------------------*/
                  $cek_bom = $this->m_colorOrder->cek_bom($kode_prod)->row_array();

                  if(empty($cek_bom['kode_produk'])){
                    $sql_bom_batch .= "('".$kode_bom."','".$product_fullname."','".$kode_prod."','".$product_fullname."','1000','m'), ";
                    $sql_bom_items_batch .= "('".$kode_bom."','".$kode_prod_rm."','".$nama_prod_rm."','1000','m','1'), ";
                  }
                  $kode_prod_rm = $kode_prod;
                  $nama_prod_rm = $product_fullname;            
                  $kode_prod    = "MF".$last_number;
                }//end if PROD

                /*----------------------------------
                    Generate Stock Moves
                ----------------------------------*/

                $origin = $kode_co.'|'.$ro_prod; 
                $sql_stock_move_batch .= "('".$move_id."','".$tgl."','".$origin."','".$rp->method."','".$rp->lokasi_dari."','".$rp->lokasi_tujuan."','draft','".$sm_row."','".$source_move."'), ";
                $sql_stock_move_produk_batch .= "('".$move_id."','".$kode_prod_rm."','".$nama_prod_rm."','".$qty."','m','draft','1'), ";
                $sm_row = $sm_row + 1;

                if($method_action == 'OUT'){//Generate Pengiriman
          
                  if($i=="1"){
                    $arr_kode[$rp->method]= $this->m_colorOrder->get_kode_pengiriman($method_dept);
                  }else{
                    $arr_kode[$rp->method]= $arr_kode[$rp->method] + 1;
                  }
                  $dgt=substr("00000" . $arr_kode[$rp->method],-5);            
                  $kode_out = $method_dept."/".$method_action."/".date("y").  date("m").$dgt;
                    
                  $sql_out_batch  .= "('".$kode_out."','".$tgl."','".$tgl."','".$tgl."','".$reff_notes."','draft','".$method_dept."','".$origin."','".$move_id."','".$rp->lokasi_dari."','".$rp->lokasi_tujuan."'), ";
                  $sql_out_items_batch .= "('".$kode_out."','".$kode_prod_rm."','".$nama_prod_rm."','".$qty."','m','draft','1'), ";

                  $source_move = $move_id;
                 
                }elseif($method_action == 'IN'){//Generete Penerimaan
            
                  if($i=="1"){
                    $arr_kode[$rp->method]= $this->m_colorOrder->get_kode_penerimaan($method_dept);
                  }else{
                    $arr_kode[$rp->method]= $arr_kode[$rp->method] + 1;
                  }
                  $dgt     =substr("00000" . $arr_kode[$rp->method],-5);            
                  $kode_in = $method_dept."/".$method_action."/".date("y").  date("m").$dgt;

                  
                  $reff_picking_in = $kode_out."|".$kode_in;
                  $sql_in_batch   .= "('".$kode_in."','".$tgl."','".$tgl."','".$tgl."','".$reff_notes."','draft','".$method_dept."','".$origin."','".$move_id."','".$reff_picking_in."','".$rp->lokasi_dari."','".$rp->lokasi_tujuan."'), "; 
                  $sql_in_items_batch   .= "('".$kode_in."','".$kode_prod_rm."','".$nama_prod_rm."','".$qty."','m','draft','1'), "; 

                  $reff_picking_out = $kode_out."|".$kode_in;
                  $case  .= "when kode = '".$kode_out."' then '".$reff_picking_out."'";
                  $where .= "'".$kode_out."',";

                  $kode_out    = "";
                  $source_move = $move_id;

                }elseif($method_action == 'CON'){
                  $source_move = "";
                 
                  //get move id rm target
                  $move_id_rm = $move_id;
                  $kode_prod_rm_target = $kode_prod_rm;
                  $nama_prod_rm_target = $nama_prod_rm;
                  $qty_rm_target       = $qty;
                  $uom_rm_target       = $uom;

                }elseif($method_action == 'PROD'){// generate mo/mg
                  $source_move = $move_id;

                  /*----------------------------------
                      Generate MO / MG
                  ----------------------------------*/

                  $move_id_fg = $move_id;
                  $kode_prod_fg_target = $kode_prod_rm;
                  $nama_prod_fg_target = $nama_prod_rm;
                  $qty_fg_target       = $qty;
                  $uom_fg_target       = $uom;

                  $source_location = $method_dept."/Stock";
                  //sql simpan mrp_production
                  $sql_mrp_prod_batch .= "('".$kode_mo."','".$tgl."','".$origin."','".$kode_prod_rm."','".$nama_prod_rm."','".$qty."','m','".$tgl."','".$reff_notes."','".$kode_bom."','".$tgl."','".$tgl."','".$source_location."','".$source_location."','".$method_dept."','draft','".$color."'), ";

                  //sql simpan mrp production rm target
                  $sql_mrp_prod_rm_batch .= "('".$kode_mo."','".$move_id_rm."','".$kode_prod_rm_target."','".$nama_prod_rm_target."','".$qty_rm_target."','".$uom_rm_target."','1'), "; 

                  //sql simpan mrp production fg target
                  $sql_mrp_prod_fg_batch .= "('".$kode_mo."','".$move_id_fg."','".$kode_prod_fg_target."','".$nama_prod_fg_target."','".$qty_fg_target."','".$uom_fg_target."','1'), "; 
                  
                  $last_bom  = $last_bom + 1;
                  $last_mo   = $last_mo + 1;

                }
                
                $kode_bom  = "BM".$last_bom;

                $dgt       = substr("00000" . $last_mo,-5);            
                $kode_mo   = "MO".date("y") .  date("m"). $dgt;

                $last_move = $last_move + 1;
                $move_id   = "SM".$last_move;

              }//end foreach 3

              $i=$i+1;

            }//end if status draft
          }//end foreach 4

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

            if(!empty($sql_out_batch)){
              $sql_out_batch = rtrim($sql_out_batch, ', ');
              $this->m_colorOrder->simpan_pengiriman_batch($sql_out_batch);

              $sql_out_items_batch = rtrim($sql_out_items_batch, ', ');
              $this->m_colorOrder->simpan_pengiriman_items_batch($sql_out_items_batch);
            }
            if(!empty($sql_in_batch)){
              $sql_in_batch = rtrim($sql_in_batch, ', ');
              $this->m_colorOrder->simpan_penerimaan_batch($sql_in_batch);

              $sql_in_items_batch = rtrim($sql_in_items_batch, ', ');
              $this->m_colorOrder->simpan_penerimaan_items_batch($sql_in_items_batch);

              $where = rtrim($where, ',');
              $sql_update_reff_out_batch  = "UPDATE pengiriman_barang SET reff_picking =(case ".$case." end) WHERE  kode in (".$where.") ";
              $this->m_colorOrder->update_reff_picking_pengiriman_batch($sql_update_reff_out_batch);
            }

            if(!empty($sql_mrp_prod_batch)){
              $sql_mrp_prod_batch = rtrim($sql_mrp_prod_batch, ', ');
              $this->m_colorOrder->simpan_mrp_production_batch($sql_mrp_prod_batch);

              $sql_mrp_prod_rm_batch = rtrim($sql_mrp_prod_rm_batch, ', ');
              $this->m_colorOrder->simpan_mrp_production_rm_target_batch($sql_mrp_prod_rm_batch);

              $sql_mrp_prod_fg_batch = rtrim($sql_mrp_prod_fg_batch, ', ');
              $this->m_colorOrder->simpan_mrp_production_fg_target_batch($sql_mrp_prod_fg_batch);
            }


            //unlock table
            $this->m_colorOrder->unlock_tabel();
          
            $jenis_log = "done";
            $note_log  = $kode_co." - Generated";
            $callback = array('status' => 'success', 'message' => 'Generate Product Berhasil !', 'icon' =>'fa fa-check', 'type' => 'success');
            
            $this->m_colorOrder->ubah_status_color_order($kode_co,$jenis_log);
            $this->m_colorOrder->ubah_status_color_order_details($kode_co,$jenis_log);
            $this->_module->gen_history($sub_menu, $kode_co, $jenis_log,$note_log, $username);

            base_url('ppic/colororder/edit/'.$kode_co);
        }
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
            $row[] = $field->sales_order .' <a class="pilih" sale_contract="'.$field->sales_order.'" buyer-code="'.$field->buyer_code.'"><span class="glyphicon glyphicon-check"></span></a>';
            $row[] = $field->buyer_code;
            $row[] = '<a class="pilih" sale_contract="'.$field->sales_order.'" buyer-code="'.$field->buyer_code.'">'.$field->sales_group.'</a>';
 
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
        //$data['detail'] = $this->m_colorOrder->list_detail_color($kode_sc);

        return $this->load->view('modal/v_color_detail_modal',$data);
    }

    public function delete_color_detail()
    {
        $sub_menu  = $this->uri->segment(2);
        $username = $this->session->userdata('username'); 

        if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
            // session habis
            $callback = array('status' => 'failed','message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
        }else{
          $kode_co    = $this->input->post('kode_co');
          $row_order  = $this->input->post('row_order');
          $this->m_colorOrder->hapus_color_detail($kode_co,$row_order);

          $jenis_log   = "cancel";
          $note_log    = "Hapus Data Color Order Details";
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
        $origin     = $kode_co."|".$row_order;
        $data['sm'] = $this->m_colorOrder->get_stock_move_by_oirigin($origin);
        $data['penerimaan'] = $this->m_colorOrder->get_penerimaan_barang_by_origin($origin);
        $data['pengiriman'] = $this->m_colorOrder->get_pengiriman_barang_by_origin($origin);
        $data['mo']        = $this->m_colorOrder->get_mrp_production_by_origin($origin);
        $data['co'] = $kode_co;
        $data['ro'] = $row_order;
        $data['status'] = $status; 

        $data['get']= $this->m_colorOrder->get_color_detail_by_id($kode_co,$row_order)->row_array();
        return $this->load->view('modal/v_color_detail_edit_modal',$data);

    }

    public function update_color_detail()
    {   
        $kode_co    = $this->input->post('kode_co');
        $row_order  = $this->input->post('row_order');
        $qty    = $this->input->post('qty');
        $reff   = $this->input->post('reff');

        $this->m_colorOrder->ubah_color_detail($kode_co,$row_order,$qty,$reff);

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
            $row[] = $field->nama_produk;
            $row[] = $field->kode_warna;
            $row[] = $field->qty;
            $row[] = $field->uom;
            $row[] = $field->piece_info;
            $row[] = '';//buat checkbox
            $row[] = htmlentities($field->nama_produk)."|".$field->kode_warna."|".$field->qty."|".$field->uom."|".$field->piece_info."|^";
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
        $sub_menu  = $this->uri->segment(2);
        $username = $this->session->userdata('username'); 

        if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
            // session habis
            $callback = array('status' => 'failed','message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
        }else{

          $so      = $this->input->post('txtso');
          $kode_co = $this->input->post('txtco');
          $cl_id   = $this->input->post('checkbox');
          $countchek= $this->input->post('countchek');
          $ro      = $this->m_colorOrder->get_row_order_color_detail($kode_co)->row_array();
          $row_order = $ro['jml'] + 1;
          $is_approve = 't';

          $row = explode("^,", $cl_id);
          for($i=0; $i <= $countchek-1;$i++){
              $dt1 =  $row[$i];

              $row2 = explode("|", $dt1);
              $this->m_colorOrder->simpan_color_detail($kode_co,'',$row2[0],$row2[1],$row2[2],$row2[3],$row2[4],'draft', $row_order); 
              $row_order++;
              $this->m_colorOrder->update_one_is_approve_color_lines($so,$row2[1],$is_approve);

          }

          $jenis_log   = "edit";
          $note_log    = "Tambah Data Color Order Details";
          $this->_module->gen_history($sub_menu, $kode_co, $jenis_log, $note_log, $username);

          $callback    = array('status'=>'success', 'message' => 'Color Details Berhasil Ditambahkan !',  'icon' =>'fa fa-check', 'type' => 'success');
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


}



?>