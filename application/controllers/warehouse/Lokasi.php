<?php defined('BASEPATH') or exit('No Direct Script Acces Allowed');

/**
 * 
 */
class Lokasi extends MY_Controller
{
  public function __construct()
  {
    parent::__construct();
    $this->is_loggedin(); //cek apakah user sudah login
    $this->load->model("_module");
    $this->load->model("m_lokasi");
    $this->load->library('Pdf'); //load library pdf
  }


  public function index()
  {
    $data['id_dept']  = 'LOKASI';
    $data['list_dept'] = $this->_module->get_list_departement();
    $this->load->view('warehouse/v_lokasi', $data);
  }

  function get_data()
  {
    if (isset($_POST['start']) && isset($_POST['draw'])) {

      $list = $this->m_lokasi->get_datatables();
      $data = array();
      $no = $_POST['start'];
      foreach ($list as $field) {
        $kode_encrypt = encrypt_url($field->id);
        $no++;
        $row = array();
        $row[] = $no;
        $row[] = $field->departemen;
        $row[] = '<a href="' . base_url('warehouse/lokasi/edit/' . $kode_encrypt) . '">' . $field->kode_lokasi . '</a>';
        $row[] = $field->nama_lokasi;
        $row[] = $field->arah_panah;
        $row[] = $field->nama_status;
        $row[] = $field->id;
        $data[] = $row;
      }

      $output = array(
        "draw" => $_POST['draw'],
        "recordsTotal" => $this->m_lokasi->count_all(),
        "recordsFiltered" => $this->m_lokasi->count_filtered(),
        "data" => $data,
      );
      //output dalam format JSON
      echo json_encode($output);
    } else {
      die();
    }
  }

  public function add()
  {
    $data['id_dept']   = 'LOKASI';
    $data['warehouse'] = $this->_module->get_list_departement();
    return $this->load->view('warehouse/v_lokasi_add', $data);
  }


  public function edit($id = null)
  {
    if (!isset($id)) show_404();
    $kode_decrypt     = decrypt_url($id);
    $id_dept          = 'LOKASI';
    $data['id_dept']  = $id_dept;
    $data['mms']      = $this->_module->get_data_mms_for_log_history($id_dept); // get mms by dept untuk menu yg beda-beda
    $data['warehouse'] = $this->_module->get_list_departement();
    $data['lokasi']    = $this->m_lokasi->get_mst_lokasi_by_kode($kode_decrypt)->row();
    return $this->load->view('warehouse/v_lokasi_edit', $data);
  }


  public function simpan()
  {

    $sub_menu = $this->uri->segment(2);
    $username = $this->session->userdata('username');

    if (empty($this->session->userdata('status'))) { //cek apakah session masih ada
      // session habis
      $callback = array('message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis');
    } else {

      $kode_lokasi = addslashes($this->input->post('kode_lokasi'));
      $nama_lokasi = addslashes($this->input->post('kode_lokasi'));
      $dept_id  = addslashes($this->input->post('departemen'));
      $aisle    = addslashes($this->input->post('aisle'));
      $bay      = addslashes($this->input->post('bay'));
      $slot      = addslashes($this->input->post('slot'));
      $panah     = addslashes($this->input->post('panah'));
      $status   = addslashes($this->input->post('status'));
      $aksi     = addslashes($this->input->post('aksi'));
      $id       = addslashes($this->input->post('last_id'));

      if ($panah == ' ') {
        $callback = array('status' => 'failed', 'field' => 'arah_panah', 'message' => 'Arah Panah Harus Diisi !', 'icon' => 'fa fa-warning', 'type' => 'danger');
      } else if (empty($aisle)) {
        $callback = array('status' => 'failed', 'field' => 'aisle', 'message' => 'Aisle Harus Diisi !', 'icon' => 'fa fa-warning', 'type' => 'danger');
      } else if (empty($bay)) {
        $callback = array('status' => 'failed', 'field' => 'bay', 'message' => 'Bay Harus Diisi !', 'icon' => 'fa fa-warning', 'type' => 'danger');
      } else if (empty($slot)) {
        $callback = array('status' => 'failed', 'field' => 'slot', 'message' => 'Slot Harus Diisi !', 'icon' => 'fa fa-warning', 'type' => 'danger');
      } else if (empty($kode_lokasi) or empty($nama_lokasi)) {
        $callback = array('status' => 'failed', 'field' => 'kode_lokasi', 'message' => 'Kode Lokasi / Nama Lokasi Harus Diisi !', 'icon' => 'fa fa-warning', 'type' => 'danger');
      } else if (empty($dept_id)) {
        $callback = array('status' => 'failed', 'field' => 'departemen', 'message' => 'Departemen Harus Diisi !', 'icon' => 'fa fa-warning', 'type' => 'danger');
      } else {

        // lock tabel
        $this->_module->lock_tabel('mst_lokasi WRITE');

        // cek kode lokasi apa sudah ada ?
        $kode  = $this->m_lokasi->cek_kode_lokasi_by_kode($kode_lokasi, $dept_id)->row_array();

        if ($aksi == 'baru' and $kode['kode_lokasi'] == $kode_lokasi) {
          $callback = array('status' => 'failed', 'field' => 'aisle', 'message' => 'Kode Lokasi / Nama Lokasi Sudah Pernah diinput !', 'icon' => 'fa fa-warning', 'type' => 'danger');
        } else if ($aksi == 'baru') {
          // insert into mst_rak

          //get last id mst_rak
          $last_id = $this->m_lokasi->get_last_id_mst_lokasi();

          $this->m_lokasi->save_lokasi($last_id, $dept_id, $kode_lokasi, $nama_lokasi, $aisle, $bay, $slot, $panah, $status);
          $last_id_encr = encrypt_url($last_id);

          if ($panah == '1') {
            $arah_panah = 'Atas';
          } else {
            $arah_panah = 'Bawah';
          }
          if ($status = 't') {
            $status_aktif = 'Aktif';
          } else {
            $status_aktif = 'Tidak Aktif';
          }

          // unlock tabel
          $this->_module->unlock_tabel();


          $jenis_log   = "create";
          $note_log    = $dept_id . " | " . $kode_lokasi . " | " . $arah_panah . " | " . $status_aktif;
          $this->_module->gen_history($sub_menu, $last_id, $jenis_log, $note_log, $username);
          $callback = array('status' => 'success', 'message' => 'Data Berhasil Disimpan !', 'isi' => $last_id_encr, 'icon' => 'fa fa-check', 'type' => 'success');
        } else {
          // update mst_lokasi
          $this->m_lokasi->update_lokasi($id, $panah, $status);

          if ($panah == '1') {
            $arah_panah = 'Atas';
          } else {
            $arah_panah = 'Bawah';
          }
          if ($status == 't') {
            $status_aktif = 'Aktif';
          } else {
            $status_aktif = 'Tidak Aktif';
          }

          // unlock tabel
          $this->_module->unlock_tabel();

          $jenis_log   = "edit";
          $note_log    = $dept_id . " | " . $kode_lokasi . " | " . $arah_panah . " | " . $status_aktif;
          $this->_module->gen_history($sub_menu, $id, $jenis_log, $note_log, $username);
          $callback = array('status' => 'success', 'message' => 'Data Berhasil Disimpan !', 'icon' => 'fa fa-check', 'type' => 'success');
        }
      }
    }

    echo json_encode($callback);
  }



