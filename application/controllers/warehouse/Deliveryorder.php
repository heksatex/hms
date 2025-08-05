<?php

defined('BASEPATH') or exit('No Direct Script Acces Allowed');
/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of Deliveryorder
 *
 * @author RONI
 */
class Deliveryorder extends MY_Controller {

    //put your code here
    public function __construct() {
        parent::__construct();
        $this->is_loggedin();
        $this->load->model("_module");
        $this->load->model("m_Picklist");
        $this->load->model("m_PicklistDetail");
        $this->load->model("m_deliveryorder");
        $this->load->model("m_deliveryorderdetail");
        $this->load->model("m_bulkdetail");
        $this->load->model("m_bulk");
        $this->load->model("m_user");
        $this->load->model("m_Pickliststockquant");
        $this->load->library("token");
        $this->load->library("wa_message");
    }

    public function index() {
        $data['id_dept'] = 'DO';
        $this->load->view('warehouse/v_do', $data);
    }

    public function data_picklist() {
        try {
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(['data' => $this->load->view('warehouse/v_do_picklist', [], true)]));
        } catch (Exception $ex) {
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }

    public function add($pl = null) {
        try {
            $kode_decrypt = decrypt_url($pl);
            if (!$kode_decrypt) {
                throw new \Exception();
            }
            $data['id_dept'] = 'DO';
            $data["section"] = "ADD";
            $data['picklist'] = $this->m_Picklist->getDataByID(['picklist.no' => $kode_decrypt], '', 'delivery');
            if (is_null($data["picklist"])) {
                throw new Exception();
            }
            $data["user"] = $this->m_user->get_user_by_username($this->session->userdata('username'));

//            $data['total_detail'] = $recordsTotal ?? 0;
            $this->load->view('warehouse/v_do_add', $data);
        } catch (Exception $ex) {
            show_404();
        }
    }

    public function edit($no) {
        try {
            $kode_decrypt = decrypt_url($no);
            if (!$kode_decrypt) {
                throw new \Exception();
            }
            $data['id_dept'] = 'DO';
            $data["id"] = $no;
//            $data["section"] = "EDIT";
            $data["user"] = $this->m_user->get_user_by_username($this->session->userdata('username'));
            $data["do"] = $this->m_deliveryorder->getDataDetail(['a.no' => $kode_decrypt]);
            $total_status = ["valid" => 0, "invalid" => 0];
            if (is_null($data["do"])) {
                throw new Exception();
            }
            if ($data["do"]->status === 'draft') {
                $getTotalItem = $this->m_deliveryorder->getTotalBarcode(['no_pl' => $data["do"]->no_picklist, 'valid !=' => 'cancel']);
                $recordsTotal = $this->m_PicklistDetail->getCountDetail(['no_pl' => $data["do"]->no_picklist, 'valid !=' => 'cancel']);

                foreach ($getTotalItem as $key => $value) {
                    if (in_array($value->valid, ["done", "validasi", "retur"])) {
                        $total_status["valid"] += $value->total;
                        continue;
                    }
                    $total_status["invalid"] += $value->total;
                }
            } else {
//                $recordsTotal = $this->m_deliveryorderdetail->countData(
//                        [
//                            'dod.do_id' => $data["do"]->id
//                        ]
//                );
                $recordsTotal = $this->m_deliveryorderdetail->countDetail(['dod.do_id' => $data["do"]->id]);
            }
            $data['picklist'] = $this->m_Picklist->getDataByID(['picklist.no' => $data["do"]->no_picklist], '', 'delivery');

            $data['total_detail'] = $recordsTotal;
            $data["total_status"] = $total_status;
            $this->load->view('warehouse/v_do_edit', $data);
        } catch (Exception $ex) {
            show_404();
        }
    }

