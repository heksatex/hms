<?php defined('BASEPATH') or exit ('No Direct Script Acces Allowed');

/**
 * 
 */
class Adjustment extends MY_Controller
{
  public function __construct()
  {
    parent::__construct();
    $this->is_loggedin();//cek apakah user sudah login
    $this->load->model("m_adjustment");//load model
    $this->load->model("_module");
  }

  public function index()
  {
    $data['id_dept']='MADJ';
    $this->load->view('warehouse/v_adjustment', $data);
  }

  function get_data()
  {
      $sub_menu  = $this->uri->segment(2);
      $kode = $this->_module->get_kode_sub_menu($sub_menu)->row_array();
      $list = $this->m_adjustment->get_datatables();
      $data = array();
      $no = $_POST['start'];
      foreach ($list as $field) {
          $kode_encrypt = encrypt_url($field->kode_adjustment);
          $no++;
          $row = array();
          $row[] = $no;
          $row[] = '<a href="'.base_url('warehouse/adjustment/edit/'.$kode_encrypt).'">'.$field->kode_adjustment.'</a>';          
          $row[] = $field->create_date;
          $row[] = $field->lokasi_adjustment;
          $row[] = $field->kode_lokasi;
          $row[] = $field->name_type;
          $row[] = $field->note;
          $row[] = $field->status;

          $data[] = $row;
      }
      
      $output = array(
          "draw" => $_POST['draw'],
          "recordsTotal" => $this->m_adjustment->count_all(),
          "recordsFiltered" => $this->m_adjustment->count_filtered(),
          "data" => $data,
      );
      //output dalam format JSON
      echo json_encode($output);
  }

  public function add()
  { 
    $data['id_dept']  ='MADJ';    
    //$data['category'] = $this->m_produk->get_list_category();
    $data['type']    = $this->m_adjustment->get_list_type_adjustment();        
    $data['warehouse'] = $this->_module->get_list_departement();
    return $this->load->view('warehouse/v_adjustment_add', $data);
  }

  public function get_stock_location_by_departemen()
  {
    $kode_departemen  = addslashes($this->input->post('kode_departemen'));    
    $result           = $this->m_adjustment->get_stock_location_by_departemen($kode_departemen)->row_array();
    $callback         = array('stock_location'=>$result['stock_location']);
    echo json_encode($callback);
  }
  
  public function simpan()
  {
    $sub_menu  = $this->uri->segment(2);
    $username  = $this->session->userdata('username'); 
    $nama_user = $this->_module->get_nama_user($username)->row_array();

    if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
        // session habis
        $callback = array('message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
    }else{
      
      //lock table
      $this->_module->lock_tabel('adjustment WRITE, departemen WRITE, user WRITE, main_menu_sub WRITE, log_history WRITE, mst_type_adjustment WRITE');
      
      $kode_adjustment    = addslashes($this->input->post('kode_adjustment'));
      $create_date        = $this->input->post('create_date');      
      $lok_adjustment     = addslashes($this->input->post('lokasi_adjustment'));
      $type_adjustment    = $this->input->post('type_adjustment');
      $lokasi_adjustment  = $this->m_adjustment->get_nama_departemen_by_kode($lok_adjustment)->row_array();
      $kode_lokasi        = addslashes($this->input->post('kode_lokasi'));
      $note               = $this->input->post('note');
      $status             = addslashes($this->input->post('status'));
      
      if(empty($type_adjustment)){
        $callback = array('status' => 'failed', 'field' => 'type_adjustment', 'message' => 'Type Adjustement Harus Diisi !', 'icon' =>'fa fa-warning', 
            'type' => 'danger'  );                    
      }else if(empty($lokasi_adjustment['nama'])){
        $callback = array('status' => 'failed', 'field' => 'lokasi_adjustment', 'message' => 'Lokasi Adjustment Harus Diisi !', 'icon' =>'fa fa-warning', 
              'type' => 'danger'  );  
      }else if(empty($kode_lokasi)){
        $callback = array('status' => 'failed', 'field' => 'kode_lokasi', 'message' => 'Kode Lokasi Harus Diisi !', 'icon' =>'fa fa-warning', 
        'type' => 'danger'  );         
      }else if(empty($note)){
        $callback = array('status' => 'failed', 'field' => 'note', 'message' => 'Notes Harus Diisi / Alasan membuat Adjustment !', 'icon' =>'fa fa-warning', 
        'type' => 'danger'  );         
      }else if($lokasi_adjustment['stock_location'] != $kode_lokasi){
        $callback = array('status' => 'failed', 'field' => 'lokasi_adjustment', 'message' => 'Kode Lokasi Tidak Valid !', 'icon' =>'fa fa-warning', 'type' => 'danger'  );
      }else{
        //cek kode adjustment apa sudah ada apa belum
        $cek = $this->m_adjustment->cek_adjustment_by_kode($kode_adjustment)->row_array();
        // get nama type adjustment
        $nm = $this->m_adjustment->get_type_adjustment_by_kode($type_adjustment);
        $nama_type_adjustment = $nm->name_type ?? '';
        if(!empty($cek['kode_adjustment']) AND $status == 'tambah'){
          $callback = array('status' => 'failed', 'field' => 'note', 'message' => 'Adjustment Ini Sudah Pernah Diinput !', 'icon' =>'fa fa-warning', 'type' => 'danger'  );    
        }else if(!empty($cek['kode_adjustment'])){

          $cek_status = $this->m_adjustment->cek_status_adjustment($kode_adjustment,'')->row_array();
      
          if($cek_status['status'] == 'done'){
              $callback = array('status' => 'failed','message' => 'Maaf, Data Tidak Bisa Disimpan, Status Adjustment Sudah Done !', 'icon' =>'fa fa-check', 'type' => 'danger');
          }else if($cek_status['status'] == 'cancel'){
              $callback = array('status' => 'failed','message' => 'Maaf, Data Tidak Bisa Disimpan, Status Adjustment Sudah Batal !', 'icon' =>'fa fa-check', 'type' => 'danger');
          }else{
            //update/edit adjustment
            $this->m_adjustment->update_adjustment($cek['kode_adjustment'],$note,$type_adjustment);
            $kode_adjustment_encr = encrypt_url($cek['kode_adjustment']);
            $jenis_log   = "edit";
            $note_log    = $cek['kode_adjustment']." | ".$note.' | '.$nama_type_adjustment;
            $this->_module->gen_history($sub_menu, $kode_adjustment, $jenis_log, $note_log, $username);
            
            $callback = array('status' => 'success', 'message' => 'Data Berhasil Disimpan !', 'isi' => $kode_adjustment_encr, 'icon' =>'fa fa-check', 'type' => 'success');
          }

        }else{
          //insert/add adjustment
          //cek auto generate kode adjustment
          $kode_adjustment   = $this->_module->get_kode_adj();      
          $kode_adjustment   = substr("00000" . $kode_adjustment,-4);                  
          $kode_adjustment   = "ADJ/".date("y") . '/' .  date("m") . '/' . $kode_adjustment;


          $this->m_adjustment->save_header_adjustment($kode_adjustment,$create_date,$lokasi_adjustment['nama'],$kode_lokasi,$note,$status,$nama_user['nama'],$type_adjustment);
          $kode_adjustment_encr = encrypt_url($kode_adjustment);
          $jenis_log   = "create";
          $note_log    = $kode_adjustment." | ".$lokasi_adjustment['nama']." | ".$kode_lokasi.' | '.$nama_type_adjustment;
          $this->_module->gen_history($sub_menu, $kode_adjustment, $jenis_log, $note_log, $username);
          $callback = array('status' => 'success', 'message' => 'Data Berhasil Disimpan !', 'isi' => $kode_adjustment_encr, 'icon' =>'fa fa-check', 'type' => 'success');
        }
      }

      //unlock table
      $this->_module->unlock_tabel();
    }

    echo json_encode($callback);
  }

