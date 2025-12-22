<?php

defined('BASEPATH') or exit('No Direct Script Acces Allowed');


class Mutasipenjualan extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->is_loggedin();
        // $this->load->model('_module');
        $this->load->model('m_mutasipenjualan');
    }

    public function index()
    {
        $data['id_dept'] = 'RMP';
        $this->load->view('report/v_mutasi_penjualan', $data);
    }


    function loadData()
    {
        try {
            //code...
            $filter = $this->_collectFilter();

            $data       = $this->proses_data();
            $callback   = array('status' => 'success', 'message' => 'berhasil', 'icon' => 'fa fa-check', 'type' => 'success', 'record' => $data);
            $this->output->set_status_header(200)
                ->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode($callback));
        } catch (Exception $ex) {
            $this->output->set_status_header(($ex->getCode()) ?? 500)
                ->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode(array('status'  => 'failed', 'message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }



    private function _collectFilter()
    {
        $post = $this->input->post();

        return [
            'check_tgl'  => !empty($post['check_tgl']), // true / false
            'partner'    => !empty($post['partner'])    ? $post['partner']    : null,
            'tgldari'    => !empty($post['tgldari'])    ? $this->_formatDate($post['tgldari']) : null,
            'tglsampai'  => !empty($post['tglsampai'])  ? $this->_formatDate($post['tglsampai']) : null,
            'no_faktur'  => !empty($post['no_faktur'])  ? $post['no_faktur']  : null,
            'no_sj'      => !empty($post['no_sj'])      ? $post['no_sj']      : null,
            'tipe'       => !empty($post['tipe'])       ? $post['tipe']       : null,
        ];
    }


    private function _formatDate($date)
    {
        // dari: 18-December-2025 → 2025-12-18
        return date('Y-m-d', strtotime($date));
    }


    function proses_data()
    {
        $filter = $this->_collectFilter();

        // =========================
        // WHERE DASAR
        // =========================
        $where = [
            'fak.status' => 'confirm',
            'fak.lunas'  => 0
        ];

        // =========================
        // FILTER NON ARRAY
        // =========================
        if ($filter['tipe'] != 'all') {
            $where['fak.tipe'] = $filter['tipe'];
        }

        // =========================
        // TANGGAL
        // =========================
        $date_filter = [];
        if ($filter['check_tgl']) {
            if (!empty($filter['tgldari'])) {
                $date_filter['from'] = $filter['tgldari'];
            }
            if (!empty($filter['tglsampai'])) {
                $date_filter['to'] = $filter['tglsampai'];
            }
        }

        // =========================
        // TEXT FILTER
        // =========================
        $like = [];
        if (!empty($filter['no_faktur'])) {
            $like['fak.no_faktur_internal'] = $filter['no_faktur'];
        }
        if (!empty($filter['no_sj'])) {
            $like['fak.no_sj'] = $filter['no_sj'];
        }

        // =========================
        // PARTNER (ARRAY / SINGLE)
        // =========================
        $partner = $filter['partner'];

        // =========================
        // HEAD DATA
        // =========================
        $head = $this->m_mutasipenjualan->get_group_partner($where, $partner, $date_filter, $like);

        if (empty($head)) {
            return [];
        }

        // =========================
        // DETAIL
        // =========================
        $result = [];
        $sisa   = 0;
        foreach ($head as $row) {

            $detail = $this->m_mutasipenjualan->get_detail_by_partner($date_filter,$row->partner_id,$where,$like);
            $items = [];
            $sisa   = 0;
            
            foreach ($detail as $d) {
                $sisa   = (float) $d->total_piutang - (float) $d->total_pelunasan -  (float) $d->total_retur - (float) $d->total_diskon;
                $items[] = [
                    'tgl_faktur'         => date('Y-m-d', strtotime($d->tanggal)),
                    'no_faktur'          => $d->no_faktur,
                    'no_sj'              => $d->no_sj,
                    'tipe'               => ucfirst($d->tipe),
                    'dpp_piutang'        => (float) $d->dpp_piutang,
                    'ppn_piutang'        => (float) $d->ppn_piutang,
                    'total_piutang'      => (float) $d->total_piutang,
                    'tgl_pelunasan'      => $d->tanggal_pelunasan,
                    'no_bukti_pelunasan' => $d->no_bukti_pelunasan,
                    'total_pelunasan'    => (float) $d->total_pelunasan,
                    'tgl_retur'          => $d->tanggal_retur,
                    'no_bukti_retur'     => $d->no_bukti_retur,
                    'dpp_retur'          => (float) $d->dpp_retur,
                    'ppn_retur'          => (float) $d->ppn_retur,
                    'total_retur'        => (float) $d->total_retur,
                    'dpp_diskon'          => (float) $d->dpp_diskon,
                    'ppn_diskon'          => (float) $d->ppn_diskon,
                    'total_diskon'        => (float) $d->total_diskon,
                    'sisa'              => $sisa
                    
                ];
            }

            $result[] = [
                'partner_id'     => $row->partner_id,
                'nama_partner'   => $row->nama_partner,
                'tmp_data_items' => $items
            ];
        }

        return $result;
    }
}
