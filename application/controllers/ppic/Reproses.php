<?php defined('BASEPATH') or exit ('No Direct Script Acces Allowed');

/**
 * 
 */
class Reproses extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->is_loggedin();//cek apakah user sudah login
        $this->load->model("_module");
        $this->load->model("m_reproses");//load model
        $this->load->model("m_adjustment");
    }


    public function index()
    {
        $id_dept        = 'REPRO';
        $data['id_dept']= $id_dept;
        $this->load->view('ppic/v_reproses', $data);
    }


    function get_data()
    {
        $sub_menu  = $this->uri->segment(2);
        $kode = $this->_module->get_kode_sub_menu($sub_menu)->row_array();
        if(isset($_POST['start']) && isset($_POST['draw'])){
          $list = $this->m_reproses->get_datatables($kode['kode']);
          $data = array();
          $no = $_POST['start'];
          foreach ($list as $field) {
              $kode_encrypt = encrypt_url($field->kode_reproses);
              $no++;
              $row = array();
              $row[] = $no;
              $row[] = '<a href="'.base_url('ppic/reproses/edit/'.$kode_encrypt).'">'.$field->kode_reproses.'</a>';          
              $row[] = $field->tanggal;
              $row[] = $field->nama_jenis;
              $row[] = $field->note;
              $row[] = $field->nama_status;

              $data[] = $row;
          }
          
          $output = array(
              "draw" => $_POST['draw'],
              "recordsTotal" => $this->m_reproses->count_all($kode['kode']),
              "recordsFiltered" => $this->m_reproses->count_filtered($kode['kode']),
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
        $data['id_dept']   = 'REPRO';
        $data['list_type'] = $this->m_reproses->get_list_type();
        return $this->load->view('ppic/v_reproses_add', $data);
    }

    public function simpan()
    {
    
        if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
            // session habis
            $callback = array('message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
        }else{

            $sub_menu  = $this->uri->segment(2);
            $username  = $this->session->userdata('username'); 
            $nama_user = $this->_module->get_nama_user($username)->row_array();

            $kode_reproses      = addslashes($this->input->post('kode_reproses'));
            $tanggal            = date("Y-m-d H:i:s");
            $jenis              = addslashes($this->input->post('jenis'));
            $note               = addslashes($this->input->post('note'));
            $status             = addslashes($this->input->post('status'));

            //lock table
            $this->_module->lock_tabel('reproses WRITE, reproses_items WRITE, reproses_jenis WRITE, user WRITE, main_menu_sub WRITE, log_history WRITE');

            if(empty($jenis)){
              $callback = array('status' => 'failed', 'field' => 'jenis', 'message' => 'Jenis Harus Diisi !', 'icon' =>'fa fa-warning', 'type' => 'danger');       
            }else if(empty($note)){
              $callback = array('status' => 'failed', 'field' => 'note', 'message' => 'Notes Harus Diisi / Alasan membuat Reproses !', 'icon' =>'fa fa-warning', 'type' => 'danger');
            }else{

              $cek = $this->m_reproses->cek_reproses_by_kode($kode_reproses)->row_array();
              if(!empty($cek['kode_reproses']) AND $status == 'add'){
                $callback = array('status' => 'failed', 'field' => 'note', 'message' => 'Kode Reproses Ini Sudah Pernah Diinput !', 'icon' =>'fa fa-warning', 'type' => 'danger');
              }else if(!empty($cek['kode_reproses'])){ // update
                $cek_status = $this->m_reproses->cek_status_reproses($kode_reproses,'')->row_array();

                if($cek_status['status'] == 'done'){
                    $callback = array('status' => 'failed','message' => 'Maaf, Data Tidak Bisa Disimpan, Status Reproses Sudah Done !', 'field' => 'note', 'icon' =>'fa fa-check', 'type' => 'danger');
                }else if($cek_status['status'] == 'cancel'){
                    $callback = array('status' => 'failed','message' => 'Maaf, Data Tidak Bisa Disimpan, Status Reproses Sudah Batal !', 'field' => 'note', 'icon' =>'fa fa-check', 'type' => 'danger');
                }else{

                    //update/edit 
                    $this->m_reproses->update_reproses($cek['kode_reproses'],$note);
                    $jenis_log   = "edit";
                    $note_log    = $cek['kode_reproses']."|".$note;
                    $this->_module->gen_history($sub_menu, $kode_reproses, $jenis_log, $note_log, $username);
                    $callback = array('status' => 'success', 'message' => 'Data Berhasil Disimpan !','icon' =>'fa fa-check', 'type' => 'success');

                }

              }else{ // insert

                $kode   = $this->m_reproses->get_kode_reproses();      
                $kode   = substr("0000" . $kode,-4);                  
                $kode   = "RPR/".date("y") . '/' .  date("m") . '/' . $kode;

                // get jenis reproses
                $jenis_reproses = $this->m_reproses->get_jenis_reproses_by_id($jenis);

                $this->m_reproses->simpan_reproses($kode,$tanggal,$jenis,$note,'draft',$nama_user['nama']);
                $kode_encr   = encrypt_url($kode);
                $jenis_log   = "create";
                $note_log    = $kode." | ".$jenis_reproses." | ".$note;
                $this->_module->gen_history($sub_menu, $kode, $jenis_log, $note_log, $username);
                $callback = array('status' => 'success', 'message' => 'Data Berhasil Disimpan !', 'isi' => $kode_encr, 'icon' =>'fa fa-check', 'type' => 'success');

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
          $kode_reproses_decrypt = decrypt_url($id);
          $data['id_dept'] ='REPRO';
          $data['reproses'] = $this->m_reproses->get_reproses_by_kode($kode_reproses_decrypt);
          $data['details'] = $this->m_reproses->get_reproses_detail_by_code($kode_reproses_decrypt);
          if(empty($data["reproses"])){
            show_404();
          }else{
            return $this->load->view('ppic/v_reproses_edit',$data);
          }
    }
    

    public function import_produk()
    {
        $kode_reproses          = $this->input->post('kode_reproses');
        $data['kode_reproses'] = $kode_reproses;
        return $this->load->view('modal/v_reproses_import_modal',$data);
    }


    public function list_import_produk()
    {
        if(isset($_POST['start']) && isset($_POST['draw'])){
          $list = $this->m_reproses->get_datatables2();
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
              $row[] = $field->lokasi;
              $row[] = $field->reff_note;
              $row[] = $field->reserve_move;
              $row[] = $field->quant_id;
              $data[] = $row;
          }
          $output = array(
              "draw" => $_POST['draw'],
              "recordsTotal" => $this->m_reproses->count_all2(),
              "recordsFiltered" => $this->m_reproses->count_filtered2(),
              "data" => $data,
          );
          //output dalam format JSON
          echo json_encode($output);
        }else{
          die();
        }
    }


    public function save_details_import_produk_reproses_modal()
    { 
        $sub_menu  = $this->uri->segment(2);
        $username  = $this->session->userdata('username'); 
    
        if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
            // session habis
            $callback = array('status' => 'failed','message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
        }else{
            $arr_data         = $this->input->post('arr_data');
            $kode_reproses    = $this->input->post('kode_reproses');
            $countchek        = $this->input->post('countchek');
            $sql_reproses_items_batch = "";
            
            //lock tabel
            $this->_module->lock_tabel('reproses WRITE, reproses_items WRITE, stock_quant WRITE');
            
            //cek status reproses = done
            $cek1  = $this->m_reproses->cek_status_reproses($kode_reproses,'done')->row_array();
            //cek status reproses = cancel
            $cek2  = $this->m_reproses->cek_status_reproses($kode_reproses,'cancel')->row_array();
      
            if(!empty($cek1['status'])){
              $callback = array('status' => 'failed', 'message'=>'Maaf, Data Tidak Bisa Disimpan, Status Reproses Sudah Done !', 'icon' => 'fa fa-warning', 'type'=>'danger');
            }else if(!empty($cek2['status'])){
              $callback = array('status' => 'failed', 'message'=>'Maaf, Data Tidak Bisa Disimpan, Status Reproses Batal !', 'icon' => 'fa fa-warning', 'type'=>'danger');
            }else{
              //get row order reproses_items
              $row_order  = $this->m_reproses->get_row_order_reproses_items_by_kode($kode_reproses);
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
                $lokasi      = $row_data['lokasi'];
          
                //cek apakah quant_id sudah ada di dalam adjustment_items
                //jika sudah ada maka tidak usah ditambahkan lagi
                $cek_quant   = $this->m_reproses->cek_quant_reproses_items($kode_reproses,$quant_id)->row_array();
                if(empty($cek_quant['kode_reproses'])){
      
                  $item_add = true;
                  //insert ke adjustment_items
                  $sql_reproses_items_batch .= "('".$kode_reproses."', '".$quant_id."', '".addslashes($kode_produk)."','".addslashes($lot)."', '".addslashes($uom)."','".$qty."','".addslashes($uom2)."','".$qty2."', '".$lokasi."', '".$row_order."'), ";
      
                  $list_product .= "(".$no.") ".$kode_produk." ".$nama_produk." ".$lot." ".$qty." ".$uom." ".$qty2." ".$uom2." ".$lokasi." <br>";
                  $no++;
                  $row_order++;            
                }else{
                  $lot_sama .= $lot.', ';
      
                }
              }
          
              if(!empty($sql_reproses_items_batch)){
                $sql_reproses_items_batch = rtrim($sql_reproses_items_batch, ', ');
                $this->m_reproses->simpan_reproses_items_batch($sql_reproses_items_batch);
              }
      
              //unlock table
              $this->_module->unlock_tabel();  
      
              if($item_add == true){
                if(!empty($lot_sama)){
                  $lot_sama = rtrim($lot_sama, ', ');
                  $callback = array('status'=>'success',  'message' => 'Reproses Detail Berhasil Ditambahkan !',  'icon' =>'fa fa-check', 'type' => 'success', 'msg2'=>'Yes', 'message2'=> 'Lot ( '.$lot_sama.' )</br> Sudah Pernah Diinput !'); 
                }else{
                  $callback = array('status'=>'success',  'message' => 'Reproses Detail Berhasil Ditambahkan !',  'icon' =>'fa fa-check', 'type' => 'success'); 
                }
                  $jenis_log   = "edit";
                  $note_log    = "Tambah Data Detail ".$kode_reproses." <br> ".$list_product;
                  $this->_module->gen_history($sub_menu, $kode_reproses, $jenis_log, $note_log, $username);
                  
              }else if($item_add == false){
                $lot_sama = rtrim($lot_sama, ', ');
                $callback = array('status'=>'failed',  'message' => 'Lot ( '.$lot_sama.' )</br> Sudah Pernah Diinput !',  'icon' =>'fa fa-check', 'type' => 'success'); 
      
              }            
    
          }           
        
        }
        echo json_encode($callback);
    }

    public function hapus_reproses_items()
    {
        if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
          // session habis
          $callback = array('message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
        }else{
          $sub_menu  = $this->uri->segment(2);
          $username  = addslashes($this->session->userdata('username')); 

          $kode_reproses = addslashes($this->input->post('kode_reproses'));
          $row           = $this->input->post('row_order');
     
          
          $cek_status = $this->m_reproses->cek_status_reproses($kode_reproses,'')->row_array();
          
          if(empty($kode_reproses) && empty($row) ){
            $callback = array('status' => 'success','message' => 'Data Gagal Dihapus !', 'icon' =>'fa fa-warning', 'type' => 'danger');
          }else if($cek_status['status'] == 'done'){
            $callback = array('status' => 'failed','message' => 'Maaf, Data tidak bisa Dihapus, Status Adjustment Sudah Done !', 'icon' =>'fa fa-check', 'type' => 'danger');
          }else if($cek_status['status'] == 'cancel'){
            $callback = array('status' => 'failed','message' => 'Maaf, Data tidak bisa Dihapus, Status Adjustment Sudah Batal !', 'icon' =>'fa fa-check', 'type' => 'danger');
          }else{
            
            // lock table
            $this->_module->lock_tabel('reproses WRITE, reproses_items WRITE, reproses_items as rei WRITE, mst_produk as mp WRITE');

            $get = $this->m_reproses->get_reproses_items_by_row($kode_reproses,$row)->row_array();
            if(!empty($get)){
              $this->m_reproses->delete_reproses_items($kode_reproses,$row);
  
              // unlock table
              $this->_module->unlock_tabel();
              
              $callback = array('status' => 'success','message' => 'Data Berhasil Dihapus !', 'icon' =>'fa fa-check', 'type' => 'success');
              $jenis_log   = "cancel";        
              $note_log    = "Hapus data Details Baris Ke ".$row." <br> ".$kode_reproses."  ".$get['quant_id']." ".$get['kode_produk']."  ".$get['nama_produk']."  ".$get['lot']."  ".$get['qty']." ".$get['uom']." ".$get['qty2']." ".$get['uom2'];
              $this->_module->gen_history($sub_menu, $kode_reproses, $jenis_log, addslashes($note_log), $username);

            }else{
              $callback = array('status' => 'failed','message' => 'Maaf, Data yang akan dihapus, tidak ditemukan !', 'icon' =>'fa fa-check', 'type' => 'danger');
            }

          }

          echo json_encode($callback);
        }
    }


    public function batal_reproses()
    {
        if(empty($this->session->userdata('username'))){
          // session habis
          $callback = array('message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
        }else{
    
          $sub_menu  = $this->uri->segment(2);
          $username  = addslashes($this->session->userdata('username')); 
    
          $kode_reproses = addslashes($this->input->post('kode_reproses'));
          $status_cancel = 'cancel';
    
          //cek validasi apa status masih draft ?
          $cek_status = $this->m_reproses->cek_status_reproses($kode_reproses,'')->row_array();
    
          if($cek_status['status'] ==  'done'){
            $callback = array('status' => 'failed', 'message'=>'Maaf, Data Tidak Bisa dibatalkan, Status Reproses Sudah Done !', 'icon' => 'fa fa-warning', 'type'=>'danger');
          }else if($cek_status['status'] == 'cancel'){
            $callback = array('status' => 'failed', 'message'=>'Maaf, Data Tidak Bisa dibatalkan, Status Reproses Sudah dibatalkan !', 'icon' => 'fa fa-warning', 'type'=>'danger');
    
          }else if($cek_status['status'] == 'draft'){

            // cek items reproses
            $items = $this->m_reproses->get_reproses_detail_by_code($kode_reproses);

            if(!empty($items)){
              $callback = array('status' => 'failed', 'message'=>'Maaf, Data Tidak Bisa dibatalkan, Silahkan hapus detail terlebih dahulu !', 'icon' => 'fa fa-warning', 'type'=>'danger');
    
            }else{
              $this->m_reproses->update_batal_reproses($kode_reproses,$status_cancel);
              $jenis_log   = "cancel";
              $note_log    = "Batal Reproses | ".$kode_reproses;
              $this->_module->gen_history($sub_menu, $kode_reproses, $jenis_log, $note_log, $username);
      
              $callback = array('status' => 'success', 'message'=>'Data Reproses berhasil dibatalkan', 'icon' => 'fa fa-check', 'type'=>'success');
            }
    
          }else{
            $callback = array('status' => 'failed', 'message'=>'Maaf, Gagal Membatalkan Reproses', 'icon' => 'fa fa-warning', 'type'=>'danger');
          }
    
        }
    
        echo json_encode($callback);
    }


    
  public function generate_detail_reproses_items()
  {

      if(empty($this->session->userdata('username'))){
        // session habis
        $callback = array('message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
      }else{

        $sub_menu  = $this->uri->segment(2);
        $username  = addslashes($this->session->userdata('username')); 
        $nama_user = $this->_module->get_nama_user($username)->row_array();

        $kode_reproses = addslashes($this->input->post('kode_reproses'));
        $status_done   = "done";
        $tanggal       = date("Y-m-d H:i:s");

        //cek validasi apa status masih draft ?
        $cek_status = $this->m_reproses->cek_status_reproses($kode_reproses,'')->row_array();

        // cek jenis reproses (Reproses,oper Warna dan Ex Setting)
        $head  = $this->m_reproses->get_reproses_by_kode($kode_reproses);
    
        if($cek_status['status'] ==  'done'){
          $callback = array('status' => 'failed', 'message'=>'Maaf, Data Tidak Bisa Generate, Status Reproses Sudah Done !', 'icon' => 'fa fa-warning', 'type'=>'danger');
        }else if($cek_status['status'] == 'cancel'){
          $callback = array('status' => 'failed', 'message'=>'Maaf, Data Tidak Bisa Generate, Status Reproses Sudah dibatalkan !', 'icon' => 'fa fa-warning', 'type'=>'danger');
        }else if(empty($head->id_jenis)){
          $callback = array('status' => 'failed', 'message'=>'Maaf, Data Tidak Bisa Generate, Jenis Kosong !', 'icon' => 'fa fa-warning', 'type'=>'danger');
        }else{

          // lock table
          $this->_module->lock_tabel('stock_move WRITE, stock_move_items WRITE, stock_move_produk WRITE, stock_quant WRITE, reproses WRITE, reproses_items WRITE, mst_produk WRITE, user WRITE, log_history WRITE, main_menu_sub WRITE, reproses_items as rei WRITE, mst_produk as mp WRITE, adjustment WRITE, adjustment_items WRITE , reproses_jenis WRITE, departemen as d WRITE');


          // get move_id
          $last_move   = $this->_module->get_kode_stock_move();
          $move_id     = "SM".$last_move; //Set kode stock_move
          // get quant_id
          $start       = $this->_module->get_last_quant_id();
          // get kode adj
          $get_kode_adjustment   = $this->_module->get_kode_adj();      

          $inisial_reproses  = true;
          
          if($head->inisial == ''){// reproses
            $inisial_reproses = false;
          }

          $jenis_reproses = $this->m_reproses->get_jenis_reproses_by_id($head->id_jenis);

          $item_empty           = false;
          $item_empty_produk    = "";
          $lokasi_produk_valid  = true;
          $reserve_move_empty   = true;
          $list_produk          = '';
          $list_produk2         = '';
          $qty_not_same         = false;
          $qty2_not_same        = false;
          $produk_empty         = false;
          $list_produk3         = '';
          $list_produk4         = '';
          $nama_produk_empty    = '';

          $sql_stock_quant_batch = "";
          $sql_stock_move_batch  = "";
          $sql_stock_move_produk_batch = "";
          $sql_stock_move_items_batch  = "";
          $sql_adjustment       = "";
          $sql_adjustment_items = "";
          $case   = "";
          $where  = ""; 
          $case2  = "";
          $case3  = "";
          $where2  = "";  
          $row_order_adj_in = 1;
          $sql_log_history_batch  = "";

          // ADJ IN
          $note_adj_in  = 'ADJ | Mengadakan. Dibuat dari Fitur Reproses. No.'.$kode_reproses.' Jenis '.$jenis_reproses;

          // get dept id GRG
          $dept_grg        = $this->_module->get_nama_dept_by_kode('GRG')->row_array();
          $nama_dept_grg   = $dept_grg['nama'];
          $stock_location_greige   = $dept_grg['stock_location'];

          $kode_adjustment_in   = substr("0000" . $get_kode_adjustment,-4);     
          $kode_adjustment_in   = "ADJ/".date("y") . '/' .  date("m") . '/' . $kode_adjustment_in;

          // insert into adj 
          $sql_adjustment .= "('".$kode_adjustment_in."', '".$tanggal."','".$nama_dept_grg."','".$stock_location_greige."','".$note_adj_in."','".$status_done."','".$nama_user['nama']."'), ";

          //create log history adjustment in 
          $note_log_adj_in = $kode_adjustment_in." ini dibuat dari Fitur Reproses";
          $date_log = date('Y-m-d H:i:s');
          $sql_log_history_batch .= "('".$date_log."','mms72','".$kode_adjustment_in."','create','".addslashes($note_log_adj_in)."','".$nama_user['nama']."'), ";

          $get_kode_adjustment++;
          
          $jumlah_adj_in = 0;
          
          if($inisial_reproses == true){
            $items = $this->m_reproses->get_reproses_detail_by_code($kode_reproses);
            foreach($items as $row){

              $lokasi_asal  = $row->lokasi_asal;

              if($row->quant_id > 0){
                
                $get_dept     = explode('-',$row->nama_produk);
                $dept_nm      = $get_dept[0];
                
                if($dept_nm == 'TRC'){
                  $nama_dept = "Tricot";
                }else if($dept_nm == "J"){
                  $nama_dept = "Inspecting";
                }else{
                  $nama_dept = "Kosong";
                }

                $prod_ori     = explode('"',$row->nama_produk);
                $product_fullname = $prod_ori[0].'" ('.$nama_dept.')';

                $cek_prod = $this->_module->cek_nama_product(addslashes($product_fullname))->row_array();//get kode_produk

                if(!empty($cek_prod['nama_produk'])){

                  $sq = $this->m_adjustment->get_stock_quant_by_quant_id($row->quant_id)->row_array();

                  if(!empty($sq)){

                    $reserve_move = $sq['reserve_move'];

                    if($row->lokasi_asal != $sq['lokasi'] ){
                      $lokasi_produk_valid = false;
                      $list_produk  .= $row->kode_produk." ".$row->nama_produk." ".$row->lot." (".$lokasi_asal.") <br>";
                    }else if($reserve_move != ''){
                      $reserve_move_empty = false;
                      $list_produk2 .= $row->kode_produk." ".$row->nama_produk." ".$row->lot." (".$sq['reserve_move'].") <br>";
                    }else if($sq['qty'] != $row->qty){
                      $qty_not_same = true;
                      $list_produk3 .= $row->kode_produk." ".$row->nama_produk." ".$row->lot." <br>";
                    }else if($sq['qty2'] != $row->qty2){
                      $qty2_not_same = true;
                      $list_produk4 .= $row->kode_produk." ".$row->nama_produk." ".$row->lot." <br>";
                    }else{

                      $note_adj_out = 'ADJ | Menghilangkan. Dibuat dari Fitur Reproses. No.'.$kode_reproses.' Jenis '.$jenis_reproses;
                    
                      $datas     = explode("/",$lokasi_asal);
                      $loop_ex   = 0;
                      $id_dept   = '';
                      $lokasi_adj = '';
                      foreach($datas as $data){
                          if($loop_ex == 0){
                            $id_dept = $data;
                          }
                          $loop_ex++;
                      }
                      
                      $nm_dept         = $this->_module->get_nama_dept_by_kode($id_dept)->row_array();
                      $nama_departemen = $nm_dept['nama'];
                      $lokasi_adj      = $nm_dept['adjustment_location'];

                      $kode_adjustment   = substr("0000" . $get_kode_adjustment,-4);                  
                      $kode_adjustment   = "ADJ/".date("y") . '/' .  date("m") . '/' . $kode_adjustment;

                      $lot_new           = $row->lot."".$head->inisial;

                      // insert into adj 
                      $sql_adjustment .= "('".$kode_adjustment."', '".$tanggal."','".$nama_departemen."','".$lokasi_asal."','".$note_adj_out."','".$status_done."','".$nama_user['nama']."'), ";

                      // loop adj
                      $row_order_adj= 1;

                      $qty1_move = 0 - $row->qty;
                      $qty2_move = 0 - $row->qty2;
                      
                      // ADJ OUT
                      // insert to adj items
                      $sql_adjustment_items .= "('".$kode_adjustment."','".$row->quant_id."','".$row->kode_produk."','".$row->lot."','".$row->uom."','".$row->qty."',0,'".$row->uom2."','".$row->qty2."',0,'".$move_id."','".$qty1_move."','".$qty2_move."',$row_order_adj), ";

                      // log history ADJ OUT
                      $note_log_adj_out = $kode_adjustment." ini dibuat dari Fitur Reproses";
                      $sql_log_history_batch .= "('".$date_log."','mms72','".$kode_adjustment."','create','".addslashes($note_log_adj_out)."','".$nama_user['nama']."'), ";

                      // log history ADJ OUT
                      $note_log_adj_out_2 = "Generate Adjustment ini di generate otomatis dari Fitur Reproses | Jumlah Adjustment 1 ";
                      $sql_log_history_batch .= "('".$date_log."','mms72','".$kode_adjustment."','generate','".addslashes($note_log_adj_out_2)."','".$nama_user['nama']."'), ";


                      // update lokasi to adj 
                      $case .= " when quant_id = '".$row->quant_id."' then '".$lokasi_adj."'";
                      $where.= "'".$row->quant_id."',";

                      $method         = $id_dept.'|ADJ';
                      $lokasi_dari    = $lokasi_asal;
                      $lokasi_tujuan  = $lokasi_adj;
                      $origin_out     = $kode_adjustment.'|1';

                      // insert stock_move
                      $sql_stock_move_batch .= "('".$move_id."','".$tanggal."','".$origin_out."','".$method."','".$lokasi_dari."','".$lokasi_tujuan."','".$status_done."','1',''), ";

                      // insert stock_move_produk
                      $sql_stock_move_produk_batch .= "('".$move_id."','".addslashes($row->kode_produk)."','".addslashes($row->nama_produk)."','".$row->qty."','".$row->uom."','".$status_done."','1',''), ";

                      
                      // insert stock_move_items
                      $sql_stock_move_items_batch .= "('".$move_id."', '".$row->quant_id."','".addslashes($row->kode_produk)."', '".addslashes($row->nama_produk)."','".addslashes(trim($row->lot))."','".$row->qty."','".$row->uom."','".$row->qty2."','".$row->uom2."','".$status_done."','1','','".$tanggal."','".addslashes($sq['lokasi_fisik'])."','".addslashes($sq['lebar_greige'])."','".addslashes($sq['uom_lebar_greige'])."','".addslashes($sq['lebar_jadi'])."','".addslashes($sq['uom_lebar_jadi'])."'), ";

                      $last_move = $last_move + 1;
                      $move_id   = "SM".$last_move;
                    

                      // ADJ IN
                      $sql_adjustment_items .= "('".$kode_adjustment_in."','".$start."','".$cek_prod['kode_produk']."','".$lot_new."','".$row->uom."',0,'".$row->qty."','".$row->uom2."',0,'".$row->qty2."','".$move_id."','".$row->qty."','".$row->qty2."',$row_order_adj_in), ";
                      $jumlah_adj_in++;

                      // insert stock_quant
                      $sql_stock_quant_batch .= "('".$start."','".$tanggal."','".addslashes($cek_prod['kode_produk'])."','".addslashes($cek_prod['nama_produk'])."','".addslashes(trim($lot_new))."','".addslashes($sq['nama_grade'])."','".$row->qty."','".$row->uom."','".$row->qty2."','".$row->uom2."','".$stock_location_greige."','".addslashes($sq['reff_note'])."','','','".$tanggal."','".addslashes($sq['lebar_greige'])."','".addslashes($sq['uom_lebar_greige'])."','".addslashes($sq['lebar_jadi'])."','".addslashes($sq['uom_lebar_jadi'])."','".addslashes($sq['sales_order'])."','".addslashes($sq['sales_group'])."'), ";

                      $method         = 'GRG|ADJ';
                      $lokasi_dari    = $dept_grg['adjustment_location'];
                      $lokasi_tujuan  = $stock_location_greige;
                      $origin_in      = $kode_adjustment_in.'|'.$row_order_adj_in;

                      // insert stock_move
                      $sql_stock_move_batch .= "('".$move_id."','".$tanggal."','".$origin_in."','".$method."','".$lokasi_dari."','".$lokasi_tujuan."','".$status_done."','1',''), ";

                      // insert stock_move_produk
                      $sql_stock_move_produk_batch .= "('".$move_id."','".addslashes($cek_prod['kode_produk'])."','".addslashes($cek_prod['nama_produk'])."','".$row->qty."','".$row->uom."','".$status_done."','1',''), ";
                        
                      // insert stock_move_items
                      $sql_stock_move_items_batch .= "('".$move_id."', '".$start."','".addslashes($cek_prod['kode_produk'])."', '".addslashes($cek_prod['nama_produk'])."','".addslashes(trim($lot_new))."','".$row->qty."','".($row->uom)."','".$row->qty2."','".$row->uom2."','".$status_done."','1','','".$tanggal."','','".addslashes($sq['lebar_greige'])."','".addslashes($sq['uom_lebar_greige'])."','".addslashes($sq['lebar_jadi'])."','".addslashes($sq['uom_lebar_jadi'])."'), ";

                      // update lot new
                      $case2 .= " when quant_id = '".$row->quant_id."' then '".$start."'";
                      $case3 .= " when quant_id = '".$row->quant_id."' then '".$lot_new."'";
                      $where2.= "'".$row->quant_id."',";

                      $last_move = $last_move + 1;
                      $move_id   = "SM".$last_move;
                      // $row_order_adj++;
                      $row_order_adj_in++;
                      $start++;

                      $get_kode_adjustment++;

                    }

                  }else{
                    $item_empty = true;
                    $item_empty_produk  .= $row->kode_produk." ".$row->nama_produk." ".$row->lot." <br>";
                  }
                }else{
                  $produk_empty        = true;
                  $nama_produk_empty  .= $product_fullname.' <br>';
                }

              }else{
                $item_empty = true;
                $item_empty_produk  .= $row->kode_produk." ".$row->nama_produk." ".$row->lot." <br>";
              }

            }
          }

          $note_log_adj_in_2 = "Generate Adjustment ini di generate otomatis dari Fitur Reproses | Jumlah Adjustment ".$jumlah_adj_in;
          $sql_log_history_batch .= "('".$date_log."','mms72','".$kode_adjustment_in."','generate','".addslashes($note_log_adj_in_2)."','".$nama_user['nama']."'), ";

          if($item_empty == false && $lokasi_produk_valid == true && $reserve_move_empty == true && $qty_not_same == false && $qty2_not_same == false && $produk_empty == false && $inisial_reproses == true){

            // simpan adjustment
            if(!empty($sql_adjustment)){
              $sql_adjustment = rtrim($sql_adjustment, ', ');
              $this->m_reproses->simpan_adjustment_batch($sql_adjustment);

              if(!empty($sql_adjustment_items)){
                $sql_adjustment_items = rtrim($sql_adjustment_items, ', ');
                $this->m_reproses->simpan_adjustment_items_batch($sql_adjustment_items);
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
            if(!empty($sql_stock_quant_batch)){
                $sql_stock_quant_batch = rtrim($sql_stock_quant_batch, ', ');
                $this->_module->simpan_stock_quant_batch($sql_stock_quant_batch);
                
            }

             // update lokasi stock_quant
            if(!empty($case) AND !empty($where)){
              $where = rtrim($where, ',');
              $sql_update_lokasi_stock_quant = "UPDATE stock_quant SET lokasi = (case ".$case." end), move_date = '".$tanggal."'  WHERE quant_id in (".$where.") ";
              $this->_module->update_reff_batch($sql_update_lokasi_stock_quant);
            }


            // update reproses items lot new, quant_id_new
            if(!empty($case2) AND !empty($where2)){
              $where2 = rtrim($where2, ',');
              $sql_update_reproses_items = "UPDATE reproses_items SET quant_id_new = (case ".$case2." end), lot_new = (case ".$case3." end)  WHERE quant_id in (".$where2.") AND kode_reproses = '".$kode_reproses."' ";
              $this->_module->update_reff_batch($sql_update_reproses_items);
            }
          
            // update reproses
            $sql_update_status_reproses = "UPDATE reproses SET status = '".$status_done."', tanggal = '".$tanggal."'  WHERE kode_reproses = '".$kode_reproses."' ";
            $this->_module->update_reff_batch($sql_update_status_reproses);

            if(!empty($sql_log_history_batch)){
              $sql_log_history_batch = rtrim($sql_log_history_batch, ', ');
              $this->_module->simpan_log_history_batch($sql_log_history_batch);
            }


            $jenis_log   = "generate";
            $note_log    = "Generate Reproses | ".$kode_reproses;
            $this->_module->gen_history($sub_menu, $kode_reproses, $jenis_log, $note_log, $username);
            $callback = array('status'=>'success',  'message' => 'Reproses Berhasil !',  'icon' =>'fa fa-check', 'type' => 'success'); 

          }else{ 

            if($produk_empty == true){
              $callback = array('status' => 'failed','message' => 'Maaf, Produk tidak ditemukan ! <br>'.$nama_produk_empty, 'icon' =>'fa fa-warning', 'type' => 'danger');

            }else if($item_empty == true){
              $item_empty_produk = rtrim($item_empty_produk, ', ');
              $callback = array('status'=>'failed',  'message' => 'Produk/Lot di Stock tidak ditemukan ! <br> '.$item_empty_produk,  'icon' =>'fa fa-check', 'type' => 'danger');

            }else if($lokasi_produk_valid == false){
              $callback = array('status' => 'failed','message' => 'Produk atau Lot sudah tidak berada dilokasi !! <br> '.$list_produk, 'icon' =>'fa fa-warning', 'type' => 'danger');

            }else if($reserve_move_empty == false){
              $callback = array('status' => 'failed','message' => 'Produk atau Lot terdapat <b>Reserve Move / Terpesan</b> oleh dokumen lain !!<br> '.$list_produk2, 'icon' =>'fa fa-warning', 'type' => 'danger');

            }else if($qty_not_same == true){
              $callback = array('status' => 'failed','message' => 'Qty 1 yang akan di Reproses tidak sama dengan Qty 1 yang  ada di Stock !! <br> '.$list_produk3, 'icon' =>'fa fa-warning', 'type' => 'danger');

            }else if($qty2_not_same == true){
              $callback = array('status' => 'failed','message' => 'Qty 2 yang akan di Reproses tidak sama dengan Qty 2 yang  ada di Stock !! <br> '.$list_produk4, 'icon' =>'fa fa-warning', 'type' => 'danger');

            }else if($inisial_reproses == false){
              $callback = array('status' => 'failed','message' => 'Jenis Reproses tidak ditemukan / tidak valid !', 'icon' =>'fa fa-warning', 'type' => 'danger');

            }else{
              $callback = array('status'=>'failed',  'message' => 'Reproses Gagal di Generate !',  'icon' =>'fa fa-check', 'type' => 'danger');
            }

          }

          //unlock table
          $this->_module->unlock_tabel();  

        }

      }

      echo json_encode($callback);
  }
    
}