  public function edit($id = null)
  {
    if(!isset($id)) show_404();
        $kode_adjustment_decrypt = decrypt_url($id);
        $data['id_dept'] ='MADJ';
        $data['warehouse']  = $this->_module->get_list_departement();
        $data['type']       = $this->m_adjustment->get_list_type_adjustment();        
        $data['adjustment'] = $this->m_adjustment->get_adjustment_by_code($kode_adjustment_decrypt);
        $data['details'] = $this->m_adjustment->get_adjustment_detail_by_code($kode_adjustment_decrypt);
        if(empty($data["adjustment"])){
          show_404();
        }else{
          return $this->load->view('warehouse/v_adjustment_edit',$data);
        }
  }

  public function get_produk_adjustment_select2()
  {
    $prod     = addslashes($this->input->post('prod'));
    $callback = $this->m_adjustment->get_list_produk_adjustment($prod);
    echo json_encode($callback);
  }

  public function get_produk_by_id()
  {
    $kode_produk = addslashes($this->input->post('kode_produk'));
    $kode_lokasi = addslashes($this->input->post('kode_lokasi'));
    $result      = $this->m_adjustment->get_produk_by_id($kode_produk)->row_array();
    $qty_data    = $this->m_adjustment->get_total_qty_stock_quant_by_produk_lokasi($kode_produk,$kode_lokasi);
    $callback    = array('kode_produk'=>$result['kode_produk'],'nama_produk'=>$result['nama_produk'],'uom'=>$result['uom'], 'uom2'=>$result['uom_2'], 'qty_data'=>$qty_data->total);
    echo json_encode($callback);
  }

  public function import_produk()
  {
      $kode_lokasi  = $this->input->post('kode_lokasi');

      $kode_adjustment  = $this->input->post('kode_adjustment');

      $data['kode_lokasi'] = $kode_lokasi;

      $data['kode_adjustment'] = $kode_adjustment;
      
      return $this->load->view('modal/v_adjustment_import_produk_modal',$data);
  }

  public function list_import_produk()
  {
      $kode_lokasi  = addslashes($this->input->post('kode_lokasi'));

      $list = $this->m_adjustment->get_datatables2($kode_lokasi);
      $data = array();
      $no = $_POST['start'];
      foreach ($list as $field) {
          $no++;
          $row = array();
          $row[] = $no.".";
          $row[] = $field->kode_produk;
          $row[] = $field->nama_produk;
          $row[] = $field->lot;
          $row[] = number_format($field->qty,2)." ".$field->uom;
          $row[] = number_format($field->qty2,2)." ".$field->uom2;
          $row[] = $field->nama_grade;
          $row[] = $field->reff_note;
          $row[] = $field->reserve_move;
          $row[] = $field->quant_id;
          $data[] = $row;
      }
      $output = array(
          "draw" => $_POST['draw'],
          "recordsTotal" => $this->m_adjustment->count_all2($kode_lokasi),
          "recordsFiltered" => $this->m_adjustment->count_filtered2($kode_lokasi),
          "data" => $data,
      );
      //output dalam format JSON
      echo json_encode($output);
  }

  public function save_details_import_produk_adjustment_modal()
  { 
    $sub_menu  = $this->uri->segment(2);
    $username  = $this->session->userdata('username'); 

    if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
        // session habis
        $callback = array('status' => 'failed','message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
    }else{
      $arr_data         = $this->input->post('arr_data');
      $kode_adjustment  = $this->input->post('kode_adjustment');
      $countchek        = $this->input->post('countchek');
      $sql_adjustment_items_batch = "";
      
      //lock tabel
      $this->_module->lock_tabel('adjustment WRITE, adjustment_items WRITE, stock_quant WRITE, mrp_production_fg_hasil as mpfg WRITE, mrp_production as mp WRITE');
      
      //cek status adjustment = done
      $cek1  = $this->m_adjustment->cek_status_adjustment($kode_adjustment,'done')->row_array();
      //cek status adjustment = cancel
      $cek2  = $this->m_adjustment->cek_status_adjustment($kode_adjustment,'cancel')->row_array();

      if(!empty($cek1['status'])){
        $callback = array('status' => 'failed', 'message'=>'Maaf, Data Tidak Bisa Disimpan, Status Adjusment Sudah Done !', 'icon' => 'fa fa-warning', 'type'=>'danger');
      }else if(!empty($cek2['status'])){
        $callback = array('status' => 'failed', 'message'=>'Maaf, Data Tidak Bisa Disimpan, Status Adjustment Batal !', 'icon' => 'fa fa-warning', 'type'=>'danger');
      }else{
        //get row order adjustment_items
        $row_order  = $this->_module->get_row_order_adjustment_items_by_kode($kode_adjustment);
        $item_add   = false;
        $lot_sama   = '';
        $list_product = '';
        $no         = 1;

        foreach($arr_data as $row){

          // get data stock by quant_id
          $row_data = $this->m_adjustment->get_stock_quant_by_quant_id($row)->row_array();

          $kode_produk = $row_data['kode_produk'];
          $nama_produk = $row_data['nama_produk'];
          $lot         = $row_data['lot'];
          $qty         = $row_data['qty'];
          $uom         = $row_data['uom'];
          $qty2        = $row_data['qty2'];
          $uom2        = $row_data['uom2'];
          $quant_id    = $row_data['quant_id'];          
    
          //cek apakah quant_id sudah ada di dalam adjustment_items
          //jika sudah ada maka tidak usah ditambahkan lagi
          $cek_quant   = $this->m_adjustment->cek_quant_adjustment_items($kode_adjustment,$quant_id)->row_array();
          if(empty($cek_quant['kode_adjustment'])){

            // get kode MO berdasarkan quant_id (syarat hanya produk yang di poduksi di lokasi adj yg telah dipilih)
            $mo = $this->m_adjustment->get_kodeMO_by_quant_id($quant_id,$kode_adjustment)->row_array();

            $item_add = true;
            //insert ke adjustment_items
            $sql_adjustment_items_batch .= "('".$kode_adjustment."', '".$quant_id."', '".addslashes($kode_produk)."','".addslashes($lot)."', '".addslashes($uom)."','".$qty."',0, '".addslashes($uom2)."','".$qty2."',0, '".$mo['kode']."', '".$row_order."'), ";

            $list_product .= "(".$no.") ".$kode_produk." ".$nama_produk." ".$lot." ".$qty." ".$uom." ".$qty2." ".$uom2." <br>";
            $no++;
            $row_order++;            
          }else{
            $lot_sama .= $lot.', ';

          }

        }
    
        if(!empty($sql_adjustment_items_batch)){
          $sql_adjustment_items_batch = rtrim($sql_adjustment_items_batch, ', ');
          $this->m_adjustment->simpan_adjustment_items_batch($sql_adjustment_items_batch);
        }

        //unlock table
        $this->_module->unlock_tabel();  

        if($item_add == true){
          if(!empty($lot_sama)){
             $lot_sama = rtrim($lot_sama, ', ');
            $callback = array('status'=>'success',  'message' => 'Adjustment Detail Berhasil Ditambahkan !',  'icon' =>'fa fa-check', 'type' => 'success', 'msg2'=>'Yes', 'message2'=> 'Lot ( '.$lot_sama.' )</br> Sudah Pernah Diinput !'); 
          }else{
            $callback = array('status'=>'success',  'message' => 'Adjustment Detail Berhasil Ditambahkan !',  'icon' =>'fa fa-check', 'type' => 'success'); 
          }
            $jenis_log   = "edit";
            $note_log    = "Tambah Data Details ".$kode_adjustment." <br> ".$list_product;
            $this->_module->gen_history($sub_menu, $kode_adjustment, $jenis_log, $note_log, $username);
            
        }else if($item_add == false){
          $lot_sama = rtrim($lot_sama, ', ');
          $callback = array('status'=>'failed',  'message' => 'Lot ( '.$lot_sama.' )</br> Sudah Pernah Diinput !',  'icon' =>'fa fa-check', 'type' => 'success'); 

        }      


      }           
    
    }
    echo json_encode($callback);
  }

