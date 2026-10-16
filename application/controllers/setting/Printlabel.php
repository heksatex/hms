<?php

defined('BASEPATH') or exit('No Direct Script Acces Allowed');

class Printlabel extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->is_loggedin();
        $this->load->model("m_printlabel");
        $this->load->model("m_lokasi");
    }

    public function index() {
        $data['id_dept'] = 'SPLB';
        $this->load->view('setting/v_print_label', $data);
    }


    public function get_data()
    {
        if(isset($_POST['start']) && isset($_POST['draw'])){
            $list = $this->m_printlabel->get_datatables();
            $data = array();
            $no = $_POST['start'];
            foreach ($list as $field) {
                $no++;
                $row = array();
                $row[] = $no;
                $row[] = $field->seri_beam;
                $row[] = $field->ui;
                $row[] = $field->type_beam;
                $row[] = $field->berat_kosong;
                $row[] = $field->id;
    
                $data[] = $row;
            }
    
            $output = array(
                "draw" => $_POST['draw'],
                "recordsTotal" => $this->m_printlabel->count_all(),
                "recordsFiltered" => $this->m_printlabel->count_filtered(),
                "data" => $data,
            );
            //output dalam format JSON
            echo json_encode($output);

        }else{
            die();
        }
    }


    public function print_plastik()
    {
        // $pdf=new PDF_Code128('l','mm',array(59.944,89.916));

        $this->load->library('Pdf'); //load library pdf
        $pdf = new PDF_Code128('L', 'mm', array(90, 60));

        $pdf->SetMargins(0, 0, 0, 0);
        $pdf->SetAutoPageBreak(FALSE);
        $data_arr  = json_decode($this->input->get('id'), true);

        $pdf->AddPage();

            // Mengatur warna teks hitam pekat
        $pdf->SetTextColor(0, 0, 0); 

            // --- BARIS 1: KATA "PLASTIK" ---
        $pdf->SetFont('Arial', 'B', 50); // Font Arial, Bold, Ukuran 32
        $pdf->SetXY(0, 12);              // Jarak vertikal dari atas kertas
            // Lebar cell 90mm penuh agar posisi teks otomatis rata tengah (C)
        $pdf->Cell(90, 14, 'PLASTIK', 0, 1, 'C', FALSE);

        // --- BARIS 2: ANGKA "100" ---
        $pdf->SetFont('Arial', 'B', 50); // Ukuran font disamakan dengan teks atas
        $pdf->SetXY(0, 30);              // Mengatur posisi vertikal baris kedua
        $pdf->Cell(90, 14, '70-80', 0, 1, 'C', FALSE);


        $pdf->Output();
    }



    public function print()
    {

      {
        // $pdf=new PDF_Code128('l','mm',array(59.944,89.916));

        $this->load->library('Pdf'); //load library pdf
        $pdf = new PDF_Code128('L', 'mm', array(90, 60));

        $pdf->SetMargins(0, 0, 0, 0);
        $pdf->SetAutoPageBreak(FALSE);
        $data_arr  = json_decode($this->input->get('id'), true);

        if (empty($data_arr)) {
            $label_id   =  array(); // id lokasi 
        } else {
            $label_id  = $data_arr;
        }

        $data_print = [];
        foreach ($label_id as $i) {
            $gt = $this->m_printlabel->get_mst_beam_dasar(['id'=>$i])->result();
            foreach ($gt as $gts) {
                $data_print[] = array(
                    'seri_beam'     => $gts->seri_beam,
                    'type_beam'     => $gts->type_beam,
                    'ui'            => $gts->ui,
                    'berat_kosong'  => $gts->berat_kosong,
                );
            }
        }

        foreach ($data_print as $dp) {

            $pdf->AddPage();

            $seri_beam    = $dp['seri_beam'];
            $type_beam    = $dp['type_beam'];
            $ui           = $dp['ui'];
            $berat_kosong = $dp['berat_kosong'];

            // Semuanya menggunakan teks warna hitam karena background polos putih
            $pdf->SetTextColor(0, 0, 0); 

            // --- 1. MENAMPILKAN TEKS KODE UTAMA (SERI BEAM) ---
            $pdf->SetFont('Arial', 'B', 70); 
            $pdf->SetXY(0, 7); 
            $pdf->Cell(90, 14, $seri_beam, 0, 1, 'C', FALSE);

            // --- 2. MENAMPILKAN BARCODE (CODE128) ---
            // Parameter: X=15, Y=25, Teks=$seri_beam, Lebar=60, Tinggi=16
            $pdf->Code128(15, 25, $seri_beam, 60, 16); 

            // --- 3. MENAMPILKAN TEKS KODE KECIL DI BAWAH BARCODE ---
            $pdf->SetFont('Arial', '', 9);
            $pdf->SetXY(0, 35);
            // $pdf->Cell(90, 10, $seri_beam, 0, 1, 'C', FALSE);

            // --- 4. MEMBUAT GARIS PEMBATAS HORISONTAL ---
            // Mengatur ketebalan garis (misal: 0.3 mm)
            $pdf->SetLineWidth(0.3);
            // Parameter Line: X1, Y1, X2, Y2 (Membuat garis lurus dari kiri ke kanan pada tinggi Y=44mm)
            $pdf->Line(0, 44, 90, 44);

            // --- 5. MENAMPILKAN SPESIFIKASI (DI BAWAH GARIS) ---
            $pdf->SetFont('Arial', 'B', 17);
            $pdf->SetXY(0, 49); // Koordinat Y ditaruh di bawah garis pembatas
            
            // Menggabungkan string data (Contoh: 21" x 30" | UI: 45 | 85.7 KG)
            $info_text = $type_beam . " | UI: " . $ui . " | " . (float) $berat_kosong . " KG";
            $pdf->Cell(90, 6, $info_text, 0, 1, 'C', FALSE);

        }

        $pdf->Output();
    }

    }
    
}