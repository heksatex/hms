<?php

defined('BASEPATH') or exit('No Direct Script Acces Allowed');

/**
 * 
 */
class Produk extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->is_loggedin(); //cek apakah user sudah login
        $this->load->model("m_produk"); //load model m_lab
        $this->load->model("_module");
        $this->load->model("m_user");
        $this->load->model("m_konversiuom");
        $this->load->model("m_coa");
        $this->load->model("m_global");
        $this->load->library("upload");
        $this->load->library("token");
        $this->load->helper('file');
    }

    public function index() {
        $data['id_dept'] = 'MPROD';
        $data['category'] = $this->m_produk->get_list_category();
        $data['route'] = $this->m_produk->get_list_route();
        $this->load->view('warehouse/v_produk', $data);
    }

    function get_data() {
        $username = $this->session->userdata('username');
        $sub_menu = $this->uri->segment(2);
        $kode = $this->_module->get_kode_sub_menu($sub_menu)->row_array();
        $level = $this->session->userdata('nama')['level'] ?? "";
        if ($level === "Entry Data") {
            $masking = $this->m_coa->setTables("user_masking")->setSelects(["GROUP_CONCAT(mst_category_id) as category"])->setOrder(["mst_category_id"])->setWheres(["username" => $username])->getDetail();
            if (!empty($masking->category)) {
                $list = $this->m_produk->setWhereRaw("id_category in ({$masking->category})")->get_datatables();
            } else {
                $list = $this->m_produk->get_datatables();
            }
        } else {
            $list = $this->m_produk->get_datatables();
        }
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $field) {
            $kode_encrypt = encrypt_url($field->id);
            if ($field->id_parent == 0) {
                $parent = 'Tidak Ada';
            } else {
                $parent = 'Ada';
            }
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = '<a href="' . base_url('warehouse/produk/edit/' . $kode_encrypt) . '">' . $field->kode_produk . '</a>';
            $row[] = $field->nama_produk;
            $row[] = $field->create_date;
            $row[] = $field->uom;
            $row[] = $field->uom_2;
            $row[] = $field->nama_category;
            $row[] = $field->route_produksi;
            $row[] = $field->type;
            $row[] = $field->nama_status;
            $row[] = $parent;
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_produk->count_all(),
            "recordsFiltered" => $this->m_produk->count_filtered(),
            "data" => $data,
        );
        //output dalam format JSON
        echo json_encode($output);
    }

    // public function add()
    // { 
    //   $data['id_dept']  ='MPROD';
    //   $data['uom']      = $this->_module->get_list_uom();
    //   $data['category'] = $this->m_produk->get_list_category();
    //   $data['route']    = $this->m_produk->get_list_route();        
    //   $data['jenis_kain'] = $this->m_produk->get_list_jenis_kain();        
    //   return $this->load->view('warehouse/v_produk_add', $data);
    // }

    public function get_uom_beli() {
        try {
            $ke = $this->input->get("ke");
            if ($ke === "" || is_null($ke))
                throw new \Exception('Satuan Beli Belum ditentukan', 500);
            $data = new $this->m_konversiuom;
            if ($ke === "0") {
                $data = $data->selects("id,dari as text, catatan")->getData();
            } else {
                $data = $data->selects("id,dari as text, catatan")->wheres(["ke" => $ke])->getData();
            }
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array("data" => $data)));
        } catch (Exception $ex) {
            $this->output->set_status_header(500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array("message" => $ex->getMessage())));
        }
    }

    public function add() {
        $username = $this->session->userdata('username');
        $data['id_dept'] = 'MPROD';
        $id = $this->input->get('id');
        $kode_produk = $this->input->get('kode_produk');
        $duplicate = $this->input->get('duplicate');
        $data['uom'] = $this->_module->get_list_uom();
//        $data["uom_beli"] = $this->m_produk->get_list_uom(['beli' => 'yes']);
        $data['category'] = $this->m_produk->get_list_category();
        $data['route'] = $this->m_produk->get_list_route();
        $data['jenis_kain'] = $this->m_produk->get_list_jenis_kain();
        $masking = [];
        foreach ($this->m_user->getMasking($username) as $value) {
            $masking[] = $value->mst_category_id;
        }
        $data["masking"] = $masking;
        if ($duplicate == 'true') {
            $produk = $this->m_produk->get_produk_by_kode($id); //id auto increment
            $data['produk'] = $produk;
            if (empty($produk)) {
                show_404();
            } else {
                $data["uom_beli"] = $this->m_konversiuom->wheres(["id" => $produk->uom_beli])->getDetail();
                return $this->load->view('warehouse/v_produk_duplicate', $data);
            }
        } else {
            return $this->load->view('warehouse/v_produk_add', $data);
        }
    }

    function get_product_parent_select2() {
        $nama = addslashes($this->input->post('nama'));
        $callback = $this->m_produk->get_list_product_parent($nama);
        echo json_encode($callback);
    }

    function get_product_sub_parent_select2() {
        $nama = addslashes($this->input->post('nama'));
        $callback = $this->m_produk->get_list_product_sub_parent($nama);
        echo json_encode($callback);
    }

    public function simpan() {
        $callback = [];
        try {
            $sub_menu = $this->uri->segment(2);
            $username = $this->session->userdata('username');

            if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
                // session habis
                $callback = array('message' => 'Waktu Anda Telah Habis', 'sesi' => 'habis');
                throw new \Exception('', 500);
            } else {
                $ids = 0;
                //lock table
                $this->_module->lock_tabel('token_increment WRITE, mst_produk WRITE, mst_category WRITE, user WRITE, main_menu_sub WRITE,'
                        . ' log_history WRITE, mst_status WRITE, mst_produk_parent WRITE,'
                        . ' mst_jenis_kain WRITE, mst_produk_sub_parent WRITE,departemen WRITE');

                //cek auto generate kode produk atau input sendiri
                $autogenerate = $this->input->post('autogenerate');
                $kategoribarang = $this->input->post('kategoribarang');
                $autogenerate_gudang = $this->input->post('autogenerate_gudang');
                //get id kategori barang
                $nmKategori = $this->m_produk->get_nama_category_by_id($kategoribarang)->row_array();

                $this->_module->startTransaction();
                if ($autogenerate === "1") {
                    $kodeproduk = $this->_module->get_kode_product();
                    $kodeproduk = 'MF' . $kodeproduk;
                } else if ($autogenerate_gudang === "1") {
                    $model = new $this->m_global;
                    $check = $model->setTables("mst_category")->setJoins("departemen", "departemen.kode = dept_id")
                                    ->setWheres(["mst_category.id" => $kategoribarang, "type_dept" => "gudang"])
                                    ->setSelects(["mst_category.*"])->getDetail();
                    if ($check === null) {
                        $callback = array('status' => 'failed', 'field' => 'namaproduk', 'message' => 'Kode Generate Tidak tersedia', 'icon' => 'fa fa-warning',
                            'type' => 'danger');
                        throw new \Exception('', 500);
                    }
                    $kodeproduk = $this->token->noUrut($check->dept_id, "", true)->generate($check->dept_id, "%d")->get();
                } else {
                    $kodeproduk = addslashes($this->input->post('kodeproduk'));
                }
//                if ($autogenerate == 0) {
//                    $kodeproduk = addslashes($this->input->post('kodeproduk'));
//                } else {
//                    $kategoriPrefix = str_replace(" ", "_", $nmKategori["nama_category"]);
//                    $check = $this->token->exists(["modul" => strtolower($kategoriPrefix), "periode" => "-"]);
//                    if (!$check) {
//                        $kodeproduk = $this->_module->get_kode_product();
//                        $kodeproduk = 'MF' . $kodeproduk;
//                    } else {
//                        $kodeproduk = $this->token->noUrut(strtolower($kategoriPrefix), "-", true)->prefixAdd("")->generate($check->prefix, $check->format)->get();
//                    }
//                }

                $id = $this->input->post('id'); //id produk auto increment
                $nama_produk = ($this->input->post('namaproduk'));
                $namaproduk = addslashes($this->input->post('namaproduk'));
                $uomproduk = addslashes($this->input->post('uomproduk'));
                $uomproduk2 = addslashes($this->input->post('uomproduk2'));
                $routeproduksi = addslashes($this->input->post('routeproduksi'));
                $typeproduk = strtolower(addslashes($this->input->post('typeproduk')));
                $bom = $this->input->post('bom');
                $dapatdijual = $this->input->post('dapatdijual');
                $dapatdibeli = $this->input->post('dapatdibeli');
                $note = addslashes($this->input->post('note'));
                $lebargreige = addslashes($this->input->post('lebargreige'));
                $uom_lebargreige = addslashes($this->input->post('uom_lebargreige'));
                $lebarjadi = addslashes($this->input->post('lebarjadi'));
                $uom_lebarjadi = addslashes($this->input->post('uom_lebarjadi'));
                $product_parent = addslashes($this->input->post('product_parent'));
                $sub_parent = addslashes($this->input->post('sub_parent'));
                $jenis_kain = addslashes($this->input->post('jenis_kain'));
                $statusproduk = addslashes($this->input->post('statusproduk'));
                $duplicate = addslashes($this->input->post('duplicate'));
                $uom_beli = addslashes($this->input->post('uom_beli'));

                $sql_insert_mst_sub_parent = '';
                $nama_sub_parent = 0;

                if ($uom_beli === null || $uom_beli === "") {
                    $datakonversi = ["ke" => $uomproduk, "dari" => $uomproduk, "nilai" => 1];
                    $getDataKonv = $this->m_konversiuom->wheres($datakonversi)->getDetail();
                    if (!$getDataKonv) {
                        $crtDataKonv = $this->m_konversiuom->save(array_merge($datakonversi, ["catatan" => "1:1"]));
                        if ($crtDataKonv === "") {
                            $getDataKonv = $this->m_konversiuom->wheres($datakonversi)->getDetail();
                            $uom_beli = $getDataKonv->id;
                        } else {
                            $uom_beli = null;
                        }
                    } else {
                        $uom_beli = $getDataKonv->id;
                    }
                }

                $tanggaldibuat = $this->input->post('tanggaldibuat');
                $status = addslashes($this->input->post('status'));

                if (empty($namaproduk)) {
                    $callback = array('status' => 'failed', 'field' => 'namaproduk', 'message' => 'Nama Produk Harus Diisi !', 'icon' => 'fa fa-warning',
                        'type' => 'danger');
                    throw new \Exception('', 500);
                    //}else if($kodeproduk == ''){
                } else if (empty($kodeproduk)) {
                    $callback = array('status' => 'failed', 'field' => 'kodeproduk', 'message' => 'Kode Produk Harus Diisi !', 'icon' => 'fa fa-warning',
                        'type' => 'danger');
                    throw new \Exception('', 500);
                } else if (empty($uomproduk)) {
                    $callback = array('status' => 'failed', 'field' => 'uomproduk', 'message' => 'UOM/Satuan Harus Diisi !', 'icon' => 'fa fa-warning',
                        'type' => 'danger');
                    throw new \Exception('', 500);
                } else if (empty($kategoribarang)) {
                    $callback = array('status' => 'failed', 'field' => 'kategoribarang', 'message' => 'Kategori Barang Harus Diisi !', 'icon' => 'fa fa-warning',
                        'type' => 'danger');
                    throw new \Exception('', 500);
                } else if (!empty($lebargreige) AND empty($uom_lebargreige)) {
                    $callback = array('status' => 'failed', 'field' => 'uom_lebargreige', 'message' => 'Uom Lebar Greige Harus Diisi !', 'icon' => 'fa fa-warning',
                        'type' => 'danger');
                    throw new \Exception('', 500);
                } else if (!empty($lebarjadi) AND empty($uom_lebarjadi)) {
                    $callback = array('status' => 'failed', 'field' => 'uom_lebarjadi', 'message' => 'Uom Lebar Jadi Harus Diisi !', 'icon' => 'fa fa-warning',
                        'type' => 'danger');
                    throw new \Exception('', 500);
                } else if (empty($product_parent) && $nmKategori["has_parent"] === "1") {
                        $callback = array('status' => 'failed', 'field' => 'product_parent', 'message' => 'Product Parent Harus Diisi !', 'icon' => 'fa fa-warning',
                            'type' => 'danger');
                        throw new \Exception('', 500);
                } else if (empty($jenis_kain) AND (strpos($nmKategori['nama_category'], 'Kain') !== FALSE)) {
                    $callback = array('status' => 'failed', 'field' => 'jenis_kain', 'message' => 'Jenis Kain Harus disini jika Kategori Barangnya Kain !', 'icon' => 'fa fa-warning',
                        'type' => 'danger');
                    throw new \Exception('', 500);
                    // }else if(empty($jenis_kain) AND (strpos($nmKategori['nama_category'], 'Dyeing') !== FALSE or strpos($nmKategori['nama_category'], 'Setting') !== FALSE OR strpos($nmKategori['nama_category'], 'Padding') !== FALSE OR strpos($nmKategori['nama_category'], 'Brushing') !== FALSE OR strpos($nmKategori['nama_category'], 'Finishing') !== FALSE  OR strpos($nmKategori['nama_category'], 'Finbrushing') !== FALSE) ){
                    // $callback = array('status' => 'failed', 'field' => 'jenis_kain', 'message' => 'Jenis Kain Harus disini jika Kategori Barangnya Kain !', 'icon' =>'fa fa-warning', 
                    // 'type' => 'danger'  );
                } else {
                    //cek kode produk apa sudah ada apa belum
                    $cek = $this->m_produk->cek_produk_by_kode($kodeproduk)->row_array();
                    // cek nama sudah ada atau belum ?
                    $cek2 = $this->m_produk->cek_produk_by_nama($kodeproduk, $namaproduk)->row_array();

                    // cek apa nama_produk tidak ada 
                    if (empty($cek2['nama_produk'])) {
                        $nama_double = FALSE;
                    } else {
                        $nama_double = TRUE;
                    }

                    if ($bom == 1) {
                        $log_bom = 'True';
                    } else {
                        $log_bom = 'False';
                    }

                    // cek mst parent by id
                    $parent = $this->m_produk->get_mst_parent_produk_by_id($product_parent)->row_array();

                    // cek mst jenis kain
                    $jk = $this->m_produk->get_mst_jenis_kain_by_id($jenis_kain)->row_array();

                    // cek mst sub parent by id
                    $sb = $this->m_produk->get_mst_sub_parent_produk_by_id($sub_parent)->row_array();

                    //get status aktif by kode f/t
                    $status_aktif = $this->_module->get_mst_status_by_kode($statusproduk);

                    // get last id mst_sub_parent
                    $id_sub_parent_new = $this->m_produk->get_last_id_mst_sub_parent();

                    if (!empty($jenis_kain) AND (strpos($nmKategori['nama_category'], 'Kain') !== FALSE)) {

                        if (empty($sub_parent) or $sub_parent == "0") {
                            $nama_sub_parent = explode('"', $nama_produk);
                            $nama_sub_parent_ex = trim($nama_sub_parent[0]) . '"';
                            // cek ke mst sub parent 
                            $cek_sp = $this->m_produk->cek_sub_parent_by_nama(addslashes($nama_sub_parent_ex))->row_array();
                            if (empty($cek_sp['id'])) {

                                // create sub_parent
                                $id_sub_parent = $id_sub_parent_new;  // sudah + 1
                                $tgl = date('Y-m-d H:i:s');
                                // insert into mst sub parent
                                $sql_insert_mst_sub_parent = "('" . $id_sub_parent_new . "','" . $tgl . "','" . addslashes($nama_sub_parent_ex) . "') ";

                                $nama_sub_parent = $nama_sub_parent_ex;

                                $sub_parent = $id_sub_parent;
                            } else {
                                $sub_parent = $cek_sp['id'];
                                $nama_sub_parent = $cek_sp['nama_sub_parent'];
                            }
                        } else {
                            $sub_parent = $sb['id'];
                            $nama_sub_parent = $sb['nama_sub_parent'];
                        }
                    }

                    if (!empty($cek['kode_produk']) AND $status == 'tambah') {
                        $callback = array('status' => 'failed', 'field' => 'kodeproduk', 'message' => 'Kode Produk ini Sudah Pernah Diinput !', 'icon' => 'fa fa-warning', 'type' => 'danger');
                        throw new \Exception('', 500);
                    } elseif ($nama_double == TRUE) {
                        $callback = array('status' => 'failed', 'field' => 'namaproduk', 'message' => 'Nama Produk ini Sudah Pernah Diinput !', 'icon' => 'fa fa-warning', 'type' => 'danger');
                        throw new \Exception('', 500);
                    } else if (!empty($cek['kode_produk']) AND $status == 'edit') {

                        //update/edit produk
                        $this->m_produk->update_produk($id, $namaproduk, $uomproduk, $uomproduk2, $routeproduksi, $typeproduk, $dapatdibeli,
                                $dapatdijual, $kategoribarang, $note, $bom, $lebargreige, $uom_lebargreige, $lebarjadi, $uom_lebarjadi,
                                $statusproduk, $product_parent, $sub_parent, $jenis_kain, $uom_beli);
                        $ids = $id;
                        if (!empty($sql_insert_mst_sub_parent)) {
                            $sql_insert_mst_sub_parent = rtrim($sql_insert_mst_sub_parent, ', ');
                            $this->m_produk->simpan_mst_sub_parent_batch($sql_insert_mst_sub_parent);
                        }

                        $jenis_log = "edit";
                        $note_log = $kodeproduk . " | " . $namaproduk . " | " . $uomproduk . " | " . $uomproduk2 . " | " . $lebargreige . " " . $uom_lebargreige . " | " . $lebarjadi . " " . $uom_lebarjadi . " | " . $routeproduksi . " | " . $typeproduk . " | " . $dapatdibeli . " | " . $dapatdijual . " | " . $nmKategori['nama_category'] . " | " . $log_bom . " | " . ($parent['nama'] ?? "") . " | " . $nama_sub_parent . " | " . $jk['nama_jenis_kain'] . " | " . $status_aktif;
                        $this->_module->gen_history($sub_menu, $kodeproduk, $jenis_log, ($note_log), $username);
                        $callback = array('status' => 'success', 'message' => 'Data Berhasil Disimpan !', 'icon' => 'fa fa-check', 'type' => 'success', 'id' => $sub_parent, 'nama' => $nama_sub_parent);
                    } else {
                        //insert/add produk
                        $id_new = $this->m_produk->get_last_id_mst_produk();
                        $ids = $id_new;
                        $this->m_produk->save_produk($kodeproduk, $namaproduk, $uomproduk, $uomproduk2, $tanggaldibuat, $routeproduksi,
                                $typeproduk, $dapatdibeli, $dapatdijual, $kategoribarang, $note, $bom, $lebargreige, $uom_lebargreige, $lebarjadi,
                                $uom_lebarjadi, $statusproduk, $product_parent, $sub_parent, $jenis_kain, $uom_beli);
                        $kodeproduk_encr = encrypt_url($id_new);

                        if (!empty($sql_insert_mst_sub_parent)) {
                            $sql_insert_mst_sub_parent = rtrim($sql_insert_mst_sub_parent, ', ');
                            $this->m_produk->simpan_mst_sub_parent_batch($sql_insert_mst_sub_parent);
                        }

                        if ($duplicate == true) {
                            $kode_produk_before = addslashes($this->input->post('kode_produk_before'));
                            $nama_produk_before = addslashes($this->input->post('nama_produk_before'));

                            $note_logs = "Duplicate dari Produk " . addslashes($kode_produk_before) . " " . addslashes($nama_produk_before) . "<br>" . $kodeproduk . " | " . $namaproduk . " | " . $uomproduk . " | " . $uomproduk2 . " | " . $lebargreige . " " . $uom_lebargreige . " | " . $lebarjadi . " " . $uom_lebarjadi . " | " . $routeproduksi . " | " . $typeproduk . " | " . $dapatdibeli . " | " . $dapatdijual . " | " . $nmKategori['nama_category'] . " | " . $log_bom . " | " . ($parent['nama'] ?? "") . " | " . $nama_sub_parent . " | " . $jk['nama_jenis_kain'] . " | " . $status_aktif;
                            ;
                        } else {
                            $note_logs = $kodeproduk . " | " . $namaproduk . " | " . $uomproduk . " | " . $uomproduk2 . " | " . $lebargreige . " " . $uom_lebargreige . " | " . $lebarjadi . " " . $uom_lebarjadi . " | " . $routeproduksi . " | " . $typeproduk . " | " . $dapatdibeli . " | " . $dapatdijual . " | " . $nmKategori['nama_category'] . " | " . $log_bom . " | " . ($parent['nama'] ?? "") . " | " . $nama_sub_parent . " | " . $jk['nama_jenis_kain'] . " | " . $status_aktif;
                        }

                        $jenis_log = "create";
                        $note_log = $note_logs;
                        $this->_module->gen_history($sub_menu, $kodeproduk, $jenis_log, ($note_log), $username);
                        $callback = array('status' => 'success', 'message' => 'Data Berhasil Disimpan !', 'isi' => $kodeproduk_encr, 'icon' => 'fa fa-check', 'type' => 'success',);
                    }
                }

                if ($_FILES['foto']['size'] !== 0) {
                    $config["upload_path"] = "./upload/product/";
                    $config["allowed_types"] = "jpg";
                    $config["file_name"] = $kodeproduk;
                    $this->upload->initialize($config);

                    if ($this->upload->do_upload("foto")) {
                        $datas = $this->upload->data();
                        $dname = explode(".", $datas["file_name"]);
                        $ext = end($dname);
                        $this->resizeImage($datas, [
                            [
                                'width' => 150,
                                'height' => 150,
                                "name" => './upload/product/thumb-' . $kodeproduk . "." . $ext
                            ]
                        ]);

                        $this->_module->update_perbatch("update mst_produk set image='{$datas["file_name"]}' where id={$ids}");
                    }
                }
            }

            if (!$this->_module->finishTransaction()) {
                $callback = array('status' => 'failed', 'field' => 'kodeproduk', 'message' => 'Gagal Menyimpan Data', 'icon' => 'fa fa-warning', 'type' => 'danger');
                throw new \Exception('', 500);
            }
            echo json_encode($callback);
        } catch (Exception $ex) {
            $this->_module->rollbackTransaction();
            log_message("error",json_encode($ex));
            echo json_encode($callback);
        } finally {
            //unlock table
            $this->_module->unlock_tabel();
        }
    }

    public function get_coa_list() {
        $coa = $this->input->post('glacc');
        $callback = $this->m_produk->get_list_coa($coa);
        echo json_encode($callback);
    }

    public function edit($id = null) {
        if (!isset($id))
            show_404();
        $username = $this->session->userdata('username');
        $kode_decrypt = decrypt_url($id);
        if(!$kode_decrypt)
            show_404();
        $data['id_dept'] = 'MPROD';
        $data['mms'] = $this->_module->get_data_mms_for_log_history('MPROD'); // get mms by dept untuk log history
        $produk = $this->m_produk->get_produk_by_kode($kode_decrypt); //id auto increment
        $data['produk'] = $produk;
        $data["catatan"] = $this->m_produk->getCatatan($kode_decrypt);
        $data['uom'] = $this->m_produk->get_list_uom();
        $data["uom_beli"] = $this->m_konversiuom->wheres(["id" => $produk->uom_beli])->getDetail();
        $data['category'] = $this->m_produk->get_list_category();
        $data['route'] = $this->m_produk->get_list_route();
        $data["id"] = $id;
        $harga = new $this->m_coa;
        $data["coa"] = $this->m_coa->setJoins("mst_produk_coa", "(mst_produk_coa.kode_coa = coa.kode_coa and level = 5)")->setWheres(['jenis' => 'pembelian', 'kode_produk' => $produk->kode_produk])
                        ->setSelects(['coa.*'])->getDetail();

        $data["harga"] = $harga->setTables("mst_produk_harga")->setWheres(['jenis' => 'pembelian', 'kode_produk' => $produk->kode_produk])->getDetail();
        $masking = [];
        foreach ($this->m_user->getMasking($username) as $value) {
            $masking[] = $value->mst_category_id;
        }
        $data["masking"] = $masking;

        //get data untuk glyphicon
        $data['onhand'] = $this->m_produk->get_qty_onhand($produk->kode_produk);
        $data['moves'] = $this->m_produk->get_jml_moves($produk->kode_produk);
        $data['bom'] = $this->m_produk->get_jml_bom($produk->kode_produk);
        $data['mo'] = $this->m_produk->get_jml_mo($produk->kode_produk);
        $data['jenis_kain'] = $this->m_produk->get_list_jenis_kain();

        //$data['dyest']    = $this->m_lab->get_data_dye_aux_by_code($kode_decrypt,'DYE');
        //$data['aux']      = $this->m_lab->get_data_dye_aux_by_code($kode_decrypt,'AUX');
        if (empty($data["produk"])) {
            show_404();
        } else {
            return $this->load->view('warehouse/v_produk_edit', $data);
        }
    }

    function view_list_bom_produk_modal() {
        $kode_produk = $this->input->post('kode_produk');

        $data['kode_produk'] = $kode_produk;
        return $this->load->view('modal/v_produk_list_bom_modal', $data);
    }

    public function delete_image() {
        try {
            $id = $this->input->post("key");

            $produk = $this->m_produk->get_produk_by_kode($id);
            if (!$produk) {
                throw new \Exception("data tidak ditemukan", 500);
            }
//            $this->_module->startTransaction();
            $this->_module->update_perbatch("update mst_produk set image='' where id={$id}");
            if (count(explode(".", $produk->image)) > 1) {
                unlink(FCPATH . "/upload/product/" . $produk->image);
                unlink(FCPATH . "/upload/product/thumb-" . $produk->image);
            }
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array()));
        } catch (Exception $ex) {
//            $this->_module->rollbackTransaction();
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }

    function get_data_list_bom_produk() {
        $kode_produk = addslashes($this->input->post('kode_produk'));
        $list = $this->m_produk->get_datatables2($kode_produk);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $field) {
            $kode_encrypt = $this->encryption->encrypt($field->kode_bom);
            $kode_encrypt = encrypt_url($field->kode_bom);
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = '<a href="' . base_url('ppic/billofmaterials/edit/' . $kode_encrypt) . '" target="_blank">' . $field->kode_bom . '</a>';
            $row[] = $field->nama_bom;
            $row[] = $field->kode_produk;
            $row[] = $field->nama_produk;
            $row[] = $field->qty;
            $row[] = $field->uom;
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_produk->count_all2($kode_produk),
            "recordsFiltered" => $this->m_produk->count_filtered2($kode_produk),
            "data" => $data,
        );
        //output dalam format JSON
        echo json_encode($output);
    }

    function view_list_mo_produk_modal() {
        $kode_produk = $this->input->post('kode_produk');

        $data['kode_produk'] = $kode_produk;
        return $this->load->view('modal/v_produk_list_mo_modal', $data);
    }

    function get_data_list_mo_produk() {
        $kode_produk = addslashes($this->input->post('kode_produk'));
        $list = $this->m_produk->get_datatables3($kode_produk);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $field) {
            $kode_encrypt = encrypt_url($field->kode);
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = '<a href="' . base_url('manufacturing/mO/edit/' . $kode_encrypt) . '">' . $field->kode . '</a>';
            $row[] = $field->tanggal;
            $row[] = $field->departemen;
            $row[] = $field->nama_produk;
            $row[] = $field->qty;
            $row[] = $field->uom;
            $row[] = $field->nama_status;
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_produk->count_all3($kode_produk),
            "recordsFiltered" => $this->m_produk->count_filtered3($kode_produk),
            "data" => $data,
        );
        //output dalam format JSON
        echo json_encode($output);
    }

    protected function resizeImage($dataFile, array $resize) {
        $config["image_library"] = "gd2";
        $config["maintain_ratio"] = false;
        $config["quality"] = "85%";
        $config["source_image"] = $dataFile["full_path"];
        $this->load->library("image_lib");
        foreach ($resize as $key => $value) {
            $config["width"] = $value["width"];
            $config["height"] = $value["height"];
            $config["new_image"] = $value["name"];
            $this->image_lib->initialize($config);
            $this->image_lib->resize();
            $this->image_lib->clear();
        }
    }

    public function save_catatan($id) {
        try {
            $sub_menu = $this->uri->segment(2);
            $username = $this->session->userdata('username');
            $kode_decrypt = decrypt_url($id);
            $val_form = array(
                [
                    'field' => 'catatan',
                    'label' => 'Catatan',
                    'rules' => ['trim', 'required'],
                    'errors' => [
                        'required' => '{field} Harus diisi'
                    ]
                ],
                [
                    'field' => 'jenis_catatan',
                    'label' => 'Jenis Catatan',
                    'rules' => ['trim', 'required'],
                    'errors' => [
                        'required' => '{field} Harus dipilih'
                    ]
                ]
            );
            $this->form_validation->set_rules($val_form);
            if ($this->form_validation->run() == FALSE) {
                throw new \Exception(array_values($this->form_validation->error_array())[0], 500);
            }
            $catatan = $this->input->post("catatan");
            $jenis_catatan = $this->input->post("jenis_catatan");
            $kode_produk = $this->input->post("produk");
            $this->m_produk->saveCatatan(['produk_id' => $kode_decrypt, 'catatan' => $catatan, 'jenis_catatan' => $jenis_catatan, 'kode_produk' => $kode_produk]);
            $this->_module->gen_history($sub_menu, $kode_produk, "edit", "update Catatan " . $catatan, $username);
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array()));
        } catch (Exception $ex) {
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }

    public function get_coa() {
        try {
            $search = $this->input->post("search");
            $coa = new $this->m_coa;
            $_POST['search'] = array(
                'value' => $search
            );
            $_POST['length'] = 20;
            $_POST['start'] = 0;
//            if (!empty($search)) {
//                $coa->setWhereRaw("(kode_coa like '%{$search}%' or nama like '%{$search}%')");
//            }
            $data = $coa->setWheres(['level' => 5])->setSearch(["kode_coa", "nama"])->setOrder(['kode_coa' => "asc"])->setSelects(['kode_coa', 'nama'])->getData();
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'success', 'icon' => 'fa fa-warning', 'type' => 'danger', 'data' => $data)));
        } catch (\Exception $ex) {
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }

    public function save_coa($id) {
        try {
            $sub_menu = $this->uri->segment(2);
            $username = $this->session->userdata('username');
            $kode_produk = decrypt_url($id);
            $kode_coa = $this->input->post("coa");
            $jenis = $this->input->post("jenis");
            $check = new $this->m_coa;
            $insertUpdate = clone $check;
            if ($check->setTables("mst_produk_coa")->setWheres(["kode_produk" => $kode_produk, 'jenis' => $jenis])->getDetail()) {
                $insertUpdate->setTables("mst_produk_coa")->setWheres(["kode_produk" => $kode_produk, 'jenis' => $jenis])->update(['kode_coa' => $kode_coa]);
            } else {
                $insertUpdate->setTables("mst_produk_coa")->save(["kode_produk" => $kode_produk, 'jenis' => $jenis, 'kode_coa' => $kode_coa]);
            }
            $this->_module->gen_history($sub_menu, $kode_produk, "edit", "update kode coa " . $kode_coa, $username);
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'success', 'icon' => 'fa fa-warning', 'type' => 'danger')));
        } catch (Exception $ex) {
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }

    public function save_harga($id) {
        $validation = [
            [
                'field' => 'harga',
                'label' => 'Harga',
                'rules' => ['required', 'regex_match[/^\d*\.?\d*$/]'],
                'errors' => [
                    'required' => '{field} Harus dipilih',
                    "regex_match" => "{field} harus berupa number / desimal"
                ]
            ]
        ];
        try {
            $sub_menu = $this->uri->segment(2);
            $username = $this->session->userdata('username');
            $kode_produk = decrypt_url($id);
            $harga = $this->input->post("harga");
            $jenis = $this->input->post("jenis");
            $this->form_validation->set_rules($validation);
            if ($this->form_validation->run() == FALSE) {
                throw new \Exception(array_values($this->form_validation->error_array())[0], 500);
            }
            
            $check = new $this->m_coa;
            $insertUpdate = clone $check;
            if ($check->setTables("mst_produk_harga")->setWheres(["kode_produk" => $kode_produk, 'jenis' => $jenis])->getDetail()) {
                $insertUpdate->setTables("mst_produk_harga")->setWheres(["kode_produk" => $kode_produk, 'jenis' => $jenis])->update(['harga' => $harga]);
            } else {
                $insertUpdate->setTables("mst_produk_harga")->save(["kode_produk" => $kode_produk, 'jenis' => $jenis, 'harga' => $harga]);
            }
            $this->_module->gen_history($sub_menu, $kode_produk, "edit", "update Harga {$jenis} " . $harga, $username);
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'success', 'icon' => 'fa fa-warning', 'type' => 'danger')));
        } catch (Exception $ex) {
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }
    
    public function hapuscatatan($id) {
        try {
            $sub_menu = $this->uri->segment(2);
            $username = $this->session->userdata('username');
            $kode_decrypt = decrypt_url($id);
            $ids = $this->input->post("ids");
            $catatan = $this->input->post("catatan");
            $model = new $this->m_global;
            $model->setTables("mst_produk_catatan")->setWheres(["id"=>$ids])->delete();
            $this->_module->gen_history($sub_menu, $kode_decrypt, "cancel", "Hapus Catatan `{$catatan}`", $username);
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'success', 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }catch (Exception $ex) {
            $this->output->set_status_header(($ex->getCode() ?? 500))
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }
    
    public function get_view_konversi() {
        try {
            $data['uom'] = $this->_module->get_list_uom();
            $html = $this->load->view('modal/v_produk_konversi_oum', $data,true);
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'success', 'icon' => 'fa fa-warning', 'type' => 'danger','data'=>$html)));
        } catch (Exception $ex) {
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger','data'=>"")));
        }
    }
}
