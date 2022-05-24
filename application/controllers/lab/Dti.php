<?php defined('BASEPATH') or exit ('No Direct Script Acces Allowed');

/**
 * 
 */
class Dti extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->is_loggedin();//cek apakah user sudah login
		$this->load->model("m_lab");//load model m_lab
		$this->load->model("_module");
	}

	public function index()
	{
		$data['id_dept']='DTI';
		$this->load->view('lab/v_dti', $data);
	}

	  function get_data()
    {
        $sub_menu  = $this->uri->segment(2);
        $kode = $this->_module->get_kode_sub_menu($sub_menu)->row_array();
        $list = $this->m_lab->get_datatables($kode['kode']);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $field) {
            $kode_encrypt = encrypt_url($field->id);
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = '<a href="'.base_url('lab/dti/edit/'.$kode_encrypt).'">'.$field->nama_warna.'</a>';
            $row[] = $field->tanggal;
            $row[] = $field->nama_status;
            $row[] = $field->notes;
 
            $data[] = $row;
        }
 
        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_lab->count_all($kode['kode']),
            "recordsFiltered" => $this->m_lab->count_filtered($kode['kode']),
            "data" => $data,
        );
        //output dalam format JSON
        echo json_encode($output);
    }

  public function add()
  {	
    $data['id_dept']  ='DTI';
    $id_warna         = $this->input->get('id_warna');
    $duplicate        = $this->input->get('duplicate');
    if($duplicate == 'true'){
      $color            = $this->m_lab->get_data_color_by_code($id_warna);
      $data['dyest']    = $this->m_lab->get_data_dye_aux_by_code($id_warna,'DYE');
      $data['aux']      = $this->m_lab->get_data_dye_aux_by_code($id_warna,'AUX');
      $data['color']    = $color;
      if(empty($color)){
        show_404();
      }else{
        $data['row_order'] = 1;
        return $this->load->view('lab/v_dti_duplicate', $data);
      }
    }else{
      return $this->load->view('lab/v_dti_add', $data);
    }
  }

  public function simpan()
	{
    //$warna_encr="";
    
    if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
      // session habis
      $callback = array('message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
    }else{

      $sub_menu   = $this->uri->segment(2);
      $username   = $this->session->userdata('username'); 

      $warna      = addslashes($this->input->post('warna'));
			$id         = $this->input->post('id');
			$tanggal    = $this->input->post('tanggal');
      $notes      = addslashes($this->input->post('note'));
      $kode_warna = addslashes($this->input->post('kode_warna'));
			$status     = addslashes($this->input->post('status'));
      $status2    = $this->input->post('status2');// status head draft/requested

			     if(empty($warna) AND empty($id)){
                $callback = array('status' => 'failed', 'field' => 'warna', 'message' => 'Warna Harus Diisi !'.$id, 'icon' =>'fa fa-warning', 
                  'type' => 'danger'  );    
          	}else if(empty($notes)){
              $callback = array('status' => 'failed', 'field' => 'note', 'message' => 'Notes Harus Diisi !', 'icon' =>'fa fa-warning', 
                  'type' => 'danger'  );    
          	}else{
            	//cek warna apa sudah ada apa belum
            	$cek = $this->m_lab->cek_color_by_color($warna)->row_array();
              if(!empty($cek['nama_warna']) AND $status == 'tambah'){
                  $callback = array('status' => 'failed', 'field' => 'note', 'message' => 'Warna Sudah Pernah Diinput !', 'icon' =>'fa fa-warning', 'type' => 'danger'  );    

              }else if(!empty($cek['nama_warna'])){
              		//update notes
                  $this->m_lab->update_color($id,$notes,$kode_warna);
                  $jenis_log   = "edit";
                  $note_log    = $warna." | ".$notes." | ".$kode_warna;
                  $this->_module->gen_history($sub_menu, $id, $jenis_log, $note_log, $username);
                  $callback = array('status' => 'success', 'message' => 'Data Berhasil Disimpan !', 'icon' =>'fa fa-check', 'type' => 'success');
                  
             	}else{
                
                if($status2 =='requested'){
                  $status_head = $status2; 
                }else{
                  $status_head = 'draft'; 
                }

                // lock tabel
                $this->_module->lock_tabel('warna WRITE');

                $last_id = $this->m_lab->get_last_id_warna();

          			//insert warna
          			$this->m_lab->save_color($warna,$tanggal,$notes,$status_head,$kode_warna);

                // unlock warna
                $this->_module->unlock_tabel();

           			$id_encr     = encrypt_url($last_id);
                $jenis_log   = "create";
                $note_log    = $warna." | ".$notes." | ".$kode_warna;
                $this->_module->gen_history($sub_menu, $last_id, $jenis_log, $note_log, $username);
                $callback = array('status' => 'success', 'message' => 'Data Berhasil Disimpan !', 'isi' => $id_encr, 'icon' =>'fa fa-check', 'type' => 'success');
                
          		}

          	}
		}

		echo json_encode($callback);
	}

	public function edit($id = null)
	{
		if(!isset($id)) show_404();
        $kode_decrypt  = decrypt_url($id);
        $data['id_dept']  = 'DTI';
        $data['mms']      = $this->_module->get_data_mms_for_log_history('DTI');// get mms by dept untuk menu yg beda-beda
        $data['color']    = $this->m_lab->get_data_color_by_code($kode_decrypt);
        $data['dyest']    = $this->m_lab->get_data_dye_aux_by_code($kode_decrypt,'DYE');
        $data['aux']      = $this->m_lab->get_data_dye_aux_by_code($kode_decrypt,'AUX');
        return $this->load->view('lab/v_dti_edit',$data);
	}

  public function tambah_dyeing_stuff_modal()
  {
      $data['id_warna']  = $this->input->post('id_warna');
      $data['warna']     = $this->input->post('warna');
      $data['tipe_obat'] = $this->input->post('tipe_obat');
      $data['uom']  = $this->_module->get_list_uom();
      return $this->load->view('modal/v_dyeing_stuff_tambah_modal',$data);
  }

  public function tambah_aux_modal()
  {
      $data['id_warna']  = $this->input->post('id_warna');
      $data['warna']     = $this->input->post('warna');
      $data['tipe_obat'] = $this->input->post('tipe_obat');
      $data['uom']  = $this->_module->get_list_uom();
      return $this->load->view('modal/v_aux_tambah_modal',$data);
  }

  public function view_history_dti()
  {
      $data['id_warna']  = $this->input->post('id_warna');
      return $this->load->view('modal/v_history_dti',$data);
  }

  public function simpan_dyestuff_aux_modal()
  {
    $sub_menu  = $this->uri->segment(2);
    $username = $this->session->userdata('username'); 

      if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
          // session habis
          $callback = array('message' => 'Waktu Anda Telah Habis', 'sesi' => 'habis' );
      }else{

        $id_warna  = addslashes($this->input->post('id_warna'));
        $warna     = addslashes($this->input->post('warna'));
        $kode      = addslashes($this->input->post('txtKode'));
        $product   = addslashes($this->input->post('txtProduct'));
        $qty       = $this->input->post('txtQty');
        $uom       = addslashes($this->input->post('txtUom'));
        $reff_note = addslashes($this->input->post('reff_note'));
        $tipe_obat = addslashes($this->input->post('tipe_obat'));
        $row_order = $this->input->post('row_order');

        $cek_prod  = $this->m_lab->cek_prod($id_warna,$kode)->row_array();

        if(empty($product)){
           $callback = array('message' => 'Product Harus Diisi !',  'status' => 'failed' );
        }elseif(empty($qty)){
          $callback = array('message' => 'qty Harus Diisi !',  'status' => 'failed' );
        }elseif(empty($uom)){
          $callback = array('message' => 'Uom Harus Diisi !',  'status' => 'failed' );
        }elseif(empty($reff_note)){
          $callback = array('message' => 'Reff Note Harus Diisi !',  'status' => 'failed' );
        }elseif(!empty($cek_prod['nama_produk']) AND empty($row_order)){
          $callback = array('message' => 'Maaf, Product "'.$product.'" sudah diinput !',  'status' => 'failed' );
        }else{

          if(empty($row_order)){ // simpan dye/aux
            
              $this->m_lab->save_dye_aux($id_warna,$kode,$product,$qty,$uom,$reff_note,$tipe_obat);
              $callback    = array('status'=>'success', 'message' => 'Data Berhasil Ditambahkan !',  'icon' =>'fa fa-check', 'type' => 'success');
              $jenis_log   = "edit";
              $note_log    = "Tambah Data"." | ".$kode." ".$product." | ".$qty." ".$uom." | ".$reff_note ;
              $this->_module->gen_history($sub_menu, $id_warna, $jenis_log, $note_log, $username);

          }else{// update dye /aux

            $this->m_lab->update_dye_aux($id_warna,$kode,$qty,$uom,$reff_note,$tipe_obat,$row_order);
            $callback    = array('status'=>'success', 'message' => 'Data Berhasil Disimpan !',  'icon' =>'fa fa-check', 'type' => 'success');
            $jenis_log   = "edit";
            $note_log    = "Edit Data"." | ".$kode." ".$product." | ".$qty." ".$uom." | ".$reff_note ;
            $this->_module->gen_history($sub_menu, $id_warna, $jenis_log, $note_log, $username);

          }
        }

      }
      echo json_encode($callback);
  }

  public function hapus_dye_aux()
  { 
    $sub_menu  = $this->uri->segment(2);
    $username = $this->session->userdata('username'); 

      if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
          // session habis
          $callback = array('message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
      }else{
        $id_warna  = addslashes($this->input->post('id_warna'));
        $row_order = $this->input->post('row_order');
        $type_obat = $this->input->post('type_obat');
        $kode_produk   = addslashes($this->input->post('kode_produk'));
        $product   = addslashes($this->input->post('nama_produk'));

        $this->m_lab->delete_dye_aux($id_warna,$row_order,$type_obat);
        $callback    = array('status'=>'success', 'message' => 'Data Berhasil Dihapus !',  'icon' =>'fa fa-check', 'type' => 'success');

        $jenis_log   = "cancel";
        $note_log    = "Hapus Data"." | ".$kode_produk.' '.$product .' | '.$type_obat ;
        $this->_module->gen_history($sub_menu, $id_warna, $jenis_log, $note_log, $username);

      }

      echo json_encode($callback);
  }

  public function edit_dye_aux_modal()
  {
      $id_warna           = $this->input->post('id_warna');
      $kode_produk        = $this->input->post('kode_produk');
      $nama_produk        = $this->input->post('nama_produk');
      $row_order          = $this->input->post('row_order');

      $data['id_warna']   = $id_warna;
      $data['nama_warna'] = $this->input->post('warna');
      $data['ro']         = $row_order;
      $data['kode_produk']= $kode_produk; 
      $data['nama_produk']= $nama_produk; 
      $data['uom']        = $this->_module->get_list_uom();

      $data['get']= $this->m_lab->get_warna_items_by_id($id_warna,$kode_produk,$row_order)->row_array();
      return $this->load->view('modal/v_dti_items_edit_modal',$data);

  }
  
  public function get_list_dye()
  {
    $prod      = addslashes($this->input->post('prod'));
    $tipe_obat = 'DYE';
    $callback  = $this->m_lab->get_list_dye_by_name($prod,$tipe_obat);
    echo json_encode($callback);
  }

  public function get_uom_select2()
  {
    $prod = addslashes($this->input->post('prod'));
   	$callback = $this->m_lab->get_list_uom_select2_by_prod($prod);
    echo json_encode($callback);
  }

  public function get_prod_by_id()
  {
	  $kode_produk = addslashes($this->input->post('kode_produk'));
   	$result      = $this->m_lab->get_produk_by_kode($kode_produk)->row_array();
    $callback    = array('kode_produk'=>$result['kode_produk'],'nama_produk'=>$result['nama_produk'],'uom'=>$result['uom']);
    echo json_encode($callback);
        
   }

  public function get_data_dye()
  {
    $kode_produk = addslashes($this->input->post('kode_produk'));
    $tipe_obat   = 'DYE';
    $result      = $this->m_lab->get_data_dye_by_kode($kode_produk,$tipe_obat)->row_array();
    $callback    = array('kode_produk'=>$result['kode_produk'],'nama_produk'=>$result['nama_produk'],'uom'=>$result['uom'] );

    echo json_encode($callback);
  }

  public function get_list_aux()
  {
    $prod     = addslashes($this->input->post('prod'));
    $tipe_obat= addslashes('AUX');
    $callback = $this->m_lab->get_list_aux_by_name($prod,$tipe_obat);
    echo json_encode($callback);
  }

  public function get_data_aux()
  {
    $kode_produk = addslashes($this->input->post('kode_produk'));
    $tipe_obat   = addslashes('AUX');
    $result      = $this->m_lab->get_data_aux_by_kode($kode_produk,$tipe_obat)->row_array();
    $callback    = array('kode_produk'=>$result['kode_produk'],'nama_produk'=>$result['nama_produk'],'uom'=>$result['uom'] );

    echo json_encode($callback);

  }

  public function generate()
  {

      if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
          // session habis
          $callback = array('message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
      }else{
          $sub_menu = $this->uri->segment(2);
          $username = $this->session->userdata('username'); 

          $id_warna   = addslashes($this->input->post('id_warna'));
          
          //cek item warna (DYE, FIN)
          $cek_item = $this->m_lab->cek_item_dye_aux_by_id_warna($id_warna)->num_rows();

          if($cek_item == 0 ){
            $callback = array('status' => 'failed', 'field' => '', 'message' => 'Dyeing Stuff atau Auxiliary masih Kosong !', 'icon' =>'fa fa-warning', 'type' => 'danger'  );    
          }else{

            $this->m_lab->update_status_warna($id_warna,'ready');
            $callback    = array('status'=>'success', 'message' => 'Generate Warna Berhasil !',  'icon' =>'fa fa-check', 'type' => 'success');
            
            $jenis_log   = "ready";
            $note_log    = "Generated" ;
            $this->_module->gen_history($sub_menu, $id_warna, $jenis_log, $note_log, $username);

          }
      }

      echo json_encode($callback);
  }

  function get_data_history_dti()
  {
      $id_warna  = addslashes($this->input->post('id_warna'));
      $dept_id = 'DYE';
      $list = $this->m_lab->get_datatables2($id_warna,$dept_id);
      $data = array();
      $no = $_POST['start'];
      foreach ($list as $field) {
          $kode_encrypt = $this->encryption->encrypt($field->kode);
          $kode_encrypt = encrypt_url($field->kode);
          $no++;
          $row = array();
          $row[] = $no;
          $row[] = '<a href="'.base_url('manufacturing/mO/edit/'.$kode_encrypt).'", target="_blank">'.$field->kode.'</a>';
          $row[] = $field->tanggal;
          $row[] = $field->origin;
          $row[] = $field->nama_mesin;
          $row[] = $field->nama_status;
          $data[] = $row;
      }

      $output = array(
          "draw" => $_POST['draw'],
          "recordsTotal" => $this->m_lab->count_all2($id_warna,$dept_id),
          "recordsFiltered" => $this->m_lab->count_filtered2($id_warna,$dept_id),
          "data" => $data,
      );
      //output dalam format JSON
      echo json_encode($output);
  }

  function print_dti()
  {
    $id_warna = $this->input->get('id_warna'); 

    if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
      // session habis
      print_r('Waktu Anda Telah Habis, Silahkan Log In Kembali !');
    }else if(empty($id_warna)){
      print_r('Id Warna tidak ditemukan !');

    }else{

      $header    = $this->m_lab->get_data_color_by_code($id_warna);
      $items_dye = $this->m_lab->get_data_dye_aux_by_code($id_warna,'DYE');
      $items_aux = $this->m_lab->get_data_dye_aux_by_code($id_warna,'AUX');

      $this->load->library('Pdf');//load library pdf

      //$pdf = new PDF_Code128('L','mm',array(139,215));
      $pdf = new PDF_Code128('P','mm','A4');

      $pdf->SetMargins(0,0,0);
      $pdf->SetAutoPageBreak(False);
      $pdf->AddPage();
      $pdf->setTitle('Print Out DTI');

      // tgl cetak
      $pdf->SetFont('Arial','',7,'C');
      $pdf->setXY(160,3);
      $tgl_now = tgl_indo(date('d-m-Y H:i:s'));
      $pdf->Multicell(50,4, 'Tgl.Cetak : '.$tgl_now, 0,'C');

      // Info Warna
      $pdf->SetFont('Arial','B',12,'C');
      $pdf->setXY(15,5);
      $pdf->Multicell(100,4,$header->nama_warna,0,'L');


      $pdf->SetFont('Arial','B',8,'C');
      
      $pdf->setXY(15,15);
      $pdf->Multicell(17,4,'Tgl.dibuat ',0,'L');
      $pdf->setXY(15,19);
      $pdf->Multicell(17,4,'Notes ',0,'L');
     
      $pdf->setXY(31, 15);
      $pdf->Multicell(5, 4, ':', 0, 'L');
      $pdf->setXY(31, 19);
      $pdf->Multicell(5, 4, ':', 0, 'L');
      $pdf->setXY(31, 28);
  
      $pdf->SetFont('Arial','',8,'C');
      $pdf->setXY(32,15);
      $pdf->Multicell(40,4,$header->tanggal,0,'L');
      $pdf->setXY(32,19);
      $pdf->Multicell(70,4,$header->notes,0,'L');

      $yPos_kiri=$pdf->GetY();


      $pdf->SetFont('Arial','B',8,'C');
      $pdf->setXY(130,15);
      $pdf->Multicell(25,4,'Kode Warna ',0,'L');
      $pdf->setXY(150, 15);
      $pdf->Multicell(5, 4, ':', 0, 'L');

      $pdf->SetFont('Arial','',8,'C');
      $pdf->setXY(151,15);
      $pdf->Multicell(40,4,$header->kode_warna,0,'L');

      $pdf->SetFont('Arial','',8,'C');
      $pdf->setXY(130,19);
      $pdf->Multicell(60,20,'Sample Warna / Kain',1,'C');

      $yPos_kanan=$pdf->GetY();

      if($yPos_kiri >= $yPos_kanan){
        $yPos = $yPos_kiri;
      }else{
        $yPos = $yPos_kanan;
      }
      $xPos=$pdf->GetX();
      $pdf->SetFont('Arial','B',8,'C');

      $pdf->setXY($xPos + 15, $yPos + 4);
      $pdf->Cell(10, 7, 'No. ', 1, 0, 'L');
      $pdf->Cell(20, 7, 'Kode ', 1, 0, 'C');
      $pdf->Cell(75, 7, 'Nama Produk', 1, 0, 'C');
      $pdf->Cell(20, 7, 'Qty', 1, 0, 'R');
      $pdf->Cell(50, 7, 'Reff Notes', 1, 0, 'C');

      $y      = $pdf->GetY() + 7;
      $x      = 15;
      $no_dye = 1;

      foreach($items_dye as $dye){

        $pdf->setXY($x, $y);
        if($no_dye == 1){
          $pdf->SetFont('Arial','B',8,'C');
          $pdf->Multicell(175, 5, 'Dyeing Stuff (%)', 1,'C');
          $y = $y + 5;
          $x = 15;
        }
        $pdf->setXY($x, $y);

        $pdf->SetFont('Arial','',8,'C');
        $pdf->Multicell(10, 5, $no_dye.'.', 1,'L');
        $pdf->setXY($x+10, $y);
        $pdf->Multicell(20, 5, $dye->kode_produk, 1,'C');
        $pdf->setXY($x+30, $y);
        $pdf->Multicell(75, 5, $dye->nama_produk, 1,'L');
        $pdf->setXY($x+105, $y);
        $pdf->Multicell(20, 5, $dye->qty.' '.$dye->uom, 1,'R');
        $pdf->setXY($x+125, $y);
        $pdf->Multicell(50, 5, $dye->reff_note, 1,'L');

        $no_dye++;
        $y = $y + 5;

      }

      $no_aux = 1;

      foreach($items_aux as $aux){

        $pdf->setXY($x, $y);
        if($no_aux == 1){
          $pdf->SetFont('Arial','B',8,'C');
          $pdf->Multicell(175, 5, 'Auxiliary (g/L)', 1,'C');
          $y = $y + 5;
          $x = 15;
        }
        $pdf->setXY($x, $y);

        $pdf->SetFont('Arial','',8,'C');
        $pdf->Multicell(10, 5, $no_aux.'.', 1,'L');
        $pdf->setXY($x+10, $y);
        $pdf->Multicell(20, 5, $aux->kode_produk, 1,'C');
        $pdf->setXY($x+30, $y);
        $pdf->Multicell(75, 5, $aux->nama_produk, 1,'L');
        $pdf->setXY($x+105, $y);
        $pdf->Multicell(20, 5, $aux->qty.' '.$aux->uom, 1,'R');
        $pdf->setXY($x+125, $y);
        $pdf->Multicell(50, 5, $aux->reff_note, 1,'L');

        $no_aux++;
        $y = $y + 5;

      }


      $pdf->Output();
    }


  }

}