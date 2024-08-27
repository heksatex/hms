<?php defined('BASEPATH') OR exit('No Direct Script Acces Allowed');

/**
 * 
 */
class HPHgudangjadi extends MY_Controller
{
    public function __construct()
	{
		parent:: __construct();
		$this->is_loggedin();//cek apakah user sudah login
		$this->load->model('_module');
        $this->load->model('m_HPHgudangjadi');
        $this->load->library('pagination');
	}

    public function index()
	{
		$id_dept        = 'HPHGJD';
        $data['id_dept']= $id_dept;
		$data['mesin']  = $this->_module->get_list_mesin_report('GJD');
		$data['mst_sales_group'] = $this->_module->get_list_sales_group();
        $data['jenis_kain'] = $this->_module->get_list_jenis_kain();        
        $data['quality']    = $this->_module->get_list_quality();   
        $data['sales_group']= $this->_module->get_list_sales_group_by_view(); 
		$this->load->view('report/v_hph_gudang_jadi', $data);
	}

    public function loadData($record=0)
	{
        $recordPerPage = 100;
        if($record != 0){
           $record = ($record-1) * $recordPerPage;
        }

        $data_filter = json_decode($this->input->post('arr_filter'),true); 
      
		// cek tgl dari dan tgl sampai
		if(count($data_filter) <= 0 ){
			$callback = array('status' => 'failed', 'message' => 'Periode Tanggal Harus diisi !', 'icon' =>'fa fa-warning', 'type' => 'danger'  );  

		}else{

            $dataRecord = $this->get_data($data_filter,$record,$recordPerPage,false);

            $total_record = $dataRecord[0];
            $result_record = $dataRecord[1];

			$allcount           = $total_record;
	        $total_record       = 'Total Data : '. number_format($total_record);

            $config['base_url']         = base_url().'report/HPHGudangjadi/loadData';
            $config['use_page_numbers'] = TRUE;
            $config['total_rows']       = $allcount;
            $config['per_page']         = $recordPerPage;
            
            //$config['first_link']     = FALSE;
            //$config['last_link']      = FALSE;
            $config['num_links']        = 1;
            $config['next_link']        = '>';
            $config['prev_link']        = '<';
            $this->pagination->initialize($config);
            $pagination         = $this->pagination->create_links();

			$callback = array('record' => $result_record, 'total_record' => $total_record, 'pagination'=>$pagination,);

		} //else if validasi

		echo json_encode($callback);
	}

