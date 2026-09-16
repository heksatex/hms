<?php defined('BASEPATH') or exit ('No Direct Script Acces Allowed');

/**
 * 
 */
class Produksi extends MY_Controller
{

    protected $nama;
    protected $info_header = [];

	public function __construct()
	{
		parent::__construct();
		$this->is_loggedin();//cek apakah user sudah login
		$this->load->model("m_mo");//load query" di model m_mo
        $this->load->model("_module");
        $this->load->model("m_produksi");
        $nama   = $this->session->userdata('nama'); 
        $this->info_header = [
                'title' => 'HMI SYSTEM',
                'sub_title' => 'Manufacturing',
                'operator_name' => $nama['nama']
        ];
	}


    public function index()
    {
        $dept_id                        = 'PROD';
        // $data['id_dept']                = $dept_id;
        
        $sub_menu                       = $this->uri->segment(2);
        $username                       = $this->session->userdata('username'); 
        $nama                           = $this->session->userdata('nama'); 
        $kode                           = $this->_module->get_kode_sub_menu_deptid($sub_menu,$dept_id)->row_array();
        $data['akses_menu']             = $this->_module->cek_priv_menu_by_user($username,$kode['kode'])->num_rows();
        $data['departemen']             = $this->list_dept();
        $data['active_menu']            = 'departemen';
        $data['step']                   = 1;

        $data['header']                 = $this->info_header;
        // cek departemen by username
        $cek_user                       = $this->m_produksi->cek_departemen_by_user(['username'=>$username])->row();
        if($cek_user){
            $user_dept = $cek_user->dept;
            $type               = $this->m_produksi->cek_dept_by_kode(['kode'=>$user_dept])->row();
            if($type->type_dept == 'manufaktur') {
                $data['produksi_dept']      = $type->kode;
                $cek                        = $this->m_produksi->cek_dept_produksi_by_kode(['kode'=>$user_dept])->row();
                $data['nama_departemen']    = $cek->nama;
                $data['step']               = 2;
                $data['active_menu']        = 'mesin';
                $data['id_dept']            = $user_dept;
                $data['mesin']              = $this->m_produksi->get_list_mesin(['m.dept_id'=> $type->kode, 'm.status_aktif'=> 't'], '')->result_array();


                $this->load->view('produksi/v_produksi_mesin', $data);
            } else {
                $this->load->view('produksi/v_produksi_departemen', $data);      
            }
        } else{
            $this->load->view('produksi/v_produksi_departemen', $data);      
        }
    }




    public function produksiMesin($id_dept = null)
    {
        if (!$id_dept) {
            show_404();
        }
        $cek               = $this->m_produksi->cek_dept_produksi_by_kode(['kode'=>$id_dept])->row();

        $data['header']   = $this->info_header;
        $data['id_dept']  = $id_dept;
        $data['nama_departemen']    = $cek->nama;
        $data['mesin']              = $this->m_produksi->get_list_mesin(['m.dept_id'=> $id_dept, 'm.status_aktif'=> 't'], '')->result_array();

        $data['active_menu']        = 'mesin';
        $data['step']               = 2;
        $this->load->view('produksi/v_produksi_mesin',$data);
    }

    public function listMO($id_dept = null, $mc_id = null)
    {
        if (!$id_dept || !$mc_id) {
            show_404();
        }
        $cek               = $this->m_produksi->cek_dept_produksi_by_kode(['kode'=>$id_dept])->row();
        $data['header']             = $this->info_header;
        $data['mc_id']              = $mc_id;
        $data['id_dept']            = $id_dept;
        $data['active_menu']        = 'mo';
        $data['step']               = 3;
        $data['nama_departemen']    = $cek->nama;
        $data['nama_mesin']         = $this->m_produksi->cek_mesin_by_kode(['mc_id'=> $mc_id])->row_array();
        $data['list_mo']            = $this->m_produksi->get_list_mo(['mp.dept_id'=>$id_dept, 'mp.mc_id'=> $mc_id, 'mp.status'=>'ready'],'','hide')->result_array();
        $data['mesin']              = $this->m_produksi->get_list_mesin(['m.dept_id'=> $id_dept, 'm.status_aktif'=> 't', "m.mc_id"=>$mc_id], '')->row_array();

        switch ($id_dept) {

            case 'WRD':
                $this->load->view('produksi/v_produksi_departemen_mesin_wrd',$data);
                break;
            default:
                $this->load->view('produksi/v_produksi_departemen_mesin_empty',$data);
                break;
        }
    }

    public function produksiHPH($id_dept = null, $mc_id = null, $kodeMO = null)
    {
        if (!$id_dept || !$mc_id || !$kodeMO ) {
            show_404();
        }
        $cek                        = $this->m_produksi->cek_dept_produksi_by_kode(['kode'=>$id_dept])->row();
        $data['header']             = $this->info_header;
        $data['mc_id']              = $mc_id;
        $data['id_dept']            = $id_dept;
        $data['active_menu']        = 'produksihph';
        $data['step']               = 4;
        $data['nama_departemen']    = $cek->nama;
        $data['nama_mesin']         = $this->m_produksi->cek_mesin_by_kode(['mc_id'=> $mc_id])->row_array();
        $list_mo                    = $this->m_produksi->get_list_mo(['mp.dept_id'=>$id_dept, 'mp.mc_id'=> $mc_id, 'mp.status'=>'ready', 'mp.kode' => $kodeMO])->row_array();
        if(empty($list_mo)) {
            show_404();
        }

        $data['data_mo']            = $list_mo;
        $data['kode_mo']            = $kodeMO;
        $ex = explode('|', $list_mo['reff_note']);
        $mo_knitting = trim($ex[1] ?? '');
        $mc_knitting = trim($ex[2] ?? '');
        $gb          = trim($ex[4] ?? '');
        $jml_beam     = trim($ex[5] ?? '');

        $data['mo_knitting']    = $mo_knitting;
        $data['mc_knitting']    = $mc_knitting;
        $data['jml_beam']       = $jml_beam .' - '.$gb;
        $data['total_target']   = $list_mo['qty'];
        $data['sudah_dibuat']   = $list_mo['qty_produced'];
        $data['sisa_target']    = $list_mo['qty'] - $list_mo['qty_produced'];
        $data['list_grade']     = $this->_module->get_list_grade();
        $data['list_cacat'] = $this->m_mo->get_list_cacat($id_dept);

        switch ($id_dept) {
            case 'WRD':
                $this->load->view('produksi/v_produksi_hph_wrd',$data);
                break;
            default:
                $this->load->view('produksi/v_produksi_departemen_mesin_empty',$data);
                break;
        }

    }

