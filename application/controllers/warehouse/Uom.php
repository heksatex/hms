<?php defined('BASEPATH') or exit('No Direct Script Acces Allowed');

/**
 * 
 */
class Uom extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->is_loggedin(); //cek apakah user sudah login
        $this->load->model("_module");
        $this->load->model("m_uom");
        // $this->load->model("m_lokasi");
    }

    public function index()
    {
        $data['id_dept']    = 'UOM';
        $this->load->view('warehouse/v_uom', $data);
    }

    function get_data()
    {
        if (isset($_POST['start']) && isset($_POST['draw'])) {

            $list = $this->m_uom->get_datatables();
            $data = array();
            $no   = $_POST['start'];
            foreach ($list as $field) {
                $kode_encrypt = encrypt_url($field->id);
                $click = "view_uom('.$kode_encrypt.')";
                $no++;
                $row = array();
                $row[] = $no;
                $row[] = '<a href="javascript:void(0);" onclick="' . $click . '">' . $field->nama . '</a>';
                $row[] = $field->short;
                $row[] = $field->jenis;
                $row[] = $field->jual;
                $row[] = $field->beli;
                $data[] = $row;
            }

            $output = array(
                "draw"  => $_POST['draw'],
                "recordsTotal"  => $this->m_uom->count_all(),
                "recordsFiltered" => $this->m_uom->count_filtered(),
                "data"  => $data,
            );
            echo json_encode($output);
        } else {
            die();
        }
    }

    public function view_uom()
    {
        $id     = $this->input->post('id');
        $kode_decrypt   = decrypt_url($id);
        $get            = $this->m_uom->get_uom_by_id($kode_decrypt)->row_array();
        $data['mms']    = $this->_module->get_data_mms_for_log_history('UOM'); // get mms by dept untuk log history
        $data['data']   = $get;
        return $this->load->view('modal/v_uom_view_modal', $data);
    }

    function add_uom()
    {
        return $this->load->view('modal/v_uom_add_modal');
    }


    public function simpan()
    {
        try {
            //code...
            if (empty($this->session->userdata('status'))) { //cek apakah session masih ada
                // session habis
                throw new \Exception('Waktu Anda Telah Habis', 401);
            } else {

                $sub_menu = $this->uri->segment(2);
                $username = $this->session->userdata('username');

                $id   = $this->input->post("id");
                $nama = $this->input->post("nama");
                $short = $this->input->post("short");
                $jenis = $this->input->post("jenis");
                $jual = $this->input->post("jual");
                $beli = $this->input->post("beli");


                if (empty($nama)) {
                    $callback = array('status' => 'failed', 'field' => 'nama', 'message' => 'Nama Harus Diisi !', 'icon' => 'fa fa-warning', 'type' => 'danger');
                } else if (empty($short)) {
                    $callback = array('status' => 'failed', 'field' => 'short', 'message' => 'Short Harus Diisi !', 'icon' => 'fa fa-warning', 'type' => 'danger');
                } else if (empty($jenis)) {
                    $callback = array('status' => 'failed', 'field' => 'jenis', 'message' => 'Jenis Harus Diisi !', 'icon' => 'fa fa-warning', 'type' => 'danger');
                } else if (empty($jual)) {
                    $callback = array('status' => 'failed', 'field' => 'jual', 'message' => 'Jual Harus Diisi !', 'icon' => 'fa fa-warning', 'type' => 'danger');
                } else if (empty($beli)) {
                    $callback = array('status' => 'failed', 'field' => 'beli', 'message' => 'Beli Harus Diisi !', 'icon' => 'fa fa-warning', 'type' => 'danger');
                } else {

                    if (!empty($id)) { //update
                        $callback = array('status' => 'success', 'message' => 'Data Berhasil Disimpan !', 'icon' => 'fa fa-check', 'type' => 'success');
                    } else { //insert

                        // cek nama / short uom
                        $cek = $this->m_uom->cek_uom_double($nama, $short, '');
                        if (!empty($cek)) {
                            throw new \Exception('Nama atau Short sudah pernah diinput', 200);
                        }

                        $id_new = $this->m_uom->get_last_id_uom();
                        $data_uom = array(
                            'id'  => $id_new,
                            "short" => $short,
                            "nama"  => $nama,
                            "jenis" => $jenis,
                            "jual"  => $jual,
                            "beli"  => $beli
                        );
                        $this->m_uom->save_uom($data_uom);

                        $jenis_log   = "create";
                        $note_log    = $nama . " | " . $short . " | " . $jenis . " | " . $jual . " | " . $beli;
                        $this->_module->gen_history($sub_menu, $id_new, $jenis_log, $note_log, $username);
                        $callback = array('status' => 'success', 'message' => 'Data Berhasil Disimpan !', 'icon' => 'fa fa-check', 'type' => 'success');
                    }
                }

                $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode($callback));
            }
        } catch (Exception $ex) {
            $this->output->set_status_header($ex->getCode() ?? 500)
                ->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode(array('status' => 'failed', 'message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }
}