  //simpan adjustment items detail
  public function simpan_detail_adjustment_items()
  {
    if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
        // session habis
        $callback = array('message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
    }else{

      $sub_menu     = $this->uri->segment(2);
      $username     = addslashes($this->session->userdata('username')); 
      
      $kode_adjustment  = addslashes($this->input->post('kode_adjustment'));      
      $kode_produk      = addslashes($this->input->post('kode_produk'));
      $produk           = addslashes($this->input->post('nama_produk'));      
      $lot              = addslashes($this->input->post('lot'));      
      $uom              = addslashes($this->input->post('uom'));
      $qty_data         = $this->input->post('qty_data');
      $qty_adjustment   = $this->input->post('qty_adjustment');
      $uom2             = addslashes($this->input->post('uom2'));
      $qty_data2        = $this->input->post('qty_data2');
      $qty_adjustment2  = $this->input->post('qty_adjustment2'); 

      $row              = $this->input->post('row_order'); 
      $quant_id         = $this->input->post('quant_id'); 

      //$data         = explode("^|",$row1);
      //$row          = $data[0];
      $cek_status = $this->m_adjustment->cek_status_adjustment($kode_adjustment,'')->row_array();
      
      if(empty($kode_adjustment) && empty($row) ){
        $callback = array('status' => 'success','message' => 'Data Gagal Dihapus !', 'icon' =>'fa fa-warning', 'type' => 'danger');
      }else if($cek_status['status'] == 'done'){
        $callback = array('status' => 'failed','message' => 'Maaf, Data tidak bisa Disimpan, Status Adjustment Sudah Done !', 'icon' =>'fa fa-check', 'type' => 'danger');
      }else if($cek_status['status'] == 'cancel'){
        $callback = array('status' => 'failed','message' => 'Maaf, Data tidak bisa Disimpan, Status Adjustment Sudah Batal !', 'icon' =>'fa fa-check', 'type' => 'danger');
      }else{

        // lock table
        $this->_module->lock_tabel('adjustment WRITE, adjustment_items WRITE, adjustment_items as adji WRITE, mst_produk as mp WRITE, mst_produk WRITE');
        
        if(!empty($row)){//update details
          /*
          $quant_id     = $data[1];
          $kode_produk  = $data[2];
          $produk       = addslashes($data[3]);
          $lot          = addslashes($data[4]);
          $uom          = $data[5];
          $q
          ty_data     = $data[6];
         */ 

         // get adjustment items
         $get = $this->m_adjustment->get_adjustment_items_by_row($kode_adjustment,$row)->row_array();

         if($quant_id == 0){

              $this->m_adjustment->update_adjustment_items2($kode_adjustment,$kode_produk, trim($lot), $uom,$qty_adjustment,$uom2,$qty_adjustment2,$row);
              // produk before
              $note_      = $get['kode_produk'].' |'.$get['nama_produk'].' | '.$get['lot'].' | '.$get['qty_adjustment'].' '.$get['uom'].' | '.$get['qty_adjustment2'].' '.$get['uom2'].'  -> '.$kode_produk.' | '.$produk." | ".$lot." |  ".$qty_adjustment."  | ".$uom." |  ".$qty_adjustment2."  | ".$uom2;;
              $note_log   = "Edit data Details | ".$kode_adjustment." Baris Ke ".$row." <br> ".$note_;
              
         }else{
              $this->m_adjustment->update_adjustment_items($kode_adjustment,$row,$qty_adjustment,$qty_adjustment2);
              // produk before
              $note_      = $get['kode_produk'].' '.$get['nama_produk'].' '.$get['lot'].' | '.$get['qty_adjustment'].' '.$get['uom'].' | '.$get['qty_adjustment2'].' '.$get['uom2'].' -> '.$get['kode_produk'].' |'.$get['nama_produk']." ".$lot." | ".$qty_adjustment." ".$get['uom']." | ".$qty_adjustment2." ".$get['uom2'];
              $note_log   = "Edit data Details | ".$kode_adjustment." Baris Ke ".$row." <br> ".$note_;
         }

          // unlock table
          $this->_module->unlock_tabel();

          $jenis_log   = "edit";
          $this->_module->gen_history($sub_menu, $kode_adjustment, $jenis_log, addslashes($note_log), $username);
          $callback = array('status' => 'success','message' => 'Data Berhasil Disimpan !', 'icon' =>'fa fa-check', 'type' => 'success');

        }else{//simpan data baru

          $ro          = $this->m_adjustment->get_row_order_adjustment_items($kode_adjustment)->row_array();
          $row_order   = $ro['row_order']+1;
          $this->m_adjustment->save_adjustment_items($kode_adjustment,$kode_produk,trim($lot),$uom,$qty_data,$qty_adjustment,$uom2,$qty_data2,$qty_adjustment2,$row_order);

           // unlock table
           $this->_module->unlock_tabel();

          $jenis_log   = "edit";
          $note_log    = "Tambah data Details | ".$kode_adjustment." | ".$produk." | ".$lot." | ".$qty_data." | ".$qty_adjustment;
          $this->_module->gen_history($sub_menu, $kode_adjustment, $jenis_log, addslashes($note_log), $username);
          $callback = array('status' => 'success','message' => 'Data Berhasil Disimpan !', 'icon' =>'fa fa-check', 'type' => 'success');
        }

      }


      echo json_encode($callback);
    }
  }


