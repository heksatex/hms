<?php defined('BASEPATH') or exit ('No Direct Script Acces Allowed');

/**
 * 
 */
class Produkparent extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->is_loggedin();//cek apakah user sudah login
        $this->load->model("m_produkParent");
        $this->load->model("_module");
    }


    public function index()
    {
        $data['id_dept']  ='MPRODPR';
        $this->load->view('warehouse/v_produkparent', $data);
    }

    function get_data()
    {
        $nama_parent = '';
        $status_parent = '';
        if($this->input->post('nama_parent')){
            $nama_parent = $this->input->post('nama_parent');
        }
		if($this->input->post('status')){
            $status_parent = $this->input->post('status');
        }

        $list = $this->m_produkParent->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $field) {
            $kode_encrypt = encrypt_url($field->id);
            $click  = "view_parent('.$kode_encrypt.')";
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = '<a href="javascript:void(0);" onclick="'.$click.'">'.$field->nama.'</a>';
            $row[] = $field->tanggal;
            $row[] = $field->child;
            $row[] = $field->nama_status;
            $data[]= $row;
        }
        
        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_produkParent->count_all(),
            "recordsFiltered" => $this->m_produkParent->count_filtered(),
            "data" => $data,
            "filter" => $_POST['search']['value'],
            'nama_parent' => $nama_parent
        );
        //output dalam format JSON
        echo json_encode($output);
    }

    public function add_parent_produk()
    {
        return $this->load->view('modal/v_produk_parent_add_modal');
    }

    public function view_parent_produk()
    {
        $id_parent    = $this->input->post("id");
        $kode_decrypt = decrypt_url($id_parent);
        $get          = $this->m_produkParent->get_data_parent_by_id($kode_decrypt)->row_array();
        $data['mms']    = $this->_module->get_data_mms_for_log_history('MPRODPR');// get mms by dept untuk log history
        $data['produk'] = $this->m_produkParent->get_list_child_by_parent($kode_decrypt)->result();
        $data['data']   = $get;
        return $this->load->view('modal/v_produk_parent_view_modal',$data);
    }

    public function simpan()
    {
        
        if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
            // session habis
            $callback = array('message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
        }else{
            $sub_menu = $this->uri->segment(2);
            $username = $this->session->userdata('username');

            $id_parent   = $this->input->post("id");
            $nama_parent = addslashes($this->input->post('nama'));
            $status      = addslashes($this->input->post('status'));

            if(empty($id_parent)){ // if insert parent new
                $cek_nama = $this->m_produkParent->cek_nama_parent_by_nama($nama_parent)->num_rows();
            }else{
                $cek_nama = $this->m_produkParent->cek_nama_parent_by_nama_id($nama_parent,$id_parent)->num_rows();
            }

            if(empty($nama_parent)){
                $callback = array('status' => 'failed', 'field' => 'nama', 'message' => 'Nama Harus Diisi !', 'icon' =>'fa fa-warning', 'type' => 'danger'  );   
            }else if(!empty($cek_nama)){
                $callback = array('status' => 'failed', 'field' => 'nama', 'message' => 'Nama Parent sudah pernah diinput !', 'icon' =>'fa fa-warning', 'type' => 'danger'  );   
            }else{
                 // lock tabel
                 $this->_module->lock_tabel('mst_produk_parent WRITE,user WRITE, main_menu_sub WRITE, log_history WRITE, mst_produk_parent as mpp WRITE, mst_status as ms WRITE, mst_status WRITE, mst_produk WRITE');

                if(!empty($id_parent)){ //update
                    //
                    $child = false;
                    if($status == 'f'){
                        // cek jml child byid _parent
                        $jml_child = $this->m_produkParent->get_jml_child_by_id_parent($id_parent);
                        if($jml_child > 0){
                            $child = true;
                        }
                    }

                    if($child){
                        $callback = array('status' => 'failed', 'field' => 'nama','message' => 'Status Parent tidak bisa dirubah ke Status Tidak Aktif, karena masih terdapat Childs !','icon' =>'fa fa-warning', 'type' => 'warning');
                    }else{
                        // get nama parent sebelum edit 
                        $before       = $this->m_produkParent->get_data_parent_by_id($id_parent)->row_array();
                        $note_before  = addslashes($before['nama']).' | '.$before['nama_status'];

                        $this->m_produkParent->update_product_parent_by_id($id_parent,$nama_parent,$status);

                        //get status aktif by kode f/t
                        $status_aktif = $this->_module->get_mst_status_by_kode($status);

                        $jenis_log   = "edit";
                        $note_log    = $note_before.' -> '.$nama_parent.' | '.$status_aktif;
                        $this->_module->gen_history($sub_menu, $id_parent, $jenis_log, $note_log, $username);
                        $callback = array('status' => 'success', 'message' => 'Data Berhasil Disimpan !','icon' =>'fa fa-check', 'type' => 'success');
                      
                    }

                }else{ // insert baru
                    
                    // get last id parent
                    $last_id = $this->m_produkParent->get_last_id_parent();
                    $this->m_produkParent->save_product_parent($nama_parent);

                    $jenis_log   = "create";
                    $note_log    = $nama_parent;
                    $this->_module->gen_history($sub_menu, $last_id, $jenis_log, $note_log, $username);
                    $callback = array('status' => 'success', 'message' => 'Data Berhasil Disimpan !','icon' =>'fa fa-check', 'type' => 'success');
                }

                // unlock warna
                $this->_module->unlock_tabel();


            }


        }

        echo json_encode($callback);
    }


    public function export_excel_parent()
    {

        $this->load->library('excel');
        ob_start();
        
        $object = new PHPExcel();
    	$object->setActiveSheetIndex(0);

        
        $nama_parent = $this->input->post("nama");
        $status      = $this->input->post("status");
        $filter      = $this->input->post("filter");
        
        // SET JUDUL
        $object->getActiveSheet()->SetCellValue('A1', 'Product Parent');
        $object->getActiveSheet()->getStyle('A1')->getAlignment()->setIndent(1);
		$object->getActiveSheet()->mergeCells('A1:L1');
        
        //bold huruf
        $object->getActiveSheet()->getStyle("A1:W4")->getFont()->setBold(true);

        // Border 
		$styleArray = array(
            'borders' => array(
              'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN
              )
            )
        );

        // header table
        $table_head_columns  = array('No', 'Nama Parent','Tanggal dibuat', 'Jumlah Child', 'Status');

        $column = 0;
        foreach ($table_head_columns as $judul) {
            # code...
            $object->getActiveSheet()->setCellValueByColumnAndRow($column, 4, $judul);  
            $column++;
        }
  
        // set with and border
        $index_header = array('A','B','C','D','E');
        $loop = 0;
        foreach ($index_header as $val) {
            $object->getActiveSheet()->getStyle($val.'4')->applyFromArray($styleArray);
        }
        

        //body
        $num      = 1;
        $rowCount = 5;
        $list     = $this->m_produkParent->get_list_product_parent_by_kode($nama_parent,$status, $filter);
        foreach ($list as $val) {
            # code...
            $object->getActiveSheet()->SetCellValue('A'.$rowCount, ($num++));
            $object->getActiveSheet()->SetCellValue('B'.$rowCount, $val->nama);
            $object->getActiveSheet()->SetCellValue('C'.$rowCount, $val->tanggal);
            $object->getActiveSheet()->SetCellValue('D'.$rowCount, $val->child);
            $object->getActiveSheet()->SetCellValue('E'.$rowCount, $val->nama_status);

            //set border true
			$object->getActiveSheet()->getStyle('A'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('B'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('C'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('D'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('E'.$rowCount)->applyFromArray($styleArray);

            $rowCount++;
        }


        
        $object = PHPExcel_IOFactory::createWriter($object, 'Excel2007');  
        // header('Content-Type: application/vnd.ms-excel'); //mime type
        // header('Content-Disposition: attachment;filename="Product Parent.xls"'); //tell browser what's the file name
        // header('Cache-Control: max-age=0'); //no cache
        $object->save('php://output');

        $xlsData = ob_get_contents();
        ob_end_clean();

        $response =  array(
            'op'        => 'ok',
            'file'      => "data:application/vnd.ms-excel;base64,".base64_encode($xlsData),
            'filename'  => "Product Parent.xlsx"
        );
    
        die(json_encode($response));
    }
}