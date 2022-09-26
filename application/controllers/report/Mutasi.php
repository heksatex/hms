<?php defined('BASEPATH') OR EXIT ('No Direct Script Acces Allowed');

/**
 * 
 */
class Mutasi extends MY_Controller
{

    public function __construct()
	{
		parent:: __construct();
		$this->is_loggedin();//cek apakah user sudah login
		$this->load->model('m_mutasi');
		$this->load->model('_module');
	}

    public function index()
	{
		$id_dept        = 'MTSI';
        $data['id_dept']= $id_dept;
		$this->load->view('report/v_mutasi', $data);
	}

    function loadData()
    {

        $tanggal    = $this->input->post('tanggal');
        $departemen = $this->input->post('departemen');

        $tahun      = date('Y', strtotime($tanggal)); // example 2022
        $bulan      = date('n', strtotime($tanggal)); // example 8

        $mutasi_dept = $this->m_mutasi->acc_dept_mutasi_by_kode($departemen)->result();

            
            $loop  = 1;
            // foreach($acc_mutasi as $acm){
                
            //         // $dataRecord[] = array(
            //         //     'kode_produk'           => $acm->kode_produk,
            //         //     'nama_produk'           => $acm->nama_produk,
            //         //     's_awal_lot'            => $acm->s_awal_lot,
            //         //     's_awal_qty1'           => $acm->s_awal_qty1,
            //         //     's_awal_qty1_uom'       => $acm->s_awal_qty1_uom,
            //         //     's_awal_qty2'           => $acm->s_awal_qty2,
            //         //     's_awal_qty2_uom'       => $acm->s_awal_qty2_uom,
            //         //     's_awal_qty_opname'     => $acm->s_awal_qty_opname,
            //         //     's_awal_qty_opname_uom' => $acm->s_awal_qty_opname_uom,
            //         // );
               
            //     $no = 1;
            //     foreach($mutasi_dept as $mts){
            //         if (strpos($mts->seq, 'in') == FALSE) {
            //             $in_qty1        = 'in'.$no.'_qty1';
            //             $in_qty1_uom    = 'in'.$no.'_qty1_uom';
            //             $in_qty2        = 'in'.$no.'_qty2';
            //             $in_qty2_uom    = 'in'.$no.'_qty2_uom';
            //             $in_opname      = 'in'.$no.'_qty_opname';
            //             $in_opname_uom  = 'in'.$no.'_qty_opname_uom';

            //             $dataRecord[]   = array(
            //                     $in_qty1  => $acm->$in_qty1
            //             );
            //         }
            //     }
            //     $loop++;
            // }

        $field    = '';
        $no_in    = 1;
        $no_out   = 1;
        $head_table1  = [];
        $head_table2  = [];
        $head_table2_in = [];
        $head_table2_out = [];
        $head_table2_awal = [];
        $head_table2_akhir = [];
            // field saldo awal
        $field   .= 'kode_produk, nama_produk, s_awal_lot, s_awal_qty1, s_awal_qty1_uom, s_awal_qty2,  s_awal_qty2_uom, s_awal_qty_opname, s_awal_qty_opname_uom, '; 
            // field saldo akhir
        $field   .= 's_akhir_lot, s_akhir_qty1, s_akhir_qty1_uom, s_akhir_qty2, s_akhir_qty2_uom, s_akhir_qty_opname, s_akhir_qty_opname_uom, ';
            // field adj in 
        $field  .= 'adj_in_lot, adj_in_qty1, adj_in_qty1_uom, adj_in_qty2, adj_in_qty2_uom, adj_in_qty_opname, adj_in_qty_opname_uom, ';
            // field adj in 
        $field  .= 'adj_out_lot, adj_out_qty1, adj_out_qty1_uom, adj_out_qty2, adj_out_qty2_uom, adj_out_qty_opname, adj_in_qty_opname_uom, ';
        // $head_table2_awal[]  = array('S Awal Lot','S Awal Qty1','S Awal Qty2','S Awal Qty Opname');
        $head_table2_awal[]  = array('Lot','Qty1','Qty2','Qty Opname');
        // $head_table2_akhir[] = array('S Akhir Lot','S Akhir Qty1','S Akhir Qty2','S Akhir Qty Opname');
        $head_table2_adj[]   = array(' Adj Lot',' Adj Qty1',' Adj Qty2',' Adj Qty Opname');
        // $head_table2_adj[]   = array('S Adj Lot','S Adj Qty1','S Adj Qty2','S Adj Qty Opname');

        $jml_in   = 0;
        $jml_out  = 0;

        foreach($mutasi_dept as $mts){
                if (strpos($mts->seq, 'in') === FALSE) {
                    $int_lot        = 'in'.$no_in.'_lot';
                    $in_qty1        = 'in'.$no_in.'_qty1';
                    $in_qty1_uom    = 'in'.$no_in.'_qty1_uom';
                    $in_qty2        = 'in'.$no_in.'_qty2';
                    $in_qty2_uom    = 'in'.$no_in.'_qty2_uom';
                    $in_opname      = 'in'.$no_in.'_qty_opname';
                    $in_opname_uom  = 'in'.$no_in.'_qty_opname_uom';

                    $field .=  $int_lot.', '.$in_qty1.', '.$in_qty1_uom.', '.$in_qty2.', '.$in_qty2_uom.', '.$in_opname.', '.$in_opname_uom.', ';
                    $no_in++;
                    $head_table2_in[] = array('Lot','Qty1','Qty2', 'Qty Opname');
                    $jml_in++;
                    if($jml_in == 1){
                        $head_table1_in[] = array('Penerimaan '.$mts->dept_id_dari);
                    }
                }

                if (strpos($mts->seq, 'out') === FALSE) {
                    $out_lot         = 'out'.$no_out.'_lot';
                    $out_qty1        = 'out'.$no_out.'_qty1';
                    $out_qty1_uom    = 'out'.$no_out.'_qty1_uom';
                    $out_qty2        = 'out'.$no_out.'_qty2';
                    $out_qty2_uom    = 'out'.$no_out.'_qty2_uom';
                    $out_opname      = 'out'.$no_out.'_qty_opname';
                    $out_opname_uom  = 'out'.$no_out.'_qty_opname_uom';

                    $field .=  $out_lot.', '.$out_qty1.', '.$out_qty1_uom.', '.$out_qty2.', '.$out_qty2_uom.', '.$out_opname.', '.$out_opname_uom.', ';
                    $no_out++;
                    $head_table2_out[] = array('Lot','Qty1','Qty2', 'Qty Opname');
                    $jml_out++;
                    if($jml_out == 1){
                        $head_table1_out[] = array('Pengiriman '.$mts->dept_id_tujuan);
                    }
                }
        }
        $field = rtrim($field,', ');
        $acc_mutasi  = $this->m_mutasi->acc_mutasi_gdb_by_kode($tahun,$bulan,$field)->result();

        $head_table1_info[]     = array('Kode Produk', 'Nama Produk');
        $head_table1_awal[]     = array('Saldo Awal');
        $head_table1_akhir[]    = array('Saldo Akhir');
        $head_table1_adj_in[]   = array('Adjustment IN');
        $head_table1_adj_out[]  = array('Adjustment OUT');

        $head_table1[] = array('info'   => $head_table1_info,
                                'awal'  => $head_table1_awal,
                                'in'    => $head_table1_in,
                                'adj_in'=> $head_table1_adj_in,
                                'out'   => $head_table1_out,
                                'adj_out'=>  $head_table1_adj_out,
                                'akhir' => $head_table1_akhir,
                                );

        // header table
        $head_table2[] = array('awal'    => $head_table2_awal, 
                                'in'      => $head_table2_in,
                                'adj_in'  => $head_table2_adj, 
                                'out'     => $head_table2_out, 
                                'adj_out' => $head_table2_adj,
                                'akhir'   => $head_table2_awal);
      

        echo json_encode(array('record'=>$acc_mutasi, 'head_table1'=>$head_table1, 'head_table2' => $head_table2, 'count_in' => $jml_in, 'count_out' => $jml_out));

        
    }
}