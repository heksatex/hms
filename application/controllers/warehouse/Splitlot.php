<?php defined('BASEPATH') or exit ('No Direct Script Acces Allowed');

/**
 * 
 */
class Splitlot extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->is_loggedin();//cek apakah user sudah login
        $this->load->model("_module");
        $this->load->model("m_splitLot");
    }

    public function index()
    {
        $data['id_dept']   = 'SPLIT';
        $data['warehouse'] = $this->_module->get_list_departement();
        $this->load->view('warehouse/v_split_lot', $data);
    }

    public function get_data()
    {	
        if(isset($_POST['start']) && isset($_POST['draw'])){
            $list = $this->m_splitLot->get_datatables();
            $data = array();
            $no = $_POST['start'];
            foreach ($list as $field) {
                $kode_encrypt = encrypt_url($field->kode_split);
            
                $no++;
                $row = array();
                $row[] = $no;
                $row[] = '<a href="'.base_url('warehouse/splitlot/edit/'.$kode_encrypt).'">'.$field->kode_split.'</a>';
                $row[] = $field->tanggal;
                $row[] = $field->departemen;
                $row[] = $field->kode_produk;
                $row[] = $field->nama_produk;
                $row[] = $field->lot;
                $row[] = $field->qty.' '.$field->uom;
                $row[] = $field->qty2.' '.$field->uom2;
                $row[] = $field->jml_split;
                $row[] = $field->note;
    
                $data[] = $row;
            }
    
            $output = array(
                "draw" => $_POST['draw'],
                "recordsTotal" => $this->m_splitLot->count_all(),
                "recordsFiltered" => $this->m_splitLot->count_filtered(),
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
	    $data['id_dept']       = 'SPLIT';
        $data['kode_split']    = $this->m_splitLot->get_kode_split();
        $data['warehouse']     = $this->_module->get_list_departement();
	    return $this->load->view('warehouse/v_split_lot_add', $data);
	}

    public function edit($id = null)
    {
        if(!isset($id)) show_404();
            $kode_decrypt     = decrypt_url($id);
            $id_dept   		  = 'SPLIT';
            $data['id_dept']  = $id_dept;
            $data['mms']      = $this->_module->get_data_mms_for_log_history($id_dept);// get mms by dept untuk menu yg beda-beda
            $data["split"]          = $this->m_splitLot->get_data_split_by_kode($kode_decrypt);
            $data['split_items']    = $this->m_splitLot->get_data_split_items_by_kode($kode_decrypt);
        if(empty($data["split"])){
            show_404();
        }else{
            return $this->load->view('warehouse/v_split_lot_edit',$data);
        }

    }


    public function import_produk()
    {
        $departemen            = $this->input->post('departemen');
        $data['departemen']    = $departemen;
        return $this->load->view('modal/v_split_lot_import_modal',$data);
    }


    public function list_import_produk()
    {
        $departemen  = addslashes($this->input->post('departemen'));
        // get lokasi by dept id
        $get_lokasi = $this->_module->get_nama_dept_by_kode($departemen)->row_array();
        $lokasi     = $get_lokasi['stock_location'];
        if(isset($_POST['start']) && isset($_POST['draw'])){
            $list = $this->m_splitLot->get_datatables2($lokasi);
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
                $row[] = '<a href="#" class="btn btn-primary btn-xs pilih" quant_id="'.$field->quant_id.'" kode_produk="'.$field->kode_produk.'"  nama_produk="'.htmlentities($field->nama_produk).'" lot ="'.$field->lot.'" qty="'.$field->qty.'" uom="'.$field->uom.'" qty2="'.$field->qty2.'" uom2="'.$field->uom2.'" data-togle="tooltip" title="Pilih Produk"><i  class="fa fa-check"></i> Pilih</a>';
                $data[] = $row;
            }
            $output = array(
                "draw" => $_POST['draw'],
                "recordsTotal" => $this->m_splitLot->count_all2($lokasi),
                "recordsFiltered" => $this->m_splitLot->count_filtered2($lokasi),
                "data" => $data,
            );
            //output dalam format JSON
            echo json_encode($output);
        }else{
            die();
        }
    }

    public function generate_split()
    {
     
        if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
            // session habis
            $callback = array('message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
        }else{
            
            $username  = addslashes($this->session->userdata('username')); 
            $nama_user = $this->_module->get_nama_user($username)->row_array();
            $sub_menu  = $this->uri->segment(2);

            $dept_id        = addslashes($this->input->post('departemen'));
            $quant_id       = addslashes($this->input->post('quant_id'));
            $kode_produk    = addslashes($this->input->post('kode_produk'));
            $nama_produk    = addslashes($this->input->post('nama_produk'));
            $lot            = addslashes($this->input->post('lot'));
            $qty            = addslashes($this->input->post('qty'));
            $uom_qty        = addslashes($this->input->post('uom_qty'));
            $qty2           = addslashes($this->input->post('qty2'));
            $uom_qty2       = addslashes($this->input->post('uom_qty2'));
            $departemen     = addslashes($this->input->post('departemen'));
            $note           = addslashes($this->input->post('note'));
            $array_split    = json_decode($this->input->post('data_split'),true); 
            $tgl            = date('Y-m-d H:i:s');

            
            if(empty($dept_id)){
                $callback = array('status' => 'failed', 'field' => 'departemen', 'message' => 'Departemen Harus dipilih !', 'icon' =>'fa fa-warning',   'type' => 'danger' );
            }else if(empty($kode_produk)){
                $callback = array('status' => 'failed', 'field' => 'kode_produk', 'message' => 'Kode Produk tidak boleh kosong !', 'icon' =>'fa fa-warning',   'type' => 'danger' );
            }else if(empty($nama_produk)){
                $callback = array('status' => 'failed', 'field' => 'nama_produk', 'message' => 'Nama Produk tidak boleh kosong !', 'icon' =>'fa fa-warning',   'type' => 'danger' );
            }else if(empty($lot)){
                $callback = array('status' => 'failed', 'field' => 'lot', 'message' => 'Lot tidak boleh kosong !', 'icon' =>'fa fa-warning',   'type' => 'danger' );
            }else if(empty($qty)){
                $callback = array('status' => 'failed', 'field' => 'qty', 'message' => 'Qty1 tidak boleh kosong !', 'icon' =>'fa fa-warning',   'type' => 'danger' );
            }else if(empty($uom_qty)){
                $callback = array('status' => 'failed', 'field' => 'uom_qty', 'message' => 'Uom Qty1 tidak boleh kosong !', 'icon' =>'fa fa-warning',   'type' => 'danger' );
            }else if(empty($array_split)){
                $callback = array('status' => 'failed', 'field' => 'kode_produk', 'message' => 'Split Items masih Kosong !', 'icon' =>'fa fa-warning',   'type' => 'danger' );
            }else{

                // cek stock quant
                $sq = $this->_module->get_stock_quant_by_id($quant_id)->row_array();

                // cek lokasi sock by dept 
                $cek_lc = $this->_module->get_nama_dept_by_kode($departemen)->row_array();

                if(empty($sq['quant_id'])){
                    $callback = array('status' => 'failed', 'field' => 'kode_produk', 'message' => 'Data yang akan di Split tidak ditemukan !', 'icon' =>'fa fa-warning',   'type' => 'danger' );
                }else if($sq['lokasi'] != $cek_lc['stock_location']){/// cek lokasi
                    $callback = array('status' => 'failed', 'field' => 'kode_produk',  'message' => 'Lokasi Produk tidak sama / sudah tidak lagi di Lokasi '.$cek_lc['stock_location'].' !', 'icon' =>'fa fa-warning',   'type' => 'danger' );
                }else if(!empty($sq['reserve_move'])){/// cek apa terpesan oleh dokumen lain atau tidak
                    $callback = array('status' => 'failed', 'field' => 'kode_produk',  'message' => 'Produk sudah terpesan oleh dokumen lain !', 'icon' =>'fa fa-warning',   'type' => 'danger' );
                }else if($sq['qty'] != $qty){// cek qty yg di post dan yg ter baru
                    $callback = array('status' => 'failed',  'field' => 'qty', 'message' => 'Qty Produk tidak sama dengan Stock yang sekarang !', 'icon' =>'fa fa-warning',   'type' => 'danger' );
                }else if($sq['qty2'] != 0 AND $sq['qty2'] != $qty2){// cek qty2 yg di post dan yg ter baru
                    $callback = array('status' => 'failed', 'field' => 'qty2',  'message' => 'Qty2 Produk tidak sama Stock yang sekarang !', 'icon' =>'fa fa-warning',   'type' => 'danger' );
                }else{

                    // lock table
                    $this->_module->lock_tabel('stock_quant WRITE, stock_move WRITE, stock_move_produk WRITE, stock_move_items WRITE, adjustment WRITE, adjustment_items WRITE, split WRITE, split_items WRITE, user WRITE, log_history WRITE, main_menu_sub WRITE');

                    // cek total split qty 1 dan qty2 terhadapt qty1 dan qty2 sebelum split
                    $sum_tbl_qty1 = 0;
                    $sum_tbl_qty2 = 0;
                    foreach($array_split as $row){

                        $sum_tbl_qty1 = $sum_tbl_qty1 + $row['qty1'];
                        $sum_tbl_qty2 = $sum_tbl_qty2 + $row['qty2'];

                    }
                    
                    if(round($sum_tbl_qty1,2) != round($qty,2)){
                        $callback = array('status' => 'failed', 'field' => 'qty',  'message' => 'Qty1 Produk tidak sama dengan Qty1 yang akan di Split !', 'icon' =>'fa fa-warning',   'type' => 'danger' );
                    }else if(round($sum_tbl_qty2,2) != round($qty2,2)){
                        $callback = array('status' => 'failed', 'field' => 'qty2',  'message' => 'Qty2 Produk tidak sama dengan Qty2 yang akan di Split !', 'icon' =>'fa fa-warning',   'type' => 'danger' );
                    }else{
                        $sql_insert_items   = "";
                        $sql_adjustment     = "";
                        $sql_adjustment_items = "";
                        $sql_stock_move_batch = "";
                        $sql_stock_move_produk_batch = "";
                        $sql_stock_move_items_batch  = "";
                        $sql_stock_quant_batch = "";
                        $sql_log_history_batch = "";
                        $row_order          = 1;
                        $lot_baru           = $lot;
                        $items_empty        = true;
                        $jumlah_split       = 0;
                        $status_done        = "done";

                        // get kode split
                        $kode_split = $this->m_splitLot->get_kode_split();
                        // get move_id
                        $last_move   = $this->_module->get_kode_stock_move();
                        $move_id     = "SM".$last_move; //Set kode stock_move
                        // get quant_id
                        $start       = $this->_module->get_last_quant_id();
                        // get kode adj
                        $get_kode_adjustment   = $this->_module->get_kode_adj();  


                        $nama_departemen = $cek_lc['nama'];
                        $lokasi_adj      = $cek_lc['adjustment_location'];
                        $lokasi_stock    = $cek_lc['stock_location'];

                        // ADJ OUT
                        $kode_adjustment   = substr("0000" . $get_kode_adjustment,-4);                  
                        $kode_adjustment   = "ADJ/".date("y") . '/' .  date("m") . '/' . $kode_adjustment;

                        $note_adj_in  = 'ADJ | Dibuat dari Fitur Split. No.'.$kode_split;

                        // insert into adj 
                        $type_adjustment = 6; // 6 = split
                        $sql_adjustment .= "('".$kode_adjustment."', '".$tgl."','".$nama_departemen."','".$lokasi_stock."','".$note_adj_in."','".$status_done."','".$nama_user['nama']."', '".$type_adjustment."'), ";

                        $method         = $departemen.'|ADJ';
                        $lokasi_dari    = $lokasi_stock;
                        $lokasi_tujuan  = $lokasi_adj;

                        $qty1_move = 0 - $qty;
                        $qty2_move = 0 - $qty2;

                        // ADJ OUT
                        // insert to adj items
                        $sql_adjustment_items .= "('".$kode_adjustment."','".$quant_id."','".$kode_produk."','".$lot."','".$uom_qty."','".$qty."',0,'".$uom_qty2."','".$qty2."',0,'".$move_id."','".$qty1_move."','".$qty2_move."',$row_order), ";

                        $origin_out      = $kode_adjustment.'|'.$row_order;

                        // stock_move ADJ OUT
                        $sql_stock_move_batch .= "('".$move_id."','".$tgl."','".$origin_out."','".$method."','".$lokasi_dari."','".$lokasi_tujuan."','".$status_done."','1',''), ";
                        
                        // insert stock_move_items
                        $sql_stock_move_items_batch .= "('".$move_id."', '".$quant_id."','".($kode_produk)."', '".($nama_produk)."','".$lot."','".$qty."','".($uom_qty)."','".$qty2."','".$uom_qty2."','".$status_done."','1','','".$tgl."','','".addslashes($sq['lebar_greige'])."','".addslashes($sq['uom_lebar_greige'])."','".addslashes($sq['lebar_jadi'])."','".addslashes($sq['uom_lebar_jadi'])."'), ";
                        
                        $last_move = $last_move + 1;
                        $move_id   = "SM".$last_move;
                        $row_order++;

                        $lokasi_dari    = $lokasi_adj;
                        $lokasi_tujuan  = $lokasi_stock;

                        // insert to split items
                        foreach($array_split as $row){
                            $items_empty = false;
                            $sql_insert_items .= "('".$kode_split."','".$start."','".$row['qty1']."','".$row['uom_qty1']."','".$row['qty2']."','".$row['uom_qty2']."','".$lot_baru."','".$row_order."' ), ";

                            // ADJ IN
                            $sql_adjustment_items .= "('".$kode_adjustment."','".$start."','".$kode_produk."','".$lot_baru."','".$row['uom_qty1']."',0,'".$row['qty1']."','".$row['uom_qty2']."',0,'".$row['qty2']."','".$move_id."','".$row['qty1']."','".$row['qty2']."',$row_order), ";

                            // move
                            $origin_in      = $kode_adjustment.'|'.$row_order;

                            // insert stock_move
                            $sql_stock_move_batch .= "('".$move_id."','".$tgl."','".$origin_in."','".$method."','".$lokasi_dari."','".$lokasi_tujuan."','".$status_done."','1',''), ";

                            // insert stock_move_produk
                            $sql_stock_move_produk_batch .= "('".$move_id."','".($kode_produk)."','".($nama_produk)."','".$row['qty1']."','".$row['uom_qty1']."','".$status_done."','1',''), ";

                            // insert stock_move_items
                            $sql_stock_move_items_batch .= "('".$move_id."', '".$start."','".($kode_produk)."', '".($nama_produk)."','".addslashes(trim($lot_baru))."','".$row['qty1']."','".($row['uom_qty1'])."','".$row['qty2']."','".$row['uom_qty2']."','".$status_done."','1','','".$tgl."','','".addslashes($sq['lebar_greige'])."','".addslashes($sq['uom_lebar_greige'])."','".addslashes($sq['lebar_jadi'])."','".addslashes($sq['uom_lebar_jadi'])."'), ";

                            // insert stock_quant
                            $sql_stock_quant_batch .= "('".$start."','".$tgl."','".($kode_produk)."','".($nama_produk)."','".addslashes(trim($lot_baru))."','".addslashes($sq['nama_grade'])."','".$row['qty1']."','".$row['uom_qty1']."','".$row['qty2']."','".$row['uom_qty2']."','".$lokasi_stock."','".addslashes($sq['reff_note'])."','','','".$tgl."','".addslashes($sq['lebar_greige'])."','".addslashes($sq['uom_lebar_greige'])."','".addslashes($sq['lebar_jadi'])."','".addslashes($sq['uom_lebar_jadi'])."','".addslashes($sq['sales_order'])."','".addslashes($sq['sales_group'])."'), ";

                            $last_move = $last_move + 1;
                            $move_id   = "SM".$last_move;

                            $row_order++;
                            $jumlah_split++;
                            $start++;
                        }
                        

                        if($items_empty == false){

                            if(!empty($sql_insert_items)){
                                // insert to split
                                $this->m_splitLot->save_splitlot($kode_split,$tgl,$departemen,$quant_id,$kode_produk,$nama_produk,$lot,$qty,$uom_qty,$qty2,$uom_qty2,$note,$nama_user['nama']);

                                $sql_insert_items = rtrim($sql_insert_items,', ');
                                $this->m_splitLot->save_split_items_batch($sql_insert_items);
                            }

                            if(!empty($sql_adjustment)){
                                $sql_adjustment = rtrim($sql_adjustment, ', ');
                                $this->m_splitLot->simpan_adjustment_batch($sql_adjustment);

                                if(!empty($sql_adjustment_items)){
                                    $sql_adjustment_items = rtrim($sql_adjustment_items, ', ');
                                    $this->m_splitLot->simpan_adjustment_items_batch($sql_adjustment_items);
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
                            

                            // update lokasi stock to adj qty lama 
                            $sql_update_lokasi_stock_quant = "UPDATE stock_quant SET lokasi = '$lokasi_adj', move_date = '".$tgl."'  WHERE quant_id = '$quant_id' ";
                            $this->_module->update_reff_batch($sql_update_lokasi_stock_quant);

                            $kode_split_encr = encrypt_url($kode_split);

                            $jenis_log   = "create";
                            $note_log    = $kode_split." | ".$kode_produk."  ".$nama_produk." ".$lot." ".$qty." ".$uom_qty." ".$qty2." ".$uom_qty2." <br> Jumlah Split ".$jumlah_split;
                            $this->_module->gen_history($sub_menu, $kode_split, $jenis_log, $note_log, $username);

                            //create log history adjustment 
                            $note_log_adj_in = $kode_adjustment." ini dibuat dari Fitur Split Lot";
                            $date_log        = date('Y-m-d H:i:s');
                            $sql_log_history_batch .= "('".$date_log."','mms72','".$kode_adjustment."','create','".addslashes($note_log_adj_in)."','".$nama_user['nama']."'), ";

                            $total_adj = $jumlah_split + 1;

                            $note_log_adj_out_2 = "Generate Adjustment ini di generate otomatis dari Fitur Split Lot | Jumlah Adjustment  ".$total_adj;
                            $sql_log_history_batch .= "('".$date_log."','mms72','".$kode_adjustment."','generate','".addslashes($note_log_adj_out_2)."','".$nama_user['nama']."'), ";

                            if(!empty($sql_log_history_batch)){
                                $sql_log_history_batch = rtrim($sql_log_history_batch, ', ');
                                $this->_module->simpan_log_history_batch($sql_log_history_batch);
                            }

                            
                            $callback = array('status' => 'success','message' => 'Data Berhasil di Generate !', 'icon' =>'fa fa-check', 'type' => 'success', 'isi'=> $kode_split_encr) ;
                        }else{

                            $callback = array('status' => 'failed', 'field' => 'kode_produk',  'message' => 'Split Gagal di generate', 'icon' =>'fa fa-warning',   'type' => 'danger' );
                        }

                    }
    
    
                    // unlock table
                    $this->_module->unlock_tabel();

                    
                }


            }
        }

        echo json_encode($callback);
            
    }


}