    public function get_list_lot_fg()
    {

    }

    public function loadLot()
    {
        try {

            $kode = $this->input->post('kode');
            $id_dept = $this->input->post('id_dept');

            if (empty($kode)) {
                throw new Exception('MO tidak ditemukan');
            }

            $lw        = $this->m_mo->get_location_waste_by_deptid($id_dept)->row_array();
            $hasil_fg  = $this->m_produksi->get_list_barang_jadi_hasil($kode,$lw['waste_location']);

            $result = [];

            foreach ($hasil_fg as $row) {

                $info = $row->reff_note;
                preg_match_all('/(UA|UI|W):([^\s]+)/', $info, $matches);

                $info_data = array_combine($matches[1], $matches[2]);
                $seri_beam = '';
                if (!empty($row->lot)) {
                    $seri = explode('-', $row->lot);
                    $seri_beam = end($seri);
                }

                $lot_adj = false;
                if($row->lot_adj){
                    $lot_adj = true;
                }

                $result[] = [
                    'quant_id'=>$row->quant_id,
                    'lot_adj'=> $lot_adj,
                    'lot'   => $row->lot,
                    'time'  => $row->create_date,

                    'seri_beam'  => $seri_beam,
                    'grade' => $row->nama_grade,

                    'qty'   => $row->qty,
                    'uom'    => $row->uom,
                    'qty2'  => $row->qty2,
                    'uom2'  => $row->uom2,
                    
                    'ua' => $info_data['UA'] ?? '',
                    'ui' => $info_data['UI'] ?? '',
                    'w'  => $info_data['W'] ?? ''
                ];
            }

            echo json_encode([
                'success' => true,
                'message' => 'Data LOT berhasil diambil',
                'data'    => $result
            ]);

        } catch (Exception $e) {

            echo json_encode([
                'success' => false,
                'message' => $e->getMessage(),
                'data'    => []
            ]);
        }
    }

    function list_dept()
    {   

        $data = [
            ['nama' => 'Twisting',        'icon' => 'fa-cog',       'disabled' => true],
            ['nama' => 'Warping Dasar',   'icon' => 'fa-cog',       'disabled' => false,      'url' => site_url('manufacturing/produksi/produksiMesin/WRD')],
            ['nama' => 'Warping Panjang', 'icon' => 'fa-cog',       'disabled' => true],
            ['nama' => 'Tricot',          'icon' => 'fa-cog',       'disabled' => false,      'url' => site_url('manufacturing/produksi/produksiMesin/TRI')],
            ['nama' => 'Jacquard',        'icon' => 'fa-cog',       'disabled' => true],
            ['nama' => 'Inspecting',      'icon' => 'fa-search',    'disabled' => true],
            ['nama' => 'Cutting',         'icon' => 'fa-scissors',  'disabled' => true],
            ['nama' => 'Searing',         'icon' => 'fa-fire',      'disabled' => true],
        ];
        return $data;
    }
    