  public function hapus_adjustment_items()
  {
    if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
      // session habis
      $callback = array('message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
    }else{
      $sub_menu  = $this->uri->segment(2);
      $username  = addslashes($this->session->userdata('username')); 

      $kode_adjustment = addslashes($this->input->post('kode_adjustment'));
      $row  = $this->input->post('row_order');
      /*
      $data = explode("^|",$row);
      $row_order    = $data[0];      
      $quant_id     = $data[1];      
      $kode_produk  = addslashes($data[2]);
      $produk       = addslashes($data[3]);
      $lot          = $data[4];
      $uom          = addslashes($data[5]);
      $qty_data     = $data[6];
      $qty_adjustment = $data[7];
      */
      
      $cek_status = $this->m_adjustment->cek_status_adjustment($kode_adjustment,'')->row_array();
      
      if(empty($kode_adjustment) && empty($row) ){
        $callback = array('status' => 'success','message' => 'Data Gagal Dihapus !', 'icon' =>'fa fa-warning', 'type' => 'danger');
      }else if($cek_status['status'] == 'done'){
        $callback = array('status' => 'failed','message' => 'Maaf, Data tidak bisa Dihapus, Status Adjustment Sudah Done !', 'icon' =>'fa fa-check', 'type' => 'danger');
      }else if($cek_status['status'] == 'cancel'){
        $callback = array('status' => 'failed','message' => 'Maaf, Data tidak bisa Dihapus, Status Adjustment Sudah Batal !', 'icon' =>'fa fa-check', 'type' => 'danger');
      }else{
        
        // lock table
        $this->_module->lock_tabel('adjustment WRITE, adjustment_items WRITE, adjustment_items as adji WRITE, mst_produk as mp WRITE');

        // get adjustment items
        $get = $this->m_adjustment->get_adjustment_items_by_row($kode_adjustment,$row)->row_array();

        $this->m_adjustment->delete_adjustment_items($kode_adjustment,$row);

        // unlock table
        $this->_module->unlock_tabel();
        
        $callback = array('status' => 'success','message' => 'Data Berhasil Dihapus !', 'icon' =>'fa fa-check', 'type' => 'success');
        $jenis_log   = "cancel";        
        $note_log    = "Hapus data Details Baris Ke ".$row." <br> ".$kode_adjustment." | ".$get['kode_produk']." | ".$get['nama_produk']." | ".$get['lot']." | ".$get['qty_data']." ".$get['uom']." ".$get['qty_adjustment']." | ".$get['qty_data2']." ".$get['uom2']." ".$get['qty_adjustment2'];
        $this->_module->gen_history($sub_menu, $kode_adjustment, $jenis_log, addslashes($note_log), $username);
      }

      echo json_encode($callback);
    }
  }
  
  public function get_uom_select2()
  {
    $prod = addslashes($this->input->post('prod'));
    $callback = $this->m_adjustment->get_list_uom_select2_by_prod($prod);
    echo json_encode($callback);
  }