    public function get_data(array $data_filter,$record=0,$recordPerPage=0,$excel)
    {

            foreach($data_filter as $row){

                if(!empty($row['tgldari'])){
                    $tgldari    = date('Y-m-d H:i:s', strtotime($row['tgldari']));
                    $where_tgldari_hph      = " AND fg.create_date >= '".$tgldari."' ";
                    $where_tgldari_split    = " AND spl.tanggal >= '".$tgldari."' ";
                    $where_tgldari_join     = " AND jl.tanggal_transaksi >= '".$tgldari."' ";
                    $where_tgldari_manual   = " AND mm.tanggal_transaksi >= '".$tgldari."' ";
                }else{
                    $where_tgldari_hph      = "";
                    $where_tgldari_split    = "";
                    $where_tgldari_join     = "";
                    $where_tgldari_manual   = "";
                }

                if(!empty($row['tglsampai'])){
				    $tglsampai  = date('Y-m-d H:i:s', strtotime($row['tglsampai']));
                    $where_tglsampai_hph      = " AND fg.create_date <= '".$tglsampai."' ";
                    $where_tglsampai_split    = " AND spl.tanggal <=  '".$tglsampai."' ";
                    $where_tglsampai_join     = " AND jl.tanggal_transaksi <=  '".$tglsampai."' ";
                    $where_tglsampai_manual   = " AND mm.tanggal_transaksi <=  '".$tglsampai."' ";
                }else{
                    $where_tglsampai_hph      = "";
                    $where_tglsampai_split    = "";
                    $where_tglsampai_join     = "";
                    $where_tglsampai_manual   = "";
                }

                if(!empty($row['no_hph'])){
                    $where_noHph_hph         = " AND mrpin.kode_mrp LIKE '%".$row['no_hph']."%' ";
                    $where_noHph_split       = " AND spl.kode_split LIKE '%".$row['no_hph']."%' ";
                    $where_noHph_join        = " AND jl.kode_join LIKE '%".$row['no_hph']."%' ";
                    $where_noHph_manual      = " AND mm.kode LIKE '%".$row['no_hph']."%' ";
                }else{
                    $where_noHph_hph         = "";
                    $where_noHph_split       = "";
                    $where_noHph_join        = "";
                    $where_noHph_manual      = "";
                }

                if(!empty($row['lot_bahan_baku'])){
                    $where_lotbahanBaku_hph         = " AND mrpin.lot LIKE '%".$row['lot_bahan_baku']."%' ";
                    $where_lotbahanBaku_split       = " AND spl.lot LIKE '%".$row['lot_bahan_baku']."%' ";
                    $where_lotbahanBaku_join        = " AND jli.lot LIKE '%".$row['lot_bahan_baku']."%' ";
                    $where_lotbahanBaku_manual      = "noField";
                }else{
                    $where_lotbahanBaku_hph         = "";
                    $where_lotbahanBaku_split       = "";
                    $where_lotbahanBaku_join        = "";
                    $where_lotbahanBaku_manual      = "";
                }

                if(!empty($row['corak'])){
                    $where_namaProduk_hph         = " AND fg.nama_produk LIKE '%".$row['corak']."%' ";
                    $where_namaProduk_split       = " AND spl.nama_produk LIKE '%".$row['corak']."%' ";
                    $where_namaProduk_join        = " AND jl.nama_produk LIKE '%".$row['corak']."%' ";
                    $where_namaProduk_manual      = " AND mbi.nama_produk LIKE '%".$row['corak']."%' ";
                }else{
                    $where_namaProduk_hph         = "";
                    $where_namaProduk_split       = "";
                    $where_namaProduk_join        = "";
                    $where_namaProduk_manual      = "";
                }

                if(!empty($row['corak_remark'])){
                    $where_corakRemark_hph         = " AND sq.corak_remark LIKE '%".$row['corak_remark']."%' ";
                    $where_corakRemark_split       = " AND sq.corak_remark LIKE '%".$row['corak_remark']."%' ";
                    $where_corakRemark_join        = " AND jl.corak_remark LIKE '%".$row['corak_remark']."%' ";
                    $where_corakRemark_manual      = " AND mbi.corak_remark LIKE '%".$row['corak_remark']."%' ";
                }else{
                    $where_corakRemark_hph         = "";
                    $where_corakRemark_split       = "";
                    $where_corakRemark_join        = "";
                    $where_corakRemark_manual      = "";
                }

                if(!empty($row['warna_remark'])){
                    $where_warnaRemark_hph         = " AND sq.warna_remark LIKE '%".$row['warna_remark']."%' ";
                    $where_warnaRemark_split       = " AND sq.warna_remark LIKE '%".$row['warna_remark']."%' ";
                    $where_warnaRemark_join        = " AND jl.warna_remark LIKE '%".$row['warna_remark']."%' ";
                    $where_warnaRemark_manual      = " AND mbi.warna_remark LIKE '%".$row['warna_remark']."%' ";
                }else{
                    $where_warnaRemark_hph         = "";
                    $where_warnaRemark_split       = "";
                    $where_warnaRemark_join        = "";
                    $where_warnaRemark_manual      = "";
                }

                if(!empty($row['quality'])){
                    $where_quality_hph         = " AND mrpin.id_quality = '".$row['quality']."' ";
                    $where_quality_split       = " AND hfg.id_quality = '".$row['quality']."' ";
                    $where_quality_join        = " AND hfg.id_quality = '".$row['quality']."' ";
                    $where_quality_manual      = " AND mb.id_quality = '".$row['quality']."' ";
                }else{
                    $where_quality_hph         = "";
                    $where_quality_split       = "";
                    $where_quality_join        = "";
                    $where_quality_manual      = "";
                }

                if(!empty($row['jenis_kain'])){
                    $where_jenisKain_hph         = " AND  mrpin.id_jenis_kain = '".$row['jenis_kain']."' ";
                    $where_jenisKain_split       = " AND ( hfg.id_jenis_kain = '".$row['jenis_kain']."' OR mp.id_jenis_kain = '".$row['jenis_kain']."' ) ";
                    $where_jenisKain_join        = " AND ( hfg.id_jenis_kain = '".$row['jenis_kain']."'  OR mp.id_jenis_kain = '".$row['jenis_kain']."' ) ";
                    $where_jenisKain_manual      = " AND ( mp.id_jenis_kain = '".$row['jenis_kain']."' OR mp.id_jenis_kain = '".$row['jenis_kain']."' ) ";
                }else{
                    $where_jenisKain_hph         = "";
                    $where_jenisKain_split       = "";
                    $where_jenisKain_join        = "";
                    $where_jenisKain_manual      = "";
                }

                if(!empty($row['lot_barang_jadi'])){
                    $where_lotBarangjadi_hph         = " AND fg.lot LIKE '%".$row['lot_barang_jadi']."%' ";
                    $where_lotBarangjadi_split       = " AND spli.lot_baru LIKE '%".$row['lot_barang_jadi']."%' ";
                    $where_lotBarangjadi_join        = " AND jl.lot LIKE '%".$row['lot_barang_jadi']."%' ";
                    $where_lotBarangjadi_manual      = " AND mbi.lot LIKE '%".$row['lot_barang_jadi']."%' ";
                }else{
                    $where_lotBarangjadi_hph         = "";
                    $where_lotBarangjadi_split       = "";
                    $where_lotBarangjadi_join        = "";
                    $where_lotBarangjadi_manual      = "";
                }

                if(!empty($row['benang'])){
                    $where_benang_hph         = " AND mrpin.benang LIKE '%".$row['benang']."%' ";
                    $where_benang_split       = " AND hfg.benang LIKE '%".$row['benang']."%' ";
                    $where_benang_join        = " AND hfg.benang LIKE '%".$row['benang']."%' ";
                    $where_benang_manual      = "noField";
                }else{
                    $where_benang_hph         = "";
                    $where_benang_split       = "";
                    $where_benang_join        = "";
                    $where_benang_manual      = "";
                }

                if(!empty($row['lebar_jadi'])){
                    $where_lebarJadi_hph         = " AND fg.lebar_jadi LIKE '%".$row['lebar_jadi']."%' ";
                    $where_lebarJadi_split       = " AND sq.lebar_jadi LIKE '%".$row['lebar_jadi']."%' ";
                    $where_lebarJadi_join        = " AND jl.lebar_jadi LIKE '%".$row['lebar_jadi']."%' ";
                    $where_lebarJadi_manual      = " AND mbi.lebar_jadi LIKE '%".$row['lebar_jadi']."%' ";
                }else{
                    $where_lebarJadi_hph         = "";
                    $where_lebarJadi_split       = "";
                    $where_lebarJadi_join        = "";
                    $where_lebarJadi_manual      = "";
                }

                if(!empty($row['mc'])){
                    $where_mc_hph         = " AND mrpin.mc_id = '".$row['mc']."' ";
                    $where_mc_split       = "noField";
                    $where_mc_join        = " AND hfg.mc_id = '".$row['mc']."' ";
                    $where_mc_manual      = "noField";
                }else{
                    $where_mc_hph         = "";
                    $where_mc_split       = "";
                    $where_mc_join        = "";
                    $where_mc_manual      = "";
                }

                if(!empty($row['color_order'])){
                    $where_colorOrder_hph         = " AND SUBSTRING_INDEX(SUBSTRING_INDEX(mp.origin,'|',2),'|',-1) LIKE '%".$row['color_order']."%' ";
                    $where_colorOrder_split       = " AND hfg.co LIKE '%".$row['color_order']."%' ";
                    $where_colorOrder_join        = " AND hfg.co LIKE '%".$row['color_order']."%' ";
                    $where_colorOrder_manual      = "noField";
                }else{
                    $where_colorOrder_hph         = "";
                    $where_colorOrder_split       = "";
                    $where_colorOrder_join        = "";
                    $where_colorOrder_manual      = "";
                }

                if(!empty($row['sales_order'])){
                    $where_salesOrder_hph         = " AND SUBSTRING_INDEX(mp.origin,'|',1)  LIKE '%".$row['sales_order']."%' ";
                    $where_salesOrder_split       = " AND hfg.sc LIKE '%".$row['sales_order']."%' ";
                    $where_salesOrder_join        = " AND hfg.sc LIKE '%".$row['sales_order']."%' ";
                    $where_salesOrder_manual      = "noField";
                }else{
                    $where_salesOrder_hph         = "";
                    $where_salesOrder_split       = "";
                    $where_salesOrder_join        = "";
                    $where_salesOrder_manual      = "";
                }

                if(!empty($row['grade'])){
                    $list_grade  = '';
                    foreach($row['grade'] as $gd){
                            $list_grade .= "'$gd', ";
                    }
                    $list_grade = rtrim($list_grade, ', ');

                    if(!empty($list_grade)){
                        $where_grade_hph         = " AND fg.nama_grade IN (".$list_grade.") ";
                        $where_grade_split       = " AND sq.nama_grade IN (".$list_grade.") ";
                        $where_grade_join        = " AND jl.grade IN (".$list_grade.") ";
                        $where_grade_manual      = " AND mb.grade IN (".$list_grade.") ";
                    }
                }else{
                    $where_grade_hph         = "";
                    $where_grade_split       = "";
                    $where_grade_join        = "";
                    $where_grade_manual      = "";
                }


                if(!empty($row['user'])){
                    $where_user_hph         = " AND fg.nama_user  LIKE '%".$row['user']."%' ";
                    $where_user_split       = " AND spl.nama_user LIKE '%".$row['user']."%' ";
                    $where_user_join        = " AND jl.nama_user LIKE '%".$row['user']."%' ";
                    $where_user_manual      = " AND mm.nama_user LIKE '%".$row['user']."%' ";
                }else{
                    $where_user_hph         = "";
                    $where_user_split       = "";
                    $where_user_join        = "";
                    $where_user_manual      = "";
                }

                if(!empty($row['jenis']) AND $row['jenis']){
                    $show_jenis_hph =  $row['jenis'];
                }

                if(!empty($row['marketing'])){
                    $where_marketing_hph         = " AND mrpin.sales_group = '".$row['marketing']."' ";
                    $where_marketing_split       = " AND sq.sales_group = '".$row['marketing']."' ";
                    $where_marketing_join        = " AND jl.sales_group = '".$row['marketing']."' ";
                    $where_marketing_manual      = " AND mm.sales_group = '".$row['marketing']."' ";
                }else{
                    $where_marketing_hph         = "";
                    $where_marketing_split       = "";
                    $where_marketing_join        = "";
                    $where_marketing_manual      = "";
                }
                

            }
         	$id_dept   = 'GJD';
			// get location by jenis (HPH=stock, Waste)
			$cek       = $this->_module->get_nama_dept_by_kode($id_dept)->row_array();

			$dataRecord= [];

			$where     = " WHERE mrpin.status NOT IN ('draft','cancel') AND mp.dept_id = '".$id_dept."' ".$where_tgldari_hph." ".$where_tglsampai_hph." ".$where_noHph_hph." ".$where_lotbahanBaku_hph." ".$where_namaProduk_hph." ".$where_corakRemark_hph." ".$where_warnaRemark_hph." ".$where_quality_hph." ".$where_jenisKain_hph." ".$where_lotBarangjadi_hph." ".$where_benang_hph." ".$where_lebarJadi_hph." ".$where_mc_hph." ".$where_colorOrder_hph." ".$where_salesOrder_hph." ".$where_grade_hph." ".$where_user_hph." ".$where_marketing_hph;
            if($show_jenis_hph == 'All' or $show_jenis_hph == 'HPH' or $show_jenis_hph == "SUSUT"){
                // if($show_jenis_hph == "SUSUT" ){
                //     $where = $where." AND fg.lokasi ='GJD/Waste' ";
                // }else{ // stock / ALl
                //     $where = $where." AND fg.lokasi ='GJD/Stock' ";
                // }
                $items = $this->m_HPHgudangjadi->get_list_hph_by_kode($where);
                foreach ($items as $val) {

                    $dataRecord[] = array('no_hph' 	   => $val->no_hph,
                                        'nama_mesin' => $val->nama_mesin,
                                        'lot'        => $val->lot,
                                        'nama_produk'=> $val->nama_produk,
                                        'qty_prod'   => $val->mtr_prod,
                                        'uom_prod'   => $val->uom,
                                        'qty2_prod'  => $val->kg_prod,
                                        'uom2_prod'  => $val->uom2,
                                        'nama_quality'=> $val->nama_quality,
                                        'tgl_hph'    => $val->tgl_hph,
                                        'lot2'	     => $val->lot_gjd,
                                        'corak_remark'=> $val->corak_remark,
                                        'warna_remark'=> $val->warna_remark,
                                        'qty1_hph'   => $val->qty1_hph,
                                        'uom_hph'    => $val->uom1_hph,
                                        'qty2_hph'   => $val->qty2_hph,
                                        'uom2_hph'   => $val->uom2_hph,
                                        'grade'      => $val->nama_grade,
                                        'lbr_jadi'   => $val->lebar_jadi,
                                        'uom_lbr_jadi'=> $val->uom_lebar_jadi,
                                        'jenis_kain' => $val->nama_jenis_kain,
                                        'gramasi'    => $val->gramasi,
                                        'berat'      => $val->berat,
                                        'benang'     => $val->benang,
                                        'qty1_jual'  => $val->qty_jual,
                                        'uom_jual'   => $val->uom_jual,
                                        'qty2_jual'  => $val->qty2_jual,
                                        'uom2_jual'  => $val->uom2_jual,
                                        'marketing'  => $val->nama_sales_group,
                                        'sc'         => $val->sc,
                                        'co'         => $val->co,
                                        'nama_user'  => $val->nama_user,
                                        'operator'   => $val->operator,
                                        'keterangan' => 'Barcode HPH',
                                        'notes'      => ''
                                        );
                
                }
            }

            // SPLIT LOT
            $where     = " WHERE spl.dept_id = '".$id_dept."' ".$where_tgldari_split." ".$where_tglsampai_split." ".$where_noHph_split." ".$where_lotbahanBaku_split." ".$where_namaProduk_split." ".$where_corakRemark_split." ".$where_warnaRemark_split." ".$where_quality_split." ".$where_jenisKain_split." ".$where_lotBarangjadi_split." ".$where_benang_split." ".$where_lebarJadi_split." ".$where_colorOrder_split." ".$where_salesOrder_split." ".$where_grade_split." ".$where_user_split." ".$where_marketing_split;

            if($show_jenis_hph == 'All' or $show_jenis_hph == 'SPLIT'){
                if($where_mc_split != 'noField'){
                    $items = $this->m_HPHgudangjadi->get_list_split_by_kode($where);
                    foreach($items as $val) {
                        $dataRecord[] = array('no_hph' 	   => $val->no_hph,
                                            'nama_mesin' => '',
                                            'lot'        => $val->lot,
                                            'nama_produk'=> $val->nama_produk,
                                            'qty_prod'   => $val->qty_awal,
                                            'uom_prod'   => $val->uom_awal,
                                            'qty2_prod'  => $val->qty2_awal,
                                            'uom2_prod'   => $val->uom2_awal,
                                            'nama_quality'=> $val->nama_quality,
                                            'tgl_hph'    => $val->tanggal,
                                            'lot2'	   => $val->lot_gjd,
                                            'corak_remark'=> $val->corak_remark,
                                            'warna_remark'=> $val->warna_remark,
                                            'qty1_hph'   => $val->qty,
                                            'uom_hph'    => $val->uom,
                                            'qty2_hph'   => $val->qty2,
                                            'uom2_hph'   => $val->uom2,
                                            'grade'      => $val->nama_grade,
                                            'lbr_jadi'   => $val->lebar_jadi,
                                            'uom_lbr_jadi'=>$val->uom_lebar_jadi,
                                            'jenis_kain' => $val->nama_jenis_kain,
                                            'gramasi'    => $val->gramasi,
                                            'berat'      => $val->berat,
                                            'benang'     => $val->benang,
                                            'qty1_jual'  => $val->qty_jual,
                                            'uom_jual'   => $val->uom_jual,
                                            'qty2_jual'  => $val->qty2_jual,
                                            'uom2_jual'   => $val->uom2_jual,
                                            'marketing'  => $val->nama_sales_group,
                                            'sc'         => $val->sc,
                                            'co'         => $val->co,
                                            'nama_user'  => $val->nama_user,
                                            'operator'   => $val->operator,
                                            'keterangan' => 'Barcode SPLIT',
                                            'notes'      => $val->note
                                            );
                    }
                }
            }


            // JOIN LOT
            $where     = " WHERE jl.dept_id = '".$id_dept."' AND jl.status = 'done' ".$where_tgldari_join." ".$where_tglsampai_join." ".$where_noHph_join." ".$where_lotbahanBaku_join." ".$where_namaProduk_join." ".$where_corakRemark_join." ".$where_warnaRemark_join." ".$where_quality_join." ".$where_jenisKain_join." ".$where_lotBarangjadi_join." ".$where_benang_join." ".$where_lebarJadi_join." ".$where_mc_join." ".$where_colorOrder_join." ".$where_salesOrder_join." ".$where_grade_join." ".$where_user_join." ".$where_marketing_join;
            if($show_jenis_hph == 'All' or $show_jenis_hph == 'JOIN'){
                $items = $this->m_HPHgudangjadi->get_list_join_by_kode($where);
                foreach($items as $val) {
                    $dataRecord[] = array('no_hph' 	   => $val->no_hph,
                                        'nama_mesin' => '',
                                        'lot'        => $val->lot_asal,
                                        'nama_produk'=> $val->nama_produk,
                                        'qty_prod'   => $val->tot_qty1,
                                        'uom_prod'   => $val->uom_tot,
                                        'qty2_prod'  => $val->tot_qty2,
                                        'uom2_prod'   => $val->uom2_tot,
                                        'nama_quality'=>  $val->nama_quality,
                                        'tgl_hph'    => $val->tanggal_transaksi,
                                        'lot2'	   => $val->lot_gjd,
                                        'corak_remark'=> $val->corak_remark,
                                        'warna_remark'=> $val->warna_remark,
                                        'qty1_hph'   => $val->qty,
                                        'uom_hph'    => $val->uom,
                                        'qty2_hph'   => $val->qty2,
                                        'uom2_hph'   => $val->uom2,
                                        'grade'      => $val->grade,
                                        'lbr_jadi'   => $val->lebar_jadi,
                                        'uom_lbr_jadi'=> $val->uom_lebar_jadi,
                                        'jenis_kain' => $val->nama_jenis_kain,
                                        'gramasi'    => $val->gramasi,
                                        'berat'      => $val->berat,
                                        'benang'     => $val->benang,
                                        'qty1_jual'  => $val->qty_jual,
                                        'uom_jual'   => $val->uom_jual,
                                        'qty2_jual'  => $val->qty2_jual,
                                        'uom2_jual'   => $val->uom2_jual,
                                        'marketing'  => $val->nama_sales_group,
                                        'sc'         => $val->sc,
                                        'co'         => $val->co,
                                        'nama_user'  => $val->nama_user,
                                        'operator'   => $val->operator,
                                        'keterangan' => 'Barcode JOIN',
                                        'notes'      => $val->note
                                        );
                }
            }

            // Barcode Manual
            $where     = " WHERE mm.status = 'done' ".$where_tgldari_manual." ".$where_tglsampai_manual." ".$where_noHph_manual." ".$where_namaProduk_manual." ".$where_corakRemark_manual." ".$where_warnaRemark_manual." ".$where_quality_manual." ".$where_lotBarangjadi_manual." ".$where_lebarJadi_manual." ".$where_grade_manual." ".$where_user_manual." ".$where_marketing_manual;
            if($show_jenis_hph == 'All' or $show_jenis_hph == 'MANUAL'){
                if($where_lotbahanBaku_manual != 'noField' AND $where_jenisKain_manual != 'noField' AND $where_benang_manual != 'noField' AND $where_mc_manual != 'noField' AND $where_colorOrder_manual != 'noField' AND $where_salesOrder_manual != 'noField'){
                    $items = $this->m_HPHgudangjadi->get_list_barcode_manual_by_kode($where);
                    foreach($items as $val) {
                        $dataRecord[] = array('no_hph' 	   => $val->no_hph,
                                            'nama_mesin' => '',
                                            'lot'        => '',
                                            'nama_produk'=> $val->nama_produk,
                                            'qty_prod'   => '',
                                            'uom_prod'   => '',
                                            'qty2_prod'  => '',
                                            'uom2_prod'   => '',
                                            'nama_quality'=> $val->nama_quality,
                                            'tgl_hph'    => $val->tanggal_transaksi,
                                            'lot2'	     => $val->lot,
                                            'corak_remark'=> $val->corak_remark,
                                            'warna_remark'=> $val->warna_remark,
                                            'qty1_hph'   => $val->qty,
                                            'uom_hph'    => $val->uom,
                                            'qty2_hph'   => $val->qty2,
                                            'uom2_hph'    => $val->uom2,
                                            'grade'      => $val->grade,
                                            'lbr_jadi'   => $val->lebar_jadi,
                                            'uom_lbr_jadi'=> $val->uom_lebar_jadi,
                                            'jenis_kain' => $val->nama_jenis_kain,
                                            'gramasi'    => '',
                                            'berat'      => '',
                                            'benang'     => '',
                                            'qty1_jual'  => $val->qty_jual,
                                            'uom_jual'   => $val->uom_jual,
                                            'qty2_jual'  => $val->qty2_jual,
                                            'uom2_jual'  => $val->uom2_jual,
                                            'marketing'  => $val->nama_sales_group,
                                            'sc'         => '',
                                            'co'         => '',
                                            'nama_user'  => $val->nama_user,
                                            'operator'   => '',
                                            'keterangan' => 'Barcode Manual',
                                            'notes'      => $val->notes
                                            );
                    }
                }
            }
            return $result_record = $this->urutkan($dataRecord,$record,$recordPerPage,$excel);
    }