    public function save_hph_hmi()
    {
        $transaction_started = false;
        try{
            if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
                // session habis
                throw new \Exception('Waktu Anda Telah Habis ', 401);
            }else{

                $sub_menu   = "mO"; //$this->uri->segment(2);
                $username   = addslashes($this->session->userdata('username')); 
                $nama       = $this->session->userdata('nama'); 
                $nama_user  = $nama['nama'];

                $kode        = trim($this->input->post('kode', true));
                $origin_mo   = trim($this->input->post('origin_mo', true));
                $kode_produk = trim($this->input->post('kode_produk', true));
                $nama_produk = trim($this->input->post('nama_produk', true));
                $deptid      = trim($this->input->post('deptid', true));
                $data_fg_raw = $this->input->post('data_fg', true);
                $data_defect_raw = $this->input->post('data_defect', true);
                $printLot     = $this->input->post('printLot');

                // ========================================
                // VALIDASI POST UTAMA
                // ========================================

                if ($kode === '') {
                    throw new \Exception('Kode MO tidak boleh kosong !', 400);
                }
                

                if ($origin_mo === '') {
                     throw new \Exception('Origin MO tidak boleh kosong !', 400);
                }

                if ($kode_produk === '') {
                     throw new \Exception('Kode produk tidak boleh kosong !', 400);
                }

                if ($nama_produk === '') {
                    throw new \Exception('Nama produk tidak boleh kosong !', 400);
                }

                if ($deptid === '') {
                    throw new \Exception('Department tidak boleh kosong !', 400);
                }

                if ($data_fg_raw === '') {
                    throw new \Exception('Data produksi tidak ditemukan !', 400);
                }

                $array_fg = json_decode($data_fg_raw, true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new \Exception('Format data produksi tidak valid !', 400);
                }

                if (!is_array($array_fg) || empty($array_fg)) {
                    throw new \Exception('Data produksi tidak boleh kosong !', 400);
                }

                if (!isset($array_fg[0]) || !is_array($array_fg[0])) {
                    throw new \Exception('Format data FG tidak valid !', 400);
                }

                $array_defect = json_decode($data_defect_raw, true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new \Exception('Format data cacat tidak valid !', 400);
                }


                foreach ($array_fg as $index => $fg) {

                    $no = $index + 1;

                    $required_fg = [
                        'kode_produk' => 'Kode produk',
                        'nama_produk' => 'Nama produk',
                        'beam'        => 'Beam',
                        'lot'         => 'Lot',
                        'grade'       => 'Grade',
                        'qty'         => 'Qty',
                        'uom'         => 'UOM',
                        'qty2'        => 'Qty 2',
                        'uom2'        => 'UOM 2',
                        'reff_note'   => 'Reference note'
                    ];

                    foreach ($required_fg as $field => $label) {

                        if (
                            !isset($fg[$field]) ||
                            trim((string)$fg[$field]) === ''
                        ) {
                            throw new \Exception(
                                $label . ' pada data ke-' . $no . ' tidak boleh kosong !',
                                400
                            );
                        }
                    }

                    if ($fg['nama_produk'] !== $nama_produk) {
                        throw new \Exception(
                            'Nama produk data produksi tidak sesuai dengan MO !',
                            400
                        );
                    }

                    if ($fg['kode_produk'] !== $kode_produk) {
                        throw new \Exception(
                            'Kode produk data produksi tidak sesuai dengan MO !',
                            400
                        );
                    }

                    if (!is_numeric($fg['qty'])) {
                        throw new \Exception(
                            'Qty pada data ke-' . $no . ' harus berupa angka !',
                            400
                        );
                    }

                    if ((float)$fg['qty'] <= 0) {
                        throw new \Exception(
                            'Qty pada data ke-' . $no . ' harus lebih besar dari 0 !',
                            400
                        );
                    }

                    if (!is_numeric($fg['qty2'])) {
                        throw new \Exception(
                            'Qty 2 pada data ke-' . $no . ' harus berupa angka !',
                            400
                        );
                    }

                    if ((float)$fg['qty2'] < 0) {
                        throw new \Exception(
                            'Qty 2 pada data ke-' . $no . ' tidak boleh negatif !',
                            400
                        );
                    }
                }



                $tgl          = date('Y-m-d H:i:s');
                $status_done  = 'done';

                
                // start transaction
                $this->m_produksi->startTransaction();
                $transaction_started = true;

                // lock table
                $this->_module->lock_tabel("mrp_production WRITE, stock_quant WRITE, mrp_production_fg_hasil WRITE, mrp_production_fg_target WRITE, stock_move WRITE,  stock_move_items WRITE, sales_contract WRITE, pengiriman_barang WRITE,  pengiriman_barang_items WRITE,  penerimaan_barang WRITE, penerimaan_barang_items WRITE, mrp_production_rm_target WRITE,  departemen WRITE, log_history WRITE, user READ,  main_menu_sub READ, stock_move_produk WRITE, mrp_production_fg_hasil as fg WRITE, mrp_production as mrp WRITE, mrp_production_cacat WRITE, mrp_production as mp WRITE, mst_status ms READ, mst_produk prod READ, stock_quant as sq WRITE, mrp_production as mph WRITE, stock_move_items as smi WRITE, stock_move as sm READ, pengiriman_barang as pb WRITE, mrp_production_rm_target as rm WRITE, mst_produk as mprod  WRITE, stock_move_items as smi2 WRITE, mrp_production_rm_hasil WRITE, product_planning  as pp WRITE");

                // cek status mrp_production
                $cek = $this->m_produksi->cek_mrp_production(['kode'=>$kode])->row();

                if(!$cek){
                    throw new \Exception('Data MO tidak ditemukan !', 400);
                } else if ($cek->status == 'done'){
                    throw new \Exception('Data Tidak Bisa Disimpan, Status MO Sudah Done !', 400);
                } else if ($cek->status == 'cancel'){
                    throw new \Exception('Data Tidak Bisa Disimpan, Status MO Sudah Batal !', 400);
                } else if ($cek->status == 'hold'){
                    throw new \Exception('Data Tidak Bisa Disimpan, Status MO di Hold !', 400);
                } else {

                    if(empty($cek->lot_prefix)) {
                        throw new \Exception('Data Tidak Bisa Disimpan, Lot Prefix tidak boleh kosong !', 400);
                    }

                    //get last quant id
                    $start          = $this->_module->get_last_quant_id();
                    $get_ro         = $this->m_mo->get_row_order_fg_hasil($kode)->row_array();
                    $row_order      = $get_ro['row']+1;
                    $status_ready   = 'ready';

                    $move_fg    = $this->m_mo->get_move_id_fg_target_by_kode($kode)->row_array();
                    $move_id_fg = $move_fg['move_id'];
                    
                    //lokasi tujuan fg
                    $lokasi_fg  = $this->_module->get_location_by_move_id($move_id_fg)->row_array();
                    $sm_tj      = $this->_module->get_stock_move_tujuan_mo($move_id_fg,$origin_mo,'done','cancel')->row_array();

                    $sm_tj_move_id = !empty($sm_tj['move_id']) ? $sm_tj['move_id'] : '';
                
                    //get row order stock_move_items tujuan
                    $row_order_smi_tujuan  = $this->_module->get_row_order_stock_move_items_by_kode($sm_tj_move_id);

                    //get row order stock_move_items produksi
                    $row_order_smi      = $this->_module->get_row_order_stock_move_items_by_kode($move_id_fg);

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


                     //cek jika kode produk/nama produk tidak kosong
                    if(!empty($kode_produk) AND !empty($nama_produk) AND count($array_fg) > 0 ){
                        $hasil_produksi = TRUE;

                        //untuk mendapatkan origin_prod yang terdapat consume kedepannya atau consume lebih dari 1
                        $loop_sm    = true;
                        $loop_count = 1;
                        $origin_prod_tj = "";
                        $next       = false;
                        $con_next   = false;
                        $con        = false;
                        $data_fg_hasil = array();
                        $data_stock_quant  = array();
                        $data_stock_move_items = array();
                        $data_fg_cacat = array();
                        $data_rm_hasil = array();
                        $case_qty_smi = "";
                        $case_qty2_smi = "";
                        $case_status_smi = "";
                        $where_smi = array();
                        $case_qty_stock = "";
                        $case_qty2_stock = "";
                        $case_lokasi_stock = "";
                        $case_movedate_stock = "";
                        $case_movedate_smi  = "";
                        $where_stock = array();
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
                        $defect_row = 1;
                        $defect_row_map = array();
                        $tmp_print  = [];
                        $total_fg_qty1 = 0;
                        $total_fg_qty2 = 0;
                        foreach ($array_fg as $fg) {

                            $lot = trim($fg['lot']);
                            $defect_key = $kode . '|' . $lot;

                             if (!isset($defect_row_map[$defect_key])) {

                                $ro = $this->m_mo->get_row_order_rekam_cacat($kode, $lot)->row_array();

                                $defect_row_map[$defect_key] =!empty($ro['row']) ? ((int)$ro['row'] + 1) : 1;
                            }


                            $data_fg_hasil[] =  array(
                                        'kode'      => $kode,
                                        'move_id'   => $move_id_fg,
                                        'quant_id'  => $start,
                                        'create_date'   => $tgl,
                                        'kode_produk'   => $fg['kode_produk'],
                                        'nama_produk'   => $fg['nama_produk'],
                                        'lot'           => trim($fg['lot']),
                                        'nama_grade'    => $fg['grade'],
                                        'qty'           => $fg['qty'],
                                        'uom'           => $fg['uom'],
                                        'qty2'          => $fg['qty2'],
                                        'uom2'          => $fg['uom2'],
                                        'lokasi'        => $lokasi_fg['lokasi_tujuan'],
                                        'nama_user'     => $nama_user,
                                        'sales_order'   => $sales_order,
                                        'consume'       => 'yes',
                                        'row_order'     => $row_order
                            );

                            $total_fg_qty1 = $total_fg_qty1 + $fg['qty'];
                            $total_fg_qty2 = $total_fg_qty2 + $fg['qty2'];
    
                            $data_stock_quant[] = array(
                                        'quant_id'      => $start,
                                        'create_date'   => $tgl,
                                        'move_date'     => $tgl,
                                        'kode_produk'   => $fg['kode_produk'],
                                        'nama_produk'   => $fg['nama_produk'],
                                        'lot'           => trim($fg['lot']),
                                        'nama_grade'    => $fg['grade'],
                                        'qty'           => $fg['qty'],
                                        'uom'           => $fg['uom'],
                                        'qty2'          => $fg['qty2'],
                                        'uom2'          => $fg['uom2'],
                                        'lokasi'        => $lokasi_fg['lokasi_tujuan'],
                                        'reff_note'     => $fg['reff_note'],
                                        'reserve_move'  => $sm_tj_move_id,
                                        'reserve_origin'=> $origin_mo,
                                        'sales_order'   => $sales_order,
                                        'sales_group'   => $sales_group
                            );
    
                            $data_stock_move_items[] = array(
                                        'move_id'       => $move_id_fg,
                                        'quant_id'      => $start,
                                        'tanggal_transaksi' => $tgl,
                                        'kode_produk'   => $fg['kode_produk'],
                                        'nama_produk'   => $fg['nama_produk'],
                                        'lot'           => trim($fg['lot']),
                                        'qty'           => $fg['qty'],
                                        'uom'           => $fg['uom'],
                                        'qty2'          => $fg['qty2'],
                                        'uom2'          => $fg['uom2'],
                                        'origin_prod'   => '',
                                        'status'            => $status_done,
                                        'row_order'         => $row_order_smi
                            );

                            $tmp_print[] = array('quant_id'=>$start, 'lot'=>trim($fg['lot']));

                            foreach($array_defect as $def) {

                                $data_fg_cacat[] = array(
                                            'kode'          => $kode,
                                            'quant_id'      => $start,
                                            'create_date'   => $tgl,
                                            'lot'           => $fg['lot'],
                                            'dept_id'       => $deptid,
                                            'point_cacat'   => $def['mtr'],
                                            'kode_cacat'    => $def['kode_cacat'],
                                            'nama_user'     => $nama_user,
                                            'row_order'     => $defect_row_map[$defect_key],
                                );

                                $defect_row_map[$defect_key]++;
                            }


                        
                            if($sm_tj_move_id != ''){ // jika stock_move tujuan nya tidak kosong maka insert ke smi

                                // cek method apakakah OUT,IN,CON
                                $mthd          = explode('|',$sm_tj['method']);
                                $origin_prod   = '';
                                //$method_dept   = trim($mthd[0]);
                                $method_action = trim($mthd[1]);//OUT,IN,CON
                                if($method_action == 'OUT'){
                                    // stock_move_tujuan = pengiriman barang
                                    $sm_tj_move_id;
                                    $kode_out = $this->_module->get_kode_pengiriman_by_move_id($sm_tj_move_id)->row_array();
                                    
                                    // get origin_prod by kode
                                    $op = $this->m_mo->get_origin_prod_pengiriman_barang_by_kode($kode_out['kode'],addslashes($kode_produk))->row_array();
                                    if (!empty($op)) {
                                        $origin_prod = $op['origin_prod'];
                                    }

                                    //update status pengiriman barang
                                    //$get_kode_out = $this->_module->get_kode_pengiriman_by_move_id($sm_tj_move_id)->row_array();
                                    if(!empty($kode_out['kode'])){
                                        //update pengiriman barang items = ready
                                        $case8  .= "when kode = '".$kode_out['kode']."' then '".$status_ready."'";
                                        $where8 .= "'".$kode_out['kode']."',"; 
                                    }

                                }else if($method_action == 'IN'){
                                    // get kode penerimaan barang by move_id
                                    $kode_in = $this->_module->get_kode_penerimaan_by_move_id($sm_tj_move_id)->row_array();
                                    
                                    // get origin_prod by kode
                                    $op = $this->m_mo->get_origin_prod_penerimaan_barang_by_kode($kode_in['kode'],addslashes($kode_produk))->row_array();
                                    if (!empty($op)) {
                                        $origin_prod = $op['origin_prod'];
                                    }

                                    //update status penerimaan barang
                                    if(!empty($kode_in['kode'])){
                                        //update penerimaan barang items = ready
                                        $case9  .= "when kode = '".$kode_in['kode']."' then '".$status_ready."'";
                                        $where9 .= "'".$kode_in['kode']."',"; 
                                    }
                                }else if($method_action == 'CON'){
                                    // get origin prod by kode 
                                    $op = $this->m_mo->get_origin_prod_mrp_production_by_kode_mrp($kode,addslashes($kode_produk))->row_array();
                                    if (!empty($op)) {
                                        $origin_prod = $op['origin_prod'];
                                    }

                                    // update status mrp_production 
                                    if(!empty($kode)){
                                        // update mrp_production dan rm target
                                        $case10  .= "when kode = '".$kode."' then '".$status_ready."'";
                                        $where10 .= "'".$kode."',"; 
                                        $where10x = $kode_produk;
                                    }
                                }

                                //stock move items tujuan
                                $data_stock_move_items[] = array(
                                            'move_id'       => $sm_tj_move_id,
                                            'quant_id'      => $start,
                                            'tanggal_transaksi' => $tgl,
                                            'kode_produk'   => $fg['kode_produk'],
                                            'nama_produk'   => $fg['nama_produk'],
                                            'lot'           => trim($fg['lot']),
                                            'qty'           => $fg['qty'],
                                            'uom'           => $fg['uom'],
                                            'qty2'          => $fg['qty2'],
                                            'uom2'          => $fg['uom2'],
                                            'origin_prod'   => $origin_prod,
                                            'status'        => $status_ready,
                                            'row_order'    => $row_order_smi_tujuan
                                );

                                    
                                //update status stock move,stock move dan stock move produk  pengiriman brg, penerimaanbarang, mrp_production_rm_target == ready
                                $case7  .= "when move_id = '".$sm_tj_move_id."' then '".$status_ready."'";
                                $where7 .= "'".$sm_tj_move_id."',";

                            }

                            $cek_dl     = $this->m_mo->cek_validasi_double_lot_by_dept($deptid);

                            //cek lot apa pernah diinput ?
                            if($cek_dl == 'true'){
                                $lot = $fg['lot'];
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
                            $row_order_smi++;
                            $row_order++;
                            $start++;                        
                        }

                        $cek_qty_hasil  = $this->m_mo->get_qty_mrp_production_fg_hasil($kode)->row_array();

                        if(round($cek->qty,2) <= round($cek_qty_hasil['sum_qty'],2)) {
                            throw new \Exception('Data tidak bisa disimpan, Qty sudah melebihi target !', 400);
                        }


                        if($sm_tj_move_id != ''){ // jika stock_move tujuan nya tidak kosong maka update pengiriman barang

                            //update status pengiriman barang
                            $get_kode_out = $this->_module->get_kode_pengiriman_by_move_id($sm_tj_move_id)->row_array();
                            if(!empty($get_kode_out['kode'])){
                                //update pengiriman barang items = ready
                                $case8  .= "when kode = '".$get_kode_out['kode']."' then '".$status_ready."'";
                                $where8 .= "'".$get_kode_out['kode']."',"; 
                            }
                        }

                        // kumpulan move_id rm
                        $list_sm_rm = $this->m_produksi->get_move_id_rm_target_by_kode($kode)->result();

                        $get_ro      = $this->m_mo->get_row_order_rm_hasil($kode)->row_array();
                        $row_order_rm= $get_ro['row']+1;

                        //simpan rm hasil
                        $move_arr     = [];
                        $move_id_rm   = '';
                        // get list row order by move_id;list_product_smi 
                        foreach($list_sm_rm as $listsm){
                            $move_id_rm  = $listsm->move_id; // get salah satu move_id
                            $row_order   = $this->_module->get_row_order_stock_move_items_by_kode($listsm->move_id); // row yang sudah + 1
                            $move_arr[]  = array('move_id' => $listsm->move_id, 'row_order' => $row_order);
                        }

                        $lokasi_rm = $this->_module->get_location_by_move_id($move_id_rm)->row_array();

                        if(empty($lokasi_rm)) {
                            throw new \Exception('Lokasi Tujuan hasil konsumsi bahan baku tidak ditemukan!', 400);
                        }

                        $con = $this->m_produksi->get_konsumsi_bahan($kode, 'ready');

                        $total_input_fg_qty1 = round($total_fg_qty1, 2);
                        $total_input_fg_qty2 = round($total_fg_qty2, 2);

                        $qty_target_all = round($cek->qty, 2);


                        /*
                        |--------------------------------------------------------------------------
                        | HITUNG TOTAL KEBUTUHAN BAHAN BAKU
                        |--------------------------------------------------------------------------
                        |
                        | Contoh:
                        |
                        | Target produksi       = 135417 mtr
                        | Total kebutuhan RM    = 728.54 Kg
                        | FG yang diproses      = 11354 mtr
                        |
                        | Kebutuhan RM:
                        |
                        | (728.54 / 135417) * 11354
                        | = 61.08 Kg
                        |
                        */

                        $total_qty_rm = 0;
                        foreach ($con as $cons) {
                            $total_qty_rm = round($cons->qty_rm, 2);
                        }


                        $qty_need = 0;
                        if ($qty_target_all > 0) {
                            $qty_need = round(($total_qty_rm / $qty_target_all) * $total_input_fg_qty1, 2);
                        }

                        $sisa_qty_need = $qty_need;


                        /*
                        |--------------------------------------------------------------------------
                        | LOOP STOCK / LOT
                        |--------------------------------------------------------------------------
                        */

                        foreach ($con as $cons) {

                            if ($sisa_qty_need <= 0) {
                                break;
                            }

                            $qty_smi  = round($cons->qty, 2);
                            $qty2_smi = round($cons->qty2, 2);

                            $qty_rm      = round($cons->qty_rm, 2);
                            $jml_produk  = $cons->jml_produk;


                            if ($qty_smi <= 0) {
                                // continue;
                                throw new \Exception('Bahan Baku tidak Valid, Qty1 Bahan Baku  tidak boleh 0 ! ', 400);
                            }

                            $qty_consume = round(min($sisa_qty_need, $qty_smi), 2);
                           
                            if ($qty_consume <= 0) {
                                // continue;
                                throw new \Exception('Konsumsi Bahan Baku  tidak valid  ! ', 400);
                            }

                            if ($qty_smi > 0 && $qty2_smi > 0) {
                                $qty2_new = round(($qty2_smi / $qty_smi) * $qty_consume,2);
                            } else {
                                $qty2_new = 0;
                            }


                            /*
                            |--------------------------------------------------------------------------
                            | CEK APAKAH STOCK HABIS
                            |--------------------------------------------------------------------------
                            */

                            $stock_habis = ($qty_consume >= $qty_smi);


                         
                            $loop = 0;

                            $row_order      = 0;
                            $row_order_push = 1;

                            foreach ($move_arr as $mv_row) {
                           
                                if (isset($mv_row['move_id']) && $mv_row['move_id'] == $cons->move_id) {

                                    $row_order = $mv_row['row_order'];
                                    $row_order_push = $mv_row['row_order'] + 1;
                                   
                                    array_splice($move_arr,$loop,1);
                                    array_push($move_arr,array('move_id'   => $cons->move_id,'row_order' => $row_order_push));
                                    break;
                                }
                                $loop++;
                            }


                            /*
                            |--------------------------------------------------------------------------
                            | ==============================================================
                            | STOCK QUANT
                            | ==============================================================
                            |
                            | ADA 2 KONDISI:
                            |
                            | 1. STOCK HABIS
                            |    ----------------
                            |    stock   = 58.73
                            |    consume = 58.73
                            |
                            |    → TIDAK INSERT STOCK QUANT
                            |    → UPDATE LOKASI QUANT LAMA
                            |
                            |
                            | 2. STOCK TIDAK HABIS
                            |    ------------------
                            |    stock   = 4.45
                            |    consume = 2.35
                            |
                            |    → UPDATE QUANT LAMA = 2.10
                            |    → INSERT QUANT BARU = 2.35
                            |
                            |--------------------------------------------------------------------------
                            */

                            $esc_move_id = $this->db->escape($cons->move_id);
                            $esc_lokasi_tujuan = $this->db->escape($lokasi_rm['lokasi_tujuan']);
                            $esc_status_done = $this->db->escape($status_done);
                            $esc_tgl = $this->db->escape($tgl);

                            if ($stock_habis) {

                                $case_qty_stock    .= " WHEN quant_id = {$cons->quant_id} THEN {$qty_smi} ";
                                $case_lokasi_stock .= " WHEN quant_id = {$cons->quant_id} THEN {$esc_lokasi_tujuan} ";
                                $case_movedate_stock .= " WHEN quant_id = {$cons->quant_id} THEN {$esc_tgl} ";
                                $where_stock[]      = "(quant_id = {$cons->quant_id})";

                                $case_qty_smi   .= " WHEN move_id = {$esc_move_id} AND quant_id = {$cons->quant_id} THEN {$qty_smi} ";
                                $case_qty2_smi  .= " WHEN move_id = {$esc_move_id} AND quant_id = {$cons->quant_id} THEN {$qty2_smi} ";
                                $case_status_smi.= " WHEN move_id = {$esc_move_id} AND quant_id = {$cons->quant_id} THEN {$esc_status_done} ";
                                $case_movedate_smi .= " WHEN move_id = {$esc_move_id} AND quant_id = {$cons->quant_id} THEN {$esc_tgl} ";
                                $where_smi[]     = "( move_id = {$esc_move_id} AND quant_id = {$cons->quant_id} )";

                                $data_rm_hasil[] = array(
                                    'kode'        => $kode,
                                    'move_id'     => $cons->move_id,
                                    'quant_id'    => $cons->quant_id,
                                    'kode_produk' => $cons->kode_produk,
                                    'nama_produk' => $cons->nama_produk,

                                    'lot'         => trim($cons->lot),
                                    'qty'         => $qty_smi,
                                    'uom'         => $cons->uom,

                                    'origin_prod' => $cons->origin_prod,
                                    'additional'  => $cons->additional,
                                    'row_order'   => $row_order_rm
                                );
                                $row_order_rm++;
                            
                                // $data_stock_move_items[] = array(
                                //     'move_id'           => $cons->move_id,
                                //     'quant_id'          => $cons->quant_id,
                                //     'tanggal_transaksi' => $tgl,
                                //     'kode_produk'       => $cons->kode_produk,
                                //     'nama_produk'       => $cons->nama_produk,
                                //     'lot'               => trim($cons->lot),
    
                                //     'qty'               => $qty_smi,
                                //     'uom'               => $cons->uom,
                                //     'qty2'              => $qty2_smi,
                                //     'uom2'              => $cons->uom2,
    
                                //     'origin_prod'       => $cons->origin_prod,
                                //     'status'             => $status_done,
                                //     'row_order'          => $row_order
                                // );


                            } else {

                                $qty_sisa = round($qty_smi - $qty_consume,2);
                                $qty2_sisa = round($qty2_smi - $qty2_new,2);

                                // update stock quant 
                                $case_qty_stock  .= " WHEN quant_id = {$cons->quant_id} THEN {$qty_sisa} ";
                                $case_qty2_stock .= " WHEN quant_id = {$cons->quant_id} THEN {$qty2_sisa} ";
                                $case_movedate_stock .= " WHEN quant_id = {$cons->quant_id} THEN {$esc_tgl} ";
                                $where_stock[]    = "(quant_id = {$cons->quant_id})";

                                // update data stock move items
                                $case_qty_smi  .= " WHEN move_id = {$esc_move_id} AND quant_id = {$cons->quant_id} THEN {$qty_sisa} ";
                                $case_qty2_smi .= " WHEN move_id = {$esc_move_id} AND quant_id = {$cons->quant_id} THEN {$qty2_sisa} ";
                                $where_smi[] = "(move_id = {$esc_move_id} AND quant_id = {$cons->quant_id})";

                                $data_stock_quant[] = array(
                                    'quant_id'       => $start,
                                    'create_date'    => $tgl,
                                    'move_date'      => $tgl,
                                    'kode_produk'    => $cons->kode_produk,
                                    'nama_produk'    => $cons->nama_produk,
                                    'lot'            => trim($cons->lot),
                                    'nama_grade'     => $cons->nama_grade,

                                    'qty'            => $qty_consume,
                                    'uom'            => $cons->uom,
                                    'qty2'           => $qty2_new,
                                    'uom2'           => $cons->uom2,

                                    'lokasi'         => $lokasi_rm['lokasi_tujuan'],
                                    'reff_note'      => $cons->reff_note,
                                    'reserve_move'   => $cons->move_id,
                                    'reserve_origin' => $origin_mo,
                                    'sales_order'    => $cons->sales_order,
                                    'sales_group'    => $cons->sales_group
                                );

                                $data_rm_hasil[] = array(
                                    'kode'        => $kode,
                                    'move_id'     => $cons->move_id,
                                    'quant_id'    => $start,
                                    'kode_produk' => $cons->kode_produk,
                                    'nama_produk' => $cons->nama_produk,

                                    'lot'         => trim($cons->lot),
                                    'qty'         => $qty_consume,
                                    'uom'         => $cons->uom,

                                    'origin_prod' => $cons->origin_prod,
                                    'additional'  => $cons->additional,
                                    'row_order'   => $row_order_rm
                                );
                                $row_order_rm++;

                                $data_stock_move_items[] = array(
                                    'move_id'           => $cons->move_id,
                                    'quant_id'          => $start,
                                    'tanggal_transaksi' => $tgl,
                                    'kode_produk'       => $cons->kode_produk,
                                    'nama_produk'       => $cons->nama_produk,
                                    'lot'               => trim($cons->lot),
    
                                    'qty'               => $qty_consume,
                                    'uom'               => $cons->uom,
                                    'qty2'              => $qty2_new,
                                    'uom2'              => $cons->uom2,
    
                                    'origin_prod'       => $cons->origin_prod,
                                    'status'             => $status_done,
                                    'row_order'          => $row_order
                                );
                            }
                          

                       
                            $sisa_qty_need = round($sisa_qty_need - $qty_consume,2);
                            $start++;

                            /*
                            |--------------------------------------------------------------------------
                            | PENGAMAN FLOATING POINT
                            |--------------------------------------------------------------------------
                            */

                            if ($sisa_qty_need < 0) {
                                $sisa_qty_need = 0;
                            }

                            /*
                            |--------------------------------------------------------------------------
                            | DEBUG
                            |--------------------------------------------------------------------------
                            |
                            | Bisa dibuka kalau mau cek proses.
                            |
                            */
                            /* 
                            log_message(
                                'error',
                                'LOT: ' . $cons->lot .
                                ' | STOCK: ' . $qty_smi .
                                ' | CONSUME: ' . $qty_consume .
                                ' | HABIS: ' . ($stock_habis ? 'YES' : 'NO') .
                                ' | SISA KEBUTUHAN: ' . $sisa_qty_need
                            ); */
                           
                        }

                        // log_message('error', "Stock quant = ".print_r($data_stock_quant,true)." + StockMove items = ".print_r($data_stock_move_items,true)." + mrp_rm_hasil = ".print_r($data_rm_hasil,true));


                        // insert and updatef
                        if(!empty($data_fg_hasil) && !empty($data_stock_quant) && !empty($data_stock_move_items) && !empty($data_rm_hasil)) {

                            if (!$this->m_produksi->save_batch('mrp_production_fg_hasil', $data_fg_hasil)) {
                                throw new \Exception('Gagal menyimpan hasil produksi !', 500);
                            }

                            if(!$this->m_produksi->save_batch('mrp_production_rm_hasil', $data_rm_hasil)) {
                                throw new \Exception('Gagal menyimpan bahan baku dikonsum !', 500);
                            }

                            if (!$this->m_produksi->save_batch('stock_quant', $data_stock_quant)) {
                                throw new \Exception('Gagal menyimpan stock quant !', 500);
                            }

                            if (!$this->m_produksi->save_batch('stock_move_items', $data_stock_move_items)) {
                                throw new \Exception('Gagal menyimpan stock move items !', 500);
                            }

                            if(!empty($data_fg_cacat)){
                                if (!$this->m_produksi->save_batch('mrp_production_cacat', $data_fg_cacat)) {
                                    throw new \Exception('Gagal menyimpan data cacat !', 500);
                                }
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

                            $set_stock = [];
                            if ($case_qty_stock !== '') {
                                $set_stock[] = " qty = CASE {$case_qty_stock} ELSE qty END ";
                            }

                            if ($case_qty2_stock !== '') {
                                $set_stock[] = " qty2 = CASE {$case_qty2_stock} ELSE qty2 END ";
                            }

                            if ($case_lokasi_stock !== '') {
                                $set_stock[] = " lokasi = CASE {$case_lokasi_stock} ELSE lokasi END ";
                            }

                            if ($case_movedate_stock !== '') {
                                $set_stock[] = " move_date = CASE {$case_movedate_stock} ELSE move_date END ";
                            }


                            if (!empty($where_stock)) {
                                $sql_stock = " UPDATE stock_quant SET " . implode(',', $set_stock) . " WHERE " . implode(' OR ', $where_stock);
                                $this->db->query($sql_stock);
                            }

                            $set_smi = [];

                            if ($case_qty_smi !== '') {
                                $set_smi[] = " qty = CASE {$case_qty_smi} ELSE qty END ";
                            }

                            if ($case_qty2_smi !== '') {
                                $set_smi[] = " qty2 = CASE {$case_qty2_smi} ELSE qty2 END ";
                            }

                            if ($case_status_smi !== '') {
                                $set_smi[] = " status = CASE {$case_status_smi} ELSE status END ";
                            }

                            
                            if ($case_movedate_smi !== '') {
                                $set_stock[] = " tanggal_transaksi = CASE {$case_movedate_smi} ELSE tanggal_transaksi END ";
                            }

                            if (!empty($where_smi) && !empty($set_smi)) {

                                $sql_smi = " UPDATE stock_move_items SET " . implode(',', $set_smi) . " WHERE " . implode(' OR ', $where_smi);
                                $this->db->query($sql_smi);
                            }


                            $case_status_smi = "";
                            $status_draft    = "draft";
                            $status_done     = "done";
                            $where_smi       = [];

                            if (!empty($con)) {

                                $esc_status_draft = $this->db->escape($status_draft);
                                $esc_status_done  = $this->db->escape($status_done);

                                foreach ($con as $cons2) {

                                    $qty_smi  = round($cons2->qty, 2);
                                    $qty2_smi = round($cons2->qty2, 2);

                                    // Raw value
                                    $origin_prod = $cons2->origin_prod;
                                    $move_id     = $cons2->move_id;

                                    // Escaped value → hanya untuk SQL manual
                                    $esc_origin_prod = $this->db->escape($origin_prod);
                                    $esc_move_id     = $this->db->escape($move_id);

                                    if ($qty_smi > 0 || $qty2_smi > 0) {

                                        // Gunakan RAW value di function
                                        $cek_smi = $this->m_mo->cek_qty_stock_move_items_by_produk($move_id,$origin_prod,'ready')->row_array();

                                        if (empty($cek_smi['jml_qty']) ||$cek_smi['jml_qty'] == '0') {
                                            $cek_smi2 = $this->m_mo->cek_qty_stock_move_items_by_produk($move_id,$origin_prod,'done')->row_array();

                                            if ($cek_smi2['jml_qty'] < $cons2->qty_rm) {
                                                // STATUS DRAFT
                                               $case_status_smi .= " WHEN origin_prod = {$esc_origin_prod} AND move_id = {$esc_move_id} THEN {$esc_status_draft} ";

                                            } else {
                                                // STATUS DONE
                                                $case_status_smi .= " WHEN origin_prod = {$esc_origin_prod} AND move_id = {$esc_move_id} THEN {$esc_status_done} ";

                                            }

                                            $where_smi[] = "(origin_prod = {$esc_origin_prod} AND move_id = {$esc_move_id}) ";
                                        }
                                    }
                                }
                            }

                            //update status barang di rm target stock_move_produk
                            if (!empty($where_smi) && !empty($case_status_smi)) {

                                $where_status = implode(' OR ', $where_smi);
                                $esc_kode = $this->db->escape($kode);

                                $sql_update_status_rm_target = " UPDATE mrp_production_rm_target 
                                    SET status = CASE {$case_status_smi} ELSE status END
                                    WHERE ( {$where_status} ) AND kode = {$esc_kode} ";
                                $this->_module->update_perbatch($sql_update_status_rm_target);
                              
                                $sql_update_status_stock_move_produk = " UPDATE stock_move_produk
                                    SET status = CASE {$case_status_smi} ELSE status END
                                    WHERE ( {$where_status} )    ";
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
                            // $this->_module->unlock_tabel();

                            if($hasil_produksi == TRUE){
                                $jenis_log   = "edit";
                                $note_log    = "Produksi Mode HMI - ". $kode.' | LOT : '.$lot;
                                $this->_module->gen_history_deptid($sub_menu, $kode, $jenis_log, $note_log, $username, $deptid);          
                            }

                            $cek_qty        = $this->m_produksi->get_list_mo(['mp.dept_id'=>$deptid, 'mp.status'=>'ready', 'mp.kode' => $kode])->row_array();

                            $sudah_dibuat  = 0;
                            $sisa_target   = 0;
                            if($cek_qty) {
                                $sudah_dibuat  = $cek_qty['qty_produced'] ;
                                $sisa_target   = $cek_qty['qty'] - $cek_qty['qty_produced'];
                            }

                            if(!empty($lot_double)){                              
                                $callback = array('status' => 'success', 'message'=>'Data Produksi Berhasil Disimpan !', 'icon' => 'fa fa-check', 'type'=>'success', 'double'=> 'yes', 'message2' => 'Lot " '.$lot_double.' " sudah pernah diinput !', 'sudah_dibuat'=> $sudah_dibuat, 'sisa_target'=> $sisa_target);
                                        
                            }else{
                                $callback = array('status' => 'success', 'message'=>'Data Produksi Berhasil Disimpan !', 'icon' => 'fa fa-check', 'type'=>'success', 'sudah_dibuat'=> $sudah_dibuat, 'sisa_target'=> $sisa_target);
                            }

                            if($printLot == 'true') {
                                $this->print($kode,$deptid,$tmp_print,$origin_mo);
                            }

                             
                        } else {
                            if(empty($data_rm_hasil)) {
                                $callback = array('status' => 'failed', 'message'=>'Data Gagal Disimpan, Bahan Baku Kosong !', 'icon' => 'fa fa-check', 'type'=>'danger');
                            } else {
                                $callback = array('status' => 'failed', 'message'=>'Data Gagal Disimpan, Bahan Baku tidak Valid !', 'icon' => 'fa fa-check', 'type'=>'danger');
                            }
                        }

                    } else { //if jika array_fg tidak kosong
                        throw new \Exception('Produk lot tidak boleh kosong ! ', 400);
                    }

                }


                if (!$this->m_produksi->finishTransaction()) {
                    throw new \Exception('Gagal Simpan Produksi Batch ', 500);
                }

                // unlock table
                $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($callback));

            }

        } catch(Exception $ex){

            if ($transaction_started) {
                $this->m_produksi->rollbackTransaction();
            }

            $this->output->set_status_header(
                    $ex->getCode() >= 100 && $ex->getCode() <= 599
                        ? $ex->getCode()
                        : 500
                )
                ->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode([
                    'status'  => 'failed',
                    'message' => $ex->getMessage(),
                    'icon'    => 'fa fa-warning',
                    'type'    => 'danger'
                ]));

        } finally {

            $this->_module->unlock_tabel();

        }
    }