  public function generate_detail_adjustment_items()
  {

    if(empty($this->session->userdata('username'))){
      // session habis
      $callback = array('message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
    }else{

      $sub_menu  = $this->uri->segment(2);
      $username  = addslashes($this->session->userdata('username')); 
      $nama_user = $this->_module->get_nama_user($username)->row_array();

      $kode_adjustment = $this->input->post('kode_adjustment');
      $kode_lokasi     = $this->input->post('kode_lokasi'); // ex JAC/Stock, BRS/Stock, GRG/Stock etc
      $lokasi_adj      = $this->input->post('lokasi_adj'); // ex JAC, BRS, GRG etc
      $tanggal         = date('Y-m-d H:i:s');
      $status_done     = "done";

      $sql_stock_quant_batch = "";
      $sql_stock_move_batch  = "";
      $sql_stock_move_produk_batch = "";
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
      $case8  = "";
      //$where8 = "";
      $case9  = "";


      //cek status adjustment = done
      $cek1  = $this->m_adjustment->cek_status_adjustment($kode_adjustment,'done')->row_array();
      //cek status adjustment = cancel
      $cek2  = $this->m_adjustment->cek_status_adjustment($kode_adjustment,'cancel')->row_array();

      $cek_head = $this->m_adjustment->get_adjustment_by_code($kode_adjustment);

      // cek lokasivalid      
      $lokasi_adjustment  = $this->m_adjustment->get_nama_departemen_by_kode($lokasi_adj)->row_array();

      if(!empty($cek1['status'])){
        $callback = array('status' => 'failed', 'message'=>'Maaf, Tidak Bisa Generate, Status Adjustment Sudah Done !', 'icon' => 'fa fa-warning', 'type'=>'danger');
      }else if(!empty($cek2['status'])){
        $callback = array('status' => 'failed', 'message'=>'Maaf, Tidak Bisa Generate, Status Adjustment Batal !', 'icon' => 'fa fa-warning', 'type'=>'danger');
      }else if(empty($lokasi_adj)){
        $callback = array('status' => 'failed', 'message'=>'Maaf, Lokasi Adjustment Tidak Boleh Kosong !', 'icon' => 'fa fa-warning', 'type'=>'danger');
      }else if(empty($cek_head->id_type_adjustment)){
          $callback = array('status' => 'failed', 'message'=>'Maaf, Type Adjustment Tidak Boleh Kosong !', 'icon' => 'fa fa-warning', 'type'=>'danger');
      }else if($lokasi_adjustment['stock_location'] != $kode_lokasi){
          $callback = array('status' => 'failed', 'field' => 'lokasi_adjustment', 'message' => 'Kode Lokasi Tidak Valid !', 'icon' =>'fa fa-warning', 'type' => 'danger'  );
      }else{

        // lock table
        $this->_module->lock_tabel('stock_move WRITE, stock_move_items WRITE, stock_move_produk WRITE, stock_quant WRITE, adjustment WRITE, adjustment_items WRITE, mst_produk WRITE, departemen WRITE, adjustment_items ai WRITE, mst_produk mp WRITE, user WRITE, log_history WRITE, main_menu_sub WRITE, stock_quant as sq WRITE, picklist_detail WRITE, mst_category as cat WRITE, mst_status as sat WRITE');

        // get move_id
        $last_move   = $this->_module->get_kode_stock_move();
        $move_id     = "SM".$last_move; //Set kode stock_move
        // get quant_id
        $start       = $this->_module->get_last_quant_id();
        // get lokasi adjustmen
        $la          = $this->m_adjustment->get_adjustment_location_by_kode_departemen($lokasi_adj)->row_array();
        $sm_row      = 1;
        $jml_adj     = 0;
        $loop_adj    = false;
        $qty_stok_adj_manual = false;
        $qty_data_adj_same   = false;
        $qty_adj_null        = false;
        $reserve_move_empty  = true;
        $reserve_pl_empty  = true;
        $list_produk         = '';
        $list_produk2        = '';
        $list_produk3        = '';
        $list_quant          = '';
        $lokasi_produk_valid = true;
        $quant               = true;
        $stock_lot_same = false;
        $lokasi_same_lot = "";
 
        $item =  $this->m_adjustment->get_adjustment_detail_by_code($kode_adjustment);
        foreach($item as $row){

          $origin  = $kode_adjustment.'|'.$row->row_order;
          $method  = $lokasi_adj.'|ADJ';
          
          // jika quant_id = 0 (save stock_quant, stock_move, stock_move_items, stock_move_produk)
          if($row->quant_id == 0){

              if($row->qty_adjustment == 0 OR empty($row->qty_adjustment)){
                $qty_stok_adj_manual = true; // tambah produk / stock manual
                break;
              }else{
                $loop_adj = true;
              }
             
              // simpan stock_quant
              $sql_stock_quant_batch .= "('".$start."','".$tanggal."','".addslashes($row->kode_produk)."','".addslashes($row->nama_produk)."','".addslashes($row->lot)."','','".$row->qty_adjustment."','".$row->uom."','".$row->qty_adjustment2."','".$row->uom2."','".$kode_lokasi."','','','','".$tanggal."','".addslashes($row->lebar_greige)."','".addslashes($row->uom_lebar_greige)."','".addslashes($row->lebar_jadi)."','".addslashes($row->uom_lebar_jadi)."','".addslashes($row->sales_order)."','".addslashes($row->sales_group)."'), ";

              // simpan stock_move
              $sql_stock_move_batch .= "('".$move_id."','".$tanggal."','".$origin."','".$method."','".$la['adjustment_location']."','".$kode_lokasi."','".$status_done."','".$sm_row."',''), ";

              // simpan stock_move_items
              $sql_stock_move_items_batch .= "('".$move_id."', '".$start."','".addslashes($row->kode_produk)."', '".addslashes($row->nama_produk)."','".addslashes(($row->lot))."','".$row->qty_adjustment."','".($row->uom)."','".$row->qty_adjustment2."','".$row->uom2."','".$status_done."','1','','".$tanggal."','','".addslashes($row->lebar_greige)."','".addslashes($row->uom_lebar_greige)."','".addslashes($row->lebar_jadi)."','".addslashes($row->uom_lebar_jadi)."'), ";

              // simpan stock_move_produk
              $sql_stock_move_produk_batch .= "('".$move_id."','".addslashes($row->kode_produk)."','".addslashes($row->nama_produk)."','".$row->qty_adjustment."','".$row->uom."','".$status_done."','1',''), ";

              //update move_id adjustment items
              $case .= " when row_order = '".$row->row_order."' then '".$move_id."'";
              $where.= "'".$row->row_order."',";

              // update quant_id adjustment_items
              $case6 .= " when row_order = '".$row->row_order."' then '".$start."'";
              $where6.= "'".$row->row_order."',";

              // update qty_move adjustment_items
              $qty_move = $row->qty_adjustment;
              $case7 .= " when row_order = '".$row->row_order."' then '".$qty_move."'";
              $where7.= "'".$row->row_order."',";
              $qty2_move = $row->qty_adjustment2;
              $case9 .= " when row_order = '".$row->row_order."' then '".$qty2_move."'";

              $start++;
              $sm_row++; //row order stock_move
              $move_id++;
              $jml_adj++;

              // Cek apakah terdapat lot yang sama di lokasi stock untuk kategori Kain Hasil
              $cek_nm_cat = $this->_module->get_prod($row->kode_produk)->row_array();
              if(stripos(strtolower($cek_nm_cat['nama_category']), 'kain') !== FALSE){
                  // cek stock terdpat produk yang menggunakan lot yg sama 
                  $cek_same_lot = $this->m_adjustment->get_stock_quant_by_lot($row->lot)->row_array();
                  if(!empty($cek_same_lot)){
                    $lokasi_same_lot .= $row->lot." Lokasi di ".$cek_same_lot['lokasi'].'<br>';
                    $stock_lot_same  = true;
                  }
              }
  
          }else{

              // cek qty stock_quant
              $sq = $this->m_adjustment->get_cek_qty_stock_quant_by_kode($row->quant_id)->row_array();
              if(!empty($sq)){// jiak quant nya ada
                $nama_grade = $sq['nama_grade'];
                $reff_note  = $sq['reff_note'];
                $reserve_origin = $sq['reserve_origin'];
                $reserve_move = $sq['reserve_move'];
                // cek quant_id / lot apa terpesan di PL atau tidak 
                $reserve_pl = $this->m_adjustment->cek_quant_id_in_picklist_by_kode($row->quant_id,$row->lot);

                if($kode_lokasi != $sq['lokasi'] ){
                  $lokasi_produk_valid = false;
                  $list_produk .= $row->nama_produk." ".$row->lot." <br>";
                }else if($reserve_move != ''){
                  $reserve_move_empty = false;
                  $list_produk2 .= $row->nama_produk." ".$row->lot." <br>";
                }else if(!empty($reserve_pl)){
                  $reserve_pl_empty = false;
                  $list_produk3 .= $row->nama_produk." ".$row->lot." <br>";
                }else{

                  // >> QTY 1
                  // jika qty stock == qty data 
                  if($sq['qty'] == $row->qty_data){
                    $qty_data       = $row->qty_data;
                    $qty_adjustment = $row->qty_adjustment;

                  }else{ // qty stock > qty_data atau qty stock < qty data

                    $qty_data       = $sq['qty'];
                    $qty_adjustment = $row->qty_adjustment;

                    // update qty_data adjustment items
                    $case2 .= " when row_order = '".$row->row_order."' then '".$sq['qty']."'";
                    $where2.= "'".$row->row_order."',";
                  
                  } 

                  // << QTY 1
                  
                  
                  // >> QTY 2

                  // jika qty2 stock == qty_data2
                  if($sq['qty2'] == $row->qty_data2){
                    $qty_data2      = $row->qty_data2;
                    $qty_adjustment2= $row->qty_adjustment2;

                  }else{
                    $qty_data2      = $sq['qty2'];
                    $qty_adjustment2= $row->qty_adjustment2;

                    // update qty_data2 adjustment items
                    $case3 .= " when row_order = '".$row->row_order."' then '".$sq['qty2']."'";
                    $where3.= "'".$row->row_order."',";

                  }

                  // << QTY 2


                  //jika qty_data != qty_adjustment
                  if($qty_adjustment == 0 AND $qty_adjustment2 == 0){
                    
                    // jika qty_data > qty_adjustment
                    //if($qty_data > $qty_adjustment){
                      $loop_adj = true;
      
                      $quant_id_new = false;

                      $lokasi_dari   = $kode_lokasi;
                      $lokasi_tujuan = $la['adjustment_location'];

                      // qty stock yang di adj
                      $qty_adj = $qty_data - $qty_adjustment;

                      // qty 2
                      if($qty_data2 > $qty_adjustment2){
                        $qty2_adj  = $qty_data2 - $qty_adjustment2;
                      }else if($qty_data2 < $qty_adjustment2){
                        $qty2_adj  = $qty_adjustment2 - $qty_data2;
                      }else{ // qty2 sama
                        $qty2_adj  = $qty_data2 - $qty_adjustment2;
                      }
                      
                      if($qty_adjustment != 0){
                          // insert stock quant dengan id baru 
                          $sql_stock_quant_batch .= "('".$start."','".$tanggal."','".addslashes($row->kode_produk)."','".addslashes($row->nama_produk)."','".addslashes($row->lot)."','".$nama_grade."','".$qty_adj."','".$row->uom."','".$qty2_adj."','".$row->uom2."','".$lokasi_tujuan."','".$reff_note."','','".$reserve_origin."','".$tanggal."','".addslashes($row->lebar_greige)."','".addslashes($row->uom_lebar_greige)."','".addslashes($row->lebar_jadi)."','".addslashes($row->uom_lebar_jadi)."','".addslashes($row->sales_order)."','".addslashes($row->sales_group)."'), ";

                          $quant_id_new = true; // untuk stock_move_items
                      
                      }

                      if($qty_adjustment != 0 ){// OR $qty_adjustment == 0
                          // update qty baru stock_quant berdasarkan qty_adjustment items dengan quant_id sebelumnya
                          $case4 .= " when quant_id = '".$row->quant_id."' then '".$qty_adjustment."'";
                          $where4.= "'".$row->quant_id."',";

                          // update qty2 baru stock_quant berdasarkan qty_adjustmen2 items dengan quant_id sebelumnya
                          $case8 .= " when quant_id = '".$row->quant_id."' then '".$qty_adjustment2."'";
                          //$where8.= "'".$row->quant_id."',";

                      }

                      // update lokasi jadi adj berdasarkan quant_id lama
                      if($qty_adjustment == 0){

                          if($qty_adjustment2 == 0 ){
                            $case5 .= " when quant_id = '".$row->quant_id."' then '".$lokasi_tujuan."'";
                            $where5.= "'".$row->quant_id."',";
                          }else{
                            // update qty baru stock_quant berdasarkan qty_adjustment items dengan quant_id sebelumnya
                            $case4 .= " when quant_id = '".$row->quant_id."' then '".$qty_adjustment."'";
                            $where4.= "'".$row->quant_id."',";

                            // update qty2 baru stock_quant berdasarkan qty_adjustmen2 items dengan quant_id sebelumnya
                            $case8 .= " when quant_id = '".$row->quant_id."' then '".$qty_adjustment2."'";

                            // insert stock quant dengan id baru 
                            $sql_stock_quant_batch .= "('".$start."','".$tanggal."','".addslashes($row->kode_produk)."','".addslashes($row->nama_produk)."','".addslashes($row->lot)."','".$nama_grade."','".$qty_adj."','".$row->uom."','".$qty2_adj."','".$row->uom2."','".$lokasi_tujuan."','".$reff_note."','','".$reserve_origin."','".$tanggal."','".addslashes($row->lebar_greige)."','".addslashes($row->uom_lebar_greige)."','".addslashes($row->lebar_jadi)."','".addslashes($row->uom_lebar_jadi)."','".addslashes($row->sales_order)."','".addslashes($row->sales_group)."'), ";
                            $quant_id_new = true; // untuk stock_move_items
                          }
                      }

                      // update qty_move adjustment_items
                      $qty_move = $qty_adjustment - $qty_data;
                      $case7 .= " when row_order = '".$row->row_order."' then '".$qty_move."'";
                      $qty2_move = $qty_adjustment2 - $qty_data2;
                      $case9 .= " when row_order = '".$row->row_order."' then '".$qty2_move."'";
                      $where7.= "'".$row->row_order."',";

                      //update move_id ajdustment_items
                      $case .= " when row_order = '".$row->row_order."' then '".$move_id."'";
                      $where.= "'".$row->row_order."',";

                      // simpan stock_move
                      $sql_stock_move_batch .= "('".$move_id."','".$tanggal."','".$origin."','".$method."','".$lokasi_dari."','".$lokasi_tujuan."','".$status_done."','".$sm_row."',''), ";

                      if($quant_id_new == true) {// cek apa quant_id di SMI pakai quant_id baru atau lama
                        $quant_id = $quant_id_new = $start;
                      }else{
                        $quant_id =  $row->quant_id;
                      }

                      // simpan stock_move_items
                      $sql_stock_move_items_batch .= "('".$move_id."', '".$quant_id."','".addslashes($row->kode_produk)."', '".addslashes($row->nama_produk)."','".addslashes(($row->lot))."','".$qty_adj."','".$row->uom."','".$qty2_adj."','".$row->uom2."','".$status_done."','1','','".$tanggal."','".addslashes($row->lokasi_fisik)."','".addslashes($row->lebar_greige)."','".addslashes($row->uom_lebar_greige)."','".addslashes($row->lebar_jadi)."','".addslashes($row->uom_lebar_jadi)."'), ";

                      // simpan stock_move_produk
                      $sql_stock_move_produk_batch .= "('".$move_id."','".addslashes($row->kode_produk)."','".addslashes($row->nama_produk)."','".$qty_adj."','".$row->uom."','".$status_done."','1',''), ";

                      $start++;
                      $move_id++;
                      $sm_row++; //row order stock_move
                      $jml_adj++;

                    // }else{
                    //   $qty_data_adj_same = true;
                    // }

                    /* // jika qty_data < qty_adjustment
                    if($qty_data < $qty_adjustment ){
                      $lokasi_dari   = $la['adjustment_location'];
                      $lokasi_tujuan = $kode_lokasi;

                      // qty adj untuk ditambah ke stock
                      $qty_adj  = $qty_adjustment - $qty_data;

                      // qty 2
                      if($qty_data2 > $qty_adjustment2){
                        $qty2_adj  = $qty_data2 - $qty_adjustment2;
                      }else if($qty_data2 < $qty_adjustment2){
                        $qty2_adj  = $qty_adjustment2 - $qty_data2;
                      }else{ // qty2 sama
                        $qty2_adj  = $qty_data2 - $qty_adjustment2;
                      }

                      // insert stock quant dengan id baru 
                      $sql_stock_quant_batch .= "('".$start."','".$tanggal."','".addslashes($row->kode_produk)."','".addslashes($row->nama_produk)."','".addslashes($row->lot)."','".$nama_grade."','".$qty_adj."','".$row->uom."','".$qty2_adj."','".$row->uom2."','".$lokasi_tujuan."','".$reff_note."','','".$reserve_origin."','".$tanggal."','".addslashes($row->lebar_greige)."','".addslashes($row->uom_lebar_greige)."','".addslashes($row->lebar_jadi)."','".addslashes($row->uom_lebar_jadi)."','".addslashes($row->sales_order)."','".addslashes($row->sales_group)."'), ";

                      $quant_id_new = true; // untuk stock_move_items

                    } */

                    /* //jika qty_data == qty_adjustment
                    if($qty_data == $qty_adjustment){
                      $lokasi_dari   = $la['adjustment_location'];
                      $lokasi_tujuan = $kode_lokasi;
                      
                      $qty_adj  = $qty_data - $qty_adjustment; // hasilnya pasti 0

                      // qty 2
                      if($qty_data2 > $qty_adjustment2){
                        $qty2_adj  = $qty_data2 - $qty_adjustment2;
                        $lokasi_dari   = $kode_lokasi;
                        $lokasi_tujuan = $la['adjustment_location'];

                        if($qty_adj != 0 AND $qty2_adj != 0){
                          // update qty baru stock_quant berdasarkan qty_adjustment items dengan quant_id sebelumnya
                          $case4 .= " when quant_id = '".$row->quant_id."' then '".$qty_adjustment."'";
                          $where4.= "'".$row->quant_id."',";
                          
                          // update qty2 baru stock_quant berdasarkan qty_adjustmen2 items dengan quant_id sebelumnya
                          $case8 .= " when quant_id = '".$row->quant_id."' then '".$qty_adjustment2."'";
                        }

                      }else if($qty_data2 < $qty_adjustment2){
                        $qty2_adj  = $qty_adjustment2 - $qty_data2;
                        $lokasi_dari   = $la['adjustment_location'];
                        $lokasi_tujuan = $kode_lokasi;
                      }else{ // qty2 sama
                        $qty2_adj  = $qty_adjustment2;
                        $qty_data_adj_same = true;// break krna qty dan qty 2 tidak dirubah/sama

                      }

                      if($qty_adjustment2 != 0 or $qty_adjustment2 == 0){
                        if($qty_adjustment == 0 AND $qty_adjustment2 == 0){ // update lokasi

                          $case5 .= " when quant_id = '".$row->quant_id."' then '".$lokasi_tujuan."'";
                          $where5.= "'".$row->quant_id."',";

                        }else{

                          // update qty baru stock_quant berdasarkan qty_adjustment items dengan quant_id sebelumnya
                          $case4 .= " when quant_id = '".$row->quant_id."' then '".$qty_adjustment."'";
                          $where4.= "'".$row->quant_id."',";

                          // update qty2 baru stock_quant berdasarkan qty_adjustmen2 items dengan quant_id sebelumnya
                          $case8 .= " when quant_id = '".$row->quant_id."' then '".$qty_adjustment2."'";

                          // insert stock quant dengan id baru 
                          $sql_stock_quant_batch .= "('".$start."','".$tanggal."','".addslashes($row->kode_produk)."','".addslashes($row->nama_produk)."','".addslashes($row->lot)."','".$nama_grade."','".$qty_adj."','".$row->uom."','".$qty2_adj."','".$row->uom2."','".$lokasi_tujuan."','".$reff_note."','','".$reserve_origin."','".$tanggal."','".addslashes($row->lebar_greige)."','".addslashes($row->uom_lebar_greige)."','".addslashes($row->lebar_jadi)."','".addslashes($row->uom_lebar_jadi)."','".addslashes($row->sales_order)."','".addslashes($row->sales_group)."'), ";
                          
                          $quant_id_new = true; // untuk stock_move_items
                        }
                      }

                    }
                    */

                  }else if($qty_data != $qty_adjustment or $qty_data2 != $qty_adjustment2){ // 
                    if($qty_data == $qty_adjustment  or  $qty_data2 == $qty_adjustment2 ){
                      $qty_data_adj_same = true;
                    }else{
                      $qty_adj_null = true;
                    }
                  }else{
                    $qty_data_adj_same = true;
                  }
                }
              }else{
                $quant = false;
                $list_quant .= $row->nama_produk." ".$row->lot." <br>";

              }

          }

        }// end foreach adjusment details


        if($loop_adj == true AND $qty_data_adj_same == false AND $qty_adj_null == false AND $reserve_move_empty == true AND $lokasi_produk_valid == true AND $quant == true AND $reserve_pl_empty == true AND $stock_lot_same == false){

  
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
          if(!empty($sql_stock_quant_batch)){
              $sql_stock_quant_batch = rtrim($sql_stock_quant_batch, ', ');
              $this->_module->simpan_stock_quant_batch($sql_stock_quant_batch);
          }

          // update move id ajdustment_items
          if(!empty($case) AND !empty($where)){
            $where = rtrim($where, ',');
            $sql_update_move_id_adj = "UPDATE adjustment_items SET move_id =(case ".$case." end) WHERE  row_order in (".$where.") AND kode_adjustment = '".$kode_adjustment."' ";
            $this->_module->update_reff_batch($sql_update_move_id_adj);
          }
          

          // update qty_data adjusment
          if(!empty($case2) AND !empty($where2)){
            $where2 = rtrim($where2, ',');
            $sql_update_qty_data = "UPDATE adjustment_items SET qty_data = (case ".$case2." end)  WHERE row_order in (".$where2.") AND kode_adjustment = '".$kode_adjustment."'";
            $this->_module->update_reff_batch($sql_update_qty_data);
          }


          // update qty, qty2 stock_quant
          if(!empty($case4) AND !empty($where4)){
            $where4 = rtrim($where4, ',');
            $sql_update_qty_stock_quant = "UPDATE stock_quant SET qty = (case ".$case4." end), qty2 = (case ".$case8." end)  WHERE quant_id in (".$where4.") ";
            $this->_module->update_reff_batch($sql_update_qty_stock_quant);
          }

          // update lokasi stock_quant
          if(!empty($case5) AND !empty($where5)){
            $where5 = rtrim($where5, ',');
            $sql_update_lokasi_stock_quant = "UPDATE stock_quant SET lokasi = (case ".$case5." end), move_date = '".$tanggal."'  WHERE quant_id in (".$where5.") ";
            $this->_module->update_reff_batch($sql_update_lokasi_stock_quant);
          }

          // update quant_id ajdustment_items
          if(!empty($case6) AND !empty($where6)){
            $where6 = rtrim($where6, ',');
            $sql_update_quant_id = "UPDATE adjustment_items SET quant_id = (case ".$case6." end) WHERE row_order in (".$where6.") AND kode_adjustment = '".$kode_adjustment."'";
            $this->_module->update_reff_batch($sql_update_quant_id);
          }

          // update qty_move adjustment_items
          if(!empty($case7) AND !empty($where7)){
            $where7 = rtrim($where7, ',');
            $sql_update_qty_move = "UPDATE adjustment_items SET qty_move = (case ".$case7." end), qty2_move = (case ".$case9." end) WHERE row_order in (".$where7.") AND kode_adjustment = '".$kode_adjustment."' ";
            $this->_module->update_reff_batch($sql_update_qty_move);
          }

          /*
           // update qty_data adjusment
          if(!empty($case3) AND !empty($where3)){
            $where3 = rtrim($where3, ',');
            $sql_update_qty_data2 = "UPDATE adjustment_items SET qty_data2 = (case ".$case3." end)  WHERE row_order in (".$where3.") AND kode_adjustment = '".$kode_adjustment."'";
            $this->_module->update_reff_batch($sql_update_qty_data2);
          }
          // update qty2 stock_quant
          if(!empty($case5) AND !empty($where5)){
            $where5 = rtrim($where5, ',');
            $sql_update_qty2_stock_quant = "UPDATE stock_quant SET qty2 = (case ".$case5." end)  WHERE quant_id in (".$where5.") ";
            $this->_module->update_reff_batch($sql_update_qty2_stock_quant);
          }
          */

          // update nama_user
          $this->m_adjustment->update_nama_user_adjustment($kode_adjustment,$nama_user['nama']);

          // update create_date
          $this->m_adjustment->update_create_date_adjustment($kode_adjustment,$tanggal);

          // update status adjustment = done
          $this->m_adjustment->update_status_adjustment($kode_adjustment,$status_done);

          $jenis_log   = "generate";
          $note_log    = "Generate Adjustment | ".$kode_adjustment." | Jumlah Adjustment ".$jml_adj;
          $this->_module->gen_history($sub_menu, $kode_adjustment, $jenis_log, $note_log, $username);

          $callback = array('status' => 'success','message' => 'Generate Data Berhasil !', 'icon' =>'fa fa-check', 'type' => 'success');
        
        }else{// $loop_adj = false

          if($qty_stok_adj_manual == true){
            $callback = array('status' => 'failed','message' => 'Qty Adjustment tidak Boleh 0 !', 'icon' =>'fa fa-warning', 'type' => 'danger');
          }else if($qty_data_adj_same == true){
            $callback = array('status' => 'failed','message' => 'Qty Stock dan Qty Adjustment tidak boleh sama, cek kembali Produk yang akan di Adjustment !', 'icon' =>'fa fa-warning', 'type' => 'danger');
          }else if($qty_adj_null == true){
            $callback = array('status' => 'failed','message' => 'Proses Adjustment tidak boleh  <b>  dikurang atau ditambah</b>, hanya boleh <b> mengadakan atau menghilangkan </b>  !', 'icon' =>'fa fa-warning', 'type' => 'danger');
          }else if($lokasi_produk_valid == false){
            $callback = array('status' => 'failed','message' => 'Produk atau Lot sudah tidak berada dilokasi <b>'.$kode_lokasi.'</b> !!<br> '.$list_produk, 'icon' =>'fa fa-warning', 'type' => 'danger');
          }else if($reserve_move_empty == false){
            $callback = array('status' => 'failed','message' => 'Produk atau Lot terdapat <b>Reserve Move / Terpesan</b> oleh dokumen lain !!<br> '.$list_produk2, 'icon' =>'fa fa-warning', 'type' => 'danger');
          }else if($reserve_pl_empty == false){
            $callback = array('status' => 'failed','message' => 'Produk atau Lot <b> sudah Masuk PL </b> !!<br> '.$list_produk3, 'icon' =>'fa fa-warning', 'type' => 'danger');
          }else if($quant == false){
            $callback = array('status' => 'failed','message' => 'Produk atau Lot di <b>Stock tidak ditemukan / sudah di hapus </b> !!<br> '.$list_quant, 'icon' =>'fa fa-warning', 'type' => 'danger');
          }else if($stock_lot_same == true){
            $callback = array('status' => 'failed','message' => 'Lot ini masih terpakai di Lokasi Stock / Transit Location </b> !!<br> '.$lokasi_same_lot, 'icon' =>'fa fa-warning', 'type' => 'danger');
          }else if(empty($item)){
            $callback = array('status' => 'failed','message' => 'Produk atau Lot yang akan di Adjustment masih  Kosong !!<br> '.$list_quant, 'icon' =>'fa fa-warning', 'type' => 'danger');
          }else{
            $callback = array('status' => 'failed','message' => 'Generate Data Gagal !', 'icon' =>'fa fa-warning', 'type' => 'danger');
          }

        }

        // unlock table
        $this->_module->unlock_tabel();


      }

    }

    echo json_encode($callback);

  }


  public function batal_adjustment()
  {
  	
  	if(empty($this->session->userdata('username'))){
      // session habis
      $callback = array('message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
    }else{

      $sub_menu  = $this->uri->segment(2);
      $username  = addslashes($this->session->userdata('username')); 

      $kode_adjustment = $this->input->post('kode_adjustment');
      $status_cancel   = 'cancel';

      //cek validasi apa status masih draft ?
      $cek_status = $this->m_adjustment->cek_status_adjustment($kode_adjustment,'')->row_array();

      if($cek_status['status'] ==  'done'){
        $callback = array('status' => 'failed', 'message'=>'Maaf, Data Tidak Bisa dibatalkan, Status Adjusment Sudah Done !', 'icon' => 'fa fa-warning', 'type'=>'danger');
      }else if($cek_status['status'] == 'cancel'){
        $callback = array('status' => 'failed', 'message'=>'Maaf, Data Tidak Bisa dibatalkan, Status Adjusment Sudah dibatalkan !', 'icon' => 'fa fa-warning', 'type'=>'danger');

      }else if($cek_status['status'] == 'draft'){

      	$this->m_adjustment->update_batal_adjustment($kode_adjustment,$status_cancel);

        $jenis_log   = "cancel";
        $note_log    = "Batal Adjustment | ".$kode_adjustment;
        $this->_module->gen_history($sub_menu, $kode_adjustment, $jenis_log, $note_log, $username);

        $callback = array('status' => 'success', 'message'=>'Data Adjustment berhasil dibatalkan', 'icon' => 'fa fa-check', 'type'=>'success');

      }else{
        $callback = array('status' => 'failed', 'message'=>'Maaf, Gagal Membatalkan Ajdustemnt', 'icon' => 'fa fa-warning', 'type'=>'danger');
      }

    }

    echo json_encode($callback);
  }



}