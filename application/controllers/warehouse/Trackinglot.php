<?php defined('BASEPATH') or exit ('No Direct Script Acces Allowed');

/**
 * 
 */
class Trackinglot extends MY_Controller
{
  public function __construct()
  {
    parent::__construct();
    $this->is_loggedin();//cek apakah user sudah login
    $this->load->model("_module");
    $this->load->model("m_trackingLot");
  }

  public function index()
  {
    $data['id_dept']  ='WTL';
    $data['list_dept']= $this->_module->get_list_departement();
    $this->load->view('warehouse/v_tracking_lot', $data);
  }

  function search_barcode()
  {
    if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
        // session habis
        $callback = array('message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
    }else{

        $txtlot         = addslashes($this->input->post('txtlot'));
        $result_info    = [];
        $result_record  = [];
        $result_record1  = [];
        $result_tmp     = [];

        $get = $this->m_trackingLot->get_data_info_by_lot($txtlot);
        $jml_barcode = $this->m_trackingLot->get_count_data_info_by_lot($txtlot);

        if(empty($txtlot)){
            $callback = array('status' => 'failed', 'message' => 'Barcode / Lot tidak boleh kosong !', 'icon' =>'fa fa-check', 'type' => 'danger');
        }else{

           // delete tmp_tracking_by_lot()
           $this->m_trackingLot->delete_tmp_tracking_lot_by_lot($txtlot);

            if($get){
                $result_info[] = array( 'lot'=>$get['lot'],
                                    'tgl_dibuat'    => $get['create_date'],
                                    'kode_produk'   => $get['kode_produk'],
                                    'nama_produk'   => $get['nama_produk'],
                                    'corak_remark'   => $get['corak_remark'],
                                    'warna_remark'   => $get['warna_remark'],
                                    'grade'         => $get['nama_grade'],
                                    'qty'           => $get['qty'].' '.$get['uom'],
                                    'qty2'          => $get['qty2'].' '.$get['uom2'],
                                    'qty_jual'      => $get['qty_jual'].' '.$get['uom_jual'],
                                    'qty2_jual'     => $get['qty2_jual'].' '.$get['uom2_jual'],
                                    'lokasi'        => $get['lokasi'],
                                    'lokasi_fisik'  => $get['lokasi_fisik'],
                                    'reff_note'     => $get['reff_note'],
                                    'lot_asal'     => $get['lot_asal'],
                                    'jml'           => $jml_barcode,

                                    );

                // get pengiriman barang
                $out = $this->m_trackingLot->get_pengiriman_barang_by_lot($txtlot);
                foreach($out as $outs){
        	          $kode_encrypt = encrypt_url($outs->kode);

                    $result_record[] = array('tanggal' => $outs->tanggal_transaksi, 
                                            'kode'    => $outs->kode,
                                            'link'     => 'warehouse/pengirimanbarang/edit/'.$kode_encrypt,
                                            'keterangan' => $outs->lokasi_dari.' -> '.$outs->lokasi_tujuan,
                                            'status'  => $outs->nama_status,
                                            'user'  => '',
                                            );
                }

                // get penerimaan barang
                $in = $this->m_trackingLot->get_penerimaan_barang_by_lot($txtlot);
                foreach($in as $ins){
                    $kode_encrypt = encrypt_url($ins->kode);
                    $result_record[] = array('tanggal' => $ins->tanggal_transaksi, 
                                            'kode'    => $ins->kode,
                                            'link'     => 'warehouse/penerimaanbarang/edit/'.$kode_encrypt,
                                            'keterangan' => $ins->lokasi_dari.' -> '.$ins->lokasi_tujuan,
                                            'status'  => $ins->nama_status,
                                            'user'  => '',
                                            );
                }

                // get mrp_production_rm_target status != done
                $rmt = $this->m_trackingLot->get_mrp_cons_target_by_lot($txtlot);
                foreach($rmt as $rmts){
                   $kode_encrypt = encrypt_url($rmts->kode);
                   $result_record[] = array('tanggal' => $rmts->tanggal_transaksi, 
                                     'kode'        => $rmts->kode,
                                     'link'        => 'manufacturing/mO/edit/'.$kode_encrypt,
                                     'keterangan'  => 'Bahan Baku Produksi yang Terpesan -> '.$rmts->nama_dept,
                                     'status'      =>  $rmts->nama_status,
                                     'user'        => '-',
                                     );
                }

                // get mrp_production_rm_hasil
                $rmh = $this->m_trackingLot->get_mrp_cons_by_lot($txtlot);
                foreach($rmh as $rmhs){
                   $kode_encrypt = encrypt_url($rmhs->kode);
                   $result_record[] = array('tanggal' => $rmhs->tanggal_transaksi, 
                                     'kode'         => $rmhs->kode,
                                     'link'       => 'manufacturing/mO/edit/'.$kode_encrypt,
                                     'keterangan'  => 'Bahan Baku Produksi -> '.$rmhs->nama_dept,
                                     'status'      => '-',
                                     'user'        => '-',
                                     );
                }

                // get mrp_production_fg_hasil
                $mrp = $this->m_trackingLot->get_mrp_by_lot($txtlot);
                foreach($mrp as $mrps){
                  $kode_encrypt = encrypt_url($mrps->kode);
                  $result_record[] = array('tanggal' => $mrps->create_date, 
                                    'kode'    => $mrps->kode,
                                    'link'     => 'manufacturing/mO/edit/'.$kode_encrypt,
                                    'keterangan'  => 'Hasil Produksi -> '.$mrps->nama_dept,
                                    'status'      => '-',
                                    'user'        => $mrps->nama_user,
                                    );
                }
               

                // transfer lokasi
                $tl = $this->m_trackingLot->get_transfer_lokasi_by_lot($txtlot);
                foreach($tl as $tls){
                  $kode_encrypt = encrypt_url($tls->kode_tl);
                  $result_record[] = array('tanggal' => $tls->tanggal_transfer, 
                                    'kode'    => $tls->kode_tl,
                                    'link'     => 'warehouse/transferlokasi/edit/'.$kode_encrypt,
                                    'keterangan'  => '( '.$tls->departemen.' )'.'Transfer Lokasi '.$tls->lokasi_asal.' -> '.$tls->lokasi_tujuan,
                                    'status'      => $tls->nama_status,
                                    'user'        => $tls->nama_user,
                                    );
                }

                // adjustment
                $adj = $this->m_trackingLot->get_adjustment_by_lot($txtlot);
                foreach($adj as $adjs){
                  $kode_encrypt = encrypt_url($adjs->kode_adjustment);
                  $result_record[] = array('tanggal' => $adjs->create_date, 
                                    'kode'    => $adjs->kode_adjustment,
                                    'link'     => 'warehouse/adjustment/edit/'.$kode_encrypt,
                                    'keterangan'  => 'Adjustment di  '.$adjs->lokasi_adjustment.' dari '.round($adjs->qty_data,2).' '.$adjs->uom.' menjadi '.round($adjs->qty_adjustment,2).' '.$adjs->uom,
                                    'status'      => $adjs->nama_status,
                                    'user'        => $adjs->nama_user,
                                    );
                }

                // reproses
                $rep = $this->m_trackingLot->get_reproses_by_lot($txtlot);
                foreach($rep as $reps){
                  $kode_encrypt = encrypt_url($reps->kode_reproses);
                  $result_record[] = array('tanggal' => $reps->tanggal, 
                                    'kode'        => $reps->kode_reproses,
                                    'link'        => 'ppic/reproses/edit/'.$kode_encrypt,
                                    'keterangan'  => 'Reproses : Jenis '.$reps->nama_jenis.'  di  '.$reps->lokasi_asal.'. dari Lot '.$reps->lot.' menjadi Lot '.$reps->lot_new,
                                    'status'      => $reps->nama_status,
                                    'user'        => $reps->nama_user,
                                  );
                }

                // SPLIT
                $split = $this->m_trackingLot->get_split_by_lot($txtlot);
                foreach($split as $splits){
                  $kode_encrypt = encrypt_url($splits->kode_split);
                  $result_record[] = array('tanggal' => $splits->tanggal, 
                                    'kode'        => $splits->kode_split,
                                    'link'        => 'warehouse/splitlot/edit/'.$kode_encrypt,
                                    'keterangan'  => 'Lot ini di Split di '.$splits->nama_departemen.' Sebanyak '.$splits->total_split,
                                    'status'      => '-',
                                    'user'        => $splits->nama_user,
                                  );
                }

                // HASIL SPLIT
                $hsplit = $this->m_trackingLot->get_hasil_split_by_lot($txtlot);
                foreach($hsplit as $ssplits){
                  $kode_encrypt = encrypt_url($ssplits->kode_split);
                  $result_record[] = array('tanggal' => $ssplits->tanggal, 
                                    'kode'        => $ssplits->kode_split,
                                    'link'        => 'warehouse/splitlot/edit/'.$kode_encrypt,
                                    'keterangan'  => 'Lot ini dibuat dari hasil SPLIT di Departemen '.$ssplits->nama_departemen.' dari Lot '.$ssplits->lot_asal,
                                    'status'      => '-',
                                    'user'        => $ssplits->nama_user,
                                  );
                }

                // join
                $join  = $this->m_trackingLot->get_join_lot_by_lot($txtlot);
                foreach($join as $joins){
                  $kode_encrypt = encrypt_url($joins->kode_join);
                  $result_record[] = array('tanggal' => $joins->tanggal_transaksi, 
                                    'kode'        => $joins->kode_join,
                                    'link'        => 'warehouse/joinlot/edit/'.$kode_encrypt,
                                    'keterangan'  => 'Lot ini di Hilangkan untuk di JOIN di Departemen '.$joins->nama_departemen,
                                    'status'      => $joins->nama_status,
                                    'user'        => $joins->nama_user,
                                  );
                }

                // hasil join
                $hjoin  = $this->m_trackingLot->get_hasil_join_by_lot($txtlot);
                foreach($hjoin as $joins){
                  $kode_encrypt = encrypt_url($joins->kode_join);
                  $result_record[] = array('tanggal' => $joins->tanggal_transaksi, 
                                    'kode'        => $joins->kode_join,
                                    'link'        => 'warehouse/joinlot/edit/'.$kode_encrypt,
                                    'keterangan'  => 'Lot ini dibuat dari hasil JOIN di Departemen '.$joins->nama_departemen.' dari Lot '.$joins->lot_asal,
                                    'status'      => $joins->nama_status,
                                    'user'        => $joins->nama_user,
                                  );
                }

                //INLET
                $inlet = $this->m_trackingLot->get_inlet_by_Lot($txtlot);
                foreach($inlet as $inlets){

                  $result_record[] = array('tanggal' => $inlets->tanggal, 
                                    'kode'        => $inlets->lot,
                                    'link'        => '',
                                    'keterangan'  => 'KP/Lot ini di INLET',
                                    'status'      => $inlets->nama_status,
                                    'user'        => $inlets->nama_user,
                                  );
                }

                // HASIL HPH
                $hph  = $this->m_trackingLot->get_hph_inlet_by_lot($txtlot);
                foreach($hph as $hphs){
                  $kode_encrypt = encrypt_url($hphs->id_inlet);
                  $result_record[] = array('tanggal' => $hphs->create_date, 
                                    'kode'        => $hphs->kode,
                                    'link'        => 'manufacturing/inlet/edit/'.$kode_encrypt,
                                    'keterangan'  => 'Lot ini dibuat dari HPH',
                                    'status'      => '',
                                    'user'        => $hphs->nama_user,
                                  );
                }

                // Barcode Manual
                $manual  = $this->m_trackingLot->get_barcode_manual_by_lot($txtlot);
                foreach($manual as $manuals){
                  $kode_encrypt = encrypt_url($manuals->kode);
                  $result_record[] = array('tanggal' => $manuals->tanggal_transaksi, 
                                    'kode'        => $manuals->kode,
                                    'link'        => 'manufacturing/barcodemanual/edit/'.$kode_encrypt,
                                    'keterangan'  => 'Lot ini dibuat dari Barcode Manual',
                                    'status'      => '',
                                    'user'        => $manuals->nama_user,
                                  );
                }


                //PICKLIST
                $picklist = $this->m_trackingLot->get_picklist_by_Lot($txtlot);
                foreach($picklist as $picklists){

                  $result_record[] = array('tanggal' => $picklists->tanggal_masuk, 
                                    'kode'        => $picklists->no_pl,
                                    'link'        => '',
                                    'keterangan'  => 'Barcode ini Masuk Picklist ',
                                    'status'      => $picklists->nama_status,
                                    'user'        => '',
                                  );
                }

                // Delivery
                $delivery = $this->m_trackingLot->get_delivery_by_Lot($txtlot);
                foreach($delivery as $deliv){

                  $result_record[] = array('tanggal' => $deliv->tanggal_buat, 
                                    'kode'        => $deliv->no,
                                    'link'        => '',
                                    'keterangan'  => 'Delivery Order : '.$deliv->no,
                                    'status'      => $deliv->nama_status,
                                    'user'        => $deliv->nama_user,
                                  );
                }

                // arsort($result_record);
                $result_record1  =  $this->urutkan($result_record);
                // $sql_insert     = '';
                // foreach($result_record as $row){
                //   $sql_insert .= "('".$get['lot']."','".$row['tanggal']."','".$row['kode']."','".$row['keterangan']."','".$row['status']."','".$row['user']."','".$row['link']."' ), ";
                // }
                
                // if(!empty($sql_insert)){
                //   $sql_insert = rtrim($sql_insert, ', ');
                //   $this->m_trackingLot->insert_tmp_tracking_lot_batch($sql_insert);
                  
                //   $result_tmp = $this->m_trackingLot->get_tmp_tracking_lot_by_lot($txtlot);
                // }
              
                // // delete tmp_tracking_by_lot()
                // $this->m_trackingLot->delete_tmp_tracking_lot_by_lot($txtlot);

                $callback = array('status' => 'success', 'message' => 'Data Barcode / Lot ditemukan !', 'icon' =>'fa fa-check', 'type' => 'success', 'info' => $result_info, 'record' => $result_record1);
            }else{
                $callback = array('status' => 'failed', 'message' => 'Data Barcode / Lot tidak ditemukan !', 'icon' =>'fa fa-warning', 'type' => 'danger', 'info' => $result_info, 'record' => $result_record1);
            }
        }
    }

    echo json_encode($callback);

  }