    public function searchMO()
    {
        $keyword = trim($this->input->post('keyword', true));

        $id_dept = $this->input->post('id_dept', true);
        $mc_id   = $this->input->post('mc_id', true);
        $showAll = $this->input->post('showAll', true);
        $show    = 'hide';
        if($this->input->post('showAll', true) == 'true') {
            $show    = 'show';
        }

        $list_mo        = $this->m_produksi->get_list_mo(['mp.dept_id'=>$id_dept, 'mp.mc_id'=> $mc_id, 'mp.status'=>'ready'], $keyword, $show)->result_array();
        $mesin         = $this->m_produksi->get_list_mesin(['m.dept_id'=> $id_dept, 'm.status_aktif'=> 't', "m.mc_id"=>$mc_id], '')->row_array();

        $html = '';
        $number  = 1;
        foreach ($list_mo as $mo) {

            $html .= $this->load->view(
                'produksi/v_produksi_departemen_mesin_wrd_card',
                [
                    'mo'      => $mo,
                    'id_dept' => $id_dept,
                    'number'  => $number++
                ],
                true
            );

        }

        echo json_encode([
            'status' => true,
            'total'  => count($list_mo),
            'total_all'=> ($mesin) ? (int) $mesin['total_mo'] :  0,
            'html'   => $html
        ]);
    }


