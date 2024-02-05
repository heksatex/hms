<?php

defined('BASEPATH') or exit('No Direct Script Acces Allowed');

/**
 * 
 */
class _module extends CI_Model {

    public function get_nama_user($username) {
        return $this->db->query("SELECT nama FROM user WHERE username = '" . $username . "' ");
    }

    public function get_kode_sub_menu($sub_menu) {
        return $this->db->query("SELECT kode FROM main_menu_sub WHERE inisial_class = '" . $sub_menu . "'");
    }

    public function get_kode_sub_menu_deptid($sub_menu, $deptid) {
        return $this->db->query("SELECT kode FROM main_menu_sub WHERE inisial_class = '" . $sub_menu . "' AND dept_id = '" . $deptid . "'");
    }

    public function get_kode_sub_menu_deptid_user($sub_menu, $deptid, $username) {
        return $this->db->query("SELECT mms.kode 
								FROM main_menu_sub mms
								INNER JOIN user_priv up ON mms.kode = up.main_menu_sub_kode
								WHERE  up.username ='" . $username . "' AND mms.inisial_class = '" . $sub_menu . "' AND mms.dept_id = '" . $deptid . "'");
    }

    public function get_prod($id) {
        return $this->db->query("SELECT mp.kode_produk, mp.nama_produk, mp.uom, mp.uom_2, cat.nama_category, mp.create_date, sat.nama_status
								FROM mst_produk mp
								LEFT JOIN mst_category cat ON mp.id_category = cat.id 
								LEFT JOIN mst_status sat ON mp.status_produk = sat.kode
								WHERE mp.kode_produk = '" . $id . "'");
    }

    public function sub_menu_default($kode_sub, $username) {
        return $this->db->query("SELECT mms.link_menu FROM user_priv up
							INNER JOIN main_menu_sub mms ON up.main_menu_sub_kode=mms.kode
							WHERE username='" . $username . "' AND main_menu_kode='" . $kode_sub . "'
							ORDER by mms.row_order LIMIT 1");
    }

    public function cek_priv_menu_by_user($username, $mm_sub) {
        return $this->db->query("SELECT username 
								FROM user_priv
								WHERE username = '$username' AND  main_menu_sub_kode = '$mm_sub'");
    }

    public function lock_tabel($table) {
        $this->db->query("LOCK TABLES $table ");
    }

    public function unlock_tabel() {
        $this->db->query("UNLOCK TABLES");
    }

    public function gen_history($sub_menu, $kode_co, $jenis_log, $note_log, $username) {
        $tgl = date('y-m-d H:i:s');
        $nama_user = $this->_module->get_nama_user($username)->row_array();
        $kode = $this->_module->get_kode_sub_menu($sub_menu)->row_array();
        $ip         = addslashes($this->input->ip_address());
        $query = $this->db->query("INSERT log_history (datelog, main_menu_sub_kode, kode, jenis_log, note, nama_user,ip_address) 
								   values ('$tgl','$kode[kode]','$kode_co','$jenis_log','$note_log','$nama_user[nama]','$ip')");
    }

    public function gen_history_deptid($sub_menu, $kode_co, $jenis_log, $note_log, $username, $deptid) {
        $tgl = date('y-m-d H:i:s');
        $nama_user = $this->_module->get_nama_user($username)->row_array();
        $kode = $this->_module->get_kode_sub_menu_deptid($sub_menu, $deptid)->row_array();
        $ip         = addslashes($this->input->ip_address());
        $query = $this->db->query("INSERT log_history (datelog, main_menu_sub_kode, kode, jenis_log, note, nama_user,ip_address) 
								   values ('$tgl','$kode[kode]','$kode_co','$jenis_log','$note_log','$nama_user[nama]','$ip')");
    }

    public function gen_history_ip_deptid($sub_menu, $username, $data_history, $deptid){

        $nama_user = $this->_module->get_nama_user($username)->row_array();
        $kode       = $this->_module->get_kode_sub_menu_deptid($sub_menu, $deptid)->row_array();
        $ip         = $this->input->ip_address();

        $add_data_history = array('nama_user'=> $nama_user['nama'],'main_menu_sub_kode' => $kode['kode'], 'ip_address'=> $ip);
        $data_history_all = array_merge($data_history,$add_data_history);

        $this->db->insert('log_history',$data_history_all);
        return is_array($this->db->error());
    }

    public function gen_history_ip($sub_menu, $username, $data_history){

        $nama_user  = $this->_module->get_nama_user($username)->row_array();
        $kode       = $this->_module->get_kode_sub_menu($sub_menu)->row_array();
        $ip         = $this->input->ip_address();

        $add_data_history = array('nama_user'=> $nama_user['nama'],'main_menu_sub_kode' => $kode['kode'], 'ip_address'=> $ip);
        $data_history_all = array_merge($data_history,$add_data_history);

        $this->db->insert('log_history',$data_history_all);
        return is_array($this->db->error());
    }

    public function get_kode_stock_move() {
        $last_no = $this->db->query("SELECT mid(move_id,3,(length(move_id))-2) as 'nom' 
						 from stock_move where left(move_id,2)='SM'
						 order by cast(mid(move_id,3,(length(move_id))-2) as unsigned) desc LIMIT 1  ");
        $result = $last_no->row();
        if (empty($result->nom)) {
            $no = 1;
        } else {
            $no = (int) $result->nom + 1;
        }
        return $no;
    }

    public function get_kode_pengiriman($deptid) {
        $kode = $deptid . "/OUT/" . date("y") . date("m");
        $result = $this->db->query("SELECT kode FROM pengiriman_barang WHERE month(tanggal)='" . date("m") . "' AND year(tanggal)='" . date("Y") . "' AND kode LIKE'%" . $deptid . "%'ORDER BY RIGHT(kode,5) DESC LIMIT 1");
        if ($result->num_rows() > 0) {
            $row = $result->row();
            $dgt = substr($row->kode, -5) + 1;
        } else {
            $dgt = "1";
        }
        //$dgt=substr("00000" . $dgt,-5);            
        $kode_out = $kode . $dgt;
        return $dgt;
    }

    public function get_kode_penerimaan($deptid) {
        $kode = $deptid . "/IN/" . date("y") . date("m");
        $result = $this->db->query("SELECT kode FROM penerimaan_barang WHERE month(tanggal)='" . date("m") . "' AND year(tanggal)='" . date("Y") . "'  AND kode LIKE'%" . $deptid . "%' ORDER BY RIGHT(kode,5) DESC LIMIT 1");
        if ($result->num_rows() > 0) {
            $row = $result->row();
            $dgt = substr($row->kode, -5) + 1;
        } else {
            $dgt = "1";
        }
        //$dgt=substr("00000" . $dgt,-5);            
        //$kode_in=$kode . $dgt;
        return $dgt;
    }

    public function get_kode_mo() {
        $kode = "MO" . date("y") . date("m");
        $result = $this->db->query("SELECT kode FROM mrp_production WHERE month(tanggal)='" . date("m") . "' AND year(tanggal)='" . date("Y") . "' AND kode LIKE '%MO%' ORDER BY RIGHT(kode,5) DESC LIMIT 1");
        if ($result->num_rows() > 0) {
            $row = $result->row();
            $dgt = substr($row->kode, -5) + 1;
        } else {
            $dgt = "1";
        }
        //$dgt=substr("00000" . $dgt,-5);            
        //$mo=$kode . $dgt;
        return $dgt;
    }

    public function get_kode_adj() {
        $result = $this->db->query("SELECT kode_adjustment FROM adjustment WHERE month(create_date)='" . date("m") . "' AND year(create_date)='" . date("Y") . "' ORDER BY RIGHT(kode_adjustment,4) DESC LIMIT 1");
        if ($result->num_rows() > 0) {
            $row = $result->row();
            $dgt = substr($row->kode_adjustment, -4) + 1;
        } else {
            $dgt = "1";
        }
        return $dgt;
    }

    public function get_kode_product() {
        $last_no = $this->db->query("SELECT mid(kode_produk,3,(length(kode_produk))-2) as 'nom' 
						 from mst_produk where left(kode_produk,2)='MF'
						 order by cast(mid(kode_produk,3,(length(kode_produk))-2) as unsigned) desc LIMIT 1  ");
        $result = $last_no->row();
        if (empty($result->nom)) {
            $no = 1;
        } else {
            $no = (int) $result->nom + 1;
        }
        //$kode = 'MF'.$no;
        return $no;
    }

    public function get_kode_bom() {
        $last_no = $this->db->query("SELECT mid(kode_bom,3,(length(kode_bom))-2) as 'nom' 
						 from bom where left(kode_bom,2)='BM'
						 order by cast(mid(kode_bom,3,(length(kode_bom))-2) as unsigned) desc LIMIT 1  ");
        $result = $last_no->row();
        if (empty($result->nom)) {
            $no = 1;
        } else {
            $no = (int) $result->nom + 1;
        }
        //$kode = 'BM'.$no;
        return $no;
    }

    public function create_stock_move_batch($sql) {
        return $this->db->query("INSERT INTO stock_move (move_id,create_date,origin,method,lokasi_dari,lokasi_tujuan,status,row_order,source_move) values $sql ");
    }

    public function create_stock_move_batch_2($data_sm) {
        $this->db->insert_batch('stock_move', $data_sm);
        return is_array($this->db->error());
    }

    public function create_stock_move_produk_batch($sql) {
        return $this->db->query("INSERT INTO stock_move_produk (move_id,kode_produk,nama_produk,qty,uom,status,row_order,origin_prod) 
								values $sql ");
    }

    public function create_stock_move_produk_batch_2($data_smp) {
        $this->db->insert_batch('stock_move_produk', $data_smp);
        return is_array($this->db->error());
        // try {
        //     $this->db->insert_batch('stock_move_produk', $data_smp);
        //     $db_error = $this->db->error();
        //     if ($db_error['code'] > 0) {
        //         throw new Exception($db_error['message']);
        //     }
        //     return "";
        // }catch (Exception $ex) {
        //     return $ex->getMessage();
        // }
    }

    public function simpan_penerimaan_batch($sql) {
        return $this->db->query("INSERT INTO penerimaan_barang (kode,tanggal,tanggal_transaksi,tanggal_jt,reff_note,status,dept_id,origin,move_id,reff_picking,lokasi_dari,lokasi_tujuan)  values $sql ");
    }

    public function simpan_penerimaan_items_batch($sql) {
        return $this->db->query("INSERT INTO penerimaan_barang_items  (kode,kode_produk,nama_produk,qty,uom,status_barang,row_order) 
								values $sql ");
    }

    public function simpan_penerimaan_items_batch_origin_prod($sql) {
        return $this->db->query("INSERT INTO penerimaan_barang_items  (kode,kode_produk,nama_produk,qty,uom,status_barang,row_order,origin_prod) 
								values $sql ");
    }

    public function simpan_pengiriman_batch($sql) {
        return $this->db->query("INSERT INTO pengiriman_barang (kode,tanggal,tanggal_transaksi,tanggal_jt,reff_note,status,dept_id,origin,move_id,lokasi_dari,lokasi_tujuan)  VALUES $sql");
    }

    public function simpan_pengiriman_reff_batch($sql) {
        return $this->db->query("INSERT INTO pengiriman_barang (kode,tanggal,tanggal_transaksi,tanggal_jt,reff_note,status,dept_id,origin,move_id,reff_picking,lokasi_dari,lokasi_tujuan)  VALUES $sql");
    }

    public function simpan_pengiriman_add_manual($sql) {
        return $this->db->query("INSERT INTO pengiriman_barang (kode,tanggal,tanggal_transaksi,tanggal_jt,reff_note,status,dept_id,origin,move_id,reff_picking,lokasi_dari,lokasi_tujuan,type_created)  VALUES $sql");
    }

    public function simpan_pengiriman_items_batch($sql) {
        return $this->db->query("INSERT INTO pengiriman_barang_items  (kode,kode_produk,nama_produk,qty,uom,status_barang,row_order,origin_prod) 
								values $sql ");
    }

    public function update_reff_batch($sql) {
        return $this->db->query(" $sql ");
    }

    public function get_list_departement() {
        return $this->db->query("SELECT kode,nama FROM departemen WHERE show_dept = 'true'  ORDER BY nama  ")->result();
    }

    public function get_route_product($route) {
        return $this->db->query("SELECT *
								FROM mrp_route mr								
								WHERE mr.nama_route = '$route' ORDER BY row_order ")->result();
    }

    public function get_nama_dept_by_kode($kode) {
        return $this->db->query("SELECT * FROM departemen d WHERE kode = '$kode'");
    }

    public function get_kode_dept_by_nama($nama) {
        return $this->db->query("SELECT kode FROM departemen WHERE nama = '$nama'");
    }

    public function cek_nama_product($produk) {
        return $this->db->query("SELECT kode_produk, nama_produk,uom, status_produk FROM mst_produk where nama_produk = '$produk'");
    }

    public function cek_produk_by_kode_produk($kode_produk) {
        return $this->db->query("SELECT kode_produk, nama_produk, uom FROM mst_produk where kode_produk = '$kode_produk' ");
    }

    public function cek_bom($kode_produk) {//production_order, procurement_order, procurement_purchase
        return $this->db->query("SELECT kode_produk,kode_bom,qty,status_bom FROM bom WHERE kode_produk  = '$kode_produk' ORDER BY tanggal desc");
    }

    public function cek_required_bom_by_kode_produk($kode_produk) {
        return $this->db->query("SELECT bom FROM mst_produk WHERE kode_produk = '$kode_produk' ");
    }

    public function cek_bom_by_kode_bom($kode_bom) {//production_order
        return $this->db->query("SELECT kode_produk,kode_bom,qty,nama_bom,status_bom FROM bom WHERE kode_bom  = '$kode_bom'");
    }

    public function simpan_mrp_production_batch($sql) {
        return $this->db->query("INSERT INTO mrp_production (kode,tanggal,origin,kode_produk,nama_produk,qty,uom,tanggal_jt,reff_note,kode_bom,start_time,finish_time,source_location,destination_location,dept_id,status,kode_warna,responsible,lebar_greige,uom_lebar_greige,lebar_jadi,uom_lebar_jadi) values $sql ");
    }

    public function simpan_mrp_production_rm_target_batch($sql) {
        return $this->db->query("INSERT INTO mrp_production_rm_target (kode,move_id,kode_produk,nama_produk,qty,uom,row_order,origin_prod,status,reff_note) values $sql");
    }

    public function simpan_mrp_production_fg_target_batch($sql) {
        return $this->db->query("INSERT INTO mrp_production_fg_target (kode,move_id,kode_produk,nama_produk,qty,uom,row_order,status) values $sql");
    }

    public function get_bom_items_by_kode($kode_bom, $qty_bom, $qty_pd) {
        return $this->db->query("SELECT bi.kode_produk,bi.nama_produk,(bi.qty/'$qty_bom')*$qty_pd as qty_bom_items, bi.uom, bi.note
								FROM bom_items bi 
								INNER JOIN mst_produk mp ON bi.kode_produk = mp.kode_produk
								WHERE mp.type = 'stockable' AND bi.kode_bom = '$kode_bom' ORDER BY row_order");
    }

    public function get_bom_items_all_by_kode($kode_bom, $qty_bom, $qty_pd) {
        return $this->db->query("SELECT kode_produk,nama_produk,(qty/'$qty_bom')*$qty_pd as qty_bom_items, uom, note
								FROM bom_items
								WHERE kode_bom = '$kode_bom' ORDER BY row_order");
    }

    public function get_total_leadtime($route) {
        $total = 0;
        $qry = $this->db->query("SELECT distinct(dept_id_dari), departemen.manf_leadtime as leadtime from mrp_route 
						inner join departemen on mrp_route.dept_id_dari=departemen.kode
						where nama_route='$route'  order by row_order")->result();

        foreach ($qry as $val) {
            $leadtime = $val->leadtime;
            $total = $total + $leadtime;
        }

        return $total;
    }

    public function get_leadtime_by_dept($dept_id) {
        return $this->db->query("SELECT manf_leadtime FROM departemen WHERE kode = '$dept_id'");
    }

    public function get_jenis_route_product($kode_produk) {
        return $this->db->query("SELECT route_produksi FROM mst_produk WHERE kode_produk = '$kode_produk'");
    }

    public function get_kode_in_by_origin($lokasi_tujuan, $origin) {
        return $this->db->query("SELECT kode, origin FROM penerimaan_barang where origin = '$origin' and lokasi_tujuan = '$lokasi_tujuan' ");
    }

    public function get_output_location_by_kode($kode) {
        return $this->db->query("SELECT output_location FROM departemen d WHERE kode = '$kode'");
    }

    public function get_row_order_stock_move_items_by_kode($move_id) {
        $last_no = $this->db->query("SELECT max(row_order) as nom FROM stock_move_items where move_id = '$move_id'");

        $result = $last_no->row();
        if (empty($result->nom)) {
            $no = 1;
        } else {
            $no = (int) $result->nom + 1;
        }
        return $no;
    }

    public function get_row_order_adjustment_items_by_kode($kode_adjustment) {
        $last_no = $this->db->query("SELECT max(row_order) as nom FROM adjustment_items where kode_adjustment = '$kode_adjustment'");

        $result = $last_no->row();
        if (empty($result->nom)) {
            $no = 1;
        } else {
            $no = (int) $result->nom + 1;
        }
        return $no;
    }

    public function get_qty_stock_move_items_by_kode($move_id, $kode_produk) {
        return $this->db->query("SELECT sum(qty) as sum_qty FROM stock_move_items  	WHERE  move_id = '$move_id' And kode_produk = '$kode_produk' ");
    }

    public function get_qty_stock_move_items_by_kode_origin($move_id, $kode_produk, $origin_prod) {
        return $this->db->query("SELECT sum(qty) as sum_qty FROM stock_move_items  	WHERE  move_id = '$move_id' And kode_produk = '$kode_produk' AND origin_prod = '$origin_prod'");
    }

    public function get_qty_stock_move_items_mo_by_kode($move_id, $origin_prod, $status) {
        if (!empty($status)) {
            return $this->db->query("SELECT sum(qty) as sum_qty FROM stock_move_items  	WHERE  move_id = '$move_id' And origin_prod = '$origin_prod' AND status = '$status' ");
        } else {
            return $this->db->query("SELECT sum(qty) as sum_qty FROM stock_move_items  	WHERE  move_id = '$move_id' And origin_prod = '$origin_prod' ");
        }
    }

    public function get_last_quant_id() {
        $last_no = $this->db->query("SELECT max(quant_id) as nom FROM stock_quant");

        $result = $last_no->row();
        if (empty($result->nom)) {
            $no = 1;
        } else {
            $no = (int) $result->nom + 1;
        }
        return $no;
    }

    public function simpan_stock_quant_batch($sql) {
        return $this->db->query("INSERT INTO stock_quant (quant_id,create_date,kode_produk,nama_produk,lot,nama_grade,qty,uom,qty2,uom2,lokasi,reff_note,reserve_move,reserve_origin,move_date,lebar_greige,uom_lebar_greige,lebar_jadi,uom_lebar_jadi,sales_order,sales_group) VALUES $sql ");
    }

    public function simpan_stock_quant_batch_2($data_stockquant) {
        $this->db->insert_batch('stock_quant', $data_stockquant);
        return is_array($this->db->error());
    }

    public function simpan_stock_move_items_batch($sql) {
        return $this->db->query("INSERT INTO stock_move_items (move_id,quant_id,kode_produk,nama_produk,lot,qty,uom,qty2,uom2,status,row_order,origin_prod,tanggal_transaksi,lokasi_fisik,lebar_greige,uom_lebar_greige,lebar_jadi,uom_lebar_jadi) values $sql ");
    }

    public function simpan_stock_move_items_batch_2($data_smi) {
        $this->db->insert_batch('stock_move_items', $data_smi);
        return is_array($this->db->error());
    }

    public function update_status_stock_move_items($move_id, $kode_produk, $status) {
        return $this->db->query("UPDATE stock_move_items SET status = '$status' WHERE move_id = '$move_id' AND kode_produk = '$kode_produk'");
    }

    public function delete_details_items($move_id, $quant_id, $row_order) {
        $this->db->query("DELETE FROM stock_move_items WHERE move_id = '$move_id' AND quant_id = '$quant_id' AND row_order = '$row_order' ");
        $this->db->query("UPDATE stock_quant set reserve_move = '' where quant_id  = '$quant_id'");
        return true;
    }

    public function cek_departement_by_kode($dept_id) {
        return $this->db->query("SELECT type_dept FROM departemen WHERE kode = '$dept_id'");
    }

    public function get_cek_stok_quant_by_prod($kode_produk, $lokasi_dari, $reserve_origin, $dept_id) {

        //cek type departement gudang atau manufaktur
        $cek_dept = $this->cek_departement_by_kode($dept_id)->row_array();

        if ($cek_dept['type_dept'] == 'manufaktur') {
            $origin = "AND reserve_origin = '" . $reserve_origin . "' ";
        }

        if ($cek_dept['type_dept'] == 'gudang') {
            $origin = '';
        }

        return $this->db->query("SELECT * FROM stock_quant where kode_produk = '$kode_produk'  AND reserve_move ='' AND qty != '0' AND lokasi = '$lokasi_dari' $origin ORDER BY create_date asc ");
    }

    public function cek_produk_di_stock_quant($quant_id, $lokasi) {
        return $this->db->query("SELECT quant_id, reserve_move FROM stock_quant WHERE quant_id = '$quant_id' AND lokasi = '$lokasi' AND reserve_move = ''");
    }

    public function get_cek_stok_quant_mo_by_prod($kode_produk, $lokasi_dari, $reserve_origin) {
        return $this->db->query("SELECT * FROM stock_quant where kode_produk = '$kode_produk'  AND reserve_move ='' AND qty != '0' AND lokasi = '$lokasi_dari' AND reserve_origin LIKE '%$reserve_origin%' ORDER BY create_date asc ");
    }

    public function simpan_log_history_batch($sql) {
        return $this->db->query("INSERT INTO log_history (datelog,main_menu_sub_kode,kode,jenis_log,note,nama_user) values $sql ");
    }

    public function simpan_log_history_batch_2($sql) {
        $this->db->insert_batch('log_history', $sql);
        return is_array($this->db->error());
    }
    
    public function get_location_by_move_id($move_id) {
        return $this->db->query("SELECT lokasi_dari, lokasi_tujuan From stock_move where move_id = '$move_id'");
    }

    public function update_perbatch($sql) {
        return $this->db->query(" $sql ");
    }

    public function update_status_stock_move($move_id, $status) {
        return $this->db->query("UPDATE stock_move SET status = '$status' WHERE move_id = '$move_id'");
    }

    public function update_status_stock_move_produk($move_id, $kode_produk, $status) {
        return $this->db->query("UPDATE stock_move_produk SET status = '$status' WHERE move_id = '$move_id' AND kode_produk = '$kode_produk'");
    }

    public function update_status_stock_move_produk_origin_prod($move_id, $kode_produk, $status, $origin_prod) {
        return $this->db->query("UPDATE stock_move_produk SET status = '$status' WHERE move_id = '$move_id' AND kode_produk = '$kode_produk' AND origin_prod = '$origin_prod'");
    }

    public function update_status_stock_move_produk_full($move_id, $status) {
        return $this->db->query("UPDATE stock_move_produk SET status = '$status' WHERE move_id = '$move_id'");
    }

    public function get_stock_move_items_by_move_id($move_id, $status = null) {
        if($status){
            $this->db->where('status',$status);
        }
        $this->db->where('move_id',$move_id);
        $result = $this->db->get('stock_move_items');
        return $result->result();
        // return $this->db->query("SELECT * FROM stock_move_items WHERE move_id = '$move_id' order by row_order ")->result();
    }

    public function get_stock_move_tujuan($move_id, $origin, $status1, $status2) {
        return $this->db->query("SELECT * FROM stock_move WHERE source_move LIKE '%$move_id%' AND origin = '$origin' AND status NOT IN ('$status1','$status2')");
    }

    public function get_stock_move_tujuan_mo($move_id, $origin, $status1, $status2) {
        return $this->db->query("SELECT * FROM stock_move WHERE source_move LIKE '%$move_id%' AND origin = '$origin' AND status NOT IN ('$status1','$status2')");
    }

    public function get_stock_move_by_move_id($move_id) {
        return $this->db->query("SELECT * FROM stock_move WHERE move_id = '$move_id' ");
    }

    public function get_move_id_by_source_move($move_id, $status1, $status2) {
        return $this->db->query("SELECT * FROM stock_move WHERE move_id  = '$move_id' AND status NOT IN ('$status1', '$status2')");
    }

    public function get_kode_penerimaan_barang_by_move_id($move_id) {
        return $this->db->query("SELECT kode FROM penerimaan_barang WHERE move_id = '$move_id'");
    }

    public function get_kode_pengiriman_barang_by_move_id($move_id) {
        return $this->db->query("SELECT kode FROM pengiriman_barang WHERE move_id = '$move_id'");
    }

    public function get_reff_picking_penerimaan_barang_by_kode($kode) {
        return $this->db->query("SELECT reff_picking FROM penerimaan_barang where kode = '$kode'");
    }

    public function cek_reff_picking_penerimaan_barang_by_kode($kode, $kode_out) {
        return $this->db->query("SELECT reff_picking FROM penerimaan_barang where kode ='$kode' AND reff_picking LIKE '%$kode_out%'");
    }

    public function get_kode_pengiriman_by_move_id($move_id) {
        return $this->db->query("SELECT kode FROM pengiriman_barang WHERE move_id = '$move_id'");
    }

    public function get_kode_penerimaan_by_move_id($move_id) {
        return $this->db->query("SELECT kode FROM penerimaan_barang WHERE move_id = '$move_id'");
    }

    public function get_list_grade() {
        return $this->db->query("SELECT * FROM mst_grade order by id ")->result();
    }

    public function get_list_mst_filter($id_dept) {
        return $this->db->query("SELECT kode_element, nama_element, type_condition FROM mst_filter WHERE id_dept LIKE '%$id_dept%' ")->result();
    }

    public function get_type_conditon($id_dept, $kode_element) {
        $type = $this->db->query("SELECT type_condition FROm mst_filter where id_dept = '$id_dept' AND kode_element = '$kode_element'");
        $result = $type->row();
        return $result->type_condition;
    }

    public function get_first_type_conditon($id_dept) {
        $type = $this->db->query("SELECT type_condition FROm mst_filter where id_dept = '$id_dept'");
        $result = $type->row();
        return $result->type_condition;
    }

    public function get_nama_element_by_kode($kode_element, $id_dept) {
        $nama = $this->db->query("SELECT nama_element FROM mst_filter where kode_element = '$kode_element' AND id_dept = '$id_dept' ");
        $result = $nama->row();
        return $result->nama_element;
    }

    public function get_list_uom() {
        return $this->db->query("SELECT nama, short FROM uom ORDER BY id ")->result();
    }

    public function get_last_user_filter_id() {
        $last_no = $this->db->query("SELECT max(id) as nom FROM user_filter");

        $result = $last_no->row();
        if (empty($result->nom)) {
            $no = 1;
        } else {
            $no = (int) $result->nom + 1;
        }
        return $no;
    }

    public function save_user_filter($id, $username, $dept_id, $inisial_class, $nama_filter, $use_default) {
        $this->db->query("INSERT INTO user_filter (id,username,dept_id,inisial_class,nama_filter,use_default) values ('$id','$username','$dept_id','$inisial_class','$nama_filter','$use_default')");
    }

    public function save_user_filter_isi($filter_id, $nama_field, $operator, $value, $condition) {
        $this->db->query("INSERT INTO user_filter_isi (filter_id,name_field,operator,value_filter,condition_filter) values ('$filter_id','$nama_field','$operator','$value','$condition') ");
    }

    public function save_user_filter_grouping($filter_id, $nama_field, $index) {
        $this->db->query("INSERT INTO user_filter_grouping (filter_id,name_field,data_index) values ('$filter_id','$nama_field','$index') ");
    }

    public function save_user_filter_order($filter_id, $nama_field, $index) {
        $this->db->query("INSERT INTO user_filter_order_by (filter_id,name_field,sort) values ('$filter_id','$nama_field','$index') ");
    }

    public function delete_user_filter($id) {
        $this->db->query("DELETE FROM user_filter WHERE id = '$id'");
        $this->db->query("DELETE FROM user_filter_isi WHERE filter_id = '$id'");
        $this->db->query("DELETE FROM user_filter_grouping WHERE filter_id = '$id'");
    }

    public function check_default_user_filter($username, $id_dept, $inisial_class, $use_default) {
        return $this->db->query("SELECT use_default, nama_filter FROM user_filter WHERE username = '$username' AND dept_id = '$id_dept' AND inisial_class = '$inisial_class' AND use_default = '$use_default'");
    }

    public function check_nama_filter_user($nama_filter, $username, $id_dept, $inisial_class) {
        return $this->db->query("SELECT use_default, nama_filter FROM user_filter WHERE username = '$username' AND dept_id = '$id_dept' AND inisial_class = '$inisial_class' AND nama_filter = '$nama_filter' ");
    }

    public function get_list_user_filter($id_dept, $username) {
        return $this->db->query("SELECT * FROM user_filter where dept_id = '$id_dept' AND username = '$username' ")->result_array();
    }

    public function get_user_filter_default($id_dept, $username) {
        return $this->db->query("SELECT * FROM user_filter where dept_id = '$id_dept' AND username = '$username' AND use_default = 'true' ")->row_array();
    }

    public function get_user_filter_isi_by_id($id) {
        return $this->db->query("SELECT * FROM user_filter_isi WHERE filter_id = '$id'");
    }

    public function get_user_filter_order_by_id($id) {
        return $this->db->query("SELECT * FROM user_filter_order_by WHERE filter_id = '$id'");
    }

    public function get_user_filter_grouping_by_id($id) {
        return $this->db->query("SELECT * FROM user_filter_grouping WHERE filter_id = '$id'");
    }

    public function get_list_stock_move_origin($origin) {
        return $this->db->query("SELECT * FROM stock_move where origin = '$origin' order by LENGTH(move_id),move_id, row_order ");
    }

    public function get_uom_by_kode_produk($kode_produk) {
        return $this->db->query("SELECT uom,uom_2 FROM mst_produk where kode_produk = '$kode_produk' ");
    }

    public function get_detail_items_penerimaan($origin) {
        return $this->db->query("SELECT a.kode, a.tanggal, a.origin, a.status, a.reff_note,a.lokasi_tujuan, b.nama as departemen,
								(SELECT sum(pi.qty)  FROM penerimaan_barang_items pi WHERE pi.kode = a.kode ) as qty_target,
								(SELECT sum(smi.qty) FROM stock_move_items smi WHERE smi.move_id = a.move_id ) as qty_tersedia
								 FROM penerimaan_barang a
								 INNER JOIN departemen b ON a.dept_id = b.kode
								 where a.origin = '$origin'  ORDER BY a.tanggal, a.kode desc ")->result();
    }

    public function get_detail_items_pengiriman($origin) {
        return $this->db->query("SELECT a.kode, a.tanggal, a.origin, a.status, a.reff_note, a.lokasi_tujuan, b.nama as departemen,
								(SELECT sum(pi.qty)  FROM pengiriman_barang_items pi WHERE pi.kode = a.kode ) as qty_target,
								(SELECT sum(smi.qty) FROM stock_move_items smi WHERE smi.move_id = a.move_id ) as qty_tersedia
								 FROM pengiriman_barang a
								 INNER JOIN departemen b ON a.dept_id = b.kode
								 where a.origin = '$origin'  ORDER BY a.tanggal, a.kode desc")->result();
    }

    public function get_detail_items_mo($origin) {
        return $this->db->query("SELECT mrp.kode, mrp.tanggal,mrp.dept_id,mrp.status,mrp.reff_note,mrp.origin, 
								 d.nama as departemen, mrp.qty as qty_target, 
								 (SELECT sum(qty) FROM stock_move_items WHERE move_id = (SELECT move_id FROM mrp_production_fg_target fg WHERE fg.kode = mrp.kode) AND kode_produk = mrp.kode_produk) as qty_tersedia
								 FROM mrp_production mrp
								 INNER JOIN departemen d ON mrp.dept_id = d.kode 
								 where mrp.origin = '$origin'  ORDER BY mrp.tanggal, mrp.kode desc")->result();
    }

    public function get_list_stock_move_by_origin($origin) {// production_order, procurement_purchase
        return $this->db->query("SELECT * FROM stock_move WHERE origin = '$origin' ORDER BY cast(mid(move_id,3,(length(move_id))-2) as unsigned) asc")->result();
    }

    public function cek_status_mrp_productin_by_origin($origin, $dept_id, $status) {// production_order, procurement_purchase
        return $this->db->query("SELECT kode FROM mrp_production WHERE origin = '$origin' AND dept_id = '$dept_id' $status ");
    }

    public function cek_status_pengiriman_barang_by_move_id($origin, $move_id, $status) {// production_order, procurement_purchase, color order
        return $this->db->query("SELECT kode FROM pengiriman_barang WHERE  origin = '$origin' AND move_id = '$move_id' $status ");
    }

    public function cek_status_penerimaan_barang_by_move_id($origin, $move_id, $status) {// production_order, procurement_purchase, color order
        return $this->db->query("SELECT kode FROM penerimaan_barang WHERE  origin = '$origin' AND move_id = '$move_id' $status ");
    }

    public function get_stock_quant_by_id($quant_id) {
        return $this->db->query("SELECT * FROM stock_quant WHERE quant_id = '$quant_id' ");
    }

    public function get_qty_target_pengiriman_barang_by_kode($kode) {
        return $this->db->query("SELECT IFNULL(sum(qty),0) as qty_target FROM pengiriman_barang_items WHERE kode = '$kode' ");
    }

    public function get_qty_tersedia_stock_move_items_by_move_id($move_id) {
        return $this->db->query("SELECT IFNULL(sum(qty),0) as qty_tersedia FROm stock_move_items WHERE move_id = '$move_id'");
    }

    public function get_qty_target_penerimaan_barang_by_kode($kode) {
        return $this->db->query("SELECT IFNULL(sum(qty),0) as qty_target FROM penerimaan_barang_items WHERE kode = '$kode' ");
    }

    public function get_data_mms_for_log_history($id_dept) {
        return $this->db->query("SELECT kode FROM main_menu_sub WHERE dept_id = '$id_dept' ")->row();
    }

    public function get_list_mesin_report($id_dept) {
        return $this->db->query("SELECT mc_id, nama_mesin FROM mesin  WHERE dept_id = '$id_dept' ORDER BY row_order ")->result();
    }

    public function get_mst_status_by_kode($kode) {
        $result = $this->db->query("SELECT *  FROM mst_status where kode = '$kode' ")->row_array();
        return $result['nama_status'];
    }

    public function get_status_aktif_by_produk($kode_produk) {
        return $this->db->query("SELECT status_produk FROM mst_produk WHERE kode_produk = '$kode_produk'");
    }

    public function cek_show_lebar_by_dept_id($dept_id) {
        return $this->db->query("SELECT show_lebar FROM departemen WHERE kode = '$dept_id' ");
    }

    public function get_sales_group_by_sales_order($sales_order) {
        $this->db->select("sales_group"); // MKT001
        $this->db->FROM("sales_contract");
        $this->db->WHERE("sales_order", $sales_order);
        $query = $this->db->get();
        $result = $query->row_array();
        return $result['sales_group'];
    }

    public function get_list_sales_group() {
        $result = $this->db->get('mst_sales_group');
        return $result->result();
    }

    public function get_list_sales_group_by_view($view = null) {
        if(isset($view)){
            $this->db->where('view',$view);
        }
        $result = $this->db->get('mst_sales_group');
        return $result->result();
    }

    public function get_nama_sales_Group_by_kode($kode) {
        $this->db->where('kode_sales_group', $kode);
        $this->db->SELECT('nama_sales_group');
        $this->db->FROM("mst_sales_group");
        $query = $this->db->get();
        $result = $query->row_array();
        return $result['nama_sales_group'];
    }

    public function get_inisial_sales_Group_by_kode($kode) {
        $this->db->where('kode_sales_group', $kode);
        $this->db->SELECT('inisial');
        $this->db->FROM("mst_sales_group");
        $query = $this->db->get();
        $result = $query->row_array();
        return $result['inisial'];
    }

    public function get_list_level_akses() {
        $result = $this->db->get('mst_level_akses');
        return $result->result();
    }

    public function get_list_departemen_all() {
        $this->db->order_by('nama_departemen', 'ASC');
        $result = $this->db->get('mst_departemen_all');
        return $result->result();
    }

    public function get_list_handling() {
        return $this->db->query("SELECT id,nama_handling FROM mst_handling ORDER BY id ")->result();
    }

    public function get_list_category() {
        return $this->db->query("SELECT id, nama_category FROM mst_category ORDER BY nama_category")->result();
    }

    public function get_handling_by_id($id) {
        return $this->db->query("SELECT * FROM mst_handling WHERE id = '$id' ");
    }

    public function get_warna_by_id($id) {
        return $this->db->query("SELECT * FROM warna WHERE id = '$id' ");
    }

    public function get_level_akses_by_user($username) {
        return $this->db->query("SELECT level FROM user where username = '$username' ");
    }

    public function cek_departemen_by_user($username) {
        return $this->db->query("SELECT dept FROM user where username = '$username' ");
    }

    public function get_list_route_co() {
        return $this->db->query("SELECT kode, nama FROM route_co ORDER BY kode ")->result();
    }

    public function get_nama_route_by_kode($kode) {
        return $this->db->query("SELECT nama FROM route_co WHERE kode  = '$kode'");
    }

    public function cek_type_mo_by_dept($kode) {
        $query = $this->db->query("SELECT type_mo FROM departemen WHERE kode = '$kode'")->row_array();
        return $query['type_mo'];
    }

    public function get_kode_departemen_by_stock_location($stock) {
        $query = $this->db->query("SELECT kode FROM departemen where stock_location = '$stock'")->row_array();
        return $query['kode'];
    }

    public function cek_status_mrp_rm_target_additional_move_id_kosong_by_kode($whereMo) {
        return $this->db->query("SELECT kode,status FROM mrp_production_rm_target where kode in (" . $whereMo . ") AND move_id != '' AND additional = 't' AND status IN ('draft','cancel') ");
    }

    public function get_stock_move_items_by_kode($move_id, $quant_id, $kode_produk, $row_order) {
        return $this->db->query("SELECT quant_id, move_id, kode_produk, nama_produk, lot, qty, uom, qty2, uom2, origin_prod, status
								FROM stock_move_items 
								WHERE move_id = '$move_id' AND quant_id = '$quant_id' AND kode_produk = '$kode_produk' AND row_order = '$row_order' ");
    }

    public function startTransaction() {
        $this->db->trans_start();
    }

    public function finishTransaction() {
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }

    public function finishRollBack(){
        $this->db->trans_rollback();
        return false;
    }

    public function finishCommit(){
        $this->db->trans_commit();
        return true;
    }

    public function get_list_number_user_by_dept($dept){
        $this->db->where_in('dept',$dept);
        $this->db->SELECT('telepon_wa');
        $result = $this->db->get('user');
        return $result->result();
    }

    public function cek_telepon_wa_by_user($username) {
        $this->db->where('username',$username);
        $this->db->SELECT('telepon_wa');
        $result = $this->db->get('user');
        return $result->result();
    }

    public function get_list_jenis_kain(){
        $this->db->order_by("id",'asc');
        $result = $this->db->get('mst_jenis_kain');
        return $result->result();
    }
    
    public function get_list_quality($nama=null){
        if($nama){
            $this->db->like('nama', $nama);
        }
        $this->db->order_by('nama','asc');
        $result = $this->db->get('mst_quality');
        return $result->result();
    }

    public function get_list_kode_k3l(){
        $result = $this->db->get('mst_kode_k3l');
        return $result->result();
    }

    public function get_list_desain_barcode_by_type($type){
        if($type){
            $this->db->where('type',$type);
        }
        $this->db->order_by('kode_desain','asc');
        $result = $this->db->get('mst_desain_barcode');
        return $result->result();
    }

    public function get_mst_quality_by_id($id){
        $this->db->where("id",$id);
        return $this->db->get('mst_quality');
    }


}

