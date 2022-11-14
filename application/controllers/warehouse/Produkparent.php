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
            $data[]= $row;
        }
        
        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_produkParent->count_all(),
            "recordsFiltered" => $this->m_produkParent->count_filtered(),
            "data" => $data,
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
        $data['data'] = $get;
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

            $cek_nama = $this->m_produkParent->cek_nama_parent_by_nama($nama_parent)->num_rows();

            if(empty($nama_parent)){
                $callback = array('status' => 'failed', 'field' => 'nama', 'message' => 'Nama Harus Diisi !', 'icon' =>'fa fa-warning', 'type' => 'danger'  );   
            }else if(!empty($cek_nama)){
                $callback = array('status' => 'failed', 'field' => 'nama', 'message' => 'Nama Parent sudah pernah diinput !', 'icon' =>'fa fa-warning', 'type' => 'danger'  );   
            }else{
                 // lock tabel
                 $this->_module->lock_tabel('mst_produk_parent WRITE,user WRITE, main_menu_sub WRITE, log_history WRITE');

                if(!empty($id_parent)){ //update
                    // get nama parent sebelum edit 
                    $before       = $this->m_produkParent->get_data_parent_by_id($id_parent)->row_array();
                    $note_before  = addslashes($before['nama']);

                    $this->m_produkParent->update_product_parent_by_id($id_parent,$nama_parent);

                    $jenis_log   = "edit";
                    $note_log    = $note_before.' -> '.$nama_parent;
                    $this->_module->gen_history($sub_menu, $id_parent, $jenis_log, $note_log, $username);
                    $callback = array('status' => 'success', 'message' => 'Data Berhasil Disimpan !','icon' =>'fa fa-check', 'type' => 'success');

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
}