    public function searchMesin()
    {
        $keyword = trim($this->input->post('keyword', true));
        $id_dept = $this->input->post('id_dept', true);
        $mesin   = $this->m_produksi->get_list_mesin(['m.dept_id'=> $id_dept, 'm.status_aktif'=> 't'], $keyword)->result_array();

        $html = '';

        foreach ($mesin as $mc) {

            $html .= $this->load->view(
                'produksi/v_produksi_mesin_card',
                [
                    'mc'      => $mc,
                    'id_dept' => $id_dept
                ],
                true
            );

        }

        echo json_encode([
            'status' => true,
            'total'  => count($mesin),
            'html'   => $html
        ]);
    }

    
    public function setting()
    {
        $dept_id                        = 'PROD';
        // $data['id_dept']                = $dept_id;
        
        $sub_menu                       = $this->uri->segment(2);
        $username                       = $this->session->userdata('username'); 
        $nama                           = $this->session->userdata('nama'); 
        $kode                           = $this->_module->get_kode_sub_menu_deptid($sub_menu,$dept_id)->row_array();
        $data['user']                   = $this->m_produksi->cek_departemen_by_user(['username'=>$username])->row();
        $data['header']                 = $this->info_header;
        $data['active_menu']            = 'setting';
        $data['step']                   = 1;

        $this->load->view('produksi/v_produksi_setting', $data);      
    }