  public function print_lokasi()
  {
    // $pdf=new PDF_Code128('l','mm',array(59.944,89.916));

    $pdf = new PDF_Code128('L', 'mm', array(90, 60));

    $pdf->SetMargins(0, 0, 0, 0);
    $pdf->SetAutoPageBreak(FALSE);

    $data_arr  = json_decode($this->input->get('lokasi'), true);

    if (empty($data_arr)) {
      $lokasi_id   =  array(); // id lokasi 
    } else {
      $lokasi_id  = $data_arr;
    }

    $data_print = [];
    foreach ($lokasi_id as $i) {
      $gt = $this->m_lokasi->get_mst_lokasi_by_kode($i)->result();
      foreach ($gt as $gts) {
        $data_print[] = array(
          'kode_lokasi' => $gts->kode_lokasi,
          'aisle' => $gts->aisle,
          'bay' => $gts->bay,
          'slot' => $gts->slot,
          'panah' => $gts->panah
        );
      }
    }

    foreach ($data_print as $dp) {

      $pdf->AddPage();
      // $row = explode('^',$check1[$i]);

      $kode_lokasi  = $dp['kode_lokasi'];
      $aisle = $dp['aisle'];
      $bay   = $dp['bay'];
      $slot  = $dp['slot'];
      $panah = $dp['panah'];

      $pdf->SetFont('Arial', 'B', 15, 'C');
      $pdf->SetXY(1, 2);


      $pdf->Cell(5, 3, '', 0, 1, '');

      $pdf->Cell(5, 10, '', 0, 0, '');
      $pdf->SetTextColor(255, 255, 255);
      $pdf->Cell(63, 10, 'KODE RACK : ' . $kode_lokasi, 0, 1, 'C', TRUE);
      $pdf->Code128(7, 16, $kode_lokasi, 60, 23, 'C', 0, 1); //barcode
      $pdf->SetTextColor(0, 0, 0);

      $pdf->Cell(10, 9, '', 0, 0, '');
      $pdf->Cell(5, 9, '', 0, 1);
      $pdf->Cell(5, 8, '', 0, 1);
      $pdf->Cell(5, 8, '', 0, 1);

      $pdf->SetFont('Arial', 'B', 25, 'C');
      $pdf->SetTextColor(255, 255, 255);
      $pdf->Cell(11, 5, '', 0, 0, '');
      $pdf->Cell(14, 10, $aisle, 0, 0, 'C', TRUE);
      $pdf->SetTextColor(0, 0, 0);

      $pdf->Cell(25, 10, $bay, 0, 0, 'C');
      $pdf->Cell(25, 10, $slot, 0, 0, 'L');
      $pdf->Cell(25, 5, '', 0, 1, 'L');
      if ($panah == '1') {
        $pdf->Image(base_url('dist/img/panah_atas.png'), 60, 5, -600);
      } elseif ($panah == '0') {
        $pdf->Image(base_url('dist/img/panah_bawah.png'), 60, 5, -600);
      }
      $pdf->SetFont('Arial', '', 10, 'C');
      $pdf->Cell(25, 15, 'X/Aisle', 0, 0, 'R');
      $pdf->Cell(25, 15, 'Y/Bay', 0, 0, 'C');
      $pdf->Cell(25, 15, 'Z/Slot', 0, 0, 'L');
      $pdf->Cell(25, 5, '', 0, 1, 'L');
    }

    $pdf->Output();
  }
}