    function column_excel($jml)
    {
        $max    = $jml; 
        $result = 'A';
        $tmp_result = [];
        for ($l = 'A', $i = 1; $i <= $max; $l++, $i++) {
            $tmp_result[]=$l;
        }
        return $tmp_result;
    }

    function urutkan(array $result_record, $record, $recordPerPage, $excel)
    {
      
        $key_values     = array_column($result_record, 'tgl_hph'); 
        array_multisort($key_values, SORT_ASC, $result_record);
        $total_all_record =  count($result_record);
        if(empty($result_record)){
            $result_record_show = $result_record;
        }else{
    
            $show_record = $record + $recordPerPage;
            if($total_all_record <= $show_record){
                $show_record = count($result_record);
            }

            $result_record_show = [];
            for($i = $record; $i<$show_record; $i++){
                $result_record_show[] = $result_record[$i];
            }
        }
        if($excel == true){
            return array($total_all_record,$result_record);
        }else{
            return array($total_all_record,$result_record_show);
        }
    }


	public function export_excel_hph()
	{

        $data_filter = json_decode($this->input->post('arr_filter'),true); 
      
		// cek tgl dari dan tgl sampai
		if(count($data_filter) <= 0 ){
			$callback = array('status' => 'failed', 'message' => 'Silahkan Generate terlebih dahulu !', 'icon' =>'fa fa-warning', 'type' => 'danger'  );  

		}else{
            
            $this->load->library('excel');
            ob_start();
            ini_set('memory_limit', '2048M');
            $tgldari_capt   = date('Y-m-d');
            $tglsampai_capt = date('Y-m-d');
            foreach($data_filter as $row){

                if(!empty($row['tgldari'])){
                    $tgldari_capt    = date('Y-m-d H:i:s', strtotime($row['tgldari']));
                   
                }
                if(!empty($row['tglsampai'])){
				    $tglsampai_capt  = date('Y-m-d H:i:s', strtotime($row['tglsampai']));
                }
            }
            
            $where_date = '';
            
            $record = $this->get_data($data_filter,0,0,true);
            $dataRecord = $record[1] ?? '';
            $id_dept    = 'GJD';
			$cek        = $this->_module->get_nama_dept_by_kode($id_dept)->row_array();

            $object = new PHPExcel();
            $object->setActiveSheetIndex(0);

            // SET JUDUL
            $object->getActiveSheet()->SetCellValue('A1', 'Laporan HPH');
            $object->getActiveSheet()->getStyle('A1')->getAlignment()->setIndent(1);
            $object->getActiveSheet()->mergeCells('A1:L1');

            // set Departemen
            $object->getActiveSheet()->SetCellValue('A2', 'Departemen');
            $object->getActiveSheet()->mergeCells('A2:B2');
            $object->getActiveSheet()->SetCellValue('C2', ': '.$cek['nama']);
            $object->getActiveSheet()->mergeCells('C2:D2');


            // set periode
            $object->getActiveSheet()->SetCellValue('A3', 'Periode');
            $object->getActiveSheet()->mergeCells('A3:B3');
            $object->getActiveSheet()->SetCellValue('C3', ': '.tgl_indo(date('d-m-Y',strtotime($tgldari_capt))).' - '.tgl_indo(date('d-m-Y',strtotime($tglsampai_capt)) ));
            $object->getActiveSheet()->mergeCells('C3:F3');

            //bold huruf
            $object->getActiveSheet()->getStyle("A1:AI6")->getFont()->setBold(true);

            // Border 
            $styleArray = array(
                'borders' => array(
                    'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                    )
                )
            );

            $styleArray2 = array(
                'borders' => array(
                    'left' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN
                    )
                )
            );	


            // header table
            $table_head_columns  = array('No', 'No.HPH', 'MC GJD', 'Lot(Bahan Baku)' ,'Nama Produk', 'Qty (Bahan Baku)', 'Uom (Bahan Baku)', 'Qty2 (Bahan Baku)', 'Uom2 (Bahan Baku)', 'Quality', 'Tanggal.Proses', 'Lot(Barang jadi)', 'Corak Remark', 'Warna Remark', 'Qty1 HPH', 'Uom1 HPH', 'Qty2 HPH', 'Uom2 HPH', 'Grade','L.Jadi','uom L.Jadi','Jenis Kain', 'Gramasi', 'Berat/Mtr/pnl', 'Benang', 'Qty1 Jual', 'Uom1 Jual', 'Qty2 Jual', 'Uom2 Jual','Marketing','SC','CO','Keterangan','Notes','Operator','User');
            $column = 0;
            foreach ($table_head_columns as $field) {

                $object->getActiveSheet()->setCellValueByColumnAndRow($column, 6, $field);  

                $column++;
            }
          

            // set wdith and border
            // $index_header = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB');
            $index_header = $this->column_excel(count($table_head_columns));
            $loop = 0;
            foreach ($index_header as $val) {
                
                $object->getActiveSheet()->getStyle($val.'6')->applyFromArray($styleArray);

                if($loop <= 0 or $loop == 5 or $loop == 6 or $loop == 7 or $loop == 8 or $loop == 10 or $loop == 14 or $loop == 15 or $loop == 16 AND $loop == 17 AND $loop == 18 or ($loop >=24 AND $loop == 27)){
                    $object->getSheet(0)->getColumnDimension($val)->setAutoSize(true); // index A,F,G,H,I,
                }else if($loop == 1 OR $loop ==3 OR $loop == 9 or $loop == 11 or $loop  == 12 or $loop == 13 or $loop == 19 or $loop == 20 or $loop == 21 or $loop ==22 or $loop == 23 or $loop>= 28){
                    $object->getSheet(0)->getColumnDimension($val)->setWidth(20); // index B,D,J,K,L,M,N
                }else if($loop == 2){
                    $object->getSheet(0)->getColumnDimension($val)->setWidth(9); // index C
                }else if($loop == 4){
                    $object->getSheet(0)->getColumnDimension($val)->setWidth(40); // index E
                }

                $loop++;
            }

            $rowCount = 7;
            $num      = 1;
 
            foreach ($dataRecord as $val) {

                $object->getActiveSheet()->SetCellValue('A'.$rowCount, ($num++));
                $object->getActiveSheet()->SetCellValue('B'.$rowCount, $val['no_hph']);
                $object->getActiveSheet()->SetCellValue('C'.$rowCount, $val['nama_mesin']);
                $object->getActiveSheet()->SetCellValue('D'.$rowCount, $val['lot']);
                $object->getActiveSheet()->SetCellValue('E'.$rowCount, $val['nama_produk']);
                $object->getActiveSheet()->SetCellValue('F'.$rowCount, $val['qty_prod']);
                $object->getActiveSheet()->SetCellValue('G'.$rowCount, $val['uom_prod']);
                $object->getActiveSheet()->SetCellValue('H'.$rowCount, $val['qty2_prod']);
                $object->getActiveSheet()->SetCellValue('I'.$rowCount, $val['uom2_prod']);
                $object->getActiveSheet()->SetCellValue('J'.$rowCount, $val['nama_quality']);
                $object->getActiveSheet()->SetCellValue('K'.$rowCount, $val['tgl_hph']);
                $object->getActiveSheet()->SetCellValue('L'.$rowCount, $val['lot2']);
                $object->getActiveSheet()->SetCellValue('M'.$rowCount, $val['corak_remark']);
                $object->getActiveSheet()->SetCellValue('N'.$rowCount, $val['warna_remark']);
                $object->getActiveSheet()->SetCellValue('O'.$rowCount, $val['qty1_hph']);
                $object->getActiveSheet()->SetCellValue('P'.$rowCount, $val['uom_hph']);
                $object->getActiveSheet()->SetCellValue('Q'.$rowCount, $val['qty2_hph']);
                $object->getActiveSheet()->SetCellValue('R'.$rowCount, $val['uom2_hph']);
                $object->getActiveSheet()->SetCellValue('S'.$rowCount, $val['grade']);
                $object->getActiveSheet()->SetCellValue('T'.$rowCount, $val['lbr_jadi']);
                $object->getActiveSheet()->SetCellValue('U'.$rowCount, $val['uom_lbr_jadi']);
                $object->getActiveSheet()->SetCellValue('V'.$rowCount, $val['jenis_kain']);
                $object->getActiveSheet()->SetCellValue('W'.$rowCount, $val['gramasi']);
                $object->getActiveSheet()->SetCellValue('X'.$rowCount, $val['berat']);
                $object->getActiveSheet()->SetCellValue('Y'.$rowCount, $val['benang']);
                $object->getActiveSheet()->SetCellValue('Z'.$rowCount, $val['qty1_jual']);
                $object->getActiveSheet()->SetCellValue('AA'.$rowCount, $val['uom_jual']);
                $object->getActiveSheet()->SetCellValue('AB'.$rowCount, $val['qty2_jual']);
                $object->getActiveSheet()->SetCellValue('AC'.$rowCount, $val['uom2_jual']);
                $object->getActiveSheet()->SetCellValue('AD'.$rowCount, $val['marketing']);
                $object->getActiveSheet()->SetCellValue('AE'.$rowCount, $val['sc']);
                $object->getActiveSheet()->SetCellValue('AF'.$rowCount, $val['co']);
                $object->getActiveSheet()->SetCellValue('AG'.$rowCount, $val['keterangan']);
                $object->getActiveSheet()->SetCellValue('AH'.$rowCount, $val['notes']);
                $object->getActiveSheet()->SetCellValue('AI'.$rowCount, $val['operator']);
                $object->getActiveSheet()->SetCellValue('AJ'.$rowCount, $val['nama_user']);

                // set align
                $object->getActiveSheet()->getStyle('B'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $object->getActiveSheet()->getStyle('X'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                
            
                //set border true
                $object->getActiveSheet()->getStyle('A'.$rowCount)->applyFromArray($styleArray);
                $object->getActiveSheet()->getStyle('B'.$rowCount)->applyFromArray($styleArray);
                $object->getActiveSheet()->getStyle('C'.$rowCount)->applyFromArray($styleArray);
                $object->getActiveSheet()->getStyle('D'.$rowCount)->applyFromArray($styleArray);
                $object->getActiveSheet()->getStyle('E'.$rowCount)->applyFromArray($styleArray);
                $object->getActiveSheet()->getStyle('F'.$rowCount)->applyFromArray($styleArray);
                $object->getActiveSheet()->getStyle('G'.$rowCount)->applyFromArray($styleArray);
                $object->getActiveSheet()->getStyle('H'.$rowCount)->applyFromArray($styleArray);
                $object->getActiveSheet()->getStyle('H'.$rowCount)->applyFromArray($styleArray);
                $object->getActiveSheet()->getStyle('I'.$rowCount)->applyFromArray($styleArray);
                $object->getActiveSheet()->getStyle('J'.$rowCount)->applyFromArray($styleArray);
                $object->getActiveSheet()->getStyle('K'.$rowCount)->applyFromArray($styleArray);
                $object->getActiveSheet()->getStyle('L'.$rowCount)->applyFromArray($styleArray);
                $object->getActiveSheet()->getStyle('M'.$rowCount)->applyFromArray($styleArray);
                $object->getActiveSheet()->getStyle('N'.$rowCount)->applyFromArray($styleArray);
                $object->getActiveSheet()->getStyle('O'.$rowCount)->applyFromArray($styleArray);
                $object->getActiveSheet()->getStyle('P'.$rowCount)->applyFromArray($styleArray);
                $object->getActiveSheet()->getStyle('Q'.$rowCount)->applyFromArray($styleArray);
                $object->getActiveSheet()->getStyle('R'.$rowCount)->applyFromArray($styleArray);
                $object->getActiveSheet()->getStyle('S'.$rowCount)->applyFromArray($styleArray);
                $object->getActiveSheet()->getStyle('T'.$rowCount)->applyFromArray($styleArray);
                $object->getActiveSheet()->getStyle('U'.$rowCount)->applyFromArray($styleArray);
                $object->getActiveSheet()->getStyle('V'.$rowCount)->applyFromArray($styleArray);
                $object->getActiveSheet()->getStyle('W'.$rowCount)->applyFromArray($styleArray);
                $object->getActiveSheet()->getStyle('X'.$rowCount)->applyFromArray($styleArray);
                $object->getActiveSheet()->getStyle('Y'.$rowCount)->applyFromArray($styleArray);
                $object->getActiveSheet()->getStyle('Z'.$rowCount)->applyFromArray($styleArray);
                $object->getActiveSheet()->getStyle('AA'.$rowCount)->applyFromArray($styleArray);
                $object->getActiveSheet()->getStyle('AB'.$rowCount)->applyFromArray($styleArray);
                $object->getActiveSheet()->getStyle('AB'.$rowCount)->applyFromArray($styleArray);
                $object->getActiveSheet()->getStyle('AC'.$rowCount)->applyFromArray($styleArray);
                $object->getActiveSheet()->getStyle('AD'.$rowCount)->applyFromArray($styleArray);
                $object->getActiveSheet()->getStyle('AE'.$rowCount)->applyFromArray($styleArray);
                $object->getActiveSheet()->getStyle('AF'.$rowCount)->applyFromArray($styleArray);
                $object->getActiveSheet()->getStyle('AG'.$rowCount)->applyFromArray($styleArray);
                $object->getActiveSheet()->getStyle('AG'.$rowCount)->applyFromArray($styleArray);
                $object->getActiveSheet()->getStyle('AH'.$rowCount)->applyFromArray($styleArray);
                $object->getActiveSheet()->getStyle('AI'.$rowCount)->applyFromArray($styleArray);
                $object->getActiveSheet()->getStyle('AJ'.$rowCount)->applyFromArray($styleArray);

                $rowCount++;
            }

            $object = PHPExcel_IOFactory::createWriter($object, 'Excel2007');  
            $object->save('php://output');
            $xlsData = ob_get_contents();
            ob_end_clean();
            $name_file = "HPH ".$cek['nama'].".xlsx";
            $response =  array(
                'op'        => 'ok',
                'file'      => "data:application/vnd.ms-excel;base64,".base64_encode($xlsData),
                'filename'  => $name_file
            );
            
        }
        die(json_encode($response));
    }


}