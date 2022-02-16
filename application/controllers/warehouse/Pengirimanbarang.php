<?php 
defined('BASEPATH') OR exit('No Direct Script Acces Allowed');
/**
 * 
 */
class Pengirimanbarang extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->is_loggedin();//cek apakah user sudah login
		$this->load->model("_module");//load modul global
		$this->load->model("m_pengirimanBarang");///load model pengiriman barang

	}

	public function index()
	{
		$kode_sub = 'mm_warehouse';
		$username	= $this->session->userdata('username');
		$row 		  = $this->_module->sub_menu_default($kode_sub,$username)->row_array();
		redirect($row['link_menu']);
	}

  public function Receiving()
  {
    $data['id_dept']='RCV';
    $this->load->view('warehouse/v_pengiriman_barang',$data);
  }

	public function Gudangbenang()
  {
    $data['id_dept']='GDB';
    $this->load->view('warehouse/v_pengiriman_barang',$data);
  }

  public function Twisting()
  {
    $data['id_dept']='TWS';
    $this->load->view('warehouse/v_pengiriman_barang',$data);
  }

  public function Warpingdasar()
  {
    $data['id_dept']='WRD';
    $this->load->view('warehouse/v_pengiriman_barang',$data);
  }

  public function Warpingpanjang()
  {
    $data['id_dept']='WRP';
    $this->load->view('warehouse/v_pengiriman_barang',$data);
  }

	public function Tricot()
	{
		$data['id_dept']='TRI';
		$this->load->view('warehouse/v_pengiriman_barang',$data);
	}


  public function Jacquard()
  {
    $data['id_dept']='JAC';
    $this->load->view('warehouse/v_pengiriman_barang',$data);
  }

  public function Raschel()
  {
    $data['id_dept']='RSC';
    $this->load->view('warehouse/v_pengiriman_barang',$data);
  }

  public function Cuttingshearing()
  {
    $data['id_dept']='CS';
    $this->load->view('warehouse/v_pengiriman_barang',$data);
  }

	public function Inspecting()
	{
		$data['id_dept']='INS1';
		$this->load->view('warehouse/v_pengiriman_barang',$data);
	}

	public function Greige()
	{
		$data['id_dept']='GRG';
		$this->load->view('warehouse/v_pengiriman_barang',$data);
	}

	public function Dyeing()
	{
		$data['id_dept']='DYE';
		$this->load->view('warehouse/v_pengiriman_barang',$data);
	}

	public function Finishing()
	{
		$data['id_dept']='FIN';
		$this->load->view('warehouse/v_pengiriman_barang',$data);
	}

	public function Brushing()
	{
		$data['id_dept']='BRS';
		$this->load->view('warehouse/v_pengiriman_barang',$data);
	}

	public function Inspecting2()
	{
		$data['id_dept']='INS2';
		$this->load->view('warehouse/v_pengiriman_barang',$data);
	}

  public function Gudangobat()
  {
    $data['id_dept']='GOB';
    $this->load->view('warehouse/v_pengiriman_barang',$data);
  }

  public function add()
  {
    $data['id_dept']   = $this->input->get('departemen');
    $data['warehouse'] = $this->_module->get_list_departement();
    return $this->load->view('warehouse/v_pengiriman_barang_add',$data);
  }

  function limit_words1($string, $word_limit){

    $words = explode(" ",$string);
    if(count($words) > $word_limit){
      $new_word = implode(" ",array_splice($words,0,$word_limit));
      return  $new_word.' [...]';
    }

  }

  function limit_words($string, $awal_start, $awal_length, $akhir_start, $akhir_length){
        
      $words = explode(" ",$string);
      $word_awal  = implode(" ",array_splice($words,$awal_start,$awal_length));
      $word_akhir = implode(" ",array_splice($words,$akhir_start,$akhir_length));
      return  $word_awal.' [...] '.$word_akhir;

  }

	public function get_data()
	{

		    $sub_menu = $this->uri->segment(2);
        $id_dept  = $this->input->post('id_dept');
        $kode     = $this->_module->get_kode_sub_menu_deptid($sub_menu,$id_dept)->row_array();

        $list = $this->m_pengirimanBarang->get_datatables($id_dept,$kode['kode']);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $field) {
        	//$kode_encrypt = $this->encryption->encrypt($field->kode);
        	$kode_encrypt = encrypt_url($field->kode);
            if(str_word_count($field->reff_note)> 55){
                $reff_note = $this->limit_words($field->reff_note, 0, 3, -50, 50);
            }else{
                $reff_note = $field->reff_note;
            }

          $no++;
          $row = array();
          $row[] = $no;
          $row[] = '<a href="'.base_url('warehouse/pengirimanbarang/edit/'.$kode_encrypt).'">'.$field->kode.'</a>';
          $row[] = $field->tanggal;
          $row[] = $field->tanggal_transaksi;
          $row[] = $field->tanggal_jt;
          $row[] = $field->lokasi_tujuan;
          $row[] = $field->reff_picking;
          $row[] = $reff_note;
          $row[] = $field->nama_status;

          $data[] = $row;
      }

      $output = array(
          "draw" => $_POST['draw'],
          "recordsTotal" => $this->m_pengirimanBarang->count_all($id_dept,$kode['kode']),
          "recordsFiltered" => $this->m_pengirimanBarang->count_filtered($id_dept,$kode['kode']),
          "data" => $data,
      );
      //output dalam format JSON
      echo json_encode($output);
  }

  public function simpan()
  {

      $sub_menu  = $this->uri->segment(2);
      $username  = addslashes($this->session->userdata('username')); 

      if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
          // session habis
          $callback = array('message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
      }else{

          $kode       = $this->input->post('kode');
          //$tgl_transaksi  = $this->input->post('tgl_transaksi');
          $reff_note   = addslashes($this->input->post('reff_note'));
          $move_id     = $this->input->post('move_id');
          $deptid      = $this->input->post('deptid');
          $type        = $this->input->post('type');// type pengirimanya dibuat manual/otomatis

          if(empty($kode) AND $type == 1){// jika kode nya kosong

            $lok_tujuan  = $this->input->post('lokasi_tujuan');
            $tgl         = date('Y-m-d H:i:s');
            $tgl_jt      = $this->input->post('tgl_jt');

            if(empty($reff_note)){
               $callback = array('status' => 'failed', 'message' => 'Reff Note Harus Diisi !', 'icon' =>'fa fa-warning', 'type' => 'danger');
            }else if(empty($lok_tujuan)){
              $callback = array('status' => 'failed', 'message' => 'Lokasi Tujuan Harus Diisi !', 'icon' =>'fa fa-warning', 'type' => 'danger');
            }else{

              //lock table
              $this->_module->lock_tabel('stock_move WRITE, pengiriman_barang WRITE, departemen as d WRITE');
              
              $warehouse     = $deptid;
              $method_dept   = $warehouse;
              $method_action = 'OUT'; 
              $method        = $warehouse.'|'.$method_action;

              $stock_location_dr = $this->_module->get_nama_dept_by_kode($warehouse)->row_array(); // ex : warehouse/stock lokasi dari
              $lokasi_dari   = $stock_location_dr['stock_location'];

              $stock_location_tj = $this->_module->get_nama_dept_by_kode($lok_tujuan)->row_array(); // ex : warehouse/stock lokasi tujuan
              $lokasi_tujuan = $stock_location_tj['stock_location'];


              // get  pengiriman barang
              $kode_= $this->_module->get_kode_pengiriman($method_dept);
              $get_kode_out= $kode_;

              $dgt     =substr("00000" . $get_kode_out,-5);            
              $kode_out = $method_dept."/".$method_action."/".date("y").  date("m").$dgt;

              // get move id
              $last_move   = $this->_module->get_kode_stock_move();
              $move_id     = "SM".$last_move; //Set kode stock_move

              // simpan stock_move
              $sql_stock_move_batch = "('".$move_id."','".$tgl."','','".$method."','".$lokasi_dari."','".$lokasi_tujuan."','draft','1','')";      
              $this->_module->create_stock_move_batch($sql_stock_move_batch);    

              // simpan pengiriman barang
              $sql_out_batch   = "('".$kode_out."','".$tgl."','".$tgl."','".$tgl_jt."','".addslashes($reff_note)."','draft','".$method_dept."','','".$move_id."','','".$lokasi_dari."','".$lokasi_tujuan."','1')"; 
              $this->_module->simpan_pengiriman_add_manual($sql_out_batch);    

              //unlock table
              $this->_module->unlock_tabel();

              // create log history
              $jenis_log   = "create";
              $note_log    = $kode_out." | ".$reff_note;
              $this->_module->gen_history_deptid($sub_menu, $kode_out, $jenis_log, $note_log, $username, $deptid);  

              $kode_encrypt = encrypt_url($kode_out);
              $callback = array('status' => 'success', 'field' => 'kode' , 'message' => 'Data Berhasil Disimpan !', 'isi'=> $kode_out, 'icon' =>'fa fa-check', 'type' => 'success', 'kode_encrypt' => $kode_encrypt);

            }

          }else{

            //cek status terkirim ?
            $cek_kirim  = $this->m_pengirimanBarang->cek_status_barang($kode)->row_array();
            if($cek_kirim['status'] == 'done'){
                $callback = array('status' => 'ada', 'message'=>'Maaf, Data Tidak bisa Disimpan, Data Sudah Terkirim !', 'icon' => 'fa fa-warning', 'type'=>'danger');
            }else if($cek_kirim['status'] == 'cancel'){
                $callback = array('status' => 'ada', 'message'=>'Maaf, Data Tidak bisa Disimpan, Data Pengiriman Sudah dibatalkan !', 'icon' => 'fa fa-warning', 'type'=>'danger');
            }else{

                if(empty($reff_note)){
                    $callback = array('status' => 'failed', 'message' => 'Reff Note Harus Diisi !', 'icon' =>'fa fa-warning', 'type' => 'danger');
                }else{
                    $this->m_pengirimanBarang->update_pengiriman_barang($kode,$reff_note);
                    $callback = array('status' => 'success', 'message' => 'Data Berhasil Disimpan !', 'icon' =>'fa fa-check', 'type' => 'success');
                    $jenis_log   = "edit";
                    $note_log    = "-> ".$reff_note;
                    $this->_module->gen_history_deptid($sub_menu, $kode, $jenis_log, $note_log, $username,$deptid);
                }
            }

          }

      }
      echo json_encode($callback);
  }


	  public function edit($kode = null)
    {   
        if(!isset($kode)) show_404();
        $kode_decrypt  = decrypt_url($kode);
        $data["list"]  = $this->m_pengirimanBarang->get_data_by_code($kode_decrypt);
        $data["items"] = $this->m_pengirimanBarang->get_list_pengiriman_barang($kode_decrypt);
        $move          = $this->m_pengirimanBarang->get_stock_move_by_kode($kode_decrypt)->row_array();
        $data['smove'] = $move;
        $data['mo']    = $this->m_pengirimanBarang->get_kode_mo_pengiriman_barang_by_move_id($move['move_id'])->row_array();
        $data['smi'] = $this->m_pengirimanBarang->get_stock_move_items_by_kode($kode_decrypt);

        if(empty($data["list"])){
          show_404();
        }else{
          return $this->load->view('warehouse/v_pengiriman_barang_edit',$data);
        }
    	
    }

    public function edit_barcode($kode = null)
    {   
        if(!isset($kode)) show_404();
        $kode_decrypt   = decrypt_url($kode);
        $data["list"]   = $this->m_pengirimanBarang->get_data_by_code($kode_decrypt);
        $data["move_id"]= $this->m_pengirimanBarang->get_move_id_by_kode($kode_decrypt)->row_array();
        $data['items']  = $this->m_pengirimanBarang->get_stock_move_items_by_kode($kode_decrypt);

        if(empty($data["list"])){
          show_404();
        }else{
          return $this->load->view('warehouse/v_pengiriman_barang_edit_barcode',$data);
        }
    	
    }

    public function tambah_data_details()
    {
        $nama_produk  = $this->input->post('nama_produk');
      	$kode_produk  = $this->input->post('kode_produk');
        $move_id      = $this->input->post('move_id');
        $deptid       = $this->input->post('deptid');
        //$kc = array('DN','JM','RB','RS','TN','TR','TS','TV');

  		if(strpos($nama_produk,'DN')){
            $nama     = explode('-', $nama_produk); 
            $prod    = substr($nama[1], 2);
        }elseif(strpos($nama_produk,'DN')){
            $nama     = explode('-', $nama_produk); 
            $prod    = substr($nama[1], 2);
        }elseif(strpos($nama_produk,'RB')){
            $nama     = explode('-', $nama_produk); 
            $prod    = substr($nama[1], 2);
        }elseif(strpos($nama_produk,'RS')){
            $nama     = explode('-', $nama_produk); 
            $prod    = substr($nama[1], 2);
        }elseif(strpos($nama_produk,'TN')){
            $nama     = explode('-', $nama_produk); 
            $prod    = substr($nama[1], 2);
        }elseif(strpos($nama_produk,'TR')){
            $nama     = explode('-', $nama_produk); 
            $prod    = substr($nama[1], 2);
        }elseif(strpos($nama_produk,'TS')){
            $nama     = explode('-', $nama_produk); 
            $prod    = substr($nama[1], 2);
        }elseif(strpos($nama_produk,'TV')){
            $nama     = explode('-', $nama_produk); 
            $prod    = substr($nama[1], 2);
        }else{
            $nama    = explode("-", $nama_produk);
            $prod    = $nama[1];
        }
    	  $data['prod'] = $prod;
        $data['kode'] = $kode_produk;
        $data['nama_produk'] = $nama_produk;
        $data['move_id'] = $move_id;
        $data['deptid']  = $deptid;
        return $this->load->view('modal/v_tambah_details_pengiriman_modal',$data);

    }

    public function tambah_data_details_quant()
    {
        $kode_produk  = $this->input->post('kode_produk');
        $move_id      = $this->input->post('move_id');
        $deptid       = $this->input->post('deptid');
        $nama_produk  = $this->input->post('nama_produk');
        $origin       = $this->input->post('origin');
        $origin_prod  = $this->input->post('origin_prod');

        $data['kode'] = $kode_produk;
        $data['nama_produk'] = $nama_produk;
        $data['move_id'] = $move_id;
        $data['deptid']  = $deptid;
        $data['origin']  = $origin;
        $data['origin_prod']  = $origin_prod;
        return $this->load->view('modal/v_tambah_details_quant_pengiriman_modal',$data);

    }



    public function tambah_data_details_quant_modal()
    {
        $kode  = $this->input->post('kode');
        $kode_produk  = $this->input->post('kode_produk');
        $move_id      = $this->input->post('move_id');
        $origin      = $this->input->post('origin');
        $deptid      = $this->input->post('deptid');
        //lokasi tujuan, lokasi dari
        $destination_location = $this->m_pengirimanBarang->get_location_by_move_id($move_id)->row_array();

        $list = $this->m_pengirimanBarang->get_datatables3($kode,$kode_produk,$destination_location['lokasi_dari'],$origin,$deptid);
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
            $row[] = $field->reff_note;
            //$row[] = '';//buat checkbox
            //$row[] = $field->kode_produk."|".htmlentities($field->nama_produk)."|".$field->lot."|".$field->qty."|".$field->uom."|".$field->qty2."|".$field->uom2."|".$field->lokasi."|".$field->quant_id."|^";
            $row[] = $field->quant_id;
          
            $data[] = $row;
        }
        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_pengirimanBarang->count_all3($kode,$kode_produk,$destination_location['lokasi_dari'],$origin,$deptid),
            "recordsFiltered" => $this->m_pengirimanBarang->count_filtered3($kode,$kode_produk,$destination_location['lokasi_dari'],$origin,$deptid),
            "data" => $data,
        );
        //output dalam format JSON
        echo json_encode($output);
    }

