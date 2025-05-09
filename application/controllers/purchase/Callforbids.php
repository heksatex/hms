<?php

defined('BASEPATH') OR EXIT('No Direct Script Acces Allowed');
/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of Cfb
 *
 * @author RONI
 */
class Callforbids extends MY_Controller {

    //put your code here
    public function __construct() {
        parent:: __construct();
        $this->is_loggedin();
        $this->load->model('m_cfb');
        $this->load->model('m_global');
        $this->load->model('m_produk');
        $this->load->model("m_konversiuom");
        $this->load->model('_module');
        $this->load->model('m_user');
    }

    public function index() {
        $data['id_dept'] = 'CFB';
        $depth = new $this->m_cfb;
        $username = $this->session->userdata('username');
        $data['user'] = (object) $this->session->userdata('nama'); //$this->m_user->get_user_by_username($username);
        $data["dept"] = $depth->setTables("departemen")->setSelects(["kode", "nama"])->setOrder(["kode" => "asc"])->getData();
        $this->load->view('purchase/v_cfb', $data);
    }

    public function list_data() {
        try {
            $data = array();

            $list = new $this->m_cfb;
            $list->setTables("cfb_items ci");
            $list->setOrders([null, "cfb.kode_cfb", "kode_produk", "nama_produk", "qty", "sales_order", "priority", "warehouse", "create_date", "status"])
                    ->setSearch(["cfb.kode_cfb", "kode_produk", "nama_produk", "qty", "sales_order", "priority", "warehouse", "kode_pp", "reff_notes"])
                    ->setJoins("cfb", "ci.kode_cfb = cfb.kode_cfb")
                    ->setJoins("departemen", "departemen.kode = cfb.warehouse")
                    ->setJoins("mst_status", "mst_status.kode = ci.status", "left")
                    ->setSelects(["ci.*", "departemen.nama as nama_warehouse,warehouse", "cfb.create_date", "sales_order,priority,notes", "nama_status", "cfb.kode_pp"])
                    ->setOrder(['create_date' => 'desc'])
                    ->setWhereRaw("ci.id NOT IN (select cfb_items_id from purchase_order_detail where status != 'cancel') and ci.status not in('cancel','done')");
            $no = $_POST['start'];
            $depth = $this->input->post("depth");
            $kode = $this->input->post("kode");
            $prio = $this->input->post("prio");
            $stat = $this->input->post("status");
            $depth_name = $this->input->post("depth_name");
            if ($depth !== "") {
                $list->setWheres(["cfb.warehouse" => $depth]);
            }
            if ($kode !== "") {
                $list->setWhereRaw("cfb.kode_pp LIKE '%{$kode}%'");
            }
            if ($prio !== "") {
                $list->setWheres(["cfb.priority" => $prio]);
            }
            if ($stat !== "") {
                $list->setWheres(["ci.status" => $stat]);
            }
            foreach ($list->getData() as $field) {
                $no++;
                $kode_encrypt = encrypt_url($field->kode_cfb);
//                $ids = $no;
//                if (strtolower($field->status) != "cancel") {
                $ids = $field->id . "|^" . $field->kode_cfb . "." . $field->kode_pp . "|^" . $field->kode_produk . "|^" .
                        $field->qty . "|^" . $field->uom . "|^" . $field->priority . "|^" . $field->reff_notes . "|^" . $field->warehouse . "|^" . $field->schedule_date . "|^" . $field->status;
//                }
                $data [] = array(
                    $ids,
                    '<a href="' . base_url('purchase/callforbids/edit/' . $kode_encrypt) . '">' . $field->kode_cfb . '</a>',
                    $field->kode_produk,
                    $field->nama_produk,
                    number_format($field->qty,2) . " " . $field->uom,
                    $field->sales_order,
                    $field->priority,
                    $field->nama_warehouse,
                    $field->create_date,
                    $field->nama_status ?? $field->status,
                    $field->reff_notes
                );
            }
            echo json_encode(array("draw" => $_POST['draw'],
                "recordsTotal" => $list->getDataCountAll(),
                "recordsFiltered" => $list->getDataCountFiltered(),
                "data" => $data,
                "depth"=>$depth,
                "depth_name"=>$depth_name,
                "kode"=>$kode,
                "stat"=>$stat
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

    public function edit($id) {
        try {
            $kode_decrypt = decrypt_url($id);
            if (!$kode_decrypt) {
                throw new \Exception();
            }
            $data['id_dept'] = 'CFB';
            $data["id"] = $id;
            $data["datas"] = $this->m_cfb->setWheres(["cfb_items.kode_cfb" => $kode_decrypt])
                            ->setSelects(["cfb.*,departemen.nama as nama_warehouse", "cfb_items.status,cfb_items.id as ids"])
                            ->setJoins("cfb_items", "cfb_items.kode_cfb = cfb.kode_cfb")
                            ->setJoins("departemen", "departemen.kode = cfb.warehouse")->getDetail();
            $this->load->view('purchase/v_cfb_show', $data);
        } catch (Exception $ex) {
            return show_404();
        }
    }

    public function list_data_detail($id) {
        try {
            $kode_decrypt = decrypt_url($id);
            $data = array();
            $no = $_POST['start'];
            $list = $this->m_cfb->setTables("cfb_items")->setSelects(["*"])
                    ->setOrders([null, "kode_produk", "nama_produk", "qty", "uom", null, "status"])
                    ->setSearch(["kode_produk", "nama_produk"])->setOrder(['row_order' => 'asc'])
                    ->setWheres(["kode_cfb" => $kode_decrypt]);
            foreach ($list->getData() as $field) {
                $no++;
                $data[] = [
                    $no,
                    $field->kode_produk,
                    $field->nama_produk,
                    $field->qty,
                    $field->uom,
                    $field->reff_notes,
                    $field->status
                ];
            }
            echo json_encode(array("draw" => $_POST['draw'],
                "recordsTotal" => $list->getDataCountAll(),
                "recordsFiltered" => $list->getDataCountFiltered(),
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

    public function create_rfq() {
        try {
            $datas = $this->input->post("data");
            $data["jenis"] = $this->input->post("jenis");
            $dd = json_decode($datas);
            $model = new $this->m_global;
             $this->_module->lock_tabel("mst_produk WRITE,cfb_items WRITE,mst_produk_harga mph read,nilai_konversi nk read,nilai_konversi read");
            $checkStatus = $model->setTables("cfb_items")->setSelects(["kode_produk,nama_produk"])->setWhereIn("id", $dd->ids)->setWheres(["status <>"=>"confirm"])->getDetail();
            if($checkStatus){
                throw new \Exception("Produk [{$checkStatus->kode_produk}] {$checkStatus->nama_produk} Tidak dalam status CONFRIM", 500);
            }
            $produk = $this->m_cfb->setTables("mst_produk")->setSelects(["mst_produk.kode_produk,nama_produk,uom_beli,mph.harga,nilai,dari,catatan,uom"])
                    ->setJoins("mst_produk_harga mph", "(mph.kode_produk = mst_produk.kode_produk and mph.jenis = 'pembelian')", "left")
                    ->setJoins("nilai_konversi nk", "nk.id = uom_beli", "left");
            $items = [];
            $temid = [];
            foreach ($dd->data as $key => $value) {
                $datas_ = explode("|^", $value);
                if(isset($temid[$datas_[0]])) {
                    continue;
                }
                if (count($datas_) > 1) {
                    $prod = $produk->setWheres(["mst_produk.kode_produk" => $datas_[2]], true)->getDetail();
                    if ($prod) {
                        if (is_null($prod->uom_beli) || ($prod->uom !== $datas_[4])) {
                            $dari = $datas_[4];
                            $nilai = 1;
                            $ke = $datas_[4];
                            $catatan = "1:1";
                            $datakonversi = ["ke" => $ke, "dari" => $dari, "nilai" => $nilai];
                            $getDataKonv = $this->m_konversiuom->wheres($datakonversi)->getDetail();
                            if (!$getDataKonv) {
                                $this->m_konversiuom->save(array_merge($datakonversi, ["catatan" => $catatan]));
                                $getDataKonv = $this->m_konversiuom->wheres($datakonversi)->getDetail();
                                $uom_beli = $getDataKonv->id;
                            } else {
                                $uom_beli = $getDataKonv->id;
                                $dari = $getDataKonv->dari;
                                $nilai = $getDataKonv->nilai;
                                $catatan = $getDataKonv->catatan;
                            }
                        } else {
                            $uom_beli = $prod->uom_beli;
                            $dari = $prod->dari;
                            $nilai = ($prod->nilai ?? 1);
                            $catatan = $prod->catatan;
                        }
//                        $qtyBeli = ceil($datas_[3] / $nilai);
                        $qtyBeli = $datas_[3] / $nilai;
                        array_push($items, [$datas_[0], $datas_[1], $prod->kode_produk, $prod->nama_produk, $datas_[3],
                            $datas_[4], ($uom_beli ?? null), $datas_[5], $prod->harga, $dari, $nilai, $catatan, $qtyBeli, $datas_[6], $datas_[7], $datas_[8]]);
                    }
                }
                $temid[$datas_[0]] ="" ;
            }
            $data["item"] = $items;
            $data["supp"] = $this->input->post("supp") ?? "";
            $data["prio"] = $this->input->post("prio") ?? "";
            $data["tanggal"] = $this->input->post("tgl") ?? "";
            $data["note"] = $this->input->post("note") ?? "";
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(['data' => $this->load->view('purchase/v_cfb_create', $data, true)]));
        } catch (Exception $ex) {
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        } finally {
            $this->_module->unlock_tabel();
        }
    }

    public function update_status() {
        try {
            $sub_menu = $this->uri->segment(2);
//            $username = $this->session->userdata('username');
            $users = $this->session->userdata('nama');
            $ids = $this->input->post("ids");
            $status = $this->input->post("status");
            $before_status = $this->input->post("before_status");

            if (is_string($ids)) {
                $ids = array($ids);
            }

            $updt = new $this->m_cfb;
            $listCfb = clone $updt;
            $log = new $this->m_global;
            $this->_module->startTransaction();
            $this->_module->lock_tabel("mst_produk WRITE,cfb_items ci write,cfb write,procurement_purchase_items write,log_history WRITE,main_menu_sub WRITE");
            $updt->setTables('cfb_items ci')->setWhereRaw("id in (" . implode(",", $ids) . ")")->setWheres(['status' => $before_status])->update(['status' => $status]);
            $listLog = [];
            if ($before_status === "confirm") {
                $status = "generated";
            }
            $kodes = $this->_module->get_kode_sub_menu($sub_menu)->row_array();
            foreach ($listCfb->setJoins('cfb_items ci', "ci.kode_cfb = cfb.kode_cfb")->setOrder(["ci.id" => "asc"])->setSelects(['kode_pp', "kode_produk", "ci.kode_cfb"])->setWhereRaw("ci.id in (" . implode(",", $ids) . ")")->getData() as $key => $value) {
                $updatePP = new $this->m_cfb;
                $updatePP->setTables("procurement_purchase_items")->setWheres(["kode_pp" => $value->kode_pp, "kode_produk" => $value->kode_produk])->update(["status" => $status]);
                $listLog[] = ["datelog" => date("Y-m-d H:i:s"), "kode" => $value->kode_cfb, "main_menu_sub_kode" => ($kodes["kode"] ?? ""),
                    "jenis_log" => "edit", "note" => "Merubah Ke status {$status}", "nama_user" => $users["nama"], "ip_address" => ""];
            }
            if (!$this->_module->finishTransaction()) {
                throw new \Exception('Gagal update status', 500);
            }
            $log->setTables("log_history")->saveBatch($listLog);
//             $this->_module->gen_history($sub_menu, $nopo, 'create', logArrayToString('; ', $kode_cfb), $username);
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success')));
        } catch (Exception $ex) {
            $this->_module->rollbackTransaction();
            $this->output->set_status_header(500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        } finally {
            $this->_module->unlock_tabel();
        }
    }
}