    function print_lot()
    {
        try {
            $kode    = $this->input->post('kode');
            $id_dept = $this->input->post('id_dept');
            $lots    = $this->input->post('lots');

            $origin_mo  = $this->m_mo->get_origin_mo_by_kode($kode);

            $resp = $this->print($kode,$id_dept,$lots,$origin_mo);
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Print Berhasil', 'icon' => 'fa fa-check', 'type' => 'success',"resp"=>$resp)));
            
        } catch (Exception $ex) {
            log_message('error', $ex->getMessage());
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }

    }


    function print($kode,$id_dept,$lots,$origin_mo)
    {
            $this->load->library('tspl/warpingprint'); 

            $wprint = new $this->warpingprint;
    
            $data_lots = [];
            $data_reff = [];
            $data_produk = [];
            $data_notes  = [];

            $method     = $id_dept.'|OUT';

            foreach ($lots as $item) {

                $lot      = $item['lot'];
                $quant_id = $item['quant_id'];

                $get    = $this->m_produksi->get_data_fg_hasil_by_kode($kode,$quant_id)->row_array();

                if (!empty($get)) {
                    $barcode     = $get['lot'];  
                    $nama_produk = $get['nama_produk'];
                    $reff_note   = $get['reff_note'];
                }else{
                    $barcode     = "Not Found";
                    $nama_produk = "Not Found";
                    $reff_note   = "Not Found";
                }

                $reff_picking  = $this->m_mo->get_reff_picking_pengiriman_by_kode($barcode, $method, $origin_mo);


                $data_lots[] = $barcode;
                $data_reff[] = $reff_picking ?? '-';
                $data_produk[] = $nama_produk;
                $data_notes[]  = $reff_note;

            }

            $wprint->setup(3.14, 2.36, 0.11);
            foreach ($data_lots as $key => $value) {
                $wprint->setLot($value)->setReffPicking($data_reff[$key])->setProduk($data_produk[$key])->setNote($data_notes[$key]);
            }
            
            $resp = $wprint->print();
            return $resp;
        
    }
    

}