    public function list_detail_view() {
        try {
            $data ["id"] = $this->input->post("id");
            $data ["pl"] = $this->input->post("pl");
            $data["doid"] = $this->input->post("doid");
            $data["type"] = $this->input->post("type");
            $data ["form"] = "edit";
            $datas = $this->load->view("warehouse/v_do_list_detail", $data, true);
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(['data' => $datas]));
        } catch (Exception $ex) {
            $data ["pl"] = $this->input->post("pl");
            $datas = $this->load->view("warehouse/v_do_list_detail_bulk", $data, true);
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(['data' => $datas]));
        }
    }

    public function list_detail_view_bulk() {
        try {
            $data ["pl"] = $this->input->post("pl");
            $datas = $this->load->view("warehouse/v_do_list_detail_bulk", $data, true);
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(['data' => $datas]));
        } catch (Exception $ex) {
            
        }
    }

    public function list_detail_view_add() {
        try {
            $data ["bulk"] = $this->input->post("bulk");
            $data ["pl"] = $this->input->post("pl");
            $data ["not_in"] = $this->input->post("not_in");
            $data ["form"] = "add";
            $data ["type"] = $this->input->post("type");
            $datas = $this->load->view("warehouse/v_do_list_detail", $data, true);
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(['data' => $datas]));
        } catch (Exception $ex) {
            
        }
    }

    public function list_detail_bulk() {
        try {
            $pl = $this->input->post("pl");
//            $condition = ["b.no_pl" => $pl, "pd.no_pl" => $pl, 'pd.valid <>' => "cancel"];
            $condition = ["pd.no_pl" => $pl, 'pd.valid <>' => "cancel"];
            $data = array();
            $no = $_POST['start'];
            $whereRaw = "(b.no_pl = '{$pl}' or b.no_pl is null)";
            $list = $this->m_bulkdetail->getDataBulk($condition, $whereRaw);
            $recordsTotal = $this->m_bulkdetail->getCountAllDataBulk($condition, $whereRaw);
            $recordsFiltered = $this->m_bulkdetail->getCountDataFilteredBulk($condition, $whereRaw);
            foreach ($list as $value) {
                $no++;
                $data[] = array(
                    $no,
                    $value->bulk_no_bulk ?? "",
                    $value->barcode_id ?? "",
                    $value->corak_remark ?? "",
                    $value->warna_remark ?? "",
                    $value->qty ?? ""
                );
            }
            echo json_encode(array("draw" => $_POST['draw'],
                "recordsTotal" => $recordsTotal,
                "recordsFiltered" => $recordsFiltered,
                "data" => $data,
            ));
            exit();
        } catch (Exception $ex) {
            
        }
    }

    public function list_detail() {
        try {

            $pl = $this->input->post("pl");
            $doid = $this->input->post("doid");
            $bulk = $this->input->post("bulk");
            $type = $this->input->post("tipe");
            $notin = json_decode($this->input->post('not_in'));
            $form = $this->input->post("form");
            $condition = ["pd.no_pl" => $pl, 'dod.do_id' => $doid];
            $data = array();
            $recordsTotal = 0;
            $recordsFiltered = 0;
            $list = array();
            $no = $_POST['start'];
            if ($form === 'edit') {
                $join = [];
                if ($type === "1") {
                    $join = ["BULK"];
                }
//                $condition = array_merge($condition, ['pd.valid !=' => 'cancel']);
                 $condition = array_merge($condition);
                $list = $this->m_deliveryorderdetail->getDataDetail($condition, $join);
                $recordsTotal = $this->m_deliveryorderdetail->getDataDetailCountAll($condition, $join);
                $recordsFiltered = $this->m_deliveryorderdetail->getDataDetailCountFiltered($condition);
                if ($type === "1") {
                    foreach ($list as $field) {
                        $no++;
                        $row = [
                            $no,
                            $field->bulk_no_bulk,
                            $field->barcode_id,
                            $field->corak_remark,
                            $field->warna_remark,
                            $field->lebar_jadi . ' ' . $field->uom_lebar_jadi,
                            $field->qty . ' ' . $field->uom
                        ];
                        $data[] = $row;
                    }
                } else {
                    foreach ($list as $field) {
                        $no++;
                        $row = [
                            $no,
                            $field->barcode_id,
                            $field->corak_remark,
                            $field->warna_remark,
                            $field->lebar_jadi . ' ' . $field->uom_lebar_jadi,
                            $field->qty . ' ' . $field->uom
                        ];
                        $data[] = $row;
                    }
                }
            } else {
                $whereNotIn = [];
                $whereIn = [];
                $join = [];
                if (count($notin) > 0) {
                    $whereNotIn = ['a.barcode_id' => $notin];
                }
                $condition = ['a.no_pl' => $pl, 'a.valid ' => 'validasi'];

                if ($type === "1" && count(json_decode($bulk)) === 0) {
                    throw new Exception("");
                }
                if (count(json_decode($bulk)) > 0) {
                    $join[] = "BULK";
                    $whereIn = ['dt.bulk_no_bulk' => json_decode($bulk)];
                }
                $list = $this->m_PicklistDetail->getDataViewDodd($condition, $join, $whereNotIn, $whereIn);
                $recordsFiltered = $this->m_PicklistDetail->getCountDataFilteredViewDodd($condition, $join, $whereNotIn, $whereIn);
                $recordsTotal = $this->m_PicklistDetail->getCountAllDataViewDodd($condition, $join, $whereNotIn, $whereIn);
                if ($type === "1") {
                    foreach ($list as $field) {
                        $no++;
                        $row = [
                            $no,
                            $field->bulk_no_bulk ?? "",
                            $field->barcode_id,
                            $field->corak_remark,
                            $field->warna_remark,
                            $field->lebar_jadi . ' ' . $field->uom_lebar_jadi,
                            $field->qty . ' ' . $field->uom
                        ];
                        $data[] = $row;
                    }
                } else {
                    foreach ($list as $field) {
                        $no++;
                        $row = [
                            $no,
                            $field->barcode_id,
                            $field->corak_remark,
                            $field->warna_remark,
                            $field->lebar_jadi . ' ' . $field->uom_lebar_jadi,
                            $field->qty . ' ' . $field->uom
                        ];
                        $data[] = $row;
                    }
                }
            }


            echo json_encode(array("draw" => $_POST['draw'],
                "recordsTotal" => $recordsTotal,
                "recordsFiltered" => $recordsFiltered,
                "data" => $data,
            ));
            exit();
        } catch (Exception $ex) {
            echo json_encode(array("draw" => $_POST['draw'],
                "recordsTotal" => 0,
                "recordsFiltered" => 0,
                "data" => [],
            ));
        }
    }

    public function update() {
        try {
            $username = $this->session->userdata('username');
            $users = $this->session->userdata('nama');
            $sub_menu = $this->uri->segment(2);

            $note = $this->input->post("note");
            $dok_date = $this->input->post("dok_date");
            $id = $this->input->post("doid");
            $data = $this->m_deliveryorder->getDataDetail(['a.no' => $id]);
            if (empty($data)) {
                throw new \Exception("No DO Tidak ditemukan", 500);
            }

            if (in_array($data->status, ["cancel"])) {
                throw new \Exception("DO Dalam Status " . $data->status, 500);
            }

            $this->_module->startTransaction();
            $update = ["note" => $note];
            $condition = ["delivery_order.no" => $id];
            $addMessage = "";
            if ($data->status === "done") {
                $user = $this->m_user->get_user_by_username($username);
                if (in_array($user->level, ["Entry Data", ""])) {
                    throw new \Exception('Akses tidak diijinkan', 500);
                }
            }
            if ($data->tanggal_dokumen === $dok_date) {
                
            } else {
                $this->_module->lock_tabel("token_increment WRITE,token_increment it READ,user WRITE, main_menu_sub WRITE, log_history WRITE,"
                        . "delivery_order WRITE,delivery_order_sj WRITE");
                $nosjs = $this->m_deliveryorder->checkNoSJ(['no_sj LIKE' => $data->tipe_no_sj . '/' . date('y', strtotime($dok_date)) . '/' . getRomawi(date('m', strtotime($dok_date))) . '/%']);
//                $nosjs = $this->m_deliveryorder->checkNoSJ(['no_sj LIKE' => $data->tipe_no_sj . '%']);
                if (!is_null($nosjs)) {
                    $nosj = $nosjs->no_sj;
                    $this->m_deliveryorder->deleteNoSJ(['no_sj' => $nosj]);
                } else {
                    if (!$nosj = $this->token->noUrut('delivery_' . $data->tipe_no_sj, date('y', strtotime($dok_date)) . '/' . getRomawi(date('m', strtotime($dok_date))), true)
                                    ->generate($data->tipe_no_sj . '/', '/%04d')->get()) {
                        throw new \Exception("No SJ tidak terbuat", 500);
                    }
                }
                $insertSJ = $this->m_deliveryorder->insertNoSJ(['no_sj' => $data->no_sj]);
                if (is_null($insertSJ)) {
                    throw new \Exception('Gagal Menyimpan Data', 500);
                }

                $update = array_merge($update, ['no_sj' => $nosj, 'tanggal_dokumen' => $dok_date]);
                $addMessage = "No SJ Berubah Dari " . $data->no_sj . ' Menjadi ' . $nosj;
            }
            $this->m_deliveryorder->update($update, $condition);

            if (!$this->_module->finishTransaction()) {
                throw new \Exception('Gagal Menyimpan Data', 500);
            }
            $this->_module->gen_history($sub_menu, $id, 'edit', ($users["nama"] ?? $username) . ' Mengubah Dokumen ' . $addMessage . ' DO - ' . $id, $username);
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success')));
        } catch (Exception $ex) {
            $this->_module->rollbackTransaction();
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        } finally {
            $this->_module->unlock_tabel();
        }
    }

    public function save() {
        try {
            $username = $this->session->userdata('username');
            $users = $this->session->userdata('nama');
            $sub_menu = $this->uri->segment(2);

            $tipe_no_sj = $this->input->post("no_sj_jenis");
            $nosj = "";
            $nodo = "";
            $tanggal_dokumen = $this->input->post("tanggal_dokumen");
            $time_dokumen = strtotime($tanggal_dokumen);
            $pl = $this->input->post("pl");
            $this->_module->startTransaction();
            $this->_module->lock_tabel("picklist READ,token_increment WRITE,token_increment it READ,user WRITE, main_menu_sub WRITE, log_history WRITE,"
                    . "delivery_order WRITE,delivery_order_sj WRITE,delivery_order a READ");
            $now = date("Y-m-d H:i:s");
            if (!$nodo = $this->token->noUrut('deliveryorder', date('ym'), true)->generate('DO', '%04d')->get()) {
                throw new \Exception("No Delivery Order tidak terbuat", 500);
            }
            $nosjs = $this->m_deliveryorder->checkNoSJ(['no_sj LIKE' => $tipe_no_sj . '/' . date('y', $time_dokumen) . '/' . getRomawi(date('m', $time_dokumen)) . '/%']);
            if (!is_null($nosjs)) {
                $nosj = $nosjs->no_sj;
                $this->m_deliveryorder->deleteNoSJ(['no_sj' => $nosj]);
            } else {
                if (!$nosj = $this->token->noUrut('delivery_' . $tipe_no_sj, date('y', $time_dokumen) . '/' . getRomawi(date('m', $time_dokumen)), true)->generate($tipe_no_sj . '/', '/%04d')->get()) {
                    throw new \Exception("No SJ tidak terbuat", 500);
                }
            }
            $check = $this->m_deliveryorder->getDataDetail(["no_picklist" => $pl, "status <>" => "cancel"]);
            if ($check) {
                throw new Exception("PL Sudah terbuat Delivery ", 500);
            }
            $tgl_dok = date("Y-m-d H:i:s", $time_dokumen);
            $diff = date_diff(date_create(date("Y-m-d")), date_create(date("Y-m-d", $time_dokumen)));
            $interval = $diff->format("%a");
            $data = [
                'no' => $nodo,
                'no_sj' => $nosj,
                'tanggal_dokumen' => ($interval < 1) ? $now : $tgl_dok,
                'tanggal_buat' => $now,
                'note' => $this->input->post("ket"),
                'status' => 'draft',
                'no_picklist' => $pl,
                'rev' => $this->input->post("rev"),
                'tipe_no_sj' => $tipe_no_sj,
                'notifikasi' => 0,
                'user' => $users["nama"] ?? $username
            ];
            $idd = $this->m_deliveryorder->insert($data);
            if (is_null($idd)) {
                throw new Exception("Gagal Membuat Surat Jalan ", 500);
            }
            $kode_decrypt = encrypt_url($nodo);
            $this->_module->gen_history($sub_menu, $data['no'], 'create', ($users["nama"] ?? $username) . ' Menambahkan Dokumen DO - ' . $data["no"], $username);

            if (!$this->_module->finishTransaction()) {
                throw new \Exception('Gagal Menyimpan Data', 500);
            }
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Surat Jalan berhasil dibuat', 'icon' => 'fa fa-check', 'type' => 'success', 'data' => $kode_decrypt)));
        } catch (Exception $ex) {
            $this->_module->rollbackTransaction();
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        } finally {
            $this->_module->unlock_tabel();
        }
    }

    public function add_item() {
        try {
            $username = $this->session->userdata('username');
            $users = $this->session->userdata('nama');
            $sub_menu = $this->uri->segment(2);

            $nodo = $this->input->post("nodo");
            $type_bulk = $this->input->post("type_bulk");
            $bulks = $this->input->post("bulks");

            $data_do = $this->m_deliveryorder->getDataDetail(['a.no' => $nodo, 'a.status' => 'draft'], true, "a.*,p.status as picklist_status");
            if (empty($data_do)) {
                throw new \Exception('Data DO harus dalam status draft', 500);
            }
            if ($data_do->faktur === "1") {
                throw new \Exception('Data DO Sudah masuk Faktur', 500);
            }
            if ($data_do->picklist_status != "validasi") {
                throw new \Exception('Picklist harus dalam status Validasi', 500);
            }
            $condition = ["no_pl" => $data_do->no_picklist, "valid" => "validasi"];
            $this->_module->startTransaction();
            $this->_module->lock_tabel("user WRITE, main_menu_sub WRITE, log_history WRITE,delivery_order_detail WRITE,picklist WRITE,picklist_detail WRITE,"
                    . "delivery_order WRITE,delivery_order_sj WRITE,stock_move_produk WRITE,stock_move_items WRITE,stock_move WRITE,deliveryorder_stock_move WRITE,"
                    . "stock_quant WRITE,stock_quant sq WRITE,picklist_detail a WRITE,picklist_detail pd WRITE,mst_produk mp WRITE,bulk_detail bd WRITE,bulk b WRITE");
            $nosm = "SM" . $this->_module->get_kode_stock_move();
            $smdata = "('" . $nosm . "','" . date("Y-m-d H:i:s") . "','" . $nodo . "|1','GJD|OUT','GJD/Stock','CST/Stock','done','1','')";
            $this->_module->create_stock_move_batch($smdata);
            $whereNotIn = [];
            $wherein = [];
            $notin = json_decode("[]");
            if (count($notin) > 0) {
                $whereNotIn = ['barcode_id' => $notin];
            }
            if ($type_bulk === "1") {
                $bulkList = json_decode($bulks);
                if (is_null($bulkList) || count($bulkList) < 1) {
                    throw new Exception("Silahkan scan item terlebih dahulu", 500);
                }
                $condition = ["a.no_pl" => $data_do->no_picklist, "b.no_pl" => $data_do->no_picklist];
                $wherein = $bulkList;
                if (count($notin) > 0) {
                    $whereNotIn = ['bd.bulk_no_bulk' => $notin];
                }
            }
            $item = $this->getItemBarcode($condition, (int) $type_bulk, $wherein, $whereNotIn);
            $rowMoveItem = $this->_module->get_row_order_stock_move_items_by_kode($nosm);
            $listBarcode = [];

            $smproduk = [];
            $insertDetail = [];
            $insertStokMvItem = [];
            $insertStokMvProd = [];
            $updateStokQuant = [];
            $check_barcode = [];
            foreach ($item as $key => $value) {
                $check = $this->checkLokasi(['stock_quant.quant_id' => $value->quant_id]);
                if (!empty($check)) {
                    throw new \Exception($check, 500);
                }
                if (in_array($value->quant_id, $check_barcode)) {
                    throw new \Exception("ada Duplikat Barcode di Picklist", 500);
                }
                $insertDetail[] = ['do_id' => $data_do->id, 'barcode_id' => $value->barcode_id, 'picklist_detail_id' => $value->picklist_detail_id, 'status' => 'done'];
                $insertStokMvItem[] = "('" . $nosm . "','" . $value->quant_id . "','" . $value->kode_produk . "','" . addslashes($value->nama_produk). "','" .
                        $value->barcode_id . "','" . $value->qty . "','" . $value->uom . "','" . $value->qty2 . "','" . $value->uom2 . "','done','" . $rowMoveItem . "','','" . date("Y-m-d H:i:s") . "','" .
                        $value->lokasi_fisik . "','" . $value->lebar_greige . "','" . $value->uom_lebar_greige . "','" . $value->lebar_jadi . "','" . $value->uom_lebar_jadi . "')";
                $updateStokQuant [] = ["move_date" => date('Y-m-d H:i:s'), "lokasi_fisik" => "", "lokasi" => "CST/Stock", 'quant_id' => $value->quant_id];
                $check_barcode[] = $value->quant_id;
                if (isset($smproduk[$value->kode_produk])) {
                    $smproduk[$value->kode_produk]["qty"] += $value->qty;
                } else {
                    $smproduk[$value->kode_produk] = array(
                        'qty' => $value->qty,
                        'uom' => $value->uom,
                        'nama' => addslashes($value->nama_produk),
                        'order' => count($smproduk) + 1
                    );
                }
                $rowMoveItem++;
                $listBarcode[] = $value->barcode_id;
            }

            foreach ($smproduk as $key => $value) {
                $insertStokMvProd[] = "('" . $nosm . "','" . $key . "','" . $value['nama'] . "','" . $value['qty'] . "','" . $value['uom'] . "','done','" . $value['order'] . "','')";
            }

            $this->m_deliveryorder->update(['status' => 'done', 'tanggal_buat' => date("Y-m-d H:i:s")], ['id' => $data_do->id]);
            $this->_module->gen_history($sub_menu, $data_do->no, 'edit', ($users["nama"] ?? $username) . ' Merubah status DO - ' . $data_do->no . ' Menjadi TERKIRIM', $username);
            $this->m_deliveryorderdetail->insertBatch($insertDetail);
            $this->_module->simpan_stock_move_items_batch(implode(",", $insertStokMvItem));
            $this->_module->create_stock_move_produk_batch(implode(",", $insertStokMvProd));
            $this->m_Pickliststockquant->updateBatch($updateStokQuant, 'quant_id');
            $this->m_Picklist->update(['status' => 'done'], ["no" => $data_do->no_picklist, 'status' => 'validasi']);
            $this->m_PicklistDetail->updateStatusWin(['no_pl' => $data_do->no_picklist, 'valid' => 'validasi'], ['valid' => 'done'], ['barcode_id' => $listBarcode], true);
            $this->m_deliveryorder->insertDoMove(['move_id' => $nosm, 'no_do' => $nodo]);

            if (!$this->_module->finishTransaction()) {
                throw new \Exception('Gagal Menyimpan Data', 500);
            }
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Surat Jalan berhasil dibuat', 'icon' => 'fa fa-check', 'type' => 'success')));
        } catch (\Exception $ex) {
            $this->_module->rollbackTransaction();
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        } finally {
            $this->_module->unlock_tabel();
        }
    }

    public function batal_do_draft() {
        try {
            $username = $this->session->userdata('username');
            $users = $this->session->userdata('nama');
            $sub_menu = $this->uri->segment(2);

            $user = $this->m_user->get_user_by_username($username);
            if (in_array($user->level, ["Entry Data", ""])) {
                throw new \Exception('Akses tidak diijinkan', 500);
            }

            $pl = $this->input->post("pl");
            $nodo = $this->input->post("nodo");

            $data = $this->m_deliveryorder->getDataDetail(["a.no_picklist" => $pl, 'a.no' => $nodo, 'a.status' => 'draft'], true, "a.*,p.jenis_jual,p.type_bulk_id,pn.nama,msg.nama_sales_group as sales,msg.kode_sales_group as kode_sales,p.nama_user as user_picklist");
            if (empty($data)) {
                throw new \Exception('Data Tidak ditemukan', 500);
            }
            if ($data->faktur === "1") {
                throw new \Exception('Data DO sudah masuk Faktur', 500);
            }
            $this->_module->startTransaction();
            $insertSJ = $this->m_deliveryorder->insertNoSJ(['no_sj' => $data->no_sj]);
            if (is_null($insertSJ)) {
                throw new \Exception('Gagal Menyimpan Data', 500);
            }
            $tgl_batal = date("Y-m-d H:i:s");
            $this->m_deliveryorder->update(['status' => 'cancel', 'tanggal_batal' => $tgl_batal], ['id' => $data->id]);
            $dataPesan = [
                "{no_sj}" => $data->no_sj,
                "{customer}" => $data->nama,
                "{tgl_batal}" => $tgl_batal,
                "{no_pl}" => $data->no_picklist,
                "{no_do}" => $data->no,
                "{sales}" => $data->sales,
                "{jenis_jual}" => $data->jenis_jual,
                "{bulk}" => ($data->type_bulk_id === "1") ? "BAL" : "LOOSE PACKING"
            ];

            $mention = [];
//            $userss = $this->m_deliveryorder->userBC(['u.aktif' => '1', 'sales_group' => $data->kode_sales, 'telepon_wa !=' => '']);
            $userss = $this->m_deliveryorder->userBC(['nama' => $data->user, 'username' => 'prianto', 'nama' => $data->user_picklist]);
            foreach ($userss as $key => $value) {
                $mention[] = $value->telepon_wa;
            }
            $this->wa_message->sendMessageToGroup('cancel_do', $dataPesan, ['WAREHOUSE 24JAM'])->setMentions($mention)->setFooter('footer_hms')->send();

            $this->_module->gen_history($sub_menu, $nodo, 'cancel', ($users["nama"] ?? $username) . ' Membatalkan Dokumen DO - ' . $data->no, $username);
            if (!$this->_module->finishTransaction()) {
                throw new \Exception('Gagal Menyimpan Data', 500);
            }
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => '', 'icon' => 'fa fa-check', 'type' => 'success')));
        } catch (Exception $ex) {
            $this->_module->rollbackTransaction();
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }

    public function batal_do() {
        try {
            $username = $this->session->userdata('username');
            $users = $this->session->userdata('nama');
            $sub_menu = $this->uri->segment(2);

            $user = $this->m_user->get_user_by_username($username);
            if (in_array($user->level, ["Entry Data", ""])) {
                throw new \Exception('Akses tidak diijinkan', 500);
            }

            $pl = $this->input->post("pl");
            $nodo = $this->input->post("nodo");
            $this->_module->startTransaction();
            $this->_module->lock_tabel("user WRITE, main_menu_sub WRITE, log_history WRITE,delivery_order_detail WRITE,picklist p WRITE,delivery_order WRITE,user u WRITE,wa_template write,"
                    . "delivery_order_sj WRITE,stock_move_produk WRITE,stock_move_items WRITE,stock_move WRITE,deliveryorder_stock_move WRITE,delivery_order_detail dod WRITE,delivery_order a WRITE, delivery_order do WRITE,"
                    . "stock_quant WRITE,stock_quant sq WRITE,picklist_detail pd WRITE,mst_produk mp WRITE, partner pn write,mst_sales_group msg write,wa_group WRITE, picklist WRITE, picklist_detail WRITE,wa_send_message WRITE");
//            $data = $this->m_deliveryorder->getDataDetail(['a.no' => $do], true, "a.*,p.jenis_jual,p.type_bulk_id,pn.nama,msg.nama_sales_group as sales,msg.kode_sales_group as kode_sales");
            $data = $this->m_deliveryorder->getDataDetail(["a.no_picklist" => $pl, 'a.no' => $nodo, 'a.status' => 'done'], true, "a.*,p.jenis_jual,p.type_bulk_id,pn.nama,msg.nama_sales_group as sales,msg.kode_sales_group as kode_sales,p.nama_user as user_picklist");
            if (empty($data)) {
                throw new \Exception('Data Tidak ditemukan', 500);
            }
            if ($data->faktur === "1") {
                throw new \Exception('Data DO sudah masuk Faktur', 500);
            }
            $nosm = "SM" . $this->_module->get_kode_stock_move();
            $smdata = "('" . $nosm . "','" . date("Y-m-d H:i:s") . "','" . $nodo . "|1','GJD|IN','CST/Stock','GJD/Stock','done','1','')";
            $this->_module->create_stock_move_batch($smdata);
            $list = $this->m_deliveryorderdetail->getDataAll(['do_id' => $data->id, 'dod.status' => 'done', 'pd.valid' => 'done'], ['PD', 'SQ']);
            $rowMoveItem = $this->_module->get_row_order_stock_move_items_by_kode($nosm);
            $smproduk = [];
            $updateIn = [
                'do_id' => [$data->id],
                'barcode_id' => []
            ];
            $insertStokMvItem = [];
            $insertStokMvProd = [];
            $updateStokQuant = [];
            foreach ($list as $key => $value) {
                $check = $this->checkLokasi(['stock_quant.quant_id' => $value->quant_id], ['stock_quant.lokasi_fisik' => '', 'stock_quant.lokasi' => 'CST/Stock', 'id_category' => 21]);
                if (!empty($check)) {
                    throw new \Exception($check, 500);
                }
                $updateIn['barcode_id'][] = $value->barcode_id;
                $insertStokMvItem[] = "('" . $nosm . "','" . $value->quant_id . "','" . $value->kode_produk . "','" . addslashes($value->nama_produk) . "','" .
                        $value->barcode_id . "','" . $value->qty . "','" . $value->uom . "','" . $value->qty2 . "','" . $value->uom2 . "','done','" . $rowMoveItem . "','','" . date("Y-m-d H:i:s") . "','" .
                        $value->lokasi_fisik . "','','','" . $value->lebar_jadi . "','" . $value->uom_lebar_jadi . "')";

                $updateStokQuant [] = ["move_date" => date('Y-m-d H:i:s'), "lokasi_fisik" => "XPD", "lokasi" => "GJD/Stock", 'quant_id' => $value->quant_id];
                if (isset($smproduk[$value->kode_produk])) {
                    $smproduk[$value->kode_produk]["qty"] += $value->qty;
                } else {
                    $smproduk[$value->kode_produk] = array(
                        'qty' => $value->qty,
                        'uom' => $value->uom,
                        'nama' => addslashes($value->nama_produk),
                        'order' => count($smproduk) + 1
                    );
                }
                $rowMoveItem++;
            }

            foreach ($smproduk as $key => $value) {
                $insertStokMvProd[] = "('" . $nosm . "','" . $key . "','" . $value['nama'] . "','" . $value['qty'] . "','" . $value['uom'] . "','done','" . $value['order'] . "','')";
            }
            $tgl_batal = date("Y-m-d H:i:s");
            $this->m_deliveryorder->update(['status' => 'cancel', 'tanggal_batal' => $tgl_batal], ['id' => $data->id]);

            if (count($insertStokMvItem) > 0) {
                $this->_module->simpan_stock_move_items_batch(implode(",", $insertStokMvItem));
                $this->_module->create_stock_move_produk_batch(implode(",", $insertStokMvProd));
                $this->m_Pickliststockquant->updateBatch($updateStokQuant, 'quant_id');
                $this->m_deliveryorderdetail->update(["status" => "cancel"], [], $updateIn);
                $this->m_Picklist->update(['status' => 'validasi'], ["no" => $pl, 'status' => 'done']);
                $this->m_PicklistDetail->updateStatusWin(['no_pl' => $pl, 'valid' => 'done'], ['valid' => 'validasi']);

                $this->m_deliveryorder->insertDoMove(['move_id' => $nosm, 'no_do' => $nodo]);
            }

            $insertSJ = $this->m_deliveryorder->insertNoSJ(['no_sj' => $data->no_sj]);
            if (is_null($insertSJ)) {
                throw new \Exception('Gagal Menyimpan Data', 500);
            }
            $dataPesan = [
                "{no_sj}" => $data->no_sj,
                "{customer}" => $data->nama,
                "{tgl_batal}" => $tgl_batal,
                "{no_pl}" => $data->no_picklist,
                "{no_do}" => $data->no,
                "{sales}" => $data->sales,
                "{jenis_jual}" => $data->jenis_jual,
                "{bulk}" => ($data->type_bulk_id === "1") ? "BAL" : "LOOSE PACKING"
            ];

            $mention = [];
//            $userss = $this->m_deliveryorder->userBC(['u.aktif' => '1', 'sales_group' => $data->kode_sales, 'telepon_wa !=' => '']);
            $userss = $this->m_deliveryorder->userBC(['nama' => $data->user, 'username' => 'prianto', 'nama' => $data->user_picklist]);

            foreach ($userss as $key => $value) {
                $mention[] = $value->telepon_wa;
            }
            $this->wa_message->sendMessageToGroup('cancel_do', $dataPesan, ['WAREHOUSE 24JAM'])->setMentions($mention)->setFooter('footer_hms')->send();

            $this->_module->gen_history($sub_menu, $nodo, 'cancel', ($users["nama"] ?? $username) . ' Membatalkan Dokumen DO - ' . $data->no, $username);

            if (!$this->_module->finishTransaction()) {
                throw new \Exception('Gagal Menyimpan Data', 500);
            }
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => '', 'icon' => 'fa fa-check', 'type' => 'success')));
        } catch (Exception $ex) {
            $this->_module->rollbackTransaction();
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        } finally {
            $this->_module->unlock_tabel();
        }
    }

    public function data() {
        try {
            $data = array();
            $customer = $this->input->post("customer");
            $periode = $this->input->post("tanggal_kirim");
            $period = explode(" - ", $periode);
            $condition = ['pr.nama LIKE ' => '%' . $customer . '%'];
            if (count($period) > 1) {
                $tanggalAwal = date("Y-m-d H:i:s", strtotime($period[0] . " 00:00:00"));
                $tanggalAkhir = date("Y-m-d H:i:s", strtotime($period[1] . " 23:59:59"));
                $condition = array_merge($condition, ['tanggal_dokumen >=' => $tanggalAwal, 'tanggal_dokumen <=' => $tanggalAkhir]);
            }

            $list = $this->m_deliveryorder->getData($condition);
            $no = $_POST['start'];
            foreach ($list as $field) {
                $kode_encrypt = encrypt_url($field->no);
                $no++;
                $row = array(
                    $no,
                    '<a href="' . base_url('warehouse/deliveryorder/edit/' . $kode_encrypt) . '">' . $field->no . '</a>',
                    $field->no_sj,
                    $field->no_picklist,
                    $field->bulk,
                    $field->tanggal_dokumen,
                    $field->buyer,
                    $field->sales_nama,
                    $field->status,
                );
                $data[] = $row;
            }
            echo json_encode(array("draw" => $_POST['draw'],
                "recordsTotal" => $this->m_deliveryorder->getDataCountAll(),
                "recordsFiltered" => $this->m_deliveryorder->getDataCountFiltered($condition),
                "data" => $data,
            ));
            exit();
        } catch (Exception $ex) {
            echo json_encode(array("draw" => $_POST['draw'],
                "recordsTotal" => 0,
                "recordsFiltered" => 0,
                "data" => [],
            ));
        }
    }

    public function print($jenis, $id = null) {
        try {
            $kode_decrypt = decrypt_url($id);
            if (!$kode_decrypt) {
                throw new \Exception();
            }
            $condition = ["do.no" => $kode_decrypt];
            $base = $this->m_deliveryorder->getDataDetail(["a.no" => $kode_decrypt], true, "a.*,concat(pn.delivery_street,' ',pn.delivery_city,' ',pn.delivery_state) as alamat, pn.nama,p.type_bulk_id,alamat_kirim");
            $data["base"] = $base;
            if ($jenis === "sje") {
                $data["count_bulk"] = $this->m_deliveryorderdetail->getCountBulk($condition);
            }
//            $data["data"] = $this->m_deliveryorderdetail->getDataWGroup(array_merge($condition, ['pd.valid <>' => 'cancel']), (int) $base->type_bulk_id, $jenis);
            $data["data"] = $this->m_deliveryorderdetail->getDataWGroup($condition, (int) $base->type_bulk_id, $jenis);
            $this->load->view("print/do/" . $jenis, $data);
        } catch (Exception $ex) {
            show_404();
        }
    }

    public function check_bal() {
        try {
            $barcode = $this->input->post("search");
            $picklist = $this->input->post("picklist");
            $check = $this->m_bulk->getCountAllData(["no_bulk" => $barcode, "no_pl" => $picklist]);
            if ($check < 1) {
                throw new Exception("BAL " . $barcode . ' tidak ditemukan di picklist ' . $picklist, 500);
            }
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'BAL berhasil ditambahkan', 'icon' => 'fa fa-check', 'type' => 'success')));
        } catch (Exception $ex) {
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }

    public function get_total_item() {
        try {
            $picklist = $this->input->post("pl");
            $bal = $this->input->post("bal");
            $bulk = json_decode($bal);

            if (count($bulk) > 0) {
                $data = $this->m_bulkdetail->getTotalItemBulk(['pl.no_pl' => $picklist, 'b.no_pl' => $picklist], ["b.no_bulk" => $bulk]);
            } else {
                $data = $this->m_bulkdetail->getTotalItemBulk(['pl.no_pl' => $picklist, 'b.no_pl' => $picklist]);
            }
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'BAL berhasil ditambahkan', 'icon' => 'fa fa-check', 'type' => 'success', 'data' => $data)));
        } catch (Exception $ex) {
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }

    public function list_data_detail(int $type) {
        try {
            $data = array(
                "data" => array(),
                "total" => 0,
                "filtered" => 0
            );
            $whereNotIn = [];
            $no = $_POST['start'];
            $condition = ['pd.no_pl' => $this->input->post("pl"), 'pd.valid ' => 'validasi'];
            if ($type === 2) {
                $notin = json_decode($this->input->post('not_in'));
                if (count($notin) > 0) {
                    $whereNotIn = ['pd.barcode_id' => $notin];
                }
                $list = $this->m_PicklistDetail->getdoDataList($condition, [], $whereNotIn);
                foreach ($list as $field) {
                    $no++;
                    $row = array(
                        $no,
                        $field->corak_remark,
                        '-',
                        $field->warna_remark,
                        $field->jumlah_qty,
                        $field->total_qty,
                        $field->uom,
                        ""
                    );
                    $data["data"][] = $row;
                }
                $data["total"] = $this->m_PicklistDetail->getCountAlldoDataList($condition, [], $whereNotIn);
                $data["filtered"] = $this->m_PicklistDetail->getCountdoDataListFiltered($condition, [], $whereNotIn);
            } else {
                $bulk = json_decode($this->input->post("bulk"));
                $notin = json_decode($this->input->post('not_in'));
                if (count($notin) > 0) {
                    $whereNotIn = ['bd.bulk_no_bulk' => $notin];
                }
                if (empty($bulk)) {
                    $bulk = [];
                    throw new Exception();
                }
//                log_message('error', json_encode($bulk));
                $list = $this->m_PicklistDetail->getdoDataList($condition, $bulk, $whereNotIn);
//                log_message('error', json_encode($list));
                foreach ($list as $field) {
                    $no++;
                    $row = array(
                        $no,
                        $field->bulk_no_bulk,
                        $field->corak_remark,
                        '-',
                        $field->warna_remark,
                        $field->jumlah_qty,
                        $field->total_qty,
                        $field->uom,
                        ""
                    );
                    $data["data"][] = $row;
                }

                $data["total"] = $this->m_PicklistDetail->getCountAlldoDataList($condition, $bulk, $whereNotIn);
                $data["filtered"] = $this->m_PicklistDetail->getCountdoDataListFiltered($condition, $bulk, $whereNotIn);
            }
            echo json_encode(array("draw" => $_POST['draw'],
                "recordsTotal" => $data["total"],
                "recordsFiltered" => $data["filtered"],
                "data" => $data["data"],
            ));
            exit();
        } catch (Exception $ex) {
            echo json_encode(array("draw" => 0,
                "recordsTotal" => 0,
                "recordsFiltered" => 0,
                "data" => 0,
            ));
        }
    }

    protected function getItemBarcode($condition = [], $type_bulk = 1, $wherein = [], $whereNotIn = []) {
        if ($type_bulk === 2) {
            $data = $this->m_PicklistDetail->getBarcodeID($condition, $whereNotIn);
            return $data;
        }
        $data = $this->m_bulkdetail->getDataListBulks($condition, $wherein, $whereNotIn);
        return $data;
    }

    public function list_picklist() {
        try {
            $data = array();
            $condition = ['picklist.status !=' => "cancel"];
            $list = $this->m_Picklist->getData(false, $condition, ["DO", "delivery"]);
            $no = $_POST['start'];
            foreach ($list as $field) {
                $kode_encrypt = encrypt_url($field->no);
                $no++;
                $row = array(
                    $no,
                    '<a href="' . base_url('warehouse/deliveryorder/add/' . $kode_encrypt) . '">' . $field->no . '</a>',
                    $field->bulk_nama,
                    $field->sales_nama,
                    $field->nama
                );
                $data[] = $row;
            }
            echo json_encode(array("draw" => $_POST['draw'],
                "recordsTotal" => $this->m_Picklist->getCountAllData($condition, ["DO", 'delivery']),
                "recordsFiltered" => $this->m_Picklist->getCountDataFiltered($condition, ["DO", 'delivery']),
                "data" => $data,
            ));
            exit();
        } catch (Exception $ex) {
            echo json_encode(array("draw" => $_POST['draw'],
                "recordsTotal" => 0,
                "recordsFiltered" => 0,
                "data" => [],
            ));
        }
    }

    public function get_table_list_remove() {
        $picklist = $this->input->post("pl");
        echo $this->load->view("warehouse/v_do_list_remove", ['picklist' => $picklist], true);
    }

    public function get_list_data() {
        try {
            $data = array();
            $nod = decrypt_url($this->input->post('id'));
            $bulk = $this->input->post('bulk');
            $condition = ['do.no' => $nod];
            $list = $this->m_deliveryorderdetail->getData($condition, ["BULK"]);
            $no = $_POST['start'];
            if ((int) $bulk === 1) {
                foreach ($list as $field) {
                    $no++;

                    $row = array(
                        $no,
                        $field->bulk_no_bulk,
                        $field->corak_remark,
                        $field->warna_remark,
                        $field->jumlah_qty,
                        $field->total_qty,
                        $field->uom,
                    );
                    $data[] = $row;
                }
            } else {
                foreach ($list as $field) {
                    $no++;

                    $row = array(
                        $no,
                        $field->corak_remark,
                        $field->warna_remark,
                        $field->jumlah_qty,
                        $field->total_qty,
                        $field->uom,
                    );
                    $data[] = $row;
                }
            }
            echo json_encode(array("draw" => $_POST['draw'],
                "recordsTotal" => $this->m_deliveryorderdetail->getDataCountAll($condition, ["BULK"]),
                "recordsFiltered" => $this->m_deliveryorderdetail->getDataCountFiltered($condition, ["BULK"]),
                "data" => $data,
            ));
            exit();
        } catch (Exception $ex) {
            echo json_encode(array("draw" => $_POST['draw'],
                "recordsTotal" => 0,
                "recordsFiltered" => 0,
                "data" => [],
            ));
        }
    }

    public function print_sj() {
        try {
            $id = $this->input->post("id");
            $kode_decrypt = decrypt_url($id);
            if (!$kode_decrypt) {
                throw new \Exception();
            }
            $report = $this->input->post("print_mode");
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => '', 'icon' => 'fa fa-check', 'type' => 'success', "data" => base_url('warehouse/deliveryorder/print/' . $report . '/' . $id))));
        } catch (Exception $ex) {
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }

    public function show_form_retur() {
        try {
            $data["do"] = $this->input->post("do");
            $data["doid"] = $this->input->post("doid");
            $data["no_sj"] = $this->input->post("no_sj");
            $data["status"] = $this->input->post("status");
            $datas = $this->load->view("warehouse/v_do_retur", $data, true);
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array("data" => $datas)));
        } catch (Exception $ex) {
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }

    public function check_item() {
        try {
            $barcode = $this->input->post("search");
            $do = $this->input->post("do");

            $condition = ["do.no" => $do, 'dod.barcode_id' => $barcode,"pd.valid"=>"done"];
            $data = $this->m_deliveryorderdetail->getDetail($condition);
            if (empty($data)) {
                throw new Exception("data item tidak ditemukan", 500);
            }
            if ($data->status != 'done') {
                throw new Exception("Item dalam status " . strtoupper($data->status), 500);
            }
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Item ditemukan', 'icon' => 'fa fa-check', 'type' => 'success', 'data' => $data)));
        } catch (Exception $ex) {
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }

    public function retur_item() {
        try {
            $username = $this->session->userdata('username');
            $users = $this->session->userdata('nama');
            $sub_menu = $this->uri->segment(2);

            $user = $this->m_user->get_user_by_username($username);
            if (in_array($user->level, ["Entry Data", ""])) {
                throw new \Exception('Akses tidak diijinkan', 500);
            }

            $dataItem = json_decode($this->input->post("data"));
            $do = $this->input->post("do");
            $doid = $this->input->post("doid");
//            $nosj = $this->input->post("no_sj");
            $this->_module->startTransaction();
            $this->_module->lock_tabel("user WRITE, main_menu_sub WRITE, log_history WRITE,delivery_order_detail WRITE,picklist WRITE,picklist_detail WRITE,"
                    . "delivery_order_detail dod WRITE,stock_move_produk WRITE,stock_move_items WRITE,stock_move WRITE,deliveryorder_stock_move WRITE,"
                    . "stock_quant WRITE,stock_quant sq WRITE,picklist_detail pd WRITE,mst_produk mp WRITE,delivery_order do WRITE,delivery_order_detail a WRITE");
            $barcode = [];
            $pl = "";
            foreach ($dataItem as $key => $value) {
                $values = json_decode($value);
                $pl = $values[0];
                $barcode[] = $values[1];
            }
            $condition = ['dod.do_id' => $doid, 'dod.status' => 'done'];
            $countData = $this->m_deliveryorderdetail->countData($condition, ['dod.barcode_id' => $barcode]);
            if ($countData != count($dataItem)) {
                throw new Exception("Silahkan scan kembali data, ada data berubah pada database " . $countData . ' - ' . count($dataItem), 500);
            }
//            log_message("error","masuk 1 ".json_encode(json_encode($barcode)));
            $nosm = "SM" . $this->_module->get_kode_stock_move();
            $smdata = "('" . $nosm . "','" . date("Y-m-d H:i:s") . "','" . $do . "|1','GJD|IN','CST/Stock','GJD/Stock','done','1','')";
            $this->_module->create_stock_move_batch($smdata);

            $list = $this->m_deliveryorderdetail->getDataAll(['dod.do_id' => $doid, 'dod.status' => 'done', 'pd.valid' => 'done'], ['PD', 'SQ'], ['dod.barcode_id' => $barcode]);
            $rowMoveItem = $this->_module->get_row_order_stock_move_items_by_kode($nosm);
            $smproduk = [];
            $updateIn = [
                'do_id' => [$doid],
                'barcode_id' => []
            ];
            $insertStokMvItem = [];
            $insertStokMvProd = [];
            $updateStokQuant = [];
            foreach ($list as $key => $value) {
//                log_message("error","masuk 2 ".json_encode(json_encode($value)));
                $check = $this->checkLokasi(['stock_quant.quant_id' => $value->quant_id], ['stock_quant.lokasi_fisik' => '', 'stock_quant.lokasi' => 'CST/Stock', 'id_category' => 21]);
                if (!empty($check)) {
                    throw new \Exception($check, 500);
                }
                $updateIn['barcode_id'][] = $value->barcode_id;
                $insertStokMvItem[] = "('" . $nosm . "','" . $value->quant_id . "','" . $value->kode_produk . "','" . addslashes($value->nama_produk) . "','" .
                        $value->barcode_id . "','" . $value->qty . "','" . $value->uom . "','" . $value->qty2 . "','" . $value->uom2 . "','done','" . $rowMoveItem . "','','" . date("Y-m-d H:i:s") . "','" .
                        $value->lokasi_fisik . "','','','" . $value->lebar_jadi . "','" . $value->uom_lebar_jadi . "')";

                $updateStokQuant [] = ["move_date" => date('Y-m-d H:i:s'), "lokasi_fisik" => "XPD", "lokasi" => "GJD/Stock", 'quant_id' => $value->quant_id];
                if (isset($smproduk[$value->kode_produk])) {
                    $smproduk[$value->kode_produk]["qty"] += $value->qty;
                } else {
                    $smproduk[$value->kode_produk] = array(
                        'qty' => $value->qty,
                        'uom' => $value->uom,
                        'nama' => addslashes($value->nama_produk),
                        'order' => count($smproduk) + 1
                    );
                }
                $rowMoveItem++;
            }
            foreach ($smproduk as $key => $value) {
                $insertStokMvProd[] = "('" . $nosm . "','" . $key . "','" . $value['nama'] . "','" . $value['qty'] . "','" . $value['uom'] . "','done','" . $value['order'] . "','')";
            }
            if (count($insertStokMvItem) > 0) {
                $this->_module->simpan_stock_move_items_batch(implode(",", $insertStokMvItem));
                $this->_module->create_stock_move_produk_batch(implode(",", $insertStokMvProd));
                $this->m_Pickliststockquant->updateBatch($updateStokQuant, 'quant_id');
                $this->m_deliveryorderdetail->update(['status' => 'retur', 'tanggal_retur' => date("Y-m-d H:i:s")], ['do_id' => $doid], ['barcode_id' => $barcode]);
                $this->m_PicklistDetail->updateStatusWin(['no_pl' => $pl, 'valid' => 'done'], ['valid' => 'validasi'], ['barcode_id' => $barcode], true);
                $this->m_deliveryorder->insertDoMove(['move_id' => $nosm, 'no_do' => $do]);
            }
            $this->_module->gen_history($sub_menu, $do, 'edit', ($users["nama"] ?? $username) . ' Meretur Item - ' . implode(",", $barcode), $username);

            if (!$this->_module->finishTransaction()) {
                throw new \Exception('Gagal Menyimpan Data', 500);
            }
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil Retur item', 'icon' => 'fa fa-check', 'type' => 'success')));
        } catch (Exception $ex) {
            $this->_module->rollbackTransaction();
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        } finally {
            $this->_module->unlock_tabel();
        }
    }

    public function data_retur() {
        try {
            $doid = $this->input->post("doid");

            $list = $this->m_deliveryorderdetail->getDataAll(['dod.do_id' => $doid, 'dod.status' => 'retur'], ["PD"]);
            $data = [];
            $no = 0;
            foreach ($list as $value) {
                $no++;
                $row = [
                    $no,
                    $value->barcode_id,
                    $value->kode_produk,
                    $value->nama_produk,
                    $value->corak_remark,
                    $value->warna_remark,
                    ($value->qty_jual ?? 0) . ' ' . $value->uom_jual,
                    ($value->qty2_jual ?? 0) . ' ' . $value->uom2_jual,
                    ""
                ];
                $data[] = $row;
            }

            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode($data));
        } catch (Exception $ex) {
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array()));
        }
    }

    public function info_stock_move() {
        $nodo = $this->input->post("nodo");
//        $nosj = $this->input->post("nosj");
        $condition = json_encode(['origin' => $nodo . '|1']);
        $datas = $this->load->view("modal/v_info_stock_move", ['condition' => $condition], true);
        $this->output->set_status_header(200)
                ->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode(['data' => $datas]));
    }

    public function broadcast() {
        try {
            $do = $this->input->post("do");
            $data = $this->m_deliveryorder->getDataDetail(['a.no' => $do], true, "a.*,p.jenis_jual,p.type_bulk_id,pn.nama,msg.nama_sales_group as sales,msg.kode_sales_group as kode_sales,p.nama_user as user_picklist");
            $dataPesan = [
                "{no_sj}" => $data->no_sj,
                "{customer}" => $data->nama,
                "{tgl_time_dokumen}" => $data->tanggal_dokumen,
                "{no_pl}" => $data->no_picklist,
                "{no_do}" => $data->no,
                "{sales}" => $data->sales,
                "{jenis_jual}" => $data->jenis_jual,
                "{bulk}" => ($data->type_bulk_id === "1") ? "BAL" : "LOOSE PACKING"
            ];

            $this->m_deliveryorder->update(['notifikasi' => 1], ['no' => $do]);

            $mention = [];
            //'sales_group' => $data->kode_sales, 
            $user = $this->m_deliveryorder->userBC(['nama' => $data->user, 'username' => 'prianto', 'nama' => $data->user_picklist]);

            foreach ($user as $key => $value) {
                if (is_null($value->telepon_wa) || empty($value->telepon_wa)) {
                    continue;
                }
                $mention[] = $value->telepon_wa;
            }
            $this->wa_message->sendMessageToGroup('new_do', $dataPesan, ['WAREHOUSE 24JAM'])->setMentions($mention)->setFooter('footer_hms')->send();

            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success')));
        } catch (Exception $ex) {
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }

    protected function checkLokasi(array $condition, array $addCondition = ['stock_quant.lokasi_fisik' => 'XPD', 'stock_quant.lokasi' => 'GJD/Stock', 'id_category' => 21]) {
        $status = "";
        $list = $this->m_Pickliststockquant->getDataItemPicklistScan(array_merge($condition, $addCondition), true, []);
        if (empty($list)) {
            $list = $this->m_Pickliststockquant->getDataItemPicklistScanDetail($condition, true);
            if (empty($list)) {
                $status = "Barcode Tidak ditemukan";
            }
            switch (true) {

                case (int) $list->id_category !== 21:
                    $status = "Kategori Produk Tidak Valid (" . $list->nama_category . ")";
                case $list->reserve_move !== "":
                    $status = "Barcode " . $list->lot . " reserve move " . $list->reserve_move;

                case in_array(strtoupper($list->lokasi_fisik), ["XPD"]) :
                    $status = "Lokasi Tidak Valid (" . $list->lokasi_fisik . ")";

                case strtoupper($list->lokasi) !== 'GJD/STOCK':
                    $status = "Lokasi Tidak Valid (" . $list->lokasi . ")";
                default :
                    $status = "Barcode Tidak ditemukan";
            }
        }
        return $status;
    }

    public function antrian_sj() {
        try {
            $sj = $this->input->post("sj");
            $tanggal_dokumen = $this->input->post("tanggal_dokumen");
            $time_dokumen = strtotime($tanggal_dokumen);
            $nosjs = $this->m_deliveryorder->listNoSJ(['no_sj LIKE' => $sj . '/' . date('y', $time_dokumen) . '/' . getRomawi(date('m', $time_dokumen)) . '/%']);
            $strings = "<table class='table'>";
            $strings .= "<thead>";
            $strings .= "<th>No SJ</th>";
            $strings .= "</thead>";
            $strings .= "<tbody>";
            foreach ($nosjs as $key => $value) {
                $strings .= "<tr>";
                $strings .= "<td>{$value->no_sj}</td>";
                $strings .= "</tr>";
            }
            $strings .= "</tbody>";
            $strings .= "</table>";
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success', "data" => $strings)));
        } catch (Exception $ex) {
            $this->output->set_status_header(500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success', "data" => "")));
        }
    }

//    public function add_item() {
//        try {
//            $username = $this->session->userdata('username');
//            $users = $this->session->userdata('nama');
//            $sub_menu = $this->uri->segment(2);
//
//            $type = $this->input->post("type");
//            $barcode = $this->input->post("search");
//            $pl = $this->input->post('picklist');
//            $do = $this->input->post("do");
//            $data = [];
//            if ($type === "BAL") {
//                $data = $this->getItemBarcode(["no" => $pl, "bulk_no_bulk" => $barcode]);
//            } else {
//                $dt = $this->m_PicklistDetail->detailData(['no_pl' => $pl, 'barcode_id' => $barcode, 'valid !=' => 'cancel']);
//                if (!is_null($dt)) {
//                    array_push($data, $dt);
//                }
//            }
//            $this->_module->startTransaction();
//            foreach ($data as $key => $value) {
//                $this->m_deliveryorderdetail->insert(["do_id" => $do, 'barcode_id' => $value->barcode_id, 'status' => '']);
//            }
//            if (!$this->_module->finishTransaction()) {
//                throw new \Exception('Gagal Menyimpan Data', 500);
//            }
//            $this->_module->gen_history($sub_menu, $data['no_bulk'], 'edit', ($users["nama"] ?? $username) . ' Menambahkan bal / Bulk.', $username);
//            $this->output->set_status_header(200)
//                    ->set_content_type('application/json', 'utf-8')
//                    ->set_output(json_encode(array('message' => 'Item berhasil ditambahkan', 'icon' => 'fa fa-check', 'type' => 'success')));
//        } catch (Exception $ex) {
//            $this->_module->finishTransaction();
//            $this->output->set_status_header($ex->getCode() ?? 500)
//                    ->set_content_type('application/json', 'utf-8')
//                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
//        }
//    }
//    public function delete_item_pl() {
//        try {
//            $barcode = $this->input->post("id");
//            $this->_module->startTransaction();
//            $this->m_bulkdetail->delete(['barcode' => $barcode]);
//            $status = $this->m_PicklistDetail->updateStatus(['barcode_id' => $barcode], ['valid' => "cancel"]);
//            if ($status != "") {
//                throw new \Exception($status, 500);
//            }
//            if (!$this->_module->finishTransaction()) {
//                throw new \Exception('Gagal Mengeluarkan Item dari PL', 500);
//            }
//            $this->output->set_status_header(200)
//                    ->set_content_type('application/json', 'utf-8')
//                    ->set_output(json_encode(array('message' => '', 'icon' => 'fa fa-check', 'type' => 'success')));
//        } catch (Exception $ex) {
//            $this->output->set_status_header($ex->getCode() ?? 500)
//                    ->set_content_type('application/json', 'utf-8')
//                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
//        }
//    }
//    public function delivery() {
//        try {
//            $username = $this->session->userdata('username');
//            $users = $this->session->userdata('nama');
//            $sub_menu = $this->uri->segment(2);
//
//            $nodo_ex = $this->input->post("nodo");
//
//            $this->_module->startTransaction();
//
//            $dataDO = $this->m_deliveryorder->getDataDetail(['no' => $nodo_ex]);
//            if (empty($dataDO)) {
//                throw new \Exception("Data Delivery tidak ditemukan", 500);
//            }
//
//            if (!$nodo = $this->token->noUrut('deliveryorder', date('ym'), true)->generate('DO', '%04d')->get()) {
//                throw new \Exception("No Delivery Order tidak terbuat", 500);
//            }
//            $nosjs = $this->m_deliveryorder->checkNoSJ(['no_sj LIKE' => $tipe_no_sj . '/' . date('y', $time_dokumen) . '/' . getRomawi(date('m', $time_dokumen)) . '%']);
//            $nosjs = $this->m_deliveryorder->checkNoSJ(['no_sj LIKE' => $dataDO->tipe_no_sj . '%']);
//            if (!is_null($nosjs)) {
//                $nosj = $nosjs->no_sj;
//                $this->m_deliveryorder->deleteNoSJ(['no_sj' => $nosj]);
//            } else {
//                if (!$nosj = $this->token->noUrut('delivery_' . $dataDO->tipe_no_sj, date('y') . getRomawi(date('m')), true)->generate($dataDO->tipe_no_sj . '/', '/%04d')->get()) {
//                    throw new \Exception("No SJ tidak terbuat", 500);
//                }
//            }
//            $data = [
//                'no' => $nodo,
//                'no_sj' => $nosj,
//                'tanggal_dokumen' => date("Y-m-d H:i:s"),
//                'tanggal_buat' => date("Y-m-d H:i:s"),
//                'note' => $this->input->post("note"),
//                'status' => 'done',
//                'no_picklist' => $dataDO->no_picklist,
//                'rev' => $this->input->post("rev"),
//                'tipe_no_sj' => $dataDO->tipe_no_sj,
//            ];
//            $idd = $this->m_deliveryorder->insert($data);
//            if (is_null($idd)) {
//                throw new Exception("Gagal Membuat Surat Jalan ", 500);
//            }
//
//            $nosm = "SM" . $this->_module->get_kode_stock_move();
//            $smdata = "('" . $nosm . "','" . date("Y-m-d H:i:s") . "','" . $nodo . "|1','GJD|OUT','GJD/Stock','CST/Stock','done','1','')";
//            $this->_module->create_stock_move_batch($smdata);
//            $rowMoveItem = $this->_module->get_row_order_stock_move_items_by_kode($nosm);
//            $smproduk = [];
//            $insertDetail = [];
//            $insertStokMvItem = [];
//            $insertStokMvProd = [];
//            $updateStokQuant = [];
//            $listBarcode = [];
//            $list = $this->m_deliveryorderdetail->getDataAll(['do_id' => $dataDO->id, 'status' => 'cancel'], ['PD', 'SQ']);
//            foreach ($list as $key => $value) {
//                $listBarcode[] = $value->barcode_id;
//
//                $check = $this->checkLokasi(['stock_quant.quant_id' => $value->quant_id]);
//                if (!empty($check)) {
//                    throw new \Exception($check, 500);
//                }
//                $insertDetail[] = ['do_id' => $idd, 'barcode_id' => $value->barcode_id, 'status' => 'done'];
//                $insertStokMvItem[] = "('" . $nosm . "','" . $value->quant_id . "','" . $value->kode_produk . "','" . $value->nama_produk . "','" .
//                        $value->barcode_id . "','" . $value->qty . "','" . $value->uom . "','" . $value->qty2 . "','" . $value->uom2 . "','done','" . $rowMoveItem . "','','" . date("Y-m-d H:i:s") . "','" .
//                        $value->lokasi_fisik . "','" . $value->lebar_greige . "','" . $value->uom_lebar_greige . "','" . $value->lebar_jadi . "','" . $value->uom_lebar_jadi . "')";
//                $updateStokQuant [] = ["move_date" => date('Y-m-d H:i:s'), "lokasi_fisik" => "", "lokasi" => "CST/Stock", 'quant_id' => $value->quant_id];
//                if (isset($smproduk[$value->kode_produk])) {
//                    $smproduk[$value->kode_produk]["qty"] += $value->qty;
//                } else {
//                    $smproduk[$value->kode_produk] = array(
//                        'qty' => $value->qty,
//                        'uom' => $value->uom,
//                        'nama' => $value->nama_produk,
//                        'order' => count($smproduk) + 1
//                    );
//                }
//                $rowMoveItem++;
//            }
//            foreach ($smproduk as $key => $value) {
//                $insertStokMvProd[] = "('" . $nosm . "','" . $key . "','" . $value['nama'] . "','" . $value['qty'] . "','" . $value['uom'] . "','done','" . $value['order'] . "','')";
//            }
//            $this->m_deliveryorder->update(['no_ex' => $nodo], ['no' => $dataDO->no]);
//
//            $this->m_deliveryorderdetail->insertBatch($insertDetail);
//            $this->_module->simpan_stock_move_items_batch(implode(",", $insertStokMvItem));
//            $this->_module->create_stock_move_produk_batch(implode(",", $insertStokMvProd));
//            $this->m_Pickliststockquant->updateBatch($updateStokQuant, 'quant_id');
//            $this->m_Picklist->update(['status' => 'done'], ["no" => $dataDO->no_picklist, 'status' => 'validasi']);
//            $this->m_PicklistDetail->updateStatusWin(['no_pl' => $dataDO->no_picklist, 'valid' => 'validasi'], ['valid' => 'done'], ['barcode_id' => $listBarcode], true);
//            $this->m_deliveryorder->insertDoMove(['move_id' => $nosm, 'no_do' => $nodo]);
//
//            $this->_module->gen_history($sub_menu, $data['no'], 'create', ($users["nama"] ?? $username) . ' Menambahkan Dokumen DO - ' . $data["no"], $username);
//            if (!$this->_module->finishTransaction()) {
//                throw new \Exception('Gagal Menyimpan Data', 500);
//            }
//            $kode_decrypt = encrypt_url($data["no"]);
//            $this->output->set_status_header(200)
//                    ->set_content_type('application/json', 'utf-8')
//                    ->set_output(json_encode(array('message' => 'Surat Jalan berhasil dibuat', 'icon' => 'fa fa-check', 'type' => 'success', 'data' => $kode_decrypt)));
//        } catch (Exception $ex) {
//            $this->_module->rollbackTransaction();
//            $this->output->set_status_header($ex->getCode() ?? 500)
//                    ->set_content_type('application/json', 'utf-8')
//                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
//        }
//    }
}
