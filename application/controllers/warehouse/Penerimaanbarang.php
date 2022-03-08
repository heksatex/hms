<?php 
defined('BASEPATH') OR exit('No Direct Script Acces Allowed');
/**
 * 
 */
class Penerimaanbarang extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->is_loggedin();//cek apakah user sudah login
		$this->load->model("_module");//load model global
        $this->load->model("m_penerimaanBarang");///load model penerimaan barang
        $this->load->model("m_mo");
	}

	public function index()
	{
		$kode_sub   = 'mm_warehouse';
		$username	= $this->session->userdata('username');
		$row 		= $this->_module->sub_menu_default($kode_sub,$username)->row_array();
		redirect($row['link_menu']);
	}

    public function Receiving()
    {
       $data['id_dept']='RCV';
       $this->load->view('warehouse/v_penerimaan_barang',$data);
    }

    public function Gudangbenang()
    {
        $data['id_dept']='GDB';
        $this->load->view('warehouse/v_penerimaan_barang',$data);
    }


    public function Twisting()
    {
        $data['id_dept']='TWS';
        $this->load->view('warehouse/v_penerimaan_barang',$data);
    }

    public function Warpingdasar()
    {
        $data['id_dept']='WRD';
        $this->load->view('warehouse/v_penerimaan_barang',$data);
    }

    public function Warpingpanjang()
    {
        $data['id_dept']='WRP';
        $this->load->view('warehouse/v_penerimaan_barang',$data);
    }

	public function Tricot()
	{
		$data['id_dept']='TRI';
		$this->load->view('warehouse/v_penerimaan_barang',$data);
	}

    public function Jacquard()
    {
        $data['id_dept']='JAC';
        $this->load->view('warehouse/v_penerimaan_barang',$data);
    }

    public function Raschel()
    {
        $data['id_dept']='RSC';
        $this->load->view('warehouse/v_penerimaan_barang',$data);
    }

    public function Cuttingshearing()
    {
        $data['id_dept']='CS';
        $this->load->view('warehouse/v_penerimaan_barang',$data);
    }

    public function Inspecting()
    {
        $data['id_dept']='INS1';
        $this->load->view('warehouse/v_penerimaan_barang',$data);
    }

    public function Greige()
    {
        $data['id_dept']='GRG';
        $this->load->view('warehouse/v_penerimaan_barang',$data);
    }

    public function Dyeing()
    {
        $data['id_dept']='DYE';
        $this->load->view('warehouse/v_penerimaan_barang',$data);
    }

    public function Finishing()
    {
        $data['id_dept']='FIN';
        $this->load->view('warehouse/v_penerimaan_barang',$data);
    }

    public function Brushing()
    {
        $data['id_dept']='BRS';
        $this->load->view('warehouse/v_penerimaan_barang',$data);
    }

    public function Inspecting2()
    {
        $data['id_dept']='INS2';
        $this->load->view('warehouse/v_penerimaan_barang',$data);
    }

    function limit_words($string, $awal_start, $awal_length, $akhir_start, $akhir_length){
        
        //$jml_kata = str_word_count($string);

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
        $list = $this->m_penerimaanBarang->get_datatables($id_dept,$kode['kode']);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $field) {
        	//$kode_encrypt = $this->encryption->encrypt($field->kode);
        	$kode_encrypt = encrypt_url($field->kode);
            if(str_word_count($field->reff_note)> 75){
                $reff_note = $this->limit_words($field->reff_note, 0, 3, -37, 37);
            }else{
                $reff_note = $field->reff_note;
            }

            $no++;
            $row = array();
            $row[] = $no;
            $row[] = '<a href="'.base_url('warehouse/penerimaanbarang/edit/'.$kode_encrypt).'">'.$field->kode.'</a>';
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
            "recordsTotal" => $this->m_penerimaanBarang->count_all($id_dept,$kode['kode']),
            "recordsFiltered" => $this->m_penerimaanBarang->count_filtered($id_dept,$kode['kode']),
            "data" => $data,
        );
        //output dalam format JSON
        echo json_encode($output);
	}



	public function edit($kode = null)
    {   
        if(!isset($kode)) show_404();
        $kode_decrypt=decrypt_url($kode);
        $list          = $this->m_penerimaanBarang->get_data_by_code($kode_decrypt);
        $data["list"]  = $list;
        $data["items"] = $this->m_penerimaanBarang->get_list_penerimaan_barang($kode_decrypt);
        $move          = $this->m_penerimaanBarang->get_stock_move_by_kode($kode_decrypt)->row_array();
        $data['smove'] =  $move;
        $data['mo']    = $this->m_penerimaanBarang->get_kode_mo_penerimaan_barang_by_move_id($move['move_id'])->row_array();
        $data['smi'] = $this->m_penerimaanBarang->get_stock_move_items_by_kode($kode_decrypt);
        $data['show_lebar'] = $this->_module->cek_show_lebar_by_dept_id($list->dept_id)->row_array();

        if(empty($data["list"])){
            show_404();
        }else{
          return $this->load->view('warehouse/v_penerimaan_barang_edit',$data);
        }


    }

    public function edit_barcode($kode = null)
    {   
        if(!isset($kode)) show_404();
        $kode_decrypt   = decrypt_url($kode);
        $data["list"]   = $this->m_penerimaanBarang->get_data_by_code($kode_decrypt);
        $smi            = $this->m_penerimaanBarang->get_move_id_by_kode($kode_decrypt)->row_array();
        $data["move_id"]= $smi;
        $data['items']  = $this->m_penerimaanBarang->get_stock_move_items_by_kode($kode_decrypt);
        $data['count']  = $this->m_penerimaanBarang->get_count_valid_scan_by_kode($kode_decrypt);
        $data['count_all'] = $this->m_penerimaanBarang->get_count_all_scan_by_kode($smi['move_id']);

        if(empty($data["list"])){
            show_404();
        }else{
          return $this->load->view('warehouse/v_penerimaan_barang_edit_barcode',$data);
        }

    }

    public function simpan()
    {
        $kode       = $this->input->post('kode');
        $tgl_transaksi  = $this->input->post('tgl_transaksi');
        $reff_note   = addslashes($this->input->post('reff_note'));
        $move_id     = $this->input->post('move_id');
        $deptid      = $this->input->post('deptid');

        $sub_menu  = $this->uri->segment(2);
        $username  = addslashes($this->session->userdata('username')); 

        if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
            // session habis
            $callback = array('message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
        }else{
            //cek status terkirim ?
            $cek_kirim  = $this->m_penerimaanBarang->cek_status_barang($kode)->row_array();
            if($cek_kirim['status'] == 'done'){
                $callback = array('status' => 'ada', 'message'=>'Maaf, Data Tidak Bisa Disimpan, Data Sudah Terkirim !', 'icon' => 'fa fa-warning', 'type'=>'danger');
            }else if($cek_kirim['status'] == 'cancel'){
                $callback = array('status' => 'ada', 'message'=>'Maaf, Data Tidak Bisa Disimpan, Data Penerimaan Sudah dibatalkan !', 'icon' => 'fa fa-warning', 'type'=>'danger');
            }else{
                if(empty($reff_note)){
                    $callback = array('status' => 'failed', 'message' => 'Reff Note Harus Diisi !', 'icon' =>'fa fa-warning', 'type' => 'danger');
                }else{
                    $this->m_penerimaanBarang->update_penerimaan_barang($kode,$reff_note);
                    $callback = array('status' => 'success', 'message' => 'Data Berhasil Disimpan !', 'icon' =>'fa fa-check', 'type' => 'success');
                    $jenis_log   = "edit";
                    $note_log    = "-> ".$reff_note;
                    $this->_module->gen_history_deptid($sub_menu, $kode, $jenis_log, $note_log, $username,$deptid);
                }
            }
        }

        echo json_encode($callback);
    }

    public function kirim_barang()
    {
        $kode        = $this->input->post('kode');
        $move_id     = $this->input->post('move_id');
        $deptid      = $this->input->post('deptid');
        $origin      = $this->input->post('origin');
        $method      = $this->input->post('method');
        $mode        = $this->input->post('mode');// scan mode / list mode
        $tgl         = date('Y-m-d H:i:s');
        $sql_stock_move_items_batch = "";
        $status_done = 'done';
        $case        = "";
        $where       = "";
        $case2       = "";
        $where2      = "";
        $case3       = "";
        $where3      = "";
        $case3x      = "";
        $where3x     = "";
        $case4       = "";
        $where4      = "";
        $case6       = "";
        $where6      = "";
        $case8       = "";
        $where8      = "";
        $whereMo     = "";
        $whereQuant     = "";

        $sub_menu  = $this->uri->segment(2);
        $username  = addslashes($this->session->userdata('username')); 
        $nu        = $this->_module->get_nama_user($username)->row_array();
        $nama_user = $nu['nama'];

        if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
            // session habis
            $callback = array('status' => 'failed','message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
        }else{

            // cek jika mode scan
            $cek_tmp = $this->m_penerimaanBarang->cek_penerimaan_barang_tmp_by_kode($kode);

            //cek status terkirim ?
            $cek_kirim  = $this->m_penerimaanBarang->cek_status_barang($kode)->row_array();
            if($cek_kirim['status'] == 'draft'){
                $callback = array('status' => 'ada', 'message'=>'Maaf, Product Belum ready !', 'icon' => 'fa fa-warning', 'type'=>'danger');
            }elseif($cek_kirim['status'] == 'done'){
                $callback = array('status' => 'ada', 'message'=>'Maaf, Data Sudah Terkirim !', 'icon' => 'fa fa-warning', 'type'=>'danger');
            }else if($cek_kirim['status'] == 'cancel'){
                $callback = array('status' => 'failed', 'message'=>'Maaf, Data Tidak Bisa Dikirim, Data Sudah dibatalkan !', 'icon' => 'fa fa-warning', 'type'=>'danger');
            }else if($cek_tmp == 0 AND $mode == 'scan'){
                $callback = array('status' => 'failed', 'message'=>'Barcode belum di Scan, Silahkan Scan Barcode terlebih dahulu !', 'icon' => 'fa fa-warning', 'type'=>'danger');
            }else{    
                    //lock table
                    $this->_module->lock_tabel('stock_move WRITE, stock_move_produk WRITE, stock_move_items WRITE, penerimaan_barang WRITE, penerimaan_barang_items WRITE, stock_quant WRITE, departemen d WRITE, pengiriman_barang WRITE, log_history WRITE, mrp_production WRITE, mrp_production_rm_target WRITE, main_menu_sub WRITE, penerimaan_barang_tmp WRITE, stock_move_items  as smi WRITE, penerimaan_barang_tmp as tmp WRITE');
                
                    //lokasi tujuan 
                    $lokasi = $this->m_penerimaanBarang->get_location_by_move_id($move_id)->row_array();

                    //update status tbl penerimaan brg
                     $this->m_penerimaanBarang->update_status_penerimaan_barang($kode,$status_done);
                    //update status tbl penerimaan brg items
                     $this->m_penerimaanBarang->update_status_penerimaan_barang_items_full($kode,$status_done);
                    //update semua status di stock_move_produk  
                     $this->_module->update_status_stock_move_produk_full($move_id,$status_done);
                    //update status tbl stock move 
                     $this->_module->update_status_stock_move($move_id,$status_done);
                    //get move id tujuan
                     $sm_tj = $this->_module->get_stock_move_tujuan($move_id,$origin,'done','cancel')->row_array();
                    // update tangal kirim = now
                    $this->m_penerimaanBarang->update_tgl_kirim_penerimaan_barang($kode,$tgl);

                    $move_id_in = $move_id;//move id asal yg ngebentuk back order

                    //get row order stock_move_items
                    $row_order  = $this->_module->get_row_order_stock_move_items_by_kode($sm_tj['move_id']);
                    
                    //loop stock_move_items
                    if($mode == 'scan'){
                        $querysm = $this->m_penerimaanBarang->get_stock_move_items_by_move_id_partial_in($move_id);
                    }else{
                        $querysm = $this->_module->get_stock_move_items_by_move_id($move_id);// jika mode list / mode != scan
                    }

                    foreach ($querysm as $val) {
                        $loop_sm     = true;
                        $sm_pasangan = true;
                        $move_id     = $val->move_id;
                        $quant_id    = $val->quant_id;

                        //sebanyak stock_move tujuanya ada
                        while ($loop_sm) {
                            if($sm_pasangan){
                                $status = "ready";
                            }
                            
                            // untuk mendapatkan origin_prod yang terdapat consume kedepannya atau consume lebih dari 1
                            $loop_sm2   = true;
                            $origin_prod_tj = "";
                            $con        = false;

                            //get list stock_move by origin
                            $list_sm = $this->_module->get_list_stock_move_origin($origin)->result_array();
                            foreach ($list_sm as $row) {
                                   
                                $mt = explode("|", $row['method']);
                                $ex_deptid = $mt[0];
                                $ex_mt     = $mt[1];

                                if($loop_sm2 == true){

                                    if($ex_mt == 'CON' AND $ex_deptid == $deptid){

                                        //get  origin_prod by move id, kode_produk
                                        $get_origin_prod = $this->m_mo->get_origin_prod_mrp_production_by_kode($row['move_id'],addslashes($val->kode_produk))->row_array();
                                        $origin_prod_tj = $get_origin_prod['origin_prod'];
                                        $loop_sm =false;
                                           
                                    }
                                   
                                }elseif($loop_sm2 == false){
                                    break;//paksa keluar looping
                                }

                            }
                                      

                            if(!empty($origin_prod_tj)){
                                $origin_prod = $origin_prod_tj; // origin prod berdasarkan 
                            }else{
                                $origin_prod = '';
                            }


                            //query ke stock_move tujuan
                            $querysm_tujuan = $this->_module->get_stock_move_tujuan($move_id,$origin,'done','cancel')->row_array();
                            $sm_tujuan      = $querysm_tujuan['move_id'];
                            if(!empty($querysm_tujuan['move_id'])){

                                // insert stock move untuk stock move tujuan (CON MO)
                                $sql_stock_move_items_batch .= "('".$querysm_tujuan['move_id']."', '".$val->quant_id."', '".addslashes($val->kode_produk)."', '".addslashes($val->nama_produk)."', '".addslashes($val->lot)."', '".$val->qty."', '".addslashes($val->uom)."', '".$val->qty2."', '".addslashes($val->uom2)."', '".$status."', '".$row_order."', '".addslashes($origin_prod)."', '".$tgl."','','".addslashes($val->lebar_greige)."','".addslashes($val->uom_lebar_greige)."','".addslashes($val->lebar_jadi)."','".addslashes($val->uom_lebar_jadi)."'), ";
                                //$sm_pasangan = false;
                                $row_order++;

                                $move_id = $querysm_tujuan['move_id'];
                                    
                                //update status stock move,stock move dan stock move produk  
                                $case3  .= "when move_id = '".$move_id."' then '".$status."'";
                                $where3 .= "'".$move_id."',";
                                $whereQuant .= "'".addslashes($val->quant_id)."',"; //quant id

                                /*
                                //update tgl stock_move_items tujuan
                                $case3x  .= "when quant_id = '".$quant_id."' then '".$tgl."'";
                                $where3x .= "'".$quant_id."',";
                                */

                                //cek jika method stock move tujuan nya CON
                                $mthd = explode("|",$querysm_tujuan['method']);
                                $ex_mthd = $mthd[1];

                                if($ex_mthd == 'CON'){//update mrp_production_rm_target by kode jadi statusnya ready
                                    //get kode MO by move id 
                                    $mrp = $this->m_mo->get_kode_mrp_production_rm_target_by_move_id($move_id)->row_array();
                                    $case8  .= "when origin_prod = '".addslashes($origin_prod)."' then '".$status."'";
                                    $where8 .= "'".addslashes($origin_prod)."',";
                                    $whereMo = "'".$mrp['kode']."',";

                                }
                                

                            }else{
                                //jika sdh tidak ada stockmove ujuan maka loop_sm berhenti
                                $loop_sm = false;
                            }

                        }//end while

                        //update stok move items asal set done
                        $case  .= "when move_id = '".$val->move_id."' then '".$status_done."'";
                        $where .= "'".$val->move_id."',";

                        //update stock quant
                        $case2 .= "when quant_id = '".$val->quant_id."' then '".$lokasi['lokasi_tujuan']."'";
                        $where2.= "'".$val->quant_id."',";

                        //update stock quant move id
                        $case6 .= "when quant_id = '".$val->quant_id."' then '".$sm_tj['move_id']."'";
                        $where6.= "'".$val->quant_id."',";


                    }//end foreach

                  
                    //simpan stock move item
                    if(!empty($sql_stock_move_items_batch)){
                        $sql_stock_move_items_batch = rtrim($sql_stock_move_items_batch, ', ');
                        $this->_module->simpan_stock_move_items_batch($sql_stock_move_items_batch);
                        $sql_stock_move_items_batch = '';
                    }
                  
                    //update status stock move items asal
                     $where = rtrim($where, ',');
                     $sql_update_stock_move_items  = "UPDATE stock_move_items SET status =(case ".$case." end), tanggal_transaksi = '".$tgl."' WHERE  move_id in (".$where.") ";
                     $this->m_penerimaanBarang->update_perbatch($sql_update_stock_move_items);

                    //update lokasi tbl stock quant
                     $where2 = rtrim($where2, ',');
                     $sql_update_stock_quant  = "UPDATE stock_quant SET lokasi =(case ".$case2." end), move_date = '".$tgl."' WHERE  quant_id in (".$where2.") ";
                     $this->m_penerimaanBarang->update_perbatch($sql_update_stock_quant);

                    $where6 = rtrim($where6, ',');
                     $sql_update_stock_quant_move_id  = "UPDATE stock_quant SET reserve_move =(case ".$case6." end) WHERE  quant_id in (".$where6.") ";
                     $this->m_penerimaanBarang->update_perbatch($sql_update_stock_quant_move_id);

                    if(!empty($where3) AND !empty($case3)){
                        //update stock move penerimaan barang 
                         $where3 = rtrim($where3, ',');
                         $sql_update_stock_move  = "UPDATE stock_move SET status =(case ".$case3." end) WHERE  move_id in (".$where3.") ";
                         $this->m_penerimaanBarang->update_perbatch($sql_update_stock_move);

                         //update stock move produk penerimaan barang 
                         $where3 = rtrim($where3, ',');
                         $sql_update_stock_move_produk  = "UPDATE stock_move_produk SET status =(case ".$case3." end) WHERE  move_id in (".$where3.") ";
                         $this->m_penerimaanBarang->update_perbatch($sql_update_stock_move_produk);

                        //update status = ready
                        $where3 = rtrim($where3, ',');
                        $where3x = rtrim($where3x, ',');
                        $whereQuant = rtrim($whereQuant, ',');
                        $sql_update_stock_move_items  = "UPDATE stock_move_items SET status =(case ".$case3." end) WHERE  move_id in (".$where3.") AND quant_id in (".$whereQuant.") ";
                        $this->m_penerimaanBarang->update_perbatch($sql_update_stock_move_items);

                         //update status=ready untuk MO tujuan
                         if(!empty($where8) AND !empty($case8)){
                            $where8 = rtrim($where8, ',');
                            $whereMo = rtrim($whereMo, ',');
                            $sql_update_mrp_rm_target  = "UPDATE mrp_production_rm_target SET status =(case ".$case8." end) WHERE  origin_prod in (".$where8.") AND kode in (".$whereMo.") ";
                            $this->_module->update_perbatch($sql_update_mrp_rm_target);

                            $sql_update_mrp_production  = "UPDATE mrp_production SET status ='ready' WHERE  kode in (".$whereMo.") "; 
                            $this->_module->update_perbatch($sql_update_mrp_production);

                         }
                    }


                    $warehouse     = $deptid;
                    $method_dept   = $warehouse;
                    $method_action = 'IN'; 

                    // Generate penerimaan barang
                    $kode_= $this->_module->get_kode_penerimaan($method_dept);
                    $get_kode_in= $kode_;

                    $dgt     =substr("00000" . $get_kode_in,-5);            
                    $kode_in = $method_dept."/".$method_action."/".date("y").  date("m").$dgt;
                    $in_row  =1;
                    $backorder = false;
                    $delete    = false;

                    $sql_stock_move_batch        = "";
                    $sql_stock_move_produk_batch = "";
                    $sql_log_history_in  = "";
                         
                    $sql_in_batch        = "";
                    $sql_in_items_batch  = "";
                    $qty_back   = "";
                    $kode_prod_del = "";
                    
                    $last_move   = $this->_module->get_kode_stock_move();
                    $move_id     = "SM".$last_move; //Set kode stock_move
                    
                    $row_order_tmp              = 1;
                    $sql_stock_move_items_batch = '';
                    $case_tmp                   = '';
                    $case_tmp_2                 = '';
                    $where_tmp                  = '';
                    
                    if($mode == 'scan'){   

                        // get stock_move_items not penerimaan_barang_tmp
                        $smi_tmp = $this->m_penerimaanBarang->get_stock_move_items_not_penerimaan_barang_tmp($move_id_in);
                        foreach($smi_tmp as $tmp){
                            $sql_stock_move_items_batch .= "('".$move_id."', '".$tmp->quant_id."', '".addslashes($tmp->kode_produk)."', '".addslashes($tmp->nama_produk)."', '".addslashes($tmp->lot)."', '".$tmp->qty."', '".addslashes($tmp->uom)."', '".$tmp->qty2."', '".addslashes($tmp->uom2)."', 'ready', '".$row_order_tmp."', '".addslashes($tmp->origin_prod)."', '".$tgl."','','".addslashes($val->lebar_greige)."','".addslashes($val->uom_lebar_greige)."','".addslashes($val->lebar_jadi)."','".addslashes($val->uom_lebar_jadi)."'), ";
                            $row_order++;

                            //get quant_id not in tmp
                            $case_tmp  .= "when quant_id = '".$tmp->quant_id."' then '' ";
                            $where_tmp .= "'".$tmp->quant_id."',";
                            $case_tmp_2  .= "when quant_id = '".$tmp->quant_id."' then '".$move_id."' ";

                        }   
                        

                        if(!empty($where_tmp) AND !empty($case_tmp)){

                            // ganti reserve move ke penerimaan baru
                            $where_tmp = rtrim($where_tmp, ',');
                            $sql_update_reserve_move  = "UPDATE stock_quant SET reserve_move =(case ".$case_tmp." end) WHERE  quant_id in (".$where_tmp.") ";
                            $this->m_penerimaanBarang->update_perbatch($sql_update_reserve_move);

                            // hapus stock move items not in tmp    
                            $sql_delete_smi_not_tmp = "DELETE  FROM stock_move_items WHERE quant_id IN (".$where_tmp.") AND move_id = '".$move_id_in."'";
                            $this->_module->update_perbatch($sql_delete_smi_not_tmp);

                        }

                                                
                    }

                    //hapus penerimaan barang tmp
                    $sql_delete_lot_tbl_tmp = "DELETE  FROM penerimaan_barang_tmp WHERE kode = '".$kode."'";
                    $this->_module->update_perbatch($sql_delete_lot_tbl_tmp);
                    
                    //foreach untuk ngebentuk back order atau tidak
                    $list  = $this->m_penerimaanBarang->get_list_penerimaan_barang_items($kode);
                    foreach ($list as $row) {
                        $kode_produk = $row->kode_produk;
                        $qty         = $row->qty;
                        
                        $qty_smi = $this->_module->get_qty_stock_move_items_by_kode($move_id_in,addslashes($kode_produk))->row_array();

                        if($qty_smi['sum_qty']<$qty and !empty($qty_smi['sum_qty'])){//jika qty di stock_move_items kurang dari qty di penerimaan barang items
                            $backorder = true;
                            $qty_back = $qty-$qty_smi['sum_qty'];
                            //simpan ke penermaan_barang_items
                            $sql_in_items_batch   .= "('".$kode_in."','".addslashes($row->kode_produk)."','".addslashes($row->nama_produk)."','".$qty_back."','".addslashes($row->uom)."','draft','".$in_row."'), ";
                            //simpan ke stock move produk 
                            $sql_stock_move_produk_batch .= "('".$move_id."','".addslashes($row->kode_produk)."','".addslashes($row->nama_produk)."','".$qty_back."','".addslashes($row->uom)."','draft','".$in_row."',''), ";                          
                            $in_row++;
                        }

                        if(empty($qty_smi['sum_qty'])){//jika qty di stock_move_items tidak ada
                            $delete = true;
                            $kode_prod_del .="'".addslashes($kode_produk)."',";
                        }

                    }

                    if($backorder== true){

                        //get data di pengiriman barang 
                        $head  = $this->m_penerimaanBarang->get_data_by_code($kode);

                        $method        = $warehouse.'|'.$method_action;              
                        $lokasi_dari   = $head->lokasi_dari;
                        $lokasi_tujuan = $head->lokasi_tujuan;
                        $reff_notes_back = 'Back Order '.$kode.' '.$head->reff_note ;
                        $schedule_date  = $head->tanggal_jt;
                        $tgl  = date('Y-m-d H:i:s');

                        //simpan ke stock move
                        $origin = $origin;
                        $sql_stock_move_batch .= "('".$move_id."','".$tgl."','".$origin."','".$method."','".$lokasi_dari."','".$lokasi_tujuan."','draft','1',''), ";         


                        $reff_picking_in = $head->reff_picking;
                        $sql_in_batch   .= "('".$kode_in."','".$tgl."','".$tgl."','".$schedule_date."','".addslashes($reff_notes_back)."','draft','".$method_dept."','".$origin."','".$move_id."','".$reff_picking_in."','".$lokasi_dari."','".$lokasi_tujuan."'), ";

                         //get mms kode berdasarkan dept_id
                         $mms = $this->_module->get_kode_sub_menu_deptid('penerimaanbarang',$method_dept)->row_array();
                         if(!empty($mms['kode'])){
                             $mms_kode = $mms['kode'];
                         }else{
                             $mms_kode = '';
                         }

                        //create log history penerimaan_barang
                        $note_log = $kode_in.'|'.$origin;
                        $date_log = date('Y-m-d H:i:s');
                        $sql_log_history_in .= "('".$date_log."','".$mms_kode."','".$kode_in."','create','".$note_log."','".$nama_user."'), ";

                        if(!empty($sql_stock_move_batch)){
                            $sql_stock_move_batch = rtrim($sql_stock_move_batch, ', ');
                            $this->_module->create_stock_move_batch($sql_stock_move_batch);

                            $sql_stock_move_produk_batch = rtrim($sql_stock_move_produk_batch, ', ');
                            $this->_module->create_stock_move_produk_batch($sql_stock_move_produk_batch);
                        }

                        if(!empty($sql_in_batch)){
                            $sql_in_batch = rtrim($sql_in_batch, ', ');
                            $this->_module->simpan_penerimaan_batch($sql_in_batch);

                            $sql_in_items_batch = rtrim($sql_in_items_batch, ', ');
                            $this->_module->simpan_penerimaan_items_batch($sql_in_items_batch);

                            $sql_log_history_in = rtrim($sql_log_history_in, ', ');
                            $this->_module->simpan_log_history_batch($sql_log_history_in);                   
                        }

                        if($mode == 'scan' AND !empty($sql_stock_move_items_batch)){

                             //simpan stock move items in baru dari mode scan
                            if(!empty($sql_stock_move_items_batch) ){
                                $sql_stock_move_items_batch = rtrim($sql_stock_move_items_batch, ', ');
                                $this->_module->simpan_stock_move_items_batch($sql_stock_move_items_batch);

                                // ganti reserve move ke penerimaan baru
                                $where_tmp = rtrim($where_tmp, ',');
                                $sql_update_reserve_move  = "UPDATE stock_quant SET reserve_move =(case ".$case_tmp_2." end) WHERE  quant_id in (".$where_tmp.") ";
                                $this->m_penerimaanBarang->update_perbatch($sql_update_reserve_move);
                            }

                            //update penerimaan barang = ready
                            $sql_update_penerimaan_barang = "UPDATE penerimaan_barang SET status ='ready' WHERE  kode in ('".$kode_in."') ";
                            $this->m_penerimaanBarang->update_perbatch($sql_update_penerimaan_barang);

                            //update penerimaan barang items = ready
                            $sql_update_penerimaan_barang_items = "UPDATE penerimaan_barang_items SET status_barang ='ready' WHERE  kode in ('".$kode_in."') ";
                            $this->m_penerimaanBarang->update_perbatch($sql_update_penerimaan_barang_items);

                            //update stock_move  == ready
                            $sql_update_stock_move  = "UPDATE stock_move SET status ='ready' WHERE  move_id in ('".$move_id."') ";
                            $this->m_penerimaanBarang->update_perbatch($sql_update_stock_move);

                            $sql_update_stock_move_produk = "UPDATE stock_move_produk SET status ='ready' WHERE  move_id in ('".$move_id."') ";
                            $this->m_penerimaanBarang->update_perbatch($sql_update_stock_move_produk);

                            $sql_update_stock_move_items  = "UPDATE stock_move_items SET status ='ready' WHERE  move_id in ('".$move_id."') ";
                            $this->m_penerimaanBarang->update_perbatch($sql_update_stock_move_items);

                        }

                        // update source_move by move_id backorder jika status nya tidak sama dengan done atau cancel
                        $sc_move = $this->_module->get_stock_move_by_move_id($move_id_in)->row_array();
                        $mvid_updt    = false;
                        $case7   = "";
                        $where7  = "";

                        // cek jika ada move_id_tujuan (biasanya ini untuk jalur jacquard saja) to consumable berdasarkan move_id sebelumnya
                        $querysm_tujuan_con = $this->_module->get_stock_move_tujuan($move_id_in,$origin,'done','cancel')->row_array();
                        $sm_tujuan          = $querysm_tujuan_con['move_id'];
                        if(!empty($sm_tujuan)){

                            $sc_move_con = $this->_module->get_stock_move_by_move_id($sm_tujuan)->row_array();
                            $source_move_con = $sc_move_con['source_move'].'|'.$move_id;

                            $sql_update_source_move_con =  "UPDATE stock_move set source_move = '$source_move_con' WHERE move_id = '$sm_tujuan' ";
                            $this->_module->update_perbatch($sql_update_source_move_con);

                        }
              
                        if(!empty($sc_move['source_move'])){
                            $sc = explode('|', $sc_move['source_move']);
                            foreach($sc as $key) {  
                                //cek jika status move id nya tidak done atau cancel
                                $mvid = $this->_module->get_move_id_by_source_move($key, 'done','cancel')->row_array();
                                if(!empty($mvid['move_id'])){
                                   $mvid_updt    = true;
                                   $move_id_updt = $mvid['move_id'].'|';
                                   $kode_out = $this->_module->get_kode_pengiriman_barang_by_move_id($mvid['move_id'])->row_array();
                                   //$case7 .= "when move_id = '".$mvid['move_id']."' then '".$kode_out['kode'].'|'.$kode_in."' ";
                                   //$where7 .= "'".$mvid['move_id']."',";
                                   $reff_picking_baru = $kode_out['kode'].'|'.$kode_in;
                                   $move_id_out   = $mvid['move_id'];
                                }
                            }

                            if($mvid_updt == true){
                                //update source_move backorder
                                $move_id_updt = rtrim($move_id_updt, '|');
                                $source_move = $move_id_updt;
                                $sql_update_source_move =  "UPDATE stock_move set source_move = '$source_move' WHERE move_id = '$move_id' ";
                                $this->_module->update_perbatch($sql_update_source_move);

                                //update reff picking baru di  pengiriman barang  dan penerimaan barang 
                                $where7 = rtrim($where7, ',');
                                $sql_update_reff_picking_pengiriman = "UPDATE pengiriman_barang SET reff_picking ='$reff_picking_baru' WHERE  move_id in ('".$move_id_out."')";
                                $this->_module->update_perbatch($sql_update_reff_picking_pengiriman); 

                                $sql_update_reff_picking_penerimaan = "UPDATE penerimaan_barang SET reff_picking ='$reff_picking_baru' WHERE  move_id in ('".$move_id."')";
                                $this->_module->update_perbatch($sql_update_reff_picking_penerimaan);                              
                           

                            }

                        }

                    }//end if backorder == true
                    
                    if($delete == true){
                        $kode_prod_del = rtrim($kode_prod_del, ',');
                        $sql_delete_penerimaan_brg_items = "DELETE  FROM penerimaan_barang_items WHERE kode_produk IN (".$kode_prod_del.") AND kode = '".$kode."'";
                        $this->m_penerimaanBarang->update_perbatch($sql_delete_penerimaan_brg_items);

                        $sql_delete_stock_move_produk = "DELETE  FROM stock_move_produk WHERE kode_produk IN (".$kode_prod_del.") AND move_id = '".$move_id_in."'";
                        $this->m_penerimaanBarang->update_perbatch($sql_delete_stock_move_produk);

                    }

                    //unlock table
                    $this->_module->unlock_tabel();

                    if($mode == 'scan'){
                        $info_partial = '( Partial )';
                    }else{
                        $info_partial = '';
                    }
              
                    $jenis_log   = "done";
                    $note_log    = "Kirim Data Barang ".$info_partial." ";
                    $this->_module->gen_history_deptid($sub_menu, $kode, $jenis_log, $note_log, $username,$deptid);
                    if($backorder == true){
                        $callback = array('status' => 'success', 'message'=>'Data Berhasil Terkirim !', 'icon' => 'fa fa-check', 'type'=>'success', 'backorder' => 'yes', 'message2'=> 'Akan terbentuk Backorder dengan No '.$kode_in);
                    }else{
                        $callback = array('status' => 'success', 'message'=>'Data Berhasil Terkirim !', 'icon' => 'fa fa-check', 'type'=>'success');

                    }
                

            }//else cek-cek

        }//else session

        echo json_encode($callback);
    }


    public function tambah_data_details_quant_penerimaan()
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
        return $this->load->view('modal/v_tambah_details_quant_penerimaan_modal',$data);
    }


    public function tambah_data_details_quant_penerimaan_modal()
    {
        $kode_produk = addslashes($this->input->post('kode_produk'));
        $move_id     = $this->input->post('move_id');
        $origin      = $this->input->post('origin');
        $deptid      = $this->input->post('deptid');
        //lokasi tujuan, lokasi dari
        $lokasi = $this->m_penerimaanBarang->get_location_by_move_id($move_id)->row_array();

        $list = $this->m_penerimaanBarang->get_datatables3($kode_produk,$lokasi['lokasi_dari'],$origin,$deptid);
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
            $row[] = $field->quant_id;
            //$row[] = '';//buat checkbox
            //$row[] = $field->kode_produk."|".htmlentities($field->nama_produk)."|".$field->lot."|".$field->qty."|".$field->uom."|".$field->qty2."|".$field->uom2."|".$field->lokasi."|".$field->quant_id."|^";
          
            $data[] = $row;
        }
        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_penerimaanBarang->count_all3($kode_produk,$lokasi['lokasi_dari'],$origin,$deptid),
            "recordsFiltered" => $this->m_penerimaanBarang->count_filtered3($kode_produk,$lokasi['lokasi_dari'],$origin,$deptid),
            "data" => $data,
        );
        //output dalam format JSON
        echo json_encode($output);
    }


    public function save_details_quant_penerimaan_modal()
    {
        $sub_menu  = $this->uri->segment(2);
        $username  = addslashes($this->session->userdata('username')); 
        $deptid    = $this->input->post('deptid');
        $kode      = $this->input->post('kode');

        $cek_kirim  = $this->m_penerimaanBarang->cek_status_barang($kode)->row_array();

        if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
            // session habis
            $callback = array('status' => 'failed','message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
        }else if($cek_kirim['status'] == 'done'){//cek jika status penerimaan sudah terkirim
            $callback = array('status' => 'failed', 'message'=>'Maaf, Data Tidak Bisa Disimpan, Data Sudah Terkirim !', 'icon' => 'fa fa-warning', 'type'=>'danger');
        }else if($cek_kirim['status'] == 'cancel'){//cek jika status penerimaan batal
            $callback = array('status' => 'failed', 'message'=>'Maaf, Data Tidak Bisa Disimpan, Data Penerimaan Sudah dibatalkan !', 'icon' => 'fa fa-warning', 'type'=>'danger');
        }else{

          $kode_produk= $this->input->post('kode_produk');
          $nama_produk= $this->input->post('nama_produk');
          $move_id    = $this->input->post('move_id');
          $origin_prod= $this->input->post('origin_prod');
          $origin     = $this->input->post('origin');
          $check      = $this->input->post('checkbox');
          $countchek  = $this->input->post('countchek');
          $sql_stock_quant_batch      = "";
          $sql_stock_move_items_batch = "";
          $tgl        = date('Y-m-d H:i:s');
          //$row        = explode("^,", $check);
          $status     = "";
          $status_brg = "ready";
          $case       = "";
          $where      = "";
          $case2      = "";
          $where2     = "";          
          $kosong      = false;

          //lock tabel
          $this->_module->lock_tabel('stock_quant WRITE, stock_move_items WRITE,stock_move WRITE,stock_move_produk WRITE, penerimaan_barang WRITE, penerimaan_barang_items WRITE, mrp_production_rm_target WRITE' );
          //get row order stock_move_items
          $row_order  = $this->_module->get_row_order_stock_move_items_by_kode($move_id);
          //get qty  produk penerimaan barang items 
          $get_qty   = $this->m_penerimaanBarang->get_qty_penerimaan_barang_items_by_kode($kode,addslashes($kode_produk))->row_array();
          //get sum qty produk stock move items
          $get_qty2  = $this->_module->get_qty_stock_move_items_by_kode($move_id,addslashes($kode_produk))->row_array();
          //get last quant id
          $start = $this->_module->get_last_quant_id();
          //get_lokasi dari by move id 
          $location = $this->_module->get_location_by_move_id($move_id)->row_array();

          foreach ($check as $data) {

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
                $sql_stock_move_items_batch .= "('".$move_id."', '".$quantid."','".addslashes($kode_produk)."', '".addslashes($nama_produk)."','".addslashes(trim($lot))."','".$qty."','".addslashes($uom)."','".$qty2."','".addslashes($uom2)."','ready','".$row_order."','".addslashes($origin_prod)."', '".$tgl."','".addslashes($lokasi_fisik)."','".addslashes($lebar_greige)."','".addslashes($uom_lebar_greige)."','".addslashes($lebar_jadi)."','".addslashes($uom_lebar_jadi)."'), ";     
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
              //$break   = false;             

              //cek product di stock quant
              $cq = $this->_module->cek_produk_di_stock_quant($quantid,$location['lokasi_dari'])->row_array();
              if(!empty($cq['quant_id'])){


                //untuk mendapatkan origin_prod yang terdapat consume kedepannya atau consume lebih dari 1
                $loop_sm    = true;
                $origin_prod_tj = "";
                $con        = false;

                //get list stock_move by origin
                $list_sm = $this->_module->get_list_stock_move_origin($origin)->result_array();
                foreach ($list_sm as $row) {
                       
                    $mt = explode("|", $row['method']);
                    $ex_deptid = $mt[0];
                    $ex_mt     = $mt[1];

                    if($loop_sm == true){

                        if($ex_mt == 'CON' AND $ex_deptid == $deptid){

                            //get  origin_prod by move id, kode_produk
                            $get_origin_prod = $this->m_mo->get_origin_prod_mrp_production_by_kode($row['move_id'],addslashes($kode_produk))->row_array();
                            $origin_prod_tj = $get_origin_prod['origin_prod'];
                            $loop_sm =false;
                               
                        }
                       
                    }elseif($loop_sm == false){
                        break;//paksa keluar looping
                    }

                }
                          

                if(!empty($origin_prod_tj)){
                    $origin_prod = $origin_prod_tj; // origin prod berdasarkan 
                }else{
                    $origin_prod = '';
                }


                //insert ke stock move items
                $sql_stock_move_items_batch .= "('".$move_id."', '".$quantid."','".addslashes($kode_produk)."', '".addslashes($nama_produk)."','".addslashes(trim($lot))."','".$qty."','".addslashes($uom)."','".$qty2."','".addslashes($uom2)."','draft','".$row_order."','".addslashes($origin_prod)."', '".$tgl."'), ";                            
                $row_order++;          

                //update reserve move by quant id di stok quant                
                $case   .= "when quant_id = '".$quantid."' then '".$move_id."'";
                $where  .= "'".$quantid."',";

              }else{
                $kosong = true;
              }     

          }
          */
        
          if(!empty($sql_stock_move_items_batch) AND $kosong == false ){
              $sql_stock_move_items_batch = rtrim($sql_stock_move_items_batch, ', ');
              $this->_module->simpan_stock_move_items_batch($sql_stock_move_items_batch);
            
              if(!empty($case)){
                //update qty stock quant 
                $where = rtrim($where, ',');
                $sql_update_qty_stock_quant  = "UPDATE stock_quant SET reserve_move =(case ".$case." end) WHERE  quant_id in (".$where.") ";
                $this->m_penerimaanBarang->update_perbatch($sql_update_qty_stock_quant);
              }

              if(!empty($case2)){
                //update qty stock quant 
                $where2 = rtrim($where2, ',');
                $sql_update_qty_stock_quant2  = "UPDATE stock_quant SET qty =(case ".$case2." end) WHERE  quant_id in (".$where2.") ";
                $this->m_penerimaanBarang->update_perbatch($sql_update_qty_stock_quant2);
              }

              $this->m_penerimaanBarang->update_status_penerimaan_barang_items($kode,addslashes($kode_produk),$status_brg);
              $this->_module->update_status_stock_move_items($move_id,addslashes($kode_produk),$status_brg);

              $cek_status = $this->m_penerimaanBarang->cek_status_barang_penerimaan_barang_items($kode,'ready')->row_array();

              if(!empty($cek_status['status_barang'])){
                $this->m_penerimaanBarang->update_status_penerimaan_barang($kode,$status_brg);
                $this->_module->update_status_stock_move_produk($move_id,addslashes($kode_produk),$status_brg);
                $cek_status2 = $this->m_penerimaanBarang->cek_status_penerimaan_barang($kode)->row_array();
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



    public function hapus_details_items()
    {   
        $sub_menu   = $this->uri->segment(2);
        $username   = addslashes($this->session->userdata('username')); 
        $deptid     = $this->input->post('deptid');
        $kode       = $this->input->post('kode');

        $cek_kirim  = $this->m_penerimaanBarang->cek_status_barang($kode)->row_array();

        if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
            // session habis
            $callback = array('message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
        }else if($cek_kirim['status'] == 'done'){//cek jika status penerimaan sudah terkii
            $callback = array('status' => 'failed', 'message'=>'Maaf, Data Tidak Bisa Dihapus, Data Sudah Terkirim !', 'icon' => 'fa fa-warning', 'type'=>'danger');
        }else if($cek_kirim['status'] == 'cancel'){//cek jika status penerimaan batal
            $callback = array('status' => 'failed', 'message'=>'Maaf, Data Tidak Bisa Dihapus, Data Penerimaan Sudah dibatalkan !', 'icon' => 'fa fa-warning', 'type'=>'danger');
        }else{

            $quant_id   = $this->input->post('quant_id');
            $row_order  = $this->input->post('row_order');
            $move_id    = $this->input->post('move_id');
            $kode_produk= addslashes($this->input->post('kode_produk'));
            $nama_produk= addslashes($this->input->post('nama_produk'));
            $status_brg = 'draft';
            
            //lock tabel
            $this->_module->lock_tabel('stock_quant WRITE, stock_move WRITE,stock_move_items WRITE,stock_move_produk WRITE, penerimaan_barang WRITE, penerimaan_barang_items WRITE' );
            
            //delete stock move item dan update reserve move jadi kosong
            $this->_module->delete_details_items($move_id,$quant_id,$row_order);

            //get sum qty produk stock move items
            $get_qty2  = $this->_module->get_qty_stock_move_items_by_kode($move_id,$kode_produk)->row_array();

            //update status draft jika qty di stock move items kosong
            if(empty($get_qty2['sum_qty'])){
              $this->m_penerimaanBarang->update_status_penerimaan_barang_items($kode,$kode_produk,$status_brg);
              $this->_module->update_status_stock_move_produk($move_id,$kode_produk,$status_brg);
            }

            $cek_status = $this->m_penerimaanBarang->cek_status_barang_penerimaan_barang_items($kode,'ready')->row_array();
            if(empty($cek_status['status_barang'])){
                $this->m_penerimaanBarang->update_status_penerimaan_barang($kode,$status_brg);
                $cek_status2 = $this->m_penerimaanBarang->cek_status_penerimaan_barang($kode)->row_array();
                if($cek_status2['status']=='draft'){
                    $this->_module->update_status_stock_move($move_id,$status_brg);
                }
            }

            if(!empty($cek_status['status_barang'])){
                $this->m_penerimaanBarang->update_status_penerimaan_barang($kode,'ready');
                $cek_status2 = $this->m_penerimaanBarang->cek_status_penerimaan_barang($kode)->row_array();
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
            $kurang = false;
            $produk_kurang = "";
            $kosong = true;
            $produk_kosong = "";
            $cukup  = false;          
            $produk_terpenuhi = "";
            $history = false;   
            $qty2_new = "";
            $qty2_update = "";
            $case_qty2 = "";           

           //cek status terkirim ?
            $cek_kirim  = $this->m_penerimaanBarang->cek_status_barang($kode)->row_array();
            if($cek_kirim['status'] == 'done'){
                $callback = array('status' => 'failed', 'message'=>'Maaf, Tidak Bisa Cek Stok, Data Sudah Terkirim !', 'icon' => 'fa fa-warning', 'type'=>'danger');
            }else if($cek_kirim['status'] == 'cancel'){
                $callback = array('status' => 'failed', 'message'=>'Maaf, Tidak Bisa Cek Stok, Data Penerimaan Sudah Dibatalkan !', 'icon' => 'fa fa-warning', 'type'=>'danger');
            }else{

                    //lock tabel
                    $this->_module->lock_tabel('stock_quant WRITE, stock_move_items WRITE,stock_move WRITE,stock_move_produk WRITE, penerimaan_barang WRITE, penerimaan_barang_items WRITE, departemen WRITE, mrp_production_rm_target WRITE' );

                    //get row order stock_move_items
                    $row_order  = $this->_module->get_row_order_stock_move_items_by_kode($move_id);
                    //lokasi tujuan, lokasi dari
                    $lokasi = $this->m_penerimaanBarang->get_location_by_move_id($move_id)->row_array();

                    $list  = $this->m_penerimaanBarang->get_list_penerimaan_barang_items($kode);
                    foreach ($list as $val) {
                        $kode_produk = $val->kode_produk;
                        $nama_produk = $val->nama_produk;
                        $qty         = $val->qty;
                        $uom         = $val->uom;
                        $ro_items    = $val->row_order;
                        $origin_prod = $val->origin_prod;

                        //get last quant id
                        $start = $this->_module->get_last_quant_id();
                     
                        //cek qty produk di stock_move_items apa masih kurang dengan target qty di penerimaan barang items
                        $qty_smi = $this->_module->get_qty_stock_move_items_by_kode($move_id,addslashes($kode_produk))->row_array();
                        $kebutuhan_qty = $qty - $qty_smi['sum_qty'];

                        if($kebutuhan_qty > 0){//jika kebutuhan_qty > 0

                            $ceK_quant = $this->_module->get_cek_stok_quant_by_prod(addslashes($kode_produk),$lokasi['lokasi_dari'],$origin,$deptid)->result_array();
                            foreach ($ceK_quant as $stock) {
                                $kosong = false;
                                $history = true;   

                                /*
                                //untuk mendapatkan origin_prod yang terdapat consume kedepannya atau consume lebih dari 1
                                $loop_sm    = true;
                                $origin_prod_tj = "";
                                $con        = false;

                                //get list stock_move by origin
                                $list_sm = $this->_module->get_list_stock_move_origin($origin)->result_array();
                                foreach ($list_sm as $row) {
                                       
                                    $mt = explode("|", $row['method']);
                                    $ex_deptid = $mt[0];
                                    $ex_mt     = $mt[1];

                                    if($loop_sm == true){

                                        if($ex_mt == 'CON' AND $ex_deptid == $deptid){

                                            //get  origin_prod by move id, kode_produk
                                            $get_origin_prod = $this->m_mo->get_origin_prod_mrp_production_by_kode($row['move_id'],addslashes($kode_produk))->row_array();
                                            $origin_prod_tj = $get_origin_prod['origin_prod'];
                                            $loop_sm =false;
                                               
                                        }
                                       
                                    }elseif($loop_sm == false){
                                        break;//paksa keluar looping
                                    }

                                }

                                if(!empty($origin_prod_tj)){
                                    $origin_prod = $origin_prod_tj; // origin prod berdasarkan 
                                }else{
                                    $origin_prod = '';
                                }
                                */


                                if($kebutuhan_qty >= $stock['qty']){//jika kebutuhan_qty lebih atau sama dengan qty di stock_quant
                                    //update reserve_move dengan move_id
                                    $case2  .= "when quant_id = '".$stock['quant_id']."' then '".$move_id."'";
                                    $where2 .= "'".$stock['quant_id']."',"; 

                                    //insert stock move items batch
                                    $sql_stock_move_items_batch .= "('".$move_id."', '".$stock['quant_id']."','".addslashes($kode_produk)."', '".addslashes($nama_produk)."','".addslashes($stock['lot'])."','".$stock['qty']."','".addslashes($uom)."','".$stock['qty2']."','".addslashes($stock['uom2'])."','".$status_brg."','".$row_order."','".addslashes($origin_prod)."', '".$tgl."','".addslashes($stock['lokasi_fisik'])."','".addslashes($stock['lebar_greige'])."','".addslashes($stock['uom_lebar_greige'])."','".addslashes($stock['lebar_jadi'])."','".addslashes($stock['uom_lebar_jadi'])."'), ";                                      
                                    $row_order++;                                 
                                    $kebutuhan_qty = $kebutuhan_qty - $stock['qty'];

                                }else if($kebutuhan_qty < $stock['qty']){//jika kebutuhan_qty kurang dari qty di stock_quant
                                    $qty_new = $stock['qty'] - $kebutuhan_qty;//qty baru di stock quant

                                    //update qty produk di stock_quant
                                    $case  .= "when quant_id = '".$stock['quant_id']."' then '".$qty_new."'";
                                    $where .= "'".$stock['quant_id']."',";

                                    $qty2_new = ($stock['qty2']/$stock['qty'])*$kebutuhan_qty;
                                    $qty2_update  = $stock['qty2'] - $qty2_new;
                                    $case_qty2 .= "when quant_id = '".$stock['quant_id']."' then '".$qty2_update."'";

                                    //insert qty stock_quant_batch dengan quant_id baru 
                                    $sql_stock_quant_batch .= "('".$start."','".$tgl."', '".addslashes($kode_produk)."', '".addslashes($nama_produk)."','".addslashes($stock['lot'])."','".addslashes($stock['nama_grade'])."','".$kebutuhan_qty."','".addslashes($uom)."','".$qty2_new."','".addslashes($stock['uom2'])."','".$lokasi['lokasi_dari']."','".addslashes($stock['reff_note'])."','".$move_id."','".$stock['reserve_origin']."','".$tgl."','".addslashes($stock['lebar_greige'])."','".addslashes($stock['uom_lebar_greige'])."','".addslashes($stock['lebar_jadi'])."','".addslashes($stock['uom_lebar_jadi'])."'), ";
                                    //insert stock move items batch
                                    $sql_stock_move_items_batch .= "('".$move_id."', '".$start."','".addslashes($kode_produk)."', '".addslashes($nama_produk)."','".addslashes($stock['lot'])."','".($kebutuhan_qty)."','".addslashes($uom)."','".$qty2_new."','".addslashes($stock['uom2'])."','".$status_brg."','".$row_order."','".addslashes($origin_prod)."', '".$tgl."','".addslashes($stock['lokasi_fisik'])."','".addslashes($stock['lebar_greige'])."','".addslashes($stock['uom_lebar_greige'])."','".addslashes($stock['lebar_jadi'])."','".addslashes($stock['uom_lebar_jadi'])."'), ";
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

                        }else{//jik kebutuhan_qty <= 0
                            $cukup = true;
                            $produk_terpenuhi .= $nama_produk.', ';
                        }


                        if(!empty($sql_stock_quant_batch) ){
                          $sql_stock_quant_batch = rtrim($sql_stock_quant_batch, ', ');
                          $this->_module->simpan_stock_quant_batch($sql_stock_quant_batch);
                          
                          $sql_stock_quant_batch = "";
                        }

                        if(!empty($sql_stock_move_items_batch)){
                          $sql_stock_move_items_batch = rtrim($sql_stock_move_items_batch, ', ');
                          $this->_module->simpan_stock_move_items_batch($sql_stock_move_items_batch);

                          $sql_stock_move_batch = "";
                        }
  
                        //update reserve_move di stock_quant
                        if(!empty($where2) AND !empty($case2)){
                          $where2 = rtrim($where2, ',');
                          $sql_update_reserve_move  = "UPDATE stock_quant SET reserve_move =(case ".$case2." end) WHERE  quant_id in (".$where2.") ";
                          $this->m_penerimaanBarang->update_perbatch($sql_update_reserve_move);

                          $sql_update_reserve_move = "";
                          $where2 = "";
                          $case2  = "";
                        }
                      
                        //update qty baru di stock quant 
                        if(!empty($where) AND !empty($case)){
                          $where = rtrim($where, ',');
                          $sql_update_qty_stock  = "UPDATE stock_quant SET qty =(case ".$case." end), qty2 =(case ".$case_qty2." end)  WHERE  quant_id in (".$where.") ";
                          $this->m_penerimaanBarang->update_perbatch($sql_update_qty_stock);

                          $sql_update_qty_stock = "";
                          $where = "";
                          $case  = "";
                        }

                        if(!empty($where3) AND !empty($case3)){
                          $where3 = rtrim($where3, ',');
                          $sql_update_status_penerimaan_items = "UPDATE penerimaan_barang_items SET status_barang =(case ".$case3." end) WHERE  kode_produk in (".$where3.") AND kode = '".$kode."' ";
                          $this->m_penerimaanBarang->update_perbatch($sql_update_status_penerimaan_items);

                          $sql_update_status_stock_move_produk = "UPDATE stock_move_produk SET status =(case ".$case3." end) WHERE  kode_produk in (".$where3.") AND move_id = '".$move_id."' ";
                          $this->m_penerimaanBarang->update_perbatch($sql_update_status_stock_move_produk);

                          $sql_update_penerimaan_barang_items = "";
                          $sql_update_status_stock_move_produk = "";
                          $where3 = "";
                          $case3  = "";
                        }


                    }// end foreach list penerimaan barang

                    //cek apa ada items yang status nya ready?
                    $all_produk_items = $this->m_penerimaanBarang->cek_status_barang_penerimaan_barang_items($kode,'ready')->row_array();

                    //jika tidak kosong maka update status di penerimaan brg
                    if(!empty($all_produk_items['status_barang'])){
                        $this->m_penerimaanBarang->update_status_penerimaan_barang($kode,$status_brg);
                    }
                     
                    $cek_status2 = $this->m_penerimaanBarang->cek_status_penerimaan_barang($kode)->row_array();
                    if($cek_status2['status']=='ready'){
                        $this->_module->update_status_stock_move($move_id,$status_brg);
                    }
        
                    
                    //unlock table
                    $this->_module->unlock_tabel();                   

                    if(!empty($produk_kosong)){
                        $callback = array('status' => 'failed', 'message'=> 
                            'Maaf, Qty Product "'.  $produk_kosong  .'" Kosong !', 'icon' => 'fa fa-warning', 'type'=>'danger');

                    }else if(!empty($produk_kurang)){                       
                        $callback = array('status' => 'failed', 'message'=> 
                            'Maaf, Qty Product "'.  $produk_kurang  .'" tidak mencukupi !', 'icon' => 'fa fa-warning', 'type'=>'danger', 'status_kurang' => 'yes',  'message2'=>'Detail Product Berhasil Ditambahkan !', 'icon2' => 'fa fa-check', 'type2'=>'success');
                    /*                                            
                    }else if(!empty($produk_terpenuhi)){
                        $callback = array('status' => 'failed', 'message'=> 
                            'Qty Product "'.  $produk_terpenuhi  .'" Sudah Terpenuhi !', 'icon' => 'fa fa-warning', 'type'=>'danger');
                    */

                    }else{

                        if(!empty($produk_terpenuhi)){
                            $callback = array('status' => 'success', 'message'=>'Detail Product Berhasil Ditambahkan !', 'icon' => 'fa fa-check', 'type'=>'success', 'terpenuhi'=>'yes');  
                        }else{
                            $callback = array('status' => 'success', 'message'=>'Detail Product Berhasil Ditambahkan !', 'icon' => 'fa fa-check', 'type'=>'success');   
                        }
                    }

                    if($history == true){
                      $jenis_log   = "edit";
                      $note_log    = "Cek Stok";
                      $this->_module->gen_history_deptid($sub_menu, $kode, $jenis_log, $note_log, $username,$deptid);
                    }
               
            }//end if cek status penerimaan barang
        }

        echo json_encode($callback);

    }


    function valid_barcode_in()
    {

        if (empty($this->session->userdata('username'))) {//cek apakah session masih ada
            // session habis
            $callback = array('message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
        }else{

            $sub_menu = $this->uri->segment(2);
            $username = addslashes($this->session->userdata('username')); 

            $deptid      = $this->input->post('deptid');
            $kode        = addslashes($this->input->post('kode'));
            $txtbarcode  = $this->input->post('txtbarcode');
            $tgl         = date('Y-m-d H:i:s');

            // lock table
            $this->_module->lock_tabel('stock_move as sm WRITE, stock_move_items WRITE, penerimaan_barang as pb WRITE, penerimaan_barang_tmp WRITE, penerimaan_barang WRITE, log_history WRITE, main_menu_sub WRITE, user WRITE');

            //cek status terkirim ?
            $cek_kirim  = $this->m_penerimaanBarang->cek_status_barang($kode)->row_array();
            if($cek_kirim['status'] == 'draft'){
                $callback = array('status' => 'ada', 'message'=>'Maaf, Product yang akan di Scan belum ready !', 'icon' => 'fa fa-warning', 'type'=>'danger');
            }elseif($cek_kirim['status'] == 'done'){
                $callback = array('status' => 'ada', 'message'=>'Maaf, Data Sudah Terkirim !', 'icon' => 'fa fa-warning', 'type'=>'danger');
            }else if($cek_kirim['status'] == 'cancel'){
                $callback = array('status' => 'failed', 'message'=>'Maaf, Data Tidak Bisa Dikirim, Data Sudah dibatalkan !', 'icon' => 'fa fa-warning', 'type'=>'danger');
            }else{ 
                // cek lo apa sudah di scan / belum
                $ck_scan = $this->m_penerimaanBarang->cek_scan_by_lot($kode,$txtbarcode)->row_array();
                if(!empty($ck_scan['lot'])){// jika tidak koosong
                    $callback = array('status' => 'failed', 'message'=>'Barcode '.$txtbarcode.' Sudah di Scan !', 'icon' => 'fa fa-warning', 'type'=>'danger');
                }else{

                    $mv = $this->m_penerimaanBarang->get_move_id_by_kode($kode)->row_array();

                    // get list tmp penerimaan barang by lot yg ready
                    $tmp   = $this->m_penerimaanBarang->get_list_stock_move_items_by_lot($mv['move_id'],$txtbarcode,'ready');
                    $empty = true;
                    foreach($tmp as $row){
                        $empty  = false;
                        // insert topenerimaan barang tmp
                        $this->m_penerimaanBarang->simpan_penerimaan_barang_tmp($kode,$row->quant_id,$mv['move_id'],$row->kode_produk,$row->lot,'t',$tgl);
                    }

                    if($empty == true ){
                        $callback = array('status' => 'failed', 'message'=>'Barcode '.$txtbarcode.' Tidak valid  !', 'icon' => 'fa fa-warning', 'type'=>'danger');
                    }else{

                        $jenis_log   = "edit";
                        $note_log    = "Scan Barcode ".$txtbarcode;
                        $this->_module->gen_history_deptid($sub_menu, $kode, $jenis_log, $note_log, $username,$deptid);

                        $callback = array('status' => 'success', 'message'=>'Barcode '.$txtbarcode.' Valid Scan !', 'icon' => 'fa fa-check', 'type'=>'success');   
                    }
                }
            }

            //unlock table            
            $this->_module->unlock_tabel();

        }
        echo json_encode($callback);
    }


    function print_penerimaan_barang()
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
        $head    = $this->m_penerimaanBarang->get_data_by_code_print($kode,$dept_id);
        
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
	    $pdf->setTitle('Penerimaan Barang : '.$nama_dept);

	    $pdf->SetFont('Arial','B',9,'C');
	    $pdf->Cell(0,10,'PENERIMAAN BARANG '.$nama_dept,0,0,'C');

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
        $items = $this->m_penerimaanBarang->get_list_penerimaan_barang_print($kode,$dept_id);
        $x    = 5;
        $y    = 32;
        $no   = 1;
        foreach($items as $row){
          
          // set font tbody =
          $pdf->SetFont('Arial','',8,'C');

            $pdf->setXY($x, $y);
            $pdf->Multicell(7, 5, $no, 1,'L');
            $pdf->setXY($x+7, $y);
            $pdf->Multicell(20, 5, $this->custom_char_in($row->kode_produk,8), 1,'L');
            $pdf->setXY($x+27, $y);
            $pdf->Multicell(70, 5, $this->custom_char_in($row->nama_produk,45), 1,'L');
            $pdf->setXY($x+97, $y);
            $pdf->Multicell(25, 5, number_format($row->qty,2), 1,'R');
            $pdf->setXY($x+122, $y);
            $pdf->Multicell(10, 5, $this->custom_char_in($row->uom,3), 1,'L');
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
        $pdf->Cell(15, 5, 'Qty2', 1, 0, 'R');
        $pdf->Cell(10, 5, 'Uom2', 1, 0, 'L');
        $pdf->Cell(20, 5, 'Reff Note', 1, 0, 'C');

        // details
        $smi  = $this->m_penerimaanBarang->get_stock_move_items_by_kode_print($kode,$dept_id);
        $x    = 5;
        $y    = $y+10;
        $no   = 1;
        foreach($smi as $row){

          // set font tbody 
          $pdf->SetFont('Arial','',8,'C');
          
            $pdf->setXY($x, $y);
            $pdf->Multicell(7, 5, $no, 1,'L');
            $pdf->setXY($x+7, $y);
            $pdf->Multicell(20, 5, $this->custom_char_in($row->kode_produk,8), 1,'L');
            $pdf->setXY($x+27, $y);
            $pdf->Multicell(70, 5,  $this->custom_char_in($row->nama_produk,45), 1,'L');
            $pdf->setXY($x+97, $y);
            $pdf->Multicell(30, 5, $row->lot, 1,'L');
            $pdf->setXY($x+127, $y);
            $pdf->Multicell(15, 5, number_format($row->qty,2), 1,'R');
            $pdf->setXY($x+142, $y);
            $pdf->Multicell(10, 5, $row->uom, 1,'L');
            $pdf->setXY($x+152, $y);
            $pdf->Multicell(15, 5, round($row->qty2,2), 1,'R');
            $pdf->setXY($x+167, $y);
            $pdf->Multicell(10, 5, $row->uom2, 1,'L');
            $pdf->setXY($x+177, $y);
            $pdf->Multicell(20, 5, $this->custom_char_in($row->reff_note,8), 1,'L');
            
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


    function custom_char_in($string, $length)
    {
      if(strlen($string) <= $length){
        return $string;
      }
      return substr($string, 0, $length). ' ...';
    }

}


?>