  function urutkan(array $result_record)
  {
    //print_r($result_record);
    $result_record1  = [];
    $result_record2  = [];
    foreach($result_record as $rr){
      if($rr['status'] == "Ready"){
        $result_record1[] = array('tanggal' => $rr['tanggal'], 
                                  'kode'        => $rr['kode'], 
                                  'link'        => $rr['link'], 
                                  'keterangan'  => $rr['keterangan'], 
                                  'status'      => $rr['status'], 
                                  'user'        => $rr['user'], 
                                  );
      }else{
        $result_record2[] = array('tanggal' => $rr['tanggal'],  
                                'kode'        => $rr['kode'], 
                                'link'        => $rr['link'], 
                                'keterangan'  => $rr['keterangan'], 
                                'status'      => $rr['status'], 
                                'user'        => $rr['user'], 
                                );
      }
    }

    $count = count($result_record2);
    for($i = 1; $i<$count; $i++){
      // var_dump($result_record2[$i]['status']);
      $j = $i-1;
        $element  = $result_record2[$i];
        while($j >= 0  && $result_record2[$j] > $element){
          $result_record2[$j + 1] = $result_record2[$j];
          $result_record2[$j] = $element;
            $j = $j - 1;

        }

    }

    // print_r($result_record);
    return array_merge($result_record2, $result_record1);
  }

}