/*
    public function save_details_modala()//revisi ada perbaikan mungkin tidak dipakai
    {
        $sub_menu  = $this->uri->segment(2);
        $username = $this->session->userdata('username'); 
        $deptid      = $this->input->post('deptid');

        if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
            // session habis
            $callback = array('message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
        }else{

          $kode       = $this->input->post('kode');
          $kode_produk= $this->input->post('kode_produk');
          $nama_produk= $this->input->post('nama_produk');
          $move_id    = $this->input->post('move_id');
          $check      = $this->input->post('checkbox');
          $countchek  = $this->input->post('countchek');
          $sql_stock_quant_batch      = "";
          $sql_stock_move_items_batch = "";
          $tgl        = date('Y-m-d H:i:s');
          $row        = explode("^,", $check);
          $status     = "";
          $status_brg = "ready";
          $lot        = '';

          //lock tabel
          $this->_module->lock_tabel('stock_quant WRITE, stock_move_items WRITE,stock_move WRITE,stock_move_produk WRITE, pengiriman_barang WRITE, pengiriman_barang_items WRITE' );
          //get row order stock_move_items
          $row_order  = $this->_module->get_row_order_stock_move_items_by_kode($move_id);
          //get last quant id
          $start = $this->m_pengirimanBarang->get_last_quant_id();
       
          for($i=0; $i <= $countchek-1;$i++){
              $dt1  =  $row[$i];
              $row2 = explode("|", $dt1);

              $cek  = $this->m_pengirimanBarang->cek_stock_move_items($row2[1])->row_array();//cek apa sudah ada lot sama
              if($cek['lot'] == $row2[1]){
                $status = 'failed';
                $lot    = $row2[1];
                break;
              }
              $sql_stock_quant_batch .= "('".$start."','".$tgl."', '".$kode_produk."', '".$nama_produk."','".$row2[0]."','".trim($row2[1])."','".$row2[2]."','m','".$row2[3]."','".$row2[4]."','".$row2[5]."','".$move_id."',''), ";
              $sql_stock_move_items_batch .= "('".$move_id."', '".$start."','".$kode_produk."', '".$nama_produk."','".trim($row2[1])."','".$row2[2]."','m','".$row2[3]."','".$row2[4]."','draft','".$row_order."'), ";
              $row_order++;
              $start++;
          }

          if(!empty($sql_stock_quant_batch)){
              $sql_stock_quant_batch = rtrim($sql_stock_quant_batch, ', ');
              $this->m_pengirimanBarang->simpan_stock_quant_batch($sql_stock_quant_batch);
              $sql_stock_move_items_batch = rtrim($sql_stock_move_items_batch, ', ');
              $this->m_pengirimanBarang->simpan_stock_move_items_batch($sql_stock_move_items_batch);
              $this->m_pengirimanBarang->update_status_pengiriman_barang_items($kode,$nama_produk,$status_brg);
              $this->_module->update_status_stock_move_items($move_id,$kode_produk,$status_brg);

              $cek_status = $this->m_pengirimanBarang->cek_status_barang_pengiriman_barang_items($kode,'draft')->row_array();
              if(empty($cek_status['status_barang'])){
                $this->m_pengirimanBarang->update_status_pengiriman_barang($kode,$status_brg);
                $this->_module->update_status_stock_move_produk($move_id,$status_brg);
                $cek_status2 = $this->m_pengirimanBarang->cek_status_pengiriman_barang($kode)->row_array();
                if($cek_status2['status']=='ready'){
                    $this->_module->update_status_stock_move($move_id,$status_brg);
                }
              }
 
          }

          //unlock table
          $this->_module->unlock_tabel();
          
          
          if($status == "failed"){
            $callback    = array('status'=>$status, 'lot'=>$lot);
          }else{
            $jenis_log   = "edit";
            $note_log    = "Tambah Data Details";
            $this->_module->gen_history_deptid($sub_menu, $kode, $jenis_log, $note_log, $username,$deptid);
            $callback    = array('status'=>'success',  'message' => 'Detail Product Berhasil Ditambahkan !',  'icon' =>'fa fa-check', 'type' => 'success');
          }
        }
        echo json_encode($callback);
    }
*/

    public function save_details_quant_modal()
    {
        $sub_menu   = $this->uri->segment(2);
        $username   = addslashes($this->session->userdata('username')); 
        $deptid     = $this->input->post('deptid');
        $kode       = $this->input->post('kode');

        $cek_kirim  = $this->m_pengirimanBarang->cek_status_barang($kode)->row_array();
        if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
          // session habis
          $callback = array('message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
        }else if($cek_kirim['status'] == 'done'){//cek jika status pengiriman sudah terkii
            $callback = array('status' => 'failed', 'message'=>'Maaf, Data TIdak Bisa Disimpan, Data Sudah Terkirim !', 'icon' => 'fa fa-warning', 'type'=>'danger');
        
        }else if($cek_kirim['status'] == 'cancel'){//cek jika status pengiriman batal
            $callback = array('status' => 'failed', 'message'=>'Maaf, Data Tidak Bisa Disimpan, Data Pengiriman Sudah dibatalkan !', 'icon' => 'fa fa-warning', 'type'=>'danger');
        }else{

          $kode_produk= $this->input->post('kode_produk');
          $nama_produk= $this->input->post('nama_produk');
          $origin_prod= $this->input->post('origin_prod');
          $origin     = $this->input->post('origin');
          $move_id    = $this->input->post('move_id');
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
          $this->_module->lock_tabel('stock_quant WRITE, stock_move_items WRITE,stock_move WRITE,stock_move_produk WRITE, pengiriman_barang WRITE, pengiriman_barang_items WRITE, mrp_production_rm_target WRITE' );
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

            /*
              $dt1         =  $row[0];
              $row2        = explode("|", $dt1);
              $quantid     = $row2[8];     
              $kode_produk = $row2[0];
              $nama_produk = $row2[1];
              $lot         = $row2[2];
              $qty         = $row2[3];
              $uom         = $row2[4];
              $qty2        = $row2[5];
              $uom2        = $row2[6];
              $lokasi      = $row2[7];
              //$break   = false;
            */

              //cek product di stock quant
              $cq = $this->_module->cek_produk_di_stock_quant($quantid,$location['lokasi_dari'])->row_array();
              if(!empty($cq['quant_id'])){

                //untuk mendapatkan origin_prod yang terdapat consume kedepannya atau consume lebih dari 1
                $loop_sm    = true;
                $loop_count = 1;
                $origin_prod_tj = "";
                $next       = false;
                $con_next   = false;
                $con        = false;
                
                /*
                //get list stock_move by origin
                $list_sm = $this->_module->get_list_stock_move_origin($origin)->result_array();
                foreach ($list_sm as $row) {
                       
                    $mt = explode("|", $row['method']);
                    $ex_deptid = $mt[0];
                    $ex_mt     = $mt[1];

                    if($loop_sm == true){

                        if($ex_mt == 'CON' AND $con_next == true){

                            //get  origin_prod by move id, kode_produk
                            $get_origin_prod = $this->m_pengirimanBarang->get_origin_prod_mrp_production_by_kode($row['move_id'],addslashes($kode_produk))->row_array();
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
                    $origin_prod = $origin_prod_tj; // origin prod berdasarkan 
                }else{
                    $origin_prod = '';
                }
                */

                //insert ke stock move items
                $sql_stock_move_items_batch .= "('".$move_id."', '".$quantid."','".addslashes($kode_produk)."', '".addslashes($nama_produk)."','".addslashes(trim($lot))."','".$qty."','".addslashes($uom)."','".$qty2."','".addslashes($uom2)."','ready','".$row_order."','".addslashes($origin_prod)."', '".$tgl."'), ";     
                $row_order++;           

                //update reserve move by quant id di stok quant                
                $case       .= "when quant_id = '".$quantid."' then '".$move_id."'";
                $where      .= "'".$quantid."',";

              }else{
                $kosong = true;
              }        

          }
        
          if(!empty($sql_stock_move_items_batch) AND $kosong == false){
              $sql_stock_move_items_batch = rtrim($sql_stock_move_items_batch, ', ');
              $this->_module->simpan_stock_move_items_batch($sql_stock_move_items_batch);
            
              if(!empty($case)){
                //update qty stock quant 
                $where = rtrim($where, ',');
                $sql_update_qty_stock_quant  = "UPDATE stock_quant SET reserve_move =(case ".$case." end) WHERE  quant_id in (".$where.") ";
                $this->_module->update_perbatch($sql_update_qty_stock_quant);
              }

              $this->m_pengirimanBarang->update_status_pengiriman_barang_items($kode,addslashes($kode_produk),$status_brg);
              $this->_module->update_status_stock_move_items($move_id,addslashes($kode_produk),$status_brg);
              $cek_status = $this->m_pengirimanBarang->cek_status_barang_pengiriman_barang_items($kode,'draft')->row_array();

              if(empty($cek_status['status_barang'])){
                $this->m_pengirimanBarang->update_status_pengiriman_barang($kode,$status_brg);
                $this->_module->update_status_stock_move_produk($move_id,addslashes($kode_produk),$status_brg);
                $cek_status2 = $this->m_pengirimanBarang->cek_status_pengiriman_barang($kode)->row_array();
                if($cek_status2['status']=='ready'){
                    $this->_module->update_status_stock_move($move_id,$status_brg);
                }
              }
          }

          //unlock table
          $this->_module->unlock_tabel(); 
          
          if($kosong == false){            
            $jenis_log   = "edit";
            $note_log    = "Tambah Data Details";
            $this->_module->gen_history_deptid($sub_menu, $kode, $jenis_log, $note_log, $username,$deptid);
            $callback    = array('status'=>'success',  'message' => 'Detail Product Berhasil Ditambahkan !',  'icon' =>'fa fa-check', 'type' => 'success');            
          }else{
             $callback    = array('status'=>'kosong',  'message' => 'Maaf, Product Sudah ada yang terpakai !',  'icon' =>'fa fa-check', 'type' => 'danger');  
          } 
            
        }
        echo json_encode($callback);
    }

    public function get_produk_pengirimanbarang_select2()
    {
        $prod     = addslashes($this->input->post('prod'));
        $lokasi   = addslashes($this->input->post('lokasi'));
        $callback = $this->m_pengirimanBarang->get_list_produk_pengirimanbarang_by_stock($prod,$lokasi);
        echo json_encode($callback);
    }


    public function get_produk_pengirimanbarang_by_kode()
    {
        $kode_produk = addslashes($this->input->post('kode_produk'));
        $result      = $this->m_pengirimanBarang->get_produk_pengiriman_barang_by_kode_produk($kode_produk)->row_array();
        $callback    = array('kode_produk'=>$result['kode_produk'],'nama_produk'=>$result['nama_produk'],'uom'=>$result['uom']);
        echo json_encode($callback);
    }

    public function simpan_product_pengiriman_barang()
    {
        if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
            // session habis
            $callback = array('message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
        }else{


            $sub_menu  = $this->uri->segment(2);
            $username  = addslashes($this->session->userdata('username')); 

            $kode        = ($this->input->post('kode')); 
            $kode_produk = ($this->input->post('kode_produk')); 
            $nama_produk = ($this->input->post('nama_produk')); 
            $qty         = $this->input->post('qty'); 
            $uom         = ($this->input->post('uom')); 
            $row1        = $this->input->post('row_order'); 
            $data        = explode("^|",$row1);
            $row         = $data[0];

            // cek status 
            $cek_kirim = $this->m_pengirimanBarang->cek_status_barang($kode)->row_array();
            if($cek_kirim['status'] == 'done'){//cek jika status pengiriman sudah terkii
                $callback = array('status' => 'failed', 'message'=>'Maaf, Product Tidak Bisa Dismpan, Data Sudah Terkirim !', 'icon' => 'fa fa-warning', 'type'=>'danger');
            }else if($cek_kirim['status'] == 'cancel'){//cek jika status pengiriman batal
                $callback = array('status' => 'failed', 'message'=>'Maaf, Product Tidak Bisa Dismpan, Data Pengiriman Sudah dibatalkan !', 'icon' => 'fa fa-warning', 'type'=>'danger');
            
            }else{

                if(!empty($row)){//update details
                  
                    $kode_produk_ex_row = addslashes($data[1]);
                    $move_id     = $data[2];
                
                    $cek_status = $this->m_pengirimanBarang->cek_status_product_pengiriman_barang_items_by_row($kode,$kode_produk_ex_row,$row)->row_array(); 

                    if(empty($cek_status['kode_produk'])){
                        $callback = array('status' => 'failed','message' => 'Maaf, Produk Kosong !', 'icon' =>'fa fa-check', 'type' => 'danger');
                    }else if($cek_status['status_barang'] == 'done'){
                        $callback = array('status' => 'failed','message' => 'Maaf, Data tidak bisa Diubah, Status Product Sudah Terkirim !', 'icon' =>'fa fa-check', 'type' => 'danger');
                    }else{
                        // update pengiriman barang items
                        $this->m_pengirimanBarang->update_pengiriman_barang_items_by_kode($kode,$kode_produk_ex_row,$qty,$row);

                        // update stock_move produk
                        $this->m_pengirimanBarang->update_stock_move_produk_by_kode($move_id,$kode_produk_ex_row,$qty,$row);
                
                        $jenis_log   = "edit";
                        $note_log    = "Edit data Details | ".$kode." | ".$kode_produk_ex_row." | ".$qty." | ".$row;
                        $this->_module->gen_history($sub_menu, $kode, $jenis_log, $note_log, $username);
                        $callback = array('status' => 'success','message' => 'Data Berhasil Disimpan !', 'icon' =>'fa fa-check', 'type' => 'success');
                    }
                    
                    
                  }else{//simpan data baru

                        // cek apa produk sudah pernah di input atau belum
                        $cek_prod = $this->m_pengirimanBarang->cek_produk_pengiriman_barang_items($kode,$kode_produk)->row_array();

                        if(!empty($cek_prod['kode_produk'])){
                          $callback = array('status' => 'failed','message' => 'Maaf, Product '.$nama_produk.' sudah pernah diinput !', 'icon' =>'fa fa-check', 'type' => 'danger');

                        }else{

                          $ro  = $this->m_pengirimanBarang->get_row_order_pengiriman_barang_items($kode)->row_array();
                          $row_order = $ro['row_order']+1;
                          $status     = 'draft';
                          $origin_prod = $kode_produk.'_'.$row_order;

                          // get move id by kode_out
                          $mv = $this->m_pengirimanBarang->get_move_id_pengiriman_barang_by_kode($kode)->row_array();

                          // simpan product pengiriman barang
                          $sql_simpan_product_out = "('".$kode."', '".addslashes($kode_produk)."','".addslashes($nama_produk)."','".$qty."','".addslashes($uom)."','".$status."','".$row_order."','".addslashes($origin_prod)."') "; 
                          $this->_module->simpan_pengiriman_items_batch($sql_simpan_product_out);
                          
                          // simpan stock move produk
                          $sql_stock_move_produk = "('".$mv['move_id']."','".addslashes($kode_produk)."','".addslashes($nama_produk)."','".$qty."','".addslashes($uom)."','".$status."','".$row_order."','".addslashes($origin_prod)."') ";
                          $this->_module->create_stock_move_produk_batch($sql_stock_move_produk);

                      
                          $jenis_log   = "edit";
                          $note_log    = "Tambah Products | ".$kode_produk." | ".$nama_produk." | ".$qty." | ".$uom." | ".$row_order;
                          $this->_module->gen_history($sub_menu, $kode, $jenis_log, $note_log, $username);
                              $callback = array('status' => 'success','message' => 'Data Berhasil Disimpan !', 'icon' =>'fa fa-check', 'type' => 'success');
                            
                        }
                  }
              }

        }

        echo json_encode($callback);
    }

    public function hapus_products_pengiriman_barang()
    {

        if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
            // session habis
            $callback = array('message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
        }else{
            $sub_menu  = $this->uri->segment(2);
            $username  = addslashes($this->session->userdata('username'));

            $row  = $this->input->post('row_order');
            $kode = $this->input->post('kode');
            $dept_id = $this->input->post('dept_id');

            $ex   = explode('^|',$row);
            $row_order = $ex[0];
            $kode_produk = $ex[1];
            $move_id     = $ex[2];
            $origin_prod = $ex[3];
            //$origin_prod = $kode_produk.'_'.$row_order; origin buatan tapi prinsipnya sama 

            $cek_kirim = $this->m_pengirimanBarang->cek_status_barang($kode)->row_array();

            if($cek_kirim['status'] == 'done'){//cek jika status pengiriman sudah terkirim
                $callback = array('status' => 'failed', 'message'=>'Maaf, Data Tidak Bisa Dihapus, Data Sudah Terkirim !', 'icon' => 'fa fa-warning', 'type'=>'danger');
            }else if($cek_kirim['status'] == 'cancel'){//cek jika status pengiriman batal
                $callback = array('status' => 'failed', 'message'=>'Maaf, Data Tidak Bisa Dihapus, Data Pengiriman Sudah dibatalkan !', 'icon' => 'fa fa-warning', 'type'=>'danger');
            }else{  

                // cek apa sudah terdapat items ?
                $cek_details = $this->m_pengirimanBarang->cek_details_items_pengiriman_barang_by_produk($move_id,$kode_produk,$origin_prod)->row_array();

                if(!empty($cek_details['kode_produk'])){
                  $callback = array('status' => 'failed', 'message'=>'Maaf, Data Tidak Bisa Dihapus, Silahkan Detail/lot Produk Hapus terlebih dahulu !', 'icon' => 'fa fa-warning', 'type'=>'danger');
                }else{

                  // hapus produk di pengiriman barang dan stock_move_produk
                  $this->m_pengirimanBarang->hapus_produk_pengirim_barang_dan_stock_move_produk_by_kode($move_id,$kode,$kode_produk,$row_order);

                  $jenis_log   = "cancel";
                  $note_log    = "Hapus Produk";
                  $this->_module->gen_history_deptid($sub_menu, $kode, $jenis_log, $note_log, $username, $dept_id);
                  $callback = array('status' => 'success', 'message'=>'Data Berhasil di hapus !', 'icon' => 'fa fa-check', 'type'=>'success');

                }

            }

        }

        echo json_encode($callback);
    }

    public function hapus_details_items()
    {   
        $sub_menu  = $this->uri->segment(2);
        $username  = addslashes($this->session->userdata('username')); 
        $deptid    = $this->input->post('deptid');
        $kode      = $this->input->post('kode');

        $cek_kirim = $this->m_pengirimanBarang->cek_status_barang($kode)->row_array();

        if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
            // session habis
            $callback = array('message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
        }else if($cek_kirim['status'] == 'done'){//cek jika status pengiriman sudah terkii
            $callback = array('status' => 'failed', 'message'=>'Maaf, Data Tidak Bisa Dihapus, Data Sudah Terkirim !', 'icon' => 'fa fa-warning', 'type'=>'danger');
        }else if($cek_kirim['status'] == 'cancel'){//cek jika status pengiriman batal
            $callback = array('status' => 'failed', 'message'=>'Maaf, Data Tidak Bisa Dihapus, Data Pengiriman Sudah dibatalkan !', 'icon' => 'fa fa-warning', 'type'=>'danger');
        }else{

            $quant_id   = $this->input->post('quant_id');
            $row_order  = $this->input->post('row_order');
            $move_id    = $this->input->post('move_id');
            $kode_produk= addslashes($this->input->post('kode_produk'));
            $nama_produk= addslashes($this->input->post('nama_produk'));
            $origin_prod= addslashes($this->input->post('origin_prod'));
            $status_brg = 'draft';
            
            //lock tabel
            $this->_module->lock_tabel('stock_quant WRITE, stock_move WRITE,stock_move_items WRITE,stock_move_produk WRITE, pengiriman_barang WRITE, pengiriman_barang_items WRITE' );
            
            //delete stock move item dan update reserve move jadi kosong
            $this->_module->delete_details_items($move_id,$quant_id,$row_order);

            //get sum qty produk stock move items
            $get_qty2  = $this->_module->get_qty_stock_move_items_by_kode($move_id,$kode_produk)->row_array();           

            //update status draft jika qty di stock move items kosong
            if(empty($get_qty2['sum_qty'])){
              $this->m_pengirimanBarang->update_status_pengiriman_barang_items($kode,$kode_produk,$status_brg);
              $this->_module->update_status_stock_move_produk($move_id,$kode_produk,$status_brg);
            }

            $cek_status = $this->m_pengirimanBarang->cek_status_barang_pengiriman_barang_items($kode,'draft')->row_array();
            if(!empty($cek_status['status_barang'])){
                $this->m_pengirimanBarang->update_status_pengiriman_barang($kode,$status_brg);
                $cek_status2 = $this->m_pengirimanBarang->cek_status_pengiriman_barang($kode)->row_array();
                if($cek_status2['status']=='draft'){
                    $this->_module->update_status_stock_move($move_id,$status_brg);
                }
            }

            if(empty($cek_status['status_barang'])){
                $this->m_pengirimanBarang->update_status_pengiriman_barang($kode,'ready');
                $cek_status2 = $this->m_pengirimanBarang->cek_status_pengiriman_barang($kode)->row_array();
                if($cek_status2['status']=='ready'){
                    $this->_module->update_status_stock_move($move_id,'ready');
                }
            }
            
            //unlock table
            $this->_module->unlock_tabel();
            
            $jenis_log   = "cancel";
            $note_log    = "Hapus Data Details";
            $this->_module->gen_history_deptid($sub_menu, $kode, $jenis_log, $note_log, $username,$deptid);
            
            $callback = array('status' => 'success', 'message'=>'Data Berhasil di hapus !', 'icon' => 'fa fa-check', 'type'=>'success');
        }
       echo  json_encode($callback);

    }

    public function kirim_barang()
    {
        $kode        = $this->input->post('kode');
        $move_id     = $this->input->post('move_id');
        $method      = $this->input->post('method');
        $origin      = $this->input->post('origin');       
        $sql_stock_move_items_batch = "";
        $tgl         = date('Y-m-d H:i:s');
        $status_done = 'done';
        $case        = "";
        $where       = "";
        $case2       = "";
        $where2      = "";
        $case3       = "";
        $where3      = "";
        $case4       = "";
        $where4      = "";
        $case5       = "";
        $where5      = "";
        $case6       = "";
        $where6      = "";
        $case7       = "";
        $where7      = "";

        //cek lokasi valid lot
        $cek_lot  = $this->m_pengirimanBarang->cek_valid_lokasi_lot_by_move_id($move_id)->row_array();
        $sub_menu = $this->uri->segment(2);
        $username = addslashes($this->session->userdata('username')); 
        $nu       = $this->_module->get_nama_user($username)->row_array();
        $nama_user= $nu['nama'];
        $deptid   = $this->input->post('deptid');

        if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
            // session habis
            $callback = array('message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
        }else{
            //cek status terkirim ?
            $cek_kirim  = $this->m_pengirimanBarang->cek_status_barang($kode)->row_array();
            if($cek_kirim['status'] == 'draft'){
                $callback = array('status' => 'ada', 'message'=>'Maaf, Product Belum ready !', 'icon' => 'fa fa-warning', 'type'=>'danger');
            }elseif($cek_kirim['status'] == 'done'){
                $callback = array('status' => 'ada', 'message'=>'Maaf, Data Sudah Terkirim !', 'icon' => 'fa fa-warning', 'type'=>'danger');
            }elseif($cek_kirim['status'] == 'cancel'){
                $callback = array('status' => 'failed', 'message'=>'Maaf, Data Tidak bisa Dikirim, Data Sudah dibatalkan !', 'icon' => 'fa fa-warning', 'type'=>'danger');
            }elseif(!empty($cek_lot['lot']) AND  $method == 'GRG|OUT'){  //lokasi lot tidak valid
                $callback = array('status' => 'not_valid', 'message'=>'Maaf, Lokasi  Lot "'.$cek_lot['lot'].'" tidak valid !', 'icon' => 'fa fa-warning', 'type'=>'danger');
               // break;
            }else{
                    //lock tabel 
                    $this->_module->lock_tabel('stock_move WRITE,stock_move_items WRITE,stock_move_produk WRITE, pengiriman_barang WRITE, pengiriman_barang_items WRITE, stock_quant WRITE, penerimaan_barang WRITE, penerimaan_barang_items WRITE, mrp_production WRITE, log_history WRITE, mrp_production_rm_target WRITE, main_menu_sub WRITE');
            
                    //lokasi tujuan 
                    $lokasi = $this->m_pengirimanBarang->get_location_by_move_id($move_id)->row_array();                    
                    //update status tbl pengiriman brg
                    $this->m_pengirimanBarang->update_status_pengiriman_barang($kode,$status_done);
                    //update status tbl pengiriman brg items
                    $this->m_pengirimanBarang->update_status_pengiriman_barang_items_full($kode,$status_done);
                    //update stock_move_produk
                    $this->_module->update_status_stock_move_produk_full($move_id,$status_done);
                    //update status tbl stock move 
                    $this->_module->update_status_stock_move($move_id,$status_done);
                    // update tangal kirim = now
                    $this->m_pengirimanBarang->update_tgl_kirim_pengiriman_barang($kode,$tgl);

                    //get move id tujuan
                    $sm_tj = $this->_module->get_stock_move_tujuan($move_id,$origin,'done','cancel')->row_array();

                    $move_id_out = $move_id;//move id asal yg ngebentuk back order
                   
                    //get row order stock_move_items
                    $row_order  = $this->_module->get_row_order_stock_move_items_by_kode($sm_tj['move_id']);
                    
                    //loop stock_move_items
                    $querysm = $this->_module->get_stock_move_items_by_move_id($move_id);
                    foreach ($querysm as $val) {
                        $loop_sm     = true;
                        $sm_pasangan = true;
                        $move_id     = $val->move_id;
                      
                        //sebanyak stock_move tujuanya ada
                        while ($loop_sm) {
                            if($sm_pasangan){
                                $status = "ready";
                                $tmp_mt = 'IN';    
                            }else{
                                $status = "draft";
                                $tmp_mt = '';
                            }


                            //untuk mendapatkan origin_prod yang terdapat in dan consume department selanjutnya
                            $loop_sm2   = true;
                            $loop_count = 1;
                            $origin_prod_tj = "";
                            $next       = false;
                            $con_next   = false;
                                  
                            //get list stock_move by origin
                            $list_sm = $this->_module->get_list_stock_move_origin($origin)->result_array();
                            foreach ($list_sm as $row) {
                                       
                                $mt = explode("|", $row['method']);
                                $ex_deptid = $mt[0];
                                $ex_mt     = $mt[1];

                                if($loop_sm2 == true){

                                    if($ex_mt == 'IN' AND $tmp_mt == 'IN'){

                                        // get move id in yang draft by method dan origin()
                                        $get_mto = $this->m_pengirimanBarang->get_move_id_by_method_origin($row['method'],$origin,'done','cancel')->row_array();

                                        if($get_mto['move_id'] != ''){

                                          //get  origin_prod by move id, kode_produk
                                          $kode_in = $this->m_pengirimanBarang->get_kode_penerimaan_by_move_id($get_mto['move_id'])->row_array();

                                          $op = $this->m_pengirimanBarang->get_origin_prod_penerimaan_barang_by_kode($kode_in['kode'],addslashes($val->kode_produk))->row_array();

                                          $origin_prod_tj = $op['origin_prod'];
                                        }
                                        
                                        $loop_sm2 =false;
                                    }

                                    if($ex_mt == 'CON' AND $con_next == true){

                                        //get  origin_prod by move id, kode_produk
                                        $get_origin_prod = $this->m_pengirimanBarang->get_origin_prod_mrp_production_by_kode($row['move_id'],addslashes($val->kode_produk))->row_array();
                                        $origin_prod_tj = $get_origin_prod['origin_prod'];
                                        $loop_sm2 =false;
                                               
                                    }

                                    if($ex_deptid == $deptid AND $ex_mt == 'CON'){
                                        $con_next = true;
                                    }

                                }elseif($loop_sm2 == false){
                                    break;//paksa keluar looping
                                }

                                    //$loop_count = $loop_count + 1;
                            }
                                          
                            if(!empty($origin_prod_tj)){
                                $origin_prod = $origin_prod_tj; // origin prod berdasarkan 
                            }else{
                                $origin_prod = '';
                            }

                            //query ke stock_move tujuan
                            $querysm_tujuan = $this->_module->get_stock_move_tujuan($move_id,$origin,'done','cancel')->row_array();
                            if(!empty($querysm_tujuan['move_id'])){
                                
                                                            
                                //insert stock_move items untuk stock_move tujuan
                                $sql_stock_move_items_batch .= "('".$querysm_tujuan['move_id']."', '".$val->quant_id."', '".addslashes($val->kode_produk)."', '".addslashes($val->nama_produk)."', '".addslashes($val->lot)."', '".$val->qty."', '".addslashes($val->uom)."', '".$val->qty2."', '".addslashes($val->uom2)."', '".$status."', '".$row_order."', '".addslashes($origin_prod)."', '".$tgl."'), ";
                                $sm_pasangan = false;
                                $row_order++;
                                
                                $move_id = $querysm_tujuan['move_id'];
                                //update status stock move,stock move dan stock move produk  penerimaan brg = ready
                                $case3  .= "when move_id = '".$move_id."' then '".$status."'";
                                $where3 .= "'".$move_id."',";

                                //update stock move 
                                $get_kode_in = $this->m_pengirimanBarang->get_kode_penerimaan_by_move_id($move_id)->row_array();
                                if(!empty($get_kode_in['kode'])){
                                  //update penerimaan barang items = ready
                                    $case4  .= "when kode = '".$get_kode_in['kode']."' then '".$status."'";
                                    $where4 .= "'".$get_kode_in['kode']."',"; 
                                }
                                  
                                //cek jika method stock move tujuan nya IN
                                $mthd = explode("|",$querysm_tujuan['method']);
                                $ex_mthd = $mthd[1];
                                if($ex_mthd == 'IN'){ // jika stock move tujuanya IN maka loop_sm ==false
                                    $loop_sm = false;
                                }
                                  
                            }else{
                                //jika sdh tidak ada stockmove tujuan maka loop_sm berhenti
                                $loop_sm = false;
                            }

                        }//end while

                        //update stok move items asal set done
                        $case  .= "when move_id = '".$val->move_id."' then '".$status_done."'";
                        $where .= "'".$val->move_id."',";

                        //update stock quant kode_lokasi
                        $case2 .= "when quant_id = '".$val->quant_id."' then '".$lokasi['lokasi_tujuan']."'";
                        $where2.= "'".$val->quant_id."',";
                        /*
                        //update lokasi lot
                        $case5  .= "when barcode_id = '".$val->lot."' then 'GOUT'";
                        $where5 .= "'".$val->lot."',";
                        */
                        //update stock quant move id
                        $case6 .= "when quant_id = '".$val->quant_id."' then '".$sm_tj['move_id']."'";
                        $where6.= "'".$val->quant_id."',";

                        //update stock quant reserve_origin 
                        $case7 .= "when quant_id = '".$val->quant_id."' then '".$origin."'";
                        $where7.= "'".$val->quant_id."',";


                    }//end foreach

                    
                    //simpan stock move item
                    if(!empty($sql_stock_move_items_batch)){
                      $sql_stock_move_items_batch = rtrim($sql_stock_move_items_batch, ', ');
                      $this->_module->simpan_stock_move_items_batch($sql_stock_move_items_batch);
                    }
                    
                    //update status stock move items asal
                     $where = rtrim($where, ',');
                     $sql_update_stock_move_items  = "UPDATE stock_move_items SET status =(case ".$case." end), tanggal_transaksi = '".$tgl."' WHERE  move_id in (".$where.") ";
                     $this->_module->update_perbatch($sql_update_stock_move_items);

                    //update lokasi tbl stock quant
                     $where2 = rtrim($where2, ',');
                     $sql_update_stock_quant  = "UPDATE stock_quant SET lokasi =(case ".$case2." end) WHERE  quant_id in (".$where2.") ";
                     $this->_module->update_perbatch($sql_update_stock_quant);

                     $where6 = rtrim($where2, ',');
                     $sql_update_stock_quant_move_id  = "UPDATE stock_quant SET reserve_move =(case ".$case6." end) WHERE  quant_id in (".$where6.") ";
                     $this->_module->update_perbatch($sql_update_stock_quant_move_id);

                     $where7 = rtrim($where7, ',');
                     $sql_update_stock_quant_reserve_origin  = "UPDATE stock_quant SET reserve_origin =(case ".$case7." end), lokasi_fisik = ''  WHERE  quant_id in (".$where7.") ";
                     $this->_module->update_perbatch($sql_update_stock_quant_reserve_origin);

                    if(!empty($case3) AND !empty($where3)){
                      //update stock move penerimaan barang 
                      $where3 = rtrim($where3, ',');
                      $sql_update_stock_move  = "UPDATE stock_move SET status =(case ".$case3." end) WHERE  move_id in (".$where3.") ";
                      $this->_module->update_perbatch($sql_update_stock_move);

                      //update stock move produk penerimaan barang 
                      $where3 = rtrim($where3, ',');
                      $sql_update_stock_move_produk  = "UPDATE stock_move_produk SET status =(case ".$case3." end) WHERE  move_id in (".$where3.") ";
                      $this->_module->update_perbatch($sql_update_stock_move_produk);
                    }

                    if(!empty($case4) AND !empty($where4)){
                      //update penerimaan barang  
                       $where4 = rtrim($where4, ',');
                       $sql_update_penerimaan_barang  = "UPDATE penerimaan_barang SET status =(case ".$case4." end) WHERE  kode in (".$where4.") ";
                       $this->_module->update_perbatch($sql_update_penerimaan_barang);

                      //update penerimaan barang  items
                       $where4 = rtrim($where4, ',');
                       $sql_update_penerimaan_barang_items  = "UPDATE penerimaan_barang_items SET status_barang =(case ".$case4." end) WHERE  kode in (".$where4.") ";
                       $this->_module->update_perbatch($sql_update_penerimaan_barang_items); 
                    }
                    
                    $qty2   = $this->m_pengirimanBarang->get_qty2_by_kode($move_id)->row_array();

                    //update berat di mrp production
                    $sql_update_berat = "UPDATE mrp_production set berat = '".$qty2['jml_qty2']."' WHERE origin = '".$origin."' AND dept_id = 'DYE'";
                    $this->_module->update_perbatch($sql_update_berat);

                     /*
                     //update lokasi lot jadi GOUT di stock kain greige
                     $where5 = rtrim($where5, ',');
                     $sql_update_lokasi_lot  = "UPDATE stock_kain_greige SET kode_lokasi =(case ".$case5." end) WHERE  barcode_id in (".$where5.") ";
                     $this->_module->update_perbatch($sql_update_lokasi_lot);             
                    */
                     
                    $warehouse     = $deptid;
                    $method_dept   = $warehouse;
                    $method_action = 'OUT'; 

                    // Generate pengiriman barang
                    $kode_= $this->_module->get_kode_pengiriman($method_dept);
                    $get_kode_out= $kode_;

                    $dgt     =substr("00000" . $get_kode_out,-5);            
                    $kode_out = $method_dept."/".$method_action."/".date("y").  date("m").$dgt;
                    $out_row  = 1;
                    $backorder = false;
                    $delete    = false;

                    $sql_stock_move_batch        = "";
                    $sql_stock_move_produk_batch = "";
                         
                    $sql_out_batch        = "";
                    $sql_out_items_batch  = "";
                    $sql_log_history_out  = "";
                    $qty_back   = "";
                    $kode_prod_del = "";
                    $origin_prod   = "";

                    $last_move   = $this->_module->get_kode_stock_move();
                    $move_id     = "SM".$last_move; //Set kode stock_move

                    //foreach untuk ngebentuk back order atau tidak
                    $list  = $this->m_pengirimanBarang->get_list_pengiriman_barang_items($kode);
                    foreach ($list as $row) {
                        $kode_produk = $row->kode_produk;
                        $qty         = $row->qty;
                        //$origin_prod = $row->origin_prod;

                        $qty_smi = $this->_module->get_qty_stock_move_items_by_kode($move_id_out,addslashes($kode_produk))->row_array();

                        if($qty_smi['sum_qty']<$qty and !empty($qty_smi['sum_qty'])){//jika qty di stock_move_items kurang dari qty di pengiriman barang items
                            $origin_prod = $kode_produk.'_'.$out_row;
                            $backorder = true;
                            $qty_back = $qty-$qty_smi['sum_qty'];
                            //simpan ke pengiriman_barang_items
                            $sql_out_items_batch   .= "('".$kode_out."','".addslashes($row->kode_produk)."','".addslashes($row->nama_produk)."','".$qty_back."','".addslashes($row->uom)."','draft','".$out_row."',''), ";
                            //simpan ke stock move produk 
                            $sql_stock_move_produk_batch .= "('".$move_id."','".addslashes($row->kode_produk)."','".addslashes($row->nama_produk)."','".$qty_back."','".addslashes($row->uom)."','draft','".$out_row."',''), ";                          
                            $out_row++;
                        }

                        if(empty($qty_smi['sum_qty'])){//jika qty di stock_move_items tidak ada
                            $delete = true;
                            $kode_prod_del .="'".addslashes($kode_produk)."',";
                        }

                    }

                    if($backorder== true){                        
                        //get data di pengiriman barang 
                        $head  = $this->m_pengirimanBarang->get_data_by_code($kode);

                        $method        = $warehouse.'|'.$method_action;              
                        $lokasi_dari   = $head->lokasi_dari;
                        $lokasi_tujuan = $head->lokasi_tujuan;
                        $reff_notes_back = 'Back Order '.$kode.' '.$head->reff_note ;
                        $schedule_date  = $head->tanggal_jt;
                        $tgl  = date('Y-m-d H:i:s');
                        $type_created = $head->type_created;

                        //get source move by move id 
                        $sc_move = $this->_module->get_stock_move_by_move_id($move_id_out)->row_array();

                        //simpan ke stock move
                        $origin = $origin;
                        $mt = explode('|', $sc_move['method']);
                        $mt_sc_move = $mt[1];// example OUT 

                        if(empty($sc_move['source_move']) AND $mt_sc_move == 'OUT'){//ini buat yg tidak ada MO/produce misal GDB|IN langsung GDB|OUT
                          $source_move = $sc_move['move_id'];
                        }else{
                          $source_move = $sc_move['move_id']."|".$sc_move['source_move'];
                        }

                        $sql_stock_move_batch .= "('".$move_id."','".$tgl."','".$origin."','".$method."','".$lokasi_dari."','".$lokasi_tujuan."','draft','1','".$source_move."'), ";          


                        $reff_picking_out = $head->reff_picking;
                        $sql_out_batch   .= "('".$kode_out."','".$tgl."','".$tgl."','".$schedule_date."','".addslashes($reff_notes_back)."','draft','".$method_dept."','".$origin."','".$move_id."','".$reff_picking_out."','".$lokasi_dari."','".$lokasi_tujuan."','".$type_created."'), ";  

                         //get mms kode berdasarkan dept_id
                         $mms = $this->_module->get_kode_sub_menu_deptid('pengirimanbarang',$method_dept)->row_array();
                         if(!empty($mms['kode'])){
                             $mms_kode = $mms['kode'];
                         }else{
                             $mms_kode = '';
                         }

                        //create log history pengiriman_barang
                        $note_log = $kode_out.'|'.$origin;
                        $date_log = date('Y-m-d H:i:s');
                        $sql_log_history_out .= "('".$date_log."','".$mms_kode."','".$kode_out."','create','".$note_log."','".$nama_user."'), ";

                        if(!empty($sql_stock_move_batch)){
                            $sql_stock_move_batch = rtrim($sql_stock_move_batch, ', ');
                            $this->_module->create_stock_move_batch($sql_stock_move_batch);

                            $sql_stock_move_produk_batch = rtrim($sql_stock_move_produk_batch, ', ');
                            $this->_module->create_stock_move_produk_batch($sql_stock_move_produk_batch);
                        }

                        if(!empty($sql_out_batch)){
                            $sql_out_batch = rtrim($sql_out_batch, ', ');
                            $this->_module->simpan_pengiriman_add_manual($sql_out_batch);

                            $sql_out_items_batch = rtrim($sql_out_items_batch, ', ');
                            $this->_module->simpan_pengiriman_items_batch($sql_out_items_batch);   

                            $sql_log_history_out = rtrim($sql_log_history_out, ', ');
                            $this->_module->simpan_log_history_batch($sql_log_history_out);          
                        }

                        //get source move by move_id_out
                        $sc_move = $this->_module->get_stock_move_tujuan($move_id_out,$origin,'done','cancel')->row_array();
                        //update source_move backorder
                        $source_move = $sc_move['source_move'].'|'.$move_id;
                        $sql_update_source_move =  "UPDATE stock_move set source_move = '$source_move' WHERE move_id = '$sm_tj[move_id]' ";
                        $this->_module->update_perbatch($sql_update_source_move);

                        //cek status penerimaan barang tujuan selain done atau cancel
                        $cek_sm = $this->_module->get_move_id_by_source_move($sm_tj['move_id'],'done','cancel')->row_array();
                        if(!empty($cek_sm['move_id'])){
                          $kode_in = $this->_module->get_kode_penerimaan_barang_by_move_id($cek_sm['move_id'])->row_array();

                          //update reff picking pengiriman_barang
                          $reff_picking_out_baru = $kode_out.'|'.$kode_in['kode'];                                                    
                          $sql_update_reff_picking_out = "UPDATE pengiriman_barang SET reff_picking = '$reff_picking_out_baru' WHERE kode = '$kode_out'";
                          $this->_module->update_perbatch($sql_update_reff_picking_out);  
                          //get reff_picking_penerimaan by kode_In['kode']
                          $reff_pick_in  = $this->_module->get_reff_picking_penerimaan_barang_by_kode($kode_in['kode'])->row_array();

                          //cek reff_picking penerimaan barang apa mengandung kata reff_picking_in['reff_picking']?
                          $cek_reff = $this->_module->cek_reff_picking_penerimaan_barang_by_kode($kode_in['kode'],$kode)->row_array(); 
                          if(empty($cek_reff['reff_picking'])){//jika reff_picking penerimaan barang kosong
                            //update reff_picking penerimaan_barang
                            $reff_picking_in_baru = $kode.' '.$reff_pick_in['reff_picking']; 
                            $sql_update_reff_picking_in = "UPDATE penerimaan_barang SET reff_picking = '$reff_picking_in_baru' WHERE kode = '$kode_in[kode]'";
                            $this->_module->update_perbatch($sql_update_reff_picking_in);   
                          }
                         
                        }

                    }
                    
                    if($delete == true){
                        $kode_prod_del = rtrim($kode_prod_del, ',');
                        $sql_delete_pengiriman_brg_items = "DELETE  FROM pengiriman_barang_items WHERE kode_produk IN (".$kode_prod_del.") AND kode = '".$kode."'";
                        $this->_module->update_perbatch($sql_delete_pengiriman_brg_items);

                        $sql_delete_stock_move_produk = "DELETE  FROM stock_move_produk WHERE kode_produk IN (".$kode_prod_del.") AND move_id = '".$move_id_out."'";
                        $this->_module->update_perbatch($sql_delete_stock_move_produk);
                    }

                    //unlock table
                    $this->_module->unlock_tabel();
                    
                    $jenis_log   = "done";
                    $note_log    = "Kirim Data Barang";
                    $this->_module->gen_history_deptid($sub_menu, $kode, $jenis_log, $note_log, $username,$deptid);
                    if($backorder == true){
                        $callback = array('status' => 'success', 'message'=>'Data Berhasil Terkirim !', 'icon' => 'fa fa-check', 'type'=>'success', 'backorder' => 'yes', 'message2'=> 'Akan terbentuk Backorder dengan No '.$kode_out);
                    }else{
                        $callback = array('status' => 'success', 'message'=>'Data Berhasil Terkirim !', 'icon' => 'fa fa-check', 'type'=>'success');

                    }              

            }//else cek stock move items

        }//else session

        echo json_encode($callback);
    }

   
    public function cek_stok()
    {
        $sub_menu = $this->uri->segment(2);
        $username = addslashes($this->session->userdata('username')); 
        $deptid   = $this->input->post('deptid');

        if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
            // session habis
            $callback = array('message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
        }else{

            $kode       = $this->input->post('kode');
            $move_id    = $this->input->post('move_id');
            $origin     = $this->input->post('origin');
            $status_brg = 'ready';
            $tgl        = date('Y-m-d H:i:s');
            $sql_stock_quant_batch      = "";
            $sql_stock_move_items_batch = "";
            $case ="";
            $where="";
            $case2 ="";
            $where2="";
            $case3 ="";
            $where3 ="";
            $case4 ="";
            $where4="";
            $where4_2="";
            $case5 ="";
            $where5 ="";
            $where_del1 ="";
            $where_del2 ="";

            $kurang = false;
            $produk_kurang = "";
            $kosong = true;
            $produk_kosong = ""; 
            $cukup  = false;          
            $produk_terpenuhi = "";
            $history = false;
            $history_split = false;
            $qty2_new = "";
            $qty2_update = "";
            $case_qty2 = "";
            $where_move_items = "";

           //cek status terkirim ?
            $cek_kirim  = $this->m_pengirimanBarang->cek_status_barang($kode)->row_array();
            if($cek_kirim['status'] == 'done'){
                $callback = array('status' => 'failed', 'message'=>'Maaf, Tidak Bisa Cek Stok, Data Sudah Terkirim !', 'icon' => 'fa fa-warning', 'type'=>'danger');
            }else if($cek_kirim['status'] == 'cancel'){
                $callback = array('status' => 'failed', 'message'=>'Maaf, Tidak Bisa Cek Stok, Data Pengiriman Sudah Dibatalkan !', 'icon' => 'fa fa-warning', 'type'=>'danger');
            }else{            
                    //lock tabel
                    $this->_module->lock_tabel('stock_quant WRITE, stock_move_items WRITE,stock_move WRITE,stock_move_produk WRITE, pengiriman_barang WRITE, pengiriman_barang_items WRITE, departemen WRITE, mrp_production_rm_target WRITE' );

                    //get row order stock_move_items
                    $row_order  = $this->_module->get_row_order_stock_move_items_by_kode($move_id);
                    //lokasi tujuan get_location_by_move_id
                    $lokasi = $this->m_pengirimanBarang->get_location_by_move_id($move_id)->row_array();
                               
                    $list  = $this->m_pengirimanBarang->get_list_pengiriman_barang_items($kode);
                    foreach ($list as $val) {
                        $kode_produk = $val->kode_produk;
                        $nama_produk = $val->nama_produk;
                        $qty         = $val->qty;
                        $uom         = $val->uom;
                        $ro_items    = $val->row_order;
                        $origin_prod = $val->origin_prod;                      

                        //get last quant id
                        $start = $this->m_pengirimanBarang->get_last_quant_id();

                        //cek qty produk di stock_move_items apa masih kurang dengan target qty di pengiriman barang items
                        $qty_smi = $this->_module->get_qty_stock_move_items_by_kode($move_id,addslashes($kode_produk))->row_array();
                        $kebutuhan_qty  = $qty - $qty_smi['sum_qty'];

                        if($kebutuhan_qty > 0){

                          $cek_quant = $this->_module->get_cek_stok_quant_by_prod(addslashes($kode_produk),$lokasi['lokasi_dari'],$origin,$deptid)->result_array();

                          foreach ($cek_quant as $stock) {  

                              $kosong = false;  
                              $history = true;                             

                                //untuk mendapatkan origin_prod yang terdapat consume kedepannya atau consume lebih dari 1
                                $loop_sm    = true;
                                $loop_count = 1;
                                $origin_prod_tj = "";
                                $next       = false;
                                $con_next   = false;
                                $con        = false;

                                //origin_prod nya berdasarkan di pengiriman barang items
                                /*
                                //get list stock_move by origin
                                $list_sm = $this->_module->get_list_stock_move_origin($origin)->result_array();
                                foreach ($list_sm as $row) {
                                       
                                    $mt = explode("|", $row['method']);
                                    $ex_deptid = $mt[0];
                                    $ex_mt     = $mt[1];

                                    if($loop_sm == true){

                                        if($ex_mt == 'CON' AND $con_next == true){

                                            //get  origin_prod by move id, kode_produk
                                            $get_origin_prod = $this->m_pengirimanBarang->get_origin_prod_mrp_production_by_kode($row['move_id'],addslashes($kode_produk))->row_array();
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
                                    $origin_prod = $origin_prod_tj; // origin prod berdasarkan 
                                }else{
                                    $origin_prod = '';
                                }
                                */


                                if(round($kebutuhan_qty,2) >= round($stock['qty'],2)){//jika kebutuhan_qty lebih atau sama dengan qty  di stock_quant

                                  //update reserve_move dengan move_id
                                  $case2  .= "when quant_id = '".$stock['quant_id']."' then '".$move_id."'";
                                  $where2 .= "'".$stock['quant_id']."',"; 
                                  
                                  //insert  stock move items batch
                                  $sql_stock_move_items_batch .= "('".$move_id."', '".$stock['quant_id']."','".addslashes($kode_produk)."', '".addslashes($nama_produk)."','".addslashes($stock['lot'])."','".$stock['qty']."','".addslashes($uom)."','".$stock['qty2']."','".addslashes($stock['uom2'])."','".$status_brg."','".$row_order."','".addslashes($origin_prod)."', '".$tgl."'), ";                                      
                                  $row_order++;                                 
                                  $kebutuhan_qty = round($kebutuhan_qty,2) - round($stock['qty'],2);
                               
                                }else if(round($kebutuhan_qty,2) < round($stock['qty'],2)){//jika kebutuhan_qty kurang dari qty di stock_quant

                                  $qty_new = round($stock['qty'],2) - round($kebutuhan_qty,2);//qty baru di stock quant

                                  //update qty produk di stock_quant
                                  $case  .= "when quant_id = '".$stock['quant_id']."' then '".$qty_new."'";
                                  $where .= "'".$stock['quant_id']."',";

                                  $qty2_new = ($stock['qty2']/$stock['qty'])*$kebutuhan_qty;
                                  $qty2_update  = $stock['qty2'] - $qty2_new;
                                  $case_qty2 .= "when quant_id = '".$stock['quant_id']."' then '".$qty2_update."'";
                                  //$where_move_items .= "'".$stock['move_id']."',";

                                  //insert stock_quant_batch
                                  $sql_stock_quant_batch .= "('".$start."','".$tgl."', '".addslashes($kode_produk)."', '".addslashes($nama_produk)."','".addslashes($stock['lot'])."','".addslashes($stock['nama_grade'])."','".$kebutuhan_qty."','".addslashes($uom)."','".$qty2_new."','".addslashes($stock['uom2'])."','".$lokasi['lokasi_dari']."','".addslashes($stock['reff_note'])."','".$move_id."','".$stock['reserve_origin']."'), ";
                                  //insert  stock move items batch
                                  $sql_stock_move_items_batch .= "('".$move_id."', '".$start."','".addslashes($kode_produk)."', '".addslashes($nama_produk)."','".addslashes($stock['lot'])."','".$kebutuhan_qty."','".addslashes($uom)."','".$qty2_new."','".addslashes($stock['uom2'])."','".$status_brg."','".$row_order."','".addslashes($origin_prod)."', '".$tgl."'), ";
                                  $row_order++;
                                  $start++;
                                  $kebutuhan_qty = 0;

                                }                         
                           
                                //update status di pengiriman_barang_items dan stock_move_produk jadi ready
                                $case3  .= "when kode_produk = '".addslashes($kode_produk)."' then '".$status_brg."'";
                                $where3 .= "'".addslashes($kode_produk)."',";
                                //untuk memotong proses looping ketika kebutuhan_qty == 0
                                if($kebutuhan_qty == 0){
                                  break;
                                }

                          }//end foreach cek_quant

                            if($kebutuhan_qty > 0){
                              $kurang    = true;
                              $produk_kurang .= $nama_produk.', ';
                            }
                            if($kosong == true){//jika qty di stock_quant_kosong/blm terisi
                               $produk_kosong .= $nama_produk.', ';
                            }

                        }else{

                          if($kebutuhan_qty < 0){

                              // get quant id by origin_prod , move_id, status = ready
                              $sq = $this->m_pengirimanBarang->get_smi_produk_out_by_kode($move_id, $kode_produk)->result_array();
                              $qty_lebih = $qty_smi['sum_qty'] - $qty; // qty lebih dari yg dibutuhkan

                              foreach ($sq as $val) {

                                    $history_split = true;

                                    if(round($val['qty'],2) > round($qty_lebih,2)){ // jika qty di smi lebih dari qty_lebih 

                                        $qty_new   = round($val['qty'],2) - round($qty_lebih,2); // untuk qty baru di smi dan stock_quant

                                        // update qty  stock_move item by move_id, quant_id
                                        $case4  .= "when quant_id = '".$val['quant_id']."' then '".$qty_new."'";
                                        $where4 .= "'".$val['quant_id']."',";
                                        $where4_2 .= "'".$move_id."',";

                                        //update qty produk di stock_quant
                                        $case  .= "when quant_id = '".$val['quant_id']."' then '".$qty_new."'";
                                        $where .= "'".$val['quant_id']."',";

                                        $cek_sq  = $this->_module->get_stock_quant_by_id($val['quant_id'])->row_array();
                                        $qty2_new = ($val['qty2']/$val['qty'])*$qty_lebih;

                                        //update qty2 di stock_quant lama dan stock_move_items
                                        $qty2_update = $val['qty2'] - $qty2_new;
                                        $case_qty2 = "when quant_id = '".$val['quant_id']."' then '".$qty2_update."'";

                                        //insert qty stock_quant_batch dengan quant_id baru 
                                        $sql_stock_quant_batch .= "('".$start."','".$tgl."', '".addslashes($cek_sq['kode_produk'])."', '".addslashes($cek_sq['nama_produk'])."','".addslashes(trim($cek_sq['lot']))."','".addslashes($cek_sq['nama_grade'])."','".$qty_lebih."','".addslashes($cek_sq['uom'])."','".$qty2_new."','".addslashes($cek_sq['uom2'])."','".$cek_sq['lokasi']."','".addslashes($cek_sq['reff_note'])."','','".$cek_sq['reserve_origin']."'), ";
                                        $start++;

                                        $qty_lebih = 0;

                                    }else if(round($val['qty'],2) <= round($qty_lebih,2)){ // jika qty smi kurang dari qty_lebih

                                        $qty_lebih = round($qty_lebih,2) - round($val['qty'],2);
                                        
                                        // reserve_move jadi kosong di tbl stock_quant
                                        $case5  .= "when quant_id = '".$val['quant_id']."' then ''";
                                        $where5 .= "'".$val['quant_id']."',";

                                        // hapus stock_move_items by move_id, quant_id
                                        $where_del1 .= "'".$val['move_id']."',"; // move_id
                                        $where_del2 .= "'".$val['quant_id']."',"; // quant_id

                                    }

                                    if($qty_lebih == 0){
                                        break; // keluar looping
                                    }
                                    
                                }
                             
                          }else{

                            $cukup = true;
                            $produk_terpenuhi .= $nama_produk.', ';
                          }


                        }//end if jika kebutuhan_qty <= 0


                        //* insert dan update data 
                        if(!empty($sql_stock_quant_batch)){
                          $sql_stock_quant_batch = rtrim($sql_stock_quant_batch, ', ');
                          $this->_module->simpan_stock_quant_batch($sql_stock_quant_batch);
                          $sql_stock_quant_batch      = "";
     
                        }

                        if(!empty($sql_stock_move_items_batch)){
                          $sql_stock_move_items_batch = rtrim($sql_stock_move_items_batch, ', ');
                          $this->_module->simpan_stock_move_items_batch($sql_stock_move_items_batch);
                         $sql_stock_move_items_batch = "";

                        }

                        //update reserve_move di stock_quant
                        if(!empty($where2) AND !empty($case2)){
                          $where2 = rtrim($where2, ',');
                          $sql_update_reserve_move  = "UPDATE stock_quant SET reserve_move =(case ".$case2." end) WHERE  quant_id in (".$where2.") ";
                          $this->_module->update_perbatch($sql_update_reserve_move);

                          $sql_update_reserve_move = "";
                          $where2 = "";
                          $case2  = "";
                        }

                        //update qty baru di stock quant 
                        if(!empty($where) AND !empty($case)){
                          $where = rtrim($where, ',');
                          $sql_update_qty_stock  = "UPDATE stock_quant SET qty =(case ".$case." end), qty2 =(case ".$case_qty2." end) WHERE  quant_id in (".$where.") ";
                          $this->_module->update_perbatch($sql_update_qty_stock);

                          $sql_update_qty_stock = "";
                          $where  = "";
                          $case   = "";
                        }

                        if(!empty($where3) AND !empty($case3)){
                          $where3 = rtrim($where3, ',');
                          $sql_update_status_pengiriman_items = "UPDATE pengiriman_barang_items SET status_barang =(case ".$case3." end) WHERE  kode_produk in (".$where3.") AND kode = '".$kode."' ";
                          $this->_module->update_perbatch($sql_update_status_pengiriman_items);

                          $sql_update_status_stock_move_produk = "UPDATE stock_move_produk SET status =(case ".$case3." end) WHERE  kode_produk in (".$where3.") AND move_id = '".$move_id."' ";
                          $this->_module->update_perbatch($sql_update_status_stock_move_produk);

                          $sql_update_status_pengiriman_items = "";
                          $sql_update_stock_move_produk = "";
                          $case3  = "";
                          $where3 = "";

                        }


                        //update qty dan qty2 di stock_move_items
                        if(!empty($where4) AND !empty($case4)){
                            $where4 = rtrim($where4, ',');
                            $where4_2 = rtrim($where4_2, ',');
                            $sql_update_qty_smi = "UPDATE stock_move_items set qty = (case ".$case4." end), qty2 = (case ".$case_qty2." end) WHERE quant_id IN (".$where4.") AND move_id IN (".$where4_2.") ";
                            $this->_module->update_perbatch($sql_update_qty_smi);
                        }

                        // update reserve_move stock_quant
                        if(!empty($where5) AND !empty($case5)){
                            $where5  = rtrim($where5, ', ');
                            $sql_update_reserve_move  = "UPDATE stock_quant SET reserve_move =(case ".$case5." end) WHERE  quant_id in (".$where5.") ";
                            $this->_module->update_perbatch($sql_update_reserve_move);
                        }

                        // delete stock_move_items
                        if(!empty($where_del1) AND !empty($where_del2)){
                            $where_del1 = rtrim($where_del1, ',');
                            $where_del2 = rtrim($where_del2, ',');

                            $sql_delete_smi = "DELETE FROM stock_move_items WHERE move_id IN (".$where_del1.") AND quant_id IN (".$where_del2.") ";
                            $this->_module->update_perbatch($sql_delete_smi);
                        }

                        
                    }//end foreach penerimaan barang items                  
                    
                      //cek apa ada items yang status nya masih draft? 
                      $all_produk_items = $this->m_pengirimanBarang->cek_status_barang_pengiriman_barang_items($kode,'draft')->row_array();

                      //jika tidak kosong maka update status di pengiriman brg
                      if(empty($all_produk_items['status_barang'])){
                        $this->m_pengirimanBarang->update_status_pengiriman_barang($kode,$status_brg);
                      }

                      $cek_status2 = $this->m_pengirimanBarang->cek_status_pengiriman_barang($kode)->row_array();
                      if($cek_status2['status']=='ready'){
                            $this->_module->update_status_stock_move($move_id,$status_brg);
                      }         
                    

                    //unlock table
                    $this->_module->unlock_tabel();
                                        
                    if(!empty($produk_kosong)){
                        $produk_kosong = rtrim($produk_kosong, ', ');
                        $callback = array('status' => 'failed', 'message'=> 
                            'Maaf, Qty Product "'.  $produk_kosong  .'" Kosong !', 'icon' => 'fa fa-warning', 'type'=>'danger');

                    }else if(!empty($produk_kurang)){           
                        $produk_kurang = rtrim($produk_kurang, ', ');
                        $callback = array('status' => 'failed', 'message'=> 
                            'Maaf, Qty Product "'.  $produk_kurang  .'" tidak mencukupi !', 'icon' => 'fa fa-warning', 'type'=>'danger', 'status_kurang' => 'yes',  'message2'=>'Detail Product Berhasil Ditambahkan !', 'icon2' => 'fa fa-check', 'type2'=>'success');                       
                                            
                    /*            
                    }else if(!empty($produk_terpenuhi)){
                        $produk_terpenuhi = rtrim($produk_terpenuhi, ', ');
                        $callback
                        //$callback = array('status' => 'failed', 'message'=> 'Qty Product "'.  $produk_terpenuhi  .'" Sudah Terpenuhi !', 'icon' => 'fa fa-warning', 'type'=>'danger');
                    */
                    }else{

                        if(!empty($produk_terpenuhi)){
                          $callback = array('status' => 'success', 'message'=>'Detail Product Berhasil Ditambahkan !', 'icon' => 'fa fa-check', 'type'=>'success', 'terpenuhi'=>'yes');   
                        }else{
                          $callback = array('status' => 'success', 'message'=>'Detail Product Berhasil Ditambahkan !', 'icon' => 'fa fa-check', 'type'=>'success');  
                           
                        }
                    }


                    if($history == true OR $history_split == true){
                      $jenis_log   = "edit";
                      $note_log    = '';
                      if($history == true){
                        $note_log    = "Cek Stok";
                      }else if($history_split == true){
                        $note_log    = "Cek Stok Split";
                      }else if($history == true AND $history_split == true){
                        $note_log  = "Cek Stok dan Cek Stok Split";
                      }

                      $this->_module->gen_history_deptid($sub_menu, $kode, $jenis_log, $note_log, $username, $deptid);
                    }
               
            }// end if  cek status kirim

        }// end if cek session 

        echo json_encode($callback);

    }


    function print_pengiriman_barang()
    {

        $this->load->library('Pdf');//load library pdf

        $dept_id  = $this->input->get('departemen');
        $kode     = $this->input->get('kode');
        
        $origin              = '';
        $tanggal             = '';
        $reff_picking        = '';
        $tanggal_transaksi   = '';
        $tanggal_jt           = '';

		    $dept    = $this->_module->get_nama_dept_by_kode($dept_id)->row_array();
        $head    = $this->m_pengirimanBarang->get_data_by_code_print($kode,$dept_id);
        
        if(!empty($head)){
          $kode     = $head->kode;
          $origin   = $head->origin;
          $tanggal  = $head->tanggal;
          $reff_picking      = $head->reff_picking;
          $tanggal_transaksi = $head->tanggal_transaksi;
          $tanggal_jt        = $head->tanggal_jt;
        }

			  $nama_dept = strtoupper($dept['nama']);
			  $pdf = new PDF_Code128('P','mm','A4');
	      //$pdf = new PDF_Code128('l','mm',array(210,148.5));

	  		$pdf->SetMargins(0,0,0);
	      $pdf->SetAutoPageBreak(False);
	      $pdf->AddPage();
	      $pdf->setTitle('Pengiriman Barang : '.$nama_dept);

	      $pdf->SetFont('Arial','B',9,'C');
	      $pdf->Cell(0,10,'PENGIRIMAN BARANG '.$nama_dept,0,0,'C');

        $pdf->SetFont('Arial','',7,'C');

		    $pdf->setXY(160,3);
        $tgl_now = tgl_indo(date('d-m-Y H:i:s'));
        $pdf->Multicell(50,4, 'Tgl.Cetak : '.$tgl_now, 0,'C');

        $pdf->SetFont('Arial','B',8,'C');

         // caption kiri
        $pdf->setXY(5,10);
        $pdf->Multicell(15,4,'Kode ',0,'L');

        $pdf->setXY(5,13);
        $pdf->Multicell(15,4,'Tgl.buat ',0,'L');

        $pdf->setXY(5,16);
        $pdf->Multicell(15,4,'Origin ',0,'L');

        $pdf->setXY(19, 10);
        $pdf->Multicell(5, 4, ':', 0, 'L');
        $pdf->setXY(19, 13);
        $pdf->Multicell(5, 4, ':', 0, 'L');
        $pdf->setXY(19, 16);
        $pdf->Multicell(5, 4, ':', 0, 'L');
          
         // isi kiri
         $pdf->SetFont('Arial','',8,'C');

         $pdf->setXY(20,10);
         $pdf->Multicell(40,4,$kode,0,'L');
         $pdf->setXY(20,13);
         $pdf->Multicell(40,4,$tanggal,0,'L');
         $pdf->setXY(20,16);
         $pdf->Multicell(70,4,$origin,0,'L');

         $pdf->SetFont('Arial','B',8,'C');
        // caption tengah
        $pdf->setXY(60,10);
        $pdf->Multicell(25,4,'Reff Picking ',0,'L');
        $pdf->setXY(60,13);
        $pdf->Multicell(25,4,'Tgl.kirim ',0,'L');
        $pdf->setXY(60,16);
        $pdf->Multicell(25,4,'Tgl.Jatuh Tempo ',0,'L');

        $pdf->setXY(85, 10);
        $pdf->Multicell(5, 4, ':', 0, 'L');
        $pdf->setXY(85, 13);
        $pdf->Multicell(5, 4, ':', 0, 'L');
        $pdf->setXY(85, 16);
        $pdf->Multicell(5, 4, ':', 0, 'L');
          
         // isi tengah
         $pdf->SetFont('Arial','',8,'C');

         $pdf->setXY(86,10);
         $pdf->Multicell(60,4,$reff_picking,0,'L');
         $pdf->setXY(86,13);
         $pdf->Multicell(40,4,$tanggal_transaksi,0,'L');
         $pdf->setXY(86,16);
         $pdf->Multicell(70,4,$tanggal_jt,0,'L');
     

        // header table product
        $pdf->SetFont('Arial','B',8,'C');
        $pdf->setXY(5,23);
        $pdf->Multicell(52,4,'Produk',0,'L');

        $pdf->setXY(5,27);
        $pdf->Cell(7, 5, 'No.', 1, 0, 'L');
        $pdf->Cell(20, 5, 'Kode Produk', 1, 0, 'C');
        $pdf->Cell(70, 5, 'Nama Produk', 1, 0, 'C');
        $pdf->Cell(25, 5, 'Qty', 1, 0, 'R');
        $pdf->Cell(10, 5, 'Uom', 1, 0, 'C');
        $pdf->Cell(18, 5, 'Tersedia', 1, 0, 'C');

        
        // products
        $items = $this->m_pengirimanBarang->get_list_pengiriman_barang_print($kode,$dept_id);
        $x    = 5;
        $y    = 32;
        $no   = 1;
        foreach($items as $row){
          
          // set font tbody =
          $pdf->SetFont('Arial','',8,'C');

            $pdf->setXY($x, $y);
            $pdf->Multicell(7, 5, $no, 1,'L');
            $pdf->setXY($x+7, $y);
            $pdf->Multicell(20, 5, $this->custom_char_out($row->kode_produk,8), 1,'L');
            $pdf->setXY($x+27, $y);
            $pdf->Multicell(70, 5, $this->custom_char_out($row->nama_produk,45), 1,'L');
            $pdf->setXY($x+97, $y);
            $pdf->Multicell(25, 5, number_format($row->qty,2), 1,'R');
            $pdf->setXY($x+122, $y);
            $pdf->Multicell(10, 5, $this->custom_char_out($row->uom,3), 1,'L');
            $pdf->setXY($x+132, $y);
            $pdf->Multicell(18, 5, number_format($row->sum_qty,2), 1,'R');
            
            $no++;
            $y = $y + 5;

            if($y>290 ){
	            $pdf->AddPage();
              $y = 7;
              $pdf->SetFont('Arial','',7,'C');
              $pdf->setXY(160,3);
              $tgl_now = tgl_indo(date('d-m-Y H:i:s'));
              $pdf->Multicell(50,4, 'Tgl.Cetak : '.$tgl_now, 0,'C');
              
            }
        }

        $y = $y+5;

        // header table details
        $pdf->SetFont('Arial','B',8,'C');
        $pdf->setXY(5,$y);
        $pdf->Multicell(52,4,'Detail Produk',0,'L');

        $pdf->setXY(5,$y+5);
        $pdf->Cell(7, 5, 'No.', 1, 0, 'L');
        $pdf->Cell(20, 5, 'Kode Produk', 1, 0, 'C');
        $pdf->Cell(70, 5, 'Nama Produk', 1, 0, 'C');
        $pdf->Cell(30, 5, 'Lot', 1, 0, 'C');
        $pdf->Cell(15, 5, 'Qty', 1, 0, 'R');
        $pdf->Cell(10, 5, 'Uom', 1, 0, 'L');
        $pdf->Cell(10, 5, 'Qty2', 1, 0, 'R');
        $pdf->Cell(10, 5, 'Uom2', 1, 0, 'L');
        $pdf->Cell(28, 5, 'Reff Note', 1, 0, 'C');

        // details
        $smi  = $this->m_pengirimanBarang->get_stock_move_items_by_kode_print($kode,$dept_id);
        $x    = 5;
        $y    = $y+10;
        $no   = 1;
        foreach($smi as $row){

            // set font tbody 
            $pdf->SetFont('Arial','',8,'C');
            
            $pdf->setXY($x, $y);
            $pdf->Multicell(7, 5, $no, 1,'L');
            $pdf->setXY($x+7, $y);
            $pdf->Multicell(20, 5, $this->custom_char_out($row->kode_produk,8), 1,'L');
            $pdf->setXY($x+27, $y);
            $pdf->Multicell(70, 5,  $this->custom_char_out($row->nama_produk,45), 1,'L');
            $pdf->setXY($x+97, $y);
            $pdf->Multicell(30, 5, $row->lot, 1,'L');
            $pdf->setXY($x+127, $y);
            $pdf->Multicell(15, 5, number_format($row->qty,2), 1,'R');
            $pdf->setXY($x+142, $y);
            $pdf->Multicell(10, 5, $row->uom, 1,'L');
            $pdf->setXY($x+152, $y);
            $pdf->Multicell(10, 5, round($row->qty2,2), 1,'R');
            $pdf->setXY($x+162, $y);
            $pdf->Multicell(10, 5, $row->uom2, 1,'L');
            $pdf->setXY($x+172, $y);
            $pdf->Multicell(28, 5, $this->custom_char_out($row->reff_note,10), 1,'L');
            
            $no++;
            $y=$y+5;

            if($y>290 ){
	            $pdf->AddPage();
              $y = 7;
              $pdf->SetFont('Arial','',7,'C');
              $pdf->setXY(160,3);
              $tgl_now = tgl_indo(date('d-m-Y H:i:s'));
              $pdf->Multicell(50,4, 'Tgl.Cetak : '.$tgl_now, 0,'C');
              
            }

        }

	      $pdf->Output();

    }


    function custom_char_out($string, $length)
    {
      if(strlen($string) <= $length){
        return $string;
      }
      return substr($string, 0, $length). ' ...';
    }
    
}


?>